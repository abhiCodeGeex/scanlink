<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\Profile;
use App\Services\CodeProfileRenewalService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;

/**
 * Legacy /code/renewCode → /code/renewableCode order summary.
 */
class RenewCodeSummary extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    public const SESSION_RENEW = 'portal_renew_checkout';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Renew Order Summary';

    protected static ?string $title = 'Order Summary';

    protected static ?string $slug = 'renew-order-summary';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.portal.pages.renew-order-summary';

    public bool $agreeTerms = false;

    /**
     * Stage selected profiles into session and return the summary URL.
     *
     * @param  iterable<int, Profile|int>  $profiles
     */
    public static function stageRenew(iterable $profiles, ?string $returnUrl = null): string
    {
        $service = app(CodeProfileRenewalService::class);
        $quote = $service->quote($profiles);

        session([
            self::SESSION_RENEW => [
                ...$quote,
                'return_url' => $returnUrl,
            ],
        ]);

        return static::getUrl(panel: 'portal');
    }

    public function mount(): void
    {
        if (! $this->hasRenewSession()) {
            Notification::make()
                ->title('Please select code to be renew.')
                ->danger()
                ->send();

            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);
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
        return (int) (session(self::SESSION_RENEW)['quantity'] ?? 0);
    }

    public function totalAmount(): float
    {
        return (float) (session(self::SESSION_RENEW)['total'] ?? 0);
    }

    /**
     * Grouped lines like legacy: "1 code/s @ $30.00"
     *
     * @return list<string>
     */
    public function amountLines(): array
    {
        $amounts = session(self::SESSION_RENEW)['amounts'] ?? [];
        if (! is_array($amounts) || $amounts === []) {
            return [];
        }

        $lines = [];
        $seen = [];

        foreach ($amounts as $amount) {
            $key = number_format((float) $amount, 2, '.', '');
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $count = count(array_filter(
                $amounts,
                fn ($a): bool => number_format((float) $a, 2, '.', '') === $key
            ));
            $annual = number_format((float) $amount * 12, 2);
            $lines[] = "{$count} code/s @ \${$annual}";
        }

        return $lines;
    }

    public function proceed(): void
    {
        if (! $this->agreeTerms) {
            Notification::make()
                ->title('Please check the terms and condtion.')
                ->danger()
                ->send();

            return;
        }

        $renew = session(self::SESSION_RENEW, []);
        $profileIds = array_map('intval', $renew['profile_ids'] ?? []);

        if ($profileIds === [] || (int) ($renew['quantity'] ?? 0) <= 0) {
            Notification::make()
                ->title('Renew session expired. Please select codes again.')
                ->danger()
                ->send();
            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);

            return;
        }

        $client = $this->requireClient();
        $member = $this->requireClientUser();

        $records = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->whereIn('id', $profileIds)
            ->get();

        // Preserve quote order.
        $ordered = collect($profileIds)
            ->map(fn (int $id) => $records->firstWhere('id', $id))
            ->filter()
            ->values();

        if ($ordered->isEmpty()) {
            Notification::make()
                ->title('Please select code to be renew.')
                ->danger()
                ->send();
            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);

            return;
        }

        try {
            $order = app(CodeProfileRenewalService::class)->renew(
                $ordered,
                $client->id,
                $renew,
                $member,
            );
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Could not renew codes')
                ->body(config('app.debug') ? $e->getMessage() : 'Please try again or contact support.')
                ->danger()
                ->send();

            return;
        }

        $this->sendInvoiceEmail(
            (string) ($member->email ?: $client->email),
            $order->id,
            (int) $order->no_of_codes,
            (float) $order->total_amount,
        );

        $returnUrl = is_string($renew['return_url'] ?? null) && filled($renew['return_url'])
            ? $renew['return_url']
            : ProfileResource::getUrl('index', panel: 'portal');

        session()->forget(self::SESSION_RENEW);

        Notification::make()
            ->title('Code(s) has been renewed successfully. Our team will send invoice in 14 days.')
            ->success()
            ->send();

        $this->redirect($returnUrl, navigate: false);
    }

    protected function sendInvoiceEmail(string $email, int $orderId, int $qty, float $total): void
    {
        $email = strtolower(trim($email));
        if ($email === '' || ! str_contains($email, '@')) {
            return;
        }

        $totalFmt = number_format($total, 2);
        $lines = implode("\n", $this->amountLines());

        try {
            Mail::raw(
                "Your ScanLink renewal invoice request has been received.\n\n"
                ."Order #{$orderId}\n"
                ."Codes: {$qty}\n"
                .($lines !== '' ? "{$lines}\n" : '')
                ."Annual total (incl GST): \${$totalFmt}\n\n"
                ."Terms are 14 days.",
                fn ($message) => $message
                    ->to($email)
                    ->subject("ScanLink Renewal Invoice Order #{$orderId}")
            );
        } catch (\Throwable) {
            // Keep renew flow successful even if outbound mail is unavailable locally.
        }
    }

    protected function hasRenewSession(): bool
    {
        $renew = session(self::SESSION_RENEW);

        return is_array($renew)
            && (int) ($renew['quantity'] ?? 0) > 0
            && ! empty($renew['profile_ids']);
    }
}
