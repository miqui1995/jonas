<!-- Este archivo recibe los formularios de index_ubicacion_topografica.php y realiza las consultas dependiendo del formulario -->
<?php 
if(!isset($_SESSION)){
	session_start();
}
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Query de Niveles</title>
	<script type="text/javascript" src="include/js/jquery.js"></script> 
	<script type="text/javascript" src="include/js/funciones_ubicacion_topografica.js"></script>
	<script src="include/js/sweetalert2.js"></script>
	<link rel="stylesheet" type="text/css" href="include/css/sweetalert2.css">
</head>
<body>
<?php   	
//	var_dump($_SESSION);
	$dependencia 	= $_SESSION['dependencia'];
	$usuario 		= $_SESSION['login'];

	$timestamp 	= date('Y-m-d H:i:s');		// Genera la fecha de transaccion para historico eventos

	require_once('../login/conexion2.php');
	
	if(isset($_POST['tipo_formulario'])){ // Desde index_ubicacion_topografica.php
		$tipo_formulario=$_POST['tipo_formulario'];

		if ($tipo_formulario=='crear_nivel') { // Valor desde index_ubicacion_topografica.php

			$query_max_nivel="select max(id_ubicacion) from ubicacion_topografica";

			$fila_nivel = pg_query($conectado,$query_max_nivel);
			$linea 		= pg_fetch_array($fila_nivel);

			$max_nivel 	= $linea[0];
			$max_nivel2 = $max_nivel+1;

			$nombre_nivel 	= $_POST['nombre_nivel'];
			$nivel_padre 	= $_POST['nivel_padre'];
			$activa 		= "SI"; // Si estoy creando el nivel, por defecto debe estar activa. No tiene sentido crear un nivel inactivo.

			$query_insertar_nivel = "insert into ubicacion_topografica(id_ubicacion,nombre_nivel, nivel_padre, activa, creador_nivel, fecha_modificacion, dependencia_ubicacion_topografica)
				VALUES ('$max_nivel2','$nombre_nivel','$nivel_padre','$activa','$usuario','$timestamp','$dependencia')";

			if(pg_query($conectado,$query_insertar_nivel)){
				echo "<script>
						auditoria(\"$tipo_formulario\",\"$nombre_nivel\",\"null\")
					</script>";
			}else{
				echo "<script> Ocurrió un error al realizar la creación del nivel, por favor revisa e intenta nuevamente.</script>";
			}

		}else if($tipo_formulario=='modificar_nivel'){ // desde index_ubicacion_topografica.php

			$id_ubicacion=$_POST['id_ubicacion'];	
			$mod_nombre_nivel=$_POST['mod_nombre_nivel'];
			$mod_nivel_padre=$_POST['mod_nivel_padre'];
			$mod_activa=$_POST['mod_activa'];
			$antiguo_nombre_nivel=$_POST['antiguo_nombre_nivel'];

			$query_modificar_nivel="UPDATE ubicacion_topografica set nombre_nivel='$mod_nombre_nivel', nivel_padre='$mod_nivel_padre', activa='$mod_activa', creador_nivel='$usuario', fecha_modificacion=current_timestamp where id_ubicacion=$id_ubicacion";
			$query_modificar_niveles_padre="UPDATE ubicacion_topografica set nivel_padre='$mod_nombre_nivel', creador_nivel='$usuario', fecha_modificacion=current_timestamp where nivel_padre='$antiguo_nombre_nivel'"; // Esta query es para modificar todos los niveles que tengan el nivel_padre nombre modificado en esta accion.

			if(pg_query($conectado,$query_modificar_nivel)){

				if(pg_query($conectado,$query_modificar_niveles_padre)){
					echo "<script> 
						auditoria(\"$tipo_formulario\",\"$mod_nombre_nivel\",\"null\");	
					</script>";
				}else{
					echo "<script> No se pudo actualizar el nivel padre. Por favor revisa e intenta nuevamente.</script>";
				}
			}else{
				echo "<script> Ocurrió un error al realizar la modificación, por favor revisa e intenta nuevamente.</script>";
			}
		}else if($tipo_formulario=='sacar_radicado_expediente'){ // desde incluye_cajas.php

			$radicado=$_POST['radicado'];	
			$expediente=$_POST['expediente'];

			$query_sacar_radicado="UPDATE radicado set id_expediente=null where numero_radicado='$radicado'";
			$transaccion="Sacar radicado de expediente"; // Variable para historico
			$comentario="Sacado del expediente $expediente por el usuario $usuario"; // Variable para historico
			$creado="$expediente el radicado $radicado"; // Variable para auditoria

			if(pg_query($conectado,$query_sacar_radicado)){					
				$query_historico="insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$radicado', '$usuario', '$transaccion', '$comentario', '$timestamp')";
				if(pg_query($conectado,$query_historico)){
					echo "<script>
						auditoria(\"$tipo_formulario\",\"$creado\",\"$expediente\");
					</script>";		
				}
			}else{
				echo "<script> Ocurrió un error al realizar la acción, por favor revisa e intenta nuevamente.</script>";
			}
		}else if($tipo_formulario=='sacar_expediente_de_caja'){ // desde incluye_cajas.php

			$caja=$_POST['caja'];	
			$expediente=$_POST['expediente'];

			$query_sacar_expediente="UPDATE expedientes set codigo_ubicacion_topografica=null where id_expediente='$expediente'";
			$transaccion="Sacar expediente de caja"; // Variable para historico
			$comentario="Sacado de la caja $caja por el usuario $usuario"; // Variable para historico
			$creado="$caja el expediente $expediente"; // Variable para auditoria

			if(pg_query($conectado,$query_sacar_expediente)){					
				$query_historico="insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$expediente', '$usuario', '$transaccion', '$comentario', '$timestamp')";
				if(pg_query($conectado,$query_historico)){
					echo "<script>
						auditoria(\"$tipo_formulario\",\"$creado\",\"$caja\");
					</script>";		
				}
			}else{
				echo "<script> Ocurrió un error al realizar la acción, por favor revisa e intenta nuevamente.</script>";
			}
		}else if($tipo_formulario=='agregar_expediente_de_caja'){ // desde incluye_cajas.php

			$id_nivel=$_POST['id_nivel'];	
			$nombre_nivel=$_POST['nombre_nivel'];	
			$expediente=$_POST['expediente'];

			$query_sacar_expediente="UPDATE expedientes set codigo_ubicacion_topografica=$id_nivel where id_expediente='$expediente'";
			$transaccion="Incluir expediente en caja"; // Variable para historico
			$comentario="Incluido en caja $nombre_nivel por el usuario $usuario"; // Variable para historico
			$creado="$nombre_nivel el expediente $expediente"; // Variable para auditoria

			$query_actualizar_caja_archivo_central="update inventario set numero_caja_archivo_central='$nombre_nivel' where expediente_jonas='$expediente'";

			if(pg_query($conectado,$query_sacar_expediente)){	
				if(pg_query($conectado,$query_actualizar_caja_archivo_central)){
					$query_historico="insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$expediente', '$usuario', '$transaccion', '$comentario', '$timestamp')";
					if(pg_query($conectado,$query_historico)){
						echo "<script>
							auditoria(\"$tipo_formulario\",\"$creado\",\"$id_nivel\");
						</script>";		
					}
				}else{
				echo "<script> Ocurrió un error al realizar la acción 1, por favor revisa e intenta nuevamente.</script>";
				}				
			}else{
				echo "<script> Ocurrió un error al realizar la acción 2, por favor revisa e intenta nuevamente.</script>";
			}
		}else if($tipo_formulario=='mover_expediente_de_caja'){ // desde incluye_cajas.php

			$id_nivel=$_POST['id_nivel'];	
			$nombre_nivel=$_POST['nombre_nivel'];	
			$expediente=$_POST['expediente'];

			$query_sacar_expediente="UPDATE expedientes set codigo_ubicacion_topografica=$id_nivel where id_expediente='$expediente'";
			$transaccion="Mover expediente a caja"; // Variable para historico
			$comentario="Movido a caja $nombre_nivel por el usuario $usuario"; // Variable para historico
			$creado="$nombre_nivel el expediente $expediente"; // Variable para auditoria

			if(pg_query($conectado,$query_sacar_expediente)){					
				$query_historico="insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$expediente', '$usuario', '$transaccion', '$comentario', '$timestamp')";
				if(pg_query($conectado,$query_historico)){
					$query_actualiza_inventario="update inventario set numero_caja_archivo_central='$nombre_nivel' where expediente_jonas='$expediente'";
					if(pg_query($conectado,$query_actualiza_inventario)){
						echo "<script>
							auditoria(\"$tipo_formulario\",\"$creado\",\"$id_nivel\");
						</script>";		
					}else{
						echo "<script> Ocurrió un error al actualizar el inventario, por favor revisa e intenta nuevamente.</script>";
					}
				}
			}else{
				echo "<script> Ocurrió un error al realizar la acción, por favor revisa e intenta nuevamente.</script>";
			}
		}else if($tipo_formulario=='agregar_radicado_exp'){ // desde incluye_cajas.php

			$radicado=$_POST['radicado'];	
			$expediente=$_POST['expediente'];

			$query_sacar_expediente="UPDATE radicado set id_expediente='$expediente' where numero_radicado='$radicado'";
			$transaccion="Incluir radicado en expediente"; // Variable para historico
			$comentario="Incluido en expediente $expediente por el usuario $usuario"; // Variable para historico
			$creado="$radicado al expediente $expediente"; // Variable para auditoria

			if(pg_query($conectado,$query_sacar_expediente)){					
				$query_historico="insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$radicado', '$usuario', '$transaccion', '$comentario', '$timestamp')";
				if(pg_query($conectado,$query_historico)){
					echo "<script>
						auditoria(\"$tipo_formulario\",\"$creado\",\"$expediente\");
					</script>";		
				}
			}else{
				echo "<script> Ocurrió un error al realizar la acción, por favor revisa e intenta nuevamente.</script>";
			}
		}else if($tipo_formulario=='mover_radicado_exp'){ // desde incluye_cajas.php

			$radicado=$_POST['radicado'];	
			$expediente=$_POST['expediente'];

			$query_sacar_expediente="UPDATE radicado set id_expediente='$expediente' where numero_radicado='$radicado'";
			$transaccion="Mover radicado a expediente"; // Variable para historico
			$comentario="Movido a expediente $expediente por el usuario $usuario"; // Variable para historico
			$creado="$radicado al expediente $expediente"; // Variable para auditoria

			if(pg_query($conectado,$query_sacar_expediente)){					
				$query_historico="insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$radicado', '$usuario', '$transaccion', '$comentario', '$timestamp')";
				if(pg_query($conectado,$query_historico)){
					echo "<script>
						auditoria(\"$tipo_formulario\",\"$creado\",\"$expediente\");
					</script>";		
				}
			}else{
				echo "<script> Ocurrió un error al realizar la acción, por favor revisa e intenta nuevamente.</script>";
			}
		}else{
			echo "Error. No viene de un formulario definido.";
		}
	}
?>
</body>
</html>