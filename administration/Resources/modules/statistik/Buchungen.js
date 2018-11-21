/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");

dojo.provide("module.statistik.Buchungen");

dojo.declare ("module.statistik.Buchungen", [dijit._Widget, dijit._Templated], {
	templateString: dojo.cache("module.statistik", "Buchungen.html"),
	service: null,
	year: null,
	totals: null, 
	monthly: null,
	
	_setServiceAttr:function ( service ) {
		console.log("service set");
		this.service=service;
	},
	
	_setYearAttr: function (value) {
		console.log("year set to: " + value);
		this._set("year", value);
		
		this.headline.innerHTML = "Buchungen " +value;
		this.footLabel.innerHTML = value + " gesamt";
		
		this.service.getBuchungen( value ).addCallback( dojo.hitch(this, "statsRecieved"))
		.addErrback ( function (data) { console.log("Error loading stats "); console.dir(data)} );
		this.tbody.innerHTML = '<tr><td colspan="7" ><center><br/><img src="/app/shared/icons/ajax-loader.gif" /></center><br/></td></tr>';
	},
	
	statsRecieved: function ( stats) {
		var label = "";
		console.log("--Stats recieved")
		
		
		this.totals = stats.total[this.year];
		this.monthly = stats.monthly;
		
		//console.log("Totals: ");
		//console.dir(this.totals);
		
		for( label in this.totals) {
			console.log(label);
			
			if ( typeof ( this['tfoot_' + label] ) !== "undefined") {
				this["tfoot_" + label].innerHTML = this.totals[label];
			}
		}
		
		//console.log("Monthly:");
		//console.dir(this.monthly);
		// monthly row
		var row = [];
		console.log ("walk through");
		for ( label in this.monthly ) {
			var item = this.monthly[label];
			
			row.push ( "<tr><td class='monthLabel'>");
			row.push (item.monat); // fixme: translate!
			row.push("</td><td  class='numericValue'>");
			row.push(item.bestaetigt);
			row.push("</td ><td class='numericValue'>");
			row.push( item.teilgenommen);
			row.push("</td ><td class='numericValue'>");
			row.push(item.umgebucht);
			row.push("</td ><td class='numericValue'>");
			row.push(item.abgesagt);
			row.push("</td ><td class='numericValue'>");
			row.push(item.storno);
			row.push("</td class='numericValue'><td class='numericValue'>");
			row.push(item.gesamt);
			row.push("</td ></tr>");
			this.tbody.innerHTML = row.join("");
		}
		
		this.tbody.innerHTML = row.join("");
		
		//console.log(row.join(""));
	}

});
