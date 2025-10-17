<?php
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Principal - Mi Bollito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4e8d1;
      font-family: 'Poppins', sans-serif;
    }

    .navbar {
      background-color: #a0522d !important;
    }

    .card-panel {
      background-color: #f4e8d1;
      border-radius: 10px;
      padding: 30px;
      text-align: center;
      position: relative;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .icon-card {
      display: inline-block;
      width: 150px;
      height: 150px;
      background-color: #ffffff;
      border: 2px solid #ccc;
      border-radius: 10px;
      margin: 0 15px;
      transition: transform 0.3s, border-color 0.3s;
      text-decoration: none;
      color: black;
    }

    .icon-card:hover {
      transform: scale(1.05);
      border-color: #4a90e2;
    }

    .icon-card img {
      width: 70px;
      margin-top: 25px;
    }

    .icon-card p {
      margin-top: 10px;
      font-weight: 500;
    }

    .logo-bg {
      position: absolute;
      opacity: 0.08;
      width: 300px;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Mi Bollito - Panel</span>
    <a href="logout.php" class="btn btn-light btn-sm">Cerrar sesión</a>
  </div>
</nav>

<div class="container mt-5">
  <div class="card-panel">
    

    <!-- Fondo con logo -->
    <img src="img/logo_mibollito.png" alt="Logo" class="logo-bg">

    <!-- Opciones -->
    <div class="mt-4">
      <a href="inventario.php" class="icon-card">
        <img src="img/icon_inventario.png" alt="Inventario">
        <p>Inventario</p>
      </a>
      <a href="domicilios.php" class="icon-card">
        <img src="img/icon_domicilios.png" alt="Domicilios">
        <p>Domicilios</p>
      </a>
      <a href="estadisticas.php" class="icon-card">
        <img src="img/icon_estadistica.png" alt="Estadísticas">
        <p>Estadística</p>
      </a>
    </div>

    <p class="mt-3 fw-bold text-secondary">Mi Bollito</p>
  </div>
</div>

</body>
</html>
