/************************************************************************************************************/
/* Buscador - Administrador de Municipios********************************************************************/
/************************************************************************************************************/
/*Funciones para limpiar el formulario*/
function limpia_formulario_agregar(){
	$('#pais').val("");
	$('#departamento').val("");
	$('#municipio').val("");

	$(".art").slideUp("slow");
	$('.errores').slideUp("slow");

	$("#pais").focus();
}
/*Fin funciónes para limpiar el formulario*/

/*Funciones para desplegar ventana modal municipios*/
function abrirVentanaCrearMunicipios(){
	$("#ventana").slideDown("slow");
	limpia_formulario_agregar();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
/*Fin funciones para desplegar ventana modal municipios*/

/*Script buscador - Administrador de Municipios*/
var timerid="";
$(function buscador_municipios(){
	$('#search').focus();	
	$('#search').on("input",function(e){ // Accion que se activa cuando se digita #search
		loading('desplegable_resultados');
		// $('#desplegable_resultados').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
		var envio = $(this).val();
		
		if($(this).data("lastval")!= envio){
	    	$(this).data("lastval",envio);
                    
   			clearTimeout(timerid);
   			timerid = setTimeout(function() {
         		if(envio.length>2 && envio.length<50){		
					$('#logo').html('<h1>Buscador de Municipios</h1>');

					$.ajax({
						type 	: 'POST',
						url 	: 'admin_muni/buscador_municipios.php',
						data 	: {
							'search'  		: envio,
							'formulario' 	: 'crear_municipio'
						},			
						success: function(resp){
							if(resp!=""){
								$('#desplegable_resultados').html(resp);
							}
						}
					})			 		
				}else{
					$('#desplegable_resultados').html("<div style='background: red; border-radius:10px; color:#FFFFFF; font-size:12px; padding: 5px; text-align:center;'>La búsqueda debe tener por lo menos 3 caracteres. Revise por favor.</div>");
				} 
				if(envio.length>50){
					$('#desplegable_resultados').html("<div style='background: red; border-radius:10px; color:#FFFFFF; font-size:12px; padding: 5px; text-align:center;'>La busqueda debe tener 50 caracteres maximo. Revise por favor</div>");
				}  				 
   			},1000);
	    };
	})
})
/*Fin script buscador - Administrador de Municipios*/
/*Script para buscador de Pais - Formulario Agregar Nuevo Municipio */
$("#pais").on("input",function(e){ // Accion que se activa cuando se digita #pais
	loading('sugerencia_pais');
	validar_pais();

	var envio_continente 	= $("#continente").val();
    var envio_pais 			= $(this).val();
    
    if($(this).data("lastval")!= envio_pais){
    	$(this).data("lastval",envio_pais);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			
			$.ajax({
				type: 'POST',
				url: 'admin_muni/buscador_municipios.php',
				data: {
					'search_pais' 		: envio_pais,
					'search_continente' : envio_continente
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_pais').html(resp);
					}
				}
			})		 						 
		},1000);
    };
});
/*Fin script para buscador de Pais - Formulario Agregar Nuevo Municipio */
/*Script para buscador de departamento - Formulario Agregar Nuevo Municipio */
$("#departamento").on("input",function(e){ // Accion que se activa cuando se digita #departamento
	loading("sugerencia_departamento");
	validar_departamento();

    $('.errores').slideUp("slow");
	var envio_pais 			= $("#pais").val();
	var envio_departamento 	= $(this).val();
    
    if($(this).data("lastval")!= envio_departamento){
    	$(this).data("lastval",envio_departamento);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
				
			$.ajax({
				type: 'POST',
				url: 'admin_muni/buscador_municipios.php',
				data: {
					'search_departamento' 	: envio_departamento,
					'search_pais2' 			: envio_pais
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_departamento').html(resp);
					}
				}
			}) 						 
		},1000);
    };
});
/*Fin script para buscador de departamento - Formulario Agregar Nuevo Municipio */
/*Script para buscador de municipio - Formulario Agregar Nuevo Municipio */
$("#municipio").on("input",function(e){ // Accion que se activa cuando se digita #municipio
	loading('municipio');
	validar_municipio();

	var envio_municipio 	= $(this).val();
	var envio_departamento 	= $('#departamento').val();
	
    if($(this).data("lastval")!= envio_municipio){
    	$(this).data("lastval",envio_municipio);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
		
			$.ajax({
				type: 'POST',
				url: 'admin_muni/buscador_municipios.php',
				data: {
					'search_departamento2' 	: envio_departamento,
					'search_municipio'		: envio_municipio
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_municipio').html(resp);
					}
				}
			})					 
		},1000);
    };
});
/*Fin script para buscador de municipio - Formulario Agregar Nuevo Municipio */
/* Script cargar pais - Formulario Agregar Nuevo Municipio */
function cargar_pais(nombre_pais){
	$(".art").slideUp("slow");
	$(".errores").slideUp("slow");

	// oculta_errores();

	$("#pais").val(nombre_pais);
	$("#departamento").val("");
	$("#municipio").val("");
	$("#departamento").focus();
}
/* Fin script cargar pais - Formulario Agregar Nuevo Municipio */
/* Script cargar departamento - Formulario Agregar Nuevo Municipio */
function cargar_departamento(nombre_departamento){
	$(".art").slideUp("slow");
	$(".errores").slideUp("slow");

	// oculta_errores();

	$("#departamento").val(nombre_departamento);
	$("#municipio").val("");
	$("#municipio").focus();
}
/* Fin script cargar departamento - Formulario Agregar Nuevo Municipio */
/* Script cargar municipio - Formulario Agregar Nuevo Municipio */
function error_municipio_ya_existe(){
	$(".errores").slideUp("slow");
	$("#municipio_ya_existe").slideDown();
	$("#municipio").focus(); 
}
/* Fin script cargar municipio - Formulario Agregar Nuevo Municipio */
/* Script para ocultar errores y continuar consulta - Formulario Agregar Nuevo Municipio */
// function oculta_errores(){
	// $("#error_pais").slideUp("slow");
	// $("#error_pais_minimo").slideUp("slow");
	// $("#error_pais_maximo").slideUp("slow");
	// $("#error_pais_invalido").slideUp("slow");

	// $("#error_departamento").slideUp("slow");
	// $("#error_departamento_minimo").slideUp("slow");
	// $("#error_departamento_maximo").slideUp("slow");
	// $("#error_departamento_invalido").slideUp("slow");

	// $("#error_municipio").slideUp("slow");
	// $("#error_municipio_minimo").slideUp("slow");
	// $("#error_municipio_maximo").slideUp("slow");
	// $("#error_municipio_invalido").slideUp("slow");
