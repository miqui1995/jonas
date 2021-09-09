<?php
	require_once("../../var/www/html/jonas/reporte_diario/envio_mail/Mailer2/Mailer.php");
	$tabla_sin_imagen_pdf = '';
	$tabla_sin_asunto = '';
	$tabla = '';
	$tabla2 = '<br>
				<h3 align = "center">
					Todas las Dependencias que Tienen Radicados sin Terminar
				</h3>
				<br>
				<center>
					<table border="0" class="center">
						<tr>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Dependencia</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Jefe de la Dependencia</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Usuario Radicador</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicados Completos</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicados sin Imagen PDF</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicados sin Asunto</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicados Totales</center></td>
						</tr>';// Estructura html
	require_once("../../var/www/html/jonas/login/conexion2.php");// Conexión a base de datos
	$fecha 		  = "26-06-2020";// genera la fecha de hoy
	$fecha_inical = $fecha." 00:00:00";// Se agrega el rango de búsqueda en horas a la fecha actual
	$fecha_final  = $fecha." 23:59:59";// Se agrega el rango de búsqueda en horas a la fecha actual
	/* Se verifica las dependencias que tienen al menos un usuario con radicado pendiente */
	$sentencia1 = "select r.dependencia_radicador, d.nombre_dependencia
					from radicado as r
					inner join dependencias as d on d.codigo_dependencia = r.dependencia_radicador
					group by r.dependencia_radicador, d.nombre_dependencia";// Estructura sql
	$fila_sentencia1 = pg_query($conectado,$sentencia1);//Se enviá la consulta mediante pg_query a postgres
	/* Fin se verifica las dependencias que tienen al menos un usuario con radicado pendiente */
	while($rows_sentencia1 = pg_fetch_array($fila_sentencia1)){// El bucle se ejecutara la cantidad de columnas extraídas en la consulta
		$i = 0;
		/* Recoger información del jefe de la dependencia */
		$sentencia2 = "select nombre_completo, login, mail_usuario
						from usuarios
						where codigo_dependencia = '$rows_sentencia1[0]' and jefe_dependencia = 'SI'";// Estructura sql
		$fila_sentencia2 = pg_query($conectado,$sentencia2);//Se enviá la consulta mediante pg_query
		$rows_sentencia2 = pg_fetch_array($fila_sentencia2);// Se pasan los resultados a un array
		/* Fin recoger información del jefe de la dependencia */
		$tabla .= '<br>
				<h3 align = "center">
					('.$rows_sentencia1[0].') -'. $rows_sentencia1[1].'
				</h3>
				<br>
				<center>
					<table border="0">
						<tr>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Dependencia</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Jefe de la Dependencia</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Usuario Radicador</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicados Completos</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicados sin Imagen PDF</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicados sin Asunto</center></td>
							<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicados Totales</center></td>
						</tr>';// Estructura html
		/* Se recogen datos de los usuarios radicadores*/
		$sentencia3 = "select count(r.*), u.nombre_completo, r.usuario_radicador
						from radicado as r
						inner join usuarios as u on u.login = r.usuario_radicador
						where r.dependencia_radicador = '$rows_sentencia1[0]'
						group by u.nombre_completo, r.usuario_radicador";// Estructura sql
		$fila_sentencia3 = pg_query($conectado,$sentencia3);//Se enviá la consulta mediante pg_query
		/* Fin se recogen datos de los usuarios radicadores*/
		$tabla_sin_imagen_pdf .= '<center>
										<table>
											<tr>
												<td style="width:49%" valign="top">
													<h3 align="center">Usuarios que Tienen Archivos sin Imagen PDF</h3><br>
													<table border="0">
														<tr>
															<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Archivos</center></td>
															<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Usuario Radicador</center></td>
															<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicado</center></td>
															<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Fecha Radicado</center></td>
														</tr>';
		$tabla_sin_asunto .= '<td style="width:49%" valign="top">
								<h3 align="center">Usuarios que Tienen Archivos sin Asunto</h3><br>
								<table border="0">
									<tr>
										<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Archivos</center></td>
										<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Usuario Radicador</center></td>
										<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Radicado</center></td>
										<td style="background: #2D9DC6; border-radius: 3px; color: #FFFFFF; padding: 5px;"><center>Fecha Radicado</center></td>
									</tr>';
		while($rows_sentencia3 = pg_fetch_array($fila_sentencia3)){// El bucle se ejecutara la cantidad de columnas extraídas en la consulta
			/* Se recoge datos de los usuario en especifico PARTE 1: se recoge numero de radicados sin imagen Pdf */
			$sentencia4 = "select numero_radicado, fecha_radicado
							from radicado
							where (path_radicado = '' or path_radicado is null) and (asunto <> '' or asunto <> NULL) and usuario_radicador = '$rows_sentencia3[2]'";// Estructura sql
			$fila_sentencia4 = pg_query($conectado,$sentencia4);//Se enviá la consulta mediante pg_query
			/* Fin se recoge datos de los usuario en especifico PARTE 2: se recoge numero de radicados sin Imagen Pdf */
			$j = 0;
			while($rows_sentencia4 = pg_fetch_array($fila_sentencia4)){// El bucle se ejecutara la cantidad de columnas extraídas en la consulta
				if($j == 0){
					$tabla_sin_imagen_pdf .= '<tr>
												<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;" rowspan="'.pg_num_rows($fila_sentencia4).'"><center>'.pg_num_rows($fila_sentencia4).'</center></td>';
				}else{
					$tabla_sin_imagen_pdf .= '<tr>';
				}
				$tabla_sin_imagen_pdf .= '<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;"><center>('.$rows_sentencia3[2].') -'. $rows_sentencia3[1].'</center></td>
											<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;"><center>'.$rows_sentencia4[0].'</center></td>
											<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;"><center>'.$rows_sentencia4[1].'</center></td>
									</tr>';
				$j++;
				}
			/* Se recoge datos de los usuario en especifico PARTE 2: se recoge numero de radicados sin Asunto */
			$sentencia5 = "select numero_radicado, fecha_radicado
							from radicado
							where (asunto = '' or asunto is NULL) and usuario_radicador = '$rows_sentencia3[2]'";// Estructura sql
			$fila_sentencia5 = pg_query($conectado,$sentencia5);//Se enviá la consulta mediante pg_query
			/* Fin se recoge datos de los usuario en especifico PARTE 2: se recoge numero de radicados sin Asunto */
			$j = 0;
			while($rows_sentencia5 = pg_fetch_array($fila_sentencia5)){// El bucle se ejecutara la cantidad de columnas extraídas en la consulta
				if($j == 0){
					$tabla_sin_asunto .= '<tr>
											<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;" rowspan="'.pg_num_rows($fila_sentencia5).'"><center>'.pg_num_rows($fila_sentencia5).'</center></td>';
				}else{
					$tabla_sin_asunto .= '<tr>';
				}
				$tabla_sin_asunto .= '<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;"><center>('.$rows_sentencia3[2].') -'.$rows_sentencia3[1].'</center></td>
											<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;"><center>'.$rows_sentencia5[0].'</center></td>
											<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;"><center>'.$rows_sentencia5[1].'</center></td>
									</tr>';
				$j++;
			}
			$radicados_completos = $rows_sentencia3[0]-(pg_num_rows($fila_sentencia4)+pg_num_rows($fila_sentencia5));// Sacamos el numero de radicados que completo el usuario restándole los radicados sin asunto y sin imagen a la cantidad total de radicados
			if($i==0){
				if($rows_sentencia2[1] == "" || $rows_sentencia2[1] == null){
					$rows_sentencia2_1_completa = '<center><font color="red">No Hay Jefe Asignado</font></center>';
				}else{
					$rows_sentencia2_1_completa = '<center>('.$rows_sentencia2[1].') -'. $rows_sentencia2[0].'</center>';
				}
				$tabla_resultados = '<tr>
										<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;" rowspan="'.pg_num_rows($fila_sentencia3).'" style="padding-right:20px;">
											<center>'.$rows_sentencia1[0].' - '.$rows_sentencia1[1].'</center>
										</td>
										<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;" rowspan="'.pg_num_rows($fila_sentencia3).'" style="padding-right:20px;">
											<center>'.$rows_sentencia2_1_completa.'</center>
										</td>';
				$tabla .= $tabla_resultados;
				$tabla2 .= $tabla_resultados;
			}else{
				$tabla_resultados2 = '<tr>';
				$tabla .= $tabla_resultados2;
				$tabla2 .= $tabla_resultados2;
			}
			if(pg_num_rows($fila_sentencia4) > 0){
				$imagen_pdf_rojo = '<font color="red">'.pg_num_rows($fila_sentencia4).'</font>';
			}else{
				$imagen_pdf_rojo = pg_num_rows($fila_sentencia4);
			}
			if(pg_num_rows($fila_sentencia5) > 0){
				$asunto_rojo = '<font color="red">'.pg_num_rows($fila_sentencia5).'</font>';
			}else{
				$asunto_rojo = pg_num_rows($fila_sentencia5);
			}
			$tabla_resultados3 = '<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;" style="padding-right:20px;"><center>
										'.$rows_sentencia3[2].' - ('.$rows_sentencia3[1].')
									</center></td>
									<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;" style="padding-right:20px;"><center>
										'.$radicados_completos.'
									</center></td>
									<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;" style="padding-right:20px;"><center>
										'.$imagen_pdf_rojo.'
									</center></td>
									<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;" style="padding-right:20px;"><center>
										'.$asunto_rojo.'
									</center></td>
									<td style="background: #E0E3E7; border-radius: 3px; padding: 5px;" style="padding-right:20px;"><center>
										'.$rows_sentencia3[0].'
									</center></td>
								</tr>';
			$tabla .= $tabla_resultados3;
			$tabla2 .= $tabla_resultados3;
			$i++;
		}
		if(pg_num_rows($fila_sentencia4) == 0){
			$tabla_sin_imagen_pdf = '<td style="width:49%" valign="top"><center><h3>No Posee Radicados sin Imagen PDF Pendientes</h3></center>';
		}else{
			$tabla_sin_imagen_pdf .= '</table>';
		}
		if(pg_num_rows($fila_sentencia5) == 0){
			$tabla_sin_asunto = '<td style="width:49%" valign="top"><center><h3>No Posee Radicados sin Asunto Pendientes</h3></center>';
		}else{
			$tabla_sin_asunto .= '</table>';
		}
		$tabla .= '</table></center><br><br>';
		$tabla .= $tabla_sin_imagen_pdf.'</td></td>';
		$tabla .= $tabla_sin_asunto.'</td></tr></table><br><hr><hr></center>';
		$tabla_codificada = preg_replace("/[\r\n|\n|\r]+/",  " ", $tabla);
		$asunto                    = "Reporte Diario de Radicados No Terminados";
		$mail_usuario              = "$rows_sentencia2[2]";
		$contenido_html_mail_envio = "$tabla_codificada";
		$nombre_completo_imprimir  = "$rows_sentencia2[0]";
		
		$config["mailer"] = [
		    'SMTP_DEBUG' => 2,
		    // Habilitar salida de depuración detallada
		    'MAIL_DRIVER' => 'smtp',
		    //Configurar la aplicación de correo para usar SMTP, driver: smpt, mail, sendmail, qmail
		    // 'HOST' => 'smtp.gmail.com', // Este es cuando se utiliza GMAIL
		    'HOST' => 'mail.gammacorp.co ',
		    //Especificar servidores SMTP principales y de respaldo   //DNS o SMTP
		    'AUTH' => true,
		    //Habilitar autenticación SMTP
		    // 'USERNAME' => 'enviomailgammacorp@gmail.com', // Esto es cuando se utiliza GMAIL
		    'USERNAME' => 'notificador_automatico_ejercito@gammacorp.co',
		    //usuario mailer (gmail, hotmail, yahoo, etc..)
		    // 'PASSWORD' => 'A123456789*',         // Esto es cuando se utiliza GMAIL
		    'PASSWORD' => '0rfeojonas*',
		    //contraseña de usuario mailer
		    'SECURE' => 'ssl',
		    //Habilite el cifrado TLS, también se acepta `ssl`
		    // 'PORT' => 587,           // Esto es cuando se utiliza GMAIL
		    'PORT' => 465,
		    //Puerto TCP para conectarse
		];
		$config["patch"] = dirname(dirname(__FILE__)) . '/envio_mail/Mailer2/'; //raiz del proyecto (nombre de la carpeta donde se encuentra este)
		$mail = new Mailer($config);


		//se toma el dato contenido_html_mail_envio que trae la estructura de como se va a visualizar el correo
		$html =  $contenido_html_mail_envio;


		$mail->setHtml($html);
		$bool = $mail->sendMail(
		    [
		        "subject" => "$asunto",
		        "from"    => [
		            "mail" => "noresponder@gammacorp.com",
		            "name" => "Notificador Automatico Jonas Gamma Corp"
		        ],
		        "address" => [
		            [
		                "mail" => "$mail_usuario",
		                "name" => "$nombre_completo_imprimir"
		            ]
		        ]
		    ]
		);
		$tabla = '';
		$tabla_sin_imagen_pdf = '';
		$tabla_sin_asunto = '';
	}
	/* Se necesita saber el correo del administrador */
	$sentencia = "select nombre_completo, login, mail_usuario
				 	from usuarios
					where login = 'ADMINISTRADOR'";// Estructura sql
	$fila_sentencia = pg_query($conectado,$sentencia);//Se enviá la consulta mediante pg_query
	$rows_sentencia = pg_fetch_array($fila_sentencia);// Se pasan los resultados a un array
	/* Fin se necesita saber el correo del administrador */
	$tabla2 .= "</table></center><br><hr><br>";
	$tabla_codificada2 = preg_replace("/[\r\n|\n|\r]+/",  " ", $tabla2);

	$asunto                    = "Reporte Diario de Radicados No Terminados";
		$mail_usuario              = "$rows_sentencia[2]";
		$contenido_html_mail_envio = "$tabla_codificada2";
		$nombre_completo_imprimir  = "$rows_sentencia[0]";
	$config["mailer"] = [
		    'SMTP_DEBUG' => 2,
		    // Habilitar salida de depuración detallada
		    'MAIL_DRIVER' => 'smtp',
		    //Configurar la aplicación de correo para usar SMTP, driver: smpt, mail, sendmail, qmail
		    // 'HOST' => 'smtp.gmail.com', // Este es cuando se utiliza GMAIL
		    'HOST' => 'mail.gammacorp.co ',
		    //Especificar servidores SMTP principales y de respaldo   //DNS o SMTP
		    'AUTH' => true,
		    //Habilitar autenticación SMTP
		    // 'USERNAME' => 'enviomailgammacorp@gmail.com', // Esto es cuando se utiliza GMAIL
		    'USERNAME' => 'notificador_automatico_ejercito@gammacorp.co',
		    //usuario mailer (gmail, hotmail, yahoo, etc..)
		    // 'PASSWORD' => 'A123456789*',         // Esto es cuando se utiliza GMAIL
		    'PASSWORD' => '0rfeojonas*',
		    //contraseña de usuario mailer
		    'SECURE' => 'ssl',
		    //Habilite el cifrado TLS, también se acepta `ssl`
		    // 'PORT' => 587,           // Esto es cuando se utiliza GMAIL
		    'PORT' => 465,
		    //Puerto TCP para conectarse
		];
		$config["patch"] = dirname(dirname(__FILE__)) . '/envio_mail/Mailer2/'; //raiz del proyecto (nombre de la carpeta donde se encuentra este)
		$mail = new Mailer($config);


		//se toma el dato contenido_html_mail_envio que trae la estructura de como se va a visualizar el correo
		$html =  $contenido_html_mail_envio;


		$mail->setHtml($html);
		$bool = $mail->sendMail(
		    [
		        "subject" => "$asunto",
		        "from"    => [
		            "mail" => "noresponder@gammacorp.com",
		            "name" => "Notificador Automatico Jonas Gamma Corp"
		        ],
		        "address" => [
		            [
		                "mail" => "$mail_usuario",
		                "name" => "$nombre_completo_imprimir"
		            ]
		        ]
		    ]
		);
	?>