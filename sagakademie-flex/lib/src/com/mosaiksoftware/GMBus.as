package com.mosaiksoftware
{
	//import com.brokenfunction.json.encodeJson;
	//import com.mosaiksoftware.json.encodeJson;
	
	import flash.external.ExternalInterface;
	
	import mx.controls.Alert;
	import com.mosaiksoftware.AssetCollection;
	import mx.containers.HBox;

	public class GMBus {
		//Summary:
		// Global message bus 
		// routes messages on the global bus, 
		// forwards them to dojo if the ExternalInterface is available
		public static var debug:Boolean = false;
		
		// dirty Hack
		public static var calWindow:HBox;
		
		public function GMBus()
		{
	
		}
				
		static public function publish (topic:String, data:Array=null):void {
			
				if ( data == null ) {
					log("Data is null");

					if ( ExternalInterface.available ) { ExternalInterface.call("_flexPublish", {topic: topic}) }
					
				} else  {
					log("Data is array");
										
					if ( ExternalInterface.available ) { ExternalInterface.call("_flexPublish", {topic: topic, format: "array", data: JSON.stringify(data).split("\\").join("#/")});}
				}
				// check if data comes in correctly
				// else use this: 				ExternalInterface.call("_flexSignal", topic, [JParser.encode(data)]);

		}
		
		static public function subscribe ( topic:String, callback:Function ):void {
			// TODO
		}
		
		static public function log(message:String):void {
			// Summary:
			// log message using the message bus
			
			if ( ExternalInterface.available ) {
				ExternalInterface.call("console.log", "Flex => " + message);
			} else {
				trace ( message );
			}
		}
	}
}