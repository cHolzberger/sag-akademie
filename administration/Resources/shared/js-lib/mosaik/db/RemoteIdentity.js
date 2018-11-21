dojo.provide ("mosaik.db.RemoteIdentity");

dojo.declare("mosaik.db.RemoteIdentity", null, {
	_app: null,
	state: "invalid",
	states: ["invalid", "waiting", "ok", "error"],
	username: "",
	password: "",
	database: "",
	userinfo: "",
	valid: false,
	ds: null,
	dn: "",
	format: "login",
	service: "authentication",

	constructor: function ( app ) {
		this._app = app;
	},
	
	setup: function () {
		
	},
	
	validateUserdata: function ( userdata ) {
		console.log ("validate userdata");
		dojo.mixin( this, userdata );
		this._app.setVar("serverurl", this.database);
		this._app.setVar("databaseurl", this.database);
		
		this._app.publish("userdata/validate");

		this._sendRequest ( this._app.datastore.getServiceURL(this.service, this.database), userdata);
	},
	
	// can be moved to .prototype
	_sendRequest: function (service, vars) {
		console.dir(vars);
		
		var query = {
			url: service,
			handleAs: "json",
			preventCache: true,
			content: vars,
			load: dojo.hitch ( this, "onLogon"),
			error: dojo.hitch (this, "onError")
		}
		
		dojo.xhrPost ( query );
		this.authenticationStateChange ("waiting");
	},

	onLogon: function (data) {
		dump(data, "Profile");
		
		if ( ! data ) {
			this.valid =false;
			setTimeout ( dojo.hitch ( this, function () {
				this.authenticationStateChange("invalid");
			}),100);

		} else if ( data.success == true ) {
			this.valid = true;
			this.userinfo = data.userinfo;
			this.dn = data.userinfo.dn;
			setTimeout ( dojo.hitch ( this, function () {
				this.authenticationStateChange("ok");
			}),100);
		} else {
			this.valid = false;
			setTimeout ( dojo.hitch ( this, function () {
				this.authenticationStateChange("invalid");
			}),100);
			
		}

	},

	onError: function (error) {
		console.log ("RemoteIdentity - Error: ");
		console.log(error);
	//	alert ("Fehler Nummer: 21\nStatus:\n"+error);
		this.authenticationStateChange ( "error");
	},

	isAuthenticated: function () {
		return this.state == "ok";
	},

	logoff: function () {

	},
	
	authenticationStateChange: function (_state) {
		var _prevState = this.state;
		this.state = _state;

		this._app.publish ( "userdata/"+_state, [{ fromState: _prevState, toState: _state }] );
	}
});
