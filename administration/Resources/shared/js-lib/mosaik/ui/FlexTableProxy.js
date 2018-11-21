dojo.provide("mosaik.ui.FlexTableProxy");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("mosaik.util.Json");
dojo.require("mosaik.core.helpers");

dojo.declare("mosaik.ui.FlexTableProxy", [dijit._Widget,dijit._Templated], {
	templateString:  dojo.cache("templates","flexTable/FlexTable.html"),// load template form file
	_serviceUrl: null, // where to load data from
	_parameters: null, // send this parameters to flexTable and then to the datasource
	_queryPending: false, // is there a query waiting?
	_checkRunning: false,
	_isHidden: false,


	_updateDomNode: function (event) {
		this._swf = event.ref;
		if (!event.ref) {
			console.error("flash konnte nicht eingebunden werden");
		} else {
			console.log (">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> flash erfolgreich eingebunden");
			this._watch ( event.ref );
		}

	},
	_watch: function (element) {
		//dojo.connect (element, "mouseenter", this, this._onMouseEnter);
		//dojo.connect (element, "mouseout", this, this._onMouseOut);


		//dojo.connect ( this.domNode, "onclick", this, this._fireFakeEvent);
		//dojo.connect ( this.domNode, "onmousedown", this, this._fireFakeEvent);
	},

	_fireFakeEvent: function (event) {
		return;
		console.log ( event.button);
		if  ( event.button == 2 ) {
			return;
			console.log("faked");
			var fireOnThis = this.domNode;
			var evObj = document.createEvent('MouseEvents');

			evObj.initMouseEvent( 'contextmenu', true, false , window, 0,  event.screenX, event.screenY, event.clientX, event.clientY,
				event.ctrlKey, event.altKey, event.shiftKey, event.metaKey,
				0, this.domNode);
			fireOnThis.dispatchEvent(evObj);
			event.stopPropagation();
			event.preventDefault();
		}
	},

	resize: function (changeSize, resultSize) {
		dijit.focus(this.domNode);
	},

	postCreate: function () {

	},

	_loadObject: function() {
		this.flashNode.id = "_" + this.domNode.id;
		this._watch( this.domNode );
	},

	startup: function () {
        if ( this._contextMenu == null) {
            this._contextMenu = [];
        }

		this._checkReadyState();
		this._loadObject();
		return;
	},

	reload: function () {
		this.flashNode.reload();
	},

	getCurrentId: function () {
		return this.flashNode.getCurrentId();
	},

	getCurrent: function (field) {
		return this.flashNode.getCurrent(field);
	},

	defaultFields: ["id","standort_id","email", "person_id", "kontakt_id","seminar_art_id", "seminar_id", "hotel_id", "akquise_kontakt_id", "buchung_id"],
	getCurrentRow: function ( fields ) {
		if ( typeof(fields) === "undefined" ) {
			fields = this.defaultFields;
		}
		return _parseFlexData( this.flashNode.getCurrentRow( fields ));
	},

	resetParameters: function () {
		var currentUser = sandbox.getUserinfo();

		if ( typeof ( this.flashNode.resetParameters ) !== "undefined") {
			this.flashNode.resetParameters();
			this.flashNode.setParameter ( "token", currentUser['auth_token'] );

		}
	},

	queryService: function ( serviceURL, parameters) {
		this._serviceURL = serviceURL,
		this._parameters = parameters;
		this._queryPending = true;

		// set back all parameters
		this.resetParameters();
		// send the query
		this._sendQuery();
	},

	lock: function () {
		this.flashNode.lock();
	},

	unlock: function () {
		this.flashNode.unlock();
	},

	toggleEdit: function () {
		console.log("Toggle Edit");
		this.flashNode.toggleEdit();
	},

	toggleColumnChooser: function () {
		console.log("Toggle Column Chooser");
		this.flashNode.toggleColumnChooser();
	},

	exportCsv: function () {
		return this.flashNode.exportCsv();
	},

	_checkReadyState: function () {
		// Summary:
		// polls the flash component to see if its ready

        /* For DTABLE **/
        //if ( !this.nodeCopyed ) {
        //   this.flashNode = this.flashNode.contentWindow;
        //    this.nodeCopyed=true;
        //}

		if ( this._isReady() ) {
			console.log ("FlexTable ready...");
			this._onReady();
		} else if ( this._isHidden ) {
			console.log("we are hidden suspending check ready state");
			return;
		} else {
			console.log ("FlexTable not ready...");
			setTimeout ( dojo.hitch ( this, "_checkReadyState"), 1000);
		}

	},

    _linkButtons: function () {
        // link buttons (if avail on page)
        var editBtn = dijit.byId("editTable");
        var columnBtn = dijit.byId("editColumns");
        var exportBtn = dijit.byId("editExport");

        if ( editBtn ) {
            dojo.connect(editBtn, "onClick", this, "toggleEdit");
        }

        if ( columnBtn ) {
            dojo.connect(columnBtn, "onClick", this, "toggleColumnChooser");
        }

        if ( exportBtn ) {
            dojo.connect(exportBtn, "onClick", this, "exportToFile");
        }
    },
	_onReady: function () {
		if ( this._queryPending ) {
			this._sendQuery();
		}

        this._restoreContextMenu();

        this._linkButtons();

		this.onReady();
	},

    _restoreContextMenu: function () {
        // restore previous context menu
        var tmp = dojo.clone(this._contextMenu);
        this.clearContextMenu();
        dojo.forEach ( tmp, dojo.hitch ( this, function ( item ) {
            this.addContextMenuItem(item.label, item.signal, item.fields);
        }));
    },

	onReady: function () {

	},

	_isReady: function () {
		// Summary:
		//  retruns the ready state of the flex component

		console.log("Check ready state...");
		if (   typeof( this.flashNode ) === "undefined" ) {
			console.log("flashNode not set");
			return false;
		} else if ( typeof(this.flashNode.isReady) === "undefined" ) {
			console.log("flashNode.isReady not set");
			return false;
		} else if (! this.flashNode.isReady() ) {
			console.log("Flash node not ready");
			return false;
		}

		return true;
	},

	_sendQuery: function () {
		if ( this._isReady() ) {
			var currentUser = sandbox.getUserinfo();

			console.info ( "====> settings parameter");
			this.resetParameters();

			this.flashNode.setParameter ( "token", currentUser['auth_token'] );
			for ( var par in this._parameters ) {
				console.info("=====> setting: " + par + " to "+ this._parameters[par]);
				this.flashNode.setParameter ( par, this._parameters[par] );
			}

			console.info ("======> datasource service url: " + this._serviceURL );
			this._queryPending = false;
			this.flashNode.loadUrl ( this._serviceURL );
            setTimeout( dojo.hitch(this, this._restoreContextMenu), 1000);
		}
	},

	_onMouseEnter: function () {
		console.log ("mouse enter");
		//sandbox.setContextMenu(this._contextMenu);
		this.domNode.focus();
		if ( dojo.isFunction(this.flashNode.setInteractive)) {
			this.flashNode.setInteractive(true);
		}
	},

	_onMouseOut: function (e) {
		console.log ("mouseleave");
		if ( dojo.isFunction(this.flashNode.setInteractive)) {
			this.flashNode.setInteractive(true);
		}
	//	sandbox.unsetContextMenu();
	},

	/** EXPORT **/
	exportFile: null,

	exportToFile: function () {
    console.debug(" export to file called");
		try {
			//console.dir(this.flashNode.getAllRows());
			var data = this.flashNode.exportCsv();
			app.exportToFile("CSV Export", data);

		} catch (e) {
			console.log(e);
		}
	},

	hide: function () {
console.log("====> HIDING TABLE")
		//this.domNode.style.display = "none";
		if ( !this._isHidden ) {
			this._oldHeight = this.domNode.style.height;
			this._oldWidth = this.domNode.style.width;
            this.domNode.style.display="none";

            this.domNode.style.height="10px";
			this.domNode.style.width="10px";
         //   this.domNode.style.display="none";
		}
		this._isHidden = true;
	},

	show: function () {
        console.log("====> SHOWING TABLE");

		this._isHidden = false;
		this.domNode.style.height = this._oldHeight;
		this.domNode.style.width = this._oldWidth;
        //this.domNode.style.display="block";
        this.domNode.style.display="block";
        // this.reload();
        this.startup();
        this._linkButtons();
		//this.domNode.style.display ="block";
		if ( this._queryPending ) {
			this._checkReadyState();
		}
	},

	addContextMenuItem: function (label, onClickSignal, fields) {
        console.log("Add Context Menu: " + label);
		if ( typeof(fields) === "undefined" ) {
			fields = this.defaultFields;
		}

		this._contextMenu.push ( {label: label, signal: onClickSignal , fields: fields});

        if ( typeof (this.flashNode.addContextMenuItem) !== "undefined") {
		    this.flashNode.addContextMenuItem(label, onClickSignal, fields);
        }
	},

	clearContextMenu: function () {
		this._contextMenu = [];

		if ( typeof ( this.flashNode.clearContextMenu ) !== "undefined" ) {
			this.flashNode.clearContextMenu();
		}
	},

	getCol: function (colName) {
		// Summary:
		// returns the data in all columns of name colName
		return this.flashNode.getCol(colName);
	},

	getAllRows: function (keys) {
		// Summary:
		// returns the data in all columns of name colName
		//console.log( this.flashNode.getAllRows() );
		var ret = [];
		var result;
		if ( typeof ( keys) === "undefined" ) {
			console.log("getting all rows with all fields");
			result = this.flashNode.getAllRows();
		} else {
			console.log("get all rows using only keys");
			result = this.flashNode.getAllRowsWithKeys( keys );
		}
		for ( var key in  result) {
			// a bug in the flex parser used
			// \n and \r are not replaced
			// it is important! that the whole json string is returned in just one line
			// if not this code will break the string
			var text = result[key];//.replace(/\n/g, "\\n").replace(/\r/g, "\\r").replace(/</g,"&lt;").replace(/>/g,"&gt;");

			try {
				ret.push( JSON.parse(text) );
				console.log("Found ROW with ID:" + ret[ret.length-1].id);
			} catch ( e ) {
				console.error("Error Parsin JSON-String: ");
				console.error(e);
				console.error(text);
			}
		}
		//console.dir(ret);
		//return JSON.parse( this.flashNode.getAllRows() );
		//return dojo.fromJson(  this.flashNode.getAllRows()  );
		return ret;
	}
});
