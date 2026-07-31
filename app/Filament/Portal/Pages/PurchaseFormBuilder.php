<?php

namespace App\Filament\Portal\Pages;

use App\Enums\CodeOrderStatus;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\FormBuilderOrder;
use App\Models\FormBuilderOrderDetail;
use App\Models\Profile;
use App\Support\SystemNotifier;
use Illuminate\Support\Facades\Mail;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class PurchaseFormBuilder extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $navigationLabel = 'Buy Form Builder';

    protected static ?string $title = 'Purchase Form Builder';

    protected static ?string $slug = 'purchase-form-builder';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 3;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public function mount(): void
    {
        $this->form->fill([
            'profile_id' => null,
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $client = $this->currentClient();

        return $schema
            ->components([
                Section::make('Activate form builder on a profile')
                    ->schema([
                        Select::make('profile_id')
                            ->label('Profile')
                            ->options(fn (): array => $client
                                ? Profile::selectOptionsForClient((int) $client->id)->all()
                                : [])
                            ->required()
                            ->searchable(),
                    ]),
            ]);
    }

    public function submitOrder(): void
    {
        $data = $this->form->getState();
        $client = $this->requireClient();
        $member = $this->requireClientUser();

        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($data['profile_id']);

        $order = DB::transaction(function () use ($client, $member, $profile): FormBuilderOrder {
            $order = FormBuilderOrder::query()->create([
                'client_id' => $client->id,
                'email' => $member->email ?: $client->email,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'company_name' => $member->company_name ?: $client->client_name,
                'billing_address' => $member->billing_address ?: $client->address,
                'phone' => $member->phone ?: $client->telephone,
                'status' => CodeOrderStatus::New,
                'enable' => false,
                'total_amount' => 0,
                'no_of_codes' => 1,
            ]);

            FormBuilderOrderDetail::query()->create([
                'form_builder_order_id' => $order->id,
                'profile_id' => $profile->id,
            ]);

            $profile->update([
                'form_active' => true,
                'form_is_enable' => true,
            ]);

            return $order;
        });

        // Legacy client_mail_for_formbuilder + admin_mail_for_formbuilder.
        $this->dispatchFormBuilderPurchaseEmails($order, $member, $client);

        Notification::make()
            ->title('Form builder activated')
            ->body('Form builder is enabled for the selected profile. Admin will invoice as needed.')
            ->success()
            ->send();

        $this->form->fill(['profile_id' => null]);
    }

    /**
     * Legacy client_mail_for_formbuilder + admin_mail_for_formbuilder (+ bell notifications).
     */
    protected function dispatchFormBuilderPurchaseEmails(FormBuilderOrder $order, ClientUser $member, Client $client): void
    {
        $email = strtolower(trim((string) ($member->email ?: $client->email)));
        $first = (string) ($member->first_name ?? '');
        $last = (string) ($member->last_name ?? '');
        $amount = number_format((float) $order->total_amount, 2);
        $adminEmail = (string) config('scanlink.admin_email');

        $this->trySendFormBuilderMail($email, new ScanlinkMail(
            'ScanLink order confirmation - order number:'.$order->id,
            'emails.formbuilder-purchase-client',
            ['firstName' => $first, 'lastName' => $last, 'orderId' => $order->id, 'amount' => $amount],
        ));

        $this->trySendFormBuilderMail($adminEmail, new ScanlinkMail(
            'Scanlink Form Builder order summary - order number:'.$order->id,
            'emails.formbuilder-purchase-admin',
            [
                'orderId' => $order->id,
                'email' => $email,
                'firstName' => $first,
                'lastName' => $last,
                'noOfCodes' => (int) $order->no_of_codes,
                'amount' => $amount,
            ],
        ));

        SystemNotifier::toMember(
            $member,
            'Form builder activated',
            'Form builder was activated for your profile (order #'.$order->id.'). A tax invoice will follow.',
            'heroicon-o-document-text',
            'success',
        );

        SystemNotifier::toAdmins(
            'Form builder order',
            trim($first.' '.$last).' activated form builder (order #'.$order->id.').',
            'heroicon-o-document-text',
            'info',
        );
    }

    protected function trySendFormBuilderMail(string $email, ScanlinkMail $mail): void
    {
        $email = strtolower(trim($email));

        if ($email === '' || ! str_contains($email, '@')) {
            return;
        }

        try {
            Mail::to($email)->send($mail);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('submitOrder')
                ->label('Submit order')
                ->submit('submitOrder'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('submitOrder')
            ->footer([
                Actions::make($this->getFormActions())
                    ->key('form-actions'),
            ]);
    }
}
