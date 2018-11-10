package com.sag.models
{
	import mx.collections.ArrayCollection;
	
	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	[Bindable]
	public class PlanungTag 
	{
			
		public var tag:int;
		public var data:ArrayCollection; 
		
		public function PlanungTag() {
			tag = 0;
			data = new ArrayCollection (new Array())
		}
		
	}
	
}