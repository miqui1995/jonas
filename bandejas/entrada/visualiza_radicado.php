<?php 
  if(!isset($_SESSION)){
        session_start();
    }
?>
<!DOCTYPE html>
<html>
<head><title>Visualizacion Datos Radicado</title></head>
<body>
<script>
$('#boton_expedientes').css('margin-top', '0px');
/* Script para ventana modal - Tecla Esc */
    window.addEventListener("keydown", function(event){
        var codigo = event.keyCode || event.which;
        if (codigo == 27){
            cerrar_ventana("ventana_solicitar_prestamo");
        }
        if(codigo== 8){ // Opcion para restringir que la tecla backspace da atras en el navegador.
            if (history.forward(1)) {
                location.replace(history.forward(1));
            }   
        }
    }, false);
/* Fin script para ventana modal - Tecla Esc */
cargar_accordion(); // Cargar funcion del menu desplegable "Accordion"

/*******************************************************************************************
* @class Funcion para validar campos y asignar_trd_expediente 
* @description Se definen las variables de radicado, serie ,subserie e id_expediente, se validan para enviarlo a procesar_ajax.php con el parametro "insertar_trd_radicado" y los valores correspondientes para insertar TRD
* @param {}() Esta funcion no recibe parámetros.
* @return {} No devuelve ningún valor. Es una funcion de asignar valores y focus.
*******************************************************************************************/   
function asignar_trd_expediente(){
    var radicado    	   = $("#radicado").val();
    var serie       	   = $("#codigo_serie_trd_exp").val();
    var subserie    	   = $("#codigo_subserie_trd_exp").val();
    var id_expediente      = $("#id_expediente_trd_exp").val();
    var nombre_expediente  = $("#seleccionar_expediente_trd_exp").val();

    if(serie==""){
        $("#error_codigo_serie_trd_exp").slideDown("slow");
        $("#codigo_serie_trd_exp").focus();
        return false;
    }else{
        $("#error_codigo_serie_trd_exp").slideUp("slow");
        
        if(subserie=="subserie"){
            $("#error2_codigo_subserie_trd_exp").slideDown("slow");
        	$("#codigo_subserie_trd_exp").focus();
            return false;
        }else{
            $("#error2_codigo_subserie_trd_exp").slideUp("slow");
	        if(id_expediente=="" || $(".art").is(":visible")){
	            $("#seleccionar_expediente_trd_exp_invalido").slideDown("slow");
        		$("#seleccionar_expediente_trd_exp").focus();
	            return false;
	        }else{
	            $("#seleccionar_expediente_trd_exp_invalido").slideUp("");
	            
	            if($(".errores").is(":visible")){
	                return false;
	            }else{
	                loading("boton_asignar_trd_exp");
	                
	                $.ajax({
	                    type: 'POST',
	                    url: 'include/procesar_ajax.php',
	                    data: {
	                        'recibe_ajax'       : 'insertar_trd_exp_radicado',
                            'id_expediente'     : id_expediente,
                            'nombre_expediente' : nombre_expediente,
                            'radicado'          : radicado,
                            'serie'             : serie,
                            'subserie'          : subserie
	                    },          
	                    success: function(resp1){
	                        if(resp1!=""){
	                            $("#resultado_js").html(resp1);
	                        }
	                    }
	                })
	            }
	        }
        }
    }
}

/*******************************************************************************************
* @class Funcion para cargar listado de series en el formulario de "asignar_trd"
* @description Se recibe parámetro (dependencia) el cual se carga en la función respectiva. Muestra #ventana_serie_subserie_radicado que es la ventana modal para asignar_trd, asigna las series correspondientes a la dependencia y enfoca #codigo_serie 
* @param {string}(dependencia) Codigo de la dependencia del expediente para asignar a funcion respectiva.
* @return {} No devuelve ningún valor. Es una funcion de asignar valores y focus.
*******************************************************************************************/
function asignar_trd(dependencia){
    $("#ventana_serie_subserie_radicado").slideDown("slow");
    $("#contenido").css({'z-index':'100'}); // Modifico estilo para sobreponer ventana modal 

    consulta_listado_series2("",dependencia,"codigo_serie");
    $("#codigo_serie").focus();
}

/*******************************************************************************************
* @class Funcion para cargar listado de series en el formulario de "asignar_trd_expediente"
* @description Se reciben parámetros (codigo_dependencia, id_expediente, codigo_serie, codigo_subserie2)del cual "codigo_serie" define el comportamiento ya que si está vacío define una opción u otra
* @param {string}(codigo_dependencia) Codigo de la dependencia del expediente para asignar a funcion respectiva.
* @param {string}(id_expediente) Id del expediente que define el comportamiento ya que si está vacío define una opción u otra.
* @param {string}(codigo_serie) Id de la serie que define el comportamiento ya que si está vacío define una opción u otra
* @param {string}(codigo_subserie2) Id de la subserie del expediente para asigner a funcion respectiva.
* @return {} No devuelve ningún valor. Es una funcion de asignar valores y focus.
*******************************************************************************************/
function asignar_trd_exp(codigo_dependencia, id_expediente, codigo_serie, codigo_subserie2){
    $("#ventana_trd_expediente").slideDown("slow");
    $("#contenido").css({'z-index':'100'}); // Modifico estilo para sobreponer ventana modal 

    if(codigo_serie==""){
        consulta_listado_series2("",codigo_dependencia,"codigo_serie_trd_exp");
        $("#codigo_serie_trd_exp").focus();
    }else{
        consulta_listado_series2(codigo_serie,codigo_dependencia,"codigo_serie_trd_exp");
        cargar_codigo_subserie2(codigo_serie, codigo_subserie2, codigo_dependencia, 'asignar_trd_exp', 'codigo_subserie_trd_exp')
        if(id_expediente==""){
	        setTimeout(function() {          
    			validar_input_formulario_serie_subserie_nrr(codigo_dependencia,'resultado_seleccionar_expediente_trd_exp','codigo_serie_trd_exp','')
	        },1000);
		}
    }
}

/*******************************************************************************************
* @class Funcion para cargar los input correspondientes según los parámetros recibidos en formulario "Documento no requiere respuesta" y en formulario "asignar TRD y EXPEDIENTE" 
* @description Se reciben parámetros (id_expediente,asunto,tipo_formulario) del cual "tipo_formulario" define el comportamiento del switch ya que indica cual formulario se está cargando.
* @param {string}(id_expediente) Id del expediente para asignar a input respectivo.
* @param {string}(asunto) Asunto del expediente para asignar a input respectivo.
* @param {string}(tipo_formulario) Define el comportamiento del switch ya que indica cual formulario se está cargando.
* @return {} No devuelve ningún valor. Es una funcion de asignar valores y focus.
*******************************************************************************************/
function cargar_input_expediente_nrr(id_expediente,asunto,tipo_formulario){
	switch(tipo_formulario){
		case 'nrr':
		    $("#id_expediente_nrr").val(id_expediente);
		    $("#seleccionar_expediente").val(asunto);
		    $("#seleccionar_expediente_invalido").slideUp("slow");
		    $("#listado_carpetas_personales_nrr").focus();
			break;

		case 'trd_exp':
		    $("#id_expediente_trd_exp").val(id_expediente);
		    $("#seleccionar_expediente_trd_exp").val(asunto);
		    $("#seleccionar_expediente_trd_exp_invalido").val(asunto);
		    $("#asignar_trd_exp").focus();
			break;	
	}
   
    $(".art").slideUp("slow");
    $(".errores").slideUp("slow");
}

function cerrar_ventana(nombre_ventana){
    $("#ventana_solicitar_prestamo").slideUp("slow");
}

