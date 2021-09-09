<?php 
	require_once('../login/conexion2.php');

/* Isset parametro para imprimir variable recibida del modulo parametrizacion */
	if(isset($_POST['parametro'])){
		$parametro = $_POST['parametro'];

		switch ($parametro) {
			case '1': // Tipo documento Formulario Radicacion Entrada
				$query="select * from tipo_doc_termino order by tipo_documento";
				$encabezado="
				<center>
					<table border='0' width='75%'>
						<tr class='row'>
							<td width='15%'>
								Nombre del tipo de documento
							</td>
							<td width='50%'>
								Descripcion del tipo de documento
							</td>
							<td width='15%'>
								Termino (Dias Habiles)
							</td>
							<td width='15%'>
								Activo
							</td>
						</tr>";
				break;
			case '2': // Tipo documento (Termino PQR)
				$query="select * from tipo_doc_termino_pqr order by tipo_documento";
				$encabezado="<center><table border='0' width='100%'><tr class='row'><td width='10%'>Nombre del tipo de documento</td><td width='70%'>Descripcion</td><td width='10%'>Termino (Dias Habiles)</td><td width='10%'>Activo</td></tr>";
				break;
			case '3':
				$query="select * from tipo_radicado order by codigo_tipo_radicado";
				$encabezado="
				<center>
					<table border='0' width='75%'>
						<tr class='row'>
							<td width='20%'>
								Codigo del tipo de Radicado
							</td>
							<td width='80%'>
								Nombre del Tipo de Radicado
							</td>
						</tr>";
				break;
			case '4': // Administrador de secuencias de radicacion
				// $query="select * from consecutivos order by year, codigo_dependencia,tipo_radicado";
				$query="select * from consecutivos c join dependencias d on c.codigo_dependencia=d.codigo_dependencia  order by year, c.codigo_dependencia,c.tipo_radicado";
				$encabezado="
				<center>
					<table border='0' width='80%'>
						<tr class='row'>
							<td width='25%'>
								Año de la secuencia
							</td>
							<td width='25%'>
								Codigo de la Dependencia
							</td>
							<td width='25%'>
								Tipo de Radicado
							</td>
							<td width='25%'>
								Codigo dependencia - Secuencia padre.
							</td>
						</tr>";
				break;	
			default:
				# code...
				break;
		}

		$fila = pg_query($conectado,$query);

		if($fila==false){ // Valida si la tabla tipo_doc_termino existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla M de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}
	/* Calcula el numero de registros que genera la consulta anterior. */
		$registros= pg_num_rows($fila);
	/* Imprimo encabezado de tabla resultado */	
		echo $encabezado;
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros>0){
			$num_fila=0; // Variable declarada para poner color a la fila según ésta variable.
			for ($i=0;$i<$registros;$i++){
				$linea = pg_fetch_array($fila);

				switch ($parametro) {
					case '1':
						$id=$linea['codigo_tipo_doc'];
						$tipo_doc = $linea['tipo_documento'];
						$descripcion_tipo_documento=$linea['descripcion_tipo_documento'];
						$tiempo_tram = $linea['tiempo_tramite'];
						$estado = $linea['activo'];
						$tabla="
							<td class='modif' onclick=\"javascript:cargar_modifica_tipo_documento('$id','$tipo_doc','$descripcion_tipo_documento','$tiempo_tram','$estado')\" > $tipo_doc </td> 
							<td> $descripcion_tipo_documento </td>
							<td> $tiempo_tram </td>
							<td> $estado </td> 
						";
						$agrega_aqui=" <br><h3>Si desea ingresar un nuevo tipo de documento haga click <a href='javascript:abrirVentanaCrearTipoDocumento();'>aqui</a></h3><hr>";
						break;
					case '2':
						$id=$linea['codigo_tipo_doc'];
						$tipo_doc = $linea['tipo_documento'];
						$descripcion_tipo_doc = $linea['descripcion_tipo_documento'];
						$tiempo_tram = $linea['tiempo_tramite'];
						$estado = $linea['activo'];
						$tabla="
							<td class='modif' onclick=\"javascript:cargar_modifica_tipo_documento_pqr('$id','$tipo_doc','$descripcion_tipo_doc','$tiempo_tram','$estado')\" > $tipo_doc </td> 
							<td>$descripcion_tipo_doc</td>
							<td> $tiempo_tram </td>
							<td> $estado </td> 
						";
						$agrega_aqui=" <br><h3>Si desea ingresar un nuevo tipo de documento haga click <a href='javascript:abrir_ventana_crear_tipo_documento_pqr();'>aqui</a></h3><hr>";
						break;	
					case '3':
						$id=$linea['codigo_tipo_radicado'];
						$tipo_radicado = $linea['tipo_radicado'];

						$tabla="
							<td title='Si desea modificar este tipo de radicado debe hacerlo por base de datos'><center> $id </center></td> 
							<td  title='Si desea modificar este tipo de radicado debe hacerlo por base de datos'> $tipo_radicado </td>
						";
						$agrega_aqui=" <br><h3>Si desea ingresar un nuevo tipo de radicado haga click <a href='javascript:abrir_ventana_crear_tipo_radicado();'>aqui</a></h3><hr>";
						break;
					case '4':
						$year 							= $linea['year'];
						$codigo_dependencia 			= $linea['codigo_dependencia'];
						$tipo_radicado 					= $linea['tipo_radicado'];
						$dependencia_consecutivo_padre 	= $linea['dependencia_consecutivo_padre'];
						$nombre_dependencia 			= $linea['nombre_dependencia'];

						switch ($tipo_radicado) { // Nombre de la secuencia de radicacion
							case '1':
								$nombre_secuencia = "Radicacion de Entrada";
								break;
							case '2':
								$nombre_secuencia = "Radicacion de Salida";
								break;
							case '3':
								$nombre_secuencia = "Radicacion Normal";
								break;
							case '4':
								$nombre_secuencia = "Radicacion Interna";
								break;
							case '5':
								$nombre_secuencia = "Radicacion Plantilla Generica";
								break;
							
							default:
								# code...
								break;
						}
						$tabla="
							<td title='Modificar secuencia' class='modif' onclick=\"javascript:cargar_modifica_secuencia('$codigo_dependencia','$tipo_radicado','$dependencia_consecutivo_padre','$nombre_dependencia','$nombre_secuencia')\" ><center> $year </center></td> 
							<td><center>($codigo_dependencia)<br>$nombre_dependencia</center></td>
							<td><center> $tipo_radicado ( $nombre_secuencia )</center></td>
							<td><center> $dependencia_consecutivo_padre </center></td> 	
						";
						$agrega_aqui=" <br><h3>Si desea ingresar una nueva secuencia haga click <a href='javascript:abrir_ventana_crear_secuencia();'>aqui</a></h3><hr>";
						break;	
					default:
						# code...
						break;
				}

				echo "<tr class='art1";
					if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
					else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
				echo " encabeza'>"; 
/* Aqui se imprimen los resultados en td en la tabla */;
					echo $tabla;

				echo "</tr>";//cierra tr class='art'
				$num_fila++; 
			}
		}else{
			switch ($parametro) {
				case '4':
					$agrega_aqui=" <br><h3>Si desea ingresar una nueva secuencia haga click <a href='javascript:abrir_ventana_crear_secuencia();'>aqui</a></h3><hr>";
					break;
			}	
		}			
		echo "</table></center>";
		echo $agrega_aqui;
	}
