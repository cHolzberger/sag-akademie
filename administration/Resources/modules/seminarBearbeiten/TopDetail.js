/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

dojo.provide("module.seminarBearbeiten.TopDetail");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("mosaik.ui.FileUpload");

dojo.declare("module.seminarBearbeiten.TopDetail",[dijit._Widget,dijit._Templated], {
	templateString: dojo.cache("module.seminarBearbeiten", "TopDetail.html"),
	widgetsInTemplate: true,
	title: "TOP",
	titleField: "info_titel",
	linkField: "info_link",
	prefix: "SeminarArt",
	titleValue: null,
	linkValue: null,
	
	_data: null,

	startup: function ( ) { 
		this.legendNode.innerHTML = this.title;
		
		dojo.connect(this.titleNode, "onChange", this,this._onChange);
		dojo.connect(this.linkNode, "onChange", this,this._onChange);
		dojo.connect(this.uploaderNode, "afterUpload", this,this._onFileChange);
		dojo.connect(this.uploaderNode, "afterDelete", this,this._onFileDelete);


	},
	
	_onFileDelete: function () {
		console.log("TopDetail::_onFileDelete");
		this.titleNode.set("value","");
		this.linkNode.set("value","");
		this.uploaderNode.set("exists", false);
		
		this._onChange();
	},
	
	_onChange: function () {
		console.log("TopDetail::_onChange");

		
		this.linkValue = this.linkNode.get("value");
		this.titleValue = this.titleNode.get("value");
		
		this.onDataChange ( );
	},
	
	_onFileChange: function ( value ) {
		this.linkNode.set("value", value.newFileName);
		this.uploaderNode.set("exists", true);
		this.onDataChange();
	},
	
	onDataChange: function () {
		
	},
	
	_setDataAttr: function (data) {
		this.legendNode.innerHTML= this.title;
		this._data = data;
		this.uploaderNode.set("service", "TopPDF");

		this.uploaderNode.set("fileId", data.id);
		this.uploaderNode.set("exists", data[this.linkField]!=="");
		this.uploaderNode.set("field", this.linkField);
		this.titleValue = data[this.titleField];
		this.linkValue = data[this.linkField];
		this.titleNode.set("value", data[this.titleField]);
		this.linkNode.set("value", data[this.linkField]);
	}
});