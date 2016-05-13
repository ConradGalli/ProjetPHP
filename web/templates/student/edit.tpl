{* web/templates/student/edit.tpl *}

<div class="bloc">
    <form id="edit-student" name="edit_student">
        <input type="hidden" name="token_form" value="{$tokenForm}">
        <h1>Modification des données du stagiaire</h1>
        <table class="nice-table">
            <thead>
            <tr>
                <td>Nom</td>
                <td>Prénom</td>
                <td>Nationalité</td>
                <td>Type de formation</td>
                <td>Formateur - Salle - Date début - Date fin</td>
                <td>Modification</td>
            </tr>
            </thead>
            <tbody>
            {* Pour chaque Stagiaire *}
            {foreach $arrStudents as $student}
                <tr>
                    {* Nom Stagiaire *}
                    <td>
                        <div class="group-form">
                            <input type="text"
                                   value="{$student->getName()}"
                                   name="student[{$student->getId()}][name]"
                                   class="small-input student-{$student->getId()}"
                                   data-type="name"
                                   >
                            <div class="validate-icon small-icon"></div>
                        </div>
                    </td>
                    {* Prénom Stagiaire *}
                    <td>
                        <div class="group-form">
                            <input type="text"
                                   value="{$student->getSurname()}"
                                   name="student[{$student->getId()}][surname]"
                                   class="small-input student-{$student->getId()}"
                                   data-type="name"
                                   >
                            <div class="validate-icon small-icon"></div>
                        </div>
                    </td>
                    {* Nationalité Stagiaire *}
                    <td>
                        <div class="group-form">
                            <select name="student[{$student->getId()}][nationality_id]"
                                    class="student-{$student->getId()}">
                                {foreach $arrNationalities as $nationality}
                                    <option value="{$nationality->getId()}"
                                            {if $student->getNationalityId() == $nationality->getId()} selected{/if}>
                                        {$nationality->getName()}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </td>
                    {* Type de formation Stagiaire *}
                    <td>
                        <div class="group-form">
                            <select name="student[{$student->getId()}][training_type_id]"
                                    class="training student-{$student->getId()}"
                                    id="training-{$student->getId()}">
                                {foreach $arrTrainingTypes as $trainingType}
                                    <option value="{$trainingType->getId()}"
                                            data-code="{$trainingType->getCode()}"
                                            {if $student->getTrainingTypeId() == $trainingType->getId()} selected{/if}>
                                        {$trainingType->getName()}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </td>
                    {* Formateurs et Dates *}
                    <td>
                        <ul class="group-form" id="teacher-{$student->getId()}">
                            {foreach $arrTeachers as $teacher}
                                {* On attribue le code de formation en classe au li pour la gestion du grisage en JS *}
                                <li class="{foreach $teacher->training_types as $trainingType}{$trainingType.training_type_code} {/foreach}">
                                    <table class="no-style">
                                        <tr>
                                            <td class="no-style">
                                                {* Input Checkbox (checked si celui du stagiaire) *}
                                                <div class="group-form">
                                                    <input class="no-style student-{$student->getId()}"
                                                           type="checkbox"
                                                           name="student[{$student->getId()}][teacher][{$teacher->getId()}][checked]"
                                                            {foreach $teacher->students as $st}
                                                                {if $student->getId() == $st.student_id} checked{/if}
                                                            {/foreach}
                                                           onclick="setRequireDate(this);"
                                                    >
                                                    {$teacher->getName()} - {$teacher->getRoom()->getName()}
                                                </div>
                                            </td>
                                            <td class="no-style">
                                                {* Date de début, remplie avec les données correspondantes si formateur associé, sinon vide *}
                                                <div class="group-form">
                                                    <input type="text"
                                                           name="student[{$student->getId()}][teacher][{$teacher->getId()}][date_start]"
                                                            {foreach $teacher->students as $stud}
                                                                {if $student->getId() == $stud.student_id}
                                                                    value="{$stud.date_start|date_format:$config.date}"
                                                                {/if}
                                                            {/foreach}
                                                           class="date-form no-style student-{$student->getId()}"
                                                           data-type="date">
                                                    <div class="validate-icon small-icon"></div>
                                                </div>
                                            </td>
                                            <td class="no-style">
                                                {* Date de fin, remplie avec les données correspondantes si formateur associé, sinon vide *}
                                                <div class="group-form">
                                                    <input type="text"
                                                           name="student[{$student->getId()}][teacher][{$teacher->getId()}][date_end]"
                                                            {foreach $teacher->students as $stud}
                                                                {if $student->getId() == $stud.student_id}
                                                                    value="{$stud.date_end|date_format:$config.date}"
                                                                {/if}
                                                            {/foreach}
                                                           class="date-form no-style student-{$student->getId()}"
                                                           data-type="date">
                                                    <div class="validate-icon small-icon"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </li>
                            {/foreach}
                        </ul>
                    </td>
                    <td>
                        {* Checkbox de confirmation de modification *}
                        <div class="group-form">
                            <input type="checkbox"
                                   name="student[{$student->getId()}][update]"
                                   id="edit-{$student->getId()}"
                                   onclick="setRequireInputsForEditForm(this);"
                                   class="edit-button">
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {* Submit formulaire *}
        <div class="group-form">
            <input type="submit" value="modifier">
        </div>
    </form>
</div>