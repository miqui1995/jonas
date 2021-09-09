<?php 
	require_once("conexion2.php");

	$id_usuario = $_POST['id_usuario'];
	$new_pass = $_POST['pass'];

	$query="update usuarios set pass=trim(md5('$new_pass')), usuario_nuevo='NO' where id_usuario='$id_usuario'";

	//$insertar = pg_query($query) or die("No se realizó la actualizacion");
	if (pg_query($conectado,$query)) {
		echo "Se ha actualizado la contraseña correctamente.";
	}else{
		echo "No se ha actualizado la contraseña por favor revisar las variables.";
	}	
?>
