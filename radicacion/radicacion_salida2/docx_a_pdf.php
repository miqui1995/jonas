<?php 
/* En este archivo se procesa dependiendo del $tipo_solicitud recibido desde radicacion/radicacion_salida2/index_radicacion_salida.php  */
require_once("../../login/validar_inactividad.php");
require_once("../../include/genera_fecha.php");

/**
 * Edita un archivo Word 2007 o mas reciente archivo .docx
 * Se utiliza la extensión ZIP http://php.net/manual/en/book.zip.php
 * para acceder al documento.xml que tiene el lenguaje de marcado para  
 * contenido y formato de un documento de Word
 *
 * En esta funcion reemplazamos las llaves de texto. Usando
 * el estándar XML de Open Office ( https://en.wikipedia.org/wiki/Office_Open_XML )
 * Se puede añadir, modificar o remover contenido o estructura del documento
 * @param string $nombre_archivo_docx
 * @param string $nombre_archivo_resultado
 * @param array  $reemplazos
 *
 * @return bool
 */
function busca_reemplaza_en_documento_word(string $nombre_archivo_docx, string $nombre_archivo_resultado, array $reemplazos): bool{
    if (copy($nombre_archivo_docx, $nombre_archivo_resultado)){
        // Se crea el objeto.
        $zip = new ZipArchive();

        // Abre el archivo de Microsoft Word .docx como si fuera un archivo zip (porque lo es...) 
        if ($zip->open($nombre_archivo_resultado, ZipArchive::CREATE) !== true) {
            return false;
        }
        // Obtenga el archivo document.xml del subdirectorio de Word en el archivo. 
        $xml = $zip->getFromName('word/document.xml');
        $zip->addFile("../../imagenes/encabezado_transparente_navidad.png","/word/media/img3.png");

        // Se reemplaza
        $xml = str_replace(array_keys($reemplazos), array_values($reemplazos), $xml);

        // // Escribe de nuevo en el documento y cierra el objeto. 
        // if (false === $zip->addFromString('word/document.xml', $xml)) {
        //     return false;
        // }
        // $zip->close();

        // return true;
    }
    return false;
}

/* En esta funcion se valida si existe en el documento .docx la variable para poner las firmas electronicas si corresponde */
function valida_variable_firma_electronica_jefe(string $nombre_archivo_docx, string $nombre_archivo_resultado){
    if (copy($nombre_archivo_docx, $nombre_archivo_resultado)){
        // Se crea el objeto.
        $zip = new ZipArchive();

        // Abre el archivo de Microsoft Word .docx como si fuera un archivo zip (porque lo es...) 
        if ($zip->open($nombre_archivo_resultado, ZipArchive::CREATE) !== true) {
            // return false;
            exit();
        }

        // Obtenga el archivo document.xml del subdirectorio de Word en el archivo. 
        $xml = $zip->getFromName('word/document.xml');

        $pos = strpos($xml, "{firma_electronica_jefe}");

        /* Retorna valor si se encuentra "{firma_electronica_jefe}" en el .docx */
       	return ($pos===false)?"NO":"SI";
     
        // Escribe de nuevo en el documento y cierra el objeto. 
        if (false === $zip->addFromString('word/document.xml', $xml)){}
        $zip->close();
    }
}

/* Toma las variables recibidas mediante POST */
$tamano 			= $_POST['tamano'];
$tipo_solicitud 	= $_POST['tipo_solicitud'];
$numero_radicado 	= $_POST['numero_radicado']; // Si viene vacía quiere decir que es un radicado nuevo, de lo contrario es una modificacion.

