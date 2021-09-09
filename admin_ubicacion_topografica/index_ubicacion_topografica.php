<?php 
	require_once("../login/validar_inactividad.php");
	require_once('../login/conexion2.php') // Para la consulta del nivel
?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Buscador de Ubicacion Topográfica</title>
	<script type="text/javascript" src="include/js/funciones_ubicacion_topografica.js"></script>
	<!-- <link rel="stylesheet" href="include/css/estilos_ubicacion_topografica.css"> -->
<!-- Inicio js ubicacion_topografica -->
<script type="text/javascript">
/******************************************************************************************/
/* Inicio script para buscador input formularios ubicacion_topografica ********************/
/******************************************************************************************/

var timerid="";
$(function buscar_input_ubicacion_topografica(){
	$('#search_nivel').focus();

	$('#search_nivel').on("input",function(e){ // Accion que se activa cuando se digita #search_nivel desde Formulario Agregar Nuevo Nivel
		loading("desplegable_resultados");
		$(".errores").slideUp("slow");
		
		var envio_nivel = $(this).val();
			
		if ($(this).data("lastval")!=envio_nivel) {
			$(this).data("lastval",envio_nivel);

			clearTimeout(timerid);
			timerid = setTimeout(function(){
				validar_input('search_nivel')
				if(envio_nivel.length>2 && envio_nivel.length<51){
					$.ajax({
						type: 'POST',
						url: 'admin_ubicacion_topografica/buscador_ubicacion_topografica.php',
						data: {
							'search_nivel' : envio_nivel
						},			
						success: function(resp){
							if(resp!=""){
								$('#desplegable_resultados').html(resp);
							}
						}
					})
				} 
			},1000);
		};		
	});
	$("#nombre_nivel").on("input",function(e){ // Accion que se activa cuando se digita #nombre_nivel desde Formulario Agregar Nuevo Nivel
	    loading("sugerencia_nombre_nivel");
		$(".errores").slideUp("slow");

	    var nom_niv = $(this).val();
	    
	    if($(this).data("lastval")!= nom_niv){
	    	$(this).data("lastval",nom_niv);
	                
			clearTimeout(timerid);
			timerid = setTimeout(function() {
				validar_input('nombre_nivel');
				if(nom_niv.length>2 && nom_niv.length<51){
	        		$.ajax({
						type: 'POST',
						url: 'admin_ubicacion_topografica/buscador_ubicacion_topografica.php',
						data: {
							'search_nom_nivel' : nom_niv
						},			
						success: function(resp){
							if(resp!=""){
								$('#sugerencia_nombre_nivel').html(resp);
							}
						}
					})	 		
				}else{
					$('#sugerencia_nombre_nivel').html("");
				}								 
			},1000);
	    };
	});


/* Script para buscador nivel por nombre en formulario Modificar Nivel */
	$('#mod_nombre_nivel').on("input",function(e){ // Accion que se activa cuando se digita #mod_nombre_nivel
		loading('sugerencia_mod_nombre_nivel');
		$(".errores").slideUp("slow");

		var search_nombre_mod_nivel 		= $(this).val().toUpperCase();
		var search_antiguo_nombre_mod_nivel = $("#antiguo_nombre_nivel").val();
		
		clearTimeout(timerid);
		timerid = setTimeout(function(){
			if(search_antiguo_nombre_mod_nivel!=search_nombre_mod_nivel) {

				validar_input('mod_nombre_nivel')

				if(search_nombre_mod_nivel.length>2 && search_nombre_mod_nivel.length<51){
					$.ajax({
						type: 'POST',
						url: 'admin_ubicacion_topografica/buscador_ubicacion_topografica.php',
						data: {
							'search_nombre_mod_nivel' 			: search_nombre_mod_nivel,
							'search_antiguo_nombre_mod_nivel' 	: search_antiguo_nombre_mod_nivel,
							'desde_formulario' 					: '1'
						},			
						success: function(resp){
							if(resp!=""){
								$('#sugerencia_mod_nombre_nivel').html(resp);
							}
						}
					})
				}else{
					// $('#sugerencia_mod_nombre_nivel').html('kkkkk');		
					loading('sugerencia_mod_nombre_nivel');
				}
				 
			}else{
				$('#sugerencia_mod_nombre_nivel').html('');
			}	
		},1000);
	});
/* Fin script para buscador nivel por nombre en formulario Modificar Nivel */

	$("#nivel_padre").on("input",function(e){ // Accion que se activa cuando se digita #nivel_padre desde Formulario Agregar Nuevo Nivel
		loading("sugerencia_nivel_padre");
		$(".errores").slideUp("slow");

	    var nivel_padre = $(this).val();
	    
	    if($(this).data("lastval")!= nivel_padre){
	    	$(this).data("lastval",nivel_padre);
	                
			clearTimeout(timerid);
			timerid = setTimeout(function() {
				validar_input('nivel_padre')
				
	     		if(nivel_padre.length<51){
	        		$.ajax({
						type: 'POST',
						url: 'admin_ubicacion_topografica/buscador_ubicacion_topografica.php',
						data: {
							'search_nivel_padre' : nivel_padre,
							'desde_formulario' : '1'
						},			
						success: function(resp){
							if(resp!=""){
								$('#sugerencia_nivel_padre').html(resp);
							}
						}
					})		
				}else{
					$('#sugerencia_nivel_padre').html('');
				}				 
			},1000);
	    };
	});

	$('#mod_nivel_padre').on("input",function(e){ // Accion que se activa cuando se digita #mod_nivel_padre
		loading('sugerencia_nivel_mod_padre')
		$(".errores").slideUp("slow");

		var mod_nivel_padre 		= $(this).val().toUpperCase();
		var antiguo_mod_padre 		= $("#antiguo_mod_padre").val();
		var antiguo_nombre_nivel 	= $("#antiguo_nombre_nivel").val();
				
		clearTimeout(timerid);
		timerid = setTimeout(function(){
			if(mod_nivel_padre==""){
				$('#sugerencia_nivel_mod_padre').html('');
			}else{
				if(antiguo_mod_padre!=mod_nivel_padre) {

					validar_input('mod_nivel_padre');

					if(mod_nivel_padre.length>2 && mod_nivel_padre.length<51){
						$.ajax({
							type: 'POST',
							url: 'admin_ubicacion_topografica/buscador_ubicacion_topografica.php',
							data: {
								'search_mod_nivel_padre' 	: mod_nivel_padre,
								'antiguo_nombre_nivel' 		: antiguo_nombre_nivel,
								'search_antiguo_mod_padre' 	: antiguo_mod_padre,
								'desde_formulario' 			: '1'
							},			
							success: function(resp){
								if(resp!=""){
									$('#sugerencia_nivel_mod_padre').html(resp);
								}
							}
						})
					}else{
						loading('sugerencia_nivel_mod_padre');
					}
				}else{
					$('#sugerencia_nivel_mod_padre').html('');
				}	
			}
		},1000);		
	});

	$('#bEnviar_nivel').click(function submit_agregar_nivel(){
		validar_input('nombre_nivel');
		validar_input('nivel_padre');

		if($('.imagen_logo').is(":visible")){
			// sweetAlert({
			Swal.fire({	
				position 			: 'top-end',
				showConfirmButton 	: false,
				timer 				: 1000,	
			    title 				:'La consulta se está ejecutando.',
			    text 				: 'Un momento por favor.',
			    type 				:'warning'
			});
		}else{			
			if($(".errores").is(":visible")){
				return false;
			}else{	// Realizar la creación del Nivel
				loading("boton_enviar_nivel");

				var tipo_formulario1 	= $("#tipo_formulario").val();
				var nombre_nivel1 		= $("#nombre_nivel").val();
				var nivel_padre1 		= $("#nivel_padre").val();
				
				$.ajax({
					type: 'POST',
					url: 'admin_ubicacion_topografica/query_topografica.php',
					data: {
						'tipo_formulario' 	: tipo_formulario1,
						'nombre_nivel' 		: nombre_nivel1,
						'nivel_padre' 		: nivel_padre1
					},			
					success: function(resp){
						if(resp!=""){
							$('#sugerencia_nivel_padre').html(resp);
						}
					}
				})	
			}
													
		}
	});

})
/******************************************************************************************/
/* Fin script para buscador input formularios ubicacion_topografica ***********************/
/******************************************************************************************/
/******************************************************************************************/
/* Agregar Nivel **************************************************************************/
/******************************************************************************************/
function abrir_ventana_crear_nivel(){
	var crear=$("#search_nivel").val();

	$("#ventana").slideDown("slow");
	$("#nombre_nivel").val(crear);
	$("#nombre_nivel").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}

