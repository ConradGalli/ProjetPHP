<?php

// src/controllers/StudentController.php

namespace Controllers;

use Core\Controller;
use Core\Request;
use Core\Tools;
use Entities\Nationality;
use Entities\Student;
use Entities\Teacher;
use Entities\Training_type;

/**
 * Class UserController
 * @package Controllers
 *
 *          Controlleur gérant toutes les pages concernant la gestion des élèves
 *          ainsi que les requêtes Ajax
 */
class StudentController extends Controller {

    /**
     * Affichage de la page du formulaire de création de stagiaire
     *
     * @param Request $request
     */
    public function createIndex(Request $request) {

        // On initialise le soustitre de la page
        $vars['titlePage'] = "Création d'un stagiaire";

        /**
         * On crée un token de sécurité qui sera inséré en hidden dans le formulaire pour éviter les éventuelles requêtes
         * mal intentionnées bypassant l'affichage de la page (par exemple par cURL)
         */
        $vars['tokenForm'] = md5('formulaireProjetPhp');

        // On récupère les nationalités
        $nationality = new Nationality;
        $vars['arrNationalities'] = $nationality->getAll();

        // On récupères les types de formation
        $trainingType = new Training_type;
        $vars['arrTrainingTypes'] = $trainingType->getAll();

        /**
         * On récupère les formateurs disponibles et pour chacun d'entre eux, on récupère leur salle, leurs stagiaires
         * et leur type de formation
         */
        $teacher = new Teacher;
        $arrTeachers = $teacher->getAll();
        foreach ($arrTeachers as $k => $oneTeacher) {
            $arrTeachers[$k]->room = $oneTeacher->getRoom();
            $arrTeachers[$k]->students = $oneTeacher->getStudentTeacher();
            $arrTeachers[$k]->training_types = $oneTeacher->getTrainingType();
        }
        $vars['arrTeachers'] = $arrTeachers;

        // Toutes les données récupérées sont passées en variables du controlleur afin de les assigner à Smarty au moment du rendu
        $this->setVars($vars);

        // Le controlleur affiche la page
        $this->render('student/create.tpl');
    }

    /**
     * Affichage de la page de modification des stagiaires
     *
     * @param Request $request
     */
    public function editIndex(Request $request) {

        // On initialise le soustitre de la page
        $vars['titlePage'] = "Modification des stagiaires";

        /**
         * On crée un token de sécurité qui sera inséré en hidden dans le formulaire pour éviter les éventuelles requêtes
         * mal intentionnées bypassant l'affichage de la page (par exemple par cURL)
         */
        $vars['tokenForm'] = md5('formulaireProjetPhp');

        // On récupère tous les stagiaires
        $student = new Student;
        $vars['arrStudents'] = $student->getAll();

        // On récupère les nationalités
        $nationality = new Nationality;
        $vars['arrNationalities'] = $nationality->getAll();

        // On récupères les types de formation
        $trainingType = new Training_type;
        $vars['arrTrainingTypes'] = $trainingType->getAll();

        /**
         * On récupère les formateurs disponibles et pour chacun d'entre eux, on récupère leur salle, leurs stagiaires
         * et leur type de formation
         */
        $teacher = new Teacher;
        $arrTeachers = $teacher->getAll();
        foreach ($arrTeachers as $k => $oneTeacher) {
            $arrTeachers[$k]->room = $oneTeacher->getRoom();
            $arrTeachers[$k]->students = $oneTeacher->getStudentTeacher();
            $arrTeachers[$k]->training_types = $oneTeacher->getTrainingType();
        }
        $vars['arrTeachers'] = $arrTeachers;

        // Toutes les données récupérées sont passées en variables du controlleur afin de les assigner à Smarty au moment du rendu
        $this->setVars($vars);

        // Le controlleur affiche la page
        $this->render('student/edit.tpl');
    }

