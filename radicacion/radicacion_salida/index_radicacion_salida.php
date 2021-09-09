<?php mail_mod
/* Este archivo es el index de la radicación de salida. */
/* Se inicia con validar inactividad y generar fecha para traducir timestamp a fecha legible */
	require_once("../../login/validar_inactividad.php");
	require_once("../../include/genera_fecha.php");

/* Funciones PHP pra definir desplegables de tratamiento y despedida dentro del formulario */

	function consulta_tratamiento($tratamiento){
		$lista_tratos 			= array("Doctor(a)","Estimado(a)","Ingeniero(a)","Señores","Señor(a)");
		$lista_tratamiento1 	= "<select id='tratamiento_doc' onchange='cambia_select_radicacion_salida()'>";

		foreach ($lista_tratos as $key => $value) {
			if($value == $tratamiento){
				$lista_tratamiento1.= "<option value='$value' selected>$value</option>";
			}else{
				$lista_tratamiento1.= "<option value='$value'>$value</option>";
			}
		}
		$lista_tratamiento1.="</select>";
		return $lista_tratamiento1;
	}

	function consulta_despedida($despedida){
		$lista_desp 		= array("Atentamente","Cordialmente","Cordial Saludo");
		$lista_despedida1 	= "<select id='despedida_doc' onchange='cambia_select_radicacion_salida()'>";

		foreach ($lista_desp as $key => $value) {
			if($value == $despedida){
				$lista_despedida1.= "<option value='$value' selected>$value</option>";
			}else{
				$lista_despedida1.= "<option value='$value'>$value</option>";
			}
		}
		$lista_despedida1.="</select>";
		return $lista_despedida1;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<!-- <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/> -->
	<title>Plantilla radicacion salida, interna, respuestas</title>
 	
 	<script type="text/javascript" src="include/js/funciones_radicacion_salida.js"></script>
	<script type="text/javascript" src="include/js/tinymce.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.0.943/pdf.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.js"></script>

	<link rel="stylesheet" href="include/css/estilos_radicacion_salida.css">
 
    <link type="text/css" href="radicacion/radicacion_salida/assets/css/style.css" rel="stylesheet" media="screen"/>
    <link type="text/css" href="radicacion/radicacion_salida/assets/css/imagestyle.css">
</head>

<?php
/* Se verifica si tiene numero de radicado, si lo tiene quiere decir que es una modificación. Si no es asi, quiere decir que es un radicado nuevo */
/* Se definen variables para crear la vista previa y el radicado */
	if(isset($_POST['radicado'])){  		//  Quiere decir que es una Modificación por lo que extrae las variables desde la base de datos en la tabla version_documentos combinada con la tabla radicado
		$radicado  	= $_POST['radicado']; 	

		if(isset($_POST['tipo_radicacion'])){  		//  Quiere decir que es una respuesta por lo que se debe modificar el formulario para coincidir con esta opcion
			$tipo_radicacion 	= $_POST['tipo_radicacion']; // El tipo de radicacion que recibe (respuesta, etc)
			$titulo_plantilla 	= "Plantilla respuesta al radicado $radicado";

			$query_cargar_datos_radicado = "select * from radicado r join datos_origen_radicado d on r.numero_radicado=d.numero_radicado where r.numero_radicado='$radicado'";
			$fila_cargar_datos_radicado 	= pg_query($conectado,$query_cargar_datos_radicado);
			$linea_cargar_datos_radicado 	= pg_fetch_array($fila_cargar_datos_radicado);

			$codigo_serie_mod 			= $linea_cargar_datos_radicado['codigo_serie']; 
			$codigo_subserie_mod 		= $linea_cargar_datos_radicado['codigo_subserie']; 
			$id_expediente 		 		= $linea_cargar_datos_radicado['id_expediente'];
			$id_expediente_mod 	 		= $linea_cargar_datos_radicado['id_expediente'];
			$lista_tratamiento 			= consulta_tratamiento('Señores');
			$tratamiento_mod 			= "";
			$remitente_destinatario_mod	= $linea_cargar_datos_radicado['nombre_remitente_destinatario']; 
			$telefono_mod 				= $linea_cargar_datos_radicado['telefono'];
			$dignatario_mod 			= $linea_cargar_datos_radicado['dignatario'];
			$cargo_destinatario_mod 	= "";
			$ubicacion_mod 				= $linea_cargar_datos_radicado['ubicacion'];
			$direccion_mod 				= $linea_cargar_datos_radicado['direccion']; 
			$asunto_documento 			= "Respuesta a radicado $radicado (".$linea_cargar_datos_radicado['asunto'].")";
			$asunto_mod 				= "";
			$pre_asunto_mod 			= "";
			$despedida_mod 				= ""; 
			$lista_despedida 			= consulta_despedida('Atentamente');
			$mail_mod 					= $linea_cargar_datos_radicado['mail'];
			$anexos_mod 				= "";
			$cc_mod 					= "";
			$firmante_mod 				= "";
			$cargo_firmante_mod 		= "";
			$firmado_mod			 	= "NO";	// Se encuentra firmado digitalmente (SI/NO)
			$aprueba_mod 				= "";
			$cargo_aprueba_mod 			= "";
			$aprobado_mod			 	= "NO";	// Se encuentra aprobado digitalmente (SI/NO)
			$elabora_mod 				= "";
			$cargo_elabora_mod 			= "";
			$version_documento1		 	= "";
			$version_documento 			= 1;
			$codigo_contacto_mod	 	= $linea_cargar_datos_radicado['codigo_contacto'];
			$codigo_carpeta1_mod	 	= $linea_cargar_datos_radicado['codigo_carpeta1'];
			$tipo_radicado 				= '2'; //  Tipo de radicado (3- Interna, 1- Entrada, 2- Salida, etc)

			if($codigo_serie_mod!=''){
				echo "<script>consulta_listado_series2('$codigo_serie_mod','$codigo_dependencia','codigo_serie'); cargar_codigo_subserie2('$codigo_serie_mod','$codigo_subserie_mod','$codigo_dependencia','respuesta_radicado','codigo_subserie')</script>";
			}			
			echo "<script>
				buscar_destinatarios_empresa('$remitente_destinatario_mod');
				$('#sugerencias_destinatario_doc').slideDown('slow'); 
				$('#datos_creacion_radicado').slideDown('slow');
				$('#botones_plantilla_radicacion_salida').slideDown('slow');
				valida_sec('2');
			</script>";
		}else{ 	//  Quiere decir que NO es una respuesta por lo que se debe modificar el formulario para coincidir con esta opcion que en este caso es una modificación al radicado
			$tipo_radicacion  = "";  	// El tipo de radicacion que recibe (respuesta, etc)
			$titulo_plantilla = "Plantilla Modificación de Radicado $radicado";

			/* Tiene el regexp_split_to_array porque no pasaba la version del numero 10. */
			$query_cargar_datos_modificar_radicado = "select * from radicado r join version_documentos v on r.numero_radicado=v.numero_radicado join datos_origen_radicado d on r.numero_radicado=d.numero_radicado where r.numero_radicado='$radicado' order by regexp_split_to_array(version, E'\\\.')::integer[] desc";

			$fila_cargar_datos_modificar_radicado 	= pg_query($conectado,$query_cargar_datos_modificar_radicado);
			$linea_cargar_datos_modificar_radicado 	= pg_fetch_array($fila_cargar_datos_modificar_radicado);
			
			$codigo_serie_mod 						= $linea_cargar_datos_modificar_radicado['codigo_serie']; 
			$codigo_subserie_mod 					= $linea_cargar_datos_modificar_radicado['codigo_subserie']; 
			$id_expediente_mod	 					= $linea_cargar_datos_modificar_radicado['id_expediente'];
			$tratamiento_mod 						= $linea_cargar_datos_modificar_radicado['tratamiento'];
			$lista_tratamiento 						= consulta_tratamiento($tratamiento_mod);
			$remitente_destinatario_mod 			= $linea_cargar_datos_modificar_radicado['nombre_remitente_destinatario']; 
			$telefono_mod 							= $linea_cargar_datos_modificar_radicado['telefono'];
			$dignatario_mod 						= $linea_cargar_datos_modificar_radicado['dignatario'];
			$cargo_destinatario_mod 				= $linea_cargar_datos_modificar_radicado['cargo_destinatario'];
			$ubicacion_mod 							= $linea_cargar_datos_modificar_radicado['ubicacion'];
			$direccion_mod 							= $linea_cargar_datos_modificar_radicado['direccion']; 
			$asunto_mod 							= $linea_cargar_datos_modificar_radicado['asunto'];
			$asunto_documento 						= $asunto_mod;
			$pre_asunto_mod 						= $linea_cargar_datos_modificar_radicado['html_asunto']; 
			$despedida_mod 							= $linea_cargar_datos_modificar_radicado['despedida']; 
			$lista_despedida 						= consulta_despedida($despedida_mod);
			$mail_mod 								= $linea_cargar_datos_modificar_radicado['mail'];
			
			$anexos_mod 							= $linea_cargar_datos_modificar_radicado['descripcion_anexos'];
			$cc_mod		 							= $linea_cargar_datos_modificar_radicado['con_copia_a'];
			$firmante_mod		 					= $linea_cargar_datos_modificar_radicado['usuario_que_firma'];
			$cargo_firmante_mod		 				= $linea_cargar_datos_modificar_radicado['cargo_usuario_que_firma'];
			$firmado_mod			 				= $linea_cargar_datos_modificar_radicado['firmado'];
			$aprueba_mod		 					= $linea_cargar_datos_modificar_radicado['usuario_que_aprueba'];
			$cargo_aprueba_mod		 				= $linea_cargar_datos_modificar_radicado['cargo_usuario_que_aprueba'];
			$aprobado_mod			 				= $linea_cargar_datos_modificar_radicado['aprobado'];
			$elabora_mod		 					= $linea_cargar_datos_modificar_radicado['usuario_que_elabora'];
			$cargo_elabora_mod		 				= $linea_cargar_datos_modificar_radicado['cargo_usuario_que_elabora'];
			$version_documento1		 				= $linea_cargar_datos_modificar_radicado['version'];
			$version_documento 						= $version_documento1+1;
			$codigo_contacto_mod	 				= $linea_cargar_datos_modificar_radicado['codigo_contacto'];
			$codigo_carpeta1_mod	 				= $linea_cargar_datos_modificar_radicado['codigo_carpeta1'];
			$tipo_radicado 							= '2'; //  Tipo de radicado (3- Interna, 1- Entrada, 2- Salida, etc)

			// Valida que la secuencia de salida si exista y carga la serie y subserie
			echo "<script>valida_sec('2'); consulta_listado_series2('$codigo_serie_mod','$codigo_dependencia','codigo_serie'); cargar_codigo_subserie2('$codigo_serie_mod','$codigo_subserie_mod','$codigo_dependencia','respuesta_radicado','codigo_subserie')</script>"; 
		}
	}else{		// Si es un radicado nuevo por lo que va definiendo las variables para dar la vista previa y generar el documento	
		$titulo_plantilla 			= "Plantilla Radicación de Salida ";
		$tipo_radicado 				= '2'; 	//  Tipo de radicado (3- Interna, 1- Entrada, 2- Salida, etc)
		$tipo_radicacion  			= "";  	// El tipo de radicacion que recibe (respuesta, etc)
		$asunto_documento 			= "";
		$codigo_serie_mod 			= ""; 
		$codigo_subserie_mod 		= ""; 
		$asunto_mod 				= "";
		$pre_asunto_mod 			= "";
		$remitente_destinatario_mod = "Persona Natural";		
		$telefono_mod 				= "";		
		$dignatario_mod 			= "";		
		$cargo_destinatario_mod 	= "";
		$ubicacion_mod 				= "BOGOTA, D.C. (BOGOTA) COLOMBIA-AMERICA";
		$direccion_mod 				= "";
		$mail_mod 					= "";
		$anexos_mod 				= "";
		$cc_mod 					= "";
		$firmante_mod 				= "";
		$cargo_firmante_mod 		= "";
		$aprueba_mod 				= "";
		$cargo_aprueba_mod 			= "";
		$elabora_mod 				= "";
		$cargo_elabora_mod 			= "";
		$version_documento 			= "1";
		$codigo_contacto_mod 		= "1";
		$id_expediente_mod 	 		= "";
		$radicado 					= "";
		$codigo_carpeta1_mod 		= "";
		$aprobado_mod 				= "NO";
		$lista_despedida 			= consulta_despedida('Atentamente');
		$lista_tratamiento 			= consulta_tratamiento('Señores');
	}

/* Hasta aqui variables para crear la vista previa y el radicado */


 	$codigo_entidad 	= $_SESSION['codigo_entidad'];

 	switch ($codigo_entidad) {
 		case 'AV1':
			$ciudad 			= "Villeta,"; // Ciudad para el encabezado de la plantilla.
			$path_encabezado 	= '../../imagenes/logos_entidades/encabezado_rad_av1.png';
			$path_piedepagina 	= '../../imagenes/logos_entidades/pie_rad_av1.png';

			break;
 		case 'EJC':
 		case 'EJEC':
			$ciudad 			= "Bogotá,"; // Ciudad para el encabezado de la plantilla.
 			break;
 		
 		default:
			$ciudad 			= "Bogotá,"; // Ciudad para el encabezado de la plantilla.
			$path_encabezado 	= '../../imagenes/encabezado_radicado.png';
			$path_piedepagina 	= '../../imagenes/pie_de_pagina_radicado.png';
 			break;
 	}

	/*Fecha que se realiza la transaccion (hoy)*/	
    $fechaDocumento 	= $b->traduce_fecha_letra($date); // Traduce fecha formato "17 de Julio del 2019"
	$fecha 				= "$ciudad $fechaDocumento";

	 
	// Extensión de las imagenes de encabezado y pie de pagina para la plantilla
	$type_encabezado	= pathinfo($path_encabezado, PATHINFO_EXTENSION);
	$type_piedepagina	= pathinfo($path_piedepagina, PATHINFO_EXTENSION);
	 
	// Cargando las imagenes de encabezado y pie de pagina para la plantilla
	$data_encabezado 	= file_get_contents($path_encabezado);
	$data_piedepagina	= file_get_contents($path_piedepagina);
	 
	// Decodificando las imagenes de encabezado y pie de pagina en base64
	$base64_encabezado 	= 'data:image/' . $type_encabezado . ';base64,' . base64_encode($data_encabezado);
	$base64_piedepagina = 'data:image/' . $type_piedepagina . ';base64,' . base64_encode($data_piedepagina);
	 
	// Mostrando las imagenes del encabezado y pie de pagina
	// echo '<img src="'.$base64_encabezado.'"/>';
	// echo '<img src="'.$base64_piedepagina.'"/>';

	/* Variable de sesion que trae la ruta de la imagen de la firma mecánica del usuario */
	// Comento para usar esta variable desde el dato path_firma de la tabla "usuario"
	// $path_firma1 	= $_SESSION['path_firma'];

	// if($path_firma1!=""){
	// 	$path_firma		= "../../imagenes/fotos_usuarios/$path_firma1";
	// 	$type_firma		= pathinfo($path_firma1, PATHINFO_EXTENSION);
	// 	$data_firma 	= file_get_contents($path_firma);
	// 	$base64_firma 	= 'data:image/' . $type_firma . ';base64,' . base64_encode($data_firma);
	// }else{
	// 	$base64_firma 	= "";
	// }


	// echo '<img src="'.$base64_firma.'"/>';

	/* Desde la sesion trae el nombre de usuario y el login */
	$nombre_usuario = $_SESSION['nombre'];
	$login_usuario 	= $_SESSION['login'];

	/* Inicio PRUEBAS definiendo aprobado y/o firmado */
	$indicador_aprobado = "";
	if($aprobado_mod=="SI"){
		$indicador_aprobado="<div class='detalle center' title='Documento ya aprobado'>El documento ya ha sido aprobado por el usuario <font color='green'>$aprueba_mod</font>.</div>";
	}else{
		if($nombre_usuario==$aprueba_mod){
			$indicador_aprobado = "<div class='art_exp center' style='background: #2aa646;' title='Aprobar Documento' onclick=\"cargar_aprueba('$login_usuario','$radicado','aprobar')\"><img src='imagenes/iconos/aprobar_documento.png' height='35px;'></div>";
		}else{
			$indicador_aprobado = "<div class='descripcion center' title='Falta por aprobar documento'>Falta que el usuario <font color='red'>$aprueba_mod</font> apruebe el documento.</div>";
		}
	}
	/* Fin PRUEBAS definiendo aprobado y/o firmado */
?>
<body>
	<div class="center">
		<br>
		<h1 style="margin-top:-10px;"><?php echo $titulo_plantilla ?></h1>		
	</div>
	<div>
		<div id="pdf" style="height: 700px; width: 50%; overflow: scroll; float: left;" class="col-md-5">
    	<div id="example1" style="height: 850px; display: none;"></div>
    	<iframe align="center" class="pdfobject-container centered" id="visor" style="height:850px;"></iframe>
   
	<!-- Validar consecutivo de la dependencia radicadora y tipo de documento -->
	</div>
	<div id="formulario_datos_radicado" style="height: 700px; width: 50%; overflow: scroll; float: left;" class="col-md-5">
        <div style="text-align: center;">
       		<table border="1">
				<tr>					
					<td class="descripcion" width="20%">
						<input type="hidden" id="nuevo_nombre_documento">
						<input type="hidden" id="codigo_dependencia" value="<?php echo $codigo_dependencia; ?>" readonly>
						<input type="hidden" id="nombre_dependencia" value="<?php echo $nombre_dependencia; ?>" readonly>
						Codigo Serie :
					</td>
					<td class="detalle" width="30%">
						<select id='codigo_serie' title='Seleccione el código de la serie documental' class='select_opciones' <?php echo "onchange='cargar_codigo_subserie2(this.value,\"\",\"$codigo_dependencia\",\"radicacion_salida\",\"codigo_subserie\")'"; ?>>
						</select>
						<div id="error_codigo_serie" class="errores">Debe seleccionar por lo menos una serie del listado</div>
					</td>
				</tr>
				<tr>	
					<td class="descripcion" width="20%">
						Codigo Subserie
					</td>
					<td class="detalle" width="30%" >
						<select id='codigo_subserie' title='Seleccione el código de la serie documental' class='select_opciones' onchange='validar_serie_subserie()'>
							<option value=''>No hay subseries asociadas a la serie seleccionada</option>
						</select>
						<div id="error_codigo_subserie" class="errores">No existen subseries asociadas a la serie seleccionada</div>
						<div id="error2_codigo_subserie" class="errores">Debe seleccionar por lo menos una subserie del listado</div>
					</td>
				</tr>
				<tr id="input_seleccionar_expediente" class="hidden">
					<td class="descripcion ">
						Expediente al que va a pertenecer éste documento
					</td>
					<td class="detalle">
						<input type="hidden" id="id_expediente" <?php echo "value='$id_expediente_mod'" ?>>	
						<input type="hidden" id="codigo_contacto" <?php echo "value='$codigo_contacto_mod'" ?>>	
						<input type="search" id="seleccionar_expediente" class="input_search" placeholder="Este campo es opcional. No es obligatorio" title="Este campo es opcional. No es obligatorio" readonly>
						
						<div id="seleccionar_expediente_max" class="errores">El campo de expediente no puede ser mayor a 100 caracteres (numeros o letras)</div>
						<div id="seleccionar_expediente_min" class="errores">El campo de expediente no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="seleccionar_expediente_null" class="errores">El asunto o nombre del expediente es obligatorio</div>
						<div id="error_seleccionar_expediente" class="errores">El numero o asunto del expediente no existe en el inventario. Ingrese por favor un numero o asunto de expediente válido</div>	
						<div id="seleccionar_expediente_invalido" class="errores">Debe seleccionar un asunto o nombre de expediente válido.</div>	

						<div id="resultado_seleccionar_expediente" style="overflow-x: auto;max-height: 100px;"></div>
					</td>
				</tr>
			</table>			
			<input type="hidden" id="numero_radicado" <?php echo "value='$radicado'"; ?>>
			<input type="hidden" id="nuevo_nombre_documento" value="" placeholder="Nuevo nombre del documento">
			<!-- <table id="tabla_formulario_salida" border="0" style="display: none; width: 100%;"> -->
			<table id="tabla_formulario_salida" border="0">
				<tr height="20px">	
					<td class="descripcion" height="20px;" width="5%">
						Fecha del Documento 
					</td>
					<td width="45%" title=<?php echo "'$fecha'"; ?>>
						<input type="search" id="fecha_doc"  class="input_search" value=<?php echo "'$fecha'"; ?> readonly >
					</td>
				
					<td class="descripcion" width="5%" >
						Tratamiento
					</td>
					<td width="45%">
						<?php echo $lista_tratamiento; ?>
					</td>
				</tr>
				<tr>
					<td class="descripcion" height="20px"  title='Empresa / Entidad a quien va dirigido el documento'>
						Empresa / Entidad / Persona Natural
					</td>
					<td title='Empresa / Entidad a quien va dirigido el documento'>
						<?php echo "<input type='search' id='empresa_destinatario_doc' class='input_search' value='$remitente_destinatario_mod' onblur='trim(\"empresa_destinatario_doc\")'>"; ?>
						<div id="sugerencias_empresa_destinatario_doc" style="max-height: 200px; overflow: scroll; display:none;"></div>

						<div id="empresa_destinatario_doc_max" class="errores">El nombre de la Empresa / Entidad a quien va dirigido el documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='empresa_destinatario_doc_contadormax'></u></b> caracteres)</div>		
						<div id="empresa_destinatario_doc_min" class="errores">El nombre de la Empresa / Entidad a quien va dirigido el documento no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="empresa_destinatario_doc_null" class="errores">Debe ingresar el nombre de la Empresa / Entidad a quien va dirigido el documento</div>
					</td>
				
					<td class="descripcion" height="20px" title="Teléfono de contacto de la persona al a quien va dirigido el documento">
						Telefono
					</td>
					<td title="Teléfono de contacto de la persona al a quien va dirigido el documento">
						<input type="search" id="telefono_remitente"  class="input_search" <?php echo "value='$telefono_mod'"; ?> onblur="trim('telefono_remitente')">
						<div id="telefono_remitente_max" class="errores">El nombre de la Empresa / Entidad a quien va dirigido el documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='telefono_remitente_contadormax'></u></b> caracteres)</div>		
						<div id="telefono_remitente_min" class="errores">El nombre de la Empresa / Entidad a quien va dirigido el documento no puede ser menor a 6 caracteres (numeros o letras)</div>
					</td>
				</tr>
				<tr>
					<td class="descripcion" height="20px;" title="Persona dentro de la Empresa / Entidad a la que va dirigido el documento" >
						Destinatario
					</td>
					<td title="Persona dentro de la Empresa / Entidad a la que va dirigido el documento">
						<input type="search" id="destinatario_doc"  class="input_search" <?php echo "value='$dignatario_mod'"; ?> onblur="trim('destinatario_doc')">
						<div id="sugerencias_destinatario_doc" style="max-height: 200px; overflow: scroll; display:none;"></div>

						<div id="destinatario_doc_max" class="errores">El nombre del destinatario a quien va dirigido el documento no puede ser mayor a 100 caracteres. (Actualmente <b><u id='destinatario_doc_contadormax'></u></b> caracteres)</div>		
						<div id="destinatario_doc_min" class="errores">El nombre del destinatario a quien va dirigido el documento no puede ser menor a 3 caracteres (numeros o letras)</div>
						<div id="destinatario_doc_null" class="errores">Debe ingresar el nombre del destinatario a quien va dirigido el documento</div>

						<div id="error_selecciona_destinatario_doc" class="errores">Debe seleccionar un nombre del destinatario a quien va dirigido el documento</div>
					</td>
			
					<td class="descripcion" height="20px;"  title="Cargo de la persona a la que va dirigido el documento">
						Cargo de <span id="representante_legal1"> Destinatario</span>
					</td>
					<td title="Cargo de la persona a la que va dirigido el documento">
						<input type="search" id="cargo_titular_doc" class="input_search" onblur="trim('cargo_titular_doc')">
					</td>
				</tr>
				<tr>
					<td class="descripcion" height="20px" title="Ubicación (Ciudad) donde se encuentra la persona al a quien va dirigido el documento">
						Ubicacion (Ciudad)
					</td>
					<td title="Ubicación (Ciudad) donde se encuentra la persona al a quien va dirigido el documento">
						<input type="search" id="ubicacion_doc" <?php echo "value='$ubicacion_mod'"; ?> class="input_search"  onblur="trim('ubicacion_doc')">
						<div id="sugerencias_ubicacion_doc" style="max-height: 200px; overflow: scroll; display:none;"></div>
						<div id="error_ubicacion_remitente" class='errores'>La ubicación ingresada no existe en la base de datos. Por favor comuníquese con el administrador del sistema para crearla.</div>
					</td>
					<td class="descripcion" height="20px" title="Dirección a la que va a ser enviado en físico el documento por mensajería">
						Direccion
					</td>
					<td title="Dirección a la que va a ser enviado en físico el documento por mensajería">
						<input type="search" id="direccion_doc" <?php echo "value='$direccion_mod'"; ?> class="input_search" onblur="trim('direccion_doc')">
					</td>
				</tr>
			</table>
			<!-- <table id="datos_creacion_radicado" style="display: none;">	 -->
			<table id="datos_creacion_radicado" >	
				<!-- <input type="button" onclick="llenar_tinymce()" value="Llenar con contenido">
				<input type="button" onclick="copiar_tinymce()" value="Copiar Contenido"> -->
