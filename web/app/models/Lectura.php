<?php
declare(strict_types=1);

// Acceso a la tabla lecturas_diarias (los datos meteorológicos los carga el ETL; la web solo los lee).
final class Lectura
{
    public static function contar(): int
    {
        return (int) Database::conexion()->query('SELECT COUNT(*) FROM lecturas_diarias')->fetchColumn();
    }
}
