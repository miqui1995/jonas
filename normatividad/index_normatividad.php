<?php 
	require_once("../login/validar_inactividad.php");

	if($_SESSION['codigo_entidad']=='EJC' || $_SESSION['codigo_entidad']=='EJEC'){
		$encabezado_normatividad = "Esta es la normatividad con la que el SGD cumple actualmente";
	}else{
		$encabezado_normatividad = "Esta es la normatividad general con la que el SGD Jonas cumple actualmente.";
	}

	/* Define si se va a mostrar inicialmente la normatividad general o la normatividad interna */
	$tipo_normat = $_POST['tipo_normatividad'];

	if($tipo_normat=="general"){
		$estilo_entidad = "style= 'display: none;'";
		$estilo_general = "style= ''";
	}else{
		$estilo_entidad = "style= ''";
		$estilo_general = "style= 'display: none;'";		
	}

	/* Si no es un administrador del sistema, no le muestra las opciones para normatividad interna */
	if($_SESSION['administrador_sistema']!="SI"){
		$estilo_botones = "style = 'display: none;'";
	}else{
		$estilo_botones = "";
	}
	/* Se realiza consulta en normatividad interna de todos los cambios_organico_funcionales existentes */
	$query_consulta_cambios_organico_funcionales 	 = "select * from cambios_organico_funcionales order by fecha_inicial_cambio";

	/* Se ejecuta la query y calcula la cantidad de filas */
	$fila_consulta_cambios_organico_funcionales 	 = pg_query($conectado,$query_consulta_cambios_organico_funcionales);
	$registros_consulta_cambios_organico_funcionales = pg_num_rows($fila_consulta_cambios_organico_funcionales);

	$tabla_paso1 = "";

	if($registros_consulta_cambios_organico_funcionales == 0){
	/* Si no hay registros en la tabla cambios_organico_funcionales */
		$paso1 = "<div class='hover_pointer div_pendientes'><img src='imagenes/iconos/checkbox3.png' style='float: left; height:25px; margin-right: 10px;'><h3 float: left;'>No se ha cargado todavía ninguna estructura organico-funcional. Para cargar una nueva haga click  <a href='javascript:cargar_cambio_of();'>aquí</a></h3></div>";
		$id_cambio_organico_funcional 	= "1";
	}else{
		$paso1="";
		$tabla_paso1 = "<table border='1' id='tabla_paso1' style='float:left; width: 100%;'><tr><td class='descripcion center'>ID</td><td class='descripcion center'>Fecha Inicial (YYYY-MM-DD)</td><td class='descripcion center'>Fecha Final (YYYY-MM-DD)</td><td class='descripcion center'>Acto Administrativo</td></tr>";

		for ($i=0; $i < $registros_consulta_cambios_organico_funcionales; $i++) { 		
			$linea_cambios_organico_funcionales = pg_fetch_array($fila_consulta_cambios_organico_funcionales);
			$id_cambios_organico_funcionales  	= $linea_cambios_organico_funcionales['id_cambio_organico_funcional'];
			$fecha_inicial_cambio  				= $linea_cambios_organico_funcionales['fecha_inicial_cambio'];
			$fecha_final_cambio1  				= $linea_cambios_organico_funcionales['fecha_final_cambio'];
			$path_acto_administrativo  			= $linea_cambios_organico_funcionales['path_acto_administrativo'];

			if($fecha_final_cambio1==""){
				$fecha_final_cambio = "<div style='color:green; font-weight:bold;'>Version utilizada actualmente</div>";
			}else{
				$fecha_final_cambio = $fecha_final_cambio1;
			}
			
			/* Defino el onclick y el title de cada uno de los resultados */
			$onclick_paso1 = "onclick=\"modificar_acto_admin('$id_cambios_organico_funcionales','$fecha_inicial_cambio','$fecha_final_cambio1')\" title='Click para modificar ésta información'";

			if($path_acto_administrativo==""){
				$acto_adm = "<div style='font-weight:bold;' $onclick_paso1>No se ha cargado PDF con el acto administrativo. Para cargarlo haga click aqui </div>";
			}else{
				// $path_acto_administrativo1 = substr($path_acto_administrativo, 0, -4);
				$acto_adm = "<div class='botones2' onclick=\"visualizar_acto_admin('$path_acto_administrativo')\" title='Click para visualizar'>Ver acto administrativo</div>";	
			}

			$tabla_paso1.="<tr>
				<td class='detalle center' $onclick_paso1>
					$id_cambios_organico_funcionales
				</td>
				<td class='detalle center' $onclick_paso1>
					$fecha_inicial_cambio
				</td>
				<td class='detalle center' $onclick_paso1>
					$fecha_final_cambio
				</td>
				<td class='detalle center'>
					$acto_adm
				</td>
			</tr>";
		}
		$tabla_paso1.="</table><div id='contenedor_viewer_acto_admin' style='display:none;'><iframe frameborder='0' id='viewer_acto_admin' scrolling='yes' style='background-color: #008080; float: left; height: 500px; width: 50%;'></iframe></div>";

		/* Consulta si es la ultima versión de cambios_organico_funcionales */
		$query_version_actual_cambios_of = "select count(*) from cambios_organico_funcionales where fecha_final_cambio=''";
		$fila_version_actual_cambios_of = pg_query($conectado,$query_version_actual_cambios_of);
		$linea_version_actual_cambios_of = pg_fetch_array($fila_version_actual_cambios_of);
		$count_version_actual_cambios_of = $linea_version_actual_cambios_of['count'];

		if($count_version_actual_cambios_of==0){
			$tabla_paso1.="<div class='hover_pointer div_pendientes' style='float:left; margin-top:10px; width:98%;'><img src='imagenes/iconos/checkbox3.png' style='float: left; height:25px; margin-right: 10px;'><h3 float: left;'>No se ha cargado todavía la versión de la estructura organico-funcional que se usa actualmente (<u>Sin Fecha Final</u>). Para cargar una nueva haga click  <a href='javascript:cargar_cambio_of();'>aquí</a></h3></div>";
		}

		/* Se obtiene el max(id_cambio_organico_funcional para crear uno nuevo. */
		$query_max_cambios_of 			= "select max(id_cambio_organico_funcional) from cambios_organico_funcionales";
		$fila_max_cambios_of 			= pg_query($conectado,$query_max_cambios_of);
		$linea_max_cambios_of  			= pg_fetch_array($fila_max_cambios_of);
		$max_id 						= $linea_max_cambios_of['max'];
		$id_cambio_organico_funcional  	= $max_id+1;

	}	
	/* PASO3 Se consulta si existe espacio entre las fechas de la versión de cambios_organico_funcionales utilizando los registros obtenidos en el PASO1 */
 	$contenido_paso3 	= "";
 	if($registros_consulta_cambios_organico_funcionales != 0){
 		$fecha_ant 			= "";
 		$fila_consulta_cambios_organico_funcionales1 	 = pg_query($conectado,$query_consulta_cambios_organico_funcionales);

 		for ($i=0; $i < $registros_consulta_cambios_organico_funcionales; $i++) { 
 			$linea_consulta_cof = pg_fetch_array($fila_consulta_cambios_organico_funcionales1);
 			$fecha_ini_cambio 	= $linea_consulta_cof['fecha_inicial_cambio'];
 			$fecha_fin_cambio 	= $linea_consulta_cof['fecha_final_cambio'];
 			$id_cam 		 	= $linea_consulta_cof['id_cambio_organico_funcional'];

 			if($fecha_ant!=""){
 				/* Da formato a fecha y suma un dia para comparar si hay intervalo de tiempo pendiente por cambio organico-funcional */
					$fecha_ant2 	= date_create($fecha_ant);
					date_add($fecha_ant2, date_interval_create_from_date_string('1 day'));
				$fecha_ant1 	= date_format($fecha_ant2, 'Y-m-d');;

 				if($fecha_ant1!=$fecha_ini_cambio){
 					$pasos_faltantes_configuracion++;

 					$contenido_paso3.= "<img src='imagenes/iconos/checkbox3.png' style='float: left; height:20px; margin-right: 10px;'><b style='color:blue';>ID($id_cam)</b> Falta estructura Organico Funcional en el rango de fechas <font color='red'>($fecha_ant)</font> hasta <font color='red'>($fecha_ini_cambio)</font><br><br>";
 				}else{
					$pasos_completados_configuracion++;
 				}
 			}
 			$fecha_ant = $fecha_fin_cambio; // Asigna $fecha_fin_cambio para siguiente en el for
 		}
 	}
 	if($contenido_paso3!=""){
 		$contenido_paso3 = substr($contenido_paso3,0, -5);
		$tabla_paso1.="<div class='hover_pointer div_pendientes' style='float:left; margin-top:10px; width:98%;'>$contenido_paso3</div>";
	}

	if($contenido_paso4!=""){
 		$contenido_paso4 = substr($contenido_paso4,0, -5);
		$tabla_paso1.="<div class='hover_pointer div_pendientes' style='float:left; margin-top:10px; width:98%;'>$contenido_paso4</div>";
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Administración Parametrización</title>
	<script type="text/javascript" src="include/js/funciones_normatividad.js"></script>
	<link rel="stylesheet" href="include/css/estilos_normatividad.css">
</head>
<script type="text/javascript">

	function cargar_cambio_of(id_cambio_of){
		$("#ventana_formulario_nuevo_cambio_of").slideDown("slow");
		if(typeof(id_cambio_of)!="undefined"){
			console.log("Valor ->"+id_cambio_of+"<--");
		}else{
			$("#fecha_inicial_of").focus();
		}
	}

	/* Funcion para reducir a la mitad la informacion del formulario de agregar_nuevo_cambio */
	function mitad_info_agregar_nuevo_cambio(){
		$(".info_nuevo_cambio").animate({width: "40%"},{queue: false,duration: 500});
		$("#fecha_final_of").animate({width: "52%"},{queue: false,duration: 500});
		$("#viewer").animate({width: "100%"},{queue: false,duration: 500});
	}
	/* Funcion para reducir a la mitad la informacion del formulario de agregar_nuevo_cambio */
	function mitad_info_modificar_cambio(){
		$(".info_nuevo_cambio").animate({width: "40%"},{queue: false,duration: 500});
		$("#mod_fecha_final_of").animate({width: "52%"},{queue: false,duration: 500});
		$("#viewer2").animate({width: "100%"},{queue: false,duration: 500});
	}

	/* Funcion para cargar modificacion a actos administrativos */
	function modificar_acto_admin(id_cambios_organico_funcionales, fecha_inicial_cambio,fecha_final_cambio){
		$("#ventana_formulario_modificar_cambio_of").slideDown("slow");

		$("#mod_id_cambio_of").val(id_cambios_organico_funcionales);
		$("#mod_fecha_inicial_of").val(fecha_inicial_cambio);
		$("#mod_fecha_final_of").val(fecha_final_cambio);
	}

	/* Funcion para enviar la creación del cambio organico-funcional*/
	function submit_agregar_cambio_of(){
		var fecha_fin_of 	= $("#fecha_final_of").val();
		var fecha_ini_of 	= $("#fecha_inicial_of").val();
		var id_cambio_org_f = $("#id_cambio_org_f").val();

		if(fecha_ini_of==""){ /* Verifica si la fecha_inicial está vacía */
			$("#fecha_inicial_vacio").slideDown("slow");
			$("#fecha_inicial_of").focus();
		}else{
			$("#fecha_inicial_vacio").slideUp("slow");
		}

		if((fecha_fin_of!="") && (fecha_ini_of>fecha_fin_of)){ /* Verifica si la fecha_inicial es mayor a la fecha_final */
			$("#fecha_inicial_mayor").slideDown("slow");
			$("#fecha_inicial_of").focus();
		}else{
			$("#fecha_inicial_mayor").slideUp("slow");
		}

		if($(".errores").is(":visible")){
			return false;
		}else{
			loading('boton_crear_cambio_of');
			var input_file_of 	= document.getElementById('acto_administrativo_of');
		    var file 			= input_file_of.files[0];

			var data 			= new FormData();

			data.append('recibe_ajax','enviar_agregar_cambio_of');
			data.append('pdf_principal',file);

			data.append('fecha_inicial',fecha_ini_of);
			data.append('fecha_final',fecha_fin_of);
			data.append('id_cambio',id_cambio_org_f);

			$.ajax({
				type: 'POST',
				url: 'include/procesar_ajax.php',
				data: data,			
		        contentType:false,
		        processData:false,
				success: function(resp){
					console.log(resp)
					if(resp!=""){
						$('#resultado_js').html(resp);
					}
				}
			})

		}
	}

	/* Funcion para enviar la modificación del cambio organico-funcional*/
	function submit_modificar_cambio_of(){
		var fecha_fin_of 	= $("#mod_fecha_final_of").val();
		var fecha_ini_of 	= $("#mod_fecha_inicial_of").val();
		var id_cambio_org_f = $("#mod_id_cambio_of").val();

		if(fecha_ini_of==""){ /* Verifica si la fecha_inicial está vacía */
			$("#mod_fecha_inicial_vacio").slideDown("slow");
			$("#mod_fecha_inicial_of").focus();
		}else{
			$("#mod_fecha_inicial_vacio").slideUp("slow");
		}

		if((fecha_fin_of!="") && (fecha_ini_of>fecha_fin_of)){ /* Verifica si la fecha_inicial es mayor a la fecha_final */
			$("#mod_fecha_inicial_mayor").slideDown("slow");
			$("#mod_fecha_inicial_of").focus();
		}else{
			$("#mod_fecha_inicial_mayor").slideUp("slow");
		}

		if($(".errores").is(":visible")){
			return false;
		}else{
			loading('boton_modificar_cambio_of');
			var input_file_of 	= document.getElementById('mod_acto_administrativo_of'); 
		    var file 			= input_file_of.files[0];

			var data 			= new FormData();

			data.append('recibe_ajax','enviar_modificar_cambio_of');
			data.append('pdf_principal',file);

			data.append('fecha_inicial',fecha_ini_of);
			data.append('fecha_final',fecha_fin_of);
			data.append('id_cambio',id_cambio_org_f);

			$.ajax({
				type: 'POST',
				url: 'include/procesar_ajax.php',
				data: data,			
		        contentType:false,
		        processData:false,
				success: function(resp){
					console.log(resp)
					if(resp!=""){
						$('#resultado_js').html(resp);
					}
				}
			})

		}
	}
	/* Funcion para visualizar PDF de actos administrativos */
	function visualizar_acto_admin(path){
		$("#contenedor_viewer_acto_admin").show("slow");
		$('#viewer_acto_admin').attr('src', path);

		$("#tabla_paso1").animate({ width: "50%"}, {queue: false,duration: 500})
		
	}
	
	function verifica_fecha_valida(tipo){
		if(tipo=="crear_cambio_of"){
			var fecha_ini 			= $("#fecha_inicial_of").val();
			var fecha_fin 			= $("#fecha_final_of").val();
			var mod_id_cambio_of 	= "";
		}else{
			var mod_id_cambio_of 	= $("#mod_id_cambio_of").val();	
			var fecha_ini 			= $("#mod_fecha_inicial_of").val();
			var fecha_fin 			= $("#mod_fecha_final_of").val();		
		}

		$.ajax({
			type: 'POST',
			url: 'include/procesar_ajax.php',
			data: {
				'recibe_ajax' 	: 'verifica_fecha_valida',
				'fecha_ini' 	: fecha_ini,	
				'fecha_fin' 	: fecha_fin,
				'id_cambio'		: mod_id_cambio_of	
			},			
	        success: function(resp){
				if(resp.trim()=="permite_crear"){
					if(tipo=="crear_cambio_of"){						
						$("#fecha_inicial_error").slideUp("slow");
						$("#fecha_inicial_error").html("");
					}else{
						$("#mod_fecha_inicial_error").slideUp("slow");
						$("#mod_fecha_inicial_error").html("");						
					}	
				}else{
					if(tipo=="crear_cambio_of"){						
						$("#fecha_inicial_error").slideDown("slow");
						$("#fecha_inicial_error").html(resp);
					}else{
						$("#mod_fecha_inicial_error").slideDown("slow");
						$("#mod_fecha_inicial_error").html(resp);						
					}	
				}
				$('#resultado_js').html(resp);
			}
		})

	}
</script>
<style type="text/css">
	.detalle{
		cursor: pointer;
	}
	.div_pendientes{
		border: solid red 1px;
		text-align: left;
	} 
</style>
<body>
<!-- Desde aqui el div que tiene el formulario principal -->
	<div class="center" id="logo">
		<h1>Modulo Normatividad</h1>
		<center <?php echo $estilo_botones ?>>
			<div style="background: #2D9DC6; border-radius: 10px; color: #FFFFFF; cursor: pointer; display: inline-block; font-weight: bold; margin-left : 5px; padding: 10px; width: 300px;" title="Normatividad con la cual cumple actualmente Jonas" onclick="tipo_normatividad('normatividad_general')">Normatividad General</div>
			<div style="background: #2D9DC6; border-radius: 10px; color: #FFFFFF; cursor: pointer; display: inline-block; font-weight: bold; margin-left : 5px; padding: 10px; width: 300px;" title="Normatividad de la empresa/entidad con la cual cumple actualmente Jonas" onclick="tipo_normatividad('normatividad_entidad')">Normatividad Interna de <?php echo $_SESSION['entidad']; ?></div>
			<br><br>
		</center>

	</div>
	<center id="normatividad_general" <?php echo $estilo_general ?>>
		<h2><?php echo $encabezado_normatividad; ?></h2>
		<table class="encabeza" border="0" width="99%">
			<tr>
				<td width="20%" id="p3" onclick="carga_contenido_param('3')" title="Por medio del cual se establecen los criterios básicos para creación, conformación, organización, control y consulta de los expedientes de archivo y se dictan otras disposiciones" class="hover_pointer">
					1. Acuerdo 002 de 2014 (Se establecen criterios para creación, conformación, organización, control y consulta de expedientes de archivo). 
				</td>
				<td width="20%" id="p4" onclick="carga_contenido_param('4')" title="Por el cual se establecen lineamientos generales para las entidades del Estado en cuanto a la gestión de documentos electrónicos generados como resultado del uso de medios electrónicos de conformidad con lo establecido en el capítulo IV de la Ley 1437 de 2011, se reglamenta el artículo 21 de la Ley 594 de 2000 y el capítulo IV del Decreto 2609 de 2012" class="hover_pointer">
					2. Acuerdo 003 de 2015 (Se establecen lineamientos en cuanto a la gestión de documentos electrónicos).
				</td>
				<td width="20%" id="p2" onclick="carga_contenido_param('23')"
				title="Por el cual se reglamenta el procedimiento para la elaboración, aprobación, evaluación y convalidación, implementación, publicación e inscripción en el Registro único de Series Documentales – RUSD de las Tablas de Retención Documental – TRD y Tablas de Valoración Documental – TVD" class="hover_pointer">
					3. Acuerdo 004 de 2019 (Instructivo procedimiento para la elaboración, aprobación, evaluación y convalidación, implementación de <b>TRD</b> y <b>TVD</b>)
				</td>
				<td width="20%" id="p2" onclick="carga_contenido_param('11')"
				title="Acuerdo 038 de 2002 (Instructivo Formato Único de Inventario Documental FUID)" class="hover_pointer">
					4. Acuerdo 038 de 2002 (Instructivo Formato Único de Inventario Documental <b>FUID</b>)
				</td>
				<td width="20%" id="p1"  onclick="carga_contenido_param('2')" 
				title="Según el Archivo General de la Nación (AGN), el inventario documental constituye  un instrumento archivístico de recuperación de información que describe de manera exacta y precisa las series o asuntos de un fondo documental. AGN (2014). El inventario documental, permite controlar la producción documental, existencia física y transferencias en cada fase del archivo (Gestión, Central e Histórico) para que en el evento de presentarse novedades administrativas como traslados, retiros, fusión de dependencias o supresión de la entidad, se cuente con la documentación debidamente organizada e inventariada facilitando así las entregas oportunas. Esto se hace a través del Formulario Único de Inventario Documental (FUID) conforme al Acuerdo 042 del AGN donde se contemplan series, subseries, fechas extremas, unidades de conservación, frecuencia de la consulta etc. Para el caso de consultas y préstamos documentales consultar artículo 5 y 6."
				class='hover_pointer'>
					5. Acuerdo 042 de 2002 (Formato Unico de Inventario Documental <b>FUID</b>, consultas y préstamos documentales).
				</td> 
			</tr>
			<tr>	
				<td id="p4" onclick="carga_contenido_param('6')" title="Por medio del cual se establecen pautas para la administración de las comuniaciones oficiales en las entidades públicas y las privadas que cumplen funciones públicas"class='hover_pointer'>
					6. Acuerdo 060 de 2001 (Se establecen pautas para la administración de las comunicaciones oficiales en las entidades públicas y las privadas que cumplen funciones públicas)
				</td>
				<td id="p2" onclick="carga_contenido_param('24')" title="Con el fin de facilitar que cada entidad establezca su sistema de clasificación documental, en este texto se desarrollan los fundamentos basados en la aplicación de los principios archivísticos que los rigen. Asi mismo ,se señalan los pasos metodológicos para llevar a cabo el proceso de la clasificación,con miras a la recuperación de la estructura orgánica de la entidad." class="hover_pointer">
					7. Cartilla Clasificacion Documental AGN (Instructivo para Cuadros de Clasificación Documental. Establecer un sistema de clasificación coherente con los principios archivisticos.)
				</td>
				<td id="p2" onclick="carga_contenido_param('14')" title="Constitución Política de Colombia" class="hover_pointer">
					8. Constitución Política de Colombia
				</td>
				<td id="p2" onclick="carga_contenido_param('16')" title="Por el cual se reglamenta el Decreto 2591 de 1991. (Accion de Tutela)" class="hover_pointer">
					9. Decreto 306 de 1992 (Por el cual se reglamenta el Decreto 2591 de 1991. [Accion de Tutela])
				</td>
				<td id="p2" onclick="carga_contenido_param('17')"
				title="Por el cual establecen reglas para el reparto de la acción de tutela" class="hover_pointer">
					10. Decreto 1382 de 2000 (Reparto de la accion de tutela)
				</td>
			</tr>
			<tr>
				<td id="p2" onclick="carga_contenido_param('20')" 
				title="Por medio del cual se reglamenta el artículo 7° de la Ley 527 de 1999, sobre la firma electrónica y se dictan otras disposiciones." class="hover_pointer">
					11. Decreto 2364 de 2012 (Por medio del cual se reglamenta el artículo 7° de la Ley 527 de 1999, sobre la firma electrónica y se dictan otras disposiciones.)
				</td>
				<td id="p2" onclick="carga_contenido_param('15')"
				title="Por el cual se reglamenta la acción de tutela consagrada en el artículo 86 de la Constitución Política." class="hover_pointer">
					12. Decreto 2591 de 1991 (Por el cual se reglamenta la acción de tutela consagrada en el artículo 86 de la Constitución Política)
				</td>
				<td id="p5" onclick="carga_contenido_param('8')"
				title="Directiva presidencial 04 de 2012 - Eficiencia Administrativa y lienamientos de la política cero papel en la administración pública" class="hover_pointer">
					13. Directiva presidencial 04 de 2012 - Eficiencia Administrativa y lienamientos de la política cero papel en la administración pública 
				</td>
				<td id="p4" onclick="carga_contenido_param('9')"
				title="Guia No.1 cero papel en la administración pública" class="hover_pointer">
					14. Guia No.1 cero papel en la administración pública (Ministerio de Tecnologías de la Información y las Comunicaciones. MINTIC)
				</td>
				<td id="p2" onclick="carga_contenido_param('18')" 
				title="Por medio de la cual se define y reglamenta el acceso y uso de los mensajes de datos, del comercio electrónico y de las firmas digitales, y se establecen las entidades de certificación y se dictan otras disposiciones." class="hover_pointer">
					15. Ley 527 de 1999 (Se define y reglamenta el acceso y uso de los mensajes de datos, del comercio electrónico y de las firmas digitales, y se establecen las entidades de certificación)
				</td>	
			</tr>
			<tr>
				<td id="p2" onclick="carga_contenido_param('1')"
				title="Ley general de archivos" class="hover_pointer">
					16. Ley 594 de 2000 (Ley General de Archivos)
				</td>	
				<td id="p2" onclick="carga_contenido_param('12')"
				title="Por la cual se expide el Código de Procedimiento Administrativo y de lo Contencioso Administrativo" class="hover_pointer">
					17. Ley 1437 de 2011 (Código de Procedimiento Administrativo y de lo Contencioso Administrativo)
				</td>	
				<td id="p2" onclick="carga_contenido_param('7')"
				title="Ley 1712 de 2014 (Ley de transparencia de accesos a la informacion)" class="hover_pointer">
					18. Ley 1712 de 2014 (Ley de transparencia de accesos a la informacion)
				</td>			
				<td onclick="carga_contenido_param('21')"
				title="Ley 1755 de 2015  (Se regula el Derecho Fundamental de Petición)" class="hover_pointer">
					19. Ley 1755 de 2015 (Se regula el Derecho Fundamental de Petición)
				</td>
				<td onclick="carga_contenido_param('22')"
				title="Modelo de Requisitos para la implementación de un Sistema de Gestión de Documentos Electrónicos (MOREQ AGN)" class="hover_pointer">
					20. Modelo de Requisitos para la implementación de un Sistema de Gestión de Documentos Electrónicos (<b>MOREQ AGN</b>)
				</td>
			</tr>
		</table>
	</center>
	<center id="normatividad_entidad" <?php echo $estilo_entidad ?>><input type="hidden" name="id_cambio_org_f" id="id_cambio_org_f" <?php echo "value='$id_cambio_organico_funcional'" ?>>
		<?php echo "$tabla_paso1 <br>$paso1"?>
		<!-- <table border="0" width="100%">
			<tr>
				<td class="hover_pointer">
				</td>
			</tr>
		</table>
 -->
	</center>
	<br>
	<div id="contenido_param"></div>
<!-- Hasta aqui el div que tiene el formulario principal -->
<!--Desde aqui el div que contiene el formulario para agregar cambio organico-funcional -->
	<div id="ventana_formulario_nuevo_cambio_of" class="ventana_modal">
		<div class="form">
			<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
			<h1>Formulario Agregar Nuevo Cambio Organico-Funcional</h1>
			<hr>
			<form method="post" id="formulario" enctype="multipart/form-data" autocomplete="off">
				<input type="hidden" name="formulario_nuevo_cambio_of" id="formulario_nuevo_cambio_of" value="crear_cambio_of">
				<!-- <input type="hidden" name="MAX_FILE_SIZE" value="3000000" /> -->
				<table class='tabla_datos_usuario'>
					<tr>
						<td class="descripcion" width="10%">
							Fecha Inicial del Organigrama:
						</td>
						<td id="info_agregar_nuevo_cambio" class="detalle"  width="90%">
							<input type="date" name="fecha_inicial_of" id="fecha_inicial_of" onchange="verifica_fecha_valida('crear_cambio_of')">
							<div id="fecha_inicial_vacio" class="errores info_nuevo_cambio">El campo de fecha no puede estar vacío. Verifique por favor.</div>
							<div id="fecha_inicial_mayor" class="errores info_nuevo_cambio">La fecha inicial no puede ser mayor a la fecha final. Verifique por favor.</div>
							<div id="fecha_inicial_error" class="errores info_nuevo_cambio"></div>
						</td>
						<td rowspan="3">
							<iframe id="viewer" frameborder="0" scrolling="yes" width="100%" height="150px"></iframe>
						</td>
					</tr>			
					<tr>
						<td class="descripcion" width="10%">
							Fecha Final del Organigrama:
						</td>
						<td class="detalle"  colspan="3">
							<input type="date" class="info_nuevo_cambio" name="fecha_final_of" id="fecha_final_of" onchange="verifica_fecha_valida('crear_cambio_of')">
						</td>
					</tr>	
					<tr>
						<td class="descripcion" width="10%">
							Acto Administrativo :
						</td>
						<td class="detalle info_nuevo_cambio"  colspan="3">
							<input type="file" name="acto_administrativo_of" id="acto_administrativo_of" onchange="validar_input_file_animado('acto_administrativo_of', 'viewer', 'info_agregar_nuevo_cambio'); mitad_info_agregar_nuevo_cambio(); ">

							<div id='acto_administrativo_of_tamano' class='errores info_nuevo_cambio'>El PDF que intenta cargar excede el limite permitido. Se puede cargar hasta 8Mb y usted intenta cargar un archivo con tamaño de <b><span id='acto_administrativo_of_tamano_actual_pdf'></span> Mb</b>. Verifique por favor</div>
							<div id="acto_administrativo_of_invalido" class="errores info_nuevo_cambio">El archivo que intenta cargar no es un PDF. Verifique por favor</div>
						</td>
					</tr>		
				</table>
				<div id='boton_crear_cambio_of' class="center">
					<input type="button" class="botones" value="Cambio Organico-Funcional" onclick="submit_agregar_cambio_of()">
				</div>
			</form>
		</div>
	</div>			
<!--Hasta aqui el div que contiene el formulario para agregar cambio organico-funcional -->
<!--Desde aqui el div que contiene el formulario para modificar cambio organico-funcional -->
	<div id="ventana_formulario_modificar_cambio_of" class="ventana_modal">
		<div class="form">
			<div class="cerrar"><a href='javascript:cerrar_ventanas_modal();'>Cerrar X</a></div>
			<h1>Formulario Modificar Cambio Organico-Funcional</h1>
			<hr>
			<form method="post" id="formulario" enctype="multipart/form-data" autocomplete="off">
				<input type="hidden" name="formulario_modificar_cambio_of" id="formulario_modificar_cambio_of" value="modificar_cambio_of">
				<!-- <input type="hidden" name="MAX_FILE_SIZE" value="3000000" /> -->
				<table class='tabla_datos_usuario'>
					<tr>
						<td class="descripcion">
							Id del Cambio Organico-Funcional
						</td>
						<td class="detalle">
							<input type="text" id="mod_id_cambio_of" name="mod_id_cambio_of" disabled="disabled">
						</td>
					</tr>
					<tr>
						<td class="descripcion" width="10%">
							Fecha Inicial del Organigrama:
						</td>
						<td id="info_modificar_nuevo_cambio" class="detalle"  width="90%">
							<input type="date" name="mod_fecha_inicial_of" id="mod_fecha_inicial_of" onchange="verifica_fecha_valida('modificar_cambio_of')">
							<div id="mod_fecha_inicial_vacio" class="errores info_nuevo_cambio">El campo de fecha no puede estar vacío. Verifique por favor.</div>
							<div id="mod_fecha_inicial_mayor" class="errores info_nuevo_cambio">La fecha inicial no puede ser mayor a la fecha final. Verifique por favor.</div>
							<div id="mod_fecha_inicial_error" class="errores info_nuevo_cambio"></div>

						</td>
						<td rowspan="3">
							<iframe id="viewer2" frameborder="0" scrolling="yes" width="100%" height="150px"></iframe>
						</td>
					</tr>			
					<tr>
						<td class="descripcion" width="10%">
							Fecha Final del Organigrama:
						</td>
						<td class="detalle"  colspan="3">
							<input type="date" class="info_nuevo_cambio" name="mod_fecha_final_of" id="mod_fecha_final_of" onchange="verifica_fecha_valida('modificar_cambio_of')">
						</td>
					</tr>	
					<tr>
						<td class="descripcion" width="10%">
							Acto Administrativo :
						</td>
						<td class="detalle info_nuevo_cambio"  colspan="3">
							<input type="file" name="mod_acto_administrativo_of" id="mod_acto_administrativo_of" onchange="validar_input_file_animado('mod_acto_administrativo_of', 'viewer2', 'info_modificar_nuevo_cambio'); mitad_info_modificar_cambio(); ">

							<div id='mod_acto_administrativo_of_tamano' class='errores info_nuevo_cambio'>El PDF que intenta cargar excede el limite permitido. Se puede cargar hasta 8Mb y usted intenta cargar un archivo con tamaño de <b><span id='mod_acto_administrativo_of_tamano_actual_pdf'></span> Mb</b>. Verifique por favor</div>
							<div id="mod_acto_administrativo_of_invalido" class="errores info_nuevo_cambio">El archivo que intenta cargar no es un PDF. Verifique por favor</div>
						</td>
					</tr>		
				</table>
				<div id='boton_modificar_cambio_of' class="center">
					<input type="button" class="botones" value="Cambio Organico-Funcional" onclick="submit_modificar_cambio_of()">
				</div>
			</form>
		</div>
	</div>			
<!--Hasta aqui el div que contiene el formulario para modificar cambio organico-funcional -->					

</body>
</html>		