<?php

function loadEnv($path) {
    if (!file_exists($path)) {
        return [];
    }
    $env = parse_ini_file($path);
    return is_array($env) ? $env : [];
}

function getPdo() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $env = loadEnv(__DIR__ . '/../.env');

    $host = $env['DB_HOST'] ?? '';
    $port = $env['DB_PORT'] ?? '3306';
    $name = $env['DB_NAME'] ?? '';
    $user = $env['DB_USER'] ?? '';
    $pass = $env['DB_PASS'] ?? '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}
