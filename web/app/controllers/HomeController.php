<?php
declare(strict_types=1);

// Página de inicio. De momento solo comprueba que la web llega a la base de datos;
// se sustituirá por el login y el menú principal cuando exista la gestión de usuarios.
final class HomeController
{
    public function index(): void
    {
        $filas = [];
        $numCiudades = 0;
        $lecturas = 0;
        $error = '';
        try {
            foreach (Ciudad::todas() as $c) {
                $filas[] = [
                    'nombre'     => $c['nombre'],
                    'indicativo' => $c['indicativo'],
                    'altitud'    => $c['altitud'] ?? '—',
                ];
            }
            $numCiudades = count($filas);
            $lecturas = number_format(Lectura::contar(), 0, ',', '.');
        } catch (PDOException $e) {
            error_log('Error de base de datos: ' . $e->getMessage());
            $error = 'No se pudo conectar con la base de datos.';
        }
        Vista::render('home/index', [
            'ciudades'    => $filas,
            'numCiudades' => $numCiudades,
            'lecturas'    => $lecturas,
            'error'       => $error,
        ], 'Inicio');
    }
}
