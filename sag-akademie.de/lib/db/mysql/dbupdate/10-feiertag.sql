ALTER TABLE `sag-akademie_de_stable`.`feiertag` 
CHANGE COLUMN `datum` `datum` DATE NULL ;

ALTER TABLE `sag-akademie_de_stable`.`feiertag` 
ENGINE = InnoDB ;
