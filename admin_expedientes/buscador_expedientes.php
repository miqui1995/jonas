<?php 
	/*Aqui defino la fecha para mostrar en formato "Jueves 05 de Mayo de 2016" */
		include "../include/genera_fecha.php";
	/*Fecha que se realiza la transaccion (hoy)*/	
		$fecha_modificacion = $date; // $date es del formato "2016-05-05"
 
		require_once('../login/conexion2.php');

		if(isset($_POST['desde_formulario'])){
			$desde_formulario = $_POST['desde_formulario']; 
		}
			
/*********************************************************************************************************/
/* Generador principal secuencias ************************************************************************/
/*********************************************************************************************************/
		$search_expediente ='';
		if(isset($_POST['dependencia_expediente'])){
			$dependencia_expediente = $_POST['dependencia_expediente'];
			$year 					= $_POST['year'];
			$serie_expediente 		= $_POST['serie_expediente'];
			$subserie_expediente 	= $_POST['subserie_expediente'];
			
		/* Inicio generar consecutivo de expediente por dependencia/serie/subserie */
			$query_cantidad_expediente = "select count(*) from expedientes where year_expediente='$year' and dependencia_expediente='$dependencia_expediente' and serie='$serie_expediente' and subserie='$subserie_expediente'";

			$fila_cantidad_expediente  = pg_query($conectado,$query_cantidad_expediente);
			$linea_cantidad_expediente = pg_fetch_array($fila_cantidad_expediente);
		/* Fin generar consecutivo de expediente por dependencia/serie/subserie */
		
			if($linea_cantidad_expediente=='0'){
				$max_expediente2="1";	// Inicia el consecutivo del expediente por año/serie/subserie
			}else{
				$max_expediente  	= $linea_cantidad_expediente[0];
				$max_expediente2 	= $max_expediente+1;		// Continúa con consecutivo por año/serie/subserie
			}
			$max_expediente3 = str_pad($max_expediente2, 7, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 7=longitud
			echo $max_expediente3;		
		}
/* Fin de generador de secuencias ************************************************************************/			

/*********************************************************************************************************/
/* Buscador principal expedientes ************************************************************************/
/*********************************************************************************************************/
/* Isset ajax (buscador_expediente) consulta sugerencias - buscador principal expedientes.*/	
		$search_expediente ='';
		if(isset($_POST['search_expediente'])){
			$search_expediente = $_POST['search_expediente'];

			$consulta = "select * from expedientes where id_expediente ilike trim('%$search_expediente%') or nombre_expediente ilike trim ('%$search_expediente%') limit 10";
			$fila_expediente = pg_query($conectado,$consulta);
			if($fila_expediente==false){ // Valida si la tabla expedientes existe en la base de datos
				echo '<script>
					alert("No pude conectarme a la tabla <b>expedientes</b> de la base de datos 1, revisa la base de datos por favor");
					window.location.href="principal3.php"
				</script>';	
			}
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_expedientes= pg_num_rows($fila_expediente);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
			if($registros_expedientes>0 && $search_expediente!=''){
					$num_fila = 0; 
					for ($i=0;$i<$registros_expedientes;$i++){
						$linea = pg_fetch_array($fila_expediente);

						$id 						= $linea['id'];					
						$id_expediente  			= $linea['id_expediente'];
						$nombre_expediente  		= $linea['nombre_expediente'];
						$serie  					= $linea['serie'];
						$subserie 					= $linea['subserie'];
						$year_expediente 			= $linea['year_expediente'];
						$dependencia_expediente 	= $linea['dependencia_expediente'];
						$id_expediente1  			= substr($id_expediente, -7);    // Numero consecutivo del expediente

						switch ($dependencia_expediente) { // En caso que sean expedientes creados por Inventario
							case 'INV':
							case 'INVE':
							case 'INVEN':
								$nombre_serie 		= "Inventario";
								$nombre_subserie 	= "Inventario";
								break;
							
							default:
								$query_nombre_trd_exp = "select * from subseries where codigo_dependencia='$dependencia_expediente' and codigo_serie='$serie' and codigo_subserie='$subserie'"; 
								$fila_nombre_trd_exp  	= pg_query($conectado,$query_nombre_trd_exp);
								$linea_trd_exp 			= pg_fetch_array($fila_nombre_trd_exp);
								$nombre_serie  			= $linea_trd_exp['nombre_serie'];
								$nombre_subserie  		= $linea_trd_exp['nombre_subserie'];
								break;
						}

						echo "<div class='art_exp";
							if ($num_fila%2==0) echo " fila2'"; //si el resto de la división es 0 pongo un color
							else echo " fila1'"; //si el resto de la división NO es 0 pongo otro color 
   
							if($desde_formulario==1){
								echo " onclick='cargar_modifica_expediente(\"$id\",\"$year_expediente\",\"$dependencia_expediente\",\"$id_expediente1\",\"$nombre_expediente\",\"$serie\",\"$subserie\")'";
							}else{
								echo "<a href=\"#\"> <script> alert('No, no hay Ahora formulario_nuevo_expediente es '+'$desde_formulario');</script>";
							}
						echo "'>"; 
			
						/*Aqui defino cuál va a ser el comportamiento al dar click sobre el 
						resultado obtenido*/	
							$search_expediente=strtoupper($search_expediente);
							$id_expediente1 = trim(str_ireplace($search_expediente, "<font color='red'>$search_expediente</font>", $id_expediente)); 
							$nombre_expediente1 = trim(str_ireplace($search_expediente, "<font color='red'>$search_expediente</font>", $nombre_expediente)); 

							echo "<span title='Numero del expediente - Dependencia $dependencia_expediente'> <b>$id_expediente1</b> </span> | <span title='Nombre del expediente'> $nombre_expediente1 </span> | <span title='Serie $serie - $nombre_serie'> Serie <b>$serie</b> </span> | <span title='Subserie $subserie - $nombre_subserie'> Subserie <b>$subserie</b></span>";
							echo "</a>"; // Cierra el href
					/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
						echo "</div>";//cierra div class='art'
						$num_fila++; 
					}
			}else{
				echo "<h2> No se han encontrado resultados</h2><p>Si desea ingresar un nuevo expediente haga click 
					<a href='javascript:abrirVentanaCrearExpediente();'>aqui</a></p>";
			}
		}	
/* Fin isset ajax (buscador_expedientes) consulta sugerencias - buscador principal expedientes.*/	
/*********************************************************************************************************/
/* Isset ajax (buscador_nombre_nivel) consulta sugerencias - nombre expediente -  Formulario Agregar Expediente */	
		$search_nom_expediente ='';
		if(isset($_POST['search_nom_expediente'])){

			$search_nom_expediente = $_POST['search_nom_expediente'];
			$consulta_nom_expediente = "select * from expedientes where UPPER(nombre_expediente) ilike trim('%$search_nom_expediente%') limit 5";

			$fila_nom_expediente = pg_query($conectado,$consulta_nom_expediente);
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_nom_expedientes= pg_num_rows($fila_nom_expediente);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	

			if($registros_nom_expedientes>0 && $search_nom_expediente!=''){
				do{
					$num_fila = 0; 
					for ($i=0;$i<$registros_nom_expedientes;$i++){
						$linea = pg_fetch_array($fila_nom_expediente);

						$id=$linea['id'];					
						$id_expediente = $linea['id_expediente'];
						$nombre_expediente = $linea['nombre_expediente'];
						$serie = $linea['serie'];
						$subserie=$linea['subserie'];
						$creador_expediente=$linea['creador_expediente'];
						$fecha_creacion_expediente=$linea['fecha_apertura_exp'];
						$year_expediente=$linea['year_expediente'];
						$dependencia_expediente=$linea['dependencia_expediente'];
						$id_expediente1 = substr($id_expediente, -7);    // Numero consecutivo del expediente

						/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
						$fecha_creacion_expediente1=$b->traducefecha($fecha_creacion_expediente);	
						
						/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
						echo "<div class='art";
							if ($num_fila%2==0) echo " fila2'>"; //si el resto de la división es 0 pongo un color
							else echo " fila1'>"; //si el resto de la división NO es 0 pongo otro color 
							
							if($desde_formulario==1){
								echo "<a href='javascript:cargar_modifica_expediente(\"$id\",\"$year_expediente\",\"$dependencia_expediente\",\"$id_expediente1\",\"$nombre_expediente\",\"$serie\",\"$subserie\",\"$creador_expediente\")'>";
							}elseif ($desde_formulario==2) {
								$id_expediente1=$_POST['id_expediente1'];

								if($id==$id_expediente1){
									echo "<a href='javascript:carga_nombre_modificar_expediente(\"$nombre_expediente\")'>";
								}else{
									echo "<a href='javascript:error_modificar_expediente()'>";
								}
							}else{
								echo "<a href=\"#\"> <script> alert('No, no hay Ahora formulario_nuevo_expediente es '+'$desde_formulario');</script>";
							}
			
						/*Aqui defino cuál va a ser el comportamiento al dar click sobre el resultado obtenido*/	
							$search_expediente=strtoupper($search_expediente);
							$id_expediente1 = trim(str_ireplace($search_nom_expediente, "<font color='red'>$search_nom_expediente</font>", $id_expediente)); 
							$nombre_expediente1 = trim(str_ireplace($search_nom_expediente, "<font color='red'>$search_nom_expediente</font>", $nombre_expediente)); 

							echo "<b>$id_expediente1</b> | $nombre_expediente1 | Serie <b>$serie</b> | Subserie <b>$subserie</b> | Creado por ($creador_expediente) el $fecha_creacion_expediente1";
							echo "</a>"; // Cierra el href
					/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
						echo "</div>";//cierra div class='art'
						$num_fila++; 
					}
				}while ($fila_expediente=pg_fetch_assoc($fila_nom_expediente));
			}else{
				echo "<script>
					$('#error_nombre_nivel').slideUp('slow');
					$('#nombre_nivel_ya_existe').slideUp('slow');
				</script>";
			}
		}
/* Fin isset ajax (buscador_nombre_nivel) consulta sugerencias - nombre nivel -  Formulario Agregar Nivel */	
/* Isset ajax (codigo_dependencia) listado de series - Formulario agregar expediente */
		$codigo_dependencia = "";
		if(isset($_POST['codigo_dependencia_serie'])){
			$codigo_dependencia = $_POST['codigo_dependencia_serie'];

			$query_nombre_serie = "select distinct codigo_serie, nombre_serie from subseries where codigo_dependencia='$codigo_dependencia' and activo='SI' order by nombre_serie";
	    /*Aqui se ejecuta la query*/
	    	$fila_query_nombre_serie  = pg_query($conectado,$query_nombre_serie);

	    /*Se trae las filas de la query*/
	    	$registros_query_nombre_serie = pg_num_rows($fila_query_nombre_serie);
	    		
	    	if($registros_query_nombre_serie==0){
				echo "<script>$('#error_serie').slideDown('slow');</script>";
			}else{
				echo "<script>$('#error_serie').slideUp('slow');</script>";
				$opcion_serie="<option title='Debe seleccionar una serie válida'>---</option>";
				for ($i=0; $i < $registros_query_nombre_serie ; $i++){
			    	$linea_nombre_serie = pg_fetch_array($fila_query_nombre_serie);  

					$codigo_serie 		= $linea_nombre_serie['codigo_serie'];
					$nombre_serie 		= $linea_nombre_serie['nombre_serie'];

					$serie_exp 	  = "<option title='($codigo_serie) - $nombre_serie'>$codigo_serie</option>";
					$opcion_serie.=$serie_exp;
				}
				echo "$opcion_serie";
			}		
		}
/* Fin isset ajax (codigo_dependencia) listado de series - Formulario agregar expediente */
/* Isset ajax (codigo_dependencia) listado de subseries - Formulario agregar expediente */
		$codigo_dependencia = "";
		if(isset($_POST['codigo_dependencia'])){
			$codigo_dependencia = $_POST['codigo_dependencia'];
			$codigo_serie 		= $_POST['codigo_serie'];

			$query_nombre_subserie = "select * from subseries where codigo_dependencia='$codigo_dependencia' and codigo_serie='$codigo_serie' and activo='SI'";
			echo "$query_nombre_subserie";
	    /*Aqui se ejecuta la query*/
	    	$fila_query_nombre_subserie  = pg_query($conectado,$query_nombre_subserie);

	    /*Se trae las filas de la query*/
	    	$registros_query_nombre_subserie = pg_num_rows($fila_query_nombre_subserie);
			// echo "<script>console.log('$registros_query_nombre_subserie');</script>";
	    		
	    	if($registros_query_nombre_subserie==0){
				echo "<script>$('#error_subserie').slideDown('slow');</script>";
			}else{
				echo "<script>$('#error_subserie').slideUp('slow');</script>";
				$opcion_subserie="<option title='Debe seleccionar una subserie válida'>---</option>";
				for ($i=0; $i < $registros_query_nombre_subserie ; $i++){
			    	$linea_nombre_subserie = pg_fetch_array($fila_query_nombre_subserie);  

					$codigo_dependencia = $linea_nombre_subserie['codigo_dependencia'];
					$codigo_serie 		= $linea_nombre_subserie['codigo_serie'];
					$codigo_subserie 	= $linea_nombre_subserie['codigo_subserie'];
					$nombre_serie 		= $linea_nombre_subserie['nombre_serie'];
					$nombre_subserie 	= $linea_nombre_subserie['nombre_subserie'];

					$subserie_exp = "<option title='($codigo_subserie) - $nombre_subserie'>$codigo_subserie</option>";
					$opcion_subserie=$opcion_subserie.$subserie_exp;
				}
				echo "$opcion_subserie";
			}		
		}
/* Fin isset ajax (codigo_dependencia) listado de subseries - Formulario agregar expediente */

/******************************************************************************************/
/* Modificar Nivel ************************************************************************/
/******************************************************************************************/

/* Isset ajax (buscador_nombre_mod_nivel) sugerencias - nombre nivel - Formulario Modificar Nivel */

		$search_nom_mod_nivel ='';
		if(isset($_POST['search_nombre_mod_nivel'])){
			$search_nom_mod_nivel = strtoupper($_POST['search_nombre_mod_nivel']);
			$search_antiguo_nombre_mod_nivel =$_POST['search_antiguo_nombre_mod_nivel'];
			$consulta_nombre_mod_nivel = "select * from ubicacion_topografica where UPPER(nombre_nivel) like trim(UPPER('%$search_nom_mod_nivel%')) limit 5";

			$fila_codi_nivel = pg_query($conectado,$consulta_nombre_mod_nivel);
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_codi_nivel= pg_num_rows($fila_codi_nivel);

	/* Si el nombre a modificar es el mismo que tenía, quita los errores */
			if($search_nom_mod_nivel==$search_antiguo_nombre_mod_nivel){
				echo"<script>
					$('.art1').slideUp('slow');
					$('#mod_nivel_ya_existe').slideUp('slow');
				</script>";
			}
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
			if($registros_codi_nivel>0 && $search_nom_mod_nivel!=''){
				echo "<script>	
					$('#error_mod_nombre_nivel').slideUp('slow');
					$('#mod_nivel_ya_existe').slideUp('slow');
				</script>";

				do{
					for ($i=0;$i<$registros_codi_nivel;$i++){
						$linea_nivel = pg_fetch_array($fila_codi_nivel);

						$id_ubicacion = $linea_nivel['id_ubicacion'];
						$nombre_nivel = $linea_nivel['nombre_nivel'];
						$nivel_padre = $linea_nivel['nivel_padre'];
						$activa = $linea_nivel['activa'];
						$creador_nivel=$linea_nivel['creador_nivel'];
						$fecha_modificacion_nivel=$linea_nivel['fecha_modificacion'];
		/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
						$fecha_modificacion_nivel=$b->traducefecha($fecha_modificacion_nivel);	
												
						echo "<div class='art1'>";
						/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
							if($desde_formulario===1){
								if($nombre_nivel==$search_antiguo_nombre_mod_nivel){
									echo "<a href='javascript:cargar_modifica_nivel(\"$nombre_nivel\",\"$nivel_padre\",\"$activa\",\"$id_ubicacion\")'>";		
								}else{
									echo "<a href=\"javascript:error_modificar_nivel()\">";
								}
							}else{
								echo "<a href=\"#\"> <script> alert('No, no hay Ahora formulario_nuevo_nivel es '+'$desde_formulario');</script>";
							}
						/*Aqui defino cuál va a ser el comportamiento al dar click sobre el resultado obtenido*/		
							if($nivel_padre==""){
								$nivel_padre1="";
							}else{
								$nivel_padre1="Nivel Padre (<b>$nivel_padre</b>) |";
							}	
							echo "<b>$nombre_nivel</b> | $nivel_padre1 Activa - <b>$activa</b> | Creado por <b>$creador_nivel</b> ($fecha_modificacion_nivel)";
							echo "</a>"; // Cierra el href
					/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
						echo "</div>"; //cierra div class='art1'
					}
				}while ($fila_expediente=pg_fetch_assoc($fila_codi_nivel));
			}else{
				echo"<script>
					$('.art1').slideUp('slow');
					$('#mod_nivel_ya_existe').slideUp('slow');
					$('#error_mod_nombre_nivel').slideUp('slow');
					$('#valida_minimo_mod_nombre').slideUp('slow');
					$('#valida_maximo_mod_nombre').slideUp('slow');
				</script>";
			}	
		}
/* Fin isset ajax (buscador_nombre_mod_nivel) sugerencias - nombre nivel - Formulario Modificar Nivel */
?>