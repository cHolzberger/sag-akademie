--  Buchungen mit Seminar infos
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchung_seminar AS SELECT
buchung.uuid as id, seminar.kursnr as kursnr, seminar.datum_begin as datum_begin
FROM buchung,seminar
WHERE buchung.seminar_id = seminar.id
AND buchung.deleted_at = '0000-00-00 00:00:00';

-- hotels mit preisen

CREATE OR REPLACE ALGORITHM = MERGE VIEW view_hotel_preis AS SELECT
hotel.id as _id, hotel_preis.*,
hotel_preis.zimmerpreis_ez + hotel_preis.fruehstuecks_preis + hotel_preis.marge as verkaufspreis_ez,
hotel_preis.zimmerpreis_dz + hotel_preis.fruehstuecks_preis + hotel_preis.marge as verkaufspreis_dz,
hotel_preis.zimmerpreis_mb46 + hotel_preis.fruehstuecks_preis + hotel_preis.marge as verkaufspreis_mb46,
hotel.name as name,
hotel.ort as ort
FROM hotel, hotel_preis
WHERE hotel.id = hotel_preis.hotel_id;



-- seminar arten mit preisen

CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_art_preis AS SELECT
seminar_art.*,
seminar_art.kursgebuehr  as preis,
seminar_art_status.name as status_name
FROM seminar_art
LEFT JOIN seminar_art_status ON seminar_art.status = seminar_art_status.id;


-- hotel buchungs preise

CREATE OR REPLACE ALGORITHM = MERGE VIEW view_hotel_buchung_preis AS SELECT
hotel_buchung.*,
h2.name as hotel_name,
hotel_buchung.zimmerpreis_ez + hotel_buchung.fruehstuecks_preis + hotel_buchung.marge as verkaufspreis_ez,
hotel_buchung.zimmerpreis_dz + hotel_buchung.fruehstuecks_preis + hotel_buchung.marge as verkaufspreis_dz
FROM hotel_buchung
LEFT JOIN hotel h2 ON hotel_id = h2.id 
WHERE hotel_buchung.deleted_at = "0000-00-00 00:00:00";


-- Buchungen Papierkorb - >
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchungen_papierkorb AS SELECT
buchung.*,
seminar.kursnr,
kontakt.firma,
person.vorname,
person.name,
kontakt.strasse,
kontakt.nr,
kontakt.plz,
kontakt.ort,
kontakt.bundesland,
kontakt.land,
person.ansprechpartner,
person.grad,
person.funktion,
person.strasse as person_strasse,
person.nr as person_nr,
person.plz as person_plz,
person.ort as person_ort,
person.tel,
person.fax,
person.mobil,
person.email,
person.geburtstag,
seminar_art.id as seminar_art_id,
seminar.datum_begin,
seminar.datum_ende,
kontakt.vdrk_mitglied as kontakt_vdrk_mitglied,
kontakt.vdrk_mitglied_nr as kontakt_vdrk_mitglied_nr,
CASE WHEN seminar.storno_datum THEN 0.00
WHEN buchung.storno_datum THEN 0.00
WHEN buchung.arbeitsagentur THEN 0.00
WHEN buchung.bildungscheck THEN buchung.kursgebuehr * 0.5 + buchung.kosten_verpflegung * DATEDIFF(seminar.datum_ende, seminar.datum_begin) + buchung.kosten_unterlagen
ELSE buchung.kursgebuehr +  buchung.kosten_verpflegung * DATEDIFF(seminar.datum_ende, seminar.datum_begin) + buchung.kosten_unterlagen END
	*
IF (DATEDIFF(buchung.datum, seminar.datum_begin) < 56,0.95, 1)
	*

IF (buchung.vdrk_mitglied, 0.95, 1)
	* ((100 - buchung.rabatt) / 100 ) as preis,

( 1-(100 - buchung.rabatt) / 100 ) as rabattInProzent,
 IF (DATEDIFF(buchung.datum, seminar.datum_begin) < 56,1, 0) as fruehbucher,
 IF (DATEDIFF(CURDATE(), seminar.datum_begin) < 0, 0, 1) as abgeschlossen,
 (CASE
  WHEN buchung.storno_datum <> '0000-00-00'  THEN 2
  WHEN buchung.umbuchungs_datum <> '0000-00-00' THEN 3
  WHEN buchung.umgebucht_id != '0'  THEN 3
  WHEN buchung.bestaetigt = 1 THEN 1
  ELSE 0
 END) as status
FROM buchung, seminar,seminar_art, person, kontakt
WHERE buchung.seminar_id = seminar.id
AND seminar.seminar_art_id = seminar_art.id
AND buchung.person_id = person.id
AND kontakt.id = person.kontakt_id
AND buchung.deleted_at <>  '0000-00-00 00:00:00';

-- Feiertage - >

CREATE OR REPLACE ALGORITHM = MERGE VIEW view_feiertag_r AS Select
feiertag.*,
r_feiertag_bundesland.id As r_id,
r_feiertag_bundesland.bundesland_id,
r_feiertag_bundesland.feiertag_id
FROM feiertag, r_feiertag_bundesland
WHERE feiertag.uid = r_feiertag_bundesland.feiertag_id;

-- Akquise_Kontak
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_akquise AS SELECT
akquise_kontakt.*,
kontakt_quelle.name as kontakt_quelle_name
FROM akquise_kontakt, kontakt_quelle
WHERE akquise_kontakt.quelle_id = kontakt_quelle.id AND akquise_kontakt.deleted_at = '0000-00-00 00:00:00'

Group by akquise_kontakt.id;

