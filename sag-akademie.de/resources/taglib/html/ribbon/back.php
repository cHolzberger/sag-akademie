<div style="position:fixed;left:8px;top:8px;z-index:1000;">
<a href="javascript:$.mosaikRuntime.goBack();"><img id="button_back" src="/css/ribbon/img/button_back.png" border="0" style="top: 0px; opacity:0.5; display: none;"/></a>
<a href="javascript:$.mosaikRuntime.goForward();"><img id="button_forward" src="/css/ribbon/img/button_forward.png" border="0" style="top: 0px; left: 26px; position: absolute; opacity:0.5; display: none;"/></a>

<script type="text/javascript">
$('#button_back').mouseover(function(){
$('#button_back').css('opacity',1);
});
$('#button_back').mouseout(function(){
$(this).css('opacity',0.5);
});
$('#button_forward').mouseover(function(){
$(this).css('opacity',1);
});
$('#button_forward').mouseout(function(){
$(this).css('opacity',0.5);
});
</script>
</div>