<?php

// src/entities/Teacher.php

namespace Entities;

use Core\DatabaseManager;
use Core\Entity;

/**
 * Class Teacher
 * @package Entities
 *
 *          Objet associé à la table teacher en base de données
 *          Possède des fonctions propres en plus des getters-setters afin de récupérer les données associées
 *          à partir des tables associatives
 */
class Teacher extends Entity {

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
    protected $surname;

    /**
     * @var int
     */
    protected $room_id;

    /**
     * Teacher constructor.
     */
    public function __construct() {
        $this->table = 'teacher';
        parent::__construct();
    }

    /**
     * FONCTIONS SPECIFIQUES GERANT LES RELATIONS ENTRE TABLES
     */

    /**
     * Récupère l'objet Room complet correspondant à la propriété Room_id
     * 
     * @return array|string
     */
    public function getRoom() {
        return DatabaseManager::find($this->getRoomId(), 'room');
    }

    /**
     * Renvoie les données de la table student_teacher correspondant
     * 
     * @return array|string
     */
    public function getStudentTeacher() {
        $tables = array(
            "student_teacher st"
        );
        $where = array(
            "st.teacher_id = '" . $this->getId() ."'"
        );
        return DatabaseManager::where($where, $tables);
    }

    /**
     * Récupère l'objet Training_type complet correspondant à la propriété Training_type_id
     * 
     * @return array|bool|mixed|string
     */
    public function getTrainingType() {
        $tables = array(
            "training_type_teacher ttt",
            "training_type tt"
        );
        $where = array(
            "ttt.teacher_id = '" . $this->getId() ."'",
            "tt.id = ttt.training_type_id"
        );
        $fields = array(
            "ttt.*",
            "tt.name as training_type_name",
            "tt.code as training_type_code"
        );
        return DatabaseManager::where($where, $tables, $fields);
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
        return utf8_decode($this->name);
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = htmlentities(addslashes($name), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return string
     */
    public function getSurname() {
        return utf8_encode($this->surname);
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname) {
        $this->surname = htmlentities(addslashes($surname), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return int
     */
    public function getRoomId() {
        return utf8_encode($this->room_id);
    }

    /**
     * @param int $room_id
     */
    public function setRoomId($room_id) {
        $this->room_id = htmlentities(addslashes($room_id), ENT_QUOTES, 'UTF-8');
    }


} 