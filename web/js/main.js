/*** LOADING PAGE ***/
$(document).ready(function () {

    /*** MENU ***/

    // On initialise l'interactivité du menu
    $("#button-main-menu").click(function () {
        $("#main-menu").toggleClass("visible-menu");
        $("#main-wrapper").toggleClass("visible-menu");
    });


    /*** FORM ***/

    // On lance les fonctions gérant le grisage des champs selon le type de formation/formateur
    checkAvailability();
    $(".training").each(function () {
        $(this).on("change", function () {
            checkAvailability();
        });
    });

    // On lance le check de verification de formulaire en bindant les inputs avec des fonctions de verification
    initValidationInputs();

    // On gère l'envoi des formulaires
    initSendingForms();

    // On initialise le datepicker des formulaires
    $(".date-form").datepicker({dateFormat: 'dd/mm/yy'});
    
    // On gère la selection de ligne automatique sur le formulaire de modification
    initModifAutoEditForm();
});

/*** FUNCTIONS ***/

/**
 * Fonction qui coche automatiquement la case "Modification" si un champ de la ligne est modifié sur le formulaire Edit
 */
function initModifAutoEditForm() {
    $("#edit-student input, #edit-student select").on("change", function(){
        var id = 0;
        var allClass = $(this).attr("class").split(" ");

        allClass.forEach(function(e){
            if (e.search("student") != -1) {
                id = e.split("-")[1];
            }
        });

        if (id > 0) {
            $("input#edit-" + id).attr("checked", "checked");
            setRequireInputsForEditForm($("input#edit-" + id));
        }
    });
}

/**
 * Bind les fonctions d'envoi sur les formulaires
 */
function initSendingForms() {
    $("#create-student input[type=submit]").click(function (e) {
        e.preventDefault();
        sendForm("#create-student", "StudentController", "add");
    });
    $("#edit-student input[type=submit]").click(function (e) {
        e.preventDefault();
        sendForm("#edit-student", "StudentController", "update");
    });
    $("#delete-student input[type=submit]").click(function (e) {
        e.preventDefault();
        sendForm("#delete-student", "StudentController", "delete");
    });
}

/**
 * Gère le grisage des champs selon le type de formation/formateur
 */
function checkAvailability() {

    /**
     * Pour comprendre cette fonction, il faut savoir que dans le template, le li ciblant un formateur
     * possède en classe le code des formations possibles pour lui (ici wd ou dev)
     * Le not des différents sélecteurs permet qu'un formateur disposant des deux formations ne soit jamais grisé
     */

    // Pour chaque element DOM ayant la classe training
    $(".training").each(function () {

        // On récupère l'id de l'element et la valeur de l'input
        var id = $(this).attr('id').split('-')[1];
        var code = $('option:selected', this).attr('data-code');

        $("#teacher-" + id + " li").each(function(){
            if ($(this).hasClass(code)) {
                $(this).find('input').removeAttr("disabled");
            } else {
                $(this).find('input').removeAttr("checked");
                $(this).find('input').attr("disabled", "disabled");
            }
        });
    });
}

/**
 * Fonction qui bind les inputs à cibler lors de la validation du formulaire
 */
function initValidationInputs() {
    // Pour tous les inputs qui ne soient pas de type submit ou checkbox
    $("form input:not(input[type=submit]):not(input[type=checkbox])").each(function () {

        // On bind uniquement si l'input a l'attribut required
        if ($(this).attr('required')) {
            $(this).on("change", function () {
                validInput($(this));
            });
            $(this).on("blur", function () {
                validInput($(this));
            });
        }
    });
}

/**
 * Set un attribut required sur les input de Date si le formateur est coché (enlève l'attribut si décoché)
 *
 * @param target
 */
function setRequireDate(target) {
    // On attribue ou non required sur les inputs
    if ($(target).is(':checked')) {
        $(target).closest("table.no-style").find("input[data-type=date]").attr("required", "required");
    } else {
        $(target).closest("table.no-style").find("input[data-type=date]").removeAttr("required");
    }

    // On relance une initialisation du binding des inputs pour ajouter les éventuels inputs de Date sur required
    initValidationInputs();
}

/**
 * Fonction spécifique au formulaire de modification pour gérer les champs requis selon les sélections
 *
 * @param target
 */
