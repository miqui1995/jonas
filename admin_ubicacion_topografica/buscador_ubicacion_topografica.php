<?php 
	if(!isset($_SESSION)){ // Si no hay variables de sesion, inicializa.
		session_start();
	}
	/*Aqui defino la fecha para mostrar en formato "Jueves 05 de Mayo de 2016" */
		include "../include/genera_fecha.php";
	/*Fecha que se realiza la transaccion (hoy)*/	
		$fecha_modificacion = $date; // $date es del formato "2016-05-05"
 
		require_once('../login/conexion2.php');

		$dependencia 	= $_SESSION['dependencia'];
		$inventario 	= $_SESSION['inventario'];

		if(isset($_POST['desde_formulario'])){
			$desde_formulario = 1; 
		}	
/* Isset ajax (buscador_nivel) consulta sugerencias - buscador principal nivel.*/	
		$search_nivel ='';
		if(isset($_POST['search_nivel'])){
			$search_nivel = $_POST['search_nivel'];

/*			
			if($inventario=="SI"){
			}else{
				$consulta = "select * from ubicacion_topografica where UPPER(nombre_nivel) ilike trim(UPPER('%$search_nivel%')) and dependencia_ubicacion_topografica='$dependencia' limit 5";
			}
*/			
			$consulta = "select * from ubicacion_topografica where UPPER(nombre_nivel) ilike trim(UPPER('%$search_nivel%')) limit 5";
			// echo "qq $consulta";
			$fila_nivel = pg_query($conectado,$consulta);
			if($fila_nivel==false){ // Valida si la tabla ubicacion_topografica existe en la base de datos
				echo '<script>
					alert("No pude conectarme a la tabla UT de la base de datos 1, revisa la base de datos por favor");
					window.location.href="principal3.php"
				</script>';	
			}
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_nivel= pg_num_rows($fila_nivel);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
			if($registros_nivel>0 && $search_nivel!=''){
				do{
					$num_fila = 0; 
					for ($i=0;$i<$registros_nivel;$i++){
						$linea = pg_fetch_array($fila_nivel);

						$nombre_nivel 			= $linea['nombre_nivel'];
						$nivel_padre 			= $linea['nivel_padre'];
						$activa 				= $linea['activa'];
						$creador_nivel 			= $linea['creador_nivel'];
						$fecha_creacion_nivel  	= $linea['fecha_modificacion'];
						/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
						$fecha_creacion_nivel=$b->traducefecha($fecha_creacion_nivel);	
						$id_ubicacion=$linea['id_ubicacion'];					
						
						$nombre_nivel1 = strtoupper(trim(str_ireplace($search_nivel, "<font color='red'>$search_nivel</font>", $nombre_nivel))); 

						echo "<div class='art_exp";
							if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
							else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
						echo "'
						onclick='cargar_modifica_nivel(\"$nombre_nivel\",\"$nivel_padre\",\"$activa\",\"$id_ubicacion\")'>"; 	
						
						if($nivel_padre==""){
							$nivel_padre1="";
						}else{
							$nivel_padre1="Nivel Padre (<b>$nivel_padre</b>) |";
						}		
						echo "<b>$nombre_nivel1</b> | $nivel_padre1 Activa - <b>$activa</b> | Creado por <b>$creador_nivel</b> ($fecha_creacion_nivel)";

					/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
						echo "</div>";//cierra div class='art'
						$num_fila++; 
					}
				}while ($fila_nivel=pg_fetch_assoc($fila_nivel));
			}else{
				echo "<h2> No se han encontrado resultados</h2><p>Si desea ingresar un nuevo nivel haga click 
					<a href='javascript:abrir_ventana_crear_nivel();'>aqui</a></p>";
			}
		}	
/* Fin isset ajax (buscador_nivel) consulta sugerencias - buscador principal nivel.*/	

