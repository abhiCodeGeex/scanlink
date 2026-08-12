<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\Profile;
use App\Services\FormBuilderPurchaseService;
use App\Support\PricingSettings;
use BackedEnum;
use DomainException;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class FormBuilderOrderSummary extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Form Builder Order Summary';

    protected static ?string $title = 'Form Builder Order Summary';

    protected static ?string $slug = 'form-builder-order-summary';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.portal.pages.form-builder-order-summary';

    public bool $agreeTerms = false;

    public function mount(): void
    {
        if (! $this->hasCheckout()) {
            $this->redirect(PurchaseFormBuilder::getUrl(panel: 'portal'), navigate: false);
            return;
        }

        $profile = $this->profile();

        if ((bool) $profile->form_active) {
            $this->clearCheckout();
            Notification::make()
                ->title('Form Builder is already activated for this profile.')
                ->warning()
                ->send();
            $this->redirect(ProfileResource::getUrl('edit', ['record' => $profile], panel: 'portal'), navigate: false);
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

    public function totalAmount(): float
    {
        return PricingSettings::formBuilder();
    }

    public function profile(): Profile
    {
        $profileId = (int) (session(PurchaseFormBuilder::SESSION_CHECKOUT)['profile_id'] ?? 0);

        return Profile::query()
            ->where('client_id', $this->requireClient()->id)
            ->active()
            ->findOrFail($profileId);
    }

    public function proceed(FormBuilderPurchaseService $purchases): void
    {
        if (! $this->agreeTerms) {
            Notification::make()->title('Please check the terms and condition.')->danger()->send();
            return;
        }

        if (! $this->hasCheckout()) {
            Notification::make()->title('Checkout session expired. Please start again.')->danger()->send();
            $this->redirect(PurchaseFormBuilder::getUrl(panel: 'portal'), navigate: false);
            return;
        }

        $checkout = session(PurchaseFormBuilder::SESSION_CHECKOUT, []);
        $billing = session(PurchaseFormBuilder::SESSION_BILLING, []);
        $profile = $this->profile();

        try {
            $order = $purchases->purchase(
                $profile,
                $this->requireClient(),
                $this->requireClientUser(),
                $billing,
                $checkout,
            );
        } catch (DomainException $exception) {
            Notification::make()->title($exception->getMessage())->warning()->send();
            $this->clearCheckout();
            $this->redirect(ProfileResource::getUrl('edit', ['record' => $profile], panel: 'portal'), navigate: false);
            return;
        } catch (\Throwable $exception) {
            report($exception);
            Notification::make()
                ->title('Could not complete Form Builder order')
                ->body(config('app.debug') ? $exception->getMessage() : 'Please try again or contact support.')
                ->danger()
                ->send();
            return;
        }

        $returnUrl = ProfileResource::getUrl('edit', ['record' => $profile], panel: 'portal');
        $this->clearCheckout();

        Notification::make()
            ->title('Form Builder activated')
            ->body("Order #{$order->id} has been created. A tax invoice will be mailed to you.")
            ->success()
            ->send();

        $this->redirect($returnUrl, navigate: false);
    }

    protected function hasCheckout(): bool
    {
        $checkout = session(PurchaseFormBuilder::SESSION_CHECKOUT);
        $billing = session(PurchaseFormBuilder::SESSION_BILLING);

        return is_array($checkout)
            && (int) ($checkout['profile_id'] ?? 0) > 0
            && is_array($billing)
            && (int) ($billing['client_id'] ?? 0) === (int) $this->requireClient()->id;
    }

    protected function clearCheckout(): void
    {
        session()->forget([
            PurchaseFormBuilder::SESSION_CHECKOUT,
            PurchaseFormBuilder::SESSION_BILLING,
        ]);
    }
}
