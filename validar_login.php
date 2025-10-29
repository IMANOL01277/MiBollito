<?php
include("conexion.php");
session_start();

$correo = $_POST['correo'];
$contraseña = $_POST['contraseña'];

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($contraseña, $usuario['contraseña'])) {
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['rol'] = $usuario['rol']; // Guardar el rol
        header("Location: panel.php");
    } else {
        header("Location: login.php?error=Contraseña incorrecta");
    }
} else {
    header("Location: login.php?error=Usuario no encontrado");
}

$stmt->close();
$conn->close();
?>