/*******************************************************************************************
* @class Funcion para cargar validar campos e insertar_trd_radicado 
* @description Se definen las variables de radicado, serie y subserie, se validan para enviarlo a procesar_ajax.php con el parametro "insertar_trd_radicado" y los valores correspondientes para insertar TRD
* @param {}() Esta funcion no recibe parámetros.
* @return {} No devuelve ningún valor. Es una funcion de asignar valores y focus.
*******************************************************************************************/   
function insertar_trd_radicado(){
    var radicado    = $("#radicado").val();
    var serie       = $("#codigo_serie").val();
    var subserie    = $("#codigo_subserie").val();

    if(serie==""){
        $("#error_codigo_serie").slideDown("slow");
        return false;
    }else{
        $("#error_codigo_serie").slideUp("slow");
        
        if(subserie=="subserie"){
            $("#error2_codigo_subserie").slideDown("slow");
            return false;
        }else{
            $("#error2_codigo_subserie").slideUp("slow");

            if($(".errores").is(":visible")){
                return false;
            }else{
                loading("boton_trd_radicado");
                
                $.ajax({
                    type: 'POST',
                    url: 'include/procesar_ajax.php',
                    data: {
                        'recibe_ajax'   : 'insertar_trd_radicado',
                        'radicado'      : radicado,
                        'serie'         : serie,
                        'subserie'      : subserie
                    },          
                    success: function(resp1){
                        if(resp1!=""){
                            $("#resultado_js").html(resp1);
                        }
                    }
                })
            }
        }
    }
}

/*******************************************************************************************
* @class Funcion para limpiar las opciones de expediente en formulario "Documento no requiere respuesta" y en formulario "asignar TRD y EXPEDIENTE" 
* @description Se invoca al cambiar el codigo de serie en el formulario "Documento no requiere respuesta" y/o "asignar TRD y EXPEDIENTE" poniendo el valor de #seleccionar_expediente/#seleccionar_expediente_trd_exp en vacío y oculta las opciones de los resultados de expedientes de la dependencia-serie.
* @param {string} Para definir el case en el switch.
* @return {} No devuelve ningún valor. Es una funcion de asignar valores y focus.
**************************************************************/
function limpiar_opciones_expediente(formulario){
    switch(formulario){
        case "nrr":
            $("#id_expediente_nrr").val("");
            $("#seleccionar_expediente").val("");
            $("#resultado_seleccionar_expediente").html("");
            $("#codigo_subserie_nrr").focus();

        case "trd_exp":
            $("#id_expediente_trd_exp").val("");
            $("#seleccionar_expediente_trd_exp").val("");
            $("#resultado_seleccionar_expediente_trd_exp").html("");
            $("#codigo_subserie_trd_exp").focus();
            break;
    }
}

/* Inicio carga menu_prestamo */
function menu_prestamo(){
    var radicado            = $("#radicado").val();
    var expediente          = $("#id_expediente").val();
    var detalle_asunto      = "<b>"+radicado+"</b><br> "+$("#detalle_asunto").val();
    var asunto_expediente   = $("#asunto_expediente1").val();
    
    $("#documento_solicitado").html(detalle_asunto);
    $("#documento_numero").val(radicado);
    $("#expediente_numero").val(expediente);
    $("#asunto_expediente").val(asunto_expediente);

   $("#ventana_solicitar_prestamo").slideDown("slow");
   $("#contenido").css({'z-index':'100'});   // Modifico estilo para sobreponer a ventana modal
}

/* Inicio funciones para prestamo de documentos / expedientes */

/* Inicio valida tipo de solicitud */
function valida_tipo_solicitud(){
    var tipo_solicitud      = $("#tipo_solicitud").val();
    var radicado            = $("#radicado").val();
    var expediente          = $("#id_expediente").val();
    var asunto_expediente   = $("#asunto_expediente1").val();
    var detalle_asunto      = $("#detalle_asunto").val();

    if(tipo_solicitud=='documento_individual'){
        var detalle_asunto1="<b>"+radicado+"</b><br> "+detalle_asunto;
    }else{
        var detalle_asunto1="<b>"+expediente+"</b><br> "+asunto_expediente;
    }
    $("#documento_solicitado").html(detalle_asunto1);
}

/* Inicio envío de solicitud de préstamo */
function valida_envio_prestamo(){
    validar_input('dias_prestamo');
    validar_input('observaciones_solicitud_prestamo');

    if($(".errores").is(":visible")){
        return false;
    }else{
        var transaccion=$("#transaccion_solicitud_prestamo").val()
        var radicado=$("#documento_numero").val()
       
        var tipo_solicitud=$("#tipo_solicitud").val();
        if(tipo_solicitud=='documento_individual'){
            var numero_documento=$("#documento_numero").val();
        }else{
            var numero_documento=$("#expediente_numero").val();
        }

        var termino=$("#dias_prestamo").val();
        var observaciones_prestamo=$("#observaciones_solicitud_prestamo").val();
        
        $("#fila_boton_solicitar_documento").html("<center><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br><h2>Cargando...</h2></center>");

        $.ajax({    // Guardo registro de ingreso al sistema para auditoria
            type: 'POST',
            url: 'bandejas/entrada/transacciones_radicado.php',
            data: {
                'radicado' : radicado,
                'transaccion' : transaccion,
                'tipo_solicitud' : tipo_solicitud,
                'numero_documento': numero_documento,
                'termino': termino,
                'observaciones_prestamo': observaciones_prestamo
            },          
            success: function(resp1){
                
                if(resp1!=""){
                    $("#resultados_solicitud").html(resp1);
                }else{
                    alert(resp1)
                }
                console.log(resp1)
            }
        })
    }
}
/* Fin envío de solicitud de préstamo */
/* Fin funciones para prestamo de documentos / expedientes */

function validar_carpeta_personal_nrr(valor){
    if(valor==""){
        $("#error_carpetas_personales_nrr2").slideDown("slow");
    }else{
        $("#error_carpetas_personales_nrr2").slideUp("slow");
        $("#observaciones_aprobar_documento").focus();
    }
}

function validar_carpeta_personal_principal(valor){
    if(valor==""){
        $("#error_carpetas_personales_pdf_principal2").slideDown("slow");
    }else{
        $("#error_carpetas_personales_pdf_principal2").slideUp("slow");
    }
}

function validar_input_formulario_serie_subserie(){
    var subserie    = $("#codigo_subserie").val();

    if(subserie=="subserie"){
        $("#error2_codigo_subserie").slideDown("slow");
    }else{
        $("#error2_codigo_subserie").slideUp("slow");
    }
    $("#codigo_subserie").focus();
}

function validar_input_formulario_pdf_firmado(dependencia){
    var subserie    = $("#codigo_subserie_pdf_firmado").val();

    if(subserie=="subserie"){
        $("#error2_codigo_serie_pdf_firmado").slideDown("slow");
    }else{
        $("#error2_codigo_serie_pdf_firmado").slideUp("slow");
    }

    validar_input_formulario_serie_subserie_nrr(dependencia,'resultado_seleccionar_expediente_pdf_firmado','codigo_serie_pdf_firmado')
}
/************************************************************************************
* @class funcion para cargar listado de expedientes por dependencia y serie 
* @description Recibe los parámetros y valida primero el valor de la serie del parametro "input_serie", 
* Envía mediante AJAX con el los parámetros "seleccionar_expediente_dependencia" el codigo de dependencia, serie y del valor buscado y pone en el div del parámetro "div_resultado" un listado de <div> con los expedientes que existen en la base de datos con la relacion dependencia-serie-subserie según los parámetros enviados.* 
* Luego hace un switch del "input_serie" para definir el comportamiento de la función.
* Consulta mediante ajax y finalmente hace otro switch del "input_serie" para definir el comportamiento final de la función. 
* @param {string} (codigo_dependencia) Es obligatorio y se usa para filtrar la consulta.
* @param {string} (div_resultado) Es obligatorio y se usa para definir el id del div donde se muestra el resultado de la consulta.
* @param {string} (input_serie) Es obligatorio y se usa para mostrar el resultado de la consulta y el comportamiento en el switch
* @param {string} (valor_buscado) Es opcional y se usa para filtrar el resultado de la consulta (para buscar este valor en el id_expediente y/o en el nombre_expedinte) y el comportamiento en el switch.
* @return {string} String con los <div> del listado de expedientes que existen en la base de datos con la relacion dependencia-serie según los parametros enviados.
**************************************************************************************/
function validar_input_formulario_serie_subserie_nrr(codigo_dependencia,div_resultado,input_serie,valor_buscado){
    var serie       = $("#"+input_serie).val();

    switch(input_serie){
    	case 'codigo_serie_nrr':
		   var tipo_formulario = "nrr";
    		break;

    	case 'codigo_serie_trd_exp':
		   var tipo_formulario = "trd_exp";		
    		break;
    }

    $.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax'       : 'seleccionar_expediente',
            'dependencia'       : codigo_dependencia,
            'serie'             : serie,
            'search_expediente' : valor_buscado,
            'tipo_formulario' 	: tipo_formulario
        },          
        success: function(respuesta){
            $("#"+div_resultado).html(respuesta)              
        }
    })

    switch(input_serie){
    	case 'codigo_serie_nrr':
		    $("#id_expediente_nrr").val("");
		    $("#seleccionar_expediente").focus();

		    if(valor_buscado==""){$("#seleccionar_expediente").val("");}
    		break;

    	case 'codigo_serie_trd_exp':
		    $("#id_expediente_trd_exp").val("");
		    $("#seleccionar_expediente_trd_exp").focus();

		    if(valor_buscado==""){$("#seleccionar_expediente_trd_exp").val("");}
    		break;
    }
}

