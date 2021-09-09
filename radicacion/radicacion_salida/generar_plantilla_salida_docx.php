<?php 
/*******************************************************************************************************/
/* ESTE ARCHIVO YA NO TIENE UTILIDAD YA QUE EL MODULO HA SIDO MODIFICADO */
/*******************************************************************************************************/
// 	require_once("../../login/validar_inactividad.php");

// /***********************************************************************************************/
// /* Funciones para procesar docx*/
// function agregar_zip($dir, $zip, $newdir) {
//   //verificamos si $dir es un directorio
//   	if (is_dir($dir)) {
//     //abrimos el directorio y lo asignamos a $da
// 	    if ($da = opendir($dir)) {
// 	//leemos del directorio hasta que termine
// 	    	while (($archivo = readdir($da)) !== false) {
// 	/* Si es un directorio imprimimos la ruta y llamamos recursivamente esta función para que verifique dentro del nuevo directorio por mas directorios o archivos */
// 	        	if (is_dir($dir . $archivo) && $archivo != "." && $archivo != "..") {
// 	        		$nuevodir = str_replace($newdir, "", $dir);
// 	          		agregar_zip($dir . $archivo . "/", $zip, $newdir);
// 					// echo "<strong>Creando directorio: $dir$archivo</strong><br/>";
	 
// 	/* Si encuentra un archivo imprimimos la ruta donde se encuentra y agregamos el archivo al zip junto con su ruta */
// 	        	} elseif (is_file($dir . $archivo) && $archivo != "." && $archivo != "..") {
// 		        	$nuevodir = str_replace($newdir, "", $dir.$archivo);
// 		          	$zip->addFile($dir.$archivo,$nuevodir);
// 		          	// echo "Agregando archivo: $nuevodir<br/>";
// 	        	}
// 	      	}
// 	//cerramos el directorio abierto en el momento
// 	      	closedir($da);
// 	    }
//  	}
// }
// /* Funcion para borrar carpeta */
// function deleteDirectory($dir) {
//     if(!$dh = @opendir($dir)) return;
//     while (false !== ($current = readdir($dh))) {
//         if($current != '.' && $current != '..') {
//             // echo 'Se ha borrado el archivo '.$dir.'/'.$current.'<br/>';
//             if (!@unlink($dir.'/'.$current)) 
//                 deleteDirectory($dir.'/'.$current);
//         }       
//     }
//     closedir($dh);
//     // echo 'Se ha borrado el directorio '.$dir.'<br/>';
//     @rmdir($dir);
// }
// /* Fin funcion para borrar carpeta */

// /* Fin funciones para procesar docx*/
// /***********************************************************************************************/
// /* Se definen variables para procesar la plantilla */
	
// 	$fecha 						= $_POST['fecha'];
// 	$tratamiento 				= $_POST['tratamiento'];
// 	$destinatario 				= $_POST['destinatario'];
// 	$cargo_destinatario 		= $_POST['cargo_destinatario'];
// 	$empresa_destinatario 	 	= $_POST['empresa_destinatario_doc'];
// 	$telefono 			 	 	= $_POST['telefono_doc'];
// 	$ubicacion 			 	 	= $_POST['ubicacion_doc'];
// 	$direccion 			 	 	= $_POST['direccion_doc'];
// 	$asunto 			 	 	= $_POST['asunto_doc'];
// 	$despedida			 	 	= $_POST['despedida_doc'];
// 	$mail				 	 	= $_POST['mail_doc'];
// 	$firmante			 	 	= $_POST['firmante_doc'];
// 	$cargo_firmante		 	 	= $_POST['cargo_firmante_doc'];
// 	$anexos 		 	 		= $_POST['anexos_doc'];
// 	$cc_doc 		 	 		= $_POST['cc_doc'];
// 	$aprueba_doc 		 	 	= $_POST['aprueba_doc'];
// 	$cargo_aprueba_doc 		  	= $_POST['cargo_aprueba_doc'];
// 	$elabora_doc 		  		= $_POST['elabora_doc'];
// 	$cargo_elabora_doc 		  	= $_POST['cargo_elabora_doc'];
// 	$codigo_serie 		  		= $_POST['codigo_serie'];
// 	$codigo_subserie 			= $_POST['codigo_subserie'];
// 	$id_expediente 				= $_POST['id_expediente'];
// 	$codigo_contacto 			= $_POST['codigo_contacto'];

// /* Primero se genera el radicado de salida */
// 	$tipo_radicado 				= 2; 			// Tipo de radicado (3- Interna, 1- Entrada, 2- Salida, etc)
// 	$year 						= date("Y"); 	// Se obtiene el año en formato 4 digitos 