<?php 
			echo "
				<input type='hidden' id='tipo_radicado' value='$tipo_radicado'>
				<input type='hidden' id='tipo_radicacion' value='$tipo_radicacion' placeholder='tipo_radicacion'>
				<input type='hidden' id='radicado_padre' value='$radicado' placeholder='radicado_padre'>
				<input type='hidden' id='codigo_serie_mod' value='$codigo_serie_mod' placeholder='codigo_serie_mod'>
				<input type='hidden' id='codigo_subserie_mod' value='$codigo_subserie_mod'>
				<input type='hidden' id='asunto_mod' value='$asunto_mod'>
				<input type='hidden' id='pre_asunto' value='$pre_asunto_mod'>
				<input type='hidden' id='cargo_destinatario_mod' value='$cargo_destinatario_mod'>
				<input type='hidden' id='ubicacion_mod' value='$ubicacion_mod'>
				<input type='hidden' id='direccion_mod' value='$direccion_mod'>
				<input type='hidden' id='mail_mod' value='$mail_mod'>
				<input type='hidden' id='anexos_mod' value='$anexos_mod'>
				<input type='hidden' id='cc_mod' value='$cc_mod'>
				<input type='hidden' id='firmante_mod' value='$firmante_mod'>
				<input type='hidden' id='cargo_firmante_mod' value='$cargo_firmante_mod'>
				<input type='hidden' id='aprueba_mod' value='$aprueba_mod'>
				<input type='hidden' id='cargo_aprueba_mod' value='$cargo_aprueba_mod'>
				<input type='hidden' id='elabora_mod' value='$elabora_mod'>
				<input type='hidden' id='cargo_elabora_mod' value='$cargo_elabora_mod'>
				<input type='hidden' id='version_documento' value='$version_documento'>
				<input type='hidden' id='usuario_actual' value='$codigo_carpeta1_mod'>
			";
 ?>				
				<tr>	
					<td class="descripcion" height="20px;">
						Asunto
					</td>
					<td colspan="3">
						<input type="search" id="asunto_doc" <?php echo "value='$asunto_documento'"; ?> class="input_search" title='Referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software.' placeholder="Ingrese la referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software" onblur="trim('asunto_doc')">

	 					<div id="asunto_doc_max" class="errores">La referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software no puede ser mayor a 500 caracteres. (Actualmente <b><u id='asunto_doc_contadormax'></u></b> caracteres)</div>		
	 					<div id="asunto_doc_min" class="errores">La referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software no puede ser menor a 6 caracteres (numeros o letras)</div>
	 					<div id="asunto_doc_null" class="errores">Debe ingresar la referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software</div>

						<textarea id="editor"></textarea>
					</td>
				</tr>
				<tr height="20px">	
					<td class="descripcion" width="5%">
						Despedida
					</td>
					<td width="45%">
						<?php echo $lista_despedida; ?>
					</td>
					<td class="descripcion" height="20px" width="5%" title="Mail al cual va a ser enviado en digital el documento">
						Mail
					</td>
					<td width="45%" title="Mail al cual va a ser enviado en digital el documento">
						<input type="search" id="mail_doc" <?php echo "value='$mail_mod'"; ?> class="input_search" onblur="trim('mail_doc')">
						<div id="mail_doc_formato_mail" class="errores">
								El mail ingresado no tiene formato correcto (usuario@algunmail.com) por lo que no se puede ingresar.
						</div>
						<div id="mail_doc_max" class="errores">La referencia, asunto ó metadatos con los que se va a encontrar éste documento dentro del software no puede ser mayor a 50 caracteres. (Actualmente <b><u id='mail_doc_contadormax'></u></b> caracteres)</div>		
					</td>
				</tr>
				<tr height="20px">
					<td class="descripcion" title="Anexos a éste documento de respuesta">
						Anexos 
					</td>
					<td title="Anexos a éste documento de respuesta">
						<input type="search" id="anexos_doc"  class="input_search" onblur="trim('anexos_doc')">	
					</td>
					<td class="descripcion" title="A quién se le debe enviar copia de éste documento con sus anexos.">
						Con Copia a
					</td>
					<td title="A quién se le debe enviar copia de éste documento con sus anexos.">
						<input type="search" id="cc_doc"  class="input_search" onblur="trim('cc_doc')">	
					</td>
				
				</tr>
				<tr height="20px">
					<td class="descripcion" title="Funcionario que firma éste documento">
						<input type='hidden' id='lista_usuario_actual' <?php echo "value='$login'" ?>>
						<input type='hidden' id='login_firmante'>
						Firmante
					</td>
					<td title="Funcionario que firma éste documento">
						<input type="search" id="firmante_doc" class="input_search" onblur="trim('firmante_doc')">	
						<div id="sugerencias_firmante" style="max-height: 200px; overflow: scroll; display:none;"></div>
						<div id="error_firmante" class="errores">No se han encontrado resultados</div>
						<div id="firmante_doc_null" class="errores">Debe ingresar el nombre del firmante del documento</div>
					</td>
					<td class="descripcion" title="Cargo del funcionario que firma éste documento">
						Cargo de <span id="cargo_firmante_rs"> quien firma éste documento</span>
					</td>
					<td title="Cargo del funcionario que firma éste documento">
						<input type="search" id="cargo_firmante_doc" class="input_search" onblur="trim('cargo_firmante_doc')">	
						<div id="cargo_firmante_doc_null" class="errores">Debe ingresar el cargo del firmante del documento</div>
					</td>
				
				</tr>
				<tr height="20px">
					<td class="descripcion" title="Funcionario que debe dar su visto bueno para aporbar éste documento antes de ser radicado.">
						<input type='hidden' id='login_aprueba'>
						Aprueba 
					</td>
					<td>
						<input type="search" id="aprueba_doc" class="input_search" onblur="trim('aprueba_doc')" title="Funcionario que debe dar su visto bueno para aporbar éste documento antes de ser radicado.">	
						<div id="aprueba_doc_null" class="errores">No se han encontrado resultados</div>
						<div id="sugerencias_aprueba" style="max-height: 200px; overflow: scroll; display:none;"></div>

						<div id="indicador_aprobado"><?php echo $indicador_aprobado; ?></div>
					</td>
					<td class="descripcion" title="Cargo del funcionario que debe dar su visto bueno para aporbar éste documento antes de ser radicado.">
						Cargo de <span id="aprueba_rs"> quien aprueba éste documento</span> 
					</td>
					<td title="Cargo del funcionario que debe dar su visto bueno para aporbar éste documento antes de ser radicado.">
						<input type="search" id="cargo_aprueba_doc" class="input_search" onblur="trim('cargo_aprueba_doc')">	
						<div id="cargo_aprueba_doc_null" class="errores">Debe ingresar el cargo del funcionario que aprueba el documento</div>
					</td>
				
				</tr>
				<tr height="20px">
					<td class="descripcion" title="Funcionario que redacta ésta respuesta.">
						<input type='hidden' id='login_elabora'>
						Elaborado por 
					</td>
					<td title="Funcionario que redacta éste documento.">
						<input type="search" id="elabora_doc"  class="input_search" onblur="trim('elabora_doc')">	
						<div id="sugerencias_elabora" style="max-height: 200px; overflow: scroll; display:none;"></div>
						<div id="elabora_doc_null" class="errores">Debe ingresar el nombre del funcionario que elabora el documento</div>
					</td>
					<td class="descripcion" title="Cargo del funcionario que redacta éste documento.">
						Cargo de <span id="elabora_rs"> quien elabora éste documento</span> 
					</td>
					<td title="Cargo del funcionario redacta éste documento.">
						<input type="search" id="cargo_elabora_doc" class="input_search" onblur="trim('cargo_elabora_doc')">	
						<div id="cargo_elabora_doc_null" class="errores">Debe ingresar el cargo del funcionario que redacta el documento</div>
					</td>
					<?php 
						echo "
		                    <input type='hidden' id='headerImg' value='$base64_encabezado'>
		                    <input type='hidden' id='footerImg' value='$base64_piedepagina'>               
		                    <input type='hidden' id='firmaImg' value='$base64_firma'>               
		                    <input type='hidden' id='nombre_usuario' value='$nombre_usuario'>
						";
					?>                       
				</tr>
			</table>
		</div>
	</div>
	<center>
		<!-- <div id="botones_plantilla_radicacion_salida" style="display:none;"> -->
		<div id="botones_plantilla_radicacion_salida">
			<input type="button" id="verPdf" class="botones center" value="Vista Previa del Documento" title="Vista previa del documento antes de guardar PDF generado">
			<span id="contenedor_boton_descargar_plantilla_respuesta" style="display: none; text-decoration: none;">
				<input type="button" class="botones2 center" id="enviarHtml" value='<?php echo "Generar Versión $version_documento del documento"; ?>' title='<?php echo "Generar documento en formato PDF con la versión $version_documento del documento"; ?>'>
			</span>
		</div>			
	</center>

