<?php
if(!isset($_SESSION)){
    session_start();
}
require_once('../../login/conexion2.php');
require_once('../../login/validar_inactividad.php');
require_once('../../include/genera_fecha.php');
require_once('src-php/dompdf/lib/html5lib/Parser.php');
require_once('src-php/dompdf/lib/php-font-lib/src/FontLib/Autoloader.php');
require_once('src-php/dompdf/lib/php-svg-lib/src/autoload.php');
require_once('src-php/dompdf/src/Autoloader.php');
Dompdf\Autoloader::register();
$radicado 						= $_POST['radicado'];
$numero_radicado_aleatorio 		= $_POST['numero_radicado'];
$tipo_guardar 					= $_POST['tipo_guardar'];
$version 						= $_POST['version'];
$version2 						= $_POST['version2'];
$fecha_radicacion				= date('Y-m-d H:i:s');
$login_usuario_actual 			= $_SESSION['login'];






$anexos 		 	 			= $_POST['anexos'];
$dependencia_usuario 			= $_SESSION['dependencia'];
$usuario_actual 				= $_SESSION['nombre'];
$asunto 						= $_POST['asunto'];
$nivel_seguridad				= $_SESSION['nivel'];
$id_expediente 					= $_POST['id_expediente'];
$codigo_serie 		  			= $_POST['serie'];
$codigo_subserie 				= $_POST['subserie'];
$id_destinatarios 				= $_POST['id_destinatarios'];
$destinatarios 					= $_POST['destinatarios'];
$cargo_destinatarios 			= $_POST['cargo_destinatarios'];
$array_destinatarios 			= explode(",", $destinatarios);
$array_cargo_destinatarios 		= explode(",", $cargo_destinatarios);
$escribir_destinatarios 		= "";
for ($i = 0; $i < count($array_destinatarios); $i++) {
	if ($i !=  0) $escribir_destinatarios .= "<br><br>"; 
 	$escribir_destinatarios .= $array_destinatarios[$i]."<br>".$array_cargo_destinatarios[$i];
}
$fecha 							= $_POST["fecha"];
$html_asunto 					= $_POST['editor'];
$html_asunto2 					= htmlentities($html_asunto, ENT_QUOTES);
$despedida 						= $_POST['despedida'];
$tratamiento 					= $_POST['tratamiento'];
$firmante			 			= $_POST['firmante'];
$elabora 		  				= $_POST['elabora'];
$elabora_login 		  			= $_POST['elabora_login'];
$cargo_elabora 					= "";
if(isset($_POST['cargo_elabora']))$cargo_elabora = $_POST['cargo_elabora'];
$cargo_destinatarios			= $_POST['cargo_destinatarios'];
$id_expediente					= $_POST['id_expediente'];
$id_expediente2 				= $_POST['id_expediente2'];
$imagen_firma 					= "<br><br><br><br>";
$ubicacion 						= $_POST['ubicacion'];
$ubicacion 						= ucwords(strtolower($ubicacion));
$ubicacion2 					= $_POST['ubicacion2'];
$ubicacion2 					= ucwords(strtolower($ubicacion2));
$transaccion_historico 			= "Genera plantilla de Radicadacion Interna";
$transaccion 					= "plantilla_interna"; 		// Variable para auditoria
$comentario	 					= "Se ha generado la <b>versión $version</b> del radicado interno numero $radicado";	// Variable para historico eventos

$usuario_radicador 		= $login_usuario_actual;



$path_radicado 					= $radicado."_".$numero_radicado_aleatorio."_".$version.".pdf";
	$path_radicado2 = $radicado;



