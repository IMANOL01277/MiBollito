<?php
include("includes/header.php");
include("conexion.php");

if ($_SESSION['rol'] != 'administrador') {
    header("Location: panel.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['crear_categoria'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    if ($nombre === "") {
        $mensaje = "El nombre no puede estar vacío.";
    } else {
        $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion, fecha_registro) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $nombre, $descripcion);

        if ($stmt->execute()) {
            $mensaje = "Categoría creada correctamente.";
        } else {
            $mensaje = "Error al crear la categoría.";
        }

        $stmt->close();
    }
}

$categorias = $conn->query("SELECT * FROM categorias ORDER BY id_categoria DESC");
?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Gestión de Categorías</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria">
            + Nueva Categoría
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
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $categorias->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_categoria'] ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['descripcion']) ?></td>
                        <td><?= $row['fecha_registro'] ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>


<!-- MODAL NUEVA CATEGORÍA -->
<div class="modal fade" id="modalCategoria">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Crear Nueva Categoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST">
      <div class="modal-body">
        <label class="form-label fw-bold">Nombre:</label>
        <input type="text" class="form-control mb-3" name="nombre" required>

        <label class="form-label fw-bold">Descripción:</label>
        <textarea class="form-control" name="descripcion"></textarea>
      </div>

      <div class="modal-footer">
        <button type="submit" name="crear_categoria" class="btn btn-primary">Guardar</button>
      </div>
      </form>

    </div>
  </div>
</div>

<?php include("includes/footer.php"); ?>
