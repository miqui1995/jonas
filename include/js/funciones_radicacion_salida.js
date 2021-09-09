var timerid="";

function cargar_empresa_entidad_radicado(codigo_contacto,nombre_contacto,ubicacion_contacto,direccion_contacto,telefono_contacto,mail_contacto,representante_legal){

	$("#codigo_contacto").val(codigo_contacto);
	$("#destinatario_doc").val(representante_legal);

	$("#empresa_destinatario_doc").val(nombre_contacto);

	$("#telefono_remitente").val(telefono_contacto);

	$("#ubicacion_doc").val(ubicacion_contacto);

	$("#direccion_doc").val(direccion_contacto);

	$("#mail_doc").val(mail_contacto);

	$('#sugerencias_empresa_destinatario_doc').slideUp("slow");

	buscar_destinatarios_empresa(nombre_contacto);
	$("#sugerencias_destinatario_doc").slideDown("slow");

	$("#datos_creacion_radicado").slideDown("slow");
	$("#botones_plantilla_radicacion_salida").slideDown("slow");

	$("#cargo_titular_doc").val("");
	$("#cargo_titular_doc").focus();
	$("#representante_legal1").html(representante_legal);

	sendPost();
}

function cargar_destinatario_radicado(representante_legal, codigo_contacto){
	$("#codigo_contacto").val(codigo_contacto);
	
	$("#destinatario_doc").val(representante_legal);

	$("#sugerencias_destinatario_doc").slideUp("slow");
	$("#sugerencias_destinatario_doc").html("");

	$("#representante_legal1").html(representante_legal);

	$("#datos_creacion_radicado").slideDown("slow");
	$("#botones_plantilla_radicacion_salida").slideDown("slow");

	$("#cargo_titular_doc").val("");
	$("#cargo_titular_doc").focus();

	sendPost();
}

function cambia_select_radicacion_salida(){
	sendPost();
}

$("#empresa_destinatario_doc").on("input",function(e){ // Accion que se activa cuando se digita #empresa_destinatario_doc
	$(".errores").slideUp("slow");
	loading('sugerencias_empresa_destinatario_doc');
	var destinatario = $("#empresa_destinatario_doc").val();
	if($(this).data("lastval")!= destinatario){
	    $(this).data("lastval",destinatario);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('empresa_destinatario_doc','capitales',0);
			$('#sugerencias_empresa_destinatario_doc').slideDown("slow");
			validar_empresa_destinatario_doc();
		},1000);
	};
});


$("#destinatario_doc").on("input",function(e){ // Accion que se activa cuando se digita #destinatario_doc
	$(".errores").slideUp("slow");
	loading('sugerencias_destinatario_doc');
	var destinatario = $(this).val();
	if($(this).data("lastval")!= destinatario){
	    $(this).data("lastval",destinatario);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('destinatario_doc','capitales',0);
			$('#sugerencias_destinatario_doc').slideDown("slow");
			validar_destinatario_doc();
		},1000);
	};
});

$("#telefono_remitente").on("input",function(e){ // Accion que se activa cuando se digita #telefono_remitente
	$(".errores").slideUp("slow");
	var telefono_destinatario = $(this).val();
	if($(this).data("lastval")!= telefono_destinatario){
	    $(this).data("lastval",telefono_destinatario);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('telefono_remitente','capitales',0);
			sendPost();
		},3000);
	};
});

$("#cargo_titular_doc").on("input",function(e){ // Accion que se activa cuando se digita #cargo_titular_doc
	$(".errores").slideUp("slow");
	var cargo_destinatario = $(this).val();   
	if($(this).data("lastval")!= cargo_destinatario){
	    $(this).data("lastval",cargo_destinatario);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('cargo_titular_doc','primera',0);
			sendPost();
		},3000);
	};
});

$("#ubicacion_doc").on("input",function(e){ // Accion que se activa cuando se digita #ubicacion_doc
	$(".errores").slideUp("slow");
	loading('sugerencias_ubicacion_doc');
	var ubicacion = $(this).val();	    
	if($(this).data("lastval")!= ubicacion){
	    $(this).data("lastval",ubicacion);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('ubicacion_doc','mayusculas',0);
			$('#sugerencias_ubicacion_doc').slideDown("slow");
			ubicacion = $("#ubicacion_doc").val()
			$.ajax({
				type 	: 'POST',
				url 	:  'admin_muni/buscador_municipios.php',
				data 	: {
					'search' 		: ubicacion,
					'formulario' 	: 'radicacion_salida'
				},
				success: function(resp){
					$("#sugerencias_ubicacion_doc").html(resp);
				}
			})
		},1000);
	};
});

function cargar_modifica_municipio(id,nombre_municipio,nombre_departamento,nombre_pais,nombre_continente){
	var ubicacion_completa = nombre_municipio+" ( "+nombre_departamento+" ) "+nombre_pais+" - "+nombre_continente;
	$("#ubicacion_doc").val(ubicacion_completa);
	sendPost();

	$("#sugerencias_ubicacion_doc").slideUp("slow");
	$("#sugerencias_ubicacion_doc").html("");

}

