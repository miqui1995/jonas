<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once('../login/conexion2.php');
	$dias_solicitados = ""; // Se inicia variable

	echo "
	<style type='text/css'>
		.boton_hidden{
			font-size:16px; 
			padding:6px;
		}
	</style>";

/* Inicio funciones para ubicacion topografica */	
	function validar_ubicacion($expediente){
		include '../login/conexion2.php';

		$query_ubicacion_topografica="select r.numero_radicado, e.id_expediente, e.codigo_ubicacion_topografica, u.nombre_nivel, u.nivel_padre, e.nombre_expediente from expedientes e join ubicacion_topografica u on e.codigo_ubicacion_topografica=u.id_ubicacion left join radicado r on e.id_expediente=r.id_expediente where e.id_expediente='$expediente' or r.numero_radicado='$expediente'";

		$fila_ubicacion_topografica  = pg_query($conectado,$query_ubicacion_topografica);
		$linea_ubicacion_topografica = pg_fetch_array($fila_ubicacion_topografica);

		if($linea_ubicacion_topografica==false){
			$ubicacion_fisica=false;
		}else{
			$asunto_expediente 	= $linea_ubicacion_topografica['nombre_expediente'];
			$codigo_ubic  		= $linea_ubicacion_topografica['codigo_ubicacion_topografica'];
			$nivel_padre 		= $linea_ubicacion_topografica['nivel_padre'];
			$nombre_nivel 		= $linea_ubicacion_topografica['nombre_nivel'];

			echo "<script>$('#asunto_expediente1').val(\"$asunto_expediente\");</script>";

			$array_padre = array();
			$ubicacion_fisica= verificar_padre($array_padre,"$nombre_nivel","");
		}
		
		return($ubicacion_fisica);
	}
	function verificar_padre($array,$nombre_nivel,$x){
	   	include '../login/conexion2.php';

		$query_ubic1="select * from ubicacion_topografica where nombre_nivel='$nombre_nivel'";
		
		$fila_ubicacion_fisica  = pg_query($conectado,$query_ubic1);
		$linea_ubicacion_fisica = pg_fetch_array($fila_ubicacion_fisica);

		$nivel_padre = $linea_ubicacion_fisica["nivel_padre"];
		//return $nombre_nivel;
		if($x==''){
		    $x=0;    
		}
	    if($nivel_padre!=""){
	    	$array[$x]=$nombre_nivel;
	    	$x++;
			return verificar_padre($array,"$nivel_padre",$x);
		}else{
			return $array;
		}
	}  
