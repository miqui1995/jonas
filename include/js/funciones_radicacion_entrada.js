/**************************************************************	
* @class Funcion para mostrar el div "div_dias_tramite"  
* @description El input "div_dias_tramite" (que contiene el input "dias_tramite") por defecto se encuentra "hidden"  
* mediante esta funcion se muestra el div "div_dias_tramite, 
* oculta el div que contiene "xx dias habiles de tramite" 
* enfoca el input "dias_tramite" 
* @param {} No recibe parametros. 
**************************************************************/
function mostrar_dias_tramite(){
	$("#div_dias_tramite").slideDown("slow");
	$("#termino_td").slideUp("slow");

	$("#dias_tramite").focus();
}

/**************************************************************	
* @class funcion para validar el valor del input "dias_tramite" 
* @description se utiliza la funcion validar_input("dias_tramite") 
*** Si hay errores visibles, repertir recursivamente la misma funcion 2 veces luego de 1 segundo
*** Si no hay errores visibles, toma el valor de "dias_tramite" y dependiendo le asigna el html al div "termino_td"
*** Vuelve a mostrar el div "termino_td", oculta el div "div_dias_tramite" 
* @param {integer} (loop) numero del loop. Si es uno, va a entrar en el ciclo si no, la funcion se llama recursivamente a sí misma. 
**************************************************************/
function ocultar_dias_tramite(loop){
	validar_input("dias_tramite")

	if($("#error_dias_tramite").is(":visible") || $("#dias_tramite_max").is(":visible") || $("#dias_tramite_null").is(":visible") || $("#dias_tramite_cero").is(":visible")){
		if (loop != 2) {
            loop += 1;
            setTimeout(function() {
	 			ocultar_dias_tramite(loop);	 			
			},1000);
        }
	}else{
 		var tram = $("#dias_tramite").val();

 		$("#termino_td").slideDown("slow");
 		$("#div_dias_tramite").slideUp("slow");
 		if(tram==1){
	 		$("#termino_td").html("<b style='font-size:15px;'>"+tram+" dia habil de tramite</b>")
 		}else{
	 		$("#termino_td").html("<b style='font-size:15px;'>"+tram+" dias habiles de tramite</b>")
 		}
	}
}

/**************************************************************	
* @class Funcion para que al dar en la tecla "Enter" avance al siguiente campo del formulario  
* @description Al ubicarse sobre cualquiera de los input del formulario y desde el teclado pulsar "Enter" 
* avanza al siguiente input que sea visible. 
* Al llegar al último input, toma el valor del input "tipo_formulario" y dependiendo del valor, llama la funcion
* que se utiliza para enviar dicho formulario.
* @param {} No recibe parametros. 
**************************************************************/
$("body").on("keydown", "input, select, textarea", function(e) {
  	var self 	= $(this),
    form 		= self.parents("form:eq(0)"),
    focusable,
    next;
  
  	// Al presionar la tecla enter
  	if (e.keyCode == 13) {
    	// busco el siguiente elemento
    	focusable 	= form.find("input,a,select,button,textarea").filter(":visible");
    	next 		= focusable.eq(focusable.index(this) + 1);
    
    	// si existe siguiente elemento, hago foco
    	if (next.length) {
     		next.focus();
    	} else {
      		// si no existe otro elemento
      		var tipo_formulario = $("#tipo_formulario").val();
      		switch(tipo_formulario){
      			case "radicacion_entrada_normal":
		      		radicar_documento_entrada();
      			break;

      			case "formulario_modificar_radicado":
      				modificar_radicado();
      			break;

      			case 'radicacion_rapida':
      				submit_grabar_radicacion_rapida();
      			break;
      		}
    	}
    	return false;
  	}
});

