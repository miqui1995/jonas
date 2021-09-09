<?php
/* Este archivo recibe dos variables: sql y nombre_reporte por GET. Se debe armar la variable $encabezado_tabla y $cuerpo_tabla */
	$query 				= $_GET['sql'];	// Recibe la query para llenar las celdas
	$nombre_reporte 	= $_GET['nombre_reporte'];
	$codigo_entidad 	= $_GET['codigo_entidad']; // Recibe codigo de la entidad para condicionar formatos

	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$nombre_reporte.xls");
	// echo "$query";
	
	require_once("../login/validar_inactividad.php");
  	$sql 			= pg_query($query); // Ejecuta la consulta

  	$cuerpo_tabla 	= "";
	
	switch ($nombre_reporte) {
		case 'listado_dependencias':
			$encabezado_tabla = '
			<tr bgcolor="#2D9DC6" align="center">
		    	<td><strong><font color="white">Numero consecutivo</font></strong></td>
		    	<td><strong><font color="white">Codigo de la dependencia</font></strong></td>
		    	<td><strong><font color="white">Nombre de la Dependencia</font></strong></td>
		    	<td><strong><font color="white">Nombre de la Dependencia Padre</font></strong></td>
		    	<td><strong><font color="white">Activa (Si - No)</font></strong></td>
		  	</tr>';

		  	while($linea=pg_fetch_array($sql)){		
				$id_dependencia  		= $linea['id_dependencia'];
				$codigo_dependencia 	= $linea['codigo_dependencia'];
				$nombre_dependencia 	= $linea['nombre_dependencia'];
				$dependencia_padre  	= $linea['dependencia_padre'];
				$activa  				= $linea['activa'];
				
				// Aqui define el cuerpo de la tabla
				$cuerpo_tabla.= "
				<tr>
					<td align='center'>$id_dependencia</td>
					<td align='center'>$codigo_dependencia</td>
					<td>$nombre_dependencia</td>
					<td>$dependencia_padre</td>
					<td align='center'>$activa</td>
			 	</tr> ";	  
			}

			break;
		case 'listado_usuarios_por_dependencia':
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
					<td align='center'>$codigo_dependencia</td>
					<td>$nombre_dependencia</td>
					<td>$perfil</td>
					<td>$documento_usuario</td>
					<td>$nombre_completo</td>
					<td>$login</td>
					<td>$mail_usuario</td>                   
			 	</tr> ";	  
			}
			break;
		
		case 'reporte1_entrega_correspondencia_entrada':
			$encabezado_tabla = '
			<tr bgcolor="#2D9DC6" align="center">
		    	<td><strong><font color="white">Id</font></strong></td>
		    	<td><strong><font color="white">Numero Radicado</font></strong></td>
		    	<td><strong><font color="white">Asunto</font></strong></td>
		    	<td><strong><font color="white">Fecha Radicación</font></strong></td>
		    	<td><strong><font color="white">Área Destino</font></strong></td>
		    	<td><strong><font color="white">Tipo PQR</font></strong></td>
		    	<td><strong><font color="white">Clasificacion Seguridad</font></strong></td>
		    	<td><strong><font color="white">Folios</font></strong></td>
		  	</tr>';
		  	$a = 1;
			while($linea=pg_fetch_array($sql)){		
				$asunto 						= $linea['asunto'];
				$clasificacion_radicado 		= $linea['clasificacion_radicado'];
				$clasificacion_seguridad 		= $linea['clasificacion_seguridad'];
				$dependencia_actual 			= $linea['dependencia_actual'];
				$fecha_radicado 				= $linea['fecha_radicado'];
				$folios 						= $linea['folios'];
				$nombre_dependencia 			= $linea['nombre_dependencia'];
				$numero_radicado 				= $linea['numero_radicado'];

				// Aqui define el cuerpo de la tabla
				$cuerpo_tabla.= "
				<tr>
					<td align='center'>$a</td>
					<td align='center'>$numero_radicado</td>
					<td>$asunto</td>
					<td>$fecha_radicado</td>
					<td>$dependencia_actual ($nombre_dependencia)</td>
					<td>$clasificacion_radicado</td>
					<td>$clasificacion_seguridad</td>
					<td>$folios</td>                                    
			 	</tr> ";
			 	$a++;	  
			}
		break;
		/*****************************************************************************************
		Inicio Case reporte2_radicado_entrega para estructurar el archivo excel
		/*****************************************************************************************
		* @brief trata las variables ya recogidas en el mismo archivo
		- para crear la estructura del excel a imprimir
		* @return {string} String con el encabezado y el cuerpo de la tabla ($encabezado_tabla y cuerpo_tabla)
		*****************************************************************************************/
		case 'reporte2_radicado_entrega':
			$encabezado_tabla = '
			<tr bgcolor="#2D9DC6" align="center">
		    	<td><strong><font color="white">Id</font></strong></td>
		    	<td><strong><font color="white">Numero Radicado</font></strong></td>
		    	<td><strong><font color="white">Fecha Radicación</font></strong></td>
		    	<td><strong><font color="white">Asunto</font></strong></td>
		    	<td><strong><font color="white">Usuario(s) responsables</font></strong></td>
		    	<td><strong><font color="white">Codigo Dependencia</font></strong></td>
		    	<td><strong><font color="white">Nombre Dependencia</font></strong></td>
		    	<td><strong><font color="white">Estado</font></strong></td>
		    	<td><strong><font color="white">Dias desde que se ha recibido</font></strong></td>
		  	</tr>';// Estructura html
		  	$a =1;
			while($linea=pg_fetch_array($sql)){		
				$asunto 					= $linea['asunto'];
				$dependencia_actual 		= $linea['dependencia_actual'];
				$estado_radicado       		= $linea['estado_radicado'];
				$fecha_radicado 			= $linea['fecha_radicado'];
				$nombre_dependencia 		= $linea['nombre_dependencia'];
				$numero_radicado 			= $linea['numero_radicado'];
				$usuarios_control 	 		= $linea['usuarios_control'];

				/* Se calculan los dias que han pasado desde que se radica el documento */
        		$fecha2 		= date("Y/m/d");

				$dias = (strtotime($fecha_radicado)-strtotime($fecha2))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
				$dias = abs($dias); 
				$dias = floor($dias); // Esta es la cantidad de dias calculado.

				$cuerpo_tabla.= "
				<tr>
					<td align='center'>$a</td>
					<td align='center'>$numero_radicado</td>
					<td>$fecha_radicado</td>
					<td>$asunto</td>
					<td>$usuarios_control</td>					
					<td>$dependencia_actual</td>					
					<td>$nombre_dependencia</td>
					<td>$estado_radicado</td>                 
					<td align='center'>$dias</td>                 
			 	</tr> ";// Estructura html
			 	$a++;	  
			}
		break;
		/*****************************************************************************************
		Fin Case reporte2_radicado_entrega para estructurar el archivo excel
		/*****************************************************************************************/
		default:
			# code...
			break;
	}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">   
<?php
	echo "$encabezado_tabla $cuerpo_tabla";	
?>  
</table>
</body>
</html>