switch ($tipo_solicitud) {
	case 'vista_previa':
		$ext_file = $_POST['tipo_file2']; // ".doc o .docx"
		/* Primero verifica si ha llegado el archivo docx */
		if ( 0 < $_FILES['plantilla_docx_firma_electronica']['error'] ) {
		    echo 'Error: ' . $_FILES['file']['error'] . '<br>';
		}else{
			/* Si el archivo se recibió correctamente. Toma el archivo cargado en docx o doc y lo 
			convierte en PDF con el mismo nombre que traía. */

			$target_dir 	= "../../bodega_pdf/plantilla_origen/";
			// $target_file = basename($_FILES["plantilla_docx_firma_electronica"]["name"]); // Nombre que trae el Archivo
			$target_file 	= ($numero_radicado=="")?$login.$ext_file:$numero_radicado.$ext_file; 

			$nombre_archivo_resultado = "";
			if(move_uploaded_file($_FILES["plantilla_docx_firma_electronica"]["tmp_name"],$target_dir.$target_file)){
				/* Utiliza la funcion (valida_variable_firma_electronica_jefe()) */
				$nombre_archivo_docx        = $target_dir.$target_file;
				$nombre_archivo_resultado   = $target_dir."2".$target_file;
				$nombre_archivo_resultado2 	= "../bodega_pdf/plantilla_origen/2".$target_file;

				$modificar_docx = valida_variable_firma_electronica_jefe($nombre_archivo_docx, $nombre_archivo_resultado);

				if($modificar_docx=="NO"){
					/* Muestra #error_variable_firmas */
					echo "<script>$('#error_variable_firmas').slideDown('slow')</script>";

				}else{
					echo "<script>$('#error_variable_firmas').slideUp('slow')</script>";

					// $reemplazos = ['{test-placeholder-1}' => 'ESO ES JONAS ASI SE HACE','{test-placeholder-2}' => 'test successful 2','{test-placeholder-3}' => 'test successful 3','{test-placeholder-4}' => 'test successful 4',];
					$reemplazos = [
					    "{firma_electronica_jefe}" => "<h1>ewer</h1>"
					];

					$modificar_docx = busca_reemplaza_en_documento_word($nombre_archivo_docx, $nombre_archivo_resultado, $reemplazos);

					echo $modificar_docx ? "Creado correctamente" : 'Falló!';
				}



// 		$nombre_archivo_resultado1 	= "../bodega_pdf/tmp/$target_file";
// 		$nombre_archivo_resultado2  = "../bodega_pdf/tmp/"."2"."$target_file";
		
// 		$despedida_doc = "Cordial Saludo";
// 		$firmante_doc = "Johnnatan Rodriguez Pinto";
// 		$cargo_firmante_doc = "CEO Gamma Corp SAS";
// 		$elabora_doc = "Lorena Parra";
// 		$cargo_elabora_doc = "Secretaria del Alcalde";
// 		$aprueba_doc_a ="1111";
// 		$revisa_doc1_a = "2222";
// 		$revisa_doc2_a ="33333";
// 		$revisa_doc3_a ="444";
// 		$revisa_doc4_a ="55555";
// 		$revisa_doc5_a ="66666";
// 		$anexos_a = "77777"; 

// 		$contenido_firma = "<table border='0' style='font-size:12px; margin-left:-5px; width: 650px;'>
//                 <tr>
//                     <td style='font-size: 14px;font-weight: bold; text-align:left; width:500px;'>
//                     	$despedida_doc
//                     </td>
//                 </tr>
//                 <tr>
//                     <td style='font-size: 14px;font-weight: bold; text-align:left; width:500px;'>
//                     	<br><br>
//                         $firmante_doc
//                         <br>
//                         <span style='margin_top:-25px;'>$cargo_firmante_doc</span>
//                     </td>
//                    	<td rowspan='2' style='font-size:8px; text-align: center; font-weight: bold;'>
// 	                    <img src='$filename' width='80px' height='80px'><br>$radicado
// 	                </td>
//                 </tr>
//                	<tr>
//                		<td style='font-size:8px; padding_top:10px; width:100px;'>
//                			<font style='font-weight:bold;'>Elaborado por: </font>$elabora_doc ($cargo_elabora_doc) $aprueba_doc_a $revisa_doc1_a $revisa_doc2_a $revisa_doc3_a $revisa_doc4_a $revisa_doc5_a $anexos_a <------------------
//                		</td>
//                	</tr>
//             </table>";
			








		
// 		sleep(3);

	

// 		if(shell_exec('cd ../../bodega_pdf/tmp/;
// 			export HOME=/tmp && libreoffice --headless;
// 			lowriter --convert-to pdf *.doc;
// 			lowriter --convert-to pdf *.docx;')){
// 			echo "<script>console.log('SIII')</script>";
// 			chmod("$target_dir",0777);
// 		}else{

// 			echo "<script>console.log('NOO')</script>";
// 		}
// 		$ruta_actual4 = shell_exec('pwd');
// 		echo "$ruta_actual4";

		
			}else{
				echo "No se pudo cargar el archivo (doc/docx). Comuníquese con el administrador del sistema";
			}
		}
		break;
	
	default:
		# code...
		break;
}
// $radicado 				= "202123452345";