/*******************************************************************************************
* @class Funcion para cargar validar campos y marcar documento como NRR (No Requiere Respuesta)
* @description Se definen las variables de expediente, listado_carpetas_personales, observaciones, radicado, serie y subserie, luego se validan para enviarlo a procesar_ajax.php con el parametro "documento_no_requiere_respuesta" y los valores correspondientes para insertar TRD
* @param {} Esta funcion no recibe parámetros.
* @return {} No devuelve ningún valor. Es una funcion de asignar valores y focus.
*******************************************************************************************/  
function valida_envio_nrr(){
    var expediente                      = $("#id_expediente_nrr").val();
    var listado_carpetas_personales     = $("#listado_carpetas_personales_nrr").val();
    var observaciones                   = $("#observaciones_aprobar_documento").val();
    var radicado                        = $("#radicado").val();
    var serie                           = $("#codigo_serie_nrr").val();
    var subserie                        = $("#codigo_subserie_nrr").val();

    if(serie==""){
        $("#error_codigo_serie_nrr").slideDown("slow");
        $("#codigo_serie_nrr").focus();
        return false;
    }else{
        if(subserie=="subserie"){
            $("#error2_codigo_subserie_nrr").slideDown("slow");
            $("#codigo_subserie_nrr").focus();
            return false;
        }else{
            if(expediente==""){
            	$("#seleccionar_expediente").focus();
                $("#seleccionar_expediente_invalido").slideDown("slow");
                return false;
            }else{
                if($(".art").is(":visible")){
	            	$("#seleccionar_expediente").focus();
                    $("#seleccionar_expediente_invalido").slideDown("slow");
                    return false;
                }else{
                    if(listado_carpetas_personales==""){
	            		$("#listado_carpetas_personales_nrr").focus();
                        $("#error_carpetas_personales_nrr2").slideDown("slow");                    
                    }else{
                        validar_input('observaciones_aprobar_documento');
                        if($(".errores").is(":visible")){
                            return false;
                        }else{
                        	loading('contenedor_boton_nrr')
                            $.ajax({
                                type: 'POST',
                                url: 'include/procesar_ajax.php',
                                data: {
                                    'recibe_ajax'       : 'documento_no_requiere_respuesta',
                                    'carpeta_personal'  : listado_carpetas_personales,
                                    'expediente'        : expediente,
                                    'observaciones'     : observaciones,
                                    'radicado'          : radicado,
                                    'serie'             : serie,
                                    'subserie'          : subserie
                                },          
                                success: function(respuesta){
                                    $("#resultado_js").html(respuesta)                  
                                }
                            })
                        }
                    }
                }
            }
        }       
    }
}
/* Fin funciones para formulario "Documento no requiere respuesta" */

/* Funcion para activar cuando se digita tecla en el input correspondiente */
var timerid="";

$("#asunto_radicado_adjunto_doc").on("input",function(e){ // Accion que se activa cuando se digita #asunto_radicado_adjunto
    var asunto_radicado_adjunto = $(this).val();
    $(".errores").slideUp("slow");
        
    if($(this).data("lastval")!= asunto_radicado_adjunto){
        $(this).data("lastval",asunto_radicado_adjunto);             
        clearTimeout(timerid);
        timerid = setTimeout(function() {
            espacios_formulario('asunto_radicado_adjunto_doc','primera',0);
            validar_input('asunto_radicado_adjunto_doc');
        },1000);
    };
});

$("#asunto_radicado_adjunto_exp").on("input",function(e){ // Accion que se activa cuando se digita #asunto_radicado_adjunto
    espacios_formulario('asunto_radicado_adjunto_exp','primera',0);
    $(".errores").slideUp("slow");
    var asunto_radicado_adjunto = $(this).val();
        
    if($(this).data("lastval")!= asunto_radicado_adjunto){
        $(this).data("lastval",asunto_radicado_adjunto);             
        clearTimeout(timerid);
        timerid = setTimeout(function() {
            validar_input('asunto_radicado_adjunto_exp');
        },1000);
    };
});

$("#observaciones_aprobar_documento").on("input",function(e){ // Accion que se activa cuando se digita #observaciones_aprobar_documento
    espacios_formulario('observaciones_aprobar_documento','primera',0);
    $(".errores").slideUp("slow");
    var asunto_radicado_adjunto = $(this).val();
        
    if($(this).data("lastval")!= asunto_radicado_adjunto){
        $(this).data("lastval",asunto_radicado_adjunto);             
        clearTimeout(timerid);
        timerid = setTimeout(function() {
            validar_input('observaciones_aprobar_documento');
        },1000);
    };
});


$("#observaciones_asociar_pdf_firmado").on("input",function(e){ // Accion que se activa cuando se digita #observaciones_asociar_pdf_firmado
    espacios_formulario('observaciones_asociar_pdf_firmado','primera',0);
    $(".errores").slideUp("slow");
    var asunto_pdf_firmado = $(this).val();
        
    if($(this).data("lastval")!= asunto_pdf_firmado){
        $(this).data("lastval",asunto_pdf_firmado);             
        clearTimeout(timerid);
        timerid = setTimeout(function() {
            validar_input('observaciones_asociar_pdf_firmado');
        },1000);
    };
});

$("#seleccionar_expediente").on("input",function(e){ // Accion que se activa cuando se digita #seleccionar_expediente
    $(".errores").slideUp("slow");
    loading('resultado_seleccionar_expediente');
    var seleccionar_expediente 	= $(this).val();
    var codigo_dependencia 		= $('#codigo_dependencia').val();
        
    if($(this).data("lastval")!= seleccionar_expediente){
        $(this).data("lastval",seleccionar_expediente);             
        clearTimeout(timerid);
        timerid = setTimeout(function() {
            validar_input('seleccionar_expediente');
            validar_input_formulario_serie_subserie_nrr(codigo_dependencia,'resultado_seleccionar_expediente','codigo_serie_nrr',seleccionar_expediente)
        },1000);
    };
});