$("#direccion_doc").on("input",function(e){ // Accion que se activa cuando se digita #direccion_doc
	$(".errores").slideUp("slow");
	var direccion_destinatario = $(this).val(); 
	if($(this).data("lastval")!= direccion_destinatario){
	    $(this).data("lastval",direccion_destinatario);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('direccion_doc','primera',0);
			sendPost();
		},3000);
	};
});

$("#asunto_doc").on("input",function(e){ // Accion que se activa cuando se digita #asunto_doc
	$(".errores").slideUp("slow");
	var asunto = $(this).val();
	if($(this).data("lastval")!= asunto){
	    $(this).data("lastval",asunto);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('asunto_doc','primera',0);
			validar_input('asunto_doc');
			sendPost();
		},3000);
	};
});

$("#mail_doc").on("input",function(e){ // Accion que se activa cuando se digita #mail_doc
	$(".errores").slideUp("slow");
	var mail_documento = $(this).val();
	if($(this).data("lastval")!= mail_documento){
	    $(this).data("lastval",mail_documento);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('mail_doc','primera',0);
			validar_input('mail_doc');
			sendPost();
		},3000);
	};
});

$("#firmante_doc").on("input",function(e){ // Accion que se activa cuando se digita #firmante_doc
	$(".errores").slideUp("slow");
	loading('sugerencias_firmante');
	var nombre_firmante = $(this).val();
	if($(this).data("lastval")!= nombre_firmante){
	    $(this).data("lastval",nombre_firmante);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('firmante_doc','capitales',0);
	 		firmante = $("#firmante_doc").val();
	 		$('#sugerencias_firmante').slideDown("slideDown");
	 		if(firmante!=""){
		 		$.ajax({
					type 	: 'POST',
					url 	:  'include/procesar_ajax.php',
					data 	: {
						'recibe_ajax'  		: 'buscar_firmante',
						'tipo_busqueda'  	: 'carga_firmante',
						'nombre_buscado'	: firmante
					},
					success: function(resp){
						if(resp=="sin_registros"){
							$("#error_firmante").slideDown("slow");
							$('#sugerencias_firmante').slideUp("slow");
						}else{
							$("#error_firmante").slideUp("slow");
							$("#sugerencias_firmante").html(resp);
						}
					}
				})	
	 		}else{
				$("#firmante_doc_null").slideDown("slow");
				$("#sugerencias_firmante").slideUp("slow");
	 		}
		},1000);
	};
});

$("#cargo_firmante_doc").on("input",function(e){ // Accion que se activa cuando se digita #cargo_firmante_doc
	$(".errores").slideUp("slow");
	var cargo_firmante = $(this).val();
	if($(this).data("lastval")!= cargo_firmante){
	    $(this).data("lastval",cargo_firmante);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('cargo_firmante_doc','capitales',0);
			validar_input('cargo_firmante_doc');
			sendPost();
		},3000);
	};
});

$("#anexos_doc").on("input",function(e){ // Accion que se activa cuando se digita #anexos_doc
	$(".errores").slideUp("slow");
	var anexos_documento = $(this).val();
	if($(this).data("lastval")!= anexos_documento){
	    $(this).data("lastval",anexos_documento);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('anexos_doc','capitales',0);
			validar_input('anexos_doc');
			sendPost();
		},3000);
	};
});

$("#cc_doc").on("input",function(e){ // Accion que se activa cuando se digita #cc_doc
	$(".errores").slideUp("slow");
	var con_copia_documento = $(this).val();	    
	if($(this).data("lastval")!= con_copia_documento){
	    $(this).data("lastval",con_copia_documento);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('cc_doc','capitales',0);
			validar_input('cc_doc');
			sendPost();
		},3000);
	};
});


$("#aprueba_doc").on("input",function(e){ // Accion que se activa cuando se digita #aprueba_doc
	$(".errores").slideUp("slow");
	loading('sugerencias_aprueba');
	var nombre_aprobado_por = $(this).val();
	if($(this).data("lastval")!= nombre_aprobado_por){
	    $(this).data("lastval",nombre_aprobado_por);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('aprueba_doc','capitales',0);
	 		nombre_aprobado = $("#aprueba_doc").val();
	 		$('#sugerencias_aprueba').slideDown('slow');
	 		if(nombre_aprobado!=""){
		 		$.ajax({
					type 	: 'POST',
					url 	:  'include/procesar_ajax.php',
					data 	: {
						'recibe_ajax'  		: 'buscar_firmante',
						'tipo_busqueda'  	: 'carga_aprueba',
						'nombre_buscado'	: nombre_aprobado
					},
					success: function(resp){
						if(resp=="sin_registros"){
							$("#aprueba_doc_null").slideDown("slow");
							$('#sugerencias_aprueba').slideUp("slow");
						}else{
							$("#aprueba_doc_null").slideUp("slow");
							$("#sugerencias_aprueba").html(resp);
						}
					}
				})	
			}else{
				$("#aprueba_doc_null").slideDown("slow");
				$("#sugerencias_aprueba").slideUp("slow");
			}	
		},1000);
	};
});

