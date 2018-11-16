<?
$mpath = (getRequiredAttribute ("mpath", $attributes));
$inputvalue = $dsl->get("dbtable", $mpath);

if (empty($inputvalue)) {
	
	$inputvalue = getAttribute("emptyValue", $attributes, "");
}
@$add = $attributes['offset'];



$inputvalue = convertString($inputvalue, $attributes);


$suffix = "";
if (array_key_exists("suffix", $attributes) && !empty ( $inputvalue ) ) {
	$suffix = $attributes['suffix'];
}

if (isset($add)) {
    $inputvalue = $inputvalue + $add;
}

if ( empty ($inputvalue)) $inputvalue = "&nbsp;";

?>
<?=$inputvalue ?><?=$suffix?>