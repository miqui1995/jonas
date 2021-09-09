<?php
// Si la sesion no existe
	if(!isset($_SESSION)){
		//Se inicia sesion
		session_start();
	}
	require_once "../login/validar_inactividad.php";
	require_once "../login/conexion2.php"; 				
	/*Aqui defino la fecha para mostrar en formato "Jueves 05 de Mayo de 2020" */
	require_once "../include/genera_fecha.php";

?>
<!DOCTYPE html>
<html>
<head>
	<title>Buscador de Metadatos</title>
</head>
	<style type="text/css">
		#img_cerrar:hover{
			background-color:red;
		}
	</style>
	<!--Estructuracion jquery funcion series_subseries, crear select, pequeño formulario creacion metadatos-->
	<script type='text/javascript'>
		function abrir_ventana_crear_metadato() {
			consulta_listado_series2('','','codigo_serie')

		    $("#ventana").slideDown("slow");
		    $("#codigo_serie").focus();

		    $("#contenido").css({
		        'z-index': '100'
		    }); // Modifico estilo para sobreponer ventana modal 
		}

		function agregar_desplegable(numero_metadato){
			var valor  				= $("#valor_desplegable_metadato"+numero_metadato).val().toLowerCase(); // Funcion para poner en minuscula toda la palabra
			var valores_actuales 	= $("#valores_desplegable_metadato"+numero_metadato).val().toLowerCase(); // Funcion para poner en minuscula toda la palabra 

			if(valores_actuales==""){
				$("#valores_desplegable_metadato"+numero_metadato).val(valor);
				$("#valor_desplegable_metadato"+numero_metadato).val("");
				$("#valor_desplegable_metadato"+numero_metadato).focus();
			}else{
				if(valores_actuales.includes(valor)){
					$("#valor_error_desplegable"+numero_metadato).html("<b>"+valor+"</b>");
					$("#error_desplegable"+numero_metadato).slideDown("slow");
					$("#valor_desplegable_metadato"+numero_metadato).focus();
				}else{
					$("#error_desplegable"+numero_metadato).slideUp("slow");
					$("#valores_desplegable_metadato"+numero_metadato).val(valores_actuales+","+valor);

					$("#valor_desplegable_metadato"+numero_metadato).val("");
					$("#valor_desplegable_metadato"+numero_metadato).focus();
				}
			}
			
			var valor_select = $("#valores_desplegable_metadato"+numero_metadato).val().toLowerCase(); // Funcion para poner en minuscula toda la palabra 

			var listado_desplegable = valor_select.split(",");
			var opciones_ld = "";

			for (var ld = 0; ld < listado_desplegable.length; ld++) {
				opciones_ld+="<option>"+listado_desplegable[ld]+"</option>";
			}

			$("#desplegable_metadato"+numero_metadato).html(opciones_ld);
		}

		function ocultar_errores(){
		    $(".errores").slideUp("slow");
		}

		function consultar_metadatos_serie_subserie(){
			$("#nombre_metadato1").focus();
			console.log("Hay que verificar serie_subserie")
		}

		function cerrar_metadato(numero_metadato){
			$("#metadato"+numero_metadato).slideUp("slow");
			$("#nombre_metadato"+numero_metadato).val("");
		}

		function ocultar_desplegable(numero_metadato){
			var tipo_metadato = $("#tipo_metadato"+numero_metadato).val();

			if(tipo_metadato == 'desplegable'){
				$(".opcion_desp"+numero_metadato).removeClass("hidden"); // Quitar class='hidden' para mostrar
				$("#valor_desplegable_metadato"+numero_metadato).focus();
			}else{
				$(".opcion_desp"+numero_metadato).addClass("hidden"); // Agregar class='hidden' para ocultar
				$("#valores_desplegable_metadato"+numero_metadato).val("")
				$("#valor_desplegable_metadato"+numero_metadato).val("");
				$("#error_desplegable"+numero_metadato).slideUp("slow");
				$("#desplegable_metadato"+numero_metadato).html("");
			}
		}

		function mostrar_opcion_metadato(){
			var a = $("#cantidad_metadato").val();
			$("#metadato"+a).slideDown("slow");

			var b = (parseInt(a) + parseInt('1'));
			$("#cantidad_metadato").val(b)
		}

		function valida_requiere_anexo(numero_metadato){
			var archivo_anexo = $("#archivo_anexo"+numero_metadato).val();

			if(archivo_anexo=='SI'){
				$(".tipo_archivo"+numero_metadato).removeClass("hidden"); // Quitar class='hidden' para mostrar
			}else{
				$(".tipo_archivo"+numero_metadato).addClass("hidden"); // Agregar class='hidden' para ocultar
			}
		}

		function submit_agregar_metadatos(){
			 /*Valida si el gif de cargando esta visible cuando envia el formulario */
		    if ($('.imagen_logo').is(":visible")) {
		        Swal.fire({
		            position            : 'top-end',
		            showConfirmButton   : false,
		            timer               : 1500,
		            title               : 'La consulta se está ejecutando.',
		            text                : 'Un momento por favor.',
		            type                : 'warning'
		        });
		    }else{
		    /* Si el GIF no esta visible se hace lo siguiente */
				
		        /* Se inicia con el armado del JSON con la información de los input requeridos para capturar los metadatos */
		        var insertar_metadato = "{";
		        var contador_json = 1;
		        for(var h=1; h<11; h++){
		        	var validar_metadato_vacio = $("#nombre_metadato"+h).val();
		        	if(validar_metadato_vacio!=""){
		        		insertar_metadato+='"'+contador_json+'":{"nombre_metadato":"'+$("#nombre_metadato"+h).val()+'","obligatorio":"'+$("#obligatorio"+h).val()+'","archivo_anexo":"'+$("#archivo_anexo"+h).val()+'","tipo_archivo":"'+$("#tipo_archivo_anexo"+h).val()+'","tipo_metadato":"'+$("#tipo_metadato"+h).val()+'","valores_desplegable":"'+$("#valores_desplegable_metadato"+h).val()+'"},';
			        	contador_json++;
		        	}

		        	if($("#nombre_metadato"+h).is(":visible")){
		        		if($("#nombre_metadato"+h).val()==""){
		        			$("#error_nombre_metadato"+h).slideDown("slow");
		        		}
		        	}

		        	if($("#valor_desplegable_metadato"+h).is(":visible")){
		        		if($("#valores_desplegable_metadato"+h).val()==""){
		        			$("#error_valores_desplegable_metadato"+h).slideDown("slow");
		        		}
		        	}
		        }
	        	insertar_metadato=insertar_metadato.slice(0,-1)+"}";
        	  /* Fin con el armado del JSON con la información de los input requeridos para capturar los metadatos */

        	  	var serie 		= $("#codigo_serie").val();
        	  	var subserie 	= $("#codigo_subserie").val();

        	  	if(serie==""){
        	  		$("#error_codigo_serie").slideDown("slow");
        	  	}

        	  	if(subserie=="subserie"){
        	  		$("#error_codigo_subserie").slideDown("slow");
        	  	}

        	  	if($(".errores").is(":visible")){
        	  		return false;
        	  	}else{
        	  		 $.ajax({
				        type: 'POST',
				        url: 'include/procesar_ajax.php',
				        data: {
				            'recibe_ajax'       : 'ingresar_metadato',
				            'codigo_serie'      : serie,
				            'codigo_subserie' 	: subserie,
				            'json_recibido'		: insertar_metadato
				        },
				        success: function(resp) {
				            if (resp != "") {
			        	  		console.log(resp)
				                // $("#contenedor_crear_documentos").html(resp);
				            }
				        }
				    })

        	  		
        	  	}
			}	
		}
	</script>
