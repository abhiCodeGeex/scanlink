<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerPricing extends Model
{
    protected $table = 'reseller_pricing';

    protected $fillable = ['code_qty', 'amount'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }
}
