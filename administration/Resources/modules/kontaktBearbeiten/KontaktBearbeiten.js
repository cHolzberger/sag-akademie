/** require dependencies **/
dojo.require("mosaik.core.Module");
dojo.require("mosaik.db.DataDriven");

dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.form.Textarea');
dojo.require('dijit.form.DateTextBox');

dojo.require("mosaik.ui.FlexTable");
dojo.require("mosaik.ui.DatasetNavigator");
dojo.require("mosaik.ui.WeitereInformationen");

dojo.provide("module.kontaktBearbeiten.KontaktBearbeiten");
var d=dojo;
var _$=dojo.byId;

dojo.declare("module.kontaktBearbeiten.KontaktBearbeiten", [mosaik.core.Module], {
	moduleName: "KontaktBearbeiten",
	moduleVersion: "1",
	dropdowns: null,
	service: null,
	formPrefix: "Kontakt",
	_options: null,
	
	knownKontext: [
		{label: "Kunde", value: "Kunde"},
		{label: "Aqkuise", value: "Akquise"},
		{label: "pot. Kunde", value: "potKunde"},
		{label: "Info", value: "Info"},
		{label: "Referent", value: "Referent"},
		{label: "Hersteller", value: "Hersteller"},
		{label: "Unbekannt", value: "Unbekannt"}
	],

	constructor: function () {
		this.dropdowns = {};
	},

	run: function (options) {
		console.debug("KontaktBearbeiten::run >>>");
		this.service =  this.sandbox.getRpcService("database/kontakt");

		
		this._options = options ? options : {};
		this.dropdowns = {};
		this.initDropdowns();
		this.linkButtons();

		if ( typeof ( options.create ) !== "undefined") {
			this.onCreate();
		} else {
			this.fetchData();
		}
		

		console.debug ("<<< KontaktBearbeiten::run");

		this.widgets.dnav.update( options, "kontaktId", "kontaktBearbeiten");
		dojo.connect(this, "onValuesSet", this, "onValuesUpdate");
        this.initFlexTable();
//        this.ftRestoreState();
    },


    ftRestoreState: function () {
        if ( !this._options.table_mode || this._options.table_mode=="mitarbeiter") {
            this.tableMitarbeiter();
        } else {
            this.tableSeminare();
        }
    },

    onResume: function () {
        this.ftRestoreState();
    },

	onCreate: function() {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;

		this.flexTable.hide();
		this.widgets.chooserFrame.show();
		this._createButtonHandle = dojo.connect( this.widgets.createButton, "onClick", this, "createDone");


		//this.nextButton = dijit.byId("nextButton");
		//this.prevButton = dijit.byId("prevButton");


		this.createKontaktDetail();
	},

	createKontaktDetail: function () {
		this.widgets.chooserFrame.set("title","Firma anlegen");
		this.widgets.chooserStack.selectChild( dijit.byId("kontaktDetailPane"));

		//this.nextButton.domNode.style.display="none";
		//this.prevButton.domNode.style.display="none";
		this.widgets.createButton.domNode.style.display="block";
	},

	createDone: function () {
		sandbox.showLoadingScreen("Daten speichern...");

		var kontaktName = dijit.byId("newKontaktName").get("value");

		this.service.create( kontaktName ).addCallback ( dojo.hitch ( this, "setValue")).addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> KontaktBearbeiten::create Error: "+data);
		}));
	},

	fetchData: function () {
		this.service.find(this._options.kontaktId).addCallback ( dojo.hitch ( this, "setValue" ) )
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> KontaktBearbeiten::find Error: ");
			console.dir(data);
		}));

		this.service.getAnsprechpartner(this._options.kontaktId).addCallback ( dojo.hitch ( this, "onAnsprechpartner" ) )
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> KontaktBearbeiten::getAnsprechpartner Error: " );
			console.dir(data);
		}));
	},
	
	onValuesUpdate: function () {
		var data = this._currentData;
        this._options.kontaktId = data.id;
		// called when the current data is changed
		console.log("==> Values updated");
		// Summary:
		// update handler for the various loading functions
		this._ignoreUpdate = true;
		console.dir(data);

		this.initStaticFields();
	
		this.widgets.chooserFrame.hide();
		this.flexTable.show();
		sandbox.hideLoadingScreen();
		
		this.widgets.weitereInformationen.setInformation(data);
		//this.initStaticFields();
		this._ignoreUpdate = false;

        this.ftRestoreState();

    },

	onSave: function () {
		sandbox.showLoadingScreen("Daten speichern...");

		console.log("Save");
		this.service.save( this._currentData.id, this._changedData )
		.addCallback( dojo.hitch ( this, "setValue") )
		.addErrback(function (data) {
			console.log ("Seminar-Save Error: " + data);
		});
	},

	initDropdowns: function () {
		// dom elemente holen
		this.dropdowns.kategorie = dijit.byId ('Kontakt:kontaktkategorie');
		this.dropdowns.kontext = dijit.byId ('Kontakt:kontext');

		this.dropdowns.taetigkeitsbereich = dijit.byId ('Kontakt:taetigkeitsbereich_id');
		this.dropdowns.branche = dijit.byId("Kontakt:branche_id");
		this.dropdowns.bundesland = dijit.byId("Kontakt:bundesland_id");
		this.dropdowns.land = dijit.byId("Kontakt:land_id");
		this.dropdowns.status = dijit.byId("Kontakt:kundenstatus");

		// dropdowns neue listen zuweisen
		this.dropdowns.kategorie.set("options", sandbox.getSelectArray("KontaktKategorie"));
		
		this.dropdowns.status.set("options", sandbox.getSelectArray("KontaktStatus"));
		//this.dropdowns.kategorie.set("value", -1);
		this.dropdowns.taetigkeitsbereich.set ( "options",  sandbox.getSelectArray("XTaetigkeitsbereich"));
		//this.dropdowns.taetigkeitsbereich.set ( "value", -1);

		this.dropdowns.branche.set("options", sandbox.getSelectArray("XBranche"));
		//this.dropdowns.branche.set("value", -1);

		this.dropdowns.bundesland.set("options", sandbox.getSelectArray("XBundesland"));
		//this.dropdowns.bundesland.set("value", -1);

		this.dropdowns.land.set("options", sandbox.getSelectArray("XLand"));
		this.dropdowns.kontext.set("options", dojo.clone(this.knownKontext));
		//this.dropdowns.land.set("value", -1);
	},

	initStaticFields: function() {
		
	},

	linkButtons: function () {
	
	},
	
	_ftInitDone: false,
	initFlexTable: function () {
		// Summary:
		// connects buttons and flex table events

		if ( this._ftInitDone ) return;

        this.subscribeTo("flextable/editPerson", "editPerson");
        this.subscribeTo("flextable/editTermin", "ftEditTermin");


		this._ftInitDone = true;
		
		//this.updateFlexTable();
	},
	
	editPerson: function ( data ) {
		// Summary:
		// loads personBearbeiten module passes in the personId
		console.log("Edit Person: " + data.id);
		sandbox.loadShellModule("personBearbeiten", {
			personId: data.id
		});
	},

    ftEditTermin : function(data) {
        console.log("Edti Termin: " + data.id);
        sandbox.loadShellModule("terminBearbeiten", {
            terminId : data.id,
            results : this.flexTable.getAllRows()
        });
    },
	
	updateFlexTable: function () {
		// Summary:
		// sets flex table options
		// and relayouts if necessary

		console.log("====> UPDATE FLEX TABLE");
		this.flexTable.resetParameters();
		// flex table service url setzen
		var serviceURL =this.flexTableServiceURL;

		this._ftOptions = {
			kontaktId: this._options.kontaktId
		};

		this.flexTable.queryService(serviceURL,this._ftOptions );
	},

	doPrint: function() {
		var token = sandbox.getUserinfo().auth_token;
		
		
		var pdfurl = this.sandbox.getServiceUrl ( "print/kontakt" ) + this._currentData.id + "?token=" + token;
		
		app.openPdf(pdfurl);
	},

	onAnsprechpartner: function(data) {
		this.nodes.ansprechpartnerName.innerHTML = data.name + ", " + data.vorname;
		this.nodes.ansprechpartnerEMail.innerHTML = data.email;
		this.nodes.ansprechpartnerTel.innerHTML = data.tel;
		this.nodes.ansprechpartnerMobil.innerHTML = data.mobil;
	},
	
	tableMitarbeiter: function () {
        var self=this;
        self.flexTable.clearContextMenu();
        self.flexTable.addContextMenuItem("Person bearbeiten", "flextable/editPerson");

        this.flexTable.setTitle("Mitarbeiter:");


        this._options.table_mode = "mitarbeiter";

        var token = sandbox.getUserinfo().auth_token;

        this.flexTableServiceURL = this.sandbox.getServiceUrl ( "personSuchen" ) + "?token=" + token;

		this.updateFlexTable();

    },
	
	tableSeminare: function () {
        var self = this;
        self.flexTable.clearContextMenu();
        self.flexTable.addContextMenuItem("Termin bearbeiten", "flextable/editTermin");

        this.flexTable.setTitle("Seminare:");

        this._options.table_mode = "seminare";

        var token = sandbox.getUserinfo().auth_token;

        this.flexTableServiceURL = this.sandbox.getServiceUrl ( "seminar/termine" ) + "?token=" + token + "&kontaktId=" + this._options.kontaktId;

		this.updateFlexTable();




	}
});
