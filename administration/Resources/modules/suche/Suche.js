/************************
 * require dependencies *
 ************************/
dojo.require("mosaik.ui.FlexTable");
dojo.provide("module.suche.Suche");
dojo.require("mosaik.core.Module");
dojo.require("module.kontakt.Umkreissuche");
dojo.require("module.suche.ErweiterteSuche");
dojo.require("dijit.form.Button");

dojo.declare("module.suche.Suche", [mosaik.core.Module], {
	_p: {
		 sCurrentAnfangsbuchstabe: null
	},
	
	constructor: function ( sandbox ) {
		console.log ("setting sandbox");
		this.sandbox = sandbox;
	},
	
	run: function () {

		this.layout = dijit.byId("borderContainer");
		// flex table init

		this.umkreissuche = dijit.byId("umkreissuche");
		dojo.connect ( this.umkreissuche, "onSearchButtonClick", this, "onUmkreissuche");
		this.showUmkreissuche();

		this.createAuswahlAnfangsbuchstabe();
				
		connectOnEnterKey("kontaktSucheName", this, "onKontaktSuchen");
		connectOnEnterKey("personSucheName", this, "personSuchenClick");
        this.flexTable.show();
        this.initFlexTable();
	},
	
	createAuswahlAnfangsbuchstabe: function () {
		// Summary:
		// create a letter chooser
		// sync with kontakt/kontak.js!!! 
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
					this.widgets.sKundenSuchen.set("checked", false);
					this.widgets.sAkquiseSuchen.set("checked",false);
					break;
				
				case "sKundenSuchen":
					this.widgets.sPersonenSuchen.set("checked",false);
					break;
					
				case "sAkquiseSuchen":
					this.widgets.sPersonenSuchen.set("checked",false);
					break;
			}
		}
	},
	
	onUmkreissuche: function( ) {

		this.flexTable.setTitle("Umkreissuche:");
		var options = this.umkreissuche.options;
		this.flexTable.clearContextMenu();
		var serviceURL = this.sandbox.getServiceUrl ( "kontakt/umkreissuche" );
		this.flexTable.queryService(serviceURL, options);
		
		
		this.flexTable.addContextMenuItem("Zur Person", "flextable/ukEditPerson");
		this.flexTable.addContextMenuItem("Zur Firma", "flextable/ukEditKontakt");
	},

	showUmkreissuche: function () {
		dojo.style(this.widgets.erweiterteSuche.domNode, "display", "none");
		dojo.style(this.widgets.umkreissuche.domNode, "display", "block");
		this.widgets.borderContainer.layout();
	},
	
	showErweiterteSuche: function () {
		dojo.style(this.widgets.erweiterteSuche.domNode, "display", "block");
		dojo.style(this.widgets.umkreissuche.domNode, "display", "none");
		this.widgets.borderContainer.layout();
	},

	setContextMenu: function (table) {
		currentModule.flexTable.clearContextMenu();
		
		switch ( table ) {
			case "ViewSeminarPreis":
				this.flexTable.addContextMenuItem("Termin bearbeiten", "flextable/editTermin");
				break;
			case "ViewBuchungPreis":
				this.flexTable.addContextMenuItem("Buchung bearbeiten", "flextable/editBuchung");
				this.flexTable.addContextMenuItem("Zum Termin", "flextable/editBuchungTermin");
				this.flexTable.addContextMenuItem("Zur Person", "flextable/editBuchungPerson");
				this.flexTable.addContextMenuItem("Zur Firma", "flextable/editBuchungFirma");
				break;
			case "ViewKontakt":
			case "Kontakt":
				this.flexTable.addContextMenuItem("Zur Firma", "flextable/editKontakt");
				break;
			case "ViewAkquise":
			case "AkquiseKontakt":
				this.flexTable.addContextMenuItem("Zum Akquise-Kontakt", "flextable/editAkquiseKontakt");
				break;
			case "ViewPerson":
			case "Person":
				this.flexTable.addContextMenuItem("Zur Person", "flextable/editPerson");
				break;
			case "ViewAkquiseKontaktR":
			case "AkquiseKontaktOrKontakt":
				this.flexTable.addContextMenuItem("Zum Kontakt / Akquise-Kontakt", "flextable/editKontaktOrAkquise");
				break;
			default:
				currentModule.flexTable.clearContextMenu();
		}
	},

	initFlexTable: function ( ) {
		// Summary:
		// connects buttons and flex table events
		console.log("onReady");
		
		if ( this._ftInitDone ) return;
			 
			 this._ftInitDone = true;
		
		console.log("onReady2");

// umkreissuche
		this.subscribeTo("flextable/ukEditPerson", "ftUkEditPerson");
		this.subscribeTo("flextable/ukEditKontakt", "ftUkEditKontakt");
		// misc
		this.subscribeTo("flextable/editKontakt", "ftEditKontakt");
		this.subscribeTo("flextable/editAkquiseKontakt",  "ftEditAkquiseKontakt");
		this.subscribeTo("flextable/editPerson", "ftEditPerson");
		this.subscribeTo("flextable/editKontaktOrAkquise", "ftEditKontaktOrAkquise");
		this.subscribeTo("flextable/editTermin", "ftEditTermin");
		this.subscribeTo("flextable/editBuchung", "ftEditBuchung");
		this.subscribeTo("flextable/editBuchungPerson", "ftEditBuchungPerson");
		this.subscribeTo("flextable/editBuchungTermin", "ftEditBuchungTermin");
		this.subscribeTo("flextable/editBuchungFirma", "ftEditBuchungFirma");
		
	},


	ftEditKontaktOrAkquise: function (data) {
		//var id=this.flexTable.getCurrentId();
		console.log("fn Called ==============================")
		console.dir(data);
		
		var prefix = data.id.substr(0,2);
		var id = data.id.substr(2,data.id.length -1);
		
		console.log("Found prefix: " + prefix + " " +id);
		
		if ( prefix == "ak") {
			sandbox.loadShellModule("akquiseKontaktBearbeiten", {akquiseKontaktId: id,
									results: this.flexTable.getAllRows()
			});
			
		} else {
			sandbox.loadShellModule("kontaktBearbeiten", {kontaktId: id,
									results: this.flexTable.getAllRows()
			});
		}
	},
	
	ftEditKontakt: function () {
		var id=this.flexTable.getCurrentId();
		
		sandbox.loadShellModule("kontaktBearbeiten", {kontaktId: id,
								results: this.flexTable.getAllRows()
		});
	},
	
	ftEditAkquiseKontakt: function () {
		var id=this.flexTable.getCurrentId();
		
		sandbox.loadShellModule("akquiseKontaktBearbeiten", {akquiseKontaktId: id,
								results: this.flexTable.getAllRows()
		});
	},
	
	ftEditPerson: function () {
		var id=this.flexTable.getCurrentId();
		
		sandbox.loadShellModule("personBearbeiten", {personId: id,
								results: this.flexTable.getAllRows()
		});
	},
	
	ftUkEditPerson: function (data) {
		console.log("edit person");
		var id=this.flexTable.getCurrent("person_id");
		
		sandbox.loadShellModule("personBearbeiten", {personId: id,
								results: this.flexTable.getAllRows()
		});
	},
	
	ftUkEditKontakt: function (data) {
		var id=this.flexTable.getCurrent("kontakt_id");
		
		sandbox.loadShellModule("kontaktBearbeiten", {kontaktId: id,
								results: this.flexTable.getAllRows()
		});
	},
	ftEditTermin: function (data) {
		console.log("Edti Termin: " + data.id);
		sandbox.loadShellModule("terminBearbeiten", {
			terminId: data.id,
			results: this.flexTable.getAllRows()
		});
	},

	ftEditBuchung: function (data) {
		console.log("Edit Buchung: " + data.id);
		sandbox.loadShellModule("buchungBearbeiten", {
			buchungId: data.id,
			results: this.flexTable.getAllRows()
		});
	},
	
	ftEditBuchungTermin: function (data) {
		console.log("Edit Termin: " + data.seminar_id);
		sandbox.loadShellModule("terminBearbeiten", {
			terminId: data.seminar_id,
			results: this.flexTable.getAllRows()
		});
	},
	
	ftEditBuchungPerson: function (data) {
		console.log("Edit Person: " + data.person_id);
		sandbox.loadShellModule("personBearbeiten", {
			personId: data.person_id,
			results: this.flexTable.getAllRows()
		});
	},
	
	ftEditBuchungFirma: function (data) {
		console.log("Edit Firma: " + data.kontakt_id);
		sandbox.loadShellModule("kontaktBearbeiten", {
			kontaktId: data.kontakt_id,
			results: this.flexTable.getAllRows()
		});
	},
	
	// DIV SUCHFUNKTIONEN
	kontakteSuchenClick: function() {
		if ( this._p.sCurrentAnfangsbuchstabe == null ) {
			alert ("Ungültige Eingabe: kein Anfangsbuchstabe ausgewählt.");
			return;
		}
		
		var anfangsbuchstabe = this._p.sCurrentAnfangsbuchstabe.getAttribute("char");
		var service = "#UNBEKANNT#";
		var kontext="";
		var lbl="";
		// FIXME!
		if ( this.widgets.sPersonenSuchen.get("checked") ) {
			lbl = "Personen:";
			service = "personSuchen";
			this.setupFlexPersonContextMenu();
		} else if ( this.widgets.sKundenSuchen.get("checked") && this.widgets.sAkquiseSuchen.get("checked") ){
			lbl = "Kunden & Akquise:";
			service = "kontaktSuchen";
			kontext = "all";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.sKundenSuchen.get("checked") ) {
            lbl = "Kunden:";
			service = "kontaktSuchen";
			kontext = "Kunde";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.sAkquiseSuchen.get("checked" )) {
            lbl = "Akquise:";
			service = "kontaktSuchen";
			kontext="Akquise";
			this.setupFlexKontaktContextMenu();
		} else if ( this.widgets.sPotKunde.get("checked" )) {
            lbl= "Pot. Kunde:";
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
		
		this.flexTable.setTitle("Personen:");
		this.setupFlexPersonContextMenu();
		this.flexTable.queryService(serviceURL, {"mode": "all", "q": suchString});
	},
	
	onKontaktSuchen: function () {
		
		var searchString = dijit.byId("kontaktSucheName").get("value");
		var kontext="";
        var lbl="";
		if ( dijit.byId("ksAlleAnzeigen").get("checked") ) {
			kontext="all";
            lbl="Alle:";
			//FIXME: akquise und kontakt?!
			this.setupFlexKontaktContextMenu();
		} else if (  dijit.byId("ksKundenAnzeigen").get("checked")) {
			kontext="Kunde";
            lbl="Kunden:";
			this.setupFlexKontaktContextMenu();
		} else if (  dijit.byId("ksAkquiseKontakteAnzeigen").get("checked")) {
			kontext="Akquise";
            lbl="Akquise:";
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
		} else if ( this.widgets.ksHesteller.get("checked" )) {
            lbl = "Hersteller:";
			service = "kontaktSuchen";
			kontext="Hersteller";
			this.setupFlexKontaktContextMenu();	
			
		} else {
			alert("Bitte wählen Sie die Datenbank in der gesucht werden soll aus.");
			return;
		}

        this.flexTable.setTitle(lbl);
		var serviceURL = this.sandbox.getServiceUrl ( "kontaktSuchen" );
		this.flexTable.queryService(serviceURL, {"q": searchString, "kontext": kontext});
	},
	
	// flex kontext menus
	setupFlexKontaktContextMenu: function () {
		this.flexTable.clearContextMenu();
		this.flexTable.addContextMenuItem("Zur Firma", "flextable/editKontakt");
	},

	setupFlexPersonContextMenu: function () {
		this.flexTable.clearContextMenu();
		this.flexTable.addContextMenuItem("Zur Person", "flextable/editPerson");
	}
});