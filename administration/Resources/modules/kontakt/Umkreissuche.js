dojo.provide("module.kontakt.Umkreissuche");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.CheckBox");
dojo.declare("module.kontakt.Umkreissuche", [dijit._Widget, dijit._Templated], {
	widgetsInTemplate: true,
	templateString: dojo.cache("module.kontakt", "Umkreissuche.html"),
	
	kategorieCheck: null,
	taetigkeitsbereichCheck: null,
	brancheCheck: null,
	
	umkreisStandort: null, //drop down filled by template
	radiusStandort: null, //drop down filled by template
	
	options: null,
	
	startup: function () {
		this.options = {};
		this.setupUmkreissuche();
	},
	

	
	setupUmkreissuche: function () {
		var standorte = sandbox.getSelectArray("ViewStandortKoordinaten");
		var kategorie = sandbox.getSelectArray ("KontaktKategorie");
		var branche = sandbox.getSelectArray ("XBranche");
		var taetigkeitsbereich = sandbox.getSelectArray ("XTaetigkeitsbereich");
	
		var i=0;
		
		// -- STANDORT
		this.umkreisStandort.options = standorte;
		this.umkreisStandort.set("value",-1);
		// -- RADIUS
		var umkreisRadiusOpt = [];
		for (  i=1; i <= 10; i++) {
			var radius = (i*50).toString();
			umkreisRadiusOpt.push ( {"value": radius, "label": radius + " km"});
		}
		this.radiusStandort.options = umkreisRadiusOpt;
		
		
		this.radiusStandort.set("value",-1);
		// - KATEGORIE AUSWAHL
		var id = "kategorie:";
		var node = null;
		var input = null;
		
		this.kategorieCheck = [];
		for ( i=0; i < kategorie.length; i++) {
			console.dir(kategorie[i]);
			node = dojo.create ("div", { }, this.kategorieSelect);
			input = dojo.create("input", {id: id + kategorie[i].value , value: kategorie[i].value}, node);
			this.kategorieCheck.push( new dijit.form.CheckBox({value: kategorie[i].value}, input));
			dojo.create("span", {innerHTML: kategorie[i].label}, node);
			dojo.create("br",{},node);
		}
		
		// - TAETIGKEITSBEREICH AUSWAHL
		id = "taetigkeitsbereich:";
		node = null;
		input = null;
		this.taetigkeitsbereichCheck = [];
		

		for ( i=0; i < taetigkeitsbereich.length; i++) {
			console.dir(taetigkeitsbereich[i]);
			node = dojo.create ("div", { }, this.taetigkeitsbereichSelect);
			input = dojo.create("input", {id: id + taetigkeitsbereich[i].value , value: taetigkeitsbereich[i].value, "class": ""}, node);
			this.taetigkeitsbereichCheck.push(new dijit.form.CheckBox({value: taetigkeitsbereich[i].value}, input));
			
			dojo.create("span", {innerHTML: taetigkeitsbereich[i].label}, node);
			dojo.create("br",{},node);
		}
		
		// - BRANCHE AUSWAHL
		id = "branche:";
		node = null;
		input = null;
		this.brancheCheck = [];
		for ( i=0; i < branche.length; i++) {
			console.dir(branche[i]);
			node = dojo.create ("div", { }, this.brancheSelect);
			input = dojo.create("input", {id: id + branche[i].value , value: branche[i].value}, node);
			this.brancheCheck.push(new dijit.form.CheckBox({ value: branche[i].value }, input));
			
			dojo.create("span", {innerHTML: branche[i].label}, node);
			dojo.create("br",{},node);
		}
	},
	
	getChecked: function () {
		var ret = {}
		ret.branche = [];
		ret.taetigkeitsbereich = [];
		ret.kategorie = [];
		
		dojo.forEach ( this.brancheCheck, function (item) {
			if ( item.get("checked")) {
				ret.branche.push ( item.get("value"));
			}
		} );
		
		dojo.forEach ( this.kategorieCheck, function (item) {
			if ( item.get("checked")) {
				ret.kategorie.push ( item.get("value"));
			}
		} );
		
		dojo.forEach ( this.taetigkeitsbereichCheck, function (item) {
			if ( item.get("checked")) {
				ret.taetigkeitsbereich.push ( item.get("value"));
			}
		} );
		
		return ret;
	},
	
	updateOptions: function () {
		this.options = {};
		this.options.ausgangsort = this.umkreisStandort.get("value");
		this.options.umkreis = this.radiusStandort.get("value");
		this.options.newsletter = this.cbNewsletter.get("value");
		this.options.email = this.cbEMail.get("value");
		var db=[];
		
		if ( this.cbAkquise.get("checked") ) {
			db.push(this.cbAkquise.get("value") )
		}
		
		if ( this.cbPersonen.get("checked") ) {
			db.push(this.cbPersonen.get("value") )
		}
		
		if ( this.cbFirmen.get("checked") ) {
			db.push(this.cbFirmen.get("value") )
		}	
		
		var checked = this.getChecked();

		this.options.db = JSON.stringify ( db );
		
		this.options.branche = JSON.stringify ( checked.branche);
		this.options.taetigkeitsbereich = JSON.stringify ( checked.taetigkeitsbereich );
		this.options.kategorie = JSON.stringify ( checked.kategorie );
	},
	
	_onSearchButtonClick: function () {
		
		this.updateOptions();
		console.dir(this.options);
		this.onSearchButtonClick();
	},
	
	onSearchButtonClick: function () {
		//Summary:
		// just a proxy
	}
});