<!-- 
	index_radicacion_interna.php
		- La radicacion interna realiza el proceso de documentacion mas rapido en un proceso donde no se vean involucrados terceros de la compañia
			* El documento index_radicacion_interna.php espera la variable de radicado, con la variable radicado se espera saber si se necesita editar un radicado ya creado y si es asi se cargara en el formulario de la radicacion interna, si la variable radicado no se envia se toma que es un radicado nuevo.
			* Index_radicacion_interna genera automaticamente el borrador de un archivo, si en el proceso de llenado se finaliza bruscamente la conexion, el sistema guardara el borrador y cuando retorne e inicie sesion el sistema le bara saber; esta forma de cargar el radicado se guiara con la variable tipo_radicacion_interna para tomar el numero de radicado, la variable tipo_radicacion_interna esta programada y no se debe alterar manualmente.
			* By: Gilberto Contreras Cardenas Desarrollador Junior
-->
<?php
	require_once("../../login/validar_inactividad.php");// Validar inactividad del usuario
	require_once("../../include/genera_fecha.php");// Se genera fecha y se utiliza la varible $fecha_e2
?>
<script type="text/javascript" src="include/js/funciones_radicacion_interna.js"></script>
<link   rel="stylesheet" 	   href="include/css/estilos_radicacion_interna.css">
<h1 id="titulo_plantilla_radicacion_interna">
	Plantilla Radicacion Interna
	<div id="vista_completa" title="Entrar en Vista Completa">
		<img id="imagen_vista_completa" src="imagenes/iconos/redimensionar.png">
		<input id="input_vista_completa" value="Entrar en Vista Completa">
	</div>
</h1>
<div id="regeresar_pantalla_normal" class="hidden" title="Regresar a Vista Normal">
	<img id="imagen_vista_completa" src="imagenes/iconos/redimensionar.png">
	<p id="regresar_texto_oculto">
		Regresar a la vista normal
	</p>
</div>
<hr>
<table id='tabla1'>
	<tr>					
		<td id="td_descipcion_codigo_serie" class='descripcion' title='Seleccione el código de la serie documental'>
			Código Serie
		</td>
		<td id="td_detalle_codigo_serie" class='detalle'>
			<select id='codigo_serie'>
				<!-- Este espacio se autollenara cuando termine de cargar la seccion de pagina -->
			</select>
		</td>
		<td id="td_descipcion_codigo_subserie" class='descripcion' title='Seleccione el código de la subserie'>
			Código Subserie
		</td>
		<td id="td_detalle_codigo_subserie" class='detalle'>
			<select id='codigo_subserie'>
				<option value='' disabled selected>--- Seleccione una SubSerie ---</option>
			</select>
		</td>
		<td id="td_descipcion_expediente" class='descripcion' title='Este campo es opcional. No es obligatorio'>
			Expediente al que va a pertenecer éste documento
		</td>
		<td id="td_detalle_expediente" class='detalle'>
			<input id='id_expediente' class='hidden'>
			<input id='id_expediente2' class='hidden'>
			<input id='seleccionar_expediente' placeholder='Este campo es opcional. No es obligatorio' readonly>
			<div id='resultado_seleccionar_expediente'></div>
		</td>
	</tr>
