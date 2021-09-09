<?php
/* En este archivo se reciben todos los datos mediante POST, se inserta en tabla radicado, datos_origen_radicado, expediente, version_documentos, se genera el archivo PDF y el HTML ya sea nuevo o modificado */
if(!isset($_SESSION)){
	session_start();
}
/* Se valida la inactividad para cerrar sesion o continuar */
require_once("../../login/validar_inactividad.php");

/* Librerias para guardar las versiones de PDF desde ckeditor */
require_once 'src-php/dompdf/lib/html5lib/Parser.php';
require_once 'src-php/dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'src-php/dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'src-php/dompdf/src/Autoloader.php';
/* Fin librerias para guardar las versiones de PDF desde ckeditor */


Dompdf\Autoloader::register();

/* Se definen todas las variables que recibe por POST */
$fecha 					= date('Y-m-d H:i:s');	// Genera la fecha de transaccion

$aleatorio_recibido 	= $_POST["nameArchive"];
$anexos 		 	 	= $_POST['anexos_doc'];
$aprueba_doc 		 	= $_POST['aprueba_doc'];
$asunto_doc 			= $_POST['asunto_doc'];
$cargo_aprueba_doc 		= $_POST['cargo_aprueba_doc'];
$cargo_destinatario  	= $_POST['cargo_destinatario'];
$cargo_elabora_doc 		= $_POST['cargo_elabora_doc'];
$cargo_firmante_doc 	= $_POST['cargo_firmante_doc'];
$cc_doc 				= $_POST['cc_doc'];
$codigo_contacto 		= $_POST['codigo_contacto'];
$codigo_serie 		  	= $_POST['codigo_serie'];
$codigo_subserie 		= $_POST['codigo_subserie'];
$despedida_doc 			= $_POST['despedida_doc'];
$destinatario 			= $_POST['destinatario'];
$direccion 			 	= $_POST['direccion_doc'];
$elabora_doc 		  	= $_POST['elabora_doc'];
$empresa_destinatario 	= $_POST['empresa_destinatario_doc'];
$firmante			 	= $_POST['firmante_doc'];
$html 					= $_POST['html'];
$id_expediente 			= $_POST['id_expediente'];
$mail				 	= $_POST['mail_doc'];
$numero_radicado 		= $_POST['numero_radicado'];
$pre_asunto1 			= $_POST['pre_asunto'];
$telefono 			 	= $_POST['telefono_doc'];
$tratamiento 			= $_POST['tratamiento_doc'];
$ubicacion 			 	= $_POST['ubicacion_doc'];
$usuario_actual 		= $_POST['usuario_actual'];
$usuario_radicador 		= $_POST['usuario_radicador'];
$version_documento 		= $_POST['version_documento'];

$pre_asunto = str_replace("'", "", $pre_asunto1);

/* Primero se genera el radicado de salida */
$tipo_radicado 			= $_POST['tipo_radicado']; 		// Tipo de radicado (3- Interna, 1- Entrada, 2- Salida, etc) para heredar a ../../login/validar_consecutivo.php
$tipo_radicacion 		= $_POST['tipo_radicacion']; 	// Tipo de radicacion (respuesta, etc) para heredar a ../../login/validar_consecutivo.php
$year 					= date("Y"); 	// Se obtiene el año en formato 4 digitos 

// Se arma el json para codigo_carpeta1 de la tabla radicado
// Extraigo cada uno de los usuario_actual_derivado	(incluye usuario_actual)
$usu  = explode(",", $usuario_actual);
$max  = sizeof($usu);
$max2 = $max-1;

$aprobado 		= 'NO';
$firmado 		= 'NO';

/* Se arma el json para codigo_carpeta1 de la tabla radicado */
$codigo_carpeta1="'{";

if($max2==0){
	echo "";
}else{
	for ($q=0; $q < $max2; $q++) {  // Genera restricción de usuarios que ya existen.
		$login_usuario_busq = $usu[$q];

		$codigo_carpeta2 = "\"$login_usuario_busq\":{\"codigo_carpeta_personal\":\"entrada\"}";

		if($q==0){
			$codigo_carpeta1 = $codigo_carpeta1.$codigo_carpeta2;
		}else{
			$codigo_carpeta1 = $codigo_carpeta1.",".$codigo_carpeta2;
		}
	}	
	$codigo_carpeta1 = $codigo_carpeta1."}'";
}
/* Fin del armado del json para codigo_carpeta1 de la tabla radicado */

