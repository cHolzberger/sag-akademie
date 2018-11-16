<?php
/* 
 * 03.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
//$mpath = getRequiredAttribute("mpath", $attributes);
$mpath = "";
$swf = "planung_1.3.1.swf?" . MosaikConfig::getVar("version");
$year = date("Y");

if( isset($_GET['year'])) {
	$year = $_GET['year'];
} 

$vars = "bridgeName=planungKalender&year={$year}&online=1&datasource=/admin/json{$mpath};json&allowFullScreen=true&wmode=transparent";
?>
<br/>
<object id="flexApp" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,5,0,0" height="95%" width="100%" >
    <param name="flashvars" value="<?=$vars?>"/>
	<param name="src" value="/resources/flex/planung_jahr/<?=$swf?>"/>
	<param name="AllowScriptAccess" value="always"/>
	<param name="allowFullScreen" value="true"/>
	<!--<param name="wmode" value="window" />-->
	<embed name="flexApp" pluginspage="http://www.macromedia.com/go/getflashplayer" src="/resources/flex/planung_jahr/<?=$swf?>" height="95%" width="100%" flashvars="<?=$vars?>"    AllowScriptAccess="always" allowFullScreen="true"/>
</object>

<script src="/resources/scripts/FABridge.js" type="text/javascript" ></script>
<script type="text/javascript">
    $().ready (function () {
	$.mosaikRuntime.flexSave = "planungKalender";
    });
</script>