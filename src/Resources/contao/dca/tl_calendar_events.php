<?php

// Erweitern der default Feldposition mit zwei weiteren: konzertID und konzertOrt
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = str_replace(
    'location',
    'location, konzertID, konzertOrt',
    $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']);

// Feldkonfiguration für konzertID
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['konzertID'] = array
(
'label'		=> &$GLOBALS['TL_LANG']['tl_calendar_events']['konzertID'],
'exclude'	=> true,
'search'	=> true,
'inputType'	=> 'text',
'eval'		=> array('rgxp'=>'natural', 'maxlength'=>10, 'tl_class'=>'w50'),
'sql'		=> "int(10) unsigned NOT NULL default NULL"
);

// Feldkonfiguration für konzertOrt
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['konzertOrt'] = array
(
'label'		=> &$GLOBALS['TL_LANG']['tl_calendar_events']['konzertOrt'],
'exclude'	=> true,
'search'	=> true,
'inputType'	=> 'text',
'eval'		=> array('maxlength'=>255, 'tl_class'=>'w50'),
'sql'		=> "varchar(255) NOT NULL default ''"
);