$("#cargo_aprueba_doc").on("input",function(e){ // Accion que se activa cuando se digita #cargo_aprueba_doc
	$(".errores").slideUp("slow");
	var cargo_aprobador_documento = $(this).val();	    
	if($(this).data("lastval")!= cargo_aprobador_documento){
	    $(this).data("lastval",cargo_aprobador_documento);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('cargo_aprueba_doc','capitales',0);
			validar_input('cargo_aprueba_doc');
			sendPost();
		},3000);
	};
});

$("#elabora_doc").on("input",function(e){ // Accion que se activa cuando se digita #elabora_doc
	$(".errores").slideUp("slow");
	loading('sugerencias_elabora');
	var nombre_elaborado_por = $(this).val();  
	if($(this).data("lastval")!= nombre_elaborado_por){
	    $(this).data("lastval",nombre_elaborado_por);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			$('#sugerencias_elabora').slideDown("slow");
			espacios_formulario('elabora_doc','capitales',0);
	 		nombre_aprobado = $("#elabora_doc").val();
	 		$.ajax({
				type 	: 'POST',
				url 	:  'include/procesar_ajax.php',
				data 	: {
					'recibe_ajax'  		: 'buscar_firmante',
					'tipo_busqueda'  	: 'carga_elabora',
					'nombre_buscado'	: nombre_aprobado
				},
				success: function(resp){
					if(resp=="sin_registros"){
						$("#elabora_doc_null").slideDown("slow");
						$('#sugerencias_elabora').slideUp("slow");
					}else{
						$("#elabora_doc_null").slideUp("slow");
						$("#sugerencias_elabora").html(resp);
					}						
				}
			})	
		},1000);
	};
});

$("#cargo_elabora_doc").on("input",function(e){ // Accion que se activa cuando se digita #cargo_elabora_doc
	$(".errores").slideUp("slow");
	var cargo_elaborador_documento = $(this).val();   
	if($(this).data("lastval")!= cargo_elaborador_documento){
	    $(this).data("lastval",cargo_elaborador_documento);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			espacios_formulario('cargo_elabora_doc','capitales',0);
			validar_input('cargo_elabora_doc');
			sendPost();
		},3000);
	};
});


/* Funciones de validar inputs */ 
function buscar_destinatarios_empresa(empresa_entidad){
	$.ajax({
		type 	: 'POST',
		url 	:  'include/procesar_ajax.php',
		data 	: {
			'recibe_ajax'  		: 'buscar_destinatario',
			'tipo_busqueda'  	: 'buscar_destinatario_empresa',
			'nombre_buscado'	: empresa_entidad
		},
		success: function(resp){
			if($(".errores").is(":visible")){
				$('#sugerencias_destinatario_doc').slideUp("slow");
				return false;
			}else{
				$("#sugerencias_destinatario_doc").html(resp);
			}	
		}
	})	
}

function validar_destinatario_doc(){
	validar_input('destinatario_doc');
	destinatario_docu = $("#destinatario_doc").val();
		
	if(destinatario_docu!=""){
 		$.ajax({
			type 	: 'POST',
			url 	:  'include/procesar_ajax.php',
			data 	: {
				'recibe_ajax'  		: 'buscar_destinatario',
				'tipo_busqueda'  	: 'representante_legal',
				'nombre_buscado'	: destinatario_docu
			},
			success: function(resp){
				if($(".errores").is(":visible")){
					$('#sugerencias_destinatario_doc').slideUp("slow");
					return false;
				}else{
					$("#sugerencias_destinatario_doc").html(resp);
				}	
			}
		})	
	}else{
		$('#sugerencias_destinatario_doc').slideUp("slideUp");
	}
}

function validar_empresa_destinatario_doc(){
	validar_input('empresa_destinatario_doc');
	destinatario = $("#empresa_destinatario_doc").val();

	if(destinatario!=""){
 		$.ajax({
			type 	: 'POST',
			url 	:  'include/procesar_ajax.php',
			data 	: {
				'recibe_ajax'  		: 'buscar_destinatario',
				'tipo_busqueda'  	: 'empresa_entidad',
				'nombre_buscado'	: destinatario
			},
			success: function(resp){
				if($(".errores").is(":visible")){
					$('#sugerencias_empresa_destinatario_doc').slideUp("slow");
					return false;
				}else{
					$("#sugerencias_empresa_destinatario_doc").html(resp);
				}	
			}
		})	
	}else{
		$('#sugerencias_empresa_destinatario_doc').slideUp("slideUp");
	}
}
/* Fin funciones de validar inputs */


