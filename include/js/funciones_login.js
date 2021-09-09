/* Javascript Login */

$(function main(){
	$("#user").focus();	
	
	function valida_entra(){
		 $("#user").keyup(function(e) {
	       if(e.which == 13) {
	          entra();
	       }
	    }); 
	}

	var codigo_entidad 	= $("#cod_ent").val();
	var ver 			= $("#ver").val();

	switch(codigo_entidad){
		case 'AV1': 
			$("#nombre_entidad").html("<br>Alcaldía de Villeta");
			$("#version").html(ver);
			$("#cont_logo_empresa").html("<img id='logo_empresa' src='imagenes/logos_entidades/logo_largo_av1.png' style='background-color: #FFFFFF; border-radius: 20px;'>");
			$(".form").css("top","5%"); /* Se baja el formulario 5%*/
			break;	

		case 'EJC':
		case 'EJEC':
			$("#title_img").html("<title>Ingreso a la actualización del Software de Gestion Documental del Ejercito Nacional</title><link href='imagenes/logos_entidades/imagen_qr_ejc.png' type='image/x-icon' rel='shortcut icon'/>");  /*Quita el logo de Jonas*/

			$("#encabezado_transparente2").html("<br>");  /*Quita el logo de Jonas*/
			$("#nombre_entidad").html("<br>Ejército Nacional");
			$("#cont_logo_empresa").html("<img id='logo_empresa' src='imagenes/logos_entidades/logo_largo_ejc.png' style='background-color: #FFFFFF; border-radius: 20px; width: 25%;'>");
			$("#version").html("<b>Version (2021 - 01)</b>");
			$("#boton_ingreso").css("background-color","#903f3a");
			$(".form").css("top","10%");
			// $("#boton_ingreso").attr('background', 'red')
			break;

		case 'JBB': 
			$("#nombre_entidad").html("<br>Jardin Botánico de Bogotá");
			$("#cont_logo_empresa").html("<img id='logo_empresa' src='imagenes/logos_entidades/logo_largo_jbb.png' style='background-color: #FFFFFF; border-radius: 20px; width: 25%;'>");
			$("#version").html(ver);
			break;

		case 'L01': 
			$("#nombre_entidad").html("<br>Litigando Punto Com");
			$("#version").html(ver);
			$("#cont_logo_empresa").html("<img id='logo_empresa' src='imagenes/logos_entidades/logo_largo_l01.png' style='background-color: #FFFFFF; border-radius: 20px; width: 25%;'>");
			$(".form").css("top","5%"); /* Se baja el formulario 5%*/
			break;	

		default:
			$("#cont_logo_empresa").html("<img id='logo_empresa' src='imagenes/iconos/logo_largo.png' style='background-color: #FFFFFF; border-radius: 20px; width: 25%;'>");
			$("#version").html(ver);
			break;	
	}
})
function upper_user(){
	var str = $('#user').val();	
	str = str.replace(' ','');
	str = str.replace('*','');
	str = str.replace('<','');
	str = str.replace('>','');
	str = str.replace('+','');
	str = str.replace('/','');
	str = str.replace('(','');
	str = str.replace(')','');
	$('#user').val(str.toUpperCase());
}

function entra(){
	var festivo = $("#festivo").val();
	var passd 	= $("#pass").val();
	var user 	= $("#user").val();
	var cod_ent = $("#cod_ent").val();
	var ver 	= $("#ver").val();

	$.ajax({
		type: 'POST',
		url: 'login/index.php',
		data: {
			'cod_ent' 	: cod_ent,
			'festivo' 	: festivo,
			'passd_j' 	: passd,
			'user_j' 	: user,
			'ver' 	 	: ver
		},			
		success: function(resp){
			switch(resp){ // Valida la respuesta de login/index.php
				case "No pude conectarme con la base de datos 1, revisa las variables de conexión por favor.":	
					alert(resp);
					break;
				case "false":
					alert("No pude conectarme a la tabla U de la base de datos 1, revisa la base de datos por favor");// Quiere decir que la tabla de usuarios no se ha creado.
					break;	
				case "":
					$('#error_user').slideDown("slow");
					$('#error_inactivo').slideUp("slow");
					break;
				case "inactivo":
					$('#error_inactivo').slideDown("slow");
					$('#error_user').slideUp("slow");
					break;	
				default:       // Ingresa de manera correcta
					$('#error_user').slideUp("slow");
					$('#error_inactivo').slideUp("slow");	
		
					$.ajax({	// Guardo registro de ingreso al sistema para auditoria
						type: 'POST',
						url: 'login/transacciones.php',
						data: {
							'transaccion' : 'login'	
						},			
						success: function(resp1){
							if(resp1=="true"){
								location.href='principal3.php';			
							}else{
								alert(resp1)
							}
						}
					})	
					break;	
			}	
		}
	})
}
$(document).bind("contextmenu",function(e){	// Con esta funcion se inhabilita el menu contextual del click derecho del mouse
    return false;		
});