/* Funcion para validar input radicacion */
var timerid="";
// $(function buscador_formulario_modificar_radicado(){
	$("#asunto_radicado").on("input",function(e){ // Accion que se activa cuando se digita #asunto_radicado
		$(".errores").slideUp("slow");
		var asunto_radicado = $(this).val();
		    
	    if($(this).data("lastval")!= asunto_radicado){
	    	$(this).data("lastval",asunto_radicado);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('asunto_radicado');
			},1000);
	    };
	});

	$("#descripcion_anexos").on("input",function(e){ // Accion que se activa cuando se digita #descripcion_anexos
		$(".errores").slideUp("slow");
		var descripcion_anexos = $(this).val();
		    
	    if($(this).data("lastval")!= descripcion_anexos){
	    	$(this).data("lastval",descripcion_anexos);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('descripcion_anexos');
			},1000);
	    };
	});

	$("#dignatario_remitente").on("input",function(e){ // Accion que se activa cuando se digita #dignatario_remitente
		$(".errores").slideUp("slow");
		loading('sugerencias_dignatario')

	    var dignatario_remitente = $(this).val();
	    
	    if($(this).data("lastval")!= dignatario_remitente){
	    	$(this).data("lastval",dignatario_remitente);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('dignatario_remitente');
	 			dignatario_remitente = $("#dignatario_remitente").val();

	 			if(dignatario_remitente.length>=3){
		 			$.ajax({
						type 	: 'POST',
						url 	: 'radicacion/radicacion_entrada/buscador_remitente.php' ,
						data 	: {
							'buscar_destinatario': dignatario_remitente
						},
						success: function(resp){
							$("#sugerencias_dignatario").html(resp);
						}
					})
	 			}else{
					$("#sugerencias_dignatario").html("");
	 			}
			},1000);
	    };
	});
	$("#direccion_remitente").on("input",function(e){ // Accion que se activa cuando se digita #direccion_remitente
		$(".errores").slideUp("slow");
		    
		var direccion_remitente = $(this).val();
	    
	    if($(this).data("lastval")!= direccion_remitente){
	    	$(this).data("lastval",direccion_remitente);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('direccion_remitente');
			},1000);
	    };
	});

	$("#mail_remitente").on("input",function(e){ // Accion que se activa cuando se digita #mail_remitente
		$(".errores").slideUp("slow");
		var mail_remitente = $(this).val();
		    
	    if($(this).data("lastval")!= mail_remitente){
	    	$(this).data("lastval",mail_remitente);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('mail_remitente');
			},1000);
	    };
	});
	$("#nombre_completo").on("input",function(e){ // Accion que se activa cuando se digita #nombre_completo
		$(".errores").slideUp("slow");
		loading('sugerencias_nombre_completo')

	    var nombre_completo = $(this).val();
	    
	    if($(this).data("lastval")!= nombre_completo){
	    	$(this).data("lastval",nombre_completo);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('nombre_completo');
	 			nombre_completo = $("#nombre_completo").val();

	 			if(nombre_completo.length>=2){
		 			$.ajax({
						type 	: 'POST',
						url 	: 'radicacion/radicacion_entrada/buscador_remitente.php' ,
						data 	: {
							'buscar_destinatario': nombre_completo
						},
						success: function(resp){
							$("#sugerencias_nombre_completo").html(resp);
						}
					})	
	 			}else{
					$("#sugerencias_nombre_completo").html("");
	 			}
			},1000);
	    };
	});

	$("#numero_guia_radicado").on("input",function(e){ // Accion que se activa cuando se digita #numero_guia_radicado
		$(".errores").slideUp("slow");
	    var numero_guia_radicado = $(this).val();
	    
	    if($(this).data("lastval")!= numero_guia_radicado){
	    	$(this).data("lastval",numero_guia_radicado);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('numero_guia_radicado');	 			
			},1000);
	    };
	});

	$('#search_dependencia_destino').on("input",function(e){ // Accion que se activa cuando se digita #search_dependencia_destino
		$(".errores").slideUp("slow");
		$('#sugerencias_remitente').slideDown("slow");
		loading('sugerencias_remitente')

		$('#sugerencias_dependencia_destino').html("");
		$('#div_boton_enviar').slideUp("slow");
		$('#sin_distribuidor').slideUp("slow");

		/* Condiciona si es formulario de entrada o radicacion rapida */
		var formulario_origen = ($("#nombre_completo").is(":visible"))?'radicar_entrada':'descripcion_anexos';

		var envio = $(this).val();
		
		if($(this).data("lastval")!= envio){
	    	$(this).data("lastval",envio);                   
   			clearTimeout(timerid);
   			timerid = setTimeout(function() {
				$.ajax({
					type: 'POST',
					url:  'radicacion/radicacion_entrada/buscador_remitente.php' ,
					data: {
						'search_dependencia_destino': envio,
						'formulario_origen' 		: formulario_origen
					},
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_remitente').html(resp);
						}
					}
				})		 						 
   			},1000);
	    };
	})

	$("#telefono_remitente").on("input",function(e){ // Accion que se activa cuando se digita #telefono_remitente
		$(".errores").slideUp("slow");
		var telefono_remitente = $(this).val();
		    
	    if($(this).data("lastval")!= telefono_remitente){
	    	$(this).data("lastval",telefono_remitente);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('telefono_remitente');
			},1000);
	    };
	});

	$('#ubicacion_remitente').on("input",function(e){ // Accion que se activa cuando se digita #ubicacion_remitente
		$('#sugerencias_ubicacion_remitente').slideDown('slow');

		loading('sugerencias_ubicacion_remitente');
		$(".errores").slideUp("slow");

		var envio = $(this).val();
		
		if($(this).data("lastval")!= envio){
	    	$(this).data("lastval",envio);                   
   			clearTimeout(timerid);
   			timerid = setTimeout(function() {
   				validar_input('ubicacion_remitente');

   				if(envio.length>0){
					$('#ubicacion_remitente_min').slideUp('slow');
					$.ajax({
						type: 'POST',
						url:  'admin_muni/buscador_municipios.php' ,
						data: {
							'search' 		: envio,
							'formulario' 	: 'entrada'
						},
						success: function(resp){
							if(resp!=""){
								$('#sugerencias_ubicacion_remitente').html(resp);
							}
						}
					})		 		
   				}
			},1000);
	    };
	})
// })
/* Fin funcion para validar input radicacion */

/**************************************************************	
* @class Funcion para mostrar el div "recibido_mensajeria"  
* @description El input "recibido_mensajeria" (que contiene los div ("Guia servientrega, Guia 4-72, etc") por defecto se encuentra "hidden"  
* mediante esta funcion se muestra el div "#recibido_mensajeria, 
* @param {} No recibe parametros. 
**************************************************************/
function valida_recepcion(){
	var medio_recepcion1 = $("#medio_recepcion").val();

	if(medio_recepcion1=="servicio_postal"){
		$("#recibido_mensajeria").slideDown("slow");
		if($("#numero_guia_radicado").val()==""){
			$("#numero_guia_radicado").focus();
		}
	}else{
		$("#numero_guia_radicado_error").slideUp("slow");
		$("#recibido_mensajeria").slideUp("slow");
		$("#medio_respuesta_solicitado").focus();		
	}
}

/**************************************************************	
* @class Funcion para cargar el valor de un div al input "numero_guia_radicado"  
* @description Se invoca al dar click en un div "carga_recibido_mensajeria" dependiendo del parámetro recibido, carga un valor de la empresa de mensajería que entrega el radicado.
* mediante esta funcion se carga el valor dependiendo del parámetro recibido en el input "numero_guia_radicado",
* enfoca el input "numero_guia_radicado" 
* @param {} tipo. Dependiendo de éste, se pone un valor diferente en "numero_guia_radicado" 
**************************************************************/
function carga_recibido_mensajeria(tipo){
	switch(tipo){
		case '1':
			var texto = "GUIA 4-72 NO. ";
			break;
		case '2':
			var texto = "GUIA INTERRAPIDISIMO NO. ";
			break;
		case '3':
			var texto = "GUIA SERVIENTREGA NO. ";
			break;
	}

	$("#numero_guia_radicado").val(texto);
	$("#numero_guia_radicado").focus();

	$("#recibido_mensajeria").slideUp("slow");
}

