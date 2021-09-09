/* Script para ventana modal - Tecla Esc */
    window.addEventListener("keydown", function(event){
        var codigo = event.keyCode || event.which;
        if (codigo == 27){
            cerrarVentanaCrearTipoDocumento();
            cerrar_ventana_modificar_tipo_documento();
            cerrar_ventana_crear_tipo_documento_pqr();
            cerrar_ventana_modificar_tipo_documento_pqr();
            cerrar_ventana_crear_tipo_radicado();
            cerrar_ventana_crear_secuencia();
            cerrar_ventana_modificar_secuencia();
        }
        if(codigo== 8){ // Opcion para restringir que la tecla backspace da atras en el navegador.
        	if (history.forward(1)) {
				location.replace(history.forward(1));
			}	
        }
    }, false);
/* Fin script para ventana modal - Tecla Esc */

/* Script para cargar contenido en div "contenido_parametrizacion" */
function carga_contenido_param(tipo_param){
	$.ajax({
		url:'admin_parametrizacion/buscador.php',
		type: 'POST',
		data: {
			'parametro' : tipo_param
		},
		success: function(resp){
				if(resp!=""){
				$('#contenido_param').html(resp);
			}
		}
	})
}
/* Fin script para cargar contenido en div "contenido_parametrizacion" */
/************************************************************************************************************/
/* Desde aqui funciones formulario Agregar Nuevo Tipo Documento - Formulario radicacion entrada */

/* Funciones para desplegar ventana modal tipo documento terminos radicacion entrada */
function abrirVentanaCrearTipoDocumento(){
	$("#ventana").slideDown("slow");
	$("#tipo_doc").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrarVentanaCrearTipoDocumento(){
	$("#ventana").slideUp("slow");
	$(".art").slideUp("slow");

	oculta_errores();

	$("#tipo_doc").val("");
	$("#termino").val("");
}
/* Fin funciones para desplegar ventana modal tipo documento terminos radicacion entrada */

/* Funcion para restringir caraceres especiales en formulario tipo documento terminos radicacion entrada - Formulario PQR */
function espacios_formulario_tipo_documento_terminos(input){
	// console.log(input)
	switch(input){
	/* tipo documento radicacion entrada */	
		case 'tipo_doc':
			var str = $('#tipo_doc').val();	
			break;
		case 'descripcion':
			oculta_errores();
			var str=$('#descripcion').val();
			break;	
		case 'termino':
			oculta_errores();
			var str = $('#termino').val();
			if (isNaN(str)) {
				$('#error_no_es_numero').slideDown('slow');
			}else{
				$('#error_no_es_numero').slideUp('slow');
			}
			break;
		case 'tipo_doc_mod':
			oculta_mod_errores();
			var str = $('#tipo_doc_mod').val();	
			break;	
		case 'descripcion_mod':
			oculta_mod_errores();
			var str=$('#descripcion_mod').val();
			break;	
		case 'mod_termino':
			oculta_mod_errores();
			var str = $('#mod_termino').val();
			if (isNaN(str)) {
				$('#error_mod_no_es_numero').slideDown('slow');
			}else{
				$('#error_mod_no_es_numero').slideUp('slow');
			}
			break;	
	/* Hasta aqui tipo documento radicacion entrada */
	/* Tipo documento PQR */		
		case 'tipo_doc_pqr':
			var str = $('#tipo_doc_pqr').val();	
			break;
		case 'descripcion_pqr':
			oculta_errores_pqr();
			var str = $('#descripcion_pqr').val();	
			break;	
		case 'termino_pqr':
			oculta_errores_pqr();
			var str = $('#termino_pqr').val();
			if (isNaN(str)) {
				$('#error_no_es_numero_pqr').slideDown('slow');
			}else{
				$('#error_no_es_numero_pqr').slideUp('slow');
			}
			break;	
		case 'tipo_doc_mod_pqr':
			var str = $('#tipo_doc_mod_pqr').val();
			break;	
		case 'descripcion_pqr_mod':
			oculta_mod_errores_pqr();
			var str = $('#descripcion_pqr_mod').val();	
			break;
		case 'mod_termino_pqr':
			oculta_mod_errores_pqr();
			var str = $('#mod_termino_pqr').val();
			if (isNaN(str)) {
				$('#error_mod_no_es_numero_pqr').slideDown('slow');
			}else{
				$('#error_mod_no_es_numero_pqr').slideUp('slow');
			}
			break;	
	/* Hasta aqui tipo documento PQR */	
	/* Desde aqui tipo radicado */
		case 'codigo_tipo_rad':
			var str = $('#codigo_tipo_rad').val();
			break;
		case 'nombre_tipo_rad':
			var str = $("#nombre_tipo_rad").val();
			break;	
	/* Hasta aqui tipo radicado */	
	/* Desde aqui secuencia */
		case 'codigo_dependencia_padre_sec_mod':
			var str = $("#codigo_dependencia_padre_sec_mod").val();		
			break;	
	/* Hasta aqui secuencia */			
	}
		str = str.replace("'",""); 	str = str.replace("*",""); 	str = str.replace("+",'');	str = str.replace("/","");	
		str = str.replace('  ','');	str = str.replace('!','');	str = str.replace('"','');	str = str.replace('#','');	
		str = str.replace('$','');	str = str.replace('%','');	str = str.replace('&','');	str = str.replace('(','');	
		str = str.replace(')','');	str = str.replace(',','');	str = str.replace('-',''); 	str = str.replace(':','');	
		str = str.replace(';','');	str = str.replace('<','');	str = str.replace('=','');	str = str.replace('>','');	
		str = str.replace('?','');	str = str.replace('@','');	str = str.replace('[',''); 	str = str.replace(']','');	
		str = str.replace('^','');	str = str.replace('_','');	str = str.replace('{','');	str = str.replace('|','');	
		str = str.replace('}','');	str = str.replace('~',''); 	str = str.replace('¡','');	str = str.replace('°','');	
		str = str.replace('´','');	str = str.replace('¿','');	str = str.replace('á','a');	str = str.replace('Á','A');	
		str = str.replace('é','e');	str = str.replace('É','E');	str = str.replace('Í','I');	str = str.replace('í','i');	
		str = str.replace('ó','o');	str = str.replace('Ó','O');	str = str.replace('ú','u'); str = str.replace('Ú','U');	
/*
		str = str.replace('ñ','N');
		str = str.replace('Ñ','N');	
*/	
	switch(input){
	/* Tipo documento radicacion entrada */							
		case 'tipo_doc':
			$('#tipo_doc').val(str.toUpperCase());
			break;
		case 'descripcion':
			$('#descripcion').val(str);	
			break;	
		case 'termino':
			$('#termino').val(str);
			break;	
		case 'tipo_doc_mod':
			$('#tipo_doc_mod').val(str.toUpperCase());	
			break;
		case 'descripcion_mod':
			$('#descripcion_mod').val(str);	
			break;	
		case 'mod_termino':
			$('#mod_termino').val(str.toUpperCase());
			break;
	/* Hasta aqui Tipo documento radicacion entrada */								
	/* Tipo documento PQR */					
		case 'tipo_doc_pqr':
			$('#tipo_doc_pqr').val(str.toUpperCase());
			break;	
		case 'termino_pqr':
			$('#termino_pqr').val(str.toUpperCase());
			break;	
		case 'tipo_doc_mod_pqr':
			$('#tipo_doc_mod_pqr').val(str.toUpperCase());
			break;	
		case 'mod_termino_pqr':
			$('#mod_termino_pqr').val(str.toUpperCase());
			break;	
	/* Hasta aqui tipo documento PQR */	
	/* Desde aqui tipo radicado */
		case 'codigo_tipo_rad':
			$('#codigo_tipo_rad').val(str.toUpperCase());
			break;
		case 'nombre_tipo_rad':
			$('#nombre_tipo_rad').val(str.toUpperCase());
			break;
	/* Hasta aqui tipo radicado */	
	/* Desde aqui secuencia */
		case 'codigo_dependencia_padre_sec_mod':
			$('#codigo_dependencia_padre_sec_mod').val(str.toUpperCase());
			break;		
	/* Hasta aqui secuencia */												
	}	
}
/* Fin funcion para restringir caraceres especiales en formulario tipo documento terminos radicacion entrada - Formulario PQR */
/* Funcion oculta_errores tipo documento radicacion entrada */
function oculta_errores(){
	$('#error_tipo_doc').slideUp('slow');
	$('#error_tipo_doc_invalido').slideUp('slow');
	$('#error_tipo_doc_minimo').slideUp('slow');
	$('#error_tipo_doc_maximo').slideUp('slow');

	$('#error_descripcion').slideUp("slow");
	$('#error_min_descripcion').slideUp("slow");
	$('#error_max_descripcion').slideUp("slow");

	$('#error_termino').slideUp('slow');
	$('#error_no_es_numero').slideUp('slow');
}
/* Fin funcion oculta errores tipo documento radicacion entrada */
/* Script buscador tipo documento - Formulario Agregar Nuevo tipo documento radicacion entrada */
var timerid='';
$('#tipo_doc').on('input',function(e){ // Accion que se activa cuando se digita #tipo_doc
    $('#sugerencias_tipo_doc').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>"); 				
    oculta_errores();
    var envio_tipo_doc = $(this).val();
    
    if($(this).data('lastval')!= envio_tipo_doc){
    	$(this).data('lastval',envio_tipo_doc);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {

     		if(envio_tipo_doc.length>2 && envio_tipo_doc.length<=30){
        		 $.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/buscador.php',
					data: {
						'search_tipo_doc' : envio_tipo_doc
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_tipo_doc').html(resp);
						}else{
							$('#sugerencias_tipo_doc').html('');
						}
					}
				})			 		
			}else{
				if(envio_tipo_doc.length>30){
					$('#error_tipo_doc_maximo').slideDown('slow');
					$('#sugerencias_tipo_doc').html("");
				}else{
					$('#sugerencias_tipo_doc').html('<h4>Para iniciar la búsqueda debe ingresar por lo menos 3 caracteres.</h4>');
				} 
			}	 				 
		},1000);
    };
});
/* Fin script buscador tipo documento - Formulario Agregar Nuevo tipo documento radicacion entrada */
/* Funcion validar tipo documento para hacer submit - Formulario tipo documento radicacion entrada */
function validar_agregar_tipo_doc(){
	var tipo_documento =$('#tipo_doc').val()

	if(tipo_documento==''){
		$('#error_tipo_doc').slideDown('slow');
		$('#error_tipo_doc_minimo').slideUp('slow');
		$('#error_tipo_doc_maximo').slideUp('slow');
		$('#error_tipo_doc_invalido').slideUp('slow');
		return false;					
	}else{
		if(tipo_documento.length<6){
			$("#error_tipo_doc").slideUp("slow");
			$("#error_tipo_doc_minimo").slideDown("slow");
			$("#error_tipo_doc_maximo").slideUp("slow");
			$("#error_tipo_doc_invalido").slideUp("slow");
			return false;
		}else{
			if(tipo_documento.length>30){
				$("#error_tipo_doc").slideUp("slow");
				$("#error_tipo_doc_minimo").slideUp("slow");
				$("#error_tipo_doc_maximo").slideDown("slow");
				$("#error_tipo_doc_invalido").slideUp("slow");
				return false;
			}else{
				if($("#art").is(":visible")){
					$("#error_tipo_doc").slideUp("slow");
					$("#error_tipo_doc_minimo").slideUp("slow");
					$("#error_tipo_doc_maximo").slideUp("slow");
					$("#error_tipo_doc_invalido").slideDown("slow");
					return false;
				}else{
					$("#error_tipo_doc").slideUp("slow");
					$("#error_tipo_doc_minimo").slideUp("slow");
					$("#error_tipo_doc_maximo").slideUp("slow");
					$("#error_tipo_doc_invalido").slideUp("slow");
					return true;
				}
			}
		}
	}			
}
/* Fin funcion validar tipo documento para hacer submit - Formulario tipo documento radicacion entrada */
/* Funcion para validar termino tipo documento radicacion entrada */
function validar_agregar_termino(){
	var ter=$("#termino").val();

	if(ter==""){
		$("#error_termino").slideDown("slow");
		return false;
	}else{
		if($("#error_no_es_numero").is(":visible")){
			return false;
		}else{
			return true;
		}
	}
}
function validar_descripcion(){
	var desc=$('#descripcion').val();

	if(desc==""){
		$('#error_descripcion').slideDown("slow");
		$('#error_min_descripcion').slideUp("slow");
		$('#error_max_descripcion').slideUp("slow");
		return false;
	}else{
		if(desc.length<20){
			$('#error_descripcion').slideUp("slow");
			$('#error_min_descripcion').slideDown("slow");
			$('#error_max_descripcion').slideUp("slow");
			return false;
		}else{
			if(desc.length>500){
				$('#error_descripcion').slideUp("slow");
				$('#error_min_descripcion').slideUp("slow");
				$('#error_max_descripcion').slideDown("slow");
				return false;
			}else{
				return true;
			}
		}
	}
}
/* Fin funcion para validar termino tipo documento radicacion entrada */
/* Funcion para insertar tipo documento - submit - tipo documento radicacion entrada */
function validar_tipo_doc(){
	var validar_td =validar_agregar_tipo_doc();
	if(validar_td==false){
		$("#tipo_doc").focus()
		return false;
	}else{
		var validar_desc=validar_descripcion();
		if(validar_desc==false){
			$('#descripcion').focus();
			return false;
		}else{
			var validar_ter=validar_agregar_termino();
			if(validar_ter==false){
				$("#termino").focus();
				return false;
			}else{
				return true;
			}
		}
	}
}

