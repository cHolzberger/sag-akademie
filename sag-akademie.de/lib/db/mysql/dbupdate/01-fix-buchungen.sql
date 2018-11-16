UPDATE IGNORE buchung SET storno_datum='1970-01-01' WHERE storno_datum="0000-00-00";
UPDATE IGNORE buchung SET rechnunggestellt='1970-01-01' WHERE rechnunggestellt="0000-00-00";
UPDATE IGNORE buchung SET bestaetigungs_datum='1970-01-01' WHERE bestaetigungs_datum="0000-00-00";
UPDATE IGNORE buchung SET umbuchungs_datum='1970-01-01' WHERE umbuchungs_datum="0000-00-00";
UPDATE IGNORE buchung SET zahlungseingang_datum='1970-01-01' WHERE zahlungseingang_datum="0000-00-00";
UPDATE IGNORE buchung SET zahlungseingang_datum='1970-01-01' WHERE zahlungseingang_datum="2001-01-00";

UPDATE IGNORE buchung SET umgebucht_auf='1970-01-01' WHERE umgebucht_auf="0000-00-00";
UPDATE IGNORE buchung SET datum_umbuchung='1970-01-01' WHERE datum_umbuchung="0000-00-00";
UPDATE IGNORE buchung SET datum='1970-01-01 00:00:00' WHERE datum='0000-00-00 00:00:00';

UPDATE IGNORE buchung SET deleted_at='1970-01-01 00:00:00' WHERE deleted_at='0000-00-00 00:00:00';
UPDATE IGNORE buchung SET angelegt_datum='1970-01-01 00:00:00' WHERE angelegt_datum='0000-00-00 00:00:00';
UPDATE IGNORE buchung SET geaendert='1970-01-01 00:00:00' WHERE geaendert='0000-00-00 00:00:00';


ALTER TABLE `sag-akademie_de_stable`.`buchung` 
CHANGE COLUMN `storno_datum` `storno_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `rechnunggestellt` `rechnunggestellt` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `bestaetigungs_datum` `bestaetigungs_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `umbuchungs_datum` `umbuchungs_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `zahlungseingang_datum` `zahlungseingang_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `datum_umbuchung` `datum_umbuchung` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `umgebucht_auf` `umgebucht_auf` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `deleted_at` `deleted_at` DATETIME NULL DEFAULT NULL,
CHANGE COLUMN `angelegt_datum` `angelegt_datum` DATETIME NULL DEFAULT NULL,
CHANGE COLUMN `geaendert` `geaendert` DATETIME NULL DEFAULT NULL,
CHANGE COLUMN `datum` `datum` DATETIME NULL DEFAULT NULL

;

UPDATE IGNORE buchung SET storno_datum=NULL WHERE storno_datum="1970-01-01";
UPDATE IGNORE buchung SET rechnunggestellt=NULL WHERE rechnunggestellt="1970-01-01";
UPDATE IGNORE buchung SET bestaetigungs_datum=NULL WHERE bestaetigungs_datum="1970-01-01";
UPDATE IGNORE buchung SET umbuchungs_datum=NULL WHERE umbuchungs_datum="1970-01-01";
UPDATE IGNORE buchung SET zahlungseingang_datum=NULL WHERE zahlungseingang_datum="1970-01-01";
UPDATE IGNORE buchung SET zahlungseingang_datum=NULL WHERE zahlungseingang_datum="1970-01-01";

UPDATE IGNORE buchung SET umgebucht_auf=NULL WHERE umgebucht_auf="1970-01-01";
UPDATE IGNORE buchung SET datum_umbuchung=NULL WHERE datum_umbuchung="1970-01-01";
UPDATE IGNORE buchung SET datum=NULL WHERE datum='1970-01-01 00:00:00';

UPDATE IGNORE buchung SET deleted_at=NULL WHERE deleted_at='1970-01-01 00:00:00';
UPDATE IGNORE buchung SET angelegt_datum=NULL WHERE angelegt_datum='1970-01-01 00:00:00';
UPDATE IGNORE buchung SET geaendert=NULL WHERE geaendert='1970-01-01 00:00:00';

# buchung_version 
UPDATE IGNORE buchung_version SET storno_datum='1970-01-01' WHERE storno_datum="0000-00-00";
UPDATE IGNORE buchung_version SET rechnunggestellt='1970-01-01' WHERE rechnunggestellt="0000-00-00";
UPDATE IGNORE buchung_version SET bestaetigungs_datum='1970-01-01' WHERE bestaetigungs_datum="0000-00-00";
UPDATE IGNORE buchung_version SET umbuchungs_datum='1970-01-01' WHERE umbuchungs_datum="0000-00-00";
UPDATE IGNORE buchung_version SET zahlungseingang_datum='1970-01-01' WHERE zahlungseingang_datum="0000-00-00";
UPDATE IGNORE buchung_version SET zahlungseingang_datum='1970-01-01' WHERE zahlungseingang_datum="2001-01-00";

UPDATE IGNORE buchung_version SET umgebucht_auf='1970-01-01' WHERE umgebucht_auf="0000-00-00";
UPDATE IGNORE buchung_version SET datum_umbuchung='1970-01-01' WHERE datum_umbuchung="0000-00-00";
UPDATE IGNORE buchung_version SET datum='1970-01-01 00:00:00' WHERE datum='0000-00-00 00:00:00';