/* Script para validar consecutivo exista en dependencia - Tipo radicado */
function valida_secuencia(tipo_radicacion){
	switch (tipo_radicacion){
		case '1':
			var tipo_documento = 'entrada'
		break;
		case '2':
			var tipo_documento = 'salida'
		break;
	}
	$.ajax({
		type 	: 'POST',
		url 	:  'radicacion/radicacion_entrada/buscador_remitente.php' ,
		data 	: {
			'tipo_radicacion': tipo_radicacion
		},
		success: function(resp){
			if(resp!="true"){
				Swal.fire({	
					position 			: 'top-end',
				    showConfirmButton 	: false,
				    timer 				: 2500,	
				    title 	 			: 'El consecutivo de su dependencia no existe, por lo que no puede radicar documentos de '+tipo_documento,
				    text 				: 'Comuníquese con el administrador del sistema.',
				    type 				: 'warning'
				}).then(function(isConfirm){	
				    window.location.href = 'principal3.php';
				});
			}
		}
	})	
}
/* Fin script para validar consecutivo exista en dependencia - Tipo radicado */
/* Script para mostrar si no hay distribuidor en la dependencia */
function sin_distribuidor(){
	$('#sin_distribuidor').slideDown('slow');
	$('#usuario_destino').val('');
	$('#search_dependencia_destino').focus();
	$('#div_boton_enviar').slideUp('slow');
}
/* Fin script para mostrar si no hay distribuidor en la dependencia */
/* Script para cargar dependencia en Formulario Radicacion de Entrada */
function cargar_valor_dependencia(depe_codigo,depe_nomb,input_focus){
	depe_nomb = depe_nomb.trim();
	$('#search_dependencia_destino_min').slideUp("slow");

	$("#search_dependencia_destino").val(depe_nomb);
	$("#nombre_dependencia_destino").val(depe_nomb);
	$("#codigo_dependencia").val(depe_codigo);

	$.ajax({
		type 	: 'POST',
		url 	:  'radicacion/radicacion_entrada/buscador_remitente.php' ,
		data 	: {
			'search_dest_depe' 	: depe_codigo,
			'input_focus'		: input_focus
 		},
		success: function(resp){
			if(resp!=""){
				$('#sugerencias_dependencia_destino').html(resp);
				$('#sugerencias_remitente').slideUp("slow");
				$("#"+input_focus).focus();
			}
		}
	})
}

function cargar_valor_dependencia_destino(usuario_destino,input_focus){ // Funcion se llama si hay un usuario en la dependencia con permiso "Distribuidor de la dependencia"
	$("#usuario_destino").val(usuario_destino);
	$('#sugerencias_remitente').html(''); 
	$('#div_boton_enviar').slideDown('slow');
	$('#'+input_focus).focus();
}
/* Fin script para cargar dependencia en Formulario Radicacion de Entrada */
/* Funcion para enviar formulario radicacion entrada */
function submit_grabar_radicacion_rapida(){
	var clasificacion_radicado 		= $("#termino").val();
	var codigo_dependencia 			= $("#codigo_dependencia").val();  // Codigo dependencia de destino
	var dependencia_destino 		= ($("#search_dependencia_destino").val()).trim(); // Nombre completo dependencia destino
	var descripcion_anexos 			= $("#descripcion_anexos").val();// Anexos que llegan (usb, cd, caja, etc)
	var dias_tramite 	 			= $("#dias_tramite").val();
	var tipo_formulario 			= $("#tipo_formulario").val();
	var tipo_radicacion 			= $("#tipo_radicacion").val(); // Tipo de radicado (1- Entrada por defecto. Porque stickers solo se generan con entradas.)
	var usuario_destino 			= $("#usuario_destino").val();

	if(descripcion_anexos==""){
		descripcion_anexos="Sin Anexos";
	}
	
/* Si en la pantalla aprarece algún error, se detiene aqui y devuelve FALSO */
	if($(".errores").is(":visible") || dependencia_destino==""){
		return false;
	}else{
	/* Si en la pantalla no hay ningún error generado desde la validación de cada uno de los campos continúa aqui definiendo el código
	de la entidad para definir cuales valores envía. */	
		var codigo_entidad = $("#codigo_entidad").val();

		/* Esta es una modificacion temporal desarrollada para ejercito nacional con el fin de reeemplazar el numero de radicado (Jonas) por 
	  	el radicado que se genera generado en Orfeo-Ejercito. Se definen las variables y se envían mediante AJAX utilizando POST a la
	  	direccion de un servidor de Orfeo-Ejercito (http://172.22.2.226/pruebas/interoperabilidad_rest/api_jonas.php) donde internamente ese
	  	servidor de Orfeo-Ejercito genera un número de radicado Orfeo-Ejercito el cual devuelve como respuesta. Si el tamaño de la respuesta 
	  	son (16) caracteres, quiere decir que es un radicado Orfeo-Ejercito por lo que procede a enviar todos los datos al archivo 
		radicacion/radicacion_entrada/radicar.php mediante POST para que se almacene en Jonas todos los datos del radicado. 
		En caso que el tamaño de la respuesta no, sean (16) caracteres, quiere decir que hubo un error, por lo que devuelve en la consola del 
		navegador el mensaje "No se puede enviar, codigo de error XXXX". 
		Los datos que se envían adicionalmente en este desarrollo de Ejercito son (usuario_origen - codigo_entidad - numero_radicado{que viene en el "resp" de
		AJAX})*/

		if(codigo_entidad=='EJC'){
			var usuario_origen = $("#login_usuario").val();
	
			$.ajax({
	            type    	: 'POST',
	            url     	: 'http://172.22.2.226/pruebas/interoperabilidad_rest/api_jonas.php',
	            datatype 	: 'jsonp',
	            crossDomain : true,
	            data 		: {
					'recibe_jonas'     	: 'radicacion_rapida',
	                'descripcion_anex' 	: descripcion_anexos,
	                'dias_tramite'		: dias_tramite,
	                'login_destino'    	: usuario_destino,
	                'login_origen'     	: usuario_origen,
	                'tipo_radicado' 	: clasificacion_radicado
	            },
	            success: function(resp){
					if($.trim(resp).length==16){ // 16 es la longitud de caracteres de un radicado Orfeo-Ejercito
						$('#contenido').load('radicacion/radicacion_entrada/radicar.php',{clasificacion_radicado:clasificacion_radicado, codigo_dependencia:codigo_dependencia, dependencia_destino:dependencia_destino, descripcion_anexos:descripcion_anexos, dias_tramite:dias_tramite, numero_radicado:resp, tipo_formulario:tipo_formulario, usuario_destino:usuario_destino});
					}else{
						alert("No se han recibido datos desde Orfeo-Ejercito, codigo de error "+resp);
					}
	            }
			})	
		}else{
		/* Si el codigo de la entidad no es EJC */
			$('#contenido').load('radicacion/radicacion_entrada/radicar.php',{clasificacion_radicado:clasificacion_radicado, codigo_dependencia:codigo_dependencia, dependencia_destino:dependencia_destino, descripcion_anexos:descripcion_anexos, dias_tramite:dias_tramite, tipo_formulario:tipo_formulario, usuario_destino:usuario_destino});
		}	
	}
}

