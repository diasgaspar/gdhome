<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace gdhome\PHPMailer;
use gdhome\HomeVars as HomeVars;
/**
 * Description of MailSender
 *
 * @author gaspar
 */
class MailSender {
    
  
    /**
     * 
     * @param type $email
     * @param type $recipient_name
     * @param type $message
     */
    function send_mail($email, $recipient_name, $subject, $message)
    {
        $sendStatus=false;
        $mail = new PHPMailer(true);

        $mail->CharSet="utf-8";
        //$mail->IsSMTP();
        $mail->Port = 465;  
        $mail->SMTPSecure = 'ssl';// set mailer to use SMTP
        $mail->Host = HomeVars::MAIL_HOST;  //smtp.gmail.com specify main and backup server
       // $mail->SMTPAuth = true;     // turn on SMTP authentication
        //$mail->Username = "diasparedes";  // SMTP username
        //$mail->Password = "digimon1234"; // SMTP password

        $mail->From = HomeVars::MAIL_FROM_ADDRESS;
        $mail->FromName = "Home Agent";
        //$mail->addReplyTo($emailT, $nume);
        $mail->AddAddress($email, $recipient_name);

        $mail->WordWrap = 120;                                 // set word wrap to 50 characters
        $mail->IsHTML(false);                                  // set email format to HTML (true) or plain text (false)

        $mail->Subject = $subject;
        $mail->Body    = $message;
        //$mail->AltBody = "This is the body in plain text for non-HTML mail clients";    
        //$mail->AddEmbeddedImage('images/logo.png', 'logo', 'logo.png');
        //$mail->addAttachment('files/file.xlsx');

        if(!$mail->Send())
        {
           echo "Message could not be sent.";
           echo "Mailer Error: " . $mail->ErrorInfo;
           $sendStatus=false;
           //exit;
        }
        else{
         $sendStatus=true;   
         echo "Message has been sent";
        }
        return $sendStatus;
    }
    
    
    
    
}
