/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.form.Button");
dojo.require("module.dialogs.PersonChooser");
dojo.provide("module.dialogs.KontaktChooser");

dojo.declare("module.dialogs.KontaktChooser" , [module.dialogs.PersonChooser], {
	_serviceName: "database/kontakt",

	createResultNode: function ( item ) {
		var resultsNode = this.resultsNode;

		console.dir(item);
		var div = dojo.create("div", {
			style: "cursor: pointer;",
			"class": "chooserResult"
		}, resultsNode);
		dojo.create("div", {
			innerHTML: item.firma + "<br/>" + item.kundennr,
			style: "font-weight: bold; font-size: +1; float: left; width: 40%; padding-right: 5px;"
		}, div);
		dojo.create("div", {
			innerHTML: item.strasse + " " + item.nr + " <br/>(" + item.plz+" "+item.ort + ")",
			style: "font-weight: normal; float: left;"
		}, div);
		dojo.create("div", {
			innerHTML: "",
			style: "clear: both;"
		}, div);

		div.data =  item;

		dojo.connect(div, "onclick", this, "_onResultClick");
	}

		
});