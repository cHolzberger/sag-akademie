<?php

$dateFormat = "%d.%m.%Y";
$shortDateFormat = "%e.%m.%y";
$shortDatetimeFormat = "%d.%m.%y %H:%M:%S";
$datetimeFormat = "%d.%m.%Y %H:%M:%S";

$mysqlDateFormat = "%Y-%m-%d";
$mysqlDatetimeFormat = "%Y-%m-%d %H:%M:%S";

// convertStrings
function createUuid() {
	return md5(uniqid(rand(), true));
}

function utf8Htmlentities($str) {
	return htmlentities($str, ENT_QUOTES, "UTF-8");
}

function utf8Htmlspecialchars($str) {
	$str = htmlspecialchars($str, ENT_QUOTES, "UTF-8");
	$str = str_replace("ß", "&szlig;", $str);
	$str = str_replace("ä", "&auml;", $str);
	$str = str_replace("ü", "&uuml;", $str);
	$str = str_replace("ö", "&ouml;", $str);
	$str = str_replace("Ä", "&Auml;", $str);
	$str = str_replace("Ü", "&uuml;", $str);
	$str = str_replace("Ö", "&ouml;", $str);
	return $str;
}

function convertString($value, &$attributes) {
	if (array_key_exists("converter", $attributes)) {
		$converter = $attributes['converter'];
		// chain commands
		foreach (explode(":", $converter) as $func) {
			$value = $func($value);
		}
	}
	return $value;
}

// converter functions
function currentMysqlDatetime() {
	global $mysqlDatetimeFormat;
	return strftime($mysqlDatetimeFormat);
}

function currentMysqlDate() {
	global $mysqlDateFormat;
	return strftime($mysqlDateFormat);
}

function currentLocalDate() {
	global $dateFormat;
	return strftime($dateFormat);
}

function currentLocalDatetime() {
	global $datetimeFormat;
	return strftime($datetimeFormat);
}

function akQuelleToString($str) {
	if ($str == 0)
		return "Akquise";
	return "Kunden";
}

function dbColor($str) {
	return str_replace("0x", "#", $str);
}

function statusToStr($str) {
	switch ($str) {
		case 0 :
			return "Gebucht";
			break;
		case 1 :
			return "Gebucht";
			break;
		case 2 :
			return "Storniert";
			break;
		case 3 :
			return "Umgebucht";
			break;
		case 4 :
			return "Abgesagt durch SAG";
			break;
		case 5 :
			return "Nicht teilgen.";
			break;
	}
}

function bestaetigtToStr($value) {
	if (!empty($value))
		return "Best&auml;tigt";
	else
		return "Nicht Best&auml;tigt";
}

function jaNeinToStr($value) {
	if (!empty($value) || $value != 0)
		return "Ja";
	else
		return "Nein";
}

global $_monate;
$_monate = array("Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");

function monatToString($value) {
	global $_monate;

	return $_monate[$value - 1];
}

function parseLocalDate($date) {
	global $datetimeFormat;
	global $shortDatetimeFormat;
	global $shortDateFormat;
	global $dateFormat;
	$aResult = "";
	$length = strlen($date);

	if ($length >= 6 && $length <= 9) {

		$data = explode(".", $date);
		$day = intval($data[0]);
		$month = intval($data[1]);
		$year = intval($data[2]);
		$str = "";
		if ($day < 10)
			$str .= "0$day";
		else
			$str .= $day;

		$str .= ".";

		if ($month < 10)
			$str .= "0$month";
		else
			$str .= "$month";

		$str .= ".";

		if ($year < 10)
			$str .= "200$year";
		else if ($year < 30)
			$str .= "20$year";
		else if ($year < 100)
			$str .= "19$year";
		else
			$str .= "$year";

		$aResult = strptime($str, $dateFormat);
	} else if (strlen($date) == 10)
		$aResult = strptime($date, $dateFormat);
	else if (strlen($date) == 20)
		$aResult = strptime($date, $shortDatetimeFormat);
	else
		$aResult = strptime($date, $datetimeFormat);
	return $aResult;
}

