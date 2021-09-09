/* Script para ventana modal - Tecla Esc */
	window.addEventListener("keyup", function(event){
	    var codigo = event.keyCode || event.which;
	    if (codigo == 27){
	        cerrarVentanaCrearDependencia();
	        cerrarVentanaModificarDependencia()
	    }
	    if(codigo== 8){ // Opcion para restringir que la tecla backspace da atras en el navegador.
	    	if (history.forward(1)) {
				location.replace(history.forward(1));
			}	
	    }
	}, false);
/* Fin script para ventana modal - Tecla Esc */
/******************************************************************************************/
/* Principal ******************************************************************************/
/******************************************************************************************/
/* Script para buscador del administrador de dependencias */
var timerid="";
$(function buscador_dependencias(){
	$('#search_dependencias').focus();

	$('#search_dependencias').on("input",function(e){ // Accion que se activa cuando se digita #search_dependencias
		// $('#desplegable_resultados').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
		loading('desplegable_resultados');
		var envio_dependencia = $(this).val();
			
		if ($(this).data("lastval")!=envio_dependencia) {
			$(this).data("lastval",envio_dependencia);

			clearTimeout(timerid);
			timerid = setTimeout(function(){
				validar_input('search_dependencias');

				// if(envio_dependencia.length>2 && envio_dependencia.length<=50){
					// console.log("search_dependencias")
					$.ajax({
						type: 'POST',
						url: 'admin_depe/buscador_dependencias.php',
						data: {
							'search_depe' 		: envio_dependencia,
							'desde_formulario' 	: '1'
						},			
						success: function(resp){
							if(resp!=""){
								$('#desplegable_resultados').html(resp);
							}
						}
					})
				// }
			},1000);
		};		
	});
});
/*Fin script para buscador del administrador de dependencias*/
/******************************************************************************************/
/* Agregar Nueva Dependencia **************************************************************/
/******************************************************************************************/
/*Script para ventana modal Agregar Nueva dependencia*/
function abrirVentanaCrearDependencia(){
	$("#ventana").slideDown("slow");
	$("#codigo_dependencia").focus();
	$("#boton_crear_dependencia").html('<input type="button" value="Grabar Dependencia" id="bEnviar_depe" class="botones" onclick="submit_agregar_dependencia()">');
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
	$("#nombre_dependencia").val($("#search_dependencias").val());
}
function cerrarVentanaCrearDependencia(){
	$("#ventana").slideUp("slow");

	$('#sugerencia_codigo_dependencia').html('');
	$('#sugerencia_nombre_dependencia').html('');
	$('#sugerencia_dependencia_padre').html('');

	$('#codigo_dependencia').val(null); 
	$('#nombre_dependencia').val(null);
	$('#dependencia_padre').val(null);

	$('.errores').slideUp('slow');
	$('.errores2').slideUp('slow');

	// oculta_errores();	

	$('.art').slideUp('slow');
	$('.art1').slideUp('slow');
	$('.art2').slideUp('slow');
	$('.art3').slideUp('slow');

	$('#search_dependencias').focus();
}
/*Fin script para ventana modal Agregar Nueva Dependencia*/
/*Script para buscar dependencia desde campo codigo Formulario Agregar Nueva Dependencia*/
$("#codigo_dependencia").on("input",function(e){ // Accion que se activa cuando se digita #codigo_dependencia
	espacios_formulario('codigo_dependencia','mayusculas',0);
	loading('sugerencia_codigo_dependencia');
	$(".errores").slideUp("slow");

    var codi = $(this).val();
    
    if($(this).data("lastval")!= codi){
    	$(this).data("lastval",codi);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
		    validar_input('codigo_dependencia');

    		$.ajax({
				type: 'POST',
				url: 'admin_depe/buscador_dependencias.php',
				data: {
					'search_codi_depe' : codi,
					'desde_formulario' : '0'
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_codigo_dependencia').html(resp);
					}
				}
			})			 
		},1000);
    };
});
/*Fin script para buscar dependencia desde campo codigo Formulario Agregar Nueva Dependencia*/

/*Script para buscar dependencia desde campo "Nombre de la Dependencia" del Formulario Agregar Nueva Dependencia*/
$("#nombre_dependencia").on("input",function(e){ // Accion que se activa cuando se digita #nombre_dependencia
	espacios_formulario('nombre_dependencia','mayusculas',0);
	loading('sugerencia_nombre_dependencia');
	$(".errores").slideUp("slow");

    var nom_depe = $(this).val();
      
    if($(this).data("lastval")!= nom_depe){
    	$(this).data("lastval",nom_depe);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_input('nombre_dependencia')

    		$.ajax({
				type: 'POST',
				url: 'admin_depe/buscador_dependencias.php',
				data: {
					'search_nom_depe' 	: nom_depe,
					'desde_formulario' 	: '1'
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_nombre_dependencia').html(resp);
					}
				}
			})	 				 
		},1000);
    };
});
/* Fin script para buscar dependencia desde campo "Nombre de la Dependencia", Formulario Agregar Nueva Dependencia */

