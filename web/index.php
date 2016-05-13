<?php

// web/index.php

/**
 * CONTROLLEUR PRINCIPAL DE L'APPLICATION
 */

// Autoload principal
require_once __DIR__ . '/../config/config.php';

use Core\Routing;
use Core\Request;

session_start();

// On récupère les variables globales et on les sécurise dans l'objet Request
$request = Request::createFromGlobals();

// On appelle le Routing et on récupère la route correspondant à la requête
$routing = new Routing;
$response = $routing->get($request);

// Avec cette réponse, on récupère le contrôleur et la méthode à appeler
$controllerName = $response->getController();
$method = $response->getMethod();

$controller = new $controllerName;

// On ajoute éventuellement les paramètres passés en GET dans l'Url à la requête
if (!empty($response->getAttributes())) 
    $request::addRequestAttributes($response);

// Si la méthode existe pour ce contrôleur, on l'envoie avec l'objet Request en paramètres, sinon on fait une erreur 404
if (method_exists($controller, $method)) {
    $controller->$method($request);
} else {
    $routing->redirect(404);
}