/* Isset ajax (buscador_nombre_nivel) consulta sugerencias - nombre nivel -  Formulario Agregar Nivel */	
		$search_nom_nivel ='';
		if(isset($_POST['search_nom_nivel'])){
			$search_nom_nivel = $_POST['search_nom_nivel'];
			
			if($inventario=="SI"){
				$consulta_nom_nivel = "select * from ubicacion_topografica where nombre_nivel ilike '%$search_nom_nivel%' limit 5";
			}else{
				$consulta_nom_nivel = "select * from ubicacion_topografica where nombre_nivel ilike '%$search_nom_nivel%' and dependencia_ubicacion_topografica='$dependencia' limit 5";
			}

			$fila_nom_nivel = pg_query($conectado,$consulta_nom_nivel);
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_nom_nivel= pg_num_rows($fila_nom_nivel);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
			if($registros_nom_nivel>0 && $search_nom_nivel!=''){
				
				for ($i=0;$i<$registros_nom_nivel;$i++){
					$linea_nom_nivel = pg_fetch_array($fila_nom_nivel);

					$id_ubicacion 			= $linea_nom_nivel['id_ubicacion'];
					$nombre_nivel 			= $linea_nom_nivel['nombre_nivel'];
					$nivel_padre 			= $linea_nom_nivel['nivel_padre'];
					$activa 				= $linea_nom_nivel['activa'];
					$creador_nivel 			= $linea_nom_nivel['creador_nivel'];
					$fecha_creacion_nivel  	= $linea_nom_nivel['fecha_modificacion'];

/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
					$fecha_creacion_nivel=$b->traducefecha($fecha_creacion_nivel);	
									
					echo "<div class='art' title='Activa - ($activa) &#13;Creado por $creador_nivel &#13;($fecha_creacion_nivel)' onclick='cargar_modifica_nivel(\"$nombre_nivel\",\"$nivel_padre\",\"$activa\",\"$id_ubicacion\")'>";
				
					/*Aqui defino cuál va a ser el comportamiento al dar click sobre el 
					resultado obtenido*/	
					if($nivel_padre==""){
						$nivel_padre1="";
					}else{
						$nivel_padre1="Nivel Padre (<b>$nivel_padre</b>)";
					}		

					$nombre_nivel1 = strtoupper(trim(str_ireplace($search_nom_nivel, "<b><font color='red'>$search_nom_nivel</font></b>", $nombre_nivel)));

					echo "<b>$nombre_nivel1</b> | $nivel_padre1";

				/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
				}
				
			}else{
				echo "<script>
					$('#error_nombre_nivel').slideUp('slow');
					$('#nombre_nivel_ya_existe').slideUp('slow');
				</script>";
			}
		}
/* Fin isset ajax (buscador_nombre_nivel) consulta sugerencias - nombre nivel -  Formulario Agregar Nivel */	
	
/* Isset ajax (buscador_nivel_padre) consulta sugerencias - nombre del nivel padre - Formulario Agregar Nivel */	

		$search_nivel_padre ='';
		if(isset($_POST['search_nivel_padre'])){
			$search_nivel_padre = $_POST['search_nivel_padre'];
		
/*			
			if($inventario=="SI"){
				$consulta_nivel_padre = "select * from ubicacion_topografica where nombre_nivel ilike '%$search_nivel_padre%' limit 5";
			}else{
				$consulta_nivel_padre = "select * from ubicacion_topografica where nombre_nivel ilike '%$search_nivel_padre%' and dependencia_ubicacion_topografica='$dependencia' limit 5";
			}
*/	
			$consulta_nivel_padre = "select * from ubicacion_topografica where nombre_nivel ilike '%$search_nivel_padre%' limit 5";
			
			$fila_nivel_padre = pg_query($conectado,$consulta_nivel_padre);
		/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_codi_padre= pg_num_rows($fila_nivel_padre);
		/*Recorre el array generado e imprime uno a uno los resultados.*/	

			if($registros_codi_padre>0 && $search_nivel_padre!=''){

				echo "<script>$('#error_nombre_nivel_padre').slideUp('slow');</script>";

				for ($i=0;$i<$registros_codi_padre;$i++){
					$linea_nivel_padre = pg_fetch_array($fila_nivel_padre);

					$id_ubicacion_padre 			= $linea_nivel_padre['id_ubicacion'];
					$nombre_nivel_padre 			= $linea_nivel_padre['nombre_nivel'];
					$nivel_padre_padre 				= $linea_nivel_padre['nivel_padre'];
					$activa_padre 					= $linea_nivel_padre['activa'];
					$creador_nivel_padre 			= $linea_nivel_padre['creador_nivel'];
					$fecha_modificacion_nivel_padre = $linea_nivel_padre['fecha_modificacion'];
	/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
					$fecha_modificacion_nivel_padre=$b->traducefecha($fecha_modificacion_nivel_padre);	
							
					$nombre_nivel_padre1 = strtoupper(trim(str_ireplace($search_nivel_padre, "<b><font color='red'>$search_nivel_padre</font></b>", $nombre_nivel_padre)));
											
					echo "<div class='art_exp' onclick='cargar_nivel_padre(\"$nombre_nivel_padre\")' title='Cargar Nivel Padre &#13; Activa - $activa_padre &#13; Creado por $creador_nivel_padre &#13;($fecha_modificacion_nivel_padre)'>";
					/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
					
					/*Aqui defino cuál va a ser el comportamiento al dar click sobre el 
					resultado obtenido*/	
						if($nivel_padre_padre==""){
							$nivel_padre_padre1="";
						}else{
							$nivel_padre_padre1="Nivel Padre (<b>$nivel_padre_padre</b>) ";
						}		
						echo "<b>$nombre_nivel_padre1</b> | $nivel_padre_padre1";
						
				/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
				}
			}elseif($registros_codi_padre==0 && $search_nivel_padre!=''){// Si no encuentra el nivel padre
				echo "<script>
					$('#error_nombre_nivel_padre').slideDown('slow');
					$('#error_nombre_nivel_padre2').slideUp('slow');
				</script>";
							
			}elseif($search_nivel_padre==''){
				echo "<script>
					$('#error_nombre_nivel_padre').slideUp('slow');
				</script>";	
			}
		}