/*Script para buscar dependencia_padre desde formulario Agregar Nueva Dependencia*/
$("#dependencia_padre").on("input",function(e){ // Accion que se activa cuando se digita #dependencia_padre
	espacios_formulario('dependencia_padre','mayusculas',0);
	loading('sugerencia_dependencia_padre');
    var depe_padre = $(this).val();

    if($(this).data("lastval")!= depe_padre){
    	$(this).data("lastval",depe_padre);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
    		$.ajax({
				type: 'POST',
				url: 'admin_depe/buscador_dependencias.php',
				data: {
					'search_depe_padre' : depe_padre,
					'desde_formulario' 	: '1'
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_dependencia_padre').html(resp);
					}
				}
			})						 
		},1000);
    };
});

/* Fin script para buscar dependencia_padre desde Formulario Agregar Nueva Dependencia */

/*Función para cargar valor dependencia padre Formulario Agregar Nueva Dependencia */
function cargar_dependencia_padre(nombre_dependencia_padre){
	$('#dependencia_padre').val(nombre_dependencia_padre);
	$('.art3').slideUp("slow");
	$('#error_nombre_dependencia_padre').slideUp("slow");
	$('#error_nombre_dependencia_padre2').slideUp("slow");
}
/*Fin función para cargar valor dependencia padre Formulario Agregar Nueva Dependencia*/

/* Validación que los campos de Formulario Agregar Nueva Dependencia (Submit) */
function validar_grabar_dependencia(){
	var codigo_dependencia =$('#codigo_dependencia').val();
	var nombre_dependencia =$('#nombre_dependencia').val();

	validar_input('codigo_dependencia');
	validar_input('nombre_dependencia');

	if($('.art1').is(":visible")){
		$("#codigo_dependencia_ya_existe").slideDown("slow");
		$("#codigo_dependencia").focus();
		return false;
	}else{
		$("#codigo_dependencia_ya_existe").slideUp("slow");

		if($('.art2').is(":visible")){
			$("#nombre_dependencia_ya_existe").slideDown("slow");
			$("#nombre_dependencia").focus();
			return false;
		}else{
			$("#nombre_dependencia_ya_existe").slideUp("slow");

			if($('.art3').is(":visible")){
				$('#error_nombre_dependencia_padre2').slideDown("slow");
				$("#dependencia_padre").focus();
				return false;
			}else{
				$('#error_nombre_dependencia_padre2').slideUp("slow");
				
				if($("#error_nombre_dependencia_padre").is(":visible")){
					$("#dependencia_padre").focus();
					return false;
				}else{
					$("#error_nombre_dependencia_padre").slideUp("slow");

					if(codigo_dependencia== "" || codigo_dependencia.length<3){
						$("#codigo_dependencia").focus();
						return false;
					}else{		
						if(nombre_dependencia== "" || nombre_dependencia.length < 6 || nombre_dependencia.length > 100){
							$("#nombre_dependencia").focus();
							return false;
						}else{
							return true;
						}
					}
				}
			}	
		}		
	}	
}

function submit_agregar_dependencia(){
	var submit_agregar_dependencia = validar_grabar_dependencia();
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
		if($(".errores").is(":visible") || $(".errores2").is(":visible") || submit_agregar_dependencia==false){
			return false;
		}else{	// Realizar la creación de la Dependencia			
			loading('boton_crear_dependencia');

			var codigo_dependencia1 			= $("#codigo_dependencia").val();
			var dependencia_padre1 			 	= $("#dependencia_padre").val();
			var id_cambio_organico_funcional 	= $("#id_cambio_organico_funcional").val();
			var nombre_dependencia1  			= $("#nombre_dependencia").val();
			var tipo_formulario1 				= $("#tipo_formulario").val();
			
			$.ajax({
				type: 'POST',
				url: 'admin_depe/query_dependencias.php',
				data: {
					'tipo_formulario' 		: tipo_formulario1,
					'codigo_dependencia' 	: codigo_dependencia1,
					'dependencia_padre' 	: dependencia_padre1,
					'id_cambio_of' 			: id_cambio_organico_funcional,
					'nombre_dependencia' 	: nombre_dependencia1
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_codigo_dependencia').html(resp);
					}
				}
			})	
		}													
	}				
}