//mysql date and datetime converters
function mysqlDatetimeFromLocal($date) {
	global $mysqlDatetimeFormat;
	global $datetimeFormat;
	global $shortDatetimeFormat;
	global $shortDatetimeFormat;
	if (empty($date))
		return null;
	$aResult = parseLocalDate($date);

	$nParsedDateTimestamp = mktime($aResult['tm_hour'], $aResult['tm_min'], $aResult['tm_sec'], $aResult['tm_mon'] + 1, $aResult['tm_mday'], intval($aResult['tm_year']) + 1900);

	return strftime($mysqlDatetimeFormat, $nParsedDateTimestamp);
}

function mysqlDatetimeToLocal($date) {
	if ($date == "1970-01-01" || $date == "0000-00-00" || $date == "0000-00-00 00:00:00" || empty($date))
		return "";
	global $datetimeFormat;
	return strftime($datetimeFormat, strtotime($date));
}

function mysqlDateFromLocal($date) {
	global $dateFormat;
	global $mysqlDateFormat;
	if (empty($date))
		return null;
	$aResult = parseLocalDate($date);
	$nParsedDateTimestamp = mktime($aResult['tm_hour'], $aResult['tm_min'], $aResult['tm_sec'], $aResult['tm_mon'] + 1, $aResult['tm_mday'], intval($aResult['tm_year']) + 1900);
	return strftime($mysqlDateFormat, $nParsedDateTimestamp);
}

function mysqlDateToLocal($date) {
	global $dateFormat;
	if ($date == "1970-01-01" || $date == "0000-00-00" || $date == "0000-00-00 00:00:00" || empty($date))
		return "";
	//$GLOBALS['firephp']->log($date, "mysqlDateToLocal");
	return strftime($dateFormat, strtotime($date));
}

function mysqlDateFromSeconds($seconds) {
	global $mysqlDateFormat;
	return strftime($mysqlDateFormat, $seconds);
}

function mysqlDateToSeconds($date) {
	global $dateFormat;
	if ($date == "0000-00-00" || $date == "0000-00-00 00:00:00" || empty($date))
		return 0;
	
	$info = explode ("-", $date);
	//$GLOBALS['firephp']->log($date, "mysqlDateToLocal");
	return mktime(0,0,0,$info[1], $info[2],$info[0]);
}

function priceToDouble(&$price) {
	if ( !empty ($price) && substr_count(",", $price) != 0 ) {
		$price = floatval(str_replace(",", ".", str_replace(".", "", $price)));
	}
	return $price;
}

function anredeToString($anrede) {
	if ($anrede == 0)
		return "Herr";
	return "Frau";
}

setlocale(LC_MONETARY, 'de_DE');

function euroPreis($data) {
	//$data = str_replace(".",",", $data);
	if (substr_count($data, ",") == 0) {
		$f = (float)$data;
		return str_replace(".", ",", sprintf('%0.2f', $f));
	}
	return $data;
}

function floatToString($data) {
	//$data = str_replace(".",",", $data);
	if (substr_count($data, ",") == 0) {
		$f = (float)$data;
		return str_replace(".", ",", sprintf('%0.2f', $f));
	}

	return $data;
}

function intToString($data) {
	//$data = str_replace(".",",", $data);
	if (substr_count($data, ",") == 0) {
		$f = (float)$data;
		return str_replace(".", ",", sprintf('%i', $f));
	}

	return $data;
}

function plz($data) {
	if (empty($data))
		return "";

	return sprintf("%05u", intval($data));
}

function AutoLink($text) {
	// WEB
	$text = preg_replace("/(?!\")([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i", "<a href=\"$1\">$1</a>", $text);

	// MAIL
	$text = preg_replace("/(?!mailto:)([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i", "<a href=\"mailto:$1\">$1</a>", $text);
	return $text;
}

function boolToString($val) {
	if ($val) {
		return "Ja";
	}
	return "Nein";
}

function m_flat_array($arr) {
	$ret = array();

	if (is_array($arr) || is_object($arr))
		foreach ($arr as $k => $a) {
			$key = $k;

			if (is_array($a) || is_object($a)) {
				$key .= ":";
				$child = m_flat_array($a);
				foreach ($child as $kc => $c) {
					$ret[$key . $kc] = $c;
				}
			} else {
				$ret[$key] = $a;
			}
		}
	else {
		$ret = $arr;
	}
	return $ret;
}

