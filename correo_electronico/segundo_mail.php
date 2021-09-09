<?php
	if(!isset($_SESSION)){
	    session_start();
	}
	/* Estructura para poder generar un pdf */
	require_once("../include/dompdf/dompdf_config.inc.php");
	require_once("../login/conexion2.php");
	/* Fin estructura para poder generar un pdf */
	/* Leemeos el archivo con el contenido de cada usuario al momento de radicar por buzon de correo electronico */
	$archivo_info_pdf = fopen("../bodega_pdf/correo_electronico/baul/".$_SESSION['login']."/info_correo_electronico_radicar_".$_SESSION['login'].".txt", "r");// Abrimos el archivo y lo leemos indicandole la "r" 
	while (!feof($archivo_info_pdf)){// Leemos cada linea hasta llegar a la ultima del archivo correspondiente
	    $informacion_txt = fread($archivo_info_pdf, 1048576);// Con fread hacemos la lectura del fichero("modo binario seguro") indicandole un valor de 1048576 bytes o 1 megabyte || si es necesario subir este valor hacerlo pero con valores mas altos mas propenso a corromperse
	}
	fclose($archivo_info_pdf);// Cerramos el archivo anterior mente abuierto con fopen
	$informacion_txt = trim(preg_replace('/\s+/', ' ', $informacion_txt));// Cuando se crea el json y se guarda en el archivo puede generar barra inlcinada, las eliminamos 
	$informacion_txt = json_decode($informacion_txt, true);// Hacemos una decodificacion de lo extraido en formato json
	$mensaje_html 	 = $informacion_txt["mensaje"][0];// Tomamos el mensaje html de todo el correo
	$mensaje_html    = str_replace("||", "&", $mensaje_html);// Se elimina llaves de la codificacion - FILTRO 1
	$mensaje_html    = str_replace("^^", "#", $mensaje_html);// Se elimina llaves de la codificacion - FILTRO 2
	$mensaje_html 	 = html_entity_decode($mensaje_html);// Se pasa codificacion utf-8 a html
	if($informacion_txt["anexos"][0] == "sin_archivos"){// Si el mensaje a radicar no contiene archivos anexos
		$anexos_completo = '';
	} else{
		$anexos_completo = '<td colspan="2"><table style="width:100%">';
		foreach ($informacion_txt["anexos"] as $anexos_nombres) {// Por cada nombre de archivos anexos se repite
			$tamo_archivo = filesize('anexos/info_correo_electronico_radicar.txt');// Se verifica el tamaño del archivo
			$anexos_completo_pre .= '<tr>
										<td style="width:5%">
											<img src="../imagenes/iconos/archivo_pdf.png" style="width:40px; height:45px">
										</td>
										<td>'.$anexos_nombres.'<br>'.$tamo_archivo.' bytes</td>
									 </tr>';
		}
		$anexos_completo .= $anexos_completo_pre.'</table></td>';
	}
	$orientacion_papel  = "portrait"; // "portrait" o "landscape"
	$nombre_reporte    = "Reporte Radicados";
	$timestamp          = date('Y-m-d');
	$codigoHTML      = '<table style="width:100%">
				<tr>
					<td style="width:55%">
					</td>
					<td style="width:45%">
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						Radicado Jonas ("'.$radicado.'")
					</td>
				</tr>
				<tr>
				  	<td>
						<img src="../imagenes/iconos/logo_largo.png" style="width:130px;height:80px;">					
					</td>
					<td>
						<p align="right" style="font:14.3px"><b>'.$informacion_txt["informacion"][1].' &lt;'.$informacion_txt["informacion"][2].'&gt;</b></p>
					</td>
				</tr>
				<tr>
				  	<td colspan="2">
				  		<hr>
				  	</td>
				</tr>
				<tr>
				  	<td colspan="2">
				  		<p style="margin-top:-0.3%"><b>'.$informacion_txt["informacion"][0].'</b><br>'.$informacion_txt["informacion"][3].' mensaje</p>
				  	</td>
				</tr>
				<tr>
				  	<td colspan="2">
				  		<hr style="margin-top:-1.1%">
				 	</td>
				</tr>
				<tr>
				  	<td>
				  		<p style="margin-top:-1.1%"><b>'.$informacion_txt["informacion"][4].'</b> &lt;'.$informacion_txt["informacion"][5].'&gt;<br>Para: "'.$informacion_txt["informacion"][1].'" &lt;'.$informacion_txt["informacion"][2].'&gt;</p>
				  	</td>
				  	<td>
				  		<p align="right" style="margin-top:-3%">'.$informacion_txt["informacion"][6].'</p>
				 	</td>
				</tr>
				<tr>
				  	<td colspan="2">
				  		<div style="padding:30px;">
				  		
						</div>
						<br>
						<br>
				  	</td>
				</tr>
			  	<tr>
			  		<td>
			  			<p align="left"><hr></p>
				  	</td>
				  	<td>
				  	</td>
				</tr>
				<tr>
				  	<td colspan="2">
				  		<p style="margin-top:-1.1%"><b>'.$informacion_txt["informacion"][7].' archivos adjuntos</b></p>
				  	</td>
				</tr>
				<tr>
					<p>'.$anexos_completo.'</p>
				</tr>
			</table><hr><hr>
			<b>Mensaje: </b>
			'.$mensaje_html;
	/* PDF documento creador */
	$codigoHTML = utf8_decode($codigoHTML);// Aplicamos una nueva codificacion para enviar a pdf
	$dompdf 	= new DOMPDF();
	$dompdf->set_paper("A4", "$orientacion_papel");
	$dompdf->load_html($codigoHTML);
	ini_set("memory_limit","128M");// Establece el valor de una directiva de configuración
	$dompdf->render();
	// $dompdf->stream("".$radicado.".pdf");
	file_put_contents(// Se guarda el pdf en la ruta definida
	    "../bodega_pdf/radicados/".$radicado."_".$numero_aleatorio.".pdf",
	    $dompdf->output()
	);
	/* Fin PDF documento creador */
?> 