<?php
require('vendor/autoload.php');

use FPDF;

class PDF extends FPDF


{
    public $lastPage = false;
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
    function Header()
    {
        // Logo
        $this->Image('logo.png', 5, 0, 60); // Reemplaza 'logo.png' por el nombre de tu archivo de logo




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
        
        $this->Image('qr.png', 90, 10, 20); // Reemplaza 'logo.png' por el nombre de tu archivo de logo
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

    function Lineas($items)
    {
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);

        // Dibujar línea superior de encabezados
        $this->Line(10, $this->GetY(), 200, $this->GetY());

        // Método para imprimir encabezados
        $this->PrintTableHeaders();

        // Dibujar línea inferior de encabezados
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);

        // Datos de la tabla
        $this->SetFont('Arial', '', 8);
        $lineSpacing = 6; // Espaciado entre filas completas

        // Espacio reservado al footer y totales
        $reservedFooterSpace = 50; // Espacio total que ocupa el footer + totales al final

        foreach ($items as $item) {
            $cellWidths = [80, 20, 20, 20, 20, 30]; // Anchos de cada columna
            $cellHeight = 4;

            // Calcular la altura de la fila
            $lineCounts = [
                $this->NbLines($cellWidths[0], $item['description']),
                1, // Las demás columnas tienen solo una línea
                1,
                1,
                1,
                1
            ];
            $rowHeight = max($lineCounts) * $cellHeight;

            // Calcular espacio restante en la página
            $spaceAvailable = $this->h - $this->GetY() - $this->bMargin;

            // DEPURACIÓN: Verificar valores
            error_log("Espacio restante: {$spaceAvailable}, Altura de la fila: {$rowHeight}, Espacio reservado: {$reservedFooterSpace}");

            // Verificar si se requiere salto de página
            if ($spaceAvailable < $rowHeight + $lineSpacing + $reservedFooterSpace) {
                $this->AddPage();
                // Dibujar línea superior de encabezados en la nueva página
                $this->Line(10, $this->GetY(), 200, $this->GetY());
                $this->PrintTableHeaders();
                $this->SetFont('Arial', '', 8);
                $this->Line(10, $this->GetY(), 200, $this->GetY());
                $this->Ln(5);
            }

            // Imprimir la fila
            $x = $this->GetX(); // Guardar posición inicial
            $y = $this->GetY(); // Guardar posición inicial

            // Descripción
            $this->MultiCell($cellWidths[0], $cellHeight, mb_convert_encoding($item['description'], 'ISO-8859-1', 'UTF-8'), 0, 'L');

            // Restablecer posición para las demás celdas
            $this->SetXY($x + $cellWidths[0], $y);

            // Otras columnas
            $this->Cell($cellWidths[1], $rowHeight, $item['quantity'], 0, 0, 'C');
            $this->Cell($cellWidths[2], $rowHeight, number_format($item['base'], 2, ',', ' ') . " " . chr(128), 0, 0, 'C');
            $this->Cell($cellWidths[3], $rowHeight, number_format($item['discount'], 2, ',', ' ') . "%", 0, 0, 'C');
            $this->Cell($cellWidths[4], $rowHeight, number_format($item['iva'], 2, ',', ' ') . chr(128), 0, 0, 'C');
            $this->Cell($cellWidths[5], $rowHeight, number_format($item['total'], 2, ',', ' ') . chr(128), 0, 1, 'C');

            // Añadir el espaciado entre filas
            $this->Ln($lineSpacing);
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

        if ($this->lastPage) {
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
        }
        // Línea superior

    }






    function Footer()
    {


        $this->SetFont('Arial', '', 8);

        // Condicionar contenido para la última página
        if ($this->lastPage) {

            $texto = "En cumplimiento de la normativa vigente en materia de protección de datos personales, informamos a los usuarios que los datos recabados serán tratados de forma confidencial y utilizados exclusivamente para los fines relacionados con la prestación de servicios y la gestión administrativa correspondiente. \n\nDe acuerdo con el Reglamento General de Protección de Datos (RGPD) y la Ley Orgánica de Protección de Datos y Garantía de Derechos Digitales (LOPDGDD), el usuario tiene derecho a acceder, rectificar y suprimir sus datos personales, así como a ejercer otros derechos reconocidos por la normativa, como el derecho a la portabilidad y a la limitación del tratamiento.";

            // Calcular la altura necesaria para el texto
            $cellWidth = 160; // Ancho de la celda
            $cellHeight = 5; // Altura de cada línea
            $numLines = $this->NbLines($cellWidth, $texto);
            $requiredHeight = $numLines * $cellHeight;

            // Dibujar línea delgada separadora


            // Ajustar la posición del texto dinámicamente
            $this->SetY(- ($requiredHeight + 30)); // Ajusta el margen adicional

            // Imagen en el footer (izquierda)
            $this->Image('qr.png', 0, $this->GetY() + 25, 30);

            // Texto de protección de datos (derecha)

            $this->SetXY(30, -45);
            $this->MultiCell($cellWidth, $cellHeight, mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8'), 0, 'C');
        }

        // Número de página
        $this->SetXY(200, -8);
        $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }

    function Condiciones($formaPago, $textoCondiciones)
    {
        if ($this->lastPage) {
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
            $this->Cell($rectWidth - 28, $cellHeight, mb_convert_encoding($formaPago, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L'); // Valor

            // Agregar texto adicional si existe
            if (!empty($textoCondiciones)) {
                $this->SetX($rectX + 3); // Alinear con el margen interno
                $this->MultiCell($rectWidth - 6, $cellHeight, $textoFormateado, 0, 'L');
            }
        }
        // Configuración inicial

    }

    function EspacioRestante()
    {
        return $this->h - $this->GetY() - $this->bMargin; // Altura total menos la posición actual y el margen inferior
    }
}

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