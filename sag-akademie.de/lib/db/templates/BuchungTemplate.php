<?php
class BuchungListener extends Doctrine_Record_Listener
{
    private function _clearCache ($event) {
		$table = get_class($event->getInvoker());
		$id = $event->getInvoker()->id;
		$data = $event->getInvoker()->toArray();

		clearCache($table, $data, $id);	
	}

    public function preInsert(Doctrine_Event $event)
    {
        global $identity;
        $this->_clearCache($event);

        if (empty ($event->getInvoker()->datum)) {
            $event->getInvoker()->datum = currentMysqlDatetime();
        }

        if (is_object($identity)) {
            $event->getInvoker()->angelegt_user_id = $identity->uid();
            $event->getInvoker()->angelegt_datum = currentMysqlDatetime();
            $event->getInvoker()->geaendert_von = $identity->uid();
            $event->getInvoker()->geaendert = currentMysqlDatetime();
        }
    }

    public function preUpdate(Doctrine_Event $event)
    {
        global $identity;
        //$event->getInvoker()->updated = date('Y-m-d', time());
        $mod = $event->getInvoker()->getModified();
        $this->_clearCache($event);

        if (is_object($identity)) {
            $event->getInvoker()->geaendert_von = $identity->uid();
            $event->getInvoker()->geaendert = currentMysqlDatetime();
        }


    }

}

class BuchungTemplate extends Doctrine_Template
{
    public function setUp()
    {
        $this->hasOne('Seminar', array(
                'local' => 'seminar_id',
                'foreign' => 'id',
                'owningSide' => true
            )
        );

        $this->hasOne('Seminar as UmgebuchtSeminar', array(
                'local' => 'umgebucht_id',
                'foreign' => 'id',
                'owningSide' => true
            )
        );

        $this->hasOne('Person', array(
                'local' => 'person_id',
                'foreign' => 'id'
            )
        );

        $this->hasOne('HotelBuchung as NHotelBuchung', array(
                'local' => 'id',
                'foreign' => 'buchung_id',
            )
        );

        $this->hasOne('ViewHotelBuchungPreis as HotelBuchung', array(
                'local' => 'id',
                'foreign' => 'buchung_id',
            )
        );

        $this->hasOne('XUser as AngelegtVon', array(
                'local' => 'angelegt_user_id',
                'foreign' => 'id',
            )
        );
        $this->hasOne('XUser as GeaendertVon', array(
                'local' => 'geaendert_von',
                'foreign' => 'id',
            )
        );

        $this->actAs("ChangeCounted");
        $this->actAs("ChangeLogged");

        $this->addListener(new BuchungListener());

    }

    public function countByDateTableProxy($mysqlStartDate, $mysqlEndDate)
    {
        $q = Doctrine_Query::create();

        $q->from("ViewBuchung buchung")
            ->select("buchung.id")
            ->where("buchung.datum < ?", $mysqlEndDate)
            ->andWhere("buchung.datum > ?", $mysqlStartDate)
            ->andWhere("buchung.status = ?", 1)
            ->andWhere("buchung.deleted_at = '0000-00-00 00:00:00'")
            ->andWhere("alte_buchung = ?", 0);


        return $q->count();
    }

    public function countStornoByDateTableProxy($mysqlStartDate, $mysqlEndDate)
    {
        $q = Doctrine_Query::create();
        $q->from("ViewBuchungPreis buchung")
            ->select("buchung.id")
            ->where("buchung.datum < ?", $mysqlEndDate)
            ->andWhere("buchung.datum > ?", $mysqlStartDate)
            ->andWhere("buchung.status = ?", 2)
            ->andWhere("buchung.deleted_at = '0000-00-00 00:00:00'");

        return $q->count();
    }

    public function countUmbuchungenByDateTableProxy($mysqlStartDate, $mysqlEndDate)
    {
        $q = Doctrine_Query::create();
        $q->from("ViewBuchung buchung")
            ->select("buchung.id")
            ->where("buchung.datum < ?", $mysqlEndDate)
            ->andWhere("buchung.datum > ?", $mysqlStartDate)
            ->andWhere("buchung.status = ?", 3)
            ->andWhere("buchung.deleted_at = '0000-00-00 00:00:00'");

        return $q->count();
    }

    //trash
    public function trashTableProxy()
    {
        $q = Doctrine_Query::create()
            ->from("Buchung buchung");


        $this->detailedQ($q);
        $q->where("buchung.deleted_at <> '0000-00-00 00:00:00'");
        return $q;
    }


