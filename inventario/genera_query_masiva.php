<?php 
/**************************************************************
* @brief Este archivo es invocado mediante ajax desde el archivo include/js/funciones_inventario.js[$("#archivo").on("change", function()] y se encarga de procesar un archivo .csv delimitado por comas y valida uno a uno los campos para realizar el cargue masivo del Formato Unico de Inventario Documental (FUID). Desde este mismo archivo, genera la inserción a la base de datos a las tablas de inventario, expedientes, radicado, datos_origen_radicado, carpetas_personales e historico eventos.

El archivo .CSV (Separado por comas pero en realidad llega separado por punto y coma ";" para poder procesarlo.)

* @param {file} ($archivo) Es el archivo en formato .csv el cual se lee mediante php y se procesa validando uno a uno los campos que registra.
*/
if(!isset($_SESSION)){
    session_start();
}  
?>
<!DOCTYPE html>
<html>
<head></head>
<body>
<style type="text/css">
    .input_query{
        display : none; 
        height  : 100px;
        padding : 5px;
        width   : 100%;
    }
</style>
<?php

//var_dump($_SESSION);

$cargado_por                    = $_SESSION['nombre'];          // Variable para tabla inventario
$id_usuario                     = $_SESSION['id_usuario']; 
$login_usuario                  = $_SESSION['login'];
$codigo_dependencia_radicador   = $_SESSION['dependencia'];
$codigo_entidad                 = $_SESSION['codigo_entidad'];  // Codigo de entidad para interoperabilidad entre Jonas

require_once('../login/conexion2.php');

$archivotmp = $_FILES['archivo']['tmp_name'];
$pesotmp    = $_FILES['archivo']['size'];
$lineas     = file($archivotmp);    //cargamos el archivo

$timestamp  = date('Y-m-d H:i:s');   // Genera la fecha de transaccion; queda en formato AAAA-MM-DD HH24:MM:SS 

$consecutivo3                       = 0;
$count                              = array();
$cuerpo_tabla                       = "";
$leido                              = "$login_usuario".",";
$query_total_datos_origen_radicado  = "";
$query_total_ubicacion_fisica       = "";
$query_total_historico              = "";
$query_total_insertar_expediente    = "";
$query_total_insertar_inventario    = "";
$query_total_insertar_radicado      = "";
$total_informacion_fila             = "";
$valida                             = "true";

/* Cantidad de filas del CSV */
$cantidad_filas = sizeof($lineas); 

/* Se consulta el codigo maximo de inventario (por ejemplo "2020JBBINVE00000051") para  */
$query_actualiza_secuencia  = "select max(radicado_jonas) from inventario";
$fila_max                   = pg_query($conectado,$query_actualiza_secuencia);
$linea_max                  = pg_fetch_array($fila_max);
$radicado                   = $linea_max['max'];

/* Se determina cual es el codigo para inventario (INV-INVE-INVEN) */
/* Inicio definiendo longitud_radicado que depende de la variable $_SESSION['caracteres_depend']*/
$caracteres_dependencia             = $_SESSION['caracteres_depend'];

switch ($caracteres_dependencia) {
    case 3:
        $codigo_inventario = 'INV';
        break;
    case 4:
        $codigo_inventario = 'INVE';
        break;    
    case 5:
        $codigo_inventario = 'INVEN';
        break;    
}
/* Fin se determina cual es el codigo para inventario (INV-INVE-INVEN) */

/* Se define que la posición estándar es en 7 caracteres (por ejemplo "2020JBB") y dependiendo la variable $caracteres dependencia puede ser 10,11 o 12. */
$cantidad_caracteres_antes_secuencia = 7+$caracteres_dependencia;
$verifica_secuencia         = intval(substr($radicado,$cantidad_caracteres_antes_secuencia,7))+1;

$query_alter_sequence_inicial="alter sequence secuencia_inventario restart $verifica_secuencia";

/* Fin define secuencia inicial para radicado */

/**************************************************************
Inicio Fin funcion para validar los encabezados en la segunda fila del CSV */
/**
* @class La funcion valida_encabezado($variable,$nombre_columna) valida si el nombre de la variable que se recibe por parámetro con el nombre de la columna definido también como parámetro.
* @ Recibe parámetros y devuelve un div con el mensaje de error si no corresponden los valores al valor del encabezado del CSV recibido.
* @param {string} ($variable)       En este parámetro se recibe el nombre de la columna que viene en el archivo CSV
* @param {string} ($nombre_columna) En este parámetro se recibe el nombre de como debería llamarse la columna.
* @return {string} String con el div con el mensaje de error. Si no es error, devuelve vacío.
**************************************************************/
function valida_encabezado($variable,$nombre_columna){ // Funcion para validar los encabezados en la segunda fila del formato CSV 
    if($variable!=$nombre_columna){
        return "<br><div class='error'>Hay un error. El encabezado del CSV debe ser<h2>($nombre_columna)</h2> y la columna que aparece es <h2>($variable)</h2> por lo tanto, la columna no es válida. Revisar y corregir por favor.</div>"; 
    }
}
/* Fin funcion para validar los encabezados en la segunda fila del CSV
**************************************************************/
/**************************************************************
Inicio Fin funcion para imprimir los encabezados en la segunda fila del CSV */
/**
* @class La funcion valida_encabezado2($variable,$nombre_columna) valida si el nombre de la variable que se recibe por parámetro con el nombre de la columna definido también como parámetro. Hace lo mismo que la funcion valida_encabezado($variable,$nombre_columna) pero en este caso si imprime una fila "<td></td> con el valor respectivo con el fin de imprimir si hay error e imprimirlo en la tabla para guiar al usuario correctamente."
* @ Recibe parámetros y devuelve "<td>" para imprimir si es un error o si está correcto.
* @param {string} ($variable)       En este parámetro se recibe el nombre de la columna que viene en el archivo CSV
* @param {string} ($nombre_columna) En este parámetro se recibe el nombre de como debería llamarse la columna.
* @return {string} String con el "<td>" con el mensaje de error. Si no es error, devuelve "<td> con el valor correcto".
**************************************************************/

