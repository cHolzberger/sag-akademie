<form id="dbform" <?=getOptional("style")?> action="<?=$dsl->get("dbtable","formaction")?>" method="post" enctype="multipart/form-data" class="<?=getAttribute("class", $attributes, "");?>">
<input type="hidden" name="dbtable" value="<?=$dsl->get("dbtable","dbtable")?>" />
<input type="hidden" name="dbclass" value="<?=$dsl->get("dbtable","dbclass")?>" />

<?=$value?>
</form>
