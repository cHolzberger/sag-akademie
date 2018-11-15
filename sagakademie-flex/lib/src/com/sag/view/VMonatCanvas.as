package com.sag.view
{
	import com.mosaiksoftware.MosaikConfig;
	import com.mosaiksoftware.GMBus;
	import com.sag.models.SPlanungMonat;
	import com.sag.models.Standort;
	import com.sag.models.Termin;
	import com.sag.view.PlanungTag;
	import flash.display.CapsStyle;
	import flash.display.JointStyle;
	import flash.display.LineScaleMode;
	import flash.events.AccelerometerEvent;
	import flash.events.ContextMenuEvent;
	import flash.events.Event;
	import flash.events.MouseEvent;
		import flash.external.ExternalInterface;
		import mx.managers.PopUpManager;
	import flash.ui.ContextMenu;
	import flash.ui.ContextMenuBuiltInItems;
	import flash.ui.ContextMenuItem;
	import mx.binding.utils.*;
	import mx.containers.Box;
	 import mx.core.*;
    import   mx.managers.DragManager;
	import mx.events.*;
	import mx.events.DragEvent;
		import mx.events.DragManager;	
		import mx.core.UIComponent;
	import spark.primitives.Line;
	import spark.components.Group;
		import spark.components.VGroup;
	import mx.core.UIComponent;
	import mx.controls.Text;
			import mx.controls.Alert;
			import spark.layouts.BasicLayout;
	import mx.containers.Canvas;
				import spark.layouts.ConstraintLayout;
	import spark.components.SkinnableContainer;
	import spark.core.SpriteVisualElement;
	import mx.binding.utils.BindingUtils;
	import flash.display.Sprite;
	import flash.display.DisplayObject;
	import flash.geom.Rectangle;
	import spark.components.supportClasses.GroupBase;
	/**
	 * ...
	 * @author MOSAIK Software // Christian Holzberger <ch@mosaik-softaware.de>
	 */
	[Bindable]
	[Event(name = "dayHeightChanged", type = "flash.events.Event")]
	public class VMonatCanvas extends Group {
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
		public  var _dayWidth:Number = 120;
		
		private var _separatorColor:Number = 0x000000;
		private var _lineColor:Number = 0xafafaf;
		private var _weekendColor:Number = 0xefefef;
		private var _feiertagColor:Number = 0xffffcc;
		private var _highlight:Box = null;
		
		private var _drawGrid:Boolean = true;
		
		private var _showReferent:Boolean = false;
		private var _showSperrung:Boolean = false;

		private var _showTeilnehmer:Boolean = false;
		
		private var _isInhouseStandort:Boolean = false;
		private var _dragOpRunning = false;

		private static var count:Number = 0;// fuer neue seminare
		private var s:SpriteVisualElement;
		public var modus:String;

			/* Main functionality */
		public function VMonatCanvas() {
			super();
			addEventListener(Event.ADDED_TO_STAGE, init);
			height = 500;

		}
		
		private function init(e:Event):void {
			createChildren();
			_terminOffset = _termine.calculateOffsets(6, _dayHeight);
			_termine.maskAllDirty();

			redrawAll();
			drawAll();
		}
		
		

		public function set showReferent(val:Boolean):void {
			_showReferent = val;
			
			var term:PlanungTag;
			for each (term in _termineObj) {
				term.showReferent = val;
			}

	//		var notiz:Text;
	//		for each ( notiz in _notizenObj) {
	//			notiz.visible = !val;
	//		}
		}
		
		public function get showReferent ():Boolean {
			return _showReferent;
		}
		
		public function set showSperrung(val:Boolean):void {
			_showSperrung = val;
			
			var term:PlanungTag;
			for each (term in _termineObj) {
				term.showSperrung = val;
			}
			
	//		var notiz:Text;
	//		for each ( notiz in _notizenObj) {
	//			notiz.visible = !val;
	//		}
		}
		
		public function get showSperrung ():Boolean {
			return _showSperrung;
		}
		
		public function set showTeilnehmer(val:Boolean):void {
			_showTeilnehmer = val;
			
			var term:PlanungTag;
			for each (term in _termineObj) {
				term.showTeilnehmer = val;
			}
			
			//var notiz:Text;
			//for each ( notiz in _notizenObj) {
		//		notiz.visible = !val;
		//	}
		}
		
		public function get showTeilnehmer ():Boolean {
			return _showTeilnehmer;
		}
		
	
		
		private function menuSelectHandler(event:ContextMenuEvent):void {
            //Alert.show("menuSelectHandler: " + event);
        }
		
		private function menuGotoTermin ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			GMBus.publish("kalender/gotoTermin", [{ "terminId": tag.termin.id.toString(), "inhouse": tag.termin.inhouse.toString() } ] );
			//ExternalInterface.call("$.mosaikRuntime.load","/admin/termine/" + tag.termin.id.toString() + "?edit" );
			
		}
		
		private function menuGotoSeminar ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			GMBus.publish("kalender/gotoSeminar", [{ "seminarId": tag.termin.name.toString(), "inhouse": tag.termin.inhouse.toString()  }]);
			//ExternalInterface.call("$.mosaikRuntime.load","/admin/seminare/" + tag.termin.name.toString() + "?edit" );
		}
		
		private function menuGotoReferenten ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			GMBus.publish("kalender/gotoReferenten", [{ "seminarId": tag.termin.id.toString(), "standortId": tag.standort.id, "inhouse": tag.termin.inhouse.toString() }]);
			//ExternalInterface.call("$.mosaikRuntime.load","/admin/planung/termin?standort_id=" + tag.standort.id +"&seminar_id=" + tag.termin.id.toString());
			
		}

		public function getDayOffsetHeight(dayNo:int, num:int = 0 ):int {
			//Logger.info("Tmp: ", dayNo.toString());
			return _terminOffset[dayNo][1] + num*_dayHeight;
		} 
		
		public function getDayForOffset(offset:int):int {
			var maxDays:Number = new Date (_date.fullYear, _date.month + 1, 0).getDate();
			var day:int = 1;
			for ( var i:int = 1; i <= maxDays; i++ ) {
				if ( _terminOffset[i][1] < offset ) {
					day = i;
				} else {
					break;
				}
			}

			return day;
		}
		private var currentHeight:Number = 0;
		private var _height:Number = 0;
		private var _heightSet:Boolean = false;

		private var _dayDirty:Array = [];
		public function drawGrid ():void {
			if ( ! _termine ) return;

			trace("Draw Grid Month: " + _date.fullYear +"."+ _date.month);

			_terminOffset = _termine.calculateOffsets(6, _dayHeight);
			var maxDays:Number = new Date (_date.fullYear, _date.month+1, 0).getDate()+1;
			
			var i:int;
			var feiertag:String;
			
			// offsets etc.
			var countTermine:int;
			
			var ft:Text;
			
			for each (ft in _feiertagLabels ) {
				removeChild(ft);
				ft = null;
			}
			_feiertagLabels = [];
			
			for ( i = 1; i <= maxDays ; i++) { // 1 to maxdays
				//if ( ! _termine.hasChangedStandort[standort.id] && ! _termine.hasChangedDay[i] ) continue; 
				_dayDirty.push(i);
							trace("Dirth:" + i);

				var currentHeight: int = _terminOffset[i][0];
				var yOffset: int = _terminOffset[i][1];
			
				this.graphics.lineStyle(1, _lineColor, 1, false, LineScaleMode.VERTICAL, CapsStyle.NONE, JointStyle.MITER, 10);
				
				// feiertag
				if (  _standort.name == "Allgemein" && (feiertag = _termine.getFeiertag(i))!= null ) {
					
					drawFeiertag(this,yOffset, _dayWidth, currentHeight,feiertag);
				} else if (_termine.getDate(i).day == 0 || _termine.getDate(i).day == 6) {
					drawWeekend(this,yOffset, _dayWidth, currentHeight);
				} else {
					drawWeekday(this,yOffset, _dayWidth, currentHeight );
				}
			}
			_height =  _terminOffset[maxDays+1][1];
			_drawGrid = false;
			
		}
		
		public var _notizMap:Object = new Object();
		
		function drawWeekend(s,yOffset, width, height) {
				//Logger.info("Weekend");
					s.graphics.beginFill(_weekendColor);
					s.graphics.drawRoundRect(0, yOffset, width , height, 3);
					s.graphics.endFill();
		}
		
		function drawWeekday(s, yOffset, width, height) { 
			s.graphics.beginFill(0xffffff);
			s.graphics.drawRoundRect(0, yOffset, width , height, 3);
			s.graphics.endFill();
		}
		
		function drawFeiertag(s, yOffset, width, height, feiertag) {
			
			
					s.graphics.beginFill(_feiertagColor);
					s.graphics.drawRoundRect(0, yOffset, width , height, 3);
					s.graphics.endFill();
			
				var tmpLabel:Text = new Text();
					tmpLabel.text = feiertag;
					tmpLabel.x = 5;
					tmpLabel.y = yOffset;
					tmpLabel.width = _dayWidth;
					tmpLabel.height = _dayHeight;
					tmpLabel.setStyle("fontSize", 9);
					tmpLabel.setStyle("textColor", "0xefefef");
					
					_feiertagLabels.push(tmpLabel);
					addChild(tmpLabel);

				
					
			
		}
		var _terminMenu: ContextMenu = null;
								var terminItem:ContextMenuItem = new ContextMenuItem("Zum Termin");
						var seminarItem:ContextMenuItem = new ContextMenuItem("Zum Seminar");
						var referentenItem:ContextMenuItem = new ContextMenuItem("Zu den Referenten");
						var sperrenTermin:ContextMenuItem = new ContextMenuItem("Aktualisierung dieses Termins sperren");
						var entsperrenTermin:ContextMenuItem = new ContextMenuItem("Aktualisierung dieses Termins zulassen");
						var sperrenSeminar:ContextMenuItem = new ContextMenuItem("Aktualisierung zukünftige Termine sperren");
						var sperrenStandort:ContextMenuItem = new ContextMenuItem("Aktualisierung zukünftige Termine dieser Seminarort sperren");
						var aktualisierenTermin:ContextMenuItem = new ContextMenuItem("Aktualisierung dieses Termins");
						var aktualisierenSeminar:ContextMenuItem = new ContextMenuItem("Aktualisierung zukünftige Termine");
						var aktualisierenStandort:ContextMenuItem = new ContextMenuItem("Aktualisierung zukünftige Termine dieser Seminarort");
						function updateContextMenu(mouseEvent:ContextMenuEvent):void {
							updateMenuFromTermin(mouseEvent.currentTarget.termin);
						}
		function updateMenuFromTermin(termin) {
									sperrenTermin.enabled = !termin.aktualisierung_gesperrt;
						entsperrenTermin.enabled = termin.aktualisierung_gesperrt;
						sperrenSeminar.enabled = !termin.seminar_art_gesperrt;
						aktualisierenTermin.enabled = !termin.aktualisierung_gesperrt && !termin.seminar_art_gesperrt;
						aktualisierenSeminar.enabled =  !termin.seminar_art_gesperrt && !termin.standort_gesperrt ;

		}
		function getTerminMenu():ContextMenu {
			if ( _terminMenu ) return _terminMenu;
							// popup / rechts klick menu konfigurieren
						var customMenu:ContextMenu = new ContextMenu();
						
			
						customMenu.hideBuiltInItems();
						
						var defaultItems:ContextMenuBuiltInItems = customMenu.builtInItems;
						defaultItems.print = true;
						
						terminItem.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, menuGotoTermin);	
						
						seminarItem.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, menuGotoSeminar);	
						
						referentenItem.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, menuGotoReferenten);	
						
						
						// SAGADMUI-307: 
				
						sperrenTermin.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, lockTermin);	
						
						entsperrenTermin.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, unlockTermin);	
						
						
						sperrenSeminar.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, lockFuturTermin);	
						
						sperrenStandort.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, lockFuturTerminStandort);	
						//sperrenStandort.enabled = !termin.standort_gesperrt;
						
						aktualisierenTermin.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, updateTermin);	
						
						aktualisierenSeminar.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, updateFuturTermin);	

						aktualisierenStandort.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, updateFuturTerminStandort);	
						//aktualisierenStandort.enabled =  !termin.seminar_art_gesperrt && !termin.standort_gesperrt ;

						
						customMenu.addEventListener(ContextMenuEvent.MENU_SELECT, menuSelectHandler);
						customMenu.customItems.push(terminItem);
						customMenu.customItems.push(seminarItem);
						customMenu.customItems.push(referentenItem);
						
						customMenu.customItems.push(sperrenTermin);
						customMenu.customItems.push(entsperrenTermin);
						customMenu.customItems.push(sperrenSeminar);
						customMenu.customItems.push(sperrenStandort);
						
						customMenu.customItems.push(aktualisierenTermin);
						customMenu.customItems.push(aktualisierenSeminar);
						customMenu.customItems.push(aktualisierenStandort);

												
						//customMenu.addEventListener(ContextMenu.)
						
						
						//tmp.flexContextMenu.setContextMenu(customMenu);
						_terminMenu = customMenu;
						return _terminMenu;
		}
		
		static var _terminPool:Array = [];
		private function createTermin() {
		
				if ( _terminPool.length == 0 ) {
					return new PlanungTag;
				}
				var t:PlanungTag = _terminPool.pop();
				t.visible = true;
				return t;
		}
		
		function _recycle(_terminObj:PlanungTag) {
			if ( _termineObj == null ) return;
					removeElement(_terminObj);

			_terminObj.visible = false;
			_terminObj.includeInLayout=false;
			_terminObj.x = 0;
			_terminObj.y = 0;
			_terminPool.push(_terminObj);


		}
		
		public function drawTermine():void {
			if ( ! _termine ) return;
			updateLabel(_standort.name);
			var maxDays:Number = new Date (_date.fullYear, _date.month+1, 0).getDate();
			var i:Number;
			
			for ( i = 0; i < _notizenObj.length; i++ ) {
				removeChild(_notizenObj[i]);
				_notizenObj[i] = null;
			}
			
		
			_notizenObj = [];
			var textColor:Number = 0;
			for ( i = 1; i <= maxDays; i++) {
				var termin:Termin;
				var terminList:Array;
				
				if (!_termineObj[i]) _termineObj[i] = [];
				textColor = 0;
				//if ( !_dayDirty[i] ) continue;
					for ( var j:int =0; j < _termineObj [i].length; j++) {
						_recycle(_termineObj[i][j]);
					}
					_termineObj[i] = [];
					_dayDirty[i]=false;
				

				if ( (terminList = _termine.getTermin(i.toString(), _standort.id)) != null ) {
					for ( var j:int = 0; j < terminList.length; j++ ) {
			
						termin = terminList[j];
						if ( termin == null ) continue;
						BindingUtils.bindSetter(termineChanged, termin, "status" );
						
						var tmp:PlanungTag = createTermin();
						tmp.x = 0;
						tmp.y = getDayOffsetHeight(i, j)+1;
						tmp.showReferent = showReferent;
						tmp.showSperrung = showSperrung;
						tmp.tag = i.toString();
						tmp.termine = _termine;

						tmp.standort = _standort;
						tmp.termin = termin;
						tmp.width = _dayWidth;
						tmp.explicitWidth = _dayWidth;
						tmp.height = _dayHeight-2;
						tmp.explicitHeight = _dayHeight - 2;
						//tmp.onDragStartCb = dragStartHandler;
						textColor = termin.fontColor;
					
						tmp.addEventListener(MouseEvent.MOUSE_DOWN, dragStartHandler);
						tmp.addEventListener( ContextMenuEvent.MENU_SELECT, updateContextMenu);

						_termineObj[i].push(tmp);
						addElement(tmp);
						tmp.contextMenu = getTerminMenu();
					} 
					
				}
			}
			/*
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
						addElement(notizLabel);
						
						//_notizMap[notizLabel.toString()].tag = i.toString();
						//_notizMap[notizLabel.toString()].standort_id = _standort.id;
					}
			}*/

		}
		private var ib:NoteInput = null;
		
		private function onClick(e:MouseEvent):void {
			var x:int = e.currentTarget.contentMouseX;
			var y:int = e.currentTarget.contentMouseY;
		//	if ( ib != null ) {
		//		return; // nur ein popup auf einmal
		//	}
						
			//Logger.info ( e.target.toString() );
			//Logger.info ( e.currentTarget.toString() );
			/*
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
				addElement(ib);
			}
			*/
		}
		
		private function onSetNote(tag:String, standort:String, txt:String):void {
			_termine.setNotiz(tag, standort, txt);
			
			removeChild(ib);
			ib = null;
				
		}
		
		override protected function measure():void {
			super.measure();
				_terminOffset = _termine.calculateOffsets(6, _dayHeight);
				measuredHeight = _terminOffset[_terminOffset.length-1][1];
				measuredWidth = _dayWidth;

		}
	
		
		override protected function updateDisplayList(unscaledWidth:Number, unscaledHeight:Number):void {
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			
			_width = unscaledWidth;
			_height = unscaledHeight;
			s.height = _height;
			s.width = _width;
			//trace(_width, _height);

			drawAll();

		}
		var _x:Number;
		var _y:Number;
		var _width:Number;
		override protected function commitProperties ():void {
			super.commitProperties();
			if (_x != x)
				x = _x;
				
			if (_y != y)
				y = _y;
						
			if ( width != _width ) 
				width = _width;
				
			if ( height != _height) 
				height = _height;
			
			
				
				trace("commit");
		}
		
		protected  override function createChildren():void {
			super.createChildren();
			measure();
			GMBus.log("createChildren");
			s = new SpriteVisualElement();
			s.x = 0;
			s.y = 0;
			s.width = width;
			s.height = height;
			addElement(s);
			addEventListener(DragEvent.DRAG_ENTER, dragEnterHandler);
			addEventListener(DragEvent.DRAG_OVER, dragOverHandler);
			//addEventListener(MouseEvent.MOUSE_UP, onClick);

			addEventListener(DragEvent.DRAG_DROP, dragDropHandler);
			//_dayWidth = getExplicitOrMeasuredWidth();
			addEventListener("gotitx", function (event:FlexEvent):void {
				
				
			} );
			
			/*
			_highlight = new Box();
			_highlight.visible = false;
			_highlight.width = _dayWidth;
			_highlight.height = _dayHeight;
			_highlight.graphics.moveTo (0, 0);
			_highlight.graphics.beginFill(_weekendColor);
			_highlight.graphics.drawRoundRect(0, _dayHeight, _dayWidth , _dayHeight, 3);
			_highlight.graphics.endFill();*/
			//_highlight.graphics.opaqueBackground = 0x00ee00;
		}
		
		public function redrawAll():void {
			_drawGrid = true;
			_termineChanged = true;
			_standortChanged = true;
		}
		
		protected function drawAll():void {
			
			if (!_termine) {
				trace("Termine fehlen");
			}
 
			if ( _drawGrid && _termine) {
				drawGrid();
				_drawGrid = false;
			}
			
			if (_termineChanged && _termine) {
				//Logger.info("invalidate");
				 drawTermine();
				_termineChanged = false;
			}
			
			if ( _standortChanged  ) {
				
				updateLabel( _standort.name);
				_standortChanged = false;
			}
		   
			if ( !_drawGrid && !_termineChanged && !_standortChanged ) {
				_termine.markAllClean();
			}
		}
	
		private function updateLabel(text:String) {
			if ( _label == null ) {
				_label = new Text();
				_label.left = 2;
				_label.top = 0;
				_label.right = 0;
				
				_label.height = _dayHeight;
				addElement(_label);
			}
			_label.text = text;
		}
		
		/* setter and getter */
		public function set standort (standort:Standort):void {
			_standort = standort;
			_standortChanged = true;
			BindingUtils.bindSetter( setEnabled, standort, "enabled");
			setEnabled(standort.enabled);
		}
		
		public function get standort (): Standort {
			return _standort;
		}
		
		private function setEnabled( b: Boolean ):void {
			GMBus.log("Standort enabled: " + b );
			visible = b;
		}

		
		public function set termine (termine:SPlanungMonat):void {
			if ( _watcher ) _watcher.unwatch();
			_termine = termine;
			_date = termine.getDate();
		}
		
		public function termineChanged(d):void {
			redrawAll();
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
		
		public function dragOverHandler(event:DragEvent):void {
			DragManager.showFeedback(DragManager.LINK);
		}
		
		/* drag and drop */
		public function dragEnterHandler(event:DragEvent):void {
		// Logger.info(getDayForOffset(event.localY).toString());
			// Get the drop target component from the event object.
			var tag:int = getDayForOffset(event.localY);
			var dauer:int = 0;
			var canHandle:Boolean = false;
			// dauer
DragManager.showFeedback(DragManager.LINK);
				DragManager.acceptDragDrop(UIComponent(event.target));
			if (event.dragSource.hasFormat("moveSeminar")) {
				var info:Object = event.dragSource.dataForFormat("moveSeminar") as Object;
				//Logger.info(info.dauer);
				dauer = info.dauer;
				canHandle = true;
				
			} else if ( event.dragSource.hasFormat("items")) {
				var dragObj:Array = event.dragSource.dataForFormat("items") as Array;
				var seminar:Object = dragObj[0];
				dauer = seminar.dauer;
				//Logger.info(seminar.dauer);
				if ( seminar.inhouse == 1 && modus == "Inhouse" && _standort.name == "Inhouse") {
					canHandle = true;
				} else if ( seminar.inhouse == 0 && modus == "Standard" && _standort.name != "Inhouse") {
					canHandle = true;
				}
			}
			
			
			//if ( _termine.hasTermin(tag, dauer, _standort.id )) {
				//DragManager.showFeedback(DragManager.NONE);
				//hideHighlight();
			//} else {
				//showHighlight( getDayOffsetHeight(tag), dauer);
			if ( canHandle ) {
				var dropTarget:VMonatCanvas=VMonatCanvas(event.currentTarget);
				// Accept the drop.
				DragManager.showFeedback(DragManager.LINK);
				DragManager.acceptDragDrop(UIComponent(event.target));
			} else {
				DragManager.showFeedback(DragManager.NONE);
				
				
			}
		
			///}
			return;
		}
		
		 public function beginDrag(event:MouseEvent)   : void
		{
			
		}
 
		public function dragStartHandler(event:MouseEvent):void {
			/*trace("drag start");
			var ds:DragSource   = new DragSource();
			ds.addData(event.currentTarget.termin , "moveSeminar");
        //proxy.product = product;
			DragManager.doDrag(this, ds, event, null, 16 - mouseX, -mouseY, 0.5,   false);
			
			event.preventDefault();*/
			//redrawAll();
			//invalidateDisplayList();
		}
		
		public function dragDropHandler(event:DragEvent):void {
			var i:int;
			var nTag:String;
			var tagNull:int = getDayForOffset(event.localY);
			
			if (event.dragSource.hasFormat("moveSeminar") && !(event.shiftKey || event.ctrlKey )) {
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
									event.dragInitiator.dispatchEvent(new FlexEvent("gotit"));
						redrawAll();
						invalidateProperties()
					invalidateDisplayList();

					GMBus.publish("kalender/change",[]);
			} else if ( event.dragSource.hasFormat("moveSeminar") && (event.shiftKey || event.ctrlKey )) {
				var seminar:Object = event.dragSource.dataForFormat("moveSeminar") as Object;
				count ++;
				
				for (i = 0; i < parseInt(seminar.dauer); i++ ) {
					GMBus.log("Neues Seminar Dauer:" + seminar.dauer);
					GMBus.log("SeminarArtID:" + seminar.seminar_art_id);
					var tmp:Object = new Object();
					tmp.id = "new_" + seminar.seminar_art_id + "_" + count.toString();
					tmp.seminar_art_id = seminar.seminar_art_id;
					tmp.erster_tag = tagNull;
					tmp.farbe = seminar.farbe;
					tmp.textfarbe = seminar.textfarbe;
					tmp.dauer = seminar.dauer;
					tmp.freigabe_flag = "P";
					tmp.freigabe_farbe = "0xff0000";
					tmp.verschoben = (tagNull + i).toString(); // termin im php server beruecksichtigen
					var nTermin:Termin = new Termin(tmp);
					nTag = tmp.verschoben;
					redrawAll();
					_termine.setTermin(nTag, standort.id, nTermin);
					invalidateProperties()
					invalidateDisplayList();


				}
				
					GMBus.publish("kalender/change",[]);
									event.dragInitiator.dispatchEvent(new FlexEvent("gotit"));

				
			} else if ( event.dragSource.hasFormat("items")) {
				var src:Object = event.dragSource;
				var dragObj:Array = event.dragSource.dataForFormat("items") as Array;
				var seminar:Object = dragObj[0];
							

				count ++;
				for (i = 0; i < parseInt(seminar.dauer); i++ ) {
					GMBus.log("Neues Seminar Dauer:" + seminar.dauer);
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
						redrawAll();
					invalidateProperties()
					invalidateDisplayList();

				}
					
					GMBus.publish("kalender/change",[]);
			}
			
		
					_dragOpRunning = true;

					redrawAll();
					//event.dragInitiator.invalidateNow();
					invalidateDisplayList();
					callLater(validateNow);
			
		}
		var _contextTermin:Termin = null;
		
		public function lockTermin ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			_contextTermin = tag.termin;
			var alertDialog:Alert = Alert.show("Sind Sie sicher, dass Sie diesen Termin sperren möchten?",
"Sperrung bestätigen",Alert.YES|Alert.NO,GMBus.calWindow,doLockTermin,null,Alert.NO);
			//Alert.show(tag.termin.aktualisierung_gesperrt);
			PopUpManager.centerPopUp(alertDialog);
		}
		
		public function doLockTermin(event:CloseEvent):void {
			if ( event.detail==Alert.YES) {
				GMBus.publish("kalender/lockTermin", [ { "terminId": _contextTermin.id } ] );
			}
		}
		
		public function unlockTermin ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			_contextTermin = tag.termin;
			
			var alertDialog:Alert =  Alert.show("Sind Sie sicher, dass Sie diesen Termin entsperren möchten?",
"Sperrung bestätigen",Alert.YES|Alert.NO,GMBus.calWindow,doUnLockTermin,null,Alert.NO);
			//Alert.show(tag.termin.aktualisierung_gesperrt);
			PopUpManager.centerPopUp(alertDialog);

		}
		
		
		public function doUnLockTermin(event:CloseEvent):void {
			if ( event.detail==Alert.YES) {
				GMBus.publish("kalender/unlockTermin", [ { "terminId": _contextTermin.id } ] );
			}
		}
		
		public function lockFuturTermin ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			_contextTermin = tag.termin;
			
			var alertDialog:Alert = Alert.show("Sind Sie sicher, dass Sie die zukünftigen Termine sperren möchten?",
"Sperrung bestätigen",Alert.YES|Alert.NO,GMBus.calWindow,doLockFuturTermin,null,Alert.NO);
			//Alert.show(tag.termin.aktualisierung_gesperrt);
			PopUpManager.centerPopUp(alertDialog);
		}
		
		public function doLockFuturTermin(event:CloseEvent):void {
			if ( event.detail==Alert.YES) {
				GMBus.publish("kalender/lockFuturTermin", [ { "terminId": _contextTermin.id } ] );
			}
		}
		
		public function lockFuturTerminStandort ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			_contextTermin = tag.termin;
			var alertDialog:Alert = Alert.show("Sind Sie sicher, dass Sie die zukünftigen Termine an diesem Standort sperren möchten?",
"Sperrung bestätigen",Alert.YES|Alert.NO,GMBus.calWindow,doLockFuturTerminStandort,null,Alert.NO);
			PopUpManager.centerPopUp(alertDialog);
		}
		
		public function doLockFuturTerminStandort(event:CloseEvent):void {
			if ( event.detail==Alert.YES) {
				GMBus.publish("kalender/lockFuturTerminStandort", [ { "terminId": _contextTermin.id} ] );
			}
		}
		
		public function updateFuturTermin ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			_contextTermin = tag.termin;
			var alertDialog:Alert = Alert.show("Sind Sie sicher, dass Sie alle zukünftigen Termine dieses Seminars aktualisieren wollen?",
"Aktualisierung bestätigen",Alert.YES|Alert.NO,GMBus.calWindow,doUpdateFuturTermin,null,Alert.NO);
			//Alert.show(tag.termin.aktualisierung_gesperrt);
			PopUpManager.centerPopUp(alertDialog);
		}
		
		public function doUpdateFuturTermin(event:CloseEvent):void {
			if ( event.detail==Alert.YES) { 
				GMBus.publish("kalender/updateFuturTermin", [ { "terminId": _contextTermin.id } ] );
			}
		}
		
		public function updateTermin ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			_contextTermin = tag.termin;
			var alertDialog:Alert = Alert.show("Sind Sie sicher, dass Sie diesen Termine aktualisieren wollen?",
"Aktualisierung bestätigen",Alert.YES|Alert.NO,GMBus.calWindow,doUpdateTermin,null,Alert.NO);
			//Alert.show(tag.termin.aktualisierung_gesperrt);
			PopUpManager.centerPopUp(alertDialog);
		}
		
		public function doUpdateTermin(event:CloseEvent):void {
			if ( event.detail==Alert.YES) {
				GMBus.publish("kalender/updateTermin", [ { "terminId": _contextTermin.id } ] );
			}

		}
		
		public function updateFuturTerminStandort ( event:ContextMenuEvent):void {
			var tag:PlanungTag = event.contextMenuOwner as PlanungTag;
			_contextTermin = tag.termin;
			var alertDialog:Alert = Alert.show("Sind Sie sicher, dass Sie alle zukünftigen Termine dieses Seminars an diesem Standort aktualisieren wollen?",
"Aktualisierung bestätigen", Alert.YES | Alert.NO, GMBus.calWindow, doUpdateFuturTermineStandort, null, Alert.NO);
			PopUpManager.centerPopUp(alertDialog);
			//Alert.show(tag.termin.aktualisierung_gesperrt);

		}
		
		public function doUpdateFuturTermineStandort(event:CloseEvent):void {
			if ( event.detail == Alert.YES) {
				
				GMBus.publish("kalender/updateFuturTerminStandort", [ { "terminId": _contextTermin.id } ] );			
			}
		}
	}
	
	
}