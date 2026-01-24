<?php

// DEPURACIÓN:
// ini_set("display_errors", 1);
// ini_set("display_startup_errors", 1);
// error_reporting(E_ALL);

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/conexion.php";

$libroId = isset($_GET["libro_id"]) ? (int)$_GET["libro_id"] : 0;
$profesorId = (int)($_SESSION["usuario_id"] ?? 0);

if ($libroId <= 0 || $profesorId <= 0) {
    header("Location: " . APP_URL . "/index.php");
    exit;
}

try {
    // 1) ¿Está prestado?
    $sqlPrestado = "SELECT COUNT(*)
                    FROM prestamos
                    WHERE libro_id = :libro_id AND estado = 'ACTIVO'";
    $stmt = $pdo->prepare($sqlPrestado);
    $stmt->execute([":libro_id" => $libroId]);
    $estaPrestado = ((int)$stmt->fetchColumn() > 0);

    if (!$estaPrestado) {
        header("Location: " . APP_URL . "/index.php");
        exit;
    }

    // 2) Evitar reserva duplicada EN_ESPERA del mismo profesor para ese libro
    $sqlDup = "SELECT COUNT(*)
               FROM reservas
               WHERE profesor_id = :profesor_id
                 AND libro_id = :libro_id
                 AND estado = 'EN_ESPERA'";
    $stmt = $pdo->prepare($sqlDup);
    $stmt->execute([
        ":profesor_id" => $profesorId,
        ":libro_id" => $libroId
    ]);
    $yaExiste = ((int)$stmt->fetchColumn() > 0);

    if ($yaExiste) {
        header("Location: " . APP_URL . "/reservas/index.php?ya=1");
        exit;
    }

    // 3) Insertar reserva
    $sqlIns = "INSERT INTO reservas (profesor_id, libro_id, fecha, estado)
               VALUES (:profesor_id, :libro_id, NOW(), 'EN_ESPERA')";
    $stmt = $pdo->prepare($sqlIns);
    $stmt->execute([
        ":profesor_id" => $profesorId,
        ":libro_id" => $libroId
    ]);

    header("Location: " . APP_URL . "/reservas/index.php?ok=1");
    exit;

} catch (PDOException $e) {
    // Para ver el error exacto mientras depuramos
    die("ERROR PDO: " . $e->getMessage());
}
