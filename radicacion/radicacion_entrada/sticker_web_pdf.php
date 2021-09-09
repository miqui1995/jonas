<?php 
/*  @brief Este archivo es invocado desde el archivo (radicacion/radicacion_entrada/query_modificar.php) mediante require_once pero antes de invocarlo 
*  debe definir las variables 
*   @param {string} ($numero_radicado) Es el numero de radicado generado. Es obligatorio. Ejemplo "2020GC1111000000112"
*   @param {string} ($texto_sticker_entidad) Es el texto que va a aparecer en el sticker de manera vertical. Es obligatorio. Ejemplo "JONAS del Ejército Nacional"; 
*   @param {string} ($nombre_pdf_salida) Es el nombre de la ubicación del "path" o ruta dentro de la bodega_pdf donde se va a guardar el PDF modificado con el stickerWeb.
*       Es obligatorio. Ejemplos "$numero_radicado.pdf";  - "2020GC1111000000112.pdf"; 
*   @param {string} ($fullPathToFile) Es el nombre de la ubicación del "path" o ruta donde se encuentra actualmente el archivo original. Es obligatorio. Ejemplo "3.pdf"; 
*/

use setasign\Fpdi\Fpdi;

/* Se inicia importando las librerías necesarias para leer y generar el PDF */
require_once('../../include/fpdf/fpdf.php');
require_once('../../include/fpdi/src/autoload.php');

/* Se declara la clase PDF para armar el objeto con "$pdf = new PDF"*/
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
$pdf->Rect(1,1,100,5, 'D');  	// Rectangulo (posicion_y,posicion_x,alto,ancho)
// $pdf->Code39(4,2,$numero_radicado);  // Code39(posicion_x, posicion_y, texto_ingresar)
$pdf->SetFont('Arial','B',10);
$pdf->Text(2, 4.5, iconv('UTF-8', 'cp1250', "Radicado No. $numero_radicado"));  	// Texto(posicion_y, posicion_x, texto_ingresar)
$pdf->Text(74, 4.5, iconv('UTF-8', 'cp1250', "Página 1 de $pdf->numPages"));     // Texto(posicion_y, posicion_x, texto_ingresar)

// Inicio con Transformation (Rotar elementos)
$pdf->StartTransform();

$pdf->Rotate(90, 60, 60);  // Rotar_elemento(angulo_rotacion_sentido_antihorario, abcisa_centro_rotacion, ordenada_centro_rotacion)
$pdf->Rect(-151,1,263, 5, 'D');  // Rectangulo (posicion_y,posicion_x,alto,ancho)

// $pdf->Code39(-10,2,$numero_radicado); // Texto(posicion_y, posicion_x, texto_ingresar)
$pdf->Text(-150, 5, iconv('UTF-8', 'cp1250', "Documento radicado como entrada por el Software de Gestión Documental $texto_sticker_entidad No. $numero_radicado"));  	// Texto(posicion_y, posicion_x, texto_ingresar)
// $pdf->Text(-79, 9, iconv('UTF-8', 'cp1250', "por el Software de Gestión Documental $texto_sticker_entidad"));  	// Texto(posicion_y, posicion_x, texto_ingresar)
// $pdf->Text(-79, 13, iconv('UTF-8', 'cp1250', $texto_sticker_entidad));		// Texto(posicion_y, posicion_x, texto_ingresar)

$pdf->Text(85, 5, iconv('UTF-8', 'cp1250', "Página 1 de $pdf->numPages"));  	// Texto(posicion_y, posicion_x, texto_ingresar)

//Detener Transformation
$pdf->StopTransform();


