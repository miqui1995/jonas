<?php 
if(!isset($_SESSION)){
	session_start();
}

require_once("../../login/validar_inactividad.php");
?>
<style type="text/css">
	.boton_cerrar_usuario{
		background-color 	: red;
		border-radius 		: 20px;
		bottom 				: 0;
		height 				: 100%;
		left 				: 100%;
		overflow 			: hidden;
		position 			: absolute;
		right 				: 0;
		transition 			: .5s ease;
		width 				: 0;
	}
	.div_agregar_destinatario{
		border 			: 2px solid rgba(255,255,255,.5);
		border-radius 	: 20px;
		cursor 			: pointer;
		float 			: left;
		height 			: 30px;
		position 		: relative;
		width 			: 30px;
	}
	.lista_destinatario{
		border-radius 	: 20px;
		border 			: 2px solid rgba(255,255,255,.5);
		cursor 			: pointer;
		display 		: block;
		float 			: left;
		font-size 		: 15px;
		height 			: 30px;
		left 			: 0%;
		position 		: relative;
		right 			:  100%;
		transition 		: .5s ease;
		width 			: 200px;
		list-style 		: none;
	}
	.nombre_destinatario{
		vertical-align: middle;
		margin-left: 35px;
		margin-top: -22px;
	}
	/* Visualizar submenu */
	.lista_destinatario .li{
		background-color 	: #D2D2D2;
		border-radius 		: 10px;
		height 				: 0;
		list-style 			: none;
		overflow 			: hidden;
		position 			: absolute;	
		transition 			: .5s ease;
		z-index 			: 99;
	}
	.lista_destinatario:hover .li{
		background-color 	: #e0e6e7;
		border 				: 2px solid rgba(255,255,255,.5);
		display 			: block;
		height 				: 70px;
		transition 			: .5s ease;
		width 				: 400px;
	}
	/* Fin visualizar submenu */
	
	/* Script para visualizar "cerrar" al dar hover pestañas */
	.lista_destinatario:hover .boton_cerrar_usuario {
		width 	: 15%;
		left 	: 85%;
	}

	.img_cerrar {
		white-space: nowrap; 
		color: white;
		font-size: 20px;
		position: absolute;
		overflow: hidden;
		top: 15%;
		left: 15%;
	}
/* Fin script para visualizar "cerrar" al dar hover pestañas */
</style>
<script type="text/javascript">
/* Inicio script para buscador input formularios envío de radicado */
var timerid="";
$(function buscar_input_envio_radicado(){
	$('#usuario_actual_nuevo').on("input",function(e){ // Accion que se activa cuando se digita #usuario_actual_nuevo 
		loading("desplegable_resultados");
		$(".errores").slideUp("slow");
		
		var usuario_actual_nuevo 	= $(this).val();
		var usuarios_actuales 		= $("#usuario_actual_derivado").val();
			
		if ($(this).data("lastval")!=usuario_actual_nuevo) {
			$(this).data("lastval",usuario_actual_nuevo);

			clearTimeout(timerid);
			timerid = setTimeout(function(){

				if(usuario_actual_nuevo.length>0){
					espacios_formulario('usuario_actual_nuevo','mayusculas',0);
					$.ajax({
						type: 'POST',
						url: 'include/procesar_ajax.php',
						data: {
							'recibe_ajax' 				: 'buscar_usuario',
							'tipo_formulario' 			: 'derivar_radicado',
							'usuarios_actuales' 		: usuarios_actuales,
				            'valor_buscado' 			: usuario_actual_nuevo
						},			
						success: function(resp){
							$('#desplegable_resultados').html(resp);
						}
					})
				}else{
					$('#desplegable_resultados').html("");
				}				 
			},1000);
		};		
	});

	$('#usuario_actual_nuevo_inf').on("input",function(e){ // Accion que se activa cuando se digita #usuario_actual_nuevo_inf 
		loading("desplegable_resultados");
		$(".errores").slideUp("slow");

		var usuario_actual_nuevo_inf 	= $(this).val();
		var usuarios_actuales			= $("#usuarios_para_agregar_informar").val();
		var usuarios_nuevos_informar 	= $("#usuarios_nuevos_informar").val();
		var usuario_actual_informado 	= $("#usuario_actual_informado").val();
		usuarios_actuales 				+= usuarios_nuevos_informar;
			
		if ($(this).data("lastval")!=usuario_actual_nuevo_inf) {
			$(this).data("lastval",usuario_actual_nuevo_inf);

			clearTimeout(timerid);
			timerid = setTimeout(function(){

				if(usuario_actual_nuevo_inf.length>0){
					espacios_formulario('usuario_actual_nuevo_inf','mayusculas',0);
					$.ajax({
						type: 'POST',
						url: 'include/procesar_ajax.php',
						data: {
							'recibe_ajax' 				: 'buscar_usuario',
							'tipo_formulario' 			: 'informar_radicado',
							'usuarios_actuales' 		: usuarios_actuales,
							'usuario_actual_radicado'	: usuario_actual_informado,
				            'valor_buscado' 			: usuario_actual_nuevo_inf
						},			
						success: function(resp){
							$('#desplegable_resultados_inf').html(resp);
						}
					})
				}else{
					$('#desplegable_resultados_inf').html("");
				}				 
			},1000);
		};		
	});

	$("#mensaje_derivar").on("input",function(e){ // Accion que se activa cuando se digita #mensaje_derivar
		$(".errores").slideUp("slow");
		espacios_formulario('mensaje_derivar','primera',0);
	    var mensaje_derivar = $(this).val();
	    
	    if($(this).data("lastval")!= mensaje_derivar){
	    	$(this).data("lastval",mensaje_derivar);
	                
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('mensaje_derivar');		 			
			},1000);
	    };
	});

	$("#mensaje_informar").on("input",function(e){ // Accion que se activa cuando se digita #mensaje_informar
		$(".errores").slideUp("slow");
		espacios_formulario('mensaje_informar','primera',0);
	    var mensaje_informar = $(this).val();
	    
	    if($(this).data("lastval")!= mensaje_informar){
	    	$(this).data("lastval",mensaje_informar);
	                
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('mensaje_informar');		 			
			},1000);
	    };
	});

	$("#mensaje_reasignar").on("input",function(e){ // Accion que se activa cuando se digita #mensaje_derivar
		$(".errores").slideUp("slow");
		espacios_formulario('mensaje_reasignar','primera',0);
	    var mensaje_reasignar = $(this).val();
	    
	    if($(this).data("lastval")!= mensaje_reasignar){
	    	$(this).data("lastval",mensaje_reasignar);
	                
			clearTimeout(timerid);
			timerid = setTimeout(function() {
	 			validar_input('mensaje_reasignar');		 			
			},1000);
	    };
	});
});