/* Fin funciones para ubicacion topografica */	

	$login 				= $_SESSION['login'];
	$login_completo 	= $_SESSION['login']." - ".$_SESSION['nombre'];
	$permiso_prestamos 	= $_SESSION['prestamo_documentos'];

	if($permiso_prestamos=="SI"){
		$solicitud_prestamo_pendientes_general 	= $_SESSION['solicitud_prestamo_pendientes_general'];
	}

	$pestana=$_POST['pestana'];

	switch ($pestana) {
		case 'informacion_prestados_general':
			$query_prestados_general="select * from prestamos p left join dependencias d on p.dependencia_solicitante=d.codigo_dependencia  where p.estado_prestamo='PRESTADO' and p.fecha_devolucion is null order by p.id asc";
			$fila_prestados_general 	 = pg_query($conectado,$query_prestados_general);
			$registros_prestados_general = pg_num_rows($fila_prestados_general);

			if($registros_prestados_general==0){
				echo "<h2><font color=red>No hay pr??stamos de documentos en f??sico pendientes por devolver por los usuarios del sistema.</font></h2>";
			}else{
				echo "
				<center style='overflow-x:scroll'>
					<table border='0' width='100%' style='font-size:12px;'>
						<tr class='row' >
							<td class='descripcion' width='10%' >
								N??mero de Documento Solicitado en Pr??stamo
							</td>
							<td class='descripcion' width='15%'>
								Documento Solicitado
							</td>
							<td class='descripcion' width='15%'>
								Usuario que solicit?? el pr??stamo del documento en f??sico
							</td>
							<td class='descripcion' width='10%'>
								Usuario que realiz?? el pr??stamo del documento en f??sico
							</td>
							<td class='descripcion' width='10%'>
								Fecha de pr??stamo
							</td>
							<td class='descripcion' width='15%'>
								Prestado hace (Cantidad en d??as calendario)
							</td>
							<td class='descripcion' width='25%'>
								Observaciones
							</td>						
						</tr>";
					$num_fila=0;
					for ($i=0;$i<$registros_prestados_general;$i++){
						$linea_prestados_general = pg_fetch_array($fila_prestados_general);

						// Defino las variables a mostrar
						$confirma_recibido 			= $linea_prestados_general['confirma_recibido'];
						$dias_solicitados 			= $linea_prestados_general['dias_solicitados'];
						$documento_solicitado 	 	= $linea_prestados_general['documento_solicitado'];
						$fecha_prestamo 			= $linea_prestados_general['fecha_prestamo'];
						$id 						= $linea_prestados_general['id'];
						$id_documento_solicitado 	= $linea_prestados_general['id_documento_solicitado'];
						$login_prestamista 			= $linea_prestados_general['login_prestamista'];
						$login_solicitante 			= $linea_prestados_general['login_solicitante'];
						$nombre_dependencia 		= $linea_prestados_general['nombre_dependencia'];
						$numero_radicado 			= $linea_prestados_general['numero_radicado'];
						$observaciones 				= $linea_prestados_general['observaciones'];


						if($documento_solicitado=="expediente_completo"){
							$documento_solicitado1="Expediente completo solicitado desde el radicado $numero_radicado";
						}else{
							$documento_solicitado1="Documento individual";
						}
						if($confirma_recibido=="SI"){
							$usuario_confirma_recibido=" quien confirm?? recibir el documento en f??sico";
						}else{
							$usuario_confirma_recibido= " quien NO ha confirmado si recibi?? el documento en f??sico";
						}

						/* Calcular diferencia de d??as entre fecha_prestamo y hoy */
						$date1 		= new DateTime("$fecha_prestamo");
						$date2 		= new DateTime("now");
						$diff 		= $date1->diff($date2);
						$dias_hasta = $diff->days;

						/* Traduce la fecha desde 2018-12-01 18:17:30 a 01 de Diciembre de 2018 a las 18:17:30 */
						include_once("../include/genera_fecha.php");
						$b = new genera_fecha();

						$fecha_prestamo1 = $b->traduce_fecha_letra_segundos("$fecha_prestamo"); // Traduce fecha formato 26 de Diciembre de 2018 a las 18:17:30
						
						// Calcula fecha estimada de devoluci??n con los d??as solicitados
						$estimado1 = date("d-m-Y",strtotime($fecha_prestamo."+ $dias_solicitados days")); 
						$estimado = $b->traduce_fecha_letra("$estimado1");

						if($dias_hasta>$dias_solicitados){
							$limite_dias = "
							<div class='semaforo_rojo' title='T??rmino del pr??stamo vencido' onclick=\"mostrar_boton_devolver('devolver_$numero_radicado')\"> 
								Prestado hace $dias_hasta d??as
							</div>
							<font color=red>
								Debi?? devolverse el $estimado
							</font>	
							<div id='devolver_$numero_radicado' class='botones2 hidden' onclick=\"devolver_documento('$id','$documento_solicitado','$id_documento_solicitado','$numero_radicado')\">
								Devolver
							</div>";
						}else{
							if($dias_solicitados/2>$dias_hasta){
								if($dias_hasta==$dias_solicitados/4){
									$a = "d??a";
								}else{
									$a = "d??as";
								}
								$limite_dias = "<div class='semaforo_verde' title='T??rmino del pr??stamo aceptable.' onclick=\"mostrar_boton_devolver('devolver_$numero_radicado')\">Prestado hace $dias_hasta $a</div><font color=green>Debe devolverse el $estimado</font><div id='devolver_$numero_radicado' class='botones2 hidden' onclick=\"devolver_documento('$id','$documento_solicitado','$id_documento_solicitado','$numero_radicado')\">
									Devolver
								</div>";
							}else{
								$limite_dias = "
								<div class='semaforo_amarillo' title='!ATENCION! T??rmino del pr??stamo por vencer.' onclick=\"mostrar_boton_devolver('devolver_$numero_radicado')\">Prestado hace  $dias_hasta d??as</div><font color=green>Debe devolverse el $estimado</font>
								<div id='devolver_$numero_radicado' class='botones2 hidden' onclick=\"devolver_documento('$id','$documento_solicitado','$id_documento_solicitado','$numero_radicado')\">
									Devolver
								</div>";
							}
						}

						if ($num_fila%2==0){	//si el resto de la divisi??n es 0 pongo un color
							$fila_color = "fila1";
						}else{					//si el resto de la divisi??n NO es 0 pongo otro color 
							$fila_color = "fila2";
						}    

						echo "
						<tr class='$fila_color'>
							<td class='center'> $id_documento_solicitado </td>
							<td class='center'> $documento_solicitado1 </td>
							<td class='center'> <b>$login_solicitante</b> ($nombre_dependencia)<br> $usuario_confirma_recibido </td>
							<td class='center'> $login_prestamista </td>
							<td class='center'> $fecha_prestamo1</td>
							<td class='center'> Se ha solicitado por $dias_solicitados d??as $limite_dias </td>
							<td class='center'> $observaciones </td>	
						</tr>	
						";
						$num_fila++;	
					}
				echo "
					</table>
				</center>";
			}
							
			break;
		case 'informacion_prestados_usuario':
			$query_prestamos_solicitados_usuario="select * from prestamos p left join dependencias d on p.dependencia_solicitante=d.codigo_dependencia  where p.estado_prestamo='PRESTADO' and p.fecha_devolucion is null and login_solicitante='$login' order by p.id desc";
			$fila_prestamos_solicitados_usuario  	 = pg_query($conectado,$query_prestamos_solicitados_usuario);
			$registros_prestamos_solicitados_usuario = pg_num_rows($fila_prestamos_solicitados_usuario);

			if($registros_prestamos_solicitados_usuario==0){
				echo "<h2><font color=red>No hay pr??stamos de documentos en f??sico pendientes por devolver por usted.</font></h2>";
			}else{
				echo "
				<center style='overflow-x:scroll'>
					<table border='0' width='100%' style='font-size:12px;'>
						<tr class='row' >
							<td class='descripcion' width='10%' >
								N??mero de Documento Solicitado en Pr??stamo
							</td>
							<td class='descripcion' width='15%'>
								Documento Solicitado
							</td>
							<td class='descripcion' width='15%'>
								Usuario que ha entregado el documento en f??sico
							</td>
							<td class='descripcion' width='15%'>
								Fecha del pr??stamo
							</td>
							<td class='descripcion' width='15%'>
								Observaciones
							</td>						
							<td class='descripcion' width='30%'>
								Prestado hace (Cantidad en d??as calendario)
							</td>
						</tr>";
				$num_fila=0;		
				for ($i=0; $i < $registros_prestamos_solicitados_usuario; $i++) { 
					$linea_prestamos_solicitados_usuario = pg_fetch_array($fila_prestamos_solicitados_usuario);

					// Defino las variables a mostrar
					$confirma_recibido 			= $linea_prestamos_solicitados_usuario['confirma_recibido'];
					$dias_solicitados 			= $linea_prestamos_solicitados_usuario['dias_solicitados'];
					$documento_solicitado 	 	= $linea_prestamos_solicitados_usuario['documento_solicitado'];
					$fecha_prestamo1 			= $linea_prestamos_solicitados_usuario['fecha_prestamo'];
					$id 						= $linea_prestamos_solicitados_usuario['id'];
					$id_documento_solicitado  	= $linea_prestamos_solicitados_usuario['id_documento_solicitado'];
					$login_prestamista 			= $linea_prestamos_solicitados_usuario['login_prestamista'];
					$login_solicitante 			= $linea_prestamos_solicitados_usuario['login_solicitante'];
					$nombre_dependencia 		= $linea_prestamos_solicitados_usuario['nombre_dependencia'];
					$numero_radicado 			= $linea_prestamos_solicitados_usuario['numero_radicado'];
					$observaciones 				= $linea_prestamos_solicitados_usuario['observaciones'];

					if($documento_solicitado=="expediente_completo"){
						$documento_solicitado1="Expediente completo solicitado desde el radicado $numero_radicado";
					}else{
						$documento_solicitado1="Documento individual";
					}
					if($confirma_recibido=="SI"){
						$usuario_confirma_recibido="Usted ha confirmado recibir ??ste documento en f??sico";
						$boton_prestamo="Usted ya ha confirmado recibir ??ste documento en f??sico";
					}else{
						$usuario_confirma_recibido= "Usted NO ha confirmado todav??a si ha recibido el documento en f??sico";
						$boton_prestamo="<button class='botones2 boton_hidden' onclick=\"
					confirmar_documento_fisico_recibido('$id','El radicado individual n??mero ','$numero_radicado','prestamo_documentos')\">Confirmar Documento / Expediente f??sico recib??do</button>";
					}

					/* Calcular diferencia de d??as entre fecha_prestamo y hoy */
					$date1 		= new DateTime("$fecha_prestamo1");
					$date2 		= new DateTime("now");
					$diff 		= $date1->diff($date2);
					$dias_hasta = $diff->days;

					/* Traduce la fecha desde 2018-12-01 18:17:30 a 01 de Diciembre de 2018 a las 18:17:30 */
					include_once("../include/genera_fecha.php");
					$b = new genera_fecha();

					$fecha_prestamo = $b->traduce_fecha_letra_segundos("$fecha_prestamo1"); // Traduce fecha formato 26 de Diciembre de 2018 a las 18:17:30
						
					// Calcula fecha estimada de devoluci??n con los d??as solicitados
					$estimado1 = date("d-m-Y",strtotime($fecha_prestamo1."+ $dias_solicitados days")); 
					$estimado = $b->traduce_fecha_letra("$estimado1");

					if($dias_hasta>$dias_solicitados){
						$limite_dias = "
						<div class='semaforo_rojo' title='T??rmino del pr??stamo vencido'> Prestado hace $dias_hasta d??as</div><font color=red>Debi?? devolverse el $estimado</font>";
					}else{
						if($dias_solicitados/2>$dias_hasta){
							if($dias_hasta==$dias_solicitados/4){
								$day = "d??a";
							}else{
								$day = "d??as";
							}
							$limite_dias = "
							<div class='semaforo_verde' title='T??rmino del pr??stamo aceptable.'>
								Prestado hace $dias_hasta $day
							</div><font color='green'>Debe devolverse el $estimado</font>";
						}else{
							$limite_dias = "
							<div class='semaforo_amarillo' title='!ATENCION! T??rmino del pr??stamo por vencer.'>Prestado hace  $dias_hasta d??as</div><font color='green'>Debe devolverse el $estimado</font>";
						}
					}
					
					if ($num_fila%2==0){	//si el resto de la divisi??n es 0 pongo un color
						$fila_color = "fila1";
					}else{					//si el resto de la divisi??n NO es 0 pongo otro color 
						$fila_color = "fila2";
					}
					
					echo "
					<tr class='$fila_color'>
						<td class='center'> $id_documento_solicitado </td>
						<td class='center'> $documento_solicitado1 </td>
						<td id='mostrar_boton_prestamo' > 
							<div id='informacion_boton_prestamo'>
								Prestado por <b>$login_prestamista</b>. $usuario_confirma_recibido 
							</div>
							<div id='detalle_boton_prestamo' class='center' >
								$boton_prestamo
							</div>
						</td> 
						<td class='center'> $fecha_prestamo </td>
						<td class='center'> $observaciones </td>	
						<td class='center'> Se ha solicitado por $dias_solicitados d??as $limite_dias </td>
					</tr>	
					";	
					$num_fila++;					
				}	
			}
			echo "</table>";
			break;	
		case 'informacion_solicitud_prestamos_general':
			$query_prestamos_solicitados="select * from prestamos p left join dependencias d on p.dependencia_solicitante=d.codigo_dependencia  where p.estado_prestamo='SOLICITADO' and p.fecha_prestamo is null order by p.id desc";
			$fila_prestamos_solicitados  	 = pg_query($conectado,$query_prestamos_solicitados);
			$registros_prestamos_solicitados = pg_num_rows($fila_prestamos_solicitados);
			echo "
			<center style='overflow-x:scroll'>
				<table border='0' width='100%' style='font-size:12px;'>
					<tr class='row' >
						<td class='descripcion' width='10%' >
							N??mero de Documento Solicitado en Pr??stamo
						</td>
						<td class='descripcion' width='15%'>
							Documento Solicitado
						</td>
						<td class='descripcion' width='15%'>
							Usuario que solicita el pr??stamo
						</td>
						<td class='descripcion' width='15%'>
							Fecha de la solicitud
						</td>
						<td class='descripcion' width='15%'>
							Observaciones
						</td>						
						<td class='descripcion' width='30%'>
							Ubicaci??n f??sica
						</td>
					</tr>";

				$num_fila=0; // Variable declarada para poner color a la fila seg??n ??sta variable.
				for ($i=0;$i<$registros_prestamos_solicitados;$i++){
					$linea_prestamos_solicitados = pg_fetch_array($fila_prestamos_solicitados);

					// Defino las variables a mostrar
					$dias_solicitados 			= $linea_prestamos_solicitados['dias_solicitados'];
					$documento_solicitado 		= $linea_prestamos_solicitados['documento_solicitado'];
					$fecha_solicitud1 			= $linea_prestamos_solicitados['fecha_solicitud'];
					$id 						= $linea_prestamos_solicitados['id'];
					$id_documento_solicitado 	= $linea_prestamos_solicitados['id_documento_solicitado'];
					$login_solicitante 			= $linea_prestamos_solicitados['login_solicitante'];
					$numero_radicado 			= $linea_prestamos_solicitados['numero_radicado'];
					$observaciones 				= $linea_prestamos_solicitados['observaciones'];
					$usuario_solicitante 		= $linea_prestamos_solicitados['login_solicitante']. " ( ".$linea_prestamos_solicitados['nombre_dependencia']." )";

					$ubicacion_topografica2=validar_ubicacion($id_documento_solicitado);

					if($ubicacion_topografica2==false){
						$ubicacion_topografica3="El documento solicitado no tiene ubicaci??n f??sica en el sistema.";
					}else{
						$ubicacion_topografica3="";	
						foreach($ubicacion_topografica2 as $item){
							if($ubicacion_topografica3==""){
								$ubicacion_topografica3=$item;
							}else{
								$ubicacion_topografica3=$ubicacion_topografica3." -> ".$item;
							}
			            }						
					}
					if($documento_solicitado=="expediente_completo"){
						$documento_solicitado1="Expediente completo solicitado desde el radicado $numero_radicado";
					}else{
						$documento_solicitado1="Documento individual";
					}
					if ($num_fila%2==0){	//si el resto de la divisi??n es 0 pongo un color
						$fila_color = "fila1";
					}else{					//si el resto de la divisi??n NO es 0 pongo otro color 
						$fila_color = "fila2";
					}

					$ubicacion_topografica=$ubicacion_topografica3;

					/* Traduce la fecha desde 2018-12-01 18:17:30 a 01 de Diciembre de 2018 a las 18:17:30 */
					include_once("../include/genera_fecha.php");
					$b = new genera_fecha();

					$fecha_solicitud = $b->traduce_fecha_letra_segundos("$fecha_solicitud1"); // Traduce fecha formato 26 de Diciembre de 2018 a las 18:17:30


					$boton_prestamo="<button class='botones2 boton_hidden' onclick=\"cargar_prestamo_documento('$id_documento_solicitado', '$dias_solicitados','$login_solicitante', '$id')\">Prestar Documento / Expediente</button>";

					echo"
					<tr class='$fila_color'>
						<td class='center'> $id_documento_solicitado </td>
						<td class='center'> 
							$documento_solicitado1
						</td> 
						<td> $usuario_solicitante </td> 
						<td> $fecha_solicitud </td> 
						<td> $observaciones </td> 
						<td id='mostrar_boton_prestamo' > 
							<div id='informacion_boton_prestamo'>
								$ubicacion_topografica
							</div>
							<div id='detalle_boton_prestamo' class='center' >
								$boton_prestamo
							</div>
						</td> 
					</tr>	
					";								
				}
	/* Aqui se imprimen los resultados en td en la tabla */;
				$num_fila++; 		
			echo "</table>
			</center>";
			break;
		
		case 'informacion_solicitud_prestamos_usuario':
			$query_prestamos_solicitados="select * from prestamos p left join dependencias d on p.dependencia_solicitante=d.codigo_dependencia  where p.estado_prestamo='SOLICITADO' and login_solicitante='$login' and p.fecha_prestamo is null order by p.id desc";

			$fila_prestamos_solicitados 	 = pg_query($conectado,$query_prestamos_solicitados);
			$registros_prestamos_solicitados = pg_num_rows($fila_prestamos_solicitados);

			if($registros_prestamos_solicitados==0){
				echo "<h2><font color=red>No hay solicitudes de pr??stamo de documentos en f??sico hechas por usted.</font></h2>";
			}else{
				echo "
				<center style='overflow-x:scroll'>
					<table border='0' width='100%' style='font-size:12px;'>
						<tr class='row' >
							<td class='descripcion' width='10%' >
								N??mero de Documento Solicitado en Pr??stamo
							</td>
							<td class='descripcion' width='15%'>
								Documento Solicitado
							</td>
							<td class='descripcion' width='15%'>
								Fecha de la solicitud
							</td>
							<td class='descripcion' width='20%'>
								Observaciones
							</td>						
							<td class='descripcion' width='40%'>
								Ubicaci??n f??sica
							</td>
						</tr>";

				$num_fila=0; // Variable declarada para poner color a la fila seg??n ??sta variable.
				for ($i=0;$i<$registros_prestamos_solicitados;$i++){
					$linea_prestamos_solicitados = pg_fetch_array($fila_prestamos_solicitados);

					// Defino las variables a mostrar
					$dias_solicitados 			= $linea_prestamos_solicitados['dias_solicitados'];
					$documento_solicitado 		= $linea_prestamos_solicitados['documento_solicitado'];
					$fecha_solicitud1 			= $linea_prestamos_solicitados['fecha_solicitud'];
					$id 						= $linea_prestamos_solicitados['id'];
					$id_documento_solicitado 	= $linea_prestamos_solicitados['id_documento_solicitado'];
					$login_solicitante 			= $linea_prestamos_solicitados['login_solicitante'];
					$numero_radicado 			= $linea_prestamos_solicitados['numero_radicado'];
					$observaciones 				= $linea_prestamos_solicitados['observaciones'];

					$ubicacion_topografica2 	= validar_ubicacion($id_documento_solicitado);

					if($ubicacion_topografica2==false){
						$ubicacion_topografica3="El documento solicitado no tiene ubicaci??n f??sica en el sistema.";
					}else{
						$ubicacion_topografica3="";	
						foreach($ubicacion_topografica2 as $item){
							if($ubicacion_topografica3==""){
								$ubicacion_topografica3=$item;
							}else{
								$ubicacion_topografica3=$ubicacion_topografica3." -> ".$item;
							}
			            }						
					}
					$ubicacion_topografica=$ubicacion_topografica3;

					if($documento_solicitado=="expediente_completo"){
						$documento_solicitado1="Expediente completo solicitado desde el radicado $numero_radicado";
						$documento_solicitado2 = "expediente completo";
						$query_nombre_documento_solicitado="select nombre_expediente from expedientes where id_expediente='$id_documento_solicitado'";
					}else{
						$documento_solicitado1="Documento individual";
						$documento_solicitado2 = "documento individual";
						$query_nombre_documento_solicitado="select asunto from radicado where numero_radicado='$id_documento_solicitado'";
					}
					if ($num_fila%2==0){	//si el resto de la divisi??n es 0 pongo un color
						$fila_color = "fila1";
					}else{					//si el resto de la divisi??n NO es 0 pongo otro color 
						$fila_color = "fila2";
					}

					$fila_nombre_documento_solicitado 	= pg_query($conectado,$query_nombre_documento_solicitado);
					$linea_nombre_documento_solicitado 	= pg_fetch_array($fila_nombre_documento_solicitado);
					$nombre_documento_solicitado 		= $linea_nombre_documento_solicitado[0];

					/* Traduce la fecha desde 2018-12-01 18:17:30 a 01 de Diciembre de 2018 a las 18:17:30 */
					include_once("../include/genera_fecha.php");
					$b = new genera_fecha();

					$fecha_solicitud = $b->traduce_fecha_letra_segundos("$fecha_solicitud1"); // Traduce fecha formato 26 de Diciembre de 2018 a las 18:17:30

					echo"
					<tr class='$fila_color'>
						<td class='center'>
							<b> $id_documento_solicitado</b> 
							<p title=' Nombre del $documento_solicitado2'>($nombre_documento_solicitado)</p>
						</td>
						<td class='center'> 
							$documento_solicitado1
						</td> 
						<td class='center'> $fecha_solicitud </td> 
						<td> $observaciones </td> 
						<td id='mostrar_boton_prestamo'> 
							<div id='informacion_boton_prestamo'>
								$ubicacion_topografica
							</div>
							<div id='detalle_boton_prestamo' >
								<center>
									<button class='botones2 boton_hidden'  onclick=\"cancelar_solicitud_prestamo('$id','$numero_radicado','$documento_solicitado')\">
										Cancelar Solicitud Documento / Expediente
									</button>
								<center>
							</div>
						</td> 
					</tr>	
					";								
					$num_fila++; 		
				}
	/* Aqui se imprimen los resultados en td en la tabla */;
				echo "</table>
				</center>";
			}
			
			break;
		case 'reporte_prestamos1':
				echo "<h3>No hay reporte configurado todav??a</h3>";
				break;	
	}	

