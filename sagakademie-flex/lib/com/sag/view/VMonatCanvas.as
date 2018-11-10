package com.sag.view
{
	import flash.display.FrameLabel;
	import flash.display.GraphicsStroke;
	import flash.events.TextEvent;
	import flash.ui.ContextMenu;
	import flash.ui.ContextMenuBuiltInItems;
	import flash.ui.ContextMenuItem;
	import mx.containers.Canvas;
	import mx.controls.Text;
	import mx.controls.Label;
	
	import flash.display.Sprite;
    import flash.display.LineScaleMode;
    import flash.display.CapsStyle;
    import flash.display.JointStyle;
    import flash.display.Shape;
	
	import com.sag.models.SPlanungMonat;
	import com.sag.models.Termin;
	import com.sag.models.Standort;


	import com.sag.view.PlanungTag;
	
	import org.osflash.thunderbolt.Logger;
	import mx.binding.utils.*;
	import mx.managers.DragManager;
	import mx.events.*;
	import mx.core.DragSource;
	
	import com.mosaik.MosaikConfig;
	import mx.collections.ArrayCollection;
	import mx.containers.Box;
	import flash.external.*;
	import flash.events.MouseEvent;
	import mx.controls.Alert;
	 import flash.events.ContextMenuEvent;
	 
	 import com.mosaiksoftware.GMBus;
	import flex.layouts.ConstraintLayout;
	import mx.containers.Canvas;

	/**
	 * ...
	 * @author MOSAIK Software // Christian Holzberger <ch@mosaik-softaware.de>
	 */
	[Bindable]
	public class VMonatCanvas extends Canvas {
		public var redraw:Function = null;
		
		private var _date:Date = null;
		
		private var _watcher:ChangeWatcher = null;
		
		public var _termine:SPlanungMonat;
		private var _termineChanged:Boolean = false;
		private var _feiertagLabels:Array = [];
		private var _termineObj:Array = [];
		private var _notizenObj:Array = [];
		
		private var _terminOffset:Object = {};
		
		private var _standort:Standort = null;
		private var _standortChanged:Boolean = false;
		private var _label:Text = null;
		private var _dayHeight:int = 15;
		private var _dayWidth:int = 120;
		
		private var _lineColor:Number = 0xafafaf;
		private var _weekendColor:Number = 0xefefef;
		private var _feiertagColor:Number = 0xffffcc;
		private var _highlight:Box = null;
		
		private var _drawGrid:Boolean = true;
		
		private var _showReferent:Boolean = false;
		private var _showTeilnehmer:Boolean = false;
		
		
		private static var count:Number = 0;// fuer neue seminare

		import com.sag.view.PlanungTag;

		public function set showReferent(val:Boolean):void {
			_showReferent = val;
			
			var term:PlanungTag;
			for each (term in _termineObj) {
				term.showReferent = val;
			}
			
			var notiz:Text;
			for each ( notiz in _notizenObj) {
				notiz.visible = !val;
			}
		}
		
		public function get showReferent ():Boolean {
			return _showReferent;
		}
		
		public function set showTeilnehmer(val:Boolean):void {
			_showTeilnehmer = val;
			
			var term:PlanungTag;
			for each (term in _termineObj) {
				term.showTeilnehmer = val;
			}
			
			var notiz:Text;
			for each ( notiz in _notizenObj) {
				notiz.visible = !val;
			}
		}
		
		public function get showTeilnehmer ():Boolean {
			return _showTeilnehmer;
		}
		
		/* Main functionality */
		public function VMonatCanvas() {
			super();
			this.layout = new  ConstraintLayout();
		}

		private function menuSelectHandler(event:ContextMenuEvent):void {
            //Alert.show("menuSelectHandler: " + event);
        }
		
		private function menuGotoTermin ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			GMBus.publish("kalender/gotoTermin", { "terminId": tag.termin.id.toString() } );
			//ExternalInterface.call("$.mosaikRuntime.load","/admin/termine/" + tag.termin.id.toString() + "?edit" );
			
		}
		
		private function menuGotoSeminar ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			GMBus.publish("kalender/gotoSeminar", { "seminarId": tag.termin.name.toString() });
			//ExternalInterface.call("$.mosaikRuntime.load","/admin/seminare/" + tag.termin.name.toString() + "?edit" );
		}
		
		private function menuGotoReferenten ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			GMBus.publish("kalender/gotoReferenten", { "seminarId": tag.termin.id.toString(), "standortId": tag.standort.id});
			//ExternalInterface.call("$.mosaikRuntime.load","/admin/planung/termin?standort_id=" + tag.standort.id +"&seminar_id=" + tag.termin.id.toString());
			
		}

		public function getDayOffsetHeight(dayNo:int, num:int = 0 ):int {
			//Logger.info("Tmp: ", dayNo.toString());
			return _terminOffset[dayNo] + num*_dayHeight;
		}
		
		public function getDayForOffset(offset:int):int {
			var maxDays:Number = new Date (_date.fullYear, _date.month + 1, 0).getDate();
			var day:int = 1;
			for ( var i:int = 1; i <= maxDays; i++ ) {
				if ( _terminOffset[i] < offset ) {
					day = i;
				} else {
					break;
				}
			}

			return day;
		}
		
		public function drawGrid ():void {
			
			if ( ! _termine ) return;
			
			this.graphics.clear();
			
			var maxDays:Number = new Date (_date.fullYear, _date.month+1, 0).getDate();
			
			this.graphics.lineStyle(1, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
			var i:int;
			var feiertag:String;
			
			
			// background
			this.graphics.lineStyle(0, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
			this.graphics.beginFill(0xffffff);
			this.graphics.drawRoundRect(0, 0, _dayWidth , (maxDays+20) * _dayHeight, 3);
			this.graphics.endFill();
			
			// offsets etc.
			var countTermine:int;
			var yOffset:Number = _dayHeight;
			var currentHeight:Number = 0;
			_terminOffset[0] = 0;
			
			var ft:Text;
			
			for each (ft in _feiertagLabels ) {
				removeChild(ft);
				ft = null;
			}
			_feiertagLabels = [];
			
			for ( i = 1; i <= maxDays ; i++) { // 1 to maxdays
			
				countTermine = _termine.countTermine(i.toString());
				if (countTermine != 0 ) {
					currentHeight = _dayHeight * countTermine;
				} else {
					currentHeight = _dayHeight;
				}
			
				_terminOffset[i] = yOffset;
				
				this.graphics.lineStyle(1, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
				
				// feiertag
				if ( (feiertag = _termine.getFeiertag(i))!= null ) {
					//Logger.info("Feiertag");
					//Logger.info(feiertag);
					var tmpLabel:Text = new Text();
					tmpLabel.text = feiertag;
					tmpLabel.x = 5;
					tmpLabel.y = yOffset;
					tmpLabel.setStyle("fontSize", 9);
					tmpLabel.setStyle("textColor", "0xefefef");
					
					_feiertagLabels.push(tmpLabel);
					addChild(tmpLabel);
					
					this.graphics.beginFill(_feiertagColor);
					this.graphics.drawRoundRect(0, yOffset, _dayWidth , currentHeight, 3);
					this.graphics.endFill();
				} else if (_termine.getDate(i).day == 0 || _termine.getDate(i).day == 6) {
					/* weekends */
					
					//Logger.info("Weekend");
					this.graphics.beginFill(_weekendColor);
					this.graphics.drawRoundRect(0, yOffset, _dayWidth , currentHeight, 3);
					this.graphics.endFill();
				}
				
				/* top line */
				this.graphics.moveTo(0, yOffset);
				this.graphics.lineTo(this._dayWidth-1, yOffset);
				
				/* left border
				this.graphics.moveTo(0, i * _dayHeight);
				this.graphics.lineTo(0, (i + 1) * _dayHeight);
				*/
				
				/* right border */
				this.graphics.moveTo(this._dayWidth-1, yOffset);
				this.graphics.lineTo(this._dayWidth - 1, yOffset + currentHeight);

				yOffset = yOffset + currentHeight;
			}
			/* last line */
			this.graphics.moveTo(0, yOffset);
			this.graphics.lineTo(this._dayWidth-1, yOffset);
		}
		
		public var _notizMap:Object = new Object();
		
		public function drawTermine():void {
			if ( ! _termine ) return;
			
			var maxDays:Number = new Date (_date.fullYear, _date.month+1, 0).getDate();
			var i:Number;
			for ( i = 0; i < _termineObj.length ; i++ ) {
				removeChild (_termineObj[i]);
				_termineObj[i] = null;
			}
			
			for ( i = 0; i < _notizenObj.length; i++ ) {
				removeChild(_notizenObj[i]);
				_notizenObj[i] = null;
			}
			
			_termineObj = [];
			_notizenObj = [];
			
			var textColor:Number = 0;
			var j:int = 0;
			for ( i = 1; i <= maxDays; i++) {
				var termin:Termin;
				var terminList:Array;
				textColor = 0;
				
				if ( (terminList = _termine.getTermin(i.toString(), _standort.id)) != null ) {
					for ( j = 0; j < terminList.length; j++ ) {
						termin = terminList[j];
						if ( termin == null ) continue;
						var tmp:PlanungTag = new PlanungTag;
						tmp.y = getDayOffsetHeight(i, j);
						tmp.showReferent = showReferent;
						tmp.tag = i.toString();
						tmp.termine = _termine;

						tmp.standort = _standort;
						tmp.termin = termin;
					
						textColor = termin.fontColor;
					
						// popup / rechts klick menu konfigurieren
						var customMenu:ContextMenu = new ContextMenu();
						
			
						customMenu.hideBuiltInItems();
						
						var defaultItems:ContextMenuBuiltInItems = customMenu.builtInItems;
						defaultItems.print = true;
						
						var terminItem:ContextMenuItem = new ContextMenuItem("Zum Termin");
						terminItem.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, menuGotoTermin);	
						
						var seminarItem:ContextMenuItem = new ContextMenuItem("Zum Seminar");
						seminarItem.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, menuGotoSeminar);	
						
						var referentenItem:ContextMenuItem = new ContextMenuItem("Zu den Referenten");
						referentenItem.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, menuGotoReferenten);	
						
						
						customMenu.addEventListener(ContextMenuEvent.MENU_SELECT, menuSelectHandler);
						customMenu.customItems.push(terminItem);
						customMenu.customItems.push(seminarItem);
						customMenu.customItems.push(referentenItem);
						//customMenu.addEventListener(ContextMenu.)
						
						tmp.contextMenu = customMenu;
						
						_termineObj.push(tmp);
						addChild(tmp);
					}
				}
				
				
			}
			
			var notiz:Object = null;
			
			for ( i = 1; i <= maxDays; i++) {

				if ( (terminList = _termine.getTermin(i.toString(), _standort.id)) != null ) {
					if ( terminList[0] != null) {
						textColor = terminList[0].fontColor;
					} else {
						textColor = 0x000000;
					}
				} else {
					textColor = 0x000000;
				}

				
				if ( (notiz = _termine.getNotiz(i.toString(), _standort.id )) != null ) {
						// notizen
				//		Logger.info("notiz");
						var notizLabel:Text = new Text();
						notizLabel.text =  notiz.notiz;
						notizLabel.x = _dayWidth - 40;
						notizLabel.width = 40;
						notizLabel.y = getDayOffsetHeight(i);
						notizLabel.setStyle("fontSize", 9);
						notizLabel.setStyle("textAlign", "right");
						notizLabel.setStyle("color", textColor);
						//notizLabel.addEventListener(MouseEvent.MOUSE_UP, onNotizClick);
						
						_notizenObj.push(notizLabel);
						addChild(notizLabel);
						
						//_notizMap[notizLabel.toString()].tag = i.toString();
						//_notizMap[notizLabel.toString()].standort_id = _standort.id;
					}
			}

		}
		private var ib:NoteInput = null;
		
		private function onClick(e:MouseEvent):void {
			var x:int = e.currentTarget.contentMouseX;
			var y:int = e.currentTarget.contentMouseY;
			if ( ib != null ) {
				return; // nur ein popup auf einmal
			}
						
			//Logger.info ( e.target.toString() );
			//Logger.info ( e.currentTarget.toString() );
			if ( x > _dayWidth - 40 && x < _dayWidth ) {
				var day:int = getDayForOffset(y);
				var offset:int = getDayOffsetHeight(day);
				ib = null;
				
				ib = new NoteInput();
				ib.tag = day.toString();
				ib.standort = _standort.id;
				ib.callback = onSetNote;
				var note:Object = _termine.getNotiz(day.toString(), _standort.id.toString());
				if ( note != null ) {
					ib.text = note.notiz;
				}
				ib.y = offset - _dayHeight;
				ib.x = 5;
				addChild(ib);
			}
			
		}
		
		private function onSetNote(tag:String, standort:String, txt:String):void {
			_termine.setNotiz(tag, standort, txt);
			
			removeChild(ib);
			ib = null;
			
			callLater(redraw());
		}
		
		protected override function createChildren():void {
			super.createChildren();
			
			addEventListener(DragEvent.DRAG_ENTER, dragEnterHandler);
			addEventListener(DragEvent.DRAG_OVER, dragEnterHandler);
			addEventListener(MouseEvent.MOUSE_UP, onClick);

			addEventListener(DragEvent.DRAG_DROP, dragDropHandler);
			//_dayWidth = getExplicitOrMeasuredWidth();
			_label = new Text();
			_label.text = "StandortLabel";
			_label.x = 0;
			_label.y = 0;
			
			addChild(_label);
			
			_highlight = new Box();
			_highlight.visible = false;
			_highlight.width = _dayWidth;
			_highlight.height = _dayHeight;
			_highlight.graphics.moveTo (0, 0);
			_highlight.graphics.beginFill(_weekendColor);
			_highlight.graphics.drawRoundRect(0, _dayHeight, _dayWidth , _dayHeight, 3);
			_highlight.graphics.endFill();
			//_highlight.graphics.opaqueBackground = 0x00ee00;
		}
		
		public function redrawAll():void {
			_drawGrid = true;
				_termineChanged = true;
				_standortChanged = true;
		}
		
		public override function invalidateProperties():void {
			super.invalidateProperties();
			
			if ( _drawGrid && _termine) {
				drawGrid();
				_drawGrid = false;
			}
			
			if (_termineChanged) {
				//Logger.info("invalidate");
				callLater(drawTermine);
				_termineChanged = false;
			}
			
			if ( _standortChanged) {
				_label.text = _standort.name;
				_standortChanged = false;
			}
		}
		
		/* setter and getter */
		public function set standort (standort:Standort):void {
			_standort = standort;
			_standortChanged = true;
		}
		
		public function get standort (): Standort {
			return _standort;
		}
		
		public function set termine (termine:SPlanungMonat):void {
			if ( _watcher ) _watcher.unwatch();
			_termine = termine;
			_watcher = ChangeWatcher.watch(_termine, "change", termineChanged);
			_date = termine.getDate();
			_termineChanged = true;
			
		}
		
		public function termineChanged(event:Event):void {
			ExternalInterface.call("$.mosaikRuntime.setChanged");
			if ( _termine.change == _standort.id ) {
				_termineChanged = true;
				this.invalidateProperties();
			}
		}
		
		public function showHighlight(topOffset:Number, dauer:Number):void {
			_highlight.y = topOffset;
			_highlight.x = 0;
			_highlight.visible = true;
			_highlight.height = dauer * _dayHeight;
		}
		
		public function hideHighlight():void {
			_highlight.visible = false;
		}
		
		public function get termine (): SPlanungMonat {
			return _termine;
		}
		
		//function mouseLeaveHandler (event:MouseEvent) {
			//hideHighlight();
		//}
		
		/* drag and drop */
		public function dragEnterHandler(event:DragEvent):void {
			// Logger.info(getDayForOffset(event.localY).toString()); 
			// Get the drop target component from the event object.
			var tag:int = getDayForOffset(event.localY);
			var dauer:int = 0;
			// dauer
			if (event.dragSource.hasFormat("moveSeminar")) {
				var info:Object = event.dragSource.dataForFormat("moveSeminar") as Object;
				//Logger.info(info.dauer);
				dauer = info.dauer;
			} else if ( event.dragSource.hasFormat("items")) {
				var dragObj:Array = event.dragSource.dataForFormat("items") as Array;
				var seminar:Object = dragObj[0];
				dauer = seminar.dauer;
				//Logger.info(seminar.dauer); 
			}
			
			
			//if ( _termine.hasTermin(tag, dauer, _standort.id )) {
				//DragManager.showFeedback(DragManager.NONE);
				//hideHighlight();
			//} else {
				//showHighlight( getDayOffsetHeight(tag), dauer);
				var dropTarget:VMonatCanvas=VMonatCanvas(event.currentTarget);
				// Accept the drop.
				DragManager.showFeedback(DragManager.LINK);
				DragManager.acceptDragDrop(dropTarget);
			///}
				
			return;
		}
		
		public function dragDropHandler(event:DragEvent):void {
			var i:int;
			var nTag:String;
			var tagNull:int = getDayForOffset(event.localY);
			
			if (event.dragSource.hasFormat("moveSeminar")) {
				var info:Object = event.dragSource.dataForFormat("moveSeminar") as Object;
				//Alert.show(info.standort_id);
				//Logger.info("monat:", info.monat.toString());
				
				var move:Array = MosaikConfig.getAC("termine").getItemAt(parseInt(info.monat)).removeSeminar(info.id, info.standort_id);
									
				for ( i = 0; i < move.length; i++) {
					nTag = (tagNull + i).toString();
					move[i].erster_tag = tagNull;
					move[i].verschoben = (tagNull+i).toString();
					_termine.setTermin(nTag, standort.id, move[i]);
				}
						
			} else if ( event.dragSource.hasFormat("items")) {
				var src:Object = event.dragSource;
				var dragObj:Array = event.dragSource.dataForFormat("items") as Array;
				var seminar:Object = dragObj[0];
				
				count ++;
				for (i = 0; i < parseInt(seminar.dauer); i++ ) {
					var tmp:Object = new Object();
					tmp.id = "new_" + seminar.id + "_" + count.toString();
					tmp.seminar_art_id = seminar.id;
					tmp.erster_tag = tagNull;
					tmp.farbe = seminar.farbe;
					tmp.textfarbe = seminar.textfarbe;
					tmp.dauer = seminar.dauer;
					tmp.freigabe_flag = "P";
					tmp.freigabe_farbe = "0xff0000";
					tmp.verschoben = (tmp.erster_tag + i).toString(); // termin im php server beruecksichtigen
					var nTermin:Termin = new Termin(tmp);
					nTag = tmp.verschoben;

					_termine.setTermin(nTag, standort.id, nTermin);
				}
			}
			if ( redraw != null) redraw();
		}
	}
}