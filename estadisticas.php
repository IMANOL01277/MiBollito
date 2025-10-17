<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// === CONSULTA DE LOS ÚLTIMOS 30 DÍAS ===
$sql = "
SELECT tipo, SUM(cantidad) AS total_cantidad, SUM(total) AS total_valor
FROM movimientos_inventario
WHERE fecha_movimiento >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY tipo;
";

$resumen = $conn->query($sql);
$compras = $produccion = $ventas = 0;

while ($r = $resumen->fetch_assoc()) {
    if ($r['tipo'] === 'entrada') $compras = $r['total_valor'];
    if ($r['tipo'] === 'produccion') $produccion = $r['total_valor'];
    if ($r['tipo'] === 'venta' || $r['tipo'] === 'salida') $ventas = $r['total_valor'];
}

$ganancia = $ventas - $compras;

// === DETALLE DE MOVIMIENTOS ===
$detalles = $conn->query("
SELECT m.id_movimiento, p.nombre AS producto, m.tipo, m.cantidad, m.precio_unitario, m.total, m.fecha_movimiento, m.descripcion
FROM movimientos_inventario m
LEFT JOIN productos p ON p.id_producto = m.id_producto
WHERE m.fecha_movimiento >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
ORDER BY m.fecha_movimiento DESC;
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>Estadísticas - Mi Bollito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f4e8d1; font-family: Arial, sans-serif; }
    .card-panel { background-color: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
  </style>
</head>
<body>
<nav class="navbar navbar-dark" style="background-color:#a0522d;">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Mi Bollito - Estadísticas</span>
    <div>
      <a href="index.php" class="btn btn-light btn-sm me-2">Inicio</a>
      <a href="inventario.php" class="btn btn-light btn-sm me-2">Inventario</a>
      <a href="logout.php" class="btn btn-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="card-panel">
    <h4>📊 Reporte últimos 30 días</h4>
    <div class="row text-center mt-3">
      <div class="col-md-3"><h6>Compras (Materia Prima)</h6><h3 class="text-danger">$<?= number_format($compras,2,',','.') ?></h3></div>
      <div class="col-md-3"><h6>Producción</h6><h3 class="text-warning">$<?= number_format($produccion,2,',','.') ?></h3></div>
      <div class="col-md-3"><h6>Ventas</h6><h3 class="text-success">$<?= number_format($ventas,2,',','.') ?></h3></div>
      <div class="col-md-3"><h6>Ganancia Neta</h6><h3 class="text-primary">$<?= number_format($ganancia,2,',','.') ?></h3></div>
    </div>

    <canvas id="grafico" height="100" class="mt-4"></canvas>

    <hr>
    <h5>📋 Detalle de movimientos</h5>
    <table class="table table-bordered table-striped mt-3">
      <thead><tr><th>#</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Total</th><th>Fecha</th><th>Descripción</th></tr></thead>
      <tbody>
      <?php if ($detalles->num_rows == 0): ?>
        <tr><td colspan="7" class="text-center">Sin movimientos recientes</td></tr>
      <?php else: $i=1; while($r=$detalles->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($r['producto']) ?></td>
          <td><span class="badge bg-<?= $r['tipo']=='entrada'?'secondary':($r['tipo']=='produccion'?'warning':($r['tipo']=='venta'?'success':'danger')) ?>"><?= ucfirst($r['tipo']) ?></span></td>
          <td><?= $r['cantidad'] ?></td>
          <td>$<?= number_format($r['total'],2,',','.') ?></td>
          <td><?= $r['fecha_movimiento'] ?></td>
          <td><?= htmlspecialchars($r['descripcion']) ?></td>
        </tr>
      <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
const ctx = document.getElementById('grafico');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Compras', 'Producción', 'Ventas', 'Ganancia Neta'],
    datasets: [{
      data: [<?= $compras ?>, <?= $produccion ?>, <?= $ventas ?>, <?= $ganancia ?>],
      backgroundColor: ['#dc3545','#ffc107','#28a745','#007bff']
    }]
  },
  options: {plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
</script>
</body>
</html>
