<?php

namespace App\Filament\Portal\Pages;

use App\Enums\CodeOrderStatus;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Models\CodePurchaseDetail;
use App\Models\EquipmentType;
use App\Models\Profile;
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

        $this->sendInvoiceEmail((string) ($billing['email'] ?? ''), $order->id, $qty, $perCode);

        session()->forget(PurchaseCodes::SESSION_CHECKOUT);
        session()->forget(PurchaseCodes::SESSION_BILLING);

        Notification::make()
            ->title('Code purchase submitted')
            ->body("Order #{$order->id} has been created. Invoice email sent.")
            ->success()
            ->send();

        $this->redirect(PurchaseCodes::getUrl(panel: 'portal'), navigate: false);
    }

    protected function sendInvoiceEmail(string $email, int $orderId, int $qty, float $perCode): void
    {
        $email = strtolower(trim($email));
        if ($email === '' || ! str_contains($email, '@')) {
            return;
        }

        $total = number_format($qty * $perCode * 12, 2);
        $perCodeFmt = number_format($perCode, 2);

        try {
            Mail::raw(
                "Your ScanLink invoice request has been received.\n\n"
                ."Order #{$orderId}\n"
                ."Codes: {$qty}\n"
                ."Per code / month: \${$perCodeFmt}\n"
                ."Annual total (incl GST): \${$total}\n\n"
                ."Terms are 14 days.",
                fn ($message) => $message
                    ->to($email)
                    ->subject("ScanLink Invoice Order #{$orderId}")
            );
        } catch (\Throwable) {
            // Keep purchase flow successful even if outbound mail is unavailable locally.
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

