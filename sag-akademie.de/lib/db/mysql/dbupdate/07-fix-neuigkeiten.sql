UPDATE IGNORE neuigkeit SET datum = "1970-01-01 00:00:00" WHERE datum = "0000-00-00 00:00:00";
UPDATE IGNORE neuigkeit SET geaendert = "1970-01-01 00:00:00" WHERE geaendert = "0000-00-00 00:00:00";
UPDATE IGNORE neuigkeit SET deleted_at = "1970-01-01 00:00:00" WHERE deleted_at = "0000-00-00 00:00:00";

ALTER TABLE `sag-akademie_de_stable`.`neuigkeit` 
CHANGE COLUMN `datum` `datum` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `geaendert` `geaendert` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `deleted_at` `deleted_at` DATETIME NULL DEFAULT NULL ;


UPDATE IGNORE neuigkeit SET datum = NULL WHERE datum = "1970-01-01 00:00:00";
UPDATE IGNORE neuigkeit SET geaendert = NULL WHERE geaendert = "1970-01-01 00:00:00";
UPDATE IGNORE neuigkeit SET deleted_at = NULL WHERE deleted_at = "1970-01-01 00:00:00";