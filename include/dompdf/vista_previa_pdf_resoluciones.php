<?php
// var_dump($_POST);
// require_once('../../login/validar_inactividad.php');// Se valida la inactividad 

require_once 'lib/html5lib/Parser.php';
require_once 'lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'lib/php-svg-lib/src/autoload.php';
require_once 'src/Autoloader.php';

// Recibe por POST el nombre del archivo temporal
$html  			= $_POST['html'];
$nombre_archivo = $_POST['nombre_archivo']; 
$tamano 		= $_POST['tamano']; 

Dompdf\Autoloader::register();
$dompdf = new Dompdf\Dompdf();
$dompdf->loadHtml("<html>$html</html>");

if($tamano == "oficio"){
	$dompdf->set_paper("A4", "portrait");
}else{
	$dompdf->set_paper("letter", "portrait");
}

$dompdf->render();

$canvas = $dompdf->get_canvas(); 
if($tamano == "oficio"){
	$canvas->page_text(10, 825, "Página {PAGE_NUM}/{PAGE_COUNT}", "", 6, array(0,0,0)); //header
}else{
	$canvas->page_text(10, 775, "Página {PAGE_NUM}/{PAGE_COUNT}", "", 6, array(0,0,0)); //header
}
// Output the generated PDF to Browser
$dompdf->stream();

if (file_exists("../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.pdf"))
{
    unlink("../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.pdf");
}

file_put_contents(
    "../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.pdf",
    $dompdf->output()
);
chmod("../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.pdf",0777);

// if (file_exists("../../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.html"))
// {
//     unlink("../../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.html");
// }

// file_put_contents(
//     "../../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.html",
//     $_POST['html']
// );
// chmod("../../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.html",0777);

?>