<?php
require_once 'fpdf/fpdf.php';
require_once '../../conexion.php'; // Asegúrate de que este es el camino correcto al archivo de conexión.

class PDF extends FPDF {
    // Ajusta el documento a horizontal (orientación 'L' para Landscape)
    function __construct($orientation = 'L', $unit = 'mm', $size = 'A4') {
        parent::__construct($orientation, $unit, $size);
    }

    // Método para encabezado de página
    function Header() {
        // Logo
        // $this->Image('logo.png',10,6,30); // Descomenta y coloca tu logo si es necesario

        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, 'Reporte de Inventario', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
    }

    // Método para pie de página
    function Footer() {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Método para cuerpo del reporte
    function BodyReport($conexion, $selectedPlace) {
        // Consulta para obtener el nombre del lugar
        $placeQuery = $conexion->prepare("SELECT laboratorio FROM lugar WHERE id = ?");
        $placeQuery->bind_param('i', $selectedPlace);
        $placeQuery->execute();
        $placeResult = $placeQuery->get_result();
        $placeName = $placeResult->fetch_assoc()['laboratorio'];

        // Agregamos el nombre del lugar al reporte
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Productos No Vencidos - Lugar: ' . $placeName, 0, 1, 'C');

        // Encabezados de la tabla
        $this->SetFont('Arial', 'B', 10);
        $headers = ['Codigo', 'Descripcion', 'Presentacion', 'Tipo', 'Existencia', 'Vencimiento', 'Lugar'];
        // Incrementar la anchura de las celdas para adaptarse al formato horizontal
        $widths = [20, 55, 40, 30, 30, 40, 55];
        foreach ($headers as $index => $header) {
            $this->Cell($widths[$index], 7, $header, 1);
        }
        $this->Ln();

        // Consulta para obtener los productos no vencidos
        $stmt = $conexion->prepare("
            SELECT p.*, t.tipo, pr.nombre AS presentacion, l.laboratorio
            FROM producto p 
            INNER JOIN tipos t ON p.id_tipo = t.id
            INNER JOIN presentacion pr ON p.id_presentacion = pr.id
            INNER JOIN lugar l ON p.id_lab = l.id
            WHERE p.id_lab = ? AND (p.vencimiento = '' OR p.vencimiento >= CURDATE())
        ");
        $stmt->bind_param('i', $selectedPlace);
        $stmt->execute();
        $result = $stmt->get_result();

        // Restablecemos la fuente para el contenido de la tabla
        $this->SetFont('Arial', '', 10);
        while ($row = $result->fetch_assoc()) {
            $this->Cell($widths[0], 7, $row['codigo'], 1);
            $this->Cell($widths[1], 7, $row['descripcion'], 1);
            $this->Cell($widths[2], 7, $row['presentacion'], 1);
            $this->Cell($widths[3], 7, $row['tipo'], 1);
            $this->Cell($widths[4], 7, $row['existencia'], 1);
            $vencimiento = $row['vencimiento'] ?: 'No vence';
            $this->Cell($widths[5], 7, $vencimiento, 1);
            $this->Cell($widths[6], 7, $row['laboratorio'], 1); // Nuevo campo lugar
            $this->Ln();
        }
    }
}

// Verificamos el método de la solicitud y si el lugar está establecido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place'])) {
    $selectedPlace = intval($_POST['place']); // Sanitizamos la entrada como un entero

    // Creamos una nueva instancia de la clase PDF
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->BodyReport($conexion, $selectedPlace);
    $pdf->Output('D', 'Reporte_Productos_No_Vencidos_Lugar_' . $selectedPlace . '.pdf'); // D: forzar descarga
} else {
    exit('No se ha seleccionado un lugar válido.');
}

// Función para sanitizar la entrada del usuario
function sanitize($data) {
    return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
}
?>
