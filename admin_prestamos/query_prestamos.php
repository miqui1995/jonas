<?php   
	if(!isset($_SESSION)){
		session_start();
	}
	require_once('../login/conexion2.php');

	$timestamp = date('Y-m-d H:i:s');		// Genera la fecha de transaccion para historico eventos

	$usuario=$_SESSION['login'];

	if(isset($_POST['radicado'])){
		$codigo_documento = $_POST['radicado']; 
		$termino = $_POST['termino']; 
		$observaciones_prestamo = trim($_POST['observaciones_prestamo']); 
		$confirma_recibido = $_POST['confirma_recibido']; 
		$id = $_POST['id']; 
		$login_prestamista=	$usuario;

		 /* Se define consecutivo de la tabla prestamos */
 			$query_max_prestamo="select max(id+1) from prestamos;";

			$fila_max_prestamo = pg_query($conectado,$query_max_prestamo);
			$linea_prestamo = pg_fetch_array($fila_max_prestamo);

			if($linea_prestamo[0]==""){
				$max_id_prestamo=1;	// Inicia el consecutivo del préstamo
			}else{
				$max_id_prestamo =$linea_prestamo[0];	
			}
		/* Fin definición consecutivo de la tabla prestamos */

		$query_prestamo="select * from prestamos where id_documento_solicitado='$codigo_documento' and estado_prestamo='SOLICITADO' and fecha_devolucion is null";
		$fila_prestamo = pg_query($conectado,$query_prestamo);
		$linea_prestamo = pg_fetch_array($fila_prestamo);

		$dependencia_solicitante 	= $linea_prestamo['dependencia_solicitante'];
		$documento_solicitado 		= $linea_prestamo['documento_solicitado'];
		$fecha_solicitud 			= $linea_prestamo['fecha_solicitud'];
		$login_solicitante 			= $linea_prestamo['login_solicitante'];
		$numero_radicado 			= $linea_prestamo['numero_radicado'];

		$query_prestar_documento="insert into prestamos(id, login_solicitante, dependencia_solicitante, login_prestamista, documento_solicitado, id_documento_solicitado, numero_radicado, estado_prestamo, dias_solicitados, fecha_solicitud, fecha_prestamo, confirma_recibido, observaciones) values($max_id_prestamo,'$login_solicitante', '$dependencia_solicitante', '$login_prestamista', '$documento_solicitado', '$codigo_documento','$numero_radicado', 'PRESTADO', $termino, '$fecha_solicitud', '$timestamp', '$confirma_recibido', '$observaciones_prestamo')";

		if($documento_solicitado=="expediente_completo"){
			$documento_solicitado1="El expediente completo $codigo_documento ";
		}else{
			$documento_solicitado1="El documento individual $codigo_documento ";
		}

		$comentario 			= $observaciones_prestamo;
		$creado 				= "Documento $documento_solicitado1 ha sido entregado en físico al usuario $login_solicitante por el usuario $login_prestamista";
		$radicado 				= $numero_radicado;			// Variables para insertar historico		
		$transaccion 			= "prestamo_documento";	// Variables para insertar historico
		$transaccion_historico 	= "Préstamo de documento";	// Variables para insertar historico	

		$query_actualizar="update prestamos set fecha_prestamo='$timestamp' where id='$id'";						
		if(pg_query($conectado,$query_actualizar)){
			if(pg_query($conectado,$query_prestar_documento)){
				require_once("../login/inserta_historico.php");
			}else{
				echo "<script> alert('Ocurrió un error al realizar el préstamo, por favor revisar e intentar nuevamente.')</script>";
			}
		}else{
			echo "<script> alert('Ocurrió un error al actualizar la información del préstamo, por favor revisar e intentar nuevamente.')</script>";
		}
	}

?>