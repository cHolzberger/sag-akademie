dojo.require("mosaik.core.Module");

dojo.require("dijit.layout.BorderContainer");
dojo.require("dijit.form.Button");
dojo.require("dijit.layout.StackContainer");
dojo.require("dijit.layout.ContentPane");
dojo.require("module.statistik.BelegungProBereich");
dojo.require("module.statistik.SeminareProBereich");

dojo.require("module.statistik.Buchungen");
dojo.require("module.statistik.BuchungenIndex");

dojo.provide("module.statistik.Statistik");
dojo.declare("module.statistik.Statistik", [mosaik.core.Module], {
	year: 2014,
	_selectHandle: null,
	
	run: function () {
		var date = new Date();
		
		this.year = date.getFullYear();

		this.service = sandbox.getRpcService("database/stats");
		
		// CONTAINER
		this.stack = dijit.byId("stackContainer");
		//PANES
		this.buchungenStatsPane = dijit.byId("buchungenStatsPane");
		this.sagStatsPane = dijit.byId("sagStatsPane");
		this.webStatsPane = dijit.byId("webStatsPane");
		this.angelegtStatsPane = dijit.byId("angelegtStatsPane");
		// SELECT
		this.yearSelect = dijit.byId("yearSelect");
		this.createYearBar();
		this.yearSelect.set("value", this.year.toString() );

		
		// widgets
		this.buchungenStats = dijit.byId("buchungenStats");
		this.belegungProBereichStats = dijit.byId("belegungProBereichStats");
		this.seminareProBereichStats = dijit.byId("seminareProBereichStats");
		this.buchungenIndex = dijit.byId("buchungenIndex");

		
		this.buchungenStats.set("service", this.service);
		this.belegungProBereichStats.set("service",this.service);
		this.seminareProBereichStats.set("service",this.service);
		this.buchungenIndex.set("service", this.service);
		
		this.selectBuchungenStats();
		this.flexTable.hide();
	},

	createYearBar: function () {
        var now = new Date();

        for ( i=2008; i< parseInt(now.getFullYear())+3;i++) {
			this.yearSelect.addOption({value: i.toString(), label: i.toString(), selected: i.toString() == this.year.toString()});
            
        }
    },
	
	selectSagStats: function () {
		this._selectHandle !== null ? dojo.disconnect(this._selectHandle):false;
		dojo.connect(this.yearSelect, "onChange", this, "selectSagStats");
		this.year = this.yearSelect.get("value");
		
		this.yearSelect.domNode.style.display="block";
		
		this.stack.selectChild(this.sagStatsPane);
		
		this.buchungenStats.set("year",this.year);
		this.belegungProBereichStats.set("year",this.year);
		this.seminareProBereichStats.set("year",this.year);
	},
	
	selectBuchungenStats: function () {
		this._selectHandle !== null ? dojo.disconnect(this._selectHandle):false;

		this.yearSelect.domNode.style.display="none";

		this.stack.selectChild(this.buchungenStatsPane);
	},
	
	selectWebStats: function () {
		this._selectHandle !== null ? dojo.disconnect(this._selectHandle):false;
		
		this.yearSelect.domNode.style.display="none";

		this.stack.selectChild(this.webStatsPane);
	},
	
	selectAngelegtStats: function () {
		this._selectHandle !== null ? dojo.disconnect(this._selectHandle):false;
		dojo.connect(this.yearSelect, "onChange", this, "selectAngelegtStats");
				
		this.yearSelect.domNode.style.display="inline-block";

		this.stack.selectChild(this.angelegtStatsPane);

		this.service.getUserStats().addCallback(dojo.hitch(this, "fillAngelegtStats"));
	},

	fillAngelegtStats: function(data) {
		var year = this.yearSelect.get("value");
		dojo.byId("year1").innerHTML = year;
		dojo.byId("year2").innerHTML = year;
		var kundeTbody = dojo.byId("kundeTbody");
		var akquiseTbody = dojo.byId("akquiseTbody");
		kundeTbody.innerHTML="";
		akquiseTbody.innerHTML="";
		console.dir(data);
		for( var key in data.user ) {
			var user = data.user[key];
			var tr = dojo.create("tr", {}, kundeTbody);
				
			dojo.create("td", {innerHTML: user, "class": 'monthLabel'}, tr);

			for ( var i=1; i<=12;i++) {
				if ( typeof(data.kontaktStats[year]) !=="undefined" &&
					typeof(data.kontaktStats[year][i]) !=="undefined" &&
					typeof(data.kontaktStats[year][i][user]) !=="undefined") {
					dojo.create("td",{innerHTML: data.kontaktStats[year][i][user], "class": 'numericValue' }, tr);
				} else {
					dojo.create("td", {innerHTML: "&nbsp;" , "class": 'numericValue'}, tr);
				}
			}

			var tr = dojo.create("tr", {}, akquiseTbody);
			console.log(user);
			dojo.create("td", {innerHTML: user, "class": 'monthLabel'}, tr);
			
			for ( var i=1; i<=12;i++) {
				if ( typeof(data.akquiseStats[year]) !=="undefined" &&
					typeof(data.akquiseStats[year][i]) !=="undefined" &&
					typeof(data.akquiseStats[year][i][user]) !=="undefined") {
					dojo.create("td",{innerHTML: data.akquiseStats[year][i][user] , "class": 'numericValue'}, tr);
					} else {
						dojo.create("td", {innerHTML: "&nbsp;"  , "class": 'numericValue'}, tr);
					}
			}
		}
	}
});