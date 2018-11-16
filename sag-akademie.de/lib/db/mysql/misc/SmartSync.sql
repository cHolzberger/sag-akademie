-- Kontakte die keine empfänger mehr sind
SELECT mailing_kontakt.*, kontakt.email AS email_2
FROM `mailing_kontakt`, kontakt
WHERE mailing_kontakt.email = kontakt.email
AND mailing_kontakt.email <> ""
AND ( mailing_kontakt.status = 2
OR mailing_kontakt.status = 5);

SELECT mailing_kontakt.*, kontakt.email AS email_2
FROM `mailing_kontakt`, kontakt
WHERE mailing_kontakt.email = kontakt.email
AND mailing_kontakt.email <> ""
AND ( mailing_kontakt.status = 1
OR mailing_kontakt.status = 0);

-- kontakte aktualisieren
UPDATE `mailing_kontakt`, kontakt
SET kontakt.newsletter_abmeldedatum = NOW(),
kontakt.newsletter = 0
WHERE mailing_kontakt.email = kontakt.email
AND mailing_kontakt.email <> ""
AND ( mailing_kontakt.status = 2
OR mailing_kontakt.status = 5);

UPDATE `mailing_kontakt`, kontakt
SET kontakt.newsletter_anmeldedatum = NOW(),
kontakt.newsletter = 1
WHERE mailing_kontakt.email = kontakt.email
AND mailing_kontakt.email <> ""
AND ( mailing_kontakt.status = 1
OR mailing_kontakt.status = 0);

-- akquise aktualisieren
UPDATE `mailing_kontakt`, akquise_kontakt
SET akquise_kontakt.abmelde_datum = NOW(),
akquise_kontakt.newsletter = 0
WHERE mailing_kontakt.email = akquise_kontakt.email
AND mailing_kontakt.email <> ""
AND ( mailing_kontakt.status = 2
OR mailing_kontakt.status = 5);

UPDATE `mailing_kontakt`, akquise_kontakt
SET akquise_kontakt.anmelde_datum = NOW(),
akquise_kontakt.newsletter = 1
WHERE mailing_kontakt.email = akquise_kontakt.email
AND mailing_kontakt.email <> ""
AND ( mailing_kontakt.status = 1
OR mailing_kontakt.status = 0);

-- personen aktualisieren
UPDATE `mailing_kontakt`, person
SET person.newsletter_abmeldedatum = NOW(),
person.newsletter = 0
WHERE mailing_kontakt.email = person.email
AND mailing_kontakt.email <> ""
AND ( mailing_kontakt.status = 2
OR mailing_kontakt.status = 5);

UPDATE `mailing_kontakt`, person
SET person.newsletter_anmeldedatum = NOW(),
person.newsletter = 1
WHERE mailing_kontakt.email = person.email
AND mailing_kontakt.email <> ""
AND ( mailing_kontakt.status = 1
OR mailing_kontakt.status = 0);