/* Fin isset ajax (buscador_nivel_padre) consulta sugerencias - nombre de nivel padre - Formulario Agregar Nivel */

/******************************************************************************************/
/* Modificar Nivel ************************************************************************/
/******************************************************************************************/

/* Isset ajax (buscador_nombre_mod_nivel) sugerencias - nombre nivel - Formulario Modificar Nivel */

		$search_nom_mod_nivel ='';
		if(isset($_POST['search_nombre_mod_nivel'])){
			$search_nom_mod_nivel 				= strtoupper($_POST['search_nombre_mod_nivel']);
			$search_antiguo_nombre_mod_nivel 	= $_POST['search_antiguo_nombre_mod_nivel'];
			$consulta_nombre_mod_nivel  		= "select * from ubicacion_topografica where nombre_nivel ilike '%$search_nom_mod_nivel%' limit 5";

			$fila_codi_nivel 		= pg_query($conectado,$consulta_nombre_mod_nivel);
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_codi_nivel 	= pg_num_rows($fila_codi_nivel);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
			for ($i=0;$i<$registros_codi_nivel;$i++){
				$linea_nivel = pg_fetch_array($fila_codi_nivel);

				$id_ubicacion 				= $linea_nivel['id_ubicacion'];
				$nombre_nivel 				= $linea_nivel['nombre_nivel'];
				$nivel_padre 				= $linea_nivel['nivel_padre'];
				$activa 					= $linea_nivel['activa'];
				$creador_nivel 				= $linea_nivel['creador_nivel'];
				$fecha_modificacion 		= $linea_nivel['fecha_modificacion'];
	/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
				$fecha_modificacion_nivel 	= $b->traducefecha($fecha_modificacion);	
											
	/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido */;
				echo "<div class='art art_nombre_nivel' title='Creado por $creador_nivel ($fecha_modificacion_nivel)' onclick=";
				if($desde_formulario===1){
					if($nombre_nivel == $search_antiguo_nombre_mod_nivel){
						echo "'cargar_modifica_nivel(\"$nombre_nivel\",\"$nivel_padre\",\"$activa\",\"$id_ubicacion\")'> ";		
					}else{
						echo "'error_modificar_nivel()'>";
					}
				}else{
					echo "<a href='#'> <script> alert('No, no hay Ahora formulario_nuevo_nivel es '+'$desde_formulario');</script>";
				}

				if($nivel_padre!=""){
					$nivel_padre1="Nivel Padre (<b>$nivel_padre</b>) |";
				}	
				echo "<b>$nombre_nivel</b> | $nivel_padre1 Activa - <b>$activa</b>";
				echo "</div>"; //cierra div class='art1'
			}
		}
/* Fin isset ajax (buscador_nombre_mod_nivel) sugerencias - nombre nivel - Formulario Modificar Nivel */

