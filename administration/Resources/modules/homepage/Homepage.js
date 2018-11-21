dojo.provide("module.homepage.Homepage");
dojo.require("mosaik.core.Module");
dojo.require("dijit.form.Button");
dojo.require("dijit.layout.BorderContainer");
dojo.require("dijit.layout.StackContainer");
dojo.require("dijit.layout.ContentPane");
dojo.require("mosaik.ui.FlexTable");
dojo.require("mosaik.ui.PageEditor");

dojo.declare("module.homepage.Homepage", [mosaik.core.Module], {
	run: function (options) {
        if ( options ) {
            this._options = options;
        } else {
            this._options = {"view": "aktuelles"};
        }

		this.flexTable.show();
        this.initFlexTable();

		this.views =["aktuelles", "referent", "kooperationspartner", "stellenangebot", "download"];
		
		sandbox.subscribe("sync/done", this, function () {
			dojo.forEach(this.views, function ( item ) {
				this.updateKategorie(item);
			},this);
			
		});
	},
	
	toggleButtons: function ( section ) {
		dojo.query(".dyn").forEach( function ( item ) {
			item.style.display = "none";
		});
		
		dojo.query("."+section).forEach( function ( item ) {
			item.style.display = "inline-block";
		});
		
	},

	_editAktuelles: function (data) {
		sandbox.loadShellModule( "aktuellesBearbeiten", {
			id: data.id,
			results: this.flexTable.getAllRows()
		});
	},
	
	_deleteAktuelles: function ( data ) {
		var service =  this.sandbox.getRpcService("database/aktuelles");

        service.remove(data.id).addCallback(dojo.hitch(this, "reload"));
	},
	
	
	_editReferent: function (data) {
		sandbox.loadShellModule( "referentBearbeiten", {
			referentId: data.id,
			results: this.flexTable.getAllRows(["id"])
			});
	},

    _deleteReferent: function ( data ) {
        alert("Löschen von Referenten nicht möglich.");
        return;
        var service =  this.sandbox.getRpcService("database/referent");

        service.remove(data.id).addCallback(dojo.hitch(this, "reload"));
    },
	
	
	_editKooperationspartner: function ( data ) {
		console.dir(data);
		sandbox.loadShellModule("kooperationspartnerBearbeiten", {
			id: data.id,
			results: []
		})
		//this.flexTable.getAllRows(["id"])
	},

    _deleteKooperationspartner: function ( data ) {
        var service =  this.sandbox.getRpcService("database/kooperationspartner");

        service.remove(data.id).addCallback(dojo.hitch(this, "reload"));
    },
	
	

	_editStellenangebot: function ( data ) {
		sandbox.loadShellModule("stellenangebotBearbeiten", {
			id: data.id,
			results: this.flexTable.getAllRows(["id"])
			})
	},

    _deleteStellenangebot: function ( data ) {
        var service =  this.sandbox.getRpcService("database/stellenangebot");

        service.remove(data.id).addCallback(dojo.hitch(this, "reload"));
    },

	// **** Liste von eintraegen ****
	select: function ( name ) {
        this._options['view'] = name;
		this.flexTable.show();
		
		this.widgets.switchContainer.selectChild(this.widgets.borderContainer);
		var pane = this.widgets.stackContainer;
		pane.selectChild(this.widgets[name+"Pane"]);

		var service = this.sandbox.getServiceUrl ( "homepage/"+name );
        this.flexTable.clearContextMenu();
        this.flexTable.addContextMenuItem("Bearbeiten", "flextable/edit"+ucfirst(name));
        this.flexTable.addContextMenuItem("Entfernen", "flextable/delete"+ucfirst(name));

		this.toggleButtons(name);
		this.flexTable.queryService(service, {});	
		this.updateKategorie(name);
	},

    selectKategorie: function (name) {
        this._options['kategorie'] = name;
        var service = this.sandbox.getServiceUrl ( "homepage/"+ this._options['view'] );

        this.flexTable.queryService(service, {
            kategorie: name
        });
    },
	
	initFlexTable : function() {
        if ( this._ftInitDone ) return;
        this._ftInitDone = true;

        this.subscribeTo("flextable/editAktuelles", "_editAktuelles");
        this.subscribeTo("flextable/deleteAktuelles", "_deleteAktuelles");

        this.subscribeTo("flextable/editReferent", "_editReferent");
        this.subscribeTo("flextable/deleteReferent", "_deleteReferent");

        this.subscribeTo("flextable/editStellenangebot","_editStellenangebot");
        this.subscribeTo("flextable/deleteStellenangebot","_deleteStellenangebot");

        this.subscribeTo("flextable/editKooperationspartner","_editKooperationspartner");
        this.subscribeTo("flextable/deleteKooperationspartner","_deleteKooperationspartner");

        this.subscribeTo("flextable/editDownload", "_editDownload");
        this.subscribeTo("flextable/deleteDownload", "_deleteDownload");

        dojo.style("toolbar","display","block");
        this.reload();

        this.widgets.borderContainer.layout();
        this.widgets.stackContainer.layout();

    },

	
	_editDownload: function ( data ) {
		sandbox.loadShellModule("downloadBearbeiten", {
			id: data.id,
			results: this.flexTable.getAllRows(["id"])
			})
	},

    _deleteDownload: function ( data ) {
        var service =  this.sandbox.getRpcService("database/download");

        service.remove(data.id).addCallback(dojo.hitch(this, "reload"));
    },
	
	_handlers: [],
	_currentKategorie: "",

    reload: function() {
        if ( this._options && this._options['view']) {
            this.select(this._options['view']);
        }

        if ( this._options['kategorie'] ) {
            this.selectKategorie(this._options['kategorie']);
        }
    },
	
	updateKategorie: function(name) {
		// Summary:
		// syncs the "rubrik" table with the view


		var rubricInfo = [];
		var target = this.nodes[name+"Kategorien"];
		if ( typeof ( target) === "undefined") return;
		var kategorieStore = sandbox.getSelectArray(ucfirst(name) + "Kategorie");
		
		var _rm = null;
		var _seminare = null;

		target.innerHTML="";

		dojo.forEach ( this._handlers, dojo.disconnect);

		this._handlers = [];
		
		dojo.forEach ( kategorieStore, dojo.hitch( this, function ( item ) {
			var div = dojo.create("div", {
				style: "float: left; width: 250px;",
				"class": "hoverBtn"
			}, target);
			var link = dojo.create("a", {
				innerHTML: item.label,
				href: "#"
			}, div);
			
			this._handlers.push( dojo.connect(link, "onclick", this, function ( e ) {


				this._currentDownloadKategorie = e.currentTarget.innerHTML;
				this.selectKategorie(item.name);
			}));
		}));
	},
	
	// **** ALLGEMEINE INFOS ****
	selectAllgemeineInfos: function () {
		this.flexTable.hide();
		this.toggleButtons("allgmeineInfos");
		var pane = this.widgets.switchContainer;
		
		pane.selectChild(this.widgets.allgemeineInfosPane);
	},
	
	// **** FRUEHBUCHER ****
	selectFruehbucher: function () {
		this.flexTable.hide();
		this.toggleButtons("fruehbucher");
		var pane = this.widgets.switchContainer;
		
		pane.selectChild(this.widgets.fruehbucherPane);
	},

    onResume: function() {
        this.reload();
    }
});