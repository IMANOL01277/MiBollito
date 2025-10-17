<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "bd_mibollito"; // cambia por el nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>