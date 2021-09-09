<?php 
if(!isset($_SESSION)){
	session_start();
}
if(isset($_SESSION["ultimo_ingreso"])){
    $fecha_antigua 	= $_SESSION["ultimo_ingreso"];
	$nuevo 			= trim($_SESSION['usuario_nuevo']);
}else{
    $fecha_antigua 	= 0;
	$nuevo 			= "";
}
	// var_dump($_SESSION);
include ("conexion2.php");

if($nuevo=="SI"){
	echo "
	<script language=javascript>
		Swal.fire({		
				position 			: 'top-end',
		    	showConfirmButton 	: false,
		    	timer 				: 1500,
			    title 				: 'Por seguridad de su cuenta, Se requiere cambio de contraseña.',
			    text 				: '',
			    type 				:'warning'
			}).then(function(isConfirm){
				$('#contenido').load('login/cambio_contrasena.php');
			});
	</script>";
}

$hora 		= date("Y-n-j H:i:s");

$tiempo 	= (strtotime($hora)-strtotime($fecha_antigua));
$operacion 	= 60*30;/* Este es el tiempo para que la sesion se inactive automaticamente. Lo cambio de 15 a 30 para desarrollar
	Es decir, 60 (segundos) por la cantidad de minutos que quiera la inactividad */

