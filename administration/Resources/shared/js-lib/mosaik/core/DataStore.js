dojo.provide("mosaik.core.DataStore");
dojo.require("dojo.cache");

dojo.require("dojo.data.ItemFileReadStore");

dojo.declare("mosaik.core.DataStore" , null, {
	// Summary:
	// Manage global operations related to storing and loading data
	_app: null,
	_knownObjects: {},

	_currentSyncLength: 0,
	_updateRunning: false,
	_syncQueue: null,
	_needSync: null,
	_serviceCache: {},
	_objectCount: 0, 
	_removeVersionInfo: {},
	_smdCache: {},
	_objectCache: {},
	
	constructor: function ( app ) {
		//Summary:
		// app => global application 
		this._app=app;
		this._needSync = [];
		this._syncQueue = [];
	},
	
	getDBPrefix: function () {
		return this._app.identity.database.replace("https://", "").replace("http://","").replace("/","_").replace(".","_");
	},

	getSmd: function ( service ) {
		var self =this;
		var smdUrl = this._app.config.smd[service];
		var separator = "?";
		if ( smdUrl.search("\\?") !== -1) {
			separator = "&";
		} 
		var smdUrl = this._app.identity.database + smdUrl + separator + "token=" + this._app.getUserinfo().auth_token;
	
		if ( typeof ( self._smdCache[smdUrl]) === "undefined") {
			dojo.xhrGet({
				url: smdUrl,
				handleAs: "json",
				load: function (data) {
					self._smdCache[smdUrl] = data;
				}, 
				error: function (error) {
					console.error("==[Cache]:" + smdUrl  + " is faulty ...");
				}
			});
			console.log("==[Cache]: Uncached SMD: " + smdUrl)
			return smdUrl;
		} else {
			console.log("==[Cache]: Cached SMD: " + smdUrl);
			return self._smdCache[smdUrl];
			
		}
	},

	getServiceURL: function ( service ) {
		var url =  this._app.identity.database + this._app.config.serviceMap[service];
	//	console.log ( "Service " + service + " has url: " + url);

		return url;
	},

	setServiceBaseURL: function ( baseUrl ) {
		this._app.config.serviceMap.serviceBase = baseUrl;
	},

	// EASY PERSISTENCE - WITH A VERSION NUMBER
	storeObject: function (key, object, version) {
		var prefix = this.getDBPrefix();
		// Summary:
		// stores the object on disk
		// uses key to indetify the object
		key = key.replace("/","_");
		var value = JSON.stringify(object);
		
		if ( typeof(version) === "undefined") {
			version = 0;
		}
		
		console.debug ("Store Object: "+ prefix + ":" + key +" with Version " + version);
		//console.dir(object);
		this._objectCache[key] = object;
		var objStream = new air.FileStream();
		var dateStream = new air.FileStream();
		var revStream = new air.FileStream();
		
	

		try {
			var objFile = new air.File("app-storage:/" +prefix + "_" + key + ".json");
			var dateFile = new air.File("app-storage:/"+prefix + "_" + key + ".date");
			var revFile = new air.File("app-storage:/"+prefix + "_" + key + ".rev");
			
			if ( objFile.exists) {
				console.log("Unlinking objFile:" + objFile.nativePath);
				objFile.deleteFile();
			}
			
			if ( dateFile.exists) {
				console.log("Unlinking dateFile:" + dateFile.nativePath);
				dateFile.deleteFile();
			}
			
			if ( revFile.exists) {
				console.log("Unlinking revFile:"+revFile.nativePath);
				revFile.deleteFile();
			}
			
			objStream.open ( objFile, air.FileMode.WRITE);
			objStream.writeMultiByte( value , air.File.systemCharset );

			var now=new Date();
	
			dateStream.open ( dateFile, air.FileMode.WRITE);
			dateStream.writeMultiByte( now.getTime().toString() , air.File.systemCharset );

			revStream.open ( revFile, air.FileMode.WRITE);
			revStream.writeMultiByte( version , air.File.systemCharset );
			
		} catch ( error ) {
			console.error("Cant write "+prefix + "_" + key+" to disk");
			console.dir(error);
		} finally {
			objStream.close();
			dateStream.close();
			revStream.close();
		}
	},

	checkObjectAge: function ( key, maxAge ) {
		// Summary:
		// checks if an object is older than maxAge in milliseconds or not
		// returns true if its younger
		// returns false if its older
		var prefix = this.getDBPrefix();

		key = key.replace("/","_");

		try {
			var file = air.File.applicationStorageDirectory.resolvePath(prefix + "_" + key + ".date");
			var stream = new air.FileStream();
			stream.open ( file, air.FileMode.READ);
			var time = parseInt (stream.readMultiByte ( stream.bytesAvailable, air.File.systemCharset));
			var now = new Date();
			stream.close();
			if ( now.getTime() + maxAge > time ) {
				return false;
			} else {
				return true;
			}

		} catch ( error ) {
			console.error("Cant read date "+prefix + "_" + key+".date from disk");
			console.dir(error);
			return false;
		}
	},

	getVersion: function ( key ) {
		key = key.replace("/","_");
		var prefix = this.getDBPrefix();
		
		try {
			var file = air.File.applicationStorageDirectory.resolvePath(prefix + "_" + key + ".rev");
			var stream = new air.FileStream();
			stream.open ( file, air.FileMode.READ);
			var data = stream.readMultiByte ( stream.bytesAvailable, air.File.systemCharset);
			stream.close();
			return data;
		} catch ( error ){
			console.error("DataStore::getVersion: cant read Version info for" + prefix + "_" + key);
			console.dir ( error );
		}
		return 0;
	},

	loadArray: function ( key ) {
		// Summary:
		// loads the object idenfyed by key from disk
		var prefix = this.getDBPrefix();

		key = key.replace("/","_");

		if ( typeof ( this._objectCache[key] )!=="undefined") {
			var ret = [];
			for ( var i in this._objectCache[key] ) {
				ret.push ( this._objectCache[key][i] );
			}
			
			return ret;
		}
		
		try {
			var file = air.File.applicationStorageDirectory.resolvePath(prefix + "_" + key + ".json");
			var stream = new air.FileStream();
			stream.open ( file, air.FileMode.READ);
			var data = stream.readMultiByte ( stream.bytesAvailable, air.File.systemCharset);
			stream.close();
			var jsonData = JSON.parse(data);
			var ret = [];
			
			for ( var i in jsonData ) {
				ret.push ( jsonData[i]);
			}

			return ret;
		} catch ( error ){
			console.error("DataStore::loadObject: cant read " + prefix + "_" + key);
			console.dir ( error );
		}
		return null;
	},


	loadObject: function ( key ) {
		// Summary:
		// loads the object idenfyed by key from disk
		key = key.replace("/","_");
		var prefix = this.getDBPrefix();

		if ( typeof ( this._objectCache[key] )!=="undefined") {
			return this._objectCache[key];
		}

		try {
			var file = air.File.applicationStorageDirectory.resolvePath(prefix + "_" + key + ".json");
			if ( !file.exists ) {
				return {};
			}
				
			
			var stream = new air.FileStream();
			stream.open ( file, air.FileMode.READ);
			var data = stream.readMultiByte ( stream.bytesAvailable, air.File.systemCharset);
			stream.close();
			return JSON.parse(data);
		} catch ( error ){
			console.info("DataStore::loadObject: cant read " + prefix + "_" + key);
			console.dir ( error );
		}
		return null;
	},
	
	clear: function () {
		console.log("DataStore::clear");
		this._knownObjects = [];
		this._objectCount = 0;
	},

	registerObject: function ( objConfig ) {
		this._objectCount ++;
		
		var istore = dojo.mixin ( {
			name: "#UNDEFINED#",
			autosync: false,
			items: null,
			localVersion: 0,
			remoteVersion: 0,
			service: null,
			serviceName: "database/table",
			methodName: "fetchAll",
			versioned: true
		}, objConfig);

		if ( istore.versioned ) {
			istore.localVersion = this.getVersion(istore.name);
		}
		console.log("( " +this._objectCount +" ) DataStore::registerObject => " + istore.name + "localVersion: " + istore.localVersion);

		this._knownObjects[istore.name] = istore;
	},

	startSync: function () {
		// Summary:
		// walks through registred objects
		// and checks the remote version against the local version
		// runs update if they differ
		var _self=this;
		console.log("DataStore::startSync => Beginning sync");
	

		var obj;
		var _knownObjects = this._knownObjects;

		//rewind this
		this._currentSyncLength = 0;
		
		//get 
		var versionService = new dojox.rpc.Service ( this.getSmd( "database/table" ) );
		versionService.getVersions().addCallback(function (data) {
			console.log("===> VERSION INFO RECIEVED");
			
			_self._remoteVersionInfo = data;
			
			for ( obj in _knownObjects ) {
				if ( _knownObjects[obj].service === null ) {
					if ( typeof ( _self._serviceCache[_knownObjects[obj].serviceName]) === "undefined" ) {
						_self._serviceCache[_knownObjects[obj].serviceName] = new dojox.rpc.Service ( _self.getSmd( _knownObjects[obj].serviceName) );
					}
					_knownObjects[obj].service =  _self._serviceCache[_knownObjects[obj].serviceName];
				}

				if ( _knownObjects[obj].autosync === false) {
					continue;
				}
				_self._currentSyncLength ++;
			}

			for ( obj in _knownObjects ) {
				var ko =_knownObjects[obj];
				if ( ko.autosync === false) {
					continue;
				}

				if ( ko.versioned && typeof (_self._remoteVersionInfo[obj]) !== "undefined") {
					// if the remote object ist versioned
					// and we allready have cached info about its version
					_self._updateVersionInfo(_self._remoteVersionInfo[obj], null);
				} else if ( ko.versioned ) {
					// if the remote object is versioned
					// we ask for the version
					ko.service.getVersion ( obj )
					.addCallback ( dojo.hitch(_self, _self._updateVersionInfo) )
					.addErrback (dojo.hitch ( _self, function ( data ) {
						console.log ("DataStore::startSync => Error while SyncModel");
						console.log ( data );
					}));
				} else {
					// if its not versioned we push it straigt to
					// the update process
					_self._syncQueue.push ( ko );
				//	if ( _self._updateRunning === false) {
				//		window.setTimeout ( dojo.hitch ( _self, _self._syncNext), 200);
				//	}
				}
			}
			
			if ( _self._updateRunning === false) {
				console.log("DataStore [S] ====> Start Sync");
				window.setTimeout ( dojo.hitch ( _self, _self._syncNext), 200);
			} else {
				console.log("DataStore [S] ====> Not Starting Sync its locked....");
			}
		});
		
		
	},

	_updateVersionInfo: function ( data , request) {
		//Summary:
		// callback for version check
		var tableName = data.table_name;
		var ko = this._knownObjects[tableName];

		//console.log("DataStore::_updateVersionInfo => Got VersionInfo for " + tableName + " it's version: " + data.version);

		ko.remoteVersion = data.version;

		//	console.dir(ko);
		
		if ( ko.localVersion != ko.remoteVersion ) {
			console.log("DataStore [R] ===> Table " + tableName + " is out of date" );
			this._syncQueue.push ( ko );
		//	if ( this._updateRunning === false) {
		//		window.setTimeout ( dojo.hitch ( this, this._syncNext), 200);
		//	}
		} else {
			this._currentSyncLength --;

			ko.items = this.loadObject( ko.name );

			this._app.publish("sync/update", [ {
				table: ko.name,
				length: this._currentSyncLength,
				method: ko.methodName,
				name: ko.name,
			}] );
		}
	},

	_syncNext: function () {
		var co;
		
		if ( this._updateRunning ) return;
				
		if ( this._syncQueue.length == 0 ) {
			this.finishSync();
			return;
		}

		// lock sync next
		this._updateRunning = true;
		this._currentSyncLength --;
		
		co = this._syncQueue.pop();

		this._app.publish("sync/update", [ {
			method: co.methodName,
			name: co.name,
			table: co.name,
			length: this._currentSyncLength
		}] );

		
		co.service[co.methodName](co.name).addCallback( dojo.hitch ( this, "_updateData" ) )
		.addErrback( dojo.hitch ( this, function (data) {
			console.error ("cant sync " + co.name);
            console.error("Data: " + data);

            this._updateRunning = false;
            window.setTimeout ( dojo.hitch ( this, "_syncNext"), 200);

        }));
	},

	_updateData: function (data) {
        console.info ("Data recived...");
        try {
		    console.log("DataStore [U] ====> Update data for "+data.table_name );
		    var ko = 	this._knownObjects[data.table_name];
		    ko.items = data.data;
		    ko.localVersion =ko.remoteVersion;
		
		    this.storeObject(ko.name, ko.items, ko.localVersion);

    		this._updateRunning = false;

        } catch (e) {
            console.error("Cant interpret Data: " + e);
        }
        window.setTimeout ( dojo.hitch ( this, "_syncNext"), 100);
    },

	finishSync: function () {
		this._updateRunning = false;
		this._currentSyncLength = -1;
		console.log("DataStore [D] =====> SYNC DONE");
		this._app.publish ("sync/done");

	}
});
	