function carga_aprueba(nombre_completo,login_aprueba,cargo_destinatario){
	$("#aprueba_doc").val(nombre_completo);
	$("#aprueba_rs").html(nombre_completo);
	$("#cargo_aprueba_doc").val(cargo_destinatario);
	$("#elabora_doc").focus();
	$("#login_aprueba").val(login_aprueba);
	$("#sugerencias_aprueba").html("");
	$("#sugerencias_aprueba").slideUp("slow");

	var numero_radicado = $("#numero_radicado").val();
	var nombre_usuario = $("#nombre_usuario").val();
	$.ajax({
		type 	: 'POST',
		url 	:  'include/procesar_ajax.php',
		data 	: {
			'recibe_ajax'  		: 'validar_carga_aprueba_firma',
			'validar'  	 		: 'aprueba',
			'numero_radicado'	: numero_radicado
		},
		success: function(resp){
			if(resp=="NO"){
				if(nombre_completo==nombre_usuario){
					$("#indicador_aprobado").html("<div class='art_exp center' style='background: #2aa646;' title='Aprobar Documento'><img src='imagenes/iconos/aprobar_documento.png' height='35px;'></div>");
				}else{
					$("#indicador_aprobado").html("<div class='descripcion center'>"+nombre_completo+" <br>No ha aprobado todavía.</div>")
					console.log(nombre_completo+" debe firmarlo")
				}
			}else{
				
			}	
		}
	})	

	sendPost();
	
}
function carga_elabora(nombre_completo,login_elabora,cargo_destinatario){
	$("#cargo_elabora_doc").val(cargo_destinatario);
	$("#elabora_doc").val(nombre_completo);
	$("#elabora_rs").html(nombre_completo);
	$("#login_elabora").val(login_elabora);
	$("#sugerencias_elabora").html("");
	$("#sugerencias_elabora").slideUp("slow");
	$("#verPdf").focus();
	sendPost();
}
function carga_firmante(nombre_completo,login_firmante,cargo_destinatario){
	$("#aprueba_doc").focus();
	$("#cargo_firmante_doc").val(cargo_destinatario);
	$("#cargo_firmante_rs").html(nombre_completo);
	$("#firmante_doc").val(nombre_completo);
	$("#login_firmante").val(login_firmante);
	$("#sugerencias_firmante").html("");
	$("#sugerencias_firmante").slideUp("slow");
	sendPost();
}

function cargar_input_expediente(id_expediente,nombre_expediente){
	$("#id_expediente").val(id_expediente);
	$("#seleccionar_expediente").val(nombre_expediente);

	// $("#resultado_seleccionar_expediente").slideUp("slow");
}
/* Funciones para tinymce */
var html 	= "";
var i 		= 1;
var verpdf 	= false;

String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};


function loaderTiny(selector, height = 300, weight = 300, plugin){
	loader(selector, height,weight, plugin);
    var select = selector.replace("#", "").replace(".", "").replace(" ", "");
    window.editor = tinymce.get(select);
}

/* Funcion que se activa con el boton de "Vista previa del documento" o "Seguir editando documento" */
$("#verPdf").click(function (e) {
	if($(".errores").is(":visible")){
		return false;
	}else{		// Realizar la creación del documento
		/* Valida los campos obligatorios */
		validar_serie_subserie();
		validar_destinatario_doc();
		validar_empresa_destinatario_doc();

		validar_input('asunto_doc');

		validar_input_null("firmante_doc");
		validar_input_null("cargo_firmante_doc");
		validar_input_null("aprueba_doc");
		validar_input_null("cargo_aprueba_doc");
		validar_input_null("elabora_doc");
		validar_input_null("cargo_elabora_doc");
		validar_usuario_actual();

		/* Volver a cargar formulario*/
		sendPost();

		if($(".errores").is(":visible")){
			return false;
		}else{	
			if($(".resultado_busq_usuario").is(":visible")){
				Swal.fire({	
					position 			: 'top-end',
				    showConfirmButton 	: false,
				    timer 				: 1500,	
				    title 				: 'Debe seleccionar por lo menos',
				    text 				: 'Un usuario del listado',
				    type 				: 'warning'
				});
			}else{
				copiar_tinymce();
				var nuevo_nombre_documento = $("#nuevo_nombre_documento").val();
		        visorPdf("radicacion/radicacion_salida/templante.php",obj('completa'),"radicacion/radicacion_salida/src-php/pdf.php","bodega_pdf/plantilla_generada_tmp/"+nuevo_nombre_documento+".pdf","#visor","#example1","#verPdf");
			}
		}	
	}
});


/* Funcion que se activa con el boton "Generar Version XX del documento" */
$("#enviarHtml").click(function (form) {
	loading('botones_plantilla_radicacion_salida');

    if(verpdf){
        $("#example1").css("display","block");
        $("#visor").css("display","none");
        $("#verPdf").val("Seguir editando el documento");

        var nuevo_nombre_documento = $("#nuevo_nombre_documento").val();

        /* Genera la Versión del Pdf */
        copiar_tinymce();
	    enviar_pdf_final();
    }else{
        alert("Presionar (Seguir editando documento)")
    }
});

  //   function verHtmlVer(url){
  //   	$.post(url, function (request) {
		// 	  iframedoc.body.innerHTML = request;
		// },'html' );
  //   }


/* Accion cuando la casilla del Tinymce se hace keyup */
setTimeout(function () {
	window.editor.on("keyup", function (e){
       clearTimeout(timerid);
        timerid = setTimeout(function() {
            sendPost();
        }, 3000);
	    });
},500);


function sendPost(){
	html =  html_tinymce()
        .replaceAll("<!DOCTYPE html>","")
        .replaceAll("<html>","")
        .replaceAll("<head>","")
        .replaceAll("<body>","")
        .replaceAll("</html>","")
        .replaceAll("</head>","")
        .replaceAll("</body>",""); 
    visorHtml("radicacion/radicacion_salida/templante.php",obj('media'));
}
	
