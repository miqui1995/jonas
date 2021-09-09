<?php  
if(!isset($_SESSION)){
	session_start();
}
/**************************************************************
Inicio funcion para calcular el maximo de adjuntos 
/**************************************************************
* @brief Funcion para calcular el maximo de la tabla adjuntos de la base de datos para asignar variable para insertar en adjuntos correctamente.

* @param {string} () Esta funcion no recibe parametros.
* @return {string} String con el maximo calculado desde la tabla de adjuntos de la base de datos existentes.
**************************************************************/	
function maximo_adjuntos(){
	include '../login/conexion2.php';

	$query_max_adjuntos = "select max(id) from adjuntos";

	$fila_max_adjuntos  	= pg_query($conectado,$query_max_adjuntos); 
	$linea_max_adjuntos 	= pg_fetch_array($fila_max_adjuntos);
    $max_adjuntos1 			= $linea_max_adjuntos[0];

    if($max_adjuntos1 ==""){
    	$max_adjuntos = 1;
    }else{
	    $max_adjuntos = $max_adjuntos1;
    }
    return $max_adjuntos;
}
/* Fin funcion calcular el maximo de adjuntos */
/**********************************************************/ 

// var_dump($_SESSION);
$usuario 	= $_SESSION['login'];
$timestamp  = date('Y-m-d H:i:s');	// Genera la fecha de transaccion	

require_once('../login/conexion2.php');


$query_anexos 			= "";
$query_radicado 		= "";
$query_update_radicado 	= "";

/* Se usa la funcion maximo_adjuntos() definida en este mismo archivo para obtener la variable $max_adjuntos con el fin de armar la query de insertar adjunto */
$max_adjuntos 	= maximo_adjuntos();