<body id="body_ubicacion_series_subseries">
<?php
	/* Se define la variable $codigo_dependencia para enviarla en la funcion cargar_codigo_subserie2(this.value,\"\",\"$codigo_dependencia\",\"trd_inventario\",\"codigo_subserie\")*/ 
	$codigo_dependencia = $_SESSION['caracteres_depend'];
	
	if ($codigo_dependencia == 3){
		$codigo_dependencia ="INV";
	} elseif ($codigo_dependencia == 4){
		$codigo_dependencia ="INVE";
	} elseif ($codigo_dependencia == 5){
		$codigo_dependencia ="INVEN";
	}
	
	$usuario  	= $_SESSION['login'];   // Desde la sesion define la variable $usuario
	// $timestamp  = date('Y-m-d H:i:s');	// Genera la fecha de transaccion

	$query_metadatos = "select distinct m.codigo_serie, s.nombre_serie, m.codigo_subserie, s.nombre_subserie, m.nombre_metadato, m.tipo_metadato, m.campo_obligatorio, m.requiere_archivo_anexo, m.tipo_archivo_anexo, m.opciones_desplegable from metadatos_expedientes m join subseries s on m.codigo_serie=s.codigo_serie and m.codigo_subserie=s.codigo_subserie
		order by m.codigo_serie, m.codigo_subserie,m.nombre_metadato
		";
	$fila_query_metadatos 			= pg_query($conectado,$query_metadatos);
	$registros_query_metadatos 		= pg_num_rows($fila_query_metadatos);
			if($registros_query_metadatos==0){	
			/*Condicion si la consulta no encuentra resultados*/
				echo "
				<div style='padding:20px;'>
				<h2> No se han encontrado resultados </h2>
				<p>Si desea ingresar un nuevo metadato haga click <a id='agregar_metadatos' href='#' onclick='abrir_ventana_crear_metadato()'>aqui</a></p>
				</div>
				";
			}else{
			/* Si existen registros*/

				$tabla_subseries = "";

				// Se imprime las filas con bucle "for" que viene desde la variable $registros_query_metadatos
				// La variable contador se usa para la columna id como autoincrementable
				$contador = 1;

				//Se recorre el resultado de la query con un for
    			for ($i=0; $i < $registros_query_metadatos ; $i++){
			    	$linea_consulta_metadatos  	= pg_fetch_array($fila_query_metadatos); 	
					$nombre_serie              	= $linea_consulta_metadatos['nombre_serie'];
					$nombre_subserie           	= $linea_consulta_metadatos['nombre_subserie'];

					$codigo_serie           	= $linea_consulta_metadatos['codigo_serie'];
					$codigo_subserie           	= $linea_consulta_metadatos['codigo_subserie'];
					$nombre_metadato           	= $linea_consulta_metadatos['nombre_metadato'];
					$tipo_metadato           	= $linea_consulta_metadatos['tipo_metadato'];
					$campo_obligatorio         	= $linea_consulta_metadatos['campo_obligatorio'];
					$requiere_archivo_anexo    	= $linea_consulta_metadatos['requiere_archivo_anexo'];
					$tipo_archivo_anexo        	= $linea_consulta_metadatos['tipo_archivo_anexo'];
					$opciones_desplegable  		= $linea_consulta_metadatos['opciones_desplegable'];
					// $codigo_serie_subserie 		= "$codigo_serie-$codigo_subserie";

					$opciones = "";

					if($requiere_archivo_anexo=="SI"){
						$opciones.= "Requiere archivo anexo($tipo_archivo_anexo)";

					}

					if($tipo_metadato=='desplegable'){
						$opciones.="Las opciones del desplegable son: [$opciones_desplegable]";
					}

					
					$nombre_serie_subserie     	= "<b>Serie </b>($codigo_serie)[$nombre_serie] - <b>Subserie </b>($codigo_subserie)[$nombre_subserie]";
			
					$tabla_subseries.="
					<tr class='detalle center fila_serie' onclick=\"\">
						<td>$contador</td>
						<td style='padding:10px; text-align:left;'> $nombre_serie_subserie</td>
						<td>$nombre_metadato</td>
						<td>$tipo_metadato</td>
						<td>$campo_obligatorio</td>
						<td>$opciones</td>
					</tr>";
					$contador++;
				}//Fin del bucle "for"
			
		?>
			<h1 style="margin-top:-10px;">Metadatos</h1>
			<table border='0' width='100%'>
				<tr>
					<td class='descripcion center' style="width: 10px;">Id</td>
					<td class='descripcion center' >Nombre Serie - Subserie</td>
					<td class='descripcion center' >Nombre Metadato</td>
					<td class='descripcion center' >Tipo Metadato</td>
					<td class='descripcion center' >Obligatorio</td>
					<td class='descripcion center' >Opciones</td>
				</tr>
		<?php
				echo $tabla_subseries;
				echo "</table></div>"; 	// Fin de tabla
				echo "
				<div style='padding:20px;'>
				<p>Si desea ingresar un nuevo metadato haga click <a id='agregar_metadatos' href='#' onclick='abrir_ventana_crear_metadato()'>aqui</a></p>
				</div>
				";
			}/* Fin condicion si existen registros asociados a el usuario */
		?>
	</center>
