<?
$rfn = "";
$srvimagepath = MosaikConfig::getVar("srvImagePath");
$webimagepath = MosaikConfig::getVar("webImagePath");
$VARIANT_IMG="img";  
$VARIANT_TEXT="text";
$var = $VARIANT_IMG; 
$textStyle = "";

if ( $attributes['label'] ) {
	$label = $attributes['label'];
	$var = $VARIANT_TEXT;
	$textStyle = "line-height: 24px; vertical-align: middle; text-align: center; color: white; padding: 0px 7px;
	font-weight: normal;
	border-left: 1px solid white; border-right: 1px solid white;
	border-image: linear-gradient(to bottom, #0f0e5b, white) 1 100%;
	";
} else if (file_exists($srvimagepath . "{$attributes['id']}.png")) {
 	$fn = $webimagepath . "{$attributes['id']}.png";
 	$afn = str_replace("nav_", "nav_a_", $fn);
	$rfn =  $srvimagepath . "{$attributes['id']}.png";

 } else {
 	$fn = $webimagepath . "{$attributes['id']}.jpg";
 	$afn = str_replace("nav_", "nav_a_", $fn);
 	$rfn =  $srvimagepath . "{$attributes['id']}.jpg";
 }

$img = getimagesize($rfn);
$menuItemStyle = $style = sprintf('style="width: %spx !important; height: %spx !important; z-index: 999; "', $img[0], $img[1]);
$hidden = getAttribute("hidden", $attributes, false );
if ( $hidden ) {
	$menuItemStyle = sprintf('style="width: %spx !important; height: %spx !important; display: none; z-index:999;"', $img[0], $img[1]);
}

 ?>
 <span class="menuItem" <?=$menuItemStyle?>>
 	<a href="<?=$attributes['href']?>" class="nav" id="<?=$attribute['id']?>" <?=$style?>>
	 <? if ($var == $VARIANT_IMG): ?> 
 		<img id="<?=$attribute['id']?>_img" class="nav" alt="" src="<?=$fn?>" border="0" 
			onmouseout="document.getElementById('<?=$attribute['id']?>_img').src='<?=$fn?>';" 
			onmouseover="document.getElementById('<?=$attribute['id']?>_img').src='<?=$afn?>';"
			<?=$style?>
			<?=$img[3] //width and height?>  
		/>
	<? else: ?>
		<div src="#" id="<?=$attribute['id']?>_img" class="nav" alt="" border="0" 
			style="<?=$textStyle?>">
		<?=$label?>
		</div>
	<? endif ?>
	</a>
 </span>
