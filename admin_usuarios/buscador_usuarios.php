<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Buscador de Usuarios</title>
	<script type="text/javascript" src="include/js/jquery.js"></script>
</head>
<body>
<?php 
	/*Aqui defino la fecha de la transaccion*/
	require_once('../include/genera_fecha.php');
	// require_once('../login/conexion2.php');
	require_once('../login/validar_inactividad.php');


/*Isset ajax consulta sugerencias - Principal Usuarios */	
	$search_usuario ='';
	if(isset($_POST['search_usuario'])){
		$search_usuario = strtoupper($_POST['search_usuario']);

		$consulta_usuario="select * from usuarios u inner join dependencias d on u.codigo_dependencia=d.codigo_dependencia where (nombre_completo ilike '%$search_usuario%' or login ilike '%$search_usuario%' or documento_usuario ilike '%$search_usuario%') and login!='ADMINISTRADOR' order by u.nombre_completo limit 10";

		$fila_usuario = pg_query($conectado,$consulta_usuario);
		if($fila_usuario==false){ // Valida si la tabla dependencias existe en la base de datos
			echo '<script>
				alert("No pude conectarme a la tabla D de la base de datos 1, revisa la base de datos por favor");
				window.location.href="principal3.php"
			</script>';	
		}
		
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_usuario= pg_num_rows($fila_usuario);

	/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_usuario>0 && $search_usuario!=''){
			$num_fila=0;
			for ($i=0;$i<$registros_usuario;$i++){
				$linea = pg_fetch_array($fila_usuario);

				$id_usuario 				= $linea['id_usuario'];
				$identificacion 			= $linea['documento_usuario'];
				$codigo_dependencia 		= $linea['codigo_dependencia'];
				$login 						= $linea['login'];
				$nombre_completo 			= $linea['nombre_completo'];
				$perfil 					= $linea['perfil'];
				$administrador_sistema 		= $linea['administrador_sistema'];
				$estado 					= $linea['estado'];
				$imagen 					= $linea['path_foto'];
				$mail 						= $linea['mail_usuario'];
				$usuario_nuevo 				= $linea['usuario_nuevo'];
				$creacion_expedientes 		= $linea['creacion_expedientes'];
				$cuadro_clasificacion 		= $linea['cuadro_clasificacion'];
				$inventario 				= $linea['inventario'];
				$jefe_dependencia			= $linea['jefe_dependencia'];
				$modificar_radicado 		= $linea['modificar_radicado'];
				$nivel_seguridad 			= $linea['nivel_seguridad'];
				$prestamo_documentos 		= $linea['prestamo_documentos'];
				$scanner 					= $linea['scanner'];
				$ubicacion_topografica 		= $linea['ubicacion_topografica'];
				$ventanilla_radicacion 		= $linea['ventanilla_radicacion'];
				$radicacion_salida 	 		= $linea['radicacion_salida'];
				$radicacion_normal 	 		= $linea['radicacion_normal'];
				$radicacion_interna  		= $linea['radicacion_interna'];
				$radicacion_resoluciones 	= $linea['radicacion_resoluciones'];
				$path_firma 				= $linea['path_firma'];		// Este es una cadena base64 de la imagen de la firma	

				$nombre_dependencia 		= $linea['nombre_dependencia'];

				// $fecha_modificacion 	= $linea['fecha_modificacion'];//Fecha que tiene registro en base de datos
				// $fecha_creacion = $b->traducefecha($fecha_modificacion);//Traduce la fecha en formato "Domingo 15 de Mayo de 2016"
					
				$nombre_completo1   = trim(str_ireplace($search_usuario, "<b><font color='red'>$search_usuario</font></b>", $nombre_completo)); // Resalta con rojo el valor buscado	
				$login1   			= trim(str_ireplace($search_usuario, "<b><font color='red'>$search_usuario</font></b>", $login)); // Resalta con rojo el valor buscado	
					
				echo "<div class='art_exp";
					if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
					else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
				echo "'>"; 
/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/
					echo "<a href=\"javascript:cargar_modifica_usuario('$identificacion','$nombre_completo','$imagen','$login','$mail','$codigo_dependencia','$nombre_dependencia','$perfil','$estado','$usuario_nuevo','$nivel_seguridad','$id_usuario','$ventanilla_radicacion','$scanner','$modificar_radicado','$inventario','$ubicacion_topografica','$creacion_expedientes','$prestamo_documentos','$cuadro_clasificacion', '$jefe_dependencia', '$radicacion_salida', '$radicacion_normal', '$radicacion_interna', '$radicacion_resoluciones','$administrador_sistema','$path_firma')\">";

/*Aqui defino lo que se muestra del resultado obtenido*/						
					echo "
						<div style='float:left;'>
							<img src='$imagen' height='40' width='40' class='fotos'>
						</div>
						<div style='padding-left: 50px;'>
							<b>$nombre_completo1</b> ( $login1 )  
						</div>
						<div style='padding-left:50px;'>
							<span>$codigo_dependencia - $nombre_dependencia</span>
						</div>
					</a>";
/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
				echo "</div>";//cierra div class='art'
				$num_fila++; 
			}
		}else{
			echo "<h2> No se han encontrado resultados</h2><p>Si desea ingresar un nuevo usuario haga click
			<a href='javascript:abrirVentanaCrearUsuarios();'>aqui</a></p>";		
		}
	}
/* Fin que ajax - sugerencias Principal Usuarios */	

