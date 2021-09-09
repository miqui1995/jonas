<?php 
if(!isset($_SESSION)){
	session_start();
} 

require_once("../../login/validar_inactividad.php");
require_once("../../include/genera_fecha.php"); /* Para traducir las fechas desde 2018-12-01 18:17:30 a 01 de Diciembre de 2018 a las 18:17:30 */

?>
<html>
<head>
	<meta charset="UTF-8">
	<title>Despliegue de pestanas</title>
</head>

<body>
<script type="text/javascript">
$('#asunto_radicado').keyup( // Retraso de 1 segundo para validar_input
	delay(function (e) {
		validar_input('asunto_radicado');
	}, 1000)
);
$('#archivo_pdf_radicado_doc').change( // Al cambiar archivo_pdf_radicado
	delay(function (e) {
		validar_input_file('archivo_pdf_radicado_doc'); 
		$("#archivo_pdf_radicado_doc_error").slideUp("slow");
	}, 500)			
)
$('#archivo_pdf_radicado_exp').change( // Al cambiar archivo_pdf_radicado
	delay(function (e) {
		validar_input_file('archivo_pdf_radicado_exp','viewer_exp'); 
		$("#archivo_pdf_radicado_exp_error").slideUp("slow");
	}, 500)			
)

function cargar_asociar_pdf_principal(numero_radicado,id_expediente,nombre_expediente,version_siguiente){
	$("#ventana_asociar_pdf_principal").slideDown("slow");

	// consulta_listado_series2(codigo_serie, dependencia, 'codigo_serie_pdf_firmado') 
 //    cargar_codigo_subserie2(codigo_serie,codigo_subserie,dependencia,'formulario_pdf_principal','codigo_subserie_pdf_firmado')
    $("#id_expediente_pdf_principal").val(id_expediente);
    $("#seleccionar_expediente_pdf_firmado").val(nombre_expediente);
	$("#radicado_pdf_principal").val(numero_radicado);
	$("#version_siguiente_pdf_principal").val(version_siguiente);

	// $("#observaciones_asociar_pdf_firmado").focus();
	$("#listado_carpetas_personales_pdf_principal").focus();
}

function carga_no_requiere_respuesta(numero_radicado,codigo_serie,codigo_subserie,dependencia){
	$("#ventana_no_requiere_respuesta").slideDown("slow");

	var id_expediente 		= $("#expediente").val();
	var nombre_expediente 	= $("#lista_nombre_expedientes").val();

	consulta_listado_series2(codigo_serie,dependencia,'codigo_serie_nrr');
    cargar_codigo_subserie2(codigo_serie,codigo_subserie,dependencia,'formulario_nrr','codigo_subserie_nrr')

    if(codigo_subserie!=""){
    	setTimeout(function() {
    		if(id_expediente==""){
	    		validar_input_formulario_serie_subserie_nrr(dependencia,'resultado_seleccionar_expediente','codigo_serie_nrr','');
    		}else{
    			$("#id_expediente_nrr").val(id_expediente);
    			$("#seleccionar_expediente").val(nombre_expediente);
    		}
        },500);
    }
    $("#codigo_serie_nrr").focus();
}
function cargar_adjunto(numero_radicado,asunto,pestana_que_invoca){
	// console.log("La pestaña que invoca es "+pestana_que_invoca);
	if(pestana_que_invoca=="pestana_documentos"){
		$("#nombre_documento_doc").html("<h4>Al documento (<i style='color:blue;'>"+asunto+"</i>)</h4>");
		$("#radicado_adjunto_doc").val(numero_radicado);

		$("#ventana_adjuntar_archivos_doc").slideDown("slow");
		$("#asunto_radicado_adjunto_doc").focus();			
		$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
	}else{
		$("#nombre_documento_exp").html("<h4>Al documento (<i style='color:blue;'>"+asunto+"</i>)</h4>");
		$("#radicado_adjunto_exp").val(numero_radicado);

		$("#ventana_adjuntar_archivos_exp").slideDown("slow");
		$("#asunto_radicado_adjunto_exp").focus();			
		$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
	}
}

function cargar_expediente(num_exp,ubicacion_topografica,nombre_expediente,lista_exp,max2){
	for (var i = 0; i <= max2; i++) {
		console.log(i)
		$("#expediente_"+i).css("background-color","white");	
	}
	$("#"+lista_exp).css("background-color","#2D9DC6");

	var radicado = $("#radicado").val();

	$("#id_expediente").val(num_exp); // Asigna el numero de expediente para facilitar los préstamos
	$('#asunto_expediente1').val(nombre_expediente);	// Asigna el asunto del expediente para facilitar los préstamos

	$("#info_expediente").slideDown("slow");

	$("#ubicacion_topografica_expediente").html(ubicacion_topografica);

	$("#visor_expedientes" ).animate({ // Para volver al 50% el width de la tabla.
    	width: "100%"
    },{
      	queue: false,
      	duration: 500
    })
	$("#visor_adjuntos_pdf").fadeOut("slow") // Para mostrar el visor_adjuntos_pdf

	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'id_expediente' : num_exp,
            'radicado' 		: radicado,
            'recibe_ajax' 	: 'cargar_expediente'
        },          
        success: function(resp1){
            if(resp1!=""){
                $("#contenido_expediente").html(resp1);
            }
        }
    })
}

function cargar_expediente_nuevo(dependencia){ 
	$("#visor_adjuntos_pdf").fadeOut("slow") // Para mostrar el visor_adjuntos_pdf
	$("#visor_expedientes" ).animate({ // Para volver al 50% el width de la tabla.
    	width: "100%"
    },{
      	queue: false,
      	duration: 500
    })
    
	var radicado 	= $("#radicado").val();
	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'dependencia' 	: dependencia,
            'radicado' 		: radicado,
            'recibe_ajax' 	: 'listado_expedientes_dependencia'
        },          
        success: function(resp1){
            if(resp1!=""){
                $("#listado_expedientes").html(resp1);
            }
        }
    })
}

// function cargar_respuesta(radicado_padre,radicado_respuesta){
// 	validar_input_file('archivo_pdf_respuesta');

// 	if($(".errores").is(":visible")){
//         console.log("No se puede realizar accion");
//         return false;
// 	}else{
// 		var inputFileRadicado 	= document.getElementById('archivo_pdf_respuesta');
//         var file 				= inputFileRadicado.files[0];

// 		var data 				= new FormData();

// 		data.append('recibe_ajax','cargar_respuesta_radicado');
// 		data.append('archivo_pdf_respuesta',file);
// 		data.append('radicado_padre',radicado_padre);
// 		data.append('radicado_respuesta',radicado_respuesta);

// 		$.ajax({
// 			type: 'POST',
// 			url: 'include/procesar_ajax.php',
// 			data: data,			
// 	        contentType:false,
// 	        processData:false,
// 			success: function(resp){
// 				console.log(resp)
// 				if(resp!=""){
// 					$('#resultado_js').html(resp);
// 				}
// 			}
// 		})
// 	}

// }

function cargar_version_radicado(){
	$("#ventana_ver_versiones_documento").slideDown("slow");
	var alto_pantalla = $("#alto_pantalla").val();

	$("#ventana_ver_versiones_documento .form").css("height",alto_pantalla-125+"px")
	$("#visor_version_documentos").css("height",alto_pantalla-196+"px")
	$("#tabla_listado_version_documento").css("height",alto_pantalla-196+"px")
}

function cargar_version_respuesta(numero_radicado){ // funcion para cargar desde la pestaña cuando es una respuesta 
	$("#ventana_ver_versiones_documento").slideDown("slow");
	$("#titulo_version_documento").html("Versión de la respuesta "+numero_radicado);

	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'radicado' 		: numero_radicado,
            'recibe_ajax' 	: 'cargar_version_respuesta_radicado'
        },          
        success: function(resp1){
            if(resp1!=""){
                $("#tabla_listado_version_documento").html(resp1);
            }
        }
    })
}

