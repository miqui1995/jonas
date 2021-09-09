/* Script para cargar contenido en div "contenido_parametrizacion" */
function carga_contenido_param(tipo_param){
	$.ajax({
		url:'normatividad/buscador.php',
		type: 'POST',
		data: {
			'parametro' : tipo_param
		},
		success: function(resp){
				if(resp!=""){
				$('#contenido_param').html(resp);
			}
		}
	})
}
/* Fin script para cargar contenido en div "contenido_parametrizacion" */
/* Script para cargar contenido del Modulo de Normatividad*/
function tipo_normatividad(tipo) {
	$('#contenido_param').html("");

	if(tipo=="normatividad_entidad"){
		$('#normatividad_entidad').show("slow");
		$('#normatividad_general').hide("slow");
	}else{
		$('#normatividad_entidad').hide("slow");
		$('#normatividad_general').show("slow");		
	}
}
/* Fin script para cargar contenido del Modulo de Normatividad*/