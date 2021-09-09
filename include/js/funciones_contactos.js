/* Script para ventana modal - Tecla Esc */
    window.addEventListener("keydown", function(event){
        var codigo = event.keyCode || event.which;
        if (codigo == 27){
            cerrar_ventana_agregar_contacto();
            cerrar_ventana_modifica_contacto();
        }
        if(codigo== 8){ // Opcion para restringir que la tecla backspace da atras en el navegador.
        	if (history.forward(1)) {
				location.replace(history.forward(1));
			}	
        }
    }, false);
/* Fin script para ventana modal - Tecla Esc */
/* Script para buscador contactos desde formulario principal */
var timerid="";
$(function buscador_contactos(){
	$('#search_remitente').focus();
	$('#search_remitente').on("input",function(e){ // Accion que se activa cuando se digita #search_remitente
		$('#sugerencias_remitente').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
		var envio = $(this).val();
		
		if($(this).data("lastval")!= envio){
	    	$(this).data("lastval",envio);                   
   			clearTimeout(timerid);
   			timerid = setTimeout(function() {
         		if(envio.length>2 && envio.length<50){		
				//	$('#logo').html('<h2>Radicación de Entrada</h2>');
					$.ajax({
						type: 'POST',
						url:  'radicacion/radicacion_salida/buscador_remitente.php' ,
						data: {
							'search_remitente': envio
						},
						success: function(resp){
							if(resp!=""){
								$('#sugerencias_remitente').html(resp);
							}
						}
					})		 		
				}else{
					$('#sugerencias_remitente').html('<h4>Para iniciar la búsqueda debe ingresar por lo menos 3 caracteres.</h4>');
				} 
				if(envio.length>50){
					$('#sugerencias_remitente').html('<h4>La busqueda debe tener 50 caracteres maximo. Revise por favor</h4>');
				}  				 
   			},1000);
	    };
	})
})
/* Funciones para desplegar ventana modal contacto */
function abrir_ventana_agregar_contacto(){
	$("#ubicacion_contacto").val("BOGOTA, D.C. (BOGOTA) COLOMBIA-AMERICA");
	$("#ventana_agregar_contacto").slideDown("slow");
	$("#ventana_modificar_remitente").slideUp("slow");
	$('#nombre_contacto').focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrar_ventana_agregar_contacto(){
	$("#ventana_agregar_contacto").slideUp("slow");
	$('#search_remitente').focus();

	$(".art").slideUp("slow");

	oculta_errores();
	$('#nombre_contacto').val("");
	$('#nit_contacto').val("");
	$('#ubicacion_contacto').val("");
	$('#direccion_contacto').val("");
	$('#telefono_contacto').val("");
	$('#email_contacto').val("");
	$('#representante_legal_contacto').val("");
}
function abrir_ventana_modifica_contacto(){
	$("#ventana_modificar_remitente").slideDown("slow");
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
function cerrar_ventana_modifica_contacto(){
	$("#ventana_modificar_remitente").slideUp("slow");
}

/* Fin funciones para desplegar ventana modal contacto */
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
			}
		}
	})
}
/* Fin script para cargar desplegable terminos */
/* Fin script buscador - Administrador de Municipios */

