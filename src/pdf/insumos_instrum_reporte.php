

<?php
require_once 'fpdf/fpdf.php';
require_once '../../conexion.php'; // Ensure this path points to your actual connection file.


// Function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags($data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type'])) {
    $selectedType = sanitize($_POST['type']);

    class PDF extends FPDF {
        // Table column widths
        protected $columnWidths = [20, 50, 30, 20, 25, 25];

        // Page header
        function Header() {
            $this->SetFont('Arial', 'B', 15);
            $this->SetFillColor(255, 255, 255); 
            $this->SetTextColor(0);
            $this->SetDrawColor(0, 0, 0); // Make sure the draw color is set to black
            $this->Cell(0, 10, 'Sistema de Registro de Insumos e Instrumentos Odontologicos', 0, 1, 'C', true);
            $this->SetFont('Arial', 'I', 12);
            $this->Cell(0, 10, 'Productos Vencidos', 0, 0, 'C', true);
            $this->Ln(20);
    
            // Table header
            $this->SetFont('Arial', 'B', 12);
    
            $headerTitles = ['Codigo', 'Descripcion', 'Presentacion', 'Tipo', 'Existencia', 'Vencimiento'];
            foreach ($this->columnWidths as $index => $width) {
                // Set fill and draw color each time to ensure consistency
                $this->SetFillColor(255, 255, 255);
                $this->SetDrawColor(0, 0, 0); // Set draw color to black for borders
                $this->Cell($width, 7, $headerTitles[$index], 1, 0, 'C', true);
            }
            $this->Ln();
        }

        // Page body
        function BodyReport($conexion, $selectedType) {
            $this->SetFont('Arial', '', 10);
        
            // Prepare the statement
            $stmt = $conexion->prepare("
                SELECT p.codigo, p.descripcion, pr.nombre as presentacion, t.tipo, p.existencia, p.vencimiento
                FROM producto p 
                INNER JOIN tipos t ON p.id_tipo = t.id
                INNER JOIN presentacion pr ON p.id_presentacion = pr.id
                WHERE t.tipo = ? AND p.vencimiento < CURDATE() AND p.vencimiento <> '' AND p.vencimiento IS NOT NULL
            ");
            $stmt->bind_param('s', $selectedType);
            $stmt->execute();
            $result = $stmt->get_result();
        
            // Ensure that the draw color is set to black for cell borders
            $this->SetDrawColor(0, 0, 0);
            
            // Iterate over each row of the result
            while ($row = $result->fetch_assoc()) {
                // Set fill to false for each cell to ensure no background fill is applied
                $fill = false;
                $this->Cell($this->columnWidths[0], 6, $row['codigo'], 'LR', 0, 'L', $fill);
                $this->Cell($this->columnWidths[1], 6, $row['descripcion'], 'LR', 0, 'L', $fill);
                $this->Cell($this->columnWidths[2], 6, $row['presentacion'], 'LR', 0, 'L', $fill);
                $this->Cell($this->columnWidths[3], 6, $row['tipo'], 'LR', 0, 'L', $fill);
                $this->Cell($this->columnWidths[4], 6, $row['existencia'], 'LR', 0, 'R', $fill);
        
                // Display 'Vencido' for expired products
                $vencimientoText = 'Vencido';
                $this->Cell($this->columnWidths[5], 6, $vencimientoText, 'LR', 0, 'R', $fill);
                $this->Ln();
            }
        
            // Closing line
            $this->Cell(array_sum($this->columnWidths), 0, '', 'T');
        }
        

        // Page footer
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->BodyReport($conexion, $selectedType);
    $pdf->Output('D', 'Reporte_' . $selectedType . '.pdf'); // D: force download
} else {
    // This part would ideally be your form for selecting the type, or a redirect back to it.
    exit('No type selected!');
}
?>
