<?

/**
 *  @author Christian Holzberger <ch@mosaik-software.de>
 * @package Mosaik
 * @license Use not permitted without written license
 *
 * Changelog:
 * Auf Swift Version 4.0.6 aktualisiert
 */
/* FIXME: make optional! */
include_once 'lib/Swift4/swift_required.php';
include_once("services/TemplateService.php");
include_once(dirname(__FILE__) . "/Datasource.php");
include_once("lib/mosaikTaglib/mosaikPageReader.class.php");

class MosaikEmail {

	var $subject;

	function __construct() {
		$this->initPageReader();
		$this->container = "__engine";
	}

	function initPageReader() {
		$this->dsContent = new MosaikDatasource("content");
		$this->dsDbtable = new MosaikDatasource("dbtable");

		/** load the standard container * */
		$this->pageReader = new MosaikPageReader(MosaikPageReaderConfig::getDefault("html", MosaikConfig::getVar("srvEmailPath")));
		$this->pageReader->initElements();

		/** create a page reader for content * */
		$this->contentReader = new MosaikPageReader(MosaikPageReaderConfig::getDefault("html", MosaikConfig::getVar("srvEmailPath")));
		$this->contentReader->initElements();

		/** add the datastores to both page readers * */
		$this->pageReader->addDatasource($this->dsContent);
		$this->pageReader->addDatasource($this->dsDbtable);
		$this->contentReader->addDatasource($this->dsContent);
		$this->contentReader->addDatasource($this->dsDbtable);
	}

	function addData($key, $value) {
		$this->dsDbtable->add($key, $value);
	}

	function getData($key) {
		return $this->dsDbtable->get($key);
	}

	function setPage($page, $convert=false) {
		// lets convert the template
		$this->page = $page;
		if ($convert) {
			EmailTemplate::convertFromWord($this->page);
		}
	}

	function setContainer($name) {
		$this->container = "$name";
	}

	function setSubject($subject) {
		$this->subject = $subject;
	}

	function send($to, $from, $extendSubj = "", $bcc=null) {
		//MosaikDebug::msg($this->page,"=====> send calledLoading EMail");
		$_bcc = $bcc;
		$_to = $to;
		try {
			$appendEmail = "";

			//Create the message
			$this->contentReader->loadPage($this->page);
			$cnt = $this->contentReader->output->get();
			$subject = "";
			if ($this->subject) {
				$subject = $this->subject;
			} else {
				$subject = $this->contentReader->output->getReplacement("subject", "-Unbekannt-");
			}



			// $override email
			if (MosaikConfig::getVar("overrideEmail") !== false) {
				$subject = $subject . " (recipient: " . $_to . ") (bcc: " . $_bcc . ") " . $this->page;
				$to = MosaikConfig::getVar("overrideEmail");
				$bcc = MosaikConfig::getVar("overrideEmail");
			}
			$this->dsContent->add("text", $cnt);

			$this->pageReader->loadPage($this->container);
			$message = $this->pageReader->output->get() . $appendEmail;

			/** THE SWIFT PART * */
			//To use the ArrayLogger


			$transport = Swift_SmtpTransport::newInstance(SMTP_SERVER, MosaikConfig::getVar("smtpPort"))
			  ->setUsername(MosaikConfig::getVar("smtpUser"))
			  ->setPassword(MosaikConfig::getVar("smtpPassword"));

			$mailer = Swift_Mailer::newInstance($transport);

			$logger = new Swift_Plugins_Loggers_ArrayLogger();
			$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

			$message = Swift_Message::newInstance($mailer)
			  ->setSubject($subject . " " . $extendSubj)
			  ->setFrom(array($from => 'SAG-Akademie'))
			  ->setTo(array($to))
			  ->setBody($message, 'text/html');

			if ($bcc != null) {
				$message->setBcc($bcc);
			}

			$mailer->send($message);

			$this->pageReader->output->clear();
			$this->contentReader->output->clear();
			file_put_contents(WEBROOT . "/resources/log/email", "=======> Gesendet am: " . date("d.m.Y H:i:s") . "\n", FILE_APPEND);
			file_put_contents(WEBROOT . "/resources/log/email", "=> Empfaenger: " . $_to . "\n", FILE_APPEND);
			file_put_contents(WEBROOT . "/resources/log/email", "=> Kopie: " . $_bcc . "\n", FILE_APPEND);
			file_put_contents(WEBROOT . "/resources/log/email", "=> Betreff: " . $subject . " " . $extendSubj . "\n", FILE_APPEND);
			file_put_contents(WEBROOT . "/resources/log/email", "=========>Protokoll START <=========\n", FILE_APPEND);
			file_put_contents(WEBROOT . "/resources/log/email", $logger->dump(), FILE_APPEND);
			file_put_contents(WEBROOT . "/resources/log/email", "=========>Protokoll ENDE<=========\n", FILE_APPEND);
		} catch (Exception $e) {
			file_put_contents(WEBROOT . "/resources/log/email", "!!!!!!!!!!> Fehler beim senden:" . "\n", FILE_APPEND);
			file_put_contents(WEBROOT . "/resources/log/email", $e->getMessage(), FILE_APPEND);
			file_put_contents(WEBROOT . "/resources/log/email", "=========>Protokoll ENDE<=========\n", FILE_APPEND);

			MosaikDebug::msg($e->getMessage(), "EMail exception");
		}
	}

}

?>