<?php 
	if(!isset($_SESSION)){
		session_start();
		if(isset($_SESSION['login'])){
			$usuario 		 = $_SESSION['login'];
			$nombre_completo = $_SESSION['nombre'];
		}else{
			$usuario 		 = "";
			$nombre_completo = "";			
		}
	}else{
		echo "no hay sesion";
	}
	require_once('conexion3.php');
//	var_dump($_SESSION);

/* Genera ID **********************************************************************************/
	$query_max_log="select max(id) from auditoria_jonas";

	$fila_log 	= pg_query($conectado_log,$query_max_log);
	$linea_log 	= pg_fetch_array($fila_log);

	$max_log 	= $linea_log[0];
	$max_log2 	= $max_log+1;

/* Fin Genera ID ******************************************************************************/

/* Genera Usuario y transaccion ***************************************************************/
	$transaccion 	 = $_POST['transaccion'];
	
	if(isset($_POST['creado'])){
		$creado = $_POST['creado'];
	}else{
		$creado = "";
	}

	switch ($transaccion) {
		case 'agregar_expediente_de_caja':
			$guarda_transaccion = "$nombre_completo ha incluido en la caja $creado";
			break;												
		case 'agregar_nuevo_contacto':
			$guarda_transaccion = "$nombre_completo ha creado el contacto $creado";
			break;	
		case 'agregar_radicado_exp':
			$guarda_transaccion = "$nombre_completo ha agregado el radicado $creado";
			break;												
		case 'anexar_archivo':
			$guarda_transaccion = "$nombre_completo ha adjuntado archivo al radicado $creado";
			break;												
		case 'aprobar_radicado':
			$guarda_transaccion = "$nombre_completo ha aprobado el radicado $creado";
			break;												
		case 'archivar_radicado':
			$guarda_transaccion = "$nombre_completo ha movido el radicado $creado a carpeta personal";
			break;												
		case 'cambio_pass':
			$guarda_transaccion = "Se ha cambiado el password del usuario $nombre_completo.";
			$_SESSION['usuario_nuevo']='NO';
			break;
		case 'cancelar_solicitud_prestamo':
			$guarda_transaccion = "$nombre_completo ha cancelado la solicitud de préstamo asociada al radicado $creado";
			break;
		case 'cerrar_sesion':
			$guarda_transaccion = "$nombre_completo ha salido del sistema.";
			break;	
		case 'cerrar_sesion_inactividad':
			$guarda_transaccion = "Se ha cerrado sesion del usuario por timeout";
			break;	
		case 'crear_carpeta_personal':
			$guarda_transaccion = "$nombre_completo ha creado la carpeta personal $creado";
			break;	
		case 'crear_dependencia':
			$guarda_transaccion = "$nombre_completo ha creado la dependencia $creado";
			break;	
		case 'crear_expediente':
			$guarda_transaccion = "$nombre_completo ha creado el expediente $creado";
			break;										
		case 'crear_municipio':
			$guarda_transaccion = "$nombre_completo ha creado el municipio $creado";
			break;	
		case 'crear_nivel':
			$guarda_transaccion = "$nombre_completo ha creado el nivel $creado";
			break;
		case 'crear_secuencia':
			$guarda_transaccion = "$nombre_completo ha creado la secuencia $creado";
			break;
		case 'crear_serie':
			$guarda_transaccion = "$nombre_completo ha creado la serie $creado";
			break;
		case 'crear_subserie':
			$guarda_transaccion = "$nombre_completo ha creado la subserie $creado";
			break;
		case 'crear_tipo_documento':
			$guarda_transaccion = "$nombre_completo ha creado el Tipo de Documento $creado";
			break;	
		case 'crear_tipo_documento_pqr':
			$guarda_transaccion = "$nombre_completo ha creado el Tipo de Documento PQR $creado";
			break;	
		case 'crear_tipo_radicado':
			$guarda_transaccion = "$nombre_completo ha creado el Tipo de Radicado $creado";
			break;	
		case 'crear_usuario':
			$guarda_transaccion = "$nombre_completo ha creado al usuario nuevo $creado";
			break;	
		case 'derivar_radicado':
			$guarda_transaccion = "$nombre_completo ha derivado a otros usuarios el radicado $creado";
			break;	
		case 'devolucion_prestamo_fisico':
			$guarda_transaccion = "$nombre_completo ha recibido devolución del documento en físico del radicado $creado";	
			break;	
		case 'documento_no_requiere_respuesta':
			$guarda_transaccion = "$nombre_completo ha marcado como NRR (No requiere respuesta) el radicado $creado";	
			break;	
		case 'generar_planilla_documentos_fisicos':
			$guarda_transaccion = "$nombre_completo ha verificado mediante firma electronica que ha generado listado para entrega de los radicados ($creado) en físico";
			break;
		case 'gestionar_datos_usuario':
			$guarda_transaccion = "$nombre_completo ha cambiado datos desde el modulo de gestionar datos usuarios";	
			break;	
		case 'informar_radicado':
			$guarda_transaccion = "$nombre_completo ha informado otros usuarios el radicado $creado";
			break;	
		case 'login':
			$guarda_transaccion = "$nombre_completo ha ingresado a Jonas.";
			break;
		case 'imagen_principal':
			$guarda_transaccion = "$nombre_completo ha asociado la imagen principal del radicado $creado.";
			break;
		case 'incluir_radicado_en_expediente':
			$guarda_transaccion = "$nombre_completo ha incluido en expediente el radicado $creado.";
			break;
		case 'ingresa_cambio_organico_funcional':
			$guarda_transaccion = "$nombre_completo ha creado la versión $creado del cambio organico-funcional (Organigrama).";
			break;
		case 'insertar_metadato':
			$guarda_transaccion = "$nombre_completo ha insertado el metadato asociado a la serie-subserie $creado.";
			break;
		case 'insertar_trd_exp_radicado':
			$guarda_transaccion = "$nombre_completo ha asignado TRD y EXPEDIENTE al radicado $creado.";
			break;
		case 'insertar_trd_radicado':
			$guarda_transaccion = "$nombre_completo ha asignado TRD al radicado $creado.";
			break;
		case 'inventario_individual':
			$guarda_transaccion = "$nombre_completo ha ingresado a inventario el documento $creado.";
			break;
		case 'masiva_inventario':
			$guarda_transaccion = "$nombre_completo ha realizado cargue masivo de inventario";
			break;	
		case 'modifica_cambio_organico_funcional':
			$guarda_transaccion = "$nombre_completo ha modificado la versión $creado del cambio organico-funcional (Organigrama).";
			break;
		case 'modifica_plantilla_salida_expediente':
		case 'modifica_plantilla_resoluciones_expediente':
			$guarda_transaccion = "$nombre_completo ha modificado el radicado $creado y ha asignado expediente.";
			break;	
		case 'modificacion_inventario':
		case 'modificacion_radicado':
			$guarda_transaccion = "$nombre_completo ha modificado el radicado $creado";
				break;	
		case 'modificacion_radicado_mas_imagen':
			$guarda_transaccion = "$nombre_completo ha modificado y cargado la imagen pdf del radicado $creado";
			break;		
		case 'modifica_plantilla_resoluciones':
		case 'modifica_plantilla_salida':
		case 'modificacion_rapida':
			$guarda_transaccion = "$nombre_completo ha modificado el radicado $creado";
			break;	
		case 'modificacion_rapida_mas_imagen':
			$guarda_transaccion = "$nombre_completo ha modificado y cargado la imagen pdf del radicado $creado";
			break;
		case 'modificar_carpeta_personal':
			$guarda_transaccion = "$nombre_completo ha modificado la carpeta personal $creado";
			break;												
		case 'modificar_consecutivo':
			$guarda_transaccion = "$nombre_completo ha modificado el consecutivo de la dependencia $creado";
			break;	
		case 'modificar_dependencia':
			$guarda_transaccion = "$nombre_completo ha modificado la dependencia $creado";
			break;	
		case 'modificar_expediente':
			$guarda_transaccion = "$nombre_completo ha modificado el expediente $creado";
			break;	
		case 'modificar_municipio':
			$guarda_transaccion = "$nombre_completo ha modificado el municipio $creado";
			break;	
		case 'modificar_nivel':
			$guarda_transaccion = "$nombre_completo ha modificado el nivel $creado";
			break;	
		case 'modificar_serie':
			$guarda_transaccion = "$nombre_completo ha modificado la serie $creado";
			break;										
		case 'modificar_subserie':
			$guarda_transaccion = "$nombre_completo ha modificado la subserie $creado";
			break;										
		case 'modificar_tipo_documento':
			$guarda_transaccion = "$nombre_completo ha modificado el Tipo de Documento $creado";
			break;		
		case 'modificar_tipo_documento_pqr':
			$guarda_transaccion = "$nombre_completo ha modificado el Tipo de Documento PQR $creado";
			break;	
		case 'modificar_usuario':
			$guarda_transaccion = "$nombre_completo ha modificado al usuario $creado";
			break;
		case 'mover_expediente_de_caja':
			$guarda_transaccion = "$nombre_completo ha movido a la caja $creado";
			break;												
		case 'mover_radicado_exp':
			$guarda_transaccion = "$nombre_completo ha movido el radicado $creado";
			break;
		case 'plantilla_interna':
			$guarda_transaccion = "$nombre_completo ha generado la plantilla de radicacion interna radicado $creado";
			break;	
		case 'plantilla_resoluciones':
			$guarda_transaccion = "$nombre_completo ha generado la plantilla de resoluciones del radicado $creado";
			break;												
		case 'plantilla_resoluciones_expediente':
			$guarda_transaccion = "$nombre_completo ha modificado la plantilla de resoluciones del radicado $creado";
			break;												
		case 'plantilla_respuesta':
			$guarda_transaccion = "$nombre_completo ha generado la plantilla para responder el radicado $creado";
			break;												
		case 'plantilla_salida':
			$guarda_transaccion = "$nombre_completo ha generado la plantilla de salida del radicado $creado";
			break;												
		case 'plantilla_salida_expediente':
			$guarda_transaccion = "$nombre_completo ha generado la plantilla de salida del radicado $creado y ha asociado al tiempo a expediente";
			break;												
		case 'plantilla_salida_respuesta':
			$guarda_transaccion = "$nombre_completo ha generado la plantilla para responder el radicado $creado";
			break;												
		case 'prestamo_documento':
			$guarda_transaccion = "$creado";
			break;
		case 'radicacion_correo_electronico':
			$guarda_transaccion = "$nombre_completo ha generado el radicado desde el modulo del correo electronico $creado";
			break;											
		case 'radicacion_entrada':
		case 'radicacion_rapida':
			$guarda_transaccion = "$nombre_completo ha generado el radicado de entrada $creado";
			break;	
		case 'radicacion_normal':
			$guarda_transaccion = "$nombre_completo ha generado el radicado interno normal $creado";
			break;	
		case 'radicado_leido':
			$guarda_transaccion = "$nombre_completo ha marcado como leido el radicado $creado";
			break;
		case 'reasignar_radicado':
			$guarda_transaccion = "$nombre_completo ha reasignado el radicado $creado";
			break;
		case 'sacar_expediente_de_caja':
			$guarda_transaccion = "$nombre_completo ha sacado de la caja $creado";
			break;												
		case 'sacar_radicado_expediente':
			$guarda_transaccion = "$nombre_completo ha sacado del expediente $creado";
			break;												
		case 'salida_respuesta':
			$guarda_transaccion = "$nombre_completo ha respondido correctamente el radicado $creado";
			break;												
		case 'solicitud_prestamo_documento':
			$guarda_transaccion = "$nombre_completo ha solicitado el prestamo del $creado";
			break;												
		case 'sticker_entrada':
			$guarda_transaccion = "$nombre_completo ha generado sticker del radicado $creado";
			break;
		case 'subir_pdf_principal':
			$guarda_transaccion = "$nombre_completo ha subido PDF principal con firmas correspondientes del radicado $creado";
			break;
		case 'recibir_listado_documentos_fisicos':
			$guarda_transaccion = "$nombre_completo ha verificado mediante firma electronica que ha recibido los radicados ($creado) en físico";		
	}
