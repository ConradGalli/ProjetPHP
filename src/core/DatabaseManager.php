<?php

// src/core/DatabaseManager.php

namespace Core;

use PDO;
use Exception;

/**
 * Class DatabaseManager
 * @package Core
 *
 *          La classe DatabaseManager se charge de la relation à la base de données
 *          Elle sera utilisée par les Entités
 *
 *          Elle a été conçu afin d'être le plus souple possible, ainsi certaines méthode auront une fonction relativement
 *          proche mais une réalisation différente.
 *
 *          Certaines méthodes sont simple comme "find" qui recherche une occurence d'une table en se basant uniquement
 *          sur son ID, d'autres permettent des requêtes plus complexe comme where qui peut prendre plusieurs tables
 *          et plusieurs paramètres de recherche.
 *
 *          Il y a également une série de quatre méthodes spéciales (start, flush, execute et cancel) qui permettent
 *          une gestion de transaction en dehors de l'objet DatabaseManager.
 *
 *          Enfin de nombreuses méthodes de requêtes ont un résultat modulables. En effet, nombre d'entre elles peuvent
 *          prendre un paramètre $getSQL à true lors de leur appel. Si c'est le cas, elles n'exécuteront pas la requête
 *          mais retournerons simplement un tableau à deux valeurs : le type de requête effectuée et
 *          la chaîne de caractère contenant la requête SQL.
 *
 *          Tous ces fonctionnements ont été mis en place afin de rendre la classe la plus générique et souple possible
 *          de manière à avoir une liberté totale dans l'exécution des requêtes (ordre, timing) à partir des Entités
 */
class DatabaseManager {

    /**
     * Constantes utilisées pour rendre le code de gestion des requêtes SQL plus lisible
     */
    const FIND_ALL = 'FIND_ALL';
    const FIND = 'FIND';
    const WHERE = 'WHERE';
    const SAVE_INSERT = 'SAVE_INSERT';
    const SAVE_UPDATE = 'SAVE_UPDATE';
    const DELETE = 'DELETE';

    /**
     * Variable dans laquelle sera stocké la connexion
     * @var null|PDO
     */
    private static $connexion = NULL;

    /**
     * Nom de la base de données récupéré dans le db.ini
     * @var string
     */
    private static $dbname = NULL;

    /**
     * Adresse de la base de données récupérée dans le db.ini
     * @var string
     */
    private static $host = NULL;

    /**
     * Compte utilisateur de la base de données récupéré dans le db.ini
     * @var string
     */
    private static $user = NULL;

    /**
     * Mot de passe du compte utilisateur récupéré dans le db.ini
     * @var string
     */
    private static $password = NULL;

    /**
     * A la construction d'un objet DatabaseManager, on établit la connexion à la base de données et on stocke
     * cette connexion dans la propriété $connexion
     *
     * DBManager constructor.
     */
    public function __construct() {

        //Recuperation de la config du db.ini
        $database_configuration = self::getDataConfig();

        //CONNEXION A LA BDD
        try {
            //Initialisation
            self::$dbname = $database_configuration->dbname;
            self::$host = $database_configuration->host;
            self::$user = $database_configuration->user;
            self::$password = $database_configuration->password;

            $config = 'mysql:';
            $config .= 'host=' . self::$host . ';';
            $config .= 'dbname=' . self::$dbname;

            //Connexion
            self::$connexion = new PDO($config, self::$user, self::$password);
            self::$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die('Error server connexion : ' . $e->getMessage());
        }
    }

    /**
     * Méthode qui permet de récupérer la connexion à la base de données grâce à la propriété $connexion
     * Si celle-ci est vide, on réinitialise l'objet DatabaseManager
     *
     * @return PDO
     */
    public static function getConnexion() {
        if (self::$connexion == NULL) {
            new DatabaseManager();

            return self::$connexion;
        } else {
            return self::$connexion;
        }
    }

