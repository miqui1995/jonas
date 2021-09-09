<?php 
    require_once('../../login/validar_inactividad.php');// Se valida la inactividad 
?>
<!DOCTYPE html>
<!-- En este archivo se arma la vista previa que se tiene de cada radicado -->
<html>
<head>
    <!-- Se definen los estilos que va a tener la vista previa del radicado  -->
    <style type="text/css">
        body{
            background-color    : #FFFFFF;
        }

        .class {
            margin-top  : 5% !important;
        }

        #header {
            left        : 0;
            position    : fixed;
            right       : 0;
            width       : 100%;
        }
        html{
            margin-bottom   : 2.5cm;
            margin-left     : 2cm;
            margin-right    : 2cm;
            margin-top      : 1.5cm;
        }
        #footer {
            /*bottom      : 10px !important;*/
            left        : 0;
            position    : fixed;
            right       : 0;
            width       : 100%;
        }

        p {     
            margin-top      : -20px;
            margin-bottom:  : 1px;
        }

        .qr-code {
            margin      : 10px;
            max-width   : 200px;
        }

        tr td{
            padding-left: 5px;
        }
    </style>
</head>
<body>

<?php 
    $headerImg  = $_POST['headerImg'];
    $footerImg  = $_POST['footerImg'];

    if(isset($numero_radicado)){
        $numero_radicado = $_POST['numero_radicado'];
    }else{
        $numero_radicado = "No se ha asignado todavia radicado";
    }

/* Desde aqui empiezo a editar la plantilla. */
    $resultado_js               = $_POST['resultado_js'];

    /* Aqui modifico la fecha para que quede con el para mostrar en formato "Jueves 05 de Mayo de 2016"*/
    require_once('../../include/genera_fecha.php');// Se valida la inactividad 
    $fecha_firma  =  date('Y-m-d H:i:s');
    $fecha_firma=$b->traducefecha($fecha_firma);  

    $codigo_entidad = $_SESSION['codigo_entidad'];
    $entidad        = $_SESSION['entidad'];
    $nombre         = $_SESSION['nombre'];

    /* Espacio para firmas en color azul que se muestra para reemplazar */
    $imagen_firma = "<div id='espacio_firmas' style='background-color:#2f8df5; border: 1px solid #ddd; height:170px; font-size:11px; width: 650px;'>Espacio para firmas</div>";
    
    switch ($codigo_entidad) {
        case 'AV1':
             /* Encabezado del comunicado */
            $encabezado_comunicado = "<div align=left style='position: absolute;top: 60px; left: 31%; width:250px; text-align: center;'><h3>*NUMERO_RADICADO_FINAL*</h3></div>
                <div align=left style='position: absolute;top: 60px; left: 70%; width:250px; text-align: center;'><h5>$nombre_dependencia</h5></div>";

            echo "<div id='header' align='center' style='top : -57px;'>
                    <img width='850px' height='150px' class='center' src='$headerImg'>
                    $encabezado_comunicado
                </div>
                <div id='footer' align='center' style ='bottom : 1px !important;'>
                <img width='800px' height='100px' class='center' src='$footerImg'>
                </div>";
            
            $cuerpo_radicado = "
            <div class='class' align='left' style='position:relative; top:70px!important; margin-top: 20px!important;'>
                <p>$resultado_js</p>
                $imagen_firma";
            break;
        
        case 'EJC':
        case 'EJEC':
            /* Encabezado del comunicado */
            $encabezado_comunicado = "<div align=left style='position: absolute;top: 60px; left: 70%; width:250px; text-align: center;'>
            <h5>$nombre_dependencia</h5></div>";

            echo "
            <div id='header' align='center' style='top : -57px;'>
                <img width='850px' height='150px' class='center' src='$headerImg'>
                $encabezado_comunicado
            </div>
            <div id='footer' align='center' style ='bottom : 1px !important;'>
            <img width='800px' height='100px' class='center' src='$footerImg'>
            </div>";
            
            $margin_top = "-50px;";

            $cuerpo_radicado = "
            <div class='class' align='left' style='position:relative; top:80px!important; margin-top: 50px!important;'>
                <p style='display: flex; justify-content: space-between;'>$resultado_js</p>
                $imagen_firma
            <span style='font-weight: bold;'>$firmante_doc</span><br>
            <span style='font-weight: bold;'>$cargo_firmante_doc</span><br><br><br>
            <div style='font-size:8px'>
            <span style='font-weight: bold; margin-right:2px;'>Aprobado por : </span> $aprueba_doc - $cargo_aprueba_doc<br>
            <span style='font-weight: bold; margin-right:2px;'>Elaborado por: </span> $elabora_doc - $cargo_elabora_doc<br>
            ";
        
            break;

        default:
            /* Encabezado del comunicado */
            $encabezado_comunicado = "<div align=left style='position: absolute;top: 60px; left: 70%; width:250px; text-align: center;'>
                <h5></h5></div>
                <div align=left style='position: absolute;top: 5px; left: 26%; width:350px; text-align: center;'><h4>NUMERO DEL RADICADO</h4></div>
                ";

            echo "
            <div id='header' align='center' style='top : -57px;'>
                <img width='815px' height='130px' class='center' src='$headerImg'>
                $encabezado_comunicado
            </div>
            <div id='footer' align='center' style ='bottom : 1px !important;'>
            <img width='815px' height='100px' class='center' src='$footerImg'>
            </div>";
            
            $cuerpo_radicado = "
            <div class='class' align='left' style='position:relative; top:90px!important; margin-top: -30px!important;'>
                <p>$resultado_js</p>
                $imagen_firma";
            break;
    }
  
    echo $cuerpo_radicado;
?>
</div>
</body>
</html>