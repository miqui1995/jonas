<?php 
/* En este archivo se consultan los casos en los cuales hay pendientes en la parametrización o configuración del sistema  */
	require_once("../login/validar_inactividad.php");
	require_once('../login/conexion2.php'); // Para la consulta de las dependencias
	
	$paso1 = "";
	$paso2 = "";
	$paso3 = "";
	$paso4 = "";
   	
	/* PASO1 Se consulta si existen los cambios_organico_funcionales. La variable $registros_consulta_cambios_organico_funcionales viene del archivo validar_inactividad.php */
	
	if($registros_consulta_cambios_organico_funcionales == 0){
	 	/* PASO1 Se consulta si existen los cambios_organico_funcionales */
		$paso1 = "<h2 style='color: blue;'>Paso1</h2><h3><img src='imagenes/iconos/checkbox3.png' style='float: left; height:25px; margin-right: 10px;'>No se ha cargado todavía ninguna versión de la estructura organico-funcional de $entidad</h3><h2 style='color:red; text-align: center;'>Incompleto</h2>";
	}else{
		$paso1 = "<h2 style='color: blue;'>Paso1</h2><h3><img src='imagenes/iconos/checkbox2.png' style='float: left; height:25px; margin-right: 10px;'>Cargar versiones estructura organico-funcional.</h3><h2 style='color:green; text-align: center;'>Completado</h2>";

	 	/* PASO2 Se consulta si existe la ultima versión de cambios_organico_funcionales */
		if($count_version_actual_cambios_of==0){
			$paso2 ="<h2 style='color: blue;'>Paso2</h2><h3><img src='imagenes/iconos/checkbox3.png' style='float: left; height:25px; margin-right: 10px;'>No existe versión actual de la estructura organico-funcional.</h3><h2 style='color:red; text-align: center;'>Incompleto</h2>";
		}else{
			$paso2 ="<h2 style='color: blue;'>Paso2</h2><h3><img src='imagenes/iconos/checkbox2.png' style='float: left; height:25px; margin-right: 10px;'>Existe versión actual estructura organico-funcional.</h3><h2 style='color:green; text-align: center;'>Completado</h2>";
		}
 		/* PASO3 Se consulta si existe espacio entre las fechas de la versión de cambios_organico_funcionales utilizando los registros obtenidos en el PASO1 */
 		if($contenido_paso3!=""){
 			$paso3 ="<h2 style='color: blue;'>Paso3</h2><h3>$contenido_paso3</h3><h2 style='color:red; text-align: center;'>Incompleto</h2>";
 		}else{
 			$paso3 ="<h2 style='color: blue;'>Paso3</h2><h3><img src='imagenes/iconos/checkbox2.png' style='float: left; height:25px; margin-right: 10px;'>Verificado espacios de tiempo entre las fechas de las versiones de cambios organico-funcionales</h3><h2 style='color:green; text-align: center;'>Completado</h2>";
 		}

 		/* PASO4 Se consulta si la versión de cambios_organico_funcionales tiene cargado su acto administrativo correspondiente utilizando los registros obtenidos en el PASO1 */
		if($contenido_paso4!=""){
			$paso4 ="<h2 style='color: blue;'>Paso4</h2><h3>$contenido_paso4</h3><h2 style='color:red; text-align: center;'>Incompleto</h2>";
		}else{
 			$paso4 ="<h2 style='color: blue;'>Paso4</h2><h3><img src='imagenes/iconos/checkbox2.png' style='float: left; height:18px; margin-right: 10px;'>Verificado actos administrativos cargados en cada una de las versiones de cambios_organico_funcionales</h3><h2 style='color:green; text-align: center;'>Completado</h2>";
		}
	}


	$muestra_paso2 = "";
	$muestra_paso3 = "";
	$muestra_paso4 = "";

	if($paso2!=""){
		$muestra_paso2 = "<td width='25%' onclick=\"carga_administrador_normatividad('interna')\" title='Se verifica si el Paso 2 (Existe versión actual de la estructura organico-funcional)' class='hover_pointer detalle'>
				$paso2
			</td>";
	}
	if($paso3!=""){
		$muestra_paso3 = "<td width='25%' onclick=\"carga_administrador_normatividad('interna')\" title='Se verifica si el Paso 3 (Verifica si existe espacio entre las fechas de la versión de cambios_organico_funcionales)' class='hover_pointer detalle'>
				$paso3
			</td>";
	}
	if($paso4!=""){
		$muestra_paso4 = "<td width='25%' onclick=\"carga_administrador_normatividad('interna')\" title='Se verifica si el Paso 4 (Verifica si existen actos administrativos cargados en cada una de las versiones de cambios_organico_funcionales)' class='hover_pointer detalle'>
				$paso4
			</td>";
	}
?>	
	<h1>Pasos Completados en parametrización Jonas</h1>
	<table border="1">
		<tr>
			<td width="25%" onclick="carga_administrador_normatividad('interna')" title="Se verifica si el Paso 1 (Estructura organico-funcional) se ha cargado correctamente" class="hover_pointer detalle">
				<?php echo $paso1 ?>
			</td>
			<?php echo $muestra_paso2.$muestra_paso3.$muestra_paso4 ?>	
		</tr>
	</table>
	<br>
   	<?php 
   		/* Se calcula porcentaje con el contador de $pasos_completados_configuracion y $cantidad_pasos_por_cumplir (que viene del archivo validar_inactividad)*/
   		$resultado = round(($pasos_completados_configuracion*100)/$cantidad_pasos_por_cumplir)."%";

   		/* Aqui imprime la barra de progreso */
   		echo " <div style='background-color: rgb(192, 192, 192); border-radius: 15px; margin-left: 5%; width: 85%;'><div style='background-color: rgb(116, 194, 92); border-radius: 15px; color: white; font-size: 20px; padding: 10px; text-align: right; width: $resultado;'>$resultado</div></div><div style='margin-left:40%;'>Se han completado $pasos_completados_configuracion de $cantidad_pasos_por_cumplir pasos.</div>";
   	?>
    
	<!-- <h2>Ingresar los cambios en la estructura Organico - Funcional de <?php echo $entidad ?> </h2> -->
