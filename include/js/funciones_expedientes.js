/* Script para ventana modal - Tecla Esc */
window.addEventListener("keyup", function(event) {
    var codigo = event.keyCode || event.which;
    if (codigo == 27) {
        cerrarVentanaCrearExpediente();
        cerrarVentanaModificarExpediente()
    }
    if (codigo == 8) { // Opcion para restringir que la tecla backspace da atras en el navegador.
        if (history.forward(1)) {
            location.replace(history.forward(1));
        }
    }
}, false);
/* Fin script para ventana modal - Tecla Esc */
/******************************************************************************************/
/* Principal ******************************************************************************/
/******************************************************************************************/
/* Script para buscador del administrador de expedientes */
var timerid = "";
$(function buscador_expediente() {
    $('#search_expediente').focus();
    $('#search_expediente').on("input", function(e) { // Accion que se activa cuando se digita #search_expediente
        // $('#desplegable_resultados').html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
        loading('desplegable_resultados');
        var envio_expediente = $(this).val();
        if ($(this).data("lastval") != envio_expediente) {
            $(this).data("lastval", envio_expediente);
            clearTimeout(timerid);
            timerid = setTimeout(function() {
                if (envio_expediente.length > 2 && envio_expediente.length < 100) {
                    $.ajax({
                        type: 'POST',
                        url: 'admin_expedientes/buscador_expedientes.php',
                        data: {
                            'search_expediente': envio_expediente,
                            'desde_formulario': '1'
                        },
                        success: function(resp) {
                            if (resp != "") {
                                $('#desplegable_resultados').html(resp);
                            }
                        }
                    })
                } else {
                    $('#desplegable_resultados').html('<h4>Para iniciar la búsqueda debe ingresar por lo menos 3 caracteres.</h4> ');
                }
                if (envio_expediente.length > 100) {
                    $('#desplegable_resultados').html('<h4>La busqueda debe tener 100 caracteres maximo. Revise por favor</h4>');
                }
            }, 1000);
        };
    });
});
/* Fin script para buscador del administrador de expediente */
/* Script para mostrar secuencia de expediente temporal */
function genera_consecutivo_temporal(tipo) {
    var dependencia_expediente1     = $("#dependencia_expediente").val();
    var year1                       = $("#year_expediente").val();
    var serie_expediente1           = $("#serie_expediente").val();
    var subserie_expediente1        = $("#subserie_expediente").val();
    $.ajax({
        type    : 'POST',
        url     : 'admin_expedientes/buscador_expedientes.php',
        data    : {
            'dependencia_expediente'    : dependencia_expediente1,
            'year'                      : year1,
            'serie_expediente'          : serie_expediente1,
            'subserie_expediente'       : subserie_expediente1
        },
        success: function(resp) {
            if (resp != "") {
                // console.log(resp)
                if (tipo == '1') {
                    $("#consecutivo_expediente").val(resp)
                    $("#nombre_expediente").focus();
                } else {
                    $("#nombre_expediente_mod").focus();
                }
            }
        }
    })
}
/* Fin script para mostrar secuencia de expediente temporal */
/* Script para generar listado series */
function genera_lista_serie() {
    var codigo_dependencia = $("#dependencia_expediente").val();

    $.ajax({
        type    : 'POST',
        url     : 'admin_expedientes/buscador_expedientes.php',
        data    : {
            'codigo_dependencia_serie'  : codigo_dependencia
        },
        success: function(resp) {
            if (resp != "") {
                $("#serie_expediente").html(resp);
            }
        }
    })
    // console.log(codigo_dependencia + " - " + codigo_serie);
}
/* Fin script para generar listado subseries */
/* Script para generar listado subseries */
function genera_lista_subserie() {
    var codigo_dependencia  = $("#dependencia_expediente").val();
    var codigo_serie        = $("#serie_expediente").val();
    $.ajax({
        type    : 'POST',
        url     : 'admin_expedientes/buscador_expedientes.php',
        data    : {
            'codigo_dependencia'    : codigo_dependencia,
            'codigo_serie'          : codigo_serie
        },
        success: function(resp) {
            if (resp != "") {
                $("#subserie_expediente").html(resp);
            }
        }
    })
    // console.log(codigo_dependencia + " - " + codigo_serie);
}
/* Fin script para generar listado subseries */
/***********************************************************************************************/
/* Agregar Nuevo Expediente ********************************************************************/
/***********************************************************************************************/
/*Script para ventana modal Agregar Nuevo Expediente*/
function abrirVentanaCrearExpediente() {
    $("#nombre_expediente").val($("#search_expediente").val().toUpperCase());
    $("#ventana").slideDown("slow");
    genera_consecutivo_temporal(1);
    genera_lista_serie();
    $("#year_exp").focus();
    $("#boton_crear_expediente").html("<input type='button' value='Crear Expediente' id='bEnviar_expediente' class='botones' onclick='submit_agregar_expediente()'>");
    $("#contenido").css({
        'z-index': '100'
    }); // Modifico estilo para sobreponer ventana modal 

   // $("#cont_crear_documentos").html("");
}

