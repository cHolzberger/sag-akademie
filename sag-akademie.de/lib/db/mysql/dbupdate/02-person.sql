UPDATE IGNORE person set geburtstag='1970-01-01' WHERE geburtstag="0000-00-00";
UPDATE IGNORE person set newsletter_abmeldedatum='1970-01-01' WHERE newsletter_abmeldedatum="0000-00-00";
UPDATE IGNORE person set newsletter_anmeldedatum='1970-01-01' WHERE newsletter_anmeldedatum="0000-00-00";
UPDATE IGNORE person set geaendert='1970-01-01 00:00:00' WHERE geaendert="0000-00-00 00:00:00";
UPDATE IGNORE person set angelegt_datum='1970-01-01 00:00:00' WHERE angelegt_datum="0000-00-00 00:00:00";

ALTER TABLE `sag-akademie_de_stable`.`person` 
CHANGE COLUMN `geburtstag` `geburtstag` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `newsletter_anmeldedatum` `newsletter_anmeldedatum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `newsletter_abmeldedatum` `newsletter_abmeldedatum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `geaendert` `geaendert` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `angelegt_datum` `angelegt_datum` DATETIME NULL DEFAULT NULL ;

UPDATE IGNORE person set geburtstag=NULL WHERE geburtstag='1970-01-01';
UPDATE IGNORE person set newsletter_abmeldedatum=NULL  WHERE newsletter_abmeldedatum='1970-01-01';
UPDATE IGNORE person set newsletter_anmeldedatum=NULL  WHERE newsletter_anmeldedatum='1970-01-01';
UPDATE IGNORE person set geaendert=NULL  WHERE geaendert='1970-01-01 00:00:00';
UPDATE IGNORE person set angelegt_datum=NULL  WHERE angelegt_datum='1970-01-01 00:00:00';

ALTER TABLE `sag-akademie_de_stable`.`person` 
CHANGE COLUMN `strasse` `strasse` TINYTEXT NULL ;

ALTER TABLE `sag-akademie_de_stable`.`person` 
CHANGE COLUMN `nr` `nr` TINYTEXT NULL ,
CHANGE COLUMN `plz` `plz` VARCHAR(10) NULL ,
CHANGE COLUMN `grad` `grad` TINYTEXT NULL ,
CHANGE COLUMN `geschlecht` `geschlecht` SMALLINT(6) NULL ,
CHANGE COLUMN `tel` `tel` TINYTEXT NULL ,
CHANGE COLUMN `email` `email` TINYTEXT NULL ,
CHANGE COLUMN `fax` `fax` TINYTEXT NULL ,
CHANGE COLUMN `mobil` `mobil` TINYTEXT NULL ,
CHANGE COLUMN `funktion` `funktion` TINYTEXT NULL ,
CHANGE COLUMN `abteilung` `abteilung` TINYTEXT NULL ,
CHANGE COLUMN `ansprechpartner` `ansprechpartner` TINYINT(1) NULL ,
CHANGE COLUMN `geschaeftsfuehrer` `geschaeftsfuehrer` TINYINT(1) NULL ,
CHANGE COLUMN `newsletter` `newsletter` TINYINT(1) NULL ,
CHANGE COLUMN `ort` `ort` TINYTEXT NULL ,
CHANGE COLUMN `bundesland` `bundesland` TEXT NULL ,
CHANGE COLUMN `land` `land` TEXT NULL ,
CHANGE COLUMN `login_name` `login_name` TINYTEXT NULL ,
CHANGE COLUMN `login_password` `login_password` TINYTEXT NULL ,
CHANGE COLUMN `land_id` `land_id` INT(11) NULL ,
CHANGE COLUMN `bundesland_id` `bundesland_id` INT(11) NULL ,
CHANGE COLUMN `aktiv` `aktiv` INT(11) NULL ,
CHANGE COLUMN `gesperrt` `gesperrt` INT(1) NULL ,
CHANGE COLUMN `gesperrt_info` `gesperrt_info` TINYTEXT NULL ,
CHANGE COLUMN `geaendert_von` `geaendert_von` INT(11) NULL DEFAULT '-1' ,
CHANGE COLUMN `wiedervorlage` `wiedervorlage` INT(1) NULL ,
CHANGE COLUMN `ausgeschieden` `ausgeschieden` INT(1) NULL ,
CHANGE COLUMN `zusatz` `zusatz` VARCHAR(255) NULL ,
CHANGE COLUMN `anrede` `anrede` TINYTEXT NULL ,
CHANGE COLUMN `titel` `titel` TINYTEXT NULL ,
CHANGE COLUMN `version` `version` INT(11) NULL ,
CHANGE COLUMN `nur_inhouse` `nur_inhouse` INT(11) NULL ;

