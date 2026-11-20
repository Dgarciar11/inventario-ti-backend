<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'serial' => ['required', 'string', 'max:255', 'unique:equipment,serial'],
            'status' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'El cliente es obligatorio.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'name.required' => 'El nombre del equipo es obligatorio.',
            'serial.required' => 'El serial del equipo es obligatorio.',
            'serial.unique' => 'Ya existe un equipo registrado con este serial.',
            'status.required' => 'El estado del equipo es obligatorio.',
        ];
    }
}
