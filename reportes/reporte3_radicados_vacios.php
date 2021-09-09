<?php
	require_once("../login/validar_inactividad.php");

	$jefe_dependencia = $_SESSION['jefe_dependencia'];
?>
<script type="text/javascript">
/*****************************************************************************************
	Inicio funcion usuarios_asignados_dependencia(cod_dependencia) // EL DESAROLLO SE BASA EN EL REPORTE 2 EL CUAL TIENE EN PROCESAR AJAX UNA PETICION PARA CARGAR LOS USUARIOS DE LA DEPENDENCIA, LA SENTENCIA SERA UTILIZADA PARA GENERAR EL LISTADO EN REPORTE 3 // La funcion carga el select con la información de usuarios filtrados por dependencias
	/*****************************************************************************************
	- Recibe cod_dependencia del select de las dependencias
	* @return {string} Listado de usuarios con discriminados por dependencia
	*****************************************************************************************/	
function usuarios_asignados_dependencia(cod_dependencia){
	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',//Destino
		data: {
			'recibe_ajax'			: 'reporte2_usuarios_dependencia',
			'codigo_dependencia' 	: cod_dependencia
			
		},
		success: function(resp){//Se recoge la variable que recibe desde procesar_ajax.php
			if(resp!=""){//Si resp es diferente a vació
				$('#usuarios_dependencia').html(resp);//Se escribe html dentro del id seleccionado
			}
		}
	})
}
/*****************************************************************************************
	Fin funcion usuarios_asignados_dependencia(cod_dependencia)
/*****************************************************************************************/

/*****************************************************************************************
	Inicio funcion cargar_reporte_radicados_vacios() para cargar la tabla con la información de los radicados que no están completos
	/*****************************************************************************************
	* @return {string} String con la tabla y estadística en forma de dona listado los radicados que no estén completados
	*****************************************************************************************/		
function cargar_reporte_radicados_vacios(){
	/* Recolección de datos que se desempeñan de filtros */
	var tipo_radicado_vacio 			  = $("#tipo_radicado_vacio").val();
	var dependencia 			          = $("#dependencia").val();
	var usuarios_dependencia 		      = $("#usuarios_dependencia").val();
	var fecha_inicial 					  = $("#fecha_inicial").val();
	var fecha_final 				      = $("#fecha_final").val();
	/* Fin Recolección de datos que se desempeñan de filtros */
	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',//Destino
		data: {
			'recibe_ajax'  	 		: 'reporte3',
			'tipo_radicado_vacio'   : tipo_radicado_vacio,
			'dependencia' 			: dependencia,
			'usuarios_dependencia' 	: usuarios_dependencia,
			'fecha_inicial' 	    : fecha_inicial,
			'fecha_final' 		    : fecha_final
		},
		success: function(resp){//Se recoge la variable que recibe desde procesar_ajax.php
			if(resp!=""){//Si resp es diferente a vació
				$('#resultado_radicados_vacios').html(resp);//Se escribe html dentro del id seleccionado
			}
		}
	})
}
/*****************************************************************************************
	Fin funcion cargar_reporte_radicados_vacios()
/*****************************************************************************************/




/*****************************************************************************************
	Inicio funcion cargar_reporte_radicados_vacios() para cargar la tabla con la información de los radicados que no están completos
	/*****************************************************************************************
	* @return {string} String con la tabla y estadística en forma de dona listado los radicados que no estén completados
	*****************************************************************************************/		
function mas_tabla(tipo, usuario){
	/* Recolección de datos que se desempeñan de filtros */
	var fecha_inicial 					  = $("#fecha_inicial").val();
	var fecha_final 				      = $("#fecha_final").val();
	/* Fin Recolección de datos que se desempeñan de filtros */
	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',//Destino
		data: {
			'recibe_ajax'  	 		: 'reporte3_2',
			'tipo'				    : tipo,
			'usuario' 			    : usuario,
			'fecha_inicial' 	    : fecha_inicial,
			'fecha_final' 		    : fecha_final
		},
		success: function(resp){//Se recoge la variable que recibe desde procesar_ajax.php
			if(resp!=""){//Si resp es diferente a vació
				$('#resultado_radicados_vacios').html(resp);//Se escribe html dentro del id seleccionado
			}
		}
	})
}
/*****************************************************************************************
	Fin funcion cargar_reporte_radicados_vacios()
/*****************************************************************************************/

