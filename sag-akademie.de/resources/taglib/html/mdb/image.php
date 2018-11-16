<?
$mpath = (getRequiredAttribute ("mpath", $attributes));
$inputvalue = $dsl->get("dbtable", $mpath);
// convert the input value

$suffix = "";
if (array_key_exists("suffix", $attributes)) {
	$suffix = "&nbsp;".$attributes['suffix'];
}

$float = getOptional("float", $attributes);
$class = getOptional("class", $attributes);
$style = getOptional("style", $attributes);
$width = getOptional( "width", $attributes);

$link = "";
$alink = "";
$jslink = "";
$alinkend='';
/* ?!?!??*/
if (array_key_exists("link", $attributes)) {
	$id = $dsl->get("dbtable", $attributes['linkOn']);
	$linksuffix = $attributes['linkSuffix'];
	$base = $attributes['link'];
	$jslink="onClick=\"document.location='$base$id$linksuffix'\"";
	$alink="<a class=\"dba\" href=\"$base$id$linksuffix\" border=0>";
	$alinkend ="</a>";
}

if (array_key_exists("dblink", $attributes)) {
	$url = $dsl->get("dbtable", $attributes['dblink']);
	$linksuffix = "";
	$base = "";
	$alink="<a class=\"dba\" href=\"$url\" border=0>";
	$alinkend ="</a>";
}

$height = getOptional( "height" );
$uid = md5(microtime());
$inputvalue = $inputvalue . "?" . $uid;
$noImgLink = array_key_exists("noImgLink", $attributes);
$noPadding = array_key_exists("noPadding", $attributes);
$padding = "";
if ( $noPadding == False ) {
    $padding= " margin: 10px; margin-left: 15px; ";
}
?>
<div class="dbinput <?=getAttribute( "class", $attributes); ?>" style="<?=getAttribute ("style", $attributes); ?>">

    	<? if (array_key_exists("label",$attributes)) { ?>
		<label class="label" ><?=$attributes['label']?></label>
	<? } else { ?>
		<label class="label" >&nbsp;</label>
	<? } ?>
<?=$alink?>
    <div style="float: left; background-color: #efefef; border: 1px solid #cdcdcd; display: block; <?=$padding?> margin-bottom: 10px; padding: 5px; -moz-box-shadow: 2px 2px 10px 0 rgba(0, 0, 0, 0.7);">
	<? if ($noImgLink == False) { ?><a href="<?=$inputvalue?>" target="_blank"><? } ?>
	    <img <?=$height?> <?=$width?> src="<?=$inputvalue ?>" border="0" <?=$float?> style="border: 1px solid #adadad;" /><?=$suffix?>
	<? if ($noImgLink == False) { ?> </a><? } ?>
    </div>
<?=$alinkend?>
</div>
