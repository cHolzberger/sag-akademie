/* 
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");

dojo.provide("module.statistik.BelegungProBereich");

dojo.declare ("module.statistik.BelegungProBereich", [dijit._Widget, dijit._Templated], {
	templateString: dojo.cache("module.statistik", "BelegungProBereich.html"),
	service: null,
	year: null,
	totals: null, 
	monthly: null,
	serviceMethod: "getBelegungProBereich",
	headlineText:"Semniarbelegung pro Bereich ",
	
	_setServiceAttr:function ( service ) {
		console.log("service set");
		this.service=service;
	},
	
	_setYearAttr: function (value) {
		console.log("year set to: " + value);
		this._set("year", value);
		
		this.headline.innerHTML = this.headlineText +value;
			
		this.service[this.serviceMethod]( value ).addCallback( dojo.hitch(this, "statsRecieved"))
		.addErrback ( function (data) {console.log("Error loading stats for " + value);console.log(data)} );
		this.tbody.innerHTML = '<tr><td colspan="7" ><center><br/><img src="/app/shared/icons/ajax-loader.gif" /></center><br/></td></tr>';
	},
	
	statsRecieved:function ( stats) {
		var label;
		console.log("Belegung pro bereich")
		console.dir(stats);
		
		var totals = this.totals = stats.total;
		this.monthly = stats.monthly;
		
		// find headers
		var _tmpHeaders = [];
		
		for ( label in this.totals ) {
			if ( label != "gesamt" && label != "monat_translated" && label!="monat" && label != "") {
				_tmpHeaders.push( label );
			}
		}
		_tmpHeaders.sort( function (a,b) { return a.toLowerCase() > b.toLowerCase();});
		_tmpHeaders.push("gesamt");
		_tmpHeaders.unshift("Monat");
		this.thead.innerHTML= "";
		// table headings
		var _th = [];
		_th.push("<tr>");
		
		dojo.forEach ( _tmpHeaders, function ( item ) {
			_th.push("<th nowrap class='statHeader'>" + item + "</th>");
		});
		
		_th.push("</tr>");
		
		this.thead.innerHTML = _th.join("");
		
		// table body
		var _tb = [];
		for ( label in this.monthly ) {
			var month = this.monthly[label];
			_tb.push("<tr><td nowrap class='monthLabel'>" + month.monat + "</td>");
			
			dojo.forEach ( _tmpHeaders, function ( item ) {
				if ( item == "Monat") return;
			
				if ( typeof ( month[item] ) !== "undefined") {
					_tb.push("<td nowrap class='numericValue'>" + month[item] + "</td>");
				} else {
					_tb.push("<td nowrap class='numericValue'>-</td>");
				}
			});
			_tb.push("</tr>");
		
		}
		this.tbody.innerHTML = _tb.join("");
		
		// table footer
		this.tfoot.innerHTML = "";
		
		var _tf = [];
		
		_tf.push("<tr><td nowrap class='monthLabel'>" + this.year + " gesamt</td>");
			
		dojo.forEach ( _tmpHeaders, function ( item ) {

			if ( item == "Monat") return;
			_tf.push("<td nowrap class='numericValue'>" + totals[item] + "</td>");
		});
		_tf.push("</tr>");
		
		this.tfoot.innerHTML = _tf.join("");
	}

});
