<?php
	require_once("../login/validar_inactividad.php");
 ?> 
<!DOCTYPE html>
<html>
<head>
	<!-- <link rel="stylesheet" href="include/css/estilos_bandejas.css"> -->
<!-- ************************************************************************************* -->
<!-- Inicio js cuadro_clasificacion_documental -->
<script type="text/javascript">
var timerid="";
/* Inicio script para buscador input formularios cuadro_clasificacion_documental */
$(function buscar_input_cuadro_clasificacion_documental(){
	$('#codigo_serie').on("input",function(e){ // Accion que se activa cuando se digita #codigo_serie
		$(".errores").slideUp("slow")
		loading('resultado_codigo_serie');

		var cod_serie = $(this).val();

		if($(this).data("lastval")!= cod_serie){
	    	$(this).data("lastval",cod_serie);                   
   			clearTimeout(timerid);

   			timerid = setTimeout(function() {
				validar_input('codigo_serie');
				$.ajax({
			        type: 'POST',
			        url: 'include/procesar_ajax.php',
			        data: {
			            'recibe_ajax' : 'buscar_codigo_serie',
			            'codigo_serie' : cod_serie
			        },          
			        success: function(resp1){
			        	// console.log(resp1);
		                $("#resultado_codigo_serie").html(resp1);
			        }
			    })	 			 
   			},1000);
	    };
	})

	$('#codigo_subserie').on("input",function(e){ // Accion que se activa cuando se digita #codigo_subserie
		$(".errores").slideUp("slow")
		loading('resultado_codigo_subserie');

		var codigo_dependencia 	= $("#codigo_dependencia").val();	 
		var cod_serie 			= $("#codigo_serie_sb").val();	 
		var cod_subserie 		= $(this).val();

		if($(this).data("lastval")!= cod_subserie){
	    	$(this).data("lastval",cod_subserie);                   
   			clearTimeout(timerid);

   			timerid = setTimeout(function() {
				validar_input('codigo_subserie');

				$.ajax({
			        type: 'POST',
			        url: 'include/procesar_ajax.php',
			        data: {
			            'recibe_ajax' 		: 'buscar_codigo_subserie',
			            'codigo_dependencia': codigo_dependencia,
			            'codigo_serie' 		: cod_serie,
			            'codigo_subserie' 	: cod_subserie
			        },          
			        success: function(resp1){
			        	// console.log(resp1);
			            $("#resultado_codigo_subserie").html(resp1);
			        }
			    })	 			 
   			},1000);
	    };
	})

	$('#codigo_subserie_mod').on("input",function(e){ // Accion que se activa cuando se digita #codigo_subserie_mod
		$(".errores").slideUp("slow")
		loading('resultado_codigo_subserie_mod');

		var codigo_dependencia 	= $("#codigo_dependencia_mod").val();	 
		var cod_serie 			= $("#codigo_serie_sb_mod").val();	 
		var cod_subserie 		= $(this).val();
		var cod_subserie_old 	= $("#codigo_subserie_mod_old").val();

		if($(this).data("lastval")!= cod_subserie){
	    	$(this).data("lastval",cod_subserie);                   
   			clearTimeout(timerid);

   			timerid = setTimeout(function() {
				validar_input('codigo_subserie_mod');
				$.ajax({
			        type: 'POST',
			        url: 'include/procesar_ajax.php',
			        data: {
			            'recibe_ajax' 			: 'buscar_codigo_subserie',
			            'codigo_dependencia' 	: codigo_dependencia,
			            'codigo_serie' 			: cod_serie,
			            'codigo_subserie' 		: cod_subserie,
			            'codigo_subserie_old' 	: cod_subserie_old,
			            'modificar' 			: 'SI'
			        },          
			        success: function(resp1){
			        	// console.log(resp1);
		                $("#resultado_codigo_subserie_mod").html(resp1);
			        }
			    })	 			 
   			},1000);
	    };
	})

	$('#nombre_mod_serie').on("input",function(e){ // Accion que se activa cuando se digita #nombre_mod_serie
		$(".errores").slideUp("slow")
		loading('resultado_nombre_mod_serie');

		var cod_serie = $("#codigo_mod_serie").val();	 
		var nom_serie = $(this).val();

		if($(this).data("lastval")!= nom_serie){
	    	$(this).data("lastval",nom_serie);                   
   			clearTimeout(timerid);

   			timerid = setTimeout(function() {
				validar_input('nombre_mod_serie');
				$.ajax({
			        type: 'POST',
			        url: 'include/procesar_ajax.php',
			        data: {
			            'recibe_ajax' 	: 'buscar_nombre_serie',
			            'codigo_serie'	: cod_serie,
			            'nombre_serie' 	: nom_serie,
			            'modificar' 	: 'SI'
			        },          
			        success: function(resp1){
			        	// console.log(resp1);
		                $("#resultado_nombre_mod_serie").html(resp1);
			        }
			    })	 			 
   			},1000);
	    };
	})

	$('#nombre_serie').on("input",function(e){ // Accion que se activa cuando se digita #nombre_serie
		$(".errores").slideUp("slow")
		loading('resultado_nombre_serie');

		var nom_serie = $(this).val();

		if($(this).data("lastval")!= nom_serie){
	    	$(this).data("lastval",nom_serie);                   
   			clearTimeout(timerid);

   			timerid = setTimeout(function() {
				validar_input('nombre_serie');
				$.ajax({
			        type: 'POST',
			        url: 'include/procesar_ajax.php',
			        data: {
			            'recibe_ajax' : 'buscar_nombre_serie',
			            'nombre_serie' : nom_serie
			        },          
			        success: function(resp1){
			        	// console.log(resp1);
		                $("#resultado_nombre_serie").html(resp1);
			        }
			    })	 			 
   			},1000);
	    };
	})

	$('#nombre_subserie').on("input",function(e){ // Accion que se activa cuando se digita #nombre_subserie
		$(".errores").slideUp("slow")
		loading('resultado_nombre_subserie');

		var codigo_dependencia 	= $("#codigo_dependencia").val();
		var codigo_serie 		= $("#codigo_serie_sb").val();
		var nombre_subserie 	= $(this).val();

		if($(this).data("lastval")!= nombre_subserie){
	    	$(this).data("lastval",nombre_subserie);                   
   			clearTimeout(timerid);

   			timerid = setTimeout(function() {
				validar_input('nombre_subserie');
				$.ajax({
			        type: 'POST',
			        url: 'include/procesar_ajax.php',
			        data: {
			            'recibe_ajax' 		: 'buscar_nombre_subserie',
			            'codigo_dependencia': codigo_dependencia,
			            'codigo_serie' 		: codigo_serie,
			            'nombre_subserie' 	: nombre_subserie
			        },          
			        success: function(resp1){
			        	// console.log(resp1);
		                $("#resultado_nombre_subserie").html(resp1);
			        }
			    })	 			 
   			},1000);
	    };
	})

	$('#nombre_subserie_mod').on("input",function(e){ // Accion que se activa cuando se digita #nombre_subserie_mod
		$(".errores").slideUp("slow")
		loading('resultado_nombre_subserie_mod');

		var codigo_dependencia 	= $("#codigo_dependencia_mod").val();
		var cod_serie 			= $("#codigo_serie_sb_mod").val();	 
		
		var nombre_subserie 	= $(this).val();
		var nombre_subserie_old = $("#nombre_subserie_mod_old").val();

		if($(this).data("lastval")!= nombre_subserie){
	    	$(this).data("lastval",nombre_subserie);                   
   			clearTimeout(timerid);

   			timerid = setTimeout(function() {
				validar_input('nombre_subserie_mod');
				$.ajax({
			        type: 'POST',
			        url: 'include/procesar_ajax.php',
			        data: {
			            'recibe_ajax' 			: 'buscar_nombre_subserie',
			            'codigo_dependencia'	: codigo_dependencia,
			            'codigo_serie'			: cod_serie,
			            'nombre_subserie' 		: nombre_subserie,
			            'nombre_subserie_old' 	: nombre_subserie_old,
			            'modificar' 			: 'SI'
			        },          
			        success: function(resp1){
			        	// console.log(resp1);
		                $("#resultado_nombre_subserie_mod").html(resp1);
			        }
			    })	 			 
   			},1000);
	    };
	})
})
/* Fin script para buscador input formularios cuadro_clasificacion_documental */

