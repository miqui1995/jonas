<?php
	if(!isset($_SESSION)){
		session_start();
	}
/* En este archivo se reciben los parámetros para realizar radicacion de entrada normal y/o radicacion rapida. Este archivo recibe como 
* parámetro obligatorio mediante POST la variable $_POST['tipo_formulario'] desde la cual se define las opciones de (switch) en todo el archivo */

/* Se inicia importando las variables de conexion a la base de datos para ejectuar las query respectivas. */
	require_once('../../login/conexion2.php');

/* Se define para extraer las variables que se requieran que se encuentran en la sesion */	
    $year 		= date("Y"); 				// Se genera el año actual del sistema en formato 4 digitos (2020) 	
	$timestamp  = date('Y-m-d H:i:s');   	// Genera la fecha de transaccion 

/* Se definen variables globales para todo el archivo desde la sesión y se extrae la variable $_POST['tipo_formulario'] para iniciar las opciones 
* las cuales pueden ser (radicacion_entrada_normal - radicacion_rapida) */
	$codigo_dependencia_radicador  	= $_SESSION['dependencia'];
	$entidad 					  	= $_SESSION['entidad'];
	$codigo_entidad 				= $_SESSION['codigo_entidad'] ;
	$tipo_formulario 				= $_POST['tipo_formulario'];