// }
/* Fin script para ocultar errores y continuar consulta - Formulario Agregar Nuevo Municipio */
/* Script espacios departamento - Formulario Agregar Nuevo Municipio - Modificar Municipio*/
function espacios_formulario_municipio(input){
	switch(input){
		case 'search':
			var str = $('#search').val();
			break;	
		// case 'pais':
		// 	var str = $('#pais').val();
		// 	break;			
		// case 'departamento':
		// 	var str = $('#departamento').val();	
		// 	break;
		// case 'municipio':
		// 	var str = $('#municipio').val();
		// 	break;
		// case 'mod_pais':
		// 	var str = $('#mod_pais').val();
		// 	break;
		case 'mod_departamento':
			var str = $('#mod_departamento').val();	
			break;
		case 'mod_municipio':
			var str = $('#mod_municipio').val();	
			break;				
	}

	str = str.replace('-',''); 	str = str.replace('°','');	str = str.replace('!','');
	str = str.replace('|','');	str = str.replace('"','');	str = str.replace('$','');
	str = str.replace('#','');	str = str.replace('%','');	str = str.replace('&','');
	str = str.replace('=','');	str = str.replace('?','');	str = str.replace('¿','');
	str = str.replace('¡','');	str = str.replace('(','');	str = str.replace(')','');
	str = str.replace('{','');	str = str.replace('}','');	str = str.replace('[','');
	str = str.replace(']','');	str = str.replace('.','');	str = str.replace(';','');
	str = str.replace(':','');	str = str.replace('_','');	str = str.replace('~','');
	str = str.replace('@','');	str = str.replace('´','');	str = str.replace("+",'');
	str = str.replace("/","");	str = str.replace("*","");	str = str.replace("'","");
	str = str.replace(',','');	str = str.replace('^','');	str = str.replace('ñ','N');
	str = str.replace('Ñ','N');	str = str.replace('á','A');	str = str.replace('é','E');
	str = str.replace('í','I');	str = str.replace('ó','O');	str = str.replace('ú','U');
	str = str.replace('Á','A');	str = str.replace('É','E');	str = str.replace('Í','I');
	str = str.replace('Ó','O');	str = str.replace('Ú','U');	str = str.replace('<','');
	str = str.replace('>','');	str = str.replace('  ','');

	switch(input){
		// case 'search':
		// 	$('#search').val(str.toUpperCase());
		// 	break;
		// case 'pais':
		// 	$('#pais').val(str.toUpperCase());
		// 	break;	
		// case 'departamento':
		// 	$('#departamento').val(str.toUpperCase());
		// 	break;
		// case 'municipio':
		// 	$('#municipio').val(str.toUpperCase());
		// 	break;
		// case 'mod_pais':
		// 	$('#mod_pais').val(str.toUpperCase());
		// 	break;
		case 'mod_departamento':
			$('#mod_departamento').val(str.toUpperCase());
			break;	
		case 'mod_municipio':
			$('#mod_municipio').val(str.toUpperCase());
			break;		
	}			
}			
/* Fin script espacios departamento - Formulario Agregar Nuevo Municipio - Modificar Municipio */
/* Funcion para validar Pais */
function validar_pais(){
	// espacios_formulario_municipio('pais');
	validar_input('pais');
	var pais =$('#pais').val();

	if(pais==""){
		$("#pais_max").slideUp("slow");
		$("#pais_min").slideUp("slow");
		$("#pais_null").slideDown("slow");
		$("#error_pais_invalido").slideUp("slow");
		return false;
	}else{
		if(pais.length<3){
			$("#pais_max").slideUp("slow");
			$("#pais_min").slideDown("slow");
			$("#pais_null").slideUp("slow");
			$("#error_pais_invalido").slideUp("slow");
			return false;
		}else{
			if(pais.length>30){
				$("#pais_max").slideDown("slow");
				$("#pais_min").slideUp("slow");
				$("#pais_null").slideUp("slow");
				$("#error_pais_invalido").slideUp("slow");
				return false;
			}else{
				if($("#resultado_pais").is(":visible")){
					$("#pais_max").slideUp("slow");
					$("#pais_min").slideUp("slow");
					$("#pais_null").slideUp("slow");
					$("#error_pais_invalido").slideDown("slow");
					return false;
				}else{
					$("#pais_max").slideUp("slow");
					$("#pais_min").slideUp("slow");
					$("#pais_null").slideUp("slow");
					$("#error_pais_invalido").slideUp("slow");
					return true;
				}	
			}
		}
	}		
}
/* Fin funcion para validar Pais */
/* Funcion para validar Departamento */
function validar_departamento(){
	// espacios_formulario_municipio('departamento');
	validar_input('departamento');
	var departamento =$('#departamento').val()
	
	if(departamento==""){
		$("#departamento_null").slideDown("slow");
		$("#departamento_min").slideUp("slow");
		$("#departamento_max").slideUp("slow");
		$("#error_departamento_invalido").slideUp("slow");
		return false;
	}else{
		if(departamento.length<3){
			$("#departamento_null").slideUp("slow");
			$("#departamento_min").slideDown("slow");
			$("#departamento_max").slideUp("slow");
			$("#error_departamento_invalido").slideUp("slow");
			return false;
		}else{
			if(departamento.length>50){
				$("#departamento_null").slideUp("slow");
				$("#departamento_min").slideUp("slow");
				$("#departamento_max").slideDown("slow");
				$("#error_departamento_invalido").slideUp("slow");
				return false;
			}else{
				if($("#resultado_departamento").is(":visible")){
					$("#departamento_null").slideUp("slow");
					$("#departamento_min").slideUp("slow");
					$("#departamento_max").slideUp("slow");
					$("#error_departamento_invalido").slideDown("slow");
					return false;
				}else{
					$("#departamento_null").slideUp("slow");
					$("#departamento_min").slideUp("slow");
					$("#departamento_max").slideUp("slow");
					$("#error_departamento_invalido").slideUp("slow");
					return true;
				}
			}
		}
	}	
}
/* Fin funcion para validar Departamento */
/* Funcion para validar Municipio */
function validar_municipio(){
	// espacios_formulario_municipio('municipio');
	validar_input('municipio');
	var municipio =$('#municipio').val();

	if(municipio==""){
		$("#municipio_null").slideDown("slow");
		$("#municipio_min").slideUp("slow");
		$("#municipio_max").slideUp("slow");
		$("#error_municipio_invalido").slideUp("slow");
		$("#municipio_ya_existe").slideUp("slow");
		return false;
	}else{
		if(municipio.length<3){
			$("#municipio_null").slideUp("slow");
			$("#municipio_min").slideDown("slow");
			$("#municipio_max").slideUp("slow");
			$("#error_municipio_invalido").slideUp("slow");
			$("#municipio_ya_existe").slideUp("slow");
			return false;
		}else{
			if(municipio.length>50){
				$("#municipio_null").slideUp("slow");
				$("#municipio_min").slideUp("slow");
				$("#municipio_max").slideDown("slow");
				$("#error_municipio_invalido").slideUp("slow");
				$("#municipio_ya_existe").slideUp("slow");
				return false;
			}else{
				if($("#resultado_municipio").is(":visible")){
					$("#municipio_null").slideUp("slow");
					$("#municipio_min").slideUp("slow");
					$("#municipio_max").slideUp("slow");
					$("#error_municipio_invalido").slideDown("slow");
					$("#municipio_ya_existe").slideUp("slow");
					return false;
				}else{
					if($("#municipio_ya_existe").is(":visible")){
						$("#municipio_null").slideUp("slow");
						$("#municipio_min").slideUp("slow");
						$("#municipio_max").slideUp("slow");
						$("#error_municipio_invalido").slideUp("slow");
						return false;
					}else{
						$("#municipio_null").slideUp("slow");
						$("#municipio_min").slideUp("slow");
						$("#municipio_max").slideUp("slow");
						$("#error_municipio_invalido").slideUp("slow");
						$("#municipio_ya_existe").slideUp("slow");
						return true;
					}
				}
			}
		}
	}
}
/* Fin funcion para validar Municipio */
/*Funcion para insertar datos de municipios*/
function validar_agregar_municipio(){
	var validar_p=validar_pais();
	if(validar_p==false){
		$("#pais").focus();
		return false;
	}else{
		var validar_dep=validar_departamento();
		if(validar_dep==false){
			$('#departamento').focus();
			return false;
		}else{
			var validar_mun=validar_municipio();
			if(validar_mun==false){
				$('#municipio').focus();
				return false;
			}else{
				return true;
			}
		}
	}
}