?>	
<!--Desde aqui el div que contiene el formulario para Modificar expediente-->	
		<div id="ventana_prestar_documento" class="ventana_modal">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
				<h1>Formulario Pr??stamo de Documento</h1>
				<hr>
				<form autocomplete="off">
					<table>
						<input type="hidden" id="numero_radicado"> <!-- Input para guardar campo temporalmente -->
						<input type="hidden" id="nombre_solicitante"> <!-- Input para guardar campo temporalmente -->
						<input type="hidden" id="id"> <!-- Input para guardar campo temporalmente -->

						<tr>
							<td class="descripcion">Tipo de pr??stamo :</td>
							<td class="detalle">
								<select name="tipo_prestamo" id="tipo_prestamo" class='select_opciones' <?php echo "onchange=\"valida_tipo_prestamo('$dias_solicitados');calcular_fecha_limite()\""; ?>	>
									
									<option value="prestamo_indefinido">Pr??stamo Indefinido</option>
									<option value="dias_prestamo" selected="selected">Dias que se va a prestar</option>		
								</select>
								<input type="search" id="dias_prestamo" style="width:60px;" 
								<?php echo "onblur=\"validar_input('dias_prestamo'); calcular_fecha_limite()\""; ?>

								onblur="validar_input('dias_prestamo'); "><br>
								 <div id="error_dias_prestamo"  class="errores">El t??rmino del pr??stamo debe ser un n??mero.</div>
								 <div id="dias_prestamo_null" class="errores">El t??rmino del pr??stamo es obligatorio.</div>
	                            <div id="dias_prestamo_max" class="errores">M??ximo 30 d??as de pr??stamo (Renovables) </div>
								<span id="duracion_estimada_prestamo"> M??ximo 30 d??as (Renovable)</span>		
							</td>
						</tr>
						<tr>
	                        <td class="descripcion">Observaciones :</td>
	                        <td class="detalle" colspan="3">
	                            <textarea name="observaciones_solicitud_prestamo" id="observaciones_solicitud_prestamo" rows="2" style="width:100%;padding:5px;" placeholder="Ingrese las observaciones. Sea lo m??s espec??fico posible" title="Ingrese las observaciones. Sea lo m??s espec??fico posible" onblur="validar_input('observaciones_solicitud_prestamo')" > </textarea>
	                            <div id="error_observaciones_solicitud_prestamo" class="errores">El mensaje de observaciones es obligatorio</div>
	                            <div id="observaciones_solicitud_prestamo_min" class="errores">El mensaje de observaciones no puede ser menor a 6 caracteres (numeros o letras) </div>
	                            <div id="observaciones_solicitud_prestamo_max" class="errores">El mensaje de observaciones no puede ser mayor a 500 caracteres (numeros o letras)</div>
	                        </td>
	                    </tr>
	                    <tr>
	                    	<td class="descripcion">Confirma Recibido ( No es obligatorio. El usuario solicitante <?php echo "[<b id='nombre_solicitante1'></b>]"; ?> puede ingresar su password para confirmar que ha recibido el documento solicitado en f??sico. <br>Si no lo ingresa aqui, en su propia sesion de Jonas le aparecer?? la alerta con la opci??n para confirmar que ha recibido el documento en f??sico. )</td>
	                    	<td class="detalle">
	                    		<input type="password" id="contr_confirma_recibido" title="Presione la tecla enter para validar la contrase??a." onkeyup="validar_mostrar_boton_prestar_documento(this.value); if (event.keyCode==13){validar_confirma_recibido(); return false;}" placeholder="El usuario solicitante puede ingresar su password aqui">
								
								<div id="error_contr_confirma_recibido" class="errores">La contrase??a no corresponde al usuario solicitante del pr??stamo</div>
	                    	</td>
	                    </tr>
						<tr>
							<td colspan="2">
								<center>
								<div id="resultado_prestamo_documento"></div>
								<div id="contenedor_boton_enviar_prestamo_documento">
									<input type="button" value="Prestar Documento" id="enviar_prestamo_documento" class="botones" onclick="enviar_prestamo()">
								</div>
								<center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para modificar expediente -->