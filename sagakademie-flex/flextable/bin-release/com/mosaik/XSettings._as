package com.mosaik 
{
	import mx.core.Singleton;
	import flash.external.ExternalInterface;
	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	public class XSettings 
	{
		public function XSettings() {
		}
		
		public function setVar(nspace:String, name:String, value:String):void {
			ExternalInterface.call("$.xSettings.set", nspace, name, value);
		}
		
		public function getVar(nspace:String, name:String,def:Object = null):Object {
			return ExternalInterface.call("$.xSettings.get", nspace, name, def);
		}
		
	}
	
}