<?php 
    session_start();
?>
<!DOCTYPE html>
<!-- En este archivo se arma la vista previa que se tiene de cada radicado -->
<html>
<head>
    <!-- Se definen los estilos que va a tener la vista previa del radicado  -->
    <style type="text/css">
        body{
            background-color : #FFFFFF;
        }
        #header {
            left        : 0;
            position    : fixed;
            right       : 0;
            /*top         : 0;*/
            width       : 100%;
        }

        #footer {
            /*bottom      : 10px !important;*/
            left        : 0;
            position    : fixed;
            right       : 0;
            width       : 100%;
        }

        .class {
            margin-top  : 5% !important;
        }

        html{
            /* margin: 2cm 2cm 2cm 2cm; */
            margin-bottom   : 2.5cm;
            margin-left     : 2cm;
            margin-right    : 2cm;
            margin-top      : 1.5cm;
        }

        .qr-code {
            margin      : 10px;
            max-width   : 200px;
        }

    </style>
</head>
<body>

<?php 
    function b64link_encode($string){
        $string = base64_encode($string);
        $string = urlencode($string);
        return $string ;
    }

    $tipo_vista = $_POST['tipo_vista'];

    //se da valor a imgqr con parametros nulos
    // $imgqr = base64_encode(file_get_contents( "https://api.qrserver.com/v1/create-qr-code/?data=https://www.gammacorp.co/consultaweb.php?numero_radicado=null%26codigo_entidad=null%26canal_respuesta=mail&amp;size=100x100")); 
    $imgqr = "";

 
    $headerImg  = $_POST['headerImg'];
    $footerImg  = $_POST['footerImg'];

    if(isset($numero_radicado)){
        $numero_radicado = $_POST['numero_radicado'];
    }else{
        $numero_radicado = "No se ha asignado todavia radicado";
    }