    public function buchungExistsTableProxy($person_id, $seminar_id)
    {
        $q = Doctrine_Query::create();
        $buchungen = $q->from("Buchung buchung")->where("buchung.seminar_id = ? AND buchung.person_id = ? AND buchung.storno_datum = ?", array($seminar_id, $person_id, "0000-00-00"))->execute();
        if ($buchungen->count() > 0) {
            return true;
        } else {
            return false;
        }

    }

    // helper
    public function detailedQ($q)
    {
        // seminar
        $q->leftJoin('buchung.Seminar seminar')
            ->leftJoin("seminar.Standort standort")
            ->leftJoin("standort.Ort ort")
            ->leftJoin("standort.Hotels hotels")

            ->leftJoin('seminar.SeminarArt seminarart')
        //hotel
            ->leftJoin("buchung.HotelBuchung hotelbuchung")
            ->leftJoin("hotelbuchung.Hotel hotel")
        //person
            ->leftJoin('buchung.Person person')
            ->leftJoin('person.Kontakt kontakt')
        // wer hat die buchung angelegt
            ->leftJoin("buchung.AngelegtVon creator")
            ->leftJoin("buchung.GeaendertVon updater");


        return $q;

    }

    public function lastSevenDaysTableProxy()
    {
        /* muss in den entsprechende kntroller */
        $start = "";
        $end = "";

        $tomorrow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
        $end = date("Y-m-d 00:00:00", $tomorrow);
        $sevendaysago = mktime(0, 0, 0, date("m"), date("d") - 8, date("Y"));
        $start = date("Y-m-d 00:00:00", $sevendaysago);


        $q = Doctrine_Query::create()
            ->from('ViewBuchungPreis buchung')
            ->leftJoin('buchung.Seminar seminar')
            ->leftJoin('seminar.SeminarArt seminarart')
            ->leftJoin('buchung.Person person')
            ->leftJoin('person.Kontakt kontakt')
            ->leftJoin('buchung.HotelBuchung hotelbuchung')
            ->leftJoin('hotelbuchung.Hotel hotel')
            ->orderBy("buchung.datum")
            ->where("deleted_at = '0000-00-00 00:00:00'")//->andWhere('storno_datum = 0000-00-00')
        ;

        if (!empty ($start)) {
            MosaikDebug::msg($start, "start");
            MosaikDebug::msg($end, "end");
            $q->andWhere("buchung.datum >= ? AND buchung.datum <= ?", array($start, $end));
        }

        return $q;
    }

    public function importantTableProxy($year = "", $month = "#")
    {
        /* muss in den entsprechende kntroller */
        $start = "";
        $end = "";

        if ($month === "#" && !empty ($year)) {
            $start = $year . "-01-01";
            $end = ($year + 1) . "-01-01";
        } else if (!empty($year)) {
            $start = $year . "-" . (intval($month) + 1) . "-01";
            $end = $year . "-" . (intval($month) + 2) . "-01";
        }

        $q = Doctrine_Query::create()
            ->from('ViewBuchungPreis buchung')
            ->leftJoin('buchung.Seminar seminar')
            ->leftJoin('seminar.SeminarArt seminarart')
            ->leftJoin('buchung.Person person')
            ->leftJoin('person.Kontakt kontakt')
            ->leftJoin('buchung.HotelBuchung hotelbuchung')
            ->leftJoin('hotelbuchung.Hotel hotel')
            ->orderBy("buchung.datum")
            ->where("deleted_at = ?", array('0000-00-00 00:00:00'));


        if (!empty ($start)) {

            $q->andWhere("buchung.datum > ? AND buchung.datum < ?", array($start, $end));
        }

        return $q;
    }

    public function countbystatusTableProxy($id, $status = 1)
    {
        $q = Doctrine_Query::create()
            ->select("buchung.id")
            ->from('ViewBuchung buchung')
            ->where('seminar_id = ?', array($id))
            ->andWhere("deleted_at = ?", '0000-00-00 00:00:00')
            ->andWhere("status = ?", $status);

        return $q->count();
    }

    public function countNichtTeilgenommenTableProxy($id)
    {
        return $this->countbystatusTableProxy($id, 5);

    }

    public function countStornoTableProxy($id)
    {
        return $this->countbystatusTableProxy($id, 2);

    }

    public function countUmbuchungTableProxy($id)
    {
        return $this->countbystatusTableProxy($id, 3);
    }