</table>
<div id='formulario_datos_radicado'>
	<table id='tabla_formulario_salida'>
		<tr>
			<td id="sangria_izquierda">
	    	</td>
			<td id="texto_justificado">
				<input id="src_imagen_cabecera" class="hidden">
				<div id="contenedor_imagen_cabecera"></div>
			</td>
			<td id="sangria_derecha">
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td> 
				<div id="div_fecha">
					<input id='ubicacion' value="BOGOTA, D.C. - COLOMBIA - AMERICA">
					<div id='quitar_ubicacion' title='Quitar ubicacion del radicado'>
						<img id='imagen_cerrar_ubicacion' src='imagenes/iconos/cerrar.png'>
					</div>
				    <div class='errores_ubicacion hidden errores'>No se encuentran resultados</div>
				    <input id="fecha" style="border: none; font-size: 18px;">
	            	<div id='ubicacion_resultados' style="margin-top: 30px;"></div>
	            	<div id='ubicacion_null' class='errores' style="margin-top: 15px;">
	            		Debe seleccionar una ubicacion
	            	</div>
	            </div>
	            <div id="tratamiento_and_destinatarios">
	            	<table>
	            		<tr>
	            			<td>
	            				<select id='tratamiento'>
	            					<option value='Doctor(a)' selected>Doctor(a) :</option>
	            					<option value='Doctores' selected>Doctores :</option>
	            					<option value='Estimado(a)' selected>Estimado(a) :</option>
	            					<option value='Estimados' selected>Estimados :</option>
	            					<option value='Ingeniero(a)' selected>Ingeniero(a) :</option>
	            					<option value='Ingenieros' selected>Ingenieros :</option>
	            					<option value='Señor(a)' selected>Señor(a) :</option>
	            					<option value='Señores' selected>Señores :</option>
								</select>
	            			</td>
	            			<td>
	            				<div id='div_agregar_destinatario'>
									<img id="imagen_vista_completa" src="imagenes/iconos/mas.png">
									Agregar Destinatarios
								</div>
	            			</td>
	            		</tr>
	            		<tr>
	            			<td colspan="2">
	            				<div id="destinatarios_final_null" class='errores'>
		        					Debe ingresar por lo menos un(1) destinatario
		        				</div>
	            			</td>
	            		</tr>
	            		<tr id="input_agregar_usuario_inf">
	            			<td colspan="2">
								<input id='usuario_actual_nuevo_inf' class='input_search'>
								<div id='desplegable_resultados_inf'></div>
				            	<div class='errores_destinatarios hidden errores'>
				            		No se encuentran resultados
				            	</div>
	            			</td>
	            		</tr>
	            	</table>
	            </div>
	            <div id="destinatarios">
	            	<table id="tabla_destinatarios">
			            <tbody id='lista_usuarios_nuevos'>
		        		</tbody>
		        	</table>
	        	</div>
	        	<div id="fecha2">
					<span id="asunto_texto" style="float:left">
						Ubicacion del Remitente:
					</span>
					<input id='ubicacion2' value="BOGOTA, D.C. - COLOMBIA - AMERICA">
					<div id='quitar_ubicacion2' title='Quitar ubicacion del radicado'>
						<img id='imagen_cerrar_ubicacion2' src='imagenes/iconos/cerrar.png'>
					</div>
				    <div class='errores_ubicacion2 hidden errores'>
				    	No se encuentran resultados
				    </div>
				    <br>
	            	<div id='ubicacion2_resultados'></div>
	            	<div id='ubicacion2_null' class='errores' style="width: 286px; margin: 13px 0px 0px 198px;">
	            		Debe seleccionar una ubicacion
	            	</div>
	            </div>
	        	<div id="div_asunto">
		        	<span id="asunto_texto">
		        		Asunto:
		        	</span>
		        	<input id='asunto' class='input_search' type='search' placeholder='Ingrese la referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software'>
					<div id="asunto_max" class="errores">
						El asunto no puede tener mas de 500 caracteres. (Actualmente <b><u id='asunto_contadormax'></u></b> caracteres)
					</div>
					<div id='asunto_min' class='errores'>
						La referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software no puede ser menor a 6 caracteres (numeros o letras)
					</div>
					<div id='asunto_null' class='errores'>
						Debe ingresar la referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software
					</div>
					<textarea id='editor_radicacion_interna'></textarea>
					<div id='editor_radicacion_interna_null' class='errores'>Debe ingresar la descripcion del radicado</div>
				</div>
				<div id="atentamente">
					<select id='despedida'>
						<option value='Atentamente' selected>Atentamente</option>
						<option value='Cordialmente'>Cordialmente</option>
						<option value='Cordial Saludo'>Cordial Saludo</option>
					</select>
				</div>
				<div id="div_firmante">
					<input id='firmante' class='input_search' type='search' placeholder="Funcionario que firma éste documento">
					<div id='sugerencias_firmante'></div>
					<div id='error_firmante' class='errores'>
						No se han encontrado resultados
					</div>
					<div id='usuario_seleccionado_firmante'>
						<input id='firmante_seleccionado' class='hidden'>
					</div>
					<div id='firmante_seleccionado_null' class='errores' style="width: 300px;">
						Debe ingresar el nombre del firmante del documento
					</div>
					<div id='indicador_firmante'></div>					
				</div>
				<div id="anexos_elaborado_aprobado">
					<div id="anexos_texto">
						<p id="anexos_texto_mas">
							Anexos :
						</p>
						<input id='anexos' class='info_extra' type='search' placeholder='(Sin anexos)'>
					</div>
					<div id="aprobado_texto">
						<p id="aprobado_texto_mas">
							Aprobado por :
						</p>
						<input id='aprueba' class='info_extra'>
						<input id='id_aprueba'>
						<div id='sugerencias_aprueba'></div>
						<div id='usuario_seleccionado_aprueba'>
							<div class='aprueba'></div>
						</div>
						<br>
						<div id='aprueba_sin_resultados'>
							No se han encontrado resultados
						</div>
						<div id='id_aprueba_null'>
							Debe ingresar el nombre del funcionario que aprueba el documento
						</div>
						<div id='indicador_aprobado'></div>
						<input id="aprobar_documento" class="botones center hidden" type="button" value="Aprobar El Documento" title="Aprobar El Documento" style="width: 178px; font-size: 13px; height: 39px; padding: 8px; position: absolute; margin: -31px 0px 0px 10px; background: #3472807d; color:black">
					</div>
					<div id="elaborado_texto">
						<p id="elaborado_texto_mas">
							Elaborado por:
						</p>
						<input id='id_elabora'>
						<input id='elabora' class='info_extra'>
						<div id='sugerencias_elabora'></div>
						<div class='usuario_elabora'></div>
						<div id='id_elabora_null'>
							Debe ingresar el nombre del funcionario que elabora el documento
						</div>
					</div>
			    </div>
	    	</td>
	    </tr>
	    <tr>
			<td id="sangria_izquierda_piecera">
	    	</td>
			<td id="piecera_td">
				<input id="src_imagen_piecera" class="hidden">
				<div id="piecera_mas"></div>
			</td>
		</tr>
	</table>
	
