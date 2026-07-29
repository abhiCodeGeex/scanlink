<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Mail\ScanlinkMail;
use App\Models\Profile;
use App\Services\LabelOrderService;
use App\Services\ProfileQrService;
use App\Support\LegacyEquipmentTypeLabels;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OrderLabel extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Order Labels';

    protected static ?string $title = 'Order Label';

    protected static ?string $slug = 'order-labels';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.portal.pages.order-label';

    public ?int $selectedProfileId = null;

    public ?string $profileName = null;

    public ?string $typeName = null;

    public ?string $qrUrl = null;

    public int|string $qtySmall = '';

    public int|string $qtyLarge = '';

    public float $priceSmall = 3.0;

    public float $priceLarge = 5.0;

    public static function getNavigationGroup(): ?string
    {
        return 'Orders';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Order Label';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getHeader(): ?View
    {
        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => LegacyEquipmentTypeLabels::navTypes(),
            'activeTab' => null,
            'addCodeUrl' => ProfileResource::getUrl('index'),
            'canAddCode' => false,
            'hideActionBar' => true,
            'hideLegend' => true,
            'readonlyNav' => true,
        ]);
    }

    public static function canAccess(): bool
    {
        return static::memberCanOrderLabel(static::portalMembership());
    }

    public function mount(ProfileQrService $qr): void
    {
        $this->priceSmall = (float) config('scanlink.label_price_small', 3);
        $this->priceLarge = (float) config('scanlink.label_price_large', 5);

        $requestedProfile = request()->integer('profile');

        if ($requestedProfile > 0) {
            $this->loadProfile($requestedProfile, $qr);
        }
    }

    public function updatedQtySmall(mixed $value): void
    {
        $this->qtySmall = $this->normalizeQty($value);
    }

    public function updatedQtyLarge(mixed $value): void
    {
        $this->qtyLarge = $this->normalizeQty($value);
    }

    public function submitOrder(LabelOrderService $labelOrders): void
    {
        if (! $this->selectedProfileId) {
            Notification::make()->title('Please select a profile.')->danger()->send();

            return;
        }

        $client = $this->requireClient();
        $member = $this->currentClientUser();

        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->find($this->selectedProfileId);

        if (! $profile) {
            Notification::make()
                ->title('Profile not found for your account')
                ->danger()
                ->send();

            return;
        }

        try {
            $order = $labelOrders->createLabelOrder(
                $profile,
                (int) ($this->qtySmall ?: 0),
                (int) ($this->qtyLarge ?: 0),
                $member,
            );
        } catch (ValidationException $e) {
            Notification::make()
                ->title($e->validator->errors()->first() ?: 'Select quantity.')
                ->danger()
                ->send();

            return;
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title('Could not create label order')
                ->body(config('app.debug') ? $e->getMessage() : 'Please try again or contact support.')
                ->danger()
                ->send();

            return;
        }

        $this->dispatchOrderLabelEmails($profile, $member);

        Notification::make()
            ->title('Label order created')
            ->body("Order #{$order->id} has been placed. Postage and handling may apply.")
            ->success()
            ->send();

        $this->qtySmall = '';
        $this->qtyLarge = '';
    }

    /**
     * Legacy orderlabel_mail_client + orderlabel_mail_admin.
     */
    protected function dispatchOrderLabelEmails(Profile $profile, mixed $member): void
    {
        $client = $this->requireClient();
        $priceSmall = (float) config('scanlink.label_price_small', 3);
        $priceLarge = (float) config('scanlink.label_price_large', 5);
        $qtySmall = (int) ($this->qtySmall ?: 0);
        $qtyLarge = (int) ($this->qtyLarge ?: 0);

        $data = [
            'profileId' => (int) $profile->id,
            'email' => strtolower(trim((string) ($member?->email ?: $client->email))),
            'firstName' => (string) ($member?->first_name ?? ''),
            'lastName' => (string) ($member?->last_name ?? ''),
            'qtySmall' => $qtySmall,
            'qtyLarge' => $qtyLarge,
            'amountSmall' => number_format($priceSmall * $qtySmall, 2),
            'amountLarge' => number_format($priceLarge * $qtyLarge, 2),
            'total' => number_format(($priceSmall * $qtySmall) + ($priceLarge * $qtyLarge), 2),
        ];

        $this->trySendMail($data['email'], new ScanlinkMail('ScanLink order confirmation', 'emails.order-label-client', $data));
        $this->trySendMail((string) config('scanlink.admin_email'), new ScanlinkMail('Scanlink Order Label', 'emails.order-label-admin', $data));
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
            report($e);
        }
    }

    public function returnToListUrl(): string
    {
        return ProfileResource::getUrl('index');
    }

    public function contactUrl(): string
    {
        return ContactUs::getUrl();
    }

    /**
     * @return array{
     *   qty_small: int,
     *   qty_large: int,
     *   tot_small: float,
     *   tot_large: float,
     *   total: float,
     *   postage: float,
     *   grand_total: float
     * }
     */
    public function orderSummary(): array
    {
        $qtySmall = max(0, (int) ($this->qtySmall ?: 0));
        $qtyLarge = max(0, (int) ($this->qtyLarge ?: 0));
        $totSmall = $qtySmall * $this->priceSmall;
        $totLarge = $qtyLarge * $this->priceLarge;
        $total = $totSmall + $totLarge;
        // Legacy on-screen postage cell stays 0; postage applied later in checkout.
        $postage = 0.0;

        return [
            'qty_small' => $qtySmall,
            'qty_large' => $qtyLarge,
            'tot_small' => $totSmall,
            'tot_large' => $totLarge,
            'total' => $total,
            'postage' => $postage,
            'grand_total' => $total + $postage,
        ];
    }

    protected function loadProfile(int $profileId, ProfileQrService $qr): void
    {
        $client = $this->requireClient();

        $profile = Profile::query()
            ->with(['equipmentType', 'qrImage'])
            ->where('client_id', $client->id)
            ->active()
            ->find($profileId);

        if (! $profile) {
            Notification::make()
                ->title('Profile not found for your account')
                ->danger()
                ->send();

            return;
        }

        $this->selectedProfileId = $profileId;
        $this->profileName = filled(trim((string) $profile->code_profile_name))
            ? (string) $profile->code_profile_name
            : (string) ($profile->name ?? '');

        $slug = $profile->typeSlug();
        $this->typeName = $slug
            ? LegacyEquipmentTypeLabels::label($slug, (string) ($profile->equipmentType?->name ?? 'Code'))
            : (string) ($profile->equipmentType?->name ?? 'Code');

        try {
            if (! $profile->qrImage) {
                $qr->generateFor($profile);
                $profile->load('qrImage');
            } else {
                $relative = $profile->qrImage->diskPath();
                $fullPath = storage_path('app/public/'.$relative);

                if (! is_file($fullPath)) {
                    $qr->generateFor($profile);
                    $profile->load('qrImage');
                }
            }

            $this->qrUrl = $profile->qrImage?->publicUrl();
        } catch (\Throwable $e) {
            report($e);
            $this->qrUrl = null;
        }
    }

    protected function normalizeQty(mixed $value): int|string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (! is_numeric($value)) {
            return '';
        }

        $qty = (int) $value;

        return $qty > 0 ? $qty : '';
    }
}
