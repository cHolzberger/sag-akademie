<?php
$firephp = FirePHP::getInstance(true);


class MCase extends MosaikElement { 
	var $conn;

	function init() {
		$this->ignoreChildren = true;
	}
	function tmpPageReader($copyFromGlobalDS = null, $clone=false) {
		$pageReader = new MosaikPageReader(MosaikPageReaderConfig::getDefault());
		$pageReader->initElements();
		$ds = new MosaikDatasource ("switch");
		$pageReader->addDatasource ( $ds );
		
		if ( $clone ) {
			$pageReader->datasourceList->copyFrom($this->datasourceList);
		}
		if ( $copyFromGlobalDS ) {
			$data = $this->datasourceList->get("dbtable", $copyFromGlobalDS);//FIXME: dbtable should not be hardcoded
			$ds->add("key", $copyFromGlobalDS);
			$ds->add("ds", "dbtable");
			$ds->add("value", $data);
			$ds->add("matched", FALSE);
		}
		return $pageReader;
	}

	function source($attributes, $innervalue) {
		if ( $this->datasourceList->get("switch", "matched") === TRUE) { 
			return;
		}
		//$GLOBALS['firephp']->log($innervalue,"no matched");
		if ( is_array( $attributes) && array_key_exists("value", $attributes)) {
			$match = $attributes['value'];
			$match = str_replace("#empty","", $match);
		} else { 
			$match = "";
		}
		
		$value = $this->datasourceList->get("switch", "value");
		
		if ( $value == $match || $match == "*" || (is_array($value) && count ($value) == 0)) {
			$this->datasourceList->get("switch")->add("matched", TRUE);
		
			$tv = trim( $innervalue );
			if ( empty ( $tv ) ) { return ""; }

			
			$pageReader = $this->tmpPageReader(null, true);
			$pageReader->loadString($innervalue);
			
			return $pageReader->output->get();
		} else { 
			return "";
		}
	}
}

class MSwitch extends MosaikElement {
	var $conn;

	function init() {
		$this->ignoreChildren = true;
	}
	function tmpPageReader($copyFromGlobalDS = null, $clone) {
		$pageReader = new MosaikPageReader(MosaikPageReaderConfig::getDefault());
		$pageReader->initElements();
		$ds = new MosaikDatasource ("switch");
		$pageReader->addDatasource ( $ds );
		
		if ( $clone ) {
			$pageReader->datasourceList->copyFrom($this->datasourceList);
		}
		//$this->datasourceList->log();

		if ( $copyFromGlobalDS ) {
			$data = $this->datasourceList->get("dbtable", $copyFromGlobalDS);//FIXME: dbtable should not be hardcoded
			$ds->add("key", $copyFromGlobalDS);
			$ds->add("ds", "dbtable");
			$ds->add("value", $data);
			$ds->add("matched", FALSE);
		}

	//	$pageReader->datasourceList->log();
		return $pageReader;
	}

	function source($attributes, $value) {
		$key = $attributes['key'];
			
		$pageReader = $this->tmpPageReader($key, true);
		//$GLOBALS['firephp']->log($value,"switch");
		$pageReader->loadString($value);
		return $pageReader->output->get();
	}
}
?>