</div>
<div id="example1"></div>
<center>
	<div id='botones_plantilla_radicacion_interna'>
		<input id='verPdf' class='botones center hidden' type='button' value='Vista Previa del Documento' title='Vista previa del documento antes de guardar PDF generado'>
		<input id='seguir_pdf' class='botones center' type='button' value='Seguir editando documento' title='Seguir editando documento'>
		<span id='contenedor_boton_descargar_plantilla_respuesta'>
			<input id='enviarHtml' class='botones2 center' value='Generar Versión 1 del documento' title='Generar documento en formato PDF con la versión 1 del documento'>";
		</span>
	</div>	
</center>
<div id="ventana_firmar_documento" class="ventana_modal">
    <div class="form">
        <div class="cerrar"><a href='javascript:cerrar_ventana("ventana_solicitar_prestamo");'>Cerrar X</a></div>
        <h1>Formulario aprobar documento</h1>
        <hr>
        <form method="post" autocomplete="off">
            <table border ="0">
                <tr>
                    <td class="descripcion" width="30%">Contraseña del usuario </td>
                    <td class="detalle">
                    	<input type="hidden" id="tipo_aprueba_firma">
                        <input type="password" id="contr_confirma_aprobado" title="Ingrese su password para aprobar aquí." placeholder="Ingrese su password para aprobar aqui">
						<div id="error_contr_confirma_aprobado" class="errores">La contraseña no corresponde al usuario que aprueba el documento</div>
                    </td>   
                </tr>
                <tr>
                    <td class="descripcion">Observaciones :</td>
                    <td class="detalle" colspan="3">
                        <textarea id="observaciones_aprobar_documento" rows="2" style="width:100%;padding:5px;" placeholder="Ingrese las observaciones. Sea lo más específico posible" title="Ingrese las observaciones. Sea lo más específico posible" onblur="trim('observaciones_aprobar_documento')" ></textarea>
                        <div id="observaciones_aprobar_documento_null" class="errores">El mensaje de observaciones es obligatorio</div>
                        <div id="observaciones_aprobar_documento_min" class="errores">El mensaje de observaciones no puede ser menor a 6 caracteres (numeros o letras) </div>
                        <div id="observaciones_aprobar_documento_max" class="errores">El nombre del expediente no puede ser mayor a 200 caracteres. (Actualmente <b><u id='observaciones_aprobar_documento_contadormax'></u></b> caracteres)</div>		
                    </td>
                </tr>
                <tr>
                    <td colspan="2" id="fila_boton_solicitar_documento">
                        <center>
                        	<input type="button" value="Aprobar Documento" class="botones" onclick="validar_aprueba_firma()">
                        <center>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>