function cerrarVentanaCrearExpediente() {
    $("#ventana").slideUp("slow");
    $('.art').slideUp("slow");
    $('#search_expediente').focus();
}
/* Fin script para ventana modal Agregar Nuevo Expediente */
/* Script para ocultar errores y continuar consulta - Formulario Agregar Expediente */
function oculta_errores() {
    $("#error_nombre_expediente").slideUp("slow");
    $("#valida_minimo_nombre").slideUp("slow");
    $("#valida_maximo_nombre").slideUp("slow");
    $("#nombre_expediente_ya_existe").slideUp("slow");
}

function oculta_error_subserie() {
    $("#error_subserie_2").slideUp("slow");
}
/* Fin script para ocultar errores y continuar consulta - Formulario Agregar Expediente */
/* Script para buscar expediente desde campo "nombre_expediente" del Formulario Agregar Nuevo Expediente */
$("#nombre_expediente").on("input", function(e) { // Accion que se activa cuando se digita #nombre_expediente
    espacios_formulario('nombre_expediente', 'mayusculas', 0);
    loading('sugerencia_nombre_expediente');
    oculta_errores();
    var nom_niv = $(this).val();
    if ($(this).data("lastval") != nom_niv) {
        $(this).data("lastval", nom_niv);
        clearTimeout(timerid);
        timerid = setTimeout(function() {
            validar_input('nombre_expediente');
            $.ajax({
                type: 'POST',
                url: 'admin_expedientes/buscador_expedientes.php',
                data: {
                    'search_nom_expediente': nom_niv,
                    'desde_formulario': '1'
                },
                success: function(resp) {
                    if (resp != "") {
                        $('#sugerencia_nombre_expediente').html(resp);
                    }
                }
            })
        }, 1000);
    };
});

/* Validación que los campos de Formulario Agregar Nuevo Nivel (Submit) */
/*
function validar_grabar_expediente() {
    $(".errores").slideUp("slow");
    var nombre_expediente1 = $('#nombre_expediente').val();
    if (nombre_expediente1 == "") {
        $("#error_nombre_expediente").slideDown("slow");
        $("#nombre_expediente").focus();
          return false;
    } else {
        if (nombre_expediente1.length < 6) {
            $("#valida_minimo_nombre").slideDown("slow");
            $("#nombre_expediente").focus();
            return false;
        } else {
            if (nombre_expediente1.length > 600) {
                $("#valida_maximo_nombre").slideDown("slow");
                $("#nombre_expediente").focus();
                return false;
            } else {
                return true;
            }
        }
    }
}
*/

