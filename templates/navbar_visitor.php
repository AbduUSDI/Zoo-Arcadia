<!-- Utilisation d'une navbar classique Bootstrap 5 en y ajoutant le bouton de connexion, il sera en "déconnexion" si l'utilisateur est déjà connecté. -->

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <a class="navbar-brand" href="index.php">Zoo Arcadia</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="services.php">Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="habitats.php">Habitats</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contact.php">Contact</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php#avis">Avis</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="animals.php">Animaux</a>
            </li>

            <!-- Ici pour le bouton de déconnexion : utilisation d'un " if/else"-->

            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin/logout.php">Déconnexion</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Connexion</a>
                </li>
            <?php endif; ?>

            <!-- Ici utilisation de if pour afficher un bouton seulement si l'utilisateur ayant le role en question est connecté, sinon rien ne s'affichera -->
            
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1): ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin/index.php">Mon espace administrateur</a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2): ?>
                <li class="nav-item">
                    <a class="nav-link" href="employee/index.php">Mon espace employé</a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 3): ?>
                <li class="nav-item">
                    <a class="nav-link" href="vet/index.php">Mon espace vétérinaire</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
