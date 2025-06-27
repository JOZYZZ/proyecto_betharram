<?php
session_start();

if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] !== true) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] !== true) {
    header("Location: index.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "contacto_db");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$condiciones = [];

if (!empty($_GET['desde'])) {
    $desde = $conexion->real_escape_string($_GET['desde']);
    $condiciones[] = "fecha >= '$desde 00:00:00'";
}

if (!empty($_GET['hasta'])) {
    $hasta = $conexion->real_escape_string($_GET['hasta']);
    $condiciones[] = "fecha <= '$hasta 23:59:59'";
}

$where = count($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '';
$sql = "SELECT * FROM mensajes $where ORDER BY fecha DESC";

$resultado = $conexion->query($sql);

?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] !== true) {
    header("Location: index.php");
    exit();
}
?>


<!-- Aquí continúa el contenido del panel -->


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>

<div class="admin-wrapper">

<header>
    <h1>Panel de Administración</h1>
    <?php if ($_SESSION["usuario_id"] === 1): ?>
    <a href="crear_usuario.php" class="crear-usuario-btn">Crear nuevo usuario</a>
<?php endif; ?>

</header>

<main>
    <h2>Mensajes recibidos</h2>

<!-- Filtro por fechas -->
<form method="GET" style="margin-bottom: 1rem; display: flex; gap: 1rem; align-items: center;">
    <label for="desde">Desde:</label>
    <input type="date" id="desde" name="desde" max="<?= date('Y-m-d') ?>" value="<?= $_GET['desde'] ?? '' ?>" required>

    <label for="hasta">Hasta:</label>
    <input type="date" id="hasta" name="hasta" max="<?= date('Y-m-d') ?>" value="<?= $_GET['hasta'] ?? '' ?>" required>

    <button type="submit" class="btn-descargar">Filtrar por fechas</button>
</form>






    <!-- Botón único para descargar todos los mensajes en PDF -->
    <div style="text-align: right; margin-bottom: 1rem;">
        <form action="generar_pdf_todos.php" method="GET" target="_blank">
                <input type="hidden" name="desde" value="<?= isset($_GET['desde']) ? $_GET['desde'] : '' ?>">
                <input type="hidden" name="hasta" value="<?= isset($_GET['hasta']) ? $_GET['hasta'] : '' ?>">
                <button type="submit" class="btn-descargar">Descargar todos los mensajes en PDF</button>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Mensaje</th>
        </tr>

        <?php while ($fila = $resultado->fetch_assoc()) : ?>
            <tr>
                <td><?= $fila['id'] ?></td>
                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                <td><?= htmlspecialchars($fila['email']) ?></td>
                <td><?= htmlspecialchars($fila['telefono']) ?></td>
                <td><?= nl2br(htmlspecialchars($fila['mensaje'])) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> Panel de administración - Vicariato</p>
</footer>

</div>

</body>
</html>
