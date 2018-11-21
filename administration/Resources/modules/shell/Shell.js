dojo.provide("module.shell.Shell");
dojo.require("mosaik.ui.FlexTable");

dojo.require("mosaik.core.helpers");
dojo.require("mosaik.core.Module");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.form.ComboButton");
dojo.require("module.shell.ShellModuleInfo");
dojo.require("module.shell.NeuMenu");
dojo.require("dijit.layout.BorderContainer");
dojo.require("dijit.layout.ContentPane");

dojo.declare ("module.shell.Shell", [mosaik.core.Module], {
	userNameNode: null,
	_overlay: null,
	_overlayVisible: false,
	_options: {},
	_currentModuleOptions: null,
	_initialModule: "startseite",
	_evHandlers: [],
	framecounter: 1,

	run: function( options ) {
		// Summary:
		// begin execution here
		
		this._options = options;
		console.debug("Shell::run >>>");
		console.dir(this.options);
		console.debug ("<<< Shell::run");

		// set this as SHELL
		sandbox.setShell(this);
		window.maximize();

		// spawn the initial module to the sandbox
		this.loadShellModule(this._initialModule);

		// connect the node where the currentUsername goes
		this.userNameNode = dojo.byId("userName");
		// and set it to the username
		this.userNameNode.innerHTML = sandbox.getUserinfo().dn;

		// link the module buttons
		this.linkButtons();

		this.moduleContainer = dojo.byId("contentArea");
		
		dojo.subscribe("sync/start", this, function () {
			dojo.byId("loadIndicator").style.display = "block";
		});
		
		dojo.subscribe("sync/done", this, function () {
			dojo.byId("loadIndicator").style.display = "none";
		});
		
		forwardPublish = dojo.hitch ( this, this.forwardPublish);

		showSWF( "flexOverlay", "/app/widgets/AIRKalender.swf" );
		this.updateToday();
		
		 dojo.subscribe ("shell/termineHeuteUpdate", this, this.updateToday);
         dojo.subscribe ("shell/discardChanges", this, this.forceChange);


		dojo.connect(window,"onmousedown", this, function() {
			sandbox.hideContextMenu();
		});
	},

	linkButtons: function () {
		// Summary:
		// connects the upper buttons to functions

		this.closeWindowNode = dojo.byId("closeWindow");
		this.openNewWindowNode = dojo.byId("openNewWindow");

		this._evHandlers.push ( dojo.connect(this.closeWindowNode,"onclick", this, "onCloseWindow") );
		this._evHandlers.push ( dojo.connect(this.openNewWindowNode,"onclick", this, "onNewWindow") );

		dojo.query(".modLink").forEach ( dojo.hitch ( this , function (item, index, array) {
			var button = dijit.byNode(item);
			button.set ( "mod", this);
			this._evHandlers.push ( dojo.connect (button, "onClick", function (e) {
				this.mod.loadShellModule ( this.get("id") );
			}));
		}));
	},

	onCloseWindow: function () {
		// Summary:
		// close the APP!
		this.sandbox.exitApp();
	},

	onNewWindow: function () {
		// Summary:
		// spawn a new shell inside a fresh window
		this.sandbox.spawnShell();
	},

    askIfChanged: function(discardCb) {
        // Summary:
        //  asks the user wether the made changes should be saved
        // or discarded
        if ( isModuleChanged() ) {
            this.showSaveDialog(discardCb);
        } else if ( discardCb ) {
            discardCb();
        }
           // var leave=confirm ("Wollen Sie das Formular ohne zu Speichern verlassen?\n\nOK zum Wechsel.\nAbbrechen um zum Formular zurück zu kehren.");
           // if (!leave) {
             //   return true;
           // }
        //}
        //if (discardCb) {
         //   discardCb();
       // }


       // return false;
    },

	loadShellModule: function(moduleName, options) {
		// Summary:
		// loads a module in this shell
		// can be triggered via sandbox.shellLoadModule
        this._nextModuleName = moduleName;
        this._nextOptions = options;

        if ( isModuleChanged() ) {
            this.askIfChanged();
        } else {
            this.forceChange();
        }

		//dojo.byId("modFrame").src = "/app/modules/"+moduleName+"/index.html";
	},

    forceChange: function () {
        // Summary:
        // discards current changes and loads moduleName with options replacing the current loaded module
        unsetModuleChanged();
        var moduleName = this._nextModuleName;
        var options = this._nextOptions;




        this.showLoadingScreen("Lade Modul..");
        this._currentModuleName = moduleName;
        this._currentModuleOptions = options;
        var mod = new module.shell.ShellModuleInfo();
        mod.options= options;
        mod.childModule = moduleName;
        mod.name = moduleName;
        mod.classname =  "module." + moduleName + "." + ucfirst(moduleName);
        mod.url = "/app/modules/"+moduleName+"/index.html";

        this.clearForward();

        addIFrame(mod);
    },

	showLoadingScreen: function ( msg ) {
		// Summary:
		// shows a loading screen on module change
		// can also be triggered by modules inside this shell via
		// sandbox.showLoadingScreen (to get the shell right)
		if ( dojo.byId("contentArea") && !this._overlayVisible) {
			dojo.addClass("contentArea","waiting");
			sandbox.publish("shell/showLoadingScreen");
			this._overlay = new dojox.widget.Standby ({
				target: "contentArea",
				zIndex: 9999,
				text: msg,
				duration: 250
			});
			dojo.addClass(this._overlay.domNode , "waiting");
			document.body.appendChild(this._overlay.domNode);
			this._overlay.startup();
			this._overlay.show();
			this._overlayVisible = true;
		}
	}, 


	hideLoadingScreen: function () {
		// Summary:
		// hides the loading screen
		// can be used via the sandbox.hideLoadingScreen
		if ( this._overlay && this._overlayVisible ) {
			dojo.removeClass("contentArea","waiting");
			sandbox.publish("shell/hideLoadingScreen");
			this._overlay.hide();
			this._overlayVisible = false;

		}
	},
	
	// history management
	_backward: [],
	_forward: [],
	activeChildModule: null,
	moduleContainer: null,
	
	addHistory: function ( shellModuleInfo ) {
		if ( shellModuleInfo === null ) return;
		
		this._backward.push(shellModuleInfo);
	},
	
	canGoForward: function () {
		return this._forward.length > 0;
	},
	
	canGoBackward: function () {
		return this._backward.length > 0;
	},
	
	clearForward: function () {
		dojo.forEach ( this._forward , function ( item ) {
			item.api.destroy();
			dojo.destroy(item.frame);
		});
		
		this._forward = [];
	},
	
	goForward: function () {
        this.askIfChanged(dojo.hitch(this, function () {

            if ( this.canGoForward() ) {
                var mod = this._forward.pop();
                mod.includeInHistory = true;
                this.setActiveChild(mod);
            }
        }));
	},
	
	goBackward: function () {
         this.askIfChanged(dojo.hitch(this,function () {

            if ( this.canGoBackward()) {
                var mod = this._backward.pop();
                mod.includeInHistory = false;
                this._forward.push ( this.activeChildModule );
                this.setActiveChild(mod, false);
            }
        }));
	},
	
	// module loading
	setActiveChild: function( child , includeInHistory) {
		// include previous module in history?
		includeInHistory = typeof(includeInHistory) === "undefined" ? true : includeInHistory;
		
		if ( this.activeChildModule !== null && this.activeChildModule.initialized && this.activeChildModule.api.isRunning( )) {
			this.activeChildModule.api.suspend( { } );
			this.activeChildModule.frame.style.visibility = "hidden";
		}
		
		if ( includeInHistory === true && child.includeInHistory === true) {	
			this.addHistory (this.activeChildModule);
		} else {	
			// lets get rid of this module
			// destroyModule( activeChildModule );
			// TODO
		}
				
		this.framecounter++; // increase it so it gets visible again
		var zindex = this.framecounter + 100; // create a bigger zindex
		child.frame.style.zIndex = zindex;
		this.activeChildModule = child;
		
		// if we go back in history it may be that 
		// we have an suspended module
		if ( child.initialized && ! child.api.isRunning( )) {
			this.activeChildModule.frame.style.visibility = "visible";

			child.api.resume({});
		}
		// update the back button
		dijit.byId("back").set("disabled", !this.canGoBackward() );

		// update the forward button
		dijit.byId("forward").set("disabled",  !this.canGoForward());

	},
	
	forwardPublish: function (name, data) { // sends global singals to children
		if ( this.activeChildModule != null && this.activeChildModule.api != null) {
			this.activeChildModule.api.publishFromApp(name,data);
		}
	},

	clearToday: function() {
		dojo.byId("termineHeuteListe").innerHTML="";
	},

	updateToday: function() {
		this.clearToday();
		var values = sandbox.getItemStore("shell/termineHeute");

		var results = values.query ({} ) ;
		var self=this;
		if ( results  ) {
			var _res = [];
			
			
			for ( var i=0; i < results.length; i++ ) {
				_res.push({id: results[i].id});
			}
			
			for ( var i=0; i < results.length; i++ ) {
				var _termin = results[i];
		
				var node = dojo.create("div", {}, dojo.byId("termineHeuteListe"));
				var a = dojo.create("a", {href: "#", innerHTML: _termin.kursnr}, node);
				
				dojo.connect( a, "onclick", {terminId: _termin.id},function(evt) {
					self.loadShellModule("terminBearbeiten", {"id": this.terminId, "terminId": this.terminId, results: _res});				 
				});
			}


		} else {
			dojo.byId("termineHeuteListe").innerHTML="Keine Termine";
		}
	},

	forceReload: function () {
		sandbox.publish("sync/start");
		this.loadShellModule(this._currentModuleName, this._currentModuleOptions);

	},

    showSaveDialog: function (discardCb) {
        // Opens up a Dilaog with the Options
        // save, cancel, withdraw changes
        var saveDialog;
        var tb;
        var self=this;
        this.forwardPublish("module/hideFlash");

        saveDialog = new dijit.Dialog({
            title: "Ungespeicherte Änderungen",
            style: "width: 300px",
            content: "Sie haben ungespeicherte Änderungen am Formular vorgenommen."
        });

        tb = dojo.create("div", {

            style: {
                height: "20px",
                textAlign: "right",
                padding: "5px"
            }
        },saveDialog.domNode);
        dojo.addClass(tb,"gradient1");
        var saveBtn = new dijit.form.Button({
            label: "speichern",
            onClick: function ( ) {
                saveDialog.hide();
                self.forwardPublish("module/showFlash");
                self.forwardPublish("module/save");
            }
        });

        tb.appendChild(saveBtn.domNode);
        var withdrawBtn = new dijit.form.Button({
            label: "verwerfen",
            onClick: function () {
                saveDialog.hide();
                if ( discardCb ) {
                    discardCb();
                } else {
                    self.forceChange();
                }
            }
        });
        tb.appendChild(withdrawBtn.domNode);
        var cancelBtn = new dijit.form.Button({
            label: "abbrechen",
            onClick: function() {
                self.forwardPublish("module/showFlash");
                saveDialog.hide();
            }

        });
        tb.appendChild(cancelBtn.domNode);

        saveDialog.show();
    }

	
});

