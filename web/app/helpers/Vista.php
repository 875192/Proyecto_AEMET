<?php
declare(strict_types=1);

// Renderizado de vistas. Cada vista es un archivo .html (sin PHP) en app/views/.
// Toda página se compone de cabecera, cuerpo y pie claramente diferenciados (RQ25).
final class Vista
{
    public static function render(string $vista, array $datos = [], string $titulo = ''): void
    {
        $nombreApp = Config::get('app.nombre');
        $usuario = Sesion::estaIdentificado() ? Sesion::usuario() : null;

        // Datos comunes a todas las páginas; los del controlador tienen prioridad.
        $comunes = [
            'nombreApp'    => $nombreApp,
            'tituloPagina' => $titulo !== '' ? $titulo . ' · ' . $nombreApp : $nombreApp,
            'aviso'        => Sesion::tomarAviso(),
            'usuario'      => $usuario,
        ];
        $datos = array_merge($comunes, $datos);

        echo self::cargar('layout/header', $datos)
           . self::cargar($vista, $datos)
           . self::cargar('layout/footer', $datos);
    }

    // Lee una plantilla .html y la rellena con los datos.
    private static function cargar(string $vista, array $datos): string
    {
        $fichero = RAIZ_APP . "/views/$vista.html";
        if (!is_file($fichero)) {
            throw new RuntimeException("No existe la vista: $vista");
        }
        return Plantilla::renderizar((string) file_get_contents($fichero), $datos);
    }

    // Escapa texto antes de mostrarlo en HTML (evita XSS).
    public static function e(mixed $texto): string
    {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }
}
