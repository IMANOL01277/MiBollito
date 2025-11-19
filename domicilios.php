<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// === CREAR DOMICILIO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create') {
    $conductor = trim($_POST['conductor_responsable']);
    $matricula = trim($_POST['matricula_vehiculo']);
    $observaciones = trim($_POST['observaciones']);
    $id_producto = (int) $_POST['id_producto'];
    $cantidad = (int) $_POST['cantidad'];

    // 🔹 Obtener datos del producto seleccionado
    $producto_q = $conn->prepare("SELECT nombre, precio FROM productos WHERE id_producto = ?");
    $producto_q->bind_param("i", $id_producto);
    $producto_q->execute();
    $producto_data = $producto_q->get_result()->fetch_assoc();
    $producto_q->close();

    if ($producto_data && $cantidad > 0) {
        $nombre_producto = $producto_data['nombre'];
        $precio_unitario = $producto_data['precio'];
        $total = $precio_unitario * $cantidad;

        // 🔹 Registrar el domicilio
        $sql = "INSERT INTO domicilios (conductor_responsable, matricula_vehiculo, observaciones, producto) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $conductor, $matricula, $observaciones, $nombre_producto);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            // 🔹 Descontar stock
            $update = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
            $update->bind_param("ii", $cantidad, $id_producto);
            $update->execute();
            $update->close();

            // 🔹 Registrar movimiento en inventario (para estadísticas)
            $mov = $conn->prepare("INSERT INTO movimientos_inventario 
                (id_producto, tipo, cantidad, precio_unitario, total, descripcion)
                VALUES (?, 'salida', ?, ?, ?, ?)");
            $desc = "Domicilio entregado por $conductor";
            $mov->bind_param("iidss", $id_producto, $cantidad, $precio_unitario, $total, $desc);
            $mov->execute();
            $mov->close();
        }

        header("Location: domicilios.php?msg=" . ($ok ? "created" : "error"));
        exit();
    } else {
        header("Location: domicilios.php?msg=invalid");
        exit();
    }
}

// === ELIMINAR DOMICILIO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $id = (int) $_POST['id_domicilio'];
    $stmt = $conn->prepare("DELETE FROM domicilios WHERE id_domicilio = ?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();

    header("Location: domicilios.php?msg=" . ($ok ? "deleted" : "error"));
    exit();
}

// === CONSULTA PRINCIPAL ===
$result = $conn->query("SELECT * FROM domicilios ORDER BY fecha_registro DESC");

// === CARGAR PRODUCTOS ===
$productos = $conn->query("SELECT id_producto, nombre, stock FROM productos ORDER BY nombre ASC");

// === INCLUIR HEADER ===
include 'includes/header.php';
?>

<div class="container-fluid mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold">🛵 Gestión de Domicilios</h4>
        <button class="btn btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalCreate">
          + Nuevo Domicilio
        </button>
      </div>

      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?= in_array($_GET['msg'], ['error','invalid']) ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
          <?php
            switch($_GET['msg']) {
              case 'created': echo '✅ Domicilio registrado correctamente.'; break;
              case 'deleted': echo '🗑️ Domicilio eliminado.'; break;
              case 'invalid': echo '⚠️ Producto inválido o cantidad incorrecta.'; break;
              default: echo '❌ Error al procesar la operación.';
            }
          ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-striped align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Conductor</th>
              <th>Matrícula</th>
              <th>Producto</th>
              <th>Observaciones</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows === 0): ?>
              <tr><td colspan="7" class="text-center text-muted py-3">No hay domicilios registrados.</td></tr>
            <?php else: $i=1; while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['conductor_responsable']) ?></td>
                <td><?= htmlspecialchars($row['matricula_vehiculo']) ?></td>
                <td><?= htmlspecialchars($row['producto']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['observaciones'])) ?></td>
                <td><?= $row['fecha_registro'] ?></td>
                <td>
                  <form method="POST" onsubmit="return confirm('¿Eliminar domicilio?');" style="display:inline-block;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_domicilio" value="<?= $row['id_domicilio'] ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" class="modal-content">
      <input type="hidden" name="action" value="create">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Registrar nuevo domicilio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Conductor responsable</label>
          <input type="text" name="conductor_responsable" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Matrícula del vehículo</label>
          <input type="text" name="matricula_vehiculo" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Producto</label>
          <select name="id_producto" class="form-select" required>
            <option value="">Seleccione un producto</option>
            <?php while($p = $productos->fetch_assoc()): ?>
              <option value="<?= $p['id_producto'] ?>"><?= htmlspecialchars($p['nombre']) ?> (Stock: <?= $p['stock'] ?>)</option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Cantidad</label>
          <input type="number" name="cantidad" class="form-control" min="1" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Observaciones</label>
          <textarea name="observaciones" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
      </div>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
