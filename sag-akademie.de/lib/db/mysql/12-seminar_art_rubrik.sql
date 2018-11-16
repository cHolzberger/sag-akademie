ALTER TABLE `sag-akademie_de_stable`.`seminar_art_rubrik` 
ENGINE = InnoDB ;

ALTER TABLE `sag-akademie_de_stable`.`seminar_art_rubrik` 
ADD INDEX `idx_name` (`name`(2) ASC) VISIBLE;
;
