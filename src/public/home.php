<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();  
    session_destroy(); 
    header('Location: login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Protection CSRF : Générer un token CSRF s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inclure les fichiers nécessaires
require '../../vendor/autoload.php';

// Connexion à la base de données
$db = (new \Database\DatabaseConnection())->connect();

$mongoConnection = new Database\MongoDBConnection();
$clickCollection = $mongoConnection->getCollection(collectionName: 'clicks');

// Initialisation des repositories
$habitatRepository = new Repositories\HabitatRepository($db);
$reviewRepository = new Repositories\ReviewRepository($db);
$zooHoursRepository = new Repositories\ZooHoursRepository($db);
$animalRepository = new Repositories\AnimalRepository($db);
$clickRepository = new Repositories\ClickRepository($clickCollection);
$clickService = new Services\ClickService($clickRepository);
$styleRepository = new Repositories\StyleRepository();
$scriptRepository = new Repositories\ScriptRepository();

// Initialisation des services
$habitatService = new Services\HabitatService($habitatRepository);
$reviewService = new Services\ReviewService($reviewRepository);
$zooHoursService = new Services\ZooHoursService($zooHoursRepository);
$animalService = new Services\AnimalService($animalRepository, $clickRepository);

// Initialisation des contrôleurs
$habitatController = new Controllers\HabitatController($habitatService);
$reviewController = new Controllers\ReviewController($reviewService);
$zooHoursController = new Controllers\ZooHoursController($zooHoursService);
$animalController = new Controllers\AnimalController($animalService, $clickService);

// Récupérer les données nécessaires via les contrôleurs
$habitats = $habitatController->getAllHabitats();
$approvedReviews = $reviewController->getApprovedReviews();
$hours = $zooHoursController->getAllHours();
$topAnimals = $animalController->getTopThreeAnimalsByClicks();
$script = $scriptRepository->homeScript();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }
}

// Inclure les fichiers de template
include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6); border-radius: 15px; padding: 20px;">
<div class="containerr my-5" style="background: linear-gradient(to right, #ffffff, #ccedb6); border-radius: 15px; padding: 20px;">
    <div class="row align-items-center mb-5">
        <div class="col-md-5">
            <img src="../../assets/image/Welcomearcadia.webp" class="img-fluid rounded shadow mb-4" alt="Image 1">
        </div>
        <div class="col-md-7">
            <h1 class="mb-4">Bienvenue au Zoo Arcadia</h1>
            <p>Situé depuis 1960 à proximité de la légendaire forêt de Brocéliande, dans la région enchanteresse de la Bretagne, le Zoo Arcadia s'est établi comme un sanctuaire dédié à la conservation et à la préservation de la faune mondiale. Depuis ses modestes débuts, il a évolué pour devenir un pilier de l'éducation environnementale et de la sensibilisation à la biodiversité.</p>
            <p>Les vastes étendues du Zoo Arcadia abritent une myriade d'animaux, offrant aux visiteurs une immersion totale dans les merveilles de la nature. Avec des habitats soigneusement aménagés pour refléter les environnements naturels d'origine, dont la savane africaine, la jungle amazonienne et les marais tropicaux, chaque coin du zoo est une invitation à l'aventure.</p>
        </div>
    </div>
    <div class="row align-items-center mb-5 flex-md-row-reverse">
        <div class="col-md-5">
            <img src="../../assets/image/habitatindex.webp" class="img-fluid rounded shadow mb-4" alt="Image 2">
        </div>
        <div class="col-md-7">
            <p>Les vastes plaines de la savane accueillent les majestueux lions, les éléphants paisibles et les girafes gracieuses, offrant aux visiteurs un aperçu de la vie sauvage africaine. Dans la jungle dense, les singes espiègles se balancent d'arbre en arbre, les jaguars se faufilent dans les ombres et les oiseaux tropicaux colorent le ciel de leurs plumes éclatantes.</p>
            <p>Les marais tranquilles abritent une multitude d'espèces, des crocodiles somnolents aux hérons élégants, tandis que les tortues glissent silencieusement à travers les eaux calmes. Chaque habitat est conçu pour offrir aux animaux un environnement naturel et stimulant, favorisant leur bien-être et leur épanouissement.</p>
        </div>
    </div>
    <div class="row align-items-center mb-5">
        <div class="col-md-5">
            <img src="../../assets/image/respect.webp" class="img-fluid rounded shadow mb-4" alt="Image 3">
        </div>
        <div class="col-md-7">
            <p>À travers des initiatives de conservation et des programmes éducatifs, le Zoo Arcadia s'efforce de sensibiliser le public à l'importance de la protection de la faune et de la flore. Des visites guidées, des présentations interactives et des rencontres avec les gardiens permettent aux visiteurs de découvrir de près la beauté et la diversité du monde animal, tout en apprenant les défis auxquels ces espèces sont confrontées dans la nature.</p>
            <p>Que vous soyez un amateur de la nature passionné ou simplement en quête d'une escapade familiale inoubliable, le Zoo Arcadia promet une expérience immersive et enrichissante pour les visiteurs de tous âges. Entrez dans un monde où la magie de la nature prend vie et où chaque visite est une aventure à part entière.</p>
        </div>
    </div>
