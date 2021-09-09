<?php

require("Mailer.php");
require("config.php");


$config = include("config.php");

$mail = new Mailer($config);

$html = $mail //asi 
    ->builderMail()
    ->title()
    ->logo()
    ->firm()
    ->line()
    ->footer()
    ->greeting()
    ->footerImg()
    ->build();

$html = (new BuilderMail()) //o  asi
    ->title()
    ->logo()
    ->firm()
    ->line()
    ->footer()
    ->greeting()
    ->footerImg()
    ->build();


$html = $mail->builderMail("View/template1.php") //template 2
->title()
    ->greeting()
    ->line()
    ->company()
    ->logo()
    ->link()
    ->dateTime()
    ->qr()
    ->build();


$mail->setHtml($html);
$mail->setArchive($config["patch"] . "View/template.php");

$bool = $mail->sendMail(
    [
        "subject" => "Esto es una prueba",
        "from"    => [
            "mail" => "wowzeros2@gmail.com",
            "name" => "carlos"
        ],
        "address" => [
            "mail" => "jonatanrod@yahoo.es",
            "name" => "Johnnatan rodriguez"
        ],
    ]
);
echo $bool;
