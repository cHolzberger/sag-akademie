/** require dependencies **/
dojo.require("dojo.number");
dojo.require("module.hotelBearbeiten.PriceRow");
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

dojo.provide("module.hotelBearbeiten.HotelBearbeiten");
var d=dojo;
var _$=dojo.byId;

dojo.declare("module.hotelBearbeiten.HotelBearbeiten", [mosaik.core.Module], {
	moduleName: "HotelBearbeiten",
	moduleVersion: "1",
	
	service: null,
	
	_options: null,
	formPrefix: "Hotel",
	_nextButtonHandle: null,
	_prevButtonHandle: null,
	_createButtonHandle: null,
	
	constructor: function () {
	},

	run: function( options ) {
		this.widgets.dnav.update(options, "hotelId", "hotelBearbeiten");
		
		this.chooserFrame = this.widgets.chooserFrame;

		console.debug("BenutzerBearbeiten::run >>>");

		this.service =  this.sandbox.getRpcService("database/hotel");
		this._options = options;

		this.initForm();
		
		if ( typeof (options.create) ==="undefined" ) {
			this.fetchData();
		} else {
			this.onCreate();
		}
		
		console.debug ("<<< BenutzerBearbeiten::run");



		this._preisliste = [];
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
		
		var newName = this.widgets.newName.get("value");
		var newStrasse = this.widgets.newStrasse.get("value");
		var newNr = this.widgets.newNr.get("value");
		var newPLZ =this.widgets.newPLZ.get("value");
		var newOrt = this.widgets.newOrt.get("value");
		var newStandort = this.widgets.newStandort.get("value");
		
		this.service.create( newName, newStrasse, newNr, newPLZ, newOrt, newStandort).addCallback( dojo.hitch( this, "updateData" ))
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

	updateGrundpreis: function(data) {
		dijit.byId("ezGrundpreis").set("value", dojo.number.parse(data.zimmerpreis_ez));
		dijit.byId("dzGrundpreis").set("value", dojo.number.parse(data.zimmerpreis_dz));
		dijit.byId("mzGrundpreis").set("value", dojo.number.parse(data.zimmerpreis_mb46));
		dijit.byId("fruehstueckGrundpreis").set("value", dojo.number.parse(data.fruehstuecks_preis));
		dijit.byId("margeGrundpreis").set("value", dojo.number.parse(data.marge));
	},

	_preisliste: null,
	
	updatePreisliste: function(data) {

		dojo.forEach(data, function (row) {
			var a=new module.hotelBearbeiten.PriceRow();
			dojo.place( a.domNode, dojo.byId("preisbereich"), "last");
			a.setData(row);
			this._preisliste.push(a);
		},this);
	},

	savePreisliste: function () {
		var sv = [];
		console.log("savePreisliste");
		dojo.forEach(this._preisliste, function(row) {
			sv.push(row.getData());
			var node = row.domNode;
			row.destroy();
			dojo.destroy(node);
			
		},this);

		//console.dir(sv);
		this._preisliste = [];
		
		this.service.setPreisliste(this._options.hotelId, sv).addCallback ( dojo.hitch(this,"updatePreisliste") )
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> HotelBearbeiten::run Grundpreis Error: "+data);
		}));
	},
	
	saveGrundpreis: function () {
		var ez = dijit.byId("ezGrundpreis").get("value");
		var dz = dijit.byId("dzGrundpreis").get("value");
		var mz = dijit.byId("mzGrundpreis").get("value");
		var fruehstueck = dijit.byId("fruehstueckGrundpreis").get("value");
		var marge = dijit.byId("margeGrundpreis").get("value");
		
		this.service.setGrundpreis(this._options.hotelId, ez, dz,mz,fruehstueck, marge).addCallback ( dojo.hitch(this,"updateGrundpreis") ).addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> HotelBearbeiten::run Grundpreis Error: "+data);
		}));;
		sandbox.hideLoadingScreen();
	},

	onSave: function () {
		console.log("Save");
		sandbox.showLoadingScreen("Daten speichern...");

		this.service.save( this._currentData.id, this._changedData ).addCallback( dojo.hitch ( this, function (data) {
			console.log("SaveDone");
			console.dir(data);
			this.setValue(data);
			this.saveGrundpreis();
			this.savePreisliste();

		})).addErrback(function (data) {
			console.log ("Benutzer Error: " + data);
				sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});
	},

	initDropdowns: function() {
		this.widgets["Hotel:standort_id"].set("options", sandbox.getSelectArray("Standort"));
		this.widgets.newStandort.set("options", sandbox.getSelectArray("Standort"));

	},

	fetchData: function () {
		sandbox.showLoadingScreen("Lade Benutzer...");
		this.service.find(this._options.hotelId).addCallback ( dojo.hitch ( this, "updateData"))
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> BenutzerBearbeiten::run Error: "+data);
		}));

		this.service.getGrundpreis(this._options.hotelId).addCallback ( dojo.hitch(this,"updateGrundpreis") ).addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> HotelBearbeiten::run Grundpreis Error: "+data);
		}));;

		this.service.getPreisliste(this._options.hotelId).addCallback ( dojo.hitch(this,"updatePreisliste") ).addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> HotelBearbeiten::run updatePreise Error: "+data);
		}));;
	},

    createPreis: function () {
		var a=new module.hotelBearbeiten.PriceRow();
		dojo.place( a.domNode, dojo.byId("preisbereich"), "last");
		a.setData({id: 0, zimmerpreis_dz: 0, zimmerpreis_ez:0, zimmerpreis_mb46: 0, fruehstuecks_preis: 0, marge: 0,datum_start: "0000-00-00", datum_ende: "0000-00-00"});
		this._preisliste.push(a);
	}

});