/* Fin genera Usuario y transaccion **********************************************************/	
/* Genera la fecha de transaccion ************************************************************/
	$timestamp = date('Y-m-d H:i:s');
/* Fin genera la fecha de transaccion ********************************************************/

/* Genera la IP ******************************************************************************/
/* Funcion que genera la ip del cliente para guardar cada vez que se realiza una transaccion */
    function getRealIP(){ 
        if (isset($_SERVER["HTTP_CLIENT_IP"])){

            return $_SERVER["HTTP_CLIENT_IP"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){

            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){

            return $_SERVER["HTTP_X_FORWARDED"];

        }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){

            return $_SERVER["HTTP_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_FORWARDED"])){

            return $_SERVER["HTTP_FORWARDED"];

        }else{
            return $_SERVER["REMOTE_ADDR"];
        }
    }
    $ip_equipo = getRealIP();
       
/* Genera la IP **********************************************************************************/
/* Fin funcion que genera la ip del cliente para guardar cada vez que se realiza una transaccion */
/* Funcion para verificar navegador***************************************************************/
$user_agent = $_SERVER['HTTP_USER_AGENT'];
function getBrowser($user_agent){
	if(strpos($user_agent, 'MSIE') !== FALSE)
	   return 'Internet explorer';
	 elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
	   return 'Microsoft Edge';
	 elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
	    return 'Internet explorer';
	 elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
	   return "Opera Mini";
	 elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
	   return "Opera";
	 elseif(strpos($user_agent, 'Firefox') !== FALSE)
	   return 'Mozilla Firefox';
	 elseif(strpos($user_agent, 'Chrome') !== FALSE)
	   return 'Google Chrome';
	 elseif(strpos($user_agent, 'Safari') !== FALSE)
	   return "Safari";
	 else
	   return 'No hemos podido detectar su navegador';
}
$navegador = getBrowser($user_agent);
 
//echo "El navegador con el que estás visitando esta web es: ".$navegador;
/* Fin funcion para verificar navegador***********************************************************/
	$query_log="insert into auditoria_jonas (id,usuario,fecha,transaccion,ip,tipo_transaccion,navegador) values ($max_log2,'$usuario','$timestamp','$guarda_transaccion','$ip_equipo','$transaccion','$navegador')";

	if(pg_query($conectado_log,$query_log)){
		echo "true";
	}else{
		echo "No se ha podido generar auditoria sobre la transaccion realizada. Por favor comuníquese con el administrador.";
	}
 ?>