<?php

include_once ("mosaikTaglib/dlog.php");

class SAG_Newsletter extends SAG_Component {

	var $entryTable = "akquiseKontakt";
	var $entryClass = "AkquiseKontakt";

	function map($name) {
		return "SAG_Newsletter";
	}

	function forward($class, $namespace = "") {
		$name = $this->next();
		$fb = $GLOBALS['firephp'];

		if ($this->next() == "danke") {
			return $this->danke();
		} else if ($this->next() == "anmelden") {
			return $this->anmelden();
		} else if ($this->next() == "aktivieren") {
			return $this->aktivieren();
		} else if ($this->next() == "deaktivieren" || $this->next() == "abmelden") {
			return $this->deaktivieren();
		}
	}

	function GET() {
		list($config, $content) = $this->createPageReader();
		$this->pageReader->output->addReplacement("page_background", "/img/header_bg_gross.jpg");
		list($config, $content) = $this->createPageReader();
		$content->loadPage($this->name() . ".xml");
		return $content->output->get();
	}

	function setStatus($email, $status, $art) {
		$abmelden = "newsletter_abmeldedatum";
		$anmelden = "newsletter_anmeldedatum";

		if ($art == "AkquiseKontakt") {
			$abmelden = "abmelde_datum";
			$anmelden = "anmelde_datum";
		}

		$target = $abmelden;
		if ($status == 1) {// anmelden
			$target = $anmelden;
		}
		$curdate = currentMysqlDate();
		MosaikDebug::msg($target, "Target:");
		MosaikDebug::msg($curdate, "Date:");

		$q = Doctrine_Query::create()->update($art)->set('newsletter', "?", $status)->set($target, "?", $curdate)->set("geaendert", "?", currentMysqlDatetime())->where('email = ?', $email);
		$q->execute();
	}

	function aktivieren() {
		$check_result = Doctrine::getTable("NewsletterAnmeldung")->findByDql("md5=?", $_GET['key'])->toArray();

		if (count($check_result) == 1) {
			$data = $check_result[0];
			list($config, $content) = $this->createPageReader();

			$checkemail_result = Doctrine::getTable("ViewNewsletterEmail")->findByDql("email=?", $data['email'])->toArray();

			if (count($checkemail_result) >= 1) {
				foreach ($checkemail_result as $kontakt) {
					if ($kontakt['newsletter'] == 0) {
						$this->setStatus($kontakt['email'], 1, $kontakt['art']);
					}
				}
			} else {
				//Datensatz als neuen Akquisesatz speichern
				$input["name"] = $data["name"];
				$input["vorname"] = $data["vorname"];
				$input["anrede"] = $data["anrede"];
				$input["email"] = $data["email"];
				$input["geaendert"] = currentMysqlDatetime();
				$input["newsletter_anmeldung"] = currentMysqlDate();
				$input['newsletter'] = 1;
				$input['kontakt_quelle'] = 'newsletter';
				$akquise = new AkquiseKontakt();
				$akquise->merge($input);
				$akquise->save();
			}
			//load page
			$content->loadPage("newsletter/danke_aktiv.xml");
			Doctrine_Query::create()->delete('NewsletterAnmeldung a')->where('a.id = ?', $data['id'])->execute();
			return $content->output->get();
		}
		instantRedirect("/newsletter/");
	}

	function anmelden() {
		$data = $_POST;
		// Gültigkeit der Eingabe checken.
		if (empty($data['email'])) {
			instantRedirect("/newsletter/");
		}
		list($config, $content) = $this->createPageReader();

		$nl_result = Doctrine::getTable("ViewNewsletterEmail")->newsletterEntrys()->fetchArray(array($data['email']));
		if (count($nl_result) >= 1) {
			//load page
			$content->loadPage("newsletter/reged.xml");
			return $content->output->get();
		}

		$nla_result = Doctrine::getTable("NewsletterAnmeldung")->findByEmail($data['email'], Doctrine::HYDRATE_ARRAY);

		if (count($nla_result) == 0) {
			$data["datum"] = currentMysqlDatetime();
			$data["md5"] = md5($data["email"]);
			$nla = new NewsletterAnmeldung();
			$nla->merge($data);
			$nla->save();
		}

		//send E-Mails
		/* EMAIL NOTIFICATION */
		$link = WEBROOT . '/newsletter/aktivieren/?key=' . md5($data['email']);

		$email = new MosaikEmail();
		$email->setContainer("__admin");
		$email->setPage("newsletter_sag");

		//$email->send(SMTP_ADMIN_RECIVER, SMTP_ADMIN_SENDER);
		//$email->send("ch@mosaik-software.de", SMTP_ADMIN_SENDER);

		$email->setPage("newsletter");
		$email->addData("link", $link);
		$email->send($data['email'], SMTP_SENDER, "", SMTP_ADMIN_RECIVER);

		//load page
		$content->loadPage("newsletter/danke.xml");
		return $content->output->get();
	}

	function deaktivieren() {
		list($config, $content) = $this->createPageReader();

		if (MosaikConfig::getEnv('email')) {
			try {
				$checkemail_result = Doctrine::getTable("ViewNewsletterEmail")->findByDql("email = ?", array(str_replace('-a-', '@', MosaikConfig::getEnv('email'))), Doctrine::HYDRATE_ARRAY);

				if (count($checkemail_result) >= 1) {
					foreach ($checkemail_result as $kontakt) {
						if ($kontakt['newsletter'] == 1) {
							$this->setStatus($kontakt['email'], 0, $kontakt['art']);
						}
					}
					$content->loadPage("newsletter/abmeldung.xml");
					return $content->output->get();
				}
			} catch ( Exception $e) {
				qlog("[E] EMail: " . MosaikConfig::getEnv('email') . " nicht in der Datenbank gefunden");
			}
			$content->loadPage("newsletter/abmeldungnichtgefunden.xml");
			return $content->output->get();
		} else {
			try {
				$checkemail_result = Doctrine::getTable("ViewNewsletterEmail")->findByDql("md5 = ?", array(MosaikConfig::getEnv('key')), Doctrine::HYDRATE_ARRAY);

				if (count($checkemail_result) >= 1) {
					foreach ($checkemail_result as $kontakt) {
						if ($kontakt['newsletter'] == 1) {
							$this->setStatus($kontakt['email'], 0, $kontakt['art']);
						}
					}
					$content->loadPage("newsletter/abmeldung.xml");
					return $content->output->get();
				}
			} catch ( Exception $e) {
				qlog("[E] EMail md5: " . MosaikConfig::getEnv('key') . " nicht in der Datenbank gefunden");
			}

			$content->loadPage("newsletter/abmeldungnichtgefunden.xml");
			return $content->output->get();
		}
	}

}