-- Akquise_Kontak - Kontakt - >
-- wichtig die reihenfolge der felder muss auf beiden seiten der union gleich seien
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_akquise_kontakt_r AS (SELECT
CONCAT('ak', akquise_kontakt.id) AS id,
akquise_kontakt.firma AS firma,
'' AS alias,
akquise_kontakt.strasse AS strasse,
akquise_kontakt.nr AS nr,
akquise_kontakt.plz AS plz,
akquise_kontakt.ort AS ort,
akquise_kontakt.bundesland AS bundesland,
akquise_kontakt.kreis AS kreis,
akquise_kontakt.regierungsbezirk AS regierungsbezirk,
akquise_kontakt.tel AS tel,
akquise_kontakt.fax AS fax,
akquise_kontakt.abteilung AS abteilung,
akquise_kontakt.anrede AS anrede,
akquise_kontakt.titel AS titel,
akquise_kontakt.vorname AS vorname,
akquise_kontakt.name AS name,
akquise_kontakt.tel_durchwahl AS rel_durchwahl,
akquise_kontakt.mobil AS mobil,
akquise_kontakt.url AS url,
akquise_kontakt.email AS email,
akquise_kontakt.newsletter AS newsletter,
akquise_kontakt.abmelde_datum AS abmelde_datum,
akquise_kontakt.anmelde_datum AS anmelde_datum,
akquise_kontakt.geaendert_von AS geaendert_von,
akquise_kontakt.bereits_in_verteiler AS bereits_inverteiler,
akquise_kontakt.kontakt_id AS kontakt_id,
akquise_kontakt.vergleich AS vergleich,
akquise_kontakt.kontakt_quelle AS kontakt_quelle,
akquise_kontakt.kontakt_quelle_stand AS kontakt_quelle_stand,
akquise_kontakt.umkreis AS umkreis,
akquise_kontakt.kontaktkategorie AS kontaktkategorie,
akquise_kontakt.branche AS branche,
akquise_kontakt.geaendert AS geaaendert,
akquise_kontakt.angelegt_user_id AS angelegt_user_id,
akquise_kontakt.quelle_id AS quelle_id,
akquise_kontakt.qualifiziert AS qualifiziert,
akquise_kontakt.qualifiziert_datum AS qualifiziert_datum,
akquise_kontakt.qualifiziert_notiz AS qualifiziert_notiz,
akquise_kontakt.ansprechpartner_email AS ansprechpartner_email,
kontakt_quelle.name AS kontakt_quelle_name,
'' AS hauptverwaltung,
'' AS kundennr,
'' AS vdrk_mitglied,
'' AS vdrk_mitglied_nr,
'' AS kundenstatus,
'' AS privat,
akquise_kontakt.bundesland_id AS bundesland_id,
'' AS land_id,
'' AS zusatz,
'' AS blz,
'' AS kto,
'' AS bank,
'' AS zahlart,
'' AS bemerkung,
'' AS notiz,
akquise_kontakt.taetigkeitsbereich_id AS taetigkeitsbereich_id,
akquise_kontakt.branche_id AS branche_id,
0 AS kunde
FROM akquise_kontakt, kontakt_quelle WHERE
akquise_kontakt.quelle_id = kontakt_quelle.id AND akquise_kontakt.deleted_at = '0000-00-00 00:00:00'

GROUP BY akquise_kontakt.id)
UNION (SELECT

CONCAT('kk', kontakt.id) AS id,
kontakt.firma AS firma,
kontakt.alias AS alias,
kontakt.strasse AS strasse,
kontakt.nr AS nr,
kontakt.plz AS plz,
kontakt.ort AS ort,
x_bundesland.name AS bundesland,
'' AS kreis,
'' AS regierungsbezirk,
kontakt.tel AS tel,
kontakt.fax AS fax,
person.abteilung AS abteilung,
'' AS anrede,
person.grad AS titel,
person.vorname AS vorname,
person.name AS name,
person.tel AS rel_durchwahl,
kontakt.mobil AS mobil,
kontakt.url AS url,
kontakt.email AS email,
kontakt.newsletter AS newsletter,
kontakt.newsletter_abmeldedatum AS abmelde_datum,
kontakt.newsletter_anmeldedatum AS anmelde_datum,
'' AS geaendert_von,
'' AS bereits_inverteiler,
'' AS kontakt_id,
'' AS vergleich,
kontakt.kontakt_quelle AS kontakt_quelle,
kontakt.kontakt_quelle_stand AS kontakt_quelle_stand,
'' AS umkreis,
kontakt.kontaktkategorie AS kontaktkategorie,
'' AS geaaendert,
kontakt.angelegt_user_id AS angelegt_user_id,
'' AS quelle_id,
'' AS qualifiziert,
'' AS qualifiziert_datum,
'' AS qualifiziert_notiz,
'' AS ansprechpartner_email,
kontakt.hauptverwaltung AS hauptverwaltung,
kontakt.branche AS branche,
kontakt.kundennr AS kundennr,
kontakt.vdrk_mitglied AS vdrk_mitglied,
kontakt.vdrk_mitglied_nr AS vdrk_mitglied_nr,
kontakt.kundenstatus AS kundenstatus,
kontakt.privat AS privat,
kontakt.bundesland_id AS bundesland_id,
kontakt.land_id AS land_id,
kontakt.zusatz AS zusatz,
kontakt.blz AS blz,
kontakt.kto AS kto,
kontakt.bank AS bank,
kontakt.zahlart AS zahlart,
kontakt.bemerkung AS bemerkung,
kontakt.notiz AS notiz,
'' AS kontakt_quelle_name,
kontakt.taetigkeitsbereich_id AS taetigkeitsbereich_id,
kontakt.branche_id AS branche_id,
1 AS kunde
FROM kontakt, person, x_bundesland
WHERE kontakt.id = person.kontakt_id
AND kontakt.bundesland_id = x_bundesland.id
group by kontakt.id);



-- kontakte
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_kontakt AS SELECT
kontakt.*,
x_user.name AS angelegt_user_name,
x_bundesland.name AS  bundesland_name,
x_land.name AS land_name
FROM kontakt
LEFT JOIN x_user ON (kontakt.angelegt_user_id = x_user.id)
LEFT JOIN x_bundesland ON(kontakt.bundesland_id = x_bundesland.id)
LEFT JOIN x_land ON (kontakt.land_id = x_land.id);


-- person
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_person AS SELECT
person.*,
kontakt.firma as firma,
x_user.name AS angelegt_user_name,
x_bundesland.name as bundesland_name,
x_land.name as land_name
FROM person
LEFT JOIN kontakt ON (person.kontakt_id = kontakt.id )
LEFT JOIN x_user ON (person.angelegt_user_id = x_user.id)
LEFT JOIN x_bundesland ON(person.bundesland_id = x_bundesland.id)
LEFT JOIN x_land ON (person.land_id = x_land.id);


-- integrity
UPDATE person SET angelegt_user_id = -1 WHERE angelegt_user_id=0;
UPDATE person SET bundesland_id = 1 WHERE bundesland_id = 0;
UPDATE person SET land_id = -1 WHERE land_id = 0;
UPDATE person SET geburtstag = '0000-00-00' WHERE geburtstag='0000-00-00';


