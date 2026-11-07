<?php
declare(strict_types=1);

// Conexión única a MySQL mediante PDO. Solo los modelos usan esta clase.
final class Database
{
    private static ?PDO $conexion = null;

    public static function conexion(): PDO
    {
        if (self::$conexion === null) {
            $dsn = Config::get('db.dsn');
            if ($dsn === '') {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    Config::get('db.host'),
                    Config::get('db.port'),
                    Config::get('db.nombre')
                );
            }
            self::$conexion = new PDO($dsn, Config::get('db.usuario'), Config::get('db.password'), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$conexion;
    }
}