function abrir_pestana_ccd(evento,value){
  	var tablinks;
  
  	tablinks = document.getElementsByClassName("tablinks");
  	for (i = 0; i < tablinks.length; i++) {
  		tablinks[i].className = tablinks[i].className.replace(" active", "");
  	}
  	
  	evento.currentTarget.className += " active";

  	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'pestana' 		: value,
            'recibe_ajax' 	: 'pestana_cuadro_clasificacion'
        },          
        success: function(resp1){
            if(resp1!=""){
                $("#pestana_cuadro_clasificacion").html(resp1);
            }
        }
    })
    cargar_input_codigo_dependencia(); 	// Cargar codigo de dependencia al cargar la pestaña
	cargar_input_codigo_serie_sb();		// Cargar codigo de serie al cargar la pestaña
}


function cargar_error_input_codigo_subserie(){
	$("#codigo_subserie_error").slideDown("slow");
	$("#codigo_subserie_mod_error").slideDown("slow");
}

function cargar_error_input_nombre_serie(){
	$("#nombre_mod_serie_error").slideDown("slow");
}

function cargar_error_input_nombre_subserie(){
	$("#nombre_subserie_error").slideDown("slow");
	$("#nombre_subserie_mod_error").slideDown("slow");
}
		
function cargar_input_codigo_dependencia(){
	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax' 	: 'listado_dependencias'
        },          
        success: function(respuesta){
            if(respuesta!=""){
                $("#codigo_dependencia").html(respuesta);
                $("#codigo_dependencia_mod").html(respuesta);
            }
        }
    })
}

function cargar_input_codigo_serie_sb(){
	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax' 	: 'listado_series'
        },          
        success: function(respuesta){
            if(respuesta!=""){
            	// console.log(respuesta)
                $("#codigo_serie_sb").html(respuesta);
                $("#codigo_serie_sb_mod").html(respuesta);
            }
        }
    })
}

function cargar_input_codigo_subserie(codigo_subserie,nombre_subserie){

	$("#codigo_subserie").val(codigo_subserie);
	$("#codigo_subserie_mod").val(codigo_subserie);
	$("#nombre_subserie").val(nombre_subserie);
	$("#nombre_subserie_mod").val(nombre_subserie);

	$("#codigo_subserie_error").slideUp("slow");
	$("#codigo_subserie_max").slideUp("slow");
	$("#codigo_subserie_min").slideUp("slow");
	$("#codigo_subserie_null").slideUp("slow");
	
	$("#codigo_subserie_mod_error").slideUp("slow");
	$("#nombre_subserie_error").slideUp("slow");
	$("#nombre_subserie_mod_error").slideUp("slow");

	$("#codigo_subserie_mod_max").slideUp("slow");
	$("#nombre_subserie_max").slideUp("slow");
	$("#nombre_subserie_mod_max").slideUp("slow");

	$("#codigo_subserie_mod_min").slideUp("slow");
	$("#nombre_subserie_min").slideUp("slow");
	$("#nombre_subserie_mod_min").slideUp("slow");

	$("#codigo_subserie_mod_null").slideUp("slow");
	$("#nombre_subserie_mod_null").slideUp("slow");
	$("#nombre_subserie_null").slideUp("slow");

	$("#resultado_codigo_subserie").html("");
	$("#resultado_codigo_subserie_mod").html("");
	$("#resultado_nombre_subserie").html("");
	$("#resultado_nombre_subserie_mod").html("");

}

