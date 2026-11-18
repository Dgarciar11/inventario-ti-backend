<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Client extends Model
{
    protected $fillable = [
        'name',
        'tax_id',
        'is_active'
    ];

    #[Override]
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function locations(): HasMany
    {
        return $this -> hasMany(Location::class);
    }

    public function areas(): HasMany
    {
        return $this -> hasMany(Area::class);
    }

    public function equipment(): HasMany
    {
        return $this -> hasMany(Equipment::class);
    }
}