/* Fin funcion para enviar formulario radicacion entrada  */
/* Funcion para buscar radicado en modificación rápida */
function buscar_radicado_modificacion_rapida(){
	var numero_radicado = $("#search_radicado_modificacion_rapida").val();
	
	$.ajax({
		type: 'POST',
		url:  'radicacion/radicacion_entrada/buscador_remitente.php',
		data: {
			'radicado_modificacion_rapida': numero_radicado
		},
		success: function(resp){
			if(resp!=""){
				$('#desplegable_resultados').html(resp);
			}
		}
	})
}

$(function buscador_modificacion_rapida(){
	$('#search_radicado_modificacion_rapida').on("input",function(e){ // Accion que se activa cuando se digita #search_radicado_modificacion_rapida
		$('#desplegable_resultados').slideDown("slow");
		$('#desplegable_resultados').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
	
		var envio = $(this).val();
		
		if($(this).data("lastval")!= envio){
	    	$(this).data("lastval",envio);                   
   			clearTimeout(timerid);
   			timerid = setTimeout(function() {
         		if(envio.length>2 && envio.length<50){		
					$.ajax({
						type: 'POST',
						url:  'radicacion/radicacion_entrada/buscador_remitente.php' ,
						data: {
							'radicado_modificacion_rapida': envio
						},
						success: function(resp){
							if(resp!=""){
								$('#desplegable_resultados').html(resp);
							}
						}
					})	
				}else{
					$('#desplegable_resultados').html('<h4>Para iniciar la búsqueda debe ingresar por lo menos 3 caracteres.</h4>');
				} 
				if(envio.length>50){
					$('#desplegable_resultados').html('<h4>La busqueda debe tener 50 caracteres maximo. Revise por favor</h4>');
				}  				 
   			},1000);
	    };
	})
})

/* Fin funcion para buscar radicado en modificación rápida */
/* Funcion para cargar modificacion rapida */
function cargar_modificacion_radicado(numero_radicado, fecha_radicado, descripcion_anexos, dependencia_destino, nombre_dependencia_destino, usuario_actual){
	$('#contenido').load( // Carga por post en el div "contenido" las variables.
		'radicacion/radicacion_entrada/modificacion_rapida.php',{
			numero_radicado:numero_radicado, fecha_radicado:fecha_radicado, descripcion_anexos:descripcion_anexos, dependencia_destino:dependencia_destino, nombre_dependencia_destino:nombre_dependencia_destino, usuario_actual:usuario_actual
		}
	)
}
/* Fin funcion para cargar modificacion rapida */
/* Funcion para buscar radicado sin PDF */
function buscar_radicado_falta_pdf(){
	$.ajax({
		type: 'POST',
		url:  'radicacion/radicacion_entrada/buscador_remitente.php',
		data: {
			'radicado_falta_pdf': ''
		},
		success: function(resp){
			if(resp!=""){
				$('#desplegable_resultados').html(resp);
			}
		}
	})
}

$(function buscador_rad_falta_pdf(){
	$('#search_radicado_falta_pdf').on("input",function(e){ // Accion que se activa cuando se digita #search_radicado_falta_pdf
		$('#desplegable_resultados').slideDown("slow");
		loading('desplegable_resultados');
	
		var envio = $(this).val();
		
		if($(this).data("lastval")!= envio){
	    	$(this).data("lastval",envio);                   
   			clearTimeout(timerid);
   			timerid = setTimeout(function() {
				$.ajax({
					type: 'POST',
					url:  'radicacion/radicacion_entrada/buscador_remitente.php' ,
					data: {
						'radicado_falta_pdf': envio
					},
					success: function(resp){
						if(resp!=""){
							$('#desplegable_resultados').html(resp);
						}
					}
				})					 
   			},1000);
	    };
	})
})

