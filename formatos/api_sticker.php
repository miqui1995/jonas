<?php 
/* Este es el formato del json que recibe para procesar en este archivo. Si el json no tiene este formato, no va a funcionar la generación de fpdf.


Debe venir en una variable $_POST['json_info_sticker'];
$json_recibido='
{
   "sticker":[
        {
            "anexos"            : "CANTIDAD DE ANEXOS QUE VIENEN CON EL RADIC",
            "asunto"            : "Asunto de pruebas largo para probar la cantidad de caract",
            "destino"           : "UTT2-UNIDAD TERRITORIAL 2",
            "encabezado"        : "Jardín Botánico José Celestino Mutis",
            "fecha_radicado"    : "2019-06-21 15:21",
            "numero_radicado"   : "2019JBBGERE00000121",
            "ruta_logo"         : "../imagenes/iconos/logo_largo.jpg",
            "ubicacion"         : "izq"
        }
   ]
}
';
*/

/* Inicia con FPDF */
// Tamaño A4 tiene 210 x 297 mm 
// (ancho de celda, alto de celda, Texto a imprimir, borde(0=sin borde,1=marco),Ln o posicion antes de invocar(0=derecha, 1=al comienzo de la siguiente linea, 2=debajo), align(L=alinear a la izquierda, C=alinear al centro, R=alinear a la derecha), fill(Indica si fondo de celda debe ser dibujada (true) o transparente(false) valor por defecto (false) ))
// AddPage(orientacion[PORTRAIT, LANDSCAPE], tamaño[A3,A4,A5,LETTER,LEGAL], rotacion)
// Cell(ancho, alto, texto, bordes, salto_pagina 1="<br>" 0= "", alineacion, rellenar, link)
// SetFont(tipo[COURRIER, HELVETICA, ARIAL, TIMES, SYMBOL, ZAPDINGBATS], estilo[normal, B,I,U], tamaño)  
// Image(ruta, posicionx, posiciony, ancho, alto, tipo, link)
   
