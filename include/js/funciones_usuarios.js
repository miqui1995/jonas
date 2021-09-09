/* Script para ventana modal - Tecla Esc */
    window.addEventListener("keydown", function(event){
        var codigo = event.keyCode || event.which;
        if (codigo == 27){
            cerrarVentanaCrearUsuarios();
            cerrarVentanaModificarUsuarios()
        }
        if(codigo== 8){ // Opcion para restringir que la tecla backspace da atras en el navegador.
        	if (history.forward(1)) {
				location.replace(history.forward(1));
			}	
        }
    }, false);
/* Fin script para ventana modal - Tecla Esc */

/**************************************************************	
* @class Funcion para que al dar en la tecla "Enter" avance al siguiente campo del formulario  
* @description Al ubicarse sobre cualquiera de los input del formulario y desde el teclado pulsar "Enter" 
* avanza al siguiente input que sea visible. 
* Al llegar al último input, toma el valor del input "tipo_formulario" y dependiendo del valor, llama la funcion
* que se utiliza para enviar dicho formulario.
* @param {} No recibe parametros. 
**************************************************************/
$("body").on("keydown", "input, select, textarea", function(e) {
  	var self 	= $(this),
    form 		= self.parents("form:eq(0)"),
    focusable,
    next;
  
  	// Al presionar la tecla enter
  	if (e.keyCode == 13) {
    	// busco el siguiente elemento
    	focusable 	= form.find("input,a,select,button,textarea").filter(":visible");
    	next 		= focusable.eq(focusable.index(this) + 1);
    
    	// si existe siguiente elemento, hago foco
    	if (next.length) {
     		next.focus();
    	} else {
      		// si no existe otro elemento
      		var tipo_formulario = $("#tipo_formulario").val();
      	/*	switch(tipo_formulario){
      			case "radicacion_entrada_normal":
		      		radicar_documento_entrada();
      			break;

      			case "formulario_modificar_radicado":
      				modificar_radicado();
      			break;

      			case 'radicacion_rapida':
      				submit_grabar_radicacion_rapida();
      			break;
      		}*/
    	}
    	return false;
  	}
});

/************************************************************************************************************/
/* Buscador - Administrador de Usuarios *********************************************************************/
/************************************************************************************************************/
var timerid="";
$(function buscador_usuarios(){
	$("#search_usuario").focus();

	$("#search_usuario").on("input",function(e){ // Accion que se activa cuando se digita #search_usuario
		loading('desplegable_resultados');
        var envio_usuario = $(this).val();
	    
	    if($(this).data("lastval")!= envio_usuario){
	    	$(this).data("lastval",envio_usuario);
                    
   			clearTimeout(timerid);
   			timerid = setTimeout(function() {
   				espacios_formulario("search_usuario","capitales",0)
		        $.ajax({
					type: 'POST',
					url: 'admin_usuarios/buscador_usuarios.php',
					data: {
						'search_usuario'  	: envio_usuario,
						'desde_formulario'  : '1'
					},			
					success: function(resp){
						if(resp!=""){
							$('#desplegable_resultados').html(resp);
						}
					}
				}); 			 						 
   			},1000);
	    };
	});
});

function listado_usuarios_depe(){ 
	$.ajax({
		type: 'POST',
		url: 'admin_usuarios/buscador_usuarios.php',
		data: {
			'listado_usuarios_depe'  : 'listado'
		},			
		success: function(resp){
			if(resp!=""){
				$('#resultado_usuarios_depe').html(resp);
			}
		}
	});
	$("#desplegable_resultados").html(''); 
}
/************************************************************************************************************/
/* Fin Buscador - Administrador de Usuarios *****************************************************************/
/************************************************************************************************************/

/************************************************************************************************************/
/* Formulario Agregar Nuevo Usuario *************************************************************************/
/************************************************************************************************************/
/* Script buscador nombre_completo - Formulario Agregar Nuevo Usuarios */
$("#nombre_completo").on("input",function(e){ // Accion que se activa cuando se digita #nombre_completo
	espacios_formulario('nombre_completo','capitales',0);
	loading('sugerencias_nombre_completo');
	$(".errores").slideUp("slow");

    var envio_nombre_completo = $(this).val();
    
    if($(this).data("lastval")!= envio_nombre_completo){
    	$(this).data("lastval",envio_nombre_completo);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
 			validar_input('nombre_completo');
 			var envio_nombre_comp = $("#nombre_completo").val();
    		$.ajax({
				type: 'POST',
				url: 'admin_usuarios/buscador_usuarios.php',
				data: {
					'search_nombre_completo' : envio_nombre_comp,
					'desde_formulario'  	 : '1' // Envio variable para que no salga "Para agregar usuario haga click aqui"
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_nombre_completo').html(resp);
					}
				}
			})			 			
		},1000);
    };
});
/* Fin script buscador nombre_completo - Formulario Agregar Nuevo Usuarios */
/* Script buscador identificacion - Formulario Agregar Nuevo Usuarios */
$("#identificacion").on("input",function(e){ // Accion que se activa cuando se digita #identificacion
	espacios_formulario('identificacion','sin_caracteres',0);
	loading('sugerencias_identificacion');
	$(".errores").slideUp("slow");

    var envio_identificacion = $(this).val();
    
    if($(this).data("lastval")!= envio_identificacion){
    	$(this).data("lastval",envio_identificacion);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_input('identificacion');
			var envio_id = $("#identificacion").val();
    		$.ajax({
				type: 'POST',
				url: 'admin_usuarios/buscador_usuarios.php',
				data: {
					'search_identificacion' : envio_id,
					'desde_formulario'  	: '1' // Envio variable para que no salga "Para agregar usuario haga click aqui"
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_identificacion').html(resp);
					}
				}
			})			 					 
		},1000);
    };
});
/* Fin script buscador identificacion - Formulario Agregar Nuevo Usuarios */
/* Script buscador login - Formulario Agregar Nuevo Usuarios */
$("#login").on("input",function(e){ // Accion que se activa cuando se digita #login
	espacios_formulario('login','mayusculas',0);
	loading('sugerencias_login');
    $(".errores").slideUp("slow");

    var envio_login = $(this).val();
    
    if($(this).data("lastval")!= envio_login){
    	$(this).data("lastval",envio_login);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
	 		validar_input('login');

    		$.ajax({
				type: 'POST',
				url: 'admin_usuarios/buscador_usuarios.php',
				data: {
					'search_login'  	: envio_login,
					'desde_formulario'  : '1' // Envio variable para que no salga "Para agregar usuario haga click aqui"
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_login').html(resp);
					}
				}
			})			 		 				 
		},1000);
    };
});
/* Fin script buscador login - Formulario Agregar Nuevo Usuarios */
/* Script buscador mail - Formulario Agregar Nuevo Usuarios */
$("#mail").on("input",function(e){ // Accion que se activa cuando se digita #mail
	espacios_formulario('mail','minusculas',0);
    $(".errores").slideUp("slow");

    var envio_mail = $(this).val();
    
    if($(this).data("lastval")!= envio_mail){
    	$(this).data("lastval",envio_mail);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_input('mail');			 		 				 
		},1000);
    };
});
/* Fin script buscador mail - Formulario Agregar Nuevo Usuarios */
/* Script buscador dependencia - Formulario Agregar Nuevo Usuarios */
$("#dependencia").on("input",function(e){ // Accion que se activa cuando se digita #dependencia
	espacios_formulario('dependencia','mayusculas',0);
	loading('sugerencias_dependencia');
    $(".errores").slideUp("slow");

    var envio_dependencia = $(this).val();
    
    if($(this).data("lastval")!= envio_dependencia){
    	$(this).data("lastval",envio_dependencia);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
	 		validar_input('dependencia');
	 		
 			$.ajax({
				type: 'POST',
				url: 'admin_usuarios/buscador_usuarios.php',
				data: {
					'search_dependencia' : envio_dependencia,
					'desde_formulario' 	 : '1' // Envio variable para que no salga "Para agregar usuario haga click aqui"
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_dependencia').html(resp);
					}
				}
			})			 		
		},1000);
    };
});
/* Fin script buscador dependencia - Formulario Agregar Nuevo Usuarios */

