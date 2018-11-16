<?php

/**
 * @author: C. Holzberger ch@mosaik-software.de
 * @copyright: Copyright &copy; 2008, MOSAIK-Software
 * @abstract
 * MosaikPageReader is used to fill a page containing new tags with
 * content assigned to these tags by feeding in a datasource to tags
 *
 * @tutorial
 * $a = new MosaikPageReader(); // inits a new PageReader with html backend and ../../ as base for your elements and templates
 */
## its so bad that php is missing namespaces

include_once("lib/Mosaik/Output.php");

include_once("lib/Mosaik/PageReaderConfig.php");

include_once("lib/Mosaik/Element.php");
include_once("lib/Mosaik/ElementList.php");
include_once("lib/Mosaik/ElementGenerator.php");

include_once("lib/Mosaik/Datasource.php");
include_once("lib/Mosaik/DatasourceList.php");

include_once("lib/Mosaik/ObjectQueue.php");
include_once("lib/Mosaik/Resource.php");

include_once("lib/Mosaik/Runtime.php");

include_once("lib/helpers.php");

$firephp = FirePHP::getInstance(true);

/**
 * @classDescription Page not Found exception
 */
class MosaikPageReader_PageNotFound extends Exception {

	protected $content;

	public function __construct($content, $message = "") {
		if ($message == "")
			$message = $content;
		$this->message = $message;
		$this->content = $content;
	}

	public function content() {
		return $this->content;
	}

}

/**
 * @classDescription the Page reader itself
 */
class MosaikPageReader {

	var $config;
	var $tidyConfig;
	var $elementList;
	var $datasourceList;
	var $output;
	public $variable;

	function __construct($config) {
		$this->config = $config;
		$this->tidyConfig = array(
			'indent' => true,
			'input-xml' => true,
			'output-xhtml' => false,
			'wrap' => 200,
			"clean" => true,
			"numeric-entities" => true,
			"quote-nbsp" => true,
			'input-encoding' => 'utf8',
			'output-encoding' => 'utf8',
			'literal-attributes' => true
		);
		$this->elementList = false;
		$this->datasourceList = new MosaikDatasourceList();
		$this->output = new MosaikOutput();
		$this->variable = $config->variable;
		$this->tidy = new tidy(); // for load string
	}

	function initElements() {
		global $firephp;

		$eg = new MosaikElementGenerator($this->config, $this->datasourceList);
		$elements = $eg->getElementList();
		$config = $this->config;
		$dsl = $this->datasourceList;

		include("lib/phpElements/init.php");
		$this->setElementList($elements);
	}

	function loadPage($page) {
		$this->elementList->setDatasourceList($this->datasourceList);
		$page = explode(";", $page);
		$page = $page[0];
		if (FALSE === strpos($page, ".xml"))
			$page = $page . ".xml";

		global $firephp;
		//$firephp->group('MosaikTagLib->loadPage()');
		$fn = $this->config->pageBasepath . "/" . $page;
		$rt = null;

		if (file_exists($fn)) {
			$rt = null;
			$loaded = false;
			if (MosaikConfig::getVar("enableApc")) {
				MosaikDebug::msg($fn, "Loading Cached Page");
				$content = apc_fetch("pg_$fn", $loaded);

				if ($loaded) {
					$rt = $this->prepareString($content);
				}
			} else if (MosaikConfig::getVar("enableMemcache")) {
				$m = new Memcache();
				$m->addServer("localhost", 11211);
				$content = $m->get("pg_$fn");

				if ($content !== FALSE) {
					$rt = $this->prepareString($content);
				}
			}

			if (!$loaded) {
				$this->tidy = new tidy($fn, $this->tidyConfig, $this->config->encoding);
				$this->tidy->CleanRepair();
				if (MosaikConfig::getVar("enableApc")) {
					MosaikDebug::msg($fn, "Generating Page Cache");
					apc_add("pg_$fn", (string) $this->tidy);
				} else if (MosaikConfig::getVar("enableMemcache")) {
					$m = new Memcache();
					$m->addServer("localhost", 11211);
					$m->set("pg_$fn", (string) $this->tidy);
				}
				$rt = $this->tidy->root();
			}

			$this->parse($rt->child, $this->output);

			return 0;
		} else {
			$ds = $this->datasourceList->get("content");
			$ds->add("text", "<p><h2>404</h2></p><p>Die Seite wurde nicht gefunden.</p>" . $fn);
			$this->tidy = new tidy($this->config->httpBasepath . "404.xml", $this->tidyConfig, $this->config->encoding);
			$this->tidy->CleanRepair();

			$rt = $this->tidy->root();
			$this->parse($rt->child, $this->output);
			/** PERFORMANCE MONITOR * */
			$mtime = microtime(true);
			//$totaltime = $mtime - $starttime;
			//$firephp->log("MPR->loadPage::NotFound($fn) took" . $totaltime . " seconds");
			/** END PERFORMANCE MONITOR * */
			throw new MosaikPageReader_PageNotFound($this->output->get(), "MosaikPageReader: Page not found:" + $fn);
			return 1;
		}
	}