// 	// require_once('../../login/conexion2.php');				// Variable de conexion
// 	require_once('../../login/validar_consecutivo.php'); 	// Valida si el consecutivo existe y genera el radicado

// 	$usuario_destino 		= $login_usuario.",";

// /* Se hace la creación del archivo.zip */
// 	mkdir("../../bodega_pdf/plantilla_generada_tmp/$radicado/",0777,true);

// 	$fichero 		= '../../bodega_pdf/plantilla_origen/plantilla_salida.docx';
// 	$nuevo_fichero 	= "../../bodega_pdf/plantilla_generada_tmp/$radicado/$radicado.zip";

// 	if (!copy($fichero, $nuevo_fichero)) {
// 	    echo "Error al copiar $fichero...\n";
// 	}

// 	$zip = new ZipArchive;
// 	$res = $zip->open($nuevo_fichero);
// 	if ($res === TRUE) {
//  // Unzip path
//  	$path = "../../bodega_pdf/plantilla_generada_tmp/$radicado/";

//  // Extract file
// 	 $zip->extractTo($path);
// 	 $zip->close();

//  	unlink($nuevo_fichero);
// }else{
//  	echo 'failed!';
// }

// $archive 	= "$path/word/document.xml";
// $hand 		= fopen($archive,'a+');
// $xmlRead 	= fread($hand,filesize($archive));

// $xmlRead 	= str_replace("{FECHA}",$fecha,$xmlRead);
// $xmlRead 	= str_replace("{TRATAMIENTO}",$tratamiento,$xmlRead);
// $xmlRead 	= str_replace("{DESTINATARIO}",$destinatario,$xmlRead);
// $xmlRead 	= str_replace("{CARGO_DESTINATARIO}",$cargo_destinatario,$xmlRead);
// $xmlRead 	= str_replace("{EMPRESA_DESTINATARIO}",$empresa_destinatario,$xmlRead);
// $xmlRead 	= str_replace("{TELEFONO}",$telefono,$xmlRead);
// $xmlRead 	= str_replace("{UBICACION}",$ubicacion,$xmlRead);
// $xmlRead 	= str_replace("{MAIL}",$mail,$xmlRead);
// $xmlRead 	= str_replace("{DIRECCION}",$direccion,$xmlRead);
// $xmlRead 	= str_replace("{RADICADO}",$radicado,$xmlRead);
// $xmlRead 	= str_replace("{ASUNTO_DOC}",$asunto,$xmlRead);
// $xmlRead 	= str_replace("{DESPEDIDA}",$despedida,$xmlRead);
// $xmlRead 	= str_replace("{FIRMANTE}",$firmante,$xmlRead);
// $xmlRead 	= str_replace("{CARGO_FIRMANTE}",$cargo_firmante,$xmlRead);
// $xmlRead 	= str_replace("{ANEXOS_DOC}",$anexos,$xmlRead);
// $xmlRead 	= str_replace("{CC_DOC}",$cc_doc,$xmlRead);
// $xmlRead 	= str_replace("{APRUEBA_DOC}",$aprueba_doc,$xmlRead);
// $xmlRead 	= str_replace("{CARGO_APRUEBA_DOC}",$cargo_aprueba_doc,$xmlRead);
// $xmlRead 	= str_replace("{ELABORA_DOC}",$elabora_doc,$xmlRead);
// $xmlRead 	= str_replace("{CARGO_ELABORA_DOC}",$cargo_elabora_doc,$xmlRead);

// fclose($hand);
// unlink($archive);
// $hand 		= fopen($archive,'a+');
// fwrite($hand, $xmlRead);
// fclose($hand);

// // Se crea una instancia de ZipArchive
// $zip = new ZipArchive();
// /*directorio a comprimir. La barra inclinada al final es importante, la ruta debe ser relativa no absoluta */
// $dir = "../../bodega_pdf/plantilla_generada_tmp/$radicado/";
 
// //ruta donde guardar los archivos zip, ya debe existir
// $archivoZip = "../../bodega_pdf/plantilla_generada_tmp/$radicado.zip";
// $newname 	= "../../bodega_pdf/plantilla_generada_tmp/$radicado.docx";
// $link_descarga = "bodega_pdf/plantilla_generada_tmp/$radicado.docx";
 

// if ($zip->open($archivoZip, ZIPARCHIVE::CREATE) === true) {
//   agregar_zip($dir, $zip,$dir);
//   $zip->close();
 
//   //Muevo el archivo a una ruta donde no se mezcle los zip con los demas archivos
//   // rename($archivoZip, "$rutaFinal/$archivoZip");
 