/* Script validar si identificacion ya existe */
function valida_identificacion_ya_existe(){
	$("#identificacion").focus();
	$("#identificacion_ya_existe").slideDown('slow');
}
/* Fin script validar si identificacion ya existe */
/* Script validar si nombre_completo ya existe */
function valida_nombre_completo_ya_existe(){
	$("#nombre_completo").focus();
	$("#error_nombre_completo_ya_existe").slideDown('slow');
}
/* Fin script validar si nombre_completo ya existe */
/* Script validar si login ya existe */
function valida_login_ya_existe(){
	$("#login").focus();
	$("#error_login_ya_existe").slideDown();
}
/* Fin script validar si login ya existe */

/* Funciones para desplegar ventana modal Agregar Usuarios */
function abrirVentanaCrearUsuarios(){
	var nom = $("#search_usuario").val();

	$("#nombre_completo").val(nom);
	$("#ventana").slideToggle("slow");
	$("#nombre_completo").focus();
	$('#viewer').attr('src',''); 
	$('#viewer_mod').attr('src',''); 

	$("#viewer").slideUp("slow");
	$("#viewer_mod").slideUp("slow");

	$("#boton_crear_usuario").html("<input type='button' id='bCrearUsuario' class='botones' value='Crear Usuario' onclick='submit_agregar_usuario()'>");

	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}

function cerrarVentanaCrearUsuarios(){
	$("#ventana").slideUp("slow");
	$("#search_usuario").focus();

	$(".art1").slideUp("slow");

	$(".errores").slideUp("slow");

	$("#identificacion").val("");
	$("#nombre_completo").val("");
	$("#login").val("");
	$("#mail").val("");
	$("#dependencia").val("");
	$("#perfil").val("USUARIO");
	$("#imagen").val("");
	$("#imagen_firma").val("");

	$("#estado").val("ACTIVO");
	$("#usuario_nuevo").val("SI");
	$("#nivel_seguridad").val("1");

	$("#ventanilla_radicacion").val("NO");
}
/* Fin funciones para desplegar ventana modal Agregar Usuarios */
/* Script cargar campo dependencia - Formulario Agregar Usuarios */
function carga_dependencia(codigo_dependencia, dependencia){
	$(".errores").slideUp("slow");
	$("#codigo_dependencia").val(codigo_dependencia);
	$("#dependencia").val(dependencia);
	$("#sugerencias_dependencia").slideUp("slow");

	$("#perfil").focus();
	valida_perfil();
	valida_jefe_dependencia();
}
/* Fin script cargar campo dependencia - Formulario Agregar Usuarios */
/* Script pasa la opcion jefe a la opcion No */
function jefe_dependencia_n(){
	$('#agregar_jefe_dependencia').val("NO");
	$('#mod_jefe_dependencia').val("NO");
}
/* Funcion pasar opcion a NO*/
/* Script que valida si la opcion jefe del usuario esta disponible en la dependencia */
function valida_jefe_dependencia(){
	var agregar_jefe_dependencia = $("#jefe_dependencia").val();

	if(agregar_jefe_dependencia == 'NO'){
		$('#error_jefe_dependencia').hide("slow");
	}else{
		var depe_codi  	= $("#codigo_dependencia").val();

		$("#depe_jefe_dependencia").html(depe_codi);

		$.ajax({
			url:'admin_usuarios/buscador_usuarios.php',
			type: 'POST',
			data: {
				'search_jefe_dependencia_depe_codi'  	: depe_codi
			},
			success: function(resp){
					if(resp!=""){
					$('#sugerencias_perfil').html(resp);
				}
			}
		})
	}
}
/* Funcion validar identificacion para hacer submit */
/* Script que valida si la opcion jefe del usuario esta disponible en la dependencia */
function valida_jefe_dependencia_mod(){
	var mod_jefe_dependencia = $("#mod_jefe_dependencia").val();
	if(mod_jefe_dependencia == 'NO'){
		$('#mod_error_jefe_dependencia').hide();
	}else{
		var depe_codi 	= $("#mod_codigo_dependencia").val();
		var login   	= $("#mod_login").val();
		$("#mod_depe_jefe_dependencia").html(depe_codi);

		$.ajax({
			url:'admin_usuarios/buscador_usuarios.php',
			type: 'POST',
			data: {
				'mod_jefe_dependencia_depe_codi'  	: depe_codi,
				'mod_login' 						: login
			},
			success: function(resp){
					if(resp!=""){
					$('#sugerencias_perfil').html(resp);
					return false;
				}
			}
		})
	}
}
/* Funcion validar identificacion para hacer submit */
/* Script que valida si el perfil del usuario esta disponible en la dependencia */
function valida_perfil(){
	var depe_codi  	= $("#codigo_dependencia").val();
	var perfil 		= $("#perfil").val();
		
	$("#depe_perfil").html(depe_codi);
	$("#perfil_p").html(perfil);

	$.ajax({
		url:'admin_usuarios/buscador_usuarios.php',
		type: 'POST',
		data: {
			'search_perfil_depe_codi'  	: depe_codi,
			'search_perfil'  			: perfil,
			'desde_formulario' 			: '1'
		},
		success: function(resp){
				if(resp!=""){
				$('#sugerencias_perfil').html(resp);
			}
		}
	})
}
/* Funcion validar identificacion para hacer submit */
function validar_identificacion(){
	var identificacion =$('#identificacion').val()

	if($("#sugerencia_id").is(":visible")){
		$(".errores").slideUp("slow");
		$("#error_identificacion_ya_existe").slideDown("slow");
		return false;
	}else{
		if($("#error_no_es_numero").is(":visible")){
			$("#error_identificacion").slideUp("slow");
			$("#error_identificacion_ya_existe").slideUp("slow");
			return false;
		}else{
			$("#error_identificacion").slideUp("slow");
			$("#error_identificacion_ya_existe").slideUp("slow");
			return true;
		}
	}			
}
/* Fin funcion validar identificacion para hacer submit */
/* Funcion validar nombre para hacer submit */
function validar_nombre(){
	if($("#sugerencia_nombre_completo").is(":visible")){
		$("#error_nombre_completo_ya_existe").slideDown("slow");
		return false;
	}else{
		$("#error_nombre_completo_ya_existe").slideUp("slow");
		return true;
	}		
}
/* Fin funcion validar nombre para hacer submit */
/* Funcion validar identificacion para hacer submit */
function validar_identificacion(){
	if($("#sugerencia_id").is(":visible")){
		$("#identificacion_ya_existe").slideDown("slow");
		return false;
	}else{
		$("#identificacion_ya_existe").slideUp("slow");
		return true;
	}		
}
/* Fin funcion validar identificacion para hacer submit */
/* Funcion validar login para hacer submit */
function validar_login(){
	if($("#sugerencia_login").is(":visible")){
		$("#error_login_ya_existe").slideDown("slow");
		return false;
	}else{
		$("#error_login_ya_existe").slideUp("slow");
		return true;
	}			
}
/* Fin funcion validar login para hacer submit */
/* Funcion para validar dependencia para hacer submit */
function validar_dependencia(){
	if($("#sugerencias_dependencia").is(":visible")){
		$("#error_dependencia_invalida").slideDown("slow");
		return false;
	}else{
		if($("#error_dependencia_inexistente").is(":visible")){
			$("#error_dependencia_invalida").slideUp("slow");
			$("#error_dependencia_inexistente").slideDown("slow");
			return false
		}else{
			$("#error_dependencia_inexistente").slideUp("slow");
			return true;
		}
	}											
}
/* Fin funcion para validar dependencia para hacer submit */
/* Funcion para validar perfil para hacer submit */
function validar_perfil(){
	var imagen=$("#imagen").val();
	if($("#error_perfil").is(":visible")){
		$("#perfil").focus();
		return false;
	}else{
		if(imagen==""){
			$("#error_imagen").slideDown("slow");
			$("#imagen").focus()
			return false;
		}else{
			$("#error_imagen").slideUp("slow");
			if($("#error_imagen_invalida").is(":visible")){
				$("#error_imagen").slideUp("slow")
				return false;
			}else{
				$("#error_imagen").slideUp("slow");
				$("#error_imagen_invalida").slideUp("slow");
				return true;
			}
		}
	}	
}
/* Fin funcion para validar perfil para hacer submit */

