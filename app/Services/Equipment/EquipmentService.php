<?php

namespace App\Services\Equipment;

use App\Models\Equipment;

class EquipmentService
{
    public function create(array $data): Equipment
    {
        return Equipment::create($data);
    }
}
