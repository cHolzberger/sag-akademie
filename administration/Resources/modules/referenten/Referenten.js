dojo.provide("module.referenten.Referenten");
dojo.require("mosaik.core.Module");
dojo.require("dijit.form.Button");
dojo.require("mosaik.ui.FlexTable");
dojo.require("mosaik.util.Mailto");

dojo.declare("module.referenten.Referenten", [mosaik.core.Module], {

	constructor: function ( sandbox ) {
		console.log ("setting sandbox");
		this.sandbox = sandbox;
	},
	run: function () {
		this.container = dijit.byId("borderContainer");
			// flex table init

		this.referentSucheButton = dijit.byId("referentSucheButton");
		this.connectTo(this.referentSucheButton, "onClick", "_ftSearchReferent");

		connectOnEnterKey("referentSucheText", this, "_ftSearchReferent");
        this.flexTable.show();
        this.initFlexTable();
		
        var now = new Date();

		for ( i=parseInt(now.getFullYear())-1; i< parseInt(now.getFullYear())+3;i++) {
            var btn = dojo.create("a",{'year': i.toString(), "href": "#","class": "", innerHTML: "&Uuml;bersicht " + i}, dojo.byId("referentExport"),"last");
			dojo.connect(btn,"click",this, function (e) {
                currentModule.downloadTermine(e.currentTarget.attributes.year.value);
            });

			dojo.create("br",{},dojo.byId("referentExport"),"last");
        }
	},
	
	_ftSearchReferent: function () {
		var text = dijit.byId("referentSucheText").get("Value");
		dijit.byId("referentShowAll").domNode.style.display = "block";
		var service = sandbox.getServiceUrl("referenten") + "?q=" + text ;
		
		this.flexTable.queryService(service, {q: text});	
	},
	
	
	showAllReferenten: function () {
		var service = sandbox.getServiceUrl("referenten");
		dijit.byId("referentShowAll").domNode.style.display = "none";
		this.flexTable.queryService(service, {});	
	},
	
	mailToAll: function () {
		var referentenService = sandbox.getRpcService("database/referent");
		referentenService.getAllMail().addCallback(this, "_sendMailToAll");
	},
	
	_sendMailToAll: function (data) {
		console.dir(data);
		var mail  = new mosaik.util.Mailto();
		mail.addTo(sandbox.getAppVar("infomail"));
		mail.setSubject("Infomail");
		dojo.forEach( data, function ( item) {
			mail.addBcc(item.email);
		});
		sandbox.navigateToUrl(mail.toString());
	},
	
	
	initFlexTable: function () {
		if ( this._ftInitDone ) return;
        this._ftInitDone=true;

        this.flexTable.setTitle("Referenten:");
		
		this.flexTable.clearContextMenu();
		this.flexTable.addContextMenuItem("Referent bearbeiten", "flextable/editReferent");
		this.flexTable.addContextMenuItem("E-Mail an Referent senden", "flextable/mailReferent");

        this.subscribeTo("flextable/editReferent", "ftEditReferent");
        this.subscribeTo("flextable/mailReferent", "ftMailReferent");

        this.showAllReferenten();
	},
	
	ftEditReferent: function () {
		var id=this.flexTable.getCurrentId();

		console.log("edit referent: " + id);
		sandbox.loadShellModule("referentBearbeiten", {"referentId": id,
			results: this.flexTable.getAllRows()
		}); // War getAllRows([['id']]);
	}, 
	
	ftMailReferent: function () {
		var mail  = new mosaik.util.Mailto();
		var email = this.flexTable.getCurrentRow().email;

		mail.addTo( email );
		sandbox.navigateToUrl( mail.toString());
	},
	downloadTermine: function ( year ) {
		var token = sandbox.getUserinfo().auth_token;
		var url =this.sandbox.getAppVar("serverurl") + "/admin/csv/seminarreferent;csv?year=" + year + "&token=" + token;
		sandbox.navigateToUrl(url);
	}
	
});