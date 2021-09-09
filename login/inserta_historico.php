<?php 
/* Este archivo es invocado por require_once, por lo tanto las variables son heredadas desde el archivo que las invoca y no es necesario hacer un "return" */
if(!isset($_SESSION)){
	session_start();
}
	$usuario 	= $_SESSION['login']; 	// Genera Usuario 
	$timestamp  = date('Y-m-d H:i:s');	// Genera la fecha de transaccion

	$query_historico = "insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$radicado', '$usuario', '$transaccion_historico', '$comentario', '$timestamp')";

	if(pg_query($conectado,$query_historico)){
		echo "<script> 
			$('#contenido').css({'z-index':'1'});	// Modifico estilo para sobreponer a ventana modal
			auditoria_general('$transaccion','$creado');
		</script>";
	}else{
		echo "<script>
			alert('No se ha podido generar historico sobre la transaccion realizada. Por favor comuníquese con el administrador.');
			volver();
		</script>";
	}
 ?>