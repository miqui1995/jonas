<?php 
	require_once("../login/validar_inactividad.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Modificacion Rapida</title>
</head>
<body>
	<script type="text/javascript">
/* Validar espacios formulario */
		$('#caja_paquete_tomo').keyup(function(){
			validar_input('caja_paquete_tomo');
		});
		$('#consecutivo_desde').keyup(function(){
			validar_input('consecutivo_desde');
		});
		$('#consecutivo_hasta').keyup(function(){
			validar_input('consecutivo_hasta');
		});
		
		$('#metadato_descriptor').keyup( // Retraso de 1 segundo para validar_input
			delay(function (e) {
				validar_input('metadato_descriptor');
			}, 1000)
		);
		
		$('#nombre_expediente').keyup( // Retraso de 1 segundo para validar_input
			delay(function (e) {
				validar_input('nombre_expediente');
			}, 1000)
		);

		$('#numero_caja_archivo_central').keyup(function(){
			validar_input('numero_caja_archivo_central');
		});
		$('#numero_caja_paquete').keyup(function(){
			validar_input('numero_caja_paquete');
		});
		$('#numero_carpeta').keyup(function(){
			validar_input('numero_carpeta');
		});

		$('#observaciones').keyup( // Retraso de 1 segundo para validar_input
			delay(function (e) {
				validar_input('observaciones');
			}, 1000)
		);
		
		$('#total_folios').keyup(function(){
			validar_input('total_folios');
		});

/* Fin validar espacios formulario */
		// Input con delay de 1 segundo
		$('#numero_caja_archivo_central').keyup(
			delay(function (e) {
			  	var valor = this.value;

			  	if(valor.length=="" || valor.length==" "){
			  		cargar_id("");
					$("#resultado_ubicacion_topografica").html("");
					$("#error_numero_caja_archivo_central").slideUp("slow");
					$("#numero_caja_archivo_central_invalido").slideUp("slow");
			  	}else{
					$.ajax({
						type: 'POST',
						url: 'include/procesar_ajax.php',
						data: {
							'recibe_ajax' 	: 'buscar_ubicacion_topografica',
							'valor'			: valor
						},			
						success: function(respuesta){
							if(respuesta==""){
								$("#error_numero_caja_archivo_central").slideDown("slow");
								$("#numero_caja_archivo_central_invalido").slideUp("slow");
								$("#resultado_ubicacion_topografica").slideUp("slow");
							}else{
								$("#error_numero_caja_archivo_central").slideUp("slow");
							}
							$("#resultado_ubicacion_topografica").slideDown("slow");
							$("#resultado_ubicacion_topografica").html(respuesta);
						}
					})
			  	}

			}, 1000)
		);

		function cargar_id(id_ubicacion){
			$("#id_caja_archivo_central").val(id_ubicacion);
		}
		function insertar_inventario(){
			validar_input_formulario();

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
			}

			if($(".errores").is(":visible")){
				return false;
			}else{
				loading('boton_insertar_inventario');

				var caja_paquete_tomo			= $("#caja_paquete_tomo").val();
				var consecutivo_desde			= $("#consecutivo_desde").val();
				var consecutivo_hasta			= $("#consecutivo_hasta").val();
				var dependencia_expediente 		= $("#codigo_dependencia").val();
				var fecha_final					= $("#fecha_final").val();
				var fecha_inicial				= $("#fecha_inicial").val();
				var id_caja_archivo_central		= $("#id_caja_archivo_central").val();
				var metadato_descriptor 		= $("#metadato_descriptor").val();
				var nombre_expediente 			= $("#nombre_expediente").val();
				var numero_caja_archivo_central	= $("#numero_caja_archivo_central").val();
				var numero_caja_paquete			= $("#numero_caja_paquete").val();
				var numero_carpeta 				= $("#numero_carpeta").val();
				var observaciones 				= $("#observaciones").val();
				var serie_expediente			= $("#codigo_serie").val();
				var subserie_expediente			= $("#codigo_subserie").val();
				var total_folios				= $("#total_folios").val();

				$.ajax({
					type: 'POST', 
					url: 'inventario/ejecuta_query_individual.php',
					data: {
						'tipo_transaccion' 				: 'insertar_inventario_individual',
						'caja_paquete_tomo'				: caja_paquete_tomo,
						'consecutivo_desde'				: consecutivo_desde,
						'consecutivo_hasta'				: consecutivo_hasta,
						'dependencia_expediente'		: dependencia_expediente ,
						'fecha_final'					: fecha_final,
						'fecha_inicial'					: fecha_inicial,
						'id_caja_archivo_central'		: id_caja_archivo_central,
						'metadato_descriptor'			: metadato_descriptor,
						'nombre_expediente'				: nombre_expediente,
						'numero_caja_archivo_central'	: numero_caja_archivo_central,
						'numero_caja_paquete'			: numero_caja_paquete,
						'numero_carpeta'				: numero_carpeta,
						'observaciones'					: observaciones,
						'serie_expediente'				: serie_expediente,
						'subserie_expediente'			: subserie_expediente,
						'total_folios'					: total_folios
					},			
					success: function(resp2){
						$("#resultado_total").html(resp2);
					}
				})
			}
		}
		function oculta_resultado_ubicacion_topografica(){
			$("#resultado_ubicacion_topografica").slideUp("slow");
		}
		function validar_input_formulario(){
			var codigo_serie 	= $("#codigo_serie").val();
			var codigo_subserie = $("#codigo_subserie").val();

			// console.log(codigo_serie+" -"+codigo_subserie+"gg");
			if(codigo_serie==""){
				$("#error_codigo_serie").slideDown("slow");
				return false;
			}else{
				$("#error_codigo_serie").slideUp("slow");
			}

			switch(codigo_subserie){
				case '':
					$("#error_codigo_subserie").slideDown("slow");
					$("#error2_codigo_subserie").slideUp("slow");
					return false;
				break;

				case 'subserie':
					$("#error_codigo_subserie").slideUp("slow");
					$("#error2_codigo_subserie").slideDown("slow");
					return false;
				break;

				default:
					$("#error_codigo_subserie").slideUp("slow");
					$("#error2_codigo_subserie").slideUp("slow");
				break;
			}

		
			if(codigo_subserie!=""){
				$("#error_codigo_subserie").slideUp("slow");
			}

			validar_input('caja_paquete_tomo'); 		// Funcion especificada en include/js/funciones_menu.js
			validar_input('codigo_dependencia');
			validar_input('consecutivo_desde');
			validar_input('consecutivo_hasta');
			validar_input('metadato_descriptor');
			validar_input('nombre_expediente');	
			validar_input('numero_caja_archivo_central');
			validar_input('numero_caja_paquete');
			validar_input('numero_carpeta');
			validar_input('observaciones');
			validar_input('total_folios');
			
			if($(".art").is(":visible")){
				$("#numero_caja_archivo_central_invalido").slideDown("slow");
			}else{
				$("#numero_caja_archivo_central_invalido").slideUp("slow");
			}

		}

		$("#codigo_dependencia").focus();

		/* Funcion para cargar listado de series */
		consulta_listado_series2('','','codigo_serie');
	</script>
	<style type="text/css">
		#ventana form input{ /* Formato de la ventana modal del Formulario de Insertar Inventario */
			border-radius: 8px;
			padding: 6px;
			width: 90%;
		}
		#resultado_ubicacion_topografica{
			display: none;
			max-height: 150px;
			overflow-y: scroll;
		}
	</style>
