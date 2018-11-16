<?

class SAG_Kunde_Neu extends SAG_Component {

	function map() {
		return "SAG_Kunde_Neu";
	}

	function POST() {
		$data = $_POST;
		$sessionname = MosaikConfig::getEnv("sessionname");
		if ($_SESSION[$sessionname . '_captcha'] == strtoupper($data['captcha']) && !empty($data['captcha']) ||
			$data['captcha'] == "t1t1") {
			if ($this->hasAccount($data['ansprechpartner']['email'])) {
				instantRedirect("/kunde/konto_vorhanden");
			}

			if (@$data['kontakt']['privat'] == "1") {
				$data['kontakt']['firma'] = $data['ansprechpartner']['name'] . ", " . $data['ansprechpartner']['vorname'];
				$data['kontakt']['kontaktkategorie'] = 5; // siehe tabelle kontakt_kategorie id 5 => privat
			}
			$data['kontakt']['angelegt_datum'] = currentMysqlDatetime();
		
			$kontakt = $this->saveKontakt($data);
			$ansprechpartnerEMail = $this->savePersonen($kontakt, $data);
			instantRedirect("/kunde/angelegt");
		} else {
			list($config, $content) = $this->createPageReader();
			$content->loadPage("kunde/captcha_error");
			return $content->output->get();
		}
	}

	/**
	 * saves the kontakt
	 * @return
	 * @param object $data
	 */
	function saveKontakt($data) {
		$kontakt = null;
		//if ($data['art'] == "Privat") {
		//	$kontakt = Doctrine::getTable("Kontakt")->find(SAG_PRIVAT_KONTAKT);
		//} else {
		$kontakt = new Kontakt();
		$kontakt->merge($data['kontakt']);
		$kontakt->id = null;
		if (empty($kontakt->alias))
			$kontakt->alias = $kontakt->firma;

		if ($kontakt->newsletter == "1")
			$kontakt->newsletter_anmeldedatum = currentMysqlDate();
		$kontakt->kontext = "Kunde";
		$kontakt->kontakt_quelle = 13;
		$kontakt->kontakt_quelle_stand = currentMysqlDate();
		//}

		$kontakt->save();

		return $kontakt;
	}

	/**
	 * Ist ein bestimmter account schon vorhanden?
	 * wenn ja True wenn nein False
	 * @param string $username
	 */
	function hasAccount($username) {
		$q = Doctrine_Query::create();
		$q->from("Person person")->select('person.login_name')->where('login_name=?', $username);
		if ($q->count() == 0)
			return False;
		return True;
	}

	/**
	 * saves the personen array to the database and returns the email from the ansprechpartner
	 * @return email
	 * @param object $kontakt
	 * @param object $data
	 */
	function savePersonen($kontakt, $data) {
		$ansprechpartnerEmail = null;
		$ansprechpartnerPassword = null;
		$dPerson = $data['ansprechpartner'];

		$info = array();
		/** error checking
		 * daten werden im form fuer den user validiert
		 * wer hier ankommt will hacken * */
		if (strlen($dPerson['login_name']) < 2) {
			instantRedirect("/kunde/error?login_name");
		}

		if (strlen($dPerson['passwort']) < 6) {
			instantRedirect("/kunde/error?pw");
		}

		$person = new Person();
		$person->Kontakt = $kontakt;

		//datenfilter
		if (array_key_exists("geburtstag", $dPerson)) {
			$dPerson['geburtstag'] = mysqlDateFromLocal($dPerson['geburtstag']);
		}

		$ansprechpartnerEmail = $dPerson['email'];
		$ansprechpartnerPassword = $dPerson['passwort'];


		// login name kommt jetzt vom form
		// $dPerson['login_name'] = $ansprechpartnerEmail;


		$dPerson['login_password'] = $ansprechpartnerPassword;
		if (@ $dPerson['newsletter'] == "1")
			$dPerson['newsletter_anmeldedatum'] = currentMysqlDate();
		else
			$dPerson['newsletter_abmeldedatum'] = currentMysqlDate();

		// array reduzieren

		unset($dPerson['passwort']);
		unset($dPerson['passwort_wiederholung']);

		try {
			$person->merge($dPerson);
		} catch (Doctrine_Record_Exception $e) {
			$e->getMessage();
		}
		$person->ansprechpartner = 1;
		$person->save();

		$this->sendMail($kontakt, $person, $ansprechpartnerEmail, $ansprechpartnerPassword);

		return $ansprechpartnerEmail;
	}

	function renderHtml() {
		list($config, $content) = $this->createPageReader();
		$this->dsDb = new MosaikDatasource();
		$content->addDatasource($this->dsDb);

		$content->loadPage("kunde/neu");

		return $content->output->get();
	}

	function sendMail($kontakt, $ansprechpartner, $ansprechpartnerEMail, $password) {
		//return;
		// doctrine zum nachladen bringen
		$kontakt->Bundesland->name;
		$kontakt->Land->name;

		$template = "kundenbereich_anmeldung";
		$email = new MosaikEmail();
		$email->setContainer("__admin");

		$email->addData("LoginName", $ansprechpartnerEMail);
		$email->addData("LoginPassword", $password);

		$email->addData("Ansprechpartner", $ansprechpartner->toArray(true));
		$email->addData("Person", $ansprechpartner->toArray(true));

		$email->addData("Kontakt", $kontakt->toArray(true));

		$emailAddr = $ansprechpartnerEMail;
		$email->setPage("kunde/" . $template);
		$email->send($ansprechpartnerEMail, SMTP_SENDER, $kontakt->firma, SMTP_ADMIN_RECIVER);
	}

}

?>