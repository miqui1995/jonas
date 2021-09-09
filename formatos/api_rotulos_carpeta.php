<?php 
/* Este es el formato del json que recibe para procesar en este archivo. Si el json no tiene este formato, no va a funcionar la generación de fpdf.

Debe venir en una variable $_POST['rotulos_carpetas'];
{
   "rotulo_carpeta":[
      {
         "correlativo":"13",
         "entidad":"litigando_punto_com",
         "fecha_final":"",
         "fecha_inicial":"2018/09/17",
         "folios":"12",
         "fondo":"yyy",
         "nombre_expediente":"2000-06068 - NULIDAD Y RESTABLECIMIENTO DEL DERECHO",
         "numero_expediente":"EXP2018INV0000000000001",
         "seccion":"(INV) Inventario General",
         "serie":"000",
         "subseccion":"iiiuyi",
         "subserie":"000"
      },
      {
         "correlativo":"15",
         "entidad":"litigando_punto_com",
         "fecha_final":"",
         "fecha_inicial":"2018/09/17",
         "folios":"14",
         "fondo":"aaa",
         "nombre_expediente":"2016-00125 - ORDINARIO",
         "numero_expediente":"EXP2018INV0000000000004",
         "seccion":"(INV) Inventario General",
         "serie":"00011",
         "subseccion":"ccc",
         "subserie":"00022"
      },
      {
         "correlativo":"17",
         "entidad":"litigando_punto_com",
         "fecha_final":"",
         "fecha_inicial":"2018/09/17",
         "folios":"16",
         "fondo":"deeee",
         "nombre_expediente":"2004-00243 - CONTRATOS",
         "numero_expediente":"EXP2018INV0000000002017",
         "seccion":"(INV) Inventario General",
         "serie":"0005",
         "subseccion":"ffff",
         "subserie":"6000"
      },
      {
         "correlativo":"19",
         "entidad":"litigando_punto_com",
         "fecha_final":"",
         "fecha_inicial":"2018/09/17",
         "folios":"18",
         "fondo":"gggg",
         "nombre_expediente":"2000-06068 - NULIDAD Y RESTABLECIMIENTO DEL DERECHO",
         "numero_expediente":"EXP2018INV0000000000001",
         "seccion":"(INV) Inventario General",
         "serie":"000jj",
         "subseccion":"iiii",
         "subserie":"000kk"
      }
   ]
}
*/

/* Inicia con FPDF */
// Tamaño A4 tiene 210 x 297 mm 
// (ancho de celda, alto de celda, Texto a imprimir, borde(0=sin borde,1=marco),Ln o posicion antes de invocar(0=derecha, 1=al comienzo de la siguiente linea, 2=debajo), align(L=alinear a la izquierda, C=alinear al centro, R=alinear a la derecha), fill(Indica si fondo de celda debe ser dibujada (true) o transparente(false) valor por defecto (false) ))
// AddPage(orientacion[PORTRAIT, LANDSCAPE], tamaño[A3,A4,A5,LETTER,LEGAL], rotacion)
// Cell(ancho, alto, texto, bordes, ?, alineacion, rellenar, link)
// SetFont(tipo[COURRIER, HELVETICA, ARIAL, TIMES, SYMBOL, ZAPDINGBATS], estilo[normal, B,I,U], tamaño)  
// Image(ruta, posicionx, posiciony, alto, ancho, tipo, link)
   
