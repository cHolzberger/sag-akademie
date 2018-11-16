<?
$mpath = (getRequiredAttribute ("mpath", $attributes));
$inputvalue = $dsl->get("dbtable", $mpath);
/*
 * 
 */
?>
<colgroup>
	<col width="5mm" />

<? foreach ($inputvalue as $value ): ?>
<col width="30mm"/>
<? endforeach; ?>
</colgroup>