if(isset($_POST['tipo_formulario'])){	// Se recibe el POST desde index_expedientes.php
	/* Se definen las variables para crear el radicado */
	$tipo_formulario=$_POST['tipo_formulario'];

	if ($tipo_formulario=='crear_expediente') { // Valor desde index_expedientes.php 
		$nombre_expediente = $_POST['nombre_expediente'];
		$codigo_dependencia = $_POST['dependencia_expediente'];
		$codigo_serie 		= $_POST['serie_expediente'];
		$codigo_subserie 	= $_POST['subserie_expediente'];

		/* Condición si llega un archivo, hay que generar un numero de radicado para el contrato, asociar el expediente a éste,  mover los archivos adjuntos(Hoja de vida, fotocopia de cedula, etc) a la carpeta bodega_pdf/adjuntos/ y hacer el insert en la base de datos en la tabla "adjuntos" */
		if(isset($_FILES['pdf_contrato']['name'])){
			$asunto_radicado 	= trim($_POST['nombre_expediente']);
		/* En el archivo validar_consecutivo.php se genera el numero de radicado para insertar en la tabla "radicados". Pero para generar éste numero de radicado se deben definir variables que son obligatorias. Desde aqui defino variables para generar el radicado. */
			$tipo_radicado 				= 4; 	// Tipo de radicado (4-Normal, 3- Interna, 2- Salida, 1- Entrada, etc)
			$year 						= date("Y"); 	// Se obtiene el año actual en formato 4 digitos 
		/* Hasta aqui defino variables para el archivo validar_consecutivo.php */
			
		// Se arma el json para codigo_carpeta1 de la tabla radicado
			// $codigo_carpeta1="'{\"$login_usuario\":{\"codigo_carpeta_personal\":\"entrada\"}}'";
			// Fin del armado del json para codigo_carpeta1 de la tabla radicado

			/* Se carga a la carpeta /bodega_pdf/radicados/ el contrato y se arma la query para insertar a la base de datos el numero de radicado con el path (ubicacion en bodega_pdf/radicados/nombre_archivo.pdf) */
			if(move_uploaded_file($_FILES["pdf_contrato"]["tmp_name"],$target_dir.$path_file)){
				$query_radicado="insert into radicado(numero_radicado, fecha_radicado, path_radicado, dependencia_actual, usuarios_visor, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, id_expediente, codigo_serie, codigo_subserie, codigo_carpeta1, estado_radicado, usuarios_control) values('$radicado', '$timestamp', '$path_file', '$codigo_dependencia', '$usuario_destino', '$codigo_dependencia', '$login_usuario', '$asunto_radicado', '$nivel', '$usuario_destino', '', '$codigo_serie', '$codigo_subserie', $codigo_carpeta1,'en_tramite', '$usuario_destino');";
			}else{ // Si hubo error al cargar el archivo recibido archivo_pdf_radicado
				$error_upload_files=$_FILES["pdf_contrato"]["error"];

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
				}	// Final del switch $error_upload_files
				echo "No se puede cargar el pdf_contrato - $error_file";
			}
		/* Finaliza carga a la carpeta /bodega_pdf/radicados/ el contrato y se arma la query para insertar a la base de datos el numero de radicado con el path (ubicacion en bodega_pdf/radicados/nombre_archivo.pdf) */
		/* Condicion si se carga el archivo pdf_hoja_de_vida */
			if(isset($_FILES['pdf_hoja_de_vida']['name'])){
				/* A la variable $max_adjuntos se le suma 1 */
				$max_adjuntos+=1;

				$target_dir 	= "../bodega_pdf/adjuntos/";
			    $asunto_adjunto = "Hoja_de_vida_del_contrato_$asunto_radicado";
			    $path_adjunto 	= $radicado.$max_adjuntos.".pdf"; 

			    if(move_uploaded_file($_FILES["pdf_hoja_de_vida"]["tmp_name"],$target_dir.$path_adjunto)){
				    $query_anexos.="insert into adjuntos(id,numero_radicado,fecha_radicado,asunto,path_adjunto)values('$max_adjuntos','$radicado','$timestamp','$asunto_adjunto','$path_adjunto');";
				}else{ // Si hubo error al cargar el archivo recibido archivo_pdf_radicado
					$error_upload_files=$_FILES["pdf_hoja_de_vida"]["error"];

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
					}	// Final del switch $error_upload_files
					echo "No se puede cargar el pdf_hoja_de_vida - $error_file";
				}
			}
		/* Fin condicion si se carga el archivo pdf_hoja_de_vida*/	
		/* Condicion si se carga el archivo pdf_cedula_ciudadania */
			if(isset($_FILES['pdf_cedula_ciudadania']['name'])){
				/* A la variable $max_adjuntos se le suma 1 */
				$max_adjuntos+=1;

				$target_dir 	= "../bodega_pdf/adjuntos/";
			    $asunto_adjunto = "Cedula_ciudadania_del_contrato_$asunto_radicado";
			    $path_adjunto 	= $radicado.$max_adjuntos.".pdf"; 

			    if(move_uploaded_file($_FILES["pdf_cedula_ciudadania"]["tmp_name"],$target_dir.$path_adjunto)){
				    $query_anexos.="insert into adjuntos(id,numero_radicado,fecha_radicado,asunto,path_adjunto)values('$max_adjuntos','$radicado','$timestamp','$asunto_adjunto','$path_adjunto');";
				}else{ // Si hubo error al cargar el archivo recibido archivo_pdf_radicado
					$error_upload_files=$_FILES["pdf_cedula_ciudadania"]["error"];

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
					}	// Final del switch $error_upload_files
					echo "No se puede cargar el pdf_cedula_ciudadania - $error_file";
				}
			}
		/* Fin condicion si se carga el archivo pdf_cedula_ciudadania*/	
		/* Condicion si se carga el archivo pdf_rut */
			if(isset($_FILES['pdf_rut']['name'])){
				/* A la variable $max_adjuntos se le suma 1 */
				$max_adjuntos+=1;

				$target_dir 	= "../bodega_pdf/adjuntos/";
			    $asunto_adjunto = "Rut_del_contrato_$asunto_radicado";
			    $path_adjunto 	= $radicado.$max_adjuntos.".pdf"; 

			    if(move_uploaded_file($_FILES["pdf_rut"]["tmp_name"],$target_dir.$path_adjunto)){
				    $query_anexos.="insert into adjuntos(id,numero_radicado,fecha_radicado,asunto,path_adjunto)values('$max_adjuntos','$radicado','$timestamp','$asunto_adjunto','$path_adjunto');";
				}else{ // Si hubo error al cargar el archivo recibido archivo_pdf_radicado
					$error_upload_files=$_FILES["pdf_rut"]["error"];

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
					}	// Final del switch $error_upload_files
					echo "No se puede cargar el pdf_rut - $error_file";
				}
			}
		/* Fin condicion si se carga el archivo pdf_rut*/	
		/* Condicion si se carga el archivo pdf_certificacion_bancaria */
			if(isset($_FILES['pdf_certificacion_bancaria']['name'])){
				/* A la variable $max_adjuntos se le suma 1 */
				$max_adjuntos+=1;

				$target_dir 	= "../bodega_pdf/adjuntos/";
			    $asunto_adjunto = "Certificacion_bancaria_del_contrato_$asunto_radicado";
			    $path_adjunto 	= $radicado.$max_adjuntos.".pdf"; 

			    if(move_uploaded_file($_FILES["pdf_certificacion_bancaria"]["tmp_name"],$target_dir.$path_adjunto)){
				    $query_anexos.="insert into adjuntos(id,numero_radicado,fecha_radicado,asunto,path_adjunto)values('$max_adjuntos','$radicado','$timestamp','$asunto_adjunto','$path_adjunto');";
				}else{ // Si hubo error al cargar el archivo recibido archivo_pdf_radicado
					$error_upload_files=$_FILES["pdf_certificacion_bancaria"]["error"];

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
					}	// Final del switch $error_upload_files
					echo "No se puede cargar el pdf_certificacion_bancaria - $error_file";
				}
			}
		/* Fin condicion si se carga el archivo pdf_certificacion_bancaria*/	
		/* Condicion si se carga el archivo pdf_examen_ingreso */
			if(isset($_FILES['pdf_examen_ingreso']['name'])){
				/* A la variable $max_adjuntos se le suma 1 */
				$max_adjuntos+=1;

				$target_dir 	= "../bodega_pdf/adjuntos/";
			    $asunto_adjunto = "Examen_ingreso_del_contrato_$asunto_radicado";
			    $path_adjunto 	= $radicado.$max_adjuntos.".pdf"; 

			    if(move_uploaded_file($_FILES["pdf_examen_ingreso"]["tmp_name"],$target_dir.$path_adjunto)){
				    $query_anexos.="insert into adjuntos(id,numero_radicado,fecha_radicado,asunto,path_adjunto)values('$max_adjuntos','$radicado','$timestamp','$asunto_adjunto','$path_adjunto');";
				}else{ // Si hubo error al cargar el archivo recibido archivo_pdf_radicado
					$error_upload_files=$_FILES["pdf_examen_ingreso"]["error"];

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
					}	// Final del switch $error_upload_files
					echo "No se puede cargar el pdf_examen_ingreso - $error_file";
				}
			}
		/* Fin condicion si se carga el archivo pdf_examen_ingreso*/				

		/* En este caso deben llegar las variables de valor_contrato y pago_mensual_contrato para alimentar la tabla que se defina ..PENDIENTE NO SEGUIR HASTA NO TENER CLARO ESTO:...  */

		$valor_contrato 		= $_POST['valor_contrato'];
		$pago_mensual_contrato 	= $_POST['pago_mensual_contrato'];

		echo "$valor_contrato - $pago_mensual_contrato";


	/* Fin condición si llega archivo para generar un numero de radicado para el contrato, asociar el expediente a éste,  mover los archivos adjuntos(Hoja de vida, fotocopia de cedula, etc) a la carpeta bodega_pdf/adjuntos/ y hacer el insert en la base de datos en la tabla "adjuntos" */	
		}

		$year = $_POST['year'];
	/* Inicio generar consecutivo tabla expediente */
		$query_max_id_expediente 	= "select max(id) from expedientes";

		$fila_max_id_expediente 	= pg_query($conectado,$query_max_id_expediente);
		$linea_id_expediente 		= pg_fetch_array($fila_max_id_expediente);

		if($linea_id_expediente==false){
			$max_id_expediente2="1";	// Inicia el consecutivo del expediente por año/serie/subserie
		}else{
			$max_id_expediente  	= $linea_id_expediente[0];
			$max_id_expediente2 	= $max_id_expediente+1;			
		}
	/* Fin generar consecutivo tabla expediente */
	
		$max_expediente2 		= trim($_POST['consecutivo_expediente']);	
		// $max_expediente3 	= str_pad($max_expediente2, 7, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 7=longitud
		$numero_expediente 		= "EXP".$year.$codigo_dependencia.$codigo_serie.$codigo_subserie.$max_expediente2;

		$query_expediente 		= "insert into expedientes (id, id_expediente, nombre_expediente, serie, subserie, fecha_inicial, creador_expediente, fecha_apertura_exp, year_expediente, dependencia_expediente) values('$max_id_expediente2', '$numero_expediente', '$nombre_expediente', '$codigo_serie', '$codigo_subserie', '$timestamp', '$usuario', '$timestamp', '$year', '$codigo_dependencia');";
