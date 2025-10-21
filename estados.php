<?php
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.php");
  exit();
}

require 'conexion.php';

// === CREAR NUEVO ESTADO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create') {
  $id_producto = (int) $_POST['id_producto'];
  $nombre = trim($_POST['nombre']);
  $estado_producto = $_POST['estado_producto'];

  $sql = "INSERT INTO estado (id_producto, nombre, estado_producto) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iss", $id_producto, $nombre, $estado_producto);
  $ok = $stmt->execute();
  $stmt->close();

  header("Location: estados.php?msg=" . ($ok ? "created" : "error"));
  exit();
}

// === EDITAR ESTADO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
  $id_estado = (int) $_POST['id_estado'];
  $nombre = trim($_POST['nombre']);
  $estado_producto = $_POST['estado_producto'];

  $sql = "UPDATE estado SET nombre = ?, estado_producto = ? WHERE id_estado = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssi", $nombre, $estado_producto, $id_estado);
  $ok = $stmt->execute();
  $stmt->close();

  header("Location: estados.php?msg=" . ($ok ? "updated" : "error"));
  exit();
}

// === ELIMINAR ESTADO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
  $id_estado = (int) $_POST['id_estado'];
  $sql = "DELETE FROM estado WHERE id_estado = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id_estado);
  $ok = $stmt->execute();
  $stmt->close();

  header("Location: estados.php?msg=" . ($ok ? "deleted" : "error"));
  exit();
}

// === LISTAR ESTADOS ===
$list_sql = "
  SELECT e.id_estado, p.nombre AS producto, e.nombre, e.estado_producto, e.fecha_registro
  FROM estado e
  LEFT JOIN productos p ON e.id_producto = p.id_producto
  ORDER BY e.id_estado DESC
";
$result = $conn->query($list_sql);

// Obtener lista de productos para los select
$productos = $conn->query("SELECT id_producto, nombre FROM productos ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estados - Mi Bollito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f4e8d1; font-family: Arial, sans-serif; }
    .card-panel { background-color: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .table thead { background-color: #a0522d; color: white; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark" style="background-color:#a0522d;">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Mi Bollito - Estados de Productos</span>
    <div>
      <a href="index.php" class="btn btn-light btn-sm me-2">Inicio</a>
      <a href="inventario.php" class="btn btn-light btn-sm me-2">Inventario</a>
      <a href="logout.php" class="btn btn-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="card-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Estados de Productos</h4>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreate">+ Nuevo estado</button>
    </div>

    <!-- Mensajes -->
    <?php if (isset($_GET['msg'])): ?>
      <?php
        $mensajes = [
          'created' => 'Estado registrado correctamente.',
          'updated' => 'Estado actualizado correctamente.',
          'deleted' => 'Estado eliminado correctamente.',
          'error' => 'Ocurrió un error en la operación.'
        ];
      ?>
      <div class="alert alert-<?= $_GET['msg'] === 'error' ? 'danger' : 'success' ?>">
        <?= $mensajes[$_GET['msg']] ?? 'Operación realizada.' ?>
      </div>
    <?php endif; ?>

    <!-- TABLA -->
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center">
        <thead>
          <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Fecha de Registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="6">No hay estados registrados.</td></tr>
          <?php else: $i=1; while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['producto'] ?? 'Sin producto') ?></td>
              <td><?= htmlspecialchars($row['nombre']) ?></td>
              <td>
                <span class="badge 
                  <?= $row['estado_producto'] === 'Bueno' ? 'bg-success' : 
                      ($row['estado_producto'] === 'Por caducar' ? 'bg-warning text-dark' : 'bg-danger') ?>">
                  <?= $row['estado_producto'] ?>
                </span>
              </td>
              <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
              <td>
                <button class="btn btn-primary btn-sm btn-edit" 
                  data-id="<?= $row['id_estado'] ?>"
                  data-nombre="<?= htmlspecialchars($row['nombre'], ENT_QUOTES) ?>"
                  data-estado="<?= $row['estado_producto'] ?>"
                  data-bs-toggle="modal" data-bs-target="#modalEdit">Editar</button>

                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este estado?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id_estado" value="<?= $row['id_estado'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL CREAR -->
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <input type="hidden" name="action" value="create">
      <div class="modal-header">
        <h5 class="modal-title">Registrar nuevo estado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Producto</label>
          <select name="id_producto" class="form-select" required>
            <option value="">Seleccione un producto</option>
            <?php while($p = $productos->fetch_assoc()): ?>
              <option value="<?= $p['id_producto'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Nombre del estado</label>
          <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Estado</label>
          <select name="estado_producto" class="form-select" required>
            <option value="Bueno">Bueno</option>
            <option value="Por caducar">Por caducar</option>
            <option value="Caducado">Caducado</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-success" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <input type="hidden" name="action" value="update">
      <input type="hidden" id="edit_id" name="id_estado">
      <div class="modal-header">
        <h5 class="modal-title">Editar estado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nombre del estado</label>
          <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Estado</label>
          <select id="edit_estado" name="estado_producto" class="form-select" required>
            <option value="Bueno">Bueno</option>
            <option value="Por caducar">Por caducar</option>
            <option value="Caducado">Caducado</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-primary" type="submit">Actualizar</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Rellenar modal de edición
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
      document.getElementById('edit_id').value = this.dataset.id;
      document.getElementById('edit_nombre').value = this.dataset.nombre;
      document.getElementById('edit_estado').value = this.dataset.estado;
    });
  });
</script>
</body>
</html>
