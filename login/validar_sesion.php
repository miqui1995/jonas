<html>
<head>
	<meta charset="UTF-8">
	<title>Validar Sesion</title>
	<script type="text/javascript" src="include/js/jquery.js"></script>
</head>
<body>
	<script>
		function enviar(){
			alert("La sesión ha sido cerrada por inactividad. Ingrese nuevamente usuario y contraseña.")
			window.location.href='../index.php';
		}
	</script>
<?php 
/* Llamo al archivo para concectar con la base de datos */
	require_once("../login/conexion2.php");

	$usuario=$_POST['user'];
	$pasword=$_POST['paswd'];

	$query="select * from usuarios where login ilike '$usuario' and pass = md5('$pasword')";
	$result=pg_query($query);

	if($res=pg_fetch_array($result)){
/* Convierto a formato operable variables de fecha para validar sesion */
	/* Esta variable la tomo desde la base de datos en la tabla usuarios campo "fecha_sesion" */
		$ultimoingreso = strtotime($res['fecha_sesion']);
		$ultimoingreso2= $res['fecha_sesion'];
	/* Esta variable la tomo desde la hora actual del sistema */
		$fecha_ya= strtotime(date("Y-m-d h:i:s.u"));

		$fecha_valida= $ultimoingreso - $fecha_ya ;
		$fecha2 = date("Y-m-d h:i:s.u");

		echo "ultimo es $ultimoingreso2<br>$ultimoingreso <br><br> y fecha_ya es <br>$fecha2<br>$fecha_ya<br><br> $fecha_valida<br><br>$fecha2";		 
	}else{
		echo "<script>
				alert('El usuario ingresado y la contraseña no coinciden con los datos registrados en la base de datos. Intente Nuevamente.');
				/* enviar();*/
			</script>";
		
	}
?>
</body>