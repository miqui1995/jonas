<html>
<head>
	<meta charset="UTF-8">
	<title>Formulario para Masiva</title>
    <script src="include/js/sweetalert2.js"></script>
    <link rel="stylesheet" type="text/css" href="include/css/sweetalert2.css">  
	<script type="text/javascript" src="include/js/funciones_inventario.js"></script>
	<link rel="stylesheet" href="include/css/estilos_inventario.css">
</head>
<body>
	<?php
		require_once "../login/validar_inactividad.php"; // Validar sesion 
	?>
	<center>			
		<h1>Módulo Radicación Masiva</h1><br>
		<div id="contenedor">
			<h3>No puede cargar archivo CSV con un tamaño superior a 8M, ni con más de 10.000 filas, ni se admite el caracter punto y coma (;)</h3>
			<form enctype="multipart/form-data" method="post" id='formuploadajax' name="formuploadajax">
				<table border="0">
					<tr>
						<td colspan="2">
							<input id="archivo" accept=".csv" name="archivo" type="file" /> 
						</td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td colspan="2"><br><br>
							<h4>Puede descargar el FUID desde <a href="#" onclick="descargar_csv()">aqui</a>. Recuerde guardarlo como "CSV (delimitado por comas)(*.csv)"</h4>
							<br><br><br>
						</td>
					</tr>
				</table>	
			</form> 
		</div>	
		<div id="resultados_masiva"></div>	
	</center>	
</body>
</html>