/* Funcion para tomar el html del tinymce */	
function html_tinymce(){
    return window.editor.getContent().trim();
};

	// function verPdf(selector, url, data, objectPdf = "#example1", viewPdf = "pdf.pdf") {
	function verPdf(selector, url, data, objectPdf, viewPdf) {
		console.log("Hay un viewPdf->"+viewPdf)
    	$.post("url", data, function (request) {
        	PDFObject.embed(viewPdf, objectPdf);
    	});
	}

	function visorHtml(url, data) {
	    $.post(url, data, function (request) {
	        let style = request
	            .replaceAll("<body>", "")
	            .replaceAll("</body>", "")
	            .replaceAll('<style type="text/css">', '<head><meta charset="UTF-8"><style type="text/css">')
	            .replaceAll("</head>", "</head><body>")
	            .replaceAll('<div id="footer">', '<div id="footer" style="display: none">')
	            .replaceAll('td{padding-left: 10px!important;padding-top: 35px!important;}', 'td{}')
	            .replaceAll("</b></b>", "")
	            .replaceAll("</html>","</body></html>")
	            .trim();
	        window.htmlPdf = style;
	        try{
	            iframedoc.body.innerHTML = style;
	        }catch (e) {

	        }
	    }, 'html');
	}

/* Funcion para generar el pdf final con codigo QR, generar radicado e histórico */
function enviar_pdf_final() {
	var nuevo_nombre_documento 	= $("#nuevo_nombre_documento").val();
    var nombre_usuario 			= $("#nombre_usuario").val();

	var url_genera_dompdf 		= "radicacion/radicacion_salida/savePdf.php";
	var url_nuevo_archivo 		= "bodega_pdf/plantilla_generada_tmp/"+nuevo_nombre_documento+".pdf";		
	var url_plantilla			= "radicacion/radicacion_salida/templante.php";

	/* Se crea un objeto y se le asignan atributos */
	var objeto3 						= new Object();

	objeto3.genera_pdf 					= "SI";
    objeto3.tipo_vista 					= "completa";

	objeto3.nameArchive 				= nuevo_nombre_documento;
	objeto3.usuario_radicador 			= nombre_usuario;

	objeto3.tipo_radicacion 			= $("#tipo_radicacion").val();
	objeto3.tipo_radicado 				= $("#tipo_radicado").val();
	objeto3.tratamiento_doc 			= $("#tratamiento_doc").val();
	objeto3.usuario_actual 				= $("#usuario_actual").val();
	objeto3.usuario_radicador 			= $("#lista_usuario_actual").val();
	objeto3.version_documento 			= $("#version_documento").val();
    objeto3.anexos_doc 					= $("#anexos_doc").val();
    objeto3.aprueba_doc 				= $("#aprueba_doc").val();
    objeto3.asunto_doc 					= $("#asunto_doc").val();
    objeto3.cargo_aprueba_doc 			= $("#cargo_aprueba_doc").val();
    objeto3.cargo_destinatario  		= $("#cargo_titular_doc").val();
    objeto3.cargo_elabora_doc 			= $("#cargo_elabora_doc").val();
    objeto3.cargo_firmante_doc 			= $("#cargo_firmante_doc").val();
    objeto3.cc_doc 						= $("#cc_doc").val();
    objeto3.codigo_contacto 			= $("#codigo_contacto").val();
    objeto3.codigo_serie 				= $("#codigo_serie").val();
    objeto3.codigo_subserie 			= $("#codigo_subserie").val();
    objeto3.despedida_doc 				= $("#despedida_doc").val();
    objeto3.destinatario 				= $("#destinatario_doc").val();
    objeto3.direccion_doc 				= $("#direccion_doc").val();
    objeto3.elabora_doc 				= $("#elabora_doc").val();
    objeto3.empresa_destinatario_doc 	= $("#empresa_destinatario_doc").val();
    objeto3.fecha 						= $("#fecha_doc").val();
    objeto3.firmante_doc 				= $("#firmante_doc").val();
    objeto3.footerImg 					= $("#footerImg").val();
    objeto3.headerImg 					= $("#headerImg").val();
    objeto3.id_expediente 				= $("#id_expediente").val();
    objeto3.mail_doc 					= $("#mail_doc").val();
    objeto3.nombre_dependencia 			= $("#nombre_dependencia").val();
    objeto3.numero_radicado 			= $("#numero_radicado").val();
    objeto3.pre_asunto 					= $("#pre_asunto").val();
    objeto3.telefono_doc 				= $("#telefono_remitente").val();
    objeto3.tratamiento 				= $("#tratamiento_doc").val();
    objeto3.ubicacion_doc 				= $("#ubicacion_doc").val();

    var firmante_doc 	= $("#firmante_doc").val();

	if(nombre_usuario==firmante_doc){ //nombre_usuario
        objeto3.firmaImg 	= $("#firmaImg").val();
	}

    objeto3.qrcode			= "" // Esta es la direccion a la cual se va a consultar el radicado PDF generado inicialmente, se cambia posteriormente
    objeto3.resultado_js 	= html_tinymce()
        .replaceAll("<!DOCTYPE html>","")
        .replaceAll("<html>","")
        .replaceAll("<head>","")
        .replaceAll("<body>","")
        .replaceAll("</html>","")
        .replaceAll("</head>","")
        .replaceAll("</body>",""); 

    /* Se envia mediante POST los valores de objeto3 para que genere el html del radicado. Luego lo encapsula y lo envía en la variable "html" de objeto3, el resultado lo envía a url_genera_dompdf */  
    $.post(url_plantilla, objeto3, function (request) {
        objeto3.html = request;

        $.ajax({
			type: 'POST',
			url:  url_genera_dompdf,
			data: objeto3,
		  	success: function(respuesta){
	        	$("#resultado_js").html(respuesta);
	        }
		})	

        // $.post(url_genera_dompdf, objeto3);
    }, 'html');
}
/* Fin funcion para generar el pdf final con codigo QR, generar radicado e histórico */

