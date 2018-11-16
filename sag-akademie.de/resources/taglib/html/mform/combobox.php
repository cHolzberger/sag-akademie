<?
$targetId = getAttribute("target", $attributes, "");
$displayTargetId = getAttribute("displayTarget", $attributes, "");
$template = getAttribute("template", $attributes, "");
$datasource = getAttribute("json",$attributes, "");	
$filter = getAttribute("filter",$attributes, "");	
$updatePrefix = getAttribute("updatePrefix",$attributes, "input_HotelBuchung_");

?>
<?=$value?>
<script type="text/javascript">
	if (!document.getElementById("mCompleteContainer")) {
		$('<div class="mCompleteContainer" id="mCompleteContainer" style="display: none; position:fixed;left:0px;top:0x;"></div>')
		.appendTo("#imain");

		$('<div class="mCompleteContainerClose" id="mCompleteContainerClose" style="text-align: center; display: none; cursor: pointer; position:fixed; left: 0px; top:0px; pointer: pointer; font-size: small; width: 25px; height: 16px; font-weight: bolder; opacity: 0.7; background-color: black; color: white;">X</div>').
			appendTo("#imain");

		$('<div id="mCompleteLoading" style="position: fixed; "><img src="/img/loadingsmall.gif" border="0"/></div>')
		.appendTo("#imain");
	}

	var dd = new MAutoComplete("<?=$targetId?>","<?=$displayTargetId?>", '<?=$datasource?>', '<?=$updatePrefix?>');

	dd.ondata = function(data) {
		var template = document.getElementById("<?=$template?>");
		var node = template.cloneNode(true);

		for ( field in data ) {
			$(node).addClass("id_" + data.id);
			$(node).addClass("{value: " + data.id + "}");
			$(node).data("json", data);
			node.innerHTML = node.innerHTML.replace("{" + field + "}", data[field]);
		}
		node.style.display="block";
		node.id += data.id;
		return node;
	};

	function filterKurs (searchTerms, data) {
		for (term in searchTerms) {
			if (data.kursnr.toLowerCase().search(searchTerms[term]) != -1) return true;

			return false;
			break;
		}
	};


	function filterHotel (searchTerms, data) {
		for (term in searchTerms) {
			if (data.name.toLowerCase().search(searchTerms[term]) != -1) return true;
			return false;
			break;
		}
	};

	function filterPerson (searchTerms, data) {
		for (term in searchTerms) {
			if (data.name.toLowerCase().search(searchTerms[term]) != -1) return true;
			else if (data.vorname.toLowerCase().search(searchTerms[term]) != -1) return true;
			return false;
			break;
		}
	};

	function filterKontakt (searchTerms, data) {
		for (term in searchTerms) {
			if (data.firma.toLowerCase().search(searchTerms[term]) != -1) return true;

			return false;
			break;
		}
	};



	dd.filterData = filter<?=$filter?>;

</script>