function enviar_adjuntar_archivo(doc_exp){ // Validar para enviar archivo_adjunto
	if(doc_exp=="doc"){
		validar_input('asunto_radicado_adjunto_doc');
		validar_input_file('archivo_pdf_radicado_doc');
	}else{
		validar_input('asunto_radicado_adjunto_exp');
		validar_input_file('archivo_pdf_radicado_exp');			
	}

	if($(".errores").is(":visible")){
        console.log("No se puede realizar accion");
        return false;
	}else{
		if(doc_exp=="doc"){
			var radicado 	= $("#radicado_adjunto_doc").val();
			var asunto  	= $("#asunto_radicado_adjunto_doc").val();
			
			var inputFileRadicado = document.getElementById('archivo_pdf_radicado_doc');
		}else{
			var radicado 	= $("#radicado_adjunto_exp").val();
			var asunto  	= $("#asunto_radicado_adjunto_exp").val();
			
			var inputFileRadicado = document.getElementById('archivo_pdf_radicado_exp');
		}

		// console.log(radicado)

        var file 	= inputFileRadicado.files[0];
		var data 	= new FormData();

		data.append('recibe_ajax','adjuntar_archivo');
		data.append('archivo_pdf_radicado',file);
		data.append('radicado',radicado);
		data.append('asunto',asunto);

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

function enviar_subir_pdf(){ // Validar para subir PDF principal 
	var archivo_pdf 		= $("#archivo_pdf_radicado_principal").val();
	var radicado 			= $("#radicado_pdf_principal").val();
	var version_siguiente 	= $("#version_siguiente_pdf_principal").val();  
	var carp_personales 	= $("#listado_carpetas_personales_pdf_principal").val(); 
	var comentario_trans 	= $("#observaciones_asociar_pdf_firmado").val(); 

	if(carp_personales==""){
		$("#error_carpetas_personales_pdf_principal2").slideDown("slow");
		$("#listado_carpetas_personales_pdf_principal").focus();
		return false;
	}

	if(archivo_pdf==""){
		$("#archivo_pdf_radicado_principal_error").slideDown("slow");
		$("#archivo_pdf_radicado_principal").focus();
		return false;
	}else{
		$("#archivo_pdf_radicado_principal_error").slideUp("slow");
	}

	validar_input('observaciones_asociar_pdf_firmado');
	var validar_imagen = validar_input_file('archivo_pdf_radicado_principal','viewer_principal_pdf');
	
	if(validar_imagen==false){
		$("#contenedor_viewer_principal_pdf").animate({ // Para volver al 50% el width de la tabla.
	    	width: "0%"
	    }, {
	      	queue: false,
	      	duration: 500
	    })

		$("#viewer_principal_pdf").hide() // Para mostrar el visor_adjuntos_pdf
	}else{
		/* Redimensiona a 50% para dar paso al div #visor_adjuntos_pdf */
	    $("#contenedor_viewer_principal_pdf").animate({ // Para volver al 50% el width de la tabla.
	        width: "50%"
	    }, {
	        queue: false,
	        duration: 1
	    })

	    /* Muestra el div donde se despliega la imagen del PDF */
	    $("#viewer_principal_pdf").fadeIn("slow") // Para mostrar el visor_adjuntos_pdf
	}

	if($(".errores").is(":visible")){
		return false;
	}

	loading('contenedor_boton_enviar_subir_pdf'); 

	/* Calcula numero aleatorio para adicionar al nombre del documento */
	var nuevo_nombre_documento = Math.floor(Math.random() * (99999 - 10000)) + 10000;

	var inputFileRadicado 	= document.getElementById('archivo_pdf_radicado_principal');
    var file 				= inputFileRadicado.files[0];

	var data 				= new FormData();

	data.append('recibe_ajax','enviar_subir_pdf');
	data.append('pdf_principal',file);

	data.append('carpeta_personal',carp_personales);
	data.append('comentario',comentario_trans);
	data.append('nuevo_nombre_documento',nuevo_nombre_documento);
	data.append('radicado',radicado);
	data.append('version_siguiente',version_siguiente);

	$.ajax({
		type: 'POST',
		url: 'include/procesar_ajax.php',
		data: data,			
        contentType:false,
        processData:false,
		success: function(resp){
			if(resp!=""){
				$('#resultado_js').html(resp);
			}
		}
	})
}

function incluir_en_expediente(id_expediente){
	var radicado = $("#radicado").val();

	Swal.fire({
        title 				:'¿Está seguro de que quiere agregar el radicado '+radicado+' al expediente  ('+id_expediente+') ?',
        text 				: "Esta acción no se puede revertir. ¿Está seguro?",
        type 				: 'warning',
        showCancelButton 	: true,
        confirmButtonColor 	: '#3085d6',
        cancelButtonColor 	: '#d33',
        confirmButtonText 	: 'Si, Incluir en Expediente!',
        cancelButtonText 	: 'Cancelar'
    }).then((result) => {
        if (result.value) {
			$.ajax({
		        type: 'POST',
		        url: 'include/procesar_ajax.php',
		        data: {
		            'id_expediente' : id_expediente,
		            'radicado' 		: radicado,
		            'recibe_ajax' 	: 'incluir_en_expediente',
		            'peticion_desde': 'pestana_expedientes'
		        },          
		        success: function(resp1){
		            if(resp1!=""){
		                $("#listado_expedientes").html(resp1);
		            }
		        }
		    })
        }
    })
}
/*******************************************************************************************
* @class Funcion para validar parámetros recibidos y dependiendo las validaciones, invocar funciones para cargar el formulario de radicacion de salida o para asignar TRD y EXPEDIENTE previamente. 
* @description Se reciben los parámetros (radicado, codigo_dependencia, codigo_serie2, codigo_subserie2, id_expediente2), Se valida si el código de subserie es vacío para definir la variable "titulo_alert_respuesta" dependiendo si el parámetro "id_expediente2" viene vacío. Si luego de las validaciones la variable "titulo_alert_respuesta" está vacía entonces invoca la función para generar la respuesta en un radicado de salida. Si no, dependiendo la validación muestra la alerta indicando si falta la TRD, el EXPEDIENTE o ambas. Luego despliega el modal para asignar la TRD, el EXPEDIENTE o ambos si corresponde.
* @param {string}(radicado) Numero de radicado. Se usa como parámetro para llamar otras funciones y para mostrar en las alertas el número de radicado que tiene o no TRD y EXPEDIENTE
* @param {string}(codigo_dependencia) Codigo de la dependencia. Se usa como parámetro para llamar otras funciones.
* @param {string}(codigo_serie2) Codigo de la serie. Se usa como parámetro para llamar otras funciones.
* @param {string}(codigo_subserie2) Codigo de la subserie. Se usa como parámetro para llamar otras funciones y para condicionar las opciones para definir comportamiento de la funcion.
* @param {string}(id_expediente2) Id del expediente. Se usa para condicionar las opciones para definir comportamiento de la funcion.
* @return {} No devuelve ningún valor. Es una funcion de invocar otras funciones.
*******************************************************************************************/   
function validar_carga_radicacion_salida2(radicado, codigo_dependencia, codigo_serie2, codigo_subserie2, id_expediente2){

	/* Se define el titulo y la descripcion del alert si le falta TRD y/o expediente */
	if(codigo_subserie2==""){
		var titulo_alert_respuesta = (id_expediente2=="")?'El documento '+radicado+' NO tiene TRD ni EXPEDIENTE asignado todavía':'El documento '+radicado+' NO tiene TRD asignado todavía';
		var descripcion_alert_respuesta = (id_expediente2=="")?'Para poder generar respuesta es necesario asignar TRD y EXPEDIENTE al radicado':'Para poder generar respuesta es necesario asignar TRD al radicado';
	}else{
		var titulo_alert_respuesta = (id_expediente2=="")?'El documento '+radicado+' NO tiene EXPEDIENTE asignado todavía':'';
		var descripcion_alert_respuesta = (id_expediente2=="")?'Para poder generar respuesta es necesario asignar EXPEDIENTE a este radicado':'';
	}	
	
	if(titulo_alert_respuesta!=""){
		Swal.fire({		
			position 			: 'top-end',
	    	showConfirmButton 	: false,
	    	timer 				: 3000,
		    title 				: titulo_alert_respuesta,
		    text 				: descripcion_alert_respuesta ,
		    type 				:'warning'
		}).then(function(isConfirm){
			if(codigo_subserie2==""){
				if(id_expediente2==""){
					/* Despliega despliega el modal para asignar la TRD y/o el EXPEDIENTE */
					asignar_trd_exp(codigo_dependencia, "", "", "")
					// console.log("Sin trd ni expediente")
				}else{
					/* Despliega el modal para asignar la TRD */
					asignar_trd(codigo_dependencia)
				}
			}else{
				if(id_expediente2==""){
					/* Despliega despliega el modal para asignar la TRD y/o el EXPEDIENTE */
					asignar_trd_exp(codigo_dependencia, id_expediente2, codigo_serie2, codigo_subserie2)
				}else{}
			}
		});
	}else{
		/* Función para generar la respuesta en un radicado de salida. */
		carga_radicacion_salida2(radicado,'respuesta');
	}
}

function visualizar_principal(radicado,path_radicado,tipo){
	switch(tipo){
		case 'adjuntos':
		case 'radicados':
			var ancho_visor			= '100%';
			var listado_documentos  = 'listado_documentos_anexos';
			var nombre_visor 		= 'visor_pdf_pestana_documentos';
			break;

		case 'radicados_1':
			var ancho_visor			= '100%';
			var listado_documentos  = 'listado_documentos_anexos';
			var nombre_visor 		= 'visor_pdf_pestana_documentos';
			var tipo 				= "radicados";
			break;
		case 'adjuntos_1':
			var ancho_visor			= '100%';
			var listado_documentos  = 'listado_documentos_anexos';
			var nombre_visor 		= 'visor_pdf_pestana_documentos';
			var tipo 				= "adjuntos";
			break;

		case 'version':
			var ancho_visor			= '100%';
			var listado_documentos  = 'tabla_listado_version_documento';
			var nombre_visor 		= 'visor_version_documentos';
			var tipo 				= "radicados";
			break;	

		case 'radicados_exp':
			var ancho_visor			= '100%';
			var listado_documentos  = 'visor_expedientes';
			var nombre_visor 		= 'visor_adjuntos_pdf';
			var tipo 				= "radicados";
			break;
		case 'adjuntos_exp':
			var ancho_visor			= '100%';
			var listado_documentos  = 'visor_expedientes';
			var nombre_visor 		= 'visor_adjuntos_pdf';
			var tipo 				= "adjuntos";
			break;
	}
	var alto_visor			= '100%';

	$( "#"+listado_documentos ).animate({ // Para volver al 50% el width de la tabla.    tabla_listado_version_documento
    	width: "50%"
    }, {
      	queue: false,
      	duration: 1
    })

	/* Para calcular el ancho, alto y posicion del #+listado_documentos*/
    var alto_pantalla = $("#alto_pantalla").val();
	$('#'+listado_documentos).css('max-height',alto_pantalla-222+"px");

	$("#"+nombre_visor).fadeIn("slow") // Para mostrar el visor_pdf_pestana_documentos

	/* Para calcular el ancho, alto y posicion del #+nombre_visor*/
	$('#'+nombre_visor).css('width','calc(100% - '+$('#'+listado_documentos).width()+'px)');
	$('#'+nombre_visor).css('height',alto_pantalla-222+"px");
	$('#'+nombre_visor).css('float','left');

	var login 					= $("#login").val();
	var caracteres_dependencia 	= $("#caracteres_dependencia").val();

	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax' 				: 'validar_usuario_actual',
            'alto_visor' 				: alto_visor, 
            'ancho_visor' 				: ancho_visor, 
            'caracteres_dependencia' 	: caracteres_dependencia, 
            'login'						: login,
            'path_adjunto'				: path_radicado,
            'radicado'					: radicado,
            'tipo'						: tipo
        },          
        success: function(respuesta){
			$("#"+nombre_visor).html(respuesta); 
        }
    })
}
</script>
<style type="text/css">
	.estilo_respuesta{
		background-color:#92d2cc;
	}
</style>
<?php
function verificar_padre($array,$nombre_nivel,$x){
   	include '../../login/conexion2.php';

	$query_ubic1="select * from ubicacion_topografica where nombre_nivel='$nombre_nivel'";
	
	$fila_ubicacion_fisica = pg_query($conectado,$query_ubic1);
	$linea_ubicacion_fisica = pg_fetch_array($fila_ubicacion_fisica);

	$nivel_padre = $linea_ubicacion_fisica["nivel_padre"];

	if($x==''){
	    $x=0;    
	}
    if($nivel_padre!=""){
    	$array[$x]=$nombre_nivel;
    	$x++;
		return verificar_padre($array,"$nivel_padre",$x);
	}else{
    	$array[$x]=$nombre_nivel;
		return $array;
	}
}  

function validar_boton_prestamo($radicado,$tipo_documento){
	include '../../login/conexion2.php';

	$query_prestamo="select * from prestamos where id_documento_solicitado='$radicado' order by id desc";

	$fila_prestamo 	= pg_query($conectado,$query_prestamo);
	$linea_prestamo = pg_fetch_array($fila_prestamo);

	$confirma_recibido 			= $linea_prestamo['confirma_recibido'];
	$estado_prestamo 			= $linea_prestamo['estado_prestamo'];
	$fecha_solicitud1 			= $linea_prestamo['fecha_solicitud'];
	$id 						= $linea_prestamo['id'];
	$id_documento_solicitado 	= $linea_prestamo['id_documento_solicitado'];
	$login 			 			= $_SESSION['login'];
	$login_solicitante 			= $linea_prestamo['login_solicitante'];

	if($linea_prestamo==""){
		$mensaje_prestamo="sin_prestamos";
	}else{
		if($tipo_documento=='expediente'){
			$tipo_documento1="El expediente completo número ";
			// return "El expediente $radicado se encuentra prestado";	
		}else{
			$tipo_documento1="El radicado individual número ";
			// return "El documento $radicado se encuentra prestado";	
		}
		/* Traduce la fecha desde 2018-12-01 18:17:30 a 01 de Diciembre de 2018 a las 18:17:30 */
			$b = new genera_fecha();

			$fecha_solicitud = $b->traduce_fecha_letra_segundos("$fecha_solicitud1"); // Traduce fecha formato 26 de Diciembre de 2018 a las 18:17:30


		switch ($estado_prestamo) {
			case 'CANCELADO':
			case 'DEVUELTO':
				$mensaje_prestamo="sin_prestamos";
				break;
			case 'SOLICITADO':
				$mensaje_prestamo="$tipo_documento1 $radicado se encuentra solicitado en préstamo por el usuario $login_solicitante desde el $fecha_solicitud";
				break;
			case 'PRESTADO':
				$boton_confirma_fisico_recibido="";
				
				if($confirma_recibido=='NO'){
					$mensaje_confirma_recibido=", pero el usuario <b>$login_solicitante</b> no ha confirmado si lo ha recibido en físico.";
					if($login==$login_solicitante){
						$boton_confirma_fisico_recibido="<input type='button' class='botones2' style=\"font-size:16px; padding: 6px;\" value='Confirmar Documento / Expediente físico recibido' onclick='confirmar_documento_fisico_recibido(\"$id\",\"$tipo_documento1\",\"$id_documento_solicitado\",\"informacion_general\")'>";
					}

				}else{
					$mensaje_confirma_recibido=" <br>El usuario <b>$login_solicitante</b> ha confirmado que lo recibió en físico.";
				}
				
				$mensaje_prestamo="$tipo_documento1 $radicado se encuentra prestado al usuario <b>$login_solicitante</b> desde el $fecha_solicitud $mensaje_confirma_recibido $boton_confirma_fisico_recibido";
				break;	
			}
	}
	return $mensaje_prestamo;
} 

function validar_ubicacion($expediente){
	include '../../login/conexion2.php';

	$query_ubicacion_topografica="select * from expedientes e join ubicacion_topografica u on e.codigo_ubicacion_topografica=u.id_ubicacion where e.id_expediente='$expediente'";

	$fila_ubicacion_topografica = pg_query($conectado,$query_ubicacion_topografica);
	$linea_ubicacion_topografica = pg_fetch_array($fila_ubicacion_topografica);

	if($linea_ubicacion_topografica==false){
		$ubicacion_fisica=false;
	}else{
		$codigo_ubic = $linea_ubicacion_topografica['codigo_ubicacion_topografica'];
		$nombre_nivel=$linea_ubicacion_topografica['nombre_nivel'];
		$nivel_padre=$linea_ubicacion_topografica['nivel_padre'];
		$asunto_expediente=$linea_ubicacion_topografica['nombre_expediente'];

		$array_padre = array();
		$ubicacion_fisica= verificar_padre($array_padre,"$nombre_nivel","");
	}
	
	return($ubicacion_fisica);
}

$pestana  				= $_POST['pestana'];
$radicado 				= $_POST['radicado'];
/* Las variables $login, $ubicacion_topografica y $codigo_dependencia las hereda desde login/validar_inactividad.php*/

$verifica_tipo_radicado = substr($radicado,-1); // Extrae el ultimo digito del numero_radicado directamente para determinar si es una entrada (1), salida(2), inventario(3) o interno(4)

$verifica_inventario1 = substr($radicado,7,3); // Extrae el codigo de la dependencia del numero_radicado directamente cuando el codigo de dependencia es de 3 caracteres
$verifica_inventario2 = substr($radicado,7,4); // Extrae el codigo de la dependencia del numero_radicado directamente cuando el codigo de dependencia es de 4 caracteres
$verifica_inventario3 = substr($radicado,7,5); // Extrae el codigo de la dependencia del numero_radicado directamente cuando el codigo de dependencia es de 5 caracteres