/*Funcion para insertar datos - Formulario Agregar Usuario*/
function validar_agregar_usuario(){
	var validar_id =validar_identificacion();
	if(validar_id==false){
		$("#identificacion").focus()
		return false;
	}else{
		var validar_nom = validar_nombre();
		if(validar_nom==false){
			$("#nombre_completo").focus();
			return false;
		}else{
			var validar_log=validar_login();
			if(validar_log==false){
				$("#login").focus();
				return false;
			}else{
				var validar_depe=validar_dependencia();
				if(validar_depe==false){
					$("#dependencia").focus();
					return false;	
				}else{
					var validar_per=validar_perfil();
					if(validar_per==false){
						$("#perfil").focus();
						return false;	
					}else{
						if($("#error_jefe_dependencia").is(":visible")){
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

function submit_agregar_usuario(){
	validar_input('nombre_completo');
	validar_input('identificacion');
	validar_input('login');
	validar_input('mail');
	validar_input('dependencia');
	valida_perfil();
	valida_jefe_dependencia();
	var submit_agregar_usuario = validar_agregar_usuario();
	if(submit_agregar_usuario==false || $(".errores").is(":visible")){
		return false;
	}else{ // 	$('#formulario_agregar_usuario').submit(); // Realizar la creación del usuario	
		loading('boton_crear_usuario');

		var tipo_formulario1 			= $("#formulario_agregar_usuario").val();
		var identificacion1 			= $("#identificacion").val();	
		var nombre_completo1 			= $("#nombre_completo").val();
		var login1 						= $("#login").val();
		var mail1 						= $("#mail").val();
		var codigo_dependencia1 		= $("#codigo_dependencia").val();
		var dependencia1 				= $("#dependencia").val();
		var perfil1 					= $("#perfil").val();
		var estado1 					= $("#estado").val();
		var usuario_nuevo1 				= $("#usuario_nuevo").val();
		var nivel_seguridad1 			= $("#nivel_seguridad").val();
		var ventanilla_radicacion1  	= $("#ventanilla_radicacion").val();
		var scanner1 					= $("#scanner").val();
		var modificar_radicado1 		= $("#modificar_radicado").val();
		var inventario1 				= $("#inventario").val();
		var ubicacion_topografica1 		= $("#ubicacion_topografica").val();
		var creacion_expedientes1 		= $("#creacion_expedientes").val();
		var prestamo_documentos1 		= $("#prestamo_documentos").val();
		var cuadro_clasificacion1 		= $("#cuadro_clasificacion").val();
		var administrador_sistema1 		= $("#administrador_sistema").val();
		var jefe_dependencia1 			= $("#jefe_dependencia").val();
		var radicacion_salida1 			= $("#radicacion_salida").val();
		var radicacion_normal1 			= $("#radicacion_normal").val();
		var radicacion_interna1 		= $("#radicacion_interna").val();
		var radicacion_resoluciones1 	= $("#radicacion_resoluciones").val();

		var inputFileFoto = document.getElementById('imagen');
        var file = inputFileFoto.files[0];

		var inputFileFirma = document.getElementById('imagen_firma');
        var file_firma = inputFileFirma.files[0];

		var data = new FormData();
		data.append('imagen_firma',file_firma);
		data.append('imagen',file);
		data.append('tipo_formulario',tipo_formulario1);
		data.append('identificacion',identificacion1);
		data.append('nombre_completo',nombre_completo1);
		data.append('login',login1);
		data.append('mail',mail1);
		data.append('codigo_dependencia',codigo_dependencia1);
		data.append('dependencia',dependencia1);
		data.append('perfil',perfil1);
		data.append('estado',estado1);
		data.append('usuario_nuevo',usuario_nuevo1);
		data.append('nivel_seguridad',nivel_seguridad1);
		data.append('ventanilla_radicacion',ventanilla_radicacion1);
		data.append('scanner',scanner1);
		data.append('modificar_radicado',modificar_radicado1);
		data.append('inventario',inventario1);
		data.append('ubicacion_topografica',ubicacion_topografica1);
		data.append('creacion_expedientes',creacion_expedientes1);
		data.append('prestamo_documentos',prestamo_documentos1);
		data.append('cuadro_clasificacion',cuadro_clasificacion1);
		data.append('administrador_sistema',administrador_sistema1);
		data.append('jefe_dependencia',jefe_dependencia1);
		data.append('radicacion_salida',radicacion_salida1);
		data.append('radicacion_normal',radicacion_normal1);
		data.append('radicacion_interna',radicacion_interna1);
		data.append('radicacion_resoluciones',radicacion_resoluciones1);

		$.ajax({
			type 		: 'POST',
			url 		: 'admin_usuarios/query_usuarios.php',
			data 		: data,			
	        contentType : false,
	        processData : false,
			success: function(resp){
				if(resp!=""){
					$('#sugerencias_identificacion').html(resp);
				}
			}
		})	
	}												
}
/*Fin funcion para insertar datos de usuario*/
/************************************************************************************************************/
/* Fin Formulario Agregar Nuevo Usuario *********************************************************************/
/************************************************************************************************************/

/************************************************************************************************************/
/* Modificar Usuarios ***************************************************************************************/
/************************************************************************************************************/

/* Funciones para desplegar ventana modal Modificar Usuarios */

function abrirVentanaModificarUsuarios(){
	$("#ventana2").slideDown("slow");
	$('#mod_nombre_dependencia').focus();
	$("#contenido").css({'z-index':'100'});	// Modifico estilo para sobreponer ventana modal 
}

function cerrarVentanaModificarUsuarios(){
	$("#ventana2").slideUp("slow");
	$("#search_usuario").focus();

	$(".art1").slideUp("slow");

	$("#error_no_es_mod_numero").slideUp("slow");

	$("#mod_id_usuario").val("");
	$("#ant_mod_identificacion").val("");
	$("#mod_identificacion").val("");

	$("#ant_mod_nombre_completo").val("");
	$("#mod_nombre_completo").val("");
	
	$("#ant_mod_login").val("");
	$("#mod_login").val("");

	$("#mod_mail").val("");
	
	$("#mod_codigo_dependencia").val("");
	$("#mod_ant_mod_nom_depe").val("");
	$("#mod_nombre_dependencia").val("");

	$("#imagen_mod").val("");
	$("#imagen_firma_mod").val("");

	$("#viewer_mod").slideUp("slow");
	$('#viewer_mod').attr('src',''); 

	$("#viewer_mod2").slideUp("slow");
	$('#viewer_mod2').attr('src','');

	$("#mod_perfil").val("USUARIO");
	$("#mod_estado").val("ACTIVO");
	$("#mod_usuario_nuevo").val("SI");
	$("#mod_nivel_seguridad").val("1");

	$("#mod_ventanilla_radicacion").val("NO");
	$("#mod_scanner").val("NO");
}
/* Fin funciones para desplegar ventana modal Modificar Usuarios*/

/**************************************************************	
* @class Funcion para cargar los valores del usuario que se va a modificar  
* @description Cambia el contenido html del #boton_modificar_usuario, muestra y asigna el #viewer_mod, Si el 
** usuario tiene firma (Rúbrica o firma mecánica) muestra y asigna valor a #viewer_mod2, llena uno a uno los 
** valores del formulario de modificar radicado con los parámetros recibidos por la funcion, Despliega la 
** ventana, oculta los listados de opciones y errores del formulario y ubica en el primer campo del formulario
* @param string{identificacion} Numero de documento del usuario. 
* @param string{nombre_completo} Nombre completo del usuario. 
* @param string{imagen} Ruta en la cual se encuentra la foto del usuario. 
* @param string{login} Login del usuario. 
* @param string{mail} Correo electrónico del usuario. 
* @param string{codigo_dependencia} Codigo de la dependencia del usuario. 
* @param string{nombre_dependencia} Nombre de la dependencia del usuario. 
* @param string{perfil} Perfil del usuario. 
* @param string{estado} Estado (activo SI o NO) del usuario. 
* @param string{usuario_nuevo} Indica si el es nuevo para reiniciar la contraseña al ingresar a Jonas. 
* @param string{nivel_seguridad} Nivel de seguridad del usuario (Del 1 al 5). 
* @param string{id_usuario} Id en la tabla "usuario". 
* @param string{ventanilla_radicacion} Indica si tiene permiso para radicacion de entrada. 
* @param string{scanner} Indica si tiene permiso para scanner en puesto de trabajo. 
* @param string{modificar_radicado} Indica si tiene permiso para modificar radicados. 
* @param string{inventario} Indica si tiene permiso para modulo de inventario. 
* @param string{ubicacion_topografica} Indica si tiene permiso para modulo de ubicacion topografica. 
* @param string{creacion_expedientes} Indica si tiene permiso para crear expedientes. 
* @param string{cuadro_clasificacion} Indica si tiene permiso para modulo TRD. 
* @param string{jefe_dependencia} Indica si es el usuario jefe_dependencia para recibir reportes. 
* @param string{radicacion_salida} Indica si tiene permiso para radicacion de salida. 
* @param string{radicacion_normal} Indica si tiene permiso para radicacion normal. 
* @param string{radicacion_interna} Indica si tiene permiso para radicacion interna. 
* @param string{radicacion_resoluciones} Indica si tiene permiso para radicacion resoluciones. 
* @param string{administrador_sistema} Indica si tiene permiso para administracion del sistema. 
* @param string{path_firma} Cadena de texto que es la imagen de la firma del usuario encriptada en base64. 
* @return {} No retorna valores. Carga el formulario para modificar usuario completo. 
**************************************************************/
function cargar_modifica_usuario(identificacion,nombre_completo,imagen,login,mail,codigo_dependencia,nombre_dependencia,perfil,estado,usuario_nuevo,nivel_seguridad,id_usuario,ventanilla_radicacion,scanner,modificar_radicado,inventario,ubicacion_topografica,creacion_expedientes,prestamo_documentos,cuadro_clasificacion, jefe_dependencia,radicacion_salida,radicacion_normal,radicacion_interna,radicacion_resoluciones, administrador_sistema, path_firma){
	$("#boton_modificar_usuario").html("<input type='button' id='bModificarUsuario' class='botones' value='Modificar Usuario' onclick='submit_modificar_usuario()'>");

	/* Muestra y asigna valor a #viewer_mod */
	$("#viewer_mod").slideDown("slow");
	$('#viewer_mod').attr('src',imagen); 

	/* Si el usuario tiene firma (Rúbrica o firma mecánica) muestra y asigna valor a #viewer_mod2 */
	if(path_firma!=""){
		$("#viewer_mod2").slideDown("slow");
		$("#viewer_mod2").attr('src',path_firma);
	}

	/* LLena uno a uno los valores del formulario de modificar radicado */
	$('#ant_mod_nombre_completo').val(nombre_completo);
	$('#mod_nombre_completo').val(nombre_completo);
	$('#mod_id_usuario').val(id_usuario);
	$("#ant_mod_login").val(login);
	$('#mod_login').val(login);
	$('#ant_mod_identificacion').val(identificacion);
	$('#mod_identificacion').val(identificacion);
	$('#mod_mail').val(mail);
	$('#mod_perfil').val(perfil);
	$("#mod_ant_mod_nom_depe").val(nombre_dependencia);
	$('#mod_codigo_dependencia').val(codigo_dependencia);
	$('#mod_nombre_dependencia').val(nombre_dependencia);
	$('#mod_estado').val(estado);
	$('#mod_usuario_nuevo').val(usuario_nuevo);
	$('#mod_nivel_seguridad').val(nivel_seguridad);
	$('#mod_jefe_dependencia').val(jefe_dependencia);
	$('#mod_administrador_sistema').val(administrador_sistema);
	$('#mod_creacion_expedientes').val(creacion_expedientes);
	$('#mod_cuadro_clasificacion').val(cuadro_clasificacion);
	$('#mod_inventario').val(inventario);
	$('#mod_modificar_radicado').val(modificar_radicado);
	$('#mod_prestamo_documentos').val(prestamo_documentos);
	$('#mod_scanner').val(scanner);
	$('#mod_ubicacion_topografica').val(ubicacion_topografica);
	$('#mod_ventanilla_radicacion').val(ventanilla_radicacion);
	$('#mod_radicacion_salida').val(radicacion_salida);
	$('#mod_radicacion_normal').val(radicacion_normal);
	$('#mod_radicacion_interna').val(radicacion_interna);
	$('#mod_radicacion_resoluciones').val(radicacion_resoluciones);

	/* Despliega la ventana, oculta los listados de opciones y errores del formulario */
	abrirVentanaModificarUsuarios(); 
	$(".art1").slideUp("slow");
	$(".errores").slideUp("slow");

	$("#mod_nombre_completo").focus();
}

function valida_mod_dependencia_actu(){
	var mod_dependencia_actu 	= $("#mod_codigo_dependencia").val();
	var ant_mod_login 			= $("#ant_mod_login").val();

	$.ajax({
		url:'admin_usuarios/buscador_usuarios.php',
		type: 'POST',
		data: {
			'mod_dependencia_actu' 	: mod_dependencia_actu,
			'ant_mod_login'  		: ant_mod_login
		},
		success: function(resp){
				if(resp!=""){
				$('#sugerencias_mod_perfil').html(resp);
			}
		}
	})
}


/* Script cargar campo mod_dependencia - Formulario Modificar Usuarios */
function carga_mod_dependencia(codigo_mod_dependencia, mod_dependencia){
	valida_mod_perfil();
	valida_jefe_dependencia_mod();

	var mod_dependencia_actu 	= $("#mod_codigo_dependencia").val();
	var ant_mod_login 			= $("#ant_mod_login").val();

	$("#mod_codigo_dependencia").val(codigo_mod_dependencia);
	$("#mod_nombre_dependencia").val(mod_dependencia);
	$("#sugerencias_mod_dependencia").slideUp("slow");

	$(".errores").slideUp("slow");

	$("#mod_estado").focus();

	if(codigo_mod_dependencia!=mod_dependencia_actu){
		$.ajax({
			url:'admin_usuarios/buscador_usuarios.php',
			type: 'POST',
			data: {
				'mod_dependencia_actu' 	: mod_dependencia_actu,
				'ant_mod_login'  		: ant_mod_login
			},
			success: function(resp){
					if(resp!=""){
					$('#sugerencias_mod_perfil').html(resp);
				}
			}
		})
	}
}
/* Fin script cargar campo mod_dependencia - Formulario Modificar Usuarios */
/* Script buscador identificacion - Formulario Modificar Nuevo Usuarios */		
$("#mod_identificacion").on("input",function(e){ // Accion que se activa cuando se digita #mod_identificacion
	espacios_formulario('mod_identificacion','sin_caracteres',0);
	loading('sugerencias_mod_identificacion');
	$(".errores").slideUp("slow");

	var envio_mod_identificacion = $(this).val();	
	var envio_ant_mod_id  		 = $("#ant_mod_identificacion").val();

	if($(this).data("lastval")!= envio_mod_identificacion){
    	$(this).data("lastval",envio_mod_identificacion);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_input('mod_identificacion');
			$.ajax({
				type: 'POST',
				url: 'admin_usuarios/buscador_usuarios.php',
				data: {
					'search_mod_id'  	: envio_mod_identificacion,
					'search_ant_mod_id' : envio_ant_mod_id,
					'desde_formulario' 	: '1' // Envio variable para que no salga "Para agregar usuario haga click aqui"
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_mod_identificacion').html(resp);
					}
				}
			})		 						 
		},1000);
    };
})

function id_anterior(documento_usuario){ // Funcion que se activa cuando se digita el mismo numero de documento que tenía.
	$('#mod_identificacion').val(documento_usuario);
	$('#sugerencias_mod_identificacion').html('');
	$('.errores').slideUp('slow');

	$('#mod_nombre_completo').focus();
}
/* Fin script buscador identificacion - Formulario Modificar Nuevo Usuarios */
/* Script buscador nombre_completo - Formulario Modificar Nuevo Usuarios */
$("#mod_nombre_completo").on("input",function(e){ // Accion que se activa cuando se digita #mod_nombre_completo
	espacios_formulario('mod_nombre_completo','capitales',0);
	loading('sugerencias_mod_nombre_completo');
	$(".errores").slideUp("slow");

	var envio_mod_nombre_completo = $(this).val();	
	var envio_ant_mod_nombre =$("#ant_mod_nombre_completo").val();

	if($(this).data("lastval")!= envio_mod_nombre_completo){
    	$(this).data("lastval",envio_mod_nombre_completo);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_input('mod_nombre_completo');

			$.ajax({
				type: 'POST',
				url: 'admin_usuarios/buscador_usuarios.php',
				data: {
					'search_mod_nombre_completo' 	: envio_mod_nombre_completo,
					'search_ant_mod_nom'  			: envio_ant_mod_nombre,
					'desde_formulario' 				: '1' // Envio variable para que no salga "Para agregar usuario haga click aqui"
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_mod_nombre_completo').html(resp);
					}
				}
			})		 				 
		},1000);
    };
})

function nombre_anterior(mod_nombre_completo){
	$('#mod_nombre_completo').val(mod_nombre_completo);
	$('#sugerencias_mod_nombre_completo').html('');
	$('.errores').slideUp('slow');

	$('#mod_identificacion').focus();
}
/* Fin script buscador nombre_completo - Formulario Modificar Nuevo Usuarios */
/* Script buscador mod_login - Formulario Modificar Usuario */
$("#mod_login").on("input",function(e){ // Accion que se activa cuando se digita #mod_login
	espacios_formulario('mod_login','mayusculas',0);	
	loading('sugerencias_mod_login');
	$(".errores").slideUp("slow");

	var envio_mod_login 	= $(this).val();	
	var envio_ant_mod_login = $("#ant_mod_login").val();

	if($(this).data("lastval")!= envio_mod_login){
    	$(this).data("lastval",envio_mod_login);
            
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_input('mod_login');
			$.ajax({
				type: 'POST',
				url: 'admin_usuarios/buscador_usuarios.php',
				data: {
					'search_mod_login' 		: envio_mod_login,
					'search_ant_mod_login'  : envio_ant_mod_login,
					'desde_formulario'  	: '1' // Envio variable para que no salga "Para agregar usuario haga click aqui"
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_mod_login').html(resp);
					}
				}
			})		 					 
		},1000);
    };
})

