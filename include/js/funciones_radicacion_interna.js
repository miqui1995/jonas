var numero_radicado_aleatorio = Math.floor(Math.random() * (99999 - 10000)) + 10000;
/*****************************************************************************************
	Inicio TinyMCE 
/*****************************************************************************************
	TinyMCE es un editor de texto WYSIWYG para HTML de código abierto que funciona completamente en JavaScript y se distribuye gratuitamente bajo licencia LGPL Al ser basado en JavaScript TinyMCE es independiente de la plataforma y se ejecuta en el navegador de internet. 
/*****************************************************************************************/

	tinymce.init({
	    selector: "#editor_radicacion_interna",
	    plugins: 'autoresize,print preview fullpage paste importcss searchreplace save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
	    imagetools_cors_hosts: ['picsum.photos'],
	    menubar: 'file edit view insert format tools table help',
	    toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
	    toolbar_sticky: true,
	    autosave_ask_before_unload: true,
	    autosave_interval: "30s",
	    autosave_prefix: "{path}{query}-{id}-",
	    autosave_restore_when_empty: false,
	    autosave_retention: "2m",
	    image_advtab: true,
	    autoresize_bottom_margin: 50,
	    content_css: [
	        '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
	        'assets/css/codepen.min.css'
	    ],

	    image_class_list: [
	        {title: 'None', value: ''},
	        {title: 'Some class', value: 'class-name'}
	    ],
	    importcss_append: true,
	    templates: [
	        {
	            title: 'New Table',
	            description: 'creates a new table',
	            content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
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
	    height: 300 === undefined ? 300 : 300,
	    image_caption: true,
	    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
	    noneditable_noneditable_class: "mceNonEditable",
	    toolbar_drawer: 'sliding',
	    contextmenu: "link image imagetools table",
	});

/*****************************************************************************************
	Fin TinyMCE 
/*****************************************************************************************/
	/* Evento en todos los id de la pagina */
	$(document).click(function(event){
		var id = event.target.id;
		if(id != "id_expediente"){
			if(id != "seleccionar_expediente"){
				if(id != "resultado_seleccionar_expediente"){
					if(id != "td_detalle_expediente"){
						if(id != "td_descipcion_expediente"){
							if(id != "codigo_subserie"){
								if(id != "opciones_serie_subserie"){
									$("#resultado_seleccionar_expediente").slideUp("slow");
								}
							}
						}
					}
				}	
			}
		}
	});
	/* Fin evento en todos los id de la pagina */
	$("#vista_completa").click(function() {
		$("#encabezado, #menu_izquierda, #pestanas_principal, #titulo_plantilla_radicacion_interna").hide();
		/*$("#menu_izquierda").hide();*/
		/*$("#pestanas_principal").hide();*/
		/*$("#titulo_plantilla_radicacion_interna").hide();*/
		$("#regeresar_pantalla_normal").show();
		$("#contenido").height("100vh");
		$("#contenido").width("100%");
		$("#indicador_aprobado").width("467");
		$("#example1").height("80vh");
	});
	$("#regeresar_pantalla_normal").click(function() {
		regresar_vista_normal();
	});
	$("#codigo_serie").change(function() {
		listar_serie_subserie(2, "");
	});
	$("#codigo_subserie").change(function() {
		if(!($("#tabla_formulario_salida").is(":visible"))){
    		$("#tabla_formulario_salida").slideDown("slow");// Mostramos el objeto con un efecto slow
			$("#botones_plantilla_radicacion_salida").slideDown("slow");// Mostramos el objeto con un efecto slow
			$("#verPdf").slideDown("slow");// Mostramos el objeto con un efecto slow
    	}
    	tratar_expediente();
		/*borrador_visorHtml();*/
	});







	$("#seleccionar_expediente").click(function() {
		abrir_resultados_expedientes();
	});
	$("#ubicacion").blur(function(){
		$("#ubicacion_resultados").slideUp("slow");
		validar_input('ubicacion');
	});
	$("#ubicacion").on('propertychange input', function () {
		enlistar_ubicacion();
		$("#ubicacion_null").slideUp("slow");
	});
	$("#quitar_ubicacion").click(function(){
		quitar_ubicacion();
	});
	$("#ubicacion2").blur(function() {
		validar_input('ubicacion2');
	});
	$("#ubicacion2").on('propertychange input', function () {
		enlistar_ubicacion2();
	});
	$("#quitar_ubicacion2").click(function(){
		quitar_ubicacion2();
	});
	$("#div_agregar_destinatario").click(function() {
		cargar_agregar_destinatarios();
	});
	$("#usuario_actual_nuevo_inf").on('propertychange input', function () {
		enlistar_destinatarios();
	});
	$("#usuario_actual_nuevo_inf").blur(function() {
		cerrar_agregar_destinatarios();
	});
	$("#asunto").on('propertychange input', function () {
		validar_asunto();
	});
	$("#asunto").blur(function(){
		validar_asunto();
	});
	$("#firmante").focus(function() {
		enlistar_firmantes();
	});
	$("#firmante").on('propertychange input', function () {
		enlistar_firmantes();
	});
	$("#firmante").blur(function(){
		validar_input('firmante');
		setTimeout(function(){
			$("#sugerencias_firmante").slideUp(1);
		}, 500);
	});
	$("#cargo_firmante").blur(function(){
		validar_input('cargo_firmante');
	});
	$("#aprueba").on('propertychange input', function () {
		enlistar_aprueba();
	});
	$("#aprueba").blur(function(){
		validar_input('id_aprueba');
		$("#aprueba_sin_resultados").slideUp("slow");
		$("#indicador_aprobado").slideDown("slow");
		$("#sugerencias_aprueba").slideUp("slow");
	});
	$("#cargo_aprueba").blur(function(){
		validar_input('cargo_aprueba');
	});
	$("#elabora").on('propertychange input', function () {
		enlistar_elaborado();
	});
	$("#elabora").blur(function(){
		validar_input('id_elabora');
		$('#sugerencias_elabora').slideUp('slow');
	});
	$("#cargo_elabora").blur(function(){
		validar_input('cargo_elabora');
	});
	$("#verPdf").click(function() {
		visorHtml(1);
	});
	$("#seguir_pdf").click(function(){
		seguir_editando();
	});
	$("#enviarHtml").click(function(){
		$("#botones_plantilla_radicacion_interna").html("<center><img src='imagenes/logo.gif' style='width: 100px;'></center>");
		visorHtml(2);
	});
	$("#aprobar_documento").click(function(){
		$("#ventana_aprobar_documento").slideDown("slow");
	});
/*****************************************************************************************
	Function cargar_documento(radicado) cargara los datos recibidos en los campos del formulario
/*****************************************************************************************
	* Recibe el numero de radicado y lo enviá mediante ajax, después de recibir los datos los escribe en cada campo con el llamado de funciones
	* @param {string} (radicado) Es obligatoria, nos dira de que radicado tomar los valores
	* @return Llena los campos del formulario con los datos recibidos
*****************************************************************************************/
function carga_info_radicacion_interna(tipo, radicado){
	if(tipo == 1){
		$.ajax({
		        type: 'POST',
		        url: 'include/procesar_ajax.php',
		        data:{
		            'recibe_ajax' 	: 'cargar_documento_radicacion_interna',
		            'radicado' 		: radicado
		        },
		        success: function(respuesta){
		        	respuesta = JSON.parse(respuesta);
		        	console.log(respuesta);
		        	listar_serie_subserie(1, respuesta['codigo_serie']);
		        	setTimeout(function() {
						listar_serie_subserie(2, respuesta['codigo_subserie']);
					}, 1000);
					$("#tabla_formulario_salida").slideDown("slow");// Mostramos el objeto con un efecto slow
					$("#botones_plantilla_radicacion_salida").slideDown("slow");// Mostramos el objeto con un efecto slow
					$("#verPdf").slideDown("slow");// Mostramos el objeto con un efecto slow
					quitar_ubicacion();
					agregar_ubicacion(respuesta['ubicacion']);
					$.ajax({
				        type: 'POST',
				        url: 'include/procesar_ajax.php',
				        data:{
				            'recibe_ajax' : 'info_dignatarios_radicacion_interna',
				            'dignatario'  : respuesta['dignatario'],
				            'cargo_dignatario' : respuesta['cargo_dignatario']
				        },
				        success: function(respuesta){
				            $("#resultado_js").html(respuesta);
				        }
				    });
					quitar_ubicacion2();
					agregar_ubicacion2(respuesta['ubicacion2']);
					$("#asunto").val(respuesta['asunto_documento']);
					setTimeout(function() {
						tinyMCE.get('editor_radicacion_interna').setContent(respuesta['html_asunto']);
					}, 1500);
					$.ajax({
				        type: 'POST',
				        url: 'include/procesar_ajax.php',
				        data:{
				            'recibe_ajax' : 'info_firmante_radicacion_interna',
				            'firmante'  : respuesta['firmante'],
					        'cargo_firmante' : respuesta['cargo_firmante']
				        },
				        success: function(respuesta){
				            $("#resultado_js").html(respuesta);
				        }
				    });
					if(respuesta['firmado'] == "NO"){
						$.ajax({
					        type: 'POST',
					        url: 'include/procesar_ajax.php',
					        data:{
					            'recibe_ajax' : 'info_firmado_radicacion_interna',
					            'firmante'  : respuesta['firmante']
					        },
					        success: function(respuesta){
					            if(respuesta){
					            	$("#ventana_firmar_documento").slideDown("slow");
					            }
					        }
					    });
					}
					if(respuesta['descripcion_anexos'] != "Sin anexos")$("#anexos").val(respuesta['descripcion_anexos']);
					$.ajax({
				        type: 'POST',
				        url: 'include/procesar_ajax.php',
				        data:{
				            'recibe_ajax' : 'info_aprueba_radicacion_interna',
				            'aprueba'  : respuesta['aprueba'],
				            'cargo_aprobado'  : respuesta['cargo_aprueba']
				        },
				        success: function(respuesta){
				            $("#resultado_js").html(respuesta);
				        }
				    });
				    alert("aa");
				    if(respuesta['aprobado'] == "NO"){
				    	alert("aa");
						$.ajax({
					        type: 'POST',
					        url: 'include/procesar_ajax.php',
					        data:{
					            'recibe_ajax' : 'info_aprobado_radicacion_interna',
					            'aprobado'  : respuesta['aprueba']
					        },
					        success: function(respuesta){
					            if(respuesta){
					            	$("#aprobar_documento").slideDown("slow");
					            }
					        }
					    });
					}
					$.ajax({
				        type: 'POST',
				        url: 'include/procesar_ajax.php',
				        data:{
				            'recibe_ajax' : 'info_elabora_radicacion_interna',
				            'elabora'  : respuesta['elabora'],
				            'cargo_elabora' : respuesta['cargo_elabora']
				        },
				        success: function(respuesta){
				            $("#resultado_js").html(respuesta);
				        }
				    });
		        }
		})
	}else{
		$.ajax({
	        type: 'POST',
	        url: 'include/procesar_ajax.php',
	        data:{
	            'recibe_ajax' : 'encabezado_piecero_radicacion_interna'
	        },
	        success: function(respuesta){
	            var base64 = respuesta.split("SEPARADOR");
	            $("#contenedor_imagen_cabecera").html(base64[0]);
	            $("#src_imagen_cabecera").val(base64[1]);
	            $("#piecera_mas").html(base64[2]);
	            $("#src_imagen_piecera").val(base64[3]);
	        }
	    });
		listar_serie_subserie(1, "");
		$("#version").val("0");
	}
}
/*****************************************************************************************
	Fin function cargar_documento(radicado) cargara los datos recibidos en los campos del formulario
/*****************************************************************************************/
var timerid = "";
var timerid2 = "";
window.htmlPdf = null;
var nombre_documento = Math.floor(Math.random() * (99999 - 10000)) + 10000;
var version2 = 1;








/*****************************************************************************************
	Function vista_completa() Ajustara la pantalla para que se vea de una forma mas amplia
/*****************************************************************************************
	* @return Esconde y muestra objetos para la reforma en aspecto en su relación a tamaño anterior
*****************************************************************************************/
function vista_completa(){
	$("#encabezado").hide();
	$("#menu_izquierda").hide();
	$("#pestanas_principal").hide();
	$("#titulo_plantilla_radicacion_interna").hide();
	$("#regeresar_pantalla_normal").show();
	$("#contenido").height("100vh");
	$("#contenido").width("100%");
	$("#indicador_aprobado").width("467");
	$("#example1").height("80vh");
}
/*****************************************************************************************
	Fin function vista_completa() Ajustara la pantalla para que se vea de una forma mas amplia
/*****************************************************************************************/
/*****************************************************************************************
	Function regresar_vista_normal() Ajustara la pantalla para que se vea normal
/*****************************************************************************************
	* @return Esconde y muestra objetos para reajustar tamaño de vista completa a vista normal
*****************************************************************************************/
function regresar_vista_normal(){
	$("#encabezado").show();
	$("#menu_izquierda").show();
	$("#pestanas_principal").show();
	$("#titulo_plantilla_radicacion_interna").show();
	$("#regeresar_pantalla_normal").hide();
	$("#contenido").height("75vh");
	$("#contenido").width("1154");
	$("#indicador_aprobado").width("300");
}
/*****************************************************************************************
	Fin function regresar_vista_normal() Ajustara la pantalla para que se vea normal
/*****************************************************************************************/
/*****************************************************************************************
	Function listar_serie_subserie() Crea las opciones a escoger para los select serie y subserie
/*****************************************************************************************
	* Toma los valores de la dependencia del usuario y el código de la serie seleccionada y los enviá mediante ajax, después de recibir los datos los escribe como código en su respectivo lugar en el html referenciado con el id o clase
	* @param {string} (tipo) Es obligatoria, si necesita en listar las series  el tipo es 1 o las subseries el tipo es 2
	* @param {string} (serie_subserie_existente) No es obligatoria, si es para editar recibe los datos para activar la opción en el select
	* @return Crea la estructura html de un select y sus opciones a escoger por petición html()
*****************************************************************************************/
function listar_serie_subserie(tipo_serie_o_subserie, serie_subserie_existente){
	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data:{
            'recibe_ajax'       		 	: 'listar_serie_subserie_radicacion_interna',
            'tipo_serie_o_subserie'  	 	: tipo_serie_o_subserie,// 1.Serie - 2.Subserie
            'serie_subserie_existente' 	 	: serie_subserie_existente,
            'serie_subserie_seleccionado' 	: $("select#codigo_serie").val()
        },
        success: function(respuesta){
            if(tipo_serie_o_subserie == 1){
            	$("#codigo_serie").html(respuesta);
            	$("#id_expediente").val("");
				$("#seleccionar_expediente").val("");
				listar_serie_subserie(2, "");
            }else{
            	$("#codigo_subserie").html(respuesta);
	        }
        }
    })
} 
/*****************************************************************************************
	Fin function listar_serie_subserie() Crea las opciones a escoger para los select serie y subserie
/*****************************************************************************************/
/*****************************************************************************************
	Function abrir_resultados_expedientes() Despliega los resultados de la búsqueda de los expedientes
/*****************************************************************************************
	* Despliega los resultados para poder cambiar de opción en cuanto al expediente
*****************************************************************************************/
function abrir_resultados_expedientes(){
	if(!($("#cartContent").html() === "")){
		if($('#resultado_seleccionar_expediente').css('display') == 'none'){
			if(!($("#resultado_seleccionar_expediente").html() === "")){
				$("#resultado_seleccionar_expediente").slideDown("slow");
				document.getElementById('resultado_seleccionar_expediente').scrollTop = 0;
			}
		}
		if($("#id_expediente").val() != ""){
			$("#sin_expediente").slideDown(1500);
		}else{
			$("#sin_expediente").slideUp(1);
		}
	}
	
}
/*****************************************************************************************
	Fin function abrir_resultados_expedientes() Despliega los resultados de la búsqueda de los expedientes
/*****************************************************************************************
/*****************************************************************************************
	Function cargar_expediente() después de seleccionar el expediente se llenara el input del expediente con el seleccionado
/*****************************************************************************************
	@param {string} (id_expediente) Es obligatoria, es el expediente representado con el código registrado en la base de datos
	* @return Escribe le valor en un value y limpia las sugerencias
*****************************************************************************************/
function cargar_expediente(id_expediente, nombre_expediente){
	$("#id_expediente").val(id_expediente);
	$("#seleccionar_expediente").val(nombre_expediente);
	$("#resultado_seleccionar_expediente").slideUp("slow");
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function cargar_expediente() después de seleccionar el expediente se llenara el input del expediente con el seleccionado
/*****************************************************************************************/
/*****************************************************************************************
	Function tratar_expediente() Revisa que hayan registros de expedientes en la base de datos
/*****************************************************************************************
	* Toma los valores de la serie y subserie escogida y los enviá mediante ajax, después de recibir los datos valida que hayan registros
	* @return escribe le resultado en el id o clase referenciado
*****************************************************************************************/
function tratar_expediente(){
	$.ajax({
		type 	: 'POST',
		url 	:  'include/procesar_ajax.php',
		data 	: {
			'recibe_ajax' 	: 'verificar_expediente_radicacion_interna',
			'serie' 		: $("select#codigo_serie").val(),
			'subserie'  	: $("select#codigo_subserie").val()
		},
		success: function(resp){
			$("#resultado_seleccionar_expediente").html(resp);
			abrir_resultados_expedientes();
		}
	})
}
/*****************************************************************************************
	Fin function tratar_expediente() Revisa que hayan registros de expedientes en la base de datos
/*****************************************************************************************/














































/*****************************************************************************************
	Function pre_visorHtml() Sirve de puente en algunos campos que se debe esperar un segundo para poder hacer la petición al visor Pdf en vista previa
/*****************************************************************************************/
function pre_visorHtml(){
	clearTimeout(timerid);
	timerid = setTimeout(function() {
		
	},1000);
}
/*****************************************************************************************
	Fin function pre_visorHtml() Sirve de puente en algunos campos que se debe esperar un segundo para poder hacer la petición al visor Pdf en vista previa
/*****************************************************************************************/
function enlistar_ubicacion(){
	$(".errores_ubicacion").slideUp("slow");
	clearTimeout(timerid);
	timerid = setTimeout(function() {
		var ubicacion 	= $("#ubicacion").val();
		if(ubicacion.length == 0){
			$('#ubicacion_resultados').slideUp("slow");
		}else{
			$.ajax({
				type: 'POST',
				url: 'include/procesar_ajax.php',
				data: {
					'recibe_ajax' 			: 'buscar_ubicacion',
		            'ubicacion_buscar' 		: ubicacion
				},			
				success: function(resp){
					$('#ubicacion_resultados').html(resp);
					$("#desplegable_resultados_inf").slideDown("slow");
					$("#ubicacion_resultados").slideDown("slow");
				}
			})
		}
	},500);
}
function agregar_ubicacion(ubicacion){
	$("#quitar_ubicacion").slideDown("slow");
	$('#ubicacion_resultados').slideUp("slow");
	$("#ubicacion").val(ubicacion);
	$("#ubicacion").prop('readonly', true);
}
function quitar_ubicacion(){
	$("#ubicacion").val("");
	$("#quitar_ubicacion").slideUp(1);
	$("#ubicacion").prop('readonly', false);
	$(".ubicacion_null").slideDown("slow");
}
function enlistar_ubicacion2(){
	$(".errores_ubicacion2").slideUp("slow");
	clearTimeout(timerid);
	timerid = setTimeout(function() {
		var ubicacion2 	= $("#ubicacion2").val();
		if(ubicacion2.length == 0){
			$('#ubicacion2_resultados').slideUp("slow");
		}else{
			$.ajax({
				type: 'POST',
				url: 'include/procesar_ajax.php',
				data: {
					'recibe_ajax' 			: 'buscar_ubicacion2',
		            'ubicacion_buscar' 		: ubicacion2
				},			
				success: function(resp){
					$('#ubicacion2_resultados').html(resp);
					$("#desplegable_resultados_inf2").slideDown("slow");
					$("#ubicacion2_resultados").slideDown("slow");
				}
			})

		}
	},500);
}
function agregar_ubicacion2(ubicacion){
	$("#quitar_ubicacion2").slideDown("slow");
	$('#ubicacion2_resultados').slideUp("slow");
	$("#ubicacion2").val(ubicacion);
	$("#ubicacion2").prop('readonly', true);
}
function quitar_ubicacion2(){
	$("#ubicacion2").val("");
	$("#quitar_ubicacion2").slideUp(1);
	$("#ubicacion2").prop('readonly', false);
	$(".ubicacion_null2").slideDown("slow");
}
function validar_cargos_destinatarios(){
	var id_destinatarios = $("#id_destinatarios_final").val() + '';
	var array_id_destinatarios = id_destinatarios.split(',');
	jQuery.each( array_id_destinatarios, function(i, val) {
	  	validar_cargo_destinatarios(val);
	});
}
function borrador_visorHtml(){
	var radicado 	= $("#radicado").val();
	var html 		= "<h1 align='center'>BORRADOR</h1>"
    $.post('radicacion/radicacion_interna/src-php/pdf.php', {
    	html: html, 
    	nombre_archivo: radicado
    });
    guardardocumento(true);
	/*
    radicado += "_"+nombre_documento;
    $.ajax({
		type: 'POST',
		url:  "radicacion/radicacion_interna/savePdf.php",
		data:{
				tipo_guardar: 1,// 1.Borrador - 2.Finalizar
				version: version,
				radicado: radicado,
		    	fecha: fecha,
		    	ubicacion: ubicacion,
		    	tratamiento: tratamiento,
		    	destinatarios: destinatarios_final,
		    	asunto: asunto,
		    	editor: editor2,
		    	despedida: despedida,
		    	anexos: anexos,
		    	firmante: firmante,
		    	cargo_firmante: cargo_firmante,
		    	aprueba: aprueba,
		    	cargo_aprueba: cargo_aprueba,
		    	elabora: elabora,
		    	cargo_elabora: cargo_elabora
		},
		success: function(resp){
			if(resp!=""){
				$('#resultado_js').html(resp);
			}
		}
	})*/
}
/*****************************************************************************************
	Function visorHtml() Recoge los datos del formulario y muestra en el visor de Pdf en vista previa
/*****************************************************************************************
	* Toma todos los valores del formulario y los enviá mediante una petición $.post, después de recibir los datos valida que hayan registros
	* @return Escribe en el visor Pdf el resultado de la petición
*****************************************************************************************/
function visorHtml(tipo){
	validar_input('ubicacion');
	validar_input('ubicacion2');
	validar_input('destinatarios_final');
	validar_cargo_destinatarios("");
	validar_asunto();
	validar_editar_textarea();
	validar_input('firmante_seleccionado');
	if($("#cargo_firmante").length > 0)validar_input('cargo_firmante');
	validar_input('id_aprueba');
	if($("#cargo_aprueba").length > 0)validar_input('cargo_aprueba');
	validar_input('id_elabora');
	if($("#cargo_elabora").length > 0)validar_input('cargo_elabora');
	if($(".errores").is(":visible")){
		return false;
	}else{
		var destinatarios 			= $("#destinatarios_final").val();
		var destinatarios_array 	= destinatarios.split(',');
		var id_destinatarios 		= $("#id_destinatarios_final").val();
		var id_destinatarios_array 	= id_destinatarios.split(',');
		var destinatarios_final 	= "";
		for(var i = 0; i <= id_destinatarios_array.length; i++){
			var cargo = $("#cargo_usuario_"+id_destinatarios_array[i]).val();
			if(cargo != undefined){
				destinatarios_final += destinatarios_array[i]+"<br>";		  
			  	destinatarios_final += cargo+"<br><br>";
		 	}
		}
		var destinatarios_final_2 = "";
		var cargo_destinatario_final_2 = "";
		for(var i = 0; i <= id_destinatarios_array.length; i++){
			var cargo = $("#cargo_usuario_"+id_destinatarios_array[i]).val();
			if(cargo != undefined){
				if(i != 0){
					destinatarios_final_2 += ",";
			  		cargo_destinatario_final_2 += ",";
				}
				destinatarios_final_2 += destinatarios_array[i];
			  	cargo_destinatario_final_2 += cargo;
		 	}
		}
		var editor  = tinyMCE.get('editor_radicacion_interna').getContent();
		var editor2 = editor.replaceAll("<!DOCTYPE html>", "")
					        .replaceAll("<html>", "")
					        .replaceAll("<head>", "")
					        .replaceAll("<body>", "")
					        .replaceAll("</html>", "")
					        .replaceAll("</head>", "")
					        .replaceAll("</body>", "");
	    $.ajax({
			type: 'POST',
			url: 'include/procesar_ajax.php',
			data: {
				'recibe_ajax' 					: 'generar_vista_previa_pdf_radicacion_interna',
				'tipo' 							: tipo,
				'ubicacion' 					: $("#ubicacion").val(),
				'ubicacion2' 					: $("#ubicacion2").val(),
				'anexos' 						: $("#anexos").val(),
				'src_imagen_cabecera' 			: $("#src_imagen_cabecera").val(),
				'fecha' 						: $("#fecha").val(),
				'tratamiento' 					: $("select#tratamiento").val(),
				'id_destinatarios'				: $("#id_destinatarios_final").val(),
				'destinatarios' 				: destinatarios_final,
				'destinatarios_final_2' 		: destinatarios_final_2,
				'cargo_destinatario_final_2' 	: cargo_destinatario_final_2,
				'asunto' 						: $("#asunto").val(),
				'editor2'						: editor2,
				'despedida'						: $("select#despedida").val(),
				'firmante' 						: $("#firmante").val(),
				'cargo_firmante' 				: $("#cargo_firmante").val(),
				'firmante_login' 				: $("#firmante_login").val(),
				'aprueba'						: $("#aprueba").val(),
		    	'cargo_aprueba'					: $("#cargo_aprueba").val(),
		    	'aprueba_login' 				: $("#aprueba_login").val(),
		    	'elabora' 						: $("#elabora").val(),
		    	'cargo_elabora' 				: $("#cargo_elabora").val(),
		    	'elabora_login' 				: $("#elabora_login").val(),	    	
		    	'src_imagen_piecera' 			: $("#src_imagen_piecera").val(),
		    	'version' 						: $("#version").val(),
		    	'numero_aleatorio'				: numero_radicado_aleatorio,
		    	'codigo_serie'					: $("select#codigo_serie").val(),
		    	'codigo_subserie'				: $("select#codigo_subserie").val(),
		    	'id_expediente'					: $("#id_expediente").val()
			},			
			success: function(resp){
				if(tipo === 1){
					var separador_login = resp.split("SEPARADORAJAX");
					PDFObject.embed('bodega_pdf/plantilla_generada_tmp/'+separador_login[0]+'/vista_previa_radicado_interno.pdf', '#example1');// Colocar el pdf en su lugar
				}else{
					var separador_login = resp.split("SEPARADORAJAX");
					$('#contenido').css({'z-index':'1'});// Modifico estilo para sobreponer a ventana modal
					var transaccion = "plantilla_interna";
					var id_expediente = $("#id_expediente").val();
					if(id_expediente != "")transaccion += "_expediente";
					alert(separador_login[2]);
					auditoria_general(transaccion, separador_login[2]);
				}
			}
		})
	    $("#tabla1").slideUp("slow");
	    $("#formulario_datos_radicado").slideUp("slow");
	    $("#verPdf").slideUp("slow");
		$("#example1").slideDown("slow");
		$("#seguir_pdf").slideDown("slow");
		$("#contenedor_boton_descargar_plantilla_respuesta").slideDown("slow");
	}
}
/*****************************************************************************************
	Fin function visorHtml() Recoge los datos del formulario y muestra en el visor de Pdf en vista previa
/*****************************************************************************************/
function seguir_editando(){
	$("#example1").slideUp("slow");
	$("#seguir_pdf").slideUp("slow");
	$("#contenedor_boton_descargar_plantilla_respuesta").slideUp("slow");
	$("#tabla1").slideDown("slow");
	$("#formulario_datos_radicado").slideDown("slow");
	$("#verPdf").slideDown("slow");
	$.ajax({
		type: 'POST',
		url: 'include/procesar_ajax.php',
		data: {
			'recibe_ajax' 			: 'eliminar_borrador'
		},			
		success: function(resp){
		}
	})
	$("#example1").html("");
}
function guardardocumento(borrador){
	var timerid3;
	regresar_vista_normal();
	if(!borrador)loading('botones_plantilla_radicacion_interna');
	var tipo_guardar = 2;
	if(borrador)tipo_guardar = 1;
	var version = $("#version").val();
	var version2 = version;
	if(version == 1 && !borrador){
		version2 = 2;
	}
	var serie 			= $("select#codigo_serie").val();
	var subserie 		= $("select#codigo_subserie").val();
	var id_expediente   = $("#id_expediente").val();
	var id_expediente2  = $("#id_expediente2").val();
	var radicado = $("#radicado").val();
	var fecha = $("#fecha").val();
	var tratamiento = $("select#tratamiento").val();
	var destinatarios = $("#destinatarios_final").val();
	var destinatarios_array = destinatarios.split(',');
	var id_destinatarios = $("#id_destinatarios_final").val();
	var id_destinatarios_array = id_destinatarios.split(',');
	var destinatarios_final = "";
	var cargo_destinatario_final = "";
	for(var i = 0; i <= id_destinatarios_array.length; i++){
		var cargo = $("#cargo_usuario_"+id_destinatarios_array[i]).val();
		if(cargo != undefined){
			if(i != 0){
				destinatarios_final += ",";
		  		cargo_destinatario_final += ",";
			}
			destinatarios_final += destinatarios_array[i];
		  	cargo_destinatario_final += cargo;
	 	}
	}
	var ubicacion = $("#ubicacion").val();
	var ubicacion2 = $("#ubicacion2").val();
	var asunto  = $("#asunto").val();
	clearTimeout(timerid3);
	
	timerid3 = setTimeout(function(){
		var editor  = tinyMCE.get('editor_radicacion_interna').getContent();
		var editor2 = editor.replaceAll("<!DOCTYPE html>","")
					        .replaceAll("<html>","")
					        .replaceAll("<head>","")
					        .replaceAll("<body>","")
					        .replaceAll("</html>","")
					        .replaceAll("</head>","")
					        .replaceAll("</body>","");
	
	    var despedida = $("select#despedida").val();
	    var anexos = $("#anexos").val();
	    if(anexos == "")anexos = "Sin Anexos";
	    var firmante = $("#firmante").val();
	    var firmante_login = $("#firmante_login").val();
	    var cargo_firmante = $("#cargo_firmante").val();
	    var aprueba = $("#aprueba").val();
	    var aprueba_login = $("#aprueba_login").val();
	    var cargo_aprueba = $("#cargo_aprueba").val();
	    var elabora = $("#elabora").val();
	    var elabora_login = $("#elabora_login").val();
	    var cargo_elabora = $("#cargo_elabora").val();
	    $.ajax({
			type: 'POST',
			url:  "radicacion/radicacion_interna/savePdf.php",
			data: {
					radicado: radicado,
					tipo_guardar: tipo_guardar,
					version: version,
					version2: version2,
					numero_radicado: nombre_documento,
					anexos: anexos,
					asunto: asunto,
					id_destinatarios: id_destinatarios,
					destinatarios: destinatarios_final,
					fecha: fecha,
					editor: editor2,
					despedida: despedida,
					tratamiento: tratamiento,				
					firmante: firmante,
					firmante_login: firmante_login,
					cargo_firmante: cargo_firmante,
					aprueba: aprueba,
					aprueba_login: aprueba_login,
			    	cargo_aprueba: cargo_aprueba,
			    	elabora: elabora,
			    	elabora_login: elabora_login,
			    	cargo_elabora: cargo_elabora,
			    	cargo_destinatarios: cargo_destinatario_final,
			    	serie: serie,
			    	subserie: subserie,
			    	id_expediente: id_expediente,
			    	id_expediente2: id_expediente2,
			    	ubicacion: ubicacion,
			    	ubicacion2: ubicacion2
			},
			success: function(resp){
				if(resp!=""){
					if(!borrador){
						$('#resultado_js').html(resp);
						$.ajax({
					        type: 'POST',
					        url: 'include/procesar_ajax.php',
					        data: {
					            'recibe_ajax'       : 'eliminar_borrador'
					        },
		    				success: function(respuesta_ajax_2){
		    				}
						})
					}
					$("#id_expediente2").val(id_expediente);
				}
			}
		})
	},6000);
}
/*****************************************************************************************
	Function cargar_agregar_destinatarios() Guardara los objetos innecesarios para agregar usuario y mostrara otros
/*****************************************************************************************
	* @return Esconde el botón de agregar y muestra el id y el input para agregar el usuario y pone el fucos en el input
*****************************************************************************************/
function cargar_agregar_destinatarios(){
  	$("#div_agregar_destinatario").slideUp(100);
	$("#input_agregar_usuario_inf").slideDown("slow");
	$("#usuario_actual_nuevo_inf").slideDown("slow");
	$("#usuario_actual_nuevo_inf").focus();
	$("#destinatarios_final_null").slideUp("slow");
	enlistar_destinatarios();
};
/*****************************************************************************************
	Fin function cargar_agregar_destinatarios() Guardara los objetos innecesarios para agregar usuario y mostrara otros
/*****************************************************************************************/
/*****************************************************************************************
	Function cerrar_agregar_destinatarios() Cierra el input de agregar destinatario
/*****************************************************************************************
/*****************************************************************************************
	* @return Esconde el input de agregar destinatarios y vuelve a mostear el botón para seguir agregando a los destinatarios
*****************************************************************************************/
function cerrar_agregar_destinatarios(){
	clearTimeout(timerid);
	timerid = setTimeout(function(){
		validar_input('destinatarios_final');
		$("#input_agregar_usuario_inf").slideUp(1);
		$(".errores_destinatarios").slideUp(1);
		$("#div_agregar_destinatario").slideDown("slow");
		$("#desplegable_resultados_inf").slideUp(1);
	},500);
};
/*****************************************************************************************
	Fin function cerrar_agregar_destinatarios() Cierra el input de agregar destinatario
/*****************************************************************************************/
/*****************************************************************************************
	Function enlistar_destinatarios() Toma los datos del usuario nuevo y los antiguos, los trata y genera los resultados de la búsqueda
/*****************************************************************************************
	* Toma los valores del input que están digitalizando y toma los usuarios ya guardados en el otro input invisible y los enviá mediante ajax
	* @return Escribe en las sugerencias de los destinatarios los resultados
*****************************************************************************************/
function enlistar_destinatarios(){
	$(".errores_destinatarios").slideUp("slow");
	clearTimeout(timerid);
	timerid = setTimeout(function() {
		var existencia_destinatario 	= 0;
		var usuario_actual_nuevo_inf 	= $("#usuario_actual_nuevo_inf").val();
		if(usuario_actual_nuevo_inf.length == 0){
			$('#desplegable_resultados_inf').html("");
			$('#desplegable_resultados_inf').slideUp(1);
		}else{
			var destinatarios_final 		= $("#destinatarios_final").val();
			$.ajax({
				type: 'POST',
				url: 'include/procesar_ajax.php',
				data: {
					'recibe_ajax' 			: 'buscar_destinatario_radicacion_interna',
		            'nombre_buscado' 		: usuario_actual_nuevo_inf,
		            'destinatarios_final'	: destinatarios_final
				},			
				success: function(resp){
					$('#desplegable_resultados_inf').html(resp);
				}
			})
		}
	},500);
};
/*****************************************************************************************
	Fin function enlistar_destinatarios() Toma los datos del usuario nuevo y los antiguos, los trata y genera los resultados de la búsqueda
/*****************************************************************************************/
/*****************************************************************************************
	Function agregar_destinatario() Guardara el usuario y creara la información del usuario para poderla visualizar mejor y completa
/*****************************************************************************************
	* @param {string} (id) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (foto) Es obligatoria, Se mostrara al usuario la foto del destinatario
	* @param {string} (nombre_largo) Es obligatoria, Se mostrara la información y se guardara en la base de datos
	* @param {string} (nombre_corto) Es obligatoria, Información del usuario destinatario
	* @param {string} (login) Es obligatoria, Información del usuario destinatario
	* @param {string} (codigo_dependencia) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (nombre_dependencia) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (cargo_usuario) Es obligatoria, nos dirá el cargo a poner en el otro input
	* @return Genera un espacio nuevo para el nuevo destinatario
*****************************************************************************************/
function agregar_destinatario(id, foto, nombre_largo, nombre_corto, login, codigo_dependencia, nombre_dependencia, cargo_usuario){
	$("#desplegable_resultados_inf").slideUp(1);
	$("#usuario_actual_nuevo_inf").slideUp(1);
	$("#usuario_actual_nuevo_inf").val("");
	var destinatarios = $("#destinatarios_final").val();
	if(destinatarios == ''){
		$("#destinatarios_final").val(nombre_largo);
	}else{
		$("#destinatarios_final").val(destinatarios+","+nombre_largo);
	}
	var id_destinatarios_final = $("#id_destinatarios_final").val();
	if(id_destinatarios_final == ''){
		$("#id_destinatarios_final").val(id);
	}else{
		$("#id_destinatarios_final").val(id_destinatarios_final+","+id);
	}
	var cargo_usuario2 = cargo_usuario;
	if(cargo_usuario == "")cargo_usuario2 = "Sin Cargo Asignado";
	$('#lista_usuarios_nuevos').append("<tr class='"+id+" li destinatario_usuario'>"+
											"<td>"+
												nombre_largo+
												"<br>"+
												"<input id='cargo_usuario_"+id+"' class='cargo_usuario_listar' value='"+cargo_usuario+"' placeholder='"+cargo_usuario2+"' oninput='validar_cargo_destinatarios(\""+id+"\")' onblur='validar_cargo_destinatarios(\""+id+"\")'>"+
												"<div id='cargo_usuario_"+id+"_null' class='errores'>"+
													"Debe ingresar el cargo"+
												"</div>"+
												"<div id='cargo_usuario_"+id+"_max' class='errores'>"+
													"El cargo no puede tener mas de 30 caracteres. (Actualmente <b><u id='cargo_usuario_"+id+"_contadormax'></u></b> caracteres)"+
												"</div>"+
												"<div class='info'>"+
													"<table id='mas_info'>"+
														"<tr>"+
															"<td id='foto_info' rowspan='2'>"+
																"<img id='foto_info_mas' src='"+foto+"'>"+
															"</td>"+
															"<td id='info_td'>"+
																nombre_largo+" - ("+login+")<br>"+
																"<b>("+cargo_usuario2+")</b>"+
															"</td>"+
														"</tr>"+
														"<tr>"+
															"<td>"+
																"<b> ("+codigo_dependencia+") - "+nombre_dependencia+"</b>"+
															"</td>"+
														"</tr>"+
													"</table>"+
												"</div>"+
											"</td>"+
											"<td class='boton_quitar_destinatarios'>"+
												"<div class='boton_cerrar_usuario' title='Quitar éste usuario del radicado' onclick=\"quitar_destinatario('"+id+"', '"+nombre_largo+"')\">"+
														"<img id='imagen_cerrar' src='imagenes/iconos/cerrar.png'>"+
												"</div>"+
												
											"</td>"+
										"</tr>");
	$("#div_agregar_destinatario").slideDown("slow");
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function agregar_destinatario() Guardara el usuario y creara la información del usuario para poderla visualizar mejor y completa
/*****************************************************************************************/
/*****************************************************************************************
	Function quitar_destinatario() Elimina el usuario destinado y con el se lleva los campos de cargo del usuario
/*****************************************************************************************
	* @param {string} (id) Es obligatoria, con este identificaremos el id que eliminaremos
	* @param {string} (nombre) Es obligatoria, sera el nombre que se borre del input de destinatarios
	* @return Quita el usuario que eliminaron, elimina el campo completo del destinatario y cargo
*****************************************************************************************/
function quitar_destinatario(id, nombre){
	var destinatarios 	= $("#destinatarios_final").val();
	var nombre_quitar 	= nombre+',';
	if(destinatarios.includes(nombre_quitar)){
		destinatarios = destinatarios.replace(nombre_quitar, '');
	}else{
		destinatarios = destinatarios.replace(nombre, '');		
	}
	$("#destinatarios_final").val(destinatarios);
	var id_destinatarios 	= $("#id_destinatarios_final").val();
	var id_quitar 	= id+',';
	if(id_destinatarios.includes(id_quitar)){
		id_destinatarios = id_destinatarios.replace(id_quitar, '');
	}else{
		id_destinatarios = id_destinatarios.replace(id, '');		
	}
	$("#id_destinatarios_final").val(id_destinatarios);
	$('#lista_usuarios_nuevos').find('.'+id).remove();
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function quitar_destinatario() Elimina el usuario destinado y con el se lleva los campos de cargo del usuario
/*****************************************************************************************/
/*****************************************************************************************
	Function validar_cargo_destinatarios() Valida el largo de la cadena y que no sea nula
/*****************************************************************************************
	* @param {string} (id) No es obligatoria, con este identificaremos el id que se validara, si es vació valida todo los cargos de los usuarios
	* @return Si se encuentra un error se despliega el error en medida del objeto y del caso
*****************************************************************************************/
function validar_cargo_destinatarios(id){
	var id_destinatarios_final = $("#id_destinatarios_final").val();
	if(id_destinatarios_final.length != 0){
		id_destinatarios_final = id_destinatarios_final.split(',');
		if(id != ""){
			var nombre_input = "cargo_usuario_"+id;
			var input = $('#' + nombre_input).val();
			espacios_formulario(nombre_input, 'primera');
	        if (input.length > 30) {
	            $('#' + nombre_input + '_max').slideDown('slow');
	            var contador_max = input.length;
	            $("#" + nombre_input + '_contadormax').html(contador_max);
	        }else{
	            $("#" + nombre_input + '_contadormax').html("");
	            $('#' + nombre_input + '_max').slideUp('slow');
	        }
	        validar_input_null(nombre_input);
	    }else{
	    	id_destinatarios_final.forEach( function(valor, indice, array) {
		    	var nombre_input = "cargo_usuario_"+valor;
				var input = $('#' + nombre_input).val();
				espacios_formulario(nombre_input, 'primera');
		        if (input.length > 30) {
		            $('#' + nombre_input + '_max').slideDown('slow');
		            var contador_max = input.length;
		            $("#" + nombre_input + '_contadormax').html(contador_max);
		        }else{
		            $("#" + nombre_input + '_contadormax').html("");
		            $('#' + nombre_input + '_max').slideUp('slow');
		        }
		        validar_input_null(nombre_input);
		    });
	    }
	}
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function validar_cargo_destinatarios() Valida el largo de la cadena y que no sea nula
/*****************************************************************************************
/*****************************************************************************************
	Function validar_asunto() Valida el largo de la cadena del asunto para ajustar el largo del visor Pdf
/*****************************************************************************************
	* @return Aumento o reducción del largo del visor Pdf
*****************************************************************************************/
function validar_asunto(){
	$("#asunto_max").slideUp(1);
	$("#asunto_min").slideUp(1);
	$("#asunto_null").slideUp(1);
	clearTimeout(timerid);
	timerid = setTimeout(function() {
		validar_input('asunto');
	}, 1000);
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function validar_asunto() Valida el largo de la cadena del asunto para ajustar el largo del visor Pdf
/*****************************************************************************************
/*****************************************************************************************
	Function validar_editar_textarea() Valida que el editor no este vació
/*****************************************************************************************
	* @return Si el editor esta vació habré el error de editor nulo
*****************************************************************************************/
function validar_editar_textarea(){
	var editor  = tinyMCE.get('editor_radicacion_interna').getContent();
	var editor2 = editor.replaceAll("<!DOCTYPE html>", "")
				        .replaceAll("<html>", "")
				        .replaceAll("<head>", "")
				        .replaceAll("<body>", "")
				        .replaceAll("</body>", "")
				        .replaceAll("</head>", "")
				        .replaceAll("</html>", "");
	if(editor2.length == 7){
		$("#editor_radicacion_interna_null").slideDown("slow");
	}else{
		$("#editor_radicacion_interna_null").slideUp("slow");
	}
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function validar_editar_textarea() Valida que el editor no este vació
/*****************************************************************************************/
/*****************************************************************************************
	Function enlistar_firmantes() Toma el valor ingresado por el usuario y genera los resultados de la búsqueda
/*****************************************************************************************
	* Recoge el valor digitado por el usuario y lo enviá mediante ajax
	* @return Escribe en las sugerencias de los firmantes los resultados
*****************************************************************************************/
function enlistar_firmantes(){
	clearTimeout(timerid);
	timerid = setTimeout(function() {
		var firmante = $("#firmante").val();
		if(firmante != ""){
	 		$.ajax({
				type 	: 'POST',
				url 	:  'include/procesar_ajax.php',
				data 	: {
					'recibe_ajax'  		: 'buscar_firmante_radicacion_interna',
					'tipo_busqueda'  	: 'cargar_firmante',
					'nombre_buscado'	: firmante
				},
				success: function(resp){
					if(resp=="sin_registros"){
						$("#error_firmante").slideDown("slow");
						$('#sugerencias_firmante').slideUp("slow");
					}else{
						$("#error_firmante").slideUp("slow");
						$("#sugerencias_firmante").html(resp);
						$('#sugerencias_firmante').slideDown("slideDown");
					}
				}
			})
	 	}else{
			$("#sugerencias_firmante").slideUp("slow");
	 	}
	 },1500);
};
/*****************************************************************************************
	Fin function enlistar_firmantes() Toma el valor ingresado por el usuario y genera los resultados de la búsqueda
/*****************************************************************************************/
/*****************************************************************************************
	Function cargar_firmante() Guardara el usuario y creara un espacio para la información para poderla visualizar mejor y completa
/*****************************************************************************************
	* @param {string} (id_usuario) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (codigo_dependencia) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (nombre_dependencia) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (nombre_completo) Es obligatoria, Se mostrara la información y se guardara en la base de datos
	* @param {string} (login) Es obligatoria, Información del usuario firmante
	* @param {string} (path_foto) Es obligatoria, Se mostrara al usuario la foto del firmante
	* @param {string} (cargo_usuario) Es obligatoria, Información del usuario firmante
*****************************************************************************************/
function cargar_firmante(id_usuario, codigo_dependencia, nombre_dependencia, nombre_completo, login, path_foto, cargo_usuario){
	$("#sugerencias_firmante").slideUp(1);
	$("#firmante").slideUp("slow");// Se esconde el input
	$("#firmante").val(nombre_completo);// Se da el valor del usuario con el nombre completo
	var nombre_corto = nombre_completo;
	if(nombre_completo.length >= 16){
		nombre_corto = nombre_completo.substring(0, 13)+'...';
	}
	var cargo_usuario2 = cargo_usuario;
	if(cargo_usuario2 == "") cargo_usuario2 = "Sin cargo asignado";
	$("#firmante_seleccionado").val(id_usuario);
	$("#firmante_login").val(login);
	$('#usuario_seleccionado_firmante').append("<div class='destinatario_usuario "+id_usuario+"' style='border: #2D9DC64F 2px solid; font-size: 20px; display: block; margin-bottom: 5%; background-color: #D2D2D23D; border-radius: 10px; height: 60px; padding: 5px;width: 450px'>"+
													"<div class='tab_a nombre_destinatario'>"+
														nombre_completo+
														"<input id='cargo_firmante' class='cargo_usuario_listar input_search' placeholder='"+cargo_usuario2+"' value='"+cargo_usuario+"' oninput='validar_input(\"cargo_firmante\")' style='margin-bottom: 3px; background-color: #0d080214; border: none; width: 90% !important;'>"+
														"<div id='cargo_firmante_max' class='errores'>"+
															"El cargo no puede pasar de 30. (Actualmente <b><u id='cargo_firmante_contadormax'></u></b> caracteres)"+
														"</div>"+
														"<div id='cargo_firmante_null' class='errores'>"+
															"Debe ingresar el cargo del firmante del documento"+
														"</div>"+
													"</div>"+
													"<div class='boton_cerrar_usuario_firmante' title='Quitar éste usuario del radicado' style='visibility: hidden; opacity: 0;'>"+
														"<img src='imagenes/iconos/cerrar.png' onclick=\"quitar_firmante('"+id_usuario+"')\" style='background-color:red; border: 1px solid red; border-radius: 10px; cursor: pointer; margin: -63px -7px 0px 0px; float: right; width: 30px;'>"+
													"</div>"+
													"<div class='info_firmante' style='visibility: hidden; opacity: 0; transition: visibility 0.001s, opacity 0.001s linear; margin: 1.5% 0px 0px 0%; border-radius: 16px; position: absolute; z-index: 1; cursor: default; background: #E0E3E7; width: 450px;'>"+
														"<table style='border: #2D9DC6 2px solid; border-radius:15px; font-size: 15px;'>"+
															"<tr>"+
																"<td rowspan=2 width='1%'>"+
																	"<img src='"+path_foto+"' style='width: 50px;border-radius: 10px;'>"+
																"</td>"+
																"<td width='39%'>"+
																	""+nombre_completo+"<br>("+login+")<br>"+
																	""+cargo_usuario2+
																"</td>"+
															"</tr>"+
															"<tr>"+
																"<td>"+
																	"<b> ("+codigo_dependencia+") - "+nombre_dependencia+" </b>"+
																"</td>"+
															"</tr>"+
														"</table>"+
													"</div>"+
												"</div>");
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function cargar_firmante() Guardara el usuario y creara un espacio para la información para poderla visualizar mejor y completa
/*****************************************************************************************/
/*****************************************************************************************
	Function quitar_firmante() Elimina el usuario firmante y con el se lleva los campos del firmante
/*****************************************************************************************
	* @param {string} (id) Es obligatoria, con este identificaremos el id que eliminaremos
	* @return Elimina la información, vaciá y muestra el input para agregar otro firmante
*****************************************************************************************/
function quitar_firmante(id){
	$('#usuario_seleccionado_firmante').find('.'+id).remove();
	$("#firmante").val("");
	$("#firmante").slideDown("slow");
	$("#cargo_firmante_rs").html(" del Firmante");
	$("#cargo_firmante").val("");
	$("#firmante_login").val("");
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function quitar_firmante() Elimina el usuario firmante y con el se lleva los campos del firmante
/*****************************************************************************************/
/*****************************************************************************************
	Function enlistar_aprueba() Toma el valor ingresado por el usuario y genera los resultados de la búsqueda
/*****************************************************************************************
	* Recoge el valor digitado por el usuario y lo enviá mediante ajax
	* @return Escribe en las sugerencias del usuario que aprueba los resultados
*****************************************************************************************/
function enlistar_aprueba(){
	clearTimeout(timerid);
	timerid = setTimeout(function() {
		var aprobado_por = $("#aprueba").val();
		$("#id_aprueba_null").slideUp("slow")
		$(".errores").slideUp("slow");
		$('#sugerencias_aprueba').slideDown('slow');
		if(aprobado_por != ""){
	 		$.ajax({
				type 	: 'POST',
				url 	:  'include/procesar_ajax.php',
				data 	: {
					'recibe_ajax'  		: 'buscar_firmante_radicacion_interna',
					'tipo_busqueda'  	: 'cargar_aprueba',
					'nombre_buscado'	: aprobado_por
				},
				success: function(resp){
					if(resp=="sin_registros"){
						$("#aprueba_sin_resultados").slideDown("slow");
						$('#sugerencias_aprueba').slideUp("slow");
					}else{
						$("#indicador_aprobado").slideUp("slow");
						$("#aprueba_sin_resultados").slideUp("slow");
						$("#sugerencias_aprueba").html(resp);
					}
				}
			})	
	 	}else{
			$("#aprueba_sin_resultados").slideDown("slow");
			$("#sugerencias_aprueba").slideUp("slow");
	 	}
	 },1500);
}
/*****************************************************************************************
	Fin function enlistar_aprueba() Toma el valor ingresado por el usuario y genera los resultados de la búsqueda
/*****************************************************************************************/
/*****************************************************************************************
	Function cargar_aprueba() Guardara el usuario y creara un espacio para la información para poderla visualizar mejor y completa
/*****************************************************************************************
	* @param {string} (id_usuario) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (codigo_dependencia) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (nombre_dependencia) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (nombre_completo) Es obligatoria, Se mostrara la información y se guardara en la base de datos
	* @param {string} (login) Es obligatoria, Información del usuario aprueba
	* @param {string} (path_foto) Es obligatoria, Se mostrara al usuario la foto del aprueba
	* @param {string} (cargo_usuario) Es obligatoria, Información del usuario aprueba
*****************************************************************************************/
function cargar_aprueba(id_usuario, codigo_dependencia, nombre_dependencia, nombre_completo, login, path_foto, cargo_usuario){
	$("#id_aprueba_null").slideUp(1);
	$("#sugerencias_aprueba").slideUp(1);
	$("#aprueba").slideUp(1);// Se esconde el input
	$("#aprueba").val(nombre_completo);// Se da el valor del usuario con el nombre completo
	var nombre_corto = nombre_completo;
	if(nombre_completo.length >= 20) nombre_corto = nombre_completo.substring(0, 17)+'...';
	var cargo_usuario2 = cargo_usuario;
	if(cargo_usuario2 == "") cargo_usuario2 = "Sin cargo asignado";
	$("#id_aprueba").val(id_usuario);
	$("#aprueba_login").val(login);
	$('.aprueba').append("<div class='destinatario_usuario "+ id_usuario +"' style='border: #2D9DC64F 2px solid; font-size: 18px; margin-left: 11px; background-color: #D2D2D23D; border-radius: 10px; padding: 5px; width: 300px; height: 20px;'>"+
							"<div class='tab_a nombre_destinatario' style='float: left; width: 150px; height: 25px;'>"+
								nombre_corto +" - "+
							"</div>"+
							"<input id='cargo_aprueba' oninput='validar_input(\"cargo_aprueba\");' value='"+ cargo_usuario +"' placeholder='"+ cargo_usuario2 +"' style='margin-top: -2px; border: none; padding: 5px; width: 127px; background-color: #0d080214; border-radius: 8px;'>"+	
							"<div id='cargo_aprueba_null' style='float: right;' class='errores'>Falta Cargo</div>"+
							"<div id='cargo_aprueba_max' class='errores'>El cargo no puede pasar de 30. (Actualmente <b><u id='cargo_aprueba_contadormax'></u></b> caracteres)</div>"+
							"<div class='boton_cerrar_usuario3' title='Quitar éste usuario del radicado' style='float: right; visibility: hidden; opacity: 0; background-color:red; border: 1px solid red; border-radius: 10px; height: 25px; margin: -32px -7px 0px 0px; cursor: pointer;'>"+
								"<img src='imagenes/iconos/cerrar.png' class='img_cerrar' onclick=\"quitar_aprueba('"+ id_usuario +"')\" style='width: 25px; position: initial !important;'>"+
							"</div>"+
							"<div class='info3' style='visibility: hidden; opacity: 0; transition: visibility 0.001s, opacity 0.001s linear; margin: 1.5% 0px 0px 0%; border-radius: 16px; position: absolute; z-index: 1; cursor: default; background: #E0E3E7; width: 300px; border: #2D9DC6 2px solid; font-size: 15px;'>"+
								"<table border: #2D9DC6 2px solid; border-radius:15px;'>"+
									"<tr>"+
										"<td rowspan=2 width='1%'>"+
											"<img src='"+ path_foto +"' style='width: 50px;border-radius: 10px;'>"+
										"</td>"+
										"<td width='39%'>"+
											nombre_completo +" - ("+ login +")<br>"+
											cargo_usuario2 +
										"</td>"+
									"</tr>"+
									"<tr>"+
										"<td>"+
											"<b> ("+codigo_dependencia+") - "+nombre_dependencia+" </b>"+
										"</td>"+
									"</tr>"+
								"</table>"+
							"</div>"+
						"</div>");
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function cargar_aprueba() Guardara el usuario y creara un espacio para la información para poderla visualizar mejor y completa
/*****************************************************************************************/
/*****************************************************************************************
	Function quitar_aprueba() Elimina el usuario que aprueba y con el se lleva los campos del que aprueba
/*****************************************************************************************
	* @param {string} (id) Es obligatoria, con este identificaremos el id que eliminaremos
	* @return Elimina la información, vaciá y muestra el input para agregar otro usuario que aprueba
*****************************************************************************************/
function quitar_aprueba(id){
	$("#aprobar_documento").slideUp("slow");
	$(".adicion_espacio_usuario_aprueba").remove();
	$('#usuario_seleccionado_aprueba').find('.'+id).remove();
	$("#id_aprueba").val("");
	$("#aprueba").val("");
	$("#aprueba").slideDown("slow");
	$(".rs_aprobado").html("");
	$("#aprueba_rs").html(" de quien aprueba éste documento");
	$("#cargo_aprueba").val("");
	$("#aprueba_login").val("");
	$(".aprobado_nuevo").html("l usuario");
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function quitar_aprueba() Elimina el usuario que aprueba y con el se lleva los campos del que aprueba
/*****************************************************************************************/
/*****************************************************************************************
	Function enlistar_elaborado() Toma el valor ingresado por el usuario y genera los resultados de la búsqueda
/*****************************************************************************************
	* Recoge el valor digitado por el usuario y lo enviá mediante ajax
	* @return Escribe en las sugerencias del usuario que aprueba los resultados
*****************************************************************************************/
function enlistar_elaborado(){
	clearTimeout(timerid);
	timerid = setTimeout(function() {
		var elabora = $("#elabora").val();
		$(".errores").slideUp("slow");
		$("#id_elabora_null").slideUp("slow");
		$('#sugerencias_elabora').slideDown("slow");
		if(elabora != ""){
	 		$.ajax({
				type 	: 'POST',
				url 	:  'include/procesar_ajax.php',
				data 	: {
					'recibe_ajax'  		: 'buscar_firmante_radicacion_interna',
					'tipo_busqueda'  	: 'cargar_elabora',
					'nombre_buscado'	: elabora
				},
				success: function(resp){
					if(resp=="sin_registros"){
						$("#elabora_null").slideDown("slow");
						$('#sugerencias_elabora').slideUp("slow");
					}else{
						$("#elabora_null").slideUp("slow");
						$("#sugerencias_elabora").html(resp);
					}
				}
			})	
	 	}else{
			$("#elabora_null").slideDown("slow");
			$("#sugerencias_elabora").slideUp("slow");
	 	}
	},1500);
}
/*****************************************************************************************
	Fin function enlistar_elaborado() Toma el valor ingresado por el usuario y genera los resultados de la búsqueda
/*****************************************************************************************/
/*****************************************************************************************
	Function cargar_elabora() Guardara el usuario y creara un espacio para la información para poderla visualizar mejor y completa
/*****************************************************************************************
	* @param {string} (id_usuario) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (codigo_dependencia) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (nombre_dependencia) Es obligatoria, con este identificaremos el id que genera
	* @param {string} (nombre_completo) Es obligatoria, Se mostrara la información y se guardara en la base de datos
	* @param {string} (login) Es obligatoria, Información del usuario que labora
	* @param {string} (path_foto) Es obligatoria, Se mostrara al usuario la foto del usuario que labora
	* @param {string} (cargo_usuario) Es obligatoria, Información del usuario que labora
*****************************************************************************************/
function cargar_elabora(id_usuario, codigo_dependencia, nombre_dependencia, nombre_completo, login, path_foto, cargo_usuario){
	$("#id_elabora_null").slideUp(1);
	$("#sugerencias_elabora").slideUp(1);
	$("#elabora").slideUp("slow");// Se esconde el input
	$("#elabora").val(nombre_completo);// Se da el valor del usuario con el nombre completo
	var nombre_corto = nombre_completo;
	if(nombre_completo.length >= 20) nombre_corto = nombre_completo.substring(0, 17)+'...';
	var cargo_usuario2 = cargo_usuario;
	if(cargo_usuario2 == "") cargo_usuario2 = "Sin cargo asignado";
	$("#id_elabora").val(id_usuario);
	$("#elabora_login").val(login);
	$('.usuario_elabora').append("<div class='destinatario_usuario "+ id_usuario +"' style='border: #2D9DC64F 2px solid; font-size: 18px; margin-left: 13px; background-color: #D2D2D23D; border-radius: 10px; padding: 5px; width: 300px; height: 20px;'>"+
									"<div style='float: left; width: 150px; height: 25px;'>"+
										nombre_corto +" - "+
									"</div>"+
									"<input id='cargo_elabora' oninput='validar_input(\"cargo_elabora\");' value='"+ cargo_usuario +"' placeholder='"+ cargo_usuario2 +"' style='margin-top: -2px; border: none; padding: 5px; width: 127px; background-color: #0d080214; border-radius: 8px;'>"+	
									"<div id='cargo_elabora_null' class='errores' style='float:right'>Falta Cargo</div>"+
									"<div id='cargo_elabora_max' class='errores'>El cargo no puede pasar de 30. (Actualmente <b><u id='cargo_elabora_contadormax'></u></b> caracteres)</div>"+
									"<div class='boton_cerrar_usuario3' title='Quitar éste usuario del radicado' style='float: right; visibility: hidden; opacity: 0; background-color:red; border: 1px solid red; border-radius: 10px; height: 25px; margin: -32px -7px 0px 0px; cursor: pointer;'>"+
										"<img src='imagenes/iconos/cerrar.png' class='img_cerrar' onclick=\"quitar_elabora('"+ id_usuario +"')\" style='width: 25px; position: initial !important;'>"+
									"</div>"+
									"<div class='info3' style='visibility: hidden; opacity: 0; transition: visibility 0.001s, opacity 0.001s linear; margin: 1.5% 0px 0px 0%; border-radius: 16px; position: absolute; z-index: 1; cursor: default; background: #E0E3E7; width: 300px; border: #2D9DC6 2px solid; font-size: 15px;'>"+
										"<table border: #2D9DC6 2px solid; border-radius:15px;'>"+
											"<tr>"+
												"<td rowspan=2 width='1%'>"+
													"<img src='"+ path_foto +"' style='width: 50px;border-radius: 10px;'>"+
												"</td>"+
												"<td width='39%'>"+
													nombre_completo +" - ("+ login +")<br>"+
													cargo_usuario2 +
												"</td>"+
											"</tr>"+
											"<tr>"+
												"<td>"+
													"<b> ("+codigo_dependencia+") - "+nombre_dependencia+" </b>"+
												"</td>"+
											"</tr>"+
										"</table>"+
									"</div>"+
								"</div>");
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function cargar_elabora() Guardara el usuario y creara un espacio para la información para poderla visualizar mejor y completa
/*****************************************************************************************/
/*****************************************************************************************
	Function quitar_elabora() Elimina el usuario que elabora y con el se lleva los campos del usuario que elabora
/*****************************************************************************************
	* @param {string} (id) Es obligatoria, con este identificaremos el id que eliminaremos
	* @return Elimina la información, vaciá y muestra el input para agregar otro usuario que elabora
*****************************************************************************************/
function quitar_elabora(id){
	$('.usuario_elabora').find('.'+id).remove();
	$("#id_elabora").val("");
	$("#elabora").val("");
	$("#elabora").slideDown("slow");
	$("#elabora_rs").html("quien elabora éste documento");
	$("#cargo_elabora").val("");
	$("#elabora_login").val("");
	/*borrador_visorHtml();*/
}
/*****************************************************************************************
	Fin function quitar_elabora() Elimina el usuario que elabora y con el se lleva los campos del usuario que elabora
/*****************************************************************************************/	