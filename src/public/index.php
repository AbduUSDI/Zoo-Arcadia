<?php

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification de la durée d'inactivité
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    // Supprimer toutes les variables de session et détruire la session
    session_unset();
    session_destroy();
    // Redirection vers la page de login
    header('Location: index.php?page=login');
    exit;
}

// Mettre à jour le temps de dernière activité de la session
$_SESSION['LAST_ACTIVITY'] = time();

// Inclure les fichiers nécessaires
require '../../vendor/autoload.php';

// Récupérer le paramètre de routage et le nettoyer
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING) ?? 'home';

// Liste des pages autorisées
$pages = [
    'home',
    'animals',
    'animal',
    'habitats',
    'login',
    'submit_review',
    'forgot_password',
    'reset_password',
    'contact',
    'services',
    'logout',
    'habitat',
    'aproposdenous',
    'mentions-legales',
    'politique-confidentialite',
    '404'
];

// Chemin de base des fichiers des pages
$baseDir = __DIR__;

// Vérifier si la page demandée est dans la liste des pages autorisées
if (!in_array($page, $pages)) {
    $page = '404'; // Rediriger vers une page 404 en cas de page non trouvée
}

// Construire le chemin du fichier à inclure
$filePath = realpath($baseDir . "/$page.php");

// Sécurité supplémentaire : vérifier que le fichier existe dans le répertoire de base
if ($filePath && strpos($filePath, $baseDir) === 0 && file_exists($filePath)) {
    try {
        // Inclure la page demandée
        include $filePath;
    } catch (Exception $e) {
        // Gestion des erreurs avec message propre
        echo "Une erreur s'est produite : " . htmlspecialchars($e->getMessage());
    }
} else {
    // Si le fichier n'existe pas, afficher un message d'erreur
    echo "Une erreur s'est produite : fichier introuvable.";
}