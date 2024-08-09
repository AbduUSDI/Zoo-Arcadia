<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <a class="navbar-brand" href="index.php">
        <img src="../../assets/image/favicon.jpg" width="32px" height="32px" alt="Zoo Arcadia"> Zoo Arcadia
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
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
            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../views/admin/logout.php">Déconnexion</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Connexion</a>
                </li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../views/admin/index.php">Mon espace administrateur</a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../views/employee/index.php">Mon espace employé</a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 3): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../views/vet/index.php">Mon espace vétérinaire</a>
                </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-light mb-0">
                        <?php
                        // Définir les chemins des breadcrumbs ici
                        $breadcrumbs = [];

                        // Exemple de génération dynamique des breadcrumbs
                        $current_page = basename($_SERVER['PHP_SELF']);

                        if ($current_page == 'index.php') {
                            $breadcrumbs[] = ['Accueil', 'index.php'];
                        } elseif ($current_page == 'habitats.php') {
                            $breadcrumbs[] = ['Accueil', 'index.php'];
                            $breadcrumbs[] = ['Habitats', 'habitats.php'];
                        } elseif ($current_page == 'animals.php') {
                            $breadcrumbs[] = ['Accueil', 'index.php'];
                            $breadcrumbs[] = ['Animaux', 'animals.php'];
                        } elseif ($current_page == 'services.php') {
                            $breadcrumbs[] = ['Accueil', 'index.php'];
                            $breadcrumbs[] = ['Services', 'services.php'];
                        } elseif ($current_page == 'contact.php') {
                            $breadcrumbs[] = ['Accueil', 'index.php'];
                            $breadcrumbs[] = ['Contact', 'contact.php'];
                        } elseif ($current_page == 'animal.php') {
                            $breadcrumbs[] = ['Accueil', 'index.php'];
                            $breadcrumbs[] = ['Animaux', 'animals.php'];
                            $breadcrumbs[] = ['Détails de l\'animal', 'animal.php'];
                        } elseif ($current_page == 'habitat.php') {
                            $breadcrumbs[] = ['Accueil', 'index.php'];
                            $breadcrumbs[] = ['Habitats', 'habitats.php'];
                            $breadcrumbs[] = ['Détails de l\'habitat', 'habitat.php'];
                        } elseif ($current_page == 'submit_review.php') {
                            $breadcrumbs[] = ['Accueil', 'index.php'];
                            $breadcrumbs[] = ['Laisser un avis', 'submit_review.php'];
                        }

                        foreach ($breadcrumbs as $key => $breadcrumb) {
                            if ($key == count($breadcrumbs) - 1) {
                                echo '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($breadcrumb[0]) . '</li>';
                            } else {
                                echo '<li class="breadcrumb-item"><a href="' . htmlspecialchars($breadcrumb[1]) . '">' . htmlspecialchars($breadcrumb[0]) . '</a></li>';
                            }
                        }
                        ?>
                    </ol>
                </nav>
            </li>
        </ul>
    </div>
</nav>
