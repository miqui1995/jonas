<!DOCTYPE html>
<html>
<head>
	<title>Formato de identificación de carpetas</title>
	<style type="text/css">
		.half{
			width: 50%;
		}
		.muestra_select{
			display: none;
		}
		.table_general{
			border: solid; 
			float: left; 
			width: 350px; 
			height: 450px; 
		}
		.titulos{
			font-size: 12px;
			text-decoration: underline #666;   /* Subrayado gris */
		}
		.subtitulos{
			font-size: 18px;
			text-align: center;
		}
		.subtitulos_vacio{
			min-height: 15px;
		}
	</style>
</head> 
<body>
	<script type="text/javascript">
		function agregar_rotulo(){
			$('#boton_imprimir').slideUp('slow');
			var contador=0;
			if($("#rotulo_carpeta1").is(":visible")){
				contador=	parseInt(contador) + parseInt(2);
			}
			if($("#rotulo_carpeta2").is(":visible")){
				contador=	parseInt(contador) + parseInt(1);
			}
			if($("#rotulo_carpeta3").is(":visible")){
				contador=	parseInt(contador) + parseInt(1);
			}
			if($("#rotulo_carpeta4").is(":visible")){
				contador=	parseInt(contador) + parseInt(1);
			}
			if($("#rotulo_carpeta5").is(":visible")){
				contador=	parseInt(contador) + parseInt(1);
			}
			if($("#rotulo_carpeta6").is(":visible")){
				contador=	parseInt(contador) + parseInt(1);
			}
				console.log(contador);
			$("#rotulo_carpeta"+contador).slideDown("slow");
			
			if(contador==6){
				$("#agregar_rotulo").slideUp("fast");
			}
		}
		function cambia_entidad(){
			var entidad=$("#select_entidad").val();
			var nombre_entidad="";
			var imagen_entidad="";
			
			switch(entidad){
				case 'litigando_punto_com':
					nombre_entidad="(L01) Litigando Punto Com";
					imagen_entidad="<img src='imagenes/logos_entidades/logo_litigando_punto_com.png' style='width: 90px;height: 40px;'>";
				break;
				case 'ministerio_agricultura':
					nombre_entidad="(MA1) Ministerio de Agricultura";
					imagen_entidad="<img src='imagenes/logos_entidades/logo_ministerio_agricultura.png' style='width: 90px;height: 40px;'>";
				break;
			}
			cargar_json(1);
			cargar_json(2);
			cargar_json(3);
			cargar_json(4);
			cargar_json(5);
			cargar_json(6);

			$("#muestra_codigo_entidad1").html(nombre_entidad);
			$("#muestra_codigo_entidad2").html(nombre_entidad);
			$("#muestra_codigo_entidad3").html(nombre_entidad);
			$("#muestra_codigo_entidad4").html(nombre_entidad);
			$("#muestra_codigo_entidad5").html(nombre_entidad);
			$("#muestra_codigo_entidad6").html(nombre_entidad);
			$("#logo_entidad1").html(imagen_entidad);
			$("#logo_entidad2").html(imagen_entidad);
			$("#logo_entidad3").html(imagen_entidad);
			$("#logo_entidad4").html(imagen_entidad);
			$("#logo_entidad5").html(imagen_entidad);
			$("#logo_entidad6").html(imagen_entidad);
			$("#muestra_codigo_entidad1").slideDown("fast"); 
			$("#muestra_input_codigo_entidad1").slideUp("fast");
		}
		function cambia_nombre(campo){
			var nombre_campo=$("#"+campo).val();
			$("#muestra_"+campo).html(nombre_campo);
			$("#muestra_"+campo).slideDown("fast");
			$("#muestra_input_"+campo).slideUp("fast");
		}
		function cambia_nombre_expediente(campo){
			var nombre_expediente 	=$("#nombre_expediente"+campo).val();
			
			$("#muestra_nombre_expediente"+campo).html(nombre_expediente);
			$("#muestra_nombre_expediente"+campo).slideDown("fast");
			$("#muestra_input_nombre_expediente"+campo).slideUp("fast");
			$("#nombre_expediente"+campo).val(nombre_expediente);

			if(nombre_expediente==""){
				$("#error_nombre_expediente"+campo).slideDown("fast");
			}else{
				$("#error_nombre_expediente"+campo).slideUp("fast");

				$.ajax({
					type: 'POST',
					url:  'formatos/buscador_formatos.php' ,
					data: {
						'nombre_expediente'	: nombre_expediente,
						'num'				: campo
					},
					success: function(resp){
						if(resp!=""){
							 console.log(resp);
							$("#resultado").html(resp);
							$("#sugerencias_nombre_expediente"+campo).html(resp);
							cargar_json(campo);
						}
					}
				})	
			}
		}
		function cambia_numero_expediente(campo){
			var numero_expediente=$("#numero_expediente"+campo).val();

			$("#muestra_numero_expediente"+campo).html(numero_expediente);
			$("#muestra_numero_expediente"+campo).slideDown("fast");
			$("#muestra_input_numero_expediente"+campo).slideUp("fast");
			$("#numero_expediente"+campo).val(numero_expediente);

			if(numero_expediente==""){
				$("#error_numero_expediente"+campo).slideDown("fast");
			}else{
				$("#error_numero_expediente"+campo).slideUp("fast");

				$.ajax({
					type: 'POST',
					url:  'formatos/buscador_formatos.php' ,
					data: {
						'numero_expediente'	: numero_expediente,
						'num'				: campo
					},
					success: function(resp){
						if(resp!=""){
							 console.log(resp);
							$("#resultado").html(resp);
							$("#sugerencias_numero_expediente"+campo).html(resp);
							cargar_json(campo)
						}
					}
				})	
			}
			
		}
		function cargar_informacion_expediente(dependencia_expediente,fecha_inicial,fecha_final,nombre_expediente,numero_expediente,serie,subserie,num){
			$("#nombre_seccion"+num).val(dependencia_expediente);
			$("#muestra_nombre_seccion"+num).html(dependencia_expediente);
			$("#serie_documental"+num).val(serie);
			$("#muestra_serie_documental"+num).html(serie);
			$("#subserie_documental"+num).val(subserie);
			$("#muestra_subserie_documental"+num).html(subserie);
			$("#numero_expediente"+num).val(numero_expediente);
			$("#muestra_numero_expediente"+num).html(numero_expediente);
			$("#nombre_expediente"+num).val(nombre_expediente);
			$("#muestra_nombre_expediente"+num).html(nombre_expediente);
			$("#fecha_inicial_"+num).val(fecha_inicial);
			$("#fecha_desde"+num).html(fecha_inicial);
			$("#fecha_final_"+num).val(fecha_final);
			$("#fecha_hasta"+num).html(fecha_final);

			$("#sugerencias_numero_expediente"+num).html("");
			$("#sugerencias_nombre_expediente"+num).html("");

			$("#error_numero_expediente"+num).slideUp("fast");

			$('#boton_imprimir').slideDown('slow');

			cargar_json(num);
		}
		function cargar_json(num){
			var correlativo 		= $("#correlativo"+num).val();
			var entidad 			= $("#select_entidad").val();
			var fecha_final 		= $("#fecha_final_"+num).val();
			var fecha_inicial 		= $("#fecha_inicial_"+num).val();
			var folios 		 		= $("#folios"+num).val();
			var fondo 				= $("#nombre_fondo"+num).val();
			var nombre_expediente 	= $("#nombre_expediente"+num).val();
			var numero_expediente 	= $("#numero_expediente"+num).val();
			var seccion 			= $("#nombre_seccion"+num).val();
			var serie 	 			= $("#serie_documental"+num).val();
			var subseccion 			= $("#nombre_sub_seccion"+num).val();
			var subserie 		 	= $("#subserie_documental"+num).val();

			console.log(entidad + " - "+num+ " - "+fondo+ " - "+subseccion);
			
			$.ajax({
				type: 'POST',
				url:  'formatos/buscador_formatos.php' ,
				data: {
					'cargar_json'			: "SI",
					'correlativo'			: correlativo,
					'entidad'				: entidad,
					'fecha_final'			: fecha_final,
					'fecha_inicial'			: fecha_inicial,
					'folios'				: folios,
					'fondo'					: fondo,
					'nombre_expediente'		: nombre_expediente,
					'num'					: num,
					'numero_expediente'		: numero_expediente,
					'seccion'				: seccion,
					'serie'					: serie,
					'subseccion'			: subseccion,
					'subserie'				: subserie
				},
				success: function(resp){
					if(resp!=""){
						 console.log(resp);
						$("#resultado").html(resp);
						
					}
				}
			})

			if($("#boton_imprimir").is(":visible")){
				$("#imprimir_carpeta").focus();
			}else{
				$("#rotulo_carpeta"+num).focus();
			}
		}
		function imprimir_rotulos_carpeta(){
				var carpeta1 = $("#carpeta1").val();
				var carpeta2 = $("#carpeta2").val();
				var carpeta3 = $("#carpeta3").val();
				var carpeta4 = $("#carpeta4").val();
				var carpeta5 = $("#carpeta5").val();
				var carpeta6 = $("#carpeta6").val();

			if($("#rotulo_carpeta6").is(":visible")){				
				var json= '{"rotulos_carpetas":['+carpeta1+','+carpeta2+','+carpeta3+','+carpeta4+','+carpeta5+','+carpeta6+']}';
			}else if($("#rotulo_carpeta5").is(":visible")){				
				var json= '{"rotulos_carpetas":['+carpeta1+','+carpeta2+','+carpeta3+','+carpeta4+','+carpeta5+']}';
			}else if($("#rotulo_carpeta4").is(":visible")){				
				var json= '{"rotulos_carpetas":['+carpeta1+','+carpeta2+','+carpeta3+','+carpeta4+']}';
			}else if($("#rotulo_carpeta3").is(":visible")){
				var json= '{"rotulos_carpetas":['+carpeta1+','+carpeta2+','+carpeta3+']}';
			}else if($("#rotulo_carpeta2").is(":visible")){
				var json= '{"rotulos_carpetas":['+carpeta1+','+carpeta2+']}';
			}else{
				var json= '{"rotulos_carpetas":['+carpeta1+']}';
			}

			$("#rotulos_carpetas").val(json);
			console.log(json);
			$("#formulario_api").submit();
		}
		function limpia_formulario(num){
			$('#muestra_cantidad_carpetas'+num).html('');
			$('#fecha_desde'+num).html('Desde');
			$('#fecha_hasta'+num).html('Hasta');
			$('#numero_caja'+num).focus();
		}
		function muestra(campo){
			$("#muestra_"+campo).slideUp("fast");
			$("#muestra_input_"+campo).slideDown("fast");
			$("#"+campo).focus();

			if(campo=="codigo_entidad1"){
				$("#select_entidad").focus();
			}
		}
	</script>
	<div class="contenido">	
	<table border="1" id="rotulo_carpeta1" class="table_general" style="margin-left: 20px;" >
		<tr>
			<td id="logo_entidad1" class="center" rowspan="3">
				<img src="imagenes/logos_entidades/logo_litigando_punto_com.png" style='width: 90;height: 40;' >
			</td>
			<tr class="titulos center">
				<td rowspan="2"  style="width: 140px">Formato <br> Identificación de Carpetas</td>
				<td style="width: 100px">Versión 1</td>
			</tr>
			<tr class="titulos center">
				<td class="celda1">Fecha de Edición<br>15-09-2018</td>
			</tr>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table id="fondo_sec_subsec" class="fondo_sec_subsec" border="1">
						<tr>
							<td class="half titulos" >
								Codigo de la Entidad (Remitente)	
							</td>
							<td class="half titulos" >
								Fondo
							</td>
						</tr>
						<tr>
							<td id ="codigo_entidad" class="half subtitulos">
								<div id="muestra_codigo_entidad1" onclick="muestra('codigo_entidad1')">(L01) Litigando Punto Com</div>
								<div id="muestra_input_codigo_entidad1" class="muestra_select">
									<select id="select_entidad" class='select_opciones' onchange="cambia_entidad()" onblur="cambia_entidad()">
										<option value="litigando_punto_com">Litigando punto com</option>
										<option value="ministerio_agricultura">Ministerio de Agricultura</option>
									</select>
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_fondo1" class="subtitulos_vacio" onclick="muestra('nombre_fondo1')"></div>
								<div id="muestra_input_nombre_fondo1" class="muestra_select">
									<input type="text" id="nombre_fondo1" onblur="cambia_nombre('nombre_fondo1'); cargar_json('1')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_fondo1'); cargar_json('1'); return false;}" maxlength="15">
								</div>
							</td>
						</tr>
						<tr>
							
							<td class="half titulos">
								Dependencia - Seccion
							</td>
							<td class="half titulos">
								Oficina Productora - Subseccion
							</td>
						</tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_nombre_seccion1" class="subtitulos_vacio" onclick="muestra('nombre_seccion1')"></div>
								<div id="muestra_input_nombre_seccion1" class="muestra_select">
									<input type="text" id="nombre_seccion1" onblur="cambia_nombre('nombre_seccion1'); cargar_json('1')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_seccion1'); cargar_json('1'); return false;}" maxlength="15">
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_sub_seccion1" class="subtitulos_vacio" onclick="muestra('nombre_sub_seccion1')"></div>
								<div id="muestra_input_nombre_sub_seccion1" class="muestra_select">
									<input type="text" id="nombre_sub_seccion1" onblur="cambia_nombre('nombre_sub_seccion1'); cargar_json('1')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_sub_seccion1'); cargar_json('1'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>

						<tr>
							<td class="half titulos" >
								Serie Documental
							</td>
							<td class="half titulos">
								Subserie Documental
							</td>
						<tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_serie_documental1" class="subtitulos_vacio" onclick="muestra('serie_documental1')"></div>
								<div id="muestra_input_serie_documental1" class="muestra_select">
									<input type="text" id="serie_documental1" onblur="cambia_nombre('serie_documental1'); cargar_json('1')" onkeyup="if (event.keyCode==13){cambia_nombre('serie_documental1'); cargar_json('1'); return false;}" maxlength="15" >
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_subserie_documental1" class="subtitulos_vacio" onclick="muestra('subserie_documental1')"></div>
								<div id="muestra_input_subserie_documental1" class="muestra_select">
									<input type="text" id="subserie_documental1" onblur="cambia_nombre('subserie_documental1'); cargar_json('1')" onkeyup="if (event.keyCode==13){cambia_nombre('subserie_documental1'); cargar_json('1'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Numero Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_numero_expediente1" class="subtitulos_vacio" onclick="muestra('numero_expediente1')"></div>
								<div id="muestra_input_numero_expediente1" class="muestra_select">
									<input type="text" id="numero_expediente1" onkeyup="if (event.keyCode==13){cambia_numero_expediente('1'); return false;}" maxlength="23" >
								</div>
								<div id="error_numero_expediente1" class="errores">El numero de expediente no existe en la base de datos</div>
								<div id="sugerencias_numero_expediente1"></div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Nombre Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_expediente1" class="subtitulos_vacio" onclick="muestra('nombre_expediente1')"></div>
								<div id="muestra_input_nombre_expediente1" class="muestra_select">
									<input type="text" id="nombre_expediente1" onkeyup="if (event.keyCode==13){cambia_nombre_expediente('1'); return false;}" maxlength="30" >
								</div>
								<div id="error_nombre_expediente1" class="errores">El nombre del expediente no existe en la base de datos</div>
								<div id="sugerencias_nombre_expediente1"></div>
							</td>
						</tr>
				</table>
				</center>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table border="0" width="100%" class="center">
						<tr style="font-size: 18px;">	
							<td colspan="2" class="titulos" >
								Fechas Extremas
							</td>
						</tr>
						<tr>
							<td class="half">
								Desde									
								<div id="fecha_desde1" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_inicial_1">
							</td>
							<td class="half">
								Hasta									
								<div id="fecha_hasta1" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_final_1">
							</td>
						</tr>
					</table>
					<table border="1" width="95%">
						<tr class="titulos center">
							<td style="width: 50%;">
								Folios
							</td>	
							<td>
								Numero Correlativo
							</td>
						</tr>
						<tr>	
							<td class='subtitulos'>
								<div id="muestra_folios1" class="subtitulos_vacio" onclick="muestra('folios1')"></div>
								<div id="muestra_input_folios1" class="muestra_select">
									<input type="text" id="folios1" onblur="cambia_nombre('folios1'); cargar_json('1')" onkeyup="if (event.keyCode==13){cambia_nombre('folios1'); cargar_json('1'); return false;}" maxlength="5" >
								</div>
							</td>

							<td class="subtitulos">
								<div id="muestra_correlativo1" class="subtitulos_vacio" onclick="muestra('correlativo1')"></div>
								<div id="muestra_input_correlativo1" class="muestra_select">
									<input type="text" id="correlativo1" onblur="cambia_nombre('correlativo1'); cargar_json('1')" onkeyup="if (event.keyCode==13){cambia_nombre('correlativo1'); cargar_json('1'); return false;}" maxlength="5" >
								</div>
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	</table>

	<table id="rotulo_carpeta2" class="table_general" border="1" style="display: none; margin-left: 20px;"  >
		<tr>
			<td id="logo_entidad2" class="center" rowspan="3">
				<img src="imagenes/logos_entidades/logo_litigando_punto_com.png" style='width: 90;height: 40;' >
			</td>
			<tr class="titulos center">
				<td rowspan="2"  style="width: 140px">Formato <br> Identificación de Carpetas</td>
				<td style="width: 100px">Versión 1</td>
			</tr>
			<tr class="titulos center">
				<td class="celda1">Fecha de Edición<br>15-09-2018</td>
			</tr>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table id="fondo_sec_subsec" class="fondo_sec_subsec" border="1">
						<tr>
							<td class="half titulos" >
								Codigo de la Entidad (Remitente)	
							</td>
							<td class="half titulos" >
								Fondo
							</td>
						</tr>
						<tr>
							<td id ="codigo_entidad" class="half subtitulos">
								<div id="muestra_codigo_entidad2" onclick="muestra('codigo_entidad1')">(L01) Litigando Punto Com</div>
								<div id="muestra_input_codigo_entidad2" class="muestra_select"></div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_fondo2" class="subtitulos_vacio" onclick="muestra('nombre_fondo2')"></div>
								<div id="muestra_input_nombre_fondo2" class="muestra_select">
									<input type="text" id="nombre_fondo2" onblur="cambia_nombre('nombre_fondo2'); cargar_json('2')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_fondo2'); cargar_json('2'); return false;}" maxlength="15">
								</div>
							</td>
						</tr>
						<tr>
							<td class="half titulos">
								Dependencia - Seccion
							</td>
							<td class="half titulos">
								Oficina Productora - Subseccion
							</td>
						</tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_nombre_seccion2" class="subtitulos_vacio" onclick="muestra('nombre_seccion2')"></div>
								<div id="muestra_input_nombre_seccion2" class="muestra_select">
									<input type="text" id="nombre_seccion2" onblur="cambia_nombre('nombre_seccion2'); cargar_json('2')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_seccion2'); cargar_json('2'); return false;}" maxlength="15">
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_sub_seccion2" class="subtitulos_vacio" onclick="muestra('nombre_sub_seccion2')"></div>
								<div id="muestra_input_nombre_sub_seccion2" class="muestra_select">
									<input type="text" id="nombre_sub_seccion2" onblur="cambia_nombre('nombre_sub_seccion2'); cargar_json('2')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_sub_seccion2'); cargar_json('2'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>

						<tr>
							<td class="half titulos" >
								Serie Documental
							</td>
							<td class="half titulos">
								Subserie Documental
							</td>
						<tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_serie_documental2" class="subtitulos_vacio" onclick="muestra('serie_documental2')"></div>
								<div id="muestra_input_serie_documental2" class="muestra_select">
									<input type="text" id="serie_documental2" onblur="cambia_nombre('serie_documental2'); cargar_json('2')" onkeyup="if (event.keyCode==13){cambia_nombre('serie_documental2'); cargar_json('2'); return false;}" maxlength="15" >
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_subserie_documental2" class="subtitulos_vacio" onclick="muestra('subserie_documental2')"></div>
								<div id="muestra_input_subserie_documental2" class="muestra_select">
									<input type="text" id="subserie_documental2"  onblur="cambia_nombre('subserie_documental2'); cargar_json('2')" onkeyup="if (event.keyCode==13){cambia_nombre('subserie_documental2'); cargar_json('2'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Numero Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_numero_expediente2" class="subtitulos_vacio" onclick="muestra('numero_expediente2')"></div>
								<div id="muestra_input_numero_expediente2" class="muestra_select">
									<input type="text" id="numero_expediente2" onkeyup="if (event.keyCode==13){cambia_numero_expediente('2'); return false;}" maxlength="23" >
								</div>
								<div id="error_numero_expediente2" class="errores">El numero de expediente no existe en la base de datos</div>
								<div id="sugerencias_numero_expediente2"></div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Nombre Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_expediente2" class="subtitulos_vacio" onclick="muestra('nombre_expediente2')"></div>
								<div id="muestra_input_nombre_expediente2" class="muestra_select">
									<input type="text" id="nombre_expediente2" onkeyup="if (event.keyCode==13){cambia_nombre_expediente('2'); return false;}" maxlength="30" >
								</div>
								<div id="error_nombre_expediente2" class="errores">El nombre del expediente no existe en la base de datos</div>
								<div id="sugerencias_nombre_expediente2"></div>
							</td>
						</tr>
				</table>
				</center>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table border="0" width="100%" class="center">
						<tr style="font-size: 18px;">	
							<td colspan="2" class="titulos" >
								Fechas Extremas
							</td>
						</tr>
						<tr>
							<td class="half">
								Desde									
								<div id="fecha_desde2" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_inicial_2">
							</td>
							<td class="half">
								Hasta									
								<div id="fecha_hasta2" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_final_2">
							</td>
						</tr>
					</table>
					<table border="1" width="95%">
						<tr class="titulos center">
							<td style="width: 50%;">
								Folios
							</td>	
							<td>
								Numero Correlativo
							</td>
						</tr>
						<tr>	
							<td class='subtitulos'>
								<div id="muestra_folios2" class="subtitulos_vacio" onclick="muestra('folios2')"></div>
								<div id="muestra_input_folios2" class="muestra_select">
									<input type="text" id="folios2"  onblur="cambia_nombre('folios2'); cargar_json('2')" onkeyup="if (event.keyCode==13){cambia_nombre('folios2'); cargar_json('2'); return false;}" maxlength="5" >
								</div>
							</td>

							<td class="subtitulos">
								<div id="muestra_correlativo2" class="subtitulos_vacio" onclick="muestra('correlativo2')"></div>
								<div id="muestra_input_correlativo2" class="muestra_select">
									<input type="text" id="correlativo2" onblur="cambia_nombre('correlativo2'); cargar_json('2')" onkeyup="if (event.keyCode==13){cambia_nombre('correlativo2'); cargar_json('2'); return false;}" maxlength="5" >
								</div>
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	</table>
	<table id="rotulo_carpeta3" class="table_general" border="1" style="display: none; margin-left: 20px;"  >
		<tr>
			<td id="logo_entidad3" class="center" rowspan="3">
				<img src="imagenes/logos_entidades/logo_litigando_punto_com.png" style='width: 90;height: 40;' >
			</td>
			<tr class="titulos center">
				<td rowspan="2"  style="width: 140px">Formato <br> Identificación de Carpetas</td>
				<td style="width: 100px">Versión 1</td>
			</tr>
			<tr class="titulos center">
				<td class="celda1">Fecha de Edición<br>15-09-2018</td>
			</tr>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table id="fondo_sec_subsec" class="fondo_sec_subsec" border="1">
						<tr>
							<td class="half titulos" >
								Codigo de la Entidad (Remitente)	
							</td>
							<td class="half titulos" >
								Fondo
							</td>
						</tr>
						<tr>
							<td id ="codigo_entidad" class="half subtitulos">
								<div id="muestra_codigo_entidad3" onclick="muestra('codigo_entidad1')">(L01) Litigando Punto Com</div>
								<div id="muestra_input_codigo_entidad3" class="muestra_select"></div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_fondo3" class="subtitulos_vacio" onclick="muestra('nombre_fondo3')"></div>
								<div id="muestra_input_nombre_fondo3" class="muestra_select">
									<input type="text" id="nombre_fondo3" onblur="cambia_nombre('nombre_fondo3'); cargar_json('3')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_fondo3'); cargar_json('3'); return false;}" maxlength="15">
								</div>
							</td>
						</tr>
						<tr>
							<td class="half titulos">
								Dependencia - Seccion
							</td>
							<td class="half titulos">
								Oficina Productora - Subseccion
							</td>
						</tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_nombre_seccion3" class="subtitulos_vacio" onclick="muestra('nombre_seccion3')"></div>
								<div id="muestra_input_nombre_seccion3" class="muestra_select">
									<input type="text" id="nombre_seccion3" onblur="cambia_nombre('nombre_seccion3'); cargar_json('3')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_seccion3'); cargar_json('3'); return false;}" maxlength="15">
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_sub_seccion3" class="subtitulos_vacio" onclick="muestra('nombre_sub_seccion3')"></div>
								<div id="muestra_input_nombre_sub_seccion3" class="muestra_select">
									<input type="text" id="nombre_sub_seccion3" onblur="cambia_nombre('nombre_sub_seccion3'); cargar_json('3')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_sub_seccion3'); cargar_json('3'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>

						<tr>
							<td class="half titulos" >
								Serie Documental
							</td>
							<td class="half titulos">
								Subserie Documental
							</td>
						<tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_serie_documental3" class="subtitulos_vacio" onclick="muestra('serie_documental3')"></div>
								<div id="muestra_input_serie_documental3" class="muestra_select">
									<input type="text" id="serie_documental3" onblur="cambia_nombre('serie_documental3'); cargar_json('3')" onkeyup="if (event.keyCode==13){cambia_nombre('serie_documental3'); cargar_json('3'); return false;}" maxlength="15" >
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_subserie_documental3" class="subtitulos_vacio" onclick="muestra('subserie_documental3')"></div>
								<div id="muestra_input_subserie_documental3" class="muestra_select">
									<input type="text" id="subserie_documental3" onblur="cambia_nombre('subserie_documental3'); cargar_json('3')" onkeyup="if (event.keyCode==13){cambia_nombre('subserie_documental3'); cargar_json('3'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Numero Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_numero_expediente3" class="subtitulos_vacio" onclick="muestra('numero_expediente3')"></div>
								<div id="muestra_input_numero_expediente3" class="muestra_select">
									<input type="text" id="numero_expediente3" onkeyup="if (event.keyCode==13){cambia_numero_expediente('3'); return false;}" maxlength="23" >
								</div>
								<div id="error_numero_expediente3" class="errores">El numero de expediente no existe en la base de datos</div>
								<div id="sugerencias_numero_expediente3"></div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Nombre Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_expediente3" class="subtitulos_vacio" onclick="muestra('nombre_expediente3')"></div>
								<div id="muestra_input_nombre_expediente3" class="muestra_select">
									<input type="text" id="nombre_expediente3" onkeyup="if (event.keyCode==13){cambia_nombre_expediente('3'); return false;}" maxlength="30" >
								</div>
								<div id="error_nombre_expediente3" class="errores">El nombre del expediente no existe en la base de datos</div>
								<div id="sugerencias_nombre_expediente3"></div>
							</td>
						</tr>
				</table>
				</center>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table border="0" width="100%" class="center">
						<tr style="font-size: 18px;">	
							<td colspan="2" class="titulos" >
								Fechas Extremas
							</td>
						</tr>
						<tr>
							<td class="half">
								Desde									
								<div id="fecha_desde3" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_inicial_3">
							</td>
							<td class="half">
								Hasta									
								<div id="fecha_hasta3" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_final_3">
							</td>
						</tr>
					</table>
					<table border="1" width="95%">
						<tr class="titulos center">
							<td style="width: 50%;">
								Folios
							</td>	
							<td>
								Numero Correlativo
							</td>
						</tr>
						<tr>	
							<td class='subtitulos'>
								<div id="muestra_folios3" class="subtitulos_vacio" onclick="muestra('folios3')"></div>
								<div id="muestra_input_folios3" class="muestra_select">
									<input type="text" id="folios3" onblur="cambia_nombre('folios3'); cargar_json('3')" onkeyup="if (event.keyCode==13){cambia_nombre('folios3'); cargar_json('3'); return false;}" maxlength="5" >
								</div>
							</td>

							<td class="subtitulos">
								<div id="muestra_correlativo3" class="subtitulos_vacio" onclick="muestra('correlativo3')"></div>
								<div id="muestra_input_correlativo3" class="muestra_select">
									<input type="text" id="correlativo3" onblur="cambia_nombre('correlativo3'); cargar_json('3')" onkeyup="if (event.keyCode==13){cambia_nombre('correlativo3'); cargar_json('3'); return false;}" maxlength="5" >
								</div>
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	</table>
	<table id="rotulo_carpeta4" class="table_general" border="1" style="display: none; margin-left: 20px;"  >
		<tr>
			<td id="logo_entidad4" class="center" rowspan="3">
				<img src="imagenes/logos_entidades/logo_litigando_punto_com.png" style='width: 90;height: 40;' >
			</td>
			<tr class="titulos center">
				<td rowspan="2"  style="width: 140px">Formato <br> Identificación de Carpetas</td>
				<td style="width: 100px">Versión 1</td>
			</tr>
			<tr class="titulos center">
				<td class="celda1">Fecha de Edición<br>15-09-2018</td>
			</tr>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table id="fondo_sec_subsec" class="fondo_sec_subsec" border="1">
						<tr>
							<td class="half titulos" >
								Codigo de la Entidad (Remitente)	
							</td>
							<td class="half titulos" >
								Fondo
							</td>
						</tr>
						<tr>
							<td id ="codigo_entidad" class="half subtitulos">
								<div id="muestra_codigo_entidad4" onclick="muestra('codigo_entidad1')">(L01) Litigando Punto Com</div>
								<div id="muestra_input_codigo_entidad4" class="muestra_select"></div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_fondo4" class="subtitulos_vacio" onclick="muestra('nombre_fondo4')"></div>
								<div id="muestra_input_nombre_fondo4" class="muestra_select">
									<input type="text" id="nombre_fondo4" onblur="cambia_nombre('nombre_fondo4'); cargar_json('4')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_fondo4'); cargar_json('4'); return false;}" maxlength="15">
								</div>
							</td>
						</tr>
						<tr>
							<td class="half titulos">
								Dependencia - Seccion
							</td>
							<td class="half titulos">
								Oficina Productora - Subseccion
							</td>
						</tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_nombre_seccion4" class="subtitulos_vacio" onclick="muestra('nombre_seccion4')"></div>
								<div id="muestra_input_nombre_seccion4" class="muestra_select">
									<input type="text" id="nombre_seccion4" onblur="cambia_nombre('nombre_seccion4'); cargar_json('4')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_seccion4'); cargar_json('4'); return false;}" maxlength="15">
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_sub_seccion4" class="subtitulos_vacio" onclick="muestra('nombre_sub_seccion4')"></div>
								<div id="muestra_input_nombre_sub_seccion4" class="muestra_select">
									<input type="text" id="nombre_sub_seccion4" onblur="cambia_nombre('nombre_sub_seccion4'); cargar_json('4')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_sub_seccion4'); cargar_json('4'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>

						<tr>
							<td class="half titulos" >
								Serie Documental
							</td>
							<td class="half titulos">
								Subserie Documental
							</td>
						<tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_serie_documental4" class="subtitulos_vacio" onclick="muestra('serie_documental4')"></div>
								<div id="muestra_input_serie_documental4" class="muestra_select">
									<input type="text" id="serie_documental4" onblur="cambia_nombre('serie_documental4'); cargar_json('4')" onkeyup="if (event.keyCode==13){cambia_nombre('serie_documental4'); cargar_json('4'); return false;}" maxlength="15" >
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_subserie_documental4" class="subtitulos_vacio" onclick="muestra('subserie_documental4')"></div>
								<div id="muestra_input_subserie_documental4" class="muestra_select">
									<input type="text" id="subserie_documental4" onblur="cambia_nombre('subserie_documental4'); cargar_json('4')" onkeyup="if (event.keyCode==13){cambia_nombre('subserie_documental4'); cargar_json('4'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Numero Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_numero_expediente4" class="subtitulos_vacio" onclick="muestra('numero_expediente4')"></div>
								<div id="muestra_input_numero_expediente4" class="muestra_select">
									<input type="text" id="numero_expediente4" onkeyup="if (event.keyCode==13){cambia_numero_expediente('4'); return false;}" maxlength="23" >
								</div>
								<div id="error_numero_expediente4" class="errores">El numero de expediente no existe en la base de datos</div>
								<div id="sugerencias_numero_expediente4"></div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Nombre Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_expediente4" class="subtitulos_vacio" onclick="muestra('nombre_expediente4')"></div>
								<div id="muestra_input_nombre_expediente4" class="muestra_select">
									<input type="text" id="nombre_expediente4" onkeyup="if (event.keyCode==13){cambia_nombre_expediente('4'); return false;}" maxlength="30" >
								</div>
								<div id="error_nombre_expediente4" class="errores">El nombre del expediente no existe en la base de datos</div>
								<div id="sugerencias_nombre_expediente4"></div>
							</td>
						</tr>
				</table>
				</center>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table border="0" width="100%" class="center">
						<tr style="font-size: 18px;">	
							<td colspan="2" class="titulos" >
								Fechas Extremas
							</td>
						</tr>
						<tr>
							<td class="half">
								Desde									
								<div id="fecha_desde4" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_inicial_4">
							</td>
							<td class="half">
								Hasta									
								<div id="fecha_hasta4" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_final_4">
							</td>
						</tr>
					</table>
					<table border="1" width="95%">
						<tr class="titulos center">
							<td style="width: 50%;">
								Folios
							</td>	
							<td>
								Numero Correlativo
							</td>
						</tr>
						<tr>	
							<td class='subtitulos'>
								<div id="muestra_folios4" class="subtitulos_vacio" onclick="muestra('folios4')"></div>
								<div id="muestra_input_folios4" class="muestra_select">
									<input type="text" id="folios4" onblur="cambia_nombre('folios4'); cargar_json('4')" onkeyup="if (event.keyCode==13){cambia_nombre('folios4'); cargar_json('4'); return false;}" maxlength="5" >
								</div>
							</td>
							<td class="subtitulos">
								<div id="muestra_correlativo4" class="subtitulos_vacio" onclick="muestra('correlativo4')"></div>
								<div id="muestra_input_correlativo4" class="muestra_select">
									<input type="text" id="correlativo4" onblur="cambia_nombre('correlativo4'); cargar_json('4')" onkeyup="if (event.keyCode==13){cambia_nombre('correlativo4'); cargar_json('4'); return false;}" maxlength="5" >
								</div>
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	</table>
	<table id="rotulo_carpeta5" class="table_general" border="1" style="display: none; margin-left: 20px;"  >
		<tr>
			<td id="logo_entidad5" class="center" rowspan="3">
				<img src="imagenes/logos_entidades/logo_litigando_punto_com.png" style='width: 90;height: 40;' >
			</td>
			<tr class="titulos center">
				<td rowspan="2"  style="width: 140px">Formato <br> Identificación de Carpetas</td>
				<td style="width: 100px">Versión 1</td>
			</tr>
			<tr class="titulos center">
				<td class="celda1">Fecha de Edición<br>15-09-2018</td>
			</tr>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table id="fondo_sec_subsec" class="fondo_sec_subsec" border="1">
						<tr>
							<td class="half titulos" >
								Codigo de la Entidad (Remitente)	
							</td>
							<td class="half titulos" >
								Fondo
							</td>
						</tr>
						<tr>
							<td id ="codigo_entidad" class="half subtitulos">
								<div id="muestra_codigo_entidad5" onclick="muestra('codigo_entidad1')">(L01) Litigando Punto Com</div>
								<div id="muestra_input_codigo_entidad5" class="muestra_select"></div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_fondo5" class="subtitulos_vacio" onclick="muestra('nombre_fondo5')"></div>
								<div id="muestra_input_nombre_fondo5" class="muestra_select">
									<input type="text" id="nombre_fondo5" onblur="cambia_nombre('nombre_fondo5'); cargar_json('5')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_fondo5'); cargar_json('5'); return false;}" maxlength="15">
								</div>
							</td>
						</tr>
						<tr>
							<td class="half titulos">
								Dependencia - Seccion
							</td>
							<td class="half titulos">
								Oficina Productora - Subseccion
							</td>
						</tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_nombre_seccion5" class="subtitulos_vacio" onclick="muestra('nombre_seccion5')"></div>
								<div id="muestra_input_nombre_seccion5" class="muestra_select">
									<input type="text" id="nombre_seccion5" onblur="cambia_nombre('nombre_seccion5'); cargar_json('5')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_seccion5'); cargar_json('5'); return false;}" maxlength="15">
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_sub_seccion5" class="subtitulos_vacio" onclick="muestra('nombre_sub_seccion5')"></div>
								<div id="muestra_input_nombre_sub_seccion5" class="muestra_select">
									<input type="text" id="nombre_sub_seccion5" onblur="cambia_nombre('nombre_sub_seccion5'); cargar_json('5')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_sub_seccion5'); cargar_json('5'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>

						<tr>
							<td class="half titulos" >
								Serie Documental
							</td>
							<td class="half titulos">
								Subserie Documental
							</td>
						<tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_serie_documental5" class="subtitulos_vacio" onclick="muestra('serie_documental5')"></div>
								<div id="muestra_input_serie_documental5" class="muestra_select">
									<input type="text" id="serie_documental5" onblur="cambia_nombre('serie_documental5'); cargar_json('5')" onkeyup="if (event.keyCode==13){cambia_nombre('serie_documental5'); cargar_json('5'); return false;}" maxlength="15" >
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_subserie_documental5" class="subtitulos_vacio" onclick="muestra('subserie_documental5')"></div>
								<div id="muestra_input_subserie_documental5" class="muestra_select">
									<input type="text" id="subserie_documental5" onblur="cambia_nombre('subserie_documental5'); cargar_json('5')" onkeyup="if (event.keyCode==13){cambia_nombre('subserie_documental5'); cargar_json('5'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Numero Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_numero_expediente5" class="subtitulos_vacio" onclick="muestra('numero_expediente5')"></div>
								<div id="muestra_input_numero_expediente5" class="muestra_select">
									<input type="text" id="numero_expediente5" onkeyup="if (event.keyCode==13){cambia_numero_expediente('5'); return false;}" maxlength="23" >
								</div>
								<div id="error_numero_expediente5" class="errores">El numero de expediente no existe en la base de datos</div>
								<div id="sugerencias_numero_expediente5"></div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Nombre Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_expediente5" class="subtitulos_vacio" onclick="muestra('nombre_expediente5')"></div>
								<div id="muestra_input_nombre_expediente5" class="muestra_select">
									<input type="text" id="nombre_expediente5" onkeyup="if (event.keyCode==13){cambia_nombre_expediente('5'); return false;}" maxlength="30" >
								</div>
								<div id="error_nombre_expediente5" class="errores">El nombre del expediente no existe en la base de datos</div>
								<div id="sugerencias_nombre_expediente5"></div>
							</td>
						</tr>
				</table>
				</center>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table border="0" width="100%" class="center">
						<tr style="font-size: 18px;">	
							<td colspan="2" class="titulos" >
								Fechas Extremas
							</td>
						</tr>
						<tr>
							<td class="half">
								Desde									
								<div id="fecha_desde5" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_inicial_5">
							</td>
							<td class="half">
								Hasta									
								<div id="fecha_hasta5" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_final_5">
							</td>
						</tr>
					</table>
					<table border="1" width="95%">
						<tr class="titulos center">
							<td style="width: 50%;">
								Folios
							</td>	
							<td>
								Numero Correlativo
							</td>
						</tr>
						<tr>	
							<td class='subtitulos'>
								<div id="muestra_folios5" class="subtitulos_vacio" onclick="muestra('folios5')"></div>
								<div id="muestra_input_folios5" class="muestra_select">
									<input type="text" id="folios5" onblur="cambia_nombre('folios5'); cargar_json('5')" onkeyup="if (event.keyCode==13){cambia_nombre('folios5'); cargar_json('5'); return false;}" maxlength="5" >
								</div>
							</td>

							<td class="subtitulos">
								<div id="muestra_correlativo5" class="subtitulos_vacio" onclick="muestra('correlativo5')"></div>
								<div id="muestra_input_correlativo5" class="muestra_select">
									<input type="text" id="correlativo5" onblur="cambia_nombre('correlativo5'); cargar_json('5')" onkeyup="if (event.keyCode==13){cambia_nombre('correlativo5'); cargar_json('5'); return false;}" maxlength="5" >
								</div>
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	</table>
	<table id="rotulo_carpeta6" class="table_general" border="1" style="display: none; margin-left: 20px;"  >
		<tr>
			<td id="logo_entidad6" class="center" rowspan="3">
				<img src="imagenes/logos_entidades/logo_litigando_punto_com.png" style='width: 90;height: 40;' >
			</td>
			<tr class="titulos center">
				<td rowspan="2"  style="width: 140px">Formato <br> Identificación de Carpetas</td>
				<td style="width: 100px">Versión 1</td>
			</tr>
			<tr class="titulos center">
				<td class="celda1">Fecha de Edición<br>15-09-2018</td>
			</tr>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table id="fondo_sec_subsec" class="fondo_sec_subsec" border="1">
						<tr>
							<td class="half titulos" >
								Codigo de la Entidad (Remitente)	
							</td>
							<td class="half titulos" >
								Fondo
							</td>
						</tr>
						<tr>
							<td id ="codigo_entidad" class="half subtitulos">
								<div id="muestra_codigo_entidad6" onclick="muestra('codigo_entidad1')">(L01) Litigando Punto Com</div>
								<div id="muestra_input_codigo_entidad6" class="muestra_select"></div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_fondo6" class="subtitulos_vacio" onclick="muestra('nombre_fondo6')"></div>
								<div id="muestra_input_nombre_fondo6" class="muestra_select">
									<input type="text" id="nombre_fondo6" onblur="cambia_nombre('nombre_fondo6'); cargar_json('6')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_fondo6'); cargar_json('6'); return false;}" maxlength="15">
								</div>
							</td>
						</tr>
						<tr>
							<td class="half titulos">
								Dependencia - Seccion
							</td>
							<td class="half titulos">
								Oficina Productora - Subseccion
							</td>
						</tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_nombre_seccion6" class="subtitulos_vacio" onclick="muestra('nombre_seccion6')"></div>
								<div id="muestra_input_nombre_seccion6" class="muestra_select">
									<input type="text" id="nombre_seccion6" onblur="cambia_nombre('nombre_seccion6'); cargar_json('6')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_seccion6'); cargar_json('6'); return false;}" maxlength="15">
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_nombre_sub_seccion6" class="subtitulos_vacio" onclick="muestra('nombre_sub_seccion6')"></div>
								<div id="muestra_input_nombre_sub_seccion6" class="muestra_select">
									<input type="text" id="nombre_sub_seccion6" onblur="cambia_nombre('nombre_sub_seccion6'); cargar_json('6')" onkeyup="if (event.keyCode==13){cambia_nombre('nombre_sub_seccion6'); cargar_json('6'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>

						<tr>
							<td class="half titulos" >
								Serie Documental
							</td>
							<td class="half titulos">
								Subserie Documental
							</td>
						<tr>
						<tr>
							<td class='half subtitulos'>
								<div id="muestra_serie_documental6" class="subtitulos_vacio" onclick="muestra('serie_documental6')"></div>
								<div id="muestra_input_serie_documental6" class="muestra_select">
									<input type="text" id="serie_documental6" onblur="cambia_nombre('serie_documental6'); cargar_json('6')" onkeyup="if (event.keyCode==13){cambia_nombre('serie_documental6'); cargar_json('6'); return false;}" maxlength="15" >
								</div>
							</td>
							<td class='half subtitulos'>
								<div id="muestra_subserie_documental6" class="subtitulos_vacio" onclick="muestra('subserie_documental6')"></div>
								<div id="muestra_input_subserie_documental6" class="muestra_select">
									<input type="text" id="subserie_documental6" onblur="cambia_nombre('subserie_documental6'); cargar_json('6')" onkeyup="if (event.keyCode==13){cambia_nombre('subserie_documental6'); cargar_json('6'); return false;}" maxlength="15" >
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Numero Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_numero_expediente6" class="subtitulos_vacio" onclick="muestra('numero_expediente6')"></div>
								<div id="muestra_input_numero_expediente6" class="muestra_select">
									<input type="text" id="numero_expediente6" onkeyup="if (event.keyCode==13){cambia_numero_expediente('6'); return false;}" maxlength="23" >
								</div>
								<div id="error_numero_expediente6" class="errores">El numero de expediente no existe en la base de datos</div>
								<div id="sugerencias_numero_expediente6"></div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Nombre Carpeta - Expediente
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_expediente6" class="subtitulos_vacio" onclick="muestra('nombre_expediente6')"></div>
								<div id="muestra_input_nombre_expediente6" class="muestra_select">
									<input type="text" id="nombre_expediente6" onkeyup="if (event.keyCode==13){cambia_nombre_expediente('6'); return false;}" maxlength="30" >
								</div>
								<div id="error_nombre_expediente6" class="errores">El nombre del expediente no existe en la base de datos</div>
								<div id="sugerencias_nombre_expediente6"></div>
							</td>
						</tr>
				</table>
				</center>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table border="0" width="100%" class="center">
						<tr style="font-size: 18px;">	
							<td colspan="2" class="titulos" >
								Fechas Extremas
							</td>
						</tr>
						<tr>
							<td class="half">
								Desde									
								<div id="fecha_desde6" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_inicial_6">
							</td>
							<td class="half">
								Hasta									
								<div id="fecha_hasta6" style="float: right;margin-right: 23px;">
								</div>
								<input type="text" class="muestra_select" id="fecha_final_6">
							</td>
						</tr>
					</table>
					<table border="1" width="95%">
						<tr class="titulos center">
							<td style="width: 50%;">
								Folios
							</td>	
							<td>
								Numero Correlativo
							</td>
						</tr>
						<tr>	
							<td class='subtitulos'>
								<div id="muestra_folios6" class="subtitulos_vacio" onclick="muestra('folios6')"></div>
								<div id="muestra_input_folios6" class="muestra_select">
									<input type="text" id="folios6" onblur="cambia_nombre('folios6'); cargar_json('6')" onkeyup="if (event.keyCode==13){cambia_nombre('folios6'); cargar_json('6'); return false;}" maxlength="5" >
								</div>
							</td>
							<td class="subtitulos">
								<div id="muestra_correlativo6" class="subtitulos_vacio" onclick="muestra('correlativo6')"></div>
								<div id="muestra_input_correlativo6" class="muestra_select">
									<input type="text" id="correlativo6" onblur="cambia_nombre('correlativo6'); cargar_json('6')" onkeyup="if (event.keyCode==13){cambia_nombre('correlativo6'); cargar_json('6'); return false;}" maxlength="5" >
								</div>
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	</table>

	<center">
		<div id="boton_imprimir" class="muestra_select" >
			<input type="button" id="agregar_rotulo" class="botones2" value="Agregar otro Rotulo Carpeta" onclick="agregar_rotulo()">
			<input type="button" id="imprimir_carpeta" class="botones2" value="Imprimir Rotulo Carpeta" onclick="imprimir_rotulos_carpeta()">
			<form action="formatos/api_rotulos_carpeta.php" method="post" id="formulario_api" target="_blank">
				<textarea id="rotulos_carpetas" name="rotulos_carpetas" class="muestra_select" rows="10" style="width:100%;padding:5px;"></textarea> 
			</form>
			<textarea id="carpeta1" name="carpeta1" class="muestra_select" rows="4" style="width:100%;padding:5px;"></textarea> 
			<textarea id="carpeta2" name="carpeta2" class="muestra_select" rows="4" style="width:100%;padding:5px;"></textarea> 
			<textarea id="carpeta3" name="carpeta3" class="muestra_select" rows="4" style="width:100%;padding:5px;"></textarea> 
			<textarea id="carpeta4" name="carpeta4" class="muestra_select" rows="4" style="width:100%;padding:5px;"></textarea> 
			<textarea id="carpeta5" name="carpeta5" class="muestra_select" rows="4" style="width:100%;padding:5px;"></textarea> 
			<textarea id="carpeta6" name="carpeta6" class="muestra_select" rows="4" style="width:100%;padding:5px;"></textarea> 
			<div id="resultado" class="muestra_select"></div>
		</div>
	</center>
	</div>
</body>
</html>