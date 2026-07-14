<?php

namespace App\Http\Controllers;

use App\Enums\PhysicalOrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PayPalNotifyController extends Controller
{
    public function __invoke(Request $request): Response
    {
        if ($request->input('payment_status') === 'Pending' && filled($request->input('txn_id'))) {
            $custom = (string) $request->input('custom', '');
            $orderId = (int) (explode('|', $custom)[2] ?? $custom);

            if ($orderId > 0) {
                Order::query()
                    ->where('id', $orderId)
                    ->update([
                        'transaction_id' => $request->input('txn_id'),
                        'status' => PhysicalOrderStatus::Paid,
                    ]);
            }
        }

        return response('OK', 200);
    }
}
