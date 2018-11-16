CREATE OR REPLACE ALGORITHM = MERGE VIEW view_anzahl_buchungen AS
	SELECT
		id,
		COUNT(id)    AS anzahl,
		YEAR(datum)  AS jahr,
		MONTH(datum) AS monat
	FROM buchung
	WHERE datum > '1990-01-01' AND deleted_at = '0000-00-00 00:00:00'
	GROUP BY jahr, monat
	ORDER BY jahr, monat;