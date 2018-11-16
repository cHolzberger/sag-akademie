<?php
$translate = array(
	"kontakte" => "Kontakte",
	"termine" => "Termine",
	"Admin" => "Startseite",
	"Admin_show_sub" => false,
	"seminare" => "Seminare",
	"standorte" => "Standorte",
	"hotels" => "Hotels",
	"db" => "Wartung",
	"buchungen" => "Buchungen",
	"rechnungen" => "Rechnungen",
	"personen" => "Personen",
	"user" => "Benutzer"
);

function breadcrumbTranslate ($name) {
	global $translate; 
	if (!empty($name) && array_key_exists($name, $translate)) {
		return $translate[$name];
	}
	return $name;
}
?>