package com.mosaiksoftware.components
{
	import com.mosaiksoftware.components.LabelFunctions;
	
	import flash.external.ExternalInterface;
	
	import mx.collections.ArrayCollection;
	import mx.controls.Alert;

	public class TableParameters
	{
		[Bindable]
		public static var showHeader:Boolean = true;
		[Bindable]
		public static var showFooter:Boolean = true;
		
		[Bindable]
		public static var showSave:Boolean = false;
		
		[Bindable]
		public static var columns:Array = null;
		
		//test parameter
		// http://sag-akademie.localhost/admin/json;json/buchung/ - buchungen
		public static var dsUrl:String = "http://sag-akademie.localhost/admin/json;json/person/";
		public static var dsParams:Object = { username: "admin", password: "trust","buchungenDarmstadt": 1, "version": 2,"q": "heinz"};
		
		public static var availableColumns:Array = null;
		
		// COLOR CODES:
		[Bindable]
		public static var linkColor:Number = 0x0000aa;
		public static var nspace:String = "SeminarTerminTable";
		
		// status color codes
		public static var statusGreen:Number=0x00ff00;
		public static var statusRed:Number=0xff0000;
		public static var statusYellow:Number=0xffff00;
		public static var statusOrange:Number=0xFF9900;
		
		public static function getType(fieldName:String):String {
			var i:int=0, count:int =0;
			for ( i=0, count = availableColumns.length; i < count; i++ ) {
				if ( availableColumns[i].field == fieldName ) 
					return availableColumns[i].format;
			}
			return null;
		}
		
		public static function getVisible(fieldName:String):Boolean {
			var i:int=0, count:int =0;
			for ( i=0, count = availableColumns.length; i < count; i++ ) {
				if ( availableColumns[i].field == fieldName ) 
					return !( TableParameters.availableColumns[i].hide == true || TableParameters.availableColumns[i].hide == "true");
			}
			return true;
		}
		
		public static function openLink(url:String ):void {
			//Alert.show(url);
			ExternalInterface.call("$.mosaikRuntime.load", url);
		}
		
		public static function isEditable(field:String):Boolean {
			for each ( var head:Object in availableColumns ) {
				if (head.field == field && head.editable == "true" ) {
					return true;
				}
			}
			return false;
		}
		
		public static function exportCsv(ds:ArrayCollection):String {
			var i:int =0;
			var j:int =0;
			var csvData:String ="";
			var tmp:Array = [];
			var key:String;
			var tmpString:String;
			var labelFn:Object = {};
			var separator:String = ";";
			/** WRITE THE HEADER **/
			for ( i=0; i < TableParameters.availableColumns.length; i++ ) {
				if ( TableParameters.availableColumns[i].hide == true || TableParameters.availableColumns[i].hide == "true" ) {
					continue;
				}
				var ci:Object = TableParameters.availableColumns[i]; 
				tmp.push (  "\"" + ci.label.replace("\"","\"\"") + "\"");
				labelFn[ ci.field] = LabelFunctions.getLabelFunction( ci.format, ci.field, ci.values);
			}
			csvData = tmp.join(separator);
			csvData += "\n";
			/** WRITE DATA **/
			
			for ( j=0; j < ds.length; j++) {
				tmp = [];
				
				for ( i=0; i < TableParameters.availableColumns.length; i++ ) {
					// SKIP HIDDEN
					if ( TableParameters.availableColumns[i].hide == true || TableParameters.availableColumns[i].hide == "true" ) {
						continue;
					}
					// LOOKUP KEY
					key = TableParameters.availableColumns[i].field;
					tmpString = ""; 
					
					if ( ds[j][key] ) {
						// FIND LABEL FUNCTION
						if ( labelFn[key] ) {
							// labelFn signature is kind of weird
							tmpString = labelFn[key] ( ds[j][key], null, key );
						} else {
							tmpString = ds[j][key];
						}
					}
					// push it to the csv array
					
					tmpString = tmpString.replace(new RegExp("[\n\r]","g"),"<br>");
					tmpString = tmpString.replace(new RegExp("\"","g"),"\"\"");
					tmpString = "\"" + tmpString + "\"";
					var crlf:String = String.fromCharCode(13, 10);
					var regEx:RegExp = new RegExp(crlf, "g");
					tmpString = tmpString.replace(regEx,"");
					tmp.push(tmpString);
				}
				
				csvData += tmp.join(separator);
				csvData += "\n";
			}
			return csvData;
		}
		
		public function TableParameters()
		{
		}
	}
}