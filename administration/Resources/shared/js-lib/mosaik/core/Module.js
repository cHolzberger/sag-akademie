/*
* Use without written License forbidden
* Copyright 2011 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
*/

// Summary:
// Basic module other modules inherit from
console.log("mosaik.core.Module");
dojo.require("dijit.form.Button")
dojo.provide("mosaik.core.Module");

dojo.provide("mosaik.core.UIState");
dojo.require('dijit.Editor');
dojo.require("dijit._editor.plugins.AlwaysShowToolbar");
dojo.require("dijit._editor.plugins.LinkDialog");
dojo.require("dijit._editor.plugins.TextColor");
dojo.require("dijit._editor.plugins.FontChoice");
dojo.require("dojox.editor.plugins.ToolbarLineBreak");
dojo.require("dijit.Dialog");
dojo.require("mosaik.ui.FlexTable");

dojo.declare("mosaik.core.UIState", null, {
	_common : null,
	_pool : null,


	constructor : function() {
		this._connections = {};

		this._pool = {
			_connections : {},
			_link : function(state, connection) {
				console.log("Link EventHandler: " + state);

				if( typeof (connection.obj) === "undefined") {
					connection.handler = dojo.subscribe(connection.event, connection.context, connection.callback);
				} else {
					connection.handler = dojo.connect(connection.obj, connection.event, connection.context, connection.callback);
				}
				connection.active = true;

				this._connections[state].push(connection);
				return this;
			},
			_unlink : function(state, connection) {
			

				if( typeof (connection.obj) === "undefined") {
					console.log("Unsubscribe EventHandler: " + state);
					dojo.unsubscribe(connection.handler);
				} else {
					console.log("DisconnectEventHandler: " + state);
					dojo.disconnect(connection.handler);
				}

				connection.handler = null;
				connection.active = false;
			}
		};
	},
	transitionTo : function(state) {
		this.state = state;
		return this;
	},
	group : function(state) {
		console.log("return group with state: " + state);

		var gr = {
			_pool : this._pool,
			to : this.to,
			isEmpty : this.isEmpty,
			suspend : this.suspend,
			resume : this.resume,
			replaceContext : this.replaceContext,
			state : state,
			connectTo : this.connectTo,
			subscribeTo : this.subscribeTo,
			onSuspend : function() {
			},
			onResume : function() {
			}
		};

		return gr;
	},
	isEmpty : function(state) {
		state = ( typeof state === "undefined" ) ? this.state : state;
		if( typeof (this._pool._connections[state]) !== "undefined" || this._pool._connections[state].length == 0) {
			return true;
		}
		return false;
	},
	clear : function(state) {
		state = ( typeof (state) === "undefined" ) ? this.state : state;
		console.log("clear state: " + state);
		// clear event queues
		dojo.forEach(this._pool._connections[state], function(itm) {
			if(itm.active === true) {
				this._pool._unlink(state, itm);
			}
		}, this);
		this._pool._connections[state] = [];
		this.state = state;
		return this;
	},
	register : function(state) {
		this.state = state;
		this._pool._connections[state] = [];
		console.log("Register EventGroup: " + state);

		return this;

	},
	applyCommon : function(obj) {
		this._commonObj = obj;
		return this;
	},
	to : function(objects) {
		var state = this.state;

		dojo.forEach(objects, function(sbs) {
			this._pool._link(state, dojo.mixin(sbs, this._commonObj));
		}, this);

		this.state = state;
		return this;
	},
	replaceContext : function(context) {
		var state = this.state;

		dojo.forEach(this._pool._connections[state], function(sbs) {
			if( typeof connection.obj === "undefined" && sbs.active && sbs.context != context) {
				this._pool._unlink(state, sbs);
			}

			sbs.context = context;

			this._pool._link(state, sbs);
		}, this);
	},
	connectTo : function(obj, event, context, callback) {
		this._pool._link(this.state, {
			obj : obj,
			event : event,
			context : context,
			callback : callback

		});
	},
	subscribeTo : function(event, context, callback) {
		this._pool._link(this.state, {
			event : event,
			context : context,
			callback : callback
		});
	},
	suspend : function(state) {
		dojo.forEach(this._pool._connections[state], function(sbs) {
			if(sbs.active) {
				alert("AktiveConnection");
				this._pool._unlink(state, sbs);
			}
		}, this);
		this.onSuspend();
	},
	onSuspend : function() {

	},
	resume : function() {
		var state = this.state;
		dojo.forEach(this._pool._connections[state], function(sbs) {
			this._pool._link(state, sbs);
		}, this);
		this.onResume();
	},
	onResume : function() {
	}
});