$(function submit_agregar_municipio(){
	$('#bEnviar').click(function submit_agregar_municipio(){
		if($('.imagen_logo').is(":visible")){
			Swal.fire({	
				position 			: 'top-end',
			    showConfirmButton 	: false,
			    timer 				: 1500,	
			    title 				: 'La consulta se está ejecutando.',
			    text 				: 'Un momento por favor.',
			    type 				: 'warning'
			});
		}else{
			var submit_agregar_municipio = validar_agregar_municipio();
	
			if(submit_agregar_municipio==false || $(".errores").is(":visible")){
				return false;
			}else{	// Realizar la creación del municipio
				loading('boton_grabar_municipio');

				var tipo_formulario1 	= $("#tipo_formulario").val();
				var continente1 		= $("#continente").val();
				var pais1 				= $("#pais").val();
				var departamento1 		= $("#departamento").val();
				var municipio1 			= $("#municipio").val();
				
				$.ajax({
					type: 'POST',
					url: 'admin_muni/query_municipios.php',
					data: {
						'tipo_formulario' 	: tipo_formulario1,
						'continente' 		: continente1,
						'pais' 				: pais1,
						'departamento'  	: departamento1,
						'municipio'  		: municipio1
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencia_pais').html(resp);
						}
					}
				})
			}										
		}									
	});
})
/*Fin funcion para insertar datos de municipios*/
/******************************************************************************************/
/* Modificar Municipios *******************************************************************/
/******************************************************************************************/
/*Funciones para desplegar ventana modal Modificar Municipio*/
function abrirVentanaModificarMunicipios(){
	$("#ventana2").slideDown("slow");
	$("#mod_pais").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrarVentanaModificarMunicipios(){
	$("#boton_modificar_municipio").html("<input type='button' value='Modificar Municipio' id='enviar_mod' class='botones2'>")
	cerrar_ventanas_modal();
}
/*Fin funciones para desplegar ventana modal Modificar Municipio*/

/* Función para cargar datos al formulario de Modificación de Municipio */
function cargar_modifica_municipio(id,nombre_municipio,nombre_departamento,nombre_pais,nombre_continente){
	$('#id_municipio').val(id);
	$('#ant_continente').val(nombre_continente);
	$('#ant_pais').val(nombre_pais);
	$('#ant_departamento').val(nombre_departamento);
	$('#ant_municipio').val(nombre_municipio);

	$('#mod_continente').val(nombre_continente);
	$('#mod_pais').val(nombre_pais);
	$('#mod_departamento').val(nombre_departamento);
	$('#mod_municipio').val(nombre_municipio);

	$('#sugerencia_mod_pais').html('');
	$('#sugerencia_mod_departamento').html('');
	$('#sugerencia_mod_municipio').html('');

	$('.errores').slideUp('slow');
/*
	$("#error_mod_departamento").slideUp("slow");
	$("#error_mod_departamento_minimo").slideUp("slow");
	$("#error_mod_departamento_maximo").slideUp("slow");
	$("#error_mod_departamento_invalido").slideUp("slow");
	
	$("#error_mod_municipio").slideUp("slow");
	$("#error_mod_municipio_minimo").slideUp("slow");
	$("#error_mod_municipio_maximo").slideUp("slow");
	$("#error_mod_municipio_invalido").slideUp("slow");
*/
	abrirVentanaModificarMunicipios();
}
/*Script para buscador de Pais - Formulario Modificar Municipio */
$("#mod_pais").on("input",function(e){ // Accion que se activa cuando se digita #pais
	loading('sugerencia_mod_pais');

	var envio_mod_continente 	= $("#mod_continente").val();
	var ant_pais 				= $("#ant_pais").val();
	var envio_mod_pais 			= $(this).val();
    
    if($(this).data("lastval")!= envio_mod_pais){
    	$(this).data("lastval",envio_mod_pais);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_mod_pais();

			$.ajax({
				type: 'POST',
				url: 'admin_muni/buscador_municipios.php',
				data: {
					'search_mod_continente' : envio_mod_continente,
					'search_ant_pais' 		: ant_pais,
					'search_mod_pais' 		: envio_mod_pais
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_mod_pais').html(resp);
					}
				}
			})	 								 
		},1000);
    };
});
/*Fin script para buscador de Pais - Formulario Modificar Municipio */
/*Script para buscador Departamento - Formulario Modificar Municipio */
$("#mod_departamento").on("input",function(e){ // Accion que se activa cuando se digita #departamento
	loading('sugerencia_mod_departamento');
    // $('#sugerencia_mod_departamento').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>"); 				
	// oculta_mod_errores();

	var envio_mod_pais2  		= $("#mod_pais").val();
	var ant_departamento 		= $("#ant_departamento").val();
	var envio_mod_departamento 	= $(this).val();
    
    if($(this).data("lastval")!= envio_mod_departamento){
    	$(this).data("lastval",envio_mod_departamento);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_mod_departamento();
     		// if(envio_mod_departamento.length>2 && envio_mod_departamento.length<50){
				$.ajax({
					type: 'POST',
					url: 'admin_muni/buscador_municipios.php',
					data: {
						'search_mod_pais2' : envio_mod_pais2,
						'search_mod_departamento': envio_mod_departamento,
						'search_ant_departamento': ant_departamento
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencia_mod_departamento').html(resp);
						}
					}
				})	
/*			}else{
				$('#sugerencia_mod_departamento').html('');
			}	
			if(envio_mod_departamento.length>50){
				$('#sugerencia_mod_departamento').html("<div style='background: red; border-radius:10px; color:#FFFFFF; font-size:12px; padding: 5px; text-align:center;'>La busqueda debe tener 50 caracteres maximo. Revise por favor</div>");
			}  				 
*/
		},1000);
    };
});
/*Fin script para buscador Departamento - Formulario Modificar Municipio */
/*Script para buscador municipio - Formulario Modificar Municipio */
$("#mod_municipio").on("input",function(e){ // Accion que se activa cuando se digita #departamento
    $('#sugerencia_mod_municipio').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>"); 				
	oculta_mod_errores();

	var envio_mod_departamento2 = $('#mod_departamento').val();
	var ant_municipio=$("#ant_municipio").val();
	var envio_mod_municipio=$(this).val();

    if($(this).data("lastval")!= envio_mod_municipio){
    	$(this).data("lastval",envio_mod_municipio);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
     		if(envio_mod_municipio.length>2 && envio_mod_municipio.length<50){	
				$.ajax({
					type: 'POST',
					url: 'admin_muni/buscador_municipios.php',
					data: {
						'search_mod_departamento2' : envio_mod_departamento2,
						'search_mod_municipio': envio_mod_municipio,
						'search_ant_municipio': ant_municipio
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencia_mod_municipio').html(resp);
						}
					}
				})
			}else{
				$('#sugerencia_mod_municipio').html('');
			}	
			if(envio_mod_municipio.length>50){
				$('#sugerencia_mod_municipio').html("<div style='background: red; border-radius:10px; color:#FFFFFF; font-size:12px; padding: 5px; text-align:center;'>La busqueda debe tener 50 caracteres maximo. Revise por favor</div>");
			}  				 
		},1000);
    };
});
/*Fin script para buscador municipio - Formulario Modificar Municipio */
/* Script cargar pais - Formulario Modificar Municipio */
function cargar_mod_pais(nombre_mod_pais){
	$(".art").slideUp("slow");

	oculta_mod_errores();

	$("#mod_pais").val(nombre_mod_pais);
	$("#mod_departamento").val("");
	$("#mod_municipio").val("");
	$("#mod_departamento").focus();
}
/* Fin script cargar pais - Formulario Modificar Municipio */
/* Script cargar departamento - Formulario Modificar Municipio */
function cargar_mod_departamento(nombre_mod_departamento){
	$(".art").slideUp("slow");

	oculta_mod_errores();

	$("#mod_departamento").val(nombre_mod_departamento);
	$("#mod_municipio").focus();
}
/* Fin script cargar departamento - Formulario Modificar Municipio */
/* Script cargar municipio - Formulario Modificar Municipio */
function cargar_mod_municipio(nombre_mod_municipio){
	$(".art").slideUp("slow");

	oculta_mod_errores();

	$("#mod_municipio").val(nombre_mod_municipio);
	$("#mod_municipio").focus();
}
/* Fin script cargar municipio - Formulario Modificar Municipio */
/* Script para ocultar errores y continuar consulta - Formulario Modificar Municipio */
function oculta_mod_errores(){
	$("#error_mod_pais").slideUp("slow");
	$("#error_mod_pais_minimo").slideUp("slow");
	$("#error_mod_pais_maximo").slideUp("slow");
	$("#error_mod_pais_invalido").slideUp("slow");

	$("#error_mod_departamento").slideUp("slow");
	$("#error_mod_departamento_minimo").slideUp("slow");
	$("#error_mod_departamento_maximo").slideUp("slow");
	$("#error_mod_departamento_invalido").slideUp("slow");

	$("#error_mod_municipio").slideUp("slow");
	$("#error_mod_municipio_minimo").slideUp("slow");
	$("#error_mod_municipio_maximo").slideUp("slow");
	$("#error_mod_municipio_invalido").slideUp("slow");

	$('.art').slideUp("slow");
}
/* Fin script para ocultar errores y continuar consulta - Formulario Modificar Municipio */