    /**
     * Récupère le contenu du db.ini pour la configuration de la base de données
     *
     * @return array|bool
     */
    private static function getDataConfig() {
        $dir = DIR_CFG . '/db.ini';
        $arrConfig = parse_ini_file($dir);

        return Tools::parseJson($arrConfig);
    }

    /**
     * Méthode qui cherche une entité d'une table à partir de son ID
     * Retourne la requête SQL sans l'exécuter si $getSQL à true
     *
     * @param int|string $id
     * @param string     $table
     * @param string     $entity
     * @param bool       $getSQL
     *
     * @return string
     */
    public static function find($id, $table, $entity = '', $getSQL = false) {

        // Vérification des paramètres obligatoires
        if (empty($id) || empty($table)) {
            return false;
        }

        /**
         * On récupère si possible la classe d'objet qui nous servira à fetcher le résultat et renvorer
         * un objet plutôt qu'un tableau
         */
        if (empty($entity)) {
            $entity = self::setEntity($table);
        }

        // Construction de la requête
        $sql = "SELECT *";
        $sql .= " FROM " . $table;
        $sql .= " WHERE 1";
        $sql .= " AND id = '" . $id . "'";

        // Traitement du résultat selon la variable $getSQL
        if ($getSQL) {
            $result['type'] = self::FIND;
            $result['sql'] = $sql;
        } else {
            list($result) = self::query($sql, self::FIND, $entity);
        }

        return $result;
    }

    /**
     * Méthode qui récupère toutes les lignes d'une table passée en paramètre
     * Retourne la requête SQL sans l'exécuter si $getSQL à true
     *
     * @param string $table
     * @param string $entity
     * @param bool   $getSQL
     *
     * @return array|string
     */
    public static function findAll($table, $entity = '', $getSQL = false) {

        // Vérification des paramètres obligatoires
        if (empty($table)) {
            return false;
        }

        /**
         * On récupère si possible la classe d'objet qui nous servira à fetcher le résultat et renvorer
         * un objet plutôt qu'un tableau
         */
        if (empty($entity)) {
            $entity = self::setEntity($table);
        }

        // Construction de la requête
        $sql = "SELECT *";
        $sql .= " FROM " . $table;
        $sql .= " WHERE 1";

        // Traitement du résultat selon la variable $getSQL
        if ($getSQL) {
            return array("type" => self::FIND_ALL, "sql" => $sql);
        } else {
            return self::query($sql, self::FIND_ALL, $entity);
        }
    }

