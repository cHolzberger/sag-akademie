dojo.provide("module.hotelBearbeiten.PriceRow");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.CheckBox");
dojo.declare("module.hotelBearbeiten.PriceRow", [dijit._Widget, dijit._Templated], {
	widgetsInTemplate: true,
	templateString: dojo.cache("module.hotelBearbeiten", "PriceRow.html"),
			 _data: null,
			 _deleteMe: false,
	startup: function () {
	},

	setData: function(data) {
		this._data = data;
		this.ez.set("value", dojo.number.parse(data.zimmerpreis_ez));
		this.dz.set("value", dojo.number.parse(data.zimmerpreis_dz));
		this.mz.set("value", dojo.number.parse(data.zimmerpreis_mb46));
		this.fruehstueck.set("value", dojo.number.parse(data.fruehstuecks_preis));
		this.marge.set("value", dojo.number.parse(data.marge));
		this.info.set("value", data.info);
		this.datumVon.set("value", mysqlDateToDate(data.datum_start));
		this.datumBis.set("value", mysqlDateToDate(data.datum_ende));
	},

	getData: function () {
		_self = this;

			data = {
			id: _self._data.id,
			 deleteRow: this._deleteMe,
			 zimmerpreis_ez: _self.ez.get("value"),
			 zimmerpreis_dz: _self.dz.get("value"),
			 zimmerpreis_mb46: _self.mz.get("value"),
			 fruehstuecks_preis: _self.fruehstueck.get("value"),
			 marge: _self.marge.get("value"),
			 datum_start: mysqlDateFromDate(_self.datumVon.get("value")),
			 datum_ende: mysqlDateFromDate(_self.datumBis.get("value")),
			 info: _self.info.get("value")
			};
	
		return data;
	},

	_deleteRow: function() {
		this.domNode.style.display = "none";
		this._deleteMe=true;
	}

});