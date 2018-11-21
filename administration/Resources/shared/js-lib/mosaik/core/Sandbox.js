console.log("mosaik.core.Sadnbox");
dojo.provide("mosaik.core.Sandbox");

dojo.require('dojox.data.JsonRestStore');
dojo.require("mosaik.db.RemoteIdentity");
dojo.require("mosaik.db.DataSource");
dojo.require("mosaik.db.LocalObjectStoreDriver");
dojo.require("dojox.rpc.Service");
dojo.require("dojox.rpc.JsonRPC");
dojo.require("dojo.store.Memory");

dojo.declare("mosaik.core.Sandbox" , null, {
	// Summary:
	// this modules organizes the internal communictions from and to modules
	// and let the modules communictate with the application
	//
	// this is the place to hook up to specific module events and
	// create access layers to the core-framework functions

	module: "login",
	prefix: "/",
	moduleConfig: null,
	moduleClass: "",
	
	tiWindow: null,
	window: null,
	app: null,
	shell: null,
	ds: null,
	
	moduleTheme: "tundra",
	//moduleTheme: "soria",
	_vars: null,
	_shellId: -1,
	_isRunning: true,
	
	constructor: function (vars) {
		dojo.mixin(this,vars);
		this._vars = {};
	},

	spawnShell: function() {
		app.spawnShell();
	},

	/** SHELL RELATED **/
	setShellId: function (id) {
		this._shellId = id;
	},

	setShell: function ( shell ) {
		// Summary:
		// sets the shell
		console.log ("Setting shell " + this._shellId );
	//this.app.shell[this._shellId] = shell;
	},

	getShell: function ( ) {
		if ( typeof ( this.app.shell[this._shellId]) !=="undefined" && this.app.shell[this._shellId] !== null ) {
		//return this.app.shell[this._shellId];
		} else {
			console.error ( "=> SHELL UNKNOWN: " + this._shellId);
			return null;
		}
	},

	loadShellModule: function (name, options) {
		// Summary:
		// loads a module inside the shell modules iframe ( or whatever the shell.loadShellModule function does with module names )
		
		this.hideContextMenu();
		
		this.setModuleChanged(currentModule.isChanged());
		
		if ( typeof( window.shell ) !== "undefined") {
			window.shell.loadShellModule(name, options);
		} else {
			alert ("shell not set");
		}
	},
	
	setModuleChanged: function (b) {
		if ( b == true) {
			window.setModuleChanged();
		} else {
			window.unsetModuleChanged();
		}
	},

	exitApp: function () {
		// Summary:
		// shuts down the app
		// forwards the function call to the application
		this.app.exit();
	},

	loadModule: function ( ) {
		this.hideContextMenu();

		// Summary:
		// is called right from windowBootstrap code
		console.log("SANDBOX::Including Module " + moduleName + " Class: " + moduleClass );
		moduleInstance = {};

		dojo.require( this.moduleClass );

		console.log("==========>> class files loaded");

		moduleInstance = new( dojo.getObject(this.moduleClass))();
		moduleInstance.setSandbox(this);
		window.currentModule = moduleInstance;
		
		window.setTitle(document.title);
			
		return moduleInstance;
	},
	
	

	runModule: function (options) {
		//console.log("SANDBOX::runModule with options: ");
		//console.dir(options)

		// init menu
		//dojo.connect(document.body,"mouseMove", this,"hideContextMenu");
		
		moduleInstance.doRun(options);
	},

	/*********************** CREATE MODULES STOP *****************************/

	getItemStore: function (name, label) {
		// Summary:
		// create a new ItemFileReadStore out of
		// data from the DataSource layer
		if ( typeof(label) === "undefined" ) {
			label = "dn";
		}

		var options = {
			idProperty: "id",
			label: label,
			data: this.ds.loadArray(name)
		};
		//console.dir(options);

		return new dojo.store.Memory(options);
	},

	getValueById: function (sourceName, id ) {
		var values = this.getObject(sourceName);
		var item = {
			"name": ""
		};

		dojo.forEach ( values, function ( _item ) {

			if ( _item.id == id) {
				item = _item;
			}
		});
		//console.dir(item);
		return item.name;
	},

	getSelectArray: function ( name ) {
		// Summary:
		// returns an array suitable for dijit.form.Select
		var data = this.getObject(name);
		var options = [];
		
		dojo.forEach(data, function ( item, request) {
			options.push({
				label: item.name.toString(),
				value: String( item.id ),
				selected: false
			})
		});
		
		return options;
	},

	getArray: function ( name ) {
		return this.ds.loadArray(name);
	},

	getObject: function (name) {
		// Summary:
		// bridge calls to datasource
		return this.ds.loadObject(name);
	},
	/** AUTHENTICATION **/
	getUserinfo: function () {
		// Summary:
		// gets current identity
		return app.getUserinfo();
	},

	/** GLOBAL subscribe / publish **/
	publish: function ( name , data) {
		// Summary:
		// send signals to all opened windows
		console.debug ("SANDBOX Publish: " + name);
		app.publish(name, data);
	},
	
	/** GLOBAL subscribe / publish **/
	subscribe: function ( name , context, cb) {
		dojo.subscribe(name, context, cb);
	},
	
	// DATASOURCE
	// gibt die url aus der config fuer einen bestimmten schluessel zurueck
	getServiceUrl: function (service) {
		console.info ("Requesting Service: " + service);
		return ds.getServiceURL (service);
	},

	getRpcService: function ( service ) {
		return new dojox.rpc.Service ( ds.getSmd(service) );
	},

	// set sandbox variables
	setVar: function (name,value) {
		this._vars[name] = value;
	},
	
	// get sandbox variable
	getVar: function (name, def) {
		return typeof ( this._vars[name] ) === "undefined" ? def : this._vars[name];
	},

	setAppVar: function (name, value) {
		this.app.setVar(name, value);
	},

	getAppVar: function (name ,def) {
		return this.app.getVar(name, def);
	},

	// FORWARDED TO THE CORRESPONDING!!! SHELL
	showLoadingScreen: function (msg) {
		// Summary:
		// forwards showLoadingScreen
		// to the shell
		if ( !currentModule.isRunning() ) return;
		if ( typeof(shell) !== "undefined" ) {
			shell.showLoadingScreen(msg);
		}
	},

	hideLoadingScreen: function () {
		// Summary:
		// forwards hideLoadingScreen
		// to the shell
		if ( !currentModule.isRunning() ) return;
		
		if ( typeof(shell)!=="undefined" ) {
			shell.hideLoadingScreen();
		}
	},

	navigateToUrl: function (url) {
		this.app.navigateToUrl(url);
	},

	exportToFile: function (title, data) {
		this.app.exportToFile(title, data);
	},
	
	_fileUploadInitialized: false,
	initFileUpload: function () {
		if ( this._fileUploadInitialized ) return;
		// bind to app signals
		dojo.subscribe( "upload/done", this, "uploadDone"); // handle after file upload
		dojo.subscribe( "upload/begin", this, "uploadBegin"); // handle begin file upload
		dojo.subscribe( "upload/cancel", this, "uploadCancel"); // handle begin file upload

		this._fileUploadInitialized = true;
	},
	
	_uploadDeferred: null,
	uploadFile: function ( dialogTitle, filetypes, vars) {
		this.initFileUpload();
		this._uploadDeferred = new dojo.Deferred();
		this.app.uploadFile( dialogTitle, filetypes,vars);
		
		return this._uploadDeferred;
	},
	
	uploadBegin: function (data) {
		console.log("Sandbox::uploadBegin");
		this.showLoadingScreen("");
	},
	
	uploadDone: function (data) {
		console.log("Sandbox::uploadDone");
		this.hideLoadingScreen();
		
		if ( this._uploadDeferred ) {
			this._uploadDeferred.callback( data );
			this._uploadDeferred = null;
		}
	},
	
	uploadCancel: function (data) {
		console.log("Sandbox::uploadCancel");
		this.hideLoadingScreen();
		
		if ( this._uploadDeferred ) {
			this._uploadDeferred.cancel();
			this._uploadDeferred = null;
		}
	},
	
	showFile: function (query) {
		var storeUrl = this.app.getVar("serverurl");
		
		this.navigateToUrl( storeUrl + "/_upload?" + dojo.objectToQuery(query) );
	},
	
	queryFileStore: function (query) {
		var storeUrl = this.app.getVar("serverurl");
		var d = new dojo.Deferred();
		
		dojo.xhrGet({
			url: storeUrl + "/_upload",
			handleAs: "text",
			preventCache: true,
			content: query,
			load: function (data) {
				//cb
				d.callback(data);
			}, 
			error: function(data){
				//cb
				d.errback(data)
			}
		});
		return d;
		
	},
	
	_ctxListener: [],
	_currentMenu: null,
	_menuVisible: false,

	toggleContextMenu: function (menu, nodeId) {
		if ( this._menuVisible ) {
			//app.contextMenu.hide();
		} else {
			this.showContextMenu ( menu, nodeId);
		}
	},

	showContextMenu: function ( menu , nodeId ) {
		this._menuVisible=true;
		app.contextMenu.clear();
		app.contextMenu.addItems ( menu );
		var node = dijit.byId(nodeId).domNode;
		var pos = dojo.position(node);
		var box = dojo.marginBox(node);
		this._currentMenu = menu;
		
		app.contextMenu.show({
			x: window.screenX + pos.x,
			y: window.screenY + pos.h + pos.y + box.h
		});
		
		this._ctxListener.push( dojo.subscribe("menu/click", this, "_onSelectContextMenu") );
		this._ctxListener.push( dojo.subscribe("menu/hide", this, "_onHideContextMenu") );
	},

	hideContextMenu: function () {
		this._menuVisible=false;
		app.contextMenu.hide();
	},

	_onHideContextMenu: function () {
		this._currentMenu = null;
		this._menuVisible=false;
		dojo.forEach ( this._ctxListener, function (item) {
			dojo.unsubscribe( item );
		})
		
		dijit.focus(document.body);
	},
	
	_onSelectContextMenu: function ( signal ) {
		console.log("!=!="+signal + "cliicked");
		
		for ( var idx in  this._currentMenu ) {
			if ( this._currentMenu[idx].signal == signal ) {
				this._currentMenu[idx].onclick();
				break;
			}	
		}
	},
	
	_userVars: {__version: 0},
	
	getUserVar: function (name, defValue) {
		var uv = app.getUserVarsVersion ( this.moduleClass);
		
		if ( uv != this._userVars['__version'] ) {
			this._userVars = app.getUserVars(this.moduleClass);
		}
		
		if ( typeof ( this._userVars[name]) !== "undefined" ) {
			var r= this._userVars[name];
			//console.log("Giving: " +name + " is " + r);
			return r;
		} else {
			//console.log("Giving: " +name + " default " + defValue);
			return defValue;
		}
		
		return null;
	}, 

	_userVarsVersion: -1,
	setUserVar: function ( name, value) {
		//console.log("Setting "+name + " to " + value);
		this._userVars[name] = value;
		this._userVars = app.setUserVars(this.moduleClass, this._userVars);
		
		//this._userVarsService.update ( this.moduleClass, name, value);
	},

	pushUserVars: function () {
		this._userVarsDirty=false;
	},
	
	reportError: function (message) {
		app.reportError(this.moduleClass, message);
	},
	
	suspend: function () {
		this._isRunning=false;
	}, 
	
	resume: function () {
		this._isRunning=true;
	}

	
});
	