$("#seleccionar_expediente_trd_exp").on("input",function(e){ // Accion que se activa cuando se digita #seleccionar_expediente_trd_exp
    $(".errores").slideUp("slow");
    loading('resultado_seleccionar_expediente_trd_exp');
    var seleccionar_expediente 	= $(this).val();
    var codigo_dependencia 		= $('#codigo_dependencia').val();
        
    if($(this).data("lastval")!= seleccionar_expediente){
        $(this).data("lastval",seleccionar_expediente);             
        clearTimeout(timerid);
        timerid = setTimeout(function() {
        	$("#resultado_seleccionar_expediente_trd_exp").slideDown("slow");
            validar_input('seleccionar_expediente_trd_exp');
            validar_input_formulario_serie_subserie_nrr(codigo_dependencia,'resultado_seleccionar_expediente_trd_exp','codigo_serie_trd_exp',seleccionar_expediente)
        },1000);
    };
});

/* Fin funcion para activar cuando se digita tecla en el input correspondiente */
/* Funcion para mostrar la informacion general */
$(function carga_informacion_general(){
    $('#boton_informacion_general').click(function carga_informacion_general(){
        var radicado=$("#radicado").val();  

        $.ajax({
            type: 'POST',
            url: 'bandejas/entrada/pestanas.php',
            data: {
                'pestana' : 'informacion_general',
                'radicado' : radicado  
            },          
            success: function(resp1){
                if(resp1!=""){
                    $("#informacion_general").html(resp1);
                }
            }
        })   
                                   
    });

    $('#boton_historico').click(function carga_historico(){
        var radicado    = $("#radicado").val();  
        var expediente  = $("#expediente").val();  

        $.ajax({
            type: 'POST',
            url: 'bandejas/entrada/pestanas.php',
            data: {
                'pestana' : 'historico',
                'radicado' : radicado,
                'expediente': expediente  
            },          
            success: function(resp1){
                if(resp1!=""){
                    $("#historico").html(resp1);
                }
            }
        })  
    }); 

    $('#documentos').click(function carga_historico(){
        var radicado    = $("#radicado").val();  
        var expediente  = $("#expediente").val();  

        $.ajax({
            type: 'POST',
            url: 'bandejas/entrada/pestanas.php',
            data: {
                'pestana' : 'documentos_anexos',
                'radicado' : radicado,
                'expediente': expediente  
            },          
            success: function(resp1){
                if(resp1!=""){
                    $("#documentos_anexos").html(resp1);
                }
            }
        })  
    }); 

    $('#boton_expedientes').click(function carga_historico(){
        var radicado    = $("#radicado").val();  
        var expediente  = $("#expediente").val();  

        $.ajax({
            type: 'POST',
            url: 'bandejas/entrada/pestanas.php',
            data: {
                'pestana'   : 'expedientes',
                'radicado'  : radicado,
                'expediente': expediente  
            },          
            success: function(resp1){
                if(resp1!=""){
                    $("#expedientes").html(resp1);
                }
            }
        })  
    });     
}) 

</script>
<?php
	require_once('../../login/conexion2.php');
    $login          = $_SESSION['login'];
    $dependencia    = $_SESSION['dependencia'];
    $radicado       = $_POST['radicado'];

    /* Defino el codigo de la dependencia en input */
    echo "<input type='hidden' id='codigo_dependencia' placeholder='codigo_dependencia' value='$dependencia'>";

    // Lo primero es marcar el radicado como leido  
    $query_consulta_leido="select leido, asunto, codigo_serie from radicado where numero_radicado='$radicado'";
    
    $fila_consulta_usuarios_leido   = pg_query($conectado,$query_consulta_leido);
    $linea_consulta_usuarios_leido  = pg_fetch_array($fila_consulta_usuarios_leido);
    $consulta_usuarios_leido        = $linea_consulta_usuarios_leido['leido'];
    $asunto_radicado                = $linea_consulta_usuarios_leido['asunto']; // Variable para prestamos
    $serie_radicado                 = $linea_consulta_usuarios_leido['codigo_serie']; // Variable para prestamos
    $serie_radicado                 = $linea_consulta_usuarios_leido['codigo_serie']; // Variable para prestamos

    if (strpos($consulta_usuarios_leido, $login) !== false) {
        $usuarios_leido_sin_login  = str_replace("$login,", "", $consulta_usuarios_leido); // Se elimina de la cadena de usuarios leido el login con una coma para mantener el formato del campo "leido".
       
        $query_leido = "update radicado set leido='$usuarios_leido_sin_login' where numero_radicado='$radicado'";
      
        if(pg_query($conectado,$query_leido)){ 
            echo "<script>auditoria('radicado_leido','$radicado')</script>";
        }else{
            echo "No se pudo actualizar la informacion del radicado. Comuniquese con el administrador del sistema.";
        }   
    }
    // Hasta aqui es marcar el radicado como leido  
    
	$lista_nombre_expedientes 		= ""; // Inicio variable para lista lista_nombre_expedientes

    /* Validar si el usuario tiene el radicado para poder reasignarlo */
    $query_usuario_actual   = "select usuarios_control,id_expediente from radicado where numero_radicado='$radicado';";
    $fila_usuario_actual    = pg_query($conectado,$query_usuario_actual);
    $linea_usuario_actual   = pg_fetch_array($fila_usuario_actual);
    $usuarios_control       = $linea_usuario_actual['usuarios_control'];
    $id_expediente          = $linea_usuario_actual['id_expediente']; // Valida si el radicado tiene expediente

    /* Indicador de numero de expediente de radicado actual */
    if($id_expediente==''){
        $indicador_boton_expediente = "Expediente  <font color='red'> (No se ha asignado expediente al radicado todavía.)</font>";
    }else{
    	// Extraigo cada uno de los expedientes	
		$exp  = explode(",", $id_expediente);
		$max  = sizeof($exp);
		$max2 = $max-1;

		$lista_id_nombre_expedientes 	= ""; // Inicio variable para lista_id_nombre_expedientes
		
		// if($max2==0){
		// 	$num_exp = $exp[0];
		// 	$consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
		// 	$fila_exp 	  = pg_query($conectado,$consulta_exp);
		// 	$linea_exp    = pg_fetch_array($fila_exp);
		// 	$nombre_exp   = $linea_exp['nombre_expediente'];
		// }else{
			for ($j=0; $j < $max2; $j++) { 
				$num_exp = $exp[$j];
				$consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
				$fila_exp 	  = pg_query($conectado,$consulta_exp);
				$linea_exp    = pg_fetch_array($fila_exp);
				$nombre_exp   = $linea_exp['nombre_expediente'];

				$lista_nombre_expedientes.="$nombre_exp, ";
				$lista_id_nombre_expedientes.="$num_exp($nombre_exp), ";
			}		
		// }			

        $indicador_boton_expediente = "Expediente <font color='blue'> [Radicado asignado en expediente $lista_id_nombre_expedientes]</font>";
    }
    /* Se consulta si el documento es una respuesta o si tiene una para mostrarla */
    $consulta_tabla_respuesta_radicados = "select * from respuesta_radicados where radicado_padre='$radicado' or radicado_respuesta='$radicado'";
    $fila_consulta_tabla_respuesta_radicados        = pg_query($conectado,$consulta_tabla_respuesta_radicados);
    $num_rows_consulta_tabla_respuesta_radicados    = pg_num_rows($fila_consulta_tabla_respuesta_radicados);

    if($num_rows_consulta_tabla_respuesta_radicados=='0'){
         $indicador_boton_documentos = "Documentos Anexos / Adjuntos /Respuesta"; }else{
        $linea_resp_radicado = pg_fetch_array($fila_consulta_tabla_respuesta_radicados);

        $radicado_padre1        = $linea_resp_radicado['radicado_padre'];
        $radicado_respuesta1    = $linea_resp_radicado['radicado_respuesta'];

        if ($radicado == $radicado_padre1){
            $indicador_boton_documentos = "Documentos Anexos / Adjuntos /Respuesta <font color='blue'>( Este radicado ya tiene respuesta[$radicado_respuesta1])</font>";
        }else{
            $indicador_boton_documentos = "Documentos Anexos / Adjuntos /Respuesta <font color='blue'>( Este radicado es la respuesta al documento [$radicado_padre1])</font>";
        }
    }   

    if (strpos($usuarios_control, $login) !== false) {
    /* Muestra si se encuentra entre los $usuarios_control las opciones de derivar, informar y reasignar */
        echo "<script>
            $('#derivar_radicado').removeClass('img1');       
            $('#derivar_radicado').addClass('img');   
            $('#informar_radicado').removeClass('img1');       
            $('#informar_radicado').addClass('img');   
            $('#reasignar_radicado').removeClass('img1');       
            $('#reasignar_radicado').addClass('img');   
            $('#responder_radicado').removeClass('img1');       
            $('#responder_radicado').addClass('img');   
        </script>";
    }else{
    /* Oculta si no se encuentra entre los $usuarios_control las opciones de derivar, informar y reasignar */
        echo "<script>
            $('#derivar_radicado').removeClass('img');       
            $('#derivar_radicado').addClass('img1');   
            $('#informar_radicado').removeClass('img');       
            $('#informar_radicado').addClass('img1');   
            $('#reasignar_radicado').removeClass('img');       
            $('#reasignar_radicado').addClass('img1');   
            $('#responder_radicado').removeClass('img');       
            $('#responder_radicado').addClass('img1');
        </script>";
    }

    /* Fin validar si el usuario tiene el radicado para poder reasignarlo */
    /* Inicio carpetas_personales para formulario NRR */
    $consulta_cantidad_carpetas_personales="select * from carpetas_personales c join usuarios u on c.id_usuario::varchar = u.id_usuario::varchar where u.login='$login' order by nombre_carpeta_personal";

    $fila_cantidad_carpetas_personales = pg_query($conectado,$consulta_cantidad_carpetas_personales);
