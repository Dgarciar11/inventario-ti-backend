<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Location extends Model
{
    protected $fillable = [
        'client_id',
        'external_id',
        'name',
        'address',
        'is_active',
    ];

    #[Override]
    protected function casts(): array
    {
        return[
            'is_active' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this -> belongsTo(Client::class);
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
