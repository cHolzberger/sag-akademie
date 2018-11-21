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

dojo.require("mosaik.ui.DatasetNavigator");

dojo.provide("module.akquiseKontaktBearbeiten.AkquiseKontaktBearbeiten");
var d=dojo;
var _$=dojo.byId;

dojo.declare("module.akquiseKontaktBearbeiten.AkquiseKontaktBearbeiten", [mosaik.core.Module], {
	moduleName: "AkquiseKontaktBearbeiten",
	formPrefix: "AkquiseKontakt",
	dropdowns: null,
	service: null,
	_options: null,

	constructor: function () {
		this.dropdowns = {};
	},

	run: function (options) {
		this._options = options;
		this.service =  this.sandbox.getRpcService("database/akquiseKontakt");
		this.dropdowns = {};
		this.initDropdowns();
		this.linkButtons();

		if ( typeof ( options.create ) !== "undefined") {
			this.onCreate();
		} else {
			this.fetchData();
		}

		this.widgets.dnav.update( options, "akquiseKontaktId", "akquiseKontaktBearbeiten");
	},

	initDropdowns: function () {
		// dom elemente holen
		this.dropdowns.kategorie = dijit.byId ('AkquiseKontakt:kontaktkategorie');
		this.dropdowns.taetigkeitsbereich = dijit.byId ('AkquiseKontakt:taetigkeitsbereich_id');
		this.dropdowns.branche = dijit.byId("AkquiseKontakt:branche_id");
		this.dropdowns.bundesland = dijit.byId("AkquiseKontakt:bundesland_id");

		// dropdowns neue listen zuweisen
		this.dropdowns.kategorie.set("options", sandbox.getSelectArray("KontaktKategorie"));
		//this.dropdowns.kategorie.set("value", -1);
		this.dropdowns.taetigkeitsbereich.set ( "options",  sandbox.getSelectArray("XTaetigkeitsbereich"));
		//this.dropdowns.taetigkeitsbereich.set ( "value", -1);

		this.dropdowns.branche.set("options", sandbox.getSelectArray("XBranche"));
		//this.dropdowns.branche.set("value", -1);

		this.dropdowns.bundesland.set("options", sandbox.getSelectArray("XBundesland"));
		//this.dropdowns.bundesland.set("value", -1);
	},

	onCreate: function() {
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;

		this.chooserFrame = dijit.byId("chooserFrame");
		this.chooserStack = dijit.byId("chooserStack");

		this.chooserFrame.show();


		//this.nextButton = dijit.byId("nextButton");
		//this.prevButton = dijit.byId("prevButton");
		this.createButton = dijit.byId("createButton");

		this._createButtonHandle = dojo.connect( this.createButton, "onClick", this, "createDone");

		this.createKontaktDetail();
	},

	createKontaktDetail: function () {
		this.chooserFrame.set("title","Firma anlegen");
		this.chooserStack.selectChild( dijit.byId("kontaktDetailPane"));

		//this.nextButton.domNode.style.display="none";
		//this.prevButton.domNode.style.display="none";
		this.createButton.domNode.style.display="block";
	},


	createDone: function () {
		var kontaktName = dijit.byId("newKontaktName").get("value");

		this.service.create( kontaktName ).addCallback ( dojo.hitch ( this, function (data) {
			this._ignoreUpdate = true;
			console.dir(data);
			this.setValue ( data );

			this.initStaticFields();
			this.chooserFrame.hide();
			this.flexTable.show();

		})).addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> KontaktBearbeiten::run Error: "+data);
		}));
	},


	fetchData: function () {
		this.service.find(this._options.akquiseKontaktId).addCallback ( dojo.hitch ( this, function (data) {
			this._ignoreUpdate = true;
			this._currentData = data;
			console.dir(data);
			this.setValue ( data );

			this.initStaticFields();
		})).addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> KontaktBearbeiten::run Error: "+data);
		}));
	},

	onSave: function () {
		console.log("Save");
		this.service.save( this._currentData.id, this._changedData ).addCallback( dojo.hitch ( this, function (data) {

			console.log("SaveDone");
			console.dir(data);
			this.setValue(data);
		})).addErrback(function (data) {
			console.log ("Seminar-Save Error: " + data);
		});
	},

	initStaticFields: function () {
		
	},

	linkButtons: function () {
		var saveBtn = dijit.byId("speichernBtn");
		dojo.connect ( saveBtn, "onClick", this, "onSave");
	}
});