include_once ("lib/Mosaik/MosaikHtmlDropDownBuilder.php");

class Resolver {

	static $_resTable = array();

	static function addMap($field, $table, $labelField = 'name', $component = "MosaikHtmlDropDownBuilder") {
		Resolver::$_resTable[$field] = array('table' => $table, 'labelField' => $labelField, "component" => $component);
	}

	static function getValue($field, $value) {
		$table = Resolver::$_resTable[$field]['table'];
		$field = Resolver::$_resTable[$field]['labelField'];
		return Doctrine::getTable($table)->find($value)->$field;
	}

	static function hasKey($key) {
		if (array_key_exists($key, Resolver::$_resTable))
			return true;
		return false;
	}

	static function getComponent($field, $value, $args = "") {
		$component = Resolver::$_resTable[$field]['component'];
		$table = Resolver::$_resTable[$field]['table'];

		$cmp = new $component($field, $value, $args);
		$cmp->setPossibleValues(Doctrine::getTable($table)->findAll(Doctrine::HYDRATE_ARRAY));
		return $cmp->toHtml();
	}

}

function advancedSearchAsString() {
	$data = "";

	if (isset($_POST['rules'])) {
		foreach ($_POST['rules'] as $key => $value) {
			if ($value == "or") {
				$data .= "Oder ";
			} else {
				$exp = explode(";", $value);
				$val_array = explode(":", $exp[0]);
				if ($exp[1] == "LIKE") {
					$exp[1] = "enthält";
				}
				$data .= $val_array[0] . " " . $exp[1] . " " . $exp[2] . " | ";
			}
		}
	} else {
		$data = "Keine Suchkriterien.";
	}

	return $data;
}

function noStyle($content) {
	return preg_replace("/(<\/?)(\w+) style=([^>]*>)/", "\\1\\2 >", $content);
}

