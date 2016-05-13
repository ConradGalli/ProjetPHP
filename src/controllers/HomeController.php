<?php

// src/controllers/HomeController.php

namespace Controllers;

use Core\Controller;
use Core\Request;

/**
 * Class HomeController
 * @package Controllers
 *          
 *          Classe gérant l'affichage de la Homepage
 */
class HomeController extends Controller {

    /**
     * Cette méthode affiche la Homepage
     * 
     * @param Request $request
     */
    public function index(Request $request) {

        // On initialise le soustitre de la page
        $vars['titlePage'] = "Bienvenu !";

        $this->setVars($vars);
        $this->render('index.tpl');
    }
}