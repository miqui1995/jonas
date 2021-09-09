<?php 
/* Este archivo es el index de la radicación de resoluciones. */
/* Se inicia con validar inactividad y generar fecha para traducir timestamp a fecha legible */
	require_once("../../login/validar_inactividad.php");
	require_once("../../include/genera_fecha.php");
	// var_dump($_SESSION);

	$cargo_usuario = $_SESSION['cargo_usuario'];
	/* Se valida si el usuario tiene el cargo asignado para poder generar radicados. Esto facilita que todos los usuarios del sistema tengan el camopo de "cargo_usuario" diligenciado ya que no es un campo obligatorio pero si muy necesario para generar firmas o para generar documentos. */
	if(!isset($cargo_usuario)){
		echo "<script language=javascript>
			Swal.fire({		
					position 			: 'top-end',
			    	showConfirmButton 	: false,
			    	timer 				: 3500,
				    title 				: 'SU USUARIO NO TIENE CARGO EN EL SISTEMA',
				    text 				: 'Para poder generar documentos es necesario que ingrese su cargo.',
				    type 				:'warning'
				}).then(function(isConfirm){
					$('#contenido').load('login/gestionar_datos_usuario.php');
				});
		</script>";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Plantilla radicacion salida, interna, respuestas</title>
	<!-- Enlace hacia librerias tinymce  -->
	<script type="text/javascript" src="include/js/tinymce.min.js"></script>
	<script type="text/javascript" src="include/js/lang/es.js"></script>
<!-- 	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.0.943/pdf.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.js"></script> -->

    <link rel="shortcut icon" href="imagenes/logo3.png">
	<link rel="stylesheet" href="include/css/estilos_radicacion_salida.css">
<!--  
    <link type="text/css" href="radicacion/radicacion_salida/assets/css/style.css" rel="stylesheet" media="screen"/>
    <link type="text/css" href="radicacion/radicacion_salida/assets/css/imagestyle.css"> -->
</head>
<script>
	/* Valida que la secuencia de resoluciones exista */
	valida_sec('5');

	var verpdf 	= false;

	/* Contador para los input cuando hacen onkeyup y cuenta 1 segundo para hacer el cambio */
	var timerid="";

	/* Se define el valor de serie. Si este valor si no está definido en el formulario quiere decir es un documento nuevo; en caso contrario, carga los datos del documento mediante la funcion 'cargar_datos_modificar_resolucion()' */
	var serie = $("#codigo_serie_mod").val();
	if(serie==""){
		<?php echo "setTimeout('consulta_listado_series2(\"\",\"$codigo_dependencia\",\"codigo_serie\");', 500);" ?>
	}else{
		setTimeout ("cargar_datos_modificar_resolucion()", 1500); // Cargar datos para modificar resolucion
	}
	/* Variable para iniciar el frame donde se muestra el PDF */
	// var iframe = document.getElementById('visor'), iframedoc = iframe.contentDocument || iframe.contentWindow.document; //visor iframe

	/* Calcula numero aleatorio para adicionar al nombre del documento */
	var nuevo_nombre_documento = Math.floor(Math.random() * (99999 - 10000)) + 10000;
	$("#nuevo_nombre_documento").val(nuevo_nombre_documento);

	/* Al cargar el contenido del html carga las librerias de TinyMce */
	$(document).ready(function(){
		loaderTiny("#editor",300,1000);

		var asunto  = $("#asunto_doc").val();

		if (asunto==""){
			$("#tamano").focus();
		}	
	});

	/* Funcion para que usando la tecla "Enter" pase de un input al siguiente. */
	$("body").on("keydown", "input, select, textarea", function(e) {
	  	var self 	= $(this),
	    form 		= self.parents("form:eq(0)"),focusable,next;
	  
	  	// Al presionar la tecla enter
	  	if (e.keyCode == 13) {
	    	// busco el siguiente elemento
	    	focusable 	= form.find("input,a,select,button,textarea").filter(":visible");
	    	next 		= focusable.eq(focusable.index(this) + 1);
	    
	    	// si existe siguiente elemento, hago foco
	    	if (next.length) {
	     		next.focus();
	    	}else{
	    		return false;
	    	} 
	    	return false;
	  	}
	});
	
	// window.htmlPdf = null;


/************************************************************** 
* @class Funcion para cargar los datos del usuario que aprueba el documento que se está generando
* @description Recibe tres parámetros (nombre_completo,login_aprueba,cargo_destinatario) con los que asigna valores
** dentro del formulario para generar el documento. Luego de hacer validaciones envía mediante AJAX al archivo 
** 'include/procesar_ajax.php' la variable 'recibe_ajax':'validar_carga_aprueba_firma' junto con las variables necesarias 
** para validar si el usuario tiene la segunda clave (para firma electrónica). En ese mismo AJAX asigna el valor a 
** #aprueba_tiene_pass2 y carga en un input (#contenido_carga_aprueba) la respuesta devuelta. 
** Al recibir la respuesta del AJAX consulta el valor de la respuesta guardada en #contenido_carga_firmante, recorre un ciclo
** del 1 al 5 en la variable "i" para tomar el contenido de los input contenido_carga_revisa y lo concatena con la respuesta 
** del AJAX para mostrar en el div #indicador_documento_electronico el div con los datos del firmante y del usuario que aprueba.
** Muestra si corresponde el #boton_agregar_revisa(muestra el botón para agregar "Revisado por"), muestra la fila 
** #fila_revisa_aprueba que despliega los resultados de los usuarios que (firma, aprueba, revisa), muestra la fila del asunto, 
** oculta la fila #fila_aprueba y enfoca el siguiente campo por diligenciar (#asunto_doc)
* @param string{nombre_completo} Nombre completo del usuario que aprueba el documento. 
* @param string{login_aprueba} Login del usuario que aprueba el documento. 
* @param string{cargo_destinatario} Cargo del usuario que aprueba el documento. 
* @return {} No retorna valores. 
**************************************************************/
	function carga_aprueba(nombre_completo,login_aprueba,cargo_destinatario){
		/* Muestra espacio en formulario donde dice si va a ser aprobado o no. */
		$("#indicador_aprobado").slideDown("slow");

		/* Llena datos con los parámetros recibidos */
		$("#aprueba_doc").val(nombre_completo.trim());
		$("#cargo_aprueba_doc").val(cargo_destinatario.trim());
		$("#login_aprueba").val(login_aprueba.trim());

		/* Reemplaza html con variables */
		$("#aprueba_rs").html(nombre_completo);
		$("#sugerencias_aprueba").html("");
		$("#sugerencia_cargo_aprueba").html("");

		$("#sugerencias_aprueba").slideUp("slow");

		/* Define variables para enviar por AJAX*/
		var numero_radicado = $("#numero_radicado").val();
		var nombre_usuario 	= $("#nombre_usuario").val();

		if(cargo_destinatario==""){
			$("#cargo_aprueba_doc_null").slideDown("slow");
			$("#cargo_aprueba_doc").focus();
		}else{
			$.ajax({
				type 	: 'POST',
				url 	:  'include/procesar_ajax.php',
				data 	: {
					'recibe_ajax'  		: 'validar_carga_aprueba_firma',
					'cargo_firmante'	: cargo_destinatario.trim(),
					'login_firmante'	: login_aprueba,
					'nombre_completo'	: nombre_completo,
					'nombre_usuario'	: nombre_usuario,
					'numero_radicado'	: numero_radicado,
					'validar'  	 		: 'aprueba'
				},
				success: function(resp_aprueba){
					/* Toma el contenido del input contenido_carga_firmante */
					var contenido_carga_firmante = $('#contenido_carga_firmante').val();

					/* Recorre un ciclo del 1 al 5 en la variable "i" para tomar el contenido de los input contenido_carga_revisa */
					var contenido_carga_revisa = "";
					for (var i = 1; i <= 5; i++) {
						/* Obtiene el valor del input de tipo texto "revisaX_tiene_pass2" */
						var revisa_doc = $("#revisa_doc"+i).val();

						if(revisa_doc==""){
							break;
						}
						
						var contenido_revisa_doc = $("#contenido_carga_revisa"+i).val();
						contenido_carga_revisa+=contenido_revisa_doc;
						
						$("#contenedor_revisado"+i).slideUp("slow");
					}

					/* En el div pone el resultado concatenado */
					$("#indicador_documento_electronico").html(contenido_carga_firmante+resp_aprueba+contenido_carga_revisa);

					if(i!=6){
						$("#boton_agregar_revisa").slideDown("slow");
					}

					$("#fila_revisa_aprueba").slideDown("slow");
					$("#fila_asunto").slideDown("slow");

					$("#fila_aprueba").slideUp("slow");
					
					/* Enfoca siguiente campo por diligenciar */
					$("#asunto_doc").focus();
				}
			})						
			$('#boton_agregar_aprueba').hide();					
			$("#lista_firma_aprueba_revisa").val($("#lista_firma_aprueba_revisa").val()+login_aprueba+",");

		}		
	}
/************************************************************** 
* @class Funcion para cargar los datos del usuario que elabora el documento que se está generando
* @description Recibe tres parámetros (nombre_completo,login_elabora,cargo_destinatario) con los que asigna valores
** dentro del formulario para generar el documento. 
** Asigna el hmtl al div (#elabora_rs - #sugerencias_elabora)
** Oculta la fila #sugerencias_elabora 
* @param string{nombre_completo} Nombre completo del usuario que elabora el documento. 
* @param string{login_apureba} Login del usuario que elabora el documento. 
* @param string{cargo_destinatario} Cargo del usuario que elabora el documento. 
* @return {} No retorna valores.
**************************************************************/
	function carga_elabora(nombre_completo,login_elabora,cargo_destinatario){
		$("#cargo_elabora_doc").val(cargo_destinatario.trim());
		$("#elabora_doc").val(nombre_completo.trim());
		$("#login_elabora").val(login_elabora.trim());

		$("#elabora_rs").html(nombre_completo);
		$("#sugerencias_elabora").html("");
		
		$("#sugerencias_elabora").slideUp("slow");
	}
/************************************************************** 
* @class Funcion que se activa cuando se digita en el input #carga_cargo_revisa_doc(1,2,3,4 y 5) con retraso de 1 segundo
* @description Recibe como parametro el codigo_revisa con el cual se define cuales espacios se van a cargar.
** Oculta los errores, hace las validaciones del input '#carga_cargo_revisa_doc(1,2,3,4 y 5)' para mirar maximo, minimo, null 
** y poner en mayusculas capitales el valor ingresado.
** Al pasar 3 segundos si el valor está entre 2 y 100 caracteres carga los datos del usuario que revisa el documento.
* @param string{codigo_revisa} Con este parametro se define el nombre de los input que se van a cargar. 
* @return {} No retorna valores. 
**************************************************************/
	function carga_cargo_revisa_doc(codigo_revisa){
		$(".errores").slideUp("slow");
		loading('sugerencia_cargo_revisa_doc'+codigo_revisa);

		var nombre_revisado_por  = $("#cargo_revisa_doc"+codigo_revisa).val();
		var nombre_revisado_por1 = "";

		if(nombre_revisado_por1 != nombre_revisado_por){
		    nombre_revisado_por1 = nombre_revisado_por;
			clearTimeout(timerid);
			timerid = setTimeout(function() {
				validar_input("cargo_revisa_doc"+codigo_revisa); 
			},1000);

			/* Se carga el usuario firmante */
			timerid = setTimeout(function() {
				if(nombre_revisado_por.length >2 && nombre_revisado_por.length<100){
					var nombre_completo = $("#revisa_doc"+codigo_revisa).val().trim();
					var login_revisa  = $("#login_revisa"+codigo_revisa).val().trim();

					carga_revisa_doc(nombre_completo,login_revisa,nombre_revisado_por,codigo_revisa)
				}
			},3000);
		};
	}
/************************************************************** 
* @class Funcion para cargar los datos del usuario que firma el documento que se está generando
* @description Recibe tres parámetros (nombre_completo,login_firmante,cargo_destinatario) con los que asigna valores
** dentro del formulario para generar el documento. Luego de hacer validaciones envía mediante AJAX al archivo 
** 'include/procesar_ajax.php' la variable 'recibe_ajax':'validar_carga_aprueba_firma' junto con las variables necesarias 
** para validar si el usuario tiene la segunda clave (para firma electrónica). En ese mismo procesar_ajax.php asigna el valor a 
** #firmante_tiene_pass2 y carga en un input (#contenido_carga_firmante) la respuesta devuelta. 
** Al recibir la respuesta del AJAX consulta el valor de la respuesta guardada en #contenido_carga_aprueba, recorre un ciclo del
** 1 al 5 en la variable "i" para tomar el contenido de los input contenido_carga_revisa y la concatena con la respuesta del 
** AJAX para mostrar en el div #indicador_documento_electronico los datos del firmante, usuario que aprueba y usuarios que 
** revisan el documento.
** Si corresponde, muestra el botón para agregar ("Revisado por" - "Aprobado por"), 
** Muestra la fila del asunto, muestra la fila_revisa_aprueba, oculta la fila #fila_firmante y enfoca el siguiente campo por
** diligenciar (#asunto_doc)
* @param string{nombre_completo} Nombre completo del usuario que firma el documento. 
* @param string{login_firmante} Login del usuario que firma el documento. 
* @param string{cargo_firmante} Cargo del usuario que firma el documento. 
* @return {} No retorna valores. 
**************************************************************/
	function carga_firmante(nombre_completo,login_firmante,cargo_firmante){
		/* Muestra espacio donde va a cargar el resultado AJAX de esta misma funcion */
		$("#indicador_firma").show();

		/* Llena datos con los parámetros recibidos */
		$("#cargo_firmante_doc").val(cargo_firmante.trim());
		$("#firmante_doc").val(nombre_completo.trim());
		$("#login_firmante").val(login_firmante.trim());

		/* Reemplaza html con variables */
		$("#cargo_firmante_rs").html(nombre_completo);
		$("#sugerencias_firmante").html("");
		$("#sugerencia_cargo_firmante").html("");

		$("#sugerencias_firmante").hide();

		/* Define variables para enviar por AJAX*/
		var numero_radicado = $("#numero_radicado").val();
		var nombre_usuario 	= $("#nombre_usuario").val();

		if(cargo_firmante==""){
			$("#cargo_firmante_doc_null").show();
			$("#cargo_firmante_doc").focus();
		}else{
			$.ajax({
				type 	: 'POST',
				url 	:  'include/procesar_ajax.php',
				data 	: {
					'recibe_ajax'  		: 'validar_carga_aprueba_firma',
					'cargo_firmante'	: cargo_firmante.trim(),
					'login_firmante'	: login_firmante,
					'nombre_completo'	: nombre_completo,
					'nombre_usuario'	: nombre_usuario,
					'numero_radicado'	: numero_radicado,
					'validar'  	 		: 'firma'
				},
				success: function(resp){
					/* Toma el contenido del input contenido_carga_revisa */
					var contenido_carga_aprueba = $('#contenido_carga_aprueba').val();

					/* Recorre un ciclo del 1 al 5 en la variable "i" para tomar el contenido de los input contenido_carga_aprueba */
					var contenido_carga_revisa = "";
					for (var i = 1; i <= 5; i++) {
						/* Obtiene el valor del input de tipo texto "revisaX_tiene_pass2" */
						var revisa_doc = $("#revisa_doc"+i).val();

						if(revisa_doc==""){
							break;
						}
						
						var contenido_revisa_doc = $("#contenido_carga_revisa"+i).val();
						contenido_carga_revisa+=contenido_revisa_doc;
						
						$("#contenedor_revisado"+i).hide();
					}

					/* En el div pone el resultado concatenado */
					$("#indicador_documento_electronico").html(resp+contenido_carga_aprueba+contenido_carga_revisa);

					if(i!=6){
						$("#boton_agregar_revisa").show();
					}

					/* Si no se ha cargado el valor a #aprueba_doc*/
					if($("#aprueba_doc").val()==""){
						$("#boton_agregar_aprueba").show();
					}

					$("#fila_asunto").show();
					$("#fila_revisa_aprueba").show();

					$("#fila_firmante").hide();
					
					/* Enfoca siguiente campo por diligenciar */
					$("#asunto_doc").focus();
				}
			})	
			$("#lista_firma_aprueba_revisa").val($("#lista_firma_aprueba_revisa").val()+login_firmante+",");
		}
	}
/************************************************************** 
* @class Funcion para cargar los datos del usuario que revisa el documento que se está generando
* @description Recibe cuatro parámetros (nombre_completo,login_aprueba,cargo_destinatario,codigo_revisa) con los que asigna
**  valores dentro del formulario para generar el documento. Luego de hacer validaciones envía mediante AJAX al archivo 
** 'include/procesar_ajax.php' la variable 'recibe_ajax':'validar_carga_aprueba_firma' junto con las variables necesarias 
** para validar si el usuario tiene la segunda clave (para firma electrónica). En ese mismo procesar_ajax.php asigna el valor a 
** #aprueba_tiene_pass2 y carga en un input (#contenido_carga_aprueba) la respuesta devuelta. 
** Al recibir la respuesta del AJAX consulta el valor de la respuesta guardada en #contenido_carga_firmante, recorre un ciclo del
** 1 al 5 en la variable "i" para tomar el contenido de los input contenido_carga_revisa y la concatena con la respuesta del 
** AJAX para mostrar en el div #indicador_documento_electronico los datos del firmante, usuario que aprueba y usuarios que 
** revisan el documento.
** Si corresponde, muestra el botón para agregar ("Revisado por" - "Aprobado por"), 
** Oculta la fila #fila_aprueba y enfoca el siguiente campo por diligenciar (#asunto_doc)
* @param string{nombre_completo} Nombre completo del usuario que aprueba el documento. 
* @param string{login_aprueba} Login del usuario que aprueba el documento. 
* @param string{cargo_destinatario} Cargo del usuario que aprueba el documento. 
* @return {} No retorna valores. 
**************************************************************/
	function carga_revisa_doc(nombre_completo,login_revisa,cargo_destinatario,codigo_revisa){
		/* Muestra espacio en formulario donde dice si va a ser revisado o no. */
		// $("#indicador_aprobado").slideDown("slow");

		/* Llena datos con los parámetros recibidos */
		$("#revisa_doc"+codigo_revisa).val(nombre_completo.trim());
		$("#cargo_revisa_doc"+codigo_revisa).val(cargo_destinatario.trim());
		$("#login_revisa"+codigo_revisa).val(login_revisa.trim());

		/* Reemplaza html con variables */
		$("#revisa_doc"+codigo_revisa+"_rs").html(nombre_completo);
		$("#sugerencias_revisa_doc"+codigo_revisa).html("");
		$("#sugerencia_cargo_revisa_doc"+codigo_revisa).html("");

		$("#sugerencias_revisa_doc"+codigo_revisa).slideUp("slow");

		/* Define variables para enviar por AJAX*/
		var numero_radicado = $("#numero_radicado").val();
		var nombre_usuario 	= $("#nombre_usuario").val();

		if(cargo_destinatario==""){
			$("#cargo_revisa_doc"+codigo_revisa+"_null").slideDown("slow");
			$("#cargo_revisa_doc"+codigo_revisa).focus();
		}else{
			$.ajax({
				type 	: 'POST',
				url 	:  'include/procesar_ajax.php',
				data 	: {
					'recibe_ajax'  		: 'validar_carga_aprueba_firma',
					'cargo_firmante'	: cargo_destinatario.trim(),
					'login_firmante'	: login_revisa,
					'nombre_completo'	: nombre_completo,
					'nombre_usuario'	: nombre_usuario,
					'numero_radicado'	: numero_radicado,
					'validar'  	 		: 'revisa_doc'+codigo_revisa
				},
				success: function(resp_revisa){
					/* Toma el contenido del input contenido_carga_firmante */
					var contenido_carga_firmante = $('#contenido_carga_firmante').val();

					/* Toma el contenido del input contenido_carga_aprueba */
					var contenido_carga_aprueba = $('#contenido_carga_aprueba').val();

					/* Recorre un ciclo del 1 al 5 en la variable "i" para tomar el contenido de los input contenido_carga_aprueba */
					var contenido_carga_revisa = "";
					for (var i = 1; i <= 5; i++) {
						/* Obtiene el valor del input de tipo texto "revisaX_tiene_pass2" */
						var revisa_doc = $("#revisa_doc"+i).val();

						if(revisa_doc==""){
							break;
						}

						var contenido_revisa_doc = $("#contenido_carga_revisa"+i).val();

						if(i==codigo_revisa){
							contenido_carga_revisa+=resp_revisa;
						}else{
							contenido_carga_revisa+=contenido_revisa_doc;
						}
					}

					/* En el div pone el resultado concatenado */
					$("#indicador_documento_electronico").html(contenido_carga_firmante+contenido_carga_aprueba+contenido_carga_revisa);

					if(codigo_revisa==5){
						$("#boton_agregar_revisa").hide();
					}else{
						$("#boton_agregar_revisa").show();
					}

					/* Si no se ha cargado el valor a #aprueba_doc*/
					if($("#aprueba_doc").val()==""){
						$("#boton_agregar_aprueba").slideDown("slow");
					}
					/* Oculta el contenedor revisado */
					$("#contenedor_revisado"+codigo_revisa).slideUp("slow");

					/* Enfoca siguiente campo por diligenciar */
					$("#asunto_doc").focus();
				}
			})	
			$("#lista_firma_aprueba_revisa").val($("#lista_firma_aprueba_revisa").val()+login_revisa+",");		
		}		
	}
/************************************************************** 
* @class Funcion para cargar los datos del expediente desde el desplegable que devuelve la funcion validar_serie_subserie()
* @description Recibe dos parámetros (id_expediente,nombre_expediente) con los que asigna valores dentro del formulario 
** para generar el documento. 
** Oculta el input #buscador_expediente que es donde ingresa el nombre del expediente, 
** Oculta la tabla #datos_clasificacion_radicado que contiene los campos de tamaño hoja, serie y subserie. 
** Oculta el div #resultado_seleccionar_expediente que es el div donde aparecen las opciones del expediente,
** Oculta los errores que aparecen en el formulario, 
** Muestra la tabla #datos_creacion_radicado que contiene la informacion de firmante, aprueba, elaborado, revisado
** y fila_asunto

* @param string{id_expediente} Numero de expediente  
* @param string{nombre_expediente} Asunto o nombre del expediente 
* @return {} No retorna valores.
**************************************************************/
	function cargar_input_expediente(id_expediente,nombre_expediente){
		$("#buscador_expediente").hide();
		$("#datos_clasificacion_radicado").hide();
		$("#resultado_seleccionar_expediente").hide();
		$(".errores").hide();

		$("#datos_creacion_radicado").show();
		$("#seleccionar_expediente").show();

		$("#buscador_expediente").val("");
		$("#id_expediente").val(id_expediente);
		$("#seleccionar_expediente").val(nombre_expediente);

		$("#firmante_doc").focus();
	}
/************************************************************** 
* @class Funcion para copiar el contenido del documento que se está elaborando
* @description Toma el valor del contenido que se diligencia y se asigna en el input #pre_asunto
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
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
/************************************************************** 
* @class Funcion para tomar el html del tinymce
* @description Funcion que retorna el valor del contenido del input #editor que es el contenido del documento
* @param string{} No recibe parámetros. 
* @return {} Retorna el valor que tiene el input #editor. 
**************************************************************/    
	function html_tinymce(){
	    return window.editor.getContent().trim();
	};
/************************************************************** 
* @class Funcion para llenar el valor del contenido del documento
* @description En el caso que no sea un documento nuevo, Se carga el contenido del input #pre_asunto en el #editor
** que es donde funciona el tinymce o el contenido del documento
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/  
    function llenar_tinymce(){
    	var argumento = $("#pre_asunto").val();
    	tinymce.get('editor').setContent(argumento);
    }
/************************************************************** 
* @class Funcion para cargar los plugins de tinymce
* @description Se cargan los plugins de tinymce
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/    
	function loader(selector, size){
	    tinymce.init({
	        branding  			: false, // Para ocultar "By TinyMce"
	        encoding 			: 'UTF-8',
  			file_picker_types 	: 'image',
	        fontsize_formats 	: '6pt 7pt 8pt 9pt 10pt 11pt 12pt 13pt 14pt 16pt 18pt 24pt 36pt 48pt',
	        language 			: 'es',
	        max_height 			: 380,
	        menubar 			: 'edit format table help',
	  		plugins 			: 'advlist anchor autoresize charmap code codesample directionality help hr image imagetools importcss insertdatetime lists nonbreaking noneditable pagebreak save searchreplace table template textpattern toc wordcount visualblocks visualchars',
	        selector 			: "#editor",
	        toolbar 			: 'undo redo | bold italic underline | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat lineheight',
	        toolbar_mode 		: 'scrolling',
	        toolbar_persist 	: true ,
	        width : 812,


  			/* Funcion para selector de imágenes personalizado (Copiar y pegar Ctrl+c y Ctrl+v)*/
	  		file_picker_callback: function (cb, value, meta) {
			    var input = document.createElement('input');
			    input.setAttribute('type', 'file');
			    input.setAttribute('accept', 'image/*');

	    		/* En los navegadores modernos, la entrada [type = "file"] es funcional sin incluso agregarlo al DOM, pero ese podría no ser el caso en algunos o navegadores extravagantes como IE, por lo que es posible que desee agregarlo al DOM por si acaso, y ocultarlo visualmente. Y no olvides quitarlo una vez que ya no lo necesite. */
			    input.onchange = function () {
			      	var file 	= this.files[0];
			      	var reader 	= new FileReader();
			      	
			      	reader.onload = function () {
				        /* Ahora necesitamos registrar el blob en el blob de imagen de TinyMCE. */
			        	var id  		= 'blobid' + (new Date()).getTime();
			        	var blobCache 	=  tinymce.activeEditor.editorUpload.blobCache;
			        	var base64  	= reader.result.split(',')[1];
			        	var blobInfo  	= blobCache.create(id, file, base64);
			        	blobCache.add(blobInfo);

			        	/* llamar a la devolución de llamada y rellenar el campo Título con el nombre del archivo */
			        	cb(blobInfo.blobUri(), { title: file.name });
			      	};
			      	reader.readAsDataURL(file);
			    };
		    	input.click();
		  	},setup: function(ed) {  // Funcion para cuando haga keyup en el #editor
                ed.on('keyup', function(e) {  
                	var indicador_vista_previa = $("#indicador_vista_previa").val();

                	if(indicador_vista_previa=="ver"){
					    $("#example1").html(""); // El div con el PDF debe desaparecer
		    	        $("#contenedor_boton_descargar_plantilla_respuesta").slideUp("slow");

		    	        verpdf = false;

		    	        var tipo_solicitud 		= $("#medio_solicitud_firmas").val();
		    	        var version_documento 	= $("#version_documento").val();

				    	if(tipo_solicitud=='fisico'){
				    		$("#verPdf").val("Vista previa para imprimir en físico");
				    		$("#enviarHtml").val("Generar Versión "+version_documento+" en físico del documento");
				    	}else{
				    		$("#verPdf").val("Vista previa para solicitar electrónicamente");
				    		$("#enviarHtml").val("Generar Versión "+version_documento+" del documento electrónico");
				    	}
				    	$("#indicador_vista_previa").val("");
                	}else{
                		// console.log("poner indicador_vista_previa en ver")
                	}
                });  
            } 
		});
	}
