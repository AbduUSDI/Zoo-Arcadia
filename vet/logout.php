<?php
session_start();
session_destroy();
header('Location: index.php');
exit;

// Fichier de déconnexion permettant de détruire la session pour éviter des problèmes de connexion au site