/*Isset ajax consulta sugerencias - nombre_completo Formulario Agregar Usuarios */	
	$search_nombre_completo ='';
	if(isset($_POST['search_nombre_completo'])){
		$search_nombre_completo = $_POST['search_nombre_completo'];

		$consulta_nombre_completo = "select * from usuarios u inner join dependencias d on u.codigo_dependencia=d.codigo_dependencia where nombre_completo ilike '%$search_nombre_completo%' or login ilike'%$search_nombre_completo%' order by u.nombre_completo limit 10";

		$fila_nombre_completo = pg_query($conectado,$consulta_nombre_completo);
/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_nombre_completo = pg_num_rows($fila_nombre_completo);

/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_nombre_completo>0 && $search_nombre_completo!=''){
			$num_fila=0;
			for ($i=0;$i<$registros_nombre_completo;$i++){
				$linea = pg_fetch_array($fila_nombre_completo);

				$perfil  			= $linea['perfil'];
				$codigo_dependencia = $linea['codigo_dependencia'];
				$login  			= $linea['login'];
				$estado  			= $linea['estado'];
				$nombre_completo 	= $linea['nombre_completo'];
				$documento_usuario  = $linea['documento_usuario'];
				$nivel_seguridad 	= $linea['nivel_seguridad'];
				$mail_usuario 		= $linea['mail_usuario'];
				$id_usuario 		= $linea['id_usuario'];
				$imagen  			= $linea['path_foto'];
				$nombre_dependencia = $linea['nombre_dependencia'];
				
				// $fecha_modificacion = $linea['fecha_modificacion'];//Fecha que tiene registro en base de datos
				// $fecha_creacion = $b->traducefecha($fecha_modificacion);//Trduce la fecha en formato "Domingo 15 de Mayo de 2016"

				$nombre_completo1   = trim(str_ireplace($search_nombre_completo, "<b><font color='red'>$search_nombre_completo</font></b>", $nombre_completo)); // Resalta con rojo el valor buscado								
				echo "
				<div class='tooltip'>	
					<div id='sugerencia_nombre_completo' class='art1 ";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "'>"; 
/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
					echo "<a href=\"javascript:valida_nombre_completo_ya_existe()\">";

/*Aqui defino lo que se muestra del resultado obtenido*/						
						echo "
							<div style='float:left;'>
								<img src='$imagen' height='40' width='40' class='fotos'> 
							</div>
							<div style='padding-left: 50px;'>
								<b>$nombre_completo1</b> ( $login ) $mail_usuario 
							</div>
							<div style='padding-left:50px;'>
								<span>$codigo_dependencia - $nombre_dependencia</span>
							</div>
						</a>";
/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
			echo "	</div> <!--cierra div class='art1' -->
			  		<span class='tooltiptext'>Usuario se encuentra $estado<br> con el perfil $perfil<br> Nivel de Seguridad $nivel_seguridad 
			  		</span>
				</div>"; // Cierra div class='tooltip'
				$num_fila++; 
			}
		}
	}
/* Fin que ajax - sugerencias nombre_completo Formulario Agregar Usuarios */	
/*Isset ajax consulta sugerencias - Identificacion Formulario Agregar Usuarios */	
	$search_identificacion ='';
	if(isset($_POST['search_identificacion'])){
		$search_identificacion = $_POST['search_identificacion'];

		$consulta_identificacion = "select * from usuarios u inner join dependencias d on u.codigo_dependencia=d.codigo_dependencia WHERE documento_usuario ilike '%$search_identificacion%' order by u.nombre_completo limit 10";

		$fila_identificacion = pg_query($conectado,$consulta_identificacion);
/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_identificacion= pg_num_rows($fila_identificacion);

/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_identificacion>0 && $search_identificacion!=''){
			$num_fila=0;
			for ($i=0;$i<$registros_identificacion;$i++){
				$linea = pg_fetch_array($fila_identificacion);

				$perfil 			= $linea['perfil'];
				$codigo_dependencia = $linea['codigo_dependencia'];
				$login  			= $linea['login'];
				$estado  			= $linea['estado'];
				$nombre_completo 	= $linea['nombre_completo'];
				$documento_usuario  = $linea['documento_usuario'];
				$nivel_seguridad  	= $linea['nivel_seguridad'];
				$mail_usuario 		= $linea['mail_usuario'];
				$id_usuario  		= $linea['id_usuario'];
				$imagen 			= $linea['path_foto'];
				$nombre_dependencia = $linea['nombre_dependencia'];
				$fecha_modificacion = $linea['fecha_modificacion'];//Fecha que tiene registro en base de datos
							
				echo "
				<div class='tooltip'>	
					<div id='sugerencia_id' class='art1 ";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "'>"; 
	/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
						echo "<a href=\"javascript:valida_identificacion_ya_existe()\">";

	/*Aqui defino lo que se muestra del resultado obtenido*/						
							echo "
								<div style='float:left;'>
									<img src='$imagen' height='40' width='40' class='fotos'> 
								</div>
								<div style='padding-left: 50px;'>
									<b>$nombre_completo</b> ( $login ) $mail_usuario 
								</div>
								<div style='padding-left:50px;'>
									<span>$codigo_dependencia - $nombre_dependencia</span>
								</div>
							</a>";
	/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
					echo "	</div> <!--cierra div class='art1' -->
			  		<span class='tooltiptext'>Usuario se encuentra $estado<br> con el perfil $perfil<br> Nivel de Seguridad $nivel_seguridad 
			  		</span>
				</div>"; // Cierra div class='tooltip'
				$num_fila++; 
			}
		}
	}
/* Fin que ajax - sugerencias Identificacion Formulario Agregar Usuarios */	