UPDATE IGNORE buchung_version SET deleted_at='1970-01-01 00:00:00' WHERE deleted_at='0000-00-00 00:00:00';
UPDATE IGNORE buchung_version SET angelegt_datum='1970-01-01 00:00:00' WHERE angelegt_datum='0000-00-00 00:00:00';
UPDATE IGNORE buchung_version SET geaendert='1970-01-01 00:00:00' WHERE geaendert='0000-00-00 00:00:00';


ALTER TABLE `sag-akademie_de_stable`.`buchung_version` 
CHANGE COLUMN `storno_datum` `storno_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `rechnunggestellt` `rechnunggestellt` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `bestaetigungs_datum` `bestaetigungs_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `umbuchungs_datum` `umbuchungs_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `zahlungseingang_datum` `zahlungseingang_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `datum_umbuchung` `datum_umbuchung` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `umgebucht_auf` `umgebucht_auf` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `deleted_at` `deleted_at` DATETIME NULL DEFAULT NULL,
CHANGE COLUMN `angelegt_datum` `angelegt_datum` DATETIME NULL DEFAULT NULL,
CHANGE COLUMN `geaendert` `geaendert` DATETIME NULL DEFAULT NULL,
CHANGE COLUMN `datum` `datum` DATETIME NULL DEFAULT NULL

;

UPDATE IGNORE buchung_version SET storno_datum=NULL WHERE storno_datum="1970-01-01";
UPDATE IGNORE buchung_version SET rechnunggestellt=NULL WHERE rechnunggestellt="1970-01-01";
UPDATE IGNORE buchung_version SET bestaetigungs_datum=NULL WHERE bestaetigungs_datum="1970-01-01";
UPDATE IGNORE buchung_version SET umbuchungs_datum=NULL WHERE umbuchungs_datum="1970-01-01";
UPDATE IGNORE buchung_version SET zahlungseingang_datum=NULL WHERE zahlungseingang_datum="1970-01-01";
UPDATE IGNORE buchung_version SET zahlungseingang_datum=NULL WHERE zahlungseingang_datum="1970-01-01";

UPDATE IGNORE buchung_version SET umgebucht_auf=NULL WHERE umgebucht_auf="1970-01-01";
UPDATE IGNORE buchung_version SET datum_umbuchung=NULL WHERE datum_umbuchung="1970-01-01";
UPDATE IGNORE buchung_version SET datum=NULL WHERE datum='1970-01-01 00:00:00';

UPDATE IGNORE buchung_version SET deleted_at=NULL WHERE deleted_at='1970-01-01 00:00:00';
UPDATE IGNORE buchung_version SET angelegt_datum=NULL WHERE angelegt_datum='1970-01-01 00:00:00';
UPDATE IGNORE buchung_version SET geaendert=NULL WHERE geaendert='1970-01-01 00:00:00';

-- seminare fuer die planung
CREATE OR REPLACE VIEW view_seminar_planung AS SELECT 
seminar.*,
seminar_art.aktualisierung_gesperrt as seminar_art_gesperrt,
seminar_art_aktualisierung.gesperrt as  standort_gesperrt,
seminar_art.sichtbar_planung as sichtbar_planung,
seminar_art.farbe as farbe,
seminar_art.textfarbe as textfarbe,
seminar_freigabestatus.farbe as freigabe_farbe,
seminar_freigabestatus.flag as freigabe_flag,
seminar_freigabestatus.name as freigabe_name,
seminar_freigabestatus.veroeffentlichen as freigabe_veroeffentlichen,
standort.name as standort_name,
YEAR(seminar.datum_begin) as begin_jahr,
MONTH(seminar.datum_begin) as begin_monat,
DAY(seminar.datum_begin) as begin_tag,
YEAR(seminar.datum_ende) as ende_jahr,
MONTH(seminar.datum_ende) as ende_monat,
DAY(seminar.datum_ende) as ende_tag,
DATEDIFF(seminar.datum_ende, seminar.datum_begin) +1 as dauer,
seminar.kursgebuehr as preis,
IF (DATEDIFF(CURDATE(), seminar.datum_begin) < 0, 0, 1) as abgeschlossen,
IF( buchung.id IS NULL , 0, COUNT(seminar.id) )  as teilnehmer,
0 as anzahlNichtBestaetigt,(
CASE WHEN seminar.storno_datum IS NOT NULL THEN 2
	ELSE 1
	END) as status,
seminar_art_rubrik.name as rubrik_name,
ihs.*,
inhouse_kunde.firma as inhouse_firma

FROM seminar
JOIN seminar_art ON ( seminar_art.id = seminar.seminar_art_id )
JOIN standort ON ( standort.id = seminar.standort_id )
JOIN seminar_freigabestatus ON ( seminar.freigabe_status = seminar_freigabestatus.id )
JOIN seminar_art_rubrik ON (  seminar_art_rubrik.id = seminar_art.rubrik )
LEFT JOIN seminar_art_aktualisierung ON ( seminar_art_aktualisierung.seminar_art_id = seminar.seminar_art_id AND seminar_art_aktualisierung.standort_id = seminar.standort_id)
LEFT JOIN kontakt as inhouse_kunde ON ( seminar.inhouse_kunde = inhouse_kunde.id )
LEFT JOIN inhouse_ort as ihs ON ( seminar.id = ihs.seminar_id )
LEFT JOIN view_buchung as buchung ON ( seminar.id = buchung.seminar_id AND ( buchung.status = 1 OR buchung.status = 5))
GROUP BY seminar.id;
