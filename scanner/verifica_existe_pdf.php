<?php
    if(!isset($_SESSION)){
        session_start();
    }
    require_once('../login/conexion2.php');


    $login = $_SESSION['login'];

    $radicado           = $_POST['radicado'];   // Variable para usar en movimiento de archivo a carpetas
    $accion             = $_POST['accion'];     // De esta variable depende la acción que se va a ejecutar en este archivo.

	$path_por_cargar    = "../bodega_pdf/tmp/TMP_$login/$radicado"; 
    $path_bodega        = "../bodega_pdf/radicados/$radicado"; 

    $archivo_por_cargar             = "bodega_pdf/tmp/TMP_$login/$radicado";     // Este es el archivo que se pre visualiza en "Verificar Scanner"
    $archivo_cargado_previamente    = "bodega_pdf/radicados/$radicado"; // Este es el archivo que se pre visualiza en "Verificar Scanner"

    $mime               = substr($radicado,-3); 
    $numero_radicado    = substr($radicado,0,-4);

    // Funcion para contar las paginas del pdf por cargar
    function numero_paginas_pdf($archivoPDF){
        $stream  = fopen($archivoPDF, "r");
        $content = fread ($stream, filesize($archivoPDF));

        if(!$stream || !$content)
            return 0;

        $count = 0;
        $regex  = "/\/Count\s+(\d+)/";
        $regex2 = "/\/Page\W*(\d+)/";
        $regex3 = "/\/N\s+(\d+)/";

        if(preg_match_all($regex, $content, $matches))
            $count = max($matches);
  
        return $count[0];
    }

    switch ($accion) {
        case 'borrar_pdf':
            $imagen = $_POST['radicado'];

            if(unlink("../bodega_pdf/tmp/TMP_$login/$imagen")){
                echo "pdf_borrado";
            }else{
                echo "Ha ocurrido un error al eliminar la imagen PDF";
            }

            break;

        case 'cargar_archivo_principal':    
            $query_actualizar_radicado = "update radicado set path_radicado='$radicado' where numero_radicado = '$numero_radicado'";
            if(pg_query($conectado,$query_actualizar_radicado)){
                if(rename("$path_por_cargar","$path_bodega")){
                    $radicado               = $numero_radicado;             // Variable para inserta_historico.php
                    $transaccion_historico  = "Asignar imagen principal";   // Variable para inserta_historico.php
                    $comentario             = "Se asigna imagen PDF Principal al radicado $numero_radicado"; // Variable para inserta_historico.php
                    $transaccion            = "imagen_principal";   // Variable para inserta_historico.php
                    $creado                 = $numero_radicado;     // Variable para inserta_historico.php

                    require_once("../login/inserta_historico.php");
                }else{
                   echo "No se pudo cargar el archivo. Verificar con el administrador del sistema";
                }      
            }else{
                echo "<script>alert('No se pudo actualizar radicado error cargar archivo prinicipal. Comuníquese con el administrador del sistema')</script>";
            }
            break;

        /* Este caso es cuando el visor se encuentra en el modulo de asociar imagen */
        /* La funcion de valida_tipo_archivo() no funciona, hay que cambiarla. */
        case 'verifica_bodega':
            $error_no_existe_por_cargar="<br><center><font size='5' color='red'>No ha cargado el documento $radicado en la carpeta de su escaner. Revise por favor</font><hr>
                <form enctype='multipart/form-data' action='radicacion/radicacion_entrada/query_modificar.php' method='POST' id ='formulario_modificar_radicado' name ='formulario_modificar_radicado'>
                    <input type='file' name='archivo_pdf' id='archivo_pdf' onchange='valida_tipo_archivo()'>
                    <div id='error_imagen' class='errores'>No ha elegido un archivo PDF para cargar al radicado</div>
                    <div id='error_archivo_invalido' class='errores'> El formato del archivo que va a ingresar no es válido. El sistema solo admite formato PDF</div>
                    <div id='error_tamano_archivo' class='errores'>El tamaño del archivo es demasiado grande. Por favor utilice otro método para cargar la imagen principal o comuníquese con el administrador del sistema</div>
                </form>
            </center>"; 
        //Fin funcion para contar las paginas del pdf por cargar
            
            $boton_cargar_archivo = "<center><input type='button' class='botones' id='cargar_archivo' name='cargar_archivo' value='Cargar archivo como imagen principal' onclick='cargar_archivo_principal(\"$radicado\")'></center>";

            echo "<hr>
            <table border='1'>
                <tr>
                    <font size='5' color='green'>
                        La imagen $radicado no existe en el sistema.
                    </font>
                </tr>";

            if(file_exists($path_por_cargar)){ // Si ya existe en la carpeta del escaner el .pdf
                $paginas_pdf_por_cargar=numero_paginas_pdf("$path_por_cargar");

                $tamano_pdf_por_cargar_b    = filesize($path_por_cargar);
                $tamano_pdf_por_cargar_kb   = $tamano_pdf_por_cargar_b/1024;
                $tamano_pdf_por_cargar_mb   = $tamano_pdf_por_cargar_kb/1024;

                echo "<tr>
                    <font size='5' color='green'>
                        Por favor verificar si el siguiente PDF es el documento que desea subir.
                    </font>    
                    <br>
                    el PDF tiene $paginas_pdf_por_cargar paginas
                    <br>
                    tamaño $tamano_pdf_por_cargar_mb MB
                    <br>
                    <object data='$archivo_por_cargar' type='application/pdf' width='80%' height='350px'></object>
                </tr>
                <tr>$boton_cargar_archivo</tr>";    
            }else{
                echo "<h2>No ha cargado el documento $radicado en la carpeta de su escaner. Revise por favor</h2>";
            }
            echo "</table><br>";
                      
            break;

        /* Este caso es cuando el visor se encuentra en el modulo de asociar imagen */
        case 'verifica_pdf_por_cargar':
            $numero_radicado    = substr($radicado,0,-4);
            $query_radicado     = "select * from radicado where numero_radicado='$numero_radicado'";

            $fila_query_radicado    = pg_query($conectado,$query_radicado);
            $linea_query_radicado   = pg_fetch_array($fila_query_radicado);
        /* Fin generar consecutivo de expediente por dependencia/serie/subserie */
            if($mime=="PDF" or $mime=="pdf"){
                if($linea_query_radicado=='0'){
                    $asunto_radicado="";   // No hay radicado con este numero
                    echo "<center><h3 style='color:red;'>No existe en la base de datos el radicado $numero_radicado<br>Por favor revise y cambie el nombre del archivo PDF</h3><br><div class='botones2' style='width:35%; background-color:#2D9DC6;' onclick='carga_modulo_scanner()'>Volver a listado de PDF por cargar</div></center>";
                }else{
                    $asunto = $linea_query_radicado['asunto'];
                    $path_radicado = $linea_query_radicado['path_radicado'];
                        echo "$path_radicado";
                //Validar que el pdf no se encuentre en la bodega. Es decir, que el nombre del .pdf no haya sido ingresado todavía.  
                    if(file_exists($path_bodega)){ // Si existe el pdf en la carpeta radicados/xxxx.pdf
                        echo "<hr>
                            <table border='1'>
                                <tr>
                                    <font size='5' color='red'>
                                        La imagen $radicado ya existe en el sistema. Por favor borre o corrija el registro.
                                    </font>
                                </tr>
                            </table>
                                            
                            <table border='0'>
                                <tr>
                                    <td width='50%'>";
                                        if(file_exists($path_por_cargar)){
                                            echo "<h2>Este es el archivo que usted va a cargar</h2><object data='$archivo_por_cargar' type='application/pdf' width='100%' height='250px'></object>"; 
                                            echo "<center><div class='botones2' style='width:35%; background-color:green; color: #FFFFFF; align:left; text-align: center; margin-left: 20px;' onclick='cargar_archivo_principal(\"$radicado\")'>Subir Imagen al Sistema</div></center>";
                                        }else{
                                            echo $error_no_existe_por_cargar;
                                        }
                                    echo "</td>
                                    <td width='50%'>
                                        <h2>Este es el archivo que ya se encuentra en el sistema</h2>
                                        <object data='$archivo_cargado_previamente' type='application/pdf' width='100%' height='250px'></object>
                                        <center>
                                        <input type='button' class='botones2' id='no_cargar_archivo' name='no_cargar_archivo' value='No modificar imagen principal y borrar archivo temporal' onclick='borrar_pdf_temporal(\"$radicado\")' style='text-align: center; margin-left: 20px;'>
                                        </center>
                                    </td>   
                                </tr>
                            </table><br>El asunto del radicado $numero_radicado es <h3>$asunto</h3><center><div class='botones2' style='width:35%; background-color:#2D9DC6;' onclick='carga_modulo_scanner()'>Volver a listado de PDF por cargar</div></center>";                  
                    }else{  // No existe en la carpeta radicados/xxxxx.pdf
                        echo "<h2 style='color:green;'>Este es el archivo que usted va a cargar</h2><object data='$archivo_por_cargar' type='application/pdf' width='100%' height='300px'></object><br>El asunto del radicado $numero_radicado es <h3>$asunto</h3><center style='width:100%;'><div class='botones2' style='width:35%; background-color:green; color: #FFFFFF; float:left; text-align: center; margin-left: 20px;' onclick='cargar_archivo_principal(\"$radicado\")'>Subir Imagen al Sistema</div><div class='botones2' style='width:35%; background-color:#2D9DC6; float:left; text-align: center; margin-left: 20px;' onclick='carga_modulo_scanner()'>Volver a listado de PDF por cargar</div></center>";
                    }
                }
            }else{
                echo "error";
            }   
            break;  
        /* Este caso es cuando el visor se encuentra en el modulo de modificacion de radicado */
        case 'verifica_scanner_por_cargar':
            $login_usuario = $_POST['login_usuario'];

            $path_por_cargar    = "../bodega_pdf/tmp/TMP_$login_usuario/$radicado"; 

            echo "<script>
                    mostrar_ocultar_ancho('mostrar');
                    $('#archivo_pdf_radicado').hide('slow');
                    $('#path_origen_scanner').val('$path_por_cargar');
                    $('#viewer3').html(\"<object data='bodega_pdf/tmp/TMP_$login_usuario/$radicado' width='100%' height='100%'></object>\");
                </script>";

            echo "$path_por_cargar";             
                break;    
        default:
            # code...  
            break;
    }
 ?>