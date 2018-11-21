/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */


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
 
dojo.provide("mosaik.ui.ErweiterteSuche");


dojo.declare("mosaik.ui.ErweiterteSuche", [ dijit._Widget, dijit._Templated], {	
	templateString:  dojo.cache("mosaik", "resources/ErweiterteSuche.html"),
	widgetsInTemplate: true,
	service: null,
	_columns: null,
	_currentTable: null,
	_rules: null,
	
	postCreate: function() {
		this.service =  sandbox.getRpcService("database/table");	
		
		dojo.connect ( this.tableSelectNode, "onChange", this, "findTableColumns");
		this.findTableColumns();
	},
	
	findTableColumns: function () {
		var tableName = this.tableSelectNode.get("value");
		this._currentTable = tableName;
		this._rules = [];
		this.rulesNode.innerHTML = "";

		this.service.getTableColumns( tableName ).addCallback(this, "onTableColumns").addErrback( function (data) {
			console.log( "Exception: " );
			console.error(data);
		});
	}, 
	
	onTableColumns: function (data) {
		var options = {
			idProperty: "value",
			label: "value'",
			data: data
		};
		

		var store = new dojo.store.Memory(options);
		
		this._columns = dojo.clone (data);
		this.fieldSelectNode.removeOption( this.fieldSelectNode.getOptions() );
		this.fieldSelectNode.set("value", -1);
		this.fieldSelectNode.addOption( data);
		this.fieldSelectNode.set("maxHeight", 200);
	},
	
	addAndRule: function () {
		var fieldName = this.fieldSelectNode.get("value");
		var operator = this.operatorSelectNode.get("value");
		var value = this.valueSelectNode.get("value");
		
		var rule = dojo.create("div", {
			innerHTML: "und Feld '"+fieldName+"' " + operator +" '"+value+"'"
			}, this.rulesNode);
		this._rules.push ( {
			fieldName: fieldName, 
			operator: operator, 
			value: value, 
			chainCommand: "and",
			type: "string" // FIXME: the type var has to be moved from the query to the php layer
		});
		this.onAddRule();
	},
	
	addOrRule: function () {
		var fieldName = this.fieldSelectNode.get("value");
		var operator = this.operatorSelectNode.get("value");
		var value = this.valueSelectNode.get("value");
		
		var rule = dojo.create("div", {
			innerHTML: "oder Feld '"+fieldName+"' " + operator +" '"+value+"'"
			}, this.rulesNode);
			
		this._rules.push ( {
			fieldName: fieldName, 
			operator: operator, 
			value: value, 
			chainCommand: "or",
			type: "string" // FIXME: the type var has to be moved from the query to the php layer
		});
		this.onAddRule();
	},
	
	onAddRule: function () {
	// Summary:
	// event hook called when the user add a new rule 
	// either an "and" or an "or" rule
	},
	
	search: function () {
		if ( this._rules.length == 0 ) {
			alert ("Keine Regel definiert, bitte fügen Sie Bedingungen hinzu.");
		} else {
			this.onSearch(this._rules, this._currentTable);
		}
	},
	
	onSearch: function (rules, table) {
		//Summary:
		// event hook triggered on search
		// the first argument is an array of rules
		// in the format: 
		//  [ {
		// 	fieldName: fieldName, 
		//	operator: operator, 
		//	value: value, 
		//	chainCommand: "or/and"
		// } , ... ]
		// the second argument is the table that should be searched
		
	},
	
	reset: function () {
		this.rulesNode.innerHTML = "";
		this._rules = [];
		this.onReset();
	},
	
	onReset: function () {
	// Summary:
	// event hook called when the user resets the list view
	}
	
});
