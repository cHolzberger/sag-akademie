package com.mosaik 
{
	import mx.core.Singleton;
	import mx.collections.ArrayCollection;

	
	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	public class MosaikConfig 
	{
		private static var config:Object = new Object();
		
		public function MosaikConfig() {
		}
		
		public static function setVar(name:String, value:String):void {
			config[name] = value;
		}
		
		public static function getVar(name:String):String {
			if ( config [name] ) {
				return config[name];
			} 
			return "";
		}
		
		public static function setObj(name:String, value:Object):void {
			config[name] = value;
		}
		
		public static function getObj(name:String):Object {
			if ( config [name] ) {
				return config[name];
			} 
			return "";
		}
		
		public static function setAC(name:String, value:ArrayCollection):void {
			config[name] = value;
		}
		
		public static function getAC(name:String):ArrayCollection {
			if ( config [name] ) {
				return config[name];
			} 
			return null;
		}
		
	}
	
}