/**************************************************************
Inicio funcion validar_serie_subserie()  mostrar botones de cargar documentos que debe tener un contrato en el div id="contenedor_crear_documentos"*/
/**
* @class La funcion validar_serie_subserie() se utiliza para mostrar botones de cargar documentos que debe tener un contrato dependiendo la serie y subserie que tenga el expediente que se va a crear. Se invoca al cambiar el select "#subserie_expediente"
* @description consulta el valor de la serie-subserie y si la serie corresponde al codigo de Contratos (16) y la subserie Prestación de Servicios (002) despliega en el div id="contenedor_crear_documentos" el contenido del archivo "admin_expedientes/documentos_contrato.php"
* @param {string} () Esta función no recibe parámetros.
**************************************************************/
function validar_serie_sub_serie() {
//     var codigo_serie            = $("#serie_expediente").val();
//     var subserie_expediente1    = $("#subserie_expediente").val();

    console.log("Falta desarrollar para metadatos esta funcion 'validar_serie_sub_serie'");
//     // console.log(codigo_serie+" - "+subserie_expediente1);

//     // if (codigo_serie === '017' && subserie_expediente1 === '002'){
//         /***********va a llamar al archivo documentos_contrato.php y le pasa los parametros de serie y subserie*************/
//     $.ajax({
//         type: 'POST',
//         url: 'admin_expedientes/documentos_contrato.php',
//         data: {
//             'tipo_consulta'         : 'validar_serie_subserie',
//             'codigo_serie'          : codigo_serie,
//             'codigo_subserie'       : subserie_expediente1
//         },
//         success: function(resp) {
//             if (resp != "") {
//                 $("#contenedor_crear_documentos").html(resp);
//             }
//         }
//     })
//     /*}else {
//      Si la serie es diferente a 017 y la subserie es diferente a 002 se borra el contenido del div 
//         $("#contenedor_crear_documentos").html('');
//     }*/
}
/* Fin funcion para cargar listado de expedientes por serie, subserie y dependencia */
/***************************************************************/

/**************************************************************
Inicio funcion para cambiar el valor de los numeros a letras en el valor de un contrato.*/
/**
* @class La funcion numeros_a_letras() se utiliza pasando por parámetro un valor entero y devuelve el valor en letras para validar si es correcto. Por ejemplo numeros_a_letras(125)-> "Ciento veinticinco"
* @description recibe valor de un numero y devuelve en letras dicho valor."
* @param {string} (numero) numero que va a ser convertido a palabras. 
**************************************************************/
 
function Unidades(num){
    switch(num){
        case 1: return "UN";
        case 2: return "DOS";
        case 3: return "TRES";
        case 4: return "CUATRO";
        case 5: return "CINCO";
        case 6: return "SEIS";
        case 7: return "SIETE";
        case 8: return "OCHO";
        case 9: return "NUEVE";
    }
    return "";
}

function Decenas(num){
    decena = Math.floor(num/10);
    unidad = num - (decena * 10);
    
    switch(decena){
        case 1:
            switch(unidad){
                case 0: return "DIEZ";
                case 1: return "ONCE";
                case 2: return "DOCE";
                case 3: return "TRECE";
                case 4: return "CATORCE";
                case 5: return "QUINCE";
                default: return "DIECI" + Unidades(unidad);
            }

        case 2:
            switch(unidad){
                case 0: return "VEINTE";
                default: return "VEINTI" + Unidades(unidad);
            }
        case 3: return DecenasY("TREINTA", unidad);
        case 4: return DecenasY("CUARENTA", unidad);
        case 5: return DecenasY("CINCUENTA", unidad);
        case 6: return DecenasY("SESENTA", unidad);
        case 7: return DecenasY("SETENTA", unidad);
        case 8: return DecenasY("OCHENTA", unidad);
        case 9: return DecenasY("NOVENTA", unidad);
        case 0: return Unidades(unidad);
    }

}//Unidades()

function DecenasY(strSin, numUnidades){
    if (numUnidades > 0)
        return strSin + " Y " + Unidades(numUnidades)
        return strSin;
}//DecenasY()

