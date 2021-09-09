<?php 

$numero_radicado        = "2020GC1111000000112";
$texto_sticker_entidad  = "JONAS del Ejército Nacional";
$nombre_pdf_salida      = "$numero_radicado.pdf";

$fullPathToFile         = "3.pdf"; 	// Archivo original con paginas múltiples 


use setasign\Fpdi\Fpdi;

require_once('fpdf/fpdf.php');
require_once('fpdi/src/autoload.php');


class PDF extends FPDI {
    var $_tplIdx;

    function Header() {
        global $fullPathToFile;

        if (is_null($this->_tplIdx)) {
            // Toma el numero de paginas
            $this->numPages = $this->setSourceFile($fullPathToFile);
            $this->_tplIdx = $this->importPage(1);
        }
        $this->useTemplate($this->_tplIdx, 0, 0,200);
    }
    function Footer() {}
}

// Iniciar PDF
$pdf = new PDF();

// Agregar una pagina
$pdf->AddPage();

$pdf->SetTextColor(35,108,5);	// Se define color mediante codigo color RGB
$pdf->Rect(1,1,112,14, 'D');  	// Rectangulo (posicion_y,posicion_x,alto,ancho)
$pdf->Code39(4,2,$numero_radicado);  // Code39(posicion_x, posicion_y, texto_ingresar)
$pdf->Text(88, 14, iconv('UTF-8', 'cp1250', "Página 1 de $pdf->numPages"));  	// Texto(posicion_y, posicion_x, texto_ingresar)

// Inicio con Transformation (Rotar elementos)
$pdf->StartTransform();

$pdf->Rotate(90, 60, 60);  // Rotar_elemento(angulo_rotacion_sentido_antihorario, abcisa_centro_rotacion, ordenada_centro_rotacion)
$pdf->Rect(-81,1,179, 14, 'D');  // Rectangulo (posicion_y,posicion_x,alto,ancho)

$pdf->Code39(-10,2,$numero_radicado); // Texto(posicion_y, posicion_x, texto_ingresar)
$pdf->Text(-79, 5, 'Documento radicado como entrada ');  	// Texto(posicion_y, posicion_x, texto_ingresar)
$pdf->Text(-79, 9, iconv('UTF-8', 'cp1250', 'por el Software de Gestión Documental'));  	// Texto(posicion_y, posicion_x, texto_ingresar)
$pdf->Text(-79, 13, iconv('UTF-8', 'cp1250', $texto_sticker_entidad));		// Texto(posicion_y, posicion_x, texto_ingresar)

$pdf->Text(74, 14, iconv('UTF-8', 'cp1250', "Página 1 de $pdf->numPages"));  	// Texto(posicion_y, posicion_x, texto_ingresar)

//Detener Transformation
$pdf->StopTransform();


// Este ciclo recorre las paginas desde la segunda cuando aplica
if($pdf->numPages>1) {
    for($i=2;$i<=$pdf->numPages;$i++) {

        $pdf->_tplIdx = $pdf->importPage($i);
        $pdf->AddPage();
        
        $pdf->Rect(1,1,112,14, 'D');  	// Rectangulo (posicion_y,posicion_x,alto,ancho)
        $pdf->Code39(4,2,$numero_radicado); 	// Texto(posicion_x, posicion_y, texto_ingresar)
        $pdf->Text(88, 14, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));  	// Texto(posicion_y, posicion_x, texto_ingresar)

        // Inicio con Transformation (Rotar elementos)
		$pdf->StartTransform();

		$pdf->Rotate(90, 60, 60);  // Rotar_elemento(angulo_rotacion_sentido_antihorario, abcisa_centro_rotacion, ordenada_centro_rotacion)
		$pdf->Rect(-81,1,179, 14, 'D');  // Rectangulo (posicion_y,posicion_x,alto,ancho)

		$pdf->Code39(-10,2,$numero_radicado); // Texto(posicion_y, posicion_x, texto_ingresar)
		$pdf->Text(-79, 5, 'Documento radicado como entrada ');  	// Texto(posicion_y, posicion_x, texto_ingresar)
		$pdf->Text(-79, 9, iconv('UTF-8', 'cp1250', 'por el Software de Gestión Documental'));  	// Texto(posicion_y, posicion_x, texto_ingresar)
		$pdf->Text(-79, 13, iconv('UTF-8', 'cp1250', $texto_sticker_entidad));		// Texto(posicion_y, posicion_x, texto_ingresar)

		$pdf->Text(74, 14, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));  	// Texto(posicion_y, posicion_x, texto_ingresar)

		//Detener Transformation
		$pdf->StopTransform();
    }
}

// Mostrar el PDF en la pagina web
// $pdf->Output();
$pdf->Output('F',$nombre_pdf_salida,'true');
?>