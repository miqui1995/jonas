<?php 
/* En este archivo se buscan para mostrar los pendientes cualquiera de las radicaciones . */
/* Se inicia con validar inactividad */
	require_once("../login/validar_inactividad.php");

	$tipo_consulta = $_POST['tipo_consulta'];

	switch ($tipo_consulta) {
		case 'pendientes_aprobar':
			$query_pendientes_aprobar = "select distinct(v.numero_radicado), r.asunto from version_documentos v join radicado r on v.numero_radicado=r.numero_radicado where v.aprobado = 'NO' and trim(upper(v.usuario_que_aprueba)) = trim(upper('$nombre')) and html_asunto !=''";
			$result_query_pendientes_aprobar = pg_query($conectado,$query_pendientes_aprobar); 

		    if($result_query_pendientes_aprobar==true){
		    	echo "<h1 style='text-align: center'>Documentos pendientes por aprobar con firma electrónica </h1>";

		    	$registros_pendientes_aprobar= pg_num_rows($result_query_pendientes_aprobar);

				$num_fila=0;
		    	for ($i=0;$i<$registros_pendientes_aprobar;$i++){
		    		$linea_alerta_pendientes_aprobar   	= pg_fetch_array($result_query_pendientes_aprobar);
			        
			        $numero_radicado	= $linea_alerta_pendientes_aprobar['numero_radicado'];
			        $asunto    			= $linea_alerta_pendientes_aprobar['asunto'];

			        $verifica_tipo_radicado = substr($numero_radicado,-1); // Extrae el ultimo digito del numero_radicado directamente para determinar si es una entrada (1), salida(2), inventario(3), interno(4) o resoluciones(5)

			        /* verifica tipo de radicacion del documento */	
			        switch ($verifica_tipo_radicado) {
		        		case '2':
							$boton_carga_radicacion = "carga_radicacion_salida('$numero_radicado')";
		        			break;

		        		case '5':
							$boton_carga_radicacion = "carga_radicacion_resoluciones('$numero_radicado')";
		        			break;
		        	}	
					
					echo"<div title='Cargar este radicado para aprobar con firma electrónica' class='art ";
						if ($num_fila%2==0) echo " fila2"; //si el resto de la división es 0 pongo un color
							else echo " fila1"; //si el resto de la división NO es 0 pongo otro color 
						echo "' onclick=\"javascript:$boton_carga_radicacion\">
							<b>$numero_radicado</b><br><b> Asunto:</b> ($asunto)";
						echo "</div>";//cierra div class='sugerencia_contacto'(art)
				
					$num_fila++;		
				}

			   if($pendientes_aprobar!=0){
			        $mostar_alertas.= "<li onclick =\"carga_pendientes_aprobar()\"><a href=\"#\"><span><img src=\"imagenes/iconos/firmar_documento.png\" style=\"width:18px;\"> Tiene <font color=red> $pendientes_aprobar </font>documentos por aprobar con firma electrónica.<br><b>(Modulo Firma Electrónica)</b></span></a></li>";
		        }
		    }else{
		        $pendientes_aprobar=0;
		    }
			break;
	}
 ?>