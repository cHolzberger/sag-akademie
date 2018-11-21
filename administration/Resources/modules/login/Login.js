console.log("module.login.Login");
dojo.provide("module.login.Login");

dojo.require('dijit.Dialog');
dojo.require('dijit.layout.ContentPane');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.FilteringSelect');
dojo.require("mosaik.core.helpers");

dojo.require('dijit.layout.StackContainer');

// XTables
dojo.require("appcore.GlobalModels");

dojo.declare("module.login.Login", [mosaik.core.Module], {
	loginButton: null,
	validAirVersions: [
	"3.1.0.4880",
	"3.2.0.2070",
	"3.3.0.3650",
	"3.3.0.3670"
	],
	
	initLoginModule: function () {
		console.log("Login => initLoginModule");
		this._iUsername = dijit.byId("username");
		this._iPassword = dijit.byId("password");
		this._iDatabase = dijit.byId("database");
		
		this.stack = dijit.byId("stackContainer");
		this.loginView = dijit.byId("loginContainer");
		this.logonRunning = dijit.byId("logonRunning");
		this.logonOk = dijit.byId("logonOk");
		this.syncRunning = dijit.byId("syncRunning");
		this.tableNameNode = dojo.byId("currentTable");

		this.stack.selectChild(this.widgets.updateContainer);

		this.loginButton = dijit.byId("loginNow");
		
		sandbox.subscribe("update/done", this, "isUpToDate");
		sandbox.subscribe("update/error", this, "onUpdateError");
		sandbox.subscribe("update/run", this, this.runUpdate);
		
		sandbox.subscribe("update/download/start", this, this.downloadStart);
		sandbox.subscribe("update/download/progress", this, this.downloadProgress);
		sandbox.subscribe("update/download/complete", this, this.downloadComplete);

		sandbox.subscribe("app/version", this, this.updateVersionString);
		
		sandbox.subscribe("userdata/ok", this, this.authenticationStateChange );
		sandbox.subscribe("userdata/error", this, this.authenticationStateChange );
		sandbox.subscribe("userdata/invalid", this, this.authenticationStateChange );
		sandbox.subscribe("userdata/waiting", this, this.authenticationStateChange );
		sandbox.subscribe("sync/done", this, this.logonSuccessfull );
		sandbox.setShell(this);

		dojo.connect ( this.loginButton, "onClick", this,"_doLogin");

		dojo.connect ( dojo.body(), "onkeydown", this, function ( e ) {
			if ( e.keyCode == dojo.keys.ENTER ) {
				this.loginButton.focus();
				this._doLogin();
			}
		});

		this._loadUsername();
		
		dojo.byId("airVersion").innerHTML = djConfig.airVersion;
	},
	
	updateVersionString: function (data) {
		this.nodes.versionString.innerHTML = data.localVersion;
		
	},
	
	onUpdateError: function () {
		//alert("Fehler beim Update...\nDas Programm wird in der Vorgängerversion gestartet.");
		this.isUpToDate();
	},
	
	isUpToDate: function (data) {
		console.log("Login::isUpToDate");
		this.stack.selectChild(this.widgets.loginContainer);
		// alte versionsprüfung
		//if (dojo.some( this.validAirVersions, function(item) { return item == djConfig.airVersion } )) {
		//	
		//} else {
		//	this.stack.selectChild(this.widgets.airVersionFailed);
		//}
		
		console.log("Done...");
	},
	
	runUpdate: function (info) {
		this.nodes.updateStatus.innerHTML="Installiere Update auf " + info.remoteVersion;
		this.nodes.updateText.innerHTML = info.details[0][1];
	},
	
	downloadStart: function () {
		this.nodes.downloadStatus.innerHTML = "Starte Download...";
		console.log("Download start");
	},
	
	downloadComplete: function () {
		this.nodes.downloadStatus.innerHTML = "Fertig";
		console.log("DownloadComplete");
	},
	
	downloadProgress: function(info) {
		this.nodes.downloadStatus.innerHTML = info.loaded.toFixed(1).toString() + " MB geladen... ";
	},
	

	_doLogin: function () {
			this._storeUsername();
			var userdata = {
				username: this._iUsername.value,
				password: this._iPassword.value,
				database: this._iDatabase.value

			};
			sandbox.publish("userdata/set", [userdata]);

	},
		
	authenticationStateChange: function (evdata) {
		var stack=this.stack;
		console.dir(evdata, "change auth state");
		switch (evdata.toState) {
		case "waiting":
			stack.selectChild(this.logonRunning);

			break;
		case "invalid":
		case "error":
			var error = new dijit.Dialog({
				title: "Fehler",
				style: "width: 300;"
				});
			error.set("content","Sie konnten nicht angemeldet werden.");
			error.show();
			
			stack.selectChild(this.loginView);
		
			break;
		
		case "ok":
			stack.selectChild(this.syncRunning);
			this.tableNameNode.innerHTML = "...";
			this.sandbox.subscribe("sync/update", dojo.hitch(this, "_tableChanged"));
			this.sandbox.publish ("sync/start");

			break;
		}
	},

	logonSuccessfull: function () {
		this.stack.selectChild(this.logonOk);
		this.sandbox.publish("app/start");
		window.setTimeout( this.closeWindow, 2000);
	},

	closeWindow: function () {
		window.close();
	},

	_tableChanged: function ( mod ) {
		this.tableNameNode.innerHTML = mod.table;
	},

	_storeUsername: function () {
		// Summary:
		// stores the username on disk

		var loginData={
			username: this._iUsername.value,
			database: this._iDatabase.value
		}

		ds.storeObject ( "login/userdata", loginData);
	},

	_loadUsername: function () {
		// Summary:
		// loads the username from disk
		var data = ds.loadObject("login/userdata");
		if ( data ) {
			this._iUsername.set("value", data.username);
			this._iDatabase.set("value", data.database);
		}
	},

	run: function() {
		console.log("MODULE::RUN");
		this.initLoginModule();
		// start the update
		sandbox.publish("request/update");
		
	},
	
	constructor: function ( ) {
		console.log("MODULE::CONSTRUCT");
	}
});

