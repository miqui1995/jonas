<?php 
	require_once("../../login/validar_inactividad.php");
?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Menu Modificacion</title>
	<script type="text/javascript" src="include/js/funciones_radicacion_entrada.js"></script>
	<link rel="stylesheet" href="include/css/estilos_radicacion_entrada.css">
</head>
<body>
	<script type="text/javascript">$("#search_radicado").focus();</script>
	<div>
		<div class="center" id="logo">
			<h1>Radicado para Modificar</h1>
		</div>
		<div class="form center">
			<input type="search" id="search_radicado" placeholder="Ingrese Numero de Radicado">
			<div id="error_search_vacio" class="errores">La consulta debe tener por lo menos un parámetro de búsqueda</div>
			<div id="search_radicado_min" class="errores">La consulta no puede ser menor a 6 caracteres (numeros o letras)</div>
			<div id="search_radicado_max" class="errores">La consulta no puede ser mayor a 50 caracteres (numeros o letras)</div>		

		</div>
			<div id="desplegable_resultados" style="height:90%; width:100%;">

			</div>
			<div id="error_dependencias" class="errores">La dependencia que ha digitado no se encuentra en la base de datos.
				Este campo es obligatorio. En caso que no encuentre una dependencia correcta, comuníquese
				por favor con el administrador del sistema.</div>

	</div>		
</body>
</html>