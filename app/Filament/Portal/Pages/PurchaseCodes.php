<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Models\Client;
use App\Models\CodePrising;
use App\Models\Profile;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class PurchaseCodes extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Purchase codes';

    protected static ?string $title = 'Purchase codes';

    protected static ?string $slug = 'purchase-codes';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.portal.pages.purchase-codes';

    public string $activeTab = 'purchase';

    public string $purchaseQuantity = '';

    public string $resellerCode = '';

    public string $resellerQuantity = '';

    public string $purchaseAmount = '0.00';

    public string $purchaseTotalAnnual = '0.00';

    public string $resellerAmount = '0.00';

    public string $resellerTotalAnnual = '0.00';

    public string $resellerMargin = '0.00';

    public bool $purchaseCalculated = false;

    public bool $resellerCalculated = false;

    public const SESSION_CHECKOUT = 'portal.purchase.checkout';

    public const SESSION_BILLING = 'portal.purchase.billing';

    public static function getNavigationGroup(): ?string
    {
        return 'My Account';
    }

    public function mount(): void
    {
        $member = $this->currentClientUser();
        $this->resellerCode = (string) ($member?->client_reseller_code ?? '');
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function switchTab(string $tab): void
    {
        if (in_array($tab, ['purchase', 'reseller'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function calculatePurchase(): void
    {
        [$qty, $tier] = $this->resolveQuantityAndTier($this->purchaseQuantity, false, false);
        if (! $tier) {
            return;
        }

        $perCode = (float) $tier->amount;
        $this->purchaseAmount = number_format($perCode, 2, '.', '');
        $this->purchaseTotalAnnual = number_format($perCode * $qty * 12, 2, '.', '');
        $this->purchaseCalculated = true;
    }

    public function calculateReseller(): void
    {
        [$qty, $tier, $resellerClientId] = $this->resolveResellerInputs();
        if (! $tier || $resellerClientId <= 0) {
            return;
        }
        unset($resellerClientId);

        $amount = (float) $tier->amount;
        $resellerAmount = (float) $tier->reseller_amount;

        $this->resellerAmount = number_format($resellerAmount, 2, '.', '');
        $this->resellerTotalAnnual = number_format($resellerAmount * $qty * 12, 2, '.', '');
        $this->resellerMargin = number_format(($amount - $resellerAmount) * $qty * 12, 2, '.', '');
        $this->resellerCalculated = true;
    }

    public function submitPurchase(): void
    {
        [$qty, $tier] = $this->resolveQuantityAndTier($this->purchaseQuantity, true, false);
        if (! $tier) {
            return;
        }

        session([
            self::SESSION_CHECKOUT => [
                'quantity' => $qty,
                'per_code_amount' => (float) $tier->amount,
                'is_reseller_pricing_code' => '0',
                'reseller_client_id' => 0,
            ],
        ]);

        $this->redirect(PurchaseBilling::getUrl(panel: 'portal'), navigate: false);
    }

    public function submitReseller(): void
    {
        [$qty, $tier, $resellerClientId] = $this->resolveResellerInputs();
        if (! $tier || $resellerClientId <= 0) {
            return;
        }

        session([
            self::SESSION_CHECKOUT => [
                'quantity' => $qty,
                'per_code_amount' => (float) $tier->reseller_amount,
                'is_reseller_pricing_code' => '1',
                'reseller_client_id' => $resellerClientId,
            ],
        ]);

        $this->redirect(PurchaseBilling::getUrl(panel: 'portal'), navigate: false);
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function availabilityBalance(): int
    {
        $client = $this->currentClient();
        if (! $client) {
            return 0;
        }

        return Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->openSlot()
            ->where(function ($query): void {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            })
            ->count();
    }

    public function exitPage(): void
    {
        $this->redirect(CodeBalance::getUrl(panel: 'portal'), navigate: false);
    }

    /**
     * @return array{0:int, 1:CodePrising|null}
     */
    protected function resolveQuantityAndTier(string $quantityRaw, bool $forSubmit, bool $reseller): array
    {
        $quantityRaw = trim($quantityRaw);
        if ($quantityRaw === '') {
            Notification::make()->title('Enter a number of code required.')->danger()->send();

            return [0, null];
        }

        $qty = (int) $quantityRaw;
        if ($qty <= 0 || $qty > 1000) {
            Notification::make()->title('Enter a number of code less than 1000.')->danger()->send();

            return [0, null];
        }

        $tier = CodePrising::query()
            ->where('code_min_qty', '<=', $qty)
            ->where('code_max_qty', '>=', $qty)
            ->orderBy('id')
            ->first();

        if (! $tier) {
            Notification::make()->title('No pricing rule matched this code quantity.')->danger()->send();

            return [0, null];
        }

        if ($forSubmit) {
            // Match legacy flow where calculate is effectively re-run on submit.
            if ($reseller) {
                $this->resellerAmount = number_format((float) $tier->reseller_amount, 2, '.', '');
                $this->resellerTotalAnnual = number_format((float) $tier->reseller_amount * $qty * 12, 2, '.', '');
                $this->resellerMargin = number_format(((float) $tier->amount - (float) $tier->reseller_amount) * $qty * 12, 2, '.', '');
            } else {
                $this->purchaseAmount = number_format((float) $tier->amount, 2, '.', '');
                $this->purchaseTotalAnnual = number_format((float) $tier->amount * $qty * 12, 2, '.', '');
            }
        }

        return [$qty, $tier];
    }

    /**
     * @return array{0:int, 1:CodePrising|null, 2:int}
     */
    protected function resolveResellerInputs(): array
    {
        $code = trim($this->resellerCode);
        if ($code === '') {
            Notification::make()->title('Enter a reseller code.')->danger()->send();

            return [0, null, 0];
        }

        $resellerClientId = (int) (Client::findByResellerCode($code)?->id ?? 0);

        if ($resellerClientId <= 0) {
            Notification::make()->title('Enter a valid active reseller code.')->danger()->send();

            return [0, null, 0];
        }

        [$qty, $tier] = $this->resolveQuantityAndTier($this->resellerQuantity, false, true);

        return [$qty, $tier, $resellerClientId];
    }

    public function updatedPurchaseQuantity(): void
    {
        $this->purchaseCalculated = false;
    }

    public function updatedResellerQuantity(): void
    {
        $this->resellerCalculated = false;
    }

    public function updatedResellerCode(): void
    {
        $this->resellerCalculated = false;
    }
}
