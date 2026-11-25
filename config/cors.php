<?php

return [

    // Rutas donde se permite la comunicación con el frontend.
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Permite los métodos que use la API.
    'allowed_methods' => ['*'],

    // Dirección del frontend autorizado.
    'allowed_origins' => ['http://localhost:5173'],

    // No usamos patrones para permitir otros orígenes.
    'allowed_origins_patterns' => [],

    // Permite los encabezados enviados por el frontend.
    'allowed_headers' => ['*'],

    // No exponemos encabezados adicionales a JavaScript.
    'exposed_headers' => [],

    // No guarda en caché la comprobación previa de CORS.
    'max_age' => 0,

    // Permite usar cookies en las peticiones desde React.
    'supports_credentials' => true,

];
