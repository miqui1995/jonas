<?php 
	require_once("../login/validar_inactividad.php");
	require_once('../login/conexion2.php') // Para la consulta de las dependencias
?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>Buscador de Dependencias</title>
	<script type="text/javascript" src="include/js/funciones_dependencias.js"></script>
	<link rel="stylesheet" href="include/css/estilos_dependencias.css">
</head>
<body>
<style type="text/css">
	.clase_organigrama{
		background 		: #005b93;
		border-radius 	: 10px;
		box-shadow 		: rgba(0, 0, 0, 0.5) 3px 3px;
		color 			: #FFFFFF;
		cursor 			: pointer;
		font-family 	: arial, helvetica, serif;
		font-size 		: 16px;
		font-weight 	: bold;
		text-align 		: center;
		vertical-align 	: middle;
	}
</style>	
<!--Desde aqui el div que contiene el formulario para agregar dependencia-->
	<div class="container">
		<div id="ventana">
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrarVentanaCrearDependencia();'>Cerrar X</a></div>
				<h1>Formulario Agregar Nueva Dependencia</h1>
				<hr>
				<form id ="formulario_agregar_dependencia" name ="formulario_agregar_dependencia" autocomplete="off">
					<table border ="0">
						<tr>
							<td class="descripcion">
								Código de cambio Organico-Funcional
							</td>
							<td class="detalle">
								<div class="contenedor_select">
									<select id="id_cambio_organico_funcional">
										<!-- Listado viene de login/validar_inactividad.php -->
										<?php echo $listado_cambios_organico_funcionales ?>
									</select>
									<i><div class="flecha_abajo"></div></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Codigo de la Dependencia (Letra o Numero) :</td>
							<td class="detalle">
							<input type="hidden" name ="tipo_formulario" id="tipo_formulario" value="crear_dependencia"><!-- Tipo de formulario para query_dependencias.php -->
								<input type="search" placeholder="Digite Codigo Dependencia Alfanumerico" name="codigo_dependencia" id="codigo_dependencia" maxlength="5" >
								<div id="sugerencia_codigo_dependencia"></div>
								
								<div id="codigo_dependencia_ya_existe" class="errores2">El codigo de la dependencia ya existe, no es posible crear una nueva dependencia con éste código</div>								
								<div id="codigo_dependencia_max" class="errores">El codigo de la dependencia no puede ser mayor a 5 caracteres. (Actualmente <b><u id='codigo_dependencia_contadormax'></u></b> caracteres)</div>
								<div id="codigo_dependencia_min" class="errores">El codigo de la dependencia debe tener más caracteres (numeros o letras)</div>
								<div id="codigo_dependencia_null" class="errores">El codigo de la dependencia es obligatoria</div>
								<div id="codigo_dependencia_caracteres" class="errores">El codigo de la dependencia debe tener <span id="cantidad_caracteres_depe"></span> caracteres</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Nombre de la Dependencia :</td>
							<td class="detalle">
								<input type="search" placeholder="Digite Nombre de la dependencia" name="nombre_dependencia" id="nombre_dependencia">

								<div id="sugerencia_nombre_dependencia" class="sugerencia"></div>
								<div id="nombre_dependencia_ya_existe" class="errores2">El nombre de la dependencia ya existe, no es posible crear una nueva dependencia con éste nombre</div>								
								<!-- <div id="error_nombre_dependencia" class="errores">El nombre de la dependencia es obligatoria</div> -->
								
								<div id="nombre_dependencia_max" class="errores">El nombre de la dependencia no puede ser mayor a 100 caracteres. (Actualmente <b><u id='nombre_dependencia_contadormax'></u></b> caracteres)</div>
								<div id="nombre_dependencia_min" class="errores">El nombre de la dependencia no puede ser menor a 6 caracteres (numeros o letras)</div>
								<div id="nombre_dependencia_null" class="errores">El nombre de la dependencia es obligatoria</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Dependencia Padre :</td>
							<td class="detalle">
								<input type="search" placeholder="Digite Dependencia Padre (Si tiene)" name="dependencia_padre" id="dependencia_padre" >							
								<div id="sugerencia_dependencia_padre" class="sugerencia"></div>
								<div id="error_nombre_dependencia_padre" class="errores2">El nombre de la dependencia padre no existe en la base de datos. Intente otro nombre</div>
								<div id="error_nombre_dependencia_padre2" class="errores2">Por favor seleccione una dependencia padre válida</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<center id='boton_crear_dependencia'>
									<input type="button" value="Grabar Dependencia" id="bEnviar_depe" class="botones" onclick="submit_agregar_dependencia()">
								<center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para agregar Dependencia-->
