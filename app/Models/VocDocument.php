<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class VocDocument extends Model
{
    protected $table = 'voc_documents';

    protected $primaryKey = 'voc_document_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['voc_document_id', 'profile_id', 'name', 'expiry_date', 'file_name'];

    /**
     * "No expiry date" is stored as the zero date (and, in some imported rows, the 1970 sentinel).
     * A plain date cast turns 0000-00-00 into 30/11/-0001, which is what the VOC dashboard was
     * printing next to those documents. Both sentinels read as null: no date, not a date in the
     * year -1.
     */
    protected function expiryDate(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): ?Carbon {
                $date = substr((string) ($value ?? ''), 0, 10);

                if ($date === '' || in_array($date, ['0000-00-00', '1970-01-01'], true)) {
                    return null;
                }

                try {
                    return Carbon::parse($date)->startOfDay();
                } catch (\Throwable) {
                    return null;
                }
            },
        );
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
