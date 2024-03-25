<?php
require_once '../../conexion.php'; // Ensure this path points to your actual connection file.
require_once 'fpdf/fpdf.php';

class PDF extends FPDF {
    // Column widths
    protected $columnWidths = [20, 50, 30, 20, 25, 25];

    // Page header
    function Header() {
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30, 10, 'Sistema de Registro de Insumos e Instrumentos Odontologicos', 0, 1, 'C');
        // Line break
        $this->Ln(20);
        // Arial italic 12
        $this->SetFont('Arial', 'I', 12);
        // Page subtitle
        $this->Cell(0, 10, 'Productos Vencidos', 0, 0, 'C');
        // Line break
        $this->Ln(10);

        // Set fill color to white
        $this->SetFillColor(255, 255, 255);
        // Set text color to black
        $this->SetTextColor(0);
        // Set draw color to black for borders
        $this->SetDrawColor(0, 0, 0);
        // Set the line width of the border
        $this->SetLineWidth(.3);
        // Set the font for the header
        $this->SetFont('Arial', 'B', 12);

        // Header
        $header = array('Codigo', 'Descripcion', 'Presentacion', 'Tipo', 'Existencia', 'Vencimiento');
        for($i = 0; $i < count($header); $i++) {
            $this->Cell($this->columnWidths[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
    }

    // Page body
    function BodyReport() {
        global $conexion;
        $this->SetFont('Arial', '', 10);

        $query = "SELECT p.codigo, p.descripcion, pres.nombre as presentacion, t.tipo, p.existencia, 
                  IF(p.vencimiento IS NULL OR p.vencimiento = '', 'No vence', p.vencimiento) AS vencimiento
                  FROM producto p 
                  INNER JOIN tipos t ON p.id_tipo = t.id
                  INNER JOIN presentacion pres ON p.id_presentacion = pres.id
                  WHERE p.vencimiento <> '' AND p.vencimiento IS NOT NULL AND p.vencimiento < CURDATE()";
        $result = mysqli_query($conexion, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            // Fill color set to white
            $this->SetFillColor(255, 255, 255);
            $fill = false;
            $this->Cell($this->columnWidths[0], 6, $row['codigo'], 'LR', 0, 'L', $fill);
            $this->Cell($this->columnWidths[1], 6, $row['descripcion'], 'LR', 0, 'L', $fill);
            $this->Cell($this->columnWidths[2], 6, $row['presentacion'], 'LR', 0, 'L', $fill);
            $this->Cell($this->columnWidths[3], 6, $row['tipo'], 'LR', 0, 'L', $fill);
            $this->Cell($this->columnWidths[4], 6, $row['existencia'], 'LR', 0, 'R', $fill);
            $this->Cell($this->columnWidths[5], 6, $row['vencimiento'], 'LR', 0, 'R', $fill);
            $this->Ln();
        }
        $this->Cell(array_sum($this->columnWidths), 0, '', 'T');
    }

    // Page footer
    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

// Instantiate the PDF class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->BodyReport();
$pdf->Output(); // Output the PDF
?>
