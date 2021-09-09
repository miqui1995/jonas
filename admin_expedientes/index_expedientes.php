<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once "../login/validar_inactividad.php";
require_once '../login/conexion2.php' // Para la consulta del expediente
?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Buscador de Expedientes</title>
	<script type="text/javascript" src="include/js/funciones_expedientes.js"></script>
	<link rel="stylesheet" href="include/css/estilos_expedientes.css">
</head>
<body>
<!--Desde aqui el div que contiene el formulario para agregar expediente-->
	<div>
	<?php
/* Desde aqui arma el select del año para creacion expedientes */
$year_actual = date("Y"); // Se obtiene el año en formato 4 digitos
$opcion_year = "";
for ($i = 1980; $i <= 2021; $i++) {
    // Mostrar años 1980 hasta 2020
    if ($year_actual == $i) {
        // Seleccionar por defecto el año actual
        $opcion = "<option value='$i' selected='selected'>$i</option>";
    } else {
        $opcion = "<option value='$i'>$i</option>";
    }
    $opcion_year = $opcion_year . $opcion;
}
/* Hasta aqui arma el select del año para creacion expedientes */
/* Desde aqui arma el select de las dependencias para creacion de expedientes */
$query_dependencias = "select * from dependencias where activa='SI' and codigo_dependencia!='ADM' order by codigo_dependencia";
$fila_dependencias  = pg_query($conectado, $query_dependencias);

/*Calcula el numero de registros que genera la consulta anterior.*/
$registros_dependencia = pg_num_rows($fila_dependencias);

//    var_dump($_SESSION);
$dependencia_usuario = $_SESSION['dependencia'];
$opcion_dependencia  = "";
for ($i = 0; $i < $registros_dependencia; $i++) {
    $linea = pg_fetch_array($fila_dependencias);

    $codigo_dependencia = $linea['codigo_dependencia'];
    $nombre_dependencia = $linea['nombre_dependencia'];

    if ($dependencia_usuario == $codigo_dependencia) {
        $dependencia = "<option value='$codigo_dependencia' title='$nombre_dependencia' selected='selected'><b>$codigo_dependencia</b></option>";
    } else {
        $dependencia = "<option value='$codigo_dependencia' title='$nombre_dependencia'><b>$codigo_dependencia</b></option>";
    }
    $opcion_dependencia = $opcion_dependencia . $dependencia;
}
/* Hasta aqui arma el select de las dependencias para creacion de expedientes */
/* Desde aqui arma el select de las series para creacion de expedientes */
$query_series = "select * from series where activo='SI' order by codigo_serie";

$fila_series = pg_query($conectado, $query_series);

/*Calcula el numero de registros que genera la consulta anterior.*/
$registros_series = pg_num_rows($fila_series);

$opcion_serie = "";

