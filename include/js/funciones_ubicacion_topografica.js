/* Script para ventana modal - Tecla Esc */
window.addEventListener("keyup", function(event) {
    var codigo = event.keyCode || event.which;
    if (codigo == 27) {
        cerrarVentanaMostrarDocumentos();
        cerrarVentanaAdminCajas();
    }
    if (codigo == 8) { // Opcion para restringir que la tecla backspace da atras en el navegador.
        if (history.forward(1)) {
            location.replace(history.forward(1));
        }
    }
}, false);
/* Fin script para ventana modal - Tecla Esc */
/******************************************************************************************/
/* Administrador de Cajas *****************************************************************/
/******************************************************************************************/
function cargar_caja(caja){
	$("#contenido").load("admin_ubicacion_topografica/incluye_cajas.php?codigo_ubicacion="+caja);
}
function cerrarVentanaAdminCajas(){
	$("#ventana3").slideUp("slow");

/*
	$('#sugerencia_nombre_nivel').html('');
	$('#sugerencia_nivel_padre').html('');

	$('#nombre_nivel').val(null);
	$('#nivel_padre').val(null);
	
	oculta_errores();	

	$('.art').slideUp("slow");
	$('.art1').slideUp("slow");
	$('.art2').slideUp("slow");
	$('.art3').slideUp("slow");

	$('#search_nivel').focus();
*/
}

/*Script para buscar expediente desde campo "search_expedientes" del Formulario Buscador de expedientes*/
$("#search_expedientes").on("input",function(e){ // Accion que se activa cuando se digita #search_expedientes
    $('#desplegable_resultados').html("<center class='imagen_logo'><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100'><br>Cargando...</h3></center>"); 				
//	oculta_errores();   
    var nom_niv = $(this).val();
    var nombre_niv=$('#nombre_nivel').val();
    var id_niv=$('#id_nivel').val();
    
    if($(this).data("lastval")!= nom_niv){
    	$(this).data("lastval",nom_niv);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
     		if(nom_niv.length>0 && nom_niv.length<40){
        		$.ajax({
					type: 'POST',
					url: 'admin_ubicacion_topografica/buscador_ubicacion_topografica.php',
					data: {
						'search_expedientes' : nom_niv,
						'nombre_n' : nombre_niv,
						'id_nivel' : id_niv
					},			
					success: function(resp){
						if(resp!=""){
							$('#desplegable_resultados').html(resp);
						}
					}
				})	 		
			}else{
				$('#desplegable_resultados').html('');
			}	
			if(nom_niv.length>40){
				$('#desplegable_resultados').html('<h4>La busqueda debe tener 40 caracteres maximo. Revise por favor</h4>');
			}  				 
		},1000);
    };
});
/* Fin script para buscar expediente desde campo "search_expedientes" del Formulario Buscador de expedientes*/

/*Script para buscar expediente desde campo "search_radicados1" del Formulario Buscador de expedientes*/
$("#search_radicados1").on("input",function(e){ // Accion que se activa cuando se digita #search_radicados1
    $('#desplegable_resultados_rad').html("<center class='imagen_logo'><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100'><br>Cargando...</h3></center>"); 				
//	oculta_errores();   
    var nom_rad = $(this).val();
    var num_exp = $("#numero_expediente_rad").val();

    var id_niv=$('#id_nivel').val();
    
    if($(this).data("lastval")!= nom_rad){
    	$(this).data("lastval",nom_rad);
    
		clearTimeout(timerid);
		timerid = setTimeout(function() {
     		if(nom_rad.length>1 && nom_rad.length<40){
        		$.ajax({
					type: 'POST',
					url: 'admin_ubicacion_topografica/buscador_ubicacion_topografica.php',
					data: {
						'search_radicados1' : nom_rad,
						'numero_expediente_rad' : num_exp,
						'id_nivel' : id_niv
					},			
					success: function(resp){
						if(resp!=""){
							$('#desplegable_resultados_rad').html(resp);
						}
					}
				})	 		
			}else{
				$('#desplegable_resultados').html('');
			}	
			if(nom_rad.length>40){
				$('#desplegable_resultados').html('<h4>La busqueda debe tener 40 caracteres maximo. Revise por favor</h4>');
			}  				 
		},1000);
    };
});
/* Fin script para buscar expediente desde campo "search_radicados1" del Formulario Buscador de expedientes*/

