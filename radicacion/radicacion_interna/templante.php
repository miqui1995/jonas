<?php 
    if(!isset($_SESSION)){// Validar que la $_SESSION no este creada 
        session_start();
    }
    /* Recibir las variables enviadas desde include/js/funciones_radicacion_interna.php[visorHtml()] */
    
    $radicado = $_POST['radicado'];
    /*$imgqr = base64_encode(file_get_contents("https://api.qrserver.com/v1/create-qr-code/?data=https://www.gammacorp.co/consultaweb.php?numero_radicado=$radicado%26codigo_entidad=".$_SESSION['dependencia']."%26canal_respuesta=mail&amp;size=100x100"));*/
    $fecha = $_POST['fecha'];
    $tratamiento = $_POST['tratamiento'];
    $destinatario = $_POST['destinatarios'];
    $ubicacion = $_POST['ubicacion'];
    $ubicacion = ucwords(strtolower($ubicacion));
    $ubicacion2 = $_POST['ubicacion2'];
    $ubicacion2 = ucwords(strtolower($ubicacion2));
    $asunto = $_POST['asunto'];
    $editor = $_POST['editor'];
    $despedida = $_POST['despedida'];
    $anexos = $_POST['anexos'];
    if($anexos==""){
        $anexos = "(Sin Anexos)";
    }
    $firmante = $_POST['firmante'];
    $cargo_firmante = "";
    if(isset($_POST['cargo_firmante']))$cargo_firmante = $_POST['cargo_firmante'];
    $aprueba = $_POST['aprueba'];
    $cargo_aprueba = "";
    if(isset($_POST['cargo_aprueba']))$cargo_aprueba = $_POST['cargo_aprueba'];
    $elabora = $_POST['elabora'];
    $cargo_elabora = "";
    if(isset($_POST['cargo_elabora']))$cargo_elabora = $_POST['cargo_elabora'];
    /* Imagenes del cabezado y del pie de pagina */
    $type_encabezado    = pathinfo('../../imagenes/encabezado_radicado.png', PATHINFO_EXTENSION);// Devuelve la extension del fichero como resultado a la información acerca de la ruta especificada 
    $type_piedepagina   = pathinfo('../../imagenes/pie_de_pagina_radicado.png', PATHINFO_EXTENSION);// Devuelve la extension del fichero como resultado a la información acerca de la ruta especificada 
    $data_encabezado    = file_get_contents('../../imagenes/encabezado_radicado.png');// Transmite un fichero completo a una cadena
    $data_piedepagina   = file_get_contents('../../imagenes/pie_de_pagina_radicado.png');// Transmite un fichero completo a una cadena
    $headerImg          = 'data:image/' . $type_encabezado . ';base64,' . base64_encode($data_encabezado);// Codifica datos con MIME base64
    $footerImg          = 'data:image/' . $type_piedepagina . ';base64,' . base64_encode($data_piedepagina);// Codifica datos con MIME base64
    /* Fin imagenes del cabezado y del pie de pagina */
    $fecha_firma = date('Y-m-d H:i:s');
    if(isset($_POST['firmaImg'])){
        $firmaImg   = $_POST['firmaImg'];
        $imagen_firma ="<br><img width='250px' height='100px' class='center' src='$firmaImg'><br><div style='font-size:5px'>Firma mecánica generada en $fecha_firma</div>";
    }else{
        $imagen_firma ='<br><br><br><br><br><br>';
    }



    $codigo_entidad     = $_SESSION['codigo_entidad'];

    switch ($codigo_entidad) {
        case 'AV1':
        case 'EJEC':
            $path_encabezado    = '../../imagenes/logos_entidades/encabezado_rad_av1.png';
            $path_piedepagina   = '../../imagenes/logos_entidades/pie_rad_av1.png';
            break;
        
        default:
            $path_encabezado    = '../../imagenes/encabezado_radicado.png';
            $path_piedepagina   = '../../imagenes/pie_de_pagina_radicado.png';
            break;
    }
    // Extensión de las imagenes de encabezado y pie de pagina para la plantilla
    $type_encabezado    = pathinfo($path_encabezado, PATHINFO_EXTENSION);
    $type_piedepagina   = pathinfo($path_piedepagina, PATHINFO_EXTENSION);
     
    // Cargando las imagenes de encabezado y pie de pagina para la plantilla
    $data_encabezado    = file_get_contents($path_encabezado);
    $data_piedepagina   = file_get_contents($path_piedepagina);
     
    // Decodificando las imagenes de encabezado y pie de pagina en base64
    $base64_encabezado  = 'data:image/' . $type_encabezado . ';base64,' . base64_encode($data_encabezado);
    $base64_piedepagina = 'data:image/' . $type_piedepagina . ';base64,' . base64_encode($data_piedepagina);



    $ubicacion = explode(" - ", $ubicacion);
    $ubicacion = $ubicacion[0].", ".$ubicacion[1];
    echo "<img width='100%' height='100px' src='$base64_encabezado'>
        <div style='margin-left: -210px; margin-top: 40px; position: absolute;'>
            <h6 >".$_SESSION['nombre_dependencia']."</h6>
        </div>
        <h5 align='left' style='margin-top:113px; margin-left:-210px; position: absolute; width:140px'>
            Sin Imagen Qr<br><br> $radicado
        </h5>
    <div style='text-align='justify'>
        <p>
            $ubicacion, $fecha
        </p>
        <p style='font-weight: bold; margin-top:2px'>
            $tratamiento
        </p>
        $destinatario
        <p style='margin-top:20px; font-size:14px'>
            $ubicacion2
        </p>
    </div>
    <p style='font-weight: bold;'>
        Asunto: $asunto
    </p>
    $editor
    <span style='font-weight: bold;'>$despedida</span><br>
    $imagen_firma
    <span style='font-weight: bold;'>$firmante</span><br>
    <span style='font-weight: bold;'>$cargo_firmante</span><br><br><br>
    <div style='font-size:8px'>
    <span style='font-weight: bold; margin-right:28px;'>Anexos :</span> $anexos<br>
    <span style='font-weight: bold; margin-right:2px;'>Aprobado por : </span> $aprueba - $cargo_aprueba<br>
    <span style='font-weight: bold; margin-right:2px;'>Elaborado por: </span> $elabora - $cargo_elabora<br>
    <div id='footer' align='center'>
        <img width='700px' height='80px' class='center' style=\"position:fixed; left:0px; bottom:0px;\" src='$base64_piedepagina'>
    </div>
    ";
?>