/* Fin funcion para buscar radicado en modificación rápida */
/* Funcion para cargar modificacion rapida */
function cargar_modificacion_radicado(numero_radicado, fecha_radicado, descripcion_anexos, dependencia_destino, nombre_dependencia_destino, usuario_actual){
	$('#contenido').load( // Carga por post en el div "contenido" las variables.
		'radicacion/radicacion_entrada/modificacion_rapida.php',{
			numero_radicado:numero_radicado, fecha_radicado:fecha_radicado, descripcion_anexos:descripcion_anexos, dependencia_destino:dependencia_destino, nombre_dependencia_destino:nombre_dependencia_destino, usuario_actual:usuario_actual
		}
	)
}
/* Fin funcion para buscar radicado sin PDF */






/* Funcion para cargar modificacion */
function cargar_modificacion(numero_radicado,codigo_contacto,login_usuario_actual){
	$('#contenido').load( // Carga por post en el div "contenido" las variables.
		'radicacion/radicacion_entrada/modificacion_rapida.php',{
			numero_radicado:numero_radicado, codigo_contacto:codigo_contacto, usuario_actual:login_usuario_actual
		}
	)
}
function cargar_modificacion_interna(numero_radicado,fecha_radicado,descripcion_anexos,usuario_actual,asunto,clasificacion_radicado){
	$('#contenido').load( // Carga por post en el div "contenido" las variables.
		'radicacion/radicacion_entrada/modificacion_rapida.php',{
			numero_radicado:numero_radicado, fecha_radicado:fecha_radicado, descripcion_anexos:descripcion_anexos, usuario_actual:usuario_actual, asunto:asunto, clasificacion_radicado:clasificacion_radicado 
		}
	)	
}
/* Fin funcion para cargar modificacion */
/* Funcion para cargar desde nombre_completo las posibles campos formulario modificar radicado */
function buscar_destinatario(nombre_contacto){
	if(nombre_contacto==""){
		$("#sugerencias_dignatario").html("");
	}else{
		$.ajax({
			type 	: 'POST',
			url 	: 'radicacion/radicacion_entrada/buscador_remitente.php' ,
			data 	: {
				'buscar_dignatario': nombre_contacto
			},
			success: function(resp){
				$("#sugerencias_dignatario").html(resp);
			}
		})
	}

}

function cargar_nombre_contacto(nombre_contacto,representante_legal,ubicacion_contacto,direccion_contacto,telefono_contacto,mail_contacto,codigo_contacto){
	$(".errores").slideUp("slow");

	$("#nombre_completo").val(nombre_contacto);
	$("#sugerencias_nombre_completo").html("");

	$("#dignatario_remitente").val(representante_legal);
	buscar_destinatario(nombre_contacto);

	$("#ubicacion_remitente").val(ubicacion_contacto);
	$("#codigo_contacto").val(codigo_contacto);

	cargar_direccion_remitente(direccion_contacto,'1');
	cargar_telefono_remitente(telefono_contacto,'1');
	cargar_mail_remitente(mail_contacto,'1');

	$("#asunto_radicado").focus();
}
/*
function cargar_representante_legal(representante_legal){
	$(".errores").slideUp("slow");
	$("#dignatario_remitente").val(representante_legal);
	$("#sugerencias_dignatario").html("");
	$("#direccion_remitente").focus();
}
*/
function cargar_direccion_remitente(direccion_contacto,cod){
	$(".errores").slideUp("slow");
	$("#direccion_remitente").val(direccion_contacto);
	if(cod=='1'){
		$("#sugerencias_direccion").html("<div class='art_exp' onclick=\"javascript:cargar_direccion_remitente('"+direccion_contacto+"','2')\" title='Esta es una sugerencia. No es obligatorio.'>"+direccion_contacto+"</div>");
	}else{
		$("#sugerencias_direccion").html("");
	}
	$("#telefono_remitente").focus();
}
function cargar_telefono_remitente(telefono_contacto,cod){
	$(".errores").slideUp("slow");
	$("#telefono_remitente").val(telefono_contacto);
	if(cod=='1'){
		$("#sugerencias_telefono").html("<div class='art_exp' onclick=\"javascript:cargar_telefono_remitente('"+telefono_contacto+"','2')\" title='Esta es una sugerencia. No es obligatorio.'>"+telefono_contacto+"</div>");
	}else{
		$("#sugerencias_telefono").html("");
	}
	$("#mail_remitente").focus();
}
function cargar_mail_remitente(mail_contacto,cod){
	$(".errores").slideUp("slow");
	$("#mail_remitente").val(mail_contacto);
	if(cod=='1'){
		$("#sugerencias_mail").html("<div class='art_exp' onclick=\"javascript:cargar_mail_remitente('"+mail_contacto+"','2')\" title='Esta es una sugerencia. No es obligatorio.'>"+mail_contacto+"</div>");
	}else{
		$("#sugerencias_mail").html("");
	}
	$("#asunto_radicado").focus();
}
/* Fin funcion para cargar desde nombre_completo las posibles campos formulario modificar radicado */

function cargar_modifica_municipio(id,nombre_municipio,nombre_departamento,nombre_pais,nombre_continente){// Esta funcion no es la misma que está en funciones_municipios.
	$('#id_municipio').val(id);
	$('#ubicacion_remitente').val(nombre_municipio+" ("+nombre_departamento+") "+nombre_pais+"-"+nombre_continente);
	$('#sugerencias_ubicacion_remitente').slideUp('slow');
	$('.errores').slideUp('slow');
	$("#direccion_remitente").focus();
}
/* Fin funcion para buscar ubicacion del remitente (Municipios) */

