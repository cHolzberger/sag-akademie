<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$uid = md5(microtime());
$table = getRequiredAttribute("table", $attributes);
$formAction = getRequiredAttribute("formaction", $attributes);
$class = getAttribute("class", $attributes, "validate");

$columns = Doctrine::getTable($table)->getColumns();
ksort($columns);

$reqInfo = array();
$reqInfo['ViewAkquiseKontaktR']['badFields'] = array('id', 'kontakt_id', 'quelle_id','land_id', 'bundesland_id', 'angelegt_user_id');
$reqInfo['ViewAkquise']['badFields'] = array('id', 'kontakt_id', 'quelle_id');
$reqInfo['ViewKontakt']['badFields'] = array('id', 'land_id', 'bundesland_id', 'angelegt_user_id');
$reqInfo['ViewPerson']['badFields'] = array('id', 'kontakt_id', 'angelegt_user_id', 'land_id', 'bundesland_id');
$reqInfo['ViewSeminarPreis']['badFields'] = array('id', 'standort_id', 'verlegt_seminar_id', 'freigabe_status', 'sichtbar_planung', 'freigabe_veroeffentlichen', 'abgeschlossen');
$reqInfo['ViewBuchungPreis']['badFields'] = array('id', 'seminar_id', 'packet_id', 'person_id', 'umgebucht_id', 'angelegt_user_id', 'bildungscheck_ausstellung_bundesland_id');
$headline = getAttribute("label", $attributes, "Erweiterte Suche");
$style = getAttribute("style",$attributes, "width: 100%; height: auto; position: relative;");
$icon = getAttribute("icon",$attributes, "/img/admin/icon_seminare.png");
$widgetId = false;
if ( ($widgetId = getAttribute("id", $attributes, false)) === false) {
	$widgetId = "as". $table;
}
?>
<div style="<?=$style?>" headline="<?=$headline?>" id="<?=$widgetId?>" class="advSearch">
	<form id="formAdvancedSearch_<?=$uid?>" class="<?=$class?> ignoreChange history no-reload" method="POST" action="<?=$formAction?>">

		<div style="float: left; width: 210px; position: absolute; top: 0px; line-height: 2.3em; text-align: right;">
			<input type="hidden" name="advancedSearch" value="1">
			<input type="hidden" name="table" value="<?=$table?>">
		
			<select name="field" id="field_<?=$uid?>" style="width: 125px;">
				<?php
				foreach($columns as $key => $val) {
					if(@ !in_array($key, $reqInfo[$table]['badFields'])) {
						?>
				<option value="<?=$key?>:<?=$val["type"]?>"><?=$key?></option>
					<?php
					}
				}
				?>
			</select>

			<select name="art" style="width: 75px;" id="art_<?=$uid?>">
				<option value="LIKE">Enthält</option>
				<option value="=">=</option>
				<option value="!=">!=</option>
				<option value="<="><=</option>
				<option value=">=">>=</option>
			</select><br/>

			<input type="text" id="value_<?=$uid?>" style="width: 200px;">
			<br/>
			<input type="button" id="setor_<?=$uid?>" value="Oder"/>
			<input type="button" id="setbtn_<?=$uid?>" value="+"/>
		</div>

		<div id="rules_<?=$uid?>" style="position: relative; float: left; width: auto; left: 230px; right: 140px; min-height: 67px; max-width: 55%;"></div>

		<div style="float: left; width: 130px; position: absolute; top: 2px; right: 2px; line-height: 1.5em; text-align: right;">
			<input type="submit" value="Suche Starten" /><br/>
			<br/>
			<input type="button" value="Zurücksetzen" onClick="$('#rules_<?=$uid?>').html(''); window.ruleCount=0;"/>
		</div>

		<script type="text/javascript">
			$("#field_<?=$uid?>").change( function() {
				var val = $("#field_<?=$uid?>").val();
				var val_array = val.split(":");
				switch(val_array[1]) {
					case "string":
						$("#art_<?=$uid?> option[value='LIKE']").attr("selected", "selected");
						$("#art_<?=$uid?> option[value='LIKE']").attr("disabled", "");
						$("#art_<?=$uid?> option[value='=']").attr("disabled", "disabled");
						$("#art_<?=$uid?> option[value='!=']").attr("disabled", "disabled");
						$("#art_<?=$uid?> option[value='<=']").attr("disabled", "disabled");
						$("#art_<?=$uid?> option[value='>=']").attr("disabled", "disabled");
						break;
					default:
						$("#art_<?=$uid?> option[value='=']").attr("selected", "selected");
						$("#art_<?=$uid?> option[value='LIKE']").attr("disabled", "disabled");
						$("#art_<?=$uid?> option[value='=']").attr("disabled", "");
						$("#art_<?=$uid?> option[value='!=']").attr("disabled", "");
						$("#art_<?=$uid?> option[value='<=']").attr("disabled", "");
						$("#art_<?=$uid?> option[value='>=']").attr("disabled", "");
						break;
					}
				});
				window.ruleCount = 0;

				$("#setbtn_<?=$uid?>").click( function() {

					var val = $("#field_<?=$uid?>").val();
					var val_array = val.split(":");
					$("#rules_<?=$uid?>").append('<div style="float: left;"><input name="rules['+window.ruleCount+']" id="rule_'+window.ruleCount+'" type="checkbox" value="'+ val + ';' + $("#art_<?=$uid?>").val() + ';' + $("#value_<?=$uid?>").val() + '" checked="checked">'+ val_array[0] + ' ' + $("#art_<?=$uid?>").val() + ' ' + $("#value_<?=$uid?>").val() + '</div>');
					window.ruleCount ++;
					$("#value_<?=$uid?>").val("");
				});
				$("#setor_<?=$uid?>").click( function() {
					$("#rules_<?=$uid?>").append('<div style="float: left;"><input name="rules['+window.ruleCount+']" id="rule_'+window.ruleCount+'" type="checkbox" value="or" checked="checked">Oder</div>');
					window.ruleCount ++;
				});
		</script>

	</form>
</div>