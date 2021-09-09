<!DOCTYPE html>
<html>
<head>
	<title>Formulario PQR</title>
	<link rel="stylesheet" href="estilos_pqr.css">
</head>
<body>
<!--Desde aqui el div que contiene el formulario PQR-->
	<div id="ventana">
		<div class="formulario">
		<!--	<div class="cerrar"><a href='javascript:cerrarVentanaCrearUsuarios();'>Cerrar X</a></div> -->
			<h1>Formulario PQR</h1>
			<hr>
			<form enctype="multipart/form-data" action="procesa_pqr.php" method="POST" id ="formulario_pqr" name ="formulario_pqr" autocomplete="off">
<!--				<input type="hidden" name="tipo_formulario" id="tipo_formulario" value="crear_usuario"> -->
				<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
				<table>
					<tr>
						<td width="20%">Tipo Documento : </td>
						<td width="30%">
							<select name="tipo_doc" id="tipo_doc" class='select_opciones' onchange="valida_td()">
								<option value="CC" selected="selected">Cedula de Ciudadania</option>
								<option value="CE">Cedula de Extranjeria</option>
								<option value="NIT">NIT</option>
								<option value="PSP">Pasaporte</option>
								<option value="TI">Tarjeta de Identidad</option>
							</select>
						</td>
						<td width="20%">Numero de Documento</td>
						<td width="30%">
							<input type="search" name="numero_doc" id="numero_doc" onkeyup="espacios_formulario_pqr('numero_doc')">
							<div id="error_numero_doc" class="errores">El número de identificación es obligatorio.</div>

						</td>
					</tr>
				<div id="datos_peticionario">
					<tr>
						<td>
							Primer Nombre :
						</td>
						<td>
							<input type="search" name="nombre_1" id="nombre_1" onkeyup="espacios_formulario_pqr('nombre_1')">
<!--						<div id="error_apellido_1" class="errores">El Primer Apellidox es obligatorio.</div> -->	
						</td>
						<td>
							Segundo Nombre :
						</td>
						<td>
							<input type="search" name="nombre_2" id="nombre_2" onkeyup="espacios_formulario_pqr('nombre_2')">
						</td>
					</tr>
					<tr>
						<td>
							Primer Apellido :
						</td>
						<td>
							<input type="search" name="apellido_1" id="apellido_1"
							 onkeyup="espacios_formulario_pqr('apellido1')">
						</td>
						<td>
							Segundo Apellido :
						</td>
						<td>
							<input type="search" name="apellido_2" id="apellido_2"
							 onkeyup="espacios_formulario_pqr('apellido_2')">
						</td>
					</tr>	
					<tr>
						<td>Genero : </td>
						<td>
							<select name="genero" id="genero" class='select_opciones'>
								<option value="NO" selected="selected"> -- Seleccione una opcion --  </option>
								<option value="H"> Masculino </option>
								<option value="M">Femenino</option>
							</select>
						</td>
						<td>Poblacion Vulnerable : </td>
						<td>
							<select name="poblacion_vulnerable" id="poblacion_vulnerable" class='select_opciones' onchange="valida_pv()">
								<option value="ADULTO"> Adulto Mayor </option>
								<option value="DESPLAZADO"> Desplazado </option>
								<option value="ETNIAS"> Etnias </option>						
								<option value="EXTREMA POBREZA"> Extrema Pobreza </option>
								<option value="LGBTI"> LGBTI </option>
								<option value="MADRE"> Madre Cabeza de Familia </option>
								<option value="NINGUNO" selected="selected"> Ninguno </option>
								<option value="DISCAPACITADO"> Personas con Discapacidad Fisica </option>
								<option value="REINSERTADOS"> Reinsertados </option>
							</select>
							<div id="etnias"></div>
						</td>
					</tr>	
				</div>
					<tr>
						<td>
							Correo Electronico
						</td>
						<td>
							<input type="email" name="mail" id="mail" placeholder="Ingrese Mail del Usuario" onkeyup="espacios_formulario_pqr('mail')" onblur="validar_correo_electronico()">

							<div id="error_mail" class="errores">El correo electronico es obligatorio.</div>
							<div id="valida_minimo_mail" class="errores">El mail del usuario no puede ser menor a 6 caracteres.</div>
							<div id="valida_maximo_mail" class="errores">El mail del usuario no puede ser mayor a 30 caracteres.</div>
							<div id="error_mail_formato" class="errores">
								El mail ingresado no tiene formato correcto (usuario@algunmail.com) por lo que no se puede crear.
							</div>
						</td>

					</tr>
					<tr>
						<td>
							Dependencia :
						</td>
						<td>
							<input type="hidden" name="codigo_dependencia" id="codigo_dependencia">
							<input type="search" name="dependencia" id="dependencia" placeholder="Ingrese Dependencia del Usuario" onkeyup="espacios_formulario_usuarios('dependencia')" onblur="validar_dependencia()">
							<div id="sugerencias_dependencia"></div>

							<div id="error_dependencia" class="errores">La dependencia del usuario es obligatoria.</div>
							<div id="valida_minimo_dependencia" class="errores">La dependencia del usuario no puede ser menor a 6 caracteres.</div>
							<div id="valida_maximo_dependencia" class="errores">La dependencia del usuario no puede ser mayor a 50 caracteres.</div>
							<div id="error_dependencia_inexistente" class="errores">La dependencia ingresada no existe en la base de datos.</div>
							<div id="error_dependencia_invalida" class="errores">Por favor seleccione una dependencia válida.</div>
						</td>
					</tr>
					<tr>
						<td>Perfil : </td>
						<td>
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
					</tr>
					<tr>
						<td>Foto del Usuario</td>
						<td>
							<input type="file" name="imagen" id="imagen" onchange="valida_tipo_archivo()">
							<div id="error_imagen" class="errores">La imagen del usuario es obligatoria</div>
							<div id="error_imagen_invalida" class="errores"> El formato de la imagen que va a ingresar no es válido. El sistema solo admite formato PNG, GIF, JPG y JPEG</div>
						</td>
					</tr>
				</table>
				<hr>
				<h2> Permisos del Usuario</h2>
				<hr>	
				<table>
					<tr>	
						<td border="5">
							Estado :
						</td>
						<td border="5">	
							<select name="estado" id="estado" class='select_opciones'>
								<option value="ACTIVO">Activo</option>
								<option value="INACTIVO">Inactivo</option>
							</select>
						</td>
				<!--</tr>	
					<tr>	
				-->		<td>
							Contraseña Nueva
						</td>
						<td>	
							<select name="usuario_nuevo" id="usuario_nuevo" class='select_opciones'>
								<option value="SI">SI</option>
								<option value="NO">NO</option>
							</select>
						</td>
					</tr>
					
					<tr>
						<td>								
							Nivel Seguridad
						</td>
						<td>
							<select name="nivel_seguridad" id="nivel_seguridad" class='select_opciones'>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
							</select>
						</td>
				<!--</tr>	
					<tr>
				-->		<td>Ventanilla de Radicacion</td>
						<td>
							<select name="ventanilla_radicacion" id="ventanilla_radicacion" class='select_opciones'>
								<option value="SI">SI</option>
								<option value="NO" selected="selected">NO</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<center>
								<input type="button" value="Crear Usuario" id="bCrearUsuario" class="botones"></td>
							</center>	
					</tr>
				</table>	
			</form>
		</div><!-- Cierra el div class ="formulario" -->		
	</div><!-- Cierra el div id='ventana' -->
<!--Hasta aqui el div que contiene el formulario para agregar municipios-->
<!--**************************************************************************************************-->
</body>
</html>