UPDATE kontakt SET angelegt_user_id = -1 WHERE angelegt_user_id=0;
UPDATE kontakt SET bundesland_id = 1 WHERE bundesland_id = 0;
UPDATE kontakt SET land_id = -1 WHERE land_id = 0;
UPDATE kontakt SET kontaktkategorie = 6 WHERE kontaktkategorie = 0;

UPDATE akquise_kontakt SET kontaktkategorie = 6 WHERE kontaktkategorie = 0;

UPDATE person,kontakt SET person.email = kontakt.email WHERE person.email="" AND kontakt.email<> "" AND person.kontakt_id = kontakt.id AND person.ansprechpartner=1;

UPDATE kontakt SET geaendert_von = -1 WHERE geaendert_von = 0;
UPDATE kontakt SET angelegt_user_id = -1 WHERE angelegt_user_id = 0;


-- benutzer mit verketteten namen
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_x_user AS
SELECT x_user.id as id,
CONCAT(x_user.name, ", ", x_user.vorname) as name
FROM x_user ORDER BY x_user.id;

-- newsletter : email + md5 sum + ak
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_newsletter_email AS (
SELECT
akquise_kontakt.id,
akquise_kontakt.email as email,
MD5(akquise_kontakt.email) as md5,
akquise_kontakt.newsletter as newsletter,
'AkquiseKontakt' as art
FROM akquise_kontakt)
UNION
(
SELECT
kontakt.id,
kontakt.email as email,
MD5(kontakt.email) as md5,
kontakt.newsletter as newsletter,
'Kontakt' as art
FROM kontakt)
UNION
(
SELECT
person.id,
person.email as email,
MD5(person.email) as md5,
person.newsletter as newsletter,
'Person' as art
FROM person);


-- Newsletter empfaenger, UNION
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_newsletter_empfaenger AS ( SELECT
kontakt.firma as firma,
"" as vorname,
"" as name,
kontakt.email as email,
kontakt.strasse as strasse,
kontakt.nr as nr,
kontakt.ort as ort,
kontakt.plz as plz,
kontakt.newsletter as newsletter,
kontakt_kategorie.name as kategorie,
'kontakt' as quelle,
'Kontakt' as art,
REPLACE(kontakt.email, "@", "-a-") as email_enc
FROM kontakt,kontakt_kategorie 
WHERE kontakt.newsletter=1 
AND kontakt_kategorie.id = kontakt.kontaktkategorie 
AND ( kontakt.email <> "" AND kontakt.email <> "-" ) )
UNION ( SELECT
kontakt.firma as firma,
person.vorname as vorname,
person.name as name,
person.email as email,
person.strasse as strasse,
person.nr as nr,
person.ort as ort,
person.plz as plz,
person.newsletter as newsletter,
'person' as kategorie,
'person' as quelle,
'Person' as art,
REPLACE(person.email, "@", "-a-") as email_enc
FROM person,kontakt 
WHERE kontakt.id = person.kontakt_id 
AND person.newsletter =1 
AND ( person.email <> "" AND person.email <> "-" ));

-- Newsletter empfaenger, UNION
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_newsletter_empfaenger_ohne_personen AS SELECT
kontakt.firma as firma,
"" as vorname,
"" as name,
kontakt.email as email,
kontakt.strasse as strasse,
kontakt.nr as nr,
kontakt.ort as ort,
kontakt.plz as plz,
kontakt.newsletter as newsletter,
kontakt_kategorie.name as kategorie,
'kontakt' as quelle,
'Kontakt' as art,
REPLACE(kontakt.email, "@", "-a-") as email_enc
FROM kontakt,kontakt_kategorie 
WHERE kontakt.newsletter=1 
AND kontakt_kategorie.id = kontakt.kontaktkategorie 
AND ( kontakt.email <> "" AND kontakt.email <> "-" );

CREATE OR REPLACE view view_kontakt_kategorie_anmeldung AS
SELECT id,name from kontakt_kategorie WHERE anmelden_form = 1;

CREATE OR REPLACE VIEW view_person_duplikate AS
SELECT id, kontakt_id, name, vorname, GROUP_CONCAT(CONVERT(id, CHAR(8)) SEPARATOR ";") AS duplikate FROM `person` GROUP BY kontakt_id, name, vorname, ort, plz,geburtstag HAVING count(*) > 1;-- BUCHUNGEN STATUS
-- 1 bestaetigt
-- 2 storno
-- 3 umgebucht
-- 4 abgesagt
-- 5 nicht teilgenommen
-- preise
CREATE OR REPLACE VIEW view_buchung AS
	SELECT
		buchung.*,
		seminar.seminar_art_id    AS seminar_art_id,
		seminar.kursnr            AS kursnr,
		seminar_art_rubrik.name   AS rubrik_name,
		angelegt_user.name        AS angelegt_von,
		bearbeitet_user.name      AS bearbeitet_von,
		neues_seminar.kursnr      AS umgebucht_auf_kursnr,
		neues_seminar.id          AS umgebucht_auf_id,
		neues_seminar.datum_begin AS umgebucht_auf_datum_begin,
		(CASE
		 WHEN buchung.storno_datum <> '0000-00-00' THEN 2
		 WHEN buchung.teilgenommen <> '0000-00-00' THEN 5
		 WHEN buchung.umgebucht_id != '0' THEN 3
		 WHEN seminar.storno_datum <> '0000-00-00' THEN 4
		 WHEN buchung.bestaetigt = 1 THEN 1
		 ELSE 1
		 END)                     AS status,
		seminar.standort_id       AS standort_id

	FROM buchung

		LEFT JOIN (seminar, seminar_art, seminar_art_rubrik)
			ON (buchung.seminar_id = seminar.id AND seminar.seminar_art_id = seminar_art.id AND
			    seminar_art.rubrik = seminar_art_rubrik.id)
		LEFT JOIN (buchung AS umbuchung, seminar AS neues_seminar)
			ON (buchung.umgebucht_id = umbuchung.id AND umbuchung.seminar_id = neues_seminar.id)
		LEFT JOIN x_user AS angelegt_user
			ON buchung.angelegt_user_id = angelegt_user.id
		LEFT JOIN x_user AS bearbeitet_user
			ON buchung.geaendert_von = bearbeitet_user.id
;

-- buchungen mit ansprechpartnern



