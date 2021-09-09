<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Query de Tipo Documento</title>
	<!-- <script type="text/javascript" src="../include/js/jquery.js"></script> -->
	<!-- <script type="text/javascript" src="../include/js/funciones_parametrizacion.js"></script> -->
	<!-- <script src="../include/js/sweetalert2.js"></script> -->
	<!-- <link rel="stylesheet" type="text/css" href="../include/css/sweetalert2.css"> -->
</head>
<body>
<?php 
	require_once('../login/conexion2.php');

	if(isset($_POST['tipo_formulario'])){
		$tipo_formulario=$_POST['tipo_formulario'];
	}
	// var_dump($tipo_formulario);	
	switch ($tipo_formulario) {
		case 'crear_tipo_documento':
			$tipo_doc=$_POST['tipo_doc'];
			$descripcion=$_POST['descripcion'];
			$termino=$_POST['termino'];
			
			$query_max_tipo_documento="select max(codigo_tipo_doc) from tipo_doc_termino";

			$fila_max_id_td = pg_query($conectado,$query_max_tipo_documento);
			$linea_td = pg_fetch_array($fila_max_id_td);

			$max_tipo_documento = $linea_td[0];
			$max_tipo_documento2= $max_tipo_documento+1;

			$query="insert into tipo_doc_termino (codigo_tipo_doc, tipo_documento, descripcion_tipo_documento, tiempo_tramite, activo) 
			values ('$max_tipo_documento2', '$tipo_doc', '$descripcion', '$termino', 'SI')";
			break;
		case 'modificar_tipo_documento':
			$id_mod=$_POST['id_mod'];
			$tipo_doc_mod=$_POST['tipo_doc_mod'];
			$tipo_doc_mod_ant=$_POST['tipo_doc_mod_ant'];
			$descripcion_mod=$_POST['descripcion_mod'];
			$mod_termino=$_POST['mod_termino'];
			$mod_estado=$_POST['mod_estado'];

			$query="update tipo_doc_termino set tipo_documento='$tipo_doc_mod', descripcion_tipo_documento='$descripcion_mod', tiempo_tramite='$mod_termino', activo='$mod_estado' where codigo_tipo_doc='$id_mod'";
			$tipo_doc=$tipo_doc_mod_ant; // Variable para auditoria.
			break;		
		case 'crear_tipo_documento_pqr':
			$tipo_doc_pqr=$_POST['tipo_doc_pqr'];
			$descripcion_pqr=$_POST['descripcion_pqr'];
			$termino_pqr=$_POST['termino_pqr'];
			
			$query_max_tipo_documento_pqr="select max(codigo_tipo_doc) from tipo_doc_termino_pqr";

			$fila_max_id_td_pqr = pg_query($conectado,$query_max_tipo_documento_pqr);
			$linea_td_pqr = pg_fetch_array($fila_max_id_td_pqr);

			$max_tipo_documento_pqr = $linea_td_pqr[0];
			$max_tipo_documento_pqr2= $max_tipo_documento_pqr+1;

			$query="insert into tipo_doc_termino_pqr (codigo_tipo_doc, tipo_documento, descripcion_tipo_documento, tiempo_tramite, activo) 
			values ('$max_tipo_documento_pqr2', '$tipo_doc_pqr', '$descripcion_pqr', '$termino_pqr', 'SI')";
			$tipo_doc=$tipo_doc_pqr; // Variable para auditoria.
			break;	
		case 'modificar_tipo_documento_pqr':
			$id_mod=$_POST['id_mod_pqr'];
			$tipo_doc_mod=$_POST['tipo_doc_mod_pqr'];
			$tipo_doc_mod_ant=$_POST['tipo_doc_mod_ant_pqr'];
			$descripcion_pqr_mod=$_POST['descripcion_pqr_mod'];
			$mod_termino=$_POST['mod_termino_pqr'];
			$mod_estado=$_POST['mod_estado_pqr'];

			$query="update tipo_doc_termino_pqr set tipo_documento='$tipo_doc_mod', descripcion_tipo_documento='$descripcion_pqr_mod', tiempo_tramite='$mod_termino', activo='$mod_estado' where codigo_tipo_doc='$id_mod'";
			$tipo_doc=$tipo_doc_mod_ant; // Variable para auditoria.
			break;	
		case 'crear_tipo_radicado':
			$id_tr=$_POST['codigo_tipo_rad'];
			$nombre_tr=$_POST['nombre_tipo_rad'];

			$query="insert into tipo_radicado (codigo_tipo_radicado, tipo_radicado) 
			values ('$id_tr', '$nombre_tr')";
			$tipo_doc=$nombre_tr; // Variable para auditoria.
			break;	
		case 'crear_secuencia':
		//var_dump($_POST);
		    $year=date("Y"); // Se obtiene el año en formato 4 digitos 
			$codigo_dependencia=$_POST['codigo_dependencia_sec'];
			$tipo_rad=$_POST['tipo_rad'];
			$codigo_dependencia_padre=$_POST['codigo_dependencia_padre_sec'];
			$query="insert into consecutivos (year, codigo_dependencia, tipo_radicado, dependencia_consecutivo_padre) values('$year', '$codigo_dependencia', '$tipo_rad', '$codigo_dependencia_padre')"; // Esta es la query que ejecuta para auditoria
			$tipo_doc="$codigo_dependencia Tipo radicado $tipo_rad"; // Variable para auditoria.
			
			$query_secuencia="create sequence SECUENCIA_".$codigo_dependencia."_"."$tipo_rad
					  start with 1
					  increment by 1
					  maxvalue 999999
					  minvalue 1";
			pg_query($conectado,$query_secuencia);	

		/* Valida si la dependencia padre ya tiene consecutivo */	
			if($codigo_dependencia!=$codigo_dependencia_padre){ // Valida si la dependencia es la misma que la dependencia padre.
				$query_valida_depe_padre="select * from consecutivos WHERE tipo_radicado='$tipo_rad' and codigo_dependencia='$codigo_dependencia_padre'";
				$fila_valida_depe_padre = pg_query($conectado,$query_valida_depe_padre); 

				$linea_valida_depe_padre = pg_fetch_array($fila_valida_depe_padre);
				if($linea_valida_depe_padre==false){  // Valida si existe la secuencia del tipo de radicado - dependencia_padre
					$query_secuencia_padre="create sequence SECUENCIA_".$codigo_dependencia_padre."_"."$tipo_rad
						start with 1
						increment by 1
						maxvalue 999999
						minvalue 1";	

					$query_padre="insert into consecutivos (year, codigo_dependencia, tipo_radicado, dependencia_consecutivo_padre) values('$year', '$codigo_dependencia_padre', '$tipo_rad', '$codigo_dependencia_padre')";	

					pg_query($conectado,$query_secuencia_padre);	
					pg_query($conectado,$query_padre);	
				}				
			}
		/* Fin valida si la dependencia padre ya tiene consecutivo */	
			break;	
		case 'modificar_consecutivo':

		    $year=date("Y"); // Se obtiene el año en formato 4 digitos 
			$codigo_dependencia_sec_mod_ant=$_POST['codigo_dependencia_sec_mod_ant'];
			$tipo_radicado_sec_mod_ant=$_POST['tipo_radicado_sec_mod_ant'];
			$codigo_dependencia_padre_sec_mod=$_POST['codigo_dependencia_padre_sec_mod'];

			$query="update consecutivos set dependencia_consecutivo_padre='$codigo_dependencia_padre_sec_mod' where codigo_dependencia='$codigo_dependencia_sec_mod_ant' and tipo_radicado='$tipo_radicado_sec_mod_ant'";
			$tipo_doc=$codigo_dependencia_sec_mod_ant; // Variable para auditoria.

			/* Valida si la dependencia padre ya tiene consecutivo */	
			$query_valida_depe_padre="select * from consecutivos WHERE tipo_radicado='$tipo_radicado_sec_mod_ant' and codigo_dependencia='$codigo_dependencia_padre_sec_mod'";
			$fila_valida_depe_padre = pg_query($conectado,$query_valida_depe_padre); 

			$linea_valida_depe_padre = pg_fetch_array($fila_valida_depe_padre);
			if($linea_valida_depe_padre==false){  // Valida si existe la secuencia del tipo de radicado - dependencia_padre
				$query_secuencia_padre="create sequence SECUENCIA_".$codigo_dependencia_padre_sec_mod."_"."$tipo_radicado_sec_mod_ant
					start with 1
					increment by 1
					maxvalue 999999
					minvalue 1";	

				$query_padre="insert into consecutivos (year, codigo_dependencia, tipo_radicado, dependencia_consecutivo_padre) values('$year', '$codigo_dependencia_padre_sec_mod', '$tipo_radicado_sec_mod_ant', '$codigo_dependencia_padre_sec_mod')";	

				pg_query($conectado,$query_secuencia_padre);	
				pg_query($conectado,$query_padre);	
			}
		/* Fin valida si la dependencia padre ya tiene consecutivo */	

			break;	
		default:
			# code...
			break;
	}
		
	if(pg_query($conectado,$query)){
		echo "<script> 
			auditoria_general('$tipo_formulario','$tipo_doc');	
		</script>";
	
	}else{
		echo "<script>
			alert('No se pudo crear / actualizar el usuario');
			volver();
		</script>";
	}
?>
</body>