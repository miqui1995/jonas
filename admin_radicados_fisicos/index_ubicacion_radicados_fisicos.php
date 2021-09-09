<?php
//Condicion si la sesion no existe
if(!isset($_SESSION)){
	//Se inicia sesion
	session_start();
}

require_once "../login/validar_inactividad.php";
require_once "../login/conexion2.php"; 				// Para la consulta

/*Aqui defino la fecha para mostrar en formato "Jueves 05 de Mayo de 2020" */
require_once "../include/genera_fecha.php";
?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Buscador de ubicacion fisica radicados</title>
	<script type="text/javascript" src="include/js/funciones_ubicacion_fisica.js"></script>
	<!--Se cargan los mismos estilos de expedientes, ya que se uso como ejemplo para este desarrollo-->
	<link rel="stylesheet" href="include/css/estilos_expedientes.css">
</head>
<body id="body_ubicacion_fisica_radicados">
	<?php
		$usuario 	= $_SESSION['login'];   // Desde la sesion define la variable $usuario
		$timestamp  = date('Y-m-d H:i:s');	// Genera la fecha de transaccion	

		/*Query para traer radicados que tiene un usuario y la ordena por fecha de forma descendiente*/
		$query_ubicacion =" select r.numero_radicado,uf.usuario_actual, uf.usuario_anterior,uf.fecha, r.asunto from ubicacion_fisica uf, radicado r where uf.numero_radicado = r.numero_radicado and uf.usuario_actual = '".$usuario."' order by 4 desc";
		 /* Aqui se ejecuta la query */
		$fila_query_ubicacion 		= pg_query($conectado,$query_ubicacion);

		/* Se trae las filas de la query */
		$registros_query_ubicacion 	= pg_num_rows($fila_query_ubicacion);
	?>
	<center>
		<table border='0' width='100%'>
			<tr>
				<td style="width:100%">
					<div class="center" id="logo">
						<br>
						<h1 style="margin-top:-10px;">Buscador Documentos en Fisico</h1>
					</div>
					<!--Inicio de contenedor para input radicados concatenados-->
					<div id='div_radicados_concatenados' class='text' style='display: none;'>
						<div class="form center">
							<!---Inicio de contenedor pára la aprobacion del documento-->
							<input type="text" id="login_aprueba" name="login_aprueba" value="<?php echo($_SESSION['login']); ?>">

							<input type='search' name='lista_radicados_fisicos' id='lista_radicados_fisicos' title='Lista radicados'  >
						</div>
						<br><br><br>
					</div>
					<!--Fin de contenedor para input radicados concatenados -->
					<br>
					<!-- Inicio de contenedor input buscador radicado -->
					<div class="form center">
						<input type="search" id="search_ubicacion_fisica" style="width: 80%;" placeholder="Ingrese numero de radicado que desea buscar" title="Busqueda por numero de radicado">
					</div>
					<div id="error_buscador_ubicacion_fisica" class="errores">La consulta no puede ser menor a 3 caracteres.</div>
					<!-- Fin de contenedor input buscador radicado-->
					<!-- Inicio de contenedor que se usa para cargar los resultados de busqueda. Este contenido de este div cambia dinamicamente cada vez que hace un keyup en el campo search_ubicacion fisica -->
					<div id="desplegable_resultados_ubicacion_fisica" style="padding: 10px;"></div>
					<!-- Fin de contenedor que se usa para cargar los resultados de busqueda. -->
				</td>
			</tr>
		</table>
			<!--Esta tabla es cuando se cargue la primera vez el modulo, pero va a cambiar cuando le de click en algun resultado de busqueda de radicado-->
			<div id="tabla_resultados_dinamica">
			<br>
		<?php
			if($registros_query_ubicacion==0){	
			/*Condicion si la consulta no encuentra resultados*/
				echo "<font color='red'> Su usuario no tiene documentos en físico registrados actualmente.</font>";
			}else{
			/* Si existen registros asociados a el usuario */
		?>
			<h1 style="margin-top:-10px;">Radicados en Físico que tiene actualmente en su poder</h1>
			
			<table border='0' width='100%'>
				<tr>
					<td class='descripcion center' style="width: 10px;">Id</td>
					<td class='descripcion center' style="width: 160px;">Radicado</td>
					<td class='descripcion center' >Asunto</td>
					<td class='descripcion center' style="width: 150px;">Usuario Actual</td>
					<td class='descripcion center' style="width: 150px;">Recibido Desde Usuario</td>
					<td class='descripcion center' style="width: 200px;">Fecha Recibido</td>
				</tr>
				<?php
					// Se imprime las filas con bucle "for" que viene desde la variable $registros_query_ubicacion
					// La variable contador se usa para la columna id como autoincrementable
					$contador = 1;
					//Se recorre el resultado de la query con un for
		    		for ($i=0; $i < $registros_query_ubicacion ; $i++){
				    	$linea_consulta_ubicacion 	= pg_fetch_array($fila_query_ubicacion); 	
						$numero_radicado 	 		= $linea_consulta_ubicacion['numero_radicado'];
						$asunto  	 				= $linea_consulta_ubicacion['asunto'];
						$usuario_anterior	 		= $linea_consulta_ubicacion['usuario_anterior'];
						$usuario_actual		 		= $linea_consulta_ubicacion['usuario_actual'];
						$fecha				 		= $linea_consulta_ubicacion['fecha'];

					/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2020"*/
						$fecha_modificacion_depe=$b->traducefecha($fecha);	
						
						if($asunto==""){
							$asunto1 = "<b>Sin asunto todavía</b>";
						}else{
							$asunto1 = $asunto;
						}

						echo "
						<tr class='detalle center fila_serie' onclick=\"\">
							<td> $contador</td>
							<td> $numero_radicado</td>
							<td style='padding:10px; text-align:left;'> $asunto1 </td>
							<td> $usuario_actual</td>
							<td> $usuario_anterior</td>
							<td style='padding:10px;'> $fecha_modificacion_depe</td>
						</tr>	";
						$contador++;
					}//Fin del bucle "for"
				echo("</table></div>");
				// Fin de tabla
			}/* Fin condicion si existen registros asociados a el usuario */ 				
		?>
	</center>
</body>
</html>