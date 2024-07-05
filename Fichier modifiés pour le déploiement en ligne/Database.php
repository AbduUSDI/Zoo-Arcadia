<?php
/**
 * Classe pour établir une connexion à la base de données
 */
class Database {
    private $hote = 'e0yll4.stackhero-network.com';
    private $nom_base_de_donnees = 'zoo_arcadia';
    private $identifiant = 'Abdurahman';
    private $mot_de_passe = 'Abdufufu2525+';
    private $port = '5040';
    private $connexion;

    public function connect() {
        $this->connexion = null;

        try {
            $this->connexion = new PDO(
                "mysql:host=" . $this->hote . ";port=" . $this->port . ";dbname=" . $this->nom_base_de_donnees,
                $this->identifiant,
                $this->mot_de_passe
            );

            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $erreur) {
            // Logger l'erreur et lancer une exception personnalisée
            error_log("Erreur de connexion à la base de données : " . $erreur->getMessage());
            throw new Exception("Impossible de se connecter à la base de données");
        }

        return $this->connexion;
    }
}