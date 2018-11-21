dojo.provide("module.administration.Administration");
dojo.require("mosaik.core.Module");
dojo.require("dijit.form.Button");
dojo.require("mosaik.ui.FlexTable");
dojo.require("dijit.layout.BorderContainer");
dojo.require("dijit.layout.StackContainer");
dojo.require("dijit.layout.ContentPane");

dojo.declare("module.administration.Administration", [mosaik.core.Module], {
	handles : null,
	initDone : false,
	_feiertagJahr : 2012,

	constructor : function() {
		this.handles = [];
	},
	run : function() {
        this.ftReady();
	},

	ftReady : function() {
		if(this.initDone) return;
        this.flexTable.show();

		this.selectStandort();
		this.initDone = true;

		this.subscribeTo("flextable/editBenutzer", "_editBenutzer");
		this.subscribeTo("flextable/editStandort", "_editStandort");
		this.subscribeTo("flextable/editHotel", "_editHotel");
		this.subscribeTo("flextable/editFerienTag","_editFeiertag");
	},

	selectStandort : function() {
		dojo.query(".newBtn").forEach(function(item) {
			item.style.display = "none";
		});

		this.widgets._neuerStandort.domNode.style.display = "block";

		this.widgets.stackContainer.selectChild(this.widgets.standortPane);

		this.flexTable.clearContextMenu();
		this.flexTable.addContextMenuItem("Bearbeiten", "flextable/editStandort");

		var service = this.sandbox.getServiceUrl("admin/standorte");
		this.flexTable.queryService(service, {});
	},
	_editStandort : function(data) {
		var options = {
			standortId : data.id
		};
		options.results = this.flexTable.getAllRows();

		sandbox.loadShellModule("standortBearbeiten", options);

		console.log("Edit Hotel");
	},
	createStandort : function() {
		var options = {
			create : true
		};
		sandbox.loadShellModule("standortBearbeiten", options);
	},
	selectHotel : function() {
		dojo.query(".newBtn").forEach(function(item) {
			item.style.display = "none";
		});

		this.widgets._neuesHotel.domNode.style.display = "block";

		this.widgets.stackContainer.selectChild(this.widgets.hotelPane);

		var service = this.sandbox.getServiceUrl("admin/hotels");

		this.flexTable.clearContextMenu();
		this.flexTable.addContextMenuItem("Bearbeiten", "flextable/editHotel");

		this.flexTable.queryService(service, {});
	},
	_editHotel : function(data) {
		try {
			var options = {
				hotelId : data.id
			};

			// syntax error from flex?! whats up there?!
			options.results = this.flexTable.getAllRows();

			sandbox.loadShellModule("hotelBearbeiten", options);
		} catch ( e ) {
			console.error("-------------------> Error");
			console.error(e);
		}
	},
	createHotel : function() {
		var options = {
			create : true
		};
		sandbox.loadShellModule("hotelBearbeiten", options);
	},
	selectBenutzer : function() {
		dojo.query(".newBtn").forEach(function(item) {
			item.style.display = "none";
		});

		this.widgets._neuerBenutzer.domNode.style.display = "block";

		this.widgets.stackContainer.selectChild(this.widgets.benutzerPane);

		this.flexTable.clearContextMenu();
		this.flexTable.addContextMenuItem("Bearbeiten", "flextable/editBenutzer");

		var service = this.sandbox.getServiceUrl("admin/benutzer");
		this.flexTable.queryService(service, {});
	},
	_editBenutzer : function(data) {

		var options = {
			benutzerId : data.id
		};
		options.results = this.flexTable.getAllRows();

		sandbox.loadShellModule("benutzerBearbeiten", options);

		console.log("Edit Hotel");
	},
	createBenutzer : function() {
		var options = {
			create : true
		};
		sandbox.loadShellModule("benutzerBearbeiten", options);
	},
	selectDoublettenabgleich : function() {

	},
	selectKategorieEditor : function() {
		// Summary:
		// jumps to different module to edit kategories
		sandbox.loadShellModule("kategorieEditor", {});
	},
	setFeiertagYear : function(year) {
		this._feiertagJahr = year;

		var service = this.sandbox.getServiceUrl("kalender/ferien");
		this.flexTable.queryService(service, {
			year : this._feiertagJahr
		});

		dojo.query(".linkList .yearSelect").forEach(function(node, index, arr) {
			dojo.removeClass(node, "hoverBtnActive");
		});

		dojo.query(".linkList .yearSelect-" + year).forEach(function(node) {
			dojo.addClass(node, "hoverBtnActive");
		});
	},
	selectFerienEditor : function() {
		dojo.query(".newBtn").forEach(function(item) {
			item.style.display = "none";
		});

		this.widgets._neuerFeiertag.domNode.style.display = "block";

		this.widgets.stackContainer.selectChild(this.widgets.ferienPane);

		this.flexTable.clearContextMenu();
		this.flexTable.addContextMenuItem("Bearbeiten", "flextable/editFerienTag");

		this.setFeiertagYear(this._feiertagJahr);
	},
	_editFeiertag : function(data) {
		sandbox.loadShellModule("feiertagBearbeiten", {
			feiertagId : data.id
		});
	},
	createFeiertag : function() {
		var options = {
			create : true
		};
		sandbox.loadShellModule("feiertagBearbeiten", options);
	}
});
