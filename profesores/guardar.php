<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . APP_URL . "/profesores/index.php");
    exit;
}

$apellido1 = trim($_POST["apellido1"] ?? "");
$apellido2 = trim($_POST["apellido2"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");
$perfil = ($_POST["perfil"] ?? "PROFESOR") === "ADMIN" ? "ADMIN" : "PROFESOR";
$avatar = trim($_POST["avatar"] ?? "");
$estado = isset($_POST["estado"]) && $_POST["estado"] === "0" ? 0 : 1;

// Validación mínima
if ($apellido1 === "" || $apellido2 === "" || $nombre === "" || $email === "" || $password === "") {
    header("Location: " . APP_URL . "/profesores/crear.php?error=1");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: " . APP_URL . "/profesores/crear.php?error=1");
    exit;
}

try {
    $sql = "INSERT INTO profesores (apellido1, apellido2, nombre, email, password, perfil, avatar, estado)
            VALUES (:apellido1, :apellido2, :nombre, :email, :password, :perfil, :avatar, :estado)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":apellido1" => $apellido1,
        ":apellido2" => $apellido2,
        ":nombre" => $nombre,
        ":email" => $email,
        ":password" => $password,
        ":perfil" => $perfil,
        ":avatar" => ($avatar !== "" ? $avatar : null),
        ":estado" => $estado
    ]);

    header("Location: " . APP_URL . "/profesores/index.php?ok=1");
    exit;

} catch (PDOException $e) {
    // email duplicado u otro error
    header("Location: " . APP_URL . "/profesores/crear.php?error=1");
    exit;
}