CREATE OR REPLACE VIEW view_buchung_preis AS
	SELECT
		buchung.*,
		kontakt.firma,
		person.vorname,
		person.name,
		kontakt.strasse,
		kontakt.nr,
		kontakt.plz,
		kontakt.ort,
		x_bundesland.name                                           AS bundesland,
		x_land.name                                                 AS land,
		person.ansprechpartner,
		person.grad,
		person.funktion,
		person.strasse                                              AS person_strasse,
		person.nr                                                   AS person_nr,
		person.plz                                                  AS person_plz,
		person.ort                                                  AS person_ort,
		person.tel,
		person.fax,
		person.mobil,
		person.email,
		person.geburtstag,
		person.kontakt_id                                           AS kontakt_id,
		seminar.datum_begin,
		seminar.datum_ende,
		kontakt.vdrk_mitglied                                       AS kontakt_vdrk_mitglied,
		kontakt.dwa_mitglied                                        AS kontakt_dwa_mitglied,
		kontakt.rsv_mitglied                                        AS kontakt_rsv_mitglied,
		kontakt.vdrk_mitglied_nr                                    AS kontakt_vdrk_mitglied_nr,
		CASE WHEN seminar.storno_datum THEN 0.00
		WHEN buchung.storno_datum THEN 0.00
		WHEN buchung.arbeitsagentur THEN 0.00
		WHEN buchung.bildungscheck THEN
			buchung.kursgebuehr * 0.5 + buchung.kosten_verpflegung * DATEDIFF(seminar.datum_ende, seminar.datum_begin) +
			buchung.kosten_unterlagen
		ELSE buchung.kursgebuehr + buchung.kosten_verpflegung * DATEDIFF(seminar.datum_ende, seminar.datum_begin) +
		     buchung.kosten_unterlagen END
		*
		IF(DATEDIFF(buchung.datum, seminar.datum_begin) < 56, 0.95, 1)
		*

		IF(buchung.vdrk_mitglied, 0.95, 1)
		* ((100 - buchung.rabatt) / 100)                            AS preis,

		(1 - (100 - buchung.rabatt) / 100)                          AS rabattInProzent,
		IF(DATEDIFF(buchung.datum, seminar.datum_begin) < 56, 1, 0) AS fruehbucher,
		IF(DATEDIFF(CURDATE(), seminar.datum_begin) < 0, 0, 1)      AS abgeschlossen,
		kontakt.email                                               AS kontakt_email,
		view_buchung_ansprechpartner.email                          AS ansprechpartner_email

	FROM view_buchung AS buchung
		LEFT JOIN seminar ON buchung.seminar_id = seminar.id
		LEFT JOIN person ON buchung.person_id = person.id
		LEFT JOIN kontakt ON person.kontakt_id = kontakt.id
		LEFT JOIN x_bundesland ON kontakt.bundesland_id = x_bundesland.id
		LEFT JOIN x_land ON kontakt.land_id = x_land.id
		LEFT JOIN view_buchung_ansprechpartner ON view_buchung_ansprechpartner.kontakt_id = kontakt.id
	WHERE buchung.deleted_at = '0000-00-00 00:00:00';


-- buchungen nach monat
-- wird auf der Startseite der Buchungen ausgegeben
-- beruecksichtigt das BUCHUNGSDATUM
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_anzahl_buchungen AS
	SELECT
		id,
		COUNT(id)    AS anzahl,
		YEAR(datum)  AS jahr,
		MONTH(datum) AS monat
	FROM view_buchung
	WHERE datum > '1990-01-01' AND deleted_at = '0000-00-00 00:00:00'
	GROUP BY jahr, monat
	ORDER BY jahr, monat;

-- STATISTIKSEITE

-- buchungen nach status
-- beruecksichtigt das SEMINAR_DATUM
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchung_status AS
	SELECT
		view_buchung.id            AS id,
		view_buchung.rubrik_name   AS rubrik_name,
		MONTH(seminar.datum_begin) AS monat,
		YEAR(seminar.datum_begin)  AS jahr,
		CASE WHEN status = 5 THEN 1
		ELSE 0 END                 AS teilgenommen,
		CASE WHEN status = 4 THEN 1
		ELSE 0 END                 AS abgesagt,
		CASE WHEN status = 3 THEN 1
		ELSE 0 END                 AS umgebucht,
		CASE WHEN status = 2 THEN 1
		ELSE 0 END                 AS storno,
		CASE WHEN status = 1 THEN 1
		ELSE 0 END                 AS bestaetigt,
		0                          AS nichtbestaetigt
	FROM view_buchung
		LEFT JOIN seminar ON (seminar.id = view_buchung.seminar_id)
	WHERE seminar.datum_begin;


-- summe der buchungen - pro monat
-- stats.php
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchung_status_summe AS
	SELECT
		monat,
		jahr,
		id,
		SUM(teilgenommen)    AS teilgenommen,
		SUM(
			abgesagt)        AS abgesagt,
		SUM(
			umgebucht)       AS umgebucht,
		SUM(
			storno)          AS storno,
		SUM(
			bestaetigt)      AS bestaetigt,
		SUM(
			nichtbestaetigt) AS nichtbestaetigt,
		SUM(teilgenommen) + SUM(abgesagt) + SUM(umgebucht) + SUM(storno) + SUM(bestaetigt) + SUM(
			nichtbestaetigt) AS gesamt
	FROM view_buchung_status
	GROUP BY monat, jahr;

-- summe der buchungen pro jahr
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchungen_summe_jahr AS
	SELECT
		jahr,
		id,
		SUM(teilgenommen)    AS teilgenommen,
		SUM(
			abgesagt)        AS abgesagt,
		SUM(
			umgebucht)       AS umgebucht,
		SUM(
			storno)          AS storno,
		SUM(
			bestaetigt)      AS bestaetigt,
		SUM(
			nichtbestaetigt) AS nichtbestaetigt,
		SUM(teilgenommen) + SUM(abgesagt) + SUM(umgebucht) + SUM(storno) + SUM(bestaetigt) + SUM(
			nichtbestaetigt) AS gesamt
	FROM view_buchung_status
	GROUP BY jahr;

-- buchungen der seminare gruppiert nach monat und jahr aufgeteilt nach rubriken
-- beruecksichtigt den termin des seminars
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_belegung AS
	SELECT
		rubrik_name AS name,
		id,
		monat,
		jahr,
		COUNT(id)   AS gesamt
	FROM view_buchung_status
	WHERE teilgenommen = 1 OR bestaetigt = 1
	GROUP BY rubrik_name, jahr, monat
	ORDER BY jahr, monat;


