<?php
	if(!isset($_SESSION)){
	    session_start();
	}
	require_once("../login/validar_inactividad.php");
?>
<style>
	#mostar_mensaje{
		overflow-y: scroll;
		background-color: #C4C4C49C;
		top:10%;
		left:15%;
		height:425px;
	}
	#pos_ventana{
		left:3% !important;
		padding: 15px !important;
		height: 445px !important;
		right: 3% !important;
		top: 3% !important;
		width: auto !important;
	}
	#pos_ventana_pdf{
		left:3% !important;
		padding: 25px !important;
		height: 445px !important;
		right: 3% !important;
		top: 3% !important;
		width: auto !important;
	}
	.mostar_pdf{
		width: 50%;
	}
	.asunto{
		cursor:pointer;
		width: 45%;
	}
	.art_exp{
		float:left !important;
		cursor:pointer !important;
	}
	.anexo{
		float:left;
		height:20px;
	}
</style>
<?php
	echo "<input type='text' id='usuario_actual' name='usuario_actual' value='".$_SESSION['login']."' class='hidden'>";
?>
<div id="crear_pdf" class="hidden"></div>
<div id="ventana" class="ventana_modal">
	<div id="pos_ventana" class="form">
		<div class="cerrar">
			<a href='javascript:cerrarVentana();'>Cerrar X</a>
		</div>
		<table id="tabla_buzon_correo">
			<tr>
				<td class="detalle" style="width: 50%;">
					<div id='mostar_mensaje'></div>
				</td>
				<td class="detalle hidden mostar_pdf">
					<div id='mostar_pdf'></div>
				</td>
			</tr>
		</table>	
	</div>
</div>
<div id="ventana_pdf" class="ventana_modal">
	<div id="pos_ventana_pdf" class="form">
		<div class="cerrar">
			<a href='javascript:cerrarVentana();'>Cerrar X</a>
		</div>
	</div>
</div>
<div id='contenedor_buzon_correo'>
	<div class='ventana'>
		<div id="cabecera_correo"></div>
		<hr>
		<table id='myTable' border='0' class='center display formulario_filtros_reporte2' width='100%'>
			<tr id="desbloquear" class="hidden">
				<td class='descripcion'>Asunto del Mail</td>
				<td class='descripcion'>Remitente del Mail</td>
				<td class='descripcion'>Archivos anexos</td>
				<td class='descripcion'>Fecha de Envio</td>
				<td></td>
			</tr>
			<tbody id="informacion_imap"></tbody>
			<tbody id="busqueda_avanzada" class="hidden">
				<td id="busqueda_avanzada_boton" class='detalle' colspan="6">
				</td>
			</tbody>
		</table>
	</div>
</div>
<div id="pre_correo_contraseña"></div>
<script>
//prueba_1234567890
var parte1;
var parte2;
var parte3;
var parte4;
var usuario_actual = $("#usuario_actual").val();
function verificar_usuario(){
	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',//Destino
		data: {
			'recibe_ajax'   	 : 'buzon_correo_electronico_verificar_usuario'
		},
		success: function(correo){// Retorna {cadena} Json codificado con la informacion del proceso imap
			var correo_decodificado = JSON.parse(correo);// Decodifica el json
			parte2 = correo_decodificado.correo_usuario;
			parte4 = correo_decodificado.usuario;
			console.log(correo_decodificado);

			var estructura_contraseña = "<center><h1>Ingrese la contraseña del correo electrónico</h1><h2>"+correo_decodificado.correo_usuario+"</h2><table width='50%'><tr><td><center><input type='password' id='contraseña_unica' placeholder='Ingrese password del correo "+correo_decodificado.correo_usuario+"' style='border-radius: 8px; padding: 6px; width: 80%;'>";
			estructura_contraseña += "</center></td></tr><tr><td><center>";
			estructura_contraseña += "<input type='button' value='Ingresar al correo' class='botones' onclick='pre_cargar_correo()'>";
			estructura_contraseña += "</td></tr></table></center>";

			$("#pre_correo_contraseña").html(estructura_contraseña);
		}
	})
}
function pre_cargar_correo(){
	var contraseña = $("#contraseña_unica").val();
	parte2 = parte2.toLowerCase();
	if(parte2.includes("gmail")){
		parte1 = "{imap.gmail.com:993/imap/ssl}INBOX";
		parte3 = contraseña;
		BuzonCorreoPrimeraVista();	//se Invoca la funcion para que ingrese cuando se cargue la pagina por primera vez
	}else if(parte2.includes("outlook") || parte2.includes("outlook")){
		parte1 = "{outlook.office365.com:993/imap/ssl}INBOX";
		parte3 = contraseña;
		BuzonCorreoPrimeraVista();	//se Invoca la funcion para que ingrese cuando se cargue la pagina por primera vez
	}else{
		$('#informacion_imap').html("El servicio de correo electronico asociado al correo "+parte2+"");
	}
	$("#pre_correo_contraseña").slideUp("slow");
	console.log(parte1, parte2, parte3, parte4)
}
verificar_usuario();
var reverse = [];
var informacionimap_vista_rapida;
var informacionimap_vista_avanzada;
var objeto_hilos = [];
var anexo_columna;
var asunto_columna_p1;
var asunto_columna_p2;
var contador_organizacion_primera_columna_vista_avanzada = 0;
var cuerpo_vista_avanzada;
var contador_anexos_modal = 0;
var mensaje_codificado_radicar;
var informacion_pdf_radicar;
///////////////////////////////////////////
		// CARGA PRIMARIA Y SECUNDARIA DE LOS MENSAJES DEL CORREO ELECTRONICO
