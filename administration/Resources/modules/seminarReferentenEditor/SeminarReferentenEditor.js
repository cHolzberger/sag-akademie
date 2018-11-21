dojo.provide("module.seminarReferentenEditor.SeminarReferentenEditor");
dojo.require("mosaik.core.Module");
dojo.require("dijit.form.Button");
dojo.require("mosaik.ui.FlexSeminarReferentenEditor");

dojo.declare("module.seminarReferentenEditor.SeminarReferentenEditor", [mosaik.core.Module], {
	_options: null,
	
	constructor: function ( sandbox ) {
		console.log ("setting sandbox");
		this.sandbox = sandbox;
	},
	
	run: function (options) {
		this._options = options;
		console.dir(options);
        this.referentenEditor = dijit.byId("referentenEditor");

		this.container = dijit.byId("borderContainer");

	    this.flexTable.hide();

		this.setupStandorte();
        this.subscribeTo("editor/change", "markDirty");
        this.referentenEditor.queryService(options);

    },

    initFlexTable: function () {
        this.flexTable.setTitle("Referenten:");
        this.flexTable.queryService(options);
    },

    markDirty: function () {
        this.setChanged(true);
    },
	
	setupStandorte: function () {
		var standorte = sandbox.getObject("Standort");
		var target = dojo.byId("standorteContainer");
		var self = this;


		
		target.innerHTML = '';
		dojo.forEach ( standorte, function ( item ) {
			if ( item.sichtbar_planung == 1) {
				dojo.create("a", {innerHTML: item.name, href: "#", onclick: function() {
					sandbox.loadShellModule("seminarReferentenEditor",{standortId: item.id, seminarId: self._options.seminarId});
				}},target);
			}
		});
	},

	save: function() {
		this.referentenEditor.save();
	}
});