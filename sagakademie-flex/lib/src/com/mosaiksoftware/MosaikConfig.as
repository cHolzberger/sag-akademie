package com.mosaiksoftware
{
	import flash.display.Stage;
	import flash.display.StageQuality;
	
	import mx.collections.ArrayCollection;
	import mx.core.FlexGlobals;
	import mx.core.Singleton;

	
	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	public class MosaikConfig 
	{
		static public var _frameRateInteractive:int = 30;
		static public var _frameRateIdle:int = 5;
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
		
		public static function setInteractive( b:Boolean ):void {
			// Summary: 
			// if true sets the framerate to _frameRateInteractive
			// if false sets the framerate to _frameRateIdle
			// this is used to gain performance 
			var stage:Stage = FlexGlobals.topLevelApplication.stage;
			if (!stage) return;
			if ( b ) {
				
				stage.quality = StageQuality.MEDIUM;
				stage.frameRate = _frameRateInteractive;
			//	frameRate = _frameRateInteractive;
				
			} else {
				stage.quality = StageQuality.MEDIUM;
				
				stage.frameRate = _frameRateIdle;
				//frameRate = _frameRateIdle;
			}
			//if ( _verbose ) 
				///this._log("setting framerate to: " + frameRate );
		}
		
	}
	
}