	function prepareString($string) {
		$this->tidy->parseString($string, $this->tidyConfig, $this->config->encoding);
		$this->tidy->CleanRepair();
		return $this->tidy->root();
	}

	function loadString($string, $prepared=NULL) {
		//$starttime = microtime(true);
		$this->elementList->setDatasourceList($this->datasourceList);
		global $firephp;
		$rt = null;
		if ($prepared == NULL) {
			$rt = $this->prepareString($string);
		} else {
			$rt = $prepared;
		}

		$content = "";
		if ($rt->child[0]->hasChildren()) {
			$content = $this->parse($rt->child[0]->child, $this->output);
		}
		//$mtime = microtime(true);
		//$totaltime = $mtime - $starttime;
		//$firephp->log("MPR->loadString took " . $totaltime . " seconds");
		return $content;
	}

	/**
	 * @param array $element
	 */
	function setElementList($elements) {
		$this->elementList = $elements;
	}

	function addElement($element) {
		$this->elementList->add($element);
	}

	function parseTagValue($value) {
		$output = new MosaikOutput();
	/* $tidy = new tidy;
		$tidy->parseString($value, $this->tidyConfig, $this->config->encoding);
		$tidy->cleanRepair();
	$rt = $this->tidy->root(); */

		$this->parse($value, $output);
		//dlog("Output parseTagValue". $output->get());
		return $output->get();
	}

	function parse($childs, $output, $indent=0) {
		$this->elementList->setDatasourceList($this->datasourceList);


		$indent = $indent + 4;
		if (is_string($childs)) {
			$output->add($childs);
		} else if (is_object($childs) && $childs->isText()) {
			$output->add($childs->value);
		} else
			foreach ($childs as $node) {
				$hasReplacement = false;
				setTagname($node->name);

				if ($node->isComment()) { #ignore comments for now
					$hasReplacement = true;
					$output->add($node->value);
				} else if ($this->elementList and $hit = $this->elementList->has($node->name)) { #taglib
					// replace child nodes first
					$value = "";
					if ($node->child and !$hit->ignoreChildren) {
						//dlog("parsing ".$node->name ." children");
						$value = $this->parseTagValue($node->child);
					} else if ($hit->ignoreChildren) {
						$value = $node->value;
					}
					$content = "";
					// then include the surrounding element
					//$hit->setDatasourceList($this->datasourceList);
					try {
						$content = $hit->source($node->attribute, $value);
					} catch (Exception $e) {
						$content = nl2br(utf8Htmlentities($e->__toString()));
						$GLOBALS['firephp']->log($content, "MosaikPageReader Taglib exception");
					}

					$hasReplacement = true;
					$output->add($content);
				} else if ($node->name == "br" || $node->name == "img") { #tidy bug... damn
					$output->shortTag($node->name, $node->attribute);
					$hasReplacement = true;
					if ($node->child)
						$this->parse($node->child, $output, $indent);
				}else if ($node->name == "meta" || $node->name == "link") {
					$output->shortTag($node->name, $node->attribute);
					$hasReplacement = true;
					if ($node->child)
						$this->parse($node->child, $output, $indent);
				} else if ($node->isText()) { #text
					$output->add($node->value);
				} else if ($node->name == "set") {
					$this->variable[$node->attribute["name"]] = $node->attribute["value"];
					$this->output->addReplacement($node->attribute["name"], $node->attribute["value"]);
					$hasReplacement = true;
				} else { #every other tag
					$output->startTag($node->name, $node->attribute);
					#only go on if this is no text node and no
					#taglib tag
					if ($node->child)
						$this->parse($node->child, $output, $indent);
				}

				if (!$hasReplacement and !$node->isText())
					$output->closeTag($node->name);
			}
	}

	function addDatasource(&$datasource) {
		global $firephp;
		$this->datasourceList->add($datasource);
	}

}

?>