<!-- Div que contiene ventana modal para solicitar prestamo --> 
    <div id="ventana_aprobar_documento" class="ventana_modal">
        <div class="form">
            <div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
            <h1>Formulario aprobar documento</h1>
            <hr>
            <form method="post" autocomplete="off">
                <table border ="0">
                    <tr>
                        <td class="descripcion" width="30%">Contraseña del usuario <?php echo $nombre_usuario ?></td>
                        <td class="detalle">
                        	<input type="hidden" id="tipo_aprueba_firma">
                            <input type="password" id="contr_confirma_aprobado" title="Ingrese su password para aprobar aquí." placeholder="Ingrese su password para aprobar aqui">
							<div id="error_contr_confirma_aprobado" class="errores">La contraseña no corresponde al usuario que aprueba el documento</div>
                        </td>   
                    </tr>
                    <tr>
                        <td class="descripcion">Observaciones :</td>
                        <td class="detalle" colspan="3">
                            <textarea id="observaciones_aprobar_documento" rows="2" style="width:100%;padding:5px;" placeholder="Ingrese las observaciones. Sea lo más específico posible" title="Ingrese las observaciones. Sea lo más específico posible" onblur="trim('observaciones_aprobar_documento')" ></textarea>
                            <div id="observaciones_aprobar_documento_null" class="errores">El mensaje de observaciones es obligatorio</div>
                            <div id="observaciones_aprobar_documento_min" class="errores">El mensaje de observaciones no puede ser menor a 6 caracteres (numeros o letras) </div>
                            <div id="observaciones_aprobar_documento_max" class="errores">El nombre del expediente no puede ser mayor a 200 caracteres. (Actualmente <b><u id='observaciones_aprobar_documento_contadormax'></u></b> caracteres)</div>		
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" id="fila_boton_solicitar_documento">
                            <center>
                            	<input type="button" value="Aprobar Documento" class="botones" onclick="validar_aprueba_firma()">
                            <center>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<!-- Hasta aqui el div que contiene ventana modal para solicitar prestamo -->
	<script>
		var iframe = document.getElementById('visor'), iframedoc = iframe.contentDocument || iframe.contentWindow.document; //visor iframe
   
		var serie = $("#codigo_serie_mod").val();
		if(serie==""){
			<?php echo "setTimeout('consulta_listado_series2(\"\",\"$codigo_dependencia\",\"codigo_serie\");', 500);" ?>
		}else{
			setTimeout ("cargar_datos_modificar_radicado()", 1500); // Cargar datos para modificar radicado
		}

		var nuevo_nombre_documento = Math.floor(Math.random() * (99999 - 10000)) + 10000;
		$("#nuevo_nombre_documento").val(nuevo_nombre_documento);
        // console.log(nuevo_nombre_documento);

		// setTimeout ("sendPost();", 1000); 

		// valida_sec('2');

		$(document).ready(function(){
			loaderTiny("#editor",300,1000);
		});
		
		window.htmlPdf = null;
	</script>
</body>
</html>