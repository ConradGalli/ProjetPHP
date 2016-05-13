<?php

// src/entities/Training_type.php

namespace Entities;

use Core\Entity;

/**
 * Class Training_type
 * @package Entities
 *          
 *          Objet associé à la table training_type en base de données
 */
class Training_type extends Entity {

    /**
     * PROPRIETES
     */
    
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $code;

    /**
     * Training_type constructor.
     */
    public function __construct() {
        $this->table = 'training_type';
        parent::__construct();
    }

    /**
     * GETTER-SETTER
     */
    
    /**
     * @return mixed
     */
    public function getId() {
        return utf8_encode($this->id);
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = htmlentities(addslashes($id), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return mixed
     */
    public function getName() {
        return utf8_encode($this->name);
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = htmlentities(addslashes($name), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return mixed
     */
    public function getCode() {
        return utf8_encode($this->code);
    }

    /**
     * @param $code
     */
    public function setCode($code) {
        $this->code = htmlentities(addslashes($code), ENT_QUOTES, 'UTF-8');
    }
    
}