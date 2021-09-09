<?php 
	require_once("../login/validar_inactividad.php");
?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Buscador de Municipios</title>
	<script type="text/javascript" src="include/js/funciones_municipios.js"></script>
	<link rel="stylesheet" href="include/css/estilos_municipios.css">
</head>
<body>
<!--Desde aqui el div que contiene el formulario para agregar municipios-->
		<div id="ventana" class='ventana_modal'>
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nuevo Municipio</h1>
				<hr>
				<form id ="formulario_agregar_municipio" name ="formulario_agregar_municipio" autocomplete="off">
					<table>
						<tr>
							<input type="hidden" name ="tipo_formulario" id="tipo_formulario" value="crear_municipio"><!-- Tipo de formulario para query_municipios.php -->
							<td class="descripcion">Continente : </td>
							<td class="detalle">
								<select name="continente" id="continente" class='select_opciones' onchange="limpia_formulario_agregar()">
									<option value="AFRICA">Africa</option>
									<option value="AMERICA" selected="selected">America</option>
									<option value="ASIA">Asia</option>
									<option value="EUROPA">Europa</option>
									<option value="OCEANIA">Oceania</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="descripcion">País :</td>
							<td class="detalle"><input type="search" value="COLOMBIA" name="pais" id="pais" onblur="validar_input('pais');trim('pais')">
								<div id="sugerencia_pais"></div>
								<div id="error_pais_invalido" class="errores">Seleccione nombre de país válido</div>
								<div id="pais_max" class="errores">El nombre de país no puede ser mayor a 30 caracteres. (Actualmente <b><u id='pais_contadormax'></u></b> caracteres)</div>
								<div id="pais_min" class="errores">El nombre de país no puede ser menor a 3 caracteres</div>
								<div id="pais_null" class="errores">El nombre del país es obligatorio</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Departamento :</td>
							<td class="detalle"><input type="search" placeholder="Digite Departamento" name="departamento" id="departamento" onblur="validar_input('departamento'); trim('departamento')">
								<div id="sugerencia_departamento"></div>
								<div id="error_departamento_invalido" class="errores">Seleccione nombre de departamento válido</div>
								<div id="departamento_max" class="errores">El nombre de departamento no puede ser mayor a 50 caracteres. (Actualmente <b><u id='departamento_contadormax'></u></b> caracteres)</div>
								<div id="departamento_min" class="errores">El nombre de departamento no puede ser menor a 3 caracteres</div>
								<div id="departamento_null" class="errores">El nombre del departamento es obligatorio</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Municipio :</td>
							<td class="detalle"><input type="search" placeholder="Digite Municipio" name="municipio" id="municipio" onblur="validar_input('municipio'); trim('municipio')">
								<div id="sugerencia_municipio"></div>
								<div id="error_municipio_invalido" class="errores">Seleccione nombre de municipio válido</div>
								<div id="municipio_max" class="errores">El nombre de municipio no puede ser mayor a 50 caracteres. (Actualmente <b><u id='municipio_contadormax'></u></b> caracteres)</div>
								<div id="municipio_min" class="errores">El nombre de municipio no puede ser menor a 3 caracteres</div>
								<div id="municipio_null" class="errores">El nombre del municipio es obligatorio</div>
								<div id="municipio_ya_existe" class="errores"> El nombre del municipio ya existe. No se puede crear.</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" id="boton_grabar_municipio">
								<input type="button" value="Grabar Municipio" id="bEnviar" class="botones2">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para agregar municipios-->
