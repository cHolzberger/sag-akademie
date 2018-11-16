<? // gears manifest php :-D ?>
<? function findImages($dir="img/") {
	$ret = "";
	$d = dir(dirname(__FILE__) ."/". $dir);
	
	while (false !== ($entry = $d->read())) {
		if ( $entry == ".." || $entry == "." || $entry == ".svn") continue;
		else if ( is_dir( $d->path . "/" .$entry )) {
			$ret .= findImages ( $dir . "/". $entry );
		} else if ( substr_count($entry, ".jpg")  || substr_count($entry, ".png") || substr_count($entry, ".gif" ) || substr_count($entry, ".swf"  )) {
			$ret .= sprintf ( '{"url": "%s" },', $dir . "/". $entry);	
		}	
	}
	$d->close();
	return $ret;
}
$version = file_get_contents ("version.txt");
$cache =  "resources/cache/gears-$version.json" ;

if ( file_exists($cache)) echo file_get_contents($cache);
else {
    ob_start();
?>
{
  "betaManifestVersion": 1,
  "version": "SAG-PHPAdmin-<?=file_get_contents ("version.txt")?>",
  "entries": [
	  { "url": "resources/scripts/google/gears_init.js"},
	  <?=findImages("/img");?>
	  <?=findImages("/css");?>
	  <?=findImages("/resources/flex");?>
	  { "url": "/admin/admin;iframe"}
    ]
}
<? 
    $manifest= ob_get_contents();
    ob_end_flush();
    file_put_contents($cache, $manifest);
}

?>