<?php 
	$query_actualiza_secuencia  = "select max(radicado_jonas) from inventario";
    $fila_max                   = pg_query($conectado,$query_actualiza_secuencia);
    $linea_max                  = pg_fetch_array($fila_max);
    $radicado                   = $linea_max['max'];
    $verifica_secuencia         = intval(substr($radicado,10,7));

    $cod_inventario = $_SESSION['caracteres_depend'];
    switch ($cod_inventario) {
     	case '3':
     		$codigo_inventario ='INV';
     		break;
     	case '4':
     		$codigo_inventario ='INVE';
     		break;	
     	case '5':
     		$codigo_inventario ='INVEN';
     		break;	
     } 
?>
	<div id="ventana" class="ventana">
		<h1 class="center">Formulario Insertar a Inventario </h1>
		<hr>
		<form enctype="multipart/form-data" method="post" id ="formulario_insertar_inventario_individual" name ="formulario_insertar_inventario_individual" autocomplete="off">
			<input type="hidden" name="numero_radicado" id="numero_radicado">
			<input type="hidden" name="tipo_modificacion" id="tipo_modificacion">
<!--			<input type="hidden" name="MAX_FILE_SIZE" value="3000000" /> -->
			<hr>
			<center>
				<table border="0">
					<tr>					
						<td class="descripcion" width="20%">
							<input type="hidden" id="codigo_dependencia" <?php echo "value='$codigo_inventario'"; ?> disabled>
							Codigo Serie :
						</td>
						<td class="detalle" width="30%" colspan="3">
							<select id="codigo_serie" title="Seleccione el código de la serie documental" class="select_opciones" <?php echo "onchange='cargar_codigo_subserie2(this.value,\"\",\"$codigo_inventario\",\"trd_inventario\",\"codigo_subserie\")'" ?> ></select>
							<div id="error_codigo_serie" class="errores">Debe seleccionar por lo menos una serie del listado</div>
						</td>
						<td class="descripcion" width="20%">
							Codigo Subserie
						</td>
						<td class="detalle" width="30%" colspan="3">
							<select id="codigo_subserie" title="Seleccione el código de la serie documental" class="select_opciones" onchange="validar_input_formulario()"><option value="">No hay subseries asociadas a la serie seleccionada</option></select>
							<div id="error_codigo_subserie" class="errores">No existen subseries asociadas a la serie seleccionada</div>
							<div id="error2_codigo_subserie" class="errores">Debe seleccionar por lo menos una subserie del listado</div>
						</td>
					</tr>
					<tr>
						<td class="descripcion" width="15%">
							Asunto o Descripción (Nombre del expediente o carpeta)
						</td>
						<td class="detalle" colspan="3">
							<textarea name="nombre_expediente" id="nombre_expediente" rows="2" style="width:95%;padding:5px;" onblur="validar_input('nombre_expediente')" placeholder="Ingrese el asunto del expediente o carpeta. Sea lo más específico posible" title="Ingrese el asunto del expediente o carpeta. Sea lo más específico posible" maxlength="500"></textarea>
							<div id="nombre_expediente_null" class="errores">El asunto o nombre del expediente es obligatorio</div>
							<div id="nombre_expediente_min" class="errores">El asunto o nombre del expediente no puede ser menor a 6 caracteres (numeros o letras) </div>
							<div id="nombre_expediente_max" class="errores">El asunto o nombre del expediente no puede ser mayor a 500 caracteres (numeros o letras)</div>
						</td>
						<td class="descripcion">
							Fecha Inicial :
						</td>
						<td class="detalle">
							<input type="date" name="fecha_inicial" id="fecha_inicial" placeholder="DD/MM/AAAA" title="Ingrese la fecha inicial del expediente o carpeta" style="width:90%;">
						</td>
						<td class="descripcion">
							Fecha Final
						</td>
						<td class="detalle">
							<input type="date" name="fecha_final" id="fecha_final" placeholder="DD/MM/AAAA" title="Ingrese la fecha final del expediente o carpeta (Si el expediente está cerrado)" style="width:90%;">
						</td>
					</tr>
					<tr>
						<td class="descripcion" width="15%">
							Descriptores (Metadatos)
						</td>
						<td class="detalle" colspan="3">
							<textarea name="metadato_descriptor" id="metadato_descriptor" rows="2" style="width:95%;padding:5px;" onblur="validar_input('metadato_descriptor')" placeholder="Ingrese metadatos o descriptores para ubicar el expediente o carpeta luego. Sea lo más específico posible" title="Ingrese metadatos o descriptores para ubicar el expediente o carpeta luego. Sea lo más específico posible" maxlength="500"></textarea>
							<div id="metadato_descriptor_min" class="errores">El metadato o descriptor no puede ser menor a 6 caracteres (numeros o letras) </div>
							<div id="metadato_descriptor_max" class="errores">El metadato o descriptor no puede ser mayor a 500 caracteres (numeros o letras)</div>
						</td>

						<td class="descripcion">
							Caja, paquete, tomo u otro
						</td>
						<td class="detalle">
							<input type="search" placeholder="Ingrese la unidad de conservación 1" title="Ingrese la unidad de conservación 1" id="caja_paquete_tomo" name="caja_paquete_tomo" onblur="validar_input('caja_paquete_tomo')" maxlength="10">
							<div id="caja_paquete_tomo_max" class="errores">La caja, paquete o tomo no puede ser mayor a 10 caracteres.</div>
						</td>
						<td class="descripcion">
							Numero caja - Paquete
						</td>
						<td class="detalle">
							<input type="search" name="numero_caja_paquete" id="numero_caja_paquete" placeholder="Ingrese la unidad de conservación 2" title="Ingrese la unidad de conservación 2" onblur="validar_input('numero_caja_paquete')" maxlength="10">
							<div id="numero_caja_paquete_max" class="errores">El número de caja o paquete no puede ser mayor a 10 caracteres.
							</div>
						</td>
					</tr>

					<tr>	
						<td class="descripcion" width="15%">
							Numero de Carpeta
						</td>	
						<td class="detalle">
							<input type="search" name="numero_carpeta" id="numero_carpeta" placeholder="Ingrese la unidad de conservación 3" title="Ingrese la unidad de conservación 3" onblur="validar_input('numero_carpeta')" maxlength="20">
							<div id="numero_carpeta_max" class="errores">El número de carpeta no puede ser mayor a 20 caracteres.
						</td>
						<td class="descripcion">
							Total Folios
						</td>
						<td class="detalle" align="left">
							<input type="search" name="total_folios" id="total_folios" placeholder="Ingrese la cantidad de folios del expediente o carpeta" title="Ingrese la cantidad de folios del expediente o carpeta" onblur="validar_input('total_folios')" maxlength="5">
							<div id="error_total_folios" class="errores">La cantidad de folios debe ser numérico.</div>
							<div id="total_folios_max" class="errores">La cantidad de folios no puede ser mayor a 5 caracteres.
						</td>
						<td class="descripcion">
							Consecutivo desde
						</td>
						<td class="detalle" align="left">
							<input type="search" name="consecutivo_desde" id="consecutivo_desde" placeholder="Ingrese la cantidad de folios del expediente o carpeta" title="Ingrese la cantidad de folios del expediente o carpeta" onblur="validar_input('consecutivo_desde')" maxlength="20">
							<div id="consecutivo_desde_max" class="errores">El consecutivo desde no puede ser mayor a 20 caracteres.
							</div>
						</td>
						<td class="descripcion">
							Consecutivo hasta
						</td>
						<td class="detalle" align="left">
							<input type="search" name="consecutivo_hasta" id="consecutivo_hasta" placeholder="Ingrese la cantidad de folios del expediente o carpeta" title="Ingrese la cantidad de folios del expediente o carpeta" onblur="validar_input('consecutivo_hasta')" maxlength="20">
							<div id="consecutivo_hasta_max" class="errores">El consecutivo hasta no puede ser mayor a 20 caracteres.
							</div>
						</td>
