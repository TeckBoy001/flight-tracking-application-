<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flight extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Flight $flight): void {
            $flight->status ??= 'confirmed';
            $flight->tracking_code ??= self::generateTrackingCode();
        });
    }

    protected $fillable = [
        'flight_number',
        'tracking_code',
        'airline',
        'origin',
        'destination',
        'stops',
        'departure_time',
        'arrival_time',
        'price',
        'total_seats',
        'seats_available',
        'status',
        'is_delayed',
        'is_cancelled',
        'tracking_paused',
        'departure_latitude',
        'departure_longitude',
        'arrival_latitude',
        'arrival_longitude',
        'current_latitude',
        'current_longitude',
        'current_location_label',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'price' => 'decimal:2',
        'stops' => 'array',
        'is_delayed' => 'boolean',
        'is_cancelled' => 'boolean',
        'tracking_paused' => 'boolean',
        'departure_latitude' => 'decimal:7',
        'departure_longitude' => 'decimal:7',
        'arrival_latitude' => 'decimal:7',
        'arrival_longitude' => 'decimal:7',
        'current_latitude' => 'decimal:7',
        'current_longitude' => 'decimal:7',
    ];

    /**
     * The journey, in order. This is the single source of truth for the
     * tracking timeline — admins move a flight along this list, and the
     * frontend renders whatever position it's currently at. Delay and
     * cancellation are separate flags (see is_delayed / is_cancelled) so
     * they don't have to be modeled as steps in the same sequence.
     */
    public const TIMELINE_STAGES = [
        'confirmed' => 'Booking Confirmed',
        'checked_in' => 'Check-in Completed',
        'boarding' => 'Boarding',
        'departed' => 'Departed',
        'in_transit' => 'In Transit',
        'approaching' => 'Approaching Destination',
        'landed' => 'Landed',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = 'FLY-' . strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ'), 0, 3)) . '-' . random_int(100000, 999999);
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    public function locationHistory(): HasMany
    {
        return $this->hasMany(FlightLocation::class)->orderBy('recorded_at');
    }

    public function statusLabel(): string
    {
        return self::TIMELINE_STAGES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * 0-based position of the current status within TIMELINE_STAGES,
     * used to decide which timeline steps render as complete / current /
     * upcoming.
     */
    public function timelineIndex(): int
    {
        $position = array_search($this->status, array_keys(self::TIMELINE_STAGES), true);

        return $position === false ? 0 : $position;
    }

    public function statusColor(): string
    {
        if ($this->is_cancelled) {
            return 'bg-red-100 text-red-800';
        }

        if ($this->is_delayed) {
            return 'bg-amber-100 text-amber-800';
        }

        return match ($this->status) {
            'confirmed', 'checked_in' => 'bg-slate-100 text-slate-700',
            'boarding' => 'bg-blue-100 text-blue-800',
            'departed', 'in_transit', 'approaching' => 'bg-indigo-100 text-indigo-800',
            'landed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function badgeLabel(): string
    {
        if ($this->is_cancelled) {
            return 'Cancelled';
        }

        if ($this->is_delayed) {
            return 'Delayed';
        }

        return $this->statusLabel();
    }

    public function hasCurrentLocation(): bool
    {
        return $this->current_latitude !== null && $this->current_longitude !== null;
    }

    public function hasRouteCoordinates(): bool
    {
        return $this->departure_latitude !== null
            && $this->departure_longitude !== null
            && $this->arrival_latitude !== null
            && $this->arrival_longitude !== null;
    }

    public function currentLocationDisplay(): string
    {
        if ($this->current_location_label) {
            return $this->current_location_label;
        }

        if ($this->hasCurrentLocation()) {
            return number_format((float) $this->current_latitude, 2) . ', ' . number_format((float) $this->current_longitude, 2);
        }

        return match (true) {
            $this->status === 'landed' => $this->destination,
            in_array($this->status, ['confirmed', 'checked_in', 'boarding']) => $this->origin,
            default => 'Location not yet available',
        };
    }

    public function getStopsAttribute($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value), fn ($stop) => $stop !== ''));
        }

        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded), fn ($stop) => $stop !== ''));
        }

        return [];
    }

    public function setStopsAttribute($value): void
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\n|,/', $value);
        }

        if (! is_array($value)) {
            $value = [];
        }

        $this->attributes['stops'] = json_encode(array_values(array_filter(array_map('trim', $value), fn ($stop) => $stop !== '')));
    }
}