</script>
<center>
	<h1>
		Reporte de Radicados Sin Terminar
	</h1>
<?php 
   	require_once("../login/conexion2.php");
   	
	$timestamp_dia  = date('Y-m-d');	// Genera la fecha de transacción

	if($jefe_dependencia=="SI"){
		$sql_dependencia_radicados_vacios   = "select codigo_dependencia, nombre_dependencia from dependencias where codigo_dependencia !='ADM' and codigo_dependencia != 'ADMI' and codigo_dependencia != 'ADMIN' and codigo_dependencia != '999' order by nombre_dependencia asc";//Estructura de consulta a base de datos
		$todas_dependencias 				= "<option value='' >TODAS LAS DEPENDENCIAS</option>";
	}else{
		$sql_dependencia_radicados_vacios   = "select codigo_dependencia, nombre_dependencia from dependencias where codigo_dependencia = '$codigo_dependencia'";//Estructura de consulta a base de datos $codigo_dependencia viene de login/validar_inactividad.php
		$todas_dependencias 				= "";
	}
	/* Consulta a base de datos */
	$fila_dependencia_radicados_vacios      = pg_query($conectado,$sql_dependencia_radicados_vacios);//Se enviá la consulta mediante pg_query
	$registros_dependencia_radicados_vacios = pg_num_rows($fila_dependencia_radicados_vacios); // Se trae la cantidad de filas de la query
	/* Fin Consulta a base de datos */
	echo"<table border='0' class='center'>
		<tr>
			<td class='descripcion'>Tipo de Radicado Vació</td>
			<td class='descripcion'>Dependencia</td>
			<td class='descripcion'>Usuarios en la Dependencia</td> 
			<td class='descripcion'>Fecha Inicial</td>
			<td class='descripcion'>Fecha Final</td>
		</tr>
		<tr>
			<td class='detalle'>
				<select id='tipo_radicado_vacio' class='select_opciones' onchange='cargar_reporte_radicados_vacios()'>
				<option value='' selected>TODOS LOS TIPOS</option>
				<option value='asunto'>ASUNTO VACIO</option>
				<option value='path_radicado'>IMAGEN PDF VACIA</option>
			</td>";
	echo "</select>
		</td>
		<td class='detalle'>
			<select id='dependencia' class='select_opciones' onchange='cargar_reporte_radicados_vacios(); usuarios_asignados_dependencia(this.value)'>
				$todas_dependencias";
				/* Creación de los option por cada registro almacenado en $registros_dependencia_reporte2 */
				for ($i=0; $i <$registros_dependencia_radicados_vacios; $i++){
					$linea_dependencia_radicados_vacios = pg_fetch_array($fila_dependencia_radicados_vacios);//Se pasa a un array la información de la base de datos
					//Se da valores independientes a la información del array $linea_dependencia_reporte2
					$codigo_dependencia	= $linea_dependencia_radicados_vacios['codigo_dependencia'];
					$dependencia 		= $linea_dependencia_radicados_vacios['nombre_dependencia'];
					/* Fin Se da valores independientes a la información del array $linea_dependencia_reporte2 */
					if($codigo_dependencia == $_SESSION['dependencia']){
						echo "<option value='$codigo_dependencia' selected>($codigo_dependencia) - $dependencia</option>";
					}else{
						echo "<option value='$codigo_dependencia'>($codigo_dependencia) - $dependencia</option>";
					}   
				}
				/* Fin Creación de los option por cada registro almacenado en $registros_dependencia_reporte2 */
	echo "</select>
		</td>
		<td class='detalle'>
			<select id='usuarios_dependencia' class='select_opciones' onchange='cargar_reporte_radicados_vacios()'>
			</select>
		</td>
		<td style='padding-right:20px;' class='detalle'>
			<input type='date' id='fecha_inicial' value='$timestamp_dia' class='input_search' onchange='cargar_reporte_radicados_vacios()'>
		</td>
		<td style='padding-right:20px;' class='detalle'>
			<input type='date' id='fecha_final' value='$timestamp_dia' class='input_search' onchange='cargar_reporte_radicados_vacios()'><br>
		</td>
		</tr>
	</table>
	";	
?>
<br>
<div id="resultado_radicados_vacios"></div>
</center>

<script type="text/javascript">
	usuarios_asignados_dependencia($("#dependencia" ).val());
	cargar_reporte_radicados_vacios();
</script>