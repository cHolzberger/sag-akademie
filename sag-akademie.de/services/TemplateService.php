<?php

include_once("services/DatabaseService.php");
//include_once( 'lib/PEAR/HTMLPurifier.includes.php');
include_once("lib/PEAR/HTMLPurifier.auto.php");

class EmailTemplate {

	public $content;
	public $id;
	public $filename;
	public $section;
	public $title;
	public $subjectNs;
	public $subjectKey;
	public $subject;

	function fromArray($arr) {
		qlog (__CLASS__."::" .__FUNCTION__ . ":");
		qdir($arr);

		$this->id = $arr['id'];
		$this->filename = $arr['filename'];
		$this->content = $arr['content'];
		$this->subject = $arr['subject'];
	}

	static function convertFromWord($fname) {
		$fId = $fname;

		$root = MosaikConfig::getVar('srvEmailPath');
		if (FALSE === strpos($fname, ".xml"))
			$fname = $fname . ".xml";
		$fname = $root . $fname;

		$file = fopen($fname, "r");
		$line = fgets($file, 8192);

		$convertedAttr = "converted=\"true\"";

		if (substr($line, 6, strlen($convertedAttr)) != $convertedAttr) {
			$cnt = "";
			while (!feof($file)) {
				if (substr($line, 0, 5) == "<link" || substr($line, 0, 5) == "href=") {
					$line = fgets($file, 8192);
					continue;
				}

				$line = iconv('CP1252', 'UTF-8', $line);
				$line = str_replace("windows-1252", "UTF-8", $line);
				$line = str_replace("<html", "<html $convertedAttr", $line);
				$cnt .= preg_replace("/<meta ([^>]*)>/", '<meta \1 />', $line);

				if (substr($line, 0, 5) == "<body")
					break;

				$line = fgets($file, 8192);
			}


			// html purifyer
			$config = HTMLPurifier_Config::createDefault();
			$config->set("CSS.AllowImportant", true);
			$config->set("CSS.AllowTricky", true);
			$config->set("CSS.Proprietary", true);
			$config->set("HTML.FlashAllowFullScreen", true);
			$config->set("HTML.TidyLevel", "light");
			$config->set("HTML.Trusted", true);
			$config->set("Cache.SerializerPath", MosaikConfig::getVar("htmlPurifyerCache"));
			$config->set('Core.Encoding', 'UTF-8');
			$config->set('Cache.DefinitionImpl', null); // TODO: remove this later!
			$config->set('HTML.DefinitionID', $fId);
			$config->set('HTML.DefinitionRev', 1);
			$purifier = new HTMLPurifier($config);

			// purify output
			$_cnt = str_replace("&#160;", "&nbsp;", $_cnt);
			$_cnt = str_replace("\n", " ", $purifier->purify(iconv('CP1252', 'UTF-8', file_get_contents($fname))));

			// run some customisations
			$_cnt = preg_replace("/(.[^\}\&\]]*)&quot;(.[^\&]*)&quot;/", '\1"\2" ', $_cnt);
			//  
			$_cnt = preg_replace('/&amp;(.[^; ]*);/', '&\1;', $_cnt);

			$_cnt = preg_replace("/\{(.[^\}]*)\}/", '<\1 >', $_cnt);
			$_cnt = preg_replace('/\$(.[^\$]*)\$/', '<\1 />', $_cnt);

			$cnt .= $_cnt . "</body></html>";

			// and now tidy it up
			$tidyconfig = array(
			 'indent' => true,
			 'preserve-entities' => true,
			 'output-xml' => true,
			 'input-xml' => true,
			 'word-2000' => true,
			 'wrap' => 200);

			$tidy = new tidy;
			$tidy->parseString($cnt, $tidyconfig, 'utf8');
			$tidy->cleanRepair();
			file_put_contents($fname, tidy_get_output($tidy));
		}
		fclose($file);
	}

	function toXml() {
		qlog (__CLASS__."::" .__FUNCTION__ . ": toXml");

		//$cnt = sed -e "s/\(.[^\}\&]*\)&quot;\(.[^\&]*\)&quot;/\1\"\2\"/g"

		$cnt = preg_replace("/(.[^\}\&]*)&quot;(.[^\&]*)&quot;/", '\1"\2"', $this->content);

		$cnt = str_replace("{", "<mdb:value mpath=\"", $cnt);
		$cnt = str_replace("}", "\" />", $cnt);

		$cnt = str_replace("[", "<mdb:value mpath=\"", $cnt);
		$cnt = str_replace("]", "\" />", $cnt);
		return "<content>\n" . $cnt . "\n</content>";
	}