/* Isset ajax (buscador_mod_nivel_padre) sugerencias - nombre nivel padre - Formulario Modificar Nivel */	

		$search_mod_nivel_padre ='';
		if(isset($_POST['search_mod_nivel_padre'])){
			$search_mod_nivel_padre 	= strtoupper($_POST['search_mod_nivel_padre']);
			$search_antiguo_mod_padre 	= $_POST['search_antiguo_mod_padre'];
			$antiguo_nombre_nivel 		= $_POST['antiguo_nombre_nivel'];

			$consulta_nivel_padre 		= "select * from ubicacion_topografica where nombre_nivel ilike '%$search_mod_nivel_padre%' and activa='SI' limit 5";

			$fila_nivel_padre 			= pg_query($conectado,$consulta_nivel_padre);
	/* Calcula el numero de registros que genera la consulta anterior. */
			$registros_mod_codi_padre 	= pg_num_rows($fila_nivel_padre);

			if($registros_mod_codi_padre==0){
				echo "<script>$('#error_nombre_mod_nivel_padre').slideDown('slow')</script>";
			}else{
		/* Recorre el array generado e imprime uno a uno los resultados. */	
				for ($i=0;$i<$registros_mod_codi_padre;$i++){
					$linea_nivel_padre = pg_fetch_array($fila_nivel_padre);

					$id_ubicacion_padre 			= $linea_nivel_padre['id_ubicacion'];
					$nombre_nivel_padre 			= $linea_nivel_padre['nombre_nivel'];
					$nivel_padre 					= $linea_nivel_padre['nivel_padre'];
					$activa_padre  					= $linea_nivel_padre['activa'];
					$creador_nivel_padre 			= $linea_nivel_padre['creador_nivel'];
					$fecha_modificacion_nivel_padre = $linea_nivel_padre['fecha_modificacion'];
	/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
					$fecha_modificacion_nivel_padre = $b->traducefecha($fecha_modificacion_nivel_padre);
					echo "<div class='art art_nivel_padre' title='Creado por $creador_nivel_padre ($fecha_modificacion_nivel_padre)' onclick=";
					/* Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href" */;
						if($desde_formulario===1){
							if($nombre_nivel_padre == $antiguo_nombre_nivel){
								echo "'error_modificar_nivel_padre3()'";
							}else{
								echo "'cargar_nivel_mod_padre(\"$nombre_nivel_padre\")'>";
							}
						}else{
							echo "'#'> <script> alert('No, no hay Ahora formulario_nuevo_nivel es '+'$desde_formulario');</script>";
						}

					/*Aqui defino cuál va a ser el comportamiento al dar click sobre el resultado obtenido*/	
						if($nivel_padre=""){
							$nivel_padre1="";
						}else{
							$nivel_padre1="Nivel Padre (<b>$nivel_padre</b>) |";
						}	
						echo "<b>$nombre_nivel_padre</b> | $nivel_padre1 Activa - <b>$activa_padre</b>";
						// echo "</a>"; // Cierra el href
				/* Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados */
					echo "</div>"; //cierra div class='art2'
				}

			}
		}
/* Fin isset ajax (buscador_mod_nivel_padre) sugerencias - nombre nivel padre - Formulario Modificar Nivel */

