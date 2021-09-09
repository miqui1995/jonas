<?php
/* Este archivo recibe dos variables: sql y nombre_reporte por GET. Se debe armar la variable $encabezado_tabla y $cuerpo_tabla */
	session_start();
	$query 				= $_GET['sql'];	// Recibe la query para llenar las celdas
	$nombre_reporte 	= $_GET['nombre_reporte'];

	$timestamp = date('Y-m-d');

	$usuario = $_SESSION['login'];

	// require_once("../include/dompdf/dompdf_config.inc.php");
	// require_once("../include/dompdf/Dompdf.php");
	// require_once 'dompdf/autoload.inc.php';
	require_once '../include/dompdf/autoload.inc.php';
	require_once("../login/conexion2.php");

  	$sql 	= pg_query("$query"); // Ejecuta la consulta

	$registros_reporte1 = pg_num_rows($sql); // Se trae la cantidad de filas de la query

  	$cuerpo_tabla="";
	
	switch ($nombre_reporte) {
		case 'listado_usuarios_por_dependencia':
			$orientacion_papel = "portrait"; // "portrait" o "landscape"
			$titulo_reporte = "Listado de Usuarios por Dependencia";
			$encabezado_tabla = '
			<tr bgcolor="#2D9DC6" align="center">
		    	<td><strong><font color="white">Codigo de la Dependencia</font></strong></td>
		    	<td><strong><font color="white">Nombre de la Dependencia</font></strong></td>
		    	<td><strong><font color="white">Perfil</font></strong></td>
		    	<td><strong><font color="white">Documento Usuario</font></strong></td>
		    	<td><strong><font color="white">Nombre Completo</font></strong></td>
		    	<td><strong><font color="white">Login</font></strong></td>
		    	<td><strong><font color="white">Mail</font></strong></td>
		  	</tr>';

			while($linea=pg_fetch_array($sql)){		
				$codigo_dependencia 	= $linea['codigo_dependencia'];
				$nombre_dependencia 	= $linea['nombre_dependencia'];
				$documento_usuario  	= $linea['documento_usuario'];
				$perfil  				= $linea['perfil'];
				$nombre_completo  		= $linea['nombre_completo'];
				$login  				= $linea['login'];
				$mail_usuario  			= $linea['mail_usuario'];	
				// Aqui define el cuerpo de la tabla
				$cuerpo_tabla.= "
				<tr>
					<td align='center' width='30px'>$codigo_dependencia</td>
					<td>$nombre_dependencia</td>
					<td width='30px'>$perfil</td>
					<td width='30px'>$documento_usuario</td>
					<td>$nombre_completo</td>
					<td width='30px'>$login</td>
					<td width='30px'>$mail_usuario</td>                   
			 	</tr> ";
			  
			}		
			break;

		case 'reporte1_entrega_correspondencia_entrada':

			$codigo_entidad = $_GET['codigo_entidad'];
			// $codigo_entidad = 'L01';

			$orientacion_papel = "landscape"; // "portrait" o "landscape"
			
			/* Se inician variables para mostrar reporte según codigo_entidad*/
			switch ($codigo_entidad) {
				case 'AV1':
					$encabezado_reporte = "
						<table border='0' width='100%'>
							<tr>
								<td colspan='2'>
									<center>
										<img src='../imagenes/logos_entidades/logo_largo_av1.png' height='60px'>
									</center>
								</td>
								<td colspan='4'>
									<center>
										<h2>Planilla de Entrega de Correspondencia Recibida en la Ventanilla Única de Correspondencia</h2>
									</center>
								</td>	
								<td style='text-align:center'><b>Usuario :</b> $usuario<br><br><b>Fecha reporte</b> <br>$timestamp</td>
							</tr>
						</table>";
					break;
				case 'EJC':
				case 'EJEC':
					$encabezado_reporte = "
						<table width='100%' border='1' cellspacing='0' cellpadding='0' style='font-size:10px;'>
							<tr>
								<td colspan='1' rowspan='4'>
									<center>
										<img src='../imagenes/logos_entidades/logo_planilla_entrega_ejc.png' height='60px'>
									</center>
								</td>
								<td colspan='4' rowspan='4'>
									<center>
										<h1>PLANILLA ENTREGA DE COMUNICACIONES OFICIALES</h1>
									</center>
								</td>	
								<td width='150px' style='padding: 5px;'>
									<b>Página _____ de _____</b>
								</td>
							</tr>
							<tr>
								<td style='padding: 5px;'>
									<b>Código: FO-SECEJ-CEAYG-020</b>
								</td>
							</tr>
							<tr>
								<td style='padding: 5px;'>
									<b>Versión: 5</b>
								</td>
							</tr>
							<tr>
								<td style='padding: 5px;'>
									<b>Fecha Emisión : 2016-06-07</b>
								</td>
							</tr>
						</table>";
					break;
							
				default:
					$encabezado_reporte = "
						<table border='0' width='100%'>
							<tr>
								<td colspan='2'>
									<center>
										<img src='../imagenes/iconos/logo_largo.png' height='60px'>
									</center>
								</td>
								<td colspan='4'>
									<center>
										<h2>Planilla de Entrega de Correspondencia Recibida en la Ventanilla Única de Correspondencia</h2>
									</center>
								</td>	
								<td style='text-align:center'><b>Usuario :</b> $usuario<br><br><b>Fecha reporte</b> <br>$timestamp</td>
							</tr>
						</table>";
					break;
			}			
			/* Se arma la tabla de datos que se muestran */
			$dependencia_actual1 	= "";
			$dependencia_actual2 	= "";
			$encabezado_tabla 		= "";
			$tabla_oficios 			= "";
			$tabla_pqr 				= "";
			$j  					= 1;
			$k  					= 1;

			$dependencia_remite 	= $_SESSION['nombre_dependencia'];
			$cod_dependencia_remite = $_SESSION['dependencia'];
			// var_dump($_SESSION);

			while($linea_reporte1=pg_fetch_array($sql)){
				$asunto 						= $linea_reporte1['asunto'];
				$clasificacion_radicado 		= $linea_reporte1['clasificacion_radicado'];
				$clasificacion_seguridad 		= $linea_reporte1['clasificacion_seguridad'];
				$dependencia_actual 			= $linea_reporte1['dependencia_actual'];
				$folios 						= $linea_reporte1['folios'];
				$nombre_dependencia 			= $linea_reporte1['nombre_dependencia'];
				$numero_radicado 				= $linea_reporte1['numero_radicado'];
				$usuario_actual 				= $linea_reporte1['usuario_actual'];

				$clasificacion_seguridad1 = strtoupper(str_replace("_", " ", $clasificacion_seguridad));

				/* Variable para imprimir encabezado de consulta.*/
				/*if($dependencia_actual1==""){ // Si es la primera columna
					$dependencia_actual1 = $dependencia_actual;
				}*/

				switch ($clasificacion_radicado){
					case 'Oficio':
					case 'OFICIO':
					case 'COMUNICACION OFICIAL':
						if($dependencia_actual!=$dependencia_actual1 || ($dependencia_actual1=="")){
							if($dependencia_actual1!=""){
								$tabla_oficios.="</table><br><br>";
								$j 				= 1;
							}
							$tabla_oficios.="
								<table width='100%' border='1' cellspacing='0' cellpadding='0' style='font-size:10px;'>
									<tr>
										<td colspan='5'> <b>Unidad : </b> $dependencia_actual - $nombre_dependencia</td>
										<td colspan='5'> <b>Fecha Elaboración :</b> $timestamp</td>
									</tr>
									<tr align='center'>
										<td style='width:2%;'><strong>No.</strong></td>
								    	<td style='width:8%;'><strong>RADICADO</strong></td>
								    	<td><strong>ASUNTO</strong></td>
								    	<td style='width:10%;'><strong>ORIGEN</strong></td>
								    	<td style='width:5%;'><strong>DESTINO</strong></td>
								    	<td style='width:5%;'><strong>NIVEL DE CLASIFICACIÓN</strong></td>
								    	<td style='width:5%;'><strong>NUMERO DE FOLIOS</strong></td>
								    	<td style='width:8%;'><strong>GRADO Y NOMBRE</strong></td>
								    	<td style='width:8%;'><strong>FECHA Y HORA</strong></td>
								    	<td style='width:8%;'><strong>FIRMA</strong></td>
									</tr>
							";
						}

						$tabla_oficios .= "
						<tr align='center'>
							<td style='padding:3px;'>$j</td>
							<td style='padding:3px;'>$numero_radicado</td>
							<td style='padding:3px;' align='justify'>$asunto</td>
							<td style='padding:2px;'>$usuario_actual ($cod_dependencia_remite)</td>
							<td style='padding:3px;'>$dependencia_actual<br></td>
							<td style='padding:3px;'>$clasificacion_seguridad1</td>
							<td style='padding:2px;'>$folios</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>";

						
						$dependencia_actual1 = $dependencia_actual;

						$j++;
						break;
					
					default:
						if($dependencia_actual!=$dependencia_actual2 || ($dependencia_actual2=="")){
								if($dependencia_actual2!=""){
									$tabla_pqr.="</table><br><br>";
									$k 				= 1;
								}
								$tabla_pqr.="
									<table width='100%' border='1' cellspacing='0' cellpadding='0' style='font-size:10px;'>
										<tr>
											<td colspan='5'> <b>Unidad : </b> $dependencia_actual - $nombre_dependencia </td>
											<td colspan='6'> <b>Fecha Elaboración :</b> $timestamp</td>
										</tr>
										<tr align='center'>
											<td style='width:2%;'><strong>No.</strong></td>
									    	<td style='width:8%;'><strong>RADICADO</strong></td>
									    	<td><strong>ASUNTO</strong></td>
									    	<td style='width:10%;'><strong>ORIGEN</strong></td>
									    	<td style='width:5%;'><strong>DESTINO</strong></td>
									    	<td style='width:5%;'><strong>NIVEL DE CLASIFICACIÓN</strong></td>
									    	<td style='width:5%;'><strong>NUMERO DE FOLIOS</strong></td>
									    	<td style='width:8%;'><strong>CLASIFICACION PQR</strong></td>
									    	<td style='width:8%;'><strong>GRADO Y NOMBRE</strong></td>
									    	<td style='width:8%;'><strong>FECHA Y HORA</strong></td>
									    	<td style='width:8%;'><strong>FIRMA</strong></td>
										</tr>
								";
							}

							$tabla_pqr .= "
							<tr align='center'>
								<td style='padding:3px;'>$k</td>
								<td style='padding:3px;'>$numero_radicado</td>
								<td style='padding:3px;' align='justify'>$asunto</td>
								<td style='padding:2px;'>$usuario_actual ($dependencia_actual)</td>
								<td style='padding:3px;'>$dependencia_actual</td>
								<td style='padding:3px;'>$clasificacion_seguridad1</td>
								<td style='padding:2px;'>$folios</td>
								<td style='padding:3px;'>$clasificacion_radicado</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>";
						$dependencia_actual2 = $dependencia_actual;

						$k++;
						
						break;
				}
		    } 

			$encabezado_tabla_pqr = '<center><h2>Documentos clasificados como PQR</h2></center>';

		    if($k==1){
			    $cuerpo_tabla= "</table>$tabla_oficios</table><br>";
		    }else if($j==1){
			    $cuerpo_tabla= "</table>$encabezado_tabla_pqr $tabla_pqr</table><hr><br>";
		    }else{
			    $cuerpo_tabla= "</table>$tabla_oficios</table> <br><br><hr><hr>$encabezado_tabla_pqr $tabla_pqr</table><hr><br>";
		    }
		    switch ($codigo_entidad) {
		    	case 'AV1':
		    	case 'GC1':
		    	case 'EJC':
		    	case 'EJEC':
		    	case 'L01':
	    			$observaciones 		= $_GET['observaciones']; 	

		    		$cuerpo_tabla.="
		    		<table width='100%' border='1' cellspacing='0' cellpadding='0' style='font-size:10px;'>
		    			<tr>
		    				<td height='50px' width='200px;' style='padding:0px 5px;'>Observaciones :</td>
		    				<td style='padding: 3px; text-align:justify;'>$observaciones</td>
		    			</tr>
		    			<tr>
		    				<td colspan='2'><b>NOMBRE Y CARGO DEL FUNCIONARIO QUE ENTREGA : $usuario_actual <b> ($dependencia_remite)</b></td>
		    			</tr>
		    		</table>";
		      		break;
		    	
		    	default:
		    		# code...
		    		break;
		    }

			break;
		/*****************************************************************************************
		Inicio Case reporte2_radicado_entrega para estructurar el archivo pdf
		/*****************************************************************************************
		* @brief trata las variables ya recogidas en el mismo archivo
		- para crear la estructura del pdf a imprimir
		* @return {string} String con el encabezado y el cuerpo del pdf ($encabezado_tabla y $cuerpo_tabla)
		*****************************************************************************************/
		case 'reporte2_radicado_entrega':
			$orientacion_papel = "landscape"; // "portrait" o "landscape"
			$titulo_reporte = "Reporte de Radicados Entregados";
			
			$encabezado_tabla = '<table width="91%" border="1" cellspacing="0" cellpadding="0" style="font-size:10px;">
			<tr bgcolor="#2D9DC6" align="center">
				<td style="width:10px;"><strong><font color="white">Id</font></strong></td>
		    	<td style="width:30px;"><strong><font color="white">Numero Radicado</font></strong></td>
		    	<td style="width:80px;"><strong><font color="white">Fecha Radicación</font></strong></td>
		    	<td style="width:100px;"><strong><font color="white">Asunto</font></strong></td>
		    	<td style="width:100px;"><strong><font color="white">Imagen PDF</font></strong></td>
		    	<td style="width:50px;"><strong><font color="white" align="center">Dependencia</font></strong></td>
		    	<td style="width:50px;"><strong><font color="white" align="center">Usuario Radicador</font></strong></td>
			</tr>';
	  		$tabla       		= "";
			$j  				= 1;
			while($linea_reporte1=pg_fetch_array($sql)){
				$numero_radicado 				= $linea_reporte1['numero_radicado'];
				$fecha_radicado 				= $linea_reporte1['fecha_radicado'];
				$asunto 						= $linea_reporte1['asunto'];
				$dependencia_radicador 		    = $linea_reporte1['dependencia_radicador'];
				$nombre_dependencia 		    = $linea_reporte1['nombre_dependencia'];
				$usuario_radicador              = $linea_reporte1['usuario_radicador'];
				$path_radicado                  = $linea_reporte1['path_radicado'];
				if ($path_radicado != "") {
					$imagen_pdf = "Si";
				} else {
					$imagen_pdf = "No";
				}
				$tabla .= "
						<tr>
							<td align='center'>$j</td>
							<td align='center'>$numero_radicado</td>
							<td align='center'>$fecha_radicado</td>
							<td>$asunto</td>
							<td>$imagen_pdf</td>
							<td align='center'>$dependencia_radicador<br>($nombre_dependencia)</td>
							<td align='center'>$usuario_radicador</td>
						</tr>";
				$j++;
				} 
		    $cuerpo_tabla = "$tabla</table>";
		    $encabezado_reporte ="";
		break;
		/*****************************************************************************************
		Fin Case reporte2_radicado_entrega para estructurar el archivo pdf
		/*****************************************************************************************/
	}

	$codigoHTML="
		$encabezado_reporte
		<table width='100%' border='1' cellspacing='0' cellpadding='0' style='font-size:10px;'>   
			$encabezado_tabla $cuerpo_tabla
		</table>";	

	 // echo "$codigoHTML";
	
	$codigoHTML = utf8_decode($codigoHTML);
	
	// $dompdf 	= new DOMPDF();
	// $dompdf->set_paper("A4", "$orientacion_papel");
	// $dompdf->load_html($codigoHTML);
	// ini_set("memory_limit","128M");
	// $dompdf->render();
	// $dompdf->stream("$nombre_reporte.pdf");

	// reference the Dompdf namespace
	use Dompdf\Dompdf;

	// instantiate and use the dompdf class
	$dompdf = new Dompdf();
	$dompdf->loadHtml($codigoHTML);

	// (Optional) Setup the paper size and orientation
	$dompdf->set_paper("A4", "$orientacion_papel");

	// Render the HTML as PDF
	$dompdf->render();

	// Output the generated PDF to Browser
	$dompdf->stream("$nombre_reporte.pdf");
	// $dompdf->stream();
?>  