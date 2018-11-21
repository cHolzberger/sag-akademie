/**
 * module loader used to bootstrap the applikation
 * either as an iframe or as an whole window
 */
dojo.provide("mosaik.core.ModuleLoader");
dojo.require("mosaik.util.Mailto");

dojo.declare("mosaik.core.ModuleLoader",null, {
	prefix: "app://",
	
	_scriptStack: [],
	_importRunning: false,
	_loadedFiles: {},
	context: document.body,
	
	constructor: function (vars) {
		dojo.mixin(this,vars);
	},

	_importScript: function (fn) {
		this._scriptStack.push(fn);
	},

	importScript: function (script) {
		this._importScript(this.prefix + script);
	},

	runScript: function (cb) {
		console.log("Appending callback...");
		this._importScript(cb);
	},
	
	_importNext: function () {
		if (this._scriptStack.length == 0) {
			this._importRunning = false;
			return;
		} else if (this._importRunning) {
			return;
		}
		this._importRunning = true;
	
		var fn = this._scriptStack.shift();
		if (typeof(fn) == "string" && this._loadedFiles[fn] == true) {
			this._importRunning = false;
			this._importNext();
		} else if (typeof(fn) == "string") {
			console.info("Doing import: <b>" + fn + "</b>");
			var scriptTag = document.createElement("script");
			scriptTag.setAttribute("type", "text/javascript");
			scriptTag.setAttribute("src", fn);
			this._loadedFiles[fn] = true;
	
			scriptTag.onload = dojo.hitch ( this, function () {
				this._importRunning = false;
				this._importNext();
			});
			
			scriptTag.onerror = function (e) {
				console.warn ( "ERROR: " + e.message);

				if ( confirm("Es ist ein Fehler aufgetreten!\nWollen Sie den Fehlerreport an die Entwickler melden?") ) {
					var mail  = new mosaik.util.Mailto();
					
					mail.addTo("ch@mosaik-software.de");
					mail.addTo("info@samirschwenker.de");
					
					mail.setSubject("Fehler im Admin: ");
					mail.setBody("Kommentar: \n\nFehlerbericht:\n------------------\n" + e.message);

					sandbox.navigateToUrl(mail.toString());
				}
			};
	
			document.body.appendChild(scriptTag);
	
		} else if (typeof(fn) == "function") {
			console.info("Running callback: <b>" + fn + "</b>");
			fn();
			this._importRunning = false;
			this._importNext();
		}
	
	
	}, 

	run: function () {
		this._importNext();
	}
});







