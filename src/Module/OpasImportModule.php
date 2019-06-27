<?php

$db = \Contao\System::getContainer()->get('database_connection');

$context = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
$url = '../files/daten/export.xml';

$ausgabe = simplexml_load_file($url);

$xml = file_get_contents($url, false, $context);
$xml = simplexml_load_string($xml);

foreach($xml->children() as $event) {

    // Even ID
    $eventId = $event->eventId;

    // Datum
    $eventStartDate = $event->eventStart;
    $eventStartTime = $eventStartDate;
    $eventEndDate = $event->eventEnd;
    $eventEndTime = $eventEndDate;

    // Titel
    $eventTitle = $event->eventProject;

    // Ort
    $eventLocationCity = $event->eventLocation_Place;
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

      $komponisten .= '<p class="event-komponist"><strong>' .$workComposerFullname. '</strong>, ' .$workTitle. ' ' .$workTitle2. ' ' .$workTitle3.' </p>';
    };

    // Solisten
    $solisten = '';
    foreach($event->eventSoloistItem as $soloist) {
      $solistFirstname = $soloist->soloistFirstname;
      $solistLastname = $soloist->soloistLastname;
      $solistFullname = $soloistFirstname. ' ' .$soloistLastname;
      $solistInstrument = $soloist->soloistInstrument;

      $solisten .= '<p class="event-solist">' .$solistFullname. ', ' .$solistInstrument. '</p>';
    };

    // Dirigenten
    $conductor = '<p class="event-dirigent">' .$event->eventConductor. '</p>';

    // Den Teaser mit allen Inhalten zusammenfassen
    $teaser = $komponisten . $solisten . $conductor;

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
        $Datum = date('d.m.Y', (int)$eventStartDate);
        echo '<p>' .$eventTitle. ' am ' .$Datum. ' ist bei <strong>Sonstiges</strong>';
    }
    else {
        // Wenn keine Kategorie vergeben wurde.
        $Datum = date('d.m.Y', (int)$eventStartDate);
        echo '<p>' .$eventTitle. ' am ' .$Datum. ' wurde <strong>keiner Kategorie</strong> zugewiesen';
    }

    // Datenbank aktualisieren.
    $sqlUpdate = "REPLACE INTO tl_calendar_events(id, pid, title, addTime, startTime, endTime, startDate, endDate, location, teaser) VALUES ('" .$eventId. "', '" .$kategorie. "', '" .$eventTitle. "', '1', '" .$eventStartTime. "', '" .$eventEndTime. "', '" .$eventStartDate. "', '" .$eventEndDate. "', '" .$eventLocation. "', '" .$teaser. "')";

    // Datenbank neue Datensätze hinzufügen.
    $sqlNew = "INSERT INTO tl_calendar_events(id, pid, title, addTime, startTime, endTime, startDate, endDate, location, teaser) VALUES ('" .$eventId. "', '" .$kategorie. "', '" .$eventTitle. "', '1', '" .$eventStartTime. "', '" .$eventEndTime. "', '" .$eventStartDate. "', '" .$eventEndDate. "', '" .$eventLocation. "', '" .$teaser. "')";

    $result = mysqli_query($db, $sqlNew);

}

echo '<script type="text/javascript" language="Javascript">alert("Der Import ist abgeschlossen")</script>';