/*Script para ventana modal mostrar documentos en expedientes */
function abrirVentanaMostrarDocumentos(expediente){
//	var crear=$("#search_nivel").val();
	$("#titulo_expediente").html("Documentos en expediente "+expediente);
	$("#numero_expediente_rad").val(expediente);
	$("#ventana4").slideDown("slow");
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
/*	
	$("#nombre_nivel").val(crear);
	$("#nombre_nivel").focus();
*/
}
function cerrarVentanaMostrarDocumentos(){
	$("#ventana4").slideUp("slow");
	$("#desplegable_resultados_rad").html("");
	$("#search_radicados1").val("");
}
/*Fin script para ventana modal mostrar documentos en expedientes */

function carga_documentos_exp(expediente){
	abrirVentanaMostrarDocumentos(expediente);

	$("#contenido").css({'z-index':'100'});	// Modifico estilo para despliegue de ubicacion topografica 

	$.ajax({
		type: 'POST',
		url: 'admin_ubicacion_topografica/buscador_ubicacion_topografica.php',
		data: {
			'search_radicados' : expediente
		},			
		success: function(resp){
			if(resp!=""){
				$('#documentos_en_expediente').html(resp);
				$('#search_radicados1').focus();
			}
		}
	})		
}
function sacar_documentos_exp(radicado, expediente){
	Swal.fire({
		title 				:'El radicado '+radicado+' va a ser eliminado del expediente '+expediente,
		text 				: "Esta acción no se puede revertir. ¿Está seguro?",
		type 				: 'warning',
		showCancelButton 	: true,
		confirmButtonColor 	: '#3085d6',
		cancelButtonColor 	: '#d33',
		confirmButtonText 	: 'Si, eliminar!',
		cancelButtonText 	: 'Cancelar'
	}).then((result) => {
	  	if (result,value) {
	  		$.ajax({
				type: 'POST',
				url: 'admin_ubicacion_topografica/query_topografica.php',
				data: {
					'tipo_formulario' : 'sacar_radicado_expediente',
					'radicado' : radicado,
					'expediente' : expediente
				},			
				success: function(resp){
					if(resp!=""){
						$('#caja_actual').html(resp);				
					}
				}
			})	

	  	}
	})
}
function sacar_exp_caja(expediente,caja){
	Swal.fire({
		title 				:'El expediente '+expediente+' va a ser eliminado de la caja '+caja,
		text 				: "Esta acción no se puede revertir. ¿Está seguro?",
		type 				: 'warning',
		showCancelButton 	: true,
		confirmButtonColor 	: '#3085d6',
		cancelButtonColor 	: '#d33',
		confirmButtonText 	: 'Si, eliminar!',
		cancelButtonText 	: 'Cancelar'
	}).then((result) => {
	  	if (result.value) {
	  		$.ajax({
				type: 'POST',
				url: 'admin_ubicacion_topografica/query_topografica.php',
				data: {
					'tipo_formulario' : 'sacar_expediente_de_caja',
					'expediente' : expediente,
					'caja' : caja
				},			
				success: function(resp){
					if(resp!=""){
						$('#caja_actual').html(resp);				
					}
				}
			})	

	  	}
	})
}
function agregar_exp_caja(expediente,id_nivel,nombre_nivel){
	$.ajax({
		type: 'POST',
		url: 'admin_ubicacion_topografica/query_topografica.php',
		data: {
			'tipo_formulario' : 'agregar_expediente_de_caja',
			'expediente' : expediente,
			'id_nivel' : id_nivel,
			'nombre_nivel' : nombre_nivel
		},			
		success: function(resp){
			if(resp!=""){
				$('#desplegable_resultados').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
				$('#caja_actual').html(resp);				
			}
		}
	})	
	
}	
function mover_exp_caja(expediente,id_nivel,nombre_nivel){
	// swal({
	Swal.fire({
		title 				:'El expediente '+expediente+' va a ser movido a la caja '+nombre_nivel,
		text 				: "Esta acción no se puede revertir. ¿Está seguro?",
		type 				: 'warning',
		showCancelButton 	: true,
		confirmButtonColor 	: '#3085d6',
		cancelButtonColor 	: '#d33',
		confirmButtonText 	: 'Si, Mover!',
		cancelButtonText 	: 'Cancelar'
	}).then((result) => {
	  	if (result.value) {
	  		$.ajax({
				type: 'POST',
				url: 'admin_ubicacion_topografica/query_topografica.php',
				data: {
					'tipo_formulario' : 'mover_expediente_de_caja',
					'expediente' : expediente,
					'id_nivel' : id_nivel,
					'nombre_nivel' : nombre_nivel
				},			
				success: function(resp){
					if(resp!=""){
						$('#caja_actual').html(resp);				
					}
				}
			})	
	  	}
	})
}
function agregar_rad_exp(radicado,expediente){
	$.ajax({
		type: 'POST',
		url: 'admin_ubicacion_topografica/query_topografica.php',
		data: {
			'tipo_formulario' : 'agregar_radicado_exp',
			'radicado' : radicado,
			'expediente' : expediente
		},			
		success: function(resp){
			if(resp!=""){
				$('#caja_actual').html(resp);				
			}
		}
	})	
}
function mover_rad_exp(radicado,expediente){
	$.ajax({
		type: 'POST',
		url: 'admin_ubicacion_topografica/query_topografica.php',
		data: {
			'tipo_formulario' : 'mover_radicado_exp',
			'radicado' : radicado,
			'expediente' : expediente
		},			
		success: function(resp){
			if(resp!=""){
				$('#caja_actual').html(resp);				
			}
		}
	})
}

