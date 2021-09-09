<?php
  // require_once("../login/validar_inactividad.php");
?>
<script type="text/javascript">

</script>
<style type="text/css">
    .encabezado{
        background-color    : #3184cc;
        color               : #FFFFFF;
        font-size           : 20px;
        font-weight         : bold;
        padding             : 10px;
    }
    h1{
        background-color    : #9d1414;
        color               : #FFFFFF;
        margin-bottom       : -2px;
    }
    table tr td{
        padding             : 5px;
    }
    .titulos{
        background-color    : #afb3b7;
        font-size           : 16px;
        font-weight         : bold;
        text-align          : center;
    }
</style>
<center>
<?php 
    require_once("../login/conexion2.php");
    $fecha_inicial = '2020-10-06';
    $fecha_final   = '2021-03-01';

    echo "<h1>Reporte de Radicados por usuario Sin Terminar entre las fechas $fecha_inicial - $fecha_final</h1>";
    
    $query_usuarios = "select * from usuarios u join dependencias d on u.codigo_dependencia=d.codigo_dependencia where u.estado='ACTIVO' order by u.login";

    $fila_usuarios    = pg_query($query_usuarios);
    $numero_usuarios  = pg_num_rows($fila_usuarios);

    /* Creación de los option por cada registro almacenado en $registros_dependencia_reporte2 */
    for ($i=0; $i <$numero_usuarios; $i++){
		$linea_usuarios = pg_fetch_array($fila_usuarios);//Se pasa a un array la información de la base de datos
		//Se da valores independientes a la información del array $linea_dependencia_reporte2
		$login    			= trim($linea_usuarios['login']);
		$nom_depe   		= $linea_usuarios['nombre_dependencia'];
		$nombre_usuario  	= $linea_usuarios['nombre_completo'];
		/* Fin Se da valores independientes a la información del array $linea_dependencia_reporte2 */

        if($login=="ADMINISTRADOR" or $login =="ADMON"){
        	echo "<br>--->".$login."<--";
        }else{	
            $a = 0;
            /* Query para radicados sin TRD */
            $query_pendientes = "select * from radicado r left join respuesta_radicados res on r.numero_radicado=res.radicado_padre where r.numero_radicado like '%1' and (res.radicado_respuesta is null or res.radicado_respuesta='') and estado_radicado ='en_tramite' and (codigo_subserie is null or codigo_subserie='') and r.codigo_carpeta1->'$login'->>'codigo_carpeta_personal'='entrada' and r.fecha_radicado between '$fecha_inicial' and '$fecha_final' order by fecha_radicado";
    
            $fila_pendientes    	= pg_query($query_pendientes);
            $numero_pendientes    	= pg_num_rows($fila_pendientes);
            
            if($numero_pendientes >0){ 
                echo "<hr><hr><h1>Usuario $login </h1>";


                $a++;
                echo "<table width = '100%'border='0'><tr><td class='encabezado' colspan='7'><div> Radicados sin TRD ($numero_pendientes) </div></td></tr>
                <tr><td class='titulos'>ID</td><td class='titulos'>Usuario</td><td class='titulos'>Numero de radicado</td><td class='titulos'>Asunto</td><td class='titulos'>TRD</td><td class='titulos'>Fecha de Radicación</td><td class='titulos'>Dias que han pasado desde que se recibe documento</td></tr>";

                for ($j=0; $j < $numero_pendientes; $j++) { 
                    $linea_pendientes = pg_fetch_array($fila_pendientes);//Se pasa a un array la información de la base de datos

                    $fecha1 		= $linea_pendientes['fecha_radicado'];
                    $fecha2 		= date("Y/m/d");

					$dias = (strtotime($fecha1)-strtotime($fecha2))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
					$dias = abs($dias); 
					$dias = floor($dias);

                    $numero_radicado 	= $linea_pendientes['numero_radicado'];
                    $asunto           	= $linea_pendientes['asunto'];

                    $k = $j+1;
                    echo "<tr><td>$k</td><td>$nombre_usuario</td><td>$numero_radicado</td><td>$asunto</td><td>SIN TRD</td><td>$fecha1</td><td>Han pasado <font color='red' style='font-weight:bold;'> $dias</font> dias desde que se ha recibido documento</td></tr>";
                }
                echo "</table>";
            }
            
            /* Query para radicados sin Expediente */
            $query_pendientes1 = "select * from radicado r left join respuesta_radicados res on r.numero_radicado=res.radicado_padre where r.numero_radicado like '%1' and (res.radicado_respuesta is null or res.radicado_respuesta='') and estado_radicado ='en_tramite' and (id_expediente is null or id_expediente='') and r.codigo_carpeta1->'$login'->>'codigo_carpeta_personal'='entrada' and r.fecha_radicado between '$fecha_inicial' and '$fecha_final' order by fecha_radicado";

            $fila_pendientes1      = pg_query($query_pendientes1);
            $numero_pendientes1    = pg_num_rows($fila_pendientes1);
            
            if($numero_pendientes1 >0){
                if($a==0){
                    echo "<hr><hr><h1>Usuario $login </h1>";
                }
                $a++;
                echo "<table width = '100%'border='0'><tr><td class='encabezado' colspan='7'><div>Radicados sin EXPEDIENTES ($numero_pendientes1) </div></td></tr>
                <tr><td class='titulos'>ID</td><td class='titulos'>Usuario</td><td class='titulos'>Numero de radicado</td><td class='titulos'>Asunto</td><td class='titulos'>Id Expediente</td><td class='titulos'>Fecha de Radicación</td><td class='titulos'>Dias que han pasado desde que se recibe documento</td></tr>";

                for ($j=0; $j < $numero_pendientes1; $j++) { 
                    $linea_pendientes = pg_fetch_array($fila_pendientes1);//Se pasa a un array la información de la base de datos

                    $fecha1 	= $linea_pendientes['fecha_radicado'];
                    $fecha2 	= date("Y/m/d");

					$dias = (strtotime($fecha1)-strtotime($fecha2))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
					$dias = abs($dias); 
					$dias = floor($dias);

                    $numero_radicado = $linea_pendientes['numero_radicado'];
                    $asunto           = $linea_pendientes['asunto'];

                    $k = $j+1;
                    echo "<tr><td>$k</td><td>$nombre_usuario</td><td>$numero_radicado</td><td>$asunto</td><td>SIN EXPEDIENTE</td><td>$fecha1</td><td>Han pasado <font color='red' style='font-weight:bold;'> $dias</font> dias desde que se ha recibido documento</td></tr>";
                }
                echo "</table>";
            }
            
            /* Query para radicados en bandeja de entrada */
            $query_pendientes2 = "select * from radicado r left join respuesta_radicados res on r.numero_radicado=res.radicado_padre where r.numero_radicado like '%1' and (res.radicado_respuesta is null or res.radicado_respuesta='') and estado_radicado ='en_tramite' and r.codigo_carpeta1->'$login'->>'codigo_carpeta_personal'='entrada' and r.fecha_radicado between '$fecha_inicial' and '$fecha_final' order by fecha_radicado";
    
            $fila_pendientes2    = pg_query($query_pendientes2);
            $numero_pendientes2  = pg_num_rows($fila_pendientes2);
            
            if($numero_pendientes2 >0){
                if($a==0){
                    echo "<hr><hr><h1>Usuario $login </h1>";
                }
                $a++;
                echo "<table width = '100%'border='0'><tr><td class='encabezado' colspan='7'><div>Radicados en BANDEJA DE ENTRADA ($numero_pendientes2)</div></td></tr>
                <tr><td class='titulos'>ID</td><td class='titulos'>Usuario</td><td class='titulos'>Numero de radicado</td><td class='titulos'>Asunto</td><td class='titulos'>BANDEJA ACTUAL</td><td class='titulos'>Fecha de Radicación</td><td class='titulos'>Dias que han pasado desde que se recibe documento</td></tr>";

                for ($j=0; $j < $numero_pendientes2; $j++) { 
                    $linea_pendientes = pg_fetch_array($fila_pendientes2);//Se pasa a un array la información de la base de datos

                    $fecha1 	= $linea_pendientes['fecha_radicado'];
                    $fecha2 	= date("Y/m/d");

					$dias = (strtotime($fecha1)-strtotime($fecha2))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
					$dias = abs($dias); 
					$dias = floor($dias);

                    $numero_radicado = $linea_pendientes['numero_radicado'];
                    $asunto          = $linea_pendientes['asunto'];

                    $k = $j+1;
                    echo "<tr><td>$k</td><td>$nombre_usuario</td><td>$numero_radicado</td><td>$asunto</td><td>BANDEJA DE ENTRADA</td><td>$fecha1</td><td>Han pasado <font color='red' style='font-weight:bold;'> $dias</font> dias desde que se ha recibido documento</td></tr>";
                }
                echo "</table>";
            }
            
             /* Query para radicados en bandeja de salida */
            $query_pendientes3 = "select * from radicado r left join respuesta_radicados res on r.numero_radicado=res.radicado_padre where r.numero_radicado like '%1' and (res.radicado_respuesta is null or res.radicado_respuesta='') and estado_radicado ='en_tramite' and r.codigo_carpeta1->'$login'->>'codigo_carpeta_personal'='Salida' and r.fecha_radicado between '$fecha_inicial' and '$fecha_final' order by fecha_radicado";
    
            $fila_pendientes3    = pg_query($query_pendientes3);
            $numero_pendientes3  = pg_num_rows($fila_pendientes3);


            if($linea_usuarios['perfil']!="DISTRIBUIDOR_DEPENDENCIA"){                	
                if($numero_pendientes3 >0){
                    if($a==0){
                        echo "<hr><hr><h1>Usuario $login </h1>";
                    }
                    $a++;

                    echo "<table width = '100%'border='0'><tr><td class='encabezado' colspan='7'><div>Radicados en BANDEJA DE SALIDA ($numero_pendientes3)</div></td></tr>
                    <tr><td class='titulos'>ID</td><td class='titulos'>Usuario</td><td class='titulos'>Numero de radicado</td><td class='titulos'>Asunto</td><td class='titulos'>BANDEJA ACTUAL</td><td class='titulos'>Fecha de Radicación</td><td class='titulos'>Dias que han pasado desde que se recibe documento</td></tr>";

                    for ($j=0; $j < $numero_pendientes3; $j++) { 
                        $linea_pendientes = pg_fetch_array($fila_pendientes3);//Se pasa a un array la información de la base de datos

			            $fecha1 	= $linea_pendientes['fecha_radicado'];
			            $fecha2 	= date("Y/m/d");

						$dias = (strtotime($fecha1)-strtotime($fecha2))/86400; // Calcula el numero de dias desde que se recibe a la fecha.
						$dias = abs($dias); 
						$dias = floor($dias);

                        $numero_radicado 	= $linea_pendientes['numero_radicado'];
                        $asunto           	= $linea_pendientes['asunto'];

                        $k = $j+1;
                        echo "<tr><td>$k</td><td>$nombre_usuario</td><td>$numero_radicado</td><td>$asunto</td><td>BANDEJA DE SALIDA</td><td>$fecha1</td><td>Han pasado <font color='red' style='font-weight:bold;'> $dias</font> dias desde que se ha recibido documento</td></tr>";
                    }
                    echo "</table>";
                }
            }
        }
    
    }
    /* Fin Creación de los option por cada registro almacenado en $registros_dependencia_reporte2 */
  