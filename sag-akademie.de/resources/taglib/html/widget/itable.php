<?php
/*
 * 03.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
//$mpath = getRequiredAttribute("mpath", $attributes);
$mpath = getRequiredAttribute("mpath", $attributes);
$swf = "table_1.6.2.swf?" . MosaikConfig::getVar("version");


$style = getAttribute("style", $attributes);
if( isset($_GET['year'])) {
	$year = $_GET['year'];
}

$getvars = getAttribute("getvars",$attributes);
MosaikDebug::msg($getvars, 'getvars');
foreach ($_GET as $key=>$val) {
    $getvars .= "&$key=" . utf8_encode(urldecode ($val));
}

foreach ($_POST as $key=>$val) {
    if ( is_array ( $val )) {
	foreach ( $val as $key1 => $val1) {
	    $getvars .= "&".$key."[".$key1."]=$val1";
	}
    } else {
	$getvars .= "&$key=$val";
    }
}
 
$vars = "bridgeName=itable&datasource=/admin/json$mpath;json" . $getvars;
//MosaikDebug::msg($vars, "Vars");
?>
<br/>

<object id="flexApp" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,5,0,0" height="99%" width="100%" >
	<param name="flashvars" value="<?=$vars?>"/>
	<param name="src" value="/resources/flex/table/<?=$swf?>"/>
	<param name="AllowScriptAccess" value="always"/>
	<param name="allowFullScreen" value="true"/>
	<param name="wmode" value="transparent" />

	<embed style="<?=$style?> margin: -5px; margin-top: -20px;"  id="flexApp" name="flexApp" pluginspage="http://www.macromedia.com/go/getflashplayer" src="/resources/flex/table/<?=$swf?>" height="99%" width="100%" flashvars="<?=$vars?>"  AllowScriptAccess="always" allowFullScreen="true" wmode="transparent"/>
</object>

<script src="/resources/scripts/FABridge.js" type="text/javascript" ></script>
<script language="javascript">
    RightClick.init( "itable" );
    
    $("#imain").click(function() {
	hidePopup();
    });
    
    var contextMenu = $("#dbbuttons");

    function showPopup() {
	if(contextMenu.css("display")=="block") {
	    hidePopup();
	    return;
	}
	contextMenu.show();
	
	contextMenu.css("position","fixed");
	contextMenu.css("z-index","9999");
	contextMenu.css("display","block");
	contextMenu.css("left", RightClick.x);
	contextMenu.css("top", RightClick.y);
	
    };

    function hidePopup() {
	contextMenu.hide();
    }
</script>