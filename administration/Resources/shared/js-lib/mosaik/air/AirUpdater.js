/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */


dojo.provide("mosaik.air.AirUpdater");
dojo.declare ( "mosaik.air.AirUpdater", null, {
	app: null,
	localVersion: 0,
	remoteVersion: 0,
	updater: null,
	
	constructor: function () {
		console.log("AirUpdater::constructor");
		this.updater = new window.runtime.air.update.ApplicationUpdater();
	},
	
	getAppVersion: function () {
		console.log("AirUpdater::getAppVersion");
		
		var xmlString = air.NativeApplication.nativeApplication.applicationDescriptor;
		var appXml = new DOMParser();
		var xmlObject = appXml.parseFromString(xmlString, "text/xml");
		var root = xmlObject.getElementsByTagName('application')[0];
		var vNode = root.getElementsByTagName("versionLabel")[0];
		

		var lblAppVersion = vNode.firstChild.data;
		this.localVersion = lblAppVersion;
		
		this.app.publish("app/version", [{
			localVersion: this.localVersion
			}]);
		
		return lblAppVersion;
	},
	
	beginUpdate: function () {
		console.log("AirUpdater::initUpdates");
		this.getAppVersion();
		this.updater.configurationFile = new air.File("app:/config/update-config.xml");
		this.connectEventListener();
		this.updater.initialize();
	},
	
	_celConnected: false,
	connectEventListener: function () {
		if ( this._celConnected ) return;
		this._celConnected = true;
		
		this.updater.addEventListener(air.ErrorEvent.ERROR, dojo.hitch( this, "onUpdateError"));
		this.updater.addEventListener(air.StatusUpdateErrorEvent.UPDATE_ERROR, dojo.hitch( this, "onUpdateError"));
		this.updater.addEventListener(air.DownloadErrorEvent.DOWNLOAD_ERROR, dojo.hitch( this, "onDownloadError"));	
		this.updater.addEventListener(air.UpdateEvent.DOWNLOAD_START, dojo.hitch(this, "onDownloadStart"));
		this.updater.addEventListener(air.UpdateEvent.DOWNLOAD_COMPLETE, dojo.hitch(this, "onDownloadComplete"));
		this.updater.addEventListener(air.UpdateEvent.INITIALIZED, dojo.hitch ( this, "checkForUpdates"));
		this.updater.addEventListener(air.StatusUpdateEvent.UPDATE_STATUS, dojo.hitch ( this, "onUpdateStatus"));
		this.updater.addEventListener(air.StatusFileUpdateErrorEvent.FILE_UPDATE_ERROR, dojo.hitch(this, "onFileUpdateError"));
		this.updater.addEventListener(air.ProgressEvent.PROGRESS, dojo.hitch(this, "onProgress"));
	},
	
	
	// *** Event Handler
	onDownloadStart	: function ( e ) {
		this.app.publish("update/download/start");
		console.log("download start");
	},
	
	onDownloadComplete: function ( e ) {
		this.app.publish("update/download/complete");
		console.log(e);
	},
	onProgress: function ( e ) {
		this.app.publish("update/progress", [{loaded: e.bytesLoaded, total: e.bytesTotal}]);
	},
	checkForUpdates: function( e ) {
		console.log("Check for Updates...");
		this.updater.checkNow();  
	},
	
	
	
	onUpdateStatus: function (e) {
		this.remoteVersion = e.version;
		if ( e.available ) {
			this.app.publish("update/run", [{remoteVersion: e.version, details: e.details}]);
		} else {
			this.app.publish("update/done", [{}]);
			e.preventDefault();
		}
	},
	
	// *** ERROR HANDLER
	onUpdateError: function (e) {
		this.updater.cancelUpdate();

		
		this.app.publish("update/error");
		console.info("updateError");
		console.error(e);
	},
	
	onDownloadError: function (e) {
		if ( e.errorID == 16826) { // no new version available
			this.app.publish("update/done", [{}]);
			e.preventDefault();
		} else {
			this.updater.cancelUpdate();

			this.app.publish("update/error", {errorID: e.errorID});
			console.info("downloadError");
			console.error(e);
		}
	},
	
	onFileUpdateError: function (e) {
		console.info("FileUpdateError");
		console.error(e);
	}
});