<?php
/*******************************************************************************************************/
/* ESTE ARCHIVO YA NO TIENE UTILIDAD YA QUE EL MODULO HA SIDO MODIFICADO */
/*******************************************************************************************************/
// 	require_once("../../login/validar_inactividad.php");
// 	require_once("../../include/genera_fecha.php");

// 	if(isset($_POST['radicado'])){
// 		$radicado 	 		= $_POST['radicado'];
// 		$encabezado_asunto 	= "Respuesta al radicado $radicado";
// 		$titulo 			= "Plantilla respuesta al radicado $radicado";
// 	}else{
// 		$encabezado_asunto 	= "";
// 		$titulo 			= "Se genera radicación de salida sin respuesta";
// 	}
// 	$ciudad = "Bogotá,"; // Ciudad para el encabezado de la plantilla.

// 	/*Fecha que se realiza la transaccion (hoy)*/	
//     $fechaDocumento = $b->traduce_fecha_letra($date); // Traduce fecha formato "17 de Julio del 2019"
// 	$fecha = "$ciudad $fechaDocumento";

// 	$query_destinatario ="select * from datos_origen_radicado where numero_radicado='$radicado'";
// 	$fila = pg_query($conectado,$query_destinatario);
// 	$linea = pg_fetch_array($fila);

// 	$nombre_remitente_destinatario 	= $linea['nombre_remitente_destinatario'];
// 	$dignatario 					= $linea['dignatario'];
// 	$direccion 						= $linea['direccion'];

// 	$empresa_destinatario 	= $nombre_remitente_destinatario;

// 	if($dignatario==""){
// 		$titular_radicado 		= $nombre_remitente_destinatario;
// 	}else{
// 		$titular_radicado = $dignatario;
// 	}

// 	$cargo_titular ="";

?>
<!-- <!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Buscador de Remitente</title>
	<script type="text/javascript" src="include/js/funciones_radicacion_salida.js"></script>
	<link rel="stylesheet" href="include/css/estilos_entrada.css">
