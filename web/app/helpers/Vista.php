<?php
declare(strict_types=1);

// Renderizado de vistas. Toda página se compone de cabecera, cuerpo y pie claramente diferenciados (RQ25).
final class Vista
{
    public static function render(string $vista, array $datos = [], string $titulo = ''): void
    {
        $tituloPagina = $titulo !== '' ? $titulo . ' · ' . Config::get('app.nombre') : Config::get('app.nombre');
        $aviso = Sesion::tomarAviso();
        extract($datos, EXTR_SKIP);

        require RAIZ_APP . '/views/layout/header.php';
        require RAIZ_APP . "/views/$vista.php";
        require RAIZ_APP . '/views/layout/footer.php';
    }

    // Escapa texto antes de mostrarlo en HTML (evita XSS).
    public static function e(mixed $texto): string
    {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }
}
