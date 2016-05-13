<?php

// src/entities/Nationality.php

namespace Entities;

use Core\Entity;

/**
 * Class Nationality
 * @package Entities
 *          
 *          Objet associé à la table nationality en base de données 
 */
class Nationality extends Entity {

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
     * Nationality constructor.
     */
    public function __construct() {
        $this->table = 'nationality';
        parent::__construct();
    }

    /**
     * GETTER-SETTER
     */

    /**
     * @return int
     */
    public function getId() {
        return utf8_encode($this->id);
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = htmlentities(addslashes($id), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return string
     */
    public function getName() {
        return utf8_encode($this->name);
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = htmlentities(addslashes($name), ENT_QUOTES, 'UTF-8');
    }

    
    
} 