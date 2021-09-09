cargar_accordion(); // Cargar funcion del menu desplegable "Accordion"

/* Funcion para mostrar botones_prestamos menu desplegable */
$(function carga_informacion_prestados_general(){
    $('#boton_documentos_prestados_general').click(function carga_informacion_prestados_general(){
        carga_informacion_prestamo('informacion_prestados_general');         
    });
}) 
$(function carga_informacion_prestados_usuario(){
    $('#boton_documentos_prestados_usuario').click(function carga_informacion_prestados_usuario(){
        carga_informacion_prestamo('informacion_prestados_usuario');         
    });
}) 
$(function carga_reporte_prestamos1(){
    $('#boton_reporte_prestamos1').click(function carga_reporte_prestamos1(){
        carga_informacion_prestamo('reporte_prestamos1');       
    });
}) 
$(function carga_solicitudes_prestamos_general(){
    $('#boton_solicitud_prestamos_general').click(function carga_solicitudes_prestamos_general(){
        carga_informacion_prestamo('informacion_solicitud_prestamos_general');         
    });
}) 
$(function carga_solicitudes_prestamos_usuario(){
    $('#boton_documentos_solicitados_usuario').click(function carga_solicitudes_prestamos_usuario(){
        carga_informacion_prestamo('informacion_solicitud_prestamos_usuario');         
    });
}) 
function calcular_fecha_limite(){
    var dias_solicitados=$("#dias_prestamo").val();

    $.ajax({
        type: 'POST',
        url: 'admin_prestamos/buscador_prestamos.php',
        data: {
            'dias_prestamo' : dias_solicitados,
            'tipo_consulta' : 'dias_solicitados'
        },          
        success: function(resp1){
            if(resp1!=""){
                $("#duracion_estimada_prestamo").html(resp1);
            }
        }
    })
}
function cancelar_solicitud_prestamo(id,numero_radicado,documento_solicitado){

  $.ajax({
    type: 'POST',
    url: 'bandejas/entrada/transacciones_radicado.php',
    data: {
      'cancelar_solicitud_id' : id,
      'documento_solicitado'  : documento_solicitado, 
      'numero_radicado'       : numero_radicado,
      'transaccion'           : 'cancelar_solicitud'
    },          
    success: function(resp1){
      if(resp1!=""){
        console.log(resp1);
        $("#resultado_js").html(resp1);
      }
    }
  })

}
function carga_informacion_prestamo(tipo_solicitud){
    $.ajax({
        type: 'POST',
        url: 'admin_prestamos/pestanas_prestamos.php',
        data: {
            'pestana' : tipo_solicitud
        },          
        success: function(resp1){
            if(resp1!=""){
                $("#"+tipo_solicitud).html(resp1);
            }
        }
    })  
}
/* Fin funcion para mostrar botones_prestamos menu desplegable */
/* Inicio cargar_prestamo_documento */
function cargar_prestamo_documento(id_documento, dias_solicitados, login_solicitante,id){
    $("#ventana_prestar_documento").slideDown("slow");

    $("#contr_confirma_recibido").focus();
    $("#contr_confirma_recibido").val("");
    $("#dias_prestamo").val(dias_solicitados);
    $("#id").val(id);
    $("#nombre_solicitante").val(login_solicitante);
    $("#nombre_solicitante1").html(login_solicitante);
    $("#numero_radicado").val(id_documento);
    $("#observaciones_solicitud_prestamo").val("");

    calcular_fecha_limite();
    validar_confirma_recibido();
    validar_input('dias_prestamo');
}
/* Fin cargar_prestamo_documento */   
/* Funcion confirmar documento físico recibido */
function confirmar_documento_fisico_recibido(id,tipo_documento,id_documento_solicitado,desde_formulario){

    Swal.fire({
        title:'¿Usted ha recibido '+tipo_documento.toLowerCase()+' '+id_documento_solicitado+' en físico?',
        text: "Esta acción no se puede revertir. ¿Está seguro?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, lo recibí!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: 'POST',
                url: 'include/procesar_ajax.php',
                data: {
                    'id_radicado' : id,
                    'recibe_ajax' : 'confirma_recibido_fisico'
                },          
                success: function(resp){
                    if(resp=="confirmado"){
                        // sweetAlert({
                        Swal.fire({ 
                            position            : 'top-end',
                            showConfirmButton   : false,
                            timer               : 1500,    
                            title               : 'Confirmado Recibido Físico',
                            text                : '',
                            type                : 'success'
                        }).then(function(isConfirm){
                            // if(isConfirm){
                            if(desde_formulario=="prestamo_documentos"){
                                carga_modulo_prestamos()
                            }else{
                                volver_busqueda();
                            }
                            // }
                        })
                    }else{
                        alert(resp)
                    }
                }
            })  

        }
    })
}
/* Fin funcion confirmar documento físico recibido */
function devolver_documento(id,documento_solicitado,id_documento_solicitado,numero_radicado){    
     Swal.fire({
        title:'¿Usted ha recibido '+documento_solicitado.toLowerCase()+' '+id_documento_solicitado+' en físico?',
        text: "Esta acción no se puede revertir. ¿Está seguro?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Gestión Documental lo está recibiendo!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: 'POST',
                url: 'bandejas/entrada/transacciones_radicado.php',
                data: {
                    'documento_solicitado'      : documento_solicitado,
                    'id_documento_solicitado'   : id_documento_solicitado,
                    'id_radicado'               : id,
                    'numero_radicado'           : numero_radicado,
                    'transaccion'               : 'confirma_devolucion_fisico'
                },   
                success: function(resp1){
                    if(resp1!=""){
                        console.log(resp1);
                        $("#resultado_js").html(resp1);
                    }
                }       
            })  
        }
    })
}
/* Funcion para realizar el prestamo */
function enviar_prestamo(){
    calcular_fecha_limite();
    validar_input('dias_prestamo');
    validar_input('observaciones_solicitud_prestamo');

    if($(".errores").is(":visible")){
        return false;
    }else{
        var confirma_recibido       = $("#contr_confirma_recibido").val();
        var observaciones_prestamo  = $("#observaciones_solicitud_prestamo").val();
        var radicado                = $("#numero_radicado").val();
        var termino                 = $("#dias_prestamo").val();

        if(confirma_recibido==""){
          var confirma_recibido1 = "NO";
        }else{
          var confirma_recibido1 = "SI";
        }
        var id = $("#id").val();

        $.ajax({
            type: 'POST',
            url: 'admin_prestamos/query_prestamos.php',
            data: {
                'confirma_recibido'         : confirma_recibido1,
                'id'                        : id,
                'observaciones_prestamo'    : observaciones_prestamo,
                'radicado'                  : radicado,
                'termino'                   : termino
            },          
            success: function(resp1){
                if(resp1!=""){
                    $("#resultado_prestamo_documento").html(resp1);
                }
            }
        })
    }
}
/* Fin funcion para realizar el prestamo */
function mostrar_boton_devolver(devolver_numero_radicado){
    if($("#"+devolver_numero_radicado).is(":visible")){
        $("#"+devolver_numero_radicado).slideUp("slow");
    }else{
        $("#"+devolver_numero_radicado).slideDown("slow");
    }
}

