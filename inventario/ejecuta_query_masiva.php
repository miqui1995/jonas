<?php 
	require_once('../login/conexion2.php');

	$query_inventario=$_POST['query_inventario'];

	if($query_inventario=='SI'){
		$file = fopen("query_radicado.txt", "r") or exit("No se puede abrir el archivo!");
		// Imprime una linea del archivo mientras encuentra la linea final. 
		while(!feof($file))
		{
		    $query1=trim(fgets($file));
		    if(pg_query($conectado,$query1)){
				unlink("query_radicado.txt");
		    	echo "<script> 
						auditoria('masiva_inventario','masiva');	
					</script>";	
		    }else{
		    	echo "<script>
					alert('No se pudo crear / actualizar el inventario');
					volver();
				</script>";
		    }
		}
		fclose($file);
	}else{
    echo "<script>
			alert('No viene de un formulario definido.');
			volver();
		</script>";
	}
?>