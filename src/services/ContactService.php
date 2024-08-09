<?php
namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactService {
    public function sendContactEmail($name, $email, $subject, $message) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';  // Serveur SMTP d'Office 365
            $mail->SMTPAuth = true;
            $mail->Username = 'Karausdi77@outlook.fr'; // Adresse email utilisée pour l'authentification
            $mail->Password = 'Abdufufu2525+';     // Mot de passe pour l'authentification
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Adresse email d'envoi
            $mail->setFrom('Karausdi77@outlook.fr', 'Service client - Contact');

            // Adresse du destinataire
            $mail->addAddress('abdu.usdi@gmail.com');

            // Contenu du message
            $mail->isHTML(true);
            $mail->Charset = 'UTF-8'; // Spécifier l'encodage UTF-8
            $mail->Subject = $subject;

            // Numéro de message unique
            $messageId = uniqid('msg_', true);

            // Corps du message incluant l'adresse email du formulaire
            $mail->Body = "<p><strong>Numéro de message:</strong> $messageId</p>
                           <p><strong>Nom:</strong> $name</p>
                           <p><strong>Email:</strong> $email</p>
                           <p>$message</p>";
            $mail->AltBody = "Numéro de message: $messageId\n
                              Nom: $name\n
                              Email: $email\n
                              Message: $message";

            $mail->send();
            return ['success' => true, 'message' => 'Votre message a été envoyé avec succès.'];
        } catch (Exception $exception) {
            return ['success' => false, 'message' => "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}"];
        }
    }
}