/* Fin script para buscador input formularios envío de radicado */

	function agregar_usuario_destino(login_usuario_busq){
		console.log("Se agrega "+login_usuario_busq)
	}

	// function cargar_nuevo_usuario_derivar_radicado(login){
	// 	var usuario_actual_derivado 	= $("#usuario_actual_derivado").val();
	// 	var nuevo_usuario_actual  		= usuario_actual_derivado+login+",";

	// 	var usuarios_para_agregar  		= $("#usuarios_para_agregar_derivar").val();
	// 	var nuevo_usuarios_para_agregar = usuarios_para_agregar+login+",";

	// 	var usuario_actual_radicado 	= $("#usuario_actual_radicado").val();
	// 	var numero_radicado 			= $('#radicado').val();

	// 	$("#usuario_actual_derivado").val(nuevo_usuario_actual);
	// 	$("#usuarios_para_agregar_derivar").val(nuevo_usuarios_para_agregar);

	// 	$('#desplegable_resultados').html("");
		
	// 	$.ajax({
	// 		type: 'POST',
	// 		url: 'include/procesar_ajax.php',
	// 		data: {
	// 			'recibe_ajax' 				: 'listado_usuarios_nuevos',
	// 			'numero_radicado'			: numero_radicado,
	// 			'tipo_formulario'			: 'derivar_radicado',
	// 			'usuario_actual_radicado'	: usuario_actual_radicado,
	// 			'usuarios_nuevos'			: nuevo_usuarios_para_agregar
	// 		},			
	// 		success: function(resp){
	// 			$('#lista_usuarios_nuevos_derivar').html(resp);
	// 		}
	// 	})

	// 	$(".div_agregar_destinatario").slideDown("slow");
	// 	$("#input_agregar_usuario").slideUp("slow");
	// 	$("#usuario_actual_nuevo").val("");
	// 	$('#desplegable_resultados').html("");
	// }

	function cargar_nuevo_usuario_informar_radicado(login){
		var numero_radicado 			= $('#radicado').val();
		var usuario_actual_informado 	= $("#usuario_actual_informado").val();
		var usuario_actual_informar 	= $("#usuarios_nuevos_informar").val();
		var nuevo_usuario_informar  	= usuario_actual_informar+login+",";
		$("#usuarios_nuevos_informar").val(nuevo_usuario_informar);

		$("#input_agregar_usuario_inf").slideUp("slow");
		$("#usuario_actual_nuevo_inf").val("");
		$(".div_agregar_destinatario").slideDown("slow");
		$('#desplegable_resultados_inf').html("");

		$.ajax({
			type: 'POST',
			url: 'include/procesar_ajax.php',
			data: {
				'recibe_ajax' 				: 'listado_usuarios_nuevos',
				'numero_radicado'			: numero_radicado,
				'tipo_formulario'			: 'informar_radicado',
				'usuario_actual_radicado'	: usuario_actual_informado,
				'usuarios_nuevos'			: nuevo_usuario_informar
			},			
			success: function(resp){
				$('#lista_usuarios_nuevos_informar').html(resp);
				$('#mensaje_informar').focus();
			}
		})


	}
	function cerrar_pestana_usuario(login_usuario_busq){
		var listado_usuarios_nuevos 		= $("#usuarios_para_agregar_derivar").val();
		var nuevo_listado_usuarios_nuevos 	= listado_usuarios_nuevos.replace(login_usuario_busq+",", "");

		$("#usuarios_para_agregar_derivar").val(nuevo_listado_usuarios_nuevos);

		var usuario_actual 				= $("#usuario_actual_derivado").val();
		var nuevo_usuario_actual 		= usuario_actual.replace(login_usuario_busq+",", "");

		var usuario_actual_radicado 	= $("#usuario_actual_informado").val();
		var numero_radicado 			= $('#radicado').val();

		$("#usuario_actual_derivado").val(nuevo_usuario_actual);

		$.ajax({
			type: 'POST',
			url: 'include/procesar_ajax.php',
			data: {
				'recibe_ajax' 				: 'listado_usuarios_nuevos',
				'numero_radicado'			: numero_radicado,
				'tipo_formulario'			: 'cerrar_pestana',
				'usuario_actual_radicado'	: usuario_actual_radicado,
				'usuarios_nuevos'			: nuevo_listado_usuarios_nuevos
			},			
			success: function(resp){
				$('#lista_usuarios_nuevos_derivar').html(resp);
			}
		})

	}
	function cerrar_pestana_usuario_inf(login_usuario_busq){
		var listado_usuarios_nuevos_informar 		= $("#usuarios_nuevos_informar").val();
		var nuevo_listado_usuarios_nuevos_informar 	= listado_usuarios_nuevos_informar.replace(login_usuario_busq+",", "");

		var usuario_actual_radicado 	= $("#usuario_actual_informado").val();
		var numero_radicado 			= $('#radicado').val();

		$("#usuarios_nuevos_informar").val(nuevo_listado_usuarios_nuevos_informar);

		$.ajax({
			type: 'POST',
			url: 'include/procesar_ajax.php',
			data: {
				'recibe_ajax' 				: 'listado_usuarios_nuevos',
				'numero_radicado'			: numero_radicado,
				'tipo_formulario'			: 'cerrar_pestana',
				'usuario_actual_radicado'	: usuario_actual_radicado,
				'usuarios_nuevos'			: nuevo_listado_usuarios_nuevos_informar
			},			
			success: function(resp){
				$('#lista_usuarios_nuevos_informar').html(resp);
			}
		})

	}
