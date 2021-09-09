<?php 
	if(!isset($_SESSION)){
	    session_start();
	}
	/*Aqui defino la fecha para mostrar en formato "Jueves 05 de Mayo de 2016" */
		include "../include/genera_fecha.php";
	/*Fecha que se realiza la transaccion (hoy)*/	
		$fecha_modificacion = $date; // $date es del formato "2016-05-05"
 
		require_once('../login/conexion2.php');
		// sleep(1);
		if(isset($_POST['desde_formulario'])){
			$desde_formulario = 1; 
		}	
/* Isset ajax (buscador_dependencias) consulta sugerencias - buscador principal dependencias.*/	
		$search_depe ='';
		if(isset($_POST['search_depe'])){
			$search_depe = $_POST['search_depe'];

			// $consulta = "select * from dependencias where codigo_dependencia ilike ('%$search_depe%') OR nombre_dependencia ilike ('%$search_depe%') order by nombre_dependencia limit 10";

			$consulta = " select * from dependencias d left join cambios_organico_funcionales c on d.id_cambio_organico_funcional=c.id_cambio_organico_funcional where codigo_dependencia ilike ('%$search_depe%') OR nombre_dependencia ilike ('%$search_depe%') order by nombre_dependencia limit 10";

			$fila_depe = pg_query($conectado,$consulta);

			if($fila_depe==false){ // Valida si la tabla dependencias existe en la base de datos
				echo '<script>
					alert("No pude conectarme a la tabla D de la base de datos 1, revisa la base de datos por favor");
					window.location.href="principal3.php"
				</script>';	
			}
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_depe= pg_num_rows($fila_depe);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	

			if($registros_depe>0 && $search_depe!=''){
				$num_fila = 0; 
				for ($i=0;$i<$registros_depe;$i++){
					$linea = pg_fetch_array($fila_depe);

					$activa  					= $linea['activa'];
					$codigo_dependencia 		= $linea['codigo_dependencia'];
					$creador_dependencia  		= $linea['creador_dependencia'];
					$dependencia_padre  		= $linea['dependencia_padre'];
					$fecha_creacion_dependencia = $linea['fecha_modificacion'];
					$fecha_final_cambio 		= $linea['fecha_final_cambio'];
					$fecha_inicial_cambio 		= $linea['fecha_inicial_cambio'];
					$id_cambio_organico_fun 	= $linea['id_cambio_organico_funcional'];
					$nombre_dependencia 		= $linea['nombre_dependencia'];

					if($fecha_final_cambio==""){
						$fecha_final_cambio = "Actualmente";
					}

					if($fecha_inicial_cambio==""){
						$vigencia1 = "<font color='red'> Dependencia sin asignar a ningun cambio organico-funcional. Consulte con el Administrador del Sistema.</font>";
					}else{
						$vigencia1 = "<font color='green'> Vigencia ($fecha_inicial_cambio hasta $fecha_final_cambio)</font>";
					}

					/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
					$fecha_creacion_depe=$b->traducefecha($fecha_creacion_dependencia);	
					$id_dependencia=$linea['id_dependencia'];					
					
					echo "<div class='art_exp";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "' onclick =\"javascript:cargar_modifica_dependencia('$codigo_dependencia','$nombre_dependencia','$dependencia_padre','$activa','$id_dependencia','$id_cambio_organico_fun')\" title='Creado por $creador_dependencia ($fecha_creacion_depe)&#13;Click para modificar datos de dependencia.'>"; 
					
						$codigo_dependencia1 = strtoupper(trim(str_ireplace($search_depe, "<font color='red'>$search_depe</font>", $codigo_dependencia)));
						$nombre_dependencia1 = strtoupper(trim(str_ireplace($search_depe, "<font color='red'>$search_depe</font>", $nombre_dependencia))); 

					/*Aqui defino cuál va a ser el comportamiento al dar click sobre el 
					resultado obtenido*/			
						echo " ($codigo_dependencia1) $nombre_dependencia1 |<font color='blue'> Activa - $activa </font> | $vigencia1 <br>Dependencia Padre ( $dependencia_padre )";
				/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
					$num_fila++; 
				}
			}else{
				echo "<h2> No se han encontrado resultados</h2><p>Si desea ingresar una nueva dependencia haga click 
					<a href='javascript:abrirVentanaCrearDependencia();'>aqui</a></p>";
			}
		}	
