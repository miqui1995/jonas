<?php
/* genera_fecha es la clase que obtiene un string con una fecha y lo convierte en diferentes formatos */
// date_default_timezone_set("America/Bogota");

class genera_fecha
{

/* Traduce una fecha en formato mm/dd/yyy a formato texto en castellano.
   Desde la pagina hacemos el include de "genera_fecha.php"; y llamaremos a la variable $fecha_hoy (por ejemplo) 
   ó echo traducefecha("11/15/2003");  
   Donde la fecha ponemos la variable que queremos traducir en formato mm/dd/yyyy 
*/ 

/* Funcion para traducir fecha desde formato 'Y-m-d H:i:s' (2018-12-08 11:33:58) a 08 de Diciembre de 2018*/
   
    // $timestamp =  date("Y-m-d.h:m:s"); // Traduce fecha formato "2016-06-05.10:06:05"
    function traduce_fecha_letra_segundos($fecha){  // Devuelve (28 de Diciembre de 2018 a las 17:45:21)

        $fecha_por_traducir = explode("-",$fecha);
        $year               = $fecha_por_traducir[0]; // año
        $mes                = $fecha_por_traducir[1]; // mes

        switch($mes){ 
            case "01": 
              $mes="Enero"; 
              break; 
            case "02": 
              $mes="Febrero"; 
              break; 
            case "03": 
              $mes="Marzo"; 
              break; 
            case "04": 
              $mes="Abril"; 
              break; 
            case "05": 
              $mes="Mayo"; 
              break; 
            case "06": 
              $mes="Junio"; 
              break; 
            case "07": 
              $mes="Julio"; 
              break; 
            case "08": 
              $mes="Agosto"; 
              break; 
            case "09": 
               $mes="Septiembre"; 
               break; 
            case "10": 
              $mes="Octubre"; 
              break; 
            case "11": 
              $mes="Noviembre"; 
              break; 
            case "12": 
              $mes="Diciembre"; 
              break; 
        } 
        $dia2= $fecha_por_traducir[2]; // dia
        $dia1 = explode(" ",$dia2);
        $dia = $dia1[0];

        $hora1 = explode(" ",$fecha);
        $hora = $hora1[1];

        $fecha_traducida = "$dia de $mes de $year a las $hora";

        return $fecha_traducida;
    }
/* Fin funcion para traducir fecha desde formato 'Y-m-d H:i:s' (2018-12-08 11:33:58) */
/* Funcion para traducir fecha desde formato d-m-Y (15-12-2018) */
 function traduce_fecha_letra($fecha){  // Devuelve (28 de Diciembre de 2018)

        $fecha_por_traducir = explode("-",$fecha);
        $year               = $fecha_por_traducir[0]; // año
        $mes                = $fecha_por_traducir[1]; // mes

        switch($mes){ 
            case "01": 
              $mes="Enero"; 
              break; 
            case "02": 
              $mes="Febrero"; 
              break; 
            case "03": 
              $mes="Marzo"; 
              break; 
            case "04": 
              $mes="Abril"; 
              break; 
            case "05": 
              $mes="Mayo"; 
              break; 
            case "06": 
              $mes="Junio"; 
              break; 
            case "07": 
              $mes="Julio"; 
              break; 
            case "08": 
              $mes="Agosto"; 
              break; 
            case "09": 
               $mes="Septiembre"; 
               break; 
            case "10": 
              $mes="Octubre"; 
              break; 
            case "11": 
              $mes="Noviembre"; 
              break; 
            case "12": 
              $mes="Diciembre"; 
              break; 
        } 
        $dia= $fecha_por_traducir[2]; // dia
       
        $fecha_traducida = "$dia de $mes de $year";

        return $fecha_traducida;
    }
/* Fin funcion para traducir fecha desde formato d-m-Y (15-12-2018) */
  
function traducefecha($fecha){ // Traduce fecha formato "Jueves 05 de Mayo de 2016"
  if (strlen(trim($fecha))==0)  
    return ("<Fecha no especificada>");
      
  $fecha= strtotime($fecha); // convierte la fecha de formato mm/dd/yyyy a marca de tiempo 
  $diasemana=date("w", $fecha);// se obtiene el número de dia de la semana. El 0 es domingo 
  
  switch ($diasemana){ 
    case "0": 
      $diasemana="Domingo"; 
      break; 
    case "1": 
      $diasemana="Lunes"; 
      break; 
    case "2": 
      $diasemana="Martes"; 
      break; 
    case "3": 
      $diasemana="Miercoles"; 
      break; 
    case "4": 
      $diasemana="Jueves"; 
      break; 
    case "5": 
      $diasemana="Viernes"; 
      break; 
    case "6": 
      $diasemana="Sabado"; 
      break; 
  } 

  $dia=date("d",$fecha); // día de mes en número 
  $mes=date("m",$fecha); // número de mes de 01 a 12 
  
  switch($mes){ 
    case "01": 
      $mes="Enero"; 
      break; 
    case "02": 
      $mes="Febrero"; 
      break; 
    case "03": 
      $mes="Marzo"; 
      break; 
    case "04": 
      $mes="Abril"; 
      break; 
    case "05": 
      $mes="Mayo"; 
      break; 
    case "06": 
      $mes="Junio"; 
      break; 
    case "07": 
      $mes="Julio"; 
      break; 
    case "08": 
      $mes="Agosto"; 
      break; 
    case "09": 
       $mes="Septiembre"; 
       break; 
    case "10": 
      $mes="Octubre"; 
      break; 
    case "11": 
      $mes="Noviembre"; 
      break; 
    case "12": 
      $mes="Diciembre"; 
      break; 
  } 

  $year=date("Y",$fecha); // Se obtiene el año en formato 4 digitos 

  $hora     = date("h",$fecha); // Se obtiene la hora
  $minutos  = date("i",$fecha); // Se obtiene los minutos
  $segundos = date("s",$fecha); // Se obtiene los segundos
  $am_pm    = date("A",$fecha); // Se obtiene AM o PM


  $fecha= $diasemana." ".$dia." de ".$mes." de ".$year." a las ".$hora.":".$minutos.":".$segundos." ".$am_pm; // SE concatena el resultado en una sola cadena 
  return $fecha; //retorna la fecha  
}
	
function traducefecha_sinDia($fecha) { // Traduce fecha formato "cinco dia(s) de mes de Mayo de 2016"
  if (strlen(trim($fecha))==0)
    return ("<Fecha no especificada>");
        		
  $fecha= strtotime($fecha); // convierte la fecha de formato mm/dd/yyyy a marca de tiempo 
  $dia=date("d",$fecha); // día de mes en número 
  $mes=date("m",$fecha); // número de mes de 01 a 12 

    switch($mes){ 
      case "01": 
        $mes="Enero"; 
        break; 
      case "02": 
        $mes="Febrero"; 
        break; 
      case "03": 
        $mes="Marzo"; 
        break; 
      case "04": 
        $mes="Abril"; 
        break; 
      case "05": 
        $mes="Mayo"; 
        break; 
      case "06": 
        $mes="Junio"; 
        break; 
      case "07": 
        $mes="Julio"; 
        break; 
      case "08": 
        $mes="Agosto"; 
        break; 
      case "09": 
        $mes="Septiembre"; 
        break; 
      case "10": 
        $mes="Octubre"; 
        break; 
      case "11": 
        $mes="Noviembre"; 
        break; 
      case "12": 
        $mes="Diciembre"; 
        break; 
    }
    switch($dia){ 
      case "1": 
        $dia="un"; 
        break; 
      case "2": 
        $dia="dos"; 
        break; 
      case "3": 
        $dia="tres"; 
        break; 
      case "4": 
        $dia="cuatro"; 
        break; 
      case "5": 
        $dia="cinco"; 
        break; 
      case "6": 
        $dia="seis"; 
        break; 
      case "7": 
        $dia="siete"; 
        break; 
      case "8": 
        $dia="ocho"; 
        break; 
      case "9": 
        $dia="nueve"; 
        break; 
      case "10": 
        $dia="diez"; 
        break; 
      case "11": 
        $dia="once"; 
        break; 
      case "12": 
        $dia="doce"; 
        break; 
	    case "13": 
        $dia="trece"; 
        break; 
      case "14": 
        $dia="catorce"; 
        break;
      case "15": 
        $dia="quince"; 
        break;
      case "16": 
        $dia="dieciseis"; 
        break; 
      case "17": 
        $dia="diecisiete"; 
        break; 
      case "18": 
        $dia="dieciocho"; 
        break; 
      case "19": 
        $dia="dicinueve"; 
        break; 
      case "20": 
        $dia="veinte"; 
        break; 
      case "21": 
        $dia="veintiun"; 
        break; 
      case "22": 
        $dia="veintidos"; 
        break; 
      case "23": 
        $dia="veintitres"; 
        break; 
      case "24": 
        $dia="veinticuatro"; 
        break; 
      case "25": 
        $dia="veinticinco"; 
        break; 
      case "26": 
        $dia="veintiseis"; 
        break; 
      case "27": 
        $dia="veintisiete"; 
        break;
      case "28": 
        $dia="veintiocho"; 
        break;
      case "29": 
        $dia="veintiueve"; 
        break;
      case "30": 
        $dia="treinta"; 
        break;
      case "31": 
        $dia="treinta y un"; 
        break; 	
    }	  

    $year=date("Y",$fecha); // Se obtiene el año en formato 4 digitos 
    $fecha= $dia." dia(s) del mes de ".$mes." de ".$year; // Se une el resultado en una sola cadena 
    return $fecha; //retorna la fecha 

  }//Fin de traducefecha_sindia()