/******************************************************************************************/
/* Auditoria ******************************************************************************/
/******************************************************************************************/
/* Funciones para agregar a auditoria */
function auditoria(tipo_formulario,nombre_nivel,caja){

	switch(tipo_formulario){
		case 'crear_nivel':
		var trans = "El nivel "+nombre_nivel+" ha sido creado correctamente";
		break;
		
		case 'modificar_nivel':
		var trans = "El nivel "+nombre_nivel+" ha sido modificado correctamente";
		break;	
		case 'sacar_radicado_expediente':
		var trans ="El radicado ha sido sacado correctamente del expediente";
		break;	
		case 'agregar_expediente_de_caja':
		var trans ="El expediente ha sido agregado correctamente de la caja";
		break;	
		case 'sacar_expediente_de_caja':
		var trans ="El expediente ha sido sacado correctamente de la caja";
		break;			
		case 'mover_expediente_de_caja':
		var trans ="El expediente ha sido movido correctamente de caja";
		break;	
		case 'agregar_radicado_exp':
		var trans ="El radicado ha sido agregado correctamente al expediente";
		break;	
		case 'mover_radicado_exp':
		var trans ="El radicado ha sido movido correctamente al expediente";
		break;	
	}
	$.ajax({	// Guardo registro de ingreso al sistema para auditoria
		type: 'POST',
		url: 'login/transacciones.php',
		data: {
			'transaccion' : tipo_formulario,
			'creado' : 	nombre_nivel
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
					// if(isConfirm){
					switch(tipo_formulario){
						case 'crear_nivel':
						case 'modificar_nivel':
							volver();
						break;	
						case 'agregar_expediente_de_caja':
						case 'sacar_expediente_de_caja':
						case 'mover_expediente_de_caja':
							cargar_caja(caja);
						break;	
						case 'agregar_radicado_exp':
						case 'sacar_radicado_expediente':
						case 'mover_radicado_exp':
							$("#search_radicados1").val('');
							$("#desplegable_resultados_rad").html('');
							carga_documentos_exp(caja)
						break;
					}
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
	carga_ubicacion_topografica();
}
/* Fin funciones para agregar a auditoria */