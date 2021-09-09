<?php 
/*******************************************************************************************************/
/* ESTE ARCHIVO YA NO TIENE UTILIDAD YA QUE EL MODULO HA SIDO MODIFICADO */
/*******************************************************************************************************/
	// require_once("../../login/validar_inactividad.php");

/***********************************************************************************************/
/* Funciones para procesar docx*/
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
	
// 	// var_dump($_POST);

// 	$fecha 						= $_POST['fecha'];
// 	$radicado_padre 			= $_POST['radicado'];
// 	$tratamiento 				= $_POST['tratamiento'];
// 	$destinatario 				= $_POST['destinatario'];
// 	$cargo_destinatario 		= $_POST['cargo_destinatario'];
// 	$empresa_destinatario 	 	= $_POST['empresa_destinatario_doc'];
// 	$direccion 			 	 	= $_POST['direccion_doc'];
// 	$asunto 			 	 	= $_POST['asunto_doc'];
// 	$despedida			 	 	= $_POST['despedida_doc'];
// 	$firmante			 	 	= $_POST['firmante_doc'];
// 	$cargo_firmante		 	 	= $_POST['cargo_firmante_doc'];
// 	$anexos 		 	 		= $_POST['anexos_doc'];
// 	$cc_doc 		 	 		= $_POST['cc_doc'];
// 	$aprueba_doc 		 	 	= $_POST['aprueba_doc'];
// 	$cargo_aprueba_doc 		  	= $_POST['cargo_aprueba_doc'];
// 	$elabora_doc 		  		= $_POST['elabora_doc'];
// 	$cargo_elabora_doc 		  	= $_POST['cargo_elabora_doc'];

// /* Se hace la creación del archivo.zip */
// 	mkdir("../../bodega_pdf/plantilla_generada_tmp/$radicado_padre/",0777,true);

// 	$fichero 		= '../../bodega_pdf/plantilla_origen/pjonas.docx';
// 	$nuevo_fichero 	= "../../bodega_pdf/plantilla_generada_tmp/$radicado_padre/$radicado_padre.zip";

// 	if (!copy($fichero, $nuevo_fichero)) {
// 	    echo "Error al copiar $fichero...\n";
// 	}

// 	$zip = new ZipArchive;
// 	$res = $zip->open($nuevo_fichero);
// 	if ($res === TRUE) {
//  // Unzip path
//  	$path = "../../bodega_pdf/plantilla_generada_tmp/$radicado_padre/";

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
// // $nombre 	= "Yuri Deivis Galvis";
// // $cargo 		= "Gerente General de Mi Corazoncito";
// $xmlRead 	= str_replace("{FECHA}",$fecha,$xmlRead);
// $xmlRead 	= str_replace("{TRATAMIENTO}",$tratamiento,$xmlRead);
// $xmlRead 	= str_replace("{DESTINATARIO}",$destinatario,$xmlRead);
// $xmlRead 	= str_replace("{CARGO_DESTINATARIO}",$cargo_destinatario,$xmlRead);
// $xmlRead 	= str_replace("{EMPRESA_DESTINATARIO}",$empresa_destinatario,$xmlRead);
// $xmlRead 	= str_replace("{DIRECCION}",$direccion,$xmlRead);
// $xmlRead 	= str_replace("{RADICADO}",$radicado_padre,$xmlRead);
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
// $dir = "../../bodega_pdf/plantilla_generada_tmp/$radicado_padre/";
 
// //ruta donde guardar los archivos zip, ya debe existir
// $archivoZip = "../../bodega_pdf/plantilla_generada_tmp/$radicado_padre.zip";
// $newname 	= "../../bodega_pdf/plantilla_generada_tmp/$radicado_padre.docx";
// $link_descarga = "bodega_pdf/plantilla_generada_tmp/$radicado_padre.docx";
 

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
// /* Se inicia proceso de insertar en base de datos la información del radicado padre y el radicado respuesta */
// /* Primero se genera el radicado de salida */
// 	$tipo_radicado 				= 2; 			// Tipo de radicado (3- Interna, 1- Entrada, 2- Salida, etc)
// 	$year 						= date("Y"); 	// Se obtiene el año en formato 4 digitos 

// 	// require_once('../../login/conexion2.php');				// Variable de conexion
// 	require_once('../../login/validar_consecutivo.php'); 	// Valida si el consecutivo existe y genera el radicado

// 	$usuario_destino 		= $login_usuario.",";

// 	$query_radicado_padre 	= "select * from radicado where numero_radicado='$radicado_padre'";
// 	$fila_radicado_padre 	= pg_query($conectado,$query_radicado_padre);
// 	$linea_radicado_padre 	= pg_fetch_array($fila_radicado_padre);

// 	$id_expediente 	= $linea_radicado_padre['id_expediente'];
// 	$leido 			= $linea_radicado_padre['leido'];

// 	$query_radicado="insert into radicado(numero_radicado, dependencia_actual, usuario_actual, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, id_expediente, estado_radicado, descripcion_anexos) 
// 		values('$radicado', '$dependencia_usuario', '$login_usuario', '$dependencia_usuario', '$login_usuario', '$asunto', '$nivel', '$leido', '$id_expediente', 'respuesta', '$anexos')";

// 	$query_respuesta = "insert into respuesta_radicados(radicado_padre, radicado_respuesta)values('$radicado_padre','$radicado')";	
// 	if(pg_query($conectado,$query_radicado)){
// 		if(pg_query($conectado,$query_respuesta)){
// 		/* Desde aqui se genera historico */	
// 			$transaccion_historico 	= "Genera plantilla salida respuesta";				// Variable para tabla historico_eventos
// 			$comentario				= "Se ha generado la plantilla del radicado $radicado como respuesta al radicado $radicado_padre";	// Variable para historico eventos

// 			$transaccion 			= "plantilla_salida_respuesta"; 	// Variable para auditoria
// 			$creado 				= "$radicado_padre";	// Variable para auditoria
// 			$radicado 				= $radicado_padre; 		// Se modifica para historico
// 			require_once("../../login/inserta_historico.php");		
// 		/* Hasta aqui se genera historico */	
// 		}else{
// 			echo "<script> alert('Ocurrió un error al realizar la respuesta al radicado, por favor contactar con el administrador del sistema.')</script>";
// 		}
// 	}else{
// 		echo "<script> alert('Ocurrió un error al generar el radicado de salida como respuesta, por favor contactar con el administrador del sistema.')</script>";
// 	}




// /* Fin proceso de insertar en base de datos la información del radicado padre y el radicado respuesta */

?>