/************************************************************** 
* @class Funcion para cargar inicializar tinymce
* @description Se cargan inicializa la libreria tinymce
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/ 
	function loaderTiny(selector, height, weight, plugin){
		loader(selector, height, weight, plugin);
	    var select = selector.replace("#", "").replace(".", "").replace(" ", "");
	    window.editor = tinymce.get(select);
	}    
/************************************************************** 
* @class Funcion para mostrar la fila_aprueba y eliminar el contenido de #indicador_documento_electronico
* @description Oculta la el boton #boton_agregar_aprueba, oculta la el boton #boton_agregar_revisa ,oculta la fila
** #fila_asunto, muestra la fila #fila_aprueba, quita el html del div #indicador_documento_electronico, pone vacío el valor 
** del input #contenido_carga_aprueba y enfoca el siguiente campo por diligenciar (#aprueba_doc)
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	function muestra_fila_aprueba(){
		$("#boton_agregar_aprueba").slideUp("slow");
		$("#boton_agregar_revisa").slideUp("slow");
		$("#fila_asunto").slideUp("slow");

		$("#fila_aprueba").slideDown("slow");
		
		$("#indicador_documento_electronico").html("");

		$("#contenido_carga_aprueba").val("")
		
		$("#aprueba_doc").focus();

		var lista_firmantes 	= $("#lista_firma_aprueba_revisa").val();
		var login_aprueba  		= $("#login_aprueba").val();
		if(login_aprueba!=""){
			var new_lista_firmantes = lista_firmantes.replace(login_aprueba+",", '');
			$("#lista_firma_aprueba_revisa").val(new_lista_firmantes);
		}
	}
/************************************************************** 
* @class Funcion para mostrar la fila_firmante y eliminar el contenido de #indicador_documento_electronico
* @description Muestra la fila #fila_firmante, oculta la fila #fila_aprueba, oculta la fila #fila_asunto, oculta la fila
** #fila_revisa_aprueba, quita el html del div #indicador_documento_electronico, pone vacío el valor del input 
** #contenido_carga_firmante y enfoca el siguiente campo por diligenciar (#firmante_doc)
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	function muestra_fila_firmante(){
		$("#fila_firmante").show();

		$("#fila_aprueba").hide();
		$("#fila_asunto").hide();
		$("#fila_revisa_aprueba").hide();

		$("#indicador_documento_electronico").html("")

		$("#contenido_carga_firmante").val("")

		var lista_firmantes = $("#lista_firma_aprueba_revisa").val();
		var new_lista_firmantes = lista_firmantes.replace($("#login_firmante").val()+",", '');

		$("#lista_firma_aprueba_revisa").val(new_lista_firmantes);

		$("#firmante_doc").focus()
	}

    function solicitar_firmas_usuarios(tipo_solicitud){
		// $("#verPdf").trigger("click");

		$("#medio_solicitud_firmas").val(tipo_solicitud)

        var version_documento 	= $("#version_documento").val();

    	if(tipo_solicitud=='fisico'){
    		$("#verPdf").val("Vista previa para imprimir en físico");
    		$("#enviarHtml").val("Generar Versión "+version_documento+" en físico del documento");
    	}else{
    		$("#verPdf").val("Vista previa para solicitar electrónicamente");
    		$("#enviarHtml").val("Generar Versión "+version_documento+" del documento electrónico");
    	}
    }
/************************************************************** 
* @class Funcion para validar si es válida la serie y la subserie que se está ingresando al documento.
* @description Consulta el codigo_serie, codigo_suberie y seleccionar_expediente (que es el nombre del expediente)
** hace las validaciones y envía mediante AJAX la variable recibe_ajax':'seleccionar_expediente' junto con las variables
** necesarias para que haga la búsqueda de los expedientes de la dependencia y serie seleccionados para ponerlos en el div 
** #resultado_seleccionar_expediente, adicionalmente envía la variable "subserie" para que en la acción del archivo procesar_ajax.php
** haga mediante jquery el nombre de la serie y de la subserie en el formulario en los campos #nombre_serie y #nombre_subserie 
** respectivamente
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	function validar_serie_subserie(){
		var codigo_serie 			= $("#codigo_serie").val();
		var codigo_subserie 		= $("#codigo_subserie").val(); 
		var seleccionar_expediente 	= $("#seleccionar_expediente").val(); 

		if($("#resultado_seleccionar_expediente").is(":visible") && seleccionar_expediente == ""){
			$("#seleccionar_expediente_invalido").slideDown("slow");
			return false;
		}else{
			if(codigo_serie==""){
				$("#error_codigo_serie").slideDown("slow");
				return false;
			}else{
				$("#error_codigo_serie").slideUp("slow");

				switch(codigo_subserie){
					case '':
						$("#error_codigo_subserie").slideDown("slow");
						$("#error2_codigo_subserie").slideUp("slow");
						$("#input_seleccionar_expediente").slideUp("slow");
						$("#seleccionar_expediente").val("");
						return false;
						break;

					case 'subserie':
						$("#error_codigo_subserie").slideUp("slow");
						$("#error2_codigo_subserie").slideDown("slow");
						$("#input_seleccionar_expediente").slideUp("slow");
						$("#seleccionar_expediente").val("");
						return false;
						break;

					default:
						$("#error_codigo_subserie").slideUp("slow");
						$("#error2_codigo_subserie").slideUp("slow");
						$("#input_seleccionar_expediente").slideDown("slow");

						if(seleccionar_expediente==""){
							var codigo_dependencia 	= $("#codigo_dependencia").val();

							$.ajax({
						        type: 'POST',
						        url: 'include/procesar_ajax.php',
						        data: {
						            'recibe_ajax' 		: 'seleccionar_expediente',
						            'dependencia' 		: codigo_dependencia,
						            'serie' 			: codigo_serie,
						            'subserie'			: codigo_subserie,
						            'search_expediente' : ""
						        },          
						        success: function(respuesta){
									$("#resultado_seleccionar_expediente").slideDown("slow");
					            	$("#resultado_seleccionar_expediente").html(respuesta);
						        }
						    })
						    $("#buscador_expediente").focus();
						}else{
						}				
					break;
				}
			}
		}		
	}	
/************************************************************** 
* @class Funcion para estimar mediante HTML el tamaño de la pagina
* @description Se envía toda la información del formulario y del contenido del documento que se está generando mediante POST
** al archivo /radicacion_resoluciones/template_resoluciones.php enviandole las variables que se capturan en la funcion 
** obj(parametro) a la cual se le envía el parámetro 'vista_html'. A la respuesta de esta función se le hacen varios .replaceAll
** y se pone en un div #visor_html con el cual se estima el alto de este div. Esto da un valor que indica que tantas paginas 
** dependiendo del tamaño de la hoja seleccionada (Carta u Oficio) 
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
/*	function vista_html(){
		$.post("radicacion/radicacion_resoluciones/template_resoluciones.php", obj('vista_html'), function (request) {
			var style = request
				.replaceAll('<div id="footer">', '<div id="footer" style="display: block">')
				.replaceAll('td{}', 'td{padding-left: 10px!important;padding-top: 35px!important;}')
				.replaceAll("<table>", "<br><br><table>")
				.replaceAll("</table>", "</table><br><br>")
				.replaceAll("</p>", "</p><br>")
				.replaceAll("</b></b>", "")
				.trim();
			$("#visor_html").html(style);
        }, 'html');

		var tamano 			= $("#tamano").val();

		setTimeout(function(){
			var alto_visor_html = $("#visor_html").height();

			if(alto_visor_html>1036.7){
				console.log("Tamaño "+tamano+" - Alto del visor = "+alto_visor_html+"-> Ocupa mas de una hoja.")
			}else{
				console.log("Tamaño "+tamano+" - Alto del visor = "+alto_visor_html+" -> Ocupa solo una hoja.")
			}
		}, 1000);
	}
*/


	/* Funcion que invoca el boton "Agregar 'Revisado por'" */
	function despliega_revisa_doc(codigo_revisa){
		$("#boton_agregar_revisa").slideUp("slow");	
		$("#boton_agregar_aprueba").slideUp("slow");

		if(codigo_revisa==6){
			/* Recorre un ciclo del 1 al 5 en la variable "i" */
			for (var i = 1; i <= 5; i++) {
				/* Obtiene el valor del input de tipo texto "revisaX_tiene_pass2" */
				var revisa_doc = $("#revisa_doc"+i).val();

				if(revisa_doc==""){
					$("#contenedor_revisado"+i).slideDown("slow");
					$("#despliega_revisa_doc"+i).slideDown("slow");
					$("#revisa_doc"+i).focus();
					break;
				}
			}
		}else{
			$("#contenedor_revisado"+codigo_revisa).slideDown("slow");
			$("#despliega_revisa_doc"+codigo_revisa).slideDown("slow");

			$("#indicador_documento_electronico").html("");

			var lista_firmantes = $("#lista_firma_aprueba_revisa").val();
			var new_lista_firmantes = lista_firmantes.replace($("#login_revisa"+codigo_revisa).val()+",", '');
			$("#lista_firma_aprueba_revisa").val(new_lista_firmantes);

			$("#revisa_doc"+codigo_revisa).focus();
		}
	}
	
