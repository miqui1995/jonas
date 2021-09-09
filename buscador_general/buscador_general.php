<?php
require_once('../login/conexion2.php');
require_once("../login/validar_inactividad.php");

/*  Inicio funciones php para pagina en general */
    function verificar_padre($array,$nombre_nivel,$x){
        include('../login/conexion2.php');

        $query_ubic1="select * from ubicacion_topografica where nombre_nivel='$nombre_nivel'";
        
        $fila_ubicacion_fisica = pg_query($conectado,$query_ubic1);
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
        include('../login/conexion2.php');

        $query_ubicacion_topografica="select * from expedientes e join ubicacion_topografica u on e.codigo_ubicacion_topografica=u.id_ubicacion where e.id_expediente='$expediente'";

        $fila_ubicacion_topografica = pg_query($conectado,$query_ubicacion_topografica);
        $linea_ubicacion_topografica = pg_fetch_array($fila_ubicacion_topografica);

        if($linea_ubicacion_topografica==false){
            $ubicacion_fisica=false;
        }else{
            $codigo_ubic = $linea_ubicacion_topografica['codigo_ubicacion_topografica'];
            $nombre_nivel=$linea_ubicacion_topografica['nombre_nivel'];
            $nivel_padre=$linea_ubicacion_topografica['nivel_padre'];
            $asunto_expediente=$linea_ubicacion_topografica['nombre_expediente'];
            echo "<script>$('#asunto_expediente1').val(\"$asunto_expediente\");</script>";

            $array_padre = array();
            $ubicacion_fisica= verificar_padre($array_padre,"$nombre_nivel","");
        }
        
        return($ubicacion_fisica);
    }

/*  Fin funciones php para pagina en general */

$search_buscador    = strtoupper(trim($_POST['search_buscador']));
$por_expediente     = $_POST['por_exp'];
$por_inventario     = $_POST['por_inv'];
$por_radicado       = $_POST['por_rad'];
$tipo_busqueda      = $_POST['tipo_busqueda'];

$max_height=500;

if($por_radicado=="SI"){
    $max_height=$max_height-120;
}
if($por_expediente=="SI"){
    $max_height=$max_height-120;
}
if($por_inventario=="SI"){
    $max_height=$max_height-120;
}
// echo "busq max $max_height $search_buscador rad $por_radicado, exp $por_expediente, inv $por_inventario";

$encabezado_tabla="<tr>
    <td colspan='9'>
        <center>
            <input type='button' name='buscar' value='Volver a buscar' class='boton' onclick='volver_busqueda()'>
        </center>
    </td>
</tr>";

