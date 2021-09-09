<?php 
	require_once("../../login/validar_inactividad.php");
?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Menu Pendientes Radicación</title>
	<script type="text/javascript" src="include/js/funciones_radicacion_entrada.js"></script>
	<link rel="stylesheet" href="include/css/estilos_radicacion_entrada.css">
</head>
<body>
	<script type="text/javascript">$("#search_radicado_modificacion_rapida").focus(); buscar_radicado_modificacion_rapida()</script>
	<div class="center" id="logo">
		<h1>Radicados por Ingresar Datos</h1>
	</div>
	<div class="form center">
		<input type="search" id="search_radicado_modificacion_rapida" placeholder="Ingrese Numero de Radicado">
	</div>
		<div id="desplegable_resultados"></div>
</body>
</html>