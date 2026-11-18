<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class Equipment extends Model
{
    protected $fillable = [
        'client_id',
        'location_id',
        'area_id',
        'current_responsible_employee_id',
        'code',
        'name',
        'type',
        'brand',
        'model',
        'serial_number',
        'status',
        'purchase_date',
        'warranty_expires_at',
        'notes',
        'is_active',
    ];

    #[Override]
    protected function casts(): array
    {
        return [
            'purchase_data' => 'date',
            'warranty_expires_at' => 'date',
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

    public function area(): BelongsTo
    {
        return $this -> belongsTo(Area::class);
    }

    public function assigment(): HasMany
    {
        return $this -> hasMany(EquipmentAssignment::class);
    }
}
