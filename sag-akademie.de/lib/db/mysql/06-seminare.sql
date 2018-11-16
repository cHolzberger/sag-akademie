-- seminar preise
-- status
-- 1 => hat stattgefunden
-- 2 => abgesagt durch SAG
--
-- muss bie aenderungen an den buchungen mit angepasst werden
CREATE OR REPLACE VIEW view_seminar_preis AS SELECT
seminar.*,
( CASE 
	WHEN seminar.freigabe_status = 4 THEN 1
	ELSE 0
END) as ausgebucht, 
seminar_art.bezeichnung as bezeichnung,
seminar_art.sichtbar_planung as sichtbar_planung,
seminar_art.farbe as farbe,
seminar_art.textfarbe as textfarbe,
seminar_freigabestatus.farbe as freigabe_farbe,
seminar_freigabestatus.flag as freigabe_flag,
seminar_freigabestatus.name as freigabe_name,
seminar_freigabestatus.veroeffentlichen as freigabe_veroeffentlichen,
standort.name as standort_name,
YEAR(seminar.datum_begin) as begin_jahr,
MONTH(seminar.datum_begin) as begin_monat,
DAY(seminar.datum_begin) as begin_tag,
YEAR(seminar.datum_ende) as ende_jahr,
MONTH(seminar.datum_ende) as ende_monat,
DAY(seminar.datum_ende) as ende_tag,
DATEDIFF(seminar.datum_ende, seminar.datum_begin) +1 as dauer,
seminar.kursgebuehr as preis,
IF (DATEDIFF(CURDATE(), seminar.datum_begin) < 0, 0, 1) as abgeschlossen,
COUNT(seminar.id) as anzahlBestaetigt,
0 as anzahlNichtBestaetigt,(
CASE WHEN seminar.storno_datum <> '0000-00-00' THEN 2
ELSE 1
END) as status,
seminar_art_rubrik.name as rubrik_name
FROM seminar
JOIN seminar_art ON ( seminar_art.id = seminar.seminar_art_id )
JOIN standort ON ( standort.id = seminar.standort_id )
JOIN seminar_freigabestatus ON ( seminar_freigabestatus.id = seminar.freigabe_status )
JOIN seminar_art_rubrik ON (  seminar_art_rubrik.id = seminar_art.rubrik )
LEFT JOIN buchung ON ( seminar.id = buchung.seminar_id AND buchung.storno_datum = '0000-00-00' AND buchung.umgebucht_id = '0' )
WHERE seminar.inhouse = 0
GROUP BY seminar.id;

-- nur die duer der seminare
CREATE OR REPLACE VIEW view_seminar_dauer AS SELECT
seminar.*,
seminar_art.bezeichnung as bezeichnung,
seminar_art.sichtbar_planung as sichtbar_planung,
seminar_art.farbe as farbe,
seminar_art.textfarbe as textfarbe,
seminar_freigabestatus.farbe as freigabe_farbe,
seminar_freigabestatus.flag as freigabe_flag,
seminar_freigabestatus.name as freigabe_name,
seminar_freigabestatus.veroeffentlichen as freigabe_veroeffentlichen,
standort.name as standort_name,
YEAR(seminar.datum_begin) as begin_jahr,
MONTH(seminar.datum_begin) as begin_monat,
DAY(seminar.datum_begin) as begin_tag,
YEAR(seminar.datum_ende) as ende_jahr,
MONTH(seminar.datum_ende) as ende_monat,
DAY(seminar.datum_ende) as ende_tag,
DATEDIFF(seminar.datum_ende, seminar.datum_begin) +1 as dauer,
IF (DATEDIFF(CURDATE(), seminar.datum_begin) < 0, 0, 1) as abgeschlossen
FROM seminar
JOIN seminar_art ON ( seminar_art.id = seminar.seminar_art_id )
JOIN standort ON ( standort.id = seminar.standort_id )
JOIN seminar_freigabestatus ON ( seminar.freigabe_status = seminar_freigabestatus.id )
JOIN seminar_art_rubrik ON (  seminar_art_rubrik.id = seminar_art.rubrik )
GROUP BY seminar.id;
-- referenten zu den seminaren
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_preis_referent AS
SELECT seminar.*,
referent.id as referent_id
FROM view_seminar_preis as seminar
JOIN seminar_referent ON (seminar_referent.seminar_id = seminar.id)
JOIN referent ON referent.id = seminar_referent.referent_id
GROUP BY seminar.id, referent.id;

-- Seminare ohne Referenten
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_ohne_referent AS
SELECT seminar.*,
seminar_art.dauer as 'dauer',
seminar_referent.referent_id as referent_id
FROM seminar
JOIN seminar_art ON ( seminar.seminar_art_id = seminar_art.id)
LEFT JOIN seminar_referent ON ( seminar.standort_id = seminar_referent.standort_id AND seminar.id = seminar_referent.seminar_id)
WHERE referent_id is NULL
GROUP BY seminar.id;