/* Isset parametro para imprimir variable recibida del modulo parametrizacion */
/* Inicio isset "search_tipo_doc" */
	if(isset($_POST['search_tipo_doc'])){ // Buscador nombre tipo documento - Formulario Agregar Tipo Documento
		$search=$_POST['search_tipo_doc'];
		$search=strtoupper($search);
		$query="select * from tipo_doc_termino where tipo_documento like '%$search%'";
		
		$fila = pg_query($conectado,$query);

		if($fila==false){ // Valida si la tabla tipo_doc_termino existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla M de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}

	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_td= pg_num_rows($fila);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_td>0 && $search!=''){
			do{
				$num_fila=0;
				for ($i=0;$i<$registros_td;$i++){
					$linea = pg_fetch_array($fila);

					$id = $linea['codigo_tipo_doc'];
					$tipo_documento = $linea['tipo_documento'];
					$tiempo_tramite = $linea['tiempo_tramite'];
					$activo = $linea['activo'];
					if($activo=="SI"){
						$est="ACTIVO";
					}else{
						$est="INACTIVO";
					}
						
					echo "<div class='art_exp";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "' id='art' style='text-decoration: none;'>"; 
	/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
						echo "<a href=\"javascript:cargar_modifica_tipo_documento('$id','$tipo_documento','$tiempo_tramite','$activo')\">";

	/*Aqui defino lo que se muestra del resultado obtenido*/						
							echo "
								<b>$tipo_documento</b> ( Termino $tiempo_tramite dias habiles ) $est 
							</a>";
	/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
					$num_fila++; 
				}
			}while ($fila=pg_fetch_assoc($fila));
		}else{
			echo "";
		}
	}
