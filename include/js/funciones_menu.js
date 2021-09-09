/* Script para ventana modal - Tecla Esc */
window.addEventListener("keyup", function(event) {
    var codigo = event.keyCode || event.which;
    if (codigo == 8) { // Opcion para restringir que la tecla backspace da atras en el navegador.
        if (history.forward(1)) {
            location.replace(history.forward(1));
        }
    }
    if (codigo == 27) {
        cerrar_ventanas_modal();
    }
}, false);
/* Fin script para ventana modal - Tecla Esc */
/* Funcion para cerrar ventanas modal, errores y reinicar formulario */
function cerrar_ventanas_modal() {
    $(".ventana_modal").slideUp("slow");
    $(".art").slideUp("slow");
    $(".errores").slideUp("slow");
    $("form").trigger("reset");
    $("#contenido").css({
        'z-index': '1'
    }); // Modifico estilo para sobreponer a ventana modal
}
/* Fin funcion para cerrar ventanas modal, errores y reinicar formulario */
/* Funcion para botones responsive del menu y menu_lateral */
$(document).ready(main);
/* Se inician los contadores para que los submenu se desplieguen correctamente */
var contador_carpetas_personales    = 1;
var contador_permisos_especiales    = 1;
var contador_alertas                = 1;
var contador_lateral                = 1;
var contador_menu                   = 1;
var contador_rad                    = 1;
var contador_reportes               = 1;
var contador_scan                   = 1;
var contador_superior               = 1;
var contador_usuario                = 1;

function main() {
    $('.boton_menu').click(function despliega_superior() { // Boton "Menu" Version Movil
        if (contador_superior == 1) {
            $('.menu_superior').animate({
                left: '0'
            });
            contador_superior = 0;
            $('#boton_menu').slideDown("slow");
            contador_menu = 0;
            if (contador_lateral != 1) {
                $('.menu_lat').animate({
                    left: '-100%'
                });
            }
        } else {
            contador_superior = 1;
            $('.menu_superior').animate({
                left: '-100%'
            });
        }
    });
    $('.boton_menu_lateral').click(function despliega_lateral() { // Boton "Menu Lateral" Version Movil
        if (contador_lateral == 1) {
            $('.menu_lat').animate({
                left: '0'
            });
            contador_lateral = 0;
            $('#boton_rad').slideDown("slow");
            contador_rad = 0;
            if (contador_superior != 1) {
                $('.menu_superior').animate({
                    left: '-100%'
                });
            }
        } else {
            contador_lateral = 1;
            $('.menu_lat').animate({
                left: '-100%'
            });
        }
    });
    /*Mostrar y ocultar sumbenus*/
    $('#menu_superior').click(function submenu1() {
        contador_alertas                = 1;
        contador_carpetas_personales    = 1;
        contador_lateral                = 1;
        contador_permisos_especiales    = 1;
        contador_rad                    = 1;
        contador_reportes               = 1;
        contador_scan                   = 1;
        contador_superior               = 1;
        contador_usuario                = 1;
        
        $("#contenido").css({
            'z-index': '1'
        }); // Modifico estilo para sobreponer a ventana modal
        if (contador_menu == 1) {
            $('#boton_menu').slideDown("slow");
            contador_menu = 0;
            if ($("#contenedor_toptil").is(":visible")) {   // Cierra menu usuario
                $('#contenedor_toptil').slideUp("slow");
            }
            if ($("#boton_rad").is(":visible")) {   // Cierra menu radicacion
                $('#boton_rad').slideUp("slow");
            }
            if ($("#boton_alertas").is(":visible")) {   // Cierra menu alertas
                $('#boton_alertas').slideUp("slow");
            }
            if ($("#boton_sc").is(":visible")) {    // Cierra menu scanner
                $('#boton_sc').slideUp("slow");
            }
            if ($("#boton_permisos_especiales").is(":visible")) {   // Cierra menu permisos especiales
                $('#boton_permisos_especiales').slideUp("slow");
            }
            if ($("#boton_reportes").is(":visible")) {  // Cierra menu reportes
                $('#boton_reportes').slideUp("slow");
            }
            if ($("#boton_carpetas_personales").is(":visible")) {  // Cierra menu carpetas_personales
                $('#boton_carpetas_personales').slideUp("slow");
            }
        } else {
            contador_menu = 1;
            $('#boton_menu').slideUp("slow");   // Cierra menu configuracion
        }
    });
    $('#menu_radicacion').click(function submenu2() { // Radicacion Entrada, Salida, etc
        contador_alertas                = 1;
        contador_carpetas_personales    = 1;
        contador_lateral                = 1;
        contador_menu                   = 1;
        contador_permisos_especiales    = 1;
        contador_reportes               = 1;
        contador_scan                   = 1;
        contador_superior               = 1;
        contador_usuario                = 1;

        $("#contenido").css({
            'z-index': '1'
        }); // Modifico estilo para sobreponer a ventana modal
        if (contador_rad == 1) {
            $('#boton_rad').slideDown("slow");
            contador_rad = 0;
            if ($("#contenedor_toptil").is(":visible")) {   // Cierra menu usuario
                $('#contenedor_toptil').slideUp("slow");
            }
            if ($("#boton_menu").is(":visible")) {  // Cierra menu configuracion
                $('#boton_menu').slideUp("slow");
            }
            if ($("#boton_alertas").is(":visible")) {   // Cierra menu alertas
                $('#boton_alertas').slideUp("slow");
            }
            if ($("#boton_sc").is(":visible")) {    // Cierra menu scanner
                $('#boton_sc').slideUp("slow");
            }
            if ($("#boton_permisos_especiales").is(":visible")) {   // Cierra menu permisos especiales
                $('#boton_permisos_especiales').slideUp("slow");
            }
            if ($("#boton_reportes").is(":visible")) {  // Cierra menu reportes
                $('#boton_reportes').slideUp("slow");
            }
            if ($("#boton_carpetas_personales").is(":visible")) {  // Cierra menu carpetas_personales
                $('#boton_carpetas_personales').slideUp("slow");
            }
        } else {
            contador_rad = 1;
            $('#boton_rad').slideUp("slow");    // Cierra menu radicacion
        }
    });
    $('#menu_alertas').click(function submenu3() { // Alertas pdf pendientes, etc
        contador_carpetas_personales    = 1;
        contador_lateral                = 1;
        contador_menu                   = 1;
        contador_permisos_especiales    = 1;
        contador_rad                    = 1;
        contador_reportes               = 1;
        contador_scan                   = 1;
        contador_superior               = 1;
        contador_usuario                = 1;

        $("#contenido").css({
            'z-index': '1'
        }); // Modifico estilo para sobreponer a ventana modal
        if (contador_alertas == 1) {
            $('#boton_alertas').slideDown("slow");
            contador_alertas = 0;
            if ($("#contenedor_toptil").is(":visible")) {   // Cierra menu usuario
                $('#contenedor_toptil').slideUp("slow");
            }
            if ($("#boton_menu").is(":visible")) {  // Cierra menu configuracion
                $('#boton_menu').slideUp("slow");
            }
            if ($("#boton_rad").is(":visible")) {   // Cierra menu radicacion
                $('#boton_rad').slideUp("slow");
            }
            if ($("#boton_sc").is(":visible")) {    // Cierra menu scanner
                $('#boton_sc').slideUp("slow");
            }
            if ($("#boton_permisos_especiales").is(":visible")) {   // Cierra menu permisos especiales
                $('#boton_permisos_especiales').slideUp("slow");
            }
            if ($("#boton_reportes").is(":visible")) {  // Cierra menu reportes
                $('#boton_reportes').slideUp("slow");
            }
            if ($("#boton_carpetas_personales").is(":visible")) {  // Cierra menu carpetas_personales
                $('#boton_carpetas_personales').slideUp("slow");
            }
        } else {
            $('#boton_alertas').slideUp("slow");    // Cierra menu alertas
            contador_alertas = 1;
        }
    });
    $('#menu_scanner').click(function submenu4() { // Asociar Imagen
        contador_alertas                = 1;
        contador_carpetas_personales    = 1;
        contador_lateral                = 1;
        contador_menu                   = 1;
        contador_permisos_especiales    = 1;
        contador_rad                    = 1;
        contador_reportes               = 1;
        contador_superior               = 1;
        contador_usuario                = 1;

        $("#contenido").css({
            'z-index': '1'
        }); // Modifico estilo para sobreponer a ventana modal
        if (contador_scan == 1) {
            $('#boton_sc').slideDown("slow");
            contador_scan = 0;
            if ($("#contenedor_toptil").is(":visible")) {   // Cierra menu usuario
                $('#contenedor_toptil').slideUp("slow");
            }
            if ($("#boton_menu").is(":visible")) {  // Cierra menu configuracion
                $('#boton_menu').slideUp("slow");
            }
            if ($("#boton_rad").is(":visible")) {   // Cierra menu radicacion
                $('#boton_rad').slideUp("slow");
            }
            if ($("#boton_alertas").is(":visible")) {   // Cierra menu alertas
                $('#boton_alertas').slideUp("slow");
            }
            if ($("#boton_permisos_especiales").is(":visible")) {   // Cierra menu permisos especiales
                $('#boton_permisos_especiales').slideUp("slow");
            }
            if ($("#boton_reportes").is(":visible")) {  // Cierra menu reportes
                $('#boton_reportes').slideUp("slow");
            }
            if ($("#boton_carpetas_personales").is(":visible")) {  // Cierra menu carpetas_personales
                $('#boton_carpetas_personales').slideUp("slow");
            }
        } else {
            $('#boton_sc').slideUp("slow");     // Cierra menu scanner
            contador_scan = 1;
        }
    });
    $('#permisos_especiales').click(function submenu5() { // Radicacion Entrada, Salida, etc
        contador_alertas                = 1;
        contador_carpetas_personales    = 1;
        contador_lateral                = 1;
        contador_menu                   = 1;
        contador_rad                    = 1;
        contador_reportes               = 1;
        contador_scan                   = 1;
        contador_superior               = 1;
        contador_usuario                = 1;
        
        $("#contenido").css({
            'z-index': '1'
        }); // Modifico estilo para sobreponer a ventana modal
        if (contador_permisos_especiales == 1) {
            $('#boton_permisos_especiales').slideDown("slow");
            contador_permisos_especiales = 0;
            if ($("#contenedor_toptil").is(":visible")) {   // Cierra menu usuario
                $('#contenedor_toptil').slideUp("slow");
            }
            if ($("#boton_menu").is(":visible")) {  // Cierra menu configuracion
                $('#boton_menu').slideUp("slow");
            }
            if ($("#boton_rad").is(":visible")) {   // Cierra menu radicacion
                $('#boton_rad').slideUp("slow");
            }
            if ($("#boton_alertas").is(":visible")) {   // Cierra menu alertas
                $('#boton_alertas').slideUp("slow");
            }
            if ($("#boton_sc").is(":visible")) {    // Cierra menu scanner
                $('#boton_sc').slideUp("slow");
            }
            if ($("#boton_reportes").is(":visible")) {  // Cierra menu reportes
                $('#boton_reportes').slideUp("slow");
            }
            if ($("#boton_carpetas_personales").is(":visible")) {  // Cierra menu carpetas_personales
                $('#boton_carpetas_personales').slideUp("slow");
            }
        } else {
            $('#boton_permisos_especiales').slideUp("slow");    // Cierra menu permisos especiales
            contador_permisos_especiales = 1;
        }
    });
    $('#menu_reportes').click(function submenu6() { // Reportes, etc
        contador_alertas                = 1;
        contador_carpetas_personales    = 1;
        contador_lateral                = 1;
        contador_menu                   = 1;
        contador_permisos_especiales    = 1;
        contador_rad                    = 1;
        contador_scan                   = 1;
        contador_superior               = 1;
        contador_usuario                = 1;

        $("#contenido").css({
            'z-index': '1'
        }); // Modifico estilo para sobreponer a ventana modal
        if (contador_reportes == 1) {
            $('#boton_reportes').slideDown("slow");
            contador_reportes = 0;
            if ($("#boton_rad").is(":visible")) {  // Cierra menu radicacion
                $('#boton_rad').slideUp("slow");
            }
            if ($("#contenedor_toptil").is(":visible")) { // Cierra menu usuario
                $('#contenedor_toptil').slideUp("slow");
            }
            if ($("#boton_menu").is(":visible")) {  // Cierra menu configuracion
                $('#boton_menu').slideUp("slow");
            }
            if ($("#boton_alertas").is(":visible")) {   // Cierra menu alertas
                $('#boton_alertas').slideUp("slow");
            }
            if ($("#boton_sc").is(":visible")) {    // Cierra menu scanner
                $('#boton_sc').slideUp("slow");
            }
            if ($("#boton_permisos_especiales").is(":visible")) {   // Cierra menu permisos especiales
                $('#boton_permisos_especiales').slideUp("slow");
            }
            if ($("#boton_carpetas_personales").is(":visible")) {  // Cierra menu carpetas_personales
                $('#boton_carpetas_personales').slideUp("slow");
            }
        } else {
            $('#boton_reportes').slideUp("slow");
            contador_reportes = 1;
        }
    });
    $('#foto_usuario').click(function menu_usuario() {
        contador_alertas                = 1;
        contador_carpetas_personales    = 1;
        contador_lateral                = 1;
        contador_menu                   = 1;
        contador_permisos_especiales    = 1;
        contador_rad                    = 1;
        contador_reportes               = 1;
        contador_scan                   = 1;
        contador_superior               = 1;

        $("#contenido").css({
            'z-index': '1'
        }); // Modifico estilo para sobreponer a ventana modal
        if (contador_usuario == 1) {
            $('#contenedor_toptil').slideDown("slow");
            contador_usuario = 0;
            if ($("#boton_menu").is(":visible")) {  // Cierra menu configuracion
                $('#boton_menu').slideUp("slow");
            }
            if ($("#boton_rad").is(":visible")) {   // Cierra menu radicacion
                $('#boton_rad').slideUp("slow");
            }
            if ($("#boton_alertas").is(":visible")) {   // Cierra menu alertas
                $('#boton_alertas').slideUp("slow");
            }
            if ($("#boton_sc").is(":visible")) {    // Cierra menu scanner
                $('#boton_sc').slideUp("slow");
            }
            if ($("#boton_permisos_especiales").is(":visible")) {   // Cierra menu permisos especiales
                $('#boton_permisos_especiales').slideUp("slow");
            }
            if ($("#boton_reportes").is(":visible")) {  // Cierra menu reportes
                $('#boton_reportes').slideUp("slow");
            }
            if ($("#boton_carpetas_personales").is(":visible")) {  // Cierra menu carpetas_personales
                $('#boton_carpetas_personales').slideUp("slow");
            }
        } else {
            $('#contenedor_toptil').slideUp("slow");    // Cierra menu usuario
            contador_usuario = 1;
        }
    });

    $('#contenido').click(function limpia() { // Comportamiento al hacer click en el div "contenido"
        contador_alertas                = 1;
        contador_carpetas_personales    = 1;
        contador_menu                   = 1;
        contador_permisos_especiales    = 1;
        contador_rad                    = 1;
        contador_reportes               = 1;
        contador_scan                   = 1;
        contador_usuario                = 1;

        $("#contenido").css({
            'z-index': '100'
        }); // Modifico estilo para sobreponer a ventana modal
        if (contador_superior != 1) {
            contador_superior = 1;
            $('.menu_superior').animate({
                left: '-100%'
            });
        }
        if (contador_lateral != 1) {
            contador_lateral = 1;
            $('.menu_lat').animate({
                left: '-100%'
            });
        }
        contador_menu = 1;
        $('#boton_menu').slideUp("slow");                   // Cierra menu configuracion
        $('#boton_rad').slideUp("slow");                    // Cierra menu radicacion
        $('#contenedor_toptil').slideUp("slow");            // Cierra menu usuario 
        $('#boton_sc').slideUp("slow");                     // Cierra menu scanner
        $('#boton_alertas').slideUp("slow");                // Cierra menu alertas
        $('#boton_permisos_especiales').slideUp("slow");    // Cierra menu permisos especiales
        $('#boton_reportes').slideUp("slow");               // Cierra menu reportes
        $('#boton_carpetas_personales').slideUp("slow");    // Cierra menu carpetas_personales
            
    });
    /* Funcion del click derecho */
    $("#carpeta_5").mousedown(function(e) {
        //1: izquierda, 2: medio/ruleta, 3: derecho
        if (e.which == 3) {
            alert("hola");
        }
    });
    /* Fin funcion del click derecho */
}

