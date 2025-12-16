<?php
// includes/header.php
require_once __DIR__ . "/../config/config.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($titulo) ? $titulo : APP_NAME; ?></title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php
$navbarPath = __DIR__ . "/navbar.php";
if (file_exists($navbarPath)) {
    require_once $navbarPath;
}
?>

<main class="container py-5">
