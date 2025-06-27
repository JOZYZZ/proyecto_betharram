<?php
session_start();

if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] !== true) {
    header("Location: index.php");
    exit();
}

// Solo el administrador principal puede acceder (ID 1 o el nombre que usás)
if ($_SESSION["usuario_id"] !== 1) {
    echo "<script>alert('Acceso denegado.'); window.location.href = 'admin.php';</script>";
    exit();
}
?>


<?php
$mensaje = "";
$conexion = new mysqli("localhost", "root", "", "contacto_db");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $nombre = trim($_POST["nombre"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

    if ($usuario && $contrasena) {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, contrasena, nombre_completo) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario, $hash, $nombre);

        if ($stmt->execute()) {
            $mensaje = "✅ Usuario creado correctamente.";
        } else {
            $mensaje = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $mensaje = "⚠️ Ingresá un usuario y una contraseña.";
    }
}
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="css/login.css"> <!-- o usá un estilo básico -->
</head>

<style>
body {
  font-family: sans-serif;
  background: #f0f0f0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.login-container {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.login-container h2 {
margin-bottom: 1.5rem;
color: #333;
}

.login-container input {
width: 91%;
padding: 12px 14px;
margin: 10px 0;
border: 1px solid #ccc;
border-radius: 6px;
font-size: 16px;
transition: border-color 0.3s;
}

.login-container input:focus {
border-color: #b08a57;
outline: none;
}

.login-container button {
width: 100%;
padding: 12px;
background: #b08a57;
color: white;
border: none;
border-radius: 6px;
font-size: 16px;
cursor: pointer;
margin-top: 1rem;
transition: background 0.3s;
}

.login-container button:hover {
background: #936e43;
}

.login-container p {
color: #333;
margin-top: 10px;
}

</style>

<body>
    <div class="login-container">
        <h2>Agregar nuevo usuario</h2>
        <?php if ($mensaje) echo "<p>$mensaje</p>"; ?>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="text" name="nombre" placeholder="Nombre completo (opcional)">
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit">Crear Usuario</button>
        </form>
    </div>
</body>
</html>
