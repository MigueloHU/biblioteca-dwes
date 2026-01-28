<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/conexion.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . APP_URL . "/login.php");
    exit;
}

$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

if ($email === "" || $password === "") {
    header("Location: " . APP_URL . "/login.php?error=1");
    exit;
}

$sql = "SELECT id, email, password, perfil, estado, nombre
        FROM profesores
        WHERE email = :email
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([":email" => $email]);
$user = $stmt->fetch();

if ($user && (int)$user["estado"] === 1 && $password === $user["password"]) {

    $_SESSION["usuario_id"] = $user["id"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["perfil"] = $user["perfil"];
    $_SESSION["nombre"] = $user["nombre"];

    header("Location: " . APP_URL . "/index.php");
    exit;
}

header("Location: " . APP_URL . "/login.php?error=1");
exit;
