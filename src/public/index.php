<?php

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: index.php?page=login');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Inclure les fichiers nécessaires
require '../../vendor/autoload.php';

// Paramètre de routage
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

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
    'habitat'
];

// Vérifier si la page demandée est dans la liste des pages autorisées
if (!in_array($page, $pages)) {
    $page = '404'; // Page par défaut en cas de page non trouvée
}

// Inclure le fichier correspondant à la page demandée
try {
    include __DIR__ . "/$page.php";
} catch (Exception $e) {
    echo "Une erreur s'est produite : " . htmlspecialchars($e->getMessage());
}