if($version == 1 && $version2 == $version){
	$query_radicado = "insert into 
							radicado(
								fecha_radicado,
								numero_radicado,
								numero_guia_oficio,
								dependencia_actual,
								dependencia_radicador,
								usuario_radicador,
								nivel_seguridad,
								leido,
								clasificacion_radicado,
								termino, codigo_serie,
								codigo_subserie,
								estado_radicado
							)
							values(
								'$timestamp',
								'$radicado',
								'No se ha enviado al destinatario todavía, por lo tanto no hay soporte de envío.',
								'$dependencia_usuario',
								'$dependencia_usuario',
								'$login_usuario_actual',
								'$nivel_seguridad',
								'$login_usuario_actual',
								'OFICIO',
								'15',
								'$codigo_serie',
								'$codigo_subserie',
								'en_tramite'
							);";
	$query_datos_origen_radicado = "insert into 
										datos_origen_radicado(
											codigo_datos_origen_radicado,
											numero_radicado
										)
										values(
											'2',
											'$radicado'
										);";  	
	$query_version_documento = "insert into 
									version_documentos(
										numero_radicado,
										version
									)
									values(
										'$radicado',
										'1'
									)";
	$query_rad_exp = $query_radicado.$query_datos_origen_radicado.$query_version_documento;
}else{
	$firmante_login			 		= $_POST['firmante_login'];
	$cargo_firmante 				= $_POST['cargo_firmante'];
	if($firmante_login == $login_usuario_actual){
		$firmado = 'SI';
	}else{
		$firmado = 'NO';
	}
	$aprueba 		 				= $_POST['aprueba'];
	$aprueba_login 		 			= $_POST['aprueba_login'];
	$cargo_aprueba  				= $_POST['cargo_aprueba'];
	if($aprueba_login == $login_usuario_actual){
		$aprobado = 'SI';
	}else{
		$aprobado = 'NO';
	}
	$usuario_visor_and_control = $login_usuario_actual;
	if($destinatarios != ""){
		$destinatarios_2 = "";
		$id_destinatarios_array = explode(",", $id_destinatarios);
		foreach ($id_destinatarios_array as $key => $value) {
			$query_destinatario 	= "select login from usuarios where id_usuario='$value'";
			$fila_destinatario  	= pg_query($conectado,$query_destinatario);
			$linea_destinatario  	= pg_fetch_array($fila_destinatario);
			if($linea_destinatario['login'] != "") {
				$login_destinatario 	= $linea_destinatario['login'];
				$destinatarios_2 	   .= ','.$login_destinatario;
			}
		}
		$usuario_visor_and_control .= $destinatarios_2;
	}	
	$firmante_login_2 = str_replace($login_usuario_actual, "", $firmante_login);
	$elabora_login_2 = str_replace($login_usuario_actual, "", $elabora_login);
	$aprueba_login_2 = str_replace($login_usuario_actual, "", $aprueba_login);
	if($firmante_login_2 != "") 	$usuario_visor_and_control .= ','.$firmante_login_2;
	if($aprueba_login_2 != "") 	$usuario_visor_and_control .= ','.$aprueba_login_2;
	if($elabora_login_2 != "") 	$usuario_visor_and_control .= ','.$elabora_login_2;
	

	$codigo_carpeta1 = "'{";
	$codigo_carpeta1_array = explode(",", $usuario_visor_and_control);
	$i = 0;
	foreach ($codigo_carpeta1_array as $key => $value) {
		if($i != 0)$codigo_carpeta1 .= ",";
		$i++;
		$codigo_carpeta1 .= "\"".$value."\":{\"codigo_carpeta_personal\":\"entrada\"}";
	}
	$codigo_carpeta1 .= "}'";

	$query_radicado = "update 
							radicado
						set 
							codigo_carpeta1 		= ".$codigo_carpeta1.",
							descripcion_anexos 		= '".$anexos."',
							path_radicado 			= '".$path_radicado."',
							dependencia_actual 		= '".$dependencia_usuario."',
							usuarios_visor 			= '".$usuario_visor_and_control."',
							usuarios_control 		= '".$usuario_visor_and_control."',
							asunto 					= '".$asunto."',
							leido 					= '".$login_usuario_actual."',
							id_expediente 			= '".$id_expediente."',
							codigo_serie 			= '".$codigo_serie."',
							codigo_subserie 		= '".$codigo_subserie."'
						where 
							numero_radicado 		= '".$radicado."';";
	$query_datos_origen_radicado = "update
										datos_origen_radicado
									set 
										nombre_remitente_destinatario 	= '$destinatarios',
										ubicacion					  	= '$ubicacion'
									where 
										numero_radicado 	= '$radicado';";  	
	$query_version_documento = "update 
									version_documentos
								set 
									version 					= '$version',
									usuario_modifica 			= '$login_usuario_actual',
									fecha_modifica				= '$fecha',
									path_pdf 					= '$path_radicado',
									html_asunto 				= '$html_asunto2',
									despedida 					= '$despedida',
									tratamiento 				= '$tratamiento',
									usuario_que_firma 			= '$firmante',
									cargo_usuario_que_firma 	= '$cargo_firmante',
									firmado 					= '$firmado',
									usuario_que_aprueba 		= '$aprueba',
									cargo_usuario_que_aprueba 	= '$cargo_aprueba',
									aprobado 					= '$aprobado',
									usuario_que_elabora 		= '$elabora',
									cargo_usuario_que_elabora 	= '$cargo_elabora',
									cargo_destinatario 			= '$cargo_destinatarios'
								where
									numero_radicado = '$radicado';";
	$query_rad_exp = $query_radicado.$query_datos_origen_radicado.$query_version_documento;
	if($id_expediente!=""){
		$radicado_existente = 0;
		$query_exp 		= "select 
								lista_radicados,
								nombre_expediente
							from 
								expedientes
							where 
								id_expediente = '$id_expediente'";
		$fila_exp 	  		= pg_query($conectado,$query_exp);
		$linea_exp    		= pg_fetch_array($fila_exp);
		$lista_radicados 	= $linea_exp['lista_radicados']; // Listado de radicados en tabla expediente
		$lista_radicados_array = explode(",", $lista_radicados);
		foreach ($lista_radicados_array as $key => $value) {
			if($radicado == $value)$radicado_existente++;
		}
		if($radicado_existente == 0){
			if(count($lista_radicados_array) > 0 && $lista_radicados != "")$lista_radicados .= ",";
			$lista_radicados .= $radicado;
			$nombre_expediente 	= $linea_exp['nombre_expediente']; // Listado de radicados en tabla expediente
			$query_expedientes 	= "update 
										expedientes 
									set 
										lista_radicados = '$lista_radicados'
									where 
										id_expediente = '$id_expediente';";
			$query_rad_exp 	.= $query_expedientes;
			$transaccion_historico 	.= " y asocia en expediente";	// Variable para tabla historico_eventos
			$comentario				.= " y se asocia directamente al expediente <b>$id_expediente($nombre_expediente)</b>";	// Variable para historico eventos
			$transaccion 			.= "_expediente"; 	// Variable para auditoria quedaría plantilla_salida_expediente ó modifica_plantilla_salida_expediente
		}
	}
	if($id_expediente2 !=""){
		$radicado_posicion= 0;
		$query_exp 		= "select 
								lista_radicados,
								nombre_expediente
							from
								expedientes
							where 
								id_expediente = '$id_expediente2'";
		$fila_exp 	  		= pg_query($conectado,$query_exp);
		$linea_exp    		= pg_fetch_array($fila_exp);
		$nombre_expediente 	= $linea_exp['nombre_expediente']; // Listado de radicados en tabla expediente
		$lista_radicados 	= $linea_exp['lista_radicados'];// Listado de radicados en tabla expediente
		$lista_radicados_array = explode(",", $lista_radicados);
		foreach ($lista_radicados_array as $key => $value) {
			if($radicado == $value) $radicado_posicion = $key;
		}
		$radicado_posicion++;
		$radicado2 		= $radicado;
		if($radicado_posicion != 1)$radicado2 = ",".$radicado;
		$lista_radicados = str_replace($radicado2, "", $lista_radicados);
		$query_expedientes 	= "update 
									expedientes
								set 
									lista_radicados = '$lista_radicados'
								where 
									id_expediente = '$id_expediente2';";
		$query_rad_exp 	.= $query_expedientes;
		$transaccion_historico 	.= " y se desasocia en expediente";	// Variable para tabla historico_eventos
		$comentario				.= " y se desasocia directamente al expediente <b>$id_expediente($nombre_expediente)</b>";	// Variable para historico eventos
		$transaccion 			.= "_expediente_actualiza_id"; 	// Variable para auditoria quedaría plantilla_salida_expediente ó modifica_plantilla_salida_expediente
	}
}
if($tipo_guardar == 2){
	$codigo_entidad     = $_SESSION['codigo_entidad'];

    switch ($codigo_entidad) {
        case 'AV1':
        case 'EJEC':
            $path_encabezado    = '../../imagenes/logos_entidades/encabezado_rad_av1.png';
            $path_piedepagina   = '../../imagenes/logos_entidades/pie_rad_av1.png';
            break;
        
        default:
            $path_encabezado    = '../../imagenes/encabezado_radicado.png';
            $path_piedepagina   = '../../imagenes/pie_de_pagina_radicado.png';
            break;
    }
    // Extensión de las imagenes de encabezado y pie de pagina para la plantilla
    $type_encabezado    = pathinfo($path_encabezado, PATHINFO_EXTENSION);
    $type_piedepagina   = pathinfo($path_piedepagina, PATHINFO_EXTENSION);
     
    // Cargando las imagenes de encabezado y pie de pagina para la plantilla
    $data_encabezado    = file_get_contents($path_encabezado);
    $data_piedepagina   = file_get_contents($path_piedepagina);
     
    // Decodificando las imagenes de encabezado y pie de pagina en base64
    $base64_encabezado  = 'data:image/' . $type_encabezado . ';base64,' . base64_encode($data_encabezado);
    $base64_piedepagina = 'data:image/' . $type_piedepagina . ';base64,' . base64_encode($data_piedepagina);















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
$imagenqr = "<img src='$filename' width='150' height='150'>";





















	$ubicacion = explode(" - ", $ubicacion);
	$ubicacion = $ubicacion[2].", ".$ubicacion[1];
	$html 	= "<img width='100%' height='80px' src='$base64_encabezado' style='margin-top:-20px'>
				<div style='margin-left: -210px;  position: absolute;'>
			            <h6 >".$_SESSION['nombre_dependencia']."</h6>
			        </div>
		        <h5 align='left' style='margin-top:80px; margin-left:-180px; position: absolute; width:120px'>
		            $imagenqr
		        </h5>
			    <div style='text-align='justify'>
			        <p>
			            $ubicacion, $fecha
			        </p>
			        <p style='font-weight: bold; margin-top:2px'>
			            $tratamiento
			        </p>
			        $escribir_destinatarios
			        <p style='margin-top:20px'>
			            $ubicacion2
			        </p>
			    </div>

			    <p style='font-weight: bold;'>
			        Asunto: $asunto
			    </p>
			    $html_asunto
			    <br>
			    <span style='font-weight: bold;'>$despedida</span><br>
			    $imagen_firma
			    <span style='font-weight: bold;'>$firmante</span><br>
			    <span style='font-weight: bold;'>$cargo_firmante</span><br><br><br>
			    <div style='font-size:8px'>
			    <span style='font-weight: bold; margin-right:28px;'>Anexos :</span> $anexos<br>
			    <span style='font-weight: bold; margin-right:2px;'>Aprobado por : </span> $aprueba - $cargo_aprueba<br>
			    <span style='font-weight: bold; margin-right:2px;'>Elaborado por: </span> $elabora - $cargo_elabora<br>
			    <div id='footer' align='center'>
			    <img width='700px' height='100px' class='center' style=\"position:fixed; left:0px; bottom:0px;\" src='$base64_piedepagina'>
			    </div>";
	$dompdf = new Dompdf\Dompdf();
	$dompdf->loadHtml($html);
	$dompdf->render();
	$dompdf->stream();
	file_put_contents(
	    "../../bodega_pdf/radicados/$path_radicado",
	    $dompdf->output()
	);
	chmod("../../bodega_pdf/radicados/$path_radicado",0777);
	unlink("../../bodega_pdf/plantilla_generada_tmp/$path_radicado2.pdf");
	if(pg_query($conectado,$query_rad_exp)){	
			$creado 	= $radicado;
			require_once("../../login/inserta_historico.php");
	}else{
		echo "<script> alert('Ocurrió un error al generar el radicado interno, por favor contactar con el administrador del sistema.')</script>";
	}
}elseif($tipo_guardar == 1){
	pg_query($conectado,$query_rad_exp);
}
?>