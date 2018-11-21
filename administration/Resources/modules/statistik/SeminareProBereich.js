/* 
 * Use without written License forbidden
 * Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("module.statistik.BelegungProBereich");

dojo.provide("module.statistik.SeminareProBereich");

dojo.declare ("module.statistik.SeminareProBereich", [dijit._Widget, dijit._Templated, module.statistik.BelegungProBereich], {
	templateString: dojo.cache("module.statistik", "SeminareProBereich.html"),
	service: null,
	year: null,
	totals: null, 
	monthly: null,
	serviceMethod: "getSeminareProBereich",
	headlineText:"Anzahl Seminare pro Bereich  "
});