/*Calcula el numero de registros que genera la consulta anterior.*/
    $registros_carpetas_personales= pg_num_rows($fila_cantidad_carpetas_personales);
/*Recorre el array generado e imprime uno a uno los resultados.*/   
    $carpeta_personal_nrr = "";
    if($registros_carpetas_personales>0){   
        $query_carpeta_radicado_actual="select codigo_carpeta1 ->'$login' ->> 'codigo_carpeta_personal' as codigo_carpeta from radicado where numero_radicado='$radicado'";
        $fila_carpeta_radicado_actual   = pg_query($conectado,$query_carpeta_radicado_actual);
        $linea_carpeta_radicado_actual  = pg_fetch_array($fila_carpeta_radicado_actual);
        $codigo_carpeta_radicado_actual = $linea_carpeta_radicado_actual['codigo_carpeta'];

        // $carpeta_personal_nrr.="";
            for ($i=0;$i<$registros_carpetas_personales;$i++){
                $linea_carpetas_personales = pg_fetch_array($fila_cantidad_carpetas_personales);

                $id_carpeta_personal     = $linea_carpetas_personales['id'];
                $nombre_carpeta_personal = $linea_carpetas_personales['nombre_carpeta_personal'];
                
                if($id_carpeta_personal==$codigo_carpeta_radicado_actual){
                    $carpeta_personal_nrr.= "<option value='$id_carpeta_personal' title='Archivar radicado en carpeta $nombre_carpeta_personal' selected='selected'>
                        $nombre_carpeta_personal 
                    </option>";
                }else{
                    $carpeta_personal_nrr.= "<option value='$id_carpeta_personal' title='Archivar radicado en carpeta $nombre_carpeta_personal'>
                        $nombre_carpeta_personal 
                    </option>";
                }   
            }
        // $carpeta_personal_nrr.="</select>";   
    }else{
        echo "<script>$('#error_carpetas_personales_nrr').slideDown('slow'); $('#error_carpetas_personales_pdf_principal').slideDown('slow')</script>";
    }
    /* Fin carpetas_personales para formulario NRR */
    echo "<script>$('#radicado').val('$radicado'); $('#expediente').val('$id_expediente'); $('#lista_nombre_expedientes').val('$lista_nombre_expedientes'); $('#archivar_radicado').removeClass('img1'); $('#archivar_radicado').addClass('img');  
        $('#cuerpo_titulo_barra_herramientas').removeClass('titulo_bh');$('#cuerpo_titulo_barra_herramientas').addClass('titulo_bh1');  

    </script>";
    echo "<h2>Radicado $radicado | El responsable del trámite de éste documento es <font color='blue'> $usuarios_control</font></h2>
    <input type='hidden' name='login' id='login' value= '$login') >";
 ?>

<!-- Inicio botones acordeon -->
<button id="boton_informacion_general" class="accordion" onclick="cargar_accordion();">Informacion General del radicado <?php echo "$radicado"; ?></button>
<div id="informacion_general" class="panel"></div>

<button id="boton_historico" class="accordion" onclick="cargar_accordion();">Historico</button>
<div id="historico" class="panel"></div>

<button id="documentos" class="accordion" onclick="cargar_accordion();"><?php echo "$indicador_boton_documentos" ?></button>

<div id="documentos_anexos" class="panel"></div>

<button id="boton_expedientes" class="accordion boton_expedientes" onclick="cargar_accordion();"><?php echo "$indicador_boton_expediente"; ?></button>
<div id="expedientes" class="panel"></div>
  
<!-- Fin botones acordeon -->

<!-- Div que contiene ventana modal para solicitar prestamo --> 
    <div id="ventana_solicitar_prestamo">
        <div class="form">
            <div class="cerrar"><a href='javascript:cerrar_ventana("ventana_solicitar_prestamo");'>Cerrar X</a></div>
            <h1>Formulario Solicitud de Préstamo</h1>
            <hr>
            <form method="post" id ="formulario_solicitar_prestamo" name ="formulario_solicitar_prestamo" >
                <table border ="0">
                    <tr>
                        <input type="hidden" name ="transaccion_solicitud_prestamo" id="transaccion_solicitud_prestamo" value="solicitud_prestamo_documento"><!-- Tipo de transaccion para transacciones_radicado.php -->
                        <input type="hidden" name ="documento_numero" id="documento_numero" placeholder="documento_numero"> 
                        <input type="hidden" name ="expediente_numero" id="expediente_numero" placeholder="expediente_agnumero"> 
                        <input type="hidden" name ="id_expediente" id="id_expediente" placeholder="id_expediente"> 
                        <input type="hidden" name ="asunto_expediente" id="asunto_expediente" placeholder="asunto_expediente"> 
                        <input type="hidden" name ="asunto_expediente1" id="asunto_expediente1" placeholder="asunto_expediente1"> 
                        <input type="hidden" name ="detalle_asunto" id="detalle_asunto" placeholder="detalle_asunto"> 

                        <td class="descripcion">Radicado solicitado :</td>
                        <td id="documento_solicitado" class="detalle"></td>
                    </tr>
                    <tr>
                        <td class="descripcion">Tipo de Solicitud </td>
                        <td class="detalle">
                            <select class="select_opciones" name="tipo_solicitud" id="tipo_solicitud" class='select_opciones' onchange="valida_tipo_solicitud()" title="Selecciona si va a solicitar el documento individual o el expediente completo">
                                <option value="documento_individual"  selected="selected">Documento Individual (Solo el radicado)</option>
                                <option value="expediente_completo">Expediente Completo (Carpeta completa)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="descripcion">Término del préstamo (En días calendario) :</td>
                        <td class="detalle">
                            <input type="search" title='Cantidad de días calendario que va a solicitar el préstamo' name ="dias_prestamo" id="dias_prestamo" value="8" onblur="validar_input('dias_prestamo')">
                            <div id="error_dias_prestamo"  class="errores">El término del préstamo debe ser un número.</div>
                            <div id="dias_prestamo_null" class="errores">El término del préstamo es obligatorio.</div>
                            <div id="dias_prestamo_max" class="errores">El término del préstamo no puede ser mayor a 30 dias.</div>
                        </td>   
                    </tr>
                    <tr>
                        <td class="descripcion">Observaciones :</td>
                        <td class="detalle" colspan="3">
                            <textarea name="observaciones_solicitud_prestamo" id="observaciones_solicitud_prestamo" rows="2" style="width:100%;padding:5px;" placeholder="Ingrese las observaciones. Sea lo más específico posible" title="Ingrese las observaciones. Sea lo más específico posible" onblur="validar_input('observaciones_solicitud_prestamo')" > </textarea>
                            <div id="error_observaciones_solicitud_prestamo" class="errores">El mensaje de observaciones es obligatorio</div>
                            <div id="observaciones_solicitud_prestamo_min" class="errores">El mensaje de observaciones no puede ser menor a 6 caracteres (numeros o letras) </div>
                            <div id="observaciones_solicitud_prestamo_max" class="errores">El mensaje de observaciones no puede ser mayor a 500 caracteres (numeros o letras)</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" id="fila_boton_solicitar_documento">
                            <center><input type="button" value="Solicitar Documento" id="solicitar_doc" class="botones" onclick="valida_envio_prestamo()"><center>
                        </td>
                        <div id="resultados_solicitud"></div>   
                    </tr>
                </table>
            </form>
        </div>
    </div>