</head>
<body>
-->
<!-- Validar consecutivo de la dependencia radicadora y tipo de documento -->
<!--
<script type="text/javascript"> valida_sec('2');</script>
	<div class="center">
		<br>
		<h1 style="margin-top:-10px;"><?php // print_r($titulo); ?></h1>		
	</div>
	<div style="height: 532px; overflow: scroll; float: left;">
		<input type="hidden" id="numero_radicado" value=<?php // print_r($radicado); ?>>
		<table border="0" style="width: 550px;">
			<tr height="20px">	
				<td class="descripcion" height="20px;" width="10%">
					Fecha del Documento 
				</td>
				<td width="20%" title=<?php // echo "'$fecha'"; ?>>
					<input type="text" id="fecha_doc"  class="input_search" value=<?php // echo "'$fecha'"; ?> disabled >
				</td>
			
				<td class="descripcion" width="10%" >
					Tratamiento
				</td>
				<td width="20%">
					<select id="tratamiento_doc" onchange="cambia_tratamiento()">
						<option>Doctor (a)</option>
						<option>Estimado (a)</option>
						<option>Ingeniero (a)</option>
						<option selected="selected">Señores</option>
						<option>Señor (a)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="descripcion" height="20px;" title="Persona a la que va dirigida la respuesta" >
					Destinatario
				</td>
				<td title="Persona a la que va dirigida la respuesta">
					<input type="search" id="destinatario_doc"  class="input_search" value=<?php // echo "'$titular_radicado'"; ?> disabled >
				</td>
		
				<td class="descripcion" height="20px;"  title="Cargo de la persona a la que va dirigida la respuesta">
					Cargo Destinatario
				</td>
				<td title="Cargo de la persona a la que va dirigida la respuesta">
					<input type="search" id="cargo_titular_doc"  class="input_search" onkeyup='cambia_titular_doc(this.value)' value=<?php // echo "'$cargo_titular'"; ?> >
				</td>
			</tr>
			<tr>
				<td class="descripcion" height="20px"  title='Empresa / Entidad a la que pertenece el destinatario a quien va dirigida la respuesta'>
					Empresa / Entidad
				</td>
				<td title='Empresa / Entidad a la que pertenece el destinatario a quien va dirigida la respuesta'>
					<input type="search" id="empresa_destinatario_doc"  class="input_search" value=<?php // echo "'$empresa_destinatario'"; ?> disabled>
				</td>
			
				<td class="descripcion" height="20px" title="Dirección a la que va a ser enviada la respuesta">
					Direccion
				</td>
				<td title="Dirección física donde se va a enviar por correspondencia la respuesta">
					<input type="search" id="direccion_doc"  class="input_search" onkeyup='cambia_direccion_doc(this.value)' value=<?php  // echo "'$direccion'"; ?>>
				</td>
			</tr>
			<tr>
				<td class="descripcion" height="20px;">
					Asunto
				</td>
				<td colspan="3">
					<?php // print_r($encabezado_asunto); ?><br>
					<textarea id="asunto_doc" class="input_search" onkeyup='cambia_asunto_doc(this.value)'></textarea>
				</td>
			</tr>
			<tr height="20px">	
				<td class="descripcion"  >
					Despedida
				</td>
				<td>
					<select id="despedida_doc" onchange="cambia_despedida()">
						<option selected="selected">Atentamente</option>
						<option>Cordialmente</option>
						<option>Cordial Saludo</option>
					</select>
				</td>
			
				<td class="descripcion" title="Funcionario que firma éste documento de respuesta">
					Firmante
				</td>
				<td title="Funcionario que firma éste documento de respuesta">
					<input type="search" id="firmante_doc"  class="input_search">	
					<div id="sugerencias_firmante" style="max-height: 200px; overflow: scroll; display:none;"></div>
					<div id="error_firmante" class="errores">No se han encontrado resultados</div>
					<div id="firmante_doc_null" class="errores">Debe ingresar el nombre del firmante del documento</div>
				</td>
			</tr>
			<tr height="20px">
				<td class="descripcion" title="Cargo del funcionario que firma éste documento de respuesta">
					Cargo del Firmante
				</td>
				<td title="Cargo del funcionario que firma éste documento de respuesta">
					<input type="search" id="cargo_firmante_doc"  class="input_search" onkeyup='cambia_cargo_funcionario_doc(this.value)'>	
					<div id="cargo_firmante_doc_null" class="errores">Debe ingresar el cargo del firmante del documento</div>
				</td>
			
				<td class="descripcion" title="Anexos a éste documento de respuesta">
					Anexos 
				</td>
				<td title="Anexos a éste documento de respuesta">
					<input type="search" id="anexos_doc"  class="input_search" onkeyup='cambia_anexos_doc(this.value)'>	
				</td>
			</tr>
			<tr height="20px">
				<td class="descripcion" title="A quién se le debe enviar copia de ésta respuesta con sus anexos.">
					Con Copia a
				</td>
				<td title="A quién se le debe enviar copia de ésta respuesta con sus anexos.">
					<input type="search" id="cc_doc"  class="input_search" onkeyup='cambia_cc_doc(this.value)'>	
				</td>
			
				<td class="descripcion" title="Funcionario que debe dar su visto bueno para aporbar ésta respuesta antes de ser radicada.">
					Aprueba 
				</td>
				<td title="Funcionario que debe dar su visto bueno para aporbar ésta respuesta antes de ser radicada.">
					<input type="search" id="aprueba_doc"  class="input_search">	
					<div id="aprueba_doc_null" class="errores">No se han encontrado resultados</div>
					<div id="sugerencias_aprueba" style="max-height: 200px; overflow: scroll; display:none;"></div>
				</td>
			</tr>
			<tr height="20px">
				<td class="descripcion" title="Cargo del funcionario que debe dar su visto bueno para aporbar ésta respuesta antes de ser radicada.">
					Cargo Aprueba 
				</td>
				<td title="Cargo del funcionario que debe dar su visto bueno para aporbar ésta respuesta antes de ser radicada.">
					<input type="search" id="cargo_aprueba_doc" onkeyup='cambia_cargo_aprueba_doc(this.value)' class="input_search">	
					<div id="cargo_aprueba_doc_null" class="errores">Debe ingresar el cargo del funcionario que aprueba el documento</div>
				</td>
			
				<td class="descripcion" title="Funcionario que redacta ésta respuesta.">
					Elaborado por 
				</td>
				<td title="Funcionario que redacta ésta respuesta.">
					<input type="search" id="elabora_doc"  class="input_search">	
					<div id="sugerencias_elabora" style="max-height: 200px; overflow: scroll; display:none;"></div>
					<div id="elabora_doc_null" class="errores">Debe ingresar el nombre del funcionario que elabora el documento</div>
				</td>
			</tr>
			<tr height="20px">
				<td class="descripcion" title="Cargo del funcionario que redacta ésta respuesta.">
					Cargo Elaborado por 
				</td>
				<td title="Cargo del funcionario redacta ésta respuesta.">
					<input type="search" id="cargo_elabora_doc" onkeyup='cambia_cargo_elabora_doc(this.value)' class="input_search">	
					<div id="cargo_elabora_doc_null" class="errores">Debe ingresar el cargo del funcionario que redacta el documento</div>
				</td>
			</tr>
		</table>
	</div>
