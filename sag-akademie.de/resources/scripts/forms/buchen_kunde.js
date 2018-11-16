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

var pagesFirma = ["buchungTeilnehmer", "buchungHotel","buchungUnterlagen","buchungFoerderung","buchungSenden"];
var addedTeilnehmer = [];
var buchenForm = new BuchenForm();
var formValidator = null;

var formValidator;
var beforeStateChange = function (fromState, toState) {
	if ( ! formValidator ) {
		formValidator = $("#buchungTeilnehmer").validate();
	}
/*
	if ( fromState == "buchungTeilnehmerx") {
		if ( $("#mitarbeiterneu")[0].checked && ( $("#person_vorname")[0].value.length == 0  || $("#person_name")[0].value.length == 0 || $("#person_geburtstag")[0].value.length == 0 ) ) {
			alert ("Fehler: Die Angaben zum neuen Mitarbeiter sind unvollstaendig.");
			return false;
		}
	}*/

	$("#" + fromState + " input").valid();
	if ( $("#" + fromState + " .error").length > 0 ) {
		return false;
	} return true;
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

	if ( toState == "buchungFoerderung") {
		$("#weiter").hide();
		$("#teilnehmerControl").show();
	} else {
		$("#teilnehmerControl").hide();
	}



}

/** einstiegspunkt
 *wird nach dem erfolgreichen laden der seite aufgerufen */
$().ready(function() {
	buchenForm.stateChange = onStateChange;
	buchenForm.beforeStateChange = beforeStateChange;

	buchenForm.init(pagesFirma);

	$("#weiter").click(function() {
		buchenForm.next();

	});

	$("#zurueck").click(function() {
		buchenForm.prev();
	});

	$("#mitarbeiterneu").click(function ( ) {
		if ( $("#mitarbeiterneu")[0].checked ) {
			$("#mitarbeiterNeuForm").show("slow");
			$("#mitarbeiterSelect").hide("slow");
		} else {
			$("#mitarbeiterNeuForm").hide("slow");
			$("#mitarbeiterSelect").show("slow");
		}
		
	});

	$("#hotelBuchenJa").click( function () {
		$("#teilnehmerAnreisedatum")[0].value="";
		$("#teilnehmerAnreisedatum").addClass("required");
		$("#hotelbuchen").show();
	} );
	$("#hotelBuchenNein").click( function () {
		$("#hotelbuchen").hide();
		$("#teilnehmerAnreisedatum").removeClass("required");
	});

	$("#teilnehmerBildungscheckJa").click( function () {
		$("#bildungscheck input").addClass("required");
		$("#bildungscheck").show();
	});

	$("#teilnehmerBildungscheckNein").click( function () {
		$("#bildungscheck").hide();
		$("#bildungscheck input").removeClass("error");
		$("#bildungscheck input").removeClass("required");
	});
	
	$("#arbeitsagenturJa").click( function () { 
		$("#arbeitsagentur").show();
		$("#zustaendige_arbeitsagentur").addClass("required");
	});
	
	$("#arbeitsagenturNein").click( function () {
		$("#arbeitsagentur").hide();
		$("#zustaendige_arbeitsagentur").removeClass("error");
		$("#zustaendige_arbeitsagentur").removeClass("required");
	});

	$("#sendenButton").click ( function () {
		if ( !$("#voraussetzungenAkzeptiert" )[0].checked  ) {
			alert("Sie müssen die Teilnahmevorraussetzungen akzeptieren.");
			return;
		}

		if (! $("#agbAkzeptiert")[0].checked) {
			alert("Sie müssen die AGB akzeptieren.");
			return;
		}

		
		$('#buchung').submit();
	});
	
	$("#weitererTeilnehmer").click(function ( ) {
		if ( $("#mitarbeiterneu")[0].checked && ( $("#person_vorname")[0].value.length == 0  || $("#person_name")[0].value.length == 0 || $("#person_geburtstag")[0].value.length == 0 ) ) {
			alert ("Fehler: Die Angaben zum neuen Mitarbeiter sind unvollstaendig.");
			return;
		}
		$("#buchungFoerderung  input").valid();
		if ( $("#buchungFoerderung .error").length > 0 ) {
			return false;
		}
		cloneForm();
		buchenForm.goTo('buchungTeilnehmer')
	});

	$("#neuerTeilnehmer").click(function ( ) {
		/*
		if ( $("#mitarbeiterneu")[0].checked && ( $("#person_vorname")[0].value.length == 0  || $("#person_name")[0].value.length == 0 || $("#person_geburtstag")[0].value.length == 0 ) ) {
			alert ("Fehler: Die Angaben zum neuen Mitarbeiter sind unvollstaendig.");
			return;
		}*/

		$("#buchungFoerderung input").valid();
		if ( $("#buchungFoerderung .error").length > 0 ) {
			return ;
		}

		// wenn der benutzer von der letzten seite wieder zurueck kommt
		// ist das template wieder leer und der clone bereits angelegt
		// also kein zweites mal clonen
		if (  $("#mitarbeiterneu")[0].checked && $("#input_personTmpl_vorname")[0].value.length != 0 ) {
			cloneForm();
		} else if ( !  $("#mitarbeiterneu")[0].checked ) {
			cloneForm();
		}
		buchenForm.goTo('buchungSenden')
	});

	$("#mitarbeiterNeuForm .required").addClass("req");
	$("#mitarbeiterNeuForm .req").removeClass("required");

	$("#mitarbeiterneu").click(function() {
		if ( $("#mitarbeiterneu")[0].checked ) {
			$("#mitarbeiterNeuForm .req").addClass("required");
		} else {
			$("#mitarbeiterNeuForm .req").removeClass("required");
		}

	});

});