<!-- Hasta aqui el div que contiene ventana modal para solicitar prestamo -->
<!-- Formulario subir PDF principal con firmas -->
<!-- <div id="ventana_subir_pdf_principal" class="ventana_modal form">
    <div class="form">
        <div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
        <h1>Formulario subir PDF principal con firmas</h1>
        <div id="nombre_radicado_principal"></div>
        <hr>
        <form autocomplete="off">
            <table>
                <tr>
                    <td class="descripcion" width="50%">Justificacion para el histórico (Trazabilidad) :</td>
                    <td class="detalle" colspan="3">
                        <input type="hidden" id="radicado_adjunto">
                        <textarea id="asunto_radicado_adjunto" type="search" placeholder="Ingrese la justificación para guardar en la trazabilidad" title="Ingrese la justificación para guardar en la trazabilidad" onblur="validar_input('asunto_radicado_adjunto')" style="width:100%;padding:5px;"></textarea>

                        <div id="asunto_radicado_adjunto_max" class="errores">La justificación para guardar en la trazabilidad no puede ser mayor a 500 caracteres (numeros o letras). (Actualmente <b><u id='asunto_radicado_adjunto_contadormax'></u></b> caracteres)</div>
                        <div id="asunto_radicado_adjunto_min" class="errores">La justificación para guardar en la trazabilidad no puede ser menor a 6 caracteres (numeros o letras) </div>
                        <div id="asunto_radicado_adjunto_null" class="errores">La justificación para guardar en la trazabilidad es obligatoria</div>
                    </td>
                </tr>
                <tr>
                    <td class="descripcion">Archivo PDF (Documento PDF con las firmas correspondientes)  :</td>
                    <td class="detalle" colspan="3">
                       <input type="file" name="archivo_pdf_radicado_principal" id="archivo_pdf_radicado_principal">

                        <div id="archivo_pdf_radicado_principal_error" class="errores"> El archivo a cargar es un campo obligatorio. El sistema solo admite formato PDF</div>
                        <div id="archivo_pdf_radicado_principal_invalido" class="errores"> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
                        <div id="archivo_pdf_radicado_principal_tamano" class="errores">El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="center">
                        <input type="button" value="Subir PDF Principal" class="botones" onclick="enviar_subir_pdf()">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div> -->
<!-- Hasta aqui formulario subir PDF principal con firmas -->

<!-- Formulario adjuntar archivos_doc -->
<div id="ventana_adjuntar_archivos_doc" class="ventana_modal form">
    <div class="form">
        <div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
        <h1>Formulario Adjuntar Archivos</h1>
        <div id="nombre_documento_doc"></div>
        <hr>
        <form autocomplete="off">
            <table>
                <tr>
                    <td class="descripcion" width="50%">Asunto :</td>
                    <td class="detalle" colspan="3">
                        <input type="hidden" id="radicado_adjunto_doc">
                        <textarea id="asunto_radicado_adjunto_doc" type="search" placeholder="Ingrese el asunto o descripción del archivo adjunto" title="Ingrese el asunto o descripción del archivo adjunto." onblur="validar_input('asunto_radicado_adjunto')" style="width:100%;padding:5px;"></textarea>
                        <div id="asunto_radicado_adjunto_doc_max" class="errores">El asunto o descripción del archivo adjunto no puede ser mayor a 500 caracteres (numeros o letras). (Actualmente <b><u id='asunto_radicado_adjunto_doc_contadormax'></u></b> caracteres)</div>
                        <div id="asunto_radicado_adjunto_doc_min" class="errores">El asunto o descripción del archivo adjunto no puede ser menor a 6 caracteres (numeros o letras) </div>
                        <div id="asunto_radicado_adjunto_doc_null" class="errores">El asunto o descripción del archivo adjunto es obligatorio</div>
                    </td>
                </tr>
                <tr>
                    <td class="descripcion">Archivo PDF (Documento que va a adjuntar)  :</td>
                    <td class="detalle" colspan="3">
                        <input type="file" name="archivo_pdf_radicado_doc" id="archivo_pdf_radicado_doc">

                        <div id="archivo_pdf_radicado_doc_error" class="errores"> El archivo a cargar es un campo obligatorio. El sistema solo admite formato PDF</div>
                        <div id="archivo_pdf_radicado_doc_invalido" class="errores"> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
                        <div id="archivo_pdf_radicado_doc_tamano" class="errores">El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="center">
                        <input type="button" value="Adjuntar Archivo" class="botones" onclick="enviar_adjuntar_archivo('doc')">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<!-- Hasta aqui formulario adjuntar archivos_doc -->

<!-- Formulario adjuntar archivos_exp -->
<div id="ventana_adjuntar_archivos_exp" class="ventana_modal form">
    <div class="form">
        <div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
        <h1>Formulario Adjuntar Archivos</h1>
        <div id="nombre_documento_exp"></div>
        <hr>
        <form autocomplete="off">
            <table>
                <tr>
                    <td class="descripcion" width="50%">Asunto :</td>
                    <td class="detalle" colspan="3">
                        <input type="hidden" id="radicado_adjunto_exp">
                        <textarea id="asunto_radicado_adjunto_exp" type="search" placeholder="Ingrese el asunto o descripción del archivo adjunto" title="Ingrese el asunto o descripción del archivo adjunto." onblur="validar_input('asunto_radicado_adjunto')" style="width:100%;padding:5px;"></textarea>
                        <div id="asunto_radicado_adjunto_exp_max" class="errores">El asunto o descripción del archivo adjunto no puede ser mayor a 500 caracteres (numeros o letras). (Actualmente <b><u id='asunto_radicado_adjunto_exp_contadormax'></u></b> caracteres)</div>
                        <div id="asunto_radicado_adjunto_exp_min" class="errores">El asunto o descripción del archivo adjunto no puede ser menor a 6 caracteres (numeros o letras) </div>
                        <div id="asunto_radicado_adjunto_exp_null" class="errores">El asunto o descripción del archivo adjunto es obligatorio</div>
                    </td>
                </tr>
                <tr>
                    <td class="descripcion">Archivo PDF (Documento que va a adjuntar)  :</td>
                    <td class="detalle" colspan="3">
                       <input type="file" name="archivo_pdf_radicado_exp" id="archivo_pdf_radicado_exp">

                        <div id="archivo_pdf_radicado_exp_error" class="errores"> El archivo a cargar es un campo obligatorio. El sistema solo admite formato PDF</div>
                        <div id="archivo_pdf_radicado_exp_invalido" class="errores"> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
                        <div id="archivo_pdf_radicado_exp_tamano" class="errores">El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="center">
                        <input type="button" value="Adjuntar Archivo" class="botones" onclick="enviar_adjuntar_archivo('exp')">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<!-- Hasta aqui formulario adjuntar archivos_exp -->