/*Isset ajax consulta sugerencias - login Formulario Agregar Usuarios */	
	$search_login ='';
	if(isset($_POST['search_login'])){
		$search_login = $_POST['search_login'];

		$consulta_login="select * from usuarios u inner join dependencias d on u.codigo_dependencia=d.codigo_dependencia where nombre_completo ilike '%$search_login%' or login ilike '%$search_login%'	order by u.nombre_completo limit 10";

		$fila_login 	 = pg_query($conectado,$consulta_login);
	/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_login = pg_num_rows($fila_login);

/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_login>0 && $search_login!=''){
				$num_fila=0;
			for ($i=0;$i<$registros_login;$i++){
				$linea = pg_fetch_array($fila_login);

				$perfil  			= $linea['perfil'];
				$codigo_dependencia = $linea['codigo_dependencia'];
				$login  			= $linea['login'];
				$estado  			= $linea['estado'];
				$nombre_completo  	= $linea['nombre_completo'];
				$documento_usuario  = $linea['documento_usuario'];
				$nivel_seguridad  	= $linea['nivel_seguridad'];
				$mail_usuario  	 	= $linea['mail_usuario'];
				$id_usuario  		= $linea['id_usuario'];
				$imagen  			= $linea['path_foto'];
				$nombre_dependencia = $linea['nombre_dependencia'];
			
				$login1   = trim(str_ireplace($search_login, "<b><font color='red'>$search_login</font></b>", $login)); // Resalta con rojo el valor buscado								
				echo "
				<div class='tooltip'>	
					<div id='sugerencia_login' class='art1 ";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "'>"; 
/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
					echo "<a href=\"javascript:valida_login_ya_existe()\">";

/*Aqui defino lo que se muestra del resultado obtenido*/
						echo "
							<div style='float:left;'>
								<img src='$imagen' height='40' width='40' class='fotos'> 
							</div>
							<div style='padding-left: 50px;'>
								<b>$nombre_completo</b> ( $login1 ) $mail_usuario 
							</div>
							<div style='padding-left:50px;'>
								<span>$codigo_dependencia - $nombre_dependencia</span>
							</div>
						</a>";
/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
			echo "	</div> <!--cierra div class='art1' -->
			  		<span class='tooltiptext'>Usuario se encuentra $estado<br> con el perfil $perfil<br> Nivel de Seguridad $nivel_seguridad 
			  		</span>
					</div>"; // Cierra div class='tooltip'
				$num_fila++; 
			}
		}else{
			/*Aqui va lo que quiero mostrar si no hay resultados*/
		}
	}
/* Fin que ajax - sugerencias login Formulario Agregar Usuarios */	
/*Isset ajax consulta sugerencias - dependencia Formulario Agregar Usuarios */	
	$search_dependencia ='';
	if(isset($_POST['search_dependencia'])){
		// echo "$search_dependencia";
		$search_dependencia = strtoupper($_POST['search_dependencia']);

		$consulta_dependencia 	= "select * from dependencias where codigo_dependencia ilike '%$search_dependencia%'
		or nombre_dependencia ilike '%$search_dependencia%' and activa='SI' order by nombre_dependencia limit 5";
		$fila_dependencia 		= pg_query($conectado,$consulta_dependencia);
/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_dependencia 	= pg_num_rows($fila_dependencia);

/* Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_dependencia>0 && $search_dependencia!=''){
			echo "<script>
				$('#error_dependencia_inexistente').slideUp();
				$('#sugerencias_dependencia').slideDown();
			</script>";
			
			$num_fila=0;
			for ($i=0;$i<$registros_dependencia;$i++){
				$linea = pg_fetch_array($fila_dependencia);

				$codigo_dependencia = $linea['codigo_dependencia'];
				$nombre_dependencia = $linea['nombre_dependencia'];
				$activa  			= $linea['activa'];
				
				$fecha_modificacion = $linea['fecha_modificacion'];//Fecha que tiene registro en base de datos
				// $fecha_creacion  	= $b->traducefecha($fecha_modificacion);//Trduce la fecha en formato "Domingo 15 de Mayo de 2016"
			
					echo "
				<div id='sugerencia_dependencia' class='art1 ";
					if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
					else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
				echo "'>"; 
/* Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href" */;
				echo "<a href=\"javascript:carga_dependencia('$codigo_dependencia','$nombre_dependencia')\">";

/*Aqui defino lo que se muestra del resultado obtenido*/
					$codigo_dependencia1   = trim(str_ireplace($search_dependencia, "<b><font color='red'>$search_dependencia</font></b>", $codigo_dependencia)); // Resalta con rojo el valor buscado						
					$nombre_dependencia1   = trim(str_ireplace($search_dependencia, "<b><font color='red'>$search_dependencia</font></b>", $nombre_dependencia)); // Resalta con rojo el valor buscado						
					echo "
						<div style='padding-left: 5px;'>
							<b>$codigo_dependencia1</b> ( $nombre_dependencia1 ) 
						</div>
					</a>";
/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
			echo "</div> <!--cierra div class='art1' --> "; 
				$num_fila++; 
			}
		}else{
			if($search_dependencia!=''){
				echo "<script>
					$('#error_dependencia_inexistente').slideDown();
				</script>";
			}
		}
	}
/* Fin que ajax - sugerencias dependencia Formulario Agregar Usuarios */

/*Isset ajax consulta sugerencias - ubicacion Formulario Agregar Usuarios */	
	$search_ubicacion ='';
	if(isset($_POST['search_ubicacion'])){
		$search_ubicacion = $_POST['search_ubicacion'];

		$consulta_ubicacion  = "select * from municipios where nombre_departamento ilike '%$search_ubicacion%' or nombre_municipio ilike '%$search_ubicacion%' order by nombre_municipio limit 5";

		$fila_ubicacion 	 = pg_query($conectado,$consulta_ubicacion);
/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_ubicacion = pg_num_rows($fila_ubicacion);

/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_ubicacion>0 && $search_ubicacion!=''){
			echo "<script>
				$('#error_ubicacion_inexistente').slideUp();
				$('#sugerencias_ubicacion').slideDown();
			</script>";

			$num_fila=0;
			for ($i=0;$i<$registros_ubicacion;$i++){
				$linea = pg_fetch_array($fila_ubicacion);

				$nombre_municipio  		= $linea['nombre_municipio'];
				$nombre_departamento  	= $linea['nombre_departamento'];
				$nombre_pais  			= $linea['nombre_pais'];
				$nombre_continente  	= $linea['nombre_continente'];
				
				$fecha_modificacion 	= $linea['fecha_modificacion'];//Fecha que tiene registro en base de datos
				// $fecha_creacion  		= $b->traducefecha($fecha_modificacion);//Trduce la fecha en formato "Domingo 15 de Mayo de 2016"
			
				echo "
				<div id='sugerencia_ubicacion' class='art1 ";
					if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
					else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
				echo "'>"; 
/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
				echo "<a href=\"javascript:carga_ubicacion('$nombre_municipio','$nombre_departamento','$nombre_pais','$nombre_continente')\">";

/*Aqui defino lo que se muestra del resultado obtenido*/						
					echo "
						<div style='padding-left: 5px;'>
							<b>$nombre_municipio</b> ( $nombre_departamento ) | $nombre_pais - $nombre_continente 
						</div>
					</a>";
/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
		echo "	</div> <!--cierra div class='art1' --> "; 
				$num_fila++; 
			}
		}else{
			if($search_ubicacion!=''){
				echo "<script>
					$('#error_ubicacion_inexistente').slideDown();
				</script>";
				/*Aqui va lo que quiero mostrar si no hay resultados*/
			}
		}
	}
