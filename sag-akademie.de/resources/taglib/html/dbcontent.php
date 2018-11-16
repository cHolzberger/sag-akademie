<?
// convert the input value
$inputvalue = $dsl->get("dbtable", $attributes['name']);
$inputvalue = convertString($inputvalue, $attributes);
echo $inputvalue;
?>