ALTER TABLE `sag-akademie_de_stable`.`person` 
ENGINE = InnoDB ;


# person_version version
UPDATE IGNORE person_version set geburtstag='1970-01-01' WHERE geburtstag="0000-00-00";
UPDATE IGNORE person_version set newsletter_abmeldedatum='1970-01-01' WHERE newsletter_abmeldedatum="0000-00-00";
UPDATE IGNORE person_version set newsletter_anmeldedatum='1970-01-01' WHERE newsletter_anmeldedatum="0000-00-00";
UPDATE IGNORE person_version set geaendert='1970-01-01 00:00:00' WHERE geaendert="0000-00-00 00:00:00";
UPDATE IGNORE person_version set angelegt_datum='1970-01-01 00:00:00' WHERE angelegt_datum="0000-00-00 00:00:00";

ALTER TABLE `sag-akademie_de_stable`.`person_version` 
CHANGE COLUMN `geburtstag` `geburtstag` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `newsletter_anmeldedatum` `newsletter_anmeldedatum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `newsletter_abmeldedatum` `newsletter_abmeldedatum` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `geaendert` `geaendert` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `angelegt_datum` `angelegt_datum` DATETIME NULL DEFAULT NULL ;

UPDATE IGNORE person_version set geburtstag=NULL WHERE geburtstag='1970-01-01';
UPDATE IGNORE person_version set newsletter_abmeldedatum=NULL  WHERE newsletter_abmeldedatum='1970-01-01';
UPDATE IGNORE person_version set newsletter_anmeldedatum=NULL  WHERE newsletter_anmeldedatum='1970-01-01';
UPDATE IGNORE person_version set geaendert=NULL  WHERE geaendert='1970-01-01 00:00:00';
UPDATE IGNORE person_version set angelegt_datum=NULL  WHERE angelegt_datum='1970-01-01 00:00:00';

ALTER TABLE `sag-akademie_de_stable`.`person_version` 
CHANGE COLUMN `strasse` `strasse` TINYTEXT NULL ;

ALTER TABLE `sag-akademie_de_stable`.`person_version` 
CHANGE COLUMN `nr` `nr` TINYTEXT NULL ,
CHANGE COLUMN `plz` `plz` VARCHAR(10) NULL ,
CHANGE COLUMN `grad` `grad` TINYTEXT NULL ,
CHANGE COLUMN `geschlecht` `geschlecht` SMALLINT(6) NULL ,
CHANGE COLUMN `tel` `tel` TINYTEXT NULL ,
CHANGE COLUMN `email` `email` TINYTEXT NULL ,
CHANGE COLUMN `fax` `fax` TINYTEXT NULL ,
CHANGE COLUMN `mobil` `mobil` TINYTEXT NULL ,
CHANGE COLUMN `funktion` `funktion` TINYTEXT NULL ,
CHANGE COLUMN `abteilung` `abteilung` TINYTEXT NULL ,
CHANGE COLUMN `ansprechpartner` `ansprechpartner` TINYINT(1) NULL ,
CHANGE COLUMN `geschaeftsfuehrer` `geschaeftsfuehrer` TINYINT(1) NULL ,
CHANGE COLUMN `newsletter` `newsletter` TINYINT(1) NULL ,
CHANGE COLUMN `ort` `ort` TINYTEXT NULL ,
CHANGE COLUMN `bundesland` `bundesland` TEXT NULL ,
CHANGE COLUMN `land` `land` TEXT NULL ,
CHANGE COLUMN `login_name` `login_name` TINYTEXT NULL ,
CHANGE COLUMN `login_password` `login_password` TINYTEXT NULL ,
CHANGE COLUMN `land_id` `land_id` INT(11) NULL ,
CHANGE COLUMN `bundesland_id` `bundesland_id` INT(11) NULL ,
CHANGE COLUMN `aktiv` `aktiv` INT(11) NULL ,
CHANGE COLUMN `gesperrt` `gesperrt` INT(1) NULL ,
CHANGE COLUMN `gesperrt_info` `gesperrt_info` TINYTEXT NULL ,
CHANGE COLUMN `geaendert_von` `geaendert_von` INT(11) NULL DEFAULT '-1' ,
CHANGE COLUMN `wiedervorlage` `wiedervorlage` INT(1) NULL ,
CHANGE COLUMN `ausgeschieden` `ausgeschieden` INT(1) NULL ,
CHANGE COLUMN `zusatz` `zusatz` VARCHAR(255) NULL ,
CHANGE COLUMN `anrede` `anrede` TINYTEXT NULL ,
CHANGE COLUMN `titel` `titel` TINYTEXT NULL ,
CHANGE COLUMN `nur_inhouse` `nur_inhouse` INT(11) NULL ;

ALTER TABLE `sag-akademie_de_stable`.`person_version` 
ENGINE = InnoDB ;
