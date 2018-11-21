/** require dependencies **/
dojo.require("mosaik.core.Module");
dojo.require("mosaik.db.DataDriven");

dojo.require("mosaik.ui.FlexTable");

dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.form.CheckBox');
dojo.require('dijit.form.Textarea');
dojo.require('dijit.form.DateTextBox');
dojo.require("mosaik.core.helpers");

dojo.require("module.termineSeminare.SeminarRubrik");
dojo.provide("module.termineSeminare.TermineSeminare");

dojo.declare("module.termineSeminare.TermineSeminare", [mosaik.core.Module], {

	container : null,
	service : null,
	moduleName : "TermineSeminare",
	moduleVersion : "3",
	models : null,
	modus : "Termin",
	rubricContainer : null,

	run : function(options) {
        this._ftInitDone=false;
		this._options = options ? options : {};
		dojo.subscribe("termineSeminare/rubrikUpdate", dojo.hitch(this, this.updateRubric));
		// nodes holen
		this.container = dijit.byId('borderContainer');
		this.rubricContainer = dojo.byId("seminarRubriken");
		this.kursnrSearchButton = dijit.byId("terminKursnrSucheBtn");
		this.datumSearchButton = dijit.byId("terminDatumSucheBtn");

		this.updateRubric();

		dojo.connect(this.kursnrSearchButton, "onClick", this, this.searchKursnr);
		dojo.connect(this.datumSearchButton, "onClick", this, this.searchDatum);
		dojo.connect(this.flexTable, "onReady", this, this.initFlexTable);



		connectOnEnterKey("terminSucheKursnr", this, "searchKursnr");
		this.subscribeTo("flextable/doubleClick", "ftOnDblKlick");
        this.flexTable.show();
        this.initFlexTable();
        this.restoreFtState();

	},

    onResume: function () {
        this.restoreFtState();
    },

    restoreFtState: function () {
        if ( this._options.table_mode && this._options.table_mode == "Termin") {
            if( this._options && this._options.seminarId ) {
                this.setSeminarArt(this._options.seminarId);
            }

            this.listTerminTable();
        } else if (this._options.table_mode && this._options.table_mode == "Year") {
            this.listYearTable( this._options.year );
        } else if (this._options.table_mode && this._options.table_mode == "Seminar") {
            this.listSeminarTable();
        } else if ( this._options.table_mode && this._options.table_mode == "Kursnr" ) {
            this.searchKursnr();
        } else if ( this._options.table_mode && this._options.table_mode == "Datum" ) {
            this.searchDatum();
        }
    },

	updateRubric : function() {
		// Summary:
		// syncs the "rubrik" table with the view
		console.log("updateRubric...");
		var rubricInfo = [];
		var data = sandbox.getArray("SeminarArtRubrik");
		var seminareStore = sandbox.getItemStore("SeminarArt");
		var _rm = null;
		var _seminare = null;

		this.rubricContainer.innerHTML = "";

		for(var i = 0; i < data.length; i++) {
			console.log("Walking: " + i);
			var seminare = seminareStore.query({
				"rubrik" : data[i].id
			});

			console.log("request completed");
			//console.dir(seminare);
			_rm = new module.termineSeminare.SeminarRubrik();
			dojo.connect(_rm, "seminarArtSelected", this, "setSeminarArt");
			// first append the node
			this.rubricContainer.appendChild(_rm.domNode);
			_rm.set("rubricName", data[i].name);
			_rm.set("seminare", seminare);
			// give space to the rubrik
			//_rm.domNode.style.position = "absolute";
			//_rm.domNode.style.left = (i * 130 ).toString() + "px";
			//this.rubricContainer.style.width = (dojo.number.parse(this.rubricContainer.style.width) + 130 ).toString() + "px";
		}
	},

	setSeminarArt : function(name) {
        this._options.seminarId = name;
		this._currentSeminarArt = name;
		this.nodes.seminarArt.innerHTML = name;
		dojo.style(this.widgets.seminarArtBearbeiten.domNode, "display", "inline-block");
		this._isYear = false;
		this.modus="Termin";
		this.updateFlexTable();
	},
	editSeminarArt : function() {

		sandbox.loadShellModule("seminarBearbeiten", {
			seminarId : this._currentSeminarArt,
			results: {}
		});
	},
	/**
	 * Flex table boilerplate
	 * would be better if included with the table itself
	 */
	_ftEditRef : null,
	initFlexTable : function() {
		// Summary:
		// connects buttons and flex table events
		if(this._ftInitDone)
			return;

		this._ftInitDone = true;
        this.connectTo(this.flexTable, "onResume", "updateFlexTable");
        this.subscribeTo("flextable/optionChanged", "ftOptionsChanged");
		this.subscribeTo("flextable/editSeminar", "ftEditSeminar");
		this.subscribeTo("flextable/editTermin", "ftEditTermin");

		this.updateFlexTable();
	},

	loadFullYear : function(year, isYear) {
		// Summary:
		this._loadYear = year;
		this._isYear = true;
		this._currentYear = year;
		this.updateFlexTable();
	},
	updateFlexTable : function() {
		// Summary:
		// sets flex table options
		// and relayouts if necessary

		if(this._isYear === true) {
			console.log("====> UPDATE FLEX TABLE BY YEAR");
            this.flexTable.show();
			this.listYearTable(this._currentYear);
		} else if (this.modus=="Seminar") {
            this.flexTable.show();
            this.listSeminarTable();
        } else if ( this._currentSeminarArt != null ) {
            this.flexTable.show();
            this.listTerminTable();
		} else {
            this.flexTable.hide();
        }
	},

    ftOptionsChanged: function (id, value,checked) {
        console.log("FlexTable option Changed");
        sandbox.setUserVar(id, checked);
        this.updateFlexTable();
    },

	ftOnDblKlick: function (data) {
		if (this._ftState == "Termin") {
			this.ftEditTermin(data);
		} else if ( this._ftState == "Seminar") {
			this.ftEditSeminar(data);
		}
	},

	ftEditSeminar : function(data) {
		var target = {
			seminarId : data.id,
			results : this.flexTable.getAllRows()
		}; // war getAllRows(["id"]);
		//	console.dir(target);
		sandbox.loadShellModule("seminarBearbeiten", target);
	},
	ftEditTermin : function(data) {
		console.log("Edit Termin: " + data.id);
		sandbox.loadShellModule("terminBearbeiten", {
			terminId : data.id,
			results : this.flexTable.getAllRows()
		});
	},

	listSeminarTable : function() {
        this._ftState="Seminar";

		this._currentSeminarArt = null;
		this.modus = "Seminar";
		this._ftOptions = {};

		var serviceURL = this.sandbox.getServiceUrl("seminar/seminare");
        if (  this._options.table_mode != "Seminar") {
            this._options.table_mode="Seminar";

            this.flexTable.setTitle("Alle Seminare:");

            this.flexTable.clearOptions();
            this.flexTable.addOption("Entwurf anzeigen","entwurfAnzeigen",1,sandbox.getUserVar("entwurfAnzeigen",false));
            this.flexTable.addOption("Inaktiv anzeigen","inaktivAnzeigen",1,sandbox.getUserVar("inaktivAnzeigen",false));
						this.flexTable.addOption("Intern anzeigen","internAnzeigen",1,sandbox.getUserVar("internAnzeigen",false));
						this.flexTable.addOption("Orga anzeigen","orgaAnzeigen",1,sandbox.getUserVar("orgaAnzeigen",false));

            this.flexTable.clearContextMenu();
            this.flexTable.addContextMenuItem("Seminar bearbeiten", "flextable/editSeminar");
        }

        this.flexTable.show();
        this.flexTable.setTitle("Alle Seminare");
        this.flexTable.queryService(serviceURL, this.flexTable.getOptions());
	},

	listTerminTable : function() {
        this._ftState="Termin";
		this.modus = "Termin";
		var serviceURL = this.sandbox.getServiceUrl("seminar/termine");

        if (  this._options.table_mode!="Termin") {
            this._options.table_mode="Termin";

            this.flexTable.clearContextMenu();
            this.flexTable.addContextMenuItem("Termin bearbeiten", "flextable/editTermin");

            this.flexTable.clearOptions();
            this.flexTable.addOption("Planung anzeigen","planungAnzeigen",1,sandbox.getUserVar("planungAnzeigen",false));
            this.flexTable.addOption("Darmstadt","termineDarmstadt",1,sandbox.getUserVar("termineDarmstadt",false));
            this.flexTable.addOption("Lünen","termineLuenen",1,sandbox.getUserVar("termineLuenen",false));

            this.flexTable.addOption("nächste 14 Tage","naechste14Tage",1,sandbox.getUserVar("naechste14Tage",false));
            this.flexTable.addOption("nächste 30 Tage","naechste30Tage",1,sandbox.getUserVar("naechste30Tage",false));
            this.flexTable.addOption("nächste 90 Tage","naechste90Tage",1,sandbox.getUserVar("naechste90Tage",false));
        }
        this.flexTable.setTitle("Termine für Seminar <b>" + this._currentSeminarArt + "</b>:");

        this._ftOptions = {
            version : "2",
            lastLogin : "0000-00-00",
            seminarArt : this._currentSeminarArt
        };
        dojo.mixin(this._ftOptions, this.flexTable.getOptions());

		this.flexTable.queryService(serviceURL, this._ftOptions);
	},

	listYearTable : function(year) {
        this._ftState="Termin";

        this._options.year = year;
        this.flexTable.setTitle("Seminare in " + year + ":");

        if (this._options.table_mode != "Year" ) {
            this._options.table_mode="Year";
            this.flexTable.clearOptions();
            this.flexTable.clearContextMenu();
            this.flexTable.addContextMenuItem("Termin bearbeiten", "flextable/editTermin");
        }

		// flex table service url setzen
		var serviceURL = this.sandbox.getServiceUrl("seminar/termine");

		this._ftOptions = {
			version : "2",
			lastLogin : "0000-00-00",
			year : year
		};

        this.flexTable.queryService(serviceURL, this._ftOptions);
	},
    searchDatum : function() {

        this._ftState="Year";
        this._isYear = false;
        var serviceURL = this.sandbox.getServiceUrl("seminar/termine");

        var start = dijit.byId("terminSucheVon").get("value");
        var end = dijit.byId("terminSucheBis").get("value");
        //was macht das hier:
       // this.unsetFlexTableOptions();

        var fStart = start.getDate() + "." + (start.getMonth()+1) + "." + start.getFullYear();
        var fEnd = end.getDate() + "." + (end.getMonth()+1) + "." + end.getFullYear();

        this._ftOptions = {
            v : fStart,
            b : fEnd
        };

        if ( this._options.table_mode!="Datum") {
            this._options.table_mode="Datum";
            this.flexTable.clearContextMenu();
            this.flexTable.addContextMenuItem("Termin bearbeiten", "flextable/editTermin");

            this.flexTable.clearOptions();
            //fixme: termin options
        }
        this.flexTable.show();

        this.flexTable.queryService(serviceURL, this._ftOptions);
    },

    searchKursnr : function() {
        this._ftState="Termin";
        this._isYear = false;
        var searchText = dijit.byId("terminSucheKursnr").get("value");
        var serviceURL = this.sandbox.getServiceUrl("seminar/termine");

        this._ftOptions = {
            q : searchText
        };

        if ( this._options.table_mode="Kursnr" ) {
            this._options.table_mode="Kursnr";
            this.flexTable.clearContextMenu();
            this.flexTable.addContextMenuItem("Termin bearbeiten", "flextable/editTermin");
            //fixme: termin options
        }
        this.flexTable.show();
        this.flexTable.queryService(serviceURL, this._ftOptions);
    }
});