/*****************************************/
/*****************************************************************************************
	Function BuzonCorreoPrimeraVista() Creara la tabla de vizualizacion de los mensajes del correo electronico
/*****************************************************************************************
	* Iniciara un proceso $.ajax donde recibira un json codificado o un mensaje de error, si trae un json codificado empezara la construccion de la tabla
	* @return Tabla estructurada del buzon del correo electronico que se escribe mediante peticion .html al id "informacion_imap"
*****************************************************************************************/
function BuzonCorreoPrimeraVista(){
	var extension_nueva = "";
	/* Guardar archivos anexos */
		$.ajax({
			type: 'POST',
			url:  'include/procesar_ajax.php',//Destino
			data: {
				'recibe_ajax'   	 : 'buzon_correo_electronico_archivos_anexos',
				'limite'     		 : 3,
				'parte1' 			 : parte1,
				'parte2' 			 : parte2,
				'parte3' 			 : parte3
			},
			success: function(cargar){// Retorna {cadena} Json codificado con la informacion del proceso imap
				console.log(""+cargar+"");
			}
		})
	/* Fin guardar archivos anexos */
	loading('informacion_imap');//Invoca pantalla de carga para para que el usuario no visualice la construccion de la estrcutura
	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',//Destino
		data: {
			'recibe_ajax'		 : 'buzon_correo_electronico_informacion_correo_electronico',
			'limite'			 : 3,
			'parte1' 			 : parte1,
			'parte2' 			 : parte2,
			'parte3' 			 : parte3,
			'invertido'			 : 1
		},
		success: function(json){// Retorna una cadena de error o un Json codificado con la informacion del proceso imap
			var cuerpo_tabla;
			if(json === "error_connect" || json === null || json === undefined){// Valida posibles errores
				cuerpo_tabla = "Error";
				$('#informacion_imap').html("");// Estrcutura html
				$('#pre_correo_contraseña').html("<div class='errores' style='display:block'><p>Error De Validacion ERROR('"+json+"')</div>"); 
				$('#pre_correo_contraseña').show();// Muestra la seccion
			}else{
				var contador_organizacion_primera_columna = 0;
				informacionimap_vista_rapida = JSON.parse(json);// Decodifica el json
				$.each(informacionimap_vista_rapida.data, function(i, a) {// Por cada mensaje extraido del buzon se genera el bucle
					var anexos_columna = '';// Reinicio de valores
					if(!a.subject.includes('Re:')){// Si el asunto del mensaje trae Re: es clasificado como parte de un hilo
						asunto_columna_p1 = '<td class="detalle asunto" onclick="VentanaModalP1(\''+i+'\',\''+a.subject+'\',1)" title="'+a.subject+'">';
						asunto_columna_p2 = '<p>'+a.subject+'</p></td>';
						if(informacionimap_vista_rapida.data[i].attachments.length > 0){// Si el mensaje tiene archivos anexos
							$.each(informacionimap_vista_rapida.data[i].attachments, function(c, d) {// Por cada archivo anexo se genera el bucle
								var desfragmentos = d.file.split(".");
								$.each(desfragmentos, function(e, f) {
									if(desfragmentos[desfragmentos.length - 1] != "pdf" && desfragmentos[desfragmentos.length - 1] != "PDF"){
										if((desfragmentos.length - 1) != e){
											extension_nueva += f;
										}else{
											extension_nueva += ".pdf";
										}
									}else{
										extension_nueva = d.file;
									}
								});
								d.file = extension_nueva;
								extension_nueva = "";
								anexos_columna += '<div class="art_exp" onclick="AbrirVistaPreviaPdf(\''+d.file+'\')" title="'+d.file+'">';
								anexos_columna += '<img class="anexo" src="imagenes/iconos/archivo_pdf.png" title="'+d.file+'">';
								anexos_columna += acortar_cadena(d.file, 7, 4);
								anexos_columna += '</div>';
							});
						}else{
							anexos_columna = "Ninguno";
						}
						cuerpo_tabla += '<tr>';
						cuerpo_tabla += asunto_columna_p1+asunto_columna_p2;
						cuerpo_tabla += '<td class="detalle" style="width:10%">'+a.from.name+'<br>('+a.from.address+')'+'</td>';
						cuerpo_tabla += '<td class="detalle" style="width:20%">'+anexos_columna+'</td>';
						cuerpo_tabla += '<td class="detalle">'+a.date+'</td>';
						cuerpo_tabla += '<td class="detalle">';
						cuerpo_tabla += '<button class="botones2" title="RADICAR" onclick="RadicarCorreo(\''+i+'\', \''+a.subject+'\',1)">';
						cuerpo_tabla += '<center><b>RADICAR</b></center>';
						cuerpo_tabla += '</button></td>';
						cuerpo_tabla += '</tr>';
					}
					contador_organizacion_primera_columna++;

				});
				$('#informacion_imap').html(cuerpo_tabla);// Estrcutura html
				$('#busqueda_avanzada').slideDown("slow");// Muestra la seccion
				$('#desbloquear').slideDown("slow");// Muestra la seccion
			}
		}
	})
	BuzonCorreoSegundaVista();
};
/*****************************************************************************************
	Fin function BuzonCorreoPrimeraVista() Creara la tabla de vizualizacion de los mensajes del correo electronico
/*****************************************************************************************/
/*****************************************************************************************
	Function BuzonCorreoSegundaVista() Creara la tabla de vizualizacion de los mensajes del correo electronico asincronicamente
/*****************************************************************************************
	* Iniciara un proceso $.ajax donde recibira un json codificado o un mensaje de error, si trae un json codificado empezara la construccion de la tabla
	* @return Tabla estructurada del buzon del correo electronico que se escribe mediante peticion .html al id "informacion_imap"
*****************************************************************************************/
function BuzonCorreoSegundaVista(){
	var extension_nueva = "";
	/* Guardar archivos anexos */
	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',//Destino
		data: {
			'recibe_ajax'        : 'buzon_correo_electronico_archivos_anexos',
			'parte1' 			 : parte1,
			'parte2' 			 : parte2,
			'parte3' 			 : parte3
		},
		success: function(cargar){// Retorna {cadena} Json codificado con la informacion del proceso imap
			console.log(""+cargar+"");
		}
	})
	/* Fin guardar archivos anexos */
	var conta = 0;
	loading('busqueda_avanzada_boton');//Invoca pantalla de carga para para que el usuario no visualice la construccion de la estrcutura
	$.ajax({
		type: 'POST',
		url:  'include/procesar_ajax.php',//Destino
		data: {
			'recibe_ajax'  	 				: 'buzon_correo_electronico_informacion_correo_electronico',
			'parte1' 			 : parte1,
			'parte2' 			 : parte2,
			'parte3' 			 : parte3
		},
		success: function(json){// Retorna {cadena} Json codificado con la informacion del proceso imap
			var cuerpo_tabla;
			if(json === "error_connect" || json === null || json === undefined){// Valida posibles errores
				cuerpo_tabla = "Error"+json;
			}else{
				var contador_organizacion_primera_columna = 0;
				informacionimap_vista_avanzada = JSON.parse(json);// Decodifica el json
				$.each(informacionimap_vista_avanzada.data, function(i, a) {// Por cada mensaje extraido del buzon se genera el bucle
					var anexos_columna = '';
					if(a.subject.includes('Re:')){// Si el asunto del mensaje trae Re: es clasificado como parte de un hilo
						if(!filtro1_mensaje_re(a.subject, i)){// Camino 1: Mensaje en hilos
							cuerpo_tabla = "Error traduccion de hilos";
						}
					}else{
						contador_organizacion_primera_columna_vista_avanzada++;
						var hilo_filtro2 = admin_hilos(a.subject);// Valida que el asunto no este arraigado con una cadena de mensjaes en hilos
						anexo_columna = '';
						if(hilo_filtro2 != undefined || hilo_filtro2 != null){// Valida que el mensaje no tenga arraigados mensajes en cadenas de hilos
							filtro2_mensaje_hilo(hilo_filtro2, i);// Camino 2: Mensaje en hilos
						}else{
							filtro3_mensaje_normal(a.subject, i);// Camino 3: Mensaje normales
						}
						cuerpo_tabla  += '<tr>';
						cuerpo_tabla  += '<td class="detalle asunto" '+asunto_columna_p1+'>'+asunto_columna_p2+'</td>';
						cuerpo_tabla  += '<td class="detalle" style="width:10%">'+a.from.name+'<br>('+a.from.address+')'+'</td>';
						cuerpo_tabla  += '<td class="detalle" style="width:20%">'+anexo_columna+'</td>';
						cuerpo_tabla  += '<td class="detalle">'+a.date+'</td>';
						cuerpo_tabla  += '<td class="detalle">';
						cuerpo_tabla  += '<button class="botones2" title="RADICAR" onclick="RadicarCorreo(\''+i+'\', \''+a.subject+'\',0)">';
						cuerpo_tabla  += '<center><b>RADICAR</b></center>';
						cuerpo_tabla  += '</button></td>';
						cuerpo_tabla  += '</tr>';
						reverse[conta] = cuerpo_tabla;
						conta++;
						cuerpo_tabla   = "";
					}
				});
			}
			cuerpo_vista_avanzada = "";
			reverse = reverse.reverse();
			$.each(reverse, function(i, a) {// Por cada mensaje extraido del buzon se genera el bucle
				cuerpo_vista_avanzada += a;
			});
			$("#busqueda_avanzada_boton").html('<button class="botones" onclick="mostrar_busqueda_avanzada()" title="RADICAR">BÚSQUEDA AVANZADA</button>');// Estrcutura html
		}
	})
}
/*****************************************************************************************
	Fin function BuzonCorreoSegundaVista() Creara la tabla de vizualizacion de los mensajes del correo electronico asincronicamente
/*****************************************************************************************/
/*****************************************************************************************
	Function filtro1_mensaje_re() Trata los mensajes clasficados como hilos
/*****************************************************************************************
	* Recopila y guarda la informacion del mensaje en objeto_hilos
	* @param {string} (asunto) Es obligatoria, asunto funciona como id en objeto_hilos
	* @param {string} (id) Es obligatoria, nos dice la porsocion del mensaje en  var informacionimap_vista_avanzada
	* @return true si la funcion se ejecuto bien
*****************************************************************************************/
function filtro1_mensaje_re(asunto, id){
	var extension_nueva = "";
	var asunto_hilo_cadenacortada = asunto.replace('Re: ', '');// Quitamos el "Re: " del asunto
	var ubicacion_objeto_hilos    = admin_hilos(asunto_hilo_cadenacortada);// Validamos si ya existe el hilo
	if(ubicacion_objeto_hilos   === undefined || ubicacion_objeto_hilos === null){// Si "ubicacion_objeto_hilos" esta vacia
		objeto_hilos.push({
			asunto_hilo_nombre : asunto_hilo_cadenacortada,
			id_hilos           : [],
			anexo_hilos        : []
		});// Se crea el registro en "objeto_hilos" Objeto multidimensional
		ubicacion_objeto_hilos    = objeto_hilos.length-1;// Se considera el 0 como posicion 1
	}
	objeto_hilos[ubicacion_objeto_hilos].id_hilos.push(id);// Asociacion del Id
	if(informacionimap_vista_avanzada.data[id].attachments.length > 0){// Si el mensaje trae archivo anexos
		$.each(informacionimap_vista_avanzada.data[id].attachments, function(c, d) {// Por cada archivo anexo se repite
			var desfragmentos = d.file.split(".");
			$.each(desfragmentos, function(e, f) {
				if(desfragmentos[desfragmentos.length - 1] != "pdf" && desfragmentos[desfragmentos.length - 1] != "PDF"){
					if((desfragmentos.length - 1) != e){
						extension_nueva += f;
					}else{
						extension_nueva += ".pdf";
					}
				}else{
					extension_nueva = d.file;
				}
			});
			d.file = extension_nueva;
			extension_nueva = "";
			objeto_hilos[ubicacion_objeto_hilos].anexo_hilos.push(d.file);// Asociacion anexos del mensaje actual a "objeto_hilos"
		});
	}
	return true;
}
/*****************************************************************************************
	Fin function mensaje_hilo()
/*****************************************************************************************/
/*****************************************************************************************
	Function filtro2_mensaje_hilo()
/*****************************************************************************************
	* Recopila y guarda la informacion del mensaje en objeto_hilos
	* @param {string} (ubicacion_hilos) Es obligatoria, es la posicion que ocupa en "objeto_hilos"
	* @param {string} (ubicacion_hilos) Es obligatoria, nos dara la posicion del mensaje en var informacionimap_vista_avanzada
	* @return json_informacion con valor de true si json no contiene error 
*****************************************************************************************/
function filtro2_mensaje_hilo(ubicacion_hilos, id){
	var extension_nueva = "";
	objeto_hilos[ubicacion_hilos].id_hilos.push(id);// Asociacion del Id en "objeto_hilos"
	if(informacionimap_vista_avanzada.data[id].attachments.length > 0){// Si el mensaje trae archivo anexos
		$.each(informacionimap_vista_avanzada.data[id].attachments, function(c, d) {// Por cada archivo anexo se repite
			var desfragmentos = d.file.split(".");
			$.each(desfragmentos, function(e, f) {
				if(desfragmentos[desfragmentos.length - 1] != "pdf" && desfragmentos[desfragmentos.length - 1] != "PDF"){
					if((desfragmentos.length - 1) != e){
						extension_nueva += f;
					}else{
						extension_nueva += ".pdf";
					}
				}else{
					extension_nueva = d.file;
				}
			});
			d.file = extension_nueva;
			extension_nueva = "";
			objeto_hilos[ubicacion_hilos].anexo_hilos.push(d.file);// Asociacion la informacion de los anexos a "objeto_hilos"
		});
	}
	if(objeto_hilos[ubicacion_hilos].anexo_hilos.length > 0){// Si el mensaje tiene archivos anexos alamacenados
		
		$.each(objeto_hilos[ubicacion_hilos].anexo_hilos, function(c, d) {
			var desfragmentos = d.split(".");
			$.each(desfragmentos, function(e, f) {
				if(desfragmentos[desfragmentos.length - 1] != "pdf" && desfragmentos[desfragmentos.length - 1] != "PDF"){
					if((desfragmentos.length - 1) != e){
						extension_nueva += f;
					}else{
						extension_nueva += ".pdf";
					}
				}else{
					extension_nueva = d;
				}
			});
			d = extension_nueva;
			extension_nueva = "";
			anexo_columna += '<div class="art_exp anexos_columna" onclick="AbrirVistaPreviaPdf(\''+d+'\')">';
			anexo_columna += '<img class="anexo" src="imagenes/iconos/archivo_pdf.png" title="'+d+'">';
			anexo_columna += acortar_cadena(d, 7, 4)+'</div>';
		});
	}else{
		anexo_columna = 'Ninguno';
	}
	asunto_columna_p1 = 'onclick="VentanaModalP1(\''+id+'\',\''+informacionimap_vista_avanzada.data[id].subject+'\',0)" title="'+informacionimap_vista_avanzada.data[id].subject+'"';
	asunto_columna_p2 = '<p>'+informacionimap_vista_avanzada.data[id].subject+' - ( Hilos: '+objeto_hilos[ubicacion_hilos].id_hilos.length+'.)</p>';
}
/*****************************************************************************************
	Fin function filtro2_mensaje_hilo()
/*****************************************************************************************
/*****************************************************************************************
	Function filtro3_mensaje_normal()
/*****************************************************************************************
	* Estructura los archivos anexos y el asunto de un mensaje sin clasificacion de hilos
	* @param {string} (asunto) Es obligatoria, nos dice con que mensaje se esta trabajando
	* @param {string} (id) Es obligatoria, nos dara la posicion del mensaje en var informacionimap_vista_avanzada
	* @return json_informacion con valor de true si json no contiene error 
*****************************************************************************************/
function filtro3_mensaje_normal(asunto, id){
	var extension_nueva = "";
	if(informacionimap_vista_avanzada.data[id].attachments.length > 0){//Si tiene archivos anexos
		$.each(informacionimap_vista_avanzada.data[id].attachments, function(c, d) {// Por cada archivo anexo se repite
			var desfragmentos = d.file.split(".");
			$.each(desfragmentos, function(e, f) {
				if(desfragmentos[desfragmentos.length - 1] != "pdf" && desfragmentos[desfragmentos.length - 1] != "PDF"){
					if((desfragmentos.length - 1) != e){
						extension_nueva += f;
					}else{
						extension_nueva += ".pdf";
					}
				}else{
					extension_nueva = d.file;
				}
			});
			d.file = extension_nueva;
			extension_nueva = "";
			anexo_columna += '<div class="art_exp anexos_columna" onclick="AbrirVistaPreviaPdf(\''+d.file+'\')">';
			anexo_columna += '<img class="anexo" src="imagenes/iconos/archivo_pdf.png" title="'+d.file+'">';
			anexo_columna += acortar_cadena(d.file, 7, 4)+'</div>';
		});
	}else{
		anexo_columna = 'Ninguno';
	}
	asunto_columna_p1 = 'onclick="VentanaModalP1(\''+id+'\',\''+informacionimap_vista_avanzada.data[id].subject+'\',0)" title="'+informacionimap_vista_avanzada.data[id].subject+'"';
	asunto_columna_p2 = '<p>'+informacionimap_vista_avanzada.data[id].subject+'</p>';
}
/*****************************************************************************************
	Fin function filtro3_mensaje_normal()
/*****************************************************************************************/
///////////////////////////////////////////
		// FIN DE LA CARGA PRIMARIA Y SECUNDARIA DE LOS MENSAJES DEL CORREO ELECTRONICO
