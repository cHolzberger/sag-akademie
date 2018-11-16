SELECT `sag-akademie_de_stable`.seminar.kursnr,
`sag-akademie_de_test`.seminar.freigabe_status as status_alt,
`sag-akademie_de_stable`.seminar.freigabe_status AS status_neu
from `sag-akademie_de_test`.seminar
JOIN `sag-akademie_de_stable`.seminar ON (`sag-akademie_de_test`.seminar.id = `sag-akademie_de_stable`.seminar.id)
WHERE `sag-akademie_de_test`.seminar.freigabe_status <> `sag-akademie_de_stable`.seminar.freigabe_status
