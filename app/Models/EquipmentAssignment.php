<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Psy\Readline\Interactive\Renderer\Area;

class EquipmentAssignment extends Model
{
    protected $fillable = [
        'equipment_id',
        'client_id',
        'employee_id',
        'location_id',
        'area_id',
        'assigment_type',
        'assigned_at',
        'returned_at',
        'notes',
    ];

    protected function casts(): array
    {
        return[
            'assigned_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public function equipment(): BelongsTo
    {
        return $this -> belongsTo(Equipment::class);
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
}