function login_anterior(login){
	$('#mod_login').val(login);
	$('#sugerencias_mod_login').html('');
	$('.errores').slideUp('slow');

	$('#mod_mail').focus();
}
/* Fin script buscador mod_login - Formulario Modificar Usuario */
/* Script buscador mod_mail - Formulario Modificar Usuario */
$("#mod_mail").on("input",function(e){ // Accion que se activa cuando se digita #mod_mail
	espacios_formulario('mod_mail','minusculas',0);
    $(".errores").slideUp("slow");

    var envio_mod_mail = $(this).val();
    
    if($(this).data("lastval")!= envio_mod_mail){
    	$(this).data("lastval",envio_mod_mail);
                
		clearTimeout(timerid);
		timerid = setTimeout(function() {
			validar_input('mod_mail');			 		 				 
		},1000);
    };
});
/* Fin script buscador mod_mail - Formulario Modificar Usuario */
/* Script buscador mod_dependencia - Formulario Modificar Usuarios */

$("#mod_nombre_dependencia").on("input",function(e){ // Accion que se activa cuando se digita #mod_nombre_dependencia
	espacios_formulario('mod_nombre_dependencia','mayusculas',0);
	loading('mod_nombre_dependencia');
	$(".errores").slideUp("slow");

	var envio_mod_dependencia = $(this).val();	
	var search_ant_mod_depe   = $("#mod_ant_mod_nom_depe").val();

	if($(this).data("lastval")!= envio_mod_dependencia){
    	$(this).data("lastval",envio_mod_dependencia);
            
		clearTimeout(timerid);
		timerid = setTimeout(function() {
 			validar_input('mod_nombre_dependencia');
			$.ajax({
				type: 'POST',
				url: 'admin_usuarios/buscador_usuarios.php',
				data: {
					'search_mod_dependencia' 	: envio_mod_dependencia,
					'search_ant_mod_depe' 		: search_ant_mod_depe,
					'desde_formulario' 			: '1' // Envio variable para que no salga "Para agregar usuario haga click aqui"
				},			
				success: function(resp){
					if(resp!=""){
						$('#sugerencias_mod_dependencia').html(resp);
					}
				}
			})		 					 
		},1000);
    };
})
/* Fin script buscador mod_dependencia - Formulario Modificar Usuarios */

