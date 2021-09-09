<?php 
require_once('../login/conexion2.php');

$timestamp  	= date('Y-m-d H:i:s');	// Genera la fecha de transaccion	
$recibe_ajax 	= $_POST['recibe_ajax'];

/* Se define $year y $month para luego asignarles $mes para poner en la ruta del PDF con el fin de
separar lo que mensualmente se hace para facilitar los backup */
$year  = date("Y");
$month = date("m");

switch ($month){
    case '01':
        $mes = "ENERO";
        break;
    case '02':
        $mes = "FEBRERO";
        break;
    case '03':
        $mes = "MARZO";
        break;
    case '04':
        $mes = "ABRIL";
        break;
    case '05':
        $mes = "MAYO";
        break;
    case '06':
        $mes = "JUNIO";
        break;
    case '07':
        $mes = "JULIO";
        break;
    case '08':
        $mes = "AGOSTO";
        break;
    case '09':
        $mes = "SEPTIEMBRE";
        break;
    case '10':
        $mes = "OCTUBRE";
        break;
    case '11':
        $mes = "NOVIEMBRE";
        break;
    case '12':
        $mes = "DICIEMBRE";
        break;     
}

switch ($recibe_ajax) {
	case 'adjuntar_archivo':
		require_once('../login/validar_inactividad.php');// Se valida la inactividad 

		$query_max_adjuntos = "select max(id) from adjuntos";

		$fila_max_adjuntos  	= pg_query($conectado,$query_max_adjuntos); 
		$linea_max_adjuntos 	= pg_fetch_array($fila_max_adjuntos);
        $max_adjuntos1 			= $linea_max_adjuntos[0];

        if($max_adjuntos1 ==""){
        	$max_adjuntos = 1;
        }else{
		    $max_adjuntos = $max_adjuntos1+1;
        }
        $asunto 		= $_POST['asunto'];
        $radicado 		= $_POST['radicado'];
        $path_adjunto 	= "$year/$mes/$radicado".$max_adjuntos.".pdf"; 

        $query_anexos="insert into adjuntos(id,numero_radicado,fecha_radicado,asunto,path_adjunto)values('$max_adjuntos','$radicado','$timestamp','$asunto','$path_adjunto')";

		$target_file = basename($_FILES["archivo_pdf_radicado"]["name"]); // Nombre que trae el archivo

        $target_dir 	= "../bodega_pdf/adjuntos/";

		$asunto1 = substr($asunto,0,68);

		if(move_uploaded_file($_FILES["archivo_pdf_radicado"]["tmp_name"],$target_dir.$path_adjunto)){
			if(pg_query($conectado,$query_anexos)){
			/* Desde aqui se genera historico */	
				$transaccion_historico 	= "Anexo archivo";	// Variable para tabla historico_eventos
				$comentario				= "Se adjunta archivo ($asunto1) a radicado";					// Variable para historico eventos

				$transaccion 			= "anexar_archivo"; 	// Variable para auditoria
				$creado 				= "$radicado";			// Variable para auditoria
				require_once("../login/inserta_historico.php");		
			/* Hasta aqui se genera historico */	
			}else{
				echo "<script> alert('Ocurrió un error al adjuntar el archivo al radicado, por favor revisar e intentar nuevamente.')</script>";
			}
		}

		break;

	case 'aprobar_firmar': // Recibe desde include/js/funciones_radicacion_salida[function validar_aprueba_firma()]
		session_start();
		$tipo  				= $_POST['tipo'];
		$numero_radicado  	= $_POST['numero_radicado'];
		$version_radicado  	= $_POST['version_radicado'];
		$observaciones  	= $_POST['observaciones'];

		$usuario 			= $_SESSION['login']; 

		$version2=$version_radicado-1;

		$query_version_documentos = "update version_documentos set aprobado='SI' where numero_radicado='$numero_radicado';";
		
		if(pg_query($conectado,$query_version_documentos)){ 
			$radicado 				= $numero_radicado;
			$transaccion_historico 	= "Aprobado electrónicamente";	// Variable para tabla historico_eventos
			$comentario 			= "El usuario $usuario ha aprobado electrónicamente el radicado || $observaciones";		// Variable para historico eventos

			$transaccion 			= "aprobar_radicado"; 	// Variable para auditoria
			$creado 				= "$numero_radicado";	// Variable para auditoria
			require_once("../login/inserta_historico.php");		
		}else{
			echo "<script> alert('Ocurrió un error al realizar la actualización del radicado, por favor revisar e intentar nuevamente.')</script>";
		}	
		break;


	case 'aprobar_ubicacion_radicado':
	/*****************************************************************************************
	Inicio funcion para asignar fisicamente un documento a un usuario 
	/*****************************************************************************************
	* @brief Recibe desde include/js/funciones_ubicacion_fisica.js[validar_aprueba_firma()]
	* para modificar en la base de datos la tabla "ubicacion_fisica" cuando un documento se mueve a otro usuario en físico. Después de hacer la actualizacion en la base de datos, genera histórico del movimiento en físico a cada uno de los radicados y en la base de datos de auditoría guarda la transaccion realizada.

	* @param {string} ($radicado_concatendado) Es un listado de radicados separado por coma, los cuales van a ser movidos en físico al usuario que confirma que ha recibido mediante su contraseña (firma_electronica) del sistema el total de estos radicados en físico.
	* @param {string} ($usuario_nuevo) Login del usuario que ha confirmado mediante su contraseña (firma_electronica) del sistema que ha recibido en físico los documentos correspondientes al listado del parámetro $radicado_concatenado.
	*/	

	//Se agrega caso para que cambie el usuario_actual y usuario_anteior
		$radicado_concatendado 	= $_POST['radicado_concatenadado'];
		$usuario_nuevo 			= $_POST['usuario_aprueba'];

		// Extraigo cada uno de los radicados separados por coma	
		$usu  = explode(",", $radicado_concatendado);
		$max  = sizeof($usu);
		$max2 = $max-1;
		//se recorre cada uno de los registros separado por coma

		$update_usuario_propietario_radicado = "";
		$query_historico_radicado_individual = "";
		for ($p=0; $p < $max2; $p++) {  
			$numero_radicado_individual = $usu[$p];

			$query_radicado_individual 	= "select * from ubicacion_fisica where numero_radicado='$numero_radicado_individual'";
			 /* Aqui se ejecuta la query */
			$fila_radicado_individual 	= pg_query($conectado,$query_radicado_individual);
			$linea_radicado_individual 	= pg_fetch_array($fila_radicado_individual); 	
			$usuario_anterior 	 		= $linea_radicado_individual['usuario_actual'];

			//hace una actualizacion por cada radicado
			$update_usuario_propietario_radicado.= "update ubicacion_fisica set usuario_actual = '$usuario_nuevo', usuario_anterior = '$usuario_anterior', fecha='$timestamp' where numero_radicado = '$numero_radicado_individual';";

			$query_historico_radicado_individual.= "insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$numero_radicado_individual', '$usuario_nuevo', 'Recibir documento en fisico', 'El usuario $usuario_nuevo recibe el <b>DOCUMENTO FISICO</b> (correspondiente al radicado $numero_radicado_individual) del usuario $usuario_anterior.', '$timestamp');";				
		}
		$query_total_radicado_individual = $update_usuario_propietario_radicado.$query_historico_radicado_individual;

		if(pg_query($conectado,$query_total_radicado_individual)){
			echo "<script>auditoria_general('recibir_listado_documentos_fisicos','$radicado_concatendado');</script>";
		}else{
			echo "<script> alert('Ocurrió un error al registrar la firma electrónica para recibir los documentos fisicos, por favor revisar e intentar nuevamente.')</script>";
		}
		
	/* Fin funcion para asignar fisicamente un documento a un usuario */
	/*****************************************************************************************/  
		break;

	case 'aprobar_ubicacion_radicado_reporte':
	/*****************************************************************************************
	Inicio funcion para asignar fisicamente un documento a un usuario y generar planilla de entrega de correspondencia.
	/*****************************************************************************************
	* @brief Recibe desde reportes/reporte1_entrega_correspondencia_entrada.php [validar_aprueba_firma()]
	* para modificar en la base de datos la tabla "ubicacion_fisica" cuando un documento se mueve a otro usuario en físico. Después de hacer la actualizacion en la base de datos, genera histórico del movimiento en físico a cada uno de los radicados y muestra el botón de enlace para descargar la planilla.

	* @param {string} ($radicado_concatendado) Es un listado de radicados separado por coma, los cuales van a ser movidos en físico al usuario que confirma que ha recibido mediante su contraseña (firma_electronica) del sistema el total de estos radicados en físico.
	* @param {string} ($usuario_aprueba) Login del usuario que ha confirmado mediante su contraseña (firma_electronica) del sistema que ha recibido en físico los documentos correspondientes al listado del parámetro $radicado_concatenado.
	*/	

	//Se agrega caso para que cambie el usuario_actual y usuario_anteior
		$radicado_concatendado 	= $_POST['radicado_concatenadado'];
		$login_usuario 			= $_POST['usuario_aprueba'];

		// Extraigo cada uno de los radicados separados por coma	
		$usu  = explode(",", $radicado_concatendado);
		$max  = sizeof($usu);
		$max2 = $max-1;
		//se recorre cada uno de los registros separado por coma

		$update_usuario_propietario_radicado = "";
		$query_historico_radicado_individual = "";
		for ($p=0; $p < $max2; $p++) {  
			$numero_radicado_individual = $usu[$p];

			// $query_radicado_individual 	= "select * from ubicacion_fisica where numero_radicado='$numero_radicado_individual'";
			$query_radicado_individual 	= "select * from ubicacion_fisica u join radicado r on u.numero_radicado = r.numero_radicado where r.numero_radicado='$numero_radicado_individual'";
			 /* Aqui se ejecuta la query */
			$fila_radicado_individual 	= pg_query($conectado,$query_radicado_individual);
			$linea_radicado_individual 	= pg_fetch_array($fila_radicado_individual); 	

			$usuario_actual  	 		= $linea_radicado_individual['usuario_actual'];
			$usuario_anterior 	 		= $linea_radicado_individual['usuario_anterior'];
			$usuarios_control 	 		= $linea_radicado_individual['usuarios_control'];
			$usuario_radicador 	 		= $linea_radicado_individual['usuario_radicador'];

			if (strpos($usuarios_control.",".$usuario_radicador, $usuario_actual)!== false) {
				$usu_actu = "Planilla entrega $usuario_actual";
			}else{
				$usu_actu = $usuario_actual;
			}

			//hace una actualizacion por cada radicado
			$update_usuario_propietario_radicado.= "update ubicacion_fisica set usuario_actual = '$usu_actu', usuario_anterior = '$usuario_actual', fecha='$timestamp' where numero_radicado = '$numero_radicado_individual';";

			$query_historico_radicado_individual.= "insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$numero_radicado_individual', '$login_usuario', 'Generar planilla para entrega de documento en fisico', 'El usuario $login_usuario ha generado la planilla para entrega del <b>DOCUMENTO FISICO</b> (correspondiente al radicado $numero_radicado_individual).', '$timestamp');";
		}
		$query_total_radicado_individual = $update_usuario_propietario_radicado.$query_historico_radicado_individual;

		if(pg_query($conectado,$query_total_radicado_individual)){
			echo "<script>auditoria_general('generar_planilla_documentos_fisicos','$radicado_concatendado');</script>";
		}else{
			echo "error_query";
		}
		
	/* Fin funcion para asignar fisicamente un documento a un usuario */
	/*****************************************************************************************/  
		break;	
	
	case 'archivar_radicado': // Recibe desde bandejas/entrada/index_bandeja_entrada.php[function valida_archivar_radicado()]
		$carpeta_personal  			= $_POST['carpeta_personal'];
		$radicado 				 	= $_POST['radicado'];
		$usuario_codigo_carpeta1 	= $_POST['usuario_codigo_carpeta1'];

		$query_codigo_carpeta1 	= "select codigo_carpeta1 from radicado where numero_radicado='$radicado'";

		$fila_codigo_carpeta1 	= pg_query($conectado,$query_codigo_carpeta1);
    	$linea_codigo_carpeta1 	= pg_fetch_array($fila_codigo_carpeta1);  

    	$codigo_carpeta1 		= json_decode($linea_codigo_carpeta1['codigo_carpeta1'],true); // Paso de JSON a array

		$codigo_carpeta1[$usuario_codigo_carpeta1]['codigo_carpeta_personal'] = $carpeta_personal; // Reemplazo en array la carpeta personal

		$nuevo_codigo_carpeta1 = json_encode($codigo_carpeta1); // Paso de array a JSON con el valor cambiado

		$query_archivar_radicado = "update radicado set codigo_carpeta1 = '$nuevo_codigo_carpeta1' where numero_radicado='$radicado'";

		if(pg_query($conectado,$query_archivar_radicado)){
			echo "<script> 
				auditoria_general('archivar_radicado','$radicado');	
			</script>";		
		}else{
			echo "<script>
				alert('No se pudo archivar el radicado en la carpeta_personal seleccionada. Comuníquese con el administrador del sistema');
				volver();
			</script>";
		}
		break;

	case 'buscar_codigo_serie': // Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function buscar_codigo_nombre_serie()]
		$codigo_serie = $_POST['codigo_serie'];

		/*Esta es la query*/
		$query_codigo_serie = "select * from series where
		 codigo_serie ILIKE '%$codigo_serie%' and activo = 'SI' order by nombre_serie ";

		 /*Aqui se ejecuta la query*/
		$fila_query_codigo_serie  = pg_query($conectado,$query_codigo_serie);

		/*Se trae las filas de la query*/
		$registros_query_codigo_serie = pg_num_rows($fila_query_codigo_serie);
		
		if($registros_query_codigo_serie==0){
			echo "";
		}else{
			for ($i=0; $i < $registros_query_codigo_serie ; $i++){
		    	$linea_codigo_serie = pg_fetch_array($fila_query_codigo_serie);  

				$codigo_serie_bd = $linea_codigo_serie['codigo_serie'];
				$nombre_serie_bd = $linea_codigo_serie['nombre_serie'];
				
				echo"<div class='art'>($codigo_serie_bd) $nombre_serie_bd</div>";
		    } 
		}
	break;	

	case 'buscar_codigo_subserie':		// Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function $('#codigo_subserie').on("input",function(e)]
		$codigo_dependencia_consulta 	= $_POST['codigo_dependencia'];
		$codigo_serie 					= $_POST['codigo_serie'];
		$codigo_subserie 				= $_POST['codigo_subserie'];

		if($codigo_subserie==""){
			echo "";
		}else{	
    		$query_subserie = "select * from subseries where codigo_serie = '$codigo_serie' and codigo_subserie ilike '%$codigo_subserie%' and activo ='SI'";

    		 /*Aqui se ejecuta la query*/
    		$fila_query_subserie  = pg_query($conectado,$query_subserie);

    		/*Se trae las filas de la query*/
    		$registros_query_subserie = pg_num_rows($fila_query_subserie);
    		
    		if($registros_query_subserie==0){
				echo "";
			}else{
				for ($i=0; $i < $registros_query_subserie ; $i++){
			    	$linea_codigo_serie = pg_fetch_array($fila_query_subserie);  

					$codigo_dependencia = $linea_codigo_serie['codigo_dependencia'];
					$codigo_subserie 	= $linea_codigo_serie['codigo_subserie'];
					$nombre_subserie 	= $linea_codigo_serie['nombre_subserie'];

					if(isset($_POST['modificar'])){
		    			$codigo_subserie_old = $_POST['codigo_subserie_old'];
		    			
		    			if($codigo_subserie_old==$codigo_subserie){
		    				$onclick = "onclick=\"cargar_input_codigo_subserie('$codigo_subserie','$nombre_subserie')\" title='Este es el código de subserie que traía originalmente'";
		    			}else{
			    			$onclick = "onclick=\"cargar_error_input_codigo_subserie()\" title='Codigo de subserie NO disponible'";
		    			}						
					}else{
						if($codigo_dependencia_consulta==$codigo_dependencia){
			    			$onclick = "onclick=\"cargar_error_input_codigo_subserie()\" title='Codigo de subserie NO disponible'";
						}else{
			    			$onclick = "onclick=\"cargar_input_codigo_subserie('$codigo_subserie','$nombre_subserie')\" title='Codigo de subserie disponible'";
						}
					}
					echo"<div class='art' $onclick>($codigo_subserie) $nombre_subserie</div>";
			    } 
			}
		}

		break;
	
	case 'buscar_destinatario': // Recibe desde include/js/funciones_radicacion_salida.js [function $("#destinatario_doc").on("input",function(e){ // Accion que se activa cuando se digita #destinatario_doc]
		$search_destinatario 	= $_POST['nombre_buscado'];
		$tipo_busqueda 			= $_POST['tipo_busqueda'];
		
			 	// $query_search_destinatario="select * from contactos where nombre_contacto ilike '%$search_destinatario%' or representante_legal ilike '%$search_destinatario%' order by nombre_contacto, representante_legal limit 10";
		switch ($tipo_busqueda) {
			case 'representante_legal':
			 	$query_search_destinatario="select * from contactos where representante_legal ilike '%$search_destinatario%' order by nombre_contacto, representante_legal limit 10";
				break;
			case 'buscar_destinatario_empresa':
			case 'empresa_entidad':
			 	$query_search_destinatario="select * from contactos where nombre_contacto ilike '%$search_destinatario%' order by nombre_contacto, representante_legal limit 10";
				break;	
		}

		$fila_search_destinatario = pg_query($conectado,$query_search_destinatario);
		/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_search_destinatario= pg_num_rows($fila_search_destinatario);
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

				switch ($tipo_busqueda) {
					case 'buscar_destinatario_empresa':
						echo "<div class='art_exp' onclick=\"javascript:cargar_destinatario_radicado('$representante_legal','$codigo_contacto')\" title='Esta es una sugerencia. No es obligatorio. Solo muestra los 10 primeros resultados de la búsqueda.'>
								$nombre_contacto1 ( $representante_legal1 )
						</div>";//cierra div .art_exp
						break;
	    			case 'empresa_entidad':
					case 'representante_legal':
						echo "<div class='art_exp' onclick=\"javascript:cargar_empresa_entidad_radicado('$codigo_contacto','$nombre_contacto','$ubicacion_contacto','$direccion_contacto','$telefono_contacto','$mail_contacto','$representante_legal')\" title='Esta es una sugerencia. No es obligatorio. Solo muestra los 10 primeros resultados de la búsqueda.'>
								$nombre_contacto1 ( $representante_legal1 ) <br>$ubicacion_contacto<br>$direccion_contacto | $telefono_contacto | $mail_contacto
						</div>";//cierra div .art_exp
						break;
				}		
			}
		}else{
			echo "<div style='background: #53ac36; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.3); color: #FFFFFF; font-size: 12px; padding: 5px; text-align: center;'>El nombre del destinatario no se encuentra en la base de datos</div>";
		}	
		break;
	/*****************************************************************************************
	* @brief Recibe desde include/js/funciones_radicacion_interna.js [function validar_destinatario_doc]
	* @brief Recibe $_POST['nombre_buscado'] es importante, contiene el string que ha escrito el usuario en el campo del destinatario en el formulario de radicacion interna
	* @return {string} Genera la consulta para el destinatario en radicacion interna y muestra los resultados en las sugerencias del formulario de radicacion interna
	*****************************************************************************************/	
	case 'buscar_destinatario_radicacion_interna': // Recibe desde include/js/funciones_radicacion_interna.js
		function validar_longitud($valor_comparar){
			if(strlen($valor_comparar) >= 16){
				$valor_comparar = substr($valor_comparar, 0, 13).'...';
			}
			return $valor_comparar;
		}
		$search_destinatario 	= strtoupper($_POST['nombre_buscado']);// Se pasan los caracteres a mayúsculas
		$array_destinatarios 	= explode(",", $_POST['destinatarios_final']);
		$where_destinatarios 	= '';
		for($i = 0; $i < count($array_destinatarios); $i++){
			$where_destinatarios .= "and nombre_completo <> '".$array_destinatarios[$i]."'";
		}
		$query_search_destinatario     = "select u.id_usuario, u.nombre_completo, u.login, u.perfil, u.codigo_dependencia, d.nombre_dependencia, u.path_foto, u.cargo_usuario
											from usuarios as u
											inner join dependencias as d on d.codigo_dependencia = u.codigo_dependencia
											where u.nombre_completo ilike '%$search_destinatario%' $where_destinatarios
											order by u.nombre_completo 
											limit 10";// Estructura sql
		$fila_search_destinatario      = pg_query($conectado,$query_search_destinatario);// Se enviá la consulta
		$registros_search_destinatario = pg_num_rows($fila_search_destinatario);// Se extrae el numero de registros
		/* Fin consulta a base de datos */
		if($registros_search_destinatario == 0 ){// Valida que el numero de registros de la consulta es 0
			echo "<script>$('.errores_destinatarios').slideDown('slow');
						  $('#desplegable_resultados_inf').slideUp(1);</script>";// Estructura html
		}else{
			for ($i = 0; $i < $registros_search_destinatario; $i++){// Se repetirá la cantidad de registros de la consulta sql
				$linea_search_destinatario 	    = pg_fetch_array($fila_search_destinatario);// Se pasa a un array la información de la consulta
				$id 					= $linea_search_destinatario['id_usuario'];
				$nombre 				= $linea_search_destinatario['nombre_completo'];
				$nombre_corto			= validar_longitud($nombre);
				$login 					= validar_longitud($linea_search_destinatario['login']);
				$perfil		 			= validar_longitud($linea_search_destinatario['perfil']);
				$cod_dependencia		= $linea_search_destinatario['codigo_dependencia'];
				$nombre_dependencia		= $linea_search_destinatario['nombre_dependencia'];
				$path_foto				= $linea_search_destinatario['path_foto'];
				$cargo					= $linea_search_destinatario['cargo_usuario'];
				$cargo2 				= $cargo;
				if($cargo == "") $cargo2 = "Sin cargo asignado";				
				$search_destinatario 	= $search_destinatario;
				$nombre_usuario1  	  	= trim(str_ireplace($search_destinatario, "<font color='red'>".$search_destinatario."</font>", $nombre));// se resalta en rojo lo que coincide en la búsqueda del usuario
				echo "<div onclick='agregar_destinatario(\"$id\", \"$path_foto\", \"$nombre\", \"$nombre_corto\", \"$login\", \"$cod_dependencia\", \"$nombre_dependencia\", \"$cargo\")'>
						<table id='destinatario_usuario_tabla_resultados' style='width: 90%; height: 30px; border: #2aa646 2px solid; border-radius:15px; padding: 3px; transition: height 0.25s, transform 0.25s; text-align: center; cursor: pointer;'>
							<tr>
								<td rowspan=2>
									<img src='$path_foto' class='imagen_destinatario_usuario' style='width: 50px; border-radius: 10px;'> 
								</td>
								<td>
									$nombre_usuario1 - ( $login )<br>
									$perfil / $cargo2<br>
									($cod_dependencia - $nombre_dependencia)
								</td>
							</tr>
						</table>
					</div><br>";// Estructura html
			}
			echo "<script>$('#desplegable_resultados_inf').slideDown('slow');</script>";
		}
		break;
	case 'buscar_firmante': // Recibe desde include/js/funciones_radicacion_salida.js [function $("#firmante_doc").on("input",function(e){ // Accion que se activa cuando se digita #firmante_doc, #aprueba_doc, #elabora_doc]

		// require_once('../login/validar_inactividad.php');// Se valida la inactividad 

		$nombre_buscado 			= trim($_POST['nombre_buscado']);
		$tipo_busqueda 				= $_POST['tipo_busqueda'];
		$lista_firma_aprueba_revisa = $_POST['lista_firma_aprueba_revisa'];

		// Extraigo cada uno de los usuarios de la lista separados por coma	
		$usu  = explode(",", $lista_firma_aprueba_revisa);
		$max  = sizeof($usu);
		$max2 = $max-1;

		$completo_query_usuarios = "";
		//se recorre cada registro separado por coma y agrego la exepcion a la consulta $query_usuarios
		for ($p=0; $p < $max2; $p++) {  
			$login_completo 			= $usu[$p];
			$completo_query_usuarios 	.= " and u.login !='$login_completo' ";
		}
		
		$query_usuarios = "select * from usuarios u join dependencias d on u.codigo_dependencia = d.codigo_dependencia where (u.nombre_completo ilike '%$nombre_buscado%' or u.login ilike '%$nombre_buscado%') and u.login !='ADMINISTRADOR' $completo_query_usuarios and u.estado='ACTIVO' order by login limit 10";

		 /*Aqui se ejecuta la query*/
		$fila_query_usuarios  = pg_query($conectado,$query_usuarios);

		/*Se trae las filas de la query*/
		$registros_query_usuarios = pg_num_rows($fila_query_usuarios);
		
		if($registros_query_usuarios==0){
			echo "sin_registros";
		}else{
			for ($i=0; $i < $registros_query_usuarios ; $i++){
		    	$linea_usuarios = pg_fetch_array($fila_query_usuarios);  

				$nombre_dependencia = $linea_usuarios['nombre_dependencia'];
				$login 				= $linea_usuarios['login'];
				$nombre_completo 	= $linea_usuarios['nombre_completo'];
				$path_foto 			= $linea_usuarios['path_foto'];
				$cargo_usuario 		= $linea_usuarios['cargo_usuario'];

				$login1   			= trim(str_ireplace($nombre_buscado, "<b><font color='red'>$nombre_buscado</font></b>", $login)); // Resalta con rojo el valor buscado
				$nombre_completo1   = trim(str_ireplace($nombre_buscado, "<b><font color='red'>$nombre_buscado</font></b>", $nombre_completo)); // Resalta con rojo el valor buscado

				switch ($tipo_busqueda) {
					case 'carga_aprueba':
					case 'carga_firmante':
						$onclick_buscar_firmante = "$tipo_busqueda(\"$nombre_completo\",\"$login\",\"$cargo_usuario\")";
						break;
					case 'carga_elabora': // Para radicacion de salida
						$onclick_buscar_firmante = "carga_elabora(\"$nombre_completo\",\"$login\",\"$cargo_usuario\")";
						break;	
					
					default:
						$codigo_revisa = substr($tipo_busqueda, -1);
						$onclick_buscar_firmante = "carga_revisa_doc(\"$nombre_completo\",\"$login\",\"$cargo_usuario\",\"$codigo_revisa\")";
						break;
				}
	    		echo"
	    		<div class='resultado_busq_usuario' onclick='$onclick_buscar_firmante'>
					<table style='border: #2aa646 2px solid; border-radius:15px; cursor:pointer;'>	
						<tr>
							<td rowspan=2 width='1%'>
								<img src='$path_foto' style='width: 50px;border-radius: 10px;'> 
							</td>
							<td width='39%'>
								$nombre_completo1 <br>($login1)
							</td>
						</tr>
							<td>
								<b>$nombre_dependencia</b>
							</td>
						<tr>
						</tr>
					</table>
				</div>
	    		";
		    } 
		}
		break;
	case 'buscar_firmante_radicacion_interna': // Recibe desde include/js/funciones_radicacion_salida.js [function $("#firmante_doc").on("input",function(e){ // Accion que se activa cuando se digita #firmante_doc, #aprueba_doc, #elabora_doc]
		$nombre_buscado = $_POST['nombre_buscado'];
		$tipo_busqueda 	= $_POST['tipo_busqueda'];
		
		$query_usuarios = "select 
								u.id_usuario,
								u.codigo_dependencia,
								d.nombre_dependencia,
								u.login,
								u.nombre_completo, u.path_foto,
								u.cargo_usuario
							from 
								usuarios u 
							join 
								dependencias d 
									on u.codigo_dependencia = d.codigo_dependencia 
							where 
								(u.nombre_completo ilike '%$nombre_buscado%' or u.login ilike '%$nombre_buscado%') and 
								u.login != 'ADMINISTRADOR' and 
								u.estado = 'ACTIVO' 
							order by 
								login 
							limit 
								10";
		 /*Aqui se ejecuta la query*/
		$fila_query_usuarios  = pg_query($conectado,$query_usuarios);

		/*Se trae las filas de la query*/
		$registros_query_usuarios = pg_num_rows($fila_query_usuarios);
		
		if($registros_query_usuarios==0){
			echo "sin_registros";
		}else{
			for ($i=0; $i < $registros_query_usuarios ; $i++){
		    	$linea_usuarios = pg_fetch_array($fila_query_usuarios);  
		    	$id_usuario 		= $linea_usuarios['id_usuario'];
		    	$codigo_dependencia = $linea_usuarios['codigo_dependencia'];
				$nombre_dependencia = $linea_usuarios['nombre_dependencia'];
				$nombre_completo 	= $linea_usuarios['nombre_completo'];
				$login 				= $linea_usuarios['login'];				
				$path_foto 			= $linea_usuarios['path_foto'];
				$cargo_usuario 		= $linea_usuarios['cargo_usuario'];
				$cargo2 				 = $cargo_usuario;
				if($cargo_usuario == "") $cargo2 = "Sin cargo asignado";
				$nombre_completo1   = trim(str_ireplace($nombre_buscado, "<b><font color='red'>$nombre_buscado</font></b>", $nombre_completo)); // Resalta con rojo el valor buscado

	    		echo"<div class='resultado_busq_usuario' onclick='$tipo_busqueda(\"$id_usuario\", \"$codigo_dependencia\", \"$nombre_dependencia\", \"$nombre_completo\",\"$login\", \"$path_foto\", \"$cargo_usuario\")'>
						<table style='border: #2aa646 2px solid; border-radius:15px; cursor:pointer;'>	
							<tr>
								<td rowspan=2 width='1%'>
									<img src='$path_foto' style='width: 50px;border-radius: 10px;'> 
								</td>
								<td width='39%'>
									$nombre_completo1 <br>($login)<br>
									$cargo2
								</td>
							</tr>
							<tr>
								<td>
									<b>$nombre_dependencia</b>
								</td>
							</tr>
						</table>
					</div>";
		    } 
		}
		break;
	case 'buscar_nombre_serie': // Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function buscar_codigo_nombre_serie()]
		$nombre_serie = $_POST['nombre_serie'];
		
		/*Esta es la query*/
		$query_nombre_serie = "select * FROM SERIES WHERE
		 nombre_serie ILIKE '%$nombre_serie%' AND ACTIVO = 'SI' ORDER BY NOMBRE_SERIE ";

		 /*Aqui se ejecuta la query*/
		$fila_query_nombre_serie  = pg_query($conectado,$query_nombre_serie);

		/*Se trae las filas de la query*/
		$registros_query_nombre_serie = pg_num_rows($fila_query_nombre_serie);
		
		if($registros_query_nombre_serie==0){
			echo "";
		}else{
			for ($i=0; $i < $registros_query_nombre_serie ; $i++){
		    	$linea_nombre_serie = pg_fetch_array($fila_query_nombre_serie);  

				$codigo_serie_bd = $linea_nombre_serie['codigo_serie'];
				$nombre_serie_bd = $linea_nombre_serie['nombre_serie'];
				
	    		if(isset($_POST['modificar'])){
	    			$codigo_serie = $_POST['codigo_serie'];
	    			if($codigo_serie==$codigo_serie_bd){
	    				$onclick = "onclick=\"cargar_input_nombre_serie('$nombre_serie_bd')\"";
	    			}else{
	    				$onclick = "onclick=\"cargar_error_input_nombre_serie()\"";
	    			}
	    		}else{
	    			$onclick = "";
	    		}

				echo"<div class='art' $onclick>($codigo_serie_bd) $nombre_serie_bd</div>";
		    } 
		}
		break;
	
	case 'buscar_nombre_subserie':	// Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[$('#nombre_subserie').on("input",function(e)]
		$codigo_dependencia_consulta 	= $_POST['codigo_dependencia'];
		$codigo_serie 					= $_POST['codigo_serie'];
		$nombre_subserie 				= $_POST['nombre_subserie'];

		if($nombre_subserie==""){
			echo "";
		}else{
    		$query_nombre_subserie = "select * from subseries where codigo_serie='$codigo_serie' and nombre_subserie ilike '%$nombre_subserie%' and activo='SI'";

    		 /*Aqui se ejecuta la query*/
    		$fila_query_nombre_subserie  = pg_query($conectado,$query_nombre_subserie);

    		/*Se trae las filas de la query*/
    		$registros_query_nombre_subserie = pg_num_rows($fila_query_nombre_subserie);
    		
    		if($registros_query_nombre_subserie==0){
				echo "";
			}else{
				for ($i=0; $i < $registros_query_nombre_subserie ; $i++){
			    	$linea_nombre_subserie = pg_fetch_array($fila_query_nombre_subserie);  

					$codigo_dependencia = $linea_nombre_subserie['codigo_dependencia'];
					$codigo_serie 		= $linea_nombre_subserie['codigo_serie'];
					$codigo_subserie 	= $linea_nombre_subserie['codigo_subserie'];
					$nombre_serie 		= $linea_nombre_subserie['nombre_serie'];
					$nombre_subserie 	= $linea_nombre_subserie['nombre_subserie'];

					
					if(isset($_POST['modificar'])){
		    			$nombre_subserie_old = $_POST['nombre_subserie_old'];

		    			if($nombre_subserie_old==$nombre_subserie){
		    				$onclick = "onclick=\"cargar_input_codigo_subserie('$codigo_subserie','$nombre_subserie')\" title='Este es el nombre de subserie que traía originalmente'";
		    			}else{
		    				$onclick = "onclick=\"cargar_error_input_nombre_subserie()\" title='Nombre de subserie NO disponible'";
		    			}
		    		}else{
		    			if($codigo_dependencia_consulta==$codigo_dependencia){
			    			$onclick = "onclick=\"cargar_error_input_nombre_subserie()\" title='Nombre de subserie NO disponible'";
						}else{
			    			$onclick = "onclick=\"cargar_input_codigo_subserie('$codigo_subserie','$nombre_subserie')\" title='Nombre de subserie disponible'";
						}
		    		}	
		    		echo"<div class='art' $onclick> <font color='blue'> Serie ($codigo_serie) $nombre_serie </font> - ($codigo_subserie) $nombre_subserie</div>";
			    } 
			}
		}
		break;

	case 'buscar_ubicacion':// Recibe desde include/js/funciones_radicacion_salida.js [function enlistar_ubicacion()]
		$ubicacion_buscar = strtoupper($_POST['ubicacion_buscar']);
		
		$query = "select 
						nombre_continente, nombre_pais, nombre_departamento, nombre_municipio
					from 
						municipios
					where 
						nombre_municipio ilike '%$ubicacion_buscar%' 
						or nombre_departamento ilike '%$ubicacion_buscar%'
						
					order by nombre_municipio 
					limit 3";
		 /*Aqui se ejecuta la query*/
		$fila_query = pg_query($conectado,$query);

		/*Se trae las filas de la query*/
		$registros_query = pg_num_rows($fila_query);
		
		if($registros_query==0){
			echo "<div class='botones_expediente'>
					<b>Sin registros</b>
				</div>";
		}else{
			for ($i=0; $i < $registros_query ; $i++){
		    	$linea 					= pg_fetch_array($fila_query);  
		    	$nombre_continente 		= $linea['nombre_continente'];
		    	$nombre_pais 			= $linea['nombre_pais'];
				$nombre_departamento 	= $linea['nombre_departamento'];
				$nombre_municipio 		= $linea['nombre_municipio'];

				if($nombre_municipio == "BOGOTA, D.C."){
					$ubicacion = $nombre_municipio.' - '.$nombre_pais.' - '.$nombre_continente;
				}else{
					$ubicacion = $nombre_municipio.' - '.$nombre_departamento.' - '.$nombre_pais.' - '.$nombre_continente;
				}
				$ubicacion_1   = trim(str_ireplace($ubicacion_buscar, "<b><font color='red'>$ubicacion_buscar</font></b>", $ubicacion)); // Resalta con rojo el valor buscado
	    		echo"<div class='botones_expediente' onclick='agregar_ubicacion(\"".$ubicacion."\")' >
						<b>$ubicacion_1</b>
					</div>";
		    } 
		}
		break;

		case 'buscar_ubicacion2':// Recibe desde include/js/funciones_radicacion_salida.js [function enlistar_ubicacion2()]
		$ubicacion_buscar = strtoupper($_POST['ubicacion_buscar']);
		
		$query = "select 
						nombre_continente, nombre_pais, nombre_departamento, nombre_municipio
					from 
						municipios
					where 
						nombre_municipio ilike '%$ubicacion_buscar%' 
						or nombre_departamento ilike '%$ubicacion_buscar%'
						
					order by nombre_municipio 
					limit 3";
		 /*Aqui se ejecuta la query*/
		$fila_query = pg_query($conectado,$query);

		/*Se trae las filas de la query*/
		$registros_query = pg_num_rows($fila_query);
		
		if($registros_query==0){
			echo "<div class='botones_expediente'>
					<b>Sin registros</b>
				</div>";
		}else{
			for ($i=0; $i < $registros_query ; $i++){
		    	$linea 					= pg_fetch_array($fila_query);  
		    	$nombre_continente 		= $linea['nombre_continente'];
		    	$nombre_pais 			= $linea['nombre_pais'];
				$nombre_departamento 	= $linea['nombre_departamento'];
				$nombre_municipio 		= $linea['nombre_municipio'];

				if($nombre_municipio == "BOGOTA, D.C."){
					$ubicacion = $nombre_municipio.' - '.$nombre_pais.' - '.$nombre_continente;
				}else{
					$ubicacion = $nombre_municipio.' - '.$nombre_departamento.' - '.$nombre_pais.' - '.$nombre_continente;
				}
				$ubicacion_1   = trim(str_ireplace($ubicacion_buscar, "<b><font color='red'>$ubicacion_buscar</font></b>", $ubicacion)); // Resalta con rojo el valor buscado
	    		echo"<div class='botones_expediente' onclick='agregar_ubicacion2(\"".$ubicacion."\")' >
						<b>$ubicacion_1</b>
					</div>";
		    } 
		}
		break;







	case 'buscar_ubicacion_topografica': // Recibe desde inventario/index_inidividual.php[$('#numero_caja_archivo_central').keyup()]

		$valor = $_POST['valor'];

		$query_buscador_ubicacion = "select * from ubicacion_topografica where nombre_nivel ilike '%$valor%' and activa='SI' limit 50";

		$fila_buscador_ubicacion  = pg_query($conectado,$query_buscador_ubicacion);

		/* Calcula el numero de registros que genera la consulta anterior. */
	    $registros_consulta= pg_num_rows($fila_buscador_ubicacion);

	    for ($i=0; $i < $registros_consulta ; $i++){
	    	$linea			= pg_fetch_array($fila_buscador_ubicacion);  

			$id_ubicacion  	= $linea['id_ubicacion'];
			$nombre_nivel 	= $linea['nombre_nivel'];

			echo "<div class='art' onclick=\"oculta_resultado_ubicacion_topografica() ; cargar_input('numero_caja_archivo_central','$nombre_nivel') ;cargar_id('$id_ubicacion')\">$nombre_nivel</div>";
	    } 
		break;
	

	case 'buscar_ubicacion_topografica_desde_exp_inv':
		$numero_expediente = $_POST['numero_expediente'];

		$query_ubicacion ="select * from expedientes e join ubicacion_topografica u on e.codigo_ubicacion_topografica=u.id_ubicacion where e.id_expediente='$numero_expediente'";
		$fila_ubicacion 	= pg_query($conectado,$query_ubicacion);
    	$linea_ubicacion 	= pg_fetch_array($fila_ubicacion);  

    	$codigo_ubicacion_topografica 	= $linea_ubicacion['codigo_ubicacion_topografica'];
    	$nombre_nivel 					= $linea_ubicacion['nombre_nivel'];

    	echo "<script>$('#numero_caja_archivo_central').val('$nombre_nivel');$('#id_caja_archivo_central').val('$codigo_ubicacion_topografica');$('#id_expediente').val('$numero_expediente');</script>";

		break;

	/*****************************************************************************************
	* @brief Recibe desde bandejas/entrada/transacciones_radicado.php[$('#usuario_actual_nuevo').on("input",function(e)]
	* @return {string} devuelve un listado de divs con borde verde, que contiene tablas con la información de nombre completo
	** del usuario que coincide con la busqueda, (LOGIN del usuario) y dependencia del usuario que se busca.  
	*****************************************************************************************/	
	case 'buscar_usuario':  // Recibe desde bandejas/entrada/transacciones_radicado.php[$('#usuario_actual_nuevo').on("input",function(e)]
		$valor_buscado 				= strtoupper($_POST['valor_buscado']);
		$usuarios_actuales 			= $_POST['usuarios_actuales'];
		$tipo_formulario 			= $_POST['tipo_formulario'];

		// Extraigo cada uno de los usuarios_actuales	
		$usu  = explode(",", $usuarios_actuales);
		$max  = sizeof($usu);
		$max2 = $max-1;

		$usuario_actual1="";
		
		// echo "max2 es $max2";
		for ($p=0; $p < $max2; $p++) {  // Genera restricción de usuarios que ya existen.
			$login_usuario_busq = $usu[$p];
			$usuario_actual1 = $usuario_actual1." and u.login!='$login_usuario_busq' ";
		}

		switch ($tipo_formulario) { // Tipo de formulario para definir funcion de buscar usuario
			case 'derivar_radicado':
				$tipo_form = "cargar_nuevo_usuario_derivar_radicado";
				break;
			case 'informar_radicado':
				$tipo_form = "cargar_nuevo_usuario_informar_radicado";
				break;
			
			default:
				# code...
				break;
		}
		/* Se consulta primero por codigo de dependencia o nombre de dependencia, el usuario que tiene perfil de "DISTRIBUIDOR_DEPENDENCIA" */
		$query_usuarios = "select * from usuarios u join dependencias d on u.codigo_dependencia = d.codigo_dependencia where ((d.codigo_dependencia ilike '%$valor_buscado%' and u.perfil ='DISTRIBUIDOR_DEPENDENCIA') or (d.nombre_dependencia ilike '%$valor_buscado%' and u.perfil='DISTRIBUIDOR_DEPENDENCIA')) and u.login !='ADMINISTRADOR' $usuario_actual1 and u.estado='ACTIVO' order by perfil, login limit 10";
		
		$fila_query_usuarios  = pg_query($conectado,$query_usuarios);

		/*Se trae las filas de la query*/
		$registros_query_usuarios = pg_num_rows($fila_query_usuarios);

		if($registros_query_usuarios==0){
			/*Esto quiere decir que no hay codigo_dependencia-nombre_dependencia con la busqueda solicitada por lo que se debe pasar a buscar por nombre-login de usuarios activos. La variable se sigue llamando $query_usuarios, $fila_query_usuarios y $registros_query_usuarios ya que reemplaza o sobreescribe
			la consulta anterior hecha por codigo_dependencia-nombre_dependencia */
			$query_usuarios = "select * from usuarios u join dependencias d on u.codigo_dependencia = d.codigo_dependencia where (u.nombre_completo ilike '%$valor_buscado%' or u.login ilike '%$valor_buscado%') and u.login !='ADMINISTRADOR' $usuario_actual1 and u.estado='ACTIVO' order by login limit 10";
			$fila_query_usuarios  		= pg_query($conectado,$query_usuarios);
			$registros_query_usuarios 	= pg_num_rows($fila_query_usuarios);
		}

		if($registros_query_usuarios==0){
			echo "<div class='errores' style='display: block; font-size:18px;'>No se encuentran resultados<div>";
		}else{
			for ($i=0; $i < $registros_query_usuarios ; $i++){
		    	$linea_usuarios = pg_fetch_array($fila_query_usuarios);  

				$codigo_dependencia		= $linea_usuarios['codigo_dependencia'];
				$login 					= $linea_usuarios['login'];
				$nombre_completo 		= $linea_usuarios['nombre_completo'];
				$nombre_dependencia 	= $linea_usuarios['nombre_dependencia'];
				$path_foto 				= $linea_usuarios['path_foto'];

				/* Resalta con rojo el valor buscado */
				$codigo_dependencia1 	= trim(str_ireplace($valor_buscado, "<b><font color='red'>$valor_buscado</font></b>", $codigo_dependencia)); 
				$login1   				= trim(str_ireplace($valor_buscado, "<b><font color='red'>$valor_buscado</font></b>", $login)); 
				$nombre_completo1   	= trim(str_ireplace($valor_buscado, "<b><font color='red'>$valor_buscado</font></b>", $nombre_completo)); 
				$nombre_dependencia1   	= trim(str_ireplace($valor_buscado, "<b><font color='red'>$valor_buscado</font></b>", $nombre_dependencia)); 

	    		echo"
	    		<div class='li resultado_busq_usuario' onclick='$tipo_form(\"$login\")'>
					<table style='width: 400px; border: #2aa646 2px solid; border-radius:15px;'>	
						<tr>
							<td rowspan=2 width='1%'>
								<img src='$path_foto' style='width: 50px;border-radius: 10px;'> 
							</td>
							<td width='39%'>
								$nombre_completo1 <br>($login1)
							</td>
						</tr>
							<td >
								<b>($codigo_dependencia1)$nombre_dependencia1</b>
							</td>
						<tr>
						</tr>
					</table>
				</div>
	    		";
		    } 
		}
		break;

	/*****************************************************************************************
		Case buzon_correo_electronico_archivos_anexos guarda en la posicion indicada los archivos anexos del correo electronico
	/*****************************************************************************************
	* @brief Recibe desde correo_electronico/buzon_correo.php[function BuzonCorreoPrimeraVista()]
	* @return {string} Mensaje de confirmacion de todo el proceso ejecutado
	*****************************************************************************************/	
	case 'buzon_correo_electronico_archivos_anexos':
		if(!isset($_SESSION)){
			session_start();
  		}
  		$usuario  	= $_SESSION['login'];
		include_once('../correo_electronico/anexo_imap/lib_buzon.imap.php');// Se invoca a "lib_buzon.imap.php" que tare las funciones del proceso imap
		$email = new Imap();// Llave para el ingreso a clases función SimpleXML
		if($email->connect($_POST['parte1'], $_POST['parte2'], $_POST['parte3'])){// Se realiza la conexion principal y se verifica su efectividad
			$inbox = $email->archivos_anexos_guardar($usuario);// Se ejecuta el proceso imap
			echo "Guardado completo";
		}else{
			echo "error_connect";// Se vuelve error en caso de la conexion falle
		}
		shell_exec('
					cd ../../../../../../../../../../;
					cd var/www/html/jonas/bodega_pdf/correo_electronico/baul/'.$usuario.';
					lowriter --convert-to pdf *.doc;
					lowriter --convert-to pdf *.docx;
					lowriter --convert-to pdf *.xls;
					lowriter --convert-to pdf *.xlsx;
					lowriter --convert-to pdf *.ppt;
					lowriter --convert-to pdf *.pptx;
					lowriter --convert-to pdf *.png;
					lowriter --convert-to pdf *.jpg;
					lowriter --convert-to pdf *.jpeg;
					lowriter --convert-to pdf *.gif
		');
	break;
	/*****************************************************************************************
		Fin case buzon_correo_electronico_archivos_anexos guarda en la posicion indicada los archivos anexos del correo electronico
	/*****************************************************************************************/
	/*****************************************************************************************
		Case buzon_correo_electronico_guardar re estructura el archivo txt para guardar la informacion nueva y guarda esta informacion a base de datos
	/*****************************************************************************************
	* @brief Recibe desde correo_electronico/buzon_correo.php[function RadicarCorreo()]
	* @return {string} instrucción javascript para redireccionar a la pagina de creado pdf o emprime en consola la instruccion a data base 
	*****************************************************************************************/	
	case 'buzon_correo_electronico_guardar':
		if(!isset($_SESSION)){
			session_start();
  		}
  		$usuario  			= $_SESSION['login'];
		$numero_aleatorio   = $_POST['numero_aleatorio'];
		$mensaje_pdf_buzon  = $_POST['mensaje'];
		$tipo_radicado 	    = 1; // Tipo de radicado (4-Normal, 3- Interna, 2- Salida, 1- Entrada, etc)
		// $year 			    = date("Y"); 
		require_once('../login/validar_consecutivo.php');// Conseguimos el consecutivo del radicado
		/* Guardar informacion en el archivo txt discriminado por cada usuario */
		$archivo = "../bodega_pdf/correo_electronico/baul/".$_SESSION['login']."/info_correo_electronico_radicar_".$_SESSION['login'].".txt";// tomamos el archivo dependiendo del usuario
		if(file_exists($archivo)){// Validamos si existe
		    unlink($archivo);// Si existe lo limpiamos
		}
		if($archivo2 = fopen($archivo, "a")){// Abrimos el archivo y lo especificamos de escritura con la ("a")
		   	fwrite($archivo2, $mensaje_pdf_buzon);// Escribimos el contenido
		    fclose($archivo2);// Cerramos el archivo para evitar posibles conflictos
		}
		/* Fin guardar informacion en el archivo txt discriminado por cada usuario */
		/* Leemos la informacion del txt */
		$archivo_info_pdf    = fopen("../bodega_pdf/correo_electronico/baul/".$_SESSION['login']."/info_correo_electronico_radicar_".$_SESSION['login'].".txt", "r");// Abrimos el archivo y lo leemos
		while (!feof($archivo_info_pdf)){// Leemos cada linea hasta llegar a la ultima del archivo correspondiente
		    $informacion_txt = fread($archivo_info_pdf, 1048576);// Con fread hacemos la lectura del fichero("modo binario seguro") indicandole un valor de 1048576 bytes o 1 megabyte || si es necesario subir este valor hacerlo pero con valores mas altos mas propenso a corromperse
		}
		fclose($archivo_info_pdf);// Cerramos el archivo anterior mente abuierto con fopen
		/* Fin leemos la informacion del txt */
		$informacion_txt     = trim(preg_replace('/\s+/', ' ', $informacion_txt));// Cuando se crea el json y se guarda en el archivo puede generar barra inlcinada, las eliminamos 
		$informacion_txt     = json_decode($informacion_txt, true);// Hacemos una decodificacion de lo extraido en formato json
		/* Miramos si contiene archivos anexos y se crea la instrucion sql para ellos */
		$contador_anexos 	 = 0;
		$query_anexos 		 = "";
		if($informacion_txt["anexos"][0] == "sin_archivos"){// Valida si trae archivos anexos
			$anexos = "Sin Anexos";
		}else{
			$query_max_adjuntos     = "select max(id) from adjuntos";
			$fila_max_adjuntos  	= pg_query($conectado,$query_max_adjuntos); 
			$linea_max_adjuntos 	= pg_fetch_array($fila_max_adjuntos);
		    $max_adjuntos1 			= $linea_max_adjuntos[0];
		    if($max_adjuntos1      == ""){
		    	$max_adjuntos       = 1;
		    }else{
			    $max_adjuntos       = $max_adjuntos1+1;
		    }
			foreach ($informacion_txt["anexos"] as $anexos_nombres) {// Por cada nombre de archivos anexos se repite
				$contador_anexos++;
				$directorio_copiado = "../bodega_pdf/correo_electronico/baul/$usuario/".$anexos_nombres;// Tomamos el archivo del baul donde se descargan
				$directorio_destino = "../bodega_pdf/adjuntos/".$radicado."_".$numero_aleatorio."_".$contador_anexos.".pdf";// Ruta donde se pasa una copia
				mkdir(dirname($directorio_destino), 1048576, true);// Si no existe el directorio lo creamos
				copy($directorio_copiado, $directorio_destino);// Se crea la copia ya validado que los dos exitan
				$query_anexos      .= "insert into adjuntos(id,numero_radicado,fecha_radicado, asunto, usuario_radicador, path_adjunto)values(".$max_adjuntos.",'$radicado','$timestamp','".$anexos_nombres."', '$login_usuario', '".$radicado."_".$numero_aleatorio."_".$contador_anexos.".pdf');";
				$max_adjuntos++;
			}
			$anexos = $contador_anexos." Anexos";	
		}
		/* Fin si contiene archivos anexos y se crea la instrucion sql para ellos */
		$usuario_destino 	= $login_usuario.",";
		$codigo_carpeta1 	= "'{\"$login_usuario\":{\"codigo_carpeta_personal\":\"entrada\"}}'";
		$query_radicado     ="insert into radicado(numero_radicado, fecha_radicado, codigo_carpeta, codigo_carpeta1, numero_guia_oficio, descripcion_anexos, path_radicado, dependencia_actual, usuarios_visor, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, clasificacion_radicado, id_expediente, codigo_serie, codigo_subserie, estado_radicado, usuarios_control, medio_respuesta_solicitado) values('".$radicado."', '".$timestamp."', 'NULL', ".$codigo_carpeta1.", '', '".$anexos."', '".$radicado."_".$numero_aleatorio.".pdf', '".$_SESSION['dependencia']."', '".$usuario_destino."', '".$_SESSION['dependencia']."', '".$login_usuario."', 'Radicado por modulo mail - ".$informacion_txt["informacion"][0]."', 1, '".$usuario_destino."', 'OFICIO', 'NULL', '', '', 'en_tramite', '".$usuario_destino."', 'correo_electronico');";
		$query_origen_radicado = "insert into datos_origen_radicado(numero_radicado, documento_identificacion, nombre_remitente_destinatario, dignatario, ubicacion, direccion, telefono, mail)values('".$radicado."', '', 'Correo Electronico', '".$_POST['nombre']."', 'Documento recibido por modulo email', 'Correo Electronico', '', '".$_POST['correo_usuario']."');";

		if($informacion_txt["anexos"][0] == "sin_archivos"){// Valida si trae archivos anexos
			$query_completa_expediente_radicado_anexos = $query_radicado.$query_origen_radicado;
		}else{
			$query_completa_expediente_radicado_anexos = $query_radicado.$query_origen_radicado.$query_anexos;
		}
		
		if(pg_query($conectado,$query_completa_expediente_radicado_anexos)){// Si el guardado fue exitoso
			require_once("../correo_electronico/segundo_mail.php");
		}else{
			echo '<script>console.log("'.$query_completa_expediente_radicado_anexos.'");</script>';
		}
		$creado 				= $radicado;	
		$transaccion  			= "radicacion_correo_electronico";
		$transaccion_historico 	= "Radicacion correo electronico";
		$comentario 			= "Documento ".$radicado." radicado desde el modulo de mail";
		require_once('../login/inserta_historico.php');
	break;
	/*****************************************************************************************
		Fin case buzon_correo_electronico_guardar re estructura el archivo txt para guardar la informacion nueva y guarda esta informacion a base de datos
	/*****************************************************************************************/
	/*****************************************************************************************
		Case buzon_correo_electronico_informacion_correo_electronico estructura la informacion del buzon en el correo se acceda
	/*****************************************************************************************
	* @brief Recibe desde correo_electronico/buzon_correo.php[function BuzonCorreoPrimeraVista() y BuzonCorreoSegundaVista()]
	* @return {string} Json codificado con la informacion de los mensajes o un mensaje de error
	*****************************************************************************************/	
	case 'buzon_correo_electronico_informacion_correo_electronico':
		include_once('../correo_electronico/anexo_imap/lib_buzon.imap.php');// Se invoca a "lib_buzon.imap.php" que tare las funciones del proceso imap
		$email = new Imap();// Llave para el ingreso a clases función SimpleXML
		if($email->connect($_POST['parte1'], $_POST['parte2'], $_POST['parte3'])){// Se realiza la conexion principal y se verifica su efectividad
			if(isset($_POST['limite']) && isset($_POST['invertido'])){
				$inbox = $email->getMessages('html', $_POST['limite'], $_POST['invertido']);// Se ejecuta el proceso imap
			}else{
				$inbox = $email->getMessages('html', 0, 0);// Se ejecuta el proceso imap
			}
			echo json_encode($inbox, JSON_PRETTY_PRINT);// Devolvemos el json con formato codificado
		}else{
			echo "error_connect";// Se vuelve error en caso de la conexion falle
		}
	break;
	/*****************************************************************************************
		Fin case buzon_correo_electronico_informacion_correo_electronico estructura la informacion del buzon en el correo se acceda
	/*****************************************************************************************/
	case 'buzon_correo_electronico_verificar_usuario':
		if(!isset($_SESSION)){
   			session_start();
   		}
		$usuario = $_SESSION['login']; 		// Genera Usuario
		$query_correo_usuario 			= "select * from usuarios where login='".$usuario."'";
		$fila_lista_radicados 			= pg_query($conectado,$query_correo_usuario);
		$resultado						= pg_fetch_array($fila_lista_radicados); 
		$correo_usuario_electronico  	= $resultado['mail_usuario'];
		$usuario_verificar_correo_electronicio = array('usuario' => $usuario, 'correo_usuario' => $correo_usuario_electronico);
		echo json_encode($usuario_verificar_correo_electronicio, JSON_PRETTY_PRINT);// Devolvemos el json
	break;


	case 'cargar_expediente':  // Recibe desde bandejas/entrada/pestanas.php[cargar_expediente(num_exp)]
		$id_expediente 			= $_POST['id_expediente'];
		$query_lista_radicados 	= "select * from expedientes where id_expediente='$id_expediente'";
		$fila_lista_radicados 	= pg_query($conectado,$query_lista_radicados);
		$linea					= pg_fetch_array($fila_lista_radicados); 
		$lista_radicados 		= $linea['lista_radicados'];
		$radicado 				= $_POST['radicado'];

		/* De la lista de radicados extraigo uno a uno los registros y los consulto en la tabla radicado */ 
		$radicado_separado  = explode(",", $lista_radicados);
		$max  				= sizeof($radicado_separado);
		$max2 				= $max-1;

		$listado_radicados="";
			if($max2==0){ // Caso cuando hay solo un radicado en el expediente
				$numero_radicado 	= $radicado_separado[0];
				$consulta_rad 		= "select * from radicado where numero_radicado='$numero_radicado'";
				$fila_rad 	  		= pg_query($conectado,$consulta_rad);
				$linea_rad    		= pg_fetch_array($fila_rad);

				$asunto   			= $linea_rad['asunto'];
				$fecha_radicado   	= $linea_rad['fecha_radicado'];
				$numero_radicado   	= $linea_rad['numero_radicado'];
				$path_principal 	= $linea_rad['path_radicado'];

				if($radicado==$numero_radicado){
					$estilo = "style='color: blue; font-weight:bold;'";
				}else{
					$estilo = "";
				}
				if($path_principal!=""){
					$path_principal="onclick='visualizar_principal(\"$numero_radicado\",\"$path_principal\",\"radicados_exp\")' title='Ver archivo principal' ";
				}else{
					$path_principal="";
				}

				$listado_radicados = $listado_radicados."<tr class='art_exp center' onclick='cargar_expediente(\"$numero_radicado\")' $estilo>
					<td class='art_exp' $path_principal style='cursor:pointer;'>$numero_radicado Este radicado no se encuentra asignado a ningún expediente</td>
					<td>$fecha_radicado</td>
					<td>$asunto</td>
				</tr>";
				// echo "$consulta_rad";
			}else{		// Caso cuando hay varios radicados en el expediente
				for ($j=0; $j < $max2; $j++) { 
					$k=$j+1;
					$numero_radicado = $radicado_separado[$j];

					$consulta_rad 		= "select * from radicado where numero_radicado='$numero_radicado'";
					$fila_rad 	  		= pg_query($conectado,$consulta_rad);
					$linea_rad    		= pg_fetch_array($fila_rad);

					$asunto   			= $linea_rad['asunto'];
					$fecha_radicado   	= $linea_rad['fecha_radicado'];
					$numero_radicado   	= $linea_rad['numero_radicado'];

					if($radicado==$numero_radicado){
						$estilo = "style='color: blue; font-weight:bold;'";
					}else{
						$estilo = "";
					}

					$path_principal 	= $linea_rad['path_radicado'];

					if($path_principal!=""){
						// $path_principal="class='art_exp' onclick='visualizar_adjunto(\"$path_principal\",\"radicados\")' title='Ver archivo principal' ";
						$path_principal="class='art_exp' onclick='visualizar_principal(\"$numero_radicado\",\"$path_principal\",\"radicados_exp\")' title='Ver archivo principal' ";
					}else{
						$path_principal="title='No tiene archivo asociado'";
					}
					/* Desde aqui consulto los adjuntos que tenga el radicado */
					$query_adjuntos		="select * from adjuntos where numero_radicado='$numero_radicado'";
					$fila_adjuntos 		= pg_query($conectado,$query_adjuntos);
					$cantidad_adjuntos  = pg_num_rows($fila_adjuntos);

					$adjunto= "";
					if($cantidad_adjuntos!=0){
				
						$tabla_adjuntos = "";	
						for ($m=0;$m<$cantidad_adjuntos;$m++){
							$linea_adjuntos  = pg_fetch_array($fila_adjuntos);

							$fecha_radicado_a = $linea_adjuntos['fecha_radicado'];
							$asunto_a 		= $linea_adjuntos['asunto'];
							$asunto_b 		= substr($asunto_a, 0, 15)."..."; 

							$path_adjunto 	= $linea_adjuntos['path_adjunto'];

							$tabla_adjuntos.= "
									<div class='art_exp center' style='float:left;' onclick='visualizar_principal(\"$numero_radicado\",\"$path_adjunto\",\"adjuntos_exp\")'>
										<img height='20px' src='imagenes/iconos/archivo_pdf.png' title='Vista Previa' style='float:left'>
										<span title='$asunto_a' style='float:left;padding-left:5px;'>$asunto_b
									</div>	
							";
						}
						$adjunto= "
						<tr>
							<td colspan='5'>$tabla_adjuntos</td>
						</tr>";
						$rowspan = "rowspan='2'";
					}else{
						$rowspan = "";
					}
					/* Hasta aqui consulto los adjuntos que tenga el radicado */

					$listado_radicados = $listado_radicados."<tr class='detalle center' $estilo>
					<td $rowspan>$k</td>
					<td $path_principal style='cursor:pointer;'>$numero_radicado</td>
					<td>$fecha_radicado</td>
					<td>$asunto</td>
					<td title='Adjuntar Archivos a radicado $numero_radicado' class='art_exp' onclick='cargar_adjunto(\"$numero_radicado\",\"$asunto\",\"pestana_expedientes\")'> 
						<img height='20px' src='imagenes/iconos/archivo_anexo.png'>
					</td>
				</tr>$adjunto";
				}
			}
			echo "$listado_radicados";

		break;
	/*****************************************************************************************
		Inicio case cargar_documento_radicacion_interna busca la informacion del radicado y la devuelve en formato json
	/*****************************************************************************************
		* @brief Recibe desde radicacion/radicacion_interna/funciones_radicaion_interna.js[function cargar_documento(radicado)]
		* @param {string} ($radicado) Es obligatorio y se usa para filtrar la consulta
		* @return {string} String con un json codificado con la informacion del radicado
	*****************************************************************************************/		
	case 'cargar_documento_radicacion_interna':
		$radicado 					= $_POST['radicado'];// Se recibe el numero de radicado
		$query_radicado 			= "select 
											descripcion_anexos, 
											asunto,
											codigo_serie,
											codigo_subserie,
											id_expediente 
										from 
											radicado 
										where 
											numero_radicado = '$radicado'";// Estructura sql
		$fila_registros_1			= pg_query($conectado,$query_radicado);// Se envia la consulta sql
		$linea_registros_1			= pg_fetch_array($fila_registros_1);// Se pasa a un array los datos regresados de la consulta
		$json_respuesta 			= '{
										  "descripcion_anexos" 	: "'.$linea_registros_1['descripcion_anexos'].'",
										  "asunto_documento" 	: "'.$linea_registros_1['asunto'].'",
										  "codigo_serie" 		: "'.$linea_registros_1['codigo_serie'].'",
										  "codigo_subserie" 	: "'.$linea_registros_1['codigo_subserie'].'",
										  "id_expediente" 		: "'.$linea_registros_1['id_expediente'].'",';
		if($linea_registros_1['id_expediente'] != ""){
			$query_exp 			= "select 
										nombre_expediente 
									from 
										expedientes 
									where 
										id_expediente = '$id_expediente_mod'";// Estructura sql
			$fila_exp 	  		= pg_query($conectado,$query_exp);// Se envia la consulta sql
			$linea_exp    		= pg_fetch_array($fila_exp);// Se pasa a un array los datos regresados de la consulta
			$json_respuesta    .= '"nombre_expediente" 	: "'.$linea_exp['nombre_expediente'].'",';
		}else{
			$json_respuesta    .= '"nombre_expediente" 	: null,';
		}		
		$query_datos_origen_radicado 	= "select 
												nombre_remitente_destinatario, 
												ubicacion
											from 
												datos_origen_radicado
											where 
												numero_radicado = '$radicado'";// Estructura sql
		$fila_registros_2				= pg_query($conectado,$query_datos_origen_radicado);// Se envia la consulta sql
		$linea_registros_2				= pg_fetch_array($fila_registros_2);// Se pasa a un array los datos regresados de la consulta
		$json_respuesta    			   .= '"dignatario" 		: "'.$linea_registros_2['nombre_remitente_destinatario'].'",
							   			   "ubicacion" 	 		: "'.$linea_registros_2['ubicacion'].'",';
		$query_version_documento 	= "select 
											fecha_modifica, 
											version,
											html_asunto,
											despedida,
											tratamiento,
											usuario_que_firma,
											cargo_usuario_que_firma,
											firmado,
											usuario_que_aprueba,
											cargo_usuario_que_aprueba,
											aprobado,
											usuario_que_elabora,
											cargo_usuario_que_elabora,
											cargo_destinatario,
											ubicacion_remitente
										from 
											version_documentos
										where 
											numero_radicado = '$radicado'";// Estructura sql
		$fila_registros_3				= pg_query($conectado,$query_version_documento);// Se envia la consulta sql
		$linea_registros_3				= pg_fetch_array($fila_registros_3);// Se pasa a un array los datos regresados de la consulta
		$json_respuesta    			   .= '"fecha_modifica" 		: "'.$linea_registros_3['fecha_modifica'].'",
							   			   "version_doc" 	 		: "'.($linea_registros_3['version']+1).'",';
		$html_asunto 					= html_entity_decode($linea_registros_3['html_asunto'], ENT_QUOTES);
		$html_asunto 					= addslashes($html_asunto);
		$html_asunto 					= preg_replace("/[\r\n|\n|\r]+/", " ", $html_asunto);
		$json_respuesta    			   .= '"lista_despedida" 		: "'.$linea_registros_3['despedida'].'",
							   			   "lista_tratamiento" 	 		: "'.$linea_registros_3['tratamiento'].'",
							   			   "firmante" 	 		: "'.$linea_registros_3['usuario_que_firma'].'",
							   			   "cargo_firmante" 	 		: "'.$linea_registros_3['cargo_usuario_que_firma'].'",
							   			   "firmado"			: "'.$linea_registros_3['firmado'].'",
							   			   "aprueba" 	 		: "'.$linea_registros_3['usuario_que_aprueba'].'",
							   			   "cargo_aprueba" 	 		: "'.$linea_registros_3['cargo_usuario_que_aprueba'].'",
							   			   "aprobado" 	 		: "'.$linea_registros_3['aprobado'].'",
							   			   "elabora" 	 		: "'.$linea_registros_3['usuario_que_elabora'].'",
							   			   "cargo_elabora" 	 		: "'.$linea_registros_3['cargo_usuario_que_elabora'].'",
							   			   "cargo_dignatario" 	 		: "'.$linea_registros_3['cargo_destinatario'].'",
							   			   "ubicacion2" 	 		: "'.$linea_registros_3['ubicacion_remitente'].'",
							   			   "html_asunto" 			: "'.$html_asunto.'"
							   			}';
		echo $json_respuesta;
		break;
	/*****************************************************************************************
		Fin case cargar_documento_radicacion_interna busca la informacion del radicado y la devuelve en formato json
	/*****************************************************************************************/
	// case 'cargar_respuesta_radicado':  // Recibe desde bandejas/entrada/pestanas.php[function cargar_respuesta(radicado_padre,radicado_respuesta)]
 //        session_start();
	// 	$radicado_padre 	= $_POST['radicado_padre'];
	// 	$radicado_respuesta = $_POST['radicado_respuesta'];

 //        $path_respuesta		= "$year/$mes/".$radicado_respuesta.".pdf"; 

	// 	$usuario 		= $_SESSION['login']; 		// Genera Usuario 
	// 	$id_usuario 	= $_SESSION['id_usuario']; 
	// 	$leido 			= $usuario.",";

	// 	/* Verifica si existe carpeta personal para Salida */    
 //        $consulta_carpeta_inventario="select * from carpetas_personales where id_usuario='$id_usuario' and nombre_carpeta_personal='Salida'";
 //        $fila_cantidad_carpeta_inventario = pg_query($conectado,$consulta_carpeta_inventario);

	// 	/* Calcula el numero de registros que genera la consulta anterior. */
 //        $registros_carpeta_inventario= pg_num_rows($fila_cantidad_carpeta_inventario);

 //        if($registros_carpeta_inventario!=0){  // Cuando si existe la carpeta personal 
 //            $linea_carpeta_inventario = pg_fetch_array($fila_cantidad_carpeta_inventario);
 //            $codigo_carpeta_inventario=$linea_carpeta_inventario['id'];
 //        }else{       // Cuando no existe la carpeta personal
 //            $query_cantidad_carpeta_per3 = "select count(*) from carpetas_personales";
 //            $fila_cantidad3  = pg_query($conectado,$query_cantidad_carpeta_per3); // La variable "$conectado" la hereda desde conexion2.php
 //            $linea_cantidad3 = pg_fetch_array($fila_cantidad3);
 //            $cantidad_total1 = $linea_cantidad3[0];
 //            $cantidad_total  = $cantidad_total1+1;

 //            $query_crear_carpeta="insert into carpetas_personales (id, nombre_carpeta_personal, id_usuario, activo, fecha_creacion_carpeta_per) values('$cantidad_total', 'Salida', '$id_usuario', 'SI', current_timestamp)";
 //            if(pg_query($conectado,$query_crear_carpeta)){ // Si crea la carpeta personal 'Inventario'
 //                $codigo_carpeta_inventario=$cantidad_total;
 //            }else{
 //                echo "No se ha creado la carpeta personal 'Salida'. Por favor comuníquese con el administrador del sistema.'";
 //            }
 //        }
	// 	/* Fin verifica si existe carpeta personal para inventario */  

 //        // Se arma el json para codigo_carpeta1 de la tabla radicado
	// 	$codigo_carpeta1="'{\"$usuario\":{\"codigo_carpeta_personal\":\"Salida\"}}'";

 //        $query_update_radicado="update radicado set fecha_radicado='$timestamp', codigo_carpeta1=$codigo_carpeta1, path_radicado='$path_respuesta', leido='$leido' where numero_radicado='$radicado_respuesta'"; // Falta definir TRD para ésta respuesta.

	// 	$target_file = basename($_FILES["archivo_pdf_respuesta"]["name"]); // Nombre que trae el archivo
 //        $target_dir 	= "../bodega_pdf/radicados/";

	// 	if(move_uploaded_file($_FILES["archivo_pdf_respuesta"]["tmp_name"],$target_dir.$path_respuesta)){
	// 		if(pg_query($conectado,$query_update_radicado)){

	// 			$nuevo_fichero= "../bodega_pdf/plantilla_generada_tmp/$radicado_padre.docx";
	// 			unlink($nuevo_fichero); // Eliminar plantilla temporal

	// 		/* Desde aqui se genera historico */	
	// 			$radicado 				= $radicado_padre;
	// 			$transaccion_historico 	= "Subir PDF de salida como respuesta";	// Variable para tabla historico_eventos
	// 			$comentario				= "Se carga la respuesta firmada en formato PDF del radicado del radicado $radicado_respuesta como RESPUESTA al radicado $radicado_padre";					// Variable para historico eventos

	// 			$transaccion 			= "salida_respuesta"; 	// Variable para auditoria
	// 			$creado 				= "$radicado_padre";	// Variable para auditoria
	// 			require_once("../login/inserta_historico.php");		
	// 		/* Hasta aqui se genera historico */	
	// 		}else{
	// 			echo "<script> alert('Ocurrió un error al cargar la respuesta al radicado, por favor revisar e intentar nuevamente.')</script>";
	// 		}
	// 	}
		// break;	

	case 'cargar_version_respuesta_radicado': // Recibe desde bandejas/entrada/pestanas.php[cargar_version_respuesta(numero_radicado)]
		$radicado = $_POST['radicado'];

		/* Desde aqui se consulta la version del radicado */
		$query_version_documento	= "select * from version_documentos where numero_radicado='$radicado' order by version desc";
		$fila_version_documento		= pg_query($conectado,$query_version_documento);
		$cantidad_version_documento = pg_num_rows($fila_version_documento);

		$lista_versiones= "";
		
		if($cantidad_version_documento!=0){
			$tabla_adjuntos = "";	
			for ($p=0;$p<$cantidad_version_documento;$p++){
				$linea_version_documento  = pg_fetch_array($fila_version_documento);

				if($p==0){
					$version_actual = $linea_version_documento['version'];
					$path_pdf 		= $linea_version_documento['path_pdf'];
					$path_principal = "style='cursor:pointer;' class='botones2' onclick='visualizar_principal(\"$radicado\",\"$path_pdf\",\"radicados\")' title='Ver archivo principal'";	
				}

				$version 					= $linea_version_documento['version'];
				$path_pdf 					= $linea_version_documento['path_pdf'];
				$usuario_modifica 			= $linea_version_documento['usuario_modifica'];
				$fecha_modifica 			= $linea_version_documento['fecha_modifica'];
				// $usuario_que_firma 			= $linea_version_documento['usuario_que_firma'];
				// $cargo_usuario_que_firma 	= $linea_version_documento['cargo_usuario_que_firma'];
				// $firmado 					= $linea_version_documento['firmado'];
				// $usuario_que_aprueba 		= $linea_version_documento['usuario_que_aprueba'];
				// $cargo_usuario_que_aprueba 	= $linea_version_documento['cargo_usuario_que_aprueba'];
				// $aprobado 					= $linea_version_documento['aprobado'];

					/*if($firmado=="NO"){
						$validacion_firmado = "Falta que el usuario  &#13; $usuario_que_firma  &#13; ($cargo_usuario_que_firma)&#13; firme electrónicamente el documento.";
						$estilo_firmado 	= "background-color:red;";
						$class_firmado 		= "class='art_exp'"; 
						if($aprobado=="NO"){
							$validacion_firmado.="&#13; &#13; Falta que el usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Apruebe electrónicamente el documento";
						}else{
							$validacion_firmado.="&#13; &#13;El usuario &#13; $usuario_que_aprueba &#13; ($cargo_usuario_que_aprueba) &#13; Ha aprobado electrónicamente el documento";
						}
					}else{
						$validacion_firmado = "Firmado electrónicamente por el usuario $usuario_que_firma ($cargo_usuario_que_firma)";
						$estilo_firmado 	= "background-color:green;";
						$class_firmado 		= ""; 
					}*/

				$lista_versiones.= "
					<tr class='center'>
						<td class='detalle botones2' style='cursor:pointer;' onclick='visualizar_principal(\"$radicado\",\"$path_pdf\",\"version\")' title='Ver documento'>$version</td>
						<td class='detalle'>$usuario_modifica</td>
						<td class='detalle'>$fecha_modifica</td>
					</tr>
				";
			}

			// $tiene_version  	= "<br>(Version $version_actual)<br>";

				$tabla_versiones= "
				<tr class='descripcion center'>
					<td>Version</td>
					<td>Usuario que Modifica</td>
					<td>Fecha de Modificación</td>
				</tr>$lista_versiones";
		}else{
			$tabla_versiones = "";
		}
		/* Hasta aqui se consulta las versiones que tenga el radicado */
		$tabla_final = "<table>$tabla_versiones</table>";
		echo $tabla_final;

		break;

	case 'ccd_por_dependencia': // Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function ccd_por_dependencia(codigo_dependencia)]
		$codigo_dependencia = $_POST['codigo_dependencia'];

		if($codigo_dependencia=="todos"){
			$query_dependencia = "select * from dependencias where id_dependencia!=1 order by nombre_dependencia";

			/* Aqui se ejecuta la query */
	    	$fila_query_dependencia  		= pg_query($conectado,$query_dependencia);

	    /* Se trae las filas de la query */
	    	$registros_query_dependencia 	= pg_num_rows($fila_query_dependencia);

    		if($registros_query_dependencia==0){
				echo "<script>alert('No hay dependencias configuradas todavía')</script>";
			}else{					
				for ($i=0; $i < $registros_query_dependencia ; $i++){
			    	$linea_codigo_dependencia 	= pg_fetch_array($fila_query_dependencia);  
					$codigo_dependencia 		= $linea_codigo_dependencia['codigo_dependencia'];
					$nombre_dependencia 		= $linea_codigo_dependencia['nombre_dependencia'];

    				echo "<hr><h1>( $codigo_dependencia ) $nombre_dependencia </h1>";

    				$query_trd_dependencia= "select * from subseries where codigo_dependencia ='$codigo_dependencia' and activo='SI' order by codigo_dependencia,nombre_serie,nombre_subserie";

    			/* Aqui se ejecuta la query */
			    	$fila_query_trd_dependencia  		= pg_query($conectado,$query_trd_dependencia);
			    /* Se trae las filas de la query */
			    	$registros_query_trd_dependencia 	= pg_num_rows($fila_query_trd_dependencia);
			    /*Se trae las filas de la query*/

		    		if($registros_query_trd_dependencia==0){
						echo "<h2>No hay Series ni Subseries asociadas con la dependencia ($codigo_dependencia) $nombre_dependencia.</h2>";
					}else{
						echo "
						<table border='0' id='ccd'>
							<tr class='descripcion center'>
								<td colspan='3' width='5%'>
									Códigos
								</td>
								<td colspan='2' width='50%'>
									Descripción Documental
								</td>
								<td class='descripcion' colspan='2' width='5%'>
									Retención (Años)
								</td>
								<td class='descripcion' colspan='4' width='5%'>
									Disposición final
								</td>
								<td class='descripcion' colspan='2' width='5%'>Soporte</td>
								<td rowspan='2' width='30%'>Procedimiento</td>
							</tr>
							<tr class='center'>
								<td class='descripcion' title='Código de la Dependencia'>
									D
								</td>
								<td class='descripcion' title='Código de la Serie'>
									S
								</td>
								<td class='descripcion' title='Código de la Subserie'>
									Sb
								</td>
								<td class='descripcion'>Serie Documental</td>
								<td class='descripcion'>Subserie Documental </td>
								<td class='descripcion' title='Archivo de Gestión'>AG</td>
								<td class='descripcion' title='Archivo Central'>AC</td>
								<td class='descripcion' title='Eliminación'>E</td>
								<td class='descripcion' title='Selección'>SE</td>
								<td class='descripcion' title='Conservación Total'>CT</td>
								<td class='descripcion' title='Microfilmación o Digitalización'>MD</td>
								<td class='descripcion' title='Papel'>P</td>
								<td class='descripcion' title='Electrónico u Otro Soporte'>EL</td>
							</tr>";

						$contador_series = 1;

						for ($j=0; $j < $registros_query_trd_dependencia; $j++) { 
							$linea_trd_dependencia 	= pg_fetch_array($fila_query_trd_dependencia); 

							$codigo_dependencia 	= $linea_trd_dependencia['codigo_dependencia'];
							$codigo_serie 			= $linea_trd_dependencia['codigo_serie'];
							$codigo_subserie 		= $linea_trd_dependencia['codigo_subserie'];
							$nombre_serie 			= $linea_trd_dependencia['nombre_serie'];
							$nombre_subserie 		= $linea_trd_dependencia['nombre_subserie'];
							$tiempo_archivo_gestion = $linea_trd_dependencia['tiempo_archivo_gestion'];
							$tiempo_archivo_central = $linea_trd_dependencia['tiempo_archivo_central'];
							$soporte_papel 			= $linea_trd_dependencia['soporte_papel'];
							$soporte_electronico 	= $linea_trd_dependencia['soporte_electronico'];
							$eliminacion 			= $linea_trd_dependencia['eliminacion'];
							$seleccion 				= $linea_trd_dependencia['seleccion'];
							$conservacion_total 	= $linea_trd_dependencia['conservacion_total'];
							$microfilmacion_digitalizacion 	= $linea_trd_dependencia['microfilmacion_digitalizacion'];
							$procedimiento 			= $linea_trd_dependencia['procedimiento'];
							$activo 				= $linea_trd_dependencia['activo'];

							if($eliminacion=="SI"){
								$eliminacion1 = "E";
							}else{
								$eliminacion1 = "";
							}

							if($seleccion=="SI"){
								$seleccion1 = "SE";
							}else{
								$seleccion1 = "";
							}
							if($conservacion_total=="SI"){
								$conservacion_total1 = "CT";
							}else{
								$conservacion_total1 = "";
							}
							if($microfilmacion_digitalizacion=="SI"){
								$microfilmacion_digitalizacion1 = "MD";
							}else{
								$microfilmacion_digitalizacion1 = "";
							}
							if($soporte_papel=="SI"){
								$soporte_papel1 = "P";
							}else{
								$soporte_papel1 = "";
							}
							if($soporte_electronico=="SI"){
								$soporte_electronico1 = "EL";
							}else{
								$soporte_electronico1 = "";
							}
							echo "
							<tr>";
							if($j==0){
								echo"
								<td class='detalle' rowspan='$registros_query_trd_dependencia' class='center' title='Código de la Dependencia'>$codigo_dependencia</td>";
							}
							$query_cantidad_registros_serie = "select count(*) from subseries where codigo_dependencia='$codigo_dependencia' and codigo_serie='$codigo_serie' and activo='SI'";

			                $fila_cantidad  = pg_query($conectado,$query_cantidad_registros_serie); 
			                $linea_cantidad = pg_fetch_array($fila_cantidad);
			                $cantidad_total1 = $linea_cantidad[0];

							$imprimir_codigo_serie ="";
							$imprimir_nombre_serie ="";
							
							if($contador_series==1){
								$imprimir_codigo_serie = "<td class='detalle' rowspan=$cantidad_total1 class='center' title='Código de la Serie'><u>$codigo_serie</td>";
								$imprimir_nombre_serie = "<td class='detalle' rowspan=$cantidad_total1 title='Nombre de la Serie'><u>$nombre_serie</u></td>";
							}
							if($contador_series==$cantidad_total1){
								$contador_series=1;
							}else{
								$contador_series++;
							}

							echo "
								$imprimir_codigo_serie
								<td class='detalle center' title='Código de la Subserie'>$codigo_subserie</td>
								$imprimir_nombre_serie
								<td class='detalle' title='Nombre de la Subserie'>$nombre_subserie</td>
								<td class='detalle center' title='Tiempo (años) en el Archivo de Gestión'>$tiempo_archivo_gestion</td>
								<td class='detalle center' title='Tiempo (años) en el Archivo Central'>$tiempo_archivo_central</td>
								<td class='detalle center' title='Eliminación'>$eliminacion1</td>
								<td class='detalle center' title='Selección'>$seleccion1</td>
								<td class='detalle center' title='Conservación Total'>$conservacion_total1</td>
								<td class='detalle center' title='Microfilmación o Digitalización'>$microfilmacion_digitalizacion1</td>
								<td class='detalle center' title='Soporte en Formato Papel'>$soporte_papel1</td>
								<td class='detalle center' title='Soporte en Formato Electrónico'>$soporte_electronico1</td>
								<td class='detalle' title='Procedimiento'>$procedimiento</td>
							</tr>";
						}	
							
						echo "
						</table>";
					}
			    } 
			}				
		}else{
    		$query_trd_dependencia= "select * from subseries where codigo_dependencia ='$codigo_dependencia' and activo='SI' order by codigo_dependencia,nombre_serie,nombre_subserie";
		/* Aqui se ejecuta la query */
	    	$fila_query_trd_dependencia  		= pg_query($conectado,$query_trd_dependencia);
	    /* Se trae las filas de la query */
	    	$registros_query_trd_dependencia 	= pg_num_rows($fila_query_trd_dependencia);
	    /*Se trae las filas de la query*/

    		if($registros_query_trd_dependencia==0){
				echo "<h2>No hay Series ni Subseries asociadas con la dependencia ($codigo_dependencia)</h2>";
			}else{
				echo "
				<table border='0' id='ccd'>
					<tr class='center'>
						<td class='descripcion' colspan='3' width='5%'>
							Códigos
						</td>
						<td class='descripcion' colspan='2' width='50%'>
							Descripción Documental
						</td>
						<td class='descripcion' colspan='2' width='5%'>
							Retención (Años)
						</td>
						<td class='descripcion' colspan='4' width='5%'>
							Disposición final
						</td>
						<td class='descripcion' colspan='2' width='5%'>Soporte</td>
						<td class='descripcion' rowspan='2' width='30%'>Procedimiento</td>
					</tr>
					<tr class='center'>
						<td class='descripcion' title='Código de la Dependencia'>
							D
						</td>
						<td class='descripcion' title='Código de la Serie'>
							S
						</td>
						<td class='descripcion' title='Código de la Subserie'>
							Sb
						</td>
						<td class='descripcion'>Serie Documental</td>
						<td class='descripcion'>Subserie Documental </td>
						<td class='descripcion' title='Archivo de Gestión'>AG</td>
						<td class='descripcion' title='Archivo Central'>AC</td>
						<td class='descripcion' title='Eliminación'>E</td>
						<td class='descripcion' title='Selección'>SE</td>
						<td class='descripcion' title='Conservación Total'>CT</td>
						<td class='descripcion' title='Microfilmación o Digitalización'>MD</td>
						<td class='descripcion' title='Papel'>P</td>
						<td class='descripcion' title='Electrónico u Otro Soporte'>EL</td>
					</tr>";

				$contador_series = 1;

				for ($k=0; $k < $registros_query_trd_dependencia; $k++) { 
					$linea_trd_dependencia 	= pg_fetch_array($fila_query_trd_dependencia); 

					$codigo_dependencia 	= $linea_trd_dependencia['codigo_dependencia'];
					$codigo_serie 			= $linea_trd_dependencia['codigo_serie'];
					$codigo_subserie 		= $linea_trd_dependencia['codigo_subserie'];
					$nombre_serie 			= $linea_trd_dependencia['nombre_serie'];
					$nombre_subserie 		= $linea_trd_dependencia['nombre_subserie'];
					$tiempo_archivo_gestion = $linea_trd_dependencia['tiempo_archivo_gestion'];
					$tiempo_archivo_central = $linea_trd_dependencia['tiempo_archivo_central'];
					$soporte_papel 			= $linea_trd_dependencia['soporte_papel'];
					$soporte_electronico 	= $linea_trd_dependencia['soporte_electronico'];
					$eliminacion 			= $linea_trd_dependencia['eliminacion'];
					$seleccion 				= $linea_trd_dependencia['seleccion'];
					$conservacion_total 	= $linea_trd_dependencia['conservacion_total'];
					$microfilmacion_digitalizacion 	= $linea_trd_dependencia['microfilmacion_digitalizacion'];
					$procedimiento 			= $linea_trd_dependencia['procedimiento'];
					$activo 				= $linea_trd_dependencia['activo'];

					if($eliminacion=="SI"){
						$eliminacion1 = "E";
					}else{
						$eliminacion1 = "";
					}

					if($seleccion=="SI"){
						$seleccion1 = "SE";
					}else{
						$seleccion1 = "";
					}
					if($conservacion_total=="SI"){
						$conservacion_total1 = "CT";
					}else{
						$conservacion_total1 = "";
					}
					if($microfilmacion_digitalizacion=="SI"){
						$microfilmacion_digitalizacion1 = "MD";
					}else{
						$microfilmacion_digitalizacion1 = "";
					}
					if($soporte_papel=="SI"){
						$soporte_papel1 = "P";
					}else{
						$soporte_papel1 = "";
					}
					if($soporte_electronico=="SI"){
						$soporte_electronico1 = "EL";
					}else{
						$soporte_electronico1 = "";
					}
					echo "
					<tr>";
					if($k==0){
						echo"
						<td rowspan='$registros_query_trd_dependencia' class='detalle center' title='Código de la Dependencia'>$codigo_dependencia</td>";
					}
					$query_cantidad_registros_serie = "select count(*) from subseries where codigo_dependencia='$codigo_dependencia' and codigo_serie='$codigo_serie' and activo='SI'";

	                $fila_cantidad  	= pg_query($conectado,$query_cantidad_registros_serie); 
	                $linea_cantidad 	= pg_fetch_array($fila_cantidad);
	                $cantidad_total1 	= $linea_cantidad[0];

					$imprimir_codigo_serie = "";
					$imprimir_nombre_serie = "";
					if($contador_series==1){
						$imprimir_codigo_serie = "<td rowspan=$cantidad_total1 class='detalle center' title='Código de la Serie'><u>$codigo_serie</td>";
						$imprimir_nombre_serie = "<td class='detalle' rowspan=$cantidad_total1 title='Nombre de la Serie'><u>$nombre_serie</u></td>";
					}
					if($contador_series==$cantidad_total1){
						$contador_series=1;
					}else{
						$contador_series++;
					}

					echo "
						$imprimir_codigo_serie
						<td class='detalle center' title='Código de la Subserie'>$codigo_subserie</td>
						$imprimir_nombre_serie
						<td class='detalle' title='Nombre de la Subserie'>$nombre_subserie</td>
						<td class='detalle center' title='Tiempo (años) en el Archivo de Gestión'>$tiempo_archivo_gestion</td>
						<td class='detalle center' title='Tiempo (años) en el Archivo Central'>$tiempo_archivo_central</td>
						<td class='detalle center' title='Eliminación'>$eliminacion1</td>
						<td class='detalle center' title='Selección'>$seleccion1</td>
						<td class='detalle center' title='Conservación Total'>$conservacion_total1</td>
						<td class='detalle center' title='Microfilmación o Digitalización'>$microfilmacion_digitalizacion1</td>
						<td class='detalle center' title='Soporte en Formato Papel'>$soporte_papel1</td>
						<td class='detalle center' title='Soporte en Formato Electrónico'>$soporte_electronico1</td>
						<td class='detalle' title='Procedimiento'>$procedimiento</td>
					</tr>";
				}	
					
				echo "
				</table>";
			}

		}
		break;

	case 'confirma_recibido_fisico': // Recibe desde include/js/funciones_prestamos.js[function confirmar_documento_fisico_recibido(id,tipo_documento,id_documento_solicitado)]
	    $id=$_POST['id_radicado'];
	    $query_confirmar_recibido_fisico="update prestamos set confirma_recibido = 'SI' where id=$id";
	    if(pg_query($conectado,$query_confirmar_recibido_fisico)){
		    echo "confirmado"; 	
	    }else{
	    	echo "Ha ocurrido un error al confirmar recibido del físico. Por favor contacte al administrador del sistema.";
	    }
		break; 
	
	case 'crear_serie': 	// Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function enviar_agregar_serie()]
		$codigo_serie = $_POST['codigo_serie'];
		$nombre_serie = $_POST['nombre_serie'];

		$query_max_serie = "select max(id) from series";

		$fila_max_serie  	= pg_query($conectado,$query_max_serie); 
		$linea_max_serie 	= pg_fetch_array($fila_max_serie);
        $max_serie1 		= $linea_max_serie[0];

        if($max_serie1 ==""){
        	$max_serie = 1;
        }else{
		    $max_serie = $max_serie1+1;
        }
        $query_insertar_serie= "insert into series (id,codigo_serie,nombre_serie,activo)values('$max_serie','$codigo_serie','$nombre_serie','SI')";

		if(pg_query($conectado,$query_insertar_serie)){
			echo "<script> 
				auditoria_general('crear_serie','$nombre_serie');	
			</script>";
		
		}else{
			echo "<script>
				alert('No se pudo crear la serie. Comuníquese con el administrador del sistema');
				volver();
			</script>";
		}
		break;

	case 'crear_subserie': 		// Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function enviar_agregar_subserie()]
        $codigo_dependencia 			= $_POST['codigo_dependencia']; 
        $codigo_serie 					= $_POST['codigo_serie']; 
        $nombre_serie 					= $_POST['nombre_serie'];
        $codigo_subserie 				= $_POST['codigo_subserie'];
        $nombre_subserie 				= $_POST['nombre_subserie'];
        $tiempo_archivo_gestion 		= $_POST['tiempo_archivo_gestion'];
        $tiempo_archivo_central 		= $_POST['tiempo_archivo_central'];
        $soporte_papel 					= $_POST['soporte_papel'];
        $soporte_electronico 			= $_POST['soporte_electronico'];
        $eliminacion 					= $_POST['eliminacion'];
        $seleccion 						= $_POST['seleccion'];
        $conservacion_total 			= $_POST['conservacion_total'];
        $microfilmacion_digitalizacion 	= $_POST['microfilmacion_digitalizacion'];
        $procedimiento 					= $_POST['procedimiento'];

		$query_max_subserie = "select max(id) from subseries";

		$fila_max_subserie  	= pg_query($conectado,$query_max_subserie); 
		$linea_max_subserie 	= pg_fetch_array($fila_max_subserie);
        $max_subserie1 		= $linea_max_subserie[0];

        if($max_subserie1 ==""){
        	$max_subserie = 1;
        }else{
		    $max_subserie = $max_subserie1+1;
        }

        $query_insertar_subserie = "insert into subseries (id, codigo_dependencia, codigo_serie, nombre_serie, codigo_subserie, nombre_subserie, tiempo_archivo_gestion, tiempo_archivo_central, soporte_papel, soporte_electronico, eliminacion, seleccion, conservacion_total, microfilmacion_digitalizacion, procedimiento, activo) values ('$max_subserie','$codigo_dependencia','$codigo_serie','$nombre_serie','$codigo_subserie','$nombre_subserie','$tiempo_archivo_gestion','$tiempo_archivo_central','$soporte_papel','$soporte_electronico','$eliminacion','$seleccion','$conservacion_total','$microfilmacion_digitalizacion',trim('$procedimiento'),'SI')";

        if(pg_query($conectado,$query_insertar_subserie)){
			echo "<script> 
				auditoria_general('crear_subserie','$nombre_subserie');	
			</script>";
		
		}else{
			echo "<script>
				alert('No se pudo crear la subserie. Comuníquese con el administrador del sistema');
				volver();
			</script>";
		}
		break;	

	// case 'derivar_radicado':
	// 	$codigo_carpeta 			= $_POST['codigo_carpeta'];
	// 	$mensaje_derivar 			= $_POST['mensaje_derivar'];
	// 	$numero_radicado 			= $_POST['numero_radicado'];
	// 	$usuario_actual_derivado 	= $_POST['usuario_actual_derivado'];
	// 	$usuarios_para_derivar 		= $_POST['usuarios_para_derivar'];
	// 	$usuarios_visor 			= $_POST['usuarios_visor'];

	// 	$query_codigo_carpeta1 	= "select codigo_carpeta1 from radicado where numero_radicado='$numero_radicado'";

	// 	$fila_codigo_carpeta1 	= pg_query($conectado,$query_codigo_carpeta1);
 //    	$linea_codigo_carpeta1 	= pg_fetch_array($fila_codigo_carpeta1);  

 //    	$codigo_carpeta1 		= json_decode($linea_codigo_carpeta1['codigo_carpeta1'],true); // Paso de JSON a array

	// 	// Extraigo cada uno de los usuarios_para_derivar	(Sin incluir usuario_actual)
	// 	$usu  = explode(",", $usuarios_para_derivar);
	// 	$max  = sizeof($usu);
	// 	$max2 = $max-1;

	// 	// Se arma el json para codigo_carpeta1 de la tabla radicado
	// 	// $codigo_carpeta1="'{";

	// 	if($max2==0){
	// 		echo "";
	// 	}else{
	// 		for ($q=0; $q < $max2; $q++) {  // Genera restricción de usuarios que ya existen.
	// 			$login_usuario_busq = $usu[$q];
	// 			$codigo_carpeta1[$login_usuario_busq] 	= array('codigo_carpeta_personal'=>'entrada');
	// 		}	
	// 		$codigo_carpeta1 = json_encode($codigo_carpeta1); // Paso de array a JSON con el valor cambiado
	// 		// $codigo_carpeta1 = $codigo_carpeta1."}'";
	// 	}
	// 	// Fin del armado del json para codigo_carpeta1 de la tabla radicado

	// 	// Extraigo cada uno de los usuarios_para_derivar
	// 	$usu  = explode(",", $usuarios_para_derivar);
	// 	$max3  = sizeof($usu);
	// 	$max4 = $max3-1;

	// 	for ($j=0; $j < $max4 ; $j++) { 
	// 		$login_us = $usu[$j];

	// 		if (strpos($usuarios_visor, $login_us)!== false) {
	// 		}else{
	// 			$usuarios_visor.= $login_us.",";
	// 		}
	// 	}
		
	// 	$query_envia_radicado="update radicado set codigo_carpeta1='$codigo_carpeta1', leido='$usuarios_visor', usuarios_visor='$usuarios_visor', usuarios_control='$usuario_actual_derivado' where numero_radicado='$numero_radicado'";

	// 	echo "$query_envia_radicado";
	// 	// if(pg_query($conectado,$query_envia_radicado)){ 
	// 	// 	$radicado 				= $numero_radicado;
	// 	// 	$transaccion_historico 	= "Se Deriva Radicado";	// Variable para tabla historico_eventos
	// 	// 	$comentario 			= "Se ha derivado el radicado a los usuarios $usuarios_para_derivar || $mensaje_derivar";		// Variable para historico eventos

	// 	// 	$transaccion 			= "derivar_radicado"; 	// Variable para auditoria
	// 	// 	$creado 				= "$numero_radicado";	// Variable para auditoria
	// 	// 	require_once("../login/inserta_historico.php");		
	// 	// }else{
	// 	// 	echo "<script> alert('Ocurrió un error al realizar el derivado del radicado, por favor revisar e intentar nuevamente.')</script>";
	// 	// }	

	// 	break;

	case 'documento_no_requiere_respuesta':
	/*****************************************************************************************
	Inicio funcion para NRR (No requiere respuesta) 
	/*****************************************************************************************
	* @brief Recibe desde bandejas/entrada/visualiza_radicado.php[valida_envio_nrr()]
	* para guardar en la base de datos cuando un documento no requiere respuesta.
	* @param {string} ($serie_select) Si viene vacío es ignorado, si viene codigo de serie y existe en la dependencia es el <option selected>, si viene codigo de serie pero no existe en la dependencia, agrega el <option> con codigo-nombre de serie y queda como <option selected>
	* @param {string} ($codigo_dependencia) Se usa para filtrar la consulta del listado de series. Si viene vacío la consulta va a retornar el listado completo de series activas. 
	(<select id='codigo_subserie' title='Seleccione el código de la serie documental' class='select_opciones' onchange='validar_serie_subserie()'><option value=''>No hay subseries asociadas a la serie seleccionada</option></select>)
	* @return {string} String con los <option> del listado de series documentales según los parametros recibidos.
	*/	
		session_start();
		$usuario 			= $_SESSION['login']; 

        $carpeta_personal 	= $_POST['carpeta_personal'];
        $expediente 		= $_POST['expediente'];
        $observaciones 		= $_POST['observaciones'];
        $radicado 			= $_POST['radicado'];
        $serie 				= $_POST['serie'];
        $subserie 			= $_POST['subserie'];

        echo "recibo $carpeta_personal, $expediente, $serie, $subserie, $observaciones - $radicado";

        /* Se actualiza codigo_carpeta1 con el usuario que hace la transaccion */
        $query_codigo_carpeta1 	= "select codigo_carpeta1 from radicado where numero_radicado='$radicado'";
		$fila_codigo_carpeta1 	= pg_query($conectado,$query_codigo_carpeta1);
    	$linea_codigo_carpeta1 	= pg_fetch_array($fila_codigo_carpeta1);  
    	$codigo_carpeta1 		= json_decode($linea_codigo_carpeta1['codigo_carpeta1'],true); // Paso de JSON a array
		$codigo_carpeta1[$usuario]['codigo_carpeta_personal'] = $carpeta_personal; // Reemplazo en array la carpeta personal
		$nuevo_codigo_carpeta1 = json_encode($codigo_carpeta1); // Paso de array a JSON con el valor cambiado

        $query_nrr = "update radicado set codigo_carpeta1='$nuevo_codigo_carpeta1', id_expediente='$expediente', codigo_serie='$serie', codigo_subserie='$subserie', estado_radicado='no_requiere_respuesta' where numero_radicado = '$radicado';";

        if(pg_query($conectado,$query_nrr)){
        /* Desde aqui se genera historico */	
			$transaccion_historico 	= "Se marca documento como NRR (No requiere respuesta)";	// Variable para tabla historico_eventos
			$comentario				= "$observaciones";					// Variable para historico eventos

			$transaccion 			= "documento_no_requiere_respuesta"; 	// Variable para auditoria
			$creado 				= "$radicado";			// Variable para auditoria
			require_once("../login/inserta_historico.php");		
		/* Hasta aqui se genera historico */
		}
		break;
	/* Fin funcion para NRR (No requiere respuesta) */
	/*****************************************************************************************/   
	
	/*****************************************************************************************
	Inicio Case eliminar_borrador vaciá la carpeta contenedora de los borradores
	/*****************************************************************************************
	* @brief Recibe desde 
	* - include/js/funciones_verficar_radicado_sin_terminar.js
	* - include/js/funciones_menu.js
	*****************************************************************************************/
	case 'eliminar_borrador':
		if(!isset($_SESSION)){
			session_start();
  		}
		$archivos_del_directorio = glob('../bodega_pdf/plantilla_generada_tmp/'.$_SESSION['login'].'/*');// Obtenemos todos los ficheros
		foreach($archivos_del_directorio as $registro_archivo){
		    unlink($registro_archivo);// Elimina el fichero
		}
		break;
	/*****************************************************************************************
	Fin Case eliminar_borrador vaciá la carpeta contenedora de los borradores
	/*****************************************************************************************/
	
	/*****************************************************************************************
	* @brief Recibe desde radicacion/radicacion_salida2/index_radicacion_salida.php
	** [visorPdf(), setTimeout(function)] y  radicacion/radicacion_salida2/docx_a_pdf.php
	** [visorPdf(), setTimeout(function)]

	* @description Borra el archivo temporal ubicado en la ruta COMPLETA recibida en $_POST
	** ("../bodega_pdf/plantilla_generada_tmp/45136.pdf")

	* @return {string} Ejecuta el borrado de archivo temporal generado en radicacion de salida 
	*****************************************************************************************/	
	case 'eliminar_temporal':
		$nombre_archivo 	= $_POST['nombre_archivo'];
		if(file_exists($nombre_archivo)) {
			unlink($nombre_archivo);
			echo "Se elimina $nombre_archivo";
		}	
		break;

	/*****************************************************************************************
	Inicio Case encabezado_piecero_radicacion_interna valida y retorna las imagenes de encabezado y piecero del pdf
	/*****************************************************************************************
	* @brief Recibe desde funciones_radicacion_interna.js/carga_radicacion_interna()
	*****************************************************************************************/
	case 'encabezado_piecero_radicacion_interna':
		if(!isset($_SESSION)){
			session_start();
  		}
		/* Se obtiene la cabecera y piecera del pdf */
		$codigo_entidad = $_SESSION['codigo_entidad'];
		switch ($codigo_entidad) {
	 		case 'AV1':
	 		case 'EJEC':
				$path_encabezado 	= '../imagenes/logos_entidades/encabezado_rad_av1.png';
				$path_piedepagina 	= '../imagenes/logos_entidades/pie_rad_av1.png';
	 			break;
	 		default:
				$path_encabezado 	= '../imagenes/encabezado_radicado.png';
				$path_piedepagina 	= '../imagenes/pie_de_pagina_radicado.png';
	 			break;
	 	}
		$base64_encabezado 		= base64_encode(file_get_contents($path_encabezado));// El fichero se pasa a cadena y codifica base64
		$base64_piedepagina 	= base64_encode(file_get_contents($path_piedepagina));// El fichero se pasa a cadena y codifica base64
		/* Fin se obtiene la cabecera y piecera del pdf */
		echo "<img class='imagen_cabecera' src='data:image/png;base64,".$base64_encabezado."' style='width: 100%;'>
	    		<div style='margin-left: 60%; margin-top: -70px; position: absolute;'>
		            <h6>".$_SESSION['nombre_dependencia']."</h6>
		        </div>
		        SEPARADOR
		        data:image/png;base64,".$base64_encabezado."
		        SEPARADOR
		        <img class='imagen_cabecera' src='data:image/png;base64,".$base64_piedepagina."' style='width: 100%; margin-top: 43px;'>
		        SEPARADOR
		        data:image/png;base64,".$base64_piedepagina."
		        ";
		break;

	/*****************************************************************************************
	Inicio funcion para agregar cambio organico-funcional de la empresa/entidad 
	/*****************************************************************************************
	* @brief Recibe desde normatividad/index_normatividad.php[submit_agregar_cambio_of()]
	** para ingresar en la base de datos en la tabla "cambios_organico_funcionales" con el fin de armar
	** las versiones de Organigrama, CCD y TRD para cumplir el MOREQ 

	* @param {string} (fecha_inicial) Valor de la fecha inicial de esta versión del Organigrama. Es un campo
	** obligatorio
	* @param {string} (fecha_final) Valor de la fecha final de esta versión del Organigrama. No es obilgatorio y
	** si viene vacío se entiende que es la versión más reciente o es la versión actual.
	* @param {string} (id_cambio) Valor del identificador de esta versión del Organigrama. Es un campo obligatorio
	* @param {$_FILES} (pdf_principal) Archivo adjunto que contiene el acto administrativo en el cual define el 
	** cambio organico-funcional de esta versión del Organigrama. No es obligatorio.
	*/		
	case 'enviar_agregar_cambio_of':
		$fecha_inicial 	= $_POST['fecha_inicial'];
		$fecha_final  	= $_POST['fecha_final'];
		$id_cambio  	= $_POST['id_cambio'];

		if(!empty($_FILES["pdf_principal"]["name"])){// Se valida si viene un archivo PDF
 			$target_file1 	= basename($_FILES["pdf_principal"]["name"]);// Tomamos solo el nombre del archivo
 			$target_file 	= substr(preg_replace('([^A-Za-z0-9])', '', $target_file1),0,-3).".pdf"; // Se eliminan espacios y caracteres especiales
 			$target_dir  	= "../normatividad/pdf/";// Se define el directorio donde sera guardado

 			if(move_uploaded_file($_FILES["pdf_principal"]["tmp_name"],$target_dir.$target_file)){// Se guarda en el directorio el archivo
				$path_acto_administrativo = "normatividad/pdf/$target_file";// Se define donde se guardo y se complementa con el nombre del archivo para ser guardado en la base de datos

				$query_usuario = "insert into cambios_organico_funcionales (id_cambio_organico_funcional, fecha_inicial_cambio, fecha_final_cambio, path_acto_administrativo)values('$id_cambio','$fecha_inicial','$fecha_final','$path_acto_administrativo'); ";// Consulta sql
			}else{
				echo "<script>Hubo un error al intentar mover el PDF a la normatividad interna. Favor revisar.</script>";
				exit();
			}
		}else{
			$query_usuario = "insert into cambios_organico_funcionales (id_cambio_organico_funcional, fecha_inicial_cambio, fecha_final_cambio, path_acto_administrativo)values('$id_cambio','$fecha_inicial','$fecha_final',''); ";// Consulta sql
 		}

 		if(pg_query($conectado,$query_usuario)){
			/* Desde aqui se genera historico (auditoria) */	
			echo "<script> 
				auditoria_general('ingresa_cambio_organico_funcional','$id_cambio');	
			</script>";		
		}else{
			echo "<script>alert('Ha ocurrido un error al insertar en la tabla de version de organigrama. Comuníquese con el administrador del sistema')</script>";
		}
		break;

	/*****************************************************************************************
	Inicio funcion para modificar cambio organico-funcional de la empresa/entidad 
	/*****************************************************************************************
	* @brief Recibe desde normatividad/index_normatividad.php[submit_modificar_cambio_of()]
	** para modificar en la base de datos en la tabla "cambios_organico_funcionales" con el fin de armar
	** las versiones de Organigrama, CCD y TRD para cumplir el MOREQ 

	* @param {string} (fecha_inicial) Valor de la fecha inicial de esta versión del Organigrama. Es un campo
	** obligatorio
	* @param {string} (fecha_final) Valor de la fecha final de esta versión del Organigrama. No es obilgatorio y
	** si viene vacío se entiende que es la versión más reciente o es la versión actual.
	* @param {string} (id_cambio) Valor del identificador de esta versión del Organigrama. Es un campo obligatorio
	* @param {$_FILES} (pdf_principal) Archivo adjunto que contiene el acto administrativo en el cual define el 
	** cambio organico-funcional de esta versión del Organigrama. No es obligatorio.
	*/		
	case 'enviar_modificar_cambio_of':
		$fecha_inicial 	= $_POST['fecha_inicial'];
		$fecha_final  	= $_POST['fecha_final'];
		$id_cambio  	= $_POST['id_cambio'];

		if(!empty($_FILES["pdf_principal"]["name"])){// Se valida si viene un archivo PDF
 			$target_file1 	= basename($_FILES["pdf_principal"]["name"]);// Tomamos solo el nombre del archvio
 			$target_file 	= substr(preg_replace('([^A-Za-z0-9])', '', $target_file1),0,-3).".pdf"; // Se eliminan espacios y caracteres especiales
 			$target_dir  	= "../normatividad/pdf/";// Se define el directorio donde sera guardado

 			if(move_uploaded_file($_FILES["pdf_principal"]["tmp_name"],$target_dir.$target_file)){// Se guarda en el directorio el archivo
				$path_acto_administrativo = "normatividad/pdf/$target_file";// Se define donde se guardo y se complementa con el nombre del archivo para ser guardado en la base de datos
				$query_usuario = "update cambios_organico_funcionales set fecha_inicial_cambio='$fecha_inicial', fecha_final_cambio='$fecha_final', path_acto_administrativo='$path_acto_administrativo' where id_cambio_organico_funcional='$id_cambio';";// Consulta sql
			}else{
				echo "<script>Hubo un error al intentar mover el PDF de la modificación a la normatividad interna. Favor revisar.</script>";
				exit();
			}
		}else{
			$query_usuario = "update cambios_organico_funcionales set fecha_inicial_cambio='$fecha_inicial', fecha_final_cambio='$fecha_final' where id_cambio_organico_funcional='$id_cambio'; ";// Consulta sql
 		}

 		if(pg_query($conectado,$query_usuario)){
			/* Desde aqui se genera historico (auditoria) */	
			echo "<script> 
				auditoria_general('modifica_cambio_organico_funcional','$id_cambio');	
			</script>";		
		}else{
			echo "<script>alert('Ha ocurrido un error al modificar la tabla de version de organigrama. Comuníquese con el administrador del sistema')</script>";
		}
		break;

	/* Recibe acción del "botón naranja" para asociar PDF Firmado */
	case 'enviar_subir_pdf': // Recibe desde bandejas/entrada/pestanas.php[function enviar_subir_pdf()]
		require_once('../login/validar_inactividad.php');// Se valida la inactividad 
		$usuario_codigo_carpeta1 = $_SESSION['login']; 

        $carpeta_personal 		= $_POST['carpeta_personal'];
        $comentario 			= $_POST['comentario'];
        $nuevo_nombre_documento = $_POST['nuevo_nombre_documento'];
        $radicado 				= $_POST['radicado'];
        $version_siguiente 		= $_POST['version_siguiente'];

        $path_radicado 	= "$year/$mes/".$radicado."_".$nuevo_nombre_documento."_".$version_siguiente.".pdf"; 
        $version_anterior = $version_siguiente-1;

        /* Se hace el cambio de carpeta1 del usuario que está haciendo la transaccion */
        $query_codigo_carpeta1 	= "select r.codigo_carpeta1, r.medio_respuesta_solicitado, d.mail from radicado r join datos_origen_radicado d on r.numero_radicado=d.numero_radicado where r.numero_radicado='$radicado'";
		$fila_codigo_carpeta1 	= pg_query($conectado,$query_codigo_carpeta1);
    	$linea_codigo_carpeta1 	= pg_fetch_array($fila_codigo_carpeta1);  
    	$medio_resp_solicitado 	= $linea_codigo_carpeta1['medio_respuesta_solicitado'];
    	$mail_usuario 			= $linea_codigo_carpeta1['mail'];

    	$codigo_carpeta1 		= json_decode($linea_codigo_carpeta1['codigo_carpeta1'],true); // Paso de JSON a array
		$codigo_carpeta1[$usuario_codigo_carpeta1]['codigo_carpeta_personal'] = $carpeta_personal; // Reemplazo en array la carpeta personal
		$nuevo_codigo_carpeta1 = json_encode($codigo_carpeta1); // Paso de array a JSON con el valor cambiado

		/* Se consulta la informacion de la ultima version del documento */
		$query_version_documento	= "select * from version_documentos where numero_radicado='$radicado' order by version desc";
		$fila_version_documento		= pg_query($conectado,$query_version_documento);
		$linea_version_documento  	= pg_fetch_array($fila_version_documento);
		$aprobado 					= $linea_version_documento['aprobado'];
		$aprueba_doc 				= $linea_version_documento['usuario_que_aprueba'];
		$cargo_aprueba_doc 			= $linea_version_documento['cargo_usuario_que_aprueba'];
		$cargo_elabora_doc 			= $linea_version_documento['cargo_usuario_que_elabora'];
		$cargo_firmante_doc 		= $linea_version_documento['cargo_usuario_que_firma'];
		$elabora_doc 				= $linea_version_documento['usuario_que_elabora'];
		$firmante_doc 				= $linea_version_documento['usuario_que_firma'];
		$html_asunto 				= $linea_version_documento['html_asunto'];
		$tamano 					= $linea_version_documento['tamano'];
		$tipo_solicitud 			= $linea_version_documento['medio_solicitud_firmas'];

		/* Se consulta si tiene radicado padre para actualizar la informacion también al 
		radicado padre */
		$query_consulta_rad_padre 	= "select * from respuesta_radicados res join radicado r on res.radicado_padre=r.numero_radicado where res.radicado_respuesta ='$radicado'";
		$fila_codigo_carpeta1_rad_padre 	 = pg_query($conectado,$query_consulta_rad_padre);
    	$registros_codigo_carpeta1_rad_padre = pg_num_rows($fila_codigo_carpeta1_rad_padre);
    			
		if($registros_codigo_carpeta1_rad_padre!=0){
	    	$linea_codigo_carpeta1_rad_padre 	= pg_fetch_array($fila_codigo_carpeta1_rad_padre);
	    	$numero_radicado_padre 		 		= $linea_codigo_carpeta1_rad_padre['numero_radicado'];  
	    	$codigo_carpeta1_rad_padre 	 		= json_decode($linea_codigo_carpeta1_rad_padre['codigo_carpeta1'],true); // Paso de JSON a array
			$codigo_carpeta1_rad_padre[$usuario_codigo_carpeta1]['codigo_carpeta_personal'] = $carpeta_personal; // Reemplazo en array la carpeta personal
			$nuevo_codigo_carpeta_rad_padre  	= json_encode($codigo_carpeta1_rad_padre); // Paso de array a JSON con el valor cambiado

			$query_update_radicado_padre 		= "update radicado set codigo_carpeta1 = '$nuevo_codigo_carpeta_rad_padre', estado_radicado='tramitado' where numero_radicado='$numero_radicado_padre';";
		}else{
			$query_update_radicado_padre 		= "";
		}

		/* Genera la query para insertar en version_documentos */
		$query_insert_version 		= "insert into version_documentos(numero_radicado, version, usuario_modifica, fecha_modifica, path_pdf, html_asunto, usuario_que_firma, cargo_usuario_que_firma, firmado, usuario_que_aprueba, cargo_usuario_que_aprueba, aprobado, usuario_que_elabora, cargo_usuario_que_elabora, usuarios_revisa_aprueba, tamano, medio_solicitud_firmas)values('$radicado', '$version_siguiente', '$usuario_codigo_carpeta1', '$timestamp', '$path_radicado', '$html_asunto', '$firmante_doc', '$cargo_firmante_doc', 'SI', '$aprueba_doc', '$cargo_aprueba_doc', '$aprobado', '$elabora_doc', '$cargo_elabora_doc', '', '$tamano', '$tipo_solicitud');";

		/* Query para actualizar la version. Se pone html_asunto en vacío para reducir el espacio en base de datos ya que es un campo grande. */	
		$query_insert_version.="update version_documentos set html_asunto='', aprobado='$aprobado' where numero_radicado='$radicado' and version!='$version_siguiente';";

        /* Define las variables para mover el archivo recibido */
		$target_file 	= basename($_FILES["pdf_principal"]["name"]); // Nombre que trae el archivo
        $target_dir 	= "../bodega_pdf/radicados/";

        /* Primero muevo el PDF principal a la ruta (../bodega_pdf/tmp/$archivo) para 
        transformarlo en PDF version 1.4 para poder luego sacar el numero de folios y moverlo
        a la ruta correspondiente */
		if(move_uploaded_file($_FILES["pdf_principal"]["tmp_name"],"../bodega_pdf/tmp".$radicado."_".$nuevo_nombre_documento."_".$version_siguiente.".pdf")){

			/* Para cargar un PDF en version superior (superior a 1.4) se transforma a 1.4 y se guarda en la carpeta /bodega_pdf/radicados/$year/mes/$aleatorio.pdf */
			echo shell_exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH  -sOutputFile=".$target_dir.$path_radicado." ../bodega_pdf/tmp".$radicado."_".$nuevo_nombre_documento."_".$version_siguiente.".pdf");

			/* Calcula la cantidad de folios del archivo a cargar */
			$stream 	= fopen($target_dir.$path_radicado, "r");
			$content 	= fread ($stream, filesize($target_dir.$path_radicado));
	 
			if(!$stream || !$content)
				return 0;
		 
			$count = 0;
		 
			$regex  = "/\/Count\s+(\d+)/";
			$regex2 = "/\/Page\W*(\d+)/";
			$regex3 = "/\/N\s+(\d+)/";
		 
			if(preg_match_all($regex, $content, $matches))
				$count = max($matches);

			$cantidad_folios =  $count[0];

	        $query_update_radicado = "$query_update_radicado_padre update radicado set path_radicado='$path_radicado', codigo_carpeta1 = '$nuevo_codigo_carpeta1', estado_radicado='tramitado', folios='$cantidad_folios' where numero_radicado='$radicado';";

			if(pg_query($conectado,$query_update_radicado)){
				if(pg_query($conectado,$query_insert_version)){
					if($medio_resp_solicitado=="correo_electronico"){
						// echo "jjjjjjjjjjjjjjj";	
					}
					/* Desde aqui se genera historico */	
					$transaccion_historico 	= "Sube PDF principal con firmas";	// Variable para tabla historico_eventos
					$comentario				= "Se sube PDF del radicado $radicado principal con firmas correspondientes desde la plantilla generada. $comentario";	// Variable para historico eventos

					$transaccion 			= "subir_pdf_principal"; 	// Variable para auditoria
					$creado 				= "$radicado";			// Variable para auditoria
					require_once("../login/inserta_historico.php");		
					/* Hasta aqui se genera historico */	
				}else{
					echo "<script>alert('Ha ocurrido un error al insertar en la tabla de version de documentos. Comuníquese con el administrador del sistema')</script>";
				}
			}else{
				echo "<script>alert('Ha ocurrido un error al insertar en la tabla de radicado. Comuníquese con el administrador del sistema')</script>";
			}
		}else{
			echo "<script> alert('Ocurrió un error al subir la imagen principal al radicado, por favor revisar e intentar nuevamente.')</script>";
		}
		break;


	/*****************************************************************************************
		Case generar_vista_previa_pdf_radicacion_interna realiza un pdf con la vista previa de la radicacion interna, lo guarda en la carpeta de plantilla_generada_tmp y su usario correspondiente
	/*****************************************************************************************
		* @brief Recibe desde funciones_radicacion_interna.js/[function visorHtml()]
		* @param {string} (ubicacion) Es importante, sera la ubicacion de donde se genera el radicado
		* @param {string} (ubicacion2) Es importante, sera la ubicacion del destinatario del radicado
		* @param {string} (anexos) No es importante, sera un texto de los documentos anexos que tenga el radicado, si viene vacio se da un valor pro defecto
		* @param {string} (src_imagen_cabecera) 
		* @param {string} (fecha) 
		* @param {string} (tratamiento) 
		* @param {string} (id_destinatarios)
		* @param {string} (destinatarios) 
		* @param {string} (asunto) 
		* @param {string} (editor2) 
		* @param {string} (despedida) 
		* @param {string} (firmante) 
		* @param {string} (cargo_firmante) 
		* @param {string} (aprueba) 
		* @param {string} (cargo_aprueba) 
		* @param {string} (elabora) 
		* @param {string} (cargo_elabora) 
		* @param {string} (src_imagen_piecera)
		* @param {string} (codido_serie)
		* @param {string} (codigo_subserie)
		* @return {string} Retorna el usuario actual para acceder al pdf previo
	*****************************************************************************************/	
	// case 'generar_vista_previa_pdf_radicacion_interna':
	//     if(!isset($_SESSION)){// Validar que la $_SESSION no este creada 
	//         session_start();
	//     }
	//     echo $_SESSION['login']."SEPARADORAJAX";// Retorna el usuario actual
	//     // Tratamiento de variables //
	// 	$ubicacion 			= $_POST['ubicacion'];
	//     $ubicacion 			= explode("-", $ubicacion);
	//     $ubicacion 			= $ubicacion[0]."  -  ".$ubicacion[1];
	//     $ubicacion2  		= $_POST['ubicacion2'];
	//     $ubicacion2  		= explode("-", $ubicacion2);
	//     $ubicacion2  		= $ubicacion2[0]."  -  ".$ubicacion2[1];
	// 	$anexos 			= $_POST['anexos'];	
	// 	if($_POST['anexos'] == "")$anexos = "Sin anexos";
	// 	$version 			= $_POST['version'] + 1;
	// 	$ruta 				= "../bodega_pdf/plantilla_generada_tmp/".$_SESSION['login']."/vista_previa_radicado_interno.pdf";
	// 	$ruta2 				= "../bodega_pdf/plantilla_generada_tmp/".$_SESSION['login'];
	// 	$firma_virtual		= "";
 //        // Fin tratamiento de variables //
	// 	if($_POST['tipo'] == 2){
	// 		$tipo_radicado 	= 4;// Tipo de radicado (3- Interna, 2- Salida, 1- Entrada, etc)
	// 		// $year 			= date("Y");// Se obtiene el año en formato 4 digitos 
	// 		require_once('../login/validar_consecutivo.php');
	// 		echo "SEPARADORAJAX".$radicado."SEPARADORAJAX";
	// 		// Generar codigo qr //
	// 			$logo           = '../imagenes/logo3.png';//logo de la entidad dentro del QR
	// 			$codigo_entidad = $_SESSION["codigo_entidad"];
	// 			switch ($codigo_entidad) {
	// 			    case 'AV1':
	// 			        $imagen_entidad = "<img src='../imagenes/logos_entidades/logo_largo_av1.png' style='width:180px; height:100px;'>";
	// 			        break;
	// 			    case 'EJC':
	// 			    case 'EJEC':
	// 			        $imagen_entidad = "<img src='../imagenes/logos_entidades/logo_largo_ejc.png' style='width:180px; height:100px;'>";
	// 			        $logo           = '../imagenes/logos_entidades/imagen_qr_ejc.png'; //logo de la entidad dentro del QR
	// 			        break;
	// 			    case 'L01':
	// 			        $imagen_entidad = "<img src='../imagenes/logos_entidades/logo_largo_l01.png' style='width:180px; height:100px;'>";
	// 			        break;
	// 			    default:
	// 			        $imagen_entidad = "<img src='../imagenes/iconos/logo_largo.png' style='width:180px; height:100px;'>";
	// 			        break;
	// 			}
	// 			/* agregar el script con la librería para generar el QR */
	// 			require ('phpqrcode/qrlib.php');
	// 			/* Se crea el enlace hacia la capeta temporal con el nombre del usuario para guardar los codigos QR generados (Ej. qr_ALUMNO2.png) */
	// 			$filename   = "../bodega_pdf/qr_usuario/qr_".$_SESSION['login']."png";
	// 			/* En esta variable se genera el QR e indica cada uno de los datos que se envían a la direccion https://xxxxxx y las variables que se envían por GET */
	// 			$cod        = "https://www.gammacorp.co/consultaweb.php?numero_radicado=$radicado%26codigo_entidad=".$_SESSION['codigo_entidad']."%26canal_respuesta=mail&amp";
	// 			$tam        = "8"; //tamaño de la imagen qr
	// 			$niv        = "H"; //nivel de seguridad o complejidad del QR del 1 al 5 o "H" (Higher) para el máximo 
	// 			$marco      = 0;  // Marco del QR es tranparente.
	// 			/* clase Qrcode:: funcion png para generar el QR en una imagen png */
	// 			QRcode::png($cod,$filename , $niv, $tam, $marco);
	// 			$QR = $filename;// Archivo original generado con codigo QR
	// 			/* Si existe el logo para crear en el centro del QR*/
	// 			if(file_exists($logo)){
	// 			    $QR             = imagecreatefromstring(file_get_contents($QR));// Imagen destino como recurso de conexion
	// 			    $logo           = imagecreatefromstring(file_get_contents($logo));// Recurso de la fuente de la imagen.
	// 			    $QR_width       = imagesx($QR);// Ancho de la imagen QR original
	// 			    $QR_height      = imagesy($QR);// Alto de la imagen QR original
	// 			    $logo_width     = imagesx($logo);// Ancho del logo 
	// 			    $logo_height    = imagesy($logo);// Alto del logo
	// 			    $logo_qr_width  = $QR_width/3;// Ancho del logo despues de la combinacion  (1 / 5 del codigo QR)
	// 			    $scale          = $logo_width/$logo_qr_width;// Ancho escalado del logo (Ancho propio / Ancho combinado)
	// 			    $logo_qr_height = $logo_height/$scale;// Alto del logo despues de combinacion
	// 			    $from_width     = ($QR_width - $logo_qr_width) / 2;// Punto de coordenada desde la esquina izquierda superior del logo despues de combinacion 
	// 			    /* Recombinar y redimensionar imagenes*/
	// 			    /* imagecopyresampled()  Copia el cuadro de una area desde una imagen (imagen origen) a otra.*/
	// 			    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
	// 			}
	// 			/* Salida de imagenes */
	// 			imagepng($QR, $filename);
	// 			imagedestroy($QR);
	// 			/* Etiqueta img para mostrar el QR */
	// 			$imagenqr = "<img src='".$filename."' width='150' height='150'>
	// 							<br>
	// 							".$radicado."";
	// 		// Fin generar codigo qr //
	// 		$transaccion_historico 		= "Genera plantilla de Radicadacion Interna";
	// 		$transaccion 				= "plantilla_interna";// Variable para auditoria
	// 		$comentario	 				= "Se ha generado la <b>versión ".$version."</b> del radicado interno numero ".$radicado;
	// 		$path_radicado 				= $radicado."_".$_POST['numero_aleatorio']."_".$version.".pdf";
	// 		$firmado              		= 'NO';
	// 		if($_POST['firmante'] 		== $_SESSION['nombre'])$firmado = 'SI';
	// 	/*	if($firmado == "SI"){
	// 			$path_firma1 	= $_SESSION['path_firma'];
	// 			if($path_firma1 != ""){
	// 				$path_firma		= "../imagenes/fotos_usuarios/$path_firma1";
	// 				$type_firma		= pathinfo($path_firma1, PATHINFO_EXTENSION);
	// 				$data_firma 	= file_get_contents($path_firma);
	// 				$base64_firma 	= 'data:image/' . $type_firma . ';base64,' . base64_encode($data_firma);
	// 			}
	// 			$firma_virtual		=  '<img src="$base64_firma" width="500" height="600">';
	// 		}*/
	// 		$aprobado 					= 'NO';
	// 		if($_POST['aprueba'] 		== $_SESSION['nombre'])$aprobado = 'SI';
	// 		$usuario_visor_and_control 	= $_SESSION['login'];
	// 		$destinatarios_2 			= "";
	// 		$id_destinatarios 			= $_POST['id_destinatarios'];
	// 		$id_destinatarios_array 	= explode(",", $id_destinatarios);
	// 		foreach($id_destinatarios_array as $key => $value){
	// 			$query_destinatario 	= "select 
	// 											login
	// 										from
	// 											usuarios
	// 										where
	// 											id_usuario = '$value'";
	// 			$fila_destinatario  	= pg_query($conectado,$query_destinatario);
	// 			$linea_destinatario  	= pg_fetch_array($fila_destinatario);
	// 			if($linea_destinatario['login'] != ""){
	// 				$login_destinatario = $linea_destinatario['login'];
	// 				$destinatarios_2   .= ','.$login_destinatario;
	// 			}
	// 		}
	// 		$login_usuario_actual = $_SESSION['login'];
	// 		$usuario_visor_and_control  .= $destinatarios_2;
	// 		$firmante_login 			 = $_POST['firmante_login'];
	// 		$firmante_login 			 = str_replace($login_usuario_actual, "", $firmante_login);
	// 		$aprueba_login 				 = $_POST['aprueba_login'];
	// 		$aprueba_login 				 = str_replace($login_usuario_actual, "", $aprueba_login);
	// 		$elabora_login 				 = $_POST['elabora_login'];
	// 		$elabora_login 				 = str_replace($login_usuario_actual, "", $elabora_login);
	// 		$usuario_visor_and_control 	.= ','.$firmante_login.','.$aprueba_login.','.$elabora_login;
	// 		$codigo_carpeta1 			 = "'{";
	// 		$codigo_carpeta1_array 		 = explode(",", $usuario_visor_and_control);
	// 		$i 						     = 0;
	// 		foreach($codigo_carpeta1_array as $key => $value){
	// 			if($i != 0)$codigo_carpeta1 .= ",";
	// 			$i++;
	// 			$codigo_carpeta1 .= "\"".$value."\":{\"codigo_carpeta_personal\":\"entrada\"}";
	// 		}
	// 		$codigo_carpeta1     .= "}'";
	// 		$ruta 		  = "../bodega_pdf/radicados/".$path_radicado.".pdf";
	// 		$ruta2 		  = "../bodega_pdf/radicados/";
	// 		if($version == 1){
	// 			$query_rad_exp = "insert into 
	// 								radicado(
	// 									numero_radicado,
	// 									fecha_radicado,
	// 									codigo_carpeta1,
	// 									numero_guia_oficio,
	// 									descripcion_anexos,
	// 									path_radicado,
	// 									dependencia_actual,
	// 									usuarios_visor,
	// 									usuarios_control,
	// 									dependencia_radicador,
	// 									usuario_radicador,
	// 									asunto,
	// 									nivel_seguridad,
	// 									leido,
	// 									clasificacion_radicado,
	// 									termino,
	// 									codigo_serie,
	// 									codigo_subserie,
	// 									estado_radicado
	// 								)
	// 								values(
	// 									'".$radicado."',
	// 									'".$timestamp."',
	// 									".$codigo_carpeta1.",
	// 									'No se ha enviado al destinatario todavía, por lo tanto no hay soporte de envío.',
	// 									'".$anexos."',
	// 									'".$path_radicado."',
	// 									'".$_SESSION['dependencia']."',
	// 									'".$usuario_visor_and_control."',
	// 									'".$usuario_visor_and_control."',
	// 									'".$_SESSION['dependencia']."',
	// 									'".$_SESSION['login']."',
	// 									'".$_POST['asunto']."',
	// 									'".$_SESSION['nivel']."',
	// 									'".$_SESSION['login']."',
	// 									'OFICIO',
	// 									'15',
	// 									'".$_POST['codigo_serie']."',
	// 									'".$_POST['codigo_subserie']."',
	// 									'en_tramite'
	// 								);
	// 								insert into 
	// 								datos_origen_radicado(
	// 									codigo_datos_origen_radicado,
	// 									numero_radicado,
	// 									nombre_remitente_destinatario,
	// 									ubicacion
	// 								)
	// 								values(
	// 									'2',
	// 									'".$radicado."',
	// 									'".$_POST['destinatarios_final_2']."',
	// 									'".$ubicacion2."'
	// 								);
	// 								insert into 
	// 								version_documentos(
	// 									numero_radicado,
	// 									version,
	// 									usuario_modifica,
	// 									fecha_modifica,
	// 									path_pdf,
	// 									html_asunto,
	// 									despedida,
	// 									tratamiento,
	// 									usuario_que_firma,
	// 									cargo_usuario_que_firma,
	// 									firmado,
	// 									usuario_que_aprueba,
	// 									cargo_usuario_que_aprueba,
	// 									aprobado,
	// 									usuario_que_elabora,
	// 									cargo_usuario_que_elabora,
	// 									cargo_destinatario,
	// 									ubicacion_remitente
	// 								)
	// 								values(
	// 									'$radicado',
	// 									'".$version."',
	// 									'".$_SESSION['login']."',
	// 									'".$timestamp."',
	// 									'".$path_radicado."',
	// 									'".$_POST['editor2']."',
	// 									'".$_POST['despedida']."',
	// 									'".$_POST['tratamiento']."',
	// 									'".$_POST['firmante']."',
	// 									'".$_POST['cargo_firmante']."',
	// 									'".$firmado."',
	// 									'".$_POST['aprueba']."',
	// 									'".$_POST['cargo_aprueba']."',
	// 									'".$aprobado."',
	// 									'".$_POST['elabora']."',
	// 									'".$_POST['cargo_elabora']."',
	// 									'".$_POST['cargo_destinatario_final_2']."',
	// 									'".$ubicacion2."'
	// 								);";				
	// 			if($_POST['id_expediente'] != ""){
	// 				$radicado_existente = 0;
	// 				$query_exp 		= "select 
	// 										lista_radicados,
	// 										nombre_expediente
	// 									from 
	// 										expedientes
	// 									where 
	// 										id_expediente = '$id_expediente'";
	// 				$fila_exp 	  		= pg_query($conectado,$query_exp);
	// 				$linea_exp    		= pg_fetch_array($fila_exp);
	// 				$lista_radicados 	= $linea_exp['lista_radicados']; // Listado de radicados en tabla expediente
	// 				$lista_radicados_array = explode(",", $lista_radicados);
	// 				foreach ($lista_radicados_array as $key => $value) {
	// 					if($radicado == $value)$radicado_existente++;
	// 				}
	// 				if($radicado_existente == 0){
	// 					if(count($lista_radicados_array) > 0 && $lista_radicados != "")$lista_radicados .= ",";
	// 					$lista_radicados .= $radicado;
	// 					$nombre_expediente 	= $linea_exp['nombre_expediente']; // Listado de radicados en tabla expediente
	// 					$query_expedientes 	= "update 
	// 												expedientes 
	// 											set 
	// 												lista_radicados = '".$lista_radicados."'
	// 											where 
	// 												id_expediente = '".$id_expediente."';";
	// 					$query_rad_exp 	.= $query_expedientes;
	// 					$transaccion_historico 	.= " y asocia en expediente";	// Variable para tabla historico_eventos
	// 					$comentario				.= " y se asocia directamente al expediente <b>".$id_expediente."(".$nombre_expediente.")</b>";	// Variable para historico eventos
	// 					$transaccion 			.= "_expediente"; 	// Variable para auditoria quedaría plantilla_salida_expediente ó modifica_plantilla_salida_expediente
	// 				}
	// 			}
	// 		}else{
	// 			$query_rad_exp = "update 
	// 									radicado
	// 								set
	// 									numero_radicado 		= '".$radicado."',
	// 									fecha_radicado 			= '".$timestamp."',
	// 									codigo_carpeta1 		= '".$codigo_carpeta1."',
	// 									numero_guia_oficio 		= 'No se ha enviado al destinatario todavía, por lo tanto no hay soporte de envío.',
	// 									descripcion_anexos 		= '".$anexos."',
	// 									path_radicado 			= '".$path_radicado."',
	// 									dependencia_actual 		= '".$_SESSION['dependencia']."',
	// 									usuarios_visor 			= '".$usuario_visor_and_control."',
	// 									usuarios_control 		= '".$usuario_visor_and_control."',
	// 									dependencia_radicador 	= '".$_SESSION['dependencia']."',
	// 									usuario_radicador 		= '".$_SESSION['login']."',
	// 									asunto 					= '".$asunto."',
	// 									nivel_seguridad 		= '".$_SESSION['nivel']."',
	// 									leido 					= '".$_SESSION['login']."',
	// 									clasificacion_radicado 	= 'OFICIO',
	// 									termino 				= '15',
	// 									codigo_serie 			= '".$_POST['codigo_serie']."',
	// 									codigo_subserie 		= '".$_POST['codigo_subserie']."',
	// 									estado_radicado 		= 'en_tramite'
	// 								where 
	// 									numero_radicado 		= '".$radicado."';
	// 								update
	// 									datos_origen_radicado
	// 								set
	// 									codigo_datos_origen_radicado 	= '2',
	// 									numero_radicado 				= '".$radicado."',
	// 									nombre_remitente_destinatario 	= '".$destinatarios_final_2."',
	// 									ubicacion 						= '".$ubicacion2."'
	// 								where 
	// 									numero_radicado 		= '".$radicado."';
	// 								update 
	// 									version_documentos
	// 								set
	// 									numero_radicado = '$radicado',
	// 									version = '".$version."',
	// 									usuario_modifica = '".$_SESSION['login']."',
	// 									fecha_modifica = '".$timestamp."',
	// 									path_pdf = '".$path_radicado."',
	// 									html_asunto = '".$_POST['editor2']."',
	// 									despedida = '".$_POST['despedida']."',
	// 									tratamiento = '".$_POST['tratamiento']."',
	// 									usuario_que_firma = '".$_POST['firmante']."',
	// 									cargo_usuario_que_firma = '".$_POST['cargo_firmante']."',
	// 									firmado = '".$firmado."',
	// 									usuario_que_aprueba = '".$_POST['aprueba']."',
	// 									cargo_usuario_que_aprueba = '".$_POST['cargo_aprueba']."',
	// 									aprobado = '".$aprobado."',
	// 									usuario_que_elabora = '".$_POST['elabora']."',
	// 									cargo_usuario_que_elabora = '".$_POST['cargo_elabora']."',
	// 									cargo_destinatario = '".$aprobado."'
	// 								where 
	// 									numero_radicado 		= '".$radicado."';";				
	// 			if($_POST['id_expediente'] != ""){
	// 				$radicado_existente = 0;
	// 				$query_exp 		= "select 
	// 										lista_radicados,
	// 										nombre_expediente
	// 									from 
	// 										expedientes
	// 									where 
	// 										id_expediente = '$id_expediente'";
	// 				$fila_exp 	  		= pg_query($conectado,$query_exp);
	// 				$linea_exp    		= pg_fetch_array($fila_exp);
	// 				$lista_radicados 	= $linea_exp['lista_radicados']; // Listado de radicados en tabla expediente
	// 				$lista_radicados_array = explode(",", $lista_radicados);
	// 				foreach($lista_radicados_array as $key => $value){
	// 					if($radicado == $value)$radicado_existente++;
	// 				}
	// 				if($radicado_existente == 0){
	// 					if(count($lista_radicados_array) > 0 && $lista_radicados != "")$lista_radicados .= ",";
	// 					$lista_radicados   .= $radicado;
	// 					$nombre_expediente 	= $linea_exp['nombre_expediente']; // Listado de radicados en tabla expediente
	// 					$query_expedientes 	= "update 
	// 												expedientes 
	// 											set 
	// 												lista_radicados = '".$lista_radicados."'
	// 											where 
	// 												id_expediente = '".$id_expediente."';";
	// 					$query_rad_exp 	        .= $query_expedientes;
	// 					$transaccion_historico 	.= " y asocia en expediente";	// Variable para tabla historico_eventos
	// 					$comentario				.= " y se asocia directamente al expediente <b>".$id_expediente."(".$nombre_expediente.")</b>";	// Variable para historico eventos
	// 					$transaccion 			.= "_expediente"; 	// Variable para auditoria quedaría plantilla_salida_expediente ó modifica_plantilla_salida_expediente
	// 				}
	// 			}
	// 		}
	// 	}else{
	// 		$imagenqr = "<h5 id='codigo_qr'>
	//             				Sin Imagen Qr<br><br>
	//             				Vista Previa
	// 	            		</h5>";
	// 	}
	// 	$imprenta_pdf = "
 //        				<style>
 //        					#header{
 //        						width: 700px;
 //        						height: 100px;
 //        					}
 //        					#dependencia_usuario{
 //        						position: absolute;
 //        						margin-top: 40px;
 //        						margin-left: -200px;
 //        					}
 //        					#codigo_qr{
 //        						position: absolute;
 //        						margin-top: 113px;
 //        						margin-left: -176px;
 //        						text-align: center;
 //        					}
 //        					#ubicacion_fecha{
 //        						position: relative;
 //        					}
 //        					#tratamiento_destinatario{
 //        						font-weight: bold;
 //        					}
 //        					#asunto{
 //        						color: black;
 //        						font-weight: bold;
	//         				}
	//         				#editor{
	//         					display: flex;
	//         					justify-content: space-between;
	// 	        			}
	// 	        			#despedida{
	// 	        				font-weight: bold;
	// 	        				margin: 100px 0px 20px 0px;
	// 		        		}
	// 		        		#firmante{
	// 		        			font-size: 15px;
	// 		        			font-weight: bold;
	// 			        		margin-bottom: 60px;
	// 				        }
	// 				        #anexos{
	// 					        font-weight: bold;
	// 					        margin-right: 28px;
	// 					    }
	// 					    #aprobado{
	// 					    	font-weight: bold;
	// 					    	margin-right: 2px;
	// 						}
	// 						#elaborado{
	// 							font-weight: bold;
	// 							margin-right: 2px;
	// 						}
	// 						#footer{
	// 							position: fixed;
	// 							bottom: -50px;
	// 							width: 600px;
	// 							height: 60px;
	// 						}
	// 						#mas_informacion{
	// 							font-size: 8px;
	// 							margin-left: 25px;
	// 						}
	// 						#texto_anexos{
	// 							font-weight: bold;
	// 							margin-right: 26px;
	// 						}
	// 						#texto_aprobado{
	// 							font-weight: bold;
	// 							margin-right: 2px;
	// 						}
	// 						#texto_elaborado{
	// 							font-weight: bold;
	// 							margin-right: 0.5px;
	// 						}
 //        				</style>
 //        				<img id='header' src='".trim($_POST['src_imagen_cabecera'])."'>
 //                		<h6 id='dependencia_usuario'>
 //                			".$_SESSION['nombre_dependencia']."
 //                		</h6>
	//             		".$imagenqr."
	//             		<p id='ubicacion_fecha'>
	//             			".$_POST['fecha']."; ".ucwords(strtolower(trim($ubicacion)))."
	//             		</p>
	//             		<p id='tratamiento_destinatario'>
	// 		            	".$_POST['tratamiento']."
	// 		            </p>
	// 		            ".$_POST['destinatarios']."
	// 		            ".ucwords(strtolower(trim($ubicacion2)))."
	// 		            <p id='asunto'>
	// 	            		Asunto: ".$_POST['asunto']."
	// 	            	</p>
	// 	            	<p id='editor'>
	// 	             		".$_POST['editor2']."
	// 	             	</p>
	// 	            	<p id='despedida'>
	// 	            		".$_POST['despedida']."
	// 	            	</p>
	// 	            	<p id='firmante'>
	// 	            		".$firma_virtual."
	// 	            		".$_POST['firmante']."<br>
	// 	            		".$_POST['cargo_firmante']."
	// 	            	</p>
	// 	            	<div id='mas_informacion'>
	// 					    <span id='texto_anexos'>
	// 					    	Anexos
	// 					    </span>
	// 					    : ".$anexos."<br>
	// 					    <span id='texto_aprobado'>
	// 					    	Aprobado por
	// 					    </span>
	// 					    : ".$_POST['aprueba']." - ".$_POST['cargo_aprueba']."<br>
	// 					    <span id='texto_elaborado'>
	// 					    	Elaborado por
	// 					    </span>
	// 					    : ".$_POST['elabora']." - ".$_POST['cargo_elabora']."<br>
	//             		</div>
	//                 	<img id='footer' src='".trim($_POST['src_imagen_piecera'])."'>
	// 		            ";// Estructura Html
	// 	require_once 'dompdf/dompdf/lib/html5lib/Parser.php';// Libreria pdf
	// 	require_once 'dompdf/dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';// Libreria pdf
	// 	require_once 'dompdf/dompdf/lib/php-svg-lib/src/autoload.php';// Libreria pdf
	// 	require_once 'dompdf/dompdf/src/Autoloader.php';// Libreria pdf	            
	// 	Dompdf\Autoloader::register();// Se registra en el docuemnto para poder acceder a las herencias
	// 	$dompdf = new Dompdf\Dompdf();// Se abre la variable
	// 	$dompdf->loadHtml("<html>".$imprenta_pdf."</html>");// Se carga el cuerpo del pdf
	// 	$dompdf->render();// Se arregla el cuerpo pdf y sus estilos aplicados
	// 	$dompdf->stream();// Se arregla el cuerpo pdf y sus estilos aplicados
	// 	if(!(file_exists($ruta2)))mkdir($ruta2, 0700);// Crea un directorio
	// 	if(file_exists($ruta))unlink($ruta);// Si existe el documento se borra
	// 	file_put_contents(
	// 	    $ruta,
	// 	    $dompdf->output()// Se escribe los datos en un fichero
	// 	);
	// 	chmod(
	// 			$ruta,
	// 			0777
	// 		);// Cambia el modo de un fichero
	// 	if($_POST['tipo'] == 2){
	// 		unlink("../bodega_pdf/plantilla_generada_tmp/".$_SESSION['login']."/vista_previa_radicado_interno.pdf");
	// 		if(pg_query($conectado, $query_rad_exp)){
	// 				$creado 	= $radicado;
	// 				require_once("../login/inserta_historico.php");
	// 		}else{
	// 			echo "<script>
	// 						alert('Ocurrió un error al generar el radicado interno, por favor contactar con el administrador del sistema.')
	// 					</script>";
	// 		}
	// 	}
	// 	break;
	/*****************************************************************************************
		Fin case generar_vista_previa_pdf_radicacion_interna
	/*****************************************************************************************
	/*****************************************************************************************
		Case guardar_gestionar_datos_usuario guarda la informacion correspondiente del usuario a base de datos
	/*****************************************************************************************
		* @brief Recibe desde login/gestionar_datos_usuario.php[function Guardar_formulario_gestionardatos()]
		* @return {string} devuleve una cadena de confirmacion o de error
	*****************************************************************************************/	
	case 'guardar_gestionar_datos_usuario':

		$cargo_usuario  = $_POST['cargo_usuario'];
		$segunda_clave 	= $_POST['segunda_clave'];
 		$id_usuario     = $_POST['id_usuario'];

 		$correo 	    = strtolower($_POST['correo']);

		$datos_usuario = "";

		if(!empty($_FILES["gestionardatos_imagen"]["name"])){// Se valida si el usuario cambiara de imagen
 			$target_file = basename($_FILES["gestionardatos_imagen"]["name"]);// Tomamos solo el nombre del archvio
 			$target_dir  = "../imagenes/fotos_usuarios/";// Se define el directorio donde sera guardado

 			if(move_uploaded_file($_FILES["gestionardatos_imagen"]["tmp_name"],$target_dir.$target_file)){// Se guarda en el directorio el archivo
				$path_foto 	 	= "imagenes/fotos_usuarios/$target_file";// Se define donde se guardo y se complementa con el nombre del archivo para ser guardado en la base de datos
				$datos_usuario.=", path_foto='$path_foto'"; // agrega a consulta sql
				$path_foto 	 = "imagenes/fotos_usuarios/$target_file";// Se define donde se guardo y se complementa con el nombre del archivo para ser guardado en la base de datos
				$query_usuario = "update usuarios set mail_usuario='$correo', path_foto='$path_foto', pass2=trim(md5('$segunda_clave')) where id_usuario ='$id_usuario'";// Consulta sql
			}
		}else{
			if($segunda_clave == ''){// Se valida que el usuario vaya a cambiar la segunda password
				$query_usuario = "update usuarios set mail_usuario='$correo' where id_usuario ='$id_usuario'";// Consulta sql
			}else{
				$query_usuario = "update usuarios set mail_usuario='$correo', pass2=trim(md5('$segunda_clave')) where id_usuario ='$id_usuario'";// Consulta sql
 			}
 		}

		if($cargo_usuario != ''){// Se valida que el usuario vaya a cambiar la segunda clave
			$datos_usuario.= ", cargo_usuario='$cargo_usuario'";// agrega a consulta sql
		}

		if($segunda_clave != ''){// Se valida que el usuario vaya a cambiar la segunda clave
			$datos_usuario.= ", pass2=trim(md5('$segunda_clave'))";// agrega a consulta sql
		}

		$query_usuario = "update usuarios set mail_usuario='$correo' $datos_usuario where id_usuario ='$id_usuario'";// Consulta sql
	
 		if(pg_query($conectado,$query_usuario)){// Se ejcuta la consulta a sql y se valida si salio true
 			if(!isset($_SESSION)){
				session_start();
	  		}

 			$_SESSION['cargo_usuario'] = $cargo_usuario;
 			echo "Se han actualizado los datos correctamente.";
 		}else{
 			echo "No se han actualizado los datos por favor revisar las variables.";
 		}
		break;
	/*****************************************************************************************
		Fin case guardar_gestionar_datos_usuario
	/*****************************************************************************************/
	case 'incluir_en_expediente':
		$expediente 	= $_POST['id_expediente'];
		$radicado 		= $_POST['radicado'];

		$query_rad ="select id_expediente from radicado where numero_radicado='$radicado'"; // Query para verificar listado de expedientes en el radicado

		$fila_rad 	  	= pg_query($conectado,$query_rad);
		$linea_rad    	= pg_fetch_array($fila_rad);
		$id_expediente 	= $linea_rad['id_expediente']; // Listado de expedientes en tabla radicado

		$id_expediente.="$expediente,";

		$query_radicado = "update radicado set id_expediente='$id_expediente' where numero_radicado='$radicado';";

		$query_exp 			= "select lista_radicados, nombre_expediente from expedientes where id_expediente='$expediente'";
		$fila_exp 	  		= pg_query($conectado,$query_exp);
		$linea_exp    		= pg_fetch_array($fila_exp);
		$lista_radicados 	= $linea_exp['lista_radicados']; // Listado de radicados en tabla expediente
		$nombre_expediente 	= $linea_exp['nombre_expediente']; 

		$lista_radicados.="$radicado,";

		$query_expedientes = "update expedientes set lista_radicados='$lista_radicados' where id_expediente='$expediente';";
		$query_rad_exp=$query_radicado.$query_expedientes;

		if(pg_query($conectado,$query_rad_exp)){ 
			$radicado 				= $radicado;
			$transaccion_historico 	= "Se incluye radicado en expediente $expediente";	// Variable para tabla historico_eventos
			$comentario 			= "Se ha incluido el radicado $radicado en el expediente $expediente ($nombre_expediente)";		// Variable para historico eventos

			$transaccion 			= "incluir_radicado_en_expediente"; 	// Variable para auditoria
			$creado 				= "$radicado";	// Variable para auditoria
			require_once("../login/inserta_historico.php");		
		}else{
			echo "<script> alert('Ocurrió un error al realizar el incluir en expediente de éste radicado, por favor revisar e intentar nuevamente.')</script>";
		}	

		break;

		case 'info_aprobado_radicacion_interna':
			if(!isset($_SESSION)){
				session_start();
	  		}
			if ($_POST['aprobado'] == $_SESSION['nombre']) {
				echo "true";
			}else{
				echo "false";
			}
			break;

		case 'info_aprueba_radicacion_interna':
			$consulta_sql = "select
								u.id_usuario,
								u.nombre_completo,
								u.login,
								u.perfil,
								u.codigo_dependencia,
								d.nombre_dependencia,
								u.path_foto
							from 
								usuarios as u
							inner join 
								dependencias as d on d.codigo_dependencia = u.codigo_dependencia
							where 
								u.nombre_completo = '".$_POST['aprueba']."'";// Estructura sql
			$fila_sql      					= pg_query($conectado,$consulta_sql);// Se enviá la consulta
			$linea_sql 	    				= pg_fetch_array($fila_sql);// Se pasa a un array la información de la consulta
			$id_usuario 					= $linea_sql['id_usuario'];
			$codigo_dependencia_usuario		= $linea_sql['codigo_dependencia'];
			$nombre_dependencia_usuario		= $linea_sql['nombre_dependencia'];
			$nombre_usuario 				= $linea_sql['nombre_completo'];
			$login_usuario 					= $linea_sql['login'];
			$path_foto						= $linea_sql['path_foto'];
			$aprueba_html = '<script>
									cargar_aprueba("'.$id_usuario.'", "'.$codigo_dependencia_usuario.'", "'.$nombre_dependencia_usuario.'", "'.$nombre_usuario.'", "'.$login_usuario.'", "'.$path_foto.'",  "'.$_POST['cargo_aprobado'].'");
								</script>';
			echo $aprueba_html;
		 	break;


		case 'info_dignatarios_radicacion_interna':
			function validar_longitud($valor_comparar){
				if(strlen($valor_comparar) >= 16){
					$valor_comparar = substr($valor_comparar, 0, 13).'...';
				}
				return $valor_comparar;
			}
			$dignatario_html = "<script>";
			$primer_nombre_where = 0;
			$dignatarios = $_POST['dignatario'];
			$array_destinatarios = explode(",", $dignatarios);
			$cargo_dignatarios = $_POST['cargo_dignatario'];
			$array_cargo_destinatarios = explode(",", $cargo_dignatarios);
			foreach ($array_destinatarios as $key => $value){
				$consulta_sql = "select 
										u.id_usuario, 
										u.nombre_completo, 
										u.login, 
										u.perfil, 
										u.codigo_dependencia, 
										d.nombre_dependencia, 
										u.path_foto
								from 
									usuarios as u
								inner join 
									dependencias as d on d.codigo_dependencia = u.codigo_dependencia
								where 
									u.nombre_completo = '".$value."'";// Estructura sql
				$fila_sql      	= pg_query($conectado,$consulta_sql);// Se enviá la consulta
				$linea_sql 	    				= pg_fetch_array($fila_sql);// Se pasa a un array la información de la consulta
				$id_usuario 					= $linea_sql['id_usuario'];
				$nombre_usuario 				= validar_longitud($linea_sql['nombre_completo']);
				$login_usuario 					= validar_longitud($linea_sql['login']);
				$perfil_usuario		 			= validar_longitud($linea_sql['perfil']);
				$codigo_dependencia_usuario		= $linea_sql['codigo_dependencia'];
				$nombre_dependencia_usuario		= $linea_sql['nombre_dependencia'];
				$path_foto						= $linea_sql['path_foto'];
				$dignatario_html .= 'agregar_destinatario("'.$id_usuario.'", "'.$path_foto.'", "'.$linea_sql['nombre_completo'].'", "'.$nombre_usuario.'", "'.$login_usuario.'", "'.$codigo_dependencia_usuario.'", "'.$nombre_dependencia_usuario.'", "'.$array_cargo_destinatarios[$key].'");';
			}
			$dignatario_html .= "</script>";
			echo $dignatario_html;
			break;

		case 'info_elabora_radicacion_interna':
			$consulta_sql = "select
								u.id_usuario,
								u.nombre_completo,
								u.login,
								u.perfil,
								u.codigo_dependencia,
								d.nombre_dependencia,
								u.path_foto
							from 
								usuarios as u
							inner join 
								dependencias as d on d.codigo_dependencia = u.codigo_dependencia
							where 
								u.nombre_completo = '".$_POST['elabora']."'";// Estructura sql
			$fila_sql      					= pg_query($conectado,$consulta_sql);// Se enviá la consulta
			$linea_sql 	    				= pg_fetch_array($fila_sql);// Se pasa a un array la información de la consulta
			$id_usuario 					= $linea_sql['id_usuario'];
			$codigo_dependencia_usuario		= $linea_sql['codigo_dependencia'];
			$nombre_dependencia_usuario		= $linea_sql['nombre_dependencia'];
			$nombre_usuario 				= $linea_sql['nombre_completo'];
			$login_usuario 					= $linea_sql['login'];
			$path_foto						= $linea_sql['path_foto'];
			$elabora_mod_html = '<script>
										cargar_elabora("'.$id_usuario.'", "'.$codigo_dependencia_usuario.'", "'.$nombre_dependencia_usuario.'", "'.$nombre_usuario.'", "'.$login_usuario.'", "'.$path_foto.'",  "'.$_POST['cargo_elabora'].'");
									</script>';
			echo $elabora_mod_html;
			break;


		case 'info_firmante_radicacion_interna':
			$consulta_sql = "select 
								u.id_usuario,
								u.nombre_completo,
								u.login, u.perfil,
								u.codigo_dependencia,
								d.nombre_dependencia,
								u.path_foto
							from 
								usuarios as u
							inner join 
								dependencias as d on d.codigo_dependencia = u.codigo_dependencia
							where 
								u.nombre_completo = '".$_POST['firmante']."'";// Estructura sql
			$fila_sql    = pg_query($conectado,$consulta_sql);// Se enviá la consulta
			$linea_sql 	 = pg_fetch_array($fila_sql);// Se pasa a un array la información de la consulta
			$id_usuario 					= $linea_sql['id_usuario'];
			$codigo_dependencia_usuario		= $linea_sql['codigo_dependencia'];
			$nombre_dependencia_usuario		= $linea_sql['nombre_dependencia'];
			$nombre_usuario 				= $linea_sql['nombre_completo'];
			$login_usuario 					= $linea_sql['login'];
			$path_foto						= $linea_sql['path_foto'];
			$firmante_html = '<script>
									cargar_firmante("'.$id_usuario.'", "'.$codigo_dependencia_usuario.'", "'.$nombre_dependencia_usuario.'", "'.$nombre_usuario.'", "'.$login_usuario.'", "'.$path_foto.'",  "'.$_POST['cargo_firmante'].'");
								</script>';
			echo $firmante_html;
			break;


		case 'info_firmado_radicacion_interna':
			if ($_POST['firmante'] == $_SESSION['nombre']) {
				return true;
			}else{
				return false;
			}
			break;

	/*****************************************************************************************
	Inicio funcion para informar radicado 
	/*****************************************************************************************
	* @brief Recibe desde bandejas/entrada/transacciones_radicado.php[valida_informar_radicado()]
	** Se actualiza el campo codigo_carpeta1 en la tabla radicado y se guarda en el histórico
	** la trazabilidad de la acción.

	* @param {string} (mensaje_informar) Mensaje que se guarda como justificación por informar el 
	** radicado.
	* @param {string} (numero_radicado) Numero de radicado que es informado 
	* @param {string} (usuarios_nuevos_informar) Listado de usuarios separados por coma que 
	** se informan en esta transaccion, se agregan al campo codigo_carpeta1 y se marcan como 
	** "no_leido" 
	* @param {string} (usuarios_para_informar) Listado de usuarios separados por coma que 
	** actualmente tienen el documento en alguna de sus bandejas, a estos se marca el radicado 
	** como "no_leido" y se mantienen en la bandeja personal, entrada o salida. 
	*/	
	case 'informar_radicado':
		// $codigo_carpeta 			= $_POST['codigo_carpeta'];
		// $usuario_actual_informado 	= $_POST['usuario_actual_informado'];
		$mensaje_informar 			= $_POST['mensaje_informar'];
		$numero_radicado 			= $_POST['numero_radicado'];
		$usuarios_nuevos_informar 	= $_POST['usuarios_nuevos_informar'];
		$usuarios_para_informar 	= $_POST['usuarios_para_informar'];

		$usuarios_visor = $usuarios_para_informar.$usuarios_nuevos_informar;

		// Extraigo cada uno de los usuarios_nuevos_informar	
		$usu2  = explode(",", $usuarios_nuevos_informar);
		$max3  = sizeof($usu2);
		$max4 = $max3-1;

		$consulta_radicado 	= "select * from radicado where numero_radicado='$numero_radicado'";
		$fila_radicado 	  	= pg_query($conectado,$consulta_radicado);
		$linea_radicado    	= pg_fetch_array($fila_radicado);
		$codigo_carpeta1 	= $linea_radicado['codigo_carpeta1'];
		$codigo_carpeta2 	= json_decode($codigo_carpeta1,true);		

		for ($r=0; $r < $max4; $r++) { // Quito el usuario_derivado del listado de usuarios_nuevos
			$login_usuario_info 					= $usu2[$r];
			$codigo_carpeta2[$login_usuario_info] 	= array('codigo_carpeta_personal'=>'entrada');
		}
		$codigo_carpeta3 							= json_encode($codigo_carpeta2);

		$query_informar_radicado="update radicado set codigo_carpeta1='$codigo_carpeta3', usuarios_visor='$usuarios_visor', leido='$usuarios_visor' where numero_radicado='$numero_radicado'";

		if(pg_query($conectado,$query_informar_radicado)){ 
			$radicado 				= $numero_radicado;
			$transaccion_historico 	= "Se Informa Radicado";	// Variable para tabla historico_eventos
			$comentario 			= "Se ha informado el radicado a los usuarios $usuarios_nuevos_informar <br> $mensaje_informar";		// Variable para historico eventos

			$transaccion 			= "informar_radicado"; 	// Variable para auditoria
			$creado 				= "$numero_radicado";	// Variable para auditoria
			require_once("../login/inserta_historico.php");		
		}else{
			echo "<script> alert('Ocurrió un error al realizar el informado del radicado, por favor revisar e intentar nuevamente.')</script>";
		}	
		break;

	case 'ingresar_metadato':
	/*****************************************************************************************
	Inicio funcion para ingresar metadato 
	/*****************************************************************************************
	* @brief Recibe desde admin/metadatos/index_metadatos.php[submit_agregar_metadatos()]
	* para ingresar en la base de datos el campo "metadatos" en la tabla "subserie" en formato JSONJ. 

	* @param {string} (json_final) Es un listado de atributos del metadato en formato JSON, el cual va a ser almacenado en la base de datos para posterior consulta y generacion de reportes.
	*/	
		$json_recibido 	= $_POST['json_recibido'];
		$serie 			= $_POST['codigo_serie'];
		$subserie 		= $_POST['codigo_subserie'];

		var_dump($_POST);

		$query_metadatos 	= "select max(id) from metadatos_expedientes";
		$fila_metadato 	 	= pg_query($conectado,$query_metadatos);
		$linea_metadato  	= pg_fetch_array($fila_metadato);
		$max_id 			= $linea_metadato['max'];

		$codigo_serie 		= $_POST['codigo_serie'];
		$codigo_subserie 	= $_POST['codigo_subserie'];

		$query_insertar_metadato = "";

		$json_recibido = $_POST['json_recibido'];

		$array_metadatos = json_decode($json_recibido);

		foreach($array_metadatos as $obj){
	        $nombre_metadato 		= $obj->nombre_metadato;
	        $tipo_metadato 			= $obj->tipo_metadato;
	        $obligatorio 			= $obj->obligatorio;
	        $archivo_anexo 			= $obj->archivo_anexo;
	        $tipo_archivo 			= $obj->tipo_archivo;
	        $valores_desplegable 	= $obj->valores_desplegable;
	      	
	      	$query_insertar_metadato.="insert into metadatos_expedientes (id, codigo_serie, codigo_subserie, nombre_metadato, tipo_metadato, campo_obligatorio, requiere_archivo_anexo, tipo_archivo_anexo, opciones_desplegable, tipo_texto) values('$max_id','$codigo_serie', '$codigo_subserie','$nombre_metadato','$tipo_metadato','$obligatorio','$archivo_anexo','$tipo_archivo','$valores_desplegable');";
	      	$max_id++;
		}
	        echo "$query_insertar_metadato";

		





		// if(pg_query($conectado,$query_insertar_metadato)){
		// 	echo "<script> 
		// 		auditoria_general('insertar_metadato','$serie-$subserie');	
		// 	</script>";
  //   	}else{
		// 	echo "<script>
		// 		alert('No se pudo insertar el metadato a la subserie. Comuníquese con el administrador del sistema');
		// 		carga_administrador_metadatos();
		// 	</script>";
    		
  //       };


		break;

	/*   Fin funcion para ingresar metadato *
	/*****************************************************************************************/

	/*****************************************************************************************
	* @brief Recibe desde bandejas/entrada/visualiza_radicado.php[insertar_trd_radicado()]
	* para insertar la TRD al radicado recibido como parámetro. En caso de no poder insertar, devuelve error.
	* @param {string} ($radicado) Numero de radicado al cual se le va a asignar la TRD
	* @param {string} ($serie) Codigo de serie de la TRD que se va a asignar al radicado
	* @param {string} ($subserie) Codigo de subserie de la TRD que se va a asignar al radicado
	* @return {} No retorna ningun valor. Al insertar en (login/inserta_historico.php) dependiendo de la variable $transacción, muestra la siguiente pantalla. 
	*****************************************************************************************/		
	case 'insertar_trd_radicado':	// Recibe desde bandejas/entrada/pestanas.php[function insertar_trd_radicado()]
		$radicado 	= $_POST['radicado'];
        $serie 		= $_POST['serie'];
        $subserie 	= $_POST['subserie'];    

        $query_trd_radicado = "update radicado set codigo_serie='$serie', codigo_subserie='$subserie' where numero_radicado='$radicado'";

        if(pg_query($conectado,$query_trd_radicado)){
			/* Desde aqui se genera historico */	
				$transaccion_historico 	= "Asignación TRD Radicado";	// Variable para tabla historico_eventos
				$comentario				= "Se asigna TRD al radicado";	// Variable para historico eventos
				$transaccion 			= "insertar_trd_radicado"; 	// Variable para auditoria
				$creado 				= "$radicado";			// Variable para auditoria
				require_once("../login/inserta_historico.php");		
			/* Hasta aqui se genera historico */
		}else{
			echo "<script>
				alert('No se pudo insertar la TRD al radicado. Comuníquese con el administrador del sistema');
				volver();
			</script>";
		}	

        break;

	/*****************************************************************************************
	* @brief Recibe parametros y despues de procesarlos, inserta la TRD y EXPEDIENTE a un radicado 
	* @descrip Recibe desde bandejas/entrada/visualiza_radicado.php[insertar_trd_exp_radicado()]
	* para insertar la TRD y EXPEDIENTE al radicado recibido como parámetro. En caso de no poder insertar, devuelve error.
	* Se consulta si hay otro id_expediente en el radicado para agregarlo al $id_expediente, luego se define la variable $id_expediente terminada con coma para indicar que el radicado pertenece a varios expedientes, Se consulta nombre de serie y subserie para ponerlo en histórico_eventos, Se consulta lista_radicados y nombre_expediente de la tabla "expedientes" para agregar el radicado de esta funcion y actualizarlo en la misma tabla, Se arma query para actualizar la tabla "expedientes" y luego Se une la $query_update_expediente y arma la query para actualizar la TRD y EXPEDIENTE de la tabla "radicado". Cuando ejecuta la query completa, inserta historico.
	* @param {string} ($radicado) Numero de radicado al cual se le va a asignar la TRD/EXPEDIENTE
	* @param {string} ($expediente) Id del expediente que se usa para actualizar la tabla "expedientes" y "radicado"
	* @param {string} ($nombre_expediente) Nombre del expediente que se usa para poner en el histórico del radicado
	* @param {string} ($serie) Codigo de serie de la TRD que se va a asignar al radicado
	* @param {string} ($subserie) Codigo de subserie de la TRD que se va a asignar al radicado
	* @return {} No retorna ningun valor. Al insertar en (login/inserta_historico.php) dependiendo de la variable $transacción, muestra la siguiente pantalla. 
	*****************************************************************************************/		
	case 'insertar_trd_exp_radicado':	// Recibe desde bandejas/entrada/pestanas.php[function insertar_trd_radicado()]
		$radicado 			= $_POST['radicado'];
        $expediente 		= $_POST['id_expediente'];    
        $nombre_expediente 	= $_POST['nombre_expediente'];    
        $serie 				= $_POST['serie'];
        $subserie 			= $_POST['subserie'];    

        /* Se consulta si hay otro id_expediente en el radicado para agregarlo al $id_expediente */
        $query_rad 		="select id_expediente, usuarios_visor from radicado where numero_radicado='$radicado'"; // Query para verificar listado de expedientes en el radicado
		$fila_rad 	  	= pg_query($conectado,$query_rad);
		$linea_rad    	= pg_fetch_array($fila_rad);
		$id_expediente 	= $linea_rad['id_expediente'];  // Listado de expedientes en tabla radicado
		$usuarios_visor = $linea_rad['usuarios_visor']; // Listado de usuarios_visor en tabla radicado

		/* Se define la variable $id_expediente terminada con coma para indicar que el radicado pertenece a varios expedientes */
		$id_expediente.="$expediente,";

        /* Se consulta nombre de serie y subserie para ponerlo en histórico_eventos */
		$query_nom_subserie  	= "select * from subseries where codigo_serie='$serie' and codigo_subserie='$subserie'";
		$fila_nom_subserie 	  	= pg_query($conectado,$query_nom_subserie);
		$linea_nom_subserie    	= pg_fetch_array($fila_nom_subserie);
		$nombre_serie 			= $linea_nom_subserie['nombre_serie']; 
		$nombre_subserie 		= $linea_nom_subserie['nombre_subserie']; 

		/* Funcion para quitar los ceros al mostrar el codigo de serie/subserie */
        $codigo_serie2       	= "".(int) $serie."";
        $codigo_subserie2      	= "".(int) $subserie."";

        /* Se consulta lista_radicados y nombre_expediente de la tabla "expedientes" para agregar el radicado de esta funcion y actualizarlo en la misma tabla. */
        $query_exp 			= "select lista_radicados, nombre_expediente from expedientes where id_expediente='$expediente'";
		$fila_exp 	  		= pg_query($conectado,$query_exp);
		$linea_exp    		= pg_fetch_array($fila_exp);
		$lista_radicados 	= $linea_exp['lista_radicados']; // Listado de radicados en tabla expediente
		$nombre_expediente 	= $linea_exp['nombre_expediente']; 

		/* Se agrega el radicado a $lista_radicados */
		$lista_radicados.="$radicado,";

		/* Se arma query para actualizar la tabla "expedientes" */
		$query_update_expediente = "update expedientes set lista_radicados='$lista_radicados' where id_expediente='$expediente';";

        /* Se une la $query_update_expediente y arma la query para actualizar la TRD y EXPEDIENTE de la tabla "radicado" */
        $query_trd_exp_radicado = "$query_update_expediente update radicado set codigo_serie='$serie', codigo_subserie='$subserie', id_expediente='$id_expediente', leido='$usuarios_visor' where numero_radicado='$radicado';";

        if(pg_query($conectado,$query_trd_exp_radicado)){
			/* Desde aqui se genera historico */	
				$transaccion_historico 	= "Asignación TRD - Expediente al Radicado";	// Variable para tabla historico_eventos
        		$comentario				= "Se asigna TRD <br>(<b>$codigo_serie2,$codigo_subserie2</b>)-[$nombre_serie/$nombre_subserie] <br>y <br>EXPEDIENTE (<b>$id_expediente</b>)-[$nombre_expediente]<br> al radicado";	// Variable para historico eventos
				$transaccion 			= "insertar_trd_exp_radicado"; 	// Variable para auditoria
				$creado 				= "$radicado";			// Variable para auditoria
				require_once("../login/inserta_historico.php");		
			/* Hasta aqui se genera historico */
		}else{
			echo "<script>
				alert('No se pudo insertar la TRD al radicado. Comuníquese con el administrador del sistema');
			</script>";
		}	

        break;

	case 'listado_dependencias':		// Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function cargar_input_codigo_dependencia()]
		$query_dependencia = "select * from dependencias where id_dependencia!=1 order by nombre_dependencia";
		
		/* Aqui se ejecuta la query */
    	$fila_query_dependencia  		= pg_query($conectado,$query_dependencia);

		/* Se trae las filas de la query */
	   	$registros_query_dependencia 	= pg_num_rows($fila_query_dependencia);

		if($registros_query_dependencia==0){
			echo "<script>alert('No hay dependencias configuradas todavía')</script>";
		}else{					
			for ($i=0; $i < $registros_query_dependencia ; $i++){
		    	$linea_codigo_dependencia 	= pg_fetch_array($fila_query_dependencia);  
				$codigo_dependencia 		= $linea_codigo_dependencia['codigo_dependencia'];
				$nombre_dependencia 		= $linea_codigo_dependencia['nombre_dependencia'];

				echo "<option value='$codigo_dependencia'>($codigo_dependencia) $nombre_dependencia</option>";
			}
		}		
		break;

	case 'listado_expedientes_dependencia':		// Recibe desde bandejas/entrada/pestanas.php[function cargar_expediente_nuevo(dependencia)]
		$dependencia  	= $_POST['dependencia'];
		$radicado 		= $_POST['radicado'];

		$query_rad ="select id_expediente from radicado where numero_radicado='$radicado'"; // Query para verificar listado de expedientes en el radicado

		$fila_rad 	  	= pg_query($conectado,$query_rad);
		$linea_rad    	= pg_fetch_array($fila_rad);
		$expediente  	= $linea_rad['id_expediente']; // Listado de expedientes en tabla radicado

		// $query_expediente = "select * from expedientes e join subseries s on e.subserie=s.codigo_subserie where dependencia_expediente='$dependencia' order by nombre_expediente";
		$query_expediente = "select * from expedientes where dependencia_expediente='$dependencia' order by fecha_inicial desc";

		/* Aqui se ejecuta la query */
    	$fila_query_expediente  		= pg_query($conectado,$query_expediente);

		/* Se trae las filas de la query */
	   	$registros_query_expediente 	= pg_num_rows($fila_query_expediente);
	   	echo "<tr><td class='descripcion' colspan='4' style='font-size:15px; text-align:center;'>Listado de expedientes de la dependencia $dependencia</td></tr>";
		if($registros_query_expediente==0){
			echo "<tr><td><h2 class='center'>No hay expedientes en ésta dependencia creados todavía.</h2></td></tr>";
		}else{					
			for ($i=0; $i < $registros_query_expediente ; $i++){
		    	$linea_expediente 		= pg_fetch_array($fila_query_expediente);  
				$id_expediente 			= $linea_expediente['id_expediente'];
				$nombre_expediente 		= $linea_expediente['nombre_expediente'];
				$creador_expediente 	= $linea_expediente['creador_expediente'];
				$serie 					= $linea_expediente['serie'];
				$subserie 				= $linea_expediente['subserie'];


				if (strpos($expediente, $id_expediente) !== false) { // Validar si el radicado ya se encuentra en un expediente, si ya está, no lo muestra.
			    	$fila_listado_expedientes = "";
				}else{
	    			$fila_listado_expedientes = "<tr onclick='incluir_en_expediente(\"$id_expediente\")' class='detalle'><td class='art_exp'>$id_expediente - $nombre_expediente</td><td class='detalle' style='width:35%'> (creado por $creador_expediente) || serie-subserie ($serie - $subserie)</td></tr>";
	    		}
				echo "$fila_listado_expedientes";
			}
		}
		break;
			
	case 'listado_series':		// Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[cargar_input_codigo_serie_sb()]
		$query_series = "select * from series where activo = 'SI' order by nombre_serie";

		/* Aqui se ejecuta la query */
    	$fila_query_series  		= pg_query($conectado,$query_series);

		/* Se trae las filas de la query */
	   	$registros_query_series 	= pg_num_rows($fila_query_series);

		if($registros_query_series==0){
			echo "<script>alert('No hay series configuradas todavía')</script>";
		}else{				
			echo "<option value=''>-- Seleccione la serie --</option>";	
			for ($i=0; $i < $registros_query_series ; $i++){
		    	$linea_codigo_serie 	= pg_fetch_array($fila_query_series);  
				$codigo_serie 			= $linea_codigo_serie['codigo_serie'];
				$nombre_serie 			= $linea_codigo_serie['nombre_serie'];

				echo "<option value='$codigo_serie'>($codigo_serie) $nombre_serie</option>";
			}
		}				
		break;	

	case 'listado_series2':	
	/*****************************************************************************************
	Inicio funcion para cargar listado de series 
	/*****************************************************************************************
	* @brief Recibe desde funciones_menu.js[consulta_listado_series2(serie_select,codigo_dependencia,nombre_input)]
	* para listar los <option> del select con el listado de series documentales según los parámetros recibidos.
	* @param {string} ($serie_select) Si viene vacío es ignorado, si viene codigo de serie y existe en la dependencia es el <option selected>, si viene codigo de serie pero no existe en la dependencia, agrega el <option> con codigo-nombre de serie y queda como <option selected>
	* @param {string} ($codigo_dependencia) Se usa para filtrar la consulta del listado de series. Si viene vacío la consulta va a retornar el listado completo de series activas. 
	(<select id='codigo_subserie' title='Seleccione el código de la serie documental' class='select_opciones' onchange='validar_serie_subserie()'><option value=''>No hay subseries asociadas a la serie seleccionada</option></select>)
	* @return {string} String con los <option> del listado de series documentales según los parametros recibidos.
	*****************************************************************************************/		
		$codigo_dependencia = $_POST['codigo_dependencia'];
   		$serie_select 		= $_POST['serie_select'];

		/* Si el parametro $codigo_dependencia viene vacío, se consulta el listado de series activas completo. Si tiene valor, se consulta el listado de series de la dependencia $codigo_dependencia */
		if($codigo_dependencia==""){
			$query_series = "select codigo_serie, nombre_serie from series where activo ='SI' order by nombre_serie";
		}else{
		/* Se consulta el listado de series de la dependencia $codigo_dependencia */
			$query_series = "select distinct codigo_serie, nombre_serie from subseries where codigo_dependencia='$codigo_dependencia' and activo = 'SI' order by nombre_serie ";		
		}

		/* Aqui se ejecuta y se trae las filas de la query */
		$fila_query_series  		= pg_query($conectado,$query_series);
	   	$registros_query_series 	= pg_num_rows($fila_query_series);
	   	$listado_series 			= "<option value=''>--- Seleccione una Serie ---</option>";

		if($registros_query_series==0){
			$listado_series = "error";
		}else{				
			$contador_serie_existe = "0";	// Se inicia contador_serie_existente
			/* Se recorre la consulta y se imprime cada uno de los <option> */
			for ($i=0; $i < $registros_query_series ; $i++){
		    	$linea_codigo_serie 	= pg_fetch_array($fila_query_series);

				$codigo_serie1 			= $linea_codigo_serie['codigo_serie'];
				$nombre_serie 			= $linea_codigo_serie['nombre_serie'];

				if($serie_select==$codigo_serie1){
					$listado_series.= "<option value='$codigo_serie1' selected>($codigo_serie1) $nombre_serie</option>";
					$contador_serie_existe = "1";
				}else{
					$listado_series.= "<option value='$codigo_serie1'>($codigo_serie1) $nombre_serie</option>";
				}
			}

			/* Si $serie_select viene vacío es ignorado, si viene codigo de serie pero no existe en la dependencia, agrega el <option> con codigo-nombre de serie y queda como <option selected> */
			if($serie_select!=""){
				if($contador_serie_existe=="0"){ 	// Quiere decir que $serie_select no existe en el listado
					$query_nombre_serie 	= "select * from series where codigo_serie='$serie_select' and activo = 'SI'";
					$fila_nombre_serie  	= pg_query($conectado,$query_nombre_serie);

					if (pg_num_rows($fila_nombre_serie) == 0) {
					   $listado_series = "error";
					}else{
						$linea_nombre_serie 	= pg_fetch_array($fila_nombre_serie);
						$nombre_serie_select 	= $linea_nombre_serie['nombre_serie'];
						$listado_series.= "<option value='$serie_select' selected>($serie_select) $nombre_serie_select</option>";	
					}
				}
			}

		}				
		echo $listado_series;

	/* Fin funcion para cargar listado de series */
	/*****************************************************************************************/
		break;

	case 'listado_subseries2':
	/*****************************************************************************************
	Inicio funcion para cargar listado de subseries */
	/*****************************************************************************************
	* @brief Recibe desde funciones_menu.js[cargar_codigo_subserie2(codigo_serie,codigo_subserie,codigo_dependencia,formulario,nombre_input)]
	* para listar los <option> de las subseries documentales según los parametros recibidos.
	* @param {string} ($codigo_serie) Es obligatorio, si viene vacío retorna error
	* @param {string} ($codigo_subserie1) Si viene vacío trae en los <option> el listado de subseries de la dependencia y serie que vienen como parametros. Si viene codigo de subserie y existe en la dependencia con el codigo de serie es el <option selected>, si viene codigo de subserie pero no existe la relacion dependencia-codigo_serie-codigo_subserie agrega el <option> con codigo-nombre de subserie y queda como <option selected> 
	* @param {string} ($codigo_dependencia) se usa para filtrar la consulta del listado de subseries. Si viene vacío retorna error. 
	* @param {string} ($formulario) si viene "trd_inventario" cambia la consulta de subseries.
	* @return {string} String con los <option> del listado de subseries documentales según los parametros recibidos.
	*****************************************************************************************/
		require_once('../login/validar_inactividad.php');// Se valida la inactividad 

		$codigo_serie 		= $_POST['codigo_serie'];
		$codigo_subserie1 	= $_POST['codigo_subserie'];
		$codigo_dependencia = $_POST['codigo_dependencia'];
		$formulario 		= $_POST['formulario'];

		/* $codigo_serie Es obligatorio, si viene vacío retorna error */
		if($codigo_serie==""){
			$respuesta_subseries = "error";
		}else{
    		/* Si viene "radicacion_normal" cambia la consulta de subseries. */
    		if($formulario=="radicacion_normal" || $formulario=="radicacion_salida"){
	    		$query_subseries = "select * from subseries where codigo_dependencia='$codigo_dependencia' and codigo_serie='$codigo_serie' and activo = 'SI' order by codigo_dependencia, codigo_serie, codigo_subserie";
    		}else{
	    		$query_subseries = "select distinct codigo_subserie, nombre_subserie from subseries where codigo_serie='$codigo_serie' and activo = 'SI' order by nombre_subserie";
    		}

		/* Aqui se ejecuta la query */
	    	$fila_query_subseries  		= pg_query($conectado,$query_subseries);

		/* Se trae las filas de la query */
		   	$registros_query_subseries 	= pg_num_rows($fila_query_subseries);

			$contador_subserie_existe = "0";	// Se inicia contador_subserie_existente

    		if($registros_query_subseries==0){
    			$respuesta_subseries = "error";
			}else{				
				$respuesta_subseries = "<option value='subserie'>-- Seleccione la subserie --</option>";	
				for ($i=0; $i < $registros_query_subseries ; $i++){
			    	$linea_codigo_subserie 	= pg_fetch_array($fila_query_subseries);  
					$codigo_subserie 		= $linea_codigo_subserie['codigo_subserie'];
					$nombre_subserie 		= $linea_codigo_subserie['nombre_subserie'];

					/* Si viene codigo de subserie y existe en la dependencia con el codigo de serie es el <option selected> */
					if($codigo_subserie==$codigo_subserie1){
						$respuesta_subseries.= "<option value='$codigo_subserie' selected>($codigo_subserie) $nombre_subserie</option>";
						$contador_subserie_existe = "1";
					}else{
						$respuesta_subseries.= "<option value='$codigo_subserie'>($codigo_subserie) $nombre_subserie</option>";
					}
    			}

    			/* Si $codigo_subserie1 viene vacío es ignorado, si viene codigo de subserie pero no existe en la validación dependencia-serie, agrega el <option> con codigo-nombre de serie y queda como <option selected> */
				if($codigo_subserie1!=""){
					if($contador_subserie_existe=="0"){ 	// Quiere decir que $codigo_subserie1 no existe en el listado
						// $query_nombre_serie 	= "select * from subseries where codigo_serie='$serie_select' and activo = 'SI'";
						$query_nombre_subserie 	= "select distinct codigo_subserie, nombre_subserie from subseries where codigo_serie='$codigo_serie' and activo = 'SI' order by nombre_subserie ";
						$fila_nombre_subserie  	= pg_query($conectado,$query_nombre_subserie);

						if (pg_num_rows($fila_nombre_subserie) == 0) {
						   $respuesta_subseries = "error";
						}else{
							$linea_nombre_subserie 	= pg_fetch_array($fila_nombre_subserie);
							$codigo_subserie 	 	= $linea_nombre_subserie['codigo_subserie'];
							$nombre_subserie 	 	= $linea_nombre_subserie['nombre_subserie'];
							$respuesta_subseries.= "<option value='$codigo_subserie' selected>($codigo_subserie) $nombre_subserie</option>";
						}
					}
				}
    		}	
		}

		echo "$respuesta_subseries";			
	/* Fin funcion para cargar listado de subseries */
	/*****************************************************************************************/    		
		break;
	
	case 'listado_usuarios_nuevos':  // Recibe desde bandejas/entrada/transacciones_radicado.php[cargar_nuevo_usuario_derivar_radicado(login)]
		$numero_radicado 			= $_POST['numero_radicado'];
		$tipo_formulario 			= $_POST['tipo_formulario'];
		$usuario_actual_radicado 	= $_POST['usuario_actual_radicado'];
		$usuarios_nuevos 			= $_POST['usuarios_nuevos'];

		switch ($tipo_formulario) {
			case 'derivar_radicado':
				$cerrar_pest ='cerrar_pestana_usuario';
				break;
			case 'cerrar_pestana':
				$cerrar_pest ='cerrar_pestana';
				break;	
			case 'informar_radicado':
				$cerrar_pest ='cerrar_pestana_usuario_inf';
				break;	
			
			default:
				# code...
				break;
		}
		$usuario_actual1="";
		
		$consulta_radicado 		= "select * from radicado where numero_radicado='$numero_radicado'";
		$fila_radicado 	  		= pg_query($conectado,$consulta_radicado);
		$linea_radicado    		= pg_fetch_array($fila_radicado);
		$usuarios_control   	= $linea_radicado['usuarios_control'];
		$codigo_carpeta1 		= $linea_radicado['codigo_carpeta1'];

		$codigo_carpeta2 		= json_decode($codigo_carpeta1,true);		

		// Extraigo cada uno de los usuarios_control	
		$usu2  = explode(",", $usuarios_control);
		$max3  = sizeof($usu2);
		$max4 = $max3-1;

		for ($r=0; $r < $max4; $r++) { // Quito el usuario_derivado del listado de usuarios_nuevos
			$login_usuario_deri = $usu2[$r];
			$usuarios_nuevos = str_replace($login_usuario_deri.",", "", $usuarios_nuevos);
		}

		// Extraigo cada uno de los usuarios_actuales	
		$usu  = explode(",", $usuarios_nuevos);
		$max  = sizeof($usu);
		$max2 = $max-1;

		$login_usuario_busq = $usuarios_nuevos;

		for ($q=0; $q < $max2; $q++) {  // Genera restricción de usuarios que ya existen.
			$login_usuario_busq = $usu[$q];

			$consulta_usuario_busq 	= "select * from usuarios u join dependencias d on u.codigo_dependencia=d.codigo_dependencia where u.login!='$usuario_actual_radicado' and u.login='$login_usuario_busq'";

			$fila_usuario_busq 	  				= pg_query($conectado,$consulta_usuario_busq);
			$linea_usuario_busq    				= pg_fetch_array($fila_usuario_busq);
			$login_usuario_busq   				= $linea_usuario_busq['login'];
			$nombre_usuario_busq   				= $linea_usuario_busq['nombre_completo'];
			$path_foto_usuario_busq 			= $linea_usuario_busq['path_foto'];
			$nombre_dependencia_usuario_busq  	= $linea_usuario_busq['nombre_dependencia'];

			$nombre_usuario_busq2 = substr($nombre_usuario_busq,0,20);
			$nombre_usuario_busq1 = $nombre_usuario_busq2."...";

			$usuario_actual1.="
				<div class='lista_destinatario'>
					<img src='$path_foto_usuario_busq' style='width: 30px; height: 30px; border-radius: 20px;'>
					<div class='tab_a nombre_destinatario' onclick=\"agregar_usuario_destino('$login_usuario_busq')\" >
						$nombre_usuario_busq1 
					</div>
					<div class='boton_cerrar_usuario' title='Quitar éste usuario del radicado'>
						<img src='imagenes/iconos/cerrar.png' width='20px' class='img_cerrar' onclick=\"$cerrar_pest('$login_usuario_busq')\">
					</div>
				
					<div class='li'>
						<table style='width: 400px; border: #2D9DC6 2px solid; border-radius:15px;'>	
							<tr>
								<td rowspan=2 width='1%'>
									<img src='$path_foto_usuario_busq' style='width: 50px;border-radius: 10px;'> 
								</td>
								<td width='39%'>
									$nombre_usuario_busq <br>($login_usuario_busq)
								</td>
							</tr>
								<td >
									<b>$nombre_dependencia_usuario_busq</b>
								</td>
							<tr>
							</tr>
						</table>
					</div>
					
				</div>";					
		}		
		echo "$usuario_actual1";
		
		break;
	/*****************************************************************************************
		Case listar_serie_subserie_radicacion_interna estructura las opciones del select con la información de las series y subseries para el radicado interno
	/*****************************************************************************************
	* @brief Recibe desde include/js/funciones_radicacion_interna.js[function listar_serie_subserie()]
	* @param {string} (tipo_select) Es obligatoria, si necesita en listar las series  el tipo es 1 o las subseries el tipo es 2
	* ¿@param {string} (serie_subserie_existente) No es obligatoria, si el formulario es para editar este es el valor de la serie o subserie para seleccionar en los diferentes option
	* @param {string} (serie_subserie_seleccionado) Es obligatoria si se en lista la subserie ya que toma la serie padre
	* @return {string} Información de las series y subseries en listados concatenando para generar los option del select
	*****************************************************************************************/	
	case 'listar_serie_subserie_radicacion_interna':
		if(!isset($_SESSION)){
			session_start();
  		}
		switch($_POST['tipo_serie_o_subserie']){
			case "1":// Tipo 1 = serie
				$select 	= "serie";
				$where  	= "and codigo_dependencia='".$_SESSION['dependencia']."'";// Parte de la consulta sql
				break;
			case "2":// Tipo 2 = subserie
				$select 	= "subserie";
				$where 		= "and codigo_serie='".$_POST['serie_subserie_seleccionado']."'";// Parte de la consulta sql
				break;
		}
		$consulta_sql 	= "select
								distinct codigo_$select,
								nombre_".$select."
							from
								subseries
							where
								activo = 'SI'
								".$where."
							order by
								nombre_".$select;// Estructura sql
		$datos_sql  	  = pg_query($conectado,$consulta_sql);// Se enviá la consulta sql a la base de datos
		$registros_sql 	  = pg_num_rows($datos_sql);// Con pg_num_rows sacamos el numero de registros encontrados en la consulta
		if($registros_sql == 0){// Valida si no se ha encontrado registros
			$listado_sql  = "<option value='' disabled selected>
									No hay ".$select." disponible
								</option>";// Estrucutra html
		}else{
			$listado_sql = "<option value='' disabled selected>
									--- Seleccione una ".$select." ---
								</option>";// Estrucutra html
			$codigo_tomar 	= "codigo_".$select;// Se crea el string completo para tomar un dato de los registros
			$nombre_tomar 	= "nombre_".$select;// Se crea el string completo para tomar un dato de los registros
			for ($i = 0; $i < $registros_sql; $i++){// El bucle se repetira hasta que $i sea menor que $registros_sql
		    	$linea_sql  = pg_fetch_array($datos_sql);// Los registros obtenidos se pasan a un array
				$codigo 	= $linea_sql[$codigo_tomar];// Se toma un dato de los registros
				$nombre 	= $linea_sql[$nombre_tomar];// Se toma un dato de los registros
				/* Si el radicado ya existe y sera modificado se da el selected al correpsondiente option que corresponda con el registero de la base de datos */
				if(isset($_POST['serie_subserie_existente'])){// Valida si la variable esta definida y no es null
					if($_POST['serie_subserie_existente'] == $codigo){// Valida si las variable son iguales
						$listado_sql .= "<option id='opciones_serie_subserie' value='".$codigo."' selected>
												(".$codigo.") ".$nombre."
											</option>";// Estrucutra html
					}else{
						$listado_sql .= "<option id='opciones_serie_subserie' value='".$codigo."'>
													(".$codigo.") ".$nombre."
												</option>";// Estrucutra html
					}
				}
				/* Fin si el radicado ya existe y sera modificado se da el selected al correpsondiente option que corresponda con el registero de la base de datos */
			}
		}			
		echo $listado_sql;	
		break;
	/*****************************************************************************************
		Fin case listar_serie_subserie_radicacion_interna estructura las opciones del select con la informacion de las series y subseries para el radicado interno
	/*****************************************************************************************/
	case 'modificar_serie': 	// Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function enviar_modificar_serie()]
		$codigo_serie = $_POST['codigo_serie'];
		$nombre_serie = $_POST['nombre_serie'];
		$serie_activa = $_POST['serie_activa'];

	    $query_modificar_serie= "update series set nombre_serie='$nombre_serie', activo='$serie_activa' where codigo_serie='$codigo_serie'"; // Query para modificar serie en tabla serie
	    $query_modificar_subserie = "update subseries set nombre_serie='$nombre_serie' where codigo_serie='$codigo_serie'";	// Query para modificar serie en tabla subserie	

		if(pg_query($conectado,$query_modificar_serie)){
    		if(pg_query($conectado,$query_modificar_subserie)){
				echo "<script> 
					auditoria_general('modificar_serie','$nombre_serie');	
				</script>";
    		}else{
    			echo "<script>
				alert('No se pudo modificar las subseries. Comuníquese con el administrador del sistema');
				volver();
			</script>";
    		}
		}else{
			echo "<script>
				alert('No se pudo modificar la serie. Comuníquese con el administrador del sistema');
				volver();
			</script>";
		}
		break;
	
	case 'modificar_subserie': 	// Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function enviar_modificar_subserie()]
		
		$id 							= $_POST['id'];
		$codigo_dependencia 			= $_POST['codigo_dependencia'];
		$codigo_serie 					= $_POST['codigo_serie'];
		$nombre_serie 					= $_POST['nombre_serie'];
		$codigo_subserie 				= $_POST['codigo_subserie'];
		$nombre_subserie 				= $_POST['nombre_subserie'];
		$tiempo_archivo_gestion 		= $_POST['tiempo_archivo_gestion'];
		$tiempo_archivo_central 		= $_POST['tiempo_archivo_central'];
		$soporte_papel 					= $_POST['soporte_papel'];
		$soporte_electronico 			= $_POST['soporte_electronico'];
		$eliminacion 					= $_POST['eliminacion'];
		$seleccion 						= $_POST['seleccion'];
		$conservacion_total 			= $_POST['conservacion_total'];
		$microfilmacion_digitalizacion 	= $_POST['microfilmacion_digitalizacion'];
		$procedimiento 					= $_POST['procedimiento'];
		$activo 						= $_POST['activo'];

	    $query_modificar_subserie= "update subseries set codigo_dependencia ='$codigo_dependencia', codigo_serie ='$codigo_serie', nombre_serie='$nombre_serie', codigo_subserie ='$codigo_subserie', nombre_subserie ='$nombre_subserie', tiempo_archivo_gestion ='$tiempo_archivo_gestion', tiempo_archivo_central ='$tiempo_archivo_central', soporte_papel ='$soporte_papel', soporte_electronico ='$soporte_electronico', eliminacion ='$eliminacion', seleccion ='$seleccion', conservacion_total ='$conservacion_total', microfilmacion_digitalizacion ='$microfilmacion_digitalizacion', procedimiento ='$procedimiento', activo ='$activo' where id='$id'";
	   
		if(pg_query($conectado,$query_modificar_subserie)){
			echo "<script> 
				auditoria_general('modificar_subserie','$nombre_subserie');	
			</script>";
		}else{
			echo "<script>
				alert('No se pudo modificar la subserie. Comuníquese con el administrador del sistema');
				volver();
			</script>";
		}
		break;

	case 'muestra_termino': 	// Recibe desde include/js/funciones_radicacion_entrada.js[function muestra_termino()]
		$tipo_documento = $_POST['tipo_documento'];

		$query_tipo_doc_termino = "select * from tipo_doc_termino where activo ='SI' and tipo_documento='$tipo_documento' union select * from tipo_doc_termino_pqr where activo ='SI' AND tipo_documento='$tipo_documento' order by tipo_documento";
		$fila_tipo_documento  	= pg_query($conectado,$query_tipo_doc_termino); 
		$linea_tipo_documento 	= pg_fetch_array($fila_tipo_documento);
        $tiempo_tramite			= $linea_tipo_documento['tiempo_tramite'];
        echo "$tiempo_tramite";

		break;

	case 'nombre_serie': 		// Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function cargar_nombre_serie_sb(valor)]
		$codigo_serie = $_POST['codigo_serie'];
		$query_nombre_serie = "select * from series where codigo_serie='$codigo_serie'";

		$fila_nombre_serie  = pg_query($conectado,$query_nombre_serie); 
		$linea_nombre_serie = pg_fetch_array($fila_nombre_serie);
        $nombre_serie		= $linea_nombre_serie['nombre_serie'];

        echo "$nombre_serie";
		break;		

	case 'pestana_cuadro_clasificacion': // Recibe desde cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php[function abrir_pestana(evt,value)]
		$pestana=$_POST['pestana'];

		switch ($pestana) {
			case 'trd':
				$query_dependencia ="select * from dependencias where id_dependencia!=1 order by nombre_dependencia";

				 /* Aqui se ejecuta la query */
	    		$fila_query_dependencia  		= pg_query($conectado,$query_dependencia);

	    		/* Se trae las filas de la query */
	    		$registros_query_dependencia 	= pg_num_rows($fila_query_dependencia);

	    		/*Se trae las filas de la query*/

	    		if($registros_query_dependencia==0){
					echo "<script>alert('No hay dependencias configuradas todavía')</script>";
				}else{					
    				echo "
    				Dependencia 
    				<select onchange='ccd_por_dependencia(this.value)'>";

						echo"<option value='todos'> -- Todas las Dependencias --</option>";
					for ($i=0; $i < $registros_query_dependencia ; $i++){
				    	$linea_codigo_dependencia 	= pg_fetch_array($fila_query_dependencia);  
						$codigo_dependencia 		= $linea_codigo_dependencia['codigo_dependencia'];
						$nombre_dependencia 		= $linea_codigo_dependencia['nombre_dependencia'];

						echo"<option value='$codigo_dependencia'>( $codigo_dependencia ) $nombre_dependencia</option>";
				    } 
    				echo "
    				</select>";
    				echo "
    				<div id='resultado_ccd_dependencia'></div>";
				}
				echo "
	    		<br>
	    		<table width='100%'>
	    			<tr>
	    				<td colspan='3'><b>CONVENCIONES</b></td>
	    			</tr>
	    			<tr>
	    				<td width='2%'></td>
	    				<td width='2%'><b>AC</b></td><td width='28%'> : Archivo Central</td>
	    				<td width='2%'><b>AG</b></td><td width='28%'> : Archivo de Gestión</td>
	    				<td width='2%'><b>CT</b></td><td width='36%'> : Conservación Total</td>
	    			</tr>
	    			<tr>
	    				<td></td>
	    				<td><b>D</b></td><td> : Código de la Dependencia</td>
	    				<td><b>E</b></td><td> : Eliminación</td>
	    				<td><b>EL</b></td><td> : Soporte Electrónico</td>
	    			</tr>
	    			<tr>
	    				<td></td>
	    				<td><b>MD</b></td><td> : Microfilmación o Digitalización</td>
	    				<td><b>P</b></td><td> : Soporte Papel</td>
	    				<td><b>S</b></td><td> : Código Serie</td>
	    			</tr>
	    			<tr>
	    				<td></td>
	    				<td><b>Sb</b></td><td> : Código Subserie</td>
	    				<td><b>SE</b></td><td> : Selección</td>
	    			</tr>
	    		<table>
	    		<br><hr>
	    		";
				echo "<script>ccd_por_dependencia('todos')</script>";
				
				break;
			
			case 'serie':
				echo "<h1>Listado de Series Documentales</h1><br>";

    		/*Esta es la query*/
    			$query_series = "select * from SERIES ORDER BY NOMBRE_SERIE";

    		/*Aqui se ejecuta la query*/
    			$fila_consulta_series  = pg_query($conectado,$query_series);

    		/*Se trae la cantidad de filas de la query*/
    			$registros_consulta_series = pg_num_rows($fila_consulta_series);
    			
	    		if($registros_consulta_series==0){
					echo "<h2 class='center'>No existen series todavía. </h2";
				}else{
					echo "
					<center style='overflow-x:scroll'>
						<table border='1' width='100%'>
							<tr class='center'>
								<td class='descripcion' width='20%'>Código de la Serie</td>
								<td class='descripcion' width='60%'>Nombre de la Serie</td>
								<td class='descripcion' width='20%'>Activo</td>
							</tr>	
								";
							
		    		//se imprime las filas con bucle for
		    		for ($i=0; $i < $registros_consulta_series ; $i++){

				    	$linea_consulta_series = pg_fetch_array($fila_consulta_series);  
			
						$codigo_serie 	= $linea_consulta_series['codigo_serie'];
						$nombre_serie 	= $linea_consulta_series['nombre_serie'];
						$activo		 	= $linea_consulta_series['activo'];
						
						echo "
						<tr class='detalle center fila_serie' onclick=\"modificar_serie('$codigo_serie','$nombre_serie','$activo')\" title='Click para modificar serie'>								
							<td> $codigo_serie </td>
							<td> $nombre_serie </td>
							<td> $activo </td>
						</tr>";

				    } 
				    echo "</table><br>";
				}
				echo "<h2>Para agregar una serie nueva haga click <a style='text-decoration: none;color: red;' href='javascript:mostrar_ventana_crear_serie();'>aquí</a></h2>";

				break;
			case 'subserie':
	    		echo "<h1>Listado de Subseries Documentales</h1><br>";	
					/*Esta es la query*/
    			$query_subseries = "select * from subseries order by codigo_dependencia,nombre_serie,nombre_subserie";

    		/*Aqui se ejecuta la query*/
    			$fila_consulta_subseries  = pg_query($conectado,$query_subseries);

    		/*Se trae la cantidad de filas de la query*/
    			$registros_consulta_subseries = pg_num_rows($fila_consulta_subseries);
    			
	    		if($registros_consulta_subseries==0){
					echo "<h2 class='center'>No existen subseries todavía. </h2";
				}else{
					echo "
					<center style='overflow-x:scroll'>
						<table border='1' width='100%'>
							<tr class='center'>
								<td class='descripcion'>Código de la Dependencia</td>
								<td class='descripcion'>Código de la Serie</td>
								<td class='descripcion'>Nombre de la Serie</td>
								<td class='descripcion'>Código de la Subserie</td>
								<td class='descripcion'>Nombre de la Subserie</td>
								<td class='descripcion'>Tiempo Archivo Gestión</td>
								<td class='descripcion'>Tiempo Archivo Central</td>
								<td class='descripcion'>Soporte Papel</td>
								<td class='descripcion'>Soporte Electrónico</td>
								<td class='descripcion'>Eliminación</td>
								<td class='descripcion'>Selección</td>
								<td class='descripcion'>Conservación Total</td>
								<td class='descripcion'>Microfilmación<br>ó Digitalización</td>
								<td class='descripcion'>Procedimiento</td>
								<td class='descripcion'>Activo</td>
							</tr>	
								";
							
		    		//se imprime las filas con bucle for
		    		for ($i=0; $i < $registros_consulta_subseries ; $i++){

				    	$linea_consulta_subseries = pg_fetch_array($fila_consulta_subseries);  
			
						$id 							= $linea_consulta_subseries['id'];
						$codigo_dependencia 			= $linea_consulta_subseries['codigo_dependencia'];
						$codigo_serie 					= $linea_consulta_subseries['codigo_serie'];
						$nombre_serie 					= $linea_consulta_subseries['nombre_serie'];
						$codigo_subserie 				= $linea_consulta_subseries['codigo_subserie'];
						$nombre_subserie 				= $linea_consulta_subseries['nombre_subserie'];
						$tiempo_archivo_gestion		 	= $linea_consulta_subseries['tiempo_archivo_gestion'];
						$tiempo_archivo_central		 	= $linea_consulta_subseries['tiempo_archivo_central'];
						$soporte_papel				 	= $linea_consulta_subseries['soporte_papel'];
						$soporte_electronico		 	= $linea_consulta_subseries['soporte_electronico'];
						$eliminacion				 	= $linea_consulta_subseries['eliminacion'];
						$seleccion		 				= $linea_consulta_subseries['seleccion'];
						$conservacion_total		 		= $linea_consulta_subseries['conservacion_total'];
						$microfilmacion_digitalizacion	= $linea_consulta_subseries['microfilmacion_digitalizacion'];
						$procedimiento		 			= $linea_consulta_subseries['procedimiento'];
						$activo		 					= $linea_consulta_subseries['activo'];
						
						echo "
						<tr class='detalle center fila_serie' onclick=\"modificar_subserie('$id','$codigo_dependencia','$codigo_serie','$nombre_serie','$codigo_subserie','$nombre_subserie','$tiempo_archivo_gestion','$tiempo_archivo_central','$soporte_papel','$soporte_electronico','$eliminacion','$seleccion','$conservacion_total','$microfilmacion_digitalizacion','$procedimiento','$activo')\" title='Click para modificar subserie'>								
							<td> $codigo_dependencia </td>
							<td> $codigo_serie </td>
							<td> $nombre_serie </td>
							<td> $codigo_subserie </td>
							<td> $nombre_subserie </td>
							<td> $tiempo_archivo_gestion </td>
							<td> $tiempo_archivo_central </td>
							<td> $soporte_papel </td>
							<td> $soporte_electronico </td>
							<td> $eliminacion </td>
							<td> $seleccion </td>
							<td> $conservacion_total </td>
							<td> $microfilmacion_digitalizacion </td>
							<td> $procedimiento </td>
							<td> $activo </td>
						</tr>";

				    } 
				    echo "</table><br>";
				}
				echo "<h2>Para agregar una subserie nueva haga click <a style='text-decoration: none;color: red;' href='javascript:mostrar_ventana_crear_subserie();'>aquí</a></h2>";
			break;	
		}
		
		break;
	case 'radicacion_normal': // Recibe desde radicacion/radicacion_interna/index_normal.php[function insertar_radicacion_normal()]
		$asunto_radicado 	= $_POST['asunto_radicado'];
		$codigo_dependencia = $_POST['codigo_dependencia'];
		$codigo_serie 		= $_POST['codigo_serie'];
		$codigo_subserie 	= $_POST['codigo_subserie'];
		$id_expediente 		= $_POST['id_expediente'];

		$id_expediente1 	= $id_expediente.",";

		$target_file= basename($_FILES["archivo_pdf_radicado"]["name"]); // Nombre que trae el archivo

		if($target_file != ""){	// Si llega archivo pdf		

		/* Desde aqui defino variables para el archivo validar_consecutivo.php */
			$tipo_radicado 				= 3; 	// Tipo de radicado (3- Interna, 2- Salida, 1- Entrada, etc)
			// $year 						= date("Y"); 			// Se obtiene el año en formato 4 digitos 
		/* Hasta aqui defino variables para el archivo validar_consecutivo.php */
			
			require_once('../login/validar_consecutivo.php'); // Valida si el consecutivo existe y genera el radicado
			
			$usuario_destino 			= $login_usuario.","; 	// Inicialmente solo es el usuario que genera el radicado

			// $path_file="bodega_pdf/radicados/$target_file";
			$target_dir="../bodega_pdf/radicados/";

		//	$target_file2=str_replace(" ", "", $target_file);
			$numero_radicado  = $radicado;
			$numero_radicado1 = $numero_radicado.",";
			$path_file 		  = "$year/$mes/$numero_radicado.pdf";				

			$query_consultar_expediente ="select * from expedientes where id_expediente='$id_expediente'";
			$fila_consultar_expediente  = pg_query($conectado,$query_consultar_expediente);
			$linea_consultar_expediente = pg_fetch_array($fila_consultar_expediente);  	
			$lista_radicados 	= $linea_consultar_expediente['lista_radicados'];

			/* De la lista de radicados extraigo uno a uno los registros y los consulto en la tabla radicado */ 
			$radicado_separado  = explode(",", $lista_radicados);
			$max  				= sizeof($radicado_separado);
			$max2 				= $max-1;

			if($max2==0){ // No hay ningún radicado en lista_radicados de expediente
				$numero_radicado2 = $numero_radicado1; 
			}else{
				if (strpos($lista_radicados, $numero_radicado)!== false) {
				    $numero_radicado2 = $lista_radicados; // Si el radicado existe en la lista_radicados
				}else{
					$numero_radicado2 = $lista_radicados.$numero_radicado1;
				}
				
			}

			// Se arma el json para codigo_carpeta1 de la tabla radicado
			$codigo_carpeta1="'{\"$login_usuario\":{\"codigo_carpeta_personal\":\"entrada\"}}'";
			// Fin del armado del json para codigo_carpeta1 de la tabla radicado

			if(move_uploaded_file($_FILES["archivo_pdf_radicado"]["tmp_name"],$target_dir.$path_file)){

				$query_radicado="insert into radicado(numero_radicado, fecha_radicado, path_radicado, dependencia_actual, usuarios_visor, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, id_expediente, codigo_serie, codigo_subserie, codigo_carpeta1, estado_radicado, usuarios_control) 
					values('$radicado', '$timestamp', '$path_file', '$codigo_dependencia', '$usuario_destino', '$codigo_dependencia', '$login_usuario', '$asunto_radicado', '$nivel', '$usuario_destino', '$id_expediente1', '$codigo_serie', '$codigo_subserie', $codigo_carpeta1,'en_tramite', '$usuario_destino')";

				$query_expediente ="update expedientes set lista_radicados='$numero_radicado2' where id_expediente='$id_expediente'";

				if(pg_query($conectado,$query_radicado)){ 
					if(pg_query($conectado,$query_expediente)){
					/* Desde aqui se genera historico */	
						$transaccion_historico 	= "Radicacion Normal Interna";	// Variable para tabla historico_eventos
						$comentario				= "Documento ingresado como radicación interna normal";					// Variable para historico eventos

						$transaccion 			= "radicacion_normal"; 	// Variable para auditoria
						$creado 				= "$radicado";			// Variable para auditoria
						require_once("../login/inserta_historico.php");		
					/* Hasta aqui se genera historico */	
					}else{
						echo "<script> alert('Ocurrió un error al realizar la actualización del expediente, por favor revisar e intentar nuevamente.')</script>";
					}
				}else{
					echo "<script> alert('Ocurrió un error al realizar la radicación normal, por favor revisar e intentar nuevamente.')</script>";
				}
			}else{ // Si hubo error al cargar el archivo recibido archivo_pdf_radicado
				$error_upload_files=$_FILES["archivo_pdf_radicado"]["error"];

				switch ($error_upload_files) {
					case '0':
						$error_file="There is no error, the file uploaded with success";
						break;
					case '1':
						$error_file="The uploaded file exceeds the upload_max_filesize directive in php.ini";
						break;	
					case '2':
						$error_file="The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
						break;	
					case '3':
						$error_file="The uploaded file was only partially uploaded";
						break;	
					case '4':
						$error_file="No file was uploaded";
						break;				
					case '6':
						$error_file="Missing a temporary folder";
						break;	
					case '7':
						$error_file="Failed to write file to disk.";
						break;	
					case '8':
						$error_file="A PHP extension stopped the file upload.";
						break;	
				}	// Final del switch $error_upload_files
				echo "No se puede cargar el archivo_pdf_radicado - $error_file";
			}	
		}	
		break;

	case 'reasignar_radicado':
		require_once('../login/validar_inactividad.php');// Se valida la inactividad 
		// session_start();
		$mensaje_reasignar 	= $_POST['mensaje_reasignar'];
		$radicado 			= $_POST['numero_radicado'];
		$usuario_destino 	= $_POST['usuario_destino'];

		/* En $usuario_destino vienen por ejemplo 900,ALUMNO1,imagenes/fotos_usuarios/admin.png */
		$porciones = explode(",", $usuario_destino);
		$codigo_dependencia = $porciones[0]; // Codigo dependencia del usuario destino
		$usuario_destino 	= $porciones[1]; // Login usuario_destino

		$query_radicado 	= "select * from radicado where numero_radicado='$radicado'";
		$fila_radicado  	= pg_query($conectado,$query_radicado);
		$linea_radicado 	= pg_fetch_array($fila_radicado);  	
		
		$usuarios_control 	= $linea_radicado['usuarios_control'];
		$usuarios_visor 	= $linea_radicado['usuarios_visor'];

		$usuario 	= $_SESSION['login']; 		// Genera Usuario 

		// Validar si $usuario se encuentra entre los usuarios_control
		if (strlen(strstr($usuarios_control,$usuario_destino))==0){
			$usuarios_control.="$usuario_destino,";
		}
		/* Como reasigna y debe ceder el control del radicado, se quita de los $usuarios_control */
		$usuarios_control = str_replace("$usuario,", "", $usuarios_control);

		// Validar si $usuario se encuentra entre los usuarios_visor
		if (strlen(strstr($usuarios_visor,$usuario_destino))==0){ 
			$usuarios_visor.="$usuario_destino,";
		}
	
		$codigo_carpeta1 	= $linea_radicado['codigo_carpeta1'];

		$codigo_carpeta2 					= json_decode($codigo_carpeta1,true);		
		$codigo_carpeta2[$usuario_destino] 	= array('codigo_carpeta_personal'=>'entrada');
		$codigo_carpeta2[$usuario] 			= array('codigo_carpeta_personal'=>'Salida');

		$codigo_carpeta3 					= json_encode($codigo_carpeta2);
		// Fin del armado del json para codigo_carpeta1 de la tabla radicado

		$query_enviados = "insert into enviados(numero_radicado,usuario_origen,usuario_destino,fecha,comentario)values('$radicado','$usuario','$usuario_destino','$timestamp','$mensaje_reasignar');";

		$query_reasignar = "update radicado set usuarios_control='$usuarios_control', codigo_carpeta1='$codigo_carpeta3', leido='$usuarios_visor', usuarios_visor='$usuarios_visor', dependencia_actual='$codigo_dependencia' where numero_radicado='$radicado';";

		if(pg_query($conectado,$query_reasignar.$query_enviados)){
		/* Desde aqui se genera historico */	
			$transaccion_historico 	= "Se reasigna radicado al usuario $usuario_destino";	// Variable para tabla historico_eventos
			$comentario				= $mensaje_reasignar; // Variable para historico eventos

			$transaccion 			= "reasignar_radicado"; // Variable para auditoria
			$creado 				= "$radicado";			// Variable para auditoria
			require_once("../login/inserta_historico.php");		
		/* Hasta aqui se genera historico */	
		}else{
			echo "<script> alert('Ocurrió un error al reasignar el documento, por favor revisar e intentar nuevamente.')</script>";
		}
		break;

	case 'reporte1':
		session_start();
		$fecha_inicial 	= $_POST['fecha_inicial'];
		$fecha_final 	= $_POST['fecha_final'];

		$fecha_inicio 	= "$fecha_inicial 00:00:00";
		$fecha_fin 		= "$fecha_final 23:59:59";

		$dependencia 		= $_SESSION['dependencia']; 		// Genera Dependencia del usuario 

	   	$query_reporte1 = "select * from radicado r join dependencias de on de.codigo_dependencia = r.dependencia_actual 
		join datos_origen_radicado d on r.numero_radicado=d.numero_radicado
	   	where dependencia_radicador = '$dependencia' and fecha_radicado between '$fecha_inicio' and '$fecha_fin' order by dependencia_actual, fecha_radicado asc";

	/*Aqui se ejecuta la query*/
		$fila_reporte1  = pg_query($conectado,$query_reporte1);

    /*Se trae la cantidad de filas de la query*/
    	$registros_reporte1 = pg_num_rows($fila_reporte1);
    			
		if($registros_reporte1==0){
			echo "<h3>No hay radicados recibidos en el rango de fechas desde el $fecha_inicial hasta el $fecha_final</h3>";
		}else{
			$encabezado_tabla_oficio="<table border='0'><tr class='center'><td class='descripcion'>Id</td><td class='descripcion'>Numero Radicado</td><td class='descripcion'>Fecha Radicación</td><td class='descripcion'>Remitente</td><td class='descripcion'>Asunto</td><td class='descripcion'>Área Destino</td><td class='descripcion'>Funcionario Destino</td><td class='descripcion'>Imagen PDF</td></tr>";
			$encabezado_tabla_pqr="<h1>Reporte de Entrega PQR</h1><table border='0'><tr class='center'><td class='descripcion'>Id</td><td class='descripcion'>Numero Radicado</td><td class='descripcion'>Fecha Radicación</td><td class='descripcion'>Remitente</td><td class='descripcion'>Asunto</td><td class='descripcion'>Área Destino</td><td class='descripcion'>Funcionario Destino</td><td class='descripcion'>Tipo PQR</td><td class='descripcion'>Imagen PDF</td></tr>";

			$tabla_oficios 	= "";
			$tabla_pqr 		= "";
			$j  			= 1;
			$k  			= 1;

			for ($i=0; $i < $registros_reporte1 ; $i++){
				$linea_reporte1 = pg_fetch_array($fila_reporte1);

				$numero_radicado 				= $linea_reporte1['numero_radicado'];
				$fecha_radicado 				= $linea_reporte1['fecha_radicado'];
				$nombre_remitente_destinatario 	= $linea_reporte1['nombre_remitente_destinatario'];
				$dignatario 					= $linea_reporte1['dignatario'];
				$asunto 						= $linea_reporte1['asunto'];
				$dependencia_actual 			= $linea_reporte1['dependencia_actual'];
				$nombre_dependencia 			= $linea_reporte1['nombre_dependencia'];
				$clasificacion_radicado 		= $linea_reporte1['clasificacion_radicado'];
				$path_radicado 					= $linea_reporte1['path_radicado'];

				if($path_radicado==""){
					$imagen_existe = "NO";
				}else{
					$imagen_existe = "SI";
				}

				/* Se consultan los tipos de PQR que tiene el sistema configurado */
				$query_tipo_pqr = "select * from tipo_doc_termino_pqr";

				/*Aqui se ejecuta la query*/
				$fila_query_tipo_pqr = pg_query($conectado,$query_tipo_pqr);

		    /*Se trae la cantidad de filas de la query*/
		    	$registros_query_tipo_pqr = pg_num_rows($fila_query_tipo_pqr);
		    		
				$case_pqr = "";
		    	for ($h=0 ; $h<$registros_query_tipo_pqr ; $h++){
					$linea_query_tipo_pqr 	= pg_fetch_array($fila_query_tipo_pqr);
					$tipo_documento 		= $linea_query_tipo_pqr['tipo_documento'];

					$case_pqr.="$tipo_documento , ";
		    	}	

		    	/* Busca en los tipos de PQR si está la clasificación del radicado para generar planilla PQR aparte */
		    	if(strpos($case_pqr, $clasificacion_radicado)){
		    		$tabla_pqr .= "<tr>
						<td class='detalle center'>$k</td>
						<td class='detalle'>$numero_radicado</td>
						<td class='detalle'>$fecha_radicado</td>
						<td class='detalle'>$nombre_remitente_destinatario - $dignatario</td>
						<td class='detalle'>$asunto</td>
						<td class='detalle'>$dependencia_actual<br>($nombre_dependencia)</td>
						<td class='detalle'></td>
						<td class='detalle'>$clasificacion_radicado</td>
						<td class='detalle center'>$imagen_existe</td>
					</tr>";
					$k++;
		    	}else{
					$tabla_oficios .= "<tr>
						<td class='detalle center'>$j</td>
						<td class='detalle'>$numero_radicado</td>
						<td class='detalle'>$fecha_radicado</td>
						<td class='detalle'>$nombre_remitente_destinatario - $dignatario</td>
						<td class='detalle'>$asunto</td>
						<td class='detalle'>$dependencia_actual<br>($nombre_dependencia)</td>
						<td class='detalle'></td>
						<td class='detalle center'>$imagen_existe</td>
					</tr>";
					$j++;
		    	}
		    } 
		    if($k==1){
			    echo "$encabezado_tabla_oficio $tabla_oficios</table>";
		    }else{
			    echo "$encabezado_tabla_oficio $tabla_oficios</table> <br><br>$encabezado_tabla_pqr $tabla_pqr</table>";
		    }
		    $codigo_entidad = $_SESSION['codigo_entidad'];

		  /*  echo "<tr>	
				<td colspan='3'>
				<td>
					<center>
						<a href='formatos/reporte_excel.php?codigo_entidad=$codigo_entidad&nombre_reporte=reporte1_entrega_correspondencia_entrada&sql=".urlencode($query_reporte1)."' style='text-decoration:none;'>
							<img src='imagenes/iconos/logo_excel.png' width='30' height='25' title='Exportar a excel' style='padding : 10px;'>
						</a> 
						<a href='formatos/reporte_pdf.php?codigo_entidad=$codigo_entidad&nombre_reporte=reporte1_entrega_correspondencia_entrada&sql=".urlencode($query_reporte1)."' style='text-decoration:none;'><img src='imagenes/iconos/archivo_pdf.png' width='30' height='25' title='Exportar a PDF' style='padding : 10px; text-decoration:none;'>
						</a>
					</center>
				</td>
				<td colspan='5'></td>
			</tr>
			";*/
    		echo "</table>"; 
		}
	break;
	/*****************************************************************************************
	Inicio Case reporte2 para cargar la tabla de radicados con base a filtros de fecha, tipo radicado, y dependencia
	/*****************************************************************************************
	* @brief Recibe desde 
	* - reportes/reporte2_radicados_entrada.php.php[function cargar_reporte_2()]
	* para listar mediante <table> los radicados que existen en la base de datos según los parámetros recibidos.
	* @param {string} ($fecha_inicial_reporte2) Es obligatorio y se usa para filtrar la consulta.
	* @param {string} ($fecha_final_reporte2) Es obligatorio y se usa para filtrar la consulta.
	* @param {string} ($dependencia_reporte2) No es obligatorio y se usa para filtrar la consulta.
	* @return {string} String con la <table> de los datos en la tabla radicado listado por filtros
	*****************************************************************************************/	
	case 'reporte2':
		require_once('../login/validar_inactividad.php');// Se valida la inactividad 

		/* Recibe variables desde la funcion cargar_reporte_2() desde reporte2_radicados_entrada.php */
		$fecha_inicial_reporte2     = $_POST['fecha_inicial_reporte2'];
		$fecha_final_reporte2 	    = $_POST['fecha_final_reporte2'];
		$dependencia_reporte2 		= $_POST['select_dependencia_reporte2'];

		if($fecha_inicial_reporte2 == "" or $fecha_final_reporte2 == ""){
	       	echo "<h3>No hay radicados recibidos en el rango de fecha indicado</h3>"; // Estructura html	
	       	exit();
		}else{
			/* Re estructuracion de variables recogidas (Pone horas a las fechas) */
			$fecha_inicio_reporte2 	= "$fecha_inicial_reporte2 00:00:00";// Ejemplo: 16/04/2020 00:00:00
			$fecha_fin_reporte2 	= "$fecha_final_reporte2 23:59:59";// Ejemplo: 16/04/2020 23:59:59
		}

		if($dependencia_reporte2 != ""){// Si select_dependencia_reporte2 no viene vacío
			$select_dependencia_reporte2 = "and codigo_dependencia ='$dependencia_reporte2'";
			$pie_reporte = "de la Dependencia <font color='green'>$dependencia_reporte2</font> desde ";
			// echo "<script>$('#estadistica_dona_reporte2').hide();
			// 			  $('#estadistica_dona2_reporte2').show();
			// 	</script>";//Se oculta la dona 1 y se habilita la dona 2
	    } else {
			$select_dependencia_reporte2 = "";
			$pie_reporte = "";
			// $select_usuario_radicador_reporte2 = "";
			// echo "<script>$('#estadistica_dona_reporte2').show();
			// 			  $('#estadistica_dona2_reporte2').hide();</script>";//Se oculta la dona 2 y se habilita la dona 1
	    }
	
		/* Consulta en la base de datos los radicados de entrada entre las fechas recibidas */
		$query_reporte2 = "select * from radicado r join dependencias d on r.dependencia_actual=d.codigo_dependencia where fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2' and numero_radicado ilike '%1' $select_dependencia_reporte2 order by fecha_radicado";

		$fila_reporte2  	= pg_query($conectado,"$query_reporte2 limit 10");//Se envia la consulta mediante pg_query
	   	$registros_reporte2 = pg_num_rows($fila_reporte2); // Se trae la cantidad de filas de la query
	   	/* Fin consulta a base de datos	*/
	   	if($registros_reporte2==0){ // Si la consulta viene vacia
	       	echo "<h3>No hay radicados recibidos en el rango de fecha desde el $fecha_inicial_reporte2 hasta el $fecha_final_reporte2</h3>"; // Estructura html
		}else{
			if($dependencia_reporte2 == ""){
				$query_contador_dependencias="select count(*) total,(select  count(*) from radicado r where estado_radicado='en_tramite' and r.dependencia_actual=d.codigo_dependencia and (fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2') and r.numero_radicado ilike '%1') en_tramite, (select  count(*) from radicado r where estado_radicado='no_requiere_respuesta' and r.dependencia_actual=d.codigo_dependencia and (fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2') and r.numero_radicado ilike '%1') nrr, (select  count(*) from radicado r where estado_radicado='tramitado' and r.dependencia_actual=d.codigo_dependencia and (fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2') and r.numero_radicado ilike '%1') tramitado, d.codigo_dependencia, d.nombre_dependencia from radicado r join dependencias d on r.dependencia_actual=d.codigo_dependencia where (r.fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2') and r.numero_radicado ilike '%1' $select_dependencia_reporte2 GROUP BY d.codigo_dependencia, d.nombre_dependencia order by nombre_dependencia";

				$fila_contador_dependencias  		= pg_query($conectado,$query_contador_dependencias);
			   	$registros_contador_dependencias 	= pg_num_rows($fila_contador_dependencias); 

			   	if($registros_contador_dependencias==0){
			   		$tabla_dependencias = "<h2>No hay resultados con los datos ingresados</h2>";
			   	}else{
				   	$tabla_dependencias = "<table border='0'><tr class='center'><td class='descripcion'>Nombre Dependencia</td><td class='descripcion'>Total Radicados Generados</td><td class='descripcion'>Tramitados<br>(Terminados)</td><td class='descripcion'>No Requiere Respuesta<br>(Terminados)</td><td class='descripcion'>En Trámite <br><b style='font-size:18px;'>(Falta por Cerrar)</b></td></tr>";

				   	$dependencia_estructura_dona 	= "";
				   	$contador_total_radicados 		= 0; // Contador para utilizar en la grafica de dona

				   	for ($k=0; $k < $registros_contador_dependencias; $k++) { 
						$linea_contador_dependencias 		= pg_fetch_array($fila_contador_dependencias);
				   		$codigo_dependencia = $linea_contador_dependencias['codigo_dependencia'];
				   		$nombre_dependencia = $linea_contador_dependencias['nombre_dependencia'];
				   		$contador 			= $linea_contador_dependencias['total'];
				   		$tramitados			= $linea_contador_dependencias['tramitado'];
				   		$en_tramite 		= $linea_contador_dependencias['en_tramite'];
				   		$nrr 				= $linea_contador_dependencias['nrr'];

				   		// Se construye el data de la dona intermedio
				   		$dependencia_estructura_dona.=",['$nombre_dependencia ($contador)', $contador]"; 
				   		$contador_total_radicados+=$contador;


				   		$tabla_dependencias .= "<tr><td class='detalle'>$nombre_dependencia<br> ($codigo_dependencia)</td><td class='detalle center'><b style='font-size:24px'>$contador</b></td><td class='detalle center'><button class='botones2' onclick='mas_tabla(\"$codigo_dependencia\",\"tramitado\",\"\")'><b style='color: orange;'>$tramitados</b></button></td><td class='detalle center'><button class='botones2' onclick='mas_tabla(\"$codigo_dependencia\",\"no_requiere_respuesta\",\"\")'><b style='color: orange;'>$nrr</button></b></td><td class='detalle center'><button class='botones2' onclick='mas_tabla(\"$codigo_dependencia\",\"en_tramite\",\"\")'><b style='color: red;'>$en_tramite</b></button></td></tr>";
				   	}
					$dependencia_estructura_dona.="])";// Se construye el data de la dona fin
				   	$tabla_dependencias .= "</table>";
			   	}
				$encabezado_tabla_oficio_reporte2="$tabla_dependencias <br><h3>Se han recibido en total <b style='color: green;'>$contador_total_radicados</b> documentos de $pie_reporte <b style='color: green;><u>ENTRADA</u></b> en el rango de fecha desde el <span style='color: green;'> $fecha_inicial_reporte2</span> hasta el <span style='color: green;'> $fecha_final_reporte2</span></h3>";
			}else{
				$query_contador_dependencias = "select dependencia_actual, nombre_dependencia, usuarios_control from radicado r join dependencias d on r.dependencia_actual=d.codigo_dependencia where (r.fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2') and r.numero_radicado ilike '%1' and codigo_dependencia ='$dependencia_reporte2' group by dependencia_actual, usuarios_control, nombre_dependencia order by dependencia_actual, usuarios_control"; 
				$fila_contador_dependencias  		= pg_query($conectado,$query_contador_dependencias);
			   	$registros_contador_dependencias 	= pg_num_rows($fila_contador_dependencias); 

			   	if($registros_contador_dependencias==0){
			   		$tabla_dependencias = "<h2>No hay resultados con los datos ingresados</h2>";
			   	}else{
				   	$tabla_dependencias = "<table border='0'><tr class='center'><td class='descripcion'>Nombre Dependencia</td><td class='descripcion'>Usuario(s) Responsable(s)</td><td class='descripcion'>Total Radicados Asignados</td><td class='descripcion'>Tramitados<br>(Terminados)</td><td class='descripcion'>No Requiere Respuesta<br>(Terminados)</td><td class='descripcion'>En Trámite <br><b style='font-size:18px;'>(Falta por Cerrar)</b></td></tr>";

				   	$contador_total_radicados 	= 0; // Contador para utilizar en la grafica de dona
				   	$usuario_estructura_dona 	= ""; // String para utilizar en la grafica de dona
				   	for ($k=0; $k < $registros_contador_dependencias; $k++) { 
						$linea_contador_dependencias 	= pg_fetch_array($fila_contador_dependencias);
						$usuarios_control 		= $linea_contador_dependencias['usuarios_control'];
						$dependencia_actual1 	= $linea_contador_dependencias['dependencia_actual'];
						$nombre_dependencia1 	= $linea_contador_dependencias['nombre_dependencia'];

				   		$query_contador_usuarios = "select count(*) total,(select count(*) from radicado r where r.numero_radicado ilike '%1' and (r.fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2') and dependencia_actual ='$dependencia_reporte2' and usuarios_control='$usuarios_control' and estado_radicado = 'tramitado') tramitados,(select count(*) from radicado r where r.numero_radicado ilike '%1' and (r.fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2') and dependencia_actual ='$dependencia_reporte2' and usuarios_control='$usuarios_control' and estado_radicado = 'no_requiere_respuesta') nrr,(select count(*) from radicado r where r.numero_radicado ilike '%1' and (r.fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2') and dependencia_actual ='$dependencia_reporte2' and usuarios_control='$usuarios_control' and estado_radicado = 'en_tramite') en_tramite from radicado r where r.numero_radicado ilike '%1' and (r.fecha_radicado between '$fecha_inicio_reporte2' and '$fecha_fin_reporte2') and dependencia_actual ='$dependencia_reporte2' and usuarios_control='$usuarios_control'";

				   		$fila_contador_usuarios  	= pg_query($conectado,$query_contador_usuarios);
						$linea_contador_usuarios 	= pg_fetch_array($fila_contador_usuarios);
						$total_usuario 		= $linea_contador_usuarios['total'];
						$tramitados_usuario = $linea_contador_usuarios['tramitados'];
						$nrr_usuario 		= $linea_contador_usuarios['nrr'];
						$en_tramite_usuario = $linea_contador_usuarios['en_tramite'];

						// Se construye el data de la dona por usuario intermedio
						$usuarios_control1 = str_replace(",", "-", $usuarios_control);
						$usuario_estructura_dona.=",['$usuarios_control1 ($total_usuario)', $total_usuario]"; 

				   		$tabla_dependencias .= "<tr><td class='detalle'>$nombre_dependencia1<br> ($dependencia_actual1)</td><td class='detalle'>$usuarios_control</td><td class='detalle center'><b style='font-size:24px'>$total_usuario</b></td><td class='detalle center'><button class='botones2' onclick='mas_tabla(\"$codigo_dependencia\",\"tramitado\",\"$usuarios_control\")'><b style='color: orange;'>$tramitados_usuario</b></button></td><td class='detalle center'><button class='botones2' onclick='mas_tabla(\"$codigo_dependencia\",\"no_requiere_respuesta\",\"$usuarios_control\")'><b style='color: orange;'>$nrr_usuario</button></b></td><td class='detalle center'><button class='botones2' onclick='mas_tabla(\"$codigo_dependencia\",\"en_tramite\",\"$usuarios_control\")'><b style='color: red;'>$en_tramite_usuario</b></button></td></tr>";
				   		$contador_total_radicados+=$total_usuario;
				   	// echo "$query_contador_usuarios<br><br>";
					}
					$usuario_estructura_dona.="])";
					$tabla_dependencias .= "</table>";
				}
				$encabezado_tabla_oficio_reporte2="$tabla_dependencias <br><h3>Se han recibido en total <b style='color: green;'>$contador_total_radicados</b> documentos de $pie_reporte <b style='color: green;><u>ENTRADA</u></b> en el rango de fecha desde el <span style='color: green;'> $fecha_inicial_reporte2</span> hasta el <span style='color: green;'> $fecha_final_reporte2</span></h3>";
			}


			// echo $query_contador_dependencias;
			
			/* Se definen varibles para la creacion de la tabla de resultados */
			$tabla_oficios 	= "";
			$j  			= 1;
			
			echo "$encabezado_tabla_oficio_reporte2
			<tr>	
				<td colspan='3'>
				<td>
					<center>
						<a href='formatos/reporte_excel.php?nombre_reporte=reporte2_radicado_entrega&codigo_entidad=$codigo_entidad&sql=".urlencode($query_reporte2)."' style='text-decoration:none;'>
							<img src='imagenes/iconos/logo_excel.png' width='30' height='25' title='Exportar a excel' style='padding : 10px;'>
						</a> 
						<a href='formatos/reporte_pdf.php?nombre_reporte=reporte2_radicado_entrega&sql=".urlencode($query_reporte2)."' style='text-decoration:none;'>
							<img src='imagenes/iconos/archivo_pdf.png' width='30' height='25' title='Exportar a PDF' style='padding : 10px; text-decoration:none;'>
						</a>
					</center>
				</td>
				<td colspan='5'></td>
			</tr>
			</table> ";//Estrucutra html

			/*Creacion de las estadisticas en forma donda con Pie Chart */
			/* Consulta a base de datos */
			$query_agrupado_dependencia_reporte2 = $query_contador_dependencias;

			$fila_agrupado_dependencia_reporte2         = pg_query($conectado,$query_agrupado_dependencia_reporte2);//Se envia la consulta mediante pg_query
			$registros_dependencia_reporte2 			= pg_num_rows($fila_agrupado_dependencia_reporte2); // Se trae la cantidad de filas de la query

			/* Construccion de la dona */
			if($dependencia_reporte2 == ""){
				$titulo_grafico = "Grafico de radicados por Dependencias";
				$dependencia_estructura_dona = "google.visualization.arrayToDataTable([['Task', 'Total Radicados ']$dependencia_estructura_dona";// Se construye el data de la dona inico
			}else{
				$titulo_grafico = "Grafico de radicados por Usuario Responsable de la dependencia";
				$dependencia_estructura_dona = "google.visualization.arrayToDataTable([['Task', 'Total Radicados']$usuario_estructura_dona";// Se construye el data de la dona
				// $dependencia_estructura_dona.="$usuario_estructura_dona";// Se construye el data de la dona fin
			}
			/* Fin Construccion de la dona */

			echo "<script> 
			google.charts.load('current', {packages:['corechart']});
		    google.charts.setOnLoadCallback(dona_dependencia);
		    function dona_dependencia() {
		        var data = $dependencia_estructura_dona;

		        var options = {
		          title: '$titulo_grafico',
		          is3D: true,
		        };
		        var chart = new google.visualization.PieChart(document.getElementById('estadistica_dona_reporte2'));
		        chart.draw(data, options);
		    };
			</script>
			<div id='estadistica_dona_reporte2' style='width: 1200px; height: 500px;' class='center'></div>";//Estructura html de las estadisticas en forma donda con Pie Chart */ 
		}		
	break;

	case 'reporte2_estados':
		$fecha_inicial 		= $_POST['fecha_inicial_reporte2'];
		$fecha_final 		= $_POST['fecha_final_reporte2'];
		$dependencia 		= $_POST['dependencia'];
		$estado_radicado 	= $_POST['estado_radicado'];
		$usuarios_control 	= $_POST['usuarios_control'];

		if($estado_radicado=="vacio"){
			$estado_radicado = "";
		}

		if($usuarios_control==""){
			$query_rep = "select * from radicado r join dependencias d on r.dependencia_actual=d.codigo_dependencia where (r.fecha_radicado between '$fecha_inicial 00:00:00' and '$fecha_final 23:59:59') and r.numero_radicado ilike '%1' and codigo_dependencia='$dependencia' and estado_radicado = '$estado_radicado'";
		}else{
			$query_rep = "select * from radicado r join dependencias d on r.dependencia_actual=d.codigo_dependencia where (r.fecha_radicado between '$fecha_inicial 00:00:00' and '$fecha_final 23:59:59') and r.numero_radicado ilike '%1' and usuarios_control='$usuarios_control' and estado_radicado = '$estado_radicado'";			
		}

		$fila_rep 		= pg_query($conectado,$query_rep);
		$registros_rep 	= pg_num_rows($fila_rep); 

	   	if($registros_rep==0){
	   		$tabla_dependencias = "<h2>No hay resultados con los datos ingresados</h2>";
	   	}else{
	   		switch ($estado_radicado) {
	   			case 'en_tramite':
				   	$tabla_dependencias = "<table border='0' style='width:2000px'><tr class='center'><td class='descripcion' style='width:25px'>ID</td><td class='descripcion' style='width:150px'>Numero Radicado</td><td class='descripcion' style='width:100px'>Usuario(s) Responsable(s) de Tramitar Documento</td><td class='descripcion' style='width:200px'>Tiempo que ha pasado desde que se recibió</td><td class='descripcion' style='width:360px;'>Usuario lo tiene asignado desde</td><td class='descripcion' style='width:75px;'>Estado del radicado</td><td class='descripcion' style='width:75px;'>Medio de Recepcion del radicado</td><td class='descripcion' style='width:160px;'>Medio de Respuesta solicitado por el usuario</td><td class='descripcion' style='width:200px;'>Expediente Asignado</td><td class='descripcion' style='width:115px;'>TRD Asignada</td><td class='descripcion' style='width:350px;'>Asunto</td></tr>";
   				
				   	for ($k=0; $k < $registros_rep; $k++) { 
				   		$h = $k+1;
						$linea_rep 						= pg_fetch_array($fila_rep);
				   		$numero_radicado 				= $linea_rep['numero_radicado'];
				   		$usuarios_control 				= $linea_rep['usuarios_control'];
				       	$asunto 						= $linea_rep['asunto'];
				       	$codigo_serie 					= $linea_rep['codigo_serie'];
				       	$codigo_subserie 				= $linea_rep['codigo_subserie'];
				       	$estado_radicado 				= $linea_rep['estado_radicado'];
				       	$fecha_radicado 				= $linea_rep['fecha_radicado'];
				       	$id_expediente 					= $linea_rep['id_expediente'];
				       	$medio_recepcion 				= $linea_rep['medio_recepcion'];
				       	$medio_respuesta_solicitado 	= $linea_rep['medio_respuesta_solicitado'];

				   		/* Se calculan los dias que han pasado desde que se radica el documento */
                		$fecha2 		= date("Y/m/d");

						$dias = (strtotime($fecha_radicado)-strtotime($fecha2))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
						$dias = abs($dias); 
						$dias = floor($dias); // Esta es la cantidad de dias calculado.

						/* Se calcula la fecha desde la que tiene el documento reasignado */
						$query_historico_reasignado = "select * from historico_eventos where numero_radicado ='$numero_radicado' and (transaccion ilike 'Se reasigna radicado al usuario%' or transaccion ilike 'Radicacion de entrada') order by id desc";
						$fila_historico_reasignado 		= pg_query($conectado,$query_historico_reasignado);
						$linea_historico_reasignado 	= pg_fetch_array($fila_historico_reasignado);
						$fecha_historico_reasignado 	= $linea_historico_reasignado['fecha']; 
						$transaccion_hist_reasignado 	= $linea_historico_reasignado['transaccion']; 
						$usuario_hist_reasignado 	 	= $linea_historico_reasignado['usuario']; 

				   		/* Se calculan los dias que han pasado desde que se recibió reasignado el documento */
                		$fecha_hoy 		= date("Y/m/d");

						$dias2 = (strtotime($fecha_historico_reasignado)-strtotime($fecha_hoy))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
						$dias2 = abs($dias2); 
						$dias2 = floor($dias2); // Esta es la cantidad de dias calculado.

						/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2020"*/
						require_once "../include/genera_fecha.php";
						$fecha_modificacion=$b->traducefecha($fecha_historico_reasignado);	

						/* Se define mensaje para id_expediente1 */
						if($id_expediente==""){
							$id_expediente = " <b style=\"color: red\"> No se ha asingado expediente todavía</b>";
						}

						/* Se define mensaje para TRD */
						if($codigo_subserie==""){
							$trd = "<b style=\"color: red\"> No se ha asingado TRD todavía</b>";
						}else{
							$trd = "Serie ($codigo_serie)<br>Subserie($codigo_subserie)";
						}

				   		$tabla_dependencias .= "
				   		<tr>
				   			<td class='detalle center'>$h</td>
				   			<td class='detalle center'>$numero_radicado</td>
				   			<td class='detalle'>$usuarios_control</td>
				   			<td class='detalle'>Han pasado <b style=\"color: red\"> $dias</b> dias desde que se recibió documento</td>
				   			<td class='detalle center'>$transaccion_hist_reasignado <br>(Desde el usuario <b style=\"color: green\"> $usuario_hist_reasignado</b>) <br>el $fecha_modificacion<br> <b style=\"color: blue\">Hace $dias2 dias </b> </td>
				   			<td class='detalle center'> <b style=\"color: red\"> $estado_radicado</b></td>
				   			<td class='detalle center'>$medio_recepcion</td>
				   			<td class='detalle center'>$medio_respuesta_solicitado</td>
				   			<td class='detalle center'>$id_expediente</td>
				   			<td class='detalle center'>$trd</td>
				   			<td class='detalle'>$asunto</td>
				   		</tr>";
				   	}
				   	$tabla_dependencias.="</table>";			
	   				break;
	   			
	   			case 'no_requiere_respuesta':
	   			 	$tabla_dependencias = "<table border='0'><tr class='center'><td class='descripcion'>ID</td><td class='descripcion'>Numero Radicado</td><td class='descripcion'>Usuario(s) Responsable(s) de Tramitar Documento</td><td class='descripcion'>Marcado como NRR desde</td><td class='descripcion'>Tiempo que ha tomado marcar como NRR</td><td class='descripcion'>Estado del radicado</td><td class='descripcion'>Expediente Asignado</td><td class='descripcion'>TRD Asignada</td><td class='descripcion'>Asunto</td></tr>";

	   			 	for ($k=0; $k < $registros_rep; $k++) { 
					   	$h = $k+1;
						$linea_rep 						= pg_fetch_array($fila_rep);

						$asunto 						= $linea_rep['asunto'];	       	
						$codigo_serie 					= $linea_rep['codigo_serie'];
						$codigo_subserie 				= $linea_rep['codigo_subserie'];
						$estado_radicado 				= $linea_rep['estado_radicado'];
						$fecha_radicado 				= $linea_rep['fecha_radicado'];
						$id_expediente 					= $linea_rep['id_expediente'];	       	
						$numero_radicado 				= $linea_rep['numero_radicado'];
						$usuarios_control 				= $linea_rep['usuarios_control'];

		   			 	/* Se calcula la fecha que se ha marcado como NRR */
						$query_historico_reasignado = "select * from historico_eventos where numero_radicado ='$numero_radicado' and (transaccion ilike 'Se marca documento como NRR%' or transaccion ilike '%Radicacion de entrada%') order by id desc";
						$fila_historico_reasignado 		= pg_query($conectado,$query_historico_reasignado);
						$linea_historico_reasignado 	= pg_fetch_array($fila_historico_reasignado);
						$fecha_historico_reasignado 	= $linea_historico_reasignado['fecha']; 
						$transaccion_hist_reasignado 	= $linea_historico_reasignado['transaccion']; 
						$usuario_hist_reasignado 	 	= $linea_historico_reasignado['usuario']; 
						/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2020"*/
						require_once "../include/genera_fecha.php";
						$fecha_modificacion=$b->traducefecha($fecha_historico_reasignado);	

						/* Se calculan los dias que ha tomado marcar como NRR */
						$dias3 = (strtotime($fecha_radicado)-strtotime($fecha_historico_reasignado))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
							$dias3 = abs($dias3); 
							$dias3 = floor($dias3); // Esta es la cantidad de dias calculado.

					   	$tabla_dependencias .= "
				   		<tr>
				   			<td class='detalle center'>$h</td>
				   			<td class='detalle center'>$numero_radicado</td>
				   			<td class='detalle'>$usuarios_control</td>
				   			<td class='detalle center'>(Desde el usuario <b style=\"color: green\"> $usuario_hist_reasignado</b>)<br>$transaccion_hist_reasignado <br>el $fecha_modificacion</td>
				   			<td class='detalle center'>Desde que se recibe el documento hasta que <b style=\"color: green\">($usuario_hist_reasignado)</b> marca como NRR el documento pasaron <b style=\"color: green\"> $dias3</b> dias</td>
				   			<td class='detalle center'><b style=\"color: green\">$estado_radicado</b></td>
				   			<td class='detalle center'>$id_expediente</td>
				   			<td class='detalle center'>Serie ($codigo_serie)<br>Subserie($codigo_subserie)</td>
				   			<td class='detalle center'>$asunto</td>				   			
				   		</tr>";	
					}     
					$tabla_dependencias.="</table>";			
	   				break;

	   			case 'tramitado':
	   				$tabla_dependencias = "<table border='0'><tr class='center'><td class='descripcion'>ID</td><td class='descripcion'>Numero Radicado de Entrada</td><td class='descripcion'>Numero Radicado de Respuesta</td><td class='descripcion'>Usuario(s) Responsable(s) de Tramitar Documento</td><td class='descripcion' style='width:320px;'>Tiempo que ha tardado en responder desde que se recibió</td><td class='descripcion'>Estado del radicado</td><td class='descripcion'>Medio de Recepcion del radicado</td><td class='descripcion'>Medio de Respuesta solicitado por el usuario</td><td class='descripcion'>Expediente Asignado</td><td class='descripcion'>TRD Asignada</td><td class='descripcion' style='width:320px;'>Asunto</td></tr>";
				
				   	for ($k=0; $k < $registros_rep; $k++) { 
				   		$h = $k+1;
						$linea_rep 						= pg_fetch_array($fila_rep);
				   		$fecha_radicado 				= $linea_rep['fecha_radicado'];
						$asunto 	 					= $linea_rep['asunto']; 
						$codigo_serie 					= $linea_rep['codigo_serie'];
						$codigo_subserie 				= $linea_rep['codigo_subserie'];
				   		$id_expediente 					= $linea_rep['id_expediente'];
				   		$medio_recepcion 				= $linea_rep['medio_recepcion'];
				   		$medio_respuesta_solicitado 	= $linea_rep['medio_respuesta_solicitado'];
				   		$numero_radicado 				= $linea_rep['numero_radicado'];


						/* Se calcula la fecha desde la que tiene el documento reasignado */
						$query_respuesta_radicados = "select * from respuesta_radicados res  join  historico_eventos h on res.radicado_respuesta=h.numero_radicado where res.radicado_padre ='$numero_radicado' and transaccion ilike '%Sube PDF principal con firmas%'";

						$fila_respuesta_radicados 	= pg_query($conectado,$query_respuesta_radicados);
						$linea_respuesta_radicados 	= pg_fetch_array($fila_respuesta_radicados);
						$fecha_respuesta			= $linea_respuesta_radicados['fecha']; 
						$radicado_padre 			= $linea_respuesta_radicados['radicado_padre']; 
						$radicado_respuesta 	 	= $linea_respuesta_radicados['radicado_respuesta']; 
						$usuario 	 				= $linea_respuesta_radicados['usuario']; 

						$dias = (strtotime($fecha_radicado)-strtotime($fecha_respuesta))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
						$dias = abs($dias); 
						$dias = floor($dias); // Esta es la cantidad de dias calculado.
						$j = $k+1;

						$tabla_dependencias.="<tr>
							<td class='detalle center'>$j</td>
							<td class='detalle center'>$radicado_padre <br> Recidido el <br>($fecha_radicado)</td>
							<td class='detalle center'>$radicado_respuesta</td>
							<td class='detalle center'>$usuario</td>
							<td class='detalle center'>El usuario <b style=\"color: green\">$usuario</b><br>ha subido el PDF firmado de la respuesta al documento el <b style=\"color: green\">($fecha_respuesta)</b><br> En total <br> <b style=\"color: green\">($dias dias)</b> </td>
							<td class='detalle center'><b style=\"color: green\">TRAMITADO</b></td>
							<td class='detalle center'>$medio_recepcion</td>
							<td class='detalle center'>$medio_respuesta_solicitado</td>
							<td class='detalle '>$id_expediente</td>
							<td class='detalle center'>Serie ($codigo_serie)<br>Subserie($codigo_subserie)</td>
				   			<td class='detalle center'>$asunto</td>

						</tr>";	
				   	}	
				   	$tabla_dependencias.="</table>";			
   					break;	
   					
	   			default:
	   				var_dump($_POST);
	   				echo "<br>";
	   				$tabla_dependencias = $query_rep;
	   				break;
	   		}

		   	echo "<button class='botones' onclick='cargar_reporte_2()'>Volver</button><br>$tabla_dependencias";
		}   	
		break;
	/*****************************************************************************************
	Fin Case reporte2 para cargar la tabla de radicados con base a filtros de fecha, tipo radicado, dependencia y usuario 
	/*****************************************************************************************/
	/*****************************************************************************************
	Inicio Case para cargar option en el select usuario_radicador_reporte2 ubicado en reporte2_radicados_entrada.php
	/*****************************************************************************************
	* @brief Recibe desde 
	* - reportes/reporte2_radicados_entrada.php[function usuarios_dependencia(codigo_dependencia)]
	* para listar mediante <option> los usuarios que existen en la base de datos con la relacion a la dependencia
	* @param {string} ($dependencia) Es obligatorio y se usa para filtrar la consulta.
	* @return {string} String con los <option> del listado de usuarios que existen en la base de datos con la relacion a la dependencia.
	*****************************************************************************************/		
	case 'reporte2_usuarios_dependencia':
		/* Recibir las variables desde la funcion usuarios_dependencia(codigo_dependencia) */
		$codigo_dependencia = $_POST['codigo_dependencia'];
		/* Fin Recibir las variables desde la funcion usuarios_dependencia(codigo_dependencia) */
		/* Consulta a base de datos */
		$query_usuarios_radicacion_reporte2     = "select login, nombre_completo from usuarios where codigo_dependencia='$codigo_dependencia' order by login asc";// Estrucutura de la consulta
    	$fila_usuarios_radicacion_reporte2      = pg_query($conectado,$query_usuarios_radicacion_reporte2);//Se envia la consulta mediante pg_query
    	$registros_usuarios_radicacion_reporte2 = pg_num_rows($fila_usuarios_radicacion_reporte2); // Se trae la cantidad de filas de la query
    	/* Fin Consulta a base de datos */
   		$return = "<option value='' selected>TODOS LOS USUARIOS</option>";//Variable de almacenamiento y que va retornar
		for ($j=0; $j < $registros_usuarios_radicacion_reporte2; $j++){// Se ejecutara "$registros_usuarios_radicacion_reporte2(Ejemplo '2')" veces. 
			$linea_usuarios_radicacion_reporte2 = pg_fetch_array($fila_usuarios_radicacion_reporte2);// Se pasan los datos a un array para ser procesados
			/* Recoleccion de datos */
			$login 				= $linea_usuarios_radicacion_reporte2['login'];
			$nombre_completo 	= $linea_usuarios_radicacion_reporte2['nombre_completo'];
			/* Fin Recoleccion de datos */
			$return.="<option value='$login' title='$nombre_completo'>$login</option>";
		}
		echo $return;
		break;
	/*****************************************************************************************
	Fin Case para cargar option en el select usuario_radicador_reporte2 ubicado en reporte2_radicados_entrada.php
	/*****************************************************************************************/
	/*****************************************************************************************
	Inicio Case reporte3 para cargar la tabla de radicados que no han sido terminados de llenar
	/*****************************************************************************************
	* @brief Recibe desde 
	* - reportes/reporte3_radicados_vacios.php[cargar_reporte_radicados_vacios]
	* En lista mediante <table> los radicados que no han sido completados según los parámetros recibidos.
	* @param {string} ($tipo_radicado_vacio) No es obligatorio y se usa para filtrar la consulta.
	* @param {string} ($dependencia) No es obligatorio y se usa para filtrar la consulta.
	* @param {string} ($usuarios_dependencia) No es obligatorio y se usa para filtrar la consulta.
	* @param {string} ($fecha_inicial) Es obligatorio y se usa para filtrar la consulta.
	* @param {string} ($fecha_final) Es obligatorio y se usa para filtrar la consulta.
	* @return {string} String con la <table> de los datos de las consultas
	*****************************************************************************************/	
	case 'reporte3':
		/* Recibe variables desde la funcion cargar_reporte_radicados_vacios() desde reporte3_radicados_vacios.php */
		$tipo_radicado_vacio         	= $_POST['tipo_radicado_vacio'];
		$dependencia_reporte3 	        = $_POST['dependencia'];
		$usuarios_dependencia_reporte3 	= $_POST['usuarios_dependencia'];
		$fecha_inicio_reporte3 		    = $_POST['fecha_inicial'];
		$fecha_fin_reporte3             = $_POST['fecha_final'];
		/* Fin Recibe variables desde la funcion cargar_reporte_radicados_vacios() desde reporte3_radicados_vacios.php */
		/* Re estructuración de variables recogidas */
		$fecha_inicial_reporte3 	= "$fecha_inicio_reporte3 00:00:00";// Ejemplo: 16/04/2020 00:00:00
		$fecha_final_reporte3 		= "$fecha_fin_reporte3 23:59:59";// Ejemplo: 16/04/2020 23:59:59
		if($tipo_radicado_vacio != ""){// Si tipo_radicado_vacio viene con otro valor diferente a vació
			$select_tipo_radicado_vacio = "and (r.$tipo_radicado_vacio is null or r.$tipo_radicado_vacio ='')";
		}else{
			$select_tipo_radicado_vacio = "and ((r.asunto is null or r.asunto ='') or (r.path_radicado is null or r.path_radicado = ''))";
		}
		if($dependencia_reporte3 != ""){// Si dependencia_reporte3 viene con otro valor diferente a vació
			$select_dependencia_reporte3 = "and r.dependencia_radicador ='$dependencia_reporte3'";
			echo "<script>$('#estadistica_dona1_reporte3').show();
						  $('#estadistica_dona2_reporte3').hide();
						  $('#estadistica_dona3_reporte3').hide();
						  $('#estadistica_dona4_reporte3').hide();
						  $('#estadistica_dona5_reporte3').hide();
						  $('#estadistica_dona6_reporte3').hide();</script>";//Se oculta la dona 1 y se habilita la dona 2
			if($usuarios_dependencia_reporte3 != ""){// Si usuarios_dependencia_reporte3 viene con otro valor diferente a vació
				$select_usuarios_dependencia_reporte3 = "and r.usuario_radicador ='$usuarios_dependencia_reporte3'";
				if($tipo_radicado_vacio != ""){
					echo "<script>$('#estadistica_dona1_reporte3').hide();
						  $('#estadistica_dona2_reporte3').hide();
						  $('#estadistica_dona3_reporte3').hide();
						  $('#estadistica_dona4_reporte3').hide();
						  $('#estadistica_dona5_reporte3').hide();
						  $('#estadistica_dona6_reporte3').hide();</script>";//Se oculta la dona 1 y se habilita la dona 2
					}else{
						echo "<script>$('#estadistica_dona1_reporte3').hide();
						  $('#estadistica_dona2_reporte3').hide();
						  $('#estadistica_dona3_reporte3').hide();
						  $('#estadistica_dona4_reporte3').show();
						  $('#estadistica_dona5_reporte3').hide();
						  $('#estadistica_dona6_reporte3').hide();</script>";//Se oculta la dona 1 y se habilita la dona 2
					}
			}else{
				$select_usuarios_dependencia_reporte3 = "";
				if($tipo_radicado_vacio != ""){
					echo "<script>$('#estadistica_dona1_reporte3').hide();
						  $('#estadistica_dona2_reporte3').hide();
						  $('#estadistica_dona3_reporte3').hide();
						  $('#estadistica_dona4_reporte3').hide();
						  $('#estadistica_dona5_reporte3').hide();
						  $('#estadistica_dona6_reporte3').show();</script>";//Se oculta la dona 1 y se habilita la dona 2
				}else{
					echo "<script>$('#estadistica_dona1_reporte3').show();
						  $('#estadistica_dona2_reporte3').hide();
						  $('#estadistica_dona3_reporte3').hide();
						  $('#estadistica_dona4_reporte3').hide();
						  $('#estadistica_dona5_reporte3').hide();
						  $('#estadistica_dona6_reporte3').hide();</script>";//Se oculta la dona 1 y se habilita la dona 2
				}
			}
	    }else{
			$select_dependencia_reporte3 = "";
			if($tipo_radicado_vacio == ""){// Si tipo_radicado_vacio viene con otro valor diferente a vació
				echo "<script>$('#estadistica_dona1_reporte3').hide();
							  $('#estadistica_dona2_reporte3').show();
							  $('#estadistica_dona3_reporte3').show();
							  $('#estadistica_dona4_reporte3').hide();
							  $('#estadistica_dona5_reporte3').hide();
							  $('#estadistica_dona6_reporte3').hide();</script>";//Se oculta la dona 1 y se habilita la dona 2
			}else{
				echo "<script>$('#estadistica_dona1_reporte3').hide();
							  $('#estadistica_dona2_reporte3').hide();
							  $('#estadistica_dona3_reporte3').hide();
							  $('#estadistica_dona4_reporte3').hide();
							  $('#estadistica_dona5_reporte3').show();
							  $('#estadistica_dona6_reporte3').hide();</script>";//Se oculta la dona 1 y se habilita la dona 2
			}
			$select_usuarios_dependencia_reporte3 = "";
		}
		/* Fin Re estructuración de variables recogidas */
		/* Consulta a base de datos */
		$sql_reporte3 = "select count(r.*), u.nombre_completo, r.usuario_radicador, r.dependencia_radicador, d.nombre_dependencia from radicado as r
						inner join usuarios as u on u.login = r.usuario_radicador join dependencias d on r.dependencia_radicador=d.codigo_dependencia
						where (r.fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') $select_dependencia_reporte3 $select_usuarios_dependencia_reporte3
						group by u.nombre_completo, r.usuario_radicador, r.dependencia_radicador, d.nombre_dependencia
						order by r.dependencia_radicador";//estructura sql

		$fila_reporte3  = pg_query($conectado,$sql_reporte3);//Se enviá la consulta mediante pg_query
	   	$registros_reporte3 = pg_num_rows($fila_reporte3); // Se trae la cantidad de filas de la query
	   	/* Fin consulta a base de datos	*/
	   	if($registros_reporte3==0){ // Si la consulta viene vaciá
        	echo "<h3>No hay radicados recibidos en el rango de fecha desde el $fecha_inicio_reporte3 hasta el $fecha_fin_reporte3</h3>"; // Estructura html
		}else{
			if($tipo_radicado_vacio != ""){
				if($tipo_radicado_vacio == "asunto"){
					$tipo_radicado_vacio2 = "Asunto Vacío";
				}else{
					$tipo_radicado_vacio2 = "Imagen PDF Vacía";
				}
				$tipo_radicado_vacio_tabla = "<td class='descripcion'>$tipo_radicado_vacio2</td>";// Estructura html
			}else{
				$tipo_radicado_vacio_tabla = "<td class='descripcion'>Radicados Completos</td>
												<td class='descripcion'>Radicados Sin Asunto</td>
												<td class='descripcion'>Radicados Sin Imagen PDF</td>
												<td class='descripcion'>Radicados Total</td>";// Estructura html
			}
			$encabezado_tabla_reporte3 = "<table border='0'><tr class='center'>";// Estructura html
			if($dependencia_reporte3 != ""){// Si dependencia_reporte3 viene con otro valor diferente a vació
				$encabezado_tabla_reporte3 .= "<td class='descripcion'>Id</td>";// Estructura html
			}else{
				$encabezado_tabla_reporte3 .= "<td class='descripcion'>Dependencia</td>";// Estructura html
			}
			$encabezado_tabla_reporte3 .= "<td class='descripcion'>Usuario</td>$tipo_radicado_vacio_tabla</tr>";
			$j = 1;
			$tabla_reporte3 = "";
			for($i=0; $i<$registros_reporte3 ; $i++){ // Se ejecutara la cantidad de $registros_reporte3
				$linea_reporte3 			= pg_fetch_array($fila_reporte3);//Se pasan los datos a un array para el tratado de estos
				/* Recolección de datos extraídos de la base de datos */
				$count 							= $linea_reporte3['count'];
				$nombre_usuario_radicador 		= $linea_reporte3['nombre_completo'];
				$usuario_radicador 				= $linea_reporte3['usuario_radicador'];
				$dependencia_radicador			= $linea_reporte3['dependencia_radicador'];
				$nombre_dependencia_radicador	= $linea_reporte3['nombre_dependencia'];
				/* Fin recolección de datos extraídos de la base de datos */
				$sql_reporte3_4     = "select d.nombre_dependencia, u.nombre_completo
										from dependencias as d 
										inner join usuarios as u on d.codigo_dependencia = u.codigo_dependencia
										where d.codigo_dependencia='$dependencia_radicador' and u.jefe_dependencia = 'SI'";//estructura sql
				$fila_reporte3_4    = pg_query($conectado,$sql_reporte3_4);//Se enviá la consulta mediante pg_query
				$linea_reporte3_4 	= pg_fetch_array($fila_reporte3_4);//Se pasan los datos a un array para el tratado de estos

				if($linea_reporte3_4==""){ // Si no hay un jefe_dependencia configurado
					$nombre_dependencia = $nombre_dependencia_radicador;
					$jefe_dependencia 	= "No hay un jefe de dependencia configurado todavía.";
				}else{
	   				$nombre_dependencia = $linea_reporte3_4['nombre_dependencia'];
		   			$jefe_dependencia   = $linea_reporte3_4['nombre_completo'];
				}

				$sql_reporte3_2     ="select count(*) 
										from radicado 
										where (fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (asunto is null or asunto ='') and usuario_radicador = '$usuario_radicador'";//estructura sql
				$fila_reporte3_2    = pg_query($conectado,$sql_reporte3_2);//Se enviá la consulta mediante pg_query
				$linea_reporte3_2 	= pg_fetch_array($fila_reporte3_2);//Se pasan los datos a un array para el tratado de estos
	   			$radicados_asunto 	= $linea_reporte3_2['count'];
	   			$sql_reporte3_3     ="select count(*) 
										from radicado 
										where (fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (path_radicado is null or path_radicado = '') and (asunto <> '' or asunto <> null) and usuario_radicador = '$usuario_radicador'";//estructura sql
				$fila_reporte3_3    = pg_query($conectado,$sql_reporte3_3);//Se enviá la consulta mediante pg_query
				$linea_reporte3_3 	= pg_fetch_array($fila_reporte3_3);//Se pasan los datos a un array para el tratado de estos
				$radicados_completo = $count-($linea_reporte3_3['count']+$linea_reporte3_2['count']);
				if($tipo_radicado_vacio != ""){
					if($tipo_radicado_vacio == "asunto"){
						if($linea_reporte3_2['count'] == 0){
							$tipo_radicado_vacio_resultado = "<td class='detalle'>
																<center>
																	".$linea_reporte3_2['count']."
																</center>
															</td>";
						}else{
							$tipo_radicado_vacio_resultado = "<td class='detalle'>
																<center>
																	<button class='botones2' href='' onclick=\"mas_tabla('sin_asunto','$usuario_radicador')\">".$linea_reporte3_2['count']."</button>
																</center>
															</td>";
						}
					}else{
						if($linea_reporte3_3['count'] == 0){
							$tipo_radicado_vacio_resultado = "<td class='detalle'>
																<center>
																	".$linea_reporte3_3['count']."
																</center>
															</td>";
						}else{
							$tipo_radicado_vacio_resultado = "<td class='detalle'>
																	<center>
																		<button class='botones2' href='' onclick=\"mas_tabla('sin_pdf', '$usuario_radicador')\">".$linea_reporte3_3['count']."</button>
																	</center>
																</td>";
						}
					}
				}else{
					$tipo_radicado_vacio_resultado = "<td class='detalle'>
															<center>
																$radicados_completo
															</center>
														</td>";
					if($linea_reporte3_2['count'] == 0){
						$tipo_radicado_vacio_resultado .= "<td class='detalle'>
															<center>
																".$linea_reporte3_2['count']."
															</center>
														</td>";
					}else{
						$tipo_radicado_vacio_resultado .= "<td class='detalle'>
															<center>
																<button class='botones2' href='' onclick=\"mas_tabla('sin_asunto','$usuario_radicador')\">".$linea_reporte3_2['count']."</button>
															</center>
														</td>";
					}
					if($linea_reporte3_3['count'] == 0){
						$tipo_radicado_vacio_resultado .= "<td class='detalle'>
															<center>
																".$linea_reporte3_3['count']."
															</center>
														</td>";
					}else{
						$tipo_radicado_vacio_resultado .= "<td class='detalle'>
															<center>
																<button class='botones2' href='' onclick=\"mas_tabla('sin_pdf', '$usuario_radicador')\">".$linea_reporte3_3['count']."</button>
															</center>
														</td>";
					}
					$tipo_radicado_vacio_resultado .= "<td class='detalle'>
															<center>
																$count
															</center>
														</td>";
				}
	   			if($dependencia_reporte3 != ""){// Si dependencia_reporte3 viene con otro valor diferente a vació
					$tabla_reporte3 .= "<tr>
											<td class='detalle center'>$j</td>
											<td class='detalle'>$nombre_usuario_radicador - ($usuario_radicador)</td>
											".$tipo_radicado_vacio_resultado."
										</tr>";
				}else{
					$tabla_reporte3 .= "<tr>
											<td class='detalle center'>($dependencia_radicador - $nombre_dependencia) - Responsable: $jefe_dependencia</td>
											<td class='detalle'>$nombre_usuario_radicador - $usuario_radicador</td>
											".$tipo_radicado_vacio_resultado."
										</tr>";
				}
				$j++;
			}
			echo "$encabezado_tabla_reporte3 $tabla_reporte3</table><br><br>";//Estructura html
			/*Creación de las estadísticas en forma dona con Pie Chart */
			/* Consulta a base de datos */


			$dona1_agrupado_reporte3 	  = "select count(r.*), r.usuario_radicador, u.nombre_completo from radicado as r
											inner join  usuarios as u on u.login = r.usuario_radicador
											where (r.fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and r.dependencia_radicador ='$dependencia_reporte3'
											group by r.usuario_radicador, u.nombre_completo";//estructura para consulta a la tabla radicado parametrizada con agrupación para realizar un conteo
			$dona1_fila_agrupado_reporte3 = pg_query($conectado,$dona1_agrupado_reporte3);//Se enviá la consulta mediante pg_query
			$dona1_registros_reporte3 	  = pg_num_rows($dona1_fila_agrupado_reporte3); // Se trae la cantidad de filas de la query
			$estructura_dona1_reporte3	  = "google.visualization.arrayToDataTable([['Task', 'Todos los Radicados Vacíos de la Dependencia']";// Se construye la información de la dona
			for($i=0; $i<$dona1_registros_reporte3; $i++){// Se ejecutara "$dona1_registros_reporte3(Ejemplo '2')" veces.
			   	$linea_dona1_reporte3  = pg_fetch_array($dona1_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados
				$estructura_dona1_reporte3  .= ",['".$linea_dona1_reporte3['usuario_radicador']." - ".$linea_dona1_reporte3['nombre_completo']."', ".$linea_dona1_reporte3['count']."]"; // Se construye el data de la dona intermedio
			};
			$estructura_dona1_reporte3 = $estructura_dona1_reporte3."])";// Se construye el data de la dona fin


			$dona2_agrupado_reporte3 	  = "select count(r.*), r.dependencia_radicador, d.nombre_dependencia from radicado as r
											inner join  dependencias as d on d.codigo_dependencia = r.dependencia_radicador
											where (r.fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3')
											group by r.dependencia_radicador, d.nombre_dependencia";//estructura para consulta a la tabla radicado parametrizada con agrupación para realizar un conteo
			$dona2_fila_agrupado_reporte3 = pg_query($conectado,$dona2_agrupado_reporte3);//Se enviá la consulta mediante pg_query
			$dona2_registros_reporte3 	  = pg_num_rows($dona2_fila_agrupado_reporte3); // Se trae la cantidad de filas de la query
			$estructura_dona2_reporte3	  = "google.visualization.arrayToDataTable([['Task', 'Todos los Radicados Vacíos de la Dependencia 2']";// Se construye la información de la dona
			for($i=0; $i<$dona2_registros_reporte3; $i++){// Se ejecutara "$dona1_registros_reporte3(Ejemplo '2')" veces.
			   	$linea_dona2_reporte3  = pg_fetch_array($dona2_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados
				$estructura_dona2_reporte3  .= ",['".$linea_dona2_reporte3['dependencia_radicador']." - ".$linea_dona2_reporte3['nombre_dependencia']."', ".$linea_dona2_reporte3['count']."]"; // Se construye el data de la dona intermedio
			};
			$estructura_dona2_reporte3 = $estructura_dona2_reporte3."])";// Se construye el data de la dona fin


			$estructura_dona3_reporte3	  = "google.visualization.arrayToDataTable([['Task', 'Todos los Radicados Vacíos de la Dependencia 3']";// Se construye la información de la dona
			$dona3_agrupado_reporte3 	  = "select count(*) 
											from radicado 
											where (fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (path_radicado is null or path_radicado = '') and (asunto <> '' or asunto <> null)";//estructura sql
			$dona3_fila_agrupado_reporte3 = pg_query($conectado,$dona3_agrupado_reporte3);//Se enviá la consulta mediante pg_query
			$linea_dona3_reporte3  		  = pg_fetch_array($dona3_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados
			$estructura_dona3_reporte3   .= ",['Radicados sin Imagen Pdf', ".$linea_dona3_reporte3['count']."]"; // Se construye el data de la dona intermedio
			$dona3_2_agrupado_reporte3 	  = "select count(*) 
											from radicado 
											where (fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (asunto = '' or asunto is null)";//estructura sql
			$dona3_2_fila_agrupado_reporte3 = pg_query($conectado,$dona3_2_agrupado_reporte3);//Se enviá la consulta mediante pg_query
			$linea_dona3_2_reporte3  		= pg_fetch_array($dona3_2_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados
			$estructura_dona3_reporte3   .= ",['Radicados sin Asunto', ".$linea_dona3_2_reporte3['count']."]"; // Se construye el data de la dona intermedio
			$estructura_dona3_reporte3 	  = $estructura_dona3_reporte3."])";// Se construye el data de la dona fin


			$estructura_dona4_reporte3	  = "google.visualization.arrayToDataTable([['Task', 'Todos los Radicados Vacíos de la Dependencia 4']";// Se construye la información de la dona
			$dona4_agrupado_reporte3 	  = "select count(r.*), r.usuario_radicador, u.nombre_completo
											from radicado as r 
											inner join usuarios as u on u.login = r.usuario_radicador
											where (r.fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (r.path_radicado is null or r.path_radicado = '') and (r.asunto <> '' or r.asunto <> null) and r.usuario_radicador = '$usuario_radicador'
											group by r.usuario_radicador, u.nombre_completo";//estructura para consulta a la tabla radicado parametrizada con agrupación para realizar un conteo
			$dona4_fila_agrupado_reporte3 = pg_query($conectado,$dona4_agrupado_reporte3);//Se enviá la consulta mediante pg_query
			$linea_dona4_reporte3  		  = pg_fetch_array($dona4_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados

			if($linea_dona4_reporte3==""){
				$count_min_asunto1  = 0;
				$nombre_completo 	= "";
				$usuario_radicador 	= "";
			}else{
				$count_min_asunto1 	= $linea_dona4_reporte3['count'];
				$nombre_completo 	= $linea_dona4_reporte3['nombre_completo'];
				$usuario_radicador 	= $linea_dona4_reporte3['usuario_radicador'];
			}
			$estructura_dona4_reporte3  .= ",['Archivos de $nombre_completo ($usuario_radicador) sin Imagen Pdf', ".$count_min_asunto1."]"; // Se construye el data de la dona intermedio
			$dona4_2_agrupado_reporte3 	  = "select count(r.*), r.usuario_radicador, u.nombre_completo
											from radicado as r 
											inner join usuarios as u on u.login = r.usuario_radicador
											where (r.fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (r.asunto = '' or r.asunto is null) and r.usuario_radicador = '$usuario_radicador'
											group by r.usuario_radicador, u.nombre_completo";//estructura sql
			$dona4_2_fila_agrupado_reporte3 = pg_query($conectado,$dona4_2_agrupado_reporte3);//Se enviá la consulta mediante pg_query
			$linea_dona4_2_reporte3  		= pg_fetch_array($dona4_2_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados

			if($linea_dona4_2_reporte3==""){
				$count_min_asunto 	= 0;
				$nombre_completo 	= "";
				$usuario_radicador 	= "";
			}else{
				$count_min_asunto = $linea_dona4_2_reporte3['count'];
				$nombre_completo 	= $linea_dona4_2_reporte3['nombre_completo'];
				$usuario_radicador 	= $linea_dona4_2_reporte3['usuario_radicador'];
			}
			$estructura_dona4_reporte3     .= ",['Archivos de $nombre_completo ($usuario_radicador) sin Asunto', ".$count_min_asunto."]"; // Se construye el data de la dona intermedio
			$estructura_dona4_reporte3 = $estructura_dona4_reporte3."])";// Se construye el data de la dona fin


			$estructura_dona5_reporte3	  = "google.visualization.arrayToDataTable([['Task', 'Todos los Radicados Vacíos de la Dependencia 5']";// Se construye la información de la dona
			if($tipo_radicado_vacio == "path_radicado"){
				$dona5_agrupado_reporte3 	  = "select count(r.*), r.usuario_radicador, u.nombre_completo, r.dependencia_radicador, d.nombre_dependencia
												from radicado as r 
												inner join usuarios as u on u.login = r.usuario_radicador
												inner join dependencias as d on d.codigo_dependencia = r.dependencia_radicador
												where (r.fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (r.path_radicado is null or r.path_radicado = '') and (r.asunto <> '' or r.asunto <> null)
												group by r.usuario_radicador, u.nombre_completo, r.dependencia_radicador, d.nombre_dependencia";//estructura para consulta a la tabla radicado parametrizada con agrupación para realizar un conteo
				$dona5_fila_agrupado_reporte3 = pg_query($conectado,$dona5_agrupado_reporte3);//Se enviá la consulta mediante pg_query
				$dona5_registros_reporte3 	  = pg_num_rows($dona5_fila_agrupado_reporte3); // Se trae la cantidad de filas de la query
				for($i=0; $i<$dona5_registros_reporte3; $i++){// Se ejecutara "$dona1_registros_reporte3(Ejemplo '2')" veces.
					$linea_dona5_reporte3  		  = pg_fetch_array($dona5_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados
					$estructura_dona5_reporte3  .= ",['Archivos de ".$linea_dona5_reporte3['nombre_completo']."(".$linea_dona5_reporte3['usuario_radicador'].") Perteneciente a la Dependencia (".$linea_dona5_reporte3['dependencia_radicador']." - ".$linea_dona5_reporte3['nombre_dependencia'].") que no tienen Imagen Pdf', ".$linea_dona5_reporte3['count']."]"; // Se construye el data de la dona intermedio
				}
			}else{
				$dona5_2_agrupado_reporte3 	  = "select count(r.*), r.usuario_radicador, u.nombre_completo, r.dependencia_radicador, d.nombre_dependencia
												from radicado as r 
												inner join usuarios as u on u.login = r.usuario_radicador
												inner join dependencias as d on d.codigo_dependencia = r.dependencia_radicador
												where (r.fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (r.asunto = '' or r.asunto is null)
												group by r.usuario_radicador, u.nombre_completo, r.dependencia_radicador, d.nombre_dependencia";//estructura sql
				$dona5_2_fila_agrupado_reporte3 = pg_query($conectado,$dona5_2_agrupado_reporte3);//Se enviá la consulta mediante pg_query
				$dona5_2_registros_reporte3 	  = pg_num_rows($dona5_2_fila_agrupado_reporte3); // Se trae la cantidad de filas de la query
				for($i=0; $i<$dona5_2_registros_reporte3; $i++){// Se ejecutara "$dona1_registros_reporte3(Ejemplo '2')" veces.
					$linea_dona5_2_reporte3  		= pg_fetch_array($dona5_2_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados
					$estructura_dona5_reporte3     .= ",['Archivos de ".$linea_dona5_2_reporte3['nombre_completo']."(".$linea_dona5_2_reporte3['usuario_radicador'].") Perteneciente a la Dependencia (".$linea_dona5_2_reporte3['dependencia_radicador']." - ".$linea_dona5_2_reporte3['nombre_dependencia'].") que no tienen Asunto', ".$linea_dona5_2_reporte3['count']."]"; // Se construye el data de la dona intermedio
				}
			}
			$estructura_dona5_reporte3 = $estructura_dona5_reporte3."])";// Se construye el data de la dona fin


			$estructura_dona6_reporte3	  = "google.visualization.arrayToDataTable([['Task', 'Todos los Radicados Vacíos de la Dependencia 6']";// Se construye la información de la dona
			if($tipo_radicado_vacio == "path_radicado"){
				$dona6_agrupado_reporte3 	  = "select count(r.*), r.usuario_radicador, u.nombre_completo
												from radicado as r 
												inner join usuarios as u on u.login = r.usuario_radicador
												where (r.fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (r.path_radicado is null or r.path_radicado = '') and (r.asunto <> '' or r.asunto <> null) and r.dependencia_radicador ='$dependencia_reporte3'
												group by r.usuario_radicador, u.nombre_completo";//estructura para consulta a la tabla radicado parametrizada con agrupación para realizar un conteo
				$dona6_fila_agrupado_reporte3 = pg_query($conectado,$dona6_agrupado_reporte3);//Se enviá la consulta mediante pg_query
				$dona6_registros_reporte3 	  = pg_num_rows($dona6_fila_agrupado_reporte3); // Se trae la cantidad de filas de la query
				for($i=0; $i<$dona6_registros_reporte3; $i++){// Se ejecutara "$dona1_registros_reporte3(Ejemplo '2')" veces.
					$linea_dona6_reporte3  		  = pg_fetch_array($dona6_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados
					$estructura_dona6_reporte3  .= ",['Archivos de ".$linea_dona6_reporte3['nombre_completo']."(".$linea_dona6_reporte3['usuario_radicador'].") sin Imagen Pdf', ".$linea_dona6_reporte3['count']."]"; // Se construye el data de la dona intermedio
				}
			}else{
				$dona6_2_agrupado_reporte3 	  = "select count(r.*), r.usuario_radicador, u.nombre_completo
												from radicado as r 
												inner join usuarios as u on u.login = r.usuario_radicador
												inner join dependencias as d on d.codigo_dependencia = r.dependencia_radicador
												where (r.fecha_radicado between '$fecha_inicial_reporte3' and '$fecha_final_reporte3') and (r. asunto = '' or r.asunto is null) and r.dependencia_radicador ='$dependencia_reporte3'
												group by r.usuario_radicador, u.nombre_completo";//estructura sql
				$dona6_2_fila_agrupado_reporte3 = pg_query($conectado,$dona6_2_agrupado_reporte3);//Se enviá la consulta mediante pg_query
				$dona6_2_registros_reporte3 	  = pg_num_rows($dona6_2_fila_agrupado_reporte3); // Se trae la cantidad de filas de la query
				for($i=0; $i<$dona6_2_registros_reporte3; $i++){// Se ejecutara "$dona1_registros_reporte3(Ejemplo '2')" veces.
					$linea_dona6_2_reporte3  		= pg_fetch_array($dona6_2_fila_agrupado_reporte3);// Se pasan los datos a un array para ser procesados
					$estructura_dona6_reporte3     .= ",['Archivos de ".$linea_dona6_2_reporte3['nombre_completo']."(".$linea_dona6_2_reporte3['usuario_radicador'].") sin Asunto', ".$linea_dona6_2_reporte3['count']."]"; // Se construye el data de la dona intermedio
				}
			}
			$estructura_dona6_reporte3 = $estructura_dona6_reporte3."])";// Se construye el data de la dona fin



			echo "<script> 
			google.charts.load('current', {packages:['corechart']});
		    google.charts.setOnLoadCallback(dona_por_dependencia1);
		    function dona_por_dependencia1() {
		        var data = $estructura_dona1_reporte3;

		        var options = {
		          title: 'Organizado por Usuarios que deben Terminar Radicados en la Dependencias ( $dependencia_radicador - $nombre_dependencia )',
		          pieHole: 0.4,
		        };
		        var chart = new google.visualization.PieChart(document.getElementById('estadistica_dona1_reporte3'));
		        chart.draw(data, options);
		    };

		    google.charts.setOnLoadCallback(dona_por_dependencia2);
		    function dona_por_dependencia2() {
		        var data = $estructura_dona2_reporte3;

		        var options = {
		          title: 'Organizado por Dependencias que deben Terminar Radicados',
		          pieHole: 0.4,
		        };
		        var chart = new google.visualization.PieChart(document.getElementById('estadistica_dona2_reporte3'));
		        chart.draw(data, options);
		    };

		    google.charts.setOnLoadCallback(dona_por_dependencia3);
		    function dona_por_dependencia3(){
		        var data = $estructura_dona3_reporte3;

		        var options = {
		          title: 'Todos los Radicados Vacíos Por Tipo',
		          pieHole: 0.4,
		        };
		        var chart = new google.visualization.PieChart(document.getElementById('estadistica_dona3_reporte3'));
		        chart.draw(data, options);
		    };

		    google.charts.setOnLoadCallback(dona_por_dependencia4);
		    function dona_por_dependencia4() {
		        var data = $estructura_dona4_reporte3;

		        var options = {
		          title: 'Organizado por Usuario',
		          pieHole: 0.4,
		        };
		        var chart = new google.visualization.PieChart(document.getElementById('estadistica_dona4_reporte3'));
		        chart.draw(data, options);
		    };

		    google.charts.setOnLoadCallback(dona_por_dependencia5);
		    function dona_por_dependencia5() {
		        var data = $estructura_dona5_reporte3;

		        var options = {
		          title: 'Personas Organizado por Dependencia que Deben Terminar Radicados',
		          pieHole: 0.4,
		        };
		        var chart = new google.visualization.PieChart(document.getElementById('estadistica_dona5_reporte3'));
		        chart.draw(data, options);
		    };

		    google.charts.setOnLoadCallback(dona_por_dependencia6);
		    function dona_por_dependencia6() {
		        var data = $estructura_dona6_reporte3;

		        var options = {
		          title: 'Usuarios en la Dependencia con Radicados sin Terminar',
		          pieHole: 0.4,
		        };
		        var chart = new google.visualization.PieChart(document.getElementById('estadistica_dona6_reporte3'));
		        chart.draw(data, options);
		    };
			</script>
			<div id='estadistica_dona1_reporte3' style='width: 900px; height: 500px;' class='center'></div>
			<div id='estadistica_dona2_reporte3' style='width: 900px; height: 500px; position: relative; float: left;' hide></div>
			<div id='estadistica_dona3_reporte3' style='width: 800px; height: 450px; position: relative; float: right;' hide></div>
			<div id='estadistica_dona4_reporte3' style='width: 900px; height: 500px;' class='center' hide></div>
			<div id='estadistica_dona5_reporte3' style='width: 900px; height: 500px;' class='center' hide></div>
			<div id='estadistica_dona6_reporte3' style='width: 900px; height: 500px;' class='center' hide></div>";
		}		
	break;
	/*****************************************************************************************
	Fin Case reporte3 
	/*****************************************************************************************/
	case 'reporte3_2':
		echo "<script>$('#estadistica_dona1_reporte3').hide();
						  $('#estadistica_dona2_reporte3').hide();
						  $('#estadistica_dona3_reporte3').hide();
						  $('#estadistica_dona4_reporte3').hide();</script>";//Se oculta la dona 1 y se habilita la dona 2
		/* Recibe variables desde la funcion cargar_reporte_radicados_vacios() desde reporte3_radicados_vacios.php */
		$tipo         	      		= $_POST['tipo'];
		$usuario 	        		= $_POST['usuario'];
		$fecha_inicio_reporte3_2 	= $_POST['fecha_inicial'];
		$fecha_fin_reporte3_2       = $_POST['fecha_final'];
		/* Fin Recibe variables desde la funcion cargar_reporte_radicados_vacios() desde reporte3_radicados_vacios.php */
		/* Re estructuración de variables recogidas */
		$fecha_inicial_reporte3_2 	= "$fecha_inicio_reporte3_2 00:00:00";// Ejemplo: 16/04/2020 00:00:00
		$fecha_final_reporte3_2 	= "$fecha_fin_reporte3_2 23:59:59";// Ejemplo: 16/04/2020 23:59:59
		switch ($tipo) {
			case 'sin_asunto':
				$sql_reporte3_2 	= "select r.numero_radicado, u.nombre_completo, r.usuario_radicador, r.fecha_radicado
										from radicado as r
										inner join usuarios as u on u.login = r.usuario_radicador
										where (r.fecha_radicado between '$fecha_inicial_reporte3_2' and '$fecha_final_reporte3_2') and r.usuario_radicador = '$usuario' and (r.asunto is null or r.asunto ='')
										order by r.fecha_radicado";//estructura sql
				break;
			case 'sin_pdf':
				$sql_reporte3_2 	= "select r.numero_radicado, u.nombre_completo, r.usuario_radicador, r.fecha_radicado
										from radicado as r
										inner join usuarios as u on u.login = r.usuario_radicador
										where (r.fecha_radicado between '$fecha_inicial_reporte3_2' and '$fecha_final_reporte3_2') and r.usuario_radicador = '$usuario' and (path_radicado is null or path_radicado = '') and (asunto <> '' or asunto <> null)
										order by r.fecha_radicado";//estructura sql
				break;
			default:
				break;
		}
		$fila_reporte3_2  = pg_query($conectado,$sql_reporte3_2);//Se enviá la consulta mediante pg_query
		$registros_reporte3_2 = pg_num_rows($fila_reporte3_2); // Se trae la cantidad de filas de la query
	   	if($registros_reporte3_2==0){ // Si la consulta viene vaciá
        	echo "<h3>No hay radicados recibidos en el rango de fecha desde el $fecha_inicial_reporte3_2 hasta el $fecha_fin_reporte3_2</h3><br><br><button class='botones' onclick='cargar_reporte_radicados_vacios()'>Volver</button></center>"; // Estructura html
		}else{
			$usuario_tabla = "<table border='0'>
											<tr class='center'>
												<td class='descripcion'>Id</td>
												<td class='descripcion'>Numero Radicado</td>
												<td class='descripcion'>Usuario</td>
												<td class='descripcion'>Fecha</td>
											</tr>";// Estructura html
			$j = 1;
			for($i=0; $i<$registros_reporte3_2 ; $i++){ // Se ejecutara la cantidad de $registros_reporte3
				$linea_reporte3_2 			= pg_fetch_array($fila_reporte3_2);//Se pasan los datos a un array para el tratado de estos
				/* Recolección de datos extraídos de la base de datos */
				$numero_radicado 						= $linea_reporte3_2['numero_radicado'];
				$nombre_completo 	= $linea_reporte3_2['nombre_completo'];
				$usuario_radicador 			= $linea_reporte3_2['usuario_radicador'];
				$fecha_radicado		= $linea_reporte3_2['fecha_radicado'];
				$usuario_tabla .= "<tr>
									<td class='detalle'>".$j."</td>
									<td class='detalle'>".$numero_radicado."</td>
									<td class='detalle'>(".$usuario_radicador.") ".$nombre_completo."</td>
									<td class='detalle'>".$fecha_radicado."</td>
								</tr>";
			}
			$usuario_tabla .= "</tabla><center><button class='botones' onclick='cargar_reporte_radicados_vacios()'>Volver</button></center><br><br>";
			echo $usuario_tabla;
		}		
	break;
	/*****************************************************************************************
	Inicio Case revisar_existencias_radicados revisa la existencia de un radicado sin terminar
	/*****************************************************************************************
	* @brief Recibe desde 
	* - include/js/verficar_radicado_sin_terminar.js
	* Revisa la carpeta de la radicacion en busca de borradores pendientes
	* @return {string} Devuelve una cadena con el resultado de la búsqueda
	*****************************************************************************************/	
	case 'revisar_existencias_radicados':
		$carpeta = @scandir('..//bodega_pdf/plantilla_generada_tmp/');// Enumera los ficheros y directorios ubicados en la ruta especificada
		if (count($carpeta) > 3){// Valida que sea mayor $carpeta a 2 conteniendo por lo menos un fichero o directorio
		    echo 'NO VACIO';
		}else{
		    echo 'VACIO';
		}
		break;
	/*****************************************************************************************
	Fin Case revisar_existencias_radicados revisa la existencia de un radicado sin terminar
	/*****************************************************************************************/

	/*****************************************************************************************
	Inicio funcion para cargar listado de expedientes por dependencia y serie 
	/*****************************************************************************************
	* @brief Recibe desde 
	** - bandejas/entrada/visualiza_radicado.php[function validar_input_formulario_serie_subserie_nrr(codigo_dependencia,div_resultado,input_serie,valor_buscado)]
	* para listar mediante <div> los expedientes que existen en la base de datos con la relación dependencia-serie según los parámetros recibidos. Adicionalmente recibe la variable "$tipo_formulario" para que en la acción de este archivo asigne en el switch el listado de expedientes con el "onclick" que corresponda
	* @param {string} ($dependencia) Es obligatorio y se usa para filtrar la consulta.
	* @param {string} ($search_expediente) Es obligatorio y se usa para filtrar la consulta.
	* @param {string} ($serie) Es obligatorio y se usa para filtrar la consulta.
	* @param {string} ($tipo_formulario) Es opcional y se usa para consultar nombre de serie, subserie para asignar en los input de las variables en el formulario.
	* @return {string} String con los <div> del listado de expedientes que existen en la base de datos con la relación dependencia-serie según los parámetros recibidos o el mensaje de error con la opción para crear el expediente.
	*****************************************************************************************/	
	case 'seleccionar_expediente': 
		$dependencia 		= $_POST['dependencia'];
		$search_expediente 	= $_POST['search_expediente'];
		$serie 				= $_POST['serie'];
		$tipo_formulario 	= $_POST['tipo_formulario'];

		if($search_expediente==""){
			$query_expediente="select * from expedientes where serie='$serie' and dependencia_expediente='$dependencia' order by fecha_inicial desc";
		}else{
			$query_expediente="select * from expedientes where serie='$serie' and dependencia_expediente='$dependencia' and (id_expediente ilike '%$search_expediente%' or nombre_expediente ilike '%$search_expediente%') order by fecha_inicial desc limit 10";
		}
		/*Aquí se ejecuta la query*/
    	$fila_consulta_expedientes  = pg_query($conectado,$query_expediente);

    	/*Se trae la cantidad de filas de la query*/
    	$registros_consulta_expedientes = pg_num_rows($fila_consulta_expedientes);
    			
		if($registros_consulta_expedientes==0){
			echo "No existen expedientes con numero de expediente o nombre <h2><b><u>$search_expediente</u></b></h2> en la dependencia <b>$dependencia</b> con la serie <b>$serie</b> <br> <h3>Para crear un expediente nuevo haga click <a style='text-decoration: none;color: red;' href='javascript:carga_creacion_expedientes();'>aquí</a>
			</h3>";
		}else{
			for ($i=0; $i < $registros_consulta_expedientes ; $i++){
		    	$linea_consulta_expedientes = pg_fetch_array($fila_consulta_expedientes);  

				$id_expediente 		= strtoupper($linea_consulta_expedientes['id_expediente']);
				$nombre_expediente 	= strtoupper($linea_consulta_expedientes['nombre_expediente']);
				$search_expediente 	= strtoupper($search_expediente);

				$id_expediente1   = trim(str_ireplace($search_expediente, "<b><font color='red'>$search_expediente</font></b>", $id_expediente)); // Resalta con rojo el valor buscado
				$nombre_expediente1   = trim(str_ireplace($search_expediente, "<b><font color='red'>$search_expediente</font></b>", $nombre_expediente)); // Resalta con rojo el valor buscado

				switch ($tipo_formulario) {
					case 'trd_exp':
					case 'nrr':
						echo "<div class='art' onclick=\"cargar_input_expediente_nrr('$id_expediente','$nombre_expediente','$tipo_formulario')\">($id_expediente1) $nombre_expediente1</div>";
						break;
					
					default:
						echo "<div class='art' onclick=\"cargar_input_expediente('$id_expediente','$nombre_expediente')\">($id_expediente1) $nombre_expediente1</div>";
						break;
				}	
		    } 
		}
		break;	

	/* Inicio de la tabla_ubicacion_fisica_radicados*/
	case 'tabla_ubicacion_fisica_radicados':
		//Se agrega este caso para que cuando le de click en algun resultado en el archivo buscador_ubicacion_fisica.php

		session_start();
		$usuario 				= $_SESSION['login']; 		// Genera Usuario 
		$radicado_concatenado 	= $_POST['radicado_concatenado'];
		$tipo_boton 			= $_POST['tipo_boton'];

		// Extraigo cada uno de los readicados separados por coma	
		$usu  = explode(",", $radicado_concatenado);
		$max  = sizeof($usu);
		$max2 = $max-1;

		$radicado = "";
		//se recorre cada registro separado por coma
		for ($p=0; $p < $max2; $p++) {  
			$radicado_con_coma 	= $usu[$p];
			$radicado 			.= "'$radicado_con_coma',";
		}
		/*Este subtring se hace para borrar la ultima coma de la variable $radicado cuando llega al final del bucle for*/
		$radicado = substr($radicado,0,-1);

		/*Query para traer radicados que estan en el campo de concatenar_radicado en el archivo: index_ubicacion_radicados_fisicos.php*/
		$query_ubicacion ="select r.numero_radicado,uf.usuario_actual,uf.usuario_anterior,uf.fecha,r.asunto, r.fecha_radicado, r.dependencia_actual, de.nombre_dependencia, r.clasificacion_radicado, r.clasificacion_seguridad, r.folios from ubicacion_fisica uf, radicado r  join dependencias de on de.codigo_dependencia = r.dependencia_actual where uf.numero_radicado = r.numero_radicado and uf.numero_radicado in($radicado) limit 10";
		
		if(isset($_POST['carga_query'])){ /* Si recibe la variable 'carga_query' imprime la query y corta el ciclo. */
			/* A la query se le elimina la parte de "limit 10 y se codifica para enviar por GET facilmente." */
			echo urlencode(str_replace("limit 10", "order by dependencia_actual, clasificacion_radicado", $query_ubicacion));
			break;
		}

		if($tipo_boton=='recibir_documentos'){
			$nombre_boton1 = "Recibir documentos físicos";
		}else{
			$nombre_boton1 = "Entregar documentos físicos en planilla";
		}

		/* Aqui se ejecuta la query */
		$fila_query_ubicacion = pg_query($conectado,$query_ubicacion);

		/* Se trae las filas de la query */
		$registros_query_ubicacion 	= pg_num_rows($fila_query_ubicacion);

		//Condicion si la consulta trae resultados
		if($registros_query_ubicacion>0 ){
			echo'<hr><br>
				<h1 style="margin-top:-10px;">Listado radicados a recibir en fisico</h1>';
		//Titulos de tabla
		echo"<table border='0' width='100%'>
			<tr>
				<td class='descripcion center' style='10px;'>Id</td>
				<td class='descripcion center' style='160px;'>Radicado</td>
				<td class='descripcion center' >Asunto</td>
				<td class='descripcion center' style='150px;'>Usuario Actual</td>
				<td class='descripcion center' style='150px;'>Recibido desde</td>
				<td class='descripcion center' style='200px;'></td>
			</tr>";
				//se imprime las filas del resuultado de la query con bucle for que viene desde la variable $registros_query_ubicacion

				//La variable contador se usa para la columna id como autoincrementable
				$contador = 1;
				//Se recorre el resultado de la query con un for
	    		for ($i=0; $i < $registros_query_ubicacion ; $i++){

			    	$linea_consulta_ubicacion = pg_fetch_array($fila_query_ubicacion); 	
					$numero_radicado 	 	= $linea_consulta_ubicacion['numero_radicado'];
					$asunto  	 			= $linea_consulta_ubicacion['asunto'];
					$usuario_anterior	 	= $linea_consulta_ubicacion['usuario_anterior'];
					$usuario_actual		 	= $linea_consulta_ubicacion['usuario_actual'];
					$desde_fecha		 	= $linea_consulta_ubicacion['fecha'];

					if($asunto==""){
						$asunto1 = "<h2 style='color:red;'>Sin Asunto todavía</h2>";
					}else{
						$asunto1 = $asunto;
					}


					/* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2020"*/
					require_once "../include/genera_fecha.php";
					$fecha_modificacion=$b->traducefecha($desde_fecha);	
					
					echo "
					<tr class='detalle center fila_serie' onclick=\"\">
						<td>$contador</td>
						<td> $numero_radicado </td>
						<td> $asunto1 </td>
						<td> $usuario_actual </td>
						<td> $fecha_modificacion </td>
						<td> <button class='botones2' onclick='quitar_radicado_lista(\"$numero_radicado\")'>Sacar del listado</button> 
						</td>
					</tr>	";
					$contador++;

				}//Fin for
				/* Inicio imprimir tabla con input para validar firma electrónica mediante contraseña de usuario y boton submit */
				echo "			
					<tr class='tabla_contrasena'>
						<td></td>
						<td class='descripcion'>Observaciones: </td>
						<td colspan='3'>
							<div class='detalle form center'>
								<textarea  id='observaciones_planilla1' rows='2' style='width:98%;padding:5px;' placeholder='Ingrese las observaciones a la planilla que está generando. Sea lo más específico posible' title='Ingrese las observaciones a la planilla que está generando. Sea lo más específico posible'></textarea>
							</div>
						</td>
						<td></td>
					</tr>
					<tr class='tabla_contrasena'>
						<td></td>
						<td></td>
						<td class='descripcion'>Contraseña del usuario <b>$usuario</b> para validar <u><b>Firma Electrónica</b></u></td>
						<td>
							<div class='detalle form center'>
								<!---Inicio de contenedor pára la aprobacion del documento-->
			                    <input type='password' autocomplete='off' id='contr_confirma_aprobado' title='Ingrese su password para aprobar aquí.' placeholder='Ingrese su password para aprobar aqui'> 
	                          
								<div id='error_contr_confirma_aprobado_ubicacion_fisica' class='errores'>La contraseña no corresponde al usuario que aprueba el documento</div>
								<div id='validar_aprobacion_ubicacion_fisica'></div>
								<!---Fin de contenedor pára la aprobacion del documento-->	
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='6'>
							<br>
							<center id='boton_validar_aprueba'>
								<div class='botones' onclick='validar_aprueba_firma();'>$nombre_boton1</div>
							</center>
						</td>
					</tr>
				</table>";
			}//Fin de condicion si la consulta trae algun resultado
			else{//Inicio else la consulta no trae ninguna resultado	
				echo "<h2> <font color='red'>La tabla esta vacia</h2>";
				echo "<br>";
				
			}//Fin else la consulta no trae ninguna resultado

		break;//Fin de tabla ubicacion_fisica_radicados

	/* Fin de la tabla_ubicacion_fisica_radicados*/
	/*****************************************************************************************/ 		

	case 'validar_aprueba_firma': 	// Recibe desde include/js/funciones_radicacion_salida.js[validar_aprueba_firma()]
		$tipo 			=	$_POST['tipo'];
        $solicitante 	=	$_POST['login'];
        $contr 			=	$_POST['pass']; 

		$isql 				="select * from usuarios where login = trim(upper('$solicitante')) and pass = md5('$contr')";
		$fila_resultado  	= pg_query($conectado,$isql);
		$linea_resultado  	= pg_fetch_array($fila_resultado);
		
	    if($linea_resultado== false){
	        echo "false";
	    }else{
			$result = $linea_resultado[0];
	    	echo "true";
	    }
        break;

    /*****************************************************************************************
		El case validar_carga_aprueba_firma verifica si el usuario que firma el documento tiene asignada su segunda clave
		para hacer la firma electrónica. 
	/*****************************************************************************************
		* @brief Recibe desde radicacion/radicacion_interna/funciones_radicacion_interna.js[function cargar_documento(radicado)]
		* @param {string} ($login_firmante) Es obligatorio y se usa para filtrar la consulta
		* @return {string} String con un <script> para ejecutar y un contenido en html con botones para las siguientes acciones.
	*****************************************************************************************/	    
	case 'validar_carga_aprueba_firma': 	// Recibe desde include/js/funciones_radicacion_salida.js[carga_aprueba(nombre_completo,login_aprueba)]
		require_once('../login/validar_inactividad.php');// Se valida la inactividad 

		$cargo_firmante 	= $_POST['cargo_firmante']; 	// cargo del firmante
		$login_firmante 	= $_POST['login_firmante']; 	// login del firmante
		$nombre_completo 	= $_POST['nombre_completo']; 	// Nombre completo del firmante
		$nombre_usuario 	= $_POST['nombre_usuario'];		// Nombre completo del usuario que hace la transaccion
		$numero_radicado 	= $_POST['numero_radicado'];	
		$validar 		 	= $_POST['validar'];			// Tipo de validacion

		/* Inicio determinando si el firmante tiene firma electrónica habilitada */
		$query_valida_firma_electronica 	= "select pass2, path_foto from usuarios where login='$login_firmante'";
		$fila_valida_firma_electronica 		= pg_query($conectado,$query_valida_firma_electronica);
		$linea_valida_firma_electronica 	= pg_fetch_array($fila_valida_firma_electronica);
		$pass2 								= $linea_valida_firma_electronica['pass2']; 
		$path_foto 							= $linea_valida_firma_electronica['path_foto']; 

		$mensaje_firma = "";

		switch ($validar) {
			case 'firma':
				$div_contenido_carga 	= "contenido_carga_firmante";
				$div_tiene_pass 		= "firmante_tiene_pass2";
				$muestra_fila 			= "muestra_fila_firmante()";
				$transaccion 			= "<u>".strtoupper($validar)."</u>";
				break;
			
			case 'aprueba':
				$div_contenido_carga 	= "contenido_carga_aprueba";
				$div_tiene_pass 		= "aprueba_tiene_pass2";
				$muestra_fila 			= "muestra_fila_aprueba()";
				$transaccion 			= "<u>".strtoupper($validar)."</u>";
				break;			
			default:
				$codigo_revisa = substr($validar, -1);

				$div_contenido_carga 	= "contenido_carga_revisa".$codigo_revisa;
				$div_tiene_pass 		= "revisa".$codigo_revisa."_tiene_pass2";
				$muestra_fila 			= "despliega_revisa_doc($codigo_revisa)";
				$transaccion 			= "<u> REVISA (".$codigo_revisa.")</u>";
				break;
		}
		
		if($pass2=="" or $pass2 ==" " or $pass2=="null" or $pass2=="NULL"){
			$valor_tiene_pass = "NO";

			$color_boton 	= '#f5672f';

			if($nombre_completo==$nombre_usuario){
				$mensaje_firma_electronica 	= "Usted no ha habilitado todavía su contraseña para firma electrónica. Si desea habilítela <a style='text-decoration: none;color: red;' href='javascript:gestionar_datos_usuario();'>aquí</a>";
				$mensaje_firma.='Usted no ha habilitado todavía su contraseña para firma electrónica.';
			}else{
				$mensaje_firma_electronica 	= "Usuario no ha habilitado la firma electrónica";
				$mensaje_firma.="El usuario $login_firmante ($nombre_completo) no ha habilitado todavía su contraseña para firma electrónica.";
			}	
		}else{
			$color_boton 				='#2aa646';
			$mensaje_firma_electronica 	= "Usuario con Firma Electrónica Habilitada";

			if($nombre_completo==$nombre_usuario){
				$mensaje_firma.="Usted SI tiene habilitada su contraseña para firma electrónica.";
			}else{
				$mensaje_firma.="El usuario $login_firmante ($nombre_completo) tiene habilitada su contraseña para firma electrónica.";
			}
			$valor_tiene_pass = "SI";
		}

		/* Devuelve el div con la información del usuario */
		echo "<script>$('#$div_tiene_pass').val('$valor_tiene_pass');</script><div style='border-radius: 20px; border: 2px solid rgba(255,255,255,.5); cursor: pointer; display: block; float: left; font-size: 15px; position: relative; padding:5px; width:280px;' title='$mensaje_firma' onclick='$muestra_fila;'><b>Persona que $transaccion el Documento</b><br><img src='$path_foto' style='width: 30px; border-radius: 20px; float: left;'><div title='Haga click si quiere cambiar el usuario que $validar'>$nombre_completo<br>($cargo_firmante)</div><div class='art_exp center' style='background: $color_boton; color: #FFFFFF;'>$mensaje_firma_electronica</div></div></div>";
					
		/* Usando javascript asigno al valor de #contenido_carga_firmante el div del firmante/aprueba/revisa. */
		echo "<script>$('#$div_contenido_carga').val(\"<div style='border-radius: 20px; border: 2px solid rgba(255,255,255,.5); cursor: pointer; display: block; float: left; font-size: 15px; position: relative; padding:5px; width:280px;' title='$mensaje_firma' onclick='$muestra_fila;'><b>Persona que $transaccion el Documento</b><br><img src='$path_foto' style='width: 30px; border-radius: 20px; float: left;'><div title='Haga click si quiere cambiar el usuario que $validar'>$nombre_completo<br>($cargo_firmante)</div><div class='art_exp center' style='background: $color_boton; color: #FFFFFF;'>$mensaje_firma_electronica</div></div>\");</script>";

		break;

	case 'validar_usuario_actual':
		$alto_visor 			= $_POST['alto_visor'];
		$ancho_visor 			= $_POST['ancho_visor'];
		$caracteres_dependencia = $_POST['caracteres_dependencia'];
		$login  				= $_POST['login'];
		$path_adjunto   		= $_POST['path_adjunto'];
		$radicado  				= $_POST['radicado'];
		$tipo 					= $_POST['tipo'];

		$query_validar_usuario = "select usuarios_visor from radicado where numero_radicado='$radicado'";
		/*Aqui se ejecuta la query*/
    	$fila_validar_usuario 	= pg_query($conectado,$query_validar_usuario);
    	$linea_validar_usuario 	= pg_fetch_array($fila_validar_usuario);  

		if(!isset($_SESSION)){
			session_start();
  		}
		$usuario_actual 		= $linea_validar_usuario['usuarios_visor'];

		/* Condicion para visualizar radicado libremente o con restriccion por usuarios */
		$codigo_entidad = $_SESSION["codigo_entidad"];
		if($codigo_entidad=='AV1'){
			if($path_adjunto==""){
				echo "<font color='red'>No se ha digitalizado el PDF correspondiente a este radicado. </font>";
			}else{
			    echo "<object data='bodega_pdf/$tipo/$path_adjunto' type='application/pdf' style='height: $alto_visor; width: $ancho_visor'></object>";
			}
		}else{
			if (strpos($usuario_actual, $login) !== false) { // Validar si $login se encuentra entre los usuarios_actuales
				if($path_adjunto==""){
					echo "<font color='red'>No se ha digitalizado el PDF correspondiente a este radicado. </font>";
				}else{
				    echo "<object data='bodega_pdf/$tipo/$path_adjunto' type='application/pdf' style='height: $alto_visor; width: $ancho_visor'></object>";
				}
			}else{
				echo "<h3 style='color:red;'>Usted no tiene permitido ver éste radicado. Puede solicitar que le envíe el documento alguno de los usuarios: $usuario_actual</h3>";
			}
		}
		break;

	/*****************************************************************************************
		Inicio case verifica_fecha_valida para validar si la fecha_inicial  listado de expedientes por serie, subserie y dependencia 
	/*****************************************************************************************
		* @brief Recibe desde radicacion/radicacion_interna/funciones_radicaion_interna.js[function tratar_expediente()]
		* @param {string} ($dependencia) Es obligatorio y se usa para filtrar la consulta.
		* @param {string} ($serie) Es obligatorio y se usa para filtrar la consulta.
		* @param {string} ($subserie) Es obligatorio y se usa para filtrar la consulta.
		* @return {string} String con los <div> del listado de expedientes que existen en la base de datos con la relación dependencia-serie-subserie según los parámetros recibidos.
	*****************************************************************************************/	
	case 'verifica_fecha_valida':
		$fecha_ini = $_POST['fecha_ini'];
		$fecha_fin = $_POST['fecha_fin'];
		$id_cambio = $_POST['id_cambio'];

		if($id_cambio==""){
			$query_consulta_fecha_valida = "select * from cambios_organico_funcionales order by fecha_inicial_cambio";
		}else{
			$query_consulta_fecha_valida = "select * from cambios_organico_funcionales where id_cambio_organico_funcional != '$id_cambio' order by fecha_inicial_cambio";
		}

		$fila_fecha_valida 		= pg_query($conectado,$query_consulta_fecha_valida);
    	$registros_fecha_valida = pg_num_rows($fila_fecha_valida);
    	
    	$respuesta = "";		
		if($registros_fecha_valida!=0){
			for ($i=0; $i < $registros_fecha_valida; $i++) { 
		    	$linea_fecha_valida 	= pg_fetch_array($fila_fecha_valida);
		    	$fecha_final_cambio 	= $linea_fecha_valida['fecha_final_cambio'];  
		    	$fecha_inicial_cambio 	= $linea_fecha_valida['fecha_inicial_cambio'];  
		    	$id_cambio_of 		 	= $linea_fecha_valida['id_cambio_organico_funcional'];  

		    	if(($fecha_ini >= $fecha_inicial_cambio) && ($fecha_ini <= $fecha_final_cambio)){
		    		$respuesta.= "$fecha_ini está en el rango del ID(<b>$id_cambio_of</b>)-[$fecha_inicial_cambio al $fecha_final_cambio]) por lo que no se puede crear en esta fecha el cambio organico-funcional";
		    	}else{
		    		if(($fecha_ini <= $fecha_inicial_cambio) && ($fecha_fin=="")){
					    $respuesta.= "Si esta va a ser la versión actual del cambio organico-funcional, ya existe un rango desde la fecha ($fecha_ini) hasta hoy por lo que no se puede crear en esta fecha el cambio organico-funcional";
			    	}else{
		    			if(($fecha_fin >= $fecha_inicial_cambio) && ($fecha_fin <= $fecha_final_cambio)){
				    		$respuesta.= "$fecha_fin está en el rango del ID(<b>$id_cambio_of</b>)-[$fecha_inicial_cambio al $fecha_final_cambio]) por lo que no se puede crear en esta fecha el cambio organico-funcional";
		    			}

			    	}
		    		$respuesta.= "";
		    	}
			}
	    }

	    if($respuesta==""){
	    	$respuesta="permite_crear";
	    }
	    echo "$respuesta";
		break;	
	/*****************************************************************************************
		Inicio case verificar_expediente_radicacion_interna para cargar listado de expedientes por serie, subserie y dependencia 
	/*****************************************************************************************
		* @brief Recibe desde radicacion/radicacion_interna/funciones_radicaion_interna.js[function tratar_expediente()]
		* @param {string} ($dependencia) Es obligatorio y se usa para filtrar la consulta.
		* @param {string} ($serie) Es obligatorio y se usa para filtrar la consulta.
		* @param {string} ($subserie) Es obligatorio y se usa para filtrar la consulta.
		* @return {string} String con los <div> del listado de expedientes que existen en la base de datos con la relación dependencia-serie-subserie según los parámetros recibidos.
	*****************************************************************************************/		
	case 'verificar_expediente_radicacion_interna':
		if(!isset($_SESSION)){
			session_start();
  		}
		$query_expediente = "select 
								*
							from 
								expedientes
							where
								serie  					= '".$_POST['serie']."' and 
								subserie  				= '".$_POST['subserie']."' and 
								dependencia_expediente 	= '".$_SESSION['dependencia']."'";// Estructura sql
    	$fila_consulta_expedientes         = pg_query($conectado,$query_expediente);
    	$registros_consulta_expedientes    = pg_num_rows($fila_consulta_expedientes);
		if($registros_consulta_expedientes == 0){
			echo "Sin  expediente creados (serie: <b>".$_POST['serie']."</b> - subserie: <b>".$_POST['subserie']."</b>)";
		}else{
			echo "<div id='sin_expediente' class='art' onclick=\"cargar_expediente('', '')\" style='padding: 2px !important; display: none;'>
						Dejar redicado sin expediente
					</div>";
			for ($i=0; $i < $registros_consulta_expedientes ; $i++){
		    	$linea_consulta_expedientes = pg_fetch_array($fila_consulta_expedientes);  
				$id_expediente 		= $linea_consulta_expedientes['id_expediente'];
				$nombre_expediente 	= $linea_consulta_expedientes['nombre_expediente'];
				echo "<div id='expediente_padding' class='art' onclick=\"cargar_expediente('".$id_expediente."','".$nombre_expediente."')\" style='padding: 2px !important;'>
							".$id_expediente."
						</div>";
		    }
		}
		break;	
	/*****************************************************************************************
	Fin case verificar_expediente_radicacion_interna para cargar listado de expedientes por serie, subserie y dependencia 
	/*****************************************************************************************/				
}
?>