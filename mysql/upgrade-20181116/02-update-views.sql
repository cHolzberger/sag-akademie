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
	FROM buchung
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


/*
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
*/
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

--  kontakte mit buchungen
 CREATE OR REPLACE VIEW `view_buchung_kontakt` AS 
 select straight_join sql_cache 
 `buchung`.`uuid` AS `id`,`buchung`.`id` AS `buchung_id`,`person`.`kontakt_id` AS `kontakt_id`,`person`.`id` AS `person_id`,`kontakt`.`firma` AS `firma`,`kontakt`.`strasse` AS `firma_strasse`,`kontakt`.`ort` AS `firma_ort`,`kontakt`.`plz` AS `firma_plz`,`buchung`.`datum` AS `buchungsdatum`,`seminar`.`seminar_art_id` AS `seminar_art_id`,`seminar`.`datum_begin` AS `seminar_datum`,`seminar`.`kursnr` AS `kursnr`,(case when (`buchung`.`storno_datum` <> '0000-00-00') then 2 when (`buchung`.`teilgenommen` <> '0000-00-00') then 5 when (`buchung`.`umgebucht_id` <> '0') then 3 when (`seminar`.`storno_datum` <> '0000-00-00') then 4 when (`buchung`.`bestaetigt` = 1) then 1 else 1 end) AS `buchung_status` from (((`person` join `kontakt`) join `buchung`) join `seminar`) where ((`buchung`.`deleted_at` = '0000-00-00 00:00:00') and (`buchung`.`person_id` = `person`.`id`) and (`kontakt`.`id` = `person`.`kontakt_id`) and (`buchung`.`seminar_id` = `seminar`.`id`));

-- buchungen mit ansprechpartnern
-- achtung der private kontakt ist hier fest eingetragen
CREATE OR REPLACE VIEW view_buchung_ansprechpartner AS
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
