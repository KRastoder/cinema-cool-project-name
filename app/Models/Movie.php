<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'duration',
        'description',
        'poster',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    public function shows(): HasMany
    {
        return $this->hasMany(Show::class);
    }
}