$(function submit_agregar_tipo_documento(){
	$('#enviar_td').click(function submit_agregar_tipo_documento(){
		if($('.imagen_logo').is(":visible")){
			// sweetAlert({
			Swal.fire({	
				position 			: 'top-end',
			    showConfirmButton 	: false,
			    timer 				: 1500,	
			    title 				: 'La consulta se está ejecutando.',
			    text 				: 'Un momento por favor.',
			    type 				: 'warning'
			});
		}else{
			var submit_agregar_td = validar_tipo_doc();
			if(submit_agregar_td==false){
				return false;
			}else{ 	// $('#formulario_agregar_tipo_documento').submit(); // Realizar la creación del tipo documento
				oculta_errores();

				var tipo_formulario1=$("#tipo_formulario_agregar_tipo_documento").val();
				var tipo_doc1=$("#tipo_doc").val();
				var descripcion1=$("#descripcion").val();
				var termino1=$("#termino").val();

				$.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/query_parametrizacion.php',
					data: {
						'tipo_formulario' : tipo_formulario1,
						'tipo_doc' : tipo_doc1,
						'descripcion' : descripcion1,
						'termino' : termino1
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_tipo_doc').html(resp);
						}
					}
				})	
			
			}										
		}
	});
})
/* Fin funcion para insertar tipo documento - submit - Formulario tipo documento radicacion entrada */
/* Hasta aqui funciones formulario Agregar Nuevo Tipo Documento radicacion entrada */
/************************************************************************************************************/
/* Desde aqui funciones formulario Modificar Tipo Documento radicacion entrada */
/* Funcion para cargar tipo de documento para modificacion tipo documento radicacion entrada */
function cargar_modifica_tipo_documento(id,tipo_doc,descripcion_tipo_documento,tiempo_tram,estado){
	//alert("id es "+id+" tipo doc "+tipo_doc+" tiempo_tram "+tiempo_tram+" estado "+estado);
	$("#id_mod").val(id);
	$("#tipo_doc_mod").val(tipo_doc);
	$("#tipo_doc_mod_ant").val(tipo_doc);
	$("#descripcion_mod").val(descripcion_tipo_documento);
	$("#mod_termino").val(tiempo_tram);
	$('#mod_estado').val(estado);
	abrir_ventana_modificar_tipo_documento();
} 
/* Fin funcion para cargar tipo de documento radicacion entrada para modificacion */
/* Funciones para desplegar ventana modal modificar tipo documento terminos radicacion entrada */
function abrir_ventana_modificar_tipo_documento(){
	$("#ventana2").slideDown("slow");
	cerrarVentanaCrearTipoDocumento();
	$("#tipo_doc_mod").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrar_ventana_modificar_tipo_documento(){
	$("#ventana2").slideUp("slow");
	$(".art").slideUp("slow");

	oculta_mod_errores();

	$("#tipo_doc_mod").val("");
	$("#termino").val("");
}
/* Fin funciones para desplegar ventana modal modificar tipo documento terminos radicacion entrada */
/* Funcion oculta_mod_errores - Tipo documento radicacion entrada */
function oculta_mod_errores(){
	$("#error_tipo_doc_mod").slideUp("slow");
	$("#error_tipo_doc_mod_invalido").slideUp("slow");
	$("#error_tipo_doc_mod_minimo").slideUp("slow");
	$("#error_tipo_doc_mod_maximo").slideUp("slow");

	$("#error_mod_termino").slideUp("slow");
	$("#error_mod_no_es_numero").slideUp("slow");

	$('#error_descripcion_mod').slideUp("slow");
	$('#error_min_descripcion_mod').slideUp("slow");
	$('#error_max_descripcion_mod').slideUp("slow");

	$("#art_mod").slideUp("slow");
}
/* Fin funcion oculta_mod_errores - Tipo documento radicacion entrada */
/* Script buscador tipo documento - Formulario Modificar tipo documento radicacion entrada */
$("#tipo_doc_mod").on("input",function(e){ // Accion que se activa cuando se digita #tipo_doc_mod
    $('#sugerencias_tipo_doc_mod').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>"); 				
    oculta_mod_errores();
    var envio_tipo_mod_doc = $(this).val(), envio_tipo_mod_doc=envio_tipo_mod_doc.trim(); // Elimino espacios antes y despues de la cadena para evitar errores

    var envio_tipo_mod_doc_ant=$("#tipo_doc_mod_ant").val();
    
    if($(this).data("lastval")!= envio_tipo_mod_doc){
    	$(this).data("lastval",envio_tipo_mod_doc);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
     		if(envio_tipo_mod_doc.length>2 && envio_tipo_mod_doc.length<=30){
        		 $.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/buscador.php',
					data: {
						'search_tipo_doc_mod' : envio_tipo_mod_doc,
						'search_tipo_doc_mod_ant' : envio_tipo_mod_doc_ant
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_tipo_doc_mod').html(resp);
						}else{
							$('#sugerencias_tipo_doc_mod').html('');
						}
					}
				})			 		
			}else{
				if(envio_tipo_mod_doc.length>30){
					$('#error_tipo_doc_mod_maximo').slideDown('slow');
					$('#sugerencias_tipo_doc_mod').html("");
				}else{
					$('#sugerencias_tipo_doc_mod').html('<h4>Para iniciar la búsqueda debe ingresar por lo menos 3 caracteres.</h4>');
				} 
			}	  				 
		},1000);
    };
});

