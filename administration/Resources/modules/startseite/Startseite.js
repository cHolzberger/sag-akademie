/** require dependencies **/
dojo.require("mosaik.ui.FlexTable");
dojo.require("mosaik.core.Module");
dojo.require("dijit.form.CheckBox")
dojo.require("module.startseite.Termine");
dojo.require("module.startseite.Statistiken");
dojo.require("module.startseite.Todo");
dojo.provide("module.startseite.Startseite");

var d = dojo;
var _$ = dojo.byId;

dojo.declare("module.startseite.Startseite", [mosaik.core.Module], {
	userInfo : null,
	startseiteInfo : null,
	mainLayout : null,
	flexTableServiceURL : "",
	startseiteService : null,
	moduleName : "Startseite",
	moduleVersion : "4",
	models : null,
	_ftOptions : null,
	_ftInitDone : false,

	boxes : {
		"statistiken" : null
	},

	run : function(/* Object[] */options) {
		// Summary:
		// module execution starts here
		//this.flexTableContainer = dijit.byId('flexTableContainer');
		this.flexTableServiceURL = this.sandbox.getServiceUrl("startseite/buchungen");

		// info setzen und cache aktualisieren
		this.setInfo();

		// globale topics
		dojo.subscribe("startseite/update", this, this.updateLayout);

        this.initFlexTable();
        this.flexTable.show();
	},



	setUserInfo : function() {
		this.userInfo.dump();
	},
	updateLayout : function() {
		// Summary:
		// updates the container layout


	},
	setInfo : function() {
	//	this.flexTableContainer.layout();

	},
	updateFlexTable : function() {
		// Summary:
		// sets flex table options
		// and relayouts if necessary
		console.log("====> UPDATE FLEX TABLE");
        this.flexTable.queryService(this.flexTableServiceURL, this.flexTable.getOptions());
    },

    ftOptionsChanged: function (id, value,checked) {
        sandbox.setUserVar(id, checked);
        this.updateFlexTable();
    },

	initFlexTable : function() {
		// Summary:
		// connects buttons and flex table events

		if(this._ftInitDone)
			return;

        this.flexTable.clearOptions();
        this.flexTable.setTitle("Buchungen:");
        this.flexTable.addOption("7-Tage Rückblick", "siebenTageRueckblick",1, sandbox.getUserVar("siebenTageRueckblick",true));
        this.flexTable.addOption("Buchungen seit der letzten Anmeldung","neueBuchungen",1, sandbox.getUserVar("neueBuchungen",false));
        this.flexTable.addOption("Buchungen Darmstadt", "buchungenDarmstadt",1,sandbox.getUserVar("buchungenDarmstadt",false));
        this.flexTable.addOption("Buchungen Lünen", "buchungenLuenen",1,sandbox.getUserVar("buchungenLuenen",false));

        this.connectTo(this.flexTable, "onResume", "updateFlexTable");

        this.subscribeTo("flextable/optionChanged", "ftOptionsChanged");
        this.subscribeTo("flextable/editBuchung", "editBuchung");
        this.subscribeTo("flextable/editTermin", "editTermin");
		this.subscribeTo("flextable/editPerson", "editPerson");
        this.subscribeTo("flextable/editFirma", "editFirma");
        this.subscribeTo("flextable/doubleClick", "editBuchung");

        this.flexTable.addContextMenuItem("Buchung bearbeiten", "flextable/editBuchung");
        this.flexTable.addContextMenuItem("Zum Termin", "flextable/editTermin");
        this.flexTable.addContextMenuItem("Zur Person", "flextable/editPerson");
        this.flexTable.addContextMenuItem("Zur Firma", "flextable/editFirma");

		this.updateFlexTable();
		this._ftInitDone = true;
	},
	editBuchung : function(data) {
		console.log("Edit Buchung: " + data.id);
		sandbox.loadShellModule("buchungBearbeiten", {
			buchungId : data.id,
			results : this.flexTable.getAllRows()
		});
	},
	editTermin : function(data) {
		console.log("Edit Termin: " + data.seminar_id);
		sandbox.loadShellModule("terminBearbeiten", {
			terminId : data.seminar_id
		});
	},
	editPerson : function(data) {
		console.log("Edit Person: " + data.person_id);
		sandbox.loadShellModule("personBearbeiten", {
			personId : data.person_id
		});
	},
	editFirma : function(data) {
		console.log("Edit Firma: " + data.kontakt_id);
		sandbox.loadShellModule("kontaktBearbeiten", {
			kontaktId : data.kontakt_id
		});
	},
	ftOpenTermin : function() {
		var row = this.flexTable.getCurrentRow();
		var id = row.seminar_id;

		sandbox.loadShellModule("terminBearbeiten", {
			terminId : id
		});
	},
	ftOpenBuchung : function() {
		var row = this.flexTable.getCurrentRow();
		var id = row.id;

		console.dir(row);
		
		sandbox.loadShellModule("buchungBearbeiten", {
			buchungId : id,
			results : this.flexTable.getAllRows()
		});
	},
	ftOpenPerson : function() {
		var row = this.flexTable.getCurrentRow();
		var id = row.person_id;

		sandbox.loadShellModule("personBearbeiten", {
			personId : id
		});
	},
	ftOpenFirma : function() {
		var row = this.flexTable.getCurrentRow();
		var id = row.kontakt_id;

		sandbox.loadShellModule("kontaktBearbeiten", {
			kontaktId : id
		});
	},
});
