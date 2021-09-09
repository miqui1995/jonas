<style type="text/css">
	.fila_resultados{	/* Formato resultados del listado bandejas entrada/salida */
		background-color 	: #e0e6e7;
		cursor 				: pointer;
	}
	.fila_resultados2{	/* Formato resultados del listado bandejas entrada/salida */
		background-color 	: #EAEBF0DE;
		cursor 				: pointer;
	}
	.fila_resultados:hover{
		box-shadow  		: 10000px 0 #D2D2D2 inset;
		color 				: black;
		font-weight 		: bold;
	}
	.fila_resultados2:hover{
		box-shadow  		: 10000px 0 #D2D2D2 inset;
		color 				: black;
		font-weight 		: bold;
	}
</style>
<?php 
	require_once('../login/validar_inactividad.php');// Se valida la inactividad 

	$query_rep = "select * from radicado where asunto IS NOT NULL and usuarios_control ilike '%$login%' and estado_radicado='en_tramite'";			
		
	$fila_rep 		= pg_query($conectado,$query_rep);
	$registros_rep 	= pg_num_rows($fila_rep); 

   	if($registros_rep==0){
   		$tabla_dependencias = "<h2>No hay resultados con los datos ingresados</h2>";
   	}else{
		$tabla_dependencias = "<table border='0' style='width:2000px;' ><tr class='center'><td class='descripcion' style='width:25px'>ID</td><td class='descripcion' style='width:150px'>Numero Radicado</td><td class='descripcion' style='width:100px'>Usuario(s) Responsable(s) de Tramitar Documento</td><td class='descripcion' style='width:200px'>Tiempo que ha pasado desde que se recibió</td><td class='descripcion' style='width:360px;'>Usuario lo tiene asignado desde</td><td class='descripcion' style='width:75px;'>Estado del radicado</td><td class='descripcion' style='width:75px;'>Medio de Recepcion del radicado</td><td class='descripcion' style='width:160px;'>Medio de Respuesta solicitado por el usuario</td><td class='descripcion' style='width:200px;'>Expediente Asignado</td><td class='descripcion' style='width:115px;'>TRD Asignada</td><td class='descripcion' style='width:350px;'>Asunto</td></tr>";
   				
	   	for ($k=0; $k < $registros_rep; $k++) { 
	   		$h = $k+1;
			$linea_rep 						= pg_fetch_array($fila_rep);
	   		$numero_radicado 				= $linea_rep['numero_radicado'];
	   		$usuarios_control 				= $linea_rep['usuarios_control'];
	       	$asunto 						= $linea_rep['asunto'];
	       	$codigo_serie 					= $linea_rep['codigo_serie'];
	       	$codigo_subserie 				= $linea_rep['codigo_subserie'];
	       	$estado_radicado 				= $linea_rep['estado_radicado'];
	       	$fecha_radicado 				= $linea_rep['fecha_radicado'];
	       	$id_expediente 					= $linea_rep['id_expediente'];
	       	$medio_recepcion 				= $linea_rep['medio_recepcion'];
	       	$medio_respuesta_solicitado 	= $linea_rep['medio_respuesta_solicitado'];

	   		/* Se calculan los dias que han pasado desde que se radica el documento */
    		$fecha2 		= date("Y/m/d");

			$dias = (strtotime($fecha_radicado)-strtotime($fecha2))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
			$dias = abs($dias); 
			$dias = floor($dias); // Esta es la cantidad de dias calculado.

			/* Se calcula la fecha desde la que tiene el documento reasignado */
			// $query_historico_reasignado = "select * from historico_eventos where numero_radicado ='$numero_radicado' order by id desc";

			$query_historico_reasignado = "select * from historico_eventos where numero_radicado ='$numero_radicado' and (transaccion ilike '%Se reasigna radicado al usuario%' or transaccion ilike '%Radicacion de entrada%' or transaccion ilike '%Genera plantilla radicacion%') order by id desc";

			$fila_historico_reasignado 		= pg_query($conectado,$query_historico_reasignado);
			$linea_historico_reasignado 	= pg_fetch_array($fila_historico_reasignado);
			$fecha_historico_reasignado 	= $linea_historico_reasignado['fecha']; 
			$transaccion_hist_reasignado 	= $linea_historico_reasignado['transaccion']; 
			$usuario_hist_reasignado 	 	= $linea_historico_reasignado['usuario']; 

	   		/* Se calculan los dias que han pasado desde que se recibió reasignado el documento */
    		$fecha_hoy 		= date("Y/m/d");

			$dias2 = (strtotime($fecha_historico_reasignado)-strtotime($fecha_hoy))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
			$dias2 = abs($dias2); 
			$dias2 = floor($dias2); // Esta es la cantidad de dias calculado.

			/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2020"*/
			require_once "../include/genera_fecha.php";
			$fecha_modificacion=$b->traducefecha($fecha_historico_reasignado);	

			/* Se define mensaje para id_expediente1 */
			if($id_expediente==""){
				$id_expediente = " <b style=\"color: red\"> No se ha asingado expediente todavía</b>";
			}

			/* Se define mensaje para TRD */
			if($codigo_subserie==""){
				$trd = "<b style=\"color: red\"> No se ha asingado TRD todavía</b>";
			}else{
				$trd = "Serie ($codigo_serie)<br>Subserie($codigo_subserie)";
			}

			if($k%2==0){
   				$fila_resultados = "fila_resultados";
 			}else{
 				$fila_resultados = "fila_resultados2";
  			}
	   		$tabla_dependencias .= "
	   		<tr class='$fila_resultados' onclick='agregar_pestanas(\"$numero_radicado\")'>
	   			<td class='center'>$h</td>
	   			<td class='center'>$numero_radicado</td>
	   			<td>$usuarios_control</td>
	   			<td>Han pasado <b style=\"color: red\"> $dias</b> dias desde que se recibió documento</td>
	   			<td class='center'>$transaccion_hist_reasignado <br>(Desde el usuario <b style=\"color: green\"> $usuario_hist_reasignado</b>) <br>el $fecha_modificacion<br> <b style=\"color: blue\">Hace $dias2 dias </b> </td>
	   			<td class='center'> <b style=\"color: red\"> $estado_radicado</b></td>
	   			<td class='center'>$medio_recepcion</td>
	   			<td class='center'>$medio_respuesta_solicitado</td>
	   			<td class='center'>$id_expediente</td>
	   			<td class='center'>$trd</td>
	   			<td>$asunto</td>
	   		</tr>";
	   	}
		$tabla_dependencias.="</table>";	
	}
	echo "$tabla_dependencias";
?>