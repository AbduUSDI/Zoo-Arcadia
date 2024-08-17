# Zoo Arcadia

Bienvenue sur le dépôt GitHub du projet Zoo Arcadia, une application web développée pour la gestion et la visualisation des informations des animaux d'un zoo.

## Description du Projet

Zoo Arcadia permet aux visiteurs de découvrir les animaux et leurs habitats, et offre une interface de gestion pour les employés et les vétérinaires du zoo.

## Technologies Utilisées

- **HTML, CSS, JavaScript** : Pour l'interface utilisateur.
- **PHP** : Scripting côté serveur.
- **MySQL** : Base de données relationnelle.
- **MongoDB** : Base de données non relationnelle.
- **Bootstrap** : Design responsive.
- **Axios** : Scripting côté client.
- **AJAX** : Scripting coté client.

## Environnement de Développement

- **XAMPP** : Serveur local Apache et MySQL.
- **Visual Studio Code, Cursor** : Éditeurs de code.
- **Composer** : Gestionnaire de dépendances PHP.
- **Git** : Contrôleur de version.
- **MongoDBCompass** : Base de données non relationnelle et aussi **MongoDB Atlas** pour la version déployée.

## Structure du Projet

- **assets/** -> Contient les documents **CSS** ; **JavaScript** ; **Image et Uploads** ; **favicon.ico**.
- **docs/** -> Dossier pour regrouper la gestion de projet, les diagrammes, la charte graphique et le manuel d'utilisation.
- **src/** -> Contient tout le code source du projet en respectant les principes SOLID : les database, le views(templates, espaces), les interfaces, les repositories, les controllers et les services enfin le dossier public.
- **index.php** -> Ce fichier redirige automatiquement vers le fichier index.php dans public.

## Installation

1. Clonez le dépôt : `git clone https://github.com/AbduUSDI/Zoo-Arcadia-New`.
3. Configurez XAMPP après l'avoir installé sur le site officiel.
4. Après avoir configuré XAMPP, vous avez besoin de créer la variable d'environnement dans vos réglages Windows
5. Une fois fait, il faut aller sur l'application XAMPP Control Panel et l'executer en tant qu'administrateur pour éviter tout conflit, le logiciel affichera alors plusieurs logiciels à ouvrir, ouvrez Apache (le serveur) ensuite ouvrez MySQL. Cliquez ensuite sur "Admin" sur la ligne MySQL.
6. La page http://localhost/phpmyadmin/ s'ouvrira sur votre navigateur par défaut ensuite cliquez sur "Importer".
7. Importez la base de données MySQL en utilisant le fichier zoo_arcadia.sql qui contient tout le code SQL pour créer la BDD complète contenant ses tables et ses valeurs.
8. Vérifiez que la base de donnée contient bien les tables du projet.
(8 bis. Télécharger le code source du projet en .zip et décompresser le tout dans un dossier nommé "zoo_arcadia" qui devra être dans votre répertoire "htdocs" qui se trouve dans le dossier "xampp" (tout dépend de où vous l'avez positionner pendant votre installation, si par défaut : le dossier se trouve dans "utilisateur" dans le Disque local).) 
9. Ouvrez un invité de commandes : aller à la racine du projet et installer les dépendances comme :  Créer le fichier composer.json en faisant : `composer init`  puis ensuite télécharger les dépendances :     `composer require mongodb/mongodb` ; `composer require phpmailer/phpmailer`
10. Une fois les dépendances installées il faut aller sur le dossier "ext" de PHP, l'adresse exacte est par défaut : `"C:\xampp\php\ext"` transférer le fichier `"php_mongodb.dll"` que vous pouvez télécharger grâce à ce lien : [https://pecl.php.net/](https://pecl.php.net/package/mongodb/1.18.1/windows). Télécharger le bon fichier selon votre version php. Après avoir déplacer le fichier correspondant dans le répertoire "ext" de "php", aller sur le fichier `"php.ini"` et chercher la ligne "extension" en utilisant la barre de recherche (raccourci CTRL+F) ajouter la ligne `"extension=php_mongodb.dll"`.
11. N'oublier pas de créer une base de données et une collection MongoDB dans MongoDB Compass ou Atlas, une fois fait si vous êtes sur le port par défaut vous devrez avoir comme URI, databaseName et collections (clicks) :  `$uri = '"mongodb://localhost:27017"` ; `"$databaseName = 'zoo_arcadia_click_counts';"` 
12. Vous pouvez maintenant lancez l'application via votre serveur local en utilisant l'url : http://localhost/Zoo-Arcadia/index.php sur votre navigateur par défaut.

## Utilisation

Naviguez dans l'application en utilisant les différents rôles pour explorer les fonctionnalités spécifiques à chaque utilisateur.
Un fichier `Manuel d'utilisation Zoo Arcadia.pdf` est dans le dépôt Github, dans le dossier `docs`, il explique toutes les fonctionnalités et comment y accéder.

## Contact

Pour des questions ou suggestions, contactez moi via GitHub.

J'espère que vous trouverez ce projet utile pour comprendre la gestion d'un zoo via une application web.
