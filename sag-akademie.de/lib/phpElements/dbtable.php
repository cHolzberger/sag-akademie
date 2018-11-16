<?php
$firephp = FirePHP::getInstance(true);

class DbTable extends MosaikElement {
	var $conn;
	var $table;


	var $headTableStart= "<div class='headWrapper' style='overflow: hidden; width: auto; left:0px; right: 17px; left: 0px; height:24px; position: absolute;'>
	<table cellspacing='0' cellpadding='0' class='headTable' height='24' style='top:0px; left:0px; height: 24px; width: 100%; table-layout:fixed;'>"; 
	var $headTableEnd = "</table></div><div class='toprightSpacer' >&nbsp;</div>";

	var $containerStart = '<div id="%s" class="dbContainer tsContainer ui-widget ui-widget-content">';
	var $wrapperStart = '<div class="tsWrapper ui-widget ui-widget-content">';
	var $wrapperEnd = '</div>';
	var $containerEnd = '<div class="tsFooter ui-widget ui-widget-content">--</div></div>';
	
	function init() {
		$this->ignoreChildren = true;
	}

	function source($attributes, $value) {
		$datafetcher = $this;

		if (array_key_exists ('dbtableDataFetch', $GLOBALS)) { /* fixme: name sux */
			$datafetcher = $GLOBALS['dbtableDataFetch'];
		}
		$pageReader = new MosaikPageReader(MosaikPageReaderConfig::getDefault("html"));
		$pageReader->initElements();
		$content = "";
		if (array_key_exists("table", $attributes)) {
			$this->table = $attributes['table'];
			$recurse = array_key_exists (  'recurse', $attributes);
			$results = $datafetcher->getData($this->table);

			$ds = new MosaikDatasource ("dbtable");
			$pageReader->addDatasource ( $ds );


			$content = "";
			$prepared = $pageReader->prepareString($value);
			
			if ( is_array($results)|| count($results) > 0) foreach ($results as $result) {
				$GLOBALS['firephp']->warn("using slow reference resolution");
				$class = "";
				
				$class = sprintf('{ id: "%s"}', $result->id);

				if ( $recurse ) {
					$result->refreshRelated();
				}
				$ds->set($result->toArray(true));
				$pageReader->loadString("", $prepared);
				$content .= "<tr class='$class'>" . $pageReader->output->get() . "</tr>";
				$pageReader->output->clear();
			}
		} else {
			$source = $this->datasourceList->get("dbtable");
			$ds = new MosaikDatasource ("dbtable");
			$ds->merge($source);

			$pageReader->addDatasource($ds);

			$this->fromArray = $attributes['fromArray'];
			$results = $source->get($this->fromArray);

			if (  !empty($results) ) foreach ($results as $result) {
				$class = "";
				if ( array_key_exists( "id", $result)) {
					$class = sprintf('{ id: "%s" }', $result['id']);
				}
				$ds->set($result);
				$pageReader->loadString($value);
				$content .= "<tr class='$class'>" . $pageReader->output->get() . "</tr>";
				$pageReader->output->clear();
			}
		}

		$style="";
		if (array_key_exists ('style', $attributes)) { /* fixme: name sux */
			$style = $attributes['style'];
		}
		$noEmpty=false;
		if (array_key_exists ('noempty', $attributes)) { /* fixme: name sux */
			$noEmpty = true;
		}

		if (empty($results) && $noEmpty) return "";
		$dbtableId=md5(time());
		$headTable = sprintf($this->containerStart,$dbtableId) . $this->headTableStart  . $this->getLabels($value) . $this->headTableEnd . $this->wrapperStart;  
		return $headTable . 
			"<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"dbtable\" style=\"$style\" ><colgroup/><tbody class=\"dbTableTbody\">" .
			 $content . 
			 "</tbody></table>" . $this->wrapperEnd . $this->containerEnd;
	}

	function getLabels($string) {
		$tidyConfig = array(
           'indent'         => true,
           'input-xml'		=> true,
           'output-xhtml'   => false,
           'wrap'           => 200,
           "clean" 			=> true,
           "numeric-entities" => true,
           "quote-nbsp" => true,
           'input-encoding'=>'utf8',
           'output-encoding'=>'utf8',
           'literal-attributes'=> true
    	);

		$this->tidy = new tidy();
		$this->tidy->parseString($string, $tidyConfig, "utf8");
		$this->tidy->CleanRepair();

		$rt = $this->tidy->root();
		$nodes = $rt->child[0]->child;
		$colgroup = '<colgroup>';
		$content = '<thead class="fixedHeader"><tr>';

		foreach ($nodes as $node) {
			//colgroup
			$id = getAttribute ("name", $node->attribute,"undefined");
			$id = str_replace(":","__", $id);
			$colId = "dbCol" . ucfirst($id);
			$thId = "dbTh" . ucfirst($id); 
			$colgroup .= "<col id=\"$colId\" />";
			// th
			$search = getOptional("search", $node->attribute);
			$sorter = getAttribute("sortable", $node->attribute, "true");
			
			if ( $sorter == "false" ) $sorter = sprintf ('{sorter: %s}', $sorter);
			else $sorter = "";

			if ($node->name == "tr") {continue;}
			if ($node->name == "dbbuttons" ) { $search ='search="0"'; $sorter="{sorter: false}"; }
			
			if ( isset ($node->attribute) && array_key_exists("noheader", $node->attribute) ) {
				; // do nothing
			}else if (isset ($node->attribute) && array_key_exists("label", $node->attribute)) {				
				$content .= "<th id='$thId' $search class=\"$sorter\">" . $node->attribute['label'] . '<div class="ui-state-hover ui-corner-all tableContextButton"><span id="buttonName" class="ui-icon ui-icon-triangle-1-s"/>' . "</th>";
			} else {
				$content .="<th id='$thId'></th>";
			}
			
		}
		$colgroup .= '</colgroup>';
		$content .="</tr></thead>";
		return $colgroup . $content;
	}

	function getData($table) {
		$tableDC = Doctrine::getTable($table);
		$GLOBALS['firephp']->log($table,"Default get table:");
		return $this->results = $tableDC->findAll();
	}
}
?>
