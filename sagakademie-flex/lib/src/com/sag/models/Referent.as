package com.sag.models 
{
	
	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	[Bindable]
	public class Referent 
	{
		public var id:int;
		public var name:String;
		public var vorname:String;
		public var image:String;
		public function Referent() {
			id = 0;
			name = "Unbekannt";
			vorname = "Unbekannt";
			image = "";
		}
		
	}
	
}