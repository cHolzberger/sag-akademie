-- seminare fuer die planung
CREATE OR REPLACE VIEW view_seminar_planung AS SELECT SQL_CACHE
seminar.*,
seminar_art.aktualisierung_gesperrt as seminar_art_gesperrt,
seminar_art_aktualisierung.gesperrt as  standort_gesperrt,
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
IF( buchung.id IS NULL , 0, COUNT(seminar.id) )  as teilnehmer,
0 as anzahlNichtBestaetigt,(
CASE WHEN seminar.storno_datum <> '0000-00-00' THEN 2
	ELSE 1
	END) as status,
seminar_art_rubrik.name as rubrik_name,
ihs.*,
inhouse_kunde.firma as inhouse_firma

FROM seminar
JOIN seminar_art ON ( seminar_art.id = seminar.seminar_art_id )
JOIN standort ON ( standort.id = seminar.standort_id )
JOIN seminar_freigabestatus ON ( seminar.freigabe_status = seminar_freigabestatus.id )
JOIN seminar_art_rubrik ON (  seminar_art_rubrik.id = seminar_art.rubrik )
LEFT JOIN seminar_art_aktualisierung ON ( seminar_art_aktualisierung.seminar_art_id = seminar.seminar_art_id AND seminar_art_aktualisierung.standort_id = seminar.standort_id)
LEFT JOIN kontakt as inhouse_kunde ON ( seminar.inhouse_kunde = inhouse_kunde.id )
LEFT JOIN inhouse_ort as ihs ON ( seminar.id = ihs.seminar_id )
LEFT JOIN view_buchung as buchung ON ( seminar.id = buchung.seminar_id AND buchung.status = 1 )
GROUP BY seminar.id;

-- notizen zur planung

CREATE OR REPLACE ALGORITHM = MERGE VIEW view_planung_notiz AS SELECT
planung_notiz.*,
YEAR(planung_notiz.id) as jahr,
MONTH(planung_notiz.id) as monat,
DAY(planung_notiz.id) as tag
FROM planung_notiz ;

