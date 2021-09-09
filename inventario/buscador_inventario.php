<?php
require_once('../login/validar_inactividad.php');// Se valida la inactividad 

// require_once('../login/conexion2.php');
// var_dump($_SESSION);
$numero_consecutivo             = $_POST['numero_consecutivo'];
$codigo_dependencia             = $_POST['codigo_dependencia'];
$codigo_serie                   = $_POST['codigo_serie'];
$codigo_subserie                = $_POST['codigo_subserie'];
$fecha_inicial                  = $_POST['fecha_inicial'];
$fecha_final                    = $_POST['fecha_final'];
$consecutivo_jonas              = strtoupper($_POST['consecutivo_jonas']);
$caja_paquete_tomo_otro         = $_POST['caja_paquete_tomo_otro'];
$numero_caja_paquete            = $_POST['numero_caja_paquete'];
$numero_carpeta                 = $_POST['numero_carpeta'];
$numero_caja_archivo_central    = $_POST['numero_caja_archivo_central'];
$nombre_documento               = strtoupper($_POST['nombre_documento']);
$metadato                       = $_POST['metadato'];

$cuerpo_tabla                   = "";
$query = "SELECT * FROM inventario i join radicado r on i.radicado_jonas=r.numero_radicado where ";

if(!empty($numero_consecutivo)){
    $query = "$query i.numero_consecutivo ilike '%$numero_consecutivo%' and";
}

if(!empty($codigo_dependencia)){
    $query = "$query i.codigo_dependencia ilike '%$codigo_dependencia%' and";
}

if(!empty($codigo_serie)){
    $query = "$query codigo_serie ilike '%$codigo_serie%' and";
}

if(!empty($codigo_subserie)){
    $query = "$query codigo_subserie ilike '%$codigo_subserie%' and";
}
if(!empty($fecha_inicial)){
    $fecha_inicial=date('Y-m-d', strtotime($fecha_inicial));
    $query = "$query to_date(i.fecha_inicial, 'DD MM YYYY')>='$fecha_inicial' and";
}
if(!empty($fecha_final)){
    $fecha_final=date('Y-m-d', strtotime($fecha_final));
    $query = "$query to_date(i.fecha_final, 'DD MM YYYY')<='$fecha_final' and";
}
if(!empty($consecutivo_jonas)){
    $query = "$query i.radicado_jonas ilike '%$consecutivo_jonas%' and";
}  
if(!empty($caja_paquete_tomo_otro)){
    $query = "$query i.caja_paquete_tomo ilike '%$caja_paquete_tomo_otro%' and";
}  
if(!empty($numero_caja_paquete)){
    $query = "$query i.numero_caja_paquete ilike '%$numero_caja_paquete%' and";
}    
if(!empty($numero_carpeta)){
    $query = "$query i.numero_carpeta ilike '%$numero_carpeta%' and";
} 
if(!empty($numero_caja_archivo_central)){
    $query = "$query i.numero_caja_archivo_central ilike '%$numero_caja_archivo_central%' and";
}  
if(!empty($nombre_documento)){
    $query = "$query i.nombre_documento ilike '%$nombre_documento%' and";
} 
if(!empty($metadato)){
    $query = "$query i.descriptor ilike '%$metadato%' and";
} 
/* Muestra la consulta quitando el ùltimo "and" para generar la query correctamente */
$query2 = substr($query,0,-3);   

/* Ejecuta la query */
$fila_consulta = pg_query($conectado,$query2);

/*Calcula el numero de registros que genera la consulta anterior.*/
$registros_consulta = pg_num_rows($fila_consulta);
$encabezado_tabla = "<tr>
    <td colspan='9'>
        <center>
            <input type='button' name='buscar' value='Volver a buscar' class='botones' onclick='volver_busqueda()'><br>
            <a href='#' id='ver_detalle' onclick='ver_detalle_busqueda()'>Ver detalle de la consulta</a>
            <a href='#' id='ocultar_detalle' class='detalle_busqueda' onclick='ocultar_detalle_busqueda()'>Ocultar detalle de la consulta</a>
        </center>
    </td>