/******************************************************************************************/
/* Administrador Cajas ********************************************************************/
/******************************************************************************************/
/* Isset ajax (search_expedientes) Buscador expedientes - Formulario Administrador Cajas */

		$search_expedientes ='';
		if(isset($_POST['search_expedientes'])){
			$search_expedientes = strtoupper($_POST['search_expedientes']); 
			$nombre_nivel_general = $_POST['nombre_n']; 
			$id_nivel = $_POST['id_nivel']; 

			$consulta_docs_en_expediente = "select * from expedientes where (id_expediente ilike '%$search_expedientes%' or nombre_expediente ilike '%$search_expedientes%') and (codigo_ubicacion_topografica!='$id_nivel' or codigo_ubicacion_topografica is null) limit 10;";
			// $consulta_docs_en_expediente = "select * from expedientes e join inventario i on e.id_expediente=i.expediente_jonas where (e.id_expediente ilike '%$search_expedientes%' or e.nombre_expediente ilike '%$search_expedientes%' or i.descriptor ilike '%$search_expedientes%') and (e.codigo_ubicacion_topografica!='$id_nivel' or e.codigo_ubicacion_topografica is null) limit 20;"; 
			echo "$consulta_docs_en_expediente";
			$fila_expedientes = pg_query($conectado,$consulta_docs_en_expediente);

	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_expedientes= pg_num_rows($fila_expedientes);

			if($registros_expedientes>0){
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
				$num_fila=0; // Variable declarada para poner color a la fila según ésta variable.
				$botones_expediente="";
				while ($linea_expedientes = pg_fetch_array($fila_expedientes)){
					$id_expediente 			= strtoupper($linea_expedientes['id_expediente']);
					$nombre_expediente 		= strtoupper($linea_expedientes['nombre_expediente']);
					// $metadato_expediente 	= strtoupper($linea_expedientes['descriptor']);

					$codigo_ubicacion_topografica = $linea_expedientes['codigo_ubicacion_topografica'];

					$id_expediente1 		= trim(str_ireplace($search_expedientes, "<b><font color='red'>$search_expedientes</font></b>", $id_expediente));
					$nombre_expediente1 	= trim(str_ireplace($search_expedientes, "<b><font color='red'>$search_expedientes</font></b>", $nombre_expediente));
					// $metadato_expediente1 	= trim(str_ireplace($search_expedientes, "<b><font color='red'>$search_expedientes</font></b>", $metadato_expediente));

					if($num_fila%2==0){	 //si el resto de la división es 0 pongo un color
						$tr="<tr class='fila2'>";
					}else{				 //si el resto de la división NO es 0 pongo otro color
						$tr="<tr class='fila1'>";
					}

					if($codigo_ubicacion_topografica!=''){
	/* Nombre del nivel */	
						$query_nombre_nivel="select * from ubicacion_topografica where id_ubicacion ='$codigo_ubicacion_topografica'";
						$fila_nombre_nivel = pg_query($conectado,$query_nombre_nivel);
						$linea_nombre_nivel = pg_fetch_array($fila_nombre_nivel);
						$nombre_nivel=$linea_nombre_nivel['nombre_nivel'];
	/* Fin Nombre del nivel */	
						$boton_agregar="<button class='botones' style=\"background-color: #c91c4a; color:#FFFFFF; font-size:12px; padding:5px; width:120px;\" onclick=\"mover_exp_caja('$id_expediente','$id_nivel','$nombre_nivel_general')\" title='Mover a $id_expediente - $nombre_expediente' >
						Mover a $nombre_nivel_general</button>";
						$caja_actual="Actualmente ubicado en ($nombre_nivel)";
					}else{

						$boton_agregar="<button class='botones' style=\"font-size:12; padding:5px; width:120px;\" title='Agregar a $id_expediente - $nombre_expediente' onclick=\"agregar_exp_caja('$id_expediente','$id_nivel','$nombre_nivel_general')\">Agregar a $nombre_nivel_general</button>";
						$caja_actual="No tiene ubicacion en caja todavía";
					}

					$botones_expediente=$botones_expediente."$tr<td width='75%' style=\"padding:5px;font-size:12px;\">$id_expediente1 - $nombre_expediente1<br><b>$caja_actual</b></td><td valign='middle'>$boton_agregar</td></tr>";
					$num_fila++; 
				}	
				$nombre_caja=$botones_expediente;
			}else{
				$nombre_caja="No hay expediente con los parametros de búsqueda.";	
			}	
			echo $nombre_caja;
		} 
/* Fin isset ajax (search_expedientes) Buscador expedientes - Formulario Administrador Cajas */