/* Fin isset ajax (buscador_dependencias) consulta sugerencias - buscador principal dependencias.*/	

/* Isset ajax (buscador_codigo_dependencias) consulta sugerencias - codigo dependencia -  Formulario de Agregar Dependencia */	
		$search_codi_depe ='';
		if(isset($_POST['search_codi_depe'])){
			if(!isset($_SESSION)){
				session_start();
			}
			$search_codi_depe = $_POST['search_codi_depe'];
			$caracteres_depend = $_SESSION['caracteres_depend'];

			// $consulta_codi_depe  = "select * from dependencias where codigo_dependencia ilike '%$search_codi_depe%' and activa ='SI' order by nombre_dependencia limit 5";

			$consulta_codi_depe = " select * from dependencias d left join cambios_organico_funcionales c on d.id_cambio_organico_funcional=c.id_cambio_organico_funcional where codigo_dependencia ilike ('%$search_codi_depe%') OR nombre_dependencia ilike ('%$search_codi_depe%') and activa='SI' order by nombre_dependencia limit 10";

			$fila_codi_depe 	 = pg_query($conectado,$consulta_codi_depe);
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_codi_depe = pg_num_rows($fila_codi_depe);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	

			if($registros_codi_depe>0 && $search_codi_depe!=''){	
				for ($i=0;$i<$registros_codi_depe;$i++){
					$linea_depe = pg_fetch_array($fila_codi_depe);

					$activa 						= $linea_depe['activa'];
					$codigo_dependencia 			= $linea_depe['codigo_dependencia'];
					$creador_dependencia 			= $linea_depe['creador_dependencia'];
					$dependencia_padre 				= $linea_depe['dependencia_padre'];
					$fecha_final_cambio 			= $linea_depe['fecha_final_cambio'];
					$fecha_inicial_cambio 			= $linea_depe['fecha_inicial_cambio'];
					$fecha_modificacion_dependencia = $linea_depe['fecha_modificacion'];
					$id_cambio_organico_fun 		= $linea_depe['id_cambio_organico_funcional'];
					$id_cambio_organico_funcional  	= $linea_depe['id_cambio_organico_funcional'];
					$id_dependencia 				= $linea_depe['id_dependencia'];
					$nombre_dependencia 			= $linea_depe['nombre_dependencia'];

	/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
					$fecha_modificacion_depe=$b->traducefecha($fecha_modificacion_dependencia);	
							
					$codigo_dependencia1 = strtoupper(trim(str_ireplace($search_codi_depe, "<font color='red'>$search_codi_depe</font>", $codigo_dependencia)));
					
					if($fecha_final_cambio==""){
						$fecha_final_cambio = "Actualmente";
					}
					if($fecha_inicial_cambio==""){
						$vigencia2 = "<font color='red'> Dependencia sin asignar a ningun cambio organico-funcional. Consulte con el Administrador del Sistema.</font>";
					}else{
						$vigencia2 = "<font color='green'> Vigencia ($fecha_inicial_cambio hasta $fecha_final_cambio)</font>";
					}

					echo "<div class='art1' ";
					/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
						if($desde_formulario===1){
							echo " onclick=\"javascript:cargar_modifica_dependencia('$codigo_dependencia','$nombre_dependencia','$dependencia_padre','$activa','$id_dependencia','$id_cambio_organico_funcional')\" title='Creado por $creador_dependencia ($fecha_modificacion_depe)&#13;Click para modificar datos de dependencia.'>";
						}else{
							echo "<a href=\"#\"> <script> alert('No, no hay Ahora formulario_nueva_dependencia es '+'$desde_formulario');</script>";
						}
		
					/*Aqui defino cuál va a ser el comportamiento al dar click sobre el 
					resultado obtenido*/			
						echo "($codigo_dependencia1) $nombre_dependencia | <font color='blue'> Activa - $activa </font> | $vigencia2
						<br>Dependencia Padre ( $dependencia_padre ) ";
				/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
				}
			}else{
				$caracteres_actual = strlen($search_codi_depe); // Longitud de codigo de dependencia
				if($caracteres_depend!=$caracteres_actual){
					echo "<script>
					$('#cantidad_caracteres_depe').html('$caracteres_depend');
					$('#codigo_dependencia_caracteres').slideDown('slow');
					</script>";
				}else{
					echo "<script>
						$('#codigo_dependencia_caracteres').slideUp('slow');
						$('#codigo_dependencia_ya_existe').slideUp('slow');
						$('#error_codigo_dependencia').slideUp('slow');
					</script>";
				}
			}
		}
