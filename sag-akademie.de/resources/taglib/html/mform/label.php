<?
$label = $attributes['label'];
$class = getAttribute("class", $attributes, "");

$id="mform_" . getAttribute("id", $attributes, md5(microtime()));

$isBold = getAttribute("bold",$attributes,false);

if ( $isBold ) {
    $label = "<strong>" . $label . "</strong>";
}
?>

<div class="dbinput mdb-input <?=$class?>"  id="container_<?=$id?>">
	<div id="label_<?=$id?>" class="label mform-label" style="float: left; width: 75px;"><?=$label?></div>
	<div id="input_<?=$id?>" class="mform-content"><?=$value?><?= getAttribute("append", $attributes, "&nbsp;"); ?></div>
	

</div>
<script type="text/javascript">
	    function update_<?=$id?> () {
		var input = $("#input_<?=$id?>");
		var label = $("#label_<?=$id?>");

		label.css("height", input.height().toString() + "px" );
		//alert("height" + input.height().toString());
	};

	$().ready(function () {
	    window.setTimeout("update_<?=$id?>()",2000);
	});
	    
</script>
