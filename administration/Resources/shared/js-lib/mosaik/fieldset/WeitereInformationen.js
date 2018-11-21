dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("mosaik.util.Json");
dojo.require("mosaik.core.helpers");


dojo.provide("mosaik.fieldset.WeitereInformationen");


dojo.declare("mosaik.fieldset.WeitereInformationen", [dijit._Widget,dijit._Templated], {
	templateString:  dojo.cache("templates","fieldset/weitereInformationen.html"),
	_info: null,
	
	constructor: function () {
		
	},

	startup: function () {

	},
	
	setInformation: function( info ) {
		this._info = info;
		this.domNode.style.display="block";
		
		if ( typeof ( info.geaendert_von) !== "undefined") {
			this.geaendertVonContainer.style.display = "block";
			this.geaendertVonNode.innerHTML = sandbox.getValueById("XUser", info.geaendert_von);
		}
		
		if ( typeof ( info.geaendert) !== "undefined") {
			this.geaendertDatumContainer.style.display = "block";
			this.geaendertDatumNode.innerHTML = mysqlDatetimeToLocal(info.geaendert);
		}
	},

	hide: function () {
		this.domNode.style.display = "none";
	},

	show: function () {
		this.domNode.style.display ="block";
	}
});
