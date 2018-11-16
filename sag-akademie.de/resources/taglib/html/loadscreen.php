<?
$message = getRequiredAttribute("message", $attributes);
?>

<div id="loadscreen" class="ui-dialog ui-widget ui-widget-content ui-corner-all" style="overflow: hidden; display: block; position: fixed; top:130px; left:0px; right:0px; bottom:0px; z-index: 10004; height:auto; width: auto; background: #FFFFFF url(/img/admin/loadscreen_bg.jpg) repeat-x scroll left top !important;">

<div style="position:absolute; display; block; width:100%; text-align:center; vertical-align:middle; bottom:50%; margin: auto;">
<div class="ui-dialog ui-widget ui-widget-content ui-corner-all" style="-webkit-box-shadow: 0 0 0.5em black;  -moz-box-shadow: 0 0 0.5em black; opacity:1; padding:20px; text-align:center; width:350px; height:155px; margin: auto;">
<img src="/img/logo_ci.png" border="0" alt=""/><br/><br/><br/>
<img src="/img/loading.gif" border="0" style="vertical-align: middle; text-align:center; clear: both;"/><br/>&nbsp;<br/>
<b><span id="loadscreenMessage"><?=$message?></span></b>
</div>
</div>

</div>




