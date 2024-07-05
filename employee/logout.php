<?php

// Fichier pour détruire la session et ensuite déconnecter l'utilisateur

session_start();
session_destroy();
header('Location: index.php');
exit;