<!-- 						
						<td class="detalle">
							<input type="file" name="archivo_pdf_radicado" id="archivo_pdf_radicado" onchange="validar_input('archivo_pdf_radicado')">
							<div id="archivo_pdf_radicado_invalido" class="errores"> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
							<div id="archivo_pdf_radicado_tamano" class="errores">El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
						</td>
-->					</tr>
					<tr>
						<td class="descripcion" width="15%">
							Observaciones
						</td>
						<td class="detalle" colspan="3">
							<textarea name="observaciones" id="observaciones" rows="2" style="width:95%;padding:5px;" onblur="validar_input('observaciones')" placeholder="Ingrese las observaciones sobre el expediente o carpeta. Sea lo más específico posible" title="Ingrese las observaciones sobre el expediente o carpeta. Sea lo más específico posible" maxlength="200"></textarea>
							<div id="observaciones_max" class="errores">Las observaciones no puede ser mayor a 200 caracteres (numeros o letras)</div>
						</td>					
						<td class="descripcion">	
							Numero de caja (Archivo Central)<br>
						</td>
						<td class="detalle" colspan="3">
							<input type="search" id="numero_caja_archivo_central" name="numero_caja_archivo_central" placeholder="Ingrese el numero de caja (Archivo central)" title="Numero de la caja en la que se va a conservar en el archivo central" onblur="validar_input('numero_caja_archivo_central')" onkeyup="loading('resultado_ubicacion_topografica')" >
							<input type="hidden" id="id_caja_archivo_central" name="id_caja_archivo_central" >
							<div id="numero_caja_archivo_central_max" class="errores">El numero de caja no puede ser mayor a 50 caracteres (numeros o letras)</div>
							<div id="error_numero_caja_archivo_central" class="errores">El numero de caja no existe en el inventario. Ingrese por favor un numero de caja válido</div>	
							<div id="numero_caja_archivo_central_invalido" class="errores">Debe seleccionar un numero de caja válido.</div>	
							<div id="resultado_ubicacion_topografica"></div>
						</td>
					</tr>
				</table>
			</center>
		</form>
		<center>
			<div id="boton_insertar_inventario">
				<input type="button" value="Insertar a Inventario" onclick="insertar_inventario()" class="botones">				
			</div>
		</center>				
	</div>	
<?php 
	echo "<div id='resultado_total'></div>";
 ?>	
</body>
</html>