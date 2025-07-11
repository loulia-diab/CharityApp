<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8" />
    <title>Reset Password Email Template</title>
    <meta name="description" content="Reset Password Email Template.">
    <style type="text/css">
        body {
            margin: 0;
            background-color: #f2f3f8;
            font-family: 'Open Sans', sans-serif;
        }

        a:hover {
            text-decoration: underline !important;
        }
    </style>
</head>

<body>
<table cellspacing="0" cellpadding="0" border="0" width="100%" bgcolor="#f2f3f8">
    <tr>
        <td>
            <table style="max-width:670px; margin:0 auto; background-color: #f2f3f8;" width="100%" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="height:80px;">&nbsp;</td>
                </tr>

                <tr>
                    <td>
                        <table width="95%" align="center" cellpadding="0" cellspacing="0"
                               style="background:#fff; border-radius:8px; text-align:center; box-shadow:0 6px 18px rgba(0,0,0,0.06);">
                            <tr>
                                <td style="height:40px;">&nbsp;</td>
                            </tr>

                            <tr>
                                <td style="padding:0 35px;">
                                    <h2 style="color:#1e1e2d; font-weight:500; margin:0; font-size:28px;">You have requested to reset your password</h2>
                                    <span style="display:inline-block; margin:25px 0; border-bottom:1px solid #cecece; width:100px;"></span>
                                    <p style="color:#455056; font-size:15px; line-height:24px; margin:0;">
                                        Your verification code is:
                                    </p>
                                    <div style="margin-top:30px;">
                                        <a href="javascript:void(0);"
                                           style="background:#3498db; color:#fff; text-decoration:none; font-weight:600; font-size:16px; padding:12px 30px; border-radius:50px; display:inline-block;">
                                            {{$code}}
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td style="height:40px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="height:40px;">&nbsp;</td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>

</html>
