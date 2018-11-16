ALTER TABLE `sag-akademie_de_stable`.`buchung` 
ADD COLUMN `seminar_unterlagen` INT(1) NULL DEFAULT 0 AFTER `angelegt_datum`;
