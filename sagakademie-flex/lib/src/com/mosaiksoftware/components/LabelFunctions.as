package com.mosaiksoftware.components
{
	
	import mx.containers.HBox;
	import mx.controls.AdvancedDataGrid;
	import mx.controls.advancedDataGridClasses.AdvancedDataGridColumn;
	import mx.controls.advancedDataGridClasses.AdvancedDataGridItemRenderer;
	import mx.controls.dataGridClasses.DataGridColumn;
	import mx.core.ClassFactory;
	import mx.core.IFactory;
	import mx.utils.ObjectUtil;
	
	import spark.components.supportClasses.ItemRenderer;

	public class LabelFunctions
	{
		static public var _values:Object = {};
		
		static public function getItemRenderer (type:String, values:Array=null):IFactory {
			switch ( type ) {
				case "link": 
					return new ClassFactory(LinkRenderer);
					break;
				case "status": 
					return new ClassFactory(StatusRenderer);
					break;
				case "bool":
					return new ClassFactory(BoolRenderer);
					break;
				case "email":
					return new ClassFactory(EMailRenderer);
					break;
				default: 
					//return null;
					var c:ClassFactory = new ClassFactory(TextRenderer);
					c.properties = {type: type};
					return c;

			}
		}
		
		static public function getItemEditor  (type:String, values:Array = null):IFactory {
			switch ( type ) {
				case "bool":
					return new ClassFactory(BoolEditor);
					break;
				case "combo":
					var c:ClassFactory = new ClassFactory(ComboEditor);
					c.properties = {type: type, values: values};
					return c;
					break;
				default: 
					return null;
			}			
		}
		
		static public function getLabelFunction (type:String,dataField:String = null, values:Array=null):Function {
			if ( dataField ) { 
				_values[dataField] = values;
			}
			
			switch ( type ) {
				case "date": 
					return dateLabelFunction;
				case "datetime": 
					return datetimeLabelFunction;	
				case "status":
					return statusLabelFunction;
				case "bool": 
					return boolLabelFunction;
				case "combo":
					return comboLabelFunction;
				default:
					return null;
			}
		}
		
		static public function getChangedHandler ( type:String ):Function {
			if ( type =="date" ) 
				return dateChangeFunction ;
			if ( type == "datetime" )
				return datetimeChangeFunction;
			
			return null;
		}
		
		
		/** DATE FUNCTIONS **/
		static public function dateChangeFunction (value:String):String { 
			var dateInfo:Array = value.split(".");
			var year:String = dateInfo[2];
			var month:String = dateInfo[1];
			var day:String = dateInfo[0];
			if ( year == null || month == null || day == null ) return "";
			return year + "-" + month + "-" + day;
		}
		
		static public function dateLabelFunction(item:Object, column:AdvancedDataGridColumn, colName:String = null):String {
			var val:String = "";
		
			if (column == null){
				val = item as String;
			} else {
				val = item[column.dataField];
			}
			
			if (val == null)
				return "";
			else if (val == "")
				return "";
			else if (val == "0000-00-00")
				return "";
			
			var dt:Array = String(val).split(" ");
			var dateInfo:Array = dt[0].split("-");
			var year:String = dateInfo[0];
			var month:String = dateInfo[1];
			var day:String = dateInfo[2];
			if ( day == null || month == null || year ==null) return "" ; 
			return day + "." + month + "." + year;
		}
		
		/** datetime **/
		static public function datetimeLabelFunction (item:Object, column:AdvancedDataGridColumn = null, colName:String = null):String {
				var val:String;
				if (column == null ){
					val = item as String;
				} else {
					val = item[column.dataField];
				}
				
				if (val == null)
					return "";
				else if (val == "")
					return "";
				else if (val == "0000-00-00 00:00")
					return "";
				else if (val == "0000-00-00 00:00:00")
					return "";
				
				var dt:Array = String(val).split(" ");
				var dateInfo:Array = dt[0].split("-");
				var year:String = dateInfo[0];
				var month:String = dateInfo[1];
				var day:String = dateInfo[2];
				
				var timeInfo:Array = dt[1].split(":");
				
				var hour:String = timeInfo[0];
				var minute:String = timeInfo[1];
				
				return day + "." + month + "." + year + " " + hour + ":" + minute;	
			}
			
			
			static public function datetimeChangeFunction (value:String):String { 
				var dateInfo:Array = value.split(".");
				var year:String = dateInfo[2];
				var month:String = dateInfo[1];
				var day:String = dateInfo[0];
				if ( year == null || month == null || day == null ) return "";
				return year + "-" + month + "-" + day;
			}
	
			/** STATUS LABEL **/	
			static public function statusLabelFunction (item:Object, column:AdvancedDataGridColumn = null, colName:String = null):String {
				var val:String;
				if (column == null){
					val = item as String;
				} else {
					val = item[column.dataField];
				}
				
				if (val == "0")
					return "Gebucht"; // vorher "Nicht Bestaetigt" -> 28.04.10;
				if (val == "1")
					return "Gebucht"; //vorher: "Bestaetigt"; -> 28.04.10
				if (val == "2")
					return "Storniert";
				if (val == "3")
					return "Umgebucht";
				if (val == "4")
					return "Gebucht"; // vorher Abgesagt
				if (val == "5")
					return "Nicht teilgen.";
				
				return "UNDEFINIERT!!!";
			}
			/** BOOL **/
			static public function boolLabelFunction (item:Object, column:AdvancedDataGridColumn = null, colName:String = null):String {
				var val:String;
				if (column == null){
					val = item as String;
				} else {
					val = item[column.dataField];
				}
				if (val == "1") {
					return "Ja";
				} return "Nein";
				
			}
			
			static public function comboLabelFunction ( item:Object, column:AdvancedDataGridColumn = null, colName:String = null):String {
				var val:String;
				var values:Array;
				var i:int;
				if (column == null){
					val = item as String;
					values = _values[colName];
				} else {
					values = _values[column.dataField]
					val = item[column.dataField];
				}
				
				for ( i=0; i < values.length; i++ ) {
					if ( values[i].id.toString() == val ) {
						return values[i].name.toString();
					}
				}
				
				return "";
			};
	}
}