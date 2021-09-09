<?php 
	require_once("../login/validar_inactividad.php");
?>
<html>
<head>
	<meta charset="UTF-8">
	<title>Módulo de préstamos</title>
	<script type="text/javascript" src="include/js/funciones_prestamos.js"></script>
	<link rel="stylesheet" href="include/css/estilos_bandejas.css">
</head>
<body>
	<h1 class="center">Módulo de Préstamos y Devoluciones</h1>
	<?php 
		$permiso_prestamo_documentos 			= $_SESSION['prestamo_documentos'];
		$por_devolucion_pendiente_usuario 		= $_SESSION['por_devolucion_pendiente_usuario'];
		$por_prestamo_realizada_usuario 		= $_SESSION['por_prestamo_realizada_usuario'];
		
		echo "<hr><h2 class='center descripcion'>Sección de préstamos y Devoluciones a usted como usuario.</h2><hr>";
		echo "<button id='boton_documentos_solicitados_usuario' onclick='cargar_accordion()' class='accordion'>
			Solicitudes de préstamo en físico hechas POR USTED, que no le han entregado. ( <font color=red>$por_prestamo_realizada_usuario </font>)
		</button>
			<div id='informacion_solicitud_prestamos_usuario' class='panel'></div>
		<button id='boton_documentos_prestados_usuario' onclick='cargar_accordion()' class='accordion accordion2'>
			Documentos prestados en físico A USTED, que no ha devuelto. ( <font color=red>$por_devolucion_pendiente_usuario </font>)
		</button>
			<div id='informacion_prestados_usuario' class='panel'></div><hr>";

		if($permiso_prestamo_documentos=="SI"){
			$por_devolucion_pendiente_general 		= $_SESSION['por_devolucion_pendiente_general'];
			$solicitud_prestamo_pendientes_general 	= $_SESSION['solicitud_prestamo_pendientes_general'];
			
			echo "<hr><h2 class='center descripcion'>Sección de préstamos del área de Gestión Documental</h2><hr><button id='boton_solicitud_prestamos_general' onclick='cargar_accordion();' class='accordion'>
				Solicitudes de préstamo de documentos hechas POR USUARIOS, pendientes para entregarles en físico. ( <font color=red>$solicitud_prestamo_pendientes_general </font> )  
			</button>
			<div id='informacion_solicitud_prestamos_general' class='panel'></div>

			<button id='boton_documentos_prestados_general' onclick='cargar_accordion()' class='accordion accordion2'>
			Documentos prestados en físico A USUARIOS, pendientes por recuperar en físico. ( <font color=red>$por_devolucion_pendiente_general </font>)</button>
			<div id='informacion_prestados_general' class='panel'></div><hr>

			<h2 class='center descripcion'>Sección de generación de reportes de préstamos.</h2><hr>
			<button id='boton_reporte_prestamos1' onclick='cargar_accordion()' class='accordion accordion2'>
			Reporte No.1 Solicitado. ( <font color=red># </font>)</button>
			<div id='reporte_prestamos1' class='panel'></div><hr>
			";
		}	
	 ?>
</body>
</html>