/* Funcion para restringir caraceres especiales en formulario Usuarios. */
function espacios_formulario_contactos(input){
	switch(input){
		case 'search_remitente':
			var str = $('#search_remitente').val();	
			break;
		case 'nombre_contacto':
			var str = $('#nombre_contacto').val();	
			break;	
		case 'nit_contacto':
			var str = $('#nit_contacto').val();
			if (isNaN(str)) {
				$("#error_no_es_numero").slideDown("slow");
			}else{
				$("#error_no_es_numero").slideUp("slow");
			}
			break;
		case 'ubicacion_contacto':
			var str = $('#ubicacion_contacto').val();
			break;	
		case 'direccion_contacto':
			var str = $('#direccion_contacto').val();
			
			str = str.replace('CRA','CARRERA');
			str = str.replace('KRA','CARRERA');
			str = str.replace('CLL','CALLE');
			str = str.replace('DG','DIAGONAL');
			str = str.replace('TV','TRANSVERSAL');
			break;	
		case 'representante_legal_contacto':
			var str = $('#representante_legal_contacto').val();
			break;		
		case 'direc_contacto':
			var str = $("#dir_contacto").val();
			break;	
		case 'dignatario2':
			var str = $("#dignatario").val();
			break;	
		case 'asunto':
			var str = $("#asunto").val();
			break;	
		case 'descripcion_anexos':
			var str = $("#descripcion_anexos").val();
			break;	
		case 'dependencia_destino':
			var str = $("#dependencia_destino").val();
			break;		
	}
		str = str.replace('-',''); 	str = str.replace('°','');	str = str.replace('!','');
		str = str.replace('|','');	str = str.replace('"','');	str = str.replace('$','');
		str = str.replace('#','');	str = str.replace('%','');	str = str.replace('&','');
		str = str.replace('=','');	str = str.replace('?','');	str = str.replace('¿','');
		str = str.replace('¡','');	str = str.replace('(','');	str = str.replace(')','');
		str = str.replace('{','');	str = str.replace('}','');	str = str.replace('[','');
		str = str.replace(']','');	str = str.replace(';','');
		str = str.replace(':','');	str = str.replace('_','');	str = str.replace('~','');
		str = str.replace('@','');	str = str.replace('´','');	str = str.replace("+",'');
		str = str.replace("/","");	str = str.replace("*","");	str = str.replace("'","");
		str = str.replace(',','');	str = str.replace('^','');	str = str.replace('ñ','N');
		str = str.replace('Ñ','N');	str = str.replace('á','A');	str = str.replace('é','E');
		str = str.replace('í','I');	str = str.replace('ó','O');	str = str.replace('ú','U');
		str = str.replace('Á','A');	str = str.replace('É','E');	str = str.replace('Í','I');
		str = str.replace('Ó','O');	str = str.replace('Ú','U');	str = str.replace('<','');
		str = str.replace('>','');	str = str.replace('  ',' ');

	switch(input){
		case 'search_remitente':
			$('#search_remitente').val(str.toUpperCase());
			break;
		case 'nombre_contacto':
			$('#nombre_contacto').val(str.toUpperCase());	
			break;
		case 'nit_contacto':
			$('#nit_contacto').val(str);
			break;	
		case 'ubicacion_contacto':
			$('#ubicacion_contacto').val(str.toUpperCase());
			break;	
		case 'direccion_contacto':
			$('#direccion_contacto').val(str.toUpperCase());
			break;	
		case 'representante_legal_contacto':
			$('#representante_legal_contacto').val(str.toUpperCase());
			break;
		case 'direc_contacto':
			$("#dir_contacto").val(str.toUpperCase());
			break;
		case 'dignatario2':
			$("#dignatario").val(str.toUpperCase());
			break;	
		case 'asunto':
			$("#asunto").val(str.toUpperCase());
			break;	
		case 'descripcion_anexos':
			$("#descripcion_anexos").val(str.toUpperCase());
			break;	
		case 'dependencia_destino':
			$("#dependencia_destino").val(str.toUpperCase());
			break;					
	}	
}
/* Fin funcion para restringir caraceres especiales en formulario Usuarios. */
/* Script espacios mail - Formulario Agregar Nuevo Contacto */
function espacios_mail(tipo_form){
	switch(tipo_form){
		case 'agregar_contacto':
			var str2 = $('#email_contacto').val();
			break;
		case 'telefono_agregar_contacto':
			var str2 = $('#telefono_contacto').val();
			break;	
	}

	str2 = str2.replace('ñ','N'); 	str2 = str2.replace('°','');	str2 = str2.replace('!','');
	str2 = str2.replace('Ñ','N');	str2 = str2.replace('á','A');	str2 = str2.replace('é','E');
	str2 = str2.replace('í','I');	str2 = str2.replace('ó','O');	str2 = str2.replace('ú','U');
	str2 = str2.replace('Á','A');	str2 = str2.replace('É','E');	str2 = str2.replace('Í','I');
	str2 = str2.replace('Ó','O');	str2 = str2.replace('Ú','U');	str2 = str2.replace('  ','');
	str2 = str2.replace('|','');	str2 = str2.replace('"','');	str2 = str2.replace('$','');
	str2 = str2.replace('#','');	str2 = str2.replace('%','');	str2 = str2.replace('&','');
	str2 = str2.replace('=','');	str2 = str2.replace('?','');	str2 = str2.replace('¿','');
	str2 = str2.replace('¡','');	str2 = str2.replace('(','');	str2 = str2.replace(')','');
	str2 = str2.replace('{','');	str2 = str2.replace('}','');	str2 = str2.replace('[','');
	str2 = str2.replace(']','');	str2 = str2.replace(';','');	str2 = str2.replace(':','');
	str2 = str2.replace('~','');	str2 = str2.replace('´','');	str2 = str2.replace("+",'');	
	str2 = str2.replace("/","");	str2 = str2.replace("*","");	str2 = str2.replace("'","");	
	str2 = str2.replace(',','');	str2 = str2.replace('^','');	str2 = str2.replace('<','');	
	str2 = str2.replace('>','');

	switch(tipo_form){
		case 'agregar_contacto':
			$('#email_contacto').val(str2.toUpperCase());
			break;
		case 'telefono_agregar_contacto':
			$('#telefono_contacto').val(str2.toUpperCase());
			break;	
	}
}
/* Fin script espacios mail - Formulario Agregar Nuevo Contacto */