/* Fin isset ajax (buscador_codigo_dependencias) consulta sugerencias - codigo dependencia -  Formulario de Agregar Dependencia */

/* Isset ajax (buscador_nombre_dependencias) consulta sugerencias - nombre dependencia -  Formulario Agregar Dependencia */	
		$search_nom_depe ='';
		if(isset($_POST['search_nom_depe'])){
			$search_nom_depe = $_POST['search_nom_depe'];

			$consulta_nom_depe = "select * from dependencias where nombre_dependencia ilike '%$search_nom_depe%' and activa ='SI' order by nombre_dependencia limit 5";

			$fila_nom_depe = pg_query($conectado,$consulta_nom_depe);
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_nom_depe= pg_num_rows($fila_nom_depe);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	

			if($registros_nom_depe>0 && $search_nom_depe!=''){
				for ($i=0;$i<$registros_nom_depe;$i++){
					$linea_nom_depe = pg_fetch_array($fila_nom_depe);

					$activa_nom  						= "SI";
					$codigo_nom_dependencia 			= $linea_nom_depe['codigo_dependencia'];
					$creador_nom_dependencia			= $linea_nom_depe['creador_dependencia'];
					$dependencia_nom_padre 				= $linea_nom_depe['dependencia_padre'];
					$id_cambio_organico_funcional 		= $linea_nom_depe['id_cambio_organico_funcional'];
					$id_nom_dependencia 				= $linea_nom_depe['id_dependencia'];
					$nombre_nom_dependencia 			= $linea_nom_depe['nombre_dependencia'];

					$fecha_nom_modificacion_dependencia = $linea_nom_depe['fecha_modificacion'];
/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
					$fecha_nom_modificacion_depe=$b->traducefecha($fecha_nom_modificacion_dependencia);	
									
					echo "<div class='art2'>";
					/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
						if($desde_formulario===1){
							echo "<a href=\"javascript:cargar_modifica_dependencia('$codigo_nom_dependencia','$nombre_nom_dependencia','$dependencia_nom_padre','$activa_nom','$id_nom_dependencia','$id_cambio_organico_funcional')\" title='Modificar Dependencia'>";
						}else{
							echo "<a href=\"#\"> <script> alert('No, no hay Ahora formulario_nueva_dependencia es '+'$desde_formulario');</script>";
						}

						$nombre_nom_dependencia1 = strtoupper(trim(str_ireplace($search_nom_depe, "<font color='red'>$search_nom_depe</font>", $nombre_nom_dependencia)));
					/*Aqui defino cuál va a ser el comportamiento al dar click sobre el 
					resultado obtenido*/			
						echo " ($codigo_nom_dependencia) $nombre_nom_dependencia1 | 
						<span>Dependencia Padre ( $dependencia_nom_padre ) | Activa - $activa_nom </span>
						<span class='ver_creador'> | Creado por $creador_nom_dependencia ($fecha_nom_modificacion_depe)</span>
						</a>";
				/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
				}
			}else{
				echo "<script>
					$('#error_nombre_dependencia').slideUp('slow');
					$('#nombre_dependencia_ya_existe').slideUp('slow');
				</script>";
			}
		}
/* Fin isset ajax (buscador_nombre_dependencias) consulta sugerencias - nombre dependencia -  Formulario Agregar Dependencia */	
	
