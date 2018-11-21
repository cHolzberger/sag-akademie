/** require dependencies **/
dojo.require("mosaik.ui.FlexKalender");
dojo.require("mosaik.ui.TemplateBox");
dojo.require("dijit.form.RadioButton");
dojo.require("mosaik.core.Module");
dojo.require("module.kalender.DruckForm");

dojo.provide("module.kalender.Kalender");

var d=dojo;
var _$=dojo.byId;
var $=dojo.query;

dojo.declare("module.kalender.Kalender", [mosaik.core.Module], {
	flexKalender: null,
	container: null,
	_currentYear: 0,
	_dataStoreUrl: "",

	run: function (/* Object[] */ options) {
		// Summary:
		// module execution starts here
		this.flexKalender = dijit.byId("flexKalender");
		this.flexKalenderContainer = dijit.byId("borderContainer");
		
		var now = new Date();
		
		if ( typeof(options) !== "undefined" && typeof(options.year) !== "undefined") {
			this._currentYear = options.year;
		} else {
			this._currentYear = now.getFullYear();
		}
		
		this.initForm();
        this.flexTable.hide();
	}, 
	
	
	initForm: function () {
		if ( this.formInitDone ) return;
		
		this.formInitDone=true;
		
		this._dataStoreURL = this.sandbox.getServiceUrl("seminar/kalender");
		
		
		dojo.connect(this.widgets.druckDialog, "onHide", this, function () { 
			this.flexKalender.show();
		});
		
		dojo.connect(this.widgets.druckDialog, "onShow", this, function () { 
			this.flexKalender.hide();
		});
		
		this.subscribeTo("kalender/gotoTermin", "gotoTermin");
		this.subscribeTo("kalender/gotoSeminar", "gotoSeminar");
		this.subscribeTo("kalender/gotoReferenten", "gotoReferenten");
		this.subscribeTo("kalender/change", "markChanged");
	
		this.subscribeTo("kalender/lockTermin", "lockTermin");
		this.subscribeTo("kalender/unlockTermin", "unlockTermin");

		this.subscribeTo("kalender/lockFuturTermin", "lockFuturTermin");
		this.subscribeTo("kalender/lockFuturTerminStandort", "lockFuturTerminStandort");

		this.subscribeTo("kalender/updateTermin", "updateTermin");
		this.subscribeTo("kalender/updateFuturTermin", "updateFuturTermin");
		this.subscribeTo("kalender/updateFuturTerminStandort", "updateFuturTerminStandort");

		
        this.createYearBar();
		this.updateFlexKalender();
		
		this.hidePrintbar();
	},

    createYearBar: function () {
        var now = new Date();

        for ( i=2008; i<= parseInt(now.getFullYear())+3;i++) {
            var btn = dojo.create("a",{"href": "#","class": "fkYear", innerHTML: i}, dojo.byId("centerBar"),"last");
            dojo.connect(btn,"click",this, function (e) {
                sandbox.loadShellModule("kalender",{"year": e.currentTarget.innerHTML});
            });
        }
    },
	
	markChanged: function () {
		this.setChanged(true);
	},
	
	updateFlexKalender: function () {
		this.flexKalenderContainer.layout();

		console.log("Datastore URL: " + this._dataStoreURL + "/"+ this._currentYear);
		
		dojo.connect(this.flexKalender, "onReady",this, function () {
			this.flexKalender.setURL(this._dataStoreURL );
			this.flexKalender.setYear(this._currentYear.toString());
			dojo.byId("jahr").innerHTML = this._currentYear;
			
			try {
				var self = this;
				setTimeout( function() {
					self.flexKalender.gotoToday();
				} , 5000)
				
			} catch (e) {
				
			}
			
		});
	},

	gotoToday: function() {
		var d= new Date();
		var cY = d.getFullYear();
		if ( this._currentYear != cY) {
		
			this._currentYear = cY;
			dojo.byId("jahr").innerHTML = cY;
			this.updateFlexKalender();
		} else {	
			this.flexKalender.gotoToday();
		}
	},

	fullscreen: function() {
		this.flexKalender.toggleFullscreen();
	},

	gotoTermin: function ( data ) {
		console.log("gotoTermin");
		console.dir(data);
		if ( data.inhouse == 1) {
			sandbox.loadShellModule("inhouseTerminBearbeiten", data);
		} else {
			sandbox.loadShellModule("terminBearbeiten", data);
		}
	},

	gotoSeminar: function ( data ) {
		console.log("gotoSeminar");
		console.dir(data);
		if ( data.inhouse == 1 ) {
			data.seminarId = data.seminarId.replace("(I) ", "");
			sandbox.loadShellModule("inhouseSeminarBearbeiten", data);
		} else {
			sandbox.loadShellModule("seminarBearbeiten", data);
		}
	},

	gotoReferenten: function ( data ) {
		console.log("gotoReferenten");
		console.dir(data);
		data.terminId = data.seminarId; //FIXME: im flex berichtigen
		sandbox.loadShellModule("terminReferentenEditor", data);
	},

	save: function () {
		sandbox.setModuleChanged(false);
		this.flexKalender.save();
	},

	doPrint: function(month) {
		var token = sandbox.getUserinfo().auth_token;
		var detail = "keine";
		if ( dijit.byId("referenten").checked ) {
			detail = "referenten";	
		} else if (dijit.byId("teilnehmer").checked  ) {
			detail = "teilnehmer";
		}

		
		
		var pdfurl = this.sandbox.getServiceUrl ( "print/kalender" ) + this._currentYear + "-"+month+"?token=" + token + "&detail=" + detail;
		
		app.openPdf(pdfurl);
	},

	showPrintbar: function () {
		dojo.byId("printBar").style.display="block";
		dojo.byId("saveBar").style.display="none";
		dojo.byId("centerBar").style.display="none";
		
	},

	hidePrintbar: function() {
		dojo.byId("printBar").style.display="none";
		dojo.byId("saveBar").style.display="block";
		dojo.byId("centerBar").style.display="block";
	},
	
	showPrintDialog: function () {
		this.widgets.druckForm.set("currentYear", this._currentYear);

		this.widgets.druckDialog.show();
	},
	
	lockTermin: function (data) {
		var service =  this.sandbox.getRpcService("database/termin");
		var flexKalender = this.flexKalender;
		service.lock(data.terminId).addCallback(function () {
			flexKalender.reload();
		});
		
	},
	
	unlockTermin: function (data) {
		var service =  this.sandbox.getRpcService("database/termin");
		var flexKalender = this.flexKalender;
		service.unlock(data.terminId).addCallback(function () {
			flexKalender.reload();
		});
	},
	
	lockFuturTermin: function (data) {
		var service =  this.sandbox.getRpcService("database/termin");
		var flexKalender = this.flexKalender;
		service.lockFutur(data.terminId).addCallback(function () {
			flexKalender.reload();
		});
	},
	
	lockFuturTerminStandort: function (data) {
		var service =  this.sandbox.getRpcService("database/termin");
		var flexKalender = this.flexKalender;
		service.lockFuturStandort(data.terminId).addCallback(function () {
			flexKalender.reload();
		});
	},
	
	updateTermin: function (data) {
		var service =  this.sandbox.getRpcService("database/termin");
		var flexKalender = this.flexKalender;
		//alert("Update Termin: "+data.terminId);
		service.sync(data.terminId).addCallback(function () {
			flexKalender.reload();
		});
	},
	
	updateFuturTermin: function(data) {
		var service =  this.sandbox.getRpcService("database/termin");
		var flexKalender = this.flexKalender;
		//alert("Update Seminar: "+data.seminarId);
		service.syncFutur(data.terminId).addCallback(function () {
			flexKalender.reload();
		});
	},
	
	updateFuturTerminStandort: function (data) {
		var service =  this.sandbox.getRpcService("database/termin");
		var flexKalender = this.flexKalender;
		service.syncFuturStandort(data.terminId).addCallback(function () {
			flexKalender.reload();
		});
	}
	
});
