/* 
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.Dialog");
dojo.require("dojox.widget.ColorPicker")
dojo.require("dijit.layout.ContentPane");
dojo.require("dijit.layout.BorderContainer");
dojo.provide("mosaik.ui.ColorInput");


dojo.declare("mosaik.ui.ColorInput", [dijit._Widget,dijit._Templated], {
	popup: null,
	colorpicker: null,
	templateString:  dojo.cache("templates","colorInput.html"),
	widgetsInTemplate: true,
	color: "#ffffff",

	constructor: function () {
	
	},
	
	
	_setValueAttr: function (color) {
		this.color = color.replace("0x","#");
		
		this.colorpicker.set("value", this.color);
		this.colorNode.style.backgroundColor = this.color+" !important;";
		console.log("color set: " + color);
		this.onChange(color);
		
	},
	
	_getValueAttr: function () {
		return  this.color.replace("#","0x");
	},
	
	selectColor: function() {
		console.log("om show!");
		this.popup.show();
	},
	
	_colorSelected: function () {
		this.popup.hide();
		
		this._setValueAttr ( this.colorpicker.get("value")) ;
	}
});
