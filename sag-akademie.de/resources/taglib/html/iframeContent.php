<?
$style = getOptional("style", $attributes);
if ( array_key_exists( "converter", $attributes)) {
	$default = array("converter" => "autoLink");
	$value = convertString($value, $default);	
} else {
	$value = convertString($value, $attributes);
}



?>

<?= $value ?>

