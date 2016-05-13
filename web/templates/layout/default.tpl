{* web/templates/layout/default.tpl *}

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Projet PHP - {$titlePage}</title>
    <link rel="icon" type="image/ico" href="/styles/img/favicon.ico">
    <link href='https://fonts.googleapis.com/css?family=Arimo' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="/styles/reset.css"/>
    <script type="text/javascript" src="/libs/jquery/jquery.js"></script>
    <script type="text/javascript" src="/libs/jquery/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/libs/jquery/jquery-ui.min.css"/>
    <link rel="stylesheet" type="text/css" href="/libs/jquery/jquery-ui.structure.min.css"/>
    <link rel="stylesheet" type="text/css" href="/libs/jquery/jquery-ui.theme.min.css"/>
    <script type="text/javascript" src="/js/main.js"></script>
    <link rel="stylesheet" type="text/css" href="/styles/styles.css"/>
</head>
<body>
<div id="main-wrapper">
    {$_HEADER}
    <div id="main-container">
        {$_CONTENT_FOR_LAYOUT}
    </div>
    {$_FOOTER}
</div>
</body>
</html>