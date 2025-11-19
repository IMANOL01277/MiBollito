<?php
include("includes/header.php");
include("conexion.php");

if ($_SESSION['rol'] != 'administrador') {
    header("Location: panel.php");
    exit();
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_proveedor'])) {
    
    $nombre      = trim($_POST['nombre']);
    $contacto    = trim($_POST['contacto']);
    $telefono    = trim($_POST['telefono']);
    $correo      = trim($_POST['correo']);
    $direccion   = trim($_POST['direccion']);

    $stmt = $conn->prepare("
        INSERT INTO proveedores (nombre, contacto, telefono, correo, direccion, fecha_registro)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param("sssss", $nombre, $contacto, $telefono, $correo, $direccion);

    if ($stmt->execute()) {
        $mensaje = "Proveedor agregado correctamente.";
    } else {
        $mensaje = "Error al agregar proveedor.";
    }

    $stmt->close();
}

$proveedores = $conn->query("SELECT * FROM proveedores ORDER BY id_proveedor DESC");
?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Gestión de Proveedores</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProveedor">
            + Nuevo Proveedor
        </button>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Proveedor</th>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Dirección</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $proveedores->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_proveedor'] ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['contacto']) ?></td>
                        <td><?= $row['telefono'] ?></td>
                        <td><?= htmlspecialchars($row['correo']) ?></td>
                        <td><?= htmlspecialchars($row['direccion']) ?></td>
                        <td><?= $row['fecha_registro'] ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>


<!-- MODAL NUEVO PROVEEDOR -->
<div class="modal fade" id="modalProveedor">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Registrar Proveedor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST">
      <div class="modal-body">

        <label class="form-label fw-bold">Nombre:</label>
        <input type="text" class="form-control" name="nombre" required>

        <label class="form-label fw-bold mt-3">Persona de contacto:</label>
        <input type="text" class="form-control" name="contacto">

        <label class="form-label fw-bold mt-3">Teléfono:</label>
        <input type="text" class="form-control" name="telefono">

        <label class="form-label fw-bold mt-3">Correo:</label>
        <input type="email" class="form-control" name="correo">

        <label class="form-label fw-bold mt-3">Dirección:</label>
        <input type="text" class="form-control" name="direccion">

      </div>

      <div class="modal-footer">
        <button type="submit" name="crear_proveedor" class="btn btn-primary">Guardar</button>
      </div>
      </form>

    </div>
  </div>
</div>

<?php include("includes/footer.php"); ?>
