<? $id = getAttribute("id",$attributes, "breadcrumbs"); 
$style = getAttribute("style",$attributes, "");
?>

<div id="<?=$id?>" style="position: fixed; height: 20px; right: 210px; top:10px;padding-right: 0px; text-align: right; vertical-align: middle; color: rgb(0, 0, 0); <?=$style?>">
Sie befinden sich hier:&nbsp;	
<?
$path = $dsl->get("content","path");
for ( $i =0; $i < count($path); $i++ ) {
	$entry = $path[$i];
	
	if ($i != count ($path)-1 && breadcrumbTranslate( $entry['name'] . "_show_sub") === false) continue;

	echo '<a href="' . $entry['url'] . '" border="0">' . breadcrumbTranslate($entry['name']) . "</a>";
	if ( $i != count($path)-1 ) { echo "&nbsp;&gt;&gt;&nbsp;"; }
}
?>

</div>