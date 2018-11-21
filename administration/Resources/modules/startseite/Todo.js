/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

dojo.provide("module.startseite.Todo");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");

dojo.declare("module.startseite.Todo",[dijit._Widget,dijit._Templated], {
	templateString: dojo.cache("module.startseite", "Todo.html"),
	data: null,
	
	constructor: function () {
		 dojo.subscribe ("startseite/todoUpdate", this, this.updateValues);
	},

	startup: function ( ) {
		this.updateValues();
	},

	updateValues: function () {
		var html = [];

		var todo = this.data = sandbox.getObject("startseite/todo");

		for (var i=0; i < todo.length; i++) {
			var row = dojo.create ("tr",{"class": "hoverBtn"},this.todoTableNode);
			
			dojo.create("td", {innerHTML: todo[i].erstellt}, row);
			dojo.create("td", {innerHTML: todo[i].kategorie}, row);
			dojo.create("td", {innerHTML: todo[i].betreff}, row);
			dojo.create("td", {innerHTML: todo[i].notiz}, row);

			var context = {todo: todo[i], results: todo};
			dojo.connect ( row, "onclick", context, this.todoClick);
		}
	},

	todoClick: function ( evt ) {
		
		sandbox.loadShellModule("todoBearbeiten", {todoId: this.todo.id, results: this.results});
	}
});