if($tiempo>=$operacion){
?>
    <link rel="shortcut icon" href="imagenes/logo3.png">
	<script language=javascript>
		$.ajax({	// Guardo registro de ingreso al sistema para auditoria
			type: 'POST',
			url : 'login/transacciones.php',
			data: {
				'transaccion' : 'cerrar_sesion_inactividad'	
			},			
			success: function(resp1){
				if(resp1=="true"){
					Swal.fire({		
						position 			: 'top-end',
						showConfirmButton 	: false,
						timer 				: 2000,
						title 				: 'Por su seguridad, su sesión ha sido caducada',
						text 				: 'Por favor ingrese nuevamente',
						type 				: 'error'
					}).then(function(isConfirm){
						self.location="index.php";
					})
				}else{
					alert(resp1)
				}
			}
		})	
	</script>';
<?php	
	exit(); // Corta toda la ejecución del codigo PHP
}else{

	$administrador_sistema              = trim($_SESSION['administrador_sistema']);
	$caracteres_depend                  = trim($_SESSION['caracteres_depend']);
	$codigo_entidad 					= trim($_SESSION['codigo_entidad']);
	$creacion_expedientes               = trim($_SESSION['creacion_expedientes']);
	$cuadro_clasificacion               = trim($_SESSION['cuadro_clasificacion']);
	$entidad 							= trim($_SESSION['entidad']);
	$fecha_especial 					= trim($_SESSION['fechas_especiales']);
	$imagen                             = trim($_SESSION['imagen']);
	$inventario                         = trim($_SESSION['inventario']);
	$jefe_dependencia                   = trim($_SESSION['jefe_dependencia']);
	$login 								= trim($_SESSION['login']);
	$modificar_radicado                 = trim($_SESSION['modificar_radicado']);
	$nombre 							= trim($_SESSION['nombre']);
	$nombre_completo                    = trim($_SESSION['nombre']);
	$perfil                             = trim($_SESSION['perfil']);
	$prestamo_documentos                = trim($_SESSION['prestamo_documentos']);
	$radicacion_interna                 = trim($_SESSION['radicacion_interna']);
	$radicacion_normal                  = trim($_SESSION['radicacion_normal']);
	$radicacion_resoluciones            = trim($_SESSION['radicacion_resoluciones']);
	$radicacion_salida                  = trim($_SESSION['radicacion_salida']);
	$scanner                            = trim($_SESSION['scanner']);
	$ubicacion_topografica              = trim($_SESSION['ubicacion_topografica']);
	$usuario                            = trim($_SESSION['login']);
	$ventanilla_radicacion              = trim($_SESSION['ventanilla_radicacion']);
	$version_jonas 						= trim($_SESSION['version_jonas']);
    // $administrador_metadatos                = trim($_SESSION['administrador_metadatos']);

	switch ($fecha_especial) {
		case 'licencia':	// Se requiere que se defina en este archivo la fecha límite de la licencia en la variable $fecha_entrada y el campo con "soporte desde el xxx" y en el archivo include/fechas_especiales.php igualmente.
			$fecha_actual  = strtotime(date("d-m-Y H:i:00",time()));
			$fecha_entrada = strtotime("15-02-2019 00:00:00");

			$fecha_diff= round(((($fecha_entrada-$fecha_actual)/60)/60)/24);
			$alerta_licencia="<div style='background: red; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.3); 	box-shadow: 0 0 10px rgba(0,0,0,0.3); color: #FFFFFF; font-size: 15px; padding: 5px;position: relative; text-align: center;'>Su entidad no tiene soporte desde el 11 de Noviembre. Si no renueva soporte, será inactivada dentro de $fecha_diff días. <br>Comuníquese por favor urgente con Gammacorp SAS.</div>";
			break;
		
		default:
			$alerta_licencia="";
			break;
	}	
	// Hasta aqui se define la alerta por la licencia

	$_SESSION['ultimo_ingreso'] 	= $hora;

/* Desde aqui uso variables para despliegue de contadores (alertas de pdf pendientes por cargar por ejemplo) */   
	$cont_alertas 		= 0; // Inicio contador de alertas
	$mostrar_alertas 	= ""; // Inicio contador de alertas

/* Desde aqui consulto contador de bandeja de entrada principal */
	$query_bandeja_entrada 		= "select count(*) from radicado where codigo_carpeta1->'$login'->>'codigo_carpeta_personal'='entrada' and asunto IS NOT NULL";

	$result_query_bandeja_entrada = pg_query($conectado,$query_bandeja_entrada);
	if($result_query_bandeja_entrada==false){
        $bandeja_entrada='0';
    }else{
        $linea_band_entrada = pg_fetch_array($result_query_bandeja_entrada);    
        $bandeja_entrada 	= $linea_band_entrada['count'];
    }
    $_SESSION['bandeja_entrada_leido']= $bandeja_entrada;   
/* Hasta aqui consulto contador de bandeja de entrada principal */

/* Desde aqui consulto contador de no leidos en bandeja de entrada */
	$query_bandeja_entrada_leidos 		= "select count(*) from radicado where codigo_carpeta1->'$login'->>'codigo_carpeta_personal'='entrada' and asunto IS NOT NULL and leido ilike '%$login%' ";

	$result_query_bandeja_entrada_leidos = pg_query($conectado,$query_bandeja_entrada_leidos);
	if($result_query_bandeja_entrada_leidos==false){
        $bandeja_entrada_leidos 	= '0';
    }else{
        $linea_band_entrada = pg_fetch_array($result_query_bandeja_entrada_leidos);    
        $bandeja_entrada_leidos 	= $linea_band_entrada['count'];
    }
    $_SESSION['bandeja_entrada_leidos']= $bandeja_entrada_leidos;

    if($bandeja_entrada_leidos!=0){
	    $icono_no_leido_be			= "entrada";
		$funcion_no_leido_be 		= "carga_bandeja_entrada('entrada','general')";
		$modulo_no_leido_be 		= "Pendientes Bandeja Entrada";
		$comentario_no_leido_be 	= "Documentos en Bandeja de entrada que usted no ha leido.";

	    $mostrar_alertas.= "<li onclick =\"carga_bandeja_entrada()\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_no_leido_be.png\" style=\"width:18px;\"> Tiene <font color=red> $bandeja_entrada_leidos </font>$comentario_no_leido_be <br><b>($modulo_no_leido_be)</b></span></a></li>";
    }
    $cont_alertas+=$bandeja_entrada_leidos;
/* Hasta aqui consulto contador de no leidos en bandeja de entrada */
/* Desde aqui consulto contador de pendientes por responder del usuario  */
    $query_bandeja_entrada2 	= "select count(*) from radicado where asunto IS NOT NULL and usuarios_control ilike '%$login%' and estado_radicado='en_tramite'";
   
    $result_query_bandeja_entrada2 = pg_query($conectado,$query_bandeja_entrada2);
	if($result_query_bandeja_entrada2==false){
        $pendientes_responder='0';
    }else{
        $linea_band_entrada2 	= pg_fetch_array($result_query_bandeja_entrada2);    
        $pendientes_responder 	= $linea_band_entrada2['count'];
    }
    $_SESSION['pendientes_responder']= $pendientes_responder;
    
    if($pendientes_responder!=0){
		$funcion_pendiente_resp 	= "carga_radicados_pendientes()";
		$comentario_pendiente_resp 	= "Documentos pendientes en su bandeja que usted debe responder o reasignar.";
		$modulo_pendiente_resp 		= "Pendientes Bandeja Entrada";
    	$icono_pendiente_resp		= "entrada";

	    $mostrar_alertas.= "<li onclick =\"$funcion_pendiente_resp\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_pendiente_resp.png\" style=\"width:18px;\"> Tiene <font color=red> $pendientes_responder </font>$comentario_pendiente_resp <br><b>($modulo_pendiente_resp)</b></span></a></li>";
    }
    $cont_alertas+=$pendientes_responder;

/* Hasta aqui consulto contador de pendientes por responder del usuario */

/* Desde aqui consulto contador de bandeja de salida */
	$query_bandeja_salida 		= "select count(*) from radicado where codigo_carpeta1->'$login'->>'codigo_carpeta_personal'='Salida' and leido ilike '%$login%'";
    $result_query_bandeja_salida = pg_query($conectado,$query_bandeja_salida);
    if($result_query_bandeja_salida==false){
        $bandeja_salida='0';
    }else{
        $linea_band_salida 	= pg_fetch_array($result_query_bandeja_salida);    
        $bandeja_salida 	= $linea_band_salida['count'];
    }
    $_SESSION['bandeja_salida'] = $bandeja_salida;
/* Hasta aqui consulto contador de bandeja de salida */

/* Desde aqui consulto join usuario - dependencias (Nombre de dependencia)*/ 
	$query_nombre_dependencia = "select * from usuarios u join dependencias d on u.codigo_dependencia = d.codigo_dependencia where login = '$login'";
	$result_nombre_dependencia 		= pg_query($conectado,$query_nombre_dependencia);
	$linea_nombre_dependencia  		= pg_fetch_array($result_nombre_dependencia);
	$nombre_dependencia 			= $linea_nombre_dependencia['nombre_dependencia'];
	$codigo_dependencia 			= $linea_nombre_dependencia['codigo_dependencia'];
	$cargo_usuario 					= $linea_nombre_dependencia['cargo_usuario'];

	$_SESSION['nombre_dependencia'] = $nombre_dependencia;

/* Validaciones que agregan a $cont_alertas +1*/
    if ($cargo_usuario==''){ // Si no se ha llenado el campo de cargo_usuario
        $cont_alertas+=1;
        $comentario_gdu = "Falta que asigne el cargo de su usuario";
        $funcion_gdu 	= "gestionar_datos_usuario()";
        $icono_gdu 		= "icono_user";
        $modulo_gdu 	= "Modulo Datos Usuario";

        $mostrar_alertas.= "<li onclick =\"$funcion_gdu\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_gdu.png\" style=\"width:18px;\"> <font color=\"red\"> $comentario_gdu<b> ($modulo_gdu)</b></font></span></a></li>";
    }  

	$_SESSION['cargo_usuario'] = $cargo_usuario;
/* Hasta aqui consulto join usuario - dependencias */ 

/* Desde aqui consulto los pasos en los cuales hay pendientes en la parametrización o configuración del sistema (Modulo Auditoría del Sistema*/
    if($administrador_sistema=="SI"){
    	$cantidad_pasos_por_cumplir 		= 0;
	    $pasos_completados_configuracion 	= 0;
	    $pasos_faltantes_configuracion 		= 0;

	 	/* PASO1 Se consulta si existen los cambios_organico_funcionales */
		$cantidad_pasos_por_cumplir++;

		$query_consulta_cambios_organico_funcionales 	 = "select * from cambios_organico_funcionales order by fecha_inicial_cambio";
		$fila_consulta_cambios_organico_funcionales 	 = pg_query($conectado,$query_consulta_cambios_organico_funcionales);
		$registros_consulta_cambios_organico_funcionales = pg_num_rows($fila_consulta_cambios_organico_funcionales);

		if($registros_consulta_cambios_organico_funcionales == 0){
			$pasos_faltantes_configuracion++;
		}else{
			$pasos_completados_configuracion++;
		}	

	 	/* PASO2 Se consulta si existe la ultima versión de cambios_organico_funcionales */
		$cantidad_pasos_por_cumplir++;

		$query_version_actual_cambios_of = "select count(*) from cambios_organico_funcionales where fecha_final_cambio=''";
		$fila_version_actual_cambios_of  = pg_query($conectado,$query_version_actual_cambios_of);
		$linea_version_actual_cambios_of = pg_fetch_array($fila_version_actual_cambios_of);
		$count_version_actual_cambios_of = $linea_version_actual_cambios_of['count'];

		if($count_version_actual_cambios_of==0){
			$pasos_faltantes_configuracion++;
		}else{
			$pasos_completados_configuracion++;
		}

	 	/* PASO3 Se consulta si existe espacio entre las fechas de la versión de cambios_organico_funcionales utilizando los registros obtenidos en el PASO1 */
	 	$contenido_paso3 	= "";
	 	$contenido_paso4 	= "";

	 	/* Listado utilizado en admin_depe/index_dependencias.php */
	 	$listado_cambios_organico_funcionales = "";

	 	if($registros_consulta_cambios_organico_funcionales != 0){
	 		$fecha_ant 			= "";
	 		for ($i=0; $i < $registros_consulta_cambios_organico_funcionales; $i++) { 
	 			$linea_consulta_cof = pg_fetch_array($fila_consulta_cambios_organico_funcionales);

	 			$id_cambio 		 	= $linea_consulta_cof['id_cambio_organico_funcional'];
	 			$fecha_ini_cambio 	= $linea_consulta_cof['fecha_inicial_cambio'];
	 			$fecha_fin_cambio 	= $linea_consulta_cof['fecha_final_cambio'];
	 			$path_acto_admin 	= $linea_consulta_cof['path_acto_administrativo'];

	 			if($fecha_ant!=""){
	 				/* Da formato a fecha y suma un dia para comparar si hay intervalo de tiempo pendiente por cambio organico-funcional */
 					$fecha_ant2 	= date_create($fecha_ant);
 					date_add($fecha_ant2, date_interval_create_from_date_string('1 day'));
					$fecha_ant1 	= date_format($fecha_ant2, 'Y-m-d');;

	 				if($fecha_ant1!=$fecha_ini_cambio){
	 					$pasos_faltantes_configuracion++;

	 					$contenido_paso3.= "<img src='imagenes/iconos/checkbox3.png' style='float: left; height:25px; margin-right: 10px;'>Falta estructura Organico Funcional en el rango de fechas <font color='red'>($fecha_ant)</font> hasta <font color='red'>($fecha_ini_cambio)</font><br>";
	 				}else{
						$pasos_completados_configuracion++;
	 				}
	 			}else{
	 				/* Cuando es solo uno de los registros */
	 				$pasos_completados_configuracion++;
	 			}
	 			$fecha_ant = $fecha_fin_cambio; // Asigna $fecha_fin_cambio para siguiente en el for
	 			/* PASO4 Se consulta si la versión de cambios_organico_funcionales tiene cargado su acto administrativo correspondiente utilizando los registros obtenidos en el PASO1 */
	 			if($path_acto_admin==""){
 					$pasos_faltantes_configuracion++;

 					if($fecha_fin_cambio==""){
 						$fecha_fin_cambio = "Actualmente";
 					}

 					$contenido_paso4.= "<img src='imagenes/iconos/checkbox3.png' style='float: left; height:18px; margin-right: 10px;'>Falta cargar acto administrativo que modifica la estructura Organico Funcional en el rango de fechas <font color='red'>($fecha_ini_cambio)</font> hasta <font color='red'>($fecha_fin_cambio)</font><br>";
	 			}else{
					$pasos_completados_configuracion++;
	 			}
	 			$cantidad_pasos_por_cumplir++;
	 			$cantidad_pasos_por_cumplir++;

	 			/* Listado utilizado en admin_depe/index_dependencias.php */
				$selected 				= "";
	 			if($fecha_fin_cambio==""){
					$fecha_fin_cambio 	= "Actualmente";
					$selected 			= "selected = 'selected'";
				}
	 			$listado_cambios_organico_funcionales.="<option title='Cambio Organico-Funcional No. $id_cambio' value='$id_cambio' $selected >Vigencia ($fecha_ini_cambio - $fecha_fin_cambio)</option>";
	 		}
	 	}

		if($pasos_faltantes_configuracion!=0){
			$cont_alertas+=$pasos_faltantes_configuracion;

			$icono_auditoria_sistema 		= "configuracion";
    		$funcion_auditoria_sistema 		= "carga_auditoria_sistema()";
    		$modulo_auditoria_sistema 		= "Modulo Auditoría sistema";
    		$comentario_auditoria_sistema 	= "Pasos pendientes por realizar para configurar correctamente el sistema.";

			$mostrar_alertas.= "<li onclick =\"$funcion_auditoria_sistema\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_auditoria_sistema.png\" style=\"width:18px;\"> Como Administrador tiene <font color=red> $pasos_faltantes_configuracion </font>$comentario_auditoria_sistema <br><b>($modulo_auditoria_sistema)</b></span></a></li>";
		}

    }
/* Hasta aqui consulto los pasos en los cuales hay pendientes en la parametrización o configuración del sistema (Modulo Auditoría del Sistema*/
	
/*Desde aqui consulto la cantidad de radicados con ubicacion que tiene un usuario (Modulo Ubicacion Fisica)*/
	$query_cont_ubicacion 		= "select count(*) from ubicacion_fisica where usuario_actual = '".$login."' ";
	$fila_query_cont_ubicacion 	= pg_query($conectado,$query_cont_ubicacion);
    if($fila_query_cont_ubicacion==true){
    	$columna_count 				= pg_fetch_array($fila_query_cont_ubicacion);
        $cantidad_radicados_fisicos = $columna_count['count'];

    	$icono_rf 	= "prestamo";
        $funcion_rf = "carga_modulo_radicados_fisicos()";
        $modulo_rf 	= "Modulo Ubicacion Fisica";

    	if($cantidad_radicados_fisicos!=0){
    		$comentario_rf = "Documentos en <b><u>FISICO</u></b> que usted tiene en su poder por lo que es directamente responsable.";
	    	
	    	$mostrar_alertas.= "<li onclick =\"$funcion_rf\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_rf.png\" style=\"width:18px;\"> Usted es responsable por <font color=red> $cantidad_radicados_fisicos </font> $comentario_rf<b>($modulo_rf)</b></span></a></li>";
        }else{
        	$comentario_rf = "Usted no tiene documentos físicos en su poder.";

	    	$mostrar_alertas.= "<li onclick =\"$funcion_rf\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_rf.png\" style=\"width:18px;\"> Usted no tiene documentos físicos en su poder.<br><b>($modulo_rf)</b></span></a></li>";
        }
        if($codigo_entidad !='EJC'){ // Exepcion $codigo_entidad !='EJC' para el tema de Oracle Ejercito
	    	//Se suma la cantidad de radicados fisicos asignados a un usuario con la variable $cont_alertas
	    	$cont_alertas+=$cantidad_radicados_fisicos; 
	    	$_SESSION['ubicacion_fisica']  = $cantidad_radicados_fisicos; 
        }else{
        	/* Si el $codigo_entidad ='EJC' no muestra esta alerta ni guarda la variable de sesion */
        	$_SESSION['ubicacion_fisica'] = "";
        }
    }
/*Hasta aqui consulto la cantidad de radicados con ubicacion que tiene un usuario (Modulo Ubicacion Fisica)*/

/* Desde aqui muestra alertas si tiene permiso de ventanilla de radicación */
	if ($ventanilla_radicacion=='SI'){ // Solo muestra reporte de radicación si tiene el permiso asignado
        /* Desde aqui consulto variable alertas de radicados pendientes por cargar informacion. (Modulo Radicacion Entrada)*/
		$query_radicados_pendientes_por_info = "select count(*) from radicado where usuario_radicador=trim(upper('$login')) and asunto IS NULL";
	    $query_radicados_pendientes_por_info = pg_query($conectado,$query_radicados_pendientes_por_info);

	    if($query_radicados_pendientes_por_info==true){
	        $linea_pendiente_pdf  			= pg_fetch_array($query_radicados_pendientes_por_info);    
	        $radicados_pendientes_por_info 	= $linea_pendiente_pdf['count'];
	        
	        if($radicados_pendientes_por_info!=0){
			$comentario_rpi = "Radicados a su nombre pendientes por ingresar datos.";
			$funcion_rpi 	= "carga_modificacion_rapida()";
			$icono_rpi 		= "radicado_pendiente";
			$modulo_rpi 	= "Modulo Radicacion Entrada";

				$mostrar_alertas.= "<li onclick =\"$funcion_rpi\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_rpi.png\" style=\"width:18px;\"><span id=\"desplegable_contador_alertas\">Tiene <font color=red> $radicados_pendientes_por_info </font>$comentario_rpi<br><b>($modulo_rpi)</b></span></span></a></li>";
	        }    
	    }else{
	    	$radicados_pendientes_por_info = 0;
	    }

        //Se suma la cantidad de radicados_pendientes_por_info con la variable $cont_alertas
		$cont_alertas+=$radicados_pendientes_por_info;
		$_SESSION['radicados_pendientes_por_info']=$radicados_pendientes_por_info;
		/* Hasta aqui consulto variable alertas de radicados pendientes por cargar informacion. (Modulo Radicacion Entrada)*/
		
		/* Desde aqui consulto variable alertas de radicados pendientes por cargar imagen PDF. (Modulo Radicacion Entrada)*/
	   	$query_radicados_pendientes_por_pdf 		= "select count(*) from radicado where usuario_radicador=trim(upper('$login')) and (path_radicado is null or path_radicado = '') and clasificacion_radicado !='INVENTARIO'";
	    $result_query_radicados_pendientes_por_pdf 	= pg_query($conectado,$query_radicados_pendientes_por_pdf); 

	    if($result_query_radicados_pendientes_por_pdf==true){
	        $linea_alerta_pendientes_pdf 	= pg_fetch_array($result_query_radicados_pendientes_por_pdf);
	        $rad_pendientes_por_pdf 		= $linea_alerta_pendientes_pdf['count'];

	        if($rad_pendientes_por_pdf!=0){
	        	$comentario_rpp = "Radicados a su nombre pendientes por asociar imagen PDF.";
				$funcion_rpp 	= "buscar_radicado_sin_pdf()";
				$icono_rpp 		= "radicado_pendiente";
				$modulo_rpp 	= "Modulo Radicacion Entrada";

				$mostrar_alertas.= "<li onclick =\"$funcion_rpp\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_rpp.png\" style=\"width:18px;\"> Tiene <font color=red> $rad_pendientes_por_pdf </font>$comentario_rpp <br><b>($modulo_rpp)</b></span></a></li>";
	        }
	    }else{
	    	$rad_pendientes_por_pdf = 0;
	    }

        //Se suma la cantidad de rad_pendientes_por_pdf con la variable $cont_alertas
		$cont_alertas+=$rad_pendientes_por_pdf;
	    $_SESSION['radicados_pendientes_por_pdf']=$rad_pendientes_por_pdf;
	    /* Hasta aqui consulto variable alertas de radicados pendientes por cargar imagen PDF. (Modulo Radicacion Entrada)*/
	}

    /* Desde aqui consulto variable alertas de inventario por archivar pendiente (Modulo Inventarios)*/  
    if($inventario=='SI'){
	    $query_por_archivar_pendientes 			= "select count(*) from inventario i join expedientes e on i.expediente_jonas=e.id_expediente where upper(i.cargado_por)=trim(upper('$login')) and e.codigo_ubicacion_topografica is null";
	    $result_query_por_archivar_pendientes 	= pg_query($conectado,$query_por_archivar_pendientes); 

	    if($result_query_por_archivar_pendientes==true){
	        $linea_alerta_por_archivar 	= pg_fetch_array($result_query_por_archivar_pendientes);
	        $por_archivar 				= $linea_alerta_por_archivar['count'];

	        if($por_archivar!=0){
	        	$comentario_por_archivar 	= "Expedientes del inventario <b>FUID</b> a su nombre por archivar físicamente.";
				$funcion_por_archivar 		= "carga_ubicacion_topografica()";
				$icono_por_archivar 		= "estantes";
				$modulo_por_archivar 		= "Modulo Inventario";

	            $mostrar_alertas.= "<li onclick =\"$funcion_por_archivar\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_por_archivar.png\" style=\"width:18px;\"> Tiene <font color=red> $por_archivar </font>$comentario_por_archivar<b>($modulo_por_archivar)</b></span></a></li>";
	        }
	    }else{
	    	$por_archivar = 0;
	    }

        $cont_alertas+=$por_archivar;
	    $_SESSION['por_archivar_pendientes']=$por_archivar;
    }
    /* Hasta aqui consulto variable alertas de inventario por archivar pendiente (Modulo Inventarios)*/ 
    /* Desde aqui consulto variable alertas de solicitudes de prestamo pendientes por atender (Modulo Prestamos) */  
	if($prestamo_documentos=='SI'){
        $query_solicitud_prestamo_general 				= "select count(*) from prestamos where estado_prestamo='SOLICITADO' and fecha_prestamo is null";
        $query_devoluciones_pendientes_general      	= "select count(*) from prestamos where estado_prestamo='PRESTADO'";

	    $result_query_solicitud_prestamo_general 		= pg_query($conectado,$query_solicitud_prestamo_general); 
        $result_query_devoluciones_pendientes_general   = pg_query($conectado,$query_devoluciones_pendientes_general); 

	    if($result_query_solicitud_prestamo_general==true){
	        $linea_alerta_solicitud_prestamo_general 	= pg_fetch_array($result_query_solicitud_prestamo_general);
	        $solicitud_prestamo_pendientes_general 		= $linea_alerta_solicitud_prestamo_general['count'];

	        if($solicitud_prestamo_pendientes_general!=0){
	        	$icono_sp 		= "prestamo";
        		$funcion_sp 	= "carga_modulo_prestamos()";
        		$modulo_sp 		= "Modulo Prestamos";
        		$comentario_sp 	= "Solicitudes de préstamo de documentos hechas por usuarios, pendientes para entregar en físico.";

	        	$mostrar_alertas.= "<li onclick =\"$funcion_sp\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_sp.png\" style=\"width:18px;\"> Tiene <font color=red> $solicitud_prestamo_pendientes_general </font>$comentario_sp<b><br>($modulo_sp)</b></span></a></li>";
	        }
	    }else{
	        $solicitud_prestamo_pendientes_general 		= 0;
	    }    

    	$cont_alertas+=$solicitud_prestamo_pendientes_general;
	    $_SESSION['solicitud_prestamo_pendientes_general'] 	= $solicitud_prestamo_pendientes_general;

	    if($result_query_devoluciones_pendientes_general==true){
            $linea_alerta_devoluciones_pendientes   = pg_fetch_array($result_query_devoluciones_pendientes_general);
            $por_devolucion_pendiente_general       = $linea_alerta_devoluciones_pendientes['count'];

            if($por_devolucion_pendiente_general!=0){
            	$icono_dpg 			= "prestamo";
        		$funcion_dpg 		= "carga_modulo_prestamos()";
        		$modulo_dpg 		= "Modulo Prestamos";
        		$comentario_dpg 	= "Documentos en préstamo a usuarios pendientes para recuperar en físico.";

	        	$mostrar_alertas.= "<li onclick =\"carga_modulo_prestamos\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_dpg.png\" style=\"width:18px;\"> Tiene <font color=red> $por_devolucion_pendiente_general </font> $comentario_dpg <br><b>($modulo_dpg)</b></span></a></li>";
            }
        }else{
            $por_devolucion_pendiente_general=0;
        }

        $cont_alertas+=$por_devolucion_pendiente_general;
        $_SESSION['por_devolucion_pendiente_general']       = $por_devolucion_pendiente_general;
    }
    /* Hasta aqui consulto variable alertas de solicitudes de prestamo pendientes por atender (Modulo Prestamos) */  

    /* Desde aqui consulto variable de solicitudes de préstamo de documentos realizadas que no han atendido (Modulo Prestamos)*/
	$query_solicitud_prestamo_usuario = "select count(*) from prestamos where estado_prestamo='SOLICITADO' and login_solicitante=trim(upper('$login')) and fecha_prestamo is null";

	$result_solicitud_prestamo_usuario=pg_query($conectado,$query_solicitud_prestamo_usuario); 

    if($result_solicitud_prestamo_usuario==true){
        $linea_solicitud_prestamo_usuario = pg_fetch_array($result_solicitud_prestamo_usuario);
        $por_prestamo_realizada_usuario   = $linea_solicitud_prestamo_usuario['count'];

        if($por_prestamo_realizada_usuario!=0){
        	$icono_ru 		= "prestamo";
    		$funcion_ru 	= "carga_modulo_prestamos()";
    		$modulo_ru 		= "Modulo Prestamos";
    		$comentario_ru 	= "Solicitudes de préstamo de documentos en físico hechas por usted, que no le han entregado.";

	        $mostrar_alertas.= "<li onclick =\"$funcion_ru\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_ru.png\" style=\"width:18px;\"> Tiene <font color=red> $por_prestamo_realizada_usuario </font>$comentario_ru<br><b>($modulo_ru)</b></span></a></li>";
        }
    }else{
        $por_prestamo_realizada_usuario = 0;
    }

	$cont_alertas+=$por_prestamo_realizada_usuario;
    $_SESSION['por_prestamo_realizada_usuario']=$por_prestamo_realizada_usuario;
    /* Hasta aqui consulto variable alertas de solicitudes de préstamo realizadas que no han atendido (Modulo Prestamos)*/
    /* Desde aqui consulto devoluciones pendientes por usuario (Modulo Prestamos)*/
    $query_devoluciones_pendientes_usuario 			= "select count(*) from prestamos where estado_prestamo='PRESTADO' and login_solicitante=trim(upper('$login')) and fecha_devolucion is null";
    $result_query_devoluciones_pendientes_usuario  	= pg_query($conectado,$query_devoluciones_pendientes_usuario); 

    if($result_query_devoluciones_pendientes_usuario==true){
        $linea_alerta_devoluciones_pendientes   = pg_fetch_array($result_query_devoluciones_pendientes_usuario);
        $por_devolucion_pendiente_usuario       = $linea_alerta_devoluciones_pendientes['count'];

	   if($por_devolucion_pendiente_usuario!=0){
	   		$icono_dpu 			= "prestamo";
    		$funcion_dpu 		= "carga_modulo_prestamos()";
    		$modulo_dpu 		= "Modulo Prestamos";
    		$comentario_dpu 	= "Documentos en físico prestados a usted que no ha devuelto.";

	        $mostrar_alertas.= "<li onclick =\"$funcion_dpu\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_dpu.png\" style=\"width:18px;\"> Tiene <font color=red> $por_devolucion_pendiente_usuario </font>$comentario_dpu <br><b>($modulo_dpu)</b></span></a></li>";
        }
    }else{
        $por_devolucion_pendiente_usuario=0;
    }
	$cont_alertas+=$por_devolucion_pendiente_usuario;
    $_SESSION['por_devolucion_pendiente_usuario'] = $por_devolucion_pendiente_usuario;
    /* Hasta aqui consulto devoluciones pendientes por usuario (Modulo Prestamos)*/

    /* Desde aqui consulto radicados no leidos bandeja de entrada */
	// $query_bandeja_entrada 		= "select count(*) from radicado where codigo_carpeta1->'$login'->>'codigo_carpeta_personal'='entrada' and asunto IS NOT NULL and leido ilike '%$login%' ";

	// $result_query_bandeja_entrada = pg_query($conectado,$query_bandeja_entrada);
	// if($result_query_bandeja_entrada==false){
 //        $bandeja_entrada='0';
 //    }else{
 //        $linea_band_entrada = pg_fetch_array($result_query_bandeja_entrada);    
 //        $bandeja_entrada 	= $linea_band_entrada['count'];
 //    }
 //    $_SESSION['bandeja_entrada']= $bandeja_entrada;
	/* Hasta aqui consulto radicados no leidos bandeja de entrada */

/* Desde aqui consulto pendientes por aprobar por usuario */
    $query_pendientes_aprobar = "select count(distinct(v.numero_radicado)) from version_documentos v join radicado r on v.numero_radicado=r.numero_radicado where v.aprobado = 'NO' and trim(upper(v.usuario_que_aprueba)) = trim(upper('$nombre')) and html_asunto !='' and v.medio_solicitud_firmas='electronico'";
    $result_query_pendientes_aprobar = pg_query($conectado,$query_pendientes_aprobar); 

    if($result_query_pendientes_aprobar==true){
        $linea_alerta_pendientes_aprobar   = pg_fetch_array($result_query_pendientes_aprobar);
        $pendientes_aprobar    			   = $linea_alerta_pendientes_aprobar['count'];

	   if($pendientes_aprobar!=0){
	   		$icono_pa 		= "firmar_documento";
    		$funcion_pa 	= "carga_pendientes_aprobar()";
    		$modulo_pa 		= "Modulo Firma Electrónica";
    		$comentario_pa 	= "Documentos pendientes a su nombre por aprobar con firma electrónica.";

	        $mostrar_alertas.= "<li onclick =\"$funcion_pa\"><a href=\"#\"><span><img src=\"imagenes/iconos/$icono_pa.png\" style=\"width:18px;\"> Tiene <font color=red> $pendientes_aprobar </font>$comentario_pa<br><b>($modulo_pa)</b></span></a></li>";
        }
    }else{
        $pendientes_aprobar=0;
    }
	$cont_alertas+=$pendientes_aprobar;
    $_SESSION['pendientes_aprobar'] = $pendientes_aprobar;
/* Hasta aqui consulto pendientes por aprobar por usuario */

	$_SESSION['reasignar_libre']='NO'; // Esta variable hay que crearle campo en la base de datos. Es provisional para el desarrollo de enviar radicados a otras dependencias. 

	/*Se actualizan contadores en interfaz */
		echo "<script>
			$('#bandeja_entrada').html('($bandeja_entrada)');
			$('#bandeja_salida').html('($bandeja_salida)');
			$('#contador_alertas').html('$cont_alertas');
			$('#boton_alertas').html('$mostrar_alertas');
			cargar_carpetas_personales();
		</script>";
}
?>