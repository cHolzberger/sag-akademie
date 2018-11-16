<? $data = $dsl->get("dbtable");
$data->log();
$data = $data->store; ?>
<hr />
<? if (array_key_exists ( "exact", $data ) && $data['exact'] == 1) { ?>

	<div style="position: relative; margin-left: 10px;">
	Genauer Treffer mit der ID: <?=$data['id'] ?>
	<div style="position: absolute; right: 5px; bottom: 0px; float: left;">
	Zusammenf&uuml;hren: <input name="merge[<?=$data['parent']['id']?>][<?=$data['id']?>]" type="checkbox" value="true" checked="checked"/>
	</div>
	</div>
	
<? } else { ?>

	<div style="position: relative; margin-left: 10px;">
	Treffer mit folgenden Differenzen:<br/>

	<? foreach ( $data as $key=>$value ) {
		if ($key == "id" || $key == "parent" || $key=="kontaktQuelleStand" || $key == "exact") continue;
	?>

		<?=$key?> (Neu): <?=$value?> <br/>
		<?=$key?> (Alt): <?=$data['parent'][$key]?> <br/>
		Ersetzen: <input type="radio" name="data[<?=$data['parent']['id']?>][<?=$key?>]" value="<?=$value?>" /><br/>
	<? } ?>
	<div style="position: absolute; right: 0px; bottom: 0px; float: left;">
	Zusammenf&uuml;hren: <input name="merge[<?=$data['parent']['id']?>][<?=$data['id']?>]" type="checkbox" value="true" /><br/>
	</div>
	</div>
<? } ?>