/* Fin que ajax - sugerencias ubicacion Formulario Agregar Usuarios */
/* Isset ajax consulta la opcion jefe del usuario esta disponible en la dependencia - Formulario Agregar Usuarios */	
	$search_jefe_dependencia_depe_codi      ='';
	if(isset($_POST['search_jefe_dependencia_depe_codi'])){
		$search_jefe_dependencia_depe_codi	= $_POST['search_jefe_dependencia_depe_codi'];
		$consulta_jefe_dependencia			="select * from usuarios where jefe_dependencia='SI' and codigo_dependencia ='$search_jefe_dependencia_depe_codi' and estado='ACTIVO'";
		$fila_jefe_dependencia 				= pg_query($conectado,$consulta_jefe_dependencia);
		$linea  							= pg_fetch_array($fila_jefe_dependencia);
	/* Calcula el numero de registros que genera la consulta anterior. */
		$registros_jefe_dependencia			= pg_num_rows($fila_jefe_dependencia);

		if($registros_jefe_dependencia>0){
			$nombre_usuario_perfil = $linea['nombre_completo'];
			echo "<script>
				$('#user_jefe_dependencia').html('$nombre_usuario_perfil');	
					$('#error_jefe_dependencia').slideDown('slow');
				</script>";
		}else{
			echo "<script>	
				$('#error_jefe_dependencia').slideUp('slow');
			</script>";	
		}	
	}
/* Fin ajax consulta si perfil del usuario esta disponible en la dependencia - Formulario Agregar Usuarios */
/* Isset ajax consulta si perfil del usuario esta disponible en la dependencia - Formulario Agregar Usuarios */	
	$search_perfil ='';
	if(isset($_POST['search_perfil_depe_codi'])){
		$search_perfil  			= $_POST['search_perfil'];
		$search_perfil_depe_codi 	= $_POST['search_perfil_depe_codi'];

	$consulta_perfil="select * from usuarios where perfil='$search_perfil' and	codigo_dependencia ='$search_perfil_depe_codi' and estado='ACTIVO'";
	
	$fila_perfil 	= pg_query($conectado,$consulta_perfil);
	$linea  		= pg_fetch_array($fila_perfil);

/* Calcula el numero de registros que genera la consulta anterior. */
	$registros_perfil= pg_num_rows($fila_perfil);


		if($registros_perfil>0){
			$nombre_usuario_perfil = $linea['nombre_completo'];
			echo "<script>
				$('#user_perfil').html('$nombre_usuario_perfil');
			</script>";


			if($search_perfil=='USUARIO'){
				echo "<script>	
					$('#error_perfil').slideUp('slow');
				</script>";
			}elseif ($search_perfil=='AUXILIAR_ARCHIVO') {
				echo "<script>	
					$('#error_perfil').slideUp('slow');
				</script>";				
			}else{
				echo "<script>	
						$('#error_perfil').slideDown('slow');
					</script>";	
			}
		}else{
			echo "<script>	
				$('#error_perfil').slideUp('slow');
			</script>";	
		}	
	}
/* Fin ajax consulta si perfil del usuario esta disponible en la dependencia - Formulario Agregar Usuarios */

/******************************************************************************************/
/* Modificar Usuario **********************************************************************/
/******************************************************************************************/

/*Isset ajax consulta sugerencias - Identificacion Formulario Modificar Usuarios */	
	$search_mod_identificacion ='';
	if(isset($_POST['search_mod_id'])){
		$search_mod_identificacion 	= $_POST['search_mod_id'];
		$search_ant_mod_id 			= $_POST['search_ant_mod_id'];

	$consulta_mod_identificacion="select * from usuarios u inner join dependencias d on u.codigo_dependencia=d.codigo_dependencia where documento_usuario ilike '%$search_mod_identificacion%' order by u.nombre_completo limit 10";

/* Si el nombre a modificar es el mismo que tenía, quita los errores */
		if(strtoupper($search_mod_identificacion)==strtoupper($search_ant_mod_id)){
			echo"<script>
				$('#sugerencias_mod_identificacion').html('');
			</script>";
		}

	$fila_mod_identificacion  		= pg_query($conectado,$consulta_mod_identificacion);
/*Calcula el numero de registros que genera la consulta anterior.*/
	$registros_mod_identificacion 	= pg_num_rows($fila_mod_identificacion);

/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_mod_identificacion>0 && $search_mod_identificacion!=''){
			$num_fila=0;
			for ($i=0;$i<$registros_mod_identificacion;$i++){
				$linea = pg_fetch_array($fila_mod_identificacion);

				$documento_usuario 	= $linea['documento_usuario'];
				$perfil  			= $linea['perfil'];
				$codigo_dependencia = $linea['codigo_dependencia'];
				$login  			= $linea['login'];
				$estado  			= $linea['estado'];
				$nombre_completo  	= $linea['nombre_completo'];
				$nivel_seguridad  	= $linea['nivel_seguridad'];
				$mail_usuario  		= $linea['mail_usuario'];
				$imagen  			= $linea['path_foto'];
				$nombre_dependencia = $linea['nombre_dependencia'];
				$fecha_modificacion = $linea['fecha_modificacion'];//Fecha que tiene registro en base de datos
				// $fecha_creacion  	= $b->traducefecha($fecha_modificacion);//Trduce la fecha en formato "Domingo 15 de Mayo de 2016"

			echo "
			<div class='tooltip'>	
				<div id='sugerencia_mod_id' class='art1'";
					if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
					else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
				echo "'>"; 
/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
					//echo "uno $documento_usuario y dos $search_ant_mod_id";
					if($documento_usuario==$search_ant_mod_id){
						echo "<a href=\"javascript:id_anterior($documento_usuario)\">";
					}else{
						echo "<a href=\"javascript:valida_mod_identificacion_ya_existe()\">";
					}

/*Aqui defino lo que se muestra del resultado obtenido*/						
						echo "
							<div style='float:left;'>
								<img src='$imagen' height='60' width='50' class='fotos'> 
							</div>
							<div style='padding-left: 60px;'>
								<b>$nombre_completo</b> ( $login ) $mail_usuario 
							</div>
							<div style='padding-left:60px;'>
								<span>$codigo_dependencia - $nombre_dependencia</span>
							</div>
						</a>";
/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
		echo "	</div> <!--cierra div class='art1' -->
		  		<span class='tooltiptext'>Usuario se encuentra $estado  con el perfil $perfil<br> Nivel de Seguridad $nivel_seguridad 
		  		</span>
				</div>"; // Cierra div class='tooltip'
				$num_fila++; 
			}
		}else{
			/*Aqui va lo que quiero mostrar si no hay resultados*/
		}
	}
