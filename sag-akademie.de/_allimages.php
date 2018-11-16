<? function htmlImages($dir="/img/") {
	$ret = "";
	$d = dir(dirname(__FILE__) ."/". $dir);
	
	while (false !== ($entry = $d->read())) {
		if ( $entry == ".." || $entry == "." || $entry == ".svn") continue;
		else if ( is_dir( $d->path . "/" .$entry )) {
			$ret .= htmlImages ( $dir . "/".$entry );
		} else if ( substr_count($entry, ".jpg")  || substr_count($entry, ".png") || substr_count($entry, ".gif" ) ) {
			$ret .= sprintf ( '<img src="%s" />', $dir ."/". $entry);	
		}	
	}
	$d->close();
	return $ret;
}

?>
<html>
	<body>
		<?=htmlImages()?>;
				<?=htmlImages("/css/")?>

	</body>
</html>