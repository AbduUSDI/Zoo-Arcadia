<?php
/**
 * Classe Database pour établir une connexion à la base de données
 */
class Database {
    private $hôte = 'localhost';
    private $nom_base_de_donnée = 'zoo_arcadia';
    private $identifiant = 'Abdurahman';
    private $mot_de_passe = 'Abdufufu2525+';
    private $connexion;

    public function connect() {

        $this->connexion = null;
    
        try {
            $this->connexion = new PDO(
                "mysql:host=" . $this->hôte . ";dbname=" . $this->nom_base_de_donnée, 
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