-->
		<!-- <td width="525px" height="655px" style="background: url('imagenes/plantilla_oficio.png') no-repeat; overflow: scroll;" rowspan="20"> -->
			<!--
	<div style="background: url('imagenes/plantilla_oficio.png') no-repeat; float: left; height: 532px; width: 415px;" >
		<center>
		<table border="0" style="font-size: 10px; height: 500px; width: 85%; ">
			<tr height="50px">
				<td></td>
			</tr>
			<tr>
				<td width="50%" height="20px;">
					<div id="fecha_rs"></div>
				</td>
				<td width="50%" style="text-align: center;">
					<img src="imagenes/iconos/codigo_barras.jpg" height="25px" width="200px"><br>
					Al contestar cite éste No. de Radicado
				</td>
			</tr>
			<tr>
				<td colspan="2" height="20px;">
					<span id='tratamiento_rs'></span> <br>
					<span id='destinatario_rs'></span> <br>
					<span id="contenedor_cargo_titular_rs">
						<span id="cargo_titular_rs"></span>
					</span>
					<span id="empresa_destinatario_rs"></span><br>
					<span id="direccion_rs"></span><br>
					Ciudad
				</td>
			</tr>
			<tr>
				<td height="20px;">
					
				</td>
				<td></td>
			</tr>
			<tr>
				<td height="20px" colspan="2" style="text-align: justify;">
					<b>Asunto : </b> <?php // print_r($encabezado_asunto); ?>
					<span id="asunto_rs"></span>
				</td>
			</tr>
			<tr>
				<td height="100px"></td>
			</tr>
			<tr>
				<td height="20px">
					<span id="despedida_rs">Atentamente</span>
				</td>
			</tr>
			<tr>
				<td height="40px;"></td>
			</tr>
			<tr>
				<td height="15px" colspan="2">
					<span id="firmante_rs"></span>
					<br>
					<span id="cargo_firmante_rs"></span>
				</td>
			</tr>
			<tr>
				<td height="15px" colspan="2">
				</td>
			</tr>
			<tr>
				<td height="20px" colspan="2" style="font-size: 8px;">
					Anexos : <span id="anexos_rs">Sin anexos</span>
					<br>
					C.C : <span id="cc_rs">Sin copias</span>
					<br>
					Aprobado por : <span id="aprueba_rs"></span><span id="cargo_aprueba_rs"></span>
					<br>
					Elaborado por : <span id="elabora_rs"></span><span id="cargo_elabora_rs"></span>
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
		</table>
		</center>
	</div>
-->
<!--Desde aqui los div que se muestran por default al cargar index_salida.php-->
<!--
	<br><br>
	<div class="center">
		 <div id="contenedor_boton_descargar_plantilla_respuesta" style="text-decoration: none;"></div>
		 <div id="contenedor_boton_validar_plantilla_respuesta">
			 <input type="button" class="botones2 center" onclick="validar_descargar_plantilla_respuesta()" value="Validar Campos Plantilla" title="Validar si los campos mínimos para descargar la plantilla se han diligenciado correctamente.">
		 </div>
	</div>		
	<script>carga_valores_plantilla();</script>
</body>
</html> -->