/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

// declare global models

dojo.provide("appcore.GlobalModels");

dojo.require("appcore.ModelSynchronizer");

dojo.declare ("appcore.GlobalModels",null, {
	_datastore: null,
	_app: null,
	
	models: {},
	services: {},
	sandbox: null,
	moduleName: "GlobalModels",
	moduleVersion: 20,
	cache: {},
	_dbOpen: false,
	_toSync: [],
	_updateRunning: false,
	_syncCount: -1,
	_currentSyncContext: null,

	constructor: function ( app ) {
		this._app = app;
		this._datastore = app.datastore;
	},

	getModel: function (name) {
		return this.models[name];
	},

	createSeminarModels: function () {

	},

	createStartseiteModels: function () { // FIXME: in extra datei auslagern... (pro modul models)
		this.createModel ( "startseite/statistiken",  {
			columns: {
				key: "text",
				buchungen: "number",
				storno: "number",
				umbuchungen: "number"
			}
		});
		this.createModel ( "startseite/termine", {
			columns: {
				id: "number",
				standort_name: "text",
				kursnr: "text",
				datum: "text"
			}
		});

		this.createModel ("startseite/todo", {
			columns: {
				id: "number",
				person_id: "number",
				buchung_id: "number",
				seminar_id: "number",
				notiz: "text",
				firma_id: "number",
				termin_id: "number",
				erstellt: "text",
				deleted_at: "text",
				kategorie: "text",
				betreff: "text",
				prioritaet: "number",
				faelligkeit: "text",
				fortschritt: "number",
				status: "number",
				kategorie_id: "number",
				zugeordnet_id: "number",
				status_id: "number",
				erstellt_von_id: "number",
				erledigt: "text",
				rubrik_id: "number"
			}
		});
	},

	createModels: function () {
		this._openDb();
		// create the rpc bridge
		this.service = this.sandbox.getRpcService("database/table");

		// create model classes
		this.versionInfo = new JazzRecord.Model ( {
			table: "gm_info",
			columns: {
				id: "number",
				key: "text",
				version: "text"
			}
		});

		this.tableInfo = new JazzRecord.Model ( {
			table: "table_info",
			columns: {
				id: "number",
				tableName: "text",
				version: "text"
			}
		});
		
		// create additional models
		this.createGenericModel("KontaktKategorie");
		this.createGenericModel("XAnrede");
		this.createGenericModel("XBranche");
		this.createGenericModel("XBundesland");
		this.createGenericModel("XCombobox");
		this.createGenericModel("XGrad");
		this.createGenericModel("XGroup");
		this.createGenericModel("XLand");
		this.createGenericModel("XTodoKategorie");
		this.createGenericModel("XUser");
		this.createGenericModel("XTodoRubrik");
		this.createGenericModel("XTodoStatus");
		this.createGenericModel("XTaetigkeitsbereich");
		this.createGenericModel("Standort");
		this.createGenericModel("ViewStandortKoordinaten");
		this.createGenericModel("SeminarFreigabestatus");
		this.createGenericModel("SeminarArtRubrik");
		this.createGenericModel("SeminarArt");

		this.createStartseiteModels();
		this.createSeminarModels();

		// create the database
		JazzRecord.migrate();

		// info setzen
		var info = this.versionInfo.findBy ("key", this.moduleName);

		if ( info ) {
			console.log ("DB =>  " + this.moduleName + " Version: " + this.moduleVersion);
			if ( info.version != this.moduleVersion) {
				console.log ("DB DIFFERS =>  " + this.moduleName + " Version: " + this.moduleVersion + " != " + info.version);
				JazzRecord.migrate({
					refresh: true
				});

				info = this.versionInfo.create( {
					key: this.moduleName,
					version: this.moduleVersion
				});
			}
		} else {
			console.log ("DB =>  " + this.moduleName + " Version: " + this.moduleVersion + " NEW");

			info = this.versionInfo.create( {
				key: this.moduleName,
				version: this.moduleVersion
			});

		}
		info = this.versionInfo.findBy ("key", this.moduleName);
	},

	createGenericModel: function (prefix) {
		console.log("===> appcore.GlobalModels.createXModel( "+prefix+" )");
		var tableName = prefix;

		this.models[prefix] = new JazzRecord.Model ( {
			table: tableName,
			columns: {
				id: "text",
				name: "text",
				json_data: "text",
				fk1: "text",
				fk2: "text",
				fk3: "text",
				info1: "text",
				info2: "text",
				info3: "text"
			}
		});
		this.models[prefix].syncTable = true;
	},

	createModel: function (tableName,data) {
		data.table = tableName.replace("/","_");
		data.version = 0;

		this.models[tableName] =new JazzRecord.Model (
			data
			);
		this.models[tableName].syncModel = false;
		return this.models[tableName];
	},

	syncModels: function () {
		console.log("===> appcore.GlobalModels.syncModels()");
		var mod; // temp mod index
		
		// count models that need update
		this._syncCount = 0;
		for ( mod in this.models ) {
			if ( this.models[mod].syncModel === false) {
				continue;
			}
			this._syncCount ++;
		}

		for ( mod in this.models ) {
			if ( this.models[mod].syncModel === false) {
				continue;
			}
		
			var sync = new appcore.ModelSynchronizer({
				model: this.models[mod],
				prefix: mod
			});

			var tableVersion = this.tableInfo.findBy("tableName",mod);
		
			if ( tableVersion ) {
				sync.localVersion = parseInt( tableVersion.version );
			} else {
				console.log ("CREATE NEW TABLE INFO");
				tableVersion = this.tableInfo.create({
					tableName: mod,
					version: "0"
				});
				sync.localVersion = "0";
			}

			var context = {
				prefix: mod,
				sync: sync,
				service: this.service,
				tableVersion: tableVersion,
				mngr: this,
				model: this.models[mod], 
				needSync: false,
				versionChecked: false,
				sandbox: this.sandbox
			}
			// get the remote Version
			this.service.getVersion ( mod ).addCallback ( dojo.hitch (context, function( data) {
				this.sync.remoteVersion = data.version;
				this.needSync = this.sync.needSync();
				this.versionChecked = true;

				if ( this.needSync ) {
					console.log ("Model: " + this.prefix + " need sync");
					// push sync
					this.mngr._toSync.push ( this );
					window.setTimeout ( dojo.hitch ( this.mngr, "syncNext"), 200);
				} else {
					console.log ("Model:" + this.prefix  + " is up to date");
					this.mngr._syncCount--;

					if ( this.mngr._syncCount == 0 ) {
						console.log("========> SYNC DONE <========");
						this.sandbox.publish("sync/done");
					}
				}
			})).addErrback (dojo.hitch ( context, function ( data ) {
				var mod=this.prefix;
				console.log ("==========!!!>> Error on " + mod);
				console.log ( data );
			}));
		}
		
	},

	syncNext: function () {
		console.log ("==> SYNC NEXT");
		console.log ("===> TO SYNC: " + this._toSync.length.toString());
		console.log ("===> RUNNING: " + this._updateRunning.toString());
		
		if ( this._updateRunning ) return;
		if ( this._syncCount == -1 ) return;
		if ( this._syncCount == 0 ) {
			console.log("========> SYNC DONE <========");
			this.sandbox.publish("sync/done");
		}
		if ( this._toSync.length == 0 ) return;
		
		// lock sync next
		this._updateRunning = true;

		// get context
		var ctx = this._toSync.pop();
		this._currentSyncContext = ctx

		// is context recent?
		if ( ! ctx.versionChecked ) {
			this._toSync.push ( ctx );
			this._updateRunning = false;
			window.setTimeout ( dojo.hitch ( this, "syncNext"), 200);
		}

		// decerese Sync count
		this._syncCount --; // TODO wenn syncCount == 0 dann ists erledigt
		// let others know what we are doing
		this.sandbox.publish("sync/update", [ {
			table: ctx.prefix
			}] );

		// start fetching
		this.service.fetchAll(ctx.prefix).addCallback ( dojo.hitch ( ctx, function (data) {
			this.tableVersion.version = this.sync.remoteVersion;
			this.tableVersion.save();
			
			this.sync.setData ( data );
			this.sync.syncData().addCallback( dojo.hitch ( this, function () {
				// done fetching
				console.log ("==>SYNC NEXT");
				this.mngr._updateRunning = false;
				window.setTimeout ( dojo.hitch ( this.mngr, "syncNext"), 200);
			}));
		}) ).addErrback (dojo.hitch (this.ctx, function (data) {
			// on error
			var mod=this.prefix;

			console.log ("==========!!!>> Error on " + mod);
			console.log ( data );
			window.setTimeout ( dojo.hitch ( this, "syncNext"), 200);
		}));
	},

	getOptionsObj: function ( table ) {
		console.log ( "Table: " + table);
		if ( typeof( this.models[table] ) === "undefined" ) {
			throw new TypeError("Table: " + table + " unknown");
		}

		if ( typeof ( this.cache[table] ) !== "undefined") {
			return this.cache[table];
		}

		var data = this.models[table].all();
		
		for ( var i=0; i < data.length; i++) {
			data[i].label = data[i].name.toString();
			data[i].value = data[i].id.toString();
		}

		this.cache[table] = data;
		return data;
	}
});
