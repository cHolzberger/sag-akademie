/**
 * @file
 * generic event dispatching
 * abstracted so the toolkit is changeable
 * functions for MosaikEventDispatcher 
 *
 * this one works with JQuery
 **/

dojo.provide("mosaik.core.EventDispatcher");

function MosaikEventDispatcher_initEventHub(e) {
	if ( !isObject ( this._refhub )) this._refhub = {};
	if ( !isObject ( this._evhub )) this._evhub = {};
	if ( ! isFunction (this._evhub[e])) this._evhub[e] = function () {};
	if ( !isObject ( this._refhub[e] )) this._refhub[e] = {};
}

function MosaikEventDispatcher_dispatch (e, data) {
	this.initEventHub(e);
	console.log ("dispatch: <b>"+e+"</b>");
	this._evhub[e]();
}

function MosaikEventDispatcher_addEventListener ( e, handler ) {
	this.initEventHub(e);
	console.log ("listen: <b>" +e+"</b>");
	this.refhub[e] = dojo.connect ( this._evhub, "e", null, handler);
}

function MosaikEventDispatcher_removeEventListener (e, handler) {
	this.initEventHub(e);
	console.log ("remove: " +e);
	dojo.disconnect( this._refhub[e]);
}
/** here **/
dojo.declare ("mosaik.core.EventDispatcher", null, {
		dispatch: MosaikEventDispatcher_dispatch,
		addEventListener: MosaikEventDispatcher_addEventListener,
		removeEventListener: MosaikEventDispatcher_removeEventListener,
		initEventHub: MosaikEventDispatcher_initEventHub
});
