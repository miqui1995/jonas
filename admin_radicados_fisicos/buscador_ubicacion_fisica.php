<?php 
	require_once ("../login/validar_inactividad.php");
	require_once ("../login/conexion2.php");

	$caracteres_dependencia 			= $_SESSION['caracteres_depend'];

	//Condicion si la variable search_ubicacion_fisica trae algun valor
	if(isset($_POST['search_ubicacion_fisica'])){
		$search_ubicacion_fisica 	= $_POST['search_ubicacion_fisica'];
		$radicado_concatenado 		= $_POST['radicado_concatenado'];
		
		// Se extrae cada uno de los readicados separados por coma
		$usu  = explode(",", $radicado_concatenado);
		$max  = sizeof($usu);
		$max2 = $max-1;
		
		//se recorre cada registro dividido por coma
		$radicado = "";
		for ($p=0; $p < $max2; $p++) {
			$radicado_con_coma = $usu[$p];
			$radicado = $radicado." and uf.numero_radicado != '$radicado_con_coma' ";
		}
		//Inicio consulta
		// $query_ubicacion_fisica = "select r.numero_radicado,uf.usuario_actual,r.asunto from ubicacion_fisica uf, radicado r where uf.usuario_actual!='$login' and uf.numero_radicado = r.numero_radicado AND uf.numero_radicado ilike trim('%$search_ubicacion_fisica%') $radicado";
		$query_ubicacion_fisica = "select r.numero_radicado,uf.usuario_actual,r.asunto from ubicacion_fisica uf, radicado r where uf.numero_radicado = r.numero_radicado AND uf.numero_radicado ilike trim('%$search_ubicacion_fisica%') $radicado limit 10";

		//Filas de la consulta
		$fila_ubicacion_fisica = pg_query($conectado,$query_ubicacion_fisica);

		// Valida si la consulta arroja algun error
		if($fila_ubicacion_fisica==false){ 
			echo '<script>
				alert("No pude conectarme a la tabla <b>ubicacion_fisica</b> de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
			
		}
		/*Calcula el numero de registros que genera la query_ubicacion_fisica.*/
		$registros_ubicacion_fisica = pg_num_rows($fila_ubicacion_fisica);	

		//Inicio de condicion si la consulta trae algun resultado
		if($registros_ubicacion_fisica>0 && $search_ubicacion_fisica!=''){

			/* Inicio definiendo longitud_radicado que depende de la variable $_SESSION['caracteres_depend']*/
			$longitud_search_radicado_modificar	= strlen($search_ubicacion_fisica);

			if($codigo_entidad=="EJC" || $codigo_entidad=="EJEC"){
				$longitud_radicado = 16;
			}else{
				$longitud_radicado = 15+$caracteres_dependencia;
			}

			if($longitud_radicado==$longitud_search_radicado_modificar){
				$linea 					= pg_fetch_array($fila_ubicacion_fisica);
				$login_usuario_actual 	= $linea['usuario_actual'];

				echo "<script>cargar_modificar_ubicacion_fisica('$search_ubicacion_fisica','$login_usuario_actual')</script>";	
			}else{
				$num_fila = 0; 
				//Se recorre cada registro del resultado de la consulta 
				for ($i=0;$i<$registros_ubicacion_fisica;$i++){

					$linea = pg_fetch_array($fila_ubicacion_fisica);
					$numero_radicado 	= $linea['numero_radicado'];
					$usuario_actual 	= $linea['usuario_actual'];
					$asunto 			= $linea['asunto'];
					
					$login 				= $linea['usuario_actual'];

					if($asunto==""){
						$asunto1 = "<b>Sin asunto todavía</b>";
					}else{
						$asunto1 = $asunto;
					}

					echo "<div class='art_exp";
						//Esto es para el color back-ground de los resultados 
						if ($num_fila%2==0) echo " fila2'"; //si el resto de la división es 0 pongo un color
						else echo " fila1'"; //si el resto de la división NO es 0 pongo otro color 
							
						//Esto es para poner en rojo lo que coindice con el valor en el input search_ubicacion
						$nombre_busqueda_ubicacion_fisica = trim(strtoupper(str_ireplace($search_ubicacion_fisica, "<font color='red'>$search_ubicacion_fisica</font>", $numero_radicado)));

					/*******Cuando le de click en algun resultado llama a la funcion cargar_modificar_ubicacion_fisica() ubicada en el archivo funciones_ubicacion_fisica.php *********/
					echo "onclick='cargar_modificar_ubicacion_fisica(\"$numero_radicado\",\"$login\")'>$nombre_busqueda_ubicacion_fisica | $asunto1 | Lo tiene en físico el usuario <b>$login</b>"; 
					echo "</div>";//cierra div class='art'
					$num_fila++; //pasa al siguiente registro del resultado de la consulta
				}/* Fin ciclo "for" que recorre cada registro del resultado de la consulta */
			} 	//Fin de condicion si la consulta trae algun resultado
		}else{
		//Inicio else la consulta no trae ninguna resultado
			echo"<h3 style='color:red;'> No se encontraron resultados</h3>";
		}//Fin else la consulta no trae ninguna resultado	
	}	// Fin de Condicion si la variable search_ubicacion_fisica trae algun valor
	if(isset($_POST['search_ubicacion_fisica_planilla'])){
		$search_ubicacion_fisica 	= trim($_POST['search_ubicacion_fisica_planilla']);
		$radicado_concatenado 		= $_POST['radicado_concatenado'];
		
		// Se extrae cada uno de los readicados separados por coma
		$usu  = explode(",", $radicado_concatenado);
		$max  = sizeof($usu);
		$max2 = $max-1;
		
		//se recorre cada registro dividido por coma
		$radicado 		= "";
		$mensaje_return = "";

		for ($p=0; $p < $max2; $p++) {
			$radicado_con_coma = $usu[$p];
			$radicado = $radicado." and uf.numero_radicado != '$radicado_con_coma' ";
			/* Cuando el numero de radicado completo ya está en el listado enviado */
			if(strtoupper($radicado_con_coma)==strtoupper($search_ubicacion_fisica)){
				$mensaje_return = "<h2 style='color:red;'>El numero de radicado ya está en el listado.</h2>";
				break;
			}
		}

		$query_ubicacion_fisica = "select r.numero_radicado,uf.usuario_actual,r.asunto, r.folios from ubicacion_fisica uf, radicado r where uf.numero_radicado = r.numero_radicado AND uf.numero_radicado ilike trim('%$search_ubicacion_fisica%') $radicado limit 10";

		//Filas de la consulta
		$fila_ubicacion_fisica = pg_query($conectado,$query_ubicacion_fisica);

		// Valida si la consulta arroja algun error
		if($fila_ubicacion_fisica==false){ 
			echo '<script>
				alert("No pude conectarme a la tabla <b>ubicacion_fisica</b> de la base de datos 1, revisa la base de datos por favor");
			</script>';	
			
		}
		/*Calcula el numero de registros que genera la query_ubicacion_fisica.*/
		$registros_ubicacion_fisica = pg_num_rows($fila_ubicacion_fisica);	

		/* Inicio definiendo longitud_radicado que depende de la variable $_SESSION['caracteres_depend']*/
		$longitud_search_radicado_modificar	= strlen($search_ubicacion_fisica);

		if($codigo_entidad=="EJC" || $codigo_entidad=="EJEC"){
			$longitud_radicado = 16;
		}else{
			$longitud_radicado = 15+$caracteres_dependencia;
		}

		//Inicio de condicion si la consulta trae algun resultado
		if($registros_ubicacion_fisica>0 && $search_ubicacion_fisica!=''){

			if($longitud_radicado==$longitud_search_radicado_modificar){
				$linea 					= pg_fetch_array($fila_ubicacion_fisica);
				$login_usuario_actual 	= $linea['usuario_actual'];

				echo "<script>cargar_modificar_ubicacion_fisica('$search_ubicacion_fisica','$login_usuario_actual')</script>";	
			}else{
				$num_fila = 0; 
				//Se recorre cada registro del resultado de la consulta 
				for ($i=0;$i<$registros_ubicacion_fisica;$i++){

					$linea = pg_fetch_array($fila_ubicacion_fisica);
					$numero_radicado 	= $linea['numero_radicado'];
					$usuario_actual 	= $linea['usuario_actual'];
					$asunto 			= $linea['asunto'];
					$folios 			= $linea['folios'];
					
					$login 				= $linea['usuario_actual'];

					echo "<div class='art_exp";
						//Esto es para el color back-ground de los resultados 
					if ($num_fila%2==0) echo " fila2'"; //si el resto de la división es 0 pongo un color
					else echo " fila1'"; //si el resto de la división NO es 0 pongo otro color 
							
					//Esto es para poner en rojo lo que coindice con el valor en el input search_ubicacion
					$nombre_busqueda_ubicacion_fisica = trim(strtoupper(str_ireplace($search_ubicacion_fisica, "<font color='red'>$search_ubicacion_fisica</font>", $numero_radicado)));

					if($asunto==""){
						$botones_respuesta ="onclick='alert(\"No se puede cargar si el documento no tiene asunto\")'>$nombre_busqueda_ubicacion_fisica | <b style='color:red;'>Sin asunto todavía</b> | Lo tiene en físico el usuario <b>$login</b>";
					}else if ($folios=="" or $folios == NULL) {
						$botones_respuesta ="onclick='alert(\"No se puede cargar si el documento no tiene imagen PDF\")'>$nombre_busqueda_ubicacion_fisica | <b style='color:red;'>Sin imagen PDF todavía</b> | Lo tiene en físico el usuario <b>$login</b>";
					}else{
						$botones_respuesta ="onclick='cargar_modificar_ubicacion_fisica(\"$numero_radicado\",\"$login\")'>$nombre_busqueda_ubicacion_fisica | $asunto | Lo tiene en físico el usuario <b>$login</b>";
					}


					/*******Cuando le de click en algun resultado llama a la funcion cargar_modificar_ubicacion_fisica() ubicada en el archivo funciones_ubicacion_fisica.php *********/
					echo "$botones_respuesta"; 
					echo "</div>";//cierra div class='art'
					$num_fila++; //pasa al siguiente registro del resultado de la consulta
				}/* Fin ciclo "for" que recorre cada registro del resultado de la consulta */
			} 	//Fin de condicion si la consulta trae algun resultado
		}else{
		//Inicio else la consulta no trae ninguna resultado
			if($mensaje_return==""){
				echo"<h3 style='color:red;'> No se encontraron resultados</h3>";
			}else{
				echo "$mensaje_return";
			}
		}//Fin else la consulta no trae ninguna resultado	
	}

?>
