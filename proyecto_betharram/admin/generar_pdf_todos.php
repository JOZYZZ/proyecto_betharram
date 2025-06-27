<?php
require('fpdf/fpdf.php');

$conexion = new mysqli("localhost", "root", "", "contacto_db");

$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';

if (!$desde || !$hasta) {
    die("Faltan fechas para generar el PDF.");
}

$desde_completo = $desde . " 00:00:00";
$hasta_completo = $hasta . " 23:59:59";

$consulta = $conexion->prepare("SELECT * FROM mensajes WHERE fecha BETWEEN ? AND ? ORDER BY fecha ASC");
$consulta->bind_param("ss", $desde_completo, $hasta_completo);
$consulta->execute();
$resultado = $consulta->get_result();

$pdf = new FPDF();
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('Arial', '', 12);
$contador = 0;

while ($mensaje = $resultado->fetch_assoc()) {
    if ($contador % 3 === 0) {
        $pdf->AddPage();

        // Título de la página
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode("Vicariato Betharram - Formulario de Contacto"), 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
    }

    // Datos del mensaje
    $pdf->SetFillColor(230, 230, 230);

    $pdf->Cell(50, 10, utf8_decode("Nombre completo:"), 1, 0, 'L', true);
    $pdf->Cell(130, 10, utf8_decode($mensaje['nombre']), 1, 1);

    $pdf->Cell(50, 10, utf8_decode("Correo electrónico:"), 1, 0, 'L', true);
    $pdf->Cell(130, 10, utf8_decode($mensaje['email']), 1, 1);

    $pdf->Cell(50, 10, utf8_decode("Teléfono:"), 1, 0, 'L', true);
    $pdf->Cell(130, 10, utf8_decode($mensaje['telefono']), 1, 1);

    $pdf->Cell(50, 10, utf8_decode("Fecha de envío:"), 1, 0, 'L', true);
    $pdf->Cell(130, 10, utf8_decode($mensaje['fecha']), 1, 1);

    $pdf->Cell(180, 10, utf8_decode("Mensaje enviado:"), 1, 1, 'L', true);
    $pdf->MultiCell(180, 10, utf8_decode($mensaje['mensaje']), 1);

    $pdf->Ln(5);
    $contador++;
}

$pdf->Output("D", "mensajes_filtrados.pdf");
