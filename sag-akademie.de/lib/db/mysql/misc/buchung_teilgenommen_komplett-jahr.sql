select seminar_art.id as seminar_art,
  standort.name as standort,
  seminar.kursnr as kursnr,
  buchung.datum as buchung_datum,
  kontakt.firma as firma_name,
  kontakt.strasse as firma_strasse,
  kontakt.plz as firma_plz,
  kontakt.ort as firma_ort,

  kontakt.tel as firma_tel,
  kontakt.email as firma_email,
  kontakt.fax as firma_fax,
  kontakt.land as firma_land,
  kontakt.bundesland as firma_bundesland,
  kontakt.newsletter as firma_newsletter,

  person.*,
  ansprechpartner.name AS ansprechpartner_name,
  ansprechpartner.vorname AS ansprechpartner_vorname,
  ansprechpartner.tel AS ansprechpartner_tel
  FROM seminar_art
  LEFT JOIN seminar ON (seminar.seminar_art_id = seminar_art.id )
  LEFT JOIN view_buchung as buchung ON ( buchung.seminar_id = seminar.id )
  LEFT JOIN person ON ( buchung.person_id = person.id )
  LEFT JOIN kontakt ON ( person.kontakt_id = kontakt.id )
  LEFT JOIN standort ON ( seminar.standort_id = standort.id )
  LEFT JOIN person as ansprechpartner ON ( ansprechpartner.kontakt_id = kontakt.id AND ansprechpartner.ansprechpartner = 1 )
WHERE (buchung.status = 1 OR buchung.status = 5) AND YEAR(seminar.datum_begin) = 2016 AND seminar_art.id='DI-SK'