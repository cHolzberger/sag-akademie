package com.sag.models 
{
	
	import flash.events.Event;
	import mx.controls.Alert;
	import mx.controls.CheckBox;
	import flash.events.EventDispatcher;
	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	
	[Bindable]
	public class Standort 
	{
		private var _details:Object = new Object();
		public var enabled:Boolean = false;
		public var x:String  = "asd";
		
		public function Standort(details:Object) {
			_details = details;
			if ( _details.planung_aktiv == "1") enabled = true;
			dispatchEvent(new Event("setEnabled"));
		}
		
				
		public function get id():String { 
			return _details.id;
		}
		
		public function get name():String {
			return _details.name;
		}
		
	}
	
}