/* Funcion para limpiar el formulario */
function limpia_formulario_modificacion(){
	$('#mod_pais').val("");
	$('#mod_departamento').val("");
	$('#mod_municipio').val("");

	$('.art').slideUp("slow");

	$("#error_mod_pais").slideUp("slow");
	$("#error_mod_pais_minimo").slideUp("slow");
	$("#error_mod_pais_maximo").slideUp("slow");
	$("#error_mod_pais_invalido").slideUp("slow");

	$("#error_mod_departamento").slideUp("slow");
	$("#error_mod_departamento_minimo").slideUp("slow");
	$("#error_mod_departamento_maximo").slideUp("slow");
	$("#error_mod_departamento_invalido").slideUp("slow");
	
	$("#error_mod_municipio").slideUp("slow");
	$("#error_mod_municipio_minimo").slideUp("slow");
	$("#error_mod_municipio_maximo").slideUp("slow");
	$("#error_mod_municipio_invalido").slideUp("slow");

	$("#mod_pais").focus();
}
/* Fin funcion para limpiar el formulario */
/* Funcion para validar mod_pais */
function validar_mod_pais(){
	// espacios_formulario_municipio('pais');
	// espacios_formulario_municipio('mod_pais');
	validar_input('mod_pais');

	var ant_pais 	= $('#ant_pais').val();
	var x  			= $('#mod_pais').val();

	if(x== ""){
		$("#error_mod_pais_invalido").slideUp("slow");
		$("#mod_pais_max").slideUp("slow");
		$("#mod_pais_min").slideUp("slow");
		$("#mod_pais_null").slideDown("slow");
		return false;
	}else{
		if(x.length<3){
			$("#error_mod_pais_invalido").slideUp("slow");
			$("#mod_pais_max").slideUp("slow");
			$("#mod_pais_min").slideDown("slow");
			$("#mod_pais_null").slideUp("slow");
			return false;
		}else{
			if(x.length>30){
				$("#error_mod_pais_invalido").slideUp("slow");
				$("#mod_pais_max").slideDown("slow");
				$("#mod_pais_min").slideUp("slow");
				$("#mod_pais_null").slideUp("slow");
				return false;
			}else{
				if($('#art_pais').is(":visible")){/* Si hay un resultado visible, genera error */
					$("#error_mod_pais_invalido").slideDown("slow");
					$("#mod_pais_max").slideUp("slow");
					$("#mod_pais_min").slideUp("slow");
					$("#mod_pais_null").slideUp("slow");
					return false;
				}else{
					$("#error_mod_pais_invalido").slideUp("slow");
					$("#mod_pais_max").slideUp("slow");
					$("#mod_pais_min").slideUp("slow");
					$("#mod_pais_null").slideUp("slow");
					return true;
				}
			}
		}
	}
}					
/* Fin funcion para validar mod_pais */
/* Funcion para validar mod_departamento */
function validar_mod_departamento(){
	validar_input('mod_departamento');

	/*onkeyup="espacios_formulario_municipio('mod_departamento')";
	var ant_departamento = $('#ant_departamento').val();// Este valor ya fue cargado en la funcion cargar_modifica_municipio
	var y =$('#mod_departamento').val(); // Este valor ya fue cargado en la funcion cargar_modifica_municipio

	if(y== ""){
		$("#mod_departamento_null").slideDown("slow");
		$("#mod_departamento_min").slideUp("slow");
		$("#mod_departamento_max").slideUp("slow");
		$("#error_mod_departamento_invalido").slideUp("slow");
		return false;
	}else{
		if(y.length<3){
			$("#mod_departamento_null").slideUp("slow");
			$("#mod_departamento_min").slideDown("slow");
			$("#mod_departamento_max").slideUp("slow");
			$("#error_mod_departamento_invalido").slideUp("slow");
			return false;
		}else{
			if(y.length>50){
				$("#mod_departamento_null").slideUp("slow");
				$("#mod_departamento_min").slideUp("slow");
				$("#mod_departamento_max").slideDown("slow");
				$("#error_mod_departamento_invalido").slideUp("slow");
				return false;
			}else{
				if($('#art_depto').is(":visible")){ Si hay un resultado visible, genera error 
					$("#mod_departamento_null").slideUp("slow");
					$("#mod_departamento_min").slideUp("slow");
					$("#mod_departamento_max").slideUp("slow");
					$("#error_mod_departamento_invalido").slideDown("slow");
					return false;
				}else{
					$("#mod_departamento_null").slideUp("slow");
					$("#mod_departamento_min").slideUp("slow");
					$("#mod_departamento_max").slideUp("slow");
					$("#error_mod_departamento_invalido").slideUp("slow");
					return true;
				}
			}
		}
	}		*/	
}
/* Fin funcion para validar mod_departamento */
/* Funcion para validar mod_municipio */
function validar_mod_municipio(){
	onkeyup="espacios_formulario_municipio('mod_municipio')";
	var ant_municipio=$('#ant_municipio').val();// Este valor ya fue cargado en la funcion cargar_modifica_municipio
	var z =$('#mod_municipio').val();

	if(z==""){
		$("#error_mod_municipio").slideDown("slow");
		$("#error_mod_municipio_invalido").slideUp("slow");
		$("#error_mod_municipio_minimo").slideUp("slow");
		$("#error_mod_municipio_maximo").slideUp("slow");
		return false;
	}else{
		if(z.length<3){
			$("#error_mod_municipio").slideUp("slow");
			$("#error_mod_municipio_invalido").slideUp("slow");
			$("#error_mod_municipio_minimo").slideDown("slow");
			$("#error_mod_municipio_maximo").slideUp("slow");
			return false;
		}else{
			if(z.length>30){
				$("#error_mod_municipio").slideUp("slow");
				$("#error_mod_municipio_invalido").slideUp("slow");
				$("#error_mod_municipio_minimo").slideUp("slow");
				$("#error_mod_municipio_maximo").slideDown("slow");
				return false;
			}else{
				if($('#art_muni').is(":visible")){/* Si hay un resultado visible, genera error */
					$("#error_mod_municipio").slideUp("slow");
					$("#error_mod_municipio_invalido").slideDown("slow");
					$("#error_mod_municipio_minimo").slideUp("slow");
					$("#error_mod_municipio_maximo").slideUp("slow");
					return false;
				}else{
					$("#error_mod_municipio").slideUp("slow");
					$("#error_mod_municipio_invalido").slideUp("slow");
					$("#error_mod_municipio_minimo").slideUp("slow");
					$("#error_mod_municipio_maximo").slideUp("slow");
					return true;				
				}
			}
		}
	}								
}
/* Fin funcion para validar mod_municipio */

