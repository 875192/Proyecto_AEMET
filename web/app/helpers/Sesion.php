<?php
declare(strict_types=1);

// Gestión de la sesión de usuario, incluida la caducidad por inactividad (CU2 del ERS):
// si un usuario identificado no interactúa durante 5 minutos, su sesión se cancela.
final class Sesion
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();

        if (self::haCaducado()) {
            self::cerrar();
            session_start();
            $_SESSION['aviso'] = 'Tu sesión ha caducado por inactividad. Vuelve a identificarte.';
        }
        if (self::estaIdentificado()) {
            $_SESSION['ultima_actividad'] = time();   // cada petición reinicia el contador
        }
    }

    // True si hay un usuario identificado y han pasado más segundos de los permitidos desde su última actividad.
    public static function haCaducado(): bool
    {
        if (!self::estaIdentificado()) {
            return false;
        }
        $ultima = (int) ($_SESSION['ultima_actividad'] ?? 0);
        return (time() - $ultima) > (int) Config::get('sesion.caducidad', 300);
    }

    // Registra al usuario en la sesión (se llamará desde el login, una vez validada la contraseña).
    public static function identificar(array $usuario): void
    {
        session_regenerate_id(true);   // evita la fijación de sesión
        $_SESSION['usuario'] = [
            'id'   => (int) $usuario['id_usuario'],
            'nick' => (string) $usuario['nick'],
            'rol'  => (string) $usuario['rol'],
        ];
        $_SESSION['ultima_actividad'] = time();
    }

    public static function cerrar(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function estaIdentificado(): bool
    {
        return isset($_SESSION['usuario']);
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function esAdministrador(): bool
    {
        return (self::usuario()['rol'] ?? '') === 'administrador';
    }

    // Devuelve y borra el aviso pendiente (p. ej. "sesión caducada") para mostrarlo una sola vez.
    public static function tomarAviso(): ?string
    {
        $aviso = $_SESSION['aviso'] ?? null;
        unset($_SESSION['aviso']);
        return $aviso;
    }
}
