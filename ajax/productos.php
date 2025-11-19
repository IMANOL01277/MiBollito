<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../conexion.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

function res($ok, $data = []) {
    echo json_encode(array_merge(['success' => !!$ok], $data));
    exit();
}

// === LISTAR PRODUCTOS ===
if ($action === 'list') {
    $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, 
                   c.nombre AS categoria, pr.nombre AS proveedor
            FROM productos p
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
            ORDER BY p.id_producto DESC";
    $res = $conn->query($sql);
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    res(true, ['products' => $rows]);
}

// === OBTENER PRODUCTO ===
if ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) res(false, ['message' => 'Producto no encontrado']);
    res(true, ['product' => $res->fetch_assoc()]);
}

// === LISTAR CATEGORÍAS ===
if ($action === 'categories') {
    $res = $conn->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    res(true, ['categorias' => $rows]);
}

// === LISTAR PROVEEDORES ===
if ($action === 'proveedores') {
    $res = $conn->query("SELECT id_proveedor, nombre FROM proveedores ORDER BY nombre ASC");
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    res(true, ['proveedores' => $rows]);
}

// === CREAR PRODUCTO ===
if ($action === 'create') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $id_proveedor = $_POST['id_proveedor'] ? (int)$_POST['id_proveedor'] : null;

    if ($nombre === '') res(false, ['message' => 'El nombre es obligatorio']);

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, stock_actual, id_categoria, id_proveedor) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("ssdiiii", $nombre, $descripcion, $precio, $stock, $stock, $id_categoria, $id_proveedor);
    $ok = $stmt->execute();
    $newId = $conn->insert_id;

    // Registrar movimiento de entrada inicial si stock > 0
    if ($ok && $stock > 0) {
        $total = $precio * $stock;
        $mov = $conn->prepare("INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, total, descripcion) VALUES (?, 'entrada', ?, ?, ?, 'Registro inicial de stock')");
        $mov->bind_param("iidd", $newId, $stock, $precio, $total);
        $mov->execute();
    }

    res($ok, ['message' => $ok ? 'Producto creado correctamente' : 'Error al crear producto']);
}

// === ACTUALIZAR PRODUCTO ===
if ($action === 'update') {
    $id = (int)($_POST['id_producto'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $id_proveedor = $_POST['id_proveedor'] ? (int)$_POST['id_proveedor'] : null;

    if ($id <= 0) res(false, ['message' => 'ID inválido']);

    // Obtener stock anterior
    $old = $conn->query("SELECT stock, precio FROM productos WHERE id_producto = $id")->fetch_assoc();
    $oldStock = (int)$old['stock'];
    $oldPrecio = (float)$old['precio'];

    $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, id_categoria=?, id_proveedor=? WHERE id_producto=?");
    $stmt->bind_param("ssdiiii", $nombre, $descripcion, $precio, $stock, $id_categoria, $id_proveedor, $id);
    $ok = $stmt->execute();

    // Registrar movimiento automático si cambió el stock
    if ($ok) {
        $cantidad = $stock - $oldStock;
        if ($cantidad != 0) {
            $tipo = $cantidad > 0 ? 'entrada' : 'salida';
            $cantidadAbs = abs($cantidad);
            $precioUsado = $precio > 0 ? $precio : $oldPrecio;
            $total = $cantidadAbs * $precioUsado;

            $mov = $conn->prepare("INSERT INTO movimientos_inventario (id_producto, tipo, cantidad, precio_unitario, total, descripcion) VALUES (?, ?, ?, ?, ?, 'Actualización de stock')");
            $mov->bind_param("isidd", $id, $tipo, $cantidadAbs, $precioUsado, $total);
            $mov->execute();
        }
    }

    res($ok, ['message' => $ok ? 'Producto actualizado correctamente' : 'Error al actualizar producto']);
}

// === ELIMINAR PRODUCTO ===
if ($action === 'delete') {
    $id = (int)($_POST['id_producto'] ?? 0);
    if ($id <= 0) res(false, ['message' => 'ID inválido']);

    // Borrar movimientos asociados
    $conn->query("DELETE FROM movimientos_inventario WHERE id_producto = $id");
    $ok = $conn->query("DELETE FROM productos WHERE id_producto = $id");

    res($ok, ['message' => $ok ? 'Producto eliminado' : 'Error al eliminar']);
}

res(false, ['message' => 'Acción no válida']);
