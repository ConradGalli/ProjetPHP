<?php

// src/core/Controller.php

namespace Core;
use Smarty;

/**
 * Class Controller
 * @package Core
 *          
 *          La classe abstraite Controller dont doivent hériter tous les controlleurs de l'application
 *          C'est elle qui gère notamment l'utilisation du template Smarty, le traitement des variables passées
 *          au template et l'appel de la vue
 */
abstract class Controller {

    /**
     * Variable qui stocke l'objet Smarty dans chaque controlleur
     * @var Smarty
     */
    protected $smarty;

    /**
     * Tableau qui contiendra toutes les variables devont être utilisées par le template
     * @var array
     */
    protected $vars = array();

    /**
     * Controller constructor.
     */
    public function __construct() {
        // A chaque appel de controlleur, on initialise Smarty
        $this->initSmarty();
    }

    /**
     * Méthode qui appel le template correspondant, passé en paramètre
     * et se charge de l'insertion des variables et de l'affichage de la page
     *
     * @param string $template
     */
    public function render($template) {

        // Pour chaque variables donnée au controlleur, on les assigne à Smarty
        foreach ($this->vars as $varName => $varValue) {
            $this->smarty->assign($varName, $varValue);
        }

        // On récupère les constantes définit dans le config.php et on les assigne à Smarty pour pouvoir y accéder dans les templates
        $const = get_defined_constants(true);
        foreach($const['user'] as $k => $v) {
            $this->smarty->assign($k, $v);
        }

        // On récupère dans une variable $contentForLayout l'affichage du template par Smarty
        ob_start();
        $this->smarty->display($template);
        $contentForLayout = ob_get_clean();

        /**
         * On assigne à Smarty les templates principaux Header et Footer, ainsi que la page du template appelé
         * afin de l'insérer dans le layout principal du site
         */
        $this->smarty->assign('_HEADER', $this->smarty->fetch('layout/header.tpl'));
        $this->smarty->assign('_CONTENT_FOR_LAYOUT', $contentForLayout);
        $this->smarty->assign('_FOOTER', $this->smarty->fetch('layout/footer.tpl'));

        $this->smarty->display('layout/default.tpl');
        exit;
    }

    /**
     * Cette fonction est la fonctio de rendu pour un appel en Ajax
     * Elle reprend le même fonctionnement que la méthode render normale, mais n'insère pas le template appelé
     * dans le layout principal, elle se contente d'afficher le rendu du template simple
     *
     * @param string $template
     * @return string
     */
    public function renderAjax($template) {

        // On assigne les variables
        foreach ($this->vars as $varName => $varValue) {
            $this->smarty->assign($varName, $varValue);
        }

        // On récupère et assigne les constantes
        $const = get_defined_constants(true);
        foreach($const['user'] as $k => $v) {
            $this->smarty->assign($k, $v);
        }

        // On affiche le rendu du template Ajax spécifique
        echo $this->smarty->fetch('ajax/' . $template);
        exit;
    }

    /**
     * Methode qui permet d'enregistrer les variables au sein du controlleur sous forme de tableau
     * dont la valeur sera la variable et la clé son nom
     *
     * @param array $arrVars
     */
    public function setVars($arrVars = array()) {
        foreach($arrVars as $nameVars => $valueVars) {
            $this->vars[$nameVars] = $valueVars;
        }
    }

    /**
     * Fonction qui permet d'initialiser Smarty en définissant les paramètres de config
     */
    private function initSmarty() {

        // Création de l'objet Smarty et passage au controlleur
        $this->smarty = new Smarty;

        // Définition des dossiers utilisés
        $this->smarty->setTemplateDir(DIR_WEB . '/templates/');
        $this->smarty->setCompileDir(DIR_CACHE . '/templates_c/');
        $this->smarty->setConfigDir(DIR_CACHE . '/config/');
        $this->smarty->setCacheDir(DIR_CACHE . '/cache/');

        // On charge le filtre qui nous permet un traitement dynamique des URLs au sein d'un template
        $this->smarty->loadFilter('output', 'filterURl');
        
        // On charge le format de date souhaité, nous permettant d'utiliser un filtre au sein du template
        $config['date'] = "%d/%m/%Y";
        $config['time'] = "%T";
        $this->smarty->assign('config', $config);
    }

}