<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodePrising extends Model
{
    protected $table = 'code_prising';

    protected $fillable = ['code_min_qty', 'code_max_qty', 'amount', 'reseller_amount'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'reseller_amount' => 'decimal:2',
        ];
    }

    public function tierLabel(): string
    {
        return "Codes {$this->code_min_qty} to {$this->code_max_qty}";
    }
}