function detectar_boton(event, carpeta, nombre) { // Funcion para detectar cual boton del mouse se ha pulsado
    var boton = event.button;
    var id_carpeta = "#carpeta_" + carpeta;
    var input_modificar_carpeta = "#modificar_carpeta" + carpeta;
    if (boton == 0) {
        if ($(input_modificar_carpeta).is(":visible")) {} else {
            carga_bandeja_entrada(carpeta, 'general'); // Carga bandejas personales 
        }
    }
    if (boton == 2) {
        $(id_carpeta).html("<span><input type='text' value='" + nombre + "' id='modificar_carpeta" + carpeta + "' style='padding:5px; width:130px;' onkeyup='oculta_errores_mod_carp_per(\"" + carpeta + "\")'></span><span onclick='modificar_carpetas_personales(" + carpeta + ")' title='Modificar carpeta personal'><img src='imagenes/iconos/checkbox2.png' style='padding-left:5px; width:18px; height:27px; position:absolute;'></span><div id='error_vacio_carpeta" + carpeta + "' class='errores' style='background-color:red;'>El nombre de la carpeta no puede ser vacío.</div><div id='error_caracteres_carpeta" + carpeta + "' class='errores' style='background-color:red;'>Los nombres de carpeta no pueden tener caraceres especiales, ni tildes.</div><div id='error_minimo_carpeta" + carpeta + "' class='errores' style='background-color:red;'>El nombre de la carpeta no puede tener menos de 4 caracteres.</div><div id='error_maximo_carpeta" + carpeta + "' class='errores' style='background-color:red;'>El nombre de la carpeta no puede tener mas de 20 caracteres.</div><div id='error_carpeta_existe" + carpeta + "' class='errores' style='background-color:red;'>El nombre de la carpeta ya existe. Por favor ingrese un nombre válido.</div>");
    }
    /*  
    $(document).bind("contextmenu",function(e){ // Con esta funcion se inhabilita el menu contextual del click derecho del mouse
        return false;       
    });
    */
}
/*Fin funcion para botones responsive del menu y menu_lateral*/
/************************************************************************/
/* Funcion para desplegar carpetas personales */
function carpetas_personales(num) { // Mostrar carpetas personales
    contador_alertas                = 1;
    contador_lateral                = 1;
    contador_menu                   = 1;
    contador_permisos_especiales    = 1;
    contador_rad                    = 1;
    contador_reportes               = 1;
    contador_scan                   = 1;
    contador_superior               = 1;
    contador_usuario                = 1;

    switch (num) {
        case 'mostrar_carpetas':

            if (contador_carpetas_personales == 1) {
                $('#boton_carpetas_personales').slideDown("slow");
                contador_carpetas_personales = 0;

                if ($("#contenedor_toptil").is(":visible")) { // Cierra menu usuario
                    $('#contenedor_toptil').slideUp("slow");
                }
                if ($("#boton_menu").is(":visible")) {  // Cierra menu configuracion
                    $('#boton_menu').slideUp("slow");
                }
                if ($("#boton_rad").is(":visible")) {   // Cierra menu radicacion
                    $('#boton_rad').slideUp("slow");
                }
                if ($("#boton_alertas").is(":visible")) {
                    $('#boton_alertas').slideUp("slow");
                }
                if ($("#boton_sc").is(":visible")) {    // Cierra menu scanner
                    $('#boton_sc').slideUp("slow");
                }
                if ($("#boton_permisos_especiales").is(":visible")) {    // Cierra menu permisos especiales
                    $('#boton_permisos_especiales').slideUp("slow");
                }
                if ($("#boton_reportes").is(":visible")) {    // Cierra menu reportes
                    $('#boton_reportes').slideUp("slow");
                }
            } else {
                $('#boton_carpetas_personales').slideUp("slow");
                contador_carpetas_personales = 1;
            }
            break;
        case 'salir_crea_carpeta':
            $('#creador_carpeta').slideUp("slow");
            break;
        case 'menu_crear_carpetas':
            $('#buscador_carpeta').slideUp("slow");
            $('#creador_carpeta').slideDown("slow");
            break;
        case 'crear_carpeta':
            var nombre_carp = $("#crear_carpeta").val();
            if (nombre_carp == '') {
                $("#error_vacio_carpeta").slideDown("slow");
            } else {
                var validar_caracteres_especiales_crear_carpeta = nombre_carp.match(/^[0-9a-zA-Z\s]+$/); // validar caracteres especiales o tildes
                if (validar_caracteres_especiales_crear_carpeta == null) {
                    $("#error_caracteres_carpeta").slideDown("slow");
                } else {
                    if (nombre_carp.length < 4) {
                        $("#error_minimo_carpeta").slideDown("slow");
                    } else {
                        if (nombre_carp.length > 20) {
                            $("#error_maximo_carpeta").slideDown("slow");
                        } else {
                            $.ajax({
                                type: 'POST',
                                url: 'login/carpetas_personales.php',
                                data: {
                                    'nombre_carpeta': nombre_carp
                                },
                                success: function(resp2) {
                                    if (resp2 != "") {
                                        if (resp2 == "carpeta_ya_existe") {
                                            $("#error_carpeta_existe").slideDown("slow");
                                        } else {
                                            if (resp2 == "error_insert") {
                                                alert("No se pudo actualizar las carpetas_personales. Por favor revisa e intenta nuevamente.")
                                            } else {
                                                auditoria_general('crear_carpeta_personal', resp2);
                                            }
                                        }
                                    }
                                }
                            })
                        }
                    }
                }
            }
            break;
    }
}

function oculta_errores_carp_per() {
    $("#error_minimo_carpeta").slideUp("slow");
    $("#error_maximo_carpeta").slideUp("slow");
    $("#error_vacio_carpeta").slideUp("slow");
    $("#error_caracteres_carpeta").slideUp("slow");
    $("#error_carpeta_existe").slideUp("slow");
}

function modificar_carpetas_personales(id) {
    var nombre_carpeta = "#modificar_carpeta" + id;
    var error_caracteres = "#error_caracteres_carpeta" + id;
    var error_vacio = "#error_vacio_carpeta" + id;
    var error_minimo = "#error_minimo_carpeta" + id;
    var error_maximo = "#error_maximo_carpeta" + id;
    var error_ya_existe_mod = "#error_carpeta_existe" + id;
    var nuevo_nombre = $(nombre_carpeta).val();
    if (nuevo_nombre == '') {
        $(error_vacio).slideDown("slow");
    } else {
        error_minimo_carpeta
        var validar_caracteres_especiales_modificar_carpeta = nuevo_nombre.match(/^[0-9a-zA-Z\s]+$/); // validar caracteres especiales o tildes
        if (validar_caracteres_especiales_modificar_carpeta == null) {
            $(error_caracteres).slideDown("slow");
        } else {
            if (nuevo_nombre.length < 4) {
                $(error_minimo).slideDown("slow");
            } else {
                if (nuevo_nombre.length > 20) {
                    $(error_maximo).slideDown("slow");
                } else {
                    $.ajax({
                        type: 'POST',
                        url: 'login/carpetas_personales.php',
                        data: {
                            'nombre_carpeta_mod': nuevo_nombre,
                            'id': id
                        },
                        success: function(resp2) {
                            if (resp2 != "") {
                                if (resp2 == "carpeta_ya_existe") {
                                    $(error_ya_existe_mod).slideDown("slow");
                                } else {
                                    if (resp2 == "error_modificar_carpeta") {
                                        alert("No se pudo modificar las carpetas_personales. Por favor revisa e intenta nuevamente.")
                                    } else {
                                        auditoria_general('modificar_carpeta_personal', resp2);
                                    }
                                }
                            }
                        }
                    })
                }
            }
        }
    }
}

function oculta_errores_mod_carp_per(id) {
    var error_vacio = "#error_vacio_carpeta" + id;
    var error_minimo = "#error_minimo_carpeta" + id;
    var error_maximo = "#error_maximo_carpeta" + id;
    var error_caracteres = "#error_caracteres_carpeta" + id;
    var error_ya_existe_mod = "#error_carpeta_existe" + id;
    $(error_vacio).slideUp("slow");
    $(error_minimo).slideUp("slow");
    $(error_maximo).slideUp("slow");
    $(error_caracteres).slideUp("slow");
    $(error_ya_existe_mod).slideUp("slow");
}

