<?php 
	session_start();
/*
* @brief Este archivo es el index principal para ingresar al aplicativo. Aqui se solicita usuario y contraseña y tiene envía el formulario de manera asíncrona para verificar usuario y contraseña para conceder acceso o devolver error y negarlo. Las funciones asíncronas se encuentran el el archivo "include/js/funciones_login.js"
* @author Johnnatan Rodriguez Pinto
* @date Diciembre 2019
*/
?>
<html>
<head>
	<meta charset="UTF-8">
	<div id="title_img">
		<title>Ingreso al Software de Gestion Documental JONAS</title>
		<link href="imagenes/logo3.png" type="image/x-icon" rel="shortcut icon"/>
	</div>	
	<meta charset="utf-8" name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<!--Esta es la parte que se visualiza en la pestaña del navegador-->
		<title>Ingreso al SGD JONAS</title>
<!--Esta es el encabezado del archivo donde llamo a los archivos que se necesitan para usar jquery, css-->
	<link rel="stylesheet" href="include/css/estilos_login.css">  
	<script type="text/javascript" src="include/js/jquery.js"></script>
	<script type="text/javascript" src="include/js/funciones_login.js"></script>
</head>
<body>	

<?php 
	/* Se inicia y/o destruye sesión al llegar a éste archivo para iniciar en limpio cuando ingrese al aplicativo. */
	session_unset();
	session_destroy();

	/* Aqui se define la fecha especial (navidad, halloween, etc.). Para desplegar interfaz con estilos de imágenes de acuerdo a la fecha especial. Si se deja vacío ("") la interfaz es por defecto normal. 
	luego se guarda en una variable de sesión. */
	$fecha_especial  				= ''; 		
	$_SESSION['fechas_especiales'] 	= $fecha_especial; 	

	// var_dump($_SESSION);
 ?>	
	<div id="general">
		<div class="form">
			<span id="encabezado_transparente2">
				<img src="imagenes/encabezado_transparente.png" id="logo_principal"> 	 
			</span>
			<h1 style="margin-top:-1px;">SIGDEA</h1>
			<h2 style="margin-top:-3%;"> Sistema Integrado de Gestión Documental Electrónica y Archivo <span id="nombre_entidad"></span> 	<!-- En algunas entidades quieren el nombre de la entidad --></h2>	
			<div id="cont_logo_empresa" style="margin-top: -15px;">
				<img src="imagenes/iconos/logo_largo.png" class="center" width="250px" style="background-color: #FFFFFF; border-radius: 20px; margin-top: -15px;"> 	
			</div>		
			<hr>
			<form id ="formulario_login" name ="formulario_login" autocomplete="off">
				<table>
					<tr>
						<td width="100%">
							<center>
								<div>
									<span id="icono_usuario">
										<img src="imagenes/iconos/icono_user.png" class="icono"> 
									</span>
									<input type="text" name="user" id="user" class="textbox"
									 placeholder="Ingrese Usuario" onblur="upper_user()" maxlength='18' onkeyup="if (event.keyCode==13){entra(); return false;}">
								 </div>
								 <div>
									<img src="imagenes/iconos/icono_lock.png" class="icono"> 
									<input type="password" name="pass" id="pass" class="textbox" onkeyup="if (event.keyCode==13){entra(); return false;}"
									 placeholder="Ingrese Contraseña" >
									 <input type="hidden" name="festivo" id="festivo" value=<?php echo $fecha_especial; ?> >
									 <input type="hidden" name="cod_ent" id="cod_ent" value="GC1">
									 <input type="hidden" name="ver" id="ver" value="Version (Ballena Azul - 07)">
								 </div>
								<div>
									<input type="button" value="Ingresar" id="boton_ingreso" class="boton" onclick="entra()" ></td>
								</div> 
							</center>	
							<div class="errores" id="error_user">El usuario y/o contraseña no son correctos. Por favor revisar.</div>
							<div class="errores" id="error_inactivo">El usuario se encuentra inactivo. Por favor comuniquese con el administrador del sistema.</div>
						</td>
					</tr>
				</table>
			</form>	
			<hr>

			<div class="footer center">
				<div id="avisos_principal"></div>
				<b id="version"></b>
			</div>
		</div>		
	</div>
</body>
<?php 
/* Se incluye el archivo que dependiendo la fecha especial, genera los estilos y reemplaza divs. */
	require_once('include/fechas_especiales.php');
?>
</html>