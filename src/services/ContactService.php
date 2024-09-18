<?php 
namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactService {
    public function sendContactEmail($name, $email, $subject, $message) {
        $mail = new PHPMailer(true);
    
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'abdu.usdi@hotmail.fr';
            $mail->Password = 'Abdufufu2525+';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('abdu.usdi@hotmail.fr', 'Service client Arcadia - Contact');
            $mail->addAddress('abdu.usdi@hotmail.fr');
    
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
    
            // Décodage des entités HTML pour le sujet
            $decodedSubject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
            $mail->Subject = '=?UTF-8?B?' . base64_encode($decodedSubject) . '?=';
    
            // Décodage des entités HTML pour le contenu du message
            $decodedMessage = html_entity_decode($message, ENT_QUOTES, 'UTF-8');
    
            // Numéro de message unique
            $messageId = uniqid('msg_', true);
    
            // Ajout de styles en ligne et d'une image au corps de l'e-mail
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333; background-color: #f2f2f2; padding: 20px;'>
                    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden;'>
                        <div style='padding: 20px;'>
                            <h2 style='color: #006400;'>Contact Arcadia</h2>
                            <p><strong style='color: #333;'>Numéro de message:</strong> <span style='color: #006400;'>$messageId</span></p>
                            <p><strong style='color: #333;'>Nom:</strong> <span style='color: #006400;'>" . htmlentities($name, ENT_QUOTES, 'UTF-8') . "</span></p>
                            <p><strong style='color: #333;'>Email:</strong> <a href='mailto:" . htmlentities($email, ENT_QUOTES, 'UTF-8') . "' style='color: #006400;'>" . htmlentities($email, ENT_QUOTES, 'UTF-8') . "</a></p>
                            <div style='margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;'>
                                <p style='font-size: 14px; color: #333;'>" . nl2br(htmlentities($decodedMessage, ENT_QUOTES, 'UTF-8')) . "</p>
                            </div>
                        </div>
                    </div>
                </div>";
    
            $mail->AltBody = "Numéro de message: $messageId\n
                              Nom: " . $name . "\n
                              Email: " . $email . "\n
                              Message: " . $decodedMessage;
    
            $mail->send();
            return ['success' => true, 'message' => 'Votre message a été envoyé avec succès.'];
        } catch (Exception $exception) {
            return ['success' => false, 'message' => "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}"];
        }
    }    
}



