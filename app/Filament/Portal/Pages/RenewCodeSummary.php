<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Services\CodeProfileRenewalService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
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

        $this->dispatchRenewalEmails($order, $ordered, $member, $client);

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

    /**
     * Legacy code.php::renewableCode email dispatch:
     *  - own (non-reseller) codes → client "order confirmation" + admin "Renew Code"
     *  - each reseller group → reseller-client "order confirmation" + admin "Renew Code"
     *
     * @param  Collection<int, Profile>  $ordered
     */
    protected function dispatchRenewalEmails(mixed $order, Collection $ordered, ClientUser $member, Client $client): void
    {
        $adminEmail = (string) config('scanlink.admin_email');
        // Legacy renewCode contact line: admin@scanlink.net.au / 1300 566 696.
        $supportEmail = 'admin@scanlink.net.au';
        $supportPhone = '1300 566 696';
        $total = number_format((float) $order->total_amount, 2);
        $amountPerCode = implode('<br>', $this->amountLines());
        $memberEmail = strtolower(trim((string) ($member->email ?: $client->email)));

        $ownCount = $ordered->reject(fn (Profile $p): bool => $this->profileIsReseller($p))->count();

        /** @var Collection<int, Collection<int, Profile>> $resellerGroups */
        $resellerGroups = $ordered
            ->filter(fn (Profile $p): bool => $this->profileIsReseller($p))
            ->groupBy(fn (Profile $p): int => (int) $p->reseller_client_id);

        // Own (non-reseller) codes.
        if ($ownCount > 0) {
            $this->trySend($memberEmail, new ScanlinkMail('ScanLink order confirmation', 'emails.order-confirmation', [
                'firstName' => (string) ($member->first_name ?? ''),
                'lastName' => (string) ($member->last_name ?? ''),
                'noOfCodes' => $ownCount,
                'total' => $total,
                'isReseller' => false,
                'userEmail' => $memberEmail,
                'supportEmail' => $supportEmail,
                'supportPhone' => $supportPhone,
            ]));

            $this->trySend($adminEmail, new ScanlinkMail('Scanlink Renew Code', 'emails.admin-code', [
                'title' => 'Scanlink Renew Code',
                'verb' => 'renewed',
                'email' => $memberEmail,
                'firstName' => (string) ($member->first_name ?? ''),
                'lastName' => (string) ($member->last_name ?? ''),
                'noOfCodes' => $ownCount,
                'amountPerCode' => $amountPerCode,
                'total' => $total,
                'resellerName' => (string) ($client->resellerName() ?? ''),
            ]));
        }

        // Reseller groups.
        foreach ($resellerGroups as $resellerId => $group) {
            $reseller = Client::query()->find($resellerId);

            if (! $reseller) {
                continue;
            }

            $resellerEmail = strtolower(trim((string) $reseller->reseller_email));
            $resellerName = (string) $reseller->client_name;
            [$rFirst, $rLast] = $this->splitName($resellerName);
            $count = $group->count();

            $this->trySend($resellerEmail, new ScanlinkMail('ScanLink order confirmation', 'emails.order-confirmation', [
                'firstName' => $resellerName !== '' ? $resellerName : $resellerEmail,
                'lastName' => '',
                'noOfCodes' => $count,
                'total' => $total,
                'isReseller' => true,
                'userEmail' => $memberEmail,
                'supportEmail' => $supportEmail,
                'supportPhone' => $supportPhone,
            ]));

            $this->trySend($adminEmail, new ScanlinkMail('Scanlink Renew Code', 'emails.admin-code', [
                'title' => 'Scanlink Renew Code',
                'verb' => 'renewed',
                'email' => $resellerEmail,
                'firstName' => $rFirst,
                'lastName' => $rLast,
                'noOfCodes' => $count,
                'amountPerCode' => $amountPerCode,
                'total' => $total,
                'resellerName' => '',
            ]));
        }
    }

    protected function profileIsReseller(Profile $profile): bool
    {
        return (bool) $profile->is_reseller_code && (int) $profile->reseller_client_id > 0;
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

    protected function trySend(string $email, ScanlinkMail $mail): void
    {
        $email = strtolower(trim($email));

        if ($email === '' || ! str_contains($email, '@')) {
            return;
        }

        try {
            Mail::to($email)->send($mail);
        } catch (\Throwable $e) {
            // Never fail the renewal because outbound mail is unavailable.
            report($e);
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
