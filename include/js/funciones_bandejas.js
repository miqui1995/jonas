/* Script para ventana modal - Tecla Esc */
    window.addEventListener("keydown", function(event){
        var codigo = event.keyCode || event.which;
        if (codigo == 27){
          //  cerrar_ventana_agregar_contacto();
          //  cerrar_ventana_modifica_contacto();
            // $("#ventana_solicitar_prestamo").slideUp("slow");
        }
        if(codigo== 8){ // Opcion para restringir que la tecla backspace da atras en el navegador.
        	if (history.forward(1)) {
				location.replace(history.forward(1));
			}	
        }
    }, false);
/* Fin script para ventana modal - Tecla Esc */
/* Funciones para el checkbox */
var contador_checkbox   = 1;
var sel_todos           = "";

function cambia_checkbox_todos(carpeta_personal){
    var offset=$("#offset").val();
    var carpeta_personal=$("#carpeta_personal").val();

    if(contador_checkbox==0){
        $("#checkbox_general").html('<img src="imagenes/iconos/checkbox2.png" id="checkbox1" class="checkbox" alt="Seleccionar o deseleccionar todos los radicados">');
        contador_checkbox=1;
        $(".img1").addClass('img');
        $(".img").removeClass('img1');
        sel_todos="NO";
    }else{
        $("#checkbox_general").html('<img src="imagenes/iconos/checkbox1.png" id="checkbox1" class="checkbox" alt="Seleccionar o deseleccionar todos los radicados">');
        contador_checkbox=0;
        $(".img").addClass('img1');        
        $(".img1").removeClass('img');
        sel_todos="SI";   
    }

    $.ajax({    // llamo funcion recursiva para seleccionar todos los radicados.
        type: 'POST',
        url: 'bandejas/entrada/lista_radicados.php',
        data: {
            'pagina_checkbox_todos'     : offset,
            'selecciona_todos_checkbox' : sel_todos,
            'carpeta_personal'          : carpeta_personal  
        },          
        success: function(resp1){
            if(resp1!=""){
                $("#lista_radicados").html(resp1);
            }
        }
    })   
}
function cambia_checkbox(numero_radicado,toogle){
    if(toogle==1){
        $("#checkbox"+numero_radicado).html('<img src="imagenes/iconos/checkbox2.png" id="checkbox'+numero_radicado+'" class="checkbox" onclick="cambia_checkbox(\''+numero_radicado+'\',0)" alt="Seleccionar o deseleccionar radicado">');
        toogle=0;
    }else{
        $("#checkbox"+numero_radicado).html('<img src="imagenes/iconos/checkbox1.png" id="checkbox'+numero_radicado+'" class="checkbox" onclick="cambia_checkbox(\''+numero_radicado+'\',1)" alt="Seleccionar o deseleccionar radicado">');
        toogle=1;
    }
}

function paginacion(numero_pagina){
    var carpeta_personal=$("#carpeta_personal").val();
                
    $.ajax({    // Guardo registro de ingreso al sistema para auditoria
        type: 'POST',
        url: 'bandejas/entrada/lista_radicados.php',
        data: {
            'pagina'            : numero_pagina,
            'selecciona_todos'  : "SI",
            'carpeta_personal'  : carpeta_personal    
        },          
        success: function(resp1){
            if(resp1!=""){
                $("#lista_radicados").html(resp1);
                $("#checkbox_general").html('<img src="imagenes/iconos/checkbox1.png" id="checkbox1" class="checkbox" alt="Seleccionar o deseleccionar todos los radicados">');
                $("#offset").val(numero_pagina);
            }else{
                alert(resp1)
            }
        }
    })  
}

/* Funciones para guardar en base de datos auditoria de modificacion o creacion de radicados */
function auditoria(tipo_formulario,numero_radicado){
   // alert(tipo_formulario+numero_radicado)
    switch(tipo_formulario){
        case'radicado_leido':
        var ruta = 'login/transacciones.php';
            break;
        case'modificar_contacto':
        var trans ="El Contacto ha sido modificado";
        var ruta = 'login/transacciones.php';
            break;
        case'radicacion_entrada':
        var trans ="Documento radicado";    
        var ruta = 'login/transacciones.php';
            break;         
    }
    $.ajax({    
        type: 'POST',
        url: ruta,
        data: {
            'transaccion'   : tipo_formulario,
            'creado'        :  numero_radicado
        },          
        success: function(resp1){
            if(resp1=="true"){
                if(tipo_formulario!="radicado_leido"){
                    // sweetAlert({
                    Swal.fire({ 
                        position            : 'top-end',
                        showConfirmButton   : false,
                        timer               : 1500,     
                        title               : trans,
                        text                : '',
                        type                : 'success'
                    }).then(function(isConfirm){
                       // if(isConfirm){
                        switch(tipo_formulario){            
                            case 'enviar_radicado':
                            window.location.href='../../principal3.php'
                            break;
                        }
                       // }
                    })
                }
            }else{
                alert(resp1)
            }
        }
    })
}
function volver(){
    window.location.href='../principal3.php'
}   
/* Fin funciones para guardar en base de datos auditoria de modificacion o creacion de radicados */