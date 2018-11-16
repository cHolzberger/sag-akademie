-- planung referenten �bersicht 2010
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_referent_export AS SELECT
seminar.datum_begin + INTERVAL seminar_referent.tag DAY - INTERVAL 1 DAY as Datum,
standort.name as 'Standort',
seminar_art.id,
referent.grad,
referent.name,
referent.vorname,
referent.firma,
seminar_referent.optional, 
seminar_referent.theorie, 
seminar_referent.praxis, 
CONCAT (seminar_referent.start_stunde,":", seminar_referent.start_minute) as theorie_start,
CONCAT (seminar_referent.ende_stunde,":", seminar_referent.ende_minute) as theorie_ende,
CONCAT (seminar_referent.start_praxis_stunde,":", seminar_referent.start_praxis_minute) as praxis_start,
CONCAT (seminar_referent.ende_praxis_stunde,":", seminar_referent.ende_praxis_minute) as praxis_ende,
seminar.kursnr as kursnr,
seminar.freigabe_status as freigabe_status,
seminar_art.bezeichnung as bezeichnung,
seminar.datum_begin as beginn,
seminar.datum_ende as ende,
DATEDIFF(seminar.datum_ende, seminar.datum_begin) +1 as dauer,
YEAR(seminar.datum_begin) as seminar_year,
referent.id as referent_id
FROM seminar
JOIN standort ON seminar.standort_id = standort.id
JOIN seminar_art ON  seminar.seminar_art_id = seminar_art.id
LEFT OUTER JOIN seminar_referent ON ( seminar.standort_id = seminar_referent.standort_id AND seminar.id = seminar_referent.seminar_id )
LEFT OUTER JOIN referent ON seminar_referent.referent_id = referent.id
GROUP BY seminar.id, seminar_referent.tag, seminar_referent.referent_id, seminar.standort_id
ORDER BY seminar.datum_begin ASC, seminar_referent.tag ASC;


-- referenten zu den seminaren
CREATE OR REPLACE VIEW view_seminar_referent AS
SELECT seminar_referent.seminar_id as id,
seminar_referent.tag as tag,
GROUP_CONCAT(referent.kuerzel ORDER BY seminar_referent.optional ASC, referent.kuerzel ASC SEPARATOR ',') as kuerzel,
seminar_referent.standort_id as standort_id
FROM seminar_referent
JOIN referent ON referent.id = seminar_referent.referent_id
GROUP BY seminar_referent.seminar_id, seminar_referent.tag, seminar_referent.standort_id;

-- collision referent / seminare
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_referent_datum AS SELECT
DATE_ADD(seminar.datum_begin, INTERVAL seminar_referent.tag-1 DAY) as datum,
seminar_referent.id as id,
seminar_referent.referent_id as referent_id,
seminar_referent.standort_id as standort_id,
seminar.id as seminar_id,
seminar.kursnr as seminar_kursnr
From seminar_referent
JOIN seminar ON ( seminar.id = seminar_referent.seminar_id)
Group by datum, standort_id, referent_id;

-- view zum herausfinden von mehrfacheinträgen pro Referent
-- hit > 1 wenn treffer
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_referent_collision AS
SELECT *, COUNT(seminar_referent.id) as hit
FROM seminar_referent 
WHERE seminar_referent.referent_id != -1 AND seminar_referent.referent_id != 0
GROUP BY seminar_id, standort_id, start_stunde, start_minute, start_praxis_stunde, start_praxis_minute, referent_id,tag;


-- referenten planung
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_art_referent AS SELECT
seminar_art_referent.*,
referent.name as referent_name,
standort.name as standort_name
FROM
seminar_art_referent,referent,standort
WHERE
seminar_art_referent.referent_id = referent.id
AND
seminar_art_referent.standort_id = standort.id;