<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['forgotEmail'];
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 2; // Active le mode debug
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'abdu.usdi@gmail.com';
        $mail->Password = 'toutpourunnouveaune';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('abdu.usdi@gmail.com', 'USDI Abdurahman');
        $mail->addAddress($email);
        $mail->Subject = 'Réinitialisation de mot de passe';
        $mail->msgHTML('Bonjour, pour réinitialiser votre mot de passe, veuillez cliquer sur ce lien : <a href="lien_de_réinitialisation">Réinitialiser mot de passe</a>');

        $mail->send();
        echo 'Email envoyé avec succès.';
    } catch (Exception $e) {
        echo 'Erreur lors de l\'envoi de l\'email : ' . $mail->ErrorInfo;
    }
}