<?php
/**************************************************************
Este archivo es invocado por el archivo funciones_radicacion_entrada.js funcion para cargar modificar radicado */
/**************************************************************
 * @brief Recibe desde funciones_radicacion_entrada.js
[cargar_modificacion_radicado(numero_radicado, fecha_radicado, descripcion_anexos, dependencia_destino, nombre_dependencia_destino, usuario_actual)]
[cargar_modificacion(numero_radicado,codigo_contacto,login_usuario_actual)]
[cargar_modificacion_interna(numero_radicado,fecha_radicado,descripcion_anexos,usuario_actual,asunto,clasificacion_radicado)]
para desplegar dependiendo si el numero del radicado tiene INV-INVE-INVEN ó si el radicado termina en 1-2-3-4 (Entrada-Salida-Normal-Interna)
 **************************************************************/
require_once "../../login/validar_inactividad.php";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Modificacion Rapida</title>
	<script type="text/javascript" src="include/js/funciones_radicacion_entrada.js" charset="UTF-8"></script>
	<link rel="stylesheet" href="include/css/estilos_radicacion_entrada.css">
</head>
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
	$('#descriptor').keyup(function(){
		validar_input('descriptor');
	});
	
	$('#nombre_documento').keyup(function(){
		validar_input_delay('nombre_documento');
	});
	// $('#numero_caja_archivo_central').keyup(function(){
	// });
	/* Input con delay de 1 segundo */
	$('#numero_caja_archivo_central').keyup(
		delay(function (e) {
		  	var valor = this.value;
		  	verificar_caja(valor);
			validar_input_delay('numero_caja_archivo_central');
		}, 1000)
	);
	/* Fin del input con delay de 1 segundo */
	$('#numero_caja_paquete').keyup(function(){
		validar_input('numero_caja_paquete');
	});
	$('#numero_carpeta').keyup(function(){
		validar_input('numero_carpeta');
	});
	$('#observaciones').keyup(function(){
		validar_input('observaciones');
	});
	$('#total_folios').keyup(function(){
		validar_input('total_folios');
	});

/* Validar espacios formulario */
	
	
	function cargar_expediente_inv(numero_expediente){
		$(".num_caja_archivo_central").slideDown("slow");

		$.ajax({
	        type: 'POST',
	        url: 'include/procesar_ajax.php',
	        data: {
	            'recibe_ajax' 			: 'buscar_ubicacion_topografica_desde_exp_inv',
	            'numero_expediente' 	: numero_expediente
	        },
	        success: function(respuesta){
	            if(respuesta!=""){
	            	console.log(respuesta)
	                $("#resultado_js").html(respuesta);
	            }
	        }
	    })
	}

	// function cargar_id(id_ubicacion){
	// 	$("#id_caja_archivo_central").val(id_ubicacion);
	// }

	function cargar_nombre_subserie_sb(codigo_subserie){
		if(codigo_subserie=="subserie" || codigo_subserie=="" || !codigo_subserie){
			$("#error_codigo_subserie").slideDown("slow");
		}else{
			$("#error_codigo_subserie").slideUp("slow");
			$("#nombre_documento").focus();
		}
	}

	/* Funcion para modificar radicado interno normal */
	function modificar_radicado_interno_normal(){
		var codigo_subserie = $("#codigo_subserie").val();
		cargar_nombre_subserie_sb(codigo_subserie);

		validar_input('nombre_documento');	// Funcion especificada en include/js/funciones_menu.js

		if($(".errores").is(":visible")){
			return false;
		}else{
			console.log("Enviar modificacion normal")
			loading("boton_modificar_radicado_interno_normal");

			var formData = new FormData($("#formulario_modificar_radicado_interno_normal")[0]);

			$.ajax({
				url: 'radicacion/radicacion_entrada/query_modificar.php' ,
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,

				success: function(datos){
					$("#resultado_total").html(datos);
				}
			});
		}
	}
	/* Fin funcion para modificar radicado interno normal */

	function modificar_radicado_inv(){
		var codigo_subserie = $("#codigo_subserie").val();
		cargar_nombre_subserie_sb(codigo_subserie);

		validar_input('nombre_documento');	// Funcion especificada en include/js/funciones_menu.js
		var fecha_inicial 	= $("#fecha_inicial").val();
		var fecha_final 	= $("#fecha_final").val();
		formato_fecha(fecha_inicial,'fecha_inicial1');
		formato_fecha(fecha_final,'fecha_final1');

		validar_input('descriptor');
		validar_input('caja_paquete_tomo');
		validar_input('numero_caja_paquete');

		validar_input('numero_carpeta');
		validar_input('total_folios');
		validar_input('consecutivo_desde');
		validar_input('consecutivo_hasta');

		validar_input('observaciones');
		validar_input('numero_caja_archivo_central');

		if($(".art").is(":visible")){
			$("#numero_caja_archivo_central_invalido").slideDown("slow");
		}else{
			$("#numero_caja_archivo_central_invalido").slideUp("slow");
			if($(".errores").is(":visible")){
				return false;
			}else{
				loading("boton_modificar_rad_inv");

				var formData = new FormData($("#formulario_modificar_inv")[0]);

				$.ajax({
					url: 'radicacion/radicacion_entrada/query_modificar.php' ,
					type: "POST",
					data: formData,
					contentType: false,
					processData: false,

					success: function(datos){
						$("#resultado_total").html(datos);
					}
				});
			}
		}
	}

	function mostrar_ocultar_ancho(modo){
		if(modo=="mostrar"){
			$(".descripcion_ancho").animate({ // Para volver al 10% el width de la tabla.
		    	width: "10%"
		    }, {
		      	queue: false,
		      	duration: 500
		    })
			$(".detalle_ancho").animate({ // Para volver al 50% el width de la tabla.
		    	width: "15%"
		    }, {
		      	queue: false,
		      	duration: 500
		    })
		}else{
			$(".descripcion_ancho").animate({ // Para volver al 10% el width de la tabla.
		    	width: "20%"
		    }, {
		      	queue: false,
		      	duration: 500
		    })
			$(".detalle_ancho").animate({ // Para volver al 50% el width de la tabla.
		    	width: "30%"
		    }, {
		      	queue: false,
		      	duration: 500
		    })
		}
	}

	function mover_descripcion_ancho(nombre_input){
		// console.log(nombre_input)
		var imagen = document.getElementById(nombre_input).files;
		var size_file=$("#"+nombre_input)[0].files[0].size;

		if(size_file<8388608){ // Tamaño en bits para 8M ver archivo README
			// $("#"+nombre_input+"_tamano").slideUp("slow");

			for(x=0; x<imagen.length; x++){
				if(imagen[x].type!= "application/pdf"){
					mostrar_ocultar_ancho("ocultar");
					return false;
				}else{
					mostrar_ocultar_ancho("mostrar");
				}
			}
		}else{
			mostrar_ocultar_ancho("ocultar");
		}
	}


	function oculta_resultado_ubicacion_topografica(){
		$("#resultado_ubicacion_topografica").slideUp("slow");
	}

	function verificar_caja(valor){
		if(valor.length=="" || valor.length==" "){
	  		// cargar_id("");
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
	}
	// desplegable_terminos(); // Cargar desplegable de términos por tipo de documento (No aplica en modificar inventario ni en normal.)
</script>
<style type="text/css">
	#resultado_ubicacion_topografica{
		display 	: none;
		max-height 	: 150px;
		overflow-y 	: scroll;
	}
	
	.descripcion_ancho{
		width: 20%;
	}
	.detalle_ancho{
		width: 30%;
	}
