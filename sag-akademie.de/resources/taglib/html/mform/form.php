<? 
	$elementId = getOptional("id");
	$style = getOptional("style",$attributes);
	
	$formaction = $dsl->get("dbtable", "formaction");
	$class = getAttribute("class", $attributes, "validate");
?>
<form id="dynForm" <?=$style?> <?$elementId?> method="POST" action="<?=$formaction?>" class="<?=$class?>">
	<span style="display: none;" class="mFormActionLog"></span>
	<?=$value?>
</form>