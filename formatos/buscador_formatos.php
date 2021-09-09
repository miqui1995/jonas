<?php 
	require_once("../login/validar_inactividad.php");
	
	if(isset($_POST['numero_caja'])){

		$numero_caja = strtoupper($_POST['numero_caja']); 
		$numero_caja1 = strtoupper($_POST['numero_caja1']); 
		$numero_caja2 = strtoupper($_POST['numero_caja2']); 
		$num = $_POST['num']; 
		$entidad = $_POST['entidad']; 
		$fondo1 = $_POST['fondo1']; 
		$fondo2 = $_POST['fondo2']; 
		$seccion1 = $_POST['seccion1']; 
		$seccion2 = $_POST['seccion2']; 
		$subseccion1 = $_POST['subseccion1']; 
		$subseccion2 = $_POST['subseccion2']; 
		$cantidad_carp1 = $_POST['cantidad_carp1']; 
		$cantidad_carp2 = $_POST['cantidad_carp2']; 
		$fecha_inicial_01 = $_POST['fecha_inicial_1']; 
		$fecha_final_01 = $_POST['fecha_final_1']; 

		$query_caja="select * from expedientes e join ubicacion_topografica u on e.codigo_ubicacion_topografica = u.id_ubicacion where u.nombre_nivel=trim('$numero_caja') order by e.id";
		
		$fila_caja = pg_query($conectado,$query_caja);
		$registros_caja= pg_num_rows($fila_caja);

		$myObj = array(); 	
		$myObj1 = array(); 	

		if($registros_caja==0){			
			echo "<script>$('#error_numero_caja$num').slideDown('slow'); limpia_formulario($num);</script>";
		}else{
		/* Desde aqui defino las fechas extremas */	
			$query_fechas_extremas="select i.fecha_inicial, i.fecha_final from radicado r join inventario i on r.numero_radicado=i.radicado_jonas join expedientes e on r.id_expediente=e.id_expediente join ubicacion_topografica u on e.codigo_ubicacion_topografica = u.id_ubicacion where u.nombre_nivel=trim('$numero_caja') order by e.id";
			$fila_fechas_extremas = pg_query($conectado,$query_fechas_extremas);
			$registros_fechas_extremas= pg_num_rows($fila_fechas_extremas);

			$fecha_inicial_1=date("j/n/Y");
			$fecha_final_1 = date("j/n/Y",strtotime($fecha_inicial_1."- 1 year"));

			do{			// Aqui el analisis de la fecha se hace con el formato DD/MM/YYYY
				for ($i=0;$i<$registros_fechas_extremas;$i++){
					$linea_f = pg_fetch_array($fila_fechas_extremas);

					$fecha_inicial = $linea_f['fecha_inicial'];

					$fecha_i1=explode("/",$fecha_inicial_1);
					$dia_i1 = $fecha_i1[0]; // dia
					$mes_i1 = $fecha_i1[1]; // mes
					$year_i1= $fecha_i1[2]; // año

					$fecha_i=explode("/",$fecha_inicial);
					$dia_i = $fecha_i[0]; // dia
					$mes_i = $fecha_i[1]; // mes
					$year_i= $fecha_i[2]; // año

					if($year_i1<$year_i){
						$fecha_inicial_real=$fecha_inicial_1;
					}elseif($year_i1==$year_i){
						if($mes_i1<$mes_i){
							$fecha_inicial_real=$fecha_inicial_1;
						}elseif($mes_i1==$mes_i){
							if($dia_i1<$dia_i){
								$fecha_inicial_real=$fecha_inicial_1;
							}else{
								$fecha_inicial_real=$fecha_inicial;
							}
						}else{
							$fecha_inicial_real=$fecha_inicial;
						}
					}else{
						$fecha_inicial_real=$fecha_inicial;
					}

					$fecha_final = $linea_f['fecha_final'];

					$fecha_f1=explode("/",$fecha_final_1);
					$dia_f1 = $fecha_f1[0]; // dia
					$mes_f1 = $fecha_f1[1]; // mes
					$year_f1= $fecha_f1[2]; // año

					$fecha_f=explode("/",$fecha_final);
					$dia_f = $fecha_f[0]; // dia
					$mes_f = $fecha_f[1]; // mes
					$year_f= $fecha_f[2]; // año

					if($year_f1>$year_f){
						$fecha_final_real=$fecha_final_1;
					}elseif($year_f1==$year_f){
						if($mes_f1>$mes_f){
							$fecha_final_real=$fecha_final_1;
						}elseif($mes_f1==$mes_f){
							if($dia_f1>$dia_f){
								$fecha_final_real=$fecha_final_1;
							}else{
								$fecha_final_real=$fecha_final;
							}
						}else{
							$fecha_final_real=$fecha_final;
						}
					}else{
						$fecha_final_real=$fecha_final;
					}
					
					$fecha_inicial_1=$fecha_inicial_real;
					$fecha_final_1=$fecha_final_real;
					
				}
			}while ($fila_fechas_extremas=pg_fetch_assoc($fila_fechas_extremas));
		/* Hasta aqui defino las fechas extremas */	

		/* Desde aqui defino las variables de la caja */	
			$myObj['entidad']= $entidad;
			$myObj['fecha_inicial']= $fecha_inicial_real;
			$myObj['fecha_final']= $fecha_final_real;
			
			switch ($num) {
				case 1:
					$myObj['fondo']= $fondo1;
					$myObj['seccion']= $seccion1;
					$myObj['subseccion']= $subseccion1;
					$myObj['numero_caja']= $numero_caja1;
					$myObj['cantidad_carpetas']= $cantidad_carp1;
					break;
				case 2:
					$myObj['fondo']= $fondo2;
					$myObj['seccion']= $seccion2;
					$myObj['subseccion']= $subseccion2;
					$myObj['numero_caja']= $numero_caja2;
					$myObj['cantidad_carpetas']= $cantidad_carp2;
					break;
			}

		/* Hasta aqui defino las variables de la caja */	

		/* Desde aqui define los expedientes dentro de la caja */
			do{
				for ($i=0;$i<$registros_caja;$i++){
					$linea = pg_fetch_array($fila_caja);

					$id_expediente = $linea['id_expediente'];
					$nombre_expediente = $linea['nombre_expediente'];
					
					switch ($num) {
						case '1':
							$j=$i+1;
							break;
						case '2':
							$j=$i+101;
							break;
					}
					$nombre_expediente1=substr($nombre_expediente, 0, 40);

					if($i<=9){
						$myObj['expedientes'][$i]['numero_carpeta']= $id_expediente;
						$myObj['expedientes'][$i]['nombre_carpeta']= $nombre_expediente1;

						echo "<script>$('#numero_carpeta_$j').html('$id_expediente'); $('#nombre_carpeta_$j').html('$nombre_expediente1'); </script>";
					}
				}
			}while ($fila_caja=pg_fetch_assoc($fila_caja));
		/* Hasta aqui define los expedientes dentro de la caja */
		
			echo "<script>
				$('#muestra_cantidad_carpetas$num').html($registros_caja);
				$('#cantidad_carpetas$num').val($registros_caja);
				$('#error_numero_caja$num').slideUp('slow');
				$('#boton_imprimir').slideDown('slow');
			</script>";
				
			$myJSON=json_encode($myObj);
			
			if($num==1){
				echo "<script>
					$('#caja1').val('$myJSON');
					$('#expedientes_caja1').val('$myJSON');
					$('#fecha_inicial_1').val('$fecha_inicial_real');
					$('#fecha_final_1').val('$fecha_final_real');
				</script>";	
			}else{
				echo "<script>
					$('#caja2').val('$myJSON');
				</script>";	
			}

			echo "<script>
				$('#fecha_desde$num').html('$fecha_inicial_real');
				$('#fecha_hasta$num').html('$fecha_final_real');
				</script>";				
		}

		echo "<script>
			$('#muestra_numero_caja$num').html('$numero_caja');
			$('#muestra_numero_caja$num').slideDown('slow');
			$('#muestra_input_numero_caja$num').slideUp('slow');
		</script>";

	}
	if(isset($_POST['numero_expediente'])){
		$numero_expediente	=$_POST['numero_expediente'];
		$num 				=$_POST['num'];

		$query_expediente="select * from expedientes where id_expediente ilike('%$numero_expediente%') order by id limit 5";
		
		$fila_expediente = pg_query($conectado,$query_expediente);
		$registros_expediente= pg_num_rows($fila_expediente);

		if($registros_expediente==0){			
			echo "<script>$('#error_numero_expediente$num').slideDown('slow'); $('#carpeta$num').html('')</script>";
		}else{
			do{
				$num_fila = 0; 
				for ($i=0;$i<$registros_expediente;$i++){
					$linea = pg_fetch_array($fila_expediente);

					$dependencia_expediente = $linea['dependencia_expediente'];
					$fecha_inicial 			= $linea['fecha_inicial'];
					$fecha_final 			= $linea['fecha_final'];
					$id_expediente 			= $linea['id_expediente'];
					$nombre_expediente 		= $linea['nombre_expediente'];
					$serie 					= $linea['serie'];
					$subserie 				= $linea['subserie'];

					if($dependencia_expediente=="INV"){
						$dependencia_expediente="(INV) Inventario General";
					}

					$fecha_inicial1 = substr($fecha_inicial,0, 10);    
					$fecha_inicial2 = str_replace("-", "/", $fecha_inicial1);    
					$fecha_final1 	= substr($fecha_final,0, 10);    // Numero consecutivo del expediente
					$fecha_final2 	= str_replace("-", "/", $fecha_final1);    
					
					echo "<div class='art";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "'>"; 

				/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
					echo "<a href='javascript:cargar_informacion_expediente(\"$dependencia_expediente\",\"$fecha_inicial2\",\"$fecha_final2\",\"$nombre_expediente\",\"$id_expediente\",\"$serie\",\"$subserie\",\"$num\")'>";

					$search_expediente=strtoupper($numero_expediente);
					$id_expediente1 = trim(str_ireplace($search_expediente, "<font color='red'>$search_expediente</font>", $id_expediente)); 
					echo "<b>$id_expediente1</b> | $nombre_expediente | Serie <b>$serie</b> | Subserie <b>$subserie</b>";
					echo "</a>"; // Cierra el href
				/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
					
					$num_fila++; 
				}
			}while ($fila_expediente=pg_fetch_assoc($fila_expediente));
		}
	}
	if(isset($_POST['nombre_expediente'])){
		$nombre_expediente 	=$_POST['nombre_expediente'];
		$num 				=$_POST['num'];

		$query_expediente="select * from expedientes where nombre_expediente ilike('%$nombre_expediente%') order by id limit 5";
		
		$fila_expediente = pg_query($conectado,$query_expediente);
		$registros_expediente= pg_num_rows($fila_expediente);

		if($registros_expediente==0){			
			echo "<script>$('#error_nombre_expediente$num').slideDown('slow'); </script>";
		}else{
			do{
				$num_fila = 0; 
				for ($i=0;$i<$registros_expediente;$i++){
					$linea = pg_fetch_array($fila_expediente);

					$dependencia_expediente = $linea['dependencia_expediente'];
					$fecha_inicial 			= $linea['fecha_inicial'];
					$fecha_final 			= $linea['fecha_final'];
					$id_expediente 			= $linea['id_expediente'];
					$nombre_expediente1		= $linea['nombre_expediente'];
					$serie 					= $linea['serie'];
					$subserie 				= $linea['subserie'];

					if($dependencia_expediente=="INV"){
						$dependencia_expediente="(INV) Inventario General";
					}

					$fecha_inicial1 = substr($fecha_inicial,0, 10);    
					$fecha_inicial2 = str_replace("-", "/", $fecha_inicial1);    
					$fecha_final1 	= substr($fecha_final,0, 10);    // Numero consecutivo del expediente
					$fecha_final2 	= str_replace("-", "/", $fecha_final1);    
					
					echo "<div class='art";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "'>"; 

				/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
					echo "<a href='javascript:cargar_informacion_expediente(\"$dependencia_expediente\",\"$fecha_inicial2\",\"$fecha_final2\",\"$nombre_expediente1\",\"$id_expediente\",\"$serie\",\"$subserie\",\"$num\")'>";
	
					$search_expediente  =strtoupper($nombre_expediente);
					$nombre_expediente2 = trim(str_ireplace($nombre_expediente, "<font color='red'>$search_expediente</font>", $nombre_expediente1)); 

					echo "<b>$id_expediente</b> | $nombre_expediente2 | Serie <b>$serie</b> | Subserie <b>$subserie</b>";
					echo "</a>"; // Cierra el href
				/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "</div>";//cierra div class='art'
					$num_fila++; 
				}
			}while ($fila_expediente=pg_fetch_assoc($fila_expediente));
		}
	}
	if(isset($_POST['cargar_json'])){
		$cargar_json 		=$_POST['cargar_json'];
		$correlativo 		=$_POST['correlativo'];
		$entidad 			=$_POST['entidad'];
		$fecha_final 		=$_POST['fecha_final'];
		$fecha_inicial 		=$_POST['fecha_inicial'];
		$folios 			=$_POST['folios'];
		$fondo 				=$_POST['fondo'];
		$nombre_expediente 	=$_POST['nombre_expediente'];
		$nombre_expediente1 =substr($nombre_expediente, 0, 47);
		$num 				=$_POST['num'];
		$numero_expediente 	=$_POST['numero_expediente'];
		$seccion 			=$_POST['seccion'];
		$serie 				=$_POST['serie'];
		$subseccion 		=$_POST['subseccion'];
		$subserie 			=$_POST['subserie'];

	/* Desde aqui se arma JSON para enviar a la api */
		$myObj = array();

		$myObj['correlativo'] 		= $correlativo;
		$myObj['entidad'] 			= $entidad;
		$myObj['fecha_final'] 		= $fecha_final;
		$myObj['fecha_inicial'] 	= $fecha_inicial;
		$myObj['folios'] 			= $folios;
		$myObj['fondo']				= $fondo;
		$myObj['nombre_expediente']	= $nombre_expediente1;
		$myObj['numero_expediente']	= $numero_expediente;
		$myObj['seccion']			= $seccion;
		$myObj['serie']				= $serie;
		$myObj['subseccion'] 		= $subseccion;
		$myObj['subserie'] 			= $subserie;

		$myJSON=json_encode($myObj);
			
		echo "<script>
			$('#carpeta$num').val('$myJSON');
		</script>";	
					// }
	/* Hasta aqui se arma JSON para enviar a la api */
	}

 ?>