function valida_encabezado2($variable,$nombre_columna){ // Funcion para validar los encabezados en la segunda fila del formato CSV 
    if($variable!=$nombre_columna){
        return "<td class='error'>Hay un error. El encabezado del CSV debe ser<h2>($nombre_columna)</h2> y la columna que aparece es <h2>($variable)</h2> por lo tanto, la columna no es válida. Revise y corrija por favor.</td>"; 
    }else{
        return "<td class='encabezado encabezado1' style='background-color:#0066cc;'>$variable</td>"; 
    }
}
/* Fin funcion para validar los encabezados en la segunda fila del CSV
**************************************************************/
/**************************************************************
Inicio funcion para validar primera columna "Numero_consecutivo" desde la tercera fila del CSV hasta el final del archivo */
/**
* @class La funcion validar_numero_consecutivo($consecutivo_anterior,$numero_consecutivo, $j,$conectado,$query_alter_sequence_inicial) valida si el valor de la columna "Numero_consecutivo del CSV por cada una de las filas, es un número o si no corresponde al siguiente que se encuentra en la base de datos."
* @ Recibe parámetros y devuelve "<div class='error'>" para imprimir si es un error o si está correcto, devuelve "false" para imprimir el campo correspondiente.
* @param {string} ($consecutivo_anterior) En este parámetro se recibe el valor del ($numero_consecutivo +1) del que viene en el archivo CSV para comparar y calcular si hay error en el numero siguiente al que debería ir. 
* @param {string} ($numero_consecutivo) En este parámetro se recibe el valor de la columna "numero_consecutivo" correspondiente a la fila que viene en el archivo CSV. 
* @param {string} ($j) En este parámetro se recibe el numero de fila que se está procesando.
* @param {string} ($conectado) En este parámetro se recibe la variable correspondiente para ejecutar una consulta en la base de datos.
* @param {string} ($query_alter_sequence_inicial) En este parámetro se recibe la query para ejecutar en el caso que corresponda para dejar la base de datos en la misma secuencia en la que se encontraba y sin insertar ninguna fila.
**************************************************************/
function validar_numero_consecutivo($consecutivo_anterior,$numero_consecutivo, $j,$conectado,$query_alter_sequence_inicial){
    
    $is_num = is_numeric($numero_consecutivo); // Valida si el numero_consecutivo es un numero.

    if($is_num==false){  // Validar si el numero consecutivo NO es un "numero" 
        pg_query($conectado,$query_alter_sequence_inicial); // Se reinicia el consecutivo inicial
        $boton_enviar="<div class='error'>El código del consecutivo (<font style=\"color:black; font-size:25px; font-weight:bold;\">$numero_consecutivo</font>) en la fila numero <b>$j</b> de su archivo CSV, <font style=\"color:black; font-size:18px; font-weight:bold;\"> NO ES UN NUMERO. </font> <br>Revisar y corregir por favor.</div>"; 
    }else{              // En este caso el numero consecutivo SI es un "numero"
        $consecutivo_anterior2=$consecutivo_anterior-1;
        if($consecutivo_anterior2!=$numero_consecutivo){
            pg_query($conectado,$query_alter_sequence_inicial); // Si se reinicia el consecutivo
            $boton_enviar = "<div class='error'>El código del consecutivo <font style=\"color:black; font-size:18px;\">($numero_consecutivo)</font> en la fila numero <b>$j</b> de su archivo CSV, NO CORRESPONDE AL NUMERO SIGUIENTE. <br> Debería ser el numero <font style=\"color:black; font-size:25px; font-weight:bold;\">($consecutivo_anterior2)</font> Revise por favor.</div>";             
        }else{
            $consulta                   = "select * from inventario where numero_consecutivo='$numero_consecutivo' ";
            $fila_numero_consecutivo    = pg_query($conectado,$consulta);

            $linea = pg_fetch_array($fila_numero_consecutivo);    

            if($linea==false){  // Si no hay resultados
                $boton_enviar="false";
            }else{              // Quiere decir que el numero de consecutivo ya existe
                $consulta_max   = "select max(numero_consecutivo) from inventario";
                $fila_max       = pg_query($conectado,$consulta_max);
                $linea_max      = pg_fetch_array($fila_max);
                $linea_max      = $linea_max['max']+1;

                $boton_enviar="<div class='error'>El numero consecutivo <font style=\"color:black; font-size:25px; font-weight: bold;\">($numero_consecutivo)</font> en la fila numero $j de su archivo CSV, ya existe en la base de datos.<br> El siguiente numero para su consecutivo debería ser el <font style=\"color:black; font-size:25px; font-weight: bold;\">($linea_max)</font> Revise por favor.</div>"; 
                pg_query($conectado,$query_alter_sequence_inicial); // Si se reinicia el consecutivo
            }   
        }
    }
    return $boton_enviar;
}
/* Fin funcion para validar primera columna "numero_consecutivo" desde la tercera fila del CSV hasta el final del archivo
**************************************************************/
/**************************************************************
Inicio funcion para validar segunda columna "codigo_dependencia" desde la tercera fila del CSV hasta el final del archivo */
/**
* @class La funcion validar_codigo_dependencia($codigo_dependencia,$j,$query_alter_sequence_inicial,$conectado) valida si el valor de la columna "codigo_dependencia del CSV por cada una de las filas, existe o no en la base de datos."
* @ Recibe parámetros y devuelve "<div class='error'>" para imprimir si es un error o si está correcto, devuelve "false" para imprimir el campo correspondiente.
* @param {string} ($codigo_dependencia) En este parámetro se recibe el valor del codigo de la dependencia y se consulta en la base de datos. Si el codigo existe, retorna "false" para imprimir el valor de la columna.
* @param {string} ($j) En este parámetro se recibe el numero de fila que se está procesando.
* @param {string} ($query_alter_sequence_inicial) En este parámetro se recibe la query para ejecutar en el caso que corresponda para dejar la base de datos en la misma secuencia en la que se encontraba y sin insertar ninguna fila.
* @param {string} ($conectado) En este parámetro se recibe la variable de conexion correspondiente para ejecutar una consulta en la base de datos.
**************************************************************/
function validar_codigo_dependencia($codigo_dependencia,$j,$query_alter_sequence_inicial,$conectado){
    if($codigo_dependencia==""){
        $validacion_codigo_dependencia = "<div class='error'>El código de la dependencia en la fila numero <b>$j</b> de su archivo CSV. <font style=\"color:black; font-size:25px; font-weight:bold;\">ES OBLIGATORIO</font> <br> Por favor revisar y corregir.</div>";
    }else{
        $query_dependencia  = "select * from dependencias where codigo_dependencia ='$codigo_dependencia'";
        $fila_dependencia   = pg_query($conectado,$query_dependencia);

        $linea = pg_fetch_array($fila_dependencia);    

        if($linea==false){  // Si no hay resultados
            $validacion_codigo_dependencia = "<div class='error'>El código de la dependencia (<font style=\"color:black; font-size:18px; font-weight:bold;\">$codigo_dependencia</font>) en la fila numero <b>$j</b> de su archivo CSV. <font style=\"color:black; font-size:25px; font-weight:bold;\">NO EXISTE EN LA BASE DE DATOS.</font>  <br> Por favor revisar y corregir.</div>";
                pg_query($conectado,$query_alter_sequence_inicial); // Si se reinicia el consecutivo
        }else{              // Quiere decir que el numero de consecutivo ya existe
            $validacion_codigo_dependencia="false";
        } 
    }
    return $validacion_codigo_dependencia;
}
/* Fin funcion para validar segunda columna "codigo_dependencia" desde la tercera fila del CSV hasta el final del archivo
**************************************************************/
/**************************************************************
Inicio funcion para validar tercera columna "serie" desde la tercera fila del CSV hasta el final del archivo */
/**
* @class La funcion validar_codigo_serie($codigo_serie,$j,$query_alter_sequence_inicial,$conectado) valida si el valor de la columna "codigo_serie" del CSV por cada una de las filas, existe o no en la base de datos."
* Recibe parámetros y devuelve "<div class='error'>" para imprimir si es un error o si está correcto, devuelve "false" para imprimir el campo correspondiente.
* @param {string} ($codigo_serie) En este parámetro se recibe el valor del codigo de la serie y se consulta en la base de datos. Si el codigo existe, retorna "false" para imprimir el valor de la columna.
* @param {string} ($j) En este parámetro se recibe el numero de fila que se está procesando.
* @param {string} ($query_alter_sequence_inicial) En este parámetro se recibe la query para ejecutar en el caso que corresponda para dejar la base de datos en la misma secuencia en la que se encontraba y sin insertar ninguna fila.
* @param {string} ($conectado) En este parámetro se recibe la variable de conexion correspondiente para ejecutar una consulta en la base de datos.
**************************************************************/
function validar_codigo_serie($codigo_serie,$j,$query_alter_sequence_inicial,$conectado){
    if($codigo_serie==""){
        $validacion_codigo_serie = "<div class='error'>El código de la serie en la fila numero <b>$j</b> de su archivo CSV. <font style=\"color:black; font-size:25px; font-weight:bold;\">ES OBLIGATORIO</font> <br> Por favor revisar y corregir.</div>";
    }else{
        $query_serie  = "select * from series where codigo_serie ='$codigo_serie'";
        $fila_serie   = pg_query($conectado,$query_serie);

        $linea = pg_fetch_array($fila_serie);    

        if($linea==false){  // Si no hay resultados
            $validacion_codigo_serie = "<div class='error'>El código de la serie (<font style=\"color:black; font-size:18px; font-weight:bold;\">$codigo_serie</font>) en la fila numero <b>$j</b> de su archivo CSV. <font style=\"color:black; font-size:25px; font-weight:bold;\">NO EXISTE EN LA BASE DE DATOS.</font>  <br> Por favor revisar y corregir.</div>";
                pg_query($conectado,$query_alter_sequence_inicial); // Si se reinicia el consecutivo
        }else{              // Quiere decir que el numero de consecutivo ya existe
            $validacion_codigo_serie="false";
        } 
    }
    return $validacion_codigo_serie;
}
/* Fin funcion para validar tercera columna "subserie" desde la tercera fila del CSV hasta el final del archivo
**************************************************************/
/**************************************************************
Inicio funcion para validar cuarta columna "subserie" desde la tercera fila del CSV hasta el final del archivo */
/**
* @class La funcion validar_codigo_subserie($codigo_serie,$codigo_subserie,$j,$query_alter_sequence_inicial,$conectado) valida si el valor de la columna "codigo_subserie" del CSV por cada una de las filas, existe o no en la base de datos."
* Recibe parámetros y devuelve "<div class='error'>" para imprimir si es un error o si está correcto, devuelve "false" para imprimir el campo correspondiente.
* @param {string} ($codigo_serie) En este parámetro se recibe el valor del codigo de la serie para armar la consulta a la base de datos. 
* @param {string} ($codigo_subserie) En este parámetro se recibe el valor del codigo de la subserie y se consulta en la base de datos junto con el codigo de la serie. Si el codigo existe, retorna "false" para imprimir el valor de la columna.
* @param {string} ($j) En este parámetro se recibe el numero de fila que se está procesando.
* @param {string} ($query_alter_sequence_inicial) En este parámetro se recibe la query para ejecutar en el caso que corresponda para dejar la base de datos en la misma secuencia en la que se encontraba y sin insertar ninguna fila.
* @param {string} ($conectado) En este parámetro se recibe la variable de conexion correspondiente para ejecutar una consulta en la base de datos.
**************************************************************/
function validar_codigo_subserie($codigo_serie,$codigo_subserie,$j,$query_alter_sequence_inicial,$conectado){
    if($codigo_serie==""){
        $validacion_codigo_subserie = "<div class='error'>El código de la subserie en la fila numero <b>$j</b> de su archivo CSV. <font style=\"color:black; font-size:25px; font-weight:bold;\">ES OBLIGATORIO</font> <br> Por favor revisar y corregir.</div>";
    }else{
        $query_subserie  = "select * from subseries where codigo_serie ='$codigo_serie' and codigo_subserie='$codigo_subserie'";
        $fila_subserie   = pg_query($conectado,$query_subserie);

        $linea = pg_fetch_array($fila_subserie);    

        if($linea==false){  // Si no hay resultados
            $validacion_codigo_subserie = "<div class='error'>El código de la serie - subserie (<font style=\"color:black; font-size:18px; font-weight:bold;\">$codigo_serie - $codigo_subserie</font>) en la fila numero <b>$j</b> de su archivo CSV. <font style=\"color:black; font-size:25px; font-weight:bold;\">NO EXISTE EN LA BASE DE DATOS.</font>  <br> Por favor revisar y corregir.</div>";
                pg_query($conectado,$query_alter_sequence_inicial); // Si se reinicia el consecutivo
        }else{              // Quiere decir que el numero de consecutivo ya existe
            $validacion_codigo_subserie="false";
        } 
    }
    return $validacion_codigo_subserie;
}
/* Fin funcion para validar cuarta columna "subserie" desde la tercera fila del CSV hasta el final del archivo
**************************************************************/

