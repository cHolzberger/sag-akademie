ALTER TABLE `sag-akademie_de_stable`.`x_user` 
ENGINE = InnoDB ,
ADD INDEX `idx_token` (`auth_token`(4) ASC) VISIBLE;
;
