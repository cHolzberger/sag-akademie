-- kontakte user stats
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

