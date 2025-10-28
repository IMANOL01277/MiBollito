<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contraseña = $_POST['contraseña'];
    $confirmar = $_POST['confirmar'];

    // ======== VALIDACIONES =========

    // Validar nombre (solo letras, espacios y acentos)
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
        header("Location: registro.php?error=El nombre solo puede contener letras y espacios");
        exit();
    }

    // Validar correo (estructura estándar y evitar doble punto)
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strpos($correo, '..') !== false) {
        header("Location: registro.php?error=El correo electrónico no es válido");
        exit();
    }

    // Validar que las contraseñas coincidan
    if ($contraseña !== $confirmar) {
        header("Location: registro.php?error=Las contraseñas no coinciden");
        exit();
    }

    // Validar contraseña segura
    if (strlen($contraseña) < 8 || 
        !preg_match('/[A-Z]/', $contraseña) || 
        !preg_match('/[\W_]/', $contraseña)) {
        header("Location: registro.php?error=La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un carácter especial");
        exit();
    }

    // Encriptar la contraseña
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

    // ======== VERIFICAR SI EL CORREO YA EXISTE ========
    $verificar = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $verificar->bind_param("s", $correo);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        header("Location: registro.php?error=El correo ya está registrado");
        exit();
    }

    // ======== INSERTAR USUARIO ========
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $correo, $contraseña_hash);

    if ($stmt->execute()) {
        header("Location: login.php?mensaje=Registro exitoso, ahora puedes iniciar sesión");
    } else {
        header("Location: registro.php?error=Error al registrar usuario");
    }

    $stmt->close();
    $verificar->close();
    $conn->close();
} else {
    header("Location: registro.php");
    exit();
}
?>