function id_anterior(documento_usuario){ // Funcion que se activa cuando se digita el mismo valor que tenía.
	$('#tipo_doc_mod').val(documento_usuario);
	$('#sugerencias_tipo_doc_mod').html('');
	oculta_mod_errores();

	$('#mod_termino').focus();
}
/* Fin script buscador tipo documento - Formulario Modificar Tipo Documento radicacion entrada */
/* Fin script buscador mod_tipo_documento - Formulario Modificar Tipo Documento radicacion entrada */
/* Script validar si tipo_documento ya existe - Formulario Modificar Tipo Documento radicacion entrada */
function valida_mod_td_ya_existe(){  // Parece que no se usa. Comento para verificar y eliminar.
	$("#tipo_doc_mod").focus();
	$("#error_tipo_doc_mod_invalido").slideDown();
}
/* Fin script validar si tipo_documento ya existe - Formulario Modificar Tipo Documento radicacion entrada */
/* Funcion validar mod_tipo_documento para hacer submit - Tipo documento radicacion entrada */
function validar_modificar_tipo_doc(){
	var tipo_documento_mod =$('#tipo_doc_mod').val()

	if(tipo_documento_mod==""){
		$("#error_tipo_doc_mod").slideDown("slow");
		$("#error_tipo_doc_mod_minimo").slideUp("slow");
		$("#error_tipo_doc_mod_maximo").slideUp("slow");
		$("#error_tipo_doc_mod_invalido").slideUp("slow");
		return false;					
	}else{
		if(tipo_documento_mod.length<6){
			$("#error_tipo_doc_mod").slideUp("slow");
			$("#error_tipo_doc_mod_minimo").slideDown("slow");
			$("#error_tipo_doc_mod_maximo").slideUp("slow");
			$("#error_tipo_doc_mod_invalido").slideUp("slow");
			return false;
		}else{
			if(tipo_documento_mod.length>30){
				$("#error_tipo_doc_mod").slideUp("slow");
				$("#error_tipo_doc_mod_minimo").slideUp("slow");
				$("#error_tipo_doc_mod_maximo").slideDown("slow");
				$("#error_tipo_doc_mod_invalido").slideUp("slow");
				return false;
			}else{
				if($("#art_mod").is(":visible")){
					$("#error_tipo_doc_mod").slideUp("slow");
					$("#error_tipo_doc_mod_minimo").slideUp("slow");
					$("#error_tipo_doc_mod_maximo").slideUp("slow");
					$("#error_tipo_doc_mod_invalido").slideDown("slow");
					return false;
				}else{
					$("#error_tipo_doc_mod").slideUp("slow");
					$("#error_tipo_doc_mod_minimo").slideUp("slow");
					$("#error_tipo_doc_mod_maximo").slideUp("slow");
					$("#error_tipo_doc_mod_invalido").slideUp("slow");
					return true;
				}
			}
		}
	}			
}
function validar_descripcion_mod(){
	var desc=$('#descripcion_mod').val();

	if(desc==""){
		$('#error_descripcion_mod').slideDown("slow");
		$('#error_min_descripcion_mod').slideUp("slow");
		$('#error_max_descripcion_mod').slideUp("slow");
		return false;
	}else{
		if(desc.length<20){
			$('#error_descripcion_mod').slideUp("slow");
			$('#error_min_descripcion_mod').slideDown("slow");
			$('#error_max_descripcion_mod').slideUp("slow");
			return false;
		}else{
			if(desc.length>500){
				$('#error_descripcion_mod').slideUp("slow");
				$('#error_min_descripcion_mod').slideUp("slow");
				$('#error_max_descripcion_mod').slideDown("slow");
				return false;
			}else{
				return true;
			}
		}
	}
}

/* Fin funcion validar mod_tipo_documento para hacer submit - Tipo documento radicacion entrada */
/* Funcion para validar mod_termino - Tipo documento radicacion entrada*/
function validar_modificar_termino(){
	var mod_ter=$("#mod_termino").val();

	if(mod_ter==""){
		$("#error_mod_termino").slideDown("slow");
		return false;
	}else{
		if($("#error_mod_no_es_numero").is(":visible")){
			return false;
		}else{
			return true;
		}
	}
}
/* Fin funcion para validar mod_termino - Tipo documento radicacion entrada*/
/*Funcion para modificar tipo documento radicacion entrada - submit */
function validar_modificar_usuario(){
	var validar_mod_td =validar_modificar_tipo_doc();
	if(validar_mod_td==false){
		$("#tipo_doc_mod").focus()
		return false;
	}else{
		var validar_desc_mod=validar_descripcion_mod();
		if(validar_desc_mod==false){
			$('#descripcion_mod').focus();
			return false;
		}else{		
			var validar_ter=validar_modificar_termino();
			if(validar_ter==false){
				$("#termino").focus();
				return false;
			}else{
				return true;
			}
		}
	}
}

$(function submit_modificar_tipo_documento(){
	$('#enviar_mod_td').click(function submit_modificar_tipo_documento(){
		if($('.imagen_logo').is(":visible")){
			// sweetAlert({
			Swal.fire({	
				position 			: 'top-end',
			    showConfirmButton 	: false,
			    timer 				: 1500,	
			    title 				: 'La consulta se está ejecutando.',
			    text 				: 'Un momento por favor.',
			    type 				: 'warning'
			});
			return false;
		}else{
			var submit_modificar_td = validar_modificar_usuario();
			if(submit_modificar_td==false){
				return false;
			}else{ 	//  	$('#formulario_modificar_tipo_documento').submit(); // Realizar la modificacion del tipo documento
				oculta_mod_errores();

				var tipo_formulario1=$("#tipo_formulario_modificar_tipo_documento").val();
				
				var id_mod1=$("#id_mod").val();
				var tipo_doc1=$("#tipo_doc_mod").val();
				var tipo_doc_mod_ant1=$("#tipo_doc_mod_ant").val();
				var descripcion1=$("#descripcion_mod").val();
				var termino1=$("#mod_termino").val();
				var estado1=$("#mod_estado").val();

				$.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/query_parametrizacion.php',
					data: {
						'tipo_formulario' : tipo_formulario1,
						'id_mod' : id_mod1,
						'tipo_doc_mod' : tipo_doc1,
						'tipo_doc_mod_ant' : tipo_doc_mod_ant1,
						'descripcion_mod' : descripcion1,
						'mod_termino' : termino1,
						'mod_estado' : estado1
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_tipo_doc_mod').html(resp);
						}
					}
				})	
			}	
		}										
	});
})
/* Fin funcion para modificar tipo documento radicacion entrada - submit */

