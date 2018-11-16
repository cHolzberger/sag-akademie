-- alle 2011 mit detail info
select * from view_seminar_preis where freigabe_veroeffentlichen=1 AND (YEAR(datum_begin) = 2011 OR YEAR(datum_ende) = 2011 );

-- alle ab jetzt bis ende 2011 mit detail info
select * from view_seminar_preis where freigabe_veroeffentlichen=1 AND (YEAR(datum_begin) = 2011 OR YEAR(datum_ende) = 2011 ) AND datum_begin > NOW();