<!--Desde aqui el div que contiene el formulario para Modificar Dependencia-->	
		<div id="ventana2">
			
			<div class="form">
				<div class="cerrar"><a href='javascript:cerrarVentanaModificarDependencia();'>Cerrar X</a></div>
				<h1>Formulario Modificar Dependencia</h1>
				<hr>
				<form id ="formulario_modificar_dependencia" name ="formulario_modificar_dependencia" autocomplete="off">
					<table>
						<tr>
							<td class="descripcion">
								Código de cambio Organico-Funcional
							</td>
							<td class="detalle">
								<div class="contenedor_select">
									<select id="id_cambio_organico_funcional_mod">
										<!-- Listado viene de login/validar_inactividad.php -->
										<?php echo $listado_cambios_organico_funcionales ?>
									</select>
									<i><div class="flecha_abajo"></div></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Codigo de la Dependencia (Letra o Numero):</td>
								<input type="hidden" name ="id_dependencia" id="id_dependencia">
								<input type="hidden" name ="tipo_formulario_mod" id="tipo_formulario_mod" value="modificar_dependencia"><!--Tipo de formulario para query_dependencias.php-->
								<input type="hidden" name ="antiguo_mod_padre" id="antiguo_mod_padre">
							<td class="detalle">
								<input type="text" name="mod_codigo_dependencia" id="mod_codigo_dependencia" title="Este campo no se puede modificar" class="readonly" readonly >
							</td>
						</tr>
						<tr>
							<td class="descripcion">Nombre de la Dependencia :</td>
							<td class="detalle">
								<input type="hidden" name="antiguo_nombre_dependencia" id="antiguo_nombre_dependencia">
								<input type="search" placeholder="Digite Nombre de la dependencia" name="mod_nombre_dependencia" id="mod_nombre_dependencia" onblur="validar_modificar_dependencia()">

								<div id="sugerencia_mod_nombre_dependencia" class="sugerencia"></div>
								<div id="mod_dependencia_ya_existe" class="errores2">El nombre de la dependencia ya existe, no es posible modificar una nueva dependencia asignando éste nombre</div>								
								<div id="mod_nombre_dependencia_max" class="errores">El nombre de la dependencia no puede ser mayor a 100 caracteres. (Actualmente <b><u id='mod_nombre_dependencia_contadormax'></u></b> caracteres)</div>
								<div id="mod_nombre_dependencia_min" class="errores">El nombre de la dependencia no puede ser menor a 6 caracteres (numeros o letras)</div>
								<div id="mod_nombre_dependencia_null" class="errores">El nombre de la dependencia es obligatorio</div>
							</td>
						</tr>
						<tr>
							<td class="descripcion">Dependencia Padre :</td>
							<td class="detalle">
								<input type="search" placeholder="Digite Dependencia Padre (Si tiene)" name="mod_dependencia_padre" id="mod_dependencia_padre" >							

								<div id="sugerencia_dependencia_mod_padre" class="sugerencia"></div>
								
								<div id="error_nombre_mod_dependencia_padre" class="errores">El nombre de la dependencia no existe en la base de datos. Intente otro nombre</div>
								<div id="error_nombre_mod_dependencia_padre2" class="errores">Por favor seleccione una dependencia padre válida</div>
								<div id="error_nombre_mod_dependencia_padre3" class="errores">La dependencia padre no puede ser el mismo nombre de la dependencia que se está creando.</div>
							</td>
						</tr>
						
						<tr>
							<td class="descripcion">Activa :</td>
							<td class="detalle">
								<select name="mod_activa" id="mod_activa" class='select_opciones'>
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<center id="boton_modificar_dependencia">
									<input type="button" value="Modificar Dependencia" id="enviar_mod_dependencia" class="botones">
								<center>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
