<?php
/**
 * Funciones de uso general: escape, URLs, redirecciones, mensajes flash,
 * protección CSRF, formatos locales y paginado.
 */
defined('APP') or exit;

/** Escapa cualquier valor para mostrarlo en HTML (previene XSS). */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL relativa al sitio, respetando si la app vive en una subcarpeta. */
function url(string $ruta = ''): string
{
    return APP_BASE . '/' . ltrim($ruta, '/');
}

/** URL absoluta (para links dentro de emails). */
function url_absoluta(string $ruta = ''): string
{
    return APP_URL . '/' . ltrim($ruta, '/');
}

function redirect(string $ruta): never
{
    header('Location: ' . url($ruta));
    exit;
}

function es_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

// ---------------- Mensajes flash ----------------

function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function tomar_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

// ---------------- CSRF ----------------

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Corta la ejecución si un POST no trae un token CSRF válido. */
function verificar_csrf(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if (!is_string($enviado) || !hash_equals(csrf_token(), $enviado)) {
        pagina_error(419, 'La página expiró', 'Por seguridad, el formulario venció. Volvé a la página anterior, recargala e intentá de nuevo.');
    }
}

// ---------------- Entrada ----------------

function get_str(string $clave, int $max = 100): string
{
    $v = $_GET[$clave] ?? '';
    return is_string($v) ? mb_substr(trim($v), 0, $max) : '';
}

function get_int(string $clave, int $defecto = 0): int
{
    $v = filter_var($_GET[$clave] ?? null, FILTER_VALIDATE_INT);
    return $v === false || $v === null ? $defecto : $v;
}

function post_int(string $clave): int
{
    $v = filter_var($_POST[$clave] ?? null, FILTER_VALIDATE_INT);
    return $v === false || $v === null ? 0 : $v;
}

// ---------------- Formatos locales ----------------

function fecha_es(?string $fecha): string
{
    if (!$fecha) {
        return '—';
    }
    $t = strtotime($fecha);
    return $t ? date('d/m/Y', $t) : '—';
}

function fecha_hora_es(?string $fecha): string
{
    if (!$fecha) {
        return '—';
    }
    $t = strtotime($fecha);
    return $t ? date('d/m/Y H:i', $t) . ' h' : '—';
}

function numero_es(int|float $n): string
{
    return number_format($n, 0, ',', '.');
}

// ---------------- Formularios ----------------

/** Mensaje de error al lado de un campo, con id para aria-describedby. */
function error_campo(array $errores, string $campo): string
{
    if (empty($errores[$campo])) {
        return '';
    }
    return '<p class="error-campo" id="error-' . e($campo) . '">' . e($errores[$campo]) . '</p>';
}

/** Atributos de accesibilidad para un campo con error. */
function attr_error(array $errores, string $campo): string
{
    return empty($errores[$campo]) ? '' : ' aria-invalid="true" aria-describedby="error-' . e($campo) . '"';
}

function seleccionado(mixed $a, mixed $b): string
{
    return (string) $a === (string) $b ? ' selected' : '';
}

function marcado(bool $condicion): string
{
    return $condicion ? ' checked' : '';
}

// ---------------- Paginado ----------------

/** Calcula página actual, offset y total de páginas. */
function paginar(int $total, int $porPagina = 9): array
{
    $paginas = max(1, (int) ceil($total / $porPagina));
    $pagina = min(max(1, get_int('pagina', 1)), $paginas);
    return ['pagina' => $pagina, 'paginas' => $paginas, 'offset' => ($pagina - 1) * $porPagina, 'limite' => $porPagina, 'total' => $total];
}

/** Dibuja el paginado conservando los filtros de la URL. */
function paginador(array $p): string
{
    if ($p['paginas'] <= 1) {
        return '';
    }
    $params = $_GET;
    $link = function (int $n) use ($params): string {
        $params['pagina'] = $n;
        return '?' . http_build_query($params);
    };
    $html = '<nav class="paginado" aria-label="Paginado">';
    if ($p['pagina'] > 1) {
        $html .= '<a class="btn btn-sec" href="' . e($link($p['pagina'] - 1)) . '" rel="prev">‹ Anterior</a>';
    }
    $html .= '<span>Página ' . $p['pagina'] . ' de ' . $p['paginas'] . '</span>';
    if ($p['pagina'] < $p['paginas']) {
        $html .= '<a class="btn btn-sec" href="' . e($link($p['pagina'] + 1)) . '" rel="next">Siguiente ›</a>';
    }
    return $html . '</nav>';
}

// ---------------- Páginas de error ----------------

function pagina_error(int $codigo, string $titulo, string $mensaje): never
{
    http_response_code($codigo);
    $migas = [];
    $seccion = '';
    require __DIR__ . '/vistas/encabezado.php';
    echo '<section class="panel error-pagina">';
    echo '<p class="codigo-error">Error ' . $codigo . '</p>';
    echo '<h1>' . e($titulo) . '</h1><p>' . e($mensaje) . '</p>';
    echo '<p class="acciones"><a class="btn" href="' . e(url('index.php')) . '">Ir al inicio</a></p>';
    echo '</section>';
    require __DIR__ . '/vistas/pie.php';
    exit;
}
