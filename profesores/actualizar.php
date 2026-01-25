<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . APP_URL . "/profesores/index.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);

$apellido1 = trim($_POST["apellido1"] ?? "");
$apellido2 = trim($_POST["apellido2"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");
$perfil = ($_POST["perfil"] ?? "PROFESOR") === "ADMIN" ? "ADMIN" : "PROFESOR";
$avatar = trim($_POST["avatar"] ?? "");
$estado = isset($_POST["estado"]) && $_POST["estado"] === "0" ? 0 : 1;

if ($id <= 0 || $apellido1 === "" || $apellido2 === "" || $nombre === "" || $email === "" || $password === "") {
    header("Location: " . APP_URL . "/profesores/editar.php?id=" . urlencode((string)$id) . "&error=1");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: " . APP_URL . "/profesores/editar.php?id=" . urlencode((string)$id) . "&error=1");
    exit;
}

// Admin id=1 no se desactiva nunca
if ($id === 1) {
    $estado = 1;
}

try {
    $sql = "UPDATE profesores
            SET apellido1 = :apellido1,
                apellido2 = :apellido2,
                nombre = :nombre,
                email = :email,
                password = :password,
                perfil = :perfil,
                avatar = :avatar,
                estado = :estado
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":apellido1" => $apellido1,
        ":apellido2" => $apellido2,
        ":nombre" => $nombre,
        ":email" => $email,
        ":password" => $password,
        ":perfil" => $perfil,
        ":avatar" => ($avatar !== "" ? $avatar : null),
        ":estado" => $estado,
        ":id" => $id
    ]);

    header("Location: " . APP_URL . "/profesores/editar.php?id=" . urlencode((string)$id) . "&ok=1");
    exit;

} catch (PDOException $e) {
    header("Location: " . APP_URL . "/profesores/editar.php?id=" . urlencode((string)$id) . "&error=1");
    exit;
}
