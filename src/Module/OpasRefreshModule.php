<?php

$db = \Contao\System::getContainer()->get('database_connection');
$DBIdCounter = $db->executeQuery('SELECT * FROM tl_mae_event_cat')->rowCount();


$context = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$url = '../files/xml/export.xml';

$ausgabe = simplexml_load_file($url);

$xml = file_get_contents($url, false, $context);
$xml = simplexml_load_string($xml);
//Counter
$importCategoryCounter = 0;

$updateCounter = 0;
$importCounter = 0;


// function for generating aliase wihtout special chars
function generateAlias($string)
{
    $search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´", "ç");
    $replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "", "c");
    $strReplace = str_replace($search, $replace, $string);
    return strtolower($strReplace);
}

//
foreach($xml->children() as $event) {

    // Even ID
    $eventId = $event->eventId;

    // Erstellen des Startdatums und der Startzeit
    $eventStartDate = ($event->eventDate);
    $eventStartTime = ($event->eventStart);
    $eventStartDateTimestamp = '';

    $eventDate = DateTime::createFromFormat('d.m.Y H:i', $eventStartDate . ' ' . $eventStartTime);

    if ($eventDate === false) {
        die("Incorrect date string");
    } else {
        $eventStartDateTimestamp = $eventDate->getTimestamp();
    }

    // Erstellen der Endzeit
    $eventEndTime = ($event->eventEnd);
    $eventEndDateTimestamp = $eventStartDateTimestamp; // Initial ist Endzeit = Startzeit

    // Wenn Zeit eingetragen ist wird die neue Endzeit erstellt und ausgegeben.
    if ($eventEndTime != '') {
        $eventEndTimeDate = DateTime::createFromFormat('d.m.Y H:i', $eventStartDate . ' ' . $eventEndTime);

        if ($eventEndTimeDate === false) {
            die("Incorrect date string");
        } else {
            $eventEndDateTimestamp = $eventEndTimeDate->getTimestamp();
        }

    }

    // Titel
    $eventTitle = $event->eventProject;

    // Ort
    $eventLocationCity = $event->eventLocation_Place;
    $eventLocationCityAlias = generateAlias($eventLocationCity);
    $eventLocationPlace = $event->eventLocation;
    if ($eventLocationCity == '') {
        $eventLocation = $eventLocationPlace;
    } elseif ($eventLocationPlace == '') {
        $eventLocation = $eventLocationCity;
    } else {
        $eventLocation = $eventLocationCity. ', ' .$eventLocationPlace;

    };

    // Komponisten
    $komponisten = '';
    foreach($event->workItem as $workItem) {
      $workTitle = $workItem->workTitle;
      $workTitle2 = $workItem->workTitle2;
      $workTitle3 = $workItem->workTitle3;
      $workComposerFirstname = $workItem->workComposerFirstname;
      $workComposerLastname = $workItem->workComposerLastname;
      $workComposerFullname = $workComposerFirstname. ' ' .$workComposerLastname;

      $komponisten .= '<span class="event-komponist"><strong>' .$workComposerFullname. '</strong>, ' .$workTitle. ' ' .$workTitle2. ' ' .$workTitle3.' </span><br />';
    };

    // Solisten
    $solisten = '';
    foreach($event->eventSoloistItem as $solist) {
      $solistFirstname = $solist->soloistFirstname;
      $solistLastname = $solist->soloistLastname;
      $solistFullname = $solistFirstname. ' ' .$solistLastname;
      $solistInstrument = $solist->soloistInstrument;

      $solisten .= '<span class="event-solist">' .$solistFullname. ', ' .$solistInstrument. '</span><br />';
    };

    // Dirigenten
    $conductor = '';
    if ($event->eventConductor != '') {
        $conductor = '<span class="event-dirigent">' .$event->eventConductor. ', Leitung</span><br />';
    } else {
        $conductor = $conductor;
    }

    // Den Teaser mit allen Inhalten zusammenfassen
    $teaser = '<p>' . $komponisten . '</p>' . '<p>' . $solisten . $conductor . '</p>';

    // Zuweiseung zu den Archiven
    $kategorie = '';
    $kategorieID = $event->serieItem->serieID;
    if ($kategorieID == '2') {
        $kategorie = '1';
    }
    elseif ($kategorieID == '3') {
        $kategorie = '2';
    }
    elseif ($kategorieID == '9') {
        $kategorie = '3';
    }
    elseif ($kategorieID == '4') {
        $kategorie = '4';
    }
    elseif ($kategorieID == '11') {
        $kategorie = '5';
    }
    elseif ($kategorieID == '6') {
        $kategorie = '6';
    }
    elseif ($kategorieID == '14') {
        $kategorie = '7';
    }
    elseif ($kategorieID == '8') {
        $kategorie = '8';
    }
    elseif ($kategorieID == '10') {
        $kategorie = '11';
    }
    elseif ($kategorieID == '13') {
        $kategorie = '12';
    }
    else {
        // Wenn keine Kategorie vergeben wurde.
        $Datum = date('d.m.Y', (int)$eventStartDate);
        echo '<script type="text/javascript" language="Javascript">alert("Keiner Kategorie zugewiesen: ' .$eventTitle. ' vom ' .$Datum. '. Der Import wird abgebrochen!")</script>';
        exit;
    }

    $DBCategorieName = $db->fetchColumn('SELECT title FROM tl_mae_event_cat WHERE title = ?', array($eventLocationCity), 0);

    if ($DBCategorieName != $eventLocationCity) {
        $DBIdCounter ++;
        $result = $db->insert('tl_mae_event_cat', array(
           'id' => $DBIdCounter,
           'tstamp' => time(),
           'title' => $eventLocationCity,
           'alias' => $eventLocationCityAlias
        ));
        $importCategoryCounter ++;
        // anzahl an Zeichen der Kategorie ID auslesen
    }


    // Datenbank tl_calendar_events aktualisieren.
    // Fetch EventID
    $DBEventId = $db->fetchColumn('SELECT id FROM tl_calendar_events WHERE id = ?', array($eventId), 0);
    $DBCategorieId = $db->fetchColumn('SELECT id FROM tl_mae_event_cat WHERE title = ?', array($eventLocationCity), 0);

    $DBIdCounterLenght = strlen((string)$DBCategorieId);

    if ($DBEventId != $eventId) {

        $result = $db->insert('tl_calendar_events', array(
           'id' => $eventId,
           'pid' => $kategorie,
           'tstamp' => time(),
           'title' => $eventTitle,
           'addTime' => '1',
           'startTime' => $eventStartDateTimestamp,
           'startDate' => $eventStartDateTimestamp,
           'endTime' => $eventEndDateTimestamp,
           'location' => $eventLocation,
           'concertOrt' => $eventLocationCity,
           'categories' => 'a:1:{i:0;s:' . $DBIdCounterLenght . ':"' . $DBCategorieId. '";}',
           'teaser' => $teaser,
           'source' => 'default',
           'author' => '3'
        ));

        $importCounter ++;
    }

}


if ($importCategoryCounter > 0 ) {
    echo '<script type="text/javascript" language="Javascript">alert("Es wurden ' . $importCategoryCounter . ' Kategorien hinzugefügt")</script>';
}


echo '<script type="text/javascript" language="Javascript">alert("Es wurden ' . $updateCounter . ' Updates durchgeführt und ' . $importCounter . ' neu importiert")</script>';