function Centenas(num){
    centenas = Math.floor(num / 100);
    decenas = num - (centenas * 100);

    switch(centenas){
        case 1:
            if (decenas > 0)
                return "CIENTO " + Decenas(decenas);
                return "CIEN";
        
        case 2: return "DOSCIENTOS " + Decenas(decenas);
        case 3: return "TRESCIENTOS " + Decenas(decenas);
        case 4: return "CUATROCIENTOS " + Decenas(decenas);
        case 5: return "QUINIENTOS " + Decenas(decenas);
        case 6: return "SEISCIENTOS " + Decenas(decenas);
        case 7: return "SETECIENTOS " + Decenas(decenas);
        case 8: return "OCHOCIENTOS " + Decenas(decenas);
        case 9: return "NOVECIENTOS " + Decenas(decenas);
    }
    return Decenas(decenas);
}//Centenas()

function Seccion(num, divisor, strSingular, strPlural){
    cientos = Math.floor(num / divisor)
    resto = num - (cientos * divisor)
    letras = "";
    
    if (cientos > 0)
    if (cientos > 1)
        letras = Centenas(cientos) + " " + strPlural;
    else
        letras = strSingular;

    if (resto > 0)
        letras += "";
    
    return letras;
}//Seccion()

function Miles(num){
    divisor     = 1000;
    cientos     = Math.floor(num / divisor)
    resto       = num - (cientos * divisor)
    strMiles    = Seccion(num, divisor, "MIL", "MIL");
    strCentenas = Centenas(resto);

    if(strMiles == "")
        return strCentenas;

    return strMiles + " " + strCentenas;
  //return Seccion(num, divisor, "UN MIL", "MIL") + " " + Centenas(resto);
}//Miles()

function Millones(num){
    divisor     = 1000000;
    cientos     = Math.floor(num / divisor)
    resto       = num - (cientos * divisor)
    strMillones = Seccion(num, divisor, "UN MILLON", "MILLONES");
    strMiles    = Miles(resto);

    if(strMillones == "")
        return strMiles;

    return strMillones + " " + strMiles;
  //return Seccion(num, divisor, "UN MILLON", "MILLONES") + " " + Miles(resto);
}//Millones()

function numero_a_letras(num,centavos){
    var data = {
        numero: num,
        enteros: Math.floor(num),
        centavos: (((Math.round(num * 100)) - (Math.floor(num) * 100))),
        letrasCentavos: "",
    };

    if(centavos == undefined || centavos==false) {
        data.letrasMonedaPlural="PESOS MCTE";
        data.letrasMonedaSingular="PESO MCTE";
    }else{
        data.letrasMonedaPlural="CENTAVOS";
        data.letrasMonedaSingular="CENTAVO";
    }

    if (data.centavos > 0)
        data.letrasCentavos = "CON " + numero_a_letras(data.centavos,true);

    if(data.enteros == 0)
        return "CERO " + data.letrasMonedaPlural + " " + data.letrasCentavos;

    if (data.enteros == 1)
        return Millones(data.enteros) + " " + data.letrasMonedaSingular + " " + data.letrasCentavos;
    else
        return Millones(data.enteros) + " " + data.letrasMonedaPlural + " " + data.letrasCentavos;
}//numero_a_letras()

/* Fin funcion para cambiar el valor de los numeros a letras en el valor de un contrato. */
/***************************************************************/

/**************************************************************
Inicio funcion para mostrar el valor de los numeros con separador de miles.*/
/**
* @class La funcion con_comas() se utiliza pasando por parámetro un valor entero y devuelve el valor con separador de miles. Por ejemplo con_comas(123456789) devuelve (123,456,789)
* @description recibe valor de un numero y devuelve valor con separador de miles."
* @param {string} (numero) numero que va a ser convertido con separador de miles. 
**************************************************************/
function con_comas(valor) {
    var nums    = new Array();
    var simb    = ","; // Éste es el separador
    valor       = valor.toString();
    valor       = valor.replace(/\D/g, "");   // Ésta expresión regular solo permitira ingresar números
    nums        = valor.split(""); // Se vacia el valor en un arreglo
    var long    = nums.length - 1; // Se saca la longitud del arreglo
    var patron  = 3; // Indica cada cuanto se ponen las comas
    var prox    = 2; // Indica en que lugar se debe insertar la siguiente coma
    var res     = "";
 
    while (long > prox) {
        nums.splice((long - prox),0,simb); // Se agrega la coma
        prox += patron; // Se incrementa la posición próxima para colocar la coma
    }
 
    for (var i = 0; i <= nums.length-1; i++) {
        res += nums[i]; // Se crea la nueva cadena para devolver el valor formateado
    }

    return res;
}
/* Fin funcion para cambiar el valor de los numeros a letras en el valor de un contrato. */
/***************************************************************/

