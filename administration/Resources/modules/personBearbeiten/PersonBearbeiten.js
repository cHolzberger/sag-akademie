/** require dependencies **/
dojo.require("dojo.number");

dojo.require("mosaik.core.Module");
dojo.require("mosaik.db.DataDriven");

dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.form.Textarea');
dojo.require('dijit.form.DateTextBox');
dojo.require('dijit.form.CurrencyTextBox');
dojo.require("dojox.widget.Standby");
dojo.require("dijit.form.CheckBox");

dojo.require('dijit.Editor');
dojo.require("dijit._editor.plugins.AlwaysShowToolbar");

dojo.require('mosaik.ui.FlexTable');
dojo.require("mosaik.util.Mailto");

dojo.require("module.dialogs.KontaktChooser");
dojo.require("mosaik.ui.DatasetNavigator");
dojo.require("mosaik.ui.WeitereInformationen");

dojo.provide("module.personBearbeiten.PersonBearbeiten");
var d=dojo;
var _$=dojo.byId;

dojo.declare("module.personBearbeiten.PersonBearbeiten", [mosaik.core.Module], {
	moduleName: "SeminarBearbeiten",
	moduleVersion: "1",
	service: null,
	_options: null,
	formPrefix: "Person",
	_currentData: null,
	container: null, // container node -> border container
	
	constructor: function () {
	
	},

	run: function( options ) {
		console.debug("PersonBearbeiten::run >>>");
		this._options = options;

		this.linkButtons();
		this.service =  this.sandbox.getRpcService("database/person");
		this.kontaktService =  this.sandbox.getRpcService("database/kontakt");


		this.container = dijit.byId('borderContainer');

		// set values
		dijit.byId("Person:geschlecht").set("options", sandbox.getSelectArray("XAnrede"));
		dijit.byId("Person:bundesland_id").set("options", sandbox.getSelectArray("XBundesland"));
		dijit.byId("Person:land_id").set("options", sandbox.getSelectArray("Xland"));

		if ( typeof ( options.create ) !== "undefined") {
			this.onCreate();
		} else {
			this.fetchData();
		}
		
		// relayout the container
		this.container.layout();
		console.debug ("<<< PersonBearbeiten::run");

		dojo.connect(this, "onValuesSet", this, "updateFlexTable");
		dojo.connect(this, "onValuesSet", this, "onValuesUpdate");

		this.widgets.dnav.update( options, "personId", "personBearbeiten");
        this.flexTable.show();
        this.initFlexTable();
	},

	onCreate: function () {
		// hide flexTable
		this.flexTable.hide();
		

		// get the dialog
		this.createDialog = dijit.byId("chooserFrame");
		this.createDialog.show();

		// get navbar buttons
		this.nextButton = dijit.byId("nextButton");
		this.prevButton = dijit.byId("prevButton");
		this.createButton = dijit.byId("createButton");

		// get frames and stack
		this.chooserFrame = dijit.byId("chooserFrame");
		this.chooserFrame.show();
		this.chooserStack = dijit.byId("chooserStack");

		// init input fields in dialog
		dijit.byId("newPersonAnrede").set("options", sandbox.getSelectArray("XAnrede"));
		//alert(this._options.kontaktId);
		if ( typeof(this._options.kontaktId ) === "undefined") {
			this.createKontakt();
		} else {
			this.createPersonDetail();
		}
	},
	
	onValuesUpdate: function () {
		// called when the current data is changed
		console.log("==> Values updated");
		this.widgets.weitereInformationen.setInformation(this._currentData);
	},

	createKontakt: function () {
		// unlink events
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;
		// set title and active pane
		this.chooserFrame.set("title","Firma ausw&auml;hlen");
		this.chooserStack.selectChild( dijit.byId("kontaktPane"));

		// set button visibility
		this.nextButton.domNode.style.display="none";
		this.prevButton.domNode.style.display="none";
		this.createButton.domNode.style.display="none";

		// relink buttons
		this._nextButtonHandle= dojo.connect( this.nextButton, "onClick", this, "createPersonDetail");
		//this._prevButtonHandle= dojo.connect( this.prevButton, "onClick", this, "createHinweisForm");
		//this._createButtonHandle= dojo.connect( this.createButton, "onClick", this, function () { this.createDone(false)});

		// only show next on select
		dojo.connect( dijit.byId("kontaktChooser"), "onResultClick", this, function () {

			this.nextButton.domNode.style.display="block";
		});

	},

	createPersonDetail: function () {
		// unlink events
		(this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
		(this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
		(this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;
		// set title and active pane
		this.chooserFrame.set("title","Person anlegen");
		this.chooserStack.selectChild( dijit.byId("personDetailPane"));

		// set button visibility
		this.nextButton.domNode.style.display="none";
		this.prevButton.domNode.style.display="block";
		this.createButton.domNode.style.display="block";
		if ( typeof ( this._options.kontaktId) === "undefined") {
			this._options.kontaktId = dijit.byId("kontaktChooser").selectedItem.id;
		}
		
		this.updateKontaktInfo(this._options.kontaktId);
		// relink buttons
		//this._nextButtonHandle= dojo.connect( this.nextButton, "onClick", this, "createPersonDetail");
		this._prevButtonHandle= dojo.connect( this.prevButton, "onClick", this, "createKontakt");
		this._createButtonHandle= dojo.connect( this.createButton, "onClick", this, "createDone");
	},

	createDone: function() {
		console.log("done");
		this.chooserFrame.hide();
		var kontaktId = this._options.kontaktId;
		

		var options = {
			kontaktId: kontaktId,
			name: dijit.byId("newPersonName").get("value"),
			vorname: dijit.byId("newPersonVorname").get("value"),
			email: dijit.byId("newPersonEMail").get("value"),
			anrede: dijit.byId("newPersonAnrede").get("value")
		};

		this.service.create(options.kontaktId, options.name, options.vorname, options.anrede, options.email).addCallback ( dojo.hitch ( this, function (data) {
			this._currentData = data;
			this.setValue ( data );
		})).addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> PersonBearbeiten::create Error: "+data);
		}));
	},

	fetchData: function () {
		console.log("fetchData ...");
		this.service.find(this._options.personId).addCallback ( dojo.hitch ( this, function (data) {
			this._currentData = data;
			this.setValue ( data );
		
		})).addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> PersonBearbeiten::fetchData Error: "+data);
		}));
	},
	
	updateKontaktInfo: function (id) {
		this.kontaktService.find(id)
		.addCallback( function(data) {
			
			dojo.byId("Person:firma").innerHTML= data.firma;
			dojo.byId("kontaktName").innerHTML= data.firma;
			
		}).addErrback(function ( err ) {
			console.log("==!!> PersonBearbeiten::updateKontaktInfo Error!");
			console.log(err);
			alert(err);
		})
	},

	linkButtons:function () {
		console.log("linkButtons");
		dojo.connect(dojo.byId("Person:firma"), "onclick", this, function( ) {
			sandbox.loadShellModule("kontaktBearbeiten", {kontaktId: this._currentData.kontakt_id});
		});

		var saveBtn = dijit.byId("speichernBtn");
		dojo.connect ( saveBtn, "onClick", this, "onSave");
	},

	onSave: function () {
		console.log("Save");
		this.service.save( this._currentData.id, this._changedData )
		.addCallback( dojo.hitch ( this, function (data) {
			console.log("SaveDone");
			console.dir(data);
			this.setValue(data);
			
			
		})).addErrback(function (data) {
			console.log ("Seminar-Save Error: " + data);
		});
	},


    updateFlexTable: function () {
        this.flexTable.resetParameters();
        var serviceURL = this.sandbox.getServiceUrl ( "person/buchungen" );
        this.flexTable.queryService(serviceURL , {personId: this._currentData.id});
    },

	initFlexTable: function () {
        if (this._ftInitDone) return;
        this._ftInitDone=true;
        this.flexTable.setTitle("Buchungen:");

        this.flexTable.clearContextMenu();
        this.flexTable.addContextMenuItem("Buchung aufrufen","flextable/editBuchung");
        this.subscribeTo("flextable/editBuchung", "ftEditBuchung");
		// flex table service url setzen

	},

	ftEditBuchung: function (data) {
		sandbox.loadShellModule("buchungBearbeiten", {buchungId: data.id});
	},
	
	doPrint: function() {
		var token = sandbox.getUserinfo().auth_token;
		
		var pdfurl = this.sandbox.getServiceUrl ( "print/person" ) + this._currentData.id + "?token=" + token;
		
		app.openPdf(pdfurl);
	}
});
