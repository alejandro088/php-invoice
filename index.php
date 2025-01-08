<?php
require('vendor/autoload.php');

use FPDF;

class PDF_Invoice extends FPDF
{
    // Encabezado de la página
    function Header()
    {
        // Salto de línea después del logo
        $this->Ln(20);

        // Factura info
        $this->SetFont('Arial', 'B', 10);
        $this->SetXY(150, 10);
        $this->Cell(50, 5, mb_convert_encoding('FACTURA N°: 123456', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
        $this->SetFont('Arial', '', 10);
        $this->SetXY(150, 15);
        $this->Cell(50, 5, 'Fecha: 29/12/2024', 0, 1, 'R');
        $this->Ln(10);

        //Detalles empresa
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(100, 10, 'Datos empresa', 0, 1);
        $this->SetFont('Arial', '', 9);
        $this->Cell(100, 5, mb_convert_encoding('DDD SL', 'ISO-8859-1', 'UTF-8'), 0, 1);
        $this->Cell(100, 5, mb_convert_encoding('C/ Falsa, 123, Cualquier', 'ISO-8859-1', 'UTF-8'), 0, 1);
        $this->Cell(100, 5, mb_convert_encoding('D, 03150, (D)', 'ISO-8859-1', 'UTF-8'), 0, 1);
        $this->Cell(100, 5, 'D@D.com', 0, 1);
        $this->Cell(100, 5, '966710126', 0, 1);
        $this->Ln(10);

        //QR Verifactu
        
        //$this->Image('qr.png', 90, 10, 20); // Reemplaza 'logo.png' por el nombre de tu archivo de logo
        $this->SetFont('Arial', '', 10);
        $this->SetXY(91, 30);
        $this->Cell(50, 5, mb_convert_encoding('Ver*factu', 'ISO-8859-1', 'UTF-8'), 0, 1);
        

        //Detalles clientes
        $this->SetFont('Arial', 'B', 10);
        $this->SetXY(145, 30);
        $this->Cell(100, 10, 'Datos cliente', 0, 1);
        $this->SetFont('Arial', '', 9);
        $this->SetXY(145, 38);
        $this->Cell(100, 5, mb_convert_encoding('DDDD SL', 'ISO-8859-1', 'UTF-8'), 0, 1);
        $this->SetXY(145, 43);
        $this->Cell(100, 5, mb_convert_encoding('D InduDstrial LosD Nazarios, s/n', 'ISO-8859-1', 'UTF-8'), 0, 1);
        $this->SetXY(145, 48);
        $this->Cell(100, 5, mb_convert_encoding('D, D del D, (D)', 'ISO-8859-1', 'UTF-8'), 0, 1);
        $this->SetXY(145, 53);
        $this->Cell(100, 5, 'hola@hola.es', 0, 1);
        $this->SetXY(145, 58);
        $this->Cell(100, 5, '987458965', 0, 1);
        $this->Ln(10);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    function PrintTableHeaders()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(80, 10, 'Descripcion / Producto', 0, 0, 'L');
        $this->Cell(20, 10, 'Cantidad', 0, 0, 'C');
        $this->Cell(20, 10, 'Base', 0, 0, 'C');
        $this->Cell(20, 10, 'Desc (%)', 0, 0, 'C');
        $this->Cell(20, 10, 'IVA', 0, 0, 'C');
        $this->Cell(30, 10, 'Total', 0, 1, 'C');
    }

    // Encabezado de la tabla
    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(90, 10, 'Descripcion', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Cantidad', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Precio Unitario', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Total', 1, 1, 'C', true);
    }

    // Filas de la tabla
    function TableRow($description, $quantity, $unitPrice)
    {
        $this->SetFont('Arial', '', 10);
        $total = $quantity * $unitPrice;

        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);

        // Dibujar línea superior de encabezados
        //$this->Line(10, $this->GetY(), 200, $this->GetY());

        $cellWidths = [80, 20, 20, 20, 20, 30]; // Anchos de cada columna
        $cellHeight = 4;

        // Imprimir la fila
        $x = $this->GetX(); // Guardar posición inicial
        $y = $this->GetY(); // Guardar posición inicial

        // Descripción
        $this->MultiCell($cellWidths[0], $cellHeight, mb_convert_encoding($description, 'ISO-8859-1', 'UTF-8'), 0, 'L');

        // Restablecer posición para las demás celdas
        $this->SetXY($x + $cellWidths[0], $y);

        // Otras columnas
        $this->Cell($cellWidths[1], 5, $quantity, 0, 0, 'C');
        $this->Cell($cellWidths[2], 5, number_format($unitPrice, 2, ',', ' ') . " " . chr(128), 0, 0, 'C');
        $this->Cell($cellWidths[3], 5, number_format(0, 2, ',', ' ') . "%", 0, 0, 'C');
        $this->Cell($cellWidths[4], 5, number_format(0, 2, ',', ' ') . chr(128), 0, 0, 'C');
        $this->Cell($cellWidths[5], 5, number_format($unitPrice * $quantity, 2, ',', ' ') . chr(128), 0, 1, 'C');

        // Añadir el espaciado entre filas
        $this->Ln(5);
    }

    // Condición de pago en la última página
    function PaymentCondition($text, $textoCondiciones = "")
    {
        $rectX = 8; // Margen izquierdo
        $rectY = $this->GetY(); // Espaciado adicional desde el contenido anterior (desglose)
        $rectWidth = 90; // Ancho del rectángulo
        $cellHeight = 5; // Altura de cada línea

        // Texto de protección dinámico
        $textoFormateado = mb_convert_encoding($textoCondiciones, 'ISO-8859-1', 'UTF-8');
        $numLinesTexto = $this->NbLines($rectWidth - 6, $textoFormateado); // Espacio interno de 6
        $requiredHeightTexto = $numLinesTexto * $cellHeight;

        // Considerar también la línea de "Forma de pago"
        $numLinesPago = 1; // Siempre 1 línea para "Forma de pago"
        $requiredHeightPago = $numLinesPago * $cellHeight;

        // Altura total del rectángulo
        $rectHeight = $requiredHeightTexto + $requiredHeightPago + 6; // +6 para márgenes internos

        // Título fuera del rectángulo
        $this->SetFont('Arial', 'B', 10);
        $this->SetXY($rectX, $rectY - 7); // Justo arriba del rectángulo
        $this->Cell($rectWidth, 5, 'Condiciones', 0, 1, 'L');

        // Dibujar el rectángulo con altura ajustada
        $this->SetDrawColor(0, 0, 0); // Color del borde
        $this->Rect($rectX, $rectY, $rectWidth, $rectHeight);

        // "Forma de pago" (con título en negrita)
        $this->SetFont('Arial', 'B', 9);
        $this->SetXY($rectX + 3, $rectY + 3); // Margen interno
        $this->Cell(25, $cellHeight, 'Forma de pago:', 0, 0, 'L'); // Título

        $this->SetFont('Arial', '', 9);
        $this->Cell($rectWidth - 28, $cellHeight, mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L'); // Valor

        // Agregar texto adicional si existe
        if (!empty($textoCondiciones)) {
            $this->SetX($rectX + 3); // Alinear con el margen interno
            $this->MultiCell($rectWidth - 6, $cellHeight, $textoFormateado, 0, 'L');
        }
    }

    // Método adicional para calcular líneas necesarias en una celda
    function NbLines($width, $text)
    {
        $cw = $this->CurrentFont['cw'];
        if ($width == 0) {
            return 0;
        }
        $wmax = ($width - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $text);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    function Totales($base, $iva, $retencion, $total)
    {

        
        $this->SetDrawColor(0, 0, 0); // Color de la línea
        $this->SetLineWidth(0.2); // Grosor de la línea
        $yStart = $this->GetY(); // Posición Y inicial
        $this->Line(140, $yStart, 200, $yStart);

        // Base Imponible
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(130, 10, '', 0);
        $this->Cell(30, 10, 'Base Imponible', 0, 0);
        $this->Cell(30, 10, number_format($base, 2, ',', ' ') . chr(128), 0, 1, 'R');

        // IVA
        $this->Cell(130, 10, '', 0);
        $this->Cell(30, 10, 'IVA', 0, 0);
        $this->Cell(30, 10, number_format($iva, 2, ',', ' ') . chr(128), 0, 1, 'R');

        // Línea entre IVA y Retención


        // Retención
        /*
        $this->Cell(130, 10, '', 0);
        $this->Cell(30, 10, mb_convert_encoding('Retención', 'ISO-8859-1', 'UTF-8'), 0, 0);
        $this->Cell(30, 10, '-' . number_format($retencion, 2, ',', ' ') . chr(128), 0, 1, 'R');
        */
        // Línea entre Retención y Total
        $yTotal = $this->GetY(); // Posición Y actual
        $this->Line(140, $yTotal, 200, $yTotal);

        // Total
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(130, 10, '', 0);
        $this->Cell(30, 10, 'Total', 0, 0);
        $this->Cell(30, 10, number_format($total, 2, ',', ' ') . chr(128), 0, 1, 'R');

        // Línea inferior
        $yEnd = $this->GetY(); // Posición Y final
        $this->Line(140, $yEnd, 200, $yEnd);
        
        // Línea superior

    }
}

// Datos para la factura
$items = [
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    ['description' => 'Revisión de contenidos da d d asdsdsad sad d ssd sad  s d ds dsadsadsd sdsds ds ddsd s', 'quantity' => 1, 'base' => 30.50, 'iva' => 14.30, 'total' => 45.20, "discount" => 10.40],
    // Agrega más productos para probar múltiples páginas
];

// Texto de la condición de pago
$paymentCondition = "El pago debe realizarse dentro de los 30 días posteriores a la emisión de esta factura. " .
                    "Por favor, utilice la referencia de la factura al realizar el pago. Contacte al departamento de contabilidad si tiene preguntas.";


// Crear PDF
$pdf = new PDF_Invoice();
$pdf->AddPage();
// Encabezado de la tabla
$pdf->PrintTableHeaders();

// Rellenar filas de la tabla
foreach ($items as $item) {
    $pdf->TableRow($item['description'], $item['quantity'], $item['base']);

    // Verificar si necesitamos agregar una nueva página
    if ($pdf->GetY() > 230) { // Margen para evitar solapamiento con el pie de página
        $pdf->AddPage();
        $pdf->PrintTableHeaders(); // Agregar encabezado de la tabla en la nueva página
    }
}

// Total final
$totalFinal = array_reduce($items, function ($sum, $item) {
    return $sum + ($item['quantity'] * $item['base']);
}, 0);

$pdf->Ln(10);

$pdf->Totales($totalFinal, $totalFinal * 0.21, 0, $totalFinal * 1.21);
// $pdf->SetFont('Arial', 'B', 12);
// $pdf->Cell(155, 10, 'Total', 1);
// $pdf->Cell(35, 10, number_format($totalFinal, 2), 1, 1, 'R');

// Agregar condición de pago solo en la última página
$pdf->PaymentCondition("Tarjeta", $paymentCondition);

// Salvar o mostrar PDF
$pdf->Output('I', 'factura.pdf');