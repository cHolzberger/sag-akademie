<?php
include_once("lib/jsmin/CSSImportProcessor.php");

$firephp = FirePHP::getInstance(true);

class SiteCSS extends MosaikElement {
	public $ignoreChildren;
	public $resourceFactory;
	
	function init() {
		$this->basename = dirname(dirname(dirname(__FILE__)));
		$this->ignoreChildren = true;
		$this->resourceBundle = CSSResourceFactory::getInstance();
		$this->srvCachePath = MosaikConfig::getVar("srvCachePath");
		$this->webCachePath = MosaikConfig::getVar("webCachePath");
		$this->version = MosaikConfig::getVar("version");
	}
	
	function debugSource($attributes, $value) { 
		if ( array_key_exists("loadnow", $attributes) ) {
			return '';
		} else if ( array_key_exists("src", $attributes) ){
			$media =getAttribute("media",$attributes,"screen");
			return sprintf ( '<link href="%s" type="text/css" rel="stylesheet" media="%s"/>%s' , trim($attributes['src']), trim($media),"\n");
		} 
	}
	
	function optimizeLocalCSS($key) {
		$cacheFile = "site-{$key}-{$this->version}.css";

		$optFile = $this->srvCachePath . "/" . $cacheFile;
		
		// lets check if anything newer than our file is there
		
		if ( !file_exists ( $optFile )) {
			extract ($GLOBALS['debug']);
			$firephp->log($new,"generating site.css");
			$externalcss = "";
			foreach ( $this->resourceBundle->get() as $item) {
				// we assume we are running from document root
				$path = dirname ($item['file']) . "/";
				//$firephp->log($path, "currentDir");
				$options = array("prependRelativePath" => $path );
				if ( $item['file'] != NULL ) 
					$externalcss .= Minify_CSS::minify( (Minify_ImportProcessor::process($this->basename . $item['file'])),$options);
			}
			file_put_contents ($optFile, $externalcss);
		}
		
		return $cacheFile;
	}
	
	function getDir($dir) {
		$arr =array();
		$d = dir(WEBROOT . $dir);
		while ( false !== ($entry = $d->read())) {
			if ( substr($entry,-4) == ".css" ) {
				$arr [] = $dir . $entry;
				$this->resourceBundle->add( $dir . $entry ); 
			} 
		}
		return $arr;
	}

	function source($attributes, $value) {
		/* dynamic site scripts debug */
		if ( config::isDebug("sitescript") && array_key_exists("dir", $attributes) ) {
			$content = "";
			$arr = $this->getDir($attributes['dir']);
			foreach ( $arr as $val ) {
				$content .= $this->debugSource(array("src"=>$val),"");
			}
			return $content;
		} else if (config::isDebug("sitecss")) return $this->debugSource($attributes, $value);
		
		
		/* include the scripts */
		if ( array_key_exists("loadnow", $attributes) ) {
			$key = $attributes['key'];
			$fname = $this->optimizeLocalCSS($key);		
			$media =getAttribute("media",$attributes,"screen");
			$tag = sprintf ( '<link href="%s/%s" type="text/css" rel="stylesheet" media="%s"/>%s' , $this->webCachePath, $fname, $media, "\n");
			return $tag;
		} else if ( array_key_exists("dir", $attributes) ){
			$arr = $this->getDir($attributes['dir']);
			return "<!-- included css " . $attributes['dir'] . "-->";
		} else if ( array_key_exists("src", $attributes) ){
			$this->resourceBundle->add ($attributes['src']);
			return "<!-- minifyed css " . $attributes['src'] . "-->";
		} else {
			$this->resourceBundle->add (NULL, $value);
		}
	}
}
?>
