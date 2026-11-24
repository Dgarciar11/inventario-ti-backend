<?php

// Modelo que Laravel usará para autenticar usuarios.
use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración principal de autenticación
    |--------------------------------------------------------------------------
    */

    // Guard y recuperador de usuarios por defecto.
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Guards de autenticación
    |--------------------------------------------------------------------------
    |
    | "web": usa sesiones para aplicaciones con vistas.
    | "sanctum": usa tokens para la API.
    */

    'guards' => [
        // Guard para páginas web con sesión.
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Guard para la API con tokens Sanctum.
        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Proveedores de usuarios
    |--------------------------------------------------------------------------
    |
    | Indican de dónde se obtienen los usuarios.
    */

    'providers' => [
        // Usa el modelo User y la tabla users.
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Restablecimiento de contraseñas
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    // Tiempo en segundos antes de pedir nuevamente la contraseña.
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
