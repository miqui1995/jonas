<!-- Este archivo es llamado mediante Ajax por el archivo include/js/funciones_expedientes.js[function validar_serie_sub_serie()] -->
<script type="text/javascript">
    
    function espacios_metadato(nombre_input,tipo_conversion,loop){
        var str = $("#" + nombre_input).val();

        if(tipo_conversion='numero'){
            str = str.replace('.', '');
            str = str.replace(',', '');
        }

        str = str.replace('°', '');
        str = str.replace('!', '');
        str = str.replace('<', '');
        str = str.replace('|', '');
        str = str.replace('"', '');
        str = str.replace('$', '');
        str = str.replace('#', '');
        str = str.replace('%', '');
        str = str.replace('&', '');
        str = str.replace('=', '');
        str = str.replace('?', '');
        str = str.replace('¿', '');
        str = str.replace('{', '');
        str = str.replace('}', '');
        str = str.replace('[', '');
        str = str.replace(']', '');
        str = str.replace(';', '');
        str = str.replace('>', '');
        str = str.replace(':', '');
        str = str.replace('_', '');
        str = str.replace('~', '');
        str = str.replace('@', '');
        str = str.replace('´', '');
        str = str.replace("+", '');
        str = str.replace("*", "");
        str = str.replace("'", "");
        str = str.replace('^', '');
        str = str.replace('¡', '');
        str = str.replace('  ', ' ');

        $('#' + nombre_input).val(str.toUpperCase()); // Funcion para poner en mayuscula toda la palabra 

        /* Llama recursivamente esta misma funcion para quitar caracteres especiales */
        if (loop != 5) {
            loop += 1;
            espacios_metadato(nombre_input, tipo_conversion, loop);
        }
    }

    function validar_metadato_texto(nombre_input,tipo_texto){
        $(".errores").slideUp("slow");

        var str = $("#" + nombre_input).val();
       
        if ($(this).data("lastval") != str) {
            $(this).data("lastval", str);
            clearTimeout(timerid);
            timerid = setTimeout(function() {
                espacios_metadato(nombre_input,tipo_texto,1);

                /* Valida si el metadato es numérico */
                if(tipo_texto='numero'){
                    setTimeout(function(){
                        var str1 = $("#" + nombre_input).val();
                        if (isNaN(str1)) {
                            $('#error_' + nombre_input).slideDown('slow');
                            $('#' + nombre_input).focus();
                        } else {
                            $('#error_' + nombre_input).slideUp('slow');
                        }
                    },1000); 
                }else{

                }
                /* Fin valida si el metadato es numérico */
            }, 1000);
        };
    }


   
function sin_errores(){
    $(".errores").slideUp("slow");
}

