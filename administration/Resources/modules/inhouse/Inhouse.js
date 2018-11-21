/***
 * Inhouse Übersicht
 * benötigt das Modul termineSeminare zur erstellung der SeminarBoxen
 */

dojo.provide("module.inhouse.Inhouse");
/** require dependencies **/
dojo.require("mosaik.core.Module");

dojo.require("dojo.number");

dojo.require("mosaik.core.Module");
dojo.require("mosaik.db.DataDriven");

dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.CheckBox');

dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.form.Textarea');
dojo.require('dijit.form.DateTextBox');
dojo.require('dijit.form.CurrencyTextBox');
dojo.require("dijit.Dialog");
dojo.require("dojox.widget.Standby");

dojo.require('dijit.Editor');
dojo.require("dijit._editor.plugins.AlwaysShowToolbar");
dojo.require("mosaik.ui.DatasetNavigator");
dojo.require("mosaik.ui.FlexTable");

dojo.declare("module.inhouse.Inhouse", [mosaik.core.Module], {
	_editTerminHandle: null,

	constructor: function () {
		
	},

	run: function( options ) {
		console.log("running....");
		this._options = options;

		this.linkButtons();
		this.updateRubric();
		
		dojo.subscribe("sync/done", this, "updateRubric");
		
		if ( typeof(options) !== "undefined" && typeof ( options.seminarId ) !== "undefined" ) {
			this.setSeminarArt(options.seminarId);
		}
		
		connectOnEnterKey("terminSucheKunde", this, "onSearchKunde");

        this.initFlexTable();
	},

	_rubricHandlers: null,


    updateRubric: function() {
		// Summary:
		// syncs the "rubrik" table with the view
		
		console.log("Inhouse::updateRubric");
		
		var rubricInfo = [];
		var target = dojo.byId("inhouseSeminare");
		var seminareStore = sandbox.getSelectArray("InhouseSeminarArt");
		
		var _rm = null;
		var _seminare = null;

		target.innerHTML="";

		dojo.forEach ( this._rubricHandlers, dojo.disconnect);

		this._rubricHandlers = [];
		
		dojo.forEach ( seminareStore, dojo.hitch( this, function ( item ) {
			var div = dojo.create("div", {
				style: "float: left; width: 100px;",
				"class": "seminarBtn"
			}, target);
			var link = dojo.create("a", {
				innerHTML: item.label,
				href: "#"
			}, div);
			
			this._rubricHandlers.push( dojo.connect(link, "onclick", this, function ( e ) {
				
				this.setSeminarArt( e.currentTarget.innerHTML);
			}));
		}));
	},
	
	setSeminarArt: function ( name ) {
		this._currentSeminarArt = name;
		this.nodes.seminarArt.innerHTML = name;
		dojo.style (this.widgets.seminarArtBearbeiten.domNode, "display", "inline-block");
		this.updateFlexTable();
	},
    ftOptionsChanged: function (id, value,checked) {
        sandbox.setUserVar(id, checked);
        this.updateFlexTable();
    },
	updateFlexTable: function ( ) {
		// Summary:
		// sets flex table options
		// and relayouts if necessary
		console.log(this._currentSeminarArt);
		if ( this._currentSeminarArt == null ) return;

		console.log("====> UPDATE FLEX TABLE");
		// flex table service url setzen
		var serviceURL = this.sandbox.getServiceUrl ( "seminar/inhouseTermine" );

		this._ftOptions = {
			version: "2",
			lastLogin: "0000-00-00",
			seminarArt: this._currentSeminarArt
		};

		// find buttons that are options to the flex table
		// and link their id and value to flex table parameter
		dojo.mixin(this._ftOptions, this.flexTable.getOptions());

		this.flexTable.queryService(serviceURL,this._ftOptions );
        this.flexTable.show();
	},

    initFlexTable: function () {
        if ( this._ftInitDone) return;
        this._ftInitDone=true;

        this.subscribeTo("flextable/editTermin", "editTermin");
        this.subscribeTo("flextable/optionChanged", "ftOptionsChanged");
        this.connectTo(this.flexTable, "onResume", "updateFlexTable");

        this.flexTable.clearContextMenu();
        this.flexTable.addContextMenuItem("Termin bearbeiten", "flextable/editTermin");
        this.flexTable.clearOptions();
        this.flexTable.addOption("Planung anzeigen", "planungAnzeigen", 1, sandbox.getUserVar("planungAnzeigen",false));


        this.flexTable.hide();
    },
	
	editTermin: function (data) {
		sandbox.loadShellModule("inhouseTerminBearbeiten", {terminId: data.id,
				results: this.flexTable.getAllRows()
		});
	},
	
	editSeminarArt: function (  ) {
		sandbox.loadShellModule("inhouseSeminarBearbeiten", {seminarId: this._currentSeminarArt,
            results: this.flexTable.getAllRows()
		});
	},
	
	ftOnDblKlick: function (data) {
		if (this._ftState == "Termin") {
			this.editTermin(data);
		} else if ( this._ftState == "Seminar") {
			this.editSeminar(data);
		}
	},
	
	
	linkButtons: function () { 
		this.kundeSuchenButton = dijit.byId("terminKundeSucheBtn");
		this.datumSucheButton  = dijit.byId("terminDatumSucheBtn");
		
		dojo.connect(this.kundeSuchenButton, "onClick", this, "onSearchKunde");
		dojo.connect(this.datumSucheButton, "onClick", this, "onSearchDatum");
	},
	
	onSearchKunde: function() {
		var serviceURL = this.sandbox.getServiceUrl ( "seminar/inhouseTermine" );
		var _txt = dijit.byId("terminSucheKunde").get("value");
		
		this.flexTable.queryService(serviceURL, {v: 2, q: _txt} );
		this.flexTableContainer.layout();
	},
	
	onSearchDatum: function () {
		var serviceURL = this.sandbox.getServiceUrl ( "seminar/inhouseTermine" );
		var start = dijit.byId("terminSucheVon").get("value");
		var end = dijit.byId("terminSucheBis").get("value");
		
		var _von = start.getDate() + "." + start.getMonth()  +  "." + start.getFullYear();
		var _bis = end.getDate() + "." + end.getMonth()  +  "." + end.getFullYear();
		
		this.flexTable.queryService(serviceURL, {v: _von, b: _bis} );
		this.flexTableContainer.layout();
	}
	
});