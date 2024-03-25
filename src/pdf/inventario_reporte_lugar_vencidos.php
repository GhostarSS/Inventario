<?php
require_once 'fpdf/fpdf.php';
require_once '../../conexion.php'; // Cambia esto por la ruta correcta al archivo de conexión.

// Crear una clase PDF que extiende de FPDF para la creación de reportes
class PDF extends FPDF {
    // Ajustar el documento a horizontal (orientación 'L' para Landscape)
    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4') {
        parent::__construct($orientation, $unit, $size);
    }

    // Encabezado de página
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Reporte de Inventario de Productos Caducados', 0, 1, 'C');
        $this->Ln(10);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Cuerpo del reporte
    function BodyReport($conexion, $selectedPlace) {
        $placeQuery = $conexion->prepare("SELECT laboratorio FROM lugar WHERE id = ?");
        $placeQuery->bind_param('i', $selectedPlace);
        $placeQuery->execute();
        $placeResult = $placeQuery->get_result();
        $placeName = $placeResult->fetch_assoc()['laboratorio'];

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Productos Caducados - Lugar: ' . $placeName, 0, 1, 'C');

        $headers = ['Código', 'Descripción', 'Presentación', 'Tipo', 'Existencia', 'Vencimiento', 'Lugar'];
        $widths = [20, 55, 40, 30, 30, 40, 55];
        $this->SetFont('Arial', 'B', 10);
        for ($i = 0; $i < count($headers); $i++) {
            $this->Cell($widths[$i], 7, $headers[$i], 1, 0, 'C');
        }
        $this->Ln();

        $stmt = $conexion->prepare("
            SELECT p.*, t.tipo, pr.nombre AS presentacion, l.laboratorio
            FROM producto p 
            INNER JOIN tipos t ON p.id_tipo = t.id
            INNER JOIN presentacion pr ON p.id_presentacion = pr.id
            INNER JOIN lugar l ON p.id_lab = l.id
            WHERE p.id_lab = ? AND p.vencimiento != '' AND p.vencimiento < CURDATE()
        ");
        $stmt->bind_param('i', $selectedPlace);
        $stmt->execute();
        $result = $stmt->get_result();

        $this->SetFont('Arial', '', 10);
        while ($row = $result->fetch_assoc()) {
            $this->Cell($widths[0], 6, $row['codigo'], 1);
            $this->Cell($widths[1], 6, $row['descripcion'], 1);
            $this->Cell($widths[2], 6, $row['presentacion'], 1);
            $this->Cell($widths[3], 6, $row['tipo'], 1);
            $this->Cell($widths[4], 6, $row['existencia'], 1);
            $this->Cell($widths[5], 6, $row['vencimiento'], 1);
            $this->Cell($widths[6], 6, $row['laboratorio'], 1);
            $this->Ln();
        }
    }
}

// Verificar si se ha seleccionado un lugar y es un POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place'])) {
    $selectedPlace = sanitize($_POST['place']);

    // Instanciar y generar el PDF
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->BodyReport($conexion, $selectedPlace);
    $pdf->Output('D', 'Reporte_Productos_Caducados_Lugar_' . $selectedPlace . '.pdf'); // Forzar descarga
} else {
    exit('No se ha seleccionado un lugar válido.');
}

// Función para sanitizar la entrada
function sanitize($data) {
    return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
}
?>