/************************************************************** 
* @class Funcion que se activa cuando se digita en el input #aprueba_doc con retraso de 1 segundo
* @description Oculta los errores, pone el valor de (#cargo_aprueba_doc y #login_aprueba) vacío, usando jquery reemplaza el 
** html de los div (#indicador_aprobado, #aprueba_rs) en vacío, pone el loading en el div #sugerencias_aprueba. 

** Pone el valor ingresado en mayusculas capitales, toma el valor del usuario que va a aprobar el documento para 
** enviarlos mediante AJAX al archivo 'include/procesar_ajax.php' junto con la variable 'recibe_ajax':'buscar_firmante' y el 
** 'tipo_busqueda': 'carga_aprueba' y la variable por buscar para obtener como respuesta un listado de conicidencias del valor 
** buscado con los usuarios registrados como activos en la base de datos en un div con la información del usuario o un mensaje 
** de error.
** Cualquiera de las dos respuestas que se pone en el div #sugerencias_aprueba
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	$("#aprueba_doc").on("input",function(e){ // Accion que se activa cuando se digita #aprueba_doc
		$(".errores").slideUp("slow");

		$("#cargo_aprueba_doc").val("");
		$("#login_aprueba").val("");

		$("#indicador_aprobado").html("");
		$("#aprueba_rs").html("");

		loading('sugerencias_aprueba');
		var lista_firma_aprueba_revisa = $("#lista_firma_aprueba_revisa").val();
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
							'recibe_ajax'  					: 'buscar_firmante',
							'tipo_busqueda'  	 			: 'carga_aprueba',
							'lista_firma_aprueba_revisa'	: lista_firma_aprueba_revisa,
							'nombre_buscado'				: nombre_aprobado
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
/************************************************************** 
* @class Funcion que se activa cuando se digita en el input #buscador_expediente con retraso de 1 segundo
* @description Oculta los errores, pone el valor ingresado en mayusculas, toma el valor de la dependencia y de la serie para 
** enviarlos mediante AJAX al archivo 'include/procesar_ajax.php' junto con la variable 'recibe_ajax':'seleccionar_expediente'
** para obtener como respuesta el listado de expedientes en la base de datos que corresponden a la dependencia y a la serie
** pero si tiene algun valor ingresado, busca en el numero del expediente o en el nombre del expediente y trae las coincidencias
** en listado. En caso que no encuentra coincidencias, trae el error correspondiente y la opción para crear expediente. 
** Cualquiera de las dos respuestas que se pone en el div #resultado_seleccionar_expediente
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	$("#buscador_expediente").on("input",function(e){ // Accion que se activa cuando se digita #buscador_expediente
		$(".errores").slideUp("slow");
		var asunto = $(this).val();
		    
		if($(this).data("lastval")!= asunto){
		    $(this).data("lastval",asunto);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
				espacios_formulario('buscador_expediente','mayusculas',0);

				var codigo_dependencia 	= $("#codigo_dependencia").val();
				var codigo_serie 		= $("#codigo_serie").val();
				var codigo_subserie 	= $("#codigo_subserie").val();

				$.ajax({
			        type: 'POST',
			        url: 'include/procesar_ajax.php',
			        data: {
			            'recibe_ajax' 		: 'seleccionar_expediente',
			            'dependencia' 		: codigo_dependencia,
			            'search_expediente' : asunto,
			            'serie' 			: codigo_serie,
			            'subserie' 			: codigo_subserie
			        },          
			        success: function(respuesta){
		            	$("#resultado_seleccionar_expediente").slideDown("slow");
		            	$("#resultado_seleccionar_expediente").html(respuesta);
			        }
			    })
			    $("#buscador_expediente").focus();
			},1000);
		};
	});
/************************************************************** 
* @class Funcion que se activa cuando se digita en el input #cargo_aprueba_doc con retraso de 1 segundo
* @description Oculta los errores, hace las validaciones del input 'cargo_aprueba_doc' para mirar maximo, minimo, null y poner
** en mayusculas capitales el valor ingresado.
** Al pasar 3 segundos si el valor está entre 2 y 100 caracteres carga los datos del usuario que aprueba el documento.
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	$("#cargo_aprueba_doc").on("input",function(e){ // Accion que se activa cuando se digita #cargo_aprueba_doc
		$(".errores").slideUp("slow");
		var cargo_aprobador_documento = $(this).val();
		loading("sugerencia_cargo_aprueba")	    
		if($(this).data("lastval")!= cargo_aprobador_documento){
		    $(this).data("lastval",cargo_aprobador_documento);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
				validar_input('cargo_aprueba_doc');
			},1000);

			/* Se carga el usuario firmante */
			timerid = setTimeout(function() {
				if(cargo_aprobador_documento.length >2 && cargo_aprobador_documento.length<100){
					var nombre_completo = $("#aprueba_doc").val().trim();
					var login_aprueba  	= $("#login_aprueba").val().trim();
					carga_aprueba(nombre_completo,login_aprueba,cargo_aprobador_documento);
				}
			},3000);
		};
	});
/************************************************************** 
* @class Funcion que se activa cuando se digita en el input #cargo_firmante_doc con retraso de 1 segundo
* @description Oculta los errores, hace las validaciones del input 'cargo_firmante_doc' para mirar maximo, minimo, null y poner
** en mayusculas capitales el valor ingresado.
** Al pasar 3 segundos si el valor está entre 2 y 100 caracteres carga los datos del firmante del documento.
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	$("#cargo_firmante_doc").on("input",function(e){ // Accion que se activa cuando se digita #cargo_firmante_doc
		$(".errores").slideUp("slow");
		var cargo_firmante = $(this).val();
		loading("sugerencia_cargo_firmante")
		if($(this).data("lastval")!= cargo_firmante){
		    $(this).data("lastval",cargo_firmante);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
				validar_input('cargo_firmante_doc');
			},1000);

			/* Se carga el usuario firmante */
			timerid = setTimeout(function() {
				if(cargo_firmante.length >2 && cargo_firmante.length<100){
					var nombre_completo = $("#firmante_doc").val().trim();
					var login_firmante  = $("#login_firmante").val().trim();

					carga_firmante(nombre_completo,login_firmante,cargo_firmante);
				}
			},3000);
		};
	});