/* Script para cargar desplegable terminos */
function desplegable_terminos(){
	$.ajax({
		type: 'POST',
		url:  'radicacion/radicacion_entrada/buscador_remitente.php',
		data: {
			'desplegable_terminos':'1'
		},
		success: function(resp){
			if(resp!=""){
				$('#td').html(resp);
				muestra_termino();
			}
		}
	})
}
/* Fin script para cargar desplegable terminos */
/* Funcion para validar formulario antes de modificar radicado */
function modificar_radicado(){
	validar_input('numero_guia_radicado');
	validar_input('nombre_completo');
	validar_input('dignatario_remitente');
	validar_input('ubicacion_remitente');
	validar_input('direccion_remitente');
	validar_input('telefono_remitente');
	validar_input('mail_remitente');
	validar_input('asunto_radicado');
	validar_input('descripcion_anexos');
	validar_input('search_dependencia_destino');
	
	if($(".errores").is(":visible")){
        return false;
    }else{
		if($("#sugerencias_ubicacion_remitente").is(":visible")){
			$("#error_no_selecciona_ubicacion").slideDown("slow");
    		return false;
    	}else{
    		if($(".art1").is(":visible")){
				$("#search_dependencia_destino").focus();
    			return false;
    		}else{
    			$("#error_no_selecciona_ubicacion").slideUp("slow");

				var formData = new FormData($("#formulario_modificar_radicado")[0]);
				loading('div_boton_enviar');

				$.ajax({
					url 		: 'radicacion/radicacion_entrada/query_modificar.php' ,
					type 		: "POST",
					data 		: formData,
					contentType : false,
					processData : false,

					success: function(datos){
						// console.log(datos)
						$("#resultado_total").html(datos);
					}
				});
    		}
    	}
    }		
}
/* Fin funcion para validar formulario antes de modificar radicado */
/* Funcion para cargar tiempo de tramite - Formuario radicacion entrada */
function muestra_termino(){
	var tipo_documento=$("#termino").val();

	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',
		data: {
			'recibe_ajax'  	 : 'muestra_termino',
			'tipo_documento' : tipo_documento
		},
		success: function(resp){
			if(resp!=""){
				$('#termino_td').html('<br><br><b>'+resp+' dias habiles de tramite</b>');
				$("#dias_tramite").val(resp);
			}
		}
	})
	
}
/* Funcion para cargar tiempo de tramite - Formulario radicacion entrada */
/* Funciones para guardar en base de datos auditoria de modificacion o creacion de contactos */
function auditoria(tipo_formulario,nombre_contacto_creado){

		switch(tipo_formulario){
		case 'agregar_nuevo_contacto':
			var trans  	= "El Contacto ha sido creado";
			var url 	= "login/transacciones.php";
			break;
		case 'modificar_contacto':
			var trans 	= "El Contacto ha sido modificado";
			var url 	= "login/transacciones.php";
			break;
			
		case 'sticker_entrada':
			var url 	= "../../login/transacciones.php";
			break;	
		case 'modificacion_rapida':
		case 'modificacion_radicado':
		case 'modificacion_inventario':
			var trans 	= "Documento modificado correctamente";
			var url 	= "login/transacciones.php";
			break;	
		case 'modificacion_rapida_mas_imagen':
		case 'modificacion_radicado_mas_imagen':
			var trans 	= "Documento modificado e imagen cargada correctamente";
			var url 	= "login/transacciones.php";
			break;
	}
	$.ajax({	// Guardo registro de ingreso al sistema para auditoria
		type: 'POST',
		url: url,
		data: {
			'transaccion' 	: tipo_formulario,
			'creado' 		: 	nombre_contacto_creado
		},			
		success: function(resp1){
			if(resp1=="true"){
				// sweetAlert({
				Swal.fire({	
					position 			: 'top-end',
				    showConfirmButton 	: false,
				    timer 				: 1500,		
				    title 				: trans,
				    text 				: '',
				    type 				: 'success'
				}).then(function(isConfirm){
					switch(tipo_formulario){			
						case 'modificacion_rapida':
						case 'modificacion_radicado':
						case 'modificacion_rapida_mas_imagen':
						case 'modificacion_radicado_mas_imagen':
						case 'modificacion_inventario':
							carga_modificacion();
							break;
					}
				})
			}else{
				alert(resp1)
			}
		}
	})
}
	
/* Fin funciones para guardar en base de datos auditoria de modificacion o creacion de contactos */

/* Funcion para mostrar error al subir pdf */
function error_subida_pdf(error_upload_files,error_file){ 
	Swal.fire({	
		position 			: 'top-end',
	    showConfirmButton 	: false,
	    timer 				: 5000,	
	    title 				: 'Comuníquese con el administrador del sistema. Codigo de error al cargar pdf '+ error_upload_files,
	    text 				: error_file,
	    type 				: 'error'
	});
	$("#div_boton_enviar").html('<input type="button" value="Modificar Radicado" id="modificar_radicado" onclick="modificar_radicado()" class="botones">')
}
/* Fin funcion para mostrar error al subir pdf */
/* Funcion para ir atras en Radicacion de entrada */
function atras(){
	$("#contenido").load("radicacion/radicacion_entrada/index_entrada.php");
}
/* Fin funcion para ir atras en Radicacion de entrada */

/* Funcion para borrar archivo PDF */
function borrar_pdf_temporal(radicado){
	 Swal.fire({
        title 				:'¿Está seguro de que quiere borrar de su carpeta de scanner PDF la imagen ('+radicado+')?',
        text 				: "Esta acción no se puede revertir. ¿Está seguro?",
        type 				: 'warning',
        showCancelButton 	: true,
        confirmButtonColor 	: '#3085d6',
        cancelButtonColor 	: '#d33',
        confirmButtonText 	: 'Si, Eliminar PDF!',
        cancelButtonText 	: 'Cancelar'

    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: 'POST',
                url:  'scanner/verifica_existe_pdf.php' ,
				data: {
					'radicado' 	: radicado,
					'accion' 	:'borrar_pdf'
				},          
                success: function(resp){
                    if(resp=="pdf_borrado"){
                        Swal.fire({ 
                            position            : 'top-end',
                            showConfirmButton   : false,
                            timer               : 2000,    
                            title               : 'PDF '+radicado ,
                            text                : 'Ha sido borrado correctamente.',
                            type                : 'success'
                        }).then(function(isConfirm){
                        	carga_modulo_scanner();
                         })
                    }else{
                        alert(resp)
                    }
                }
            })  
        }
    })
}
/* Fin funcion para borrar archivo PDF */

