/* Script para ventana modal - Tecla Esc */
    window.addEventListener("keydown", function(event){
        var codigo = event.keyCode || event.which;
        if (codigo == 27){
          //  cerrar_ventana_agregar_contacto();
          //  cerrar_ventana_modifica_contacto();
        }
        if(codigo== 8){ // Opcion para restringir que la tecla backspace da atras en el navegador.
        	if (history.forward(1)) {
				location.replace(history.forward(1));
			}	
        }
    }, false);
/* Fin script para ventana modal - Tecla Esc */
function valida_checkbox() {
	var titulo="Buscar por ";
	if ($('#por_radicado').is(":checked")){
		$("#por_rad").val("SI");
		titulo="Buscar por número del <b>radicado</b>, por numero de Guia-Oficio o por el asunto del <b>radicado</b> ";

		if ($('#por_expediente').is(":checked")){
		  	$("#por_exp").val("SI");
		  	titulo=titulo+" - por número del <b>expediente</b> o asunto del <b>expediente</b> ";
		  	
		  	if ($('#por_inventario').is(":checked")){
				$("#por_inv").val("SI");
				titulo=titulo+" - por número radicado del <b>inventario</b>, metadato o asunto del <b>inventario</b>";
			}else{
			  	$("#por_inv").val("NO");
			}

		}else{
		  	$("#por_exp").val("NO");
			titulo="Buscar por número del <b>radicado</b>, por numero de Guia-Oficio o por el asunto del <b>radicado</b>";

			if ($('#por_inventario').is(":checked")){
				$("#por_inv").val("SI");
				titulo=titulo+" - por número radicado del <b>inventario</b>, metadato o asunto del <b>inventario</b>";
			}else{
			  	$("#por_inv").val("NO");
				titulo="Buscar por número de <b>radicado</b>, por numero de Guia-Oficio o por el asunto del <b>radicado</b> ";
			}		  	
		}
	}else{
		$("#por_rad").val("NO");
		
		if ($('#por_expediente').is(":checked")){
		  	$("#por_exp").val("SI");
		  	titulo=" Buscar por número del <b>expediente</b> o asunto del <b>expediente</b>";

		  	if ($('#por_inventario').is(":checked")){
				$("#por_inv").val("SI");
				titulo=titulo+" - por número radicado del <b>inventario</b>, metadato o asunto del <b>inventario</b>";
			}else{
			  	$("#por_inv").val("NO");
			  	titulo="Buscar por número del <b>expediente</b> o asunto del <b>expediente</b>";
			}
		}else{
		  	$("#por_exp").val("NO");
		  	if ($('#por_inventario').is(":checked")){
				$("#por_inv").val("SI");
				titulo="Buscar por número radicado del <b>inventario</b>, metadato o asunto del <b>inventario</b>";
			}else{
			  	$("#por_inv").val("NO");
			  	titulo="";
			}		  	
		}		
	}
	
	if(titulo==""){
		titulo="Seleccione por lo menos un parametro de búsqueda";
	}

	validar_envio_consulta()
	$("#titulo_buscador").html(titulo);
	$("#search_buscador").focus();
}

