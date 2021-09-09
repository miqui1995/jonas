<?php 
/** 
* @brief Este archivo es la API que recibe desde un aplicativo externo teniendo en cuenta la arquitectura REST y RESTFULL un JSON con su respectiva imagen en formato “base64”, datos de un remitente para ser ingresado a la base de datos lo cual va a generar un número de radicado dentro del aplicativo el cual va a ser devuelto como respuesta al llamar el método correctamente para su posterior consulta. 
En caso que alguno de los campos obligatorios no sean enviados, el valor devuelto sería un JSON con la información del error. 
El request debe ser un objeto en formato JSON llamado  radicar_documento y debe contener los atributos asunto, nombre_remitente_destinatario, dignatario, ubicación, direccion, telefono, mail, archivo_pdf
*
* Un ejemplo del formato que se recibe es el siguiente:
********************************************************************************************************
@param json_object radicar_documento
{
    "radicar_documento":[{
            "asunto" 						: "Este es el asunto de pruebas de recepcion de radicado",
            "nombre_remitente_destinatario" : "Terranum",
            "dignatario" 					: "Manuel Alberto Piedrahita Melo",
            "ubicación" 					: "BOGOTA, D.C. (BOGOTA) COLOMBIA-AMERICA",
            "direccion" 					: "Ac. 26 #92-32",
            "telefono" 						: "7426060",
            "mail" 							: "terranum@gmail.com",
			"archivo_pdf" 					: "RXN0YSBlcyBsYSBub3RhIGRlIGVzdGEgcOFnaW5h"
        }
    ]
}
********************************************************************************************************
@return json response respuesta_radicar_documento

Ejemplo del json response :
{
    "respuesta_radicar_documento":[{
            "radicado"  :  "2019JBBGERE00000252"
        }
    ]
}
********************************************************************************************************
Ejemplo del json response en caso de error :
{
    "respuesta_radicar_documento":[{
	"radicado"  :  "error",
            "error"  :  "El campo asunto es obligatorio."
        }
    ]
}
********************************************************************************************************
* @author Johnnatan Rodriguez Pinto
* @date Diciembre 2019
*/
/* Se incluye el archivo que contiene las variables para ejecutar consulta a la base de datos */
	require_once('../login/conexion2.php');
	require_once('../login/conexion3.php');

/* Se recibe el objeto mediante POST y se convierte a un array con json_decode */
	$cuerpo 	= file_get_contents('php://input');
	$parametros = json_decode($cuerpo,true);

	$timestamp  = date('Y-m-d H:i:s');  // Genera la fecha de transaccion 

/* Funciones para definir variables de la base de datos de auditoria */

/* Genera ID para auditoria */
	$query_max_log="select max(id) from auditoria_jonas";

	$fila_log 	= pg_query($conectado_log,$query_max_log);
	$linea_log 	= pg_fetch_array($fila_log);

	$max_log 	= $linea_log[0];
	$max_log2 	= $max_log+1;

/* Fin Genera ID para auditoria */

/* Funcion que genera la ip del cliente para guardar cada vez que se realiza una transaccion */
    function getRealIP(){ 
        if (isset($_SERVER["HTTP_CLIENT_IP"])){

            return $_SERVER["HTTP_CLIENT_IP"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){

            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_X_FORWARDED"])){

            return $_SERVER["HTTP_X_FORWARDED"];

        }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])){

            return $_SERVER["HTTP_FORWARDED_FOR"];

        }elseif (isset($_SERVER["HTTP_FORWARDED"])){

            return $_SERVER["HTTP_FORWARDED"];

        }else{
            return $_SERVER["REMOTE_ADDR"];
        }
    }
    $ip_equipo = getRealIP();
       
/* Fin funcion que genera la ip del cliente para guardar cada vez que se realiza una transaccion */
/* Funcion para verificar navegador */
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	function getBrowser($user_agent){
		if(strpos($user_agent, 'MSIE') !== FALSE)
		   return 'Internet explorer';
		 elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
		   return 'Microsoft Edge';
		 elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
		    return 'Internet explorer';
		 elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
		   return "Opera Mini";
		 elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
		   return "Opera";
		 elseif(strpos($user_agent, 'Firefox') !== FALSE)
		   return 'Mozilla Firefox';
		 elseif(strpos($user_agent, 'Chrome') !== FALSE)
		   return 'Google Chrome';
		 elseif(strpos($user_agent, 'Safari') !== FALSE)
		   return "Safari";
		 else
		   return 'No hemos podido detectar su navegador';
	}
	$navegador = getBrowser($user_agent);
