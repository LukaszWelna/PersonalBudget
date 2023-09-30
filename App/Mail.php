<?php

namespace App;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;
use \App\Config;

/**
 * Mail
 */

class Mail {

    /**
     * Send a message
     * 
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     * 
     */
    public static function send($to, $subject, $text, $html) {

        $mail = new PHPMailer(true);
        
        $mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
            );

        try {

            $mail->isSMTP();                                //Send using SMTP
            $mail->Host       = Config::SMTP_HOST;          //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                       //Enable SMTP authentication
            $mail->Username   = Config::SMTP_USER;          //SMTP username
            $mail->Password   = Config::SMTP_PASSWORD;      //SMTP password
            $mail->SMTPSecure = 'tls';                      //Enable implicit TLS encryption
            $mail->Port       = 587;         

            $mail->setFrom('myEmailServer1024@gmail.com');
            $mail->addAddress($to);
            $mail->addReplyTo('myEmailServer1024@gmail.com');
            $mail->Subject = $subject;
            $mail-> AltBody = $text;
            $mail->Body = $html;

            $mail -> send();

            $sent = true;

        } catch (Exception $e) {
            $errors[] = $mail -> ErrorInfo;
            var_dump($errors);
        }
    }
 }