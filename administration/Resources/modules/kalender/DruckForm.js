//TODO: Ids aus dem template raus
dojo.provide("module.kalender.DruckForm");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.CheckBox");
dojo.require("dijit.form.RadioButton");


dojo.declare("module.kalender.DruckForm", [dijit._Widget, dijit._Templated], {
	widgetsInTemplate: true,
	templateString: dojo.cache("module.kalender", "DruckForm.html"),

	constructor: function () {
		this.currentYear = null;
		this.options = null;
	},
	
	_setCurrentYearAttr: function (val) {
		this.currentYear = val;
	},
	
	_getCurrentYearAttr: function () {
		return this.currentYear;
	},
	
	startup: function () {	
		dojo.query(".druckOption").forEach(function ( node, index, nodelist) {
			node.style.display="none";
		});
		
		dojo.query(".druckOption-monat").forEach(function ( node, index, nodelist) {
			node.style.display="block";
		});
		
		this.druckStandort.addOption( sandbox.getSelectArray("Standort"));
	
	},
	
	_changeZeitraum: function () {
		var selected = this.druckZeitraum.get("value");
		
		dojo.query(".druckOption").forEach(function ( node, index, nodelist) {
			node.style.display="none";
		});
		
		dojo.query(".druckOption-"+selected).forEach(function ( node, index, nodelist) {
			node.style.display="block";
		});
		
	},
	
	_changeAlleStandorte: function () {
		if ( this.alleStandorte.get("checked")) {
			dojo.style ("druckStandortContainer", "display", "none");
		} else {
			dojo.style ("druckStandortContainer", "display", "block");
		}
	},
	
	_onDrucken: function () {
		var formData = this.druckForm.get("value");
		
		console.dir(formData);
		
		var token = sandbox.getUserinfo().auth_token;
		var detail = formData.selExtInfo;
		var currentYear = this.get("currentYear");
		var timeFrame = "";

	
		var standort = formData.druckStandort;
		
		if ( formData.alleStandorte[0] == "true") {
			standort="alle";
		}
		
		var zeitraum = formData.druckZeitraum; 
		
		if (zeitraum == "monat") {
			timeFrame = formData.selMonat;
		} else if ( zeitraum =="quartal") {
			timeFrame = formData.selQuartal;
		} else if ( zeitraum =="jahr") {
			timeFrame = "jahr"; 
		} else {
			timeFrame = formData.selHj;
		}

		var queryString = "?token=" + token + "&detail=" + detail + 
		"&zeitraum=" + zeitraum + 
		"&standort=" + standort;
		
		
		
		var pdfurl = sandbox.getServiceUrl ( "print/kalender" ) + currentYear + "-"+timeFrame+queryString;
		
		app.openPdf(pdfurl);
	}
	
});