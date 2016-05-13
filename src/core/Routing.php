<?php

// src/core/Routing.php

namespace Core;


/**
 * Class Routing
 * @package Core
 *
 *          L'objet Routing est la classe chargée du système de routing du framework
 *          Il renverra habituellement dans son utilisation,un objet RoutingResponse dont les propriétés sont les
 *          propriétés de la route correspondant à l'url
 */
class Routing {

    /**
     * Variable de stockage de toutes les routes enregistrées dans le dossier /config/routes/
     *
     * @var array
     */
    private $allRoutes;

    /**
     * Regex permettant de passer des paramètres GET dans les urls des routes stockées dans /config/routes
     *
     * Ex :
     *      url : /article/{id}
     *      Ici, la valeur remplaçant {id} sera passé dans la variable $_GET['id']
     *
     * Note : Non utilisé dans le cadre de ce projet PHP
     *
     * @var string
     */
    private $patternGET;

    /**
     * On initialise l'objet Routing en récupérant toutes les routes et en définissant la regex servant à récupérer
     * les paramètres en GET
     *
     * Routing constructor.
     */
    public function __construct() {
        $this->patternGET = '/\{[a-zA-Z0-9_]+\}/';
        $this->allRoutes = $this->getAllRoutes();
    }

    /**
     * Retourne l'objet RoutingResponse avec les propriétés correspondant à l'url demandée (objet Request passé en
     * paramètre)
     *
     * @param Request $request
     * @return RoutingResponse
     */
    public function get(Request $request) {

        // On récupère l'url de base, débarassée des éventuelles paramètres passés en GET directement dans l'URL
        $url = $request::getUrl();

        // Toutes les routes ayant été stocké, on les parcours pour voir si l'une d'entre elle correspond à l'url demandée
        foreach ($this->allRoutes as $route) {
            
            // On récupère l'expression regulière de la route parcouru
            $patternURL = $this->patternURL($route->url);
            
            // Si la route correspond match l'expression régulière de la route parcourue, on traite, sinon on passe
            if (preg_match($patternURL, $url)) {
                // On set la route parcouru comme route actuelle
                $currentRoute = $route;

                /**
                 * Traitement afin de gérer les paramètre passé en GET dans l'url réécrite
                 * Ex :
                 *      url : /article/{id}
                 *      Ici, la valeur remplaçant {id} sera passé dans la variable $_GET['id']
                 *
                 * Note : Non utilisé dans le cadre de ce projet PHP
                 */
                preg_match($this->patternGET, $route->url, $arrGET, PREG_OFFSET_CAPTURE);
                foreach ($arrGET as $k => $v) {
                    $nameGET = str_replace('{', '', str_replace('}', '', $arrGET[$k][0]));
                    $minURL = str_replace('/', '', $url);
                    $valueGET = explode('/', substr($minURL, $arrGET[$k][1]));
                    $arrGET[$nameGET] = $valueGET[0];
                    unset($arrGET[$k]);
                }
                
                // On attribue ces paramètres à la route actuelle
                $currentRoute->attributes = $arrGET;

                /**
                 * On y ajoute les éventuels paramètres passés en GET sans passer par la réécriture
                 * Ex :
                 *      url: /article?id=var
                 */
                foreach ($request::getRequest() as $k => $get) {
                    $currentRoute->attributes[$k] = $get;
                }
            }
        }

        /**
         * Si après le scan de toutes les routes, aucun ne correspond, on fait une erreur 404
         * Sinon, on retourn l'objet RoutingResponse correspondant à la route
         */
        if (empty($currentRoute)) $this->redirect(404);
        else return new RoutingResponse($currentRoute);
    }

    /**
     * Permet de récupérer rapidement une route stockée dans /config/routes/ pas son nom
     * 
     * @param string $name
     * @return mixed
     */
    public function getLinkByName($name) {
        foreach ($this->getAllRoutes() as $route) {
            if ($name == $route->name) {
                return $route->url;
            }
        }
        return false;
    }

    /**
     * Récupère toutes les routes contenus dans le dossier /config/routes/
     * @return array
     */
    private function getAllRoutes() {
        $arrRoutesFiles = Tools::scanDir(DIR_ROUTES);
        $result = array();

        foreach ($arrRoutesFiles as $routesFile) {
            $fileContent = json_decode(file_get_contents(DIR_ROUTES . '/' . $routesFile));
            foreach ($fileContent->routes as $route) {
                $result[] = $route;
            }
        }
        return $result;
    }

    /**
     * Récupère les routes d'un fichier de routes spécifiques passé en paramètre
     * 
     * @param string $routeFile
     * @return array
     */
    private function getRoute($routeFile) {
        $fileContent = json_decode(file_get_contents(DIR_ROUTES . '/' . $routeFile . '.json'));
        foreach ($fileContent->routes as $route) {
            $result[] = $route;
        }
        return (!empty($result)) ? $result : false;
    }

    /**
     * Renvoie une expression régulière correspondant à l'url passée en paramètre
     * 
     * @param string $url
     * @return mixed
     */
    private function patternURL($url) {
        $patternURL = "/^" . addcslashes($url, '/') . "[\/]{0,1}$/";
        return preg_replace($this->patternGET, '[a-zA-Z0-9_]+', $patternURL);
    }

    /**
     * Méthode de redirection par code ou par nom
     * 
     * @param $target
     * @param array $params
     * @return bool
     */
    public function redirect($target, $params = array()) {
        if (is_int($target)) {
            $routes = $this->getRoute('core');
            foreach ($routes as $route) {
                if ($route->name == $target) {
                    $result = $route;
                }
            }
            if (empty($result)) {
                return false;
            } else {
                header('Location: ' . $result->url);
                exit;
            }
        } elseif (is_string($target)) {
            $routes = $this->getAllRoutes();
            foreach ($routes as $route) {
                if ($route->name == $target) {
                    $result = $route;
                }
            }
            $paramsStr = '?';
            foreach ($params as $k => $v) {
                $paramsStr .= $k . '=' . $v . '&';
            }
            $paramsStr = Tools::minusStr($paramsStr, 1);
            if (empty($result)) {
                return false;
            } else {
                header('Location: ' . $result->url . $paramsStr);
                exit;
            }
        }
    }
}