switch ($tipo_busqueda) {
    case 'por_rad':   
        // $query_rad="select r.numero_radicado, r.asunto, dor.nombre_remitente_destinatario, dor.dignatario, r.id_expediente from radicado r inner join datos_origen_radicado dor on r.numero_radicado=dor.numero_radicado where r.numero_radicado ilike '%$search_buscador%' or r.asunto ilike '%$search_buscador%' order by r.fecha_radicado, r.numero_radicado  desc limit 50";
        $query_rad="select * from radicado where numero_radicado ilike '%$search_buscador%' or asunto ilike '%$search_buscador%' or numero_guia_oficio ilike '%$search_buscador%' order by fecha_radicado, numero_radicado  desc limit 50";
    /* Ejecuta la query */
        $fila_consulta_rad = pg_query($conectado,$query_rad);

    /* Calcula el numero de registros que genera la consulta anterior. */
        $registros_consulta= pg_num_rows($fila_consulta_rad);

        if($registros_consulta>0){
            echo "<hr><h3 style=\"background-color:#e0e6e7; width:100%;\">Resultado Consulta por Radicados (solo lista los 50 primeros resultados)</h3><hr>";
            echo "<div style=\"overflow-x:auto; width:100%; max-height:$max_height\">";
            echo "
            <table border='0'>
                <tr class='descripcion center'>
                    <td colspan='4'></td>
                    
                    <td id='num_radicado' title='Numero de radicado'>No. Radicado</td> 
                    <td id='asunto' title='Asunto'>Asunto del Radicado</td>
                    <td id='numero_guia_oficio' title='Numero de Guia u Oficio'>Numero Guia u Oficio</td>
                    <td id='numero_expediente' title='Número de Expediente'>No. de Expediente</td>
                    <td id='nombre_contacto' title='Nombre del Contacto'>Nombre del Contacto</td>
                </tr>";
        // Inicia tabla 
            for ($i=0;$i<$registros_consulta;$i++){
                $linea              = pg_fetch_array($fila_consulta_rad);
                $numero_radicado    = $linea['numero_radicado'];
                $codigo_subserie    = $linea['codigo_subserie'];    // Para verificar TRD
                $codigo_contacto    = $linea['codigo_contacto'];    // Para verificar contacto 
                $numero_guia_oficio = $linea['numero_guia_oficio'];    // Para verificar contacto 

                $numero_guia_oficio1 = trim(str_ireplace($search_buscador, "<b><font color='red'>$search_buscador</font></b>", $numero_guia_oficio));

                $j=$i+1;
            // Define si tiene numero de expediente para mostrar carpeta verde o roja
                $id_expediente      = $linea['id_expediente'];

                if($id_expediente!=""){
                        $exp  = explode(",", $id_expediente);
                        $max  = sizeof($exp);
                        $max2 = $max-1;

                        $nombre_expediente  = "";
                        $codigo_ubicacion   = "";

                        if($max2==0){
                            $num_exp = $exp[0];
                            $consulta_exp       = "select * from expedientes where id_expediente='$num_exp'";
                            $fila_exp           = pg_query($conectado,$consulta_exp);
                            $linea_exp          = pg_fetch_array($fila_exp);
                            $nombre_exp         = $linea_exp['nombre_expediente'];
                            $codigo_ubic        = $linea_exp['codigo_ubicacion_topografica'];

                            $nombre_expediente  = $nombre_expediente."&#13;$num_exp ($nombre_exp)";

                        // Verificar si tiene ubicacion topografica
                            if($codigo_ubic==""){
                                // $codigo_ubicacion       = $codigo_ubicacion."&#13;$num_exp (No tiene ubicación topográfica)";
                                $ubicacion_topografica  = "<img height='20px' src='imagenes/iconos/ubicacion_rojo.png' title='($num_exp) No tiene ubicacion topográfica'> ";
                            }else{
                                $codigo_ubicacion       = $codigo_ubicacion."&#13;$num_exp (Si tiene ubicación topográfica)";
                                $ubicacion_topografica  = "<img height='20px' src='imagenes/iconos/ubicacion_verde.png' title='Si tiene ubicacion topográfica en expediente(s) ($num_exp) $codigo_ubicacion'> ";
                            }
                        // Fin de verificar si tiene ubicacion topografica

                        }else{
                            for ($k=0; $k < $max2; $k++) { 
                                $num_exp = $exp[$k];

                                $consulta_exp = "select * from expedientes where id_expediente='$num_exp'";
                                $fila_exp     = pg_query($conectado,$consulta_exp);
                                $linea_exp    = pg_fetch_array($fila_exp);
                                $nombre_exp   = $linea_exp['nombre_expediente'];
                                $codigo_ubic  = $linea_exp['codigo_ubicacion_topografica'];

                                $nombre_expediente  = $nombre_expediente."&#13;$num_exp ($nombre_exp)";

                            // Verificar si tiene ubicacion topografica
                                 if($codigo_ubic==""){
                                    // $codigo_ubicacion       = $codigo_ubicacion."&#13;$num_exp (No tiene ubicación topográfica)";
                                    $ubicacion_topografica  = "<img height='20px' src='imagenes/iconos/ubicacion_rojo.png' title='($num_exp) No tiene ubicacion topográfica'> ";
                                }else{
                                    $codigo_ubicacion       = $codigo_ubicacion."&#13;$num_exp (Si tiene ubicación topográfica)";
                                    $ubicacion_topografica  = "<img height='20px' src='imagenes/iconos/ubicacion_verde.png' title='Si tiene ubicacion topográfica en expediente(s) ($num_exp) $codigo_ubicacion'> ";
                                }
                            // Fin de verificar si tiene ubicacion topografica
                            }
                        }
                                           
                    $expediente="<img id='exp$numero_radicado' height='20px' src='imagenes/iconos/exp_verde.png' title='Se encuentra en expediente(s) $nombre_expediente'> ";
                    $id_expediente1 = $id_expediente;   // Para mostrar en resultados
                }else{
                    $expediente="<img id='exp$numero_radicado' height='20px' src='imagenes/iconos/exp_rojo.png' title='No se encuentra en un expediente.'> ";
                    $ubicacion_topografica = "<img height='20px' src='imagenes/iconos/ubicacion_rojo.png' title='No tiene ubicacion topográfica'> ";
                    $id_expediente1 = "";               // Para mostrar en resultados
                }

                /* Inicio verificar si tiene TRD */
                if($codigo_subserie==""){
                    $trd="<img id='trd$numero_radicado' height='25px' src='imagenes/iconos/trd_rojo.png' title='No tiene TRD asignada'>";
                }else{
                    $trd="<img id='trd$numero_radicado' height='25px' src='imagenes/iconos/trd_verde.png' title='Si tiene TRD asignada'>";
                }
                /* Fin verificar si tiene TRD */
                /* Inicio verificar si tiene codigo_contacto */
                if($codigo_contacto==""){
                    $nombre_contacto    = "";
                    $nombre_contacto2   = "";
                }else{
                    $query_rem_des="select r.numero_radicado, r.asunto, dor.nombre_remitente_destinatario, dor.dignatario, r.id_expediente from radicado r inner join datos_origen_radicado dor on r.numero_radicado=dor.numero_radicado where r.numero_radicado ='$numero_radicado'";

                    $fila_rem_des       = pg_query($conectado,$query_rem_des);
                    $linea_rem_des      = pg_fetch_array($fila_rem_des);
                    $registros_rem_des  = pg_num_rows($fila_rem_des);

                    if($registros_rem_des>0){
                        $nombre_contacto    = $linea_rem_des['nombre_remitente_destinatario']." - ".$linea_rem_des['dignatario'];
                        $nombre_contacto2   = substr("$nombre_contacto", 0, 120);
                    }else{
                        $nombre_contacto    = "";
                        $nombre_contacto2   = "";
                    }

                }
                /* Fin verificar si tiene codigo_contacto */
              
                $numero_radicado1   = trim(str_ireplace($search_buscador, "<b><font color='red'>$search_buscador</font></b>", $numero_radicado)); // Resalta con rojo el valor buscado

                $asunto     = $linea['asunto'];
                $asunto_cut = strtoupper(substr($asunto, 0,120));
                $asunto1    = trim(str_ireplace($search_buscador, "<b><font color='red'>$search_buscador</font></b>", $asunto_cut));     // Resalta con rojo el valor buscado


                    
            /*Aqui defino lo que voy a mostrar  */          
                echo "
                <tr class='art_busc' onclick=\"agregar_pestanas('$numero_radicado')\">
                    <td class='center' title='Consecutivo de ésta consulta'> $j</td>
                    <td> $expediente</td>
                    <td> $ubicacion_topografica</td>
                    <td> $trd</td>
                    <td id='num_radicado' title='Numero de radicado'>$numero_radicado1
                    </td> 
                    <td id='asunto' title='$asunto'>$asunto1</td>
                    <td id='numero_guia_oficio' title='Numero de Guia u Oficio'>$numero_guia_oficio1</td>
                    <td id='numero_expediente' title='Expediente $id_expediente'>$id_expediente1</td>
                    <td id='nombre_contacto' title='$nombre_contacto'>$nombre_contacto2</td>
                </tr>";
            }

            echo "<table>"; // Cierra tabla 
            // do{
            // }while ($fila_consulta_rad=pg_fetch_assoc($fila_consulta_rad));  
            echo "</div>"; // Cierra div con overflow-x (para responsive)   
        }else{
            echo "<center><h3>No hay radicados con éstos criterios de búsqueda.</h3></center>";
        }

        break;
    case 'por_exp':
        $query_exp="select * from expedientes where id_expediente ilike '%$search_buscador%' or nombre_expediente ilike '%$search_buscador%' order by id_expediente limit 50";
    /* Ejecuta la query */
        $fila_consulta_exp = pg_query($conectado,$query_exp);

    /* Calcula el numero de registros que genera la consulta anterior. */
        $registros_consulta= pg_num_rows($fila_consulta_exp);

        if($registros_consulta>0){
            echo "<hr><h3 style=\"background-color:#e0e6e7; width:100%;\">Resultado Consulta por Expediente (solo lista los 50 primeros resultados)</h3><hr>";
            echo "
            <div style=\"overflow-x:auto; width:100%; max-height:$max_height\">
            <table border='0'width='100%'>
                <tr class='descripcion center'>
                    <td colspan='2'></td>
                    <td id='numero_expediente'>No. Expediente</td>
                    <td style='padding :5px' title='Nombre del Expediente'>Nombre del Expediente</td>
                    <td style='padding :5px' title='Serie'>Serie</td> 
                    <td style='padding :5px' title='Subserie'>Subserie</td> 
                    <td style='padding :5px' title='Ubicación topográfica'>Ubicación topográfica</td> 
                </tr> "; // Inicia tabla 
         
            for ($i=0;$i<$registros_consulta;$i++){
                $linea = pg_fetch_array($fila_consulta_exp);

                $j=$i+1;

                $id_expediente = $linea['id_expediente'];
                $id_expediente1 = trim(str_ireplace($search_buscador, "<b><font color='red'>$search_buscador</font></b>", $id_expediente));

                $codigo_ubicacion_topografica = $linea['codigo_ubicacion_topografica'];

                if($codigo_ubicacion_topografica==''){
                    $ubicacion_topografica = "<img height='20px' src='imagenes/iconos/ubicacion_rojo.png' title='No tiene ubicacion topográfica'> ";
                }else{
                    $ubicacion_topografica = "<img height='20px' src='imagenes/iconos/ubicacion_verde.png' title='Si tiene ubicacion topográfica'> ";
                }   

                $nombre_expediente  = $linea['nombre_expediente'];
                $nombre_expediente2 = substr("$nombre_expediente", 0, 40);
                $nombre_expediente3 = trim(str_ireplace($search_buscador, "<b><font color='red'>$search_buscador</font></b>", $nombre_expediente2));
                $codigo_dependencia_serie = substr("$id_expediente", 7, 3);

                /* Inicia definir nombre serie y subserie */
                $serie1         = $linea['serie'];
                $subserie1       = $linea['subserie'];

                $query_subserie = "select * from subseries where codigo_dependencia='$codigo_dependencia_serie' and codigo_serie ='$serie1' and codigo_subserie='$subserie1'";
                $fila_subserie  = pg_query($conectado,$query_subserie);
                $linea_subserie = pg_fetch_array($fila_subserie);
                $serie          = "($serie1) ".$linea_subserie['nombre_serie'];
                $subserie       = "($subserie1) ".$linea_subserie['nombre_subserie'];
                /* Fin definir nombre serie y subserie */
                // Inicia ubicacion topografica del expediente
                $ubicacion_topografica2 = validar_ubicacion($id_expediente);

                if($ubicacion_topografica2==false){
                    $ubicacion_topografica3 = "Expediente no se ha ubicado topográficamente todavía";
                }else{
                    $ubicacion_topografica3 = ""; 
                    foreach($ubicacion_topografica2 as $item){
                        if($ubicacion_topografica3 == ""){
                            $ubicacion_topografica3 = $item;
                        }else{
                            $ubicacion_topografica3 = $ubicacion_topografica3." <- ".$item;
                        }
                    }
                }
                $ubicacion_topografica1=$ubicacion_topografica3;

                // Fin ubicacion topografica del expediente

                echo "
                <tr class='art_busc'>
                    <td class='center' title='Consecutivo de ésta consulta'>$j</td>
                    <td>$ubicacion_topografica</td>
                    <td id='numero_expediente'>$id_expediente1</td>
                    <td style='padding :5px' title='$nombre_expediente'>$nombre_expediente3</td>
                    <td style='padding :5px' title='Serie' class='center'>$serie</td> 
                    <td style='padding :5px' title='Subserie' class='center'>$subserie</td> 
                    <td style='padding :5px' title='Ubicación topográfica'>$ubicacion_topografica1</td> 
                </tr>";               
            }
          
            echo "
            </table>
            </div>"; // Cierra div con overflow-x (para responsive)   
        }else{
            echo "<center><h3>No hay expedientes con éstos criterios de búsqueda.</h3></center>";
        }

        break;
    case 'por_inv':
        $query_inv="select * from inventario where radicado_jonas ilike '%$search_buscador%' or descriptor ilike '%$search_buscador%' or nombre_documento ilike '%$search_buscador%' order by radicado_jonas  desc limit 50";
    /* Ejecuta la query */
        $fila_consulta_inv = pg_query($conectado,$query_inv);
    /* Calcula el numero de registros que genera la consulta anterior. */
        $registros_consulta= pg_num_rows($fila_consulta_inv);

        if($registros_consulta>0){
            echo "<hr><h3 style=\"background-color:#e0e6e7; width:100%;\">Resultado Consulta por inventario (solo lista los 50 primeros resultados)</h3><hr>";

            echo "
            <div style=\"overflow-x:auto; width:100%; max-height:$max_height\">
            <table border='0'width='100%'>
                <tr class='descripcion center'>
                    <td colspan='2'></td>
                    <td id='numero_expediente'>No. Radicado</td>
                    <td style='padding :5px' title='Asunto del documento'>Asunto del documento</td>
                    <td style='padding :5px' title='Descriptores o Metadatos'>Descriptores o Metadatos</td> 
                </tr> "; // Inicia tabla 

                for ($i=0;$i<$registros_consulta;$i++){
                    $linea = pg_fetch_array($fila_consulta_inv);

                    $j=$i+1;

                    $numero_caja_archivo_central = $linea['numero_caja_archivo_central'];

                // Verificar si tiene ubicacion topografica
                    if($numero_caja_archivo_central==''){
                        $ubicacion_topografica = "<img height='20px' src='imagenes/iconos/ubicacion_rojo.png' title='No tiene ubicacion topográfica'> ";
                        $boolean_ubicacion = "No tiene ubicación topográfica";
                    }else{
                        $ubicacion_topografica = "<img height='20px' src='imagenes/iconos/ubicacion_verde.png' title='Si tiene ubicacion topográfica'> ";
                        $boolean_ubicacion = "Si tiene ubicación topográfica";
                    }
                // Fin de verificar si tiene ubicacion topografica

             
                    $radicado_jonas = $linea['radicado_jonas'];
                    $radicado_jonas1 = trim(str_ireplace($search_buscador, "<b><font color='red'>$search_buscador</font></b>", $radicado_jonas));

                    $nombre_documento =$linea['nombre_documento'];
                    $nombre_documento2=substr("$nombre_documento", 0, 100);
                    $nombre_documento3 = trim(str_ireplace($search_buscador, "<b><font color='red'>$search_buscador</font></b>", $nombre_documento2));

                    $descriptor =$linea['descriptor'];
                    $descriptor2=substr("$descriptor", 0, 100);
                    $descriptor3 = trim(str_ireplace($search_buscador, "<b><font color='red'>$search_buscador</font></b>", $descriptor2));
                        
                    echo "
                    <tr class='art_busc' onclick=\"agregar_pestanas('$radicado_jonas')\">
                        <td title='Consecutivo de ésta consulta' class='center'>$j</td>
                        <td title='$boolean_ubicacion' class='center'>$ubicacion_topografica</td>
                        <td title='Numero de radicado Jonas'>$radicado_jonas1</td>
                        <td style=\" padding : 5px\" title='$nombre_documento'>$nombre_documento3</td>
                        <td style=\" padding : 5px\" title='$descriptor'>$descriptor3</td>
                    </tr>";                  
                }

            echo "
            </table>
            </div>"; // Cierra div con overflow-x (para responsive)   
        }else{
            echo "<center><h3>No hay radicados con éstos criterios de búsqueda.</h3></center>";
        }

        break;
    
    default:
        echo "Seleccione por lo menos un parametro de búsqueda.";
        break;
}
?>