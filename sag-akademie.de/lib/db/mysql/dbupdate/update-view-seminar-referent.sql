CREATE OR REPLACE VIEW view_seminar_referent AS
SELECT seminar_referent.seminar_id as id,
seminar_referent.tag as tag,
GROUP_CONCAT(referent.kuerzel ORDER BY seminar_referent.optional ASC, referent.kuerzel ASC SEPARATOR ',') as kuerzel,
seminar_referent.standort_id as standort_id
FROM seminar_referent
JOIN referent ON referent.id = seminar_referent.referent_id
GROUP BY seminar_referent.seminar_id, seminar_referent.tag, seminar_referent.standort_id;