/* Hasta aqui funciones formulario Modificar Tipo Documento radicacion entrada*/
/************************************************************************************************************/
/************************************************************************************************************/
/************************************************************************************************************/
/* Desde aqui funciones formulario agregar tipo documento PQR */
/* Funciones para desplegar ventana modal tipo documento PQR terminos */
function abrir_ventana_crear_tipo_documento_pqr(){
	$("#ventana3").slideDown("slow");
	$("#tipo_doc_pqr").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrar_ventana_crear_tipo_documento_pqr(){
	$("#ventana3").slideUp("slow");
	$(".art_mod").slideUp("slow");

	oculta_errores_pqr();

	$("#tipo_doc_pqr").val("");
	$("#descripcion_pqr").val("");
	$("#termino_pqr").val("");
}
/* Fin funciones para desplegar ventana modal tipo documento PQR terminos */
/* Funcion oculta_errores_pqr */
function oculta_errores_pqr(){
	$("#error_tipo_doc_pqr").slideUp("slow");
	$("#error_tipo_doc_pqr_invalido").slideUp("slow");
	$("#error_tipo_doc_pqr_minimo").slideUp("slow");
	$("#error_tipo_doc_pqr_maximo").slideUp("slow");

	$("#error_descripcion_pqr").slideUp("slow");
	$("#error_min_descripcion_pqr").slideUp("slow");
	$("#error_max_descripcion_pqr").slideUp("slow");

	$("#error_termino_pqr").slideUp("slow");
	$("#error_no_es_numero_pqr").slideUp("slow");
}
/* Fin funcion oculta_errores_pqr */
/* Script buscador tipo documento - Formulario Agregar Nuevo tipo documento PQR */
$("#tipo_doc_pqr").on("input",function(e){ // Accion que se activa cuando se digita #tipo_doc_pqr
    $('#sugerencias_tipo_doc_pqr').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>"); 				
    oculta_errores_pqr();
    var envio_tipo_doc_pqr = $(this).val();
    
    if($(this).data("lastval")!= envio_tipo_doc_pqr){
    	$(this).data("lastval",envio_tipo_doc_pqr);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {

     		if(envio_tipo_doc_pqr.length>2 && envio_tipo_doc_pqr.length<=30){
        		 $.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/buscador.php',
					data: {
						'search_tipo_doc_pqr' : envio_tipo_doc_pqr
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_tipo_doc_pqr').html(resp);
						}else{
							$('#sugerencias_tipo_doc_pqr').html('');
						}
					}
				})			 		
			}else{
				if(envio_tipo_doc_pqr.length>30){
					$('#error_tipo_doc_pqr_maximo').slideDown('slow');
					$('#sugerencias_tipo_doc_pqr').html("");
				}else{
					$('#sugerencias_tipo_doc_pqr').html('<h4>Para iniciar la búsqueda debe ingresar por lo menos 3 caracteres.</h4>');
				}  	
			}	  				 
		},1000);
    };
});
/* Fin script buscador tipo documento - Formulario Agregar Nuevo tipo documento PQR */
/* Funcion validar tipo documento pqr para hacer submit */
function validar_agregar_tipo_doc_pqr(){
	var tipo_documento_pqr =$('#tipo_doc_pqr').val()

	if(tipo_documento_pqr==""){
		$("#error_tipo_doc_pqr").slideDown("slow");
		$("#error_tipo_doc_pqr_minimo").slideUp("slow");
		$("#error_tipo_doc_pqr_maximo").slideUp("slow");
		$("#error_tipo_doc_pqr_invalido").slideUp("slow");
		return false;					
	}else{
		if(tipo_documento_pqr.length<6){
			$("#error_tipo_doc_pqr").slideUp("slow");
			$("#error_tipo_doc_pqr_minimo").slideDown("slow");
			$("#error_tipo_doc_pqr_maximo").slideUp("slow");
			$("#error_tipo_doc_pqr_invalido").slideUp("slow");
			return false;
		}else{
			if(tipo_documento_pqr.length>30){
				$("#error_tipo_doc_pqr").slideUp("slow");
				$("#error_tipo_doc_pqr_minimo").slideUp("slow");
				$("#error_tipo_doc_pqr_maximo").slideDown("slow");
				$("#error_tipo_doc_pqr_invalido").slideUp("slow");
				return false;
			}else{
				if($("#art3").is(":visible")){
					$("#error_tipo_doc_pqr").slideUp("slow");
					$("#error_tipo_doc_pqr_minimo").slideUp("slow");
					$("#error_tipo_doc_pqr_maximo").slideUp("slow");
					$("#error_tipo_doc_pqr_invalido").slideDown("slow");
					return false;
				}else{
					$("#error_tipo_doc_pqr").slideUp("slow");
					$("#error_tipo_doc_pqr_minimo").slideUp("slow");
					$("#error_tipo_doc_pqr_maximo").slideUp("slow");
					$("#error_tipo_doc_pqr_invalido").slideUp("slow");
					return true;
				}
			}
		}
	}			
}
/* Fin funcion validar tipo documento pqr para hacer submit */
/* Funcion validar descripcion agregar tipo documento pqr para hacer submit */
function validar_descripcion_pqr(){
	var desc_documento_pqr =$('#descripcion_pqr').val()

	if(desc_documento_pqr==""){
		$("#error_descripcion_pqr").slideDown("slow");
		$("#error_min_descripcion_pqr").slideUp("slow");
		$("#error_max_descripcion_pqr").slideUp("slow");
		return false;					
	}else{
		if(desc_documento_pqr.length<20){
			$("#error_descripcion_pqr").slideUp("slow");
			$("#error_min_descripcion_pqr").slideDown("slow");
			$("#error_max_descripcion_pqr").slideUp("slow");
			return false;
		}else{
			if(desc_documento_pqr.length>500){
				$("#error_descripcion_pqr").slideUp("slow");
				$("#error_min_descripcion_pqr").slideUp("slow");
				$("#error_max_descripcion_pqr").slideDown("slow");
				return false;
			}else{
				$("#error_descripcion_pqr").slideUp("slow");
				$("#error_min_descripcion_pqr").slideUp("slow");
				$("#error_max_descripcion_pqr").slideUp("slow");
				return true;	
			}
		}
	}			
}
/* Fin funcion validar descripcion agregar tipo documento pqr para hacer submit */
/* Funcion para validar agregar termino_pqr */
function validar_agregar_termino_pqr(){
	var ter_pqr=$("#termino_pqr").val();

	if(ter_pqr==""){
		$("#error_termino_pqr").slideDown("slow");
		return false;
	}else{
		if($("#error_no_es_numero_pqr").is(":visible")){
			return false;
		}else{
			return true;
		}
	}
}
/* Fin funcion para validar agregar termino_pqr */
/* Funcion para insertar tipo documento PQR - submit */
function validar_tipo_doc_pqr(){
	var validar_td_pqr =validar_agregar_tipo_doc_pqr();
	if(validar_td_pqr==false){
		$("#tipo_doc_pqr").focus()
		return false;
	}else{
		var validar_desc_pqr=validar_descripcion_pqr();
		if(validar_desc_pqr==false){
			$("#descripcion_pqr").focus();
			return false;
		}else{
			var validar_ter_pqr=validar_agregar_termino_pqr();
			if(validar_ter_pqr==false){
				$("#termino_pqr").focus();
				return false;
			}else{
				return true;
			}	
		}
	}
}

