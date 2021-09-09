<?php
require_once 'dompdf/lib/html5lib/Parser.php';
require_once 'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'dompdf/src/Autoloader.php';
$nombre_archivo = $_POST['nombre_archivo'];
Dompdf\Autoloader::register();
$dompdf = new Dompdf\Dompdf();
$dompdf->loadHtml("<html>".$_POST['html']."</html>");
$dompdf->render();
$dompdf->stream();
if (file_exists("../../../bodega_pdf/plantilla_generada_tmp/".$nombre_archivo.".pdf")){
    unlink("../../../bodega_pdf/plantilla_generada_tmp/".$nombre_archivo.".pdf");
}
file_put_contents(
    "../../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.pdf",
    $dompdf->output()
);
chmod("../../../bodega_pdf/plantilla_generada_tmp/$nombre_archivo.pdf",0777);
?>