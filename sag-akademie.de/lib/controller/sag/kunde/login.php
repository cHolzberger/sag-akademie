<?php

$GLOBALS['path'] = array();

class SAG_Kunde_PWVergessen extends SAG_Component {

    function map() {
        return "SAG_Kunde_Loginfehler";
    }

    function renderHtml() {
        list ($config, $content) = $this->createPageReader();

        $content->loadPage("kunde/pwvergessen.xml");

        return $content->output->get();
    }

    function POST() {
        if (isset($_POST['pw_username'])) {
            $q = Doctrine_Query::create();
            $q->from("Person person")->where("person.login_name=?", $_POST['pw_username']);
            $kunde = $q->fetchOne();
	    $this->sendMail($kunde);
        }
        return $this->renderHtml();
    }

    function sendMail($kunde  ) {
        $email = new MosaikEmail();

        $template = "pwvergessen";
        $email->addData("LoginName", $kunde->login_name);
        $email->addData("LoginPassword", $kunde->login_password);
        $email->addData("Person", $kunde->toArray(true));

        $emailAddr = $kunde->email;
        $email->setPage("kunde/" . $template);
	// fixme ggf. Firma anzeigen
        $email->send($kunde->email, SMTP_SENDER, "", SMTP_ADMIN_RECIVER);
    }

}

class SAG_Kunde_Loginfehler extends SAG_Component {

    function map() {
        return "SAG_Kunde_Loginfehler";
    }

    function renderHtml() {
        list ($config, $content) = $this->createPageReader();

        $content->loadPage("kunde/loginfehlgeschlagen.xml");

        return $content->output->get();
    }
}

class SAG_Kunde_Gesperrt extends SAG_Component {

    function map() {
        return "SAG_Kunde_Gesperrt";
    }

    function renderHtml() {
        list ($config, $content) = $this->createPageReader();
        $this->dsDb = new MosaikDatasource();
        $content->addDatasource($this->dsDb);

        $this->dsDb->add("Info", $_GET['Info']);

        $content->loadPage("kunde/gesperrt.xml");

        return $content->output->get();
    }
}

class SAG_Kunde_Login extends SAG_Component {

    public $map = Array(
        "startseite" => "SAG_Kunde_Startseite",
        "fehler" => "SAG_Kunde_Loginfehler",
        "gesperrt" => "SAG_Kunde_Gesperrt",
        "buchung" => "SAG_Kunde_Buchung",
        "kontakt" => "SAG_Kunde_Kontakt",
        "person" => "SAG_Kunde_Person",
        "neu" => "SAG_Kunde_Neu",
	 "loeschen" => "SAG_Kunde_Loeschen",
        "angelegt" => "SAG_Staticpage",
        "konto_vorhanden" => "SAG_Staticpage",
        "pwvergessen" => "SAG_Kunde_PWVergessen",
        "inhouse_termin" => "SAG_Kunde_InhouseTermin"
    );

    function __construct() {
    }

    function map($name) {
        if (array_key_exists($name, $this->map)) {
            return $this->map[$name];
        } else {
            throw new MosaikPageReader_PageNotFound("");
        }
    }

    function POST() {
	MosaikDebug::msg($_POST,"Doing Login");	

        if (isset($_POST['username'])) {
            $this->identity()->authenticate();
            if (!$this->identity()->anonymous()) {
                if (isset($_POST['seminar']['id'])) {
                   instantRedirect("/seminar/buchen/" . $_POST['seminar']['id']);
                } else {
                    instantRedirect("/kunde/startseite");
                }
            } else {
                return $this->renderHtml();
            }
        }
        return $this->renderHtml();
    }

    function renderHtml() {
        if (!$this->identity()->anonymous()) {
            if (isset($_POST['seminar']['id'])) {
                instantRedirect("/seminar/buchen/" . $_POST['seminar']['id']);
            } else {
		instantRedirect("/kunde/startseite");
            }
        }

        list ($config, $content) = $this->createPageReader();
        $this->dsDb = new MosaikDatasource("dbtable");
        $content->addDatasource($this->dsDb);

        $content->loadPage("kunde/login.xml");

        return $content->output->get();
    }

    function renderHtmlForward($class_name, $namespace = "") {
        list ($config, $content) = $this->createPageReader();

        if (is_array($class_name)) {
            $namespace = $class_name[1];
            $class_name = $class_name[0];
        }

        $next = $this->createComponent($class_name, $namespace);
        $content = "";

        return $next->dispatch();
    }

    function HEAD() {
        throw new k_http_Response(200);
    }
}
