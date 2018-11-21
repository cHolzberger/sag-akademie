dojo.provide("module.mailing.Mailing");
dojo.require("mosaik.core.Module");
dojo.require("module.kontakt.Umkreissuche");
dojo.require("dijit.form.Button");
dojo.require("mosaik.ui.FlexTable");

dojo.declare("module.mailing.Mailing", [mosaik.core.Module], {
	_ftInitDone: false,
	
	constructor: function ( sandbox ) {
		console.log ("setting sandbox");
		this.sandbox = sandbox;
	},
	run: function () {
		this.layout = dijit.byId("borderContainer");
			// flex table init

		
		this.umkreissuche = dijit.byId("umkreissuche");
		this.connectTo ( this.umkreissuche, "onSearchButtonClick", this.onUmkreissuche);
		this.initFlexTable();

		
	},
	onUmkreissuche: function( ) {
		var options = this.umkreissuche.options;
		
		var serviceURL = this.sandbox.getServiceUrl ( "kontakt/umkreissuche" );
		this.flexTable.queryService(serviceURL, options);

	},

	initFlexTable: function () {
		// Summary:
		// connects buttons and flex table events
		if ( this._ftInitDone ) return;
			 
			 this._ftInitDone = true;
        this.flexTable.setTitle("Umkreissuche:");
        this.flexTable.clearContextMenu();

		//this.subscribeTo("flextable/editKontakt", "ftEditKontakt");
		//this.subscribeTo("flextable/editAkquiseKontakt",  "ftEditAkquiseKontakt");
		//this.subscribeTo("flextable/editPerson", "ftEditPerson");
		//this.subscribeTo("flextable/editKontaktOrAkquise", "ftEditKontaktOrAkquise");
	},
});