/*****************************************/
///////////////////////////////////////////
/*****************************************/
///////////////////////////////////////////
			// SECCIONES DE LAS VENTANAS MODAL 
/*****************************************/
///////////////////////////////////////////
		// SECCION DE VENTA MODAL DE VIZUALIZACION DEL MENSAJE COMPLETO
/*****************************************/
/*****************************************************************************************
	Function VentanaModalP1() nos mostrara la ventana modal con los datos del mensaje
/*****************************************************************************************/
/*****************************************************************************************
	* Estructura de la ventana modal de vizualizacion del mensaje completo
	* @param {string} (id) Es obligatoria, se traduce a la posicion del mensaje en json correspondiente
	* @param {string} (asunto) Es obligatoria, nos dara un pamorama mas amplio si cuento con mensjaes en hilos al filtrar asunto
	* @param {string} (vista_rapida) Es obligatoria, nos dira que json tomar
	* @return Ventana modal 
*****************************************************************************************/
function VentanaModalP1(id, asunto, vista_rapida){
	$(".mostar_pdf").hide();// Esconde el pdf en la ventana modal
	var cabeza_ventana_modal = '';
	var archivos_anexos_modal;
	var archivos_anexos = '';
	var numero_adjuntos_modal ='';
	if(vista_rapida == 1){
		json = informacionimap_vista_rapida.data;
	}else{
		json = informacionimap_vista_avanzada.data;
	}
	cabeza_ventana_modal += "<h2>"+json[id].subject+"</h2>";
	cabeza_ventana_modal += "<br><table><tr><td>";
	cabeza_ventana_modal += "<b>"+json[id].from.name+"</b> - (\""+json[id].from.address+"\")</td>";
	cabeza_ventana_modal += "<td align='right'>"+json[id].date+"</td>";
	cabeza_ventana_modal += "</tr></table><hr>";
	archivos_anexos_modal = admin_hilos(asunto);// Validamos si ya existe el hilo
	if(archivos_anexos_modal   === undefined || archivos_anexos_modal === null){// Si "ubicacion_objeto_hilos" esta vacia
		archivos_anexos = VentanaModalP1Camino2(id);
	}else{
		id = objeto_hilos[archivos_anexos_modal].id_hilos[0];//Tomamos el id del primer mensaje en hilo ("contiene todo el mensaje")
		archivos_anexos = VentanaModalP1Camino1(archivos_anexos_modal);
	}
	if(contador_anexos_modal > 0){// Si hay adjuntos en los mensajes se muestra el conteo
		numero_adjuntos_modal = "<br><br><hr><br>"+contador_anexos_modal+". archivos adjuntos<br><br>";
	}
	$('#mostar_mensaje').html(cabeza_ventana_modal+json[id].message+numero_adjuntos_modal+archivos_anexos);// Estructura html
	$("#ventana").slideDown("slow");// Muestra la ventana modal
	$("#contenido").css({'z-index': '100'});// Estilo modificado para sobreponer la ventana modal
	contador_anexos_modal = 0;//Reinicio
}
/*****************************************************************************************
	Fin function VentanaModalP1() nos mostrara la ventana modal con los datos del mensaje
/*****************************************************************************************/
/*****************************************************************************************
	Function VentanaModalP1Camino1() Construye la secion de archivos anexos al someterlo por filtros a mensajes clasificados como hilos
/*****************************************************************************************/
/*****************************************************************************************
	* Estructura los anexos que se vizualizaran en la ventana modal
	* @param {string} (poscion_hilos) Es obligatoria, se traduce a la posicion del mensaje en objeto_hilos
	* @return seccion de anexos con su vista
*****************************************************************************************/
function VentanaModalP1Camino1(poscion_hilos){
	var archivo_anexo_modal = '';
	var nombre_corto_anexos;
	var nombre_extra_corto_errores;
	if(objeto_hilos[poscion_hilos].anexo_hilos.length > 0){// Si el hilo en general tiene archivos anexos alamacenados
		$.each(objeto_hilos[poscion_hilos].anexo_hilos, function(a, b) {// Por cada archivo anexo en los mensajes de hilos se repite
			nombre_corto_anexos        = acortar_cadena(b, 30, 27);// Cortamos y sacamos un nombre mas corto
			nombre_extra_corto_errores = b.substr(0, 5);// Acortamos el asunto aun mas para un div
			archivo_anexo_modal += '<table style="width:auto"><tr><td>';
			archivo_anexo_modal += '<div class="art_exp center" style="float:left;">';
			archivo_anexo_modal += '<a href="#" onclick="pdf_modal(\''+a+''+nombre_extra_corto_errores+'\',\''+b+'\')">';
			archivo_anexo_modal += '<img src="imagenes/iconos/archivo_pdf.png" title="'+b+'" style="float:left" height="20px">';
			archivo_anexo_modal += '<span title="'+b+'" style="float:left;padding-left:5px;">'+nombre_corto_anexos+'</span></a></div>';
			archivo_anexo_modal += '</td></tr><tr><td>';
			archivo_anexo_modal += '<div id="'+a+''+nombre_extra_corto_errores+'" class="errores" style="display: none;"> ';
			archivo_anexo_modal += 'El formato del archivo que desea visualizar no es válido. El sistema solo admite formato PDF</div>';
			archivo_anexo_modal += '</td></tr></table>';
			contador_anexos_modal++;
		});
	}
	return archivo_anexo_modal;
}
/*****************************************************************************************
	Fin function VentanaModalP1Camino1() Construye la secion de archivos anexos al someterlo por filtros a mensajes clasificados como hilos
/*****************************************************************************************/
/*****************************************************************************************
	Function VentanaModalP1Camino2() Construye la secion de archivos anexos al someterlo por filtros a mensajes sin ninguna clasificacion
/*****************************************************************************************/
/*****************************************************************************************
	* Estructura los anexos que se vizualizaran en la ventana modal
	* @param {string} (poscion_hilos) Es obligatoria, se traduce a la posicion del mensaje en objeto_hilos
	* @return seccion de anexos con su vista
*****************************************************************************************/
function VentanaModalP1Camino2(poscion_json){
	var archivo_anexo_modal = '';
	var nombre_corto_anexos;
	var nombre_extra_corto_errores;
	if(json[poscion_json].attachments.length > 0){// Si el hilo en general tiene archivos anexos alamacenados
		$.each(json[poscion_json].attachments, function(a, b) {// Por cada archivo anexo en los mensajes de hilos se repite
			nombre_corto_anexos        = acortar_cadena(b.file, 30, 27);// Cortamos y sacamos un nombre mas corto
			nombre_extra_corto_errores = b.file.substr(0, 5);// Acortamos el asunto aun mas para un div
			archivo_anexo_modal += '<table style="width:auto"><tr><td>';
			archivo_anexo_modal += '<div class="art_exp center" style="float:left;">';
			archivo_anexo_modal += '<a href="#" onclick="pdf_modal(\''+a+''+nombre_extra_corto_errores+'\',\''+b.file+'\')">';
			archivo_anexo_modal += '<img src="imagenes/iconos/archivo_pdf.png" title="'+b.file+'" style="float:left" height="20px">';
			archivo_anexo_modal += '<span title="'+b.file+'" style="float:left;padding-left:5px;">'+nombre_corto_anexos+'</span></a></div>';
			archivo_anexo_modal += '</td></tr><tr><td>';
			archivo_anexo_modal += '<div id="'+a+''+nombre_extra_corto_errores+'" class="errores" style="display: none;"> ';
			archivo_anexo_modal += 'El formato del archivo que desea visualizar no es válido. El sistema solo admite formato PDF</div>';
			archivo_anexo_modal += '</td></tr></table>';
			contador_anexos_modal++;
		});
	}
	return archivo_anexo_modal;
}
/*****************************************************************************************
	Fin function VentanaModalP1Camino2() Construye la secion de archivos anexos al someterlo por filtros a mensajes sin ninguna clasificacion
/*****************************************************************************************/
/*****************************************************************************************
	Function pdf_modal() visualizará el pdf y mostrara el td que lo contiene 
/*****************************************************************************************
	* Valida si el formato es soportado y escribe en el id especifico que lo contiene o mostrara el mensaje de error
	* @param {string} (nombre) Es obligatorio, trae el nombre corte del id que se aloja el error
	* @param {string} (nombre_comparar) Es obligatorio, es el nombre completo del archivo el cual sera validado y llamara al archivo desde la raiz
	* @return Mostrara el pdf en su td contenedor o mostrara el mensaje de error por archivo no soportado
*****************************************************************************************/
function pdf_modal(nombre, nombre_comparar){
	if (nombre_comparar.indexOf(".pdf") > 0 || nombre_comparar.indexOf(".PDF") > 0){// Valida si el formato es soprotado
		$('#mostar_pdf').html("<div class='cerrar'><a href='javascript:cerrarVentana_pdf_modal();'>Cerrar Vista Previa Del PDF</a></div><object data='bodega_pdf/correo_electronico/baul/"+usuario_actual+"/"+nombre_comparar+"' width='100%' height='370px'></object>");// Estructura html
		$(".mostar_pdf").show();// Muestra el pdf en la ventana modal
	}else {
		nombre_errores = '#'+nombre;
		$(""+nombre_errores+"").slideDown("slow");// Muestra el error por archivo no soportado
	}
}
/*****************************************************************************************
	Fin function pdf_modal() visualizará el pdf y mostrara el td que lo contiene 
/*****************************************************************************************/
/*****************************************************************************************
	Function cerrarVentana_pdf_modal() 
/*****************************************************************************************
	* Nos va a cerrar el pdf que se vizualice en la ventana modal junto al html del mensaje
	@return Esconde el td que contiene el pdf
*****************************************************************************************/
function cerrarVentana_pdf_modal(){
	$(".mostar_pdf").hide();// Esconde el pdf en la ventana modal
}
/*****************************************************************************************
	Fin Function cerrarVentana_pdf_modal()
/*****************************************************************************************/
/*****************************************************************************************
	Function cerrarVentana()
/*****************************************************************************************
	* Cierra la ventana modal
*****************************************************************************************/
function cerrarVentana(){
	$("#ventana").slideUp("slow");
	$(".errores").slideUp("slow");
}
/*****************************************************************************************
	Fin function cerrarVentana()
/*****************************************************************************************/
///////////////////////////////////////////
		// SIGUE EL COMPLENTO DE LAS VENTANAS MODAL Y ESTE SE UTILIZARA PARA VIZUALIZAR EL PDF EN GRANDE ("SOLO PDF")
