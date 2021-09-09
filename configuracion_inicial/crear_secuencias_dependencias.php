<link href="../imagenes/logo3.png" type="image/x-icon" rel="shorttcut icon"/>
<?php 
/* Archivo que se invoca por navegador para crear las secuencias de radicacion por cada una de las dependencias que existan en el sistema. 							******
* Al llamarla solo muestra las query de insertar en la tabla consecutivo para verificar las variables que si estén bien definidas o se pueda cambiar en el archivo. ******
* Hay que definir las variables dependiendo del lugar de instalacion. Si tiene 3,4 o 5 caracteres el codigo de dependencia, varía $dependencia_administrador quedando 'ADM', 'ADMI' o 'ADMIN' 
* Si la entidad define que debe existir un solo consecutivo para TODA la entidad, se define la variable $dependencia_padre_todos. Por ejemplo si toda la entidad va a tener el consecutivo de la dependencia padre '100' entonces se define ($dependencia_padre_todos = '100';)
*/

/* Se define 'ADM', 'ADMI' o 'ADMIN' */
	$dependencia_administrador 	= 'ADMI';
/* Se define codigo de dependencia de la cual van a depender TODOS los consecutivos */
	$dependencia_padre_todos 	= '1000';

	require_once('../login/conexion2.php');
	$query_dependencias="select codigo_dependencia from dependencias where codigo_dependencia != '$dependencia_administrador'";

	$fila_dependencias 	= pg_query($conectado,$query_dependencias);

	/*Calcula el numero de registros que genera la consulta anterior.*/
	$registros_depe= pg_num_rows($fila_dependencias);

	$query_crear_secuencias 		= "";
	$mostrar_query_crear_secuencias = "";

	for ($i=0; $i < $registros_depe; $i++) { 
		$linea_dependencias = pg_fetch_array($fila_dependencias);
		$codigo_depe = $linea_dependencias['codigo_dependencia'];

		for ($j=0; $j < 5; $j++) { 
			$k = $j+1;

			$mostrar_query_crear_secuencias.="insert into consecutivos(year,codigo_dependencia, tipo_radicado, dependencia_consecutivo_padre) values ('2020', '$codigo_depe','$k','$dependencia_padre_todos');<br> create sequence secuencia_".$codigo_depe."_$k start with 1 increment by 1 maxvalue 999999 minvalue 1;<br>";
			$query_crear_secuencias.="insert into consecutivos(year,codigo_dependencia, tipo_radicado, dependencia_consecutivo_padre) values ('2020', '$codigo_depe','$k','$dependencia_padre_todos'); create sequence secuencia_".$codigo_depe."_$k start with 1 increment by 1 maxvalue 999999 minvalue 1;";
		}

	}
if(isset($_POST['enviado'])){
	if(pg_query($query_crear_secuencias)){
		echo "Secuencias creadas";
	}else{
		echo "No se pudo crear las secuencias";
	}
}else{
	echo "$mostrar_query_crear_secuencias";
}
// var_dump($_POST);
echo "<br>";
 ?>
	<form action="#" method="post">
		<input type="hidden" name="enviado" value="SI">
		<input type="submit" value="Enviar">
	</form>