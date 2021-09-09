<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/d3js/5.15.1/d3.min.js"></script>

<?php 
header("Access-Control-Allow-Origin: *");
/* Este archivo es un puente para recibir mediante POST un JSON con datos específicos con el fin de reenviarlo a la ip de Jonas Ejercito. 
Luego de hacer la petición, la respuesta la devuelve como respuesta ya sea un JSON o un codigo de error.
 */

/* Primero recibe el json */
$json = $_POST["json"]; 


?>

<script type="text/javascript">
	var json1 ='<?php echo $json;?>';

  	$.ajax({
        type 	: 'POST',
        url 	: '172.22.3.35/jonas_desarrollo/ws/mostrar_datos.php',
        data 	: {
            'json'  : json1
        },
        //se comprueba la respuesta de ajax enviado
        success:function(json_resultante){
        	//  console.log(json_resultante);
        	return json_resultante;
    	}
 	})            

</script>