</tr>";

// Inicio funcion para verificar padre y mostrar ubicacion fisica.
function verificar_padre($array,$nombre_nivel,$x){
    include '../login/conexion2.php';

    $query_ubic1="select * from ubicacion_topografica where nombre_nivel='$nombre_nivel'";
    
    $fila_ubicacion_fisica  = pg_query($conectado,$query_ubic1);
    $linea_ubicacion_fisica = pg_fetch_array($fila_ubicacion_fisica);

    $nivel_padre = $linea_ubicacion_fisica["nivel_padre"];
    //return $nombre_nivel;
    if($x==''){
        $x=0;    
    }
    if($nivel_padre!=""){
        $array[$x]=$nombre_nivel;
        $x++;
        return verificar_padre($array,"$nivel_padre",$x);
    }else{
        $array[$x]=$nombre_nivel;
        return $array;
    
    }
}  

function validar_ubicacion($expediente){
    include '../login/conexion2.php';

    /* Consulta en la tabla "expedientes" join ubicacion_topografica el numero de expediente */
    $query_ubicacion_topografica="select * from expedientes e join ubicacion_topografica u on e.codigo_ubicacion_topografica=u.id_ubicacion where e.id_expediente='$expediente'";

    $fila_ubicacion_topografica         = pg_query($conectado,$query_ubicacion_topografica);
    $registros_ubicacion_topografica    = pg_num_rows($fila_ubicacion_topografica);

    /* Si la consulta encuentra resultados */
    if($registros_ubicacion_topografica>0){
        $linea_ubicacion_topografica    = pg_fetch_array($fila_ubicacion_topografica);

        $codigo_ubic    = $linea_ubicacion_topografica['codigo_ubicacion_topografica'];
        $nombre_nivel   = $linea_ubicacion_topografica['nombre_nivel'];
        $nivel_padre    = $linea_ubicacion_topografica['nivel_padre'];

        $array_padre = array();
        $ubicacion_fisica = verificar_padre($array_padre,"$nombre_nivel","");
    }else{
    /* Si la consulta NO encuentra resultados devuelve el array con el primer valor en false */
        $array                  = array();
        $ubicacion_fisica[0]    = false;
    }
    
    return($ubicacion_fisica);
}
// Fin funcion para verificar padre y mostrar ubicacion fisica.