if (!function_exists('apache_request_headers')) {
	eval('
        function apache_request_headers() {
            foreach($_SERVER as $key=>$value) {
                if (substr($key,0,5)=="HTTP_") {
                    $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                    $out[$key]=$value;
                }
            }
            return $out;
        }
    ');
}

/**
 * send redirect header and exit!
 * @param $url String TargetURL
 * */
function instantRedirect($url) {
	header('Location: ' . $url);
	exit(0);
}

class NumberFormat {

	static function toNumber($value) {
		priceToDouble($value);
		return $value;
	}

}

/**
 * Filtert den uebergebenen Array auf Felder die auch in der Tabelle enthalten sind
 *
 * @param String $table Tabellen name
 * @param Array $data Data z/b Post data
 */
function mergeFilter($table, $data) {
	$columns = Doctrine::getTable($table)->getColumns();
	$newData = array();
	foreach ($columns as $col => $colInfo) {
		if (array_key_exists($col, $data)) {
			qlog("------------------->");
			qlog($colInfo['type']);
			switch ( $colInfo['type']) {
				case "date":
					$newData[$col] = $data[$col] == "0000-00-00" ? null : $data[$col];
					break;
				case "datetime":
					$newData[$col] = $data[$col] == "0000-00-00 00:00:00" ? null : $data[$col];
					break;
				case "float" :
					$newData[$col] = floatval($data[$col]);
					break;
				default :
					$newData[$col] = $data[$col];
			}
		}
	}
	return $newData;
}



// usefull for decoiding _POST with some json info
function solveJson(&$arr) {

	foreach ($arr as $key => $data) {
		if (is_string($data)) {// may be json
			if ((substr($data, -1, 1) == "]" && substr($data, 0, 1) == "[") || (substr($data, -1, 1) == "}" && substr($data, 0, 1) == "{")) {
				// we have json data here
				$arr[$key] = json_decode($data);
			}
		}
	}

}

// von der taglib genutzt - eigentlich ueberfluessig?!
function setTagname($name) {
	//	$GLOBALS['firephp']->log($name, "setTagname");
	$GLOBALS['currentTag'] = $name;
}

function unsetTagname() {
	//	$GLOBALS['firephp']->log("unsetTagname");
	$GLOBALS['currentTag'] = "none";
}

/**
 * defaults framework for getOptional
 */
$GLOBALS['getOptionalDefaults'] = array();
$GLOBALS['currentTag'] = "none";

function setDefault($tag, $name, $value) {
	$GLOBALS['getOptionalDefaults'][$tag][$name] = $value;
}

/**
 * "returns the attribute $key as string"
 * @return "returns the attribute $key as string"
 * @param object $key
 * @param object $arr[optional]
 * @param object $default[optional]
 */
function getRequiredAttribute($key, $arr = null, $default = "") {
	if ($arr == null) {
		global $attributes;
		$arr = $attributes;
	}
	$option = getAttribute($key, $arr, $default = "");
	if ($option == "") {// FIXME
		throw new Exception('Undefined Attribute ' . $key);
		return false;
	}
	return $option;
}

/**
 * returns the attribute $key as string
 * @return returns the attribute $key as string
 * @param object $key
 * @param object $arr[optional]
 * @param object $default[optional]
 */
function getAttribute($key, $arr = null, $default = "") {
	if ($arr == null) {
		global $attributes;
		$arr = $attributes;
	}
	if (!$arr)
		return $default;

	if (array_key_exists($key, $arr)) {
		switch($arr[$key]) {
			case "true" :
				return true;
				break;
			case "false" :
			case "0" :
			case "no" :
				return false;
			default :
				return $arr[$key];
				break;
		}
	}

	return $default;
}

/**
 *returns the attribute $name in key=value notation
 * @return returns the attribute $name in key=value notation
 * @param object $name
 * @param object $lattributes[optional]
 * @param object $overrideName[optional]
 */
function getRequired($name, $lattributes = null, $overrideName = null) {
	if ($lattributes == null) {
		global $attributes;
		$lattributes = $attributes;
	}
	$option = getOptional($name, $lattributes, $overrideName);
	if ($option == "") {
		throw new Exception('Undefined Value ' . $name);
		return False;
	}
	return $option;
}

/**
 * returns the attribute $name in key=value notation
 * @return: returns the attribute $name in key=value notation
 */
function getOptional($name, $lattributes = null, $overrideName = null) {
	if ($lattributes == null) {
		global $attributes;
		$lattributes = $attributes;
	}

	$target = "";
	if ($overrideName == null)
		$overrideName = $name;

	if (array_key_exists($GLOBALS['currentTag'], $GLOBALS['getOptionalDefaults']) && array_key_exists($name, $GLOBALS['getOptionalDefaults'][$GLOBALS['currentTag']])) {
		$target = sprintf('%s="%s"', $overrideName, $GLOBALS['getOptionalDefaults'][$GLOBALS['currentTag']][$name]);
	}

	if (is_array($lattributes) && array_key_exists($name, $lattributes)) {

		$target = sprintf('%s="%s"', $overrideName, $lattributes[$name]);
	}

	return $target;
}

/* SOME CONFUSE HELPERS */
function getCssAttr($key, $arr, $default = "") {
	$ret = "";
	$GLOBALS['firephp']->log("USE OF DEPRECATED FUNCTION getCssAttr");
	if (is_array($key)) {
		foreach ($key as $style => $default) {
			$ret .= getAttr($style, $arr, $default);
		}
	}
	return $ret;

}

function getAttrClass($arr, $default = "") {
	$GLOBALS['firephp']->log("USE OF DEPRECATED FUNCTION getAttrClass");
	if (!$arr)
		return $default;

	if (array_key_exists("class", $arr)) {
		return $arr["class"];
	} else {
		return $default;
	}
}

function getAttr($key, $arr, $default = "") {
	$GLOBALS['firephp']->log("USE OF DEPRECATED FUNCTION getAttrClass");
	if (!$arr)
		return "$key: " . $default . ";";

	if (array_key_exists($key, $arr)) {
		return "$key: " . $arr[$key] . ";";
	} else {
		return "$key: " . $default . ";";
	}
}

function getAttrStyle($arr, $default = "") {
	$GLOBALS['firephp']->log("USE OF DEPRECATED FUNCTION getAttrClass");
	if (!$arr)
		return $default;

	if (array_key_exists("style", $arr)) {
		return $arr["style"];
	} else {
		return $default;
	}
}

// END TAGLIB

// cache cleaner
function clearCache ($table, $data, $id) {
	$cache = DBPool::$cacheDriver;
	
	qlog("Clear Cache for Table: $table and id: $id");
	$cache->delete("tbl_${table}_${id}");
	$cache->delete("rpc_${table}_${id}");
	$cache->delete("json_${table}_${id}");

	if ( $table == "Seminar") {
		clearCacheSeminar($id);
		$standort_id = $data['standort_id'];
		$status = $data['status'];
		$cache->delete("seminar_next_{$standort_id}_{$status}");
		$cache->delete("seminar_next_{$standort_id}");
	} else if ( $table == "Buchung") {
		clearCacheBuchung($id);
	} else if ( $table == "SeminarArt") {
		clearCacheSeminarArt($id);
	} else if ( $table == "XSettings" ) {
		$u = Identity::get();
		$ns = $data['namespace'];
		$cache->delete("rpc_{$table}_{$u->getId()}_{$ns}");
	}

	if (array_key_exists("seminar_id", $data)) {
			clearCacheSeminar($data['seminar_id']);
	}		
	
	if (array_key_exists("seminar_art_id", $data)) {
		clearCacheSeminar($data['seminar_art_id']);
}			

	if (array_key_exists("kontakt_id", $data)) {
		clearCacheBuchung($data['kontakt_id']);
	}
	
}
function clearCacheSeminarArt($id) {
	$cache = DBPool::$cacheDriver;

	qlog("Clearing extra for SeminarArt");
	try {
		$cache->delete("rpc_SeminarArt_". $id);
		$cache->delete("seminar_art_future_". $id);
		$cache->delete("seminar_art_next_". $id);
		$cache->delete("seminar_art_more_". $id);
		$cache->delete("seminar_art_planung");
		$cache->delete("seminar_art_detail_".$id);
		$cache->delete("seminar_art_detaillist");

		
	} catch (Exception $e) { 
		qlog("Exception: ". $e);
	} 
}
function clearCacheSeminar($id, $seminar=null) {
	$cache = DBPool::$cacheDriver;
	if ( $seminar ) {
		$date = strtotime($seminar->datum_begin);
		$year = date("Y",$date);
		$cache->delete("seminar_year_{$year}");
		qlog("Deleting cache for year {$year}");
	}
	qlog("Clearing extra for Seminar");
			try {
				$cache->delete("rpc_Seminar_". $id);
				$cache->delete("rpc_ViewInhouseSeminar_". $id);
				$cache->delete("kalender_seminar_" . $id);
				$cache->delete("_kalender_seminar_" . $id);
				$cache->delete("rpc_ViewSeminarPreis_". $id);
			} catch (Exception $e) { 
				qlog("Exception: ". $e);
			} 
}

function clearCacheBuchung($id) {
	$cache = DBPool::$cacheDriver;
	qlog("Clearing extra for Buchung");
	try {
		$cache->delete("buchung_kontakt_detail_" . $id);
		$cache->delete("tmpl_buchung_person_detail_" . $id);
	} catch (Exception $e) { 
		qlog("Exception: ". $e);
	} 
}
/*
function arrayCollect(&$ret, &$value, $idx, ...$fields) {
	if ( !array_key_exists($idx, $value) && $idx != ":MERGE_ARRAY:") {
		throw new Exception("Array incompatible.. missing $idx got " . join(',', array_keys($value)));
	}

	if ( count( $fields ) > 0 ) {
		if ( !array_key_exists($value[$idx],$ret)) {
			$ret[$value[$idx]] = [];
		} 
		return arrayCollect($ret[$value[$idx]], $value, ...$fields);
	}
	if ( $idx == ":MERGE_ARRAY:") {
		if ( !is_array ( $ret )) {
			$ret = [];
		}
		return array_push( $ret, $value );
	} else {
		return $ret[$value[$idx]] = &$value;
	}
}

	function arrayUnpack($arr, ...$fields) {
		for ( $i=0; $i<count($fields); $i++) {
			$arr = array_merge(...$arr);
		}
		qlog("merged");
		qdir($arr);
		return $arr;
	}
*/
Resolver::addMap('land_id', "XLand");
Resolver::addMap('bundesland_id', "XBundesland");
Resolver::addMap('kontaktkategorie', "KontaktKategorie");
Resolver::addMap('angelegt_user_id', "XUser");
Resolver::addMap('geschlecht', "XAnrede");
