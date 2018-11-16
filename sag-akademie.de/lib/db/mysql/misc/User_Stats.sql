-- alle kontakte inkl user
SELECT kontakt.*, x_user.vorname as user_vorname, x_user.nachname as nachname, YEAR ( angelegt_datum ) as jahr, MONTH (angelegt_datum) as monat FROM `kontakt` 
LEFT JOIN x_user ON( angelegt_user_id = x_user.id )
WHERE 1;

-- alle akquise-kontakte inkl user
SELECT akquise_kontakt.*, x_user.vorname as user_vorname, x_user.nachname as nachname, YEAR ( angelegt_datum ) as jahr, MONTH ( angelegt_datum) as monat FROM `akquise_kontakt` 
LEFT JOIN x_user ON( angelegt_user_id = x_user.id )
WHERE 1;

-- kontakte user stats
SELECT x_user.vorname as user_vorname, x_user.nachname as nachname, YEAR ( angelegt_datum ) as jahr , MONTH (angelegt_datum) as monat, COUNT(*) as anzahl FROM `kontakt` 
LEFT JOIN x_user ON( angelegt_user_id = x_user.id )
GROUP BY jahr, monat, x_user.id;

-- akquise user stats
SELECT x_user.vorname as user_vorname, x_user.nachname as nachname, YEAR (angelegt_datum ) as jahr , MONTH ( angelegt_datum) as monat, COUNT(*) as anzahl FROM `akquise_kontakt` 
LEFT JOIN x_user ON( angelegt_user_id = x_user.id )
GROUP BY jahr,monat, x_user.id;

