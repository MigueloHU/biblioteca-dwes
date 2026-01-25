<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . APP_URL . "/index.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);

$isbn = trim($_POST["isbn"] ?? "");
$ejemplar = (int)($_POST["ejemplar"] ?? 1);
$portada = trim($_POST["portada"] ?? "");
$titulo = trim($_POST["titulo"] ?? "");
$autor = trim($_POST["autor"] ?? "");
$genero = trim($_POST["genero"] ?? "");
$fecha_publicacion = trim($_POST["fecha_publicacion"] ?? "");
$editorial = trim($_POST["editorial"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

$precio = null;
if (isset($_POST["precio"]) && $_POST["precio"] !== "") {
    $precio = (float)$_POST["precio"];
}

if ($id <= 0 || $isbn === "" || $titulo === "" || $autor === "" || $ejemplar <= 0) {
    header("Location: " . APP_URL . "/libros/editar.php?id=" . urlencode((string)$id) . "&error=1");
    exit;
}

// Validación ISBN simple
if (!preg_match("/^[0-9A-Za-z\-]+$/", $isbn)) {
    header("Location: " . APP_URL . "/libros/editar.php?id=" . urlencode((string)$id) . "&error=1");
    exit;
}

// Validación fecha mm/aaaa (si se ha introducido)
if ($fecha_publicacion !== "") {
    if (!preg_match("/^(0[1-9]|1[0-2])\/\d{4}$/", $fecha_publicacion)) {
        header("Location: " . APP_URL . "/libros/editar.php?id=" . urlencode((string)$id) . "&error=1");
        exit;
    }
}

try {
    $sql = "UPDATE libros
            SET isbn = :isbn,
                ejemplar = :ejemplar,
                portada = :portada,
                titulo = :titulo,
                autor = :autor,
                genero = :genero,
                fecha_publicacion = :fecha_publicacion,
                editorial = :editorial,
                descripcion = :descripcion,
                precio = :precio
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":isbn" => $isbn,
        ":ejemplar" => $ejemplar,
        ":portada" => ($portada !== "" ? $portada : null),
        ":titulo" => $titulo,
        ":autor" => $autor,
        ":genero" => ($genero !== "" ? $genero : null),
        ":fecha_publicacion" => ($fecha_publicacion !== "" ? $fecha_publicacion : null),
        ":editorial" => ($editorial !== "" ? $editorial : null),
        ":descripcion" => ($descripcion !== "" ? $descripcion : null),
        ":precio" => $precio,
        ":id" => $id
    ]);

    header("Location: " . APP_URL . "/libros/editar.php?id=" . urlencode((string)$id) . "&ok=1");
    exit;

} catch (PDOException $e) {
    header("Location: " . APP_URL . "/libros/editar.php?id=" . urlencode((string)$id) . "&error=1");
    exit;
}
