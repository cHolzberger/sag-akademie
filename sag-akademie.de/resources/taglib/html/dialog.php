<?
$title = getRequired("title", $attributes);
$type = getAttribute("type", $attributes);

$targetId = getAttribute("target", $attributes, false);

if ($type == "") { $type = "message"; }

$uid = getAttribute("id", $attributes, uniqid("dialog_"));
$id = sprintf ('id="%s"', $uid );
?>
<div <?=$id?> class="dialog_<?=$type?> dialog" <?=$title?>>
	<p>
		<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
		<?=$value?>
	</p>
</div>
<? 
if ( $targetId != false) {
$script = <<<END
\$().ready(function () {
	var elemList = \$("$targetId");
	var dialog = \$("#$uid");
	
	elemList.attr("href", function() { 
		var elem = \$(this);
		var url = elem.attr("href");
		elem.mousedown(function() {
			dialog_confirmation(dialog, url);
		});
		return "#";
	});
});

END;
addSiteScript($script);
}
?>