function validar_fecha($fecha1,$fecha2,$cantidad_caracteres,$campo,$fila,$query_alter_sequence_inicial,$conectado){ 
    $cant1=strlen($fecha1);
    $boton_enviar = "$campo - $cantidad_caracteres($cant1) -- fecha1 $fecha1 --- fecha2 $fecha2";
    
    if($cantidad_caracteres<$cant1){    // Valida tamaño de la cadena de texto por caracteres
        pg_query($conectado,$query_alter_sequence_inicial); // Si se reinicia el consecutivo
        $boton_enviar="<div class='error'>El campo <h2>$campo <b style=\"color:black;\">($fecha1)</b> en la fila $fila</h2> de su archivo CSV puede tener hasta <b style=\"color:black;\">($cantidad_caracteres)</b> caracteres para poder insertarlo en la base de datos. Revise por favor.</div>"; 
    }else{
        if($campo=='FECHA_FINAL' && ($fecha2>$fecha1)){
            $boton_enviar="<div class='error'>El campo <h2>$campo <b style=\"color:black;\">($fecha1)</b> en la fila $fila</h2> de su archivo CSV es una fecha menor a la fecha inicial<b>($fecha2)</b>. <br>La fecha final no puede ser menor a la fecha inicial.<br> Revise por favor.</div>"; 
    //     }else{
    //        
        }else{
            $separador_dia=substr($fecha1,2,1);
            $separador_mes=substr($fecha1,5,1);

            if($separador_dia !="/"){
                $boton_enviar = "<div class='error'>El campo <h2>$campo <b style=\"color:black;\">($fecha1)</b> en la fila $fila</h2> de su archivo CSV No tiene el formato de fecha correcto. Debe ser con formato <h2>DD/MM/AAAA</h2> </div>";
            }else{
                if($separador_mes !="/"){
                    $boton_enviar = "<div class='error'>El campo <h2>$campo <b style=\"color:black;\">($fecha1)</b> en la fila $fila</h2> de su archivo CSV No tiene el formato de fecha correcto. Debe ser con formato <h2>DD/MM/AAAA</h2> </div>";
                }else{
                    $boton_enviar="false";
                }
            }
        }     
    }
    return $boton_enviar;
}


