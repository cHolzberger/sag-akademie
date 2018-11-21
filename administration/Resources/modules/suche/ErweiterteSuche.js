dojo.provide("module.suche.ErweiterteSuche");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.CheckBox");
dojo.declare("module.suche.ErweiterteSuche", [dijit._Widget, dijit._Templated], {
	widgetsInTemplate: true,
	templateString: dojo.cache("module.suche", "ErweiterteSuche.html"),
	tables: [
	{ value: "ViewPerson",
		label: "Personen" } ,
	{ value: "ViewKontakt",
		label: "Kunden" },
	{ value: "ViewSeminarPreis",
		label: "Termine" },
	{ value: "ViewBuchungPreis",
		label: "Buchungen"
	}],
	rowService: null,
	columns: null,
	table: null,

	operators: {
		"default": [
			{ label: "=", value: "="},
			{ label: "<=", value: "<="},
			{ label: ">=", value: "<="},
			{ label: "nicht =", value: "!="}
		],
		"string": [
			 { label: "=", value: "=" },
			 { label: "Enthält", value: "LIKE" },
			 { label: "!=", value: "!=" }
		]
	},

	options: null,
	rules: null,
	
	startup: function () {
		this.options = {};
		this.rules = [];

		this.rowService =  sandbox.getRpcService("database/table");

		this.tableSelect.set("options", this.tables);
		this.tableSelect.set("value", "asViewPerson");

		this.operatorSelect.set("options", this.operators['default']);
		this.operatorSelect.set("value", "=");

		this._updateFields();
	},

	_reset: function () {
		this.rules = [];
		dojo.byId("searchRules").innerHTML = "";
	},

	_onSearchButtonClick: function () {
		
		this.updateOptions();
		console.dir(this.options);
		this.onSearchButtonClick();
	},
	
	onSearchButtonClick: function () {
		//Summary:
		// just a proxy
	},

	_addRuleAnd: function() {
		var op = this.operatorSelect.get("value");
		var val = this.valueInput.get("value");
		var field = this.fieldSelect.get("value");
		this._addRule( "and", op, field,val);
	},

	_addRuleOr: function () {
		var op = this.operatorSelect.get("value");
		var val = this.valueInput.get("value");
		var field = this.fieldSelect.get("value");
		this._addRule( "or", op, field, val);
	},

	_addRule: function( connection, operator,field, data) {
		var table = dojo.byId("searchRules");
		var row=dojo.create("tr", {}, table);

		var conLbl = "und";
		if ( connection == "or" ) {
			conLbl = "oder";
		}

		dojo.create("td", {innerHTML: conLbl}, row);
		dojo.create("td", {innerHTML: field}, row);
		dojo.create("td", {innerHTML: operator}, row);
		dojo.create("td", {innerHTML: data}, row);

		this.rules.push({connection: connection, operator: operator, field: field, data: data});
		
	},

	_getDataType: function (field) {
		for ( var key in this.columns ) {
			if ( this.columns[key].value == field ) {
				return this.columns[key].type;
			}
		}
		return null;
	},

	_updateOperator: function () {
		var field = this.fieldSelect.get("value");
		var type = this._getDataType(field);

		if ( type == "string" ) {
			this.operatorSelect.set("options", this.operators['string']);
			this.operatorSelect.set("value", "LIKE");
		} else {
			this.operatorSelect.set("options", this.operators['default']);
			this.operatorSelect.set("value", "=");
		}
	},

	_updateFields: function () {
		var table = this.tableSelect.get("value");
		this.table = table;
		var _self = this;
		//alert("update fields" + this.tableSelect.get("value"));
		
		this.rowService.getTableColumns(table).addCallback(function (data) {
			console.dir(data);
			
			_self.fieldSelect.set("options",data);
			_self.columns = data;
			_self.fieldSelect.set("value", _self.columns[0].value);
		});
	},

	_onSearchButtonClick: function () {
		// Summary:
		// runs the search, requires the currentModule to have an flexTable
		
		var requestData = {
             advancedSearch: "yes",
			 rules: [],
			 table: this.table
		};

		dojo.forEach(this.rules, function ( item ) {
			if ( item.connection == "or" ) {
				requestData.rules.push("or");
			}

			requestData.rules.push( item.field + ":" + this._getDataType(item.field) +";" + item.operator + ";" + item.data);
		},this);
        requestData.rules = requestData.rules.join(",");
	//	console.dir(requestData.rules);

		//fixme: set context menus
		var serviceURL = sandbox.getServiceUrl ( "advSearch/" + this.table );
		currentModule.flexTable.queryService(serviceURL, requestData);
		currentModule.setContextMenu(this.table);
	}
});