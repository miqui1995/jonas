<?php 
/*  En este archivo se modifica el radicado y se insertan todas las query correspondientes*/
if(!isset($_SESSION)){
	session_start();
}
require_once('../../login/validar_inactividad.php');
require_once('../../login/conexion2.php');


function numeroPaginasPdf($archivoPDF){
	$stream 	= fopen($archivoPDF, "r");
	$content 	= fread ($stream, filesize($archivoPDF));
 
	if(!$stream || !$content)
		return 0;
 
	$count = 0;
 
	$regex  = "/\/Count\s+(\d+)/";
	$regex2 = "/\/Page\W*(\d+)/";
	$regex3 = "/\/N\s+(\d+)/";
 
	if(preg_match_all($regex, $content, $matches))
		$count = max($matches);
 
	return $count[0];
}

/* Se define $year y $month para luego asignarles $mes para poner en la ruta del PDF con el fin de
separar lo que mensualmente se hace para facilitar los backup */
$year  = date("Y");
$month = date("m");

switch ($month) {
    case '01':
        $mes = "ENERO";
        break;
    case '02':
        $mes = "FEBRERO";
        break;
    case '03':
        $mes = "MARZO";
        break;
    case '04':
        $mes = "ABRIL";
        break;
    case '05':
        $mes = "MAYO";
        break;
    case '06':
        $mes = "JUNIO";
        break;
    case '07':
        $mes = "JULIO";
        break;
    case '08':
        $mes = "AGOSTO";
        break;
    case '09':
        $mes = "SEPTIEMBRE";
        break;
    case '10':
        $mes = "OCTUBRE";
        break;
    case '11':
        $mes = "NOVIEMBRE";
        break;
    case '12':
        $mes = "DICIEMBRE";
        break;     
}

// Se toma la imagen imagenes/iconos/logo_largo.png y se convierte en en base64 para ser incluida en el contenido(html) del envio de correo electronico
$path_logo_entidad 			= "../../imagenes/iconos/logo_largo.png";
$data_logo_entidad			= file_get_contents($path_logo_entidad);
$logo_transparente_entidad 	= 'data:image/png;base64,' . base64_encode($data_logo_entidad);

// Se toma la imagen imagenes/encabezado_transparente.png y se convierte en en base64 para ser incluida en el contenido(html) del envio de correo electronico
$path_logo					= "../../imagenes/encabezado_transparente.png";
$data_logo_mail				= file_get_contents($path_logo);
$logo_transparente_mail 	= 'data:image/png;base64,' . base64_encode($data_logo_mail);
//fin de la conversion de la imagen a base64

/*****************************************************************************************
Inicio funcion para consultar codigo_carpeta1 en tabla radicado.
******************************************************************************************
* @brief Recibe desde este mismo archivo [consulta_codigo_carpeta1($numero_radicado,$tipo_consulta,$usuario_actual)]
** para generar el nuevo codigo_carpeta1 dependiendo de los usuarios visor y si reasigna a otro usuario de una nueva dependencia
** para que tenga habilidado el radicado en sus bandejas.
* @param {string} ($numero_radicado) Obligatorio, numero de radicado al cual se va a consultar y al cual se va a generar nuevo codigo_carpeta1
* @param {string} ($tipo_consulta) Se usa para indicar cual va a ser el resultado procesado. Puede ser "codigo_carpeta1", "usuario_actual" o 
** "usuarios_control"
* @param {string} ($usuario_actual) Se usa para verificar si está en el listado de usuarios_control, usuarios visor y en codigo_carpeta1. En 
** caso que no se encuentre, se agrega al campo correspondiente.
* @return {string} String con el campo "codigo_carpeta1" para modificar, String con "usuarios_visor", "usuarios_control" y/o "leido" 
*****************************************************************************************/		
function consulta_codigo_carpeta1($numero_radicado,$tipo_consulta,$usuario_actual){
	include '../../login/conexion2.php';

	$consulta_rad 		= "select * from radicado where numero_radicado='$numero_radicado'";
	$fila_rad 	  		= pg_query($conectado,$consulta_rad);
	$linea_rad    		= pg_fetch_array($fila_rad);

	$codigo_carpeta  	= $linea_rad['codigo_carpeta1'];
	$usuarios_control   = $linea_rad['usuarios_control'];
	$usuarios_visor   	= $linea_rad['usuarios_visor'];
	$dependencia_actual = $linea_rad['dependencia_actual'];

	/* La consulta para inventario genera el mismo codigo_carpeta1 porque no cambia el usuario_actual en modificacion de radicado */
	if($dependencia_actual=="INV" || $dependencia_actual=="INVE" ||$dependencia_actual=="INVEN"){
		$codigo_carpeta2 = $codigo_carpeta;
	}else{
		if(!isset($codigo_carpeta)){
			$codigo_carpeta2 = "{\"$usuario_actual\":{\"codigo_carpeta_personal\":\"entrada\"}}";
		}else{
			/* Inicio validando si el usuario destino ($usuario_actual) se encuentra entre los usuarios_visor */
			if (strlen(strstr($usuarios_visor,$usuario_actual))>0){
				$codigo_carpeta2 = $codigo_carpeta;
			}else{	
				/* Si no se encuentra, pasa el jsonb, incluye el usuario_actual y luego vuelve a pasarlo a JSON */
				$codigo_carpeta3 = json_decode($codigo_carpeta,true); // Paso de JSON a array
				$codigo_carpeta3[$usuario_actual]['codigo_carpeta_personal'] = 'entrada'; // Reemplazo en array la carpeta personal
				$codigo_carpeta2 = json_encode($codigo_carpeta3); // Paso de array a JSON con el valor cambiado
			}
		}

		/* Se valida si el usuario destino ($usuario_actual) está entre los usuarios_control para definir usarios_control y usuarios_visor */	
		if (strlen(strstr($usuarios_control,$usuario_actual))==0){
			$usuarios_control 	.= "$usuario_actual,";
			$usuarios_visor 	.= "$usuario_actual,";
		}
		/* Fin definicion $usuarios_control y $usuarios_visor */	
	}
	switch ($tipo_consulta) {
		case 'codigo_carpeta1':
			return ($codigo_carpeta2);
			break;	

		case 'usuario_actual':
			return ($usuarios_visor);
			break;

		case 'usuarios_control':
			return ($usuarios_control);
			break;
	}
}
/* Fin funcion para consultar usuarios_visor, usuarios_control y asignar codigo_carpeta1 */

