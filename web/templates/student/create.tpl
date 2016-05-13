{* web/templates/student/create.tpl *}

<div class="bloc">
    <form id="create-student" name="create_student">
        {* Token de sécurité *}
        <input type="hidden" name="token_form" value="{$tokenForm}">
        <h1>Insérer un stagiaire en formation</h1>
        {* Nom Stagiaire *}
        <div class="group-form">
            <label for="name">Nom :</label>
            <input type="text" name="name" id="name" data-type="name" required>
            <div class="validate-icon"></div>
        </div>
        {* Prénom Stagiaire *}
        <div class="group-form">
            <label for="surname">Prénom :</label>
            <input type="text" name="surname" id="surname" data-type="name" required>
            <div class="validate-icon"></div>
        </div>
        {* Nationalité Stagiaire *}
        <div class="group-form">
            <label for="nationality">Nationalité :</label>
            <select name="nationality_id" id="nationality">
                {foreach $arrNationalities as $nationality}
                    <option value="{$nationality->getId()}">
                        {$nationality->getName()}
                    </option>
                {/foreach}
            </select>
        </div>
        {* Type de formation *}
        <div class="group-form">
            <label for="training-1">Type de la formation :</label>
            <select name="training_type_id" class="training" id="training-1">
                {foreach $arrTrainingTypes as $trainingType}
                    <option value="{$trainingType->getId()}">{$trainingType->getName()}</option>
                {/foreach}
            </select>
        </div>
        {* Formateurs par date *}
        <div class="group-form">
            <label>Formateurs par date :</label>
            <ul id="teacher-1">
                {foreach $arrTeachers as $teacher}
                    {* On attribue le code de formation en classe au li pour la gestion du grisage en JS *}
                    <li class="{foreach $teacher->training_types as $trainingType}{$trainingType.training_type_code} {/foreach}">
                        <table class="no-style nice-table">
                            <tr>
                                <td class="no-style big">
                                    <div class="group-form">
                                        <input type="checkbox"
                                               name="teacher[{$teacher->getId()}]" onclick="setRequireDate(this);">
                                        {$teacher->getName()} - {$teacher->getRoom()->getName()}
                                    </div>
                                </td>
                                <td class="no-style big">
                                    <div class="group-form">
                                        <input type="text"
                                               name="teacher_date_start[{$teacher->getId()}]"
                                               class="date-form no-style"
                                               data-type="date">
                                        <div class="validate-icon"></div>
                                    </div>
                                </td>
                                <td class="no-style big">
                                    <div class="group-form">
                                        <input type="text"
                                               name="teacher_date_end[{$teacher->getId()}]"
                                               class="date-form no-style"
                                               data-type="date">
                                        <div class="validate-icon"></div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </li>
                {/foreach}
            </ul>
        </div>
        {* Submit formulaire *}
        <div class="group-form">
            <input type="submit" value="Valider">
        </div>
    </form>
</div>