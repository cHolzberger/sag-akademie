/***********************************************
* MailTo wrapper to generate mailto links
* copyright 2010 by Christian Holzberger <ch@mosaik-software.de>
* Released under LGPL
************************************************/
dojo.provide ("mosaik.util.Mailto");

dojo.declare ("mosaik.util.Mailto", null, {
	_to: null,
	_cc: null,
	_bcc: null,
	_subject: "",
	 _body: "",
	 
	 constructor: function () {
		this._to = [];
		this._cc = [];
		this._bcc = [];
	 },
	/**
	* adds a reciepient
	**/
	addTo: function (to) {
        to = to.toLowerCase();
        for ( i=0; i< this._to.length; i++) {
            if (this._to[i] == to) {
                return;
            }
        }
		this._to.push(to);
	},

	/**
	* adds a carbon copy reciepient
	**/
	addCc: function (cc) {
        cc = cc.toLowerCase();
        for ( i=0; i< this._cc.length; i++) {
            if (this._cc[i] == cc) {
                return;
            }
        }
		this._cc.push(cc);
	},

	/**
	* adds a blind carbon copy reciepient
	**/
	addBcc: function ( bcc ) {
        bcc = bcc.toLowerCase();
        for ( i=0; i< this._bcc.length; i++) {
            if (this._bcc[i] == bcc) {
                return;
            }
        }
		this._bcc.push (bcc);
	},

	/**
	 * checks if the add is in bcc
	 */
	isBcc: function ( bcc ) {
		for ( var i=0; i< this._bcc.length; i++ ) {
			if ( this._bcc[i] == bcc) return true;
		}
		return false;
	},

	/**
	* sets the subject of the message
	**/
	setSubject: function ( subject ) {
		this._subject = subject;
	},

	/**
	* Sets the Body of the Message
	**/
	setBody: function ( body ) {
		this._body = body;
	},

	/**
	* Construct Mailto String
	**/
	toString: function () {
		var _link = ["mailto:"];

		// check to
		if ( this._to.length > 0 ) {
			_link.push(this._to.join("; "));
		}
		// set the subject ( and add the first arg with ? )
		_link.push("?subject=");
		_link.push(escape(this._subject));
		// CHECK CC
		if ( this._cc.length > 0 ) {
			_link.push ("&cc=");
			_link.push ( this._cc.join("; ") );
		}
		// CHECK BCC
		if ( this._bcc.length > 0 ) {
			_link.push ("&bcc=");
			_link.push (this._bcc.join("; "));
		}

		if ( this._body != "" ) {
			_link.push("&body=");
			_link.push(escape(this._body));
		}

		return _link.join("");
	}

});