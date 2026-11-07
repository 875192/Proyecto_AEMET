<?php
declare(strict_types=1);

// Acceso a la tabla ciudades.
final class Ciudad
{
    public static function todas(): array
    {
        return Database::conexion()
            ->query('SELECT id_ciudad, nombre, indicativo, altitud FROM ciudades ORDER BY nombre')
            ->fetchAll();
    }
}
