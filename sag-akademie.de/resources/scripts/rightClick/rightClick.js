/**
 * 
 * Copyright 2007-2009
 * 
 * Paulius Uza
 * http://www.uza.lt
 * 
 * Dan Florio
 * http://www.polygeek.com
 * 
 * Arif Ali Saiyed
 * http://arif-ali-saiyed.blogspot.com/
 * 
 * Project website:
 * http://code.google.com/p/custom-context-menu/
 * 
 * --
 * RightClick for Flash Player. 
 * Version 0.6.3
 * 
 */

var RightClick = {
	x: 0,
	y: 0,
	registered: false,

	/**
	 *  Constructor
	 */ 
	init: function (bridge) {
		this.FlashObjectID = "flexApp";
		this.FlashContainerID = "imain";
		this.Cache = this.FlashObjectID;
		this.bridge = bridge;

		if ( this.registered ) return;
		if(window.addEventListener){
			 window.addEventListener("mousedown", this.onGeckoMouse(), true);
		} else {
			document.getElementById(this.FlashContainerID).onmouseup = function() { document.getElementById(RightClick.FlashContainerID).releaseCapture(); }
			document.oncontextmenu = function(){ if(window.event.srcElement.id == RightClick.FlashObjectID) { return false; } else { RightClick.Cache = "nan"; }}
			document.getElementById(this.FlashContainerID).onmousedown = RightClick.onIEMouse;
		}
		this.registered=true;
	},
	/**
	 *  Disable the Right-Click event trap  and continue showing flash player menu
	 */ 
	UnInit: function () { 
	    //alert('Un init is called' );			
		if(window.RemoveEventListener){
			alert('Un init is called for GECKO' );			
			window.addEventListener("mousedown", null, true);
			window.RemoveEventListener("mousedown",this.onGeckoMouse(),true);
			 //w//indow.releaseEvents("mousedown");
		} else {
			//alert('Un init is called for IE' );							
			document.getElementById(this.FlashContainerID).onmouseup = "" ;
			document.oncontextmenu = "";
			document.getElementById(this.FlashContainerID).onmousedown = "";
		}
	},

	/**
	 * GECKO / WEBKIT event overkill
	 * @param {Object} eventObject
	 */
	killEvents: function(eventObject) {
		this.x = eventObject.clientX;
		this.y = eventObject.clientY;
		if(eventObject) {
			if (eventObject.stopPropagation) eventObject.stopPropagation();
			if (eventObject.preventDefault) eventObject.preventDefault();
			if (eventObject.stopImmediatePropagation) eventObject.stopImmediatePropagation();

			if (eventObject.preventCapture) eventObject.preventCapture();
	   		if (eventObject.preventBubble) eventObject.preventBubble();
			
		}
	},
	/**
	 * GECKO / WEBKIT call right click
	 * @param {Object} ev
	 */
	onGeckoMouse: function(ev) {
	  	return function(ev) {

	       if (ev.button == 2) {
			if(ev.target.id == RightClick.FlashObjectID && RightClick.Cache == RightClick.FlashObjectID) {
	    			RightClick.killEvents(ev);
		    		RightClick.call();
				return true;
			}
			RightClick.Cache = ev.target.id;
			return true;
		}
	  }
	},
	/**
	 * IE call right click
	 * @param {Object} ev
	 */
	onIEMouse: function() {
	  	if (event.button > 1) {
			if(window.event.srcElement.id == RightClick.FlashObjectID && RightClick.Cache == RightClick.FlashObjectID) {
				RightClick.call(); 
			}
			document.getElementById(RightClick.FlashContainerID).setCapture();
			if(window.event.srcElement.id)
			RightClick.Cache = window.event.srcElement.id;
		}
	},
	/**
	 * Main call to Flash External Interface
	 */
	call: function() {
		//document.getElementById(this.FlashObjectID).rightClick();
		FABridge[this.bridge].root().rightClick();
	}
}