$usuario 			= $_SESSION['login']; // Genera Usuario 
$codigo_entidad 	= $_SESSION['codigo_entidad']; // Genera el codigo de la entidad 
$nombre_entidad 	= $_SESSION['entidad']; // Genera el nombre de la entidad 

// Genera la fecha de transaccion 
$timestamp 			= date('Y-m-d H:i:s');

$tipo_modificacion 	= $_POST['tipo_modificacion'];

switch ($tipo_modificacion) {
	case 'modificacion_inventario':
		$caja_paquete_tomo 				= $_POST['caja_paquete_tomo'];
		$codigo_serie 					= $_POST['codigo_serie_sb'];
		$codigo_subserie 				= $_POST['codigo_subserie'];
		$consecutivo_desde 				= $_POST['consecutivo_desde'];
		$consecutivo_hasta 				= $_POST['consecutivo_hasta'];
		$descriptor 					= $_POST['descriptor'];
		$fecha_final 					= $_POST['fecha_final1'];
		$fecha_inicial 					= $_POST['fecha_inicial1'];
		$nombre_documento 				= $_POST['nombre_documento'];
		$numero_caja_archivo_central 	= $_POST['numero_caja_archivo_central'];
		$numero_caja_paquete 			= $_POST['numero_caja_paquete'];
		$numero_carpeta 				= $_POST['numero_carpeta'];
		$numero_radicado 				= $_POST['numero_radicado2'];
		$observaciones 					= $_POST['observaciones'];
		$total_folios 					= $_POST['total_folios'];

		if($numero_caja_archivo_central!=""){
			$id_caja_archivo_central 		= $_POST['id_caja_archivo_central'];
			$id_expediente 					= $_POST['id_expediente'];

			$query_update_expediente="update expedientes set codigo_ubicacion_topografica='$id_caja_archivo_central' where id_expediente='$id_expediente'";

			pg_query($conectado,$query_update_expediente);

			$num_caja_arch_central = "numero_caja_archivo_central='$numero_caja_archivo_central',";
		}else{
			$num_caja_arch_central = "";
		}

		/* Se inicia con la subida de las imagenes */

		$target_file= basename($_FILES["archivo_pdf_radicado_inv"]["name"]); 
		//	$target_file2=str_replace(" ", "", $target_file);
		$target_file2="$numero_radicado.pdf";
		$path="";

		if($target_file==""){ // No llega archivo pdf
			$anexar_archivo="NO";
		}else{	// Si llega archivo pdf		
			$path_file="bodega_pdf/radicados/$year/$mes/$target_file";
			$target_dir="../../bodega_pdf/radicados/$year/$mes/";

			if(move_uploaded_file($_FILES["archivo_pdf_radicado_inv"]["tmp_name"],$target_dir.$target_file2)){
				$anexar_archivo="SI";
				$path="$target_file2";
			}else{
				$error_upload_files=$_FILES["archivo_pdf_radicado"]["error"];

				switch ($error_upload_files) {
					case '0':
						$error_file="There is no error, the file uploaded with success";
						break;
					case '1':
						$error_file="The uploaded file exceeds the upload_max_filesize directive in php.ini";
						break;	
					case '2':
						$error_file="The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
						break;	
					case '3':
						$error_file="The uploaded file was only partially uploaded";
						break;	
					case '4':
						$error_file="No file was uploaded";
						break;				
					case '6':
						$error_file="Missing a temporary folder";
						break;	
					case '7':
						$error_file="Failed to write file to disk.";
						break;	
					case '8':
						$error_file="A PHP extension stopped the file upload.";
						break;	
				}
				$anexar_archivo="NO";
				echo"<script>error_subida_pdf('$error_upload_files','$error_file')</script>";
				exit();
			}
		}
		/* Fin de la subida de las imagenes */	

		$codigo_carpeta1 	= consulta_codigo_carpeta1($numero_radicado,'codigo_carpeta1',$usuario);
		$usuario_actual 	= consulta_codigo_carpeta1($numero_radicado,'usuario_actual',$usuario);

		if($path!=""){
			$path_rad = ", path_radicado='$target_file2'";
		}else{
			$path_rad ="";
		}

		$query_update_radicado = "update radicado set codigo_serie='$codigo_serie', codigo_subserie='$codigo_subserie', asunto='$nombre_documento', codigo_carpeta1='$codigo_carpeta1', leido='$usuario_actual' $path_rad where numero_radicado='$numero_radicado'";

		
		$query_modifica_radicado="update inventario set nombre_documento='$nombre_documento', fecha_inicial='$fecha_inicial', fecha_final='$fecha_final', caja_paquete_tomo='$caja_paquete_tomo', numero_caja_paquete='$numero_caja_paquete', numero_carpeta='$numero_carpeta', consecutivo_desde='$consecutivo_desde', consecutivo_hasta='$consecutivo_hasta', descriptor='$descriptor', total_folios='$total_folios', $num_caja_arch_central observaciones='$observaciones' where radicado_jonas='$numero_radicado';";	

		$query_modifica_radicado.=$query_update_radicado;
		$medio_respuesta_solicitado =""; // Variable para evitar error en el envio de mail ya que en modificacion de inventario no debe enviar mail

		break;

	case 'modificacion_radicado_interno_normal':
		$numero_radicado 			= $_POST['numero_radicado2']; // Tipo de radicado (2- Entrada, 1-Salida, etc)
		$codigo_serie_sb 			= $_POST['codigo_serie_sb']; 
		$codigo_subserie 			= $_POST['codigo_subserie']; 
		$nombre_documento 			= $_POST['nombre_documento']; 

		/* Se inicia con la subida de las imagenes */

		$target_file= basename($_FILES["archivo_pdf_radicado_inv"]["name"]); 
	//	$target_file2=str_replace(" ", "", $target_file);
		$target_file2="$numero_radicado.pdf";
		$path="";

		if($target_file==""){ // No llega archivo pdf
			$anexar_archivo="NO";
		}else{	// Si llega archivo pdf		
			$path_file="bodega_pdf/radicados/$year/$mes/$target_file";
			$target_dir="../../bodega_pdf/radicados/$year/$mes/";

			if(move_uploaded_file($_FILES["archivo_pdf_radicado_inv"]["tmp_name"],$target_dir.$target_file2)){
				$anexar_archivo="SI";
				$path="$target_file2";
			}else{
				$error_upload_files=$_FILES["archivo_pdf_radicado"]["error"];

				switch ($error_upload_files) {
					case '0':
						$error_file="There is no error, the file uploaded with success";
						break;
					case '1':
						$error_file="The uploaded file exceeds the upload_max_filesize directive in php.ini";
						break;	
					case '2':
						$error_file="The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
						break;	
					case '3':
						$error_file="The uploaded file was only partially uploaded";
						break;	
					case '4':
						$error_file="No file was uploaded";
						break;				
					case '6':
						$error_file="Missing a temporary folder";
						break;	
					case '7':
						$error_file="Failed to write file to disk.";
						break;	
					case '8':
						$error_file="A PHP extension stopped the file upload.";
						break;	
				}
				$anexar_archivo="NO";
				echo"<script>error_subida_pdf('$error_upload_files','$error_file')</script>";
				exit();
			}
		}
	/* Fin de la subida de las imagenes */	

		if($anexar_archivo=="SI"){
			$path_rad = ", path_radicado='$target_file2'";
		}else{
			$path_rad ="";
		}

		$codigo_carpeta1 	= consulta_codigo_carpeta1($numero_radicado,'codigo_carpeta1',$usuario);
		$usuario_actual 	= consulta_codigo_carpeta1($numero_radicado,'usuario_actual',$usuario);

		$query_modifica_radicado = "update radicado set codigo_serie='$codigo_serie_sb', codigo_subserie='$codigo_subserie', asunto='$nombre_documento', codigo_carpeta1='$codigo_carpeta1', leido='$usuario_actual' $path_rad where numero_radicado='$numero_radicado'";

		break;
	default:
	/* Este caso es cuando no es "modificacion_inventario" ni "modificacion_radicado_interno_normal" */
		// Se definen los valores para la radicacion
		$asunto_radicado 				= trim($_POST['asunto_radicado']);
		$clasificacion_radicado 		= $_POST['termino']; // Clasificacion de documento (PQR, Oficio, etc)
		$clasificacion_seguridad 		= $_POST['clasificacion_seguridad']; // Clasificacion de seguridad(Sin clasificacion, restringido, confidencial, secreto, ultrasecreto, publica clasificada, publica reservada.)
		$codigo_contacto 		 		= $_POST['codigo_contacto'];
		$codigo_dependencia_destino 	= $_POST['codigo_dependencia'];
		$codigo_dependencia_radicador 	= $_SESSION['dependencia'];
		$dependencia_destino 			= $_POST['search_dependencia_destino'];
		$descripcion_anexos 			= $_POST['descripcion_anexos'];
		$dignatario_remitente 			= $_POST['dignatario_remitente'];
		$direccion_remitente 			= $_POST['direccion_remitente'];
		$mail_remitente 				= $_POST['mail_remitente'];
		$medio_recepcion 				= $_POST['medio_recepcion'];
		$medio_respuesta_solicitado 	= $_POST['medio_respuesta_solicitado'];
		$nombre_completo 				= $_POST['nombre_completo'];
		$numero_guia_radicado 			= $_POST['numero_guia_radicado']; // Anexos que llegan (usb, cd, caja, etc)
		$numero_radicado 				= $_POST['numero_radicado']; // Tipo de radicado (2- Entrada, 1-Salida, etc)
		$telefono_remitente 			= $_POST['telefono_remitente'];
		$termino 						= $_POST['dias_tramite']; // Termino para respuesta del radicado.
		$usuario_destino 				= $_POST['usuario_destino']; // Si es una modificacion de dependencia_destino cambia el usuario destino.
		$ubicacion_remitente 			= $_POST['ubicacion_remitente'];

		if(isset($_POST['folios'])){
			$numero_folios 				= $_POST['folios'];
		}else{
			$numero_folios 				= '';
		}

		if($termino==""){
			$termino = 0;
		}

	/* Se inicia con la subida de las imagenes */

		/* Genera aleatorio alfanumerico para path del pdf */
		$permitted_chars = '2345789abcdefghjkmnpqrstuvwxyzABCDEFHJKLMNPQRSTUVWXYZ';

		$target_dir 	= "../../bodega_pdf/radicados/$year/$mes/";
		$target_file 	= basename($_FILES["archivo_pdf_radicado"]["name"]); 
		$aleatorio 		= substr(str_shuffle($permitted_chars), 0, 5);
		$target_file2 	= "$numero_radicado"."_".$aleatorio.".pdf";
		$path  			= "";

		if($target_file==""){ 	// No llega archivo pdf
			/* Variable que solo funciona cuando tiene habilitado el modulo de asociar imagen desde carpeta compartida */
			$path_origen_scanner  	= $_POST['path_origen_scanner'];

			if($path_origen_scanner == ""){
				$anexar_archivo 		= "NO";
				$path_rad  				= "";
			}else{
				$anexar_archivo 		= "SI";

				/* Para cargar un PDF en version superior (superior a 1.4) se transforma a 1.4 y se guarda en la carpeta /bodega_pdf/radicados/$year/mes/$aleatorio.pdf */
				shell_exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH  -sOutputFile=".$target_dir.$aleatorio.".pdf ../".$path_origen_scanner);
				
				$path_rad 				= ", path_radicado='../bodega_pdf/radicados/$year/$mes/$target_file2'"; // Variable para update radicado

				/* Variables para sticker_web.pdf */	
				$numero_radicado        = "$numero_radicado";
				$fullPathToFile         = $target_dir.$aleatorio.".pdf"; // Archivo original con paginas múltiples 
				$nombre_pdf_salida 	= "../../bodega_pdf/radicados/$year/$mes/$target_file2";
				
				if($codigo_entidad == 'EJC'){
					$texto_sticker_entidad  = "de $nombre_entidad";
				}else{
					$texto_sticker_entidad  = "JONAS de $nombre_entidad";
				}

				/* */
				require_once('sticker_web_pdf.php');

				$fullPathToFile  	= $nombre_pdf_salida; 

				$numero_folios 		= numeroPaginasPdf($fullPathToFile);				
				$path_rad  			= ", path_radicado='$year/$mes/$target_file2'";	

				unlink("../$path_origen_scanner");
				unlink($target_dir.$aleatorio.".pdf");
			}
		}else{	// Si llega archivo pdf		
			$path_file="bodega_pdf/radicados/$year/$mes/$target_file";

			if(move_uploaded_file($_FILES["archivo_pdf_radicado"]["tmp_name"],$target_dir.$target_file2)){
				$anexar_archivo			= "SI";
				$path 			 		= "$year/$mes/$target_file2";
				$path_rad 				= ", path_radicado='$path'"; // Variable para update radicado
					
				/* Para cargar un PDF en version superior (superior a 1.4) se transforma a 1.4 y se guarda en la carpeta /bodega_pdf/$year/$mes/$aleatorio.pdf */
				echo shell_exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH  -sOutputFile=".$target_dir.$aleatorio.".pdf ".$target_dir.$target_file2);

				/* Variables para sticker_web.pdf */	
				$numero_radicado        = "$numero_radicado";
				$fullPathToFile         = $target_dir.$aleatorio.".pdf"; // Archivo original con paginas múltiples 
				$nombre_pdf_salida  	= $target_dir.$target_file2;
				
				if($codigo_entidad == 'EJC'){
					$texto_sticker_entidad  = "de $nombre_entidad";
				}else{
					$texto_sticker_entidad  = "JONAS de $nombre_entidad";
				}

				require_once('sticker_web_pdf.php');

				echo unlink($target_dir.$aleatorio.".pdf");
				$numero_folios 	= numeroPaginasPdf($nombre_pdf_salida);				
			}else{
				$error_upload_files=$_FILES["archivo_pdf_radicado"]["error"];

				switch ($error_upload_files) {
					case '0':
						$error_file="There is no error, the file uploaded with success";
						break;
					case '1':
						$error_file="The uploaded file exceeds the upload_max_filesize directive in php.ini";
						break;	
					case '2':
						$error_file="The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
						break;	
					case '3':
						$error_file="The uploaded file was only partially uploaded";
						break;	
					case '4':
						$error_file="No file was uploaded";
						break;				
					case '6':
						$error_file="Missing a temporary folder";
						break;	
					case '7':
						$error_file="Failed to write file to disk.";
						break;	
					case '8':
						$error_file="A PHP extension stopped the file upload.";
						break;	
				}
				$anexar_archivo="NO";
				echo"<script>error_subida_pdf('$error_upload_files','$error_file')</script>";
				exit();
			}
		}

	/* Fin de la subida de las imagenes */	
		if($tipo_modificacion=="insert"){ // Si el tipo de modificacion es un "insert" o un  "update"
			$query_datos_origen="insert into datos_origen_radicado(codigo_datos_origen_radicado, numero_radicado, nombre_remitente_destinatario, dignatario, ubicacion, direccion, telefono, mail) values('$codigo_contacto', '$numero_radicado', '$nombre_completo', '$dignatario_remitente', '$ubicacion_remitente', '$direccion_remitente', '$telefono_remitente', '$mail_remitente');";
		}else{
			$query_datos_origen="update datos_origen_radicado set nombre_remitente_destinatario='$nombre_completo', direccion='$direccion_remitente', dignatario='$dignatario_remitente', ubicacion='$ubicacion_remitente', telefono='$telefono_remitente', mail='$mail_remitente' where numero_radicado='$numero_radicado';";
		}

		$codigo_carpeta1 	= consulta_codigo_carpeta1($numero_radicado,'codigo_carpeta1',$usuario_destino);
		$usuario_actual 	= consulta_codigo_carpeta1($numero_radicado,'usuario_actual',$usuario_destino);
		$usuarios_control 	= consulta_codigo_carpeta1($numero_radicado,'usuarios_control',$usuario_destino);
		
		$query_modifica_radicado="update radicado set numero_guia_oficio='$numero_guia_radicado', descripcion_anexos='$descripcion_anexos', codigo_contacto='$codigo_contacto', dependencia_actual='$codigo_dependencia_destino', usuarios_visor='$usuario_actual', usuarios_control='$usuarios_control', leido='$usuario_actual', asunto='$asunto_radicado', termino='$termino', clasificacion_radicado='$clasificacion_radicado', clasificacion_seguridad='$clasificacion_seguridad', codigo_carpeta1='$codigo_carpeta1', medio_respuesta_solicitado='$medio_respuesta_solicitado', folios = '$numero_folios', medio_recepcion='$medio_recepcion' $path_rad where numero_radicado='$numero_radicado';";
		
		$query_modifica_radicado.=$query_datos_origen;
		break;
}


