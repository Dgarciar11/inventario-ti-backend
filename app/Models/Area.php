<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Area extends Model
{
    protected $fillable = [
        'client_id',
        'location_id',
        'external_id',
        'name',
        'is_active',
    ];

    #[Override]
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this -> belongsTo(Client::class);
    }

    public function location(): BelongsTo
    {
        return $this -> belongsTo(Location::class);
    }

    public function equipment(): HasMany
    {
        return $this -> hasMany(Equipment::class);
    }
}
