DOCUMENTATION PROJET PHP - Charles Roman
Semaine du 09/05/2016 au 13/05/2016
Correcteur : Olivier Rollet


///// INSTALLATION PROJET /////

Lancer Wamp
Dans la colonne "Outils", cliquez sur "Ajouter un Virtual Host"
Remplissez le premier champ avec l'url que vous souhaitez utiliser. ex : "projetphp.dev"
Remplissez le second champ avec le chemin du répertoire dans lequel se trouve le projet, auquel vous ajoutez "/web/"
Ex :
    Si le projet se trouve dans c:/wamp/www/projetphp/, le chemin renseigné doit être c:/wamp/www/projetphp/web/
Attention : un chemin dont les noms de dossier ont des espaces peut générer des erreurs. Evitez donc de mettre le projet
dans un repertoire dont le chemin comporte des espaces

Ensuite, installer la base de données se trouvant à la racine du projet "projet_php.sql"
Si vous décidez de modifier le nom de la base ou de mettre un mot de passe pour votre connexion au phpMyAdmin de wamp,
pensez à renseigner/modifier ces informations dans le fichier /config/db.ini du projet


///// EXPLICATION PROJET /////

Le projet réalisé en PHP pour la formation AFIP Concepteur Développeur Informatique prend la forme d'une application de gestion d'élèves.
L'application doit comporter trois pages distinctes :
 - une permettant d'ajouter un stagiaire à un programme de formation avec un formateur et à des dates précises
 - une permettant de mettre à jour les données des stagiaires déjà inscrits
 - une permettant d'effacer des stagiaires déjà inscrit



///// INTRODUCTION /////

Afin de mettre en place une application évolutive et facilement maintenable, j'ai décidé d'organiser le projet selon un
pattern MVC, en réutilisant certains composants développés pour mon framework PHP personnel.
Ainsi, le projet incorpore avec lui des éléments très génériques tels que :
 - un système de routing basé sur un stockage des différentes routes dans un fichier json
 - un objet Request qui gère l'intégralité des variables globales et leur sécurisation
 - un objet DatabaseManager par lequel passent toutes les requêtes et qui gère la connexion à la base de données
 - les classes abstraites Controller et Entity dont hériteront tous les controlleurs et les entités
 - un objet Tools stockant un certain nombre de fonctions utiles

Les seuls éléments "extérieurs" au projet sont :
 - l'utilisation du moteur de template Smarty, installé grâce au package de Composer
 - l'utilisation des bibliothèques javascript jQuery et jQueryUI


///// STRUCTURE PROJET /////

/cache
Dossier contenant la mise en cache possible des données du site
Pour le moment, il ne contient que le dossier "templates_c" utilisé par Smarty

/config
Dossier de configuration du projet.
Le dossier "routes" contient les fichiers json que l'objet Routing vient scanner lorsqu'il doit
renvoyé un controlleur et une méthode correspondant à l'url appelée.
Le fichier "config.php" est le fichier de configuration qui servira pour l'autoload et pour définir un certain nombre de
constantes globales
Le fichier "custom_func.php" sert à définir une méthode permettant à smarty une gestion des url dynamiques
Le fichier db.ini contient les données de configuration de la base de données, permettant la connexion

/src
Dossier des différentes classes d'objet PHP
Le dossier "core" contient toutes les classes génériques et les classes abstraites nécessaire au bon
fonctionnement de l'application
Le dossier "controllers" contient les controlleurs principaux qui seront susceptibles d'être appelé par le système de
Routing. Ils héritent tous de la classe abstraite "Controller" du dossier "Core"
Le dossier "entities" contient les entités qui serviront de modèles pour le dialogue avec la base de données. Elles héritent
toutes de la classe abstraite "Entity" du dossier "Core" et intègrent l'utilisation de l'objet DatabaseManager pour le dialogue avec
la base de données.

/vendor
Dossier généré par composer pour l'instalation des packages (ici Smarty uniquement) et où se trouve l'autoload de composer

/web
Dossier vers lequel doit pointer l'url racine du projet.
Le fichier "index.php" est le controlleur principal qui se chargera d'appeler la méthode correspondante du controlleur
correspondant à l'url demandée, grâce à l'utilisation de l'objet Routing. Il charge aussi les autoloads de l'application.
Le fichier "index_async.php" a la même fonction que le fichier "index.php" mais sera appelé uniquement pour les requêtes
effectuées en Ajax.
Le fichier ".htaccess" permet de bypasser les urls demandées afin de toujours faire passer l'utilisateur par l'index.php
(sauf pour les requêtes Ajax qui passeront pas index_async.php)
Le dossier "js" contient les fichiers javascript du site
Le dossier "libs" contient jQuery et jQueryUI
Le dossier "styles" contient les feuilles de styles css (dont le reset css) et les images du site
Le dossier "templates" contient les vues du site. Ce sont des fichiers en .tpl car ils sont gérés par Smarty



///// NOTE SUR LE SYSTEME DE ROUTING /////

Le fichiers json ne permettant pas l'inclusion de commentaires, voici quelques explications :
Par souci de lisibilité et de maintenabilité, les routes ne sont pas réunies au sein d'un seul fichier, mais séparés en
plusieurs fichiers, organisés par sections. Ici, on aura le "core.json" qui contient les routes standards telles que la Homepage
ou la page d'erreur 404 et le "student.json" qui contient nos routes concernant la gestion des élèves
Chaque route est définit par un nom, une url, le controlleur qu'elle doit appeler et la méthode de ce controlleur qu'elle doit
invoquer.
Si l'utilisateur du framework souhaite définir de nouvelles routes, il suffit de les rajouter soit dans un fichier existant,
soit en créant un nouveau fichier json dans le dossier /config/routes qui est scanné en entier par l'objet Routing lors de
l'initialisation de l'appli
