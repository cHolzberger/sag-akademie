dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("mosaik.util.Json");

dojo.provide("mosaik.ui.DatasetNavigator");


dojo.declare("mosaik.ui.DatasetNavigator", [dijit._Widget,dijit._Templated], {
	templateString:  dojo.cache("mosaik","resources/DatasetNavigator.html"),
	data: null,
	currentId: null,
	currentIndex: 0,
	targetModule: "startseite",
	
	dataLength: 0,
	attributeMap: {
		dataLength: { node: "countNode", type: "innerHTML"},
		currentIndex: {node: "indexNode", type: "innerHTML"}
	},

	constructor: function () {
		
	},

	
	resize: function (changeSize, resultSize) {
		console.log("mosaik.ui.FlexKaelnder: resize");
		dijit.focus(this.domNode);
	},

	startup: function () {
		
	},

	
	postCreate: function () {
	
	},
	
	_setDataAttr: function (data) {
		// exit on invalid data
		if ( typeof ( data ) === "undefined" || !data.length) {
			this.dataLength = 0;
			return;
		}
		
		this.data = data;
		this.set("dataLength", data.length);
		this._checkVisibility();

	},
	
	_setCurrentIdAttr: function (id) {
		this.currentId = id;
		this._checkVisibility();
	},
	
	_checkVisibility: function () {
		if ( this.data == null  || this.currentId == null || this.get("dataLength") == 0) {
			dojo.style(this.domNode, "display", "none");
			return;
		}
		dojo.style(this.domNode, "display", "inline-block");
		
		var i;
		for (  i=0 ;i<this.data.length;i++ ) {
			if ( this.data[i].id == this.currentId ) break;
		}
		
		this.set("currentIndex", i+1);
		
		dojo.style ( this.firstNode, { visibility: "hidden"});
		dojo.style ( this.nextNode, { visibility: "hidden"});
		dojo.style ( this.lastNode, { visibility: "hidden"});
		dojo.style ( this.prevNode, { visibility: "hidden"});
		
		 if ( this.data.length != this.currentIndex) {
			dojo.style ( this.nextNode, { visibility: "visible"});
			dojo.style ( this.lastNode, { visibility: "visible"});
		} 
		
		if ( this.currentIndex != 1 ) {
			dojo.style ( this.firstNode, { visibility: "visible"});
			dojo.style ( this.prevNode, {visibility: "visible"});
		}
	},
	
	update: function ( options, idProperty, targetModule) {
	//	console.dir(options);
		
		if ( typeof ( options.create ) !== "undefined") return;
		if ( typeof ( options.results ) === "undefined") return;
		this.idProperty = idProperty;
		this.targetModule = targetModule;
		
		console.log("values set");
		this.set("currentId",  options[idProperty]);
		this.set("data", options.results);
	},
	
	gotoFirst: function () {
		var options = {};
		options.results = this.data;
		// rewind to first entry
		options.id = this.data[0].id;
		options[this.idProperty] = options.id;
		
		sandbox.loadShellModule(this.targetModule, options);
	},
	
	gotoNext: function() {
		var options = {};
		options.results = this.data;
		options.id = this.data[this.currentIndex].id;
		options[this.idProperty] = options.id;
		
		sandbox.loadShellModule(this.targetModule, options);
	},
	
	gotoPrev: function () {
		var options = {};
		options.results = this.data;
		options.id = this.data[this.currentIndex-2].id;
		options[this.idProperty] = options.id;
		
		sandbox.loadShellModule(this.targetModule, options);
	},
	
	gotoLast: function () {
		var options = {};
		options.results = this.data;
		options.id = this.data[this.data.length-1].id;
		options[this.idProperty] = options.id;
		
		sandbox.loadShellModule(this.targetModule, options);
	}
	
});