/* Script validar si identificacion ya existe - Formulario Modificar Usuario */
function valida_mod_identificacion_ya_existe(){
	$("#mod_identificacion").focus();
	$("#mod_identificacion_ya_existe").slideDown();
}
/* Fin script validar si identificacion ya existe - Formulario Modificar Usuario */
/* Script validar si nombre_completo ya existe - Formulario Modificar Usuario */
function valida_mod_nombre_completo_ya_existe(){
	$("#mod_nombre_completo").focus();
	$("#error_mod_nombre_completo_ya_existe").slideDown();
}
/* Fin script validar si nombre_completo ya existe - Formulario Modificar Usuario */
/* Script validar si mod_login ya existe - Formulario Modificar Usuario */
function valida_mod_login_ya_existe(){
	$("#mod_login").focus();
	$("#error_mod_login_ya_existe").slideDown();
}
/* Fin script validar si mod_login ya existe - Formulario Modificar Usuario */
/* Script que valida si el mod_perfil del usuario esta disponible en la dependencia - Formulario Modificar Usuario*/
function valida_mod_perfil(){
	var mod_depe_codi 	= $("#mod_codigo_dependencia").val();
	var mod_perfil 		= $("#mod_perfil").val();
	var ant_mod_login 	= $("#ant_mod_nombre_completo").val();
		
	$("#depe_mod_perfil").html(mod_depe_codi);
	$("#mod_perfil_p").html(mod_perfil);

	$.ajax({
		url:'admin_usuarios/buscador_usuarios.php',
		type: 'POST',
		data: {
			'search_mod_perfil_depe_codi' 	: mod_depe_codi,
			'search_mod_perfil'  			: mod_perfil,
			'ant_mod_login'  				: ant_mod_login,
			'desde_formulario'  			: '1'
		},
		success: function(resp){
				if(resp!=""){
				$('#sugerencias_mod_perfil').html(resp);
			}
		}
	})	
}
/* Fin script que valida si el mod_perfil del usuario esta disponible en la dependencia - Formulario Modificar Usuario*/
/* Script para validar campo mod_identificacion - Formulario Modificar Usuario */
function validar_mod_identificacion(){
	if($("#sugerencia_mod_id").is(":visible")){
		$("#error_mod_identificacion").slideUp("slow");
		$("#mod_identificacion_ya_existe").slideDown("slow");

		return false;
	}else{
		if($("#error_no_es_mod_numero").is(":visible")){
			$("#error_mod_identificacion").slideUp("slow");
			$("#mod_identificacion_ya_existe").slideUp("slow");
			return false;
		}else{
			$("#error_mod_identificacion").slideUp("slow");
			$("#mod_identificacion_ya_existe").slideUp("slow");
			return true;
		}
	}
}	
/* Fin script para validar campo mod_identificacion - Formulario Modificar Usuario */

