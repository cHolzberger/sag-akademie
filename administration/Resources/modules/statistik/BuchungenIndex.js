/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("mosaik.core.helpers");

dojo.provide("module.statistik.BuchungenIndex");

dojo.declare ("module.statistik.BuchungenIndex", [dijit._Widget, dijit._Templated], {
	templateString: dojo.cache("module.statistik", "BuchungenIndex.html"),
	service: null,
	
	_setServiceAttr:function ( service ) {
		console.log("service set");
		this.service=service;
		this.statContainer.innerHTML = '<center><br/><img src="/app/shared/icons/ajax-loader.gif" /></center><br/>';
		this.service.getBuchungenIndex( ).addCallback( dojo.hitch(this, "statsRecieved"))
		.addErrback ( function (data) {console.log("Error loading stats for ");console.dir(data)} );
		
	},
	
	statsRecieved: function ( stats ) {
		console.log("Got it")
		var _cnt = [];
		var now = new Date();
		var year = now.getFullYear();

		for ( var i=parseInt(year)+2; i >= 2004; i--) {
			_cnt.push ("<fieldset class='box -verySmall outer' style='float: left;'><legend>" + i + " ("+stats[i].total +" )</legend>");
			for ( var j=1; j<=12; j++) {
				if ( typeof ( stats[i][j] ) === "undefined") {
					_cnt.push( intToMonth ( j) + "<br/>" );
				} else {
					_cnt.push( intToMonth ( j) + " ("+ stats[i][j]+ ")<br/>" );
				}
			}
			_cnt.push("</fieldset>");
			this.statContainer.innerHTML = _cnt.join("");

		}
		
		this.statContainer.innerHTML = _cnt.join("");
	}

});

