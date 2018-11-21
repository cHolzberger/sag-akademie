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

dojo.provide("module.stellenangebotBearbeiten.StellenangebotBearbeiten");


dojo.declare("module.stellenangebotBearbeiten.StellenangebotBearbeiten", [mosaik.core.Module], {
	moduleName: "StellenangebotBearbeiten",
	service: null,
	
	_options: null,
	formPrefix: "Stellenangebot",
	_nextButtonHandle: null,
	_prevButtonHandle: null,
	_createButtonHandle: null,

	run: function( options ) {
		console.debug(this.moduleName + "::run >>>");

		this.service =  this.sandbox.getRpcService("database/stellenangebot");
		this._options = options;
		this.initForm();
		
		if ( typeof (options.create) ==="undefined" ) {
			this.fetchData();
		} else {
			this.onCreate();
		}
		
		console.debug ("<<< "+this.moduleName +"::run");
		dojo.connect(this,"onValuesSet", this, "valuesSet");
        this.flexTable.hide();

	},
	
	initForm: function () {
		
		this.widgets["Stellenangebot:kategorie"].set("options", sandbox.getSelectArray("StellenangebotKategorie"));
		this.widgets.newKategorie.set("options", sandbox.getSelectArray("StellenangebotKategorie"));
		
		dojo.connect(this.widgets.saveBtn, "onClick", this, "onSave");
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
		
		var name =this.widgets.newName.get("value");
		var link =this.widgets.newLink.get("value");		

		var kategorie =this.widgets.newKategorie.get("value");		
		
		this.service.create( name, link, kategorie ).addCallback( dojo.hitch( this, "setValue" ))
		.addErrback(function (data) {
			console.log ("Aktuelles-Save Error: " + data);
			sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});
	},
	
	onSave: function () {
		console.log("Save");
		sandbox.showLoadingScreen("Daten speichern...");

		this.service.save( this._currentData.id, this._changedData ).addCallback( dojo.hitch ( this, "setValue")).addErrback(function (data) {
			console.log ("Todo Error: " + data);
				sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});
	},

	fetchData: function () {
		sandbox.showLoadingScreen("Lade Aufgabe...");
		this.service.find( this._options.id ).addCallback ( dojo.hitch ( this, "setValue"))
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> StellenangebotBearbeiten::run Error: "+data);
			alert(data);
		}));
	},
	
	valuesSet: function () {
		this.widgets.chooserFrame.hide();
		sandbox.hideLoadingScreen();
		
		var _u = this.widgets["Stellenangebot:Pdf"];
		_u.set("fileId", this._currentData.id);
		_u.set("uploadTitle", "Stellenagebot-PDF aktualisieren");
		_u.set("uploadType", "PDF-Datei");
		_u.set("uploadFilter", "*.pdf;");
		_u.set("exists", this._currentData.pdf!=="");
		_u.set("field", "pdf");
	}
});
