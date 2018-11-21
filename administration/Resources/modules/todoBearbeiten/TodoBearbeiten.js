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

dojo.provide("module.todoBearbeiten.TodoBearbeiten");
var d=dojo;
var _$=dojo.byId;

dojo.declare("module.todoBearbeiten.TodoBearbeiten", [mosaik.core.Module], {
	moduleName: "TodoBearbeiten",
	moduleVersion: "1",
	
	service: null,
	
	_options: null,
	formPrefix: "Todo",
	_nextButtonHandle: null,
	_prevButtonHandle: null,
	_createButtonHandle: null,
	
	dropdowns: {
		
	},

	constructor: function () {
		this.inherited(arguments);
	},

	run: function( options ) {
		this.widgets.dnav.update(options, "todoId", "todoBearbeiten");

		console.debug("TodoBearbeiten::run >>>");

		this.service =  this.sandbox.getRpcService("database/todo");
		this._options = options;

		this.initForm();
		
		if ( typeof (options.create) ==="undefined" ) {
			this.fetchData();
		} else {
			this.onCreate();
		}
		this.flexTable.hide();
		console.debug ("<<< TodoBearbeiten::run");
	},
	
	initForm: function () {
		// container
		this.chooserFrame = dijit.byId("chooserFrame");
		this.chooserStack = dijit.byId("chooserStack");	
		
		this.initDropdowns();
		
		// get buttons
		this.nextButton = dijit.byId("nextButton");
		this.prevButton = dijit.byId("prevButton");
		this.createButton = dijit.byId("createButton");

		var saveBtn = dijit.byId("speichernBtn");
		dojo.connect ( saveBtn, "onClick", this, "onSave");

		dijit.byId("Todo:notiz").editNode.addEventListener("paste", function(e) {
			console.log("paste:");
			e.preventDefault();
		
			if (e.clipboardData) {
				content = (e.originalEvent || e).clipboardData.getData('text/plain');
		
				dijit.byId("Todo:notiz").execCommand('insertText', content);
			}
			else if (window.clipboardData) {
				content = window.clipboardData.getData('Text');
		
				document.selection.createRange().pasteHTML(content);
			}   
			console.dir(content);
		});
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
		sandbox.showLoadingScreen("Erstelle Termin...");
		
		var betreff = dijit.byId("newTitel").get("value");
		var kategorie = this.dropdowns.newKategorie.get("value");
		var rubrik = this.dropdowns.newRubrik.get("value");
		var user = this.dropdowns.newUser.get("value");
		
		this.service.create( betreff, kategorie, rubrik, user).addCallback( dojo.hitch( this, "updateData" ))
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
			console.log ("Todo Error: " + data);
				sandbox.hideLoadingScreen();
			alert("Fehler beim Speichern: \n " + data);
		});
	},

	initDropdowns: function() {
		this.dropdowns.kategorie = dijit.byId("Todo:kategorie_id");
		this.dropdowns.status = dijit.byId("Todo:status_id");
		this.dropdowns.rubrik = dijit.byId("Todo:rubrik_id");
		this.dropdowns.user = dijit.byId("Todo:zugeordnet_id");
		this.dropdowns.newKategorie = dijit.byId("newKategorie");
		this.dropdowns.newRubrik = dijit.byId("newRubrik");
		this.dropdowns.newUser = dijit.byId("newUser");

		this.dropdowns.kategorie.set("options", sandbox.getSelectArray("XTodoKategorie"));
		this.dropdowns.newKategorie.set("options", sandbox.getSelectArray("XTodoKategorie"));
		this.dropdowns.newKategorie.set("value", -1);

		this.dropdowns.status.set("options", sandbox.getSelectArray("XTodoStatus"));
		this.dropdowns.rubrik.set("options", sandbox.getSelectArray("XTodoRubrik"));
		
		this.dropdowns.newRubrik.set("options", sandbox.getSelectArray("XTodoRubrik"));
		this.dropdowns.newRubrik.set("value", -1);
		
		this.dropdowns.user.set("options", sandbox.getSelectArray("XUser"));
		this.dropdowns.newUser.set("options", sandbox.getSelectArray("XUser"));
		this.dropdowns.newUser.set("value", -1);
	},

	fetchData: function () {
		sandbox.showLoadingScreen("Lade Aufgabe...");
		this.service.find(this._options.todoId).addCallback ( dojo.hitch ( this, "updateData"))
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> TodoBearbeiten::run Error: "+data);
		}));
	}
});