// Este ciclo recorre las paginas desde la segunda cuando aplica
if($pdf->numPages>1) {
    for($i=2;$i<=$pdf->numPages;$i++) {

        $pdf->_tplIdx = $pdf->importPage($i);
        $pdf->AddPage();
        
        $pdf->Rect(1,1,100,5, 'D');   	// Rectangulo (posicion_y,posicion_x,alto,ancho)
        // $pdf->Code39(4,2,$numero_radicado); 	// Texto(posicion_x, posicion_y, texto_ingresar)
        // $pdf->Text(85, 14, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));  	// Texto(posicion_y, posicion_x, texto_ingresar)
        $pdf->Text(2, 4.5, iconv('UTF-8', 'cp1250', "Radicado No. $numero_radicado"));      // Texto(posicion_y, posicion_x, texto_ingresar)
        /* Dependiendo de la cantidad de hojas posiciona el número de paginas. */
        if($i>=2 and $i <10){
            $pdf->Text(74, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));     // Texto(posicion_y, posicion_x, texto_ingresar)
        }else if($i>=10 and $i <100){
            $pdf->Text(72, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));     // Texto(posicion_y, posicion_x, texto_ingresar)
        }else{
            $pdf->Text(70, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));     // Texto(posicion_y, posicion_x, texto_ingresar)
        }


        // Inicio con Transformation (Rotar elementos)
		$pdf->StartTransform();

		$pdf->Rotate(90, 60, 60);  // Rotar_elemento(angulo_rotacion_sentido_antihorario, abcisa_centro_rotacion, ordenada_centro_rotacion)

        $pdf->Rect(-151,1,263, 5, 'D');  // Rectangulo (posicion_y,posicion_x,alto,ancho)

        // $pdf->Code39(-10,2,$numero_radicado); // Texto(posicion_y, posicion_x, texto_ingresar)
        $pdf->Text(-150, 5, iconv('UTF-8', 'cp1250', "Documento radicado como entrada por el Software de Gestión Documental $texto_sticker_entidad No. $numero_radicado"));     // Texto(posicion_y, posicion_x, texto_ingresar)
        // $pdf->Text(-79, 9, iconv('UTF-8', 'cp1250', "por el Software de Gestión Documental $texto_sticker_entidad"));    // Texto(posicion_y, posicion_x, texto_ingresar)
        // $pdf->Text(-79, 13, iconv('UTF-8', 'cp1250', $texto_sticker_entidad));       // Texto(posicion_y, posicion_x, texto_ingresar)


         /* Dependiendo de la cantidad de hojas posiciona el número de paginas. */
        if($i>=2 and $i <10){
            $pdf->Text(85, 5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));      // Texto(posicion_y, posicion_x, texto_ingresar)
        }else if($i>=10 and $i <100){
            $pdf->Text(82, 5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));     // Texto(posicion_y, posicion_x, texto_ingresar)
        }else{
            $pdf->Text(80, 5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));     // Texto(posicion_y, posicion_x, texto_ingresar)
        }




		// $pdf->Rect(-81,1,179, 14, 'D');  // Rectangulo (posicion_y,posicion_x,alto,ancho)

		// $pdf->Code39(-10,2,$numero_radicado); // Texto(posicion_y, posicion_x, texto_ingresar)
		// $pdf->Text(-79, 5, 'Documento radicado como entrada ');  	// Texto(posicion_y, posicion_x, texto_ingresar)
		// $pdf->Text(-79, 9, iconv('UTF-8', 'cp1250', 'por el Software de Gestión Documental'));  	// Texto(posicion_y, posicion_x, texto_ingresar)
		// $pdf->Text(-79, 13, iconv('UTF-8', 'cp1250', $texto_sticker_entidad));		// Texto(posicion_y, posicion_x, texto_ingresar)

		// $pdf->Text(70, 14, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));  	// Texto(posicion_y, posicion_x, texto_ingresar)

		//Detener Transformation
		$pdf->StopTransform();
    }
}

// Mostrar el PDF en la pagina web
// $pdf->Output();

// Crear el PDF en la ubicacion definida como $nombre_pdf_salida
$pdf->Output('F',$nombre_pdf_salida,'true');

?>