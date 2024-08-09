<?php
namespace Database;

use PDO;
use PDOException;

class DatabaseConnection {
    private $serveur = 'localhost';
    private $database = 'zoo_arcadia';
    private $identifiant = 'Abdurahman';
    private $mot_de_passe = 'Abdufufu2525+';
    private $connexion;

    public function connect() {
        $this->connexion = null;

        try {
            $this->connexion = new PDO(
                "mysql:host=" . $this->serveur . ";dbname=" . $this->database, 
                $this->identifiant, 
                $this->mot_de_passe
            );
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $erreur) {
            echo "Erreur de connexion à la base de donnée : " . $erreur->getMessage();
        }
        return $this->connexion;
    }
}