function valida_tipo_prestamo(dias_solicitados){
  var tipo_prestamo=$("#tipo_prestamo").val();

  if(tipo_prestamo=="prestamo_indefinido"){
    $("#dias_prestamo").val(30);
    $("#dias_prestamo").prop('disabled',true)
    validar_input('dias_prestamo');
  }else{
    $("#dias_prestamo").val(dias_solicitados);
    $("#dias_prestamo").prop('disabled',false);
    $("#dias_prestamo").focus();
  }

  var dias_prestamo=$("#dias_prestamo").val();  
}

function validar_confirma_recibido(){ // Funcion para validar si el usuario ingresa la contraseña de su cuenta para confirmar que ha recibido en fisico el documento.
    var contr_confirma_recibido   = $("#contr_confirma_recibido").val();
    var solicitante               = $("#nombre_solicitante").val();

    if(contr_confirma_recibido.length<1){
        $("#error_contr_confirma_recibido").slideUp("slow");
    }else{
        $.ajax({
            type: 'POST',
            url: 'admin_prestamos/buscador_prestamos.php',
            data: {
                'contr'         : contr_confirma_recibido,
                'solicitante'   : solicitante,
                'tipo_consulta' : 'verificar_usuario'
            },          
            success: function(resp1){
                if(resp1=="false"){
                    $("#error_contr_confirma_recibido").slideDown("slow");
                    $("#contr_confirma_recibido").focus();
                    return false;
                }else{
                    $("#error_contr_confirma_recibido").slideUp("slow");
                    $("#contenedor_boton_enviar_prestamo_documento").slideDown("slow");
                    return true;
                }
            }
        })
        // return false;
    }
}

function validar_mostrar_boton_prestar_documento(valor){
    if(valor.length>0){
        $("#contenedor_boton_enviar_prestamo_documento").slideUp("slow");
    }else{
        $("#contenedor_boton_enviar_prestamo_documento").slideDown("slow");
    }
    $("#error_contr_confirma_recibido").slideUp("slow");
}