<?php
	if(!isset($_SESSION)){// Se valida que la session_start no este creada como causa de un heredamiento
		session_start();
	}
	require_once("../login/validar_inactividad.php");
	require_once('../include/genera_fecha.php');
	require_once('../login/conexion2.php');
	
	$id_usuario 		= $_SESSION['id_usuario'];
	$usuario        	= $_SESSION['nombre'];
	$consulta_usuario 	= "select * from usuarios where id_usuario = $id_usuario";// Consulta sql
	$fila_usuario 		= pg_query($conectado,$consulta_usuario);// Se ejecuta la consulta
	$linea 				= pg_fetch_array($fila_usuario);// Se cambia las filas por una matriz
	$path_foto			= $linea['path_foto'];
	$cargo_usuario		= $linea['cargo_usuario'];
	$mail 				= $linea['mail_usuario'];
?>
<style type="text/css">
	input {
		border-radius 	: 8px;
		font-size 	 	: 15px;
		padding 		: 6px;
		width 			: 95%;
	}
</style>
<!DOCTYPE html>
<html> 
	<head>
		<meta charset="UTF-8">
		<title>Gestionar Datos del Usuario</title>
		<script type="text/javascript" src="include/js/funciones_usuarios.js"></script>
		<link rel="stylesheet" href="include/css/estilos_usuarios.css">
	</head>
	<body>
		<script>
			var gestionardatos_mail 	= "";
			var segunda_pass  			= "";
			/*****************************************************************************************
				Function validar_pass() Valida que el campo de la segunda contraseña no tenga menos de 6 caracteres
			/*****************************************************************************************
				* @return Abrirá o cerrara el div de errores correspondiente
			*****************************************************************************************/
			function validar_pass(){
				var id_usuario = <?php echo "$id_usuario" ?>;
				var gestionardatos_segunda_clave = $('#gestionardatos_segunda_clave').val();
				if(gestionardatos_segunda_clave.length<6){
					$('#valida_segunda_clave_vacio').slideDown("slow");
				}else{
					$('#valida_segunda_clave_vacio').slideUp("slow");
				}
			}
			/*****************************************************************************************
				Fin Function validar_pass()
			/*****************************************************************************************/
			/*****************************************************************************************
				Function Guardar_formulario_gestionardatos() Valida que los campos no tengan errores y hará la petición ajax para guardar los datos y otro ajax para generar  histórico
			/*****************************************************************************************
				* @return Mensaje de alerta
			*****************************************************************************************/
			function Guardar_formulario_gestionardatos(){
				var data = new FormData();
				data.append('recibe_ajax','guardar_gestionar_datos_usuario');// append nos ayudara a juntar todos los datos
				data.append('id_usuario', <?php echo "$id_usuario" ?>);// append nos ayudara a juntar todos los datos
				if(!($('.errores').is(":visible"))){// Valida que ningún mensaje de error este abierto
					var inputFile = document.getElementById('gestionardatos_imagen');
			        var file 	  = inputFile.files[0];
					if(file != undefined) {// Si el formulario tiene la imagen para guardar
						data.append('gestionardatos_imagen',file);// append nos ayudara a juntar todos los datos
					}else{
						data.append('gestionardatos_imagen','');// append nos ayudara a juntar todos los datos
					}
					if($("#gestionardatos_segunda_clave").val() != 0){// Valida que el formulario tenga el campo de segunda contraseña
						segunda_pass = $("#gestionardatos_segunda_clave").val();
					}
					
					var cargo_usuario 	= $("#cargo_usuario").val();
					gestionardatos_mail = $("#gestionardatos_mail").val();

					data.append('correo', gestionardatos_mail);// append nos ayudara a juntar todos los datos
					data.append('segunda_clave', segunda_pass);// append nos ayudara a juntar todos los datos
					data.append('cargo_usuario', cargo_usuario);// append nos ayudara a juntar todos los datos
					
					$.ajax({
						url:'include/procesar_ajax.php',
						type: 'POST',
						data: data,			
				        contentType:false,
				        processData:false,
						success: function(resp){
							$.ajax({
								type: 'POST',
								url: 'login/transacciones.php',
								data: {
									'transaccion' : 'gestionar_datos_usuario'	
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
											location.href='principal3.php';		
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
			/************************************************************************************************************/
			/* Fin Function Guardar_formulario_gestionardatos()
			/************************************************************************************************************/
			var timerid="";

			$(function onkeyup(){
				$("#cargo_usuario").on("input",function(e){ // Accion que se activa cuando se digita #numero_guia_radicado
					espacios_formulario('cargo_usuario','capitales',0);
					$(".errores").slideUp("slow");

				    var cargo_usuario = $(this).val();
				    
				    if($(this).data("lastval")!= cargo_usuario){
				    	$(this).data("lastval",cargo_usuario);             
						clearTimeout(timerid);
						timerid = setTimeout(function() {
				 			validar_input('cargo_usuario');	 			
						},1000);
				    };
				});

				$("#gestionardatos_mail").on("input",function(e){ // Accion que se activa cuando se digita #numero_guia_radicado
					espacios_formulario('gestionardatos_mail','minusculas',0);
					$(".errores").slideUp("slow");

				    var mail_usuario = $(this).val();
				    
				    if($(this).data("lastval")!= mail_usuario){
				    	$(this).data("lastval",mail_usuario);             
						clearTimeout(timerid);
						timerid = setTimeout(function() {
							validar_input('gestionardatos_mail')
						},1000);
				    };
				});
			});	
			<?php
				if($mail != ''){
			     	echo "document.getElementById(\"gestionardatos_mail\").value = \"$mail\";";
				}
				if($cargo_usuario != ''){
			     	echo "document.getElementById(\"cargo_usuario\").value = \"$cargo_usuario\";";
				}
			?>
		</script>
		<div class="center contenido" id="logo">
			<h1>Gestionar Datos del Usuario
				<?php 
					echo "<h2>$usuario</h2>";
		 		?>
		 	</h1>
		 	<br><br>
			<center>
				<table>
					<tr>
						<td class="descripcion" width="15%" rowspan="3">
							Foto del Usuario :
						</td>
						<td class="detalle" width="25%" rowspan="3">
							<input type="file" name="gestionardatos_imagen" id="gestionardatos_imagen" onchange="valida_tipo_imagen('gestionardatos_imagen','viewer2','imagen','error_imagen_invalida2','imagen')">
							<div id="error_imagen_invalida2" class="errores"> El formato de la imagen que va a ingresar no es válido. El sistema solo admite formato PNG, GIF, JPG y JPEG</div>
							<iframe id="viewer2" frameborder="0" scrolling="yes" width="100%" height="150px" style="display: none;"></iframe>
								<?php
								if($path_foto!=""){
									echo "<script>
										$('#viewer2').slideDown('slow');
					                    $('#viewer2').attr('src', '$path_foto');
									</script>";
								}
								?>
						</td>
						<td class="descripcion">
							Cargo del Usuario
						</td>
						<td class="detalle">
							<input type="text" name="cargo_usuario" id="cargo_usuario" placeholder="Ingrese su cargo">
							<div id="cargo_usuario_max" class="errores">El cargo del usuario no puede ser mayor a 100 caracteres. (Actualmente <b><u id='cargo_usuario_contadormax'></u></b> caracteres)</div>
							<div id="cargo_usuario_min" class="errores">El cargo del usuario no puede ser menor a 6 caracteres.</div>
						</td>
					</tr>
					<tr>	
						<td class="descripcion">
							Mail Usuario :
						</td>
						<td class="detalle">
							<input type="email" name="gestionardatos_mail" id="gestionardatos_mail" placeholder="Ingrese Mail del Usuario" onchange="validar_input('gestionardatos_mail')">
							<div id="gestionardatos_mail_max" class="errores">El mail del usuario no puede ser mayor a 50 caracteres. (Actualmente <b><u id='gestionardatos_mail_contadormax'></u></b> caracteres)</div>
							<div id="gestionardatos_mail_min" class="errores">El mail del usuario no puede ser menor a 6 caracteres.</div>
							<div id="gestionardatos_mail_null" class="errores">El mail del usuario es obligatorio.</div>
							<div id="gestionardatos_mail_formato_mail" class="errores">El mail ingresado no tiene formato correcto (usuario@algunmail.com).</div>
						</td>
					</tr>
					<tr>										
						<td class="descripcion">
							Segunda Clave : 
						</td>
						<td class="detalle">
							<input type="password" id="gestionardatos_segunda_clave" name="gestionardatos_segunda_clave" placeholder="Cambiar Segunda Clave" onchange="validar_pass()">
							<div id="valida_segunda_clave_vacio" class="errores">El password no puede ser menor a 6 caracteres</div>
						</td>
					</tr>	
					<tr>
						<td colspan="4">
							<center>
								<input type="button" value="Guardar Cambios" class="botones" onclick="Guardar_formulario_gestionardatos()">
							</center>	
						</td>
					</tr>					
				</table>
			</center>
		</div>
	</body>
</html>