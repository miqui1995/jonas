<?php
	if(!isset($_SESSION)){
		session_start();
	}
?>		
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Listado de Radicados por bandejas</title>
</head>
<body>
<?php  	
	require_once('../../login/conexion2.php');
//		var_dump($_SESSION);
/* Isset ajax (buscador_radicados) consulta sugerencias - buscador principal radicados.*/	
		$login 			 = $_SESSION['login'];
		// $bandeja_entrada = $_SESSION['bandeja_entrada'];

		if(isset($_POST['pagina_checkbox_todos'])){
			$pagina=$_POST['pagina_checkbox_todos'];
			$selecciona_todos=$_POST['selecciona_todos_checkbox'];
		}
		if(isset($_POST['pagina'])){	
			$pagina=$_POST['pagina'];
			$selecciona_todos=$_POST['selecciona_todos'];
		}
		if(isset($_POST['carpeta_personal'])){	
			$codigo_carpeta=$_POST['carpeta_personal'];
		}
		
		// $consulta = "select * from radicado where asunto IS NOT NULL and codigo_carpeta1 ->'$login' ->> 'codigo_carpeta_personal' = '$codigo_carpeta' order by fecha_radicado desc limit 10 offset $pagina ";

		/* Se arma la consulta para que visualice primero los radicados que tienen actividad mas reciente. */
		$consulta="select r.numero_radicado, max(h.fecha) fecha from radicado r left join historico_eventos h on r.numero_radicado=h.numero_radicado where r.asunto IS NOT NULL and codigo_carpeta1 ->'$login' ->> 'codigo_carpeta_personal' = '$codigo_carpeta' group by r.numero_radicado order by fecha desc limit 10 offset $pagina";

		$fila_radicado = pg_query($conectado,$consulta);
		if($fila_radicado==false){ // Valida si la tabla radicados existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla R de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_radicado= pg_num_rows($fila_radicado);
	/*Recorre el array generado e imprime uno a uno los resultados.*/
		//Array para traducir meses a formato español.
		$replace=array('Jan'=>'Ene', 'Apr'=>'Abr', 'Aug'=>'Ago', 'Dec'=>'Dic');
		if($registros_radicado>0){
			echo "<table id='lista_radicados_bandeja' style=\"float: left; overflow-x:auto; width: 100%;\"><tr><td class='descripcion'></td><td class='descripcion'></td><td class='descripcion'>Número de Radicado</td><td class='descripcion'>Asunto del Radicado</td><td class='descripcion'>Último Usuario</td><td class='descripcion'>Término respuesta</td><td class='descripcion'>Fecha de Radicación</td></tr>";
			$num_fila = 0; 
			for ($i=0;$i<$registros_radicado;$i++){
				$linea_principal = pg_fetch_array($fila_radicado);

				$numero_radicado 	= $linea_principal['numero_radicado'];
				$fecha_radicado3 	= $linea_principal['fecha'];

				$query_listado_radicados 	= "select * from radicado where numero_radicado='$numero_radicado'";
				$fila_listado_radicados 	= pg_query($conectado,$query_listado_radicados);
				$linea 						= pg_fetch_array($fila_listado_radicados);

				$query_historico_radicado = "select * from historico_eventos where numero_radicado='$numero_radicado' and fecha = '$fecha_radicado3'";
				$fila_historico_radicado 	= pg_query($conectado,$query_historico_radicado);
				$linea_historico_radicado 	= pg_fetch_array($fila_historico_radicado);

				$path_radicado 		= $linea['path_radicado'];
				$id_expediente 		= $linea['id_expediente'];
				$estado_radicado 	= $linea['estado_radicado'];
				$usuarios_control 	= $linea['usuarios_control'];

				// Define si tiene numero de expediente para mostrar carpeta verde o roja
				if($id_expediente!=""){
					$exp  = explode(",", $id_expediente);
					$max  = sizeof($exp);
					$max2 = $max-1;

					$nombre_expediente="";
					if($max2==0){
						$num_exp = $exp[0];
						$consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
						$fila_exp 	  = pg_query($conectado,$consulta_exp);
						$linea_exp    = pg_fetch_array($fila_exp);
						$nombre_exp   = $linea_exp['nombre_expediente'];

						$nombre_expediente = $nombre_expediente."&#13;$num_exp ($nombre_exp)";
					}else{
						for ($j=0; $j < $max2; $j++) { 
							$num_exp = $exp[$j];

							$consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
							$fila_exp 	  = pg_query($conectado,$consulta_exp);
							$linea_exp    = pg_fetch_array($fila_exp);
							$nombre_exp   = $linea_exp['nombre_expediente'];

							$nombre_expediente = $nombre_expediente."&#13;$num_exp ($nombre_exp)";
						}
					}
					
                    $expediente="<img id='exp$numero_radicado' height='20px' src='imagenes/iconos/exp_verde.png' title='Se encuentra en expediente(s) $nombre_expediente'> ";
                }else{
                    $expediente="<img id='exp$numero_radicado' height='20px' src='imagenes/iconos/exp_rojo.png' title='No se encuentra en un expediente.'> ";
                }
				// Fin si tiene numero de expediente para mostrar carpeta verde o roja
			/* Definir TRD del radicado */
				$codigo_serie 		= $linea['codigo_serie'];
				$codigo_subserie 	= $linea['codigo_subserie'];

                $codigo_serie_subserie = $codigo_serie.$codigo_subserie;

                if($codigo_serie_subserie==""){
					$trd="<img id='trd$numero_radicado' height='20px' src='imagenes/iconos/trd_rojo.png' title='No tiene TRD asignada'>";
                }else{
					$trd="<img id='trd$numero_radicado' height='20px' src='imagenes/iconos/trd_verde.png' title='Si tiene TRD asignada'>";
                }
			/* Fin de definir TRD del radicado */
			/* Se define el visor pdf */ 
				if ($path_radicado=="") { 
	    			$pdf ="";
				}else{
					$pdf="<img height='20px' src='imagenes/iconos/archivo_pdf.png' title='Si tiene imagen principal' onclick='visualizar_radicado(\"$path_radicado\")'>";
	     		}
			/* Se define es/tiene respuesta */ 
				if ($estado_radicado=="en_tramite") { 
					$valida_usuario_control = strpos($usuarios_control, $login);
					if($codigo_carpeta=="entrada"){
						if($valida_usuario_control===false){
							$title_pendiente = "Documento que falta por pasar a carpeta personal.";	
						}else{
							$title_pendiente = "Documento que falta que usted lo tramite o reasigne.";	
						}
					}else{
						$title_pendiente = "Documento que falta que lo tramite o reasigne. $usuarios_control";
					}

	    			$estado_r ="<img height='23px' src='imagenes/iconos/gestion_red.png' title='$title_pendiente'>";
				}else{
					$estado_r="<img height='23px' src='imagenes/iconos/gestion_green.png' title='Documento Tramitado $codigo_carpeta'>";
	     		}

			// Formato fecha para mostrar Ene/06	
				$fecha_radicado2 	= $linea['fecha_radicado'];
				$fecha_radicado1 	= date_create("$fecha_radicado2");
				$fecha_radicado 	= date_format($fecha_radicado1,'M/d');
				$fecha_radicado 	= str_ireplace(array_keys($replace), array_values($replace), $fecha_radicado);

		//		$path_radicado = $linea['path_radicado'];
				$asunto 		= $linea['asunto'];
				$asunto_cut 	= substr($asunto, 0,50);
				if(strlen($asunto)>70){
					$asunto_cut	.=" [...]";
				}	
				$termino 		= $linea['termino'];
				$leido 			= $linea['leido'];

				$transaccion 	= $linea_historico_radicado['transaccion'];
				$comentario 	= $linea_historico_radicado['comentario'];
				$usuario 		= $linea_historico_radicado['usuario'];

				/* Se define color de la fila */
				if($i%2==0){
					$fila_resultados = "fila_resultados";
				}else{
					$fila_resultados = "fila_resultados2";
				}

				echo "<tr class='$fila_resultados"; if (strpos($leido, $login) !== false) {echo "";}else{echo " leido";} 	//si el radicado esta leido pone como clase "leido"
				echo "' >"; 
						
			/*Aqui defino lo que voy a mostrar*/			
				echo "
					<td id='checkbox$numero_radicado' title='Seleccionar o deseleccionar radicado' style=\"width:10px;\" >
						<img id='checkbox$numero_radicado' class='checkbox' src='imagenes/iconos/checkbox1.png' onclick=\"cambia_checkbox('$numero_radicado','1')\"'>
					</td>
					<td id='exp' width='110px'>$expediente $trd $pdf $estado_r</td>
					<td id='num_radicado' class='center' title='Numero de radicado' width='150px' 
					onclick=\"agregar_pestanas('$numero_radicado')\">$numero_radicado</td> 
					<td id='asunto' title='$asunto' onclick=\"agregar_pestanas('$numero_radicado')\"> $asunto_cut</td>
					<td id='enviado_por' width='150px' title='El $fecha_radicado2 el usuario $usuario &#13;$transaccion&#13;$comentario' 50px><center>$usuario</center></td>
					<td id='termino'  width='40px' title='Dias habiles restantes para dar respuesta'><center>$termino</center></td>
					<td id='fecha' width='50px' title='Fecha de radicado &#13;$fecha_radicado2'><center>$fecha_radicado<center></td>";
				
				echo "</tr>";//cierra tr class='fila_resultados'
					if($selecciona_todos=="NO"){ // Checkbox selecciona todos
						echo "<script>cambia_checkbox('$numero_radicado','1')</script>";
					}
					$num_fila++; 
			}

			$query_paginacion = "select count(*) from radicado where asunto IS NOT NULL AND codigo_carpeta1 ->'$login' ->> 'codigo_carpeta_personal' = '$codigo_carpeta'";

			$result_query_paginacion = pg_query($conectado,$query_paginacion);

            if($result_query_paginacion==false){
                $bandeja_entrada='0';
            }else{
                $linea_paginacion = pg_fetch_array($result_query_paginacion);    
                $bandeja_entrada=$linea_paginacion['count'];
            }
		echo "</table>"; // Cierra div con overflow-x (para responsive)
		echo "<div id='visor_adjuntos_pdf' class='hidden'></div>"; // Div para visualizar PDF principal

			$paginas=ceil($bandeja_entrada/10);
			$pag_actu=($pagina/10)+1;
			
		echo "<br>
			<div id='paginacion' style=\"overflow-x:auto; width:100%;\">
				<center><br>";
		$contador_limite_inicio=0;
		
		/* Se calcula para paginacion con los botones "<" y "<<"*/	
		$menos_uno = ($pag_actu-2)*10;
		if($pag_actu!=1){
			echo "<a href='javascript: paginacion(0)' class='pagina_actual' title='Ir a la primera página'><<</a>";
			echo "<a href='javascript: paginacion($menos_uno)' class='pagina_actual' title='Ir a la página anterior'><</a>";
		}

		for($i=1;$i<=$paginas;$i++){
			$offset=($i-1)*10;
					
			$limite_inferior=$pag_actu-7;
			$limite_superior=$pag_actu+7;
			$paginas1=($paginas-1)*10;

			if($i<$limite_superior && $i >$limite_inferior){

				if($pag_actu==$i){
					echo "<a href='javascript: paginacion($offset)' class='pagina_actual'>$i</a>";
				}else{
					echo "<a href='javascript: paginacion($offset)' class='paginas' title='Ir a la página $i'>$i</a>";
				}					
			}						
		}
	/* Se calcula para paginacion con los botones ">" y ">>"*/
		$mas_uno = $pag_actu*10;
		if($pag_actu!=$paginas){
			echo "<a href='javascript: paginacion($mas_uno)' class='pagina_actual' title='Ir a la siguiente página'>></a>";
			echo "<a href='javascript: paginacion($paginas1)' class='pagina_actual' title='Ir a la última página'>>></a>";
		}

		echo "<br>
		    pagina $pag_actu de $paginas
			</center>
		</div>";
		}else{
			echo "<center><h3>No hay radicados en esta bandeja.</h3></center>";
		}
/* Fin isset ajax (buscador_radicados) consulta sugerencias - buscador principal radicados.*/	
?>
</body>