/************************************************************** 
* @class Funcion que se activa cuando se digita en el input #firmante_doc con retraso de 1 segundo
* @description Oculta los errores, pone el valor de (#cargo_firmante_doc y #login_firmante) vacío, usando jquery reemplaza el 
** html de los div (#cargo_firmante_rs, #indicador_documento_electronico, #indicador_firma) en vacío, pone el loading en el div 
** #sugerencias_firmante. 
** Pone el valor ingresado en mayusculas capitales, toma el valor del usuario que va a firmar el documento para 
** enviarlos mediante AJAX al archivo 'include/procesar_ajax.php' junto con la variable 'recibe_ajax':'buscar_firmante'
** para obtener como respuesta un listado de conicidencias del valor buscado con los usuarios registrados como activos en la base
** de datos en un div con la información del usuario o un mensaje de error.
** Cualquiera de las dos respuestas que se pone en el div #sugerencias_firmante
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	$("#firmante_doc").on("input",function(e){ // Accion que se activa cuando se digita #firmante_doc
		$(".errores").slideUp("slow");
		$("#cargo_firmante_doc").val("");
		$("#login_firmante").val("");

		$("#cargo_firmante_rs").html("");
		$("#indicador_documento_electronico").html("");
		$("#indicador_firma").html("");

		loading('sugerencias_firmante');
		var lista_firma_aprueba_revisa = $("#lista_firma_aprueba_revisa").val();
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
							'recibe_ajax'  					: 'buscar_firmante',
							'tipo_busqueda'  				: 'carga_firmante',
							'lista_firma_aprueba_revisa'	: lista_firma_aprueba_revisa,
							'nombre_buscado'				: firmante
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
/************************************************************** 
* @class Funcion que se activa cuando se digita en el input #revisa_doc1 con retraso de 1 segundo
* @description Oculta los errores, pone el valor de (#cargo_revisa_doc1 y #login_revisa_1) vacío
** Pone el loading en el div #sugerencias_aprueba. 

** Pone el valor ingresado en mayusculas capitales, toma el valor del usuario que va a aprobar el documento para 
** enviarlos mediante AJAX al archivo 'include/procesar_ajax.php' junto con la variable 'recibe_ajax':'buscar_firmante' y el 
** 'tipo_busqueda': 'carga_aprueba' y la variable por buscar para obtener como respuesta un listado de conicidencias del valor 
** buscado con los usuarios registrados como activos en la base de datos en un div con la información del usuario o un mensaje 
** de error.
** Cualquiera de las dos respuestas que se pone en el div #sugerencias_aprueba
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	// $("#revisa_doc1").on("input",function(e){ // Accion que se activa cuando se digita #revisa_doc1
	function revisa_doc_keyup(codigo_revision){	
		$(".errores").slideUp("slow");

		$("#cargo_revisa_doc"+codigo_revision).val("");
		$("#login_revisa_"+codigo_revision).val("");

		// $("#indicador_aprobado").html("");
		// $("#aprueba_rs").html("");

		var nombre_revisado_por  = $("#revisa_doc"+codigo_revision).val();
		var nombre_revisado_por1 = "";

		if(nombre_revisado_por1 != nombre_revisado_por){
		    nombre_revisado_por1 = nombre_revisado_por;
		    // $(this).data("lastval",nombre_revisado_por);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
				loading('sugerencias_revisa_doc'+codigo_revision);
				espacios_formulario('revisa_doc'+codigo_revision,'capitales',0);

		 		nombre_revisado_por = $("#revisa_doc"+codigo_revision).val();
		 		var lista_firma_aprueba_revisa = $("#lista_firma_aprueba_revisa").val();

		 		$('#sugerencias_revisa_doc'+codigo_revision).slideDown('slow');
		 		if(nombre_revisado_por!=""){
			 		$.ajax({
						type 	: 'POST',
						url 	:  'include/procesar_ajax.php',
						data 	: {
							'recibe_ajax'  					: 'buscar_firmante',
							'tipo_busqueda'  				: 'revisa_doc'+codigo_revision,
							'lista_firma_aprueba_revisa'	: lista_firma_aprueba_revisa,
							'nombre_buscado'				: nombre_revisado_por
						},
						success: function(resp){
							if(resp=="sin_registros"){
								$("#revisa_doc"+codigo_revision+"_null").slideDown("slow");
								$('#sugerencias_revisa_doc'+codigo_revision).slideUp("slow");
							}else{
								$("#revisa_doc"+codigo_revision+"_null").slideUp("slow");
								$("#sugerencias_revisa_doc"+codigo_revision).html(resp);
							}
						}
					})	
				}else{
					$("#aprueba_doc_null").slideDown("slow");
					$("#sugerencias_revisa_doc"+codigo_revision).slideUp("slow");
				}	
			},1000);
		};
	}	
	
	$("#anexos_doc").on("input",function(e){ // Accion que se activa cuando se digita #anexos_doc
		$(".errores").slideUp("slow");
		var anexos_documento = $(this).val();
		if($(this).data("lastval")!= anexos_documento){
		    $(this).data("lastval",anexos_documento);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
				espacios_formulario('anexos_doc','capitales',0);
				validar_input('anexos_doc');
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
			},3000);
		};
	});
	
	$("#asunto_doc").on("input",function(e){ // Accion que se activa cuando se digita #asunto_doc
		espacios_formulario('asunto_doc','primera',0);
		$("#botones_contenido_documento").slideUp("slow");

		$(".errores").slideUp("slow");
		var asunto = $(this).val();
		    
		if($(this).data("lastval")!= asunto){
		    $(this).data("lastval",asunto);             
			clearTimeout(timerid);
			timerid = setTimeout(function() {
				validar_input('asunto_doc');
				if($(".errores").is(":visible")){
					return false;
				}else{	
					$("#botones_contenido_documento").slideDown("slow");
				}	
			},1000);
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
			},3000);
		};
	});

	function validar_expediente(){
		var buscador_expediente		= $("#buscador_expediente").val();
		var id_expediente 			= $("#id_expediente").val(); 

		if(buscador_expediente=="" && id_expediente==""){
			$("#seleccionar_expediente_invalido").slideDown("slow");
			return false;
		}else{
			return true;
		}
	}
	/* Funcion que se activa con el boton de "Vista previa del documento" o "Seguir editando documento" */
	$("#verPdf").click(function (e) {
		// console.log("click en #verPdf");
		if($(".errores").is(":visible")){
			return false;
		}else{		// Realizar la creación del documento
			if($(".errores").is(":visible")){
				return false;
			}else{	
				if($(".resultado_busq_usuario").is(":visible") ){
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
			        visorPdf();
				}
			}	
		}
	});

	function visorPdf() {
	    if (!verpdf) { // Si al dar click en el botón "#verPdf" es hacer Vista previa del documento
			var nuevo_nombre_documento 			= $("#nuevo_nombre_documento").val();
			var ruta_pdf_vista_previa_temporal 	= "bodega_pdf/plantilla_generada_tmp/"+nuevo_nombre_documento+".pdf";
			var tamano							= $("#tamano").val();
	       /* Indicador para activar o detener onkeyup desde #editor */
	       	$("#indicador_vista_previa").val("ver");

       		/* Se crea un objeto y se le asignan atributos */
			var objeto_vista_previa		= new Object();
	        // objeto_vista_previa.aprueba_doc 				= $("#aprueba_doc").val();
	        // objeto_vista_previa.cargo_aprueba_doc 			= $("#cargo_aprueba_doc").val();
	        // objeto_vista_previa.cargo_elabora_doc 			= $("#cargo_elabora_doc").val();
	        // objeto_vista_previa.cargo_firmante_doc 			= $("#cargo_firmante_doc").val();
	        // objeto_vista_previa.cargo_revisa_doc1 			= $("#cargo_revisa_doc1").val();
	        // objeto_vista_previa.cargo_revisa_doc2 			= $("#cargo_revisa_doc2").val();
	        // objeto_vista_previa.cargo_revisa_doc3 			= $("#cargo_revisa_doc3").val();
	        // objeto_vista_previa.cargo_revisa_doc4 			= $("#cargo_revisa_doc4").val();
	        // objeto_vista_previa.cargo_revisa_doc5 			= $("#cargo_revisa_doc5").val();
	        // objeto_vista_previa.codigo_serie 				= $("#codigo_serie").val();
	        // objeto_vista_previa.codigo_subserie 			= $("#codigo_subserie").val();
	        // objeto_vista_previa.elabora_doc 				= $("#elabora_doc").val();
	        // objeto_vista_previa.firmante_doc 				= $("#firmante_doc").val();
	        // objeto_vista_previa.id_expediente 				= $("#id_expediente").val();
	        // objeto_vista_previa.nombre_dependencia 			= $("#nombre_dependencia").val(); // Variable que se muestra en el encabezado
	        // objeto_vista_previa.revisa_doc1 				= $("#revisa_doc1").val();
	        // objeto_vista_previa.revisa_doc2 				= $("#revisa_doc2").val();
	        // objeto_vista_previa.revisa_doc3 				= $("#revisa_doc3").val();
	        // objeto_vista_previa.revisa_doc4 				= $("#revisa_doc4").val();
	        // objeto_vista_previa.revisa_doc5 				= $("#revisa_doc5").val();

	        objeto_vista_previa.footerImg 					= $("#footerImg").val();
	        objeto_vista_previa.headerImg 					= $("#headerImg").val();
	        objeto_vista_previa.nombre_archivo				= ruta_pdf_vista_previa_temporal;
	        objeto_vista_previa.numero_radicado 			= $("#numero_radicado").val();
	        objeto_vista_previa.pre_asunto 					= $("#pre_asunto").val();
	        objeto_vista_previa.tamano						= tamano;
	        objeto_vista_previa.tipo_solicitud 				= $("#medio_solicitud_firmas").val(); // Se imprime o no

        	objeto_vista_previa.resultado_js 	= html_tinymce()
            .replaceAll("<!DOCTYPE html>","")
            .replaceAll("<html>","")
            .replaceAll("<head>","")
            .replaceAll("<body>","")
            .replaceAll("</html>","")
            .replaceAll("</head>","")
            .replaceAll("</body>",""); 

	        $.post("radicacion/radicacion_resoluciones/template_resoluciones.php", objeto_vista_previa, function (request) {
	            var style = request
	                .replaceAll('<div id="footer">', '<div id="footer" style="display: block">')
	                .replaceAll('td{}', 'td{padding-left: 10px!important;padding-top: 35px!important;}')
	                .replaceAll("<table>", "<br><br><table>")
	                .replaceAll("</table>", "</table><br><br>")
	                .replaceAll("</p>", "</p><br>")
	                .replaceAll("</b></b>", "")
	                .trim();

	                $.post("include/dompdf/vista_previa_pdf_resoluciones.php", {html: style, nombre_archivo:nuevo_nombre_documento, tamano : tamano}, function (request) {
				        PDFObject.embed(ruta_pdf_vista_previa_temporal, "#example1");
				    });
	        }, 'html');

	        $("#verPdf").val("Seguir editando documento"); 	// Cambiar el valor de #verPdf    
	        $("#contenedor_boton_descargar_plantilla_respuesta").show(); // Mostrar el boton para generar version del documento

	        verpdf = true; // Variable para saber si al dar clic en el boton #verPdf sigue editando documento o genera vista previa	
		
		}else{ 	// Si al dar click en el botón "#verPdf" es Seguir editando el documento
	        verpdf = false; // Variable para saber si al dar clic en el boton #verPdf sigue editando documento o genera vista previa

	        var tipo_solicitud = $("#medio_solicitud_firmas").val();
	    	if(tipo_solicitud=='fisico'){
	    		$("#verPdf").val("Vista previa para imprimir en físico");
	    	}else{
	    		$("#verPdf").val("Vista previa para solicitar electrónicamente");
	    	}

	        $("#contenedor_boton_descargar_plantilla_respuesta").slideUp("slow");
		    $("#example1").html(""); // El div con el PDF debe desaparecer
	    }
	}

	/* Funcion que se activa con el boton "Generar Version XX del documento" */
	$("#enviarHtml").click(function (form) {
		loading('contenedor_boton_descargar_plantilla_respuesta');

	    if(verpdf){
	        $("#example1").css("display","block");
	        $("#visor_pdf").css("display","none");
	        $("#verPdf").val("Seguir editando el documento");

	        var nuevo_nombre_documento = $("#nuevo_nombre_documento").val();

	        /* Genera la Versión del Pdf */
	        copiar_tinymce();
		    enviar_pdf_final();
	    }else{
	        alert("Presionar (Seguir editando documento)")
	    }
	});

	/* Funcion para generar el pdf final con codigo QR, generar radicado e histórico */
	function enviar_pdf_final() {
		var nuevo_nombre_documento 	= $("#nuevo_nombre_documento").val();
	    var nombre_usuario 			= $("#nombre_usuario").val();

		var url_genera_dompdf 		= "radicacion/radicacion_resoluciones/generar_pdf_resoluciones.php";
		var url_nuevo_archivo 		= "bodega_pdf/plantilla_generada_tmp/"+nuevo_nombre_documento+".pdf";		
		var url_plantilla			= "radicacion/radicacion_resoluciones/template_final_resoluciones.php";

		/* Se crea un objeto y se le asignan atributos */
		var objeto3 						= new Object();

		objeto3.nameArchive 				= nuevo_nombre_documento;
		objeto3.usuario_radicador 			= nombre_usuario;

		// objeto3.tratamiento_doc 			= $("#tratamiento_doc").val();
	    // objeto3.anexos_doc 					= $("#anexos_doc").val();
	    // objeto3.cargo_destinatario  		= $("#cargo_titular_doc").val();
	    // objeto3.cc_doc 						= $("#cc_doc").val(); // Con Copia
	    // objeto3.despedida_doc 				= $("#despedida_doc").val();
	    // objeto3.destinatario 				= $("#destinatario_doc").val();
	    // objeto3.direccion_doc 				= $("#direccion_doc").val();
	    // objeto3.empresa_destinatario_doc 	= $("#empresa_destinatario_doc").val();
	    // objeto3.mail_doc 					= $("#mail_doc").val();
	    // objeto3.telefono_doc 				= $("#telefono_remitente").val();
	    // objeto3.ubicacion_doc 				= $("#ubicacion_doc").val();
		objeto3.tipo_radicacion 			= $("#tipo_radicacion").val(); // El tipo de radicacion que recibe (respuesta, etc)
		objeto3.tipo_radicado 				= $("#tipo_radicado").val();
		objeto3.usuario_actual 				= $("#usuario_actual").val();
		objeto3.usuario_radicador 			= $("#lista_usuario_actual").val();
		objeto3.version_documento 			= $("#version_documento").val();
	    objeto3.aprobado_doc				= $("#aprobado_mod").val(); // Se encuentra aprobado digitalmente (SI/NO)
	    objeto3.aprueba_doc 				= $("#aprueba_doc").val();
	    objeto3.asunto_doc 					= $("#asunto_doc").val();
	    objeto3.cargo_aprueba_doc 			= $("#cargo_aprueba_doc").val();
	    objeto3.cargo_elabora_doc 			= $("#cargo_elabora_doc").val();
	    objeto3.cargo_firmante_doc 			= $("#cargo_firmante_doc").val();
	    objeto3.codigo_contacto 			= $("#codigo_contacto").val();
	    objeto3.codigo_serie 				= $("#codigo_serie").val();
	    objeto3.codigo_subserie 			= $("#codigo_subserie").val();
	    objeto3.elabora_doc 				= $("#elabora_doc").val();
	    objeto3.fecha 						= $("#fecha_doc").val();
	    objeto3.firmante_doc 				= $("#firmante_doc").val();
	    objeto3.footerImg 					= $("#footerImg").val();
	    objeto3.headerImg 					= $("#headerImg").val();
	    objeto3.id_expediente 				= $("#id_expediente").val();
	    objeto3.lista_firma_aprueba_revisa 	= $("#lista_firma_aprueba_revisa").val();
	    objeto3.login_aprueba 				= $("#login_aprueba").val();
	    objeto3.login_elabora 				= $("#login_elabora").val();
	    objeto3.login_firmante 				= $("#login_firmante").val();
	    objeto3.nombre_dependencia 			= $("#nombre_dependencia").val();
	    objeto3.numero_radicado 			= $("#numero_radicado").val();
	    objeto3.pre_asunto 					= $("#pre_asunto").val();
	    objeto3.tamano 						= $("#tamano").val();
	    objeto3.tipo_solicitud 				= $("#medio_solicitud_firmas").val(); // Se imprime o no

	    objeto3.cargo_revisa_doc1 			= $("#cargo_revisa_doc1").val();
        objeto3.cargo_revisa_doc2 			= $("#cargo_revisa_doc2").val();
        objeto3.cargo_revisa_doc3 			= $("#cargo_revisa_doc3").val();
        objeto3.cargo_revisa_doc4 			= $("#cargo_revisa_doc4").val();
        objeto3.cargo_revisa_doc5 			= $("#cargo_revisa_doc5").val();
        objeto3.revisa_doc1 				= $("#revisa_doc1").val();
        objeto3.revisa_doc2 				= $("#revisa_doc2").val();
        objeto3.revisa_doc3 				= $("#revisa_doc3").val();
        objeto3.revisa_doc4 				= $("#revisa_doc4").val();
        objeto3.revisa_doc5 				= $("#revisa_doc5").val();

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
			  		console.log(respuesta)
		        	$("#resultado_js").html(respuesta);
		        }
			})	
	    }, 'html');
	}
	/* Fin funcion para generar el pdf final con codigo QR, generar radicado e histórico */
	function focus_subserie(){
		$("#codigo_subserie").focus();
		$("#resultado_seleccionar_expediente").html("");
	}
	function cargar_datos_modificar_resolucion(){
		llenar_tinymce();

		var codigo_serie_mod 		= $("#codigo_serie_mod").val();
		var codigo_subserie_mod 	= $("#codigo_subserie_mod").val();
		var asunto_mod 				= $("#asunto_mod").val();
		var firmante_mod 			= $("#firmante_mod").val();
		var cargo_firmante_mod 		= $("#cargo_firmante_mod").val();
		var aprueba_mod 			= $("#aprueba_mod").val();
		var cargo_aprueba_mod 		= $("#cargo_aprueba_mod").val();
		var elabora_mod 			= $("#elabora_mod").val();
		var cargo_elabora_mod 		= $("#cargo_elabora_mod").val();
		var codigo_contacto_mod 	= $("#codigo_contacto_mod").val();
		var nombre_expediente_mod  	= $("#nombre_expediente").val();
		var login_firmante  		= $("#login_firmante").val();
		var login_aprueba  			= $("#login_aprueba").val();

		$("#aprueba_doc").val(aprueba_mod);
		$("#asunto_doc").val(asunto_mod);
		$("#cargo_aprueba_doc").val(cargo_aprueba_mod);
		$("#cargo_elabora_doc").val(cargo_elabora_mod);
		$("#cargo_firmante_doc").val(cargo_firmante_mod);
		$("#codigo_contacto_mod").val(codigo_contacto_mod);
		$("#codigo_serie").val(codigo_serie_mod);
		$("#codigo_subserie").val(codigo_subserie_mod);
		$("#elabora_doc").val(elabora_mod);
		$("#firmante_doc").val(firmante_mod);
		$("#seleccionar_expediente").val(nombre_expediente_mod);

	    // $("#tabla_formulario_salida").slideDown("slow"); // Tabla no definida en formuario
	    $("#datos_creacion_radicado").slideDown("slow");

		$("#input_seleccionar_expediente").slideDown("slow");
		$("#botones_plantilla_radicacion_salida").slideDown("slow");

		/* Desde aqui se carga el firmante y usuario_aprueba */
		carga_firmante(firmante_mod,login_firmante,cargo_firmante_mod)

		if(aprueba_mod!=""){
			carga_aprueba(aprueba_mod,login_aprueba,cargo_aprueba_mod)
		}
		$("#botones_contenido_documento").slideDown("slow");
		$("#verPdf").slideUp("slow");
	}

	/* Funcion para cargar formulario de aprobar documento*/
	function cargar_aprueba(login,radicado,tipo){
		$("#ventana_aprobar_documento").slideDown("slow");
		$("#login_aprueba").val(login);
		$("#tipo_aprueba_firma").val(tipo);

		$("#contr_confirma_aprobado").focus();
		// console.log(login + " - "+radicado+ " - "+tipo)
	}

	$("#contr_confirma_aprobado").on("input",function(e){ // Accion que se activa cuando se digita #contr_confirma_aprobado
		$(".errores").slideUp("slow");
	});


	/* Funcion para aprobar la firma electrónica con primera clave */
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

	function valida_expediente_vacio(){
		var expediente = $("#buscador_expediente").val();

		if(expediente==""){
			$("#seleccionar_expediente").val("");
			$("#id_expediente").val("");
			$("#resultado_seleccionar_expediente").slideDown("slow");
		}
	}

	function valida_indicador_aprueba_firma(){
		var indicador_aprobado 	= $("#indicador_aprobado").html();
		var indicador_firma  	= $("#indicador_firma").html();

		if (indicador_aprobado ==""){
			$("#aprueba_doc_null").slideDown("slow");
		}

		if (indicador_firma ==""){
			$("#firmante_doc_null").slideDown("slow");

		}
	}

	function valida_elaborar_contenido(){
		validar_input('asunto_doc');

		validar_input_null("firmante_doc");
		validar_input_null("cargo_firmante_doc");
		validar_input_null("aprueba_doc");
		validar_input_null("cargo_aprueba_doc");
		validar_input_null("elabora_doc");
		validar_input_null("cargo_elabora_doc");

		validar_serie_subserie();
		validar_expediente();
		valida_indicador_aprueba_firma();
		// validar_usuario_actual();

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
				$("#elaborar_contenido").show();
				$("#verPdf").show();
				$("#botones_plantilla_radicacion_salida").show();

				$("#datos_clasificacion_radicado").hide();
				$("#datos_creacion_radicado").hide();
				$("#botones_contenido_documento").hide();
			}
		}

		var tamano 					= $("#tamano").val();
		var codigo_serie 			= $("#codigo_serie").val();
		var codigo_subserie 		= $("#codigo_subserie").val();
		var asunto_doc 				= $("#asunto_doc").val();
		var id_expediente 			= $("#id_expediente").val();
		var seleccionar_expediente 	= $("#seleccionar_expediente").val();

		var firmante_doc 			= $("#firmante_doc").val();
		var cargo_firmante_doc 		= $("#cargo_firmante_doc").val();

		var aprueba_doc 			= $("#aprueba_doc").val();
		var cargo_aprueba_doc 		= $("#cargo_aprueba_doc").val();
		
		if(aprueba_doc==""){
			var contenido_carga_revisa = "";
		}else{
			var contenido_carga_revisa = "<td class='borde_tabla' style='cursor:pointer' onclick='volver_ventana_datos()' title='Haga clic para cambiar el valor de usuario que aprueba el documento que está generando'>El usuario que <b>APRUEBA</b> el documento es :<br><span class='dato_form'>"+aprueba_doc+"</span><br>("+cargo_aprueba_doc+")</td></tr>";
		}

		var habilita_firma_electronica 	= ""; 	// Variable para poner el contenido de las sugerencias si tiene o no firma electrónica cada usuario que firma/aprueba/revisa
		// var contenido_carga_revisa 		= ""; 	// Variable para generar el contenido de cada uno de los usuarios que revisa (1,2,3,4 o 5) para concatenarlo despues con los usuarios que firman/aprueban
				
		for (var i = 1; i <= 5; i++) {
			var revisa_doc 			= $("#revisa_doc"+i).val();
			var cargo_revisa_doc 	= $("#cargo_revisa_doc"+i).val();

			/* Obtiene el valor del input de tipo texto "revisaX_tiene_pass2" */
			var revisa_tiene_pass2 	= $("#revisa"+i+"_tiene_pass2").val();

			if(revisa_tiene_pass2=="NO"){
				habilita_firma_electronica+="el usuario <font color='red'><b>"+revisa_doc+" ( "+cargo_revisa_doc+" )</b></font> ,";
			}

			if(revisa_doc==""){
				break;
			}
			
			/**/
			var fila_nueva 		= "";
			var fin_fila_nueva 	= "";
			var prueba ="";

			var salta_tabla 		= "";		
			var salta_fin_tabla 	= "";		

			switch (i){
				case 1 :
					salta_tabla 	= "<tr>";
					salta_fin_tabla = "";
					break;
				case 2 :
				case 3 :
				case 4 :
					salta_tabla 	= "";
					salta_fin_tabla = "";
					break;

				case 5 :
					salta_tabla 	= "<tr>";
					salta_fin_tabla = "</tr>";
					break;
			}

			contenido_carga_revisa+=salta_tabla+"<td class='borde_tabla' style='cursor:pointer' onclick='volver_ventana_datos()' title='Haga clic para cambiar el valor de usuario que revisa el documento que está generando'>El usuario que hace la <b>REVISION ("+i+")</b> del documento es : <br><span class='dato_form'>"+revisa_doc+"</span><br>("+cargo_revisa_doc+")</td>"+salta_fin_tabla;
		}
	
		var tipo_solicitud 		= $("#medio_solicitud_firmas").val();
		var version_documento 	= $("#version_documento").val();

    	if(tipo_solicitud=='fisico'){
    		$("#enviarHtml").val("Generar Versión "+version_documento+" en físico del documento");
    	}else{
    		$("#enviarHtml").val("Generar Versión "+version_documento+" del documento electrónico");
    	}

		var firmante_habilitado = $("#firmante_tiene_pass2").val();
		var aprueba_habilitado 	= $("#aprueba_tiene_pass2").val();
		var revisa1_habilitado 	= $("#revisa1_tiene_pass2").val();
		var revisa2_habilitado 	= $("#revisa2_tiene_pass2").val();
		var revisa3_habilitado 	= $("#revisa3_tiene_pass2").val();
		var revisa4_habilitado 	= $("#revisa4_tiene_pass2").val();
		var revisa5_habilitado 	= $("#revisa5_tiene_pass2").val();

		if(firmante_habilitado=="NO"){
			habilita_firma_electronica+="el usuario <font color='red'><b>"+firmante_doc+" ( "+cargo_firmante_doc+" )</b></font><br>";
		}

		if(aprueba_habilitado=="NO"){
			habilita_firma_electronica+="el usuario <font color='red'><b> "+aprueba_doc+" ( "+cargo_aprueba_doc+" )</b></font><br>";
		}

		if(habilita_firma_electronica==""){
			var texto_firma_electronica = "<font color='green'>Este documento <b>SI</b> se puede firmar electrónicamente.</font><br><div style='background: #2f8df5; color: #FFFFFF; float: left; width: 250px;' title='Tramitar este documento mediante firmas electrónicas solicitando primero las segundas claves de quien aprueba, luego de quienes revisan y finalmente de quien firma para generar el documento final.' class='art_exp center' onclick='solicitar_firmas_usuarios(\"electronico\")'>Solicitar firmas electrónicamente</div> <div style='background: #2f8df5; color: #FFFFFF; float: left; width: 250px;' title='Tramitar este documento imprimiendo el documento físico solicitando el cargar despues el documento en formato PDF firmado en físico.' class='art_exp center' onclick='solicitar_firmas_usuarios(\"fisico\")'>Generar formato para imprimir.</div>";

	   		// $("#verPdf").val("Vista previa para solicitarrrrrrrrrrrr electrónicamente");
		}else{
			var texto_firma_electronica = "<font color='red'>Este documento <b>NO</b> se puede firmar electrónicamente.</font><br>Se podría firmar electrónicamente si "+habilita_firma_electronica+" habilitara su segunda clave para firma electrónica.<br><font color='blue'>Por esa razón, este documento por defecto se genera en formato para imprimir, solicitando cargar el PDF firmado físicamente para finalizar proceso.</font>";
    		// $("#verPdf").val("Vista previa para imprimir en físico");
		}

		var nombre_serie 	= $("#nombre_serie").val();
		var nombre_subserie = $("#nombre_subserie").val();
		$("#vista_previa_listado_usuarios").html("<h2 class='descripcion' style='cursor:pointer' onclick='volver_ventana_datos(); $(\"#asunto_doc\").focus();' title='Haga clic para cambiar el valor del Asunto del documento que está generando'>El asunto de este documento es (<span class='dato_form'>"+asunto_doc+"</span>)</h2><table style='border-collapse: collapse;' width='99%'><tr><td rowspan='2' width='10%' class='borde_tabla' style='cursor:pointer' onclick='volver_ventana_datos(); $(\"#codigo_serie\").focus();' title='Haga clic para cambiar el valor de la Serie - Subserie del documento que está generando'>La serie y subserie de este documento son :<br>Serie:(<span class='dato_form'>"+codigo_serie+")</span><br>"+nombre_serie+"<br><br>Subserie:(<span class='dato_form'>"+codigo_subserie+"</span>)<br>"+nombre_subserie+"</td><td class='borde_tabla' style='cursor:pointer' onclick='volver_ventana_datos(); $(\"#tamano\").focus();' title='Haga clic para cambiar el valor a Carta u Oficio'>El tamaño de hoja de este documento es :<br>(<span class='dato_form'>"+tamano.toUpperCase()+"</span>)</td><td width='10%' class='borde_tabla' style='cursor:pointer' onclick='volver_ventana_datos(); $(\"#seleccionar_expediente\").focus();' title='Haga clic para cambiar el valor del expediente al que va a ser asignado el documento que está generando'> Este documento va a ser incluido en el expediente: <span class='dato_form'>"+id_expediente+"</span><br>("+seleccionar_expediente+")</span><td class='borde_tabla' style='cursor:pointer' onclick='volver_ventana_datos()' title='Haga clic para cambiar el usuario que va a firmar el documento que está generando'>El usuario que <b>FIRMA</b> el documento es : <br><span class='dato_form'>"+firmante_doc+"</span<br>("+cargo_firmante_doc+")</td>"+contenido_carga_revisa+"<tr><td colspan='6' style='text-align:justify;'>"+texto_firma_electronica+"</td></tr></table>");
		
		$("#formulario_datos_radicado").animate({ // Para volver al 50% el width de la tabla.
	    	width: "838px"
	    },{
	      	queue: false,
	      	duration: 50
	    })
	    $("#body_resoluciones").css("width","calc(100% - 838px)")
	    $("#body_resoluciones").show()

		tinymce.get('editor').focus();
	}

	function volver_ventana_datos(){
		if($("#enviarHtml").is(":visible")){
			$("#verPdf").trigger("click");
		}

		$("#elaborar_contenido").hide();
		$("#botones_plantilla_radicacion_salida").hide();

		$("#datos_clasificacion_radicado").show();
		$("#datos_creacion_radicado").show();
		$("#botones_contenido_documento").show();

		$("#asunto_doc").focus();

		$("#formulario_datos_radicado").animate({ // Para volver al 50% el width de la tabla.
	    	width: "99%"
	    },{
	      	queue: false,
	      	duration: 50
	    })
	    $("#body_resoluciones").animate({ // Para volver al 50% el width de la tabla.
	    	width: "0%"
	    },{
	      	queue: false,
	      	duration: 50
	    })

	}


