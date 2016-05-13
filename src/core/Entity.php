<?php

// src/core/Entity.php

namespace Core;

use JsonSerializable;

/**
 * Class Entity
 * @package Core
 *
 *          Classe abstraite dont hériteront toutes les entités (correspondant aux tables de la base de données,
 *          hormis les tables associatives et/ou de jointures)
 */
abstract class Entity implements JsonSerializable {

    /**
     * Nom de la table associée à l'entité
     * Doit forcément être définit par le constructeur de la classe enfant
     * 
     * @var string
     */
    protected $table;

    /**
     * Entity constructor.
     */
    public function __construct() {
        if (empty($this->table)) return false;
    }

    /**
     * Permet de sérialiser l'objet et récupérer des objets lors de requêtes en base de donées
     * 
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    /**
     * Méthode générale d'hydratation fonctionnant pour toutes les entités
     * Met à jour les propriétés à partir d'un tableau de valeurs (dont les clés doivent être les noms des
     * propriétés)
     * 
     * @param $data
     */
    public function hydrate($data) {
        foreach ($data as $attribute => $value) {
            // On construit le nom du setter à partir du nom de l'attribut
            $attribute = explode('_', $attribute);
            $method = 'set' . ucfirst($attribute[0]);
            for ($i = 1 ; $i < count($attribute) ; $i++) {
                $method .= ucfirst($attribute[$i]);
            }
            
            // Si la méthode existe, on l'exécute
            if (!empty($value) && method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Méthode save commune à toutes les entités, appelant le save du DatabaseManager sur la table associée à l'entité
     * 
     * @return array|string
     */
    public function save() {
        return DatabaseManager::save($this, $this->table);
    }

    /**
     * Méthode get commune à toutes les entités, appelant le save du DatabaseManager sur la table associée à l'entité
     * 
     * @param $id
     * @return mixed
     */
    public function get($id) {
        list($myData) = DatabaseManager::find($id, $this->table);
        return $myData;
    }

    /**
     * Méthode getAll commune à toutes les entités, appelant le findAll du DatabaseManager sur la table associée à l'entité
     * 
     * @return array|string
     */
    public function getAll() {
        return DatabaseManager::findAll($this->table);
    }

    /**
     * Méthode delete commune à toutes les entités, appelant le delete du DatabaseManager sur la table associée à l'entité
     * 
     * @param array $params
     *
     * @return array|bool|string
     */
    public function delete($params) {
        return DatabaseManager::delete($params, $this->table);
    }

    /**
     * GETTER-SETTER
     */
    
    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable($table) {
        $this->table = $table;
    }

}