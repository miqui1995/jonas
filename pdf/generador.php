<?php //Inicio de codigo php
require('rotacion.php');//requiere el archivo 	

//inicia la asignacion de variables resibidas de la formulario.html	
$variable_donde_se_guarda_el_valor_del_texto = $_POST['texto']; 
$variable_donde_se_guarda_el_valor_de_la_ciudad = $_POST['ciudad']; 
$variable_donde_se_guarda_el_valor_del_dirigido = $_POST['dirigido']; 
$variable_donde_se_guarda_el_valor_del_radicado = $_POST['radicado'];
$variable_donde_se_guarda_el_valor_del_cargo_del_dirigido = $_POST['cargo'];
$variable_donde_se_guarda_el_valor_de_la_fecha_actual = $_POST['fecha_actual']; 
$variable_donde_se_guarda_el_valor_del_asunto_del_documento = $_POST['asunto'];
$variable_donde_se_guarda_el_valor_de_quien_genero_este_documento = $_POST['nombre'];
$variable_donde_se_guarda_el_valor_de_la_direccion_del_dirigido = $_POST['direccion'];
$variable_donde_se_guarda_el_valor_del_cargo_del_remitente = $_POST['cargo_remitente'];
$variable_donde_se_guarda_el_valor_de_la_empresa_donde_labora_el_dirigido = $_POST['empresa'];
$variable_donde_se_guarda_el_valor_de_en_nombre_de_quien_se_genera_el_documento = $_POST['en_nombre_de_quien'];
//Finaliza la asignacion de variables resibidas de la formulario.html	

class PDF extends PDF_Rotate
{//inicia la clase PDF con extencion PDF_ROTATE 
		
	function Header()
		{//inicia la funcion Header y cada vez que se agregue una pagina al documento se va a repetir esta funcion
			//se resibe los datos de 'marca_de_agua' y se asigna a una variable aqui por que fuera de si se hace fuera de la clase PDF no la reconoce y bota Error 'undefined'
			$variable_donde_se_guarda_el_valor_de_la_marca_de_agua = $_POST['marca_de_agua'];

			//Inicia las caracteristicas para la cabezera de las paginas

			//false = valor falso
			//40 = saltos de linea despues de termina la imagen
			//Cell = metodo para transformar e imprimir imagene y texto
			//Image = para que sepa que va a imprimir y entre parentesis y con comillas simples la ruta de la imagen
			//10 = la cantidad de pixeles que va a haber desde el borde izquierdo de la pagina hasta el borde izquierdo de la imagen
			// 9 = la cantidad de pixeles que se va a correr la imagen desde el borde superior de la pagina
			//190 = la cantidad de acercamiento a la imagen (efecto zoom)
			$this->Cell(false,40,false,false,40,false,$this->Image('./img/imagen_cabezera.jpg', 10,9, 190));
			//Fin de las caracteristicas de la cabezera de las paginas

			//Inicio de las caracteristicas de la marca de agua 
			//Arial = tipo de letra 
			// B = Bolt
			// 200 = tamaño de letra
			$this->SetFont('Arial','B',200);
			//color del texto 
			$this->SetTextColor(200,200,200);

			// 100 = la cantidad de pixeles que va a haber desde el borde izquierdo de la pagina hasta la primera letra de la palabra
			// 185 = la cantidad de pixeles que va a haber desde el borde superior de la pagina hasta borde superior letra de la palabra
			// utf8_decode = para que acepte caracteres raors como tildes y signos de puntuacion mientras imprime la variable_donde_se_guarda_el_valor_de_la_marca_de_agua
			// 45 =  los grados que se va a girar el texto
			$this->RotatedText(100,185,utf8_decode($variable_donde_se_guarda_el_valor_de_la_marca_de_agua),45);
			//Fin de caracteristicas de la marca de agua
		}//Finaliza la funcion Header

// la funcion de RotatedText es crear variables para que cuando se use se pueda llenar con los datos que establecemos 
	function RotatedText($x, $y, $txt, $angle)
		{
			//El texto gira alrededor de su origen
			$this->Rotate($angle,$x,$y);
			$this->Text($x,$y,$txt);
			$this->Rotate(0);
		}

	function Footer()
		{//inicio de la funcion Footer
			// false = valor falso 
			//Image = para que sepa que va a imprimir y entre parentesis y con comillas simples la ruta de la imagen
			// 10 = la cantidad de pixeles que va a haber desde el borde izquierdo de la pagina hasta el borde izquierdo de la imagen
			// 275 = la cantidad de pixeles que va a haber desde el borde superior de la pagina hasta borde superior letra de la imagen
			//190 = la cantidad de acercamiento a la imagen (efecto zoom)
			$this->Cell(false,false,'',false,false,'',$this->Image('./img/imagen_pie_de_pagina.jpg', 10,275, 190));
		
		}//Fin funcion Footer
}