/************************************************************** 
* @class Funcion para armar el valor de usuario_actual para poner en la base de datos.
* @description hay que arreglarla ya que no es funcional cuando no es nuevo el documento
* @param string{} No recibe parámetros. 
* @return {} No retorna valores. 
**************************************************************/
	// function validar_usuario_actual(){
	// 	var lista 		= $("#lista_usuario_actual").val();
	// 	var firmante 	= $("#login_firmante").val();
	// 	var aprueba 	= $("#login_aprueba").val();
	// 	var elabora 	= $("#login_elabora").val();

	// 	if(lista==firmante){
	// 		firmante="";
	// 	}
	// 	if(lista==aprueba){
	// 		aprueba="";
	// 	}
	// 	if(lista==elabora){
	// 		elabora="";
	// 	}
	// 	if(firmante==aprueba){
	// 		aprueba="";
	// 	}
	// 	if(firmante==elabora){
	// 		elabora="";
	// 	}
	// 	if(aprueba==elabora){
	// 		elabora="";
	// 	}

	// 	lista+=",";
	// 	if(firmante!=""){
	// 		lista+=firmante+",";
	// 	}
	// 	if(aprueba!=""){
	// 		lista+=aprueba+",";
	// 	}
	// 	if(elabora!=""){
	// 		lista+=elabora+",";
	// 	}
	// 	$("#usuario_actual").val(lista);
	// }
</script>
<style type="text/css">
#contenido{
	overflow 	: auto;
}	
#body_resoluciones{
	/*background-color: blue;*/
	float 			: left;
	width 			: 0px;
	/*height 			: 400px;*/
}
.borde_tabla{
	border 		: 4px solid white;
	color 		: #2D3A4A;
	padding 	: 5px;
}
.dato_form{
	color: blue;
	font-weight: bold; 
}
#example1{
	height: 	850px; 
}

#formulario_datos_radicado{
	/*height  	: 50vh;*/
	float 		: left;
	overflow 	: scroll; 
	width 		: 99%; 
}

