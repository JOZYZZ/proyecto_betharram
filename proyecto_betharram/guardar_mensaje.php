<?php
$conexion = new mysqli("localhost", "root", "", "contacto_db");

if ($conexion->connect_error) {
    http_response_code(500);
    exit;
}

$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

$sql = "INSERT INTO mensajes (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $nombre, $email, $telefono, $mensaje);

if (!$stmt->execute()) {
    http_response_code(500);
}

$stmt->close();
$conexion->close();
?>
