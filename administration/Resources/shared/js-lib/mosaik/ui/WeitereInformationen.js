dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("mosaik.util.Json");
dojo.require("mosaik.core.helpers");


dojo.provide("mosaik.ui.WeitereInformationen");


dojo.declare("mosaik.ui.WeitereInformationen", [dijit._Widget,dijit._Templated], {
	templateString:  dojo.cache("mosaik","resources/WeitereInformationen.html"),
	_info: null,
	
	geaendertVon: "",
	geaendertDatum: "",
    erstelltVon: "",
    erstelltDatum: "",
	
	attributeMap: {
		geaendertVon: { node: "geaendertVonNode", type: "innerHTML"},
		geaendertDatum: {node: "geaendertDatumNode", type: "innerHTML"},
        erstelltVon: { node: "erstelltVonNode", type: "innerHTML"},
        erstelltDatum: {node: "erstelltDatumNode", type: "innerHTML"}
	},
	
	constructor: function () {
		
	},

	startup: function () {

	},
	
	setInformation: function( info ) {
		console.log("setInformationen");
		console.dir(info);
		this._info = info;
		this.domNode.style.display="block";

        if ( typeof ( info.angelegt_user_id) !== "undefined") {
            this.set("erstelltVon", sandbox.getValueById("XUser", info.angelegt_user_id));
        }

        if ( typeof ( info.angelegt_datum) !== "undefined") {
            this.set("erstelltDatum", mysqlDatetimeToLocal(info.angelegt_datum));
        }

		if ( typeof ( info.geaendert_von) !== "undefined") {
			this.set("geaendertVon", sandbox.getValueById("XUser", info.geaendert_von));
		}
		
		if ( typeof ( info.geaendert) !== "undefined") {

			this.set("geaendertDatum", mysqlDatetimeToLocal(info.geaendert));
		}
	},

	hide: function () {
		this.domNode.style.display = "none";
	},

	show: function () {
		this.domNode.style.display ="block";
	}
});
