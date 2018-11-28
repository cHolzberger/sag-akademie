ALTER TABLE `sag-akademie_de_stable`.`seminar_art` ADD COLUMN `qualifikationsart` VARCHAR(45) NULL AFTER `info`;
ALTER TABLE `sag-akademie_de_stable`.`seminar_art` ADD COLUMN `rezertifizierungszeit` INT(2) NULL AFTER `qualifikationsart`;

ALTER TABLE `sag-akademie_de_stable`.`seminar` ADD COLUMN `qualifikationsart` VARCHAR(45) NULL AFTER `aktualisierung_gesperrt`;
ALTER TABLE `sag-akademie_de_stable`.`seminar` ADD COLUMN `rezertifizierungszeit` INT(2) NULL AFTER `qualifikationsart`;

