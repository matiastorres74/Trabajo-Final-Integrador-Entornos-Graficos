<?php
/**
 * Conexión PDO única. Todas las consultas del sistema usan sentencias preparadas.
 */
defined('APP') or exit;

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec("SET time_zone = '" . (new DateTime())->format('P') . "'");
    }
    return $pdo;
}

/** Ejecuta una consulta preparada y devuelve el statement. */
function q(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Ejecuta varias escrituras como una sola unidad: o se guardan todas o ninguna.
 * Si $fn lanza una excepción, se deshace todo y la excepción sigue su camino.
 */
function transaccion(callable $fn): mixed
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $resultado = $fn();
        $pdo->commit();
        return $resultado;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function fila(string $sql, array $params = []): ?array
{
    $r = q($sql, $params)->fetch();
    return $r === false ? null : $r;
}

function filas(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

function valor(string $sql, array $params = []): mixed
{
    return q($sql, $params)->fetchColumn();
}