/* Script para validar campo mod_nombre_completo - Formulario Modificar Usuario */
function validar_mod_nom_completo(){	
	if($("#sugerencia_mod_nom").is(":visible")){
		$("#error_mod_nombre_completo").slideUp("slow");
		$("#error_mod_nombre_completo_ya_existe").slideDown("slow");
		return false;
	}else{
		$("#error_mod_nombre_completo").slideUp("slow");
		$("#error_mod_nombre_completo_ya_existe").slideUp("slow");
		return true;
	}
}	
/* Fin script para validar campo mod_nombre_completo - Formulario Modificar Usuario */

/* Script para validar campo mod_login - Formulario Modificar Usuario */
function validar_modif_login(){
	if($("#sugerencia_mod_login").is(":visible")){
		$("#error_mod_login").slideUp("slow");
		$("#error_mod_login_ya_existe").slideDown("slow");
		return false;
	}else{
		$("#error_mod_login").slideUp("slow");
		$("#error_mod_login_ya_existe").slideUp("slow");
		return true;
	}
}	
/* Fin script para validar campo mod_login - Formulario Modificar Usuario */
/* Script para validar campo mod_dependencia - Formulario Modificar Usuario */
function validar_modif_dep(){			
	var mod_depe=$("#mod_nombre_dependencia").val();

	if(mod_depe==""){
		$("#error_mod_dependencia").slideDown("slow");
		$("#valida_minimo_mod_dependencia").slideUp("slow");
		$("#valida_maximo_mod_dependencia").slideUp("slow");
		$("#error_mod_dependencia_invalida").slideUp("slow");
		$("#error_mod_dependencia_inexistente").slideUp("slow");

		return false;					
	}else{
		if(mod_depe.length<6){
			$("#error_mod_dependencia").slideUp("slow");
			$("#valida_minimo_mod_dependencia").slideDown("slow");
			$("#valida_maximo_mod_dependencia").slideUp("slow");
			$("#error_mod_dependencia_invalida").slideUp("slow");
			$("#error_mod_dependencia_inexistente").slideUp("slow");

			return false;
		}else{
			if(mod_depe.length>50){
				$("#error_mod_dependencia").slideUp();
				$("#valida_minimo_mod_dependencia").slideUp("slow");
				$("#valida_maximo_mod_dependencia").slideDown("slow");
				$("#error_mod_dependencia_invalida").slideUp("slow");
				$("#error_mod_dependencia_inexistente").slideUp("slow");

				return false;
			}else{
				if($("#sugerencia_mod_dependencia").is(":visible")){
					$("#error_mod_dependencia").slideUp("slow");
					$("#valida_minimo_mod_dependencia").slideUp("slow");
					$("#valida_maximo_mod_dependencia").slideUp("slow");
					$("#error_mod_dependencia_invalida").slideDown("slow");
					$("#error_mod_dependencia_inexistente").slideUp("slow");

					return false;
				}else{
					if($("#error_mod_dependencia_inexistente").is(":visible")){
						$("#error_mod_dependencia").slideUp();
						$("#valida_minimo_mod_dependencia").slideUp("slow");
						$("#valida_maximo_mod_dependencia").slideUp("slow");
						$("#error_mod_dependencia_invalida").slideUp("slow");

						return false
					}else{
						$("#error_mod_dependencia").slideUp();
						$("#valida_minimo_mod_dependencia").slideUp("slow");
						$("#valida_maximo_mod_dependencia").slideUp("slow");
						$("#error_mod_dependencia_invalida").slideUp("slow");
						$("#error_mod_dependencia_inexistente").slideUp("slow");

						return true;
					}
				}		
			}
		}
	}			
}
/* Fin script para validar campo mod_dependencia - Formulario Modificar Usuario */
/* Funcion para validar la opcion de jede para hacer submit */
function validar_mod_jefe_dependencia(){
	if($("#mod_error_jefe_dependencia").is(":visible")){
		return false;
	}else{
		return true;		
	}	
}
/* Fin funcion para validar mod_perfil para hacer submit */
/* Funcion para validar mod_perfil para hacer submit */
function validar_mod_perfil(){
	if($("#error_mod_perfil").is(":visible")){
		return false;
	}else{
		if($("#error_mod_imagen_invalida").is(":visible")){
			return false;
		}else{
			$("#error_mod_imagen_invalida").slideUp("slow");
			return true;
		}		
	}	
}
/* Fin funcion para validar mod_perfil para hacer submit */

