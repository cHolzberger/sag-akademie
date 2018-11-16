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
SELECT id, kontakt_id, name, vorname, GROUP_CONCAT(CONVERT(id, CHAR(8)) SEPARATOR ";") AS duplikate FROM `person` GROUP BY kontakt_id, name, vorname, ort, plz,geburtstag HAVING count(*) > 1;