function cargar_input_nombre_serie(nombre_serie){
	$("#nombre_mod_serie").val(nombre_serie);
	$("#nombre_mod_serie_error").slideUp("slow");
	$("#resultado_nombre_mod_serie").html("");
}

function cargar_nombre_serie_sb(valor){
	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax' 	: 'nombre_serie',
            'codigo_serie'	: valor
        },          
        success: function(respuesta){
        	if(respuesta==""){
        		$("#codigo_subserie").slideUp("slow");
        		$("#codigo_subserie_mod").slideUp("slow");
        		$("#nombre_subserie").slideUp("slow");
        		$("#nombre_subserie_mod").slideUp("slow");
        	}else{
        		$("#codigo_subserie").slideDown("slow");
        		$("#codigo_subserie_mod").slideDown("slow");
        		$("#nombre_subserie").slideDown("slow");
        		$("#nombre_subserie_mod").slideDown("slow");
        		// $("#codigo_subserie").focus();
        	}
    		$("#codigo_subserie").val("");
    		$("#codigo_subserie_mod").val("");
    		$("#nombre_subserie").val("");
    		$("#nombre_subserie_mod").val("");

    		// console.log("respuesta "+respuesta);
            $("#nombre_serie_sb").val(respuesta);
            $("#nombre_serie_sb_mod").val(respuesta);
            $(".art").slideUp("slow");
            $(".errores").slideUp("slow");
        }
    })
}

function cargar_nombre_serie_sb_dep(){ // Funcion que se activa (onchange) cuando cambia la dependencia
	var codigo_serie = $("#codigo_serie_sb").val();
	cargar_nombre_serie_sb(codigo_serie);

	var codigo_serie_mod = $("#codigo_serie_sb_mod").val();
	cargar_nombre_serie_sb(codigo_serie_mod);
}

function ccd_por_dependencia(codigo_dependencia){ 	// Consultar el Cuadro de Clasificación Documental por dependencia
  	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax' 			: 'ccd_por_dependencia', 
            'codigo_dependencia' 	: codigo_dependencia
        },          
        success: function(resp1){
            if(resp1!=""){
                $("#resultado_ccd_dependencia").html(resp1);
            }
        }
    })
}

function enviar_agregar_serie(){	// Funcion para enviar el formulario de agregar serie

	validar_input('codigo_serie'); 		// Funcion especificada en include/js/funciones_menu.js
	validar_input('nombre_serie');

	if($(".art").is(":visible")){
		$("#error_serie_existe").slideDown("slow");
		return false;
	}else{
		$("#error_serie_existe").slideUp("slow");
		if($(".errores").is(":visible")){
			return false;
		}else{
			loading('boton_agregar_serie');	// Funcion especificada en include/js/funciones_menu.js

			var codigo_serie = $("#codigo_serie").val();
			var nombre_serie = $("#nombre_serie").val();

		    $.ajax({
		        type: 'POST',
		        url: 'include/procesar_ajax.php',
		        data: {
		            'recibe_ajax' : 'crear_serie',
		            'codigo_serie' : codigo_serie, 
		            'nombre_serie' : nombre_serie
		        },          
		        success: function(respuesta){
		            if(respuesta!=""){
		            	// console.log(respuesta);
		                $("#resultado_js").html(respuesta);
		            }
		        }
		    })
		}
	}
}

function enviar_agregar_subserie(){	// Funcion para enviar el formulario de agregar subserie
	var codigo_nombre_serie=$("#codigo_serie_sb").val();

	if(codigo_nombre_serie==""){
		$("#error_nombre_codigo_serie").slideDown("slow");
		$("#codigo_serie_sb").focus();
		return false;
	}else{
		$("#error_nombre_codigo_serie").slideUp("slow");
	}
	validar_input('codigo_subserie'); 		// Funcion especificada en include/js/funciones_menu.js
	validar_input('nombre_subserie');
	validar_input('tiempo_archivo_gestion');
	validar_input('tiempo_archivo_central');
	validar_input('procedimiento');

	if($(".errores").is(":visible") || $(".art").is(":visible")){
		return false;
	}else{
		 loading('boton_agregar_subserie');	// Funcion especificada en include/js/funciones_menu.js

		 var codigo_dependencia 			= $("#codigo_dependencia").val();
		 var nombre_serie 					= $("#nombre_serie_sb").val();
		 var codigo_subserie 				= $("#codigo_subserie").val();
		 var nombre_subserie 				= $("#nombre_subserie").val();
		 var tiempo_archivo_gestion 		= $("#tiempo_archivo_gestion").val();
		 var tiempo_archivo_central 		= $("#tiempo_archivo_central").val();
		 var soporte_papel 					= $("#soporte_papel").val();
		 var soporte_electronico 			= $("#soporte_electronico").val();
		 var eliminacion 					= $("#eliminacion").val();
		 var seleccion 						= $("#seleccion").val();
		 var conservacion_total 			= $("#conservacion_total").val();
		 var microfilmacion_digitalizacion 	= $("#microfilmacion_digitalizacion").val();
		 var procedimiento 					= $("#procedimiento").val();

	    $.ajax({
	        type: 'POST',
	        url: 'include/procesar_ajax.php',
	        data: {
	            'recibe_ajax' 					: 'crear_subserie',
	            'codigo_dependencia' 			: codigo_dependencia, 
	            'codigo_serie' 					: codigo_nombre_serie, 
	            'nombre_serie' 					: nombre_serie,
	            'codigo_subserie' 				: codigo_subserie,
	            'nombre_subserie' 				: nombre_subserie,
	            'tiempo_archivo_gestion' 		: tiempo_archivo_gestion,
	            'tiempo_archivo_central' 		: tiempo_archivo_central,
	            'soporte_papel' 				: soporte_papel,
	            'soporte_electronico' 			: soporte_electronico,
	            'eliminacion' 					: eliminacion,
	            'seleccion' 					: seleccion,
	            'conservacion_total' 			: conservacion_total,
	            'microfilmacion_digitalizacion' : microfilmacion_digitalizacion,
	            'procedimiento' 				: procedimiento
	        },          
	        success: function(respuesta){
	            if(respuesta!=""){
	            	// console.log(respuesta);
	                $("#resultado_js").html(respuesta);
	            }
	        }
	    })
	}
}

