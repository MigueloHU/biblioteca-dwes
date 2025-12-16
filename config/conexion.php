<?php
require_once __DIR__ . "/config.php";

function getConexion(): PDO
{
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    try {
        $cn = new PDO($dsn, DB_USER, DB_PASS);
        $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $cn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $cn;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