/*Función para cargar valor nivel padre Formulario Agregar Nuevo Nivel */
function cargar_nivel_padre(nombre_nivel_padre){
	$('#nivel_padre').val(nombre_nivel_padre);
	$('.art_exp').slideUp("slow");
	$('#error_nombre_nivel_padre').slideUp("slow");
	$('#error_nombre_nivel_padre2').slideUp("slow");

	validar_input('nombre_nivel');
	validar_input('nivel_padre');
}
/*Fin función para cargar valor nivel padre Formulario Agregar Nuevo Nivel*/
/******************************************************************************************/
/* Fin Agregar Nivel **********************************************************************/
/******************************************************************************************/
/******************************************************************************************/
/* Modificar Nivel ************************************************************************/
/******************************************************************************************/
function abrirVentanaModificarNivel(){
	$("#ventana2").slideDown("slow");
	$("#mod_nombre_nivel").focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}
/*Función para cargar datos al formulario de modificación de nivel*/
function cargar_modifica_nivel(nombre_nivel,nivel_padre,activa,id_ubicacion){
	// console.log(nombre_nivel+" , "+nivel_padre+" , "+activa+" , "+id_ubicacion);
	$('#antiguo_mod_padre').val(nivel_padre);
	$('#mod_nombre_nivel').val(nombre_nivel);
	$('#antiguo_nombre_nivel').val(nombre_nivel);	
	$('#mod_nivel_padre').val(nivel_padre);
	$('#mod_activa').val(activa);
	$('#id_ubicacion').val(id_ubicacion)

	$('.art_nombre_nivel').slideUp("slow");
	
	// cerrar_ventanas_modal();
	abrirVentanaModificarNivel();
	$("#mod_nivel_ya_existe").slideUp("slow");

	$("#boton_modificar_nivel").html("<center><input type='button' value='Modificar Nivel' id='enviar_mod_nivel' class='botones' onclick='submit_modificar_nivel()'><center>")
}	
/* Fin funcion para cargar datos al formulario de modificacion de nivel */
/*Función para cargar valor nivel padre Formulario Modificar Nivel */
function cargar_nivel_mod_padre(mod_nivel_padre){
	$('#mod_nivel_padre').val(mod_nivel_padre);
	$('.art_nivel_padre').slideUp("slow");
	$('.errores').slideUp("slow");
}
/*Fin función para cargar valor nivel padre Formulario Modificar Nivel*/
/* Error al dar click sobre enlace de nivel que ya existe - Formulario Modificar Nivel se invoca en buscador_ubicacion_topografica.php */
function error_modificar_nivel(){
	$("#mod_nivel_ya_existe").slideDown("slow");
}
/* Fin error al dar click sobre enlace de nivel que ya existe - Formulario Modificar Nivel*/
function error_modificar_nivel_padre3(){
	$("#error_nombre_mod_nivel_padre3").slideDown("slow");
}
/* Validación que los campos de Formulario Modificar Nivel (Submit) */				
function submit_modificar_nivel(){
	validar_input('mod_nombre_nivel')

	if($('.imagen_logo').is(":visible")){
		// sweetAlert({
		Swal.fire({	
			position 			: 'top-end',
			showConfirmButton 	: false,
			timer 				: 1000,	
		    title 				:'La consulta se está ejecutando.',
		    text 				: 'Un momento por favor.',
		    type 				:'warning'
		});
	}else{
		var submit_modificar_nivel = validar_modificar_nivel();
		if(submit_modificar_nivel==false){
			return false;
		}else{	// Realizar la modificacion del nivel
			loading('boton_modificar_nivel');

			var tipo_formulario1 		= $("#tipo_formulario_mod").val();
			var id_ubicacion1 			= $("#id_ubicacion").val();
			var mod_nombre_nivel1 		= $("#mod_nombre_nivel").val();
			var mod_nivel_padre1 		= $("#mod_nivel_padre").val();
			var mod_activa1 			= $("#mod_activa").val();
			var antiguo_nombre_nivel1 	= $("#antiguo_nombre_nivel").val();
			
			$.ajax({
				type: 'POST',
				url: 'admin_ubicacion_topografica/query_topografica.php',
				data: {
					'tipo_formulario' 		: tipo_formulario1,
					'id_ubicacion' 			: id_ubicacion1,
					'mod_nombre_nivel' 		: mod_nombre_nivel1,
					'mod_nivel_padre' 		: mod_nivel_padre1,
					'mod_activa' 			: mod_activa1,
					'antiguo_nombre_nivel' 	: antiguo_nombre_nivel1
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencia_nivel_padre').html(resp);
					}
				}
			})	
		}										
	}										
};
/* Fin de validación que los campos de Formulario Modificar Nivel (Submit) */
/* Funcion para validar mod_nombre_nivel */
function validar_mod_nombre_nivel(){
	var mod_nombre_nivel =$('#mod_nombre_nivel').val(); // Este valor ya fue cargado en la funcion cargar_modifica_nivel

	if(mod_nombre_nivel==""){
		validar_input('nombre_nivel');
		return false;
	}else{
		if(mod_nombre_nivel.length<3){
			validar_input('nombre_nivel');
			return false;
		}else{
			if(mod_nombre_nivel.length>50){
				validar_input('nombre_nivel');
				return false;
			}else{
				if($(".art_nombre_nivel").is(":visible")){
					validar_input('nombre_nivel');
					$("#mod_nivel_ya_existe").slideDown("slow");
					return false;
				}else{
					$("#mod_nivel_ya_existe").slideUp("slow");
					return true;				
				}
			}
		}
	}	
}
/* Fin funcion para validar mod_nombre_nivel */
/* Funcion para validar mod_nivel_padre */
function validar_mod_nombre_nivel_padre(){
	var mod_nombre_nivel 		= $('#mod_nombre_nivel').val(); 
	var mod_nombre_nivel_padre  = $('#mod_nivel_padre').val();

	if(mod_nombre_nivel_padre.length<3 && mod_nombre_nivel_padre.length>0){
		$("#mod_nivel_padre_min").slideDown("slow");
		$("#mod_nivel_padre_max").slideUp("slow");
		$("#error_nombre_mod_nivel_padre2").slideUp("slow");
		$("#error_nombre_mod_nivel_padre3").slideUp("slow");
		$('#mod_nivel_padre').focus();
		return false;
	}else{
		if(mod_nombre_nivel_padre.length>50){
			$("#mod_nivel_padre_min").slideUp("slow");
			$("#mod_nivel_padre_max").slideDown("slow");
			$("#error_nombre_mod_nivel_padre2").slideUp("slow");
			$("#error_nombre_mod_nivel_padre3").slideUp("slow");
			$('#mod_nivel_padre').focus();
			return false;
		}else{
			if($('#error_nombre_mod_nivel_padre').is(':visible')){
				$('#mod_nivel_padre').focus();
				return false;
			}else{
				if(mod_nombre_nivel_padre==mod_nombre_nivel){
					$("#mod_nivel_padre_min").slideUp("slow");
					$("#mod_nivel_padre_max").slideUp("slow");
					$("#error_nombre_mod_nivel_padre2").slideUp("slow");
					$("#error_nombre_mod_nivel_padre3").slideDown("slow");
					$('#mod_nivel_padre').focus();
					return false;
				}else{
					if($(".art_nivel_padre").is(":visible")){
						$("#mod_nivel_padre_min").slideUp("slow");
						$("#mod_nivel_padre_max").slideUp("slow");
						$("#error_nombre_mod_nivel_padre2").slideDown("slow");
						$("#error_nombre_mod_nivel_padre3").slideUp("slow");
						$('#mod_nivel_padre').focus();
						return false;
					}else{
						if($(".errores").is(':visible')){
							$('#mod_nivel_padre').focus();
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
/* Fin funcion para validar mod_nivel_padre */
function validar_modificar_nivel(){
	var validar_mod_nombre_niv = validar_mod_nombre_nivel();
	console.log(validar_mod_nombre_niv);
	if(validar_mod_nombre_niv==false){
		$("#mod_nombre_nivel").focus()
		return false;
	}else{
		var validar_mod_nombre_niv_padre= validar_mod_nombre_nivel_padre();
		console.log(validar_mod_nombre_niv_padre);
		if(validar_mod_nombre_niv_padre==false){
			$("#mod_nivel_padre").focus();
			return false;
		}else{
			return true;
		}
	}
}	

/******************************************************************************************/
/* Fin Modificar Nivel ********************************************************************/
/******************************************************************************************/

function acordeon_nivel(nombre_nivel_actual){
	if($("."+nombre_nivel_actual).is(":visible")){
		$("."+nombre_nivel_actual).hide();
	}else{
		$("."+nombre_nivel_actual).show()
	}	
}

function mostrar_vista_compacta(){
	if($("#chart_div").is(":visible")){
		$("#chart_div").hide();
		$("#vista_comprimida_organigrama").show();
		$("#boton_vista_compacta").val("Vista como flujo")
		$("#contenido li").hide();
		$(".nivel1").show();
	}else{
		$("#boton_vista_compacta").val("Vista Compacta")
		$("#chart_div").show();
		$("#vista_comprimida_organigrama").hide();
	}		
}
</script>
<!-- Fin js ubicacion_topografica -->
<!-- ************************************************************************************* -->
<!-- Inicio Estilos ubicacion_topografica -->
<style type="text/css">
	#listado_expedientes_pendientes{
		background-color : #FFFFFF;
		height 			 : 200px;
		margin 		 	 : 25px;
		overflow-y 		 : scroll;	
		padding 		 : 15px;
		width 			 : 95%;
	}
	#vista_comprimida_organigrama li{
		background-color 	: black;
		border-top 			: solid #2d9dc6 1px;
		box-shadow 			: 0px 0 #2D9DC6 inset;
		color 				: #FFFFFF;
		cursor 	 			: pointer;
		display 			: block;
		padding 			: 5px 2px;
		position 			: relative;
		transition 			: all 0.5s;
		width 				: 500px;
	}
	#vista_comprimida_organigrama li a{
		color 				: #FFFFFF;
		text-decoration: none;
	}
	#vista_comprimida_organigrama li:hover {
	    /*Animacion de llena caja otro color al hover*/
	    border-radius   : 10px;
	    box-shadow      : 700px 0 #2D9DC6 inset;
	}
</style>
<!-- Fin Estilos ubicacion_topografica -->
<!-- ************************************************************************************* -->
</head>
<body>
<!--Desde aqui el div que contiene el formulario para agregar nivel-->
	<div>
		<div id="ventana"  class="ventana_modal">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nuevo Nivel</h1>
				<hr>
				<form id ="formulario_agregar_nivel" name ="formulario_agregar_nivel" autocomplete="off">
					<table border ="0">
						<tr>
							<td class="descripcion">Nombre del Nivel :</td>
							<td class="detalle">
								<input type="hidden" name ="tipo_formulario" id="tipo_formulario" value="crear_nivel">
								
								<input type="search" placeholder="Digite Nombre del Nivel" title="Nombre del Nivel" name="nombre_nivel" id="nombre_nivel" onblur="validar_input('nombre_nivel')">
								
								<div id="sugerencia_nombre_nivel" class="sugerencia"></div>

								<div id="nombre_nivel_max" class="errores">El nombre del nivel no puede ser mayor a 50 caracteres (numeros o letras)</div>		
								<div id="nombre_nivel_min" class="errores">El nombre del nivel no puede ser menor a 3 caracteres (numeros o letras)</div>
								<div id="nombre_nivel_null" class="errores">El nombre del nivel es obligatorio</div>
								<div id="nombre_nivel_ya_existe" class="errores">El nombre del nivel ya existe, no es posible crear un nuevo nivel con éste nombre</div>		
							</td>
						</tr>
						<tr>
							<td class="descripcion">Nivel Padre :</td>
							<td class="detalle">
								<input type="search" placeholder="Digite Nivel Padre (Si tiene)" title="Nombre del nivel padre" name="nivel_padre" id="nivel_padre" onblur="validar_input('nivel_padre')" >

								<div id="sugerencia_nivel_padre" class="sugerencia"></div>

								<div id="nivel_padre_max" class="errores">El nombre del nivel no puede ser mayor a 50 caracteres (numeros o letras)</div>		
								<div id="nivel_padre_min" class="errores">El nombre del nivel no puede ser menor a 3 caracteres (numeros o letras)</div>


								<div id="error_nombre_nivel_padre" class="errores">El nombre del nivel padre no existe en la base de datos. Intente otro nombre</div>
								<div id="error_nombre_nivel_padre2" class="errores">Por favor seleccione un nivel padre válido</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" id="boton_enviar_nivel"><center><input type="button" value="Grabar Nivel" id="bEnviar_nivel" class="botones"><center></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para agregar nivel-->
<!--Desde aqui el div que contiene el formulario para Modificar nivel-->	
		<div id="ventana2" class="ventana_modal">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
				<h1>Formulario Modificar Nivel</h1>
				<hr>
				<form id ="formulario_modificar_nivel" name ="formulario_modificar_nivel" autocomplete="off">
					<table>
						<tr>
							<td class="descripcion">Nombre del Nivel :</td>
							<td class="detalle" style="max-width: 180px;">
								<input type="hidden" name ="tipo_formulario_mod" id="tipo_formulario_mod" value="modificar_nivel"><!--Tipo de formulario para query_topografica.php-->
								<input type="hidden" name ="id_ubicacion" id="id_ubicacion">
								<input type="hidden" name ="antiguo_mod_padre" id="antiguo_mod_padre">
								<input type="hidden" name="antiguo_nombre_nivel" id="antiguo_nombre_nivel">
								
								<input type="search" placeholder="Digite Nombre del Nivel" name="mod_nombre_nivel" id="mod_nombre_nivel">
								<div id="sugerencia_mod_nombre_nivel" class="sugerencia"></div>

								<div id="mod_nivel_ya_existe" class="errores">El nombre del nivel ya existe, no es posible modificar un nuevo nivel asignando éste nombre</div>								
								<div id="mod_nombre_nivel_null" class="errores">El nombre del nivel es obligatorio</div>
								<div id="mod_nombre_nivel_max" class="errores">El nombre del nivel no puede ser mayor a 50 caracteres (numeros o letras)</div>
								<div id="mod_nombre_nivel_min" class="errores">El nombre del nivel no puede ser menor a 3 caracteres (numeros o letras)</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Nivel Padre :</td>
							<td class="detalle" style="max-width: 180px;">
								<input type="search" placeholder="Digite Nivel Padre (Si tiene)" name="mod_nivel_padre" id="mod_nivel_padre">							
								<div id="sugerencia_nivel_mod_padre" class="sugerencia"></div>

								<div id="error_nombre_mod_nivel_padre" class="errores">El nombre del nivel no existe en la base de datos. Intente otro nombre</div>
								<div id="error_nombre_mod_nivel_padre2" class="errores">Por favor seleccione un nivel padre válido</div>
								<div id="error_nombre_mod_nivel_padre3" class="errores">El nivel padre no puede ser el mismo nombre del nivel que se está creando.</div>
								<div id="mod_nivel_padre_min" class="errores">El nombre del nivel padre no puede ser menor a 3 caracteres (numeros o letras)</div>
								<div id="mod_nivel_padre_max" class="errores">El nombre del nivel padre no puede ser mayor a 50 caracteres (numeros o letras)</div>
							</td>
						</tr>
						
						<tr>
							<td class="descripcion">Activa :</td>
							<td class="detalle">
								<select name="mod_activa" id="mod_activa" class='select_opciones'>
<!--									<option value="SI" selected="selected">SI</option>-->
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" id="boton_modificar_nivel">
								<center>
									<input type="button" value="Modificar Nivel" id="enviar_mod_nivel" class="botones" onclick="submit_modificar_nivel()">
								<center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para modificar nivel -->
<!--Desde aqui el div que contiene el formulario para administrador de cajas-->	
<!--		 <div id="ventana3">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrarVentanaAdminCajas();'>Cerrar X</a></div>
				<h1 id="titulo_caja">a</h1>
				<hr>
				<form id ="formulario_admin_cajas" name ="formulario_admin_cajas" autocomplete="off">
					<table>
						<tr>
							<td class="descripcion">Nombre de la caja :</td>
							<td class="detalle">
								<input type="hidden" name ="tipo_formulario_mod" id="tipo_formulario_mod" value="modificar_nivel">Tipo de formulario para query_topografica.php
								<input type="hidden" name ="id_ubicacion" id="id_ubicacion">
								<input type="hidden" name ="antiguo_mod_padre" id="antiguo_mod_padre">
								<input type="hidden" name="antiguo_nombre_nivel" id="antiguo_nombre_nivel">
								
								<input type="search" placeholder="Digite Nombre del Nivel" name="mod_nombre_nivel" id="mod_nombre_nivel" onkeyup="espacios_formulario_nivel('mod_nombre_nivel')" onblur="validar_modificar_nivel()">
								<div id="sugerencia_mod_nombre_nivel" class="sugerencia"></div>
								<div id="mod_nivel_ya_existe" class="errores">El nombre del nivel ya existe, no es posible modificar un nuevo nivel asignando éste nombre</div>								
								<div id="error_mod_nombre_nivel" class="errores">El nombre del nivel es obligatorio</div>
								<div id="valida_minimo_mod_nombre" class="errores">El nombre del nivel no puede ser menor a 4 caracteres (numeros o letras)</div>
								<div id="valida_maximo_mod_nombre" class="errores">El nombre del nivel no puede ser mayor a 40 caracteres (numeros o letras)</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Nivel Padre :</td>
							<td class="detalle">
								<input type="search" placeholder="Digite Nivel Padre (Si tiene)" name="mod_nivel_padre" id="mod_nivel_padre" onkeyup="espacios_formulario_nivel('mod_nivel_padre')" onblur="validar_modificar_nivel()">							
								<div id="sugerencia_nivel_mod_padre" class="sugerencia"></div>
								<div id="error_nombre_mod_nivel_padre" class="errores">El nombre del nivel no existe en la base de datos. Intente otro nombre</div>
								<div id="error_nombre_mod_nivel_padre2" class="errores">Por favor seleccione un nivel padre válido</div>
								<div id="error_nombre_mod_nivel_padre3" class="errores">El nivel padre no puede ser el mismo nombre del nivel que se está creando.</div>
								<div id="error_minimo_mod_nombre_padre" class="errores">El nombre del nivel padre no puede ser menor a 4 caracteres (numeros o letras)</div>
								<div id="error_maximo_mod_nombre_padre" class="errores">El nombre del nivel padre no puede ser mayor a 40 caracteres (numeros o letras)</div>
							</td>
						</tr>
						
						<tr>
							<td class="descripcion">Activa :</td>
							<td class="detalle">
								<select name="mod_activa" id="mod_activa" class='select_opciones'>
									<option value="SI" selected="selected">SI</option>
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" id="boton_modificar_nivel"><center><input type="button" value="Modificar Nivel" id="enviar_mod_nivel" class="botones"><center></td>
						</tr>
					</table>
				</form>
			</div>
		</div> 
		-->
<!--Hasta aqui el div que contiene el formulario para administrador de cajas -->
		<div class="center">
			<br>
			<h1 style="margin-top:-10px;">Configuración Nivel</h1>
		</div>
		<div class="form center">
			<input type="search" id="search_nivel" class='input_search' placeholder="Ingrese nombre del nivel" title="Ingrese nombre del nivel" onblur="validar_input('search_nivel')">
		</div>
		<div id="search_nivel_max" class="errores">La busqueda debe tener 50 caracteres maximo. Revise por favor</div>
		<div id="search_nivel_min" class="errores">Para iniciar la búsqueda debe ingresar por lo menos 3 caracteres.</div>

		<div id="desplegable_resultados"></div>

<!-- Desde aqui el listado de expedientes pendientes por ubicación topografica -->
		<div id="listado_expedientes_pendientes">
		<?php  
			$dependencia 		= $_SESSION['dependencia'];
			$login 				= $_SESSION['login'];
			$permiso_inventario = $_SESSION['inventario'];
			
			if($permiso_inventario=="SI"){
				$query_expedientes_pendientes = "select * from inventario i join expedientes e on i.expediente_jonas=e.id_expediente where upper(i.cargado_por)=trim(upper('$login')) and 
				e.codigo_ubicacion_topografica is null"; 

				$fila_expedientes_pendientes 		= pg_query($conectado,$query_expedientes_pendientes);
				$registros_expedientes_pendientes 	= pg_num_rows($fila_expedientes_pendientes);

				echo "<h3 class='center'>Listado de Expedientes del FUID pendientes por archivar físicamente <font color='red'>($registros_expedientes_pendientes)</font></h3><hr>";

				for ($i=0;$i<$registros_expedientes_pendientes;$i++){
	                $linea_expedientes_pendientes = pg_fetch_array($fila_expedientes_pendientes);

	                $id_expediente 		= $linea_expedientes_pendientes['id_expediente'];
	                $nombre_documento 	= $linea_expedientes_pendientes['nombre_documento'];
	                $descriptor 		= $linea_expedientes_pendientes['descriptor'];

	                echo "<hr><b>$id_expediente</b> - $nombre_documento ($descriptor)";
	            }
			}else{
				$query_expedientes_pendientes = "select * from expedientes where dependencia_expediente='$dependencia' and codigo_ubicacion_topografica is null";

				$fila_expedientes_pendientes 		= pg_query($conectado,$query_expedientes_pendientes);
				$registros_expedientes_pendientes 	= pg_num_rows($fila_expedientes_pendientes);

				echo "<h3 class='center'>Listado de Expedientes del FUID pendientes por archivar físicamente en su dependencia <font color='red'>($registros_expedientes_pendientes)</font></h3><hr>";

				for ($i=0;$i<$registros_expedientes_pendientes;$i++){
	                $linea_expedientes_pendientes = pg_fetch_array($fila_expedientes_pendientes);

	                $id_expediente 		= $linea_expedientes_pendientes['id_expediente'];
	                $nombre_expediente 	= $linea_expedientes_pendientes['nombre_expediente'];

	                echo "<hr><b>$id_expediente</b> - $nombre_expediente";
	            }
			}
		?>
			
		</div>	
<!-- Desde aqui el listado de expedientes pendientes por ubicación topografica -->
<!-- Desde aqui es el despliegue del gráfico del organigrama de nivel  -->	
		<?php 
		// $query_ubicacion= "select * from ubicacion_topografica where activa ='SI' order by nombre_nivel";
		$query_ubicacion= "select * from ubicacion_topografica where activa ='SI'";
		$fila_ubicacion = pg_query($conectado,$query_ubicacion);
	/* Calcula el numero de registros que genera la consulta anterior. */
		$registros_ubicacion= pg_num_rows($fila_ubicacion);
		$organigrama="";
		for ($i=0;$i<$registros_ubicacion;$i++){
			$linea_ubicacion = pg_fetch_array($fila_ubicacion);

			$query_nivel_padre="select * from ubicacion_topografica where activa ='SI' and nivel_padre='".$linea_ubicacion['nombre_nivel']."'";
			// echo "$query_nivel_padre<br>";
			$fila_nivel_padre  	= pg_query($conectado,$query_nivel_padre);
			$linea_nivel_padre  = pg_fetch_array($fila_nivel_padre);

			if(isset($linea_nivel_padre['nombre_nivel'])){
				$boton_mostrar_caja="";		
			}else{
				$boton_mostrar_caja="<br><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_ubicacion['id_ubicacion'].")' style='padding: 5px;'>";
			}
			$organigrama = $organigrama."[\"".$linea_ubicacion['nombre_nivel']."$boton_mostrar_caja\",\"".$linea_ubicacion['nivel_padre']."\",\"\"],";
		}
		$organigrama1=substr($organigrama, 0,-1);


		/* Se inicia para visualizar vista compacta */
		$query_nivel1 	= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='' order by nombre_nivel";
		$fila_ubicacion1 		= pg_query($conectado,$query_nivel1);
		$registros_ubicacion1 	= pg_num_rows($fila_ubicacion1);

		$lista_registros_ubicacion = "";

		echo "<input type='button' id='boton_vista_compacta' class='botones' onclick='mostrar_vista_compacta()' value='Vista Compacta'><br>";
		// echo "<div id='boton_vista_compacta' class='botones' onclick='mostrar_vista_compacta()'>Vista Compacta</div><br>";
		echo "<div id='vista_comprimida_organigrama' style='display:none;'><hr><ul>";

		/* Si no hay registros en el primer nivel */
		if($registros_ubicacion1==0){
			echo "<center><h1 style='color:red;'>No hay registros en la base de datos. <br>Ingrese el primer nivel en el campo 'Ingrese nombre del nivel'.</h1></center>";
		}else{
			/* Recorre los resultados de $query_nivel1 */
			for ($j=0; $j < $registros_ubicacion1 ; $j++) { 
				$linea_nivel1 	= pg_fetch_array($fila_ubicacion1);
				$nombre_nivel1 	= $linea_nivel1['nombre_nivel'];

				/* Si hay registros en el primer nivel pero se consulta si el primer nivel tiene hijos para mostrar boton o siguiente nivel*/
				$query_nivel2 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel1' order by nombre_nivel";
				$fila_nivel2  		= pg_query($conectado,$query_nivel2);
				$registros_nivel2 	= pg_num_rows($fila_nivel2);

				if($registros_nivel2==0){
					/* Si el primer nivel no tiene hijos*/
					// echo "->Nivel1 es $nombre_nivel1 <--------- Mostrar Caja<br> ";  // Imprime Nivel1
					echo "<li title='Menu $nombre_nivel1'><span style='margin-left:20px;'>$nombre_nivel1</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel1['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>"; 
				}else{
					/* Si el primer nivel tiene hijos*/
					// echo "->Nivel1 es $nombre_nivel1 < Siguiente<br> ";  // Imprime Nivel1
					$nombre_nivel1 = str_replace(" ", "_", $nombre_nivel1);
					echo "<li id='MENU_$nombre_nivel1' class='nivel1' title='Menu $nombre_nivel1'  onclick='acordeon_nivel(\"MENU_$nombre_nivel1\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel1</span></li>";

					/* Recorre los resultados de $query_nivel2 */
					for ($k=0; $k < $registros_nivel2; $k++) { 
						$linea_nivel2 	= pg_fetch_array($fila_nivel2);
						$nombre_nivel2 	= $linea_nivel2['nombre_nivel'];

						/* Se consulta si el segundo nivel tiene hijos para mostrar boton o siguiente nivel*/
						$query_nivel3 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel2' order by nombre_nivel";
						$fila_nivel3  		= pg_query($conectado,$query_nivel3);
						$registros_nivel3 	= pg_num_rows($fila_nivel3);

						if($registros_nivel3==0){
							/* Si el segundo nivel no tiene hijos*/
							// echo "-->Nivel2 es $nombre_nivel2 <--------- Mostrar Caja<br> ";  // Imprime Nivel2
							echo "<li class='MENU_$nombre_nivel1' title='Menu $nombre_nivel2' style='margin-left:20px;'><span style='margin-left:20px;'>$nombre_nivel2</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel2['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>"; 
						}else{
							/* Si el segundo nivel tiene hijos*/
							// echo "-->Nivel2 es $nombre_nivel2 < Siguiente<br> ";  // Imprime Nivel2
							$nombre_nivel2 = str_replace(" ", "_", $nombre_nivel2);
							echo "<li class='MENU_$nombre_nivel1' title='Menu $nombre_nivel2' style='margin-left:20px;' onclick='acordeon_nivel(\"MENU_$nombre_nivel2\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel2</span></li>";
					
							/* Recorre los resultados de $query_nivel3 */
							for ($l=0; $l < $registros_nivel3; $l++) { 
								$linea_nivel3 	= pg_fetch_array($fila_nivel3);
								$nombre_nivel3 	= $linea_nivel3['nombre_nivel'];
								/* Se consulta si el tercer nivel tiene hijos para mostrar boton o siguiente nivel*/
								$query_nivel4 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel3' order by nombre_nivel";
								$fila_nivel4  		= pg_query($conectado,$query_nivel4);
								$registros_nivel4 	= pg_num_rows($fila_nivel4);

								if($registros_nivel4==0){
								/* Si el tercer nivel no tiene hijos*/
								// echo "---->Nivel3 es $nombre_nivel3 <--------- Mostrar Caja<br> ";  // Imprime Nivel3									
									echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2' title='Menu $nombre_nivel3' style='margin-left:40px;'><span style='margin-left:20px;'>$nombre_nivel3</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel3['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>"; 
								}else{
									/* Si el tercer nivel tiene hijos*/
									// echo "---->Nivel3 es $nombre_nivel3 < Siguiente<br> ";  // Imprime Nivel3
									$nombre_nivel3 = str_replace(" ", "_", $nombre_nivel3);
									echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 ' title='Menu $nombre_nivel3' style='margin-left:40px;' onclick='acordeon_nivel(\"MENU_$nombre_nivel3\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel3</span></li>";
									/* Recorre los resultados de $query_nivel4 */
									for ($m=0; $m < $registros_nivel4; $m++) { 
										$linea_nivel4 	= pg_fetch_array($fila_nivel4);
										$nombre_nivel4 	= $linea_nivel4['nombre_nivel'];
										/* Se consulta si el cuarto nivel tiene hijos para mostrar boton o siguiente nivel*/
										$query_nivel5 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel4' order by nombre_nivel";
										$fila_nivel5  		= pg_query($conectado,$query_nivel5);
										$registros_nivel5 	= pg_num_rows($fila_nivel5);

										if($registros_nivel5==0){
										/* Si el cuarto nivel no tiene hijos*/
											// echo "------>Nivel4 es $nombre_nivel4 <--------- Mostrar Caja<br> ";  // Imprime Nivel4
											echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3' title='Menu $nombre_nivel4' style='margin-left:60px;' ><span style='margin-left:20px;'>$nombre_nivel4</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel4['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>"; 
										}else{
											/* Si el cuarto nivel tiene hijos*/
											// echo "------>Nivel4 es $nombre_nivel4 < Siguiente<br> ";  // Imprime Nivel4
											$nombre_nivel4 = str_replace(" ", "_", $nombre_nivel4);
											echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3' title='Menu $nombre_nivel4' style='margin-left:60px;' onclick='acordeon_nivel(\"MENU_$nombre_nivel4\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel4</span></li>";										/* Recorre los resultados de $query_nivel5 */
											for ($n=0; $n < $registros_nivel5; $n++) { 
												$linea_nivel5 	= pg_fetch_array($fila_nivel5);
												$nombre_nivel5 	= $linea_nivel5['nombre_nivel'];
												/* Se consulta si el quinto nivel tiene hijos para mostrar boton o siguiente nivel*/
												$query_nivel6 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel5' order by nombre_nivel";
												$fila_nivel6  		= pg_query($conectado,$query_nivel6);
												$registros_nivel6 	= pg_num_rows($fila_nivel6);

												if($registros_nivel6==0){
												/* Si el quinto nivel no tiene hijos*/
													// echo "--------->Nivel5 es $nombre_nivel5 <--------- Mostrar Caja<br> ";  // Imprime Nivel5
													echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4' title='Menu $nombre_nivel5' style='margin-left:80px;' ><span style='margin-left:20px;'>$nombre_nivel5</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel5['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>";
												}else{
													// echo "--------->Nivel5 es $nombre_nivel5 < Siguiente<br> ";  // Imprime Nivel5
													$nombre_nivel5 = str_replace(" ", "_", $nombre_nivel5);
													echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4' title='Menu $nombre_nivel5' style='margin-left:80px;' onclick='acordeon_nivel(\"MENU_$nombre_nivel5\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel5</span></li>";	
													for ($o=0; $o < $registros_nivel6; $o++) { 
														$linea_nivel6 	= pg_fetch_array($fila_nivel6);
														$nombre_nivel6 	= $linea_nivel6['nombre_nivel'];
														/* Se consulta si el sexto nivel tiene hijos para mostrar boton o siguiente nivel*/
														$query_nivel7 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel6' order by nombre_nivel";
														$fila_nivel7  		= pg_query($conectado,$query_nivel7);
														$registros_nivel7 	= pg_num_rows($fila_nivel7);

														if($registros_nivel7==0){										
														/* Si el sexto nivel no tiene hijos*/
															// echo "------------>Nivel6 es $nombre_nivel6 <--------- Mostrar Caja<br> ";  // Imprime Nivel6
															echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5' title='Menu $nombre_nivel6' style='margin-left:100px;' ><span style='margin-left:20px;'>$nombre_nivel6</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel6['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>";
														}else{
															// echo "------------>Nivel6 es $nombre_nivel6 < Siguiente<br> ";  // Imprime Nivel6
															$nombre_nivel6 = str_replace(" ", "_", $nombre_nivel6);
															echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5' title='Menu $nombre_nivel6' style='margin-left:100px;' onclick='acordeon_nivel(\"MENU_$nombre_nivel6\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel6</span></li>";	
															for ($p=0; $p < $registros_nivel7; $p++) { 
																$linea_nivel7 	= pg_fetch_array($fila_nivel7);
																$nombre_nivel7 	= $linea_nivel7['nombre_nivel'];
																/* Se consulta si el septimo nivel tiene hijos para mostrar boton o siguiente nivel*/
																$query_nivel8 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel7' order by nombre_nivel";
																$fila_nivel8  		= pg_query($conectado,$query_nivel8);
																$registros_nivel8 	= pg_num_rows($fila_nivel8);

																/* Poner el boton de "Mostrar Caja" */
																if($registros_nivel8==0){
																	// echo "--------------->Nivel7 es $nombre_nivel7 <--------- Mostrar Caja<br> ";  // Imprime Nivel7
																	echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5 MENU_$nombre_nivel6' title='Menu $nombre_nivel7' style='margin-left:120px;' ><span style='margin-left:20px;'>$nombre_nivel7</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel7['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>";
																}else{
																	// echo "--------------->Nivel7 es $nombre_nivel7 < Siguiente<br> ";  // Imprime Nivel7
																	$nombre_nivel7 = str_replace(" ", "_", $nombre_nivel7);
																	echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5 MENU_$nombre_nivel6' title='Menu $nombre_nivel7' style='margin-left:120px;' onclick='acordeon_nivel(\"MENU_$nombre_nivel7\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel7</span></li>";	
																	for ($q=0; $q < $registros_nivel8; $q++) { 
																		$linea_nivel8 	= pg_fetch_array($fila_nivel8);
																		$nombre_nivel8 	= $linea_nivel8['nombre_nivel'];
																		/* Se consulta si el octavo nivel tiene hijos para mostrar boton o siguiente nivel*/
																		$query_nivel9 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel8' order by nombre_nivel";
																		$fila_nivel9  		= pg_query($conectado,$query_nivel9);
																		$registros_nivel9 	= pg_num_rows($fila_nivel9);

																		/* Poner el boton de "Mostrar Caja" */
																		if($registros_nivel9==0){
																			// echo "--------------->Nivel8 es $nombre_nivel8 <--------- Mostrar Caja<br> ";  // Imprime Nivel8
																			echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5 MENU_$nombre_nivel6 MENU_$nombre_nivel7' title='Menu $nombre_nivel8' style='margin-left:140px;' ><span style='margin-left:20px;'>$nombre_nivel8</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel8['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>";
																		}else{
																			// echo "--------------->Nivel8 es $nombre_nivel8 < Siguiente<br> ";  // Imprime Nivel8
																			$nombre_nivel8 = str_replace(" ", "_", $nombre_nivel8);

																			echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5 MENU_$nombre_nivel6 MENU_$nombre_nivel7' title='Menu $nombre_nivel8' style='margin-left:140px;' onclick='acordeon_nivel(\"MENU_$nombre_nivel8\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel8</span></li>";
																			for ($r=0; $r < $registros_nivel9; $r++) { 
																				$linea_nivel9 	= pg_fetch_array($fila_nivel9);
																				$nombre_nivel9 	= $linea_nivel9['nombre_nivel'];

																				$query_nivel10 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel9' order by nombre_nivel";
																				$fila_nivel10  		= pg_query($conectado,$query_nivel10);
																				$registros_nivel10 	= pg_num_rows($fila_nivel10);

																				/* Poner el boton de "Mostrar Caja" */
																				if($registros_nivel10==0){
																					// echo "--------------->Nivel9 es $nombre_nivel9 <--------- Mostrar Caja<br> ";  // Imprime Nivel9
																					echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5 MENU_$nombre_nivel6 MENU_$nombre_nivel7 MENU_$nombre_nivel8' title='Menu $nombre_nivel9' style='margin-left:160px;' ><span style='margin-left:20px;'>$nombre_nivel9</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel9['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>";
																				}else{
																					$nombre_nivel9 = str_replace(" ", "_", $nombre_nivel9);
																					// echo "--------------->Nivel9 es $nombre_nivel9 < Siguiente<br> ";  // Imprime Nivel9
																					echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5 MENU_$nombre_nivel6 MENU_$nombre_nivel7 MENU_$nombre_nivel8' title='Menu $nombre_nivel9' style='margin-left:160px;' onclick='acordeon_nivel(\"MENU_$nombre_nivel9\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel9</span></li>";

																					for ($s=0; $s < $registros_nivel10; $s++) { 
																						$linea_nivel10 	= pg_fetch_array($fila_nivel10);
																						$nombre_nivel10 	= $linea_nivel10['nombre_nivel'];

																						$nivel10 = "Nivel10 es $nombre_nivel10";
																						$query_nivel11 		= "select * from ubicacion_topografica where activa ='SI' and nivel_padre='$nombre_nivel10' order by nombre_nivel";
																						$fila_nivel11  		= pg_query($conectado,$query_nivel11);
																						$registros_nivel11 	= pg_num_rows($fila_nivel11);

																						/* Poner el boton de "Mostrar Caja" */
																						if($registros_nivel11==0){
																							// echo "--------------->Nivel10 es $nombre_nivel10 <--------- Mostrar Caja<br> ";  // Imprime Nivel10
																							echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5 MENU_$nombre_nivel6 MENU_$nombre_nivel7 MENU_$nombre_nivel8 MENU_$nombre_nivel9' title='Menu $nombre_nivel10' style='margin-left:180px;' ><span style='margin-left:20px;'>$nombre_nivel10</span><input type='button' class='botones_expediente' value='Mostrar Caja' onclick='cargar_caja(".$linea_nivel10['id_ubicacion'].")' style='margin-left:20px; padding: 5px;'></li>";
																						}else{
																							$nombre_nivel10 = str_replace(" ", "_", $nombre_nivel10);
																							echo "<li class='MENU_$nombre_nivel1 MENU_$nombre_nivel2 MENU_$nombre_nivel3 MENU_$nombre_nivel4 MENU_$nombre_nivel5 MENU_$nombre_nivel6 MENU_$nombre_nivel7 MENU_$nombre_nivel8 MENU_$nombre_nivel9' title='Menu $nombre_nivel10' style='margin-left:180px;' onclick='acordeon_nivel(\"MENU_$nombre_nivel10\")'><span style='padding:10px;'><img src='imagenes/iconos/flecha_abajo.png' style='width:18px;'></span><span>$nombre_nivel10 <font color='red'><---- Aqui termina el nivel 10. Hay que hacer más si se requiere</font> </span></li>";
																							// echo "--------------->Nivel10 es $nombre_nivel10 < Siguiente<br> ";  // Imprime Nivel10
																						}
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}	
			}
		}	
		$lista_registros_ubicacion.= "<hr>";
		echo "</div>";
		echo "$lista_registros_ubicacion";

		?>
		<script type="text/javascript">
		//	  google.charts.load('current', {packages:["orgchart"]});
		      google.charts.setOnLoadCallback(drawChart);

		    function drawChart() {
		        var data = new google.visualization.DataTable();
		        data.addColumn('string', 'Name');
		        data.addColumn('string', 'Manager');
		        data.addColumn('string', 'ToolTip');

		        // Por cada una de las casillas, ingresa el nombre, jefe y tooltip para mostrar
		        data.addRows([
		        	<?php echo $organigrama1; ?>
		        ]);

		        var container = document.getElementById('chart_div');
			    var chart = new google.visualization.OrgChart(container);

			    container.addEventListener('click', function (e) {
			      e.preventDefault();
			      if (e.target.tagName.toUpperCase() === 'A') {
			        console.log(e.target.href);
			        // window.open(e.target.href, '_blank');
			        // or
			        // location.href = e.target.href;
			      } else {
			        var selection = chart.getSelection();
			        if (selection.length > 0) {
			          var row = selection[0].row;
			          var collapse = (chart.getCollapsedNodes().indexOf(row) == -1);
			          chart.collapse(row, collapse);
			        }
			      }
			      chart.setSelection([]);
			      return false;
			    }, false);

			    chart.draw(data, {allowHtml:true, allowCollapse:true});
		        // Se crea el organigrama.
		        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
		        // Dibuje el gráfico, estableciendo la opción allowHtml en true para la información sobre tooltips
		        chart.draw(data, {allowHtml:true});
		    }
		    /* Script para mostrar en vista general al cargar la pagina */
		</script>
		<center>
			<div id="chart_div"></div>
		</center>
<!-- Hasta aqui es el despliegue del gráfico del organigrama de nivel  -->	
	</div>

</body>
</html>