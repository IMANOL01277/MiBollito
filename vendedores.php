<?php
require 'conexion.php';
include 'includes/header.php';

if ($_SESSION['rol'] !== 'administrador') {
  echo "<div class='alert alert-danger mt-4'>🚫 No tienes permisos para acceder aquí.</div>";
  include("includes/footer.php");
  exit();
}

// === CARGAR LISTA DE VENDEDORES Y PRODUCTOS ===
$vendedores = $conn->query("SELECT id_vendedor, nombre FROM vendedores_ambulantes ORDER BY nombre ASC");
$productos = $conn->query("SELECT id_producto, nombre, stock FROM productos ORDER BY nombre ASC");

// === REGISTRAR NUEVA ENTREGA ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_vendedor = (int) $_POST['id_vendedor'];
    $id_producto = (int) $_POST['id_producto'];
    $cantidad = (int) $_POST['cantidad'];
    $fecha = date('Y-m-d H:i:s');

    // Verificar stock disponible
    $stock_check = $conn->prepare("SELECT stock, precio FROM productos WHERE id_producto = ?");
    $stock_check->bind_param("i", $id_producto);
    $stock_check->execute();
    $stock_result = $stock_check->get_result()->fetch_assoc();

    if ($stock_result && $stock_result['stock'] >= $cantidad) {
        // Insertar registro de entrega
        $stmt = $conn->prepare("INSERT INTO entregas_vendedores (id_vendedor, id_producto, cantidad, fecha_entrega)
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $id_vendedor, $id_producto, $cantidad, $fecha);
        $stmt->execute();

        // Descontar del inventario
        $nuevo_stock = $stock_result['stock'] - $cantidad;
        $update_stock = $conn->prepare("UPDATE productos SET stock = ? WHERE id_producto = ?");
        $update_stock->bind_param("ii", $nuevo_stock, $id_producto);
        $update_stock->execute();

        // Registrar movimiento de salida en movimientos_inventario
        $tipo = 'salida';
        $precio_unitario = $stock_result['precio'];
        $total = $precio_unitario * $cantidad;
        $desc = "Entrega a vendedor";
        $mov = $conn->prepare("INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, total, fecha_movimiento, descripcion)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $mov->bind_param("isiddss", $id_producto, $tipo, $cantidad, $precio_unitario, $total, $fecha, $desc);
        $mov->execute();

        $mensaje = "Entrega registrada correctamente.";
    } else {
        $error = "Stock insuficiente para realizar la entrega.";
    }
}

// === CONSULTAR ENTREGAS REGISTRADAS ===
$entregas = $conn->query("
SELECT e.id_entrega, v.nombre AS vendedor, p.nombre AS producto, e.cantidad, e.fecha_entrega
FROM entregas_vendedores e
JOIN vendedores_ambulantes v ON v.id_vendedor = e.id_vendedor
JOIN productos p ON p.id_producto = e.id_producto
ORDER BY e.fecha_entrega DESC
");
?>

<!-- Contenido -->
<div class="content">
  <div class="container">
    <h3 class="mb-4">🚚 Registro de entregas a vendedores</h3>

    <?php if (isset($mensaje)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
    <?php elseif (isset($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card p-4 mb-4">
      <form method="POST">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Vendedor</label>
            <select name="id_vendedor" class="form-select" required>
              <option value="">Seleccione un vendedor</option>
              <?php while($v = $vendedores->fetch_assoc()): ?>
                <option value="<?= $v['id_vendedor'] ?>"><?= htmlspecialchars($v['nombre']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Producto</label>
            <select name="id_producto" class="form-select" required>
              <option value="">Seleccione un producto</option>
              <?php while($p = $productos->fetch_assoc()): ?>
                <option value="<?= $p['id_producto'] ?>">
                  <?= htmlspecialchars($p['nombre']) ?> (Stock: <?= $p['stock'] ?>)
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" min="1" class="form-control" required>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Registrar</button>
          </div>
        </div>
      </form>
    </div>

    <div class="card p-4">
      <h5>📋 Entregas registradas</h5>
      <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Vendedor</th>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($entregas->num_rows == 0): ?>
              <tr><td colspan="5" class="text-center">No hay entregas registradas.</td></tr>
            <?php else: $i=1; while($row = $entregas->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['vendedor']) ?></td>
                <td><?= htmlspecialchars($row['producto']) ?></td>
                <td><?= $row['cantidad'] ?></td>
                <td><?= $row['fecha_entrega'] ?></td>
              </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
