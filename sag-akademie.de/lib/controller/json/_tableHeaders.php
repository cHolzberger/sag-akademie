<?php
global $_tableHeaders;
$_tableHeaders = array();

// Tabelle: ViewAkquise 
$head = array();
$head[] = array("field" => 'id', "label" => "ID", "format" => "default", "hide" => true);
$head[] = array("field" => 'firma', "label" => "Firma", "format" => "default", "editable" => "true");
$head[] = array("field" => 'strasse', "label" => "Straße", "format" => "default", "editable" => "true");
$head[] = array("field" => 'plz', "label" => "PLZ", "format" => "default", "editable" => "true");
$head[] = array("field" => 'ort', "label" => "Ort", "format" => "default", "editable" => "true");
//$head[] = array ( "field"=> 'bundesland',"label"=>"Bundesland", "format"=>"default", "editable"=>"true");
$head[] = array("field" => 'bundesland_id', "label" => "Bundesland", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XBundesland")->findAll(Doctrine::HYDRATE_ARRAY));
$head[] = array("field" => 'kreis', "label" => "Kreis", "format" => "default", "editable" => "true");
$head[] = array("field" => 'regierungsbezirk', "label" => "Regierungsbezirk", "format" => "default", "editable" => "true");
$head[] = array("field" => 'kontaktkategorie', "label" => "Kontaktkategorie", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("KontaktKategorie")->findAll(Doctrine::HYDRATE_ARRAY));
$head[] = array("field" => 'branche_id', "label" => "Branche", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XBranche")->findAll(Doctrine::HYDRATE_ARRAY));
$head[] = array("field" => 'taetigkeitsbereich_id', "label" => "Tätigkeitsbereich", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XTaetigkeitsbereich")->findAll(Doctrine::HYDRATE_ARRAY));
$head[] = array("field" => 'tel', "label" => "Telefon", "format" => "default", "editable" => "true");
$head[] = array("field" => 'fax', "label" => "Fax", "format" => "default", "editable" => "true");
$head[] = array("field" => 'abteilung', "label" => "Abteilung", "format" => "default", "editable" => "true");
//$head[] = array ( "field"=> 'anrede',"label"=>"Anrede", "format"=>"combo", "editable"=>"true", "values"=>Doctrine::getTable("XAnrede")->findAll(Doctrine::HYDRATE_ARRAY) );
$head[] = array("field" => 'anrede', "label" => "Anrede", "format" => "default");
$head[] = array("field" => 'titel', "label" => "Titel", "format" => "default", "editable" => "true");
$head[] = array("field" => 'alias', "label" => "Alias", "format" => "default", "editable" => "true");
$head[] = array("field" => 'vorname', "label" => "Vorname", "format" => "default", "editable" => "true");
$head[] = array("field" => 'name', "label" => "Name", "format" => "default", "editable" => "true");
$head[] = array("field" => 'tel_durchwahl', "label" => "Telefon Durchwahl", "format" => "default", "editable" => "true");
$head[] = array("field" => 'mobil', "label" => "Mobil", "format" => "default", "editable" => "true");
$head[] = array("field" => 'url', "label" => "Webseite", "format" => "web", "editable" => "true");
$head[] = array("field" => 'email', "label" => "E-Mail Firma", "format" => "email", "editable" => "true");
$head[] = array("field" => 'email2', "label" => "2. E-Mail", "format" => "default");
$head[] = array("field" => 'notiz', "label" => "Notiz", "format" => "default");
$head[] = array("field" => 'newsletter', "label" => "Newsletter", "format" => "bool", "editable" => "true");
$head[] = array("field" => 'zusatz', "label" => "Zusatz", "format" => "default", "editable" => "true");
$head[] = array("field" => 'abmelde_datum', "label" => "Abmelde-Datum", "format" => "date");
$head[] = array("field" => 'anmelde_datum', "label" => "Anmelde-Datum", "format" => "date");
//$head[] = array ( "field"=> 'geaendert_von',"label"=>"Geändert von", "format"=>"date");
//$head[] = array ( "field"=> 'bereits_in_verteiler',"label"=>"Bereits in Verteiler", "format"=>"date");
$head[] = array("field" => 'vergleich', "label" => "Vergleich", "format" => "default", "editable" => "true");
$head[] = array("field" => 'umkreis', "label" => "Umkreis", "format" => "default", "editable" => "true");
$head[] = array("field" => 'branche', "label" => "Branche", "format" => "default", "editable" => "true");
$head[] = array("field" => 'kontakt_quelle_name', "label" => "Datenpool", "format" => "default");
$head[] = array("field" => 'geaendert', "label" => "Geändert", "format" => "default");

$_tableHeaders["ViewAkquise"] = $head;

// Tabelle: Buchung und ViewBuchung
$head = array();
// id - hidden
$head[] = array("field" => 'id', "label" => "ID", "format" => "default", 'hide' => true);
$head[] = array("field" => 'seminar_id', "label" => "SeminarID", "format" => "default", 'hide' => true);
$head[] = array("field" => 'person_id', "label" => "PersonID", "format" => "default", 'hide' => true);
$head[] = array("field" => 'kontakt_id', "label" => "KontaktID", "format" => "default", 'hide' => true);

// Status
$head[] = array("field" => 'status', "group" => "Status", "label" => "Status", "format" => "status");

//Buchung
$head[] = array("field" => 'storno_datum', "group" => "Buchung", "label" => "Storno Datum", "format" => "date");
$head[] = array("field" => 'storno_status', "group" => "Buchung", "label" => "Storno Status*", "format" => "default", "editable" => "true");
$head[] = array("field" => 'umbuchungs_datum', "group" => "Buchung", "label" => "Umbuchungsdatum", "format" => "date");
$head[] = array("field" => 'datum', "group" => "Buchung", "label" => "Buchungsdatum", "format" => "datetime");

// Rechnung
$head[] = array("field" => 'rechnungsnummer', "group" => "Rechnung", "label" => "ReNr.*", "format" => "default", "editable" => "true");
$head[] = array("field" => 'rechnungsnummer1', "group" => "Rechnung", "label" => "ReNr 2.*", "format" => "default", "editable" => "true");
$head[] = array("field" => 'rechnungsnummer2', "group" => "Rechnung", "label" => "ReNr 3.*", "format" => "default", "editable" => "true");
$head[] = array("field" => 'rabatt', "label" => "Rabatt*", "group" => "Rechnung", "format" => "default", "editable" => "true");
$head[] = array("field" => 'rechnunggestellt', "group" => "Rechnung", "label" => "Rechnung gestellt", "format" => "date");
$head[] = array("field" => 'zahlungseingang_datum', "group" => "Rechnung", "label" => "Bezahlt", "format" => "date");

// Seminar
$head[] = array("field" => 'seminar_art_id', "group" => "Seminar", "label" => "Seminar", "format" => "default");
$head[] = array("field" => 'datum_begin', "group" => "Seminar", "label" => "Beginn", "format" => "date");
$head[] = array("field" => 'datum_ende', "group" => "Seminar", "label" => "Ende", "format" => "date");
$head[] = array("field" => 'kursnr', "group" => "Seminar", "label" => "Kurs Nr.", "format" => "default");

// Teilnehmer
$head[] = array("field" => 'firma', "group" => "Teilnehmer", "label" => "Firma", "format" => "default");
$head[] = array("field" => 'vorname', "group" => "Teilnehmer", "label" => "Vorname", "format" => "default");
$head[] = array("field" => 'name', "group" => "Teilnehmer", "label" => "Name", "format" => "default");
$head[] = array("field" => 'strasse', "group" => "Teilnehmer", "label" => "Straße", "format" => "default");
$head[] = array("field" => 'plz', "group" => "Teilnehmer", "label" => "PLZ", "format" => "default");
$head[] = array("field" => 'ort', "group" => "Teilnehmer", "label" => "Ort", "format" => "default");
$head[] = array("field" => 'bundesland', "group" => "Teilnehmer", "label" => "Bundesland", "format" => "default");
$head[] = array("field" => 'land', "group" => "Teilnehmer", "label" => "Land", "format" => "default");
$head[] = array("field" => 'ansprechpartner', "group" => "Teilnehmer", "label" => "Ansprechpartner", "format" => "bool");
$head[] = array("field" => 'grad', "group" => "Teilnehmer", "label" => "Grad", "format" => "default");
$head[] = array("field" => 'funktion', "group" => "Teilnehmer", "label" => "Funktion", "format" => "default");

$head[] = array("field" => 'seminar_unterlagen', "group" => "Teilnehmer", "label" => "Unterlagen", "format" => "bool");

//$head[] = array ( "field"=> 'person_strasse', "label"=>"Straße", "format"=>"default");
//$head[] = array ( "field"=> 'Person_nr', "label"=>"Nr.", "format"=>"default");
//$head[] = array ( "field"=> 'Person_plz', "label"=>"PLZ", "format"=>"default");
//$head[] = array ( "field"=> 'Person_ort', "label"=>"Ort", "format"=>"default");
//
// Kommunikation
$head[] = array("field" => 'tel', "group" => "Teilnehmer-Kommunikation", "label" => "Telefon", "format" => "default");
$head[] = array("field" => 'fax', "group" => "Teilnehmer-Kommunikation", "label" => "Fax", "format" => "default");
$head[] = array("field" => 'mobil', "group" => "Teilnehmer-Kommunikation", "label" => "Mobil", "format" => "default");
$head[] = array("field" => 'email', "group" => "Teilnehmer-Kommunikation", "label" => "Email privat", "format" => "default");
$head[] = array("field" => 'geburtstag', "group" => "Teilnehmer", "label" => "Geburtstag", "format" => "date");
$head[] = array("field" => 'kontakt_email', "group" => "Teilnehmer-Kommunikation", "label" => "Email (Firma)", "format" => "default");
$head[] = array("field" => 'ansprechpartner_email', "group" => "Teilnehmer-Kommunikation", "label" => "Email (Anspr.)", "format" => "default");

// Förderung
$head[] = array("field" => 'kontakt_vdrk_mitglied', "group" => "Teilnehmer-Förderung", "label" => "VDRK Mitglied", "format" => "bool");
$head[] = array("field" => 'kontakt_vdrk_mitglied', "group" => "Teilnehmer-Förderung", "label" => "VDRK Mitglied Nr.", "format" => "default");
$head[] = array("field" => 'vdrk_referrer', "group" => "Teilnehmer-Förderung", "label" => "Buchung über VDRK", "format" => "bool");
$head[] = array("field" => 'arbeitsagentur', "group" => "Teilnehmer-Förderung", "label" => "Arbeitsagentur", "format" => "bool");
$head[] = array("field" => 'zustaendige_arbeitsagentur', "group" => "Teilnehmer-Förderung", "label" => "zust. Arbeitsagentur", "format" => "default");
$head[] = array("field" => 'bildungscheck', "group" => "Teilnehmer-Förderung", "label" => "Bildungscheck", "format" => "bool");
$head[] = array("field" => 'bildungscheck_ausstellung_ort', "group" => "Teilnehmer-Förderung", "label" => "Ausstellungsort", "format" => "default");
$head[] = array("field" => 'bildungscheck_ausstellung_datum', "group" => "Teilnehmer-Förderung", "label" => "Ausstellungsdatum", "format" => "default");
$head[] = array("field" => 'bildungscheck_ausstellung_bundesland_id', "group" => "Teilnehmer-Förderung", "label" => "Ausstellungsbundesland", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XBundesland")->findAll(Doctrine::HYDRATE_ARRAY));

// Hotelbuchung
$head[] = array("field" => 'HotelBuchung:hotel_name', "group" => "Hotel-Buchung", "label" => "Hotel", "format" => "default");
$head[] = array("field" => 'HotelBuchung:anreise_datum', "group" => "Hotel-Buchung", "label" => "Anreisedatum", "format" => "default");
$head[] = array("field" => 'HotelBuchung:storno_datum', "group" => "Hotel-Buchung", "label" => "Stornodatum", "format" => "date");
$head[] = array("field" => 'HotelBuchung:uebernachtungen', "group" => "Hotel-Buchung", "label" => "Übernachtungen", "format" => "default");

// Informationen
$head[] = array("field" => 'bemerkung', "group" => "Info", "label" => "Notiz Kunde*", "format" => "default", "editable" => "true");
$head[] = array("field" => 'notiz', "group" => "Info", "label" => "Notiz SAG*", "format" => "default", "editable" => "true");
$head[] = array("field" => 'teilgenommen', "group" => "Info", "label" => "Nicht Teilgenommen", "format" => "bool", "editable" => "true");

$_tableHeaders['ViewBuchung']= $head;
$_tableHeaders['Buchung'] = $head;

//Tabelle: Download
$head = array();
$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);
$head[] = array ("field"=> 'name', "label"=>"Name", "format"=>"text", "editable"=>"true", "group"=>"Download");
$head[] = array ("field"=> 'text', "label"=>"Text", "format"=>"text", "editable"=>"true", "group"=>"Download");
$head[] = array("field" => 'kategorie', "label" => "Kategorie", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("DownloadKategorie")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Download");
$head[] = array ("field"=> 'deleted_at', "label"=>"Deleted", "format"=>"text" , "hide"=>true);

$_tableHeaders['Download'] = $head;

//Tabelle: Feiertag
$head = array();
$head[] = array("field" => 'id', "label" => "ID", "format" => "default", "hide" => true);
$head[] = array("field" => 'datum', "label" => "Datum", "format" => "date", "editable" => "true", "group"=>"Feiertag");

$head[] = array("field" => 'name', "label" => "Name", "format" => "default", "editable" => "true", "group"=>"Feiertag");
$_tableHeaders['Feiertag'] = $head;

//Tabelle: Hotel
$head = array();
$head[] = array ("field"=> 'aktiv', "label"=>"Aktiv", "format"=>"bool", "group" => "Sichtbarkeit");
$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);
$head[] = array ("field"=> 'standort_id', "label"=>"Standort", "format"=>"default","hide"=>true);
$head[] = array ("field"=> 'name',"label"=>"Name", "format"=>"default","editable"=>"true","group" => "Kontaktdaten");
$head[] = array ("field"=> 'strasse',"label"=>"Straße", "format"=>"default", "editable"=>"true" ,"group" => "Adresse");
$head[] = array ("field"=> 'nr', "label"=>"Nr", "format"=>"default", "editable"=>"true","group" => "Adresse");
$head[] = array ("field"=> 'tel',"label"=>"Tel.", "format"=>"default", "editable"=>"true","group" => "Kontaktdaten");
$head[] = array ("field"=> 'fax', "label"=>"Fax", "format"=>"default", "editable"=>"true","group" => "Kontaktdaten");
$head[] = array ("field"=> 'email', "label"=>"E-Mail", "format"=>"email", "editable"=>"true","group" => "Kontaktdaten");
$head[] = array ("field"=> 'url',"label"=>"Homepage", "format"=>"web", "editable"=>"true","group" => "Kontaktdaten");
$head[] = array ("field"=> 'ansprechpartner', "label"=>"Ansprechpartner", "format"=>"default", "editable"=>"true","group" => "Kontaktdaten");
$head[] = array ("field"=> 'kategorie', "label"=>"Kategorie", "format"=>"default", "editable"=>"true","group" => "Kontaktdaten");
$head[] = array ("field"=> 'beschreibung', "label"=>"Beschreibung", "format"=>"default", "editable"=>"true", "group"=>"Informationen");
$head[] = array ("field"=> 'entfernung', "label"=>"Entfernung", "format"=>"default", "editable"=>"true", "group"=>"Adresse");
$head[] = array ("field"=> 'ort', "label"=>"Ort", "format"=>"default", "editable"=>"true", "group"=>"Adresse");
$head[] = array ("field"=> 'plz', "label"=>"PLZ", "format"=>"default", "editable"=>"true", "group"=>"Adresse");
$head[] = array ("field"=> 'notiz', "label"=>"Notiz", "format"=>"default", "editable"=>"true","group"=>"Informationen");

$_tableHeaders['Hotel'] = $head;

//Tabelle: InhouseSeminar (Seminar variation Inhouse)
$head = array();


$head[] = array("field" => 'id', "label" => "ID", "format" => "default", 'hide' => true);
$head[] = array("field" => 'kontakt_id', "label" => "Kontakt-ID", "format" => "default", 'hide' => true);
$head[] = array("field" => 'kontakt_firma', "label" => "Kunde", "format" => "default", "group" => "Kunde");

$head[] = array("field" => 'kursnr', "label" => "Kurs Nr.", "format" => "default", "editable" => "true","group"=>"Termin");

$head[] = array("field" => 'freigabe_name', "label" => "Freigabe", "format" => "default", "group" =>"Freigabe");
$head[] = array("field" => 'freigabe_datum', "label" => "Freigabe Datum", "format" => "date", "group" =>"Freigabe");


$head[] = array("field" => 'inhouse_ort', "label" => "Ort", "format" => "default", "group" => "Veranstaltungsort", "editable"=>"true");
$head[] = array("field" => 'inhouse_plz', "label" => "PLZ", "format" => "default", "group" => "Veranstaltungsort", "editable"=>"true");
$head[] = array("field" => 'inhouse_strasse', "label" => "Strasse", "format" => "default", "group" => "Veranstaltungsort", "editable"=>"true");

$head[] = array("field" => 'seminar_art_id', "label" => "Seminar", "format" => "default", "group"=>"Termin");
//$head[] = array ("field"=> 'anzahlNichtBestaetigt', "label"=>"Nichbestätigte Buchungen", "format"=>"default");
$head[] = array("field" => 'datum_begin', "label" => "Beginn", "format" => "date","group"=>"Termin");
$head[] = array("field" => 'datum_ende', "label" => "Ende", "format" => "date","group"=>"Termin");
$head[] = array("field" => 'anzahlBestaetigt', "label" => "Buchungen", "format" => "default", "group"=>"Teilnehmer");

$head[] = array("field" => 'teilnehmer_min', "label" => "TN Minimum", "format" => "default", "editable" => "true", "group"=>"Teilnehmer");
$head[] = array("field" => 'teilnehmer_max', "label" => "TN Maximum", "format" => "default", "editable" => "true", "group"=>"Teilnehmer");
$head[] = array("field" => 'storno_datum', "label" => "Storno Datum", "format" => "date","group"=>"Termin");
$head[] = array("field" => 'standort_name', "label" => "Standort", "format" => "default", "group"=>"Kosten & Preise");
$head[] = array("field" => 'kosten_verpflegung', "label" => "Kosten Verpflegung", "format" => "price", "editable" => "true", "group"=>"Kosten & Preise");
$head[] = array("field" => 'kosten_unterlagen', "label" => "Kosten Unterlagen", "format" => "price", "editable" => "true", "group"=>"Kosten & Preise");
$head[] = array("field" => 'kursgebuehr', "label" => "Kursgebühr", "format" => "price", "editable" => "true", "group"=>"Kosten & Preise");
$head[] = array("field" => 'uelaenge', "label" => "UE", "format" => "default", "group"=>"Termin");
//$head[] = array ( "field"=> 'nettoep', "label"=>"Netto EP", "format"=>"price");
$head[] = array("field" => 'kosten_allg', "label" => "Kosten Allgemein", "format" => "price", "editable" => "true", "group"=>"Kosten & Preise");
$head[] = array("field" => 'kosten_refer', "label" => "Kosten Referent", "format" => "price", "editable" => "true", "group"=>"Kosten & Preise");
$head[] = array("field" => 'hinweis', "label" => "Hinweis", "format" => "default", 'editable' => 'true',"group"=>"Info");
$head[] = array("field" => 'interne_information', "label" => "Interne Informationen", "format" => "default", 'editable' => 'true' ,"group"=>"Info");

$_tableHeaders['InhouseSeminar'] = $head;

// Tabelle: InhouseSeminarArt (SeminarArt variation Inhouse)
$head = array();
$head[] = array("field" => 'id', "label" => "KursNr.", "format" => "default", "group"=>"Kurs");
$head[] = array("field" => 'status', "label" => "Status", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtStatus")->findAll(Doctrine::HYDRATE_ARRAY),"group"=>"Kurs");
$head[] = array("field" => 'rubrik', "label" => "Rubrik 1", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");
$head[] = array("field" => 'rubrik2', "label" => "Rubrik 2", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");
$head[] = array("field" => 'rubrik3', "label" => "Rubrik 3", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");
$head[] = array("field" => 'rubrik4', "label" => "Rubrik 4", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");
$head[] = array("field" => 'rubrik5', "label" => "Rubrik 5", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");

$head[] = array("field" => 'bezeichnung', "label" => "Bezeichnung", "format" => "default", 'editable' => 'true', "group"=>"Informationen");
$head[] = array("field" => 'kurzbeschreibung', "label" => "Kurzbeschreibung", "format" => "default", 'editable' => 'true', "group"=>"Informationen");
$head[] = array("field" => 'langbeschreibung', "label" => "Langbeschreibung", "format" => "default", 'editable' => 'true', "group"=>"Informationen");

//$head[] = array ( "field"=> 'nettoEp',"label"=>"Netto EP", "format"=>"price"); // netto ep wird nicht mehr benutzt!!!
$head[] = array("field" => 'nachweise', "label" => "Nachweise", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'voraussetzungen', "label" => "Voraussetzungen", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'zielgruppe', "label" => "Zielgruppe", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'organisatorisches', "label" => "Organisatorisches", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'geraetschaften', "label" => "Gerätschaften", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'material', "label" => "Material", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'seminar_hinweis', "label" => "Hinweis", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
//$head[] = array ("field"=> 'art', "label"=>"Art", "format"=>"default");
$head[] = array("field" => 'dauer', "label" => "Dauer", "format" => "default", 'editable' => "true", "group"=>"Planung");
$head[] = array("field" => 'stundenzahl', "label" => "Stundenzahl", "format" => "default", 'editable' => "true", "group"=>"Planung");
$head[] = array("field" => 'teilnehmer_min_tpl', "label" => "TN Minimum", "format" => "default", 'editable' => "true","group"=>"Planung");
$head[] = array("field" => 'teilnehmer_max_tpl', "label" => "TN Maximum", "format" => "default", 'editable' => "true","group"=>"Planung");
$head[] = array("field" => 'kosten_verpflegung', "label" => "Kosten Verpflegung", "format" => "price", 'editable' => "true","group"=>"Ausgaben / Gewinn");
$head[] = array("field" => 'kosten_unterlagen', "label" => "Kosten Unterlagen", "format" => "price", 'editable' => "true","group"=>"Ausgaben / Gewinn");
$head[] = array("field" => 'kosten_allg', "label" => "Verwaltungskosten (%)", "format" => "default", 'editable' => "true","group"=>"Ausgaben / Gewinn");
$head[] = array("field" => 'kosten_pruefung', "label" => "Prüfungskosten (%)", "format" => "default", 'editable' => "true","group"=>"Ausgaben / Gewinn");
$head[] = array("field" => 'kosten_refer', "label" => "Kosten Referent", "format" => "price", 'editable' => "true","group"=>"Ausgaben / Gewinn");
$head[] = array("field" => 'kursgebuehr', "label" => "Kursgebühr", "format" => "price", 'editable' => "true","group"=>"Ausgaben / Gewinn");
//$head[] = array ("field"=> 'infolink', "label"=>"Info Link", "format"=>"default");




$_tableHeaders['InhouseSeminarArt'] = $head;

// Tabelle: Kontakt
$head = array();
$head[] = array("field" => 'id', "label" => "ID", "format" => "default", "hide" => true);
$head[] = array("field" => 'kontext', "label" => "Art", "format" => "default", "editable" => "false","group"=>"Art");

$head[] = array("field" => 'firma', "label" => "Firma", "format" => "default", "editable" => "true","group"=>"Anschrift");
$head[] = array("field" => 'hauptverwaltung', "label" => "Niederlassung", "format" => "bool","group"=>"Anschrift");
$head[] = array("field" => 'alias', "label" => "Alias", "format" => "default", "editable" => "true","group"=>"Anschrift");
$head[] = array("field" => 'strasse', "label" => "Straße", "format" => "default", "editable" => "true","group"=>"Anschrift");
$head[] = array("field" => 'plz', "label" => "PLZ", "format" => "default", "editable" => "true","group"=>"Anschrift");
$head[] = array("field" => 'ort', "label" => "Ort", "format" => "default", "editable" => "true","group"=>"Anschrift");
$head[] = array("field" => 'privat', "label" => "Privat", "format" => "bool","group"=>"Anschrift");
$head[] = array("field" => 'bundesland_id', "label" => "Bundesland", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XBundesland")->findAll(Doctrine::HYDRATE_ARRAY),"group"=>"Anschrift");
$head[] = array("field" => 'kreis', "label" => "Kreis", "format" => "default", "editable" => "true","group"=>"Anschrift");
$head[] = array("field" => 'regierungsbezirk', "label" => "Regierungsbezirk", "format" => "default", "editable" => "true","group"=>"Anschrift");
$head[] = array("field" => 'land_id', "label" => "Land", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XLand")->findAll(Doctrine::HYDRATE_ARRAY),"group"=>"Anschrift");
$head[] = array("field" => 'tel', "label" => "Tel.", "format" => "default", "editable" => "true","group"=>"Kontakt");
$head[] = array("field" => 'email', "label" => "E-Mail", "format" => "email", "editable" => "true","group"=>"Kontakt");
$head[] = array("field" => 'email2', "label" => "2. E-Mail", "format" => "email", "editable" => "true","group"=>"Kontakt");

$head[] = array("field" => 'fax', "label" => "Fax", "format" => "default", "editable" => "true","group"=>"Kontakt");
$head[] = array("field" => 'url', "label" => "Homepage", "format" => "web", "editable" => "true","group"=>"Kontakt");
//$head[] = array ( "field"=> 'kontaktkategorie',"label"=>"Kontaktkategorie", "format"=>"default");
$head[] = array("field" => 'kontaktkategorie', "label" => "Kontaktkategorie", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("KontaktKategorie")->findAll(Doctrine::HYDRATE_ARRAY),"group"=>"Rubriken");
$head[] = array("field" => 'branche_id', "label" => "Branche", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XBranche")->findAll(Doctrine::HYDRATE_ARRAY),"group"=>"Rubriken");
$head[] = array("field" => 'taetigkeitsbereich_id', "label" => "Tätigkeitsbereich", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XTaetigkeitsbereich")->findAll(Doctrine::HYDRATE_ARRAY),"group"=>"Rubriken");


//$head[] = array ( "field"=> 'bundesland_name',"label"=>"Bundesland", "format"=>"default");

//$head[] = array ( "field"=> 'land_name',"label"=>"Land", "format"=>"default");
//$head[] = array("field" => 'branche', "label" => "Branche", "format" => "default", "editable" => "true");
$head[] = array("field" => 'vdrk_mitglied', "label" => "VDRK Mitglied", "format" => "bool","group"=>"VDRK");
$head[] = array("field" => 'vdrk_mitglied_nr', "label" => "VDRK Mitglied Nr.", "format" => "default", "editable" => "true","group"=>"VDRK");
$head[] = array("field" => 'kundenstatus', "label" => "Kundenstatus", "format" => "default", "editable" => "true");
$head[] = array("field" => 'kontakt_quelle_stand', "label" => "Kontakt-Quelle Stand", "format" => "date","group"=>"Kundendaten");
$head[] = array("field" => 'kontakt_quelle', "label" => "Kontakt-Quelle", "format" => "combo","group"=>"Kundendaten", "values" => Doctrine::getTable("KontaktQuelle")->findAll(Doctrine::HYDRATE_ARRAY) );


$head[] = array("field" => 'kundennr', "label" => "Kunden-Nr.", "format" => "default", "editable" => "true","group"=>"Kundendaten");
//$head[] = array("field" => 'zusatz', "label" => "Zusatz", "format" => "default", "editable" => "true");
$head[] = array("field" => 'blz', "label" => "BLZ", "format" => "default", "editable" => "true","group"=>"Bankverbindung");
$head[] = array("field" => 'kto', "label" => "Konto", "format" => "default", "editable" => "true","group"=>"Bankverbindung");
$head[] = array("field" => 'bank', "label" => "Bank", "format" => "default", "editable" => "true","group"=>"Bankverbindung");
$head[] = array("field" => 'zahlart', "label" => "Zahlart", "format" => "default", "editable" => "true","group"=>"Bankverbindung");
$head[] = array("field" => 'bemerkung', "label" => "Bemerkung", "format" => "default", "editable" => "false","group"=>"Informationen");
$head[] = array("field" => 'notiz', "label" => "Notiz", "format" => "default", "editable" => "false","group"=>"Informationen");
$head[] = array("field" => 'newsletter', "label" => "Newsletter", "format" => "bool", "editable" => "true","group"=>"Informationen");
//$head[] = array ( "field"=> 'angelegt_user_id',"label"=>"Angelegt von", "format"=>"default");
$head[] = array("field" => 'mobil', "label" => "Mobil", "format" => "default", "editable" => "true");
$head[] = array("field" => 'wiedervorlage', "label" => "Wiedervorlage", "format" => "bool", "editable" => "true");

$head[] = array("field" => 'newsletter_abmeldedatum', "label" => "Newsletter Abmeldedatum", "format" => "date");
$head[] = array("field" => 'newsletter_anmeldedatum', "label" => "Newsletter Anmeldedatum", "format" => "date");

$_tableHeaders['Kontakt'] = $head;

//Tabelle: KontakteFull (Kontakte und AkquiseKontakte)
$head = array();
$head[] = array("field" => 'id', "label" => "ID", "format" => "default", "hide" => true);
$head[] = array("field" => 'quelle', "label" => "Quelle", "format" => "quelle", "hide"=>true);
$head[] = array("field" => 'firma', "label" => "Firma", "format" => "default", "editable" => "true", "group"=>"Anschrift");
$head[] = array("field" => 'strasse', "label" => "Straße", "format" => "default", "editable" => "true", "group"=>"Anschrift");
$head[] = array("field" => 'plz', "label" => "PLZ", "format" => "default", "editable" => "true", "group"=>"Anschrift");
$head[] = array("field" => 'ort', "label" => "Ort", "format" => "default", "editable" => "true", "group"=>"Anschrift");
//$head[] = array ( "field"=> 'bundesland',"label"=>"Bundesland", "format"=>"default");
$head[] = array("field" => 'bundesland_id', "label" => "Bundesland", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XBundesland")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Anschrift");
$head[] = array("field" => 'kreis', "label" => "Kreis", "format" => "default", "editable" => "true", "group"=>"Anschrift");
$head[] = array("field" => 'regierungsbezirk', "label" => "Regierungsbezirk", "format" => "default", "editable" => "true", "group"=>"Anschrift");
$head[] = array("field" => 'kontaktkategorie', "label" => "Kontaktkategorie", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("KontaktKategorie")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubrik");
$head[] = array("field" => 'branche_id', "label" => "Branche", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XBranche")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubrik");
$head[] = array("field" => 'taetigkeitsbereich_id', "label" => "Tätigkeitsbereich", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XTaetigkeitsbereich")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubrik");
$head[] = array("field" => 'tel', "label" => "Tel.", "format" => "default", "editable" => "true", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'fax', "label" => "Fax", "format" => "default", "editable" => "true", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'abteilung', "label" => "Abteilung", "format" => "default", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'anrede', "label" => "Anrede", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XAnrede")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'titel', "label" => "Titel", "format" => "default", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'name', "label" => "Name", "format" => "default", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'vorname', "label" => "Vorname", "format" => "default", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'tel_durchwahl', "label" => "Telefon Durchwahl", "format" => "default", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'mobil', "label" => "Mobil", "format" => "default", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'url', "label" => "Webseite", "format" => "web", "editable" => "true", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'email', "label" => "E-Mail Firma", "format" => "email", "editable" => "true", "group"=>"Kontakt / Ansprechpartner");
$head[] = array("field" => 'newsletter', "label" => "Newsletter", "format" => "bool", "editable" => "true", "group"=>"Newsletter");
$head[] = array("field" => 'abmelde_datum', "label" => "Abmelde-Datum", "format" => "date", "group"=>"Newsletter");
$head[] = array("field" => 'anmelde_datum', "label" => "Anmelde-Datum", "format" => "date", "group"=>"Newsletter");
$head[] = array("field" => 'bereits_in_verteiler', "label" => "Bereits in Verteiler", "format" => "default");
$head[] = array("field" => 'vergleich', "label" => "Vergleich", "format" => "default");
$head[] = array("field" => 'umkreis', "label" => "Umkreis", "format" => "default");
$head[] = array("field" => 'geaendert_von', "label" => "Geändert von", "format" => "default", "group"=>"Informationen");


$head[] = array("field" => 'kontakt_quelle_name', "label" => "Datenpool", "format" => "default","group"=>"Informationen");
$head[] = array("field" => 'geaendert', "label" => "Geändert", "format" => "datetime","group"=>"Informationen");

$_tableHeaders['KontakteFull'] = $head;

//Tabelle: Kooperationspartner
$head = array();
$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);
$head[] = array ("field"=> 'name', "label"=>"Name", "format"=>"text", "editable"=>"true", "group"=>"Kooperationspartner");
$head[] = array ("field"=> 'text', "label"=>"Text", "format"=>"text", "editable"=>"true", "group"=>"Kooperationspartner");
$head[] = array ("field"=> 'link', "label"=>"Link", "format"=>"text", "editable"=>"true", "group"=>"Kooperationspartner");
$head[] = array ("field"=> 'kategorie', "label"=>"Kategorie", "format"=>"text", "editable"=>"true", "group"=>"Kooperationspartner");

$head[] = array ("field"=> 'deleted_at', "label"=>"Deleted", "format"=>"text" , "hide"=>true);

$_tableHeaders['Kooperationspartner'] = $head;

//Tabelle: Neuigkeit
$head = array();
$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);
$head[] = array ("field"=> 'datum',"label"=>"Datum", "format"=>"date", "editable"=>"true", "group"=>"Aktuelles");
$head[] = array ("field"=> 'titel', "label"=>"Titel", "format"=>"text", "editable"=>"true", "group"=>"Aktuelles");
$head[] = array ("field"=> 'text', "label"=>"Text", "format"=>"text", "editable"=>"true", "group"=>"Aktuelles");
$head[] = array ("field"=> 'deleted_at', "label"=>"Deleted", "format"=>"text" , "hide"=>true);

$_tableHeaders['Neuigkeit'] = $head;

//Tabelle: Person
$head = array();
$head[] = array("field" => 'id', "label" => "ID", "format" => "default", "hide" => true);
$head[] = array("field" => 'kontakt_id', "label" => "Kontakt ID", "format" => "default", "hide" => true);
$head[] = array("field" => 'vorname', "label" => "Vorname", "format" => "default", "editable" => "true");
$head[] = array("field" => 'name', "label" => "Name", "format" => "default", "editable" => "true");
$head[] = array("field" => 'strasse', "label" => "Straße", "format" => "default", "editable" => "true");
$head[] = array("field" => 'plz', "label" => "PLZ", "format" => "default", "editable" => "true");
$head[] = array("field" => 'ort', "label" => "Ort", "format" => "default", "editable" => "true");
$head[] = array("field" => 'land_id', "label" => "Land", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("XLand")->findAll(Doctrine::HYDRATE_ARRAY));
//$head[] = array ( "field"=> 'land_name',"label"=>"Land", "format"=>"default");
//$head[] = array("field" => 'branche', "label" => "Branche", "format" => "default");
$head[] = array("field" => 'grad', "label" => "Grad", "format" => "default", "editable" => "true");
$head[] = array("field" => 'geschlecht', "label" => "Anrede", "format" => "combo", "values" => Doctrine::getTable("XAnrede")->findAll(Doctrine::HYDRATE_ARRAY));
$head[] = array("field" => 'geburtstag', "label" => "Geburtstag", "format" => "date");
$head[] = array("field" => 'tel', "label" => "Tel.", "format" => "default", "editable" => "true");
$head[] = array("field" => 'email', "label" => "EMail", "format" => "email", "editable" => "true");
$head[] = array("field" => 'fax', "label" => "Fax", "format" => "default", "editable" => "true");
$head[] = array("field" => 'mobil', "label" => "Mobil", "format" => "default", "editable" => "true");
$head[] = array("field" => 'funktion', "label" => "Funktion", "format" => "default", "editable" => "true");
$head[] = array("field" => 'abteilung', "label" => "Abteilung", "format" => "default", "editable" => "true");
$head[] = array("field" => 'ansprechpartner', "label" => "Ansprechpartner", "format" => "bool");
$head[] = array("field" => 'geschaeftsfuehrer', "label" => "Geschäftsführer", "format" => "bool");
$head[] = array("field" => 'newsletter', "label" => "Newsletter", "format" => "bool", "editable" => "true");
$head[] = array("field" => 'newsletter_abmeldedatum', "label" => "Newsletter Abmeldedatum", "format" => "date");
$head[] = array("field" => 'newsletter_anmeldedatum', "label" => "Newsletter Anmeldedatum", "format" => "date");
$head[] = array("field" => 'notiz', "label" => "Notiz", "format" => "default", "editable" => "true");
$head[] = array("field" => 'login_name', "label" => "Benutzername", "format" => "default");
$head[] = array("field" => 'login_password', "label" => "Passwort", "format" => "default");
$head[] = array("field" => 'gesperrt', "label" => "Gesperrt", "format" => "bool");
$head[] = array("field" => 'gesperrt_info', "label" => "Gesperrt Hinweis", "format" => "default");
$head[] = array("field" => 'aktiv', "label" => "Aktiv", "format" => "bool");
$head[] = array("field" => 'wiedervorlage', "label" => "Wiedervorlage", "format" => "bool");

$_tableHeaders['Person'] = $head;

//Tabelle: Seminar
$head = array();
$head[] = array("field" => 'id', "label" => "ID", "format" => "default", 'hide' => true);
$head[] = array("field" => 'freigabe_name', "label" => "Freigabe", "format" => "default", "group" => "Freigabe");
$head[] = array("field" => 'freigabe_datum', "label" => "Freigabe Datum", "format" => "date", "group" => "Freigabe");

$head[] = array("field" => 'seminar_art_id', "label" => "Seminar", "format" => "default" , "group" => "Seminar");
$head[] = array("field" => 'anzahlBestaetigt', "label" => "Buchungen", "format" => "default", "group" => "Termin");
//$head[] = array ("field"=> 'anzahlNichtBestaetigt', "label"=>"Nichbestätigte Buchungen", "format"=>"default");
$head[] = array("field" => 'datum_begin', "label" => "Beginn", "format" => "date", "group" => "Freigabe");
$head[] = array("field" => 'datum_ende', "label" => "Ende", "format" => "date", "group" => "Freigabe");
$head[] = array("field" => 'teilnehmer_min', "label" => "TN Minimum", "format" => "default", "editable" => "true", "group" => "Freigabe");
$head[] = array("field" => 'teilnehmer_max', "label" => "TN Maximum", "format" => "default", "editable" => "true", "group" => "Freigabe");
$head[] = array("field" => 'storno_datum', "label" => "Storno Datum", "format" => "date", "group" => "Freigabe");
$head[] = array("field" => 'standort_name', "label" => "Standort", "format" => "default", "group" => "Freigabe");
$head[] = array("field" => 'kursnr', "label" => "Kurs Nr.", "format" => "default", "editable" => "true", "group" => "Freigabe");
$head[] = array("field" => 'kosten_verpflegung', "label" => "Kosten Verpflegung", "format" => "price", "editable" => "true", "group" => "Kosten");
$head[] = array("field" => 'kosten_unterlagen', "label" => "Kosten Unterlagen", "format" => "price", "editable" => "true", "group" => "Kosten");
$head[] = array("field" => 'kursgebuehr', "label" => "Kursgebühr", "format" => "price", "editable" => "true", "group" => "Kosten");
$head[] = array("field" => 'ausgebucht', "label" => "Ausgebucht", "format" => "bool", "group" => "Termin");
$head[] = array("field" => 'uelaenge', "label" => "UE", "format" => "default", "group" => "Termin");
//$head[] = array ( "field"=> 'nettoep', "label"=>"Netto EP", "format"=>"price");
$head[] = array("field" => 'kosten_allg', "label" => "Kosten Allgemein", "format" => "price", "editable" => "true", "group" => "Kosten");
$head[] = array("field" => 'kosten_refer', "label" => "Kosten Referent", "format" => "price", "editable" => "true", "group" => "Kosten");
$head[] = array("field" => 'hinweis', "label" => "Hinweis", "format" => "default", 'editable' => 'true', "group" => "Info");
$head[] = array("field" => 'interne_information', "label" => "Interne Informationen", "format" => "default", 'editable' => 'true',"group" => "Info");
$_tableHeaders['Seminar'] = $head;


// SeminarArt
$head = array();
$head[] = array("field" => 'id', "label" => "ID", "format" => "default","group"=>"Kurs", "hide"=>false);
//$head[] = array("field" => 'id', "label" => "Kurs-Nr.", "format" => "default","group"=>"Kurs", "hide"=>false);
$head[] = array("field" => 'status', "label" => "Status", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtStatus")->findAll(Doctrine::HYDRATE_ARRAY),"group"=>"Kurs");

$head[] = array("field" => 'rubrik', "label" => "Rubrik 1", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");
$head[] = array("field" => 'rubrik2', "label" => "Rubrik 2", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");
$head[] = array("field" => 'rubrik3', "label" => "Rubrik 3", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");
$head[] = array("field" => 'rubrik4', "label" => "Rubrik 4", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");
$head[] = array("field" => 'rubrik5', "label" => "Rubrik 5", "format" => "combo", "editable" => "true", "values" => Doctrine::getTable("SeminarArtRubrik")->findAll(Doctrine::HYDRATE_ARRAY), "group"=>"Rubriken");

$head[] = array("field" => 'bezeichnung', "label" => "Bezeichnung", "format" => "default", 'editable' => 'true', "group"=>"Informationen");
$head[] = array("field" => 'kurzbeschreibung', "label" => "Kurzbeschreibung", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'langbeschreibung', "label" => "Langbeschreibung", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'dauer', "label" => "Dauer", "format" => "default", 'editable' => "true", "group"=>"Planung");
$head[] = array("field" => 'stundenzahl', "label" => "Stundenzahl", "format" => "default", 'editable' => "true", "group"=>"Planung");
//$head[] = array ( "field"=> 'nettoEp',"label"=>"Netto EP", "format"=>"price"); // netto ep wird nicht mehr benutzt!!!
$head[] = array("field" => 'nachweise', "label" => "Nachweise", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'voraussetzungen', "label" => "Voraussetzungen", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'zielgruppe', "label" => "Zielgruppe", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'organisatorisches', "label" => "Organisatorisches", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'geraetschaften', "label" => "Gerätschaften", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'material', "label" => "Material", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
$head[] = array("field" => 'seminar_hinweis', "label" => "Hinweis", "format" => "default", 'editable' => 'false', "group"=>"Informationen");
//$head[] = array ("field"=> 'art', "label"=>"Art", "format"=>"default");
$head[] = array("field" => 'kosten_verpflegung', "label" => "Kosten Verpflegung", "format" => "price", 'editable' => "true", "group"=>"Ausgaben & Einnahmen");
$head[] = array("field" => 'kosten_unterlagen', "label" => "Kosten Unterlagen", "format" => "price", 'editable' => "true", "group"=>"Ausgaben & Einnahmen");
$head[] = array("field" => 'kosten_allg', "label" => "Verwaltungskosten (%)", "format" => "default", 'editable' => "true", "group"=>"Ausgaben & Einnahmen");
$head[] = array("field" => 'kosten_pruefung', "label" => "Prüfungskosten (%)", "format" => "default", 'editable' => "true", "group"=>"Ausgaben & Einnahmen");

$head[] = array("field" => 'kosten_refer', "label" => "Kosten Referent", "format" => "price", 'editable' => "true", "group"=>"Ausgaben & Einnahmen");
//$head[] = array ("field"=> 'infolink', "label"=>"Info Link", "format"=>"default");
$head[] = array("field" => 'kursgebuehr', "label" => "Kursgebühr", "format" => "price", 'editable' => "true", "group"=>"Ausgaben & Einnahmen");
$head[] = array("field" => 'teilnehmer_min_tpl', "label" => "TN Minimum", "format" => "default", 'editable' => "true", "group"=>"Planung");
$head[] = array("field" => 'teilnehmer_max_tpl', "label" => "TN Maximum", "format" => "default", 'editable' => "true", "group"=>"Planung");

$_tableHeaders['SeminarArt'] = $head;

//Tabelle:Standort
$head = array();
$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);
$head[] = array ("field"=> 'plz', "label"=>"PLZ", "format"=>"default", "editable"=>"true");
$head[] = array ("field"=> 'name',"label"=>"Name", "format"=>"default", "editable"=>"true");
$head[] = array ("field"=> 'strasse', "label"=>"Straße", "format"=>"default", "editable"=>"true");
$head[] = array ("field"=> 'nr',"label"=>"Nr", "format"=>"default", "editable"=>"true");
$head[] = array ("field"=> 'tel', "label"=>"Telefon", "format"=>"default", "editable"=>"true");
$head[] = array ("field"=> 'fax', "label"=>"Fax", "format"=>"default", "editable"=>"true");
$head[] = array ("field"=> 'art', "label"=>"Art", "format"=>"default", "editable"=>"true");
$head[] = array ("field"=> 'sichtbar_planung', "label"=>"In der Pl. Komp. Anz.", "format"=>"bool");
$head[] = array ("field"=> 'planung_aktiv', "label"=>"Sofort in der Pl. Komp. Anz.", "format"=>"bool");
$head[] = array ("field"=> 'kuerzel', "label"=>"Kürzel", "format"=>"default", "editable"=>"true");

$_tableHeaders['Standort'] = $head;

//TableHeaders: Todo
$head = array();
$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);
$head[] = array ("field"=> 'betreff', "label"=>"Betreff", "format"=>"default");
$head[] = array ("field"=> 'notiz',"label"=>"Notiz", "format"=>"default");
//$head[] = array ("field"=> 'firma_id', "label"=>"Firma", "format"=>"default");
//$head[] = array ("field"=> 'person_id', "label"=>"Person", "format"=>"default");
//$head[] = array ("field"=> 'buchung_id', "label"=>"Buchung", "format"=>"default");
//$head[] = array ("field"=> 'termin_id', "label"=>"Termin", "format"=>"default");
//$head[] = array ("field"=> 'seminar_id', "label"=>"Seminar", "format"=>"default");
$head[] = array ("field"=> 'erstellt', "label"=>"Erstellt", "format"=>"date");
$head[] = array ("field"=> 'erledigt', "label"=>"Erledigt am", "format"=>"date");
//$head[] = array ("field"=> 'deleted_at', "label"=>"Gelöscht", "format"=>"date");
$head[] = array ("field"=> 'kategorie_id',"label"=>"Kategorie", "format"=>"combo", "editable"=>"true", "values"=> Doctrine::getTable("XTodoKategorie")->findAll(Doctrine::HYDRATE_ARRAY) );
$head[] = array ("field"=> 'rubrik_id',"label"=>"Rubrik", "format"=>"combo", "editable"=>"true", "values"=> Doctrine::getTable("XTodoRubrik")->findAll(Doctrine::HYDRATE_ARRAY) );
$head[] = array ("field"=> 'person_id',"label"=>"Zugewiesen", "format"=>"combo", "editable"=>"true", "values"=> Doctrine::getTable("ViewXUser")->findAll(Doctrine::HYDRATE_ARRAY) );

//$head[] = array ("field"=> 'prioritaet', "label"=>"Priorität", "format"=>"default");
//$head[] = array ("field"=> 'fortschritt', "label"=>"Fortschritt", "format"=>"default");
//$head[] = array ("field"=> 'faelligkeit', "label"=>"Fällig", "format"=>"date");
$head[] = array ("field"=> 'status_id',"label"=>"Status", "format"=>"combo", "editable"=>"true", "values"=>Doctrine::getTable("XTodoStatus")->findAll(Doctrine::HYDRATE_ARRAY));
//$head[] = array ("field"=> 'erstellt_von_id',"label"=>"Erstellt von", "format"=>"default");
//$head[] = array ("field"=> 'zugeordnet_id', "label"=>"Zugeordnet", "format"=>"default");
$_tableHeaders['Todo'] = $head;

//Tabelle: ViewWerbeEmpfaenger

$head = array();
$head[] = array("field" => 'id', "label" => "ID", "format" => "default", "hide" => true);
$head[] = array("field" => 'person_id', "label" => "PersonID", "format" => "default", "hide" => true);
$head[] = array("field" => 'kontakt_id', "label" => "KontaktID", "format" => "default", "hide" => true);

$head[] = array("field" => 'firma', "label" => "Firma", "format" => "default");
$head[] = array("field" => 'vorname', "label" => "Vorname", "format" => "default");
$head[] = array("field" => 'name', "label" => "Name", "Name" => "default");
$head[] = array("field" => 'email', "label" => "E-Mail", "format" => "default");
$head[] = array("field" => 'strasse', "label" => "Straße", "format" => "default");
$head[] = array("field" => 'tel', "label" => "Telefon", "format" => "default");

//$head[] = array("field" => 'nr', "label" => "Nummer", "format" => "default");

$head[] = array("field" => 'ort', "label" => "Ort", "format" => "default");
$head[] = array("field" => 'plz', "label" => "PLZ", "format" => "default");
$head[] = array("field" => 'newsletter', "label" => "Newsletter", "format" => "default");
$head[] = array("field" => 'kategorie', "label" => "Kategorie", "format" => "default");
$head[] = array("field" => 'branche', "label" => "Branche", "format" => "default");
$head[] = array("field" => 'taetigkeitsbereich', "label" => "Tätigkeitsbereich", "format" => "default");
$head[] = array("field" => 'quelle', "label" => "Datenbank", "format" => "default");

$_tableHeaders['ViewWerbeEmpfaenger'] =$head;

// Tabelle: XUser
$head = array();
$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);


$head[] = array ("field"=> 'name', "label"=>"Benutzername", "format"=>"default", "editable"=>"true" , "group" => "Zugangsdaten");
$head[] = array ("field"=> 'password', "label"=>"Passwort", "format"=>"default", "editable"=>"true", "group" => "Zugangsdaten");
$head[] = array ("field"=> 'email', "label"=>"E-Mail", "format"=>"default", "editable"=>"true", "group" => "Benutzerdaten");
$head[] = array ("field"=> 'vorname', "label"=>"Vorname", "format"=>"default", "editable"=>"true" , "group" => "Benutzerdaten");
$head[] = array ("field"=> 'nachname', "label"=>"Nachname", "format"=>"default", "editable"=>"true" , "group" => "Benutzerdaten");
$head[] = array ("field"=> 'class', "label"=>"Gruppe", "format"=>"default", "group" => "Gruppe");

$_tableHeaders['XUser'] = $head;

$head = array();
$head[] = array ("field"=> 'id', "label"=>"ID", "format"=>"default", "hide"=>true);
$head[] = array ("field"=> 'firma', "label"=>"Firma", "format"=>"default", "editable"=>"true", "group"=> "Referent");
$head[] = array ("field"=> 'status', "label"=>"Status", "format"=>"combo", "editable"=>"true", "group"=> "Referent", "values"=> Doctrine::getTable("XStatus")->findAll(Doctrine::HYDRATE_ARRAY)  );
$head[] = array ("field"=> 'anrede_id', "label"=>"Anrede", "format"=>"combo", "editable"=>"true", "values"=> Doctrine::getTable("XAnrede")->findAll(Doctrine::HYDRATE_ARRAY) , "group"=> "Referent" );
$head[] = array ("field"=> 'grad', "label"=>"Grad", "format"=>"default", "editable"=>"true"  ,"group"=> "Referent");
$head[] = array ("field"=> 'kuerzel',"label"=>"Kürzel", "format"=>"default", "editable"=>"true"  ,"group"=> "Referent");
$head[] = array ("field"=> 'vorname',"label"=>"Vorname", "format"=>"default", "editable"=>"true"  ,"group"=> "Referent");
$head[] = array ("field"=> 'name',"label"=>"Name", "format"=>"default", "editable"=>"true" ,"group"=> "Referent");
$head[] = array ("field"=> 'email',"label"=>"E-Mail", "format"=>"email", "editable"=>"false" ,"group"=> "Kontakt");
$head[] = array ("field"=> 'beschreibung', "label"=>"Beschreibung", "format"=>"default", "editable"=>"true" ,"group"=> "Homepage");
$head[] = array ("field"=> 'veroeffentlicht', "label"=>"Veröffentlicht", "format"=>"bool", "editable"=>"true","group"=> "Homepage");
$head[] = array ("field"=> 'image', "label"=>"Bild", "format"=>"image" ,"group"=> "Homepage");

$_tableHeaders['Referent'] = $head;
