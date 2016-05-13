<?php

// src/core/RoutingResponse.php

namespace Core;

use JsonSerializable;

/**
 * Class RoutingResponse
 * @package Core
 *          
 *          Objet semblable à une entité mais séparé de celles-ci car concerne uniquement le système de Routing
 *          Propriétés correspondant aux routes
 */
class RoutingResponse implements JsonSerializable {

    /**
     * Nom de la route
     * 
     * @var string
     */
    private $name;

    /**
     * Url de la route
     * 
     * @var string
     */
    private $url;

    /**
     * Controlleur associé à la route
     * 
     * @var string
     */
    private $controller;

    /**
     * Méthode du controlleur à appeler
     * 
     * @var string
     */
    private $method;

    /**
     * Attributs passés en GET
     * 
     * @var array
     */
    private $attributes;

    /**
     * Hydrate immédiatement l'objet avec les données passées en paramètre
     * 
     * RoutingResponse constructor.
     * @param null $object
     */
    public function __construct($object = null) {
        if (!empty($object)) $this->hydrate($object);
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    /**
     * Met à jour les propriétés à partir d'un tableau de valeurs (dont les clés doivent être les noms des
     * propriétés)
     * 
     * @param $data
     */
    public function hydrate($data) {
        foreach ($data as $attribute => $value) {
            $attribute = explode('_', $attribute);
            $method = 'set' . ucfirst($attribute[0]);
            for ($i = 1 ; $i < count($attribute) ; $i++) {
                $method .= ucfirst($attribute[$i]);
            }
            if (!empty($value) && method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * GETTER-SETTER
     */

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController($controller) {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes) {
        $this->attributes = $attributes;
    }
}