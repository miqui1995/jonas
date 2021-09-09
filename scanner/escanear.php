<?php 
	require_once("../login/validar_inactividad.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Menu Scanner</title>
	<script type="text/javascript" src="include/js/funciones_radicacion_entrada.js"></script>
	<link rel="stylesheet" href="include/css/estilos_radicacion_entrada.css">
</head>
<body>
	<script type="text/javascript">$("#search_radicado").focus();</script>
	<div class="center" id="logo">
		<h1 style="margin-top:-10px;" id="titulo_escanear">Asociar Imagen Radicado</h1><hr>
	</div>
	
 	<div id="desplegable_resultados"></div>
	<!-- <div id="comprueba_pdf"></div> -->
<script>
  function getTimeAJAX() {
        if($("#titulo_escanear").is(":visible")){
            // Se guarda en una variable el resultado de la consulta AJAx    
            var time = $.ajax({
                url			: 'scanner/time.php', 	// indicamos la ruta donde se genera la consulta de la carpeta
                data        : {'accion' : 'verifica_pdf_por_cargar'}, // Se define accion para mostrar imagen
                dataType	: 'text',				// indicamos que es de tipo texto plano
                async		: false     			// ponemos el parámetro asyn a falso
            }).responseText;
            //actualizamos el div que nos mostrará la hora actual
            document.getElementById("lista_documentos_escaneados").innerHTML = time;
        }else{
            clearInterval(this);
            return false;
        }
    }
    //con esta funcion llamamos a la función getTimeAJAX cada 5 segundos para actualizar el div que mostrará la hora
    setInterval(getTimeAJAX,5000);    
</script>

<html>
    <body>
    	<center id="contenedor_boton_enviar_imagen"></center>
    	<div id='error_adjunto_pdf' class="errores">El archivo que intenta cargar no tiene formato (.pdf) ó no se encuentra el número de radicado en la base de datos. </div>
    	<div id="contenedor_documentos_escaneados">
	    	<div id='lista_documentos_escaneados' style="float:left; width: 100%; max-height: 500px; overflow: auto;"></div>
	    	<div id="visor_pdf" class='hidden' style="float:left; width: 100%; max-height: 500px; overflow: auto;"></div>
    	</div>
    	<br>
    </body>
</html>		
</body>
</html>