-- summe der seminare nach monat und jahr ohne beruecksichtigung der rubirk
-- beruecksichtigt den termin des seminars
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_belegung_jahr AS
	SELECT
		name,
		id,
		monat,
		jahr,
		SUM(gesamt) AS gesamt
	FROM view_seminar_belegung
	GROUP BY jahr, name;

-- buchungen nach rubriken
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchungen_stats AS
	SELECT
		seminar_art_rubrik.name,
		seminar_art_rubrik.name    AS id,
		MONTH(seminar.datum_begin) AS monat,
		YEAR(seminar.datum_begin)  AS jahr,
		COUNT(buchung.id)          AS buchungen
	FROM `seminar_art_rubrik`
		LEFT JOIN seminar_art ON seminar_art.rubrik = seminar_art_rubrik.id
		LEFT JOIN seminar ON seminar_art.id = seminar.seminar_art_id
		LEFT JOIN buchung ON seminar.id = buchung.seminar_id
	WHERE seminar.storno_datum = "0000-00-00"
	      AND buchung.storno_datum = "0000-00-00"
	      AND buchung.umgebucht_id = 0
	      AND buchung.bestaetigt = 1
	GROUP BY monat, jahr, name;

--  kontakte mit buchungen
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchung_kontakt AS
	SELECT
		STRAIGHT_JOIN
		SQL_CACHE
		buchung.uuid      AS id,
		buchung.id        AS buchung_id,
		person.kontakt_id AS kontakt_id,
		person.id         AS person_id,
		kontakt.firma     AS firma
	FROM person, kontakt, buchung
	WHERE buchung.deleted_at = '0000-00-00 00:00:00'
	      AND buchung.person_id = person.id
	      AND kontakt.id = person.kontakt_id;

-- BUCHUNGEN STATUS
-- 1 bestaetigt
-- 2 storno
-- 3 umgebucht
-- 4 abgesagt
-- 5 nicht teilgenommen
-- preise
CREATE OR REPLACE VIEW view_buchung AS
	SELECT
		buchung.*,
		seminar.seminar_art_id    AS seminar_art_id,
		seminar.kursnr            AS kursnr,
		seminar_art_rubrik.name   AS rubrik_name,
		angelegt_user.name        AS angelegt_von,
		bearbeitet_user.name      AS bearbeitet_von,
		neues_seminar.kursnr      AS umgebucht_auf_kursnr,
		neues_seminar.id          AS umgebucht_auf_id,
		neues_seminar.datum_begin AS umgebucht_auf_datum_begin,
		(CASE
		 WHEN buchung.storno_datum <> '0000-00-00' THEN 2
		 WHEN buchung.teilgenommen <> '0000-00-00' THEN 5
		 WHEN buchung.umgebucht_id != '0' THEN 3
		 WHEN seminar.storno_datum <> '0000-00-00' THEN 4
		 WHEN buchung.bestaetigt = 1 THEN 1
		 ELSE 1
		 END)                     AS status,
		seminar.standort_id       AS standort_id

	FROM buchung

		LEFT JOIN (seminar, seminar_art, seminar_art_rubrik)
			ON (buchung.seminar_id = seminar.id AND seminar.seminar_art_id = seminar_art.id AND
			    seminar_art.rubrik = seminar_art_rubrik.id)
		LEFT JOIN (buchung AS umbuchung, seminar AS neues_seminar)
			ON (buchung.umgebucht_id = umbuchung.id AND umbuchung.seminar_id = neues_seminar.id)
		LEFT JOIN x_user AS angelegt_user
			ON buchung.angelegt_user_id = angelegt_user.id
		LEFT JOIN x_user AS bearbeitet_user
			ON buchung.geaendert_von = bearbeitet_user.id
;


CREATE OR REPLACE VIEW view_buchung_preis AS
	SELECT
		buchung.*,
		kontakt.firma,
		person.vorname,
		person.name,
		kontakt.strasse,
		kontakt.nr,
		kontakt.plz,
		kontakt.ort,
		x_bundesland.name                                           AS bundesland,
		x_land.name                                                 AS land,
		person.ansprechpartner,
		person.grad,
		person.funktion,
		person.strasse                                              AS person_strasse,
		person.nr                                                   AS person_nr,
		person.plz                                                  AS person_plz,
		person.ort                                                  AS person_ort,
		person.tel,
		person.fax,
		person.mobil,
		person.email,
		person.geburtstag,
		person.kontakt_id                                           AS kontakt_id,
		seminar.datum_begin,
		seminar.datum_ende,
		kontakt.vdrk_mitglied                                       AS kontakt_vdrk_mitglied,
		kontakt.dwa_mitglied                                        AS kontakt_dwa_mitglied,
		kontakt.rsv_mitglied                                        AS kontakt_rsv_mitglied,
		kontakt.vdrk_mitglied_nr                                    AS kontakt_vdrk_mitglied_nr,
		CASE WHEN seminar.storno_datum THEN 0.00
		WHEN buchung.storno_datum THEN 0.00
		WHEN buchung.arbeitsagentur THEN 0.00
		WHEN buchung.bildungscheck THEN
			buchung.kursgebuehr * 0.5 + buchung.kosten_verpflegung * DATEDIFF(seminar.datum_ende, seminar.datum_begin) +
			buchung.kosten_unterlagen
		ELSE buchung.kursgebuehr + buchung.kosten_verpflegung * DATEDIFF(seminar.datum_ende, seminar.datum_begin) +
		     buchung.kosten_unterlagen END
		*
		IF(DATEDIFF(buchung.datum, seminar.datum_begin) < 56, 0.95, 1)
		*

		IF(buchung.vdrk_mitglied, 0.95, 1)
		* ((100 - buchung.rabatt) / 100)                            AS preis,

		(1 - (100 - buchung.rabatt) / 100)                          AS rabattInProzent,
		IF(DATEDIFF(buchung.datum, seminar.datum_begin) < 56, 1, 0) AS fruehbucher,
		IF(DATEDIFF(CURDATE(), seminar.datum_begin) < 0, 0, 1)      AS abgeschlossen,
		kontakt.email                                               AS kontakt_email,
		view_buchung_ansprechpartner.email                          AS ansprechpartner_email

	FROM view_buchung AS buchung
		LEFT JOIN seminar ON buchung.seminar_id = seminar.id
		LEFT JOIN person ON buchung.person_id = person.id
		LEFT JOIN kontakt ON person.kontakt_id = kontakt.id
		LEFT JOIN x_bundesland ON kontakt.bundesland_id = x_bundesland.id
		LEFT JOIN x_land ON kontakt.land_id = x_land.id
		LEFT JOIN view_buchung_ansprechpartner ON view_buchung_ansprechpartner.kontakt_id = kontakt.id
	WHERE buchung.deleted_at = '0000-00-00 00:00:00';