function cargar_carpetas_personales() {
    $.ajax({
        type: 'POST',
        url: 'login/carpetas_personales.php',
        data: {
            'carpetas_per': 'search'
        },
        success: function(resp2) {
            $("#boton_carpetas_personales").html(resp2);
            $("#creador_carpeta").slideUp("slow");
            oculta_errores_carp_per();
            $("#crear_carpeta").val("");
        }
    })
}
/* Fin funcion para desplegar carpetas personales */
/* Funcion auditoria general */
function auditoria_general(transaccion, creado) {
    switch (transaccion) {
        case'agregar_nuevo_contacto':
            var trans = "El Contacto ha sido creado correctamente";
            var url = 'login/transacciones.php';
            break;
        case 'anexar_archivo':
            var trans = 'El archivo ha sido adjuntado al radicado ' + creado + ' correctamente';
            var url = 'login/transacciones.php';
            break;
        case 'aprobar_radicado':
            var trans = 'El documento ' + creado + ' ha sido aprobado correctamente';
            var url = 'login/transacciones.php';
            break;
        case 'archivar_radicado':
            var trans = 'El documento ha sido movido a la carpeta personal correctamente';
            var url = 'login/transacciones.php';
            break;
        case 'crear_carpeta_personal':
            var trans = "La carpeta " + creado + " ha sido creada correctamente";
            var url = 'login/transacciones.php';
            break;
        case 'crear_expediente':
            var trans = 'El expediente ha sido creado correctamente';
            var url = "login/transacciones.php";
            break;
        case 'cancelar_solicitud_prestamo':
            var trans = 'La solicitud de préstamo asociada al radicado ' + creado + ' ha sido cancelada';
            var url = 'login/transacciones.php';
            break;
        case 'crear_secuencia':
            var trans = 'El Tipo de secuencia ha sido creado correctamente';
            var url = "login/transacciones.php";
            break;
        case 'crear_serie':
            var trans = 'La serie ' + creado + ' ha sido creada correctamente';
            var url = "login/transacciones.php";
            break;
        case 'crear_subserie':
            var trans = 'La subserie ' + creado + ' ha sido creada correctamente';
            var url = "login/transacciones.php";
            break;
        case 'crear_tipo_documento':
        case 'crear_tipo_documento_pqr':
            var trans = 'El Tipo de documento ha sido creado correctamente';
            var url = "login/transacciones.php";
            break;
        case 'crear_tipo_radicado':
            var trans = 'El Tipo de radicado ha sido creado correctamente';
            var url = "login/transacciones.php";
            break;
        case 'crear_usuario':
            var trans = "El Usuario ha sido creado correctamente";
            var url = "login/transacciones.php";
            break;
        case 'derivar_radicado':
            var trans = "El Radicado ha sido derivado correctamente";
            var url = "login/transacciones.php";
            break;
        case 'devolucion_prestamo_fisico':
            var trans = "Documento registrado como devuelto en físico correctamente";
            var url = "login/transacciones.php";
            break;
        case 'documento_no_requiere_respuesta':
            var trans = "Documento marcado como No requiere respuesta correctamente";
            var url = "login/transacciones.php";
            break;
        case 'enviar_radicado':
            var trans = "El radicado ha sido reasignado";
            var url = "login/transacciones.php";
            break;
        case 'generar_planilla_documentos_fisicos':
            var trans   = "Ha confirmado mediante firma electrónica que ha generado la planilla.";
            var url     = "login/transacciones.php";
            break;
        case 'incluir_radicado_en_expediente':
            var trans = "El Radicado ha sido incluido en el expediente correctamente";
            var url = "login/transacciones.php";
            break;
        case 'informar_radicado':
            var trans = "El Radicado ha sido informado correctamente";
            var url = "login/transacciones.php";
            break;
        case 'ingresa_cambio_organico_funcional':
            var trans = "La versión del cambio organico-funcional ha sido ingresada correctamente";
            var url = "login/transacciones.php";
            break;
        case 'insertar_metadato':
            var trans = "El Metadato ha sido ingresado correctamente";
            var url = "login/transacciones.php";
            break;
        case 'imagen_principal':
            var trans   = "La imagen principal del radicado ha sido asignada correctamente";
            var url     = "login/transacciones.php";
            break;
        case 'insertar_trd_exp_radicado':
            var trans   = "La TRD  y EXPEDIENTE han sido asignados al radicado correctamente";
            var url     = "login/transacciones.php";
            break;
        case 'insertar_trd_radicado':
            var trans   = "La TRD ha sido asignada al radicado correctamente";
            var url     = "login/transacciones.php";
            break;
        case 'inventario_individual':
            var trans = "El radicado ha sido ingresado al inventario correctamente";
            var url = "login/transacciones.php";
            break;
        case 'modifica_cambio_organico_funcional':
            var trans = "La versión del cambio organico-funcional ha sido modificada correctamente";
            var url = "login/transacciones.php";
            break;
        case 'modifica_plantilla_resoluciones':
        case 'modifica_plantilla_resoluciones_expediente':
        case 'modifica_plantilla_salida':
        case 'modifica_plantilla_salida_expediente':
        case 'modificacion_inventario':
        case 'modificacion_radicado':
        case 'modificacion_rapida':
            var trans = 'El radicado ha sido modificado correctamente';
            var url = "login/transacciones.php";
            break;
        case 'modificacion_rapida_mas_imagen':
        case 'modificacion_radicado_mas_imagen':
            var trans = 'El radicado con la imagen ha sido modificado correctamente';
            var url = "login/transacciones.php";
            break;
        case 'modificar_carpeta_personal':
            var trans = "La carpeta " + creado + " ha sido modificada correctamente";
            var url = "login/transacciones.php";
            break;
        case 'modificar_consecutivo':
            var trans = 'El Tipo de secuencia ha sido modificado correctamente';
            var url = "login/transacciones.php";
            break;
        case 'modificar_expediente':
            var trans = 'El expediente ha sido modificado correctamente';
            var url = "login/transacciones.php";
            break;
        case 'modificar_serie':
            var trans = 'La serie ' + creado + ' ha sido modificada correctamente';
            var url = "login/transacciones.php";
            break;
        case 'modificar_subserie':
            var trans = 'La subserie ' + creado + ' ha sido modificada correctamente';
            var url = "login/transacciones.php";
            break;
        case 'modificar_tipo_documento':
        case 'modificar_tipo_documento_pqr':
            var trans = 'El Tipo de documento ha sido modificado correctamente';
            var url = "login/transacciones.php";
            break;
        case 'modificar_usuario':
            var trans = "El usuario ha sido modificado correctamente";
            var url = "login/transacciones.php";
            break;
        case 'plantilla_interna':
            var trans = "La plantilla de radicacion interna del documento " + creado + " ha sido generada.";
            var url = "login/transacciones.php";
            break;
        case 'plantilla_salida_respuesta':
            var trans = "La plantilla para la respuesta del documento " + creado + " ha sido generada.";
            var url = "login/transacciones.php";
            break;
        case 'prestamo_documento':
            var trans = "Documento registrado como prestado en físico correctamente";
            var url = "login/transacciones.php";
            break;
        case 'radicacion_correo_electronico':
        case 'radicacion_entrada':
        case 'radicacion_normal':
        case 'radicacion_rapida':
            var trans = "Documento radicado correctamente";
            var url = "login/transacciones.php";
            break;
        case 'reasignar_radicado':
            var trans = "La reasignación del documento ha sido realizada.";
            var url = "login/transacciones.php";
            break;
        case 'recibir_listado_documentos_fisicos':
            var trans   = "Ha confirmado mediante firma electrónica que ha recibido los documentos en físico.";
            var url     = "login/transacciones.php";
            break;
        case 'salida_respuesta':
            var trans = "La respuesta al radicado ha sido realizada.";
            var url = "login/transacciones.php";
            break;
        case 'subir_pdf_principal':
            var trans = "El archivo PDF ha sido subido correctamente.";
            var url = "login/transacciones.php";
            break;
        case 'plantilla_resoluciones':
        case 'plantilla_resoluciones_expediente':
        case 'plantilla_respuesta':
        case 'plantilla_salida':
        case 'plantilla_salida_expediente':
            var trans = "La plantilla ha sido generada correctamente.";
            var url = "login/transacciones.php";
            break;
        case 'sticker_entrada':
            var trans = "El sticker ha sido generado correctamente.";
            var url = "../../login/transacciones.php";        
            break;    
    }
    $.ajax({ // Guardo registro de ingreso al sistema para auditoria
        type: 'POST',
        url: url,
        data: {
            'creado'        : creado,
            'transaccion'   : transaccion
        },
        success: function(resp_general){
            if (resp_general == "true"){
                Swal.fire({
                    position            : 'top-end',
                    showConfirmButton   : false,
                    timer               : 1500,
                    title               : trans,
                    text                : '',
                    type                : 'success'
                }).then(function(isConfirm){
                    switch (transaccion) {
                        case 'modifica_plantilla_salida':
                        case 'modifica_plantilla_salida_expediente':
                        case 'plantilla_salida':
                        case 'plantilla_salida_expediente':
                            agregar_pestanas(creado)
                            break;
                        case 'crear_serie':
                        case 'crear_subserie':
                        case 'modificar_serie':
                        case 'modificar_subserie':
                            carga_administrador_cuadro_clasificacion_documental();
                            break;
                        case 'insertar_metadato':
                            carga_administrador_metadatos();
                            break;
                        case 'ingresa_cambio_organico_funcional':
                        case 'modifica_cambio_organico_funcional':
                            carga_administrador_normatividad('interna');
                            break;    
                        case 'crear_secuencia':
                        case 'crear_tipo_documento':
                        case 'crear_tipo_documento_pqr':
                        case 'crear_tipo_radicado':
                        case 'modificar_consecutivo':
                        case 'modificar_tipo_documento':
                        case 'modificar_tipo_documento_pqr':
                            carga_administrador_parametrizacion();
                            break;
                        case 'crear_usuario':
                        case 'modificar_usuario':
                            carga_administrador_usuarios();
                            break;
                        case 'agregar_nuevo_contacto':
                            $("#div_agregar_destinatario").html("");
                            break;
                        case 'derivar_radicado':
                        case 'enviar_radicado':
                        case 'radicacion_normal':
                        case 'reasignar_radicado':
                        case 'solicitud_prestamo_documento':
                            carga_bandeja_entrada('entrada', 'general'); // Muestra bandeja de entrada
                            break;
                        case 'anexar_archivo':
                        case 'aprobar_radicado':
                        case 'archivar_radicado':
                        case 'documento_no_requiere_respuesta':
                        case 'incluir_radicado_en_expediente':
                        case 'insertar_trd_exp_radicado':
                        case 'insertar_trd_radicado':
                        case 'modifica_plantilla_resoluciones':
                        case 'modifica_plantilla_resoluciones_expediente':
                        case 'plantilla_interna':
                        case 'plantilla_resoluciones':
                        case 'plantilla_resoluciones_expediente':
                        case 'plantilla_respuesta':
                        case 'radicacion_correo_electronico':
                        case 'salida_respuesta':
                        case 'subir_pdf_principal':
                            carga_bandeja_entrada('entrada', creado); // Muestra bandeja de entrada
                            break;
                        case 'crear_expediente':
                        case 'modificar_expediente':
                            carga_creacion_expedientes();
                            break;
                        case 'inventario_individual':
                            carga_index_inventario_individual();
                            break;
                        case 'modificacion_inventario':
                        case 'modificacion_radicado':
                        case 'modificacion_radicado_mas_imagen':
                        case 'modificacion_rapida':
                        case 'modificacion_rapida_mas_imagen':
                            carga_modificacion();
                            break;
                        case 'cancelar_solicitud_prestamo':
                        case 'devolucion_prestamo_fisico':
                        case 'prestamo_documento':
                            carga_modulo_prestamos();
                            break;
                        case 'recibir_listado_documentos_fisicos':
                            carga_modulo_radicados_fisicos();
                            break;  
                        case 'imagen_principal':
                            carga_modulo_scanner();
                            break;
                        case 'crear_carpeta_personal':
                        case 'modificar_carpeta_personal':
                            cargar_carpetas_personales();
                            break;
                        case 'informar_radicado':
                            $("#ventana_informar_radicado").slideUp("slow");
                            break;      
                    }
                })
            } else {
                alert("error " + resp_general)
            }
        }
    })
}
/* Fin funcion auditoria general */
/************************************************************************/
/* Funciones para cargar div #contenido */

