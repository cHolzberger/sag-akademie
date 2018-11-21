/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");

dojo.provide("module.startseite.Termine");
dojo.declare("module.startseite.Termine",[dijit._Widget,dijit._Templated], {
	templateString: dojo.cache("module.startseite", "Termine.html"),
	standort: "Unbekannt",

	constructor: function () {
		 dojo.subscribe ("startseite/termineUpdate", this, this.updateValues);
	},

	 postMixInProperties:function(){
		this.inherited(arguments);

		console.log ("Termine.js: " + this.standort);
	 },

	 startup: function() {
		 console.log("Termine: startup");
		 this.inherited(arguments);
		 this.updateValues();
	 },

	 updateValues: function () {
		var values = sandbox.getItemStore("startseite/termine");
		
		this.beginUpdate();
		
		var results = values.query ( {"standort_name": new RegExp(".*"+this.standort+".*") }) ;
		
		this.setValues(results);
	 },
	 
	 beginUpdate: function () {
		console.log("begin update");
		this.terminNode.innerHTML = "";
	 },

	 setValues:function( termine, request) {
		var html = [];
		this.termine = termine;

		//console.log("==============================================Termine:");
		//console.dir(termine);
		dojo.forEach(termine, function ( termin ) {
			var row = dojo.create("tr", {}, this.terminNode);
			var kursCell = dojo.create("td", {"class": "kursnr hoverBtn", innerHTML: termin.kursnr}, row);
			dojo.create("td",{innerHTML: termin.datum}, row);

			// event handler
			var context = {
				termine: termine,
				termin: termin
			}
			dojo.connect ( kursCell, "onclick", context, this.terminClick);
		},this);

		dojo.publish("startseite/update");
	 },

	 terminClick: function (evt) {
		 
		 sandbox.loadShellModule("terminBearbeiten", {id: this.termin.id, terminId: this.termin.id, results: this.termine});
	 }
});