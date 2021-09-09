<?php 
require_once("../login/validar_inactividad.php");
 ?><html>
<head>
	<meta charset="UTF-8">
	<title>Buscador General</title>
    <script src="include/js/sweetalert2.js"></script>
    <link rel="stylesheet" type="text/css" href="include/css/sweetalert2.css">  
	<script type="text/javascript" src="include/js/funciones_buscador_general.js"></script>
	<script type="text/javascript" src="include/js/funciones_prestamos.js"></script>
	<link rel="stylesheet" href="include/css/estilos_buscador_general.css">
	<link rel="stylesheet" href="include/css/estilos_bandejas.css">
</head>
<body>
<!-- 	<div class="contenido">		
		<div class="center" id="logo">	 -->
			<script type="text/javascript">$("#search_buscador").focus();</script>
			<div id="contenedor">
			<h1 class="center">Módulo Buscador General</h1>
			<br>	
				<input type='hidden' name='radicado' id='radicado' value='0'>
				<input type='hidden' name='expediente' id='expediente' value='0'>

				<table border="0"  style="width: 95%;">
					<tr>
						<td><center><span id="titulo_buscador">Buscar por número del <b>radicado</b>, por numero de Guia-Oficio o por el asunto del <b>radicado</b></span><hr>
							<input type="search" name="search_buscador" id="search_buscador" onkeyup="if (event.keyCode==13){buscador_general(); return false;}">
							</center>
						</td>
					</tr>
					<tr>
						<td>
							<center>
							<label class="container">Radicado
							  <input type="checkbox" checked="checked" id="por_radicado" onchange="valida_checkbox()">
							  <input type="hidden" name="por_rad" id="por_rad" value="SI">
							  <span class="checkmark"></span>
							</label>
							<label class="container" onchange="valida_checkbox()">Expediente
							  <input type="checkbox" id="por_expediente">
							  <input type="hidden" name="por_exp" id="por_exp" value="NO">
							  <span class="checkmark"></span>
							</label>
							<label class="container" onchange="valida_checkbox()">Inventario
							  <input type="checkbox" id="por_inventario">
							  <input type="hidden" name="por_inv" id="por_inv" value="NO">
							  <span class="checkmark"></span>
							</label>
							</center>
						</td>
					</tr>
					<div id="error_campos_vacio" class="error">La consulta debe tener por lo menos un parámetro de búsqueda.</div>
					<div id="valida_minimo_busqueda" class="error">La consulta no puede ser menor a 6 caracteres.</div>
					<div id="valida_maximo_busqueda" class="error">La consulta no puede ser mayor a 50 caracteres.</div>

					<tr id="boton_buscador_general" style="display: none">
						<td colspan="6">
							<center>
								<input type="button" name="buscar" value="Buscar" class='boton' onclick="buscador_general()">
							</center>
						</td>
					</tr>
				</table>	
			<div id="resultados"></div>
			<div id="resultados2"></div>
			<div id="resultados3"></div>
			</div>
			<div id="volver_resultados" style="display:none;">
				<input type='button' name='buscar' value='Volver a buscar' class='boton' onclick='volver_busqueda()'>
			</div>
			<div id="barra_herramientas">
				<img id='archivar_radicado' class="img1" src="imagenes/iconos/archivar.png" title="Archivar documentos en carpeta personal" onclick="validar_transaccion('archivar_radicado','lista_carpetas_personales')">
				<!-- <img id='derivar_radicado' class="img1" src="imagenes/iconos/derivar.png" title="Derivar documento a otros usuarios para gestionar la respuesta" onclick="validar_transaccion('derivar_radicado','lista_usuarios_derivado')"> -->
				<img id='informar_radicado' class="img1" src="imagenes/iconos/informar.png" title="Archivar documentos en carpeta personal" onclick="validar_transaccion('informar_radicado','lista_usuarios_informados')">
				<img id='reasignar_radicado' class="img1" src="imagenes/iconos/reasignar.png" title="Reasignar documento a otro usuario" onclick="validar_transaccion('reasignar_radicado','lista_usuarios_radicado')">
				<!-- <img id='responder_radicado' class="img1" src="imagenes/iconos/responder.png" title="Responder documento" onclick="validar_transaccion('responder_radicado',<?php // print_r("'$radicado'") ?>)"> -->
			</div>

			<div id="resultados4"></div>

	<!-- div que contiene ventana modal para archivar en carpeta personal el radicado -->	
	<div id="ventana_archivar_radicado" class='ventana_modal'>
		<div class="form">
			<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
			<h1>Formulario Archivar Radicado en Carpeta Personal</h1>
			<hr>
			<!-- <form action="bandejas/entrada/transacciones_radicado.php" method="post" id ="formulario_enviar_radicado" name ="formulario_enviar_radicado" > -->
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
			<!-- </form> -->
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
					<input type="hidden" name ="transaccion" id="transaccion" value="enviar_radicado" placeholder="transaccion"><!-- Tipo de transaccion para transacciones_radicado.php -->
					<input type="hidden" name ="codigo_carpeta" id="codigo_carpeta" value="entrada" placeholder="codigo_carpeta"><!-- codigo_carpeta para transacciones_radicado.php -->
					<input type="hidden" name ="usuario_actual" id="usuario_actual" value="" placeholder="usuario_actual"><!-- nombre del usuario actual para transacciones_radicado.php -->
					<input type="hidden" name ="usuarios_para_agregar" id="usuarios_para_agregar" value="" placeholder="usuarios_para_agregar"><!-- nombre de los usuarios para agregar -->
					<input type="hidden" name ="numero_radicado" id="numero_radicado" value="" placeholder="numero_radicado"><!-- codigo_carpeta para transacciones_radicado.php -->
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
	<div id="ventana_derivar_radicado" class='ventana_modal'>
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
					<input type="text" name ="usuario_actual_radicado" id="usuario_actual_radicado" value="" placeholder="usuario_actual_radicado">
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
	</div>
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
</body>

</html>