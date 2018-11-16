package com.sag.models
{
	import flash.events.Event;
	import com.sag.models.SPlanungMonat;
	import mx.collections.ArrayCollection;
	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	[Bindable]
	[Event(name="dataChanged", type="flash.events.Event")]
	public class SPlanungTag 
	{			
		public var tag: Number;
		public var monat: Number;
		public var color: Number;
		public var _termine: SPlanungMonat;
		
		
		public function SPlanungTag(dtag:Number, dcolor:Number, termine:SPlanungMonat) {
			tag = dtag;
			color = dcolor;
			monat = termine.monat;
			_termine = termine;
		}
		
		public function set termine(t:Object):void {
			_termine[tag] = t;
			dispatchEvent(new Event("dataChanged"));
		}
		
		[Bindable("dataChange")]
		public function get termine(): Object {
			return _termine[tag];
		}
	}
}