<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Mail\ScanlinkMail;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Services\LabelOrderService;
use App\Support\SystemNotifier;
use App\Services\ProfileQrService;
use App\Support\LegacyEquipmentTypeLabels;
use App\Support\PricingSettings;
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

    /**
     * Legacy 2-step flow: 'form' = quantity entry, 'summary' = invoice confirmation.
     */
    public string $step = 'form';

    /**
     * Legacy blocks the order form for expired (non-free / free+renewal-required)
     * profiles and shows a message in place of it (dashboard.php:922-924).
     */
    public bool $profileExpired = false;

    public bool $agreeTerms = false;

    /**
     * Legacy on-screen postage stays 0 (controller $postage = 0; the 8.95 hidden
     * field is dead code the JS never reads).
     */
    public float $postage = 0.0;

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
        $this->priceSmall = PricingSettings::labelSmall();
        $this->priceLarge = PricingSettings::labelLarge();

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

    /**
     * Legacy step 1 → step 2: NEXT stores quantities and shows the order summary.
     * Legacy requires at least one non-zero quantity ("Select quantity.").
     */
    public function goToSummary(): void
    {
        if (! $this->selectedProfileId) {
            Notification::make()->title('Please select a profile.')->danger()->send();

            return;
        }

        if ($this->profileExpired) {
            Notification::make()->title('You can not perform this action on expired profile.')->danger()->send();

            return;
        }

        $qtySmall = (int) ($this->qtySmall ?: 0);
        $qtyLarge = (int) ($this->qtyLarge ?: 0);

        if ($qtySmall < 1 && $qtyLarge < 1) {
            Notification::make()->title('Select quantity.')->danger()->send();

            return;
        }

        $this->agreeTerms = false;
        $this->step = 'summary';
    }

    public function backToForm(): void
    {
        $this->step = 'form';
    }

    /**
     * Legacy step 2 (ordersummary POST): requires the terms checkbox, then places the
     * temp order, emails client + admin, and returns to the master code list.
     */
    public function placeOrder(LabelOrderService $labelOrders)
    {
        if (! $this->selectedProfileId) {
            Notification::make()->title('Please select a profile.')->danger()->send();

            return null;
        }

        if (! $this->agreeTerms) {
            Notification::make()->title('Please check the terms and conditions.')->danger()->send();

            return null;
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

            return null;
        }

        // Legacy: cannot order labels for an expired profile (dashboard.php:922-924).
        if ($profile->isExpired()) {
            $this->profileExpired = true;

            Notification::make()
                ->title('You can not perform this action on expired profile.')
                ->danger()
                ->send();

            return null;
        }

        try {
            $order = $labelOrders->createLabelOrder(
                $profile,
                (int) ($this->qtySmall ?: 0),
                (int) ($this->qtyLarge ?: 0),
                $member,
            );
        } catch (ValidationException $e) {
            $this->step = 'form';

            Notification::make()
                ->title($e->validator->errors()->first() ?: 'Select quantity.')
                ->danger()
                ->send();

            return null;
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title('Could not create label order')
                ->body(config('app.debug') ? $e->getMessage() : 'Please try again or contact support.')
                ->danger()
                ->send();

            return null;
        }

        $this->dispatchOrderLabelEmails($profile, $member);

        // Legacy success popup wording (views/template/orderlabel.php).
        Notification::make()
            ->title('Your order has been successful and your ScanLink label/s will be dispatched within the next 48 hours.')
            ->success()
            ->send();

        // Legacy redirects back to the profile-type list after placing the order.
        return $this->redirect($this->returnToListUrl(), navigate: false);
    }

    /**
     * Legacy orderlabel_mail_client + orderlabel_mail_admin.
     */
    protected function dispatchOrderLabelEmails(Profile $profile, mixed $member): void
    {
        $client = $this->requireClient();
        $priceSmall = PricingSettings::labelSmall();
        $priceLarge = PricingSettings::labelLarge();
        $postage = PricingSettings::labelPostage();
        $qtySmall = (int) ($this->qtySmall ?: 0);
        $qtyLarge = (int) ($this->qtyLarge ?: 0);
        $itemsTotal = ($priceSmall * $qtySmall) + ($priceLarge * $qtyLarge);

        $data = [
            'profileId' => (int) $profile->id,
            'email' => strtolower(trim((string) ($member?->email ?: $client->email))),
            'firstName' => (string) ($member?->first_name ?? ''),
            'lastName' => (string) ($member?->last_name ?? ''),
            'qtySmall' => $qtySmall,
            'qtyLarge' => $qtyLarge,
            'amountSmall' => number_format($priceSmall * $qtySmall, 2),
            'amountLarge' => number_format($priceLarge * $qtyLarge, 2),
            'postage' => number_format($postage, 2),
            'total' => number_format($itemsTotal + $postage, 2),
        ];

        $this->trySendMail($data['email'], new ScanlinkMail('ScanLink order confirmation', 'emails.order-label-client', $data));
        $this->trySendMail((string) config('scanlink.admin_email'), new ScanlinkMail('Scanlink Order Label', 'emails.order-label-admin', $data));

        SystemNotifier::toMember(
            $member instanceof ClientUser ? $member : $this->currentClientUser(),
            'Label order placed',
            'Your label order for code #'.$profile->id.' has been placed (total $'.$data['total'].').',
            'heroicon-o-tag',
            'success',
        );

        SystemNotifier::toAdmins(
            'New label order',
            trim($data['firstName'].' '.$data['lastName']).' placed a label order for code #'.$profile->id.'.',
            'heroicon-o-tag',
            'success',
        );
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
        // Postage & Handling is admin-managed (Label & Form Pricing master).
        $postage = PricingSettings::labelPostage();

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
        $this->profileExpired = $profile->isExpired();
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
