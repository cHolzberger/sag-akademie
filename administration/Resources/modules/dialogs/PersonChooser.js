/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.form.Button");
dojo.provide("module.dialogs.PersonChooser");
dojo.declare("module.dialogs.PersonChooser" , [dijit._Widget,dijit._Templated], {
	templateString: dojo.cache("module.dialogs", "PersonChooser.html"),
	widgetsInTemplate: true,
	service: null,
	selectedNode: null,
	selectedItem: null,
	_overlay: null,
	_serviceName: "database/person",
	options: null,
	kontaktId: null,

	constructor: function ( ) {
		this.options = {};
	},

	beforeSearch: function() { // Summary: event hook
	},

	afterSearch: function () { // Summary: event hook
	},

	search: function () {
		// Summary:
		// trigger search
		this.beforeSearch();
		
		if ( this.kontaktId != null ) {
			this.options.kontaktId = this.kontaktId;
		}
		console.log( this.searchNode.value);
		this.resultsNode.innerHTML = '<center><br/><img src="/app/shared/icons/ajax-loader.gif" /></center>';

		console.log("Sending Request: " + this.searchNode.value);
		console.dir(this.options);
		
		
		this.service.search ( this.searchNode.value, this.options).addCallback( this, "onResults");

	},

	postCreate: function () {
		// Summary:
		// connect search button to search function
		this.inherited(arguments);
		this.service = sandbox.getRpcService(this._serviceName);
		dojo.connect(this.searchBtnNode,"onClick", this, "search");
	},

	onResults: function ( data ) {
		// Summary:
		// construct the list of display nodes
		this.resultsNode.innerHTML = "";
		
		dojo.forEach ( data, dojo.hitch( this, this.createResultNode));

		this.afterSearch();
	},

	createResultNode: function ( item ) {
		var resultsNode = this.resultsNode;

		var div = dojo.create("div", {
			style: "cursor: pointer;",
			"class": "chooserResult"
		}, resultsNode);
		dojo.create("div", {
			innerHTML: item.firma,
			style: "font-weight: bold; font-size: +1; float: left; width: 40%; padding-right: 5px;"
		}, div);
		dojo.create("div", {
			innerHTML: item.vorname + " " + item.name,
			style: "font-weight: normal; float: left;"
		}, div);
		dojo.create("div", {
			innerHTML: "",
			style: "clear: both;"
		}, div);

		div.data =  item;

		dojo.connect(div, "onclick", this, "_onResultClick");
	},

	_onResultClick: function (e) {
		// Summary:
		// set selectedNode
		// add class to selectedNode
		console.log("Clicked");
		var data = e.currentTarget.data;
		console.dir(data);
		if ( this.selectedNode != null ) {
			dojo.removeClass(this.selectedNode, "chooserResultSelected");
		}

		this.selectedNode = e.currentTarget;
		this.selectedItem = data;
		dojo.addClass(this.selectedNode, "chooserResultSelected");
			
		this.onResultClick(e,data);
	},

	onResultClick:function (e,data) {
		// Summary: event hub
	}
});