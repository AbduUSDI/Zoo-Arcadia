<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoo Arcadia - Mentions légales</title>
    <link rel="icon" href="/Zoo-Arcadia-New/assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/Zoo-Arcadia-New/assets/css/styles.css">
    <!-- Inclusion de Bootstrap 5 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Inclusion de FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Style personnalisé -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1, h2 {
            color: #2c3e50;
            font-weight: bold;
        }

        h1 {
            font-size: 2.5rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.75rem;
            margin-top: 40px;
            color: #3498db;
            border-left: 4px solid #3498db;
            padding-left: 15px;
        }

        p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #2c3e50;
        }

        /* Ajout de padding et de marges pour les sections */
        section {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .icon-header {
            margin-right: 10px;
            color: #3498db;
        }

        /* Bouton plus stylisé */
        #toggleContentBtn {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #toggleContentBtn:hover {
            background: linear-gradient(to right, black, #ccedb6);
        }
    </style>
</head>
<body>
    <?php include_once '../views/templates/navbar_visitor.php' ?>
    
    <div class="container mt-5">
        <!-- Bouton pour basculer entre contenu fictif et contenu réel -->
        <button id="toggleContentBtn" class="btn mb-4" onclick="toggleContent()">Afficher la version réelle</button>

        <header>
            <h1><i class="fas fa-info-circle icon-header"></i> Mentions légales</h1>
        </header>

        <!-- Contenu fictif qui sera modifié dynamiquement -->
        <section id="contentSection">
            <h2><i class="fas fa-user icon-header"></i> Éditeur du site</h2>
            <p>
                Ce site est un projet fictif réalisé dans le cadre d'un projet scolaire.<br>
                <strong>Nom de l'éditeur :</strong> José Sanchez (propriétaire fictif)<br>
                <strong>Adresse email :</strong> zooarcadiausdi@gmail.com
            </p>

            <h2><i class="fas fa-server icon-header"></i> Hébergement</h2>
            <p>
                <strong>Le site est hébergé par :</strong><br>
                <strong>Nom de l'hébergeur :</strong> IONOS<br>
                <strong>Adresse :</strong> 1&1 IONOS SARL, 7 place de la Gare, 57200 Sarreguemines<br>
                <strong>Téléphone :</strong> +33 9 70 80 89 11
            </p>

            <h2><i class="fas fa-user-graduate icon-header"></i> Responsable du projet</h2>
            <p>
                Ce projet fictif a été réalisé par Abdurahman USDI dans le cadre d'une formation scolaire.<br>
                <strong>Email :</strong> abdu.usdi@gmail.com
            </p>

            <h2><i class="fas fa-copyright icon-header"></i> Propriété intellectuelle</h2>
            <p>
                Le contenu de ce site est fictif et n'est pas destiné à une utilisation commerciale. Toute reproduction du contenu du site est interdite sans autorisation.
            </p>

            <h2><i class="fas fa-exclamation-triangle icon-header"></i> Responsabilité</h2>
            <p>
                Étant un projet fictif, ce site ne peut être tenu responsable de tout dommage ou inexactitude. Les informations présentes sont créées à des fins éducatives uniquement.
            </p>
        </section>
    </div>

    <footer id="footerId" class="bg-light text-center text-lg-start mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
        <!-- Section Contact et Navigation -->
        <div class="containerr p-4">
            <div class="row">
                <div class="col-md-4">
                    <h5>Nous contacter</h5>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/contact">
                                <img src="/Zoo-Arcadia-New/assets/image/lettre.png" width="32px" height="32px" alt="Nous contacter"> Nous contacter
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/home#openhours">
                                <img src="/Zoo-Arcadia-New/assets/image/ouvert.png" width="32px" height="32px" alt="Nos horaires"> Nos horaires
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/aproposdenous">
                                <img src="/Zoo-Arcadia-New/assets/image/a-propos-de-nous.png" width="32px" height="32px" alt="A propos de nous"> A propos de nous
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/mentions-legales">Mentions légales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/politique-de-confidentialite">Politique de confidentialité</a>
                        </li>
                    </ul>
                </div>
    
                <!-- Section Plan Google Maps -->
                <div class="col-md-4">
                    <h5>Adresse</h5>
                    <p>Forêt de Brocéliande, 35380 Paimpont</p>
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2665.098412817444!2d-2.2466591491221856!3d48.00743897921212!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480ed92e0dbf4477%3A0x9e59e8de9302db5a!2s35380%20Paimpont%2C%20France!5e0!3m2!1sen!2sfr!4v1695648726871!5m2!1sen!2sfr" 
                        width="100%" 
                        height="200" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
    
                <!-- Section Réseaux sociaux -->
                <div class="col-md-4">
                    <h5>Suivez-nous</h5>
                    <div class="d-flex justify-content-center">
                        <a href="https://twitter.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-x-twitter fa-2x"></i>
                        </a>
                        <a href="https://facebook.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook-f fa-2x"></i>
                        </a>
                        <a href="https://snapchat.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-snapchat-ghost fa-2x"></i>
                        </a>
                        <a href="https://instagram.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-instagram fa-2x"></i>
                        </a>
                        <a href="https://github.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-github fa-2x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Footer Copyright -->
        <div class="containerr p-4">
            <p class="text-secondary">
                <img src="/Zoo-Arcadia-New/assets/image/favicon.jpg" width="32px" height="32px" alt="Zoo Arcadia Favicon"> &copy; 2024 Zoo Arcadia. Tous droits réservés.
            </p>
        </div>
    </footer>

    <!-- Inclusion de jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Inclusion de Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

    <!-- Inclusion de Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Inclusion de AXIOS -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Inclusion des scripts personnalisés -->
    <script src="/Zoo-Arcadia-New/assets/js/scripts.js"></script>

    <!-- Script pour basculer entre le contenu fictif et réel -->
    <script>
        function toggleContent() {
            const contentSection = document.getElementById("contentSection");
            const toggleButton = document.getElementById("toggleContentBtn");

            if (toggleButton.innerHTML === "Afficher la version réelle") {
                contentSection.innerHTML = `
                    <h2><i class="fas fa-user icon-header"></i> Éditeur du site</h2><br>
                    <p>
                        Nom de l'éditeur : Zoo Arcadia<br>
                        Adresse : Forêt de Brocéliande, 35380 Paimpont<br>
                        Email : zooarcadiausdi@gmail.com<br>
                        Téléphone : +33 1 23 45 67 89
                    </p>

                    <h2><i class="fas fa-server icon-header"></i> Hébergement</h2><br>
                    <p>
                        Le site est hébergé par :<br>
                        Nom de l'hébergeur : IONOS<br>
                        Adresse : 1&1 IONOS SARL, 7 place de la Gare, 57200 Sarreguemines<br>
                        Téléphone : +33 9 70 80 89 11
                    </p>

                    <h2><i class="fas fa-copyright icon-header"></i> Propriété intellectuelle</h2><br>
                    <p>
                        Le contenu du site www.zooarcadia.fr (textes, images, vidéos, etc.) est la propriété exclusive de Zoo Arcadia. Toute reproduction sans autorisation préalable est interdite.
                    </p>

                    <h2><i class="fas fa-exclamation-triangle icon-header"></i> Responsabilité</h2><br>
                    <p>
                        Zoo Arcadia ne peut être tenu responsable des erreurs techniques ou des interruptions du site.
                    </p>
                `;
                toggleButton.innerHTML = "Afficher la version fictive";
            } else {
                contentSection.innerHTML = `
                    <h2><i class="fas fa-user icon-header"></i> Éditeur du site</h2><br>
                    <p>
                        Ce site est un projet fictif réalisé dans le cadre d'un projet scolaire.<br>
                        Nom de l'éditeur : José Sanchez (propriétaire fictif)<br>
                        Adresse email : zooarcadiausdi@gmail.com
                    </p>

                    <h2><i class="fas fa-server icon-header"></i> Hébergement</h2><br>
                    <p>
                        <strong>Le site est hébergé par :</strong><br>
                        Nom de l'hébergeur : IONOS<br>
                        Adresse : 1&1 IONOS SARL, 7 place de la Gare, 57200 Sarreguemines<br>
                        Téléphone : +33 9 70 80 89 11
                    </p>

                    <h2><i class="fas fa-user-graduate icon-header"></i> Responsable du projet</h2><br>
                    <p>
                        Ce projet fictif a été réalisé par Abdurahman USDI dans le cadre d'une formation scolaire.<br>
                        Email : abdu.usdi@gmail.com
                    </p>

                    <h2><i class="fas fa-copyright icon-header"></i> Propriété intellectuelle</h2><br>
                    <p>
                        Le contenu de ce site est fictif et n'est pas destiné à une utilisation commerciale. Toute reproduction du contenu du site est interdite sans autorisation.
                    </p>

                    <h2><i class="fas fa-exclamation-triangle icon-header"></i> Responsabilité</h2><br>
                    <p>
                        Étant un projet fictif, ce site ne peut être tenu responsable de tout dommage ou inexactitude. Les informations présentes sont créées à des fins éducatives uniquement.
                    </p>
                `;
                toggleButton.innerHTML = "Afficher la version réelle";
            }
        }
    </script>

</body>
</html>
