/***********************************************
* MailTo wrapper to generate mailto links
* copyright 2010 by Christian Holzberger <ch@mosaik-software.de>
* Released under LGPL
************************************************/

function MosaikMailto () {
	var _to=[];
	var _cc=[];
	var _bcc=[];
	var _subject = "";
	var _body = "";

	/**
	* adds a reciepient
	**/
	this.addTo = function (to) {
		_to.push(to);
	}

	/**
	* adds a carbon copy reciepient
	**/
	this.addCc = function (cc) {
		_cc.push(cc);
	}

	/**
	* adds a blind carbon copy reciepient
	**/
	this.addBcc = function ( bcc ) {
		_bcc.push (bcc);
	}

	/**
	 * checks if the add is in bcc
	 */
	this.isBcc = function ( bcc ) {
		for ( var i=0; i< _bcc.length; i++ ) {
			if ( _bcc[i] == bcc) return true;
		}
		return false;
	}

	/**
	* sets the subject of the message
	**/
	this.setSubject = function ( subject ) {
		_subject = subject;
	}

	/**
	* Sets the Body of the Message
	**/
	this.setBody = function ( body ) {
		_body = body.replace ("\n", "%0A");
	}

	/**
	* Construct Mailto String
	**/
	this.toString = function () {
		var _link = ["mailto:"];

		// check to
		if ( _to.length > 0 ) {
			_link.push(_to.join("; "));
		}
		// set the subject ( and add the first arg with ? )
		_link.push("?subject=");
		_link.push(_subject);
		// CHECK CC
		if ( _cc.length > 0 ) {
			_link.push ("&cc=");
			_link.push ( _cc.join("; ") );
		}
		// CHECK BCC
		if ( _bcc.length > 0 ) {
			_link.push ("&bcc=");
			_link.push (_bcc.join("; "));
		}

		if ( _body != "" ) {
			_link.push("&body=");
			_link.push(_body);
		}

		return _link.join("");
	}

}