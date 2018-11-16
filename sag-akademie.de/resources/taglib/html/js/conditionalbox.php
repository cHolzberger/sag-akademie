<? 
	$mpath = getAttribute("mpath", $attributes);
	$watch = getAttribute("watchId", $attributes);
	
	$default = '';
	
	if ( !empty($mpath) ) {
		$default = $dsl->get("dbtable", $mpath);
	}
	$style = "display: none;";
	
	if ( $default == "1" ) {
		$style="";
	}
	
	$id = str_replace(":","_", $mpath);
	$showon = getAttribute("showon", $attributes, "1");
?>
<span id="<?=$watch?>_visible" style="<?=$style?>">
	<?=$value?>
</span>

<script type="text/javascript">
	<? if ( !empty($default )) { ?>
		$("#<?=$watch?>").val("<?=$default?>");
	<? } ?>
	
	$("#<?=$watch?>").change(function () {
		if ( $("#<?=$watch?>").val() == "<?=$showon?>" ) {
			$("#<?=$watch?>_visible").show();
		} else {
			$("#<?=$watch?>_visible").hide();
		}
	}); 			
	
	$("#<?=$watch?>").change();	
</script>