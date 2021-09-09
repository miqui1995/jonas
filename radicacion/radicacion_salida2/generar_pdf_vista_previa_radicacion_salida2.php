<?php
/* En este archivo se reciben todos los datos mediante POST, se inserta en tabla radicado, datos_origen_radicado, expediente, version_documentos, se genera el archivo PDF y el HTML ya sea nuevo o modificado */
if(!isset($_SESSION)){
	session_start();
}
/* Se valida la inactividad para cerrar sesion o continuar */
require_once("../../login/validar_inactividad.php");

/* Librerias para guardar las versiones de PDF desde ckeditor */
require_once "../../include/dompdf/lib/html5lib/Parser.php";
require_once "../../include/dompdf/lib/php-font-lib/src/FontLib/Autoloader.php";
require_once "../../include/dompdf/lib/php-svg-lib/src/autoload.php";
require_once "../../include/dompdf/src/Autoloader.php";
// require_once '../../include/dompdf/autoload.inc.php';
/* Fin librerias para guardar las versiones de PDF desde ckeditor */

Dompdf\Autoloader::register();

$aleatorio_recibido 	= trim($_POST["nuevo_nombre_documento"]);
$html 					= trim($_POST['html']);
$numero_radicado 		= trim($_POST['numero_radicado']);
$tamano 				= trim($_POST['tamano']); 
$version_documento 		= trim($_POST['version_documento']);


// if($numero_radicado==""){ // En este caso es un radicado nuevo 
// 	$version 	= "1"; // Ya que es un radicado nuevo o una respuesta, la version es la numero 1
// }else{
// 	$version 	= $version_documento;
// }

// Se define el nombre del archivo PDF 
// $nombre_archivo 		= $aleatorio_recibido."_".$version;
$path_pdf  				= $aleatorio_recibido.".pdf";

$dompdf = new Dompdf\Dompdf();
// $dompdf->loadHtml($html);
$dompdf->loadHtml("<html>$html</html>");

if($tamano == "oficio"){
	$dompdf->set_paper("A4", "portrait");
}else{
	$dompdf->set_paper("letter", "portrait");
}

$dompdf->render();

/* Numeración de paginas */
$canvas = $dompdf->get_canvas(); 
if($tamano == "oficio"){
	$canvas->page_text(10, 825, "Página {PAGE_NUM}/{PAGE_COUNT}", "", 6, array(0,0,0)); //header
}else{
	$canvas->page_text(10, 775, "Página {PAGE_NUM}/{PAGE_COUNT}", "", 6, array(0,0,0)); //header
}

// Salida del PDF generado al navegador
// $dompdf->stream();

// Se realiza la creación del archivo PDF con la versión. 
file_put_contents(
    "../../bodega_pdf/plantilla_generada_tmp/$path_pdf",
    $dompdf->output()
);

chmod("../../bodega_pdf/plantilla_generada_tmp/$path_pdf",0777);
// unlink("../../bodega_pdf/plantilla_generada_tmp/$aleatorio_recibido.pdf");

?>