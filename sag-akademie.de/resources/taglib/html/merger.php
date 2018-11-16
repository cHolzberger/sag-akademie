<?$data = $dsl->get("dbtable")->store ?>
<?
if ($data['exact'] == 1):
	?>
<script type="text/javascript">
    $("#topspacer").height(30);
    </script>
    <h2>Genauer Treffer mit der ID:
		<?= $data['id'] ?></h2>
    <div style="text-align: left; position: fixed; right: 25px; top:150px; border-style:solid;border-width:1px;border-color:black;padding:15px;background-color:white;">
        Zusammenf&uuml;hren: <input name="merge[<?=$data['parent']['id']?>][<?=$data['id']?>]" type="checkbox" value="true" checked="checked"/>
    </div>

<?
else :
	?>
<div style="position: relative; margin-left: 10px; clear: both;">
	<br/>
		<?
		foreach ($data as $key=>$value) {
			if ( $key == "parent" || $key == "kontaktQuelleStand" || $key == "exact" || $key == "count" || $key == "notiz" || $key=="wiedervorlage") continue ;
			?>

	<!--  -->

	<div style="border-style:solid;border-width:1px;border-color:black;padding:5px;margin-bottom:5px;width:290px;float:left;margin-right:5px;background-color:white;">

		<!-- ausgangs datensatz -->
		<div style="clear:both;">
			<b>Feld:</b>
					<?= $key ?>
		</div><br/>

		<div style="width: auto; height: auto; clear:both; vertical-align: middle;">
			<div style="width: 100px; float: left; clear: both;">
				<input type="radio" name="data[<?=$data['parent']['id']?>][<?=$key?>]" value="<?=$value?>"
						<?if
						( empty($data['parent'][$key]) || $data['parent'][$key] == "0000-00-00"):
							?>
					   checked="checked"
							   <?endif
							   ;
							   ?>
					   />
				<b>Ersetzen:</b>
			</div>
			<div style="padding-top:5px;"><?
			if ( Resolver::hasKey($key) ) {
				echo Resolver::getValue($key, $value);
			} else {
				echo $value;
			}
			?></div>
		</div>

		<div style="clear: both; width: 1px; height: 1px;" />

		<!-- alter datensatz -->
		<div style="width: auto; height: auto; clear:both; vertical-align: middle;">
			<div style="width: 100px; float: left; clear: both;">
				<input type="radio" name="data[<?=$data['parent']['id']?>][<?=$key?>]" value="<?= $data['parent'][$key] ?>"
						<?if
						(! empty($data['parent'][$key]) && $data['parent'][$key] != "0000-00-00"):
							?>
					   checked="checked"
							   <?endif
							   ;
							   ?>
					   />
				<b>Beibeh.:</b>
			</div>
			<div style="padding-top:5px;"><?
			if ( Resolver::hasKey($key) ) {
				echo Resolver::getValue($key, $data['parent'][$key]);
			} else {
				echo $data['parent'][$key];
			}
			?></div>
		</div>

		<div style="clear: both; width: 1px; height: 1px;" />

		<!-- eigene eingabe -->
		<div style="width: auto; height: 20px; clear:both; vertical-align: middle; padding-bottom:20px;">
			<div style="width: 100px; float: left; clear: both;">
				<input type="radio" name="data[<?=$data['parent']['id']?>][<?=$key?>]" value=""
					   id="repl_<?=$key?>"/>
				<b>Neu:</b>
			</div>
			<div style="padding-top:1px;"><?
	    			if ( Resolver::hasKey($key) ) {
				    //echo $data['parent'][$key];
				    echo Resolver::getComponent($key, $data['parent'][$key], 'onchange=\'$("#repl_'.$key.'").val($(this).val());\'  onclick=\'$("#repl_'.$key.'").attr("checked", true);\'' );
				} else {
				?>
				<input type="text" onclick="$('#repl_<?=$key?>').attr('checked', true);" onchange="$('#repl_<?=$key?>').val($(this).val());" style="width: 180px;"/>
				<? } ?>
			</div>

		</div>

	</div>

			<?
		}
		?>

	<br/>&nbsp;





</div>


<div style="position: fixed; top: 131px; padding:5px; padding-left: 25px; padding-top: 30px; right: 0px; left: 2px;border-bottom: 1px solid gray;margin-bottom:10px; float:left;margin-right:22px;background-color:white; overflow: hidden;" id="notizen">
	
	<textarea name="data[<?=$data['parent']['id']?>][notiz]" width="500" height="300" style="width: 80%; height: 100px; margin-bottom: 5px;">
Notiz des Duplikat - Filters vom <?= date("d.m.Y");
			$printed = false;?>:

<? if ( !empty($data['parent']['notiz'])) { $printed=true;?>

**************************************
* Informationen aus den alten Datensätzen:

<? echo $data['parent']['notiz']; }?>

<? if ( !empty($data['notiz'])) { ?>
<? if ( !$printed ) { ?>
**************************************
* Informationen aus den alten Datensätzen:
<? } else { ?>
---
<? } ?>

<? echo$data['notiz']; }?>
	</textarea>
	<h2 style="border-bottom: 0px !important;"><?=@$data['parent']['firma']?><? if ( !empty ($data['parent']['name'] )) { ?> &nbsp;&nbsp;&nbsp; <?=@$data['parent']['name']?>, <?=@$data['parent']['vorname']?> <? }?> </h2>

	<div style="text-align: left; position: absolute; right: 5px; top:10px; border-style:solid;border-width:1px;border-color:black;padding:15px;background-color:white;">
        <input name="merge[<?=$data['parent']['id']?>][<?=$data['id']?>]" type="checkbox" value="true" /> Zusammenf&uuml;hren <br/>
		<input name="ignore[<?=$data['parent']['id']?>]" value="<?=$data['id']?>" type="checkbox" /> kein Duplikat <br/><Br/>
		<input type="checkbox" name="data[<?=$data['parent']['id']?>][wiedervorlage]" value="1" <? if ($data['parent']['wiedervorlage']==1) echo "checked='checked'" ; ?> /> Wiedervorlage<br/>
    </div>
</div>


<?endif
;
?>
