package com.sag.view
{
	import flash.display.GraphicsStroke;
	import flash.events.TextEvent;
	import mx.containers.Canvas;
	import mx.controls.Text;
	
	import flash.display.Sprite;
    import flash.display.LineScaleMode;
    import flash.display.CapsStyle;
    import flash.display.JointStyle;
    import flash.display.Shape;
	
	import com.sag.models.SPlanungMonat;
	import org.osflash.thunderbolt.Logger;


		/**
	 * ...
	 * @author MOSAIK Software // Christian Holzberger <ch@mosaik-softaware.de>
	 */
	[Bindable]
	public class VMonatTageCanvas extends Canvas {
		private var _date:Date = null;
		private var _termine:SPlanungMonat;
		private var _termineChanged:Boolean = false;
		private var _redraw:Boolean = false;


		private var _label:Array = [];
		private var _dayHeight:int = 15;
		private var _dayWidth:int = 25;
		private var _lineColor:Number = 0xafafaf;
		private var _weekendColor:Number = 0xefefef;
		
		/* Main functionality */
		public function VMonatTageCanvas() {
			super();
			
		}
		
		public function getDayOffsetHeight(dayNo:int):int {
			return dayNo * this._dayHeight;
		}
		
		public function drawGrid ():void {
			if ( ! _termine ) return;
			var maxDays:Number = new Date (_date.fullYear, _date.month + 1, 0).getDate();
			
			//Logger.info (_date.fullYear.toString());
			//Logger.info (_date.month.toString());
			this.graphics.clear();
			
			var tmpLabel:Text = null;
			
			var i:int;
			var countTermine:int;
			var yOffset:Number = _dayHeight;
			var currentHeight:Number = 0;
			var lbl:Text;
			
			for each (lbl in _label) {
				removeChild(lbl);
			}
			
			_label = [];
		
			
			for ( i = 1; i <= maxDays; i++) {
				tmpLabel = new Text();
				countTermine = _termine.countTermine( i.toString() );
				
				if (countTermine != 0 ) {
					currentHeight = _dayHeight * countTermine;
				} else {
					currentHeight = _dayHeight;
				}
				
				tmpLabel.text =  i.toString() + ".";
				//tmpLabel.text =  countTermine.toString();
				tmpLabel.x = 0;
				tmpLabel.width = 24;
				
				// position text label at the top
				tmpLabel.y = yOffset;
				tmpLabel.setStyle("fontSize", 9);
				tmpLabel.setStyle("textAlign", "right");
				

				_label.push(tmpLabel);
				addChild(tmpLabel);
					
				/* weekends */
				this.graphics.lineStyle(1, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
				
				if (_termine.getDate(i).day == 0 || _termine.getDate(i).day == 6) {
					//Logger.info("Weekend");
					this.graphics.beginFill(_weekendColor);
					this.graphics.drawRoundRect(0, yOffset, _dayWidth , currentHeight, 3);
					this.graphics.endFill();
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

				yOffset = yOffset + currentHeight;
			}
			/* last line */
			this.graphics.lineStyle(1, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
			this.graphics.moveTo(0, yOffset);
			this.graphics.lineTo(this._dayWidth-1, yOffset);
		}
		
		protected override function createChildren():void {
			super.createChildren();
			//_dayWidth = getExplicitOrMeasuredWidth();
			this.drawGrid();
		}
		
		protected override function updateDisplayList(unscaledWidth: Number, unscaledHeight:Number):void {
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			if (_redraw ) {
				drawGrid();
				_redraw = false;
			}
		}
		
		public function redrawAll():void {
			_redraw = true;
			_termineChanged = true;
		}
		
		public override function invalidateProperties():void {
			super.invalidateProperties();
			
			if (_termineChanged) {
				_redraw = true;
				_termineChanged = false;
			}
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