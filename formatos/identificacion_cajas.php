<!DOCTYPE html>
<html>
<head>
	<title>Formato de identificación de cajas</title>
	<style type="text/css">
		.celda1{
			padding-left: 10px;
			padding-right: 10px;
		}
		.center{
			text-align: center;
		}
		.fondo_sec_subsec{
			width: 90%;
		}
		#lista_carpetas td{
			height: 	17px;
			padding: 	2px;
		}
		.muestra_select{
			display: none;
		}
		table{
			font-weight: bold;
		}
		.table_general{
			border: solid; 
			float: left; 
			width: 340px; 
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
			$("#agregar_rotulo").slideUp("fast");
			$("#rotulo_caja2").slideDown("slow");
			$('#boton_imprimir').slideUp('slow');
		}
		function buscar_caja(num){
			limpia_formulario(num);
			// console.log("num")

			var cantidad_carpetas1 	= $("#cantidad_carpetas1").val();
			var cantidad_carpetas2 	= $("#cantidad_carpetas2").val();
			var entidad 			= $("#select_entidad").val();
			var expedientes_caja1 	= $("#expedientes_caja1").val();
			var fecha_final_1 		= $("#fecha_final_1").val();
			var fecha_inicial_1 	= $("#fecha_inicial_1").val();
			var fondo1 				= $("#nombre_fondo1").val();
			var fondo2 				= $("#nombre_fondo2").val();
			var numero_caja 		= $("#numero_caja"+num).val();
			var numero_caja1 		= $("#numero_caja1").val();
			var numero_caja2 		= $("#numero_caja2").val();
			var seccion1 			= $("#nombre_seccion1").val();
			var seccion2 			= $("#nombre_seccion2").val();
			var subseccion1 		= $("#nombre_sub_seccion1").val();
			var subseccion2 		= $("#nombre_sub_seccion2").val();

			$.ajax({
				type: 'POST',
				url:  'formatos/buscador_formatos.php' ,
				data: {
					'numero_caja'		: numero_caja,
					'numero_caja1'		: numero_caja1,
					'numero_caja2'		: numero_caja2,
					'num' 				: num,
					'entidad'			: entidad,
					'fondo1'			: fondo1,
					'fondo2'			: fondo2,
					'seccion1'			: seccion1,
					'seccion2'			: seccion2,
					'subseccion1'		: subseccion1,
					'subseccion2'		: subseccion2,
					'cantidad_carp1'	: cantidad_carpetas1,
					'cantidad_carp2'	: cantidad_carpetas2,
					'fecha_inicial_1'	: fecha_inicial_1,
					'fecha_final_1'		: fecha_final_1,
					'expedientes_caja1'	: expedientes_caja1
				},
				success: function(resp){
					if(resp!=""){
						// console.log(resp);
						$("#resultado").html(resp);
						$("#imprimir_caja").focus();
					}
				}
			})	
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
			buscar_caja(1);
			buscar_caja(2);
			$("#muestra_codigo_entidad1").html(nombre_entidad);
			$("#muestra_codigo_entidad2").html(nombre_entidad);
			$("#logo_entidad1").html(imagen_entidad);
			$("#logo_entidad2").html(imagen_entidad);
			$("#muestra_codigo_entidad1").slideDown("fast"); 
			$("#muestra_codigo_entidad2").slideDown("fast");
			$("#muestra_input_codigo_entidad1").slideUp("fast");
			$("#muestra_input_codigo_entidad2").slideUp("fast");
		}
		function cambia_nombre(campo){
			var nombre_campo=$("#"+campo).val();
			$("#muestra_"+campo).html(nombre_campo);
			$("#muestra_"+campo).slideDown("fast");
			$("#muestra_input_"+campo).slideUp("fast");
			buscar_caja(1);
			buscar_caja(2);
		}
		function imprimir_rotulo(){
			if($("#rotulo_caja2").is(":visible")){
				buscar_caja(2);
				var caja1 = $("#caja1").val();
				var caja2 = $("#caja2").val();
				var json= '{"rotulo_caja":['+caja1+','+caja2+']}';
			}else{
				buscar_caja(1);
				var caja1 = $("#caja1").val();
				var json= '{"rotulo_caja":['+caja1+']}';	
			}

			$("#rotulos_cajas").val(json);
			console.log(json);
			$("#formulario_api").submit();
		}
		function limpia_formulario(num){
		/* Inicia ciclo para limpiar formulario */
			if(num==1){
				for (var i=0;i<10;i++){
					var j=i+1;
					$('#numero_carpeta_'+j).html(''); $('#nombre_carpeta_'+j).html(''); 
				}
			}else{
				for (var i=100;i<110;i++){
					var j=i+1;
					$('#numero_carpeta_'+j).html(''); $('#nombre_carpeta_'+j).html(''); 
				}
			}
		/* Cierra ciclo para limpiar formulario */
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
		
	<table border="1" class="table_general" style="margin-left: 20px;" >
		<tr>
			<td id="logo_entidad1" class="center" rowspan="3">
				<img src="imagenes/logos_entidades/logo_litigando_punto_com.png" style='width: 90px;height: 40px;' >
			</td>
			<tr class="titulos center">
				<td rowspan="2" style="width: 140px">Formato <br> Identificación de Cajas</td>
				<td style="width: 100px">Versión 1</td>
			</tr>
			<tr class="titulos center">
				<td class="celda1">Fecha de Edición<br>15-09-2018</td>
			</tr>
		</tr>
		<tr>
			<td colspan="3">
				<center>
				<table id="fondo_sec_subsec" class="fondo_sec_subsec" border="0">
						<tr>
							<td class="titulos" colspan="3">
								Codigo de la Entidad (Remitente)	
							</td>
						</tr>
						<tr>
							<td id ="codigo_entidad" colspan="3" class="subtitulos">
								<div id="muestra_codigo_entidad1" onclick="muestra('codigo_entidad1')">(L01) Litigando Punto Com</div>
								<div id="muestra_input_codigo_entidad1" class="muestra_select">
									<select id="select_entidad" class='select_opciones' onchange="cambia_entidad()" onblur="cambia_entidad()">
										<option value="litigando_punto_com">Litigando punto com</option>
										<option value="ministerio_agricultura">Ministerio de Agricultura</option>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Fondo
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_fondo1" class="subtitulos_vacio" onclick="muestra('nombre_fondo1')"></div>
								<div id="muestra_input_nombre_fondo1" class="muestra_select">
									<input type="text" id="nombre_fondo1" onblur="cambia_nombre('nombre_fondo1')" maxlength="18">
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Dependencia - Seccion
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_seccion1" class="subtitulos_vacio" onclick="muestra('nombre_seccion1')"></div>
								<div id="muestra_input_nombre_seccion1" class="muestra_select">
									<input type="text" id="nombre_seccion1" onblur="cambia_nombre('nombre_seccion1')" maxlength="18">
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Oficina Productora - Subseccion
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_sub_seccion1" class="subtitulos_vacio" onclick="muestra('nombre_sub_seccion1')"></div>
								<div id="muestra_input_nombre_sub_seccion1" class="muestra_select">
									<input type="text" id="nombre_sub_seccion1" onblur="cambia_nombre('nombre_sub_seccion1')" maxlength="18" >
								</div>
							</td>
						</tr>
				</table>
				</center>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table id="lista_carpetas" style="font-size: 10px;" border="1" width="100%">
						<tr>
							<td style="width: 120px;">Numero Carpeta - Expediente</td>
							<td>Nombre Carpeta - Expediente</td>
						</tr>
						<tr>
							<td id="numero_carpeta_1"></td>
							<td id="nombre_carpeta_1"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_2"></td>
							<td id="nombre_carpeta_2"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_3"></td>
							<td id="nombre_carpeta_3"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_4"></td>
							<td id="nombre_carpeta_4"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_5"></td>
							<td id="nombre_carpeta_5"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_6"></td>
							<td id="nombre_carpeta_6"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_7"></td>
							<td id="nombre_carpeta_7"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_8"></td>
							<td id="nombre_carpeta_8"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_9"></td>
							<td id="nombre_carpeta_9"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_10"></td>
							<td id="nombre_carpeta_10"></td>
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
							<td>
								<div id="fecha_desde1">
								Desde									
								</div>
								<input type="text" class="muestra_select" id="fecha_inicial_1">
							</td>
							<td>
								<div id="fecha_hasta1">
								Hasta									
								</div>
								<input type="text" class="muestra_select" id="fecha_final_1">
							</td>
						</tr>
					</table>
					<table border="1" width="95%">
						<tr class="titulos center">
							<td style="width: 120px;">
								Numero de Caja
							</td>
							<td style="font-size: 12px;">
								Cantidad de Carpetas en la Caja
							</td>
							<td>
								Numero Correlativo
							</td>
						</tr>
						<tr>	
							<td class='subtitulos'>
								<div id="muestra_numero_caja1" class="subtitulos_vacio" onclick="muestra('numero_caja1')"></div>
								<div id="muestra_input_numero_caja1" class="muestra_select">
									<input type="text" id="numero_caja1" onkeyup="if (event.keyCode==13){buscar_caja(1); return false;}">
								</div>
								<div id="error_numero_caja1" class="errores">El numero de caja no existe o no tiene expedientes ingresados</div>
							</td>

							<td class="subtitulos">
								<div id="muestra_cantidad_carpetas1" class="subtitulos_vacio">	
								</div>
								<input type="text" class="muestra_select" id="cantidad_carpetas1">
							</td>
							<td>
								
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	</table>

	<table id="rotulo_caja2" class="table_general" border="1" style="display: none; margin-left: 20px;"  >
		<tr>
			<td id="logo_entidad2" class="center" rowspan="3">
				<img src="imagenes/logos_entidades/logo_litigando_punto_com.png" style='width: 75;height: 35px;' >
			</td>
			<tr class="titulos center">
				<td rowspan="2" style="width: 140px">Formato <br> Identificación de Cajas</td>
				<td style="width: 100px">Versión 1</td>
			</tr>
			<tr class="titulos center">
				<td class="celda1">Fecha de Edición<br>15-09-2018</td>
			</tr>
		</tr>
		<tr>
			<td colspan="3">
				<center>
				<table id="fondo_sec_subsec2" class="fondo_sec_subsec" border="0">
						<tr>
							<td class="titulos" colspan="3">
								Codigo de la Entidad (Remitente)	
							</td>
						</tr>
						<tr>
							<td id ="codigo_entidad2" colspan="3" class="subtitulos">
								<div id="muestra_codigo_entidad2" onclick="muestra('codigo_entidad1')">(L01) Litigando Punto Com</div>
								<div id="muestra_input_codigo_entidad2" class="muestra_select">	</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Fondo
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_fondo2" class="subtitulos_vacio" onclick="muestra('nombre_fondo2')"></div>
								<div id="muestra_input_nombre_fondo2" class="muestra_select">
									<input type="text" id="nombre_fondo2" onblur="cambia_nombre('nombre_fondo2')" maxlength="18">
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Dependencia - Seccion
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_seccion2" class="subtitulos_vacio" onclick="muestra('nombre_seccion2')"></div>
								<div id="muestra_input_nombre_seccion2" class="muestra_select">
									<input type="text" id="nombre_seccion2" onblur="cambia_nombre('nombre_seccion2')" maxlength="18">
								</div>
							</td>
						</tr>
						<tr>
							<td class="titulos" colspan="3">
								Oficina Productora - Subseccion
							</td>
						<tr>
						<tr>
							<td colspan="3" class='subtitulos'>
								<div id="muestra_nombre_sub_seccion2" class="subtitulos_vacio" onclick="muestra('nombre_sub_seccion2')"></div>
								<div id="muestra_input_nombre_sub_seccion2" class="muestra_select">
									<input type="text" id="nombre_sub_seccion2" onblur="cambia_nombre('nombre_sub_seccion2')" maxlength="18" >
								</div>
							</td>
						</tr>
				</table>
				</center>
			</td>
		
		</tr>
		<tr>
			<td colspan="3">
				<center>
					<table id="lista_carpetas" style="font-size: 10px;" border="1" width="100%">
						<tr>
							<td style="width: 120px;">Numero Carpeta - Expediente</td>
							<td>Nombre Carpeta - Expediente</td>
						</tr>
						<tr>
							<td id="numero_carpeta_101"></td>
							<td id="nombre_carpeta_101"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_102"></td>
							<td id="nombre_carpeta_102"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_103"></td>
							<td id="nombre_carpeta_103"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_104"></td>
							<td id="nombre_carpeta_104"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_105"></td>
							<td id="nombre_carpeta_105"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_106"></td>
							<td id="nombre_carpeta_106"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_107"></td>
							<td id="nombre_carpeta_107"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_108"></td>
							<td id="nombre_carpeta_108"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_109"></td>
							<td id="nombre_carpeta_109"></td>
						</tr>
						<tr>
							<td id="numero_carpeta_110"></td>
							<td id="nombre_carpeta_110"></td>
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
							<td>
								<div id="fecha_desde2">
								Desde									
								</div>
							</td>
							<td>
								<div id="fecha_hasta2">
								Hasta									
								</div>
							</td>
						</tr>
					</table>
					<table border="1" width="95%">
						<tr class="titulos center">
							<td style="width: 120px;">
								Numero de Caja
							</td>
							<td style="font-size: 12px;">
								Cantidad de Carpetas en la Caja
							</td>
							<td>
								Numero Correlativo
							</td>
						</tr>
						<tr>	
							<td class='subtitulos'>
								<div id="muestra_numero_caja2" class="subtitulos_vacio" onclick="muestra('numero_caja2')"></div>
								<div id="muestra_input_numero_caja2" class="muestra_select">
									<input type="text" id="numero_caja2" onkeyup="if (event.keyCode==13){buscar_caja(2); return false;}">
								</div>
								<div id="error_numero_caja2" class="errores">El numero de caja no existe o no tiene expedientes ingresados</div>
							</td>

							<td class="subtitulos">
								<div id="muestra_cantidad_carpetas2" class="subtitulos_vacio">
								</div>
								<input type="text" class="muestra_select" id="cantidad_carpetas2">
							</td>
							<td>
								
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
		
	</table><br>

	<center style="margin-top: 50%;">
		<div id="boton_imprimir" class="muestra_select" >
			<input type="button" id="agregar_rotulo" class="botones2" value="Agregar otro Rotulo Caja" onclick="agregar_rotulo()">
			<input type="button" id="imprimir_caja" class="botones2" value="Imprimir Rotulo Caja" onclick="imprimir_rotulo()">
			<form action="formatos/api_rotulos_caja.php" method="post" id="formulario_api" target="_blank">
				<textarea id="rotulos_cajas" name="rotulos_cajas" class="muestra_select" rows="10" style="width:100%;padding:5px;"></textarea> 
			</form>
			<textarea id="caja1" name="caja1" class="muestra_select" rows="4" style="width:100%;padding:5px;"></textarea> 
			<textarea id="caja2" name="caja2" class="muestra_select" rows="4" style="width:100%;padding:5px;"></textarea> 
			<div id="resultado" class="muestra_select"></div>
		</div>
	</center>
	</div>
</body>
</html>