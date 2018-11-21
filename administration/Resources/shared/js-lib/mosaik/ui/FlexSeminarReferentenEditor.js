dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("mosaik.util.Json");
dojo.require("mosaik.core.helpers");
dojo.require("dijit.Dialog");

dojo.provide("mosaik.ui.FlexSeminarReferentenEditor");

dojo.declare("mosaik.ui.FlexSeminarReferentenEditor", [dijit._Widget,dijit._Templated], {
	templateString:  dojo.cache("templates","flexSeminarReferentenEditor/editor.html"),
	_queryPending: false,
	_checkRunning: false,
	_isHidden: false,
	_options: null,

	constructor: function () {
		
	},

	queryService: function ( options ) {
		// Summary:
		// send the options to the flex component
		// meaning
		//		seminarId: id of the seminar
		//		standortId: id of the standort
		this._queryPending = true;
		this._options = options;
		// send the query
		this._sendQuery();
	},

	_sendQuery: function () {
		
		if ( this._isReady() ) {
			this._options["token"] = sandbox.getUserinfo().auth_token;
			this.flashNode.queryService( this._options );
		}
	},
	
	resize: function (changeSize, resultSize) {
		console.log("mosaik.ui.FlexKaelnder: resize");
		dijit.focus(this.domNode);
	},

	startup: function () {
		this._checkReadyState();
		this._sendQuery();
	},

	_checkReadyState: function () {
		// Summary:
		// polls the flash component to see if its ready
	
		if ( this._isReady() ) {
			console.log ("FlexKalender ready...");
			this._onReady();
		} else if ( this._isHidden ) {
			console.log("FlexKalender hidden suspending ready check");
			return;
		} else {
			console.log ("FlexKalender not ready...");
			setTimeout ( dojo.hitch ( this, this._checkReadyState), 200);
		}

	},

	postCreate: function () {
		
	},
	
	_isReady: function () {
		return  ( typeof( this.flashNode ) !== "undefined" && typeof(this.flashNode.isReady) !== "undefined" && this.flashNode.isReady() );
	},

	_onReady: function () {
		if ( this._queryPending ) {
			this._queryPending = false;
			this._sendQuery();
		} 
		this.onReady();
	},

	onReady: function () {

	},

	hide: function () {
		console.log("============´´´´´´´´´´´´´´´´´´´´´==> Hiding FlexTable");

		this._isHidden = true;
		this.flashNode.setAttribute("hidden", true);
		this.domNode.style.display = "none;";
		this.flashNode.style.display = "none;";
		this.flashNode.setAttribute("height", "1");
	},

	show: function () {
				console.log("============´´´´´´´´´´´´´´´´´´´´´==> Showing FlexTable");

		this._isHidden = false;
		this.flashNode.setAttribute("hidden", false);

		this.domNode.style.display ="block";
		this.flashNode.style.display = "block;";
		this.flashNode.setAttribute("height", "100%");
		this._checkReadyState();
	},

	save: function () {
		if ( this._isReady() ) {
			this.flashNode.save();
		}
	},
});
