<?php 
	require_once("../login/validar_inactividad.php");
?>
<!DOCTYPE html>
<html > 
<head>
	<meta charset="UTF-8">
	<title>Buscador de Usuarios</title>
	<script type="text/javascript" src="include/js/funciones_usuarios.js"></script>
	<link rel="stylesheet" href="include/css/estilos_usuarios.css">
</head>
<body>
<!--Desde aqui el div que contiene el formulario para agregar usuarios-->
		<div id="ventana">
			<div class="formulario">
				<div class="cerrar"><a href='javascript:cerrarVentanaCrearUsuarios();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nuevo Usuario</h1>
				<hr>
				<form method="post" id="formulario" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" name="formulario_agregar_usuario" id="formulario_agregar_usuario" value="crear_usuario">
					<!-- <input type="hidden" name="MAX_FILE_SIZE" value="3000000" /> -->
					<table class='tabla_datos_usuario'>
						<tr>
							<td class="descripcion" width="10%">
								Nombre Completo:
							</td>
							<td class="detalle"  colspan="3">
								<input type="search" name="nombre_completo" id="nombre_completo" placeholder="Ingrese Nombres y Apellidos completos (sin numeros)" title='Ingrese Nombres y Apellidos completos (sin numeros)' onblur="validar_nombre();validar_input('nombre_completo')">
								<div id="sugerencias_nombre_completo"></div>

								<div id="nombre_completo_max" class="errores">El nombre completo del usuario no puede ser mayor a 100 caracteres. (Actualmente <b><u id='nombre_completo_contadormax'></u></b> caracteres)</div>
								<div id="nombre_completo_min" class="errores">El nombre  del usuario (con apellido) no puede ser menor a 6 caracteres (sin numeros)</div>
								<div id="nombre_completo_null" class="errores">El nombre completo del usuario es obligatorio.</div>

								<div id="error_nombre_completo_ya_existe" class="errores">
									El nombre ingresado corresponde a un usuario que ya existe por lo que no se puede crear.
								</div>
							</td>
							<td class="descripcion" width="10%">
								Nombre de Usuario (Login):
							</td>
							<td class="detalle" colspan="3" width="20%">
								<input type="search" name="login" id="login" placeholder="Ingrese Login del usuario" title="Ingrese login del usuario" onblur="validar_login();validar_input('login')">
								<div id="sugerencias_login"></div>

								<div id="login_max" class="errores">El Login del usuario no puede ser mayor a 50 caracteres. (Actualmente <b><u id='login_contadormax'></u></b> caracteres)</div>
								<div id="login_min" class="errores">El Login del usuario no puede ser menor a 3 caracteres</div>
								<div id="login_null" class="errores">El Login del usuario es obligatorio.</div>
								<div id="error_login_ya_existe" class="errores">
									El Login ingresado corresponde a un usuario que ya existe por lo que no se puede crear.
								</div>
							</td>
							<td class="descripcion" rowspan="2">Foto del Usuario:</td>
							<td class="detalle"  rowspan="2">
								<input type="file" name="imagen" id="imagen" onchange="valida_tipo_imagen('imagen','viewer','error_imagen','error_imagen_invalida','imagen')">
								<div id="error_imagen" class="errores">La imagen del usuario es obligatoria</div>
								<div id="error_imagen_invalida" class="errores"> El formato de la imagen que va a ingresar no es válido. El sistema solo admite formato PNG, GIF, JPG y JPEG</div>
								<iframe id="viewer" frameborder="0" scrolling="yes" width="100%" height="150px" style='display: none;'></iframe> 
							</td>
						</tr>
						<tr>	
							<td class="descripcion">
								Numero Identificacion:
							</td>
							<td class="detalle" colspan="3" width="20%">
								<input type="search" name="identificacion" id="identificacion"
								 placeholder="Ingrese Numero Identificacion" title='Ingrese Numero de Identificacion' onblur="validar_identificacion();validar_input('identificacion')">
								<div id="sugerencias_identificacion"></div>

								<div id="error_identificacion" class="errores">En este campo solo puede ingresar numeros (sin puntos ni guiones).</div>
								<div id="identificacion_max" class="errores">El numero de identificación no puede ser mayor a 20 caracteres (Actualmente <b><u id='identificacion_contadormax'></u></b> caracteres)</div>
								<div id="identificacion_min" class="errores">El numero de identificación no puede ser menor a 6 caracteres (numeros sin puntos.)</div>
								<div id="identificacion_null" class="errores">El número de identificación es obligatorio.</div>

								<div id="identificacion_ya_existe" class="errores">
									El número de identificación ingresado corresponde a un usuario que ya existe por lo que no se puede crear.
								</div>
							</td>	
							<td class="descripcion">
								Mail Usuario:
							</td>
							<td class="detalle" colspan="3">
								<input type="email" name="mail" id="mail" placeholder="Ingrese email del usuario" onblur="validar_input('mail')" title='Ingrese email del usuario'>

								<div id="mail_null" class="errores">El mail del usuario es obligatorio.</div>
								<div id="mail_max" class="errores">El mail del usuario no puede ser mayor a 50 caracteres. (Actualmente <b><u id='mail_contadormax'></u></b> caracteres)</div>
								<div id="mail_formato_mail" class="errores">
									El mail ingresado no tiene formato correcto (usuario@algunmail.com) por lo que no se puede crear.
								</div>
							</td>
						</tr>		
						<tr>
							<td class="descripcion">Perfil:</td>
							<td class="detalle" colspan="3">
								<select name="perfil" id="perfil" class='select_opciones' onchange="valida_perfil()">
									<option value="DISTRIBUIDOR_DEPENDENCIA">Distribuidor de la Dependencia</option>
									<option value="JEFE_ARCHIVO">Jefe de Archivo</option>
									<option value="AUXILIAR_ARCHIVO">Auxiliar Archivo</option>
									<option value="USUARIO" selected="selected">Usuario</option>		
								</select>
									
								<div id="sugerencias_perfil"></div>
								<div id="error_perfil" class="errores">
									En la dependencia <strong id="depe_perfil"></strong> ya existe un usuario con el perfil <strong id="perfil_p"></strong> : <br> <strong id="user_perfil"></strong> 
								</div>
							</td>
							<td class="descripcion">
								Dependencia:
							</td>
							<td class="detalle" colspan="3">
								<input type="hidden" name="codigo_dependencia" id="codigo_dependencia">
								<input type="search" name="dependencia" id="dependencia" placeholder="Ingrese Dependencia del Usuario" onblur="validar_dependencia()">
								<div id="sugerencias_dependencia"></div>

								<div id="dependencia_null" class="errores">La dependencia del usuario es obligatoria.</div>
								<div id="error_dependencia_inexistente" class="errores">La dependencia ingresada no existe en la base de datos.</div>
								<div id="error_dependencia_invalida" class="errores">Por favor seleccione una dependencia válida.</div>
							</td>
							<td class="descripcion" width="15%" rowspan="2">Imagen de la firma del Usuario:</td>
							<td class="detalle" width="25%" rowspan="2">
								<input type="file" name="imagen_firma" id="imagen_firma" onchange="valida_tipo_imagen('imagen_firma','viewer2','no','error_imagen_invalida2','png')">
								<div id="error_imagen_invalida2" class="errores"> El formato de esta imagen que va a ingresar no es válido. Esta imagen debe ser en formato PNG</div>
								<iframe id="viewer2" frameborder="0" scrolling="yes" width="100%" height="150px" style='display: none;'>
								</iframe> 
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Estado:
							</td>
							<td class="detalle">	
								<select name="estado" id="estado" class='select_opciones'>
									<option value="ACTIVO">Activo</option>
									<option value="INACTIVO">Inactivo</option>
								</select>
								
								<label class="checkbox_opciones">
									<input class="checkbox_input" type="checkbox" name="estado1" id="estado1" onchange="console.log(this.checked)">
						      		<span class="checkbox_contenido" data-on="Activo" data-off="Inactivo"></span> 
						      		<span class="boton_movil"></span> 
							    </label>
							</td>
							<td class="descripcion">
								Contraseña Nueva:
							</td>
							<td class="detalle">	
								<select name="usuario_nuevo" id="usuario_nuevo" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</td>
							<td class="descripcion">								
								Nivel Seguridad:
							</td>
							<td class="detalle">
								<select name="nivel_seguridad" id="nivel_seguridad" class='select_opciones'>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
							</td>
							<td class="descripcion">Jefe Dependencia:</td>
							<td class="detalle">
								<select name="jefe_dependencia" id="jefe_dependencia" class='select_opciones' onchange="valida_jefe_dependencia()">
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
								<div id="error_jefe_dependencia" class="errores">
									En la dependencia <strong id="depe_jefe_dependencia"></strong> ya tiene asignado un Jefe : <br> <strong id="user_jefe_dependencia"></strong> 
								</div>
							</td>
						</tr>
					</table>
					<hr>
					<h2> Permisos del Usuario</h2>
					<hr>	
					<table border="0">
						<tr>	
							<td class="descripcion">Administrador del Sistema:</td>
							<td class="detalle">
								<select name="administrador_sistema" id="administrador_sistema" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo Creación - Modificación de expedientes:</td>
							<td class="detalle">
								<select name="creacion_expedientes" id="creacion_expedientes" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo Cuadro de Clasificación Documental (TRD):</td>
							<td class="detalle">
								<select name="cuadro_clasificacion" id="cuadro_clasificacion" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo de Inventario:</td>
							<td class="detalle">
								<select name="inventario" id="inventario" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo Modificación de Radicados:</td>
							<td class="detalle">
								<select name="modificar_radicado" id="modificar_radicado" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>	
						</tr>
						<tr>
							<td class="descripcion">Módulo Préstamo de documentos:</td>
							<td class="detalle">
								<select name="prestamo_documentos" id="prestamo_documentos" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo Scanner en puesto de Trabajo:</td>
							<td class="detalle">
								<select name="scanner" id="scanner" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>					
							<td class="descripcion">Módulo Ubicación Topográfica:</td>
							<td class="detalle">
								<select name="ubicacion_topografica" id="ubicacion_topografica" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Módulo Ventanilla de Radicacion de Entrada (1):</td>
							<td class="detalle">
								<select name="ventanilla_radicacion" id="ventanilla_radicacion" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Radicacion Salida (2):</td>
							<td class="detalle">
								<select name="radicacion_salida" id="radicacion_salida" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Radicacion Normal (3):</td>
							<td class="detalle">
								<select name="radicacion_normal" id="radicacion_normal" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Radicacion Interna (4):</td>
							<td class="detalle">
								<select name="radicacion_interna" id="radicacion_interna" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Radicacion Resoluciones (5):</td>
							<td class="detalle">
								<select name="radicacion_resoluciones" id="radicacion_resoluciones" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="12">
								<div id='boton_crear_usuario' class="center">
									<input type="button" id="bCrearUsuario" class="botones" value="Crear Usuario" onclick="submit_agregar_usuario()">
								</div>
							</td>
						</tr>
					</table>	
				</form>
			</div>   <!-- Cierra el div class ="formulario" -->		
		</div> 	<!-- Cierra el div id='ventana' -->