/* Fin isset "search_tipo_doc" */	
/* Inicio isset "search_tipo_doc_mod" */
	if(isset($_POST['search_tipo_doc_mod'])){ // Buscador nombre tipo documento - Formulario Modificar Tipo Documento
		//$search_mod=;
		$search_mod=strtoupper($_POST['search_tipo_doc_mod']);
		$search_mod_ant=$_POST['search_tipo_doc_mod_ant'];
		$query="select * from tipo_doc_termino where activo!='NO' and tipo_documento like '%$search_mod%'";
		
		$fila_mod = pg_query($conectado,$query);

		if($fila_mod==false){ // Valida si la tabla tipo_doc_termino existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla M de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}
		/* Si el nombre a modificar es el mismo que tenía, quita los errores */
			if(strtoupper($search_mod)==$search_mod_ant){
				echo"<script>
					$('#sugerencias_tipo_doc_mod').html('');
				</script>";
			}
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_td= pg_num_rows($fila_mod);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_td>0 && $search_mod!=''){
			do{
				$num_fila=0;
				for ($i=0;$i<$registros_td;$i++){
					$linea_mod = pg_fetch_array($fila_mod);

					$id = $linea_mod['codigo_tipo_doc'];
					$tipo_documento = $linea_mod['tipo_documento'];
					$tiempo_tramite = $linea_mod['tiempo_tramite'];
					$activo = $linea_mod['activo'];
					
					echo "<div class='art_mod";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "' id='art_mod' style='text-decoration: none;'>"; 
	/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/
					if($tipo_documento==$search_mod_ant){
						echo "<a href=\"javascript:id_anterior('$tipo_documento')\">";
					}else{
						echo "<a href=\"javascript:valida_mod_td_ya_existe()\">";						
					//	echo "<a href=\"javascript:cargar_modifica_tipo_documento('$id','$tipo_documento','$tiempo_tramite','$activo')\">";
					}	

	/*Aqui defino lo que se muestra del resultado obtenido*/						
						echo "
							<b>$tipo_documento</b> ( Termino $tiempo_tramite dias habiles )  
							<span> $activo </span>
						</a>";
	/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
					$num_fila++; 
				}
			}while ($fila_mod=pg_fetch_assoc($fila_mod));
		}else{
			echo "";
		}
	}
