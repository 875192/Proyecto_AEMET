<?php
declare(strict_types=1);

// Ajustes de la aplicación. Los valores sensibles llegan por variables de entorno
// (las define docker-compose a partir del .env de la raíz del repositorio).
return [
    'app' => [
        'nombre' => 'Climate Change Analyzer',
    ],
    'db' => [
        'host'     => getenv('DB_HOST') ?: 'db',
        'port'     => (int) (getenv('DB_PORT') ?: 3306),
        'nombre'   => getenv('DB_NAME') ?: 'meteo_aemet',
        'usuario'  => getenv('DB_USER') ?: 'meteo',
        'password' => getenv('DB_PASSWORD') ?: '',
        'dsn'      => getenv('DB_DSN') ?: '',   // solo para pruebas (p. ej. SQLite); vacío = MySQL
    ],
    'sesion' => [
        'caducidad' => 300,   // segundos de inactividad antes de cancelar la sesión (5 min, CU2 del ERS)
    ],
];