$(function submit_agregar_tipo_documento_pqr(){
	$('#enviar_td_pqr').click(function submit_agregar_tipo_documento_pqr(){
		if($('.imagen_logo').is(":visible")){
			// sweetAlert({
			Swal.fire({	
				position 			: 'top-end',
			    showConfirmButton 	: false,
			    timer 				: 1500,	
			    title 				: 'La consulta se está ejecutando.',
			    text 				: 'Un momento por favor.',
			    type 				: 'warning'
			});
		}else{
			var submit_agregar_td_pqr = validar_tipo_doc_pqr();
			if(submit_agregar_td_pqr==false){
				return false;
			}else{  // 		$('#formulario_agregar_tipo_documento_pqr').submit(); // Realizar la creación del tipo documento PQR
				oculta_errores_pqr();

				var tipo_formulario1 	= $("#tipo_formulario_crear_tipo_documento_pqr").val();
				var tipo_doc1 			= $("#tipo_doc_pqr").val();
				var descripcion1 		= $("#descripcion_pqr").val();
				var termino1 			= $("#termino_pqr").val();

				$.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/query_parametrizacion.php',
					data: {
						'tipo_formulario' 	: tipo_formulario1,
						'tipo_doc_pqr' 		: tipo_doc1,
						'descripcion_pqr' 	: descripcion1,
						'termino_pqr' 		: termino1
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_tipo_doc_pqr').html(resp);
						}
					}
				})	
			}										
		}
	});
})
/* Fin funcion para insertar tipo documento PQR - submit */
/* Fin funciones formulario agregar tipo documento PQR */
/************************************************************************************************************/
/* Desde aqui funciones formulario modificar tipo documento PQR */
/* Funcion para cargar tipo de documento PQR para modificacion */
function cargar_modifica_tipo_documento_pqr(id,tipo_doc,descripcion,tiempo_tram,estado){
	//alert("id es "+id+" tipo doc "+tipo_doc+" Descripcion es "+descripcion+" tiempo_tram "+tiempo_tram+" estado "+estado);
	$("#id_mod_pqr").val(id);
	$("#tipo_doc_mod_pqr").val(tipo_doc);
	$("#tipo_doc_mod_ant_pqr").val(tipo_doc);
	$("#descripcion_pqr_mod").val(descripcion);
	$("#mod_termino_pqr").val(tiempo_tram);
	$('#mod_estado_pqr').val(estado);
	abrir_ventana_modificar_tipo_documento_pqr();
} 
/* Fin funcion para cargar tipo de documento PQR para modificacion */
/* Funciones para desplegar ventana modal modificar tipo documento PQR terminos */
function abrir_ventana_modificar_tipo_documento_pqr(){
	$("#ventana4").slideDown("slow");
	cerrar_ventana_crear_tipo_documento_pqr();
	$("#tipo_doc_mod_pqr").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrar_ventana_modificar_tipo_documento_pqr(){
	$("#ventana4").slideUp("slow");

	oculta_mod_errores_pqr();
	$(".art_mod").slideUp("slow");

	$("#id_mod_pqr").val("");
	$("#tipo_doc_mod_ant_pqr").val("");
	$("#tipo_doc_mod_pqr").val("");
	$("#descripcion_pqr_mod").val("");
	$("#mod_termino_pqr").val("");
	$("#mod_estado_pqr").val("");	
}
/* Fin funciones para desplegar ventana modal modificar tipo documento PQR terminos */
/* Funcion oculta_mod_errores_pqr */
function oculta_mod_errores_pqr(){
	$("#error_tipo_doc_mod_pqr").slideUp("slow");
	$("#error_tipo_doc_mod_pqr_invalido").slideUp("slow");
	$("#error_tipo_doc_mod_pqr_minimo").slideUp("slow");
	$("#error_tipo_doc_mod_pqr_maximo").slideUp("slow");

	$("#error_descripcion_pqr_mod").slideUp("slow");
	$("#error_min_descripcion_pqr_mod").slideUp("slow");
	$("#error_max_descripcion_pqr_mod").slideUp("slow");

	$("#error_mod_termino_pqr").slideUp("slow");
	$("#error_mod_no_es_numero_pqr").slideUp("slow");

	$(".errores").slideUp("slow");
}
/* Fin funcion oculta_mod_errores_pqr */
/* Script buscador tipo documento - Formulario Modificar tipo documento PQR */
$("#tipo_doc_mod_pqr").on("input",function(e){ // Accion que se activa cuando se digita #tipo_doc_mod_pqr
    $('#sugerencias_tipo_doc_mod_pqr').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>"); 				
    oculta_mod_errores_pqr();
    var envio_tipo_doc_pqr_mod = $(this).val();
    var envio_tipo_doc_mod_ant_pqr =$("#tipo_doc_mod_ant_pqr").val();
    
    if($(this).data("lastval")!= envio_tipo_doc_pqr_mod){
    	$(this).data("lastval",envio_tipo_doc_pqr_mod);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {

     		if(envio_tipo_doc_pqr_mod.length>2 && envio_tipo_doc_pqr_mod.length<=30){
        		 $.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/buscador.php',
					data: {
						'search_tipo_doc_pqr_mod' : envio_tipo_doc_pqr_mod,
						'search_tipo_doc_mod_ant_mod' : envio_tipo_doc_mod_ant_pqr
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_tipo_doc_mod_pqr').html(resp);
						}else{
							$('#sugerencias_tipo_doc_mod_pqr').html('');
						}
					}
				})			 		
			}else{
				if(envio_tipo_doc_pqr_mod.length>30){
					$('#error_tipo_doc_mod_pqr_maximo').slideDown('slow');
					$('#sugerencias_tipo_doc_mod_pqr').html("");
				}else{
					$('#sugerencias_tipo_doc_mod_pqr').html('<h4>Para iniciar la búsqueda debe ingresar por lo menos 3 caracteres.</h4>');
				}  				 
			}	
		},1000);
    };
});
function id_anterior_pqr(documento_usuario){ // Funcion que se activa cuando se digita el mismo valor que tenía.
	$('#tipo_doc_mod_pqr').val(documento_usuario);
	$('#sugerencias_tipo_doc_mod_pqr').html('');
	oculta_mod_errores_pqr();

	$('#descripcion_pqr_mod').focus();
}
/* Fin script buscador tipo documento - Formulario Modificar tipo documento PQR */
/* Funcion validar mod_tipo_documento para hacer submit - Tipo documento PQR */
function validar_modificar_tipo_doc_pqr(){
	var tipo_documento_mod_pqr =$('#tipo_doc_mod_pqr').val();

	if(tipo_documento_mod_pqr==""){
		$("#error_tipo_doc_mod_pqr").slideDown("slow");
		$("#error_tipo_doc_mod_pqr_minimo").slideUp("slow");
		$("#error_tipo_doc_mod_pqr_maximo").slideUp("slow");
		$("#error_tipo_doc_mod_pqr_invalido").slideUp("slow");
		return false;					
	}else{
		if(tipo_documento_mod_pqr.length<5){
			$("#error_tipo_doc_mod_pqr").slideUp("slow");
			$("#error_tipo_doc_mod_pqr_minimo").slideDown("slow");
			$("#error_tipo_doc_mod_pqr_maximo").slideUp("slow");
			$("#error_tipo_doc_mod_pqr_invalido").slideUp("slow");
			return false;
		}else{
			if(tipo_documento_mod_pqr.length>30){
				$("#error_tipo_doc_mod_pqr").slideUp("slow");
				$("#error_tipo_doc_mod_pqr_minimo").slideUp("slow");
				$("#error_tipo_doc_mod_pqr_maximo").slideDown("slow");
				$("#error_tipo_doc_mod_pqr_invalido").slideUp("slow");
				return false;
			}else{
				if($("#art4").is(":visible")){
					$("#error_tipo_doc_mod_pqr").slideUp("slow");
					$("#error_tipo_doc_mod_pqr_minimo").slideUp("slow");
					$("#error_tipo_doc_mod_pqr_maximo").slideUp("slow");
					$("#error_tipo_doc_mod_pqr_invalido").slideDown("slow");
					return false;
				}else{
					$("#error_tipo_doc_mod_pqr").slideUp("slow");
					$("#error_tipo_doc_mod_pqr_minimo").slideUp("slow");
					$("#error_tipo_doc_mod_pqr_maximo").slideUp("slow");
					$("#error_tipo_doc_mod_pqr_invalido").slideUp("slow");
					return true;
				}
			}
		}
	}			
}
/* Fin funcion validar mod_tipo_documento para hacer submit - Tipo documento PQR */
/* Script validar si tipo_documento ya existe - Formulario Modificar Tipo Documento PQR */
function valida_mod_td_pqr_ya_existe(){  
	$("#tipo_doc_mod_pqr").focus();
	$("#error_tipo_doc_mod_pqr_invalido").slideDown();
}
/* Fin script validar si tipo_documento ya existe - Formulario Modificar Tipo Documento PQR */
/* Funcion validar descripcion modificar tipo documento pqr para hacer submit */
function validar_modificar_descripcion_pqr(){
	var desc_documento_mod_pqr =$('#descripcion_pqr_mod').val()

	if(desc_documento_mod_pqr==""){
		$("#error_descripcion_pqr_mod").slideDown("slow");
		$("#error_min_descripcion_pqr_mod").slideUp("slow");
		$("#error_max_descripcion_pqr_mod").slideUp("slow");
		return false;					
	}else{
		if(desc_documento_mod_pqr.length<20){
			$("#error_descripcion_pqr_mod").slideUp("slow");
			$("#error_min_descripcion_pqr_mod").slideDown("slow");
			$("#error_max_descripcion_pqr_mod").slideUp("slow");
			return false;
		}else{
			if(desc_documento_mod_pqr.length>500){
				$("#error_descripcion_pqr_mod").slideUp("slow");
				$("#error_min_descripcion_pqr_mod").slideUp("slow");
				$("#error_max_descripcion_pqr_mod").slideDown("slow");
				return false;
			}else{
				$("#error_descripcion_pqr_mod").slideUp("slow");
				$("#error_min_descripcion_pqr_mod").slideUp("slow");
				$("#error_max_descripcion_pqr_mod").slideUp("slow");
				return true;	
			}
		}
	}			
}
/* Fin funcion validar descripcion modificar tipo documento pqr para hacer submit */
/* Funcion para validar mod_termino - Tipo documento PQR*/
function validar_modificar_termino_pqr(){
	var mod_ter_pqr=$("#mod_termino_pqr").val();

	if(mod_ter_pqr==""){
		$("#error_mod_termino_pqr").slideDown("slow");
		return false;
	}else{
		if($("#error_mod_no_es_numero_pqr").is(":visible")){
			return false;
		}else{
			return true;
		}
	}
}
/* Fin funcion para validar mod_termino - Tipo documento PQR*/
/*Funcion para modificar tipo documento PQR - submit */
function validar_modificar_usuario_pqr(){
	var validar_mod_td_pqr =validar_modificar_tipo_doc_pqr();
	if(validar_mod_td_pqr==false){
		$("#tipo_doc_mod_pqr").focus()
		return false;
	}else{
		var validar_mod_desc=validar_modificar_descripcion_pqr();
		if(validar_mod_desc==false){
			$("#descripcion_pqr_mod").focus();
			return false;
		}else{
			var validar_ter_mod_pqr=validar_modificar_termino_pqr();
			if(validar_ter_mod_pqr==false){
				$("#mod_termino_pqr").focus();
				return false;
			}else{
				return true;
			}
		}
	}
}

