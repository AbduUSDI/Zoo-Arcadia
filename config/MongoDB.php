<?php
require '../../vendor/autoload.php';

use MongoDB\Client;
/** Classe MongoDB pour toutes les méthode et le constructeur en rapport avec MongoDB Atlas */
class MongoDB {
    private $mongoClient;
    private $mongoCollection;

    // Le constructeur ici contient l'uri et la BDD pour exécuter le try/catch afin d'avoir la connexion à la BDD immédiatement après son appel dans un autre fichier

    public function __construct() {
        $uri = 'mongodb://localhost:27017';
        $databaseName = 'zoo_arcadia_click_counts';

        try {
            $this->mongoClient = new Client($uri);
            $this->mongoCollection = $this->mongoClient->selectDatabase($databaseName)->clicks;
        } catch (Exception $erreur) {
            error_log("Erreur de connexion à MongoDB : " . $erreur->getMessage());
            throw new Exception("Impossible de se connecter à la base de données MongoDB");
        }
    }
    public function recordClick($animal_id) {
        try {
            // Paramètres : animal_id ici pour l'élement à sélectionner et inc pour incrémenter un clic dans le data existant, si il n'existe pas alors il créer un data pour l'animal_id choisi grâce à upsert
            $this->mongoCollection->updateOne(  // Mise à jour d'un seul document, celui sélectionné
                ['animal_id' => $animal_id],
                ['$inc' => ['clicks' => 1]],
                ['upsert' => true]
            );
            echo "Clic enregistré !\n";

        } catch (Exception $erreur) {
            error_log("Erreur lors de l'enregistrement du clic : " . $erreur->getMessage());
            throw new Exception("Erreur lors de l'enregistrement du clic");
        }
    }
    public function getClicks($animal_id) {
        try {
            $click = $this->mongoCollection->findOne(['animal_id' => $animal_id]);
            return $click ? $click['clicks'] : 0;
        } catch (Exception $erreur) {
            error_log("Erreur lors de la récupération des clics : " . $erreur->getMessage());
            throw new Exception("Erreur lors de la récupération des clics");
        }
    }
}