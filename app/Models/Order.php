<?php

namespace App\Models;

use App\Enums\PhysicalOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /** Live `orders` table has no created_at/updated_at (uses ordered_on). */
    public $timestamps = false;

    protected $fillable = [
        'client_id', 'user_id', 'profile_id', 'qty_small', 'qty_large',
        'price_small', 'price_large', 'status', 'transaction_id',
        'first_name', 'last_name', 'address1', 'address2', 'city', 'state',
        'zip', 'country', 'email', 'contact', 'ordered_on',
    ];

    protected function casts(): array
    {
        return [
            'price_small' => 'decimal:2',
            'price_large' => 'decimal:2',
            'status' => PhysicalOrderStatus::class,
            'ordered_on' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
