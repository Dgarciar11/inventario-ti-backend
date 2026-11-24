<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Conexión LDAP por defecto
    |--------------------------------------------------------------------------
    */

    // Indica qué conexión de LDAP usará la aplicación.
    'default' => env('LDAP_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Conexiones LDAP
    |--------------------------------------------------------------------------
    */

    'connections' => [

        // Conexión principal con Active Directory.
        'default' => [
            // Dirección del servidor LDAP.
            'hosts' => [env('LDAP_HOST', '127.0.0.1')],

            // Usuario técnico para consultar Active Directory.
            'username' => env('LDAP_USERNAME', 'cn=user,dc=local,dc=com'),

            // Contraseña del usuario técnico.
            'password' => env('LDAP_PASSWORD', 'secret'),

            // Puerto estándar de LDAP.
            'port' => env('LDAP_PORT', 389),

            // Dominio base donde se buscarán los usuarios.
            'base_dn' => env('LDAP_BASE_DN', 'dc=local,dc=com'),

            // Tiempo máximo de espera para conectar.
            'timeout' => env('LDAP_TIMEOUT', 5),

            // Opciones de seguridad; por ahora están desactivadas.
            'use_tls' => env('LDAP_TLS', false),
            'use_starttls' => env('LDAP_STARTTLS', false),
            'use_sasl' => env('LDAP_SASL', false),

            'sasl_options' => [
                // 'mech' => 'GSSAPI',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Registro de operaciones LDAP
    |--------------------------------------------------------------------------
    */

    // Guarda información de búsquedas y autenticaciones en los logs.
    'logging' => [
        'enabled' => env('LDAP_LOGGING', true),
        'channel' => env('LOG_CHANNEL', 'stack'),
        'level' => env('LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caché de LDAP
    |--------------------------------------------------------------------------
    */

    // Permite guardar resultados de búsquedas para que sean más rápidas.
    'cache' => [
        'enabled' => env('LDAP_CACHE', false),
        'driver' => env('CACHE_DRIVER', 'file'),
    ],

];
