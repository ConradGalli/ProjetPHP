<?php

// src/core/Request.php

namespace Core;

/**
 * Class Request
 * @package Core
 *
 *          L'objet Request est l'objet qui est chargé de gérer l'utilisation des variables globales
 *          Pour des raisons de sécurité, celles-ci ne seront plus appelés directement dans les controlleurs et/ou les
 *          entités
 *          Elles sont sécurisées puis stockées dans les propriétés de cet objet
 *          Cet objet Request est passé par défaut en paramètre de toutes les méthodes des controlleurs afin de pouvoir
 *          accéder à ces données n'importe où
 */
class Request {

    /**
     * Stockage de la superglobale $_SERVER
     * @var array
     */
    private static $server;

    /**
     * Stockage de la superglobale $_SESSION
     * @var array
     */
    private static $session;

    /**
     * Stockage de la superglobale $_FILES
     * @var array
     */
    private static $files;

    /**
     * Stockage de la superglobale $_COOKIE
     * @var array
     */
    private static $cookies;

    /**
     * Stockage de la superglobale $_GET
     * @var array
     */
    private static $request;

    /**
     * Stockage de la superglobale $_POST
     * @var array
     */
    private static $query;

    /**
     * Stockage de l'Url appelée parl'utilisateur
     * @var string
     */
    private static $base_url;

    /**
     * Cette méthode invoque tous les setters des variables superglobales et retourne l'objet Request
     * 
     * @return Request
     */
    public static function createFromGlobals() {
        self::setServer();
        self::setSession();
        self::setFiles();
        self::setCookies();
        self::setRequest();
        self::setQuery();
        self::setUrl();
        return new static;
    }

    /**
     * Setter de l'Url qui découpe l'url appelée afin d'éliminer les paramètres passés en get et ne conserver que l'url
     * de base (qui sert notamment au système de routing) 
     */
    public static function setUrl() {
        $url = explode('?', $_SERVER['REQUEST_URI']);
        self::$base_url = $url[0];
    }

    /**
     * Getter de $base_url
     * 
     * @return string
     */
    public static function getUrl() {
        return self::$base_url;
    }

    /**
     * Getter de $server avec possibilité de ne récupérer qu'un élément dont le nom est passé en paramètre
     * 
     * @param string $input
     * @return array|bool
     */
    public static function getServer($input = '') {
        if (!empty($input)) return (isset(self::$server[$input])) ? self::$server[$input] : false; else
            return self::$server;
    }

    /**
     * Getter de $session avec possibilité de ne récupérer qu'un élément dont le nom est passé en paramètre
     * 
     * @param string $input
     * @return array|bool
     */
    public static function getSession($input = '') {
        if (!empty($input)) return (isset(self::$session[$input])) ? self::$session[$input] : false; else
            return self::$session;

    }

    /**
     * Getter de $files avec possibilité de ne récupérer qu'un élément dont le nom est passé en paramètre
     * 
     * @param string $input
     * @return array|bool|mixed
     */
    public static function getFiles($input = '') {
        if (!empty($input)) return (isset(self::$files[$input])) ? self::$files[$input] : false; else
            return self::$files;
    }

    /**
     * Getter de $cookies avec possibilité de ne récupérer qu'un élément dont le nom est passé en paramètre
     * 
     * @param string $input
     * @return array|bool
     */
    public static function getCookies($input = '') {
        if (!empty($input)) return (isset(self::$cookies[$input])) ? self::$cookies[$input] : false; else
            return self::$cookies;
    }

    /**
     * Getter de $query avec possibilité de ne récupérer qu'un élément dont le nom est passé en paramètre
     * 
     * @param string $input
     * @return array|bool
     */
    public static function getQuery($input = '') {
        if (!empty($input)) return (isset(self::$query[$input])) ? self::$query[$input] : false; else
            return self::$query;
    }

    /**
     * Getter de $request avec possibilité de ne récupérer qu'un élément dont le nom est passé en paramètre
     * 
     * @param string $input
     * @return array|bool
     */
    public static function getRequest($input = '') {
        if (!empty($input)) return (isset(self::$request[$input])) ? self::$request[$input] : false; else
            return self::$request;
    }

