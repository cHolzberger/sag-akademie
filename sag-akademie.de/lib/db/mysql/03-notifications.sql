SET NAMES "utf8";
SET collation_connection = @@collation_database;

-- VIEWS fuer die Benachrichtigungen

CREATE OR REPLACE VIEW view_person_heute_geburtstag AS
SELECT  person.*,
( CASE
	WHEN person.geschlecht = 0 THEN "Sehr geehrter Herr"
	WHEN person.geschlecht = 1 THEN "Sehr geehrte Frau"
END) as anredestr
FROM person
WHERE ((DATE_FORMAT(CURDATE(),"%m-%d") = DATE_FORMAT(geburtstag,"%m-%d"))
OR ((DATE_FORMAT(CURDATE(),"%m-%d") = '03-01')
AND (DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 1 DAY),"%m-%d") = '02-28')
AND (DATE_FORMAT(geburtstag,"%m-%d") = '02-29')))
AND email <> ""
AND geburtstag != '1900-01-01'
GROUP BY email;
-- bitte beruecksichtigen
-- keine stornierten buchung mit zaehlen!

CREATE OR REPLACE VIEW view_seminar_teilnehmer_nicht_erreicht_subquery AS
SELECT seminar_id, COUNT(*) as anzahl_teilnehmer from view_buchung_preis
WHERE status=1
GROUP BY seminar_id;

CREATE OR REPLACE VIEW view_seminar_teilnehmer_nicht_erreicht AS
SELECT seminar.*,
DATE_FORMAT(seminar.datum_begin,"%e.%c.%Y") as datum_beginn_de,
DATE_FORMAT(seminar.datum_ende,"%e.%c.%Y") as datum_ende_de,
sub.anzahl_teilnehmer,
DATEDIFF(datum_begin,CURDATE()) as delta
 FROM view_seminar_teilnehmer_nicht_erreicht_subquery sub, seminar
WHERE ((sub.anzahl_teilnehmer<seminar.teilnehmer_min)
AND (sub.seminar_id=seminar.id))
AND datum_begin > CURDATE();

-- email log vergangene tage
CREATE OR REPLACE VIEW view_email_log AS
SELECT email_log.*, DATEDIFF(CURDATE(),email_log.gesendet) as delta
FROM email_log;
