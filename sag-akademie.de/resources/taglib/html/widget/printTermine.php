<?php
$mpath = (getRequiredAttribute ("mpath", $attributes));
$inputvalue = $dsl->get("dbtable", $mpath);

foreach ($inputvalue as $value ): 
	$weekend = "noweekend";
	if ( $value['weekend']) $weekend = "weekend";
?>
	<tr>
		
		<td class="termin_tag <?=$weekend?>">
			<?=$value['tag']; ?>
		</td>
		<? foreach ( $value['Standorte'] as $term_liste ): ?>
		<td class="termin_border <?=$weekend?>">
		
		<? foreach ( $term_liste['Termine'] as $termine ): ?>
		<div class="termin_highlight" style="background-color: <?= str_replace("0x", "#", $termine['freigabe_farbe']) ?>;">
			<?=$termine['freigabe_flag'] ?>
		</div>
		<div class="termin_seminar_art" style="background-color: <?= str_replace("0x", "#", $termine['farbe']) ?>; color: <?= str_replace("0x", "#", $termine['textfarbe']) ?>;">
				<?=$termine['seminar_art_id'] ?>	
		</div>
		<div class="termin_info" style="background-color: <?= str_replace("0x", "#", $termine['farbe']) ?>;color: <?= str_replace("0x", "#", $termine['textfarbe']) ?>;">
		<?	if (!empty ($termine['info'])): ?>
			<?=$termine['info']; ?>
			<? else: ?>
			&nbsp;
		<?endif; ?>
		</div>
		<br />
		<? endforeach; ?>
	</td>
	<? endforeach; ?>
</tr>
<? endforeach; ?>