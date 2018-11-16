-- Mysql sort order and optimisation

ALTER TABLE `buchung`  ORDER BY `datum`;
ALTER TABLE `akquise_kontakt`  ORDER BY `firma`;
ALTER TABLE `hotel`  ORDER BY `name`;
ALTER TABLE `hotel_preis`  ORDER BY `datum_start`;
ALTER TABLE `kontakt`  ORDER BY `firma`;
ALTER TABLE `kontakt_kategorie`  ORDER BY `name`;
ALTER TABLE `kontakt_quelle`  ORDER BY `name`;
ALTER TABLE `person`  ORDER BY `name`;
ALTER TABLE `referent`  ORDER BY `name`;
ALTER TABLE `seminar`  ORDER BY `datum_begin`;
ALTER TABLE `seminar_art`  ORDER BY `id`;
ALTER TABLE `standort`  ORDER BY `name`;

-- optimizsation
OPTIMIZE TABLE `person`;
OPTIMIZE TABLE `seminar`;
OPTIMIZE TABLE `seminar_art`;
OPTIMIZE TABLE `standort`;
OPTIMIZE TABLE `akquise_kontakt`;
OPTIMIZE TABLE `kontakt`;

UPDATE person SET newsletter = 0 WHERE newsletter_abmeldedatum > newsletter_anmeldedatum;
UPDATE kontakt SET newsletter = 0 WHERE newsletter_abmeldedatum > newsletter_anmeldedatum;
UPDATE akquise_kontakt SET newsletter = 0 WHERE abmelde_datum > anmelde_datum;