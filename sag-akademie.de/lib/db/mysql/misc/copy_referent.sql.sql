-- standort planung auf andere standorte kopieren
INSERT INTO seminar_art_referent (seminar_art_id, tag,referent_id, standort_id) SELECT seminar_art_id, tag, referent_id, 17 as standort_id FROM seminar_art_referent WHERE standort_id = 1;

INSERT INTO seminar_art_referent (seminar_art_id, tag ,referent_id, standort_id) SELECT seminar_art_id, tag, referent_id, 15 as standort_id FROM seminar_art_referent WHERE standort_id = 2;
INSERT INTO seminar_art_referent (seminar_art_id, tag,referent_id, standort_id) SELECT seminar_art_id, tag, referent_id, 16 as standort_id FROM seminar_art_referent WHERE standort_id = 2;
INSERT INTO seminar_art_referent (seminar_art_id, tag,referent_id, standort_id) SELECT seminar_art_id, tag, referent_id, 19 as standort_id FROM seminar_art_referent WHERE standort_id = 2;

-- inserts finden
INSERT INTO seminar_referent ( seminar_id, tag, referent_id, standort_id )
SELECT seminar.id, seminar_art_referent.tag, seminar_art_referent.referent_id, seminar_art_referent.standort_id
FROM seminar, seminar_art_referent
WHERE seminar.seminar_art_id = seminar_art_referent.seminar_art_id
AND seminar.standort_id = seminar_art_referent.standort_id
AND seminar.datum_begin > "2010-01-01";


-- inserts finden
INSERT INTO seminar_referent ( seminar_id, tag, referent_id, standort_id )
SELECT seminar.id, seminar_art_referent.tag, seminar_art_referent.referent_id, seminar_art_referent.standort_id
FROM seminar, seminar_art_referent
WHERE seminar.seminar_art_id = seminar_art_referent.seminar_art_id
AND seminar.standort_id = seminar_art_referent.standort_id
AND seminar.id = 934;