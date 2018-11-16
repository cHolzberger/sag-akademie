package com.mosaiksoftware
{
	import flash.external.ExternalInterface;
	
	import mx.controls.Alert;
	import mx.core.Singleton;

	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	public class XSettings 
	{
		public function XSettings() {
		}
		
		static public function setVar(nspace:String, name:String, value:String):void {
			//Alert.show("setting " + nspace + " " + name + " " + value);
			if ( ExternalInterface.available ) { 
				ExternalInterface.call("_flexUserSettingSet", nspace, name, value);
			}
		}
		
		static public function getVar(nspace:String, name:String,def:Object = null):Object {
			if ( ExternalInterface.available ) {
				return ExternalInterface.call("_flexUserSettingGet", nspace, name, def);
			} else {
				return def;
			}
		}
		
		static public function getInt(nspace:String, name:String,def:int = 0):int {
			if ( ExternalInterface.available ) { 
				
				var obj:Object = ExternalInterface.call("_flexUserSettingGet", nspace, name, def);
				if ( obj && obj.value ) { 
					return parseInt ( obj.value);
				} else { 
					//Alert.show("setting undef" + nspace + " " + name );
					return def;
				}
			} return def;
		}
		
		static public function getBool(nspace:String, name:String,def:Boolean = true):Boolean {
			if ( ExternalInterface.available ) {
				var obj:Object = ExternalInterface.call("_flexUserSettingGet", nspace, name, def);
				if ( obj && obj.value ) { 
					
					return obj.value != "false";
				} else { 
					//Alert.show("setting undef" + nspace + " " + name );
					return def;
				}
			} return def;
		}
		
	}
	
}