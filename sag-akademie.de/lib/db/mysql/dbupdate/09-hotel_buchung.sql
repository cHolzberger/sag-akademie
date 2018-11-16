UPDATE IGNORE hotel_buchung SET storno_datum = "1970-01-01" WHERE storno_datum = "0000-00-00";
UPDATE IGNORE hotel_buchung SET buchungs_datum = "1970-01-01" WHERE buchungs_datum = "0000-00-00";
UPDATE IGNORE hotel_buchung SET angelegt_datum = "1970-01-01 00:00:00" WHERE angelegt_datum = "0000-00-00 00:00:00";
UPDATE IGNORE hotel_buchung SET geaendert = "1970-01-01 00:00:00" WHERE geaendert = "0000-00-00 00:00:00";
UPDATE IGNORE hotel_buchung SET deleted_at = "1970-01-01 00:00:00" WHERE deleted_at = "0000-00-00 00:00:00";

ALTER TABLE `sag-akademie_de_stable`.`hotel_buchung` 
CHANGE COLUMN `storno_datum` `storno_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `buchungs_datum` `buchungs_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `deleted_at` `deleted_at` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `geaendert` `geaendert` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `angelegt_datum` `angelegt_datum` DATETIME NULL DEFAULT NULL ;

UPDATE IGNORE hotel_buchung SET storno_datum = NULL WHERE storno_datum = "1970-01-01";
UPDATE IGNORE hotel_buchung SET buchungs_datum = NULL WHERE buchungs_datum = "1970-01-01";

UPDATE IGNORE hotel_buchung SET deleted_at = NULL WHERE deleted_at = "1970-01-01 00:00:00";
UPDATE IGNORE hotel_buchung SET geaendert = NULL WHERE geaendert = "1970-01-01 00:00:00";
UPDATE IGNORE hotel_buchung SET angelegt_datum = NULL WHERE angelegt_datum = "1970-01-01 00:00:00";