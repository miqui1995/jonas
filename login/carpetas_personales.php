<link rel="shortcut icon" href="imagenes/logo3.png">
<?php 
	if(!isset($_SESSION)){
		session_start();
	}
	require_once("conexion2.php");

	$id_usuario = $_SESSION['id_usuario'];
	$login 		= $_SESSION['login'];

/* Isset ajax (nombre_carpeta) - Formulario de Agregar Carpeta Personal */	
	$nombre_carpeta ='';
	if(isset($_POST['nombre_carpeta'])){
		$nombre_carpeta = $_POST['nombre_carpeta'];
		
		$query_cantidad_carpeta_per="select count(*) from carpetas_personales where nombre_carpeta_personal ilike '%$nombre_carpeta%' and id_usuario='$id_usuario' ";
		
		$fila_cantidad = pg_query($conectado,$query_cantidad_carpeta_per); // La variable "$conectado" la hereda desde conexion2.php
		$linea_cantidad = pg_fetch_array($fila_cantidad);
		
		if($linea_cantidad==false){  // Si no existe en la tabla consecutivos la secuencia del tipo de radicado - dependencia no permita continuar.
			echo "Error en la consulta a carpetas_personales 1. Contacte al administrador del sistema.";
		}else{
			$cantidad=$linea_cantidad[0];
			
			if($cantidad==0){
				$query_cantidad_carpeta_per2="select count(*) from carpetas_personales";
				$fila_cantidad2 = pg_query($conectado,$query_cantidad_carpeta_per2); // La variable "$conectado" la hereda desde conexion2.php
				$linea_cantidad2 = pg_fetch_array($fila_cantidad2);
				$cantidad_total1=$linea_cantidad2[0];
				$cantidad_total=$cantidad_total1+1;

				$query_crear_carpeta="insert into carpetas_personales (id, nombre_carpeta_personal, id_usuario, activo, fecha_creacion_carpeta_per) values('$cantidad_total', '$nombre_carpeta', '$id_usuario', 'SI', current_timestamp)";
				if(pg_query($conectado,$query_crear_carpeta)){
					echo "$nombre_carpeta";
				}else{
					echo "error_insert";
				}
			}else{
				echo "carpeta_ya_existe";
			}
		}
	}
/* Fin isset ajax (nombre_carpeta) - Formulario de Agregar Carpeta Personal */	

/* Isset ajax (buscador_nombre_carpetas_personales) - Formulario de Inicio Carpetas Personales */	
		$carpetas_per ='';
		if(isset($_POST['carpetas_per'])){
			$carpetas_per = $_POST['carpetas_per'];

			$consulta_cantidad_carpetas_personales="select * from carpetas_personales where id_usuario ='$id_usuario' and activo='SI' order by nombre_carpeta_personal";
			$fila_cantidad_carpetas_personales = pg_query($conectado,$consulta_cantidad_carpetas_personales);
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_carpetas_personales= pg_num_rows($fila_cantidad_carpetas_personales);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
			if($registros_carpetas_personales>0){	
				$contador_total=0; // Se inicia contador total
				for ($i=0;$i<$registros_carpetas_personales;$i++){
					$linea_carpetas_personales = pg_fetch_array($fila_cantidad_carpetas_personales);

					$nombre_carpeta_personal = $linea_carpetas_personales['nombre_carpeta_personal'];
					
					if($nombre_carpeta_personal=='Inventario'){
						$id='Inventario';
					}else{
						$id = $linea_carpetas_personales['id'];	
					}
				/* Inicio contador carpeta personal una por una */	
					$contador_carpeta_individual="select count(distinct(numero_radicado)) from radicado where codigo_carpeta1->'$login'->>'codigo_carpeta_personal'='$id' and asunto is not null and leido ilike '%$login%'"; // Se pone asunto is not null para que no genere conflicto con los radicados que no han pasado modificacion_rapida	
					$fila_contador_carpeta_individual  = pg_query($conectado,$contador_carpeta_individual);
					$linea_contador_carpeta_individual = pg_fetch_array($fila_contador_carpeta_individual);
					$contador=$linea_contador_carpeta_individual[0];
					$contador_total+=$contador;
				/* Fin contador carpeta personal una por una */		
					
					echo "<li id='carpeta_$id' style='padding:10px;' onmousedown=\"javascript:detectar_boton(event,'$id','$nombre_carpeta_personal')\" title='Radicados NO LEIDOS en la carpeta $nombre_carpeta_personal'>
						$nombre_carpeta_personal ($contador)
					</li>";
				}

				echo "<script>$('#contador_total_carpetas_personales').html('($contador_total)')</script>";
			}else{
				echo "<li style='padding:10px;'> No ha creado todavía carpetas personales</li>";
			}
		}
/* Fin isset ajax (buscador_codigo_dependencias) consulta sugerencias - codigo dependencia -  Formulario de Agregar Dependencia */
/* Isset ajax (nombre_carpeta) - Formulario de Modificar Carpeta Personal */	
	$nombre_carpeta_mod ='';
	if(isset($_POST['nombre_carpeta_mod'])){
		$nombre_carpeta_mod = $_POST['nombre_carpeta_mod'];
		$id = $_POST['id'];
		
		$query_cantidad_carpeta_per="select count(*) from carpetas_personales where nombre_carpeta_personal ilike '%$nombre_carpeta_mod%' and id_usuario='$id_usuario' ";
		
		$fila_cantidad = pg_query($conectado,$query_cantidad_carpeta_per); // La variable "$conectado" la hereda desde conexion2.php
		$linea_cantidad = pg_fetch_array($fila_cantidad);
		
		if($linea_cantidad==false){  // Si no existe en la tabla consecutivos la secuencia del tipo de radicado - dependencia no permita continuar.
			echo "Error en la consulta a carpetas_personales 2. Contacte al administrador del sistema.";
		}else{
			$cantidad=$linea_cantidad[0];
			
			if($cantidad==0){
				$query_modificar_carpeta_per="update carpetas_personales set nombre_carpeta_personal ='$nombre_carpeta_mod' where id='$id'";
		
				if(pg_query($conectado,$query_modificar_carpeta_per)){
					echo "$nombre_carpeta_mod";
				}else{
					echo "error_modificar_carpeta";
				}
			}else{
				echo "carpeta_ya_existe";
			}
		}
	}
/* Fin isset ajax (nombre_carpeta) - Formulario de Modificar Carpeta Personal */	
?>