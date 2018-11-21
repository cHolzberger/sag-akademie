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
dojo.require("mosaik.ui.FileUpload");

dojo.provide("module.aktuellesBearbeiten.AktuellesBearbeiten");


dojo.declare("module.aktuellesBearbeiten.AktuellesBearbeiten", [mosaik.core.Module], {
	moduleName: "AktuellesBearbeiten",
	service: null,
	
	_options: null,
	formPrefix: "Aktuelles",
	_nextButtonHandle: null,
	_prevButtonHandle: null,
	_createButtonHandle: null,

	run: function( options ) {
		this.widgets.dnav.update(options, "aktuellesId", "aktuellesBearbeiten");
		console.debug("AktuellesBearbeiten::run >>>");

		this.service =  this.sandbox.getRpcService("database/aktuelles");
		this._options = options;
		this.initForm();
		
		if ( typeof (options.create) ==="undefined" ) {
			this.fetchData();
		} else {
			this.onCreate();
		}
		this.flexTable.hide();

		console.debug ("<<< AktuellesBearbeiten::run");
	},
	
	initForm: function () {
		dojo.connect(this.widgets.saveBtn, "onClick", this, "onSave");
		dojo.connect(this, "onValuesSet", this, "valuesSet");
	},
	
	onCreate: function () {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;
		
		this.widgets.chooserFrame.show();
		
		// change button visibility
		this.widgets.createButton.domNode.style.display="block";
		this.widgets.prevButton.domNode.style.display="none";
		this.widgets.nextButton.domNode.style.display="none";
		
		this.widgets.createButton = dojo.connect(this.widgets.createButton , "onClick", this, "createDone");
	},
	
	createDone: function () {
		sandbox.showLoadingScreen("Erstelle Termin...");
		
		var title =this.widgets.newTitle.get("value");
		var text = "";
		var datum = mysqlDateFromDate( this.widgets.newDatum.get("value") );
		
		
		this.service.create( title, text, datum ).addCallback( dojo.hitch( this, "updateData" ))
		.addErrback(function (data) {
			console.log ("Aktuelles-Save Error: " + data);
			sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});
	},
	
	updateData: function (data) {
		this.widgets.chooserFrame.hide();
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
			console.log ("Todo Error: " + data);
				sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});
	},

	fetchData: function () {
		sandbox.showLoadingScreen("Lade Aufgabe...");
		this.service.find( this._options.id ).addCallback ( dojo.hitch ( this, "updateData"))
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> TodoBearbeiten::run Error: "+data);
		}));
	},
	
	valuesSet: function () {
		var _u = this.widgets["Aktuelles:pdf"];
		_u.set("fileId", this._currentData.id);
		_u.set("uploadTitle", "PDF aktualisieren");
		_u.set("uploadType", "PDF");
		_u.set("uploadFilter", "*.pdf;");
	},
});
