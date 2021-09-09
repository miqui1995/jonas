<?php 
	if(!isset($_SESSION)){
		session_start();
	}

	//require_once("../login/validar_inactividad.php");
	//	var_dump($_SESSION);
	$perfil=$_SESSION['perfil'];
	$usuario=$_SESSION['nombre'];
	$id_usuario=$_SESSION['id_usuario'];
 ?>
<!DOCTYPE html>
<html > 
<head>
	<meta charset="UTF-8">
	<title>Buscador de Usuarios</title>
	<script type="text/javascript" src="include/js/funciones_usuarios.js"></script>
	<link rel="stylesheet" href="include/css/estilos_usuarios.css">
</head>
<body>
	<script>
		$('#pass1').focus();
	</script>
	
<!-- Desde aqui el div que tiene el formulario principal -->
	<div class="center contenido" id="logo">
		<!-- <img src="imagenes/logo3.png" width="100" alt="Jonas SGD" title="Logo Jonas">	-->
		<h1>Cambiar Contraseña 
			<?php 
				echo "<h2>$usuario</h2>
				<input type='hidden' id='id_usuario' value='$id_usuario'>
				";
	 		?>
	 	</h1>
	 	<br><br>
		<form action="javascript:cambio_pass()" method="post" id="envia_pass" class="form">
			<center>
			<table width='50%'>
				<tr>
					<td>
	 					<center>
	 						<input type="password" id="pass1" name="pass1" placeholder="Ingrese su nuevo password" onkeyup="if (event.keyCode==13){modificar_pass_usuario(); return false;}">	
							<div id="valida_pass_vacio" class="errores">El password no puede ser menor a 6 caracteres</div>
	 					</center>
					</td>
				</tr>
				<tr>
					<td>
						<center>
							<input type="password" id="pass2" name="pass2" placeholder="Confirme su nuevo password"  onkeyup="if (event.keyCode==13){modificar_pass_usuario(); return false;}">	
							<div id="valida_pass_confirmacion" class="errores">El password de confirmacion no corresponde al ingresado</div>
						</center>			
					</td>
				</tr>
				<tr>
					<td>
						<center>
							<input type="button" value="Cambiar Password" class="botones" onclick="modificar_pass_usuario()">
						</center>	
					</td>
				</tr>
			</table>
			</center>
		</form>
	</div>
<!-- Hasta aqui el div que tiene el formulario principal -->
</body>
</html>