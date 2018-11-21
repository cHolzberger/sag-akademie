dojo.require("mosaik.core.helpers");
dojo.require("mosaik.core.Module");

dojo.require("dijit.form.Button");
dojo.require("dijit.layout.BorderContainer");
dojo.require("dijit.layout.ContentPane");
dojo.provide("module.aufgaben.Aufgaben");

dojo.declare("module.aufgaben.Aufgaben", [mosaik.core.Module], {
	_options: null,
	_todo: null,
	
	constructor: function (  ) {
		dojo.require("module.startseite.Todo");

	},
	
	run: function (options) {
		this._options = options;
		console.dir(options);
		
		this.container = dijit.byId("borderContainer");
		this.pane = dijit.byId("pane");
	}
});