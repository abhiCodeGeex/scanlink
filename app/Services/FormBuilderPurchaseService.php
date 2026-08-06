<?php

namespace App\Services;

use App\Enums\CodeOrderStatus;
use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\FormBuilderOrder;
use App\Models\FormBuilderOrderDetail;
use App\Models\Profile;
use App\Support\SystemNotifier;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FormBuilderPurchaseService
{
    public const PRICE = 5.00;

    /**
     * @param  array<string, mixed>  $billing
     * @param  array<string, mixed>  $checkout
     */
    public function purchase(
        Profile $profile,
        Client $client,
        ClientUser $member,
        array $billing,
        array $checkout = [],
    ): FormBuilderOrder {
        $order = DB::transaction(function () use ($profile, $client, $billing, $checkout): FormBuilderOrder {
            /** @var Profile $lockedProfile */
            $lockedProfile = Profile::query()
                ->where('client_id', $client->id)
                ->active()
                ->claimedSlot()
                ->lockForUpdate()
                ->findOrFail($profile->id);

            if ((bool) $lockedProfile->form_active) {
                throw new DomainException('Form Builder is already activated for this profile.');
            }

            if ($lockedProfile->isExpired()) {
                throw new DomainException('Form Builder cannot be activated on an expired code profile.');
            }

            $payload = [
                'client_id' => $client->id,
                'email' => strtolower(trim((string) $billing['email'])),
                'town' => trim((string) $billing['town']),
                'first_name' => trim((string) $billing['first_name']),
                'last_name' => trim((string) $billing['last_name']),
                'company_name' => trim((string) $billing['company_name']),
                'billing_address' => trim((string) $billing['billing_address']),
                'phone' => trim((string) $billing['phone']),
                'postal_code' => trim((string) $billing['postal_code']),
                'no_of_codes' => 1,
                'per_code_amount' => self::PRICE,
                'status' => CodeOrderStatus::New,
                'enable' => false,
                'exipry_date' => now()->addDays(365),
                'is_reseller_pricing_code' => (string) ($checkout['is_reseller_pricing_code'] ?? '0'),
            ];

            // Legacy live table stores no total_amount; total is quantity x unit price.
            if (FormBuilderOrder::hasTotalAmountColumn()) {
                $payload['total_amount'] = self::PRICE;
            }

            $order = FormBuilderOrder::query()->create($payload);

            FormBuilderOrderDetail::query()->create([
                'form_builder_order_id' => $order->id,
                'profile_id' => $lockedProfile->id,
            ]);

            $lockedProfile->forceFill([
                'form_active' => true,
                'form_is_enable' => true,
                'pop_up_formbuilder' => true,
            ])->save();

            return $order;
        });

        $this->dispatchNotifications($order, $client, $member, $billing, $checkout);

        return $order;
    }

    /**
     * @param  array<string, mixed>  $billing
     * @param  array<string, mixed>  $checkout
     */
    public function dispatchNotifications(
        FormBuilderOrder $order,
        Client $client,
        ClientUser $member,
        array $billing,
        array $checkout = [],
    ): void {
        $amount = number_format($order->totalAmount(), 2);
        $buyerEmail = strtolower(trim((string) $billing['email']));
        $firstName = (string) $billing['first_name'];
        $lastName = (string) $billing['last_name'];
        $resellerId = (int) ($checkout['reseller_client_id'] ?? 0);
        $reseller = $resellerId > 0 ? Client::query()->find($resellerId) : null;

        if ($reseller) {
            $recipientEmail = strtolower(trim((string) ($reseller->reseller_email ?: $reseller->email)));
            $recipientName = trim((string) $reseller->client_name);

            // Legacy reseller_mail_client_for_formbuilder: single-name greeting, the
            // activation line shows the quantity ($1.00 from no_of_codes) while the total
            // stays at per_code_amount ($5.00), and support phone is 1300 566 696.
            $this->trySend($recipientEmail, new ScanlinkMail(
                'ScanLink order confirmation - order number:'.$order->id,
                'emails.formbuilder-purchase-reseller',
                [
                    'name' => $recipientName,
                    'orderId' => $order->id,
                    'activationAmount' => number_format((float) ($order->no_of_codes ?: 1), 2),
                    'amount' => $amount,
                ],
            ));

            SystemNotifier::toClientPrimary(
                $reseller,
                'Form builder order',
                trim($firstName.' '.$lastName).' activated Form Builder using your reseller code (order #'.$order->id.').',
                'heroicon-o-document-text',
                'info',
            );
        } else {
            $this->trySend($buyerEmail, new ScanlinkMail(
                'ScanLink order confirmation - order number:'.$order->id,
                'emails.formbuilder-purchase-client',
                [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'orderId' => $order->id,
                    'amount' => $amount,
                ],
            ));

            SystemNotifier::toMember(
                $member,
                'Form builder activated',
                'Form Builder was activated for your profile (order #'.$order->id.'). A tax invoice will follow.',
                'heroicon-o-document-text',
                'success',
            );
        }

        $this->trySend((string) config('scanlink.admin_email'), new ScanlinkMail(
            'Scanlink Form Builder order summary - order number:'.$order->id,
            'emails.formbuilder-purchase-admin',
            [
                'orderId' => $order->id,
                'intro' => $reseller
                    ? 'A reseller has ordered a form builder at scanlink.'
                    : 'User has ordered a form builder at scanlink.',
                'email' => $reseller
                    ? strtolower(trim((string) ($reseller->reseller_email ?: $reseller->email)))
                    : $buyerEmail,
                'firstName' => $reseller ? (string) $reseller->client_name : $firstName,
                'lastName' => $reseller ? '' : $lastName,
                'noOfCodes' => 1,
                'amount' => $amount,
            ],
        ));

        SystemNotifier::toAdmins(
            'Form builder order',
            trim($firstName.' '.$lastName).' activated Form Builder (order #'.$order->id.').',
            'heroicon-o-document-text',
            'info',
        );
    }

    private function trySend(string $email, ScanlinkMail $mail): void
    {
        $email = strtolower(trim($email));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($email)->send($mail);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