/* Isset ajax (buscador_dependencia_padre) consulta sugerencias - nombre de dependencia padre - Formulario Agregar Dependencia */	
		$search_depe_padre ='';
		if(isset($_POST['search_depe_padre'])){
			$search_depe_padre = $_POST['search_depe_padre'];
		
			$consulta_depe_padre = "select * from dependencias where codigo_dependencia ilike '%$search_depe_padre%' or nombre_dependencia ilike '%$search_depe_padre%' and activa ='SI' order by nombre_dependencia limit 5";

			$fila_depe_padre = pg_query($conectado,$consulta_depe_padre);
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_codi_padre= pg_num_rows($fila_depe_padre);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	

			if($registros_codi_padre>0 && $search_depe_padre!=''){

				echo "<script>$('#error_nombre_dependencia_padre').slideUp('slow');</script>";

				for ($i=0;$i<$registros_codi_padre;$i++){
					$linea_depe_padre = pg_fetch_array($fila_depe_padre);

					$id_dependencia_padre 					= $linea_depe_padre['id_dependencia'];
					$codigo_dependencia_padre 				= $linea_depe_padre['codigo_dependencia'];
					$nombre_dependencia_padre 				= $linea_depe_padre['nombre_dependencia'];
					$dependencia_padre_padre 				= $linea_depe_padre['dependencia_padre'];
					$activa_padre 							= $linea_depe_padre['activa'];
					$creador_dependencia_padre 				= $linea_depe_padre['creador_dependencia'];
					$fecha_modificacion_dependencia_padre 	= $linea_depe_padre['fecha_modificacion'];
	/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
					$fecha_modificacion_depe_padre=$b->traducefecha($fecha_modificacion_dependencia_padre);	
											
					echo "<div class='art3'>";
					/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
						if($desde_formulario===1){
							echo "<a href=\"javascript:cargar_dependencia_padre('$nombre_dependencia_padre')\" title='Cargar Dependencia Padre'>";
						}else{
							echo "<a href=\"#\"> <script> alert('No, no hay Ahora formulario_nueva_dependencia es '+'$desde_formulario');</script>";
						}
						
						$codigo_dependencia_padre1 = strtoupper(trim(str_ireplace($search_depe_padre, "<font color='red'>$search_depe_padre</font>", $codigo_dependencia_padre)));
						$nombre_dependencia_padre1 = strtoupper(trim(str_ireplace($search_depe_padre, "<font color='red'>$search_depe_padre</font>", $nombre_dependencia_padre)));
					/*Aqui defino cuál va a ser el comportamiento al dar click sobre el 
					resultado obtenido*/			
						echo " ($codigo_dependencia_padre1) $nombre_dependencia_padre1 | 
						<span>Dependencia Padre ( $dependencia_padre_padre ) | Activa - $activa_padre </span>
						<span class='ver_creador'> | Creado por $creador_dependencia_padre ($fecha_modificacion_depe_padre)</span>
						</a>";
				/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
				}
			}elseif($registros_codi_padre==0 && $search_depe_padre!=''){// Si no encuentra la dependencia padre
				echo "<script>
					$('#error_nombre_dependencia_padre').slideDown('slow');
					$('#error_nombre_dependencia_padre2').slideUp('slow');
				</script>";
			}elseif($search_depe_padre==''){
				echo "<script>
					$('#error_nombre_dependencia_padre').slideUp('slow');
				</script>";
			}
		}
/* Fin isset ajax (buscador_dependencia_padre) consulta sugerencias - nombre de dependencia padre - Formulario Agregar Dependencia */

/******************************************************************************************/
/* Modificar Dependencia ******************************************************************/
/******************************************************************************************/

