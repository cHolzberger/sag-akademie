<?php
$inputId = "";

$tooltip = getOptional("tooltip", $attributes, "title");
$captchaname = $attributes['captcha'];
$forId = $attributes['name'];

$key = $attributes['name'];
$inputId = sprintf('id="%s"', $forId);
$inputClass = "";
if (array_key_exists("validate", $attributes)) {
    $inputClass = 'class="' . $attributes['validate'] . '"';
}
?>

<div class="dbinput">
    <label class="label">Sicherheitscode</label>
    <img src="/captcha/image;download?sessionname=<?=$captchaname?>" />
</div>


<div class="dbinput">
    <label class="label">
    Bitte hier den Sicherheitscode eingeben *
    </label>
    
	<input <?=$inputId?> type="text" name="<?=$forId?>" <?=$inputClass?> <?=$tooltip?> />

	<input type="hidden" name="sessionname"  value="<?=$captchaname?>" />
	
</div>
<script type="text/javascript">
    var CUR_SESSION = '<?=$captchaname?>';
</script>