</style>
<body>
<?php
require_once '../../login/conexion2.php';

/***************************************************************************************************************/
/* Se definen las variables que se usan para mostrar formularios dependiendo de los parametros aqui definidos. */
$login_usuario         	= $_SESSION['login'];
$ubicacion_topografica 	= $_SESSION['ubicacion_topografica'];
$entidad               	= $_SESSION['entidad'];
$codigo_entidad        	= $_SESSION['codigo_entidad'];
$permiso_scanner  		= $_SESSION['scanner'];

if (isset($_POST['codigo_contacto'])) {
    $codigo_contacto = $_POST['codigo_contacto'];
} else {
    $codigo_contacto = "";
}

$numero_radicado 	= $_POST['numero_radicado'];
if(isset($_POST['usuario_actual'])){
	$usuario_actual 	= $_POST['usuario_actual'];
}

$verifica_inventario1 = substr($numero_radicado, 7, 3);
$verifica_inventario2 = substr($numero_radicado, 7, 4);
$verifica_inventario3 = substr($numero_radicado, 7, 5);

$verifica_radicacion_interna = substr($numero_radicado, -1);

/***************************************************************************************************************/
/* Dentro de esta condición muestra el formulario de modificación si el código de radicado contiene las marcas de inventario en su codigo de dependencia */
if ($verifica_inventario1 == "INV" || $verifica_inventario2 == "INVE" || $verifica_inventario3 == "INVEN") {
    $query_inventario = "select r.dependencia_radicador, r.codigo_serie, r.codigo_subserie, i.nombre_documento, i.fecha_inicial, i.fecha_final, i.caja_paquete_tomo, i.numero_caja_paquete, i.numero_carpeta, i.consecutivo_desde, i.consecutivo_hasta, i.descriptor, i.total_folios, i.observaciones, r.id_expediente, i.numero_caja_archivo_central, i.codigo_dependencia from radicado r join inventario i on r.numero_radicado=i.radicado_jonas where i.radicado_jonas='$numero_radicado'";

    $fila_inventario  = pg_query($conectado, $query_inventario);
    $linea_inventario = pg_fetch_array($fila_inventario);
    $id_expediente    = $linea_inventario["id_expediente"];

    /* Consulta si hay ubicacion_topografica del radicado para mostrar en numero_caja_archivo_central */
    $numero_caja_archivo_central = $linea_inventario["numero_caja_archivo_central"];

    $nombre_nivel = "";
    $id_ubicacion = "";

    if ($numero_caja_archivo_central != "") {
        $query_nivel_topografica = "select * from ubicacion_topografica where nombre_nivel='$numero_caja_archivo_central'";

        $fila_ubicacion_topografica  = pg_query($conectado, $query_nivel_topografica);
        $linea_ubicacion_topografica = pg_fetch_array($fila_ubicacion_topografica);
        $id_ubicacion                = $linea_ubicacion_topografica['id_ubicacion'];
        $nombre_nivel                = $numero_caja_archivo_central;
    }
    /* Fin encuentra ubicacion_topografica para mostrar en numero_caja_archivo_central */

    $codigo_dependencia  = $linea_inventario["codigo_dependencia"];
    $codigo_serie        = $linea_inventario["codigo_serie"];
    $codigo_subserie     = $linea_inventario["codigo_subserie"];
    $nombre_documento    = $linea_inventario["nombre_documento"];
    $fecha_inicial       = $linea_inventario["fecha_inicial"];
    $fecha_final         = $linea_inventario["fecha_final"];
    $caja_paquete_tomo   = $linea_inventario["caja_paquete_tomo"];
    $numero_caja_paquete = $linea_inventario["numero_caja_paquete"];
    $numero_carpeta      = $linea_inventario["numero_carpeta"];
    $consecutivo_desde   = $linea_inventario["consecutivo_desde"];
    $consecutivo_hasta   = $linea_inventario["consecutivo_hasta"];
    $descriptor          = $linea_inventario["descriptor"];
    $total_folios        = $linea_inventario["total_folios"];
    $observaciones       = $linea_inventario["observaciones"];

    $fecha_inicial2 = $fecha_inicial;
    $fecha_final2   = $fecha_final;

    /* Se convierte el formato de la fecha a 2020-01-21 */
    $caracter_fecha_inicial = substr($fecha_inicial, 2, 1);
    $caracter_fecha_final   = substr($fecha_final, 2, 1);
    if ($caracter_fecha_inicial == "/") {
        $year          = substr($fecha_inicial, 6, 4);
        $mes           = substr($fecha_inicial, 3, 2);
        $dia           = substr($fecha_inicial, 0, 2);
        $fecha_inicial = $year . "-" . $mes . "-" . $dia;
    }
    if ($caracter_fecha_final == "/") {
        $year        = substr($fecha_final, 6, 4);
        $mes         = substr($fecha_final, 3, 2);
        $dia         = substr($fecha_final, 0, 2);
        $fecha_final = $year . "-" . $mes . "-" . $dia;
    }
    $numero_consecutivo = substr($numero_radicado, 10, 7);

    echo "<script>setTimeout('consulta_listado_series2(\"$codigo_serie\",\"\",\"codigo_serie_sb\");', 500); cargar_codigo_subserie2('$codigo_serie','$codigo_subserie','$codigo_dependencia','modificar_inventario','codigo_subserie')</script>";

    ?>
	<div id="ventana2" class="ventana">
		<h1 class="center">Formulario Modificar Radicado (Inventario)
			<?php echo $numero_radicado; ?></a>
		</h1>
		<hr>
		<center>
			<form enctype="multipart/form-data" method="post" id ="formulario_modificar_inv" name ="formulario_modificar_inv">
				<input type="hidden" name="numero_radicado2" id="numero_radicado2" value="<?php echo $numero_radicado; ?>">
				<input type="hidden" name="tipo_modificacion" id="tipo_modificacion" value="modificacion_inventario">
				<input type="hidden" name="codigo_dependencia" id="codigo_dependencia" title="Codigo de la dependencia a la cual pertenece el radicado. No se puede modificar." value=<?php echo $codigo_dependencia; ?> disabled>

				<table border="0">
					<tr>
						<td class="descripcion" width="8%">
							Codigo Serie
						</td>
						<td class="detalle" colspan="3" width="42%">
		                   <select name="codigo_serie_sb" id="codigo_serie_sb" class='select_opciones' <?php echo "onchange='cargar_codigo_subserie2(this.value,\"\",\"\",\"modificar_inventario\",\"codigo_subserie\")'"; ?>>
		                   </select>
							<!-- <input type="hidden" name="nombre_serie_sb" id="nombre_serie_sb"> -->
							<div id="error_codigo_serie_sb" class="errores">Debe seleccionar por lo menos un código - nombre de serie</div>
						</td>
						<td class="descripcion" width="8%">
							Codigo Subserie
						</td>
						<td class="detalle" colspan="3" width="42%">
							<select name="codigo_subserie" id="codigo_subserie" class='select_opciones' onchange="cargar_nombre_subserie_sb(this.value)">
		                   	<?php // echo "$listado_subseries_fn"; ?>
		                   </select>
							<!-- <input type="hidden" id="nombre_serie_sb"> -->
							<div id="error_codigo_subserie" class="errores">Debe seleccionar por lo menos un código - nombre de subserie</div>
						</td>
					</tr>
					<tr>
						<td class="descripcion" >
							Asunto o Descripción (Nombre del expediente o carpeta)
						</td>
						<td class="detalle" colspan="3">
							<textarea name="nombre_documento" id="nombre_documento" rows="2" style="width:97%;padding:5px;" placeholder="Ingrese el nombre del documento. Sea lo más específico posible" title="Ingrese el nombre del documento. Sea lo más específico posible" onblur="validar_input('nombre_documento')"><?php echo $nombre_documento; ?></textarea>
							<div id="nombre_documento_null" class="errores">El nombre del documento es obligatorio</div>
							<div id="nombre_documento_min" class="errores">El nombre del documento no puede ser menor a 6 caracteres (numeros o letras) </div>
							<div id="nombre_documento_max" class="errores">El nombre del documento no puede ser mayor a 500 caracteres (numeros o letras)</div>
						</td>
						<td class="descripcion" width="8%">
							Fecha Inicial
						</td>
						<td class="detalle" width="16%">
							<input type="date" name="fecha_inicial" id="fecha_inicial" class="input_inv" title="Fecha Inicial del Documento" value="<?php echo $fecha_inicial; ?>" onchange="formato_fecha(this.value,'fecha_inicial1')" style="width: 100px;">
							<input type="hidden" name="fecha_inicial1" id="fecha_inicial1" class="input_inv" value="<?php echo $fecha_inicial2; ?>">
							<div id="fecha_inicial1_error" class="errores">Hay un error con el formato de fecha, por favor comuníquese con el administrador del sistema.</div>
						</td>
						<td class="descripcion" width="8%">
							Fecha Final
						</td>
						<td class="detalle" width="16%">
							<input type="date" name="fecha_final" id="fecha_final" class="input_inv" title="Fecha Final del Documento" value="<?php echo $fecha_final; ?>" onchange="formato_fecha(this.value,'fecha_final1')" style="width: 100px;">
							<input type="hidden" name="fecha_final1" id="fecha_final1" class="input_inv" value="<?php echo $fecha_final2; ?>">
							<div id="fecha_final1_error" class="errores">Hay un error con el formato de fecha, por favor comuníquese con el administrador del sistema.</div>
						</td>
					</tr>
					<tr>
						<td class="descripcion">
							Descriptores (Metadatos)
						</td>
						<td class="detalle" colspan="3">
							<textarea name="descriptor" id="descriptor" rows="2" style="width:97%;padding:5px;" placeholder="Ingrese los descriptores / metadatos del documento. Sea lo más específico posible" title="Ingrese los descriptores / metadatos del documento. Sea lo más específico posible" onblur="validar_input('descriptor')"><?php echo $descriptor; ?></textarea>
							<div id="descriptor_null" class="errores">El campo descriptor es obligatorio</div>
							<div id="descriptor_min" class="errores">El campo descriptor no puede ser menor a 6 caracteres (numeros o letras) </div>
							<div id="descriptor_max" class="errores">El campo descriptor no puede ser mayor a 500 caracteres (numeros o letras)</div>
						</td>
						<td class="descripcion">
							Caja, paquete, tomo u otro
						</td>
						<td class="detalle">
							<input type="search" name="caja_paquete_tomo" id="caja_paquete_tomo" class='input_inv' placeholder="Caja, paquete, tomo, otro (Tipo de medio de conservacion)" title="Caja, paquete, tomo, otro (Tipo de medio de conservacion)" value="<?php echo $caja_paquete_tomo; ?>" onblur="validar_input('caja_paquete_tomo')">
							<div id="caja_paquete_tomo_max" class="errores">El campo de Caja, paquete, tomo, otro no puede ser mayor a 10 caracteres</div>
						</td>
						<td class="descripcion">
							Numero caja - Paquete
						</td>
						<td class="detalle">
							<input type="search" name="numero_caja_paquete" id="numero_caja_paquete" class="input_inv" placeholder="Numero del paquete, tomo, otro (Codigo consecutivo del tipo del medio de conservacion)" title="Numero del paquete, tomo, otro (Codigo consecutivo del tipo del medio de conservacion)" value="<?php echo $numero_caja_paquete; ?>" onblur="validar_input('numero_caja_paquete')">
							<div id="numero_caja_paquete_max" class="errores">El campo de Numero caja - paquete no puede ser mayor a 10 caracteres</div>
						</td>
					</tr>
					<tr>
						<td class="descripcion">
							Numero de Carpeta
						</td>
						<td class="detalle" >
							<input type="search" name="numero_carpeta" id="numero_carpeta" class='input_inv' placeholder="Numero de carpeta donde se encuentra el documento" title="Numero de carpeta donde se encuentra el documento" value="<?php echo $numero_carpeta; ?>" onblur="validar_input('numero_carpeta')">
							<div id="numero_carpeta_max" class="errores">El numero de la carpeta no puede ser mayor a 20 caracteres</div>
						</td>
						<td class="descripcion">
							Total Folios
						</td>
						<td class="detalle">
							<input type="search" name="total_folios" id="total_folios" class="input_inv" placeholder="Numero de folios del documento" title="Numero de folios del documento" value="<?php echo $total_folios; ?>" onblur="validar_input('total_folios')">
							<div id="error_total_folios" class="errores">La cantidad de folios debe ser numérico.</div>
							<div id="total_folios_max" class="errores">El valor de este campo no puede ser mayor a 5 caracteres</div>
						</td>
						<td class="descripcion">
							Consecutivo Desde
						</td>
						<td class="detalle">
							<input type="search" name="consecutivo_desde" id="consecutivo_desde" class="input_inv" placeholder="Valor inicial si tiene consecutivo" title="Valor inicial si tiene consecutivo" value="<?php echo $consecutivo_desde; ?>" onblur="validar_input('consecutivo_desde')">
							<div id="consecutivo_desde_max" class="errores">El campo de este consecutivo no puede ser mayor a 20 caracteres</div>
						</td>
						<td class="descripcion">
							Consecutivo Hasta
						</td>
						<td class="detalle">
							<input type="search" name="consecutivo_hasta" id="consecutivo_hasta" class="input_inv" placeholder="Valor final si tiene consecutivo" title="Valor final si tiene consecutivo" value="<?php echo $consecutivo_hasta; ?>" onblur="validar_input('consecutivo_hasta')">
							<div id="consecutivo_hasta_max" class="errores">El campo de este consecutivo no puede ser mayor a 20 caracteres</div>
						</td>
					</tr>
					<tr>
						<td class="descripcion">
							Observaciones
						</td>
						<td class="detalle" colspan="3">
							<textarea name="observaciones" id="observaciones" rows="2" style="width:97%;padding:5px;" placeholder="Notas u observaciones al documento" title="Notas u observaciones al documento" onblur="validar_input('observaciones')"><?php echo $observaciones; ?></textarea>
							<div id="observaciones_max" class="errores">El campo descriptor no puede ser mayor a 200 caracteres (numeros o letras)</div>
						</td>
						<td class="descripcion">
							Archivo PDF (Imagen Principal del Radicado)
						</td>
						<td class="detalle" colspan="3">
							<input type="file" name="archivo_pdf_radicado_inv" id="archivo_pdf_radicado_inv" onchange="validar_input_file('archivo_pdf_radicado_inv')">
							<div id="archivo_pdf_radicado_inv_invalido" class="errores"> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
							<div id="archivo_pdf_radicado_inv_tamano" class="errores">El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
							<!-- <input id="uploadPDF" type="file" onchange="PreviewImage();" name="myPDF"/>&nbsp;  -->
							<!-- <input type="button" value="Preview" onclick="PreviewImage();" /> <div style="clear:both">  -->
							</div>
						</td>
					</tr>
<?php
	if ($ubicacion_topografica == "SI") {
        $exp  = explode(",", $id_expediente);
        $max  = sizeof($exp);
        $max2 = $max - 1;

        $nombre_expediente = "";
        if ($max2 == 0) {
            $num_exp      = $exp[0];
            $consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
            $fila_exp     = pg_query($conectado, $consulta_exp);
            $linea_exp    = pg_fetch_array($fila_exp);
            $nombre_exp   = $linea_exp['nombre_expediente'];

            $nombre_expediente = $nombre_expediente . "<div class='art_exp center' onclick='cargar_expediente_inv(\"$num_exp\")'>($num_exp)<br> $nombre_exp</div>";
        } else {
            for ($j = 0; $j < $max2; $j++) {
                $num_exp = $exp[$j];

                $consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
                $fila_exp     = pg_query($conectado, $consulta_exp);
                $linea_exp    = pg_fetch_array($fila_exp);
                $nombre_exp   = $linea_exp['nombre_expediente'];

                $nombre_expediente = $nombre_expediente . "<div class='art_exp center' onclick='cargar_expediente_inv(\"$num_exp\")'>($num_exp)<br> $nombre_exp</div>";
            }
        }
        ?>
					<tr>
						<td class="descripcion">
							Expedientes en los que está incluido el radicado:
						</td>
						<td class="detalle" colspan="3">
							<?php echo "$nombre_expediente"; ?>
						</td>
						<td class="num_caja_archivo_central hidden descripcion">
							Numero Caja Archivo Central
						</td>
						<td class="num_caja_archivo_central hidden detalle" colspan="3">
							<input type="search" name="numero_caja_archivo_central" id="numero_caja_archivo_central" class="input_inv" placeholder="Ingrese el numero de caja (Archivo central)" title="Numero de la caja en la que se va a conservar en el archivo central" onblur="validar_input('numero_caja_archivo_central')" onkeyup="loading('resultado_ubicacion_topografica')" maxlength="20">

							<input type="hidden" name="id_caja_archivo_central" id="id_caja_archivo_central" >
							<input type="hidden" name="id_expediente" id="id_expediente" >

							<div id="numero_caja_archivo_central_max" class="errores">El numero de caja no puede ser mayor a 50 caracteres (numeros o letras)</div>
							<div id="error_numero_caja_archivo_central" class="errores">El numero de caja no existe en el inventario. Ingrese por favor un numero de caja válido</div>
							<div id="numero_caja_archivo_central_invalido" class="errores">Debe seleccionar un numero de caja válido.</div>
							<div id="resultado_ubicacion_topografica"></div>
						</td>
					</tr>
					<tr>
						<td colspan="8">
							<iframe id="viewer" frameborder="0" scrolling="yes" width="100%" height="400" style='display: none;'>
							</iframe>
						</td>
					</tr>
	<?php
	}
    ?>
				</table>
			</form>
			<div id="boton_modificar_rad_inv">
				<input type="button" value="Modificar Radicado" id="modificar_radicado_inv" onclick="modificar_radicado_inv()" class="botones">
			</div>
		</center>
	</div>
	<?php

} else { // Fin verificacion if ($verifica_inventario1 == "INV" || $verifica_inventario2 == "INVE" || $verifica_inventario3 == "INVEN")
    // Si $verifica_inventario es diferente a 'INV' - 'INVE' ó 'INVEN'
    /***************************************************************************************************************/
/* Dentro de esta condición muestra el formulario de modificación si el código de radicado NO contiene las marcas de inventario en su codigo de dependencia */

    if ($verifica_radicacion_interna == 3) {
        /***************************************************************************************************************/
        /* Esta condición muestra el formulario de modificación si es un radicado 3 (Normal) */

        if ($codigo_contacto != '') {
            $query_radicado = "select * from radicado r full outer join datos_origen_radicado dor on r.numero_radicado=dor.numero_radicado and r.codigo_contacto=dor.codigo_datos_origen_radicado where r.numero_radicado='$numero_radicado' order by asunto limit 150";
            echo "<h3>Falta interfaz para radicados internos con codigo_contacto</h3>";
        } else {
            $query_radicado = "select * from radicado where numero_radicado='$numero_radicado'";

            $fila_radicado  = pg_query($conectado, $query_radicado);
            $linea_radicado = pg_fetch_array($fila_radicado);

            $codigo_serie     = $linea_radicado["codigo_serie"];
            $codigo_subserie  = $linea_radicado["codigo_subserie"];
            $nombre_documento = $linea_radicado["asunto"];

            echo "<script>setTimeout('consulta_listado_series2(\"$codigo_serie\",\"\",\"codigo_serie_sb\");', 500); cargar_codigo_subserie2('$codigo_serie','$codigo_subserie','$codigo_dependencia','modificar_inventario','codigo_subserie')</script>";

?>
			<div id="ventana3" class="ventana">
				<h1 class="center">Formulario Modificar Radicado (Interno - Normal)
					<?php echo $numero_radicado; ?></a>
				</h1>
				<hr>
				<center>
					<form enctype="multipart/form-data" method="post" id ="formulario_modificar_radicado_interno_normal" name ="formulario_modificar_radicado_interno_normal">
						<input type="hidden" name="numero_radicado2" id="numero_radicado2" value="<?php echo $numero_radicado; ?>">
						<input type="hidden" name="tipo_modificacion" id="tipo_modificacion" value="modificacion_radicado_interno_normal">
						<!-- <input type="hidden" name="codigo_dependencia" id="codigo_dependencia" title="Codigo de la dependencia a la cual pertenece el radicado. No se puede modificar." value=<?php // echo $codigo_dependencia; ?> disabled> -->

						<table border="0">
							<tr>
								<td class="descripcion" width="8%">
									Codigo Serie
								</td>
								<td class="detalle" colspan="3" width="42%">
				                   <select name="codigo_serie_sb" id="codigo_serie_sb" class='select_opciones' <?php echo "onchange='cargar_codigo_subserie2(this.value,\"\",\"\",\"modificar_inventario\",\"codigo_subserie\")'"; ?>>
				                   </select>
									<!-- <input type="hidden" name="nombre_serie_sb" id="nombre_serie_sb"> -->
									<!-- <div id="error_nombre_codigo_serie_sb" class="errores">Debe seleccionar por lo menos un código - nombre de serie</div> -->
								</td>
								<td class="descripcion" width="8%">
									Codigo Subserie
								</td>
								<td class="detalle" colspan="3" width="42%">
									<select name="codigo_subserie" id="codigo_subserie" class='select_opciones' onchange="cargar_nombre_subserie_sb(this.value)">
				                   </select>
									<!-- <input type="hidden" id="nombre_serie_sb"> -->
									<div id="error_codigo_subserie" class="errores">Debe seleccionar por lo menos un código - nombre de subserie</div>
								</td>
							</tr>
							<tr>
								<td class="descripcion" >
									Asunto o Descripción (Nombre del expediente o carpeta)
								</td>
								<td class="detalle" colspan="3">
									<textarea name="nombre_documento" id="nombre_documento" rows="2" style="width:97%;padding:5px;" placeholder="Ingrese el nombre del documento. Sea lo más específico posible" title="Ingrese el nombre del documento. Sea lo más específico posible" onblur="validar_input('nombre_documento')"><?php echo $nombre_documento; ?></textarea>
									<div id="nombre_documento_null" class="errores">El nombre del documento es obligatorio</div>
									<div id="nombre_documento_min" class="errores">El nombre del documento no puede ser menor a 6 caracteres (numeros o letras) </div>
									<div id="nombre_documento_max" class="errores">El nombre del documento no puede ser mayor a 500 caracteres (numeros o letras)</div>
								</td>
								<td class="descripcion">
									Archivo PDF (Imagen Principal del Radicado)
								</td>
								<td class="detalle" colspan="3">
									<input type="file" name="archivo_pdf_radicado_inv" id="archivo_pdf_radicado_inv" onchange="validar_input_file('archivo_pdf_radicado_inv')">
									<div id="archivo_pdf_radicado_inv_invalido" class="errores"> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
									<div id="archivo_pdf_radicado_inv_tamano" class="errores">El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
								</td>
							</tr>
							<tr>
							<td colspan="8">
								<iframe id="viewer" frameborder="0" scrolling="yes" width="100%" height="400" style='display: none;'>
								</iframe>
							</td>
					</tr>
						</table>
					</form>
					<div id="boton_modificar_radicado_interno_normal">
						<input type="button" value="Modificar Radicado" id="modificar_radicado_interno_normal" onclick="modificar_radicado_interno_normal()" class="botones">
					</div>
				</center>
			</div>
<?php
		} // Fin validacion  if ($codigo_contacto == '') 

    }else{ // Fin verificacion if ($verifica_radicacion_interna == 3) 
        // Si es un radicado diferente a "INV" y a 3 (Normal) es decir 1-2-4 (Entrada-Salida-Interna) 
        /***************************************************************************************************************/
        /* Esta condición muestra el formulario de modificación si es un radicado diferente a 3 (Normal) es decir 1-2-4 (Entrada-Salida-Interna) */

        $query_radicado = "select * from radicado r full outer join datos_origen_radicado dor on r.numero_radicado=dor.numero_radicado where r.numero_radicado='$numero_radicado' order by asunto limit 10";

        // echo "$query_radicado";
        $fila_radicado  = pg_query($conectado, $query_radicado);
        $linea_radicado = pg_fetch_array($fila_radicado);

        $asunto                 	= $linea_radicado['asunto'];
        $clasificacion_radicado 	= $linea_radicado['clasificacion_radicado'];
        $clasificacion_seguridad 	= $linea_radicado['clasificacion_seguridad'];
        $dependencia_destino    	= $linea_radicado['dependencia_actual'];
        $descripcion_anexos     	= $linea_radicado['descripcion_anexos'];
        $medio_recepcion  			= $linea_radicado['medio_recepcion'];
        $medio_respuesta_solicitado = $linea_radicado['medio_respuesta_solicitado'];
        $numero_guia_oficio     	= $linea_radicado['numero_guia_oficio'];
        $path_radicado          	= $linea_radicado['path_radicado'];
        $termino 					= $linea_radicado['termino'];
        $usuarios_control       	= $linea_radicado['usuarios_control'];

        if ($medio_respuesta_solicitado == "null" || $medio_respuesta_solicitado == ""){
        	$medio_respuesta_solicitado == "seleccionar";
        }
        //se crea estructura del select de medio respuesta solicitado
        $pre_select_medio_respuesta_solicitado = "<select name='medio_respuesta_solicitado' class='select_opciones'><option value='correspondencia_fisica' title='El usuario indica que desea recibir la respuesta a este documento mediante oficio impreso en papel y enviado por mensajería fisica'>Correspondencia Fisica</option><option value='correo_electronico' title='El usuario indica que requiere la respuesta a este documento mediante correo electrónico'>Correo Electronico</option><option value='no_requiere_respuesta' title='El usuario manifiesta que no desea respuesta a este documento'>No Requiere Respuesta</option></select>";
        //con el str_replace se cambia el valor de selected dependiendo de la consulta a base de datos con valor en medio_respuesta_solicitado
		$select_medio_respuesta_solicitado = str_replace($medio_respuesta_solicitado, "".$medio_respuesta_solicitado."' selected='true", $pre_select_medio_respuesta_solicitado);

		if ($medio_recepcion == "null" || $medio_recepcion == ""){
        	$medio_recepcion = "presencial";
        }

		//se crea estructura del select de medio respuesta solicitado
        $pre_select_medio_recepcion = "<select name='medio_recepcion' id='medio_recepcion' class='select_opciones' onchange='valida_recepcion()'><option value='correo_electronico' title='Este documento se recibe mediante correo electrónico o e-mail'>Correo Electrónico</option><option value='presencial' title='Este documento se recibe de manera física y se entrega Sticker de recibido al usuario.'>Personal</option><option value='servicio_postal' title='Este documento se recibe mediante mensajería (4-72, Interrapidisimo, Servientrega, etc.)'>Servicio Postal</option>";
        //con el str_replace se cambia el valor de selected dependiendo de la consulta a base de datos con valor en medio_recepcion
		$select_medio_recepcion = str_replace($medio_recepcion, "".$medio_recepcion."' selected='true", $pre_select_medio_recepcion);

		//se crea estructura del select de medio respuesta solicitado
        $pre_select_clasificacion_seguridad = "<select name='clasificacion_seguridad' class='select_opciones'><option value='sin_clasificacion' title=''>Sin Clasificación</option><option value='restringido' title=''>Restringido</option><option value='confidencial' title=''>Confidencial</option><option value='secreto' title=''>Secreto</option><option value='ultrasecreto' title=''>Ultrasecreto</option><option value='publica_clasificada' title=''>Pública Clasificada</option><option value='publica_reservada' title=''>Pública Reservada</option>";
        //con el str_replace se cambia el valor de selected dependiendo de la consulta a base de datos con valor en medio_recepcion
		$select_clasificacion_seguridad = str_replace($clasificacion_seguridad, "".$clasificacion_seguridad."' selected='true", $pre_select_clasificacion_seguridad);

        if ($path_radicado != "") {
            echo "<script>
					mostrar_ocultar_ancho('mostrar');
					$('#viewer3').html(\"<object data='bodega_pdf/radicados/$path_radicado' type='application/pdf' width='100%' height='100%'></object>\");
				</script>";
        }

        /* Variables para imprimir sticker */
        $timestamp 			= $linea_radicado['fecha_radicado'];
        $login_radicador 	= $linea_radicado['usuario_radicador'];

        /* Se consulta nombre_dependencia_destino */
        $query_nombre_dependencia_destino = "select * from dependencias where codigo_dependencia='$dependencia_destino'";
        $fila_nombre_dependencia_destino  = pg_query($conectado, $query_nombre_dependencia_destino);
        $linea_nombre_dependencia_destino = pg_fetch_array($fila_nombre_dependencia_destino);

        $nombre_dependencia_destino = $linea_nombre_dependencia_destino['nombre_dependencia'];

        /* Fin variables para imprimir sticker */

        if ($asunto == "") {
            $varEnvio          = "fecha=$timestamp&usu_radicador=$login_radicador&radicado=$numero_radicado&dependencia_destino=$dependencia_destino-$nombre_dependencia_destino&anexos=$descripcion_anexos&entidad=$entidad&codigo_entidad=$codigo_entidad";
            $tipo_modificacion = "insert";
        } else {
            $varEnvio          = "fecha=$timestamp&usu_radicador=$login_radicador&radicado=$numero_radicado&dependencia_destino=$dependencia_destino-$nombre_dependencia_destino&anexos=$descripcion_anexos&entidad=$entidad&codigo_entidad=$codigo_entidad&asunto=$asunto";
            $tipo_modificacion = "update";
        }

        $dignatario                    = $linea_radicado['dignatario'];
        $direccion                     = $linea_radicado['direccion'];
        $mail                          = $linea_radicado['mail'];
        $nombre_remitente_destinatario = $linea_radicado['nombre_remitente_destinatario'];
        $telefono                      = $linea_radicado['telefono'];
        $ubicacion                     = $linea_radicado['ubicacion'];

        /* Se define desplegable con clasificación del radicado (PQR,Oficio, etc) */
        $query_termino = "select * from tipo_doc_termino where activo ='SI' union select * from tipo_doc_termino_pqr where activo ='SI' order by tipo_documento";
        $fila_termino  = pg_query($conectado, $query_termino);
        /* Calcula el numero de registros que genera la consulta anterior. */
        $registros_termino = pg_num_rows($fila_termino);
        /* Recorre el array generado e imprime uno a uno los resultados. */

        $select_tipo_documento = "<select name='termino' id='termino' class='select_opciones' class='select_opciones' onchange='muestra_termino()'>"; // Inicia el input select
        for ($t = 0; $t < $registros_termino; $t++) {
            $linea_termino = pg_fetch_array($fila_termino);

            $tipo_documento 			= $linea_termino['tipo_documento'];
            $tiempo_tramite 			= $linea_termino['tiempo_tramite'];
            $descripcion_tipo_documento = $linea_termino['descripcion_tipo_documento'];

            if ($tipo_documento == $clasificacion_radicado) {
                $select_tipo_documento .= "<option value='$tipo_documento' selected='selected' title='$descripcion_tipo_documento'>$tipo_documento</option>"; // Si es la opcion "OFICIO" sea precargada por defecto.
            } else {
                $select_tipo_documento .= "<option value='$tipo_documento' title='$descripcion_tipo_documento'>$tipo_documento</option>";
            }
        }
        $select_tipo_documento .= "</select>"; // Fin del input select

        /* Hasta aqui se define desplegable con clasificación del radicado (PQR,Oficio, etc) */
        /* Hasta aqui se definen las variables que se usan en este archivo */
        ?>
		 	<div id="ventana" class="ventana">
				<h1 class="center">Formulario Modificar Radicado
					<a href="radicacion/radicacion_entrada/sticker.php?<?php echo $varEnvio; ?>" title="Imprimir Sticker" onClick="window.open(this.href,'window','toolbar=no, status=no, scrollbars=no, location=no, menubar=no, directories=no, width=710, height=350, top=300, left=400');return false" >
					<?php echo $numero_radicado; ?></a>
				</h1>
				<hr>
				<form enctype="multipart/form-data" method="post" id ="formulario_modificar_radicado" name ="formulario_modificar_radicado">

					<input type="hidden" name="numero_radicado" id="numero_radicado" value="<?php echo $numero_radicado; ?>">
					<input type="hidden" name="tipo_modificacion" id="tipo_modificacion" value="<?php echo $tipo_modificacion; ?>">
					<input type="hidden" name="login_usuario" id="login_usuario" value="<?php echo $login_usuario; ?>">
					<input type="hidden" id="tipo_formulario" value="formulario_modificar_radicado">
					<input type="hidden" name='path_origen_scanner' id="path_origen_scanner" value=""> <!-- Se utiliza en modulo de carpeta compartida en implementaciones inhouse. Faltaría ajustar en implementaciones web-hosting -->
		<!--			<input type="hidden" name="MAX_FILE_SIZE" value="3000000" /> -->
					<center>
						<table border="0">
							<tr>
								<td class="descripcion" style="width: 100px;">
									Fecha Radicacion:
								</td>
								<td class="detalle" style="width: 150px;">
									<input type="search" title="Fecha en la cual se generó el radicado. No se puede modificar." value=<?php echo $timestamp; ?> disabled>
								</td>
								<td class="descripcion" style="width: 100px;" id="medio_respuesta_solicitado">
									Medio de recepcion:
								</td>
								<td class="detalle" style="width: 150px;">
									<?php echo "$select_medio_recepcion"; ?>
								</td>	
								<td class="descripcion" style="width: 100px;" id="medio_respuesta_solicitado">
									Medio de respuesta solicitado:
								</td>
								<td class="detalle" style="width: 150px;">
									<?php echo "$select_medio_respuesta_solicitado"; ?>
								</td>	
								<td class="descripcion" style="width: 100px;">
									Tipo de Documento:
								</td>
								<td class="detalle" style="width: 350px;">
									<div name="td" title="Del tipo de documento depende el tiempo de trámite asignado" style="float: left;">
										<?php echo "$select_tipo_documento"; ?>
									</div>
									<div class="detalle" id="termino_td" align="left" max-width="50px" onclick="mostrar_dias_tramite()">
										<b><?php if($termino==1){ echo "$termino dia habil de tramite";}else{ echo "$termino dias habiles de tramite"; }  ?> </b>
									</div>
									<div id="div_dias_tramite" style="display: none;">
									
									<input type="text" id="dias_tramite" name="dias_tramite" <?php echo "value='$termino'"; ?> style="float: left; width: 20px;" onblur="ocultar_dias_tramite(1)" onkeyup="espacios_formulario('dias_tramite', 'sin_caracteres');">
									</div>
									<div id="error_dias_tramite" class="errores" style="float: left;">El valor ingresado debe ser un número</div>
									<div id="dias_tramite_max" class="errores" style="float: left;">No puede tener mas de 30 dias de trámite</div>
									<div id="dias_tramite_null" class="errores" style="float: left;">Este campo es obligatorio</div>
									<div id="dias_tramite_cero" class="errores" style="float: left;">El valor no puede ser CERO</div>
								</td>
								<td class="descripcion" style="width: 100px;">
									Clasificación de Seguridad:
								</td>
								<td class="detalle" style="width: 150px;">
									<?php echo "$select_clasificacion_seguridad"; ?>
								</td>	
							</tr>
						</table>
						<table>	
							<tr>
								<td class="descripcion descripcion_ancho" >
									Numero de Guía - Oficio del Radicado:
								</td>
								<td class="detalle detalle_ancho" >
									<input type="search" placeholder="Ingrese el número de guía o número del oficio del radicado" title="Ingrese el número de guía o número del oficio del radicado" value="<?php echo $numero_guia_oficio; ?>" id="numero_guia_radicado" name="numero_guia_radicado">
									<div id="numero_guia_radicado_max" class="errores">El número o guía del radicado no puede ser mayor a 50 caracteres. (Actualmente <b><u id='numero_guia_radicado_contadormax'></u></b> caracteres)</div>
									<div id="numero_guia_radicado_min" class="errores">El número o guía del radicado no puede ser menor a 3 caracteres.</div>
								</td>
								<td class="descripcion descripcion_ancho">
									Descripcion de Anexos:
								</td>
								<td class="detalle detalle_ancho" >
									<input type="search" id="descripcion_anexos" name="descripcion_anexos" placeholder="El radicado viene con anexo CD, AZ, USB, Caja, etc." title="El radicado viene con anexo CD, AZ, USB, Caja, etc." value='<?php echo "$descripcion_anexos"; ?>'>
									<div id="descripcion_anexos_min" class="errores">La descripción de los anexos no puede ser menor a 6 caracteres (numeros o letras) </div>
									<div id="descripcion_anexos_max" class="errores">La descripción de los anexos no puede ser mayor a 100 caracteres. (Actualmente <b><u id='descripcion_anexos_contadormax'></u></b> caracteres)</div>
								</td>
								<td rowspan="8" width="0%">
									<div id="viewer3" style="height:55vh;"></div>
									<iframe id="viewer" frameborder="0" scrolling="yes" width="100%"  style='display: none; height: 55vh;'>
									</iframe>
								</td>
							</tr>
							<tr>
								<td class="descripcion descripcion_ancho" style="height: 50px;">
									Nombre Completo Empresa o Persona:
								</td>
								<td class="detalle">
									<input type="search" name="nombre_completo" id="nombre_completo" placeholder="Ingrese Nombres y Apellidos completos (sin numeros)" title="Ingrese Nombres y Apellidos completos (sin numeros)" value="<?php echo $nombre_remitente_destinatario; ?>">
									<div id="sugerencias_nombre_completo" style="overflow-x: auto; max-height: 100px;"></div>

									<div id="nombre_completo_max" class="errores">El nombre completo del usuario no puede ser mayor a 100 caracteres. (Actualmente <b><u id='nombre_completo_contadormax'></u></b> caracteres)</div>
									<div id="nombre_completo_min" class="errores">El nombre  del usuario (con apellido) no puede ser menor a 6 caracteres (sin numeros)</div>
									<div id="nombre_completo_null" class="errores">El nombre completo del usuario es obligatorio.</div>
								</td>
								<td class="descripcion descripcion_ancho">
									Dignatario de la Empresa o Entidad que Remite el Radicado:
								</td>
								<td class="detalle">
									<input type="search" name="dignatario_remitente" id="dignatario_remitente" placeholder="No aplica para personas naturales. Solo para Entidad o Empresa" title="No aplica para personas naturales. Solo para Entidad o Empresa" value="<?php echo $dignatario ?>">
									<div id="sugerencias_dignatario" style="overflow-x: auto; max-height: 100px;"></div>

									<div id="dignatario_remitente_min" class="errores">El nombre del dignatario (con apellidos) no puede ser menor a 6 caracteres (sin numeros)</div>
									<div id="dignatario_remitente_max" class="errores">El nombre completo del dignatario no puede ser mayor a 100 caracteres (Actualmente <b><u id='dignatario_remitente_contadormax'></u></b> caracteres)</div>
									<div id="dignatario_remitente_null" class="errores">El nombre completo del dignatario es obligatorio.</div>
								</td>
							</tr>
							<tr>	
								<td class="descripcion descripcion_ancho">
									Ubicación Remitente del Radicado:
								</td>
								<td class="detalle">
									<input type="search" name="ubicacion_remitente" id="ubicacion_remitente" placeholder="Ingrese la ubicación del remitente" title="Ingrese la ubicación del remitente" value="<?php echo $ubicacion; ?>">
									<div id="sugerencias_ubicacion_remitente" style="display:none"></div>

									<div id="ubicacion_remitente_min" class="errores">El nombre del municipio no puede ser menor a 3 caracteres (sin numeros)</div>
									<div id="error_ubicacion_remitente" class="errores">
										No se han encontrado resultados. Si desea ingresar un nuevo municipio comuníquese con el administrador del sistema</a>
									</div>
									<div id="error_no_selecciona_ubicacion" class="errores">Seleccione una ubicación válida por favor</div>
								</td>
								<td class="descripcion descripcion_ancho">
									Telefono Remitente del Radicado:
								</td>
								<td class="detalle">
									<input type="search" placeholder="Digite Teléfono del remitente (Si tiene extensión también)" title="Digite Teléfono del remitente (Si tiene extensión también)" id="telefono_remitente" name="telefono_remitente" value="<?php echo $telefono; ?>">
									<div id="sugerencias_telefono"></div>

									<div id="telefono_remitente_max" class="errores">El telefono del contacto no puede ser mayor a 50 caracteres. (Actualmente <b><u id='telefono_remitente_contadormax'></u></b> caracteres)</div>
									<div id="telefono_remitente_min" class="errores">El telefono del contacto no puede ser menor a 6 caracteres.</div>
								</td>
							</tr>
							<tr>
								<td class="descripcion descripcion_ancho">
									Direccion Remitente del Radicado:
								</td>
								<td class="detalle">
									<input type="search" name="direccion_remitente" id="direccion_remitente" placeholder="Digite dirección completa del remitente" title="Digite dirección completa del remitente" value="<?php echo $direccion; ?>">
									<div id="sugerencias_direccion"></div>
									<div id="direccion_remitente_min" class="errores">La dirección del contacto no puede ser menor a 6 caracteres (numeros o letras) </div>
									<div id="direccion_remitente_max" class="errores">La dirección del contacto no puede ser mayor a 100 caracteres. (Actualmente <b><u id='direccion_remitente_contadormax'></u></b> caracteres) </div>
								</td>
								<td class="descripcion descripcion_ancho">
									Mail Remitente del Radicado:
								</td>
								<td class="detalle">
									<input type="email" name="mail_remitente" id="mail_remitente" placeholder="Ingrese el correo electrónico del remitente" title="Ingrese el correo electrónico del remitente" value="<?php echo $mail; ?>" style="width: 95%;">
									<div id="sugerencias_mail"></div>

									<div id="mail_remitente_max" class="errores">El mail del usuario no puede ser mayor a 50 caracteres. (Actualmente <b><u id='mail_remitente_contadormax'></u></b> caracteres)</div>
									<div id="mail_remitente_null" class="errores">El mail del usuario es obligatorio.</div>
									<div id="mail_remitente_formato_mail" class="errores">
										El mail ingresado no tiene formato correcto (usuario@algunmail.com) por lo que no se puede crear.
									</div>
								</td>
							</tr>
							<tr>
								<td class="descripcion descripcion_ancho">
									Asunto:
								</td>
								<td class="detalle" colspan="3">
									<textarea name="asunto_radicado" id="asunto_radicado" rows="2" style="width:97%;padding:5px;" placeholder="Ingrese el asunto del radicado. Sea lo más específico posible" title="Ingrese el asunto del radicado. Sea lo más específico posible"><?php echo $asunto; ?></textarea>
									<div id="asunto_radicado_max" class="errores">El asunto no puede ser mayor a 500 caracteres (numeros o letras). (Actualmente <b><u id='asunto_radicado_contadormax'></u></b> caracteres)</div>
									<div id="asunto_radicado_min" class="errores">El asunto no puede ser menor a 6 caracteres (numeros o letras) </div>
									<div id="asunto_radicado_null" class="errores">El asunto es obligatorio</div>
								</td>
							</tr>
								<table border="0" width="95%">
									<tr>
										<td class="descripcion descripcion_ancho">
											Dependencia Destino:
										</td>
										<td class="detalle detalle_ancho" colspan="2">
											<input type="hidden" id="codigo_dependencia" name="codigo_dependencia" placeholder="codigo_dependencia" value='<?php echo "$dependencia_destino"; ?>'><!-- Tipo de radicacion de entrada (2) por defecto. -->
											<input type="hidden" id="nombre_dependencia_destino" placeholder="nombre_dependencia_destino" value='<?php echo "$nombre_dependencia_destino"; ?>'><!-- Nombre completo de la dependencia destino para usarla en el sticker. -->
											<input type="hidden" id="codigo_contacto" name="codigo_contacto" placeholder="codigo_contacto" value="1"><!-- Codigo Contacto. (1) por defecto. -->
											<input type="hidden" name="usuario_destino" id="usuario_destino" name="usuario_destino" placeholder="usuario_destino"  value='<?php echo "$usuario_actual"; ?>'> <!-- Usuario del sistema con perfil "Distribuidor de dependencia" que va a recibir el radicado -->
								 			<input type="text" name ="search_dependencia_destino" id="search_dependencia_destino" placeholder="Ingrese la dependencia a la que va a ser asignado este documento" title="Ingrese la dependencia a la que va a ser asignado este documento" value='<?php echo "$nombre_dependencia_destino"; ?>' style='width: 90%;'><br>
											<div id="sugerencias_remitente" style="display:none"></div>

											<div id="sugerencias_dependencia_destino"></div>
											<div id="search_dependencia_destino_min" class="errores">La dependencia de destino no puede ser menor a 6 caracteres (numeros o letras)</div>
											<div id="search_dependencia_destino_max" class="errores">La búsqueda puede tener máximo 50 caracteres.</div>
											<div id='sin_distribuidor' class='errores'>
												En la dependencia seleccionada no existe un usuario con el perfil
												<b>	'DISTRIBUIDOR_DEPENDENCIA' </b>
												por lo que no se puede radicar a esta dependencia.<br> Comuniquese con el administrador del sistema.
											</div>
										</td>
										<td class="descripcion descripcion_ancho" rowspan="2">
											Archivo PDF (Imagen Principal del Radicado):
										</td>
										<td class="detalle detalle_ancho" colspan="2" rowspan="2">
										<?php 
											/* Este es el div que se muestra para modulo con carpeta asíncrona de archivos PDF */
											if($permiso_scanner=="SI"){	
											echo "<div id='lista_documentos_escaneados'></div>";
											}
										?>		
											<input type="file" name="archivo_pdf_radicado" id="archivo_pdf_radicado" onchange="validar_input_file('archivo_pdf_radicado'); mover_descripcion_ancho('archivo_pdf_radicado'); ocultar_lista_documentos_escaneados();">
											<div id="archivo_pdf_radicado_invalido" class="errores"> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
											<div id="archivo_pdf_radicado_tamano" class="errores">El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
										</td>
									</tr>									
								</table>
						</table>
					</center>
				</form>
				<center id='div_boton_enviar'>
					<input type="button" value="Modificar Radicado" id="modificar_radicado" onclick="modificar_radicado()" class="botones">
				</center>
			</div>
			<script type="text/javascript">$("#numero_guia_radicado").focus()</script>
<?php
	} // Fin si es un radicado diferente a "INV" y a interno
}
?>
<div id='resultado_total'></div>
</body>
</html>