/**************************************************************
Inicio funcion para pasar numero a letras con separador de miles. */
/**
* @class La funcion pasar_numero_a_letras() se utiliza pasando por parámetro un valor entero, el nombre de un div donde va a desplegar el resultado y devuelve el valor en parentesis en pesos con separador de miles, luego en letras. Por ejemplo pasar_numero_a_letras(3042985 ,div_despliega,) devuelve [($ 3,042,985) TRES MILLONES CUARENTA Y DOS MIL NOVECIENTOS OCHENTA Y CINCO PESOS MCTE]
* @description recibe valor de un numero y devuelve valor con separador de miles y en letras invocando las funciones numero_a_letras(numero) y con_comas(numero) definidas en éste mismo archivo."
* @param {string} (numero) numero que va a ser convertido. 
* @param {string} (div_despliega) Div en el cual se va a mostrar el resultado de la conversión realizada. 
**************************************************************/
function pasar_numero_a_letras(numero,div_despliega,){
    var valor_en_letras     = numero_a_letras(numero);
    var valor_con_puntos    = con_comas(numero);

    if(numero!=""){
        $("#"+div_despliega).html("($ "+valor_con_puntos+") "+valor_en_letras);
    }else{
        $("#"+div_despliega).html("");

    }
}
/* Fin funcion para pasar numero a letras con separador de miles. */
/***************************************************************/

/******************************************************************************************/
/* Modificar Expediente *******************************************************************/
/******************************************************************************************/
/*Funciones para desplegar ventana modal Modificar Expediente*/
function abrirVentanaModificarExpediente() {
    $("#ventana2").slideDown("slow");
    $("#mod_nombre_expediente").focus();
    $("#contenido").css({
        'z-index': '100'
    }); // Modifico estilo para sobreponer ventana modal 
    genera_consecutivo_temporal(2);
}

function cerrarVentanaModificarExpediente() {
    $("#ventana2").slideUp("slow");
    $('.art').slideUp("slow");
    $('.errores').slideUp("slow");
    $("#search_expediente").focus();
}
/* Fin funciones para desplegar ventana modal Modificar Nivel */
/* Script para ocultar errores y continuar consulta - Formulario Modificar Nivel */
function oculta_mod_errores() {
    $("#error_nombre_expediente_mod").slideUp("slow");
    $("#valida_minimo_nombre_mod").slideUp("slow");
    $("#valida_maximo_nombre_mod").slideUp("slow");
    $("#nombre_expediente_mod_ya_existe").slideUp("slow");
}
/* Fin script para ocultar errores y continuar consulta - Formulario Modificar Nivel */
/* Script para buscador nivel por nombre en formulario Modificar Expediente */
$('#nombre_expediente_mod').on("input", function(e) { // Accion que se activa cuando se digita #mod_nombre_nivel
    espacios_formulario('nombre_expediente_mod', 'mayusculas', 0);
    loading('sugerencia_nombre_expediente_mod');
    oculta_mod_errores();
    var search_nombre_mod_expediente = $(this).val();
    var id_expediente_mod1 = $("#id_expediente_mod").val();
    if ($(this).data("lastval") != search_nombre_mod_expediente) {
        $(this).data("lastval", search_nombre_mod_expediente);
        clearTimeout(timerid);
        timerid = setTimeout(function() {
            validar_input('nombre_expediente_mod');
            $.ajax({
                type: 'POST',
                url: 'admin_expedientes/buscador_expedientes.php',
                data: {
                    'search_nom_expediente': search_nombre_mod_expediente,
                    'id_expediente1': id_expediente_mod1,
                    'desde_formulario': '2'
                },
                success: function(resp) {
                    if (resp != "") {
                        $('#sugerencia_nombre_expediente_mod').html(resp);
                    }
                }
            })
        }, 1000);
    };
});
/* Fin script para buscador nivel por nombre en formulario Modificar Expediente */
/* Alerta error al dar click sobre enlace de expediente que ya existe - Formulario Modificar Expediente se invoca en buscador_ubicacion_topografica.php */
function error_modificar_expediente() {
    // sweetAlert({
    Swal.fire({
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        title: 'No es posible asignar este nombre de expediente.',
        text: 'El nombre seleccionado corresponde a un Expediente que ya existe.',
        type: 'warning'
    });
}

