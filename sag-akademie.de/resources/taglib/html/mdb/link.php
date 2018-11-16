<?
$id = $dsl->get("dbtable", $attributes['mpath']);

/* BUGFIX! - bitte nicht tags aendern die schon in bestehenden modulen gebraucht werden WICHTIG!! */
$mdata1 = "";
if (!empty ($attributes['mpath1'])) {
    $mdata1 = $dsl->get("dbtable", $attributes['mpath1']);
}

$uuid = "";
if (!empty ($attributes['id'])) {
	$uuid = $attributes['id'] ."_button";
} else {
	$uuid = createUuid(); 
}

$href = getRequired("href", $attributes);
$style = getOptional("style", $attributes,"");

$hrefA = getRequiredAttribute("href", $attributes);

$target = getOptional("target", $attributes);
$class = getOptional("class", $attributes);

$ivalue = $prefix = "";

if (array_key_exists("value", $attributes)) {
	$ivalue = $dsl->get("dbtable", $attributes['value'], $attributes['value']);
} else { 
	$ivalue = $value;
}

if (array_key_exists("prefix", $attributes)) {
	$prefix = $dsl->get("dbtable", $attributes['prefix'], $attributes['prefix']);
	$prefix.="&nbsp;";
}

$tagname = "a";
$urlencode = getAttribute("urlencode", $attributes, true);
$idval = $urlencode == true ? urlencode($id) : $id;
$tagargs = str_replace("#DATA#", $idval, $href);
$hrefA = str_replace("#DATA#", $idval, $hrefA);

if ( $mdata1 != NULL ) {
	$tagargs = str_replace(	"#DATA1#", urlencode($mdata1), $tagargs);
	$hrefA = str_replace("#DATA1#", urlencode($mdata1), $hrefA);
} else {
    $tagargs = str_replace(	"#DATA1#", "", $tagargs);
	$hrefA = str_replace("#DATA1#", "", $hrefA);
}

$onClick = "";

if (array_key_exists("type", $attributes)) {
	if ( $attributes['type'] == "button" ) {
		$tagname = "button";
		$tagargs = sprintf('id="%s"', $uuid);
		
		// FIXME: wird das hier gebraucht? anhaengen der id an die url
		$onClick = sprintf("onClick=\"window.location.href='%s%s'; return false;\"", $hrefA, $id);
		//addSiteScript( sprintf ( '$("#%s").click(function () { window.location.href = "%s%s" });', $uuid, $basehref, $id));
	} else if ( $attributes['type'] == "pdf" ) {
		$tagname = "a";
		$tagargs = sprintf('id="%s" href="%s" target="_blank"', $uuid,  $id);

		// FIXME: wird das hier gebraucht? anhaengen der id an die url
		///$onClick = sprintf("onClick=\"window.open('%s','_blank'); return false;\"", $id);
		//addSiteScript( sprintf ( '$("#%s").click(function () { window.location.href = "%s%s" });', $uuid, $basehref, $id));
	}
} 

?>
<?
if (array_key_exists("label",$attributes)) { ?>
		<div class="dbinput">
		<label class="label"><?=$attributes['label']?></label>
<?
}
if(trim($hrefA) == "") {
    echo "Keine Datei vorhanden";
}else{
?>
<?=$prefix?><<?=$tagname?> <?=$style?> <?=$tagargs?> <?=$class?> <?=$target?> <?=$onClick?> id="<?= getAttribute("id", $attributes)?>" ><?=$ivalue?></<?=$tagname?>>
<?
}
if (array_key_exists("label",$attributes)) { ?>
		</div>
<? } ?>