function enviar_modificar_serie(){		// Funcion para enviar el formulario de modificar serie
	validar_input('nombre_mod_serie');	// Funcion especificada en include/js/funciones_menu.js
	
	if($(".errores").is(":visible") || $(".art").is(":visible")){
		return false;
	}else{
		loading('boton_modificar_serie');	// Funcion especificada en include/js/funciones_menu.js

		var codigo_serie = $("#codigo_mod_serie").val();
		var nombre_serie = $("#nombre_mod_serie").val();
		var serie_activa = $("#serie_activa").val();

	    $.ajax({
	        type: 'POST',
	        url: 'include/procesar_ajax.php',
	        data: {
	            'recibe_ajax' : 'modificar_serie',
	            'codigo_serie' : codigo_serie, 
	            'nombre_serie' : nombre_serie,
	            'serie_activa' : serie_activa
	        },          
	        success: function(respuesta){
	            if(respuesta!=""){
	            	// console.log(respuesta);
	                $("#resultado_js").html(respuesta);
	            }
	        }
	    })
	}
}

function enviar_modificar_subserie(){		// Funcion para enviar el formulario de modificar subserie
	var codigo_serie = $("#codigo_serie_sb_mod").val();

	if(codigo_serie==""){
		$("#error_nombre_codigo_serie_mod").slideDown("slow");
		$("#codigo_serie_sb_mod").focus();
		return false;
	}else{
		$("#error_nombre_codigo_serie_mod").slideUp("slow");
	}

	validar_input('codigo_subserie_mod'); 		// Funcion especificada en include/js/funciones_menu.js
	validar_input('nombre_subserie_mod');
	validar_input('tiempo_archivo_gestion_mod');
	validar_input('tiempo_archivo_central_mod');
	validar_input('procedimiento_mod');


	if($(".errores").is(":visible") || $(".art").is(":visible")){
		return false;
	}else{
		// loading('boton_modificar_subserie');	// Funcion especificada en include/js/funciones_menu.js

		var id 								= $("#id_subserie").val();
		var codigo_dependencia 				= $("#codigo_dependencia_mod").val();
		var nombre_serie 					= $("#nombre_serie_sb_mod").val();
		var codigo_subserie 				= $("#codigo_subserie_mod").val();
		var nombre_subserie 				= $("#nombre_subserie_mod").val();
		var tiempo_archivo_gestion 			= $("#tiempo_archivo_gestion_mod").val();
		var tiempo_archivo_central 			= $("#tiempo_archivo_central_mod").val();
		var soporte_papel 					= $("#soporte_papel_mod").val();
		var soporte_electronico				= $("#soporte_electronico_mod").val();
		var eliminacion						= $("#eliminacion_mod").val();
		var seleccion						= $("#seleccion_mod").val();
		var conservacion_total				= $("#conservacion_total_mod").val();
		var conservacion_total				= $("#conservacion_total_mod").val();
		var microfilmacion_digitalizacion	= $("#microfilmacion_digitalizacion_mod").val();
		var procedimiento					= $("#procedimiento_mod").val();
		var activo							= $("#activo_mod").val();

	    $.ajax({
	        type: 'POST',
	        url: 'include/procesar_ajax.php',
	        data: {
	            'recibe_ajax' 					: 'modificar_subserie',
	    		'id' 							: id,
				'codigo_dependencia' 			: codigo_dependencia,
				'codigo_serie' 					: codigo_serie,
				'nombre_serie' 					: nombre_serie,
				'codigo_subserie' 				: codigo_subserie,
				'nombre_subserie' 				: nombre_subserie,
				'tiempo_archivo_gestion' 		: tiempo_archivo_gestion,
				'tiempo_archivo_central' 		: tiempo_archivo_central,
				'soporte_papel' 				: soporte_papel,
				'soporte_electronico' 			: soporte_electronico,
				'eliminacion' 					: eliminacion,
				'seleccion' 					: seleccion,
				'conservacion_total' 			: conservacion_total,
				'microfilmacion_digitalizacion' : microfilmacion_digitalizacion,
				'procedimiento' 				: procedimiento,
				'activo' 						: activo	   
	        },          
	        success: function(respuesta){
	            if(respuesta!=""){
	            	// console.log(respuesta);
	                $("#resultado_js").html(respuesta);
	            }
	        }
	    })
	}
}