    /**
     * Affichage de la page de supression des stagiaires inscrits
     *
     * @param Request $request
     */
    public function deleteIndex(Request $request) {

        // On initialise le soustitre de la page
        $vars['titlePage'] = "Supression de stagiaire";

        /**
         * On crée un token de sécurité qui sera inséré en hidden dans le formulaire pour éviter les éventuelles requêtes
         * mal intentionnées bypassant l'affichage de la page (par exemple par cURL)
         */
        $vars['tokenForm'] = md5('formulaireProjetPhp');

        // On récupère tous les stagiaires et pour chacun d'entre eux, on récupère leur(s) formateur(s)
        $student = new Student();
        $arrStudent = $student->getAll();
        foreach ($arrStudent as $k => $oneStudent) {
            $arrStudent[$k]->teachers = $oneStudent->getTeachers();
        }
        $vars['arrStudent'] = $arrStudent;

        // Toutes les données récupérées sont passées en variables du controlleur afin de les assigner à Smarty au moment du rendu
        $this->setVars($vars);

        // Le controlleur affiche la page
        $this->render('student/delete.tpl');
    }

    /**
     * Methode appelée en ajax uniquement lors de l'insertion d'un nouveau stagiaire
     *
     * @param Request $request
     *
     * @throws \Exception
     */
    public function add(Request $request) {

        // On vérifie le token du formulaire intégré en hidden lors de l'affichage de la page
        if ($request::getQuery('token_form') != md5('formulaireProjetPhp')) {
            throw new \Exception('Wrong token. Data not send');
        }

        // On récupère les données souhaitées parmi celles envoyées par le formulaire
        $data['name'] = $request::getQuery('name');
        $data['surname'] = $request::getQuery('surname');
        $data['nationality_id'] = $request::getQuery('nationality_id');
        $data['training_type_id'] = $request::getQuery('training_type_id');

        /**
         * Traitement spécifique pour les données des formateurs
         * Chaque type de données et contenu dans un tableau dont la clé est l'id du formateur
         * La donnée teacher est un tableau des formateurs coché
         */
        $teacherDateStart = $request::getQuery('teacher_date_start');
        $teacherDateEnd = $request::getQuery('teacher_date_end');
        $arrTeachers = $request::getQuery('teacher');
        
        if (!empty($arrTeachers)) {
            foreach ($arrTeachers as $k => $teacher) {
                //Si le formateur n'est pas coché, on passe, sinon, on insère les données dans $data
                if (!empty($teacher)) {
                    $data['teacher'][$k]['teacher_id'] = $k;
                    $data['teacher'][$k]['date_start'] = Tools::formatDate($teacherDateStart[$k]);
                    $data['teacher'][$k]['date_end'] = Tools::formatDate($teacherDateEnd[$k]);
                }
            }
        }

        //On vérifie que les champs soient bien remplis et correctement
        if (empty($data['name']) && preg_match('/[a-zA-Z_- ]+/', $data['name'])) {
            $vars['errors'][] = 'Vous devez renseigner un nom correct.';
        }
        if (empty($data['surname']) && preg_match('/[a-zA-Z_- ]+/', $data['surname'])) {
            $vars['errors'][] = 'Vous devez renseigner un prénom correct.';
        }
        if (empty($data['nationality_id']) && preg_match('/[0-9]+/', $data['nationality_id'])) {
            $vars['errors'][] = 'Vous devez renseigner une nationalité.';
        }
        if (empty($data['training_type_id']) && preg_match('/[0-9]+/', $data['training_type_id'])) {
            $vars['errors'][] = 'Vous devez renseigner un type de formation.';
        }
        if (empty($data['teacher'])) {
            $vars['errors'][] = 'Vous devez renseigner un formateur.';
        }

        if (!empty($vars['errors'])) {
            $this->setVars($vars);
            $this->renderAjax('message.tpl');
        }

        /**
         * Si le script n'est pas arrêté à cause d'une erreur de données de formulaire, on réalise l'insertion
         * On crée un nouvel objet Student que l'on hydrate avec les données recueillis
         * puis on l'insère en base de données grâce à sa méthode save (qui sera utilisé tant pour un INSERT que pour un UPDATE
         * et qui peut prendre en paramètre un tableau de formateurs à associer à l'inscription de ce stagiaire)
         * Cette fonction renverra true si l'insertion est réussie
         */
        $student = new Student;
        $student->hydrate($data);

        // On envoie le rendu ajax
        if ($student->save($data['teacher'])) {
            $vars['messages'][] = 'Inscription enregistrée !';
        } else {
            $vars['errors'][] = 'Erreur à l\'inscription.';
        }
        $this->setVars($vars);
        $this->renderAjax('message.tpl');
    }