/* Fin que ajax - sugerencias Identificacion Formulario Modificar Usuarios */	

/*Isset ajax consulta sugerencias - nombre_completo Formulario Modificar Usuarios */	
	$search_mod_nombre_completo ='';
	if(isset($_POST['search_mod_nombre_completo'])){
		$search_mod_nombre_completo = $_POST['search_mod_nombre_completo'];
		$search_ant_mod_nom  		= $_POST['search_ant_mod_nom'];

	$consulta_mod_nombre_completo="select * from usuarios u inner join dependencias d on u.codigo_dependencia=d.codigo_dependencia where login ilike '%$search_mod_nombre_completo%' or nombre_completo ilike '%$search_mod_nombre_completo%' order by u.nombre_completo limit 10";

/* Si el nombre a modificar es el mismo que tenía, quita las sugerencias */
		if(strtoupper($search_mod_nombre_completo)==$search_ant_mod_nom){
			echo"<script>
				$('#sugerencia_mod_nom').html('slow');
			</script>";
		}else{
			echo"<script>
				$('#sugerencia_mod_nom').slideDown('slow');
			</script>";
		}

	$fila_mod_nombre_completo = pg_query($conectado,$consulta_mod_nombre_completo);
/*Calcula el numero de registros que genera la consulta anterior.*/
	$registros_mod_nombre_completo= pg_num_rows($fila_mod_nombre_completo);

/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_mod_nombre_completo>0 && $search_mod_nombre_completo!=''){
			$num_fila=0;
			for ($i=0;$i<$registros_mod_nombre_completo;$i++){
				$linea = pg_fetch_array($fila_mod_nombre_completo);

				$perfil  			= $linea['perfil'];
				$codigo_dependencia = $linea['codigo_dependencia'];
				$login  			= $linea['login'];
				$estado  			= $linea['estado'];
				$nombre_completo  	= $linea['nombre_completo'];
				$nivel_seguridad  	= $linea['nivel_seguridad'];
				$mail_usuario  	 	= $linea['mail_usuario'];
				$imagen  			= $linea['path_foto'];
				$nombre_dependencia = $linea['nombre_dependencia'];
				$fecha_modificacion = $linea['fecha_modificacion'];//Fecha que tiene registro en base de datos
				// $fecha_creacion  	= $b->traducefecha($fecha_modificacion);//Trduce la fecha en formato "Domingo 15 de Mayo de 2016"
				$nombre_completo1   = trim(str_ireplace($search_mod_nombre_completo, "<b><font color='red'>$search_mod_nombre_completo</font></b>", $nombre_completo)); // Resalta con rojo el valor buscado	
				$login1   			= trim(str_ireplace($search_mod_nombre_completo, "<b><font color='red'>$search_mod_nombre_completo</font></b>", $login)); // Resalta con rojo el valor buscado	

				echo "
				<div class='tooltip'>	
					<div id='sugerencia_mod_nom' class='art1' ";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
						else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
					echo "'>"; 
/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;

						if(strtoupper($search_ant_mod_nom)==strtoupper($nombre_completo)){
							echo "<a href=\"javascript:nombre_anterior('$nombre_completo')\">";
						}else{
							echo "<a href=\"javascript:valida_mod_nombre_completo_ya_existe()\">";
						}

/*Aqui definmod_o lo que se muestra del resultado obtenido*/						
						echo "
							<div style='float:left;'>
								<img src='$imagen' height='60' width='50' class='fotos'> 
							</div>
							<div style='padding-left: 60px;'>
								<b>$nombre_completo1</b> ( $login1 ) $mail_usuario 
							</div>
							<div style='padding-left:60px;'>
								<span>$codigo_dependencia - $nombre_dependencia</span>
							</div>
						</a>";
/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
			echo "	</div> <!--cierra div class='art1' -->
		  		<span class='tooltiptext'>Usuario se encuentra $estado  con el perfil $perfil<br> Nivel de Seguridad $nivel_seguridad 
		  		</span>
			</div>"; // Cierra div class='tooltip'
				$num_fila++; 
			}
		}else{
			/*Aqui va lo que quiero mostrar si no hay resultados*/
		}
	}
