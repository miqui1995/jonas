<?php 
/* Este archivo solo recibe un parametro por GET llamado "codigo_ubicacion" y despliega una tabla con ese dato unicamente.*/
 ?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Incluye Cajas en Ubicación Topográfica</title>
	<script type="text/javascript" src="include/js/funciones_ubicacion_topografica.js"></script>
	<link rel="stylesheet" href="include/css/estilos_ubicacion_topografica.css">
</head>
<body>
	<div>
<?php
	require_once("../login/validar_inactividad.php");
	require_once('../login/conexion2.php');

/*Aqui defino la fecha para mostrar en formato "Jueves 05 de Mayo de 2016" */
//	include "../include/genera_fecha.php";
/*Fecha que se realiza la transaccion (hoy)*/	
//	$fecha_modificacion = $date; // $date es del formato "2016-05-05"

	$codigo_ubicacion=$_GET['codigo_ubicacion']; // Codigo de la caja recibido.

/******************************************************************************************/
/* Administrador Cajas ********************************************************************/
/******************************************************************************************/
/* Nombre del nivel */	
	$query_nombre_nivel="select * from ubicacion_topografica where id_ubicacion ='$codigo_ubicacion'";
	$fila_nombre_nivel = pg_query($conectado,$query_nombre_nivel);
	$linea_nombre_nivel = pg_fetch_array($fila_nombre_nivel);
	$nombre_nivel=$linea_nombre_nivel['nombre_nivel'];
/* Fin Nombre del nivel */	
	
	$consulta_caja = "select * from expedientes e join ubicacion_topografica u on e.codigo_ubicacion_topografica = u.id_ubicacion where u.id_ubicacion= '$codigo_ubicacion'";
//	$consulta_caja = "select * from expedientes limit 7";
	$fila_caja = pg_query($conectado,$consulta_caja);

	/*Calcula el numero de registros que genera la consulta anterior.*/
	$registros_caja= pg_num_rows($fila_caja);
	$botones_expediente="";

	if($registros_caja>0){
		
	/*Recorre el array generado e imprime uno a uno los resultados.	*/
		$num_fila=0; // Variable declarada para poner color a la fila según ésta variable.

		while ($row = pg_fetch_array($fila_caja)){
			$id_expediente=$row['id_expediente'];
			$nombre_expediente=$row['nombre_expediente'];
			if($num_fila%2==0){	 //si el resto de la división es 0 pongo un color




				$tr="<tr class='fila2'>";
			}else{				 //si el resto de la división NO es 0 pongo otro color
				$tr="<tr class='fila1'>";
			}
	/* Verifica si el expediente es de inventario para incluir metadato */ 

			$descriptor="";
			$verifica_inventario=substr($id_expediente,7,3);
			if($verifica_inventario=='INV'){
				$consulta_metadato="select descriptor from inventario where expediente_jonas='$id_expediente'";
				$fila_metadato = pg_query($conectado,$consulta_metadato);
				$linea_metadato = pg_fetch_array($fila_metadato);
				
				$descriptor = $linea_metadato['descriptor'];
			}

			$botones_expediente=$botones_expediente."$tr<td style=\"font-size:12px;\">$id_expediente - $nombre_expediente $descriptor</td><td><input type='button' class='botones' value='Ver' title='Ver documentos en el expediente $id_expediente - $nombre_expediente' onclick=\"carga_documentos_exp('$id_expediente')\" style=\"width:auto;\"></td><td style=\"font-size:12px;\"><input type='button' class='botones' value='Sacar' title='Sacar documento del expediente $id_expediente - $nombre_expediente' onclick=\"sacar_exp_caja('$id_expediente','$codigo_ubicacion')\" style=\"width:auto;\" style=\"font-size:12; padding:5px; width:auto;\"></td></tr>";
			$num_fila++; 
		}
	}else{
		$botones_expediente="No hay expedientes asociados.";	
	}	

echo "<script>$('#search_expedientes').focus();</script>
		<input type='hidden' id='nombre_nivel' value='$nombre_nivel'>
		<input type='hidden' id='id_nivel' value='$codigo_ubicacion'>
		<div id='caja_busqueda' class='medio_div'>
			<h2 class='center' style='margin-top:-10px;'>Buscador de Expedientes</h2>
				<center>
				<input type='search' id='search_expedientes' placeholder='Buscar expediente' onkeyup='espacios_formulario(\"search_expedientes\",\"mayusculas\") '>
				</center>
				<div id='desplegable_resultados'></div>
		</div>
		<div id='caja_actual' class='medio_div'>
			<h2 class='center' style='margin-top:-10px;'>Expedientes en Caja $nombre_nivel</h2>
			<table border='0' width='100%'>
				$botones_expediente	
			</table>
		</div><br>
		<center>
		<input type='button' class ='botones' width= '420px' onclick='carga_ubicacion_topografica()' value='Volver'>
		</center>
";

?>
		<div id="ventana4">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrarVentanaMostrarDocumentos();'>Cerrar X</a></div>
				<hr>
				<form id ="formulario_agregar_radicados" name ="formulario_agregar_radicados" autocomplete="off">
						<div id='expediente_busqueda' class='medio_div'>
							<h1 class='center' style='margin-top:-10px;'>Buscador de Radicados</h1>
								<input type="hidden" id="numero_expediente_rad" name="numero_expediente_rad">
								<input type='search' id='search_radicados1' placeholder='Buscar radicado' 
								onkeyup="espacios_formulario_nivel('search_radicados1')">
								<div id='desplegable_resultados_rad'></div>
						</div>
						<div id='expediente_actual' class='medio_div'>
							<h1 id="titulo_expediente"></h1>
							<table id="documentos_en_expediente" border="0"></table>
						</div><br>
					<center>
						<input type='button' style="width: 25%;" class ='botones' onclick='carga_ubicacion_topografica()' value='Volver'>
					<center>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para agregar nivel-->
	</div>
</body>