<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();  
    session_destroy();
    header('Location: /Zoo-Arcadia-New/login');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Protection CSRF : Générer un token CSRF s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inclure les fichiers nécessaires
require '../../vendor/autoload.php';

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6); border-radius: 15px; padding: 20px;">
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <img src="/Zoo-Arcadia-New/assets/image/broceliande.webp" class="img-fluid rounded shadow mb-4" alt="Forêt de Brocéliande">
        </div>
        <div class="col-md-6">
            <h1 class="mb-4">À propos de Zoo Arcadia</h1>
            <p>
                Situé depuis 1960 à proximité de la légendaire forêt de Brocéliande, dans la région enchanteresse de la Bretagne, le Zoo Arcadia s'est établi comme un sanctuaire dédié à la conservation et à la préservation de la faune mondiale. 
                Depuis ses modestes débuts, il a évolué pour devenir un pilier de l'éducation environnementale et de la sensibilisation à la biodiversité.
            </p>
        </div>
    </div>

    <div class="row align-items-center mb-5 flex-md-row-reverse">
        <div class="col-md-6">
            <img src="/Zoo-Arcadia-New/assets/image/savaneapropos.webp" class="img-fluid rounded shadow mb-4" alt="Savane et animaux">
        </div>
        <div class="col-md-6">
            <h2 class="mb-4">Des habitats exceptionnels</h2>
            <p>
                Les vastes plaines de la savane accueillent les majestueux lions, les éléphants paisibles et les girafes gracieuses, offrant aux visiteurs un aperçu de la vie sauvage africaine.
                Dans la jungle dense, les singes espiègles se balancent d'arbre en arbre, tandis que les jaguars et les oiseaux tropicaux colorent le ciel de leurs plumes éclatantes.
            </p>
        </div>
    </div>

    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <img src="/Zoo-Arcadia-New/assets/image/maraisapropos.webp" class="img-fluid rounded shadow mb-4" alt="Marais et animaux">
        </div>
        <div class="col-md-6">
            <h2 class="mb-4">Conservation et éducation</h2>
            <p>
                Les marais tranquilles abritent une multitude d'espèces, des crocodiles somnolents aux hérons élégants, tandis que les tortues glissent silencieusement à travers les eaux calmes.
                Le Zoo Arcadia propose des initiatives de conservation et des programmes éducatifs, permettant aux visiteurs de découvrir de près la beauté et la diversité du monde animal.
            </p>
        </div>
    </div>

    <div class="row align-items-center mb-5">
        <div class="col-md-12">
            <h2 class="text-center mb-4">Un sanctuaire pour la faune mondiale</h2>
            <p>
                Que vous soyez un amateur de la nature passionné ou simplement en quête d'une escapade familiale inoubliable, le Zoo Arcadia promet une expérience immersive et enrichissante pour les visiteurs de tous âges.
                Entrez dans un monde où la magie de la nature prend vie et où chaque visite est une aventure à part entière.
            </p>
        </div>
    </div>
    <!-- Section de la vidéo de présentation -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Vidéo de présentation du Zoo Arcadia</h2>
            <div class="video-container" data-splash="true">
                <video data-title="titre" controls="controls" wmode="transparent" type="video/mp4" width="100%" height="400" 
                src="/Zoo-Arcadia-New/assets/image/presentationvideo.mp4" 
                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></video>
            </div>
        </div>
    </div>
</div>

<?php
include '../../src/views/templates/footer.php';
?>
