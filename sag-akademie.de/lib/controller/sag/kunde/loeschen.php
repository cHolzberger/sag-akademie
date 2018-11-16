<?php

class SAG_Kunde_Loeschen extends SAG_Component {

	function __construct() {
		
	}

	function map($name) {
		return "SAG_Kunde_Loeschen";
	}

	function POST() {
		return $this->renderHtml();
	}

	function renderHtml() {
		list ($config, $content) = $this->createPageReader();
		$this->dsDb = new MosaikDatasource("dbtable");
		$content->addDatasource($this->dsDb);

		if (@isset($_GET['login'])) {
			$login = Doctrine::getTable("Person")->findByDql("login_name = ?", $_GET['login'])->getFirst();
			if (is_object($login)) {
				$kontakt = $login->Kontakt;
				$personen = Doctrine::getTable("Person")->findByDql("kontakt_id = ?", $kontakt->id);
				foreach ($personen as $person) {
					$buchungen = Doctrine::getTable("Buchung")->findByDql("person_id = ?", $person->id);
					foreach ($buchungen as $buchung) {
						$buchung->delete();
					}
					$person->delete();
				}
				$kontakt->delete();
			}
			$content->loadPage("kunde/geloescht.xml");
		} else {
			$content->loadPage("kunde/user_loeschen.xml");
		}
		return $content->output->get();
	}

	function HEAD() {
		throw new k_http_Response(200);
	}

}