dojo.declare("mosaik.core.Module", null, {
	moduleName : "#UNBEKANNT#",
	formPrefix : "Data",
	currencyFormat : {
		places : 2,
		type : "currency"
	},
	hourFormat : {
		places : 2,
		type : "decimal"
	},
	_changedData : null,
    flexTable:null,
	_ignoreUpdate : true,
	_currentData : null,
	_isRunning : true,
	_suspendable : null,

	constructor : function() {
        if ( window.ft_singleton ) {
            this.flexTable =  window.ft_singleton();
        }

        this._valueWatchers = [];
		this._topics = []; // store subscribe handles
		this._events = []; // store connect handles
	},
	initDNav : function() {

	},
	setModuleName : function(name) {
		// Summary:
		// set current modules name
		this.moduleName = name;
	},
	setSandbox : function(sandbox) {
		// Summary:
		// set a sandbox for this module
		this.sandbox = sandbox;
	},
	run : function(options) {
		// Summary:
		// run this module
		// your module should implement this
		console.log("==> NO RUN METHOD DEFINED!!");
		document.body.innerHTML = "<div style='width: 100%; height: 100%;'>Todo: " + document.body.module + "</div>";

	},
	doRun : function(options) {
		// Summary:
		// run this module
		// additionally trigger events

		this._suspendable = [];

		// lets walk the widgets
		var widgets = {};
		dijit.registry.forEach(function(w) {
			widgets[w.id] = w;
		});
		this.widgets = widgets;

		var nodes = {};
		dojo.query("[id]").forEach(function(node) {
			//console.log(node.id);
			nodes[node.id] = node;
		});
		this.nodes = nodes;

		// anyone want to listen?
		this.onRunStart();
		this._isRunning = true;

		// run this
		try {
            this.subscribeTo("module/save", "_onSave");
            this.subscribeTo("module/hideFlash","hideFlash");
            this.subscribeTo("module/showFlash","showFlash");
			this.run(options);
		} catch ( error ) {
			console.error("Module::run error: ")
			console.dir(error);
			
			var message = "Name: " + this.moduleName + "\n";
			message +="Options:\n";
			for ( var key in options) {
				message += key +": " + options[key] + "\n";
			}
			 message += error.name + "\nMessage\n" + error.message + "\nStackTrace:\n\n";
			for( var i =0; i<error.stackTrace.length; i++ ) {
					message += error.stackTrace[i].sourceURL + ":" +error.stackTrace[i].line + " " + error.stackTrace[i].functionName + "\n"; 
			}
				
			sandbox.reportError( message );

			hideLoadingScreen();
		}

		dojo.connect(window, "onmousedown", this, function() {
			sandbox.hideContextMenu();
		});
		
		dojo.connect(window, "onresize", this, this.windowResize);
		this.windowResize();
		// run has finised so trigger the event
		this.onRunFinish();
	},
	windowResize: function () {
		if ( this.widgets.topPane && this.widgets.borderContainer) {	
			
			var topPaneHeight = dojo.position(this.widgets.topPane.domNode).h;
			var borderContainerHeight = dojo.position(this.widgets.borderContainer.domNode).h;
			if ( topPaneHeight > borderContainerHeight) {
				this.widgets.topPane.resize({h: borderContainerHeight-50});
				this.widgets.borderContainer.layout();
			}
		} else if ( this.widgets.stackContainer && this.widgets.borderContainer) {	
			
			var topPaneHeight = dojo.position(this.widgets.stackContainer.domNode).h;
			var borderContainerHeight = dojo.position(this.widgets.borderContainer.domNode).h;
			if ( topPaneHeight > borderContainerHeight) {
				this.widgets.stackContainer.resize({h: borderContainerHeight-50});
				this.widgets.borderContainer.layout();
			}
		}


	},
	
	onRunStart : function() {

	},
	onRunFinish : function() {

	},

	_valueUpdate : function(newValue) {
		// Summary:
		// a automaticly filled input field is updated and
		// we record the changes here
		// - be aware that this method runs in a different context

		if( typeof (newValue) === "undefined") {
			console.error("newValue is undefined");
			return;
		}

		if(this.module._ignoreUpdate) {// should we ignore made changes? e.g. to load initial data
			console.log("ignore change");
			return;
		}
		var _value = "";

		var dataName = this.element.id.replace(this.module.formPrefix + ":", "");

		switch ( this.element.declaredClass ) {// convert special formats to a format the database layer understands
			case "dijit.form.DateTextBox":
				if(newValue === null) {
					_value = "0000-00-00";
				} else {
					_value = mysqlDateFromDate(newValue);
				}
				console.log("DATE TEXT BOX: " + _value);
				break;
			case "dijit.form.CheckBox":
				if(newValue === true) {
					_value = 1;
				} else {
					_value = 0;
				}
				break;
			case "dijit.form.CurrencyTextBox":
				if(newValue == null) {
					console.error("newValue IS NULL!");
					return;
				}
				_value = parseFloat(this.element.get("value").toString());
				break;
			case "dijit.form.Select":
				if(newValue == null) {
					console.error("newValue IS NULL!");
					return;
				}
				_value = this.element.get("value");
				break;
			default:
				if(newValue == null) {
					console.error("newValue IS NULL!");
					return;
				}
				_value = newValue;
				break;
		}

		this.module.updateValue(dataName, _value);
	},
	updateValue : function(dataName, _value) {
		// monitor changedData
		//console.dir(this._changedData);
		console.log(dataName + " changed to: " + _value);

		// notice a change when the changed property exists in modules _currentData property
		if(this._currentData[dataName] === null || ( typeof (this._currentData[dataName] ) !== "undefined" && _value.toString() != this._currentData[dataName].toString()  )) {
			console.log("CHANGE> " + dataName);
			console.log(" -> oldValue: " + this._currentData[dataName]);
			console.log(" -> newValue:" + _value);
			this._changedData[dataName] = _value;
		//	this.setChanged(true);

		} else if( typeof (this._changedData[dataName]) !== "undefined" && this._changedData[dataName] === this._currentData[dataName]) {
			// if old data and new data are equal remove the
			// changed data entry
			delete (this._changedData[dataName]);
		}



		this.valueUpdate(dataName, _value);
	},
	valueUpdate : function(id, value) { // here external scripts can connect via dojo.connect( mod, "valueUpdate" );
		// just a hook;
	},
	_valueWatchers : null,
	setValue : function(valueObj) {
		// Summary:
		// sets input data of ui elements to the matching value:
		// valueObj = {data1: "xy", data2: "xz"};
		// and
		// formPrefix == "myform"
		//
		// <input id="myform:data1" /> <- value will be set to xy
		// <input id="myform:data2" /> <- value will be set to xz
		//
		//

		dojo.forEach(this._valueWatchers, function(item) {
			dojo.disconnect(item);
		});

		this._changedData = {};
		this._currentData = valueObj;
		var prevIgn = this._ignoreUpdate;
		this._ignoreUpdate = true;

		for(var key in valueObj ) {
			//			console.log ("Looking up "+this.formPrefix+":" +key);
			var element = dijit.byId(this.formPrefix + ":" + key);

			if( typeof (element ) !== "undefined" && element !== null) {// dojo pimped element (aka widget)
				//console.log ("Setting "+this.formPrefix+":" + key + " => " + valueObj[key] );
				//console.log("Watching " + this.formPrefix + ":" + key);
				// setup change watcher
				// glue the module to the element
				var context = {
					module : this,
					element : element,
                    org_value: null
				};
				this._valueWatchers.push(dojo.connect(element, "onChange", context, this._valueUpdate));

                this._valueWatchers.push(dojo.connect(element, "onKeyDown", context, function () {
                    this.module.setChanged(true);
                }));

                this._valueWatchers.push(dojo.connect(element, "onFocus", context, function () {
                    this.org_value = this.element.get("value");
                }));

                this._valueWatchers.push(dojo.connect(element, "onChange", context, function () {
                    if ( this.org_value !== null && this.org_value != this.element.get("value")) {
                        this.module.setChanged(true);
                    }
                }));



				try {
					// extract value accordingly to its type
					switch ( element.declaredClass ) {
						case "dijit.form.Select":
							//console.log("found select setting to: "  + valueObj[key].toString());
							//element.attr("value", String (valueObj[key]) );
							//element.value = String ( valueObj[key] );
							element.set("value", String(valueObj[key]));
							break;
						case "dijit.form.DateTextBox":
							//console.log("found date");
							if( typeof (valueObj[key]) === "undefined" || valueObj[key].toString() == "0000-00-00" || valueObj[key].toString() == "0000-00-00 00:00:00") {
								continue;
							}
							element.set("value", mysqlDateToDate(valueObj[key].toString()));

							break;
						case "dijit.form.CheckBox":
							var val = valueObj[key].toString();

							if(val == "1" || val == "true" || val == true) {
								element.set("value", true);
							} else {
								element.set("value", false);
							}

							break;
						case "dijit.form.CurrencyTextBox":
							//console.log("found currency");
							element.set("value", parseFloat(valueObj[key].toString()));
							break;
						default:
							//console.log("default");
							element.set("value", valueObj[key].toString());
							break;
					}
				} catch ( e ) {
					// do messy error report ;)
					console.log("Error while extracting Data: ");
					console.log(e);
				}

			} else {// if the module is plain HTML
				element = dojo.byId(this.formPrefix + ":" + key);

				if( typeof (element ) !== "undefined" && element !== null && typeof (valueObj[key] ) !== "undefined" && valueObj[key] !== null) {
					//console.log("found normal html element");
					element.innerHTML = valueObj[key].toString();
				}
			}
		}
		this._ignoreUpdate = prevIgn;
		this.onValuesSet();
		this.setChanged(false);
	},
	
	changed: false,
	setChanged: function (b) {
		sandbox.setModuleChanged(b);
		this.changed=b;
	},
	
	isChanged: function () {
        // Summary:
        // when another module is requested this method is called to check if the
        // data has changed and loading of the new module should be interupted to ask
        // the user to save his data
		return this.changed;
	},
	
	onValuesSet : function() {
		//Summary: this hook is called AFTER new values have been set
		this._ignoreUpdate = false;
		//console.log("~> new data set successfully");
	},
	hideFlash : function() {
		if( typeof (this.flexTable) !== "undefined") {
	//		this.flexTable.hide();
		}

		if(this.flexKalenderContainer) {
			this.flexKalenderContainer.style.display = "none";
		}

		if(this.flexKalender) {
			this.flexKalender.hide();
		}
	},
	showFlash : function() {


		if( typeof (this.flexTable ) !== "undefined") {
//			this.flexTable.show();
		}

		if(this.flexKalenderContainer) {
			this.flexKalenderContainer.style.display = "block";
		}

		if(this.flexKalender) {
			this.flexKalender.show();
		}
	},
    /* sets up rte that is embedded in the page
    * should really be a mixin */
    rtEdit: function ( node ) {
        if ( !this.rteInitDone) {
            this.rteInitDone=true;
            this.rteDialog = new dijit.Dialog({
                title: "Bearbeiten",
                id: "rteDialog",
                draggable: false,
                focused: true


            });
            //this.rteDialog.show();

            this.rteNode = new dijit.Editor({
                extraPlugins:['|','createLink','|','foreColor','hiliteColor','||', 'fontName', 'fontSize', 'formatBlock']
            },this.rteDialog.containerNode);

            //this.rteNode.placeAt(this.rteDialog.domNode);

        }
        var dialog = this.rteDialog;
        var rteNode = this.rteNode;
        var htmlNode = dojo.byId(node);


        var eBody = rteNode.iframe.contentDocument.body;
        dojo.connect(eBody,'click',function(evt) {
            if (evt.target.tagName === "A" ) {
                dojo.stopEvent(evt);
            }
        } );
        this.hideFlash();
        dialog.show();
        var oldValue = htmlNode.innerHTML;
        rteNode.set("value", htmlNode.innerHTML);
        if ( this._rteEvtHandle) {
            dojo.disconnect( this._rteEvtHandle );
        }

        this._rteEvtHandle = dojo.connect(dialog, "onHide", htmlNode, function ( ) {

            var context = {
                module: currentModule,
                element: this
            };
            var vchandler = dojo.hitch ( context, currentModule._valueUpdate);
            this.innerHTML = rteNode.get("value");
            if (this.innerHTML != oldValue) {
                currentModule.setChanged(true);
            }
            currentModule.showFlash();
            vchandler(this.innerHTML);

        });

    },

	suspend : function(options) {
		console.log(" ================> Suspending " + this.moduleName);
		this.hideFlash();
        this.flexTable.onSuspend();
		this._isRunning = false;
		this.sandbox.suspend();
		
		
		dojo.forEach(this._events, function (item) {
			dojo.disconnect(item.handle);
			item.handle = null;
		},this);
		
		dojo.forEach(this._topics, function (item) {
			dojo.unsubscribe(item.handle);
			item.handle = null;
		},this);
		// sync everything on suspend
		// before other module starts
		// QUICKFIX FOR SAGADMUI-48
		// this.sandbox.publish("sync/start");
		this.onSuspend();

	},
	onSuspend : function() {

	},
	connectTo : function(target, event, handler) {
		this._events.push({ event: event, handler: handler, target:target, handle: dojo.connect(target, event, this, handler)});
	},
	subscribeTo : function(event, handler) {
		this._topics.push({ event: event, handler: handler, handle: dojo.subscribe(event, this, handler)});
	},
	
	resume : function(options) {
		console.log("================> Resuming " + this.moduleName);
		this.showFlash();
        this.flexTable.onResume();
		this._isRunning = true;
		this.sandbox.resume();
		
		dojo.forEach(this._events, function (item) {
			item.handle = dojo.connect(item.target, item.event, this, item.handler);
		},this);
		
		dojo.forEach(this._topics, function (item) {
			item.handle = dojo.subscribe(item.event, this,item.handler);
		},this);
		
		this.onResume();

	},
	onResume : function() {

	},
	isRunning : function() {
		return this._isRunning;
	},
	destroy : function() {
		console.log("Destorying Module...");
		this.onDestory();
	},
	onDestory : function() {

	},

    _onSave: function (data) {
        this.onSave();
       // alert("Goto: " + data.moduleName);
    },
    onSave: function() {
        alert("Missing save handler!");
    }


});
