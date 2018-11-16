<?php
/* 
 * 03.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
//$mpath = getRequiredAttribute("mpath", $attributes);
$mpath = "";
$seminar_art_id = $_GET['seminar_id'];
$standort_id = $_GET['standort_id'];
$swf = "planung_termin_6.swf?" . MosaikConfig::getVar("version");
?>

<br/>
<object id="flexApp" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,5,0,0" height="93%" width="100%">
    <param name="flashvars" value="bridgeName=planungTermin&online=1&seminar_id=<?=$seminar_art_id?>&standort_id=<?=$standort_id?>" />
	<param name="seminar_id" value="<?=$seminar_art_id?>" />
	<param name="src" value="/resources/flex/planung_termin/<?=$swf?>"/>
	<param name="AllowScriptAccess" value="always"/>

	<embed name="flexApp" pluginspage="http://www.macromedia.com/go/getflashplayer" src="/resources/flex/planung_termin/<?=$swf?>"
	height="93%"
	width="100%"
	flashvars="bridgeName=planungTermin&online=1&seminar_id=<?=$seminar_art_id?>&standort_id=<?=$standort_id?>"
	AllowScriptAccess="always" 
	
	/>
</object>

<script src="/resources/scripts/FABridge.js" type="text/javascript" ></script>