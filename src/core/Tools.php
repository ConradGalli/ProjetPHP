<?php

// src/core/Tools.php

namespace Core;

/**
 * Class Tools
 * @package Core
 *
 *          Objet particulier contenant des fonctions utiles pouvant être utilisées partout pour faciliter la lisibilité
 *          et l'organisation du code
 */
class Tools {

    /**
     * Affiche un dump et stop le script
     *
     * @param string|int|bool|array $input
     */
    public static function oneDump($input) {
        echo '<pre>';
        var_dump($input);
        exit;
    }

    /**
     * Affiche plusieurs dumps et stop le script
     *
     * @param array $arr
     */
    public static function manyDump($arr = array()) {
        echo '<pre>';
        foreach ($arr as $k => $v) {
            echo ':::::' . $k . ':::::<br>';
            var_dump($v);
            echo '<br><br>';
        }
        exit;
    }

    /**
     * Permet de récupérer de données récupérables uniquement grâce à un json_encode
     * Ex : les données récupérées dans le db.ini
     *
     * @param array $input
     * @return bool|mixed
     */
    public static function parseJson($input = array()) {
        if (empty($input)) {
            return false;
        } else {
            return json_decode(json_encode($input));
        }
    }

    /**
     * Scan un repertoire en suppriment les données .. et . renvoyées systématiquement par la fonction initiale
     * scandir de PHP
     *
     * @param string $dir
     * @return array|bool
     */
    public static function scanDir($dir) {
        if (empty($dir)) {
            return false;
        } else {
            return array_diff(scandir($dir), array('..', '.'));
        }
    }

    /**
     * Permet d'enlever un certain nombre de caractères à la fin d'une chaine selon les données passées en paramètres
     * Utile dans la construction des requêtes SQL
     *
     * @param $string
     * @param int $nb
     * @return mixed
     */
    public static function minusStr($string, $nb = 0) {
        return substr($string, 0 , (strlen($string) - $nb));
    }

    /**
     * Méthode à double sens pour formater une date
     * Si la date passée en paramètre est au format DD/MM/YY, retournera YY-MM-DD
     * Si la date passée en paramètre est au format YY-MM-DD, retournera DD/MM/YY
     * 
     * @param $date
     *
     * @return bool|string
     */
    public static function formatDate($date) {
        if (empty($date) || !is_string($date)) return false;

        if (strpos($date, '/')) {
            $date = explode('/', $date);
            return $date[2] . '-' . $date[1] . '-' . $date[0];
        } else if (strpos($date, '-')) {
            $date = explode('-', $date);
            return $date[2] . '/' . $date[1] . '/' . $date[0];
        } else {
            return false;
        }

    }
}