-- buchungen nach monat
-- wird auf der Startseite der Buchungen ausgegeben
-- beruecksichtigt das BUCHUNGSDATUM
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_anzahl_buchungen AS
	SELECT
		id,
		COUNT(*)     AS anzahl,
		YEAR(datum)  AS jahr,
		MONTH(datum) AS monat
	FROM view_buchung_preis
	WHERE datum > '01-01-1990'
	GROUP BY jahr, monat
	ORDER BY jahr, monat;

-- STATISTIKSEITE

-- buchungen nach status
-- beruecksichtigt das SEMINAR_DATUM
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchung_status AS
	SELECT
		view_buchung.id            AS id,
		view_buchung.rubrik_name   AS rubrik_name,
		MONTH(seminar.datum_begin) AS monat,
		YEAR(seminar.datum_begin)  AS jahr,
		CASE WHEN status = 5 THEN 1
		ELSE 0 END                 AS teilgenommen,
		CASE WHEN status = 4 THEN 1
		ELSE 0 END                 AS abgesagt,
		CASE WHEN status = 3 THEN 1
		ELSE 0 END                 AS umgebucht,
		CASE WHEN status = 2 THEN 1
		ELSE 0 END                 AS storno,
		CASE WHEN status = 1 THEN 1
		ELSE 0 END                 AS bestaetigt,
		0                          AS nichtbestaetigt
	FROM view_buchung
		LEFT JOIN seminar ON (seminar.id = view_buchung.seminar_id)
	WHERE seminar.datum_begin;


-- summe der buchungen - pro monat
-- stats.php
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchung_status_summe AS
	SELECT
		monat,
		jahr,
		id,
		SUM(teilgenommen)    AS teilgenommen,
		SUM(
			abgesagt)        AS abgesagt,
		SUM(
			umgebucht)       AS umgebucht,
		SUM(
			storno)          AS storno,
		SUM(
			bestaetigt)      AS bestaetigt,
		SUM(
			nichtbestaetigt) AS nichtbestaetigt,
		SUM(teilgenommen) + SUM(abgesagt) + SUM(umgebucht) + SUM(storno) + SUM(bestaetigt) + SUM(
			nichtbestaetigt) AS gesamt
	FROM view_buchung_status
	GROUP BY monat, jahr;

-- summe der buchungen pro jahr
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchungen_summe_jahr AS
	SELECT
		jahr,
		id,
		SUM(teilgenommen)    AS teilgenommen,
		SUM(
			abgesagt)        AS abgesagt,
		SUM(
			umgebucht)       AS umgebucht,
		SUM(
			storno)          AS storno,
		SUM(
			bestaetigt)      AS bestaetigt,
		SUM(
			nichtbestaetigt) AS nichtbestaetigt,
		SUM(teilgenommen) + SUM(abgesagt) + SUM(umgebucht) + SUM(storno) + SUM(bestaetigt) + SUM(
			nichtbestaetigt) AS gesamt
	FROM view_buchung_status
	GROUP BY jahr;

-- buchungen der seminare gruppiert nach monat und jahr aufgeteilt nach rubriken
-- beruecksichtigt den termin des seminars
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_belegung AS
	SELECT
		rubrik_name AS name,
		id,
		monat,
		jahr,
		COUNT(id)   AS gesamt
	FROM view_buchung_status
	WHERE teilgenommen = 1 OR bestaetigt = 1
	GROUP BY rubrik_name, jahr, monat
	ORDER BY jahr, monat;


-- summe der seminare nach monat und jahr ohne beruecksichtigung der rubirk
-- beruecksichtigt den termin des seminars
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_belegung_jahr AS
	SELECT
		name,
		id,
		monat,
		jahr,
		SUM(gesamt) AS gesamt
	FROM view_seminar_belegung
	GROUP BY jahr, name;

-- buchungen nach rubriken
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchungen_stats AS
	SELECT
		seminar_art_rubrik.name,
		seminar_art_rubrik.name    AS id,
		MONTH(seminar.datum_begin) AS monat,
		YEAR(seminar.datum_begin)  AS jahr,
		COUNT(buchung.id)          AS buchungen
	FROM `seminar_art_rubrik`
		LEFT JOIN seminar_art ON seminar_art.rubrik = seminar_art_rubrik.id
		LEFT JOIN seminar ON seminar_art.id = seminar.seminar_art_id
		LEFT JOIN buchung ON seminar.id = buchung.seminar_id
	WHERE seminar.storno_datum = "0000-00-00"
	      AND buchung.storno_datum = "0000-00-00"
	      AND buchung.umgebucht_id = 0
	      AND buchung.bestaetigt = 1
	GROUP BY monat, jahr, name;

--  kontakte mit buchungen
-- und details die in view_buchung_kontakt_ansprechpoartner benoetigt werden
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchung_kontakt AS
	SELECT
		STRAIGHT_JOIN
		SQL_CACHE
		buchung.uuid           AS id,
		buchung.id             AS buchung_id,
		person.kontakt_id      AS kontakt_id,
		person.id              AS person_id,
		kontakt.firma          AS firma,
		kontakt.strasse        AS firma_strasse,
		kontakt.ort            AS firma_ort,
		kontakt.plz            AS firma_plz,
		buchung.datum          AS buchungsdatum,
		seminar.seminar_art_id AS seminar_art_id,
		seminar.datum_begin    AS seminar_datum,
		seminar.kursnr         AS kursnr,
		(CASE
		 WHEN buchung.storno_datum <> '0000-00-00' THEN 2
		 WHEN buchung.teilgenommen <> '0000-00-00' THEN 5
		 WHEN buchung.umgebucht_id != '0' THEN 3
		 WHEN seminar.storno_datum <> '0000-00-00' THEN 4
		 WHEN buchung.bestaetigt = 1 THEN 1
		 ELSE 1
		 END)                  AS buchung_status
	FROM person, kontakt, buchung, seminar
	WHERE buchung.deleted_at = '0000-00-00 00:00:00'
	      AND buchung.person_id = person.id
	      AND kontakt.id = person.kontakt_id
	      AND buchung.seminar_id = seminar.id;