// /****************************************************************************************/
// /* Inicia con la generación del QR para poner en el radicado */
//   /* Genera codigo aleatorio para codigo_verificacion */
//     $permitted_chars        = '2345789abcdefghjkmnpqrstuvwxyz';
//     $codigo_verificacion    = substr(str_shuffle($permitted_chars), 0, 15);
// /* Hereda la variable $codigo_entidad desde (../../login/validar_inactividad) */
// switch ($codigo_entidad) {
//     case 'EJC':
//     case 'EJEC':
//         $logo           = '../../imagenes/logos_entidades/imagen_qr_ejc.png'; //logo de la entidad dentro del QR
//         break;

//     default:
// 		$logo           = '../../imagenes/logo3.png'; //logo de la entidad dentro del QR
//         break;
// }

// /* Agregar el script con la librería para generar el QR */
// require ('../../include/phpqrcode/qrlib.php');

// /* Se crea el enlace hacia la capeta temporal con el nombre del usuario para guardar los codigos QR generados (Ej. qr_ALUMNO2.png) */
// $filename   = "../../bodega_pdf/qr_usuario/qr_$login".".png";

// /* En esta variable se genera el QR e indica cada uno de los datos que se envían a la direccion https://xxxxxx y las variables que se envían por GET */
// $cod        = "https://www.gammacorp.co/consultaweb.php?numero_radicado=$radicado&codigo_verificacion=$codigo_verificacion&codigo_entidad=$codigo_entidad&canal_respuesta=mail&amp"; 

// $tam        = "8"; //tamaño de la imagen qr
// $niv        = "H"; //nivel de seguridad o complejidad del QR del 1 al 5 o "H" (Higher) para el máximo 
// $marco      = 0;  // Marco del QR es tranparente.

// /* clase Qrcode:: funcion png para generar el QR en una imagen png */
// QRcode::png($cod,$filename , $niv, $tam, $marco);

// $QR = $filename;                // Archivo original generado con codigo QR

// /* Si existe el logo para crear en el centro del QR*/
// if (file_exists($logo)) {
//     $QR             = imagecreatefromstring(file_get_contents($QR));    // Imagen destino como recurso de conexion
//     $logo           = imagecreatefromstring(file_get_contents($logo));  // Recurso de la fuente de la imagen.
//     $QR_width       = imagesx($QR);                         // Ancho de la imagen QR original
//     $QR_height      = imagesy($QR);                         // Alto de la imagen QR original
//     $logo_width     = imagesx($logo);                       // Ancho del logo 
//     $logo_height    = imagesy($logo);                       // Alto del logo
//     $logo_qr_width  = $QR_width/3;                          // Ancho del logo despues de la combinacion  (1 / 5 del codigo QR)
//     $scale          = $logo_width/$logo_qr_width;           // Ancho escalado del logo (Ancho propio / Ancho combinado)
//     $logo_qr_height = $logo_height/$scale;                  // Alto del logo despues de combinacion
//     $from_width     = ($QR_width - $logo_qr_width) / 2;     // Punto de coordenada desde la esquina izquierda superior del logo despues de combinacion 

//     /* Recombinar y redimensionar imagenes*/
//     /* imagecopyresampled()  Copia el cuadro de una area desde una imagen (imagen origen) a otra.*/
//     imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
// }

// /* Salida de imagenes */
// imagepng($QR, $filename);
// imagedestroy($QR);
// /* Fin de la generación del QR para poner en el radicado */
// /****************************************************************************************/