/* Fin validación que los campos de Formulario Agregar Nueva Dependencia (Submit) */

/******************************************************************************************/
/* Modificar Dependencia ******************************************************************/
/******************************************************************************************/
/*Funciones para desplegar ventana modal Modificar Dependencias*/

function abrirVentanaModificarDependencias(){
	$("#ventana2").slideDown("slow");
	$("#mod_nombre_dependencia").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrarVentanaModificarDependencia(){
	$("#ventana2").slideUp("slow");
	$("#search_dependencias").focus();

	$('.art').slideUp("slow");
	$('.art1').slideUp("slow");
	$('.art2').slideUp("slow");
	$('.art3').slideUp("slow");

	$("#id_dependencia").val(null);
	$("#antiguo_mod_padre").val(null);
	$("#mod_codigo_dependencia").val(null);
	$("#mod_nombre_dependencia").val(null);
	$("#mod_dependencia_padre").val(null);
	$("#antiguo_nombre_dependencia").val(null);

	oculta_mod_errores();

	$('#sugerencia_mod_nombre_dependencia').html('');
	$('#sugerencia_dependencia_mod_padre').html('');
}
/* Fin funciones para desplegar ventana modal Modificar Dependencias */

/* Script para ocultar errores y continuar consulta - Formulario Modificar Dependencia */
function oculta_mod_errores(){
	$("#mod_dependencia_ya_existe").slideUp("slow");
	$("#error_mod_nombre_dependencia").slideUp("slow");
	$('#valida_minimo_mod_nombre').slideUp("slow");
	$('#valida_maximo_mod_nombre').slideUp("slow");
	$('#error_nombre_mod_dependencia_padre').slideUp("slow");	
	$('#error_nombre_mod_dependencia_padre2').slideUp("slow");	
	$('#error_nombre_mod_dependencia_padre3').slideUp("slow");	
}	
/* Fin script para ocultar errores y continuar consulta - Formulario Modificar Dependencia */


/* Script para buscador dependencia por nombre en formulario Modificar Dependencia */
$('#mod_nombre_dependencia').on("input",function(e){ // Accion que se activa cuando se digita #mod_nombre_dependencia
	espacios_formulario('mod_nombre_dependencia','mayusculas',0);
	loading('sugerencia_mod_nombre_dependencia');
	$(".errores").slideUp('slow');

	var search_nombre_mod_depe 			= $(this).val();
	var search_antiguo_nombre_mod_depe 	= $("#antiguo_nombre_dependencia").val();
	
	if($(this).data("lastval")!=search_nombre_mod_depe) {
		$(this).data("lastval",search_nombre_mod_depe);

		clearTimeout(timerid);
		timerid = setTimeout(function(){
			validar_input('mod_nombre_dependencia');

			$.ajax({
				type: 'POST',
				url: 'admin_depe/buscador_dependencias.php',
				data: {
					'search_nombre_mod_depe' 			: search_nombre_mod_depe,
					'search_antiguo_nombre_mod_depe' 	: search_antiguo_nombre_mod_depe,
					'desde_formulario' 					: '1'
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_mod_nombre_dependencia').html(resp);
					}
				}
			})
		},1000);
	};		
});
/* Fin script para buscador dependencia por nombre en formulario Modificar Dependencia */

/* Script para buscar dependencia_padre desde formulario Modificar Dependencia */
$('#mod_dependencia_padre').on("input",function(e){ // Accion que se activa cuando se digita #mod_dependencia_padre
	espacios_formulario('mod_dependencia_padre','mayusculas',0);	
	loading('sugerencia_dependencia_mod_padre');
	$(".errores").slideUp("slow");

	var mod_depe_padre 		= $(this).val();
	var antiguo_mod_padre 	= $("#antiguo_mod_padre").val();
	
	if ($(this).data("lastval")!=mod_depe_padre) {
		$(this).data("lastval",mod_depe_padre);

		clearTimeout(timerid);
		timerid = setTimeout(function(){
			validar_input('mod_dependencia_padre');

			if(mod_depe_padre.length>0){
				$.ajax({
					type: 'POST',
					url: 'admin_depe/buscador_dependencias.php',
					data: {
						'search_mod_depe_padre' 	: mod_depe_padre,
						'search_antiguo_mod_padre' 	: antiguo_mod_padre,
						'desde_formulario' 			: '1'
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencia_dependencia_mod_padre').html(resp);
						}
					}
				})
			}else{
				$('#sugerencia_dependencia_mod_padre').html('');	
				$('.errores').slideUp('slow');		
			}  
		},1000);
	};		
});

