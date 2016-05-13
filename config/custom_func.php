<?php

/**
 * Fonction permettant le parsage d'url dans Smarty
 * 
 * @param $tpl_output
 * @param $smarty
 * @return mixed
 */
function smarty_outputfilter_filterURL($tpl_output, $smarty) {
    return preg_replace_callback(
        '/\[url:name=(.+?)(\&(.+)?)?\]/',
        create_function(
            '$matches',
            'if(empty($matches[3])) $matches[3] = NULL;
            $routing = new Core\\Routing;
            return $routing->getLinkByName($matches[1], $matches[3]);'
        ),
        $tpl_output
    );
}