/* Fin funcion para verificar navegador */
/* Fin funciones para definir variables de la base de datos de auditoria */

	switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET':  // Según la documentación REST el método GET es utilizado únicamente para consultar información al servidor, muy parecidos a realizar un SELECT a la base de datos.
			foreach ($parametros['consultar_estado_radicado'] as $img){	
				/* Extrae cada uno de los parámetros recibidos del array convertido y valida si en el JSON viene la variable, si viene vacía o si la varable es un espacio y devuelve error*/
				if(!isset($img['numero_radicado']) or $img['numero_radicado']=="" or $img['numero_radicado']==" "){
					echo "{'respuesta_estado_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El campo numero_radicado es obligatorio'
				        }
				    ]}";
					break;
				}else{
					$numero_radicado  = $img['numero_radicado'];				
				}

				/* Se consulta en la base de datos el estado del radicado */
				$query_radicado = "select estado_radicado from radicado where numero_radicado='$numero_radicado';";
				
				$fila_estado_radicado 	= pg_query($conectado,$query_radicado);
				$linea_estado_radicado 	= pg_fetch_array($fila_estado_radicado);
				$estado_radicado 		= $linea_estado_radicado['estado_radicado'];
			
				if($linea_estado_radicado=='0'){
					echo "{'respuesta_estado_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El numero de radicado no tiene estado o no existe en la base de datos '
				        }
				    ]}";
				}else{
					/* Se inserta en la tabla de auditoria_jonas que es la segunda base de datos para reportes */
					$query_log="insert into auditoria_jonas (id,usuario,fecha,transaccion,ip,tipo_transaccion,navegador) values ($max_log2,'WEBSERVICE_VUC','$timestamp','Por medio de WebService se consulta el estado del radicado $numero_radicado','$ip_equipo','consulta_estado_radicado_webservice_vuc','$navegador')";

					if(pg_query($conectado_log,$query_log)){
						/* Se arma el json response que se va a devolver al webservice cuando sea invocado */
						echo "{'respuesta_estado_documento':[{'estado' : '$estado_radicado'}]}";
					}else{
						/* Se arma el json response de respuesta de error */
						echo "{'respuesta_estado_documento':[{
							'radicado'  :  'error',
						    'error' 	:  'No se ha podido generar auditoria sobre la transaccion realizada. Por favor comuníquese con el administrador del GEA.'
					        }
					    ]}";
					}
				}	
			}	
			break;
   		case 'POST':  // Según la documentación REST el método POST es utilizado para solicitar la creación de un nuevo registro, es decir, algo que no existía previamente, es decir, es equivalente a realizar un INSERT en la base de datos. 

			foreach ($parametros['radicar_documento'] as $img){	
				/* Extrae cada uno de los parámetros recibidos del array convertido */
				/* Valida si en el JSON viene la variable, si viene vacía o si la varable es un espacio y devuelve error*/
				if(!isset($img['asunto']) or $img['asunto']=="" or $img['asunto']==" "){
					echo "{'respuesta_radicar_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El campo asunto es obligatorio'
				        }
				    ]}";
					break;
				}else{
					$asunto  = $img['asunto'];				
				}

				if(!isset($img['nombre_remitente_destinatario']) or $img['nombre_remitente_destinatario']=="" or $img['nombre_remitente_destinatario']==" "){
					echo "{'respuesta_radicar_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El campo nombre_remitente_destinatario es obligatorio'
				        }
				    ]}";
					break;
				}else{
					$nombre_remitente_destinatario 	= $img['nombre_remitente_destinatario'];				
				}

				if(!isset($img['dignatario'])){
					echo "{'respuesta_radicar_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'No se ha recibido el campo dignatario'
				        }
				    ]}";
					break;
				}else{
					$dignatario  = $img['dignatario'];				
				}

				if(!isset($img['ubicacion']) or $img['ubicacion']=="" or $img['ubicacion']==" "){
					echo "{'respuesta_radicar_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El campo ubicacion es obligatorio'
				        }
				    ]}";
					break;
				}else{
					$ubicacion  = $img['ubicacion'];				
				}

				if(!isset($img['direccion']) or $img['direccion']=="" or $img['direccion']==" "){
					echo "{'respuesta_radicar_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El campo direccion es obligatorio'
				        }
				    ]}";
					break;
				}else{
					$direccion  = $img['direccion'];				
				}

				if(!isset($img['telefono'])){
					echo "{'respuesta_radicar_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'No se ha recibido el campo telefono'
				        }
				    ]}";
					break;
				}else{
					$telefono  = $img['telefono'];				
				}

				if(!isset($img['mail']) or $img['mail']=="" or $img['mail']==" "){
					echo "{'respuesta_radicar_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El campo mail es obligatorio'
				        }
				    ]}";
					break;
				}else{
					$mail  = $img['mail'];				
				}

				if(!isset($img['archivo_pdf']) or $img['archivo_pdf']=="" or $img['archivo_pdf']==" "){
					echo "{'respuesta_radicar_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El campo archivo_pdf es obligatorio'
				        }
				    ]}";
					break;
				}else{
					$archivo_pdf  = $img['archivo_pdf'];				
				}
				/* Hasta aqui se valida si en el JSON viene la variable, si viene vacía o si la varable es un espacio y devuelve error*/

				/* Se convierte el archivo que se recibe en base64 a un PDF para asociarlo al radicado y guardarlo */
				$pdf_decoded = base64_decode($archivo_pdf);

				/* Se definen variables para generar radicado */
				$codigo_dependencia_destino = 'ADMI';
				$login_usuario_actual  		= 'ADMINISTRADOR';
				$nivel_seguridad	  		= '1';
				$tipo_radicado		  		= '1';
				$year 						= date("Y"); 			// Se obtiene el año en formato 4 digitos 
				$codigo_entidad_ws			= "JBB"; 

				require_once('../login/validar_consecutivo.php'); 	// Valida si el consecutivo existe y genera el número de radicado

				/* Se definen variables para insertar en la base de datos */
				$codigo_carpeta1 		= "{\"$login_usuario_actual\":{\"codigo_carpeta_personal\":\"entrada\"}}";	
				$numero_guia_radicado 	= "";	
				$descripcion_anexos 	= "";	
				$codigo_contacto 		= "1";	
				$leido 					= $login_usuario_actual.",";
				$clasificacion_radicado = "Oficio";
				$dias_tramite  			= "15";
				$path_radicado 			= "ws_".$radicado.".pdf";

				/* Con las variables definidas se arman las query de inserción a la base de datos a las tablas radicado, datos_origen_radicado, historico_eventos y se concatenan para ejecutarlas en una sola sentencia. */
				$query_radicado1 = "insert into radicado(numero_radicado, fecha_radicado, codigo_carpeta1, numero_guia_oficio, descripcion_anexos, codigo_contacto, dependencia_actual, usuarios_visor, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, clasificacion_radicado, termino, estado_radicado, path_radicado, usuarios_control) values ('$radicado', '$timestamp', '$codigo_carpeta1','$numero_guia_radicado', '$descripcion_anexos', '$codigo_contacto','$codigo_dependencia_destino', '$leido','$codigo_dependencia_destino', '$login_usuario_actual','$asunto','$nivel', '$leido', '$clasificacion_radicado', '$dias_tramite','en_tramite', '$path_radicado', '$leido');";
				$query_datos_origen_radicado = "insert into datos_origen_radicado(numero_radicado, nombre_remitente_destinatario, dignatario, ubicacion, direccion, telefono, mail) values ('$radicado', '$nombre_remitente_destinatario', '$dignatario', '$ubicacion', '$direccion', '$telefono', '$mail');";
				$query_historico="insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$radicado', 'WebService VUC 1', 'Radicado mediante WebService', 'Mediante servicio web se genera documento y se guarda PDF recibido', '$timestamp')";
				$query_radicado = $query_radicado1.$query_datos_origen_radicado.$query_historico;

				if(pg_query($conectado,$query_radicado)){	// Si se crea el radicado
					/* Se crea el documento PDF con el contenido recibido en base64 */
					$pdf = fopen ("../bodega_pdf/radicados/$path_radicado",'w');
					fwrite ($pdf,$pdf_decoded);
					fclose ($pdf);

					/* Se inserta en la tabla de auditoria_jonas que es la segunda base de datos para reportes */
					$query_log="insert into auditoria_jonas (id,usuario,fecha,transaccion,ip,tipo_transaccion,navegador) values ($max_log2,'WEBSERVICE_VUC','$timestamp','Por medio de WebService se ha generado el radicado de entrada $radicado','$ip_equipo','radicado_webservice_vuc','$navegador')";

					if(pg_query($conectado_log,$query_log)){
						/* Se arma el json response que se va a devolver al webservice cuando sea invocado */
						echo "{'respuesta_radicar_documento':[{'radicado' : '$radicado'}]}";
					}else{
						echo "{'respuesta_radicar_documento':[{
							'radicado'  :  'error',
						    'error' 	:  'No se ha podido generar auditoria sobre la transaccion realizada. Por favor comuníquese con el administrador del GEA.'
					        }
					    ]}";
					}
						
				}else{
					echo "{'respuesta_radicar_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'No se pudo radicar el documento. Comuníquese con el administrador del GEA.'
				        }
				    ]}";
				}	
			}
   			break;

   		case 'PUT': // Según la documentación REST el método PUT se utiliza para actualizar por completo un registro existente, es decir, es parecido a realizar un UPDATE a la base de datos. 

   			foreach ($parametros['actualizar_estado_radicado'] as $img){	
				/* Extrae cada uno de los parámetros recibidos del array convertido y valida si en el JSON viene la variable, si viene vacía o si la varable es un espacio y devuelve error*/
				if(!isset($img['numero_radicado']) or $img['numero_radicado']=="" or $img['numero_radicado']==" "){
					echo "{'respuesta_estado_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El campo numero_radicado es obligatorio'
				        }
				    ]}";
					break;
				}else{
					$numero_radicado  = $img['numero_radicado'];				
				}

				if(!isset($img['nuevo_estado']) or $img['nuevo_estado']=="" or $img['nuevo_estado']==" "){
					echo "{'respuesta_estado_documento':[{
						'radicado'  :  'error',
					    'error' 	:  'El campo nuevo_estado es obligatorio'
				        }
				    ]}";
					break;
				}else{
					$nuevo_estado  = $img['nuevo_estado'];				
				}

				/* Con las variables definidas se arman las query de update a la base de datos en la tabla radicado, se inserta historico_eventos y se concatenan para ejecutarlas en una sola sentencia. */
				$query_update_radicado = "update radicado set estado_radicado='$nuevo_estado' where numero_radicado='$numero_radicado';";
				$query_historico = "insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$numero_radicado', 'WebService VUC 2', 'Actualiza estado mediante WebService', 'Mediante servicio web se actualiza estado a <b>$nuevo_estado</b>', '$timestamp');";
				$query_radicado = $query_update_radicado.$query_historico;

				if(pg_query($conectado,$query_radicado)){	// Si se modifica el radicado
					/* Se inserta en la tabla de auditoria_jonas que es la segunda base de datos para reportes */
					$query_log="insert into auditoria_jonas (id,usuario,fecha,transaccion,ip,tipo_transaccion,navegador) values ($max_log2,'WEBSERVICE_VUC','$timestamp','Por medio de WebService se ha modificado el estado del radicado $numero_radicado al estado ($nuevo_estado)','$ip_equipo','radicado_webservice_vuc','$navegador')";

					if(pg_query($conectado_log,$query_log)){
						/* Se arma el json response que se va a devolver al webservice cuando sea invocado */
						echo "{'respuesta_estado_documento':[{'confirmacion' : 'correctamente_actualizada'}]}";
					}else{
						echo "{'respuesta_estado_documento':[{
							'radicado'  :  'error',
						    'error' 	:  'No se ha podido generar auditoria sobre la transaccion realizada. Por favor comuníquese con el administrador del GEA.'
					        }
					    ]}";
					}

				}else{
					echo "{'respuesta_estado_documento':[{
						'confirmacion'  :  'error',
					    'error' 		:  'No se pudo actualizar el documento. Comuníquese con el administrador del GEA.'
				        }
				    ]}";
				}		
			}	

   			break;
   	
   	default:
   		# code...
   		break;
   }
?>