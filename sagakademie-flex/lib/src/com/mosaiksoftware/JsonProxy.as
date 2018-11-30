package com.mosaiksoftware
{
//	import com.brokenfunction.json.encodeJson;
///	import com.brokenfunction.json.decodeJson;
//	import com.mosaiksoftware.json.decodeJson;
//	import com.mosaiksoftware.json.encodeJson;
	
	import flash.events.ErrorEvent;
	import flash.events.HTTPStatusEvent;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	
	import mx.controls.Alert;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.http.mxml.HTTPService;

	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 * this class uses datahost setting from MosaikConfig
	 */
	public class JsonProxy
	{
		private var _verbose:Boolean = false;
		private var httpService:HTTPService = null;
		protected var defaultOptions:Object = null;
		private var resultCallback:Function = null;
		private var saveCallback:Function = null
		private var _requestRunning:Boolean = false;
		private var _timer:Timer;
		public var _faultCb:Function = null;
		// the current arguments for the httprequest
		private var _args:Object = null;
		
		static public var appendToUrl:String = "";
		
		public function JsonProxy(url:String = "", defOpt:Object = null) {
			defaultOptions = defOpt;
			
			setUrl(url);
		}
		
		public var currentUrl:String = "";
		public function setUrl(url:String):void {
			var urlBase:String = MosaikConfig.getVar ("datahost");
		
			currentUrl = urlBase + url + appendToUrl;
		}
		
		public function setFaultCb(f:Function):void {
			this._faultCb = f;
		}
		
		public function request(cb:Function, obj:Object = null, method:String = "POST"):void {
		//	GMBus.log("sending request to: " + httpService.url);
			
			if ( this._requestRunning ) {
				GMBus.log("cancel old request");
				if ( httpService) 
					httpService.cancel();
				_requestRunning = false;
			}
			
	
			resultCallback = cb;
			_args = obj;
			
			
			
			sendRequest();
		}
		
		public function sendRequest():void {
			// generate cache string
			var cacheFix:String = "";
			if ( appendToUrl == "") cacheFix = "?_=" + Math.random();
			else cacheFix = "&_=" + Math.random();
			
			// init the request
			if ( httpService != null ) {
				httpService.removeEventListener(ResultEvent.RESULT, this.onResult);
				httpService.removeEventListener(FaultEvent.FAULT, this.onFault);
				httpService.removeEventListener(ErrorEvent.ERROR, this.onError);
			}
			httpService = new HTTPService();
			httpService.showBusyCursor = false;
			httpService.makeObjectsBindable = false;
			httpService.useProxy = false;
			httpService.url = currentUrl + cacheFix;
			
			httpService.showBusyCursor = true;		
			httpService.method = "POST";
			httpService.resultFormat = "text";
			
			httpService.addEventListener(ResultEvent.RESULT, this.onResult);
			httpService.addEventListener(FaultEvent.FAULT, this.onFault);
			httpService.addEventListener(ErrorEvent.ERROR, this.onError);
			
			
			_requestRunning=true;
			if ( _args != null ) {
				for ( var opt:String in defaultOptions ) {
					_args[opt] = defaultOptions[opt];
				}
				if ( _verbose ) 
					GMBus.log("Sending with Data");
				httpService.send(_args);
			} else {
				if ( _verbose ) 
					GMBus.log("Sending without Data");
				httpService.send(defaultOptions);
			}
			
		}
		
		public function get url():String {
			return httpService.url;
		}
		
		public function save(data:Object, method:String = "POST", cb:Function=null):void {
			httpService.method = method;
			var response:Object = new Object();
			response.type = "json";
			response.data = JSON.stringify(data);
			for ( var opt:String in defaultOptions ) {
					response[opt] = defaultOptions[opt];
			}
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
			_requestRunning = false;
			var jsonData:Object;
			if (GMBus.debug) {
				GMBus.log ( event.result as String );
			}
			try {
				jsonData = JSON.parse(event.result as String);
			} catch ( e:SyntaxError  ) {
				trace ( event.result );
			}
		
			if (jsonData.success == false ) {
				//Alert.show("Fehler beim ");
				onFault(null);
			} else if ( resultCallback != null ) {
				
				resultCallback(jsonData);
				//resultCallback ( jsonData );
			} else {
				Alert.show(event.result as String);
			}
		}
		
		private function onFault(event:FaultEvent):void {
			_requestRunning = false;
			if ( faultCb != null ) {
				faultCb (event);
			}
			
			_timer = new Timer(300, 1);
			_timer.addEventListener(TimerEvent.TIMER_COMPLETE, this.reload);
		}
		
		private function onError(event:ErrorEvent):void {
			_requestRunning = false;
			if ( faultCb != null ) {
				faultCb (event);
			}
			
			_timer = new Timer(300, 1);
			_timer.addEventListener(TimerEvent.TIMER_COMPLETE, this.reload);
		}
		
		private function reload():void {
			sendRequest();
		}
	}
	
}