if ($registros_series == 0) {
    echo "<script>$('#error_serie').slideDown('slow');</script>";
} else {

    for ($i = 0; $i < $registros_series; $i++) {
        $linea = pg_fetch_array($fila_series);

        $codigo_serie = $linea['codigo_serie'];
        $nombre_serie = $linea['nombre_serie'];

        $serie_activa = "<option value='$codigo_serie' title='($codigo_serie) $nombre_serie'>$codigo_serie</option>";

        $opcion_serie = $opcion_serie . $serie_activa;
    }
    echo "<script>$('#error_serie').slideUp('slow'); genera_lista_subserie();</script>";
}
/* Hasta aqui arma el select de las series para creacion de expedientes */
?>
		<div id="ventana" class="ventana_modal">
			<div class="form" style="overflow: scroll; max-height: 80vh;">
				<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nuevo Expediente</h1>
				<hr>
				<form method="post" id="form_exp" enctype="multipart/form-data" autocomplete="off">
					<table border ="0">
						<tr>
							<td class="detalle" colspan="2">
							<center>
								<input type="hidden" name ="formulario_agregar_expediente" id="formulario_agregar_expediente" value="crear_expediente">

								<input type="search" disabled="true" class="readonly" name="identificador_expediente" id="identificador_expediente" value="EXP" title="Identificador del expediente">
								<select name="year_expediente" id="year_expediente" class='select_opciones' onchange="genera_consecutivo_temporal('1')" title="Año del Expediente">
									<?php echo "$opcion_year"; ?>
								</select>
								<select name="dependencia_expediente" id="dependencia_expediente" class='select_opciones' onchange="genera_consecutivo_temporal('1');genera_lista_serie(); genera_lista_subserie()" title="Dependencia del Expediente">
									<?php echo "$opcion_dependencia"; ?>
								</select>

								<select id="serie_expediente" class='select_opciones' onchange="genera_lista_subserie(); genera_consecutivo_temporal('1');validar_serie_sub_serie()" title ="Serie del expediente" >
									<?php echo "$opcion_serie"; ?>
								</select>

								<select id="subserie_expediente" class='select_opciones' onchange="genera_consecutivo_temporal('1');oculta_error_subserie();validar_serie_sub_serie()" title="Subserie del expediente"></select>

								<input type="search" disabled="true" class="readonly" style="width: 100px;" name="consecutivo_expediente" id="consecutivo_expediente" value="1234567" title="Consecutivo Temporal del Expediente">
								<br>

								<div id="error_serie" class="errores">No existe una serie configurada con esta dependencia. Comuníquese con el administrador del sistema para crearla.</div>
								<div id="error_subserie" class="errores">No existe una subserie configurada con esta dependencia y serie. Comuníquese con el administrador del sistema para crearla.</div>
								<div id="error_subserie_2" class="errores">Debe seleccionar una subserie. Verificar por favor.</div>

							</center>
							</td>
						</tr>
						<tr>
							<td class="descripcion" style="width: 15%;">Nombre del Expediente :</td>
							<td class="detalle">
								<textarea type="search" placeholder="Digite Nombre del Expediente" style="width:95%; padding: 5px;" name="nombre_expediente" id="nombre_expediente" onblur="validar_input('nombre_expediente');" title="Este es el Nombre del Expediente"></textarea>
								<!-- <input type="search" placeholder="Digite Nombre del Expediente" style="width:95%;" name="nombre_expediente" id="nombre_expediente" onblur="validar_input('nombre_expediente');trim('nombre_expediente')" title="Este es el Nombre del Expediente"> -->

								<div id="sugerencia_nombre_expediente" class="sugerencia"></div>

								<div id="error_nombre_expediente" class="errores">El nombre del expediente ya existe, no es posible crear un nuevo expediente con éste nombre</div>

								<div id="nombre_expediente_max" class="errores">El nombre del expediente no puede ser mayor a 600 caracteres. (Actualmente <b><u id='nombre_expediente_contadormax'></u></b> caracteres)</div>
								<div id="nombre_expediente_min" class="errores">El nombre del expediente no puede ser menor a 6 caracteres (numeros o letras)</div>
								<div id="nombre_expediente_null" class="errores">El nombre del expediente es obligatorio</div>
							</td>
						</tr>
						<!--Se agrega columna y fila para cargar el contenedor de documentos de cotratos-->
						<tr>
							<td colspan="2">
								<div id='contenedor_crear_documentos' style='float: left; width: 100%;'></div>
								<iframe frameborder='0' id='contenedor_visor_documentos' scrolling='yes' style='height: 0px; background-color: #008080; float: left;'></iframe>
							</td>
						</tr>	
						<tr>
							<td colspan="2">
								<center id="boton_crear_expediente">
									<input type="button" value="Crear Expediente" id="bEnviar_expediente" class="botones" onclick="submit_agregar_expediente()">
								<center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para agregar expediente-->
