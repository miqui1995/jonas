<?php 
if(!isset($_SESSION)){
	session_start();
}

echo date("d-m-Y H:i:s")."<br>"; 

$login = $_SESSION['login'];

$directorio_temporal 	= "../bodega_pdf/tmp/TMP_$login";
$accion1 				= $_GET['accion']; 

if (file_exists($directorio_temporal)){	
	$directorio = opendir($directorio_temporal); //ruta de carpeta compartida con usuario

	$archivos = "";

	while ($archivo = readdir($directorio)) // Se obtiene un archivo y luego otro sucesivamente
	{
	    if (!is_dir($archivo)) 		// Se verifica si es o no un directorio
	    {	 
	    	$archivos.= "<div class='botones2' style='float:left; max-width:250px;'  onclick='verificar_pdf_por_cargar(\"$archivo\",\"$accion1\")'>$archivo</div>";
	    }
	}
	closedir($directorio);

	
	$directorio2 = opendir($directorio_temporal); //ruta de carpeta compartida con usuario

	$cadena = "";
	while(false!==($entrada = readdir($directorio2))){
		if($entrada=="." or $entrada==".."){
			$cadena.="1";
		}else{
			$cadena.="$entrada";
		}
	}
	closedir($directorio2);

	if($cadena=="11" or $cadena==""){
		echo "<h3>No existen documentos escaneados en la carpeta asíncrona</h3>";
	}else{
		echo "<h3>Los documentos escaneados listos para subir son los siguientes:</h3> <br>$archivos";
	}
}else{
	echo "<center><h2>No se ha creado el acceso a la carpeta asíncrona del escaner.</h2><h4>Comuníquese con el administrador del sistema</h4></center>";
}
?>