// /*  @brief Este archivo es invocado desde el archivo (radicacion/radicacion_entrada/query_modificar.php) mediante require_once pero antes de invocarlo 
// *  debe definir las variables 
// *   @param {string} ($numero_radicado) Es el numero de radicado generado. Es obligatorio. Ejemplo "2020GC1111000000112"
// *   @param {string} ($texto_sticker_entidad) Es el texto que va a aparecer en el sticker de manera vertical. Es obligatorio. Ejemplo "JONAS del Ejército Nacional"; 
// *   @param {string} ($nombre_pdf_salida) Es el nombre de la ubicación del "path" o ruta dentro de la bodega_pdf donde se va a guardar el PDF modificado con el stickerWeb.
// *       Es obligatorio. Ejemplos "$numero_radicado.pdf";  - "2020GC1111000000112.pdf"; 
// *   @param {string} ($fullPathToFile) Es el nombre de la ubicación del "path" o ruta donde se encuentra actualmente el archivo original. Es obligatorio. Ejemplo "3.pdf"; 
// */

// $numero_radicado        = "2020GC1111000000112";
// $radicado 				= $numero_radicado;
// $texto_sticker_entidad  = "JONAS de Gamma Corp SAS";
// $nombre_pdf_salida      = "../../bodega_pdf/tmp/aaa.pdf";

// $fullPathToFile         = "../../bodega_pdf/tmp/2bbb.pdf"; 	// Archivo original con paginas múltiples 

// switch ($codigo_entidad) {
// 	case 'value':
// 		# code...
// 		break;
	
// 	default:
// 		// $pie_documento_entidad = "../../imagenes/logos_entidades/logo_gea.png";
// 		$encabezado_documento_entidad  	= "../../imagenes/logos_entidades/encabezado_rad_gc1.png";
// 		$pie_documento_entidad  		= "../../imagenes/logos_entidades/pie_rad_gc1.png";
// 		break;
// }





// use setasign\Fpdi\Fpdi;

// /* Se inicia importando las librerías necesarias para leer y generar el PDF */
// require_once('../../include/fpdf/fpdf.php');
// require_once('../../include/fpdi/src/autoload.php');

// /* Se declara la clase PDF para armar el objeto con "$pdf = new PDF"*/
// class PDF extends FPDI {
//     var $_tplIdx;

//     function Header() {
//         global $fullPathToFile;

//         if (is_null($this->_tplIdx)) {
//             // Toma el numero de paginas
//             $this->numPages = $this->setSourceFile($fullPathToFile);
//             $this->_tplIdx = $this->importPage(1);
//         }
//         $this->useTemplate($this->_tplIdx, 0, 0,200);
//     }
//     function Footer() {}
// }

// // Iniciar PDF

// /* Define el tamaño de la hoja. Carta u oficio */
// if($tamano=="carta"){
// 	$pdf=new PDF('P','mm','LETTER');
// }else{
// 	$pdf=new PDF('P','mm','A4');
// }

// // $pdf = new PDF();

// // Agregar una pagina
// $pdf->AddPage();

// // Image(path_ruta, posicion_y, posicion_x, ancho_imagen, alto_imagen)
// $pdf->Image("$encabezado_documento_entidad",0,0,211,31);

// 	// Image(path_ruta, posicion_y, posicion_x, ancho_imagen, alto_imagen)
// $tamano=="carta" ? $pdf->Image($pie_documento_entidad,0,249,217,31):$pdf->Image($pie_documento_entidad,0,267,216,31);

// /* Codigo QR en todas las paginas */
// $tamano=="carta" ? $pdf->Image("$filename",1,253,25,25):$pdf->Image("$filename",1,271,25,25);


// /* Se escribe el encabezado dependiendo de la entidad (Hay que hacerlo en este y tambien en el otro switch para paginas >1) */
// switch ($codigo_entidad) {
// 	case 'value':
// 		# code...
// 		break;
	
