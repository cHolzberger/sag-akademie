function BuchenForm () { 
	this.currentPage = 0;
	var pages = [];
	var $page = null;
	
	this.init = function (initPages) {
		pages = initPages; // wenn pages noch nicht gesetzt dann jetzt setzen
		
		this.currentPage = 0;
		this.changeState(pages[0]);
		this.show();
	};

	this.beforeStateChange = function () {
		return true;
	}

	this.stateChange = function () {

	};

	this.isLast = function () {
		if ( this.currentPage+1 == pages.length) {
			return true;
		}
		return false;
	};

	this.isFirst = function () {
		if ( this.currentPage == 0) {
			return true;
		}
		return false;
	};
	
	this.next = function () {
		var self = this;
		var nextPage = this.currentPage;
		if (! self.isLast() ) {
			nextPage = nextPage + 1;
		}

		nextPage = pages[nextPage];

		if ( this.beforeStateChange (pages[this.currentPage], nextPage ) ) {
			this.hide(  function () {	
				self.changeState(nextPage);
				self.show();
			});
		}
	};

	this.prev = function () {
		var self = this;
		var nextPage = self.currentPage;
		if (! self.isFirst() ) {
			nextPage = nextPage -1;
		}
		nextPage =  pages[nextPage];
		
		if ( this.beforeStateChange(pages[this.currentPage], nextPage)) {
			self.hide(function () {
				self.changeState (nextPage );
				self.show();
			});
		}
	};

	this.hide = function (cb)  {
		if ( $page ) {
			$page.fadeOut("slow", cb);
		}
	}

	this.show = function (cb) {
		if ( $page ) {
			$page.fadeIn("slow", cb);
		}
	}
	
	this.getCurrentPageName = function () { 
		return pages[this.currentPage];
	};

	this.goTo = function (state) {
		var self = this;
		if ( this.beforeStateChange( pages[this.currentPage], state)) {
			this.hide( function () {
				self.changeState(state);
				self.show();
			});
		}
	}

	this.changeState = function (page) {
		var newpage = $.inArray(page, pages);
		var oldpage = pages[this.currentPage];
		this.currentPage = newpage;
		$page = $("#" + page);
		this.stateChange( oldpage, page);
	}		
};

var pagesFirma = ["anmeldungAdressdaten", "anmeldungKontaktdaten", "anmeldungMitgliedschaft", "anmeldungAnsprechpartner", "anmeldungPasswort", "anmeldungSenden"];

var buchenForm = new BuchenForm();
var formValidator = null;

var beforeStateChange = function (fromState, toState) {
	if ( fromState == "anmeldungPasswort") {
		if ( $("#pw1").val() != $("#pw2").val() && ($("#pw1")[0].value.length != 0 || $("#pw2")[0].value.length != 0 )) {
			alert("Fehler: Die Passwörter stimmen nicht überein .");
			return false;
		}

		if ( $("#pw1")[0].value.length < 6 ) {
			alert("Fehler: Das Passwort muss mindestens 6 Zeichen lang sein.");
			return false;
		}
	}
	
	if ( ! formValidator ) {
		formValidator = $("#anmeldung").validate();
	}

	$("#" + fromState + " input").valid();

	if ( $("#" + fromState + " .error").length > 0 ) {
		return false;
	}

	return true;
}

var onStateChange = function (fromState, toState) {
	if ( buchenForm.isLast()) {
		$("#weiter").hide();
		$("#sendenButton").show();
	} else {
		$("#weiter").show();
		$("#sendenButton").hide();
	}

	if ( buchenForm.isFirst()) {
		$("#zurueck").hide();
	} else {
		$("#zurueck").show();
	}

	if ( $("#" + fromState + "Crumb") ) {
		$("#" + fromState + "Crumb").removeClass("breadcrumb-item-active");
	}
	$("#" + toState + "Crumb").addClass("breadcrumb-item-active");
		
}

$().ready(function() {
	buchenForm.stateChange = onStateChange;
	buchenForm.beforeStateChange = beforeStateChange;

	buchenForm.init(pagesFirma);
		

	$("#weiter").click(function() {
	
	if (buchenForm.getCurrentPageName = "anmeldungAdressdaten" && !$("#privat")[0].checked){
	if($('#input_kontakt_bundesland_id').val()== -1 || $('#input_kontakt_bundesland_id').val()== 1){
	alert ('Bitte wählen Sie das Bundesland aus.');
	}
	else{
	buchenForm.next();
	}
	}
	else{	
		buchenForm.next();
	}
	});
	
	$("#zurueck").click(function() {
		buchenForm.prev();
	});

	

	$("#privat").click(function()  {
		if ( $("#privat")[0].checked ) {
			$("#firmendaten").hide("slow");
			$("#firmendaten .required").addClass("re");
			$("#firmendaten .re").removeClass("required");
			$("#firmendatenKontakt .required").addClass("re");
			$("#firmendatenKontakt .re").removeClass("required");
			$("#firmendatenKontakt").hide();

			$("#privatpersonKontakt").show();
		} else {
			$("#firmendaten").show("slow");
			$("#firmendaten .re").addClass("required");
			$("#firmendaten .required").removeClass("re");
			$("#firmendatenKontakt .re").addClass("required");
			$("#firmendatenKontakt .required").removeClass("re");
			$("#firmendatenKontakt").show();
			$("#privatpersonKontakt").hide();
		}
	});

	$("#vdrkYes").click( function () {
		$("#vdrkNr").addClass("required");
		$(".vdrkNr").show();
	});

	$("#vdrkNo").click( function () {
		$("#vdrkNr").removeClass("required");
		$(".vdrkNr").hide()
	});

	$("#rsvYes").click( function () {
		$("#rsvNr").addClass("required");
		$(".rsvNr").show()
	});

	$("#rsvNo").click( function () {
		$("#rsvNr").removeClass("required");
		$(".rsvNr").hide()
	});

	$("#dawYes").click( function () {
		$("#dawNr").addClass("required");
		$(".dawNr").show()
	});

	$("#dawNo").click( function () {
		$("#dawNr").removeClass("required");
		$(".dawNr").hide()
	});

	$("#sendenButton").click(function () {

		$('#anmeldung').submit();
	})
	
});
