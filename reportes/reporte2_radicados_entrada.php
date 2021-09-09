<?php 
   	require_once("../login/validar_inactividad.php");
?>
<script type="text/javascript">
/*****************************************************************************************
	Inicio funcion cargar_reporte2() para cargar la tabla con la informacion de los radicados en la base de datos
	/*****************************************************************************************
	* @brief No recibe datos, en la estructuracion de la funcion los recoge del formulario de la tabla (#formulario_filtros_reporte2)
	* @return {string} String con la tabla y estadistica en forma de dona listado los radicados que existen en la base de datos con la relacion de filtros recogidos y que se cambian en la tabla (#formulario_filtros_reporte2).
	*****************************************************************************************/		
function cargar_reporte_2(){
	loading('resultado_reporte');
	/* Recoleccion de datos que se desempeñan de filtros */
	var fecha_inicial_reporte2 			  = $("#fecha_inicial_reporte2").val();
	var fecha_final_reporte2 			  = $("#fecha_final_reporte2").val();
	var select_dependencia_reporte2       = $("#dependencia_reporte2").val();
	/* Fin Recoleccion de datos que se desempeñan de filtros */
	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',//Destino
		data: {
			'recibe_ajax'  	 					: 'reporte2',
			'fecha_inicial_reporte2' 	        : fecha_inicial_reporte2,
			'fecha_final_reporte2' 		        : fecha_final_reporte2,
			'select_dependencia_reporte2'       : select_dependencia_reporte2
		},
		success: function(resp){//Se recoge la variable que recibe desde procesar_ajax.php
			if(resp!=""){//Si resp es diferente a vacio
				$('#resultado_reporte').html(resp);//Se escribe html dentro del id seleccionado
			}
		}
	})
}
/*****************************************************************************************
	Fin funcion cargar_reporte2() para cargar la tabla con la informacion de los radicados en la base de datos
/*****************************************************************************************/

function mas_tabla(codigo_dependencia,estado_radicado,usuarios_control) {
	loading('resultado_reporte')
	var fecha_inicial_reporte2 			  = $("#fecha_inicial_reporte2").val();
	var fecha_final_reporte2 			  = $("#fecha_final_reporte2").val();
	
	if(estado_radicado==""){
		estado = "vacio";
	}else{
		estado = estado_radicado;
	}

	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',//Destino
		data: {
			'recibe_ajax'  	 			: 'reporte2_estados',
			'dependencia' 				: codigo_dependencia,
			'estado_radicado'       	: estado,
			'fecha_final_reporte2' 		: fecha_final_reporte2,
			'fecha_inicial_reporte2' 	: fecha_inicial_reporte2,
			'usuarios_control' 	        : usuarios_control
		},
		success: function(resp){//Se recoge la variable que recibe desde procesar_ajax.php
			if(resp!=""){//Si resp es diferente a vacio
				$('#resultado_reporte').html(resp);//Se escribe html dentro del id seleccionado
			}
		}
	})
}
/*****************************************************************************************
	Inicio funcion usuarios_dependencia(codigo_dependencia) para cargar el select con la informacion de usuarios filtrados por dependencias
	/*****************************************************************************************
	* @brief 
	- Recibe codigo_dependencia del select (#dependencia_reporte2) que tomara el value de la option seleccionada
	* @return {string} String con la tabla y estadistica en forma de dona listado los radicados que existen en la base de datos con la relacion de filtros recogidos y que se cambian en la tabla (#formulario_filtros_reporte2).
	*****************************************************************************************/	
// function usuarios_dependencia(codigo_dependencia){
// 	$.ajax({
// 		type: 'POST',
// 		url:  'include/procesar_ajax.php',//Destino
// 		data: {
// 			'recibe_ajax'			: 'reporte2_usuarios_dependencia',
// 			'codigo_dependencia' 	: codigo_dependencia
			
// 		},
// 		success: function(resp){//Se recoge la variable que recibe desde procesar_ajax.php
// 			if(resp!=""){//Si resp es diferente a vacio
// 				$('#usuario_radicador_reporte2').html(resp);//Se escribe html dentro del id seleccionado
// 			}
// 		}
// 	})
// }
/*****************************************************************************************
	Fin funcion usuarios_dependencia(codigo_dependencia) para cargar el select con la informacion de usuarios filtrados por dendencias
/*****************************************************************************************/
</script>
<center><h1>Reporte de Radicados</h1>
<?php 
	$timestamp_dia  = date('Y-m-d');	// Genera la fecha de transaccion

	$query_dependencia_reporte2         = "select codigo_dependencia, nombre_dependencia from dependencias where codigo_dependencia != 'ADM' and codigo_dependencia != 'ADMI' and codigo_dependencia != 'ADMIN' order by nombre_dependencia asc";//Estrucutura de consulta a base de datos
	$fila_dependencia_reporte2          = pg_query($conectado,$query_dependencia_reporte2);//Se envia la consulta mediante pg_query
	$registros_dependencia_reporte2     = pg_num_rows($fila_dependencia_reporte2); // Se trae la cantidad de filas de la query
	/* Fin Consulta a base de datos */
	echo"<table id='formulario_filtros_reporte2' border='0' class='center'>
		<tr>
			<td class='descripcion'>Fecha Inicial</td>
			<td class='descripcion'>Fecha Final</td>
			<td class='descripcion'>Dependencia</td>
		</tr>
		<tr>
			<td style='padding-right:20px;'>
				<input type='date' id='fecha_inicial_reporte2' value='$timestamp_dia' class='input_search' onchange='cargar_reporte_2()'>
			</td>
			<td style='padding-right:20px;'>
				<input type='date' id='fecha_final_reporte2' value='$timestamp_dia' class='input_search' onchange='cargar_reporte_2()'><br>
			</td>
	
		<td>
			<select id='dependencia_reporte2' class='select_opciones' onchange='cargar_reporte_2();'>
				<option value='' selected>TODAS LAS DEPENDENCIAS</option>";
				/* Creacion de los option por cada registro almacenado en $registros_dependencia_reporte2 */
				for ($i=0; $i < $registros_dependencia_reporte2; $i++){
					$linea_dependencia_reporte2 = pg_fetch_array($fila_dependencia_reporte2);//Se pasa a un array la informacion de la base de datos
					//Se da valores independientes a la informacion del array $linea_dependencia_reporte2
					$codigo_dependencia	= $linea_dependencia_reporte2['codigo_dependencia'];
					$dependencia 		= $linea_dependencia_reporte2['nombre_dependencia'];
					/* Fin Se da valores independientes a la informacion del array $linea_dependencia_reporte2 */
				    echo "<option value='$codigo_dependencia'>($codigo_dependencia)$dependencia</option>";
				}
				/* Fin Creacion de los option por cada registro almacenado en $registros_dependencia_reporte2 */
	echo "</select>
		</td>
		</tr>
	</table>
	";	
?>
<div id="resultado_reporte"></div>
</center>
<script type="text/javascript">cargar_reporte_2();</script>