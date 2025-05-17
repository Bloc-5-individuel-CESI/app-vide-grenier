<?php

namespace App\Controllers;

use Core\Controller;
require("/home/site/libs/PHPMailer-master/src/PHPMailer.php");
require("/home/site/libs/PHPMailer-master/src/SMTP.php");
class Contact extends Controller
{
    public function indexAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $to = $_POST['to_email'];
            $from = $_POST['from_email'];
            $message = $_POST['message'];
            $subject = "Message depuis le site Vide Grenier";
        
            $mail = new PHPMailer(true);
        
            try {
                // Paramètres du serveur SMTP (exemple Gmail)
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'ton.email@gmail.com'; // à remplacer
                $mail->Password   = 'ton_mot_de_passe_app'; // mot de passe ou token d'app
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
        
                // Destinataires
                $mail->setFrom($from);
                $mail->addAddress($to);
        
                // Contenu
                $mail->isHTML(false);
                $mail->Subject = $subject;
                $mail->Body    = $message;
        
                $mail->send();
                echo 'Message envoyé avec succès.';
            } catch (Exception $e) {
                echo "Erreur lors de l'envoi du message: {$mail->ErrorInfo}";
            }
        
            exit;
        }
    }
}