<!-- Formulario para No requiere respuesta -->
<div id="ventana_no_requiere_respuesta" class="ventana_modal form">
    <div class="form">
        <div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
        <h1>Documento No Requiere Respuesta</h1>
        <div id="nombre_documento_exp"></div>
        <hr>
        <form autocomplete="off">
            <table border="0">
                <tr>                    
                    <td class="descripcion" width="20%">
                        Codigo Serie :
                    </td>
                    <td class="detalle" width="30%">
                        <select id="codigo_serie_nrr" title="Seleccione el código de la serie documental" class="select_opciones" <?php echo "onchange='cargar_codigo_subserie2(this.value,\"\",\"$dependencia\",\"documento_nrr\",\"codigo_subserie_nrr\");limpiar_opciones_expediente(\"nrr\")'"; ?>>            
                        </select>
                        
                        <div id="error_codigo_serie_nrr" class="errores">Debe seleccionar por lo menos una serie del listado</div>
                    </td>
                    <td class="descripcion" width="20%">
                        Codigo Subserie
                    </td>
                    <td class="detalle" width="30%" >
                        <select id="codigo_subserie_nrr" title="Seleccione el código de la serie documental" class="select_opciones" <?php echo "onchange='validar_input_formulario_serie_subserie_nrr(\"$dependencia\",\"resultado_seleccionar_expediente\",\"codigo_serie_nrr\",\"\")';" ?>>

                            <option value="">No hay subseries asociadas a la serie seleccionada
                            </option>
                        </select>
                        <div id="error_codigo_subserie_nrr" class="errores">No existen subseries asociadas a la serie seleccionada</div>
                        <div id="error2_codigo_subserie_nrr" class="errores">Debe seleccionar por lo menos una subserie del listado</div>
                    </td>
                </tr>
                <tr id="input_seleccionar_expediente_exp" style="display: table-row;">
                    <td class="descripcion ">
                        Expediente al que va a pertenecer éste documento
                    </td>
                    <td class="detalle">
                        <input type="hidden" id="id_expediente_nrr" placeholder="id_expediente_nrr"> 
                        <input type="search" id="seleccionar_expediente" class="input_search" placeholder="Este campo es OBLIGATORIO" title="Este campo es OBLIGATORIO.">
                        
                        <div id="seleccionar_expediente_max" class="errores">El campo de expediente no puede ser mayor a 100 caracteres (numeros o letras). (Actualmente <b><u id='seleccionar_expediente_contadormax'></u></b> caracteres)</div>
                        <div id="seleccionar_expediente_min" class="errores">El campo de expediente no puede ser menor a 3 caracteres (numeros o letras)</div>
                        <div id="seleccionar_expediente_null" class="errores">El asunto o nombre del expediente es obligatorio</div>
                        <div id="error_seleccionar_expediente" class="errores">El numero o asunto del expediente no existe en el inventario. Ingrese por favor un numero o asunto de expediente válido</div>    
                        <div id="seleccionar_expediente_invalido" class="errores">Debe seleccionar un asunto o nombre de expediente válido.</div>     

                        <div id="resultado_seleccionar_expediente" style="overflow-x: auto;max-height: 100px;"></div>
                    </td>
                    <td class="descripcion ">
                        Va a mover éste documento a la carpeta personal
                    </td>
                    <td class="detalle">
                        <select id='listado_carpetas_personales_nrr' class='select_opciones' onchange="validar_carpeta_personal_nrr(this.value)">
                            <option value="">--Seleccione una carpeta personal --</option>
                            <?php echo $carpeta_personal_nrr; ?>
                        </select>
                        <div id="error_carpetas_personales_nrr" class="errores">No se han creado todavía carpetas personales</div>
                        <div id="error_carpetas_personales_nrr2" class="errores">Debe seleccionar una carpeta personal</div>
                    </td>
                </tr>
                
            </table>

            <table>
                <tr>
                    <td class="descripcion" width="20%">Observaciones :</td>
                    <td class="detalle" colspan="3">
                        <textarea id="observaciones_aprobar_documento" type="search" placeholder="Indique por que el documento no requiere respuesta." title="Indique por que el documento no requiere respuesta.." onblur="validar_input('observaciones_aprobar_documento')" style="width:98%;padding:5px;"></textarea>
                        <div id="observaciones_aprobar_documento_max" class="errores">La observación no puede ser mayor a 500 caracteres (numeros o letras). (Actualmente <b><u id='observaciones_aprobar_documento_contadormax'></u></b> caracteres)</div>
                        <div id="observaciones_aprobar_documento_min" class="errores">La observación no puede ser menor a 6 caracteres (numeros o letras) </div>
                        <div id="observaciones_aprobar_documento_null" class="errores">La observación es obligatoria</div>
                    </td>
                </tr>
               
                <tr>
                    <td colspan="2" class="center">
                    	<div id="contenedor_boton_nrr">
                        	<input type="button" value="Marcar como NRR (No Requiere Respuesta)" class="botones" onclick="valida_envio_nrr()">
                    	</div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<!-- Hasta aqui formulario para No requiere respuesta -->

<!-- Inicio ventana para asignar TRD al radicado que se despliega cuando el radicado no tiene TRD en la base de datos. -->
<div id="ventana_serie_subserie_radicado" class="ventana_modal">
    <div class="form">
    <div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
    <h1 class="center">Formulario TRD Radicado</h1>
    
    <form id ="formulario_trd_radicado" name ="formulario_trd_radicado" autocomplete="off" onsubmit="return false;">
        <hr>
        <center>
            <table border="0">
                <tr>                    
                    <td class="descripcion" width="20%">
                        Codigo Serie :
                    </td>
                    <td class="detalle" width="30%">
                        <select id="codigo_serie" title="Seleccione el código de la serie documental" class="select_opciones" <?php echo "onchange='validar_input_formulario_serie_subserie(); cargar_codigo_subserie2(this.value,\"\",\"$dependencia\",\"formulario_trd\",\"codigo_subserie\")'";?>></select>
                        <div id="error_codigo_serie" class="errores">Debe seleccionar por lo menos una serie del listado</div>
                    </td>
                    <td class="descripcion" width="20%">
                        Codigo Subserie
                    </td>
                    <td class="detalle" width="30%" >
                        <select id="codigo_subserie" title="Seleccione el código de la serie documental" class="select_opciones" onchange="validar_input_formulario_serie_subserie()"><option value="">No hay subseries asociadas a la serie seleccionada</option></select>
                        <div id="error_codigo_subserie" class="errores">No existen subseries asociadas a la serie seleccionada</div>
                        <div id="error2_codigo_subserie" class="errores">Debe seleccionar por lo menos una subserie del listado</div>
                    </td>
                </tr>
                
            </table>
        </center>
    </form>
    <center>
        <div id="boton_trd_radicado" class="input_asunto_expediente">
            <input type="button" value="Asignar TRD al radicado" onclick="insertar_trd_radicado()" class="botones">             
        </div>
    </center>
    </div>              
