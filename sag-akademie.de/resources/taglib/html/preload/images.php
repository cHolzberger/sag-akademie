<? function preload_mod_htmlImages($dir="/img") {
	$ret = "";
	$d = dir(WEBROOT ."/". $dir);
	
	while (false !== ($entry = $d->read())) {
		if ( $entry == ".." || $entry == "." || $entry == ".svn") continue;
		else if ( is_dir( $d->path . "/" .$entry )) {
			$ret .=  preload_mod_htmlImages ( $dir . "/" .$entry );
		} else if ( substr_count($entry, ".jpg")  || substr_count($entry, ".png") || substr_count($entry, ".gif" ) ) {
			$ret .= sprintf ( '<img src="%s" />', $dir ."/". $entry);	
		}	
	}
	$d->close();
	return $ret;
}

?>
<div style="display: none;">
<?=preload_mod_htmlImages()?>
<?=preload_mod_htmlImages("/css")?>
</div>