/*****************************************/
/*****************************************************************************************
	Function AbrirVistaPreviaPdf()
/*****************************************************************************************
	* Abre la ventana modal con el archivo o un error de archivo no soportado
	* @param {string} (nombre_archivo) Es obligatorio, se utiliza al verificar que sea soportado el archivo y llama al archivo de raiz
	* @return Muestra la ventana modal con la vizualizacion del archivo pdf o si no es soprotado mostara el error y el nombre del archivo
*****************************************************************************************/
function AbrirVistaPreviaPdf(nombre_archivo){
	cerrarVentana_pdf_modal();
	$("#ventana").slideDown("slow");// Muestra la ventana modal
	$("#contenido").css({'z-index': '100'});// Estilo modificado para sobreponer la ventana modal
	if (nombre_archivo.indexOf(".pdf") > 0 || nombre_archivo.indexOf(".PDF") > 0){// Valida que el archivo sea soportado
		$("#mostar_mensaje").html("<object data='bodega_pdf/correo_electronico/baul/"+usuario_actual+"/"+nombre_archivo+"' width='100%' height='370px'></object>");// Estrcutura html
	}else {
		$('#mostar_mensaje').html("<h1><div class='errores' style='display:block'><center>DOCUMENTO NO SOPORTADO \" "+nombre_archivo+" \"</center></div></h1>");// Estrcutura html
	}
}
/*****************************************************************************************
	Fin function AbrirVistaPreviaPdf()
/*****************************************************************************************/
/*****************************************************************************************/
///////////////////////////////////////////
			// FIN SECCIONES DE LAS VENTANAS MODAL 