<div id="ventana_aprobar_documento" class="ventana_modal">
    <div class="form">
        <div class="cerrar"><a href='javascript:cerrar_ventana("ventana_solicitar_prestamo");'>Cerrar X</a></div>
        <h1>Formulario aprobar documento</h1>
        <hr>
        <form method="post" autocomplete="off">
            <table border ="0">
                <tr>
                    <td class="descripcion" width="30%">Contraseña del usuario </td>
                    <td class="detalle">
                    	<input type="hidden" id="tipo_aprueba_firma">
                        <input type="password" id="contr_confirma_aprobado" title="Ingrese su password para aprobar aquí." placeholder="Ingrese su password para aprobar aqui">
						<div id="error_contr_confirma_aprobado" class="errores">La contraseña no corresponde al usuario que aprueba el documento</div>
                    </td>   
                </tr>
                <tr>
                    <td class="descripcion">Observaciones :</td>
                    <td class="detalle" colspan="3">
                        <textarea id="observaciones_aprobar_documento" rows="2" style="width:100%;padding:5px;" placeholder="Ingrese las observaciones. Sea lo más específico posible" title="Ingrese las observaciones. Sea lo más específico posible" onblur="trim('observaciones_aprobar_documento')" ></textarea>
                        <div id="observaciones_aprobar_documento_null" class="errores">El mensaje de observaciones es obligatorio</div>
                        <div id="observaciones_aprobar_documento_min" class="errores">El mensaje de observaciones no puede ser menor a 6 caracteres (numeros o letras) </div>
                        <div id="observaciones_aprobar_documento_max" class="errores">El nombre del expediente no puede ser mayor a 200 caracteres. (Actualmente <b><u id='observaciones_aprobar_documento_contadormax'></u></b> caracteres)</div>		
                    </td>
                </tr>
                <tr>
                    <td colspan="2" id="fila_boton_solicitar_documento">
                        <center>
                        	<input type="button" value="Aprobar Documento" class="botones" onclick="validar_aprueba_firma()">
                        <center>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
    <div id='resultado_js' class="hidden"></div>
<!-- Hasta aqui el div que contiene ventana modal para solicitar prestamo -->
	<script>
		var timerid = "";
		var selector = "#editor_radicacion_interna";
		var size = 300;
		
	    /*tinymce.get('editor_radicacion_interna').on('blur',function(e){
			validar_editar_textarea();
	    });
		tinymce.get('editor_radicacion_interna').on('click',function(e){
	        $(".tox-silver-sink").slideDown("slow");
	        $("#editor_radicacion_interna_null").slideUp("slow");
	    });
	    tinymce.get('editor_radicacion_interna').on('change',function(e){
	        validar_editar_textarea();
	    });
	    tinymce.get('editor_radicacion_interna').on('keyup',function(e){
	    	$(".tox-silver-sink").slideUp("slow");
	    });
	    window.onscroll = function (){
    	 	$(".tox-silver-sink").slideUp("slow");
	    }
	    $("#contenido").scroll(function (){
    	 	$(".tox-silver-sink").slideUp("slow");
	    });
	    $("#contenido").scroll(function (){
    	 	$(".tox-silver-sink").slideUp("slow");
	    });
	    $("#formulario_datos_radicado").scroll(function (){
    	 	$(".tox-silver-sink").slideUp("slow");
	    });*/
	</script>

<div class='hidden'>
	<input id='codigo_dependencia' value="">









	<input id='destinatarios_final'>
	<input id='id_destinatarios_final'>
	<input id='version'>
	<input id='firmante_login'>
	<input id='aprueba_login'>
	<input id='elabora_login'>
</div>
<?php
	/* Revisar si el radicado es nuevo o se carga un radicado ya existente */
	if(isset($_POST['radicado']) || isset($_POST['tipo_radicacion_interna'])){// Se verifica que las variables esten definidas
		if(isset($_POST['tipo_radicacion_interna'])){// Se verfica que el radicado sea un retorno de un borrador
			$archivos = glob('../../bodega_pdf/plantilla_generada_tmp/*');// Obtenemos todos los ficheros
			$radicado = str_replace(
										".pdf",
										"",
										str_replace(
														"../../bodega_pdf/correo_electronico/baul/".$_SESSION['login']."/",
														"",
														$archivos[0]
													)
									);// El radicado viene con la direccion y la extension del archivo Ej: $radicado = "../../bodega_pdf/correo_electronico/baul/GILBERT/2020AV1111000002704.pdf", con dos str_replace se elimina la extencion ".pdf" y se elimina la direccion"("../../bodega_pdf/correo_electronico/baul/".$_SESSION['login']."/*")" esto para dejar limpio el numero de radiaco Ej: $radicado = "2020AV1111000002704"
		}else{
			$radicado = $_POST['radicado'];
		}
		echo "<script>
					carga_info_radicacion_interna(1, '".$radicado."');
				</script>";
	}else{
		echo '<script>
					$("#fecha").val("'.$fecha_e2.'");
					cargar_elabora("'.$_SESSION['id_usuario'].'", "'.$_SESSION['dependencia'].'", "'.$_SESSION['nombre_dependencia'].'", "'.$_SESSION['nombre'].'", "'.$_SESSION['login'].'", "'.$_SESSION['imagen'].'", "'.$_SESSION['cargo_usuario'].'");
					carga_info_radicacion_interna(2, "");
				</script>';
		
	}
	/* Fin revisar si el radicado es nuevo o se carga un radicado ya existente */
?>