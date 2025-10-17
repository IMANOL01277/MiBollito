<?php
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.php");
  exit();
}
require 'conexion.php';

// === CREAR PRODUCTO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
  $nombre = trim($_POST['nombre']);
  $descripcion = trim($_POST['descripcion']);
  $precio = (float) $_POST['precio'];
  $stock = (int) $_POST['stock'];
  $tipo_producto = $_POST['tipo_producto']; // 'materia_prima' o 'produccion'
  $id_proveedor = !empty($_POST['id_proveedor']) ? (int) $_POST['id_proveedor'] : null;

  // Guardar producto
  $sql = $id_proveedor === null ?
    "INSERT INTO productos (nombre, descripcion, precio, stock, id_proveedor) VALUES (?, ?, ?, ?, NULL)" :
    "INSERT INTO productos (nombre, descripcion, precio, stock, id_proveedor) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  if ($id_proveedor === null)
    $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);
  else
    $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $id_proveedor);
  $ok = $stmt->execute();
  $nuevo_id = $conn->insert_id;
  $stmt->close();

  // Registrar movimiento inicial
  if ($ok) {
    $tipo_mov = $tipo_producto === 'materia_prima' ? 'entrada' : 'produccion';
    $desc = $tipo_producto === 'materia_prima' ? 'Compra inicial de materia prima' : 'Producción inicial';
    $total = $precio * $stock;
    $mov = $conn->prepare("INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, total, descripcion, fecha_movimiento)
                           VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $mov->bind_param("isidds", $nuevo_id, $tipo_mov, $stock, $precio, $total, $desc);
    $mov->execute();
    $mov->close();
  }

  header("Location: inventario.php?msg=" . ($ok ? "created" : "errcreate"));
  exit();
}

// === ACTUALIZAR PRODUCTO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
  $id = (int) $_POST['id_producto'];
  $nombre = trim($_POST['nombre']);
  $descripcion = trim($_POST['descripcion']);
  $precio = (float) $_POST['precio'];
  $stock_nuevo = (int) $_POST['stock'];
  $id_proveedor = !empty($_POST['id_proveedor']) ? (int) $_POST['id_proveedor'] : null;

  // Obtener stock actual
  $res = $conn->prepare("SELECT stock, precio FROM productos WHERE id_producto = ?");
  $res->bind_param("i", $id);
  $res->execute();
  $res->bind_result($stock_actual, $precio_actual);
  $res->fetch();
  $res->close();

  // Actualizar producto
  $sql = $id_proveedor === null ?
    "UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, id_proveedor=NULL WHERE id_producto=?" :
    "UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, id_proveedor=? WHERE id_producto=?";
  $stmt = $conn->prepare($sql);
  if ($id_proveedor === null)
    $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock_nuevo, $id);
  else
    $stmt->bind_param("ssdiii", $nombre, $descripcion, $precio, $stock_nuevo, $id_proveedor, $id);
  $ok = $stmt->execute();
  $stmt->close();

  // Registrar movimiento si el stock cambió
  $diferencia = $stock_nuevo - $stock_actual;
  if ($diferencia != 0) {
    $tipo = $diferencia > 0 ? 'entrada' : 'salida';
    $cantidad = abs($diferencia);
    $precio_unitario = $precio > 0 ? $precio : $precio_actual;
    $total = $cantidad * $precio_unitario;
    $descripcion_mov = "Ajuste manual de stock desde Inventario";

    $mov = $conn->prepare("INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, total, descripcion, fecha_movimiento)
                           VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $mov->bind_param("isidds", $id, $tipo, $cantidad, $precio_unitario, $total, $descripcion_mov);
    $mov->execute();
    $mov->close();
  }

  header("Location: inventario.php?msg=" . ($ok ? "updated" : "errupdate"));
  exit();
}

// === ELIMINAR PRODUCTO ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
  $id = (int) $_POST['id_producto'];

  // Primero eliminar los movimientos relacionados
  $delMov = $conn->prepare("DELETE FROM movimientos_inventario WHERE id_producto = ?");
  $delMov->bind_param("i", $id);
  $delMov->execute();
  $delMov->close();

  // Luego eliminar el producto
  $stmt = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
  $stmt->bind_param("i", $id);
  $ok = $stmt->execute();
  $stmt->close();

  header("Location: inventario.php?msg=" . ($ok ? "deleted" : "errdelete"));
  exit();
}

