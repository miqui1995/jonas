<!-- Este archivo recibe los formularios de index_municipios.php y realiza las consultas dependiendo del formulario -->
<?php 
	if(!isset($_SESSION)){
		session_start();
	}
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Query de Municipios</title>
</head>
<body>
<?php 
	//var_dump($usuario);
	$usuario=$_SESSION['login'];

	require_once('../login/conexion2.php');
	
/*Aqui defino la fecha de la transaccion*/
	$fecha_modificacion = date('Y-m-d H:i:s');
	if(isset($_POST['tipo_formulario'])){
		$tipo_formulario=$_POST['tipo_formulario']; // Recibe tipo de formulario desde la que invoca el archivo 

		if($tipo_formulario=='crear_municipio'){
		/* Script para extraer maximo id de la tabla municipios para crear correctamente. */	
			$query_max_muni="select max(id) from municipios";

			$fila_dependencia = pg_query($conectado,$query_max_muni);
			$linea = pg_fetch_array($fila_dependencia);

			$max_muni = $linea[0];
			$max_muni2= $max_muni+1;

			$continente = strtoupper($_POST['continente']);	// Paso a mayusculas el nombre del continente
			switch ($continente){
				case 'AMERICA':
					$id_continente=1;
					break;
				case 'ASIA':
					$id_continente=2;
					break;	
				case 'AFRICA':
					$id_continente=3;
					break;
				case 'EUROPA':
					$id_continente=4;
					break;	
				case 'OCEANIA':
					$id_continente=5;
					break;	
				default:
					$id_continente=1;
					break;
			}
			$continente=$_POST['continente'];
			$pais=$_POST['pais'];
			$departamento=$_POST['departamento'];
			$municipio=$_POST['municipio'];

			$query ="INSERT INTO municipios (id, id_continente, nombre_continente, nombre_pais,	nombre_departamento, nombre_municipio, fecha_creacion, creador_municipio) VALUES ($max_muni2, $id_continente, '$continente', '$pais','$departamento','$municipio','$fecha_modificacion','$usuario');";

		}else if($tipo_formulario=='modificar_municipio'){
			$continente = strtoupper($_POST['mod_continente']);
			switch ($continente) {
				case 'AMERICA':
					$id_continente=1;
					break;
				case 'ASIA':
					$id_continente=2;
					break;	
				case 'AFRICA':
					$id_continente=3;
					break;
				case 'EUROPA':
					$id_continente=4;
					break;	
				case 'OCEANIA':
					$id_continente=5;
					break;	
				default:
					$id_continente=1;
					break;
			}

			$id_municipio=$_POST['id_municipio'];

			$mod_continente=$_POST['mod_continente'];
			$mod_pais=$_POST['mod_pais'];
			$mod_departamento=$_POST['mod_departamento'];
			$mod_municipio=$_POST['mod_municipio'];
			$municipio=$mod_municipio; // Variable para enviar el nombre del municipio a auditoria

			$query="update municipios set nombre_continente='$mod_continente', nombre_pais='$mod_pais', nombre_departamento='$mod_departamento', nombre_municipio='$mod_municipio', creador_municipio='$usuario', fecha_modificacion='$fecha_modificacion' where id='$id_municipio';";
		}else{
			echo "Error. No viene de un formulario definido.";
		}

		if(pg_query($conectado,$query)){
			echo "<script> 
					auditoria('$tipo_formulario','$municipio');	
				</script>";
		}else{
			echo "<script> Ocurrió un error al realizar la consulta, por favor revisa e intenta nuevamente.</script>";
		}

	}else{
		echo "Error. No viene de un formulario definido.";
	}		
?>
</body>
</html>