$pdf=new PDF();// se le asigna a la variable $pdf la clase PDF y se crea un nuevo documento apartir de las caracteristicas establecidas en la clase PDF que esta en 'fpdf.php'

$pdf->AddPage();//se agrega una nueva pagina


$pdf->SetMargins(20,20,20,20);//20 = la cantidad de pixeles desde los bordes superior,inferior,del borde izquierdo y del borde derecho de la pagina  

//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//Arial = tipo de letra 
// B = Bolt
// 25 = tamaño de letra
$pdf->SetFont('Arial','B',25);

//Cell (136) = la cantidad de pixeles que se va a correr desde el borde izquierdo de la pagina hasta la primera letra de el texto
$pdf->Cell(136);

//10 = la cantidad de pixeles que va a haber desde el borde inferior de la imagen hasta borde superior de el radicado 
// false = valor falso
$pdf->Cell(false,10,$variable_donde_se_guarda_el_valor_del_radicado,false,false,false);

$pdf->Ln(7);//salta 7 pixeles hacia abajo

//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//Arial = tipo de letra 
// B = Bolt
// 11 = tamaño de letra
$pdf->SetFont('Arial','B',11);

//Cell (103) = la cantidad de pixeles que se va a correr desde el borde izquierdo de la pagina  hasta la primera letra de el texto
$pdf->Cell(103);

//10 = la cantidad de pixeles que va a haber desde el borde inferior del radicado hasta borde superior del texto 'Al contestar por favor cite estos datos:'
$pdf->Cell(false,10,'Al contestar por favor cite estos datos:',false,false,false);

$pdf->Ln(5);//salta 5 pixeles hacia abajo

//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//Arial = tipo de letra 
// 11 = tamaño de letra
$pdf->SetFont('Arial','',11);

//Cell (122) = la cantidad de pixeles que se va a correr desde el borde izquierdo de la pagina  hasta la primera letra de el texto
$pdf->Cell(122);

//10 = la cantidad de pixeles que va a haber desde el borde inferior del texto 'Al contestar por favor cite estos datos:' hasta borde superior del texto 'Radicado No:'
$pdf->Cell(false,10,'Radicado No:',false,false,false);

//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//Arial = tipo de letra 
// B = Bolt
// 11 = tamaño de letra
$pdf->SetFont('Arial','B',11);

//Cell (-17) = la cantidad de pixeles que se va a correr desde el borde izquierdo de la pagina  hasta la primera letra de el texto
$pdf->Cell(-17);

//10 = la cantidad de pixeles que va a haber desde el borde inferior  del texto 'Radicado No:' hasta borde izquierdo del radicado
$pdf->Cell(false,10,$variable_donde_se_guarda_el_valor_del_radicado,false,false,false);
$pdf->Ln(5);//salta 5 pixeles hacia abajo

//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//Arial = tipo de letra 
// 11 = tamaño de letra
$pdf->SetFont('Arial','',11);

//Cell (138) = la cantidad de pixeles que se va a correr desde el borde izquierdo de la pagina hasta la primera letra de el texto
$pdf->Cell(138);

//10 = la cantidad de pixeles que va a haber desde el borde inferior del texto 'Radicado No:' hasta borde superior del texto 'Fecha'
$pdf->Cell(false,10,'Fecha:',false,false,false);

//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//Arial = tipo de letra 
// B = Bolt
// 11 = tamaño de letra
$pdf->SetFont('Arial','B',11);

//Cell (138) = la cantidad de pixeles que se va a correr desde el borde izquierdo de la pagina hasta la primera letra de el texto
$pdf->Cell(-15);

//10 = la cantidad de pixeles que va a haber desde el borde inferior del radicado hasta borde superior de la fecha
$pdf->Cell(false,10,$variable_donde_se_guarda_el_valor_de_la_fecha_actual,false,false,false);

$pdf->Ln(15);



//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//helvetica = tipo de letra 
// 12 = tamaño de letra
$pdf->SetFont('helvetica','',12); 


$pdf->SetTextColor(0,0,0); //esto asigna el color negro a todo lo que siga hasta encontrar otro SetTextColor


$pdf->Write(false,utf8_decode($variable_donde_se_guarda_el_valor_de_la_ciudad));//imprime el valor que le llega de la variable_donde_se_guarda_el_valor_de_la_ciudad

$pdf->Ln(8);//salta 8 pixeles hacia abajo

$pdf->Write(0,utf8_decode("Señor(a)"));//Imprime el texto "Señor(a)"

$pdf->Ln(5);//salta 5 pixeles hacia abajo