<!--Hasta aqui el div que contiene el formulario para agregar municipios-->
<!--**************************************************************************************************-->
<!--Desde aqui el div que contiene el formulario para modificar usuarios-->	
		<div id="ventana2">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrarVentanaModificarUsuarios();'>Cerrar X</a></div>
				<h1>Formulario Modificar Usuario</h1>
				<hr>
				<form enctype="multipart/form-data" autocomplete="off">		
					<input type="hidden" name="formulario_modificar_usuario" id="formulario_modificar_usuario" value="modificar_usuario">		
					<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
					<table>
						<tr>
							<td class="descripcion" width="10%">
								Nombre Completo:
							</td>
							<td class="detalle" colspan="3">
								<input type="hidden" name="ant_mod_nombre_completo" id="ant_mod_nombre_completo">
								<input type="search" name="mod_nombre_completo" id="mod_nombre_completo" placeholder="Ingrese Nombres y Apellidos completos (sin numeros)" title="Ingrese Nombres y Apellidos completos (sin numeros)" onblur="validar_modificar_usuario();validar_input('mod_nombre_completo')">

								<div id="sugerencias_mod_nombre_completo"></div>

								<div id="mod_nombre_completo_max" class="errores">El nombre completo del usuario no puede ser mayor a 100 caracteres (Actualmente <b><u id='mod_nombre_completo_contadormax'></u></b> caracteres)</div>
								<div id="mod_nombre_completo_min" class="errores">El nombre  del usuario (con apellido) no puede ser menor a 6 caracteres (sin numeros)</div>
								<div id="mod_nombre_completo_null" class="errores">El nombre completo del usuario es obligatorio.</div>

								<div id="error_mod_nombre_completo_ya_existe" class="errores">
									El nombre ingresado corresponde a un usuario que ya existe por lo que no se puede crear.
								</div>
							</td>
							<td class="descripcion" width="10%">
								Nombre de Usuario (Login):
							</td>
							<td class="detalle" colspan="3">
								<input type="hidden" name="ant_mod_login" id="ant_mod_login">
								<input type="search" name="mod_login" id="mod_login" placeholder="Ingrese Login del usuario" title="Ingrese Login del usuario" onblur="valida_mod_perfil();validar_input('mod_login')">
								<div id="sugerencias_mod_login"></div>

								<div id="error_mod_login_ya_existe" class="errores">
									El Login ingresado corresponde a un usuario que ya existe por lo que no se puede crear.
								</div>
								<div id="mod_login_max" class="errores">El Login del usuario no puede ser mayor a 50 caracteres. (Actualmente <b><u id='mod_login_contadormax'></u></b> caracteres)</div>
								<div id="mod_login_min" class="errores">El Login del usuario no puede ser menor a 6 caracteres (sin numeros)</div>
								<div id="mod_login_null" class="errores">El Login del usuario es obligatorio.</div>
							</td>			
							<td class="descripcion" rowspan="2">Foto del Usuario:</td>
							<td class="detalle" rowspan="2">
								<input type="file" name="imagen_mod" id="imagen_mod" onchange="valida_tipo_imagen('imagen_mod','viewer_mod','error_imagen_mod','error_imagen_invalida_mod','imagen')" >
								<div id="error_imagen_mod" class="errores">La imagen del usuario es obligatoria</div>
								<div id="error_imagen_invalida_mod" class="errores"> El formato de la imagen que va a ingresar no es válido. El sistema solo admite formato PNG, GIF, JPG y JPEG</div>
								<iframe id="viewer_mod" frameborder="0" scrolling="yes" width="100%" height="150px" style="display: none;"></iframe> 
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Numero Identificacion:
							</td>
							<td class="detalle" colspan="3">
								<input type="hidden" name="mod_id_usuario" id="mod_id_usuario">
								<input type="hidden" name="ant_mod_identificacion" id="ant_mod_identificacion">
								<input type="search" name="mod_identificacion" id="mod_identificacion"
								 placeholder="Ingrese Numero Identificacion" title='Ingrese Numero de Identificacion' onblur="validar_modificar_usuario();validar_input('mod_identificacion')">

								<div id="sugerencias_mod_identificacion"></div>

								<div id="error_mod_identificacion" class="errores">En este campo solo puede ingresar numeros (sin puntos ni guiones).</div>
								<div id="mod_identificacion_max" class="errores">El numero de identificación no puede ser mayor a 20 caracteres. (Actualmente <b><u id='mod_identificacion_contadormax'></u></b> caracteres)</div>
								<div id="mod_identificacion_min" class="errores">El numero de identificación no puede ser menor a 6 caracteres (numeros sin puntos.)</div>
								<div id="mod_identificacion_null" class="errores">El número de identificación es obligatorio.</div>

								<div id="mod_identificacion_ya_existe" class="errores">
									El número de identificación ingresado corresponde a un usuario que ya existe por lo que no se puede crear.
								</div>
							</td>
							<td class="descripcion">
								Mail Usuario:
							</td>
							<td class="detalle" colspan="3">
								<input type="email" name="mod_mail" id="mod_mail" placeholder="Ingrese Mail del Usuario" onblur="validar_input('mod_mail')">

								<div id="mod_mail_max" class="errores">El mail del usuario no puede ser mayor a 50 caracteres. (Actualmente <b><u id='mod_mail_contadormax'></u></b> caracteres)</div>
								<div id="mod_mail_min" class="errores">El mail del usuario no puede ser menor a 6 caracteres.</div>
								<div id="mod_mail_null" class="errores">El mail del usuario es obligatorio.</div>
								<div id="mod_mail_formato_mail" class="errores">
									El mail ingresado no tiene formato correcto (usuario@algunmail.com) por lo que no se puede crear.
								</div>
							</td>
						</tr>
						<tr>											
							<td class="descripcion">Perfil: </td>
							<td class="detalle" colspan="3">							
								<select name="mod_perfil" id="mod_perfil" class='select_opciones' onchange="valida_mod_perfil()">
									<option value="DISTRIBUIDOR_DEPENDENCIA">Distribuidor de la Dependencia</option>
									<option value="JEFE_ARCHIVO">Jefe de Archivo</option>
									<option value="AUXILIAR_ARCHIVO">Auxiliar Archivo</option>
									<option value="USUARIO" selected="selected">Usuario</option>		
								</select>

								<div id="sugerencias_mod_perfil"></div>
								<div id="error_mod_perfil" class="errores">
									En la dependencia <strong id="depe_mod_perfil"></strong> ya existe un usuario con el perfil <strong id="mod_perfil_p"></strong> : <br> <strong id="user_mod_perfil"></strong> 
								</div>
							</td>
							<td class="descripcion">
								Dependencia:
							</td>
							<td class="detalle" colspan="3">
								<input type="hidden" name="mod_codigo_dependencia" id="mod_codigo_dependencia">
								<input type="hidden" name="mod_ant_mod_nom_depe" id="mod_ant_mod_nom_depe">
								<input type="search" name="mod_nombre_dependencia" id="mod_nombre_dependencia" placeholder="Ingrese Dependencia del Usuario" onblur="validar_input('mod_nombre_dependencia')">
								<div id="sugerencias_mod_dependencia"></div>

								<div id="mod_nombre_dependencia_max" class="errores">La dependencia del usuario no puede ser mayor a 50 caracteres. (Actualmente <b><u id='mod_nombre_dependencia_contadormax'></u></b> caracteres)</div>
								<div id="mod_nombre_dependencia_min" class="errores">La dependencia del usuario no puede ser menor a 6 caracteres.</div>
								<div id="mod_nombre_dependencia_null" class="errores">La dependencia del usuario es obligatoria.</div>

								<div id="error_mod_dependencia_inexistente" class="errores">La dependencia ingresada no existe en la base de datos.</div>
								<div id="error_mod_dependencia_invalida" class="errores">Por favor seleccione una dependencia válida.</div>

								<!-- Div para mostrar el error de un usuario con radicados pendientes -->
								<div id="error_usuario_radicados_pendientes" class="errores center"></div>
							</td>
							<td class="descripcion" width="15%" rowspan="2">Imagen de la firma del Usuario:</td>
							<td class="detalle" width="25%" rowspan="2">
								<input type="file" name="imagen_firma_mod" id="imagen_firma_mod" onchange="valida_tipo_imagen('imagen_firma_mod','viewer_mod2','no','error_imagen_invalida_mod2','png')">
								<div id="error_imagen_invalida_mod2" class="errores"> El formato de la imagen que va a ingresar no es válido. El sistema solo admite formato PNG, GIF, JPG y JPEG</div>
								<iframe id="viewer_mod2" frameborder="0" scrolling="yes" width="100%" height="150px" style='display: none;'>
								</iframe> 
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Estado:
							</td>
							<td class="detalle">	
								<select name="mod_estado" id="mod_estado" class='select_opciones' onchange="valida_mod_dependencia_actu()">
									<option value="ACTIVO">Activo</option>
									<option value="INACTIVO">Inactivo</option>
								</select>
							</td>
							<td class="descripcion">
								Contraseña Nueva:
							</td>
							<td class="detalle">	
								<select name="mod_usuario_nuevo" id="mod_usuario_nuevo" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</td>
							<td class="descripcion">								
								Nivel Seguridad:
							</td>
							<td class="detalle">
								<select name="mod_nivel_seguridad" id="mod_nivel_seguridad" class='select_opciones'>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
							</td>
							<td class="descripcion">Jefe Dependencia:</td>
							<td class="detalle">
								<select name="mod_jefe_dependencia" id="mod_jefe_dependencia" class='select_opciones'onchange="valida_jefe_dependencia_mod()">
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
								<div id="mod_error_jefe_dependencia" class="errores">
									En la dependencia <strong id="mod_depe_jefe_dependencia"></strong> ya tiene asignado un Jefe : <br> <strong id="mod_user_jefe_dependencia"></strong> 
								</div>
							</td>
						</tr>
					</table>
					<hr>
					<h2> Permisos del Usuario</h2>
					<hr>	
					<table border="0">
						<tr>	
							<td class="descripcion">Administrador del Sistema:</td>
							<td class="detalle">
								<select name="mod_administrador_sistema" id="mod_administrador_sistema" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo Creación / Modificación de expedientes:</td>
							<td class="detalle">
								<select name="mod_creacion_expedientes" id="mod_creacion_expedientes" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo Cuadro de Clasificación Documental (TRD):</td>
							<td class="detalle">
								<select name="mod_cuadro_clasificacion" id="mod_cuadro_clasificacion" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo de Inventario:</td>
							<td class="detalle">
								<select name="mod_inventario" id="mod_inventario" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Modificación de Radicados:</td>
							<td class="detalle">
								<select name="mod_modificar_radicado" id="mod_modificar_radicado" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
						</tr>
						<tr>	
							<td class="descripcion">Módulo de préstamo de documentos:</td>
							<td class="detalle">
								<select name="mod_prestamo_documentos" id="mod_prestamo_documentos" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo Scanner en puesto de Trabajo:</td>
							<td class="detalle">
								<select name="mod_scanner" id="mod_scanner" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Módulo de Ubicación Topográfica:</td>
							<td class="detalle">
								<select name="mod_ubicacion_topografica" id="mod_ubicacion_topografica" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Módulo Ventanilla de Radicacion de Entrada (1):</td>
							<td class="detalle">
								<select name="mod_ventanilla_radicacion" id="mod_ventanilla_radicacion" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>

							<td class="descripcion">Radicacion Salida (2):</td>
							<td class="detalle">
								<select name="mod_radicacion_salida" id="mod_radicacion_salida" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>

							<td class="descripcion">Radicacion Normal (3):</td>
							<td class="detalle">
								<select name="mod_radicacion_normal" id="mod_radicacion_normal" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</td>
							<td class="descripcion">Radicacion Interna (4):</td>
							<td class="detalle">
								<select name="mod_radicacion_interna" id="mod_radicacion_interna" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
							<td class="descripcion">Radicacion Resoluciones (5):</td>
							<td class="detalle">
								<select name="mod_radicacion_resoluciones" id="mod_radicacion_resoluciones" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO" selected="selected">NO</option>
								</select>
							</td>
						</tr>		
						<tr>
							<td colspan="11">
								<div id='boton_modificar_usuario' class="center">
									<input type="button" id="bModificarUsuario"  class="botones" value="Modificar Usuario" onclick="submit_modificar_usuario()">
								</div>
							</td>
						</tr>
					</table>		
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para modificar usuarios-->

