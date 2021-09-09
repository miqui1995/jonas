<?php
/* En este archivo se reciben todos los datos mediante POST, se inserta en tabla radicado, datos_origen_radicado, expediente, version_documentos, se genera el archivo PDF y el HTML ya sea nuevo o modificado */
/* Para entenderlo detalladamente, se divide en varias partes: 
	1. Valida si la sesion existe, valida la inactividad e incluye librerias para generar el PDF.
	2. Se define $year y $month para luego asignarles $mes para poner en la ruta del PDF con el fin de
separar lo que mensualmente se hace para facilitar los backup
	3. Se definen todas las variables que recibe por POST.
	4.1 Si se genera para imprimir ($tipo_solicitud='fisico') no requiere enviarlo a bandejas de
entrada de otros usuarios, sino unicamente al usuario que lo genera por lo que se arma la variable $usuarios_visor.
	4.2 Si se genera electronicamente ($tipo_solicitud='electronico') requiere enviarlo a bandejas de
entrada de otros usuarios y armar el JSONB del campo "firmas_electronicas" de la tabla version_documentos
	5.1 En este caso es un radicado nuevo o es una respuesta por lo que se definen variables para las tablas correspondientes 
	5.2 En este caso es un radicado existente es decir, para modificar el radicado por lo que se definen variables para las tablas correspondientes 
	6. Arma la $query_version_documento y finalmente la $query_radicado_completo. Hay una variación si el $tipo_radicacion="respuesta"
	7. Inicia con la generación del QR para poner en el PDF del radicado
	8. Inicio genera imagen_firma dependiendo si $tipo_solicitud= fisico o electronico
	9. Reemplaza del html recibido las variables *NUMERO_RADICADO_FINAL* e *IMAGEN_FIRMA* (Generada en el punto 8.) y genera el PDF del documento 
	10. Ejecuta la $query_radicado_completo e inserta historicos.
*/

/* Parte 1. Valida si la sesion existe, valida la inactividad e incluye librerias para generar el PDF */
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

/* Parte 2. Se define $year y $month para luego asignarles $mes para poner en la ruta del PDF con el fin de
separar lo que mensualmente se hace para facilitar los backup */
$year  = date("Y");
$month = date("m");

