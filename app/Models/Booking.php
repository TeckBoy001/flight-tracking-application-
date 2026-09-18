<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'flight_id',
        'booking_reference',
        'passenger_name',
        'passenger_email',
        'seats_booked',
        'total_price',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    /**
     * Produces tracking codes in the style "KOH-482951": three letters,
     * a dash, six digits. Easy to read aloud and to type back in.
     */
    public static function generateReference(): string
    {
        // Skip visually-confusing letters (I, O) so codes are easy to read back.
        $pool = 'ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $letters = '';
            for ($i = 0; $i < 3; $i++) {
                $letters .= $pool[random_int(0, strlen($pool) - 1)];
            }
            $digits = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $reference = "{$letters}-{$digits}";
        } while (self::where('booking_reference', $reference)->exists());

        return $reference;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }
}
