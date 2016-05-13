{* web/templates/student/delete.tpl *}

<div class="bloc">
    <form name="delete_student" id="delete-student">
        <input type="hidden" name="token_form" value="{$tokenForm}">
        <h1>Suppression des données d'un stagiaire</h1>
        <table class="nice-table">
            <thead>
            <tr>
                <td>Nom</td>
                <td>Prénom</td>
                <td>Nationalité</td>
                <td>Type de formation</td>
                <td>Formateur - Salle - Date début - Date fin</td>
                <td>Suppression</td>
            </tr>
            </thead>
            <tbody>
            {* Pour chaque stagiaire *}
            {foreach $arrStudent as $student}
                <tr>
                    <td>{$student->getName()}</td>
                    <td>{$student->getSurname()}</td>
                    <td>{$student->getNationality()->getName()}</td>
                    <td>{$student->getTrainingType()->getName()}</td>
                    <td>
                        {foreach $student->teachers as $teacher}
                            {$teacher.name} - {$teacher.room_name} - {$teacher.date_start} - {$teacher.date_end}<br/>
                        {/foreach}
                    </td>
                    {* Les données seront un tableau delete rempli avec les IDs des stagiaires à supprimer *}
                    <td><input type="checkbox" value="{$student->getId()}" name="delete[]"></td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {* Submit formulaire *}
        <div class="group-form">
            <input type="submit" value="supprimer">
        </div>
    </form>
</div>