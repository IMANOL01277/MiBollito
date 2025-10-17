<?php
include("conexion.php");

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$contraseña = $_POST['contraseña'];

// Encriptar la contraseña
$contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Verificar si el correo ya existe
$verificar = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
$verificar->bind_param("s", $correo);
$verificar->execute();
$resultado = $verificar->get_result();

if ($resultado->num_rows > 0) {
    header("Location: registro.php?error=El correo ya está registrado");
    exit();
}

// Insertar usuario en la base de datos
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nombre, $correo, $contraseña_hash);

if ($stmt->execute()) {
    // ✅ Redirigir al login con mensaje de éxito
    header("Location: login.php?mensaje=Registro exitoso, ahora puedes iniciar sesión");
} else {
    header("Location: registro.php?error=Error al registrar usuario");
}

$stmt->close();
$conn->close();
?>
