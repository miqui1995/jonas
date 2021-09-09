<?php 
	require_once("../../login/validar_inactividad.php");
	$carpeta 	= $_POST['carpeta'];
	$radicado  	= $_POST['radicado'];

	switch($carpeta){
        case 'entrada':
            $titulo_barra_herramientas = "Bandeja de Entrada";
        break;

        case 'Salida':
            $titulo_barra_herramientas = "Bandeja de Enviados";
        break;

        default:
        	$query_bandeja_personal  	= "select * from carpetas_personales where id='$carpeta'";
        	$fila_bandeja_personal 		= pg_query($conectado,$query_bandeja_personal);
        	$linea_bandeja_personal 	= pg_fetch_array($fila_bandeja_personal);
        	$nombre_carpeta_personal 	= $linea_bandeja_personal['nombre_carpeta_personal'];

            $titulo_barra_herramientas 	= "Carpeta Personal ($nombre_carpeta_personal)";
        break;
    }
?>
<html>
<head>
	<meta charset="UTF-8">
	<title>Bandeja Principal</title>
	<script type="text/javascript" src="include/js/funciones_bandejas.js"></script>
	<link rel="stylesheet" href="include/css/estilos_bandejas.css">
	<script>
	function valida_archivar_radicado(){
		var carpeta_personal 		= $("#listado_carpetas_personales").val();
		var radicado 				= $("#radicado").val();
		var usuario_codigo_carpeta1 = $("#usuario_actual_codigo_carpeta1").val();
		
		loading('boton_archivar_radicado');
		$.ajax({    // Guardo registro de ingreso al sistema para auditoria
            type: 'POST',
            url: 'include/procesar_ajax.php',
            data: {
            	'recibe_ajax' 				: 'archivar_radicado',
				'carpeta_personal' 			: carpeta_personal,
	            'radicado' 					: radicado,
				'usuario_codigo_carpeta1' 	: usuario_codigo_carpeta1
            },          
            success: function(respuesta){
                $('#resultado_js').html(respuesta);
            }
        })
	}
	</script>
</head>
<body>
	<input type='hidden' name='offset' id='offset' value='0'>
	<input type='hidden' name='radicado' id='radicado' value='0'>
	<input type='hidden' name='expediente' id='expediente' value='0'>
	<input type='hidden' name='lista_nombre_expedientes' id='lista_nombre_expedientes'> <!-- input que se usa en formularios como "No_requiere_respuesta, etc" -->
	<input type='hidden' name='carpeta_personal' id='carpeta_personal' value= <?php print_r("$carpeta") ?>>
	<input type='hidden' id='radicado_recibido' value= <?php print_r("$radicado") ?>>
	<div id="barra_herramientas"><?php echo "<center><h2 id='cuerpo_titulo_barra_herramientas' class='titulo_bh'>$titulo_barra_herramientas del usuario $nombre_completo ($login)</h2></center>"; ?>
		<img id='archivar_radicado' class="img1" src="imagenes/iconos/archivar.png" title="Archivar documentos en carpeta personal" onclick="validar_transaccion('archivar_radicado','lista_carpetas_personales')">
<!-- 		<img id='derivar_radicado' class="img1" src="imagenes/iconos/derivar.png" title="Derivar documento a otros usuarios para gestionar la respuesta" onclick="validar_transaccion('derivar_radicado','lista_usuarios_derivado')"> -->
		<img id='informar_radicado' class="img1" src="imagenes/iconos/informar.png" title="Archivar documentos en carpeta personal" onclick="validar_transaccion('informar_radicado','lista_usuarios_informados')">
		<img id='reasignar_radicado' class="img1" src="imagenes/iconos/reasignar.png" title="Reasignar documento a otro usuario" onclick="validar_transaccion('reasignar_radicado','lista_usuarios_radicado')">
		<!-- <img id='responder_radicado' class="img1" src="imagenes/iconos/responder.png" title="Responder documento" onclick="validar_transaccion('responder_radicado',<?php // print_r("'$radicado'") ?>)"> -->
	</div>
	<div id="lista_radicados">
		<script>cambia_checkbox_todos('entrada')</script>
	</div>