/*****************************************/
///////////////////////////////////////////
/*****************************************/
///////////////////////////////////////////
		// SECCION RADICAR 
/*****************************************/
/*****************************************************************************************
	Function RadicarCorreo()
/*****************************************************************************************
	* Recopila la informacion necesaria para la creacion del pdf y la radicacion
	* @param {string} (ubicacion) Es obligatoria, nos dira que ubicacion tiene en el json genereal
	* @param {string} (asunto) Es obligatoria, trae el asunto a comparar si contiene hilos
	* @param {string} (vista_rapida) Es obligatoria, nos dira que json tomar
	* @return descarga de Pdf
*****************************************************************************************/
function RadicarCorreo(ubicacion, asunto, vista_rapida){
	var informacion_radicar;
	var mensaje_hilo;
	var nombre_anexos;
	var json;
	var nuevo_nombre_documento;
	if(vista_rapida == 1){
		json = informacionimap_vista_rapida.data;
	}else{
		json = informacionimap_vista_avanzada.data;
	}
	mensaje_hilo = admin_hilos(asunto);// Verifica si existe en objeto_hilos
	if(mensaje_hilo != undefined || mensaje_hilo != null){// Valida si el mensaje hace parte de un hilo
		nombre_anexos = RadicarCorreoCamino1(asunto, mensaje_hilo, vista_rapida);
	}else{
		nombre_anexos = RadicarCorreoCamino2(asunto, ubicacion, vista_rapida);
	}
	informacion_radicar = '{"anexos":["'+nombre_anexos+'], "informacion":["'+informacion_pdf_radicar+'], "mensaje":["'+mensaje_codificado_radicar+'"]}';
	nuevo_nombre_documento = Math.floor(Math.random() * (99999 - 10000)) + 10000;
	/* Radicar a procesar ajax */
	loading('myTable');
	$.ajax({
			type: 'POST',
			url:  'include/procesar_ajax.php',//Destino
			data: {recibe_ajax: 'buzon_correo_electronico_guardar', mensaje: informacion_radicar, numero_aleatorio: nuevo_nombre_documento, nombre : json[ubicacion].from.name, correo_usuario: json[ubicacion].from.address},
			success: function(cargar){// Retorna {cadena} Json codificado con la informacion del proceso imap
				$('#crear_pdf').html(cargar);
			}
	})
	/* Fin radicar a procesar ajax */
}
/*****************************************************************************************
	Fin function RadicarCorreo()
/*****************************************************************************************/
/*****************************************************************************************
	Function RadicarCorreoCamino1()
/*****************************************************************************************
	* Recopila la informacion necesaria para la creacion de los archivos anexos si el mensaje hace parte de un hilo, tambien crea la informacion y el mensaje a imprimir en el pdf
	* @param {string} (asunto) Es obligatoria, trae el asunto a comparar si contiene hilos
	* @param {string} (ubicacion) Es obligatoria, nos dira que ubicacion tiene en el json genereal
	* @param {string} (vista_rapida) Es obligatoria, nos dira que json tomar
	* @return nombre_anexos con la informacion de los anexos del hilo
*****************************************************************************************/
function RadicarCorreoCamino1(asunto, ubicacion, vista_rapida){
	var id = objeto_hilos[ubicacion].id_hilos[0];// Tomamos el primer id de los mensajes en hilo ya que contiene todo el mensaje
	var nombre_anexos = '';
	var informacion_pdf;
	var numero_anexos_pdf_radicar = 0;
	var json;
	if(vista_rapida == 1){// Valida si es vista rapida o es busqueda avanzada
		json = informacionimap_vista_rapida.data;
	}else{
		json = informacionimap_vista_avanzada.data;
	}
	if(objeto_hilos[ubicacion].anexo_hilos.length > 0){// Si el hilo en general tiene archivos anexos almacenados en objeto_hilos
		$.each(objeto_hilos[ubicacion].anexo_hilos, function(a, b) {// Por cada archivo anexo se repite
			if(objeto_hilos[ubicacion].anexo_hilos.length == 1){// Verifica si solo contiene un archivo
				nombre_anexos += b+'"';
			}else{
				if(a == 0){// Confirma el primero de la cadena
					nombre_anexos += b+'"';
				}else{
					nombre_anexos += ',"'+b+'"';
				}
			}// Crea una cadena sin comillas inciales ni finales
			numero_anexos_pdf_radicar++;
		});
	}else{
		nombre_anexos = "sin_archivos\"";
	}
	informacion_pdf_radicar = asunto+'","'+parte4+'","'+parte2+'", '+objeto_hilos[ubicacion].id_hilos.length+', "'+json[ubicacion].from.name+'","'+json[ubicacion].from.address+'","'+json[ubicacion].date+'",'+numero_anexos_pdf_radicar;// Informacion necesaria para el pdf
	mensaje_codificado_radicar = json[id].message.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/"/g, '&quot;').replace(/&/g, '||').replace(/#/g, '^^');// Codificacion carcateres especiales del mensaje html
	return nombre_anexos;
}
/*****************************************************************************************
	Fin function RadicarCorreoCamino1()
/*****************************************************************************************/
/*****************************************************************************************
	Function RadicarCorreoCamino2()
/*****************************************************************************************
	* Recopila la informacion necesaria para la creacion de los archivos anexos si el mensaje no es calsificadfo como parte de un hilo, tambien crea la informacion y el mensaje a imprimir en el pdf
	* @param {string} (asunto) Es obligatoria, trae el asunto a comparar si contiene hilos
	* @param {string} (ubicacion) Es obligatoria, nos dira que ubicacion tiene en el json genereal
	* @param {string} (vista_rapida) Es obligatoria, nos dira que json tomar
	* @return nombre_anexos con la informacion de los anexos del hilo
*****************************************************************************************/
function RadicarCorreoCamino2(asunto, ubicacion, vista_rapida){
	var nombre_anexos = '';
	var numero_anexos_pdf_radicar = 0;// Valida si es vista rapida o es busqueda avanzada
	var json;
	if(vista_rapida == 1){
		json = informacionimap_vista_rapida.data;
	}else{
		json = informacionimap_vista_avanzada.data;
	}
	if(json[ubicacion].attachments.length > 0){//Si tiene archivos anexos
		$.each(json[ubicacion].attachments, function(a, b) {// Por cada archivo anexo se repite
			if(json[ubicacion].attachments.length == 1){// Verifica si solo contiene un archivo
				nombre_anexos += b.file+'"';
			}else{
				if(a == 0) {// Confirma el primero de la cadena
					nombre_anexos += b.file+'"';
				}else{
					nombre_anexos += ',"'+b.file+'"';
				}// Crea una cadena sin comillas inciales ni finales
			}
			numero_anexos_pdf_radicar++;
		});
	}else{
		nombre_anexos = "sin_archivos\"";
	}
	informacion_pdf_radicar = asunto+'","'+parte4+'","'+parte2+'", 1, "'+json[ubicacion].from.name+'","'+json[ubicacion].from.address+'","'+json[ubicacion].date+'",'+numero_anexos_pdf_radicar;// Informacion necesaria para el pdf
	mensaje_codificado_radicar = json[ubicacion].message.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/"/g, '&quot;').replace(/&/g, '||').replace(/#/g, '^^');// Codificacion carcateres especiales del mensaje html
	return nombre_anexos;
}
/*****************************************************************************************
	Fin function RadicarCorreoCamino2()
/*****************************************************************************************/
///////////////////////////////////////////
		// FIN SECCION RADICAR
