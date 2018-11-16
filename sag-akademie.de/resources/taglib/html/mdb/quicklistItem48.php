<?
	$mpathField1 = (getRequiredAttribute ("mpathField1", $attributes));
	$mpathField2 = (getRequiredAttribute ("mpathField2", $attributes));
	$mpathField3 = (getAttribute ("mpathField3", $attributes, false));
	$mpathField4 = (getAttribute ("mpathField4", $attributes, false));
	$mpathField5 = (getAttribute ("mpathField5", $attributes, false));
	$mpathField6 = (getAttribute ("mpathField6", $attributes, false));
	$mpathField7 = (getAttribute ("mpathField7", $attributes, false));
	$mpathField8 = (getAttribute ("mpathField8", $attributes, false));

	$mpathGrayout = (getAttribute ("mpathGrayout", $attributes, false));
	$grayoutColor = (getAttribute ("grayoutColor", $attributes, "#7a7a7a;"));

	
	$checkbox1 = (getAttribute ("checkbox1", $attributes, false));
	$checkbox1Id = (getAttribute ("checkbox1Id", $attributes, false));
	$cOffset1 = (getAttribute ("offsetField3", $attributes, "150"));

	/* keine converter fuer feld 1,2,6,7,8 */
	
	$field1 = $dsl->get("dbtable", $mpathField1);
	$field2 = $dsl->get("dbtable", $mpathField2);
	$field3 = '';
	$field4 = '';
	$field5 = '';
	$field6 = '';
	$field7 = '';
	$field8 = '';
	

	
	if($mpathField3) { $field3 = $dsl->get("dbtable", $mpathField3);
		if (array_key_exists("converterField3", $attributes)) {
			$converter = $attributes['converterField3'];
			$field3 = $converter( $field3 );
		}
		if ( $mpathField4 ) { 
			$field3 .= ","; // komma nur hinzufuegen wenn feld3 einen gueltigen wert hat und das 4. feld auch gesetzt ist
		}
	}
	
	if($mpathField4) { $field4 = $dsl->get("dbtable", $mpathField4);
		if (array_key_exists("converterField4", $attributes)) {
			$converter = $attributes['converterField4'];
			$field4 = $converter( $field4 );
		}
	}
	if($mpathField5) { $field5 = $dsl->get("dbtable", $mpathField5);
		if (array_key_exists("converterField5", $attributes)) {
			$converter = $attributes['converterField5'];
			$field5 = $converter( $field5 );
		}
	}
	if($checkbox1) {
	    $checkbox1Val = $dsl->get("dbtable", $checkbox1);
	    $checkbox1Id = $dsl->get("dbtable", $checkbox1Id);
	}

	if (array_key_exists("converterField2", $attributes)) {
			$converter = $attributes['converterField2'];
			$field2 = $converter( $field2 );
	}

	if($mpathField1) {
		if (array_key_exists("converterField1", $attributes)) {
			$converter = $attributes['converterField1'];
			$field1 = $converter( $field1 );
		}
	}

	$color = "";
	if($mpathGrayout) {
	    $_value = $dsl->get("dbtable", $mpathGrayout);
	    if ( $_value == "1" ) {
		$color="color: $grayoutColor;";
	    }
	}

	if ( $mpathField6 ) {
	    $field6 = "<b>". $dsl->get("dbtable", $mpathField6) . ",</b>";
	}

	if ( $mpathField7) {
	    $field7 = "<b>" . $dsl->get("dbtable", $mpathField7) . "</b>";
	}

	if ( $mpathField8) {
	    $field8 = "<b>" . $dsl->get("dbtable", $mpathField8) ."</b>";
	}

	if ( $dsl->get("dbtable", "count") % 2 == 0) $class="quicklistItem48-even";
	else $class="quicklistItem48-odd";
?>
<div class="quicklistItem48 <?=$class?>" style="<?=$color?>">
	<?php
	if($checkbox1) {
	    $checked = '';
	    if($checkbox1Val == 1)
	    {
		$checked = 'checked="checked"';
	    }
	?>
	<div class="quicklistItem_checkbox">
	Nicht Teilg.: <input type="checkbox" value="1" name="input_<?=$checkbox1?>[<?=$checkbox1Id?>]" style="" <?=$checked?> />
	</div>
	<?php
	}
	?>
	<div style="width: auto; height: auto; float: left;">
	<b><?=$field1?></b><br/>
	<?=$field2?><br />
	<?=$field6?> <?=$field7?> <?=$field8?><br />
	</div>
	<div style="position:absolute;left: <?=$cOffset1?>px;"><?=$field3?> <?=$field4?>
	<br/><?=$field5?>
	</div>
	<div class="quicklistItem48-content">
		<?=$value?>
	</div>
	<div style="clear: both;"></div>
</div> 