$(function submit_modificar_tipo_documento_pqr(){
	$('#enviar_mod_td_pqr').click(function submit_modificar_tipo_documento_pqr(){
		if($('.imagen_logo').is(":visible")){
			// sweetAlert({
			Swal.fire({	
				position 			: 'top-end',
			    showConfirmButton 	: false,
			    timer 				: 1500,	
			    title 				: 'La consulta se está ejecutando.',
			    text 				: 'Un momento por favor.',
			    type 				: 'warning'
			});
			return false;
		}else{
			var submit_modificar_td_pqr = validar_modificar_usuario_pqr();
			if(submit_modificar_td_pqr==false){
				return false;
			}else{ // 	$('#formulario_modificar_tipo_documento_pqr').submit(); // Realizar la modificacion del tipo documento
				oculta_mod_errores_pqr();

				var tipo_formulario1=$("#tipo_formulario_modificar_td_pqr").val();
				
				var id_mod1=$("#id_mod_pqr").val();
				var tipo_doc1=$("#tipo_doc_mod_pqr").val();
				var tipo_doc_mod_ant1=$("#tipo_doc_mod_ant_pqr").val();
				var descripcion1=$("#descripcion_pqr_mod").val();
				var termino1=$("#mod_termino_pqr").val();
				var estado1=$("#mod_estado_pqr").val();

				$.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/query_parametrizacion.php',
					data: {
						'tipo_formulario' : tipo_formulario1,
						'id_mod_pqr' : id_mod1,
						'tipo_doc_mod_pqr' : tipo_doc1,
						'tipo_doc_mod_ant_pqr' : tipo_doc_mod_ant1,
						'descripcion_pqr_mod' : descripcion1,
						'mod_termino_pqr' : termino1,
						'mod_estado_pqr' : estado1
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_tipo_doc_mod_pqr').html(resp);
						}
					}
				})	
			}	
		}										
	});
})
/* Fin funcion para modificar tipo documento PQR - submit */
/* Fin funciones formulario modificar tipo documento PQR */
/************************************************************************************************************/
/************************************************************************************************************/
/************************************************************************************************************/
/* Desde aqui funciones formulario agregar Tipo Radicado */
/* Funciones para desplegar ventana modal agregar Tipo Radicado */
function abrir_ventana_crear_tipo_radicado(){
	$("#ventana5").slideDown("slow");
	$("#codigo_tipo_rad").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrar_ventana_crear_tipo_radicado(){
	$("#ventana5").slideUp("slow");
	$(".art_mod").slideUp("slow");

	oculta_errores_tipo_rad();

	$("#codigo_tipo_rad").val("");
	$("#nombre_tipo_rad").val("");
}
/* Fin funciones para desplegar ventana modal agregar Tipo Radicado */
/* Funcion oculta_errores_tipo_rad */
function oculta_errores_tipo_rad(){
	$("#error_codigo_tipo_rad").slideUp("slow");
	$("#error_codigo_tipo_rad_invalido").slideUp("slow");
	$("#error_codigo_tipo_rad_maximo").slideUp("slow");

	$("#error_nombre_tipo_rad").slideUp("slow");
	$("#error_nombre_tipo_rad_minimo").slideUp("slow");
	$("#error_nombre_tipo_rad_maximo").slideUp("slow");
}
/* Fin funcion oculta_errores_tipo_rad */
/* Script tr_existe */
function tr_existe(){
	$("#error_codigo_tipo_rad_invalido").slideDown("slow");
}
/* Fin script tr_existe */
/* Script buscador tipo radicado - Formulario Agregar Nuevo tipo radicado */
$("#codigo_tipo_rad").on("input",function(e){ // Accion que se activa cuando se digita #codigo_tipo_rad
    $('#sugerencias_codigo_tipo_rad').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>"); 				
    oculta_errores_tipo_rad();
    var envio_tipo_radicado = $(this).val();
    
    if($(this).data("lastval")!= envio_tipo_radicado){
    	$(this).data("lastval",envio_tipo_radicado);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {

     		if(envio_tipo_radicado.length>0 && envio_tipo_radicado.length<=2){
        		 $.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/buscador.php',
					data: {
						'search_tipo_radicado' : envio_tipo_radicado
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_codigo_tipo_rad').html(resp);
						}else{
							$('#sugerencias_codigo_tipo_rad').html('');
						}
					}
				})			 		
			}  				 
		},1000);
    };
});
/* Fin script buscador tipo radicado - Formulario Agregar Nuevo tipo radicado */
/* Funcion validar codigo tipo radicado para hacer submit */
function validar_agregar_codigo_tipo_rad(){
	var codigo_tipo_radi =$('#codigo_tipo_rad').val()

	if(codigo_tipo_radi==""){
		$("#error_codigo_tipo_rad").slideDown("slow");
		$("#error_codigo_tipo_rad_invalido").slideUp("slow");
		$("#error_codigo_tipo_rad_maximo").slideUp("slow");
		return false;					
	}else{
		if(codigo_tipo_radi.length>1){
			$("#error_codigo_tipo_rad").slideUp("slow");
			$("#error_codigo_tipo_rad_invalido").slideUp("slow");
			$("#error_codigo_tipo_rad_maximo").slideDown("slow");
			return false;
		}else{
			if($("#art5").is(":visible")){
				$("#error_codigo_tipo_rad").slideUp("slow");
				$("#error_codigo_tipo_rad_invalido").slideDown("slow");
				$("#error_codigo_tipo_rad_maximo").slideUp("slow");
				return false;
			}else{
				$("#error_codigo_tipo_rad").slideUp("slow");
				$("#error_codigo_tipo_rad_invalido").slideUp("slow");
				$("#error_codigo_tipo_rad_maximo").slideUp("slow");
				return true;
			}
		}
	}			
}
/* Fin funcion validar codigo tipo radicado para hacer submit */
/* Funcion validar nombre agregar tipo radicado para hacer submit */
function validar_nombre_tr(){
	var nombre_tr =$('#nombre_tipo_rad').val()
	if(nombre_tr==""){
		$("#error_nombre_tipo_rad").slideDown("slow");
		$("#error_nombre_tipo_rad_minimo").slideUp("slow");
		$("#error_nombre_tipo_rad_maximo").slideUp("slow");
		return false;					
	}else{
		if(nombre_tr.length<3){
			$("#error_nombre_tipo_rad").slideUp("slow");
			$("#error_nombre_tipo_rad_minimo").slideDown("slow");
			$("#error_nombre_tipo_rad_maximo").slideUp("slow");
			return false;
		}else{
			if(nombre_tr.length>12){
				$("#error_nombre_tipo_rad").slideUp("slow");
				$("#error_nombre_tipo_rad_minimo").slideUp("slow");
				$("#error_nombre_tipo_rad_maximo").slideDown("slow");
				return false;
			}else{
				$("#error_nombre_tipo_rad").slideUp("slow");
				$("#error_nombre_tipo_rad_minimo").slideUp("slow");
				$("#error_nombre_tipo_rad_maximo").slideUp("slow");
				return true;	
			}
		}
	}			
}
/* Fin funcion validar nombre agregar tipo radicado para hacer submit */
/* Funcion para insertar tipo radicado - submit */
function validar_tipo_radi(){
	var validar_codi_tr =validar_agregar_codigo_tipo_rad();
	if(validar_codi_tr==false){
		$("#codigo_tipo_rad").focus()
		return false;
	}else{
		var validar_nom_tr=validar_nombre_tr();
		if(validar_nom_tr==false){
			$("#nombre_tipo_rad").focus();
			return false;
		}else{
			return true;
		}
	}
}

$(function submit_agregar_tipo_radicado(){
	$('#enviar_tr').click(function submit_agregar_tipo_radicado(){
		if($('.imagen_logo').is(":visible")){
			// sweetAlert({
			Swal.fire({	
				position 			: 'top-end',
			    showConfirmButton 	: false,
			    timer 				: 1500,	
			    title 				: 'La consulta se está ejecutando.',
			    text 				: 'Un momento por favor.',
			    type 				: 'warning'
			});
		}else{
			var submit_agregar_tipo_radi = validar_tipo_radi();
			if(submit_agregar_tipo_radi==false){
				return false;
			}else{ // 	$('#formulario_agregar_tipo_radicacion').submit(); // Realizar la creación del tipo Radicado
				oculta_errores_tipo_rad();

				var tipo_formulario1=$("#tipo_formulario_crear_tipo_radicado").val();
				var codigo_tipo_rad1=$("#codigo_tipo_rad").val();
				var nombre_tipo_rad1=$("#nombre_tipo_rad").val();

				$.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/query_parametrizacion.php',
					data: {
						'tipo_formulario' : tipo_formulario1,
						'codigo_tipo_rad' : codigo_tipo_rad1,
						'nombre_tipo_rad' : nombre_tipo_rad1
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_codigo_tipo_rad').html(resp);
						}
					}
				})	
			}										
		}
	});
})
/* Fin funcion para insertar tipo radicado - submit */
/* Fin funciones formulario agregar Tipo Radicado
/************************************************************************************************************/