// 	default:
// 		/* Numero de radicado y fecha */
//        	$pdf->SetFont('Arial','BI',12);
// 		/* Texto(posicion_y, posicion_x, texto_ingresar) */
// 		$pdf->Text(70, 35, iconv('UTF-8', 'cp1250', "Radicado No. $numero_radicado"));      // Texto(posicion_y, posicion_x, texto_ingresar)
// 		$pdf->Text(87, 40, iconv('UTF-8', 'cp1250', "$fecha_e2"));      // Texto(posicion_y, posicion_x, texto_ingresar)

// 		/* Foliacion electronica */
// 		$pdf->SetFont('Arial','B',10);
//         /* Dependiendo de la cantidad de hojas posiciona el número de paginas. */
    
//         	$tamano=="carta" ? $pdf->Text(190, 4.5, iconv('UTF-8', 'cp1250', "Página 1 de $pdf->numPages")):$pdf->Text(181, 4.5, iconv('UTF-8', 'cp1250', "Página 1 de $pdf->numPages"));
      
//         /* Se vuelve a tamaño de letra Arial Negrilla tamaño 10 */
// 		$pdf->SetFont('Arial','B',8);
//         // Inicio con Transformation (Rotar elementos)
// 		$pdf->StartTransform();

// 		$pdf->Rotate(90, 60, 60);  // Rotar_elemento(angulo_rotacion_sentido_antihorario, abcisa_centro_rotacion, ordenada_centro_rotacion)

// 		if($tamano=="carta"){
// 	        $pdf->Rect(-131,1,150, 6.5, 'D');  // Rectangulo (posicion_y,posicion_x,alto,ancho)
// 	        $pdf->Text(-130, 3.5, iconv('UTF-8', 'cp1250', "Documento radicado como entrada por el Software de Gestión Documental $texto_sticker_entidad"));     // Texto(posicion_y, posicion_x, texto_ingresar)
// 	        $pdf->Text(-130, 7, iconv('UTF-8', 'cp1250', "Puede verificar la veracidad de la firma electrónica de este documento escaneando el codigo QR"));    // Texto(posicion_y, posicion_x, texto_ingresar)
// 		}else{
// 	        $pdf->Rect(-149,1,150, 6.5, 'D');  // Rectangulo (posicion_y,posicion_x,alto,ancho)
// 	        $pdf->Text(-148, 3.5, iconv('UTF-8', 'cp1250', "Documento radicado como entrada por el Software de Gestión Documental $texto_sticker_entidad"));     // Texto(posicion_y, posicion_x, texto_ingresar)
// 	        $pdf->Text(-148, 7, iconv('UTF-8', 'cp1250', "Puede verificar la veracidad de la firma electrónica de este documento escaneando el codigo QR"));    // Texto(posicion_y, posicion_x, texto_ingresar)					
// 		}

// 		//Detener Transformation
// 		$pdf->StopTransform();
// 		break;
// }

// // Este ciclo recorre las paginas desde la segunda cuando aplica
// if($pdf->numPages>1) {
//     for($i=2;$i<=$pdf->numPages;$i++) {

//         $pdf->_tplIdx = $pdf->importPage($i);
//         $pdf->AddPage();

// 		// Image(path_ruta, posicion_y, posicion_x, ancho_imagen, alto_imagen)
//         $pdf->Image("$encabezado_documento_entidad",0,0,211,31);

//    		// Image(path_ruta, posicion_y, posicion_x, ancho_imagen, alto_imagen)
//         $tamano=="carta" ? $pdf->Image($pie_documento_entidad,0,249,217,31):$pdf->Image($pie_documento_entidad,0,267,216,31);

//         /* Codigo QR en todas las paginas */
//         $tamano=="carta" ? $pdf->Image("$filename",1,253,25,25):$pdf->Image("$filename",1,271,25,25);
        
//         /* Se escribe el encabezado dependiendo de la entidad */
//         switch ($codigo_entidad) {
//         	case 'value':
//         		# code...
//         		break;
        	
//         	default:
//         		/* Numero de radicado y fecha */
// 		       	$pdf->SetFont('Arial','BI',12);
//         		/* Texto(posicion_y, posicion_x, texto_ingresar) */
//         		$pdf->Text(70, 35, iconv('UTF-8', 'cp1250', "Radicado No. $numero_radicado"));      // Texto(posicion_y, posicion_x, texto_ingresar)
//         		$pdf->Text(87, 40, iconv('UTF-8', 'cp1250', "$fecha_e2"));      // Texto(posicion_y, posicion_x, texto_ingresar)

