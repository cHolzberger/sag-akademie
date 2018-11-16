

$().ready (function () {
	var button= "#umbuchenButton";
	var umbuchenCb = function () {
		var AufText = $("#seminarUmbuchenId :selected").text();
		var umbuchenAuf = document.getElementById("seminarUmbuchenId").value;
		var action = document.getElementById("dbform").action;

		if( confirm('Wollen Sie wirklich auf \"'+ AufText +'\" umbuchen?\n\nAchtung: Es wird automatische eine E-Mail an den Kunden gesendet!\n\n') ) {
			$(button).unbind("click",umbuchenCb);
			$.mosaikRuntime.load( action , true, {
				"umbuchenAuf": umbuchenAuf,
				"umbuchenHinweis": $("#input_umbuchenHinweis")[0].value
			});
		}else{
			return false;
		}
	}
	$(button).bind("click",umbuchenCb);
	$("#input_umbuchenHinweis").hide();
	$("#label_umbuchenHinweis").hide();


	$("#seminarUmbuchenId").change(function() {
		$("#input_umbuchenHinweis").show();
		$("#label_umbuchenHinweis").show();
	});
});