<?php 
if(!isset($_SESSION)){
	session_start();
}
?>
<html>
<head>
	<meta charset="UTF-8">
	<title>Buscador para Inventario</title>
    <script src="include/js/sweetalert2.js"></script>
    <link rel="stylesheet" type="text/css" href="include/css/sweetalert2.css">  
	<script type="text/javascript" src="include/js/funciones_inventario.js"></script>
	<link rel="stylesheet" href="include/css/estilos_inventario.css">
</head>
<body>
	<center>			
		<script type="text/javascript">$("#nombre_documento").focus();</script>
		<h1>Módulo Buscador Inventario</h1>
		<div id="contenedor">
		<br>
			<table border="0">
				<tr>
					<td class="busq_av">Codigo Dependencia
						<input type="search" name="codigo_dependencia" id="codigo_dependencia">
					</td>
					<td class="busq_av">Codigo Serie
						<input type="search" name="codigo_serie" id="codigo_serie">
					</td>
					<td class="busq_av">Codigo Subserie
						<input type="search" name="codigo_subserie" id="codigo_subserie">
					</td>
					<td class="busq_av">Fecha Inicial
						<input type="date" name="fecha_inicial" id="fecha_inicial">
					</td>
					<td class="busq_av">Fecha Final
						<input type="date" name="fecha_final" id="fecha_final">
					</td>
				</tr>
				<tr>
					<td class="busq_av">Numero consecutivo Jonas
						<input type="search" name="consecutivo_jonas" id="consecutivo_jonas">
					</td>
					<td class="busq_av">Caja, Paquete, Tomo, Otro
						<input type="search" name="caja_paquete_tomo_otro" id="caja_paquete_tomo_otro">
					</td>
					<td class="busq_av">Numero caja - paquete
						<input type="search" name="numero_caja_paquete" id="numero_caja_paquete">
					</td>
					<td class="busq_av">Numero carpeta
						<input type="search" name="numero_carpeta" id="numero_carpeta">
					</td>
					<td class="busq_av">Numero caja archivo central
						<input type="search" name="numero_caja_archivo_central" id="numero_archivo_central">
					</td>
				</tr>
				<tr>
					<td class="busq_av">Numero Consecutivo
						<input type="search" name="numero_consecutivo" id="numero_consecutivo">
					</td>
					<td colspan="2">Nombre del Documento o Asunto
						<input type="search" name="nombre_documento" id="nombre_documento" onkeyup="if (event.keyCode==13){buscar_inventario(); return false;}">
					</td>
					<td colspan="2">Metadato
						<input type="search" name="metadato" id="metadato" onkeyup="if (event.keyCode==13){buscar_inventario(); return false;}">
					</td>
				</tr>
				<div id="error_campos_vacio" class="error" style="display: none;">La consulta debe tener por lo menos un parámetro de búsqueda</div>
				<tr>
					<td class="busq_av" colspan="6">
						<center>
							<a href="#" onclick="display_busq_basica()">Búsqueda Básica</a>
						</center>
					</td>
					<td colspan="6" id="busq_basica">
						<center>
							<a href="#" onclick="display_busq_av()">Búsqueda Avanzada</a>
						</center>
					</td>
				</tr>
				<tr>
					<td colspan="6">
						<center>
							<input type="button" name="buscar" value="Buscar" class='botones2' onclick="buscar_inventario()">
						</center>
					</td>
				</tr>
			</table>	
		</div>	
		<div id="resultados_masiva"></div>
	</center>	
</body>
</html>