function setRequireInputsForEditForm(target) {
    var id = $(target).attr("id").split("-")[1];
    if ($(target).is(":checked")) {
        $("input.student-" + id + "[data-type=name]").attr("required", "required");
        $("input.student-" + id + "[data-type=surname]").attr("required", "required");
        $("input.no-style.student-" + id + "[type=checkbox]").each(function(){
            setRequireDate(this);
        });
    } else {
        $("input.student-" + id + "[data-type=name]").removeAttr("required");
        $("input.student-" + id + "[data-type=surname]").removeAttr("required");
        $("input.student-" + id + "[data-type=date]").removeAttr("required");
    }

    // On relance une initialisation du binding des inputs
    initValidationInputs();
}


/**
 * Fonction pour la requête Ajax du formulaire
 * Les données du formulaire sont envoyées en POST tandis que le controlleur
 * et sa méthode sont appelés par l'url en GET
 *
 * @param idForm
 * @param controller
 * @param method
 */
function sendForm(idForm, controller, method) {
    if (validateForm(idForm)) {
        var form = $(idForm).serialize();
        $.ajax({
            url: "/index_async.php?controller=" + controller + "&method=" + method,
            method: "POST",
            data: form,
            dataType: "html",
            success: function (data) {
                popup(data);
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
}

/**
 * Lance la vérification globale du formulaire sur chaque input required à l'envoi
 * Retourn true si tous les inputs passe la validation, sinon false
 *
 * @param idForm
 * @returns {boolean}
 */
function validateForm(idForm) {
    var success = [];
    // Gestion des inputs
    $(idForm + " input:not(input[type=checkbox]):not(input[type=hidden]):not(input[type=submit])").each(function () {
        if ($(this).attr('required')) {
            if (validInput($(this))) {
                success.push(true);
            } else {
                success.push(false);
            }
        }
    });

    // On a rempli un tableau success avec les résultats de validation des inputs, si il y en a un seul à false, on retourne false
    var result = jQuery.inArray(false, success);
    return (result == -1);
}

/**
 * Traite la validité d'un input passé en paramètre en fonction de son attribut data-type
 * Traite l'aspect visuel du résultat
 * Renvoie true si input validé, sinon renvoie false
 *
 * @param target
 * @returns {*}
 */
function validInput(target) {
    var value = target.val();
    var regex;
    var result;
    switch (target.attr("data-type")) {
        case 'name':
            regex = /^[a-zA-Z- ]{2,}$/;
            result = value.match(regex);
            break;
        case 'date':
            regex = /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/;
            result = (value.match(regex));
            break;
    }

    if (result) {
        target.parent("div.group-form").addClass('form-ok');
        if (target.parent("div.group-form").hasClass('form-not-ok')) {
            target.parent("div.group-form").removeClass('form-not-ok');
        }
        var top = target.offset().top - $("#main-container").offset().top;
        var left = target.offset().left - $("#main-container").offset().left + target.outerWidth();
        target.next(".validate-icon").css("top", top + "px");
        target.next(".validate-icon").css("left", left + "px");
        return true;
    } else {
        target.parent("div.group-form").addClass('form-not-ok');
        if (target.parent("div.group-form").hasClass('form-ok')) {
            target.parent("div.group-form").removeClass('form-ok');
        }
        var top = target.offset().top - $("#main-container").offset().top;
        var left = target.offset().left - $("#main-container").offset().left + target.outerWidth();
        target.next(".validate-icon").css("top", top + "px");
        target.next(".validate-icon").css("left", left + "px");
        return false;
    }
}

/**
 * Fonction permettant d'afficher une popup
 * Son style se trouve dans le fichier de style CSS, seule la hauteur est gérée dynamiquement ici
 *
 * @param content
 */
function popup(content) {
    var height = ($('html').outerHeight(true) <= $(document).outerHeight(true)) ? $(document).outerHeight(true) : $('html').outerHeight(true);
    var popup = "<div id='popup-wrapper' onclick='closePopup(true);' style='height: " + height + "px;'><div id='popup'></div></div>";
    $('body').append(popup);
    $('html,body').animate({scrollTop: "200px"}, 200);
    setTimeout(function () {
        $("#popup").html(content);
    }, 200);
}

/**
 * Fonction de fermeture de la popup
 * Peut déclencher un reload de la page ou non
 *
 * @param reloadpage
 */
function closePopup(reloadpage) {
    if (reloadpage) {
        window.location.reload();
    } else {
        $("#popup-wrapper").remove();
    }
}
