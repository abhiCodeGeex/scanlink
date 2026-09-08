<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Mail\ScanlinkMail;
use App\Support\SystemNotifier;
use App\Support\TransactionalMailer;
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
use Illuminate\Support\Str;

/**
 * Legacy /code/renewCode → /code/renewableCode order summary.
 */
class RenewCodeSummary extends Page
{
    use InteractsWithClientMembership;

    public const SESSION_RENEW = 'portal_renew_checkout';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Renew Order Summary';

    protected static ?string $title = 'Order Summary';

    protected static ?string $slug = 'renew-order-summary';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.portal.pages.renew-order-summary';

    public bool $agreeTerms = false;

    /**
     * Identifies the staged quote this page was opened for.
     *
     * Held as a property, not re-read from the query string, because a Livewire update posts
     * to its own endpoint and carries no query string of its own.
     */
    public string $token = '';

    /**
     * Legacy mastercode renew is available to primary and account sub-users.
     * (Multiple Code Renewal menu remains primary-only.)
     */
    public static function canAccess(): bool
    {
        return static::portalMembership() !== null;
    }

    /**
     * Stage selected profiles into session and return the summary URL.
     *
     * @param  iterable<int, Profile|int>  $profiles
     */
    public static function stageRenew(iterable $profiles, ?string $returnUrl = null): string
    {
        $service = app(CodeProfileRenewalService::class);
        $quote = $service->quote($profiles);

        // Every staging gets its own token, carried in the URL. The summary only renders a
        // quote whose token matches the one it was opened with, so a quote left in the
        // session by an earlier selection can never be presented — or charged for — as if
        // it were this one.
        $token = Str::random(40);

        session([
            self::SESSION_RENEW => [
                ...$quote,
                'return_url' => $returnUrl,
                'token' => $token,
            ],
        ]);

        return static::getUrl(['t' => $token], panel: 'portal');
    }

    public function mount(): void
    {
        $this->token = (string) request()->query('t', '');

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
     * The profile numbers this quote covers.
     *
     * The summary used to show only a count and a total, so a selection that was not what the
     * user intended looked identical to one that was — and pressing proceed raises an invoice.
     *
     * @return list<int>
     */
    public function profileNumbers(): array
    {
        return array_values(array_map(
            'intval',
            session(self::SESSION_RENEW)['profile_ids'] ?? [],
        ));
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

        // Re-check the token here too: the quote could have been replaced by a staging in
        // another tab between opening this page and pressing proceed.
        if (! $this->hasRenewSession()) {
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

        // Exactly one admin notification per renewal, whatever mix of own and reseller codes
        // it contains. It used to be sent once for the member's own codes and again for every
        // reseller group, so a renewal spanning three resellers put four near-identical
        // "Scanlink Renew Code" messages in the admin inbox for a single order.
        $this->trySend($adminEmail, new ScanlinkMail('Scanlink Renew Code', 'emails.admin-code', [
            'title' => 'Scanlink Renew Code',
            'verb' => 'renewed',
            'email' => $memberEmail,
            'firstName' => (string) ($member->first_name ?? ''),
            'lastName' => (string) ($member->last_name ?? ''),
            'noOfCodes' => $ordered->count(),
            'amountPerCode' => $amountPerCode,
            'total' => $total,
            'resellerName' => (string) ($client->resellerName() ?? ''),
        ]));

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

            SystemNotifier::toMember(
                $member,
                'Codes renewed',
                "{$ownCount} code(s) renewed — total \${$total} AUD.",
                'heroicon-o-arrow-path',
                'success',
            );

            SystemNotifier::toAdmins(
                'Codes renewed',
                trim(($member->first_name ?? '').' '.($member->last_name ?? '')).' renewed '.$ownCount.' code(s) — total $'.$total.' AUD.',
                'heroicon-o-arrow-path',
                'success',
            );
        }

        // Reseller groups.
        foreach ($resellerGroups as $resellerId => $group) {
            $reseller = Client::query()->find($resellerId);

            if (! $reseller) {
                continue;
            }

            $resellerEmail = strtolower(trim((string) $reseller->reseller_email));
            $resellerName = (string) $reseller->client_name;
            $count = $group->count();

            // A member renewing codes bought with their own reseller code is one person, and
            // already had the confirmation above.
            if ($resellerEmail !== $memberEmail) {
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
            }

            // The admin notification for this renewal was already sent once, above.

            SystemNotifier::toClientPrimary(
                $reseller,
                'Reseller codes renewed',
                $count.' code(s) using your reseller code were renewed.',
                'heroicon-o-arrow-path',
                'info',
            );

            SystemNotifier::toAdmins(
                'Reseller codes renewed',
                $resellerName.' — '.$count.' reseller code(s) renewed.',
                'heroicon-o-arrow-path',
                'info',
            );
        }
    }

    protected function profileIsReseller(Profile $profile): bool
    {
        return (bool) $profile->is_reseller_code && (int) $profile->reseller_client_id > 0;
    }


    protected function trySend(string $email, ScanlinkMail $mail): void
    {
        // Deduplicates per request and swallows outbound-mail failures, so the renewal still
        // completes if the mail server is down.
        app(TransactionalMailer::class)->sendOnce($email, $mail);
    }

    protected function hasRenewSession(): bool
    {
        $renew = session(self::SESSION_RENEW);

        if (! is_array($renew)
            || (int) ($renew['quantity'] ?? 0) <= 0
            || empty($renew['profile_ids'])) {
            return false;
        }

        // The quote must be the one this page was opened for. Without this any leftover
        // quote satisfied the guard, which is how a superseded selection could be renewed.
        $staged = (string) ($renew['token'] ?? '');

        return $staged !== ''
            && $this->token !== ''
            && hash_equals($staged, $this->token);
    }
}