<!-- div que contiene ventana modal para archivar en carpeta personal el radicado -->	
	<div id="ventana_archivar_radicado" class='ventana_modal'>
		<div class="form">
			<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
			<h1>Formulario Archivar Radicado en Carpeta Personal</h1>
			<hr>
				<table border ="0">
					<tr>
						<td class="descripcion">Carpetas personales del usuario</td>
						<td class="detalle" id="lista_carpetas_personales">
						</td>
					</tr>
					<tr>
						<input type="hidden" name ="usuario_actual_codigo_carpeta1" id="usuario_actual_codigo_carpeta1" value="" placeholder="usuario_actual"><!-- nombre del usuario actual para transacciones_radicado.php -->
					</tr>
					
					<tr>
						<td colspan="2" id="boton_archivar_radicado">
							<center><input type="button" value="Archivar Radicado" class="botones" onclick="valida_archivar_radicado()"><center>
						</td>
					</tr>
				</table>
		</div>
	</div>
<!-- Hasta aqui el div que contiene ventana modal para archivar en carpeta personal el radicado-->
<!-- div que contiene ventana modal para reasignar radicado -->	
	<div id="ventana_envio_radicado" class='ventana_modal'>
		<div class="form">
			<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
			<h1>Formulario Reasignar Radicado</h1>
			<hr>
			<table border ="0">
				<tr>
					<td class="descripcion" width="330px;">
						<span style="float: left;">Radicado va a ser asignado a : </span>
					</td>
					<td class="detalle">
						<span id="foto_usuario_destino" style="float: left;"></span>
						<div id="lista_usuarios_radicado" style="float: left;" title="Listado ordenado por nombre de dependencia"></div>
						<div class="errores" id='error_usuario_destino' style="float: left;">Debe seleccionar un usuario para reasignar el radicado</div>
					</td>
				</tr>
				<tr>
					<!-- Tipo de transaccion para transacciones_radicado.php -->
					<input type="hidden" name ="transaccion" id="transaccion" value="enviar_radicado" placeholder="transaccion">

					<!-- codigo_carpeta para transacciones_radicado.php -->
					<input type="hidden" name ="codigo_carpeta" id="codigo_carpeta" value="entrada" placeholder="codigo_carpeta">
					
					<!-- nombre del usuario actual para transacciones_radicado.php -->
					<input type="hidden" name ="usuario_actual" id="usuario_actual" value="" placeholder="usuario_actual">

					<!-- nombre de los usuarios para agregar -->
					<input type="hidden" name ="usuarios_para_agregar" id="usuarios_para_agregar" value="" placeholder="usuarios_para_agregar">

					<input type="hidden" name ="numero_radicado" id="numero_radicado" value="" placeholder="numero_radicado">
					<input type="hidden" name ="usuario_actual_radicado" id="usuario_actual_radicado" value="" placeholder="usuario_actual_radicado">
				</tr>
				<tr>
					<td class="descripcion">Mensaje para reasignar :</td>
					<td class="detalle" colspan="3">
						<textarea name="mensaje_reasignar" id="mensaje_reasignar" rows="2" style="width:98%;padding:5px;" placeholder="Ingrese el mensaje para reasignar el radicado. Sea lo más específico posible" title="Ingrese el mensaje para reasignar el radicado. Sea lo más específico posible" ></textarea>
						<div id="mensaje_reasignar_null" class="errores">El mensaje de reasignación es obligatorio</div>
						<div id="mensaje_reasignar_min" class="errores">El mensaje de reasignación no puede ser menor a 6 caracteres (numeros o letras) </div>
						<div id="mensaje_reasignar_max" class="errores">El mensaje de reasignación no puede ser mayor a 500 caracteres. (Actualmente <b><u id='mensaje_reasignar_contadormax'></u></b> caracteres)</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" id="boton_enviar_radicado">
						<center><input type="button" value="Reasignar" class="botones" onclick="valida_reasignar_radicado()"><center>
					</td>
				</tr>
			</table>
		</div>
	</div>