/* Fin isset "search_tipo_doc_mod" */		
/* Inicio isset "search_tipo_doc_pqr" */
	if(isset($_POST['search_tipo_doc_pqr'])){ // Buscador nombre tipo documento - Formulario Agregar Tipo Documento
		$search_pqr=$_POST['search_tipo_doc_pqr'];
		$search_pqr=strtoupper($search_pqr);
		$query="select * from tipo_doc_termino_pqr where tipo_documento like '%$search_pqr%'";
		
		$fila_pqr = pg_query($conectado,$query);

		if($fila_pqr==false){ // Valida si la tabla tipo_doc_termino existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla M de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}

	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_td_pqr= pg_num_rows($fila_pqr);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_td_pqr>0 && $search_pqr!=''){
			do{
				$num_fila_pqr=0;
				for ($i=0;$i<$registros_td_pqr;$i++){
					$linea_pqr = pg_fetch_array($fila_pqr);

					$id = $linea_pqr['codigo_tipo_doc'];
					$tipo_documento = $linea_pqr['tipo_documento'];
					$descripcion_tipo_doc=$linea_pqr['descripcion_tipo_documento'];
					$tiempo_tramite = $linea_pqr['tiempo_tramite'];
					$activo = $linea_pqr['activo'];
					if($activo=="SI"){
						$est="ACTIVO";
					}else{
						$est="INACTIVO";
					}
						
					echo "<div class='art";
						if ($num_fila_pqr%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "' id='art3' style='text-decoration: none;'>"; 
	/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
						echo "<a href=\"javascript:cargar_modifica_tipo_documento_pqr('$id','$tipo_documento','$descripcion_tipo_doc','$tiempo_tramite','$activo')\">";

	/*Aqui defino lo que se muestra del resultado obtenido*/						
							echo "
								<b>$tipo_documento</b> ( Termino $tiempo_tramite dias habiles ) $est 
							</a>";
	/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
					$num_fila_pqr++; 
				}
			}while ($fila_pqr=pg_fetch_assoc($fila_pqr));
		}else{
			echo "";
		}
	}
/* Fin isset "search_tipo_doc_pqr" */	
//* Inicio isset "search_tipo_doc_pqr_mod" */
	if(isset($_POST['search_tipo_doc_pqr_mod'])){ // Buscador nombre tipo documento - Formulario Modificar Tipo Documento PQR
		$search_mod_pqr=strtoupper($_POST['search_tipo_doc_pqr_mod']);
		$search_mod_ant_pqr=$_POST['search_tipo_doc_mod_ant_mod'];

		$query="select * from tipo_doc_termino_pqr where activo!='NO' and tipo_documento like '%$search_mod_pqr%'";
		$fila_mod_pqr = pg_query($conectado,$query);

		if($fila_mod_pqr==false){ // Valida si la tabla tipo_doc_termino existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla M de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}
		/* Si el nombre a modificar es el mismo que tenía, quita los errores */
			if(strtoupper($search_mod_pqr)==$search_mod_ant_pqr){
				echo"<script>
					$('#sugerencias_tipo_doc_mod_pqr').html('');
				</script>";
			}
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_td_pqr= pg_num_rows($fila_mod_pqr);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_td_pqr>0 && $search_mod_pqr!=''){
			do{
				$num_fila=0;
				for ($i=0;$i<$registros_td_pqr;$i++){
					$linea_mod_pqr = pg_fetch_array($fila_mod_pqr);

					$id = $linea_mod_pqr['codigo_tipo_doc'];
					$tipo_documento = $linea_mod_pqr['tipo_documento'];
					$descripcion_tipo_doc= $linea_mod_pqr['descripcion_tipo_documento'];
					$tiempo_tramite = $linea_mod_pqr['tiempo_tramite'];
					$activo = $linea_mod_pqr['activo'];
					if($activo=="SI"){
						$est="ACTIVO";
					}else{
						$est="INACTIVO";
					}
					
					echo "<div class='art_mod";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "' id='art4' style='text-decoration: none;'>"; 
	/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/
					if($tipo_documento==$search_mod_ant_pqr){
						echo "<a href=\"javascript:id_anterior_pqr('$tipo_documento')\">";
					}else{
						echo "<a href=\"javascript:valida_mod_td_pqr_ya_existe()\">";						
					}	

	/*Aqui defino lo que se muestra del resultado obtenido*/						
						echo "
							<b>$tipo_documento</b> ( Termino $tiempo_tramite dias habiles )  
							<span> $est </span>
						</a>";
	/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
					$num_fila++; 
				}
			}while ($fila_mod_pqr=pg_fetch_assoc($fila_mod_pqr));
		}else{
			echo "";
		}
	}
/* Fin isset "search_tipo_doc_pqr_mod" */	
/* Inicio isset "search_tipo_radicado" */
	if(isset($_POST['search_tipo_radicado'])){ // Buscador nombre tipo radicado - Formulario Agregar Tipo Radicado
		$search_tipo_rad=$_POST['search_tipo_radicado'];
		$search_tipo_rad=strtoupper($search_tipo_rad);
		$query="select * from tipo_radicado where codigo_tipo_radicado like '%$search_tipo_rad%'";
		
		$fila_tipo_radi = pg_query($conectado,$query);

		if($fila_tipo_radi==false){ // Valida si la tabla tipo_doc_termino existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla M de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}

	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_tipo_radi= pg_num_rows($fila_tipo_radi);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_tipo_radi>0 && $search_tipo_rad!=''){
			do{
				$num_fila=0;
				for ($i=0;$i<$registros_tipo_radi;$i++){
					$linea_tipo_radi = pg_fetch_array($fila_tipo_radi);

					$id = $linea_tipo_radi['codigo_tipo_radicado'];
					$tipo_documento = $linea_tipo_radi['tipo_radicado'];
										
					echo "<div class='art_mod";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "' id='art5' style='text-decoration: none;'>"; 
	/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/
					echo "<a href=\"javascript:tr_existe()\">";

	/*Aqui defino lo que se muestra del resultado obtenido*/						
						echo "
							<b>$tipo_documento</b> ( $id )  
						</a>";
	/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
					$num_fila++; 
				}
			}while ($fila_tipo_radi=pg_fetch_assoc($fila_tipo_radi));
		}else{
			echo "";
		}	

	}
/* Fin isset "search_tipo_radicado" */
/* Inicio isset 'search_select_tipo_radicado' */
	if(isset($_POST['search_select_tipo_radicado'])){  // Variable para armar select tipo radicado
		$tipo_rad=$_POST['search_select_tipo_radicado'];
		$query="select * from tipo_radicado order by 1";
		$fila_select_td = pg_query($conectado,$query);

		if($fila_select_td==false){ // Valida si la tabla tipo_radicado existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla std de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}

	/* Define si el select es pre cargado o no. */
		if($tipo_rad=="NEW"){
			$sel="selected='selected'";
		}else{
			$sel="";
		}	
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_select_td= pg_num_rows($fila_select_td);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		echo "
		<select name='tipo_rad' id='tipo_rad' class='select_opciones' onchange='valida_tipo_rad()'>
			<option value='' $sel >-- Seleccione Tipo de radicado --</option>"; 
			$num_fila=0;
			for ($i=0;$i<$registros_select_td;$i++){
				$linea_select_td = pg_fetch_array($fila_select_td);

				$codigo_tipo_radicado = $linea_select_td['codigo_tipo_radicado'];
				$tipo_radicado = $linea_select_td['tipo_radicado'];
	/* Defino si la opcion del select debe ir pre seleccionada */			
			if($tipo_rad==$codigo_tipo_radicado){
				$sel_option="selected='selected'";
			}else{
				$sel_option="";
			}
	/*Aqui defino lo que se muestra del resultado obtenido*/						
				echo "<option value='$codigo_tipo_radicado' $sel_option> $codigo_tipo_radicado ($tipo_radicado) </option>";
	/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
				$num_fila++; 
			}
		echo "</select>";//cierra select 		
	}
/* Fin isset 'search_select_tipo_radicado' */
/* Inicio isset "search_codigo_depe_sec" */
	if(isset($_POST['search_codigo_depe_sec'])){ // Buscador codigo_dependencia - Formulario Agregar Secuencia
		$search_codigo_depe_sec=strtoupper($_POST['search_codigo_depe_sec']);
		$query="select * from dependencias where codigo_dependencia like '%$search_codigo_depe_sec%' or nombre_dependencia like '%$search_codigo_depe_sec%'";
		
		$fila_depe_sec = pg_query($conectado,$query);

		if($fila_depe_sec==false){ // Valida si la tabla tipo_doc_termino existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla D de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}

	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_codigo_depe_sec= pg_num_rows($fila_depe_sec);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_codigo_depe_sec>0 && $search_codigo_depe_sec!=''){
			do{
				$num_fila=0;
				for ($i=0;$i<$registros_codigo_depe_sec;$i++){
					$linea_codigo_depe_sec = pg_fetch_array($fila_depe_sec);

					$nombre_dependencia = $linea_codigo_depe_sec['nombre_dependencia'];
					$codigo_dependencia = $linea_codigo_depe_sec['codigo_dependencia'];
				
					$codigo_dependencia1 = trim(str_ireplace($search_codigo_depe_sec, "<font color='red'>$search_codigo_depe_sec</font>", $codigo_dependencia));
					$nombre_dependencia1 = str_ireplace($search_codigo_depe_sec, "<font color='red'>$search_codigo_depe_sec</font>", $nombre_dependencia);
					
					if($codigo_dependencia==$search_codigo_depe_sec){
						echo "<script>$('#sugerencias_codigo_sec').html('');
							carga_select_tipo_radicado();
						</script>";
					}else{
						echo "<div class='art_mod";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
							else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
						echo "' id='art6' style='text-decoration: none;'>"; 
		/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/
						echo "<a href=\"javascript:cargar_codigo_depe('$codigo_dependencia')\">";

		/*Aqui defino lo que se muestra del resultado obtenido*/						
							echo "
								<b> $codigo_dependencia1 ($nombre_dependencia1)</b>  
							</a>";
		/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
						echo "</div>";//cierra div class='art'
						$num_fila++; 
					}
				}
			}while ($fila_depe_sec=pg_fetch_assoc($fila_depe_sec));
		}else{
			echo "";
		}	
	}
/* Fin isset "search_codigo_depe_sec" */
/* Inicio isset 'valida_cod_depe' */
	if(isset($_POST['valida_cod_depe'])){ // Buscador codigo_dependencia - Formulario Agregar Secuencia
		$valida_cod_depe=$_POST['valida_cod_depe'];
		$valida_tr=$_POST['valida_tr'];
		$query="select * from consecutivos WHERE tipo_radicado='$valida_tr' and codigo_dependencia='$valida_cod_depe'";
		
		$fila_valida_cod_depe = pg_query($conectado,$query);
		$linea_valida_cod_depe = pg_fetch_array($fila_valida_cod_depe);
		if($linea_valida_cod_depe==false){  // Valida si existe la secuencia del tipo de radicado - dependencia
			echo"<script>
				$('#error_consecutivo_invalido').slideUp('slow');
			</script>";
		}else{
			echo"<script>
				$('#error_consecutivo_invalido').slideDown('slow');
			</script>";
		}	
	}
/* Fin isset 'valida_cod_depe' */
/* Inicio isset "search_codigo_depe_padre_sec" */
	if(isset($_POST['search_codigo_depe_padre_sec'])){ // Buscador codigo_dependencia_padre - Formulario Agregar Secuencia
		$search_codigo_depe_padre_sec=strtoupper($_POST['search_codigo_depe_padre_sec']);
		$query="select * from dependencias where codigo_dependencia ilike '%$search_codigo_depe_padre_sec%' or nombre_dependencia ilike '%$search_codigo_depe_padre_sec%'";
		
		$fila_depe_padre_sec = pg_query($conectado,$query);

		if($fila_depe_padre_sec==false){ // Valida si la tabla dependencias existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla D de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}

	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_codigo_depe_padre_sec= pg_num_rows($fila_depe_padre_sec);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_codigo_depe_padre_sec>0){
			for ($i=0;$i<$registros_codigo_depe_padre_sec;$i++){
				$linea_codigo_depe_padre_sec = pg_fetch_array($fila_depe_padre_sec);

				$nombre_dependencia = $linea_codigo_depe_padre_sec['nombre_dependencia'];
				$codigo_dependencia = $linea_codigo_depe_padre_sec['codigo_dependencia'];
			
				$codigo_dependencia1 = trim(str_ireplace($search_codigo_depe_padre_sec, "<font color='red'>$search_codigo_depe_padre_sec</font>", $codigo_dependencia));
				$nombre_dependencia1 = trim(str_ireplace($search_codigo_depe_padre_sec, "<font color='red'>$search_codigo_depe_padre_sec</font>", $nombre_dependencia));
				
				if($codigo_dependencia==$search_codigo_depe_padre_sec){
					echo "<script>
						$('#sugerencias_codigo_sec_padre').html('');
					</script>";
				}else{
					echo "<div class='art_mod' id='art7' style='text-decoration: none;'>"; 

	/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/
					echo "<a href=\"javascript:cargar_codigo_depe_padre('$codigo_dependencia')\">";

	/*Aqui defino lo que se muestra del resultado obtenido*/						
						echo "
							<b> $codigo_dependencia1 ($nombre_dependencia1)</b>  
						</a>";
	/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
				}
			}
		}else{
			echo "<script>$('#error_codigo_sec_padre_invalido').slideDown('slow');</script>";	
		}		
	}
/* Fin isset "search_codigo_depe_padre_sec" */

/* Inicio isset "search_codigo_depe_padre_sec_mod" */
	if(isset($_POST['search_codigo_depe_padre_sec_mod'])){ // Buscador codigo_dependencia_padre - Formulario Agregar Secuencia
		$search_codigo_depe_padre_sec_mod=strtoupper($_POST['search_codigo_depe_padre_sec_mod']);
		$query="select * from dependencias where codigo_dependencia like '%$search_codigo_depe_padre_sec_mod%'";
		
		$fila_depe_padre_sec = pg_query($conectado,$query);

		if($fila_depe_padre_sec==false){ // Valida si la tabla dependencias existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla D de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}

	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_codigo_depe_padre_sec= pg_num_rows($fila_depe_padre_sec);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_codigo_depe_padre_sec>0){
			do{
				for ($i=0;$i<$registros_codigo_depe_padre_sec;$i++){
					$linea_codigo_depe_padre_sec = pg_fetch_array($fila_depe_padre_sec);

					$nombre_dependencia = $linea_codigo_depe_padre_sec['nombre_dependencia'];
					$codigo_dependencia = $linea_codigo_depe_padre_sec['codigo_dependencia'];
				
					$codigo_dependencia1 = trim(str_ireplace($search_codigo_depe_padre_sec_mod, "<font color='red'>$search_codigo_depe_padre_sec_mod</font>", $codigo_dependencia));
					
					if($codigo_dependencia==$search_codigo_depe_padre_sec_mod){
						echo "<script>
							$('#sugerencias_codigo_dependencia_padre_sec_mod').html('');
						</script>";
					}else{
						echo "<div class='art_mod' id='art8' style='text-decoration: none;'>"; 

		/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/
						echo "<a href=\"javascript:cargar_codigo_depe_padre_mod('$codigo_dependencia')\">";

		/*Aqui defino lo que se muestra del resultado obtenido*/						
							echo "
								<b> $codigo_dependencia1 ($nombre_dependencia)</b>  
							</a>";
		/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
						echo "</div>";//cierra div class='art'
					}
				}
			}while ($fila_depe_padre_sec=pg_fetch_assoc($fila_depe_padre_sec));
		}else{
			echo "<script>$('#error_codigo_dependencia_padre_sec_invalido_mod').slideDown('slow');</script>";	
		}		
	}
/* Fin isset "search_codigo_depe_padre_sec_mod" */


?>