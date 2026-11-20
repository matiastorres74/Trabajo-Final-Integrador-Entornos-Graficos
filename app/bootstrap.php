<?php
/**
 * Punto de arranque común: lo incluye cada página en su primera línea.
 * Carga configuración, manejo de errores, sesión, base de datos y helpers.
 */
define('APP', true);

require __DIR__ . '/config.php';

// ---- Errores: nunca se muestran crudos en producción ----
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
if (!is_dir(STORAGE_DIR . '/logs')) {
    @mkdir(STORAGE_DIR . '/logs', 0775, true);
}
ini_set('error_log', STORAGE_DIR . '/logs/php-error.log');

require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/validador.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/mailer.php';
require __DIR__ . '/negocio.php';

set_exception_handler(function (Throwable $e): void {
    error_log('[' . date('c') . '] ' . get_class($e) . ': ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
    if (APP_DEBUG) {
        http_response_code(500);
        echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }
    pagina_error(500, 'Ocurrió un error inesperado', 'No pudimos completar la operación. Probá de nuevo en unos minutos.');
});

// ---- Sesión segura ----
$esHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_name('ARSHOPPING');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => APP_BASE === '' ? '/' : APP_BASE . '/',
    'secure'   => $esHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ---- Cabeceras de seguridad ----
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
