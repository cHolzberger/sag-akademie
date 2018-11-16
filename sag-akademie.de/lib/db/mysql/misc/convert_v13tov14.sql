-- update der seminar arten
UPDATE seminar_art set kursgebuehr = nettoep WHERE 1=1;

-- hotel preise aus der hotel tabelle generieren
INSERT INTO hotel_preis (datum_start, datum_ende, hotel_id, zimmerpreis_ez, zimmerpreis_dz)
SELECT "0000-00-00", "0000-00-00", id, verkaufspreisEz, verkaufspreisDz FROM hotel
WHERE 1=1;

-- seminar preise aktualisiren
UPDATE seminar, seminar_art SET 
seminar.kosten_verpflegung = seminar_art.kosten_verpflegung,
seminar.kosten_unterlagen = seminar_art.kosten_unterlagen,
seminar.kursgebuehr = seminar_art.kursgebuehr 
WHERE seminar.seminarArtID = seminar_art.id; 

-- buchung preise aktualisieren
UPDATE buchung, seminar SET 
buchung.kosten_verpflegung = seminar.kosten_verpflegung,
buchung.kosten_unterlagen = seminar.kosten_unterlagen,
buchung.kursgebuehr = seminar.kursgebuehr 
WHERE buchung.seminar_id = seminar.id; 