/* Funcion para cargar el archivo principal a la bodega */
function cargar_archivo_principal(radicado){
	loading('contenedor_documentos_escaneados');

	$.ajax({
		type: 'POST',
		url:  'scanner/verifica_existe_pdf.php' ,
		data: {
			'radicado' 	: radicado,
			'accion' 	:'cargar_archivo_principal'
		},
		success: function(resp){
			if(resp=="cargado_correctamente"){
				console.log("archivo cargado correctamente")
			}else{
				$('#desplegable_resultados').html(resp);
			}
		}
	})
}
/* Fin funcion para cargar el archivo principal a la bodega */
/* Funcion para buscar radicado en modificación */
$(function search_radicado(){
	$('#search_radicado').on("input",function(e){ // Accion que se activa cuando se digita #search_radicado
		$("#error_search_min").slideUp("slow");	
		$("#error_search_max").slideUp("slow");	
		$('#desplegable_resultados').slideDown("slow");
		loading("desplegable_resultados");

		var numero_radicado = ($(this).val()).trim();
		
		if($(this).data("lastval")!= numero_radicado){
	    	$(this).data("lastval",numero_radicado);                   
   			clearTimeout(timerid);
   			timerid = setTimeout(function() {
				validar_input("search_radicado");

				var numero_radicado2 = ($("#search_radicado").val()).trim();

				$.ajax({
					type: 'POST',
					url:  'radicacion/radicacion_entrada/buscador_remitente.php',
					data: {
						'radicado_modificacion': numero_radicado2
					},
					success: function(resp){
						if(resp!=""){
							$('#desplegable_resultados').html(resp);
						}
					}
				})				 		
   			},1000);
	    };
	})
	$('#search_radicado').focus();
})
/* Fin funcion para buscar radicado en modificación */
/* Funcion para buscar radicado en asociar imagen */
function buscar_radicado_imagen(tipo_imagen){
	switch(tipo_imagen){
		case 'principal':
		var www="";
		break;
	}
	var numero_radicado = $("#search_radicado").val();

	$.ajax({
		type: 'POST',
		url:  'radicacion/radicacion_entrada/buscador_remitente.php',
		data: {
			'radicado_asociar_imagen': numero_radicado
		},
		success: function(resp){
			if(resp!=""){
				$('#desplegable_resultados').html(resp);
			}
		}
	})
}
/* Fin funcion para buscar radicado en asociar imagen */

/**
 * Inicio funcion para radicar documento de entrada normal 
 * @brief La funcion radicar_documento_entrada define los campos que se requieren para generar un radicado de entrada y los envía mediante POST al archivo (radicacion/radicacion_entrada/radicar.php) 
 * @description Primero lleva a cabo las validaciones de cada uno de los campos del formulario mediante la funcion "validar_input('xxxx')" y en caso que alguno muestre error, se detiente hasta que se corrija. Si ninguna de las funciones "validar_input('xxxx')" devuelve error, define uno a uno los campos que utiliza para realizar la radicación de entrada.
 * @param  {} Esta funcion no recibe parámetros.
 * @return {} No retorna ni imprime ningún valor. Solo envía los datos correspondientes al archivo (radicacion/radicacion_entrada/radicar.php) 
 **/

/**********************************************************************************************************
Inicio funcion para enviar correo electronico 
* @class La funcion enviar_mail recibe las variables para estructurar el envio del correo electronico
* @description Manda por ajax a envio_mail/mail.php las variables recibidas cambiandoles ligeramente 
** el nombre de como las reciben y como la manda. Ejemplo: asunto1 se convierte en asunto 
* @param {string} Si las variables vienen vacias se enviaran por ajax con el mismo valor
* @return {string} Se imprime en la consola.log todo lo realizado en el mail.php en caso de ser incorrecto 
* el funcionamiento, se muestra el error. Ejemplo: You must provide at least one recipient email address. 
* y si es correcto se mostrara todo los mensajes de estructuracion y se termina imprimiendo con un solo 1. 
* Esta variable definida como $bool en Mail.php
**********************************************************************************************************/
function enviar_mail(asunto1,direccion_destino1,contenido_html1,nombre_completo_segundo1){
	 $.ajax({
        type: 'POST',
        url:  'envio_mail/mail.php',
        data: {
            'asunto' 					: asunto1,
            'direccion_destino' 		: direccion_destino1,
            'contenido_html' 			: contenido_html1,
            'nombre_completo_segundo' 	: nombre_completo_segundo1,
            'tipo_envio' 				: 'normal'
            },          
        success: function(resp){
       	console.log(resp);          
        }
    }) 
}
/* Fin funcion para cargar enviar correo electronico */
/**************************************************************/
function ingresar_nuevo_contacto(nombre_completo,dignatario_remitente,ubicacion_remitente,direccion_remitente,telefono_remitente,mail_remitente){
    Swal.fire({
        title 				:'¿Está seguro de que quiere agregar el contacto '+nombre_completo+' ('+dignatario_remitente+') a la base de datos de contactos frecuentes?',
        text 				: "Esta acción no se puede revertir. ¿Está seguro?",
        type 				: 'warning',
        showCancelButton 	: true,
        confirmButtonColor 	: '#3085d6',
        cancelButtonColor 	: '#d33',
        confirmButtonText 	: 'Si, Agregar Contacto!',
        cancelButtonText 	: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type 	: 'POST',
            	url 	:  'radicacion/radicacion_entrada/buscador_remitente.php',
                data  	: {
                    'agregar_nuevo_contacto' 	: nombre_completo,
                    'dignatario_remitente' 		: dignatario_remitente,
                    'ubicacion_remitente' 		: ubicacion_remitente,
                    'direccion_remitente' 		: direccion_remitente,
                    'telefono_remitente' 		: telefono_remitente,
                    'mail_remitente' 			: mail_remitente
                },          
                success: function(resp){
                	$("#resultado_js").html(resp);
                }
            })  
        }
    })
}

