<?php 
	require_once('../login/conexion2.php');

	$tipo_consulta=$_POST['tipo_consulta'];

	switch ($tipo_consulta) {
		case 'dias_solicitados':
			$dias_prestamo = $_POST['dias_prestamo']; 
			$fecha=date('Y-m-j');
			$estimado_prestamo=strtotime("+$dias_prestamo day", strtotime($fecha));
			$fecha_limite_estimada= date('Y-m-j',$estimado_prestamo);
		
		/*Aqui defino la fecha para mostrar en formato "Jueves 05 de Mayo de 2016" */
			include "../include/genera_fecha.php";
			$fecha_e = $b->traducefecha($fecha_limite_estimada); // Traduce fecha formato "Jueves 05 de Mayo de 2016"

			echo "La fecha límite para la devolución sería el $fecha_e";
			break;
		case 'verificar_usuario':
			$contr=$_POST['contr'];
			$solicitante=$_POST['solicitante'];

			$isql ="select * from usuarios where login = trim(upper('$solicitante')) and pass = md5('$contr')";
			$fila_resultado = pg_query($conectado,$isql);
			$linea_resultado = pg_fetch_array($fila_resultado);
			$result=$linea_resultado[0];
			
		    if($result==""){
		        echo "false";
		    }else{
		    	echo "true";
		    }
			
			break;	
	}

 ?>