/* Isset ajax (search_radicados) Buscador radicados - Formulario Administrador Cajas */

		$search_radicados ='';
		if(isset($_POST['search_radicados'])){
			$search_radicados = $_POST['search_radicados']; 
			
			$consulta_radicados = "select * from radicado where id_expediente ilike'%$search_radicados%'"; 
			$fila_radicados = pg_query($conectado,$consulta_radicados);

	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_radicados= pg_num_rows($fila_radicados);
			$botones_radicados="";

			if($registros_radicados>0){
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
				$num_fila=0; // Variable declarada para poner color a la fila según ésta variable.
				while ($linea_radicados = pg_fetch_array($fila_radicados)){
					$numero_radicado=$linea_radicados['numero_radicado'];
					$asunto=$linea_radicados['asunto'];

					if($num_fila%2==0){	 //si el resto de la división es 0 pongo un color
						$tr="<tr class='fila2'>";
					}else{				 //si el resto de la división NO es 0 pongo otro color
						$tr="<tr class='fila1'>";
					}
					$botones_radicados=$botones_radicados."$tr<td style=\"padding:5px\"><b>$numero_radicado</b> - $asunto</td><td><input type='button' class='art_exp' style='background-color:#2D9DC6;' value='Ver documento' title='Ver documento $numero_radicado' onclick=\"cerrarVentanaAdminCajas(); agregar_pestanas('$numero_radicado')\"><input type='button' class='art_exp' style='background-color:#2D9DC6;' value='Sacar' title='Sacar documento del expediente $search_radicados' onclick=\"sacar_documentos_exp('$numero_radicado','$search_radicados')\" ></td></tr>";
					$num_fila++; 
				}

				$listado_radicados=$botones_radicados;
			}else{
				$listado_radicados="No hay radicados asociados a éste expediente. ";	
			}	
			echo $listado_radicados;	
		}
/* Fin isset ajax (search_radicados) Buscador radicados - Formulario Administrador Cajas */
		
/* Isset ajax (search_radicados1) Buscador radicados - Formulario Administrador Expedientes */

		$search_radicados1 ='';
		if(isset($_POST['search_radicados1'])){
			$search_radicados1 = $_POST['search_radicados1']; 
			$numero_expediente_rad = $_POST['numero_expediente_rad']; 
			
			$consulta_radicados = "select * from radicado where (numero_radicado ilike '%$search_radicados1%' or asunto ilike '%$search_radicados1%') and (id_expediente!='$numero_expediente_rad' or id_expediente is null) limit 10"; 

			$fila_radicados = pg_query($conectado,$consulta_radicados);

	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_radicados= pg_num_rows($fila_radicados);
			$botones_radicados1="";

			if($registros_radicados>0){
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
				$num_fila=0; // Variable declarada para poner color a la fila según ésta variable.
				while ($linea_radicados = pg_fetch_array($fila_radicados)){
					$numero_radicado=$linea_radicados['numero_radicado'];
					$asunto=$linea_radicados['asunto'];
					$expediente_actual=$linea_radicados['id_expediente'];

					 $numero_radicado1 = trim(str_ireplace($search_radicados1, "<b><font color='red'>$search_radicados1</font></b>", $numero_radicado));
					 $asunto1 = trim(str_ireplace($search_radicados1, "<b><font color='red'>$search_radicados1</font></b>", $asunto));

					if($expediente_actual!=''){
	/* Nombre del expediente 	
						$query_nombre_nivel="select * from expedientes where id_expediente ='$expediente_actual'";
						$fila_nombre_nivel = pg_query($conectado,$query_nombre_nivel);
						$linea_nombre_nivel = pg_fetch_array($fila_nombre_nivel);
						$nombre_nivel=$linea_nombre_nivel['nombre_nivel'];
	 Fin Nombre del expediente */	
						$boton_agregar="<input type='button' class='botones' value='Mover' title='Mover a $numero_expediente_rad' onclick=\"mover_rad_exp('$numero_radicado','$numero_expediente_rad')\">";
						$expediente_actual1="Actualmente en expediente ($expediente_actual)";
					}else{
						$boton_agregar="<input type='button' class='botones' value='Agregar' title='Agregar al expediente $numero_expediente_rad' onclick=\"agregar_rad_exp('$numero_radicado','$numero_expediente_rad')\">";
						$expediente_actual1="No tiene expediente todavía";
					}



					if($num_fila%2==0){	 //si el resto de la división es 0 pongo un color
						$tr="<tr class='fila2'>";
					}else{				 //si el resto de la división NO es 0 pongo otro color
						$tr="<tr class='fila1'>";
					}
					$botones_radicados1=$botones_radicados1."$tr<td width='85%'>consulta_radicados <br><b>$numero_radicado1</b> - $asunto1</td><td>$boton_agregar<br>$expediente_actual1</td></tr>";
					$num_fila++; 
				}

				$listado_radicados=$botones_radicados1;
			}else{
				$listado_radicados="<h4>No se encuentran resultados para su búsqueda.</h4> ";	
			}	
			echo $listado_radicados;	
		}
		
/* Fin isset ajax (search_radicados1) Buscador radicados - Formulario Administrador Expedientes */
  
?>