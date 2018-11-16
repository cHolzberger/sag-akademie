<?php
$firephp = FirePHP::getInstance(true);

function MForEach_Compare($e1, $e2) {
    $e1 = m_flat_array($e1); // quick fix
    $e2 = m_flat_array($e2);
	if ( is_string($e1[MForEach::$compareBy])) {
	    return strcmp($e1[MForEach::$compareBy],$e2[MForEach::$compareBy]);
	} else {
	    return $e1[MForEach::$compareBy]<$e2[MForEach::$compareBy] ? -1:1;
	}

    }

class MForEach extends MosaikElement {
    var $conn;
    static $compareBy=null;

    function init() {
		$this->ignoreChildren = true;
    }

    function source($attributes, $value) {
		$aId = $attributes['fromArray'];
		$arr = $this->datasourceList->get("dbtable", $aId);//FIXME: dbtable should not be hardcoded
		$orig = &$this->datasourceList->get("dbtable")->store;
		//	$GLOBALS['firephp']->info($arr,"$aId");

		$onEmptyStr = "";

		//MosaikDebug::msg($arr,"Array");
		if ( isset ($attributes['onEmpty'])) {
			$onEmptyStr = $attributes['onEmpty'];
		}

		//MosaikDebug::msg(count($arr),"ArrayLength");
		if (  count($arr) == 0 ) {
			MosaikDebug::msg($onEmptyStr,"Return");
			return "<span class='foreachEachEmpty'>" . $onEmptyStr . "</span>";
		}

		$pageReader = new MosaikPageReader(MosaikPageReaderConfig::getDefault());
		$pageReader->initElements();
		$content = "";

		if ( isset ($attributes['sortOn']) ) {
			MForEach::$compareBy = $attributes['sortOn'];
		}

		$ds = new MosaikDatasource ("dbtable");
		$pageReader->addDatasource ( $ds );

		$count = 1;
		if ( is_array($arr) ) {
			if ( MForEach::$compareBy != null ) {
				usort($arr, "MForEach_Compare");
			}

			if ( isset($attributes['sortOrder']) && strtolower($attributes['sortOrder']) == "desc") {
				$arr = array_reverse ($arr);
			}
			foreach ($arr as $key => $result) {
				//if ($key == "itemCount" ) { continue; }
				//else if ($key == "hasMore" ) { continue; }

				if (array_key_exists("basehref", $attributes)) { // fixme not so good
					$content .= '<a href="'.$attributes['basehref'] . $result[$attributes['linkId']].'">';
				}

				$ds->set($result);
				$ds->add("count",$count);
				//$GLOBALS['firephp']->log($value);
				$pageReader->loadString($value);
				$content .=  $pageReader->output->get();
				$pageReader->output->clear();
				if (array_key_exists("basehref", $attributes)) { // fixme not so good
					$content.="</a>";
				}
				$count ++;
			}
			
		} else {
			$content = "";
		}
		$ds->set($orig);
		return $content;
    }
}
?>
