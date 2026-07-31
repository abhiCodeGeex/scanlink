<?php

namespace App\Filament\Portal\Pages;

use App\Enums\CodeOrderStatus;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\CodePurchaseDetail;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Support\SystemNotifier;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderSummary extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Purchase Order Summary';

    protected static ?string $title = 'Purchase Order Summary';

    protected static ?string $slug = 'purchase-order-summary';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.portal.pages.purchase-order-summary';

    public bool $agreeTerms = false;

    public function mount(): void
    {
        if (! $this->hasCheckout()) {
            $this->redirect(PurchaseCodes::getUrl(panel: 'portal'), navigate: false);
        }

        if (! $this->hasBilling()) {
            $this->redirect(PurchaseBilling::getUrl(panel: 'portal'), navigate: false);
        }
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function quantity(): int
    {
        return (int) (session(PurchaseCodes::SESSION_CHECKOUT)['quantity'] ?? 0);
    }

    public function perCodeAmount(): float
    {
        return (float) (session(PurchaseCodes::SESSION_CHECKOUT)['per_code_amount'] ?? 0);
    }

    public function totalAmount(): float
    {
        return round($this->quantity() * $this->perCodeAmount() * 12, 2);
    }

    public function proceed(): void
    {
        if (! $this->agreeTerms) {
            Notification::make()->title('Please check the terms and condtion.')->danger()->send();

            return;
        }

        $checkout = session(PurchaseCodes::SESSION_CHECKOUT, []);
        $billing = session(PurchaseCodes::SESSION_BILLING, []);
        $qty = (int) ($checkout['quantity'] ?? 0);
        $perCode = (float) ($checkout['per_code_amount'] ?? 0);

        if ($qty <= 0 || $perCode <= 0 || $billing === []) {
            Notification::make()->title('Checkout session expired. Please start again.')->danger()->send();
            $this->redirect(PurchaseCodes::getUrl(panel: 'portal'), navigate: false);

            return;
        }

        $client = $this->requireClient();
        $member = $this->requireClientUser();

        try {
            $order = DB::transaction(function () use ($client, $member, $checkout, $billing, $qty, $perCode) {
            $order = $client->codePurchases()->create([
                'email' => (string) ($billing['email'] ?? $member->email ?? $client->email),
                'first_name' => (string) ($billing['first_name'] ?? ''),
                'last_name' => (string) ($billing['last_name'] ?? ''),
                'company_name' => (string) ($billing['company_name'] ?? ''),
                'billing_address' => (string) ($billing['billing_address'] ?? ''),
                'town' => (string) ($billing['town'] ?? ''),
                'phone' => (string) ($billing['phone'] ?? ''),
                'postal_code' => (string) ($billing['postal_code'] ?? ''),
                'no_of_codes' => $qty,
                'per_code_amount' => $perCode,
                'total_amount' => round($qty * $perCode * 12, 2),
                'status' => CodeOrderStatus::New,
                'enable' => false,
                'is_reseller_pricing_code' => (string) ($checkout['is_reseller_pricing_code'] ?? '0'),
                'reseller_client_id' => (int) ($checkout['reseller_client_id'] ?? 0),
                'free_code' => false,
                'ordered_on' => now(),
            ]);

            $codeTypeId = (int) (EquipmentType::query()->where('slag', 'code')->value('id') ?? 0);
            if ($codeTypeId <= 0) {
                $codeTypeId = (int) (EquipmentType::query()->orderBy('id')->value('id') ?? 1);
            }

            for ($i = 0; $i < $qty; $i++) {
                $profile = Profile::query()->create([
                    'client_id' => $client->id,
                    'user_id' => $member->id,
                    'code_purchase_id' => $order->id,
                    'type_id' => $codeTypeId,
                    'expired_at' => now()->addYear(),
                    'update_or_not' => false,
                    'is_reseller_code' => (string) ($checkout['is_reseller_pricing_code'] ?? '0'),
                    'reseller_client_id' => (int) ($checkout['reseller_client_id'] ?? 0),
                    'free_code' => false,
                ]);

                CodePurchaseDetail::query()->create([
                    'code_purchase_id' => $order->id,
                    'profile_id' => (int) $profile->id,
                    // Live table has NOT NULL amount.
                    'amount' => $perCode,
                ]);
            }

            return $order;
            });
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Could not complete order')
                ->body(config('app.debug') ? $e->getMessage() : 'Please try again or contact support.')
                ->danger()
                ->send();

            return;
        }

        $this->dispatchPurchaseEmails($order, $billing, $checkout, $qty, $perCode);

        session()->forget(PurchaseCodes::SESSION_CHECKOUT);
        session()->forget(PurchaseCodes::SESSION_BILLING);

        Notification::make()
            ->title('Code purchase submitted')
            ->body("Order #{$order->id} has been created. Invoice email sent.")
            ->success()
            ->send();

        $this->redirect(PurchaseCodes::getUrl(panel: 'portal'), navigate: false);
    }

    /**
     * Legacy purchaseCode_mail_client / purchaseCode_mail_admin (+ reseller variants).
     *
     * @param  array<string, mixed>  $billing
     * @param  array<string, mixed>  $checkout
     */
    protected function dispatchPurchaseEmails(mixed $order, array $billing, array $checkout, int $qty, float $perCode): void
    {
        $adminEmail = (string) config('scanlink.admin_email');
        $total = number_format($qty * $perCode * 12, 2);
        $perCodeFmt = '$'.number_format($perCode, 2).' AUD';
        $buyerEmail = strtolower(trim((string) ($billing['email'] ?? '')));
        $firstName = (string) ($billing['first_name'] ?? '');
        $lastName = (string) ($billing['last_name'] ?? '');

        // Legacy purchaseCode contact line: support@scanlink.net.au / 0417557640.
        $supportEmail = 'support@scanlink.net.au';
        $supportPhone = '0417557640';

        $isReseller = (string) ($checkout['is_reseller_pricing_code'] ?? '0') === '1';
        $resellerId = (int) ($checkout['reseller_client_id'] ?? 0);
        $reseller = ($isReseller && $resellerId > 0) ? Client::query()->find($resellerId) : null;

        // Buyer confirmation.
        $this->trySendMail($buyerEmail, new ScanlinkMail('ScanLink order confirmation', 'emails.order-confirmation', [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'noOfCodes' => $qty,
            'total' => $total,
            'isReseller' => false,
            'userEmail' => $buyerEmail,
            'supportEmail' => $supportEmail,
            'supportPhone' => $supportPhone,
        ]));

        // Bell notification for the buyer (same person the confirmation email targets).
        SystemNotifier::toMember(
            $this->currentClientUser(),
            'Order confirmed',
            "Your purchase of {$qty} code(s) is confirmed — total \${$total} AUD.",
            'heroicon-o-shopping-cart',
            'success',
        );

        // Admin notification.
        $this->trySendMail($adminEmail, new ScanlinkMail('Scanlink Purchase Code', 'emails.admin-code', [
            'title' => 'Scanlink Purchase Code',
            'verb' => 'purchased',
            'email' => $buyerEmail,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'noOfCodes' => $qty,
            'amountPerCode' => $perCodeFmt,
            'total' => $total,
            'resellerName' => (string) ($reseller?->client_name ?? ''),
        ]));

        SystemNotifier::toAdmins(
            'New code purchase',
            trim($firstName.' '.$lastName).' purchased '.$qty.' code(s) — total $'.$total.' AUD.',
            'heroicon-o-shopping-cart',
            'success',
        );

        // When a reseller code was used, notify the reseller + admin.
        if ($reseller) {
            $resellerEmail = strtolower(trim((string) $reseller->reseller_email));
            [$rFirst, $rLast] = $this->splitName((string) $reseller->client_name);

            $this->trySendMail($resellerEmail, new ScanlinkMail('ScanLink order confirmation', 'emails.order-confirmation', [
                'firstName' => (string) $reseller->client_name,
                'lastName' => '',
                'noOfCodes' => $qty,
                'total' => $total,
                'isReseller' => true,
                'userEmail' => $buyerEmail,
                'supportEmail' => $supportEmail,
                'supportPhone' => $supportPhone,
            ]));

            $this->trySendMail($adminEmail, new ScanlinkMail('Scanlink Purchase Code', 'emails.admin-code', [
                'title' => 'Scanlink Purchase Code',
                'verb' => 'purchased',
                'email' => $resellerEmail,
                'firstName' => $rFirst,
                'lastName' => $rLast,
                'noOfCodes' => $qty,
                'amountPerCode' => $perCodeFmt,
                'total' => $total,
                'resellerName' => '',
            ]));

            // Notify the reseller account owner that their code was used.
            SystemNotifier::toClientPrimary(
                $reseller,
                'Your reseller code was used',
                trim($firstName.' '.$lastName).' purchased '.$qty.' code(s) using your reseller code.',
                'heroicon-o-ticket',
                'info',
            );
        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function splitName(string $name): array
    {
        $name = trim($name);

        if ($name === '') {
            return ['', ''];
        }

        $parts = explode(' ', $name, 2);

        return [$parts[0], $parts[1] ?? ''];
    }

    protected function trySendMail(string $email, ScanlinkMail $mail): void
    {
        $email = strtolower(trim($email));

        if ($email === '' || ! str_contains($email, '@')) {
            return;
        }

        try {
            Mail::to($email)->send($mail);
        } catch (\Throwable $e) {
            // Keep purchase flow successful even if outbound mail is unavailable.
            report($e);
        }
    }

    protected function hasCheckout(): bool
    {
        return is_array(session(PurchaseCodes::SESSION_CHECKOUT))
            && (int) (session(PurchaseCodes::SESSION_CHECKOUT)['quantity'] ?? 0) > 0;
    }

    protected function hasBilling(): bool
    {
        return is_array(session(PurchaseCodes::SESSION_BILLING))
            && filled(session(PurchaseCodes::SESSION_BILLING)['email'] ?? null);
    }
}