function imprimir_sticker_ubic(ubicacion,descripcion_anexos,asunto_radicado,cod_nom_destino,encabezado,timestamp,radicado,ruta_logo){
	var json= '{"sticker":[{"anexos":"'+descripcion_anexos+'","asunto":"'+asunto_radicado+'","destino":"'+cod_nom_destino+'","encabezado":"'+encabezado+'","fecha_radicado":"'+timestamp+'","numero_radicado":"'+radicado+'","ruta_logo":"'+ruta_logo+'","ubicacion":"'+ubicacion+'"}]}';

	$("#json_info_sticker").val(json);
	$("#api_sticker").submit();
}
/* Fin funcion para radicar documento de entrada normal */
/***********************************************************************************************************/
/* Funciones para Modificacion Inventario*/
/***********************************************************************************************************/
function formato_fecha(fecha,input){ 		// Valida que al poner la fecha_inicial tenga el formato YYYY-MM-DD para cambiarlo por DD/MM/YYYY
	var caracter_fecha=fecha.substring(4,5);

	if(caracter_fecha=="-"){
		var year 	= fecha.substring(0,4);
		var mes 	= fecha.substring(5,7);
		var dia 	= fecha.substring(8,10);
		
		var fecha_con_formato = dia+"/"+mes+"/"+year;
		$("#"+input+"_error").slideUp("slow");
	}else{
		alert("Hay un error con el formato de fecha, por favor comuníquese con el administrador del sistema.");
		$("#"+input+"_error").slideDown("slow");
		// carga_modificacion();
	}

	$("#"+input).val(fecha_con_formato);
	// console.log(fecha);
}

/**********************************************************************************************************
Inicio funcion para verificar si existe el PDF para cargar desde la carpeta compartida. Se usa en el modulo cuando se instala en intranet. 
** para web-hosting no es posible utilizarla todavía.

* @class La funcion verificar_pdf_por_cargar se usa unicamente en el formulario de modificacion de radicado de entrada y asociar imagen principal.

* @description Recibe dos variables como parámetro las cuales envía mediante ajax para verificar si existe algun archivo PDF en la carpeta especificada
** y si la petición retorna error o si despliega el div oculto donde se carga la vista previa del PDF   

* @param string{path} Es la ruta del archivo que se desea visualizar.

* @param string{accion} Dependiendo de esta variable, se ejecutan diferentes acciones en scanner/verifica_existe_pdf.php

** instalar para que funcione en las entidades donde Jonas se encuentra instalado de manera interna. Falta adaptar para web-hosting.
* @return {} No retorna valores ya que la labor de la funcion es previsualizar archivos PDF que se encuentran en la bodega_pdf.
**********************************************************************************************************/
function verificar_pdf_por_cargar(path,accion){
	// var archivo_pdf  	= $("#archivo_pdf_radicado").val();
	var login_usuario  	= $("#login_usuario").val();

	$.ajax({
		type: 'POST',
		url:  'scanner/verifica_existe_pdf.php',
		data: {
			'accion'  		: accion,
			'login_usuario' : login_usuario,
			'radicado' 		: path
		},
		success: function(resp){
			if(resp=="error"){
				$('#error_adjunto_pdf').slideUp("slow");
				$( "#lista_documentos_escaneados" ).animate({ // Para volver al 100% el width de la tabla.
			    	width: "100%"
			    }, {
			      	queue: false,
			      	duration: 500
			    })
				$("#visor_pdf").fadeOut("slow") // Para dejar de mostrar el visor_adjuntos_pdf

				$('#resultado_js').html(resp);
				$('#error_adjunto_pdf').slideDown("slow");

				$("#contenedor_boton_enviar_imagen").html("");
			}else{
				$('#error_adjunto_pdf').slideUp("slow");

				// $( "#lista_documentos_escaneados" ).animate({ // Para volver al 50% el width de la tabla.
			 //    	width: "0"
			 //    }, {
			 //      	queue: false,
			 //      	duration: 500
			 //    })

				$("#visor_pdf").fadeIn("slow") // Para mostrar el visor_adjuntos_pdf
				$("#visor_pdf").html(resp); 


				$("#viewer3").html(resp);
				$("#viewer3").animate({ // Para volver al 100% el width de la tabla.
			    	width: "100%"
			    }, {
			      	queue: false,
			      	duration: 500
			    })

                $("#viewer").html("");
                $("#viewer").slideUp("slow");

			}
		}
	})	
}

/**********************************************************************************************************
Inicio funcion para ocultar_lista_documentos_escaneados 
* @class La funcion ocultar_lista_documentos_escaneados se usa unicamente en el formulario de modificacion de radicado de entrada
* @description Oculta el listado de documentos_escaneados que se genera en la carpeta compartida por el usuario la cual se debe
** instalar para que funcione en las entidades donde Jonas se encuentra instalado de manera interna. Falta adaptar para web-hosting.
* @param {} No tiene parámetros
* @return {} No retorna valores.
**********************************************************************************************************/
function ocultar_lista_documentos_escaneados(){ 
	$("#lista_documentos_escaneados").hide("slow");
}