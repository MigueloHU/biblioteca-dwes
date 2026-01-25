<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . APP_URL . "/libros/crear.php");
    exit;
}

// Recogida de datos (sanear/trimear)
$isbn = trim($_POST["isbn"] ?? "");
$ejemplar = (int)($_POST["ejemplar"] ?? 1);
$portada = trim($_POST["portada"] ?? "");
$titulo = trim($_POST["titulo"] ?? "");
$autor = trim($_POST["autor"] ?? "");
$genero = trim($_POST["genero"] ?? "");
$fecha_publicacion = trim($_POST["fecha_publicacion"] ?? "");
$editorial = trim($_POST["editorial"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

// Precio: si viene vacío -> NULL
$precio = null;
if (isset($_POST["precio"]) && $_POST["precio"] !== "") {
    $precio = (float)$_POST["precio"];
}

// Validaciones mínimas
if ($isbn === "" || $titulo === "" || $autor === "" || $ejemplar <= 0) {
    header("Location: " . APP_URL . "/libros/crear.php?error=1");
    exit;
}

// Validación ISBN (simple): solo permitir letras/números/guiones (opcional)
if (!preg_match("/^[0-9A-Za-z\-]+$/", $isbn)) {
    header("Location: " . APP_URL . "/libros/crear.php?error=1");
    exit;
}

// Validación fecha mm/aaaa (si se ha introducido)
if ($fecha_publicacion !== "") {
    if (!preg_match("/^(0[1-9]|1[0-2])\/\d{4}$/", $fecha_publicacion)) {
        header("Location: " . APP_URL . "/libros/crear.php?error=1");
        exit;
    }
}

try {
    $sql = "INSERT INTO libros
            (isbn, ejemplar, portada, titulo, autor, genero, fecha_publicacion, editorial, descripcion, precio)
            VALUES
            (:isbn, :ejemplar, :portada, :titulo, :autor, :genero, :fecha_publicacion, :editorial, :descripcion, :precio)";

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
        ":precio" => $precio
    ]);

    header("Location: " . APP_URL . "/libros/crear.php?ok=1");
    exit;

} catch (PDOException $e) {
    // Por ejemplo, si se repite (isbn, ejemplar) por la UNIQUE KEY
    header("Location: " . APP_URL . "/libros/crear.php?error=1");
    exit;
}