<!--Hasta aqui el div que contiene el formulario para modificar Dependencia -->

		<div class="center" id="logo">
			<br>
			<h1 style="margin-top:-10px;">Configuración Dependencias</h1>
		</div>
		<div class="form center">
			<input type="search" id="search_dependencias" placeholder="Ingrese Nombre de dependencia" title="Ingrese nombre de la dependencia. Solo muestra los 10 primeros desplegable_resultados">
		</div>
		<div id="desplegable_resultados" style="padding: 10px;">
			<center>
				<div style="background: #2D9DC6; border-radius: 10px; color: #FFFFFF; cursor: pointer; display: inline-block; font-weight: bold; padding: 10px; width: 100px;" title="Cargar listado en plantilla/formato de dependencias en formato Excel de manera masiva"><img src="imagenes/iconos/icono_cargar_archivo.png" height="15px" style="margin-right: 5px;" onclick="cargar_plantilla_dependencias()">  Cargar</div>

				<?php 
					/* Se arma la información que se envía a formatos/reporte_excel.php */
					$query_dependencias = "select * from dependencias where id_dependencia!='1' order by codigo_dependencia";

					echo "<a href='formatos/reporte_excel.php?nombre_reporte=listado_dependencias&sql=".urlencode($query_dependencias)."&codigo_entidad=$codigo_entidad' style='text-decoration:none;'>
				<div style='background: #2D9DC6; border-radius: 10px; color: #FFFFFF; cursor: pointer; display: inline-block; font-weight: bold; margin-left : 5px; padding: 10px; width: 100px;' title='Descargar listado de dependencias'><img src='imagenes/iconos/icono_descargar_archivo.png' height='15px' style='margin-right: 5px;'>Descargar</div></a><br>
				";
				?>

				<div style="background: #2D9DC6; border-radius: 10px; color: #FFFFFF; cursor: pointer; display: inline-block; margin-left : 5px; margin-top : 5px; padding: 10px; " onclick="descargar_csv_formato_dependencias()" >Puede descargar el Formato de dependencias en formato Excel desde aqui. Recuerde guardarlo como "CSV (delimitado por comas)(*.csv)</div>
			</center>
		</div>

<!-- Desde aqui es el despliegue del gráfico del organigrama de dependencias  -->	
	<?php 
		$query_dependencias= "select * from dependencias where activa ='SI' and id_dependencia!='1'";
		$fila_dependencias = pg_query($conectado,$query_dependencias);
	/* Calcula el numero de registros que genera la consulta anterior. */
		$registros_dependencias= pg_num_rows($fila_dependencias);
		$organigrama="";
		for ($i=0;$i<$registros_dependencias;$i++){
			$linea_dependencia = pg_fetch_array($fila_dependencias);

			// $organigrama = $organigrama."[\"".$linea_dependencia['nombre_dependencia']."\",\"".$linea_dependencia['dependencia_padre']."\",\"\"],";
			// $organigrama = $organigrama."[{'v':\"".$linea_dependencia['nombre_dependencia']."\", 'f':\"<b style='color: #ff9104; font-size: 25px;'>(".$linea_dependencia['codigo_dependencia'].")</b><br>".$linea_dependencia['nombre_dependencia']."\"},\"".$linea_dependencia['dependencia_padre']."\",\"Codigo y Nombre de Dependencia\"],";
			$activa  			= $linea_dependencia['activa'];
			$codigo_dependencia = $linea_dependencia['codigo_dependencia'];
			$dependencia_padre  = $linea_dependencia['dependencia_padre'];
			$id_cambio_of 		= $linea_dependencia['id_cambio_organico_funcional'];
			$id_dependencia 	= $linea_dependencia['id_dependencia'];
			$nombre_dependencia = $linea_dependencia['nombre_dependencia'];
		
			/* Se arma el organigrama contemplando la accion de onclick sobre el div que se pone como mascara delante del cuadro que contiene la dependencia en el parámetro 'f'. */
			$organigrama = $organigrama."[{'v':'$nombre_dependencia','f':'<div style=\"height: 100%; padding: 3px; width:100%;\" onclick=\"cargar_modifica_dependencia(\'$codigo_dependencia\',\'$nombre_dependencia\',\'$dependencia_padre\',\'$activa\',\'$id_dependencia\',\'$id_cambio_of\')\"><b style=\"color: #ff9104; font-size: 25px;\">($codigo_dependencia)</b><br>$nombre_dependencia</div>'},\"$dependencia_padre\",\"Click para modificar dependencia ($codigo_dependencia) - $nombre_dependencia\"],";
		}
		$organigrama1=substr($organigrama, 0,-1);
	?>
			<script type="text/javascript">
			//	  google.charts.load('current', {packages:["orgchart"]});
			    google.charts.setOnLoadCallback(drawChart);

			    function drawChart() {
			        var data = new google.visualization.DataTable();
			        data.addColumn('string', 'Name');
			        data.addColumn('string', 'Manager');
			        data.addColumn('string', 'ToolTip','Funcion');

			        // Por cada una de las casillas, ingresa el nombre, jefe y tooltip para mostrar
			        data.addRows([
			        	<?php echo $organigrama1; ?>
			        ]);

       		        var container = document.getElementById('chart_div');
			        // Se crea el organigrama.
			        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));

			        // Dibuje el gráfico, estableciendo la opción allowHtml en true para la información sobre tooltips
			        chart.draw(data, {allowHtml:true, nodeClass: 'clase_organigrama'});
			    }
			</script>
			<center>
				<div id="chart_div"></div>
			</center>
<!-- Hasta aqui es el despliegue del gráfico del organigrama de dependencias  -->	

		<hr>
	</div>
</body>
</html>
