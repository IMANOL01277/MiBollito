<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}
require 'conexion.php';

// === ACTUALIZAR O CREAR ESTADO DE PRODUCTO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'], $_POST['estado_producto'])) {
    $id_producto = (int) $_POST['id_producto'];
    $estado_producto = $_POST['estado_producto'];

    $sql = "
    INSERT INTO estado (id_producto, nombre, estado_producto, fecha_registro)
    VALUES (?, '', ?, CURRENT_TIMESTAMP)
    ON DUPLICATE KEY UPDATE
        estado_producto = VALUES(estado_producto),
        fecha_registro = CURRENT_TIMESTAMP;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id_producto, $estado_producto);
    $stmt->execute();
    $stmt->close();

    $mensaje = "✅ Estado del producto actualizado correctamente.";
}

// === OBTENER PRODUCTOS Y ESTADOS ===
$query = "
SELECT 
    p.id_producto, 
    p.nombre AS producto,
    e.estado_producto,
    e.fecha_registro
FROM productos p
LEFT JOIN estado e ON e.id_producto = p.id_producto
ORDER BY p.id_producto ASC
";
$result = $conn->query($query);
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
  <div class="card p-4 card-style">
    <h3 class="mb-4">📦 Estado de los Productos</h3>

    <?php if (isset($mensaje)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th style="width: 70px;">#</th>
            <th>Nombre del Producto</th>
            <th style="width: 200px;">Estado</th>
            <th style="width: 180px;">Última Actualización</th>
            <th style="width: 150px;">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="5" class="text-center">No hay productos registrados.</td></tr>
          <?php else: $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['producto']) ?></td>
              <td>
                <form method="POST" class="d-flex">
                  <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">
                  <select name="estado_producto" class="form-select form-select-sm me-2">
                    <?php 
                      $estados = ['En proceso', 'Finalizado', 'Detenido', 'En espera'];
                      foreach ($estados as $estado) {
                        $selected = ($row['estado_producto'] === $estado) ? 'selected' : '';
                        echo "<option value='$estado' $selected>$estado</option>";
                      }
                    ?>
                  </select>
                  <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                </form>
              </td>
              <td><?= $row['fecha_registro'] ? $row['fecha_registro'] : '-' ?></td>
              <td>
                <?php
                $color = match ($row['estado_producto']) {
                    'Finalizado' => 'success',
                    'En proceso' => 'warning',
                    'Detenido' => 'danger',
                    'En espera' => 'secondary',
                    default => 'light'
                };
                ?>
                <span class="badge bg-<?= $color ?>"><?= htmlspecialchars($row['estado_producto'] ?? 'Sin estado') ?></span>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