    /**
     * Méthode appelée en Ajax uniquement par le formulaire de supression de stagiaire
     * 
     * @param Request $request
     *
     * @throws \Exception
     */
    public function delete(Request $request) {

        // On vérifie le token du formulaire intégré en hidden lors de l'affichage de la page
        if ($request::getQuery('token_form') != md5('formulaireProjetPhp')) {
            throw new \Exception('Wrong token. Data not send');
        }

        //On vérifie qu'un stagiaire a été sélectionné, sinon, on affiche un message d'erreur
        if (empty($request::getQuery('delete'))) {
            $vars['errors'][] = 'Vous n\'avez sélectionné aucun stagiaire !';
            $this->setVars($vars);
            $this->renderAjax('message.tpl');
        }

        /**
         * Les données se présente sous la forme d'un tableau d'ID des stagiaires à effacer, donc on parcours le tableau
         * et on lance la méthode delete pour chaque ID de stagiaires
         */
        foreach ($request::getQuery('delete') as $studentID) {
            $student = new Student;
            $student->delete($studentID);
        }
        $vars['messages'][] ='Désinscription réussie !';
        $this->setVars($vars);
        $this->renderAjax('message.tpl');
    }

    /**
     * Méthode appelée en Ajax uniquement par le formulaire de mise à jour des stagiaires
     * 
     * @param Request $request
     *
     * @throws \Exception
     */
    public function update(Request $request) {

        // On vérifie le token du formulaire intégré en hidden lors de l'affichage de la page
        if ($request::getQuery('token_form') != md5('formulaireProjetPhp')) {
            throw new \Exception('Wrong token. Data not send');
        }

        /**
         * Chaque ligne du tableau du formulaire devient un tableau de données contenu dans le tableau
         * principal student des données envoyées dont les clés sont les ID des stagiaires
         */
        $arrStudent = $request::getQuery('student');
        $nbStudentUpdated = 0;
        
        // On parcours ce tableau student
        foreach ($arrStudent as $studID => $stud) {
            
            /**
             * Si le stagiaire n'a pas été coché (case Modification), on ne fait rien et on passe au suivant
             * sinon, on traite le stagiaire
             */
            if (empty($stud['update']) || $stud['update'] != 'on') {
                continue;
            }
            
            /**
             * On crée l'objet Student que l'on hydrate avec les données du formulaire, après avoir récupéré son ID
             * à partir de la clé du tableau
             */
            $student = new Student;
            $stud['id'] = $studID;
            $student->hydrate($stud);
            
            /**
             * On traite ensuite la case Formateurs de ce stagiaire
             * Tous les formateurs et leurs éventuelles dates associées, sont stockés dans un tableau teacher
             * donc on parcours ce tableau afin de ne lui faire conserver que des données correctes
             */
            foreach ($stud['teacher'] as $teacherID => $teacher) {
                //Si le formateur n'est pas coché, un enlève le formateur du tableau, sinon on traite
                if (empty($teacher['checked']) || $teacher['checked'] != 'on') {
                    unset($stud['teacher'][$teacherID]);
                } else {
                    //On vérifie que les dates soient bien remplies
                    if (empty($teacher['date_start']) || empty($teacher['date_end'])) {
                        $vars['errors'][] = 'Vous devez renseignez des dates lorsque vous sélectionez un formateur';
                        $this->setVars($vars);
                        $this->renderAjax('message.tpl');
                    }
                    
                    /**
                     * On enlève la donnée "checked" de ce formateur afin de ne garder uniquement les données qui seront
                     * insérées en base de données
                     */
                    unset($stud['teacher'][$teacherID]['checked']);
                    
                    //On organise et formate les données restantes
                    $stud['teacher'][$teacherID]['teacher_id'] = $teacherID;
                    $stud['teacher'][$teacherID]['student_id'] = $studID;
                    $stud['teacher'][$teacherID]['date_start'] = Tools::formatDate($teacher['date_start']);
                    $stud['teacher'][$teacherID]['date_end'] = Tools::formatDate($teacher['date_end']);
                }
            }
            
            /**
             * On met à jour la base de données pour ce stagiaire grâce à la méthode save qui peut prendre en paramètre
             * un tableau de formateurs à associer au stagiaire
             */
            $student->save($stud['teacher']);
            $nbStudentUpdated++;
        }
        $vars['messages'][] = 'Données mises à jour pour ' . $nbStudentUpdated . ' stagiaire(s)';
        $this->setVars($vars);
        $this->renderAjax('message.tpl');
    }

}