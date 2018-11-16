UPDATE IGNORE `sag-akademie_de_stable`.seminar_art SET geaendert='1970-01-01 00:00:00' WHERE geaendert='0000-00-00 00:00:00';

ALTER TABLE `sag-akademie_de_stable`.`seminar_art` 
CHANGE COLUMN `geaendert` `geaendert` DATETIME NULL DEFAULT NULL ;

ALTER TABLE `sag-akademie_de_stable`.`seminar_art` 
CHANGE COLUMN `id` `id` CHAR(40) NOT NULL ;


UPDATE IGNORE seminar SET storno_datum='1970-01-01' WHERE storno_datum="0000-00-00"
UPDATE IGNORE seminar SET freigabe_datum='1970-01-01' WHERE freigabe_datum="0000-00-00"
UPDATE IGNORE seminar SET geaendert='1970-01-01 00:00:00' WHERE geaendert='0000-00-00 00:00:00';
UPDATE IGNORE seminar SET angebot_datum='1970-01-01' WHERE angebot_datum='0000-00-00';
UPDATE IGNORE seminar SET datum_ende='1970-01-01' WHERE datum_ende='0000-00-00';
UPDATE IGNORE seminar SET datum_begin='1970-01-01' WHERE datum_begin='0000-00-00';

UPDATE `sag-akademie_de_stable`.`seminar` SET `datum_ende`='1970-01-01' WHERE `id`='671';
UPDATE `sag-akademie_de_stable`.`seminar` SET `datum_ende`='1970-01-01' WHERE `id`='420';
UPDATE `sag-akademie_de_stable`.`seminar` SET `datum_ende`='1970-01-01' WHERE `id`='421';
UPDATE `sag-akademie_de_stable`.`seminar` SET `datum_ende`='1970-01-01' WHERE `id`='675';
UPDATE `sag-akademie_de_stable`.`seminar` SET `datum_ende`='1970-01-01' WHERE `id`='422';


ALTER TABLE `sag-akademie_de_stable`.`seminar` 
CHANGE COLUMN `seminar_art_id` `seminar_art_id` CHAR(40) NOT NULL ,
CHANGE COLUMN `datum_begin` `datum_begin` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `datum_ende` `datum_ende` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `angebot_datum` `angebot_datum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `storno_datum` `storno_datum` DATE NULL DEFAULT NULL,
CHANGE COLUMN `geaendert` `geaendert` DATETIME NULL DEFAULT NULL,
CHANGE COLUMN `freigabe_datum` `freigabe_datum` DATE NULL DEFAULT NULL ;

UPDATE seminar SET datum_begin = NULL WHERE datum_begin='1970-01-01';
UPDATE seminar SET datum_ende = NULL WHERE datum_ende='1970-01-01';
UPDATE seminar SET angebot_datum = NULL WHERE angebot_datum='1970-01-01';
UPDATE seminar SET geaendert = NULL WHERE geaendert='1970-01-01 00:00:00';
UPDATE seminar SET freigabe_datum = NULL WHERE freigabe_datum='1970-01-01';
UPDATE seminar SET storno_datum = NULL WHERE storno_datum='1970-01-01';

ALTER TABLE `sag-akademie_de_stable`.`seminar` 
ENGINE = InnoDB ;

ALTER TABLE `sag-akademie_de_stable`.`seminar_art` 
ENGINE = InnoDB ;

