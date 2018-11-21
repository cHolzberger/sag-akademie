/**
 * @file
 *
 * LocalObjectStoreDriver
 * uses localStorage (html5) to store key/value pairs in namespace partitions
 *
 * stores are normaly created through the sandbox! 
 */
dojo.provide( "mosaik.db.LocalObjectStoreDriver" );
dojo.require( "mosaik.core.EventDispatcher" );

if ( typeof ( localStorage ) === "undefined" ) {
	localStorage = {};
}

dojo.declare ( "mosaik.db.LocalObjectStoreDriver", null , {
	requestUpdate: function () {
		// noop for this driver
	},
	
	// event hook, onUpdateDone
	updateDone: function () { 
		
	},
	
	//event hook, onUpdateStart
	updateStart: function () {
		
	},

	commitChanges: function () {
		// noop for this driver
	},

	addKnownKey: function (namespace, key) {
		var keys = localStorage["__" + namespace + "__knownKeys"];

		if ( keys ) {
			keys = $.JSON.decode (keys);
			keys.push ( key );
		} else {
			keys = [];
			keys.push(key);
		}

		localStorage["__" + namespace + "__knownKeys"] = $.JSON.encode( keys );
	},

	removeKnownKey: function ( namespace, key ) {
		var keys = localStorage["__" + namespace + "__knownKeys"] ;
		var newkeys = [];
		for (var i=0; i< keys.length; i++) {

			if ( keys[i] != key ) {
				newkeys.push ( key );
			}
		}
		localStorage["__" + namespace + "__knownKeys"] = $.JSON.encode( newkeys );
	},

	isKnownKey: function (namespace, key) {
		var keys = $.JSON.decode ( localStorage["__" + namespace + "__knownKeys"] );
		if ( !isArray ( keys ) ) return false;

		for ( var i=0; i<keys.length; i++) {
			if ( keys[i] == key ) return true;
		}

		return false;
	},

	getKnownKeys: function (namespace) {
		return $.JSON.decode ( localStorage["__" + namespace + "__knownKeys"] );
	},

	set: function ( namespace, key, value ) {
		var fkey = namespace + "__" + key;
		if ( ! this.isKnownKey ( namespace, key ) ) {
			this.addKnownKey (namespace, key);
		}
		localStorage[fkey] = $.JSON.encode(value);
		return value;
	},

	get: function ( namespace, key , def) {
		var fkey = namespace + "__" + key;

		if  ( typeof (localStorage[fkey]) !== "undefined" ) {
			return $.JSON.decode( localStorage[fkey] );
		} else {
			return def;
		}
	},

	unset: function (namespace, key ) {
		var fkey = namespace + "__" + key;
		delete localStorage[fkey];
	},

	toObject: function (namespace) {
		var keys = this.getKnownKeys ( namespace);
		var obj = {};
		if ( isArray ( keys )) {
			for ( var i=0; i< keys.length; i++) {
				obj[keys[i]] = this.get(namespace, keys[i]);
			}
		}

		return obj;
	}
});