<!--Desde aqui el div que contiene el formulario para Modificar expediente-->
		<div id="ventana2">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrarVentanaModificarExpediente();'>Cerrar X</a></div>
				<h1>Formulario Modificar Expediente</h1>
				<hr>
				<form autocomplete="off">
					<table>
						<tr>
							<td class="detalle" colspan="2">
							<center>
								<input type="hidden" name ="formulario_modificar_expediente" id="formulario_modificar_expediente" value="modificar_expediente">
								<input type="hidden" name ="id_expediente_mod" id="id_expediente_mod">

								<input type="search" disabled="true" class="readonly" name="identificador_expediente" id="identificador_expediente" value="EXP" title="Identificador del expediente">
								<input type="search" disabled="true" class="readonly" name="year_expediente_mod" id="year_expediente_mod" title="Año del Expediente">
								<input type="search" disabled="true" class="readonly" name="dependencia_expediente_mod" id="dependencia_expediente_mod" title="Dependencia del Expediente">
								<input type="search" disabled="true" class="readonly" name="serie_expediente_mod" id="serie_expediente_mod" title="Serie del Expediente">
								<input type="search" disabled="true" class="readonly" name="subserie_expediente_mod" id="subserie_expediente_mod" title="Subserie del Expediente">
								<input type="search" disabled="true" class="readonly" style="width: 100px;" name="consecutivo_expediente_mod" id="consecutivo_expediente_mod" title="Consecutivo Temporal del Expediente">
							</center>
							</td>
						</tr>
						<tr>
							<td class="descripcion" style="width: 15%;">Nombre del Expediente :</td>
							<td class="detalle">
								<textarea type="search" placeholder="Digite Nombre del Expediente" style="width:95%; padding: 5px;" name="nombre_expediente_mod" id="nombre_expediente_mod" onblur="validar_grabar_expediente_mod()" title="Este es el Nombre del Expediente"></textarea>
								<div id="sugerencia_nombre_expediente_mod" class="sugerencia"></div>
								<div id="nombre_expediente_mod_max" class="errores">El nombre del expediente no puede ser mayor a 500 caracteres. (Actualmente <b><u id='nombre_expediente_mod_contadormax'></u></b> caracteres)</div>
								<div id="nombre_expediente_mod_min" class="errores">El nombre del expediente no puede ser menor a 6 caracteres (numeros o letras)</div>
								<div id="nombre_expediente_mod_null" class="errores">El nombre del expediente es obligatorio</div>
								<div id="nombre_expediente_mod_ya_existe" class="errores">El nombre del expediente ya existe, no es posible crear un nuevo expediente con éste nombre</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<center id='boton_modificar_expediente'>
									<input type="button" value="Modificar Expediente" id="enviar_mod_expediente" class="botones" onclick="submit_modificar_nivel()">
								<center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para modificar expediente -->

		<div class="center" id="logo">
			<br>
			<h1 style="margin-top:-10px;">Configuración Expedientes</h1>
		</div>
		<div class="form center">
				<input type="search" id="search_expediente" style="width: 80%;" placeholder="Ingrese Nombre o Número del Expediente">
		</div>
		<div id="desplegable_resultados"></div>
		<div id="error_expediente" class="errores">El nómbre o código de expediente que ha digitado no se encuentra en la base de datos.
			Este campo es obligatorio. En caso que no encuentre un expediente correcto, comuníquese por favor con el administrador del sistema.</div>

	</div>	<!--Hasta aqui el div que contiene el formulario para agregar expediente-->
	<script type="text/javascript">
		 /* Inicio validación que los campos de Formulario Agregar Nuevo expediente (Submit) */
