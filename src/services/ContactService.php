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
            $mail->Username = 'zoo-arcadia-usdi@hotmail.com';
            $mail->Password = 'Arcadia123+';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('zoo-arcadia-usdi@hotmail.com', 'Service client Arcadia - Contact');
            $mail->addAddress('Karausdi77@outlook.fr');

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            // Décodage des entités HTML pour le sujet
            $decodedSubject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
            $mail->Subject = '=?UTF-8?B?' . base64_encode($decodedSubject) . '?=';

            // Décodage des entités HTML pour le contenu du message
            $decodedMessage = html_entity_decode($message, ENT_QUOTES, 'UTF-8');

            // Numéro de message unique
            $messageId = uniqid('msg_', true);

            // Construction du corps du message
            $mail->Body = "<p><strong>Numéro de message:</strong> $messageId</p>
                           <p><strong>Nom:</strong> " . htmlentities($name, ENT_QUOTES, 'UTF-8') . "</p>
                           <p><strong>Email:</strong> " . htmlentities($email, ENT_QUOTES, 'UTF-8') . "</p>
                           <p>" . nl2br(htmlentities($decodedMessage, ENT_QUOTES, 'UTF-8')) . "</p>";
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



