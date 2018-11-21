/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

dojo.provide("module.seminarBearbeiten.Standorte");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");

dojo.declare("module.seminarBearbeiten.Standorte",[dijit._Widget, dijit._Templated], {
	templateString: dojo.cache("module.seminarBearbeiten", "Standorte.html"),
	title: "Referenten Planung",
	seminarArtId: "Undef",
	
	constructor: function () {
	
	},

	startup: function () {
		

	},
	
	postCreate: function() {
		this.inherited(arguments);
		var standorte = sandbox.getObject("Standort");
		var target = this.standorteNode;
		var self = this;


		
		target.innerHTML = '';
		dojo.forEach ( standorte, function ( item ) {
			if ( item.sichtbar_planung == 1) {
				dojo.create("a", {innerHTML: item.name, href: "#", onclick: function() {
					sandbox.loadShellModule("seminarReferentenEditor",{standortId: item.id, seminarId: self.seminarArtId});
				}},target);
				dojo.create("br", null, target);
			}
		});
	},

	 postMixInProperties:function(){
		this.inherited(arguments);
	 },

	 updateValues: function () {
		
	 }
});