/* 
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.Button");
dojo.provide("mosaik.ui.FileUpload");

dojo.declare("mosaik.ui.FileUpload", [dijit._Widget,dijit._Templated], {
	declaredClass: "mosaik.ui.FileUpload",
	templateString:  dojo.cache("templates","FileUpload.html"),
	widgetsInTemplate: true,
	fileId: null,
	service: "",
	field: "",
	value: "",
	_exists: false,
	emptyValue: "",
	uploadTitle: "Top auswählen",
	uploadType: "PDF Dokument",
	uploadFilter: "*.pdf;",

	_setExistsAttr: function ( value) {
		this._exists	=value;
		if ( value) {
			this.uploadBtn.set("label", "Ändern");
			this.deleteBtn.domNode.style.display="inline-block";
			this.showBtn.domNode.style.display="inline-block";
		} else {
			this.uploadBtn.set("label", "Datei auswählen");
			this.showBtn.domNode.style.display="none";
			this.deleteBtn.domNode.style.display="none";
		}
	},
	
	_setValueAttr: function ( value ) {
		if ( value != "" && value != null && this.emptyValue != value) {
			this.value = value;
			this.set("exists", true);
		} else {
			this.set("exists", false);
		}
	},

	postCreate: function() {
		console.log("PostCreate");
		dojo.connect ( this.uploadBtn, "onClick", this, "_onUpload");
		dojo.connect ( this.deleteBtn, "onClick", this, "_onDelete");
		dojo.connect ( this.showBtn, "onClick", this, "_onShow");
	},
	
	_onUpload: function () {
		console.log("_onUpload");
		var options = {}
		options[this.uploadType] = this.uploadFilter;
		var deferred = sandbox.uploadFile(this.uploadTitle, options, {"service": this.service, "id": this.fileId, "upload": true, "field": this.field});
		deferred.addCallback(dojo.hitch(this, "_afterUpload"));
	},
	
	_onDelete: function () {
		console.log("_onDelete");
		var deferred =sandbox.queryFileStore({"service": this.service, "id": this.fileId, "delete": 1,"field": this.field});
		deferred.addCallback(dojo.hitch(this, "_afterDelete"));
	},
	
	_onShow: function () {
		console.log("_onShow");
		sandbox.showFile({"service": this.service, "id": this.fileId, "download": 1,"field": this.field});
	},
	
	_afterDelete: function ( data ) {
		this.set("exists",false);
		this.afterDelete(data);
	},
	
	_afterUpload: function ( data ) {
		this.set("exists", true);
		console.log("Upload done");
		this.afterUpload(data);
	},
	
	_onChange: function ( data ) {
		console.log("Upload done");
		this.afterUpload(data);
	},
	
	onChange: function ( data ) {
		
	},
	
	afterDelete: function (data) {
		
	}, 
	
	afterUpload: function ( data ) {
		
	}
});
