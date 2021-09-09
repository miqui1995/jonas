<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Query de Usuarios</title>
</head>
<body>
<?php 
	require_once('../include/genera_fecha.php');
	require_once('../login/conexion2.php');

	/* isset de variables para agregar usuario */
	if(isset($_POST['codigo_dependencia'])){
		$codigo_dependencia=$_POST['codigo_dependencia'];	
	}
	if(isset($_POST['dependencia'])){
		$dependencia=$_POST['dependencia'];	
	}
	if(isset($_POST['estado'])){
		$estado=$_POST['estado'];	
	}
	if(isset($_POST['identificacion'])){
		$identificacion=$_POST['identificacion'];
	}
	if(isset($_POST['login'])){
		$login=$_POST['login'];
	}
	if(isset($_POST['mail'])){
		$mail=$_POST['mail'];
	}
	if(isset($_POST['nivel_seguridad'])){
		$nivel_seguridad=$_POST['nivel_seguridad'];	
	}
	if(isset($_POST['nombre_completo'])){
		$nombre_completo=$_POST['nombre_completo'];
	}
	if(isset($_POST['perfil'])){
		$perfil=$_POST['perfil'];
	}
	if(isset($_POST['ubicacion'])){
		$ubicacion=$_POST['ubicacion'];	
	}
	if(isset($_POST['usuario_nuevo'])){
		$usuario_nuevo=$_POST['usuario_nuevo'];	
	}
	if(isset($_POST['tipo_formulario'])){	
		$tipo_formulario=$_POST['tipo_formulario'];
	}
	if(isset($_POST['scanner'])){
		$scanner=$_POST['scanner'];	
	}
	if(isset($_POST['modificar_radicado'])){
		$modificar_radicado=$_POST['modificar_radicado'];
	}
	if(isset($_POST['inventario'])){
		$inventario=$_POST['inventario'];
	}
	if(isset($_POST['ubicacion_topografica'])){
		$ubicacion_topografica=$_POST['ubicacion_topografica'];
	}
	if(isset($_POST['creacion_expedientes'])){
		$creacion_expedientes=$_POST['creacion_expedientes'];
	}
	if(isset($_POST['prestamo_documentos'])){
		$prestamo_documentos=$_POST['prestamo_documentos'];
	}
	if(isset($_POST['cuadro_clasificacion'])){
		$cuadro_clasificacion=$_POST['cuadro_clasificacion'];
	}
	if(isset($_POST['jefe_dependencia'])){
		$jefe_dependencia=$_POST['jefe_dependencia'];	
	}
	if(isset($_POST['administrador_sistema'])){
		$administrador_sistema=$_POST['administrador_sistema'];	
	}
	if(isset($_POST['ventanilla_radicacion'])){
		$ventanilla_radicacion=$_POST['ventanilla_radicacion'];	
	}
	if(isset($_POST['radicacion_salida'])){
		$radicacion_salida=$_POST['radicacion_salida'];	
	}
	if(isset($_POST['radicacion_normal'])){
		$radicacion_normal=$_POST['radicacion_normal'];	
	}	
	if(isset($_POST['radicacion_interna'])){
		$radicacion_interna=$_POST['radicacion_interna'];	
	}	
	if(isset($_POST['radicacion_resoluciones'])){
		$radicacion_resoluciones=$_POST['radicacion_resoluciones'];	
	}

	/* isset de variables para modificar usuario */
	if(isset($_POST['mod_id_usuario'])){	
		$mod_id_usuario=$_POST['mod_id_usuario'];
	}
	if(isset($_POST['mod_identificacion'])){	
		$mod_identificacion=$_POST['mod_identificacion'];
	}
	if(isset($_POST['mod_nombre_completo'])){	
		$mod_nombre_completo=$_POST['mod_nombre_completo'];
	}
	if(isset($_POST['mod_login'])){	
		$mod_login=$_POST['mod_login'];
	}
	if(isset($_POST['mod_mail'])){	
		$mod_mail=$_POST['mod_mail'];
	}
	if(isset($_POST['mod_codigo_dependencia'])){	
		$mod_codigo_dependencia=$_POST['mod_codigo_dependencia'];
	}
	if(isset($_POST['mod_perfil'])){	
		$mod_perfil=$_POST['mod_perfil'];
	}
	if(isset($_POST['mod_estado'])){	
		$mod_estado=$_POST['mod_estado'];
	}
	if(isset($_POST['mod_usuario_nuevo'])){	
		$mod_usuario_nuevo=$_POST['mod_usuario_nuevo'];
	}else{
		$mod_usuario_nuevo="";
	}
	if(isset($_POST['mod_nivel_seguridad'])){	
		$mod_nivel_seguridad=$_POST['mod_nivel_seguridad'];
	}
	if(isset($_POST['mod_jefe_dependencia'])){
		$mod_jefe_dependencia=$_POST['mod_jefe_dependencia'];	
	}
	if(isset($_POST['mod_administrador_sistema'])){
		$mod_administrador_sistema=$_POST['mod_administrador_sistema'];	
	}
	if(isset($_POST['mod_creacion_expedientes'])){
		$mod_creacion_expedientes=$_POST['mod_creacion_expedientes'];
	}
	if(isset($_POST['mod_cuadro_clasificacion'])){
		$mod_cuadro_clasificacion=$_POST['mod_cuadro_clasificacion'];
	}
	if(isset($_POST['mod_inventario'])){
		$mod_inventario=$_POST['mod_inventario'];
	}
	if(isset($_POST['mod_modificar_radicado'])){
		$mod_modificar_radicado=$_POST['mod_modificar_radicado'];
	}
	if(isset($_POST['mod_prestamo_documentos'])){
		$mod_prestamo_documentos=$_POST['mod_prestamo_documentos'];
	}
	if(isset($_POST['mod_scanner'])){	
		$mod_scanner=$_POST['mod_scanner'];
	}
	if(isset($_POST['mod_ubicacion_topografica'])){
		$mod_ubicacion_topografica=$_POST['mod_ubicacion_topografica'];
	}
	if(isset($_POST['mod_ventanilla_radicacion'])){	
		$mod_ventanilla_radicacion=$_POST['mod_ventanilla_radicacion'];
	}
	if(isset($_POST['mod_radicacion_salida'])){
		$mod_radicacion_salida=$_POST['mod_radicacion_salida'];
	}
	if(isset($_POST['mod_radicacion_normal'])){
		$mod_radicacion_normal=$_POST['mod_radicacion_normal'];
	}
	if(isset($_POST['mod_radicacion_interna'])){
		$mod_radicacion_interna=$_POST['mod_radicacion_interna'];
	}
	if(isset($_POST['mod_radicacion_resoluciones'])){
		$mod_radicacion_resoluciones=$_POST['mod_radicacion_resoluciones'];
	}

	if($mod_usuario_nuevo=="SI"){ // Si modifica usuario, actualiza contraseña a 123
		$query_modificar_pass="update usuarios set pass=md5('123') where id_usuario ='$mod_id_usuario'";
		pg_query($conectado,$query_modificar_pass);
	}

	$query_max_usuario 	="select max(id_usuario) from usuarios";
	$fila_usuario 		= pg_query($conectado,$query_max_usuario);
	$linea 				= pg_fetch_array($fila_usuario);
	$max_usuario 		= $linea[0];
	$max_usuario2 		= $max_usuario+1;

	/* Se inicializan variables que bajo condiciones modifican la query para insert o para update */
	$path_firma 			= "";
	$valor_path_firma 		= "";
	$mod_valor_path_firma 	= "";

	/* Si viene como dato la imagen_firma para encriptarla */
	if(isset($_FILES['imagen_firma']) || isset($_FILES['imagen_firma_mod'])){
		if(isset($_FILES['imagen_firma'])){
			$imagen_firma_origen = "imagen_firma";
		}else{
			$imagen_firma_origen = "imagen_firma_mod";
			$login 				 = $mod_login;
		}

		if(move_uploaded_file($_FILES["$imagen_firma_origen"]["tmp_name"],"../bodega_pdf/qr_usuario/firma_$login".".png")){ // Si el formulario envía foto del usuario
			$base64_contenido 	= base64_encode(file_get_contents("../bodega_pdf/qr_usuario/firma_$login".".png"));// El fichero se pasa a cadena y encripta en base64
			$firma_encriptada 	= "data:image/png;base64,$base64_contenido"; // Dato para guardar en base de datos.

			/* El campo de la tabla "usuarios" que se agrega y su valor correspondiente */
			$path_firma 			= ", path_firma";
			$valor_path_firma 		= ", 'data:image/png;base64,$base64_contenido'";

			$mod_valor_path_firma 	= ", path_firma='data:image/png;base64,$base64_contenido'";

			/* Se elimina la firma cargada ya  que se guarda encriptada en la base de datos. */
			unlink("../bodega_pdf/qr_usuario/firma_$login".".png");
		}
	}

	/* Define dependiendo del tipo de formulario la variable $mover_a */
	switch ($tipo_formulario) {
		case 'crear_usuario':
			$mover_a = "imagen";
			break;
		case 'modificar_usuario':
			$mover_a ="imagen_mod";
			break;
	}
	/* Se inicia variable para query de modificar usuario */
	$mod_valor_path_foto = '';
	if(isset($_FILES['imagen']) || isset($_FILES['imagen_mod'])){
		$target_file = basename($_FILES["$mover_a"]["name"]);
		$path_foto 	 = "imagenes/fotos_usuarios/$target_file"; // Variable para guardar en path_foto

		$target_dir  = "../imagenes/fotos_usuarios/";
		// echo "El tamaño maximo del archivo es ".ini_get('upload_max_filesize');// Este es el maximo tamaño permitido en php.ini -> upload_max_filesize
	
		if(move_uploaded_file($_FILES["$mover_a"]["tmp_name"],$target_dir.$target_file)){ // Si el formulario envía foto del usuario
			$mod_valor_path_foto = ", path_foto='$path_foto'"; 
		}else{	// Si el formulario no envía foto del usuario
			$mod_valor_path_foto = "";
			print_r(error_get_last());
		}
	}

	if($tipo_formulario=='crear_usuario'){	// Crea usuario con foto
		$query_usuario="insert into usuarios (id_usuario, documento_usuario, nombre_completo, login, pass, codigo_dependencia, mail_usuario, perfil, path_foto, estado, nivel_seguridad, usuario_nuevo, administrador_sistema, jefe_dependencia, creacion_expedientes, cuadro_clasificacion, inventario, modificar_radicado, prestamo_documentos, ventanilla_radicacion, radicacion_salida, radicacion_normal, radicacion_interna, radicacion_resoluciones, scanner, ubicacion_topografica $path_firma) VALUES('$max_usuario2', '$identificacion', '$nombre_completo', '$login', md5('123'), '$codigo_dependencia', '$mail', '$perfil', '$path_foto', '$estado', '$nivel_seguridad', '$usuario_nuevo', '$administrador_sistema', '$jefe_dependencia', '$creacion_expedientes', '$cuadro_clasificacion', '$inventario', '$modificar_radicado', '$prestamo_documentos', '$ventanilla_radicacion', '$radicacion_salida', '$radicacion_normal', '$radicacion_interna', '$radicacion_resoluciones', '$scanner', '$ubicacion_topografica' $valor_path_firma)";
	}elseif ($tipo_formulario=='modificar_usuario') { // Modifica usuario con foto
		$query_usuario="update usuarios set nombre_completo='$mod_nombre_completo', login='$mod_login', documento_usuario='$mod_identificacion', mail_usuario='$mod_mail', perfil='$mod_perfil', codigo_dependencia='$mod_codigo_dependencia', estado='$mod_estado', usuario_nuevo='$mod_usuario_nuevo', nivel_seguridad='$mod_nivel_seguridad', jefe_dependencia='$mod_jefe_dependencia', administrador_sistema='$mod_administrador_sistema', creacion_expedientes='$mod_creacion_expedientes', cuadro_clasificacion='$mod_cuadro_clasificacion', inventario='$mod_inventario', modificar_radicado='$mod_modificar_radicado', prestamo_documentos='$mod_prestamo_documentos', scanner='$mod_scanner', ubicacion_topografica='$mod_ubicacion_topografica', ventanilla_radicacion='$mod_ventanilla_radicacion', radicacion_salida='$mod_radicacion_salida', radicacion_normal='$mod_radicacion_normal', radicacion_interna='$mod_radicacion_interna', radicacion_resoluciones='$mod_radicacion_resoluciones' $mod_valor_path_foto $mod_valor_path_firma where id_usuario ='$mod_id_usuario'";

		$login=$mod_login;	// Variable para auditoria
	}else{
		echo "<script>
			alert('El formulario para crear/modificar usuario no se pudo enviar.')
		</script>";
	}		

	
	if(pg_query($conectado,$query_usuario)){
		echo "<script> 
			$('#contenido').css({'z-index':'1'});	// Modifico estilo para sobreponer a ventana modal
			auditoria_general('$tipo_formulario','$login');	
		</script>";	
	}else{
		echo "<script>
			alert('No se pudo crear / actualizar el usuario');
			volver();
		</script>";
	}
?>
</body>