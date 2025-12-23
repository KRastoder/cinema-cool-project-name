<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShowSeat extends Model
{
    protected $fillable = [
        'show_id',
        'seat_id',
        'reservation_id',
        'status',
        'held_at',
    ];

    protected $casts = [
        'held_at' => 'datetime',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    // Helper: Check if seat hold is expired
    public function isHoldExpired(): bool
    {
        return $this->status === 'held'
        && $this->held_at
        && $this->held_at->addMinutes(10) < now();
    }

    // Helper: Release held seat
    public function release(): void
    {
        $this->update([
            'status'         => 'available',
            'reservation_id' => null,
            'held_at'        => null,
        ]);
    }
}
