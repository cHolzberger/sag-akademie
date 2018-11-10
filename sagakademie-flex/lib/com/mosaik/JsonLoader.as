﻿package com.mosaik 
{
	import flash.events.HTTPStatusEvent;
	import mx.controls.Alert;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.http.mxml.HTTPService;
	import json.*;


	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 * this class uses datahost setting from MosaikConfig
	 */
	public class JsonLoader 
	{
		private var httpService:HTTPService = new HTTPService();
		private var resultCallback:Function = null;
		
		public function JsonLoader(url:String = "") {
			httpService.useProxy = false;
			httpService.url = "";
			httpService.method = "POST";
			httpService.resultFormat = "text";
			
			httpService.addEventListener(ResultEvent.RESULT, this.onResult);
			httpService.addEventListener(FaultEvent.FAULT, this.onFault);
			
			setUrl(url);
		}
		
		public function setUrl(url:String):void {
			var urlBase:String = MosaikConfig.getVar ("datahost");
			httpService.url = urlBase + url;
		}
		
		public function request(cb:Function):void {
			resultCallback = cb;
			httpService.send();
			
		}
		
		private function onResult(event:ResultEvent):void {
			var jsonData:Object;
			var resposeString:String = new String(event.result);
			
			jsonData = JParser.decode(resposeString);
			jsonData.response = resposeString;
			if ( resultCallback != null ) {
				resultCallback ( jsonData );
			} else { 
				Alert.show(resposeString);
			}
		}
		
		private function onFault(event:FaultEvent):void {
			Alert.show("Fehler beim Laden der Daten! \n" + httpService.url);
		}	
	}
	
}