-- buchungen mit ansprechpartnern
-- achtung der private kontakt ist hier fest eingetragen
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_buchung_ansprechpartner AS
	SELECT
		view_buchung_kontakt.id AS id,
		-- achtung das ist die uuid
		view_buchung_kontakt.buchung_id AS buchung_id,
		(CASE
		 WHEN person.geschlecht = 0 THEN "Frau"
		 ELSE "Herr"
		 END)                           AS anrede,
		person.grad,
		person.email,
		person.name                     AS name,
		person.vorname                  AS vorname,
		view_buchung_kontakt.kontakt_id,
		view_buchung_kontakt.person_id,
		view_buchung_kontakt.firma,
		view_buchung_kontakt.firma_ort,
		view_buchung_kontakt.firma_strasse,
		view_buchung_kontakt.firma_plz,
		view_buchung_kontakt.buchungsdatum,
		view_buchung_kontakt.seminar_art_id,
		view_buchung_kontakt.seminar_datum,
		view_buchung_kontakt.kursnr
	FROM view_buchung_kontakt, person
	WHERE (view_buchung_kontakt.kontakt_id <> 1
	       AND view_buchung_kontakt.kontakt_id = person.kontakt_id
	       AND person.ansprechpartner <> 0) OR (view_buchung_kontakt.kontakt_id = 1
	                                            AND view_buchung_kontakt.person_id = person.id)
	                                           AND view_buchung_kontakt.id <> ""
	GROUP BY view_buchung_kontakt.id;
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
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_seminar_referent AS
SELECT seminar_referent.seminar_id as id,
seminar_referent.tag as tag,
GROUP_CONCAT(referent.kuerzel SEPARATOR ',') as kuerzel,
seminar_referent.standort_id as standort_id
FROM seminar_referent
JOIN referent ON referent.id = seminar_referent.referent_id
GROUP BY seminar_referent.seminar_id, seminar_referent.tag, seminar_referent.standort_id
ORDER BY referent.kuerzel;

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
seminar_art_referent.standort_id = standort.id;-- seminar preise
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
GROUP BY seminar.seminar_art_id, kontakt.id;CREATE OR REPLACE VIEW view_inhouse_seminar AS SELECT
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
SET NAMES "utf8";
-- werbe empfaenger, UNION
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_werbe_empfaenger AS ( SELECT
kontakt.id as id,
kontakt.id as kontakt_id,
person.id as person_id,
kontakt.firma as firma,
kontakt.tel as tel,
person.vorname as vorname,
person.name as name,
kontakt.email as email,
kontakt.strasse as strasse,
kontakt.nr as nr,
kontakt.ort as ort,
kontakt.plz as plz,
kontakt.newsletter as newsletter,
kontakt.kontaktkategorie as kategorie_id,
kontakt.branche_id as branche_id,
kontakt.taetigkeitsbereich_id as taetigkeitsbereich_id,
x_branche.name as branche,
x_taetigkeitsbereich.name as taetigkeitsbereich,
kontakt_kategorie.name as kategorie,
'kontakt' as quelle,
opengeodb_plz.x as x,
 opengeodb_plz.y as y,
 opengeodb_plz.z as z
FROM kontakt
LEFT JOIN kontakt_kategorie ON ( kontakt_kategorie.id = kontakt.kontaktkategorie  )
LEFT JOIN person ON (person.kontakt_id = kontakt.id )
LEFT JOIN opengeodb_plz ON (  opengeodb_plz.plz = kontakt.plz )
LEFT JOIN x_branche ON (kontakt.branche_id = x_branche.id)
LEFT JOIN x_taetigkeitsbereich ON ( kontakt.taetigkeitsbereich_id = x_taetigkeitsbereich.id )
WHERE person.ansprechpartner = 1  )
UNION ( SELECT
kontakt.id as id,
kontakt.id as kontakt_id,
person.id as person_id,
kontakt.firma as firma,
person.tel as tel,
person.vorname as vorname,
person.name as name,
kontakt.email as email,
kontakt.strasse as strasse,
kontakt.nr as nr,
kontakt.ort as ort,
kontakt.plz as plz,
kontakt.newsletter as newsletter,
kontakt.kontaktkategorie as kategorie_id,
kontakt.branche_id as branche_id,
kontakt.taetigkeitsbereich_id as taetigkeitsbereich_id,
x_branche.name as branche,
x_taetigkeitsbereich.name as taetigkeitsbereich,
kontakt_kategorie.name as kategorie,
'akquise_kontakt' as quelle,
opengeodb_plz.x as x,
 opengeodb_plz.y as y,
 opengeodb_plz.z as z
FROM kontakt
JOIN kontakt_kategorie ON ( kontakt_kategorie.id = kontakt.kontaktkategorie  )
JOIN person ON (person.kontakt_id = kontakt.id )
JOIN opengeodb_plz ON (  opengeodb_plz.plz = kontakt.plz )
JOIN x_branche ON (kontakt.branche_id = x_branche.id)
JOIN x_taetigkeitsbereich ON ( kontakt.taetigkeitsbereich_id = x_taetigkeitsbereich.id )
WHERE person.ansprechpartner = 1)
UNION ( SELECT
person.id as id,
kontakt.id as kontakt_id,
person.id as person_id,
kontakt.firma as firma,
kontakt.tel as tel,
person.vorname as vorname,
person.name as name,
person.email as email,
kontakt.strasse as strasse, -- war mal person, ggf. noch ergänzen
kontakt.nr as nr,-- war mal person, ggf. noch ergänzen
kontakt.ort as ort,-- war mal person, ggf. noch ergänzen
kontakt.plz as plz,-- war mal person, ggf. noch ergänzen
person.newsletter as newsletter,
-1 as kategorie_id,
-1 as branche_id,
-1 as taetigkeitsbereich_id,
'person' as branche,
'person' as kategorie,
'person' as taetigkeitsbereich,
'person' as quelle,
opengeodb_plz.x as x,
 opengeodb_plz.y as y,
 opengeodb_plz.z as z
FROM person
JOIN kontakt ON (   kontakt.id = person.kontakt_id  )
JOIN opengeodb_plz ON ( opengeodb_plz.plz = person.plz )
);