/* Funcion para mostrar input para agregar destinatario */
	function mostrar_agregar_usuario(input){
		$(".div_agregar_destinatario").slideUp("slow");
		$("#input_agregar_usuario").slideDown("slow");
		$("#input_agregar_usuario_inf").slideDown("slow");
		$("#"+input).focus();
	}

	function ocultar_agregar_usuario(){

		if($(".resultado_busq_usuario").is(":visible")){
			// console.log("Hay que cargar el usuario")
		}else{
			$(".div_agregar_destinatario").slideDown("slow");

			$("#input_agregar_usuario").slideUp("slow");
			$("#input_agregar_usuario_inf").slideUp("slow");

			$("#usuario_actual_nuevo").val("");
			$("#usuario_actual_nuevo_inf").val("");

			$('#desplegable_resultados').html("");
			$('#desplegable_resultados_inf').html("");
			// console.log("ocultar_agregar_usuario")
		}
	}
/* Fin funcion para mostrar input para agregar destinatario */
	function validar_usuario_destino(){
		var usuario_destino = $("#usuario_destino").val();

		$("#mensaje_reasignar").focus();
     
        saludoArray = usuario_destino.split(','),
		ultima      = saludoArray[saludoArray.length - 1]
		$("#foto_usuario_destino").html("<img src='"+ultima+"' height='60px' style='border-radius: 10px;' title='Reasigna a "+saludoArray[1]+"'>")

		if(usuario_destino==''){
			$("#error_usuario_destino").slideDown("slow");
			return false;
		}else{
			$("#error_usuario_destino").slideUp("slow");
		    return true;
		}
	}
	function valida_reasignar_radicado(){ 
      	if(validar_usuario_destino()==true){
			validar_input('mensaje_reasignar');
			
			if($(".errores").is(":visible")){
		        return false;
		    }else{
		        var mensaje_reasignar 	= $('#mensaje_reasignar').val();
		        var numero_radicado 	= $('#radicado').val();
		        var usuario_actual 		= $('#usuario_actual').val();
		        var usuario_destino 	= $('#usuario_destino').val();

		        loading('boton_enviar_radicado')
		       
		        $.ajax({    
		            type: 'POST',
		            url: 'include/procesar_ajax.php',
		            data: {
		            	'recibe_ajax' 			: 'reasignar_radicado',
		                'usuario_destino' 		: usuario_destino,
		                'numero_radicado' 		: numero_radicado,
		                'usuario_actual' 		: usuario_actual,
		                'mensaje_reasignar' 	: mensaje_reasignar
		            },          
		            success: function(resp1){ 
		                if(resp1!=""){
		                    $("#resultados_solicitud").html(resp1);
		                }else{
		                    alert(resp1)
		                }
		                // console.log(resp1)
		            }
		        })
		    }
		}else{
			return false;
		}
	}
/* Fin validar y enviar formulario para reasignar radicado  */
/* Inicio validar y enviar formulario derivar radicado */
	// function valida_derivar_radicado(){ 
	// 	espacios_formulario('mensaje_derivar','primera',0);
	//     validar_input('mensaje_derivar');

	//     if($(".errores").is(":visible")){
	//         return false;
	//     }else{
	//         var codigo_carpeta 			= $('#codigo_carpeta_derivar').val();
	//         var mensaje_derivar 		= $('#mensaje_derivar').val();
	//         var numero_radicado 		= $('#radicado').val();
 //        	var usuario_actual_derivado = $("#usuario_actual_derivado").val(); // Variable para bandeja de entrada incluye usuario_actual
	//         var usuarios_para_derivar 	= $('#usuarios_para_agregar_derivar').val(); // Variable con listado de usuarios derivados
 //        	var usuarios_visor 			= $("#usuario_actual_informado").val(); 
	        
	//         // loading('boton_enviar_radicado')
	       
	//         $.ajax({    
	//             type: 'POST',
	//             url: 'include/procesar_ajax.php',
	//             data: {
	//             	'recibe_ajax' 				: 'derivar_radicado',
	//                 'codigo_carpeta' 			: codigo_carpeta,
	//                 'numero_radicado' 			: numero_radicado,
	//                 'usuarios_para_derivar' 	: usuarios_para_derivar,
	//                 'usuario_actual_derivado' 	: usuario_actual_derivado,
	//                 'usuarios_visor' 			: usuarios_visor,
	//                 'mensaje_derivar' 			: mensaje_derivar
	//             },          
	//             success: function(resp1){
	//                 if(resp1!=""){
	//                     $("#resultados_solicitud").html(resp1);
	//                 }else{
	//                     alert(resp1)
	//                 }
	//                 console.log(resp1)
	//             }
	//         })
	//     }
	// }
