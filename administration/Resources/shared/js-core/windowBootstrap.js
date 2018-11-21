/*
* Use without written License forbidden
* Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
*/

// this file connects functions
// and configures dojo,
// then it loads dojo and bootstraps the module

window.onerror=function(message,url,lineno) {
	alert("error");
}

// we wont let the user select text
document.onselectstart = function(e) {
	// Summary:
	// do not let the user select text
	return false;
};
var iface = {};
var forwardPublish = null;
// publis proxy
var _known = {};

iface.injectScript = function(script) {
	//Summary:
	// injects a script into the current context
	console.log("injecting: " + script);
	if( typeof (_known[script]) === "undefined") {
		_known[script] = true;

		document.write("<script src='" + script + "' type='text/javascript' async=true><\/script>");
	}
}

iface.publishFromApp = function(name, data) {
	// Summary:
	// used to send signals from the app

	// this is really strange, childSandboxBridge is reset on each forwarded call

	if( typeof (dojo) === "undefined") {
		console.log("dojo not ready... ignored signal: " + name);
		return;
	} else {
		console.log("windowBootstrap: got signal from APP: " + name);
	}


    if ( dojo.isArray(data) === true) {
        console.log("Data is Native-Array");
        dojo.publish(name, data);

    } else if (dojo.isObject(data) === true ){
        console.log("Data is Native-Object");
        dojo.publish(name, data);
    } else if( typeof (data) !== "undefined") {
            var _p = JSON.parse(data);

		    if(dojo.isArray(_p)) {
    			console.log("isArray");
    			dojo.publish(name, _p);
	    	} else if(dojo.isObject(_p)) {
    			console.log("isObject");
    			dojo.publish(name, [_p]);
    		}
	} else {
        console.log("Data is Undefined");
        dojo.publish(name);
	}

	if( typeof (forwardPublish ) !== "undefined " && dojo.isFunction(forwardPublish)) {
		console.log("Has Forward Publish!");
		forwardPublish(name, data);
	}
}

iface.suspend = function(options) {
	// Summary:
	// called when the app requests this frame to suspend
	// eg. do not respond to global messages
	// hide flash
	// stop perfromance intensive actions

	console.log("Suspending module...");
	return currentModule.suspend(options);
}

iface.resume = function(options) {
	// Summary:
	// this module was suspended before and now it should run again
	// show flash nodes, restore options, update display and so on

	console.log("Resumiong module...");

	return currentModule.resume(options);
}

iface.isRunning = function() {
	// Summary:
	// return status of this frame/window
	// is it suspended or not?
    if ( currentModule ) {
	    return currentModule.isRunning();
    } else {
        return false;
    }
}

iface.destroy = function(options) {
	// Summary:
	// we are going to be destoryed soon
	// remove event listeners, clean up environment
	// send signals and so on
    return currentModule ? currentModule.destroy(options) : null;
}

window.childSandboxBridge = iface;
childSandboxBridge = iface;

function init() {

	window.maximize = parentSandboxBridge.window_maximize;
	window.close = parentSandboxBridge.window_close;
	window.setTitle = function ( t) {
		t = t + "                             (Version: " + djConfig.appVersion + ")"; 
		parentSandboxBridge.window_setTitle(t);
	}
	djConfig = parentSandboxBridge.djConfig;
	console = {
		// Summary: bind to app console
		log : parentSandboxBridge.console_log,
		dir : parentSandboxBridge.console_dir,
		info : parentSandboxBridge.console_info,
		debug : parentSandboxBridge.console_debug
	};
	djConfig.addOnLoad = function() {
		dojo.require("mosaik.core.moduleBootstrap");
	};
	iface.injectScript(djConfig.dojoURL);
	app = {
		// Summary: bind to app
		generateId : parentSandboxBridge.app_generateId,
		spawnShell : parentSandboxBridge.app_spawnShell,

		exit : parentSandboxBridge.app_exit,
		setVar : parentSandboxBridge.app_setVar,
		getVar : parentSandboxBridge.app_getVar,
		publish : parentSandboxBridge.app_publish,
		moduleReady : parentSandboxBridge.app_moduleReady,
		getUserinfo : parentSandboxBridge.app_getUserinfo,
		bridgeReady : parentSandboxBridge.app_bridgeReady,
		navigateToUrl : parentSandboxBridge.app_navigateToUrl,
		exportToFile : parentSandboxBridge.app_exportToFile,
		uploadFile : parentSandboxBridge.app_uploadFile,
		checkForUpdates : parentSandboxBridge.app_checkForUpdates,
		openPdf : parentSandboxBridge.app_openPdf,
		setUserVars : parentSandboxBridge.app_setUserVars,
		getUserVars : parentSandboxBridge.app_getUserVars,
		getUserVarsVersion : parentSandboxBridge.app_getUserVarsVersion,
		reportError: parentSandboxBridge.app_reportError,
		contextMenu : {
			show : parentSandboxBridge.app_contextMenu_show,
			hide : parentSandboxBridge.app_contextMenu_hide,
			clear : parentSandboxBridge.app_contextMenu_clear,
			addItems : parentSandboxBridge.app_contextMenu_addItems
		}
	};
	ds = {
		// Summary: bind to global datastore
		setServiceBaseURL : parentSandboxBridge.ds_setServiceBaseURL,
		getSmd : parentSandboxBridge.ds_getSmd,
		getServiceURL : parentSandboxBridge.ds_getServiceURL,
		loadObject : parentSandboxBridge.ds_loadObject,
		loadArray : parentSandboxBridge.ds_loadArray,
		storeObject : parentSandboxBridge.ds_storeObject
	};
	
	
	

}

/*
 djConfig = {
 // Summary: configure this windows dojo
 afterOnLoad : true,
 isDebug: true,
 parseOnLoad: false,
 locale: "de-de",
 extraLocale: ["en-us"],
 require: [
 "dojo.custom_sag",
 "mosaik.core.helpers",
 "mosaik.core.Sandbox",
 "mosaik.core.Module"
 ],
 airConfig:{
 hasStorage: false,
 terminal: false,
 showTimestamp: false,
 showSender: false
 },
 modulePaths: {
 dojo: "/app/shared/dojo/dojo",
 dijit: "/app/shared/dojo/dijit",
 dojox: "/app/shared/dojo/dojox",
 mosaik: "/app/shared/js-lib/mosaik",
 dair: "/app/shared/dojo/dair",
 module: "/app/modules",
 templates: "/app/widgets",
 appcore: "/app/shared/js-core"
 },
 addOnLoad : function() {
 // check for bridgeReady function

 //		dojo.require("mosaik.core.helpers");
 //	dojo.addOnLoad( function () {
 dojo.require("mosaik.core.moduleBootstrap");
 //});

 }
 };*/
function onModuleException() {
	alert("Exception");
}

init();

if( typeof (window.bridgeReady ) !== "undefined") {
	window.bridgeReady();
}
app.bridgeReady();