function validar_cantidad($cantidad_rotulos,$arreglo){
    // echo "$cantidad_rotulos";
    require_once('../fpdf/fpdf.php');    
/* Funcion para generar codigo de barras */

    class PDF_Code39 extends FPDF {
        function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5){
            $wide = $baseline;
            $narrow = $baseline / 3 ; 
            $gap = $narrow;

            $barChar['0'] = 'nnnwwnwnn';
            $barChar['1'] = 'wnnwnnnnw';
            $barChar['2'] = 'nnwwnnnnw';
            $barChar['3'] = 'wnwwnnnnn';
            $barChar['4'] = 'nnnwwnnnw';
            $barChar['5'] = 'wnnwwnnnn';
            $barChar['6'] = 'nnwwwnnnn';
            $barChar['7'] = 'nnnwnnwnw';
            $barChar['8'] = 'wnnwnnwnn';
            $barChar['9'] = 'nnwwnnwnn';
            $barChar['A'] = 'wnnnnwnnw';
            $barChar['B'] = 'nnwnnwnnw';
            $barChar['C'] = 'wnwnnwnnn';
            $barChar['D'] = 'nnnnwwnnw';
            $barChar['E'] = 'wnnnwwnnn';
            $barChar['F'] = 'nnwnwwnnn';
            $barChar['G'] = 'nnnnnwwnw';
            $barChar['H'] = 'wnnnnwwnn';
            $barChar['I'] = 'nnwnnwwnn';
            $barChar['J'] = 'nnnnwwwnn';
            $barChar['K'] = 'wnnnnnnww';
            $barChar['L'] = 'nnwnnnnww';
            $barChar['M'] = 'wnwnnnnwn';
            $barChar['N'] = 'nnnnwnnww';
            $barChar['O'] = 'wnnnwnnwn'; 
            $barChar['P'] = 'nnwnwnnwn';
            $barChar['Q'] = 'nnnnnnwww';
            $barChar['R'] = 'wnnnnnwwn';
            $barChar['S'] = 'nnwnnnwwn';
            $barChar['T'] = 'nnnnwnwwn';
            $barChar['U'] = 'wwnnnnnnw';
            $barChar['V'] = 'nwwnnnnnw';
            $barChar['W'] = 'wwwnnnnnn';
            $barChar['X'] = 'nwnnwnnnw';
            $barChar['Y'] = 'wwnnwnnnn';
            $barChar['Z'] = 'nwwnwnnnn';
            $barChar['-'] = 'nwnnnnwnw';
            $barChar['.'] = 'wwnnnnwnn';
            $barChar[' '] = 'nwwnnnwnn';
            $barChar['*'] = 'nwnnwnwnn';
            $barChar['$'] = 'nwnwnwnnn';
            $barChar['/'] = 'nwnwnnnwn';
            $barChar['+'] = 'nwnnnwnwn';
            $barChar['%'] = 'nnnwnwnwn';

            $this->SetFont('Arial','',10);
            $this->SetFillColor(0);

            $code = '*'.strtoupper($code).'*';
            // $this->Text($xpos, $ypos + $height + 4, $code);

            for($i=0; $i<strlen($code); $i++){
                $char = $code[$i];
                if(!isset($barChar[$char])){
                    $this->Error('Invalid character in barcode: '.$char);
                }
                $seq = $barChar[$char];
                for($bar=0; $bar<9; $bar++){
                    if($seq[$bar] == 'n'){
                        $lineWidth = $narrow;
                    }else{
                        $lineWidth = $wide;
                    }
                    if($bar % 2 == 0){
                        $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                    }
                    $xpos += $lineWidth;
                }
                // $xpos += $gap;
            }
        }

        var $angle=0;

        function Rotate($angle, $x=-1, $y=-1){
            if($x==-1)
                $x=$this->x;
            if($y==-1)
                $y=$this->y;
            if($this->angle!=0)
                $this->_out('Q');
            $this->angle=$angle;
            if($angle!=0)
            {
                $angle*=M_PI/180;
                $c=cos($angle);
                $s=sin($angle);
                $cx=$x*$this->k;
                $cy=($this->h-$y)*$this->k;
                $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
            }
        }

        function _endpage(){
            if($this->angle!=0)
            {
                $this->angle=0;
                $this->_out('Q');
            }
            parent::_endpage();
        }

        function RotatedText($x, $y, $txt, $angle){
            //Text rotated around its origin
            $this->Rotate($angle, $x, $y);
            $this->Text($x, $y, $txt);
            $this->Rotate(0);
        }

        function RotatedImage($file, $x, $y, $w, $h, $angle){
            //Image rotated around its upper-left corner
            $this->Rotate($angle, $x, $y);
            $this->Image($file, $x, $y, $w, $h);
            $this->Rotate(0);
        }
    }
