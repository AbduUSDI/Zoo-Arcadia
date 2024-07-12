<?php
$password = 'vet123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
// Fichier pour hasher un mot de passe et le mettre manuellement dans la BDD, fichier d'entrainement