/* Fin script para buscar dependencia_padre desde Formulario Modificar Dependencia */

/* Alerta error al dar click sobre enlace de dependencia que ya existe - Formulario Modificar Dependencia se invoca en buscador_dependencias.php */
function error_modificar_dependencia(){
	// sweetAlert({
	Swal.fire({	
		position 			: 'top-end',
	    showConfirmButton 	: false,
	    timer 				: 1500,		
	    title 				: 'No es posible asignar este codigo / nombre.',
	    text 				: 'El codigo / nombre seleccionado corresponde a una dependencia que ya existe.',
	    type 				: 'warning'
	});
}
/* Fin alerta error al dar click sobre enlace de dependencia que ya existe - Formulario Modificar Dependencia*/

/*Función para cargar valor dependencia padre Formulario Modificar Nueva Dependencia */
function cargar_dependencia_mod_padre(mod_dependencia_padre){
	$('#mod_dependencia_padre').val(mod_dependencia_padre);
	$('.art2').slideUp("slow");
	$('#error_nombre_mod_dependencia_padre').slideUp("slow");
	$('#error_nombre_mod_dependencia_padre2').slideUp("slow");
	$('#error_nombre_mod_dependencia_padre3').slideUp("slow");
}
/*Fin función para cargar valor dependencia padre Formulario Modificar Nueva Dependencia*/

/*Función para cargar datos al formulario de modificación de Dependencia*/
function cargar_modifica_dependencia(codigo_dependencia,nombre_dependencia,dependencia_padre,activa,id_dependencia,id_cambio_organico_funcional){

	$('#antiguo_mod_padre').val(dependencia_padre);
	$('#antiguo_nombre_dependencia').val(nombre_dependencia);	
	$('#id_cambio_organico_funcional_mod').val(id_cambio_organico_funcional);
	$('#id_dependencia').val(id_dependencia)
	$('#mod_activa').val(activa);
	$('#mod_codigo_dependencia').val(codigo_dependencia);
	$('#mod_dependencia_padre').val(dependencia_padre);
	$('#mod_nombre_dependencia').val(nombre_dependencia);

	$('.art1').slideUp("slow");
	$('.art2').slideUp("slow");
	$('#cod_dependencia_ya_existe').slideUp("slow");		
	$('#mod_dependencia_ya_existe').slideUp("slow");		

	cerrarVentanaCrearDependencia();
	abrirVentanaModificarDependencias();
}	
/* Fin funcion para cargar datos al formulario de modificacion de Dependencia */
/* Funcion para validar mod_nombre_dependencia */
function validar_mod_nombre_dependencia(){
	var mod_nombre_dependencia =$('#mod_nombre_dependencia').val(); // Este valor ya fue cargado en la funcion cargar_modifica_dependencia
	var antiguo_nombre = $('#antiguo_nombre_dependencia').val();// Este valor ya fue cargado en la funcion cargar_modifica_dependencia

	if(mod_nombre_dependencia==""){
		$("#mod_dependencia_ya_existe").slideUp("slow");
		$("#error_mod_nombre_dependencia").slideDown("slow");
		$("#valida_minimo_mod_nombre").slideUp("slow");
		$("#valida_maximo_mod_nombre").slideUp("slow");
		return false;
	}else{
		if(mod_nombre_dependencia.length<6){
			$("#mod_dependencia_ya_existe").slideUp("slow");
			$("#error_mod_nombre_dependencia").slideUp("slow");
			$("#valida_minimo_mod_nombre").slideDown("slow");
			$("#valida_maximo_mod_nombre").slideUp("slow");
			return false;
		}else{
			if(mod_nombre_dependencia.length>100){
				$("#mod_dependencia_ya_existe").slideUp("slow");
				$("#error_mod_nombre_dependencia").slideUp("slow");
				$("#valida_minimo_mod_nombre").slideUp("slow");
				$("#valida_maximo_mod_nombre").slideDown("slow");
				return false;
			}else{
				if($(".art1").is(":visible")){
					$("#mod_dependencia_ya_existe").slideDown("slow");
					$("#error_mod_nombre_dependencia").slideUp("slow");
					$("#valida_minimo_mod_nombre").slideUp("slow");
					$("#valida_maximo_mod_nombre").slideUp("slow");
					return false;
				}else{
					return true;				
				}
			}
		}
	}	
}
/* Fin funcion para validar mod_nombre_dependencia */
/* Funcion para validar mod_dependencia_padre */
function validar_mod_nombre_dependencia_padre(){
	var mod_nombre_dependencia_padre =$('#mod_dependencia_padre').val();
	var antiguo_padre=$('#antiguo_mod_padre').val();// Este valor ya fue cargado en la funcion cargar_modifica_dependencia
	
	if($(".art2").is(":visible")){
		$("#error_nombre_mod_dependencia_padre").slideUp("slow");
		$("#error_nombre_mod_dependencia_padre2").slideDown("slow");
		$("#error_nombre_mod_dependencia_padre3").slideUp("slow");
		return false;
	}
	if($('#error_nombre_mod_dependencia_padre').is(':visible')){
		return false;
	}else{
		if($('#error_nombre_mod_dependencia_padre2').is(':visible')){
			return false;
		}else{
			if($('#error_nombre_mod_dependencia_padre3').is(':visible')){
				return false;
			}else{
				return true;
			}	
		}
	}
}
/* Fin funcion para validar mod_dependencia_padre */