function visorPdf(url, data, postUrl, viewPdfUrl, visor, objectPdf, inputViewPdf) {
	var nuevo_nombre_documento = $("#nuevo_nombre_documento").val();

    if (!verpdf) { // Si al dar click en el botón "#verPdf" es hacer Vista previa del documento

        $.post(url, data, function (request) {
            var style = request
                .replaceAll('<div id="footer">', '<div id="footer" style="display: block">')
                .replaceAll('td{}', 'td{padding-left: 10px!important;padding-top: 35px!important;}')
                .replaceAll("<table>", "<br><br><table>")
                .replaceAll("</table>", "</table><br><br>")
                .replaceAll("</p>", "</p><br>")
                .replaceAll("</b></b>", "")
                .trim();
            viewPdf(postUrl, style, objectPdf, viewPdfUrl, nuevo_nombre_documento)
        }, 'html');

        $(visor).css("display", "none");
        $(objectPdf).css("display", "block"); //pdf
        $(inputViewPdf).val("Seguir editando documento");
        verpdf = true;
    
        $("#contenedor_boton_descargar_plantilla_respuesta").slideDown("slow"); // Mostrar el boton para generar version del documento
        $("#formulario_datos_radicado").animate({ // Para poner en 0% el width del formulario.
	    	width: "0%"
	    },{
	      	queue: false,
	      	duration: 500
	    })
	    $("#pdf").animate({ // Para volver al 100% el width de la tabla.
	    	width: "100%"
	    },{
	      	queue: false,
	      	duration: 500
	    })
	    $("#example1").animate({ // Para volver al 100% el width del PDF que se muestra como plantilla.
	    	width: "100%"
	    },{
	      	queue: false,
	      	duration: 500
	    })
    }else{ 	// Si al dar click en el botón "#verPdf" es Seguir editando el documento
	    	// console.log("Seguir editando documento 2")

        verpdf = false;
        visorHtml(url, data);
        $(visor).css("display", "block");
        $(objectPdf).css("display", "none"); //pdf
        $(inputViewPdf).val("Vista previa del documento");
        $("#contenedor_boton_descargar_plantilla_respuesta").slideUp("slow");

        sendPost();

        $("#formulario_datos_radicado").animate({ // Para volver al 50% el width de la tabla.
	    	width: "50%"
	    },{
	      	queue: false,
	      	duration: 500
	    })
	    $("#pdf").animate({ // Para volver al 50% el width de la tabla.
	    	width: "50%"
	    },{
	      	queue: false,
	      	duration: 500
	    })
	    $("#example1").animate({ // Para volver al 50% el width de la tabla.
	    	width: "50%"
	    },{
	      	queue: false,
	      	duration: 500
	    })
    }
}

	// function viewPdf(url, html, objectPdf = "#exmaple1", viewPdfUrl = "pdf.pdf") {
	function viewPdf(url, html, objectPdf, viewPdfUrl,nombre_archivo) {
	    $.post(url, {html: html, nombre_archivo:nombre_archivo}, function (request) {
	        PDFObject.embed(viewPdfUrl, objectPdf);
	    });
	}

	/* Funcion para cargar los plugins de tinymce */
	function loader(selector = "textarea", size = 300){
	    tinymce.init({
	        selector: "#editor",
	        plugins: 'autoresize,print preview fullpage paste importcss searchreplace save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
	        imagetools_cors_hosts: ['picsum.photos'],
	        menubar: 'file edit view insert format tools table help',
	        toolbar: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat',
	        toolbar_sticky: true,
	        autosave_ask_before_unload: true,
	        autosave_interval: "30s",
	        autosave_prefix: "{path}{query}-{id}-",
	        autosave_restore_when_empty: false,
	        autosave_retention: "2m",
	        image_advtab: true,
	        autoresize_bottom_margin: 50,
	       

	        image_class_list: [
	            {title: 'None', value: ''},
	            {title: 'Some class', value: 'class-name'}
	        ],
	        importcss_append: true,
	        templates: [
	            {
	                title: 'New Table',
	                description: 'creates a new table',
	                content: '<div class="mceTmpl"><table width="98%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
	            },
	            {title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...'},
	            {
	                title: 'New list with dates',
	                description: 'New List with dates',
	                content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
	            }
	        ],
	        template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
	        template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
	        height: size === undefined ? 300 : size,
	        image_caption: true,
	        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
	        noneditable_noneditable_class: "mceNonEditable",
	        toolbar_drawer: 'sliding',
	        contextmenu: "link image imagetools table",
	    });
	}

    function obj(tipo_vista) { 	// Crea un objeto (obj()) y le asigna los atributos.
        var obj 						= new Object();
        obj.fecha 						= $("#fecha_doc").val();
        obj.codigo_contacto 			= $("#codigo_contacto").val();
        obj.codigo_serie 				= $("#codigo_serie").val();
        obj.codigo_subserie 			= $("#codigo_subserie").val();
        obj.id_expediente 				= $("#id_expediente").val();
        obj.tratamiento 				= $("#tratamiento_doc").val();
        obj.destinatario 				= $("#destinatario_doc").val();
        obj.cargo_destinatario  		= $("#cargo_titular_doc").val();
        obj.empresa_destinatario_doc 	= $("#empresa_destinatario_doc").val();
        obj.telefono_doc 				= $("#telefono_remitente").val();
        obj.ubicacion_doc 				= $("#ubicacion_doc").val();
        obj.direccion_doc 				= $("#direccion_doc").val();
        obj.asunto_doc 					= $("#asunto_doc").val();
        obj.despedida_doc 				= $("#despedida_doc").val();
        obj.mail_doc 					= $("#mail_doc").val();
        obj.firmante_doc 				= $("#firmante_doc").val();
        obj.cargo_firmante_doc 			= $("#cargo_firmante_doc").val();
        obj.anexos_doc 					= $("#anexos_doc").val();
        obj.cc_doc 						= $("#cc_doc").val();
        obj.aprueba_doc 				= $("#aprueba_doc").val();
        obj.cargo_aprueba_doc 			= $("#cargo_aprueba_doc").val();
        obj.elabora_doc 				= $("#elabora_doc").val();
        obj.cargo_elabora_doc 			= $("#cargo_elabora_doc").val();
        obj.numero_radicado 			= $("#numero_radicado").val();
        obj.headerImg 					= $("#headerImg").val();
        obj.footerImg 					= $("#footerImg").val();
        obj.pre_asunto 					= $("#pre_asunto").val();
        obj.nombre_dependencia 			= $("#nombre_dependencia").val();

        obj.tipo_vista 					= tipo_vista;

        var nombre_usuario 	= $("#nombre_usuario").val();
        var firmante_doc 	= $("#firmante_doc").val();

		if(nombre_usuario==firmante_doc){ //nombre_usuario
	        obj.firmaImg 	= $("#firmaImg").val();
		}

        obj.qrcode			= "" // Esta es la direccion a la cual se va a consultar el radicado PDF generado inicialmente, se cambia posteriormente
        obj.resultado_js 	= html_tinymce()
            .replaceAll("<!DOCTYPE html>","")
            .replaceAll("<html>","")
            .replaceAll("<head>","")
            .replaceAll("<body>","")
            .replaceAll("</html>","")
            .replaceAll("</head>","")
            .replaceAll("</body>",""); 
        return obj;
    }

    function llenar_tinymce(){
    	var argumento = $("#pre_asunto").val();
    	tinymce.get('editor').setContent(argumento);
    }

    function copiar_tinymce(){
    	html =  html_tinymce()
            .replaceAll("<!DOCTYPE html>","")
            .replaceAll("<html>","")
            .replaceAll("<head>","")
            .replaceAll("<body>","")
            .replaceAll("</html>","")
            .replaceAll("</head>","")
            .replaceAll("</body>","");
        $("#pre_asunto").val(html);
    }
/* Fin funciones para tinymce */
/* Inicio funciones formulario modificar radicado salida */
function cargar_datos_modificar_radicado(){
	llenar_tinymce();

	var codigo_serie_mod 		= $("#codigo_serie_mod").val();
	var codigo_subserie_mod 	= $("#codigo_subserie_mod").val();
	var asunto_mod 				= $("#asunto_mod").val();
	var cargo_destinatario_mod 	= $("#cargo_destinatario_mod").val();
	var ubicacion_mod 			= $("#ubicacion_mod").val();
	var direccion_mod 			= $("#direccion_mod").val();
	var mail_mod 				= $("#mail_mod").val();
	var anexos_mod 				= $("#anexos_mod").val();
	var cc_mod 					= $("#cc_mod").val();
	var firmante_mod 			= $("#firmante_mod").val();
	var cargo_firmante_mod 		= $("#cargo_firmante_mod").val();
	var aprueba_mod 			= $("#aprueba_mod").val();
	var cargo_aprueba_mod 		= $("#cargo_aprueba_mod").val();
	var elabora_mod 			= $("#elabora_mod").val();
	var cargo_elabora_mod 		= $("#cargo_elabora_mod").val();
	var codigo_contacto_mod 	= $("#codigo_contacto_mod").val();

	$("#codigo_serie").val(codigo_serie_mod);
	$("#codigo_subserie").val(codigo_subserie_mod);
	$("#asunto_doc").val(asunto_mod);
	$("#cargo_titular_doc").val(cargo_destinatario_mod);
	$("#ubicacion_doc").val(ubicacion_mod);
	$("#direccion_doc").val(direccion_mod);
	$("#mail_doc").val(mail_mod);
	$("#anexos_doc").val(anexos_mod);
	$("#cc_doc").val(cc_mod);
	$("#firmante_doc").val(firmante_mod);
	$("#cargo_firmante_doc").val(cargo_firmante_mod);
	$("#aprueba_doc").val(aprueba_mod);
	$("#cargo_aprueba_doc").val(cargo_aprueba_mod);
	$("#elabora_doc").val(elabora_mod);
	$("#cargo_elabora_doc").val(cargo_elabora_mod);
	$("#codigo_contacto_mod").val(codigo_contacto_mod);
    $("#tabla_formulario_salida").slideDown("slow");
    $("#datos_creacion_radicado").slideDown("slow");

	$("#input_seleccionar_expediente").slideDown("slow");
	$("#botones_plantilla_radicacion_salida").slideDown("slow");

	setTimeout ("sendPost();", 500); 
}
function validar_usuario_actual(){
	var lista 		= $("#lista_usuario_actual").val();
	var firmante 	= $("#login_firmante").val();
	var aprueba 	= $("#login_aprueba").val();
	var elabora 	= $("#login_elabora").val();

	if(lista==firmante){
		firmante="";
	}
	if(lista==aprueba){
		aprueba="";
	}
	if(lista==elabora){
		elabora="";
	}
	if(firmante==aprueba){
		aprueba="";
	}
	if(firmante==elabora){
		elabora="";
	}
	if(aprueba==elabora){
		elabora="";
	}

	lista+=",";
	if(firmante!=""){
		lista+=firmante+",";
	}
	if(aprueba!=""){
		lista+=aprueba+",";
	}
	if(elabora!=""){
		lista+=elabora+",";
	}
	$("#usuario_actual").val(lista);
}

function cargar_aprueba(login,radicado,tipo){
	$("#ventana_aprobar_documento").slideDown("slow");
	$("#login_aprueba").val(login);
	$("#tipo_aprueba_firma").val(tipo);

	$("#contr_confirma_aprobado").focus();


	console.log(login + " - "+radicado+ " - "+tipo)
}

$("#asunto_doc").on("input",function(e){ // Accion que se activa cuando se digita #asunto_doc
	espacios_formulario('asunto_doc','primera',0);

	$(".errores").slideUp("slow");

	var asunto = $(this).val();
	    
	if($(this).data("lastval")!= asunto){
	    $(this).data("lastval",asunto);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_input('asunto_doc');
			sendPost();
		},3000);
	};
});

