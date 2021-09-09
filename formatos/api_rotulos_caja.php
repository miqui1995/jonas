<?php 
/* Este es el formato del json que recibe para procesar en este archivo. Si el json no tiene este formato, no va a funcionar la generación de fpdf.

Debe venir en una variable $_POST['rotulos_cajas'];

{
   "rotulo_caja":[
      {
         "entidad":"litigando_punto_com",
         "fecha_inicial":"07/12/1999",
         "fecha_final":"06/03/2014",
         "fondo":"Ejemplo fondo 1",
         "seccion":"Ejemplo Seccion numero 1",
         "subseccion":"Ejemplo Subseccion numero 1",
         "numero_caja":"NIVEL 10",
         "cantidad_carpetas":"11",
         "expedientes":[
            {
               "numero_carpeta":"EXP2018INV0000000000001",
               "nombre_carpeta":"000-06068 - NULIDAD Y RESTABLECIMIENTO DEL"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000002",
               "nombre_carpeta":"000-06068 - NULIDAD Y RESTABLECIMIENTO DEL"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000003",
               "nombre_carpeta":"998-05098 - PECULADO"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000004",
               "nombre_carpeta":"016-00125 - ORDINARIO"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000005",
               "nombre_carpeta":"002-0374 - ACCION CONTRACTUAL"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000006",
               "nombre_carpeta":"002-0374 - ACCION CONTRACTUAL"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000007",
               "nombre_carpeta":"002-0374 - ACCION CONTRACTUAL"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000008",
               "nombre_carpeta":"001-1924 - ACCION REPETICION"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000009",
               "nombre_carpeta":"000-03968 - NULIDAD Y RESTABLECIMIENTO DEL"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000017",
               "nombre_carpeta":"999-02798 - ACCION DE GRUPO"
            }
         ]
      },
      {
         "entidad":"litigando_punto_com",
         "fecha_inicial":"11/11/1999",
         "fecha_final":"20/02/2014",
         "fondo":"Ejemplo fondo 2",
         "seccion":"Ejemplo Seccion numero 2",
         "subseccion":"Ejemplo Subseccion numero 2",
         "numero_caja":"NIVEL 11",
         "cantidad_carpetas":"3",
         "expedientes":[
            {
               "numero_carpeta":"EXP2018INV0000000000013",
               "nombre_carpeta":"000-03945 - NULIDAD Y RESTABLECIMIENTO DEL"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000014",
               "nombre_carpeta":"003-08482 - NULIDAD Y RESTABLECIMIENTO DEL"
            },
            {
               "numero_carpeta":"EXP2018INV0000000000027",
               "nombre_carpeta":"000-01355 - ACCION DE NULIDAD Y RESTABLECI"
            }
         ]
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
   
function imprimir_hoja($cantidad_rotulos,$arreglo){
      require_once('../fpdf/fpdf.php');    
      $pdf=new FPDF('L','mm','LETTER');
   
   #Establecemos los márgenes izquierda, arriba y derecha:
      $pdf->SetMargins(5, 4 , 30);
      #Establecemos el margen inferior:
      $pdf->SetAutoPageBreak(true,5);

      for ($a=0; $a < $cantidad_rotulos; $a++) { 
         $arreglo_caja = get_object_vars($arreglo['rotulo_caja'][$a]);

         if($a%2==0){
            $b=$a+1;
            if($b==$cantidad_rotulos){
               $pdf->AddPage();
               
               $entidad=$arreglo_caja['entidad'];  // Defino entidad
               
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
               $pdf->Image($logo_entidad,6,3,40,15);

               $pdf->SetFont('Arial','B',12);
               
               $fondo               =$arreglo_caja['fondo'];               // Defino fondo
               $seccion             =$arreglo_caja['seccion'];             // Defino seccion
               $subseccion          =$arreglo_caja['subseccion'];          // Defino subseccion1
               $fecha_inicial       =$arreglo_caja['fecha_inicial'];       // Defino fecha_inicial
               $fecha_final         =$arreglo_caja['fecha_final'];         // Defino fecha_final
               $numero_caja         =$arreglo_caja['numero_caja'];         // Defino numero_caja
               $cantidad_carpetas   =$arreglo_caja['cantidad_carpetas'];   // Defino cantidad_carpetas

               $cantidad_expedientes=sizeof($arreglo_caja['expedientes']);

               $pdf->SetLineWidth(0.5); 

               $pdf->Cell(45.5,5,'','LT',0); 
               $pdf->Cell(43,5,'Formato','LT',0,'C'); 
               $pdf->Cell(43,5,utf8_decode('Versión 1'),'LTR',1,'C'); 
               $pdf->Cell(45.5,5,'','LR',0); 
               $pdf->Cell(43,5,utf8_decode('Identificación'),'LR',0,'C'); 
               $pdf->Cell(43,5,utf8_decode('Fecha de Edición'),'LTR',1,'C'); 
               $pdf->Cell(45.5,5,'','LB',0); 
               $pdf->Cell(43,5,utf8_decode('de Cajas'),'LB',0,'C'); 
               $pdf->Cell(43,5,utf8_decode('15-09-2018'),'LBR',1,'C'); 
               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',1,'L'); 
               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($nombre_entidad),'LR',1,'C'); 
               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Fondo'),'LR',1,'L'); 
               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($fondo),'LR',1,'C'); 
               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Dependencia - Seccion'),'LR',1,'L'); 
               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($seccion),'LR',1,'C'); 
               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Oficina Productora - Subseccion'),'LR',1,'L'); 
               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($subseccion),'LR',1,'C'); 

               $pdf->SetFont('Arial','B',10);
               $pdf->Cell(49,5,utf8_decode('Numero Carpeta - Expediente'),1,0,'C'); 
               $pdf->Cell(82.5,5,utf8_decode('Nombre Carpeta - Expediente'),1,1,'C'); 
              
               for ($j=0; $j < $cantidad_expedientes; $j++) { 
                  $expedientes_caja =get_object_vars($arreglo_caja['expedientes'][$j]);
                  $numero_carpeta = $expedientes_caja['numero_carpeta'];
                  $nombre_carpeta = $expedientes_caja['nombre_carpeta'];

                  $pdf->Cell(49,7.5,utf8_decode($numero_carpeta),1,0,'C'); 
                  $pdf->Cell(82.5,7.5,utf8_decode($nombre_carpeta),1,1,'C'); 
               }        
               if($cantidad_expedientes<10){
                  for ($k=$cantidad_expedientes; $k < 10; $k++) { 
                     $pdf->Cell(49,7.5,utf8_decode(''),1,0,'C'); 
                     $pdf->Cell(82.5,7.5,utf8_decode(''),1,1,'C'); 
                  }
               }

               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,10,utf8_decode('Fechas Extremas'),'LR',1,'C'); 
               $pdf->SetFont('Arial','B',14);
               $pdf->Cell(65.75,5,utf8_decode('Desde '.$fecha_inicial),'L',0,'C'); 
               $pdf->Cell(65.75,5,utf8_decode('Hasta '.$fecha_final),'R',1,'C'); 

               $pdf->SetFont('Arial','BU',18);
               $pdf->Cell(45.5,7,'Numero','LT',0,'C'); 
               $pdf->Cell(43,7,'Cantidad de ','LT',0,'C'); 
               $pdf->Cell(43,7,utf8_decode('Numero'),'LTR',1,'C'); 

               $pdf->Cell(45.5,7,'de Caja','LR',0,'C'); 
               $pdf->Cell(43,7,utf8_decode('Carpetas'),'R',0,'C'); 
               $pdf->Cell(43,7,utf8_decode('Correlativo'),'R',1,'C'); 

               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(45.5,15,utf8_decode($numero_caja),1,0,'C'); 
               $pdf->Cell(43,15,utf8_decode($cantidad_carpetas),1,0,'C'); 
               $pdf->Cell(43,15,'',1,1,'C'); 

            }else{ 
               $arreglo_caja2= get_object_vars($arreglo['rotulo_caja'][$b]);
               $pdf->AddPage();
                      
               $entidad=$arreglo_caja['entidad'];  // Defino entidad
               
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
               $pdf->Image($logo_entidad,6,3,40,15);
               $pdf->Image($logo_entidad,147,3,40,15);

               $pdf->SetFont('Arial','B',12);
               
               $fondo               =$arreglo_caja['fondo'];               // Defino fondo
               $seccion             =$arreglo_caja['seccion'];             // Defino seccion
               $subseccion          =$arreglo_caja['subseccion'];          // Defino subseccion
               $fecha_inicial       =$arreglo_caja['fecha_inicial'];       // Defino fecha_inicial
               $fecha_final         =$arreglo_caja['fecha_final'];         // Defino fecha_final
               $numero_caja         =$arreglo_caja['numero_caja'];         // Defino numero_caja
               $cantidad_carpetas   =$arreglo_caja['cantidad_carpetas'];   // Defino cantidad_carpetas

               $cantidad_expedientes =sizeof($arreglo_caja['expedientes']);


               $fondo2              =$arreglo_caja2['fondo'];               // Defino fondo2
               $seccion2            =$arreglo_caja2['seccion'];             // Defino seccion2
               $subseccion2         =$arreglo_caja2['subseccion'];          // Defino subseccion2
               $fecha_inicial2      =$arreglo_caja2['fecha_inicial'];       // Defino fecha_inicial2
               $fecha_final2        =$arreglo_caja2['fecha_final'];         // Defino fecha_final2
               $numero_caja2        =$arreglo_caja2['numero_caja'];         // Defino numero_caja2
               $cantidad_carpetas2  =$arreglo_caja2['cantidad_carpetas'];   // Defino cantidad_carpetas2

               $cantidad_expedientes2=sizeof($arreglo_caja2['expedientes']);

               $pdf->SetLineWidth(0.5); 

               $pdf->Cell(45.5,5,'','LT',0); 
               $pdf->Cell(43,5,'Formato','LT',0,'C'); 
               $pdf->Cell(43,5,utf8_decode('Versión 1'),'LTR',0,'C');
               $pdf->Cell(7,5,'',0,0); 
               $pdf->Cell(45.5,5,'','LT',0); 
               $pdf->Cell(43,5,'Formato','LT',0,'C'); 
               $pdf->Cell(43,5,utf8_decode('Versión 1'),'LTR',1,'C'); 
               
               $pdf->Cell(45.5,5,'','LR',0); 
               $pdf->Cell(43,5,utf8_decode('Identificación'),'LR',0,'C'); 
               $pdf->Cell(43,5,utf8_decode('Fecha de Edición'),'LTR',0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->Cell(45.5,5,'','LR',0); 
               $pdf->Cell(43,5,utf8_decode('Identificación'),'LR',0,'C'); 
               $pdf->Cell(43,5,utf8_decode('Fecha de Edición'),'LTR',1,'C'); 

               $pdf->Cell(45.5,5,'','LB',0); 
               $pdf->Cell(43,5,utf8_decode('de Cajas'),'LB',0,'C'); 
               $pdf->Cell(43,5,utf8_decode('15-09-2018'),'LBR',0,'C');
               $pdf->Cell(7,5,'',0,0); 
               $pdf->Cell(45.5,5,'','LB',0); 
               $pdf->Cell(43,5,utf8_decode('de Cajas'),'LB',0,'C'); 
               $pdf->Cell(43,5,utf8_decode('15-09-2018'),'LBR',1,'C');

               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',0,'L'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Codigo de la Entidad (Remitente)'),'LR',1,'L'); 

               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($nombre_entidad),'LR',0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($nombre_entidad),'LR',1,'C'); 

               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Fondo'),'LR',0,'L'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Fondo'),'LR',1,'L'); 

               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($fondo),'LR',0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($fondo2),'LR',1,'C'); 

               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Dependencia - Seccion'),'LR',0,'L'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Dependencia - Seccion'),'LR',1,'L'); 

               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($seccion),'LR',0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($seccion2),'LR',1,'C'); 

               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Oficina Productora - Subseccion'),'LR',0,'L'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,8,utf8_decode('Oficina Productora - Subseccion'),'LR',1,'L'); 

               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($subseccion),'LR',0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(131.5,8,utf8_decode($subseccion2),'LR',1,'C'); 

               $pdf->SetFont('Arial','B',10);
               $pdf->Cell(49,5,utf8_decode('Numero Carpeta - Expediente'),1,0,'C'); 
               $pdf->Cell(82.5,5,utf8_decode('Nombre Carpeta - Expediente'),1,0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->Cell(49,5,utf8_decode('Numero Carpeta - Expediente'),1,0,'C'); 
               $pdf->Cell(82.5,5,utf8_decode('Nombre Carpeta - Expediente'),1,1,'C'); 
               
               $cantidad_expedientes=sizeof($arreglo_caja['expedientes']);
               $cantidad_expedientes2=sizeof($arreglo_caja2['expedientes']);

               for ($i=0; $i < 10; $i++) { 

                  if($i<$cantidad_expedientes){
                     $expedientes_caja =get_object_vars($arreglo_caja['expedientes'][$i]);
                     $numero_carpeta = $expedientes_caja['numero_carpeta'];
                     $nombre_carpeta = $expedientes_caja['nombre_carpeta'];
                     
                     $pdf->Cell(49,7.5,utf8_decode($numero_carpeta),1,0,'C'); 
                     $pdf->Cell(82.5,7.5,utf8_decode($nombre_carpeta),1,0,'C'); 
                  }else{
                     $pdf->Cell(49,7.5,utf8_decode(''),1,0,'C'); 
                     $pdf->Cell(82.5,7.5,utf8_decode(''),1,0,'C'); 
                  }
                  $pdf->Cell(7,8,'',0,0); 

                  if ($i<$cantidad_expedientes2){
                     $expedientes_caja2 =get_object_vars($arreglo_caja2['expedientes'][$i]);
                     $numero_carpeta2 = $expedientes_caja2['numero_carpeta'];
                     $nombre_carpeta2 = $expedientes_caja2['nombre_carpeta'];

                     $pdf->Cell(49,7.5,utf8_decode($numero_carpeta2),1,0,'C'); 
                     $pdf->Cell(82.5,7.5,utf8_decode($nombre_carpeta2),1,1,'C');       
                  }else{
                     $pdf->Cell(49,7.5,utf8_decode(''),1,0,'C'); 
                     $pdf->Cell(82.5,7.5,utf8_decode(''),1,1,'C'); 
                  }  
               }        

               $pdf->SetFont('Arial','BU',14);
               $pdf->Cell(131.5,10,utf8_decode('Fechas Extremas'),'LR',0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->Cell(131.5,10,utf8_decode('Fechas Extremas'),'LR',1,'C'); 

               $pdf->SetFont('Arial','B',14);
               $pdf->Cell(65.75,5,utf8_decode('Desde '.$fecha_inicial),'L',0,'C'); 
               $pdf->Cell(65.75,5,utf8_decode('Hasta '.$fecha_final),'R',0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->Cell(65.75,5,utf8_decode('Desde '.$fecha_inicial2),'L',0,'C'); 
               $pdf->Cell(65.75,5,utf8_decode('Hasta '.$fecha_final2),'R',1,'C'); 

               $pdf->SetFont('Arial','BU',18);
               $pdf->Cell(45.5,7,'Numero','LT',0,'C'); 
               $pdf->Cell(43,7,'Cantidad de ','LT',0,'C'); 
               $pdf->Cell(43,7,utf8_decode('Numero'),'LTR',0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->Cell(45.5,7,'Numero','LT',0,'C'); 
               $pdf->Cell(43,7,'Cantidad de ','LT',0,'C'); 
               $pdf->Cell(43,7,utf8_decode('Numero'),'LTR',1,'C'); 

               $pdf->Cell(45.5,7,'de Caja','LR',0,'C'); 
               $pdf->Cell(43,7,utf8_decode('Carpetas'),'R',0,'C'); 
               $pdf->Cell(43,7,utf8_decode('Correlativo'),'R',0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->Cell(45.5,7,'de Caja','LR',0,'C'); 
               $pdf->Cell(43,7,utf8_decode('Carpetas'),'R',0,'C'); 
               $pdf->Cell(43,7,utf8_decode('Correlativo'),'R',1,'C'); 

               $pdf->SetFont('Arial','B',20);
               $pdf->Cell(45.5,15,utf8_decode($numero_caja),1,0,'C'); 
               $pdf->Cell(43,15,utf8_decode($cantidad_carpetas),1,0,'C'); 
               $pdf->Cell(43,15,'',1,0,'C'); 
               $pdf->Cell(7,5,'',0,0); 
               $pdf->Cell(45.5,15,utf8_decode($numero_caja2),1,0,'C'); 
               $pdf->Cell(43,15,utf8_decode($cantidad_carpetas2),1,0,'C');       
               $pdf->Cell(43,15,'',1,1,'C'); 
      
            }
         }
      }
   $pdf->Output();
}
 
/* Aqui recibe el json por post y hace el decode */
   $json_recibido=$_POST['rotulos_cajas'];

   $decode =json_decode($json_recibido);
   $arreglo = get_object_vars($decode); 
   $cantidad_rotulos = sizeof($arreglo['rotulo_caja']);
/* Hasta aqui recibe el json por post y hace el decode */

  imprimir_hoja($cantidad_rotulos,$arreglo);
?>