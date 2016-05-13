<?php

// src/controllers/ErrorsController.php

namespace Controllers;

use Core\Controller;

/**
 * Class _404Controller
 * @package Controllers
 *          
 *          Cette classe gère les erreurs en cas de mauvaise url
 *         
 */
class ErrorsController extends Controller {

    /**
     * Affiche la page 404
     */
    public function error404() {
        $this->smarty->assign('erreurMsg', 'ERREUR 404');
        $this->render('404.tpl');
    }

    /**
     * Renvoie un message d'erreur si une requête Ajax ne trouve aucun routing correpsondant
     */
    public function errorAjax404() {
        
    }
}