    /**
     * Getter global qui récupère toutes les propriétés et les dispatch dans un tableau
     * 
     * @return array
     */
    public static function getAll() {
        return array("SERVER" => self::$server, "SESSION" => self::$session, "COOKIES" => self::$cookies, "QUERY" => self::$query, "FILES" => self::$files, "REQUEST" => self::$request);
    }

    /**
     * SETTERS qui appelle chacun la méthode secureData au moment de stocké les données 
     */
    
    public static function setSession() {
        self::$session = self::secureData($_SESSION);
    }

    public static function setCookies() {
        self::$cookies = self::secureData($_COOKIE);
    }

    public static function setQuery() {
        self::$query = self::secureData($_POST);
    }

    public static function setRequest() {
        self::$request = self::secureData($_GET);
    }

    public static function setFiles() {
        self::$request = self::secureData($_FILES);
    }

    public static function setServer() {
        self::$request = self::secureData($_SERVER);
    }

    /**
     * METHODES PERMETTANT D'AJOUTER UN ELEMENT A UNE DES VARIABLES
     */

    /**
     * @param array $input
     */
    public static function addSession(array $input) {
        foreach ($input as $k => $v) {
            self::$session[$k] = self::secureData($v);
        }
    }

    /**
     * @param array $input
     */
    public static function addCookie(array $input) {
        foreach ($input as $k => $v) {
            self::$cookies[$k] = self::secureData($v);
        }
    }

    /**
     * @param array $input
     */
    public static function addQuery(array $input) {
        foreach ($input as $k => $v) {
            self::$query[$k] = self::secureData($v);
        }
    }

    /**
     * @param array $input
     */
    public static function addRequest(array $input) {
        foreach ($input as $k => $v) {
            self::$request[$k] = self::secureData($v);
        }
    }

    /**
     * Fonction permettant d'ajouter des éléments de requêtes à partir de l'objet RoutingResponse passé en 
     * paramètre et renvoyer par l'objet Routing après avoir récupéré la route correspondant à l'url demandée
     * 
     * @param RoutingResponse $routingResponse
     */
    public static function addRequestAttributes(RoutingResponse $routingResponse) {
        foreach ($routingResponse->getAttributes() as $k => $attr) {
            self::addRequest(array($k => $attr));
        }
    }

    /**
     * METHODES PERMETTANT D'EFFACER PARTIELLEMENT OU INTEGRALEMENT LES VARIABLES STOCKE ET LES SUPERGLOBALES
     */

    /**
     * @param string $input
     * @return bool
     */
    public static function removeSession($input = '') {
        if (!empty($input)) {
            if (isset($_SESSION[$input])) {
                unset($_SESSION[$input]);
                self::$session = array();
                return true;
            } else {
                return false;
            }
        } else {
            $_SESSION = array();
            self::$session = array();
            return true;
        }
    }

    /**
     * @param string $input
     * @return bool
     */
    public static function removeCookie($input = '') {
        if (!empty($input)) {
            if (isset($_COOKIE[$input])) {
                unset($_COOKIE[$input]);
                self::$cookies = array();
                return true;
            } else {
                return false;
            }
        } else {
            $_COOKIE = array();
            self::$cookies = array();
            return true;
        }
    }

    /**
     * @param string $input
     * @return bool
     */
    public static function removeQuery($input = '') {
        if (!empty($input)) {
            if (isset($_POST[$input])) {
                unset($_POST[$input]);
                self::$query = array();
                return true;
            } else {
                return false;
            }
        } else {
            $_POST = array();
            self::$query = array();
            return true;
        }
    }

    /**
     * @param string $input
     * @return bool
     */
    public static function removeRequest($input = '') {
        if (!empty($input)) {
            if (isset($_GET[$input])) {
                unset($_GET[$input]);
                self::$request = array();
                return true;
            } else {
                return false;
            }
        } else {
            $_GET = array();
            self::$request = array();
            return true;
        }
    }

    /**
     * Méthode sécurisant les données
     * Fonction récursive dans le cas de tableaux à plusieurs dimensions
     * 
     * @param $data
     * @return array|bool
     */
    private static function secureData($data) {
        if (is_string($data)) {
            $data = htmlentities(addslashes($data), ENT_QUOTES, 'UTF-8');
        } else if (is_array($data) || is_object($data)) {
            foreach ($data as $a => $b) {
                $data[$a] = self::secureData($b);
            }
        }
        return $data;
    }
}