function carga_nombre_modificar_expediente(nombre_expediente) {
    $('#nombre_expediente_mod').val(nombre_expediente);
    $('.art').slideUp("slow");
    $('#nombre_expediente_mod_ya_existe').slideUp("slow");
}
/* Fin alerta error al dar click sobre enlace de expediente que ya existe - Formulario Modificar Expediente*/
/*Función para cargar datos al formulario de modificación de nivel*/
function cargar_modifica_expediente(id, year_expediente, dependencia_expediente, id_expediente, nombre_expediente, serie, subserie, creador) {
    $('#id_expediente_mod').val(id);
    $('#year_expediente_mod').val(year_expediente);
    $('#dependencia_expediente_mod').val(dependencia_expediente);
    $('#nombre_expediente_mod').val(nombre_expediente);
    $('#serie_expediente_mod').val(serie);
    $('#subserie_expediente_mod').val(subserie);
    $('#consecutivo_expediente_mod').val(id_expediente);
    $("#boton_modificar_expediente").html("<input type='button' value='Modificar Expediente' id='enviar_mod_expediente' class='botones' onclick='submit_modificar_nivel()'>")
    cerrarVentanaCrearExpediente();
    abrirVentanaModificarExpediente();
}
/* Fin funcion para cargar datos al formulario de modificacion de expediente */
/* Funcion para validar nombre_expediente_mod */
function validar_grabar_expediente_mod() {
    validar_input('nombre_expediente_mod');

    if ($(".errores").is(":visible")) {
        return false;
    }

    if ($(".art").is(":visible")) {
        $("#nombre_expediente_mod_ya_existe").slideDown("slow");
        $("#error_nombre_expediente_mod").slideUp("slow");
        $("#valida_minimo_nombre_mod").slideUp("slow");
        $("#valida_maximo_nombre_mod").slideUp("slow");
        return false;
    }
}
/* Fin funcion para validar nombre_expediente_mod */
/* Validación que los campos de Formulario Modificar Expediente (Submit) */
function submit_modificar_nivel() {
    if ($('.imagen_logo').is(":visible")) {
        // sweetAlert({
        Swal.fire({
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            title: 'La consulta se está ejecutando.',
            text: 'Un momento por favor.',
            type: 'warning'
        });
    } else {
        var submit_modificar_expediente = validar_grabar_expediente_mod();
        if (submit_modificar_expediente == false) {
            $("#nombre_expediente_mod").focus();
            return false;
        } else { // Realizar la modificacion del nivel
            loading('boton_modificar_expediente'); // Funcion especificada en include/js/funciones_menu.js
            var tipo_formulario1 = $("#formulario_modificar_expediente").val();
            var nombre_expediente_mod1 = $("#nombre_expediente_mod").val();
            var id_expediente_mod1 = $("#id_expediente_mod").val();
            $.ajax({
                type: 'POST',
                url: 'admin_expedientes/query_expedientes.php',
                data: {
                    'tipo_formulario': tipo_formulario1,
                    'nombre_expediente_mod': nombre_expediente_mod1,
                    'id_expediente_mod': id_expediente_mod1
                },
                success: function(resp) {
                    if (resp != "") {
                        $('#sugerencia_nombre_expediente_mod').html(resp);
                    }
                }
            })
        }
    }
}