</script>
<?php 
    require_once('../login/conexion2.php');

    $tipo_consulta = $_POST['tipo_consulta'];

    switch ($tipo_consulta) {
        case 'validar_serie_subserie':
            /* Se recibe codigo de serie y subserie */
            $codigo_serie       = $_POST['codigo_serie'];
            $codigo_subserie    = $_POST['codigo_subserie'];

            $query_metadatos = "select * from metadatos_expedientes where codigo_serie='$codigo_serie' and codigo_subserie='$codigo_subserie' order by tipo_metadato desc, nombre_metadato";

            $fila_query_metadatos           = pg_query($conectado,$query_metadatos); 
            $registros_query_metadatos      = pg_num_rows($fila_query_metadatos);

            if($registros_query_metadatos==0){
                echo "<font color='green' >No hay metadatos de expedientes creados para serie <b>($codigo_serie)</b> - subserie <b>($codigo_subserie)</b></font>";
            }else{
                echo "<input type='hidden' id='cantidad_registros_query_metadatos' value='$registros_query_metadatos'>";
                echo "<div id='tabla_informacion_metadatos' style='float:left; width:99%; overflow:auto;'>
                <table border='0' style='width:100%;'>
                ";
                for ($i=0; $i < $registros_query_metadatos ; $i++){
                    $j = $i+1;

                    $linea_metadatos            = pg_fetch_array($fila_query_metadatos);
                    $nombre_metadato1           = $linea_metadatos['nombre_metadato'];
                    $tipo_metadato              = $linea_metadatos['tipo_metadato'];
                    $tipo_texto                 = $linea_metadatos['tipo_texto'];
                    $campo_obligatorio          = $linea_metadatos['campo_obligatorio'];
                    $requiere_archivo_anexo     = $linea_metadatos['requiere_archivo_anexo'];
                    $tipo_archivo_anexo         = $linea_metadatos['tipo_archivo_anexo'];
                    $opciones_desplegable       = $linea_metadatos['opciones_desplegable'];

                    $nombre_metadato            = str_replace("_", " ", $nombre_metadato1);

                    $id_metadato                = "nombre_metadato_".$j;
                    $id_metadato_obligatorio    = "campo_obligatorio_".$j;
                    $id_tipo_metadato           = "tipo_metadato_".$j;
                    $id_requiere_archivo_anexo  = "requiere_anexo_".$j;

                    echo "
                    <input type='hidden' id='$id_metadato' value='$nombre_metadato1'>
                    <input type='hidden' id='$id_metadato_obligatorio' value='$campo_obligatorio'>
                    <input type='hidden' id='$id_tipo_metadato' value='$tipo_metadato'>
                    <input type='hidden' id='$id_requiere_archivo_anexo' value='$requiere_archivo_anexo'>
                    ";
                    switch ($tipo_archivo_anexo) {
                        case 'PDF':
                            $archivo_admitido = "application/pdf";
                            break;
                    }

                    switch ($tipo_metadato) {
                        case 'anexo':
                            echo "<td class='descripcion'>$nombre_metadato</td>";

                            if($requiere_archivo_anexo=="SI"){
                                $nombre_input_m        = "metadato_$j";
                                $nombre_input_metadato ="file_".str_replace(" ", "_", strtolower($nombre_metadato));
                                $error_input_metadato1 = $nombre_input_m."_tamano";
                                $error_input_metadato2 = $nombre_input_m."_tamano_actual_pdf";
                                $error_input_metadato3 = $nombre_input_m."_invalido";
                                $error_input_metadato4 = $nombre_input_m."_null";
                                echo "                              
                                    <td colspan='2'>
                                        <!-- Para este input file se usa la funcion validar_input_file_animado(nombre_input,iframe_pdf_viewer, div_informacion) definida en include/js/funciones_menu.js -->
                                        <input type='file' onchange='sin_errores();validar_input_file_animado(\"$nombre_input_m\",\"visor_documentos_por_cargar_metadatos\",\"tabla_informacion_metadatos\");' id='$nombre_input_m' accept='$archivo_admitido' >
                                        <div id='$error_input_metadato1' class='errores'>El PDF que intenta cargar excede el limite permitido. Se puede cargar hasta 8Mb y usted intenta cargar un archivo con tamaño de <b><span id='$error_input_metadato2'></span> Mb</b>. Verifique por favor</div>
                                        <div id='$error_input_metadato3' class='errores'>El archivo que intenta cargar no es un $tipo_archivo_anexo. Verifique por favor</div>
                                        <div id='$error_input_metadato4' class='errores'>El campo $nombre_metadato1 es <b>OBLIGATORIO</b>. Verifique por favor</div>
                                    <hr>
                                    </td>
                                </tr>
                                ";
                            }    
                            break;
                        case 'desplegable':
                            $error_1                = "metadato_".$j."_null";
                            echo "
                            <tr>
                                <td class='descripcion'>$nombre_metadato</td>
                                <td class='detalle'>";

                                $usu2  = explode(",", $opciones_desplegable);
                                $max3  = sizeof($usu2);
                                $max4  = $max3-1;
                                
                                echo "<select id='metadato_$j' class='select_opciones' title='Seleccionar $nombre_metadato' onchange='sin_errores()'><option value=''>-- Seleccione una opcion --</option>";

                                for ($r=0; $r < $max3; $r++) { // Quito el usuario_derivado del listado de usuarios_nuevos

                                    $opcion_desplegable_metadato    = $usu2[$r];
                                    $opcion_desplegable_metadato    = str_replace(" ", "_", $opcion_desplegable_metadato);

                                    echo "<option value='$opcion_desplegable_metadato'>$opcion_desplegable_metadato</option>";
                                }
                                echo "</select>
                                <div id='$error_1'  class='errores'>El campo $nombre_metadato1 es <b>OBLIGATORIO</b>. Verifique por favor</div>
                                </td></tr>";

                            break; 
                        
                        case 'fecha': 
                            $error_1                = "metadato_".$j."_null";
                            echo "
                                <td class='descripcion'>$nombre_metadato</td>
                                <td class='detalle'>
                                    <input type='date' id='metadato_$j' title='Ingresar $nombre_metadato' onchange='sin_errores()'>
                                    <div id='$error_1'  class='errores'>El campo $nombre_metadato1 es <b>OBLIGATORIO</b>. Verifique por favor</div>
                                </td>
                            ";
                            break;  
                        
                        case 'texto':
                            // $nombre_metadato        =  str_replace("_", " ", $nombre_metadato);
                            $error_1                = "metadato_".$j."_null";

                            /* Se define si el texto es de tipo numerico o alfanumerico   */
                            switch ($tipo_texto) {
                                case 'numero':
                                    $error_metadato1= "<div id='error_metadato_$j' class='errores'>El metadato $nombre_metadato debe ser numérico. Revise por favor</div>
                                    <div id='$error_1'  class='errores'>El campo $nombre_metadato1 es <b>OBLIGATORIO</b>. Verifique por favor</div>";
                                    break;
                                case 'texto':
                                    $error_metadato1= "<div id='$error_1'  class='errores'>El campo $nombre_metadato1 es <b>OBLIGATORIO</b>. Verifique por favor</div>"; 
                                    break;                                    
                            }

                            echo "
                                <input type='hidden' id='anexo_metadato_$j' value='$requiere_archivo_anexo' >
                                <td class='descripcion'>$nombre_metadato</td>
                                <td class='detalle'>
                                    <input type='search' id='metadato_$j' onkeyup='validar_metadato_texto(\"metadato_$j\")' title='Ingresar $nombre_metadato'>
                                    $error_metadato1
                                </td>
                            ";

                            if($requiere_archivo_anexo=="SI"){
                                $nombre_input_metadato ="file_".str_replace(" ", "_", strtolower($nombre_metadato));
                                $error_input_metadato1 = $nombre_input_metadato."_tamano";
                                $error_input_metadato2 = $nombre_input_metadato."_tamano_actual_pdf";
                                $error_input_metadato3 = $nombre_input_metadato."_invalido";
                                $error_input_metadato4 = $nombre_input_metadato."_null";

                                echo "                              
                                <tr>
                                    <td colspan='2'>
                                        <!-- Para este input file se usa la funcion validar_input_file_animado(nombre_input,iframe_pdf_viewer, div_informacion) definida en include/js/funciones_menu.js -->
                                        <input type='file' onchange='sin_errores();validar_input_file_animado(\"$nombre_input_metadato\",\"visor_documentos_por_cargar_metadatos\",\"tabla_informacion_metadatos\");' id='$nombre_input_metadato' accept='$archivo_admitido' >
                                        <div id='$error_input_metadato1' class='errores'>El PDF que intenta cargar excede el limite permitido. Se puede cargar hasta 8Mb y usted intenta cargar un archivo con tamaño de <b><span id='$error_input_metadato2'></span> Mb</b>. Verifique por favor</div>
                                        <div id='$error_input_metadato3' class='errores'>El archivo que intenta cargar no es un $tipo_archivo_anexo. Verifique por favor</div>
                                        <div id='$error_input_metadato4' class='errores'>El campo $nombre_input_metadato es <b>OBLIGATORIO</b>. Verifique por favor</div>
                                    <hr>
                                    </td>
                                </tr>
                                ";
                            }else{
                                echo "</tr>";
                            }

                            break;    
                    }    
                }    
                echo "</table></div>
                <iframe frameborder='0' id='visor_documentos_por_cargar_metadatos' scrolling='yes' style='height: 0px; background-color: #008080; float: left;'></iframe>
                ";
            }

            break;
        
        case 'tabla_metadatos':
            
 
            break;
    } // Fin switch($tipo_consulta)
?>

    