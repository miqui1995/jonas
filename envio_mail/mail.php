<?php
header('Content-Type: text/html; charset=utf-8');
require("Mailer2/Mailer.php");

$asunto                    = $_POST['asunto'];
$contenido_html_mail_envio = $_POST['contenido_html'];
$mail_usuario              = $_POST['direccion_destino'];
$nombre_completo_imprimir  = $_POST['nombre_completo_segundo'];
$tipo_envio                = $_POST['tipo_envio'];

$config["mailer"] = [
    'SMTP_DEBUG' => 0, // Habilitar salida de depuración detallada (2=Debug completo)
    'MAIL_DRIVER' => 'smtp',
    //Configurar la aplicación de correo para usar SMTP, driver: smpt, mail, sendmail, qmail
    // 'HOST' => 'smtp.gmail.com', // Este es cuando se utiliza GMAIL
    'HOST' => 'mail.gammacorp.co ',
    //Especificar servidores SMTP principales y de respaldo   //DNS o SMTP
    'AUTH' => true,
    //Habilitar autenticación SMTP
    // 'USERNAME' => 'enviomailgammacorp@gmail.com', // Esto es cuando se utiliza GMAIL
    'USERNAME' => 'notificador_automatico@gammacorp.co',
    //usuario mailer (gmail, hotmail, yahoo, etc..)
    // 'PASSWORD' => 'A123456789*',         // Esto es cuando se utiliza GMAIL
    'PASSWORD' => 'GammaCorp2021*+',
    //contraseña de usuario mailer
    'SECURE' => 'ssl',
    //Habilite el cifrado TLS, también se acepta `ssl`
    // 'PORT' => 587,           // Esto es cuando se utiliza GMAIL
    'PORT' => 465,
    //Puerto TCP para conectarse
];
$config["patch"] = dirname(dirname(__FILE__)) . '/envio_mail/Mailer2/'; //raiz del proyecto (nombre de la carpeta donde se encuentra este)
$mail = new Mailer($config);


//se toma el dato contenido_html_mail_envio que trae la estructura de como se va a visualizar el correo
$html =  $contenido_html_mail_envio;


$mail->setHtml($html);

if($tipo_envio=="respuesta_radicado"){
    $path = $_POST['path'];
    $bool = $mail->sendMail(
        [
            "subject" => "$asunto",
            "from"    => [
                "mail" => "noresponder@gammacorp.com",
                "name" => "Notificador Automatico Jonas Gamma Corp"
            ],
            "address" => [
                [
                    "mail" => "$mail_usuario",
                    "name" => "$nombre_completo_imprimir"
                ]
            ],
            "AddAttachment"=> "chunk_split(base64_encode(file_get_contents($path)))"
        ]
    );
}else{
    $bool = $mail->sendMail(
        [
            "subject" => "$asunto",
            "from"    => [
                "mail" => "noresponder@gammacorp.com",
                "name" => "Notificador Automatico Jonas Gamma Corp"
            ],
            "address" => [
                [
                    "mail" => "$mail_usuario",
                    "name" => "$nombre_completo_imprimir"
                ]
            ]
        ]
    );
}
// echo $bool;
?>