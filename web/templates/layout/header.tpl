{* web/templates/layout/header.tpl *}

<header id="main-header">
    {* Bannière Titre du Site *}
    <div id="main-header-banner">
        <h1>Projet PHP Website</h1>
        <h2>Modification d'un stagiaire</h2>
        <div id="button-main-menu">Menu</div>
    </div>
    {* Menu principal avec gestion des URLs grâce au custom_func.php qui appelle la fonction getLinkByName de l'objet Routing *}
    <ul id="main-menu">
        <li><a href="[url:name=student-create]">Ajout d'un stagiaire</a></li>
        <li><a href="[url:name=student-edit]">Modification d'un stagiaire</a></li>
        <li><a href="[url:name=student-delete]">Suppression d'un stagiaire</a></li>
    </ul>
</header>