<?php
/** 
* @brief Este archivo es la API que recibe desde un aplicativo externo teniendo en cuenta la arquitectura REST y RESTFULL un JSON que no recibe archivos en "base64", recolecta datos para ser enviados a su procesamiento donde se devolera un valor json como verdadero si su ejecucion fue correcta. 
En caso que la ejecucion fuese incorrecta el valor devuelto sería un mensaje de error. 
El request debe ser un objeto en formato urldecode y debe contener los atributos codigo_entidad, numero_radicado y canal_respuesta
*
* Un ejemplo del formato que se recibe es el siguiente:
************************************
@param urldecode
    $codigo_entidad  = $_GET["codigo_entidad"];
    $numero_radicado = $_GET["numero_radicado"];
    $canal_respuesta = $_GET["canal_respuesta"];

    json_object json{
        "codigo_entidad": GC1, 
        "numero_radicado": 2020GC1111000000151, 
        "canal_respuesta": mail,
    }
************************************
@return json response json_final

$json_resultante = array("codigo_entidad"=>"$codigo_entidad","numero_radicado"=>"$numero_radicado","remitente"=>"$remitente","dignatario"=>"$dignatario","fecha_radicado"=>"$fecha_radicado","canal_respuesta"=>"$canal_respuesta","direccion_respuesta"=>"$direccion_respuesta","asunto"=>"$asunto");
$json_final = json_encode($json_resultante);
Ejemplo del json response :
{
    "json_final":{
        "codigo_entidad" : "GC1",
        "numero_radicado": 2020GC1111000000151,
        "remitente": "Acueducto Agua Y Alcantarillado De Bogota", 
        "dignatario": "Hernan Javier Ruge Padilla",
        "fecha_radicado": "2019-06-21 11:54:07",
        "canal_respuesta": "mail",
        "direccion_respuesta": "acueducto@com.co",
        "asunto" : "Asunto de prueba envio mail 01",
        "estado" : "en_tramite"
    }
}
************************************
Ejemplo de la solicitud response en caso de error :
¡Error en la consulta a base de datos, verificar url que contenga parametros solicitados y estructura acorde.
************************************
* @author Gilberto Contreras Cardenas
* @date Marzo 2020
*/

    //se reciben las variables de la url
    $codigo_entidad1    = $_GET["codigo_entidad"];
    $numero_radicado1   = $_GET["numero_radicado"];
    $canal_respuesta1   = $_GET["canal_respuesta"];
    
    switch($codigo_entidad1){
        case 'AV1':
            $ruta_raiz      = ".";
            // $ruta_raiz      = "sgd/jonas";
            break;
        case 'GC1':
            $ruta_raiz      = ".";
            // $ruta_raiz      = "sgd/jonas";
            break;
              
        default:
            echo "<script>alert('El codigo de entidad no se encuentra configurado. Comuníquese con el administrador del sistema.')</script>";
            break;
    }
    $repositorio    = "$ruta_raiz/ws/mostrar_datos.php";
?>
<!DOCTYPE html>
<html >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Consulta web por radicado :: Software de Gestion Documental JONAS</title>
    <?php echo "<link href='$ruta_raiz/imagenes/logo3.png' type='image/x-icon' rel='shorttcut icon'/>" ?>  
<!--Esta es la parte que se visualiza en la pestaña del navegador-->
        <title>Ingreso al SGD JONAS</title>