	function traducefechaDocto($fecha){ // convierte "date" en formato "cinco de Mayo de 2016"  
    	
    if (strlen(trim($fecha))==0)	
    	return ("<NO ESPECIFICADA>");
    	  		
    $fecha= strtotime(trim($fecha)); // convierte la fecha de formato mm/dd/yyyy a marca de tiempo      
    $dia=date("d",$fecha); // día de mes en número 
    $mes=date("m",$fecha); // número de mes de 01 a 12 
       
    switch($mes){ 
       case "01": 
          $mes="Enero"; 
          break; 
       case "02": 
          $mes="Febrero"; 
          break; 
       case "03": 
          $mes="Marzo"; 
          break; 
       case "04": 
          $mes="Abril"; 
          break; 
       case "05": 
          $mes="Mayo"; 
          break; 
       case "06": 
          $mes="Junio"; 
          break; 
       case "07": 
          $mes="Julio"; 
          break; 
       case "08": 
          $mes="Agosto"; 
          break; 
       case "09": 
          $mes="Septiembre"; 
          break; 
       case "10": 
          $mes="Octubre"; 
          break; 
       case "11": 
          $mes="Noviembre"; 
          break; 
       case "12": 
          $mes="Diciembre"; 
          break; 
    }
	  switch($dia){ 
       case "1": 
          $dia="Primero"; 
          break; 
       case "2": 
          $dia="Dos"; 
          break; 
       case "3": 
          $dia="Tres"; 
          break; 
       case "4": 
          $dia="Cuatro"; 
          break; 
       case "5": 
          $dia="Cinco"; 
          break; 
       case "6": 
          $dia="Seis"; 
          break; 
       case "7": 
          $dia="Siete"; 
          break; 
       case "8": 
          $dia="Ocho"; 
          break; 
       case "9": 
          $dia="Nueve"; 
          break; 
       case "10": 
          $dia="Diez"; 
          break; 
       case "11": 
          $dia="Once"; 
          break; 
       case "12": 
          $dia="Doce"; 
          break; 
			case "13": 
          $dia="Trece"; 
          break; 
			case "14": 
          $dia="Catorce"; 
          break;
			case "15": 
          $dia="Quince"; 
          break; 
			case "16": 
          $dia="Dieciseis"; 
          break; 
       case "17": 
          $dia="Diecisiete"; 
          break; 
       case "18": 
          $dia="Dieciocho"; 
          break; 
       case "19": 
          $dia="Dicinueve"; 
          break; 
       case "20": 
          $dia="Veinte"; 
          break; 
       case "21": 
          $dia="Veintiuno"; 
          break; 
       case "22": 
          $dia="Veintidos"; 
          break; 
       case "23": 
          $dia="Veintitres"; 
          break; 
       case "24": 
          $dia="Veinticuatro"; 
          break; 
       case "25": 
          $dia="Veinticinco"; 
          break; 
       case "26": 
          $dia="Veintiseis"; 
          break; 
       case "27": 
          $dia="Veintisiete"; 
          break; 
			case "28": 
          $dia="Veintiocho"; 
          break; 
			case "29": 
          $dia="Veintiueve"; 
          break;
			case "30": 
          $dia="Treinta"; 
          break; 
			case "31": 
          $dia="Treinta y un"; 
          break; 			
    }	 

    $year=date("Y",$fecha); // Se obtiene el año en formato 4 digitos 
    $fecha= $dia." de ".$mes." de ".$year; // Se une el resultado en una sola cadena 
    return $fecha; //retorna la fecha  
    } 
}
    $b          = new genera_fecha();
    $date       =  date("Y-m-d");
    $timestamp  =  date("Y-m-d h:i:s A"); // Traduce fecha formato "2020-04-07 10:44:40 PM"

    $fecha_e            = $b->traducefecha($date); // Traduce fecha formato "Jueves 05 de Mayo de 2016"
    $fecha_e2           = $b->traduce_fecha_letra($date); // Traduce fecha formato "Jueves 05 de Mayo de 2016"
    $fechaDocumento2    = $b->traducefecha_sinDia($date); // Traduce fecha formato "cinco dia(s) de mes de Mayo de 2016"
    $fechaDocumento     = $b->traducefechaDocto($date); // Traduce fecha formato "Cinco de Mayo de 2016 "
    //echo "fechas $date <br> fecha_hoy $fecha_hoy <br>fechaDocumento2 $fechaDocumento2 <br>fechaDocumento $fechaDocumento<br>";
?>
   
