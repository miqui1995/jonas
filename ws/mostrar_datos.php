<?php
/* Se incluye el archivo que contiene las variables para ejecutar consulta a la base de datos */
session_start();
require_once('../login/conexion2.php');
require_once('../login/conexion3.php');
//se recibe variables
$json = $_POST["json"];
//se decodifica la variable json para su uso
$json_decodificado  	= json_decode($json);
// se asignan valores de la variable json
$codigo_entidad 		= $json_decodificado->{'codigo_entidad'};
$numero_radicado 		= $json_decodificado->{'numero_radicado'};
$canal_respuesta 		= $json_decodificado->{'canal_respuesta'};

// se consulta en base de datos el radicado
$consulta_sql = "select * from radicado r join datos_origen_radicado d on r.numero_radicado=d.numero_radicado where r.numero_radicado='$numero_radicado'";
// se hace la funcion a base de datos
$resultado_consulta = pg_query($conectado, $consulta_sql);
// se pasa el resultado a la variable ordenar_resultado
$ordenar_resultado = pg_fetch_array($resultado_consulta);
//se da valores con los datos traidos del select realizado

if(pg_num_rows($resultado_consulta)==0){
	echo "sin_resultados";
}else{
	$codigo_entidad  	 	= $codigo_entidad;
	$numero_radicado 	 	= $numero_radicado;
	$canal_respuesta 	 	= $canal_respuesta;
	$remitente       	 	= $ordenar_resultado['nombre_remitente_destinatario'];
	$dignatario     	 	= $ordenar_resultado['dignatario'];
	$fecha_radicado 	 	= $ordenar_resultado['fecha_radicado'];
	$direccion_respuesta 	= $ordenar_resultado['mail'];
	$asunto              	= $ordenar_resultado['asunto'];
	$estado              	= $ordenar_resultado['estado_radicado'];
	$usuarios_control    	= $ordenar_resultado['usuarios_control'];
	$dependencia_actual  	= $ordenar_resultado['dependencia_actual'];
	$medio_resp_solicitado  = $ordenar_resultado['medio_respuesta_solicitado'];

	switch($estado){
		case "no_requiere_respuesta":
			$query_historico 		= "select h.comentario, h.fecha, u.login, d.nombre_dependencia from historico_eventos h join usuarios u on h.usuario=u.login join dependencias d on u.codigo_dependencia=d.codigo_dependencia where numero_radicado='$numero_radicado' and transaccion='Se marca documento como NRR (No requiere respuesta)'";
			$resultado_historico 	= pg_query($conectado, $query_historico);
			$ordenar_resultado_h 	= pg_fetch_array($resultado_historico);

			$comentario 		= $ordenar_resultado_h['comentario'];
			$fecha 				= $ordenar_resultado_h['fecha'];
			$login 				= $ordenar_resultado_h['login'];
			$nombre_dependencia = $ordenar_resultado_h['nombre_dependencia'];
		break;

		case "en_tramite":
			$query_historico 		= "select * from dependencias where codigo_dependencia='$dependencia_actual'";
			$resultado_historico 	= pg_query($conectado, $query_historico);
			$ordenar_resultado_h 	= pg_fetch_array($resultado_historico);

			$nombre_dependencia = $ordenar_resultado_h['nombre_dependencia'];

			$comentario 		= "";
			$fecha 				= "";
			$login 				= $usuarios_control;
		break;

		case "tramitado":
			$query_historico 		= "select rad.numero_radicado, rad.asunto, rad.medio_respuesta_solicitado, v.usuario_que_elabora, v.cargo_usuario_que_elabora, v.usuario_que_firma, v.cargo_usuario_que_firma, v.fecha_modifica, d.nombre_dependencia from respuesta_radicados r join radicado rad on r.radicado_respuesta=rad.numero_radicado join version_documentos v on rad.numero_radicado=v.numero_radicado join dependencias d on rad.dependencia_actual=d.codigo_dependencia where r.radicado_padre='$numero_radicado' and v.html_asunto!=''";
			$resultado_historico 	= pg_query($conectado, $query_historico);
			$ordenar_resultado_h 	= pg_fetch_array($resultado_historico);

			$nombre_dependencia 	= $ordenar_resultado_h['nombre_dependencia'];
			$fecha 					= $ordenar_resultado_h['fecha_modifica'];
			$login 					= $ordenar_resultado_h['usuario_que_firma'];
			$numero_radicado_r		= $ordenar_resultado_h['numero_radicado']; // Respuesta
			$asunto					= $ordenar_resultado_h['asunto'];
			$usuario_que_elabora	= $ordenar_resultado_h['usuario_que_elabora']; 
			$cargo_usuario_elabora	= $ordenar_resultado_h['cargo_usuario_que_elabora']; 
			$cargo_usuario_firma	= $ordenar_resultado_h['cargo_usuario_que_firma']; 

			$enviado_mail = ($medio_resp_solicitado=="correo_electronico")?"<br>Respuesta enviada el $fecha al email <b><i>$direccion_respuesta</i></b>":"";

			$comentario 			= "Radicado respondido el $fecha con el radicado <b>$numero_radicado_r</b><br> por $login ($cargo_usuario_firma de $nombre_dependencia)<br>Con el asunto <b><i>$asunto</i></b>$enviado_mail<br><span style='font-size:12px'>Respuesta elaborada por $usuario_que_elabora ($cargo_usuario_elabora de $nombre_dependencia)</span>";
		break;
	}	

	// se genera el json para mostrar como respuesta
	$json_resultante = array("codigo_entidad"=>"$codigo_entidad","numero_radicado"=>"$numero_radicado","remitente"=>"$remitente","dignatario"=>"$dignatario","fecha_radicado"=>"$fecha_radicado","canal_respuesta"=>"$canal_respuesta","direccion_respuesta"=>"$direccion_respuesta","asunto"=>"$asunto","estado"=>"$estado","comentario"=>"$comentario","fecha"=>"$fecha","login"=>"$login","nombre_dependencia"=>"$nombre_dependencia");
	$json_final = json_encode($json_resultante);
	
	echo ($json_final);
}

?>