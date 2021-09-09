<?php 
if(!isset($_SESSION)){
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<!--Esta es la parte que se visualiza en la pestaña del navegador-->
    <div id="title_img">
        <link rel="shortcut icon" href="imagenes/logo3.png">
        <title>Jonas SGD Principal</title>
    </div>    

<!--Esta es el href a los archivos que necesito para usar jquery, css-->
    <link rel="stylesheet" href="include/css/estilos_menu_principal.css">
    <script type="text/javascript" src="include/js/jquery.js"></script>
    <script type="text/javascript" src="include/js/funciones_menu.js"></script>

    <!-- Se cargan librerías para organigramas usados en admin_depe/index_dependencias.php y en admin_ubicacion_topografica/index_ubicacion_topografica.php inicialmente -->
    <script type="text/javascript" src="include/js/organigramas.js"></script>
    <script type="text/javascript">google.charts.load('current', {packages:["orgchart"]});</script>

    <!-- Se cargan librerías para sweet alert -->
    <script src="include/js/sweetalert2.js"></script>

    <!-- Se cargan librerías para ver PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.0.943/pdf.min.js"></script>
    <script type="text/javascript" src="include/js/tinymce.min.js"></script>
</head>
<?php 
if(!isset($_SESSION['perfil'])){
?>
    <script type="text/javascript">
        Swal.fire({ 
            position            : 'top-end',
            showConfirmButton   : false,
            timer               : 1500, 
            title               : 'Por su seguridad, su sesión ha sido caducada',
            text                : 'Por favor ingrese nuevamente',
            type                : 'error'
        }).then(function(isConfirm){
            self.location="index.php";
        })
    </script>
<?php
}else{
    require_once("login/validar_inactividad.php");  
?>

<body style ="margin: 0px" onload="Javascript:history.go(1);" onunload="Javascript:history.go(1);">
<?php


    /***************************************************************************************************
        Funcion para pintar los cuadros de reportes que aparecen en la primera visa a Jonas.
    ****************************************************************************************************

        * @brief Recibe datos y devuelve el cuadro en formato establecido.
        * @param {string} ($clase_color_cuadro) El nombre de la clase que determina el color del fondo del cuadro.
        ** Se debe crear en el archivo (include/css/estilos_menu_principal.css) este mismo nombre con el color elegido
        * @param {string} ($nombre_funcion) Es el nombre de la función que se invoca al dar click sobre este cuadro,
        ** Puede ser una funcion con o sin parametros
        * @param {string} ($cantidad) Es el numero que aparece como cantidad del pendiente 
        ** (Ejemplo: 45 documentos sin PDF)
        * @param {string} ($comentario) Es la descripción que aparece del pendiente (Ejemplo: Documentos por aprobar) 
        * @param {string} ($modulo) Es el nombre del módulo del pendiente (Ejemplo: Modulo Firma Electrónica) 
        * @param {string} ($icono) Es el nombre del icono ubicado en /imagenes/iconos/$icono.png que va a aparecer en
        ** el cuadro de reporte. (Ejemplo: radicado_pendiente) 
        * @return {string} Retorna el cuadro de reporte respectivo.
    ***************************************************************************************************/
    function mostrar_cuadro_pendientes($clase_color_cuadro,$nombre_funcion,$cantidad,$comentario, $modulo,$icono){
        $cuadro_mostrar = "
            <div class='$clase_color_cuadro boton_reporte' onclick = \"$nombre_funcion\">
                <table border='0' style='color:#FFFFFF'>
                    <tr style='height:50%'>
                        <td style='width:50%'>
                            <img src='imagenes/iconos/$icono.png' style='height:60px;'>
                        </td>
                        <td style='font-size: 250%; width:50%'>
                            $cantidad
                        </td>
                    </tr>
                    <tr style='height:50%'>
                        <td colspan='2' style='text-align:justify;'>
                            $comentario <br>
                            <center style='font-size:19px; margin-top:5px;'>
                                <b>($modulo)</b>
                            </center>
                        </td>
                    </tr>
                </table>    
            </div>
        ";                           
        return $cuadro_mostrar;
    }

    // var_dump($_SESSION);

    /* Define la entidad para mostrar los modulos correspondientes. Se hace para hacer las exepciones con Oracle de Ejercito ($codigo_entidad = 'EJC') */
    $codigo_entidad = $_SESSION['codigo_entidad'];
    // var_dump($_SESSION);

    $year  = date("Y");
    $month = date("m");

    switch ($month) {
        case '01':
            $mes = "ENERO";
            break;
        case '02':
            $mes = "FEBRERO";
            break;
        case '03':
            $mes = "MARZO";
            break;
        case '04':
            $mes = "ABRIL";
            break;
        case '05':
            $mes = "MAYO";
            break;
        case '06':
            $mes = "JUNIO";
            break;
        case '07':
            $mes = "JULIO";
            break;
        case '08':
            $mes = "AGOSTO";
            break;
        case '09':
            $mes = "SEPTIEMBRE";
            break;
        case '10':
            $mes = "OCTUBRE";
            break;
        case '11':
            $mes = "NOVIEMBRE";
            break;
        case '12':
            $mes = "DICIEMBRE";
            break;     
    }
    /* Verifica si existe dentro de la carpeta /bodega_pdf/radicados la carpeta del año y mes
    para poder hacer más fácilmente el backup */
    if(file_exists("bodega_pdf/radicados/$year")) {
        if(!(file_exists("bodega_pdf/radicados/$year/$mes"))){
            mkdir("bodega_pdf/radicados/$year/$mes",0777,true);
            chmod("bodega_pdf/radicados/$year/$mes", 0777); 
        }
    }else{
        /* Crear carpetas de año y mes */
        mkdir("bodega_pdf/radicados/$year/$mes",0777,true);
        chmod("bodega_pdf/radicados/$year/$mes", 0777); 
        chmod("bodega_pdf/radicados/$year", 0777); 
    }

    /* Verifica si existe dentro de la carpeta /bodega_pdf/adjuntos la carpeta del año y mes
    para poder hacer más fácilmente el backup */
    if(file_exists("bodega_pdf/adjuntos/$year")) {
        if(!(file_exists("bodega_pdf/adjuntos/$year/$mes"))){
            mkdir("bodega_pdf/adjuntos/$year/$mes",0777,true);
            chmod("bodega_pdf/adjuntos/$year/$mes", 0777); 
        }
    }else{
        /* Crear carpetas de año y mes */
        mkdir("bodega_pdf/adjuntos/$year/$mes",0777,true);
        chmod("bodega_pdf/adjuntos/$year/$mes", 0777); 
        chmod("bodega_pdf/adjuntos/$year", 0777); 
    }
?>
<!-- <script type="text/javascript" src="include/js/funciones_verificar_radicado_sin_terminar.js"></script> -->
    <div id="contenedor_general">

<!--Inicio del encabezado de principal3.php-->      
        <div id="encabezado">
            <div class="boton_menu">
                <a href="#" class="bt-menu"><span class ="icon-menu"> Menu</span></a>
            </div>
            <div class="boton_menu_lateral">
                <a href="#" class="bt-menu"><span class ="icon-menu"> Menu Lateral</span></a>
            </div>
            <nav class="menu_superior">
                <ul style="margin:0px">
                    <li id="menu_superior" title="Menu Principal">
                        <a href="#"><span><img src="imagenes/iconos/menu.png" style="width:35px;"></span></a>
                        <ul id="boton_menu" class="children">
<?php
                            if ($administrador_sistema=='SI'){ // Permisos del perfil "Administrador del sistema" 
?>
                                <li id="audit_sistema" onclick="carga_auditoria_sistema()">
                                    <a href="#">
                                        <span><img src="imagenes/iconos/configuracion.png" style="width:18px;"></span> Auditoría del sistema
                                    </a>
                                </li>
                                <li id="admin_municipios" onclick="carga_administrador_municipios()">
                                    <a href="#">
                                        <span><img src="imagenes/iconos/globo.png" style="width:18px;"></span> Configuración Países-Departamentos-Municipios
                                    </a>
                                </li>
                                <li id="admin_dependencias" onclick="carga_administrador_dependencias()" >
                                    <a href="#">
                                        <span><img src="imagenes/iconos/diagrama_flujo.png" style="width:18px;"></span> Dependencias
                                    </a>
                                </li>
<?php
                            }   
?>
                            <li id="manual_funcional" onclick="window.open('http://www.gammacorp.co/manual_jonas.html', '_blank');">
                                <a href="#">
                                    <div id="nombre_normatividad">
                                        <span><img src="imagenes/iconos/configuracion.png" style="width:18px;"></span> Manual Oficial Jonas
                                    </div>
                                </a>
                            </li>
                            <li id="normatividad" onclick="carga_administrador_normatividad('general')">
                                <a href="#">
                                    <div id="nombre_normatividad">
                                        <span><img src="imagenes/iconos/legal.png" style="width:18px;"></span> Módulo Normatividad
                                    </div>
                                </a>
                            </li>
<?php           
                            if ($administrador_sistema=='SI'){ // Permisos del perfil "Administrador del sistema" 
?>      
                                <li id="admin_parametrizacion" onclick="carga_administrador_parametrizacion()" >
                                    <a href="#">
                                        <span>
                                            <img src="imagenes/iconos/configuracion.png" style="width:18px;">
                                        </span> Parametrización
                                    </a>
                                </li>
                                <li id="admin_usuarios" onclick="carga_administrador_usuarios()">
                                    <a href="#">
                                        <span>
                                            <img src="imagenes/iconos/usuarios.png" style="width:18px;">
                                        </span> Usuarios y Perfiles
                                    </a>
                                </li>               
                                <!--
                                <li><a href="#"><span class="icon-tools"></span> Configuración de Envíos</a></li>
                                <li><a href="#"><span class="icon-cogs"></span> Configuración de Devoluciones</a></li>
                                <li><a href="#"><span class="icon-thumbs-up"></span> Generar Paz y Salvo Jonas</a></li>
                                -->
<?php                       }
?>
                        <li onclick="buzon_correo_modulo()">
                            <a href="#">
                                <span><img src="imagenes/iconos/buscar_bd.png" style="width:18px;"></span> Ver Buzon de Correo
                            </a>
                        </li>
                        </ul>
                    </li>

                    <li id="menu_reportes" title="Menu de Reportes">
                        <a href="#">
                            <span>
                                <img src="imagenes/iconos/cargar_inventario.png" style="width:35px;">
                            </span>                                  
                        </a>
                        <ul id="boton_reportes" class="children">                                    
<?php   
                            if ($ventanilla_radicacion=='SI'){ // Solo muestra reporte de radicación si tiene el permiso asignado  
?>
                                <li onclick="carga_reporte_entrega_correspondencia()">
                                    <a href="#">
                                        <span><img src="imagenes/iconos/buscar_bd.png" style="width:18px;"></span> Reporte 1 (Planilla Entrega Radicados Físicos)
                                    </a>
                                </li>
<?php                               
                            }
?>                            
                                <li onclick="carga_reporte2_radicacion_entrada()">
                                    <a href="#">
                                        <span><img src="imagenes/iconos/buscar_bd.png" style="width:18px;"></span> Reporte 2 (Radicados de Entrada Vs Salida)
                                    </a>
                                </li>
<?php
                            if ($jefe_dependencia=="SI" || $usuario == "ADMINISTRADOR" || $ventanilla_radicacion=='SI'){
?>
                                <li onclick="carga_reporte3_radicados_vacios()">
                                    <a href="#">
                                        <span><img src="imagenes/iconos/buscar_bd.png" style="width:18px;"></span> Reporte 3 (Reporte Radicados Vacíos)
                                    </a>
                                </li>    
<?php
                            }
?>
                        </ul>
                    </li>
                    <li id="menu_alertas" title="Menu Alertas">
                        <a href="#">
                            <span><img src="imagenes/iconos/campana.png" style="width:35px;">
                                <span id="contador_alertas">
                                    <?php echo $cont_alertas ?>
                                </span>
                            </span>
                        </a>
                        <ul id="boton_alertas" class="children">
                            <?php echo $mostrar_alertas ?>
                        </ul>
                    <!-- Hasta aqui muestra desplegable solicitudes de documentos hechas pero que no han entregado al usuario actual  -->
                    </li>
    <!-- Inicio menu usuario -->                
                    <div id="derecha">
                        <div id="circulo" title='Menu gestion usuario'>
                            <?php
                                echo '<img src="'.$imagen.'" id="foto_usuario">';
                            ?>
                        </div>
                    </div>
                    <div id="contenedor_toptil">
                        <?php
                            echo "<center><h1><b>$nombre_completo<br>( $usuario ) <br>[ $codigo_dependencia - $nombre_dependencia ]</b></h1></center>";           
                        ?>
                    <div>
                        <li id="cambiar_pass" onclick="gestionar_datos_usuario()">
                            <a href="#">
                                <span>
                                    <img src="imagenes/iconos/icono_user.png" style="width:18px;">
                                </span> Gestionar Datos del Usuario
                            </a>
                        </li>
                        <li id="cambiar_pass" onclick="cambiar_contrasena()">
                            <a href="#">
                                <span>
                                    <img src="imagenes/iconos/cambio_password.png" style="width:18px;">
                                </span> Cambiar Contraseña
                            </a>
                        </li>
                        <li id="destroy" onclick="destruir_sesion()">
                            <a href="#">
                                <span>
                                    <img src="imagenes/iconos/cerrar.png" style="width:18px;">
                                </span> Cerrar Sesion
                            </a>
                        </li>                   
                    </div>
    <!-- Fin menu usuario -->
                </ul>
            </nav>
        </div>  
<!--Fin del encabezado de principal3.php-->

<!--Inicio del menu_izquierda de principal3.php-->
        <div id="menu_izquierda">
            <div id="menu_left">
                <nav class="menu_lat">
                    <div>
<?php       
                    if($ventanilla_radicacion=='SI' || $radicacion_salida=='SI' || $radicacion_normal=='SI' || $radicacion_interna=='SI' || $radicacion_resoluciones=='SI'){
 ?>                        
                        <ul>
                            <li id="menu_radicacion" title='Menu redactar radicacion rapida, radicacion de entrada, salida, normal e interna'>
                                <a href="#">
                                    <span>
                                        <img src="imagenes/iconos/new_file.png" style="width:18px;">
                                    </span> Redactar
                                    <span>
                                        <img src="imagenes/iconos/flecha_abajo.png" style="padding-left:63px;  width:18px;">
                                    </span>
                                </a>
                                <ul id="boton_rad" class="children">
<?php
                                    if ($ventanilla_radicacion=='SI'){ // Solo muestra Radicacion de Entrada si tiene perfil asignado  ?>   
                                        <li onclick ="carga_radicacion_rapida()">
                                            <a href="#">
                                                <span>
                                                    <img src="imagenes/iconos/entrada.png" style="width:18px;">
                                                </span> Radicación Rápida
                                            </a>
                                        </li>
                                        <li onclick ="carga_radicacion_entrada()">
                                            <a href="#">
                                                <span>
                                                    <img src="imagenes/iconos/entrada.png" style="width:18px;">
                                                </span> Radicación de Entrada
                                            </a>
                                        </li>
<?php                               
                                    } 

                                    if ($radicacion_normal=='SI'){ // Solo muestra Radicacion normal si tiene permiso asignado  ?>   
                                        <li onclick ="carga_radicado_normal()">
                                            <a href="#">
                                                <span>
                                                    <img src="imagenes/iconos/editar.png" style="width:18px;">
                                                </span> Radicación Normal
                                            </a>
                                        </li>
<?php                               
                                    } 

                                    if ($radicacion_salida=='SI'){ // Solo muestra Radicacion salida si tiene permiso asignado  ?>   
                            <!--        Antigua Radicacion de Salida. Se modifica.     
                                        <li onclick ="carga_radicacion_salida()">
                                            <a href="#">
                                                <span><img src="imagenes/iconos/salida.png" style="width:18px;"></span> Radicación de Salida
                                            </a>
                                        </li> -->
                                        <li onclick ="carga_radicacion_salida2()">
                                            <a href="#">
                                                <span><img src="imagenes/iconos/salida.png" style="width:18px;"></span> Radicación de Salida
                                            </a>
                                        </li>
<?php                               
                                    } 
                                    
                                    if ($radicacion_interna=='SI'){ // Solo muestra Radicacion interna si tiene permiso asignado  
?>   
                                        <li onclick ="carga_radicacion_interna()">
                                            <a href="#">
                                                <span><img src="imagenes/iconos/salida.png" style="width:18px;"></span> Radicación Interna
                                            </a>
                                        </li>                                                      
<?php                               
                                    } 

                                    if ($radicacion_resoluciones=='SI'){ // Solo muestra Radicacion resoluciones si tiene permiso asignado  
?>
                                        <li onclick ="carga_radicacion_resoluciones()">
                                            <a href="#">
                                                <span><img src="imagenes/iconos/salida.png" style="width:18px;"></span> Radicación Resoluciones
                                            </a>
                                        </li> 
<?php 
                                    }
?>
                                    <!-- Comento desde aqui porque no se ha desarrollado | Johnnatan Rodriguez 
                                        <li><a href="#"><span class="icon-add-to-list"></span> Radicación Masiva</a></li>
                                    -->
                                </ul>
                            </li>
                        </ul> 
<?php 
                    }
?>                    
                        <ul>
<?php 
                            if($codigo_entidad != 'EJC'){
?>
                            <li onclick ="carga_buscador_general()"><a href="#">
                                    <span>
                                        <img src="imagenes/iconos/buscar_bd.png" style="width:18px;">
                                    </span> Buscador General
                                </a>
                            </li>
<?php 
                            } // Fin condicion de if($codigo_entidad != 'EJC') por el tema de Oracleif($codigo_entidad != 'EJC')
                        
                        if($cuadro_clasificacion=='SI' || $creacion_expedientes=='SI' || $modificar_radicado=='SI' || $inventario=='SI' || $ubicacion_topografica=='SI'){
?>                            
                            <li id="permisos_especiales" title="Permisos Especiales">
                                <a href="#">
                                    <span>
                                        <img src="imagenes/iconos/estrella.png" style="width:18px;">
                                    </span> Permisos Espe
                                    <span>
                                        <img src="imagenes/iconos/flecha_abajo.png" style="padding-left:20px; width:18px;">
                                    </span>
                                </a>
                                <ul id="boton_permisos_especiales" class="children">

                            <?php
                              /* Inicia administrador metadatos */
                                if ($cuadro_clasificacion=='SI' && $codigo_entidad !='EJC'){
                            ?>
                                    <li onclick="carga_administrador_metadatos()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/crear_expediente.png" style="width:18px">
                                            </span>Administrar Metadatos
                                        </a>
                                    </li>
                            <?php   
                                }
                                /* Fin administrador metadatos */
 
                                if ($inventario=='SI'){ // Solo muestra pendientes por archivar inventario si tiene el permiso asignado 
                            ?>  
                                    <li onclick="carga_buscador_inventario()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/buscar_bd.png" style="width:18px">
                                            </span>Buscador Inventario
                                        </a>
                                    </li>
                                    <li onclick="carga_index_inventario_individual()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/cargar_inventario.png" style="width:18px">
                                            </span>Cargar Inventario Individual
                                        </a>
                                    </li>
                                    
                                    <?php 
                                    if($codigo_entidad !='EJC'){

                                    ?>
                                    <li onclick="carga_index_masiva_inventario()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/cargar_inventario.png" style="width:18px">
                                            </span>Cargar Inventario Masivo
                                        </a>
                                    </li>
                            <?php
                                    }// Fin de condicion $codigo_entidad !='EJC' por el tema de Oracle
                                }

                                if($creacion_expedientes=='SI'){
                             ?> 
                                    <li onclick="carga_creacion_expedientes()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/crear_expediente.png" style="width:18px">
                                            </span>Crear - Modificar Expediente
                                        </a>
                                    </li>
                            <?php
                                }
                                /* Inicia cuadro clasificacion documental */
                                if ($cuadro_clasificacion=='SI'){
                            ?>
                                    <li onclick="carga_administrador_cuadro_clasificacion_documental()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/crear_expediente.png" style="width:18px">
                                            </span>Cuadro de Clasificación Documental
                                        </a>
                                    </li>
                            <?php   
                                }
                                /* Fin cuadro clasificacion documental */                              
                                if ($modificar_radicado=='SI'){ // Solo muestra Radicacion de Entrada si tiene perfil asignado 
                            ?>  
                                    <li onclick ="carga_modificacion()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/editar.png" style="width:18px;">
                                            </span> Modificar Radicado
                                        </a>
                                    </li>
                            <?php
                                }
 
                                if ($inventario=='SI' && $codigo_entidad !='EJC'){
                            ?>  
                                    <li onclick="carga_rotulos_cajas()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/estantes.png" style="width:18px">
                                            </span>Rotulos Cajas
                                        </a>
                                    </li>
                                    <li onclick="carga_rotulos_carpetas()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/estantes.png" style="width:18px">
                                            </span>Rotulos Carpetas
                                        </a>
                                    </li>                                   
                            <?php
                                }
                                if($ubicacion_topografica=='SI'){
                             ?> 
                                    <li onclick="carga_ubicacion_topografica()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/bodega.png" style="width:18px">
                                            </span>Ubicacion Topográfica
                                        </a>
                                    </li>
                            <?php
                                }
                             ?>     
                                </ul>
                            </li>
<?php  
                        }
?>    
                        </ul>               
                    <?php

                    if ($scanner=='SI'){ // Solo muestra modulo de asociar imagen si tiene perfil asignado  ?>  
                        <ul>    
                            <li id="menu_scanner">
                                <a href="#">
                                    <span>
                                        <img src="imagenes/iconos/cadena.png" style="width:18px;">
                                    </span>Asociar Imagen
                                    <span>
                                        <img src="imagenes/iconos/flecha_abajo.png" style="padding-left:15px;  width:18px;">
                                    </span>
                                </a>        
                                <ul id="boton_sc" class="children">
                                    <li onclick="carga_modulo_scanner()">
                                        <a href="#">
                                            <span>
                                                <img src="imagenes/iconos/new_file.png" style="width:18px;">
                                                <span> Imagen Principal
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                            <!--        
                                    <li>
                                        <a href="#">
                                            <span class="icon-file-add"><span> Asociar Imagen Como Anexo</span>
                                            </span>
                                        </a>
                                    </li>
                            -->     
                                </ul>
                            </li>
                            <!--
                            <li>
                                <a href="#"><span class="icon-email"><span> Envíos</span><span class="caret icon-circle-down"></span></span></a>
                                <ul class="children">
                                    <li><a href="#"><span class="icon-envelope"><span> Envío de Correo</span></span></a></li>
                                    <li><a href="#"><span class="icon-email2"><span> Devoluciones de Correo</span></span></a></li>
                                </ul>
                            </li>
                            Hasta aqui comento porque no se ha desarrollado
                            -->
                        </ul>
<?php               } // Fin permiso "scanner" ?>
                    </div>
<?php 
                    if($codigo_entidad != 'EJC'){ // Por ser Oracle no es posible mostrar las bandejas ni carpetas personales. 
 ?>                    
                    <div id="bandejas" class="bandejas">        
                        <ul>
                            <li onclick ="carga_bandeja_entrada('entrada','general')">
                                <a href="#">
                                    <span> Bandeja de Entrada
                                        <span id="bandeja_entrada"><?php echo "($bandeja_entrada)"; ?></span>
                                    </span>
                                </a>
                            </li>
                            <li onclick ="carga_bandeja_entrada('Salida','general')">
                                <a href="#">
                                    <span> Bandeja de Enviados
                                        <span id="bandeja_salida"><?php echo "($bandeja_salida)"; ?></span>
                                    </span>
                                </a>
                            </li>                            
                        </ul>
                    </div>
                    <div id="carpetas">
                        <ul>
                            <li title="Cantidad de documentos NO LEIDOS en las carpetas personales">
                                <a href="#"><span>
                                    <span id="carpetas_per" onclick="carpetas_personales('mostrar_carpetas')" title="Menu Carpetas Personales"> Carpetas Per</span><span id="contador_total_carpetas_personales" style="margin-left: -7px;margin-right: -5px;"></span>
                                    <span id="bandeja_entrada" onclick="carpetas_personales('menu_crear_carpetas')" title="Crear nueva carpeta personal">
                                        <img src="imagenes/iconos/crear_expediente.png" style="padding-left:5px;  width:20px;">
                                    </span>
                                </a>
                                <ul id="creador_carpeta" class="children" style="width:180px;">
                                    <li style="padding:10px;">
                                        <span>
                                            <input type="text" id="crear_carpeta" placeholder="Crear carpeta personal" style="width:130px;" onkeyup="oculta_errores_carp_per()">
                                        </span>
                                        <span onclick="carpetas_personales('crear_carpeta')" title="Crear nueva carpeta personal">
                                            <img src="imagenes/iconos/checkbox2.png" style="padding-left:5px;  width:18px; height:27px; position:absolute;">
                                        </span>
                                        <div id="error_vacio_carpeta" class="errores" style="background-color:red;">El nombre de la carpeta no puede ser vacío.</div>
                                        <div id="error_minimo_carpeta" class="errores" style="background-color:red;">El nombre de la carpeta no puede tener menos de 4 caracteres.</div>
                                        <div id="error_maximo_carpeta" class="errores" style="background-color:red;">El nombre de la carpeta no puede tener mas de 20 caracteres.</div>
                                        <div id="error_caracteres_carpeta" class="errores" style="background-color:red;">Los nombres de carpeta no pueden tener caraceres especiales, ni tildes.</div>
                                        <div id="error_carpeta_existe" class="errores" style="background-color:red;">El nombre de la carpeta ya existe. Por favor ingrese un nombre válido.</div>
                                    </li>
                                </ul>
                                <ul id="boton_carpetas_personales" class="children" style="width:180px;"></ul>
                            </li>        
                        </ul>
                    </div>
<?php 
                    }// Cierra condicion si $codigo_entidad != 'EJC'
 ?>
                    <div style="background:white" id='div_logo_empresa'>
                        <img src="imagenes/iconos/logo_largo.png" id="logo_empresa">
                    </div>
                </nav>
                <hr>
            </div>
        </div><!--Fin del menu_izquierda de principal3.php-->
<!--Inicio del contenido de principal3.php-->
        <div id="pestanas_principal">
        <input type="hidden" id="caracteres_dependencia" value=<?php echo "$caracteres_depend"; ?>>
        <input type="hidden" name="contador_pestanas" id="contador_pestanas" value="0">
            <ul class="tabs">
                <div id="pestana0"></div>
                <div id="pestana1"></div>
                <div id="pestana2"></div>
                <div id="pestana3"></div>
                <div id="pestana4"></div>
                <div id="pestana5"></div>
                <div id="pestana6"></div>
                <div id="pestana7"></div>
                <div id="pestana8"></div>
                <div id="pestana9"></div>
                <div id="pestana10"></div>
                <div id="pestana11"></div>
                <div id="pestana12"></div>
                <div id="pestana13"></div>
                <div id="pestana14"></div>
                <div id="pestana15"></div>
                <div id="pestana16"></div>
                <div id="pestana17"></div>
                <div id="pestana18"></div>
                <div id="pestana19"></div>
                <div id="pestana20"></div>
            </ul>
        </div>
        <div id="contenido">    
            <div class="">
                <h2 class='center'>Bienvenido</h2><h1 class="center"><?php echo "$nombre_completo ($usuario)"; ?></h1><h3 class="center"><?php print_r($nombre_dependencia); ?> </h3><br>

                <!-- Muestra de colores usados en alertas 
                <div class="pasos_pendientes_configuracion demo_colores" title="pasos_pendientes_configuracion">a</div>
                <div class="usuario_sin_firma demo_colores" title="usuario_sin_firma">b</div>
                <div class="contenedor_ubicacion_fisica_radicado demo_colores" title="contenedor_ubicacion_fisica_radicado">c</div>
                <div class="expedientes_fuid_sin_archivar demo_colores" title="expedientes_fuid_sin_archivar">d</div>
                <div class="pendientes_por_devolucion demo_colores" title="pendientes_por_devolucion">e</div>
                <div class="pendientes_por_prestar demo_colores" title="pendientes_por_prestar">f</div>
                <div class="devoluciones_pendientes demo_colores" title="devoluciones_pendientes">g</div>
                <div class="solicitudes_pendientes demo_colores" title="solicitudes_pendientes">h</div>
                <div class="pendientes_aprobar demo_colores" title="pendientes_aprobar">i</div>
                <div class="radicados_sin_datos demo_colores" title="radicados_sin_datos">j</div>
                <div class="radicados_sin_pdf demo_colores" title="radicados_sin_pdf">k</div>
                <div class="no_leidos_bandeja_entrada demo_colores" title="l">l</div>
                <div class="pendientes_respuesta demo_colores" title="m">m</div>
                <div class="n demo_colores" title="n">n</div>
                <div class="o demo_colores" title="o">o</div>
                <div class="p demo_colores" title="p">p</div>
                <div class="q demo_colores" title="q">q</div>
                <div class="r demo_colores" title="r">r</div>
                <div class="s demo_colores" title="s">s</div>
                <div class="t demo_colores" title="t">t</div>
                <div class="u demo_colores" title="u">u</div>
                <div class="v demo_colores" title="v">v</div>
                <div class="w demo_colores" title="w">w</div>
                <div class="x demo_colores" title="x">x</div>
                <div class="y demo_colores" title="y">y</div>
                <div class="z demo_colores" title="z">z</div>
                 -->
<?php   
                if($administrador_sistema=="SI" and $pasos_faltantes_configuracion!=0){
                    /* Cuadro de pasos pendientes para configurar sistema */
                    echo mostrar_cuadro_pendientes('pasos_pendientes_configuracion', $funcion_auditoria_sistema, $pasos_faltantes_configuracion, $comentario_auditoria_sistema, $modulo_auditoria_sistema, $icono_auditoria_sistema);
                }

                if($cargo_usuario==""){
                    /* Cuadro de Gestionar datos de usuario */
                    echo mostrar_cuadro_pendientes('usuario_sin_firma', $funcion_gdu,'1', $comentario_gdu, $modulo_gdu, $icono_gdu);                         
                }

                /* Cuadro de documentos no leidos en bandeja de entrada */
                if($pendientes_responder!=0){
                    echo mostrar_cuadro_pendientes('pendientes_respuesta', $funcion_pendiente_resp, $pendientes_responder, $comentario_pendiente_resp, $modulo_pendiente_resp, $icono_pendiente_resp); 
                }
                    // function mostrar_cuadro_pendientes($clase_color_cuadro,$nombre_funcion,$cantidad,$comentario, $modulo,$icono){


                /* Cuadro de documentos pendientes por responder */
                if($bandeja_entrada_leidos!=0){
                    echo mostrar_cuadro_pendientes('no_leidos_bandeja_entrada', $funcion_no_leido_be, $bandeja_entrada_leidos, $comentario_no_leido_be, $modulo_no_leido_be, $icono_no_leido_be); 
                }

                if($codigo_entidad != 'EJC'){
                    /* Cuadro de ubicacion física de documentos */
                    echo mostrar_cuadro_pendientes('contenedor_ubicacion_fisica_radicado', $funcion_rf, $cantidad_radicados_fisicos, $comentario_rf, $modulo_rf, $icono_rf);                        
                } // Fin condicion de if($codigo_entidad != 'EJC') por el tema de Oracle

                if ($inventario=='SI'){ // Solo muestra pendientes por archivar inventario si tiene el permiso asignado  
                    if($por_archivar!=0){
                        /* Cuadro de Gestionar datos de usuario */
                        echo mostrar_cuadro_pendientes('expedientes_fuid_sin_archivar', $funcion_por_archivar, $por_archivar, $comentario_por_archivar, $modulo_por_archivar, $icono_por_archivar); 
                    }
                }

                if ($prestamo_documentos=='SI'){ // Solo muestra prestamos si tiene el permiso asignado

                    if($por_devolucion_pendiente_general!=0){ 
                        /* Cuadro de devoluciones pendientes general de prestamos */
                        echo mostrar_cuadro_pendientes('pendientes_por_devolucion', $funcion_dpg, $por_devolucion_pendiente_general, $comentario_dpg, $modulo_dpg, $icono_dpg);
                    }

                    if($solicitud_prestamo_pendientes_general!=0){
                        /* Cuadro de solicitudes de prestamos pendientes hechas por usuarios pendientes por atender */
                        echo mostrar_cuadro_pendientes('pendientes_por_prestar', $funcion_sp, $solicitud_prestamo_pendientes_general, $comentario_sp, $modulo_sp, $icono_sp);
                    }
                }

                if($por_devolucion_pendiente_usuario!=0){
                    /* Cuadro de prestamos pendientes hechas por usted, que no ha devuelto */
                    echo mostrar_cuadro_pendientes('devoluciones_pendientes', $funcion_dpu, $por_devolucion_pendiente_usuario, $comentario_dpu, $modulo_dpu, $icono_dpu);
                }   

                if($por_prestamo_realizada_usuario!=0){ 
                    /* Cuadro de prestamos solicitados por usted, que no le han entregado */
                    echo mostrar_cuadro_pendientes('solicitudes_pendientes', $funcion_ru, $por_prestamo_realizada_usuario, $comentario_ru, $modulo_ru, $icono_ru);
                }                 

                if ($pendientes_aprobar !=0){ 
                        /* Cuadro de radicados pendientes por aprobar */
                        echo mostrar_cuadro_pendientes('pendientes_aprobar', $funcion_pa, $pendientes_aprobar, $comentario_pa, $modulo_pa, $icono_pa);
                }

                if ($ventanilla_radicacion=='SI'){ // Solo muestra Radicacion de Entrada si tiene perfil asignado
                    if($radicados_pendientes_por_info!=0){
                        /* Cuadro de radicados sin datos */
                        echo mostrar_cuadro_pendientes('radicados_sin_datos', $funcion_rpi, $radicados_pendientes_por_info, $comentario_rpi, $modulo_rpi, $icono_rpi);
                    }   

                    if($rad_pendientes_por_pdf!=0){       
                        /* Cuadro de radicados pendientes por PDF */
                        echo mostrar_cuadro_pendientes('radicados_sin_pdf', $funcion_rpp, $rad_pendientes_por_pdf, $comentario_rpp, $modulo_rpp, $icono_rpp);
                    }
                }      

?>
            </div>
    <!--Fin del contenido de principal3.php-->
        </div>
        <!-- <br><br><br><br> -->
        <div id="resultado_js" style="display: none;"></div>
        <div class="footer center">
                <?php   echo "$alerta_licencia <br><b>$version_jonas</b>"; ?>
        </div>
    </div> <!--Fin del contenedor_general-->
<?php

    // Con esta condición se llama a la función getTimeAJAX2 cada 5 segundos para actualizar el div que mostrará la hora y contenido de carpeta asíncrona
    if($scanner=='SI'){                     
    ?>              
        <script type="text/javascript">
            $('#contenido').animate({scrollTop: 0}, 500); // Scroll automatico al contenido
            /* Funcion para recargar cada 5 segundos y mostar hora/min/seg y si hay un nuevo documento en carpeta asíncrona correspondiente para cargar archivos desde carpeta asíncrona */
            function getTimeAJAX2() {
                if($('#archivo_pdf_radicado').is(':visible')){

                }

                if($('#lista_documentos_escaneados').is(':visible')){
                    // Se guarda en una variable el resultado de la consulta AJAX    
                    var time = $.ajax({
                        url         : 'scanner/time.php',   // indicamos la ruta donde se genera la consulta de la carpeta
                       
                        data        : {'accion' : 'verifica_scanner_por_cargar'}, // Se define accion para mostrar imagen
                        dataType    : 'text',               // indicamos que es de tipo texto plano
                        async       : false                 // ponemos el parámetro asyn a falso
                    }).responseText;
                    //actualizamos el div que nos mostrará la hora actual
                    document.getElementById('lista_documentos_escaneados').innerHTML = time;
                }else{
                    clearInterval(this);
                    return false;
                }
            } 
            /* Llamado a funcion cada 5 segundos */
            $(document).ready(function() {   
                setInterval(getTimeAJAX2,5000);
            });
        </script>
<?php       
    }
?>
    <script type="text/javascript">
        // Se calcula el alto de la pantalla desde la que visualiza y da height al div #contenido
        var alto_pantalla   = $(window).height();
        var ancho_pantalla  = $(window).width();

        var screen_size= "<br><center>"+ancho_pantalla+" x "+alto_pantalla+"</center><input id='alto_pantalla' type='hidden' value='"+alto_pantalla+"' placeholder='alto_pantalla'><input id='ancho_pantalla' type='hidden' value='"+ancho_pantalla+"' placeholder='ancho_pantalla'>";

        var height_screen  = alto_pantalla-145;
        $("#contenido").css("height",height_screen+"px")
    </script>
<?php       
    /* Se hace un include a las fechas especiales */
    require_once('include/fechas_especiales.php');

    /* Dependiendo del codigo de la entidad, hace modificaciones a la interfaz */
    switch ($codigo_entidad) {
        case 'AV1':
            echo "<script>
                $('#div_logo_empresa').html(\"<img id='logo_empresa' src='imagenes/logos_entidades/logo_largo_av1.png'>\"+screen_size);
            </script>";
            break;

        case 'EJC':
        case 'EJEC':
            echo "<script>
                $('#title_img').html(\"<title>Ingreso a la actualización del Software de Gestion Documental del Ejercito Nacional</title><link href='imagenes/logos_entidades/imagen_qr_ejc.png' type='image/x-icon' rel='shortcut icon'/>\"); 
                $('#nombre_normatividad').html(\"<span><img src='imagenes/iconos/legal.png' style='width:18px;'></span> Normatividad\"); 

                $('#encabezado').css('background-image','url(\"imagenes/encabezado_principal_ejc.png\")'); 
                $('#div_logo_empresa').html(\"<img id='logo_empresa' src='imagenes/logos_entidades/logo_largo_ejc.png'>\");
                $('.footer').html('<b>Versión (2021 - 01)</b>');
            </script>";
            break;

        case 'L01':
            echo "<script>
                $('#div_logo_empresa').html(\"<img id='logo_empresa' src='imagenes/logos_entidades/logo_largo_l01.png'>\"+screen_size);
            </script>";
            break;

        case 'JBB':
            echo "<script>
                $('#div_logo_empresa').html(\"<img id='logo_empresa' src='imagenes/logos_entidades/logo_largo_jbb.png'>\"+screen_size);
            </script>";
            break;
        
        default:
?>
        <script type="text/javascript">
            $("#div_logo_empresa").html("<img id='logo_empresa' src='imagenes/iconos/logo_largo.png'>"+screen_size);
        </script>
<?php
       echo "<script>
            </script>";
            break;
    }
}
?>
</body>
</html>