/* Desde aqui empiezo a editar la plantilla. */
    $fecha                      = $_POST['fecha'];
    $tratamiento                = $_POST['tratamiento'];
    $destinatario               = $_POST['destinatario'];
    $cargo_destinatario         = $_POST['cargo_destinatario'];
    $empresa_destinatario_doc   = $_POST['empresa_destinatario_doc'];
    $direccion_doc              = $_POST['direccion_doc'];
    $telefono_doc               = $_POST['telefono_doc'];
    $ubicacion_doc              = $_POST['ubicacion_doc'];
    $resultado_js               = $_POST['resultado_js'];
    $despedida_doc              = $_POST['despedida_doc'];
    $firmante_doc               = $_POST['firmante_doc'];
    $cargo_firmante_doc         = $_POST['cargo_firmante_doc'];
    $anexos_doc                 = $_POST['anexos_doc'];
    $cc_doc                     = $_POST['cc_doc'];
    $aprueba_doc                = $_POST['aprueba_doc'];
    $cargo_aprueba_doc          = $_POST['cargo_aprueba_doc'];
    $elabora_doc                = $_POST['elabora_doc'];
    $cargo_elabora_doc          = $_POST['cargo_elabora_doc'];
    $asunto_doc                 = $_POST['asunto_doc'];
    $qrcode                     = $_POST["qrcode"];
    $nombre_dependencia         = $_POST["nombre_dependencia"];

    if($cargo_destinatario!=""){
        $cargo_destinatario = "<span>$cargo_destinatario</span><br>";
    }

    if($telefono_doc!=""){
        $telefono_doc = "<span>$telefono_doc</span><br>";
    }

    if($anexos_doc==""){
        $anexos_doc = "(Sin Anexos)";
    }

    if($cc_doc!=""){
        $cc_doc="<span style='font-weight: bold;margin-right:10px;'>Con Copia a :</span> $cc_doc<br>";
    }


    $fecha_firma =  date('Y-m-d H:i:s');
    if(isset($_POST['firmaImg'])){
        $firmaImg   = $_POST['firmaImg'];
        $imagen_firma ="<br><img width='250px' height='100px' class='center' src='$firmaImg'><br><div style='font-size:5px'>Firma mecánica generada en $fecha_firma</div>";
    }else{
        $imagen_firma ='<br><br><br><br><br><br>';
    }

    $codigo_entidad = $_SESSION['codigo_entidad'];

    switch ($codigo_entidad) {
        case 'AV1':
            if($tipo_vista=='media'){ // Vista media
                echo "
                <div id='header' align='center' style='top : 0; left: -100px;'>
                    <img width='850px' height='150px' class='center' src='$headerImg'>
                </div>
                <div id='footer' align='center' style ='bottom : -30px !important;'>
                <img width='700px' height='100px' class='center' src='$footerImg'>
                </div>";

                $margin_top = "80px;";

                /* Encabezado del comunicado */
                echo "<div align=left style='position: absolute; top: 80px; left: 85%'>
                <h6 style='margin-top:2px; margin-left:-10px; text-align: center;'>$nombre_dependencia</h6></div>";

                /* Campo para QR */
                echo "<div align=left style='position: absolute; top: 130px; left: 85%;'>
                <h5 style='margin-top:2px; margin-left:-20px;'>Codigo Qr no disponible-$numero_radicado</h5></div>";

            }else{      // Vista completa
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


                /* Campo para QR */
                echo "<div align=left style='position: absolute; top: 75px; left: 90%'>
                <h5 style='margin-top:2px; margin-left:-20px;'>Codigo Qr no disponible-$numero_radicado</h5></div>";
            }
            $fecha1 = str_replace("Bogotá", "Villeta", $fecha);
            $cuerpo_radicado = "
            <div class='class' align='left' style='position:relative; top:80px!important; margin-top: 50px!important;'>
            <div style='text-align='justify'><p style='margin-top: $margin_top ;position: relative; bottom 50px!important;'>$fecha1</p>
            <span>$tratamiento</span><br>
            <span>$destinatario</span><br>
            $cargo_destinatario
            <span>$empresa_destinatario_doc</span><br>
            <span>$direccion_doc</span><br>
            $telefono_doc
            <span>$ubicacion_doc</span><br><br><br></div>
            <p style='color: black; font-weight: bold;'>Asunto: $asunto_doc</p>
             <p style='display: flex; justify-content: space-between;'>$resultado_js</p>
            <span style='font-weight: bold;'>$despedida_doc</span><br>
            $imagen_firma
            <span style='font-weight: bold;'>$firmante_doc</span><br>
            <span style='font-weight: bold;'>$cargo_firmante_doc</span><br><br><br>
            <div style='font-size:8px'>
            <span style='font-weight: bold; margin-right:28px;'>Anexos :</span> $anexos_doc<br>
            $cc_doc
            <span style='font-weight: bold; margin-right:2px;'>Aprobado por : </span> $aprueba_doc - $cargo_aprueba_doc<br>
            <span style='font-weight: bold; margin-right:2px;'>Elaborado por: </span> $elabora_doc - $cargo_elabora_doc<br>
            ";

            break;
        
        default:
            echo "
            <div id='header' align='center'>
                <img width='650px' height='100px' class='center' src='$headerImg'>
            </div>
            <div id='footer' align='center'>
            <img width='700px' height='100px' class='center' src='$footerImg'>
            </div>
            ";
            $cuerpo_radicado = "
            <div class='class' align='left' style='position:relative; top:5%!important; margin-top: 10%!important;'>
            <div style='text-align='justify'><p style='margin-top:-80px; ;position: relative; bottom 50px!important;'>$fecha</p>
                <span>$tratamiento</span><br>
                <span>$destinatario</span><br>
                $cargo_destinatario
                <span>$empresa_destinatario_doc</span><br>
                <span>$direccion_doc</span><br>
                $telefono_doc
                <span>$ubicacion_doc</span><br><br><br></div>
                <p style='color: black; font-weight: bold;'>Asunto: $asunto_doc</p>
                $resultado_js
                <span style='font-weight: bold;'>$despedida_doc</span><br>
                $imagen_firma
                <span style='font-weight: bold;'>$firmante_doc</span><br>
                <span style='font-weight: bold;'>$cargo_firmante_doc</span><br><br><br>
                <div style='font-size:8px'>
                <span style='font-weight: bold; margin-right:28px;'>Anexos :</span> $anexos_doc<br>
                $cc_doc
                <span style='font-weight: bold; margin-right:2px;'>Aprobado por : </span> $aprueba_doc - $cargo_aprueba_doc<br>
                <span style='font-weight: bold; margin-right:2px;'>Elaborado por: </span> $elabora_doc - $cargo_elabora_doc<br>
            ";

            echo "<div align=left style='position: absolute;top: 5%; left: 85%'>
            <h5 style='margin-top:2px; margin-left:-20px;'>Codigo Qr no disponible-$numero_radicado</h5></div>";
   
            break;
    }
  
    echo $cuerpo_radicado;
?>
</div>
</body>
</html>