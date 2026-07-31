<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class PurchaseBilling extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Purchase Billing';

    protected static ?string $title = 'Purchase Billing';

    protected static ?string $slug = 'purchase-billing';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.portal.pages.purchase-billing';

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
        if (! $this->hasCheckout()) {
            $this->redirect(PurchaseCodes::getUrl(panel: 'portal'), navigate: false);

            return;
        }

        $saved = session(PurchaseCodes::SESSION_BILLING, []);
        $client = $this->requireClient();
        $member = $this->requireClientUser();

        $this->firstName = (string) ($saved['first_name'] ?? $member->first_name ?? '');
        $this->lastName = (string) ($saved['last_name'] ?? $member->last_name ?? '');
        $this->companyName = (string) ($saved['company_name'] ?? $member->company_name ?? $client->client_name ?? '');
        $this->billingAddress = (string) ($saved['billing_address'] ?? $member->billing_address ?? $client->address ?? '');
        $this->email = (string) ($saved['email'] ?? $member->email ?? $client->email ?? '');
        $this->town = (string) ($saved['town'] ?? $member->town ?? '');
        $this->phone = (string) ($saved['phone'] ?? $member->phone ?? $client->telephone ?? '');
        $this->postalCode = (string) ($saved['postal_code'] ?? $member->postal_code ?? '');
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function next(): void
    {
        if (trim($this->firstName) === '') {
            Notification::make()->title('Enter a first name.')->danger()->send();

            return;
        }
        if (trim($this->lastName) === '') {
            Notification::make()->title('Enter a last name.')->danger()->send();

            return;
        }
        if (trim($this->companyName) === '') {
            Notification::make()->title('Enter a company name.')->danger()->send();

            return;
        }
        if (trim($this->billingAddress) === '') {
            Notification::make()->title('Enter a billing address.')->danger()->send();

            return;
        }
        if (trim($this->email) === '') {
            Notification::make()->title('Enter a email.')->danger()->send();

            return;
        }
        if (! preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $this->email)) {
            Notification::make()->title('Enter a valid email.')->danger()->send();

            return;
        }
        if (trim($this->town) === '') {
            Notification::make()->title('Enter a town.')->danger()->send();

            return;
        }
        if (trim($this->phone) === '') {
            Notification::make()->title('Enter a phone.')->danger()->send();

            return;
        }
        if (trim($this->postalCode) === '') {
            Notification::make()->title('Enter a postal code.')->danger()->send();

            return;
        }

        session([
            PurchaseCodes::SESSION_BILLING => [
                'first_name' => trim($this->firstName),
                'last_name' => trim($this->lastName),
                'company_name' => trim($this->companyName),
                'billing_address' => trim($this->billingAddress),
                'email' => strtolower(trim($this->email)),
                'town' => trim($this->town),
                'phone' => trim($this->phone),
                'postal_code' => trim($this->postalCode),
            ],
        ]);

        $this->redirect(PurchaseOrderSummary::getUrl(panel: 'portal'), navigate: false);
    }

    protected function hasCheckout(): bool
    {
        return is_array(session(PurchaseCodes::SESSION_CHECKOUT))
            && (int) (session(PurchaseCodes::SESSION_CHECKOUT)['quantity'] ?? 0) > 0;
    }
}

