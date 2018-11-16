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
opengeodb_plz.lat as lat FROM standort, opengeodb_plz WHERE opengeodb_plz.plz = standort.plz;