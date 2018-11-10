package com.mosaiksoftware
{
	import flash.external.ExternalInterface;
	import json.JParser;
	
	import mx.controls.Alert;

	public class GMBus
	{
		public function GMBus()
		{
		}
		
		static public function publish (topic:String, data:Object=null):void {
			if ( ExternalInterface.available ) {
				ExternalInterface.call("_flexSignal", topic, [JParser.encode(data)]);
			}
		}
		
		static public function subscribe ( topic:String, callback:Function ):void {
			// TODO
		}
		
		static public function log(message:String):void {
			// Summary:
			// log message using the message bus
			
			if ( ExternalInterface.available ) {
				ExternalInterface.call("console.log", message);
			} else {
				trace ( message);
			}
		}
	}
}