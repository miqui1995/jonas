<?php session_start();?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="color: #515F7F; background-color: #F5F7FA; -webkit-text-size-adjust: none; font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; height: 100%; -ms-hyphens: auto; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100%!important; -webkit-hyphens: auto; word-break: break-word;">
<style>
    @media only screen and (max-width: 600px) {
        .inner-body {
            width: 100% !important;
        }

        .footer {
            width: 100% !important;
        }
    }

    @media only screen and (max-width: 500px) {
        .button {
            width: 100% !important;
        }
    }

    .footer {
        text-transform: uppercase;
        text-align: center;
        height: 40px;
        font-size: 14px;
        font-style: italic;
    }

    .footer a {
        color: #000000;
        text-decoration: none;
        font-style: normal;
    }
</style>
<table class="wrapper" width="100%"
       style="font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; background-color: #F5F7FA; padding: 0;  margin: 0; width: 100%;">
    <tbody>
    <tr>
        <td align="center" style="font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box;">
            <table class="content" width="100%"
                   style="font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; padding: 0; -premailer-cellpadding: 0; -premailer-cellspacing: 0; margin: 0; width: 100%; -premailer-width: 100%;">
                <tbody>

                <tr>
                    <td class="body" width="100%"
                        style="font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; margin: 0; width: 100%; -premailer-width: 100%; padding: 0; -premailer-cellpadding: 0; -premailer-cellspacing: 0;">
                        <table class="inner-body inner-body-first" align="center" width="1000" cellpadding="0"
                               cellspacing="0"
                               style="font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; padding: 0; -premailer-cellpadding: 0; -premailer-cellspacing: 0; background-color: #FFF; box-shadow: 0 0 10px 1px #ddd; margin: 0 auto; width: 700px; max-width: 700px; -premailer-width: 570px; border-top: 5px solid #30D7F0;">
                            <!-- Body content -->
                            <tbody>
                            <tr>
                                <td class="content-cell"
                                    style="font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; padding: 25px; max-width: 500px">
                                    <h2 style="color: #2F3133; text-align: center; margin-top: 0; font-weight: 700; font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; font-size: 16px;">
                                        :greeting-page</h2>
                                    <a href=":link-logo"
                                       style="font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; text-decoration: none!important; font-weight: 700; color: #bbbfc3; font-size: 19px; text-shadow: 0 1px 0 #fff;"
                                       target="_blank">
                                        <img src=":logo-page" width="200" height="66"
                                             style="font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; max-width: 100%; border: none;"></a>
                                    <br>
                                    <p style="color: #515F7F; margin-top: 0; font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; text-align: left; font-size: 16px; line-height: 1.5em;">
                                        :fecha-page</p>
                                    <p style="color: #515F7F; margin-top: 0; font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; text-align: left; font-size: 16px; line-height: 1.5em;">
                                        :line-page</p>
                                    <p>
                                    <p><a href=":qr-code" target="_blank">Consultar vínculo vía web</a></p>
                                    <img src=":qr-link">
                                    <p><b>Codigo de refencia :qr-code</b></p>
                                    </p>
                                    <br><br>
                                    <p style="color: #515F7F; margin-top: 0; font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; text-align: left; font-size: 16px; line-height: 1.5em; margin-bottom: 0;">
                                        Cordialmente</p>
                                    <br><br>
                                    <p style="color: #515F7F; margin-top: 0; font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; text-align: left; font-size: 16px; line-height: 1.5em; margin-bottom: 0;">
                                        <b>:name-empresa<br>
                                            :gestion-empresa<br>
                                            :agencia-empresa</b><br>
                                        E-mail: :email<br>
                                        Dirección: :address<br>
                                        PBX: :pbx<br>
                                        Siganos en: :redes<br>
                                        :url-page
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="spacer"
                        style="font-family: Panton,Avenir,Helvetica,sans-serif; box-sizing: border-box; height: 35px;"></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>

