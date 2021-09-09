<?php 
	require_once("../../login/validar_inactividad.php");
	$timestamp  = date('Y-m-d H:i:s');   // Genera la fecha de transaccion 

	/* Se define desplegable con clasificación del radicado (PQR,Oficio, etc) */ 
		$query_termino="select * from tipo_doc_termino where activo ='SI' union select * from tipo_doc_termino_pqr where activo ='SI' order by tipo_documento";
		$fila_termino = pg_query($conectado,$query_termino);
		/* Calcula el numero de registros que genera la consulta anterior. */
		$registros_termino= pg_num_rows($fila_termino);
		/* Recorre el array generado e imprime uno a uno los resultados. */	
		
		$select_tipo_documento = "<select name='termino' id='termino' class='select_opciones' class='select_opciones' onchange='muestra_termino()'>"; // Inicia el input select
			for ($t=0;$t<$registros_termino;$t++){
				$linea_termino = pg_fetch_array($fila_termino);

				$tipo_documento 			= $linea_termino['tipo_documento'];
				$tiempo_tramite 			= $linea_termino['tiempo_tramite'];
	            $descripcion_tipo_documento = $linea_termino['descripcion_tipo_documento'];

				// $codigo_tipo_doc = $linea_termino['codigo_tipo_doc'];

				if($tipo_documento=='OFICIO' or $tipo_documento=="COMUNICACION OFICIAL"){
					$select_tipo_documento.= "<option value='$tipo_documento' selected='selected' title='$descripcion_tipo_documento'>$tipo_documento</option>"; // Si es la opcion "OFICIO" sea precargada por defecto.
				}else{
					$select_tipo_documento.= "<option value='$tipo_documento' title='$descripcion_tipo_documento'>$tipo_documento</option>";
				}	
			}
		$select_tipo_documento.="</select>"; // Fin del input select
	/* Hasta aqui se define desplegable con clasificación del radicado (PQR,Oficio, etc) */ 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Buscador de Remitente</title>
	<script type="text/javascript" src="include/js/funciones_radicacion_entrada.js"></script>
	<link rel="stylesheet" href="include/css/estilos_radicacion_entrada.css">
	<script type="text/javascript">

