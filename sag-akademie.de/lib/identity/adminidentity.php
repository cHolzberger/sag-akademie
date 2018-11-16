<?
class SAG_AdminIdentity extends SAG_Identity {	
	function __construct( &$session) {
		$this->session = $session;
	}

	function toArray() {
		return $this->info->toArray();
	}
	
	function user() {
		return $this->info['name'] . ", " . $this->info['vorname'];
	}
	
  	function group() {
  		return $this->info['class'];
  	}
	
	function uid() {
		return $this->info['id'];
	}
	
	function logout() {
		$this->session->set("user", null);
		$this->session->set("token",  null);

		$this->session->destroy();
 		instantRedirect("/resources/logon/");
	}

	function tokenLogin($users) {
		foreach ($users as $u) {
			if ( MosaikConfig::getEnv("token") == $u['auth_token']) {
				return $u['username'];
			}
		}
		
		return NULL;
	}
	
	function authenticate($class=NULL) {
		qlog("SAG_AdminIdentity - Autentication");

		$session = $this->session;
	
		$HTTPDigest = new HTTPDigest();
		$POSTAuth = new POSTAuth();
		
		// fixme: performance can be improved
		$users = $this->getUsers($class);
		$u = array();
		qdir($_POST);
		qdir($_GET);
		foreach ( $users as $user ) {
				$u[$user['name']]['digest'] = md5(sprintf('%s:'.$HTTPDigest->getRealm().':%s', $user['name'], $user['password']) );
				$u[$user['name']]['username'] = $user['name'];
				$u[$user['name']]['password'] = $user['password'];
				$u[$user['name']]['auth_token'] = $user['auth_token'];
				$ui[$user['name']] = $user;
		}
		
		 if ( isset ($_POST['username']) || MosaikConfig::getEnv('token',false)) {
			$session->set("user",null);
			$session->set("token",null);
		}
		
		if (isset($_GET['logout'])){
			$this->logout();
		} else if ( MosaikConfig::getEnv("token",false) && @$username = $this->tokenLogin( $u ) ) {
			// token login 
			$this->info = $ui[$username];
			qlog("Authenticated via TOKEN as: ".$username);
			$session->set("user", $username); // FIXME: set timeout*/
			$session->set("token",  $u[$username]['auth_token']);
			
			$_tupel = Doctrine::getTable("XUser")->find ( $ui[$username]['id']);
			$_tupel->save();
		} else if ( $session->get("user") != null) {
			$this->info = $ui [ $session->get("user")];
			qlog("User: Authenticated via Session as: " . $session->get("user") );
		} else if ( ($username = $POSTAuth->authenticate($u))!= NULL ){		
			$this->info = $ui[$username];
			qlog("User Authenticated via POST as: " . $username);
			$session->set("user", $username); // FIXME: set timeout*/
 			// save last login
			$_tupel = Doctrine::getTable("XUser")->find ( $ui[$username]['id']);
			$_tupel->vorletzte_anmeldung = $_tupel->letzte_anmeldung;
			$_tupel->letzte_anmeldung = currentMysqlDatetime();
			$_tupel->save();

			$session->set("token",  $u[$username]['auth_token']);

		 
 		} else {
			if ( getRequestType( ) == "json") {
				header("HTTP/1.0 403 Forbidden");
				$data['needlogin']=true;
				die (json_encode($data));
			} else { 
				instantRedirect("/resources/logon/");
			}
			//$HTTPDigest->send();
			qlog("Not Authenticated");
			die();
		}
		//MosaikObjectStore::add("identity", $this);
	}
}
?>