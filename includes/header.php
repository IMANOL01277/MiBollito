<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
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
  <title>Mi Bollito - Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4e8d1;
      font-family: Arial, sans-serif;
      margin: 0;
      display: flex;
    }
    .sidebar {
      width: 240px;
      background-color: #a0522d;
      height: 100vh;
      color: #fff;
      position: fixed;
      top: 0; left: 0;
      padding: 1rem;
      display: flex;
      flex-direction: column;
    }
    .sidebar h4 {
      font-weight: bold;
      margin-bottom: 2rem;
      text-align: center;
    }
    .sidebar a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 10px 15px;
      border-radius: 8px;
      margin-bottom: 5px;
      transition: background 0.2s;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #8b4513;
    }
    .content {
      margin-left: 260px;
      padding: 20px;
      flex: 1;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h4>Mi Bollito</h4>

  <a href="panel.php"><i class="bi bi-house-door"></i> Inicio</a>
  <a href="inventario.php"><i class="bi bi-box-seam"></i> Inventario</a>
  <a href="estadisticas.php"><i class="bi bi-graph-up"></i> Estadísticas</a>
  <a href="domicilios.php"><i class="bi bi-bicycle"></i> Domicilios</a>
  <a href="estados.php"><i class="bi bi-tags"></i> Estados</a>

  <?php if ($_SESSION['rol'] === 'administrador'): ?>
    <a href="vendedores.php"><i class="bi bi-truck"></i> Vendedores</a>
    <a href="usuarios.php"><i class="bi bi-people"></i> Usuarios</a>
  <?php endif; ?>

  <div class="mt-auto">
    <hr class="text-light">
    <p class="small mb-1">👤 <?= htmlspecialchars($_SESSION['nombre']) ?></p>
    <p class="small text-light"><?= $_SESSION['rol'] ?></p>
    <a href="logout.php" class="btn btn-light btn-sm w-100 mt-2">
      <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
  </div>
</div>

<div class="content">
