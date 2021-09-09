$(function(){
	$("#archivo").on("change", function(){
		$("#resultados_masiva").slideDown("slow");
		$("#resultados_masiva").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");
		var formData = new FormData($("#formuploadajax")[0]);
		var ruta = "inventario/genera_query_masiva.php";

		var tamano_archivo= (this.files[0].size).toFixed(2);
		if(tamano_archivo>8000000){
			// sweetAlert({
			Swal.fire({	
				position 			: 'top-end',
			    showConfirmButton 	: false,
			    timer 				: 1500,	
                title	 			: "No se puede cargar el archivo.<br> ( "+tamano_archivo/1024/1024+" Mb )",
                text 	 			: 'El tamaño supera los 8 Mb permitidos. Revise por favor.',
                type 		 		: 'error'
            }).then(function(isConfirm){
                // if(isConfirm){
                carga_index_masiva_inventario();
                // }
            })
		}else{
			$("#contenedor").slideUp("slow");
			$.ajax({
				url	 		: ruta,
				type 		: "POST",
				data 		: formData,
				contentType : false,
				processData : false,

				success: function(datos){
					$("#resultados_masiva").html(datos);
				}
			});
		}
	});
});
function form_inventario(){
	loading('boton_insertar_inventario');

    $.ajax({
		url 	: "inventario/ejecuta_query_masiva.php",
		type 	: "POST",
		data:{
			'query_inventario' : 'SI'
		},
		success: function(respuesta){
			$("#resultados_masiva").html(respuesta);
			$("#resultados_masiva").slideDown("slow");
		}
	});
}
function descargar_csv(){
	location.href ="inventario/Formato_FUID_Jonas.xls";
}
function volver_busqueda(){
	$("#contenedor").slideDown("slow");
	$("#resultados_masiva").slideUp("slow");
	$("#nombre_documento").focus();
}
function buscar_inventario(){
	var numero_consecutivo=$("#numero_consecutivo").val();
	var codigo_dependencia=$("#codigo_dependencia").val();
	var codigo_serie=$("#codigo_serie").val();
	var codigo_subserie=$("#codigo_subserie").val();
	var fecha_inicial=$("#fecha_inicial").val();
	var fecha_final=$("#fecha_final").val();
	var consecutivo_jonas=$("#consecutivo_jonas").val();
	var caja_paquete_tomo_otro=$("#caja_paquete_tomo_otro").val();
	var numero_caja_paquete=$("#numero_caja_paquete").val();
	var numero_carpeta=$("#numero_carpeta").val();
	var numero_caja_paquete=$("#numero_caja_paquete").val();
	var numero_caja_archivo_central=$("#numero_archivo_central").val();
	var nombre_documento=$("#nombre_documento").val();
	var metadato=$("#metadato").val();

	var consulta_total=numero_consecutivo+codigo_dependencia+codigo_serie+codigo_subserie+fecha_inicial+fecha_final
	+consecutivo_jonas+caja_paquete_tomo_otro+numero_caja_paquete+numero_carpeta+numero_caja_paquete
	+numero_caja_archivo_central+nombre_documento+metadato;
	
	$("#resultados_masiva").html("<center><h3><img src='imagenes/logo.gif' alt='Cargando...' width='100' class='imagen_logo'><br>Cargando...</h3></center>");	


	if(consulta_total==""){
		$('#error_campos_vacio').slideDown('slow');
	}else{
		$("#contenedor").slideUp("slow");
		$("#resultados_masiva").slideDown("slow");
		$('#error_campos_vacio').slideUp('slow');
		$.ajax({
			url:"inventario/buscador_inventario.php",
			type:"POST",
			data:{
				'numero_consecutivo' : numero_consecutivo,
				'codigo_dependencia': codigo_dependencia,
				'codigo_serie': codigo_serie,
				'codigo_subserie': codigo_subserie,
				'fecha_inicial': fecha_inicial,
				'fecha_final':fecha_final,
				'consecutivo_jonas': consecutivo_jonas,
				'caja_paquete_tomo_otro': caja_paquete_tomo_otro,
				'numero_caja_paquete' : numero_caja_paquete,
				'numero_carpeta' : numero_carpeta,
				'numero_caja_paquete' : numero_caja_paquete,
				'numero_caja_archivo_central' : numero_caja_archivo_central,
				'nombre_documento' : nombre_documento,
				'metadato' : metadato
			},
			success: function(respuesta){
				$("#resultados_masiva").html(respuesta);
				$("#resultados_masiva").slideDown("slow");
			}
		});	
	}


}
/* Funciones para guardar en base de datos auditoria de modificacion o creacion de usuarios */
function display_busq_av(){
	$(".busq_av").show("blind");
	$("#busq_basica").hide("blind");
	$("#nombre_documento").focus();
}
function display_busq_basica(){
	$(".busq_av").hide("blind");
	$("#busq_basica").show("blind");
	$("#nombre_documento").focus();
}
function ocultar_detalle_busqueda(){
	$(".detalle_busqueda").hide("blind");
	$("#ver_detalle").show("blind");
}
function ver_detalle_busqueda(){
	$(".detalle_busqueda").show("blind");
	$("#ver_detalle").hide("blind");
}
function auditoria(tipo_formulario,login){	
	$.ajax({	// Guardo registro de ingreso al sistema para auditoria
		type: 'POST',
		url: 'login/transacciones.php',
		data: {
			'transaccion' : tipo_formulario,
			'creado' : 	login
		},			
		success: function(resp1){
			if(resp1=="true"){
				// sweetAlert({
				Swal.fire({	
					position 			: 'top-end',
				    showConfirmButton 	: false,
				    timer 				: 1500,	
				    title 				: "La inserción a inventario masiva se ha realizado correctamente.",
				    text 				: '',
				    type 				: 'success'
				}).then(function(isConfirm){
					// if(isConfirm){
					volver();
					// }
				})
			}else{
				alert(resp1)
			}
		}
	})
}
function volver(){
	window.location.href='principal3.php'
}		
/* Fin funciones para guardar en base de datos auditoria de modificacion o creacion de usuarios */