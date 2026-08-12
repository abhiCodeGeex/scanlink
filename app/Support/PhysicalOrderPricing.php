<?php

namespace App\Support;

use App\Models\Order;

class PhysicalOrderPricing
{
    /**
     * @return array{
     *     small_label: string,
     *     big_label: string,
     *     small_total: float,
     *     large_total: float,
     *     subtotal: float,
     *     postage: float,
     *     grand_total: float
     * }
     */
    public static function summarize(Order $order): array
    {
        $cutoff = $order->ordered_on?->format('Y-m-d') ?? now()->format('Y-m-d');
        $useNewLabels = $cutoff > '2014-07-31';

        $smallTotal = (float) $order->qty_small * (float) $order->price_small;
        $largeTotal = (float) $order->qty_large * (float) $order->price_large;
        $subtotal = $smallTotal + $largeTotal;
        $postage = PricingSettings::labelPostage();

        return [
            'small_label' => $useNewLabels ? '50 X 40 mm label' : '45 X 60 mm label',
            'big_label' => $useNewLabels ? '100 X 75 mm label' : '90 X 120 mm label',
            'small_total' => $smallTotal,
            'large_total' => $largeTotal,
            'subtotal' => $subtotal,
            'postage' => $postage,
            'grand_total' => $subtotal + $postage,
        ];
    }
}
