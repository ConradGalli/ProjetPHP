<?php

// web/index_async.php

/**
 * CONTROLLEUR PRINCIPAL DE L'APPLICATION POUR LES REQUETES AJAX
 */

// Autoload principal
require_once __DIR__ . '/../config/config.php';

use Core\Routing;
use Core\Request;

session_start();

// On récupère les variables globales et on les sécurise dans l'objet Request
$request = Request::createFromGlobals();

// On récupère le contrôleur et la méthode à appeler qui, lors des appels ajax seront appelés directement par les données GET ou POST
$controllerName = (!empty($request::getQuery('controller'))) ? 'Controllers\\' . $request::getQuery('controller') : 'Controllers\\' . $request::getRequest('controller');
$method = (!empty($request::getQuery('method'))) ? $request::getQuery('method') : $request::getRequest('method');

$controller = new $controllerName;

// Si la méthode existe pour ce contrôleur, on l'envoie avec l'objet Request en paramètres, sinon on fait une erreur 404ajax
if (method_exists($controller, $method)) {
    $controller->$method($request);
} else {
    $routing = new Routing;
    $routing->redirect('404ajax');
}