function validar_cantidad($cantidad_rotulos,$arreglo){
    require_once('../fpdf/fpdf.php');    
    $pdf=new FPDF('P','mm','LETTER');
   
    #Establecemos los márgenes izquierda, arriba y derecha:
    $pdf->SetMargins(5, 6 , 30);
    #Establecemos el margen inferior:
    $pdf->SetAutoPageBreak(true,5);

    for ($d=0; $d < $cantidad_rotulos; $d+=6) { 
        $cantidad_restante=$cantidad_rotulos-$d;
        
        $pdf->AddPage();

        $contador1=$d;
        $contador2=$d+1;
        $contador3=$d+2;
        $contador4=$d+3;
        $contador5=$d+4;
        $contador6=$d+5;

        if($cantidad_restante<6){

            $arreglo_carpeta1 = get_object_vars($arreglo['rotulos_carpetas'][$contador1]);

            $entidad                = $arreglo_carpeta1['entidad'];  // Defino entidad

            $fondo1                 = $arreglo_carpeta1['fondo'];    
            $seccion1               = $arreglo_carpeta1['seccion'];     
            $subseccion1            = $arreglo_carpeta1['subseccion'];    
            $serie1                 = $arreglo_carpeta1['serie'];
            $subserie1              = $arreglo_carpeta1['subserie'];    
            $numero_expediente1     = $arreglo_carpeta1['numero_expediente'];
            $nombre_expediente1     = $arreglo_carpeta1['nombre_expediente'];  
            $fecha_inicial1         = $arreglo_carpeta1['fecha_inicial'];     
            $fecha_final1           = $arreglo_carpeta1['fecha_final'];  
            $folios1                = $arreglo_carpeta1['folios'];            
            $correlativo1           = $arreglo_carpeta1['correlativo'];            
            switch ($entidad) {
                case 'litigando_punto_com':
                    $nombre_entidad = "(L01) Litigando Punto Com";
                    $logo_entidad  = "../imagenes/logos_entidades/logo_litigando_punto_com.png";
                    break;
                case 'ministerio_agricultura':
                    $nombre_entidad = "(MA1) Ministerio de Agricultura";
                    $logo_entidad  = "../imagenes/logos_entidades/logo_ministerio_agricultura.png";
                    break;
            }

            switch ($cantidad_restante) {
                case '1':
                    $pdf->Image($logo_entidad,6,6,32,14);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetLineWidth(0.5); 
                    $pdf->Cell(33.5,5,'','LT',0); 
                    $pdf->Cell(33,5,'Formato','LT',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',0,'C'); 
                   
                    $pdf->Cell(6,4,'',' ',1); 
                    
                    $pdf->Cell(33.5,5,'','LR',0); 
                    $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',1,'C');   
                    $pdf->Cell(33.5,5,'','LB',0); 
                    $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',1,'L'); 

                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($fondo1),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',1,'L'); 
     
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($seccion1),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subseccion1),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',1,'L');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($serie1),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subserie1),'LR',1,'C'); 
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($numero_expediente1),'LR',1,'C');
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($nombre_expediente1),'LR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial1),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final1),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
                    $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($folios1),'LBR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($correlativo1),'LBR',1,'C'); 
                    $pdf->Cell(99.5,5,utf8_decode(" "),' ',1,'C');
                                    
                    break;
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                    $arreglo_carpeta2 = get_object_vars($arreglo['rotulos_carpetas'][$contador2]);

                    $entidad                = $arreglo_carpeta2['entidad'];  
                    $fondo2                 = $arreglo_carpeta2['fondo'];  
                    $seccion2               = $arreglo_carpeta2['seccion'];  
                    $subseccion2            = $arreglo_carpeta2['subseccion'];  
                    $serie2                 = $arreglo_carpeta2['serie'];  
                    $subserie2              = $arreglo_carpeta2['subserie'];  
                    $numero_expediente2     = $arreglo_carpeta2['numero_expediente'];  
                    $nombre_expediente2     = $arreglo_carpeta2['nombre_expediente'];  
                    $fecha_inicial2         = $arreglo_carpeta2['fecha_inicial'];  
                    $fecha_final2           = $arreglo_carpeta2['fecha_final'];  
                    $folios2                = $arreglo_carpeta2['folios'];  
                    $correlativo2           = $arreglo_carpeta2['correlativo'];  

                    $pdf->Image($logo_entidad,6,7,32,14);
                    $pdf->Image($logo_entidad,111,7,32,14);
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetLineWidth(0.5); 
                    $pdf->Cell(33.5,5,'','LT',0); 
                    $pdf->Cell(33,5,'Formato','LT',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',0,'C');                
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(33.5,5,'','LT',0); 
                    $pdf->Cell(33,5,'Formato','LT',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',1,'C'); 
                    
                    $pdf->Cell(33.5,5,'','LR',0); 
                    $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',0,'C');   
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(33.5,5,'','LR',0); 
                    $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',1,'C');   

                    $pdf->Cell(33.5,5,'','LB',0); 
                    $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(33.5,5,'','LB',0); 
                    $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',0,'L');         
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',1,'L'); 

                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($fondo1),'LR',0,'C');        
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($fondo2),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',0,'L'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($seccion1),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subseccion1),'LR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($seccion2),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subseccion2),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',0,'L');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',1,'L');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($serie1),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subserie1),'LR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($serie2),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subserie2),'LR',1,'C'); 
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',0,'L'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($numero_expediente1),'LR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode($numero_expediente2),'LR',1,'C');
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',0,'L'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($nombre_expediente1),'LR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode($nombre_expediente2),'LR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial1),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final1),'LR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial2),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final2),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
                    $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
                    $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($folios1),'LBR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($correlativo1),'LBR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($folios2),'LBR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($correlativo2),'LBR',1,'C'); 

                    $pdf->Cell(99.5,5,utf8_decode(" "),' ',1,'C');
                    break;
            }
            switch ($cantidad_restante) {
                case '3':
                    $arreglo_carpeta3 = get_object_vars($arreglo['rotulos_carpetas'][$contador3]);

                    $entidad                = $arreglo_carpeta3['entidad'];  
                    $fondo3                 = $arreglo_carpeta3['fondo'];  
                    $seccion3               = $arreglo_carpeta3['seccion'];  
                    $subseccion3            = $arreglo_carpeta3['subseccion'];  
                    $serie3                 = $arreglo_carpeta3['serie'];  
                    $subserie3              = $arreglo_carpeta3['subserie'];  
                    $numero_expediente3     = $arreglo_carpeta3['numero_expediente'];  
                    $nombre_expediente3     = $arreglo_carpeta3['nombre_expediente'];  
                    $fecha_inicial3         = $arreglo_carpeta3['fecha_inicial'];  
                    $fecha_final3           = $arreglo_carpeta3['fecha_final'];  
                    $folios3                = $arreglo_carpeta3['folios'];  
                    $correlativo3           = $arreglo_carpeta3['correlativo'];  

                    $pdf->Image($logo_entidad,6,96,32,14);
                    
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetLineWidth(0.5); 
                    $pdf->Cell(33.5,5,'','LT',0); 
                    $pdf->Cell(33,5,'Formato','LT',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',0,'C'); 
                   
                    $pdf->Cell(6,4,'',' ',1); 
                    
                    $pdf->Cell(33.5,5,'','LR',0); 
                    $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',1,'C');   
                    $pdf->Cell(33.5,5,'','LB',0); 
                    $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',1,'L'); 

                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($fondo3),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',1,'L'); 
     
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($seccion3),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subseccion3),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',1,'L');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($serie3),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subserie3),'LR',1,'C'); 
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($numero_expediente3),'LR',1,'C');
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($nombre_expediente3),'LR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial3),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final3),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
                    $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($folios3),'LBR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($correlativo3),'LBR',1,'C'); 

                    $pdf->Cell(99.5,5,utf8_decode(" "),' ',1,'C');                
                    break;
                case '4':
                case '5':
                case '6':
                    $arreglo_carpeta3 = get_object_vars($arreglo['rotulos_carpetas'][$contador3]);
                    $arreglo_carpeta4 = get_object_vars($arreglo['rotulos_carpetas'][$contador4]);

                    $fondo3                 = $arreglo_carpeta3['fondo'];  
                    $seccion3               = $arreglo_carpeta3['seccion'];  
                    $subseccion3            = $arreglo_carpeta3['subseccion'];  
                    $serie3                 = $arreglo_carpeta3['serie'];  
                    $subserie3              = $arreglo_carpeta3['subserie'];  
                    $numero_expediente3     = $arreglo_carpeta3['numero_expediente'];  
                    $nombre_expediente3     = $arreglo_carpeta3['nombre_expediente'];  
                    $fecha_inicial3         = $arreglo_carpeta3['fecha_inicial'];  
                    $fecha_final3           = $arreglo_carpeta3['fecha_final'];  
                    $folios3                = $arreglo_carpeta3['folios'];  
                    $correlativo3           = $arreglo_carpeta3['correlativo'];  

                    $fondo4                 = $arreglo_carpeta4['fondo'];  
                    $seccion4               = $arreglo_carpeta4['seccion'];  
                    $subseccion4            = $arreglo_carpeta4['subseccion'];  
                    $serie4                 = $arreglo_carpeta4['serie'];  
                    $subserie4              = $arreglo_carpeta4['subserie'];  
                    $numero_expediente4     = $arreglo_carpeta4['numero_expediente'];  
                    $nombre_expediente4     = $arreglo_carpeta4['nombre_expediente'];  
                    $fecha_inicial4         = $arreglo_carpeta4['fecha_inicial'];  
                    $fecha_final4           = $arreglo_carpeta4['fecha_final'];  
                    $folios4                = $arreglo_carpeta4['folios'];  
                    $correlativo4           = $arreglo_carpeta4['correlativo'];  

                    $pdf->Image($logo_entidad,6,96,32,14);      // Imagen rotulo 3
                    $pdf->Image($logo_entidad,111,96,32,14);    // Imagen rotulo 4

                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetLineWidth(0.5); 
                    $pdf->Cell(33.5,5,'','LT',0); 
                    $pdf->Cell(33,5,'Formato','LT',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',0,'C');                
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(33.5,5,'','LT',0); 
                    $pdf->Cell(33,5,'Formato','LT',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',1,'C'); 
                    
                    $pdf->Cell(33.5,5,'','LR',0); 
                    $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',0,'C');   
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(33.5,5,'','LR',0); 
                    $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',1,'C');   

                    $pdf->Cell(33.5,5,'','LB',0); 
                    $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(33.5,5,'','LB',0); 
                    $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',0,'L');         
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',1,'L'); 

                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($fondo3),'LR',0,'C');        
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($fondo4),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',0,'L'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($seccion3),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subseccion3),'LR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($seccion4),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subseccion4),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',0,'L');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',1,'L');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($serie3),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subserie3),'LR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($serie4),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subserie4),'LR',1,'C'); 
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',0,'L'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($numero_expediente3),'LR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode($numero_expediente4),'LR',1,'C');
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',0,'L'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($nombre_expediente3),'LR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode($nombre_expediente4),'LR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial3),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final3),'LR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial4),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final4),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
                    $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
                    $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($folios3),'LBR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($correlativo3),'LBR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($folios4),'LBR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($correlativo4),'LBR',1,'C');                 

                    $pdf->Cell(99.5,5,utf8_decode(" "),' ',1,'C');
                    break;
                }    
                switch ($cantidad_restante) {
                    case '5':
                    $arreglo_carpeta5 = get_object_vars($arreglo['rotulos_carpetas'][$contador5]);
                    
                    $fondo5                 = $arreglo_carpeta5['fondo'];  
                    $seccion5               = $arreglo_carpeta5['seccion'];  
                    $subseccion5            = $arreglo_carpeta5['subseccion'];  
                    $serie5                 = $arreglo_carpeta5['serie'];  
                    $subserie5              = $arreglo_carpeta5['subserie'];  
                    $numero_expediente5     = $arreglo_carpeta5['numero_expediente'];  
                    $nombre_expediente5     = $arreglo_carpeta5['nombre_expediente'];  
                    $fecha_inicial5         = $arreglo_carpeta5['fecha_inicial'];  
                    $fecha_final5           = $arreglo_carpeta5['fecha_final'];  
                    $folios5                = $arreglo_carpeta5['folios'];  
                    $correlativo5           = $arreglo_carpeta5['correlativo'];  

                    $pdf->Image($logo_entidad,6,186,32,14);
                    
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetLineWidth(0.5); 
                    $pdf->Cell(33.5,5,'','LT',0); 
                    $pdf->Cell(33,5,'Formato','LT',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',0,'C'); 
                   
                    $pdf->Cell(6,4,'',' ',1); 
                    
                    $pdf->Cell(33.5,5,'','LR',0); 
                    $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',1,'C');   
                    $pdf->Cell(33.5,5,'','LB',0); 
                    $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',1,'L'); 

                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($fondo5),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',1,'L'); 
     
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($seccion5),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subseccion5),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',1,'L');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($serie5),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subserie5),'LR',1,'C'); 
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($numero_expediente5),'LR',1,'C');
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($nombre_expediente5),'LR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial5),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final5),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
                    $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($folios5),'LBR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($correlativo5),'LBR',1,'C'); 

                    break;
                case '6':
                    $arreglo_carpeta5 = get_object_vars($arreglo['rotulos_carpetas'][$contador5]);
                    $arreglo_carpeta6 = get_object_vars($arreglo['rotulos_carpetas'][$contador6]);

                    $fondo5                 = $arreglo_carpeta5['fondo'];  
                    $seccion5               = $arreglo_carpeta5['seccion'];
                    $subseccion5            = $arreglo_carpeta5['subseccion'];  
                    $serie5                 = $arreglo_carpeta5['serie'];  
                    $subserie5              = $arreglo_carpeta5['subserie'];  
                    $numero_expediente5     = $arreglo_carpeta5['numero_expediente'];  
                    $nombre_expediente5     = $arreglo_carpeta5['nombre_expediente'];  
                    $fecha_inicial5         = $arreglo_carpeta5['fecha_inicial'];  
                    $fecha_final5           = $arreglo_carpeta5['fecha_final'];  
                    $folios5                = $arreglo_carpeta5['folios']; 
                    $correlativo5           = $arreglo_carpeta5['correlativo'];  

                    $fondo6                 = $arreglo_carpeta6['fondo'];  
                    $seccion6               = $arreglo_carpeta6['seccion'];
                    $subseccion6            = $arreglo_carpeta6['subseccion'];  
                    $serie6                 = $arreglo_carpeta6['serie'];  
                    $subserie6              = $arreglo_carpeta6['subserie'];  
                    $numero_expediente6     = $arreglo_carpeta6['numero_expediente'];  
                    $nombre_expediente6     = $arreglo_carpeta6['nombre_expediente'];  
                    $fecha_inicial6         = $arreglo_carpeta6['fecha_inicial'];  
                    $fecha_final6           = $arreglo_carpeta6['fecha_final'];  
                    $folios6                = $arreglo_carpeta6['folios']; 
                    $correlativo6           = $arreglo_carpeta6['correlativo'];  

                    $pdf->Image($logo_entidad,6,186,32,14);     // Imagen rotulo 5
                    $pdf->Image($logo_entidad,111,186,32,14);   // Imagen rotulo 6

                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetLineWidth(0.5); 
                    $pdf->Cell(33.5,5,'','LT',0); 
                    $pdf->Cell(33,5,'Formato','LT',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',0,'C');                
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(33.5,5,'','LT',0); 
                    $pdf->Cell(33,5,'Formato','LT',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',1,'C'); 
                    
                    $pdf->Cell(33.5,5,'','LR',0); 
                    $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',0,'C');   
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(33.5,5,'','LR',0); 
                    $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',1,'C');   

                    $pdf->Cell(33.5,5,'','LB',0); 
                    $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(33.5,5,'','LB',0); 
                    $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
                    $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',0,'L');         
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',1,'L'); 

                    $pdf->SetFont('Arial','B',9);
                    $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($fondo5),'LR',0,'C');        
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($fondo6),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',0,'L'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($seccion5),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subseccion5),'LR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($seccion6),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subseccion6),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',0,'L');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
                    $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',1,'L');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($serie5),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subserie5),'LR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($serie6),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($subserie6),'LR',1,'C'); 
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',0,'L'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($numero_expediente5),'LR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode($numero_expediente6),'LR',1,'C');
                    
                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',0,'L'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',1,'L'); 

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(99.5,5,utf8_decode($nombre_expediente5),'LR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode($nombre_expediente6),'LR',1,'C');

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial5),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final5),'LR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial6),'LR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final6),'LR',1,'C'); 

                    $pdf->SetFont('Arial','BU',8);
                    $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
                    $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',0,'C');
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
                    $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',1,'C');

                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(49.75,5,utf8_decode($folios5),'LBR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($correlativo5),'LBR',0,'C'); 
                    $pdf->Cell(6,4,'',' ',0); 
                    $pdf->Cell(49.75,5,utf8_decode($folios6),'LBR',0,'C');
                    $pdf->Cell(49.75,5,utf8_decode($correlativo6),'LBR',1,'C');                 

                    break;
                }     
        }else{
            $arreglo_carpeta1 = get_object_vars($arreglo['rotulos_carpetas'][$contador1]);

            $entidad                = $arreglo_carpeta1['entidad'];  // Defino entidad

            $fondo1                 = $arreglo_carpeta1['fondo'];    
            $seccion1               = $arreglo_carpeta1['seccion'];     
            $subseccion1            = $arreglo_carpeta1['subseccion'];    
            $serie1                 = $arreglo_carpeta1['serie'];
            $subserie1              = $arreglo_carpeta1['subserie'];    
            $numero_expediente1     = $arreglo_carpeta1['numero_expediente'];
            $nombre_expediente1     = $arreglo_carpeta1['nombre_expediente'];  
            $fecha_inicial1         = $arreglo_carpeta1['fecha_inicial'];     
            $fecha_final1           = $arreglo_carpeta1['fecha_final'];  
            $folios1                = $arreglo_carpeta1['folios'];            
            $correlativo1           = $arreglo_carpeta1['correlativo'];            
            switch ($entidad) {
                case 'litigando_punto_com':
                    $nombre_entidad = "(L01) Litigando Punto Com";
                    $logo_entidad  = "../imagenes/logos_entidades/logo_litigando_punto_com.png";
                    break;
                case 'ministerio_agricultura':
                    $nombre_entidad = "(MA1) Ministerio de Agricultura";
                    $logo_entidad  = "../imagenes/logos_entidades/logo_ministerio_agricultura.png";
                    break;
            }

            $arreglo_carpeta2 = get_object_vars($arreglo['rotulos_carpetas'][$contador2]);

            $entidad                = $arreglo_carpeta2['entidad'];  
            $fondo2                 = $arreglo_carpeta2['fondo'];  
            $seccion2               = $arreglo_carpeta2['seccion'];  
            $subseccion2            = $arreglo_carpeta2['subseccion'];  
            $serie2                 = $arreglo_carpeta2['serie'];  
            $subserie2              = $arreglo_carpeta2['subserie'];  
            $numero_expediente2     = $arreglo_carpeta2['numero_expediente'];  
            $nombre_expediente2     = $arreglo_carpeta2['nombre_expediente'];  
            $fecha_inicial2         = $arreglo_carpeta2['fecha_inicial'];  
            $fecha_final2           = $arreglo_carpeta2['fecha_final'];  
            $folios2                = $arreglo_carpeta2['folios'];  
            $correlativo2           = $arreglo_carpeta2['correlativo'];  

            $pdf->Image($logo_entidad,6,7,32,14);
            $pdf->Image($logo_entidad,111,7,32,14);
            $pdf->SetFont('Arial','B',10);
            $pdf->SetLineWidth(0.5); 
            $pdf->Cell(33.5,5,'','LT',0); 
            $pdf->Cell(33,5,'Formato','LT',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',0,'C');                
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(33.5,5,'','LT',0); 
            $pdf->Cell(33,5,'Formato','LT',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',1,'C'); 
            
            $pdf->Cell(33.5,5,'','LR',0); 
            $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',0,'C');   
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(33.5,5,'','LR',0); 
            $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',1,'C');   

            $pdf->Cell(33.5,5,'','LB',0); 
            $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(33.5,5,'','LB',0); 
            $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',1,'C');

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',0,'L');         
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',1,'L'); 

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($fondo1),'LR',0,'C');        
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($fondo2),'LR',1,'C'); 

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',0,'L'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',1,'L'); 

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode($seccion1),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subseccion1),'LR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($seccion2),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subseccion2),'LR',1,'C'); 

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',0,'L');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',1,'L');

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode($serie1),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subserie1),'LR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($serie2),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subserie2),'LR',1,'C'); 
            
            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',0,'L'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',1,'L'); 

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(99.5,5,utf8_decode($numero_expediente1),'LR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode($numero_expediente2),'LR',1,'C');
            
            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',0,'L'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',1,'L'); 

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(99.5,5,utf8_decode($nombre_expediente1),'LR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode($nombre_expediente2),'LR',1,'C');

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',1,'C');

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial1),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final1),'LR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial2),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final2),'LR',1,'C'); 

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
            $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
            $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',1,'C');

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode($folios1),'LBR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($correlativo1),'LBR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($folios2),'LBR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($correlativo2),'LBR',1,'C'); 

            $pdf->Cell(99.5,5,utf8_decode(" "),' ',1,'C');

            $arreglo_carpeta3 = get_object_vars($arreglo['rotulos_carpetas'][$contador3]);
            $arreglo_carpeta4 = get_object_vars($arreglo['rotulos_carpetas'][$contador4]);

            $fondo3                 = $arreglo_carpeta3['fondo'];  
            $seccion3               = $arreglo_carpeta3['seccion'];  
            $subseccion3            = $arreglo_carpeta3['subseccion'];  
            $serie3                 = $arreglo_carpeta3['serie'];  
            $subserie3              = $arreglo_carpeta3['subserie'];  
            $numero_expediente3     = $arreglo_carpeta3['numero_expediente'];  
            $nombre_expediente3     = $arreglo_carpeta3['nombre_expediente'];  
            $fecha_inicial3         = $arreglo_carpeta3['fecha_inicial'];  
            $fecha_final3           = $arreglo_carpeta3['fecha_final'];  
            $folios3                = $arreglo_carpeta3['folios'];  
            $correlativo3           = $arreglo_carpeta3['correlativo'];  

            $fondo4                 = $arreglo_carpeta4['fondo'];  
            $seccion4               = $arreglo_carpeta4['seccion'];  
            $subseccion4            = $arreglo_carpeta4['subseccion'];  
            $serie4                 = $arreglo_carpeta4['serie'];  
            $subserie4              = $arreglo_carpeta4['subserie'];  
            $numero_expediente4     = $arreglo_carpeta4['numero_expediente'];  
            $nombre_expediente4     = $arreglo_carpeta4['nombre_expediente'];  
            $fecha_inicial4         = $arreglo_carpeta4['fecha_inicial'];  
            $fecha_final4           = $arreglo_carpeta4['fecha_final'];  
            $folios4                = $arreglo_carpeta4['folios'];  
            $correlativo4           = $arreglo_carpeta4['correlativo'];  

            $pdf->Image($logo_entidad,6,96,32,14);      // Imagen rotulo 3
            $pdf->Image($logo_entidad,111,96,32,14);    // Imagen rotulo 4

            $pdf->SetFont('Arial','B',10);
            $pdf->SetLineWidth(0.5); 
            $pdf->Cell(33.5,5,'','LT',0); 
            $pdf->Cell(33,5,'Formato','LT',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',0,'C');                
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(33.5,5,'','LT',0); 
            $pdf->Cell(33,5,'Formato','LT',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',1,'C'); 
            
            $pdf->Cell(33.5,5,'','LR',0); 
            $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',0,'C');   
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(33.5,5,'','LR',0); 
            $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',1,'C');   

            $pdf->Cell(33.5,5,'','LB',0); 
            $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(33.5,5,'','LB',0); 
            $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',1,'C');

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',0,'L');         
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',1,'L'); 

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($fondo3),'LR',0,'C');        
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($fondo4),'LR',1,'C'); 

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',0,'L'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',1,'L'); 

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode($seccion3),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subseccion3),'LR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($seccion4),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subseccion4),'LR',1,'C'); 

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',0,'L');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',1,'L');

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode($serie3),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subserie3),'LR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($serie4),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subserie4),'LR',1,'C'); 
            
            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',0,'L'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',1,'L'); 

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(99.5,5,utf8_decode($numero_expediente3),'LR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode($numero_expediente4),'LR',1,'C');
            
            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',0,'L'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',1,'L'); 

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(99.5,5,utf8_decode($nombre_expediente3),'LR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode($nombre_expediente4),'LR',1,'C');

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',1,'C');

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial3),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final3),'LR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial4),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final4),'LR',1,'C'); 

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
            $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
            $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',1,'C');

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode($folios3),'LBR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($correlativo3),'LBR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($folios4),'LBR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($correlativo4),'LBR',1,'C');                 

            $pdf->Cell(99.5,5,utf8_decode(" "),' ',1,'C');

            $arreglo_carpeta5 = get_object_vars($arreglo['rotulos_carpetas'][$contador5]);
            $arreglo_carpeta6 = get_object_vars($arreglo['rotulos_carpetas'][$contador6]);

            $fondo5                 = $arreglo_carpeta5['fondo'];  
            $seccion5               = $arreglo_carpeta5['seccion'];
            $subseccion5            = $arreglo_carpeta5['subseccion'];  
            $serie5                 = $arreglo_carpeta5['serie'];  
            $subserie5              = $arreglo_carpeta5['subserie'];  
            $numero_expediente5     = $arreglo_carpeta5['numero_expediente'];  
            $nombre_expediente5     = $arreglo_carpeta5['nombre_expediente'];  
            $fecha_inicial5         = $arreglo_carpeta5['fecha_inicial'];  
            $fecha_final5           = $arreglo_carpeta5['fecha_final'];  
            $folios5                = $arreglo_carpeta5['folios']; 
            $correlativo5           = $arreglo_carpeta5['correlativo'];  

            $fondo6                 = $arreglo_carpeta6['fondo'];  
            $seccion6               = $arreglo_carpeta6['seccion'];
            $subseccion6            = $arreglo_carpeta6['subseccion'];  
            $serie6                 = $arreglo_carpeta6['serie'];  
            $subserie6              = $arreglo_carpeta6['subserie'];  
            $numero_expediente6     = $arreglo_carpeta6['numero_expediente'];  
            $nombre_expediente6     = $arreglo_carpeta6['nombre_expediente'];  
            $fecha_inicial6         = $arreglo_carpeta6['fecha_inicial'];  
            $fecha_final6           = $arreglo_carpeta6['fecha_final'];  
            $folios6                = $arreglo_carpeta6['folios']; 
            $correlativo6           = $arreglo_carpeta6['correlativo'];  

            $pdf->Image($logo_entidad,6,186,32,14);     // Imagen rotulo 5
            $pdf->Image($logo_entidad,111,186,32,14);   // Imagen rotulo 6

            $pdf->SetFont('Arial','B',10);
            $pdf->SetLineWidth(0.5); 
            $pdf->Cell(33.5,5,'','LT',0); 
            $pdf->Cell(33,5,'Formato','LT',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',0,'C');                
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(33.5,5,'','LT',0); 
            $pdf->Cell(33,5,'Formato','LT',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Versión 1'),'LTR',1,'C'); 
            
            $pdf->Cell(33.5,5,'','LR',0); 
            $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',0,'C');   
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(33.5,5,'','LR',0); 
            $pdf->Cell(33,5,utf8_decode('Identificación'),'LR',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('Fecha de Edición'),'LTR',1,'C');   

            $pdf->Cell(33.5,5,'','LB',0); 
            $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(33.5,5,'','LB',0); 
            $pdf->Cell(33,5,utf8_decode('de Carpetas'),'LB',0,'C'); 
            $pdf->Cell(33,5,utf8_decode('15-09-2018'),'LBR',1,'C');

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',0,'L');         
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Fondo'),'LR',1,'L'); 

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($fondo5),'LR',0,'C');        
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($nombre_entidad),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($fondo6),'LR',1,'C'); 

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',0,'L'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Dependencia - Seccion'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Oficina Productora - Subseccion'),'LTR',1,'L'); 

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode($seccion5),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subseccion5),'LR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($seccion6),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subseccion6),'LR',1,'C'); 

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',0,'L');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Serie Documental'),'LTR',0,'L'); 
            $pdf->Cell(49.75,5,utf8_decode('Subserie Documental'),'LTR',1,'L');

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode($serie5),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subserie5),'LR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($serie6),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($subserie6),'LR',1,'C'); 
            
            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',0,'L'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode('Numero Carpeta - Expediente'),'LTR',1,'L'); 

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(99.5,5,utf8_decode($numero_expediente5),'LR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode($numero_expediente6),'LR',1,'C');
            
            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',0,'L'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode('Nombre Carpeta - Expediente'),'LTR',1,'L'); 

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(99.5,5,utf8_decode($nombre_expediente5),'LR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode($nombre_expediente6),'LR',1,'C');

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(99.5,5,utf8_decode('Fechas Extremas'),'LTR',1,'C');

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial5),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final5),'LR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Desde '.$fecha_inicial6),'LR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode('Hasta '.$fecha_final6),'LR',1,'C'); 

            $pdf->SetFont('Arial','BU',8);
            $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
            $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',0,'C');
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode('Folios'),'LTR',0,'C'); 
            $pdf->Cell(49.75,5,utf8_decode('Numero Correlativo'),'LTR',1,'C');

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(49.75,5,utf8_decode($folios5),'LBR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($correlativo5),'LBR',0,'C'); 
            $pdf->Cell(6,4,'',' ',0); 
            $pdf->Cell(49.75,5,utf8_decode($folios6),'LBR',0,'C');
            $pdf->Cell(49.75,5,utf8_decode($correlativo6),'LBR',1,'C');      
        }
    }
    $pdf->Output();
}
/* Aqui recibe el json por post y hace el decode */
   $json_recibido=$_POST['rotulos_carpetas'];

   $decode =json_decode($json_recibido);
   $arreglo = get_object_vars( $decode ); 
   $cantidad_rotulos = sizeof($arreglo['rotulos_carpetas']);
/* Hasta aqui recibe el json por post y hace el decode */

    validar_cantidad($cantidad_rotulos,$arreglo);

?>