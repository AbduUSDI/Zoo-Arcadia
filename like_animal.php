<?php
require 'functions.php';

$db = (new Database())->connect();
$animal = new Animal($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = $_POST['animal_id'];
    $animal->ajouterLike($animal_id);
    header('Location: animals.php');
    exit;
}