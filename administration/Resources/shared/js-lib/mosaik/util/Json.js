/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

dojo.provide("mosaik.util.Json");

mosaik.util.Json.decode = function ( jsonData ) {
	if ( JSON ) {
		return JSON.parse(jsonData);
	} else {
		return dojo.fromJson (jsonData);
	}
}

mosaik.util.Json.encode = function ( objData  ) {
	if ( JSON ) {
		return JSON.stringify ( objData );
	} else {
		return dojo.toJson( objData);
	}
}
