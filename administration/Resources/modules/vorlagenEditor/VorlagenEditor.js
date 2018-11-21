dojo.provide("module.vorlagenEditor.VorlagenEditor");
dojo.require("mosaik.core.Module");
dojo.require("dijit.form.Button");
dojo.require("dijit.layout.AccordionContainer");
dojo.require("dijit.layout.ContentPane");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.Editor");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");

dojo.declare("module.vorlagenEditor.VorlagenEditor", [mosaik.core.Module], {
	_options: null,
	_sections: null,
	_templates: null,
	_sTemplates: {},
	currentTemplate: null,
	
	service: null,
	
	
	constructor: function ( sandbox ) {
		console.log ("setting sandbox");
		this.sandbox = sandbox;
	},
	
	run: function (options) {
		this._options = options;
		console.dir(options);
		this.service = this.sandbox.getRpcService("web/template");
		
		this.service.getSections().addCallback(this, "onSections");
		dojo.connect ( this.widgets.saveBtn, "onClick", this, "onSave");
        this.flexTable.hide();
	},
	
	onSections: function ( data ) {
		console.dir(data);
		
		dojo.forEach ( data, function ( item ) {
			var pane = dijit.layout.ContentPane({
				title: item
			});
			
			currentModule._sTemplates[item] = pane;
			currentModule.widgets.accordeonContainer.addChild(pane);
			
			currentModule.service.getTemplates( item ).addCallback(currentModule, "onTemplates");
		});
	},
	
	onTemplates: function ( data ) {
		var _t = this._sTemplates[data.section];
		
		console.dir(data);
		
		dojo.forEach ( data.templates, function ( item) { 
			var cnt = dojo.create("div", {innerHTML: item.title, "class": "hoverButton"},_t.domNode);
			dojo.connect(cnt, "onclick", this, function () {
				currentModule.currentTemplate = item;
				currentModule.widgets.betreff.set("value",  item.subject);
				currentModule.widgets.content.set("value", item.content);
			});
			
		});
		
		
	},
	
	onSave: function () {
		console.log("Save");
		sandbox.showLoadingScreen("Daten speichern...");
		
		var subject = currentModule.widgets.betreff.get("value");
		var content = currentModule.widgets.content.get("value");
		
		this.currentTemplate.content = content;
		this.currentTemplate.subject = subject;
		
		console.log("Content:"  + content);
		this.service.saveTemplate ( this.currentTemplate.id, subject, content  ).addCallback( this, "saveDone");
	}, 
	
	saveDone: function () {
		sandbox.hideLoadingScreen();
	}
	

});