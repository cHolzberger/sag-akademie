/*** SITE FUNCTIONALITY ***/
function trim (zeichenkette) {
  // Erst führende, dann Abschließende Whitespaces entfernen
  // und das Ergebnis dieser Operationen zurückliefern
  return zeichenkette.replace (/^\s+/, '').replace (/\s+$/, '');
};


function dlog(s) {
	if (typeof console != "undefined" && typeof console.debug != "undefined") {
		console.log(s);
	} else {
		alert(s);
	}
};

function theme_init() {
	//$(".nav").bgiframe();
	/* theme support */
	$("input").addClass("text ui-widget-content ui-corner-all ui-widget-content");
	$("input[type=submit],input[type=button],input[type=reset]").addClass("ui-button ui-state-default ui-corner-all ui-state-default");

	$("input[type=submit],input[type=button]").hover(function() {
		$(this).addClass("ui-state-hover");
	}, function() {
		$(this).removeClass("ui-state-hover");
	});

	$("button").addClass("text ui-widget-content ui-corner-all ui-state-default");
	$("button").hover(function() {
		$(this).addClass("ui-state-hover");
	}, function() {
		$(this).removeClass("ui-state-hover");
	});
	/*
	$("#nachricht_senden").click(function(){
	if(document.getElementById("email").value=="" || document.getElementById("vorname").value=="" || document.getElementById("name").value=="" || document.getElementById("nachricht").value=="")
	{
	$("#fehler").show("slow");
	}
	else {
	document.kontaktformular.submit();
	}
	});
	$("#hide_fehler").click(function(){
	$("#fehler").hide("slow");
	});
*/

};

function hideKontakt() {
	if ( $("#isFirma")[0].checked) {
		$("#buchenFirma").show();
		$("#buchenPrivat1").css("left","250px");
		$("#buchenPrivat2").css("left","500px");
	} else {
		$("#buchenFirma").hide();
		$("#buchenPrivat1").css("left","0px");
		$("#buchenPrivat2").css("left","250px");
	}
};

function buchen_init() {
	if ($("#isPrivat").length > 0 ) {
		$("#isPrivat").click(hideKontakt);
		$("#isFirma").click(hideKontakt);
		hideKontakt();
	}
};

function validator_init() {
	if ($(".form").length > 0) {
		$(".form, #buchung").validate({
			 errorLabelContainer: "#validatorErrorMessages ul",
			 wrapper: "li",
			 errorContainer: "#validatorError",
			 errorPlacement: function (error, element) {
			 	
			 },
			 invalidHandler: function ( form, validator) {
			 	$("#speichern").hide();
			 }
		});
	}
};


/*** PAGING **/
var currentPage = 1;


function nextPage() {
	if (currentPage == Number($("#pages").attr("value"))) {
		$("#pageform").submit();
	} else {
		$("#page" + currentPage).slideUp(500);
		currentPage++;
		$("#page" + currentPage).slideDown(500);
		$("#prev").show();
		if (currentPage == Number($("#pages").attr("value"))) {
			$("#next").attr("value", "Abschicken");
		}
	}
	$("#pageinfo").html("Seite " + currentPage + " von " + $("#pages").attr("value") );
};

function prevPage() {
	if (currentPage > 1) {
		$("#next").attr("value","Weiter >");
		$("#page" + currentPage).slideUp(300);
		currentPage --;
		if (currentPage == 1) {
			$("#prev").hide();
		}
	} else {
		$("#prev").hide();
	}

	$("#page" + currentPage).slideDown(300);
	$("#pageinfo").html("Seite " + currentPage + " von " + $("#pages").attr("value") );
};

function pageing_init() {
	$("#pageinfo").html("Seite " + currentPage + " von " + $("#pages").attr("value") );
	$("#next").click (nextPage);
	$("#prev").click( prevPage);
	$("#prev").hide();
	$("#send").hide();
};

/*** on ready ***/
$().ready(function () {
 	theme_init();
	validator_init();
});

function errorHandler(message, url, line) {
	return true;
};
//ie on error fix
//window.onerror = errorHandler;
