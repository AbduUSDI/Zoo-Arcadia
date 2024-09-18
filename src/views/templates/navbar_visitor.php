<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <a class="navbar-brand" href="index.php?page=home">
        <img src="/Zoo-Arcadia-New/assets/image/favicon.jpg" width="32px" height="32px" alt="Zoo Arcadia"> Zoo Arcadia
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=home">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=services">Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=habitats">Habitats</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=contact">Contact</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php#avis">Avis</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=animals">Animaux</a>
            </li>
            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=logout">Déconnexion</a>
                </li>
                <?php if ($_SESSION['user']['role_id'] == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../views/admin/index.php">Mon espace administrateur</a>
                    </li>
                <?php elseif ($_SESSION['user']['role_id'] == 2): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../views/employee/index.php">Mon espace employé</a>
                    </li>
                <?php elseif ($_SESSION['user']['role_id'] == 3): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../views/vet/index.php">Mon espace vétérinaire</a>
                    </li>
                <?php endif; ?>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=login">Connexion</a>
                </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
            <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background-color: #ccedb6; border-radius: 5px;">
        <?php
        // Breadcrumbs dynamiques basés sur le paramètre 'page'
        $breadcrumbs = [['Accueil', 'index.php?page=home']];
        
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                case 'services':
                    $breadcrumbs[] = ['Services', 'index.php?page=services'];
                    break;
                case 'habitats':
                    $breadcrumbs[] = ['Habitats', 'index.php?page=habitats'];
                    break;
                case 'animals':
                    $breadcrumbs[] = ['Animaux', 'index.php?page=animals'];
                    break;
                case 'contact':
                    $breadcrumbs[] = ['Contact', 'index.php?page=contact'];
                    break;
                case 'animal':
                    $breadcrumbs[] = ['Animaux', 'index.php?page=animals'];
                    $breadcrumbs[] = ['Détails de l\'animal', 'index.php?page=animal'];
                    break;
                case 'habitat':
                    $breadcrumbs[] = ['Habitats', 'index.php?page=habitats'];
                    $breadcrumbs[] = ['Détails de l\'habitat', 'index.php?page=habitat'];
                    break;
            }
        }

        foreach ($breadcrumbs as $key => $breadcrumb) {
            if ($key == count($breadcrumbs) - 1) {
                echo '<li class="breadcrumb-item active" aria-current="page" style="color: #333333; font-weight: bold;">' . htmlspecialchars($breadcrumb[0]) . '</li>';
            } else {
                echo '<li class="breadcrumb-item"><a href="' . htmlspecialchars($breadcrumb[1]) . '" style="color: #006400;">' . htmlspecialchars($breadcrumb[0]) . '</a></li>';
            }
        }
        ?>
    </ol>
</nav>

            </li>
        </ul>
    </div>
</nav>