-- standort mit geo db verbindne
CREATE OR REPLACE ALGORITHM = MERGE VIEW view_standort_koordinaten AS SELECT
standort.plz as standort_plz,
 standort.id as id,
standort.name as standort_name,
 opengeodb_plz.x as x,
 opengeodb_plz.y as y,
 opengeodb_plz.z as z,
 opengeodb_plz.lon as lon,
opengeodb_plz.lat as lat FROM standort, opengeodb_plz WHERE opengeodb_plz.plz = standort.plz;-- kontakte user stats
CREATE OR REPLACE VIEW view_user_kontakt AS
SELECT CONCAT(x_user.id, YEAR(angelegt_datum), MONTH(angelegt_datum)) as id,
x_user.vorname as user_vorname,
x_user.nachname as nachname,
YEAR ( angelegt_datum ) as jahr ,
MONTH (angelegt_datum) as monat,
COUNT(*) as anzahl
FROM `kontakt`
LEFT JOIN x_user ON( angelegt_user_id = x_user.id )
GROUP BY jahr, monat, x_user.id;

-- akquise user stats
CREATE OR REPLACE VIEW view_user_akquise_kontakt AS
SELECT CONCAT(x_user.id, YEAR(angelegt_datum), MONTH(angelegt_datum)) as id,
x_user.vorname as user_vorname,
 x_user.nachname as nachname,
  YEAR (angelegt_datum ) as jahr ,
   MONTH ( angelegt_datum) as monat,
    COUNT(*) as anzahl FROM `kontakt`
LEFT JOIN x_user ON( angelegt_user_id = x_user.id )
WHERE kontext='Akquise'

GROUP BY jahr,monat, x_user.id;


-- view zum herausfinden von mehrfacheinträgen pro Referent für Termine
-- hit > 1 wenn treffer
CREATE OR REPLACE VIEW check_seminar_referent AS
SELECT *, COUNT(id) as ref_count  FROM `seminar_referent` 
WHERE referent_id != 0 AND referent_id != -1
GROUP BY seminar_id, tag,standort_id, theorie, praxis, start_stunde, start_minute, start_praxis_stunde, start_praxis_minute, referent_id
HAVING COUNT(id) > 1;


-- view zum herausfinden von mehrfacheinträgen pro Referent für Seminare
-- hit > 1 wenn treffer
CREATE OR REPLACE VIEW check_seminar_art_referent AS
SELECT *, COUNT(id) as ref_count  FROM `seminar_art_referent` 
WHERE referent_id != 0 AND referent_id != -1
GROUP BY seminar_id, tag,standort_id, theorie, praxis, start_stunde, start_minute, start_praxis_stunde, start_praxis_minute, referent_id
HAVING COUNT(id) > 1;

-- Kontakte zur weiteren verarbeitung

CREATE OR REPLACE VIEW view_kontakt_export AS
SELECT
  * ,
  x_taetigkeitsbereich.name AS taetigkeitsbereich,
  x_branche.name AS branche,
  kontakt_kategorie.name AS kategorie,
  x_land.name AS land,
  x_bundesland.name AS bundesland,
  kontakt_quelle.name AS kontaktquellename,
  x_user.vorname AS angelegt_vorname,
  x_user.nachname AS angelegt_nachname
FROM `kontakt`
  LEFT JOIN kontakt_kategorie ON ( kontakt.kontaktkategorie = kontakt_kategorie.id )
  LEFT JOIN x_branche ON ( kontakt.branche_id = x_branche.id )
  LEFT JOIN x_taetigkeitsbereich ON ( kontakt.taetigkeitsbereich_id = x_taetigkeitsbereich.id )
  LEFT JOIN x_land ON ( kontakt.land_id = x_land.id )
  LEFT JOIN x_bundesland ON ( kontakt.bundesland_id = x_bundesland.id )
  LEFT JOIN kontakt_quelle ON ( kontakt.kontakt_quelle = kontakt_quelle.id )
  LEFT JOIN x_user ON ( kontakt.angelegt_user_id = x_user.id );
-- Mysql sort order and optimisation

ALTER TABLE `buchung`  ORDER BY `datum`;
ALTER TABLE `akquise_kontakt`  ORDER BY `firma`;
ALTER TABLE `hotel`  ORDER BY `name`;
ALTER TABLE `hotel_preis`  ORDER BY `datum_start`;
ALTER TABLE `kontakt`  ORDER BY `firma`;
ALTER TABLE `kontakt_kategorie`  ORDER BY `name`;
ALTER TABLE `kontakt_quelle`  ORDER BY `name`;
ALTER TABLE `person`  ORDER BY `name`;
ALTER TABLE `referent`  ORDER BY `name`;
ALTER TABLE `seminar`  ORDER BY `datum_begin`;
ALTER TABLE `seminar_art`  ORDER BY `id`;
ALTER TABLE  `standort` ORDER BY  sortierung, name;

-- optimizsation
OPTIMIZE TABLE `person`;
OPTIMIZE TABLE `seminar`;
OPTIMIZE TABLE `seminar_art`;
OPTIMIZE TABLE `standort`;
OPTIMIZE TABLE `akquise_kontakt`;
OPTIMIZE TABLE `kontakt`;

UPDATE person SET newsletter = 0 WHERE newsletter_abmeldedatum > newsletter_anmeldedatum;
UPDATE kontakt SET newsletter = 0 WHERE newsletter_abmeldedatum > newsletter_anmeldedatum;
UPDATE akquise_kontakt SET newsletter = 0 WHERE abmelde_datum > anmelde_datum;

-- sortieren nach standort und datum_begin
ALTER TABLE  `sag-akademie_de_stable`.`seminar` ADD INDEX  `sortieren nach datum, standort` (  `datum_begin` ,  `standort_id` );

-- sortieren zwischen datum_start und datum_ende
ALTER TABLE  `sag-akademie_de_stable`.`seminar` ADD INDEX `suche nach datum_begin und datum_ende` (  `datum_begin` ,  `datum_ende` );

--Nicht gelpeschte buchungen mit firmen
ALTER TABLE  `dev_sagakademie`.`buchung` ADD INDEX  `person nicht geloescht` (  `person_id` ,  `deleted_at` )

ALTER TABLE  `sag-akademie_de_stable`.`person` ADD INDEX  `kontakt_id` (  `kontakt_id` ,  `id` )