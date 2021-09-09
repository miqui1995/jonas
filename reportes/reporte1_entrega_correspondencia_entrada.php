<?php 
	if(!isset($_SESSION)){
	    session_start();
	}
 ?>
<script type="text/javascript">

/* Funcion para cargar reporte 1 */
function cargar_reporte_1(){
	var fecha_inicial 	= $("#fecha_inicial").val();
	var fecha_final 	= $("#fecha_final").val();

	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',
		data: {
			'recibe_ajax'  	 	: 'reporte1',
			'fecha_inicial' 	: fecha_inicial,
			'fecha_final' 		: fecha_final
		},
		success: function(resp){
			if(resp!=""){
				$('#resultado_reporte').html(resp);
			}
		}
	})
	
}
/* Funcion para cargar reporte 1 */
var temporizador = "";

// Accion que se activa cuando se digita en campo con id #search_lista_radicados
$('#search_lista_radicados').on("input", function(e) {
	espacios_formulario('search_lista_radicados','sin_caracteres');
	loading('desplegable_resultados_lista_radicados');

    //variable que se usa para traer el valor del elemento actual (this)
    var envio_radicado  		= $(this).val();
    var radicado_concatenado  	= $('#lista_radicados_fisicos').val();

    temporizador = setTimeout(function() {
           
        $("#error_buscador_ubicacion_fisica").slideUp("slow");
        //actualiza la lista de resultados
        $.ajax({
            type: 'POST',
            url: 'admin_radicados_fisicos/buscador_ubicacion_fisica.php',
            data: {
                'search_ubicacion_fisica_planilla'  : envio_radicado,
                'radicado_concatenado'     			: radicado_concatenado
            },
            success: function(resp) {
                //Condicion si el archivo buscador le responde algo
                if (resp != "") {
                    $('#desplegable_resultados_lista_radicados').html(resp);
       				$("#formulario_por_defecto").slideUp();
                }
            }
        })      
    }, 1000);  //1000 milisegundos == 1 segundo
}); // fin accion que se activa cuando se digita en campo con id #search_lista_radicados


/**************************************************************
Inicio funcion resetear_busqueda
/**************************************************************
* @brief Funcion para ocultar la lista de resultados y borrar el valor de search_ubicacion_fisica
* @param {string} () Esta funcion no recibe parametros
* @return {true} Retorna true para ejecutar los cambios 
**************************************************************/
function resetear_busqueda() {
    $('#desplegable_resultados_ubicacion_fisica').html('');
    $('#search_ubicacion_fisica').val('');
    $('#search_ubicacion_fisica').focus();
}
/*************Fin funcion resetear_busqueda********************/
/**************************************************************
Inicio funcion cargar_modificar_ubicacion_fisica
/**************************************************************
* @brief Funcion para controlar diferentes eventos cuando le de click en alguna parte de la lista de resultados
* @param {string} () Esta funcion recibe los parametros (radicado) y login desde la pantalla de index_ubicacion_radicados_fisicos.php 
* @return {true} Retorna true siempre
**************************************************************/
function cargar_modificar_ubicacion_fisica(radicado, login) {
    //muestra el campo de radicados concatenados
    // Traer el valor actual del campo radicados_concatenados
    var valor_input = $("#lista_radicados_fisicos").val();
    // Se concatena radicado nuevo con los radicados que ya estan en el campo
    var radicado_concatenado = valor_input + radicado + ",";
    /******************se retrasa la actuazlizacion de la tabla 100 milisegundos para que no cause conflicto (se duplica el valor) con el proceso de concatenacion de radicados*************************/
    setTimeout(function() {
        //trae el valor del campo radicados_concatenados
        $('#lista_radicados_fisicos').val(radicado_concatenado);
        //Se actualiza la tabla cada vez que le de click en algun resultado de busqueda
        $.ajax({
            type: 'POST',
            url: 'include/procesar_ajax.php',
            data: {
                'recibe_ajax'           : 'tabla_ubicacion_fisica_radicados',
                'radicado_concatenado'  : radicado_concatenado,
                'tipo_boton' 			: 'entregar_documentos'
            },
            success: function(resp) {
                $('#tabla_resultados_dinamica').html(resp);
                $('#desplegable_resultados_lista_radicados').html("");
				$("#search_lista_radicados").val("");
  				$("#search_lista_radicados").focus();
  				cargar_query_planilla(radicado, login);
            }
        })
    }, 100);
    //resetea la busqueda
    resetear_busqueda();
}
// Fin de funcion cargar_modificar_ubicacion_fisica
function quitar_radicado_lista(radicado) {
    var lista_radicados_fisicos = $('#lista_radicados_fisicos').val();
    var nuevo_listado_radicados = lista_radicados_fisicos.replace(radicado + ",", "");
    $('#lista_radicados_fisicos').val(nuevo_listado_radicados);
    /******************se retrasa la actuazlizacion de la tabla 100 milisegundos para que no cause conflicto (se duplica el valor) con el proceso de concatenacion de radicados*************************/
    setTimeout(function() {
        //Se actualiza la tabla cada vez que le de click en algun resultado de busqueda
        $.ajax({
            type: 'POST',
            url: 'include/procesar_ajax.php',
            data: {
                'recibe_ajax'           : 'tabla_ubicacion_fisica_radicados',
                'radicado_concatenado'  : nuevo_listado_radicados,
                'tipo_boton' 			: 'entregar_documentos'

            },
            success: function(resp) {
                $('#tabla_resultados_dinamica').html(resp);
            }
        })
        $('#search_lista_radicados').focus();
    }, 100);
}