/* Validación que los campos de Formulario Modificar Dependencia (Submit) */
function validar_modificar_dependencia(){
	var validar_mod_nombre_depe = validar_mod_nombre_dependencia();
	
	if(validar_mod_nombre_depe==false){
		$("#mod_nombre_dependencia").focus()
		return false;
	}else{
		var validar_mod_nombre_depe_padre= validar_mod_nombre_dependencia_padre();
		if(validar_mod_nombre_depe_padre==false){
			$("#mod_dependencia_padre").focus();
			return false;
		}else{
			return true;
		}
	}
}					
$(function submit_modificar_dependencia(){
	$('#enviar_mod_dependencia').click(function submit_modificar_dependencia(){
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
			var submit_modificar_dependencia = validar_modificar_dependencia();
			if(submit_modificar_dependencia==false){
				return false;
			}else{	// Realizar la modificación de la Dependencia
				
				loading('boton_modificar_dependencia');

				var tipo_formulario1=$("#tipo_formulario_mod").val();
				var id_dependencia1=$("#id_dependencia").val();
				var mod_codigo_dependencia1=$("#mod_codigo_dependencia").val();
				var antiguo_nombre_dependencia1=$("#antiguo_nombre_dependencia").val();
				var mod_nombre_dependencia1=$("#mod_nombre_dependencia").val();
				var mod_dependencia_padre1=$("#mod_dependencia_padre").val();
				var mod_activa1=$("#mod_activa").val();

				$.ajax({
					type: 'POST',
					url: 'admin_depe/query_dependencias.php',
					data: {
						'tipo_formulario' 				: tipo_formulario1,
						'id_dependencia' 				: id_dependencia1,
						'mod_codigo_dependencia' 		: mod_codigo_dependencia1,
						'antiguo_nombre_dependencia' 	: antiguo_nombre_dependencia1,
						'mod_nombre_dependencia' 		: mod_nombre_dependencia1,
						'mod_dependencia_padre' 		: mod_dependencia_padre1,
						'mod_activa' 					: mod_activa1
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencia_codigo_dependencia').html(resp);
						}
					}
				})	
			}										
		}										
	});
})
/* Fin de validación que los campos de Formulario Modificar Dependencia (Submit) */
/* Funciones para agregar a auditoria */
function auditoria(tipo_formulario,nombre_dependencia){
	switch(tipo_formulario){
		case'crear_dependencia':
		var trans = "creado";
		break;
		case'modificar_dependencia':
		var trans ="modificado";
		break;	
	}
	$.ajax({	// Guardo registro de ingreso al sistema para auditoria
		type: 'POST',
		url: 'login/transacciones.php',
		data: {
			'transaccion' 	: tipo_formulario,
			'creado' 		: nombre_dependencia
		},			
		success: function(resp1){
			if(resp1=="true"){
				// sweetAlert({
				Swal.fire({	
					position 			: 'top-end',
				    showConfirmButton 	: false,
				    timer 				: 1500,	
				    title 				:'La dependencia '+nombre_dependencia+' ha sido '+trans+' correctamente',
				    text 				: '',
				    type 				: 'success'
				}).then(function(isConfirm){
					// if(isConfirm){
					volver();
					// }
				})
			}else{
				alert(resp1);
			}
		}
	})
}
function volver(){
	// window.location.href='../principal3.php';
	carga_administrador_dependencias();
}
/* Fin funciones para agregar a auditoria */
/* Funcion para descargar el formato / plantilla excel para cargar dependencias de manera masiva */
function descargar_csv_formato_dependencias(){
	location.href ="admin_depe/Formato_cargue_masivo_dependencias_Jonas.xls";
}
/* Fin funcion para descargar el formato / plantilla excel para cargar dependencias de manera masiva */
