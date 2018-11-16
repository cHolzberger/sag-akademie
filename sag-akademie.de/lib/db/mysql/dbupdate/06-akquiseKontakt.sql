UPDATE IGNORE akquise_kontakt SET deleted_at='1970-01-01 00:00:00' WHERE deleted_at='0000-00-00 00:00:00';
UPDATE IGNORE akquise_kontakt set geaendert='1970-01-01 00:00:00' WHERE geaendert="0000-00-00 00:00:00";
UPDATE IGNORE akquise_kontakt set angelegt_datum='1970-01-01 00:00:00' WHERE angelegt_datum="0000-00-00 00:00:00";
UPDATE IGNORE akquise_kontakt set abmelde_datum='1970-01-01' WHERE abmelde_datum="0000-00-00";
UPDATE IGNORE akquise_kontakt set anmelde_datum='1970-01-01' WHERE anmelde_datum="0000-00-00";
UPDATE IGNORE akquise_kontakt set kontakt_quelle_stand='1970-01-01' WHERE kontakt_quelle_stand="0000-00-00";
UPDATE IGNORE akquise_kontakt set qualifiziert_datum='1970-01-01' WHERE qualifiziert_datum="0000-00-00";

ALTER TABLE `sag-akademie_de_stable`.`akquise_kontakt` 
CHANGE COLUMN `abmelde_datum` `abmelde_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `anmelde_datum` `anmelde_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `kontakt_quelle_stand` `kontakt_quelle_stand` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `geaendert` `geaendert` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `qualifiziert_datum` `qualifiziert_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `deleted_at` `deleted_at` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `angelegt_datum` `angelegt_datum` DATETIME NULL DEFAULT NULL ;

UPDATE IGNORE akquise_kontakt set abmelde_datum=NULL  WHERE abmelde_datum='1970-01-01';
UPDATE IGNORE akquise_kontakt set anmelde_datum=NULL  WHERE anmelde_datum='1970-01-01';
UPDATE IGNORE akquise_kontakt set geaendert=NULL  WHERE geaendert='1970-01-01 00:00:00';
UPDATE IGNORE akquise_kontakt set angelegt_datum=NULL  WHERE angelegt_datum='1970-01-01 00:00:00';
UPDATE IGNORE akquise_kontakt SET deleted_at=NULL WHERE deleted_at='1970-01-01 00:00:00';
UPDATE IGNORE akquise_kontakt set kontakt_quelle_stand=NULL WHERE kontakt_quelle_stand='1970-01-01';
UPDATE IGNORE akquise_kontakt set qualifiziert_datum=NULL WHERE qualifiziert_datum='1970-01-01';

