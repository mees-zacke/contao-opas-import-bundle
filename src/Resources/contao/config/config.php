<?php

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['content']['opasImport'] = array
(
    'callback' => 'Floxn\ContaoOpasImportBundle\Module\OpasImportModule'
);

$GLOBALS['BE_MOD']['content']['opasRefresh'] = array
(
    'callback' => 'Floxn\ContaoOpasImportBundle\Module\OpasRefreshModule'
);
