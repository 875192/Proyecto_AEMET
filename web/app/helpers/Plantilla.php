<?php
declare(strict_types=1);

// Motor de plantillas mínimo. Las vistas son archivos .html sin código PHP; este motor las rellena
// con los datos que prepara el controlador. Así la interfaz (HTML) queda separada de la lógica (PHP).
//
// Sintaxis de las plantillas:
//   {{nombre}}            valor escapado (seguro frente a XSS). Admite "usuario.nick".
//   {{{nombre}}}          valor sin escapar (solo para HTML ya generado por la aplicación).
//   {{#si nombre}}...{{/si}}     se muestra si el valor existe y no está vacío.
//   {{#no nombre}}...{{/no}}     se muestra si el valor no existe o está vacío.
//   {{#cada lista}}...{{/cada}}  repite el bloque por cada elemento; dentro se usan sus campos.
final class Plantilla
{
    // Rellena una plantilla (texto HTML) con los datos indicados.
    public static function renderizar(string $html, array $datos): string
    {
        $trozos = preg_split('/(\{\{\{.+?\}\}\}|\{\{.+?\}\})/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        $posicion = 0;
        $arbol = self::analizar($trozos, $posicion, null);
        return self::pintar($arbol, [$datos]);
    }

    // Convierte los trozos en un árbol de nodos: texto, variable, variable sin escapar y bloques.
    private static function analizar(array $trozos, int &$i, ?string $cierre): array
    {
        $nodos = [];
        while ($i < count($trozos)) {
            $t = $trozos[$i++];
            if (str_starts_with($t, '{{{')) {
                $nodos[] = ['crudo', trim(substr($t, 3, -3))];
            } elseif (str_starts_with($t, '{{')) {
                $etiqueta = trim(substr($t, 2, -2));
                if (preg_match('/^#(si|no|cada)\s+(\S+)$/', $etiqueta, $m)) {
                    $hijos = self::analizar($trozos, $i, $m[1]);
                    $nodos[] = [$m[1], $m[2], $hijos];
                } elseif (preg_match('/^\/(si|no|cada)$/', $etiqueta, $m)) {
                    if ($m[1] !== $cierre) {
                        throw new RuntimeException("Plantilla: cierre inesperado {{/{$m[1]}}}");
                    }
                    return $nodos;
                } else {
                    $nodos[] = ['var', $etiqueta];
                }
            } elseif ($t !== '') {
                $nodos[] = ['texto', $t];
            }
        }
        if ($cierre !== null) {
            throw new RuntimeException("Plantilla: falta cerrar {{/$cierre}}");
        }
        return $nodos;
    }

    // Recorre el árbol y genera el HTML final. $pila contiene el contexto actual y los anteriores.
    private static function pintar(array $nodos, array $pila): string
    {
        $salida = '';
        foreach ($nodos as $nodo) {
            switch ($nodo[0]) {
                case 'texto':
                    $salida .= $nodo[1];
                    break;
                case 'var':
                    $salida .= Vista::e(self::buscar($nodo[1], $pila));
                    break;
                case 'crudo':
                    $salida .= (string) self::buscar($nodo[1], $pila);
                    break;
                case 'si':
                    if (self::tieneValor(self::buscar($nodo[1], $pila))) {
                        $salida .= self::pintar($nodo[2], $pila);
                    }
                    break;
                case 'no':
                    if (!self::tieneValor(self::buscar($nodo[1], $pila))) {
                        $salida .= self::pintar($nodo[2], $pila);
                    }
                    break;
                case 'cada':
                    $lista = self::buscar($nodo[1], $pila);
                    if (is_array($lista)) {
                        foreach ($lista as $elemento) {
                            $contexto = is_array($elemento) ? $elemento : ['.' => $elemento];
                            $salida .= self::pintar($nodo[2], array_merge([$contexto], $pila));
                        }
                    }
                    break;
            }
        }
        return $salida;
    }

    // Busca un nombre (con puntos para campos anidados) en el contexto actual y, si no está, en los anteriores.
    private static function buscar(string $nombre, array $pila): mixed
    {
        $partes = $nombre === '.' ? ['.'] : explode('.', $nombre);
        foreach ($pila as $contexto) {
            $valor = $contexto;
            foreach ($partes as $parte) {
                if (is_array($valor) && array_key_exists($parte, $valor)) {
                    $valor = $valor[$parte];
                } else {
                    continue 2;
                }
            }
            return $valor;
        }
        return null;
    }

    private static function tieneValor(mixed $valor): bool
    {
        return $valor !== null && $valor !== false && $valor !== '' && $valor !== [];
    }
}
