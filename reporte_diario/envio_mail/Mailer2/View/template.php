<!DOCTYPE HTML>
<html>
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>:title-page</title>
    <style type="text/css">
        body {
            margin: 0 auto;
            padding: 0;
            min-width: 100%;
            font-family: sans-serif;
        }

        table {
            margin: 50px 0 50px 0;
        }

        .header {
            height: 40px;
            text-align: center;
            text-transform: uppercase;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            height: 100px;
            font-size: 18px;
            line-height: 30px;
        }

        .button {
            text-align: center;
            font-size: 18px;
            font-family: sans-serif;
            font-weight: bold;
            padding: 0 30px 0 30px;
        }

        .button a {
            color: #FFFFFF;
            text-decoration: none;
        }

        .buttonwrapper {
            margin: 0 auto;
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
</head>
<body style="background-color:#009587">
<table style="background-color:#FFFFFF" width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header">
        <td style="padding: 40px;">
            <img src=":logo-page"
                 alt="Smiley face" height="100" width="100">
            <br><br><br>
            :greeting-page
        </td>
    </tr>

    <tr class="content" align="center">
        <td style="padding:20px ">
            <p>
                :line-page <br>
            </p>
        </td>
    </tr>

    <tr class="button" align="center">
        <td style="padding: 20px 0 0 0; display: none">
            <table style="background-color:#009587" border="0" cellspacing="0" cellpadding="0" class="buttonwrapper">
                <tr>
                    <td class="button" height="45">
                        <a href=":action-send-page" target="_blank">:action-page</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr class="content" align="center">
        <td style="padding: 40px">
            <p>:firm-page</p>
            <p style="position: relative; bottom: 40px;">_______________________________________</p>
            <p id="tel" style="display: none;position: relative; bottom: 60px; font-size: 15px;">Telefono: :tel-page</p>
            <p id="email" style="display: none;position: relative; bottom: 80px; font-size: 15px;">Correo:
                :email-page</p>
        </td>
    </tr>

    <tr class="footer">
        <td style="padding: 20px; font-size: 10px;">
            <img src=":logo-footer-page"
                 alt="Smiley face">
            <br><br>
            :footer-page
        </td>
    </tr>
</table>
</body>
</html>