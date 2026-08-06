<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Models\Client;
use App\Models\Profile;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class PurchaseFormBuilder extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $navigationLabel = 'Buy Form Builder';

    protected static ?string $title = 'Purchase Form Builder';

    protected static ?string $slug = 'purchase-form-builder';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.portal.pages.purchase-form-builder';

    public const SESSION_CHECKOUT = 'form_builder_purchase.checkout';

    public const SESSION_BILLING = 'form_builder_purchase.billing';

    public ?int $profileId = null;

    public string $firstName = '';

    public string $lastName = '';

    public string $companyName = '';

    public string $billingAddress = '';

    public string $email = '';

    public string $town = '';

    public string $phone = '';

    public string $postalCode = '';

    public function mount(): void
    {
        $client = $this->requireClient();
        $member = $this->requireClientUser();
        $requestedProfileId = (int) request()->query('profile', 0);
        $savedCheckout = session(self::SESSION_CHECKOUT, []);
        $savedBilling = session(self::SESSION_BILLING, []);

        $this->profileId = $requestedProfileId > 0
            ? $requestedProfileId
            : ((int) ($savedCheckout['profile_id'] ?? 0) ?: null);

        if ($this->profileId !== null) {
            $profile = $this->resolveProfile($this->profileId);

            if ((bool) $profile->form_active) {
                Notification::make()
                    ->title('Form Builder is already activated for this profile.')
                    ->warning()
                    ->send();

                $this->redirect($this->profileEditUrl($profile), navigate: false);

                return;
            }
        }

        $this->firstName = (string) ($savedBilling['first_name'] ?? $member->first_name ?? '');
        $this->lastName = (string) ($savedBilling['last_name'] ?? $member->last_name ?? '');
        $this->companyName = (string) ($savedBilling['company_name'] ?? $member->company_name ?? $client->client_name ?? '');
        $this->billingAddress = (string) ($savedBilling['billing_address'] ?? $member->billing_address ?? $client->address ?? '');
        $this->email = (string) ($savedBilling['email'] ?? $member->email ?? $client->email ?? '');
        $this->town = (string) ($savedBilling['town'] ?? $member->town ?? '');
        $this->phone = (string) ($savedBilling['phone'] ?? $member->phone ?? $client->telephone ?? '');
        $this->postalCode = (string) ($savedBilling['postal_code'] ?? $member->postal_code ?? '');
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    /**
     * @return array<int, string>
     */
    public function profileOptions(): array
    {
        $client = $this->currentClient();

        if (! $client) {
            return [];
        }

        return Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->claimedSlot()
            ->where('form_active', false)
            ->orderBy('name')
            ->get()
            ->reject(fn (Profile $profile): bool => $profile->isExpired())
            ->mapWithKeys(fn (Profile $profile): array => [
                (int) $profile->id => $profile->displayLabel(),
            ])
            ->all();
    }

    public function next(): void
    {
        if (! $this->profileId) {
            Notification::make()->title('Please select a profile.')->danger()->send();
            return;
        }

        $required = [
            'firstName' => 'Enter a first name.',
            'lastName' => 'Enter a last name.',
            'companyName' => 'Enter a company name.',
            'billingAddress' => 'Enter a billing address.',
            'email' => 'Enter an email.',
            'town' => 'Enter a town.',
            'phone' => 'Enter a phone.',
            'postalCode' => 'Enter a postal code.',
        ];

        foreach ($required as $property => $message) {
            if (trim((string) $this->{$property}) === '') {
                Notification::make()->title($message)->danger()->send();
                return;
            }
        }

        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            Notification::make()->title('Enter a valid email.')->danger()->send();
            return;
        }

        $profile = $this->resolveProfile($this->profileId);

        if ((bool) $profile->form_active) {
            Notification::make()->title('Form Builder is already activated for this profile.')->warning()->send();
            $this->redirect($this->profileEditUrl($profile), navigate: false);
            return;
        }

        $client = $this->requireClient();
        $member = $this->requireClientUser();
        $reseller = Client::findByResellerCode((string) $member->client_reseller_code);

        session([
            self::SESSION_CHECKOUT => [
                'profile_id' => (int) $profile->id,
                'per_code_amount' => 5.00,
                'quantity' => 1,
                'return_url' => $this->profileEditUrl($profile),
                'is_reseller_pricing_code' => $reseller ? '1' : '0',
                'reseller_client_id' => (int) ($reseller?->id ?? 0),
            ],
            self::SESSION_BILLING => [
                'first_name' => trim($this->firstName),
                'last_name' => trim($this->lastName),
                'company_name' => trim($this->companyName),
                'billing_address' => trim($this->billingAddress),
                'email' => strtolower(trim($this->email)),
                'town' => trim($this->town),
                'phone' => trim($this->phone),
                'postal_code' => trim($this->postalCode),
                'client_id' => (int) $client->id,
            ],
        ]);

        $this->redirect(FormBuilderOrderSummary::getUrl(panel: 'portal'), navigate: false);
    }

    protected function resolveProfile(int $profileId): Profile
    {
        return Profile::query()
            ->where('client_id', $this->requireClient()->id)
            ->active()
            ->claimedSlot()
            ->findOrFail($profileId);
    }

    protected function profileEditUrl(Profile $profile): string
    {
        return \App\Filament\Portal\Resources\Profiles\ProfileResource::getUrl(
            'edit',
            ['record' => $profile],
            panel: 'portal',
        );
    }
}