	function fromXml($xml) {

		$xml = str_replace("<content>", "", $xml);
		$xml = str_replace("</content>", "", $xml);
		//  \/>/

		$cnt = preg_replace("/(<\/?)mdb:value mpath=\"([^\"]*)\" \/>/", "[\\2]", $xml);
		//$cnt = str_replace("<mdb:value mpath=\"","[",$xml);
		//$cnt = str_replace("\" />","]",$cnt);
		$this->content = $cnt;
	}
	
	function toObject() {
		$r = new stdClass();
		$r->id = $this->id;
		$r->filename = $this->filename;
		$r->content = $this->content;
		$r->subject = $this->subject;

		return $r;
	}
	
	function save() {
		qlog (__CLASS__."::" .__FUNCTION__ . ": filename => {$this->filename}");
		file_put_contents(WEBROOT . $this->filename, $this->toXml());
		qlog (__CLASS__."::" .__FUNCTION__ . ": filename => {$this->filename} template done");

		MosaikConfig::setPersistent($this->subjectNs, $this->subjectKey, $this->subject);
		qlog (__CLASS__."::" .__FUNCTION__ . ": filename => {$this->filename} subject done");

	}
}

class TemplateService extends DatabaseService {
	static  $sections = array();
	static $templates = array();
	static $_initDone = false;
	
	static function _initTemplates() {
		if ( self::$_initDone ) return;
		
		$template[] = TemplateService::createTemplate('geb_mail', "Glückwünsche", "Glückwunsch Mail", "/templates/emails/notification_geburtstag.xml", "geburtstagsCheck", "subject");
		
		$warnung1_mail_check =  MosaikConfig::getPersistent("teilnehmerNichtErreicht","check1");
		$warnung2_mail_check =  MosaikConfig::getPersistent("teilnehmerNichtErreicht","check2");
		$warnung3_mail_check =  MosaikConfig::getPersistent("teilnehmerNichtErreicht","check3");
		
		$template[] = TemplateService::createTemplate('warnung1_mail', "Warnungen", "Warnung 1 ({$warnung1_mail_check} Tage)","/templates/emails/notification_teilnehmerzahl1.xml", "teilnehmerCheck", "subject1");
		$template[] = TemplateService::createTemplate('warnung2_mail', "Warnungen", "Warnung 2 ({$warnung2_mail_check} Tage)","/templates/emails/notification_teilnehmerzahl2.xml", "teilnehmerCheck", "subject2");
		$template[] = TemplateService::createTemplate('warnung3_mail', "Warnungen", "Warnung 3 ({$warnung3_mail_check} Tage)","/templates/emails/notification_teilnehmerzahl3.xml", "teilnehmerCheck", "subject3");
		self::$_initDone = true;
		self::$templates = $template;
	}
	
	static function createTemplate($id, $section, $infoKey, $filename, $subjectNs, $subjectKey) {
		
		
		array_push(self::$sections , $section);
		self::$sections = array_unique(self::$sections);
		
		$t = new EmailTemplate();
		$t->fromXml(file_get_contents(WEBROOT . $filename));
		$t->id = $id;
		$t->section = $section;
		$t->title = $infoKey;
		$t->filename = $filename;
		$t->subjectNs = $subjectNs;
		$t->subjectKey = $subjectKey;
		$t->subject = MosaikConfig::getPersistent($subjectNs,$subjectKey);
		return $t;
	}
	
	
	static function getSections() {
		self::_initTemplates();

		return array_values(array_unique(self::$sections));
	}
	public static function getTemplate($id) {
		self::_initTemplates();
		
		foreach ( self::$templates as $template ) {
			if ( $template->id == $id ) return $template;
		} 
		
		return null;
	}

	public static function getTemplates($section) {
		self::_initTemplates();
		$ret = array();
		
		foreach ( self::$templates as $template ) {
			if ( $template->section == $section ) $ret[] = $template;
		}
		
		return $ret;
	}

	/**
	 * 	Speichert die template $_template
	 *
	 *
	 * @param $_template EmailTemplate 
	 */
	public static function saveTemplate($_template) {
		$t = new EmailTemplate();
		$t->id = $_template->id;
		$t->content = $_template->content;
		$t->filename = $_template->filename;
		$t->subject = $_template->subject;
		$t->save();
	}
	
	public static function init() {
		self::_initTemplates();
	}

}