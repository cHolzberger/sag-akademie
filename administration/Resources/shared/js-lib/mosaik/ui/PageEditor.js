/** require dependencies **/
dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.Editor');
dojo.require("dojox.widget.Standby");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require('dijit.Editor');
dojo.require("dijit._editor.plugins.AlwaysShowToolbar");
dojo.require("dijit.layout.ContentPane");
dojo.require("dijit.layout.BorderContainer");


dojo.provide("mosaik.ui.PageEditor");


dojo.declare("mosaik.ui.PageEditor", [ dijit._Widget, dijit._Templated], {	
	templateString:  dojo.cache("mosaik", "resources/PageEditor.html"),
	widgetsInTemplate: true,
	service: null,
	pageKey: "start",
	
	postCreate: function() {
		this.service =  sandbox.getRpcService("web/text");
		console.log("PageEditor::postCreate: loading page: " + this.pageKey);
		this.fetchData();		
	},

	save: function () {
		this.data.title = this.titleNode.get("value");
		this.data.text = this.textNode.get("value");
		
		this.onSave();
	},

	onSave: function () {
		this.service.save( this.pageKey, this.data ).addCallback( dojo.hitch ( this, function (data) {
			this.updateData(data);
		})).addErrback(function (data) {
			console.log ("Todo Error: " + data);
			alert("Fehler beim Speichern: \n " + data);
		});
	},
	
	resize: function (size) {
		console.log ("!!!!!!!!!! RESIZE");
		console.dir (size);
		
		dojo.marginBox( this.domNode, { w: size.w, h: size.h, x: 0, y: 0});	
				this.borderContainer.resize(size);

	},
	
	load: function (key) {
		this.pageKey = key;
		this.fetchData();
	},
	
	fetchData: function () {
		this.service.find( this.pageKey ).addCallback ( dojo.hitch ( this, "updateData"))
		.addErrback (dojo.hitch ( this, function (data) {
			console.log ("==!!> TodoBearbeiten::run Error: "+data);
		}));
	},
	
	updateData: function (data) {
		this.data = data;
		this.titleNode.set("value", data.name);
		this.textNode.set("value", data.text);
	}
});
