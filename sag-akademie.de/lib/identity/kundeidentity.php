<? 
class SAG_KundeIdentity extends SAG_Identity {
	function __construct(&$session) {
		$this->session = $session;
		$person_id = $session->get("kunde_user");
		if ( $person_id) {
			$this->info = Doctrine::getTable("Person")->find($person_id);
		} else {
			$this->info = null;
		}
	}
	
	function user() {
		return $this->info->name . ", ". $this->info->vorname;
	}
	
	function toArray() {
		return $this->info->toArray();
	}
	
	function group() {
		return "kunde";
	}
	
	function active() {
		return $this->info->gesperrt != 1;
	}
	
	function getKontakt() {
		return $this->info->Kontakt;
	}
	
	function getPerson($id=0) {
		if($id==0) $id = $this->info->id;
		$p = Doctrine::getTable("Person")->find($id);
		return $p;
	}
	
	function getPersonen() {
		if ( $this->getKontakt()->id == 1 ) {
			return NULL;
		} else {
			return Doctrine::getTable("Person")->findByDql("kontakt_id=?", $this->getKontakt()->id);
			
		}
	}

	/**
	 * Liefert alle Mitarbeiter als ARRAY oder NULL wenn nicht vorhanden
	 * @return Array of Person
	 **/
	function getMitarbeiter() {
		if ( $this->getKontakt()->id == 1 ) {
			return NULL;
		} else {
		    $ret = array();
		    $personen = Doctrine::getTable("Person")->findByDql("kontakt_id=? AND nur_inhouse <> ?", array($this->info->kontakt_id,1))->toArray();
		    foreach ($personen  as $person )  {
			
			    if ( $person['ansprechpartner'] == 0 && $person['id'] != $this->info->id ) {
				$ret[] = $person;
			   }
			}
			return $ret;
		}
	}
	
	function getBuchungen() {
		if ( $this->info->Kontakt->id == 1 ) {
			return Doctrine::getTable("Buchung")->detailedByPersonId($this->info->id)->execute();
		} else {
			return Doctrine::getTable("Buchung")->detailedByKontaktId($this->info->Kontakt->id)->execute();
		}
	}
	
	function logout() {
		$this->session->set("kunde_user",null);
		$this->session->destroy();

		instantRedirect("/");
	}
	
	function authenticateUser($user, $password) {
		if ( empty ( $user ) || empty ($password )) return false;
		
		$user = Doctrine::getTable("Person")->findByDql("login_name=? AND login_password=?",array($user, $password))->getFirst();
		if ( !is_object ($user)) return false;
		return $user;
	}
	
	function authenticate ($class=NULL) {
		MosaikDebug::msg("SAG_AnonymousIdentity", "Login");
		
		$session = $this->session;
		
		$u=""; $p="";
		if ( isset ($_POST['username'])) {
			$u=$_POST['username'];
			$p=$_POST['password'];
			$session->set("kunde_user",null);
		}
		
		if (isset($_GET['logout'])){
			$this->logout();
		} else if ( $session->get("kunde_user") != null) {
			$this->info = Doctrine::getTable("Person")->find($session->get("kunde_user"));
			//$session->set("kunde_user", $this->info); // FIXME: set timeout*/
			$session->set("kunde_user", $this->info->id);
			MosaikDebug::infoDebug("Authenticated via Session as: ".$session->get("kunde_user"));
		} else if ( FALSE !== ($user = $this->authenticateUser($u, $p))){		
			$this->info = $user;
		//	$session->set("kunde_user", $user); // FIXME: set timeout*/
			$session->set("kunde_user", $user->id); // FIXME: set timeout*/
			
 		} else {
 			qlog("Not Authenticated");
			
 			instantRedirect("/kunde/fehler");
			//$HTTPDigest->send();
			die();
		}
		
		if ( ! $this->active() ) {
		//	$session->set("kunde_user", null);
			$session->set("kunde_user", null);
			
			instantRedirect("/kunde/gesperrt?Info=". urlencode($user->gesperrt_info));
		}
		
	}
}
?>