<!--Desde aqui el div que contiene el formulario para modificar municipios-->	
		<div id="ventana2" class='ventana_modal'>
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
				<h1>Formulario Modificar Municipio</h1>
				<hr>
				<form id ="formulario_modificar_municipio" name ="formulario_modificar_municipio" autocomplete="off">
					<table>
						<tr>
							<input type="hidden" name ="tipo_formulario_mod" id="tipo_formulario_mod" value="modificar_municipio"><!-- Tipo de formulario para query_municipios.php -->		
							<input type="hidden" name ="id_municipio" id="id_municipio" value=""><!-- Id de Municipio para query_municipios.php -->			
							<td class="descripcion">Continente : </td>
							<td class="detalle">
								<input type="hidden" name="ant_continente" id="ant_continente" value=""></input>
								<select name="mod_continente" id="mod_continente" class='select_opciones' onchange="limpia_formulario_modificacion()">
									<option value="AFRICA">Africa</option>
									<option value="AMERICA">America</option>
									<option value="ASIA">Asia</option>
									<option value="EUROPA">Europa</option>
									<option value="OCEANIA">Oceania</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="descripcion">País :</td>
							<td class="detalle">
								<input type="hidden" name="ant_pais" id="ant_pais" value=""></input>
								<input type="search" name="mod_pais" id="mod_pais" onblur="validar_mod_pais()">
								<div id="sugerencia_mod_pais"></div>
								<div id="mod_pais_max" class="errores">El nombre de país no puede ser mayor a 30 caracteres. (Actualmente <b><u id='mod_pais_contadormax'></u></b> caracteres)</div>
								<div id="mod_pais_min" class="errores">El nombre de país no puede ser menor a 3 caracteres</div>
								<div id="mod_pais_null" class="errores">El nombre del país es obligatorio</div>
								<div id="error_mod_pais_invalido" class="errores">Seleccione nombre de país válido</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Departamento :</td>
							<input type="hidden" name="ant_departamento" id="ant_departamento" value=""></input>
							<td class="detalle">
								<input type="search" placeholder="Digite Departamento" name="mod_departamento" id="mod_departamento" onblur="validar_modificar_municipio()">	
								<div id="sugerencia_mod_departamento"></div>			

								<div id="error_mod_departamento_invalido" class="errores">Seleccione nombre de departamento válido</div>

								<div id="mod_departamento_max" class="errores">El nombre de país no puede ser mayor a 50 caracteres. (Actualmente <b><u id='mod_departamento_contadormax'></u></b> caracteres)</div>
								<div id="mod_departamento_min" class="errores">El nombre de departamento no puede ser menor a 3 caracteres</div>
								<div id="mod_departamento_null" class="errores">El nombre del departamento es obligatorio</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Municipio :</td>
							<td class="detalle">
								<input type="hidden" name="ant_municipio" id="ant_municipio" value=""></input>
								<input type="search" placeholder="Digite Municipio" name="mod_municipio" id="mod_municipio" onblur="validar_modificar_municipio()">
								<div id="sugerencia_mod_municipio"></div>			
								<div id="error_mod_municipio" class="errores">El nombre del municipio es obligatorio</div>
								<div id="error_mod_municipio_minimo" class="errores">El nombre de municipio no puede ser menor a 3 caracteres</div>
								<div id="error_mod_municipio_maximo" class="errores">El nombre de municipio no puede ser mayor a 30 caracteres</div>
								<div id="error_mod_municipio_invalido" class="errores">El nombre de municipio ya existe. No se puede crear</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" id="boton_modificar_municipio">
								<input type="button" value="Modificar Municipio" id="enviar_mod" class="botones2">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para modificar municipios-->
<!-- Desde aqui el div que tiene el formulario principal -->
		<br>
		<div class="center" id="logo">
			<!-- <img src="imagenes/logo3.png" width="100" alt="Jonas SGD" title="Logo Jonas">	 -->
			<h1 style="margin-top:-10px;">Configuración Países, Departamentos y Municipios</h1>
		</div>
		<div class="form center">
				<input type="search" id="search" onkeyup="espacios_formulario_municipio('search')" placeholder="Ingrese nombre del municipio">
		</div>
		<div id="desplegable_resultados"></div>
<!-- Hasta aqui el div que tiene el formulario principal -->
</body>
</html>