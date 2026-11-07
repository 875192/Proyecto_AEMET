<?php
declare(strict_types=1);

// Página de inicio. De momento solo comprueba que la web llega a la base de datos;
// se sustituirá por el login y el menú principal cuando exista la gestión de usuarios.
final class HomeController
{
    public function index(): void
    {
        $ciudades = [];
        $lecturas = 0;
        $error    = null;
        try {
            $ciudades = Ciudad::todas();
            $lecturas = Lectura::contar();
        } catch (PDOException $e) {
            error_log('Error de base de datos: ' . $e->getMessage());
            $error = 'No se pudo conectar con la base de datos.';
        }
        Vista::render('home/index', compact('ciudades', 'lecturas', 'error'), 'Inicio');
    }
}