/* Carga el Módulo de gestionar datos del usuario*/
function buscar_radicado_sin_pdf() {
    $("#contenido").load("radicacion/radicacion_entrada/menu_pendientes_imagen_pdf.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
}

/* Carga el Módulo de gestionar datos del usuario*/
function gestionar_datos_usuario() {
    $("#contenido").load("login/gestionar_datos_usuario.php");
    menu_superior();
    $('#contenedor_toptil').slideUp();
}
/* Fin Módulo de gestionar datos del usuario*/
function cambiar_contrasena() {
    $("#contenido").load("login/cambio_contrasena.php");
    menu_superior();
    $('#contenedor_toptil').slideUp();
    // eliminar_borrador_radicado();
}
/* Fin Módulo cambio de contraseña*/
/*Carga el administrador de dependencias*/
function carga_administrador_dependencias() {
    $("#contenido").load("admin_depe/index_dependencias.php");
    menu_superior();
    // eliminar_borrador_radicado();
};
/*Carga el administrador de dependencias*/
function carga_administrador_cuadro_clasificacion_documental() {
    $("#contenido").load("cuadro_clasificacion_documental/index_cuadro_clasificacion_documental.php");
    menu_superior();
    // eliminar_borrador_radicado();
};
/*Fin carga el administrador de dependencias*/
/*Carga el administrador de dependencias*/
function carga_administrador_metadatos() {
    $("#contenido").load("admin_metadatos/index_metadatos.php");
    menu_superior();
    // eliminar_borrador_radicado();
};
/*Fin carga el administrador de dependencias*/
/*Carga el administrador de municipios*/
function carga_administrador_municipios() {
    $("#contenido").load("admin_muni/index_municipios.php");
    menu_superior();
    // eliminar_borrador_radicado();
};
/*Fin carga el administrador de municipios*/
/* Carga el módulo de normatividad */
function carga_administrador_normatividad(tipo) {
    $("#contenido").load("normatividad/index_normatividad.php", {
        tipo_normatividad: tipo
    });
    $('.menu_superior').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga módulo de normatividad */
/* Carga el Módulo de Parametrizacion */
function carga_administrador_parametrizacion() {
    $("#contenido").load("admin_parametrizacion/index_parametrizacion.php");
    menu_superior();
    // eliminar_borrador_radicado();
}
/* Fin Módulo Parametrizacion */
/* Carga el Módulo de Auditoría del sistema */
function carga_auditoria_sistema() {
    $("#contenido").load("admin_auditoria_sistema/index_auditoria.php");
    menu_superior();
}
/* Fin Módulo Auditoría del sistema */
/*Carga el modulo consultar radicados fisicos*/
function carga_modulo_radicados_fisicos() {
    $("#contenido").load("admin_radicados_fisicos/index_ubicacion_radicados_fisicos.php");
    menu_superior();
    // eliminar_borrador_radicado();
};
/*Fin Carga el modulo consultar radicados fisicos*/
/* Carga el reporte de radicados de entrada */
function carga_reporte_entrega_correspondencia() {
    $("#contenido").load("reportes/reporte1_entrega_correspondencia_entrada.php");
    $('.menu_superior').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga reporte de radicados de entrada */
/* Carga el reporte de radicados de entrada */
function carga_reporte2_radicacion_entrada() {
    $("#contenido").load("reportes/reporte2_radicados_entrada.php");
    $('.menu_superior').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga el reporte de radicados de entrada */
/* Carga el reporte de radicados que esten vacios */
function carga_reporte3_radicados_vacios() {
    $("#contenido").load("reportes/reporte3_radicados_vacios.php");
    $('.menu_superior').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga el reporte de radicados que esten vacios */
/* Carga el buzon de correo */
function buzon_correo_modulo() {
    $("#contenido").load("correo_electronico/buzon_correo.php");
    $('.menu_superior').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga el buzon de correo */
/*Carga el administrador de usuarios*/
function carga_administrador_usuarios() {
    $("#contenido").load("admin_usuarios/index_usuarios.php");
    menu_superior();
    // eliminar_borrador_radicado();
};
/*Fin carga del administrador de usuarios*/
/*Carga el Módulo de bandeja de entrada*/
function carga_bandeja_entrada(carpeta, radicado) {
    /* Para el caso del llamado carga_bandeja_entrada() sin parámetros */
    if(typeof carpeta==="undefined" && typeof radicado==="undefined"){
        carpeta     = "entrada";
        radicado    = "general";
    }
    $('#contenido').load('bandejas/entrada/index_bandeja_entrada.php', {
        carpeta     : carpeta,
        radicado    : radicado
    });
    $('.menu_lat').animate({
        left: '-100%'
    });
};
/*Fin carga el Módulo de bandeja de entrada*/
/* Carga módulo de busqueda general */
function carga_buscador_general() {
    $("#contenido").load("buscador_general/index_buscador_general.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga módulo de busqueda general */
/* Carga módulo de busqueda inventario */
function carga_buscador_inventario() {
    $("#contenido").load("inventario/index_buscador_inventario.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga módulo de busqueda inventario */
/* Carga el módulo de creacion - modificacion expedientes */
function carga_creacion_expedientes() {
    $("#contenido").load("admin_expedientes/index_expedientes.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga el módulo de creacion - modificacion expedientes */
/* Carga módulo de inventario individual */
function carga_index_inventario_individual() {
    $("#contenido").load("inventario/index_individual.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga módulo de Inventario individual */
/* Carga módulo de inventario masivo */
function carga_index_masiva_inventario() {
    $("#contenido").load("inventario/index_masiva.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga módulo de Inventario masivo */
/*Carga el Módulo de modificacion*/
function carga_modificacion() {
    $("#contenido").load("radicacion/radicacion_entrada/menu_modificacion.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
};
/*Fin carga el Módulo de modificacion*/
/* Carga el Módulo de radicación de entrada*/
function carga_modificacion_rapida() {
    $("#contenido").load("radicacion/radicacion_entrada/menu_pendientes_modificacion.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
};
/*Fin carga el Módulo de radicación de entrada*/
/* Carga el módulo de prestamos */
function carga_modulo_prestamos() {
    $("#contenido").load("admin_prestamos/index_prestamos.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga el módulo de prestamos */
/* Carga el Módulo de Scanner */
function carga_modulo_scanner() {
    $("#contenido").load("scanner/escanear.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin Módulo Scanner */
/* Carga el Módulo de pendientes por aprobar */
function carga_pendientes_aprobar() {
    $("#contenido").load("radicacion/pendientes_firma_electronica.php", {
        tipo_consulta : 'pendientes_aprobar'
    });
    $('.menu_lat').animate({
        left: '-100%'
    });
}
/* Fin Módulo pendientes por aprobar */
/* Carga el Módulo de radicación interna */
function carga_radicado_normal() {
    $("#contenido").load("radicacion/radicacion_interna/index_normal.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
};
/* Fin carga el Módulo de radicación interna */
/* Carga el Módulo de radicación interna */
function carga_radicados_pendientes() {
    $("#contenido").load("bandejas/radicados_pendientes_usuario.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
};
/* Fin carga el Módulo de radicación interna */
/* Carga el Módulo de radicación de entrada */
function carga_radicacion_rapida() {
    $("#contenido").load("radicacion/radicacion_entrada/index_entrada.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
};

function carga_radicacion_entrada() {
    $("#contenido").load("radicacion/radicacion_entrada/index_entrada_normal.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
};

function carga_radicacion_salida(radicado, tipo_radicacion) {
    $("#contenido").load("radicacion/radicacion_salida/index_radicacion_salida.php", {
        radicado: radicado,
        tipo_radicacion: tipo_radicacion
    });
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
};

function carga_radicacion_salida2(radicado, tipo_radicacion) {
    $("#contenido").load("radicacion/radicacion_salida2/index_radicacion_salida.php", {
        radicado: radicado,
        tipo_radicacion: tipo_radicacion
    });
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
};
/* Carga el Módulo de radicación interno */
function carga_radicacion_interna(radicado, tipo_radicacion) {
    $("#contenido").load("radicacion/radicacion_interna/index_radicacion_interna.php", {
        radicado: radicado,
        tipo_radicacion: tipo_radicacion
    });
    $('.menu_lat').animate({
        left: '-100%'
    });
};
/* Fin carga el Módulo de radicación interno */
function carga_radicacion_resoluciones(radicado, tipo_radicacion) {
    $("#contenido").load("radicacion/radicacion_resoluciones/index_radicacion_resoluciones.php", {
        radicado            : radicado,
        tipo_radicacion     : tipo_radicacion
    });
    $('.menu_lat').animate({
        left: '-100%'
    });
};


/* Carga el módulo de rotulos */
function carga_rotulos_cajas() {
    $("#contenido").load("formatos/identificacion_cajas.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}

function carga_rotulos_carpetas() {
    $("#contenido").load("formatos/identificacion_carpetas.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga módulo de rotulos */
/* Carga el módulo de ubicación topográfica */
function carga_ubicacion_topografica() {
    $("#contenido").load("admin_ubicacion_topografica/index_ubicacion_topografica.php");
    $('.menu_lat').animate({
        left: '-100%'
    });
    // eliminar_borrador_radicado();
}
/* Fin carga módulo de ubicación topográfica */
/* Fin funciones para cargar div #contenido */
/************************************************************************/
/* Funcion para que al dar clic cargue el div #contenido */
function menu_superior() {
    $('.menu_superior').animate({
        left: '-100%'
    });
}
/* Cierra sesion */
function destruir_sesion() {
    $.ajax({ // Guardo registro de ingreso al sistema para auditoria
        type: 'POST',
        url: 'login/transacciones.php',
        data: {
            'transaccion': 'cerrar_sesion'
        },
        success: function(resp1) {
            if (resp1 == "true") {
                // sweetAlert({
                Swal.fire({
                    // position             : 'top-end',
                    showConfirmButton: true,
                    timer: 3000,
                    title: 'La sesión ha sido cerrada.',
                    text: 'Ingrese nuevamente usuario y contraseña.',
                    type: 'info'
                }).then(function(isConfirm) {
                    window.location.href = 'index.php';
                });
            } else {
                alert(resp1)
            }
        }
    })
}
/* Fin cierra sesion */
/* Funcion para mostrar cerrar_pestañas */
var array_radicados = new Array(); // Se crea el array para las pestañas de bandejas
function cerrar_pestana(radicado) {
    $("#tab" + radicado).removeClass('contenedor');
    $("#tab" + radicado).addClass('contenedor2');
}
/* Fin funcion para mostrar cerrar_pestañas */
/*Fin funcion para que al dar clic cargue el div #contenido*/
/**************************************************************
Inicio funcion para cargar listado de series */
/**
* @class La funcion consulta_listado_series2 consulta mediante ajax los <option> para desplegar en un input <select> las series documentales
* @description Genera los <option> de las series documentales según los parametros recibidos.
* @param {string} (serie_select) Si viene vacío es ignorado, si viene codigo de serie y existe en la dependencia es el <option selected>, si viene 
** codigo de serie pero no existe en la dependencia, agrega el <option> con codigo-nombre de serie y queda como <option selected>

* @param {string} (codigo_dependencia) Se usa para filtrar la consulta del listado de series. Si viene vacío la consulta va a retornar el listado 
** completo de series activas. 

* @param {string} (nombre_input) Es el id del input <select> donde se despliegan las opciones, los <option> que retorna esta funcion van a estar 
** dentro del select que debe ser por ejemplo 
** (<select id='codigo_subserie' title='Seleccione el código de la serie documental' class='select_opciones' onchange='validar_serie_subserie()'>
** <option value=''>No hay subseries asociadas a la serie seleccionada</option></select>)

* @return {string} String con los <option> del listado de series documentales según los parametros recibidos.
**************************************************************/
function consulta_listado_series2(serie_select, codigo_dependencia, nombre_input ) {
    $.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax'           : 'listado_series2',
            'serie_select'          : serie_select,
            'codigo_dependencia'    : codigo_dependencia
        },
        success: function(respuesta) {
            if (respuesta != "error") {
                $("#error_" + nombre_input).slideUp("slow");
                $("#" + nombre_input).html(respuesta);
            } else {
                $("#error_" + nombre_input).slideDown("slow");
            }
        }
    })
}
/* Fin funcion para cargar listado de series */
/**************************************************************
Inicio funcion para cargar listado de subseries */
/**
* @class La funcion cargar_codigo_subserie2 consulta mediante ajax los <option> para desplegar en un input <select> las subseries documentales
* @description Genera los <option> de las subseries documentales según los parametros recibidos.
* @param {string} (codigo_serie) Es obligatorio, si viene vacío retorna error

* @param {string} (codigo_subserie) Si viene vacío trae en los <option> el listado de subseries de la dependencia y serie que vienen como parametros. 
** Si viene codigo de subserie y existe en la dependencia con el codigo de serie es el <option selected>, si viene codigo de subserie pero no existe la 
** relacion dependencia-codigo_serie-codigo_subserie agrega el <option> con codigo-nombre de subserie y queda como <option selected> 

* @param {string} (codigo_dependencia) se usa para filtrar la consulta del listado de subseries. Si viene vacío retorna error. 
* @param {string} (formulario) si viene "trd_inventario" cambia la consulta de subseries.

* @param (nombre_input) es el nombre del input donde se despliegan las opciones, se debe declarar el select y las opciones van a estar dentro del select por ejemplo
** (<select id='codigo_subserie' title='Seleccione el código de la serie documental' class='select_opciones' onchange='validar_serie_subserie()'>
** <option value=''>No hay subseries asociadas a la serie seleccionada</option></select>)

* @return string con los <option> con el listado de series 
*************************************************************
*/
function cargar_codigo_subserie2(codigo_serie, codigo_subserie, codigo_dependencia, formulario, nombre_input) {
    $(".errores").slideUp("slow");
    $.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax'       : 'listado_subseries2',
            'formulario'        : formulario,
            'codigo_dependencia': codigo_dependencia,
            'codigo_serie'      : codigo_serie,
            'codigo_subserie'   : codigo_subserie
        },
        success: function(respuesta) {
            if (respuesta != "error") {
                $("#error_" + nombre_input).slideUp("slow");
                $("#" + nombre_input).html(respuesta);
            } else {
                $("#error_" + nombre_input).slideDown("slow");
                $("#" + nombre_input).html("<option value=''>No hay subseries asociadas a la serie seleccionada</option>");
            }
        }
    })
}
/* Fin funcion para cargar listado de subseries */
/*************************************************************************************/
/* Inicio funciones para Jonas en General */
/*************************************************************************************/
/**************************************************************
Inicio Funcion para cargar valor desde .art al input */
/**
 * @class La funcion cargar_input carga un input con un valor y oculta los <div class='art'>
 * @description Desde un listado de <div class='art'> se llama ésta función para cargar un input según los parametros recibidos.
 * @param {string} (input) Nombre del input que se va a cargar
 * @param {string} (valor) Valor que se va a poner en el input
 **************************************************************/
function cargar_input(input, valor) {
    $("#" + input).val(valor);
    $(".art").slideUp("slow");
    $("#" + input + "_invalido").slideUp("slow");
}
/* Fin funcion para cargar valor desde .art al input */
/* Funcion para delay en input para retrazar en 1 segundo - Se usa por ahora en el archivo radicacion/radicacion_entrada/modificacion_rapida.php[$('#numero_caja_archivo_central').keyup()] */
function delay(callback, ms) {
    var timer = 0;
    return function() {
        var context = this,
            args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function() {
            callback.apply(context, args);
        }, ms || 0);
    };
}
/**************************************************************
Inicio Funcion para aplicar validar_input con delay de 1 segundo */
/**
* @class La funcion validar_input_delay aplica validar_input con delay de 1 segundo a un input recibido
* @description Recibe como parámetro el nombre del div al cual se le aplica la funcion validar_input(nombre_input) 
** (función para quitar mayusculas y caracteres especiales), 

- Oculta los div con class='errores'
- Toma el valor del input recibido
- Con un retraso de 1 segundo aplica el cambio en el valor del input con la funcion validar_input(nombre_input)

* @param {string} (nombre_input) Nombre del input que se va a aplicar la funcion validar_input(nombre_input)
**************************************************************/
function validar_input_delay(nombre_input) {
    $(".errores").slideUp("slow");
    var valor_input = $("#" + nombre_input).val();
    if ($(this).data("lastval") != valor_input) {
        $(this).data("lastval", valor_input);
        clearTimeout(timerid);
        timerid = setTimeout(function() {
            validar_input(nombre_input);
        }, 1000);
    };
}
/* Fin funcion para delay en input */
/************************************************************** 
* @class Funcion para validar espacios, caracteres especiales, etc en el contenido de los input en todos los formularios.  

* @description Recibe el nombre del input, el tipo de conversion y una variable llamada loop para llamar recursivamente la misma funcion con el
** objetivo de eliminar varios espacios o varios caraceres especiales a la vez. 

* @param string{input} Nombre del input que se evalúa y donde finalmente se realiza la modificación. Con este parámetro se toma el valor del input 
** y hace un str.replace de cada uno de los caracteres especiales remplazandolo por un valor vacío donde corresponda.
** Dependiendo del input se pone en un switch para los casos donde el input es de tipo mail por lo que debe permitir la @ pero debe quitar las tildes,
** o está tambien el caso donde se deben eliminar tildes, comas, etc. O está también el caso donde se debe eliminar cualquier espacio en el input

* @param string{tipo_conversion} Define el tipo de conversion que se desea hacer en el input: puede ser capitales - para poner en mayúscula cada una de las 
** palabras que se ingresan, mayusculas - para poner en mayusculas todo el contenido, minusculas -  para poner en minúsculas todo el contenido, 
** primera - para poner únicamente la primera letra del contenido en mayúscula, sin_caracteres - para quitar todos los caracteres especiales del contenido.

* @param string{loop} Puede ser vacío por lo que se le asigna el valor "1" luego de recorrer una vez la funcion, suma 1 y se llama recursivamente 
** a sí misma hasta completar 5 llamados para reemplazar cada vez que se invoca, 5 caracteres especiales o espacios al mismo tiempo.

* @return {} No retorna valores. Mediante JQuery reemplaza el valor del input con las modificaciones correspondientes realizadas. 
**************************************************************/
function espacios_formulario(input, tipo_conversion, loop) {
    /* Puede ser vacío por lo que se le asigna el valor "1" */
    if (loop == null) {
        loop = 1;
    }

    /* Se toma el valor del input */
    var str = $('#' + input).val();

    /* Dependiendo del input se reemplaza el contenido */
    switch (input) {
        case 'gestionardatos_mail':
        case 'mail':
        case 'mail_doc':
        case 'mail_remitente':
        case 'mod_mail':
            // str = str.replace('ñ','n'); 
            // str = str.replace('Ñ','N');  str = str.replace('á','a'); str = str.replace('é','e');
            // str = str.replace('í','i');  str = str.replace('ó','o'); str = str.replace('ú','u');
            // str = str.replace('Á','A');  str = str.replace('É','E'); str = str.replace('Í','I');
            // str = str.replace('Ó','O');  str = str.replace('Ú','U'); 
            str = str.replace("'", "");
            str = str.replace("*", "");
            str = str.replace("+", '');
            str = str.replace("/", "");
            str = str.replace("\n", '');
            str = str.replace('  ', '');
            str = str.replace('!', '');
            str = str.replace('"', '');
            str = str.replace('#', '');
            str = str.replace('$', '');
            str = str.replace('%', '');
            str = str.replace('&', '');
            str = str.replace('(', '');
            str = str.replace(')', '');
            str = str.replace(',', '');
            str = str.replace(':', '');
            str = str.replace(';', '');
            str = str.replace('<', '');
            str = str.replace('=', '');
            str = str.replace('>', '');
            str = str.replace('?', '');
            str = str.replace('[', '');
            str = str.replace(']', '');
            str = str.replace('^', '');
            str = str.replace('{', '');
            str = str.replace('|', '');
            str = str.replace('}', '');
            str = str.replace('~', '');
            str = str.replace('¡', '');
            str = str.replace('°', '');
            str = str.replace('´', '');
            str = str.replace('¿', '');
            break;
        default:
            str = str.replace("'", "");
            str = str.replace("*", "");
            str = str.replace("+", '');
            str = str.replace("\n", '');
            str = str.replace('  ', ' ');
            str = str.replace('!', '');
            str = str.replace('"', '');
            str = str.replace('#', '');
            str = str.replace('$', '');
            str = str.replace('%', '');
            str = str.replace('&', '');
            str = str.replace(':', '');
            str = str.replace(';', '');
            str = str.replace('<', '');
            str = str.replace('=', '');
            str = str.replace('>', '');
            str = str.replace('?', '');
            str = str.replace('@', '');
            str = str.replace('[', '');
            str = str.replace(']', '');
            str = str.replace('^', '');
            str = str.replace('_', '');
            str = str.replace('{', '');
            str = str.replace('|', '');
            str = str.replace('}', '');
            str = str.replace('~', '');
            str = str.replace('¡', '');
            str = str.replace('°', '');
            str = str.replace('´', '');
            str = str.replace('¿', '');
            // break;
            break;    
    }

    /* Para que elimine tildes, comas, etc */
    switch (input) {
        case 'login':
        case 'mod_login':
        case 'pais':
            str = str.replace('/', '');
            str = str.replace('-', '');
            str = str.replace('#', '');
            str = str.replace(',', '');
            str = str.replace('Á', 'A');
            str = str.replace('É', 'E');
            str = str.replace('Í', 'I');
            str = str.replace('Ó', 'O');
            str = str.replace('Ú', 'U');
            str = str.replace('(', '');
            str = str.replace(')', '');
            break;
    }
    /* Para que elimine cualquier espacio en el input */
    switch (input) {
        case 'login':
        case 'mod_login':
            str = str.replace(' ', '');
            break;
    }

    /* Define el tipo de conversion que se desea hacer en el input y reemplaza según corresponda utilizando JQuery */
    switch (tipo_conversion) {
        case 'capitales':
            const re = /(^|[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ])(?:([a-záéíóúüñ])|([A-ZÁÉÍÓÚÜÑ]))|([A-ZÁÉÍÓÚÜÑ]+)/gu;
            $('#' + input + '').val(str.replace(re, (m, caracterPrevio, minuscInicial, mayuscInicial, mayuscIntermedias) => {
                const locale = ['es', 'gl', 'ca', 'pt', 'en'];
                //Son letras mayúsculas en el medio de la palabra
                // => llevar a minúsculas.
                if (mayuscIntermedias) return mayuscIntermedias.toLocaleLowerCase(locale);
                //Es la letra inicial de la palabra
                // => dejar el caracter previo como está.
                // => si la primera letra es minúscula, capitalizar si no, dejar como está.
                return caracterPrevio + (minuscInicial ? minuscInicial.toLocaleUpperCase(locale) : mayuscInicial);
            }));
            break;
        case 'mayusculas':
            $('#' + input).val(str.toUpperCase()); // Funcion para poner en mayuscula toda la palabra 
            break;
        case 'minusculas':
            $('#' + input).val(str.toLowerCase()); // Funcion para poner en mayuscula toda la palabra 
            break;
        case 'primera':
            $('#' + input).val(str.charAt(0).toUpperCase() + str.slice(1)); // Funcion para poner en mayuscula solo las letras capitales (Primera letra de cada palabra) 
            break;
        case 'sin_caracteres':
            str = str.replace('.', '');
            str = str.replace('#', '');
            str = str.replace(',', '');
            str = str.replace(' ', '');
            str = str.replace('/', '');
            str = str.replace('-', '');
            str = str.replace('(', '');
            str = str.replace(')', '');
            $('#' + input + '').val(str);
            break;
    }
    
    /* luego de recorrer una vez la funcion, suma 1 y se llama recursivamente a sí misma hasta completar 5 llamados */
    if (loop != 5) {
        loop += 1;
        espacios_formulario(input, tipo_conversion, loop)
    }
}

function trim(input) { // Funcion para quitar espacios de inicio y final de cada input.
    var str = $('#' + input).val();
    str = str.trim();
    $('#' + input).val(str);
}
/* Fin funcion para mayusculas en formularios */

/************************************************************** 
* @class Funcion para poner la imagen "loading" con el logo de Jonas.  
* @description Recibe el nombre del div como parámetro y reemplaza todo el contenido del div por el "loading" con el logo de Jonas
* @param string{input} Nombre del div que va a ser reemplazado por el logo de Jonas. 
* @return {} No retorna valores. Es para reemplazar el contenido del div. 
**************************************************************/
function loading(input) {
    $("#" + input).html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>")
}
/* Fin funcion para poner la imagen "loading" con el logo de Jonas */
/* Funcion para validar input en formularios */
function validar_input(nombre_input) {
    var input = $("#" + nombre_input).val();
    /* Verifica espacios y caracteres especiales */
    /*************************************************/
    switch (nombre_input) {
    /* Inicio verifica numerico o no */
        case 'total_folios':
            if (isNaN(input)) {
                $('#error_' + nombre_input).slideDown('slow');
            } else {
                $('#error_' + nombre_input).slideUp('slow');
            }
            espacios_formulario(nombre_input, 'mayusculas')
            validar_input_max(nombre_input);
            break;

        case 'dias_prestamo':
        case 'dias_tramite':
        case 'pago_mensual_contrato':
        case 'tiempo_archivo_central':
        case 'tiempo_archivo_central_mod':
        case 'tiempo_archivo_gestion':
        case 'tiempo_archivo_gestion_mod':
        case 'valor_contrato':
            if (isNaN(input)) {
                $('#error_' + nombre_input).slideDown('slow');
            } else {
                $('#error_' + nombre_input).slideUp('slow');
            }
            espacios_formulario(nombre_input, 'sin_caracteres');
            validar_input_max(nombre_input);
            validar_input_null(nombre_input);
            break;

        case 'identificacion':
        case 'mod_identificacion':
            if (isNaN(input)) {
                $('#error_' + nombre_input).slideDown('slow');
            } else {
                $('#error_' + nombre_input).slideUp('slow');
            }
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            validar_input_null(nombre_input);
            break;
    /* Fin verifica numerico o no */
    /*************************************************/
    /*************************************************/
    /* Desde aqui validacion sin espacios_formulario() */
        case 'aprueba_doc':
        case 'dependencia':
        case 'destinatarios_final':
        case 'elabora_doc':
        case 'firmante_seleccionado':
        case 'id_aprueba':
        case 'id_elabora':
        case 'ubicacion':
        case 'ubicacion2':
            validar_input_null(nombre_input);
            break;

        case 'cargo_usuario':
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            break;

        case 'asunto_radicado_adjunto_doc':
        case 'asunto_radicado_adjunto_exp':
        case 'destinatario':
        case 'destinatario_doc':
        case 'dignatario_remitente':
        case 'empresa_destinatario_doc':
        case 'login':
        case 'mensaje_derivar':
        case 'mensaje_informar':
        case 'mensaje_reasignar':
        case 'mod_login':
        case 'mod_nombre_dependencia':
        case 'nombre_dependencia':
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            validar_input_null(nombre_input); // Este case si tiene null
            break;

    /* Hasta aqui validacion sin espacios_formulario() */

    /* Desde aqui validacion con espacios_formulario(nombre_input, 'capitales') */
        case 'direccion_remitente':
        case 'observaciones_solicitud_prestamo':
        case 'telefono_remitente':
        // case 'search_radicado':
            espacios_formulario(nombre_input, 'capitales');
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            break;

        case 'descriptor':
        case 'nombre_documento':
        case 'nombre_subserie':
        case 'nombre_subserie_mod':
            espacios_formulario(nombre_input, 'capitales');
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            validar_input_null(nombre_input); 
            break;
    /* Hasta aqui validacion con espacios_formulario(nombre_input, 'capitales') */  

    /* Desde aqui validacion con espacios_formulario(nombre_input, 'mayusculas') */
            // case 'dependencia_padre':
        case 'mod_dependencia_padre':
            espacios_formulario(nombre_input, 'mayusculas');
            break;

        case 'caja_paquete_tomo':
        case 'consecutivo_desde':
        case 'consecutivo_hasta':
        case 'nivel_padre':
        case 'numero_caja_archivo_central':
        case 'numero_caja_paquete':
        case 'numero_carpeta':
        case 'observaciones':
            espacios_formulario(nombre_input, 'mayusculas');
            validar_input_max(nombre_input);
            break;

        case 'ubicacion_remitente':
            espacios_formulario(nombre_input, 'mayusculas');
            validar_input_min(nombre_input);
            break;

        case 'descripcion_anexos':
        case 'metadato_descriptor':
        case 'mod_nivel_padre':
        case 'numero_guia_radicado':
        case 'search_dependencia_destino':
        case 'search_dependencias':
        case 'search_nivel':
            espacios_formulario(nombre_input, 'mayusculas');
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            break;

        case 'codigo_dependencia':
        case 'codigo_dependencia_padre_sec':
        case 'codigo_dependencia_sec':
        case 'codigo_serie':
        case 'codigo_subserie':
        case 'codigo_subserie_mod':
        case 'departamento':
        case 'mod_departamento':
        case 'mod_nombre_nivel':
        case 'mod_pais':
        case 'municipio':
        case 'nombre_expediente':
        case 'nombre_expediente_mod':
        case 'nombre_mod_serie':
        case 'nombre_nivel':
        case 'nombre_serie':
        case 'pais':
        case 'seleccionar_expediente':
        case 'seleccionar_expediente_trd_exp':
            espacios_formulario(nombre_input, 'mayusculas');
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            validar_input_null(nombre_input); // Este case si tiene null
            break;
    /* Hasta aqui validacion con espacios_formulario(nombre_input, 'mayusculas') */

    /* Desde aqui validacion con espacios_formulario(nombre_input, 'minusculas') */
        /* Desde aqui validacion con validar_input_mail(nombre_input) */
        case 'mail_doc':
            espacios_formulario(nombre_input, 'minusculas');
            validar_input_mail(nombre_input);
            validar_input_max(nombre_input);
            break;

        case 'mail':
        case 'mail_remitente':
        case 'mod_mail':
            espacios_formulario(nombre_input, 'minusculas');
            validar_input_mail(nombre_input);
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            break;

        case 'gestionardatos_mail':
            espacios_formulario(nombre_input, 'minusculas');
            validar_input_mail(nombre_input);
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            validar_input_null(nombre_input);
            break;
    /* Hasta aqui validacion con espacios_formulario(nombre_input, 'minusculas') */

    /* Desde aqui validacion con espacios_formulario (nombre_input, 'primera') */
        case 'anexos_doc':
            espacios_formulario(nombre_input, 'primera');
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            break;

        case 'cargo_firmante':
        case 'cargo_aprueba':
        case 'cargo_elabora':
        case 'cargo_elabora_doc':
            espacios_formulario(nombre_input, 'primera');
            validar_input_max(nombre_input);
            validar_input_null(nombre_input);
            break;

        case 'asunto':
        case 'asunto_doc':
        case 'asunto_radicado':
        case 'cargo_aprueba_doc':
        case 'cargo_firmante_doc':
        case 'cargo_revisa_doc1':
        case 'cargo_revisa_doc2':
        case 'cargo_revisa_doc3':
        case 'cargo_revisa_doc4':
        case 'cargo_revisa_doc5':
        case 'cc_doc':
        case 'direccion_doc':
        case 'mod_nombre_completo':
        case 'nombre_completo':
        case 'observaciones_aprobar_documento':
        case 'observaciones_asociar_pdf_firmado':
        case 'procedimiento':
        case 'procedimiento_mod':
            espacios_formulario(nombre_input, 'primera');
            validar_input_max(nombre_input);
            validar_input_min(nombre_input);
            validar_input_null(nombre_input); // Este case si tiene null
            break;
    /* Hasta aqui validacion con espacios_formulario(nombre_input, 'primera') */
    }
    /* Fin verifica espacios y caracteres especiales */
    trim(nombre_input)
}
/* Fin funcion para validar input en formularios */

/* Funcion para validar input de tipo file */
function validar_input_file(nombre_input, viewer) {
    if (viewer == null) {
        viewer = "viewer";
    }
    var imagen = document.getElementById(nombre_input).files;
    var size_file = $("#" + nombre_input)[0].files[0].size;
    if (size_file < 8388608) { // Tamaño en bits para 8M ver archivo README
        $("#" + nombre_input + "_tamano").slideUp("slow");
        for (x = 0; x < imagen.length; x++) {
            if (imagen[x].type != "application/pdf") {
                $("#" + nombre_input + "_invalido").slideDown("slow");
                $('#' + viewer).empty();
                $('#' + viewer).attr('src', '');
                $('#' + viewer).slideUp("slow");
                return false;
            } else {
                $("#" + nombre_input + "_invalido").slideUp("slow");
                $("#viewer").slideDown("slow");
                $("#viewer3").html("");
                $("#viewer3").slideUp("slow");
                preview_pdf(nombre_input, viewer);
            }
        }
    } else {
        $("#" + nombre_input + "_tamano").slideDown("slow");
    }
}
/* Fin funcion para validar input de tipo file */
/* Funcion para previsualizar pdf en el formulario */
function preview_pdf(input, viewer) {
    pdffile = document.getElementById(input).files[0];
    pdffile_url = URL.createObjectURL(pdffile);
    // $('#viewer').attr('src',pdffile_url); 
    $('#' + viewer).attr('src', pdffile_url);
}
/* Fin funcion para previsualizar pdf en el formulario */

/**************************************************************
Inicio funcion para validar si es un PDF, el tamaño del archivo, animar div a 50% y mostrar PDF a cargar */
/**
* @class La funcion validar_input_file_animado(nombre_input,iframe_pdf_viewer, div_informacion) se utiliza para mostrar el div donde se visualiza un PDF, darle un ancho del 50% y dimensiona a otro div con informacion del 100% al 50% con el fin de que quede cada uno de los div con la mitad de la pantalla para mostrar la informacion y el PDF al tiempo.
* @description Recibe los parámetros de nombre_input, iframe_pdf_viewer y div_informacion para mostrar la vista previa de un PDF que se va a cargar. 

*** Aparte de esta funcion, debajo del input "nombre_input" debe existir un div con class="errores" y con id="nombre_input"_tamano para mostrar error cuando el tamaño en bits exceda el permitido por apache para cargar el documento. El div debe tener un span id="nombre_input_tamano_actual_pdf" para mostrar cuanto pesa el archivo que intenta cargar.
Ejemplo para un nombre_input="file1" = (<div id="file1_tamano" class="errores">El PDF que intenta cargar excede el limite permitido. Se puede cargar hasta 8Mb y usted intenta cargar un archivo con tamaño de <b><span id="file1_tamano_actual_pdf"></span> Mb</b>. Verifique por favor</div>)

*** Aparte de esta funcion, debajo del input "nombre_input" debe existir un div con class="errores" y con id="nombre_input"_invalido para mostrar error cuando el tipo de archivo que intenta cargar no sea un PDF.
Ejemplo para un nombre_input="file1" = (<div id="file1_invalido" class="errores">El archivo que intenta cargar no es un PDF. Verifique por favor</div>)

* @param {string} (nombre_input) Este parámetro toma el valor del input de tipo file con éste ID para cargar la vista previa.
* @param {string} (iframe_pdf_viewer) Este parámetro indica el nombre del div donde se encuentra el <iframe> que muestra el PDF en la vista previa.
* @param {string} (div_informacion) Este parámetro indica el nombre del div donde se encuentra la información o la tabla para redimensionarla a 50% y que deje el espacio necesario para desplegar el div "iframe_pdf_viewer".
**************************************************************/
function validar_input_file_animado(nombre_input, iframe_pdf_viewer, div_informacion) {
    $("#" + nombre_input + "_invalido").slideUp("slow");
    $("#" + nombre_input + "_tamano").slideUp("slow");
    var imagen = document.getElementById(nombre_input).files;
    var size_file = $("#" + nombre_input)[0].files[0].size;
    if (size_file < 8388608) { // Tamaño en bits para 8M ver archivo README
        $("#" + nombre_input + "_tamano").slideUp("slow");
    } else {
        $("#" + nombre_input + "_tamano").slideDown("slow");
        var newsize = size_file / 1000000;
        $("#" + nombre_input + "_tamano_actual_pdf").html(newsize);
    }
    for (x = 0; x < imagen.length; x++) {
        if (imagen[x].type != "application/pdf") {
            /* Muestra el div class='error' que indica que NO es un PDF*/
            $("#" + nombre_input + "_invalido").slideDown("slow");
            $("#" + div_informacion).animate({ // Para volver al 100% el ancho del div_informacion.
                width: "100%"
            }, {
                queue: false,
                duration: 500
            })
            $("#" + iframe_pdf_viewer).animate({ // Para volver al 0% el ancho del iframe_pdf_viewer.
                width: "0%",
                height: "0%"
            }, {
                queue: false,
                duration: 500
            })
            return false;
        } else {
            /* Oculta el div class='error' que indica que NO es un PDF*/
            $("#" + nombre_input + "_invalido").slideUp("slow");
            $("#" + div_informacion).animate({ // Para volver al 50% el ancho del div_informacion.
                width: "50%"
            }, {
                queue: false,
                duration: 500
            })
            $("#" + iframe_pdf_viewer).animate({ // Para volver al 50% el width de la tabla.
                width: "50%",
                height: "50vh"
            }, {
                queue: false,
                duration: 500
            })
            pdffile = document.getElementById(nombre_input).files[0];
            pdffile_url = URL.createObjectURL(pdffile);
            $('#' + iframe_pdf_viewer).attr('src', pdffile_url);
        }
    }
}
/* Fin funcion para validar si es un PDF, el tamaño del archivo, animar div a 50% y mostrar PDF a cargar */

/***************************************************************/
/* Funcion para validar tipo de archivo para imagenes png, jpg, etc */
// function valida_tipo_archivo(nombre_input) {
//     var imagen = document.getElementById(nombre_input).files;
//     if (nombre_input == 'imagen') {
//         viewer = "viewer";
//         error_imagen_invalida = "error_imagen_invalida";
//     } else {
//         viewer = "viewer2";
//         error_imagen_invalida = "error_imagen_invalida2";
//     }
//     if (nombre_input == 'imagen' && imagen.length == 0) {
//         $("#error_imagen").slideDown("slow");
//     } else {
//         $("#error_imagen").slideUp("slow");
//         for (x = 0; x < imagen.length; x++) {
//             if (imagen[x].type != "image/png" && imagen[x].type != "image/jpg" && imagen[x].type != "image/jpeg" && imagen[x].type != "image/gif") {
//                 $("#" + error_imagen_invalida).slideDown("slow");
//                 $('#' + viewer).attr('src', '');
//                 $("#" + viewer).slideUp("slow");
//                 return false;
//             } else {
//                 $("#" + error_imagen_invalida).slideUp("slow");
//                 $("#" + viewer).slideDown("slow");
//                 $("#viewer3").html("");
//                 preview_image(nombre_input, viewer);
//             }
//         }
//     }
// }
/* Funcion para validar tipo de archivo para imagenes png, jpg, etc */


/* Funcion para validar tipo de archivo para imagenes png, jpg, etc */
function valida_tipo_imagen(nombre_input,viewer,div_error_imagen_null,div_error_imagen_invalida,formato_imagen){
    var imagen = document.getElementById(nombre_input).files;
    if (imagen.length == 0) {
        $("#"+div_error_imagen_null).slideDown("slow");
    }else{
        $("#"+div_error_imagen_null).slideUp("slow");

        for (x = 0; x < imagen.length; x++) {
            if(formato_imagen=='png'){
                if (imagen[x].type != "image/png"){
                    $("#" + div_error_imagen_invalida).slideDown("slow");
                    $('#' + viewer).attr('src', '');
                    $("#" + viewer).slideUp("slow");
                    return false;
                }else{
                    $("#" + div_error_imagen_invalida).slideUp("slow");
                    $("#" + viewer).slideDown("slow");
                    preview_image(nombre_input, viewer);
                }
            }else{
                if (imagen[x].type != "image/png" && imagen[x].type != "image/jpg" && imagen[x].type != "image/jpeg" && imagen[x].type != "image/gif"){
                    $("#" + div_error_imagen_invalida).slideDown("slow");
                    $('#' + viewer).attr('src', '');
                    $("#" + viewer).slideUp("slow");
                    return false;
                }else{
                    $("#" + div_error_imagen_invalida).slideUp("slow");
                    $("#" + viewer).slideDown("slow");
                    preview_image(nombre_input, viewer);
                }
            }
        }    
    }
}
/* Funcion para validar tipo de archivo para imagenes png, jpg, etc */

/* Funcion para previsualizar imagenes en el formulario */
function preview_image(nombre_input, viewer) {
    file = document.getElementById(nombre_input).files[0];
    file_url = URL.createObjectURL(file);
    $("#" + viewer).attr('src', file_url);
    // var output = document.getElementById('viewer'); 
    //    output.src = URL.createObjectURL(event.target.files[0]);
}
/* Fin funcion para previsualizar imagenes en el formulario */
/* Funcion para previsualizar imagen principal de archivos */

/************************************************************** 
* @class Funcion que es invocada para visualizar un radicado desde el navegador. 

* @description Recibe la ruta del documento como parámetro. Debe existir un listado de radicados contenido en un div #lista_radicados_bandeja
** el cual redimensiona a 50% para dar paso al div #visor_adjuntos_pdf donde se despliega la imagen del PDF. 
** Se valida si el contenido de la ruta inicia con "hhtp" lo que indica que es un enlace a una ubicación externa al servidor por lo que no se mostraría 
** con <object data> sino en un <iframe> por lo que hay que configurar en el caso del <iframe> en el navegador o en el archivo config de apache del 
** servidor para que permita esta interacción.

* @param string{path_adjunto} Ruta de ubicación del documento PDF para visualizarlo. 
* @return {} No retorna valores.  Es para mostar u ocultar div de error. 
**************************************************************/
function visualizar_radicado(path_adjunto) {
    /* Redimensiona a 50% para dar paso al div #visor_adjuntos_pdf */
    $("#lista_radicados_bandeja").animate({ // Para volver al 50% el width de la tabla.
        width: "50%"
    }, {
        queue: false,
        duration: 1
    })
    /* Muestra el div donde se despliega la imagen del PDF */
    $("#visor_adjuntos_pdf").fadeIn("slow") // Para mostrar el visor_adjuntos_pdf

    /* Se valida si el contenido de la ruta inicia con "http" */
    var destino = path_adjunto.substring(0,4);

    if (destino=="http"){
        $("#visor_adjuntos_pdf").html("<iframe src='"+ path_adjunto + "' type='application/pdf' width='"+ancho_visor+"px' style='float:left; height:85vh;'></iframe>");
    }else{
        $("#visor_adjuntos_pdf").html("<object data='bodega_pdf/radicados/" + path_adjunto + "' type='application/pdf' style='height:85vh; width: calc(100% - "+$("#lista_radicados_bandeja").width()+"px)'></object>");
        $("#visor_adjuntos_pdf").html("<object data='bodega_pdf/radicados/" + path_adjunto + "' type='application/pdf'  style='height:"+$("#lista_radicados_bandeja").height()+"px; width: calc(100% - "+$("#lista_radicados_bandeja").width()+"px)'></object>");
    }
}
/* Fin funcion para previsualizar imagen principal de archivos */
/* Funcion para validar input de tipo mail */
function validar_input_mail(nombre_input) {
    var input = $("#" + nombre_input).val();
    var expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    switch (nombre_input) {
        case 'mail':
        case 'mail_doc':
        case 'mail_remitente':
        case 'mod_mail':
        case 'gestionardatos_mail':
            if (input.length > 0 && !expr.test(input)) {
                $('#' + nombre_input + "_formato_mail").slideDown("slow");
            } else {
                $('#' + nombre_input + "_formato_mail").slideUp("slow");
            }
            break;
    }
}
/* Fin funcion para validar input de tipo mail */
/* Funcion para validar maximo de caracteres input */
function validar_input_max(nombre_input) {
    var input = $("#" + nombre_input).val();
    var contador_max = input.length;
    switch (nombre_input) {
        case 'dias_prestamo':
        case 'dias_tramite':
            if (input > 30) { // Mayor al numero 30 no a la longitud de 30
                $('#' + nombre_input + "_max").slideDown("slow");
            } else {
                $('#' + nombre_input + "_max").slideUp("slow");
            }
            if(input==0){
                $('#' + nombre_input + "_cero").slideDown("slow");             
            }else{
                $('#' + nombre_input + "_cero").slideUp("slow");
            }
            break;
        case 'codigo_serie':
        case 'codigo_subserie':
        case 'codigo_subserie_mod':
        case 'tiempo_archivo_central':
        case 'tiempo_archivo_central_mod':
        case 'tiempo_archivo_gestion':
        case 'tiempo_archivo_gestion_mod':
            if (input.length > 3) {
                $("#" + nombre_input + '_contadormax').html(contador_max);
                $('#' + nombre_input + "_max").slideDown("slow");
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + "_max").slideUp("slow");
            }
            break;
        case 'codigo_dependencia':
        case 'codigo_dependencia_padre_sec':
        case 'codigo_dependencia_sec':
        case 'total_folios':
            if (input.length > 5) {
                $("#" + nombre_input + '_contadormax').html(contador_max);
                $('#' + nombre_input + "_max").slideDown("slow");
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + "_max").slideUp("slow");
            }
            break;
        case 'caja_paquete_tomo':
        case 'numero_caja_paquete':
            if (input.length > 10) {
                $("#" + nombre_input + '_contadormax').html(contador_max);
                $('#' + nombre_input + "_max").slideDown("slow");
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + "_max").slideUp("slow");
            }
            break;
        case 'consecutivo_desde':
        case 'consecutivo_hasta':
        case 'identificacion':
        case 'mod_identificacion':
        case 'numero_carpeta':
            if (input.length > 20) {
                $("#" + nombre_input + '_contadormax').html(contador_max);
                $('#' + nombre_input + "_max").slideDown("slow");
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + "_max").slideUp("slow");
            }
            break;
        case 'cargo_aprueba':
        case 'cargo_elabora':
        case 'cargo_firmante':
        case 'cargo_elabora_doc':
        case 'mod_pais':
        case 'pais':
            if (input.length > 30) {
                $("#" + nombre_input + '_contadormax').html(contador_max);
                $('#' + nombre_input + "_max").slideDown("slow");
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + "_max").slideUp("slow");
            }
            break;
                
        case 'departamento':
        case 'gestionardatos_mail':
        case 'login':
        case 'mail':
        case 'mail_doc':
        case 'mail_remitente':
        case 'mod_departamento':
        case 'mod_login':
        case 'mod_mail':
        case 'mod_nivel_padre':
        case 'mod_nombre_nivel':
        case 'municipio':
        case 'nivel_padre':
        case 'nombre_nivel':
        case 'numero_caja_archivo_central':
        case 'search_dependencias':
        case 'search_nivel':
        case 'search_radicado':
        case 'telefono_remitente':
            if (input.length > 50) {
                $("#" + nombre_input + '_contadormax').html(contador_max);
                $('#' + nombre_input + "_max").slideDown("slow");
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + "_max").slideUp("slow");
            }
            break;

        case 'cargo_aprueba_doc':
        case 'cargo_firmante_doc':
        case 'cargo_revisa_doc1':
        case 'cargo_revisa_doc2':
        case 'cargo_revisa_doc3':
        case 'cargo_revisa_doc4':
        case 'cargo_revisa_doc5':
        case 'cargo_usuario':
        case 'descripcion_anexos':
        case 'destinatario_doc':
        case 'dignatario_remitente':
        case 'direccion_remitente':
        case 'empresa_destinatario_doc':
        case 'mod_nombre_completo':
        case 'mod_nombre_dependencia':
        case 'nombre_completo':
        case 'nombre_dependencia':
        case 'numero_guia_radicado':
        case 'search_dependencia_destino':
        case 'seleccionar_expediente':
        case 'seleccionar_expediente_trd_exp':
            if (input.length > 100) {
                $('#' + nombre_input + '_max').slideDown('slow');
                var contador_max = input.length;
                $("#" + nombre_input + '_contadormax').html(contador_max);
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + '_max').slideUp('slow');
            }
            break;

        case 'nombre_mod_serie':
        case 'nombre_serie':
        case 'nombre_subserie':
        case 'nombre_subserie_mod':
            if (input.length > 150) {
                $('#' + nombre_input + '_max').slideDown('slow');
                var contador_max = input.length;
                $("#" + nombre_input + '_contadormax').html(contador_max);
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + '_max').slideUp('slow');
            }
            break;
        case 'cc_doc':
        case 'direccion_doc':
        case 'observaciones':
            if (input.length > 200) {
                $("#" + nombre_input + '_contadormax').html(contador_max);
                $('#' + nombre_input + '_max').slideDown('slow');
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + '_max').slideUp('slow');
            }
            break;
        case 'anexos_doc':
        case 'asunto':
        case 'asunto_doc':
        case 'asunto_radicado':
        case 'asunto_radicado_adjunto_doc':
        case 'asunto_radicado_adjunto_exp':
        case 'descriptor':
        case 'mensaje_derivar':
        case 'mensaje_informar':
        case 'mensaje_reasignar':
        case 'metadato_descriptor':
        case 'nombre_documento':
        case 'observaciones_aprobar_documento':
        case 'observaciones_asociar_pdf_firmado':
        case 'observaciones_solicitud_prestamo':
        case 'procedimiento':
        case 'procedimiento_mod':
            if (input.length > 500) {
                $("#" + nombre_input + '_contadormax').html(contador_max);
                $('#' + nombre_input + "_max").slideDown("slow");
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + "_max").slideUp("slow");
            }
            break;
        case 'nombre_expediente':
        case 'nombre_expediente_mod':
            if (input.length > 600) {
                $("#" + nombre_input + '_contadormax').html(contador_max);
                $('#' + nombre_input + "_max").slideDown("slow");
            } else {
                $("#" + nombre_input + '_contadormax').html("");
                $('#' + nombre_input + "_max").slideUp("slow");
            }
            break;
    }
}
/* Fin funcion para validar maximo de caracteres input */
/* Funcion para validar minimo de caracteres input */
function validar_input_min(nombre_input) {
    var input = $("#" + nombre_input).val();
    switch (nombre_input) {
        case 'search_dependencias':
        case 'search_nivel':
        case 'ubicacion_remitente':
            if (input.length < 3) {
                $('#' + nombre_input + "_min").slideDown("slow");
            } else {
                $('#' + nombre_input + "_min").slideUp("slow");
            }
            break;
        case 'anexos_doc':
        case 'cargo_aprueba_doc':
        case 'cargo_firmante_doc':
        case 'cargo_revisa_doc1':
        case 'cargo_revisa_doc2':
        case 'cargo_revisa_doc3':
        case 'cargo_revisa_doc4':
        case 'cargo_revisa_doc5':
        case 'cc_doc':
        case 'codigo_dependencia':
        case 'codigo_dependencia_padre_sec':
        case 'codigo_dependencia_sec':
        case 'codigo_serie':
        case 'codigo_subserie':
        case 'codigo_subserie_mod':
        case 'departamento':
        case 'descripcion_anexos':
        case 'destinatario_doc':
        case 'direccion_doc':
        case 'empresa_destinatario_doc':
        case 'login':
        case 'mod_departamento':
        case 'mod_login':
        case 'mod_nivel_padre':
        case 'mod_nombre_nivel':
        case 'mod_pais':
        case 'municipio':
        case 'nombre_mod_serie':
        case 'nombre_nivel':
        case 'nombre_serie':
        case 'nombre_subserie':
        case 'nombre_subserie_mod':
        case 'numero_guia_radicado':
        case 'pais':
        case 'seleccionar_expediente':
        case 'seleccionar_expediente_trd_exp':
            if (input.length < 3 && input.length != "") {
                $('#' + nombre_input + "_min").slideDown("slow");
            } else {
                $('#' + nombre_input + "_min").slideUp("slow");
            }
            break;
            
        case 'observaciones_solicitud_prestamo':
        case 'search_dependencia_destino':
        // case 'search_radicado':
            if (input.length < 6) {
                $('#' + nombre_input + "_min").slideDown("slow");
            } else {
                $('#' + nombre_input + "_min").slideUp("slow");
            }
            break;
        case 'asunto':
        case 'asunto_doc':
        case 'asunto_radicado':
        case 'asunto_radicado_adjunto_doc':
        case 'asunto_radicado_adjunto_exp':
        case 'cargo_titular_doc':
        case 'cargo_usuario':
        case 'descriptor':
        case 'dignatario_remitente':
        case 'direccion_remitente':
        case 'gestionardatos_mail':
        case 'identificacion':
        case 'mensaje_derivar':
        case 'mensaje_informar':
        case 'mensaje_reasignar':
        case 'metadato_descriptor':
        case 'mod_identificacion':
        case 'mod_nombre_completo':
        case 'mod_nombre_dependencia':
        case 'nombre_completo':
        case 'nombre_dependencia':
        case 'nombre_documento':
        case 'nombre_expediente':
        case 'nombre_expediente_mod':
        case 'observaciones_aprobar_documento':
        case 'observaciones_asociar_pdf_firmado':
        case 'procedimiento':
        case 'procedimiento_mod':
        case 'telefono_remitente':
            if (input.length < 6 && input.length != "") {
                $('#' + nombre_input + "_min").slideDown("slow");
            } else {
                $('#' + nombre_input + "_min").slideUp("slow");
            }
            break;
    }
}
/* Fin funcion para validar minimo de caracteres input */

/************************************************************** 
* @class Funcion que es invocada para validar si el input tiene un valor vacío.  
* @description Recibe el nombre del input como parámetro y en caso que esté vacío despliega el div de error llamado del mismo nombre del input 
** concatenando con "_null". En caso que no esté vacío el input, oculta el div de error.
* @param string{nombre_input} Nombre del input que se evalúa y del cual define el nombre del div "_error" cuando corresponde. 
* @return {} No retorna valores. Es para mostar u ocultar div de error. 
**************************************************************/
function validar_input_null(nombre_input) {
    var input = $("#" + nombre_input).val();
    if (input.length == 0) {
        $('#' + nombre_input + "_null").slideDown("slow");
    }else{
        $('#' + nombre_input + "_null").slideUp("slow");
    }
}
/* Fin funcion para validar null de input */

/* Funcion agregar pestaña */
function agregar_pestanas(numero_radicado) {
    var contador_pestana = $("#contador_pestanas").val();
    for (var i = 0; i <= contador_pestana; i++) {
        $("#tab" + array_radicados[i]).removeClass('focus');
    }
    if ($("#tab" + numero_radicado).length) { // Si la pestaña ya ha sido invocada
        $("#tab" + numero_radicado).removeClass('contenedor2');
        $("#tab" + numero_radicado).addClass('contenedor');
        $("#tab" + numero_radicado).addClass('focus');
    } else { // Si la pestaña es nueva.        
        array_radicados[contador_pestana] = (numero_radicado); // Guarda en array en la posicion "contador_pestana" el numero de radicado      
        $("#pestana" + contador_pestana).html("<li id='tab" + numero_radicado + "' class='contenedor focus' ><a href='#' class='tab_a' onclick=\"agregar_pestanas('" + numero_radicado + "')\" >" + numero_radicado + "</a><div class='boton_cerrar'><img src='imagenes/iconos/cerrar.png' width='20px' class='img_cerrar' onclick=\"cerrar_pestana('" + numero_radicado + "')\"></div></li>");
        contador_pestana = (parseInt(contador_pestana) + parseInt(1)); // Aumenta en 1 el valor del contador
        // console.log(array_radicados);
        $("#contador_pestanas").val(contador_pestana);
    }
    if ($("#titulo_buscador").is(":visible") || $("#resultados").is(":visible") || $("#resultados4").is(":visible") || $("#lista_radicados").is(":visible") ) { // Si viene del buscador general
        $.ajax({
            type: 'POST',
            url: 'bandejas/entrada/visualiza_radicado.php',
            data: {
                'radicado': numero_radicado
            },
            success: function(resp1) {
                $("#contenedor").slideUp("slow");
                $("#resultados4").slideDown("slow");
                $("#resultados4").html(resp1);
                $("#radicado").val(numero_radicado);
                $("#volver_resultados").slideDown("slow");
                $("#lista_radicados").html(resp1);
            }
        })
    }else{
        /* Si viene de la bandeja de entrada o salida */
        carga_bandeja_entrada('entrada', numero_radicado)
    }
}
/* Fin funcion agregar pestaña */
/* Funcion para mostrar radicado */
function mostrar_radicado(radicado_recibido) {
    // console.log(radicado_recibido)
    // var radicado_recibido = $("#radicado_recibido").val();
    if (radicado_recibido != 'general') {
        $.ajax({
            type: 'POST',
            url: 'bandejas/entrada/visualiza_radicado.php',
            data: {
                'radicado': radicado_recibido
            },
            success: function(resp1) {
                if (resp1 != "") {
                    $("#lista_radicados").html(resp1);
                    return false;
                } else {
                    alert(resp1)
                }
            }
        })
    }
}
/* Fin funcion para mostrar radicado */
/* Script para validar consecutivo exista en dependencia - Tipo radicado */
function valida_sec(tipo_radicacion) {
    switch (tipo_radicacion) {
        case '1':
            var tipo_documento = 'entrada';
            break;
        case '2':
            var tipo_documento = 'salida';
            break;
        case '3':
            var tipo_documento = 'radicacion normal';
            break;
        case '4':
            var tipo_documento = 'radicacion interna';
            break;
        case '5':
            var tipo_documento = 'radicacion resoluciones';
            break;
    }
    $.ajax({
        type: 'POST',
        url: 'radicacion/radicacion_entrada/buscador_remitente.php',
        data: {
            'tipo_radicacion': tipo_radicacion
        },
        success: function(resp) {
            $("#resultado_js").html(resp);          
        }
    })
}
/* Fin script para validar consecutivo exista en dependencia - Tipo radicado */
/*************************************************************************************/

/* Script para abrir ventana modal dependiendo la transaccion */
function abrir_ventana(transaccion){   
    $("#"+transaccion+"").slideDown("slow");
    switch(transaccion){
        case 'ventana_envio_radicado':
            $("#mensaje_reasignar").val("");
            $("#mensaje_reasignar").focus();
        break;
    }
    $("#contenido").css({'z-index':'100'}); // Modifico estilo para sobreponer ventana modal 
}
/* Fin script para abrir ventana modal dependiendo la transaccion */

/* Validar transaccion de cada uno de los botones (reasignar, derivar, informar, etc) */
    function validar_transaccion(transaccion,div){
        var radicado=$('#radicado').val();
        $("#numero_radicado").val(radicado);

        switch(transaccion){
            case 'archivar_radicado':
                transaccion2="ventana_archivar_radicado";
                $("#boton_archivar_radicado").html('<center><input type="button" value="Archivar Radicado" class="botones" onclick="valida_archivar_radicado()"><center>');
                break;
            case 'derivar_radicado':
                var expediente = $("#expediente").val();

                if(expediente!=""){
                    abrir_ventana('ventana_derivar_radicado');
                }else{
                    Swal.fire({ 
                        position            : 'top-end',
                        showConfirmButton   : false,
                        timer               : 2500, 
                        title               : 'Radicado Sin Expediente.',
                        text                : ' Puede asignarlo en la pestaña (Expedientes) del radicado '+radicado,
                        type                : 'warning'
                    });
                }
                break;
            case 'informar_radicado':
                abrir_ventana('ventana_informar_radicado');
                $("#usuarios_nuevos_informar").val("");
                break;
            case 'reasignar_radicado':
                $("#foto_usuario_destino").html("")
                abrir_ventana('ventana_envio_radicado');
                break;
            case 'responder_radicado':
                // $('#contenido').load('radicacion/radicacion_salida/index_salida.php',{radicado:radicado});
                carga_radicacion_salida(radicado);
                break;
        }

        $.ajax({    // Guardo registro de ingreso al sistema para auditoria
            type: 'POST',
            url: 'bandejas/entrada/transacciones_radicado.php',
            data: {
                'radicado'      : radicado,
                'transaccion'   : transaccion
            },          
            success: function(resp1){
                if(resp1!=""){
                    // console.log(div)
                    $("#"+div).html(resp1);
                }else{
                    alert(resp1)
                }
            }
        })
    }
/* Fin validar transaccion */
/* Fin funciones para Jonas en General */
/*************************************************************************************/
/* Funcion para botones clase accordion */
function cargar_accordion() {
    var acc = document.getElementsByClassName("accordion");
    // console.log(acc)
    for (var i = 0; i < acc.length; i++) {
        acc[i].onclick = function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
            $('#boton_expedientes').css('margin-top', '0px');
            $('#visor_pdf_pestana_documentos').slideUp("slow");
        }
    }
}
/* Fin funcion para botones clase accordion */
/*****************************************************************************************
    Function eliminar_borrador_radicado() Vacia la carptera donde se alojan los borradores de los radicados
/*****************************************************************************************/
// function eliminar_borrador_radicado(){
//     $.ajax({
//         type: 'POST',
//         url: 'include/procesar_ajax.php',
//         data: {
//             'recibe_ajax' : 'eliminar_borrador',
//         },
//         success: function(respuesta) {
//         }
//     })
// }
/*****************************************************************************************
    Fin function eliminar_borrador_radicado() Vacia la carptera donde se alojan los borradores de los radicados
/*****************************************************************************************/



/*  Futuro desarrollo para cuando pidan tipo especial de contraseña
function cambio_pass(){
    var usuario=$('#usuario').val();
    var pass1=$('#new_pass').val();
    var pass2=$('#confirma_pass').val();
    //alert(pass1)
    if(pass1.length< 5){
      alert("La clave debe tener al menos 5 caracteres");  
    }else if(pass1.length>10){
      alert("La clave no puede tener más de 10 caracteres");
    }
    else if(pass1!=pass2){
        alert("Las contraseñas no coinciden, Por favor intente nuevamente.")
    }else if(usuario=="-Seleccionar Usuario-"){
        alert("Seleccione un usuario por favor")
    }else if(pass1==''){
        alert("Ingrese una nueva contraseña")
    }else if (!pass1.match('[a-z]')){
        alert('La clave debe tener al menos una letra minúscula');
    }else if (!pass1.match('[A-Z]')){
        alert('La clave debe tener al menos una letra mayúscula');
    }else if (!pass1.match('[0-9]')){
        alert('La clave debe tener al menos un caracter numérico');
    }else{
        //alert("si pasa")
        $('#envia_pass').submit();
    }
}
*/