#visor_pdf{
	height 				: 500px;
}
@media screen and (max-width: 1350px) {
  	#body_resoluciones {
      	background-color 	: green;
  		height  			: 730px;
  	}
  	#contenido{
		height 				: 770px;
  	}
  	#formulario_datos_radicado{
		/*height  			: 665px; */
  	}
  	#visor_pdf{
  		height 				: 665px;
  	}
}
</style>
<?php
/* Se verifica si tiene numero de radicado, si lo tiene quiere decir que es una modificación. Si no es asi, quiere decir que es un radicado nuevo */
/* Se definen variables para crear la vista previa y el radicado */
	if(isset($_POST['radicado'])){  		//  Quiere decir que es una Modificación por lo que extrae las variables desde la base de datos en la tabla version_documentos combinada con la tabla radicado
		$radicado  	= $_POST['radicado']; 	

		if(isset($_POST['tipo_radicacion'])){  		//  Quiere decir que es una respuesta por lo que se debe modificar el formulario para coincidir con esta opcion
			// $tipo_radicacion 	= $_POST['tipo_radicacion']; // El tipo de radicacion que recibe (respuesta, etc)
			// $titulo_plantilla 	= "Modificacion plantilla de Resoluciones $radicado";

			// $query_cargar_datos_radicado = "select * from radicado r join datos_origen_radicado d on r.numero_radicado=d.numero_radicado where r.numero_radicado='$radicado'";

			// $fila_cargar_datos_radicado 	= pg_query($conectado,$query_cargar_datos_radicado);
			// $linea_cargar_datos_radicado 	= pg_fetch_array($fila_cargar_datos_radicado);

			// $codigo_serie_mod 			= $linea_cargar_datos_radicado['codigo_serie']; 
			// $codigo_subserie_mod 		= $linea_cargar_datos_radicado['codigo_subserie']; 
			// $id_expediente 		 		= $linea_cargar_datos_radicado['id_expediente'];
			// $id_expediente_mod 	 		= $linea_cargar_datos_radicado['id_expediente'];
			// $asunto_documento 			= $linea_cargar_datos_radicado['asunto'];
			// $asunto_mod 				= "";
			// $pre_asunto_mod 			= "";
			// $despedida_mod 				= ""; 
			// $firmante_mod 				= "";
			// $cargo_firmante_mod 		= "";
			// $firmado_mod			 	= "NO";	// Se encuentra firmado digitalmente (SI/NO)
			// $aprueba_mod 				= "";
			// $cargo_aprueba_mod 			= "";
			// $aprobado_mod			 	= $linea_cargar_datos_radicado['aprobado'];	// Se encuentra aprobado digitalmente (SI/NO)
			// $elabora_mod 				= "";
			// $cargo_elabora_mod 			= "";
			// $version_documento1		 	= "";
			// $version_documento 			= 1;
			// $codigo_contacto_mod	 	= $linea_cargar_datos_radicado['codigo_contacto'];
			// $codigo_carpeta1_mod	 	= $linea_cargar_datos_radicado['codigo_carpeta1'];
			// $tipo_radicado 				= '5'; //  Tipo de radicado (3- Interna, 1- Entrada, 2- Salida, etc)

			// if($codigo_serie_mod!=''){
			// 	echo "<script>consulta_listado_series2('$codigo_serie_mod','$codigo_dependencia','codigo_serie'); cargar_codigo_subserie2('$codigo_serie_mod','$codigo_subserie_mod','$codigo_dependencia','respuesta_radicado','codigo_subserie')</script>";
			// }			
			// // echo "<script>
			// // 	$('#datos_creacion_radicado').slideDown('slow');
			// // 	$('#botones_plantilla_radicacion_salida').slideDown('slow');
			// // </script>";
		}else{ 	//  Quiere decir que NO es una respuesta por lo que se debe modificar el formulario para coincidir con esta opcion que en este caso es una modificación al radicado
			$tipo_radicacion  = "";  	// El tipo de radicacion que recibe (respuesta, etc)
			$titulo_plantilla = "Plantilla Modificación de Radicado $radicado";

			/* Tiene el regexp_split_to_array porque no pasaba la version del numero 10. */ 

			// $query_existe_exp = "select * from radicado where numero_radicado='$radicado'";
			$query_datos_modificar_radicado = " select * from radicado r join version_documentos v on r.numero_radicado=v.numero_radicado join subseries s on r.dependencia_radicador = s.codigo_dependencia and r.codigo_serie=s.codigo_serie and r.codigo_subserie=s.codigo_subserie join usuarios u on v.usuario_que_firma=u.nombre_completo where r.numero_radicado='$radicado' order by regexp_split_to_array(version, E'\\\.')::integer[] desc";

			// echo "$query_datos_modificar_radicado<br>";
			$fila_datos_modificar_radicado 	= pg_query($conectado,$query_datos_modificar_radicado);
			$linea_datos_modificar_radicado = pg_fetch_array($fila_datos_modificar_radicado);

			$aprobado_mod			= $linea_datos_modificar_radicado['aprobado'];
			$aprueba_mod		 	= $linea_datos_modificar_radicado['usuario_que_aprueba'];
			$asunto_documento 		= $linea_datos_modificar_radicado['asunto'];
			$asunto_mod 			= $asunto_documento;
			$cargo_aprueba_mod		= $linea_datos_modificar_radicado['cargo_usuario_que_aprueba'];
			$cargo_elabora_mod		= $linea_datos_modificar_radicado['cargo_usuario_que_elabora'];
			$cargo_firmante			= $linea_datos_modificar_radicado['cargo_usuario_que_firma'];
			$cargo_firmante_mod		= $linea_datos_modificar_radicado['cargo_usuario_que_firma'];
			$codigo_carpeta1_mod	= $linea_datos_modificar_radicado['codigo_carpeta1'];
			$codigo_contacto_mod	= $linea_datos_modificar_radicado['codigo_contacto'];
			$elabora_mod		 	= $linea_datos_modificar_radicado['usuario_que_elabora'];
			$firmante_mod		 	= $linea_datos_modificar_radicado['usuario_que_firma'];
			$id_exp	 				= $linea_datos_modificar_radicado['id_expediente'];
			$login_firmante			= $linea_datos_modificar_radicado['login']; // Login del usuario que firma
			$nombre_serie	 		= $linea_datos_modificar_radicado['nombre_serie'];
			$nombre_subserie	 	= $linea_datos_modificar_radicado['nombre_subserie'];
			$pre_asunto_mod 		= $linea_datos_modificar_radicado['html_asunto']; 
			$usuarios_visor	 		= $linea_datos_modificar_radicado['usuarios_visor'];
			$version_docum 			= $linea_datos_modificar_radicado['version'];
			$version_documento 		= $version_docum+1;
			
			if($id_exp==""){
				$id_expediente_mod	= "";
				$nombre_expediente 	= "";
			}else{
				$id_expediente 		= str_replace(",", "", "$id_exp"); // Solo cuando es un solo expediente del radicado quita la coma.
				$query_expediente 	= "select * from expedientes where id_expediente='$id_expediente'";
				$fila_expediente 	= pg_query($conectado,$query_expediente);
				$linea_expediente 	= pg_fetch_array($fila_expediente);

				$id_expediente_mod	= $linea_expediente['id_expediente'];
				$nombre_expediente 	= $linea_expediente['nombre_expediente'];
			}
			/* Si el radicado tiene asignado quien aprueba */
			if($aprueba_mod==""){
				$login_aprueba = "";
			}else{
				$aprueba_mod1 	= trim($aprueba_mod);
				$query_aprueba 	= "select * from usuarios where nombre_completo ilike '%$aprueba_mod1%'";
				$fila_aprueba 	= pg_query($conectado,$query_aprueba);
				$linea_aprueba 	= pg_fetch_array($fila_aprueba);

				/* Variable para eliminar del listado de usuarios_visor */
				$login_aprueba	= $linea_aprueba['login'];
			}

			/* Genera el login del usuario que elabora */
			$elabora_mod1  	= trim($elabora_mod);	
			$query_elabora 	= "select * from usuarios where nombre_completo='$elabora_mod1'";
			$fila_elabora 	= pg_query($conectado,$query_elabora);
			$linea_elabora 	= pg_fetch_array($fila_elabora);
			$login_elabora	= $linea_elabora['login'];


			// echo "$query_expediente<br> $query_aprueba <<---------";

			$usuarios_visor = str_replace("$login_firmante,", "", $usuarios_visor);
			$usuarios_visor = str_replace("$login_aprueba,", "", $usuarios_visor);
			$usuarios_visor = str_replace("$login_elabora,", "", $usuarios_visor);

			// echo "<br>firmante->$login_firmante <br>aprueba->$login_aprueba<br>listado -> $usuarios_visor<br>";

			// Extraigo cada uno de los usuarios de la lista separados por coma	
			$usu  = explode(",", $usuarios_visor);
			$max  = sizeof($usu);
			$max2 = $max-1;

			for ($p=0; $p < $max2; $p++) {  
				$login_completo 		= $usu[$p];
				$q=$p+1;

				$query_llena_revisa_ap 	= "select * from usuarios where login='$login_completo'";
				$fila_llena_revisa_ap 	= pg_query($conectado,$query_llena_revisa_ap);
				$linea_llena_revisa_ap 	= pg_fetch_array($fila_llena_revisa_ap);
				$login_llena_revisa_ap	= $linea_llena_revisa_ap['login'];
				$nombre_completo_rev_ap	= $linea_llena_revisa_ap['nombre_completo'];
				$cargo_usuario_rev_ap	= $linea_llena_revisa_ap['cargo_usuario'];

				if($cargo_usuario_rev_ap=='') $cargo_usuario_rev_ap="Usuario sin cargo en el sistema";
				echo "<script>
					timerid = setTimeout(function() {
						carga_revisa_doc('$nombre_completo_rev_ap','$login_completo','$cargo_usuario_rev_ap','$q')
					},3000);
				</script>";
			}
				
			$codigo_serie_mod 		= $linea_datos_modificar_radicado['codigo_serie']; 
			$codigo_subserie_mod 	= $linea_datos_modificar_radicado['codigo_subserie']; 
			
			$tamano			 		= $linea_datos_modificar_radicado['tamano'];
			if($tamano=='oficio'){
				$opcion_carta 	= "";
				$opcion_oficio 	= "selected";
			}else{
				$opcion_carta 	= "selected";
				$opcion_oficio 	= "";
			}

			$tipo_radicado 				= '5'; //  Tipo de radicado (3- Interna, 1- Entrada, 2- Salida, etc)

			// $firmado_mod			 				= $linea_datos_modificar_radicado['firmado'];


			// Valida que la secuencia de salida si exista y carga la serie y subserie
			echo "<script>consulta_listado_series2('$codigo_serie_mod','$codigo_dependencia','codigo_serie'); cargar_codigo_subserie2('$codigo_serie_mod','$codigo_subserie_mod','$codigo_dependencia','respuesta_radicado','codigo_subserie');</script>"; 
		}
	}else{		// Si es un radicado nuevo por lo que va definiendo las variables para dar la vista previa y generar el documento		
		echo "<script>carga_elabora('$nombre','$login','$cargo_usuario');</script>";

		$id_expediente_mod 	 		= "";
		$asunto_mod 				= "";
		$codigo_serie_mod 			= ""; 
		$codigo_subserie_mod 		= ""; 
		$nombre_expediente 	 		= "";
		$pre_asunto_mod 			= "";
		$radicado 					= "";
		$tipo_radicacion  			= "";  	// El tipo de radicacion que recibe (respuesta, etc)
		$aprobado_mod 				= "NO";
		$aprueba_mod 				= "";
		$cargo_aprueba_mod 			= "";
		$asunto_documento 			= "";
		$cargo_elabora_mod 			= "";
		$cargo_firmante_mod 		= "";
		$codigo_carpeta1_mod 		= "";
		$elabora_mod 				= "";
		$firmante_mod 				= "";
		$tipo_radicado 				= '5'; 	//  Tipo de radicado (1- Entrada, 2- Salida, 3-Normal, 4-Interna, 5-Resoluciones etc)
		$titulo_plantilla 			= "Plantilla Radicación de Resoluciones";
		$version_documento 			= "1";
		$login_firmante 			= "";
		$login_aprueba  			= "";
		// $anexos_mod 				= "";
		// $cargo_destinatario_mod 	= "";
		// $cc_mod 					= "";
		// $dignatario_mod 			= "";		
		// $direccion_mod 				= "";
		// $firmado_mod 				= "NO";
		// $mail_mod 					= "";
		// $remitente_destinatario_mod = "Persona Natural";		
		// $telefono_mod 				= "";		
		// $ubicacion_mod 				= "BOGOTA, D.C. (BOGOTA) COLOMBIA-AMERICA";

		/* Por defecto trae seleccionado tamaño "carta" */
		$opcion_carta 	= "selected";
		$opcion_oficio 	= "";

		$codigo_contacto_mod 		= "";
		$nombre_serie	 			= "";
		$nombre_subserie	 		= "";
	}
/* Hasta aqui variables para crear la vista previa y el radicado */

	// $ciudad 		= "Bogotá,"; // Ciudad para el encabezado de la plantilla.

	/*Fecha que se realiza la transaccion (hoy)*/	
    $fechaDocumento 	= $b->traduce_fecha_letra($date); // Traduce fecha formato "17 de Julio del 2019"
	// $fecha 				= "$ciudad $fechaDocumento";

 	$codigo_entidad 	= $_SESSION['codigo_entidad'];

 	switch ($codigo_entidad) {
 		case 'AV1':
			$path_encabezado 	= '../../imagenes/logos_entidades/encabezado_rad_av1.png';
			$path_piedepagina 	= '../../imagenes/logos_entidades/pie_rad_av1.png';
 			break;

 		case 'EJC':	
 		case 'EJEC':
			$path_encabezado 	= '../../imagenes/logos_entidades/encabezado_rad_ejc.png';
			$path_piedepagina 	= '../../imagenes/logos_entidades/pie_rad_ejc.png'; 		
 			break;
 		
 		default:
			$path_encabezado 	= '../../imagenes/logos_entidades/encabezado_rad_gc1.png';
			$path_piedepagina 	= '../../imagenes/logos_entidades/pie_rad_gc1.png'; 		
 			break;
 	}

	// Extensión de las imagenes de encabezado y pie de pagina para la plantilla
	$type_encabezado	= pathinfo($path_encabezado, PATHINFO_EXTENSION);
	$type_piedepagina	= pathinfo($path_piedepagina, PATHINFO_EXTENSION);
	 
	// Cargando las imagenes de encabezado y pie de pagina para la plantilla
	$data_encabezado 	= file_get_contents($path_encabezado);
	$data_piedepagina	= file_get_contents($path_piedepagina);
	 
	// Decodificando las imagenes de encabezado y pie de pagina en base64
	$base64_encabezado 	= 'data:image/' . $type_encabezado . ';base64,' . base64_encode($data_encabezado);
	$base64_piedepagina = 'data:image/' . $type_piedepagina . ';base64,' . base64_encode($data_piedepagina);
	 
	// Mostrando las imagenes del encabezado y pie de pagina
	// echo '<img src="'.$base64_encabezado.'"/>';
	// echo '<img src="'.$base64_piedepagina.'"/>';


	// if($path_firma1!=""){
	// 	$path_firma		= "../../imagenes/fotos_usuarios/$path_firma1";
	// 	$type_firma		= pathinfo($path_firma1, PATHINFO_EXTENSION);
	// 	$data_firma 	= file_get_contents($path_firma);
	// 	$base64_firma 	= 'data:image/' . $type_firma . ';base64,' . base64_encode($data_firma);
	// }else{
	// 	$base64_firma 	= "";
	// }

	// echo '<img src="'.$base64_firma.'"/>';

	/* Desde la sesion trae el nombre de usuario y el login */
	$nombre_usuario = $_SESSION['nombre'];
	$login_usuario 	= $_SESSION['login'];

	/* Inicio PRUEBAS definiendo aprobado y/o firmado */
	// $indicador_aprobado = "";
	// if($aprobado_mod=="SI"){
	// 	$indicador_aprobado="<div class='detalle center' title='Documento ya aprobado'>El documento ya ha sido aprobado por el usuario <font color='green'>$aprueba_mod</font>.</div>";
	// }else{
	// 	if($aprueba_mod==""){ // Si es un radicado nuevo no muestra todavía ningun indicador de aprobado.
	// 		$indicador_aprobado = "";
	// 	}else{
	// 		if($nombre_usuario==$aprueba_mod){
	// 			$indicador_aprobado = "<div class='art_exp center' style='background: #2aa646;' title='Aprobar documento electrónico' onclick=\"cargar_aprueba('$login_usuario','$radicado','aprobar')\"><img src='imagenes/iconos/aprobar_documento.png' height='35px;'></div>";
	// 			// $indicador_aprobado = "<div class='art_exp center' style='background: #2aa646;' title='Documento por aprobar' onclick=\"cargar_aprueba('$login_usuario','$radicado','aprobar')\">Al generar el radicado el documento es aprobado por <b>$nombre_usuario</b></div>";
	// 		}else{
	// 			$indicador_aprobado = "<div class='descripcion center' title='Falta por aprobar documento'>Falta que el usuario <font color='red'>$aprueba_mod</font> apruebe el documento.</div>";
	// 		}
	// 	}
	// }
	// $indicador_firma = "";

	// // echo "---->$firmado_mod";
	// if($firmado_mod=="SI"){
	// 	$indicador_firma="<div class='detalle center' title='Documento ya firmado'>El documento ya ha sido firmado electrónicamente por el usuario <font color='green'>$firmante_mod</font>.</div>";
	// }else{
	// 	if($firmante_mod==""){ // Si es un radicado nuevo no muestra todavía ningun indicador de aprobado.
	// 		$indicador_firma = "";
	// 	}else{
	// 		if($nombre_usuario==$firmante_mod){
	// 			$indicador_firma = "<div class='art_exp center' style='background: #2aa646;' title='Firmar documento electrónicamente' onclick=\"cargar_aprueba('$login_usuario','$radicado','aprobar')\"><img src='imagenes/iconos/aprobar_documento.png' height='35px;'></div>";
	// 		}else{
	// 			$indicador_firma = "<div class='descripcion center' title='Falta por firmar electrónicamente documento'>Falta que el usuario <font color='red'>$firmante_mod</font> firme el documento.</div>";
	// 		}
	// 	}
	// }

	// if($base64_firma==""){
	// 	$indicador_firma = "<div class='descripcion center' title='La firma de su usuario no se encuentra en la base de datos.'>Falta que el usuario <font color='red'>$login</font> cargue su firma (Rúbrica) en el sistema.<br> Comuníquese con el administrador del sistema.</div>";
	// }
	/* Fin PRUEBAS definiendo aprobado y/o firmado */
