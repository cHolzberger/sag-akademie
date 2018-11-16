<?php
$firephp = FirePHP::getInstance(true);

class DbSelect extends MosaikElement {
	var $conn;

	function init() {
		$this->ignoreChildren = true;
	}
	
	function dbselectGetData( $fromTable ) {
		$tableDC = Doctrine::getTable($fromTable);
		$results = $tableDC->findAll();
		$res = array();
		foreach ( $results as $result) {
			$result->refreshRelated();
			$res[] = $result->toArray(true);
		}
		
		return $res;
	}

	function source($attributes, $value) {
		$spacer = " ";
		$style = "";
		$key = "";

		$rdonly = "";
		if ( array_key_exists("readonly",$attributes) ) {
		    $rdonly='readonly="readonly" disabled="disabled"';
		}
		if (array_key_exists ("style", $attributes)) {
			$style = $attributes['style'];
		}

		if (array_key_exists ("fromTableDisplaySpacer", $attributes)) {
			$spacer = $attributes['fromTableDisplaySpacer'];
		}
		
		$fromTable = $attributes['fromTable'];
		$fromDisplay = $attributes['fromTableDisplay'];
		$fromKey = $attributes['fromTableKey'];
		
		if ( array_key_exists("dbtable", $attributes)) {
				$table = $attributes['dbtable'];
		} else {
			$table = $this->datasourceList->get('dbtable','dbtable');
		}
		$class = $this->datasourceList->get('dbtable','dbclass');
		
		if ( !empty ($table)) {
			$key = $class . ":".$attributes['name'];
		} else {
			$key = $attributes['name'];
		}
		
		$fName = "";
		if (isset ($table) && !empty ($table)) {
			$fName = $table."[".$attributes['name']."]";
		} else {
			$fName = $key;
		}
		
		$selected = "";
		if (array_key_exists ("selectFromGet", $attributes) ) {
			if (array_key_exists("$key", $_GET)) $selected = $_GET[$key];
		} else {
			$selected = $this->datasourceList->get("dbtable", $key);
		}
		
		$results = array();
		
		if ( array_key_exists ( "dbselectGet", $GLOBALS )) {
			$results = $GLOBALS['dbselectGet']->dbselectGetData($fromTable);		
		} else { 
			$results = $this->dbselectGetData($fromTable);
		}
		

		$content = "";
		$pre="";
		$elementId = sprintf("input_%s", str_replace("]","",str_replace("[","_",$fName)));
		
		if (array_key_exists ("label", $attributes)) {
			$pre =sprintf('<label class="label" for="%s">%s</label>', $elementId, $attributes['label']);
		}
		
		$count = 1;
		foreach ($results as $result) {
			$result['count']=$count;
			if ( $result[$fromKey] == $selected || $selected == "") {
				$content .= "<option selected=\"selected\" value='".$result[$fromKey] ."'>";
				$selected ="ignoireMyValue";
			} else {
				$content .= "<option value='". $result[$fromKey] ."'>";
			}
			$isfirst = true;
			
			foreach (explode (",", $fromDisplay) as $fdkey) {
				$r = $result;
				if (!$isfirst) {
					$content .= utf8Htmlentities(" ".$spacer. " ");
				}
				
				foreach ( explode(":", $fdkey) as $fk) {
					$r =  $r[$fk];
				}
				$content .= utf8Htmlentities($r);
				$isfirst = false;
			}

			$content .= "</option>";
			$count ++;
		}
		 
		return "<div class=\"dbselect\">$pre<select id=\"$elementId\" style=\"$style\" class=\"dbselect\" name=\"$fName\" $rdonly>\n". $content ."\n</select></div>";
	}
}
?>
