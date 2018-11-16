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
WHERE seminar.storno_datum IS NULL
AND seminar_freigabestatus.veroeffentlichen = 1
GROUP BY monat,jahr,name;

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
CASE WHEN seminar.storno_datum IS NOT NULL THEN 2
ELSE 1
END) as status,
seminar_art_rubrik.name as rubrik_name
FROM seminar
JOIN seminar_art ON ( seminar_art.id = seminar.seminar_art_id )
JOIN standort ON ( standort.id = seminar.standort_id )
JOIN seminar_freigabestatus ON ( seminar_freigabestatus.id = seminar.freigabe_status )
JOIN seminar_art_rubrik ON (  seminar_art_rubrik.id = seminar_art.rubrik )
LEFT JOIN buchung ON ( seminar.id = buchung.seminar_id AND buchung.storno_datum = NULL AND buchung.umgebucht_id = '0' )
WHERE seminar.inhouse = 0
GROUP BY seminar.id;

