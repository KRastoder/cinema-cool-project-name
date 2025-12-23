<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hall extends Model
{
    protected $fillable = [
        'name',
        'rows',
        'columns',
        'row_gap',
        'col_gap',
    ];

    protected $casts = [
        'row_gap' => 'array',
        'col_gap' => 'array',
    ];

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function shows(): HasMany
    {
        return $this->hasMany(Show::class);
    }

    // Helper: Get total capacity (excluding blocked seats)
    public function getCapacityAttribute(): int
    {
        return $this->seats()->where('type', '!=', 'blocked')->count();
    }

    // Helper: Check if there's a gap after specific row
    public function hasRowGapAfter(int $row): bool
    {
        return $this->row_gap && in_array($row, $this->row_gap);
    }

    // Helper: Check if there's a gap after specific column
    public function hasColGapAfter(int $col): bool
    {
        return $this->col_gap && in_array($col, $this->col_gap);
    }
}
