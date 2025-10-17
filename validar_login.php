<?php
session_start();
include("conexion.php");

$correo = $_POST['correo'];
$contraseña = $_POST['contraseña'];

$sql = "SELECT * FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    // Comparar contraseñas
    if (password_verify($contraseña, $usuario['contraseña'])) {
        $_SESSION['nombre'] = $usuario['nombre'];
        header("Location: index.php");
        exit();
    } else {
        header("Location: login.php?error=Contraseña incorrecta");
        exit();
    }
} else {
    header("Location: login.php?error=Correo no registrado");
    exit();
}
?>