var clones = 0;
var known = [];

function delClone(num) {
	var id = $("#clone"+num+"_id").text();
	$("#clone"+num+"_select").remove();
	$("#clone"+num+"_check").remove();
	$("#clone"+num+"_id").remove();
	$("#clone"+num+"_data").remove();
	$(".clone"+num).hide("slow");

	for ( var ix =0; ix < addedTeilnehmer.length; ix++ ) {
		if ( addedTeilnehmer[ix] == id) {
			addedTeilnehmer[ix] = -1;
			return;
		}
	}
}

function cloneForm() {
	for ( var ix =0; ix < addedTeilnehmer.length; ix++ ) {
		if ($("#input_id option:selected")[0].value == addedTeilnehmer[ix] && !$("#mitarbeiterneu")[0].checked ) {
			alert("Fehler!\nDer Teilnehmer ist bereits vorhanden");
			return;
		}
	}

	$("#buchungTeilnehmer input").valid();

	if ( $ ( "#buchungTeilnehmer .error").length != 0 ) {
		return false;
	}

	// daten kopieren
	$("#hiddenForm").append('<div id="clone'+clones+'_select"></div>');
	$("#hiddenForm").append('<div id="clone'+clones+'_check"></div>');
	$("#hiddenForm").append('<div id="clone'+clones+'_data"></div>');
	$("#hiddenForm").append('<div id="clone'+clones+'_id">'+	$("#input_id option:selected")[0].value +'</div>');

	var $select = $("#clone"+clones+"_select");
	var $data = $("#clone"+clones+"_data");
	var $check = $("#clone"+clones+"_check");
	
	var $page = $("#buchung");
	var temp = $page.find("input[name^=personTmpl]").clone(); // alle felder clonen die im div sind un dmit person anfangen

	if ( $("#mitarbeiterneu")[0].checked) {
		$("#Teilnehmer").append ( "<tr class='clone"+clones+"'><td>Name:</td><td>" + $("#input_personTmpl_vorname")[0].value + ", "+ $("#input_personTmpl_name")[0].value +"</td><td><a onclick='delClone("+clones+")' href='#'>entfernen</a></td></tr>");
	} else {
		addedTeilnehmer.push ( $("#input_id option:selected")[0].value );
		$("#Teilnehmer").append ( "<tr class='clone"+clones+"'><td>Name:</td><td>" + $("#input_id option:selected").text() +"</td><td><a onclick='delClone("+clones+")' href='#'>entfernen</a></td></tr>");
	}
//radio buttons und checkboxen
	known = [];
	temp.each( function(element) {
		var newName = this.name.replace(/personTmpl/g, "person[" + clones + "]");
		this.className = "clone" + clones;
		this.id = "";

		if ( this.type == "radio" || this.type=="checkbox" ) {
			if ( $.inArray(this.name, known) == -1 ) {
				known.push(this.name);
				var value = $page.find("input[name='"+this.name+"']:checked").val();

				$check.append( $("<input class='clone"+clones+"' type='hidden' name='" + newName + "' value='" + value + "' />") );
				this.name="pureTrash";
			} else {
				this.name="pureTrash";
			}
		} else {
			var value = $(this).val();
			$data.append( $("<input class='clone"+clones+"' type='hidden' name='" + newName + "' value='" + value + "' />") );
			this.name = newName;
		}
	});
// select elemente
	$page.find("select").each( function () {
		var value = $(this).val();
		var newName = this.name.replace(/personTmpl/g, "person[" + clones + "]");
		$select.append( $("<input class='clone"+clones+"' type='hidden' name='"+newName+"' value='"+ value +"' />") );
	//alert(newName + value)
	});

// alle inputs zuruecksetzen
	$page.find("input[name^='personTmpl']").each( function() {
		this.value = this.defaultValue;
	});

	$("#zustaendige_arbeitsagentur")[0].value ="";
	$("#teilnehmerBildungscheckArt")[0].value ="";
	$("#teilnehmerBildungscheckOrt")[0].value ="";
	$("#teilnehmerBildungscheckDatum")[0].value ="";


	clones = clones + 1;

	/* die boxen arbeitsagentur etc. verstecken */
	$("#arbeitsagentur").hide();
	$("#bildungscheck").hide();
	$("#hotelbuchen").hide();

	 $("#arbeitsagenturNein").selected();
	  $("#teilnehmerBildungscheckNein").selected();
	   $("#hotelBuchenNein").selected();
	   $("#buchung input").removeClass("required");
	   $("#buchung input").removeClass("error");
	return false;
};