function submit_agregar_expediente() {
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
        var codigo_subserie = $("#subserie_expediente").val();

        if (codigo_subserie == "---" || codigo_subserie==null) {
            $("#error_subserie_2").slideDown("slow");
            console.log("error_subserie")
            return false;
        }
        /*LLama a la funcion validar input y le envia parametro del valor de el nombre de expediente*/ 
        validar_serie_sub_serie();
        validar_input('nombre_expediente');

        /* Valida si los metadatos se encuentran correctamente ingresados */
            /*

					var input_file_1 = document.getElementById('contrato');
                    var input_file_2 = document.getElementById('hoja_de_vida');
                    var input_file_3 = document.getElementById('cedula_ciudadania');
                    var input_file_4 = document.getElementById('rut');
                    var input_file_5 = document.getElementById('certificacion_bancaria');
                    var input_file_6 = document.getElementById('examen_ingreso');

		            var data         = new FormData();

                    if(input_file_1!=null){
                        var file1                   = input_file_1.files[0];
                        var file2                   = input_file_2.files[0];
                        var file3                   = input_file_3.files[0];
                        var file4                   = input_file_4.files[0];
                        var file5                   = input_file_5.files[0];
                        var file6                   = input_file_6.files[0];
                        var valor_contrato1         = $("#valor_contrato").val();
                        var pago_mensual_contrato1  = $("#pago_mensual_contrato").val();

                        data.append('pdf_contrato',file1);
                        data.append('pdf_hoja_de_vida',file2);
                        data.append('pdf_cedula_ciudadania',file3);
                        data.append('pdf_rut',file4);
                        data.append('pdf_certificacion_bancaria',file5);
                        data.append('pdf_examen_ingreso',file6);
                        data.append('valor_contrato',valor_contrato1);
                        data.append('pago_mensual_contrato',pago_mensual_contrato1);
                    }

            // var cantidad_metadatos = $("#cantidad_registros_query_metadatos").val();
            var json_metadatos = "";

                    for (var i = 1; i <=cantidad_metadatos; i++) {
                        var nombre_metadato = $("#nombre_metadato_"+i).val();
                        var nom_metadato    = "metadato_"+i;
                        var tipo_metadato   = "tipo_metadato_"+i;

                        json_metadatos+= nombre_metadato+" - ";

                        if($("#campo_obligatorio_"+i).val()=="SI"){
                            var c       = $("#metadato_"+i).val();
                            var file_m  = "file_"+nombre_metadato.toLowerCase(); 

                            if($("#"+tipo_metadato).val()=="texto"){
                                var requiere_anexo = "requiere_anexo_"+i;

                                if($("#"+requiere_anexo).val()=="SI"){
                                    if($("#"+file_m).val()==""){
                                        $("#"+file_m+"_null").slideDown("slow");
                                    }else{
                                        console.log("validar_input2--> "+file_m)
                                        validar_input_file_animado(file_m,'visor_documentos_por_cargar_metadatos','tabla_informacion_metadatos');
                                        $("#"+file_m+"_null").slideUp("slow");
                                    }
                                }
                            }
                        // console.log(nombre_metadato+" - "+nom_metadato+" - "+tipo_metadato)


                            if($("#metadato_"+i).val()==""){
                                $("#metadato_"+i+"_null").slideDown("slow");
                                $("#metadato_"+i).focus();
                                console.log("metadato"+i+"null - abajo")
                                break;
                            }else{
                                $("#metadato_"+i+"_null").slideUp("slow");
                            }

                        }
                      
                    }
                    console.log(json_metadatos)

                    validar_input_file_animado(file_m,'visor_documentos_por_cargar_metadatos','tabla_informacion_metadatos');
            */
        /* Fin valida si los metadatos se encuentran correctamente ingresados */

        if ($(".errores").is(":visible")) {
            if ($(".art").is(":visible")) {
                $("#error_nombre_expediente").slideDown("slow");
                return false;
            }
            return false;
        }else{
           	var data         = new FormData();

            var tipo_formulario1        = $("#formulario_agregar_expediente").val();
            var nombre_expediente1      = $("#nombre_expediente").val();
            var dependencia_expediente1 = $("#dependencia_expediente").val();
            var serie_expediente1       = $("#serie_expediente").val();
            var subserie_expediente1    = $("#subserie_expediente").val();
            var year1                   = $("#year_expediente").val();
            var consecutivo_expediente1 = $("#consecutivo_expediente").val();
                
            data.append('tipo_formulario',tipo_formulario1);
            data.append('nombre_expediente',nombre_expediente1);
            data.append('dependencia_expediente',dependencia_expediente1);
            data.append('serie_expediente',serie_expediente1);
            data.append('subserie_expediente',subserie_expediente1);
		    data.append('year',year1);
            data.append('consecutivo_expediente',consecutivo_expediente1);

            loading('boton_crear_expediente'); // Funcion especificada en include/js/funciones_menu.js

            $.ajax({
                type        : 'POST',
                url         : 'admin_expedientes/query_expedientes.php',
                data        : data,         
                contentType : false,
                processData : false,
                success: function(resp){
                    if(resp!=""){
                        $('#resultado_js').html(resp);
                    }
                }
            })        
        }
    }
}
/* Fin validación que los campos de Formulario Agregar Nuevo expediente (Submit) */
	</script>
</body>
</html>