-- newsletter emfpaenger darmstadt umkreis 200km
CREATE OR REPLACE VIEW view_newsletter_da_umkreis_200km AS SELECT
* from view_newsletter_empfaenger WHERE plz in (SELECT plz from da_umkreis_200km) AND ( quelle = "kontakt" OR quelle = 'akquise_kontakt');

-- vergebene kursnummer markieren
-- UPDATE seminar set kursnr_vergeben=1 WHERE kursnr NOT LIKE "#PL#%";

-- mailing import
--CREATE OR REPLACE VIEW view_mailing_import AS
--SELECT * FROM mailing_kontakt
--WHERE mailing_kontakt.email
--NOT IN (SELECT email from akquise_kontakt)
--AND mailing_kontakt.email
--NOT in (SELECT email from person)
--AND mailing_kontakt.email
--NOT in (select email from kontakt)
--AND (mailing_kontakt.status = 1 or mailing_kontakt.status=0;
