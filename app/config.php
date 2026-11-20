<?php
/**
 * Configuración de la aplicación.
 * Los valores se leen de variables de entorno o, si existe, del archivo .env
 * de la raíz del proyecto. Nunca se escriben credenciales en el código.
 */
defined('APP') or exit;

function cargar_env(string $archivo): void
{
    if (!is_readable($archivo)) {
        return;
    }
    foreach (file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
            continue;
        }
        [$clave, $valor] = array_map('trim', explode('=', $linea, 2));
        // Quita comillas envolventes: CLAVE="valor"
        if (strlen($valor) >= 2 && ($valor[0] === '"' || $valor[0] === "'") && $valor[-1] === $valor[0]) {
            $valor = substr($valor, 1, -1);
        }
        // Las variables de entorno reales del servidor tienen prioridad.
        if (getenv($clave) === false) {
            putenv("$clave=$valor");
            $_ENV[$clave] = $valor;
        }
    }
}

function env(string $clave, ?string $defecto = null): ?string
{
    $valor = getenv($clave);
    return ($valor === false || $valor === '') ? $defecto : $valor;
}

cargar_env(getenv('ENV_FILE') ?: dirname(__DIR__) . '/.env');

define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', APP_ENV === 'development');
define('APP_NAME', env('APP_NAME', 'Alto Rosario Shopping'));
define('APP_URL', rtrim(env('APP_URL', 'http://localhost:8000'), '/'));
define('APP_BASE', rtrim(parse_url(APP_URL, PHP_URL_PATH) ?? '', '/'));

define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'tp_shopping'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

define('MAIL_HOST', env('MAIL_HOST', ''));
define('MAIL_PORT', (int) env('MAIL_PORT', '587'));
define('MAIL_ENCRYPTION', strtolower(env('MAIL_ENCRYPTION', 'tls'))); // tls | ssl | none
define('MAIL_USER', env('MAIL_USER', ''));
define('MAIL_PASS', env('MAIL_PASS', ''));
define('MAIL_FROM', env('MAIL_FROM', 'no-responder@localhost'));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', APP_NAME));
define('ADMIN_EMAIL', env('ADMIN_EMAIL', MAIL_FROM));

// Reglas de categoría de clientes: cantidad de descuentos aceptados necesarios.
define('USOS_PARA_MEDIUM', (int) env('USOS_PARA_MEDIUM', '3'));
define('USOS_PARA_PREMIUM', (int) env('USOS_PARA_PREMIUM', '6'));

define('STORAGE_DIR', dirname(__DIR__) . '/storage');
date_default_timezone_set(env('APP_TZ', 'America/Argentina/Buenos_Aires'));