$pdf->Write(0,utf8_decode($variable_donde_se_guarda_el_valor_del_dirigido));//imprime el valor que le llega de la variable_donde_se_guarda_el_valor_del_dirigido

$pdf->Ln(5);//salta 5 pixeles hacia abajo

$pdf->Write(0,utf8_decode($variable_donde_se_guarda_el_valor_del_cargo_del_dirigido));//imprime el valor que le llega de la variable_donde_se_guarda_el_valor_del_cargo_del_dirigido

$pdf->Ln(5);//salta 5 pixeles hacia abajo

$pdf->Write(0,utf8_decode($variable_donde_se_guarda_el_valor_de_la_empresa_donde_labora_el_dirigido));//imprime el valor que le llega de la variable_donde_se_guarda_el_valor_de_la_empresa_donde_labora_el_dirigido

$pdf->Ln(5);//salta 5 pixeles hacia abajo

$pdf->Write(0,utf8_decode($variable_donde_se_guarda_el_valor_de_la_direccion_del_dirigido ));//imprime el valor que le llega de la variable_donde_se_guarda_el_valor_de_la_direccion_del_dirigido

$pdf->Ln(15);//salta 15 pixeles hacia abajo

$pdf->Write(0,utf8_decode("Asunto:"));//imprime el texto "Asunto:"

$pdf->Ln(5);//salta 5 pixeles hacia abajo

$pdf->Write(0,utf8_decode($variable_donde_se_guarda_el_valor_del_asunto_del_documento ));//imprime el valor que le llega de la variable_donde_se_guarda_el_valor_del_asunto_del_documento

$pdf->Ln(7);//salta 7 pixeles hacia abajo

//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//Arial = tipo de letra 
// 11 = tamaño de letra
$pdf->SetFont('Arial','',11); 


$pdf->SetTextColor(0,0,0); //esto asigna el color negro a todo lo que siga hasta encontrar otro  SetTextColor

$pdf->MultiCell(0,4,utf8_decode($variable_donde_se_guarda_el_valor_del_texto),0,'J');//esto imprime la variable_donde_se_guarda_el_valor_del_texto, lo codifica para que acepte tilde y caracteres raros y lo justifica con la letra "J"

$pdf->Ln(7);//salta 7 pixeles hacia abajo

//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//helvetica = tipo de letra 
// 12 = tamaño de letra
$pdf->SetFont('helvetica','',12); 

$pdf->Ln(8);//salta 8 pixeles hacia abajo

$pdf->Write(0,utf8_decode("Respetado Señor(a):")) ;//Imprime el texto "Respetado Señor(a):" 


$pdf->Ln(18);//salta 18 pixeles hacia abajo

$pdf->Write(0,utf8_decode("Reciba un atento saludo")) ;//Imprime el texto "Reciba un atento saludo"


$pdf->Ln(15);//salta 15 pixeles hacia abajo

$pdf->Write(0,utf8_decode($variable_donde_se_guarda_el_valor_de_en_nombre_de_quien_se_genera_el_documento));//esto imprime la variable_donde_se_guarda_el_valor_de_en_nombre_de_quien_se_genera_el_documento

$pdf->Ln(5);//salta 5 pixeles hacia abajo

$pdf->Write(0,utf8_decode($variable_donde_se_guarda_el_valor_del_cargo_del_remitente));//esto imprime la variable_donde_se_guarda_el_valor_del_cargo_del_remitente

$pdf->Ln(5);//salta 5 pixeles hacia abajo

//se va a aplicar el metodo SetFont a todo el contenido hasta encontrar otro metodo SetFont
//Arial = tipo de letra 
// 9 = tamaño de letra
$pdf->SetFont('Arial','',9);

$pdf->Write(0,utf8_decode("Proyectó:"));//Imprime el texto "Proyectó:"

$pdf->Write(0,utf8_decode($variable_donde_se_guarda_el_valor_de_quien_genero_este_documento));//esto imprime la variable_donde_se_guarda_el_valor_de_quien_genero_este_documento

//EAN13 = funcion que codifica los valores que llegan a la variable_donde_se_guarda_el_valor_del_radicado y lo convierte en codigo de barras que esta en 'rotacion.php'
//140 = La cantidad de pixeles que va a haber desde el borde izquierdo de la pagina y el borde izquierdo de la primera barra del codigo de barras
//240 = La cantidad de pixeles que va a haber desde el borde superior de la pagina y el borde superior del codigo de barras
$pdf->EAN13(140,240,$variable_donde_se_guarda_el_valor_del_radicado);

// "prueba.pdf" = nombre del documento cuando se descargue
// "D" = Descargar documento
$pdf->Output("prueba.pdf","D");

//Fin de codigo php?>
