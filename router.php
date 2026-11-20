<?php
/**
 * Router SOLO para el servidor embebido de PHP (desarrollo local):
 *   php -S localhost:8000 router.php
 * Reproduce lo que hace el .htaccess en Apache: bloquea carpetas internas y
 * archivos sensibles, y muestra la página 404 propia.
 */
$ruta = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

if (preg_match('#^/(app|storage|database)(/|$)#', $ruta) || preg_match('#/\.|\.(sql|md|log|ini|example)$#i', $ruta)) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return true;
}

$archivo = realpath(__DIR__ . $ruta);
if ($ruta === '/' || ($archivo && is_dir($archivo) && is_file($archivo . '/index.php'))) {
    return false; // index.php
}
if ($archivo && str_starts_with($archivo, __DIR__) && is_file($archivo)) {
    return false; // archivo existente: lo sirve el servidor embebido
}
http_response_code(404);
require __DIR__ . '/404.php';
return true;
