<?php

namespace App\Support;

use App\Models\CodePurchase;
use Illuminate\Support\Collection;

class CodePurchasePricing
{
    /**
     * @return array{lines: list<string>, subtotal: float, grand_total: float}
     */
    public static function summarize(CodePurchase $order): array
    {
        $order->loadMissing('details');

        if ((float) $order->per_code_amount > 0) {
            $subtotal = (float) $order->total_amount > 0
                ? (float) $order->total_amount
                : (float) $order->no_of_codes * (float) $order->per_code_amount;

            return [
                'lines' => [sprintf('$%s AUD', number_format((float) $order->per_code_amount, 2))],
                'subtotal' => $subtotal,
                'grand_total' => $subtotal,
            ];
        }

        $amounts = $order->details
            ->pluck('amount')
            ->filter(fn ($amount): bool => $amount !== null && (float) $amount > 0);

        if ($amounts->isEmpty()) {
            $subtotal = (float) $order->no_of_codes * (float) $order->per_code_amount * 12;

            return [
                'lines' => ['$0.00 AUD'],
                'subtotal' => $subtotal,
                'grand_total' => (float) $order->total_amount > 0 ? (float) $order->total_amount : $subtotal,
            ];
        }

        $lines = self::tierLines($amounts);

        $subtotal = (float) $order->total_amount > 0
            ? (float) $order->total_amount
            : $amounts->sum(fn ($amount): float => 12 * (float) $amount);

        return [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'grand_total' => $subtotal,
        ];
    }

    /**
     * @param  Collection<int, mixed>  $amounts
     * @return list<string>
     */
    protected static function tierLines(Collection $amounts): array
    {
        $lines = [];
        $seen = [];

        foreach ($amounts as $amount) {
            $key = number_format((float) $amount, 2, '.', '');

            if (in_array($key, $seen, true)) {
                continue;
            }

            $seen[] = $key;
            $count = $amounts->filter(fn ($value): bool => number_format((float) $value, 2, '.', '') === $key)->count();
            $totalAmt = number_format(12 * (float) $amount, 2);

            $lines[] = sprintf('%d code/s @ $%s', $count, $totalAmt);
        }

        return $lines;
    }
}
