<?php
include 'includes/header.php';
include 'conexion.php';

// Solo permitir a administradores
if ($_SESSION['rol'] !== 'administrador') {
    echo "<div class='alert alert-danger m-4'>❌ No tienes permiso para acceder a esta sección.</div>";
    include 'includes/footer.php';
    exit();
}

// === AGREGAR VENDEDOR ===
if (isset($_POST['add_vendedor'])) {
    $id_usuario = $_POST['id_usuario'];
    $zona = $_POST['zona'];

    $stmt = $conn->prepare("INSERT INTO vendedores_ambulantes (id_usuario, zona) VALUES (?, ?)");
    $stmt->bind_param("is", $id_usuario, $zona);

    if ($stmt->execute()) {
        $mensaje = "Vendedor agregado correctamente";
    } else {
        $error = "Error al guardar vendedor";
    }
    $stmt->close();
}

// === ELIMINAR VENDEDOR ===
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $del = $conn->prepare("DELETE FROM vendedores_ambulantes WHERE id_vendedor = ?");
    $del->bind_param("i", $id);
    $del->execute();

    header("Location: vendedores.php");
    exit();
}

// === CONSULTAR USUARIOS DISPONIBLES ===
$usuarios = $conn->query("SELECT id_usuario, nombre FROM usuarios ORDER BY nombre ASC");

// === CONSULTAR VENDEDORES ===
$vendedores = $conn->query("
    SELECT v.id_vendedor, u.nombre AS usuario, v.zona, v.fecha_registro 
    FROM vendedores_ambulantes v
    INNER JOIN usuarios u ON u.id_usuario = v.id_usuario
    ORDER BY v.id_vendedor DESC
");
?>

<style>
.card-panel {
    background: #ffffff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
}

.table thead {
    background: #7a3e1c;
    color: #fff;
}

.btn-admin {
    background: #7a3e1c;
    color: white;
}
.btn-admin:hover {
    background: #5e2f15;
}
</style>

<div class="container mt-4">
    <div class="card-panel">
        <h3 class="mb-4">Gestión de Vendedores Ambulantes</h3>

        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success"><?= $mensaje ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- FORMULARIO PARA AGREGAR -->
        <h5 class="mb-3">➕ Registrar nuevo vendedor</h5>

        <form method="POST" class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Seleccionar usuario</label>
                <select name="id_usuario" class="form-select" required>
                    <option value="">Seleccione un usuario...</option>
                    <?php while($u = $usuarios->fetch_assoc()): ?>
                        <option value="<?= $u['id_usuario'] ?>"><?= $u['nombre'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Zona del vendedor</label>
                <input type="text" name="zona" class="form-control" placeholder="Ej: Centro, Sur..." required>
            </div>

            <div class="col-12 text-end">
                <button class="btn btn-admin" name="add_vendedor">Guardar vendedor</button>
            </div>
        </form>

        <hr>

        <!-- TABLA -->
        <h5 class="mb-3">📋 Lista de vendedores registrados</h5>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Zona</th>
                        <th>Fecha registro</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($vendedores->num_rows == 0): ?>
                        <tr><td colspan="5" class="text-center">No hay vendedores registrados</td></tr>
                    <?php else: ?>
                        <?php while ($v = $vendedores->fetch_assoc()): ?>
                        <tr>
                            <td><?= $v['id_vendedor'] ?></td>
                            <td><?= $v['usuario'] ?></td>
                            <td><?= $v['zona'] ?></td>
                            <td><?= $v['fecha_registro'] ?></td>
                            <td>
                                <a href="vendedores.php?eliminar=<?= $v['id_vendedor'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar vendedor?')">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