/* Isset ajax (buscador_nombre_mod_dependencias) sugerencias - nombre dependencia - Formulario Modificar Dependencia */
		$search_nom_mod_depe ='';
		if(isset($_POST['search_nombre_mod_depe'])){
			$search_nom_mod_depe 			= strtoupper($_POST['search_nombre_mod_depe']);
			$search_antiguo_nombre_mod_depe = $_POST['search_antiguo_nombre_mod_depe'];
			$consulta_nombre_mod_depe 		= "select * from dependencias where nombre_dependencia ilike '%$search_nom_mod_depe%' and activa ='SI' order by nombre_dependencia limit 5";

			$fila_codi_depe 	 = pg_query($conectado,$consulta_nombre_mod_depe);
	/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_codi_depe = pg_num_rows($fila_codi_depe);

	/* Si el nombre a modificar es el mismo que tenía, quita los errores */
			if($search_nom_mod_depe==$search_antiguo_nombre_mod_depe){
				echo"<script>
					$('.art1').slideUp('slow');
					$('#mod_dependencia_ya_existe').slideUp('slow');
				</script>";
			}
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
			if($registros_codi_depe>0 && $search_nom_mod_depe!=''){
				echo "<script>	
					$('#error_mod_nombre_dependencia').slideUp('slow');
					$('#mod_dependencia_ya_existe').slideUp('slow');
				</script>";

				for ($i=0;$i<$registros_codi_depe;$i++){
					$linea_depe = pg_fetch_array($fila_codi_depe);

					$activa  						= $linea_depe['activa'];
					$codigo_dependencia 			= $linea_depe['codigo_dependencia'];
					$creador_dependencia 			= $linea_depe['creador_dependencia'];
					$dependencia_padre 				= $linea_depe['dependencia_padre'];
					$id_cambio_organico_funcional 	= $linea_depe['id_cambio_organico_funcional'];
					$id_dependencia 				= $linea_depe['id_dependencia'];
					$nombre_dependencia 			= $linea_depe['nombre_dependencia'];

					$fecha_modificacion_dependencia = $linea_depe['fecha_modificacion'];
	/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
					$fecha_modificacion_depe 		= $b->traducefecha($fecha_modificacion_dependencia);	
					
					$nombre_dependencia1 = strtoupper(trim(str_ireplace($search_nom_mod_depe, "<font color='red'>$search_nom_mod_depe</font>", $nombre_dependencia)));

					echo "<div class='art1 fila2'>";
					/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
						if($desde_formulario===1){
							if($nombre_dependencia==$search_antiguo_nombre_mod_depe){
								echo "<a href=\"javascript:cargar_modifica_dependencia('$codigo_dependencia','$nombre_dependencia','$dependencia_padre','$activa','$id_dependencia','$id_cambio_organico_funcional')\">";		
							}else{
								echo "<a href=\"javascript:error_modificar_dependencia()\">";
							}
						}else{
							echo "<a href=\"#\"> <script> alert('No, no hay Ahora formulario_nueva_dependencia es '+'$desde_formulario');</script>";
						}
					/*Aqui defino cuál va a ser el comportamiento al dar click sobre el resultado obtenido*/			
						// echo " ($codigo_dependencia) $nombre_dependencia1 | 
						// <span>Dependencia Padre ( $dependencia_padre ) | Activa - $activa </span>
						// <span class='ver_creador'> | Creado por $creador_dependencia ($fecha_modificacion_depe)</span>
						// </a>";
						echo " ($codigo_dependencia) $nombre_dependencia1 <br><span>Dependencia Padre ( $dependencia_padre ) <br>Activa - $activa </span></a>";
				/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>"; //cierra div class='art1'
				}
			}else{
				echo"<script>
					$('.art1').slideUp('slow');
					$('#mod_dependencia_ya_existe').slideUp('slow');
					$('#error_mod_nombre_dependencia').slideUp('slow');
					$('#valida_minimo_mod_nombre').slideUp('slow');
					$('#valida_maximo_mod_nombre').slideUp('slow');
				</script>";
			}	
		}
/* Fin isset ajax (buscador_nombre_mod_dependencias) sugerencias - nombre dependencia - Formulario Modificar Dependencia */

