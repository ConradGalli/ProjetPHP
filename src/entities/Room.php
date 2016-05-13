<?php

// src/entities/Room.php

namespace Entities;

use Core\Entity;

/**
 * Class Room
 * @package Entities
 *          
 *          Objet associé à la table room en base de données
 */
class Room extends Entity {

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
     * Room constructor.
     */
    public function __construct() {
        $this->table = 'room';
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
    
    

} 