/* Script buscador remitente desde nombre completo - Formulario Agregar Nuevo Contacto */
$("#nombre_contacto").on("input",function(e){ // Accion que se activa cuando se digita #nombre_contacto
	$('#sugerencia_nombre_contacto').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
    oculta_errores();
    var envio_nombre_completo=$(this).val();
    
    if($(this).data("lastval")!= envio_nombre_completo){
    	$(this).data("lastval",envio_nombre_completo);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
	 		if(envio_nombre_completo.length>3 && envio_nombre_completo.length<50){
	    		$.ajax({
					type: 'POST',
					url:  'radicacion/radicacion_entrada/buscador_remitente.php',
					data: {
						'nombre_contacto': envio_nombre_completo
					},
					success: function(resp){
						$('#sugerencia_nombre_contacto').html(resp);						
					}
				})		 		
			}else{
				if(envio_nombre_completo.length>50){
					$('#error_max_nombre_contacto').slideDown("slow");
				}else{
					$('#error_min_nombre_contacto').slideDown("slow");
				}
			}	 				 
		},1000);
    };
});
/* Fin script buscador remitente desde nombre completo - Formulario Agregar Nuevo Contacto */
/* Script buscador remitente desde Nit - Formulario Agregar Nuevo Contacto */
$("#nit_contacto").on("input",function(e){ // Accion que se activa cuando se digita #nit_contacto
	$('#sugerencia_nit_contacto').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
    oculta_errores();
    var envio_nit = $(this).val();
    
    if($(this).data("lastval")!= envio_nit){
    	$(this).data("lastval",envio_nit);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
	 		if(envio_nit.length>2 && envio_nit.length<50){
	    		$.ajax({
					type: 'POST',
					url:  'radicacion/radicacion_entrada/buscador_remitente.php',
					data: {
						'search_nit': envio_nit
					},
					success: function(resp){
						$('#sugerencia_nit_contacto').html(resp);						
					}
				})		 		
			}else{
				if(envio_nit.length>30){
					$('#error_max_nit_contacto').slideDown("slow");
				}else{
					$('#error_min_nit_contacto').slideDown("slow");
				}
			}	 				 
		},1000);
    };
});
/* Fin script buscador remitente desde Nit - Formulario Agregar Nuevo Contacto */
/* Script buscador Ubicacion - Formulario Agregar Nuevo Contacto */
$("#ubicacion_contacto").on("input",function(e){ // Accion que se activa cuando se digita #ubicacion_contacto
	$('#sugerencia_ubicacion').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
    oculta_errores();
	var search_muni = $(this).val();
    
    if($(this).data("lastval")!= search_muni){
    	$(this).data("lastval",search_muni);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
	 		if(search_muni.length>2 && search_muni.length<50){
	    		$.ajax({
					type: 'POST',
					url:  'radicacion/radicacion_entrada/buscador_remitente.php',
					data: {'search_muni':search_muni
					},
					success: function(resp){
						if(resp!=""){
							$('#sugerencia_ubicacion').html(resp);
						}
					}
				})	 		
			}else{
				$('#sugerencia_ubicacion').html('');
			}	
			if(search_muni.length>50){
				$('#sugerencia_ubicacion').html('<h4>La busqueda debe tener 50 caracteres maximo. Revise por favor</h4>');
			}  				 
		},1000);
    };
});
/* Fin script buscador Ubicacion - Formulario Agregar Nuevo Contacto */
/* Script buscador Direccion - Formulario Agregar Nuevo Contacto */
$("#direccion_contacto").on("input",function(f){ // Accion que se activa cuando se digita #direccion_contacto
    oculta_errores();    
});
/* Fin script buscador Direccion - Formulario Agregar Nuevo Contacto */
/* Script buscador Telefono - Formulario Agregar Nuevo Contacto */
$("#telefono_contacto").on("input",function(g){ // Accion que se activa cuando se digita #direccion_contacto
    oculta_errores();    
});
/* Fin script buscador Telefono - Formulario Agregar Nuevo Contacto */
/* Script buscador Representante legal - Formulario Agregar Nuevo Contacto */
$("#representante_legal_contacto").on("input",function(g){ // Accion que se activa cuando se digita #direccion_contacto
    oculta_errores();    
});
/* Fin script buscador Representante legal - Formulario Agregar Nuevo Contacto */
/* Script para ocultar errores y continuar consulta - Formulario Agregar Nuevo Contacto */
function oculta_errores(){
	$("#error_nombre_contacto").slideUp("slow");
	$("#error_min_nombre_contacto").slideUp("slow");	
	$("#error_max_nombre_contacto").slideUp("slow");	
	$("#contacto_ya_existe").slideUp("slow");

	$('#error_no_es_numero').slideUp("slow");
	$("#error_nit_contacto").slideUp("slow");
	$("#error_min_nit_contacto").slideUp("slow");	
	$("#error_max_nit_contacto").slideUp("slow");	
	$("#nit_ya_existe").slideUp("slow");

	$("#error_ubicacion_contacto").slideUp("slow");
	$("#error_ubicacion_contacto2").slideUp("slow");
	$("#error_no_selecciona_ubicacion").slideUp("slow");
	$("#sin_muni").slideUp("slow");	

	$("#error_direccion").slideUp("slow");	
	$("#error_min_direccion").slideUp("slow");	
	$("#error_max_direccion").slideUp("slow");

	$("#valida_minimo_tel").slideUp("slow");	
	$("#valida_maximo_tel").slideUp("slow");	

	$("#valida_minimo_rep_legal").slideUp("slow");	
	$("#valida_maximo_rep_legal").slideUp("slow");	
}
/* Fin script para ocultar errores y continuar consulta - Formulario Agregar Nuevo Contacto */
/* Script para cargar el valor del municipio en el campo "ubicacion_contacto" - Formulario Agregar Nuevo Contacto */
function cargar_valor_municipio(nombre_municipio,nombre_departamento,nombre_pais,nombre_continente){
	$('#ubicacion_contacto').val(nombre_municipio+' ('+nombre_departamento+') '+nombre_pais+'-'+nombre_continente)
	$('#sugerencia_ubicacion').html("");
	oculta_errores();
	$('#direccion_contacto').focus();
}
/* Fin script para cargar el valor del municipio en el campo "ubicacion_contacto" - Formulario Agregar Nuevo Contacto */
/* Script que valida el nombre del contacto */
function valida_nombre_contacto(){
	var nombre_contacto = $('#nombre_contacto').val()
	if(nombre_contacto==""){
		$("#error_nombre_contacto").slideDown("slow");
		$("#error_min_nombre_contacto").slideUp("slow");
		$("#error_max_nombre_contacto").slideUp("slow");
		$("#contacto_ya_existe").slideUp("slow");
		return false;					
	}else{
		if(nombre_contacto.length<5){
			$("#error_nombre_contacto").slideUp("slow");
			$("#error_min_nombre_contacto").slideDown("slow");
			$("#error_max_nombre_contacto").slideUp("slow");
			$("#contacto_ya_existe").slideUp("slow");
			return false;	
		}else{
			if(nombre_contacto.length>40){
				$("#error_nombre_contacto").slideUp("slow");
				$("#error_min_nombre_contacto").slideUp("slow");
				$("#error_max_nombre_contacto").slideDown("slow");
				$("#contacto_ya_existe").slideUp("slow");
				return false;	
			}else{
				if($("#nombre_agregar_contacto").is(":visible")){
					$("#error_nombre_contacto").slideUp("slow");
					$("#error_min_nombre_contacto").slideUp("slow");
					$("#error_max_nombre_contacto").slideUp("slow");
					$("#contacto_ya_existe").slideDown("slow");
					return false;
				}else{
					$("#error_nombre_contacto").slideUp("slow");
					$("#error_min_nombre_contacto").slideUp("slow");
					$("#error_max_nombre_contacto").slideUp("slow");
					$("#contacto_ya_existe").slideUp("slow");
					return	true;
				}
			}	
		}
	}	
}
/* Fin script que valida el nombre del contacto */
/* Script que valida el nit del contacto */
function valida_nit_contacto(){
	var nit_contacto = $('#nit_contacto').val();
	if(nit_contacto==""){
		$("#error_nit_contacto").slideDown("slow");
		$("#error_min_nit_contacto").slideUp("slow");
		$("#error_max_nit_contacto").slideUp("slow");
		$("#error_no_es_numero").slideUp("slow");
		$("#nit_ya_existe").slideUp("slow");
		return false;	
	}else{
		if(nit_contacto.length<10){
			$("#error_nit_contacto").slideUp("slow");
			$("#error_min_nit_contacto").slideDown("slow");
			$("#error_max_nit_contacto").slideUp("slow");
			$("#error_no_es_numero").slideUp("slow");
			$("#nit_ya_existe").slideUp("slow");
			return false;
		}else{
			if(nit_contacto.length>30){
				$("#error_nit_contacto").slideUp("slow");
				$("#error_min_nit_contacto").slideUp("slow");
				$("#error_max_nit_contacto").slideDown("slow");
				$("#error_no_es_numero").slideUp("slow");
				$("#nit_ya_existe").slideUp("slow");
				return false;
			}else{
				if(isNaN(nit_contacto)){
					$("#error_nit_contacto").slideUp("slow");
					$("#error_min_nit_contacto").slideUp("slow");
					$("#error_max_nit_contacto").slideUp("slow");
					$("#error_no_es_numero").slideDown("slow");
					$("#nit_ya_existe").slideUp("slow");
					return false;
				}else{
					if($("#nit_agregar_contacto").is(":visible")){
						$("#error_nit_contacto").slideUp("slow");
						$("#error_min_nit_contacto").slideUp("slow");
						$("#error_max_nit_contacto").slideUp("slow");
						$("#error_no_es_numero").slideUp("slow");
						$("#nit_ya_existe").slideDown("slow");
						return false;
					}else{
						$("#error_nit_contacto").slideUp("slow");
						$("#error_min_nit_contacto").slideUp("slow");
						$("#error_max_nit_contacto").slideUp("slow");
						$("#error_no_es_numero").slideUp("slow");
						$("#nit_ya_existe").slideUp("slow");
						return true;
					}
				}
			}
		}
	}
}	
/* Fin script que valida el nit del contacto */
/* Script que valida la ubicacion del contacto */
function valida_ubicacion(){
	var ubicacion_contacto = $('#ubicacion_contacto').val();
	if(ubicacion_contacto==""){
		$("#error_ubicacion_contacto").slideUp("slow");
		$("#error_ubicacion_contacto2").slideDown("slow");
		$("#error_no_selecciona_ubicacion").slideUp("slow");
		return false;	
	}else{
		if(ubicacion_contacto.length<3){
			$("#error_ubicacion_contacto").slideDown("slow");
			$("#error_ubicacion_contacto2").slideUp("slow");
			$("#error_no_selecciona_ubicacion").slideUp("slow");
			return false;
		}else{
			if($("#muni_agregar_contacto").is(":visible")){
				$("#error_ubicacion_contacto").slideUp("slow");
				$("#error_ubicacion_contacto2").slideUp("slow");
				$("#error_no_selecciona_ubicacion").slideDown("slow");
				return false;
			}else{
				if($("#valida_ubicacion").is(":visible")){				
					$("#error_ubicacion_contacto").slideUp("slow");
					$("#error_ubicacion_contacto2").slideUp("slow");
					$("#error_no_selecciona_ubicacion").slideDown("slow");
					return false;
				}else{
					if($("#sin_muni").is(":visible")){
						$("#error_ubicacion_contacto").slideUp("slow");
						$("#error_ubicacion_contacto2").slideUp("slow");
						$("#error_no_selecciona_ubicacion").slideUp("slow");
						return false;
					}else{
						$("#error_ubicacion_contacto").slideUp("slow");
						$("#error_ubicacion_contacto2").slideUp("slow");
						$("#error_no_selecciona_ubicacion").slideUp("slow");
						return true;
					}
				}
			}
		}
	}
}	
/* Fin script que valida la ubicacion del contacto */	
/* Script que valida la direccion del contacto */
function valida_direccion(){
	var direc_contacto = $('#direccion_contacto').val();
	if(direc_contacto==""){
		$("#error_direccion").slideDown("slow");
		$("#error_min_direccion").slideUp("slow");
		$("#error_max_direccion").slideUp("slow");
		return false;	
	}else{
		if(direc_contacto.length<10){
			$("#error_direccion").slideUp("slow");
			$("#error_min_direccion").slideDown("slow");
			$("#error_max_direccion").slideUp("slow");
			return false;
		}else{
			if(direc_contacto.length>100){
				$("#error_direccion").slideUp("slow");
				$("#error_min_direccion").slideUp("slow");
				$("#error_max_direccion").slideDown("slow");
				return false;
			}else{
				$("#error_direccion").slideUp("slow");
				$("#error_min_direccion").slideUp("slow");
				$("#error_max_direccion").slideUp("slow");
				return true;
			}
		}
	}
}	
/* Fin script que valida la direccion del contacto */	
/* Funcion para validar telefono del contacto */
function validar_telefono(){
	var tel=$("#telefono_contacto").val();
	
    if(tel.length>0 && tel.length<7){
		$("#valida_minimo_tel").slideDown("slow");
		$("#valida_maximo_tel").slideUp("slow");
		return false;
    }else{
    	if(tel.length>40){
			$("#valida_minimo_tel").slideUp("slow");
			$("#valida_maximo_tel").slideDown("slow");
			return false;
    	}else{
			$("#valida_minimo_tel").slideUp("slow");
			$("#valida_maximo_tel").slideUp("slow");
			return true;	
    	}
    }
}
/* Fin funcion para validar telefono del contacto */
/* Funcion para validar formato de email (usuario@algunmail.com) */
function validar_correo_electronico(){
	var mail=$("#email_contacto").val();
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	
    if(mail.length>0 && mail.length <6){
		$("#valida_minimo_mail").slideDown("slow");
		$("#valida_maximo_mail").slideUp("slow");
    	$("#error_mail_formato").slideUp("slow");
		return false;
    }else{
    	if(mail.length>40){
			$("#valida_minimo_mail").slideUp("slow");
			$("#valida_maximo_mail").slideDown("slow");
    		$("#error_mail_formato").slideUp("slow");
    	}else{
		    if(mail.length>0 && !expr.test(mail)){
				$("#valida_minimo_mail").slideUp("slow");
				$("#valida_maximo_mail").slideUp("slow");
				$("#error_mail_formato").slideDown("slow");
		    	return false;
		    }else{	
				$("#error_mail_formato").slideUp("slow");
				$("#valida_minimo_mail").slideUp("slow");
				$("#valida_maximo_mail").slideUp("slow");
				return true;
			}	
    	}
    }
}
/* Fin funcion para validar formato de email (usuario@algunmail.com) */
/* Funcion para validar Representante Legal del contacto */
function validar_rep_legal(){
	var rep=$("#representante_legal_contacto").val();
	
    if(rep.length>0 && rep.length<7){
		$("#valida_minimo_rep_legal").slideDown("slow");
		$("#valida_maximo_rep_legal").slideUp("slow");
		return false;
    }else{
    	if(rep.length>40){
			$("#valida_minimo_rep_legal").slideUp("slow");
			$("#valida_maximo_rep_legal").slideDown("slow");
			return false;
    	}else{
			$("#valida_minimo_rep_legal").slideUp("slow");
			$("#valida_maximo_rep_legal").slideUp("slow");
			return true;	
    	}
    }
}
/* Fin funcion para validar Representante Legal del contacto */
/* Funcion para Grabar Usuarios */
function validar_grabar_contacto(){
	var validar_nombre = valida_nombre_contacto();
	if(validar_nombre==false){
		$("#nombre_contacto").focus()
		return false;
	}else{
		var validar_nit=valida_nit_contacto();
		if(validar_nit==false){
			$("#nit_contacto").focus();
			return false;
		}else{
			var validar_ubicacion=valida_ubicacion();
			if(validar_ubicacion==false){
				$("#ubicacion_contacto").focus();
				return false;
			}else{
				var validar_direccion=valida_direccion();
				if(validar_direccion==false){
					$("#direccion_contacto").focus();
					return false;
				}else{
					var validar_email=validar_correo_electronico();
					if(validar_email==false){
						$("#email_contacto").focus();
						return false;
					}else{
						var valida_tel=validar_telefono();
						if(valida_tel==false){
							$("#telefono_contacto").focus();
							return false;
						}else{
							var valida_rep=validar_rep_legal();
							if(valida_rep==false){
								$("#representante_legal_contacto").focus();
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
}	
$(function submit_grabar_contacto(){
	$('#grabar_contacto').click(function submit_grabar_contacto(){
		if($('.imagen_logo').is(":visible")){
			// sweetAlert({
			Swal.fire({	
				position 			: 'top-end',
			    showConfirmButton 	: false,
			    timer 				: 1500,	
			    title 				: 'La consulta se está ejecutando.',
			    text  				: 'Un momento por favor.',
			    type 				: 'warning'
			});					
		}else{
			var submit_grabar_contacto=validar_grabar_contacto();
			if(submit_grabar_contacto==false){
				return false;
			}else{
				$('#form_datos_contacto').submit(); // Realizar la creación del contacto
			}								
		}
	});
})
/* Fin funcion para Grabar Usuarios */ 
/* Funciones para guardar en base de datos auditoria de modificacion o creacion de contactos */
function auditoria(tipo_formulario,nombre_contacto_creado){
	switch(tipo_formulario){
		case'modificar_contacto':
		var trans ="El Contacto ha sido modificado";
			break;
		case'radicacion_entrada':
		var trans ="Documento radicado";	
			break;		
	}
	$.ajax({	// Guardo registro de ingreso al sistema para auditoria
		type: 'POST',
		url: 'login/transacciones.php',
		data: {
			'transaccion' : tipo_formulario,
			'creado' : 	nombre_contacto_creado
		},			
		success: function(resp1){
			if(resp1=="true"){
				alert(trans+" correctamente");
				if(trans=="Documento radicado"){

				}else{
					volver();
				}
			}else{
				alert(resp1)
			}
		}
	})
}
function volver(){
	window.location.href='../principal3.php'
}	
/* Fin funciones para guardar en base de datos auditoria de modificacion o creacion de contactos */
/* Funcion para ir atras en Radicacion de entrada */
function atras(){
	$("#contenido").load("radicacion/radicacion_salida/index_salida.php");
}
/* Fin funcion para ir atras en Radicacion de entrada */

/*******************************************************************************************************/
/* Funciones para formulario de entrada ****************************************************************/
/*******************************************************************************************************/
/* Funcion para cargar datos en formulario radicacion entrada */
function cargar_formulario_radicacion_entrada(nombre_contacto,nit_contacto,ubicacion_contacto,direccion_contacto,telefono_contacto,mail_contacto,representante_legal,codigo_contacto){
	$('#contenido').load('radicacion/radicacion_salida/entrada.php',{var1:nombre_contacto, var2:nit_contacto, var3:ubicacion_contacto, var4:direccion_contacto, var5:telefono_contacto, var6:mail_contacto, var7:representante_legal, var8:codigo_contacto})
	desplegable_terminos();
}
/* Fin funcion para cargar datos en formulario radicacion entrada */
/* Funcion para cambiar o mantener el input adress (direccion) para enviar con el formulario radicacion de entrada */
function cambia_direccion(direccion_recibida){
	$('#dir').html("<input type='text' name='dir_contacto' id='dir_contacto' onblur='carga_direccion()' onkeyup=\"espacios_formulario_contactos('direc_contacto')\">");
	$("#error_direccion").slideUp("slow");
	$("#error_min_direccion").slideUp("slow");
	$("#error_max_direccion").slideUp("slow");
	$('#dir_contacto').val(direccion_recibida);
	$('#dir_contacto').focus();
}
function carga_direccion(){
	var direccion=$('#dir_contacto').val(), direccion=direccion.trim(); // Quito espacios al principio y final
	if(direccion==""){
		$("#error_direccion").slideDown("slow");
		$("#dir_contacto").focus();
		return false;
	}else{
		$('#adress').val(direccion);
		$("#dir").html(direccion);
		$("#dignatario").focus();
		$("#error_direccion").slideUp("slow");
		$("#error_min_direccion").slideUp("slow");
		$("#error_max_direccion").slideUp("slow");
	}
}
/* Fin funcion para cambiar o mantener el input adress (direccion) para enviar con el formulario radicacion de entrada */
/* Funcion para cambiar o mantener el input dignatario2 (Representante Legal) para enviar con el formulario radicacion de entrada */
function cambia_dignatario(destinatario_recibido){
	$('#dign').html("<input type='text' name='dignatario' id='dignatario' onblur='carga_dign()' onkeyup=\"espacios_formulario_contactos('dignatario2')\">");
	
	oculta_errores_rad();
	
	$('#dignatario').val(destinatario_recibido);
	$('#dignatario').focus();
}
function carga_dign(){
	var dign2=$('#dignatario').val(), dign2=dign2.trim(); // Quito espacios al principio y final
	if(dign2==""){
		$("#error_dignatario").slideDown("slow");
		$("#dignarario").focus();
		return false;
	}else{
		$('#dignatario2').val(dign2);
		$("#dign").html(dign2);
		
		oculta_errores_rad();
		
		$("#asunto").focus();
	}
}
/* Fin funcion para cambiar o mantener el input dignatario2 (Representante Legal) para enviar con el formulario radicacion de entrada */
function oculta_errores_rad(){
	$("#error_dignatario").slideUp("slow");
	$("#error_min_dignatario").slideUp("slow");
	$("#error_max_dignatario").slideUp("slow");

	$("#sin_distribuidor").slideUp("slow");
	$("#sin_dependencia").slideUp("slow");
	$("#error_dependencia_destino").slideUp("slow");

	$("#dependencia_real").slideUp("slow");
	$('#min_depe_destino').slideUp("slow");
}
/* Script buscador dependencia_destino - Formulario Radicacion de Entrada */
$("#dependencia_destino").on("input",function(e){ // Accion que se activa cuando se digita #dependencia_destino
	$('#sugerencias_dependencia_destino').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
    oculta_errores_rad();
	var search_depe = $(this).val();
    
    if($(this).data("lastval")!= search_depe){
    	$(this).data("lastval",search_depe);
                
		clearTimeout(timerid);
		timerid = setTimeout(function(){
	 		if(search_depe.length>2 && search_depe.length<50){
	    		$.ajax({
					type: 'POST',
					url:  'radicacion_radicacion_entrada/buscador_remitente.php',
					data: {
						'search_depe':search_depe
					},
					success: function(resp){
						if(resp!=""){
							$('#sugerencias_dependencia_destino').html(resp);
						}
					}
				})	 		
			}else{
				$('#min_depe_destino').slideDown("slow");
			}	
			if(search_depe.length>50){
				$('#sugerencias_dependencia_destino').html('<h4>La busqueda debe tener 50 caracteres maximo. Revise por favor</h4>');
			}  				 
		},1000);
    };
});
/* Fin script buscador dependencia_destino - Formulario Radicacion de Entrada */
/* Script para cargar dependencia en Formulario Radicacion de Entrada */
function cargar_valor_dependencia(depe_codigo,depe_nomb){
	$("#depe_rad").html('');
	$("#dependencia_real").slideUp("slow");
	$("#dependencia_destino").val(depe_nomb);
	$("#depe_codi").val(depe_codigo);
	$.ajax({
		type: 'POST',
		url:  'radicacion/radicacion_entrada/buscador_remitente.php' ,
		data: {
			'search_dest_depe': depe_codigo
		},
		success: function(resp){
			if(resp!=""){
				$('#sugerencias_dependencia_destino').html(resp);
			}
		}
	})	
}
/* Fin script para cargar dependencia en Formulario Radicacion de Entrada */
/* Funcion para cargar tiempo de tramite - Formuario radicacion entrada */
function muestra_termino(){
	var ter=$("#termino").val();
	$('#termino_td').html('<b>'+ter+' dias habiles de tramite</b>')
}
/* Funcion para cargar tiempo de tramite - Formulario radicacion entrada */
/* Funcion para validar campo direccion_rad_entrada */
function valida_dir_entrada(){
	if($("#error_direccion").is(":visible")){
		return false;
	}else{	
		var dir_ent=$("#adress").val();
		if(dir_ent==""){
			$("#error_direccion").slideDown("slow");
			$("#error_min_direccion").slideUp("slow");
			$("#error_max_direccion").slideUp("slow");
			return false;
		}else{
			if(dir_ent.length<5){
				$("#error_direccion").slideUp("slow");
				$("#error_min_direccion").slideDown("slow");
				$("#error_max_direccion").slideUp("slow");
				return false;
			}else{
				if(dir_ent.length>100){
					$("#error_direccion").slideUp("slow");
					$("#error_min_direccion").slideUp("slow");
					$("#error_max_direccion").slideDown("slow");
					return false;
				}else{
					$("#error_direccion").slideUp("slow");
					$("#error_min_direccion").slideUp("slow");
					$("#error_max_direccion").slideUp("slow");
					return true;
				}
			}
		}
	}
}

/* Fin funcion para validar campo direccion_rad_entrada */
/* Funcion para validar campo dignatario_rad_entrada */
function valida_dignatario_entrada(){
	if($("#error_dignatario").is(":visible")){
		return false;
	}else{	
		var dign_ent=$("#dignatario2").val();
		if(dign_ent==""){
			$("#error_dignatario").slideDown("slow");
			$("#error_min_dignatario").slideUp("slow");
			$("#error_max_dignatario").slideUp("slow");
			return false;
		}else{
			if(dign_ent.length<5){
				$("#error_dignatario").slideUp("slow");
				$("#error_min_dignatario").slideDown("slow");
				$("#error_max_dignatario").slideUp("slow");
				return false;
			}else{
				if(dign_ent.length>100){
					$("#error_dignatario").slideUp("slow");
					$("#error_min_dignatario").slideUp("slow");
					$("#error_max_dignatario").slideDown("slow");
					return false;
				}else{
					$("#error_dignatario").slideUp("slow");
					$("#error_min_dignatario").slideUp("slow");
					$("#error_max_dignatario").slideUp("slow");
					return true;
				}
			}
		}
	}
}

/* Fin funcion para validar campo dignatario_rad_entrada */
/* Script que valida el asunto_rad_entrada */
function valida_asunto(){
	var asu = $('#asunto').val()
	if(asu==""){
		$("#error_asunto").slideDown("slow");
		$("#error_min_asunto").slideUp("slow");
		$("#error_max_asunto").slideUp("slow");
		return false;					
	}else{
		if(asu.length<5){
			$("#error_asunto").slideUp("slow");
			$("#error_min_asunto").slideDown("slow");
			$("#error_max_asunto").slideUp("slow");
			return false;	
		}else{
			if(asu.length>300){
				$("#error_asunto").slideUp("slow");
				$("#error_min_asunto").slideUp("slow");
				$("#error_max_asunto").slideDown("slow");
				return false;	
			}else{
				$("#error_asunto").slideUp("slow");
				$("#error_min_asunto").slideUp("slow");
				$("#error_max_asunto").slideUp("slow");
				return	true;	
			}	
		}
	}	
}
/* Fin script que valida el asunto_rad_entrada */
/* Script que valida el dependencia_destino */
function valida_depe_destino(){
	var depe_dest = $('#dependencia_destino').val();
	if(depe_dest==""){
		$("#error_dependencia_destino").slideDown("slow");
		$("#dependencia_real").slideUp("slow");
		$("#dependencia_destino").focus();
		return false;					
	}else{
		$("#error_dependencia_destino").slideUp("slow");
		if($("#sin_distribuidor").is(":visible")){
			$("#dependencia_destino").focus();
			return false;
		}else{
			if($("#sin_dependencia").is(":visible")){
				$("#dependencia_destino").focus();
				return false;
			}else{
				if($("#depe_rad").is(":visible")){
					$("#dependencia_real").slideDown("slow");
					return false;
				}else{
					return true;	
				}
			}
		}
	}	
}
/* Fin script que valida el dependencia_destino */
/* Funcion para enviar formulario radicacion entrada */
function validar_radicacion_entrada(){
	var validar_dir_entrada=valida_dir_entrada();
	if(validar_dir_entrada==false){
		$("#dir_contacto").focus();
		return false;
	}else{
		var valida_dignatario_ent=valida_dignatario_entrada();
		if(valida_dignatario_ent==false){
			$("#dign").focus();
			return false;
		}else{
			var val_asunto=valida_asunto();
			if(val_asunto==false){
				$("#asunto").focus();
				return false;
			}else{
				var val_dest=valida_depe_destino();
				if(val_dest==false){
					$("#dependencia_destino").focus();
					return false;
				}else{
					return true;
				}
			}
		}
	}
}
$(function submit_grabar_radicacion(){
	$('#envia_rad_entrada').click(function submit_grabar_radicacion(){
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
			var submit_grabar_rad=validar_radicacion_entrada();
			if(submit_grabar_rad==false){
				return false;
			}else{
			/* Funcion para enviar datos a radicar.php y Realizar la radicacion respectiva. */
				var tipo_radicacion=$("#tipo_radicacion").val(); // Tipo de radicado (2- Entrada, 1-Salida, etc)
				var nombre_contacto=$("#nombre_contacto").val();
				var adress=$("#adress").val();
				var dignatario2=$("#dignatario2").val();
				var codigo_contacto=$("#codigo_contacto").val();
				var asunto=$("#asunto").val();
				var termino=$("#termino").val();
				var descripcion_anexos=$("#descripcion_anexos").val(); // Anexos que llegan (usb, cd, caja, etc)
				var depe_codi=$("#depe_codi").val();
				var dependencia_destino=$("#dependencia_destino").val();	
				var usuario_destino=$("#usuario_destino").val();
				
				$('#contenido').load(
					'radicacion/radicacion_entrada/radicar.php',{
						tipo_radicacion:tipo_radicacion, nombre_contacto:nombre_contacto, adress:adress, 
						dignatario2:dignatario2, codigo_contacto:codigo_contacto, asunto:asunto, termino:termino,
						descripcion_anexos:descripcion_anexos, depe_codi:depe_codi, dependencia_destino:dependencia_destino,
						usuario_destino:usuario_destino
					}
				)		
			/* Fin funcion para enviar datos a radicar.php y Realizar la radicacion respectiva. */
			}									
		}
	});
})
/* Fin funcion para enviar formulario radicacion entrada */ 
/* Funcion para comprobar si radicado existe en carpeta bodega */
function verifica_bodega(radicado){
	$.ajax({
		type: 'POST',
		url:  'radicacion/radicacion_entrada/verifica_existe_pdf.php' ,
		data: {
			'radicado' 	: radicado,
			'accion' 	: 'verifica_bodega'
		},
		success: function(resp){
			console.log(resp)
			if(resp!=""){
				$('#comprueba_pdf').html(resp);
			}
		}
	})
}
/* Fin funcion para comprobar si radicado existe en carpeta bodega */
/* Funcion para cargar el archivo principal a la bodega */
function cargar_archivo_principal(radicado){
	$.ajax({
		type: 'POST',
		url:  'radicacion/radicacion_entrada/verifica_existe_pdf.php' ,
		data: {
			'radicado': radicado,
			'accion':'cargar_archivo_principal'
		},
		success: function(resp){
			console.log(resp)
			if(resp!=""){
				alert(resp)
				//$('#comprueba_pdf').html(resp);
			}
		}
	})
}
/* Fin funcion para cargar el archivo principal a la bodega */