/*
		$query_update_radicado 	= "update radicado set id_expediente='$numero_expediente,' where numero_radicado='$numero_radicado'; update expedientes set lista_radicados='$numero_radicado,' where id_expediente='$numero_expediente';";
		$query_historico 		= "insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$numero_radicado', '$usuario', 'Creacion contrato expediente y anexos', 'Se realiza la creación del expediente <b>$numero_expediente</b> junto con el contrato y los anexos respectivos por el usuario $usuario.', '$timestamp');";
*/
	/* Condición si llega un archivo, quiere decir que es el módulo de agregar contrato por lo que hay que generar la query_radicado, query_anexos y query_expediente concatenadas. */
		if(isset($_FILES['pdf_contrato']['name'])){
			$query_completa_expediente_radicado_anexos = $query_radicado.$query_anexos.$query_expediente.$query_update_radicado.$query_historico;
		}else{
			$query_completa_expediente_radicado_anexos = $query_expediente;
		}	
		/* Genera la creación del expediente */
		if(pg_query($conectado,$query_completa_expediente_radicado_anexos)){
			echo "<script>auditoria_general(\"$tipo_formulario\",\"$numero_expediente\")</script>";
		}else{
			echo "<script> Ocurrió un error al realizar la creación del expediente, por favor revisa e intenta nuevamente.</script>";
		}

	}else if($tipo_formulario=='modificar_expediente'){ // desde index_expedientes.php

		$id_expediente_mod=$_POST['id_expediente_mod'];	
		$nombre_expediente_mod=$_POST['nombre_expediente_mod'];
		$nombre_expediente_mod1="($nombre_expediente_mod)";

		$query_modificar_expediente="UPDATE expedientes set nombre_expediente='$nombre_expediente_mod' where id=$id_expediente_mod";
		
		if(pg_query($conectado,$query_modificar_expediente)){
			echo "<script> 
				auditoria_general(\"$tipo_formulario\",\"$nombre_expediente_mod1\");	
			</script>";		
		}else{
			echo "<script> Ocurrió un error al realizar la modificación, por favor revisa e intenta nuevamente.</script>";
		}
	}else{
		echo "Error. No viene de un formulario definido.";
	}
}
?>