switch ($month){
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

Dompdf\Autoloader::register();

/* Parte 3. Se definen todas las variables que recibe por POST */
$fecha 					= date('Y-m-d H:i:s');	// Genera la fecha de transaccion
$year 					= date("Y"); 	// Se obtiene el año en formato 4 digitos 

$aleatorio_recibido 		= trim($_POST["nameArchive"]);
$anexos 		 	 		= trim($_POST['anexos_doc']);
$aprobado 	 				= trim($_POST['aprobado_doc']); // Si el documento está aprobado SI o NO
$aprueba_doc 		 		= trim($_POST['aprueba_doc']);
$asunto_doc 				= trim($_POST['asunto_doc']);
$cargo_aprueba_doc 			= trim($_POST['cargo_aprueba_doc']);
$cargo_destinatario 		= trim($_POST['cargo_destinatario']);
$cargo_elabora_doc 			= trim($_POST['cargo_elabora_doc']);
$cargo_firmante_doc 		= trim($_POST['cargo_firmante_doc']);
$cc_doc 					= trim($_POST['cc_doc']);
$codigo_serie 		  		= trim($_POST['codigo_serie']);
$codigo_subserie 			= trim($_POST['codigo_subserie']);
$despedida_doc 				= trim($_POST['despedida_doc']);
$destinatario 				= trim($_POST['destinatario']);
$direccion 			 		= trim($_POST['direccion_doc']);
$elabora_doc 		  		= trim($_POST['elabora_doc']);
$empresa_destinatario 		= trim($_POST['empresa_destinatario_doc']);
$firmante_doc           	= trim($_POST['firmante_doc']);
$html 						= trim($_POST['html']);
$id_expediente 				= trim($_POST['id_expediente']);
$lista_firma_aprueba_revisa = trim($_POST['lista_firma_aprueba_revisa']);
$login_aprueba 				= trim($_POST['login_aprueba']); // Login del usuario que aprueba el documento
$login_elabora 				= trim($_POST['login_elabora']); // Login del usuario que elabora el documento
$login_firmante 			= trim($_POST['login_firmante']); // Login del usuario que firma el documento
$mail				 		= trim($_POST['mail_doc']);
$numero_radicado 			= trim($_POST['numero_radicado']);
$pre_asun  					= trim($_POST['pre_asunto']);
$pre_asunto 				= trim(str_replace("'", "", $pre_asun));
$revisa_doc1            	= trim($_POST['revisa_doc1']);
$revisa_doc2            	= trim($_POST['revisa_doc2']);
$revisa_doc3            	= trim($_POST['revisa_doc3']);
$revisa_doc4            	= trim($_POST['revisa_doc4']);
$revisa_doc5            	= trim($_POST['revisa_doc5']);
$tamano 					= trim($_POST['tamano']); 
$telefono 			 		= trim($_POST['telefono_doc']);
$tipo_radicacion 			= trim($_POST['tipo_radicacion']); 	// Tipo de radicacion (respuesta, etc) para heredar a ../../login/validar_consecutivo.php
$tipo_radicado 				= trim($_POST['tipo_radicado']); 		// Tipo de radicado (3- Interna, 1- Entrada, 2- Salida, etc) para heredar a ../../login/validar_consecutivo.php
$tipo_solicitud         	= trim($_POST['tipo_solicitud']); // Si documento se imprime o si es con firma electronica
$tratamiento_doc 			= trim($_POST['tratamiento_doc']);
$ubicacion 			 		= trim($_POST['ubicacion_doc']);
$usuario_actual 			= trim($_POST['usuario_actual']);
$usuario_radicador 			= trim($_POST['usuario_radicador']);
$version_documento 			= trim($_POST['version_documento']);
/* Se inician datos para guardar en tabla datos_origen_radicado */	

/* Parte 4.1 Si se genera para imprimir ($tipo_solicitud='fisico') no requiere enviarlo a bandejas de
entrada de otros usuarios, sino unicamente al usuario que lo genera. */
if($tipo_solicitud=='fisico'){
	if($numero_radicado=="" or $tipo_radicacion=='respuesta'){}else{
		$consulta_radicado 						= "select * from radicado where numero_radicado='$numero_radicado'";
		$fila_radicado 	  						= pg_query($conectado,$consulta_radicado);
		$linea_radicado    						= pg_fetch_array($fila_radicado);
		$codigo_carpeta1 						= $linea_radicado['codigo_carpeta1'];
		$usuarios_visor2 						= $linea_radicado['usuarios_visor'];
		$codigo_carpeta2 						= json_decode($codigo_carpeta1,true);		
		$codigo_carpeta2[$usuario_radicador] 	= array('codigo_carpeta_personal'=>'entrada'); // Se pone en la bandeja de entrada del $usuario_radicador
		$codigo_carpeta3 						= json_encode($codigo_carpeta2);
		
	    $aprobado = 'SI'; // Se define la variable para que no aparezca pendiente por aprobar ya que es impreso en físico en la tabla version_documentos

		/* Se arma la variable $usuarios_visor */
		$usu  = explode(",", $usuarios_visor2);
		$max  = sizeof($usu);
		$max2 = $max-1;

		/* Se arma la variable $usuarios_visor para cuando $tipo_solicitud='fisico' */
		$usuarios_visor = "";
		if($max2==0){
			$usuarios_visor.="$login_elabora,";
		}else{
			$usuarios_visor.="$lista_firma_aprueba_revisa";
			for ($q=0; $q < $max2; $q++){  // Recorre uno a uno los usuarios del listado separado por comas.
				$login_usuario_busq = $usu[$q];
				/* Verifica si el $login_usuario_busq existe en la $lista_firma_aprueba_revisa*/
				$pos = strpos($lista_firma_aprueba_revisa, $login_usuario_busq);

				if ($pos === false) {
					// Si no existe en el listado, lo agrega.
					$usuarios_visor.="$login_usuario_busq,";				    
				}
			}	
		}		
	} 
}else{ // Para firmar electronicamente
	/* Parte 4.2 Si se genera electronicamente ($tipo_solicitud='electronico') requiere enviarlo a bandejas de
entrada de otros usuarios y armar el JSONB del campo "firmas_electronicas" de la tabla version_documentos */
	/************************************************************** 
	* @brief Funcion PHP para consultar y devolver JSON de los datos de un usuario consultado 
	** por Login

	* @description Recibe como parámetro el login de un usuario y devuelve un string en formato de 
	** cadena JSON para armar la variable de $firmas_electronicas.

	* @param string{$login_usuario} Login del usuario a consultar. 
	* @param string{$firmado} (SI/NO) Valor si el usuario que realiza la acción es la misma que firma el documento. 

	* @return {} Retorna JSON en formato STRING ({\"login\":\"\",\"nombre_completo\":\"\",\"cargo\":\"\",\"path_firma\": \"\",\"firmado\":\"\",\"fecha_firmado\":\"\"}) con los campos correspondientes. 
	**************************************************************/
	function datos_usuario_firma($login_usuario,$firmado){
		if($login_usuario==""){
			$return = "{\"login\":\"\",\"nombre_completo\":\"\",\"cargo\":\"\",\"path_firma\": \"\",\"firmado\":\"\",\"fecha_firmado\":\"\"}";
		}else{
			include '../../login/conexion2.php';

			$query_usuario_firma = "select * from usuarios where login='$login_usuario'";

			$fila_usuario_firma  = pg_query($conectado,$query_usuario_firma);
			$linea_usuario_firma = pg_fetch_array($fila_usuario_firma);

			$nombre_completo 	= trim($linea_usuario_firma['nombre_completo']);
			$cargo_usuario 		= trim($linea_usuario_firma['cargo_usuario']);
			$path_firma 		= trim($linea_usuario_firma['path_firma']);

			$return = "{\"login\":\"$login_usuario\",\"nombre_completo\":\"$nombre_completo\",\"cargo\":\"$cargo_usuario\",\"path_firma\": \"$path_firma\",\"firmado\":\"$firmado\",\"fecha_firmado\":\"\"}";
		}
		return $return;
	}

			
	/* Se arma la variable "firmas_electronicas" */
	$firmas_electronicas = "{";
	
	$firmado1 = $login_firmante==$login_elabora ? 'SI':'NO';
	$firmado2 = $login_aprueba==$login_elabora ? 'SI':'NO';

	$firma1 = datos_usuario_firma($login_firmante,$firmado1); 	// Genero JSON usuario que Firma
	$firma2 = datos_usuario_firma($login_aprueba,$firmado2); 	// Genero JSON usuario que aprueba 	
	
	/* Se elimina del $listado_usuarios_revisa_aprueba*/
	$listado_usuarios_revisa_aprueba = str_replace("$login_elabora,", "", $lista_firma_aprueba_revisa);
	$listado_usuarios_revisa_aprueba = str_replace("$login_firmante,", "", $listado_usuarios_revisa_aprueba);
	$listado_usuarios_revisa_aprueba = str_replace("$login_aprueba,", "", $listado_usuarios_revisa_aprueba);

	// Extraigo cada uno de la $lista_firma_aprueba_revisa para usarla para armar el json para codigo_carpeta1 de la tabla radicado
	$usu  = explode(",", $listado_usuarios_revisa_aprueba);
	$max  = sizeof($usu);
	$max2 = $max-1;

	/* Genero JSON usuarios que revisan */
	$usuarios_revisa = "";
	for ($r=0; $r < 5; $r++){  // Recorre uno a uno los usuarios del $listado_usuarios_revisa_aprueba separado por comas.
		$id_ciclo=$r+1;

		if($r<=$max2){
			/* Login de cada uno de los usuarios de $lista_firma_aprueba_revisa */
			$login_usuario_busr = $usu[$r];

			$revisa = $login_usuario_busr==$login_elabora ? 'SI':'NO';

			$firma_r = datos_usuario_firma($login_usuario_busr,$revisa); 	// Genero JSON usuario que Firma
		}else{
			$firma_r = datos_usuario_firma('',''); 	// Genero JSON usuario que Firma
		}

		if($id_ciclo==5){
			$usuarios_revisa.="\"usuario_revisa$id_ciclo\":$firma_r";
		}else{
			$usuarios_revisa.="\"usuario_revisa$id_ciclo\":$firma_r ,";
		}
	}

	$firmas_electronicas.= "\"usuario_firma\":$firma1 , \"usuario_aprueba\":$firma2 , $usuarios_revisa}";

	/* Para iniciar el proceso de firmas electrónicas primero se envía a las bandejas de entrada a los usuarios que revisan. */
	/* Ya está la tabla creada, FALTA POR TERMINAR */ 


	// Extraigo cada uno de la $lista_firma_aprueba_revisa para usarla para armar el json para codigo_carpeta1 de la tabla radicado
	$usu  = explode(",", $lista_firma_aprueba_revisa);
	$max  = sizeof($usu);
	$max2 = $max-1;

	/* Se arma la variable $codigo_carpeta1 para radicado nuevo cuando $tipo_solicitud es físico */
	if($max2==0){
		echo "";
	}else{
		for ($q=0; $q < $max2; $q++){  // Recorre uno a uno los usuarios del listado separado por comas.
			/* Login de cada uno de los usuarios de $lista_firma_aprueba_revisa */
			$login_usuario_busq = $usu[$q];

			/* Se define si es un documento firmado por la misma persona que lo elabora entonces se pone como $firmado='SI' */
			/* Variable para validar si documento ya está firmado. */
			if($login_firmante==$login_elabora){
				
				if($login_elabora==$login_usuario_busq){
					/* Verifica si existe carpeta personal para Firmado, Aprobado o Revisado con Firma Electronica */    
		            $consulta_carpeta_inventario="select * from carpetas_personales c join usuarios u on c.id_usuario=u.id_usuario where u.login='$login_elabora' and nombre_carpeta_personal='Firmado, Aprobado o Revisado con Firma Electronica'";
		            $fila_cantidad_carpeta_inventario = pg_query($conectado,$consulta_carpeta_inventario);

		    		/* Calcula el numero de registros que genera la consulta anterior. */
		            $registros_carpeta_inventario= pg_num_rows($fila_cantidad_carpeta_inventario);

		            if($registros_carpeta_inventario!=0){  // Cuando si existe la carpeta personal 
		                $linea_carpeta_inventario = pg_fetch_array($fila_cantidad_carpeta_inventario);
		                $codigo_carpeta_inventario=$linea_carpeta_inventario['id'];
		            }else{       // Cuando no existe la carpeta personal
		                $query_cantidad_carpeta_per3 = "select count(*) from carpetas_personales";
		                $fila_cantidad3  = pg_query($conectado,$query_cantidad_carpeta_per3); // La variable "$conectado" la hereda desde conexion2.php
		                $linea_cantidad3 = pg_fetch_array($fila_cantidad3);
		                $cantidad_total1 = $linea_cantidad3[0];
		                $cantidad_total  = $cantidad_total1+1;

		                /* Se consulta el id_usuario para la $query_crear_carpeta */
		                $query_cantidad_carpeta_per4 = "select * from usuarios where login='$login_elabora'";
		            	$fila_cantidad4  	= pg_query($conectado,$query_cantidad_carpeta_per4); // La variable "$conectado" la hereda desde conexion2.php
		                $linea_cantidad4 	= pg_fetch_array($fila_cantidad4);
		                $id_usuario 		= $linea_cantidad4['id_usuario'];

		                $query_crear_carpeta="insert into carpetas_personales (id, nombre_carpeta_personal, id_usuario, activo, fecha_creacion_carpeta_per) values('$cantidad_total', 'Firmado, Aprobado o Revisado con Firma Electronica', '$id_usuario', 'SI', current_timestamp)";
		                if(pg_query($conectado,$query_crear_carpeta)){ // Si crea la carpeta personal 'Inventario'
		                    $codigo_carpeta_inventario=$cantidad_total;
		                }else{
		                    echo "<script>No se ha creado la carpeta personal 'Firmado, Aprobado o Revisado con Firma Electronica'. Por favor comuníquese con el administrador del sistema.'</script>";
		                }
		            }
				    /* Fin verifica si existe carpeta personal para inventario */  
					$codigo_carpeta2 	= "\"$login_usuario_busq\":{\"codigo_carpeta_personal\":\"$codigo_carpeta_inventario\"}";
					$firmado 			= "SI";
					$estado_radicado 	= "tramitado";
					
				}else{
					$codigo_carpeta2 	= "\"$login_usuario_busq\":{\"codigo_carpeta_personal\":\"entrada\"}";
				}
			}else{
				/* Cuando ($login_firmante!=$login_elabora */
				$codigo_carpeta2 = "\"$login_usuario_busq\":{\"codigo_carpeta_personal\":\"entrada\"}";	
			}

			if($q==0){
				$codigo_carpeta1 = "{".$codigo_carpeta2;
			}else{
				$codigo_carpeta1 = $codigo_carpeta1.",".$codigo_carpeta2;
			}
			/* Se agrega como carpeta personal el usuario que genera $login_elabora para que aparezca en la bandeja de entrada al generar el documento */
			$codigo_carpeta1.= ",\"$login_elabora\":{\"codigo_carpeta_personal\":\"entrada\"}";					
		}	
		$codigo_carpeta1.="}"; // Cierra el JSON armado
	}
	$codigo_carpeta3 	= $codigo_carpeta1;
	$usuarios_visor 	= $lista_firma_aprueba_revisa;
}

/* Variable para validar si documento ya está firmado. */
$firmado 			= 'NO';
$estado_radicado	= 'en_tramite';
// var_dump($_POST);

// echo "<br><br> --->$tipo_solicitud";

// echo("$codigo_carpeta1");

// $listado_usuarios_revisa_aprueba = str_replace("$login_elabora,", "", $lista_firma_aprueba_revisa);
// $listado_usuarios_revisa_aprueba = str_replace("$login_firmante,", "", $listado_usuarios_revisa_aprueba);
// $listado_usuarios_revisa_aprueba = str_replace("$login_aprueba,", "", $listado_usuarios_revisa_aprueba);

/* 5.1 En este caso es un radicado nuevo o es una respuesta */
if($numero_radicado=="" or $tipo_radicacion=='respuesta'){ 
	$version 	= "1"; // Ya que es un radicado nuevo o una respuesta, la version es la numero 1
	require_once('../../login/validar_consecutivo.php'); 	// Valida si el consecutivo existe y genera el radicado

	/* Se arma la query para agregar al listado del expediente el radicado nuevo */
	if($id_expediente!=""){
		if(strpos($id_expediente,",")){
			$id_expediente1 = substr($id_expediente, 0, strpos($id_expediente, ","));
		}else{
			/* Se valida si recibe el $id_expediente sin comas. Esto aplica para radicados nuevos.*/
			$id_expediente1 = $id_expediente;
			$id_expediente.=",";
		}

		$query_exp 			= "select lista_radicados, nombre_expediente from expedientes where id_expediente='$id_expediente1'";
		$fila_exp 	  		= pg_query($conectado,$query_exp);
		$linea_exp    		= pg_fetch_array($fila_exp);
		$lista_radicados 	= $linea_exp['lista_radicados']; // Listado de radicados en tabla expediente
		$nombre_expediente 	= $linea_exp['nombre_expediente']; // Listado de radicados en tabla expediente

		if(strpos($lista_radicados, $radicado)){
			$lista_radicados = $lista_radicados;		
		}else{
			$lista_radicados.="$radicado,";
		}
	
		$query_expedientes 	= "update expedientes set lista_radicados='$lista_radicados' where id_expediente='$id_expediente1';";
	}else{
		$query_expedientes 	= "";
	}
	/* Fin arma la query para agregar al listado del expediente el radicado nuevo */

	// Se define el nombre del archivo PDF y HTML
	$nombre_archivo 	= $radicado."_".$aleatorio_recibido."_".$version;
	$path_pdf  			= "$year/$mes/".$nombre_archivo.".pdf";
	
	/* Valida si en el $lista_firma_aprueba_revisa está el usuario $login_elabora y lo agrega en caso que no */
	$pos2  				= strpos($lista_firma_aprueba_revisa, $login_elabora);
	$usuarios_visor 	= $lista_firma_aprueba_revisa;
	if ($pos2 === false) {
		// Si no existe en el listado, lo agrega.
		$usuarios_visor.="$login_elabora,";				    
	}

	$usuarios_control  	= "$login_elabora,";
	$codigo_carpeta1 = "'{"."\"$login_elabora\":{\"codigo_carpeta_personal\":\"entrada\"}}'";	


	/* Se determina si el que aprueba es el mismo que elabora */
	if(trim($nombre) == trim($aprueba_doc)){
		$aprobado = 'SI';
	}else{
		$aprobado = 'NO';
	}

	/* Query para radicado en caso que es un radicado nuevo - Las variables $dependencia_usuario, $login_usuario las hereda desde (../../login/validar_consecutivo) y la variable $hora la hereda desde login/validar_inactividad.php */
	$query_radicado="insert into radicado(numero_radicado, fecha_radicado, codigo_carpeta1, numero_guia_oficio, descripcion_anexos, path_radicado, dependencia_actual, usuarios_visor, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, clasificacion_radicado, termino, id_expediente, codigo_serie, codigo_subserie, usuarios_control, estado_radicado, medio_recepcion)values('$radicado', '$hora', $codigo_carpeta1, 'No se ha enviado al destinatario todavía, por lo tanto no hay soporte de envío.', '$anexos', '$path_pdf', '$dependencia_usuario', '$usuarios_visor', '$dependencia_usuario', '$login_usuario', '$asunto_doc', '1', '$usuarios_visor', 'OFICIO', '15', '$id_expediente', '$codigo_serie', '$codigo_subserie', '$usuarios_control','$estado_radicado', 'radicacion_plantilla_salida');";	

	$query_datos_origen_radicado = "insert into datos_origen_radicado(numero_radicado, nombre_remitente_destinatario, dignatario, ubicacion, direccion, telefono, mail) values ('$radicado', '$empresa_destinatario', '$destinatario', '$ubicacion', '$direccion', '$telefono', '$mail');";  

	/* Query para actualizar la version. Como es un radicado nuevo, se deja vacío */
	$query_version_documento2 = "";

	/* Variables para historico */
	$transaccion_historico 	= "Genera plantilla radicacion salida";
	$transaccion 			= "plantilla_salida"; 		// Variable para auditoria

	$comentario	 = "Se ha generado la <b>versión $version</b> del radicado $radicado";	// Variable para historico eventos
	
	if($id_expediente!=""){
		$transaccion_historico 	.= " y asocia en expediente";	// Variable para tabla historico_eventos
		$comentario				.= " y se asocia directamente al expediente <b>$id_expediente($nombre_expediente)</b>";	// Variable para historico eventos
		$transaccion 			.= "_expediente"; 	// Variable para auditoria quedaría plantilla_salida_expediente ó modifica_plantilla_salida_expediente
	}

	/* Por ser un documento en físico, se asigna el documento impreso para recolectar firmas como responsable el usuario que lo genera. */
	$inserta_ubicacion_fisica = "insert into ubicacion_fisica (numero_radicado, usuario_actual, usuario_anterior, fecha)values('$radicado', '$login_elabora', 'Imprime documento físico para recolectar firmas','$hora');";
}else{	
	/* 5.2 En este caso es un radicado existente es decir, para modificar el radicado */
	$version 	= $version_documento;
	$version2 	= $version-1;
	$radicado 	= $numero_radicado; // Es el numero que llega por POST al cual vamos a modificar la información.

	// Se define el nuevo nombre del archivo PDF y HTML
	$nombre_archivo = $radicado."_".$aleatorio_recibido."_".$version;

	$path_pdf  		= "$year/$mes/".$nombre_archivo.".pdf";

	/* Se arma la query para expediente cuando es un radicado para modificar */
	if($id_expediente!=""){
		if(strpos($id_expediente,",")){
			$id_expediente1 = substr($id_expediente, 0, strpos($id_expediente, ","));
		}else{
			$id_expediente1 = $id_expediente;
		}

		$query_exp 			= "select lista_radicados, nombre_expediente from expedientes where id_expediente='$id_expediente1'";
		$fila_exp 	  		= pg_query($conectado,$query_exp);
		$linea_exp    		= pg_fetch_array($fila_exp);
		$lista_radicados 	= $linea_exp['lista_radicados']; // Listado de radicados en tabla expediente
		$nombre_expediente 	= $linea_exp['nombre_expediente']; // Listado de radicados en tabla expediente

		if(strpos($lista_radicados, $radicado)){
			$lista_radicados = $lista_radicados;		
		}else{
			$lista_radicados.="$radicado,";
		}
		
		$query_expedientes 	= "update expedientes set lista_radicados='$lista_radicados' where id_expediente='$id_expediente1';";
	}else{
		$query_expedientes 	= "";
	}
	/* Fin arma la query para expediente cuando es un radicado para modificar */
	
	/* Cuando el radicado todavía no ha sido aprobado */
	if ($aprobado != "SI"){
		/* Se determina si el que aprueba es el mismo que elabora */
		if(trim($nombre) == trim($aprueba_doc)){
			$aprobado = 'SI';
		}else{
			$aprobado = 'NO';
		}
	}

	/* Query para radicado en caso que es un radicado para modificar - Las variables $codigo_dependencia y $login las hereda desde (../../login/validar_inactividad)*/
	$dependencia_usuario 	= $codigo_dependencia;
	$login_usuario 			= $login;
	$query_radicado = "update radicado set codigo_carpeta1='$codigo_carpeta3', descripcion_anexos='$anexos', path_radicado='$path_pdf', dependencia_actual='$dependencia_usuario', usuarios_visor='$usuarios_visor', dependencia_radicador='$dependencia_usuario', usuario_radicador='$login_usuario', asunto='$asunto_doc', leido='$usuarios_visor', id_expediente='$id_expediente', codigo_serie='$codigo_serie', codigo_subserie='$codigo_subserie', folios='' where numero_radicado='$numero_radicado';";

	/* Query para actualizar los datos de la tabla datos_origen_radicado */
	$query_datos_origen_radicado = "update datos_origen_radicado set nombre_remitente_destinatario='$empresa_destinatario', dignatario='$destinatario', ubicacion='$ubicacion', direccion='$direccion', telefono='$telefono', mail='$mail' where numero_radicado='$radicado';"; 

	/* Query para actualizar la version. Se pone html_asunto en vacío para reducir el espacio en base de datos ya que es un campo grande. */	
	$query_version_documento2="update version_documentos set html_asunto='', aprobado='$aprobado' where numero_radicado='$numero_radicado' and version!='$version';";

	$inserta_ubicacion_fisica 	=  "";
	
	$transaccion_historico 		= "Modifica plantilla salida";
	$transaccion 				= "modifica_plantilla_salida"; 	// Variable para auditoria
}

/* 6. Arma la $query_version_documento y finalmente la $query_radicado_completo. Hay una variación si el $tipo_radicacion="respuesta" */
$query_version_documento = "insert into version_documentos(numero_radicado, version, usuario_modifica, fecha_modifica, path_pdf, html_asunto, despedida, con_copia_a, tratamiento, usuario_que_firma, cargo_usuario_que_firma, firmado, usuario_que_aprueba, cargo_usuario_que_aprueba, aprobado, usuario_que_elabora, cargo_usuario_que_elabora, cargo_destinatario, usuarios_revisa_aprueba, tamano, medio_solicitud_firmas)values('$radicado', '$version', '$login_usuario', '$fecha', '$path_pdf', '$pre_asunto', '$despedida_doc', '$cc_doc', '$tratamiento_doc', '$firmante_doc', '$cargo_firmante_doc', '$firmado', '$aprueba_doc', '$cargo_aprueba_doc', '$aprobado', '$elabora_doc', '$cargo_elabora_doc', '$cargo_destinatario', '$lista_firma_aprueba_revisa', '$tamano', '$tipo_solicitud');$query_version_documento2";

$query_radicado_completo = $query_expedientes.$query_radicado.$query_version_documento.$query_datos_origen_radicado.$inserta_ubicacion_fisica;

if($tipo_radicacion=="respuesta"){
	$comentario	 = "Se ha generado la <b>versión $version</b> del radicado $radicado como respuesta al radicado $numero_radicado";	// Variable para historico eventos
	$query_radicado_completo.= "insert into respuesta_radicados(radicado_padre, radicado_respuesta)values('$numero_radicado', '$radicado');"; // Se agrega el insertar en respuesta_radicados para amarrar el antecedente con el consecuente.
	$query_radicado_completo.="update radicado set dependencia_actual='$codigo_dependencia' where numero_radicado='$numero_radicado;'";// Se asigna dependencia_actual al padre (Entrada terminado en 1) para que en el reporte aparezca como responsable la dependencia del usuario que está gestionando la respuesta.
}else{
	$comentario	 = "Se ha generado la <b>versión $version</b> del radicado $radicado";	// Variable para historico eventos
}

/****************************************************************************************/
/* 7. Inicia con la generación del QR para poner en el PDF del radicado */
  	/* Genera codigo aleatorio para codigo_verificacion */
    $permitted_chars        = '2345789abcdefghjkmnpqrstuvwxyz';
    $codigo_verificacion    = substr(str_shuffle($permitted_chars), 0, 15);

/* Hereda la variable $codigo_entidad desde (../../login/validar_inactividad) */
switch ($codigo_entidad) {
    case 'EJC':
    case 'EJEC':
        $logo           = '../../imagenes/logos_entidades/imagen_qr_ejc.png'; //logo de la entidad dentro del QR
        break;

    default:
		$logo           = '../../imagenes/logo3.png'; //logo de la entidad dentro del QR
        break;
}

/* agregar el script con la librería para generar el QR */
require ('../../include/phpqrcode/qrlib.php');

/* Se crea el enlace hacia la capeta temporal con el nombre del usuario para guardar los codigos QR generados (Ej. qr_ALUMNO2.png) */
$filename   = "../../bodega_pdf/qr_usuario/qr_$usuario_radicador".".png";

/* En esta variable se genera el QR e indica cada uno de los datos que se envían a la direccion https://xxxxxx y las variables que se envían por GET */
$cod        = "https://www.gammacorp.co/consultaweb.php?numero_radicado=$radicado&codigo_verificacion=$codigo_verificacion&codigo_entidad=$codigo_entidad&canal_respuesta=mail&amp"; 

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
/* Fin de la generación del QR para poner en el radicado */
/****************************************************************************************/

/* 8. Inicio genera imagen_firma */
	$falta_firma ="";
	if($anexos!=""){
		$anexos_a = "<br><font style='font-weight:bold;'>Anexos  :</font> $anexos";
	}else{
		$anexos_a = "<br><font style='font-weight:bold;'>Anexos  :</font> Sin anexos";
	}

	if($cc_doc!=""){
		$cc_doc_a = "<br><font style='font-weight:bold;'>Con copia a:</font> $cc_doc";
	}else{
		$cc_doc_a = "";
	}

	if($aprueba_doc!=""){
        $cargo_aprueba_doc      = $_POST['cargo_aprueba_doc'];
        $aprueba_doc_a = "<br><font style='font-weight:bold;'>Aprobado por:</font> $aprueba_doc ($cargo_aprueba_doc)";
        $falta_firma  .= "<br>Falta por aprobar electrónicamente el usuario <font style='color: red; font-weight:bold;'> $aprueba_doc ($cargo_aprueba_doc)</font>";
    }else{
        $aprueba_doc_a = "";
    }

    if($revisa_doc1!=""){
        $cargo_revisa_doc1      = $_POST['cargo_revisa_doc1'];
        $revisa_doc1_a          = "<br><font style='font-weight:bold;'> Revision 1 por:</font>   $revisa_doc1 ($cargo_revisa_doc1)";
        $falta_firma  			.= "<br>Falta por revisar(1) firmando electrónicamente el usuario <font style='color: red; font-weight:bold;'> $revisa_doc1 ($cargo_revisa_doc1)</font>";
    }else{
        $revisa_doc1_a = "";
    }

    if($revisa_doc2!=""){
        $cargo_revisa_doc2      = $_POST['cargo_revisa_doc2'];
        $revisa_doc2_a 			= "<br><font style='font-weight:bold;'>Revision 2 por:</font>   $revisa_doc2 ($cargo_revisa_doc2)";
        $falta_firma  			.= "<br>Falta por revisar(2) firmando electrónicamente el usuario <font style='color: red; font-weight:bold;'> $revisa_doc2 ($cargo_revisa_doc2)</font>";
    }else{
        $revisa_doc2_a = "";
    }

    if($revisa_doc3!=""){
        $cargo_revisa_doc3      = $_POST['cargo_revisa_doc3'];
        $revisa_doc3_a 			= "<br><font style='font-weight:bold;'>Revision 3 por:</font>   $revisa_doc3 ($cargo_revisa_doc3)";
		$falta_firma  			.= "<br>Falta por revisar(3) firmando electrónicamente el usuario <font style='color: red; font-weight:bold;'> $revisa_doc3 ($cargo_revisa_doc3)</font>";
    }else{
        $revisa_doc3_a = "";
    }

    if($revisa_doc4!=""){
        $cargo_revisa_doc4      = $_POST['cargo_revisa_doc4'];
        $revisa_doc4_a 			= "<br><font style='font-weight:bold;'>Revision 4 por:</font>   $revisa_doc4 ($cargo_revisa_doc4)";
        $falta_firma  			.= "<br>Falta por revisar(4) firmando electrónicamente el usuario <font style='color: red; font-weight:bold;'> $revisa_doc4 ($cargo_revisa_doc4)</font>";

    }else{
        $revisa_doc4_a = "";
    }

    if($revisa_doc5!=""){
        $cargo_revisa_doc5      = $_POST['cargo_revisa_doc5'];
        $revisa_doc5_a 			= "<br><font style='font-weight:bold;'>Revision 5 por:</font>   $revisa_doc5 ($cargo_revisa_doc5)";
        $falta_firma  			.= "<br>Falta por revisar(5) firmando electrónicamente el usuario <font style='color: red; font-weight:bold;'> $revisa_doc5 ($cargo_revisa_doc5)</font>";
    }else{
        $revisa_doc5_a = "";
    }

    if($tipo_solicitud=="electronico"){ // Si este documento es con firma electronica
        // $login = $_SESSION['login'];
        /* Variable de sesion "$login" heredada de validar_inactividad.php que trae la imagen de la firma encriptada del usuario */
        $query_firma_encriptada         = "select path_firma from usuarios where login ='$login'";
        $result_query_firma_encriptada  = pg_query($conectado,$query_firma_encriptada);
        $linea_query_firma_encriptada   = pg_fetch_array($result_query_firma_encriptada);    
        $path_firma                     = $linea_query_firma_encriptada['path_firma'];

        if(isset($path_firma)){
            /* Numero de radicado para marca de agua en la firma */

            $proporciones_imagen    = getimagesize(trim($path_firma)); /* Obtencion de las proporciones de la imagen_firma */    
            $ancho_imagen_firma     = $proporciones_imagen[0];                      /* Proporcion en ancho_imagen_firma */ 
            $alto_imagen_firma      = $proporciones_imagen[1];                      /* Proporcion en alto_imagen_firma */ 

            $cuadro_fondo_blanco_peq    = imagecreatetruecolor(142, 135); // Crear cuadro de 142x135
            $white                      = imagecolorallocate($cuadro_fondo_blanco_peq, 255, 255, 255); // Poner el fondo del cuadro blanco
            imagefill($cuadro_fondo_blanco_peq, 0, 0, $white);

            /* Escribe en el cuadro_fondo_blanco_peq arriba, centro y debajo el numero de radicado */
            imagestring($cuadro_fondo_blanco_peq, 3, 5, 5, $radicado, 0x0000FF); // imagestring (imagen,fuente(del 1 al 5), posicionX, posicionY, Texto, int $color )
            imagestring($cuadro_fondo_blanco_peq, 3, 5, 55, $radicado, 0x0000FF); // imagestring (imagen,fuente(del 1 al 5), posicionX, posicionY, Texto, int $color )
            imagestring($cuadro_fondo_blanco_peq, 3, 5, 115, $radicado, 0x0000FF); // imagestring (imagen,fuente(del 1 al 5), posicionX, posicionY, Texto, int $color )

            $cuadro_fondo_blanco_grande     = imagecreatetruecolor(284, 265);     /* Crea una nueva imagen en blanco de 284 x 265 */
            imagecopyresized($cuadro_fondo_blanco_grande, $cuadro_fondo_blanco_peq, 0, 0, 0, 0, 284, 265, 142, 135); /* Modifica la imagen cargada y la agrega al $cuadro_fondo_blanco_grande */

            $imagen_firma_marca_agua    = imagecreatetruecolor(284, 265);     /* Crea una nueva imagen en blanco de 284 x 265 */
            $imagen_firma_original      = imagecreatefrompng(trim($path_firma));
             
            imagecopyresized($imagen_firma_marca_agua, $imagen_firma_original, 0, 0, 0, 0, 284, 265, $ancho_imagen_firma, $alto_imagen_firma); /* Modifica la imagen cargada y la agrega a la $imagen_firma_marca_agua */

            /* Fusiona la $imagen_firma_marca_agua y el $cuadro_fondo_blanco_grande con transparencia de 50 */
            imagecopymerge($imagen_firma_marca_agua, $cuadro_fondo_blanco_grande, 0, 0, 0, 0, imagesx($imagen_firma_marca_agua), imagesy($imagen_firma_marca_agua), 25);

            // Imprimir y liberar memoria
            header('Content-type: image/png');
            /* Guarda la imagen fusionada en la carpeta con el nombre */
            imagepng($imagen_firma_marca_agua, "../../bodega_pdf/qr_usuario/firma_fusionada_$login".".png");
            imagedestroy($imagen_firma_marca_agua);

            $sello_calidad_firma  = "../../bodega_pdf/qr_usuario/firma_fusionada_$login".".png";
        }else{
            $sello_calidad_firma  = 'data:image/png;base64,'.base64_encode(file_get_contents('../../imagenes/iconos/sello_calidad_firma.png'));
        }

        if(($login_firmante==$login_elabora) and $falta_firma==""){
        	/* En este caso quien firma es el mismo que elabora por lo que no requiere mas proceso. */
	        $imagen_firma = "
	        <table border='0' style='border: 1px solid #ddd; border-collapse: collapse; font-size:11px; width: 650px;'>
	            <tr>
	                <td rowspan='5'>
	                    <center>
	                        <img width='100px' height='100px' class='center' src='$sello_calidad_firma'>
	                    </center>    
	                </td>
	                <td style='text-align:left; width:350px;' colspan='2'>
	                    Documento generado en $fecha
	                </td>
	                <td rowspan='5' style='font-size:8px; text-align: center; font-weight: bold;'>
	                    <img src='$filename' width='80px' height='80px'><br>$radicado
	                </td>
	            </tr>
	            <tr> 
	                <td style='font-weight: bold; width:150px;'> 
	                    Firmado electrónicamente por: 
	                </td>
	                <td style='width:100px;' >
	                    $firmante_doc
	                </td>  
	            </tr>
	            <tr>
	                <td style='font-weight: bold;'> 
	                    Cargo: 
	                </td>
	                <td>
	                    $cargo_firmante_doc
	                </td>
	            </tr>
	            <tr>
	                <td style='font-weight: bold;'> 
	                    Entidad: 
	                </td>
	                <td>
	                    $entidad                    
	                </td>
	            </tr>
	            <tr>
	                <td style='font-weight: bold;'> 
	                    Código de verificación: 
	                </td>
	                <td>
	                    <b>$codigo_verificacion</b>                    
	                </td>
	            </tr>
	            <tr>
	                <td style='text-align:center;' colspan='4'> 
	                    <hr>
	                    Este documento fue generado con firma electrónica y cuenta con plena validez jurídica, conforme a lo dispuesto en la Ley 527/99 y el decreto reglamentario 2364/12 <br>
	                    <hr>
	                </td>
	            </tr>
	        </table>";
        }else{
        	if($login_firmante==$login_elabora){
        		$contenido_firmante = "El usuario <font style='color: green; font-weight:bold;'> $firmante_doc ($cargo_firmante_doc)</font> ha firmado electrónicamente el documento pero";
        	}else{
        		$contenido_firmante = "Falta por firmar electrónicamente el usuario <font style='color: red; font-weight:bold;'> $firmante_doc ($cargo_firmante_doc)</font>";
        	}

        	/* Espacio para firmas en color azul que se muestra para reemplazar */
    		$imagen_firma = "<div id='espacio_firmas' style='background-color:#2f8df5; border: 1px solid #ddd; height:170px; font-size:11px; text-align:center; width: 650px;'><h2>Espacio para firmas</h2>$contenido_firmante $falta_firma</div></div>";
        }
    }else{ // Si el documento se imprime
        $imagen_firma = "
            <table border='0' style='font-size:12px; margin-left:-5px; width: 650px;'>
                <tr>
                    <td style='font-size: 14px;font-weight: bold; text-align:left; width:500px;'>
                    	$despedida_doc
                    </td>
                </tr>
                <tr>
                    <td style='font-size: 14px;font-weight: bold; text-align:left; width:500px;'>
                    	<br><br>
                        $firmante_doc
                        <br>
                        <span style='margin_top:-25px;'>$cargo_firmante_doc</span>
                    </td>
                   	<td rowspan='2' style='font-size:8px; text-align: center; font-weight: bold;'>
	                    <img src='$filename' width='80px' height='80px'><br>$radicado
	                </td>
                </tr>
               	<tr>
               		<td style='font-size:8px; padding_top:10px; width:100px;'>
               			<font style='font-weight:bold;'>Elaborado por: </font>$elabora_doc ($cargo_elabora_doc) $aprueba_doc_a $revisa_doc1_a $revisa_doc2_a $revisa_doc3_a $revisa_doc4_a $revisa_doc5_a $anexos_a $cc_doc_a
               		</td>
               	</tr>
            </table>";

        // $inserta_ubicacion_fisica = "insert into ubicacion_fisica (numero_radicado, usuario_actual, usuario_anterior, fecha)values('$radicado', '$login_elabora', 'Imprime documento físico para recolectar firmas','$hora');";

        // $aprobado = 'SI'; // Se define la variable para que no aparezca pendiente por aprobar ya que es impreso en físico en la tabla version_documentos
    }
/* Fin genera imagen_firma */
/****************************************************************************************/

/* 9. Estas variables entre asteriscos deben definirse en template_final_resoluciones y genera PDF */
$html = str_replace("*NUMERO_RADICADO_FINAL*", $radicado, $html); // Pone el numero de radicado en el encabezado del documento
$html = str_replace("*IMAGEN_FIRMA*", $imagen_firma, $html); // Pone el numero de radicado en el encabezado del documento

$dompdf = new Dompdf\Dompdf();
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
    "../../bodega_pdf/radicados/$path_pdf",
    $dompdf->output()
);

chmod("../../bodega_pdf/radicados/$path_pdf",0777);

if(file_exists("../../bodega_pdf/plantilla_generada_tmp/$aleatorio_recibido.pdf")){
	unlink("../../bodega_pdf/plantilla_generada_tmp/$aleatorio_recibido.pdf");
}
/* 10. Ejecuta la $query_radicado_completo e inserta historicos. */
// echo "$query_radicado_completo";
if(pg_query($conectado,$query_radicado_completo)){		
	/* Desde aqui se genera historico */	
		$creado 	= "$radicado";	// Variable para auditoria

		require_once("../../login/inserta_historico.php");		
	/* Hasta aqui se genera historico */	
}else{
	echo "<script> alert('Ocurrió un error al generar el radicado, por favor contactar con el administrador del sistema.')</script>";
}

?>