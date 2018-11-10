package com.mosaik
{
	import flash.events.HTTPStatusEvent;
	import mx.controls.Alert;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.http.mxml.HTTPService;
	import json.*;
	import json.JParser;
	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 * this class uses datahost setting from MosaikConfig
	 */
	public class JsonProxy
	{
		private var httpService:HTTPService = new HTTPService();
		private var resultCallback:Function = null;
		private var saveCallback:Function = null;
		
		public function JsonProxy(url:String = "") {
			httpService.showBusyCursor = false;
			httpService.makeObjectsBindable = true;
			httpService.useProxy = false;
			httpService.url = "";
			
			httpService.method = "POST";
			httpService.resultFormat = "text";
			
			httpService.addEventListener(ResultEvent.RESULT, this.onResult);
			httpService.addEventListener(FaultEvent.FAULT, this.onFault);
			
			setUrl(url);
		}
		
		public function setUrl(url:String):void {
			httpService.method = "POST";
			var urlBase:String = MosaikConfig.getVar ("datahost");
			httpService.url = urlBase + url;
		}
		
		public function request(cb:Function, obj:Object=null, method:String="POST"):void {
			httpService.method = method;
			resultCallback = cb;
			
			if ( obj != null ) {
				httpService.send(obj);
			} else {
				httpService.send();
			}
		}
		
		public function get url():String {
			return httpService.url;
		}
		
		public function save(data:Object, method:String = "POST", cb:Function=null):void {
			httpService.method = method;
			var response:Object = new Object();
			response.type = "json";
			response.data = JParser.encode(data);
			//Alert.show("Sende daten...");
			resultCallback = onSaveResult;
			saveCallback = cb;
			httpService.send(response);
		}
		
		private function onSaveResult(data:Object): void {
			//Alert.show("Daten gespeichert. Bitte die Seite erneut Laden - F5");
			if ( saveCallback != null ) {
				saveCallback ( data );
			}
		}
		
		private function onResult(event:ResultEvent):void {
			var jsonData:Object;
			jsonData = JParser.decode(event.result as String);
		
			if ( resultCallback != null ) {
				resultCallback(jsonData);
				//resultCallback ( jsonData );
			} else {
				Alert.show(event.result as String);
			}
		}
		
		private function onFault(event:FaultEvent):void {
			Alert.show("Fehler beim Laden der Daten! \n" + httpService.url);
		}
	}
	
}