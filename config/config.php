<?php

// config/config.php

/**
 * Constantes de dossier afin de pouvoir accèder à des chemins de fichiers ou dossier
 * facilement depuis n'importe quel endroit de l'application
 */

define('DIR_CFG', __DIR__);
define('DIR_SRC', __DIR__ . '/../src');
define('DIR_VEND', __DIR__ . '/../vendor');
define('DIR_CACHE', __DIR__ . '/../cache');
define('DIR_WEB', __DIR__ . '/../web');
define('DIR_CTRL', __DIR__ . '/../src/controllers');
define('DIR_CORE', __DIR__ . '/../src/core');
define('DIR_ENT', __DIR__ . '/../src/entities');
define('DIR_ROUTES', __DIR__ . '/routes');

//Autres constantes
define('IS_DEV_MODE', true);
define('ADMIN_EMAIL', 'charlesroman.web@gmail.com');

/**
 * Définit si l'application est lancé en mode debug ou non
 * Si oui : affichage des erreurs PHP et des Exception, sinon non
 */
if (IS_DEV_MODE) {
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL ^ E_DEPRECATED ^ E_STRICT);
} else {
    ini_set('display_errors', 0);
    ini_set('error_reporting', E_ALL ^ E_NOTICE);
}

/**
 * Chargement de l'autoload de Composer
 */
require_once DIR_VEND . '/autoload.php';

/**
 * Chargement du fichier de custom_func pour Smarty
 */
require_once DIR_CFG . '/custom_func.php';