/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
;;
function MosaikDataStore() {

	this.get = function (name) {
		if ( $("#" + name )[0] !== undefined) {
			return $('#'+name)[0].value;
		} return false;
	}

	this.set = function (name, value) {
		if ($("#" + name )[0] !== undefined ) {
			return $("#" + name)[0].value = value;
		} return false;
	}

	this.bind = function ( name, target) {
		alert ("Bind data not implemented");
	}
};



$.extend({
	mosaikDs: new MosaikDataStore()
});
