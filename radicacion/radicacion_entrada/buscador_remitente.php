<?php
	if(!isset($_SESSION)){
		session_start();
	}
	if(!isset($_POST['tipo_radicacion'])){
	}
	require_once('../../login/validar_inactividad.php');
	require_once('../../login/conexion2.php');
	// $busca_contactos 	= ''; // Se inicializa variable para evitar errores.

	
	if(isset($_POST['tipo_radicacion'])){ // Busca consecutivo dependencia
		$tipo_radicacion=strtoupper($_POST['tipo_radicacion']);
		// $dependencia=$_SESSION['dependencia'];
		// La variable $codigo_dependencia la hereda desde (../../login/validar_inactividad.php)

		$consulta = "SELECT * FROM consecutivos where codigo_dependencia='$codigo_dependencia' and tipo_radicado='$tipo_radicacion'";
		$fila = pg_query($conectado,$consulta);
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros= pg_num_rows($fila);

		if($registros==0){
			echo "<script>
				Swal.fire({
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    title: 'El consecutivo de ($tipo_radicacion) de la dependencia no existe, por lo que no puede radicar documentos',
                    text: 'Comuníquese con el administrador del sistema.',
                    type: 'warning'
                }).then(function(isConfirm) {
                    window.location.href = 'principal3.php';
                });
			</script>
			"; // No existe consecutivo
		}else{
			echo "true"; // El consecutivo existe
		}
	}elseif(isset($_POST['search_dependencia_destino'])){ // Formulario de radicación rápida
		$search_depe 		= strtoupper($_POST['search_dependencia_destino']);
		$formulario_origen 	= trim($_POST['formulario_origen']);

		$query_dependencia="select * from dependencias where id_dependencia!='1' and activa ='SI' and (nombre_dependencia ilike '%$search_depe%' or codigo_dependencia ilike '%$search_depe%') order by nombre_dependencia";
		
		$fila_dependencia = pg_query($conectado,$query_dependencia);
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_dependencia= pg_num_rows($fila_dependencia);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	

		if($registros_dependencia>0 && $search_depe!=''){
			for ($i=0;$i<$registros_dependencia;$i++){
				$linea_dependencia = pg_fetch_array($fila_dependencia);

				$codigo_dependencia = $linea_dependencia['codigo_dependencia'];
				$nombre_dependencia = $linea_dependencia['nombre_dependencia'];

				$depe1 = trim(str_ireplace($search_depe, "<font color='red'>$search_depe</font>", $nombre_dependencia));
				$depe2 = trim(str_ireplace($search_depe, "<font color='red'>$search_depe</font>", $codigo_dependencia)); 
				
	/* Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido  */
			echo "<div class='art' title='Cargar esta dependencia' onclick=\"javascript:cargar_valor_dependencia('$codigo_dependencia','$nombre_dependencia','$formulario_origen')\">
					($depe2) <b>$depe1</b>";
			echo "</div>";//cierra div class='sugerencia_contacto'(art)
			}
		}else{
			echo "<div id='sin_dependencia' class='errores' style='display:block;'> 
				No se han encontrado resultados. El nombre de dependencia que intenta no se encuentra en la base de datos. Revise nuevamente o comuníquese con el administrador del sistema.
			</div>";
		}
	}elseif (isset($_POST['radicado_modificacion_rapida'])) {
		$search_radicado_modificar=strtoupper($_POST['radicado_modificacion_rapida']);
		
		$longitud_search_radicado_modificar=strlen($search_radicado_modificar);

		if($search_radicado_modificar==""){
/*			$query_nombre_dependencia 	= "select d.nombre_dependencia, u.login from dependencias d join usuarios u on d.codigo_dependencia=u.codigo_dependencia where d.codigo_dependencia='$dependencia_destino' and u.perfil='DISTRIBUIDOR_DEPENDENCIA' and u.estado='ACTIVO'";*/
			$query_radicado_modificar = "select distinct r.numero_radicado, r.fecha_radicado, r.descripcion_anexos, r.dependencia_actual, u.perfil, u.login from radicado r join usuarios u on r.dependencia_actual=u.codigo_dependencia where r.usuario_radicador='$login' and r.asunto is null and u.perfil='DISTRIBUIDOR_DEPENDENCIA'";
			// $query_radicado_modificar="select * from radicado where usuario_radicador='".$_SESSION['login']."' and asunto is null order by fecha_radicado desc";
		}else{
			$query_radicado_modificar = "select distinct r.numero_radicado, r.fecha_radicado, r.descripcion_anexos, r.dependencia_actual, u.perfil, u.login from radicado r join usuarios u on r.dependencia_actual=u.codigo_dependencia where r.usuario_radicador='$login' and r.asunto is null and r.numero_radicado ilike '%$search_radicado_modificar%' and u.perfil='DISTRIBUIDOR_DEPENDENCIA' order by fecha_radicado desc";
		}

		$fila_radicado_modificar = pg_query($conectado,$query_radicado_modificar);
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_radicado_modificar= pg_num_rows($fila_radicado_modificar);
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_radicado_modificar>0 ){
			// do{
				$num_fila=0;
				for ($i=0;$i<$registros_radicado_modificar;$i++){
					$linea_radicado_modificar = pg_fetch_array($fila_radicado_modificar);

					$numero_radicado 		= trim($linea_radicado_modificar['numero_radicado']);
					$fecha_radicado  		= $linea_radicado_modificar['fecha_radicado'];
					$descripcion_anexos 	= $linea_radicado_modificar['descripcion_anexos'];
					$dependencia_destino 	= $linea_radicado_modificar['dependencia_actual'];
					$usuario_actual 		= $linea_radicado_modificar['login'];

					$query_nombre_dependencia="select nombre_dependencia from dependencias where codigo_dependencia='$dependencia_destino'";
					$fila_nombre_dependencia = pg_query($conectado,$query_nombre_dependencia);
					$linea_nombre_dependencia = pg_fetch_array($fila_nombre_dependencia);
					$nombre_dependencia_destino = $linea_nombre_dependencia['nombre_dependencia'];

					$numero_radicado1 = trim(str_ireplace($search_radicado_modificar, "<font color='red'>$search_radicado_modificar</font>", $numero_radicado)); 
					if($longitud_search_radicado_modificar==18){		
						echo "<script>cargar_modificacion('$numero_radicado', '','$usuario_actual')</script>";
					}else{
						echo"<div id='depe_rad' title='Cargar este radicado' class='art ";
							if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
							else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
						echo "' onclick=\"javascript:cargar_modificacion('$numero_radicado','','$usuario_actual')\">
							<b>$numero_radicado1</b><br><b>Dependencia destino:</b> ($dependencia_destino - $nombre_dependencia_destino) <br><b> Anexos:</b> ($descripcion_anexos)";
						echo "</div>";//cierra div class='sugerencia_contacto'(art)
					}	
					$num_fila++;		
				}
			// }while ($fila_radicado_modificar=pg_fetch_assoc($fila_radicado_modificar));
		}else{
			echo "<div id='sin_dependencia' class='errores' style='display:block;'> 
				No se han encontrado resultados. El numero de radicado que intenta no se encuentra en la base de datos. Revise nuevamente o comuníquese con el administrador del sistema.
			</div>";
		}
	}elseif (isset($_POST['desplegable_terminos'])) { // Armar el select "Tipo de documento (Termino Radicacion Entrada)"
		$query_termino="select * from tipo_doc_termino where activo ='SI' union select * from tipo_doc_termino_pqr where activo ='SI' order by tipo_documento";
		$fila_termino = pg_query($conectado,$query_termino);
	/* Calcula el numero de registros que genera la consulta anterior. */
		$registros_termino= pg_num_rows($fila_termino);
	/* Recorre el array generado e imprime uno a uno los resultados. */	
		echo "<select name='termino' id='termino' class='select_opciones' class='select_opciones' onchange='muestra_termino()'>"; // Inicia el input select
			for ($t=0;$t<$registros_termino;$t++){
				$linea_termino = pg_fetch_array($fila_termino);

				$tipo_documento 			= $linea_termino['tipo_documento'];
				$tiempo_tramite  			= $linea_termino['tiempo_tramite'];
	            $descripcion_tipo_documento = $linea_termino['descripcion_tipo_documento'];

				if($tipo_documento=="OFICIO" or $tipo_documento=="COMUNICACION OFICIAL"){
					echo "<option value='$tipo_documento' selected='selected' title='$descripcion_tipo_documento'>$tipo_documento</option>"; // Si es la opcion "OFICIO" sea precargada por defecto.
				}else{
					echo "<option value='$tipo_documento' title='$descripcion_tipo_documento'>$tipo_documento</option>";
				}	
			}
		echo "</select>"; // Fin del input select
	}elseif (isset($_POST['search_dest_depe'])) {
		$search_dest_depe 	= strtoupper($_POST['search_dest_depe']);
		$input_focus 		= trim($_POST['input_focus']);

		$consulta_consecutivo = "select * from consecutivos WHERE tipo_radicado='1' and codigo_dependencia='$search_dest_depe'"; // Valida si existe la secuencia del tipo de radicado - dependencia del usuario que radica
		$fila_consecutivo = pg_query($conectado,$consulta_consecutivo); // La variable "$conectado" la hereda desde el require ../login/conexion2.php que tiene el archivo que hace el include de este archivo.
		$linea_consecutivo = pg_fetch_array($fila_consecutivo);
		
		if($linea_consecutivo==false){  // Si no existe en la tabla consecutivos la secuencia del tipo de radicado - dependencia no permita continuar.
			echo "<div class='errores' style='display: block;'>No se encuentra consecutivo configurado para ésta dependencia.</div>
				<script>$('#div_boton_enviar').slideUp('slow');</script>
			";
			echo "<script>
				Swal.fire({	
						position 			: 'top-end',
					    showConfirmButton 	: true,
					    timer 				: 5000,	
					    title 				: 'No hay secuencia para radicación de entrada para dependencia $search_dest_depe',
					    text 				: 'No se puede radicar porque el consecutivo de esta dependencia no existe. Comuníquese con el administrador del sistema para crearlo.',
					    type 				: 'error'
					});
			</script>"; 
		}else{
			$query_dest_depe  = "select * from usuarios u join dependencias d on u.codigo_dependencia=d.codigo_dependencia where u.codigo_dependencia = '$search_dest_depe' and u.perfil='DISTRIBUIDOR_DEPENDENCIA'";
		
			$fila_dest_depe 	 = pg_query($conectado,$query_dest_depe);
			/*Calcula el numero de registros que genera la consulta anterior.*/
			$registros_dest_depe = pg_num_rows($fila_dest_depe);
			/*Recorre el array generado e imprime uno a uno los resultados.*/	

			if($registros_dest_depe>0 && $search_dest_depe!=''){
				for ($i=0;$i<$registros_dest_depe;$i++){
					$linea_dest_depe = pg_fetch_array($fila_dest_depe);

					$nombre_dependencia = $linea_dest_depe['nombre_dependencia'];
					$nombre_completo  	= $linea_dest_depe['nombre_completo'];
					$login1				= $linea_dest_depe['login'];
					$path_foto 			= $linea_dest_depe['path_foto'];
					
			/* Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido  */
					echo "<center><h2 style='color:green;'>El documento que se esta radicando va a ser asignado al usuario </h2>
							<table border='0' style='max-width: 60%; margin-left:20px; border: #2D9DC6 3px solid; border-radius:15px;'>	
								<tr>
									<td rowspan=2 width='1%'>
										<img src='$path_foto' style='width: 50px;border-radius: 10px;'> 
									</td>
									<td width='60%' style='padding-bottom: 10px;'>
										<b style='color:green;'>$nombre_dependencia</b><br>
										$nombre_completo 
										<br>
										<b>($login1)</b>
									</td>
								</tr>
							</table>
						</center>	
					<script>cargar_valor_dependencia_destino('$login1','$input_focus');</script>
					";
				}
			}else{
				echo "<script>sin_distribuidor();</script>";
			}
		}
	}elseif (isset($_POST['radicado_modificacion'])) {
		$search_radicado_modificar=strtoupper($_POST['radicado_modificacion']);
		
		$longitud_search_radicado_modificar=strlen($search_radicado_modificar);

		$query_radicado_modificar = "select * from radicado where numero_radicado ilike '%$search_radicado_modificar%' or asunto ilike '%$search_radicado_modificar%' order by asunto limit 10";

		$fila_radicado_modificar = pg_query($conectado,$query_radicado_modificar);
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_radicado_modificar= pg_num_rows($fila_radicado_modificar);
		// echo "<br>$registros_radicado_modificar<br>";
	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_radicado_modificar>0){
			$num_fila=0;
			for ($i=0;$i<$registros_radicado_modificar;$i++){

				$linea_radicado_modificar = pg_fetch_array($fila_radicado_modificar);

				$numero_radicado 	 = trim($linea_radicado_modificar['numero_radicado']);
				$codigo_contacto	 = $linea_radicado_modificar['codigo_contacto'];
				$dependencia_destino = $linea_radicado_modificar['dependencia_actual'];
				$asunto 			 = $linea_radicado_modificar['asunto'];

				if($asunto==""){
					$asunto1="<b>El asunto se encuentra vacío</b>";
				}else{
					$asunto1=$asunto;
				}

				$verifica_inventario = substr($numero_radicado,7,3);
				$verifica_radicacion_interna = substr($numero_radicado,-1);

				$numero_radicado1 	= trim(str_ireplace($search_radicado_modificar, "<font color='red'>$search_radicado_modificar</font>", $numero_radicado));
				$asunto2 			= trim(str_ireplace($search_radicado_modificar, "<font color='red'>$search_radicado_modificar</font>", $asunto1));

				$descripcion_anexos2= $linea_radicado_modificar['descripcion_anexos'];

				// Se consulta la cantidad de adjuntos que tiene el radicado
				$query_adjuntos 	= "select * from adjuntos where numero_radicado = '$numero_radicado'";
				$fila_adjuntos  	= pg_query($conectado,$query_adjuntos);
				$registros_adjuntos = pg_num_rows($fila_adjuntos);

				if($registros_adjuntos>0){
					if($registros_adjuntos==1){
						$descripcion_anexos1 = "$descripcion_anexos2 $registros_adjuntos archivo anexo";
					}else{
						$descripcion_anexos1 = "$descripcion_anexos2 $registros_adjuntos archivos anexos";
					}
				}else{
					$descripcion_anexos1 = $descripcion_anexos2;
				}

				if($descripcion_anexos1==''){
					$descripcion_anexos='Sin anexos';
				}else{
					$descripcion_anexos = $descripcion_anexos1;
				}
				$fecha_radicado			= $linea_radicado_modificar['fecha_radicado'];
				$clasificacion_radicado = $linea_radicado_modificar['clasificacion_radicado'];
				$usuarios_visor 		= $linea_radicado_modificar['usuarios_visor'];
				$usuarios_control 		= $linea_radicado_modificar['usuarios_control'];

				$caracteres_depend 		= $_SESSION['caracteres_depend'];

				if($codigo_entidad=="EJC" || $codigo_entidad=="EJEC"){
					$longitud_para_buscar = 16;
				}else{
					$longitud_para_buscar = 15+$caracteres_depend;
				}

				// $longitud_para_buscar 	= $caracteres_depend+15;

				if($verifica_inventario=='INV'){
					if($longitud_search_radicado_modificar==$longitud_para_buscar){		
						echo "<script>cargar_modificacion('$numero_radicado','$codigo_contacto')</script>";
						break;
					}else{
						echo "<div title='Cargar este radicado' class='art ";
							if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
							else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
						echo "' onclick=\"cargar_modificacion('$numero_radicado','$codigo_contacto')\"><b>$numero_radicado1</b> ($dependencia_destino) , $descripcion_anexos , $asunto2</div>";//cierra div class='sugerencia_contacto'(art)
					}
				}else{
					/* Se hace la query para determinar nombre de la dependencia y login de la $dependencia_destino al usuario DISTRIBUIDOR_DEPENDENCIA y que se encuentre activo */
					$query_nombre_dependencia 	= "select login from usuarios where codigo_dependencia='$dependencia_destino' and perfil='DISTRIBUIDOR_DEPENDENCIA' and estado='ACTIVO'";

					$fila_nombre_dependencia 	= pg_query($conectado,$query_nombre_dependencia);
					$linea_nombre_dependencia 	= pg_fetch_array($fila_nombre_dependencia);
					$usuario_actual 			= $linea_nombre_dependencia['login'];

					/* Se define si la entidad es ejercito, la radicacion de entrada es 2 */
					if($codigo_entidad=='EJC' || $codigo_entidad=='EJEC'){
						if($verifica_radicacion_interna==1){
							$cargar_modificacion="carga_radicacion_salida('$numero_radicado')";
						}else{
							$cargar_modificacion="cargar_modificacion('$numero_radicado','$codigo_contacto','$usuario_actual')";
						}
					}else{
						if($verifica_radicacion_interna==2){
							$cargar_modificacion="carga_radicacion_salida2('$numero_radicado')";
						}else{
							$cargar_modificacion="cargar_modificacion('$numero_radicado','$codigo_contacto','$usuario_actual')";
						}
					}

					/* Si es un numero_radicado completo, cargar_modificacion */					
					if($longitud_search_radicado_modificar==$longitud_para_buscar){		
						echo "<script>$cargar_modificacion</script>";
						break;
					}else{
						echo"<div title='Cargar este radicado' class='art ";
							if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
							else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
						echo "' onclick= \"$cargar_modificacion\"><b>$numero_radicado1</b> ($dependencia_destino), $descripcion_anexos, $asunto2</div>";//cierra div class='sugerencia_contacto'(art)
					}		
				}
				$num_fila++;	
			}
		}else{
			echo "<div id='sin_dependencia' class='errores' style='display:block;'> 
				No se han encontrado resultados. El numero de radicado que intenta no se encuentra en la base de datos. Revise nuevamente o comuníquese con el administrador del sistema.
			</div>";
		}
	}elseif (isset($_POST['radicado_asociar_imagen'])) { 
		$search_radicado_modificar=strtoupper($_POST['radicado_asociar_imagen']);
		
		$longitud_search_radicado_modificar=strlen($search_radicado_modificar);

		if($search_radicado_modificar==""){
			$query_radicado_modificar="select * from radicado r full outer join datos_origen_radicado dor on r.numero_radicado=dor.numero_radicado and r.codigo_contacto=dor.codigo_datos_origen_radicado where r.usuario_radicador='$login'";
		}else{
			$query_radicado_modificar="select * from radicado r full outer join datos_origen_radicado dor on r.numero_radicado=dor.numero_radicado and r.codigo_contacto=dor.codigo_datos_origen_radicado where r.numero_radicado ilike '%$search_radicado_modificar%'";
		}

		$fila_radicado_modificar  		= pg_query($conectado,$query_radicado_modificar);
		
		/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_radicado_modificar 	= pg_num_rows($fila_radicado_modificar);
		
		/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_radicado_modificar>0 ){
			for ($i=0;$i<$registros_radicado_modificar;$i++){
				$linea_radicado_modificar = pg_fetch_array($fila_radicado_modificar);

				$numero_radicado 				= $linea_radicado_modificar['numero_radicado'];
				$fecha_radicado 				= $linea_radicado_modificar['fecha_radicado'];
				$descripcion_anexos 			= $linea_radicado_modificar['descripcion_anexos'];
				$path_radicado 					= $linea_radicado_modificar['path_radicado'];
				$dependencia_destino 			= $linea_radicado_modificar['dependencia_actual'];
				$usuarios_control 				= $linea_radicado_modificar['usuarios_control'];
				$asunto 						= $linea_radicado_modificar['asunto'];
				$nivel_seguridad 				= $linea_radicado_modificar['nivel_seguridad'];
				$termino 						= $linea_radicado_modificar['termino'];
				$numero_guia_oficio 			= $linea_radicado_modificar['numero_guia_oficio'];
				$nombre_remitente_destinatario 	= $linea_radicado_modificar['nombre_remitente_destinatario'];
				$direccion 						= $linea_radicado_modificar['direccion'];
				$dignatario 					= $linea_radicado_modificar['dignatario'];
				$ubicacion 						= $linea_radicado_modificar['ubicacion'];
				$telefono 						= $linea_radicado_modificar['telefono'];
				$mail 							= $linea_radicado_modificar['mail'];

				if($asunto==""){
					$asunto1="<b>El asunto se encuentra vacío</b>";
				}else{
					$asunto1=$asunto;
				}

				$query_nombre_dependencia="select nombre_dependencia from dependencias where codigo_dependencia='$dependencia_destino'";
				$fila_nombre_dependencia = pg_query($conectado,$query_nombre_dependencia);
				$linea_nombre_dependencia = pg_fetch_array($fila_nombre_dependencia);
				$nombre_dependencia_destino = $linea_nombre_dependencia['nombre_dependencia'];

				$numero_radicado1 = trim(str_ireplace($search_radicado_modificar, "<font color='red'>$search_radicado_modificar</font>", $numero_radicado)); 
				if($longitud_search_radicado_modificar==18){		
					echo "<script>verifica_bodega('$numero_radicado', '$path_radicado')</script>";
				}else{
					echo "<div class='art' id='depe_rad' title='Cargar este radicado' onclick=\"javascript:verifica_bodega('$numero_radicado', '$path_radicado')\">
							<b>$numero_radicado1</b> ($dependencia_destino), $descripcion_anexos, $asunto1
						</div>";//cierra div class='sugerencia_contacto'(art)
				}		
			}
		}else{
			echo "<div id='sin_dependencia' class='errores' style='display:block;'> 
				No se han encontrado resultados. El numero de radicado que intenta no se encuentra en la base de datos. Revise nuevamente o comuníquese con el administrador del sistema.
			</div>";
		}
	}elseif(isset($_POST['buscar_destinatario'])){
		$search_destinatario=$_POST['buscar_destinatario'];

		$query_search_destinatario="select * from contactos where nombre_contacto ilike '%$search_destinatario%' or representante_legal ilike '%$search_destinatario%' order by nombre_contacto, representante_legal limit 10";
		// echo "$query_search_destinatario";

		$fila_search_destinatario 		= pg_query($conectado,$query_search_destinatario);
		
		/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_search_destinatario 	= pg_num_rows($fila_search_destinatario);
		
		/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_search_destinatario>0 ){
			for ($i=0;$i<$registros_search_destinatario;$i++){
				$linea_search_destinatario = pg_fetch_array($fila_search_destinatario);

				$codigo_contacto 				= $linea_search_destinatario['codigo_contacto'];
				$nombre_contacto 				= $linea_search_destinatario['nombre_contacto'];
				$ubicacion_contacto 			= $linea_search_destinatario['ubicacion_contacto'];
				$direccion_contacto 			= $linea_search_destinatario['direccion_contacto'];
				$telefono_contacto 				= $linea_search_destinatario['telefono_contacto'];
				$mail_contacto 					= $linea_search_destinatario['mail_contacto'];
				$representante_legal 			= $linea_search_destinatario['representante_legal'];

				$nombre_contacto1 	  = trim(str_ireplace($search_destinatario, "<font color='red'>$search_destinatario</font>", $nombre_contacto));
				$representante_legal1 = trim(str_ireplace($search_destinatario, "<font color='red'>$search_destinatario</font>", $representante_legal));

				echo "<div class='art_exp' onclick=\"javascript:cargar_nombre_contacto('$nombre_contacto','$representante_legal','$ubicacion_contacto','$direccion_contacto','$telefono_contacto','$mail_contacto','$codigo_contacto')\" title='Esta es una sugerencia. No es obligatorio. Solo muestra los 10 primeras coincidencias de la búsqueda'>
						$nombre_contacto1 ( $representante_legal1 ) <br>$ubicacion_contacto<br>$direccion_contacto | $telefono_contacto | $mail_contacto
				</div>";//cierra div .art_exp
			}
		}else{
			echo "";
		}		
	}elseif(isset($_POST['buscar_dignatario'])){
		$search_dignatario=$_POST['buscar_dignatario'];
		$query_search_dignatario="select * from contactos where nombre_contacto ilike '%$search_dignatario%' order by representante_legal limit 10";

		$fila_search_dignatario 		= pg_query($conectado,$query_search_dignatario);
		/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_search_dignatario 	= pg_num_rows($fila_search_dignatario);
		/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_search_dignatario>0 ){
			for ($i=0;$i<$registros_search_dignatario;$i++){
				$linea_search_dignatario = pg_fetch_array($fila_search_dignatario);

				$codigo_contacto 				= $linea_search_dignatario['codigo_contacto'];
				$nombre_contacto 				= $linea_search_dignatario['nombre_contacto'];
				$ubicacion_contacto 			= $linea_search_dignatario['ubicacion_contacto'];
				$direccion_contacto 			= $linea_search_dignatario['direccion_contacto'];
				$telefono_contacto 				= $linea_search_dignatario['telefono_contacto'];
				$mail_contacto 					= $linea_search_dignatario['mail_contacto'];
				$representante_legal 			= $linea_search_dignatario['representante_legal'];

				echo "<div class='art_exp' onclick=\"javascript:cargar_nombre_contacto('$nombre_contacto','$representante_legal','$ubicacion_contacto','$direccion_contacto','$telefono_contacto','$mail_contacto','$codigo_contacto')\" title='Esta es una sugerencia. No es obligatorio. Solo muestra los 10 primeras coincidencias de la búsqueda'>$representante_legal ($nombre_contacto) <br>$ubicacion_contacto<br>$direccion_contacto | $telefono_contacto | $mail_contacto
				</div>";//cierra div .art_exp
			}
		}else{
			echo "";
		}		
	}
	// elseif(isset($_POST['buscar_dignatario_rem'])){
	// 	$search_dignatario=$_POST['buscar_dignatario_rem'];
	// 	$query_search_dignatario="select * from contactos where representante_legal ilike '%$search_dignatario%' order by nombre_contacto, representante_legal limit 10";

	// 	$fila_search_dignatario 		= pg_query($conectado,$query_search_dignatario);
	
	// 	/*Calcula el numero de registros que genera la consulta anterior.*/
	// 	$registros_search_dignatario 	= pg_num_rows($fila_search_dignatario);
		
	// 	/*Recorre el array generado e imprime uno a uno los resultados.*/	
	// 	if($registros_search_dignatario>0 ){
	// 		for ($i=0;$i<$registros_search_dignatario;$i++){
	// 			$linea_search_dignatario = pg_fetch_array($fila_search_dignatario);

	// 			$codigo_contacto 				= $linea_search_dignatario['codigo_contacto'];
	// 			$nombre_contacto 				= $linea_search_dignatario['nombre_contacto'];
	// 			$ubicacion_contacto 			= $linea_search_dignatario['ubicacion_contacto'];
	// 			$direccion_contacto 			= $linea_search_dignatario['direccion_contacto'];
	// 			$telefono_contacto 				= $linea_search_dignatario['telefono_contacto'];
	// 			$mail_contacto 					= $linea_search_dignatario['mail_contacto'];
	// 			$representante_legal 			= $linea_search_dignatario['representante_legal'];

	// 			$representante_legal1 = trim(str_ireplace($search_dignatario, "<font color='red'>$search_dignatario</font>", $representante_legal));

	// 			echo "<div class='art_exp' onclick=\"javascript:cargar_nombre_contacto('$nombre_contacto','$representante_legal','$ubicacion_contacto','$direccion_contacto','$telefono_contacto','$mail_contacto','$codigo_contacto')\" title='Esta es una sugerencia. No es obligatorio. Solo muestra los 10 primeros resultados de la consulta.'>$representante_legal1 ($nombre_contacto) <br>$ubicacion_contacto<br>$direccion_contacto | $telefono_contacto | $mail_contacto
	// 			</div>";//cierra div .art_exp
	// 		}
	// 	}else{
	// 		echo "";
	// 	}
	// }
	elseif(isset($_POST['agregar_nuevo_contacto'])){
		$nombre_remitente 		= $_POST['agregar_nuevo_contacto'];
        $dignatario_remitente 	= $_POST['dignatario_remitente'];
        $ubicacion_remitente 	= $_POST['ubicacion_remitente'];
        $direccion_remitente  	= $_POST['direccion_remitente'];
        $telefono_remitente  	= $_POST['telefono_remitente'];
        $mail_remitente  		= $_POST['mail_remitente'];	

        $query_agregar_contacto = "insert into contactos (nombre_contacto, ubicacion_contacto, direccion_contacto, telefono_contacto, mail_contacto, representante_legal) values ('$nombre_remitente', '$ubicacion_remitente', '$direccion_remitente', '$telefono_remitente', '$mail_remitente', '$dignatario_remitente')";

        if(pg_query($conectado,$query_agregar_contacto)){	// Si se crea el contacto
       		// Variable para historico eventos
			$transaccion 			= "agregar_nuevo_contacto";	 					// Variable para auditoria
			$creado  				= "$nombre_remitente ($dignatario_remitente)"; 	// Variable para auditoria

			echo "<script> 
				auditoria_general('agregar_nuevo_contacto','$creado');
			</script>";
        }else{
        	echo "Hubo un error al crear el contacto. Comuníquese con el administrador del sistema.";
        }	
    }elseif (isset($_POST['radicado_falta_pdf'])) {
		$search_radicado_falta_pdf=strtoupper($_POST['radicado_falta_pdf']);
		
		$longitud_search_radicado_falta_pdf = strlen($search_radicado_falta_pdf);

		if($search_radicado_falta_pdf==""){
			$and_search = "";
		}else{
			$and_search = "and r.numero_radicado ilike '%$search_radicado_falta_pdf%'";
		}
		$query_radicado_falta_pdf = "select distinct r.numero_radicado, r.asunto, r.fecha_radicado, r.descripcion_anexos, r.dependencia_actual, u.perfil, u.login, d.nombre_dependencia from radicado r join usuarios u on r.dependencia_actual=u.codigo_dependencia join dependencias d on r.dependencia_actual=d.codigo_dependencia where r.usuario_radicador='$login' and u.perfil='DISTRIBUIDOR_DEPENDENCIA' and r.path_radicado is null $and_search order by fecha_radicado desc";

		$fila_radicado_falta_pdf = pg_query($conectado,$query_radicado_falta_pdf);
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_radicado_falta_pdf= pg_num_rows($fila_radicado_falta_pdf);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_radicado_falta_pdf>0 ){
			$num_fila=0;
			for ($i=0;$i<$registros_radicado_falta_pdf;$i++){
				$linea_radicado_falta_pdf = pg_fetch_array($fila_radicado_falta_pdf);

				$numero_radicado 				= trim($linea_radicado_falta_pdf['numero_radicado']);
				$asunto 	 					= $linea_radicado_falta_pdf['asunto'];
				$dependencia_destino 			= $linea_radicado_falta_pdf['dependencia_actual'];
				$descripcion_anexos 			= $linea_radicado_falta_pdf['descripcion_anexos'];
				$fecha_radicado  				= $linea_radicado_falta_pdf['fecha_radicado'];
				$nombre_dependencia_destino 	= $linea_radicado_falta_pdf['nombre_dependencia'];
				$usuario_actual 				= $linea_radicado_falta_pdf['login'];

				$numero_radicado1 = trim(str_ireplace($search_radicado_falta_pdf, "<font color='red'>$search_radicado_falta_pdf</font>", $numero_radicado)); 

				if($longitud_search_radicado_falta_pdf==18){		
					echo "<script>cargar_modificacion('$numero_radicado', '','$usuario_actual')</script>";
				}else{
					if($asunto==""){
						$asu = "<b>Sin Asunto todavía.</b>";
					}else{
						$asu = "<b>Asunto: </b> ($asunto)";
					}

					echo"<div title='Cargar este radicado' class='art ";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "' onclick=\"javascript:cargar_modificacion('$numero_radicado','','$usuario_actual')\">
						<b>$numero_radicado1</b> - <b> Anexos:</b> ($descripcion_anexos)
						<br><b>Dependencia destino:</b> ($dependencia_destino - $nombre_dependencia_destino) <br> $asu";
					echo "</div>";//cierra div class='sugerencia_contacto'(art)
				}	
				$num_fila++;		
			}
		}else{
			echo "<div id='sin_dependencia' class='errores' style='display:block;'> 
				No se han encontrado resultados. El numero de radicado que intenta ya tiene imagen PDF ó no se encuentra en la base de datos. Revise nuevamente o comuníquese con el administrador del sistema.
			</div>";
		}
	}else{
		echo "El sistema no esta recibiendo la variable correcta. Por favor comuníquese con el administrador del sistema.";
	}	
/*Fin recorre el array generado e imprime uno a uno los resultados.*/	
?>