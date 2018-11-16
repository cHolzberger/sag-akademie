
-- AKQUISE KONTAKTE ETC
update akquise_kontakt set kontaktkategorie = 4 WHERE branche LIKE "Ing.-Büro"
OR branche LIKE "Ingenieurbüro";

UPDATE akquise_kontakt set kontaktkategorie = 2 WHERE branche LIKE "Kanaltechnik"
OR branche LIKE "Kläranlage"
OR branche LIKE "Kommune"
OR branche LIKE "Städtereinigung"
OR branche LIKE "Städtereinigungsunternehmen";

UPDATE akquise_kontakt set kontaktkategorie = 3 WHERE branche LIKE "Verband";


UPDATE akquise_kontakt set branche_id = 7 WHERE branche LIKE "Abwasser"
OR branche LIKE "Abwasserreinigungsunternehmen"
OR branche LIKE "Wassertechnik";


UPDATE akquise_kontakt set branche_id = 11 WHERE branche LIKE "berufliche Weiterbildung";

UPDATE akquise_kontakt set branche_id = 12 WHERE branche LIKE "Gas- und Sanitärinstallateure"
OR branche LIKE "Heizungs-, Klima-, Lüftungs- und Sanitärkundendienste";

UPDATE akquise_kontakt set branche_id = 6 WHERE branche LIKE "Gewerbe";

UPDATE akquise_kontakt set branche_id = 13 WHERE branche LIKE "Hersteller"
OR branche LIKE "Hersteller TV-Anlagen";

UPDATE akquise_kontakt set branche_id = 2 WHERE branche  LIKE "Kanalbauunternehmen"
OR branche LIKE "Kanalisationsbauunternehme"
OR branche LIKE "Kanalisationsbauunternehmen"
OR branche LIKE "Kanalisiationsbauunternehmen";

UPDATE akquise_kontakt set branche_id=1 WHERE branche LIKE "Kanalreinigungsunternehmen"
OR branche LIKE "Kanalreinigungsunternhehmen"
OR branche LIKE "Kanalsanierung"
OR branche LIKE "Kanalsanierungsunternehmen"
OR branche LIKE "Kanaltechnik"
OR branche LIKE "Rohr- und Kanalreinigung"
OR branche LIKE "Rohr- und Kanaltechnik"
OR branche LIKE "Rohrreinigungsunternehmen"
OR branche LIKE "Rohrreiningungsunternehmen";

UPDATE akquise_kontakt set branche_id=14 WHERE branche LIKE "Tiefbau%";

-- KONTAKTE
UPDATE kontakt set kontaktkategorie=4 WHERE branche LIKE "Ingenie%";

UPDATE kontakt set kontaktkategorie=2 WHERE branche LIKE "Kläranlage"
OR branche LIKE "Kommu%"
OR branche LIKE "Städterein%";


UPDATE kontakt set branche_id=7 WHERE branche LIKE "Abwasser%";

UPDATE kontakt set branche_id=14 WHERE branche LIKE "Bau"
OR branche LIKE "Hoch%"
OR branche LIKE "Bagger%"
OR branche LIKE "Gala%"
OR branche LIKE "Garten%"
OR branche LIKE "Straßen%"
OR branche LIKE "Tief%";

UPDATE kontakt set branche_id=11 WHERE branche LIKE "Berufsausbildung";

UPDATE kontakt set branche_id=6 WHERE branche LIKE "Dienst%";

UPDATE kontakt set branche_id=1 WHERE branche LIKE "%Rohr%";

UPDATE kontakt set branche_id=15 WHERE branche LIKE "Entsorgung%"
OR branche LIKE "Umwelt"
OR branche LIKE "Ver%"
OR branche LIKE "Wasser%";

UPDATE kontakt set branche_id=12 WHERE branche LIKE "Gas%"
OR branche LIKE "Grundst%"
OR branche LIKE "%Heizung%";

UPDATE kontakt set branche_id=13 WHERE branche LIKE "Hersteller";

UPDATE kontakt set branche_id = 2 WHERE branche  LIKE "Kanalbauunternehmen"
OR branche LIKE "Kanalisationsbauunternehme"
OR branche LIKE "Kanalisationsbauunternehmen"
OR branche LIKE "Kanalisiationsbauunternehmen";

UPDATE kontakt set branche_id=1 WHERE branche LIKE "Kanalreinigungsunternehmen"
OR branche LIKE "Kanalreinigung%"
OR branche LIKE "Kanalrei%"
OR branche LIKE "Kanalreinigungsunternhehmen"
OR branche LIKE "Kanalsanierung"
OR branche LIKE "Kanalsanierungsunternehmen"
OR branche LIKE "Kanaltechnik"
OR branche LIKE "Rohr- und Kanalreinigung"
OR branche LIKE "Rohr- und Kanaltechnik"
OR branche LIKE "Rohrreinigungsunternehmen"
OR branche LIKE "Rohrreiningungsunternehmen";