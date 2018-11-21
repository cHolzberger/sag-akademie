/** require dependencies **/
dojo.require("dojo.number");

dojo.require("mosaik.core.Module");
dojo.require("mosaik.db.DataDriven");

dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.Editor');
dojo.require('dijit.form.DateTextBox');
dojo.require('dijit.form.CurrencyTextBox');
dojo.require("dojox.widget.Standby");

dojo.require('dijit.Editor');
dojo.require("dijit._editor.plugins.AlwaysShowToolbar");

dojo.require('mosaik.ui.FlexTable');
dojo.require('mosaik.ui.DatasetNavigator');

dojo.require("mosaik.util.Mailto")

dojo.provide("module.standortBearbeiten.StandortBearbeiten");
var d=dojo;
var _$=dojo.byId;

dojo.declare("module.standortBearbeiten.StandortBearbeiten", [mosaik.core.Module], {
	moduleName: "StandortBearbeiten",
	moduleVersion: "1",
	
	service: null,
	
	_options: null,
	formPrefix: "Standort",
	_nextButtonHandle: null,
	_prevButtonHandle: null,
	_createButtonHandle: null,
	
	constructor: function () {
	},

	run: function( options ) {
		this.widgets.dnav.update(options, "standortId", "standortBearbeiten");
		
		this.chooserFrame = this.widgets.chooserFrame;

		console.debug("BenutzerBearbeiten::run >>>");

		this.service =  this.sandbox.getRpcService("database/standort");
		this._options = options;

		this.initForm();
		
		if ( typeof (options.create) ==="undefined" ) {
			this.fetchData();
		} else {
			this.onCreate();
		}
		
		console.debug ("<<< BenutzerBearbeiten::run");
        this.flexTable.hide();
	},
	
	initForm: function () {
		console.log("BenutzerBearbeiten::initForm");
		this.initDropdowns();
		
		// get buttons
		this.nextButton = dijit.byId("nextButton");
		this.prevButton = dijit.byId("prevButton");
		this.createButton = dijit.byId("createButton");

		var saveBtn = dijit.byId("speichernBtn");
		dojo.connect ( saveBtn, "onClick", this, "onSave");
	},
	
	onCreate: function () {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;
		
		this.createButton = dijit.byId("createButton");
		
		this.chooserFrame.show();
		
		// change button visibility
		this.createButton.domNode.style.display="block";
		this.prevButton.domNode.style.display="none";
		this.nextButton.domNode.style.display="none";
		
		
		this.createButton = dojo.connect(this.createButton , "onClick", this, "createDone");
	},
	
	createDone: function () {
		sandbox.showLoadingScreen("Erstelle Benutzer...");
		
		var name = this.widgets.newName.get("value");
		var strasse = this.widgets.newStrasse.get("value");
		var nummer = this.widgets.newNummer.get("value");
		var ort = this.widgets.newOrt.get("value");
		var plz =this.widgets.newPLZ.get("value");
		var art =this.widgets.newArt.get("value");
		var kuerzel = this.widgets.newKuerzel.get("value");
		
		this.service.create( name, strasse, nummer,plz,ort,art,kuerzel).addCallback( dojo.hitch( this, "updateData" ))
		.addErrback(function (data) {
			console.log ("Seminar-Save Error: " + data);
			sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});
	},
	
	updateData: function (data) {
		this.chooserFrame.hide();
		sandbox.hideLoadingScreen();
		console.log("SaveDone");
		console.dir(data);
		this.setValue(data);
	},

	onSave: function () {
		console.log("Save");
		sandbox.showLoadingScreen("Daten speichern...");

		this.service.save( this._currentData.id, this._changedData ).addCallback( dojo.hitch ( this, function (data) {
			console.log("SaveDone");
			console.dir(data);
			this.setValue(data);
			sandbox.hideLoadingScreen();

		})).addErrback(function (data) {
			console.log ("Benutzer Error: " + data);
				sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});
	},

	initDropdowns: function() {
		
	},

	fetchData: function () {
		sandbox.showLoadingScreen("Lade Standort: " + this._options.standortId );
		this.service.find(this._options.standortId).addCallback ( dojo.hitch ( this, "updateData"))
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> BenutzerBearbeiten::run Error: "+data);
		}));
	}
});