/* Fin funcion para generar codigo de barras */
    
    for ($d=0; $d < $cantidad_rotulos; $d+=6) { 
        $cantidad_restante=$cantidad_rotulos-$d;
    
        $contador1=$d;
        // $contador2=$d+1;
        // $contador3=$d+2;
        // $contador4=$d+3;
        // $contador5=$d+4;
        // $contador6=$d+5;

        if($cantidad_restante<6){

            $arreglo_sticker = get_object_vars($arreglo['sticker'][$contador1]);

            $anexos1            = "Anexos: ".$arreglo_sticker['anexos'];    
            $anexos             = substr($anexos1, 0,34);

            $asunto             = "Asunto :".$arreglo_sticker['asunto'];

            $destino            = "Destino: ".$arreglo_sticker['destino'];
            $destino1           = substr($destino, 0, 35);

            $encabezado         = $arreglo_sticker['encabezado'];    
            $fecha_radicado     = "Fecha Rad: ".$arreglo_sticker['fecha_radicado'];    
            $numero_radicado    = $arreglo_sticker['numero_radicado'];     
            $ruta_logo          = $arreglo_sticker['ruta_logo'];  
            $ubicacion          = $arreglo_sticker['ubicacion'];  

            if(!isset($_SESSION)){
                session_start();
            }
            $codigo_entidad     = $_SESSION["codigo_entidad"];
            $usuario_radicador  = $_SESSION['login'];
            $usuario_radicador1 = substr("Radicado por: ".$usuario_radicador, 0,35);

            switch ($codigo_entidad) {
                case 'AV1':
                    $imagen_entidad = "<img src='../imagenes/logos_entidades/logo_largo_av1.png' style='width:180px; height:100px;'>";
                    break;

                case 'EJC':
                case 'EJEC':
                    $imagen_entidad = "<img src='../imagenes/logos_entidades/logo_largo_ejc.png' style='width:180px; height:100px;'>";
                    $logo           = '../imagenes/logos_entidades/imagen_qr_ejc.png'; //logo de la entidad dentro del QR
                    break;

                case 'L01':
                    $imagen_entidad = "<img src='../imagenes/logos_entidades/logo_largo_l01.png' style='width:180px; height:100px;'>";
                    break;

                default:
                    $imagen_entidad = "<img src='../imagenes/iconos/logo_largo.png' style='width:180px; height:100px;'>";
                    break;
            }


            /* agregar el script con la librería para generar el QR */
            require ('../include/phpqrcode/qrlib.php');

            /* Se crea el enlace hacia la capeta temporal con el nombre del usuario para guardar los codigos QR generados (Ej. qr_ALUMNO2.png) */
            $filename   = "../bodega_pdf/qr_usuario/qr_$usuario_radicador".".png";

            /* En esta variable se genera el QR e indica cada uno de los datos que se envían a la direccion https://xxxxxx y las variables que se envían por GET */
            $cod        = "https://www.gammacorp.co/consultaweb.php?numero_radicado=$numero_radicado%26codigo_entidad=".$codigo_entidad."%26canal_respuesta=mail&amp"; 

            $tam        = "8"; //tamaño de la imagen qr
            $niv        = "H"; //nivel de seguridad o complejidad del QR del 1 al 5 o "H" (Higher) para el máximo 
            $marco      = 0;  // Marco del QR es tranparente.

            /* clase Qrcode:: funcion png para generar el QR en una imagen png */
            QRcode::png($cod,$filename , $niv, $tam, $marco);

            $QR = $filename;                // Archivo original generado con codigo QR

            /* Si existe el logo para crear en el centro del QR*/
            if (file_exists($logo)) {
                $QR             = imagecreatefromstring(file_get_contents($QR));    // Imagen destino como recurso de conexion
                $logo           = imagecreatefromstring(file_get_contents($logo));  // Recurso de la fuente de la imagen.
                $QR_width       = imagesx($QR);                         // Ancho de la imagen QR original
                $QR_height      = imagesy($QR);                         // Alto de la imagen QR original
                $logo_width     = imagesx($logo);                       // Ancho del logo 
                $logo_height    = imagesy($logo);                       // Alto del logo
                $logo_qr_width  = $QR_width/3;                          // Ancho del logo despues de la combinacion  (1 / 5 del codigo QR)
                $scale          = $logo_width/$logo_qr_width;           // Ancho escalado del logo (Ancho propio / Ancho combinado)
                $logo_qr_height = $logo_height/$scale;                  // Alto del logo despues de combinacion
                $from_width     = ($QR_width - $logo_qr_width) / 2;     // Punto de coordenada desde la esquina izquierda superior del logo despues de combinacion 

                /* Recombinar y redimensionar imagenes*/
                /* imagecopyresampled()  Copia el cuadro de una area desde una imagen (imagen origen) a otra.*/
                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
            }

            /* Salida de imagenes */
            imagepng($QR, $filename);
            imagedestroy($QR);

            /* Etiqueta img para mostrar el QR */
            $imagenqr = "<img src='$filename' style='width:100px; height:100px;'>";

            switch ($ubicacion) {
                case 'izq':
                case 'der':
                    $pdf=new PDF_Code39('P','mm','LETTER');
                    break;
                
                case 'izq_sup':
                    $pdf=new PDF_Code39('L','mm','LETTER');
                    # code...
                    break;
            }


            if($asunto!=""){
                $asunto1 = substr($asunto, 0,55);
            }else{
                $asunto1 = "Asunto : Documento recibido mediante radicacion rapida.";
            }

            #Establecemos los márgenes izquierda, arriba y derecha:
            $pdf->SetMargins(3,3,30);
            #Establecemos el margen inferior:
            $pdf->SetAutoPageBreak(true,5);

            $pdf->AddPage();

            switch ($ubicacion) {
                case 'izq':
                    $pdf->SetFont('Arial','B',5);
                    $pdf->SetLineWidth(0.1); 
                    $pdf->Cell(55,2.5,utf8_decode($encabezado),'',1,'C'); 

                    $pdf->Image($ruta_logo,3,5.5,20,10);
                    $pdf->Image($filename,58,3,15,15); // Imagen QR

                    $pdf->Cell(20,2.5,'','',0); 
                    $pdf->Cell(35,2.5,$fecha_radicado,'',1,'L'); 
                   
                    $pdf->Cell(20,2.5,'','',0); 
                    $pdf->Cell(35,2.5,$anexos,'',1,'L'); 
                    
                    $pdf->Cell(20,2.5,'','',0); 
                    $pdf->Cell(35,2.5,$usuario_radicador1,'',1,'L');   

                    $pdf->Cell(20,2.5,'','',0); 
                    $pdf->Cell(35,2.5,$destino1,'',1,'L'); 
    
                    $pdf->Cell(55,2.5,$asunto1,'',1,'L'); 
                   
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(70,4,$numero_radicado,'',1,'C'); 
                    $pdf->Code39(3.5,21.5,$numero_radicado,0.63,5);
                    break;

                case 'der': 
                    $pdf->SetFont('Arial','B',5);
                    $pdf->SetLineWidth(0.1); 
                    $pdf->Cell(140,5,'','',0); 
                    $pdf->Cell(55,2.5,utf8_decode($encabezado),'',1,'C'); 

                    // Image(ruta, posicionx, posiciony, ancho, alto, tipo, link)
                    $pdf->Image($ruta_logo,143,5.5,20,10);
                    $pdf->Image($filename,198,3,15,15); // Imagen QR

                    // Cell(ancho, alto, texto, bordes, salto_pagina 1="<br>" 0= "", alineacion, rellenar, link)
                    $pdf->Cell(160,2.5,'','',0); 
                    $pdf->Cell(35,2.5,$fecha_radicado,'',1,'L'); 

                    $pdf->Cell(160,2.5,'','',0); 
                    $pdf->Cell(35,2.5,$anexos,'',1,'L'); 

                    $pdf->Cell(160,2.5,'','',0); 
                    $pdf->Cell(35,2.5,$usuario_radicador1,'',1,'L');   

                    $pdf->Cell(160,2.5,'','',0); 
                    $pdf->Cell(35,2.5,$destino1,'',1,'L'); 

                    $pdf->Cell(140,2.5,'','',0); 
                    $pdf->Cell(55,2.5,$asunto1,'',1,'L'); 
                    
                    $pdf->Cell(140,2.5,'','',0); 
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(70,4,$numero_radicado,'',1,'C'); 

                    // Code39($xpos, $ypos, $code, $baseline=0.5, $height=5)
                    $pdf->Code39(143,21.5,$numero_radicado,0.63,5);

                    break;
                case 'izq_sup':
                    $pdf->SetFont('Arial','B',5);
                    $pdf->SetLineWidth(0.1); 
                    $pdf->Cell(203,5,'','',0); 
                    $pdf->Cell(55,2.5,utf8_decode($encabezado),'',1,'C'); 

                    // Image(ruta, posicionx, posiciony, ancho, alto, tipo, link)
                    $pdf->Image($ruta_logo,206,5.5,20,10);
                    $pdf->Image($filename,261,3,15,15); // Imagen QR

                    // Cell(ancho, alto, texto, bordes, salto_pagina 1="<br>" 0= "", alineacion, rellenar, link)
                    $pdf->Cell(223,5,'','',0); 
                    $pdf->Cell(35,2.5,$fecha_radicado,'',1,'L'); 

                    $pdf->Cell(223,5,'','',0); 
                    $pdf->Cell(35,2.5,$anexos,'',1,'L'); 

                    $pdf->Cell(223,5,'','',0); 
                    $pdf->Cell(35,2.5,$usuario_radicador1,'',1,'L');   

                    // $pdf->Cell(160,2.5,'','',0); 
                    $pdf->Cell(223,5,'','',0); 
                    $pdf->Cell(35,2.5,$destino1,'',1,'L'); 

                    // $pdf->Cell(140,2.5,'','',0); 
                    $pdf->Cell(203,5,'','',0); 
                    $pdf->Cell(55,2.5,$asunto1,'',1,'L'); 
                    
                    $pdf->Cell(203,5,'','',0); 
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(70,4,$numero_radicado,'',1,'C'); 

                    // Code39($xpos, $ypos, $code, $baseline=0.5, $height=5)
                    $pdf->Code39(206,21.5,$numero_radicado,0.63,5);


                    // $pdf->SetFont('Arial','B',8);
                    // $pdf->SetLineWidth(0.1); 
                    // $pdf->Cell(200,5,'',' ',0); 
                    // $pdf->Cell(70,5,utf8_decode($encabezado),'LTR',0,'C'); 

                    // $pdf->Cell(6,4,'',' ',1); 
                    
                    // $pdf->Image($ruta_logo,205,10,9,7);
                    // $pdf->Code39(215,10,$numero_radicado,0.55,4);
                    // $pdf->Cell(200,4,'','',0); 
                    // $pdf->Cell(70,4,'','LR',1,'C');   
                   
                    // $pdf->Cell(200,4,'',' ',0); 
                    // $pdf->SetFont('Arial','B',10);
                    // $pdf->Cell(70,4,$numero_radicado,'LR',1,'C'); 

                    // $pdf->Cell(200,4,'',' ',0); 
                    // $pdf->Cell(10,1.5,'','L',0,'L'); 
                    // $pdf->SetFont('Arial','B',5);
                    // $pdf->Cell(10,1.5,'Fecha Rad:','',0,'L'); 
                    // $pdf->SetFont('Arial','',5);
                    // $pdf->Cell(17,1.5,$fecha_radicado,'',0,'C'); 
                    // $pdf->SetFont('Arial','B',5);
                    // $pdf->Cell(9,1.5,'Destino:','',0,'L'); 
                    // $pdf->SetFont('Arial','',4);
                    // $pdf->Cell(24,1.5,$destino,'R',1,'C'); 


                    // $pdf->Cell(200,4,'',' ',0); 
                    // if($asunto!=""){
                    //     $pdf->Cell(10,2,'','L',0,'L'); 
                    //     $pdf->SetFont('Arial','B',5);
                    //     $pdf->Cell(10,2,'Anexos:','',0,'L'); 
                    //     $pdf->SetFont('Arial','',5);
                    //     $pdf->Cell(50,2,$anexos,'R',1,'L'); 

                    //     $pdf->Cell(200,4,'',' ',0); 
                    //     $pdf->Cell(10,2,'','LB',0,'L'); 
                    //     $pdf->SetFont('Arial','B',5);
                    //     $pdf->Cell(10,2,'Asunto:','B',0,'L'); 
                    //     $pdf->SetFont('Arial','',5);
                    //     $pdf->Cell(50,2,$asunto,'BR',1,'L'); 
                    // }else{
                    //     $pdf->Cell(10,2,'','LB',0,'L'); 
                    //     $pdf->SetFont('Arial','B',5);
                    //     $pdf->Cell(10,2,'Anexos:','B',0,'L'); 
                    //     $pdf->SetFont('Arial','',5);
                    //     $pdf->Cell(50,2,$anexos,'BR',1,'L'); 
                    // }
                break;    
            }
           
        }
    }
    $pdf->Output();
}
/* Aqui recibe el json por post y hace el decode */
   $json_recibido=$_POST['json_info_sticker'];

   $decode              = json_decode($json_recibido);
   $arreglo             = get_object_vars( $decode ); 
   $cantidad_rotulos    = sizeof($arreglo['sticker']);
/* Hasta aqui recibe el json por post y hace el decode */

    validar_cantidad($cantidad_rotulos,$arreglo);

?>