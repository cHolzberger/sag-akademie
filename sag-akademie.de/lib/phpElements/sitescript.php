<?php
$firephp = FirePHP::getInstance(true);

function addSiteScript($script) { 
	global $firephp;
	$resourceBundle = JSResourceFactory::getInstance();
	$resourceBundle->add (NULL, $script);
}

class SiteScript extends MosaikElement {
	public $ignoreChildren;
	public $resourceFactory;
	
	function init() {
		$this->basename = dirname(dirname(dirname(__FILE__)));
		$this->ignoreChildren = true;
		$this->resourceBundle = JSResourceFactory::getInstance();
		$this->srvCachePath = MosaikConfig::getVar("srvCachePath");
		$this->webCachePath = MosaikConfig::getVar("webCachePath");
		$this->version = MosaikConfig::getVar("version");
	}
	
	function debugSource($attributes, $value, $tag, $tagclose) { 
		if ( array_key_exists("loadnow", $attributes) ) {
			return '';
		} else if ( array_key_exists("src", $attributes) ){
			return sprintf ( '<'.$tag.' src="%s"></'.$tagclose.'>%s' , trim($attributes['src']), "\n");
		} else {
			return sprintf ( '<'.$tag.'>%s%s%s</'.$tagclose.'>%s', 
				"/* <![CDATA[ */\n",
				trim($value),
				"\n /* ]]> */",
				"\n"
			);
		}
	}
	
	function optimizeLocalJS($key) {
		$cacheFile = "site-{$key}-{$this->version}.js";
		$optFile = $this->srvCachePath ."/".$cacheFile;
		
		if ( !file_exists ( $optFile )) {
			extract ($GLOBALS['debug']);
			$firephp->log($new,"generating site.js");
			$externaljs = "";
			foreach ( $this->resourceBundle->get() as $item) {
				if ( $item['file'] != NULL ) $externaljs .= file_get_contents ($this->basename . $item['file']) . "\n";
			}
			MosaikDebug::infoDebug("Minify: " . $item['file'] );
			file_put_contents ($optFile, JSMin::minify($externaljs));
		}
		
		return $cacheFile;
	}
	
	function getInlineSitescript() {
		$internaljs = "/*** Internal Sitescript ***/" ;
		foreach ( $this->resourceBundle->get() as $item) {
			if ($item['content'] != NULL)
				$internaljs .=  trim($item['content'])."\n";
		}
		return JSMin::minify($internaljs);
	}
	
	function getDir($dir) {
		$arr =array();
		$d = dir(WEBROOT . $dir);
		while ( false !== ($entry = $d->read())) {
			if ( substr($entry,-3) == ".js" ) {
				$arr [] = $dir . $entry;
				$this->resourceBundle->add( $dir . $entry ); 
			} 
		}
		return $arr;
	}

	function source($attributes, $value) {
		/* dynamic site scripts debug */
		$tag = 'script type="text/javascript"';
		$tagclose = "script";
		$tag = getAttribute("hidein",$attributes,$tag);
		$tagclose = getAttribute("hidein",$attributes,$tagclose);
		
		if ( config::isDebug("sitescript") && array_key_exists("dir", $attributes) ) {
			$content = "";
			$arr = $this->getDir($attributes['dir']);
			foreach ( $arr as $val ) {
				$content .= $this->debugSource(array("src"=>$val),"", $tag, $tagclose);
			}
			return $content;
		} else if ( config::isDebug("sitescript") && array_key_exists("loadnow", $attributes)  ) {
			return sprintf ( '<%s>%s%s%s</%s>',
				$tag,
				"/* */ \n", /*FIXME: add <![CDATA[ */
				$this->getInlineSitescript(),
				 "\n/*  */", /*FIXME: ]]>*/
				 $tagclose);
		} else if (config::isDebug("sitescript")) return $this->debugSource($attributes, $value, $tag, $tagclose);
		
		
		/* include the scripts */
		if ( array_key_exists("loadnow", $attributes) ) {
			$js = "";
			$scripttag = "";
			$key = $attributes['key'];
			$internaljs = $this->getInlineSitescript();
			$fname = $this->optimizeLocalJS($key);		
			if (!getAttribute("inline",$attributes,false)) {
				$scripttag = '<'.$tag.' src="'.$this->webCachePath.$fname.'"></'.$tagclose.'>';
			}
			$scripttag .= sprintf ('<'.$tag.'>%s</'.$tagclose.'>', $internaljs);
			return $scripttag;
		} else if ( array_key_exists("dir", $attributes) ) {
			$arr = $this->getDir($attributes['dir']);
			return "<!-- included " . $attributes['dir'] . "-->";
		} else if ( array_key_exists("src", $attributes) ){
			$this->resourceBundle->add ($attributes['src']);
			return "<!-- minifyed " . $attributes['src'] . "-->";
		} else {
			$this->resourceBundle->add (NULL, $value);
		}
	}
}
?>