-- freigegebene seminsare
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_freigegebene_seminare AS SELECT
seminar.id as id,
seminar_art.id as seminar_art_id,
YEAR(seminar.datum_begin) as jahr,
RIGHT (YEAR(seminar.datum_begin),2) as jahr_kurz,
standort.kuerzel as standort_kuerzel
FROM seminar, seminar_art, seminar_freigabestatus, standort
WHERE seminar.seminar_art_id = seminar_art.id AND
seminar.freigabe_status = seminar_freigabestatus.id AND
seminar_freigabestatus.veroeffentlichen = 1 AND
seminar.standort_id = standort.id AND
seminar.kursnr_vergeben = 0
ORDER BY seminar.datum_begin ASC;

-- statistik

-- pro bereicht nach jahr und monat
CREATE OR REPLACE VIEW view_seminare_pro_bereich AS SELECT
seminar.id as id,
YEAR(seminar.datum_begin) as jahr,
MONTH(seminar.datum_begin) as monat,
seminar_art_rubrik.name as name,
COUNT(seminar.id) as gesamt
FROM seminar
JOIN seminar_art ON ( seminar_art.id = seminar.seminar_art_id )
JOIN seminar_freigabestatus ON ( seminar.freigabe_status = seminar_freigabestatus.id )
JOIN seminar_art_rubrik ON (  seminar_art_rubrik.id = seminar_art.rubrik )
WHERE seminar.storno_datum = '0000-00-00'
AND seminar_freigabestatus.veroeffentlichen = 1
GROUP BY monat,jahr,name;


-- pro bereich nach jahr
CREATE OR REPLACE VIEW view_seminare_pro_bereich_jahr AS SELECT
view_seminare_pro_bereich.id as id,
view_seminare_pro_bereich.jahr as jahr,
view_seminare_pro_bereich.name as name,
SUM(view_seminare_pro_bereich.gesamt) as gesamt
FROM view_seminare_pro_bereich
GROUP BY jahr,name;

-- teilnehmerliste
-- zeigt die teilnehmer an die auch wirklich am seminar teilnehmen

CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_teilnehmer AS SELECT
buchung.id as id,
seminar.id as seminar_id,
seminar.kursnr as kursnr,
buchung.teilgenommen as teilgenommen,
seminar_art.bezeichnung as bezeichnung,
standort.name as standort_name,
kontakt.firma as firma,
kontakt.strasse as strasse,
kontakt.nr as nummer,
kontakt.plz as plz,
kontakt.ort as ort,
kontakt.bundesland as bundesland,
kontakt.land as land,
kontakt.tel as telefon,
kontakt.fax as fax,
kontakt.mobil as mobil,
kontakt.email as email,
kontakt.url as url,
kontakt.privat as privat,
person.grad as grad,
person.name as name,
person.vorname as vorname,
person.funktion as funktion,
person.abteilung as abteilung,
person.strasse as person_strasse,
person.nr as person_nr,
person.plz as person_plz,
person.ort as person_ort,
person.bundesland as person_bundesland,
person.land as person_land,
person.tel as person_telefon,
person.fax as person_fax,
person.mobil as person_mobil,
person.email as person_email,
person.geburtstag as geburtstag,
(CASE
	WHEN person.geschlecht = 0 THEN "Sehr geehrter Herr"
	ELSE "Sehr geehrte Frau"
END) as anrede
FROM seminar, seminar_art, standort, kontakt, person, view_buchung as buchung
WHERE person.kontakt_id = kontakt.id
AND seminar.id = buchung.seminar_id
AND buchung.person_id = person.id
AND seminar.seminar_art_id = seminar_art.id
AND seminar.standort_id = standort.id
AND buchung.status = 1
ORDER BY person.name,  kontakt.firma;

CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_tage AS SELECT
standort_id as standort_id,
SUM(seminar.dauer) as dauer,
seminar.begin_jahr as jahr
FROM view_seminar_preis as seminar
GROUP BY seminar.begin_jahr, seminar.standort_id;

CREATE OR REPLACE VIEW `view_buchung_teilnehmer_email` AS select 
`buchung`.`id` AS `id`,
`buchung`.`seminar_id` AS `seminar_id`,
`buchung`.`status` AS `status`,
(case when (`person`.`email` <> '') then `person`.`email` when (`kontakt`.`email` <> '') then `kontakt`.`email` else '' end) AS `email` 
from ((`view_buchung` `buchung` left join `person` on((`buchung`.`person_id` = `person`.`id`))) left join `kontakt` on((`person`.`kontakt_id` = `kontakt`.`id`))) 
where (`buchung`.`status` = 1);

-- Seminar Buchung nach Kontakt
CREATE OR REPLACE VIEW view_seminar_kontakt AS SELECT
seminar.*,
kontakt.id as kontakt_id
FROM view_seminar_preis as seminar
LEFT JOIN buchung ON ( buchung.seminar_id = seminar.id ) 
LEFT JOIN person ON ( buchung.person_id = person.id ) 
LEFT JOIN kontakt ON ( person.kontakt_id = kontakt.id )
GROUP BY seminar.seminar_art_id, kontakt.id;