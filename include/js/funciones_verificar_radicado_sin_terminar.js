$(function() {
	$.ajax({
        type: 'POST',
        url: 'include/procesar_ajax.php',
        data: {
            'recibe_ajax'       : 'revisar_existencias_radicados'
        },
        success: function(respuesta_ajax) {
            if(respuesta_ajax == 'NO VACIO'){
    	  		Swal.fire({
			        title 				: 'Parece que te saliste cuando estabas realizando una Radicacion interna.\n\n¿Seguir Editándolo?',
			        text 				: "Esta acción no se puede revertir",
			        type 				: 'warning',
			        showCancelButton 	: true,
			        confirmButtonColor 	: '#3085d6',
			        cancelButtonColor 	: '#d33',
			        confirmButtonText 	: 'Si, seguir con el documento !',
			        cancelButtonText 	: 'Eliminar el borrador'

			    }).then((result) => {
			        if (result.value) {
				        $("#contenido").load("radicacion/radicacion_interna/index_radicacion_interna.php", {
					        tipo_radicacion_interna: "borrador"
					    });
					    $('.menu_lat').animate({
					        left: '-100%'
					    });
				    }else{
				    	$.ajax({
					        type: 'POST',
					        url: 'include/procesar_ajax.php',
					        data: {
					            'recibe_ajax'       : 'eliminar_borrador'
					        },
	        				success: function(respuesta_ajax_2){
	        				}
    					})
				    }
			    })
            }
        }
    })
});