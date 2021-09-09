<?php 
	require_once("../login/validar_inactividad.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Administración Parametrización</title>
	<script type="text/javascript" src="include/js/funciones_parametrizacion.js"></script>
	<link rel="stylesheet" href="include/css/estilos_parametrizacion.css">
</head>
<body>
<!-- Desde aqui el div que tiene el formulario principal -->
		<div class="center" id="logo">
			<br>	
			<h1>Modulo Parametrización</h1>
		</div>
		<center>		
		<table class="encabeza" border="0" width="80%">
			<tr>
				<td width="50%" id="p1" class="hover_pointer"  onclick="carga_contenido_param('1')" >
					1. Tipo de Documento (Términos Formulario Radicacion de Entrada).
				</td>
				<td width="50%" id="p2" class="hover_pointer" onclick="carga_contenido_param('2')">
					2. Tipo de Documento (Términos Formulario PQR).
				</td>
			</tr>
			<tr>
				<td id="p3" class="hover_pointer" onclick="carga_contenido_param('3')">
					3. Administrador de Tipos de Radicados.
				</td>
				<td id="p4" class="hover_pointer" onclick="carga_contenido_param('4')">
					4. Administrador de Secuencias de Radicación.
				</td>
			</tr>
		</table>
		</center>
		<br>
		<div id="contenido_param"></div>
	
<!-- Hasta aqui el div que tiene el formulario principal -->
<!-- Div que contiene agregar terminos Formulario Radicacion de Entrada -->
		<div id="ventana" class="ventana">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrarVentanaCrearTipoDocumento();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nuevo Tipo Documento Formulario Radicacion Entrada</h1>
				<hr>
				<form id ="formulario_agregar_tipo_documento" name ="formulario_agregar_tipo_documento" autocomplete="off">
					<input type="hidden" name="tipo_formulario_agregar_tipo_documento" id="tipo_formulario_agregar_tipo_documento" value="crear_tipo_documento">
					<table border="0">
						<tr>
							<td class="descripcion" width="50%">Nombre del tipo de documento :</td>
							<td class="detalle" width="50%">
								<input type="search" placeholder="Digite Nombre del Tipo de Documento" name="tipo_doc" id="tipo_doc" onkeyup="espacios_formulario_tipo_documento_terminos('tipo_doc')" onblur="validar_agregar_tipo_doc()">
								<div id="sugerencias_tipo_doc"></div>
								<div id="error_tipo_doc" class="errores">El nombre del tipo de documento es obligatorio</div>
								<div id="error_tipo_doc_invalido" class="errores">El nombre del tipo de documento ya existe.</div>
								<div id="error_tipo_doc_minimo" class="errores">El nombre del tipo de documento no puede ser menor a 6 caracteres</div>
								<div id="error_tipo_doc_maximo" class="errores">El nombre del tipo de documento no puede ser mayor a 30 caracteres</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Descripcion</td>
							<td class="detalle">
								<textarea name="descripcion" id="descripcion" rows="2" style="width:100%;padding:5px;" onkeyup="espacios_formulario_tipo_documento_terminos('descripcion')" onblur="validar_descripcion()"></textarea>
								<div id="error_descripcion" class="errores">La descripcion del tipo de documento es obligatoria</div>
								<div id="error_min_descripcion" class="errores">La descripcion del tipo de documento no puede ser menor a veinte caracteres (numeros o letras) </div>
								<div id="error_max_descripcion" class="errores">La descripcion del tipo de documento no puede ser mayor a quinientos caracteres (numeros o letras)</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Días en término del tipo de documento :</td>
							<td class="detalle"><input type="number" placeholder="Dias Habiles de Término" name="termino" id="termino" onkeyup="espacios_formulario_tipo_documento_terminos('termino')" onblur="validar_agregar_termino()">
								<div id="error_termino" class="errores">El campo "Días en término del tipo de documento" es un campo obligatorio</div>
								<div id="error_no_es_numero" class="errores">El campo "Días en término del tipo de documento" es un numérico. No se admiten letras ni caracteres. </div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<center>
									<input type="button" value="Grabar Tipo Documento" id="enviar_td" class="botones">
								</center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!-- Hasta aqui div que contiene agregar terminos Formulario Radicacion de Entrada -->
<!-- Div que contiene modificar terminos Formulario Radicacion de Entrada -->
		<div id="ventana2" class="ventana">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventana_modificar_tipo_documento();'>Cerrar X</a></div>
				<h1>Formulario Modificar Tipo Documento Formulario Radicacion Entrada</h1>
				<hr>
				<form id ="formulario_modificar_tipo_documento" name ="formulario_modificar_tipo_documento" autocomplete="off" >
					<input type="hidden" name="tipo_formulario_modificar_tipo_documento" id="tipo_formulario_modificar_tipo_documento" value="modificar_tipo_documento">
					<input type="hidden" name="id_mod" id="id_mod">
					<table border="0">
						<tr>
							<td class="descripcion" width="50%">Nombre del tipo de documento :</td>
							<td class="detalle" width="50%">
								<input type="hidden" name="tipo_doc_mod_ant" id="tipo_doc_mod_ant">
								<input type="search" placeholder="Digite Nombre del Tipo de Documento" name="tipo_doc_mod" id="tipo_doc_mod" onkeyup="espacios_formulario_tipo_documento_terminos('tipo_doc_mod')" onblur="validar_modificar_tipo_doc()">
								<div id="sugerencias_tipo_doc_mod"></div>
								<div id="error_tipo_doc_mod" class="errores">El nombre del tipo de documento es obligatorio</div>
								<div id="error_tipo_doc_mod_invalido" class="errores">El nombre del tipo de documento ya existe.</div>
								<div id="error_tipo_doc_mod_minimo" class="errores">El nombre del tipo de documento no puede ser menor a 5 caracteres</div>
								<div id="error_tipo_doc_mod_maximo" class="errores">El nombre del tipo de documento no puede ser mayor a 30 caracteres</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Descripcion</td>
							<td class="detalle">
								<textarea name="descripcion_mod" id="descripcion_mod" rows="2" style="width:100%;padding:5px;" onkeyup="espacios_formulario_tipo_documento_terminos('descripcion_mod')" onblur="validar_descripcion_mod()"></textarea>
								<div id="error_descripcion_mod" class="errores">La descripcion del tipo de documento es obligatoria</div>
								<div id="error_min_descripcion_mod" class="errores">La descripcion del tipo de documento no puede ser menor a veinte caracteres (numeros o letras) </div>
								<div id="error_max_descripcion_mod" class="errores">La descripcion del tipo de documento no puede ser mayor a quinientos caracteres (numeros o letras)</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Días en término del tipo de documento :</td>
							<td class="detalle"><input type="number" placeholder="Dias Habiles de Término" name="mod_termino" id="mod_termino" onkeyup="espacios_formulario_tipo_documento_terminos('mod_termino')" onblur="validar_modificar_termino()">
								<div id="error_mod_termino" class="errores">El campo "Días en término del tipo de documento" es un campo obligatorio</div>
								<div id="error_mod_no_es_numero" class="errores">El campo "Días en término del tipo de documento" es un numérico. No se admiten letras ni caracteres. </div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Activo :</td>
							<td class="detalle">
								<select name="mod_estado" id="mod_estado" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</td>
						</tr>
						<tr>
						<tr>
							<td colspan="2">																<center>
									<input type="button" value="Modificar Tipo Documento" id="enviar_mod_td" class="botones">
								</center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!-- Hasta aqui div que contiene modificar terminos Formulario Radicacion de Entrada -->
<!-- Div que contiene agregar terminos Formulario PQR -->
		<div id="ventana3" class="ventana">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventana_crear_tipo_documento_pqr();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nuevo Tipo Documento Formulario PQR</h1>
				<hr>
				<form id ="formulario_agregar_tipo_documento_pqr" name ="formulario_agregar_tipo_documento_pqr" autocomplete="off">
					<input type="hidden" name="tipo_formulario_crear_tipo_documento_pqr" id="tipo_formulario_crear_tipo_documento_pqr" value="crear_tipo_documento_pqr">
					<table border="0">
						<tr>
							<td class="descripcion"width="50%">Nombre del tipo de documento :</td>
							<td class="detalle" width="50%">
								<input type="search" placeholder="Digite Nombre del Tipo de Documento" name="tipo_doc_pqr" id="tipo_doc_pqr" onkeyup="espacios_formulario_tipo_documento_terminos('tipo_doc_pqr')" onblur="validar_agregar_tipo_doc_pqr()">
								<div id="sugerencias_tipo_doc_pqr"></div>
								<div id="error_tipo_doc_pqr" class="errores">El nombre del tipo de documento es obligatorio</div>
								<div id="error_tipo_doc_pqr_invalido" class="errores">El nombre del tipo de documento ya existe.</div>
								<div id="error_tipo_doc_pqr_minimo" class="errores">El nombre del tipo de documento no puede ser menor a 5 caracteres</div>
								<div id="error_tipo_doc_pqr_maximo" class="errores">El nombre del tipo de documento no puede ser mayor a 30 caracteres</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Descripcion</td>
							<td class="detalle">
								<textarea name="descripcion_pqr" id="descripcion_pqr" rows="2" style="width:100%;padding:5px;" onkeyup="espacios_formulario_tipo_documento_terminos('descripcion_pqr')" onblur="validar_descripcion_pqr()"></textarea>
								<div id="error_descripcion_pqr" class="errores">La descripcion del tipo de documento es obligatoria</div>
								<div id="error_min_descripcion_pqr" class="errores">La descripcion del tipo de documento no puede ser menor a veinte caracteres (numeros o letras) </div>
								<div id="error_max_descripcion_pqr" class="errores">La descripcion del tipo de documento no puede ser mayor a quinientos caracteres (numeros o letras)</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Días en término del tipo de documento :</td>
							<td class="detalle">
								<input type="number" placeholder="Dias Habiles de Término" name="termino_pqr" id="termino_pqr" onkeyup="espacios_formulario_tipo_documento_terminos('termino_pqr')" onblur="validar_agregar_termino_pqr()">
								<div id="error_termino_pqr" class="errores">El campo "Días en término del tipo de documento" es un campo obligatorio</div>
								<div id="error_no_es_numero_pqr" class="errores">El campo "Días en término del tipo de documento" es un numérico. No se admiten letras ni caracteres. </div>
							</td>
						</tr>
						<tr>
							<td colspan="2">								
								<center>
									<input type="button" value="Grabar Tipo Documento" id="enviar_td_pqr" class="botones"></td>
								</center>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!-- Hasta aqui div que contiene agregar terminos Formulario PQR -->
<!-- Div que contiene modificar terminos Formulario PQR -->
		<div id="ventana4" class="ventana">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventana_modificar_tipo_documento_pqr();'>Cerrar X</a></div>
				<h1>Formulario Modificar Tipo Documento Formulario PQR</h1>
				<hr>
				<form id ="formulario_modificar_tipo_documento_pqr" name ="formulario_modificar_tipo_documento_pqr" autocomplete="off">
					<input type="hidden" name="tipo_formulario_modificar_td_pqr" id="tipo_formulario_modificar_td_pqr" value="modificar_tipo_documento_pqr">
					<input type="hidden" name="id_mod_pqr" id="id_mod_pqr">
					<table border="0">
						<tr>
							<td class="descripcion" width="50%">Nombre del tipo de documento :</td>
							<td class="detalle" width="50%">
								<input type="hidden" name="tipo_doc_mod_ant_pqr" id="tipo_doc_mod_ant_pqr">
								<input type="search" placeholder="Digite Nombre del Tipo de Documento" name="tipo_doc_mod_pqr" id="tipo_doc_mod_pqr" onkeyup="espacios_formulario_tipo_documento_terminos('tipo_doc_mod_pqr')" onblur="validar_modificar_tipo_doc_pqr()">
								<div id="sugerencias_tipo_doc_mod_pqr"></div>
								<div id="error_tipo_doc_mod_pqr" class="errores">El nombre del tipo de documento es obligatorio</div>
								<div id="error_tipo_doc_mod_pqr_invalido" class="errores">El nombre del tipo de documento ya existe.</div>
								<div id="error_tipo_doc_mod_pqr_minimo" class="errores">El nombre del tipo de documento no puede ser menor a 5 caracteres</div>
								<div id="error_tipo_doc_mod_pqr_maximo" class="errores">El nombre del tipo de documento no puede ser mayor a 30 caracteres</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Descripcion</td>
							<td class="detalle">
								<textarea name="descripcion_pqr_mod" id="descripcion_pqr_mod" rows="2" style="width:100%;padding:5px;" onkeyup="espacios_formulario_tipo_documento_terminos('descripcion_pqr_mod')" onblur="validar_modificar_descripcion_pqr()"></textarea>
								<div id="error_descripcion_pqr_mod" class="errores">La descripcion del tipo de documento es obligatoria</div>
								<div id="error_min_descripcion_pqr_mod" class="errores">La descripcion del tipo de documento no puede ser menor a 20 caracteres (numeros o letras) </div>
								<div id="error_max_descripcion_pqr_mod" class="errores">La descripcion del tipo de documento no puede ser mayor a 500 caracteres (numeros o letras)</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Días en término del tipo de documento :</td>
							<td class="detalle"><input type="number" placeholder="Dias Habiles de Término" name="mod_termino_pqr" id="mod_termino_pqr" onkeyup="espacios_formulario_tipo_documento_terminos('mod_termino_pqr')" onblur="validar_modificar_termino_pqr()">
								<div id="error_mod_termino_pqr" class="errores">El campo "Días en término del tipo de documento" es un campo obligatorio</div>
								<div id="error_mod_no_es_numero_pqr" class="errores">El campo "Días en término del tipo de documento" es un numérico. No se admiten letras ni caracteres. </div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Activo :</td>
							<td class="detalle">
								<select name="mod_estado_pqr" id="mod_estado_pqr" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</td>
						</tr>
						<tr>
						<tr>
							<td colspan="2">
							<center>
								<input type="button" value="Modificar Tipo Documento" id="enviar_mod_td_pqr" class="botones">
							</center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!-- Hasta aqui div que contiene modificar terminos Formulario PQR -->
<!-- Div que contiene agregar tipo de radicados -->
		<div id="ventana5" class="ventana">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventana_crear_tipo_radicado();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nuevo Tipo Radicación</h1>
				<hr>
				<form id ="formulario_agregar_tipo_radicacion" name ="formulario_agregar_tipo_radicacion" autocomplete="off">
					<input type="hidden" name="tipo_formulario_crear_tipo_radicado" id="tipo_formulario_crear_tipo_radicado" value="crear_tipo_radicado">
					<table border="0">
						<tr>
							<td class="descripcion" width="50%">Codigo del tipo de radicado :</td>
							<td class="detalle" width="50%">
								<input type="search" placeholder="Digite Codigo del Tipo de Radicado" name="codigo_tipo_rad" id="codigo_tipo_rad" onkeyup="espacios_formulario_tipo_documento_terminos('codigo_tipo_rad')" onblur="validar_agregar_codigo_tipo_rad()">
								<div id="sugerencias_codigo_tipo_rad"></div>
								<div id="error_codigo_tipo_rad" class="errores">El codigo del tipo de radicado es obligatorio</div>
								<div id="error_codigo_tipo_rad_invalido" class="errores">El codigo del tipo de radicado ya existe.</div>
								<div id="error_codigo_tipo_rad_maximo" class="errores">El codigo del tipo de radicado no puede ser mayor a 1 caracter (Numero o letra)</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Nombre del tipo de Radicado :</td>
							<td class="detalle">
								<input type="search" placeholder="Nombre del Tipo de Radicado" name="nombre_tipo_rad" id="nombre_tipo_rad" onkeyup="espacios_formulario_tipo_documento_terminos('nombre_tipo_rad')" onblur="validar_nombre_tr()">
								<div id="error_nombre_tipo_rad" class="errores">El Nombre del tipo de radicado es obligatorio</div>
								<div id="error_nombre_tipo_rad_minimo" class="errores">El nombre del tipo de radicado no puede ser menor a 3 caracteres (Numeros o letras)</div>
								<div id="error_nombre_tipo_rad_maximo" class="errores">El nombre del tipo de radicado no puede ser mayor a 12 caracteres (Numeros o letras)</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<center>
									<input type="button" value="Grabar Tipo Radicado" id="enviar_tr" class="botones"></td>
								</center>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!-- Hasta aqui div que contiene agregar tipo de radicados -->

<!-- Div que contiene agregar consecutivo -->
		<div id="ventana6" class="ventana">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventana_crear_secuencia();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nueva Secuencia</h1>
				<hr>
				<form id ="formulario_agregar_secuencia" name ="formulario_agregar_secuencia" autocomplete="off">
					<input type="hidden" name="tipo_formulario_crear_secuencia" id="tipo_formulario_crear_secuencia" value="crear_secuencia">
					<table border="0">
						<tr>
							<td class="descripcion" width="50%">
								Codigo de la Dependencia
							</td>
							<td class="detalle" width="50%">
								<input type="search" placeholder="Digite el codigo de la dependencia" id="codigo_dependencia_sec" name="codigo_dependencia_sec" >
								<div id="sugerencias_codigo_sec"></div>
								<div id="error_codigo_sec_invalido" class="errores2">El codigo de la dependencia no existe en la base de datos.</div>

								<div id="codigo_dependencia_sec_max" class="errores">El codigo de la dependencia no puede ser mayor a 5 caracteres</div>
								<div id="codigo_dependencia_sec_min" class="errores">El codigo de la dependencia debe tener más caracteres (numeros o letras)</div>
								<div id="codigo_dependencia_sec_null" class="errores">El codigo de la dependencia es obligatorio</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Tipo de Radicado : </td>
							<td class="detalle">
								<div id="select_tr"></div>
								<div id='error_consecutivo' class='errores'>No ha seleccionado un tipo de radicado válido</div>				
								<div id='error_consecutivo_invalido' class='errores'>El consecutivo de la dependencia ya existe</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Va a radicar con el consecutivo de la Dependencia
							</td>
							<td class="detalle">
								<input type="search" placeholder="Digite el codigo de la dependencia padre" id="codigo_dependencia_padre_sec" name="codigo_dependencia_padre_sec" onblur="validar_secuencia()">

								<div id="sugerencias_codigo_sec_padre"></div>
								<div id="error_codigo_sec_padre_invalido" class="errores2">El codigo de la dependencia no existe en la base de datos.</div>

								<div id="codigo_dependencia_padre_sec_max" class="errores">El codigo de la dependencia padre no puede ser mayor a 5 caracteres</div>
								<div id="codigo_dependencia_padre_sec_min" class="errores">El codigo de la dependencia padre debe tener más caracteres (números o letras)</div>
								<div id="codigo_dependencia_padre_sec_null" class="errores">El codigo de la dependencia padre es obligatorio</div>
							</td>
						</tr>
				
						<tr>
							<td colspan="2">
								<center id='boton_enviar_sec'>
									<input type="button" value="Crear secuencia de la dependencia" id="enviar_sec" class="botones" onclick="submit_agregar_secuencia()">
								</center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!-- Hasta aqui div que contiene agregar consecutivo -->
<!-- Desde aqui div que contiene modificar consecutivo -->
		<div id="ventana7" class="ventana">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventana_modificar_secuencia();'>Cerrar X</a></div>
				<h1>Formulario Modificar Consecutivo</h1>
				<hr>
				<form id ="formulario_modificar_consecutivo" name ="formulario_modificar_consecutivo" autocomplete="off">
					<input type="hidden" name="tipo_formulario_modificar_consecutivo" id="tipo_formulario_modificar_consecutivo" value="modificar_consecutivo">
					<table border="0">
						<tr>
							<td class="descripcion" width="50%">Codigo de la Dependencia :</td>
							<td class="detalle" width="50%">
								<input type="hidden" name="codigo_dependencia_sec_mod_ant" id="codigo_dependencia_sec_mod_ant">
								<input type="search" placeholder="Digite el codigo de la dependencia" name="codigo_dependencia_sec_mod" id="codigo_dependencia_sec_mod" disabled="true">
								<div id="sugerencias_codigo_dependencia_sec_mod"></div>
								<div id="error_codigo_dependencia_sec_mod" class="errores">El codigo de la dependencia es obligatorio</div>
								<div id="error_codigo_dependencia_sec_invalido_mod" class="errores">El codigo de la dependencia no existe en la base de datos.</div>
								<div id="error_codigo_dependencia_sec_minimo_mod" class="errores">El codigo de la dependencia no puede ser menor a 3 caracteres</div>
								<div id="error_codigo_dependencia_sec_maximo_mod" class="errores">El codigo de la dependencia no puede ser mayor a 3 caracteres</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Tipo de Radicado : </td>
							<td class="detalle">
								<input type="hidden" name="tipo_radicado_sec_mod_ant" id="tipo_radicado_sec_mod_ant">
								<input type="search" placeholder="Digite el codigo de la dependencia" name="tipo_documento_sec_mod" id="tipo_documento_sec_mod" disabled="true">
								<div id='error_consecutivo_mod' class='errores'>No ha seleccionado un tipo de radicado válido</div>				
								<div id='error_consecutivo_invalido_mod' class='errores'>El consecutivo de la dependencia ya existe</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Va a radicar con el consecutivo de la Dependencia
							</td>
							<td class="detalle">
								<input type="search" placeholder="Digite el codigo de la dependencia padre" id="codigo_dependencia_padre_sec_mod" name="codigo_dependencia_padre_sec_mod" onkeyup="espacios_formulario_tipo_documento_terminos('codigo_dependencia_padre_sec_mod')">
								<div id="sugerencias_codigo_dependencia_padre_sec_mod"></div>
								<div id="error_codigo_dependencia_padre_sec_mod" class="errores">El codigo de la dependencia es obligatorio</div>
								<div id="error_codigo_dependencia_padre_sec_invalido_mod" class="errores">El codigo de la dependencia no existe en la base de datos.</div>
								<div id="error_codigo_dependencia_padre_sec_minimo_mod" class="errores">El codigo de la dependencia no puede ser menor a 3 caracteres</div>
								<div id="error_codigo_dependencia_padre_sec_maximo_mod" class="errores">El codigo de la dependencia no puede ser mayor a 5 caracteres</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<center>
									<input type="button" value="Modificar secuencia de la dependencia" id="enviar_mod_sec" class="botones" onclick="submit_modificar_secuencia()">
								</center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!-- Hasta aqui div que contiene modificar consecutivo -->
</body>
</html>

