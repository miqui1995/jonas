<?php 
	require_once('../login/validar_inactividad.php');

	if(isset($_POST['tipo_transaccion'])){
	/* Desde aqui se definen las variables que se usan en este archivo */	

		$codigo_dependencia			 	= $_SESSION['dependencia'];
		$codigo_entidad					= $_SESSION['codigo_entidad'];    // Codigo de entidad para interoperabilidad entre Jonas
		$id_usuario						= $_SESSION['id_usuario']; 
		$login_usuario					= $_SESSION['login'];

		$leido 							= $login_usuario.",";

		$timestamp 	= date('Y-m-d H:i:s');
	    $year 		= date("Y"); // Se obtiene el año en formato 4 digitos

		/* Define secuencia inicial para radicado */

	    $consecutivo_inicial3			= pg_query($conectado,"select nextval('secuencia_inventario')");
	    $consecutivo_inicial2 			= pg_fetch_array($consecutivo_inicial3);
	    $consecutivo_inicial 			= $consecutivo_inicial2[0];
		/* Fin define secuencia inicial para radicado */
		/* Define numero_consecutivo del inventario */ 
		$query_consecutivo_inventario 	= "select max(numero_consecutivo) from inventario";
	    $consecutivo_inventario1 		= pg_query($conectado,$query_consecutivo_inventario);
		$consecutivo_inventario2 		= pg_fetch_array($consecutivo_inventario1);
	    $consecutivo_inventario 	 	= $consecutivo_inventario2[0]+1;
		/* Fin definicion numero_consecutivo del inventario */ 
	/* Hasta aqui se definen las variables que se usan en este archivo */
	    
/*** Inicio generar consecutivo completo para secuencia expediente_jonas ***/
	    $caja_paquete_tomo 				= $_POST['caja_paquete_tomo'];
	    $consecutivo_desde 				= $_POST['consecutivo_desde'];
	    $consecutivo_hasta 				= $_POST['consecutivo_hasta'];
	    $dependencia_expediente 		= $_POST['dependencia_expediente'];
	    $fecha_final 					= $_POST['fecha_final'];
	    $fecha_inicial 					= $_POST['fecha_inicial'];
	    $id_caja_archivo_central 		= $_POST['id_caja_archivo_central'];
	    $metadato_descriptor 			= $_POST['metadato_descriptor'];
	    $nombre_expediente 				= strtoupper($_POST['nombre_expediente']);
	    $numero_caja_archivo_central 	= $_POST['numero_caja_archivo_central'];
	    $numero_caja_paquete 			= $_POST['numero_caja_paquete'];
	    $numero_carpeta 				= $_POST['numero_carpeta'];
	    $observaciones 					= $_POST['observaciones'];
	    $serie_expediente1 				= $_POST['serie_expediente'];
	    $subserie_expediente1 			= $_POST['subserie_expediente'];
	    $total_folios 					= $_POST['total_folios'];

    /* Inicio generar consecutivo tabla expediente */   
	    $query_max_id_expediente = "select max(id) from expedientes";

	    $fila_max_id_expediente = pg_query($conectado,$query_max_id_expediente);
	    $linea_id_expediente = pg_fetch_array($fila_max_id_expediente);

	    if($linea_id_expediente==false){
	        $max_id_expediente2 = "1";    // Inicia el consecutivo del expediente por año/serie/subserie
	    }else{
	        $max_id_expediente  = $linea_id_expediente[0];
	        $max_id_expediente2 = $max_id_expediente+1;  // Continúa con consecutivo por año/serie/subserie 
	    }
	/* Fin generar consecutivo tabla expediente */  
    /* Inicio generar consecutivo de expediente por dependencia/serie/subserie */
        $query_cantidad_expediente = "select count(*) from expedientes where year_expediente='$year' and dependencia_expediente='$dependencia_expediente' and serie='$serie_expediente1' and subserie='$subserie_expediente1'";

        $fila_cantidad_expediente  = pg_query($conectado,$query_cantidad_expediente);
        $linea_cantidad_expediente = pg_fetch_array($fila_cantidad_expediente);
    /* Fin generar consecutivo de expediente por dependencia/serie/subserie */

        $max_expediente2="";
        if($linea_cantidad_expediente=='0'){
            $max_expediente2="1";   // Inicia el consecutivo del expediente por año/serie/subserie
        }else{
            $max_expediente = $linea_cantidad_expediente[0];
            
            if ( !empty ( $dependencia_expediente ) ){   		 
                $position = $dependencia_expediente;
                if ( isset( $count[$position] ) ){
                    $count[$position]++;
                } else {
                    // Consulta la base de datos y trae la cantidad 
                    $countDB 			= $max_expediente+1;
                    $count[$position] 	= $countDB; 			// Inicializa el valor con lo que viene de la base de datos 
                }
            }
            $max_expediente2 = $count[$dependencia_expediente];   
        }

    /* Genera el numero de expediente */        
            $max_expediente3  = str_pad($max_expediente2, 7, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 7=longitud
            $expediente_jonas = "EXP".$year.$dependencia_expediente.$serie_expediente1.$subserie_expediente1.$max_expediente3;
            $expediente_jonas1 = $expediente_jonas.",";
    /* Fin genera el numero de expediente */        
/*** Fin generar consecutivo completo para secuencia expediente_jonas ***/
/*** Inicio generar consecutivo completo para secuencia radicado_jonas ***/
        /* Desde aqui genera la secuencia para el radicado_jonas */
            $query_verifica_secuencia_padre 	= "select * from consecutivos where tipo_radicado='1' and codigo_dependencia='$dependencia_expediente'"; // Termina en 2 porque son entradas
            $fila_verifica_secuencia_padre 		= pg_query($conectado, $query_verifica_secuencia_padre);
            $linea_verifica_secuencia_padre 	= pg_fetch_array($fila_verifica_secuencia_padre);
            $year_padre 						= $linea_verifica_secuencia_padre['year']; // Verifico año para secuencia si existe

            $codigo_inv = $_SESSION['caracteres_depend'];
            	switch ($codigo_inv) {
            		case '3':
            			$consecutivo_inv = 'INV';
            			break;
            		case '4':
            			$consecutivo_inv = 'INVE';
            			break;
            		case '5':
            			$consecutivo_inv = 'INVEN';
        				break;	           		
            	}

            if($year_padre==""){
                $query_alter_sequence 	= "alter sequence secuencia_inventario restart 1";
            	$query_update_sequence 	= "insert into consecutivos(year, codigo_dependencia, tipo_radicado, dependencia_consecutivo_padre) values('$year', '$consecutivo_inv', '1', '$consecutivo_inv');";

            	if(pg_query($conectado,$query_alter_sequence)){ // Si se inserta el consecutivo
                    echo "Se reinicia el consecutivo porque no existía secuencia_inventario.";
                    if(pg_query($conectado,$query_update_sequence)){    // Si se actualiza el año en la secuencia
                        // pg_query($conectado,"select nextval('secuencia_inventario')");
                        $consecutivo3 	= 1;
                    }else{
                        echo "No se pudo actualizar la secuencia1. Comuníquese con el administrador del sistema";
                    }
                }else{
                    echo "No se pudo crear el consecutivo de inventario. Comuníquese con el administrador del sistema";
                }
            }else{
	            if($year_padre==$year){ // Si el año para secuencia existe, toma siguiente valor de la secuencia
	                $consecutivo3 	= $consecutivo_inicial;
	            }else{ // Si el año no coincide, reinicia consecutivo de la secuencia e inicia en 1.
	                $query_alter_sequence 	= "alter sequence secuencia_inventario restart 1";
	                $query_update_sequence 	= "update consecutivos set year='$year' where dependencia_consecutivo_padre='$consecutivo_inv' and tipo_radicado='1'";
	                
	                if(pg_query($conectado,$query_alter_sequence)){ // Si se reinicia el consecutivo
	                    echo "Se reinicia el consecutivo porque año es nuevo.";
	                    
	                    if(pg_query($conectado,$query_update_sequence)){    // Si se actualiza el año en la secuencia
	                        // pg_query($conectado,"select nextval('secuencia_inventario')");
	                        $consecutivo3 	= 1;
	                    }else{
	                        echo "No se pudo actualizar la secuencia. Comuníquese con el administrador del sistema";
	                    }
	                }else{
	                    echo "No se pudo reiniciar el consecutivo. Comuníquese con el administrador del sistema";
	                }
	            }   
            }	
        /* Hasta aqui genera la secuencia para el radicado_jonas */

        /* Genera el radicado */
            $consecutivo4= str_pad($consecutivo3, 7, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 7=longitud
            $radicado=$year.$codigo_entidad.$consecutivo_inv.$consecutivo4."1"; // Arma el numero de radicado

        /* Hasta aqui genera el radicado */
/*** Fin generar consecutivo completo para secuencia radicado_jonas ***/
    /* Verifica si existe carpeta personal para inventario */    
            $consulta_carpeta_inventario="select * from carpetas_personales where id_usuario='$id_usuario' and nombre_carpeta_personal='Inventario'";
            $fila_cantidad_carpeta_inventario = pg_query($conectado,$consulta_carpeta_inventario);

    /* Calcula el numero de registros que genera la consulta anterior. */
            $registros_carpeta_inventario= pg_num_rows($fila_cantidad_carpeta_inventario);

            if($registros_carpeta_inventario!=0){  // Cuando si existe la carpeta personal 
                $linea_carpeta_inventario = pg_fetch_array($fila_cantidad_carpeta_inventario);
                $codigo_carpeta_inventario=$linea_carpeta_inventario['id'];
            }else{       // Cuando no existe la carpeta personal
                $query_cantidad_carpeta_per3 = "select count(*) from carpetas_personales";
                $fila_cantidad3  = pg_query($conectado,$query_cantidad_carpeta_per3); // La variable "$conectado" la hereda desde conexion2.php
                $linea_cantidad3 = pg_fetch_array($fila_cantidad3);
                $cantidad_total1 = $linea_cantidad3[0];
                $cantidad_total  = $cantidad_total1+1;

                $query_crear_carpeta="insert into carpetas_personales (id, nombre_carpeta_personal, id_usuario, activo, fecha_creacion_carpeta_per) values('$cantidad_total', 'Inventario', '$id_usuario', 'SI', current_timestamp)";
                if(pg_query($conectado,$query_crear_carpeta)){ // Si crea la carpeta personal 'Inventario'
                    $codigo_carpeta_inventario=$cantidad_total;
                }else{
                    echo "<script>No se ha creado la carpeta personal 'Inventario. Por favor comuníquese con el administrador del sistema.'</script>";
                }
            }
    /* Fin verifica si existe carpeta personal para inventario */  

    /* Se arma el json para codigo_carpeta1 de la tabla radicado */
			$codigo_carpeta1="'{\"$login_usuario\":{\"codigo_carpeta_personal\":\"Inventario\"}}'";
	/* Fin del armado del json para codigo_carpeta1 de la tabla radicado */
  
    	if($fecha_inicial==""){
	    	$parametro1 = "";
	    	$fecha_inicial2 = "";
	    }else{
	    	$parametro1 =", fecha_inicial";
	    	$new_fecha_inicial = date("d/m/Y", strtotime($fecha_inicial));
	    	$fecha_inicial2= ",'$new_fecha_inicial'";
	    }
	    if($fecha_final==""){
	    	$parametro2 = "";
	    	$fecha_final2 = "";
	    }else{
	    	$parametro2 = ", fecha_final";
	    	$new_fecha_final = date("d/m/Y", strtotime($fecha_final));
	    	$fecha_final2 =", '$new_fecha_final'";
	    }
	    if($numero_caja_archivo_central==""){
	    	$parametro3 = "";
	    	$ubicacion ="";
	    }else{
	    	$parametro3 = ", codigo_ubicacion_topografica";
	    	$ubicacion =", '$id_caja_archivo_central'";	    	
	    }
	    $parametro4 = ", lista_radicados";
	    $lista_radicados = ", '$radicado,'";

        $query_expediente="insert into expedientes (id, id_expediente, nombre_expediente, serie, subserie, fecha_apertura_exp, year_expediente, dependencia_expediente, creador_expediente $parametro1 $parametro2 $parametro3 $parametro4) values('$max_id_expediente2', '$expediente_jonas', '$nombre_expediente', '$serie_expediente1', '$subserie_expediente1', '$timestamp', '$year', '$dependencia_expediente', '$login_usuario' $fecha_inicial2 $fecha_final2 $ubicacion $lista_radicados);";
    	
        $query_insertar_inventario="insert into inventario(numero_consecutivo, codigo_dependencia, nombre_documento, caja_paquete_tomo, numero_caja_paquete, numero_carpeta, consecutivo_desde, consecutivo_hasta,descriptor, total_folios, numero_caja_archivo_central, observaciones, fecha_inventario, cargado_por, radicado_jonas, expediente_jonas $parametro1 $parametro2 ) values ('$consecutivo_inventario', '$dependencia_expediente', '$nombre_expediente', '$caja_paquete_tomo', '$numero_caja_paquete', '$numero_carpeta', '$consecutivo_desde', '$consecutivo_hasta', '$metadato_descriptor', '$total_folios', '$id_caja_archivo_central', '$observaciones', '$timestamp', '$login_usuario', '$radicado', '$expediente_jonas' $fecha_inicial2 $fecha_final2);";

        $query_radicado="insert into radicado (numero_radicado, fecha_radicado, codigo_carpeta1, dependencia_actual, usuarios_visor, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, clasificacion_radicado, id_expediente, codigo_serie, codigo_subserie, estado_radicado, usuarios_control) values ('$radicado', '$timestamp', $codigo_carpeta1, '$dependencia_expediente', '$leido', '$dependencia_expediente', '$login_usuario', '$nombre_expediente', '1', '$leido', 'INVENTARIO', '$expediente_jonas1', '$serie_expediente1', '$subserie_expediente1', 'INVENTARIO', '$leido');";

        $query_ubicacion_fisica = "$query_radicado insert into ubicacion_fisica (numero_radicado, usuario_actual, usuario_anterior, fecha)values('$radicado','$login_usuario','Inventario individual', '$timestamp');";

        $query_historico="insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$radicado', '$login_usuario', 'Inventario FUID individual', 'Documento ingresado por modulo inventario FUID individual.', '$timestamp');";

		$transaccion_historico 	= "Inserta inventario individual";							// Variable para tabla historico_eventos
		$comentario 			= "Documento ingresado por modulo inventario individual"; 	// Variable para historico eventos

		$transaccion 			= "inventario_individual"; 	// Variable para auditoria
		$creado 				= "$radicado";				// Variable para auditoria
		
		if(pg_query($conectado,$query_expediente)){
			if(pg_query($conectado,$query_insertar_inventario)){
				if(pg_query($conectado,$query_radicado)){
						require_once("../login/inserta_historico.php");					
				}else{
					echo "<script> alert('Ocurrió un error al realizar la creación del radicado')</script>";
				}
			}else{
				echo "<script> alert('Ocurrió un error al realizar la insercion al inventario')</script>";
			}
		}else{
			echo "<script> alert('Ocurrió un error al realizar la creación del expediente')</script>";
		}	
	}
?>