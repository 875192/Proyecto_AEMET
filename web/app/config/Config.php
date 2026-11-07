<?php
declare(strict_types=1);

// Acceso a los ajustes con notación de puntos: Config::get('db.host').
final class Config
{
    private static ?array $datos = null;

    public static function get(string $clave, mixed $defecto = null): mixed
    {
        self::$datos ??= require __DIR__ . '/ajustes.php';
        $valor = self::$datos;
        foreach (explode('.', $clave) as $parte) {
            if (!is_array($valor) || !array_key_exists($parte, $valor)) {
                return $defecto;
            }
            $valor = $valor[$parte];
        }
        return $valor;
    }
}
