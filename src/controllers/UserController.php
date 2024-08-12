<?php
namespace Controllers;

use Interfaces\UserServiceInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController {
    private $userService;

    public function __construct(UserServiceInterface $userService) {
        $this->userService = $userService;
    }

    public function getAllUsers() {
        return $this->userService->getAllUsers();
    }

    public function getUserByEmail($email) {
        return $this->userService->getUserByEmail($email);
    }

    public function getUserById($id) {
        return $this->userService->getUserById($id);
    }

    public function addUser($email, $password, $role_id, $username) {
        return $this->userService->addUser($email, $password, $role_id, $username);
    }

    public function updateUser($id, $email, $role_id, $username, $password = null) {
        return $this->userService->updateUser($id, $email, $role_id, $username, $password);
    }

    public function deleteUser($id) {
        return $this->userService->deleteUser($id);
    }

    // Gestion de la réinitialisation du mot de passe
    
    public function initiatePasswordReset($email) {
        $user = $this->userService->getUserByEmail($email);

        if ($user) {
            // Générer un token de réinitialisation
            $token = bin2hex(random_bytes(16));
            
            // Enregistrer le token dans la base de données
            $this->userService->createPasswordResetToken($user['id'], $token);

            // Envoyer l'email avec le lien de réinitialisation
            $this->sendPasswordResetEmail($email, $token);

            return $token;
        } else {
            // Gérer le cas où l'utilisateur n'est pas trouvé
            return false;
        }
    }

    public function verifyPasswordResetToken($token) {
        return $this->userService->verifyPasswordResetToken($token);
    }

    public function resetPassword($token, $newPassword) {
        return $this->userService->resetPassword($token, $newPassword);
    }
    public function handleForgotPasswordRequest($email) {
        try {
            // Appeler la méthode pour initier la réinitialisation du mot de passe
            $resetToken = $this->initiatePasswordReset($email);

            if ($resetToken) {
                // Utiliser PHPMailer pour envoyer l'email
                $this->sendPasswordResetEmail($email, $resetToken);

                return ['success' => true, 'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.'];
            } else {
                return ['success' => false, 'message' => 'Aucun utilisateur trouvé avec cet email.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la tentative de réinitialisation du mot de passe : ' . $e->getMessage()];
        }
    }

    private function sendPasswordResetEmail($email, $token) {
        $mail = new PHPMailer(true);

        try {
            // Configurer SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'Karausdi77@outlook.fr'; // Remplacez par votre email
            $mail->Password = 'Abdufufu2525+'; // Remplacez par votre mot de passe
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinataire
            $mail->setFrom('Karausdi77@outlook.fr', 'Zoo Arcadia');
            $mail->addAddress($email); // Utiliser l'email fourni dans le formulaire

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';  // Ajoutez cette ligne pour forcer l'encodage UTF-8
            $mail->Subject = '=?UTF-8?B?' . base64_encode('Réinitialisation de mot de passe') . '?=';
            
            // Lien de réinitialisation
            $resetLink = 'http://localhost/Zoo-Arcadia-New/src/public/reset_password.php?token=' . urlencode($token);
            $mail->Body = "Bonjour,<br><br>Pour réinitialiser votre mot de passe, veuillez cliquer sur ce lien : <a href=\"$resetLink\">Réinitialiser mot de passe</a>";

            // Envoyer l'email
            $mail->send();
        } catch (Exception $e) {
            // Gérer les erreurs d'envoi
            error_log("Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}");
        }
    }
}