/* Funcion para modificar Usuarios */
function validar_modificar_usuario(){
	var validar_mod_id = validar_mod_identificacion();
	if(validar_mod_id==false){
		$("#mod_identificacion").focus()
		return false;
	}else{
		var validar_mod_nombre_completo= validar_mod_nom_completo();
		if(validar_mod_nombre_completo==false){
			$("#mod_nombre_completo").focus();
			return false;
		}else{
			var validar_mod_login= validar_modif_login();
			if(validar_mod_login==false){
				$("#mod_login").focus();
				return false;
			}else{		
				var validar_modif_depe=validar_modif_dep();
				if(validar_modif_depe==false){
					$("#mod_nombre_dependencia").focus();
					return false;
				}else{
					var validar_modif_perfil=validar_mod_perfil();
					if(validar_modif_perfil==false){
						$("#mod_perfil").focus();
						return false
					}else{
						var validar_modif_jefe_dependencia = validar_mod_jefe_dependencia();
						if(validar_modif_jefe_dependencia==false){
							return false
						}else{
							return true
						}
					}
				}				
			}
		}
	}
}

function submit_modificar_usuario(){
	validar_input('mod_nombre_completo');
	validar_input('mod_login');
	validar_input('mod_identificacion');
	validar_input('mod_mail');
	valida_mod_perfil();
	validar_input('mod_nombre_dependencia');
	valida_jefe_dependencia_mod();

	var submit_modificar_usuario = validar_modificar_usuario();

	/* Verificar si el estado del usuario es INACTIVO */
	var estado1							= $("#mod_estado").val();
	if(estado1=="INACTIVO"){
		valida_mod_dependencia_actu();
	}


	if(submit_modificar_usuario==false || $(".errores").is(":visible")){
		return false;
	}else{
		loading('boton_modificar_usuario');

		var tipo_formulario1				= $("#formulario_modificar_usuario").val();
		var mod_id_usuario1					= $("#mod_id_usuario").val();	
		var identificacion1					= $("#mod_identificacion").val();	
		var nombre_completo1				= $("#mod_nombre_completo").val();
		var login1							= $("#mod_login").val();
		var mail1							= $("#mod_mail").val();
		var codigo_dependencia1				= $("#mod_codigo_dependencia").val();
		var perfil1							= $("#mod_perfil").val();
		var usuario_nuevo1					= $("#mod_usuario_nuevo").val();
		var nivel_seguridad1				= $("#mod_nivel_seguridad").val();
		var mod_jefe_dependencia			= $("#mod_jefe_dependencia").val();
		var mod_administrador_sistema		= $("#mod_administrador_sistema").val();
		var creacion_expedientes1			= $("#mod_creacion_expedientes").val();
		var mod_cuadro_clasificacion1 		= $("#mod_cuadro_clasificacion").val();
		var inventario1						= $("#mod_inventario").val();
		var modificar_radicado1				= $("#mod_modificar_radicado").val();
		var mod_prestamo_documentos1		= $("#mod_prestamo_documentos").val();
		var scanner1						= $("#mod_scanner").val();
		var ubicacion_topografica1			= $("#mod_ubicacion_topografica").val();
		var ventanilla_radicacion1			= $("#mod_ventanilla_radicacion").val();
		var mod_radicacion_salida1 			= $('#mod_radicacion_salida').val();
		var mod_radicacion_normal1 			= $('#mod_radicacion_normal').val();
		var mod_radicacion_interna1 		= $('#mod_radicacion_interna').val();
		var mod_radicacion_resoluciones1  	= $('#mod_radicacion_resoluciones').val();

		var data = new FormData();
		
		var foto_usuario_mod 	= document.getElementById('imagen_mod');
        var file 				= foto_usuario_mod.files[0];

		if (file != undefined) {	// Si el formulario tiene la imagen para cargar
			data.append('imagen_mod',file);
		}

		var foto_firma_mod 		= document.getElementById('imagen_firma_mod');
        var file_firma 			= foto_firma_mod.files[0];

        if (file_firma != undefined) {	// Si el formulario tiene la imagen para cargar
			data.append('imagen_firma_mod',file_firma);
		}

		data.append('tipo_formulario',tipo_formulario1);
		data.append('mod_id_usuario',mod_id_usuario1);
		data.append('mod_identificacion',identificacion1);
		data.append('mod_nombre_completo',nombre_completo1);
		data.append('mod_login',login1);
		data.append('mod_mail',mail1);
		data.append('mod_codigo_dependencia',codigo_dependencia1);
		data.append('mod_perfil',perfil1);
		data.append('mod_estado',estado1);
		data.append('mod_usuario_nuevo',usuario_nuevo1);
		data.append('mod_nivel_seguridad',nivel_seguridad1);
		data.append('mod_jefe_dependencia', mod_jefe_dependencia);
		data.append('mod_administrador_sistema', mod_administrador_sistema);
		data.append('mod_creacion_expedientes',creacion_expedientes1);
		data.append('mod_cuadro_clasificacion',mod_cuadro_clasificacion1);
		data.append('mod_inventario',inventario1);
		data.append('mod_modificar_radicado',modificar_radicado1);
		data.append('mod_prestamo_documentos',mod_prestamo_documentos1);
		data.append('mod_scanner',scanner1);
		data.append('mod_ubicacion_topografica',ubicacion_topografica1);
		data.append('mod_ventanilla_radicacion',ventanilla_radicacion1);
		data.append('mod_radicacion_salida', mod_radicacion_salida1);
		data.append('mod_radicacion_normal', mod_radicacion_normal1);
		data.append('mod_radicacion_interna', mod_radicacion_interna1);
		data.append('mod_radicacion_resoluciones', mod_radicacion_resoluciones1);

		$.ajax({
			type: 'POST',
			url: 'admin_usuarios/query_usuarios.php',
			data: data,			
	        contentType:false,
	        processData:false,
			success: function(resp){
				if(resp!=""){
					$('#sugerencias_mod_identificacion').html(resp);
				}
			}
		})			
	}										
}