// === LISTAR PRODUCTOS ===
$list = $conn->query("SELECT id_producto, nombre, descripcion, precio, stock, id_proveedor, fecha_registro FROM productos ORDER BY id_producto DESC");
$productos = $list->fetch_all(MYSQLI_ASSOC);
$list->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>Inventario - Mi Bollito</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f4e8d1; font-family: Arial, sans-serif; }
    .card-panel { background-color: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .table-wrap { overflow-x:auto; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-dark" style="background-color:#a0522d;">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1">Mi Bollito - Inventario</span>
      <div>
        <a href="index.php" class="btn btn-light btn-sm me-2">Inicio</a>
        <a href="estadisticas.php" class="btn btn-light btn-sm me-2">Estadísticas</a>
        <a href="logout.php" class="btn btn-light btn-sm">Cerrar sesión</a>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <div class="card-panel">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Productos</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreate">+ Nuevo producto</button>
      </div>

      <?php if (isset($_GET['msg'])): 
        $mensajes = [
          'created'=>'Producto creado correctamente.',
          'errcreate'=>'Error al crear producto.',
          'updated'=>'Producto actualizado correctamente.',
          'errupdate'=>'Error al actualizar producto.',
          'deleted'=>'Producto eliminado correctamente.',
          'errdelete'=>'Error al eliminar producto.'
        ]; ?>
        <div class="alert alert-<?= str_starts_with($_GET['msg'],'err')?'danger':'success' ?>">
          <?= $mensajes[$_GET['msg']] ?? 'Operación realizada.' ?>
        </div>
      <?php endif; ?>

      <div class="table-wrap">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Precio</th>
              <th>Stock</th>
              <th>Proveedor</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($productos)): ?>
              <tr><td colspan="8" class="text-center">No hay productos registrados.</td></tr>
            <?php else: foreach ($productos as $p): ?>
              <tr>
                <td><?= $p['id_producto'] ?></td>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['descripcion']) ?></td>
                <td>$<?= number_format($p['precio'],2,',','.') ?></td>
                <td><?= $p['stock'] ?></td>
                <td><?= $p['id_proveedor'] ?: '-' ?></td>
                <td><?= $p['fecha_registro'] ?></td>
                <td>
                  <button class="btn btn-primary btn-sm btn-edit" data-id="<?= $p['id_producto'] ?>" data-nombre="<?= htmlspecialchars($p['nombre'],ENT_QUOTES) ?>" data-descripcion="<?= htmlspecialchars($p['descripcion'],ENT_QUOTES) ?>" data-precio="<?= $p['precio'] ?>" data-stock="<?= $p['stock'] ?>" data-proveedor="<?= $p['id_proveedor'] ?>" data-bs-toggle="modal" data-bs-target="#modalEdit">Editar</button>
                  <form method="POST" style="display:inline-block;" onsubmit="return confirm('¿Eliminar este producto?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                    <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Crear -->
  <div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" class="modal-content">
        <input type="hidden" name="action" value="create">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo producto</h5>
          <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"></textarea>
          </div>
          <div class="row g-2">
            <div class="col">
              <label class="form-label">Precio</label>
              <input name="precio" type="number" step="0.01" min="0" class="form-control" required>
            </div>
            <div class="col">
              <label class="form-label">Stock inicial</label>
              <input name="stock" type="number" min="0" class="form-control" required>
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label">Tipo de producto</label>
            <select name="tipo_producto" class="form-select" required>
              <option value="materia_prima">Materia Prima</option>
              <option value="produccion">Producto Elaborado</option>
            </select>
          </div>
          <div class="mt-3">
            <label class="form-label">ID Proveedor (opcional)</label>
            <input name="id_proveedor" type="number" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-success" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar -->
  <div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" class="modal-content" id="formEdit">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id_producto" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title">Editar producto</h5>
          <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input id="edit_nombre" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea id="edit_descripcion" name="descripcion" class="form-control"></textarea>
          </div>
          <div class="row g-2">
            <div class="col">
              <label class="form-label">Precio</label>
              <input id="edit_precio" name="precio" type="number" step="0.01" min="0" class="form-control" required>
            </div>
            <div class="col">
              <label class="form-label">Stock</label>
              <input id="edit_stock" name="stock" type="number" min="0" class="form-control" required>
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label">ID Proveedor (opcional)</label>
            <input id="edit_proveedor" name="id_proveedor" type="number" class="form-control">
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
    document.querySelectorAll('.btn-edit').forEach(btn=>{
      btn.addEventListener('click',()=>{
        document.getElementById('edit_id').value=btn.dataset.id;
        document.getElementById('edit_nombre').value=btn.dataset.nombre;
        document.getElementById('edit_descripcion').value=btn.dataset.descripcion;
        document.getElementById('edit_precio').value=btn.dataset.precio;
        document.getElementById('edit_stock').value=btn.dataset.stock;
        document.getElementById('edit_proveedor').value=btn.dataset.proveedor;
      });
    });
  </script>
</body>
</html>
