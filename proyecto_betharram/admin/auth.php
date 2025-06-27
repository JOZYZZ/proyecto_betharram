<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "contacto_db");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$usuario = trim($_POST["usuario"] ?? "");
$password = $_POST["password"] ?? "";

if (!$usuario || !$password) {
    echo "<script>alert('Completa todos los campos.'); window.location.href = 'index.php';</script>";
    exit();
}

// Buscar el usuario en la base de datos
$stmt = $conexion->prepare("SELECT id, contrasena, nombre_completo FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $fila = $resultado->fetch_assoc();

    if (password_verify($password, $fila["contrasena"])) {
        // Login exitoso
        $_SESSION["autenticado"] = true;
        $_SESSION["usuario_id"] = $fila["id"];
        $_SESSION["usuario_nombre"] = $fila["nombre_completo"] ?? $usuario;

        header("Location: admin.php");
        exit();
    } else {
        echo "<script>alert('Contraseña incorrecta'); window.location.href = 'index.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Usuario no encontrado'); window.location.href = 'index.php';</script>";
    exit();
}

