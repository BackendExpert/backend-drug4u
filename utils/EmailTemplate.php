<?php

class EmailTemplate
{
    public static function layout(
        string $title,
        string $message,
        string $buttonText,
        string $buttonLink
    ): string {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='margin:0; padding:0; background:#f5f5f5; font-family:Arial, sans-serif;'>

            <div style='max-width:600px; margin:40px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 8px 25px rgba(0,0,0,0.08);'>

                <div style='background:#000000; padding:20px; text-align:center;'>
                    <h2 style='color:#ffffff; margin:0;'>{$title}</h2>
                </div>

                <div style='padding:30px; text-align:center;'>

                    <p style='font-size:16px; color:#333; margin-bottom:25px;'>
                        {$message}
                    </p>

                    <a href='{$buttonLink}' 
                       style='display:inline-block;
                              padding:14px 28px;
                              background:#000000;
                              color:#ffffff;
                              text-decoration:none;
                              border-radius:8px;
                              font-weight:600;
                              font-size:14px;
                              letter-spacing:0.5px;'>
                       {$buttonText}
                    </a>

                    <p style='margin-top:25px; font-size:12px; color:#777; word-break:break-all;'>
                        {$buttonLink}
                    </p>

                </div>

                <div style='background:#f1f1f1; padding:15px; text-align:center; font-size:12px; color:#666;'>
                    © " . date('Y') . " Drug 4 You
                </div>

            </div>

        </body>
        </html>
        ";
    }


    public static function Notification(
        string $title,
        string $message,
    ): string {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='margin:0; padding:0; background:#f5f5f5; font-family:Arial, sans-serif;'>

            <div style='max-width:600px; margin:40px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 8px 25px rgba(0,0,0,0.08);'>

                <div style='background:#000000; padding:20px; text-align:center;'>
                    <h2 style='color:#ffffff; margin:0;'>{$title}</h2>
                </div>

                <div style='padding:30px; text-align:center;'>

                    <p style='font-size:16px; color:#333; margin-bottom:25px;'>
                        {$message}
                    </p>

                </div>

                <div style='background:#f1f1f1; padding:15px; text-align:center; font-size:12px; color:#666;'>
                    © " . date('Y') . " Drug 4 You
                </div>

            </div>

        </body>
        </html>
        ";
    }
}