?>
<body>
	<div class="center">
		<br>
		<h1 style="margin-top:-10px;"><?php echo $titulo_plantilla ?></h1>		
	</div>
	<div id="formulario_datos_radicado" class="col-md-5">
        <form style="text-align: center;">
        	<input type="hidden" id="lista_firma_aprueba_revisa" placeholder="lista_firma_aprueba_revisa">
       		<table id="datos_clasificacion_radicado" border="0">
				<tr>
					<td class="descripcion">
						Tamaño Hoja
					</td>			
					<td class="detalle">
						<select id="tamano" class='select_opciones' onchange="validar_serie_subserie(); $('#codigo_serie').focus(); ">
							<option value='carta' <?php echo $opcion_carta ?>>Carta</option>
							<option value='oficio' <?php echo $opcion_oficio ?>>Oficio</option>
						</select>
					</td>		
					<td class="descripcion" width="20%">
						<input type="hidden" id="codigo_dependencia" value="<?php echo $codigo_dependencia; ?>" readonly>
						<input type="hidden" id="nombre_dependencia" value="<?php echo $nombre_dependencia; ?>" readonly>
						Codigo Serie :
					</td>
					<td class="detalle" width="30%">
						<input type="hidden" id="nombre_serie" placeholder="nombre_subserie" <?php echo "value='$nombre_serie'" ?>>
						<select id='codigo_serie' title='Seleccione el código de la serie documental' class='select_opciones' <?php echo "onchange='cargar_codigo_subserie2(this.value,\"\",\"$codigo_dependencia\",\"radicacion_salida\",\"codigo_subserie\"); focus_subserie()'" ?>>
						</select>
						<div id="error_codigo_serie" class="errores">Debe seleccionar por lo menos una serie del listado</div>
					</td>
					
					<td class="descripcion" width="20%">
						Codigo Subserie
					</td>
					<td class="detalle" width="30%" >
						<input type="hidden" id="nombre_subserie" placeholder="nombre_subserie" <?php echo "value='$nombre_subserie'" ?>>
						<select id='codigo_subserie' title='Seleccione el código de la serie documental' class='select_opciones' onchange='validar_serie_subserie(); $("#buscador_expediente").focus()'>
							<option value=''>No hay subseries asociadas a la serie seleccionada</option>
						</select>
						<div id="error_codigo_subserie" class="errores">No existen subseries asociadas a la serie seleccionada</div>
						<div id="error2_codigo_subserie" class="errores">Debe seleccionar por lo menos una subserie del listado</div>
					</td>
				</tr>
				<tr id="input_seleccionar_expediente" class="hidden">
					<td class="descripcion "colspan="3">
						Expediente al que va a pertenecer éste documento
					</td>
					<td class="detalle" colspan="3">
						<input type="hidden" id="id_expediente" <?php echo "value='$id_expediente_mod'" ?>>	
						<input type="hidden" id="nombre_expediente" <?php echo "value='$nombre_expediente'" ?>>	
						<input type="hidden" id="codigo_contacto" <?php echo "value='$codigo_contacto_mod'" ?>>	
						<input type="search" id="seleccionar_expediente" class=" hidden" placeholder="Este campo es opcional. No es obligatorio - seleccionar_expediente" title="Este campo es opcional. No es obligatorio - seleccionar_expediente" onclick="$('#buscador_expediente').slideDown('slow');$('#buscador_expediente').focus(); $('#seleccionar_expediente').slideUp('slow');" readonly>

						<input type="search" id="buscador_expediente" <?php echo "value='$nombre_expediente'" ?>  placeholder="Ingrese nombre de expediente. No es obligatorio" onblur="valida_expediente_vacio()" title="Este campo es opcional. No es obligatorio">
						
						<div id="seleccionar_expediente_max" class="errores">El campo de expediente no puede ser mayor a 100 caracteres (numeros o letras)</div>
						<div id="seleccionar_expediente_min" class="errores">El campo de expediente no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="seleccionar_expediente_null" class="errores">El asunto o nombre del expediente es obligatorio</div>
						<div id="error_seleccionar_expediente" class="errores">El numero o asunto del expediente no existe en el inventario. Ingrese por favor un numero o asunto de expediente válido</div>	
						<div id="seleccionar_expediente_invalido" class="errores">Debe seleccionar un asunto o nombre de expediente válido.</div>	

						<div id="resultado_seleccionar_expediente" style="overflow-x: auto;max-height: 100px;"></div>
					</td>
				</tr>
			</table>			
			<input type="hidden" id="numero_radicado" <?php echo "value='$radicado'"; ?>>
			<input type="hidden" id="nuevo_nombre_documento" value="" placeholder="Nuevo nombre del documento">

			<table id="datos_creacion_radicado" class="hidden" width="100%">	
				<tr height="20px" id='fila_firmante'>
					<td class="descripcion" title="Funcionario que firma éste documento">
						<input type='hidden' placeholder ='lista_usuario_actual' id='lista_usuario_actual' <?php echo "value='$login'" ?>>
						<input type='hidden' placeholder="login_firmante" id='login_firmante' <?php echo "value='$login_firmante'" ?>>
						<!-- Variable para validar si el usuario firmante tiene segunda clave o no -->
						<input type='hidden' placeholder="firmante_tiene_pass2" id='firmante_tiene_pass2'>
						Usuario que va a <b><u>FIRMAR</u></b> este documento 
					</td>
					<td class="detalle" title="Funcionario que firma éste documento">
						<input type="search" id="firmante_doc" title="Ingrese el nombre del funcionario que va a firmar el documento" placeholder="Ingrese el nombre del funcionario que va a firmar el documento" onblur="trim('firmante_doc')">	

						<!-- Errores al ingresar informacion -->
						<div id="sugerencias_firmante" style="max-height: 200px; overflow: scroll; display:none;"></div>
						<div id="error_firmante" class="errores">No se han encontrado resultados</div>
						<div id="firmante_doc_null" class="errores">Debe ingresar el nombre del firmante del documento</div>

						<!-- <div id="indicador_firma"><?php //echo $indicador_firma; ?></div> -->
					</td>
					<td class="descripcion" title="Cargo del funcionario que firma éste documento">
						Cargo de <span id="cargo_firmante_rs"> quien firma éste documento</span>
					</td>
					<td class="detalle" title="Cargo del funcionario que firma éste documento">
						<input type="search" id="cargo_firmante_doc" title="Ingrese cargo del funcionario que va a firmar el documento" placeholder="Ingrese cargo del funcionario que va a firmar el documento" onblur="trim('cargo_firmante_doc')">	
						<div id="sugerencia_cargo_firmante"></div>
						<!-- Errores al ingresar informacion -->
						<div id="cargo_firmante_doc_max" class="errores">El cargo del funcionario que firma el documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='cargo_firmante_doc_contadormax'></u></b> caracteres)</div>		
						<div id="cargo_firmante_doc_min" class="errores">El cargo del funcionario que firma el documento no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="cargo_firmante_doc_null" class="errores">Debe ingresar el cargo del funcionario que firma el documento</div>
					</td>
				</tr>
				<tr height="20px" id='fila_aprueba' class="hidden">
					<td class="descripcion" title="Funcionario que debe dar su visto bueno para aporbar éste documento antes de ser radicado.">
						<input type='hidden' placeholder="login_aprueba"  id='login_aprueba' <?php echo "value='$login_aprueba'" ?>>
						<input type='hidden' placeholder="aprueba_tiene_pass2" id='aprueba_tiene_pass2'>
						Usuario que va a <b><u>APROBAR</u></b> este documento
					</td>
					<td class="detalle">
						<input type="search" id="aprueba_doc" onblur="trim('aprueba_doc')" title="Funcionario que debe dar su visto bueno para aporbar éste documento antes de ser radicado.">	
						<div id="aprueba_doc_null" class="errores">No se han encontrado resultados</div>
						<div id="sugerencias_aprueba" style="max-height: 200px; overflow: scroll; display:none;"></div>

						<!-- <div id="indicador_aprobado"><?php//  echo $indicador_aprobado; ?></div> -->
					</td>
					<td class="descripcion" title="Cargo del funcionario que debe dar su visto bueno para aporbar éste documento antes de ser radicado.">
						Cargo de <span id="aprueba_rs"> quien aprueba éste documento</span> 
					</td>
					<td class="detalle" title="Cargo del funcionario que debe dar su visto bueno para aporbar éste documento antes de ser radicado.">
						<input type="search" id="cargo_aprueba_doc" onblur="trim('cargo_aprueba_doc')">	
						
						<div id="sugerencia_cargo_aprueba"></div>
						<div id="cargo_aprueba_doc_max" class="errores">El cargo del firmante del documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='cargo_aprueba_doc_contadormax'></u></b> caracteres)</div>		
						<div id="cargo_aprueba_doc_min" class="errores">El cargo del firmante del documento no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="cargo_aprueba_doc_null" class="errores">Debe ingresar el cargo del funcionario que aprueba el documento</div>

					</td>
				
				</tr>
				<tr height="20px" id='fila_elaborado' class="hidden">
					<td class="descripcion" title="Funcionario que redacta éste documento.">
						<input type='hidden' id='login_elabora' <?php echo "value='$login'" ?>>
						Elaborado por 
					</td>
					<td class="detalle" title="Funcionario que redacta éste documento.">
						<input type="search" id="elabora_doc" onblur="trim('elabora_doc')" <?php echo "value='$nombre_usuario'"; ?>>
						<div id="sugerencias_elabora" style="max-height: 200px; overflow: scroll; display:none;"></div>
						<div id="elabora_doc_null" class="errores">Debe ingresar el nombre del funcionario que elabora el documento</div>
					</td>
					<td class="descripcion" title="Cargo del funcionario que redacta éste documento.">
						Cargo de <span id="elabora_rs"> quien elabora éste documento</span> 
					</td>
					<td class="detalle" title="Cargo del funcionario redacta éste documento.">
						<input type="search" id="cargo_elabora_doc" onblur="trim('cargo_elabora_doc')" <?php echo "value='$cargo_usuario'"; ?>>	
						<div id="cargo_elabora_doc_null" class="errores">Debe ingresar el cargo del funcionario que redacta el documento</div>
					</td>
					<?php 
						echo "
		                    <input type='hidden' id='headerImg' value='$base64_encabezado'>
		                    <input type='hidden' id='footerImg' value='$base64_piedepagina'>              
		                    <input type='hidden' id='nombre_usuario' value='$nombre_usuario'>
						";
					?>                       
				</tr>
				<tr id='contenedor_revisado1' class="hidden">
					<td class="descripcion" title="Funcionario que revisa ésta respuesta.">
						<input type='hidden' placeholder="login_revisa1" id='login_revisa1'>
						<input type='hidden' placeholder="revisa1_tiene_pass2" id='revisa1_tiene_pass2'>
						Revisado (1) por 
					</td>
					<td class="detalle" title="Funcionario que revisa éste documento.">
						<input type="search" id="revisa_doc1" onblur="trim('revisa_doc1')" placeholder="Nombre del funcionario revisa éste documento." onkeyup="revisa_doc_keyup('1')">	
						<div id="sugerencias_revisa_doc1" style="max-height: 200px; overflow: scroll; display:none;"></div>

						<div id="revisa_doc1_null" class="errores">Debe ingresar el nombre del funcionario que revisa el documento</div>
					</td>
					<td class="descripcion" title="Cargo del funcionario que revisa éste documento.">
						Cargo de <span id="revisa_doc1_rs"> quien revisa éste documento</span> 
					</td>
					<td class="detalle" title="Cargo del funcionario revisa éste documento.">
						<input type="search" id="cargo_revisa_doc1" placeholder="Cargo del funcionario revisa éste documento." onkeyup="carga_cargo_revisa_doc('1')" onblur="trim('cargo_revisa_doc1')">	

						<div id="sugerencia_cargo_revisa_doc1"></div>
						<div id="cargo_revisa_doc1_max" class="errores">El cargo del funcionario que revisa el documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='cargo_revisa_doc1_contadormax'></u></b> caracteres)</div>		
						<div id="cargo_revisa_doc1_min" class="errores">El cargo del funcionario que revisa el documento no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="cargo_revisa_doc1_null" class="errores">Debe ingresar el cargo del funcionario que revisa el documento</div>
					</td>                
				</tr>
				<tr id='contenedor_revisado2' class="hidden">
					<td class="descripcion" title="Funcionario que revisa ésta respuesta.">
						<input type='hidden' placeholder="login_revisa2" id='login_revisa2'>
						<input type='hidden' placeholder="revisa2_tiene_pass2" id='revisa2_tiene_pass2'>
						Revisado (2) por 
					</td>
					<td class="detalle" title="Funcionario que revisa éste documento.">
						<input type="search" id="revisa_doc2" onblur="trim('revisa_doc2')" placeholder="Nombre del funcionario revisa éste documento." onkeyup="revisa_doc_keyup('2')">	
						<div id="sugerencias_revisa_doc2" style="max-height: 200px; overflow: scroll; display:none;"></div>
						<div id="revisa_doc2_null" class="errores">Debe ingresar el nombre del funcionario que revisa el documento</div>
					</td>
					<td class="descripcion" title="Cargo del funcionario que revisa éste documento.">
						Cargo de <span id="revisa_doc2_rs"> quien revisa éste documento</span> 
					</td>
					<td class="detalle" title="Cargo del funcionario revisa éste documento.">
						<input type="search" id="cargo_revisa_doc2" placeholder="Cargo del funcionario revisa éste documento." onkeyup="carga_cargo_revisa_doc('2')" onblur="trim('cargo_revisa_doc2')">	

						<div id="sugerencia_cargo_revisa_doc2"></div>
						<div id="cargo_revisa_doc2_max" class="errores">El cargo del funcionario que revisa el documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='cargo_revisa_doc2_contadormax'></u></b> caracteres)</div>		
						<div id="cargo_revisa_doc2_min" class="errores">El cargo del funcionario que revisa el documento no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="cargo_revisa_doc2_null" class="errores">Debe ingresar el cargo del funcionario que revisa el documento</div>						
					</td>                
				</tr>
				<tr id='contenedor_revisado3' class="hidden">
					<td class="descripcion" title="Funcionario que revisa ésta respuesta.">
						<input type='hidden' placeholder="login_revisa3" id='login_revisa3'>
						<input type='hidden' placeholder="revisa3_tiene_pass2" id='revisa3_tiene_pass2'>
						Revisado (3) por 
					</td>
					<td class="detalle" title="Funcionario que revisa éste documento.">
						<input type="search" id="revisa_doc3" onblur="trim('revisa_doc3')" placeholder="Nombre del funcionario revisa éste documento." onkeyup="revisa_doc_keyup('3')">	
						<div id="sugerencias_revisa_doc3" style="max-height: 200px; overflow: scroll; display:none;"></div>
						<div id="revisa_doc3_null" class="errores">Debe ingresar el nombre del funcionario que revisa el documento</div>
					</td>
					<td class="descripcion" title="Cargo del funcionario que revisa éste documento.">
						Cargo de <span id="revisa_doc3_rs"> quien revisa éste documento</span> 
					</td>
					<td class="detalle" title="Cargo del funcionario revisa éste documento.">
						<input type="search" id="cargo_revisa_doc3" placeholder="Cargo del funcionario revisa éste documento." onkeyup="carga_cargo_revisa_doc('3')" onblur="trim('cargo_revisa_doc3')">	

						<div id="sugerencia_cargo_revisa_doc3"></div>
						<div id="cargo_revisa_doc3_max" class="errores">El cargo del funcionario que revisa el documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='cargo_revisa_doc3_contadormax'></u></b> caracteres)</div>		
						<div id="cargo_revisa_doc3_min" class="errores">El cargo del funcionario que revisa el documento no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="cargo_revisa_doc3_null" class="errores">Debe ingresar el cargo del funcionario que revisa el documento</div>
					</td>                
				</tr>
				<tr id='contenedor_revisado4' class="hidden">
					<td class="descripcion" title="Funcionario que revisa ésta respuesta.">
						<input type='hidden' placeholder="login_revisa4" id='login_revisa4'>
						<input type='hidden' placeholder="revisa4_tiene_pass2" id='revisa4_tiene_pass2'>
						Revisado (4) por 
					</td>
					<td class="detalle" title="Funcionario que revisa éste documento.">
						<input type="search" id="revisa_doc4" onblur="trim('revisa_doc4')" placeholder="Nombre del funcionario revisa éste documento." onkeyup="revisa_doc_keyup('4')">	
						<div id="sugerencias_revisa_doc4" style="max-height: 200px; overflow: scroll; display:none;"></div>
						<div id="revisa_doc4_null" class="errores">Debe ingresar el nombre del funcionario que revisa el documento</div>
					</td>
					<td class="descripcion" title="Cargo del funcionario que revisa éste documento.">
						Cargo de <span id="revisa_doc4_rs"> quien revisa éste documento</span> 
					</td>
					<td class="detalle" title="Cargo del funcionario revisa éste documento.">
						<input type="search" id="cargo_revisa_doc4" placeholder="Cargo del funcionario revisa éste documento." onkeyup="carga_cargo_revisa_doc('4')" onblur="trim('cargo_revisa_doc4')">	

						<div id="sugerencia_cargo_revisa_doc4"></div>
						<div id="cargo_revisa_doc4_max" class="errores">El cargo del funcionario que revisa el documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='cargo_revisa_doc4_contadormax'></u></b> caracteres)</div>		
						<div id="cargo_revisa_doc4_min" class="errores">El cargo del funcionario que revisa el documento no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="cargo_revisa_doc4_null" class="errores">Debe ingresar el cargo del funcionario que revisa el documento</div>
					</td>                
				</tr>
				<tr id='contenedor_revisado5' class="hidden">
					<td class="descripcion" title="Funcionario que revisa ésta respuesta.">
						<input type='hidden' placeholder="login_revisa5" id='login_revisa5'>
						<input type='hidden' placeholder="revisa5_tiene_pass2" id='revisa5_tiene_pass2'>
						Revisado (5) por 
					</td>
					<td class="detalle" title="Funcionario que revisa éste documento.">
						<input type="search" id="revisa_doc5" onblur="trim('revisa_doc5')" placeholder="Nombre del funcionario revisa éste documento." onkeyup="revisa_doc_keyup('5')">	
						<div id="sugerencias_revisa_doc5" style="max-height: 200px; overflow: scroll; display:none;"></div>
						<div id="revisa_doc5_null" class="errores">Debe ingresar el nombre del funcionario que revisa el documento</div>
					</td>
					<td class="descripcion" title="Cargo del funcionario que revisa éste documento.">
						Cargo de <span id="revisa_doc5_rs"> quien revisa éste documento</span> 
					</td>
					<td class="detalle" title="Cargo del funcionario revisa éste documento.">
						<input type="search" id="cargo_revisa_doc5" placeholder="Cargo del funcionario revisa éste documento." onkeyup="carga_cargo_revisa_doc('5')" onblur="trim('cargo_revisa_doc5')">	

						<div id="sugerencia_cargo_revisa_doc5"></div>
						<div id="cargo_revisa_doc5_max" class="errores">El cargo del funcionario que revisa el documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='cargo_revisa_doc5_contadormax'></u></b> caracteres)</div>		
						<div id="cargo_revisa_doc5_min" class="errores">El cargo del funcionario que revisa el documento no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="cargo_revisa_doc5_null" class="errores">Debe ingresar el cargo del funcionario que revisa el documento</div>
					</td>                
				</tr>
				<tr id='fila_revisa_aprueba' class="hidden">
					<td id='indicador_documento_electronico' class="detalle" colspan="2"></td><td id='' colspan="2">
						<!-- <center> -->
							<div id='boton_agregar_aprueba' class="art_exp center" onclick="muestra_fila_aprueba()" style="background: #2f8df5; color: #FFFFFF; float: left; width: 250px;">Agregar "Aprobado por"</div>
							<div id='boton_agregar_revisa' class="art_exp center" onclick="despliega_revisa_doc(6)" style="background: #2f8df5; color: #FFFFFF; float: left; width: 250px;">Agregar "Revisado por"</div>
						<!-- </center> -->
					</td>
				</tr>
				<tr><td><input type="hidden" id='contenido_carga_firmante' placeholder="contenido_carga_firmante"></td></tr>
				<tr><td><input type="hidden" id='contenido_carga_aprueba' placeholder="contenido_carga_aprueba"></td></tr>
				<tr><td><input type="hidden" id='contenido_carga_revisa1' placeholder="contenido_carga_revisa1"></td></tr>
				<tr><td><input type="hidden" id='contenido_carga_revisa2' placeholder="contenido_carga_revisa2"></td></tr>
				<tr><td><input type="hidden" id='contenido_carga_revisa3' placeholder="contenido_carga_revisa3"></td></tr>
				<tr><td><input type="hidden" id='contenido_carga_revisa4' placeholder="contenido_carga_revisa4"></td></tr>
				<tr><td><input type="hidden" id='contenido_carga_revisa5' placeholder="contenido_carga_revisa5"></td></tr>

			<!-- <table id="datos_creacion_radicado" width="100%">	 -->
				<!-- <input type="button" onclick="llenar_tinymce()" value="Llenar con contenido">
				<input type="button" onclick="copiar_tinymce()" value="Copiar Contenido"> -->
