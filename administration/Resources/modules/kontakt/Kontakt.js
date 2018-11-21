/************************
 * require dependencies *
 ************************/
dojo.require("mosaik.ui.FlexTable");
dojo.require("mosaik.core.Module");
dojo.require("dijit.form.Select");
dojo.require("dijit.form.CheckBox");
dojo.require("dijit.form.TextBox");
dojo.require("mosaik.ui.ErweiterteSuche");

dojo.provide("module.kontakt.Kontakt");

/****************************
 * listen for global events *
 ****************************/
dojo.declare("module.kontakt.Kontakt", mosaik.core.Module, {
	moduleName: "Kontakt",
	sandbox: null,
	layout: null,
	_ftInitDone: false,
	buttons: {
		personSucheBtn: null
	},
	
	constructor: function ( sandbox ) {
		console.log ("setting sandbox");
		this.sandbox = sandbox;
	},
	
	_p: {
		 sCurrentAnfangsbuchstabe: null
	},
	
	run: function () {
		this.layout = dijit.byId("borderContainer");


		this._p.sKundenSuchen = dijit.byId ("sKundenSuchen");
		this._p.sAkquiseSuchen = dijit.byId("sAkquiseSuchen");
		this._p.sPersonenSuchen = dijit.byId("sPersonenSuchen");

		this.createAuswahlAnfangsbuchstabe();


		// widget onClick event ! nicht dom on click

		
		setTimeout( dojo.hitch ( this, this._relayout ), 100);

        this.initFlexTable();

		
		dojo.connect(this.widgets.erweiterteSuche, "onAddRule", this.widgets.borderContainer, "layout");
		this.connectTo(this.widgets.erweiterteSuche, "onSearch", "onErweiterteSuche");
        this.widgets.borderContainer.layout();

		connectOnEnterKey("kontaktSucheName", this, "onKontaktSuchen");
		connectOnEnterKey("personSucheName", this, "personSuchenClick");

        this.flexTable.hide();
    },
	
	kontextEdit: function (data) {
        if ( typeof(data.kontext)=== "undefined") {
            this.ftEditPerson(data);
        } else {
            this.ftEditKontakt(data);
        }

    },
	
	initFlexTable: function () {
			// Summary:
		// connects buttons and flex table events
		if ( this._ftInitDone ) return;

		this._ftInitDone = true;
        this.flexTable.setTitle("Firmen:");
		this.subscribeTo("flextable/editKontakt", "ftEditKontakt");
		this.subscribeTo("flextable/editPerson", "ftEditPerson");
        this.subscribeTo("flextable/doubleClick", "kontextEdit");
	},

	setupFlexKontaktContextMenu: function () {


        this.flexTable.clearContextMenu();
		this.flexTable.addContextMenuItem("Zur Firma", "flextable/editKontakt");
	},

	setupFlexPersonContextMenu: function () {


        this.flexTable.clearContextMenu();
		this.flexTable.addContextMenuItem("Zur Person", "flextable/editPerson");
	},
	
	ftEditKontakt: function (data) {

		sandbox.loadShellModule("kontaktBearbeiten", {kontaktId: data.id,
								results: this.flexTable.getAllRows()
		});
	},

	ftEditPerson: function (data) {

		sandbox.loadShellModule("personBearbeiten", {personId: data.id,
			results: this.flexTable.getAllRows()
		});
	},
	
	_relayout: function () {
		this.layout.layout();
	},
	
	checkParameter: function (event) {
		if ( dojo.hasClass (event.target, "singleChar" )) {
			if ( this._p.sCurrentAnfangsbuchstabe ) {
				dojo.removeClass ( this._p.sCurrentAnfangsbuchstabe, "singleCharActive");
			}
			this._p.sCurrentAnfangsbuchstabe = event.target;
		
			dojo.addClass (event.target ,"singleCharActive");	
		}
		
		// transition from unchecked to checked
		if ( event.target.type=="checkbox" && event.target.checked === true) {
			switch ( event.target.id ) {
				case "sPersonenSuchen":
					this._p.sKundenSuchen.set("checked", false);
					this._p.sAkquiseSuchen.set("checked",false);
					break;
				
				case "sKundenSuchen":
					this._p.sPersonenSuchen.set("checked",false);
					break;
					
				case "sAkquiseSuchen":
					this._p.sPersonenSuchen.set("checked",false);
					break;
			}
		}
	},
	
	kontakteSuchenClick: function() {
		if ( this._p.sCurrentAnfangsbuchstabe == null ) {
			alert ("Ungültige Eingabe: kein Anfangsbuchstabe ausgewählt.");
			return;
		}
		this.flexTable.show();
		var anfangsbuchstabe = this._p.sCurrentAnfangsbuchstabe.getAttribute("char");
		var service = "#UNBEKANNT#";
		var kontext="";
		var lbl="";
		// FIXME!
		if ( this._p.sPersonenSuchen.get("checked") ) {
            lbl = "Personen:";
			service = "personSuchen";
			this.setupFlexPersonContextMenu();
		} else if ( this._p.sKundenSuchen.get("checked") && this._p.sAkquiseSuchen.get("checked") ){
            lbl = "Kunden & Akquise:";
			service = "kontaktSuchen";
			kontext = "all";
			this.setupFlexKontaktContextMenu();
		} else if ( this._p.sKundenSuchen.get("checked") ) {
            lbl = "Kunden:";
			service = "kontaktSuchen";
			kontext = "Kunde";
			this.setupFlexKontaktContextMenu();
		} else if ( this._p.sAkquiseSuchen.get("checked" )) {
            lbl = "Akquise:";
			service = "kontaktSuchen";
			kontext="Akquise";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.sPotKunde.get("checked" )) {
            lbl = "Pot. Kunde:";
			service = "kontaktSuchen";
			kontext="potKunde";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.sInfo.get("checked" )) {
            lbl = "Info:";
			service = "kontaktSuchen";
			kontext="Info";
			this.setupFlexKontaktContextMenu();	
		} else if ( this.widgets.sReferent.get("checked" )) {
            lbl = "Referent:";
			service = "kontaktSuchen";
			kontext="Referent";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.sUnbekannt.get("checked" )) {
            lbl = "Unbekannt:";
			service = "kontaktSuchen";
			kontext="Unbekannt";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.sHesteller.get("checked" )) {
            lbl = "Hersteller:";
			service = "kontaktSuchen";
			kontext="Hersteller";
			this.setupFlexKontaktContextMenu();
		} else {
			alert("Bitte wählen Sie die Datenbank in der gesucht werden soll aus.");
			return;
		}

        this.flexTable.setTitle(lbl);
		var serviceURL = this.sandbox.getServiceUrl ( service );
		this.flexTable.queryService(serviceURL, {"mode": "all","a": anfangsbuchstabe, "kontext": kontext});
	},

	personSuchenClick: function () {
		var serviceURL = this.sandbox.getServiceUrl ( "personSuchen" );
		var suchString = dijit.byId("personSucheName").get("value");
		
		if ( suchString == "" ) {
			alert("Personensuche:\nBitte geben Sie einen Namen ein.");
			return;
		}
        this.flexTable.show();

        this.flexTable.setTitle("Personen:");
		this.setupFlexPersonContextMenu();
		this.flexTable.queryService(serviceURL, {"mode": "all", "q": suchString});
	},

	createAuswahlAnfangsbuchstabe: function () {
		// Summary:
		// create a letter chooser
		// sync with suche/suche.js!!! 
		var target=dojo.byId("auswahlAnfangsbuchstabe");
		var start = "A".charCodeAt(0);
		var ende = "Z".charCodeAt(0);
		var i=start;
		var rn = dojo.create("span");
		
		for ( i; i<=ende; i++) {
			var chr = String.fromCharCode(i);
			var node = dojo.create("span", {innerHTML: chr, "class": "singleChar", "char": chr});
			
			rn.appendChild(node);
		}
		
		target.appendChild ( rn );
				this.connectTo (dojo.byId ("sucheAnfangsbuchstabe"),"onclick", "checkParameter");

	},
	
	kontaktBearbeiten: function ( ) {
	
	},

	onKontaktSuchen: function () {
		
		var searchString = dijit.byId("kontaktSucheName").get("value");
		var kontext="";
		var lbl="";
		
		if ( dijit.byId("ksAlleAnzeigen").get("checked") ) {
			kontext="all";
            lbl="Alle"
			//FIXME: akquise und kontakt?!
			this.setupFlexKontaktContextMenu();
		} else if (  dijit.byId("ksKundenAnzeigen").get("checked")) {
			kontext="Kunde";
			this.setupFlexKontaktContextMenu();
		} else if (  dijit.byId("ksAkquiseKontakteAnzeigen").get("checked")) {
			kontext="Akquise";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.ksPotKunde.get("checked" )) {
            lbl = "Pot. Kunde:";
			service = "kontaktSuchen";
			kontext="potKunde";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.ksInfo.get("checked" )) {
            lbl = "Info:";
			service = "kontaktSuchen";
			kontext="Info";
			this.setupFlexKontaktContextMenu();	
		} else if ( this.widgets.ksReferent.get("checked" )) {
            lbl = "Referent:";
			service = "kontaktSuchen";
			kontext="Referent";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.ksUnbekannt.get("checked" )) {
            lbl = "Unbekannt:";
			service = "kontaktSuchen";
			kontext="Unbekannt";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.ksHersteller.get("checked" )) {
            lbl = "Hersteller:";
			service = "kontaktSuchen";
			kontext="Hersteller";
			this.setupFlexKontaktContextMenu();	
			
		} else {
			alert("Kontaktsuche:\nBitte wählen Sie die Datenbank in der gesucht werden soll aus.");
			return;
		}
		
		if ( searchString == "" ) {
			alert("Kontaktsuche:\nBitte geben Sie einen Namen ein.");
			return;
		}
        this.flexTable.show();


        this.flexTable.setTitle(lbl);

		var serviceURL = this.sandbox.getServiceUrl ( "kontaktSuchen" );
		this.flexTable.queryService(serviceURL, {"q": searchString, "kontext": kontext});
	},
	
	showErweiterteSuche: function () {
		dojo.style(this.widgets.erweiterteSuche.domNode, "display", "block");
		this.widgets.borderContainer.layout();
	},
	
	onErweiterteSuche: function (rules, table) { 
		var qA=[];
		
		for ( var i=0; i< rules.length; i++) {
			qA.push ( rules[i].fieldName + ":"+rules[i].type+";" + rules[i].operator + ";" + rules[i].value); 
			if ( rules.length != i+1) {
				qA.push( rules[i+1].chainCommand);
			}
		}
		
		var qS = qA.join("#:#");
        this.flexTable.show();

        var serviceURL = this.sandbox.getServiceUrl ( "advSearch/" + table );
		console.log("ServiceID:  advSearch/" + table  );
		console.log(serviceURL);
		console.log(qS);
		
		this.flexTable.queryService(serviceURL, {advancedSearch: 1, rules: qS});
	},

	showAllKontakte: function () {
		var serviceURL = this.sandbox.getServiceUrl ( "kontaktSuchen" );
        this.flexTable.show();

        this.flexTable.setTitle("Alle Kontakte");
        this.flexTable.queryService(serviceURL, {"all": 1});
	}
 });