//         		/* Foliacion electronica */
// 				$pdf->SetFont('Arial','B',10);
// 		        /* Dependiendo de la cantidad de hojas posiciona el número de paginas. */
// 		        if($i>=2 and $i <10){
// 		        	$tamano=="carta" ? $pdf->Text(190, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages")):$pdf->Text(185, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));

// 		            // $pdf->Text(190, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));     // Texto(posicion_y, posicion_x, texto_ingresar)
// 		            // $pdf->Text(74, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));     // Texto(posicion_y, posicion_x, texto_ingresar)
// 		        }else if($i>=10 and $i <100){
// 		        	$tamano=="carta" ? $pdf->Text(188, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages")):$pdf->Text(183, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));
// 		        }else{
// 		        	$tamano=="carta" ? $pdf->Text(186, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages")):$pdf->Text(181, 4.5, iconv('UTF-8', 'cp1250', "Página $i de $pdf->numPages"));
// 		        }

// 		        /* Se vuelve a tamaño de letra Arial Negrilla tamaño 10 */
// 				$pdf->SetFont('Arial','B',8);
// 		        // Inicio con Transformation (Rotar elementos)
// 				$pdf->StartTransform();

// 				$pdf->Rotate(90, 60, 60);  // Rotar_elemento(angulo_rotacion_sentido_antihorario, abcisa_centro_rotacion, ordenada_centro_rotacion)

// 				if($tamano=="carta"){
// 			        $pdf->Rect(-131,1,150, 6.5, 'D');  // Rectangulo (posicion_y,posicion_x,alto,ancho)
// 			        $pdf->Text(-130, 3.5, iconv('UTF-8', 'cp1250', "Documento radicado como entrada por el Software de Gestión Documental $texto_sticker_entidad"));     // Texto(posicion_y, posicion_x, texto_ingresar)
// 			        $pdf->Text(-130, 7, iconv('UTF-8', 'cp1250', "Puede verificar la veracidad de la firma electrónica de este documento escaneando el codigo QR"));    // Texto(posicion_y, posicion_x, texto_ingresar)
// 				}else{
// 			        $pdf->Rect(-149,1,150, 6.5, 'D');  // Rectangulo (posicion_y,posicion_x,alto,ancho)
// 			        $pdf->Text(-148, 3.5, iconv('UTF-8', 'cp1250', "Documento radicado como entrada por el Software de Gestión Documental $texto_sticker_entidad"));     // Texto(posicion_y, posicion_x, texto_ingresar)
// 			        $pdf->Text(-148, 7, iconv('UTF-8', 'cp1250', "Puede verificar la veracidad de la firma electrónica de este documento escaneando el codigo QR"));    // Texto(posicion_y, posicion_x, texto_ingresar)					
// 				}

// 				//Detener Transformation
// 				$pdf->StopTransform();
//         		break;
//         }	
//     }
// }

// // Mostrar el PDF en la pagina web
// // $pdf->Output();

// // Crear el PDF en la ubicacion definida como $nombre_pdf_salida
// $pdf->Output('F',$nombre_pdf_salida,'true');





?>
 <script type="text/javascript">
	/* Eliminar luego de 10 segundos el archivo DOCX generado modificado */
	setTimeout(function(){ 	 
		var nombre_archivo_eliminar = <?php echo "'$nombre_archivo_resultado2'"; ?>;
		console.log(nombre_archivo_eliminar)
		$.ajax({
			type: 'POST',
			url: 'include/procesar_ajax.php',
			data: {
				'recibe_ajax' 		: 'eliminar_temporal',
				'nombre_archivo' 	: nombre_archivo_eliminar
			},          
			success: function(respuesta){
				if(respuesta!=""){
					$("#resultado_js").html(respuesta); // ejectuar 
				}
			}
		})	
	}, 10000);	
 </script>