/* Fin validar y enviar formulario derivar radicado  */
/* Inicio validar y enviar formulario informar radicado */
	function valida_informar_radicado(){ 
		espacios_formulario('mensaje_informar','primera',0);
	    validar_input('mensaje_informar');

	    if($(".errores").is(":visible")){
	        return false;
	    }else{
	        var mensaje_informar 			= $('#mensaje_informar').val();
	        var numero_radicado 			= $('#radicado').val();
        	var usuario_actual_informado 	= $("#usuario_actual_informado").val(); // Variable para bandeja de entrada incluye usuario_actual
	        var usuarios_para_informar 		= $('#usuarios_para_agregar_informar').val(); // Variable con listado de usuarios derivados
	        var usuarios_nuevos_informar 	= $('#usuarios_nuevos_informar').val(); // Variable con listado de usuarios informar
	       	
	        loading('boton_enviar_informado')
	        var codigo_entidad = $("#codigo_entidad").val();

	       /* Esta es una modificacion temporal desarrollada para ejercito nacional con el fin de informar un radicado a varios usuarios del sistema tanto 
	       	en Jonas como en el numero de radicado generado en Orfeo-Ejercito. Se definen las variables y se envían mediante AJAX utilizando POST a la
	  		direccion de un servidor de Orfeo-Ejercito (http://172.22.2.226/pruebas/interoperabilidad_rest/api_jonas.php) donde internamente ese
	  		servidor de Orfeo-Ejercito genera las query correspondientes para informar el documento a los usuarios implicados.
	  		Si el tamaño de la respuesta son (16) caracteres, quiere decir que es un radicado Orfeo-Ejercito por lo que procede a informar el radicado a uno o varios usuarios.
	  		En caso que el tamaño de la respuesta no, sean (16) caracteres, quiere decir que hubo un error, por lo que devuelve en la consola del 
	  		navegador el mensaje "No se puede enviar, codigo de error XXXX". */

			if(codigo_entidad=='EJC'){
				var numero_radicado 			= $("#pasar_radicado").val();
				var usuario_destino 			= $("#usuario_destino").val();
				var usuario_origen 				= $("#usuario_origen").val();
				var usuarios_nuevos_informar 	= $("#usuarios_nuevos_informar").val();
		
				$.ajax({
		            type    	: 'POST',
		            url     	: 'http://172.22.2.226/pruebas/interoperabilidad_rest/api_jonas.php',
		            datatype 	: 'jsonp',
		            crossDomain : true,
		            data 		: {
						'recibe_jonas'     			: 'informar_radicado',
		                'login_destino'    			: usuario_destino,
		                'login_origen'     			: usuario_origen,
		                'mensaje_informar' 			: mensaje_informar,
		                'numero_radicado'   		: numero_radicado,
		                'usuarios_para_informar' 	: usuarios_nuevos_informar
		            },
		            success: function(resp){
		            	console.log(resp)
						if(resp.length==16){  // 16 es la longitud de caracteres de un radicado Orfeo-Ejercito

						}	
					}	
				})
			}
			/* Se realiza la transaccion "Informar" */
	        $.ajax({    
	            type: 'POST',
	            url: 'include/procesar_ajax.php',
	            data: {
	            	'recibe_ajax' 				: 'informar_radicado',
	                'mensaje_informar' 			: mensaje_informar,
	                'numero_radicado' 			: numero_radicado,
	                'usuarios_nuevos_informar' 	: usuarios_nuevos_informar,
	                'usuarios_para_informar' 	: usuarios_para_informar
	            },          
	            success: function(resp1){
	                if(resp1!=""){
	                    $("#resultado_js").html(resp1);
	                }else{
	                    alert("El resultado es vacío. Comuníquese con el administrador del sistema.")
	                }
	            }
	        })
	    }
	}
/* Fin validar y enviar formulario informar radicado  */
</script>
<?php

$timestamp = date('Y-m-d H:i:s');		// Genera la fecha de transaccion para historico eventos
//var_dump($_SESSION);
require_once('../../login/conexion2.php');

$dependencia 		= $_SESSION['dependencia'];
$login 				= $_SESSION['login'];
$perfil 			= $_SESSION['perfil'];
$reasignar_libre 	= $_SESSION['reasignar_libre'];