//   //Hasta aqui el archivo zip ya esta creado
  
//   //Verifico si el archivo ha sido creado
//   	if (file_exists($archivoZip)) {
//     	// echo "Proceso Finalizado!! <br/><br/>Descargar: <a href='$link_descarga'>$link_descarga</a>";
//     	echo "docx_listo";
//   	}else{
//     	echo "Error, archivo zip no ha sido creado!!";
//   	}
// }
// rename($archivoZip, $newname);
// deleteDirectory($dir);

// ?>
<!--  <script type="text/javascript">
	var radicado = "<?php // echo $radicado; ?>";
 	$("#contenedor_boton_descargar_plantilla_respuesta").html("<a href='bodega_pdf/plantilla_generada_tmp/"+radicado+".docx'><input type='button' id='descargar_plantilla_respuesta' onclick='descargar_plantilla_respuesta()' class='botones2 center' value='Descargar ésta plantilla de salida'></a>");
 </script> -->
 <?php 
// /* Se inicia proceso de insertar en base de datos la información del radicado de salida y el expediente */
	
// 	// Se arma el json para codigo_carpeta1 de la tabla radicado
// 	$codigo_carpeta1="'{\"$login_usuario\":{\"codigo_carpeta_personal\":\"entrada\"}}'";
// 	// Fin del armado del json para codigo_carpeta1 de la tabla radicado
// 	$leido = "$login_usuario,";

// 	$query_radicado="insert into radicado(numero_radicado, codigo_carpeta1, descripcion_anexos, codigo_contacto, dependencia_actual, usuario_actual, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, id_expediente, codigo_serie, codigo_subserie, termino, estado_radicado) 
// 	 	values('$radicado', $codigo_carpeta1, '$anexos', '$codigo_contacto', '$dependencia_usuario', '$login_usuario', '$dependencia_usuario', '$login_usuario', '$asunto', '$nivel_seguridad', '$leido', '$id_expediente', '$codigo_serie', '$codigo_subserie', '15', 'en_tramite');";

// 	$query_datos_origen_radicado = "insert into datos_origen_radicado(numero_radicado, nombre_remitente_destinatario, dignatario, ubicacion, direccion, telefono, mail) values ('$radicado', '$empresa_destinatario', '$destinatario', '$ubicacion', '$direccion', '$telefono', '$mail');"; 	

// 	/* Se arma la query para el expediente si aplica. */ 	
// 	if($id_expediente!=""){
// 		$query_exp 	= "select lista_radicados, nombre_expediente from expedientes where id_expediente='$id_expediente'";
// 			$fila_exp 	  		= pg_query($conectado,$query_exp);
// 			$linea_exp    		= pg_fetch_array($fila_exp);
// 			$lista_radicados 	= $linea_exp['lista_radicados']; // Listado de radicados en tabla expediente
// 			$nombre_expediente 	= $linea_exp['nombre_expediente']; // Listado de radicados en tabla expediente

// 			$lista_radicados.="$radicado,";

// 			$query_expedientes = "update expedientes set lista_radicados='$lista_radicados' where id_expediente='$id_expediente';";
// 			$query_rad_exp=$query_radicado.$query_datos_origen_radicado.$query_expedientes;
	
// 			$transaccion_historico 	= "Genera plantilla salida y asocia en expediente";	// Variable para tabla historico_eventos
// 			$comentario				= "Se ha generado la plantilla del radicado de salida $radicado y se asocia directamente al expediente $id_expediente($nombre_expediente)";	// Variable para historico eventos
// 			$transaccion 			= "plantilla_salida_expediente"; 	// Variable para auditoria
// 	}else{
// 		$query_rad_exp=$query_radicado;

// 		$transaccion_historico 	= "Genera plantilla salida";	// Variable para tabla historico_eventos
// 		$comentario				= "Se ha generado la plantilla del radicado de salida $radicado de salida.";	// Variable para historico eventos
// 		$transaccion 			= "plantilla_salida"; 	// Variable para auditoria
// 	}
// 	/* Fin de armar la query para el expediente si aplica. */ 	
	
// 	if(pg_query($conectado,$query_rad_exp)){		
// 		/* Desde aqui se genera historico */	
// 			$creado 			= "$radicado";	// Variable para auditoria
// 			require_once("../../login/inserta_historico.php");		
// 		/* Hasta aqui se genera historico */	
// 	}else{
// 		echo "<script> alert('Ocurrió un error al generar el radicado de salida, por favor contactar con el administrador del sistema.')</script>";
// 	}

// /* Fin proceso de insertar en base de datos la información del radicado padre y el radicado respuesta */

?>