/* Fin que ajax - sugerencias nombre_completo Formulario Modificar Usuarios */	
/*Isset ajax consulta sugerencias - mod_login Formulario Modificar Usuario  */	
	$search_mod_login ='';
	if(isset($_POST['search_mod_login'])){
		$search_mod_login  		= $_POST['search_mod_login'];
		$search_ant_mod_login 	= $_POST['search_ant_mod_login'];

	$consulta_mod_login="select * from usuarios u inner join dependencias d on u.codigo_dependencia=d.codigo_dependencia where login ilike '%$search_mod_login%' order by u.nombre_completo limit 10";
/* Si el nombre a modificar es el mismo que tenía, quita los errores */
		if(strtoupper($search_mod_login)==strtoupper($search_ant_mod_login)){
			echo"<script>
				$('#sugerencia_mod_login').html('');
			</script>";
		}else{
			echo"<script>
				$('#sugerencia_mod_login').slideDown('slow');
			</script>";
		}

	$fila_mod_login = pg_query($conectado,$consulta_mod_login);
/*Calcula el numero de registros que genera la consulta anterior.*/
	$registros_mod_login= pg_num_rows($fila_mod_login);

/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_mod_login>0 && $search_mod_login!=''){
			$num_fila=0;
			for ($i=0;$i<$registros_mod_login;$i++){
				$linea = pg_fetch_array($fila_mod_login);

				$perfil  			= $linea['perfil'];
				$codigo_dependencia = $linea['codigo_dependencia'];
				$login  			= $linea['login'];
				$estado  			= $linea['estado'];
				$nombre_completo  	= $linea['nombre_completo'];
				$documento_usuario  = $linea['documento_usuario'];
				$nivel_seguridad  	= $linea['nivel_seguridad'];
				$mail_usuario   	= $linea['mail_usuario'];
				$id_usuario  		= $linea['id_usuario'];
				$imagen  			= $linea['path_foto'];
				$nombre_dependencia = $linea['nombre_dependencia'];
				$fecha_modificacion = $linea['fecha_modificacion'];//Fecha que tiene registro en base de datos
				// $fecha_creacion 	= $b->traducefecha($fecha_modificacion);//Traduce la fecha en formato "Domingo 15 de Mayo de 2016"
				$login1   = trim(str_ireplace($search_mod_login, "<b><font color='red'>$search_mod_login</font></b>", $login)); // Resalta con rojo el valor buscado	

				echo "
			<div id='sugerencia_mod_login' class='tooltip'>	
				<div  class='art1' ";
					if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
					else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
				echo "'>"; 
/*Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href"*/;
					echo "<a href=\"javascript:valida_mod_login_ya_existe()\">";

					if(strtoupper($login)==$search_ant_mod_login){
						echo "<a href=\"javascript:login_anterior('$login')\">";
					}else{
						echo "<a href=\"javascript:valida_mod_login_ya_existe()\">";
					}

/*Aqui defino lo que se muestra del resultado obtenido*/						
						echo "
							<div style='float:left;'>
								<img src='$imagen' height='40' width='40' class='fotos'> 
							</div>
							<div style='padding-left: 50px;'>
								<b>$nombre_completo</b> ( $login1 ) $mail_usuario 
							</div>
							<div style='padding-left:50px;'>
								<span>$codigo_dependencia - $nombre_dependencia</span>
							</div>
						</a>";
/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
		echo "	</div> <!--cierra div class='art1' -->
		  		<span class='tooltiptext'>Usuario se encuentra $estado<br> con el perfil $perfil<br> Nivel de Seguridad $nivel_seguridad 
		  		</span>
				</div>"; // Cierra div class='tooltip'
				$num_fila++; 
			}
		}else{
			/*Aqui va lo que quiero mostrar si no hay resultados*/
		}
	}
/* Fin que ajax - sugerencias mod_login Formulario Modificar Usuarios */
/*Isset ajax consulta sugerencias - mod_dependencia Formulario Modificar Usuarios */	
	$search_mod_dependencia ='';
	if(isset($_POST['search_mod_dependencia'])){
		$search_mod_dependencia = $_POST['search_mod_dependencia'];
		$mod_ant_nom_depe 		= $_POST['search_ant_mod_depe'];

	/* Si el nombre a modificar es el mismo que tenía, quita los errores */
		if(strtoupper($search_mod_dependencia)==strtoupper($mod_ant_nom_depe)){
			echo"<script>
				$('#sugerencia_mod_dependencia').slideUp('slow');
			</script>";
		}else{
			echo"<script>
				$('#sugerencia_mod_dependencia').slideDown('slow');
			</script>";
		}
	

		$consulta_mod_dependencia="select * from dependencias where codigo_dependencia ilike '%$search_mod_dependencia%' or nombre_dependencia ilike '%$search_mod_dependencia%' and activa='SI' order by nombre_dependencia limit 5";

		$fila_mod_dependencia 		= pg_query($conectado,$consulta_mod_dependencia);
/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_mod_dependencia  = pg_num_rows($fila_mod_dependencia);

/* Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_mod_dependencia>0 && $search_mod_dependencia!=''){
			echo "<script>
				$('#error_mod_dependencia_inexistente').slideUp();
				$('#sugerencias_mod_dependencia').slideDown();
			</script>";
			$num_fila=0;
			for ($i=0;$i<$registros_mod_dependencia;$i++){
				$linea = pg_fetch_array($fila_mod_dependencia);

				$codigo_mod_dependencia = $linea['codigo_dependencia'];
				$nombre_mod_dependencia = $linea['nombre_dependencia'];
				$activa 				= $linea['activa'];

				$codigo_mod_dependencia1   = trim(str_ireplace($search_mod_dependencia, "<b><font color='red'>$search_mod_dependencia</font></b>", $codigo_mod_dependencia)); // Resalta con rojo el valor buscado	
				$nombre_mod_dependencia1   = trim(str_ireplace($search_mod_dependencia, "<b><font color='red'>$search_mod_dependencia</font></b>", $nombre_mod_dependencia)); // Resalta con rojo el valor buscado	

				
				$fecha_modificacion=$linea['fecha_modificacion'];//Fecha que tiene registro en base de datos
				// $fecha_creacion = $b->traducefecha($fecha_modificacion);//Trduce la fecha en formato "Domingo 15 de Mayo de 2016"
			
					echo "
				<div id='sugerencia_mod_dependencia' class='art1' ";
					if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
					else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
				echo "'>"; 
/* Aqui defino cuál va a ser el comportamiento al dar clic sobre el resultado obtenido desde el "a href" */;
					echo "<a href=\"javascript:carga_mod_dependencia('$codigo_mod_dependencia','$nombre_mod_dependencia')\">";

/*Aqui defino lo que se muestra del resultado obtenido*/						
						echo "
							<div style='padding-left: 5px;'>
								<b>$codigo_mod_dependencia1</b> ( $nombre_mod_dependencia1 ) 
							</div>
						</a>";
/*Hasta aqui debe ir la etiqueta "a href" para que cuando haga clic en cada uno de los resultados*/
		echo "	</div> <!--cierra div class='art1' --> "; 
				$num_fila++; 
			}
		}else{
			if($search_mod_dependencia!=''){
				echo "<script>
					$('#error_mod_dependencia_inexistente').slideDown('slow');
				</script>";
				/*Aqui va lo que quiero mostrar si no hay resultados*/
			}
		}
	}