/************************************************************************************************************/
/* Desde aqui funciones formulario crear secuencia */
/* Funciones para desplegar ventana modal crear secuencia */
function abrir_ventana_crear_secuencia(){
	$("#ventana6").slideDown("slow");
	carga_select_tipo_radicado();	
	$("#boton_enviar_sec").html('<input type="button" value="Crear secuencia de la dependencia" id="enviar_sec" class="botones" onclick="submit_agregar_secuencia()">');
	$("#codigo_dependencia_sec").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrar_ventana_crear_secuencia(){
	$("#ventana6").slideUp("slow");
	$("#codigo_dependencia_sec").val("");
	$("#codigo_dependencia_padre_sec").val("");

	$("#select_tr").html("");
	$("#sugerencias_codigo_sec").html("");
	$("#sugerencias_codigo_sec_padre").html("");

	$("#error_consecutivo_invalido").slideUp("slow");
	$(".art_mod").slideUp("slow");

	$(".errores").slideUp("slow");
	// oculta_errores_secuencias();
}
/* Fin funciones para desplegar ventana modal crear secuencia */
/* Funcion oculta_errores_secuencias */
function oculta_errores_secuencias(){
	$("#error_codigo_sec").slideUp("slow");
	$("#error_codigo_sec_invalido").slideUp("slow");
	$("#error_codigo_sec_minimo").slideUp("slow");
	$("#error_codigo_sec_maximo").slideUp("slow");

	$("#error_consecutivo").slideUp("slow");
//	$("#error_consecutivo_invalido").slideUp("slow");

	$("#error_codigo_sec_padre").slideUp("slow");
	$("#error_codigo_sec_padre_invalido").slideUp("slow");
	$("#error_codigo_sec_padre_minimo").slideUp("slow");
	$("#error_codigo_sec_padre_maximo").slideUp("slow");
}
/* Fin funcion oculta_errores_secuencias */
/* Script secuencia_existe */
function secuencia_existe(){
	$("#error_codigo_sec_invalido").slideDown("slow");
}
/* Fin script secuencia_existe */
/* Script para cargar codigo_depe_sec */
function cargar_codigo_depe(codigo_dependencia){
	$("#codigo_dependencia_sec").val(codigo_dependencia);
	$("#sugerencias_codigo_sec").html("");

	$(".errores").slideUp("slow");
	// oculta_errores_secuencias();
}
/* Fin script para cargar codigo_depe_sec */
/* Script para cargar codigo_depe_padre_sec */
function cargar_codigo_depe_padre(codigo_dependencia){
	$("#codigo_dependencia_padre_sec").val(codigo_dependencia);
	$("#sugerencias_codigo_sec_padre").html("");

	$(".errores").slideUp("slow");
	// oculta_errores_secuencias();	
}
/* Fin script para cargar codigo_depe_padre_sec */
/* Script para validar tipo de radicado - codigo de dependencia */
function valida_tipo_rad(){
	var cod_depe=$("#codigo_dependencia_sec").val();
	var tr=$("#tipo_rad").val();

	$.ajax({
		type: 'POST',
		url: 'admin_parametrizacion/buscador.php',
		data: {
			'valida_cod_depe' : cod_depe,
			'valida_tr': tr
		},
		success: function(resp){
			if(resp!=""){
				$("#sugerencias_codigo_sec_padre").html(resp);
			}
		}
	})
}
/* Fin script para validar tipo de radicado - codigo de dependencia */
/* Script para generar el select que contiene los tipos de radicado */
function carga_select_tipo_radicado(){
	$.ajax({
		type: 'POST',
		url: 'admin_parametrizacion/buscador.php',
		data: {
			'search_select_tipo_radicado' : 'NEW' // Valor que se envía para que el select venga pre cargado en valor vacío
		},
		success: function(resp){
			if(resp!=""){
				$('#select_tr').html(resp);
			}else{
				$('#select_tr').html('Error de conexion 404.');
			}
		}
	})
}
/* Fin script para generar el select que contiene los tipos de radicado */
/* Script buscador codigo_dependencia - Formulario crear secuencia */
$("#codigo_dependencia_sec").on("input",function(e){ // Accion que se activa cuando se digita #codigo_dependencia_sec
    loading('sugerencias_codigo_sec');
    espacios_formulario('codigo_dependencia_sec','mayusculas',0)

    $("#error_codigo_sec_invalido").slideUp("slow");
    $(".errores").slideUp("slow");
    var cod_depe_sec = $(this).val();
    
    if($(this).data("lastval")!= cod_depe_sec){
    	$(this).data("lastval",cod_depe_sec);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {

    		$.ajax({
				type: 'POST',
				url: 'admin_parametrizacion/buscador.php',
				data: {
					'search_codigo_depe_sec' : cod_depe_sec
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_codigo_sec').html(resp);
					}else{
						$('#error_codigo_sec_invalido').slideDown('slow');
						$('#sugerencias_codigo_sec').html("");
					}
				}
			})			 		
		},1000);
    };
});
/* Fin script buscador codigo_dependencia - Formulario crear secuencia */
/* Script buscador codigo_dependencia_padre - Formulario crear secuencia */
$("#codigo_dependencia_padre_sec").on("input",function(e){ // Accion que se activa cuando se digita #codigo_dependencia_padre_sec
  	espacios_formulario('codigo_dependencia_padre_sec','mayusculas',0)
    loading('sugerencias_codigo_sec_padre');
    $("#error_codigo_sec_padre_invalido").slideUp("slow");
    $(".errores").slideUp("slow");

    var cod_depe_padre_sec = $(this).val();
    
    if($(this).data("lastval")!= cod_depe_padre_sec){
    	$(this).data("lastval",cod_depe_padre_sec);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
    		$.ajax({
				type: 'POST',
				url: 'admin_parametrizacion/buscador.php',
				data: {
					'search_codigo_depe_padre_sec' : cod_depe_padre_sec
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_codigo_sec_padre').html(resp);
					}else{
						$('#error_codigo_sec_padre_invalido').slideDown('slow');
						$('#sugerencias_codigo_sec_padre').html("");
					}
				}
			})			 				 
		},1000);
    };
});
/* Fin script buscador codigo_dependencia_padre - Formulario crear secuencia */
/* Funcion validar codigo_dependencia para hacer submit */
function validar_agregar_codigo_dependencia(){
	var codigo_depe_sec =$('#codigo_dependencia_sec').val()

	if(codigo_depe_sec==""){
		validar_input('codigo_dependencia_sec');
		return false;					
	}else{
		if(codigo_depe_sec.length<3){
			validar_input('codigo_dependencia_sec');
			return false;
		}else{	
			if(codigo_depe_sec.length>5){
				validar_input('codigo_dependencia_sec');
				return false;
			}else{
				if($("#error_codigo_sec_invalido").is(":visible")){
					return false;
				}else{	
					return true;
				}
			}
		}
	}			
}
/* Fin funcion validar codigo_dependencia para hacer submit */
/* Funcion validar select codigo_dependencia - tipo de radicado para hacer submit */
function validar_select_tr(){
	var select_tr =$('#tipo_rad').val()
	if(select_tr==""){
		$("#error_consecutivo").slideDown("slow");
		return false;					
	}else{
		$("#error_consecutivo").slideUp("slow");

		if($("#error_consecutivo_invalido").is(":visible")){
			return false;
		}else{
			$("#codigo_dependencia_padre_sec").focus();
			return true;
		}
	}
}
/* Fin funcion validar select codigo_dependencia - tipo de radicado para hacer submit */
/* Funcion validar codigo_dependencia_padre para hacer submit */
function validar_agregar_codigo_dependencia_padre(){
	var codigo_depe_padre_sec =$('#codigo_dependencia_padre_sec').val();

	if(codigo_depe_padre_sec==""){
		validar_input('codigo_dependencia_padre_sec');
		return false;					
	}else{
		if(codigo_depe_padre_sec.length<3){
			validar_input('codigo_dependencia_padre_sec');
			return false;
		}else{	
			if(codigo_depe_padre_sec.length>5){
				validar_input('codigo_dependencia_padre_sec');
				return false;
			}else{
				if($("#art7").is(":visible")){
					validar_input('codigo_dependencia_padre_sec');
					return false;
				}else{
					if($("#error_codigo_sec_padre_invalido").is(":visible")){
						return false;
					}else{	
						return true;
					}
				}
			}
		}
	}			
}
/* Fin funcion validar codigo_dependencia_padre para hacer submit */
/* Funcion para insertar secuencia - submit */
function validar_secuencia(){
	var validar_depe_codi =validar_agregar_codigo_dependencia();
	if(validar_depe_codi==false){
		$("#codigo_dependencia_sec").focus()
		return false;
	}else{
		var validar_sel_tr=validar_select_tr();
		if(validar_sel_tr==false){
			$("#select_tr").focus();
			return false;
		}else{
			var valida_depe_padre=validar_agregar_codigo_dependencia_padre();
			if(valida_depe_padre==false){
				$("#codigo_dependencia_padre_sec").focus();
				return false;
			}else{
				return true;
			}
		}
	}
}
function submit_agregar_secuencia(){
	if($('.imagen_logo').is(":visible")){
		// sweetAlert({
		Swal.fire({	
			position 			: 'top-end',
		    showConfirmButton 	: false,
		    timer 				: 1500,	
		    title 				: 'La consulta se está ejecutando.',
		    text 				: 'Un momento por favor.',
		    type 				: 'warning'
		});
	}else{
		var submit_agregar_sec = validar_secuencia();
		if(submit_agregar_sec==false){
			return false;
		}else{ 	//	$('#formulario_agregar_secuencia').submit(); // Realizar la creación de la secuencia
			loading('boton_enviar_sec');

			var tipo_formulario1 				= $("#tipo_formulario_crear_secuencia").val();
			var codigo_dependencia_sec1 		= $("#codigo_dependencia_sec").val();
			var select_tr1 				 		= $("#tipo_rad").val();
			var codigo_dependencia_padre_sec1  	= $("#codigo_dependencia_padre_sec").val();

			$.ajax({
				type: 'POST',
				url: 'admin_parametrizacion/query_parametrizacion.php',
				data: {
					'tipo_formulario' 				: tipo_formulario1,
					'codigo_dependencia_sec' 		: codigo_dependencia_sec1,
					'tipo_rad' 						: select_tr1,
					'codigo_dependencia_padre_sec' 	: codigo_dependencia_padre_sec1
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_codigo_sec').html(resp);
					}
				}
			})	
		}										
	}
}
/* Fin funcion para insertar secuencia - submit */
/* Fin funciones formulario crear secuencia 
/************************************************************************************************************/
/* Desde aqui funciones formulario modificar secuencia */
/* Script para generar el select que contiene los tipos de radicado - Formulario modificar secuencia */
function carga_select_mod_tipo_radicado(tipo_rad){
	$.ajax({
		type: 'POST',
		url: 'admin_parametrizacion/buscador.php',
		data: {
			'search_select_tipo_radicado' : tipo_rad
		},
		success: function(resp){
			if(resp!=""){
				$('#select_tr_mod').html(resp);
			}else{
				$('#select_tr_mod').html('Error de conexion Jonas (202).');
			}
		}
	})
}
/* Fin script para generar el select que contiene los tipos de radicado - Formulario modificar secuencia */
/* Funcion para cargar formulario para modificar secuencia */
function cargar_modifica_secuencia(codigo_dependencia_sec,tipo_radicado,codigo_dependencia_padre_sec,nombre_dependencia,nombre_secuencia){
	//carga_select_mod_tipo_radicado(tipo_radicado); // Carga el select con los tipos de radicado
	
	$("#codigo_dependencia_sec_mod_ant").val(codigo_dependencia_sec);
	$("#codigo_dependencia_sec_mod").val(codigo_dependencia_sec);
	$("#tipo_radicado_sec_mod_ant").val(tipo_radicado);
	$("#tipo_documento_sec_mod").val(tipo_radicado);

	/* Se agrega el title del (codigo) nombre_dependencia */
	$("#codigo_dependencia_sec_mod").attr('title', '('+codigo_dependencia_sec+') '+nombre_dependencia);

	/* Se agrega el title del tipo de codigo */
	$("#tipo_documento_sec_mod").attr('title', nombre_secuencia);


	$("#codigo_dependencia_padre_sec_mod").val(codigo_dependencia_padre_sec);
	
	abrir_ventana_modificar_secuencia();
} 
/* Fin funcion para cargar formulario para modificar secuencia */
/* Funciones para desplegar ventana modal - Formulario modificar secuencia */
function abrir_ventana_modificar_secuencia(){
	$("#ventana7").slideDown("slow");
	$("#codigo_dependencia_padre_sec_mod").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrar_ventana_modificar_secuencia(){
	$("#ventana7").slideUp("slow");

	oculta_mod_errores_secuencia();
}
/* Fin funciones para desplegar ventana modal - Formulario modificar secuencia */
/* Funcion oculta_mod_errores_secuencia */
function oculta_mod_errores_secuencia(){
	$("#error_codigo_dependencia_padre_sec_mod").slideUp("slow");
	$("#error_codigo_dependencia_padre_sec_invalido_mod").slideUp("slow");
	$("#error_codigo_dependencia_padre_sec_minimo_mod").slideUp("slow");
	$("#error_codigo_dependencia_padre_sec_maximo_mod").slideUp("slow");
}
/* Fin funcion oculta_mod_errores_secuencia */
/* Script buscador codigo_dependencia_padre - Formulario modificar secuencia */
$("#codigo_dependencia_padre_sec_mod").on("input",function(e){ // Accion que se activa cuando se digita #codigo_dependencia_padre_sec_mod
    $('#sugerencias_codigo_dependencia_padre_sec_mod').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>"); 				
    oculta_mod_errores_secuencia();
    var cod_depe_padre_sec = $(this).val();
    
    if($(this).data("lastval")!= cod_depe_padre_sec){
    	$(this).data("lastval",cod_depe_padre_sec);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {

     		if(cod_depe_padre_sec.length>0 && cod_depe_padre_sec.length<=5){
        		 $.ajax({
					type: 'POST',
					url: 'admin_parametrizacion/buscador.php',
					data: {
						'search_codigo_depe_padre_sec_mod' : cod_depe_padre_sec
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_codigo_dependencia_padre_sec_mod').html(resp);
						}else{
							$('#error_codigo_dependencia_padre_sec_invalido_mod').slideDown('slow');
							$('#sugerencias_codigo_dependencia_padre_sec_mod').html("");
						}
					}
				})			 		
			}else{
				if(cod_depe_padre_sec.length>5){
					$('#error_codigo_dependencia_padre_sec_maximo_mod').slideDown('slow');
					$('#sugerencias_codigo_dependencia_padre_sec_mod').html("");
				}else{
					$('#error_codigo_dependencia_padre_sec_minimo_mod').slideDown("slow");
					return false;
				}  				 
			}  				 
		},1000);
    };
});
/* Fin script buscador codigo_dependencia_padre - Formulario modificar secuencia */
/* Script para cargar codigo_depe_sec - Formulario modificar secuencia */
function cargar_codigo_depe_padre_mod(codigo_dependencia){
	$("#codigo_dependencia_padre_sec_mod").val(codigo_dependencia);
	$("#sugerencias_codigo_dependencia_padre_sec_mod").html("");
	oculta_mod_errores_secuencia();
}
/* Fin script para cargar codigo_depe_sec - Formulario modificar secuencia */

