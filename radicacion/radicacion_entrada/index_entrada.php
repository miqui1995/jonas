<?php 
	require_once("../../login/validar_inactividad.php");
	$login_usuario = $_SESSION['login'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Buscador de Remitente</title>
	<script type="text/javascript" src="include/js/funciones_radicacion_entrada.js"></script>
	<link rel="stylesheet" href="include/css/estilos_radicacion_entrada.css">
</head>
<body>
<!--Desde aqui el div que contiene el formulario para agregar contacto-->
	<form enctype="multipart/form-data" id ="formulario_radicacion_rapida" autocomplete="off" class="form">

	<!-- La variable $codigo_entidad la hereda desde el archivo incluido como require_once("../../login/validar_inactividad.php");-->
		<input type="hidden" name="codigo_entidad" id="codigo_entidad" <?php echo "value='$codigo_entidad'"; ?> >
		<input type="hidden" name="login_usuario" id="login_usuario" <?php echo "value='$login_usuario'"; ?> >

		<div class="center" id="logo">
			<br>
			<h1 style="margin-top:-10px;">Módulo de radicación de entrada</h1><hr><br>	
			<h3>Al generar este número se guardará en la Base de Datos del Sistema. 
				Este número es OFICIAL y <b style="color:red"><u>NO SE PUEDE ELIMINAR</u></b> <br>
				Si no está seguro de la operación, por favor no la realice.
			</h3><br>	
		</div>
		<div class="form" align="center">
			<table id="table" border="0">
				<tr>	
					<td class='descripcion' style="width: 30%">Tipo de Documento a radicar</td>
					<td class="detalle">
						<div style='float: left;' id="td" title="Del tipo de documento depende el tiempo de trámite asignado"></div> 

						<div id="div_dias_tramite" style="display: none;">
							<input type="text" id="dias_tramite" name="dias_tramite" value="15" style="float: left;" onblur="ocultar_dias_tramite(1)" onkeyup="espacios_formulario('dias_tramite', 'sin_caracteres');">
						</div>
						<div id="error_dias_tramite" class="errores" style="float: left;">El valor ingresado debe ser un número</div>
						<div id="dias_tramite_max" class="errores" style="float: left;">No puede tener mas de 30 dias de trámite</div>
						<div id="dias_tramite_null" class="errores" style="float: left;">Este campo es obligatorio</div>
						<div id="dias_tramite_cero" class="errores" style="float: left;">El valor no puede ser CERO</div>
						
						<div class="detalle" id="termino_td" align="left"  max-width="50px" onclick="mostrar_dias_tramite()">
							<b>15 dias habiles de tramite</b>
						</div>
					</td>
				</tr>
				<tr>
					<td width="30%"  class="descripcion">
						Radicado para la Dependencia :
					</td>
					<td width="70%" class="detalle">
						<input type="hidden" id="tipo_formulario" placeholder="tipo_formulario" value="radicacion_rapida"><!-- Tipo de formulario (radicacion_rapida por defecto). -->
						<input type="hidden" id="tipo_radicacion" placeholder="tipo_radicacion" value="1"><!-- Tipo de radicacion de entrada (1) por defecto. -->
						<input type="hidden" id="codigo_dependencia" placeholder="codigo_dependencia" value="">
						<input type="hidden" id="nombre_dependencia_destino" placeholder="nombre_dependencia_destino" value=""><!-- Nombre completo de la dependencia destino para usarla en el sticker. -->
						<input type="hidden" name="usuario_destino" id="usuario_destino" placeholder="usuario_destino"> <!-- Usuario del sistema con perfil "Distribuidor de dependencia" que va a recibir el radicado -->
			 			<input type="text" name ="search_dependencia_destino" id="search_dependencia_destino" placeholder="Ingrese la dependencia a la que va a ser asignado este documento" title="Ingrese la dependencia a la que va a ser asignado este documento" autocomplete="off"><br>
						<div id="sugerencias_remitente"></div>
					</td>
				</tr>
				<tr>
					<td class='descripcion'>
						Anexos al radicado : 
					</td>
					<td class='detalle'>
						<input type="text" name ="descripcion_anexos" id="descripcion_anexos"  placeholder="El radicado viene con anexo CD, AZ, USB, Caja, etc." title="El radicado viene con anexo CD, AZ, USB, Caja, etc.">
						<div id="descripcion_anexos_min" class="errores">La descripción de los anexos no puede tener menos de 3 caracteres</div>
						<div id="descripcion_anexos_max" class="errores">La descripción de los anexos no puede tener mas de 100 caracteres. (Actualmente <b><u id='descripcion_anexos_contadormax'></u></b> caracteres)</div>
					</td>
				</tr>
			</table>
		</div>
		<center>			
			<div id="sugerencias_dependencia_destino"></div>
			<div id='sin_distribuidor' class='errores'> 
				En la dependencia seleccionada no existe un usuario con el perfil 
				<b>	'DISTRIBUIDOR_DEPENDENCIA' </b> 
				por lo que no se puede radicar a esta dependencia.<br> Comuniquese con el administrador del sistema.
			</div>
			<br>
			<div id="div_boton_enviar" style="display:none; width: 40vw; ">
				<input type="button" value="Radicar Documento" onclick="submit_grabar_radicacion_rapida(); loading('div_boton_enviar')" class="botones">
			</div>
		</center>
	</form>
	<script type="text/javascript">
		desplegable_terminos();
		$("#search_dependencia_destino").focus();
	</script>
</body>
</html>