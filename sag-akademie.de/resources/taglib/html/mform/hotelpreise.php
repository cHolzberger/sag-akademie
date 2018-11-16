<?
$style = getAttribute("style", $attributes);
$class = getAttribute("class", $attributes);
$elementId = getOptional ("id", $attributes);
$class .="dynFields";

if (getAttribute("template", $attributes, false)) { // template for js
	$id = "new";
	$entryId = "#ID#";
	$style .="display: none;";
	
	$infoValue = "";
	$datum_startValue="";
	$datum_endeValue="";
	$zimmerpreis_ezValue = "";
	$zimmerpreis_dzValue = "";
	$fruehstuecks_preisValue = "";
	$zimmerpreis_mb46Value  = "";
	$margeValue = "";
} else {
	$id = $dsl->get("dbtable", "id");
	$entryId = "_$id";
	
	$infoValue = $dsl->get("dbtable", "info");
	$datum_startValue = mysqlDateToLocal($dsl->get("dbtable", "datum_start"));
	$datum_endeValue = mysqlDateToLocal($dsl->get("dbtable", "datum_ende"));
	$zimmerpreis_ezValue = euroPreis($dsl->get("dbtable", "zimmerpreis_ez"));
	$zimmerpreis_dzValue = euroPreis($dsl->get("dbtable", "zimmerpreis_dz"));
	$zimmerpreis_mb46Value = euroPreis($dsl->get("dbtable", "zimmerpreis_mb46"));

	$fruehstuecks_preisValue =  euroPreis($dsl->get("dbtable", "fruehstuecks_preis"));
	$margeValue =  euroPreis($dsl->get("dbtable", "marge"));
}
?>
<div style="<?= $style ?> padding-bottom: 15px;" class="{id: '<?=$id ?>'} <?=$class?>" <?=$elementId?> >
    <h2 style="position: relative;">Hotelpreise<? if($datum_startValue !=""): echo "&nbsp; von <u>$datum_startValue</u> bis <u>$datum_endeValue</u>"; endif;?>:
		<a href="#" class="remove" style="position: absolute; right: 1px; bottom: 1px;">l&ouml;schen</a>
	</h2>
    <div style="width: 300px; float: left;">
		<input type="hidden" name="HotelPreise[<?=$entryId?>][id]" value="<?=$id?>" />
        <div class="dbinput">
            <label class="label" style="width: 50px !important; float: left;">
                Von:
            </label>
            <input type="text" class="dateInput required" name="HotelPreise[<?=$entryId?>][datum_start]" value="<?=$datum_startValue?>" />
        </div>
        <div class="dbinput">
            <label class="label" style="width: 50px !important; float: left; clear: both;">
                Bis:
            </label>
            <input type="text" class="dateInput required" name="HotelPreise[<?=$entryId?>][datum_ende]" value="<?=$datum_endeValue?>"/>
        </div>
		<br/>
		<div class="dbinput">
            <label class="label" style="width: 50px !important; float: left; clear: both;">
                Grund:
            </label>
            <input type="text" class="" name="HotelPreise[<?=$entryId?>][info]" value="<?=$infoValue?>"/>
        </div>
		
    </div>
    <div style="float: left; width: 350px;">
        <div class="dbinput">
            <label class="label" style="width: 125px !important; float: left;">
                Einzelzimmer:
            </label>
            <input type="text" name="HotelPreise[<?=$entryId?>][zimmerpreis_ez]" value="<?=$zimmerpreis_ezValue?>" class="required"/>
        </div>
        <div class="dbinput">
            <label class="label" style="width: 125px !important; float: left;">
                Doppelzimmer:
            </label>
            <input type="text" name="HotelPreise[<?=$entryId?>][zimmerpreis_dz]" value="<?=$zimmerpreis_dzValue?>" class="required"/>
        </div>
		<div class="dbinput">
            <label class="label" style="width: 125px !important; float: left;">
                Mehrbettzimmer (4-6):
            </label>
            <input type="text" name="HotelPreise[<?=$entryId?>][zimmerpreis_mb46]" value="<?=$zimmerpreis_mb46Value?>" class="required"/>
        </div>
        <div class="dbinput">
            <label class="label" style="width: 125px !important; float: left;">
                Fr&uuml;hst&uuml;ckspreis:
            </label>
            <input type="text" name="HotelPreise[<?=$entryId?>][fruehstuecks_preis]" value="<?=$fruehstuecks_preisValue?>" class="required"/>
        </div>
        <div class="dbinput">
            <label class="label" style="width: 125px !important; float: left;">
                Marge:
            </label>
            <input type="text" name="HotelPreise[<?=$entryId?>][marge]" value="<?=$margeValue?>" class="required"/> 
        </div>
    </div>
    <div style="clear:both; text-align:right;">
			
    </div>
</div>
