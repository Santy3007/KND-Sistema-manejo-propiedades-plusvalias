<?php
require_once('tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    public function Header() {
        // Encabezado con estilo
        $this->SetFillColor(35, 61, 123); // Azul corporativo
        $this->Rect(0, 0, $this->getPageWidth(), 20, 'F');
        
        $this->SetTextColor(255, 255, 255); // Texto blanco
        $this->SetFont('helvetica', 'B', 14);
        $this->SetXY(10, 5);
        $this->Cell(0, 10, 'Reporte de Dashboard', 0, 0, 'L');
        
        // Nombre de la empresa con estilo
        $this->SetFont('helvetica', 'I', 10);
        $this->SetXY($this->getPageWidth() - 60, 5);
        $this->Cell(50, 10, 'PlusvaliaBlk', 0, 0, 'R');
        
        // Línea separadora
        $this->SetDrawColor(220, 220, 220);
        $this->Line(10, 21, $this->getPageWidth() - 10, 21);
    }
    
    public function Footer() {
        // Pie de página
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
        $this->Cell(0, 10, 'Generado el: ' . date('d/m/Y H:i'), 0, 0, 'R');
    }
}

// Crear PDF con márgenes personalizados
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(15, 30, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();

// Título principal con diseño mejorado
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetTextColor(35, 61, 123); // Azul corporativo
$pdf->Cell(0, 12, 'Reporte General', 0, 1, 'C');

// Subtítulo con fecha
$pdf->SetFont('helvetica', 'I', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, 'Datos actualizados al ' . date('d/m/Y'), 0, 1, 'C');
$pdf->Ln(8);

// Sección de KPIs con diseño de tarjetas
$pdf->SetDrawColor(220, 220, 220);
$pdf->SetLineWidth(0.1);

// Crear función para tarjetas KPI (CORREGIDA)
function drawKpiCard($pdf, $title, $value, $x, $y, $width, $height, $color) {
    // Fondo de la tarjeta
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetDrawColor(235, 235, 235);
    $pdf->Rect($x, $y, $width, $height, 'DF');
    
    // Línea de color en la parte superior
    $pdf->SetDrawColor($color[0], $color[1], $color[2]);
    $pdf->SetLineWidth(4);
    $pdf->Line($x, $y, $x + $width, $y);
    $pdf->SetLineWidth(0.1);
    
    // Título
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($x + 2, $y + 7);
    $pdf->Cell($width - 4, 5, $title, 0, 0, 'L');
    
    // Valor
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($color[0], $color[1], $color[2]);
    $pdf->SetXY($x + 2, $y + 15);
    $pdf->Cell($width - 4, 8, $value, 0, 0, 'C');
}

// Colores para cada KPI
$colorPropiedades = [52, 152, 219]; // Azul
$colorSolicitudes = [231, 76, 60];  // Rojo
$colorEmpresas = [46, 204, 113];    // Verde
$colorUsuarios = [155, 89, 182];    // Púrpura

// Dibujar las tarjetas de KPI en una fila
$cardWidth = 42;
$cardHeight = 30;
$cardSpacing = 5;
$startX = ($pdf->getPageWidth() - (4 * $cardWidth + 3 * $cardSpacing)) / 2;
$startY = 55;

drawKpiCard($pdf, 'Propiedades', '6', $startX, $startY, $cardWidth, $cardHeight, $colorPropiedades);
drawKpiCard($pdf, 'Solicitudes', '1', $startX + $cardWidth + $cardSpacing, $startY, $cardWidth, $cardHeight, $colorSolicitudes);
drawKpiCard($pdf, 'Empresas', '1', $startX + 2 * ($cardWidth + $cardSpacing), $startY, $cardWidth, $cardHeight, $colorEmpresas);
drawKpiCard($pdf, 'Usuarios', '5', $startX + 3 * ($cardWidth + $cardSpacing), $startY, $cardWidth, $cardHeight, $colorUsuarios);

$pdf->Ln(40);

// Título sección de gráficos
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(35, 61, 123);
$pdf->Cell(0, 10, 'Análisis Gráfico', 0, 1, 'L');

// Línea separadora
$pdf->SetDrawColor(220, 220, 220);
$pdf->Line(15, $pdf->GetY(), $pdf->getPageWidth() - 15, $pdf->GetY());
$pdf->Ln(5);

// Función mejorada para agregar gráficos
function agregarGraficoEstilizado($pdf, $titulo, $descripcion, $ruta, $x, $y, $width, $height) {
    if (file_exists($ruta)) {
        // Marco del gráfico
        $pdf->SetFillColor(248, 249, 250);
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->Rect($x, $y, $width, $height + 25, 'DF');
        
        // Título del gráfico
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(35, 61, 123);
        $pdf->SetXY($x + 5, $y + 5);
        $pdf->Cell($width - 10, 6, $titulo, 0, 1, 'L');
        
        // Descripción del gráfico
        $pdf->SetFont('helvetica', 'I', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY($x + 5, $y + 11);
        $pdf->Cell($width - 10, 5, $descripcion, 0, 1, 'L');
        
        // Imagen del gráfico
        $pdf->Image($ruta, $x + 5, $y + 20, $width - 10, $height - 5);
    } else {
        $pdf->SetXY($x, $y);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->SetTextColor(231, 76, 60);
        $pdf->Cell($width, 8, '⚠️ Imagen no disponible: ' . basename($ruta), 0, 1, 'C');
    }
}

// Posiciones para los gráficos
$grafico1_x = 15;
$grafico2_x = 105;
$grafico_width = 85;
$grafico_height = 55;
$fila1_y = $pdf->GetY();
$fila2_y = $fila1_y + $grafico_height + 30;

// Agregar gráficos estilizados
agregarGraficoEstilizado(
    $pdf, 
    "Propiedades por Tipo", 
    "Distribución de propiedades según categoría", 
    "graficos/propiedades_tipo.jpg", 
    $grafico1_x, 
    $fila1_y, 
    $grafico_width, 
    $grafico_height
);

agregarGraficoEstilizado(
    $pdf, 
    "Disponibilidad de Propiedades", 
    "Estado actual del inventario", 
    "graficos/disponibilidad.jpg", 
    $grafico2_x, 
    $fila1_y, 
    $grafico_width, 
    $grafico_height
);

agregarGraficoEstilizado(
    $pdf, 
    "Precio Promedio por Provincia", 
    "Comparativa de precios por ubicación geográfica", 
    "graficos/precio_provincia.jpg", 
    $grafico1_x, 
    $fila2_y, 
    $grafico_width * 2, 
    $grafico_height
);

// Agregar nota al pie
$pdf->Ln(95);
$pdf->SetFont('helvetica', 'I', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 5, "Nota: Este informe es generado automáticamente y representa datos en tiempo real del sistema PlusvaliaBlk. Para más información, contacte con el departamento de analítica.", 0, 'L');

// Generar el PDF
$pdf->Output('Reporte_Dashboard.pdf', 'D');
?>