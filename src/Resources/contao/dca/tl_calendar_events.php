<?php

// Erweitern der default Feldposition mit zwei weiteren: konzertID und konzertOrt
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = str_replace(
    'location',
    'location, concertID, concertOrt, ',
    $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']);

// Feldkonfiguration für konzertID
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['concertID'] = array
(
'label'		=> &$GLOBALS['TL_LANG']['tl_calendar_events']['concertID'],
'exclude'	=> true,
'search'	=> true,
'inputType'	=> 'text',
'eval'		=> array('rgxp'=>'natural', 'maxlength'=>10, 'tl_class'=>'w50'),
'sql'		=> "int(10) unsigned NOT NULL default NULL"
);

// Feldkonfiguration für konzertOrt
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['concertOrt'] = array
(
'label'		=> &$GLOBALS['TL_LANG']['tl_calendar_events']['concertOrt'],
'exclude'	=> true,
'search'	=> true,
'inputType'	=> 'text',
'eval'		=> array('maxlength'=>255, 'tl_class'=>'w50'),
'sql'		=> "varchar(255) NOT NULL default ''"
);