/* Fin que ajax - sugerencias mod_dependencia Formulario Modificar Usuarios */
/* Isset ajax consulta la opcion jefe del usuario esta disponible en la dependencia - Formulario Modificar Usuarios */	
	// $mod_jefe_dependencia_depe_codi      ='';
	if(isset($_POST['mod_jefe_dependencia_depe_codi'])){
		$mod_jefe_dependencia_depe_codi	= $_POST['mod_jefe_dependencia_depe_codi'];
		$mod_login 						= $_POST['mod_login'];

		$consulta_jefe_dependencia_mod		="select * from usuarios where jefe_dependencia='SI' and codigo_dependencia ='$mod_jefe_dependencia_depe_codi' and estado='ACTIVO'";
		$fila_jefe_dependencia_mod 			= pg_query($conectado,$consulta_jefe_dependencia_mod);
		$linea  							= pg_fetch_array($fila_jefe_dependencia_mod);
	/* Calcula el numero de registros que genera la consulta anterior. */
		$registros_jefe_dependencia_mod			= pg_num_rows($fila_jefe_dependencia_mod);

		if($registros_jefe_dependencia_mod>0){
			$nombre_usuario_perfil 	= $linea['nombre_completo'];
			$login_usuario_perfil  	= $linea['login'];

			if($login_usuario_perfil==$mod_login){
				echo "<script>	
					$('#mod_error_jefe_dependencia').slideUp('slow');
				</script>";		
			}else{
				echo "<script>
					$('#mod_user_jefe_dependencia').html('$nombre_usuario_perfil');	
						$('#mod_error_jefe_dependencia').slideDown('slow');
					</script>";
			}
		}else{
			echo "<script>	
				$('#mod_error_jefe_dependencia').slideUp('slow');
			</script>";	
		}	
	}
/* Fin ajax consulta si perfil del usuario esta disponible en la dependencia - Formulario Formulario Modificar Usuarios */
/* Isset ajax consulta si mod_perfil del usuario esta disponible en la dependencia - Formulario Agregar Usuarios */	

	$search_mod_perfil ='';
	if(isset($_POST['search_mod_perfil_depe_codi'])){
		$search_mod_perfil  			= $_POST['search_mod_perfil'];
		$search_mod_perfil_depe_codi  	= $_POST['search_mod_perfil_depe_codi'];
		$ant_mod_login_perfil  			= $_POST['ant_mod_login'];

	$consulta_mod_perfil="select * from usuarios where perfil='$search_mod_perfil' and	codigo_dependencia ='$search_mod_perfil_depe_codi' and estado='ACTIVO'";

	$fila_mod_perfil 	= pg_query($conectado,$consulta_mod_perfil);
	$linea 				= pg_fetch_array($fila_mod_perfil);

	$nombre_usuario_mod_perfil = $linea['nombre_completo'];

	echo "<script>
		$('#user_mod_perfil').html('$nombre_usuario_mod_perfil');
	</script>";

/* Calcula el numero de registros que genera la consulta anterior. */
	$registros_mod_perfil= pg_num_rows($fila_mod_perfil);

		if($registros_mod_perfil>0){
			if($search_mod_perfil=='AUXILIAR_ARCHIVO'){
			echo "<script>	
					$('#error_mod_perfil').slideUp('slow');
				</script>";				
			}elseif($ant_mod_login_perfil==$nombre_usuario_mod_perfil){
				echo"<script>
					$('#error_mod_perfil').slideUp('slow');
				</script>";		
			}elseif ($search_mod_perfil=='USUARIO') {
				echo "<script>	
					$('#error_mod_perfil').slideUp('slow');
				</script>";	
			}else{
				echo "<script>	
					$('#error_mod_perfil').slideDown('slow');
				</script>";
			}
		}else{
			echo "<script>	
					$('#error_mod_perfil').slideUp('slow');
				</script>";
		}	
	}