<!-- Desde aqui el div que tiene el formulario principal -->
		<div class="center" id="logo">
			<br><h1 style="margin-top:-10px;">Configuración Usuarios</h1>
		</div>
		<div class="form center">
			<input type="search" id="search_usuario" class="input_largo" placeholder="Ingrese Nombre de Usuario">
		</div>
		<div id="desplegable_resultados">
			Para generar listado de usuarios puede dar click <a href="javascript:listado_usuarios_depe();">aquí</a>
		</div>
		<hr>
		<!-- Desde aqui es el despliegue del gráfico del organigrama de dependencias  -->	
		<?php 
		$query_dependencias= "select * from dependencias where activa ='SI' and id_dependencia!='1'";
		$fila_dependencias = pg_query($conectado,$query_dependencias);
	/* Calcula el numero de registros que genera la consulta anterior. */
		$registros_dependencias= pg_num_rows($fila_dependencias);
		$organigrama="";
		for ($i=0;$i<$registros_dependencias;$i++){
			$linea_dependencia = pg_fetch_array($fila_dependencias);
			$codigo_dependencia=$linea_dependencia['codigo_dependencia'];
			$nombre_dependencia1=$linea_dependencia['nombre_dependencia'];
			$linea_dependencia_padre1=$linea_dependencia['dependencia_padre'];

//			$query_usuarios= "select * from usuarios where activa ='SI' and codigo_dependencia ='$codigo_dependencia'";
			$query_usuarios= "select * from usuarios where codigo_dependencia ='$codigo_dependencia' order by login";
			$fila_usuarios = pg_query($conectado,$query_usuarios);
		/* Calcula el numero de registros que genera la consulta anterior. */
			$registros_usuarios= pg_num_rows($fila_usuarios);
			$nombre_usuarios="";

			for($j=0;$j<$registros_usuarios;$j++){ // Imprime los usuarios de la dependencia.
				$linea_usuarios = pg_fetch_array($fila_usuarios);

				$login_usuario=$linea_usuarios['login'];
				$estado_usuario=$linea_usuarios['estado'];

				if($estado_usuario=="ACTIVO"){	// Si está activo, color verde si está inactivo, color rojo 
					$nombre_usuarios= $nombre_usuarios."<li style=\"color:green;\">$login_usuario</li>";
				}else{
					$nombre_usuarios= $nombre_usuarios."<li style=\"color:red;\">$login_usuario</li>";
				}
			}

			$organigrama = $organigrama."[{v:'$nombre_dependencia1', f:'$nombre_dependencia1 <div style=\"text-align:left;\">$nombre_usuarios</div>'},\"$linea_dependencia_padre1\",\"Usuarios de la dependencia $nombre_dependencia1\"],";

		}
		$organigrama1=substr($organigrama, 0,-1);

		 ?>
			<script type="text/javascript">
			//	  google.charts.load('current', {packages:["orgchart"]});
			      google.charts.setOnLoadCallback(drawChart);

			      function drawChart() {
			        var data = new google.visualization.DataTable();
			        data.addColumn('string', 'Name');
			        data.addColumn('string', 'Manager');
			        data.addColumn('string', 'ToolTip');

			        // Por cada una de las casillas, ingresa el nombre, jefe y tooltip para mostrar
			        data.addRows([
			        	<?php echo $organigrama1; ?>
			        ]);

			        // Se crea el organigrama.
			        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
			        // Dibuje el gráfico, estableciendo la opción allowHtml en true para la información sobre tooltips
			        chart.draw(data, {allowHtml:true});
			      }
			</script>
			<center id='resultado_usuarios_depe'>
				<div id="chart_div" style="  overflow: auto;"></div>
				<ul>
					<li style="color:green;">Usuarios Activos</li>
					<li style="color:red;">Usuarios Inactivos</li>
				</ul>
			</center>
<!-- Hasta aqui es el despliegue del gráfico del organigrama de dependencias  -->	

		<hr>
<!-- Hasta aqui el div que tiene el formulario principal -->
</body>
</html>