/* Isset ajax (buscador_mod_dependencia_padre) sugerencias - nombre dependencia padre - Formulario Modificar Dependencia */	

		$search_mod_depe_padre ='';
		if(isset($_POST['search_mod_depe_padre'])){
			$search_mod_depe_padre = $_POST['search_mod_depe_padre'];
			$search_antiguo_mod_padre=$_POST['search_antiguo_mod_padre'];

		$consulta_depe_padre = "select * from dependencias where codigo_dependencia ilike '%$search_mod_depe_padre%' or nombre_dependencia ilike '%$search_mod_depe_padre%' and activa ='SI' order by nombre_dependencia limit 5";

		$fila_depe_padre 			= pg_query($conectado,$consulta_depe_padre);
	/* Calcula el numero de registros que genera la consulta anterior. */
		$registros_mod_codi_padre 	= pg_num_rows($fila_depe_padre);

	/* Si el nombre a modificar es el mismo que tenía, quita los errores */
			if(strtoupper($search_mod_depe_padre)==$search_antiguo_mod_padre){
				echo"<script>
					$('#error_nombre_mod_dependencia_padre').slideUp('slow');
					$('#error_nombre_mod_dependencia_padre2').slideUp('slow');
					$('#error_nombre_mod_dependencia_padre3').slideUp('slow');
				</script>";
			}else{
				if($search_mod_depe_padre==""){
					echo "<script>
						$('#error_nombre_mod_dependencia_padre').slideUp('slow');
						$('#error_nombre_mod_dependencia_padre2').slideUp('slow');
						$('#error_nombre_mod_dependencia_padre3').slideUp('slow');
					</script>";
				}
		/* Recorre el array generado e imprime uno a uno los resultados. */	
				if($registros_mod_codi_padre>0 && $search_mod_depe_padre!=''){
					echo"<script>
						$('#error_nombre_mod_dependencia_padre').slideUp('slow');
						$('#error_nombre_mod_dependencia_padre2').slideUp('slow');
						$('#error_nombre_mod_dependencia_padre3').slideUp('slow');
					</script>";
						
					for ($i=0;$i<$registros_mod_codi_padre;$i++){
						$linea_depe_padre = pg_fetch_array($fila_depe_padre);

						$id_dependencia_padre 					= $linea_depe_padre['id_dependencia'];
						$codigo_dependencia_padre 				= $linea_depe_padre['codigo_dependencia'];
						$nombre_dependencia_padre 				= $linea_depe_padre['nombre_dependencia'];
						$dependencia_padre_padre 				= $linea_depe_padre['dependencia_padre'];
						$activa_padre 							= $linea_depe_padre['activa'];
						$creador_dependencia_padre 				= $linea_depe_padre['creador_dependencia'];
						$fecha_modificacion_dependencia_padre 	= $linea_depe_padre['fecha_modificacion'];
		/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016" */
						$fecha_modificacion_depe_padre 			= $b->traducefecha($fecha_modificacion_dependencia_padre);	
						
						$codigo_dependencia_padre1 = strtoupper(trim(str_ireplace($search_mod_depe_padre, "<font color='red'>$search_mod_depe_padre</font>", $codigo_dependencia_padre)));
						$nombre_dependencia_padre1 = strtoupper(trim(str_ireplace($search_mod_depe_padre, "<font color='red'>$search_mod_depe_padre</font>", $nombre_dependencia_padre)));

						echo "<div class='art2 fila2'";
						/* Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href" */;
							if($desde_formulario===1){
								echo " onclick=\"javascript:cargar_dependencia_mod_padre('$nombre_dependencia_padre')\">";
							}else{
								echo "> <script> alert('No, no hay Ahora formulario_nueva_dependencia es '+'$desde_formulario');</script>";
							}
			
					/* Aqui defino cuál va a ser el comportamiento al dar click sobre el resultado obtenido */			
							echo " ($codigo_dependencia_padre1) $nombre_dependencia_padre1 <br>
							<span>Dependencia Padre ( $dependencia_padre_padre )<br>Activa - $activa_padre</span>";
					/* Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados */
						echo "</div>"; //cierra div class='art2'
					}
				}else{
					echo "<script>
						$('#error_nombre_mod_dependencia_padre').slideDown('slow');
						$('#error_nombre_mod_dependencia_padre2').slideUp('slow');
						$('#error_nombre_mod_dependencia_padre3').slideUp('slow');
					</script>";
				}
			}
		}
/* Fin isset ajax (buscador_mod_dependencia_padre) sugerencias - nombre dependencia padre - Formulario Modificar Dependencia */	
?>

