/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

dojo.provide("module.termineSeminare.SeminarRubrik");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojo.Stateful");

dojo.declare("module.termineSeminare.SeminarRubrik",[dijit._Widget, dijit._Templated], {
	templateString: dojo.cache("module.termineSeminare", "SeminarRubrik.html"),
	rubricName: "#Unbekannt#",
	seminare: null,
	seminarArtNode: null,
	
	attributeMap: {
		rubricName: {
			node: "rubricNameNode",
			type: "innerHTML"
		}
	},

	constructor: function () {
		this.seminare = [];
	},

	_setSeminareAttr: function (value) {
		this.seminare = value;
		if ( value.length == 0 ) {
			this.domNode.style.display = "none";
		} else {
			this.domNode.style.display = "block";
			this.updateSeminare();
		}
	},

	_seminarClick: function (evt) {
		console.log("seminar clicked: " + evt.currentTarget.innerHTML);
		this.seminarArtSelected ( evt.currentTarget.innerHTML);
	},

	seminarArtSelected: function (seminarName) {
		// event proxy 
	},
	
	updateSeminare: function () {
		var _node = null;
		console.log ("update seminar");
		this.seminarArtNode.innerHTML = "";

		for ( var i =0; i < this.seminare.length; i++) {
			_node = dojo.create ("li", {
				"class":"seminarBtn",
				"innerHTML": this.seminare[i].name,
				"seminarId": this.seminare[i].name },
			this.seminarArtNode);

			dojo.connect ( _node, "onclick", this, this._seminarClick);
		}
	},

	startup: function () {
		
		this.updateSeminare();
	},

	 postMixInProperties:function(){
		this.inherited(arguments);
	 },

	 updateValues: function () {
		
	 }
});