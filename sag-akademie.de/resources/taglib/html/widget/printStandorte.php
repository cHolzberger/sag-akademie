<?
$mpath = (getRequiredAttribute("mpath", $attributes));
$inputvalue = $dsl->get("dbtable", $mpath);
/*
 *
 */
?>
<tr>
<td class="standort">T</td>
<? foreach ($inputvalue as $value ): ?>
<td class="standort_name"><?=$value['name'] ?></td>
<? endforeach; ?>
</tr>