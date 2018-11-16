CREATE OR REPLACE VIEW view_inhouse_seminar AS SELECT
seminar.*,
inhouse_ort.inhouse_strasse,
inhouse_ort.inhouse_plz,
inhouse_ort.inhouse_ort,
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
kontakt.firma as kontakt_firma,
kontakt.id as kontakt_id
FROM seminar
JOIN seminar_art ON ( seminar_art.id = seminar.seminar_art_id )
JOIN standort ON ( standort.id = seminar.standort_id )
JOIN seminar_freigabestatus ON ( seminar.freigabe_status = seminar_freigabestatus.id )
LEFT JOIN buchung ON ( seminar.id = buchung.seminar_id AND buchung.storno_datum = '0000-00-00' AND buchung.umgebucht_id = '0' )
LEFT JOIN inhouse_ort ON (seminar.id = inhouse_ort.seminar_id)
LEFT JOIN kontakt ON (seminar.inhouse_kunde = kontakt.id)
WHERE seminar.inhouse = 1
GROUP BY seminar.id;

CREATE OR REPLACE ALGORITHM = MERGE VIEW view_inhouse_seminar_art AS SELECT
seminar_art.*,
seminar_art.kursgebuehr  as preis,
seminar_art_status.name as status_name
FROM seminar_art
LEFT JOIN seminar_art_status ON status = seminar_art_status.id
WHERE seminar_art.inhouse=1;