if($_SESSION['codigo_entidad']=='EJC'){
    $indicador_jonas = "";
}else{
    $indicador_jonas = "<br>JONAS";
}
/*Recorre el array generado e imprime uno a uno los resultados.*/   
if($registros_consulta>0){
    $encabezado_tabla="$encabezado_tabla 
        <tr class='encabezado encabezado1'>
            <td></td>
            <td colspan='3'>CONTADOR UNIDAD DOCUMENTAL</td>
            <td colspan='3' class='detalle_busqueda'>CÓDIGO TABLA DE RETENCIÓN DOCUMENTAL</td>
            <td class='celda'>ASUNTO O DESCRIPCIÓN</td>
            <td colspan='2' class='detalle_busqueda'>FECHAS EXTREMAS</td>
            <td colspan='3'>UNIDAD DE CONSERVACION</td>
            <td colspan='2' class='celda detalle_busqueda'>CONSECUTIVO</td>
            <td class='celda'>METADATO</td>
            <td>FOLIOS</td>
            <td>CONSECUTIVO CAJA</td>
            <td>NOTAS</td>
        </tr>
        <tr class='encabezado encabezado1'>
            <td style='padding: 5px 200px;'>UBICACIÓN FÍSICA</td>
            <td>CONSECUTIVO EXPEDIENTE (CARPETA)</td>
            <td>CONSECUTIVO RADICADO $indicador_jonas</td>
            <td class='celda'>NUMERO<br>CONSECUTIVO</td>
            <td class='celda detalle_busqueda'>CODIGO<br>DEPENDENCIA</td>
            <td class='celda detalle_busqueda'>CODIGO<br>SERIE</td>
            <td class='celda detalle_busqueda'>CODIGO<br>SUBSERIE</td>
            <td class='celda'>NOMBRE_DOCUMENTO</td>
            <td class='celda detalle_busqueda'>FECHA_INICIAL</td>
            <td class='celda detalle_busqueda'>FECHA_FINAL</td>
            <td class='celda'>CAJA_PAQUETE<br>TOMO_OTRO</td>
            <td class='celda'>NUMERO<br>CAJA_PAQUETE</td>
            <td class='celda'>NUMERO<br>CARPETA</td>
            <td class='celda detalle_busqueda'>DESDE</td>
            <td class='celda detalle_busqueda'>HASTA</td>
            <td class='celda'>DESCRIPTOR</td>
            <td class='celda'>TOTAL_FOLIOS</td>
            <td class='celda'>NUMERO_CAJA<br>ARCHIVO_CENTRAL</td>
            <td class='celda'>OBSERVACIONES</td>
        </tr>";

    $num_fila=0;
    for($i=0;$i<$registros_consulta;$i++){
        if ($i%2==0){$fila="fila2"; //  si el resto de la división es 0 pongo un color (estilo de la tabla)
            }else{ $fila="fila1";} //   si el resto de la división NO es 0 pongo otro color (estilo de la tabla)

        $linea = pg_fetch_array($fila_consulta); 

        $expediente=$linea['id_expediente'];

        $exp  = explode(",", $expediente);

        $max  = sizeof($exp);
        $max2 = $max-1;

        $nombre_expediente="";
                
        if($max2==0){
            $num_exp = $exp[0];
            
            $consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
            $fila_exp     = pg_query($conectado,$consulta_exp);
            $linea_exp    = pg_fetch_array($fila_exp);
            $nombre_exp   = $linea_exp['id_expediente'];

        /* Se define la ubicación topográfica */
            $ubicacion_topografica2=validar_ubicacion($num_exp);
            // echo "mm $ubicacion_topografica2";
        
            if($ubicacion_topografica2[0]==false){
                $ubicacion_topografica3="<font color=red>Expediente no se ha ubicado topográficamente todavía</font>";
            }else{
                $ubicacion_topografica3=""; 
                foreach($ubicacion_topografica2 as $item){
                    if($ubicacion_topografica3==""){
                        $ubicacion_topografica3=$item;
                    }else{
                        $ubicacion_topografica3=$ubicacion_topografica3." <- ".$item;
                    }
                }
            }
            $ubicacion_topografica1=$ubicacion_topografica3;     
        }else{
            for ($j=0; $j < $max2; $j++) { 
                $num_exp = $exp[$j];

                $consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
                $fila_exp     = pg_query($conectado,$consulta_exp);
                $linea_exp    = pg_fetch_array($fila_exp);
                $nombre_exp   = $linea_exp['id_expediente'];

            /* Se define la ubicación topográfica */
                $ubicacion_topografica2=validar_ubicacion($num_exp);
                
                if($ubicacion_topografica2[0]==false){
                    $ubicacion_topografica3="<font color=red>Expediente no se ha ubicado topográficamente todavía</font>";
                }else{
                    $ubicacion_topografica3=""; 
                    foreach($ubicacion_topografica2 as $item){
                        if($ubicacion_topografica3==""){
                            $ubicacion_topografica3=$item;
                        }else{
                            $ubicacion_topografica3=$ubicacion_topografica3." <- ".$item;
                        }
                    }
                }
                $ubicacion_topografica1=$ubicacion_topografica3;
            /* Fin de definir la ubicación topográfica */
            }
        }
        
        $radicado_jonas1 = trim(str_ireplace($consecutivo_jonas, "<b><font color='red'>$consecutivo_jonas</font></b>", $linea['radicado_jonas']));
        $numero_consecutivo1 = trim(str_ireplace($numero_consecutivo, "<b><font color='red'>$numero_consecutivo</font></b>", $linea['numero_consecutivo']));
        $codigo_dependencia1 = trim(str_ireplace($codigo_dependencia, "<b><font color='red'>$codigo_dependencia</font></b>", $linea['codigo_dependencia']));

        $codigo_serie           = $linea['codigo_serie'];          // Trae resultado 001
        $codigo_subserie        = $linea['codigo_subserie'];

        $codigo_serie1          = "".(int) $codigo_serie."";    // Transforma del resultado 001 a 1
        $codigo_serie2          = "".(int) $codigo_serie."";
        $codigo_subserie1       = "".(int) $codigo_subserie."";
        
        $codigo_serie1              = trim(str_ireplace($codigo_serie1, "<b><font color='red'>$codigo_serie1</font></b>", $codigo_serie1));
        $codigo_subserie1           = trim(str_ireplace($codigo_subserie1, "<b><font color='red'>$codigo_subserie1</font></b>", $codigo_subserie1));
        $codigo_subserie1           = "$codigo_serie2,$codigo_subserie1";

        $nombre_documento1          = trim(str_ireplace($nombre_documento, "<b><font color='red'>$nombre_documento</font></b>", $linea['nombre_documento']));
        $fecha_inicial1             = $linea['fecha_inicial'];
        $fecha_final1               = $linea['fecha_final'];
        $caja_paquete_tomo_otro1    = trim(str_ireplace($caja_paquete_tomo_otro, "<b><font color='red'>$caja_paquete_tomo_otro</font></b>", $linea['caja_paquete_tomo']));
        $numero_caja_paquete1 = trim(str_ireplace($numero_caja_paquete, "<b><font color='red'>$numero_caja_paquete</font></b>", $linea['numero_caja_paquete']));
        $numero_carpeta1 = trim(str_ireplace($numero_carpeta, "<b><font color='red'>$numero_carpeta</font></b>", $linea['numero_carpeta']));
        $consecutivo_desde1=$linea['consecutivo_desde'];
        $consecutivo_hasta1=$linea['consecutivo_hasta'];
        $metadato1 = trim(str_ireplace($metadato, "<b><font color='red'>$metadato</font></b>", $linea['descriptor']));     
        $folios1=$linea['total_folios'];
        $numero_caja_archivo_central1 = trim(str_ireplace($numero_caja_archivo_central, "<b><font color='red'>$numero_caja_archivo_central</font></b>", $linea['numero_caja_archivo_central']));
        $observaciones1=$linea['observaciones'];
      
/* Se arma el cuerpo de la tabla */          
        $cuerpo_tabla= "$cuerpo_tabla <tr class='$fila'>
        <td class='encabezado' style='width:300px; text-align: justify;'>$ubicacion_topografica1</td>
        <td class='encabezado'>$nombre_exp</td>
        <td class='encabezado celda'>$radicado_jonas1</td>
        <td class='encabezado'>$numero_consecutivo1</td>
        <td class='encabezado detalle_busqueda'>$codigo_dependencia1</td>
        <td class='encabezado detalle_busqueda'>$codigo_serie1</td>
        <td class='encabezado detalle_busqueda'>$codigo_subserie1</td>
        <td class='celda'>$nombre_documento1</td><td class='encabezado detalle_busqueda'>$fecha_inicial1</td><td class='encabezado detalle_busqueda'>$fecha_final1</td>
        <td class='encabezado'>$caja_paquete_tomo_otro1</td><td class='encabezado'>$numero_caja_paquete1</td>
        <td class='encabezado'>$numero_carpeta1</td>
        <td class='encabezado detalle_busqueda'>$consecutivo_desde1</td>
        <td class='encabezado detalle_busqueda'>$consecutivo_hasta1</td>
        <td class='encabezado'>$metadato1</td>
        <td class='encabezado'>$folios1</td><td class='encabezado'>$numero_caja_archivo_central1</td>
        <td class='encabezado'>$observaciones1</td>
        
        </tr>";
    $num_fila++;
    }
}else{
    $encabezado_tabla="$encabezado_tabla<div class='error'>No se encuentran resultados con los parámetros de búsqueda.<br> Revise por favor.</div>";
}

echo "
<table border='0' width='100%'>
    $encabezado_tabla
    $cuerpo_tabla
</table>";
?>




