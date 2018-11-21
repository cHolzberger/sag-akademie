/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

dojo.provide("module.startseite.Statistiken");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");

dojo.declare("module.startseite.Statistiken",[dijit._Widget,dijit._Templated], {
	templateString: dojo.cache("module.startseite", "Statistiken.html"),
	headline: "",
	buchungen: "#",
	umbuchungen: "#",
	storno: "#",
	infoKey: "currentMonth",
	attributeMap: {
		buchungen: {
			node: "_buchungen",
			type: "innerHTML"
		},
		storno: {
			node: "_storno",
			type: "innerHTML"
		}
	},

	constructor: function () {
		dojo.subscribe ("startseite/statisticsUpdate", this, this.updateValues);
	},

	startup: function () {
		console.log("Statistiken.js => startup");
		this.inherited(arguments);

		this.updateValues();
	},

	updateValues: function () {
		var values = sandbox.getObject("startseite/statistiken")[this.infoKey];
		console.dir(values);
		
		if ( values ) {
			this.set ( "buchungen",values.buchungen ? values.buchungen : "0");
			this.set ( "umbuchungen", values.umbuchungen ? values.umbuchungen : "0");
			this.set ("storno" , values.storno ? values.storno : "0");
		}
		dojo.publish("startseite/update");
	}
});