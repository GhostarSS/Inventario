<?php
require_once '../../conexion.php'; // Ensure this path points to your actual connection file.
require_once 'fpdf/fpdf.php';

class PDF extends FPDF
{
    // Column widths
    protected $columnWidths = [20, 50, 30, 20, 25, 25];

    // Page header
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        // Center this cell by calculating margins
        $this->Cell(0, 10, 'Sistema de Registro de Insumos e Instrumentos Odontologicos', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, 'Productos No Vencidos', 0, 0, 'C');
        $this->Ln(20);

        // Table header
        $this->SetFillColor(255, 255, 255); // White background
        $this->SetTextColor(0); // Black text
        $this->SetDrawColor(0, 0, 0); // Black border
        $this->SetLineWidth(.1); // Thinner line
        $this->SetFont('Arial', 'B', 12);

        $headerTitles = ['Codigo', 'Descripcion', 'Presentacion', 'Tipo', 'Existencia', 'Vencimiento'];
        foreach ($headerTitles as $i => $headerTitle) {
            $this->Cell($this->columnWidths[$i], 7, $headerTitle, 1, 0, 'C', true);
        }
        $this->Ln();
    }


    // Page body
    function BodyReport()
    {
        global $conexion;
        $this->SetFont('Arial', '', 10);

        $query = "SELECT p.codigo, p.descripcion, pr.nombre AS presentacion, t.tipo, p.existencia, 
          CASE 
            WHEN p.vencimiento IS NULL OR p.vencimiento = '' THEN 'No vence'
            ELSE p.vencimiento
          END AS vencimiento
          FROM producto p
          INNER JOIN tipos t ON p.id_tipo = t.id
          INNER JOIN presentacion pr ON p.id_presentacion = pr.id
          WHERE p.vencimiento >= CURDATE() OR p.vencimiento IS NULL OR p.vencimiento = ''";
        $result = mysqli_query($conexion, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $this->Cell($this->columnWidths[0], 6, $row['codigo'], 'LR', 0, 'L');
            $this->Cell($this->columnWidths[1], 6, $row['descripcion'], 'LR', 0, 'L');
            $this->Cell($this->columnWidths[2], 6, $row['presentacion'], 'LR', 0, 'L');
            $this->Cell($this->columnWidths[3], 6, $row['tipo'], 'LR', 0, 'L');
            $this->Cell($this->columnWidths[4], 6, $row['existencia'], 'LR', 0, 'R');
            $this->Cell($this->columnWidths[5], 6, $row['vencimiento'], 'LR', 0, 'R');
            $this->Ln();
        }

        $this->Cell(array_sum($this->columnWidths), 0, '', 'T');
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->BodyReport();
$pdf->Output();
?>