if($numero_radicado=="" or $tipo_radicacion=='respuesta'){ // En este caso es un radicado nuevo o es una respuesta
	$version 	= "1"; // Ya que es un radicado nuevo o una respuesta, la version es la numero 1
	require_once('../../login/validar_consecutivo.php'); 	// Valida si el consecutivo existe y genera el radicado

	// Se define el nombre del archivo PDF y HTML
	$nombre_archivo = $radicado."_".$aleatorio_recibido."_".$version;


	$path_html 		= $nombre_archivo.".html";
	$path_pdf  		= $nombre_archivo.".pdf";

	if($codigo_contacto==""){
		$codigo_contacto = '1';
	}

	$query_radicado="insert into radicado(numero_radicado, fecha_radicado, codigo_carpeta1, numero_guia_oficio, descripcion_anexos, path_radicado, codigo_contacto, dependencia_actual, usuarios_visor, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, clasificacion_radicado, termino, id_expediente, codigo_serie, codigo_subserie, estado_radicado, usuarios_control) 
	 	values('$radicado', '$hora', $codigo_carpeta1, 'No se ha enviado al destinatario todavía, por lo tanto no hay soporte de envío.', '$anexos', '$path_pdf', '$codigo_contacto', '$dependencia_usuario', '$usuario_actual', '$dependencia_usuario', '$login_usuario', '$asunto_doc', '$nivel_seguridad', '$usuario_actual', 'OFICIO', '15', '$id_expediente', '$codigo_serie', '$codigo_subserie', 'en_tramite', '$usuario_actual');";

	 // echo "$usuario_actual";	
	$query_datos_origen_radicado = "insert into datos_origen_radicado(numero_radicado, nombre_remitente_destinatario, dignatario, ubicacion, direccion, telefono, mail) values ('$radicado', '$empresa_destinatario', '$destinatario', '$ubicacion', '$direccion', '$telefono', '$mail');";  	
	$query_version_documento2 = "";

	if($tipo_radicacion=='respuesta'){
		$transaccion_historico 	= "Genera plantilla respuesta a radicado";
		$transaccion 			= "plantilla_respuesta"; 	// Variable para auditoria
	}else{
		$transaccion_historico 	= "Genera plantilla salida";
		$transaccion 			= "plantilla_salida"; 		// Variable para auditoria
	}
}else{	// En este caso es un radicado existente osea para modificar
	$version 	= $version_documento;
	$version2 	= $version-1;
	$radicado 	= $numero_radicado;
	
	$login_usuario 			= $_SESSION['login']; 			// Usuario que hace el radicado
	$dependencia_usuario 	= $_SESSION['dependencia']; 	// Dependencia del usuario que está radicando

	// Se define el nombre del archivo PDF y HTML
	$nombre_archivo = $radicado."_".$aleatorio_recibido."_".$version;

	$path_html 		= $nombre_archivo.".html";
	$path_pdf  		= $nombre_archivo.".pdf";

	$query_radicado = "update radicado set codigo_carpeta1=$codigo_carpeta1, descripcion_anexos='$anexos', path_radicado='$path_pdf',codigo_contacto='$codigo_contacto', dependencia_actual='$dependencia_usuario', usuarios_visor='$usuario_actual', dependencia_radicador='$dependencia_usuario', usuario_radicador='$login_usuario', asunto='$asunto_doc', nivel_seguridad='$nivel_seguridad', leido='$usuario_actual', id_expediente='$id_expediente', codigo_serie='$codigo_serie', codigo_subserie='$codigo_subserie' where numero_radicado='$numero_radicado';";
	$query_datos_origen_radicado = "update datos_origen_radicado set nombre_remitente_destinatario='$empresa_destinatario', dignatario='$destinatario', ubicacion='$ubicacion', direccion='$direccion', telefono='$telefono', mail='$mail' where numero_radicado='$radicado';"; 
	$query_version_documento2="update version_documentos set html_asunto='' where numero_radicado='$numero_radicado' and version='$version2';";

	$transaccion_historico 	= "Modifica plantilla salida";
	$transaccion 			= "modifica_plantilla_salida"; 	// Variable para auditoria
}

$query_version_documento = "insert into version_documentos(numero_radicado, version, usuario_modifica, fecha_modifica, path_html, path_pdf, html_asunto, despedida, con_copia_a, usuario_que_firma, cargo_usuario_que_firma, firmado, usuario_que_aprueba, cargo_usuario_que_aprueba, aprobado, usuario_que_elabora, cargo_usuario_que_elabora, tratamiento, cargo_destinatario)values('$radicado', '$version', '$login_usuario', '$fecha', '$path_html', '$path_pdf', '$pre_asunto', '$despedida_doc', '$cc_doc', '$firmante', '$cargo_firmante_doc', '$firmado', '$aprueba_doc', '$cargo_aprueba_doc', '$aprobado', '$elabora_doc', '$cargo_elabora_doc','$tratamiento','$cargo_destinatario');$query_version_documento2";

$query_radicado_dor_doc = $query_radicado.$query_datos_origen_radicado.$query_version_documento;
// echo "<br><br>query_version_documento<br><br> $query_version_documento<br><br>$query_datos_origen_radicado<br><br>$query_radicado<br><br>";