<!--Esta es el encabezado del archivo donde llamo a los archivos que se necesitan para usar jquery, css-->
    <?php echo "
    <link rel='stylesheet' href='$ruta_raiz/include/css/estilos_menu_principal.css'>
    <script src='$ruta_raiz/include/js/jquery.js' type='text/javascript'></script>
    "?>
    <script type='text/javascript'>
        //se define la variable json, para enviar
        json        ='{"codigo_entidad": "<?php echo $codigo_entidad1; ?>", "numero_radicado": "<?php echo $numero_radicado1; ?>", "canal_respuesta": "<?php echo $canal_respuesta1; ?>"}';
        // Se define la ruta a la que va a enviar ajax
        repositorio1='<?php echo $repositorio;?>';

        // Se envían datos a pagina externa 
        function enviar_metadatos(){
            $.ajax({
                type    : 'POST',
                url     : repositorio1,
                data    : {
                    'json'  : json
                },
            //se comprueba la respuesta de ajax enviado
                success:function(json_resultante){
                    if(json_resultante=="sin_resultados"){                        
                        alert("Numero de radicado no se encuentra en base de datos");
                         $("#tabla_resultado").append("<table border='0' width='100%'><tr><td class='descripcion center' colspan='2'><h2>Radicado "+'<?php echo $numero_radicado1; ?>'+" no existe en la base de datos</h2></td></tr></table>")
                    }else{
                        var json_js = "["+json_resultante+"]";
                        var contenido_array = JSON.parse(json_js);

                        var numero_radicado     = contenido_array[0].numero_radicado;
                        var codigo_entidad      = contenido_array[0].codigo_entidad;
                        var remitente           = contenido_array[0].remitente;
                        var dignatario          = contenido_array[0].dignatario;
                        var fecha_radicado      = contenido_array[0].fecha_radicado;
                        var canal_respuesta     = contenido_array[0].canal_respuesta;
                        var direccion_respuesta = contenido_array[0].direccion_respuesta;
                        var asunto              = contenido_array[0].asunto;
                        var estado              = contenido_array[0].estado;
                        var login               = contenido_array[0].login;
                        var fecha               = contenido_array[0].fecha;
                        var comentario          = contenido_array[0].comentario;
                        var nombre_dependencia  = contenido_array[0].nombre_dependencia;

                        switch(estado){
                            case "no_requiere_respuesta":
                                var complemento = "El usuario "+login+" de la dependencia "+nombre_dependencia+" el dia "+fecha+" ha marcado su solicitud como <b>No requiere respuesta</b> bajo la siguiente justificación: <br><i>"+comentario+"</i>";

                            break;
                            case "en_tramite":
                                var complemento = "Documento en trámite. Lo está gestionando <br><b>"+login+"</b> de la dependencia <b>"+nombre_dependencia+"</b>";
                            break;
                            case "tramitado":
                                var complemento = "<div style='background-color:green; color:#FFFFFF; padding:10px;'>"+comentario+"</div>";
                            break;
                        }
                        
                        console.log(json_resultante);                  
                        //con el la respuesta se crea tabla para mostar datos traidos
                        $("#tabla_resultado").append("<table border='0' width='100%'><tr><td class='descripcion center' colspan='2'><h2>Radicado: "+numero_radicado+"</h2></td></tr><tr><td class='descripcion'>Codigo entidad : </td><td class='detalle'>"+codigo_entidad+"</td></tr><tr><td class='descripcion'>Numero radicado: </td><td class='detalle'>"+numero_radicado+"</td></tr><tr><td class='descripcion'>Remitente: </td><td class='detalle'>"+remitente+"</td></tr><tr><td class='descripcion'>Dignatario: </td><td class='detalle'>"+dignatario+"</td></tr><tr><td class='descripcion'>Fecha radicado: </td><td class='detalle'>"+fecha_radicado+"</td></tr><tr><td class='descripcion'>Canal respuesta: </td><td class='detalle'>"+canal_respuesta+"</td></tr><tr><td class='descripcion'>Direccion respuesta: </td><td class='detalle'>"+direccion_respuesta+"</td></tr><tr><td class='descripcion'>Asunto: </td><td class='detalle'>"+asunto+"</td></tr><tr><td class='descripcion'>Estado del trámite: </td><td class='detalle'>"+estado+"</td></tr><tr><td class='detalle' colspan='2'>"+complemento+"</td></tr></table>");
                    }
                }
            })
        }
    </script>
    <style>
        body{
            <?php echo "background-image    : url('$ruta_raiz/imagenes/background_main.png');"; ?>
            background-repeat   : no-repeat;
            background-size     : cover;
            padding             : 20px; 
        }
        #main_div{
            <?php echo "background-image    : url('$ruta_raiz/imagenes/bg-modal.png');"; ?>
            border-radius       : 20px; 
            font-size           : 30px;
            padding             : 20px;
            width               : auto;
        }
        #div_img{
            float   : left;
            width   : 30%
        }
        #div_img img{
            width   : 100%;
        }
        #div_name{
            float           : left;
            padding-left    : 10%;
        }
        #tabla_resultado{
            width: 100%;
        }
        
        /* Se inicia estilos al form de municipios cuando es movil o ventana menos de 768px */          
        @media screen and (max-width: 768px){
            #div_img{
                width       : 100%
            }
            #main_div{
                font-size   : 18px;
            }
        }
    </style>   
</head>
<body onload="enviar_metadatos()">
    <div id='main_div'>
        <div id='div_img'>
            <?php echo "<img src='$ruta_raiz/imagenes/encabezado_transparente.png'>" ?>
        </div> 
        <div id='div_name'>
            <h1 style='color: #FFFFFF;text-align:center;' >Consulta Web Radicado</h1>
        </div>
   
        <div id="tabla_resultado">
    </div>    
</body>
</html>