/* Validación que los campos de Formulario Modificar Municipio (Submit) */
function validar_modificar_municipio(){
	var validar_mod_p=validar_mod_pais();
	if(validar_mod_p==false){
		$("#mod_pais").focus();
		return false;
	}else{
		var validar_mod_dep=validar_mod_departamento();
		if(validar_mod_dep==false){
			$("#mod_departamento").focus();
			return false;
		}else{
			var validar_mod_mun=validar_mod_municipio();
			if(validar_mod_mun==false){
				$("#mod_municipio").focus();
				return false;
			}else{
				return true;
			}
		}
	}
}

$('#enviar_mod').click(function submit_modificar_municipio(){
	if($('.imagen_logo').is(":visible")){
		// sweetAlert({
		Swal.fire({	
			position 			: 'top-end',
		    showConfirmButton 	: false,
		    timer 				: 1500,	
		    title				: 'La consulta se está ejecutando.',
		    text 				: 'Un momento por favor.',
		    type 				: 'warning'
		});
	}else{
		var submit_modificar_municipio = validar_modificar_municipio();
		if(submit_modificar_municipio==false){
			return false;
		}else{	//  $('#formulario_modificar_municipio').submit(); // Realizar la creación del municipio
			$("#boton_modificar_municipio").html("<center><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</center>")

			var id_municipio1=$("#id_municipio").val();
			var tipo_formulario1=$("#tipo_formulario_mod").val();
			var continente1=$("#mod_continente").val();
			var pais1=$("#mod_pais").val();
			var departamento1=$("#mod_departamento").val();
			var municipio1=$("#mod_municipio").val();

			$.ajax({
				type: 'POST',
				url: 'admin_muni/query_municipios.php',
				data: {
					'id_municipio' : id_municipio1,
					'tipo_formulario' : tipo_formulario1,
					'mod_continente' : continente1,
					'mod_pais' : pais1,
					'mod_departamento' : departamento1,
					'mod_municipio' : municipio1
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_mod_pais').html(resp);
					}
				}
			})	
			
		}										
	}									
});
/* Fin de validación que los campos de Formulario Modificar Municipio (Submit) */

/* Funciones para guardar en base de datos auditoria de modificacion o creacion de municipio */
function auditoria(tipo_formulario,creado){
	switch(tipo_formulario){
		case'crear_municipio':
			var trans = "creado";
			break;
		case'modificar_municipio':
			var trans ="modificado";
			break;	
	}

	$.ajax({	// Guardo registro de ingreso al sistema para auditoria
		type: 'POST',
		url: 'login/transacciones.php',
		data: {
			'transaccion' : tipo_formulario,
			'creado' : 	creado
		},			
		success: function(resp1){
			if(resp1=="true"){
				// sweetAlert({
				Swal.fire({	
					position 			: 'top-end',
				    showConfirmButton 	: false,
				    timer 				: 1500,	
				    title 				: "El Municipio ha sido "+trans+" correctamente",
				    text 				: '',
				    type 				: 'success'
				}).then(function(isConfirm){
					// if(isConfirm){
					volver();
					// }
				})
			}else{
				alert(resp1)
			}
		}
	})
}
function volver(){
	carga_administrador_municipios();
	//	window.location.href='../principal3.php'
}		
/* Fin funciones para guardar en base de datos auditoria de modificacion o creacion de municipio */