</div>
    <!-- Section Habitats -->
    <div>
        <hr>
        <h2 class="text-center">Nos Habitats</h2>
        <hr>
        <div class="row">
            <?php foreach ($habitats as $habitat): ?>
                <div class="col-md-6">
                    <div class="card mb-4 shadow-sm">
                        <img class="card-img-top" src="../../assets/uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($habitat['name']); ?></h5>
                            <p class="card-text"><?php echo $habitat['description']; ?></p>
                            <a href="index.php?page=habitat&id=<?php echo $habitat['id']; ?>" class="btn btn-success btn-block">Voir les habitants</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<!-- Section pour les 3 animaux les plus cliqués -->
<div class="container my-5" style="background: linear-gradient(to right, #ffffff, #ccedb6); border-radius: 15px; padding: 20px;">
    <h2 class="text-center">Les top 3 des animaux les plus vus</h2>
    <hr>
    <div class="row">
        <?php foreach ($topAnimals as $animal): ?>
            <div class="col-md-4">
                <div class="card-rounded mb-4 shadow-sm">
                    <img class="card-img-top img-fluid" src="../../assets/uploads/<?php echo htmlspecialchars($animal['image']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                        <p class="card-text">Nombre de clics : <?php echo htmlspecialchars($animal['clicks']); ?></p>
                        <a href="index.php?page=animal&id=<?php echo $animal['id']; ?>" class="btn btn-success btn-block" onclick="registerClick(<?php echo $animal['id']; ?>)">En savoir plus</a>
                        </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<!-- Section Horaires d'ouverture -->
<div>
    <h1 id="openhours" style="color: transparent">.</h1>
    <br>
    <hr>
    <h2 class="text-center">Horaires d'ouverture du Zoo</h2>
    <hr>
    <div class="row">
        <?php foreach ($hours as $hour): ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($hour['day']); ?></h5>
                        <p class="card-text">
                            <?php 
                            if ($hour['open_time'] === '00:00:00' && $hour['close_time'] === '00:00:00') {
                                echo '<span class="badge badge-danger">Fermé</span>';
                            } else {
                                echo '<span class="badge badge-success">Ouvert</span><br>';
                                echo '<strong>' . htmlspecialchars(substr($hour['open_time'], 0, 5)) . ' - ' . htmlspecialchars(substr($hour['close_time'], 0, 5)) . '</strong>';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    <!-- Section Laissez un Avis -->
    <div>
        <hr>
        <h2 class="text-center">Laissez un Avis</h2>
        <hr>
        <div id="avis" class="col-md-8 mx-auto">
            <form action="submit_review.php" method="POST" class="mt-5">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="mb-3">
                    <label for="pseudo" class="form-label">Pseudo:</label>
                    <input type="text" class="form-control" id="pseudo" name="pseudo" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Objet:</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="review_text" class="form-label">Texte de l'avis:</label>
                    <textarea class="form-control" id="review_text" name="review_text" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-success btn-block">Envoyer</button>
            </form>
        </div>
    </div>

    <!-- Section Avis des visiteurs -->
    <div>
        <hr>
        <h2 class="text-center">Avis des Visiteurs</h2>
        <hr>
        <div class="containerr mt-5">
            <div class="row">
                <?php foreach ($approvedReviews as $review): ?>
                    <?php if (!empty($review['subject'])): ?>
                        <div class="col-md-6">
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title" style="color: green;"><?php echo htmlspecialchars($review['subject'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($review['review_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-right text-muted"><?php echo htmlspecialchars(date('d/m/Y', strtotime($review['created_at'])), ENT_QUOTES, 'UTF-8'); ?> par <strong><?php echo htmlspecialchars($review['visitor_name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php 

echo $script;

?>
<?php include '../../src/views/templates/footer.php'; ?>