$("#observaciones_aprobar_documento").on("input",function(e){ // Accion que se activa cuando se digita #observaciones_aprobar_documento
	espacios_formulario('observaciones_aprobar_documento','primera',0);
	$(".errores").slideUp("slow");
	var asunto = $(this).val();
	    
	if($(this).data("lastval")!= asunto){
	    $(this).data("lastval",asunto);             
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_input('observaciones_aprobar_documento');
			sendPost();
		},3000);
	};
});

$("#contr_confirma_aprobado").on("input",function(e){ // Accion que se activa cuando se digita #contr_confirma_aprobado
	$(".errores").slideUp("slow");
});

function validar_aprueba_firma(){
	var login_aprueba 	= $("#login_aprueba").val();
	var pass_aprueba 	= $("#contr_confirma_aprobado").val();
	var tipo 		 	= $("#tipo_aprueba_firma").val();

	validar_input('observaciones_aprobar_documento');

	if($(".errores").is(":visible")){
		return false;
	}else{
		$.ajax({
	        type: 'POST',
	        url: 'include/procesar_ajax.php',
	        data: {
	            'recibe_ajax' 	: 'validar_aprueba_firma',
	            'tipo'		 	: tipo,
	            'login' 		: login_aprueba,
	            'pass'		 	: pass_aprueba
	        },          
	        success: function(respuesta){
	            if(respuesta!="true"){
	            	$("#error_contr_confirma_aprobado").slideDown("slow");
	                $("#contr_confirma_aprobado").focus();
	            }else{
	            	$("#error_contr_confirma_aprobado").slideUp("slow");
	            	var numero_radicado 					= $("#numero_radicado").val();
	            	var version_radicado 					= $("#version_documento").val();
					var observaciones_aprobar_documento 	= $("#observaciones_aprobar_documento").val();

					Swal.fire({
				        title 				: 'Va a '+tipo+' electrónicamente éste documento.',
				        text 				: "Esta acción no se puede revertir. ¿Está seguro?",
				        type 				: 'warning',
				        showCancelButton 	: true,
				        confirmButtonColor 	: '#3085d6',
				        cancelButtonColor 	: '#d33',
				        confirmButtonText 	: 'Si, '+tipo+' electrónicamente documento !',
				        cancelButtonText 	: 'Cancelar'

				    }).then((result) => {
				        if (result.value) {
				           	$.ajax({
						        type: 'POST',
						        url: 'include/procesar_ajax.php',
						        data: {
						            'recibe_ajax' 		: 'aprobar_firmar',
						            'tipo'		 		: tipo,
						            'numero_radicado' 	: numero_radicado,
						            'version_radicado' 	: version_radicado,
						            'observaciones'		: observaciones_aprobar_documento
						        },          
						        success: function(resp){
						        	$("#resultado_js").html(resp);
						        }
						    }) 
				        }
				    })
	            }
	        }
	    })
	}
}
/* Fin funciones formulario modificar radicado salida */
