<?php 
	require_once("../../login/validar_inactividad.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Radicacion Normal</title>
	<script type="text/javascript">
/* Validar espacios formulario */
		$('#archivo_pdf_radicado').change( // Al cambiar archivo_pdf_radicado
			delay(function (e) {
				validar_input_file('archivo_pdf_radicado'); 
				$("#archivo_pdf_radicado_error").slideUp("slow");
			}, 1000)			
		)
		$('#asunto_radicado').keyup( // Retraso de 1 segundo para validar_input
			delay(function (e) {
				validar_input('asunto_radicado');
			}, 1000)
		);
		
		$('#seleccionar_expediente').keyup( // Retraso de 1 segundo para validar_input
			delay(function (e) {
				validar_input('seleccionar_expediente');
				reset_archivo_asunto();
				
				var valor=this.value;

				if(valor.length>2 && valor.length<101){ // en la funcion validar_input define min y max
					var codigo_dependencia 	= $("#codigo_dependencia").val();
					var codigo_serie 		= $("#codigo_serie").val();
					var codigo_subserie 	= $("#codigo_subserie").val();

					$.ajax({
						type: 'POST',
						url: 'include/procesar_ajax.php',
						data: {
							'recibe_ajax' 		: 'seleccionar_expediente',
							'dependencia' 		: codigo_dependencia,
							'serie'				: codigo_serie,
							'subserie'			: codigo_subserie,
							'search_expediente' : valor
						},			
						success: function(respuesta){
							// console.log(respuesta)
							$("#resultado_seleccionar_expediente").html("");

							switch(respuesta){
								case 'sin_registros':
									$("#error_seleccionar_expediente").slideDown("slow");
									break;
								default:
									$("#error_seleccionar_expediente").slideUp("slow");
									$("#resultado_seleccionar_expediente").html(respuesta);
									break;
							}
						}
					})
				}else{
					$("#resultado_seleccionar_expediente").html("");
				}
			}, 1000)
		);

		function cargar_input_expediente(id_expediente,asunto){
			$("#seleccionar_expediente").val("("+id_expediente+") "+asunto);
			$("#id_expediente").val(id_expediente);

			$(".art").slideUp("slow");
			$(".input_asunto_expediente").slideDown("slow");
			// $("#asunto_radicado").focus();
		}
		function insertar_radicacion_normal(){
			validar_input_formulario();
		
			if($('.errores').is(":visible")){
				return false;
			}else{	
				if($('.imagen_logo').is(":visible")){
					// sweetAlert({
					Swal.fire({	
						position 			: 'top-end',
					    showConfirmButton 	: false,
					    timer 				: 1500,	
					    title 				: 'La consulta se está ejecutando.',
					    text 				: 'Un momento por favor.',
					    type 				: 'warning'
					});
					return false;					
				}else{
					var archivo = $("#archivo_pdf_radicado").val();
					if(archivo==""){
						$("#archivo_pdf_radicado_error").slideDown("slow");
					}else{
						$("#archivo_pdf_radicado_error").slideUp("slow");
						loading('boton_radicacion_normal');
						
						if($(".errores").is(":visible")){
							return false;
						}else{
							var codigo_dependencia 	= $("#codigo_dependencia").val();
							var codigo_serie 		= $("#codigo_serie").val();
							var codigo_subserie 	= $("#codigo_subserie").val();
							var id_expediente 		= $("#id_expediente").val();
							var asunto_radicado 	= $("#asunto_radicado").val();

							var inputFileRadicado = document.getElementById('archivo_pdf_radicado');
				            var file = inputFileRadicado.files[0];

							var data = new FormData();

							data.append('recibe_ajax','radicacion_normal');
							data.append('archivo_pdf_radicado',file);
							data.append('codigo_dependencia',codigo_dependencia);
							data.append('codigo_serie',codigo_serie);
							data.append('codigo_subserie',codigo_subserie);
							data.append('id_expediente',id_expediente);
							data.append('asunto_radicado',asunto_radicado);

							$.ajax({
								type: 'POST',
								url: 'include/procesar_ajax.php',
								data: data,			
						        contentType:false,
						        processData:false,
								success: function(resp){
									console.log(resp)
									if(resp!=""){
										$('#resultado_js').html(resp);
									}
								}
							})
						}
					}
				}
			}	
		}

		function reset_archivo_asunto(){
			$(".art").slideUp("slow");
			$(".input_asunto_expediente").slideUp("slow");

			$("#archivo_pdf_radicado").val("");
			$("#asunto_radicado").val("");
		}

		function reset_expediente(){
			$(".art").slideUp("slow");
			$(".errores").slideUp("slow");
			$(".input_seleccionar_expediente").slideUp("slow");
			
			$("#id_expediente").val("");
			$("#seleccionar_expediente").val("");

			reset_archivo_asunto();
		}

		function validar_input_formulario(){
			var codigo_serie 	= $("#codigo_serie").val();
			var codigo_subserie = $("#codigo_subserie").val();

			validar_input('asunto_radicado');

			if(codigo_serie==""){
				$("#error_codigo_serie").slideDown("slow");
				return false;
			}else{
				$("#error_codigo_serie").slideUp("slow");

				switch(codigo_subserie){
					case '':
						$("#error_codigo_subserie").slideDown("slow");
						$("#error2_codigo_subserie").slideUp("slow");
						$(".input_seleccionar_expediente").slideUp("slow");
						$("#seleccionar_expediente").val("");
						// return false;
					break;

					case 'subserie':
						$("#error_codigo_subserie").slideUp("slow");
						$("#error2_codigo_subserie").slideDown("slow");
						$(".input_seleccionar_expediente").slideUp("slow");
						$("#seleccionar_expediente").val("");
						return false;
					break;

					default:
						$("#error_codigo_subserie").slideUp("slow");
						$("#error2_codigo_subserie").slideUp("slow");
						$(".input_seleccionar_expediente").slideDown("slow");

						var codigo_dependencia 	= $("#codigo_dependencia").val();
						var serie 				= $("#codigo_serie").val();
						var subserie 			= $("#codigo_subserie").val();

						$.ajax({
					        type: 'POST',
					        url: 'include/procesar_ajax.php',
					        data: {
					            'recibe_ajax' 	: 'seleccionar_expediente_dependencia',
					            'dependencia' 	: codigo_dependencia,
					            'serie' 		: serie,
					            'subserie' 		: subserie
					        },          
					        success: function(respuesta){
				            	$("#resultado_seleccionar_expediente").html(respuesta)		            
					        }
					    })
						// $("#seleccionar_expediente").focus();
					break;
				}
			}
		}
	</script>
</head>
<body>
<?php 
// var_dump($_SESSION);
$dependencia = $_SESSION['dependencia'];
?>
	<div id="ventana" class="ventana">
		<h1 class="center">Formulario Radicación Normal</h1>
		
		<form enctype="multipart/form-data" method="post" id ="formulario_radicacion_normal" name ="formulario_radicacion_normal" autocomplete="off" onsubmit="return false;">
		<!-- 	<input type="hidden" name="numero_radicado" id="numero_radicado">
			<input type="hidden" name="tipo_modificacion" id="tipo_modificacion"> -->
<!--			<input type="hidden" name="MAX_FILE_SIZE" value="3000000" /> -->
			<hr>
			<center>
				<table border="0">
					<tr>					
						<td class="descripcion" width="20%">
							<input type="hidden" id="codigo_dependencia" value="<?php echo $dependencia; ?>" disabled>
							Codigo Serie :
						</td>
						<td class="detalle" width="30%">
							<select id="codigo_serie" title="Seleccione el código de la serie documental" class="select_opciones" <?php echo "onchange='cargar_codigo_subserie2(this.value,\"\",\"$dependencia\",\"radicacion_normal\",\"codigo_subserie\")'"; ?>>
							 </select>
							<div id="error_codigo_serie" class="errores">Debe seleccionar por lo menos una serie del listado</div>
						</td>
						<td rowspan="5" colspan="2">
							<iframe id="viewer" frameborder="0" scrolling="yes" width="100%" height="400" style='display: none;'>
							</iframe> 
						</td>
					</tr>
					<tr>
						<td class="descripcion" width="20%">
							Codigo Subserie
						</td>
						<td class="detalle" width="30%" >
							<select id="codigo_subserie" title="Seleccione el código de la serie documental" class="select_opciones" onchange="reset_expediente(); validar_input_formulario()"><option value="">No hay subseries asociadas a la serie seleccionada</option></select>
							<div id="error_codigo_subserie" class="errores">No existen subseries asociadas a la serie seleccionada</div>
							<div id="error2_codigo_subserie" class="errores">Debe seleccionar por lo menos una subserie del listado</div>
						</td>
					</tr>
					<tr class="input_seleccionar_expediente hidden">
						<td class="descripcion ">
							Expediente al que pertenece éste documento
						</td>
						<td class="detalle">
							<input type="hidden" id="id_expediente" disabled>	
							<input type="search" id="seleccionar_expediente" class="input_search" placeholder="Ingrese el número de expediente o asunto del expediente">
							
							<div id="seleccionar_expediente_max" class="errores">El campo de expediente no puede ser mayor a 100 caracteres (numeros o letras)</div>
							<div id="seleccionar_expediente_min" class="errores">El campo de expediente no puede ser menor a 3 caracteres (numeros o letras)</div>
							<div id="seleccionar_expediente_null" class="errores">El asunto o nombre del expediente es obligatorio</div>
							<div id="error_seleccionar_expediente" class="errores">El numero o asunto del expediente no existe en el inventario. Ingrese por favor un numero o asunto de expediente válido</div>	
							<div id="seleccionar_expediente_invalido" class="errores">Debe seleccionar un asunto o nombre de expediente válido.</div>	

							<div id="resultado_seleccionar_expediente" style="overflow-x: auto;max-height: 100px;"></div>
						</td>
					</tr>
					<tr class="input_asunto_expediente hidden">
						<td class="descripcion" width="15%">
							Asunto o Descripción (Nombre del documento)
						</td>
						<td class="detalle" >
							<textarea id="asunto_radicado" rows="3" class="input_search" placeholder="Ingrese el asunto del documento. Sea lo más específico posible" title="Ingrese el asunto del documento. Sea lo más específico posible"></textarea>
							<div id="asunto_radicado_null" class="errores">El asunto o nombre del expediente es obligatorio</div>
							<div id="asunto_radicado_min" class="errores">El asunto o nombre del expediente no puede ser menor a 6 caracteres (numeros o letras) </div>
							<div id="asunto_radicado_max" class="errores">El asunto o nombre del expediente no puede ser mayor a 500 caracteres (numeros o letras)</div>
						</td>
					</tr>
					<tr class="input_asunto_expediente hidden">
						<td class="descripcion">
							Archivo PDF (Imagen Principal del Radicado) 
						</td>
						<td class="detalle input_asunto_expediente hidden" width="35%">
							<input type="file" name="archivo_pdf_radicado" id="archivo_pdf_radicado">
							<div id="archivo_pdf_radicado_error" class="errores"> El archivo a cargar es un campo obligatorio. El sistema solo admite formato PDF</div>
							<div id="archivo_pdf_radicado_invalido" class="errores"> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
							<div id="archivo_pdf_radicado_tamano" class="errores">El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
						</td>
					</tr>
				</table>
			</center>
		</form>
		<center>
			<div id="boton_radicacion_normal" class="input_asunto_expediente hidden">
				<input type="button" value="Radicar" onclick="insertar_radicacion_normal()" class="botones">				
			</div>
		</center>				
	</div>	
<?php 
	echo "<div id='resultado_total'></div>";
 ?>	
<script>
	// verifica secuencia radicacion interna
	valida_sec('3');

	<?php echo "setTimeout('consulta_listado_series2(\"\",\"$dependencia\",\"codigo_serie\");', 500);" ?>
</script>
</body>
</html>