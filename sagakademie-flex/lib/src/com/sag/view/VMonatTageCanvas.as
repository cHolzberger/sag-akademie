package com.sag.view
{
	import flash.display.GraphicsStroke;
	import flash.events.TextEvent;
	import spark.components.Label;
import spark.components.VGroup;

	
	import flash.display.Sprite;
    import flash.display.LineScaleMode;
    import flash.display.CapsStyle;
    import flash.display.JointStyle;
    import flash.display.Shape;
	import mx.controls.Image;
	import spark.components.Group;
	import spark.layouts.BasicLayout;
	import com.sag.models.SPlanungMonat;
	import mx.core.UIComponent;
	import mx.events.FlexEvent;;

		/**
	 * ...
	 * @author MOSAIK Software // Christian Holzberger <ch@mosaik-softaware.de>
	 */
	[Bindable]
	public class VMonatTageCanvas extends Group {
		private var _date:Date = null;
		private var _termine:SPlanungMonat;
		private var _termineChanged:Boolean = false;
		private var _redraw:Boolean = false;
		private var _feiertagLabels:Array = [];
private var _feiertagColor:Number = 0xffffcc;
		private var _label:Array = [];
		private var _dayHeight:int = 15;
		private var _dayWidth:int = 35;
		private var _lineColor:Number = 0xafafaf;
		private var _weekendColor:Number = 0xefefef;
		[Embed(source = "/assets/feiertag.png")]
		static private var _feiertagImage:Class;
		
		/* Main functionality */
		public function VMonatTageCanvas() {
			super();
			this.layout = new BasicLayout();
			width = _dayWidth;
			height = 31 * _dayHeight;
			
			_redraw = false;
		}
		
		override protected function updateDisplayList(unscaledWidth:Number, unscaledHeight:Number):void {
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			drawGrid();
			
		}
		private var _terminOffset:Array;
		public function getDayOffsetHeight(dayNo:int):int {
			return dayNo * this._dayHeight;
		}
				private var _heightSet:Boolean = false;
		private var currentHeight:Number = 0;
		private var _height:Number = 0;
		public function drawGrid ():void {
			if ( ! _redraw ) return;
			if ( ! visible ) return;
			if ( ! _termine ) return;
			var maxDays:Number = new Date (_date.fullYear, _date.month + 1, 0).getDate();
			//Logger.info (_date.fullYear.toString());
			//Logger.info (_date.month.toString());
			this.graphics.clear();
		
			var i:int;
			var countTermine:int;
			//var yOffset:Number = _dayHeight + 6;
		//	var currentHeight:Number = 0;
			var lbl:Label;
			_terminOffset = _termine.calculateOffsets(6, _dayHeight);

			for each (lbl in _label) {
				removeElement(lbl);
			}
			
			_label = [];
			var ft:Image;
			
			for each (ft in _feiertagLabels ) {
				removeElement(ft);
				ft = null;
			}
			_feiertagLabels = [];
				var tmpLabel:Label = null;
			for ( i = 1; i <= maxDays; i++) {
				tmpLabel = new Label();
				countTermine = _termine.countTermine( i.toString() );
				var currentHeight: int = _terminOffset[i][0];
				var yOffset: int = _terminOffset[i][1];				
				tmpLabel.text =  i.toString() + ".";
				//tmpLabel.text =  countTermine.toString();
				tmpLabel.x = 0;
				tmpLabel.explicitWidth = 18;
				tmpLabel.explicitHeight = _dayHeight;
				
				// position text label at the top
				tmpLabel.y = yOffset+5;
				tmpLabel.setStyle("fontSize", 9);
				tmpLabel.setStyle("textAlign", "right");
				

				_label.push(tmpLabel);
				addElement(tmpLabel);
					
				/* weekends */
				this.graphics.lineStyle(1, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
				// feiertag
				var feiertag:String;

				if ( (feiertag = _termine.getFeiertag(i))!= null ) {
					drawFeiertag(yOffset, _dayWidth, currentHeight, feiertag);
					tmpLabel.toolTip = feiertag;

					tmpLabel.setStyle("fontWeight", "bold");
				} else if (_termine.getDate(i).day == 0 || _termine.getDate(i).day == 6) {
					drawWeekend(yOffset,_dayWidth, currentHeight);
				} else {
					drawWeekday(yOffset,_dayWidth, currentHeight);
				}
				
				/* top line */
				this.graphics.lineStyle(1, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
				this.graphics.moveTo(0, yOffset );
				this.graphics.lineTo(this._dayWidth-1, yOffset );
				
				/* left border
				this.graphics.moveTo(0, i * _dayHeight);
				this.graphics.lineTo(0, (i + 1) * _dayHeight);
				*/
				
				/* right border */
				this.graphics.lineStyle(2, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
				this.graphics.moveTo(this._dayWidth-1, yOffset);
				this.graphics.lineTo(this._dayWidth - 1, yOffset + currentHeight);
				
				/* Left border */
				this.graphics.moveTo(0, yOffset);
				this.graphics.lineTo(0, yOffset + currentHeight);

			}
			_height =  _terminOffset[maxDays+1][1];
				
			callLater(updateHeight);
			/* last line */
			this.graphics.lineStyle(1, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
			this.graphics.moveTo(0, yOffset);
			this.graphics.lineTo(this._dayWidth - 1, yOffset);
			_redraw = false;
		}
			public function updateHeight():void {
			if ( _height != height) {
				_heightSet = true;
				height = explicitHeight = _height ;
			}
		}
		
		function drawFeiertag(yOffset, width, height, feiertag) {
			//Logger.info("Feiertag");
					//Logger.info(feiertag);
					var tmpImage:Image = new Image();
					tmpImage.source = _feiertagImage;
					tmpImage.toolTip = feiertag;
					tmpImage.x = 23;
					tmpImage.y = yOffset+3;
					tmpImage.width = 10;
					tmpImage.height = 10;
					//tmpLabel.setStyle("fontSize", 9);
					//tmpLabel.setStyle("textColor", "0xefefef");

					_feiertagLabels.push(tmpImage);
					addElement(tmpImage);
					
					this.graphics.beginFill(_feiertagColor);
					this.graphics.drawRoundRect(0, yOffset, width , height, 3);
					this.graphics.endFill();
		}
		
		function drawWeekday(yOffset, width, height) { 
			this.graphics.beginFill(0xffffff);
					this.graphics.drawRoundRect(0, yOffset, width , height, 3);
					this.graphics.endFill();
		}
		
		function drawWeekend(yOffset, width, height) {
				//Logger.info("Weekend");
					this.graphics.beginFill(_weekendColor);
					this.graphics.drawRoundRect(0, yOffset, width , height, 3);
					this.graphics.endFill();
		}
		
		protected override function createChildren():void {
			super.createChildren();
			//_dayWidth = getExplicitOrMeasuredWidth();
		}
			
		public function redrawAll():void {
			_redraw = true;
			_termineChanged = true;
		}
		
		
		/* setter and getter */
		public function set standort (label:String):void {
			_label.text = label;
		}
		
		public function get standort ():String {
			return _label.text;
		}
		
		public function set termine (termine:SPlanungMonat):void {
			_termine = termine;
			_date = termine.getDate();
			_termineChanged = true;
			_redraw = true;
		}
		
		public function get termine (): SPlanungMonat {
			return _termine;
		}
	}
}