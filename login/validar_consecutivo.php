<?php 
if(!isset($_SESSION)){
	session_start();
}
require_once('conexion2.php');
/* Este archivo es incluido por "require_once" y las variables utilizadas son usadas desde el archivo que lo incluye. 
Las variables que devuelve se usan también en el archivo que lo incluye. */

/* Consulto la dependencia del usuario que está radicando. */

	if(isset($codigo_dependencia_destino)){
		$dependencia_usuario 	= $codigo_dependencia_destino;
	}else{
		$dependencia_usuario 	= $_SESSION['dependencia']; 	// Dependencia del usuario que está radicando
	}

	if(isset($login_usuario_actual)){  // Variable que se recibe desde los WebServices 
		$login_usuario 	= $login_usuario_actual;
	}else{
		$login_usuario 	= $_SESSION['login']; // Usuario que hace el radicado
	}

	if(isset($nivel_seguridad)){  // Variable que se recibe desde los WebServices 
		$nivel 	= $nivel_seguridad;
	}else{
		$nivel 			= $_SESSION['nivel']; // Nivel del usuario que realiza la transaccion
	}


//	var_dump($_SESSION);

/* La variable $tipo_radicado la hereda desde el archivo que incluye este archivo. */

	$consulta_consecutivo = "select * from consecutivos WHERE tipo_radicado='$tipo_radicado' and codigo_dependencia='$dependencia_usuario'"; // Valida si existe la secuencia del tipo de radicado - dependencia del usuario que radica
	$fila_consecutivo = pg_query($conectado,$consulta_consecutivo); // La variable "$conectado" la hereda desde el require ../login/conexion2.php que tiene el archivo que hace el include de este archivo.
	$linea_consecutivo = pg_fetch_array($fila_consecutivo);
	
	if($linea_consecutivo==false){  // Si no existe en la tabla consecutivos la secuencia del tipo de radicado - dependencia no permita continuar.
		echo "<div class='errores'>No se encuentra consecutivo configurado para ésta dependencia.</div>";
		echo "<script>
			Swal.fire({	
					position 			: 'top-end',
				    showConfirmButton 	: true,
				    timer 				: 5000,	
				    title 				: \"$consulta_consecutivo\",
				    text 				: 'No se puede radicar porque el consecutivo de esta dependencia no existe. Comuníquese con el administrador del sistema para crearlo.',
				    type 				: 'error'
				});
		</script>"; 
		$radicado = "";
	}else{ // Este es el caso cuando coincide la dependencia y tipo de radicado
		$dependencia_consecutivo_padre = $linea_consecutivo['dependencia_consecutivo_padre'];

		$query_verifica_secuencia_padre="select * from consecutivos where tipo_radicado='$tipo_radicado' and codigo_dependencia='$dependencia_consecutivo_padre'";
		$fila_verifica_secuencia_padre=pg_query($conectado, $query_verifica_secuencia_padre);
		$linea_verifica_secuencia_padre=pg_fetch_array($fila_verifica_secuencia_padre);

		if($linea_verifica_secuencia_padre==false){ // Esto sucede cuando no trae resultados es decir, cuando no hay un consecutivo en la dependencia padre.
			$query_secuencia="create sequence SECUENCIA_$dependencia_consecutivo_padre_$tipo_radicado
					  start with 1 increment by 1 maxvalue 999999 minvalue 1";
			pg_query($conectado,$query_secuencia);	

			$query_secuencia_padre="insert into consecutivos (year, codigo_dependencia, tipo_radicado, dependencia_consecutivo_padre) values('$year', '$dependencia_consecutivo_padre', '$tipo_radicado', '$dependencia_consecutivo_padre')";	
    
    		pg_query($conectado,$query_secuencia_padre);

			$query_update_sequence="update consecutivos set year='$year' where dependencia_consecutivo_padre='$dependencia_consecutivo_padre' and tipo_radicado='$tipo_radicado'";
			pg_query($conectado,$query_update_sequence);

			$year_padre=$year; // Asigno año actual para secuencia ya que la acabo de crear.
		}else{
			$year_padre=$linea_verifica_secuencia_padre['year']; // Verifico año para secuencia si existe
		}

	//	echo "<script>alert($linea_verifica_secuencia_padre)</script>";

		if($year_padre==$year){	
			$secuencia1="secuencia_".$dependencia_consecutivo_padre."_".strtolower($tipo_radicado);
			$consecutivo=pg_query($conectado,"select nextval('$secuencia1')");
			$consecutivo2 = pg_fetch_array($consecutivo);
			$consecutivo3 = $consecutivo2[0];
		}else{
			$query_alter_sequence="alter sequence secuencia_".$dependencia_consecutivo_padre."_".$tipo_radicado." restart 1";
			$query_update_sequence="update consecutivos set year='$year' where dependencia_consecutivo_padre='$dependencia_consecutivo_padre' and tipo_radicado='$tipo_radicado'";
			if(pg_query($conectado,$query_alter_sequence)){	// Si se reinicia el consecutivo
				echo "$query_update_sequence";
				echo "Se reinicia el consecutivo porque año es nuevo.";
				if(pg_query($conectado,$query_update_sequence)){ 	// Si se actualiza el año en la secuencia
					$secuencia="secuencia_".$dependencia_consecutivo_padre."_".$tipo_radicado;
					$consecutivo=pg_query($conectado,"select nextval('$secuencia')");
					$consecutivo2 = pg_fetch_array($consecutivo);
					$consecutivo3 = $consecutivo2[0];
				}else{
					echo "No se pudo actualizar la secuencia. Comuníquese con el administrador del sistema";
				}
			}else{
				echo "No se pudo reiniciar el consecutivo. Comuníquese con el administrador del sistema";
			}
		}	

		if(isset($codigo_entidad_ws)){
			$codigo_entidad 	= $codigo_entidad_ws;
		}else{
			$codigo_entidad   	= $_SESSION['codigo_entidad'];	// Codigo de entidad para interoperabilidad entre Jonas
		}


		$consecutivo4= str_pad($consecutivo3, 7, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 7=longitud
		$radicado=$year.$codigo_entidad.$dependencia_usuario.$consecutivo4.$tipo_radicado; // Arma el numero de radicado


	// Genera id datos_origen_radicado para guardar en radicar.php  
		$query_max_datos_origen=" select max(codigo_datos_origen_radicado) from datos_origen_radicado";

		$fila_datos_origen = pg_query($conectado,$query_max_datos_origen);
		$linea_datos_origen = pg_fetch_array($fila_datos_origen);

		$max_datos_origen = $linea_datos_origen[0];
		$max_datos_origen2= $max_datos_origen+1;
	}	// Fin caso dependencia y tipo de radicado
?>