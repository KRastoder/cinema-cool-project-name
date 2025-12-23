<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Reservation extends Model
{
    protected $fillable = [
        'code',
        'name',
        'email',
        'status',
        'total_price',
        'expires_at',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'total_price' => 'decimal:2',
    ];

    // Auto-generate reservation code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->code)) {
                $reservation->code = strtoupper(Str::random(8));
            }

            if (empty($reservation->expires_at)) {
                $reservation->expires_at = now()->addMinutes(10);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function showSeats(): HasMany
    {
        return $this->hasMany(ShowSeat::class);
    }

    // Helper: Check if reservation is expired
    public function isExpired(): bool
    {
        return $this->status === 'pending' && $this->expires_at < now();
    }

    // Helper: Confirm reservation
    public function confirm(): void
    {
        $this->update(['status' => 'confirmed']);
        $this->showSeats()->update(['status' => 'sold']);
    }

    // Helper: Cancel reservation
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
        $this->showSeats()->update([
            'status'         => 'available',
            'reservation_id' => null,
            'held_at'        => null,
        ]);
    }
}