// Define los valores para la radicacion
	switch ($tipo_formulario) {
		case 'radicacion_entrada_normal':
			$asunto_radicado  				= $_POST['asunto_radicado'];
			$clasificacion_radicado 		= $_POST['clasificacion_radicado'];
			$clasificacion_seguridad 		= $_POST['clasificacion_seguridad'];
			$codigo_contacto 				= $_POST['codigo_contacto'];
			$codigo_dependencia_destino 	= $_POST['codigo_dependencia'];
			$descripcion_anexos 			= $_POST['descripcion_anexos'];
			$dias_tramite 					= $_POST['dias_tramite'];
			$dignatario_remitente 			= $_POST['dignatario_remitente'];
			$direccion_remitente 			= $_POST['direccion_remitente'];
			$mail_remitente 				= $_POST['mail_remitente'];
			$nombre_completo 				= $_POST['nombre_completo'];
			$nombre_dependencia_destino 	= $_POST['search_dependencia_destino'];
			$numero_guia_radicado 			= $_POST['numero_guia_radicado'];
			$telefono_remitente 			= $_POST['telefono_remitente'];
			$tipo_radicado 					= '1';
			$ubicacion_remitente 			= $_POST['ubicacion_remitente'];
			$usuario_destino 				= $_POST['usuario_destino'];
			$medio_recepcion 				= $_POST['medio_recepcion'];
			$medio_respuesta_solicitado     = $_POST['medio_respuesta_solicitado'];	
			$boton_agregar_destinatario 	= "<br><br>
			<div id='div_agregar_destinatario'>
				<h3>Los datos del siguiente contacto deben guardarse en la base de datos como un contacto frecuente:</h3>
				<div class='art_exp' style='width: 600px;' onclick=\"javascript:ingresar_nuevo_contacto('$nombre_completo','$dignatario_remitente','$ubicacion_remitente','$direccion_remitente','$telefono_remitente','$mail_remitente')\" title='Confirmar SOLO SI EL CONTACTO NO EXISTE'> 
					$nombre_completo ( $dignatario_remitente ) <br>$ubicacion_remitente<br>$direccion_remitente | $telefono_remitente | $mail_remitente
			</div></div>";//cierra div .art_exp
	
			break;

		case 'radicacion_rapida':
			$clasificacion_radicado 		= $_POST['clasificacion_radicado']; // Oficio, Peticion, Queja, Reclamo, etc.
			$codigo_dependencia_destino 	= $_POST['codigo_dependencia'];
			$descripcion_anexos 			= $_POST['descripcion_anexos']; // Anexos que llegan (usb, cd, caja, etc)
			$nombre_dependencia_destino 	= $_POST['dependencia_destino'];
			$tipo_radicado 					= '1'; 
			$usuario_destino 				= $_POST['usuario_destino'];
			$dias_tramite 					= $_POST['dias_tramite'];

			$boton_agregar_destinatario 	= "";
			break;	
		
		default:
			# code...
			break;
	}

	$leido 							= $usuario_destino.",";

	/* Desarrollo realizado para ejercito que recibe de manera temporal el numero de radicado desde Orfeo Ejercito para ingresarlo a la base de datos. 
	* Recibe parámetro $_POST['numero_radicado'] desde el archivo funciones_radicacion_entrada.js{function radicar_documento_entrada()} */
	if(isset($_POST['numero_radicado'])){
		$radicado = trim($_POST['numero_radicado']);

		/* Consulto la dependencia del usuario que está radicando. */
		if(isset($codigo_dependencia_destino)){
            $dependencia_usuario    = $codigo_dependencia_destino;
    	}else{
            $dependencia_usuario    = $_SESSION['dependencia'];     // Dependencia del usuario que está radicando
    	}

       	if(isset($login_usuario_actual)){  // Variable que se recibe desde los WebServices
            $login_usuario  = $login_usuario_actual;
        }else{
           	$login_usuario  = $_SESSION['login']; // Usuario que hace el radicado
        }

        if(isset($nivel_seguridad)){  // Variable que se recibe desde los WebServices
           	$nivel  = $nivel_seguridad;
        }else{
           	$nivel  = $_SESSION['nivel']; // Nivel del usuario que realiza la transaccion
        }

	}else{
		require_once('../../login/validar_consecutivo.php'); // Valida si el consecutivo existe y genera el radicado
	}

	/* Fin desarrollo realizado para ejercito */
	 
	// Se arma el json para codigo_carpeta1 de la tabla radicado
	$codigo_carpeta1 	= "{\"$usuario_destino\":{\"codigo_carpeta_personal\":\"entrada\"}}";	

	if($radicado!=""){
		switch ($tipo_formulario) {
		case 'radicacion_entrada_normal':
			$asunto 			 	= substr($asunto_radicado,0,63);
			$anexo 					= substr($descripcion_anexos,0,46);
			$dependencia_destino 	= "$codigo_dependencia_destino-$nombre_dependencia_destino";
			$cod_nom_destino 		= substr($dependencia_destino,0,29);
			$encabezado 	 		= "Radicado de Entrada - $entidad";

			switch ($_SESSION['codigo_entidad']){
				case 'EJC':
				case 'EJEC':
					$ruta_logo 		 		= '../imagenes/logos_entidades/logo_largo_ejc.png';
					break;
				default:
					$ruta_logo 		 		= '../imagenes/iconos/logo_largo.png';
					break;
			}
			echo"
			<div id='guardar_radicado' class='hidden'><input type='text' id='pasar_radicado' name='pasar_radicado' value='$radicado'></div>
			<div id='ubicacion_sticker'>
				<center>
					<table border='0' class='center'>
						<tr>
		 					<td width='30%' onclick=\"javascript:imprimir_sticker_ubic('izq','$anexo','$asunto','$cod_nom_destino','$encabezado','$timestamp', '$radicado', '$ruta_logo')\">
								<img src='imagenes/iconos/ubicacion_sticker_izq.png'>
							</td>
							<td width='30%' onclick=\"javascript:imprimir_sticker_ubic('izq_sup','$anexo','$asunto','$cod_nom_destino','$encabezado','$timestamp', '$radicado', '$ruta_logo')\">
								<img src='imagenes/iconos/ubicacion_sticker_izq_sup.png'>
							</td>
							<td width='30%' onclick=\"javascript:imprimir_sticker_ubic('der','$anexo','$asunto','$cod_nom_destino','$encabezado','$timestamp', '$radicado', '$ruta_logo')\">
								<img src='imagenes/iconos/ubicacion_sticker_der.png'>
							</td>
						</tr>
					</table>
					<form action='formatos/api_sticker.php' method='post' id='api_sticker' target='_blank' class='hidden'>
						<textarea id='json_info_sticker' name='json_info_sticker' class='muestra_select' rows='10' style='width:100%;padding:5px;'></textarea> 
					</form>
				</center>
			</div>
			";	
			$query_radicado1 = "insert into radicado(numero_radicado, fecha_radicado, codigo_carpeta1, numero_guia_oficio, descripcion_anexos, codigo_contacto, dependencia_actual, usuarios_control, usuarios_visor, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, clasificacion_radicado, clasificacion_seguridad, termino, estado_radicado, medio_recepcion, medio_respuesta_solicitado) values ('$radicado', '$timestamp', '$codigo_carpeta1','$numero_guia_radicado', '$descripcion_anexos', '$codigo_contacto','$codigo_dependencia_destino', '$leido', '$leido', '$codigo_dependencia_radicador', '$login_usuario','$asunto_radicado','$nivel', '$leido', '$clasificacion_radicado', '$clasificacion_seguridad', '$dias_tramite','en_tramite', '$medio_recepcion', '$medio_respuesta_solicitado');";

			/*Se agrega la variable $query_radicado2 en la que se guarda el insert a ubicacion_fisica.
			La funcionalidad nextval('ubicacion_fisica_id_seq'::regclass) sirve para que inserte el registro con el maximo id +1 */
			$query_radicado2 = "insert into ubicacion_fisica values(nextval('ubicacion_fisica_id_seq'::regclass),'$radicado','$login_usuario','Radicacion entrada', '$timestamp');";

			$query_datos_origen_radicado = "insert into datos_origen_radicado(numero_radicado, nombre_remitente_destinatario, dignatario, ubicacion, direccion, telefono, mail) values ('$radicado', '$nombre_completo', '$dignatario_remitente', '$ubicacion_remitente', '$direccion_remitente', '$telefono_remitente', '$mail_remitente');";

			/*La variable $query_radicado guarda todos los insert concatenados. */			
			$query_radicado = $query_radicado1.$query_datos_origen_radicado.$query_radicado2;

			$transaccion_historico 	= "Radicacion de entrada";		// Variable para tabla historico_eventos
			$transaccion 			= "radicacion_entrada";	 	// Variable para auditoria
			$comentario 			= "Documento radicado como entrada";	// Variable para historico eventos

			$varEnvio="fecha=$timestamp&usu_radicador=$login_usuario&radicado=$radicado&dependencia_destino=$codigo_dependencia_destino-$nombre_dependencia_destino&anexos=$descripcion_anexos&entidad=$entidad&asunto=$asunto_radicado&codigo_entidad=$codigo_entidad";

			$boton_agregar_destinatario.= "<br>
			<input type='hidden' name ='radicado' id='radicado' value='$radicado' placeholder='numero_radicado'>
			<input type='hidden' name ='usuario_actual_radicado' id='usuario_actual_radicado' value='' placeholder='usuario_actual_radicado'>


			<input type='button' class='botones2' value='Requiere Copiar (Informar) Radicado a otros usarios' onclick='validar_transaccion(\"informar_radicado\",\"lista_usuarios_informados\")'>

			<!-- div que contiene ventana modal para informar radicado -->	
			<div id='ventana_informar_radicado' class='ventana_modal'>
				<div class='form'>
					<div class='cerrar'><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
					<h1>Formulario Informar Radicado</h1>
					<hr>
					<table border ='0'>
						<tr>
							<td class='descripcion' width='300px;'>Radicado va a ser informado a :</td>
							<td class='detalle' id='lista_usuarios_informados'></td>
						</tr>
						<tr>
							<input type='hidden' name ='codigo_entidad' id='codigo_entidad' value='$codigo_entidad' placeholder='codigo entidad'>
							<input type='hidden' name ='usuario_destino' id='usuario_destino' value='$usuario_destino' placeholder='codigo entidad'>
							<input type='hidden' name ='usuario_origen' id='usuario_origen' value='$login_usuario' placeholder='usuario_origen'>
							<input type='hidden' name ='usuario_actual_informado' id='usuario_actual_informado' value='' placeholder='usuario_actual_informado'>
							<input type='hidden' name ='usuarios_para_agregar_informar' id='usuarios_para_agregar_informar' value='' placeholder='usuarios_para_agregar_informar'>
							<input type='hidden' name ='usuarios_nuevos_informar' id='usuarios_nuevos_informar' value='' placeholder='usuarios_nuevos_informar'>
						</tr>
						<tr>
							<td class='descripcion'>Mensaje para informar :</td>
							<td class='detalle' colspan='3'>
								<textarea name='mensaje_informar' id='mensaje_informar' rows='2' style='width:100%;padding:5px;' placeholder='Ingrese el mensaje para informar el radicado. Sea lo más específico posible' title='Ingrese el mensaje para informar el radicado. Sea lo más específico posible' ></textarea>
								<div id='mensaje_informar_null' class='errores'>El mensaje de informar es obligatorio</div>
								<div id='mensaje_informar_min' class='errores'>El mensaje de informar no puede ser menor a 6 caracteres (numeros o letras) </div>
								<div id='mensaje_informar_max' class='errores'>El mensaje de informar no puede ser mayor a 500 caracteres. (Actualmente <b><u id='mensaje_informar_contadormax'></u></b> caracteres)</div>
							</td>
						</tr>
						<tr>
							<td colspan='2'>
								<center><input type='button' value='Informar Documento' class='botones' onclick='valida_informar_radicado()'><center>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<!-- Hasta aqui el div que contiene ventana modal para derivar radicado-->";	
			break;

		case 'radicacion_rapida':
			$query_radicado1 ="insert into radicado(numero_radicado, fecha_radicado, descripcion_anexos, dependencia_actual, usuarios_control, usuarios_visor, dependencia_radicador, usuario_radicador, nivel_seguridad, leido, clasificacion_radicado, termino, estado_radicado) 
				values('$radicado', '$timestamp', '$descripcion_anexos','$codigo_dependencia_destino', '$leido', '$leido', '$codigo_dependencia_radicador', '$login_usuario', '$nivel', '$leido', '$clasificacion_radicado', '$dias_tramite', 'en_tramite');";
			/*Se agrega la variable $query_radicado2 en la que se guarda el insert a ubicacion_fisica.
			La funcionalidad nextval('ubicacion_fisica_id_seq'::regclass) sirve para que inserte el registro con el maximo id +1 */
			$query_radicado2 = "insert into ubicacion_fisica values(nextval('ubicacion_fisica_id_seq'::regclass),'$radicado','$login_usuario','Radicacion rapida-entrada', '$timestamp');";
			$query_radicado = $query_radicado1.$query_radicado2;	

			$transaccion_historico  = "Radicacion rapida";		// Variable para tabla historico_eventos
			$transaccion 			= "radicacion_rapida";	 	// Variable para auditoria
			$comentario 			= "Documento radicado como entrada por radicación rápida";	// Variable para historico eventos

			$varEnvio="fecha=$timestamp&usu_radicador=$login_usuario&radicado=$radicado&dependencia_destino=$codigo_dependencia_destino-$nombre_dependencia_destino&anexos=$descripcion_anexos&entidad=$entidad&codigo_entidad=$codigo_entidad";	

			$boton_agregar_destinatario = ""; // Variable para agregar_destinatario
			break;	
		}

		if(pg_query($conectado,$query_radicado)){	// Si se crea el radicado
			$creado = "$radicado"; // Variable para auditoria
			
			require_once('../../login/inserta_historico.php');
			
			/*Condicion si el tipo de radicacion es radicacion de entrada */
			if($tipo_radicado=='1'){
				echo "<center>
					<a href=\"radicacion/radicacion_entrada/sticker.php?$varEnvio\" title='Imprimir Sticker' onClick=\"window.open(this.href,'window','toolbar=no, status=no, scrollbars=no, location=no, menubar=no, directories=no, width=710, height=350, top=300, left=400');return false\" >
						<h1>Radicado $radicado</h1>
					</a>
					<a href=\"javascript:cargar_modificacion('$radicado','','$usuario_destino')\" style='text-decoration:none'>
					<input type='button' class='botones' id='modificar_radicado' name='modificar_radicado' value='Modificar Radicado'>
					</a>
					$boton_agregar_destinatario	
				</center>";
				require_once("../../login/validar_inactividad.php");
			}else{
				echo "El tipo de radicado no es '1' o 'Entrada'";		
			}	
		}else{
			echo "No se pudo radicar el documento. Comuníquese con el administrador del sistema";
		}	
	}	
?>
<!-- 
<script type="text/javascript">
	function validar_transaccion(transaccion,div){
        $("#numero_radicado").val(radicado);
        var radicado=$('#radicado').val();

        $('#ventana_informar_radicado').slideDown("slow");
		        
        $.ajax({  
            type: 'POST',
            url: 'bandejas/entrada/transacciones_radicado.php',
            data: {
            	'radicado' 		: radicado,
                'transaccion' 	: transaccion
            },          
            success: function(resp1){
                if(resp1!=""){
                    $("#"+div).html(resp1);
                }else{
                    alert(resp1)
                }
            }
        })
	}
/* Fin validar transaccion */
</script>
 -->