function modificar_serie(codigo_serie,nombre_serie,activo){
	$("#ventana_modificar_serie").slideDown("slow");
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 

	$("#codigo_mod_serie").val(codigo_serie);
	$("#codigo_mod_serie_mostrar").val(codigo_serie);
	$("#nombre_mod_serie").val(nombre_serie);
	$('#nombre_mod_serie').focus();
	$("#serie_activa").val(activo);
}

function modificar_subserie(id,codigo_dependencia,codigo_serie,nombre_serie,codigo_subserie,nombre_subserie,tiempo_archivo_gestion,tiempo_archivo_central,soporte_papel,soporte_electronico,eliminacion,seleccion,conservacion_total,microfilmacion_digitalizacion,procedimiento,activo){

	$("#ventana_mod_subserie").slideDown("slow");
	$("#id_subserie").val(id);
	$("#codigo_dependencia_mod").val(codigo_dependencia);
	$("#codigo_serie_sb_mod").val(codigo_serie);
	$("#nombre_serie_sb_mod").val(nombre_serie);
	$("#codigo_subserie_mod").val(codigo_subserie);
	$("#codigo_subserie_mod_old").val(codigo_subserie);
	$("#nombre_subserie_mod").val(nombre_subserie);
	$("#nombre_subserie_mod_old").val(nombre_subserie);
	$("#tiempo_archivo_gestion_mod").val(tiempo_archivo_gestion);
	$("#tiempo_archivo_central_mod").val(tiempo_archivo_central);
	$("#soporte_papel_mod").val(soporte_papel);
	$("#soporte_electronico_mod").val(soporte_electronico);
	$("#eliminacion_mod").val(eliminacion);
	$("#seleccion_mod").val(seleccion);
	$("#conservacion_total_mod").val(conservacion_total);
	$("#microfilmacion_digitalizacion_mod").val(microfilmacion_digitalizacion);
	$("#procedimiento_mod").val(procedimiento);
	$("#activo_mod").val(activo);

	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function mostrar_ventana_crear_serie() {
	$("#ventana_serie").slideDown("slow");
	$('#codigo_serie').focus();

	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function mostrar_ventana_crear_subserie() {
	$("#ventana_subserie").slideDown("slow");
	
	$('#codigo_dependencia_mod').focus();
	$("#codigo_subserie").slideUp("slow");
	$("#nombre_subserie").slideUp("slow");

	$("#boton_agregar_subserie").html('<input type="button" value="Agregar Subserie" class="botones" onclick="enviar_agregar_subserie()">');

	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
</script>
<!-- Fin js cuadro_clasificacion_documental -->
<!-- ************************************************************************************* -->
<!-- Inicio Estilos cuadro_clasificacion_documental -->
<style>

/* Estilo de la pestaña */
#ccd{
	width: 100%;
}
.fila_serie:hover {
	background-color: #ccc;
}
.tab {
  	overflow: hidden;
}

/* Estilo de cada boton en la pestaña */
.tab button {
	background-color 	: #666;
  	border 				: none;
  	color 				: #FFFFFF;
  	cursor 				: pointer;
  	float 				: left;
  	font-size 			: 17px;
  	outline 			: none;
  	padding 			: 14px 16px;
  	transition 			: 0.3s;
}

/* Cambiar background color de los botones al hover */
.tab button:hover {
  	background-color 	: #ddd;
  	color 				: #FFFFFF;
}

/* Crear una clase del boton activo o inactivo */
.tab button.active {
 	background-color 	: #ccc;
  	color 				: #666;
}
</style>
<!-- Fin Estilos cuadro_clasificacion_documental -->
<!-- ************************************************************************************* -->
</head>
<body>

<div class="center">
	<h1>Cuadro de Clasificación Documental</h1>
</div>

<div class="tab"> <!-- Pestañas que se muestran en el cuadro de clasificacion documental -->
  	<button class="tablinks" onclick="abrir_pestana_ccd(event,'trd')">Por dependencia</button>
  	<button class="tablinks" onclick="abrir_pestana_ccd(event,'serie')">Series</button>
  	<button class="tablinks" onclick="abrir_pestana_ccd(event,'subserie')">Subseries</button>
</div>

<div id="pestana_cuadro_clasificacion"></div>


<!-- Ventanas modales -->
<div id="ventana_serie" class="ventana_modal form">
	<div class="form">
		<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
		<h1>Formulario Agregar Serie</h1>
		<hr>
		<form autocomplete="off">
			<table>
				<tr>
                    <td class="descripcion" width="50%">Codigo serie :</td>
                    <td class="detalle" colspan="3">
                        <input id="codigo_serie" type="search" placeholder="Ingrese el codigo de la serie" title="Ingrese el codigo de la serie." onblur="validar_input('codigo_serie')" > 

                        <div id="resultado_codigo_serie"></div>
                        <div id="codigo_serie_null" class="errores">El código de la serie es obligatorio</div>
                        <div id="codigo_serie_min" class="errores">El código de la serie no puede ser menor a 3 caracteres (numeros o letras) </div>
                        <div id="codigo_serie_max" class="errores">El codigo de la serie no puede ser mayor a 3 caracteres (numeros o letras)</div>
                        <div id="error_serie_existe" class="errores">El código de la serie ya existe. Revise nuevamente por favor.</div>
                    </td>
                </tr>
				<tr>
	                <td class="descripcion">Nombre serie :</td>
	                <td class="detalle" colspan="3">
	                    <input id="nombre_serie" type="search" placeholder="Ingrese el nombre de la serie" title="Ingrese el nombre de la serie" onblur="validar_input('nombre_serie');trim('nombre_serie')"> 

	                    <div id="resultado_nombre_serie"></div>
	                    <div id="nombre_serie_null" class="errores">El nombre de la serie es obligatorio</div>
	                    <div id="nombre_serie_min" class="errores">El nombre de la serie no puede ser menor a 3 caracteres (numeros o letras).</div>
	                    <div id="nombre_serie_max" class="errores">El nombre de la serie no puede ser mayor a 150 caracteres (numeros o letras). (Actualmente <b><u id='nombre_serie_contadormax'></u></b> caracteres)</div>
	                </td>
	            </tr>
	            <tr>
					<td colspan="2" class="center">
						<div id="resultado_prestamo_documento"></div>
						<div id="boton_agregar_serie">
							<input type="button" value="Agregar Serie" class="botones" onclick="enviar_agregar_serie()">
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

<div id="ventana_modificar_serie" class="ventana_modal form">
	<div class="form">
		<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
		<h1>Formulario Modificar Serie</h1>
		<hr>
		<form autocomplete="off">
			<table>
				<tr>
                    <td class="descripcion">Codigo serie :</td>
                    <td class="detalle" colspan="3">
                    	<input type="hidden" id='codigo_mod_serie'>
                        <input id="codigo_mod_serie_mostrar" type="search" title="Código de la serie." disabled>
                    </td>
                </tr>
				<tr>
	                <td class="descripcion" width="50%">Nombre serie :</td>
	                <td class="detalle" colspan="3">
	                    <input id="nombre_mod_serie" type="search" placeholder="Ingrese el nombre de la serie" title="Ingrese el nombre de la serie" onblur="validar_input('nombre_mod_serie');"> 

	                    <div id="resultado_nombre_mod_serie"></div>
	                    <div id="nombre_mod_serie_null" class="errores">El nombre de la serie es obligatorio</div>
	                    <div id="nombre_mod_serie_min" class="errores">El nombre de la serie no puede ser menor a 3 caracteres (numeros o letras).</div>
	                    <div id="nombre_mod_serie_max" class="errores">El nombre de la serie no puede ser mayor a 150 caracteres (numeros o letras). (Actualmente <b><u id='nombre_mod_serie_contadormax'></u></b> caracteres)</div>
	                    <div id="nombre_mod_serie_error" class="errores">El nombre de la serie ya existe. Por favor revisar</div>
	                </td>
	            </tr>
	            <tr>
	                <td class="descripcion">Serie Activa :</td>
	            	<td class="detalle" colspan="3">
	            		<select id="serie_activa" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>			
	            	</td>
	            </tr>
	            <tr>
					<td colspan="2" class="center">
						<div id="resultado_prestamo_documento"></div>
						<div id="boton_modificar_serie">
							<input type="button" value="Modificar Serie" class="botones" onclick="enviar_modificar_serie()">
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

<div id="ventana_subserie" class="ventana_modal form">
	<div class="form" style="overflow: scroll; max-height: 80vh; max-width: 75vw;">
		<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
		<h1>Formulario Agregar Subserie</h1>
		<hr>
		<form autocomplete="off">
			<table>
				<tr>
                    <td class="descripcion" width="25%" colspan="2">Codigo Dependencia :</td>
                    <td class="detalle" width="25%" colspan="2">
                    	<select id="codigo_dependencia" class='select_opciones' onchange="cargar_nombre_serie_sb_dep()">
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
	                <td class="descripcion" width="25%" colspan="2">Codigo - Nombre serie :</td>
	                <td class="detalle" width="25%" colspan="2">
	                   <select id="codigo_serie_sb" class='select_opciones' onchange="cargar_nombre_serie_sb(this.value)"></select>
						<input type="hidden" id="nombre_serie_sb">
						<div id="error_nombre_codigo_serie_sb" class="errores">Debe seleccionar por lo menos un código - nombre de serie</div>
	                </td>
	            </tr>
	            <tr>
	                <td class="descripcion" colspan="2">Codigo Subserie :</td>
	                <td class="detalle" colspan="2">
	                    <input id="codigo_subserie" type="search" placeholder="Ingrese el codigo de la subserie" title="Ingrese el codigo de la subserie" onblur="validar_input('codigo_subserie')"> 

	                    <div id="resultado_codigo_subserie"></div>

	                    <div id="codigo_subserie_error" class="errores">El codigo de la subserie ya existe en ésta dependencia. Por favor revisar</div>
	                    <div id="codigo_subserie_max" class="errores">El codigo de la subserie no puede ser mayor a 3 caracteres (numeros o letras)</div>
	                    <div id="codigo_subserie_min" class="errores">El codigo de la subserie no puede ser menor a 3 caracteres (numeros o letras).</div>
	                    <div id="codigo_subserie_null" class="errores">El codigo de la subserie es obligatorio</div>
	                </td>
	                <td class="descripcion" colspan="2">Nombre Subserie :</td>
	                <td class="detalle" colspan="2">
	                    <input id="nombre_subserie" type="search" placeholder="Ingrese el nombre de la subserie" title="Ingrese el nombre de la subserie" onblur="validar_input('nombre_subserie');trim('nombre_subserie')"> 

	                    <div id="resultado_nombre_subserie"></div>
	                    
	                   	<div id="nombre_subserie_error" class="errores">El nombre de la subserie ya existe en esta dependencia. Por favor revisar</div>
	                    <div id="nombre_subserie_max" class="errores">El nombre de la subserie no puede ser mayor a 150 caracteres (numeros o letras). (Actualmente <b><u id='nombre_subserie_contadormax'></u></b> caracteres)</div>
	                    <div id="nombre_subserie_min" class="errores">El nombre de la subserie no puede ser menor a 3 caracteres (numeros o letras).</div>
	                    <div id="nombre_subserie_null" class="errores">El nombre de la subserie es obligatorio</div>
	                </td>
	            </tr>
	            <tr>
	            	<td class="descripcion">Tiempo archivo Gestión (Años)</td>
	            	<td class="detalle">
	            		<input id="tiempo_archivo_gestion" type="search" placeholder="Ingrese el tiempo de la subserie en el archivo de gestión" title="Ingrese el tiempo de la subserie en el archivo de gestión" onblur="validar_input('tiempo_archivo_gestion')" onkeyup="validar_input_delay('tiempo_archivo_gestion')">

	            		<div id="error_tiempo_archivo_gestion" class="errores">El tiempo en archivo de gestión (en años) debe ser numérico</div>
	                    <div id="tiempo_archivo_gestion_max" class="errores">El tiempo en archivo de gestión no puede ser mayor a 3 caracteres (numeros unicamente)</div>
	                    <div id="tiempo_archivo_gestion_null" class="errores">El tiempo en archivo de gestión es obligatorio</div>
	            	</td>
	            	<td class="descripcion">Tiempo archivo Central (Años)</td>
	            	<td class="detalle">
	            		<input id="tiempo_archivo_central" type="search" placeholder="Ingrese el tiempo de la subserie en el archivo central" title="Ingrese el tiempo de la subserie en el archivo central" onblur="validar_input('tiempo_archivo_central')" onkeyup="validar_input_delay('tiempo_archivo_central')">

	            		<div id="error_tiempo_archivo_central" class="errores">El tiempo en archivo central (en años) debe ser numérico</div>
	                    <div id="tiempo_archivo_central_max" class="errores">El tiempo en archivo de gestión no puede ser mayor a 3 caracteres (numeros unicamente)</div>
	                    <div id="tiempo_archivo_central_null" class="errores">El tiempo en archivo central es obligatorio</div>
	            	</td>
	            	<td class="descripcion">Soporte Físico en Papel :</td>
                    <td class="detalle">
                    	<select id="soporte_papel" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
                    <td class="descripcion">Soporte Electrónico :</td>
                    <td class="detalle">
                    	<select id="soporte_electronico" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
	            </tr>
	            <tr>
	            	<td class="descripcion">Eliminacion :</td>
                    <td class="detalle">
                    	<select id="eliminacion" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
                    <td class="descripcion">Seleccion :</td>
                    <td class="detalle">
                    	<select id="seleccion" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
	            	<td class="descripcion">Conservacion Total :</td>
                    <td class="detalle">
                    	<select id="conservacion_total" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
                    <td class="descripcion">Microfilmación y/o Digitalización :</td>
                    <td class="detalle">
                    	<select id="microfilmacion_digitalizacion" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
	            </tr>
	            <tr>
	            	<td class="descripcion">Procedimiento</td>
	            	<td colspan="7">
	            		<textarea id="procedimiento" cols="110" rows="5" onkeyup="validar_input_delay('procedimiento')" onblur="validar_input('procedimiento')"></textarea>

	                    <div id="procedimiento_max" class="errores">El procedimiento no puede ser mayor a 500 caracteres (numeros o letras). (Actualmente <b><u id='procedimiento_contadormax'></u></b> caracteres)</div>
	            		<div id="procedimiento_min" class="errores">El procedimiento no puede ser menor a 6 caracteres (numeros o letras).</div>
	            		<div id="procedimiento_null" class="errores">El procedimiento es obligatorio</div>
	            	</td>
	            </tr>
	            <tr>
					<td colspan="8" class="center">
						<div id="resultado_prestamo_documento"></div>
						<div id="boton_agregar_subserie">
							<input type="button" value="Agregar Subserie" class="botones" onclick="enviar_agregar_subserie()">
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

<div id="ventana_mod_subserie" class="ventana_modal form">
	<div class="form" style="overflow: scroll; max-height: 80vh; max-width: 75vw;">
		<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
		<h1>Formulario Modificar Subserie</h1>
		<hr>
		<form autocomplete="off">
			<table>
				<tr>
                    <td class="descripcion" width="25%" colspan="2">Codigo Dependencia :</td>
                    <td class="detalle" width="25%" colspan="2">
                    	<input type="hidden" id="id_subserie">
                    	<select id="codigo_dependencia_mod" class='select_opciones' onchange="cargar_nombre_serie_sb_dep()"> </select>		
                    </td>
	                <td class="descripcion" width="25%" colspan="2">Codigo - Nombre serie :</td>
	                <td class="detalle" width="25%" colspan="2">
	                   <select id="codigo_serie_sb_mod" class='select_opciones' onchange="cargar_nombre_serie_sb(this.value)"></select>
						<input type="hidden" id="nombre_serie_sb_mod">
						<div id="error_nombre_codigo_serie_mod" class="errores">Debe seleccionar por lo menos un código - nombre de serie</div>
	                </td>
	            </tr>
	            <tr>
	                <td class="descripcion" colspan="2">Codigo Subserie :</td>
	                <td class="detalle" colspan="2">
	                	<input type="hidden" id="codigo_subserie_mod_old">
	                    <input id="codigo_subserie_mod" type="search" placeholder="Ingrese el codigo de la subserie" title="Ingrese el codigo de la subserie" onblur="validar_input('codigo_subserie_mod')"> 

	                    <div id="resultado_codigo_subserie_mod"></div>

	                    <div id="codigo_subserie_mod_error" class="errores">El codigo de la subserie ya existe en ésta dependencia. Por favor revisar</div>
	                    <div id="codigo_subserie_mod_max" class="errores">El codigo de la subserie no puede ser mayor a 3 caracteres (numeros o letras)</div>
	                    <div id="codigo_subserie_mod_min" class="errores">El codigo de la subserie no puede ser menor a 3 caracteres (numeros o letras).</div>
	                    <div id="codigo_subserie_mod_null" class="errores">El codigo de la subserie es obligatorio</div>
	                </td>
	                <td class="descripcion" colspan="2">Nombre Subserie :</td>
	                <td class="detalle" colspan="2">
	                	<input type="hidden" id="nombre_subserie_mod_old">
	                    <input id="nombre_subserie_mod" type="search" placeholder="Ingrese el nombre de la subserie" title="Ingrese el nombre de la subserie" onblur="validar_input('nombre_subserie_mod');trim('nombre_subserie_mod')"> 

	                    <div id="resultado_nombre_subserie_mod"></div>

	                    <div id="nombre_subserie_mod_error" class="errores">El nombre de la subserie ya existe en esta dependencia. Por favor revisar</div>
	                    <div id="nombre_subserie_mod_max" class="errores">El nombre de la subserie no puede ser mayor a 100 caracteres (numeros o letras). (Actualmente <b><u id='nombre_subserie_mod_contadormax'></u></b> caracteres)</div>
	                    <div id="nombre_subserie_mod_min" class="errores">El nombre de la subserie no puede ser menor a 3 caracteres (numeros o letras).</div>
	                    <div id="nombre_subserie_mod_null" class="errores">El nombre de la subserie es obligatorio</div>
	                </td>
	            </tr>
	            <tr>
	            	<td class="descripcion">Tiempo archivo Gestión (Años)</td>
	            	<td class="detalle">
	            		<input id="tiempo_archivo_gestion_mod" type="search" placeholder="Ingrese el tiempo de la subserie en el archivo de gestión" title="Ingrese el tiempo de la subserie en el archivo de gestión" onblur="validar_input('tiempo_archivo_gestion_mod')" onkeyup="validar_input_delay('tiempo_archivo_gestion_mod')">

	            		<div id="error_tiempo_archivo_gestion_mod" class="errores">El tiempo en archivo de gestión (en años) debe ser numérico</div>
	                    <div id="tiempo_archivo_gestion_mod_max" class="errores">El tiempo en archivo de gestión no puede ser mayor a 3 caracteres (numeros unicamente)</div>
	                    <div id="tiempo_archivo_gestion_mod_null" class="errores">El tiempo en archivo de gestión es obligatorio</div>
	            	</td>
	            	<td class="descripcion">Tiempo archivo Central (Años)</td>
	            	<td class="detalle">
	            		<input id="tiempo_archivo_central_mod" type="search" placeholder="Ingrese el tiempo de la subserie en el archivo central" title="Ingrese el tiempo de la subserie en el archivo central" onblur="validar_input('tiempo_archivo_central_mod')" onkeyup="validar_input_delay('tiempo_archivo_central_mod')">

	            		<div id="error_tiempo_archivo_central_mod" class="errores">El tiempo en archivo central (en años) debe ser numérico</div>
	                    <div id="tiempo_archivo_central_mod_max" class="errores">El tiempo en archivo de gestión no puede ser mayor a 3 caracteres (numeros unicamente)</div>
	                    <div id="tiempo_archivo_central_mod_null" class="errores">El tiempo en archivo central es obligatorio</div>
	            	</td>
	            	<td class="descripcion">Soporte Físico en Papel :</td>
                    <td class="detalle">
                    	<select id="soporte_papel_mod" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
                    <td class="descripcion">Soporte Electrónico :</td>
                    <td class="detalle">
                    	<select id="soporte_electronico_mod" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
	            </tr>
	            <tr>
	            	<td class="descripcion">Eliminacion :</td>
                    <td class="detalle">
                    	<select id="eliminacion_mod" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
                    <td class="descripcion">Seleccion :</td>
                    <td class="detalle">
                    	<select id="seleccion_mod" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
	            	<td class="descripcion">Conservacion Total :</td>
                    <td class="detalle">
                    	<select id="conservacion_total_mod" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
                    <td class="descripcion">Microfilmación y/o Digitalización :</td>
                    <td class="detalle">
                    	<select id="microfilmacion_digitalizacion_mod" class='select_opciones' >
							<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
						</select>		
                    </td>
	            </tr>
	            <tr>
	            	<td class="descripcion">Procedimiento</td>
	            	<td colspan="5">
	            		<textarea id="procedimiento_mod" cols="60" rows="5" onkeyup="validar_input_delay('procedimiento_mod')" onblur="validar_input('procedimiento_mod')"></textarea>
	                    <div id="procedimiento_mod_max" class="errores">El procedimiento no puede ser mayor a 500 caracteres (numeros o letras). (Actualmente <b><u id='procedimiento_mod_contadormax'></u></b> caracteres)</div>
	            		<div id="procedimiento_mod_min" class="errores">El procedimiento no puede ser menor a 6 caracteres (numeros o letras).</div>
	            		<div id="procedimiento_mod_null" class="errores">El procedimiento es obligatorio</div>
	            	</td>
	            	<td class="descripcion">
	            		Subserie Activa
	            	</td>
	            	<td class="detalle">
	            		<select id="activo_mod">
	            			<option value="SI">Si</option>
							<option value="NO" selected="selected">No</option>
	            		</select>
	            	</td>
	            </tr>
	            <tr>
					<td colspan="8" class="center">
						<div id="resultado_prestamo_documento"></div>
						<div id="boton_modificar_subserie">
							<input type="button" value="Modificar Subserie" class="botones" onclick="enviar_modificar_subserie()">
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
</body>
</html>