dojo.provide("mosaik.core.TitaniumApp");


dojo.require("dair.AIR");
dojo.require("dair.Application");
// mosaik
dojo.require("mosaik.core.Sandbox");
dojo.require("mosaik.core.DataStore");
dojo.require("mosaik.air.AirUpdater");

dojo.declare ("mosaik.core.TitaniumApp", null, {
	_vars: {},

	databaseUrl: "http://unkownhost",
	identity: null,
	datastore: null,
	config: null,
	shell: {},
	firstModule: "login",
	shellModule: "shell",
	windows: [],
	contextMenu: null,

	windowCount: 0,
	updater: null,
	
	constructor: function (config) {
		// Summary:
		// sets up a new application

		// Dair APP -> do Air ops
		this.app = new dair.Application({
			startHidden: true,
			autoExit: true
		});

		//delete djConfig.addOnLoad;
		// Datastore and GlobalModels
		this.datastore = new mosaik.core.DataStore(this);
		//this.globalmodels = new appcore.GlobalModels(this);
		
		this.identity = new mosaik.db.RemoteIdentity(this);
		this.config = config;
		this.djConfig = djConfig;
		console.dir(djConfig);
		this._vars = appConfig["defaults"];
		
		// create the updater
		this.updater = new mosaik.air.AirUpdater();
		this.updater.app =  this;

		dojo.subscribe("userdata/set", this.identity, this.identity.validateUserdata);
		dojo.subscribe("app/start", this, this.spawnShell);
		dojo.subscribe("request/update", this.updater, "beginUpdate");
		dojo.subscribe("userdata/validate", this, this.setupDatastore);
		dojo.subscribe("document/click", this, function () {
			this.contextMenu.hide()
		});
		
		
	},

	setupDatastore: function () {
		this.datastore.clear();
		
		//setup datastore
		this.datastore.registerObject( {
			name: "Hotel",
			autosync: true,
			versioned: true
		} );
		
		this.datastore.registerObject( {
			name: "KontaktKategorie",
			autosync: true,
			versioned: true
		} );
		
		this.datastore.registerObject( {
			name: "KontaktStatus",
			autosync: true,
			versioned: true
		} );
		
		this.datastore.registerObject({
			name:"XAnrede",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XStatus",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XBranche",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XBundesland",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XCombobox",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XGrad",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XGroup",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XLand",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XTodoKategorie",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XUser",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XTodoRubrik",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XTodoStatus",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"XTaetigkeitsbereich",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"Standort",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"ViewStandortKoordinaten",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"SeminarFreigabestatus",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"SeminarArtRubrik",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"SeminarArtStatus",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"SeminarArt",
			autosync: true,
			versioned: true
		});

		this.datastore.registerObject({
			name:"InhouseSeminarArt",
			autosync: true,
			versioned: true
		});

		this.datastore.registerObject({
			name:"Kooperationspartner",
			autosync: true,
			versioned: true
		});
		
		this.datastore.registerObject({
			name:"KooperationspartnerKategorie",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"DownloadKategorie",
			autosync: true,
			versioned: true
		});
		this.datastore.registerObject({
			name:"StellenangebotKategorie",
			autosync: true,
			versioned: true
		});

		this.datastore.registerObject({
			name:"startseite/statistiken",
			serviceName: "startseite",
			methodName: "getStatistics",
			autosync: true,
			versioned: false
		});

		this.datastore.registerObject({
			name:"shell/termineHeute",
			serviceName: "startseite",
			methodName: "getTermineToday",
			autosync: true,
			versioned: false
		});

		this.datastore.registerObject({
			name:"startseite/termine",
			serviceName: "startseite",
			methodName: "getNextTermine",
			autosync: true,
			versioned: false
		});
		
		this.datastore.registerObject({
			name:"startseite/todo",
			serviceName: "startseite",
			methodName: "getUserTodo",
			autosync: true,
			versioned: false
		}); // FIXME needs user id as argument

		this.datastore.registerObject({
			name:"UserSettings",
			serviceName: "user/settings",
			methodName: "getUserSettings",
			autosync: true,
			versioned: false
		});

		dojo.subscribe ("sync/start", this.datastore, "startSync");

	},

	generateId: function () {
		var x = new Date();
		return x.getMilliseconds();
	},

	createMenuWindow: function () {
		console.info("Creating Menu Window..");
		var window = new dair.Window(this.config.appWindows["menu"]);
		var tiWindow = this.app.addWindow( window );
		
		// heh this _is_ weird
		var _window =window._window.window;
		_window.console = console;
		_window.app = this;
		_window.djConfig = this.djConfig;
		//_window.djConfig.addOnLoad = null;
		_window.moduleDjConfig = moduleDjConfig;
		this.contextMenu = _window;
	},

	spawnSandbox: function ( /*String*/ moduleName, /* Window Object */ _window, /* int */ shellId ) {
		// Summary:
		// spawns a module to a window containing _empty.html
		console.info ("TitaniumApp::spawnSandbox( "+moduleName+" ) to Shell: " + shellId );
		console.log("leave spawn sandbox");
	},
	
	spawnSandboxWindow: function (/*String */ moduleName , /* Object*/ options) {
		// Summary:
		// opens a new window and spawns module with name moduleName to this window
		// creates a new sandbox for the module
		console.info ("TitaniumApp::spawnSandboxWindow : " + moduleName);
		// create Window
		var window = new dair.Window(this.config.appWindows[moduleName]);
		var tiWindow = this.app.addWindow( window );
		// heh this _is_ weird
		var _window =window._window.window;
		//...
		console.log("Setting up window");
		_window.runtime = window.runtime;
		_window.djConfig = this.djConfig;
		_window.moduleDjConfig = moduleDjConfig;
		
		_window.console = console;
		_window.module = moduleName;
		_window.app = this;
		_window.options = options;
		_window.dairWindow = tiWindow;
		console.log("[Done]");
	},
	
	run: function () {
		// Summary:
		// begins to run this App
		// starts with the module this.firstModule ( defaults to "login" )
        air.trace("Create menu Window...");
		this.createMenuWindow();
		//this.createPdfWindow();
        air.trace("spawnSandboxWindow");
		this.spawnSandboxWindow( this.firstModule );
        air.trace("sendHeartbeat");
		this.sendHeartbeat();		
	},
	
	spawnShell: function () {
		// Summary:
		// spawns the first shell (which is just another module)
		// loads module this.shellModule (which defaults to "shell")
		this.databaseUrl =  this.identity.database;
		// set stuff
		this._userVarsService = new dojox.rpc.Service ( this.datastore.getSmd("user/settings") );
		this._userVarsCache = this.datastore.loadObject("UserSettings");
		if ( ! this._userVarsCache ) {
			this._userVarsCache = {};
		}

		console.log(" ===========>>> Loaded Persistant Settings:")
		console.dir(this._userVarsCache);
		this.spawnSandboxWindow(this.shellModule,{
			"mod": this.shellModule
		});
		
	},
	
	exit: function() {
		// Summary:
		// shuts down the whole application

		this.app.shutdown();
	},

	//APP Global variables:
	
	setVar: function (name,value) {
		// Summary:
		// sets the variable name to value value
		this._vars[name] = value;
	},

	
	getVar: function (name, def) {
		// Summary:
		// returns the content of varaible name
		var ret = typeof ( this._vars[name] ) === "undefined" ? def : this._vars[name];
		console.log("Value for: " + name +": " + this._vars[name]);
		return ret;
	},

	// app global scubscribe / publish
	publish: function (name, data ) {
		//console.debug ("GLOBAL-Signal: " + name);
		//console.dir(data);
		dojo.publish(name, data);
		var i=0;
		
		console.log("Forwarding events ["+name+"]to windows....");
		for ( i=0; i<this.windows.length; i ++ ) {
			if ( typeof (this.windows[i].publish) !== "undefined" ) {
				this.windows[i].publish(name,data);
			} else {
				//console.log ("Window " + i + " has no publish method!");
			}
		}
	},

	sendHeartbeat: function () {
	//this.publish ("heartbeat", {tick: "tock"});
	//setTimeout(dojo.hitch( this, this.sendHeartbeat), 500);
	},

	getUserinfo: function () {
		// Summary:
		// returns info about the current loggedin user
		return this.identity.userinfo;
	},

	navigateToUrl: function (url) {
		// Summary:
		// open url in external browser
		// FIXME: should be moved to sandbox
		console.log("Navigate to URL: " + url);
		var req = new air.URLRequest();
		req.url = url;
		air.navigateToURL(req, "_new");
	},
	
	exportBound:false,
	exportToFile: function (dialogTitle, data) {
		//Summary:
		// Export data to File (which the user selects 
		// the dialog title will be dialogTitle
		if ( ! this.exportFile) {
			this.exportFile = air.File.documentsDirectory;
		}
		this.exportData = data;
		this.exportFile.browseForSave(dialogTitle);
		if ( !this.exportBound) {
			this.exportFile.addEventListener(air.Event.SELECT, dojo.hitch ( this, "_exportSelected"));
			this.exportBound =true;
		} 
	},

	_exportSelected: function ( evt ) {
		// Summary:
		// handle file selection for export
		var file = this.exportFile;
		var data = this.exportData;
		data = data.replace(/\n/g, air.File.lineEnding);

		try {
			var stream = new air.FileStream();
			stream.open( file, air.FileMode.WRITE);
			
		} catch ( e ) {
			console.log ( e);
			alert("Fehler beim Öffnen!");
		}
		
		try {
			stream.writeMultiByte(data,"iso-8859-15");
			stream.close();
		} catch (e) {
			console.log(e);
			alert("Fehler beim Schreiben!");
		}
	},
	
	uploadFile: function ( dialogTitle, types, vars) {
		this.uploadFileRef = null;
		this.uploadFileRef = air.File.documentsDirectory;
		
		var filter = [];
		for ( type in types) {
			filter.push (new air.FileFilter(type, types[type]));
		}
		this.uploadFileVars = vars;
		this.uploadFileRef.browseForOpen(dialogTitle, filter);
		
		this.uploadFileRef.addEventListener(air.Event.SELECT, dojo.hitch ( this, "_uploadFile"));
		this.uploadFileRef.addEventListener(air.Event.CANCEL, dojo.hitch ( this, "_uploadCancel"));
		this.uploadFileRef.addEventListener(air.DataEvent.UPLOAD_COMPLETE_DATA, dojo.hitch ( this, "_uploadFileDone"));
		this.uploadFileRef.addEventListener(air.HTTPStatusEvent.HTTP_STATUS, dojo.hitch ( this, "_uploadFileError"));

	},
	
	_uploadFile: function(evt) {
		var url = this.databaseUrl + "/_upload/?" + dojo.objectToQuery(this.uploadFileVars);
		console.log("Uploading file to: " + url );
		var uploadReq = new air.URLRequest( url );
		
		this.uploadFileRef.upload(uploadReq);
		this.publish("upload/begin",[{
			url: url
		}]);
	},
	
	_uploadFileDone: function (e) {
		
		var url = this.databaseUrl + "/_upload/?" + dojo.objectToQuery(this.uploadFileVars);
		console.log("UPLOAD DONE");
		console.log ("ResponseText:" + e.text);
		console.log("ResponseData" + e.data);
		this.publish("upload/done", [{
			url: url, 
			response: e.data, 
			newFileName: e.text
		}]);
	},
	
	_uploadFileError: function () {
		var url = this.databaseUrl + "/_upload/?" + dojo.objectToQuery(this.uploadFileVars);
		console.log("UPLOAD ERROR");
		this.publish("upload/error", [{
			url: url
		}]);
	},
	
	_uploadCancel: function () {
		var url = this.databaseUrl + "/_upload/?" + dojo.objectToQuery(this.uploadFileVars);
		console.log("UPLOAD ERROR");
		this.publish("upload/cancel", [{
			url: url
		}]);
	},
	
	createPdfWindow: function ( url ) {
		console.info("Creating Pdf Window..");
        this.navigateToUrl(url);

        return;
        /* pdf preview windows - not working for now
		var window = new dair.Window(this.config.appWindows["pdf"]);
		var tiWindow = this.app.addWindow( window );
		
		// heh this _is_ weird
		var _window =window._window.window;
		_window.console = console;
		_window.app = this;
		_window.djConfig = this.djConfig;
		_window.moduleDjConfig = moduleDjConfig;
		
		_window.pdfUrl = url;
		console.log("createPdfWindow: " + url);
		_window.nativeWindow.visible=true;
		_window.nativeWindow.title = "Druckvorschau";*/
		
	},
	
	openPdf: function ( url ) {
		// Summary:
		// opens a new window displaying the PDF Specifiyed by url
		
		// test url: "http://sag-akademie.localhost/admin/pdf/buchung;pdf/4989?token=28df385b4dbee98030daeb64b327811e"
		
		console.log("openPdf: " + url);

		this.createPdfWindow( url );
	},

	_userVarsCache: {},

	getUserVarsVersion: function (ns) {
		if ( typeof ( this._userVarsCache[ns]) === "undefined" || typeof ( this._userVarsCache[ns]['__version']) === "undefined" ) {
			return 1;
		} else {
			return this._userVarsCache[ns]['__version'];
		}
	},

	getUserVars: function (ns) {
        var conf;
		if ( typeof ( this._userVarsCache[ns] ) === "undefined") {
			conf ={};
		} else {
			conf = this._userVarsCache[ns];
			
		}
		console.log("=== USERVARS FOR " + ns + " === ");
		console.dir(conf);
		return conf;
	},

	_syncTimeout: null,
	setUserVars: function (ns, vars) {
		

		this._userVarsCache[ns] = vars;

		if ( typeof ( this._userVarsCache[ns]['__version']) === "undefined" ) {
			this._userVarsCache[ns]['__version'] = 1;
		} else {
			this._userVarsCache[ns]['__version']++;
		}
		if ( this._syncTimeout !== null ) clearTimeout(this._syncTimeout);
		this._syncTimeout = setTimeout(dojo.hitch( this, "syncSettings"), 10000);

		return vars;
	},

	syncSettings: function () {
		console.log("=== SYNCRONIZING USER VARS === ");


        var out = [];

		for ( key in this._userVarsCache ) {
         //   console.log("Var: " + key);
          //  console.dir(this._userVarsCache[key]);
			out.push ( {key: key, data: this._userVarsCache[key]} );
		}

		this._syncTimeout=null;
		//console.log(dojo.toJson(out));
		this._userVarsService.setUserSettings([out]).addCallback(dojo.hitch(this, function(data) {
			console.log("User vars syncronized");
		})).addErrback(function(data,err) {
                console.error("TitaniumApp::syncSettings ERROR:" + err);
			//alert("Abgleich der Einstellungen mit dem Server fehlgeschlagen!");
			
		});
	}

	
});


