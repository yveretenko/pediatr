<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailHelper
{
    public static function send($to, $subject, $text)
    {
        global $config;

        $mail = new PHPMailer;
        $mail->CharSet='UTF-8';
        $mail->setFrom('admin@fcunited.ru');
        $mail->Subject=$subject;
        $mail->msgHTML($text);

        foreach (explode(',', $to) as $receiver)
            $mail->addAddress(trim($receiver));

        if ($config['use_smtp'])
        {
            $mail->isSMTP();
            $mail->SMTPAuth=true;
            $mail->SMTPDebug=SMTP::DEBUG_OFF;
            $mail->Host=$config['smtp']['host'];
            $mail->Port=$config['smtp']['port'];
            $mail->Username=$config['smtp']['username'];
            $mail->Password=$config['smtp']['password'];
        }

        return $mail->send();
    }
}