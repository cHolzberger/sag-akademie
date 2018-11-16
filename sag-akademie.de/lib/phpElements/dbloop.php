<?php
$firephp = FirePHP::getInstance(true);

class DbLoop extends MosaikElement {
	var $conn;

	function init() {
		$this->ignoreChildren = true;
	}

	function source($attributes, $value) {
		$table = $attributes['table'];
		$recurse = array_key_exists (  'recurse', $attributes);
		$tableDC = Doctrine::getTable($table);
		$results = $tableDC->findAll();

		$pageReader = new MosaikPageReader(MosaikPageReaderConfig::getDefault());
		$ds = new MosaikDatasource ("dbtable");

		$pageReader->addDatasource ( $ds );
		$pageReader->initElements();

		$content = "";

		foreach ($results as $result) {
			$result->refreshRelated();
			$GLOBALS['firephp']->log($recurse,"DbTable::recurse");
			
			$ds->set($result->toArray($recurse));
			$pageReader->loadString($value);
			$content .=  $pageReader->output->get();
			$pageReader->output->clear();
		}

		return $content;
	}

	function getLabels($string) {
		$tidyConfig = array(
           'indent'         => true,
           'input-xml'		=> true,
           'output-xhtml'   => true,
           'wrap'           => 200,
           "input-xml" => true
    	);

		$this->tidy = new tidy();
		$this->tidy->parseString($string, $tidyConfig, "raw");
		$this->tidy->CleanRepair();

		$rt = $this->tidy->root();
		$nodes = $rt->child[0]->child;
		$content = "<thead>";
		foreach ($nodes as $node) {
			if (isset ($node->attribute) && array_key_exists("label", $node->attribute)) {
				$content .= "<th>" . $node->attribute['label'] . "</th>";
			} else {
				$content .="<th></th>";
			}
		}
		$content .="</thead>";
		return $content;
	}

}
?>