    public function detailedTableProxy($view = "ViewBuchungPreis")
    {
        $q = Doctrine_Query::create()
            ->from($view . ' buchung');

        $this->detailedQ($q);

        $q->where('buchung.id = ?');
        $q->andWhere("deleted_at = '0000-00-00 00:00:00'");
        //->andWhere('buchung.storno_datum = ?', '0000-00-00');

        return $q;
    }


    public function getIdTableProxy($uuid, $teilnehmer)
    {
        $q = Doctrine_Query::create()
            ->from("Buchung buchung")
            ->where("buchung.uuid=?", $uuid)
            ->andWhere("buchung.person_id=?", $teilnehmer)
            ->andWhere("buchung.storno_datum = ?", '0000-00-00')
            ->andWhere("buchung.umbuchungs_datum = ?", '0000-00-00');
        $q->andWhere("deleted_at = '0000-00-00 00:00:00'");
        return $q;
    }

    public function detailedByKontaktIdTableProxy($kontaktId)
    {
        $q = Doctrine_Query::create()
            ->from("ViewBuchungPreis buchung");

        $this->detailedQ($q);

        $q->where("person.kontakt_id = ?", $kontaktId);
        $q->useResultCache(true, 3600, "buchung_kontakt_detail_" . $kontaktId);
        //$q->andWhere ("buchung.storno_datum = '0000-00-00'");
        //$q->andWhere ("buchung.umbuchungs_datum = '0000-00-00'");

        $q->orderBy(" seminar.datum_begin");
        return $q;
    }

    public function detailedByPersonIdTableProxy($kontaktId)
    {
        $q = Doctrine_Query::create()
            ->from("ViewBuchungPreis buchung");

        $this->detailedQ($q);
        $q->useResultCache(true, 3600, "tmpl_buchung_person_detail_" . $kontaktId);
        $q->where("buchung.person_id = ?", $kontaktId);
        $q->andWhere("buchung.storno_datum = '0000-00-00'");
        $q->andWhere("buchung.umbuchungs_datum = '0000-00-00'");

        $q->orderBy(" seminar.datum_begin");
        return $q;
    }

    public function detailedByUuidTableProxy($view = "ViewBuchungPreis", $teilnehmer = -1)
    {
        $q = Doctrine_Query::create()
            ->from($view . ' buchung');

        $this->detailedQ($q);

        $q->where('buchung.uuid = ?');
        if ($teilnehmer != -1) {
            $q->andWhere("buchung.person_id = ?", $teilnehmer);
            $q->useResultCache(true, 3600, "view_buchung_preis_uuid_" . $teilnehmer);

        }
        // exclude storno and umgebucht -> wg. uebersichten der buchungen
        $q->andWhere("buchung.storno_datum = ?", '0000-00-00')
            ->andWhere("buchung.umbuchungs_datum=?", '0000-00-00');


        return $q;
    }

    public function tnByUuidTableProxy($view = "ViewBuchungPreis")
    {
        $q = Doctrine_Query::create()
            ->from($view . ' buchung');

        $this->detailedQ($q);

        $q->where('buchung.uuid = ?');
        $q->andWhere("buchung.person_id = ?");


        $q->limit(1);

        return $q;
    }


    public function unbezahltTableProxy()
    {
        $q = Doctrine_Query::create()
            ->from('ViewBuchungPreis buchung')
            ->leftJoin("buchung.Seminar seminar")
            ->leftJoin("buchung.Person person")
            ->leftJoin("seminar.SeminarArt seminarart")
            ->leftJoin("buchung.HotelBuchung hotelbuchung")
            ->leftJoin("hotelbuchung.Hotel hotel")
            ->leftJoin("person.Kontakt kontakt")
            ->where('buchung.zahlungseingang_datum = 0000-00-00')
            ->andWhere('buchung.bestaetigt = 1');
        return $q;
    }

    public function isFruehbucher()
    {
        $invoker = $this->getInvoker();
        $eightWeeks = 60 * 60 * 24 * 7 * 8; //8 Wochen

        $buchungs_datum = strtotime($invoker->datum);
        $time = strtotime($invoker->Seminar['datum_begin']);

        if ($time + $eightWeeks < $buchungs_datum) {
            return true;
        }
        return false;
    }

    public function isVDRK()
    {
        $invoker = $this->getInvoker();
        return $invoker->Person->Kontakt['vdrk_mitglied'] == 1;
    }

    public function isArbeitsamt()
    {
        $invoker = $this->getInvoker();
        return $invoker['arbeitsagentur'] == 1;
    }

    public function isBildungscheck()
    {
        $invoker = $this->getInvoker();
        return $invoker['bildungscheck'] == 1;
    }
}

?>