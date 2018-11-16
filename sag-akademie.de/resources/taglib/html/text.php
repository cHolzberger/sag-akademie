<?
$key = "text";
$datasource = $ds;

if ( array_key_exists ( "name", $attributes ) ) {
	$key = $attributes['name'];
}

if ( array_key_exists ( "datasource", $attributes ) ) {
	$datasource = $dsl->get($attributes['datasource']);
}

?>
<div class="container"><?=$datasource->get($key) ?></div>