<!-- Hasta aqui el div que contiene ventana modal para reasignar radicado-->
<!-- div que contiene ventana modal para derivar radicado -->	
<!-- 	<div id="ventana_derivar_radicado" class='ventana_modal'>
		<div class="form">
			<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
			<h1>Formulario Derivar Radicado</h1>
			<hr>
			<table border ="0">
				<tr>
					<td class="descripcion" width="300px;">Radicado va a ser derivado a :</td>
					<td class="detalle" id="lista_usuarios_derivado"></td>
				</tr>
				<tr>
					<input type="hidden" name ="transaccion" id="transaccion" value="derivar_radicado" placeholder="transaccion">
					<input type="text" name ="codigo_carpeta_derivar" id="codigo_carpeta_derivar" value="entrada" placeholder="codigo_carpeta">
					<input type="text" name ="usuario_actual_derivado" id="usuario_actual_derivado" value="" placeholder="usuario_actual_derivado">
					<input type="text" name ="usuarios_para_agregar_derivar" id="usuarios_para_agregar_derivar" value="" placeholder="usuarios_para_agregar_derivar">
				</tr>
				<tr>
					<td class="descripcion">Mensaje para derivar :</td>
					<td class="detalle" colspan="3">
						<textarea name="mensaje_derivar" id="mensaje_derivar" rows="2" style="width:100%;padding:5px;" placeholder="Ingrese el mensaje para derivar el radicado. Sea lo más específico posible" title="Ingrese el mensaje para derivar el radicado. Sea lo más específico posible" ></textarea>
						<div id="mensaje_derivar_null" class="errores">El mensaje de derivación es obligatorio</div>
						<div id="mensaje_derivar_min" class="errores">El mensaje de derivación no puede ser menor a 6 caracteres (numeros o letras) </div>
						<div id="mensaje_derivar_max" class="errores">El mensaje de derivación no puede ser mayor a 500 caracteres. (Actualmente <b><u id='mensaje_derivar_contadormax'></u></b> caracteres)</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" id="boton_enviar_derivado">
						<center><input type="button" value="Derivar Documento" class="botones" onclick="valida_derivar_radicado()"><center>
					</td>
				</tr>
			</table>
		</div>
	</div> -->
<!-- Hasta aqui el div que contiene ventana modal para derivar radicado-->
<!-- div que contiene ventana modal para derivar radicado -->	
	<div id="ventana_informar_radicado" class='ventana_modal'>
		<div class="form">
			<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
			<h1>Formulario Informar Radicado</h1>
			<hr>
			<table border ="0">
				<tr>
					<td class="descripcion" width="300px;">Radicado va a ser informado a :</td>
					<td class="detalle" id="lista_usuarios_informados"></td>
				</tr>
				<tr>
					<input type="hidden" name ="usuario_actual_informado" id="usuario_actual_informado" value="" placeholder="usuario_actual_informado">
					<input type="hidden" name ="usuarios_para_agregar_informar" id="usuarios_para_agregar_informar" value="" placeholder="usuarios_para_agregar_informar">
					<input type="hidden" name ="usuarios_nuevos_informar" id="usuarios_nuevos_informar" value="" placeholder="usuarios_nuevos_informar">

					<!-- <input type="hidden" name ="transaccion" id="transaccion" value="derivar_radicado" placeholder="transaccion"> -->
				</tr>
				<tr>
					<td class="descripcion">Mensaje para informar :</td>
					<td class="detalle" colspan="3">
						<textarea name="mensaje_informar" id="mensaje_informar" rows="2" style="width:100%;padding:5px;" placeholder="Ingrese el mensaje para informar el radicado. Sea lo más específico posible" title="Ingrese el mensaje para informar el radicado. Sea lo más específico posible" ></textarea>
						<div id="mensaje_informar_null" class="errores">El mensaje de informar es obligatorio</div>
						<div id="mensaje_informar_min" class="errores">El mensaje de informar no puede ser menor a 6 caracteres (numeros o letras) </div>
						<div id="mensaje_informar_max" class="errores">El mensaje de informar no puede ser mayor a 500 caracteres. (Actualmente <b><u id='mensaje_informar_contadormax'></u></b> caracteres)</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" id="boton_enviar_informado">
						<center><input type="button" value="Informar Documento" class="botones" onclick="valida_informar_radicado()"><center>
					</td>
				</tr>
			</table>
		</div>
	</div>
<!-- Hasta aqui el div que contiene ventana modal para derivar radicado-->
<?php echo "<script>mostrar_radicado('$radicado');</script>"; ?>
</body>
</html>
