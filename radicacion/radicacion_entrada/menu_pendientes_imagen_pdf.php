<?php 
	require_once("../../login/validar_inactividad.php");
?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Menu Pendientes Imagen PDF</title>
	<script type="text/javascript" src="include/js/funciones_radicacion_entrada.js"></script>
	<link rel="stylesheet" href="include/css/estilos_radicacion_entrada.css">
</head>
<body>
	<script type="text/javascript">$("#search_radicado_falta_pdf").focus(); buscar_radicado_falta_pdf()</script>
	<div class="center" id="logo">
		<h1>Radicados sin PDF</h1>
	</div>
	<div class="form center">
		<input type="search" id="search_radicado_falta_pdf" placeholder="Ingrese Numero de Radicado">
	</div>
		<div id="desplegable_resultados"></div>
</body>
</html>