function cargar_query_planilla(radicado, login){
    var lista_radicados_fisicos = $('#lista_radicados_fisicos').val();

	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax'           : 'tabla_ubicacion_fisica_radicados',
            'carga_query' 			: "SI",
            'radicado_concatenado'  : lista_radicados_fisicos,
            'tipo_boton' 			: 'entregar_documentos'
        },
        success: function(resp) {
            $('#query_planilla').val(resp);
            console.log(resp)
        }
    })
}

function validar_aprueba_firma() {
    var login_aprueba   = $("#login_aprueba").val();
    var pass_aprueba    = $("#contr_confirma_aprobado").val();
    var codigo_entidad1 = $("#codigo_entidad").val();

    espacios_formulario('observaciones_planilla1', 'primera', 0);

    $.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax'   : 'validar_aprueba_firma',
            'tipo'          : 'ubicacion_fisica_radicados',
            'login'         : login_aprueba,
            'pass'          : pass_aprueba
        },
        success: function(respuesta) {
            if (respuesta != "true") {
                $("#error_contr_confirma_aprobado_ubicacion_fisica").slideDown("slow");
                $("#contr_confirma_aprobado").focus();
            }else{
                $("#error_contr_confirma_aprobado_ubicacion_fisica").slideUp("slow");
                Swal.fire({
                    title               : 'Desea generar la plantilla de entrega del listado de radicados.',
                    text                : "Esta acción no se puede revertir. ¿Está seguro?",
                    type                : 'warning',
                    showCancelButton    : true,
                    confirmButtonColor  : '#3085d6',
                    cancelButtonColor   : '#d33',
                    confirmButtonText   : '¡Si,acepto!',
                    cancelButtonText    : 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        var radicado_concatenadado 	= $('#lista_radicados_fisicos').val();

						var query_pdf 			 	= $('#query_planilla').val();

                        $.ajax({
                            type: 'POST',
                            url: 'include/procesar_ajax.php',
                            data: {
                                'recibe_ajax'           : 'aprobar_ubicacion_radicado_reporte',
                                'radicado_concatenadado': radicado_concatenadado,
                                'usuario_aprueba'       : login_aprueba
                            },
                            success: function(resp) {
                                $("#validar_aprobacion_ubicacion_fisica").html(resp);
                                console.log(resp)
                                var observaciones = $("#observaciones_planilla1").val();

                            	if(resp!='error_query'){
                            		$("#boton_validar_aprueba").html("<a href='formatos/reporte_excel.php?codigo_entidad=$codigo_entidad&nombre_reporte=reporte1_entrega_correspondencia_entrada&sql="+query_pdf+"' style='text-decoration:none;'><img src='imagenes/iconos/logo_excel.png' width='30' height='25' title='Exportar a excel' style='padding : 10px;'></a><a href='formatos/reporte_pdf.php?codigo_entidad="+codigo_entidad1+"&nombre_reporte=reporte1_entrega_correspondencia_entrada&observaciones="+observaciones+"&sql="+query_pdf+"' style='text-decoration:none;'><img src='imagenes/iconos/archivo_pdf.png' width='30' height='25' title='Exportar a PDF' style='padding : 10px; text-decoration:none;'></a>")
                            		$(".tabla_contrasena").html('') // Oculto tabla de contraseña
                            		// 
                            	}else{
                            		alert('Ocurrió un error al registrar la firma electrónica para recibir los documentos fisicos, por favor revisar e intentar nuevamente.')
                            	}
                            }
                        });
                    }
                })
            }
        } //Fin success ajax 1
    }) //Fin ajax verifica contraseña usuario
} //Fin funcion valida contraseña usuario, hace la actualizacion de propietario y muestra un sweet alert
/* Fin funciones formulario modificar radicado salida */
</script>
<?php 
	$login 			= $_SESSION['login'];
	$codigo_entidad = $_SESSION['codigo_entidad'];
   	echo "<center>
   		<h1>Buscador radicados para entregar en planilla</h1>
		<input type='hidden' id='codigo_entidad' name='codigo_entidad' value='$codigo_entidad')>
		<input type='hidden' id='login_aprueba' name='login_aprueba' value='$login')>
   		<input type='hidden' id='lista_radicados_fisicos'>
   		<input type='hidden' id='query_planilla'>
		<div>
			<input type='search' name='search_lista_radicados' id='search_lista_radicados' title='Ingrese aquí el numero de radicado que va a entregar en la planilla' placeholder='Ingrese aquí el numero de radicado que va a entregar en la planilla' style='border-radius: 8px; padding: 6px; width: 80%;'>
		</div>
		</center>
		<div id='lista_radicados_min' class='errores'>La consulta no puede ser menor a 3 caracteres.</div>
		<!-- Inicio de contenedor que se usa para cargar los resultados de busqueda. Este contenido de este div cambia dinamicamente cada vez que hace un keyup en el campo search_lista_radicados -->
		<div id='desplegable_resultados_lista_radicados' style='padding: 10px;'></div>
		<div id='tabla_resultados_dinamica' style='padding: 10px;'></div>
	";

	$nombre_dependencia = $_SESSION['nombre_dependencia'];

   	echo "<center><div id='formulario_por_defecto'><hr><h1>Reporte entrega en Físico de Correspondencia de Entrada de la dependencia <br>$nombre_dependencia</h1>";

	$timestamp_dia  = date('Y-m-d');	// Genera la fecha de transaccion	

	echo"<table border='0' class='center' width='600px'>
		<tr>
			<td class='descripcion'>Fecha Inicial</td>
			<td class='descripcion'>Fecha Final</td>
		</tr>
		<tr>
			<td style='padding-right:20px;'>
				<input type='date' id='fecha_inicial' value='$timestamp_dia' class='input_search'  onchange='cargar_reporte_1()'>
			</td>
			<td style='padding-right:20px;'>
				<input type='date' id='fecha_final' value='$timestamp_dia' class='input_search' onchange='cargar_reporte_1()'><br>
			</td>
		</tr>
	</table>
	";	
?>
<div id="resultado_reporte"></div>
</center>
<script type="text/javascript">cargar_reporte_1(); $("#search_lista_radicados").focus();</script>