<!--Desde aqui el div que contiene el formulario para agregar metadato -->

		<div id="ventana" class="ventana_modal">
			<div class="form" style="overflow: scroll; max-height: 80vh;">
				<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nuevo Metadato</h1>
				<hr>
				<form method="post" id="form_exp" enctype="multipart/form-data" autocomplete="off">
					<table border ="1">
						<tr>
							<td class='descripcion'>
								<input type="hidden" id="tipo_formulario_metadato" value="crear_metadato">
								<input type="hidden" id="cantidad_metadato" value="2">
								Serie
							</td>
							<td class='detalle'>
								<select id='codigo_serie' title='Seleccione el código de la serie documental' class='select_opciones' <?php echo "onchange='ocultar_errores();cargar_codigo_subserie2(this.value,\"\",\"$codigo_dependencia\",\"trd_inventario\",\"codigo_subserie\")'";?>
								>
									<option value=''>No hay series asociadas a la dependencia seleccionada</option>
								</select>
								<div id="error_codigo_serie" class="errores">Debe seleccionar por lo menos una serie del listado</div>
							</td>
							<td class='descripcion'>
								Subserie
							</td>
							<td class='detalle'>
								<select id='codigo_subserie' class='select_opciones' <?php echo "onchange='consultar_metadatos_serie_subserie(); ocultar_errores()'";?>>	
			  						<option value='' >No hay subseries asociadas a la serie seleccionada</option>
								</select>
								<div id="error_codigo_subserie" class="errores">Debe seleccionar por lo menos una subserie del listado</div>
							</td>
						</tr>
					</table>		
					<div id="tabla_metadatos_por_cargar">
						<table border="1">
							<tr id="metadato1">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('1')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 1</td>
								<td class="detalle">
									<input type="search" id="nombre_metadato1" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato1" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio1' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo1' class='select_opciones' onchange="valida_requiere_anexo('1')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo1 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo1 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo1' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato1' class='select_opciones' onchange="ocultar_desplegable('1')">
										<option value='texto' selected>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp1 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp1 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato1'>
									<select id='desplegable_metadato1' class='select_opciones opcion_desp1 hidden'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato1' onkeyup="ocultar_errores()">
									<div id='error_valores_desplegable_metadato1' class="errores">El desplegable no tiene valores asignados.</div>
									<div id="error_desplegable1" class="errores">El valor <b id='valor_error_desplegable1' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
									<input type='button' class="botones2 opcion_desp1 hidden" value="Agregar Desplegable" onclick="agregar_desplegable('1')">
								</td>
							</tr>

							<tr id="metadato2" class="hidden">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('2')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 2</td>
								<td class="detalle">
									<input type="search" id="nombre_metadato2" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato2" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio2' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo2' class='select_opciones' onchange="valida_requiere_anexo('2')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo2 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo2 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo2' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato2' class='select_opciones' onchange="ocultar_desplegable('2')">
										<option value='texto'>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp2 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp2 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato2'>
									<select id='desplegable_metadato2' class='select_opciones'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato2' onkeyup="ocultar_errores()">
									<div id='error_valores_desplegable_metadato2' class="errores">El desplegable no tiene valores asignados.</div>
									<div id="error_desplegable2" class="errores">El valor <b id='valor_error_desplegable2' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
									<input type='button' class="botones2" value="Agregar Desplegable" onclick="agregar_desplegable('2')">
								</td>
							</tr>
							<tr id="metadato3" class="hidden">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('3')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 3</td>
								<td class="detalle">
									<input type="search" id="nombre_metadato3" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato3" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio3' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo3' class='select_opciones' onchange="valida_requiere_anexo('3')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo3 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo3 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo3' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato3' class='select_opciones' onchange="ocultar_desplegable('3')">
										<option value='texto'>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp3 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp3 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato3'>
									<select id='desplegable_metadato3' class='select_opciones'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato3'  onkeyup="ocultar_errores()">
									<div id='error_valores_desplegable_metadato3' class="errores">El desplegable no tiene valores asignados.</div>
									<div id="error_desplegable3" class="errores">El valor <b id='valor_error_desplegable3' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
									<input type='button' class="botones2" value="Agregar Desplegable" onclick="agregar_desplegable('3')">
								</td>
							</tr>	
							<tr id="metadato4" class="hidden">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('4')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 4</td>
								<td class="detalle">
									<input type="search" id="nombre_metadato4" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato4" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio4' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo4' class='select_opciones' onchange="valida_requiere_anexo('4')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo4 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo4 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo4' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato4' class='select_opciones' onchange="ocultar_desplegable('4')">
										<option value='texto'>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp4 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp4 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato4'>
									<select id='desplegable_metadato4' class='select_opciones'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato4'   onkeyup="ocultar_errores()">
									<div id='error_valores_desplegable_metadato4' class="errores">El desplegable no tiene valores asignados.</div>
									<input type='button' class="botones2" value="Agregar Desplegable" onclick="agregar_desplegable('4')">
									<div id="error_desplegable4" class="errores">El valor <b id='valor_error_desplegable4' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
								</td>
							</tr>
							<tr id="metadato5" class="hidden">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('5')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 5</td>
								<td class="detalle">
									<input type="search" id="nombre_metadato5" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato5" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio5' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo5' class='select_opciones' onchange="valida_requiere_anexo('5')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo5 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo5 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo5' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato5' class='select_opciones' onchange="ocultar_desplegable('5')">
										<option value='texto'>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp5 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp5 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato5'>
									<select id='desplegable_metadato5' class='select_opciones'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato5'  onkeyup="ocultar_errores()">
									<div id='error_valores_desplegable_metadato5' class="errores">El desplegable no tiene valores asignados.</div>
									<input type='button' class="botones2" value="Agregar Desplegable" onclick="agregar_desplegable('5')">
									<div id="error_desplegable5" class="errores">El valor <b id='valor_error_desplegable5' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
								</td>
							</tr>
							<tr id="metadato6" class="hidden">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('6')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 6</td>
								<td class="detalle"><input type="search" id="nombre_metadato6" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato6" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio6' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo6' class='select_opciones' onchange="valida_requiere_anexo('6')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo6 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo6 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo6' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato6' class='select_opciones' onchange="ocultar_desplegable('6')">
										<option value='texto'>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp6 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp6 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato6'>
									<select id='desplegable_metadato6' class='select_opciones'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato6'  onkeyup="ocultar_errores()">
									<div id='error_valores_desplegable_metadato6' class="errores">El desplegable no tiene valores asignados.</div>
									<input type='button' class="botones2" value="Agregar Desplegable" onclick="agregar_desplegable('6')">
									<div id="error_desplegable6" class="errores">El valor <b id='valor_error_desplegable6' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
								</td>
							</tr>
							<tr id="metadato7" class="hidden">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('7')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 7</td>
								<td class="detalle"><input type="search" id="nombre_metadato7" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato7" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio7' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo7' class='select_opciones' onchange="valida_requiere_anexo('7')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo7 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo7 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo7' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato7' class='select_opciones' onchange="ocultar_desplegable('7')">
										<option value='texto'>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp7 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp7 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato7'>
									<select id='desplegable_metadato7' class='select_opciones'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato7'  onkeyup="ocultar_errores()">
									<div id='error_valores_desplegable_metadato7' class="errores">El desplegable no tiene valores asignados.</div>
									<input type='button' class="botones2" value="Agregar Desplegable" onclick="agregar_desplegable('7')">
									<div id="error_desplegable7" class="errores">El valor <b id='valor_error_desplegable7' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
								</td>
							</tr>
							<tr id="metadato8" class="hidden">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('8')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 8</td>
								<td class="detalle"><input type="search" id="nombre_metadato8" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato8" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio8' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo8' class='select_opciones' onchange="valida_requiere_anexo('8')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo8 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo8 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo8' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato8' class='select_opciones' onchange="ocultar_desplegable('8')">
										<option value='texto'>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp8 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp8 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato8'>
									<select id='desplegable_metadato8' class='select_opciones'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato8'  onkeyup="ocultar_errores()">
									<div id='error_valores_desplegable_metadato8' class="errores">El desplegable no tiene valores asignados.</div>
									<input type='button' class="botones2" value="Agregar Desplegable" onclick="agregar_desplegable('8')">
									<div id="error_desplegable8" class="errores">El valor <b id='valor_error_desplegable8' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
								</td>
							</tr>
							<tr id="metadato9" class="hidden">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('9')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 9</td>
								<td class="detalle"><input type="search" id="nombre_metadato9" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato9" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio9' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo9' class='select_opciones' onchange="valida_requiere_anexo('9')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo9 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo9 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo9' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato9' class='select_opciones' onchange="ocultar_desplegable('9')">
										<option value='texto'>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp9 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp9 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato9'>
									<select id='desplegable_metadato9' class='select_opciones'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato9'  onkeyup="ocultar_errores()">
									<div id='error_valores_desplegable_metadato9' class="errores">El desplegable no tiene valores asignados.</div>
									<input type='button' class="botones2" value="Agregar Desplegable" onclick="agregar_desplegable('9')">
									<div id="error_desplegable9" class="errores">El valor <b id='valor_error_desplegable9' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
								</td>
							</tr>
							<tr id="metadato10" class="hidden">
								<td id="img_cerrar" class="descripcion" style="width: 5px;" onclick="cerrar_metadato('10')">
									<img src="imagenes/iconos/cerrar.png" width="20px">
								</td>
								<td class="descripcion">Nombre del metadato 10</td>
								<td class="detalle"><input type="search" id="nombre_metadato10" class="input_search" onkeyup="ocultar_errores()">
									<div id="error_nombre_metadato10" class="errores">Este campo es obligatorio</div>
								</td>
								<td class="descripcion">Campo Obligatorio</td>
									<td class='detalle'>
									<select id='obligatorio10' class='select_opciones'>
										<option value='SI' >SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">Requiere archivo anexo</td>
								<td class='detalle' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='archivo_anexo10' class='select_opciones' onchange="valida_requiere_anexo('10')">
										<option value='SI'>SI</option>
										<option value='NO' selected>NO</option>
									</select>
								</td>
								<td class='descripcion tipo_archivo10 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									Tipo archivo
								</td>
								<td class='detalle tipo_archivo10 hidden' style="border-top: solid 4px #ce2da6;border-bottom: solid 4px #ce2da6;">
									<select id='tipo_archivo_anexo10' class='select_opciones'>
										<option value='PDF' selected>PDF</option>
										<option value='PNG' >PNG</option>
										<option value='JPEG' >JPEG</option>
										<option value='JPG' >JPG</option>
									</select>
								</td>
								<td class='descripcion' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Tipo
								</td>
								<td class='detalle' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<select id='tipo_metadato10' class='select_opciones' onchange="ocultar_desplegable('10')">
										<option value='texto'>Texto</option>
										<option value='desplegable'>Desplegable</option>
										<option value='fecha'>Fecha</option>
									</select>
								</td>
								<td class='detalle opcion_desp10 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									Agregar Desplegable
								</td>
								<td class='detalle opcion_desp10 hidden' style="border-top: solid 4px orange;border-bottom: solid 4px orange;">
									<input type='hidden' placeholder="Valores del Desplegable" id='valores_desplegable_metadato10'>
									<select id='desplegable_metadato10' class='select_opciones'></select>
									<input type='texto' placeholder="Agregar ..." id='valor_desplegable_metadato10'  onkeyup="ocultar_errores()">
									<input type='button' class="botones2" value="Agregar Desplegable" onclick="agregar_desplegable('10')">
									<div id='error_valores_desplegable_metadato10' class="errores">El desplegable no tiene valores asignados.</div>
									<div id="error_desplegable10" class="errores">El valor <b id='valor_error_desplegable10' style="font-size: 18px"></b> ya existe en el desplegable por lo que no se puede agregar nuevamente</div>
								</td>
							</tr>
						</table>
					</div>
					<table>
						<tr>
							<td colspan="2">
								<center id="boton_crear_metadato">
									<input type="button" value="Crear Metadato" id="bEnviar_expediente" class="botones" onclick="submit_agregar_metadatos()">
									<input type="button" value="Agregar Opción Metadato" class="botones" onclick="mostrar_opcion_metadato()">
								<center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para agregar metadato-->

</body>
</html>