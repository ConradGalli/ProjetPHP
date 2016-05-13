<?php

// src/entities/Student.php

namespace Entities;

use Core\Entity;
use Core\DatabaseManager;

/**
 * Class User
 * @package Entities
 *
 *          Objet associé à la table student en base de données
 *          Possède des fonctions propres en plus des getters-setters afin de récupérer les données associées
 *          à partir des tables associatives
 */
class Student extends Entity {

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
    protected $nationality_id;

    /**
     * @var int
     */
    protected $training_type_id;

    /**
     * Student constructor.
     */
    public function __construct() {
        $this->table = 'student';
        parent::__construct();
    }

    /**
     * FONCTIONS SPECIFIQUES GERANT LES RELATIONS ENTRE TABLES
     */

    /**
     * Récupère l'objet Nationality complet correspondant à la propriété Nationality_id
     *
     * @return array|string
     */
    public function getNationality() {
        return DatabaseManager::find($this->getNationalityId(), 'nationality');
    }

    /**
     * Récupère l'objet Training_type complet correspondant à la propriété Training_type_id
     *
     * @return array|string
     */
    public function getTrainingType() {
        return DatabaseManager::find($this->getTrainingTypeId(), 'training_type');
    }

    /**
     * Récupère les formateurs associés à l'id de ce stagiaire
     *
     * @return array|string
     */
    public function getTeachers() {

        if (empty($this->getId())) {
            return false;
        }

        $tables = array("student_teacher st", "teacher t", "room r");
        $where = array("st.student_id = '" . $this->getId() . "'", "t.id = st.teacher_id", "t.room_id = r.id");
        $fields = array("st.*", "t.name as name", "t.surname as surname", "t.room_id", "r.name as room_name");
        return DatabaseManager::where($where, $tables, $fields);
    }

    /**
     * Methode de sauvegarde du stagiaire en base de données
     * Remplace la fonction par défaut save de la classe parent Entity car nécessite un fonctionnement
     * spécifique vu que l'enregistrement d'un stagiaire fait intervenir des données de plusieurs tables différentes
     *
     * @param array $arrTeachers
     *
     * @return bool
     */
    public function save($arrTeachers = array()) {

        /**
         * On va ici utiliser les fonctions du DatabaseManager permettant une gestion contrôlée des transactions
         */

        // On récupère la connexion et on démarre la transaction
        $cnx = DatabaseManager::start();

        // On récupère le requête sql d'insertion ou d'update du stagiaire et on l'execute
        $sqlSaveStudent = DatabaseManager::save($this, $this->table, true);
        $idStudent = DatabaseManager::execute($cnx, $sqlSaveStudent['sql'], true);

        // Soit le stagiaire a déjà un id (update) et on le récupère soit on l'a déjà récupéré (insert)
        if (!empty($this->getId())) {
            $idStudent = $this->getId();
        } elseif (empty($idStudent)) {
            // Si on a pas d'id, on annule les modifications
            DatabaseManager::cancel($cnx);
        }

        $success = array();

        // Si un tableau de formateurs est passé en paramètres, on gère l'insertion de données dans la table student_teacher
        if (!empty($arrTeachers)) {

            // On commence par effacer toutes les données de cette table en rapport avec l'id de notre stagiaire
            $sqlDeleteTeacher = DatabaseManager::delete(array("student_id" => $idStudent), 'student_teacher', true);
            $success[] = DatabaseManager::execute($cnx, $sqlDeleteTeacher['sql']);

            // On insère les nouvelles données dans cette table
            foreach ($arrTeachers as $teacher) {
                $teacher['student_id'] = $idStudent;
                $sqlInsertTeacher = DatabaseManager::save($teacher, 'student_teacher', true);
                $success[] = DatabaseManager::execute($cnx, $sqlInsertTeacher['sql']);
            }
        }

        // Si des erreurs se sont produites, on annule l'intégralité des modifications, sinon on les valide
        if (in_array(false, $success)) {
            DatabaseManager::cancel($cnx);
            return false;
        } else {
            DatabaseManager::flush($cnx);
            return true;
        }
    }

    /**
     * Fonction effaçant le stagiaire de la base de données
     * On effacera pas les données des tables associatives du stagiaire ici, car la base de données est en InnoDB et conçue
     * de manière à effacer en cascade les données associées à un ID de stagiaire dans les tables associatives lors d'une
     * requête DELETE
     * 
     * @param array $id
     *
     * @return array|bool|string
     */
    public function delete($id) {
        $params['id'] = $id;
        return parent::delete($params);
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
     * @param int $id
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
     * @param string $name
     */
    public function setName($name) {
        $this->name = htmlentities(addslashes($name), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function getNationalityId() {
        return utf8_encode($this->nationality_id);
    }

    /**
     * @param int $nationality_id
     */
    public function setNationalityId($nationality_id) {
        $this->nationality_id = htmlentities(addslashes($nationality_id), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return mixed
     */
    public function getTrainingTypeId() {
        return utf8_encode($this->training_type_id);
    }

    /**
     * @param int $training_type_id
     */
    public function setTrainingTypeId($training_type_id) {
        $this->training_type_id = htmlentities(addslashes($training_type_id), ENT_QUOTES, 'UTF-8');
    }
}