function validar_envio_consulta(){
	$("#boton_buscador_general").slideDown("slow");
	$("#error_campos_vacio").slideUp("slow");
	$("#valida_minimo_busqueda").slideUp("slow");
	$("#valida_maximo_busqueda").slideUp("slow");
	$("#resultados").html("");
	$("#resultados2").html("");
	$("#resultados3").html("");
}
function validar_campo(valor, input){
	switch(input){
		case 'search_buscador':
			if(valor==""){
				$("#error_campos_vacio").slideDown("slow");
				$("#valida_minimo_busqueda").slideUp("slow");
				$("#valida_maximo_busqueda").slideUp("slow");
				$(".imagen_logo").slideUp("slow");
				return false;
			}else{
				if(valor.length<6){
					$("#error_campos_vacio").slideUp("slow");
					$("#valida_minimo_busqueda").slideDown("slow");
					$("#valida_maximo_busqueda").slideUp("slow");
					$(".imagen_logo").slideUp("slow");
					return false;
				}else{
					if(valor.length>50){
						$("#error_campos_vacio").slideUp("slow");
						$("#valida_minimo_busqueda").slideUp("slow");
						$("#valida_maximo_busqueda").slideDown("slow");
						$(".imagen_logo").slideUp("slow");
						return false;
					}else{
						$("#error_campos_vacio").slideUp("slow");
						$("#valida_minimo_busqueda").slideUp("slow");
						$("#valida_maximo_busqueda").slideUp("slow");
						return true;
					}
				}
			}
		break;
	}
}
function buscador_general(){
	$("#boton_buscador_general").slideUp("slow");

//	$("#resultados").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");	
	var busqueda=$("#search_buscador").val();
	var por_rad=$("#por_rad").val();
	var por_exp=$("#por_exp").val();
	var por_inv=$("#por_inv").val();

//	alert(por_rad+por_exp+por_inv)
	if(validar_campo(busqueda,'search_buscador')==true){

		if(por_rad=="SI"){
			// $("#resultados").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");	
			loading("resultados");
			search_all('por_rad',busqueda,por_rad,por_exp,por_inv);
			if(por_exp=="SI"){
				// $("#resultados2").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");	
				loading("resultados2");
				search_all('por_exp',busqueda,por_rad,por_exp,por_inv);
				if(por_inv=="SI"){
					// $("#resultados3").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");	
					loading("resultados3");
					search_all('por_inv',busqueda,por_rad,por_exp,por_inv);
				}
			}else{
				if(por_inv=="SI"){
					// $("#resultados3").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");	
					loading("resultados3");
					search_all('por_inv',busqueda,por_rad,por_exp,por_inv);
				}
			}
		}else{
			if(por_exp=="SI"){
				// $("#resultados2").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");	
				loading("resultados2");
				search_all('por_exp',busqueda,por_rad,por_exp,por_inv);
				if(por_inv=="SI"){
					// $("#resultados3").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");	
					loading("resultados3");
					search_all('por_inv',busqueda,por_rad,por_exp,por_inv);
				}
			}else{
				if(por_inv=="SI"){
					// $("#resultados3").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");	
					loading("resultados3");
					search_all('por_inv',busqueda,por_rad,por_exp,por_inv);
				}
			}
		}
	}	
	$("#search_buscador").focus();
}
function search_all(tipo_busqueda,busqueda,por_rad,por_exp,por_inv){
	if(por_rad=='NO'){
		$('#resultados').slideUp('slow');
	}
	if(por_exp=='NO'){
		$('#resultados2').slideUp('slow');
	}
	if(por_inv=='NO'){
		$('#resultados3').slideUp('slow');
	}

	$.ajax({
		url:"buscador_general/buscador_general.php",
		type:"POST",
		data:{
			'search_buscador' : busqueda,
			'tipo_busqueda': tipo_busqueda,
			'por_rad' : por_rad,
			'por_exp' : por_exp,
			'por_inv' : por_inv
		},
		success: function(respuesta){
			switch(tipo_busqueda){
				case 'por_rad':
					$("#resultados").slideDown("slow");
					$("#resultados").html(respuesta);
				break;				
				case 'por_exp':
					$("#resultados2").slideDown("slow");
					$("#resultados2").html(respuesta);
				break;
				case 'por_inv':
					$("#resultados3").slideDown("slow");
					$("#resultados3").html(respuesta);
				break;
			}
		}
	});	
}

function volver_busqueda(){
    $("#contenedor").slideDown("slow");
    $("#resultados4").slideUp("slow");
    $("#volver_resultados").slideUp("slow");
    
    $("#search_buscador").focus();

	/* Ocultar las opciones para archivar, derivar, informar y/o resignar radicado */
    $('#archivar_radicado').removeClass('img');       
    $('#archivar_radicado').addClass('img1');   
    $('#derivar_radicado').removeClass('img');       
    $('#derivar_radicado').addClass('img1');   
    $('#informar_radicado').removeClass('img1');       
    $('#informar_radicado').addClass('img1');   
    $('#reasignar_radicado').removeClass('img');       
    $('#reasignar_radicado').addClass('img1');   
    $('#responder_radicado').removeClass('img');       
    $('#responder_radicado').addClass('img1');        
}
/* Funcion para mostrar la informacion general */
$(function carga_informacion_general(){
    $('#boton_informacion_general').click(function carga_informacion_general(){
        var radicado=$("#radicado").val();  

        $.ajax({
            type: 'POST',
            url: 'bandejas/entrada/pestanas.php',
            data: {
                'pestana' : 'informacion_general',
                'radicado' : radicado  
            },          
            success: function(resp1){
                if(resp1!=""){
                    $("#informacion_general").html(resp1);
                }
            }
        })   
                                   
    });
}) 
        
$(function carga_historico(){ // Cargar pestaña historico en acordeon
    $('#boton_historico').click(function carga_historico(){
        var radicado=$("#radicado").val();  
        var expediente=$("#expediente").val();  

        $.ajax({
            type: 'POST',
            url: 'bandejas/entrada/pestanas.php',
            data: {
            	'pestana' 		: 'historico',
                'radicado' 		: radicado,
                'expediente' 	: expediente 
            },  
            success: function(resp1){
                if(resp1!=""){
                    $("#historico").html(resp1);
                }
            }
        })  
    });     
})

/* Fin funcion para mostrar la informacion general */