function validar_campos_formulario_radicacion_entrada(){
		var error_campos_form_entrada = "";

		var nombre_completo = $("#nombre_completo").val();
		switch(nombre_completo){
			case '':
				$("#dignatario_remitente_null").html("El nombre de la Persona Natural/Dignatario de la empresa/entidad es obligatorio.")
			break;
			case 'Persona natural':
				$("#dignatario_remitente_null").html("El nombre de la <b style='font-size:20px;'>Persona Natural</b> es obligatorio.")
			break;
			default: 
				$("#dignatario_remitente_null").html("Debe ingresar el nombre del funcionario de la empresa/entidad  <br><b style='font-size:20px;'>"+nombre_completo+"</b><br> quien remite o firma el documento que está recibiendo.")
			break;
		}

		/* Validar campo3(dias_tramite). */
		if($("#error_dias_tramite").is(":visible") || $("#dias_tramite_max").is(":visible") || $("#dias_tramite_null").is(":visible") || $("#dias_tramite_cero").is(":visible")){
			$("#dias_tramite").focus();
			// console.log("1")
			return false;
		}else{
			ocultar_dias_tramite(1);
			/* Validar campo1(medio_recepcion). Primero invoca la funcion "validar_input('xxxx')" para revisar que el input #numero_guia_radicado lo ponga en mayusculas, valide máximo y mínimo. 
			Luego define las variables medio_recepcion - numero_guia_radicado y valida que las variables sean correctas o muestra errores correspondientes. */
			var medio_recepcion  		= $("#medio_recepcion").val();
			var numero_guia_radicado 	= $("#numero_guia_radicado").val().trim();
			if(medio_recepcion=="servicio_postal"){
				switch(numero_guia_radicado){
					case "GUIA 4-72 NO.":
					case "GUIA INTERRAPIDISIMO NO.":
					case "GUIA SERVIENTREGA NO.":
					case "":
						valida_recepcion();
						$("#numero_guia_radicado_error").slideDown("slow");
						$("#recibido_mensajeria").slideDown("slow");
						$("#numero_guia_radicado").focus();
						// console.log("2")
						error_campos_form_entrada+="numero_guia_radicado";
					break;

					default:
						// console.log("2-2")
						$("#numero_guia_radicado_error").slideUp("slow");
						$("#recibido_mensajeria").slideUp("slow");
					break;
				}
			}

			if(error_campos_form_entrada!=""){
				// console.log("21 "+error_campos_form_entrada)
				return false;
			}else{
				/* Validar campo2(correo_electronico). Define las variables medio_respuesta_solicitado - dignatario_remitente, mail_remitente y valida que las variables sean correctas o muestra errores correspondientes. */
				var medio_respuesta_solicitado  = $("#medio_respuesta_solicitado").val();
				var dignatario_remitente 		= $("#dignatario_remitente").val().trim();
				var mail_remitente 				= $("#mail_remitente").val();
				
				if(medio_respuesta_solicitado=="correo_electronico"){
					if(dignatario_remitente!="" && mail_remitente==""){
						$("#mail_respuesta_obligatorio").slideDown("slow");
						$("#mail_remitente").focus();
					// console.log("12")
						error_campos_form_entrada+="correo_electronico";
					}else{
						// $("#mail_respuesta_obligatorio").slideUp("slow");
					}
				}
				if(error_campos_form_entrada!=""){
					// console.log("35 "+error_campos_form_entrada)
					return false;
				}else{
					$("#mail_respuesta_obligatorio").slideUp("slow");
					/* Validar campo4(numero_guia_radicado). */
					if($("#numero_guia_radicado_max").is(":visible") || $("#numero_guia_radicado_min").is(":visible") || $("#recibido_mensajeria").is(":visible") || $("#numero_guia_radicado_error").is(":visible")){
						$("#numero_guia_radicado").focus();
						// console.log("3")
						return false;
					}else{
						/* Validar campo5(descripcion_anexos). */
						if($("#descripcion_anexos_min").is(":visible") || $("#descripcion_anexos_max").is(":visible")){
							$("#descripcion_anexos").focus();
							// console.log("4")
							return false;
						}else{
							/* Validar campo6(nombre_completo). */
							if($("#nombre_completo_max").is(":visible") || $("#nombre_completo_min").is(":visible") || $("#nombre_completo_null").is(":visible")){
								$("#nombre_completo").focus();
								// console.log("5")
								return false;
							}else{
								/* Validar campo7(dignatario_remitente). */
								if($("#dignatario_remitente_max").is(":visible") || $("#dignatario_remitente_min").is(":visible") || $("#dignatario_remitente_null").is(":visible")){
									$("#dignatario_remitente").focus();
									// console.log("6")
									return false;
								}else{
									/* Validar campo8(ubicacion_remitente). */
									if($("#ubicacion_remitente_min").is(":visible") || $("#error_ubicacion_remitente").is(":visible") || $("#error_no_selecciona_ubicacion").is(":visible")){
										$("#ubicacion_remitente").focus();
										// console.log("7")
										return false;
									}else{
										/* Validar campo9(direccion_remitente). */
										if($("#direccion_remitente_min").is(":visible") || $("#direccion_remitente_max").is(":visible")){
											$("#direccion_remitente").focus();
											// console.log("8")
											return false;
										}else{
											/* Validar campo10(telefono_remitente). */
											if($("#telefono_remitente_max").is(":visible") || $("#telefono_remitente_min").is(":visible")){
												$("#telefono_remitente").focus();
												// console.log("9")
												return false;
											}else{
												/* Validar campo11(mail_remitente). */
												if($("#mail_remitente_max").is(":visible") || $("#mail_remitente_formato_mail").is(":visible") || $("#mail_respuesta_obligatorio").is(":visible")){
													$("#mail_remitente").focus();
													// console.log("13")
													return false;
												}else{
													/* Validar campo12(asunto_radicado). */
													if($("#asunto_radicado_max").is(":visible") || $("#asunto_radicado_min").is(":visible") || $("#asunto_radicado_null").is(":visible")){
														$("#asunto_radicado").focus();
														// console.log("14")
														return false;
													}else{
														/* Validar campo13(search_dependencia_destino). */
														if($("#search_dependencia_destino_max").is(":visible") || $("#search_dependencia_destino_min").is(":visible") || $("#sin_distribuidor").is(":visible")){
															$("#search_dependencia_destino").focus();
															// console.log("15")
															return false;									
														}else{
															validar_input('dias_tramite');
															validar_input('numero_guia_radicado');
															validar_input('descripcion_anexos');
															validar_input('nombre_completo');
															validar_input('dignatario_remitente');
															validar_input('ubicacion_remitente');
															validar_input('direccion_remitente');
															validar_input('telefono_remitente');
															validar_input('mail_remitente');
															validar_input('asunto_radicado');
															validar_input('search_dependencia_destino');
															return true;									
														}													
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}	

function radicar_documento_entrada(){
	/* Primero realiza la validación de todos los campos del formulario de radicación de entrada */
	var validar_form_entrada = validar_campos_formulario_radicacion_entrada();

	/* Si en la pantalla aprarece algún error, se detiene aqui y devuelve FALSO */
	if($(".errores").is(":visible")){
		return false;
	}else{
		if(validar_form_entrada==false){
			return false;
		}else{
			/* Se define cada una de las variables que se necesitan para hacer una radicacion de entrada */
			var asunto_radicado  			= $("#asunto_radicado").val();
			var clasificacion_radicado 		= $("#termino").val();
			var clasificacion_seguridad 	= $("#clasificacion_seguridad").val();
			var codigo_contacto 			= $("#codigo_contacto").val();
			var codigo_dependencia 			= $("#codigo_dependencia").val();
			var descripcion_anexos 			= $("#descripcion_anexos").val();
			var dias_tramite 				= $("#dias_tramite").val();
			var dignatario_remitente 		= $("#dignatario_remitente").val();
			var direccion_remitente 		= $("#direccion_remitente").val();
			var mail_remitente 				= $("#mail_remitente").val();
			var medio_recepcion  			= $("#medio_recepcion").val();
			var medio_respuesta_solicitado  = $("#medio_respuesta_solicitado").val();
			var nombre_completo 			= $("#nombre_completo").val();
			var numero_guia_radicado 		= $("#numero_guia_radicado").val().trim();
			var search_dependencia_destino 	= $("#search_dependencia_destino").val();
			var telefono_remitente 			= $("#telefono_remitente").val();
			var tipo_formulario 			= $("#tipo_formulario").val();
			var ubicacion_remitente 		= $("#ubicacion_remitente").val();
			var usuario_destino 			= $("#usuario_destino").val();

			if(descripcion_anexos==""){
				descripcion_anexos="Sin Anexos";
			}
		
			loading('div_boton_enviar');

			/* Si en la pantalla no hay ningún error generado desde la validación de cada uno de los campos continúa aqui definiendo el código de la entidad para definir cuales valores envía. */	
			var codigo_entidad = $("#codigo_entidad").val();

		  	/* Esta es una modificacion temporal desarrollada para ejercito nacional con el fin de reeemplazar el numero de radicado (Jonas) por 
		  	el radicado que se genera generado en Orfeo-Ejercito. Se definen las variables y se envían mediante AJAX utilizando POST a la
		  	direccion de un servidor de Orfeo-Ejercito (http://172.22.2.226/pruebas/interoperabilidad_rest/api_jonas.php) donde internamente ese
		  	servidor de Orfeo-Ejercito genera un número de radicado Orfeo-Ejercito el cual devuelve como respuesta. Si el tamaño de la respuesta 
		  	son (16) caracteres, quiere decir que es un radicado Orfeo-Ejercito por lo que procede a enviar todos los datos al archivo 
			radicacion/radicacion_entrada/radicar.php mediante POST para que se almacene en Jonas todos los datos del radicado. 
			En caso que el tamaño de la respuesta no, sean (16) caracteres, quiere decir que hubo un error, por lo que devuelve en la consola del 
			navegador el mensaje "No se puede enviar, codigo de error XXXX". 
			Los datos que se envían adicionalmente en este desarrollo de Ejercito son (usuario_origen - codigo_entidad - numero_radicado{que viene en el "resp" de
			AJAX})*/
			if(codigo_entidad=='EJC'){
				var usuario_origen = $("#login_usuario").val();
		
				$.ajax({
		            type    	: 'POST',
		            url     	: 'http://172.22.2.226/pruebas/interoperabilidad_rest/api_jonas.php',
		            datatype 	: 'jsonp',
		            crossDomain : true,
		            data 		: {
						'recibe_jonas'     			: 'radicado_entrada',
		                'asunto'           			: asunto_radicado,
		                'clasificacion_seguridad' 	: clasificacion_seguridad,
		                'descripcion_anex' 			: descripcion_anexos,
		                'destinatario'     			: nombre_completo,
		                'dignatario'       			: dignatario_remitente,
		                'direccion'        			: direccion_remitente,
		                'login_destino'    			: usuario_destino,
		                'login_origen'     			: usuario_origen,
		                'mail'             			: mail_remitente,
		                'medio_recepcion'  			: medio_recepcion,
		                'municipio'        			: ubicacion_remitente,
		                'numero_guia_rad'  			: numero_guia_radicado,
						'telefono'         			: telefono_remitente
		            },
		            success: function(resp){
		            	var resp_trim = $.trim(resp);
						if(resp_trim.length==16){  // 16 es la longitud de caracteres de un radicado Orfeo-Ejercito
							$('#contenido').load('radicacion/radicacion_entrada/radicar.php',{asunto_radicado:asunto_radicado, clasificacion_radicado:clasificacion_radicado, clasificacion_seguridad:clasificacion_seguridad, codigo_contacto:codigo_contacto, codigo_dependencia:codigo_dependencia, descripcion_anexos:descripcion_anexos, dias_tramite:dias_tramite, dignatario_remitente:dignatario_remitente, direccion_remitente:direccion_remitente, mail_remitente:mail_remitente, medio_recepcion:medio_recepcion, medio_respuesta_solicitado:medio_respuesta_solicitado, nombre_completo:nombre_completo, numero_guia_radicado:numero_guia_radicado, numero_radicado:resp_trim, search_dependencia_destino:search_dependencia_destino, telefono_remitente:telefono_remitente, tipo_formulario:tipo_formulario, ubicacion_remitente:ubicacion_remitente,usuario_destino:usuario_destino});
						}else{
							alert("No se han recibido datos desde Orfeo-Ejercito, codigo de error "+resp);
						}
		            }
				})	
			}else{
				/* Si el codigo de la entidad no es EJC */
				$('#contenido').load('radicacion/radicacion_entrada/radicar.php',{asunto_radicado:asunto_radicado,clasificacion_radicado:clasificacion_radicado, clasificacion_seguridad:clasificacion_seguridad, codigo_contacto:codigo_contacto, codigo_dependencia:codigo_dependencia, descripcion_anexos:descripcion_anexos, dias_tramite:dias_tramite,dignatario_remitente:dignatario_remitente, direccion_remitente:direccion_remitente, mail_remitente:mail_remitente, medio_recepcion:medio_recepcion, medio_respuesta_solicitado:medio_respuesta_solicitado, nombre_completo:nombre_completo, numero_guia_radicado:numero_guia_radicado, search_dependencia_destino:search_dependencia_destino, telefono_remitente:telefono_remitente, tipo_formulario:tipo_formulario, ubicacion_remitente:ubicacion_remitente, usuario_destino:usuario_destino});
			}
		}
	}	
}
</script>	
</head>
<body>
	<h1 class="center">Formulario Radicacion Entrada</h1>
	<form enctype="multipart/form-data" method="post" id ="formulario_modificar_radicado" name ="formulario_modificar_radicado" autocomplete="off">
		<!-- La variable $codigo_entidad la hereda desde el archivo incluido como require_once("../../login/validar_inactividad.php");-->
		<input type="hidden" name="codigo_entidad" id="codigo_entidad" <?php echo "value='$codigo_entidad'"; ?> >
		<input type="hidden" name="login_usuario" id="login_usuario" <?php echo "value='$login'"; ?> >
		<!--			<input type="hidden" name="MAX_FILE_SIZE" value="3000000" /> -->
		<hr>
		<center>
		<table border="0" style="font-size:15px;">
			<tr>
				<td class="descripcion descripcion_ancho" style='height: 5px;'>
					Fecha Radicacion:
				</td>
				<td class="detalle detalle_ancho">	
					<input type="search" title="Fecha en la cual se generó el radicado. No se puede modificar." style ="width: 80px;" value=<?php echo $timestamp; ?> disabled>
				</td>
				<td class="descripcion descripcion_ancho" style='height: 5px;'>
					Medio de Recepcion:
				</td>
				<td class="detalle detalle_ancho">	
					<select id="medio_recepcion" class='select_opciones' onchange="valida_recepcion(); validar_campos_formulario_radicacion_entrada();">
						<option value='correo_electronico' title='Este documento se recibe mediante correo electrónico o e-mail'>Correo Electrónico</option>
						<option value='presencial' title='Este documento se recibe de manera física y se entrega Sticker de recibido al usuario.' selected="selected">Personal</option>
						<option value='servicio_postal' title='Este documento se recibe mediante mensajería (4-72, Interrapidisimo, Servientrega, etc.)'>Servicio Postal</option>
					</select>
				</td>
				<td class="descripcion">
					Medio de respuesta solicitado:
				</td>
				<td class="detalle detalle_ancho">
					<select id="medio_respuesta_solicitado" onchange="validar_campos_formulario_radicacion_entrada()" class='select_opciones'>
						<option value='correspondencia_fisica' title='El usuario indica que desea recibir la respuesta a este documento mediante oficio impreso en papel y enviado por mensajería fisica'>Correspondencia Fisica</option>
						<option value='correo_electronico' title='El usuario indica que requiere la respuesta a este documento mediante correo electrónico'>Correo Electronico</option>
						<option value='no_requiere_respuesta' title='El usuario manifiesta que no desea respuesta a este documento'>No Requiere Respuesta</option>
					</select>
				</td>
				<td class="descripcion">
					Tipo de Documento:
				</td>	
				<td class="detalle" width="300px">
					<div name="td" title="Del tipo de documento depende el tiempo de trámite asignado" style="float: left;">
					<!-- Aqui carga con ajax el tipo de documento (Termino para Radicacion Entrada) -->
						<?php echo "$select_tipo_documento"; ?>
					</div>
					<div id="div_dias_tramite" style="display: none; float: left; width: 50px;">
						<input type="text" id="dias_tramite" name="dias_tramite" value="15" style="float: left;" onblur="ocultar_dias_tramite(1); validar_campos_formulario_radicacion_entrada()" onkeyup="espacios_formulario('dias_tramite', 'sin_caracteres');">
					</div>
					<div id="error_dias_tramite" class="errores" style="float: left;">El valor ingresado debe ser un número</div>
					<div id="dias_tramite_max" class="errores" style="float: left;">No puede tener mas de 30 dias de trámite</div>
					<div id="dias_tramite_null" class="errores" style="float: left;">Este campo es obligatorio</div>
					<div id="dias_tramite_cero" class="errores" style="float: left;">El valor no puede ser CERO</div>
					<div class="detalle" id="termino_td" align="left"  max-width="50px" onclick="mostrar_dias_tramite()">
						<b>15 dias habiles de tramite</b>
					</div>
				</td>
				<td class="descripcion">
					Clasificación de Seguridad:
				</td>	
				<td class="detalle detalle_ancho">
					<select id="clasificacion_seguridad" class='select_opciones'>
						<option value='sin_clasificacion' title='' selected="selected">Sin Clasificación</option>
						<option value='restringido' title=''>Restringido</option>
						<option value='confidencial' title=''>Confidencial</option>
						<option value='secreto' title=''>Secreto</option>
						<option value='ultrasecreto' title=''>Ultrasecreto</option>
						<option value='publica_clasificada' title=''>Pública Clasificada</option>
						<option value='publica_reservada' title=''>Pública Reservada</option>
					</select>
				</td>
				<td rowspan="8" width="0%">
					<div id="viewer3" style="max-height:400px;"></div>
					<iframe id="viewer" frameborder="0" scrolling="yes" width="100%" height="400" style='display: none;'>
					</iframe> 
				</td>
			</tr>
			<tr>
				<td class="descripcion descripcion_ancho">
					Numero de Guía - Oficio del Radicado:
				</td>
				<td class="detalle detalle_ancho" colspan="5">
					<input type="search" placeholder="Ingrese el número de guía o número del oficio del radicado" title="Ingrese el número de guía o número del oficio del radicado" id="numero_guia_radicado" name="numero_guia_radicado" onblur="validar_input('numero_guia_radicado'); validar_campos_formulario_radicacion_entrada()">
					<div id="numero_guia_radicado_max" class="errores">El número o guía del radicado no puede ser mayor a 100 caracteres. (Actualmente <b><u id='numero_guia_radicado_contadormax'></u></b> caracteres)</div>
					<div id="numero_guia_radicado_min" class="errores">El número o guía del radicado no puede ser menor a 3 caracteres.</div>
					<div id="recibido_mensajeria" class="hidden">
						<div class='art' onclick="carga_recibido_mensajeria('1')" >GUIA 4-72 NO. </div>
						<div class='art' onclick="carga_recibido_mensajeria('2')" >GUIA INTERRAPIDISIMO NO. </div>
						<div class='art' onclick="carga_recibido_mensajeria('3')" >GUIA SERVIENTREGA NO. </div>
					</div>
					<div id="numero_guia_radicado_error" class="errores">El medio de recepción seleccionado es <b style='font-size:20px;'>Servicio Postal</b> por lo que debe seleccionar una opción y/o ingresar <br>el numero de Guía-Oficio del documento que está recibiendo.</div>
				</td>
				<td class="descripcion" style="height: 5px;">
					Descripcion de Anexos:
				</td>
				<td class="detalle" colspan="5">
					<input type="search" id="descripcion_anexos" name="descripcion_anexos" placeholder="El radicado viene con anexo CD, AZ, USB, Caja, etc." title="El radicado viene con anexo CD, AZ, USB, Caja, etc." onblur="validar_input('descripcion_anexos');validar_campos_formulario_radicacion_entrada()">
					<div id="descripcion_anexos_min" class="errores">La descripción de los anexos no puede ser menor a 3 caracteres (numeros o letras) </div>
					<div id="descripcion_anexos_max" class="errores">La descripción de los anexos no puede ser mayor a 100 caracteres. (Actualmente <b><u id='descripcion_anexos_contadormax'></u></b> caracteres)</div>
				</td>
			</tr>
			<tr>
				<td class="descripcion">
					Nombre Empresa o Persona Natural:
				</td>
				<td class="detalle" colspan="5">
					<input type="search" name="nombre_completo" id="nombre_completo" placeholder="Ingrese si es Persona Natural o el Nombre de Empresa/Entidad" title="Ingrese si es Persona Natural o el Nombre de Empresa/Entidad " onblur="validar_input('nombre_completo'); validar_campos_formulario_radicacion_entrada()" value="Persona natural">
					<div id="sugerencias_nombre_completo" style="overflow-x: auto; max-height: 100px;"></div>

					<div id="nombre_completo_max" class="errores">El nombre completo del usuario no puede ser mayor a 100 caracteres. (Actualmente <b><u id='nombre_completo_contadormax'></u></b> caracteres)</div>
					<div id="nombre_completo_min" class="errores">El nombre  del usuario (con apellido) no puede ser menor a 6 caracteres (sin numeros)</div>
					<div id="nombre_completo_null" class="errores">El nombre completo del usuario es obligatorio.</div>
				</td>
				<td class="descripcion">
					Dignatario de la Empresa o Entidad:
				</td>
				<td class="detalle" colspan="5">
					<input type="search" name="dignatario_remitente" id="dignatario_remitente" placeholder="Ingrese Nombres y Apellidos completos (sin numeros)" title="Ingrese Nombres y Apellidos completos (sin numeros)" onblur="validar_campos_formulario_radicacion_entrada()">
					<div id="sugerencias_dignatario" style="overflow-x: auto; max-height: 100px;"></div>

					<div id="dignatario_remitente_min" class="errores">El nombre del dignatario (con apellidos) no puede ser menor a 6 caracteres (sin numeros)</div>
					<div id="dignatario_remitente_max" class="errores">El nombre completo del dignatario no puede ser mayor a 100 caracteres (Actualmente <b><u id='dignatario_remitente_contadormax'></u></b> caracteres)</div>
					<div id="dignatario_remitente_null" class="errores">El nombre de la Persona Natural es obligatorio.</div>
				</td>
			</tr>
			
			<tr>	
				<td class="descripcion" style='height: 5px;'>
					Ubicación Remitente del Radicado:
				</td>
				<td class="detalle" colspan="5">
					<input type="search" name="ubicacion_remitente" id="ubicacion_remitente" placeholder="Ingrese la ubicación del remitente" title="Ingrese la ubicación del remitente" value="BOGOTA, D.C. (BOGOTA) COLOMBIA-AMERICA" onblur="validar_input('ubicacion_remitente');validar_campos_formulario_radicacion_entrada()">
					<div id="sugerencias_ubicacion_remitente" style="display:none"></div>

					<div id="ubicacion_remitente_min" class="errores">El nombre del municipio no puede ser menor a 3 caracteres (sin numeros)</div>
					<div id="error_ubicacion_remitente" class="errores">
						No se han encontrado resultados. Si desea ingresar un nuevo municipio comuníquese con el administrador del sistema</a>
					</div>
					<div id="error_no_selecciona_ubicacion" class="errores">Seleccione una ubicación válida por favor</div>
				</td>
				
				<td class="descripcion">
					Direccion Remitente del Radicado:
				</td>
				<td class="detalle" colspan="5">
					<input type="search" name="direccion_remitente" id="direccion_remitente" placeholder="Digite dirección completa del remitente" title="Digite dirección completa del remitente" onblur="validar_input('direccion_remitente');validar_campos_formulario_radicacion_entrada()">
					<div id="sugerencias_direccion"></div>
					<div id="direccion_remitente_min" class="errores">La dirección del contacto no puede ser menor a seis caracteres (numeros o letras) </div>
					<div id="direccion_remitente_max" class="errores">La dirección del contacto no puede ser mayor a cien caracteres. (Actualmente <b><u id='direccion_remitente_contadormax'></u></b> caracteres) </div>	
				</td>	
			</tr>
			<tr>	
				<td class="descripcion">
					Telefono Remitente del Radicado:
				</td>
				<td class="detalle" colspan="5">
					<input type="search" id="telefono_remitente" name="telefono_remitente"  placeholder="Digite Teléfono del remitente (Si tiene extensión también)" title="Digite Teléfono del remitente (Si tiene extensión también)" onblur="validar_input('telefono_remitente');validar_campos_formulario_radicacion_entrada()">
					<div id="sugerencias_telefono"></div>

					<div id="telefono_remitente_max" class="errores">El telefono del contacto no puede ser mayor a 50 caracteres. (Actualmente <b><u id='telefono_remitente_contadormax'></u></b> caracteres)</div>
					<div id="telefono_remitente_min" class="errores">El telefono del contacto no puede ser menor a 6 caracteres.</div>
				</td>				
				<td class="descripcion">
					Mail del Remitente Radicado:
				</td>
				<td class="detalle" colspan="3">
					<input type="email" name="mail_remitente" id="mail_remitente" placeholder="Ingrese el correo electrónico del remitente" title="Ingrese el correo electrónico del remitente" onblur="validar_input('mail_remitente'); validar_campos_formulario_radicacion_entrada()">
					<div id="sugerencias_mail"></div>

					<div id="mail_remitente_max" class="errores">El mail del usuario no puede ser mayor a 100 caracteres. (Actualmente <b><u id='mail_remitente_contadormax'></u></b> caracteres)</div>
					<div id="mail_remitente_formato_mail" class="errores">
						El mail ingresado no tiene formato correcto (usuario@algunmail.com) por lo que no se puede crear.
					</div>
					<div id="mail_respuesta_obligatorio" class="errores">
						El medio de respuesta solicitado es <b style='font-size:20px;'>Correo Electronico</b> por lo que este campo es obligatorio para saber donde se va a enviar la respuesta.
					</div>
				</td>
			</tr>
			<tr>
				<td class="descripcion detalle_ancho" >
					Asunto:
				</td>
				<td class="detalle" colspan="9" >
					<textarea name="asunto_radicado" id="asunto_radicado" rows="2" style="width:98%;padding:5px;" placeholder="Ingrese el asunto del radicado. Sea lo más específico posible" title="Ingrese el asunto del radicado. Sea lo más específico posible" onblur="validar_input('asunto_radicado');validar_campos_formulario_radicacion_entrada()"></textarea>
					<div id="asunto_radicado_max" class="errores">El asunto no puede ser mayor a 500 caracteres (numeros o letras). (Actualmente <b><u id='asunto_radicado_contadormax'></u></b> caracteres)</div>
					<div id="asunto_radicado_min" class="errores">El asunto no puede ser menor a 6 caracteres (numeros o letras) </div>
					<div id="asunto_radicado_null" class="errores">El asunto es obligatorio</div>
				</td>
			</tr>
		
			<tr>
				<td></td>
				<td class="descripcion">	
					Dependencia Destino:
				</td>
				<td class="detalle" colspan="5">
					<input type="hidden" id="tipo_formulario" placeholder="tipo_formulario" value="radicacion_entrada_normal"><!-- Tipo de formulario (radicacion_rapida por defecto). -->
					<input type="hidden" id="codigo_contacto" placeholder="codigo_contacto" value="1"><!-- Tipo de formulario (radicacion_rapida por defecto). -->
					<input type="hidden" id="codigo_dependencia" name="codigo_dependencia" placeholder="codigo_dependencia"><!-- Tipo de radicacion de entrada (2) por defecto. -->
					<input type="hidden" id="nombre_dependencia_destino" placeholder="nombre_dependencia_destino"><!-- Nombre completo de la dependencia destino para usarla en el sticker. -->
					<input type="hidden" id="codigo_contacto" name="codigo_contacto" placeholder="codigo_contacto" value="1"><!-- Codigo Contacto. (1) por defecto. -->
					<input type="hidden" name="usuario_destino" id="usuario_destino" name="usuario_destino" placeholder="usuario_destino"> <!-- Usuario del sistema con perfil "Distribuidor de dependencia" que va a recibir el radicado -->
		 			
		 			<input type="search" name ="search_dependencia_destino" id="search_dependencia_destino" placeholder="Ingrese la dependencia a la que va a ser asignado este documento" title="Ingrese la dependencia a la que va a ser asignado este documento" onblur="validar_input('search_dependencia_destino');validar_campos_formulario_radicacion_entrada()"><br>

					<div id="sugerencias_remitente" style="display:none"></div>
			
					<div id="sugerencias_dependencia_destino"></div>
					<div id="search_dependencia_destino_min" class="errores">La dependencia de destino no puede ser menor a 6 caracteres (numeros o letras)</div>
					<div id="search_dependencia_destino_max" class="errores">La búsqueda puede tener máximo 100 caracteres.</div>
					<div id='sin_distribuidor' class='errores'> 
						En la dependencia seleccionada no existe un usuario con el perfil 
						<b>	'DISTRIBUIDOR_DEPENDENCIA' </b> 
						por lo que no se puede radicar a esta dependencia.<br> Comuniquese con el administrador del sistema.
					</div>
				</td>			
			</tr>
			
		</table>
		</center>
	</form>
	<center id='div_boton_enviar' style="display: none;">
		<input type="button" value="Radicar entrada" id="radicar_entrada" onclick="radicar_documento_entrada()" class="botones">
	</center>				
</body>
<script>$("#dignatario_remitente").focus();</script>
</html>			