/************************************************************************************************************/
/* Fin Modificar Usuarios ***********************************************************************************/
/************************************************************************************************************/

/************************************************************************************************************/
/* Inicio Modificar Contraseña ******************************************************************************/
/************************************************************************************************************/
function modificar_pass_usuario(){
	var id_usuario = $('#id_usuario').val();
	var pass1 = $('#pass1').val();
	var pass2 = $('#pass2').val();

	if(pass1.length<6){
		$('#valida_pass_vacio').slideDown("slow");
		$('#pass1').focus();
	}else{
		$('#valida_pass_vacio').slideUp("slow");
		if(pass1!=pass2){
			$('#valida_pass_confirmacion').slideDown("slow");
			$('#pass2').focus();
		}else{
			$('#valida_pass_confirmacion').slideUp("slow");

			$.ajax({
				url:'login/modifica_pass.php',
				type: 'POST',
				data: {
					'id_usuario':id_usuario,
					'pass':pass2 
				},
				success: function(resp){
					$.ajax({	// Guardo registro de ingreso al sistema para auditoria
						type: 'POST',
						url: 'login/transacciones.php',
						data: {
							'transaccion' : 'cambio_pass'	
						},			
						success: function(resp1){
							if(resp1=="true"){
								// sweetAlert({
								Swal.fire({	
									position 			: 'top-end',
								    showConfirmButton 	: false,
								    timer 				: 1500,
								    title 				: resp,
								    text 				: '',
								    type 				:'success'
								}).then(function(isConfirm){
									// if(isConfirm){
										location.href='principal3.php';		
									// }
								})	
							}else{
								alert(resp1)
							}
						}
					})	
				}
			})	
		}
	}
}

/************************************************************************************************************/
/* Fin Modificar Contraseña *********************************************************************************/
/************************************************************************************************************/