/* Fin ajax consulta si mod_perfil del usuario esta disponible en la dependencia - Formulario Agregar Usuarios */
	if(isset($_POST['search_perfil_depe_codi'])){
		$search_perfil = $_POST['search_perfil'];
		$search_perfil_depe_codi = $_POST['search_perfil_depe_codi'];

	$consulta_perfil="select * from usuarios where perfil='$search_perfil'
	and	codigo_dependencia ='$search_perfil_depe_codi' and estado='ACTIVO'";
	
	$fila_perfil 	= pg_query($conectado,$consulta_perfil);

/* Calcula el numero de registros que genera la consulta anterior. */
	$registros_perfil= pg_num_rows($fila_perfil);

		if($registros_perfil>0){
			$linea  		= pg_fetch_array($fila_perfil);

			$nombre_usuario_perfil = $linea['nombre_completo'];

			echo "<script>
				$('#user_perfil').html('$nombre_usuario_perfil');
			</script>";
			
			if($search_perfil=='USUARIO'){
				echo "<script>	
					$('#error_perfil').slideUp('slow');
				</script>";
			}elseif ($search_perfil=='AUXILIAR_ARCHIVO') {
				echo "<script>	
					$('#error_perfil').slideUp('slow');
				</script>";				
			}else{
				echo "<script>	
						$('#error_perfil').slideDown('slow');
					</script>";	
			}
		}else{
			echo "<script>	
				$('#error_perfil').slideUp('slow');
			</script>";	
		}	
	}

	if(isset($_POST['listado_usuarios_depe'])){
		echo "
		<table border='0'>
			<tr>
				<td class='descripcion'>Nombre de la dependencia</td>
				<td class='descripcion'>Perfil</td>
				<td class='descripcion'>Documento Usuario</td>
				<td class='descripcion'>Nombre Completo</td>
				<td class='descripcion'>Login</td>
				<td class='descripcion'>Mail</td>
			</tr>";
		$query_usuarios_depe ="select d.nombre_dependencia,d.codigo_dependencia,u.documento_usuario,u.perfil,u.nombre_completo,u.login,u.mail_usuario from usuarios u join dependencias d on d.codigo_dependencia=u.codigo_dependencia where d.id_dependencia!='1' order by 1,4";
		
		$fila_usuarios_depe = pg_query($conectado,$query_usuarios_depe);
/*Calcula el numero de registros que genera la consulta anterior.*/
		$registros_usuarios_depe = pg_num_rows($fila_usuarios_depe);

/*Recorre el array generado e imprime uno a uno los resultados.*/	
		if($registros_usuarios_depe>0){
			$reg = "";
			for ($i=0;$i<$registros_usuarios_depe;$i++){
				if($i%2==0){
					$fila ='fila1';
				}else{
					$fila ='fila2';
				}
				$linea = pg_fetch_array($fila_usuarios_depe);

				$nombre_dependencia 	= $linea['nombre_dependencia'];
				$codigo_dependencia 	= $linea['codigo_dependencia'];
				$documento_usuario  	= $linea['documento_usuario'];
				$perfil  				= $linea['perfil'];
				$nombre_completo  		= $linea['nombre_completo'];
				$login  				= $linea['login'];
				$mail_usuario  			= $linea['mail_usuario'];

				$reg.="<tr class='$fila'><td>( $codigo_dependencia ) $nombre_dependencia </td><td>$perfil</td><td>$documento_usuario</td><td>$nombre_completo</td><td>$login</td><td>$mail_usuario</td></tr>";
			}
		}	
		echo "$reg
		<tr style='height:20px;'><td></td>
		</tr>
		<tr>	
			<td colspan='2'>
			<td>
				<center>
					<a href='formatos/reporte_excel.php?nombre_reporte=listado_usuarios_por_dependencia&sql=".urlencode($query_usuarios_depe)."' style='text-decoration:none;'>
						<img src='imagenes/iconos/logo_excel.png' width='30' height='25' title='Exportar a excel' style='padding : 10px;'>
					</a> 
					<a href='formatos/reporte_pdf.php?nombre_reporte=listado_usuarios_por_dependencia&sql=".urlencode($query_usuarios_depe)."' style='text-decoration:none;'><img src='imagenes/iconos/archivo_pdf.png' width='30' height='25' title='Exportar a PDF' style='padding : 10px; text-decoration:none;'>
					</a>
				</center>
			</td>
			<td>
			<td colspan='3'>
		</tr>
		</table>";
	}
	/* Case para verificar si antes de cambiar el usuario de dependencia, este tiene radicados pendientes */
	if(isset($_POST['mod_dependencia_actu'])){
		$mod_dependencia_actu 	= $_POST['mod_dependencia_actu'];
		$ant_mod_login 			= $_POST['ant_mod_login'];

		/* Se consulta cantidad de radicados en bandeja de entrada del usuario */
		$consulta_radicados_entrada = "select count(*) from radicado r  where codigo_carpeta1 ->'$ant_mod_login' ->> 'codigo_carpeta_personal' = 'entrada'";
		$result_consulta_radicados_entrada  = pg_query($conectado,$consulta_radicados_entrada);
    	$linea_consulta_radicados_entrada   = pg_fetch_array($result_consulta_radicados_entrada);    
    	$radicados_entrada                  = $linea_consulta_radicados_entrada['count'];

		/* Se consulta cantidad de radicados en bandeja de salida del usuario */
		// $consulta_radicados_salida = "select count(*) from radicado r  where codigo_carpeta1 ->'$ant_mod_login' ->> 'codigo_carpeta_personal' = 'Salida'";
		// $result_consulta_radicados_salida  = pg_query($conectado,$consulta_radicados_salida);
  //   	$linea_consulta_radicados_salida   = pg_fetch_array($result_consulta_radicados_salida);    
  //   	$radicados_salida                 = $linea_consulta_radicados_salida['count'];

    	$contenido_error_radicados_pendientes = "";

    	if($radicados_entrada != 0){
	    	$contenido_error_radicados_pendientes.="El usuario <b>$ant_mod_login</b> tiene <b>$radicados_entrada</b> pendientes en su bandeja de entrada<br>";
    	}

    	// if($radicados_salida != 0){
	    // 	$contenido_error_radicados_pendientes.="El usuario <b>$ant_mod_login</b> tiene <b>$radicados_salida</b> pendientes en su bandeja de salida<br>";
    	// }

    	if($contenido_error_radicados_pendientes!=""){
    		$contenido_error_radicados_pendientes.="Por lo que no puede cambiar de dependencia ni inactivar todavía.";

	    	echo "<script>	
					$('#error_usuario_radicados_pendientes').slideDown('slow');
					$('#error_usuario_radicados_pendientes').html('$contenido_error_radicados_pendientes');
				</script>";
    	}
	}	
?>
</body>
</html>