function validar_aprueba_firma() {
    var login_aprueba   = $("#login_aprueba").val();
    var pass_aprueba    = $("#contr_confirma_aprobado").val();
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
            if (respuesta.trim() != "true") {
                $("#error_contr_confirma_aprobado_ubicacion_fisica").slideDown("slow");
                $("#contr_confirma_aprobado").focus();
            }else{
                $("#error_contr_confirma_aprobado_ubicacion_fisica").slideUp("slow");
                Swal.fire({
                    title               : 'Desea recibir la plantilla de recibido del listado de radicados.',
                    text                : "Esta acción no se puede revertir. ¿Está seguro?",
                    type                : 'warning',
                    showCancelButton    : true,
                    confirmButtonColor  : '#3085d6',
                    cancelButtonColor   : '#d33',
                    confirmButtonText   : '¡Si,acepto!',
                    cancelButtonText    : 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        var radicado_concatenadado = $('#lista_radicados_fisicos').val();
                        $.ajax({
                            type: 'POST',
                            url: 'include/procesar_ajax.php',
                            data: {
                                'recibe_ajax'           : 'aprobar_ubicacion_radicado',
                                'radicado_concatenadado': radicado_concatenadado,
                                'usuario_aprueba'       : login_aprueba
                            },
                            success: function(resp) {
                                $("#validar_aprobacion_ubicacion_fisica").html(resp);
                                // genera_pdf();
                            }
                        });
                    }
                })
            }
        } //Fin success ajax 1
    }) //Fin ajax verifica contraseña usuario
} //Fin funcion valida contraseña usuario, hace la actualizacion de propietario y muestra un sweet alert
/* Fin funciones formulario modificar radicado salida */
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
                'radicado_concatenado'  : nuevo_listado_radicados
            },
            success: function(resp) {
                $('#tabla_resultados_dinamica').html(resp);
            }
        })
    }, 100);
}
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
                'tipo_boton'            : 'recibir_documentos'

            },
            success: function(resp) {
                $('#tabla_resultados_dinamica').html(resp);
            }
        })
    }, 100);
    //resetea la busqueda
    resetear_busqueda();
}
// Fin de funcion cargar_modificar_ubicacion_fisica
/************************************
 variable que se usa para le retrazo de 1 segundo cada vez que se dispare el evento onkeyup
 ********************/
var temporizador = "";
$(function buscador_ubicacion_fisica() {
    //Se para en el campo input con id search_ubicacion_fisica
    $('#search_ubicacion_fisica').focus();
    // Accion que se activa cuando se digita en campo con id #search_ubicacion_fisica
    $('#search_ubicacion_fisica').on("input", function(e) {
        //Muestra el efectos cargando
        loading('desplegable_resultados_ubicacion_fisica');
        //variable que se usa para traer el valor del elemento actual (this)
        var envio_radicado = $(this).val();
        //condicion si el atributo de data es diferente a la variable envio_radicado
        if ($(this).data("lastval") != envio_radicado) {
            $(this).data("lastval", envio_radicado);
            var radicado_concatenado = $('#lista_radicados_fisicos').val();
            //consulta el temporizador
            clearTimeout(temporizador);
            temporizador = setTimeout(function() {
                //condicion si la longitud del valor del input es mayor a 3 
                if (envio_radicado.length >= 3){
                    $("#error_buscador_ubicacion_fisica").slideUp("slow");
                    //actualiza la lista de resultados
                    $.ajax({
                        type: 'POST',
                        url: 'admin_radicados_fisicos/buscador_ubicacion_fisica.php',
                        data: {
                            'search_ubicacion_fisica'   : envio_radicado,
                            'radicado_concatenado'      : radicado_concatenado
                        },
                        success: function(resp) {
                            //Condicion si el archivo buscador le responde algo
                            if (resp != "") {
                                $('#desplegable_resultados_ubicacion_fisica').html(resp);
                            }
                        }
                    })
                // Fin condicion longitud de valor mayor a 3 y menor a 50 
                }else{
                    //si es menor a 3 caracteres muestra error
                    $("#error_buscador_ubicacion_fisica").slideDown("slow");
                } //fin else valor envio_radicado es menor a 3
               
            }, 1000);  //1000 milisegundos == 1 segundo
        } //Fin de condicion si el atributo de data es diferente a la variable envio_radicado
    }); // fin accion que se activa cuando se digita en campo con id #search_ubicacion_fisica
}) // Fin de $(function buscador_expediente()