
-- view zum herausfinden von mehrfacheinträgen pro Referent für Termine
-- hit > 1 wenn treffer
CREATE OR REPLACE VIEW check_seminar_referent AS
SELECT *, COUNT(id) as ref_count  FROM `seminar_referent` 
WHERE referent_id != 0 AND referent_id != -1
GROUP BY seminar_id, tag,standort_id, theorie, praxis, start_stunde, start_minute, start_praxis_stunde, start_praxis_minute, referent_id
HAVING COUNT(id) > 1;


-- view zum herausfinden von mehrfacheinträgen pro Referent für Seminare
-- hit > 1 wenn treffer
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`sag_stable`@`%` SQL SECURITY DEFINER VIEW `check_seminar_art_referent` AS select `seminar_art_referent`.`id` AS `id`,`seminar_art_referent`.`seminar_art_id` AS `seminar_art_id`,`seminar_art_referent`.`tag` AS `tag`,`seminar_art_referent`.`referent_id` AS `referent_id`,`seminar_art_referent`.`standort_id` AS `standort_id`,`seminar_art_referent`.`theorie` AS `theorie`,`seminar_art_referent`.`praxis` AS `praxis`,`seminar_art_referent`.`anwesenheit` AS `anwesenheit`,`seminar_art_referent`.`start_stunde` AS `start_stunde`,`seminar_art_referent`.`start_minute` AS `start_minute`,`seminar_art_referent`.`ende_stunde` AS `ende_stunde`,`seminar_art_referent`.`ende_minute` AS `ende_minute`,`seminar_art_referent`.`start_praxis_stunde` AS `start_praxis_stunde`,`seminar_art_referent`.`start_praxis_minute` AS `start_praxis_minute`,`seminar_art_referent`.`ende_praxis_stunde` AS `ende_praxis_stunde`,`seminar_art_referent`.`ende_praxis_minute` AS `ende_praxis_minute`,`seminar_art_referent`.`optional` AS `optional`,`seminar_art_referent`.`version` AS `version`,`seminar_art_referent`.`angelegt_datum` AS `angelegt_datum`,`seminar_art_referent`.`angelegt_user_id` AS `angelegt_user_id`,count(`seminar_art_referent`.`id`) AS `ref_count` from `seminar_art_referent` where ((`seminar_art_referent`.`referent_id` <> 0) and (`seminar_art_referent`.`referent_id` <> -(1))) group by `seminar_art_referent`.`seminar_art_id`,`seminar_art_referent`.`tag`,`seminar_art_referent`.`standort_id`,`seminar_art_referent`.`theorie`,`seminar_art_referent`.`praxis`,`seminar_art_referent`.`start_stunde`,`seminar_art_referent`.`start_minute`,`seminar_art_referent`.`start_praxis_stunde`,`seminar_art_referent`.`start_praxis_minute`,`seminar_art_referent`.`referent_id` having (count(`seminar_art_referent`.`id`) > 1);

/*
CREATE OR REPLACE VIEW check_seminar_art_referent AS
SELECT *, COUNT(id) as ref_count  FROM `seminar_art_referent` 
WHERE referent_id != 0 AND referent_id != -1
GROUP BY seminar_id, tag,standort_id, theorie, praxis, start_stunde, start_minute, start_praxis_stunde, start_praxis_minute, referent_id
HAVING COUNT(id) > 1;
*/