function validar_largo_campo($codigo_campo,$cantidad_caracteres, $campo, $fila, $query_alter_sequence_inicial, $conectado){ // Validar tamaño de caracteres
    $cant1=strlen($codigo_campo);
    
    if($cantidad_caracteres<$cant1){    // Valida tamaño de la cadena de texto por caracteres
        pg_query($conectado,$query_alter_sequence_inicial); // Si se reinicia el consecutivo
        $boton_enviar="<div class='error'>El campo <font style='font-size: 25px; font-weight: bold;'>$campo</font><br><font style='color:black;'>($codigo_campo)</font> <br>en la fila <font style='font-size: 25px; font-weight: bold;'>$fila</font> de su archivo CSV puede tener hasta <b style=\"color:black;\">($cantidad_caracteres)</b> caracteres (actualmente tiene <b style='color: black;'>$cant1</b>) para poder insertarlo en la base de datos. Revisar y corregir por favor.</div>"; 
    }else{
        $boton_enviar="false";
    }
    return $boton_enviar;
}
/**************************************************************************************************************/
/* Desde aqui inicia la validacion del CSV */
/* Primera validacion : Valida si el archivo pesa mas de 8Mb */
if($pesotmp>8000000){   
    echo "El archivo no puede pesar mas de 8Mb";
}else{
    /* Inicia con el recorrido de TODAS las filas una por una desde el CSV empezando por la columna 0 */ 
    for ($i=0; $i<$cantidad_filas;$i++){ 

/* Segunda validacion : Valida si la cantidad de filas del CSV es superior a 4.000 */
        if($cantidad_filas>4002){  
            // Se reinicia el consecutivo porque al recorrer uno se va generando una secuencia siguiente. Pero al llegar a ser superior a 4000 reinicia consecutivo para no perder la secuencia
            pg_query($conectado,$query_alter_sequence_inicial); 
            $boton_enviar="
            <div class='error'>
                Operacion interrumpida. Su archivo CSV tiene más de <b>4.000 (Cuatro mil) filas.</b>. Revise y cargue el archivo CSV nuevamente.
            </div>";
            break;
        }

        /* Busca el caracter ";"" ya que el CSV es separado por ; */
        $datos = explode(";",$lineas[$i]); 

        /* Con esta variable se define la cantidad de columnas del FUID */
        $cantidad_columnas=sizeof($datos); 

        $numero_consecutivo             = utf8_encode(trim($datos[0]));
        $codigo_dependencia             = utf8_encode(trim($datos[1]));
        $codigo_serie                   = utf8_encode(trim($datos[2]));
        $codigo_subserie                = utf8_encode(trim($datos[3]));
        $nombre_documento               = utf8_encode(trim($datos[4]));
        $fecha_inicial                  = utf8_encode(trim($datos[5]));
        $fecha_final                    = utf8_encode(trim($datos[6]));
        $caja_paquete_tomo_otro         = utf8_encode(trim($datos[7]));
        $numero_caja_paquete            = utf8_encode(trim($datos[8]));
        $numero_carpeta                 = utf8_encode(trim($datos[9]));
        $desde                          = utf8_encode(trim($datos[10]));
        $hasta                          = utf8_encode(trim($datos[11]));
        $descriptor                     = utf8_encode(trim($datos[12]));
        $total_folios                   = utf8_encode(trim($datos[13]));
        $numero_caja_archivo_central    = utf8_encode(trim($datos[14]));
        $observaciones                  = utf8_encode(trim($datos[15]));
/* Tercera validacion : Si la fila es la numero 1 es decir, la fila de los encabezados del formato. */
        if($i==1){ 

            /* Se concatena todos los valores con la funcion valida_encabezado. Si esta todo correcto devuelve "true" en la variable $valida si tiene algun error, asigna la variable $valida el error impreso */
            $validar_encabezados=
                valida_encabezado($numero_consecutivo,'NUMERO_CONSECUTIVO').
                valida_encabezado($codigo_dependencia,'CODIGO_DEPENDENCIA').
                valida_encabezado($codigo_serie,'CODIGO_SERIE').
                valida_encabezado($codigo_subserie,'CODIGO_SUBSERIE').
                valida_encabezado($nombre_documento,'NOMBRE_DOCUMENTO').
                valida_encabezado($fecha_inicial,'FECHA_INICIAL').
                valida_encabezado($fecha_final,'FECHA_FINAL').
                valida_encabezado($caja_paquete_tomo_otro,'CAJA_PAQUETE_TOMO_OTRO').
                valida_encabezado($numero_caja_paquete,'NUMERO_CAJA_PAQUETE').
                valida_encabezado($numero_carpeta,'NUMERO_CARPETA').
                valida_encabezado($desde,'DESDE').
                valida_encabezado($hasta,'HASTA').
                valida_encabezado($descriptor,'DESCRIPTOR').
                valida_encabezado($total_folios,'TOTAL_FOLIOS').
                valida_encabezado($numero_caja_archivo_central,'NUMERO_CAJA_ARCHIVO_CENTRAL').
                valida_encabezado($observaciones,'OBSERVACIONES');
            
            if($validar_encabezados!=""){ // Si la validación trae alguna obervación
                $valida=$validar_encabezados;  
            }else{  // Indica que no hay errores en el encabezado. Por eso es "true"
                $valida="true";
            }
        /* Se define el encabezado de la tabla */
    /* Cuarta validacion: Si la variable $valida no tiene el valor "true" hay un error por lo que ejecuta la funcion valida_encabezado2(param1,param2) para imprimir en una tabla igual al excel el campo donde se encuentra el error. */    
            if($valida!="true"){    // Si hay alguna observacion del encabezado
                $encabezado_revisado="";
                $encabezado_revisado.=
                    valida_encabezado2($numero_consecutivo,'NUMERO_CONSECUTIVO').
                    valida_encabezado2($codigo_dependencia,'CODIGO_DEPENDENCIA').
                    valida_encabezado2($codigo_serie,'CODIGO_SERIE').
                    valida_encabezado2($codigo_subserie,'CODIGO_SUBSERIE').
                    valida_encabezado2($nombre_documento,'NOMBRE_DOCUMENTO').
                    valida_encabezado2($fecha_inicial,'FECHA_INICIAL').
                    valida_encabezado2($fecha_final,'FECHA_FINAL').
                    valida_encabezado2($caja_paquete_tomo_otro,'CAJA_PAQUETE_TOMO_OTRO').
                    valida_encabezado2($numero_caja_paquete,'NUMERO_CAJA_PAQUETE').
                    valida_encabezado2($numero_carpeta,'NUMERO_CARPETA').
                    valida_encabezado2($desde,'DESDE').
                    valida_encabezado2($hasta,'HASTA').
                    valida_encabezado2($descriptor,'DESCRIPTOR').
                    valida_encabezado2($total_folios,'TOTAL_FOLIOS').
                    valida_encabezado2($numero_caja_archivo_central,'NUMERO_CAJA_ARCHIVO_CENTRAL').
                    valida_encabezado2($observaciones,'OBSERVACIONES');

                $encabezado_tabla="<tr class='encabezado encabezado1' style='background-color:#0066cc;'>
                    <td>CONTADOR UNIDAD DOCUMENTAL</td>
                    <td colspan='3'>CÓDIGO TABLA DE RETENCIÓN DOCUMENTAL</td>
                    <td>ASUNTO O DESCRIPCIÓN</td>
                    <td colspan='2'>FECHAS EXTREMAS</td>
                    <td colspan='3'>UNIDAD DE CONSERVACION</td>
                    <td colspan='2'>CONSECUTIVO</td>
                    <td>METADATO</td>
                    <td>FOLIOS</td>
                    <td>CONSECUTIVO CAJA</td>
                    <td>NOTAS</td></tr>
                    <tr>$encabezado_revisado</tr>";

                $boton_enviar = "";    // Por el hecho que hay un break en el ciclo, se requiere que esta variable esté vacía.
                break;
            }else{                  // Si el encabezado es correcto.
                $encabezado_tabla="
                <tr class='descripcion' style='text-align:center;'>
                    <td style='padding:5px;'>CONTADOR UNIDAD DOCUMENTAL</td>
                    <td colspan='3' style='padding:5px;'>CÓDIGO TABLA DE RETENCIÓN DOCUMENTAL</td>
                    <td style='padding:5px;'>ASUNTO O DESCRIPCIÓN</td>
                    <td style='padding:5px;' colspan='2'>FECHAS EXTREMAS</td>
                    <td style='padding:5px;' colspan='3'>UNIDAD DE CONSERVACION</td>
                    <td style='padding:5px;' colspan='2'>CONSECUTIVO</td>
                    <td style='padding:5px;'>METADATO</td>
                    <td style='padding:5px;'>FOLIOS</td>
                    <td style='padding:5px;'>CONSECUTIVO CAJA</td>
                    <td style='padding:5px;'>NOTAS</td>
                </tr>
                <tr class='descripcion' style='text-align:center;'>
                    <td style='padding: 5px;'>NUMERO_CONSECUTIVO</td>
                    <td style='padding: 5px;'>CODIGO_DEPENDENCIA</td>
                    <td style='padding: 5px;'>CODIGO_SERIE</td>
                    <td style='padding: 5px;'>CODIGO_SUBSERIE</td>
                    <td style='padding: 5px;'>NOMBRE_DOCUMENTO</td>
                    <td style='padding: 5px;'>FECHA_INICIAL</td>
                    <td style='padding: 5px;'>FECHA_FINAL</td>
                    <td style='padding: 5px;'>CAJA_PAQUETE_TOMO_OTRO</td>
                    <td style='padding: 5px;'>NUMERO_CAJA_PAQUETE</td>
                    <td style='padding: 5px;'>NUMERO_CARPETA</td>
                    <td style='padding: 5px;'>DESDE</td>
                    <td style='padding: 5px;'>HASTA</td>
                    <td style='padding: 5px;'>DESCRIPTOR</td>
                    <td style='padding: 5px;'>TOTAL_FOLIOS</td>
                    <td style='padding: 5px;'>NUMERO_CAJA_ARCHIVO_CENTRAL</td>
                    <td style='padding: 5px;'>OBSERVACIONES</td>
                </tr>";
            }   // Fin del caso donde el encabezado es correcto
        }    
/* Fin donde se define el encabezado de la tabla validando la fila numero 1 determinando la variable $encabezado_tabla */

    /* Quinta validacion : Se inicia la validacion de los datos de la tabla despues del encabezado */
        if($i>=2){              // Inicia la validacion desde la tercera fila. (0,1,2)
            $j=$i+1;            // Agrega uno al contador para orientar al usuario en el numero de fila en el que se encuentra el error ya que se inicia el conteo en cero.
            
            if($i%2==0){
                $fila="fila2";  //  Si el resto de la división es "0" pongo un color (estilo de la tabla)
            }else{ 
                $fila="fila1";  //  Si el resto de la división NO es "0" pongo otro color (estilo de la tabla)
            } 

            // $cantidad_columnas=sizeof($datos); // Cantidad de columnas del FUID
    /* Sexta validacion : Si la cantidad de columnas es mayor a 16 quiere decir que hay columnas adicionales que pueden generar errores por lo que se asigna a la variable $boton_enviar el div especificando el error. */
            if($cantidad_columnas>16){  
                $boton_enviar="<div class='error'>Operacion interrumpida.<h2> En la fila numero $j</h2> de su archivo CSV, hay un caracter inválido <h2>( <b>;</b> )</h2>ó<br> hay una columna adicional. <br>Revise y cargue el archivo CSV nuevamente.</div>"; 
                break;
            }else{
                $validar_fila_final=$numero_consecutivo.$codigo_dependencia.$codigo_serie.$codigo_subserie.$nombre_documento.$fecha_inicial.$fecha_final.$caja_paquete_tomo_otro.$numero_caja_paquete.$numero_carpeta.$desde.$hasta.$descriptor.$total_folios.$numero_caja_archivo_central.$observaciones;

                if($validar_fila_final!=""){    // Validar si la fila no está completamente vacía. Por lo que devuelve el boton para enviar.
                    $fila_vacia     ="false";
                    $total_registros=$i-1;
                    $boton_enviar   ="<tr><td colspan='6' align='center'><input type='button' value='Enviar $total_registros registros a la base de datos' onclick='form_inventario()' class='boton'></td></tr>";   
                }else{                          // Validar si la fila SI está completamente vacía 
                    $fila_vacia     ="true";
                    break;       
                }   // Fin validacion si la fila no está completamente vacía.   
            }   // Fin de la validacion de la cantidad de columnas para evitar errores

            if($fila_vacia=="false"){   // Si la fila no está completamente vacía (Si tiene datos)
        /* Inicio de validación por cada una de las filas */
            /* Campo numero_consecutivo */ 
                if($i==2){                  // Se inicia la variable del consecutivo anterior
                    $consecutivo_anterior=$numero_consecutivo+1;
                }
                
                $validar_numero_consecutivo1= validar_numero_consecutivo($consecutivo_anterior, $numero_consecutivo, $j, $conectado, $query_alter_sequence_inicial);

                $consecutivo_anterior+=1;   // Se incrementa el consecutivo anterior

                /* Se inicia con la variable para imprimir la tabla $cuerpo_tabla */
                if($validar_numero_consecutivo1!="false"){ // Si el numero de consecutivo no existe
                    $boton_enviar=$validar_numero_consecutivo1;
                    $cuerpo_tabla.= "<tr class='$fila'><td class='error'>$validar_numero_consecutivo1</td>";
                    break; 
                }else{
                    $cuerpo_tabla.= "<tr class='$fila'><td class='encabezado'>$numero_consecutivo</td>";
                }

            /* Fin campo numero_consecutivo */ 
            /* Campo codigo_dependencia */
                $caracteres_dependencia = $_SESSION['caracteres_depend'];
                $validar_codigo_dependencia1 = validar_codigo_dependencia($codigo_dependencia,$j,$query_alter_sequence_inicial,$conectado);
                if($validar_codigo_dependencia1!="false"){ 
                    $boton_enviar=$validar_codigo_dependencia1;
                    $cuerpo_tabla.="<td class='error'>$validar_codigo_dependencia1</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$codigo_dependencia</td>";
                }

            /* Fin campo codigo_dependencia */

            /* Campo codigo_serie */
                $codigo_serie1= str_pad($codigo_serie, 3, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 3=longitud
 
                $validar_codigo_serie=validar_codigo_serie($codigo_serie1,$j,$query_alter_sequence_inicial,$conectado);
                // $validar_codigo_serie=validar_largo_campo($codigo_serie,3,"CODIGO_SERIE",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_codigo_serie!="false"){
                    $boton_enviar=$validar_codigo_serie;
                    $cuerpo_tabla.="<td class='error'>$validar_codigo_serie</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$codigo_serie1</td>";
                }
            /* Fin campo codigo_serie */
            /* Campo codigo_subserie */
                $codigo_subserie1= str_pad($codigo_subserie, 3, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 3=longitud
                $validar_codigo_subserie=validar_codigo_subserie($codigo_serie1,$codigo_subserie1,$j,$query_alter_sequence_inicial,$conectado);
                if($validar_codigo_subserie!="false"){
                    $boton_enviar=$validar_codigo_subserie;
                    $cuerpo_tabla.="<td class='error'>$validar_codigo_subserie</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$codigo_subserie1</td>";
                }
            /* Fin campo codigo_subserie */
            /* Campo nombre_documento */
                if($nombre_documento==""){      // Validar si la fila tiene nombre del documento vacío
                    pg_query($conectado,$query_alter_sequence_inicial); // Si se reinicia el consecutivo
                    $boton_enviar="<div class='error'>La fila numero <b>$j</b> de su archivo CSV, no tiene nombre del documento. Revise por favor.</div>"; 
                    $cuerpo_tabla.="<td class='error'><div class='error'>La fila numero <b>$j</b> de su archivo CSV, no tiene nombre del documento. Revise por favor.</div></td>";
                    break;
                }else{
                    $validar_nombre_documento=validar_largo_campo($nombre_documento,500,"NOMBRE_DOCUMENTO",$j,$query_alter_sequence_inicial,$conectado);
                    if($validar_nombre_documento!="false"){
                        $boton_enviar=$validar_nombre_documento;
                        $cuerpo_tabla.="<td class='error'>$validar_nombre_documento</td>";
                        break;
                    }else{
                        $cuerpo_tabla.= "<td class='encabezado'>$nombre_documento</td>";
                    }
                }
            /* Fin campo nombre_documento */
            /* Campo fecha_inicial */
                $validar_fecha_inicial=validar_fecha($fecha_inicial,"",10,"FECHA_INICIAL",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_fecha_inicial!="false"){
                    $boton_enviar =$validar_fecha_inicial;
                    $cuerpo_tabla.="<td class='error'>$validar_fecha_inicial</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$fecha_inicial</td>";
                }
            /* Fin campo fecha_inicial */
            /* Campo fecha_final */
                $validar_fecha_final=validar_fecha($fecha_final,$fecha_inicial,10,"FECHA_FINAL",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_fecha_final!="false"){
                    $boton_enviar = $validar_fecha_final;
                    $cuerpo_tabla.= "<td class='error'>$validar_fecha_final</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$fecha_inicial</td>";
                }
            /* Fin campo fecha_final */
            /* Campo caja_paquete_tomo_otro */
                $validar_caja_paquete_tomo=validar_largo_campo($caja_paquete_tomo_otro,10,"CAJA_PAQUETE_TOMO_OTRO",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_caja_paquete_tomo!="false"){
                    $boton_enviar=$validar_caja_paquete_tomo;
                    $cuerpo_tabla.= "<td class='error'>$validar_caja_paquete_tomo</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$caja_paquete_tomo_otro</td>";
                }
            /* Fin campo caja_paquete_tomo_otro */
            /* Campo numero_caja_paquete */
                $validar_numero_caja_paquete=validar_largo_campo($numero_caja_paquete,10,"NUMERO_CAJA_PAQUETE",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_numero_caja_paquete!="false"){
                    $boton_enviar=$validar_numero_caja_paquete;
                    $cuerpo_tabla.= "<td class='error'>$validar_numero_caja_paquete</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$numero_caja_paquete</td>";
                }
            /* Fin campo numero_caja_paquete */
            /* Campo numero_carpeta */
                $validar_numero_carpeta=validar_largo_campo($numero_carpeta,20,"NUMERO_CARPETA",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_numero_carpeta!="false"){
                    $boton_enviar=$validar_numero_carpeta;
                    $cuerpo_tabla.= "<td class='error'>$validar_numero_carpeta</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$numero_carpeta</td>";
                }
            /* Fin campo numero_carpeta */
            /* Campo desde */
                $validar_consecutivo_desde=validar_largo_campo($desde,20,"CONSECUTIVO_DESDE",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_consecutivo_desde!="false"){
                    $boton_enviar=$validar_consecutivo_desde;
                    $cuerpo_tabla.= "<td class='error'>$validar_consecutivo_desde</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$desde</td>";
                }
            /* Fin campo desde */
            /* Campo hasta */
                $validar_consecutivo_hasta=validar_largo_campo($hasta,20,"CONSECUTIVO_HASTA",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_consecutivo_hasta!="false"){
                    $boton_enviar = $validar_consecutivo_hasta;
                    $cuerpo_tabla.= "<td class='error'>$validar_consecutivo_hasta</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$hasta</td>";
                }
            /* Fin campo hasta */
            /* Campo descriptor */
                $validar_descriptor=validar_largo_campo($descriptor,500,"DESCRIPTOR",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_descriptor!="false"){
                    $boton_enviar=$validar_descriptor;
                    $cuerpo_tabla.= "<td class='error'>$validar_descriptor</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$descriptor</td>";
                }
            /* Fin campo descriptor */
            /* Campo total_folios */
                $validar_folios=validar_largo_campo($total_folios,5,"TOTAL_FOLIOS",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_folios!="false"){
                    $boton_enviar=$validar_folios;
                    $cuerpo_tabla.= "<td class='error'>$validar_folios</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$total_folios</td>";
                }
            /* Fin campo total_folios */
            /* Campo numero_caja_archivo_central */
                $validar_numero_caja_arch=validar_largo_campo($numero_caja_archivo_central,20,"NUMERO_CAJA_ARCHIVO_CENTRAL",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_numero_caja_arch!="false"){
                    $boton_enviar=$validar_numero_caja_arch;
                    $cuerpo_tabla.= "<td class='error'>$validar_numero_caja_arch</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$numero_caja_archivo_central</td>";
                }
            /* Fin campo numero_caja_archivo_central */
            /* Campo observaciones */
                $validar_observaciones=validar_largo_campo($observaciones,200,"OBSERVACIONES",$j,$query_alter_sequence_inicial,$conectado);
                if($validar_observaciones!="false"){
                    $boton_enviar=$validar_observaciones;
                    $cuerpo_tabla.= "<td class='error'>$validar_observaciones</td>";
                    break;
                }else{
                    $cuerpo_tabla.= "<td class='encabezado'>$observaciones</td>";
                }
                  $cuerpo_tabla.="</tr>";
            /* Fin campo observaciones */
            }   // Fin si la fila no está completamente vacía (Si tiene datos)
       
/* Fin de la validacion de los datos de la tabla despues del encabezado */
            
                $boton_enviar="<input type='button' value='Enviar $total_registros registros a la base de datos' onclick='form_inventario()' class='botones'>";
                // $boton_enviar="<input type='button' value='Enviar Masiva' id='botones2' class='botones'>";
    /* Inicio armar cuerpo_tabla, insertar_inventario, query_expediente y query_radicado */
                $year       = date("Y"); // Se obtiene el año en formato 4 digitos

    /*** Inicio generar consecutivo completo para secuencia expediente_jonas ***/
                $dependencia_expediente = $codigo_dependencia;
                $nombre_expediente      = $nombre_documento;
                $serie_expediente       = $codigo_serie;
                $subserie_expediente    = $codigo_subserie;

                $serie_expediente1= str_pad($serie_expediente, 3, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 3=longitud
                $subserie_expediente1= str_pad($subserie_expediente, 3, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 3=longitud

            /* Inicio generar consecutivo tabla expediente */   
                $query_max_id_expediente="select max(id) from expedientes";

                $fila_max_id_expediente = pg_query($conectado,$query_max_id_expediente);
                $linea_id_expediente    = pg_fetch_array($fila_max_id_expediente);

                if($linea_id_expediente == false){
                    $max_id_expediente2 = "1";    // Inicia el consecutivo del expediente por año/serie/subserie
                }else{
                    $max_id_expediente  = $linea_id_expediente[0];
                    $max_id_expediente2 = $i+$max_id_expediente-1;  // Continúa con consecutivo por año/serie/subserie es -1 para eliminar las 2 primeras filas del csv despues del $i       
                }
            /* Fin generar consecutivo tabla expediente */  
            /* Inicio generar consecutivo de expediente por año/dependencia/serie/subserie */
                $query_cantidad_expediente = "select count(*) from expedientes where year_expediente='$year' and dependencia_expediente='$dependencia_expediente' and serie='$serie_expediente1' and subserie='$subserie_expediente1'";

                $fila_cantidad_expediente  = pg_query($conectado,$query_cantidad_expediente);
                $linea_cantidad_expediente = pg_fetch_array($fila_cantidad_expediente);
            /* Fin generar consecutivo de expediente por dependencia/serie/subserie */
                $max_expediente2="";
                if($linea_cantidad_expediente=='0'){
                    $max_expediente2="1";   // Inicia el consecutivo del expediente por año/serie/subserie
                }else{
                    if($linea_cantidad_expediente=='0'){
                        $max_expediente2="1";   // Inicia el consecutivo del expediente por año/serie/subserie
                    }else{
                        $max_expediente = $linea_cantidad_expediente[0];
                        
                        if ( !empty ( $dependencia_expediente ) ){   // $valor = "GES"   || "TER" 
                            $position = $dependencia_expediente;
                            if ( isset( $count[$position] ) ){
                                $count[$position]++;
                            } else {
                                // Consulta la base de datos y trae la cantidad 
                                $countDB = $max_expediente+1;
                                $count[$position] = $countDB; // Inicializa el valor con lo que viene de la base de datos 
                            }
                        }
                        $max_expediente2= $count[$dependencia_expediente];  
                    }
                }
            /* Genera el numero de expediente */        
                $max_expediente3= str_pad($max_expediente2, 7, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 7=longitud
                $expediente_jonas="EXP".$year.$dependencia_expediente.$serie_expediente1.$subserie_expediente1.$max_expediente3;
            /* Fin genera el numero de expediente */        

    /*** Fin generar consecutivo completo para secuencia expediente_jonas ***/
    /*** Inicio generar consecutivo completo para secuencia radicado_jonas ***/

            /* Desde aqui genera la secuencia para el radicado_jonas */
                $query_verifica_secuencia_padre = "select * from consecutivos where tipo_radicado='1' and codigo_dependencia='INV' or codigo_dependencia='INVE' or codigo_dependencia='INVEN'"; // Termina en 1 porque son entradas
                $fila_verifica_secuencia_padre  = pg_query($conectado, $query_verifica_secuencia_padre);
                $linea_verifica_secuencia_padre = pg_fetch_array($fila_verifica_secuencia_padre);
                $year_padre                     = $linea_verifica_secuencia_padre['year']; // Verifico año para secuencia si existe
                if($year_padre==$year){ // Si el año para secuencia existe, toma siguiente valor de la secuencia
                    $consecutivo    = pg_query($conectado,"select nextval('secuencia_inventario')");
                    $consecutivo2   = pg_fetch_array($consecutivo);
                    $consecutivo3   = $consecutivo2[0];
                }else{ // Si el año no coincide, reinicia consecutivo de la secuencia e inicia en 1.
                    $query_alter_sequence   = "alter sequence secuencia_inventario restart 1";
                    $query_update_sequence  = "update consecutivos set year='$year' where dependencia_consecutivo_padre='INV' and tipo_radicado='2'";
                    
                    if(pg_query($conectado,$query_alter_sequence)){ // Si se reinicia el consecutivo
                        echo "Se reinicia el consecutivo porque año es nuevo.";
                        
                        if(pg_query($conectado,$query_update_sequence)){    // Si se actualiza el año en la secuencia
                            $secuencia      = "secuencia_inventario";
                            $consecutivo    = pg_query($conectado,"select nextval('secuencia_inventario')");
                            $consecutivo2   = pg_fetch_array($consecutivo);
                            $consecutivo3   = $consecutivo2[0];
                        }else{
                            echo "No se pudo actualizar la secuencia. Comuníquese con el administrador del sistema";
                        }
                    }else{
                        echo "No se pudo reiniciar el consecutivo. Comuníquese con el administrador del sistema";
                    }
                }   
            /* Hasta aqui genera la secuencia para el radicado_jonas */

            /* Genera el radicado */
                $consecutivo4   = str_pad($consecutivo3, 7, '0', STR_PAD_LEFT); // Agrega los ceros a la izquierda 7=longitud
                $radicado       = $year.$codigo_entidad.$codigo_inventario.$consecutivo4."1"; // Arma el numero de radicado
            /* Hasta aqui genera el radicado */

    /*** Fin generar consecutivo completo para secuencia radicado_jonas ***/

    /*Consulta si existe una carpeta personal para inventario. En caso que no exista, la crea; en caso que si exista, asigna los radicados hechos en esta carpeta. */
        /* Verifica si existe carpeta personal para inventario */    
                $consulta_carpeta_inventario        = "select * from carpetas_personales where id_usuario='$id_usuario' and nombre_carpeta_personal='Inventario'";
                $fila_cantidad_carpeta_inventario   = pg_query($conectado,$consulta_carpeta_inventario);

        /* Calcula el numero de registros que genera la consulta anterior. */
                $registros_carpeta_inventario       = pg_num_rows($fila_cantidad_carpeta_inventario);

                if($registros_carpeta_inventario!=0){  // Cuando si existe la carpeta personal 
                    $linea_carpeta_inventario  = pg_fetch_array($fila_cantidad_carpeta_inventario);
                    $codigo_carpeta_inventario = $linea_carpeta_inventario['id'];
                }else{       // Cuando no existe la carpeta personal
                    $query_cantidad_carpeta_per3 = "select count(*) from carpetas_personales";
                    $fila_cantidad3              = pg_query($conectado,$query_cantidad_carpeta_per3); // La variable "$conectado" la hereda desde conexion2.php
                    $linea_cantidad3 = pg_fetch_array($fila_cantidad3);
                    $cantidad_total1 = $linea_cantidad3[0];
                    $cantidad_total  = $cantidad_total1+1;

                    $query_crear_carpeta = "insert into carpetas_personales (id, nombre_carpeta_personal, id_usuario, activo, fecha_creacion_carpeta_per) values('$cantidad_total', 'Inventario', '$id_usuario', 'SI', current_timestamp)";
                    if(pg_query($conectado,$query_crear_carpeta)){ // Si crea la carpeta personal 'Inventario'
                        $codigo_carpeta_inventario=$cantidad_total;
                    }else{
                        echo "No se ha creado la carpeta personal 'Inventario. Por favor comuníquese con el administrador del sistema.'";
                    }
                }
        /* Fin verifica si existe carpeta personal para inventario */    
    /* Fin consulta si existe una carpeta personal para inventario. En caso que no exista, la crea; en caso que si exista, asigna los radicados hechos en esta carpeta. */

                $query_expediente = "insert into expedientes (id, id_expediente, nombre_expediente, serie, subserie, fecha_inicial, creador_expediente, fecha_apertura_exp, year_expediente, dependencia_expediente) values('$max_id_expediente2', '$expediente_jonas', '$nombre_expediente', '$serie_expediente1', '$subserie_expediente1', '$timestamp', 'Masiva', '$timestamp', '$year', '$dependencia_expediente');";
                $query_insertar_inventario = "insert into inventario(numero_consecutivo, codigo_dependencia, nombre_documento, fecha_inicial, fecha_final, caja_paquete_tomo, numero_caja_paquete, numero_carpeta, consecutivo_desde, consecutivo_hasta,descriptor, total_folios, numero_caja_archivo_central, observaciones, fecha_inventario, cargado_por, radicado_jonas, expediente_jonas) values ($numero_consecutivo, '$codigo_dependencia', '$nombre_documento', '$fecha_inicial', '$fecha_final', '$caja_paquete_tomo_otro', '$numero_caja_paquete', '$numero_carpeta', '$desde', '$hasta', '$descriptor', '$total_folios', '$numero_caja_archivo_central', '$observaciones', '$timestamp','$cargado_por', '$radicado','$expediente_jonas');";
                $query_radicado = "insert into radicado (numero_radicado, fecha_radicado, codigo_carpeta, codigo_contacto, dependencia_actual, usuarios_control, dependencia_radicador, usuario_radicador, asunto, nivel_seguridad, leido, id_expediente ) values ('$radicado', '$timestamp', 'Inventario', '1', '$codigo_dependencia_radicador', '$login_usuario', '$codigo_dependencia_radicador', '$login_usuario', 'Documento ingresado por modulo FUID - $nombre_documento', '1', '$leido', '$expediente_jonas');";
                $query_datos_origen_datos_radicado = "insert into datos_origen_radicado (codigo_datos_origen_radicado, numero_radicado, nombre_remitente_destinatario, dignatario, ubicacion, direccion) values ('1', '$radicado', '$nombre_documento', '$descriptor', 'BOGOTA, D.C. (BOGOTA) COLOMBIA-AMERICA', 'Documento ingresado por modulo FUID'); ";
                $query_ubicacion_fisica = "insert into ubicacion_fisica (numero_radicado, usuario_actual, usuario_anterior, fecha)values('$radicado','$login_usuario','Masiva Inventario', '$timestamp');";
                $query_historico = "insert into historico_eventos(numero_radicado, usuario, transaccion, comentario, fecha) values('$radicado', '$cargado_por', 'Inventario FUID Masivo', 'Documento ingresado por modulo inventario FUID masivo.', '$timestamp');";

            /* Se arma la query concatenando todas las anteriores */   
                $query_total_insertar_expediente.=$query_expediente;
                $query_total_insertar_inventario.=$query_insertar_inventario;
                $query_total_insertar_radicado.=$query_radicado;
                $query_total_datos_origen_radicado.=$query_datos_origen_datos_radicado;
                $query_total_ubicacion_fisica.=$query_ubicacion_fisica;
                $query_total_historico.=$query_historico;

    /* Fin armar cuerpo_tabla, insertar_inventario, query_expediente y query_radicado */ 
            
        }   // Fin de la validacion desde la tercera fila. (0,1,2)    
    }   // Fin del ciclo FOR que recorre uno a uno TODAS las filas del CSV  
}       // Fin condicion si el archivo pesa menos de 8Mb
// var_dump($boton_enviar);
 
echo "
<table border='0' width='100%'>
    $encabezado_tabla
    $cuerpo_tabla
    <tr>
        <td colspan='10' id='boton_insertar_inventario'>
            <center>
                <input type='button' class='botones' value='Volver' onclick='carga_index_masiva_inventario()' class='boton'>
                $boton_enviar
            </center>
        </td>
    </tr>
</table>
<div id='resultado_query'></div>
<textarea id='input_query_expediente' rows='2' class='input_query'>
    $query_total_insertar_expediente
</textarea>
<textarea id='input_query_inventario' rows='2' class='input_query'>
    $query_total_insertar_inventario
</textarea>
<textarea id='input_query_radicado' rows='2' class='input_query'>
    $query_total_insertar_radicado
</textarea>
<textarea id='input_query_datos_origen_radicado' rows='2' class='input_query'>
    $query_total_datos_origen_radicado
</textarea>
<textarea id='input_query_historico' rows='2' class='input_query'>
    $query_total_historico
</textarea>
";
/* Se crea archivo para procesar consultas armadas anteriormente porque la query es muy larga y al ejecutarla como sql por tiempo de ejecución no permite hacerlo. */
$nombre_archivo = "query_radicado.txt"; 
if(file_exists($nombre_archivo)){
    unlink($nombre_archivo);
}
$consecutivo5 = $consecutivo3+1;     // Se define consecutivo a seguir luego de insertar radicado
$query_alter_sequence_total="alter sequence secuencia_inventario restart $consecutivo5";

$query_total=$query_total_insertar_inventario.$query_total_insertar_radicado.$query_total_insertar_expediente.$query_total_datos_origen_radicado.$query_total_ubicacion_fisica.$query_total_historico.$query_alter_sequence_total;

if($archivo = fopen($nombre_archivo, "a")){
    if(fwrite($archivo, $query_total)){
        pg_query($conectado,$query_alter_sequence_inicial); // Si se reinicia el consecutivo    
    }else{
        echo "Ha habido un problema al crear el archivo";
    }
    fclose($archivo);
}
/* Fin creacion de archivo para procesar consultas armadas anteriormente. */
?>
</body>
</html>