<?php 
/**
* @brief Este archivo es incluido en el index.php y desde principal3.php 
* @param $fecha_especial que recibe mediante sesión inicializada en los archivos que llaman este archivo mediante require_once()
* @author Johnnatan Rodriguez Pinto
* @date Diciembre 2019
*/
	$fecha_especial = $_SESSION['fechas_especiales'];
	/* Se define mediante un switch las posibles opciones de fecha especial y ejecuta cambios a la interfaz dependiendo de ésto. */
	switch ($fecha_especial) {
		case 'navidad':
?>
			<!-- Script para navidad -->
			<script type='text/javascript'>
			var fallObjects=new Array();function newObject(url,height,width){fallObjects[fallObjects.length]=new Array(url,height,width);}

			var numObjs=30, waft=50, fallSpeed=5, wind=0;
			newObject("imagenes/nieve1.png",22,22);
			newObject("imagenes/nieve2.png",22,22);

			function winSize(){winWidth=(moz)?window.innerWidth-180:document.body.clientWidth-180;winHeight=(moz)?window.innerHeight+500:document.body.clientHeight+500;}
			function winOfy(){winOffset=(moz)?window.pageYOffset:document.body.scrollTop;}
			function fallObject(num,vari,nu){
			objects[num]=new Array(parseInt(Math.random()*(winWidth-waft)),-30,(parseInt(Math.random()*waft))*((Math.random()>0.5)?1:-1),0.02+Math.random()/20,0,1+parseInt(Math.random()*fallSpeed),vari,fallObjects[vari][1],fallObjects[vari][2]);
			if(nu==1){document.write('<img id="fO'+i+'" style="position:fixed; z-index:2000;" src="'+fallObjects[vari][0]+'">'); }
			}
			function fall(){
				for(i=0;i<numObjs;i++){
					var fallingObject=document.getElementById('fO'+i);
					if((objects[i][1]>(winHeight-(objects[i][5]+objects[i][7])))||(objects[i][0]>(winWidth-(objects[i][2]+objects[i][8])))){fallObject(i,objects[i][6],0);}
					objects[i][0]+=wind;objects[i][1]+=objects[i][5];objects[i][4]+=objects[i][3];
					with(fallingObject.style){ top=objects[i][1]+winOffset+'px';left=objects[i][0]+(objects[i][2]*Math.cos(objects[i][4]))+'px';}
				}
				setTimeout("fall()",31);
			}
			var objects=new Array(),winOffset=0,winHeight,winWidth,togvis,moz=(document.getElementById&&!document.all)?1:0;winSize();
			for (i=0;i<numObjs;i++){fallObject(i,parseInt(Math.random()*fallObjects.length),1);}
			fall();

			$("#icono_usuario").html("<img src='imagenes/iconos/icono_user_navidad.png' class='icono'>")
			$("#encabezado_transparente2").html("<img src='imagenes/encabezado_transparente_navidad.png' id='logo_principal'>");
			document.getElementById("encabezado").style.backgroundImage = "url(\"imagenes/encabezado_principal_navidad.png\")";
			</script>
			<!-- Fin script navidad -->
<?php			
			break;
		case 'licencia':  // Se requiere que se defina en este archivo la fecha límite de la licencia en la variable $fecha_entrada y el campo con "soporte desde el xxx" y en el archivo login/validar_inactividad.php igual.
		
			// Desde aqui se define la alerta por la licencia
			$fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
			$fecha_entrada = strtotime("20-02-2019 00:00:00");

			$fecha_diff= round(((($fecha_entrada-$fecha_actual)/60)/60)/24);

			$alerta_licencia="<div style='background: red; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.3); 	box-shadow: 0 0 10px rgba(0,0,0,0.3); color: #FFFFFF; font-size: 15px; padding: 5px;position: relative; text-align: center;'>Su entidad no tiene soporte desde el xxx. Si no renueva soporte, su acceso puede ser inactivado dentro de $fecha_diff días. <br>Comuníquese por favor urgente con Gammacorp SAS.</div>";
			// Hasta aqui se define la alerta por la licencia
			echo "<script>$('#avisos_principal').html(\"$alerta_licencia\")</script>";
			break;
		default:
			break;
	}
 ?>