<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Campos que se pueden guardar
#[Fillable([
    'name',
    'email',
    'ldap_username',
    'is_active',
    'rol',
    'last_login_at',
])]
class User extends Authenticatable
{
    // HasApiTokens: permite crear tokens para el login.
    // HasFactory: permite usar UserFactory para pruebas.
    // Notifiable: permite enviar notificaciones si más adelante se necesita.
    use HasApiTokens, HasFactory, Notifiable;

    // Convierte los datos al tipo correcto al consultarlos o guardarlos.
    protected function casts(): array
    {
        return [
            // Guarda y lee "activo/inactivo" como verdadero o falso.
            'is_active' => 'boolean',

            // Guarda y lee la fecha del último inicio de sesión.
            'last_login_at' => 'datetime',
        ];
    }
}