if($tipo_radicacion=='respuesta'){
	$comentario	 = "Se ha generado la <b>versión $version</b> del radicado $radicado como respuesta al radicado $numero_radicado";	// Variable para historico eventos
	$query_radicado_dor_doc.= "insert into respuesta_radicados(radicado_padre, radicado_respuesta)values('$numero_radicado', '$radicado');"; // Se agrega el insertar en respuesta_radicados para amarrar el antecedente con el consecuente.
}else{
	$comentario	 = "Se ha generado la <b>versión $version</b> del radicado de salida $radicado";	// Variable para historico eventos
}

/* Se arma la query para el expediente si aplica. */ 	
if($id_expediente!=""){
	$query_exp 		= "select lista_radicados, nombre_expediente from expedientes where id_expediente='$id_expediente'";
	$fila_exp 	  		= pg_query($conectado,$query_exp);
	$linea_exp    		= pg_fetch_array($fila_exp);
	$lista_radicados 	= $linea_exp['lista_radicados']; // Listado de radicados en tabla expediente
	$nombre_expediente 	= $linea_exp['nombre_expediente']; // Listado de radicados en tabla expediente

	$lista_radicados.="$radicado,";

	$query_expedientes 	= "update expedientes set lista_radicados='$lista_radicados' where id_expediente='$id_expediente';";
	$query_rad_exp 		= $query_radicado_dor_doc.$query_expedientes;

	$transaccion_historico 	.= " y asocia en expediente";	// Variable para tabla historico_eventos
	$comentario				.= " y se asocia directamente al expediente <b>$id_expediente($nombre_expediente)</b>";	// Variable para historico eventos
	$transaccion 			.= "_expediente"; 	// Variable para auditoria quedaría plantilla_salida_expediente ó modifica_plantilla_salida_expediente
}else{
	$query_rad_exp 	= $query_radicado_dor_doc;	
}
/* Fin de armar la query para el expediente si aplica. */ 	
/* Fin proceso de insertar en base de datos la información del radicado padre y el radicado respuesta */


$logo           = '../../imagenes/logo3.png'; //logo de la entidad dentro del QR

$codigo_entidad = $_SESSION["codigo_entidad"];

switch ($codigo_entidad) {
    case 'AV1':
        $imagen_entidad = "<img src='../../imagenes/logos_entidades/logo_largo_av1.png' style='width:180px; height:100px;'>";
        break;

    case 'EJC':
    case 'EJEC':
        $imagen_entidad = "<img src='../../imagenes/logos_entidades/logo_largo_ejc.png' style='width:180px; height:100px;'>";
        $logo           = '../../imagenes/logos_entidades/imagen_qr_ejc.png'; //logo de la entidad dentro del QR
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
$filename   = "../../bodega_pdf/qr_usuario/qr_$usuario_radicador".".png";

/* En esta variable se genera el QR e indica cada uno de los datos que se envían a la direccion https://xxxxxx y las variables que se envían por GET */
$cod        = "https://www.gammacorp.co/consultaweb.php?numero_radicado=$radicado%26codigo_entidad=".$codigo_entidad."%26canal_respuesta=mail&amp"; 

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
$imagenqr = "<img src='$filename' width='100' height='100'><h5 style='margin-top:2px;'>$radicado</h5>";

$html = str_replace("Codigo Qr no disponible-No se ha asignado todavia radicado", $imagenqr, $html); //

$dompdf = new Dompdf\Dompdf();
// $dompdf->loadHtml($html);
$dompdf->loadHtml("<html>$html</html>");


$dompdf->render();

// Salida del PDF generado al navegador
// $dompdf->stream();

// Se realiza la creación del archivo PDF con la versión. 
file_put_contents(
    "../../bodega_pdf/radicados/$path_pdf",
    $dompdf->output()
);

// Se realiza la creación del archivo HTML con la versión. 
// file_put_contents(
//     "../../bodega_pdf/plantillas_html/$path_html",
//     $_POST['html']
// );
// chmod("../../bodega_pdf/plantillas_html/$path_html",0777);

chmod("../../bodega_pdf/radicados/$path_pdf",0777);
unlink("../../bodega_pdf/plantilla_generada_tmp/$aleatorio_recibido.pdf");

// echo "$query_rad_exp";
if(pg_query($conectado,$query_rad_exp)){		
	/* Desde aqui se genera historico */	
		$creado 	= "$radicado";	// Variable para auditoria

		require_once("../../login/inserta_historico.php");		
	/* Hasta aqui se genera historico */	
}else{
	echo "<script> alert('Ocurrió un error al generar el radicado de salida, por favor contactar con el administrador del sistema.')</script>";
}


?>