</div>              
<!-- Fin ventana para asignar TRD al radicado que se despliega -->
<!-- Formulario para TRD y expediente -->
<div id="ventana_trd_expediente" class="ventana_modal form">
    <div class="form">
        <div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
        <h1>Formulario asignar TRD y EXPEDIENTE</h1>
        <div id="nombre_documento_exp"></div>
        <hr>
        <form autocomplete="off">
            <table border="0">
                <tr>                    
                    <td class="descripcion" width="20%">
                        Codigo Serie :
                    </td>
                    <td class="detalle" width="30%">
                        <select id="codigo_serie_trd_exp" title="Seleccione el código de la serie documental" class="select_opciones" <?php echo "onchange='cargar_codigo_subserie2(this.value,\"\",\"$dependencia\",\"formulario_trd_exp\",\"codigo_subserie_trd_exp\"); limpiar_opciones_expediente(\"trd_exp\")'"; ?>>  
                        </select>
					<div id="error_codigo_serie_trd_exp" class="errores">Debe seleccionar por lo menos una serie del listado</div>
                    </td>
                    <td class="descripcion" width="20%">
                        Codigo Subserie
                    </td>
                    <td class="detalle" width="30%" >
                        <select id="codigo_subserie_trd_exp" title="Seleccione el código de la subserie documental" class="select_opciones" <?php echo "onchange='validar_input_formulario_serie_subserie_nrr(\"$dependencia\",\"resultado_seleccionar_expediente_trd_exp\",\"codigo_serie_trd_exp\",\"\")';" ?>><option value="">No hay subseries asociadas a la serie seleccionada
                            </option>
                        </select>
                        <div id="error_codigo_subserie_trd_exp" class="errores">No existen subseries asociadas a la serie seleccionada</div>
                        <div id="error2_codigo_subserie_trd_exp" class="errores">Debe seleccionar por lo menos una subserie del listado</div>
                    </td>
                </tr>
                <tr id="input_seleccionar_expediente_exp" style="display: table-row;">
                    <td class="descripcion" colspan="2">
                        Expediente al que va a pertenecer éste documento
                    </td>
                    <td class="detalle" colspan="2">
                        <input type="hidden" id="id_expediente_trd_exp" placeholder="id_expediente_trd_exp"> 
                        <input type="search" id="seleccionar_expediente_trd_exp" class="input_search" placeholder="Este campo es OBLIGATORIO" title="Este campo es OBLIGATORIO.">
                        
                        <div id="seleccionar_expediente_trd_exp_invalido" class="errores">Debe seleccionar un asunto o nombre de expediente válido.</div>   

                        <div id="resultado_seleccionar_expediente_trd_exp" style="overflow-x: auto;max-height: 100px;"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="center" id="boton_asignar_trd_exp">
                        <input type="button" id="asignar_trd_exp" value="Asignar TRD y EXPEDIENTE" class="botones" onclick="asignar_trd_expediente()">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<!-- Hasta aqui formulario para TRD y expediente -->

<!-- Formulario asociar_pdf_principal -->
<div id="ventana_asociar_pdf_principal" class="ventana_modal form">
    <div class="form">
        <div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
        <h1>Formulario Asociar PDF FIRMADO</h1>
        <!-- <div id="nombre_documento_exp"></div> -->
        <hr>
        <form autocomplete="off">
            <table border="0" id="tabla1_asociar_pdf">
               <!--  <tr>                    
                    <td class="descripcion" width="20%">
                        Codigo Serie :
                    </td>
                    <td class="detalle" width="30%">
                        <select id="codigo_serie_pdf_firmado" title="Seleccione el código de la serie documental" class="select_opciones" <?php // echo "onchange='cargar_codigo_subserie2(this.value,\"\",\"$dependencia\",\"codigo_serie_pdf_firmado\",\"codigo_subserie_pdf_firmado\");  validar_input_formulario_pdf_firmado(\"$dependencia\")'"; ?>>            
                        </select>
                        
                        <div id="error_codigo_serie_pdf_firmado" class="errores">Debe seleccionar por lo menos una serie del listado</div>
                    </td>
                    <td class="descripcion" width="20%">
                        Codigo Subserie
                    </td>
                    <td class="detalle" width="30%" >
                        <select id="codigo_subserie_pdf_firmado" title="Seleccione el código de la serie documental" class="select_opciones" <?php// echo "onchange='validar_input_formulario_pdf_firmado(\"$dependencia\")'" ?>>
                            <option value="">No hay subseries asociadas a la serie seleccionada
                            </option>
                        </select>
                        <div id="error_codigo_subserie_pdf_firmado" class="errores">No existen subseries asociadas a la serie seleccionada</div>
                        <div id="error2_codigo_subserie_pdf_firmado" class="errores">Debe seleccionar por lo menos una subserie del listado</div>
                    </td>
                </tr> -->
                <tr id="input_seleccionar_expediente_exp" style="display: table-row;">
                    <!-- <td class="descripcion ">
                        Expediente al que va a pertenecer éste documento
                    </td>
                    <td class="detalle">
                        <input type="hidden" id="id_expediente_pdf_principal" value="">   
                        <input type="search" id="seleccionar_expediente_pdf_firmado" class="input_search" placeholder="Este campo es OBLIGATORIO" title="Este campo es OBLIGATORIO." readonly>
                        
                        <div id="seleccionar_expediente_invalido_pdf_firmado" class="errores">Debe seleccionar un asunto o nombre de expediente válido.</div>   

                        <div id="resultado_seleccionar_expediente_pdf_firmado" style="overflow-x: auto;max-height: 100px;"></div>
                    </td> -->
                    <td class="descripcion ">
                        <input type="hidden" id="version_siguiente_pdf_principal" value="">   
                        <input type="hidden" id="radicado_pdf_principal" value="">   
                        Va a mover éste documento a la carpeta personal
                    </td>
                    <td class="detalle">
                        <select id='listado_carpetas_personales_pdf_principal' class='select_opciones' onchange="validar_carpeta_personal_principal(this.value); $('#observaciones_asociar_pdf_firmado').focus();">
                            <option value="">--Seleccione una carpeta personal --</option>
                            <?php echo $carpeta_personal_nrr; ?>
                        </select>
                        <div id="error_carpetas_personales_pdf_principal" class="errores">No se han creado todavía carpetas personales</div>
                        <div id="error_carpetas_personales_pdf_principal2" class="errores">Debe seleccionar una carpeta personal</div>
                    </td>
                    <td id="contenedor_viewer_principal_pdf" rowspan="3" width="0px">
                        <iframe id='viewer_principal_pdf' style="background-color: grey; display: none; height: 375px; width: 100%;"></iframe>
                    </td>
                </tr>
                <tr>
                    <td class="descripcion" width="20%">Justificacion para el histórico (Trazabilidad):</td>
                    <td class="detalle">
                        <!-- <input type="hidden" id="radicado_adjunto_exp"> -->
                        <textarea id="observaciones_asociar_pdf_firmado" type="search" placeholder="Ingrese la justificación para guardar en la trazabilidad" title="Ingrese la justificación para guardar en la trazabilidad" onblur="validar_input('observaciones_asociar_pdf_firmado')" style="width:98%;padding:5px;"></textarea>
                        <div id="observaciones_asociar_pdf_firmado_max" class="errores">La Justificacion para el histórico (Trazabilidad) no puede ser mayor a 500 caracteres (numeros o letras). (Actualmente <b><u id='observaciones_asociar_pdf_firmado_contadormax'></u></b> caracteres)</div>
                        <div id="observaciones_asociar_pdf_firmado_min" class="errores">La Justificacion para el histórico (Trazabilidad) no puede ser menor a 6 caracteres (numeros o letras) </div>
                        <div id="observaciones_asociar_pdf_firmado_null" class="errores">La Justificacion para el histórico (Trazabilidad) es obligatoria</div>
                    </td>
                </tr>         
                <tr>
                    <td class="descripcion">Archivo PDF (Documento PDF con las firmas correspondientes)  :</td>
                    <td class="detalle" >
                       <input type="file" name="archivo_pdf_radicado_principal" id="archivo_pdf_radicado_principal" onchange="enviar_subir_pdf()">

                        <div id="archivo_pdf_radicado_principal_error" class="errores"> El archivo a cargar es un campo obligatorio. El sistema solo admite formato PDF</div>
                        <div id="archivo_pdf_radicado_principal_invalido" class="errores"> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
                        <div id="archivo_pdf_radicado_principal_tamano" class="errores">El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="center" id='contenedor_boton_enviar_subir_pdf'>
                        <input type="button" value="Subir PDF Principal FIRMADO" class="botones" onclick="enviar_subir_pdf()">
                    </td>
                </tr>
            </table>
        </form>
    </div> 

</div>
<!-- Hasta aqui formulario asociar_pdf_principal -->
<?php 
	echo "<script>
        $('#detalle_asunto').val('$asunto_radicado')
    </script>";
?>
</body>
</html>