// echo "$query_modifica_radicado";
if(pg_query($conectado,$query_modifica_radicado)){	// Si se modifica el radicado
	$radicado = $numero_radicado; // Variable para tabla historico_eventos
	if($anexar_archivo=="SI"){
		if($tipo_modificacion=="insert"){
			$transaccion_historico	= "Modificación rápida de radicado y cargue de imagen principal pdf";
			$tipo_formulario		= "modificacion_rapida_mas_imagen"; 	// Variable para auditoria
			$comentario				= "Documento $radicado modificado e imagen principal cargada";	// Variable para historico eventos
		}else{
			$transaccion_historico	= "Modificación de radicado y cargue de imagen principal pdf";
			$tipo_formulario 		= "modificacion_radicado_mas_imagen";
			$comentario				= "Documento $radicado modificado e imagen principal cargada / modificada";
		}
	}else{
		switch ($tipo_modificacion) {
			case 'insert':
		 		$transaccion_historico 	= "Modificacion rapida";	// Variable para tabla historico_eventos
				$tipo_formulario 		= "modificacion_rapida"; 	// Variable para auditoria
				$comentario 			= "Documento $radicado modificado";	// Variable para historico eventos
				break;
			case 'modificacion_radicado_interno_normal':
			case 'update':
				$transaccion_historico 	= "Modificacion";
				$tipo_formulario 		= "modificacion_radicado";
				$comentario 			= "Documento $radicado modificado";
				break;
			case 'modificacion_inventario':
		 		$transaccion_historico 	= "Modificacion inventario";	// Variable para tabla historico_eventos
				$tipo_formulario 		= "modificacion_inventario"; 	// Variable para auditoria
				$comentario 			= "Documento $radicado modificado";	// Variable para historico eventos
				break;
			default:
				# code...
				break;
		}
	}
	$creado 		= $radicado;	
	$transaccion  	= $tipo_formulario;

	if ($medio_respuesta_solicitado == "correo_electronico"){ //Si el usuario requiere respuesta por correo electronico
		/* Defino variable para enviar_mail e historico*/
		$nombre_segundo_mail_completo = $nombre_completo."(".$dignatario_remitente.")"; //Nombre del distinatario que se verá en el correo 
		
		include "../../include/genera_fecha.php";
		/*Fecha que se realiza la transaccion (hoy)*/   
		 /* Aqui se modifica la fecha para que quede con el formato para mostrar "Jueves 05 de Mayo de 2020" la variable $timestamp es heredada desde include/genera_fecha.php */
		$fecha_hoy=$b->traducefecha($timestamp);

		$html_mail_estructurado = '<table align="center" width="1000" cellpadding="0" cellspacing="0" style="background-color: rgba(0, 0, 0, 0.03) !important; max-width: 800px; border-top: 5px solid rgb(109, 161, 174); border-bottom: 5px solid rgb(109, 161, 174);"><tbody><tr><td style="padding:25px; max-width:500px"><h2 style="color:#2F3133 !important; text-align: center; margin-top: 0px; font-weight: 700; font-size: 16px;">Comunicación Oficial.</h2><a href="https://gammacorp.co/que_es_jonassgd.html" style="text-decoration:none!important; float: left;"><img naturalheight="0" naturalwidth="0" src="'.$logo_transparente_entidad.'" width="120" height="70"></a><a href="https://gammacorp.co/que_es_jonassgd.html" style="text-decoration:none!important; float: right;"><img naturalheight="0" naturalwidth="0" src="'.$logo_transparente_mail.'" width="200" height="66" style="border: none;"></a><br><p style="color: #515F72 !important; margin-top: 70px; text-align: left; font-size: 16px; line-height: 1.5em;">'.$fecha_hoy.'</p><p style="color: #515F72 !important; margin-top: 0px; text-align: justify; font-size: 18px; line-height: 1.5em;" data-ogsc="rgb(81, 95, 127)"><br>Estimado(a) <br><b>'.$dignatario_remitente.'<br>'.$nombre_completo.'</b><br><br><span>El sistema de Gestión Documental <b>Jonas - '.$nombre_entidad.'</b> le informa que se ha generado un nuevo radicado de entrada a su nombre.<br><br>Se le ha asignado el número de radicado <b>'.$radicado.'</b> <br>Con el asunto <b> '.$asunto_radicado.'</b> y se han registrado como anexos (<b>'.$descripcion_anexos.'</b>)</span><br>ha sido clasificado como <u><b>'.$clasificacion_radicado.'</b></u> y según el artículo 14 de la Ley 1437 de 2011, '.$nombre_entidad.' tiene <u><b>'.$termino.'</b></u> dias para darle respuesta la cual será enviada por éste mismo medio.<br><span><br>Puede consultar el estado de su trámite a través del siguiente enlace: </span><br></p><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&amp;data=http://gammacorp.co/consultaweb.php?numero_radicado%3D'.$radicado.'%26codigo_entidad%3D'.$codigo_entidad.'%26canal_respuesta%3Dmail"><p style="color: #515F72 !important"></p><p><a href="https://www.gammacorp.co/consultaweb.php?numero_radicado='.$radicado.'&amp;codigo_entidad='.$codigo_entidad.'&amp;canal_respuesta=mail" target="_blank" style="color: #515F72 !important;">Consultar estado del trámite del radicado '.$radicado.' vía web.</a></p><br><br><p style="color: #515F72 !important; margin-top: 0px; text-align: left; font-size: 16px; line-height: 1.5em; margin-bottom: 0px;">Cordialmente</p><br><br><p style="color: #515F72 !important; margin-top: 0px; text-align: left; font-size: 16px; line-height: 1.5em; margin-bottom: 0px;"><b>Software de Gestión Documental Jonas <br>'.$entidad.'</b></p><hr><h3><p style="color: #515F72 !important; text-align:center;"><b>*** Importante: Por favor no responder este correo electronico. ***<br>******** Esta cuenta no permite recibir correos electrónicos. *******</b></p></h3><br></td></tr></tbody></table>';//Se crea el contenido(html) del correo electronico
		
		/* Se llama la function enviar_mail definida en include/funciones_radicacion_entrada.js para enviar mail al usuario_radicador */
		if($mail_remitente!=""){
			echo "<script>enviar_mail('$asunto_radicado','$mail_remitente','$html_mail_estructurado','$nombre_segundo_mail_completo')</script>";
			$comentario.="<br>Se envía mail al usuario externo $nombre_segundo_mail_completo indicandole el tiempo de respuesta para tramitar su solicitud.";
		}
	
		// Consulta mail y nombre_completo del usuario_destino
		$query_usuario_dest 		= "select mail_usuario,nombre_completo from usuarios where login='$usuario_destino'";//estructura de consulta a base de datos, tabla radicado con where de login
		$ejecuta_query_usuario_dest = pg_query($conectado,$query_usuario_dest);
		$linea_query_usuario_dest 	= pg_fetch_array($ejecuta_query_usuario_dest);//se ordenan los registros extraidos con la consulta a base de datos
		$mail_usuario  	      		= $linea_query_usuario_dest['mail_usuario'];
		$nombre_completo_mail 		= $linea_query_usuario_dest['nombre_completo'];

		$nombre_completo_imprimir  	= $nombre_completo_mail."(".$usuario_destino.")";//Nombre del destinatario que se verá en el correo electronico

		$html_mail_estructurado2 = '<table align="center" width="1000" cellpadding="0" cellspacing="0" style="background-color: rgba(0, 0, 0, 0.03) !important; max-width: 800px; border-top: 5px solid rgb(109, 161, 174); border-bottom: 5px solid rgb(109, 161, 174);"><tr><td style="padding:25px; max-width:500px"><h2 style="color:#2F3133 !important; text-align: center; margin-top: 0px; font-size: 16px;">Comunicación Oficial</h2><a href="https://gammacorp.co/que_es_jonassgd.html" style="text-decoration:none!important; float: left;"><img naturalheight="0" naturalwidth="0" src="'.$logo_transparente_entidad.'" width="120" height="70"></a><a href="https://gammacorp.co/que_es_jonassgd.html" style="text-decoration:none!important; float: right;"><img naturalheight="0" naturalwidth="0" src="'.$logo_transparente_mail.'" width="200" height="66" style="border: none;"></a><br><p style="color: #515F72 !important; margin-top: 70px; text-align: left; font-size: 16px; line-height: 1.5em;">'.$fecha_hoy.'</p><p style="color: #515F72 !important; margin-top: 0px; text-align: justify; font-size: 18px; line-height: 1.5em;"><br>Estimado(a) <br><b>'.$nombre_completo_mail.'('.$usuario_destino.')</b><br><br><span>El sistema de Gestión Documental <b>Jonas SGDEA - '.$nombre_entidad.'</b> le informa que le ha sido asignado un nuevo radicado de entrada con el numero <b>'.$radicado.'</b> <br>Con el asunto <b>'.$asunto_radicado.'</b> y se han registrado como anexos (<b>'.$descripcion_anexos.'</b>)</span><br>ha sido clasificado como <u><b>'.$clasificacion_radicado.'</b></u> y según el artículo 14 de la Ley 1437 de 2011, en <b>'.$nombre_entidad.'</b> tenemos <u><b>'.$termino.'</b></u> dias para darle respuesta al solicitante, por lo que le pedimos diligentemente agilizar la gestión de este documento que ha quedado bajo su responsabilidad.<br><br>Por favor consulte su bandeja de entrada del sistema y responda o reasigne a la persona que le corresponda dar respuesta a éste documento para evitar incurrir en términos legales.</span><br><br><span></p><p style="color: #515F72 !important; margin-top: 0px; text-align: left; font-size: 16px; line-height: 1.5em; margin-bottom: 0px;">Cordialmente</p><br><br><p style="color: #515F72 !important; margin-top: 0px; text-align: left; font-size: 16px; line-height: 1.5em; margin-bottom: 0px;"><b>Software de Gestión Documental Jonas - '.$entidad.'</b></p><hr><h3><p style="color: #515F72 !important; text-align:center;"><b>*** Importante: Por favor no responder este correo electronico. ***<br>******** Esta cuenta no permite recibir correos electrónicos. *******</b></p></h3><br></td></tr></tbody></table>';
		
		/* Se llama la function enviar_mail para enviar mail al usuario_actual */
		if($mail_usuario!=""){
			echo "<script>enviar_mail('$asunto_radicado','$mail_usuario','$html_mail_estructurado2','$nombre_completo_imprimir')</script>";
			$comentario.="<br>Se envía mail al usuario interno de Jonas $usuario_destino($mail_usuario) indicandole el tiempo que tiene para responder el documento.";
		}
	}

	if($codigo_entidad == 'EJC' || $codigo_entidad == 'EJEC'){ // Desarrollo hecho para interactuar con Ejercito Nacional
		if($path_rad != ""){
			$numero_folios 	= numeroPaginasPdf($fullPathToFile);				
			$file_enviar 	= base64_encode(file_get_contents($fullPathToFile));

				// echo "<input type='text' id='file_enviar_orfeo' value='$file_enviar'>
				// <script>
				// 	var file1 	= $('#file_enviar_orfeo').val();
				// </script>";
			$imagen_pdf = ", 'archivo_b_64' : '$file_enviar', 'nombre_archivo'	: '$target_file2', 'numero_folios' : '$numero_folios'";
		}else{
			$imagen_pdf = "";
		}

		$data = "
			data: {
				'asunto' 					: '$asunto_radicado',
				'clasificacion_seguridad' 	: '$clasificacion_seguridad',
				'descrip_anex'  			: '$descripcion_anexos',
				'destinatario' 				: '$nombre_completo', 
				'dignatario' 				: '$dignatario_remitente', 
				'direccion' 				: '$direccion_remitente', 
				'mail' 						: '$mail_remitente', 
				'medio_recepcion' 			: '$medio_recepcion', 
                'municipio' 				: '$ubicacion_remitente',				                
                'numero_guia_rad' 			: '$numero_guia_radicado',				                
                'numero_radicado'			: '$numero_radicado',
                'login_destino'   			: '$usuario_destino',	
                'login_origen'   			: '$usuario',					                
				'recibe_jonas'     			: 'modifica_archivo',
				'telefono'					: '$telefono_remitente'
                $imagen_pdf
            }";

		echo "<script>	
			var usuario_destino 	= $('#usuario_destino').val();
			var usuario_radicador 	= $('#login_usuario').val();
			 
			$.ajax({
	            type    : 'POST',
	            url     : 'http://172.22.2.226/pruebas/interoperabilidad_rest/api_jonas.php',
	            datatype : 'jsonp',
	            crossDomain : true,
	            $data,
	            success: function(resp){
	            	var resp_trim = $.trim(resp);
					if(resp_trim.length==16){
						console.log('Radicado '+resp+' modificado correctamente');
					}else{
						console.log('No se puede enviar, codigo de error '+resp);
					}
	            }
	        })	    
		</script>";

				// //Decode pdf content
				// $pdf_decoded = base64_decode ($file_enviar);
				// //Write data back to pdf file
				// $pdf = fopen ($target_dir.'test.pdf','w');
				// fwrite ($pdf,$pdf_decoded);
				// //close output file
				// fclose ($pdf);	
	}
	/* Fin desarrollo hecho para interactuar con Ejercito Nacional */
	require_once('../../login/inserta_historico.php');
}
?>
