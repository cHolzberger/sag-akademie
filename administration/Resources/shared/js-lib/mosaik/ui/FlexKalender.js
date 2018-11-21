dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("mosaik.util.Json");
dojo.require("mosaik.core.helpers");
dojo.provide("mosaik.ui.FlexKalender");

dojo.declare("mosaik.ui.FlexKalender", [dijit._Widget, dijit._Templated], {
	_dataStoreURL : null,
	_currentYear : "2011",
	templateString : dojo.cache("templates", "flexKalender/FlexKalender.html"),
	_serviceUrl : null,
	_queryPending : false,
	_checkRunning : false,
	_isHidden : false,

	_oldHeight : "",
	_oldWidth : "",

	constructor : function() {

	},

	setURL : function(url) {
		console.log("setting url: " + url);
		this._queryPending = true;
		this._dataStoreURL = url;
		this._sendQuery();
	},

	setYear : function(year) {
		console.log("setting year: " + year);
		this._queryPending = true;
		this._currentYear = year;
		this._sendQuery();
	},

	_sendQuery : function() {
		if (this._isReady()) {
			this.flashNode.setAuthToken(sandbox.getUserinfo().auth_token);
			this.flashNode.setYear(this._currentYear);
			this.flashNode.setURL(this._dataStoreURL);
		}
	},

	save : function() {
		if (this._isReady()) {
			this.flashNode.save();
		}
	},
	
	reload: function() {
		if (this._isReady()) {
			this.flashNode.reload();
		}
	},

	resize : function(changeSize, resultSize) {
		console.log("mosaik.ui.FlexKaelnder: resize");
		dijit.focus(this.domNode);
	},

	startup : function() {
		this._checkReadyState();
		this._sendQuery();
	},

	_checkReadyState : function() {
		// Summary:
		// polls the flash component to see if its ready

		if (this._isReady()) {
			console.log("FlexKalender ready...");
			this._onReady();
		} else if (this._isHidden) {
			console.log("FlexKalender hidden suspending ready check");
			return;
		} else {
			console.log("FlexKalender not ready...");
			setTimeout(dojo.hitch(this, this._checkReadyState), 1000);
		}

	},

	gotoToday : function() {
		this.flashNode.gotoToday();
	},

	toggleFullscreen : function() {
		this.flashNode.toggleFullscreen();
	},

	postCreate : function() {

	},

	_isReady : function() {
		return ( typeof (this.flashNode ) !== "undefined" && typeof (this.flashNode.isReady) !== "undefined" && this.flashNode.isReady() );
	},

	_onReady : function() {


		if (this._queryPending) {
			// quick hack
			this.flashNode.setAuthToken(sandbox.getUserinfo().auth_token);
			console.dir(sandbox.getUserinfo());

			this._queryPending = false;
			this._sendQuery();
		}
		this._oldHeight = this.domNode.style.height;
		this._oldWidth = this.domNode.style.width;
		this.onReady();
	},

	onReady : function() {

	},


	hide : function() {
		if (this._isHidden)
			return;
		//this.domNode.style.display = "none";

		this._oldHeight = this.domNode.style.height;
		this._oldWidth = this.domNode.style.width;
        //this.domNode.style.display="none";
		this.domNode.style.height = "25px";
		this.domNode.style.width = "25px";

	},

	show : function() {
		this._isHidden = false;
		this.domNode.style.height = this._oldHeight;
		this.domNode.style.width = this._oldWidth;
        //this.domNode.style.display="block";
       // this.reload();
        //this.startup();
        // FIXME: hier die optionen erneut setzen!!!
	}
});