/*****************************************/
///////////////////////////////////////////
/*****************************************/
///////////////////////////////////////////
		// FUNCIONES ADICIONALES
/*****************************************/
/*****************************************************************************************
	Function acortar_cadena()
/*****************************************************************************************
	* Tomara la cadena y la validara si cuenta con la longitud deseada
	* @param {string} (cadena) Es obligatorio, sera la cadena principal la cual se medira y se acortara si es el debido caso
	* @param {string} (limite) Es obligatorio, establece el limite de las palabras que seran cortadas
	* @param {string} (corte) Es obligatorio, indica a que distancia se hara el corte de la cadena
	* @return Cadena comprodaba a su longitud o recortada a su indice sumandole tres puntos suspensivos
*****************************************************************************************/
function acortar_cadena(cadena, limite, corte){
	if(cadena.length > limite){// Si el asunto del mensaje tien una longitud mayor a 25 caracteres
		return cadena.substr(0, corte)+'...';// Con substr recortamos el asunto y agregamos '...'
	} else {
		return cadena;
	}
}
/*****************************************************************************************
	Function acortar_cadena()
/*****************************************************************************************/
/*****************************************************************************************
	Function admin_hilos()
/*****************************************************************************************
	* Tomara la cadena y la validara si cuenta con la longitud deseada
	* @param {string} (asunto_hilo_comparar) Es obligatorio, sera el asunto el cual comparemos si ya se encuentra registrado en un hilo
	* @return El numero que sirve como indice para encontrarlo en el objeto o null en caso que no exista en el objeto
*****************************************************************************************/
function admin_hilos(asunto_hilo_comparar){
	var existe_asunto_hilos_pre = null;
	$.each(objeto_hilos, function(a, b) {// Por cada hilo regitrado en "objeto_hilos" se repite
		if(objeto_hilos[a].asunto_hilo_nombre === asunto_hilo_comparar){// Si existe el asunto en "objeto_hilos"
			existe_asunto_hilos_pre = a;// Se referencia la posicion donde lo encontro
		}
	});
	return existe_asunto_hilos_pre;
}
/*****************************************************************************************
	Fin function admin_hilos()
/*****************************************************************************************/	
/* JQUERY - Funcion onclick para escribir la busqueda avanzada */
function mostrar_busqueda_avanzada() {
	$("#informacion_imap").html(cuerpo_vista_avanzada);// Estrcutura html
	$("#busqueda_avanzada").slideUp("slow");// Muestra la ventana modal
};
/* JQUERY - Fin funcion onclick para escribir la busqueda avanzada */
///////////////////////////////////////////
		// FIN FUNCIONES ADICIONALES
/*****************************************/
</script>