/* Funcion validar codigo_dependencia_padre para hacer submit - Formulario modificar secuencia */
function validar_modificar_codigo_dependencia_padre(){
	var codigo_depe_padre_sec_mod =$('#codigo_dependencia_padre_sec_mod').val();

	if(codigo_depe_padre_sec_mod==""){
		$("#error_codigo_dependencia_padre_sec_mod").slideDown("slow");
		$("#error_codigo_dependencia_padre_sec_invalido_mod").slideUp("slow");
		$("#error_codigo_dependencia_padre_sec_minimo_mod").slideUp("slow");
		$("#error_codigo_dependencia_padre_sec_maximo_mod").slideUp("slow");
		return false;					
	}else{
		if(codigo_depe_padre_sec_mod.length<3){
			$("#error_codigo_dependencia_padre_sec_mod").slideUp("slow");
			$("#error_codigo_dependencia_padre_sec_invalido_mod").slideUp("slow");
			$("#error_codigo_dependencia_padre_sec_minimo_mod").slideDown("slow");
			$("#error_codigo_dependencia_padre_sec_maximo_mod").slideUp("slow");
			return false;
		}else{	
			if(codigo_depe_padre_sec_mod.length>5){
				$("#error_codigo_dependencia_padre_sec_mod").slideUp("slow");
				$("#error_codigo_dependencia_padre_sec_invalido_mod").slideUp("slow");
				$("#error_codigo_dependencia_padre_sec_minimo_mod").slideUp("slow");
				$("#error_codigo_dependencia_padre_sec_maximo_mod").slideDown("slow");
				return false;
			}else{
				if($("#art8").is(":visible")){
					$("#error_codigo_dependencia_padre_sec_mod").slideUp("slow");
					$("#error_codigo_dependencia_padre_sec_minimo_mod").slideUp("slow");
					$("#error_codigo_dependencia_padre_sec_maximo_mod").slideUp("slow");
					return false;
				}else{
					if($(".errores").is(":visible")){
						return false;
					}else{
						$("#error_codigo_dependencia_padre_sec_mod").slideUp("slow");
						$("#error_codigo_dependencia_padre_sec_invalido_mod").slideUp("slow");
						$("#error_codigo_dependencia_padre_sec_minimo_mod").slideUp("slow");
						$("#error_codigo_dependencia_padre_sec_maximo_mod").slideUp("slow");	
						return true;
					}
				}
			}
		}
	}			
}
/* Fin funcion validar codigo_dependencia_padre para hacer submit - Formulario modificar secuencia */
/* Funcion para modificar secuencia - submit */
function validar_mod_secuencia(){
	var validar_depe_padre_mod_codi =validar_modificar_codigo_dependencia_padre();
	if(validar_depe_padre_mod_codi==false){
		$("#codigo_dependencia_padre_sec_mod").focus()
		return false;
	}else{
		return true;
	}
}
function submit_modificar_secuencia(){
	if($('.imagen_logo').is(":visible")){
		// sweetAlert({
		Swal.fire({	
			position 			: 'top-end',
		    showConfirmButton 	: false,
		    timer 				: 1500,	
		    title 				: 'La consulta se está ejecutando.',
		    text 				: 'Un momento por favor.',
		    type 				: 'warning'
		});
	}else{
		var submit_modificar_sec = validar_mod_secuencia();
		if(submit_modificar_sec==false){
			return false;
		}else{		//	$('#formulario_modificar_consecutivo').submit(); // Realizar la modificacion de la secuencia
		
			var tipo_formulario1=$("#tipo_formulario_modificar_consecutivo").val();
			var codigo_dependencia_sec1=$("#codigo_dependencia_sec_mod_ant").val();
			var select_tr1=$("#tipo_radicado_sec_mod_ant").val();
			var codigo_dependencia_padre_sec1=$("#codigo_dependencia_padre_sec_mod").val();

			$.ajax({
				type: 'POST',
				url: 'admin_parametrizacion/query_parametrizacion.php',
				data: {
					'tipo_formulario' : tipo_formulario1,
					'codigo_dependencia_sec_mod_ant' : codigo_dependencia_sec1,
					'tipo_radicado_sec_mod_ant' : select_tr1,
					'codigo_dependencia_padre_sec_mod' : codigo_dependencia_padre_sec1
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_codigo_dependencia_padre_sec_mod').html(resp);
					}
				}
			})	
		}										
	}
}

/* Fin funcion para modificar secuencia - submit */

/* Fin funciones formulario modificar secuencia */
/************************************************************************************************************/
/************************************************************************************************************/

function volver(){
	carga_administrador_parametrizacion();
}		
/************************************************************************************************************/