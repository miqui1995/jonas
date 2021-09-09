<?php 
/* Este archivo sirve para imprimir un sticker incluyendo codigo QR y codigo de barras, recibe todas las variables mediante GET y se invoca desde
el formulario modificar_radicado, radicacion_rapida y radicacion_entrada_normal. El QR se invoca desde require ('../../include/phpqrcode/qrlib.php'); */

// var_dump($_GET);
$fecha              = $_GET['fecha'];
$numrad             = $_GET['radicado'];
$usuario            = $_GET['usu_radicador'];
$entidad            = $_GET['entidad'];
$codigo_entidad     = $_GET['codigo_entidad'];
$anexo              = $_GET['anexos'];

if($anexo==""){
    $anexo="Sin anexos al momento de radicar";
}

if(isset($_GET['asunto'])){
    $asunto_rad = $_GET['asunto'];
    $asunto     = "<tr><td style='width: 250px;'><b>Asunto: </b>".substr($asunto_rad,0,77)."</td></tr>";
}else{
    $asunto     = "<tr><td style='width: 250px;'><b>Documento recibido mediante módulo de radicacion rapida</b></td></tr>";
}
$logo           = '../../imagenes/logo3.png'; //logo de la entidad dentro del QR

switch ($codigo_entidad) {
    case 'AV1':
        $imagen_entidad = "<img src='../../imagenes/logos_entidades/logo_largo_av1.png' style='width:180px; height:100px;'>";
        break;

    case 'EJC':
    case 'EJEC':
        $imagen_entidad = "<img src='../../imagenes/logos_entidades/logo_largo_ejc.png' style='width:180px; height:100px;'>";
        $logo           = '../../imagenes/logos_entidades/imagen_qr_ejc.png'; //logo de la entidad dentro del QR
        break;

    case 'JBB':
        $imagen_entidad = "<img src='../../imagenes/logos_entidades/logo_largo_jbb.png' style='width:180px; height:100px;'>";
        break;

    case 'L01':
        $imagen_entidad = "<img src='../../imagenes/logos_entidades/logo_largo_l01.png' style='width:180px; height:100px;'>";
        break;

    default:
        $imagen_entidad = "<img src='../../imagenes/iconos/logo_largo.png' style='width:180px; height:100px;'>";
        break;
}

/* agregar el script con la librería para generar el QR */
require ('../../include/phpqrcode/qrlib.php');

/* Se crea el enlace hacia la capeta temporal con el nombre del usuario para guardar los codigos QR generados (Ej. qr_ALUMNO2.png) */
$filename   = "../../bodega_pdf/qr_usuario/qr_$usuario".".png";

/* En esta variable se genera el QR e indica cada uno de los datos que se envían a la direccion https://xxxxxx y las variables que se envían por GET */
$cod        = "https://www.gammacorp.co/consultaweb.php?numero_radicado=$numrad&codigo_entidad=$codigo_entidad&canal_respuesta=mail&amp"; 

$tam        = "8"; //tamaño de la imagen qr
$niv        = "H"; //nivel de seguridad o complejidad del QR del 1 al 5 o "H" (Higher) para el máximo 
$marco      = 0;  // Marco del QR es tranparente.

/* clase Qrcode:: funcion png para generar el QR en una imagen png */
QRcode::png($cod,$filename , $niv, $tam, $marco);

$QR = $filename;                // Archivo original generado con codigo QR

/* Si existe el logo para crear en el centro del QR*/
if (file_exists($logo)) {
    $QR             = imagecreatefromstring(file_get_contents($QR));    // Imagen destino como recurso de conexion
    $logo           = imagecreatefromstring(file_get_contents($logo));  // Recurso de la fuente de la imagen.
    $QR_width       = imagesx($QR);                         // Ancho de la imagen QR original
    $QR_height      = imagesy($QR);                         // Alto de la imagen QR original
    $logo_width     = imagesx($logo);                       // Ancho del logo 
    $logo_height    = imagesy($logo);                       // Alto del logo
    $logo_qr_width  = $QR_width/3;                          // Ancho del logo despues de la combinacion  (1 / 5 del codigo QR)
    $scale          = $logo_width/$logo_qr_width;           // Ancho escalado del logo (Ancho propio / Ancho combinado)
    $logo_qr_height = $logo_height/$scale;                  // Alto del logo despues de combinacion
    $from_width     = ($QR_width - $logo_qr_width) / 2;     // Punto de coordenada desde la esquina izquierda superior del logo despues de combinacion 

    /* Recombinar y redimensionar imagenes*/
    /* imagecopyresampled()  Copia el cuadro de una area desde una imagen (imagen origen) a otra.*/
    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
}

/* Salida de imagenes */
imagepng($QR, $filename);
imagedestroy($QR);

/* Etiqueta img para mostrar el QR */
$imagenqr = "<img src='$filename' width='200' height='200'>";

?>

<html>
<head>
<meta charset="UTF-8">
<title>Sticker web</title>
<style type="text/css">
    body {
        font-family     : Arial, Helvetica, sans-serif;
        margin-bottom   : 0;
        margin-left     : 0;
        margin-right    : 0;
        margin-top      : 0;
        padding-bottom  : 0;
        padding-left    : 0;
        padding-right   : 0;
        padding-top     : 0;
    }

    font{
        line-height     : 100%;
    }
    @font-face{
        font-family: "3of9";
        src: local("?"), url("../../include/iconos/fonts/3OF9_NEW.TTF") format("truetype");
    }
    #barcode{
        font-family     : "3of9";
        font-size       : 55px;
        font-weight     : normal;
    }
</style>
    <script src="../../include/js/sweetalert2.js"></script>
    <script type="text/javascript" src="../../include/js/jquery.js"></script>
    <script type="text/javascript" src="../../include/js/funciones_menu.js"></script>
</head>
<body topmargin="0" leftmargin="0"  onload="window.print();">
    <table cellpadding="5" cellspacing="0" border="0">
        <tr>
            <td colspan='2'>
                <center>
                    <div id="titulo_sticker" size="2">
                        <b>Radicado de Entrada - <?php print_r($entidad); ?></b>
                    </div> 
                </center>   
            </td>
            <td rowspan="5">
                <?php echo $imagenqr ?>
            </td>
        </tr>
        <tr align=center>
            <td rowspan="4">
                <?php echo $imagen_entidad; ?>
            </td>
            <td align="left">
                <b>Fecha Rad: </b><?php echo substr($fecha,0,16);?>
            </td>
        </tr>
        <tr align="left">
            <td>    
                <b>Anexos: </b><?php echo substr($anexo,0,25);?> 
            </td>
        </tr>
        <tr>
            <td>    
                <b>Radicado por:</b> <?php echo substr($usuario, 0,18);?>
            </td>     
        </tr>
        <?php echo $asunto ?>
        <tr>
            <td colspan="3" align="center">
                <div style="font-size: 28px; margin-bottom: -5px; margin-top: -5px;"><?php echo $numrad; ?></div>
                <div id="barcode">*<?php echo $numrad; ?>*</div>
            </td>
        </tr>
    </table>
  <!--   <script>
       auditoria_general('sticker_entrada',<?php // echo "'$numrad'"; ?>)
    </script> -->
</body>
</html>