if(isset($_POST['transaccion'])){
	switch ($_POST['transaccion']) {
		case 'archivar_radicado':
			$radicado = $_POST['radicado'];

			$query_valida_trd 	= "select * from radicado where numero_radicado='$radicado'";
			$fila_valida_trd 	= pg_query($conectado,$query_valida_trd);
			$linea_valida_trd 	= pg_fetch_array($fila_valida_trd);
			$codigo_subserie 	= $linea_valida_trd['codigo_subserie'];
			$usuarios_control 	= $linea_valida_trd['usuarios_control'];

			/* Si no es un usuario_control quiere decir que solo ha sido informado por lo que no requiere TRD. Se compara si el $login está en los $usuarios_control */
			$busca_us_control 	= strpos($usuarios_control, $login);

			if($codigo_subserie=="" && $busca_us_control!==false){
				echo "<script>
					Swal.fire({	
						position 			: 'top-end',
					    showConfirmButton 	: false,
					    timer 				: 1500,	
					    title 				: 'Falta TRD.',
					    text 				: ' Puede asignarla en la pestaña (Información General del radicado $radicado)',
					    type 				: 'warning'
					}).then((result) => {
						var codigo_dependencia = $('#codigo_dependencia').val();
						console.log(codigo_dependencia)
		                asignar_trd(codigo_dependencia,'$radicado')
		            })
				</script>";
			}else{
				echo "<script>abrir_ventana('ventana_archivar_radicado');
					$('#usuario_actual_codigo_carpeta1').val('$login');
				</script>";
				
				$consulta_cantidad_carpetas_personales="select * from carpetas_personales c join usuarios u on c.id_usuario::varchar = u.id_usuario::varchar where u.login='$login' order by nombre_carpeta_personal";

				$fila_cantidad_carpetas_personales = pg_query($conectado,$consulta_cantidad_carpetas_personales);
		/*Calcula el numero de registros que genera la consulta anterior.*/
				$registros_carpetas_personales= pg_num_rows($fila_cantidad_carpetas_personales);
		/*Recorre el array generado e imprime uno a uno los resultados.*/	
				if($registros_carpetas_personales>0){	
					$query_carpeta_radicado_actual="select codigo_carpeta1 ->'$login' ->> 'codigo_carpeta_personal' as codigo_carpeta from radicado where numero_radicado='$radicado'";
					$fila_carpeta_radicado_actual 	= pg_query($conectado,$query_carpeta_radicado_actual);
					$linea_carpeta_radicado_actual 	= pg_fetch_array($fila_carpeta_radicado_actual);
					$codigo_carpeta_radicado_actual = $linea_carpeta_radicado_actual['codigo_carpeta'];

					echo "<script>$('#boton_archivar_radicado').slideDown('slow')</script>
						<select id='listado_carpetas_personales' class='select_opciones'>";
							for ($i=0;$i<$registros_carpetas_personales;$i++){
								$linea_carpetas_personales = pg_fetch_array($fila_cantidad_carpetas_personales);

								$id_carpeta_personal 	 = $linea_carpetas_personales['id'];
								$nombre_carpeta_personal = $linea_carpetas_personales['nombre_carpeta_personal'];
								
								if($codigo_carpeta_radicado_actual==""){
									echo "<option value='$id_carpeta_personal' title='Archivar radicado en carpeta $nombre_carpeta_personal' selected='selected'>
											$nombre_carpeta_personal 
										</option>";
								}else{
									if($id_carpeta_personal==$codigo_carpeta_radicado_actual){
										echo "<option value='$id_carpeta_personal' title='Archivar radicado en carpeta $nombre_carpeta_personal' selected='selected'>
											$nombre_carpeta_personal 
										</option>";
									}else{
										echo "<option value='$id_carpeta_personal' title='Archivar radicado en carpeta $nombre_carpeta_personal'>
											$nombre_carpeta_personal 
										</option>";
									}	
								}
							}
					echo "</select>";	
				}else{
					echo "<h3 style='color:red;'> No se han creado todavía carpetas personales</h3><script>$('#boton_archivar_radicado').slideUp('slow')</script>";
				}
			}
			break;

		case 'cancelar_solicitud':
			$cancelar_solicitud_id  	= $_POST['cancelar_solicitud_id'];
			$documento_solicitado  		= $_POST['documento_solicitado'];
			$radicado  					= $_POST['numero_radicado'];

			$query_cancelar_solicitud 	= "update prestamos set estado_prestamo='CANCELADO' where id = '$cancelar_solicitud_id'";
			$comentario 				="Se cancela la solicitud de préstamo por el usuario $login";
			$creado 					= $radicado;
			$transaccion 				= "cancelar_solicitud_prestamo";	// Variables para insertar historico
			if($documento_solicitado=="expediente_completo"){
				$transaccion_historico 	= "Se cancela la solicitud de préstamo del expediente completo";
			}else{
				$transaccion_historico 	= "Se cancela la solicitud de préstamo del documento individual";
			}

			if(pg_query($conectado,$query_cancelar_solicitud)){
				require_once("../../login/inserta_historico.php");
			}else{
				echo "<script> alert('Ocurrió un error al realizar la cancelación del préstamo, por favor comuníquese con el administrador del sistema.')</script>";
			}	
			break;

		case 'confirma_devolucion_fisico': // Recibe desde include/js/funciones_prestamos.js[function devolver_documento(id,documento_solicitado,id_documento_solicitado,numero_radicado)]
		    $documento_solicitado 		= $_POST['documento_solicitado'];
		    $id 						= $_POST['id_radicado'];
		    $id_documento_solicitado 	= $_POST['id_documento_solicitado'];
		    $radicado 					= $_POST['numero_radicado'];

		    $timestamp 			=  date("Y-m-d h:m:s"); // Traduce fecha formato "2016-06-05.10:06:05"

		    $query_confirmar_devolucion_fisico="update prestamos set estado_prestamo ='DEVUELTO', fecha_devolucion='$timestamp' where id=$id";

		    if($documento_solicitado=="expediente_completo"){
			    $comentario 		="Se devuelve a gestión documental el $documento_solicitado $id_documento_solicitado desde el radicado $radicado";
		    }else{
			    $comentario 		="Se devuelve a gestión documental el $documento_solicitado $radicado";
		    }
			$creado 				= $radicado;
			$transaccion 			= "devolucion_prestamo_fisico";	// Variables para insertar historico
			$transaccion_historico 	= "Devolución de préstamo en físico";	// Variables para insertar historico
    
		    if(pg_query($conectado,$query_confirmar_devolucion_fisico)){
				require_once("../../login/inserta_historico.php");
			}else{
				echo "<script> alert('Ocurrió un error al realizar la devolución del préstamo, por favor revisar e intentar nuevamente.')</script>";
			}	

    		break;	

   //  	case 'derivar_radicado': // Recibe desde bandejas/entrada/index_bandeja_entrada.php[function validar_transaccion(transaccion,div)]
			// $radicado = $_POST['radicado'];

			// $query_listado_usuario_actual = "select * from radicado where numero_radicado='$radicado'";

			// $fila_listado_usuario_actual 	= pg_query($conectado,$query_listado_usuario_actual);
			// $linea_listado_usuario_actual 	= pg_fetch_array($fila_listado_usuario_actual);
			// $usuarios_control 				= $linea_listado_usuario_actual['usuarios_control'];
			// $usuario_actual_radicado 		= $linea_listado_usuario_actual['usuarios_visor'];

			// echo "<script>$('#usuario_actual_derivado').val('$usuarios_control'); $('#usuario_actual_radicado').val('$usuario_actual_radicado'); </script>";

			// $usu  = explode(",", $usuarios_control);
			// $max  = sizeof($usu);
			// $max2 = $max-1;

			// $usuario_actual1="";	
			
			// for ($m=0; $m < $max2; $m++) { 
			// 	$login_usuario_busq = $usu[$m];

			// 	$consulta_usuario_busq 	= "select * from usuarios u join dependencias d on u.codigo_dependencia=d.codigo_dependencia where u.login='$login_usuario_busq'";

			// 	$fila_usuario_busq 	  				= pg_query($conectado,$consulta_usuario_busq);
			// 	$linea_usuario_busq    				= pg_fetch_array($fila_usuario_busq);
			// 	$login_usuario_busq   				= $linea_usuario_busq['login'];
			// 	$nombre_usuario_busq   				= $linea_usuario_busq['nombre_completo'];
			// 	$path_foto_usuario_busq 			= $linea_usuario_busq['path_foto'];
			// 	$nombre_dependencia_usuario_busq  	= $linea_usuario_busq['nombre_dependencia'];

			// 	$nombre_usuario_busq2 = substr($nombre_usuario_busq,0,20);
			// 	$nombre_usuario_busq1 = $nombre_usuario_busq2."...";

			// 	$usuario_actual1.="
			// 		<div class='lista_destinatario'>
			// 			<img src='$path_foto_usuario_busq' style='width: 30px; height: 30px; border-radius: 20px;'>
			// 			<div class='nombre_destinatario'>
			// 				$nombre_usuario_busq1
			// 			</div>
			// 			<div class='li'>
			// 				<table style='width: 400px; border: #2D9DC6 2px solid; border-radius:15px;'>
			// 					<tr>
			// 						<td rowspan=2 width='1%'>
			// 							<img src='$path_foto_usuario_busq' style='width: 50px;border-radius: 10px;'> 
			// 						</td>
			// 						<td width='39%'>
			// 							$nombre_usuario_busq <br>($login_usuario_busq)
			// 						</td>
			// 					</tr>
			// 						<td >
			// 							<b>$nombre_dependencia_usuario_busq</b>
			// 						</td>
			// 					<tr>
			// 					</tr>
			// 				</table>
			// 			</div>
			// 		</div> 
			// 	";
			// }
			echo $usuario_actual1."
			<div id='lista_usuarios_nuevos_derivar' style='display: inline-block; float:left;'></div>	
			<div class='div_agregar_destinatario' onclick='mostrar_agregar_usuario(\"usuario_actual_nuevo\")' style='display: inline-block; float:left;'></div>
			<div id='input_agregar_usuario' class='hidden'>
				<input type='search' name='usuario_actual_nuevo' id='usuario_actual_nuevo' title='Nuevo destinatario para radicado' title='Ingrese nuevo usuario para radicado' onblur='ocultar_agregar_usuario()'><br>
				<div id='desplegable_resultados' style='display: inline-block; cursor: pointer; max-height: 200px; overflow: scroll; width:500;'></div>
			</div>";

			break;
				
		case 'informar_radicado': // Recibe desde bandejas/entrada/index_bandeja_entrada.php[function validar_transaccion(transaccion,div)]
			$radicado = $_POST['radicado'];

			$query_listado_usuario_actual = "select * from radicado where numero_radicado='$radicado'";

			$fila_listado_usuario_actual 	= pg_query($conectado,$query_listado_usuario_actual);
			$linea_listado_usuario_actual 	= pg_fetch_array($fila_listado_usuario_actual);
			$usuarios_visor 				= $linea_listado_usuario_actual['usuarios_visor'];
			$codigo_carpeta1 				= $linea_listado_usuario_actual['codigo_carpeta1'];

			$codigo_carpeta2 				= json_decode($codigo_carpeta1,true);		

			$usuario_actual1=""; // Variable para imprimir los usuarios que tienen en bandejas el radicado
			$usuario_actual2=""; // Variable para listar usuarios y omitirlos en la consulta de agregar_informados

			if(is_null($codigo_carpeta1)){  // //Borrar // Para el modulo de informar desde radicacion de entrada no tiene valor $codigo_carpeta1
				echo "SE DEBE NULL";
			}else{

				foreach($codigo_carpeta2 as $usuario_informado => $detalles){
					$consulta_usuario_busq 	= "select * from usuarios u join dependencias d on u.codigo_dependencia=d.codigo_dependencia where u.login='$usuario_informado';";

					$fila_usuario_busq 	  				= pg_query($conectado,$consulta_usuario_busq);
					$linea_usuario_busq    				= pg_fetch_array($fila_usuario_busq);
					$login_usuario_busq   				= $linea_usuario_busq['login'];
					$nombre_usuario_busq   				= $linea_usuario_busq['nombre_completo'];
					$path_foto_usuario_busq 			= $linea_usuario_busq['path_foto'];
					$nombre_dependencia_usuario_busq  	= $linea_usuario_busq['nombre_dependencia'];

					$nombre_usuario_busq2 = substr($nombre_usuario_busq,0,20);
					$nombre_usuario_busq1 = $nombre_usuario_busq2."...";

					if($usuarios_visor!=$login_usuario_busq){
						$usuario_actual2.= "$login_usuario_busq,";
					}

					$usuario_actual1 .= "
					<div class='lista_destinatario'>
						<img src='$path_foto_usuario_busq' style='width: 30px; height: 30px; border-radius: 20px;'>
						<div class='nombre_destinatario'>
							$nombre_usuario_busq1
						</div>
						<div class='li'>
							<table style='width: 400px; border: #2D9DC6 2px solid; border-radius:15px;'>	
								<tr>
									<td rowspan=2 width='1%'>
										<img src='$path_foto_usuario_busq' style='width: 50px;border-radius: 10px;'> 
									</td>
									<td width='39%'>
										$nombre_usuario_busq <br>($login_usuario_busq)
									</td>
								</tr>
									<td >
										<b>$nombre_dependencia_usuario_busq</b>
									</td>
								<tr>
								</tr>
							</table>
						</div>
					</div> 
					";
				}

			} // Borrar

			echo "<script>$('#usuario_actual_informado').val('$usuarios_visor'); $('#usuarios_para_agregar_informar').val('$usuario_actual2')</script>";

			echo $usuario_actual1."
			<div id='lista_usuarios_nuevos_informar' style='display: inline-block; float:left;'></div>	
			<div class='div_agregar_destinatario' onclick='mostrar_agregar_usuario(\"usuario_actual_nuevo_inf\")' style='display: inline-block; float:left;'></div>
			<div id='input_agregar_usuario_inf' class='text' style='display: none;'>
				<input type='search' name='usuario_actual_nuevo_inf' id='usuario_actual_nuevo_inf' style='border-radius: 8px; padding: 8px; width: 250px;' title='Nuevo destinatario para informar radicado' title='Ingrese nuevo usuario para radicado' onblur='ocultar_agregar_usuario()'><br>
				<div id='desplegable_resultados_inf' style='display: inline-block; cursor: pointer; max-height: 200px; overflow-y: scroll; width:500;'></div>
			</div>";
			break;
		
		case 'reasignar_radicado': // Recibe desde bandejas/entrada/index_bandeja_entrada.php[function validar_transaccion(transaccion,div)]
			$radicado = $_POST['radicado'];

			$query_listado_usuario_actual = "select * from radicado where numero_radicado='$radicado'";

			$fila_listado_usuario_actual 	= pg_query($conectado,$query_listado_usuario_actual);
			$linea_listado_usuario_actual 	= pg_fetch_array($fila_listado_usuario_actual);
			$usuarios_control 				= $linea_listado_usuario_actual['usuarios_control'];

			echo "<script>$('#usuario_actual').val('$usuarios_control');$('#usuarios_para_agregar').val('')</script>";
	/* Desde aqui falta revisar porque en las entidades se requiere que sea por canal de distribución. */	
			if($reasignar_libre=='SI'){ // Se desarrolla para reasingar a cualquier usuario.
				$usu  = explode(",", $usuario_actual);
				$max  = sizeof($usu);
				$max2 = $max-1;

				$usuario_actual1="";
				
				if($max2==0){
					echo "max2 es $max2";
				}else{
					for ($m=0; $m < $max2; $m++) { 
						$login_usuario_busq = $usu[$m];

						$consulta_usuario_busq 	= "select * from usuarios u join dependencias d on u.codigo_dependencia=d.codigo_dependencia where u.login='$login_usuario_busq'";

						$fila_usuario_busq 	  				= pg_query($conectado,$consulta_usuario_busq);
						$linea_usuario_busq    				= pg_fetch_array($fila_usuario_busq);
						$login_usuario_busq   				= $linea_usuario_busq['login'];
						$nombre_usuario_busq   				= $linea_usuario_busq['nombre_completo'];
						$path_foto_usuario_busq 			= $linea_usuario_busq['path_foto'];
						$nombre_dependencia_usuario_busq  	= $linea_usuario_busq['nombre_dependencia'];

						$nombre_usuario_busq2 = substr($nombre_usuario_busq,0,20);
						$nombre_usuario_busq1 = $nombre_usuario_busq2."...";

						$usuario_actual1 = $usuario_actual1."
							<div class='lista_destinatario'>
								<img src='$path_foto_usuario_busq' style='width: 30px; height: 30px; border-radius: 20px;'>
								<div class='nombre_destinatario'>
									$nombre_usuario_busq1
								</div>
								<div class='li'>
									<table style='width: 400px; border: #2D9DC6 2px solid; border-radius:15px;'>	
										<tr>
											<td rowspan=2 width='1%'>
												<img src='$path_foto_usuario_busq' style='width: 50px;border-radius: 10px;'> 
											</td>
											<td width='39%'>
												$nombre_usuario_busq <br>($login_usuario_busq)
											</td>
										</tr>
											<td >
												<b>$nombre_dependencia_usuario_busq</b>
											</td>
										<tr>
										</tr>
									</table>
								</div>
							</div> 
						";
					}
					echo $usuario_actual1."
					<div id='lista_usuarios_nuevos_reasignar' style='display: inline-block; float:left;'></div>	
					<div class='div_agregar_destinatario' onclick='mostrar_agregar_usuario(\"usuario_actual_nuevo\")' style='display: inline-block; float:left;'></div>
					<div id='input_agregar_usuario' class='hidden'>
						<input type='search' name='usuario_actual_nuevo' id='usuario_actual_nuevo' title='Nuevo destinatario para radicado' title='Ingrese nuevo usuario para radicado' onblur='ocultar_agregar_usuario()'><br>
						<div id='desplegable_resultados' style='display: inline-block; cursor: pointer;'></div>
					</div>";
				}
	/* Hasta aqui falta revisar porque en las entidades se requiere que sea por canal de distribución. */	
			}else{
				if($perfil=="DISTRIBUIDOR_DEPENDENCIA"){
					$query_dependencias="select u.nombre_completo, u.login, u.codigo_dependencia, d.nombre_dependencia, u.perfil, u.path_foto from usuarios u join dependencias d on u.codigo_dependencia=d.codigo_dependencia where (u.perfil='DISTRIBUIDOR_DEPENDENCIA' or u.codigo_dependencia='$dependencia') and u.login!='$login' and activa='SI' and estado='ACTIVO' order by 4, 1";
				}else{
					$query_dependencias="select u.nombre_completo, u.login, u.codigo_dependencia, d.nombre_dependencia, u.perfil, u.path_foto from usuarios u join dependencias d on u.codigo_dependencia=d.codigo_dependencia where u.codigo_dependencia='$dependencia' and u.login!='$login' and activa='SI' and estado='ACTIVO' order by 4, 1";
				}

				$fila_query_dependencias 	= pg_query($conectado,$query_dependencias);
				$cantidad_dependencias 		= pg_num_rows($fila_query_dependencias);

			/* Recorre el array generado e imprime el select con cada uno de los resultados. */	
				echo "<select name='dependencia_destino' id='usuario_destino' class='select_opciones' onchange='validar_usuario_destino()'>
				<option value='' selected='selected'>--- Seleccione un usuario para reasignar ---</option>"; // Inicia el input select

					for ($t=0;$t<$cantidad_dependencias;$t++){
						$linea_termino = pg_fetch_array($fila_query_dependencias);

						$codigo_dependencia = $linea_termino['codigo_dependencia'];
						$nombre_dependencia = $linea_termino['nombre_dependencia'];
						$nombre_completo  	= $linea_termino['nombre_completo'];
						$login  			= $linea_termino['login'];
						$perfil  			= $linea_termino['perfil'];
						$path_foto  		= $linea_termino['path_foto'];

						if($perfil=="DISTRIBUIDOR_DEPENDENCIA"){
							$marca_distribuidor_dependencia = "[DD]";
							echo "<option value='$codigo_dependencia,$login,$path_foto' style='background-color: #2D9DC6; color: #FFFFFF;' title='Usuario [DD] DISTRIBUIDOR DEPENDENCIA ($nombre_dependencia)'>[DD]($codigo_dependencia) $nombre_dependencia ($nombre_completo)</option>";
						}else{
							echo "<option value='$codigo_dependencia,$login,$path_foto' title='Este usuario hace parte de SU DEPENDENCIA'>($codigo_dependencia) $nombre_dependencia ($nombre_completo)</option>";					
						}
					
					}
				echo "</select>"; // Fin del input select
			}
			break;

		case 'solicitud_prestamo_documento':
			$codigo_documento=$_POST['numero_documento'];
			$documento_solicitado=$_POST['tipo_solicitud'];
			$termino=$_POST['termino'];
			$transaccion=$_POST['transaccion'];
			$observaciones_prestamo=$_POST['observaciones_prestamo'];

			$login_solicitante=$login;

			if($documento_solicitado=='documento_individual'){
				$comentario="Documento individual solicitado en préstamo.<br>$observaciones_prestamo"; // Variables para insertar historico
				$transaccion_historico="Solicitud préstamo de documento individual";	// Variables para insertar historico
				$creado="documento individual $codigo_documento";	// Variables para auditoria_general	
			}else{
				$comentario="Expediente completo número $codigo_documento solicitado en préstamo.<br>$observaciones_prestamo"; // Variables para insertar historico
				$transaccion_historico="Solicitud préstamo de expediente completo";	// Variables para insertar historico
				$creado="expediente completo $codigo_documento";	// Variables para auditoria_general	
			}
			
			 /* Se define consecutivo de la tabla prestamos */
 			$query_max_prestamo="select max(id+1) from prestamos;";

			$fila_max_prestamo = pg_query($conectado,$query_max_prestamo);
			$linea_prestamo = pg_fetch_array($fila_max_prestamo);

			if($linea_prestamo[0]==""){
				$max_id_prestamo=1;	// Inicia el consecutivo del préstamo
			}else{
				$max_id_prestamo =$linea_prestamo[0];	
			}
			/* Fin definición consecutivo de la tabla prestamos */

			$radicado=$_POST['radicado'];		// Variables para insertar historico		

			$query_solicitud_prestamo="insert into prestamos(id, login_solicitante, dependencia_solicitante, documento_solicitado, id_documento_solicitado, numero_radicado, estado_prestamo, dias_solicitados, fecha_solicitud, observaciones ) values($max_id_prestamo,'$login_solicitante', '$dependencia', '$documento_solicitado', '$codigo_documento', '$radicado', 'SOLICITADO', $termino, '$timestamp', '$observaciones_prestamo')";

			$transaccion="solicitud_prestamo_documento";	// Variables para insertar historico		
				
			if(pg_query($conectado,$query_solicitud_prestamo)){
				require_once("../../login/inserta_historico.php");
			}else{
				echo "<script> alert('Ocurrió un error al realizar el préstamo, por favor revisar e intentar nuevamente.')</script>";
			}	

			break;	
	}
}
?>	