<?php
$firephp = FirePHP::getInstance(true);

class XDbSelect extends MosaikElement {
	var $conn;

	function init() {
		$this->ignoreChildren = true;
	}
	function source($attributes, $value) {
		$spacer = " ";
		$style = "";
		$key = "";
		if (array_key_exists ("style", $attributes)) {
			$style = $attributes['style'];
		}

		if (array_key_exists ("fromTableDisplaySpacer", $attributes)) {
			$spacer = $attributes['fromTableDisplaySpacer'];
		}
		
		$fromArray = $attributes['fromArray'];
		$fromDisplay = $attributes['fromTableDisplay'];
		$fromKey = $attributes['fromTableKey'];
		$preselect = $attributes['preselect'];

		$targetKey = $attributes['name'];

		$selected = "";
		$selected = $this->datasourceList->get("dbtable", $fromKey);
		
		$results = array();
		$results = $this->datasourceList->get("dbtable", $fromArray);
		
		$content = "";
		$pre="";
		$elementId="";
		
		if ( array_key_exists("id", $attributes)) {
			$elementId = $attributes['id'];
		} else {
			$elementId = sprintf("input_%s", str_replace("]","",str_replace("[","_",$fromKey)));
		}
		
		$preselect= "x!x";
		if ( array_key_exists("preselect", $attributes)) {
			$preselect = $this->datasourceList->get("dbtable", $attributes['preselect']);
		} 
		
		if (array_key_exists ("label", $attributes)) {
			$pre =sprintf('<label class="label" for="%s">%s</label>', $elementId, $attributes['label']);
		}
		
		$count = 1;
		if ( is_array($results)) foreach ($results as $result) {
			$result['count']=$count;
			
			if ( $result[$fromKey] == $preselect || $preselect == "") {
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
		 
		return "<div class=\"dbselect\">$pre<select id=\"$elementId\" style=\"$style\" class=\"dbselect\" name=\"$targetKey\">\n". $content ."\n</select></div>";
	}
}
?>