/***************************************************************************************************/
/***************************************************************************************************/
switch ($pestana) {
	case 'informacion_general':
	/* Si al verificar inventario el codigo de la dependencia es INV, INVE o INVEN despliega los datos capturados de inventario */
		if($verifica_inventario1=='INV' || $verifica_inventario2=='INVE' || $verifica_inventario3=='INVEN'){
			// $codigo_dependencia = 'INV'; // Variable para asignar serie al radicado

			$query_radicado=" select i.caja_paquete_tomo,r.codigo_serie, r.codigo_subserie, i.consecutivo_desde, i.consecutivo_hasta, i.descriptor, r.id_expediente, i.fecha_final, i.fecha_inicial, i.fecha_inventario, i.nombre_documento, i.numero_caja_archivo_central, i.numero_caja_paquete, i.numero_carpeta, i.observaciones, r.path_radicado, i.total_folios, r.usuarios_visor, r.usuarios_control from radicado r join inventario i on i.radicado_jonas=r.numero_radicado where i.radicado_jonas='$radicado'";

			$fila_radicado  = pg_query($conectado,$query_radicado);
			
			if($fila_radicado==false){ // Valida si la tabla radicados existe en la base de datos
				echo '<script>
					alert("No pude conectarme a la tabla R de la base de datos 1, revisa la base de datos por favor");
					window.location.href="principal3.php"
				</script>';	
			}	

			$linea_radicado 				= pg_fetch_array($fila_radicado);
			$caja_paquete_tomo 				= $linea_radicado['caja_paquete_tomo'];
			$codigo_serie 					= $linea_radicado['codigo_serie'];
			$codigo_subserie 				= $linea_radicado['codigo_subserie'];
			$consecutivo_desde 				= $linea_radicado['consecutivo_desde'];
			$consecutivo_hasta 				= $linea_radicado['consecutivo_hasta'];
			$descriptor 					= $linea_radicado['descriptor'];
			$expediente 					= $linea_radicado['id_expediente'];
			$fecha_final 					= $linea_radicado['fecha_final'];
			$fecha_inicial 					= $linea_radicado['fecha_inicial'];
			$fecha_inventario 				= $linea_radicado['fecha_inventario'];
			$medio_respuesta_solicitado 	= $linea_radicado['medio_respuesta_solicitado'];
			$nombre_documento 				= $linea_radicado['nombre_documento'];
			$numero_caja_archivo_central 	= $linea_radicado['numero_caja_archivo_central'];
			$numero_caja_paquete 			= $linea_radicado['numero_caja_paquete'];
			$numero_carpeta 				= $linea_radicado['numero_carpeta'];
			$observaciones 					= $linea_radicado['observaciones'];
			$path_radicado 					= $linea_radicado['path_radicado'];
			$total_folios 					= $linea_radicado['total_folios'];
			$usuarios_control 				= $linea_radicado['usuarios_control'];
			$usuarios_visor 				= $linea_radicado['usuarios_visor'];

			if($codigo_serie=='' && $codigo_subserie==''){
				$codigo_serie1 		= "<div class='art_exp' onclick='asignar_trd(\"$codigo_dependencia\")'>Asignar TRD</div>";
				$codigo_subserie1 	= "<div class='art_exp' onclick='asignar_trd(\"$codigo_dependencia\")'>Asignar TRD</div>";
			}else{
			/* Inicio consulta para mostrar nombre serie y subserie */
				$consulta_nombre_serie 	= "select * from series where codigo_serie='$codigo_serie'";
				$fila_nombre_serie  	= pg_query($conectado,$consulta_nombre_serie);
				$linea_nombre_serie 	= pg_fetch_array($fila_nombre_serie);
				$nombre_serie 			= $linea_nombre_serie['nombre_serie'];

				$codigo_serie1 			= "(<b>$codigo_serie</b>) $nombre_serie";

				$consulta_nombre_subserie 	= "select * from subseries where codigo_serie='$codigo_serie' and codigo_subserie='$codigo_subserie'";
				$fila_nombre_subserie  	= pg_query($conectado,$consulta_nombre_subserie);
				$linea_nombre_subserie 	= pg_fetch_array($fila_nombre_subserie);
				$nombre_subserie 		= $linea_nombre_subserie['nombre_subserie'];

				$codigo_subserie1 		= "(<b>$codigo_subserie</b>) $nombre_subserie";
			/* Fin consulta para mostrar nombre serie y subserie */
			}
		/* Se define ruta del path para mostrar PDF principal del radicado */	
			if($path_radicado==""){
				$path="<h1>No se ha digitalizado el PDF correspondiente a este radicado.</h1>";
			}else{
				$path_bodega="bodega_pdf/radicados/$path_radicado";
				$path_bodega_valida="../../bodega_pdf/radicados/$path_radicado";
			    
			    if(file_exists($path_bodega_valida)){ // Si existe el pdf en la carpeta radicados/xxxx.pdf
			    	if (strpos($usuarios_visor, $login) !== false) { // Validar si $login se encuentra entre los usuarios_actuales
						$path="<object data='$path_bodega' type='application/pdf' width='100%' style='height:85vh;'></object>";
					}else{
						if($codigo_entidad=='AV1'){
							if(file_exists($path_bodega_valida)){ // Si existe el pdf en la carpeta radicados/xxxx.pdf
								$path="<object data='$path_bodega' type='application/pdf' width='100%' style='height:85vh;'></object>";
							}else{
								$path="<h1>No se ha digitalizado el PDF correspondiente a este radicado.</h1>";
							}
						}else{
			    			$path ="<h3 style='color:red;'>Usted no tiene permitido ver éste radicado. Puede solicitar que le envíe el documento alguno de los usuarios: $usuarios_control</h3>";
						}
		     		}
				}else{
					$path="<h1>No se ha digitalizado el PDF correspondiente a este radicado.</h1>";
				}
			}
		/* Fin se define ruta del path para mostrar PDF principal del radicado */	
?>
			<div id="table">
				<table border="0" width='100%'>
					<tr>
						<td class="descripcion">
							Nombre del Documento
						</td>
						<td class="detalle" colspan="5">
							<?php echo "$nombre_documento"; ?>
						</td>
					</tr>
					<tr>
						<td class="descripcion">
							Descriptor (Metadatos)
						</td>
						<td class="detalle" colspan="2">
							<?php echo "$descriptor"; ?>
						</td>
						<td class="descripcion">
							Observaciones
						</td>
						<td class="detalle" colspan="2">
							<?php echo "$observaciones"; ?>
						</td>
					</tr>
					<tr>
						<td class="descripcion" width="15%">
							Fecha de Inventario
						</td>
						<td class="detalle" width="20%">
							<?php echo "$fecha_inventario"; ?>
						</td>
						<td class="descripcion" width="15%">
							Caja, paquete o tomo 
						</td>
						<td class="detalle" width="20%">
							<?php echo "$caja_paquete_tomo"; ?>
						</td>
						<td class="descripcion" width="15%">
							Numero de Caja o Numero de Paquete
						</td>
						<td class="detalle" width="15%">
							<?php echo "$numero_caja_paquete"; ?>
						</td>
					</tr>
					<tr>
						<td class="descripcion">
							Codigo Serie - Subserie
						</td>
						<td class="detalle">
							<?php echo "$codigo_serie1 - $codigo_subserie1"; ?>
						</td>
						<td class="descripcion">
							Numero de Carpeta
						</td>
						<td class="detalle">
							<?php echo "$numero_carpeta"; ?>
						</td>
						<td class="descripcion">
							Total de Folios
						</td>
						<td class="detalle">
							<?php echo "$total_folios"; ?>
						</td>
					</tr>
					<tr>
						<td class="descripcion">
							Fecha Inicial del Documento
						</td>
						<td class="detalle">
							<?php echo "$fecha_inicial"; ?>
						</td>
						<td class="descripcion">
							Fecha Final del Documento
						</td>
						<td class="detalle">
							<?php echo "$fecha_final"; ?>
						</td>
						<td class="descripcion">
							Numero de Caja en Archivo Central
						</td>
						<td class="detalle">
							<?php echo "$numero_caja_archivo_central"; ?>
						</td>
					</tr>
					<tr>
						<td class="descripcion">
							Consecutivo Desde
						</td>
						<td class="detalle">
							<?php echo "$consecutivo_desde"; ?>
						</td>
						<td class="descripcion">
							Consecutivo Hasta
						</td>
						<td class="detalle">
							<?php echo "$consecutivo_hasta"; ?>
						</td>
					</tr>
					<!-- 
					<tr>
						<td class="descripcion">
							Ubicación Física
						</td>
						<td id="mostrar_boton_prestamo" class="detalle" >
							<div id="informacion_boton_prestamo">
								<?php // echo "$ubicacion_topografica1"; ?>
							</div>
							<div id="detalle_boton_prestamo" >
								<?php // echo "$boton_prestamo"; ?>
							</div>
						</td> 
					</tr>
					-->
				</table>
			</div>
			<div id="imagen_principal">
				<?php echo "$path";?>
			</div>
<?php		
	/* Fin si al verificar inventario el codigo de la dependencia es INV, INVE o INVEN despliega los datos capturados de inventario en el acordeon "Informacion General" */

		}else{ 
	/* Si al verificar inventario el codigo de la dependencia es diferente a INV, INVE o INVEN despliega los datos capturados del radicado en el acordeon "Informacion General" */

			/* Query para consultar en la base de datos los campos cuando es radicado de entrada (1), radicado de salida(2) o si es radicado interno (4) con la tabla datos_origen_radicado. Si es otro tipo de radicado, no guarda datos en ésta tabla. */
			if($verifica_tipo_radicado == 1 or $verifica_tipo_radicado == 2 or $verifica_tipo_radicado == 4){
				$query_radicado = "select * from radicado r full outer join datos_origen_radicado dor on r.numero_radicado=dor.numero_radicado where r.numero_radicado='$radicado' order by asunto limit 10";
			}else{
				$query_radicado="select * from radicado where numero_radicado='$radicado'";
			}

			$fila_radicado 	= pg_query($conectado,$query_radicado);
			$linea_radicado = pg_fetch_array($fila_radicado);
			
			// Defino las variables a mostrar
			$anexos 						= $linea_radicado['descripcion_anexos'];
			$asunto  						= $linea_radicado['asunto'];
			$codigo_serie 					= $linea_radicado['codigo_serie'];
			$codigo_subserie 				= $linea_radicado['codigo_subserie'];
			$expediente 					= $linea_radicado['id_expediente'];
			$fecha_radicado 				= $linea_radicado['fecha_radicado'];
			$medio_respuesta_solicitado 	= $linea_radicado['medio_respuesta_solicitado'];
			$nivel_seguridad 				= $linea_radicado['nivel_seguridad'];
			$numero_guia_oficio 			= $linea_radicado['numero_guia_oficio'];
			$path 							= $linea_radicado['path_radicado'];
			$termino 						= $linea_radicado['termino'];
			$usuarios_control 				= $linea_radicado['usuarios_control'];
			$usuarios_visor 				= $linea_radicado['usuarios_visor'];

		/* Se definen las variables si el radicado es de entrada(1), de salida(2) ó interno (4) que corresponden a la tabla datos_origen_radicado */
			if($verifica_tipo_radicado == 1 or $verifica_tipo_radicado == 2 or $verifica_tipo_radicado == 4){
				$dignatario  		= $linea_radicado['dignatario'];
				$direccion 			= $linea_radicado['direccion'];
				$email 				= $linea_radicado['mail'];
				$municipio 			= $linea_radicado['ubicacion'];
				$remitente 			= $linea_radicado['nombre_remitente_destinatario'];
				$telefono 			= $linea_radicado['telefono'];
				$folios 			= $linea_radicado['folios'];
			}

		/* Verifica si el radicado tiene serie y subserie asignados */
			if($codigo_serie=='' && $codigo_subserie==''){
				$codigo_serie1 		= "<div class='art_exp' style='background: red; color:#FFFFFF; border-color: #FFFFFF;' onclick='asignar_trd(\"$codigo_dependencia\")'>Asignar TRD</div>";
				$codigo_subserie1 	= "";
			}else{
				$codigo_serie1  	= str_pad($codigo_serie, 3, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 3=longitud

		/* Inicio consulta para mostrar nombre serie y subserie porque si tenemos el codigo de serie y subserie asignado */
				$consulta_nombre_serie 	= "select * from series where codigo_serie='$codigo_serie1'";
				$fila_nombre_serie  	= pg_query($conectado,$consulta_nombre_serie);
				$linea_nombre_serie 	= pg_fetch_array($fila_nombre_serie);
				$nombre_serie 			= $linea_nombre_serie['nombre_serie'];

				/* Funcion para quitar los ceros al mostrar el codigo de serie */
		        $codigo_serie2       	= "".(int) $codigo_serie."";
		        $codigo_subserie2      	= "".(int) $codigo_subserie."";

				$codigo_serie1 			= "<b>Serie:</b>(<b title='Codigo de la Serie'>$codigo_serie2</b>) $nombre_serie";

				$consulta_nombre_subserie 	= "select * from subseries where codigo_serie='$codigo_serie' and codigo_subserie='$codigo_subserie'";
				$fila_nombre_subserie  	= pg_query($conectado,$consulta_nombre_subserie);
				$linea_nombre_subserie 	= pg_fetch_array($fila_nombre_subserie);
				$nombre_subserie 		= $linea_nombre_subserie['nombre_subserie'];

				$codigo_subserie1 		= " <br><br><b>Subserie:</b>(<b title='Codigo de la Subserie'>$codigo_serie2,$codigo_subserie2</b>) $nombre_subserie";
		/* Fin consulta para mostrar nombre serie y subserie porque si tenemos el codigo de serie y subserie asignado */
			}
			/* Consulta la ubicación física del radicado */
			$query_ubicacion_fisica = "select * from ubicacion_fisica uf join usuarios us on uf.usuario_actual =us.login where uf.numero_radicado='$radicado'";

			$fila_ubicacion_fisica  	= pg_query($conectado,$query_ubicacion_fisica);
			$registros_ubicacion_fisica = pg_num_rows($fila_ubicacion_fisica);

			if($registros_ubicacion_fisica>0){
				$linea_ubicacion_fisica 	= pg_fetch_array($fila_ubicacion_fisica);
				$nombre_completo 	 		= $linea_ubicacion_fisica['nombre_completo'];
				$login_uf 				 	= $linea_ubicacion_fisica['login'];
				$path_foto 	 				= $linea_ubicacion_fisica['path_foto'];
				$fecha 	 					= $linea_ubicacion_fisica['fecha'];
				$b 							= new genera_fecha();
				$fecha_con_formato 			= $b->traduce_fecha_letra_segundos("$fecha"); // Traduce fecha formato 26 de Diciembre de 2018 a las 18:17:30

				$campo_ubicacion_fisica = "<table border='0'><tr><td colspan='2'>El documento lo tiene en físico desde el<b> $fecha_con_formato</b></td></tr><tr><td rowspan='2' width='55px;'><img src='$path_foto' height='50px'></td></tr><tr><td style='padding:5px;'>El usuario : $login_uf<br><font color='blue'>$nombre_completo</font></td></tr></table>";

			}else{
				$campo_ubicacion_fisica = "<font color='red'>El documento no tiene ubicación física.</font>";
			}



			// if($expediente==""){
			// 	$ubicacion_topografica="No está incluido en expediente todavía.";
			// 	$boton_prestamo="";
			// }else{
			// 	/* Inicio validar prestamo radicado - expediente */
			// 	$validar_prestamo_doc = validar_boton_prestamo($radicado,'radicado');

			// 	if($validar_prestamo_doc=='sin_prestamos'){
			// 		$validar_prestamo_exp=validar_boton_prestamo($expediente,'expediente');

			// 		if($validar_prestamo_exp=='sin_prestamos'){
			// 			$boton_prestamo="<input type='button' class='botones2' style=\"font-size:16px; padding: 6px;\" value='Solicitar físico Documento / Expediente' onclick='menu_prestamo()'>";
			// 		}else{
			// 			$boton_prestamo=$validar_prestamo_exp;
			// 		}

			// 	}else{
			// 		$boton_prestamo=$validar_prestamo_doc;	
			// 	}
			// 	/* Fin validar prestamo radicado - expediente */

			// 	$ubicacion_topografica2=validar_ubicacion($expediente);

			// 	if($ubicacion_topografica2==false){
			// 		$ubicacion_topografica3="Expediente no se ha ubicado topográficamente todavía";
			// 	}else{
			// 		$ubicacion_topografica3="";	
			// 		foreach($ubicacion_topografica2 as $item){
			// 			if($ubicacion_topografica3==""){
			// 				$ubicacion_topografica3=$item;
			// 			}else{
			// 				$ubicacion_topografica3=$ubicacion_topografica3." -> ".$item;
			// 			}
		 //            }
			// 	}
			// 	$ubicacion_topografica=$ubicacion_topografica3;
			// }
			
			if($fila_radicado==false){ // Valida si la tabla radicados existe en la base de datos
				echo '<script>
					alert("No pude conectarme a la tabla R de la base de datos 1, revisa la base de datos por favor");
					window.location.href="principal3.php"
				</script>';	
			}

		/* Valida si el radicado tiene imagen asignada en la base de datos */
			if($path==""){
				$path="<h1>No se ha digitalizado el PDF correspondiente a este radicado.</h1>";
			}

			$path_bodega 		= "bodega_pdf/radicados/$path";
			$path_bodega_valida = "../../bodega_pdf/radicados/$path";
		    
			$destino_path = substr($path,0,4);

			if (strpos($usuarios_visor, $login) !== false) { // Validar si $login se encuentra entre los usuarios_actuales
				if($destino_path=="http"){
					$path = "<iframe src='$path' type='application/pdf' width='99%' style='float:left; height:85vh;'></iframe>";
				}else{
					if(file_exists($path_bodega_valida)){ // Si existe el pdf en la carpeta radicados/xxxx.pdf
						$path="<object data='$path_bodega' type='application/pdf' width='100%' style='height:570px;'></object>";
					}else{
						$path="<h1>No se ha digitalizado el PDF correspondiente a este radicado.</h1>";
					}
				}				
			}else{
				if($codigo_entidad=='AV1'){
					if(file_exists($path_bodega_valida)){ // Si existe el pdf en la carpeta radicados/xxxx.pdf
						$path="<object data='$path_bodega' type='application/pdf' width='100%' style='height:570px;'></object>";

					}else{
						$path="<h1>No se ha digitalizado el PDF correspondiente a este radicado.</h1>";
					}
				}else{
	    			$path ="<h3 style='color:red;'>Usted no tiene permitido ver éste radicado. Puede solicitar que le envíe el documento alguno de los usuarios: $usuarios_control</h3>";
				}
     		}
		/* Fin valida si el radicado tiene imagen asignada en la base de datos */
    
?>	
				<div id="table">
<?php 
			/* Imprime tabla para la pestaña "Informacion General" cuando es radicado de entrada(1), de salida(2) ó interna(4) */
				if($verifica_tipo_radicado == 1 or $verifica_tipo_radicado == 2 or $verifica_tipo_radicado == 4){ 
?>
					<table border="0" style="float: left; width: 100%">
						<tr>
							<td class="descripcion" width="8%">
								Fecha de radicado
							</td>
							<td class="detalle" width="8%">
								<?php echo "$fecha_radicado"; ?>
							</td>
							<td class="descripcion" width="8%">
								Numero de Guía - Oficio del Radicado 
							</td>
							<td class="detalle" width="8%">
								<?php echo "$numero_guia_oficio"; ?>
							</td>
							<td class="descripcion" width="8%">
								Descripcion anexos
							</td>
							<td class="detalle" width="8%">
								<?php echo "$anexos"; ?>
							</td>
							<td rowspan="6" width="50%">
								<?php echo "$path"; ?>
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Asunto
							</td>
							<td class="detalle" colspan="3">
								<?php echo "$asunto"; ?>
							</td>
							<td class="descripcion">
								Nivel de Seguridad del radicado
							</td>
							<td class="detalle">
								<?php echo "$nivel_seguridad"; ?>
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Remitente
							</td>
							<td class="detalle">
								<?php echo "$remitente"; ?>
							</td>
							<td class="descripcion">
								Dignatario
							</td>
							<td class="detalle">
								<?php echo "$dignatario"; ?>
							</td>
							<td class="descripcion">
								Municipio / Departamento
							</td>
							<td class="detalle">
								<?php echo "$municipio"; ?>
							</td>		
						</tr>
						<tr>
							<td class="descripcion">
								Direccion Correspondencia
							</td>
							<td class="detalle">
								<?php echo "$direccion"; ?>
							</td>
							<td class="descripcion">
								Email
							</td>
							<td class="detalle">
								<?php echo "$email"; ?>
							</td>
							<td class="descripcion">
								Telefono
							</td>
							<td class="detalle">
								<?php echo "$telefono"; ?>
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Serie - Subserie
							</td>
							<td class="detalle">
								<?php echo "$codigo_serie1 $codigo_subserie1"; ?>
							</td>
							<td class="descripcion">
								Término del documento
							</td>
							<td class="detalle">
								<?php echo "$termino"; ?>
							</td>
							<td class="descripcion">
								Numero de folios digitalizados
							</td>
							<td class="detalle">
								<?php echo "$folios"; ?>
							</td>
						</tr>
						<tr>
						<?php 
							if($verifica_tipo_radicado == 1){
								echo "
									<td class='descripcion'>
										Ubicación física del documento
									</td>
									<td class='detalle' colspan='3'>
										$campo_ubicacion_fisica
									</td>
									<td class='descripcion'>
										Medio Respuesta Solicitado
									</td>
									<td class='detalle'>
										$medio_respuesta_solicitado
									</td>
									";
							}else{
								echo "
									<td class='descripcion'>
										Ubicación física del documento
									</td>
									<td class='detalle' colspan='5'>
										$campo_ubicacion_fisica 
									</td>";
							}
						?>	
						</tr>

					</table>
				</div>
<?php 
			/* Fin imprime tabla para la pestaña "Informacion General" cuando es radicado de entrada(1), de salida(2) ó interna (4) */
				}else{
			/* Imprime tabla para la pestaña "Informacion General" cuando es radicado normal(3) */
?>
					<table border="0" width='100%'>
						<tr>
							<td class="descripcion" width="20%">
								Fecha de radicado
							</td>
							<td class="detalle" width="30%">
								<?php echo "$fecha_radicado"; ?>
							</td>
							<td rowspan="5">
								<div id="imagen_principal">
									<?php echo "$path";?>
								</div>	
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Nivel de Seguridad del radicado
							</td>
							<td class="detalle">
								<?php echo "$nivel_seguridad"; ?>
							</td>
						</tr>	
						<tr>
							<td class="descripcion">
								Asunto
							</td>
							<td class="detalle">
								<?php echo "$asunto"; ?>
							</td>
						</tr>
						<tr>
							<td class="descripcion">
								Código de Serie
							</td>
							<td class="detalle">
								<?php echo "$codigo_serie1"; ?>
							</td>
						</tr>	
						<tr>
							<td class="descripcion">
								Código de Subserie
							</td>
							<td class="detalle" >
								<?php echo "$codigo_subserie1"; ?>
							</td>
						</tr>
					</table>
<?php						
				}
			/* Fin imprime tabla para la pestaña "Informacion General" cuando es radicado normal(3) */				
?>	
			</div>			
<?php 
	/* Fin si al verificar inventario el codigo de la dependencia es diferente a INV, INVE o INVEN despliega los datos capturados del radicado en el acordeon "Informacion General" */
		}
		break; // Fin del case "Informacion General"

/***************************************************************************************************/
/***************************************************************************************************/

	case 'historico':
		$expediente=$_POST['expediente'];

		if($expediente==""){
			$query_historico="select * from historico_eventos h join usuarios u on h.usuario=u.login join dependencias d on u.codigo_dependencia=d.codigo_dependencia where numero_radicado='$radicado' order by id desc"; 
		}else{
			$query_historico="select * from historico_eventos h join usuarios u on h.usuario=u.login join dependencias d on u.codigo_dependencia=d.codigo_dependencia where numero_radicado='$radicado' or numero_radicado='$expediente' order by id desc";
		}
		$fila_historico = pg_query($conectado,$query_historico);
		$registros_historico= pg_num_rows($fila_historico);
		echo "
		<center>
			<table border='0' width='100%'>
				<tr class='row' >
					<td class='descripcion' width='20%' >
						Transacción
					</td>
					<td class='descripcion' width='20%'>
						Usuario que realiza la transacción
					</td>
					<td class='descripcion' width='45%'>
						Comentario sobre la transacción
					</td>
					<td class='descripcion' width='15%'>
						Fecha de la transacción
					</td>
				</tr>";
			$tabla=""; // Inicializo la variable
			$num_fila=0; // Variable declarada para poner color a la fila según ésta variable.

			for ($i=0;$i<$registros_historico;$i++){
				$linea_historico = pg_fetch_array($fila_historico);

				// Defino las variables a mostrar
				$transaccion 		= $linea_historico['transaccion'];
				$usuario 			= $linea_historico['usuario'];
				$nombre_completo 	= $linea_historico['nombre_completo'];
				$nombre_dependencia = $linea_historico['nombre_dependencia'];
				$comentario 		= $linea_historico['comentario'];
				$fecha  			= $linea_historico['fecha'];

				$tabla="$tabla
				<tr class='fila1'>
					<td> $transaccion </td>
					<td> $nombre_completo - <u><b>$usuario</b></u><br> ($nombre_dependencia)</td> 
					<td> $comentario </td> 
					<td> $fecha </td> 
				</tr>	
				";								
			}
/* Aqui se imprimen los resultados en td en la tabla */;
			echo $tabla;
			$num_fila++; 		
		echo "</table>
		</center>";
		break; // Fin del case "Historico"

/***************************************************************************************************/
/***************************************************************************************************/
	case 'documentos_anexos':
  		$tiene_respuesta = "";

  		$consulta_entidad = substr($radicado,4,3); // Extrae el codigo de la entidad del numero_radicado directamente para diferenciar los radicados de Orfeo(anterior) 
		if(is_numeric($consulta_entidad)){ // Es de una migración Orfeo
			$consulta_rad 		= "select * from radicado where numero_radicado='$radicado'";

			$fila_rad 	  		= pg_query($conectado,$consulta_rad);
			$linea_rad    		= pg_fetch_array($fila_rad);

			$codigo_serie2 				= $linea_rad['codigo_serie'];
			$codigo_subserie2   		= $linea_rad['codigo_subserie'];
			$path_pdf   				= $linea_rad['path_radicado'];
			$numero_radicado 			= $radicado;
			$asunto   					= $linea_rad['asunto'];
			
			/* Se consulta si el radicado tiene respuesta para mostrarla */
			$query_respuesta 	= "select * from respuesta_radicados where radicado_padre='$radicado'";
			$fila_respuesta 	= pg_query($conectado,$query_respuesta);
	  		$num_rows_respuesta = pg_num_rows($fila_respuesta);

		  	if($num_rows_respuesta=='0'){
				$tiene_respuesta .= "<center>
						<div title='Dar respuesta al radicado $radicado' class='botones2' style='font-size: 15px; float: left; margin: 5px 10px; width:120px;' onclick='carga_radicacion_salida2(\"$radicado\", \"respuesta\")'>Responder</div>
						<div title='Este documento no requiere respuesta' class='botones2' style='font-size: 15px; float: left; margin: 5px 10px; width:150px;' onclick='carga_no_requiere_respuesta(\"$radicado\",\"$codigo_serie2\",\"$codigo_subserie2\",\"$codigo_dependencia\")'>No requiere respuesta</div>
					</center>";
		  	}
		  	echo "$consulta_rad";
		}else{ // Es de Jonas 
			/* Se consulta si es un radicado que tiene respuesta o es una respuesta, por eso se consulta en la tabla respuesta_radicados */
			$consulta_tabla_respuesta_radicados = "select * from respuesta_radicados where radicado_padre='$radicado' or radicado_respuesta='$radicado'";
			$fila_consulta_tabla_respuesta_radicados 		= pg_query($conectado,$consulta_tabla_respuesta_radicados);
	  		$num_rows_consulta_tabla_respuesta_radicados 	= pg_num_rows($fila_consulta_tabla_respuesta_radicados);

	  		/* Se define vacía la variable $tabla_radicado_respuesta que es lo que se muestra en el acordeon al lado izquierdo de la vista previa (tercera pestaña ) */
	  		$tabla_radicado_respuesta  	= "";
	  		$radicado_respuesta 		= $radicado;
		  	if($num_rows_consulta_tabla_respuesta_radicados=='0'){
		  		/*******************************************************************************************************/
		  		/* Este es el caso donde NO hay respuesta o el documento no es una respuesta */
		  		switch ($verifica_tipo_radicado) {
		  			case '1':		// Si es radicacion entrada o normal no requiere versionamiento
		  			case '3':
						$consulta_rad 				= "select * from radicado where numero_radicado='$radicado'";
						/* Ejecuta la query en este case para armar el path_principal */
						$fila_version_radicado2 	= pg_query($conectado,$consulta_rad);
						$linea_version_radicado2  	= pg_fetch_array($fila_version_radicado2);

						$path_radicado1 			= $linea_version_radicado2['path_radicado'];
						$codigo_serie2 				= $linea_version_radicado2['codigo_serie'];
						$codigo_subserie2 			= $linea_version_radicado2['codigo_subserie'];
						$asunto2 		 			= $linea_version_radicado2['asunto'];
						$id_expediente2  			= $linea_version_radicado2['id_expediente'];

						/* Se define el path_principal del documento */
						$path_principal  	= "style='cursor:pointer;' class='botones2' onclick='visualizar_principal(\"$radicado\",\"$path_radicado1\",\"radicados_1\")' title='Ver archivo principal'";

						/* Inicio imprimir todo del entrada y normal */
						$estado_radicado2 			= $linea_version_radicado2['estado_radicado'];

						if($estado_radicado2=='no_requiere_respuesta'){
							$tiene_respuesta.= "<center>
								<h3 style ='color : green;'>Este documento ha sido marcado como NO REQUIERE RESPUESTA por lo tanto no necesita trámites adicionales.</h3>
							</center>";
						}else{
							$tiene_respuesta .= "<center>
								<div title='Dar respuesta al radicado $radicado' class='botones2' style='font-size: 15px; margin: 5px 10px; width:230px;' onclick='validar_carga_radicacion_salida2(\"$radicado\", \"$codigo_dependencia\",\"$codigo_serie2\",\"$codigo_subserie2\",\"$id_expediente2\")'>Responder</div>
								<div title='Este documento no requiere respuesta' class='botones2' style='font-size: 15px; margin: 5px 10px; width:230px;' onclick='carga_no_requiere_respuesta(\"$radicado\",\"$codigo_serie2\",\"$codigo_subserie2\",\"$codigo_dependencia\")'>No requiere respuesta</div>
							</center>";
						}

						/* Ya que no tiene version, en su lugar se ponen los botones de tiene_respuesta */
						$tiene_version = $tiene_respuesta; 

						$ancho_asunto 	= "60%"; // Ancho del td (asunto) de la tercera pestaña

						/* Desde aqui consulto los adjuntos que tenga el radicado */
						$query_adjuntos 	= "select * from adjuntos where numero_radicado='$radicado' order by fecha_radicado desc";
						// $query_adjuntos		="select * from adjuntos where numero_radicado='$numero_radicado'";
						$fila_adjuntos 		= pg_query($conectado,$query_adjuntos);
						$cantidad_adjuntos  = pg_num_rows($fila_adjuntos);

						$tabla_adjuntos 	= "";

						if($cantidad_adjuntos!=0 ){
							for ($m=0;$m<$cantidad_adjuntos;$m++){
								$linea_adjuntos  = pg_fetch_array($fila_adjuntos);

								$fecha_radicado_a 	= $linea_adjuntos['fecha_radicado'];
								$asunto_a 			= $linea_adjuntos['asunto'];
								$asunto_b 			= substr($asunto_a, 0, 15)."..."; 

								$path_adjunto 		= $linea_adjuntos['path_adjunto'];

								$tabla_adjuntos.= "
										<div class='art_exp center' style='float:left;' onclick='visualizar_principal(\"$radicado\",\"$path_adjunto\",\"adjuntos_1\")'>
											<img height='20px' src='imagenes/iconos/archivo_pdf.png' title='Vista Previa' style='float:left'>
											<span title='$asunto_a' style='float:left;padding-left:5px;'>$asunto_b
										</div>";
							}
							$adjunto= "
							<tr class='detalle'>
								<td colspan='4'>$tabla_adjuntos</td>
							</tr>";
							// $rowspan = "rowspan='2'";
							$rowspan = "";
						}else{
							$rowspan = "";
							$tabla_adjuntos.="<h4 style='color:blue; padding:10px;''> El radicado todavía no tiene documentos anexos / adjuntos</h4>";
						}

						$tabla_radicado_respuesta.= "<tr class='detalle center'>
							<td $path_principal style='width:25%;'>$radicado</td>
							<td width='$ancho_asunto'>$asunto2</td>
							<td>
								<center>
								$tiene_respuesta
												
								<div title='Adjuntar Archivos a radicado $radicado' style=\"font-size: 15px; margin: 0px 10px 5px 10px; padding: 0px 30px 0px 30px; width:200px; height:50px;\" class='botones' onclick='cargar_adjunto(\"$radicado\",\"$asunto2\",\"pestana_documentos\")'>
									<img height='50px' src='imagenes/iconos/archivo_anexo.png' style='margin: 0px 0px -18px -20px' > 
									<span style='padding : 20px 0px 0px 0px;'>Anexar / Adjuntar Soportes</span>
								</div> 
								</center>
							</td>
						</tr>

						<tr>
							<td title='Archivos anexos / adjuntos al radicado respuesta $radicado' colspan='3'>
								$tabla_adjuntos
							</td>
						</tr>";

						echo "
						<div id='listado_documentos_anexos' style='float:left; width: 100%;'>
							<table border='0' style='float:left; width:100%;'>
								$tabla_radicado_respuesta
							</table>
						</div>			
						<div id='visor_pdf_pestana_documentos' class='hidden' style='float:left;'></div>
						";
		  				break;
		  			
		  			/* Este es el caso donde NO hay respuesta o el documento no es una respuesta pero es 2 o 5 */
		  			case '2': // Cuando es tipo de radicado de salida(2)
					case '5': // Cuando es tipo de radicado de resoluciones(5)
						$consulta_rad 	= "select * from radicado r join version_documentos v on r.numero_radicado=v.numero_radicado where r.numero_radicado='$radicado' order by version desc";

						/* Ejecuta la query en este case para armar el path_principal */
						$fila_version_radicado5 	= pg_query($conectado,$consulta_rad);
						$cantidad_version_radicado5 = pg_num_rows($fila_version_radicado5);

						/* Inicializa variable para llenar la lista de las versiones del radicado respuesta */
						$lista_versiones5 		 	= "";
						$ancho_asunto 				= "40%"; // Ancho del td (asunto) de la tercera pestaña

						/* Como este si tiene versiones, se recorre el resultado */
						if($cantidad_version_radicado5!=0){
							for ($p=0;$p<$cantidad_version_radicado5;$p++){
								$linea_version_radicado5  	= pg_fetch_array($fila_version_radicado5);

								if($p==0){
									$asunto2  					= $linea_version_radicado5['asunto'];
									$codigo_serie2 				= $linea_version_radicado5['codigo_serie'];
									$codigo_subserie2 			= $linea_version_radicado5['codigo_subserie'];
									$estado_radicado2 			= $linea_version_radicado5['estado_radicado'];
									$medio_solicitud_firmas1 	= $linea_version_radicado5['medio_solicitud_firmas'];
									$path_radicado1 			= $linea_version_radicado5['path_radicado'];
									$version_actual 			= $linea_version_radicado5['version'];
									$usuarios_control5 		 	= $linea_version_radicado5['usuarios_control'];
									$firmado5 		 			= $linea_version_radicado5['firmado'];

									/* Se define el path_principal del documento */
									$path_principal  	= "style='cursor:pointer;' class='botones2' onclick='visualizar_principal(\"$radicado\",\"$path_radicado1\",\"radicados_1\")' title='Ver archivo principal'";
								}

								$cargo_usuario_que_firma 	= $linea_version_radicado5['cargo_usuario_que_firma'];
								$fecha_modifica 			= $linea_version_radicado5['fecha_modifica'];
								$medio_solicitud_firmas5 	= $linea_version_radicado5['medio_solicitud_firmas'];
								$path_pdf 					= $linea_version_radicado5['path_pdf'];
								$usuario_modifica 			= $linea_version_radicado5['usuario_modifica'];
								$usuario_que_firma 			= $linea_version_radicado5['usuario_que_firma'];
								$version 					= $linea_version_radicado5['version'];

								/* Se arma el listado de versiones que aparece en el desplegable "Version del documento XXX */
								$lista_versiones5.= "
								<tr class='center'>
									<td class='detalle botones2' style='cursor:pointer;width:100px;' onclick='visualizar_principal(\"$radicado\",\"$path_pdf\",\"version\")' title='Ver documento'>$version</td>
									<td class='detalle'>$usuario_modifica</td>
									<td class='detalle'>$fecha_modifica</td>
								</tr>";
							/* Fin recorrido del resultado de $consulta_rad */	
							}

							/* Esta es la lista que aparece en el desplegable "Version del documento XXX " */
							$radicado_respuesta = $radicado;
							$tabla_versiones_respuesta= "
							<tr class='descripcion center'>
								<td>version</td>
								<td>usuario que modifica</td>
								<td>fecha de modificación</td>
							</tr>$lista_versiones5";

							$tiene_version  	= "<br>(Version $version_actual)<br>";
							
							/* verifica tipo de radicacion del documento */		
							if($verifica_tipo_radicado=='2'){
								$boton_carga_radicacion = "onclick='carga_radicacion_salida2(\"$radicado\")'";
							}

							if($verifica_tipo_radicado=='5'){
								$boton_carga_radicacion = "onclick='carga_radicacion_resoluciones(\"$radicado\")'";
							}

							/* Inician validaciones para mostrar opciones para la firma electrónica */
							if($medio_solicitud_firmas1=="fisico"){
								if($firmado5=="NO"){
									$validacion_firmado = "Falta cargar el PDF firmado y escaneado desde papel con la firma del usuario  &#13; $usuario_que_firma ($cargo_usuario_que_firma)&#13;";
									$estilo_firmado 	= "background-color:red;"; // Color del boton (Aprobar/Firmar/Modificar Documento)
									$class_firmado 		= "class='art_exp'"; 

									// if($aprobado=="NO"){
									// 	$validacion_firmado.="&#13; Falta que el usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Apruebe electrónicamente el documento";
									// }else{
									// 	$validacion_firmado.="&#13; &#13;El usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Ha aprobado electrónicamente el documento";
									// }
								}else{
									$validacion_firmado = "Documento impreso en físico, &#13; Firmado manualmente por el usuario $usuario_que_firma ($cargo_usuario_que_firma),&#13;luego Escaneado y cargado PDF firmado por $usuario_modifica";
									$estilo_firmado 	= "background-color:green;";
									$class_firmado 		= ""; 
								}
							}else{
								/* Esto es lo que muestra si es solicitado con firma electrónica. */
								if($firmado5=="NO"){
									$validacion_firmado = "Falta que el usuario  &#13; $usuario_que_firma  &#13; ($cargo_usuario_que_firma)&#13; firme electrónicamente el documento.";
									$estilo_firmado 	= "background-color:red;";
									$class_firmado 		= "class='art_exp'"; 
									// if($aprobado=="NO"){
									// 	$validacion_firmado.="&#13; &#13; Falta que el usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Apruebe electrónicamente el documento";
									// }else{
									// 	$validacion_firmado.="&#13; &#13;El usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Ha aprobado electrónicamente el documento";
									// }
								}else{
									$validacion_firmado = "Firmado aaaaa electrónicamente por el usuario $usuario_que_firma ($cargo_usuario_que_firma)";
									$estilo_firmado 	= "background-color:green;";
									$class_firmado 		= ""; 
								}
							}
							
					  		/* Esta opcion es para cuando el medio_solicitud de firmas es físico y no está marcado como firmado */
					  		$pos1 = strpos($usuarios_control5, $login);

					  		if($firmado5=='NO' and $medio_solicitud_firmas1=='fisico' and $pos1!==false){
								$id_expediente2 = $_POST['expediente'];

								// Extraigo cada uno de los expedientes	
								$usu2  				= explode(",", $id_expediente2);
								$id_expediente1 	= $usu2[0];

								$query_expediente_pdf_firmado 	= "select * from expedientes where id_expediente='$id_expediente1'";
								$fila_expediente_pdf_firmado 	= pg_query($conectado,$query_expediente_pdf_firmado);
								$linea_expediente_pdf_firmado  	= pg_fetch_array($fila_expediente_pdf_firmado);
								$nombre_expediente 				= $linea_expediente_pdf_firmado['nombre_expediente'];
								$version_siguiente 				= $version_actual+1; 

					  			$boton_subir_pdf_firmado = "<center>
									<div title='Cargar PDF FIRMADO en físico del documento' class='art_exp' style='background: #f5672f; color:#FFFFFF; font-size: 15px; font-weight:bold;' onclick='cargar_asociar_pdf_principal(\"$radicado\",\"$id_expediente1\",\"$nombre_expediente\",\"$version_siguiente\")'>Cargar PDF FIRMADO en físico del documento</div>
									</center>";
								
								$validacion_firmado = "";
								$estilo_firmado 	= "background-color:red;";
								$class_firmado 		= "class='art_exp'";

								$boton_aprobar_firmar_modificar = "<td style='padding:5px;'>
									<center>
									<div title='$validacion_firmado' class='art_exp' style=\"color: #FFFFFF; border-color:#FFFFFF; font-weight:bold; padding: 5px 25px 5px 25px; $estilo_firmado\" $class_firmado $boton_carga_radicacion >
										Aprobar / Firmar / Modificar Documento
									</div>
									$boton_subir_pdf_firmado
									</center>
								</td>";	

							
							/* Cierra opcion cuando el medio_solicitud de firmas es físico y no está marcado como firmado */
					  		}else{
					  			// En este caso NO se muestra el boton anaranjado
					  			$boton_subir_pdf_firmado ="";
				  				$boton_aprobar_firmar_modificar = "<td class='center background-color:green;' style='padding:5px;'>
									<div title='$validacion_firmado' class='art_exp' style=\"color: #FFFFFF; border-color:#FFFFFF; font-weight:bold; padding: 5px 25px 5px 25px; $estilo_firmado\" $class_firmado $boton_carga_radicacion >
										Aprobar / Firmar / Modificar Documento
									</div>
									$boton_subir_pdf_firmado
								</td>";	
					  // 			/* Se comenta porque hay que definir el procedimiento para terminar documentos correctamente */
					  // 			// if($verifica_tipo_radicado=='5'){
						 //  		// 	$boton_subir_pdf_firmado ="";
						 //  		// 	$boton_aprobar_firmar_modificar = "<td style='padding:5px;'><div title='Documento terminado' class='art_exp' style='color: #FFFFFF; background: green;'>Documento terminado correctamente </div></tr>";
					  // 			// }else{
						 //  			$boton_subir_pdf_firmado ="";
					  // 				$boton_aprobar_firmar_modificar = "<td style='padding:5px;'>
							// 		<div title='$validacion_firmado' class='art_exp' style=\"color: #FFFFFF; border-color:#FFFFFF; font-weight:bold; padding: 5px 25px 5px 25px; $estilo_firmado\" $class_firmado $boton_carga_radicacion >
							// 			Aprobar / Firmar / Modificar Documento
							// 		</div>
							// 		$boton_subir_pdf_firmado
							// 	</td>";	
					  		}
							$botones_responder_nrr ="<center>
							<div title='Verificar Versión de documento' style=\"font-size: 15px; margin: 0px 10px 5px 10px; padding: 0px 0px 0px 60px; width:200px; height:50px;\" class='botones' onclick='cargar_version_radicado()'>
									<img height='50px' src='imagenes/iconos/version_documento.png' style='margin: 0px 0px -18px -60px'> 
									<span style='padding : 20px 0px 0px 0px;'>Verificar versión del documento<span>
							</div></center>";

					  		/* Desde aqui consulto los adjuntos que tenga el radicado */
							$query_adjuntos 	= "select * from adjuntos where numero_radicado='$radicado' order by fecha_radicado desc";
							// $query_adjuntos		="select * from adjuntos where numero_radicado='$numero_radicado'";
							$fila_adjuntos 		= pg_query($conectado,$query_adjuntos);
							$cantidad_adjuntos  = pg_num_rows($fila_adjuntos);

							$tabla_adjuntos 	= "";

							if($cantidad_adjuntos!=0 ){
								for ($m=0;$m<$cantidad_adjuntos;$m++){
									$linea_adjuntos  = pg_fetch_array($fila_adjuntos);

									$fecha_radicado_a 	= $linea_adjuntos['fecha_radicado'];
									$asunto_a 			= $linea_adjuntos['asunto'];
									$asunto_b 			= substr($asunto_a, 0, 15)."..."; 

									$path_adjunto 		= $linea_adjuntos['path_adjunto'];

									$tabla_adjuntos.= "
											<div class='art_exp center' style='float:left;' onclick='visualizar_principal(\"$radicado\",\"$path_adjunto\",\"adjuntos_1\")'>
												<img height='20px' src='imagenes/iconos/archivo_pdf.png' title='Vista Previa' style='float:left'>
												<span title='$asunto_a' style='float:left;padding-left:5px;'>$asunto_b
											</div>";
								}
								
								// $rowspan = "rowspan='2'";
								$rowspan = "";
							}else{
								$rowspan = "";
								$tabla_adjuntos.="<h4 style='color:blue; padding:10px;''> El radicado todavía no tiene documentos anexos / adjuntos</h4>";
							}

					  		/* Ahora se arma lo que se va a mostrar en la tercera pestaña */	
							$tabla_radicado_respuesta.= "<tr class='detalle center'>
								<td $path_principal style='width:25%;'>$radicado $tiene_version</td>
								<td rowspan='2' width='$ancho_asunto'>$asunto2 </td>
								<td rowspan='2'>
									<center>
									$botones_responder_nrr				
									<div title='Adjuntar Archivos a radicado $radicado' style=\"font-size: 15px; margin: 0px 10px 5px 10px; padding: 0px 30px 0px 30px; width:200px; height:50px;\" class='botones' onclick='cargar_adjunto(\"$radicado\",\"$asunto2\",\"pestana_documentos\")'>
										<img height='50px' src='imagenes/iconos/archivo_anexo.png' style='margin: 0px 0px -18px -20px' > 
										<span style='padding : 20px 0px 0px 0px;'>Anexar / Adjuntar Soportes</span>
									</div> 
									</center>
								</td>
							</tr>
							<tr>
								$boton_aprobar_firmar_modificar
							</tr>
							<tr>
								<td title='Archivos anexos / adjuntos al radicado respuesta $radicado' colspan='3'>
									$tabla_adjuntos
								</td>
							</tr>";
				
					  		echo "
							<div id='listado_documentos_anexos' style='float:left; width: 100%;'>
								<table border='0' style='float:left; width:100%;'>
									$tabla_radicado_respuesta
								</table>
							</div>			
							<div id='visor_pdf_pestana_documentos' class='hidden' style='float:left;'></div>

							<div id='ventana_ver_versiones_documento' class='ventana_modal'>
								<div class='form'>
								<div class='cerrar'><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
								<h1 class='center' id='titulo_version_documento'>Versión del documento $radicado_respuesta</h1>
									<hr>
									<center>
										<div id='tabla_listado_version_documento' border='0' style='float:left; max-height:700px; width:100%; overflow:auto;'>
											<table >
												$tabla_versiones_respuesta									
											</table>
										</div>
										<div id='visor_version_documentos' class='hidden' style='float:left; height:700px; width:45%;'></div>
									</center>
								</form>
								<center>
									<div id='boton_trd_radicado'>
											
									</div>
								</center>
								</div>				
							</div>";
				  		}else{
				  			echo "No hay resultado en la query consulta_rad";
				  		}
						break;
					
		  				  			
		  			default:		
						$consulta_rad = "select * from radicado r join version_documentos v on r.numero_radicado=v.numero_radicado where r.numero_radicado='$radicado' and html_asunto!='' order by version desc";

						/* Ejecuta la query en este case para armar la tabla de versiones */
						$fila_version_radicado1 		= pg_query($conectado,$consulta_rad);
						$cantidad_version_radicado1 	= pg_num_rows($fila_version_radicado1);

						/* Inicializa variable para llenar la lista de las versiones del radicado respuesta */
						$lista_versiones_radicado1 	= "";

						/* Ciclo que recorre el resultado de la consulta de versiones y datos del radicado. */
						for ($p=0;$p<$cantidad_version_radicado1;$p++){
							$linea_version_documento  = pg_fetch_array($fila_version_radicado1);
							/* Ya que está ordenado descendente, De la primera linea es la versión más reciente */
							echo "--->$p <br>";
							if($p==0){
								$codigo_serie2 				= $linea_version_documento['codigo_serie'];
								$codigo_subserie2   		= $linea_version_documento['codigo_subserie'];
								$firmado1 		 			= $linea_version_documento['firmado']; 
								$id_expediente 		  		= $linea_version_documento['id_expediente'];
								$medio_solicitud_firmas1 	= $linea_version_documento['medio_solicitud_firmas'];
								$path_radicado2 		 	= $linea_version_documento['path_radicado'];
								$version_actual1 		 	= $linea_version_documento['medio_solicitud_firmas'];

								$tiene_version  			= "<br>(Version $version_actual1)<br>";
							}

							$fecha_modifica1 	= $linea_version_documento['fecha_modifica'];
							$path_radicado1 	= $linea_version_documento['path_radicado'];
							$usuario_modifica1	= $linea_version_documento['usuario_modifica'];
							$version1 			= $linea_version_documento['version'];

							/* Se arma el listado de versiones que aparece en el desplegable "Version del documento XXX */
							$lista_versiones_radicado1.= "
							<tr class='center'>
								<td class='detalle botones2' style='cursor:pointer;width:100px;' onclick='visualizar_principal(\"$radicado\",\"$path_radicado2\",\"version\")' title='Ver documento'>$version1</td>
								<td class='detalle'>$usuario_modifica1</td>
								<td class='detalle'>$fecha_modifica1</td>
							</tr>";
						}	
						$tabla_versiones_respuesta= "
							<tr class='descripcion center'>
								<td>version</td>
								<td>usuario que modifica</td>
								<td>fecha de modificación</td>
							</tr>$lista_versiones_radicado1";
		
		  				break;
		  		}

				$fila_radicado1 		= pg_query($conectado,$consulta_rad);
				$linea_radicado1 		= pg_fetch_array($fila_radicado1);

				$asunto_radicado1 	 	= $linea_radicado1['asunto'];
				$path_radicado1 		= $linea_radicado1['path_radicado'];
				$fecha_radicado1 		= $linea_radicado1['fecha_radicado'];
				$usuarios_control1 		= $linea_radicado1['usuarios_control'];



				$ancho_asunto 			= "60%";

	 // kkkkk

				switch ($verifica_tipo_radicado) {
					case '1': // Cuando el radicado es de Entrada(1)
					case '3': // Cuando el radicado es Normal(3)
						break;

					case '4': // Cuando es tipo de radicado interno (4)
						// $ancho_asunto 			= "40%"; // Ancho del td (asunto) de la tercera pestaña

						// if($fecha_radicado==0){
						// 	$fecha_radicado="<h4 style='color:red;'>No se ha asignado fecha de radicación porque se ha generado hasta ahora la plantilla del radicado pero faltan las firmas electrónicas de aprobación y de revisión correspondientes.</h4>";
						// }
						// /* Desde aqui se consulta la version del radicado */
						// $query_version_documento	= "select * from version_documentos where numero_radicado='$numero_radicado' order by version desc";
						// $fila_version_documento		= pg_query($conectado,$query_version_documento);
						// $cantidad_version_documento = pg_num_rows($fila_version_documento);

						// $lista_versiones 	= "";
						// $validacion_firmado = "";
						// $estilo_firmado  	= "";
						// $class_firmado  	= "";
						// $path_principal  	= "";
						// $tiene_version  	= "";

						// if($cantidad_version_documento!=0){

						// 	for ($p=0;$p<$cantidad_version_documento;$p++){
						// 		$linea_version_documento  = pg_fetch_array($fila_version_documento);

						// 		if($p==0){
						// 			$version_actual = $linea_version_documento['version'];
						// 			$path_pdf 		= $linea_version_documento['path_pdf'];
						// 			$path_principal = "style='cursor:pointer; font-size:15px; padding:5px;' class='botones2' onclick='visualizar_principal(\"$radicado\",\"$path_pdf\",\"radicados\")' title='Ver archivo principal'";	
						// 		}

						// 		$version 					= $linea_version_documento['version'];
						// 		$path_pdf 					= $linea_version_documento['path_pdf'];
						// 		$usuario_modifica 			= $linea_version_documento['usuario_modifica'];
						// 		$fecha_modifica 			= $linea_version_documento['fecha_modifica'];
						// 		$usuario_que_firma 			= $linea_version_documento['usuario_que_firma'];
						// 		$cargo_usuario_que_firma 	= $linea_version_documento['cargo_usuario_que_firma'];
						// 		$firmado 					= $linea_version_documento['firmado'];
						// 		$usuario_que_aprueba 		= $linea_version_documento['usuario_que_aprueba'];
						// 		$cargo_usuario_que_aprueba 	= $linea_version_documento['cargo_usuario_que_aprueba'];
						// 		$aprobado 					= $linea_version_documento['aprobado'];

						// 		if($firmado=="NO"){
						// 			$validacion_firmado = "Falta que el usuario  &#13; $usuario_que_firma  &#13; ($cargo_usuario_que_firma)&#13; firme electrónicamente el documento.";
						// 			$estilo_firmado 	= "background-color:red;";
						// 			$class_firmado 		= "class='art_exp'"; 
						// 			if($aprobado=="NO"){
						// 				$validacion_firmado.="&#13; &#13; Falta que el usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Apruebe electrónicamente el documento";
						// 			}else{
						// 				$validacion_firmado.="&#13; &#13;El usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Ha aprobado electrónicamente el documento";
						// 			}
						// 		}else{
						// 			$validacion_firmado = "Firmado electrónicamente por el usuario $usuario_que_firma ($cargo_usuario_que_firma)";
						// 			$estilo_firmado 	= "background-color:green;";
						// 			$class_firmado 		= ""; 
						// 		}

						// 		$lista_versiones.= "
						// 			<tr class='center'>
						// 				<td class='detalle botones2' style='cursor:pointer;' onclick='visualizar_principal(\"$radicado\",\"$path_pdf\",\"version\")' title='Ver documento'>$version</td>
						// 				<td class='detalle'>$usuario_modifica</td>
						// 				<td class='detalle'>$fecha_modifica</td>
						// 			</tr>
						// 		";
						// 	}

						// 	$tiene_version  	= "<br>(Version $version_actual)<br>";

						// 	$tabla_versiones= "
						// 	<tr class='descripcion center'>
						// 		<td>Version</td>
						// 		<td>Usuario que Modifica</td>
						// 		<td>Fecha de Modificación</td>
						// 	</tr>$lista_versiones";
						// }else{
						// 	$tabla_versiones = "";
						// }
						// /* Hasta aqui se consulta las versiones que tenga el radicado */
									
						// $boton_aprobar_firmar_modificar = "<td style='padding:5px;'>
						// 	<div title='$validacion_firmado' class='art_exp' style=\"color: #FFFFFF; border-color:#FFFFFF; font-weight:bold; margin: 0px 5px 5px 5px; padding: 5px 25px 5px 25px; $estilo_firmado\" $class_firmado onclick='carga_radicacion_interna(\"$numero_radicado\")'>
						// 		1 Aprobar / Firmar / Modificar Documento
						// 	</div>
						// </td>";	
						// $botones_responder_nrr ="
						// <div title='Verificar Versión de documento' style=\"font-size: 15px; margin: 0px 10px 5px 10px; padding: 0px 30px 0px 30px; float: left; width:300px; height:50px;\" class='botones' onclick='cargar_version_radicado()'>
						// 		<img height='50px' src='imagenes/iconos/version_documento.png' style='margin: 0px 0px -18px -60px'> 
						// 		<span style='padding : 20px 0px 0px 0px;'>Verificar versión del documento<span>
						// </div>";			
						break;	
				}
				/* Hasta aqui defino si el radicado es entrada, salida, normal o interno */

				/* Desde aqui consulto los adjuntos que tenga el radicado */
				$query_adjuntos 	= "select * from adjuntos where numero_radicado='$radicado' order by fecha_radicado desc";
				// $query_adjuntos		="select * from adjuntos where numero_radicado='$numero_radicado'";
				$fila_adjuntos 		= pg_query($conectado,$query_adjuntos);
				$cantidad_adjuntos  = pg_num_rows($fila_adjuntos);

				$tabla_adjuntos 	= "";

				if($cantidad_adjuntos!=0 ){
					for ($m=0;$m<$cantidad_adjuntos;$m++){
						$linea_adjuntos  = pg_fetch_array($fila_adjuntos);

						$fecha_radicado_a 	= $linea_adjuntos['fecha_radicado'];
						$asunto_a 			= $linea_adjuntos['asunto'];
						$asunto_b 			= substr($asunto_a, 0, 15)."..."; 

						$path_adjunto 		= $linea_adjuntos['path_adjunto'];

						$tabla_adjuntos.= "
								<div class='art_exp center' style='float:left;' onclick='visualizar_principal(\"$radicado\",\"$path_adjunto\",\"adjuntos_1\")'>
									<img height='20px' src='imagenes/iconos/archivo_pdf.png' title='Vista Previa' style='float:left'>
									<span title='$asunto_a' style='float:left;padding-left:5px;'>$asunto_b
								</div>	
						";
					}
					$adjunto= "
					<tr class='detalle'>
						<td colspan='4'>$tabla_adjuntos</td>
					</tr>";
					// $rowspan = "rowspan='2'";
					$rowspan = "";
				}else{
					$rowspan = "";
					$tabla_adjuntos.="<h4 style='color:blue; padding:10px;''>El radicado todavía no tiene documentos anexos / adjuntos</h4>";
				}

		  		/* Si es un radicado de inventario o si es un radicado normal */
				if($verifica_inventario1=='INV' || $verifica_inventario2=='INVE' || $verifica_inventario3=='INVEN' || $verifica_tipo_radicado=='3'){
				}else{
					// $botones_responder_nrr 			= $tiene_respuesta;
				}

			/* Fin del caso donde no hay respuesta todavía */
		  	/*******************************************************************************************************/
		  	}else{
		  		/* Inicia el caso cuando el documento SI tiene respuesta */
		  		/* Aqui inicia armando la tabla del radicado respuesta que es el primero en aparecer. */
		  		$linea_resp_radicado = pg_fetch_array($fila_consulta_tabla_respuesta_radicados);
		  		$radicado_padre1 		= $linea_resp_radicado['radicado_padre'];
		  		$radicado_respuesta1 	= $linea_resp_radicado['radicado_respuesta'];

		  		$ancho_asunto 			= "40%"; // Ancho del td (asunto) de la tercera pestaña (De la respuesta )

		  		/* Se muestra primero el radicado respuesta. Por eso se arma la tabla así */
				/* Desde aqui se consulta la version del radicado de respuesta join radicado */
				$query_version_documento_respuesta	= "select version::integer , path_pdf, usuario_modifica, fecha_modifica, usuario_que_firma, cargo_usuario_que_firma, firmado, usuario_que_aprueba, cargo_usuario_que_aprueba, aprobado, asunto, medio_solicitud_firmas, id_expediente, usuarios_control, codigo_serie, codigo_subserie from version_documentos v join radicado r on v.numero_radicado=r.numero_radicado where v.numero_radicado='$radicado_respuesta1' order by version desc";

				$fila_version_documento_respuesta		= pg_query($conectado,$query_version_documento_respuesta);
				$cantidad_version_documento_respuesta 	= pg_num_rows($fila_version_documento_respuesta);

				/* Inicializa variable para llenar la lista de las versiones del radicado respuesta */
				$lista_versiones_respuesta 	= "";

				/* Ciclo que recorre el resultado de la consulta de versiones y datos del radicado. */
				for ($p=0;$p<$cantidad_version_documento_respuesta;$p++){
					$linea_version_documento  = pg_fetch_array($fila_version_documento_respuesta);

					$path_pdf 		= $linea_version_documento['path_pdf'];

					/* Ya que está ordenado descendente, De la primera linea es la versión más reciente */
					if($p==0){
						$asunto 					= $linea_version_documento['asunto'];
						$cargo_usuario_que_firma 	= $linea_version_documento['cargo_usuario_que_firma'];
						$codigo_serie2 				= $linea_version_documento['codigo_serie'];
						$codigo_subserie2 			= $linea_version_documento['codigo_subserie'];
						$firmado_version 			= $linea_version_documento['firmado'];
						$id_expediente 				= $linea_version_documento['id_expediente'];
						$medio_solicitud_firmas 	= $linea_version_documento['medio_solicitud_firmas'];
						$usuario_que_firma 			= $linea_version_documento['usuario_que_firma'];
						$usuarios_control 			= $linea_version_documento['usuarios_control'];
						$version_actual 			= $linea_version_documento['version'];

						$path_principal = "style='cursor:pointer;' class='botones2' onclick='visualizar_principal(\"$radicado_respuesta1\",\"$path_pdf\",\"radicados\")' title='Ver archivo respuesta principal'";	
					}
					/* Variables para armar la tabla del desplegable "Versión del documento" */
					$fecha_modifica 			= $linea_version_documento['fecha_modifica'];
					$usuario_modifica 			= $linea_version_documento['usuario_modifica'];
					$version 					= $linea_version_documento['version'];

					// $aprobado 					= $linea_version_documento['aprobado'];
					// $cargo_usuario_que_aprueba 	= $linea_version_documento['cargo_usuario_que_aprueba'];
					// $usuario_que_aprueba 		= $linea_version_documento['usuario_que_aprueba'];

					/* Inician validaciones para firma electrónica */
					if($medio_solicitud_firmas=="fisico"){
						if($firmado_version=="NO"){
							$validacion_firmado = "Falta cargar el PDF firmado y escaneado desde papel con la firma del usuario  &#13; $usuario_que_firma ($cargo_usuario_que_firma)&#13;";
							$estilo_firmado 	= "background-color:red;"; // Color del boton (Aprobar/Firmar/Modificar Documento)
							$class_firmado 		= "class='art_exp'"; 

							// if($aprobado=="NO"){
							// 	$validacion_firmado.="&#13; Falta que el usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Apruebe electrónicamente el documento";
							// }else{
							// 	$validacion_firmado.="&#13; &#13;El usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Ha aprobado electrónicamente el documento";
							// }
						}else{
							$validacion_firmado = "Documento impreso en físico, &#13; Firmado manualmente por el usuario $usuario_que_firma ($cargo_usuario_que_firma),&#13;luego Escaneado y cargado PDF firmado por $usuario_modifica";
							$estilo_firmado 	= "background-color:green;";
							$class_firmado 		= ""; 
						}
					}else{
						/* Esto es lo que muestra si es solicitado con firma electrónica. */
						if($firmado_version=="NO"){
							$validacion_firmado = "Falta que el usuario  &#13; $usuario_que_firma  &#13; ($cargo_usuario_que_firma)&#13; firme electrónicamente el documento.";
							$estilo_firmado 	= "background-color:red;";
							$class_firmado 		= "class='art_exp'"; 
							// if($aprobado=="NO"){
							// 	$validacion_firmado.="&#13; &#13; Falta que el usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Apruebe electrónicamente el documento";
							// }else{
							// 	$validacion_firmado.="&#13; &#13;El usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Ha aprobado electrónicamente el documento";
							// }
						}else{
							$validacion_firmado = "Firmado aaaaa electrónicamente por el usuario $usuario_que_firma ($cargo_usuario_que_firma)";
							$estilo_firmado 	= "background-color:green;";
							$class_firmado 		= ""; 
						}
					}

					/* Se arma el listado de versiones que aparece en el desplegable "Version del documento XXX */
					$lista_versiones_respuesta.= "
						<tr class='center'>
							<td class='detalle botones2' style='cursor:pointer;width:100px;' onclick='visualizar_principal(\"$radicado_respuesta1\",\"$path_pdf\",\"version\")' title='Ver documento'>$version</td>
							<td class='detalle'>$usuario_modifica</td>
							<td class='detalle'>$fecha_modifica</td>
						</tr>";
				}

				/* Esta es la lista que aparece en el desplegable "Version del documento XXX " */
				$radicado_respuesta = $radicado_respuesta1;
				$tabla_versiones_respuesta= "
				<tr class='descripcion center'>
					<td>version</td>
					<td>usuario que modifica</td>
					<td>fecha de modificación</td>
				</tr>$lista_versiones_respuesta";

				/* Verifica tipo de radicacion del documento */		
				$verifica_tipo_radicado5 = substr($radicado_respuesta1,-1); // Extrae el ultimo digito del numero_radicado directamente para determinar si es una entrada (1), salida(2), inventario(3) o interno(4)

				switch ($verifica_tipo_radicado5) {
					case '2':
						$boton_carga_radicacion = "onclick='carga_radicacion_salida2(\"$radicado_respuesta1\")'";
						break;
					case '5':
						$boton_carga_radicacion = "onclick='carga_radicacion_resoluciones(\"$radicado_respuesta1\")'";
						break;	
					
					default:
						$boton_carga_radicacion = "";
						break;
				}
				$tiene_version  = "<br>(Version $version_actual)<br>";

		  		/* Esta opcion es para cuando el medio_solicitud de firmas es físico y no está marcado como firmado */
		  		$pos = strpos($usuarios_control, $login);

				/* Condiciones para mostrar el boton anaranjado en la respuesta */
				if($firmado_version=='NO' and $medio_solicitud_firmas=='fisico' and $pos!==false){
					/* En este caso si se muestra el boton anaranjado */

					// Extraigo cada uno de los usuarios_visor para enviar variables en la funcion (cargar_asociar_pdf_principal)
					$usu2  				= explode(",", $id_expediente);
					$id_expediente1 	= $usu2[0];

					$query_expediente_pdf_firmado 	= "select * from expedientes where id_expediente='$id_expediente1'";
					$fila_expediente_pdf_firmado 	= pg_query($conectado,$query_expediente_pdf_firmado);
					$linea_expediente_pdf_firmado  	= pg_fetch_array($fila_expediente_pdf_firmado);
					$nombre_expediente 				= $linea_expediente_pdf_firmado['nombre_expediente'];
					$version_siguiente 				= $version_actual+1; 

		  			$boton_subir_pdf_firmado = "<center>
						<div title='Cargar PDF FIRMADO en físico del documento' class='art_exp' style='background: #f5672f; color:#FFFFFF; font-size: 15px; font-weight:bold;' onclick='cargar_asociar_pdf_principal(\"$radicado_respuesta1\",\"$id_expediente\",\"$nombre_expediente\",\"$version_siguiente\")'>Cargar PDF FIRMADO en físico del documento</div>
						</center>";
					
					$boton_aprobar_firmar_modificar = "<td class='center estilo_respuesta' style='padding:5px;'>
						<div title='$validacion_firmado' class='art_exp' style=\"color: #FFFFFF; border-color:#FFFFFF; font-weight:bold; padding: 5px 25px 5px 25px; $estilo_firmado\" $class_firmado $boton_carga_radicacion >
							Aprobar / Firmar / Modificar Documento
						</div>
						$boton_subir_pdf_firmado
					</td>";	
		  		}else{
					// En este caso NO se muestra el boton anaranjado
		  			$boton_subir_pdf_firmado ="";
	  				$boton_aprobar_firmar_modificar = "<td class='center estilo_respuesta' style='padding:5px;'>
						<div title='$validacion_firmado' class='art_exp' style=\"color: #FFFFFF; border-color:#FFFFFF; font-weight:bold; padding: 5px 25px 5px 25px; $estilo_firmado\" $class_firmado $boton_carga_radicacion >
							Aprobar / Firmar / Modificar Documento
						</div>
						$boton_subir_pdf_firmado
					</td>";	
		  		}
		  		/* Desde aqui consulto los adjuntos que tenga el radicado de respuesta */
				$query_adjuntos_rad_respuesta	= "select * from adjuntos where numero_radicado='$radicado_respuesta1' order by fecha_radicado desc";
				$fila_adjuntos_rad_respuesta 		= pg_query($conectado,$query_adjuntos_rad_respuesta);
				$cantidad_adjuntos_rad_respuesta 	= pg_num_rows($fila_adjuntos_rad_respuesta);
		  		
				$tabla_adjuntos = "";
				// if($cantidad_adjuntos_rad_respuesta!=0 or $tabla_adjuntos!=''){
				if($cantidad_adjuntos_rad_respuesta!=0){
					for ($m=0;$m<$cantidad_adjuntos_rad_respuesta;$m++){
						$linea_adjuntos_rad_respuesta  = pg_fetch_array($fila_adjuntos_rad_respuesta);

						$fecha_radicado_a 	= $linea_adjuntos_rad_respuesta['fecha_radicado'];
						$asunto_a 			= $linea_adjuntos_rad_respuesta['asunto'];
						$asunto_b 			= substr($asunto_a, 0, 15)."..."; 

						$path_adjunto 		= $linea_adjuntos_rad_respuesta['path_adjunto'];

						$tabla_adjuntos.= "
								<div class='art_exp center' style='float:left; background-color:#FFFFFF; margin-left:2px;' onclick='visualizar_principal(\"$radicado\",\"$path_adjunto\",\"adjuntos\")'>
									<img height='20px' src='imagenes/iconos/archivo_pdf.png' title='Vista Previa' style='float:left'>
									<span title='$asunto_a' style='float:left;padding-left:5px;'>$asunto_b
								</div>";
					}
				}else{
					$rowspan = "";
					$tabla_adjuntos.="<h4 style='color:blue; padding:10px;'>El radicado todavía no tiene documentos anexos / adjuntos</h4>";
				}

		  		$tabla_radicado_respuesta.= "<tr class='detalle center'>
					<td $path_principal style='width:25%;'>$radicado_respuesta1 $tiene_version</td>
					<td class='estilo_respuesta' rowspan='2' width='$ancho_asunto'>$asunto</td>
					<td class='estilo_respuesta' rowspan='2'>
						<div title='Verificar Versión de documento' style=\"font-size: 15px; margin: 0px 10px 5px 10px; padding: 0px 0px 0px 60px; float: left; width:200px; height:50px;\" class='botones' onclick='cargar_version_radicado()'>
								<img height='50px' src='imagenes/iconos/version_documento.png' style='margin: 0px 0px -18px -60px'> 
								<span style='padding : 20px 0px 0px 0px;'>Verificar versión del documento<span>
						</div>					
						<center>
						<div title='Adjuntar Archivos a radicado $radicado_respuesta1' style=\"font-size: 15px; margin: 0px 10px 5px 10px; padding: 0px 30px 0px 30px; float: left; width:200px; height:50px;\" class='botones' onclick='cargar_adjunto(\"$radicado_respuesta1\",\"$asunto\",\"pestana_documentos\")'>
							<img height='50px' src='imagenes/iconos/archivo_anexo.png' style='margin: 0px 0px -18px -20px' > 
							<span style='padding : 20px 0px 0px 0px;'>Anexar / Adjuntar Soportes</span>
						</div> 
						</center>
					</td>
				</tr>
				<tr>
					$boton_aprobar_firmar_modificar
				</tr>
				<tr>
					<td class='estilo_respuesta' title='Archivos anexos / adjuntos al radicado respuesta $radicado_respuesta1' colspan='3'>
						$tabla_adjuntos
					</td>
				</tr>";
				/**********************************************************************************/
		  		/* Aqui inicia armando la tabla del radicado padre que es el segundo en aparecer. */
				$query_version_documento_padre	= "select * from radicado r where numero_radicado='$radicado_padre1'";

				// echo "$query_version_documento_padre <br>";
				$fila_version_documento_padre		= pg_query($conectado,$query_version_documento_padre);
				$cantidad_version_documento_padre 	= pg_num_rows($fila_version_documento_padre);

				$linea_version_documento_padre  	= pg_fetch_array($fila_version_documento_padre);

				$path_pdf_padre						= $linea_version_documento_padre['path_radicado'];
				$asunto_padre  						= $linea_version_documento_padre['asunto'];

				$path_principal_padre = "style='cursor:pointer;' class='botones2' onclick='visualizar_principal(\"$radicado_padre1\",\"$path_pdf_padre\",\"radicados\")' title='Ver archivo padre principal'";	
					
				/* Desde aqui consulto los adjuntos que tenga el radicado padre */
				$query_adjuntos_rad_padre	= "select * from adjuntos where numero_radicado='$radicado_padre1' order by fecha_radicado desc";
				$fila_adjuntos_rad_padre 		= pg_query($conectado,$query_adjuntos_rad_padre);
				$cantidad_adjuntos_rad_padre 	= pg_num_rows($fila_adjuntos_rad_padre);

				$tabla_adjuntos_padre = "";

				// if($cantidad_adjuntos_rad_respuesta!=0 or $tabla_adjuntos_padre!=''){
				if($cantidad_adjuntos_rad_padre!=0){
					for ($m=0;$m<$cantidad_adjuntos_rad_padre;$m++){
						$linea_adjuntos_rad_padre  = pg_fetch_array($fila_adjuntos_rad_padre);

						$fecha_radicado_a 	= $linea_adjuntos_rad_padre['fecha_radicado'];
						$asunto_a 			= $linea_adjuntos_rad_padre['asunto'];
						$asunto_b 			= substr($asunto_a, 0, 15)."..."; 

						$path_adjunto_padre	= $linea_adjuntos_rad_padre['path_adjunto'];

						$tabla_adjuntos_padre.= "
								<div class='art_exp center' style='float:left;' onclick='visualizar_principal(\"$radicado_padre1\",\"$path_adjunto_padre\",\"adjuntos\")'>
									<img height='20px' src='imagenes/iconos/archivo_pdf.png' title='Vista Previa' style='float:left'>
									<span title='$asunto_a' style='float:left;padding-left:5px;'>$asunto_b
								</div>";
					}
				}else{
					$rowspan = "";
					$tabla_adjuntos_padre.="<h4 style='color:blue; padding:10px;'>El radicado todavía no tiene documentos anexos / adjuntos</h4>";
				}
								
				$tabla_radicado_respuesta.= "</table>
				<table border='0' width='100%'>
					<tr class='detalle center'>
						<td rowspan='2' style='background-color: blue; width:20px; color: #FFFFFF;color: #FFFFFF; writing-mode: vertical-lr; transform: rotate(180deg); font-weight: bold; font-size: 18px;'>PADRE</td>
						<td $path_principal_padre >$radicado_padre1 </td>
						<td style='padding : 10px;'>$asunto_padre</td>
						<td>				
							<center>
							<div title='Adjuntar Archivos a radicado $radicado_padre1' style=\"font-size: 15px; margin: 0px 10px 5px 10px; padding: 0px 30px 0px 30px; float: left; width:200px; height:50px;\" class='botones' onclick='cargar_adjunto(\"$radicado_padre1\",\"$asunto_padre\",\"pestana_documentos\")'>
								<img height='50px' src='imagenes/iconos/archivo_anexo.png' style='margin: 0px 0px -18px -20px' > 
								<span style='padding : 20px 0px 0px 0px;'>Anexar / Adjuntar Soportes</span>
							</div> 
							</center>
						</td>
					</tr>
					<tr>
						<td class='detalle' colspan='3'>
							$tabla_adjuntos_padre
						</td>
					</tr>";

				/* Se imprime toda la tercera pestaña "Documentos Anexos / Adjuntos / Respuesta de los que tienen respuesta */
				echo "
				<div id='listado_documentos_anexos' style='float:left; width: 100%;'>
					<table border='0' style='float:left; width:100%;'>
						$tabla_radicado_respuesta
					</table>
				</div>			

				<div id='visor_pdf_pestana_documentos' class='hidden' style='float:left;'></div>
				<div id='ventana_ver_versiones_documento' class='ventana_modal'>
					<div class='form'>
					<div class='cerrar'><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
					<h1 class='center' id='titulo_version_documento'>Versión del documento $radicado_respuesta</h1>
						<hr>
						<center>
							<div id='tabla_listado_version_documento' border='0' style='float:left; max-height:700px; width:100%; overflow:auto;'>
								<table >
									$tabla_versiones_respuesta									
								</table>
							</div>
							<div id='visor_version_documentos' class='hidden' style='float:left; height:700px; width:45%;'></div>
						</center>
					</form>
					<center>
						<div id='boton_trd_radicado'>
								
						</div>
					</center>
					</div>				
				</div>
				";
			/* Fin del caso cuando el documento SI tiene respuesta */
		  	}	

		/* Fin del caso cuando SI es un Jonas */
		}	
		
		break; 	// Fin del case "documentos_anexos"

/***************************************************************************************************/
/***************************************************************************************************/

	case 'expedientes':
		$expediente = $_POST['expediente'];

		if($expediente==""){
			$contenido = "<h2>Este documento no tiene expediente todavía.</h2><div class='art_exp center'  onclick='cargar_expediente_nuevo(\"$codigo_dependencia\")'><h3>Incluir en un expediente</h3></div>";
			$botones_expedientes ="";
		}else{
			$contenido = "<b>ESTE DOCUMENTO SE ENCUENTRA INCLUIDO EN EL(LOS) SIGUIENTE(S) EXPEDIENTE(S).</b>";

			$exp  = explode(",", $expediente);

			$max  = sizeof($exp);
			$max2 = $max-1;

			$nombre_expediente="";
			
			if($max2==0){
				$num_exp = $exp[0];
				$consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
				$fila_exp 	  = pg_query($conectado,$consulta_exp);
				$linea_exp    = pg_fetch_array($fila_exp);
				$nombre_exp   = $linea_exp['nombre_expediente'];

			/* Se define la ubicación topográfica */
				$ubicacion_topografica2=validar_ubicacion($num_exp);
				
				if($ubicacion_topografica2==false){
					if($ubicacion_topografica=='SI'){
						$onclick_ubicacion_topografica = "onclick='carga_ubicacion_topografica()' title='Ésta es la ubicación física del expediente. Click aquí para ir al módulo para cargar ó modificar la ubicación topográfica de éste expediente'";
					}else{
						$onclick_ubicacion_topografica = "title='Usted no tiene permisos para cargar la ubicación topográfica de éste expediente. Comuníquese con el administrador del sistema.'";
					}
					$ubicacion_topografica3="Expediente no se ha ubicado topográficamente todavía";
				}else{
					$onclick_ubicacion_topografica = "onclick='menu_prestamo()'";
					$ubicacion_topografica3="";	
					foreach($ubicacion_topografica2 as $item){
						if($ubicacion_topografica3==""){
							$ubicacion_topografica3=$item;
						}else{
							$ubicacion_topografica3=$ubicacion_topografica3." <- ".$item;
						}
		            }
				}
				$ubicacion_topografica1=$ubicacion_topografica3;
			/* Fin de definir la ubicación topográfica */
				$nombre_expediente.="<div class='art_exp center' onclick='cargar_expediente(\"$num_exp\",\"$ubicacion_topografica1\",\"$nombre_expediente\")'>($num_exp)<br> $nombre_exp</div>";
			}else{
				for ($j=0; $j < $max2; $j++) { 
					$num_exp = $exp[$j];

					$consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
					$fila_exp 	  = pg_query($conectado,$consulta_exp);
					$linea_exp    = pg_fetch_array($fila_exp);
					$nombre_exp   = $linea_exp['nombre_expediente'];

				/* Se define la ubicación topográfica */
					$ubicacion_topografica2=validar_ubicacion($num_exp);
					
					if($ubicacion_topografica2==false){
						if($ubicacion_topografica=='SI'){
							$onclick_ubicacion_topografica = "onclick='carga_ubicacion_topografica()' title='Ésta es la ubicación física del expediente. Click aquí para ir al módulo para cargar ó modificar la ubicación topográfica de éste expediente'";
						}else{
							$onclick_ubicacion_topografica = "title='Usted no tiene permisos para cargar la ubicación topográfica de éste expediente. Comuníquese con el administrador del sistema.'";
						}
						$ubicacion_topografica3="Expediente no se ha ubicado topográficamente todavía";
					}else{
						$onclick_ubicacion_topografica = "onclick='menu_prestamo()'";
						$ubicacion_topografica3="";	
						foreach($ubicacion_topografica2 as $item){
							if($ubicacion_topografica3==""){
								$ubicacion_topografica3=$item;
							}else{
								$ubicacion_topografica3=$ubicacion_topografica3." <- ".$item;
							}
			            }
					}
					$ubicacion_topografica1=$ubicacion_topografica3;
				/* Fin de definir la ubicación topográfica */
					$nombre_expediente = $nombre_expediente."<div id='expediente_$j' class='art_exp center' onclick='cargar_expediente(\"$num_exp\",\"$ubicacion_topografica1\",\"$nombre_exp\",\"expediente_$j\",\"$max2\")'>($num_exp)<br> $nombre_exp</div>";
				}
			}
			$botones_expedientes = "
				<td>$nombre_expediente</td>
				<td id='info_expediente' class='hidden'>
					<div class='art_exp center' onclick='cargar_expediente_nuevo(\"$codigo_dependencia\")'>Incluir en otro expediente</div>
					<div class='art_exp center'>Excluir de éste expediente</div>
					<div id='ubicacion_topografica_expediente' class='art_exp center' $onclick_ubicacion_topografica>$ubicacion_topografica1</div>
				</td>";
		}
		echo "
		<center>
		<div id='visor_expedientes' style='float:left; overflow-y:scroll; width: 100%;'>
			<table id='listado_expedientes' border='0' width='100%' style='font-size: 12px; float:left; overflow-y: scroll;'>
				<tr>
					<td style='padding: 10px;'>$contenido</td>$botones_expedientes
				</tr>
				<tr>
					<td colspan='3'>
						<table id='contenido_expediente' border='0' width='100%'>
						</table>
					</td>
				</tr>
			</table>
		</div>

		<div id='visor_adjuntos_pdf' class='hidden' width='50%'></div>
		</center>";
		break;	

/***************************************************************************************************/
/***************************************************************************************************/
} // Cierre del switch(pestana)
?>	
</body>
</html>