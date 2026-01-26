<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../lib/fpdf/fpdf.php";

if (ob_get_length()) {
    ob_end_clean();
}

function pdf_txt(string $s): string
{
    return iconv("UTF-8", "ISO-8859-1//TRANSLIT", $s);
}

// ----------------------
// CONSULTA LIBROS
// ----------------------
$sql = "SELECT isbn, titulo, autor, editorial, precio, estado
        FROM libros
        ORDER BY titulo ASC";

$stmt = $pdo->query($sql);
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ----------------------
// PDF
// ----------------------
$pdf = new FPDF("P", "mm", "A4");
$pdf->AddPage();

// TÍTULO
$pdf->SetFont("Arial", "B", 14);
$pdf->Cell(0, 10, pdf_txt("Listado de Libros - Biblioteca"), 0, 1, "C");
$pdf->Ln(4);

// --- Anchos (suman 190mm aprox, que cabe en A4 con márgenes por defecto)
$wIsbn = 28;
$wTitulo = 62;
$wAutor = 38;
$wEditorial = 32;
$wPrecio = 20;
$wEstado = 10;

// CABECERAS
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell($wIsbn, 8, "ISBN", 1, 0, "C");
$pdf->Cell($wTitulo, 8, pdf_txt("Título"), 1, 0, "C");
$pdf->Cell($wAutor, 8, pdf_txt("Autor"), 1, 0, "C");
$pdf->Cell($wEditorial, 8, pdf_txt("Editorial"), 1, 0, "C");
$pdf->Cell($wPrecio, 8, pdf_txt("Precio"), 1, 0, "C");
$pdf->Cell($wEstado, 8, pdf_txt("Est."), 1, 1, "C");

// DATOS
$pdf->SetFont("Arial", "", 9);

foreach ($libros as $l) {
    $isbn = (string)$l["isbn"];
    $titulo = (string)$l["titulo"];
    $autor = (string)$l["autor"];
    $editorial = (string)($l["editorial"] ?? "");

    // Precio compacto (sin símbolo € si quieres aún más compacto)
    if ($l["precio"] === null) {
        $precio = "-";
    } else {
        $precio = number_format((float)$l["precio"], 2); // sin €
    }

    // Estado compacto
    $estadoRaw = (string)($l["estado"] ?? "");
    $estado = ($estadoRaw === "DISPONIBLE") ? "D" : (($estadoRaw === "PRESTADO") ? "P" : "");

    // Recortes para que no desborden
    if (mb_strlen($titulo) > 45) $titulo = mb_substr($titulo, 0, 45) . "...";
    if (mb_strlen($autor) > 28) $autor = mb_substr($autor, 0, 28) . "...";
    if (mb_strlen($editorial) > 20) $editorial = mb_substr($editorial, 0, 20) . "...";

    $pdf->Cell($wIsbn, 8, pdf_txt($isbn), 1, 0, "L");
    $pdf->Cell($wTitulo, 8, pdf_txt($titulo), 1, 0, "L");
    $pdf->Cell($wAutor, 8, pdf_txt($autor), 1, 0, "L");
    $pdf->Cell($wEditorial, 8, pdf_txt($editorial), 1, 0, "L");
    $pdf->Cell($wPrecio, 8, pdf_txt($precio), 1, 0, "R");
    $pdf->Cell($wEstado, 8, pdf_txt($estado), 1, 1, "C");
}

// Leyenda del estado
$pdf->Ln(3);
$pdf->SetFont("Arial", "I", 8);
$pdf->Cell(0, 5, pdf_txt("Est.: D = Disponible, P = Prestado"), 0, 1, "L");

// Pie con fecha
$pdf->Cell(0, 5, pdf_txt("Generado el " . date("d/m/Y H:i:s")), 0, 1, "R");

// SALIDA
$pdf->Output("I", "listado_libros.pdf");
exit;