    /**
     * Méthode pour envoyer une requête de recherche plus complexe
     * Prend en paramètre plusieurs tables, plusieurs conditions et éventuellement plusieurs champs à retourner
     * Attention cependant, ne permet pas d'utiliser des jointures de tables
     * Retourne la requête SQL sans l'exécuter si $getSQL à true
     *
     * @param array $params
     * @param array $tables
     * @param array $fields
     * @param bool  $getSQL
     *
     * @return array|bool|mixed|string
     */
    public static function where($params, $tables, $fields = array(), $getSQL = false) {

        // Vérification des paramètres obligatoires
        if (empty($tables) || empty($params)) {
            return false;
        }

        // Construction de la requête
        $sql = "SELECT";

        // Gestion des champs s'il y en a
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $sql .= " " . $field . ",";
            }
            $sql = Tools::minusStr($sql, 1);
        } else {
            $sql .= " *";
        }
        $sql .= " FROM";

        // Selection des tables
        foreach ($tables as $table) {
            $sql .= " " . $table . ",";
        }
        $sql = Tools::minusStr($sql, 1);
        $sql .= " WHERE 1";

        // Gestion des conditions
        foreach ($params as $param) {
            $sql .= " AND " . $param;
        }

        // Traitement du résultat selon la variable $getSQL
        if ($getSQL) {
            return array("type" => self::WHERE, "sql" => $sql);
        } else {
            return self::query($sql, self::WHERE);
        }
    }

    /**
     * Méthode qui réalise une insertion en base de données
     * Invoquable uniquement par la méthode save du DatabaseManager qui dispatchera à la bonne méthode
     * selon les données reçues
     * Prend en paramères un tableau d'attributs et une table
     * Retourne la requête SQL sans l'exécuter si $getSQL à true
     *
     * @param array  $arrAttr
     * @param string $table
     * @param bool   $getSQL
     *
     * @return array|string
     */
    private static function saveInsert($arrAttr, $table, $getSQL = false) {

        // Vérification des paramètres obligatoires
        if (empty($arrAttr) || empty($table)) {
            return false;
        }

        // Construction de la requête
        $sql = "INSERT INTO " . $table . " (";

        /**
         * Récupération des noms des attributs/colonnes, en ignorant si le nom de l'attribut est "table"
         * car propriété de toutes les entitées
         */
        foreach ($arrAttr as $nameAttr => $valueAttr) {
            if (!empty($valueAttr) && !is_array($valueAttr) && $nameAttr != 'table') {
                $sql .= $nameAttr . ", ";
            }
        }

        // On enlève la dernière virgule et le dernier espace
        $sql = Tools::minusStr($sql, 2);


        $sql .= ") VALUES (";

        /**
         * On insère les valeurs des attributs/colonnes en ignorant si le nom de l'attribut est "table"
         * car propriété de toutes les entitées et selon le type de données
         */
        foreach ($arrAttr as $nameAttr => $valueAttr) {
            if (!empty($valueAttr) && $nameAttr != 'table') {
                if (is_int($valueAttr) || is_float($valueAttr)) {
                    $sql .= $valueAttr . ",";
                } else if (is_string($valueAttr) || is_bool($valueAttr)) {
                    $sql .= "'" . $valueAttr . "',";
                }
            }
        }

        // On enlève la dernière virgule
        $sql = Tools::minusStr($sql, 1);
        $sql .= ")";

        // Traitement du résultat selon la variable $getSQL
        if ($getSQL) {
            return array("type" => self::SAVE_INSERT, "sql" => $sql);
        } else {
            return self::query($sql, self::SAVE_INSERT);
        }
    }

    /**
     * Méthode qui réalise une mise à jour en base de données
     * Invoquable uniquement par la méthode save du DatabaseManager qui dispatchera à la bonne méthode
     * selon les données reçues
     * Prend en paramères un id de ligne, un tableau d'attributs et une table
     * Retourne la requête SQL sans l'exécuter si $getSQL à true
     *
     * @param string|int $idObject
     * @param array      $arrAttr
     * @param string     $table
     * @param bool       $getSQL
     *
     * @return mixed|string
     */
    private static function saveUpdate($idObject, $arrAttr, $table, $getSQL = false) {

        // Vérification des paramètres obligatoires
        if (empty($idObject) || empty($arrAttr) || empty($table)) {
            return false;
        }

        // Construction de la requête
        $sql = "UPDATE " . $table . " SET";

        // Modification des attributs/colonnes
        foreach ($arrAttr as $nameAttr => $valueAttr) {
            if ($nameAttr != 'table') {
                $sql .= " " . $nameAttr . " = '" . $valueAttr . "',";
            }
        }

        // On elnève la dernière virgule
        $sql = Tools::minusStr($sql, 1);
        $sql .= " WHERE id = '" . $idObject . "'";

        // Traitement du résultat selon la variable $getSQL
        if ($getSQL) {
            return array("type" => self::SAVE_UPDATE, "sql" => $sql);
        } else {
            self::query($sql, self::SAVE_UPDATE);
            return self::find($idObject, $table);
        }
    }

    /**
     * Fonction chargée de dispatcher sur les fonctions saveInsert ou saveUpdate selon les données envoyées
     * Le dispatch se fait en fonction de la présence ou non d'un id dans les données envoyées
     * S'il y en a un : update, sinon : insert
     * Prend également en paramètre la variable $getSQL afin de la passer à la fonction appelée le cas échéant
     *
     * @param mixed  $data
     * @param string $table
     * @param bool   $getSQL
     *
     * @return array|mixed|string
     */
    public static function save($data, $table, $getSQL = false) {

        // Vérification des paramètres obligatoires
        if (empty($data) || empty($table)) {
            return false;
        }

        // On cherche s'il y a un ID, la variable $data pouvant être un tableau ou un objet
        if (is_object($data)) {
            $id = $data->getId();
        } else if (!empty($data['id'])) {
            $id = $data['id'];
        } else {
            $id = '';
        }

        // Si $data est un objet on fait un json serialize pour récupérer un tableau d'attributs
        $arrAttr = (is_object($data)) ? $data->jsonSerialize() : $data;

        // On dispatch selon qu'il y ait un id ou non
        if (!empty($id)) {
            $result = self::saveUpdate($id, $arrAttr, $table, $getSQL);
        } else {
            $result = self::saveInsert($arrAttr, $table, $getSQL);
        }

        return $result;
    }

    /**
     * Méthode qui est chargé d'effacer des données en base selon une table donnée et des conditions se trouvant dans un
     * tableau $params passé en paramètre
     * Retourne la requête SQL sans l'exécuter si $getSQL à true
     *
     * @param array  $params
     * @param string $table
     * @param bool   $getSQL
     *
     * @return array|bool|string
     */
    public static function delete($params, $table, $getSQL = false) {

        // Vérification des paramètres obligatoires
        if (empty($table) || empty($params)) {
            return false;
        }

        // Construction de la requête SQL
        $sql = "DELETE FROM " . $table;
        $sql .= " WHERE 1";

        // Gestion des conditions
        foreach ($params as $nameAttr => $valueAttr) {
            $sql .= " AND " . $nameAttr . " = '" . $valueAttr . "'";
        }

        // Traitement du résultat selon la variable $getSQL
        if ($getSQL) {
            return array("type" => self::DELETE, "sql" => $sql);
        } else {
            return self::query($sql, self::DELETE);
        }
    }

    /**
     * Methode de gestion de requête SQL principale
     * Prend en paramètre la requête SQL et le type de requête qui permettra de traiter le résultat différemment selon
     * le type de requête
     * Est en générale appelée uniquement par les méthodes de DatabaseManager (si le $getSQL est à false), mais reste
     * une méthode public au cas où nous serions amenés à l'utiliser au sein d'une Entité
     *
     * @param string $sql
     * @param string $type
     * @param string $entity
     *
     * @return array|string
     */
    public static function query($sql, $type, $entity = '') {

        // Vérification des paramètres obligatoires
        if (empty($sql) || empty($type)) {
            return false;
        }

        // On prépare la requête et démarre la transaction
        $CNX = self::getConnexion();
        $CNX->beginTransaction();
        $request = $CNX->prepare($sql);

        // On gère le fetch mode par objet ou par tableau
        if (!empty($entity)) {
            $typeFetch = PDO::FETCH_CLASS;
            $request->setFetchMode($typeFetch, $entity);
        } else {
            $typeFetch = PDO::FETCH_ASSOC;
            $request->setFetchMode($typeFetch);
        }

        // TRY CATCH de l'execution de la requete
        try {
            $request->execute();

            /**
             * Gestion du résultat renvoyé
             * Le commit de la transaction est exécuté spécifiquement selon le type de requête afin de pouvoir
             * appelé lastInsertId dans le cas d'une requête d'insertion
             */
            switch ($type) {
                case self::FIND:
                case self::FIND_ALL:
                case self::WHERE:
                    $CNX->commit();
                    $result = array();
                    while ($row = $request->fetch($typeFetch)) {
                        $result[] = $row;
                    }
                    break;
                case self::SAVE_UPDATE:
                case self::DELETE:
                    $CNX->commit();
                    $result = 'No returned data';
                    break;
                case self::SAVE_INSERT:
                    $result = $CNX->lastInsertId();
                    $CNX->commit();
                    break;
                default:
                    $CNX->commit();
                    $result = 'Query\'s type unkown';
                    break;
            }

            return $result;
        } catch (Exception $e) {
            // Si il y a erreur, on effectue un rollBack sur la transaction
            $CNX->rollBack();
            if (IS_DEV_MODE) {
                echo 'query ' . $type . ' failed : ' . $e->getMessage() . '<br/> SQL : ' . $sql;
            }

            return false;
        }
    }

    /**
     * Méthode qui permet d'effectuer une série de requête SQL contenu dans un tableau passé en paramètre (dont le format
     * est celui du tableau renvoyé par les méthodes quand la variable $getSQL est à true afin de matcher les fonctionnements)
     * Cette fonction ne renvoie qu'un booléen selon sa réussite ou non, on ne l'utilisera donc pas pour des requêtes
     * de type SELECT
     *
     * @param array $arrSQL
     *
     * @return bool
     */
    public static function multipleQuery($arrSQL = array()) {

        // Vérification des paramètres obligatoires
        if (empty($arrSQL)) {
            return false;
        }

        $CNX = self::getConnexion();

        /**
         * TRY CATCH de l'execution des requêtes successives avec démarrage de la transaction et rollBack si
         * une des requêtes échoue
         */
        try {
            $CNX->beginTransaction();
            foreach ($arrSQL as $sqlData) {
                if (empty($sqlData['sql'])) {
                    continue;
                }

                $request = $CNX->prepare($sqlData['sql']);
                $request->execute();
            }
            $CNX->commit();

            return true;
        } catch (Exception $e) {
            $CNX->rollBack();
            if (IS_DEV_MODE) {
                echo 'complexQuery Failed : ' . $e->getMessage();
            }

            return false;
        }
    }

    /**
     * Méthode renvoyant une connexion pour laquelle une transaction a été initialisé, permettant l'utilisation des
     * transactions à l'extérieur de la classe DatabaseManager
     *
     * @return PDO
     */
    public static function start() {
        try {
            $CNX = self::getConnexion();
            $CNX->beginTransaction();
            return $CNX;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Commit les modifications à la base de données sur une connexion pour laquelle une transaction a été initialisée
     *
     * @param PDO $CNX
     *
     * @return bool
     */
    public static function flush($CNX) {
        if (empty($CNX)) {
            return false;
        } else {
            $CNX->commit();
        }
    }

    /**
     * Execute une requête SQL sur une connexion pour laquelle une transaction a été initialisée
     * Peut aussi retourner le dernier ID insérer
     *
     * @param PDO    $CNX
     * @param string $sql
     * @param bool   $lastID
     *
     * @return bool
     */
    public static function execute($CNX, $sql, $lastID = false) {
        $request = $CNX->prepare($sql);
        $request->execute();
        if ($lastID) {
            return $CNX->lastInsertId();
        } else {
            return true;
        }
    }

    /**
     * Annule les modifications de la base de données à partir d'une connexion pour laquelle une transaction
     * a été initialisée
     *
     * @param PDO $CNX
     *
     * @return bool
     */
    public static function cancel($CNX) {
        if (empty($CNX)) {
            return false;
        } else {
            $CNX->rollBack();
            echo 'request failed, cancel modifications';
            exit;
        }
    }

    /**
     * Permet de définir une classe d'objet existantes à partir du nom d'une table
     * 
     * @param string $table
     *
     * @return string
     */
    private static function setEntity($table) {
        $class = 'Entities\\' . ucfirst($table);

        return (class_exists($class)) ? $class : false;
    }
}