<?php 
			echo "
				<input type='hidden' id='tipo_radicado' value='$tipo_radicado' placeholder='tipo_radicado'>
				<input type='hidden' id='tipo_radicacion' value='$tipo_radicacion' placeholder='tipo_radicacion es decir, el tipo de radicacion que recibe (respuesta, etc)'>
				<input type='hidden' id='radicado_padre' value='$radicado' placeholder='radicado_padre'>
				<input type='hidden' id='codigo_serie_mod' value='$codigo_serie_mod' placeholder='codigo_serie_mod'>
				<input type='hidden' id='codigo_subserie_mod' value='$codigo_subserie_mod' placeholder='codigo_subserie_mod'>
				<input type='hidden' id='asunto_mod' value='$asunto_mod' placeholder='asunto_mod'>
				<input type='hidden' id='pre_asunto' value='$pre_asunto_mod' placeholder='pre_asunto'>
				<input type='hidden' id='firmante_mod' value='$firmante_mod' placeholder='firmante_mod'>
				<input type='hidden' id='cargo_firmante_mod' value='$cargo_firmante_mod' placeholder='cargo_firmante_mod'>
				<input type='hidden' id='aprueba_mod' value='$aprueba_mod' placeholder='aprueba_mod'>
				<input type='hidden' id='aprobado_mod' value='$aprobado_mod' placeholder='aprobado_mod'>
				<input type='hidden' id='cargo_aprueba_mod' value='$cargo_aprueba_mod' placeholder='cargo_aprueba_mod'>
				<input type='hidden' id='elabora_mod' value='$elabora_mod' placeholder='elabora_mod'>
				<input type='hidden' id='cargo_elabora_mod' value='$cargo_elabora_mod' placeholder='cargo_elabora_mod'>
				<input type='hidden' id='version_documento' value='$version_documento' placeholder='version_documento'>
				<input type='hidden' id='usuario_actual' value='$codigo_carpeta1_mod' placeholder='usuario_actual'>
			";
 ?>				
				<tr id='fila_asunto' class="hidden">	
					<td class="descripcion" height="20px;">
						Asunto del documento que se está generando
					</td>
					<td class="detalle" colspan="3">
						<input type="search" id="asunto_doc" <?php echo "value='$asunto_documento'"; ?>  title='Referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software.' placeholder="Ingrese la referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software" onblur="trim('asunto_doc')">

	 					<div id="asunto_doc_max" class="errores">La referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software no puede ser mayor a 500 caracteres. (Actualmente <b><u id='asunto_doc_contadormax'></u></b> caracteres)</div>		
	 					<div id="asunto_doc_min" class="errores">La referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software no puede ser menor a 6 caracteres (numeros o letras)</div>
	 					<div id="asunto_doc_null" class="errores">Debe ingresar la referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software</div>

					</td>
				</tr>
			</table>

			<center>
				<div id="botones_contenido_documento" style="display:none;">
					<input type="button" class="botones center" value="Elaborar contenido del Documento" title="Elaborar contenido del documento antes de generar vista previa PDF" onclick="valida_elaborar_contenido()">
				</div>			
			</center>

			<table id="elaborar_contenido" class="hidden" style="display: none; width: 80%;">	
				<tr>
					<td class="detalle">
						<center>
							<textarea id="editor"></textarea>
							<div id="vista_previa_listado_usuarios" style="float: left; text-align: left; width: 810px;"></div>
							<input type="hidden" id="medio_solicitud_firmas" placeholder="medio_solicitud_firmas" value="fisico">
						</center>
						<!-- <div style="float: right; font-size: 12px; font-style: italic;" id="estimado_paginas">Contenido estimado en 1 página (10%)</div> -->
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div id="body_resoluciones">
    	<div id="example1"></div>
		<!-- <div id="pdf" class="col-md-5"> -->
    	<!-- <iframe align="center" class="pdfobject-container centered" id="visor_pdf"></iframe> -->
   		<!-- </div> -->
	<!-- Validar consecutivo de la dependencia radicadora y tipo de documento -->
	</div>
	<br>
	<center>
		<input type="hidden" id="indicador_vista_previa" placeholder="indicador_vista_previa">
		<div id="botones_plantilla_radicacion_salida" style="display: none;">
			<!-- <input type="button" id="volver_datos" class="botones center" style="display: inline-block; width: 180px;" title="Volver a mostrar datos del radicado" onclick="volver_ventana_datos()" value="Volver"> -->
			<input type="button" id="verPdf" class="botones center" value="Vista previa para imprimir en físico" title="Vista previa del documento antes de guardar PDF generado">
			<!-- <input type="button" class="botones center" id="test" value='Ver HTML' onclick="vista_html()"> -->

			<span id="contenedor_boton_descargar_plantilla_respuesta" style="display: none; text-decoration: none;">
				<input type="button" class="botones center" id="enviarHtml" value='<?php echo "Generar Versión $version_documento del documento"; ?>' title='<?php echo "Generar documento en formato PDF con la versión $version_documento del documento"; ?>'>
			</span>
		</div>			
	</center>
	<!-- <div id="visor_html" style="display: none; width: 764px;"></div> -->

<!-- Div que contiene ventana modal para solicitar prestamo --> 
    <div id="ventana_aprobar_documento" class="ventana_modal">
        <div class="form">
            <div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
            <h1>Formulario aprobar documento</h1>
            <hr>
            <form method="post" autocomplete="off">
                <table border ="0">
                    <tr>
                        <td class="descripcion" width="30%">Contraseña del usuario <?php echo $nombre_usuario ?></td>
                        <td class="detalle">
                        	<input type="hidden" id="tipo_aprueba_firma">
                            <input type="password" id="contr_confirma_aprobado" title="Ingrese su password para aprobar aquí." placeholder="Ingrese su password para aprobar aqui">
							<div id="error_contr_confirma_aprobado" class="errores">La contraseña no corresponde al usuario que aprueba el documento</div>
                        </td>   
                    </tr>
                    <tr>
                        <td class="descripcion">Observaciones :</td>
                        <td class="detalle" colspan="3">
                            <textarea id="observaciones_aprobar_documento" rows="2" style="width:100%;padding:5px;" placeholder="Ingrese las observaciones. Sea lo más específico posible" title="Ingrese las observaciones. Sea lo más específico posible" onblur="trim('observaciones_aprobar_documento')" ></textarea>
                            <div id="observaciones_aprobar_documento_null" class="errores">El mensaje de observaciones es obligatorio</div>
                            <div id="observaciones_aprobar_documento_min" class="errores">El mensaje de observaciones no puede ser menor a 6 caracteres (numeros o letras) </div>
                            <div id="observaciones_aprobar_documento_max" class="errores">El nombre del expediente no puede ser mayor a 200 caracteres. (Actualmente <b><u id='observaciones_aprobar_documento_contadormax'></u></b> caracteres)</div>		
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" id="fila_boton_solicitar_documento">
                            <center>
                            	<input type="button" value="Aprobar Documento" class="botones" onclick="validar_aprueba_firma()">
                            <center>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<!-- Hasta aqui el div que contiene ventana modal para solicitar prestamo -->
</body>
</html>