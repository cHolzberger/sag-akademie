package com.sag.models
{
	import com.mosaiksoftware.MosaikConfig;
	import com.mosaiksoftware.GMBus;
	import com.sag.models.Termin;
	
	import flash.events.Event;
	
	import mx.binding.utils.ChangeWatcher;
	import mx.collections.ArrayCollection;
	import mx.controls.Alert;
	

	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	[Bindable]
	public class SPlanungMonat
	{
		static public var monateLabel:Array = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];
		
		/* datum fuer diesen monat festhalten */
		public var monat:int;
		public var year:int;
		public var label:String;
		
		/* internes speichern der termine, notizen, feiertage etc. */
		public var _termine:Object;
		public var _notizen:Object;
		public var _feiertage:Object;
		public var _standorte:ArrayCollection;
		
		/* hiermit kann ein onChange getriggert werden */
		public var change:String = "";
		public var _cache:Object = new Object();
		public var _maxTage:int = 0;
		
		public var planungJahr:ArrayCollection=null;

		public function SPlanungMonat( cyear: int,  imonat:int, nstandorte: ArrayCollection, termine:Object, notizen:Object, feiertage:Object) {
			
			_maxTage = new Date ( cyear, imonat+1, 0).getDate();
			this.year = cyear;
			monat = imonat;
			label = monateLabel[imonat];
			if ( !notizen) _notizen = new Object();
			else _notizen = notizen;
			
			if ( !termine) _termine = new Object();
			else _termine = termine;
			
			_standorte = nstandorte;
			
			if ( !feiertage ) _feiertage = new Object();
			else _feiertage = feiertage;
		}
		
		public function getDate (d:int = 1):Date {
		//	trace (this.year + "." + monat + "." + d);
			return new Date( this.year, monat, d );
		}
		
		
	private var _terminOffset:Array = null;

	public function calculateOffsets(startOffset:int, height:int):Array {
			if ( _terminOffset ) return _terminOffset;
			
			
			var currentHeight = 0;
			var _date = getDate();
			var maxDays:Number = new Date (_date.fullYear, _date.month+1, 0).getDate()+1;

			var i:int;
			var month = parseInt(_date.month);
			
			_terminOffset = [];
			
			// offsets etc.
			var countTermine:int;

			var yOffset:Number = height+startOffset;
			_terminOffset[0] = [0,0];
			
			for ( i = 1; i <= maxDays ; i++) { // 1 to maxdays
			
				countTermine = this.countTermine(i.toString());
				if (countTermine != 0 ) {
					currentHeight = height * countTermine;
				} else {
					currentHeight = height;
				}
				_terminOffset[i] = [currentHeight, yOffset];
				
				yOffset = yOffset + currentHeight;
			}
			
			
			_terminOffset[maxDays + 1] = [0, yOffset];
			
			return _terminOffset;
	}
		
		public function hasTermin(tag:int, dauer:int, standort:String):Boolean {
			//Logger.info("Tag: " + tag.toString() + " Dauer: " + dauer.toString());
			
			if ( !_termine) {
				GMBus.log ("Termine not set"  );
				return false;
				
			} else if ( tag > _maxTage) {
				GMBus.log("Tag zu hoch")
				return false;
			}
			
			var end:int = tag + dauer;
			if ( end > _maxTage) end = _maxTage;
			
			for ( var i:int = tag; i < end; i++) {
				if ( _termine[i.toString()] != null ) {
					if ( _termine[i.toString()][standort][0] != null ) {
						return true;
					}
				}
			}
			
			return false;
		}
		
		// gibt das maximum fuer die termine an einem bestimmten tag zurueck
		public function countTermine(tag:String):int {
			var max:int = 0;
			
			var standort: Array;
			
			for each ( standort in _termine[tag] ) {
				var subcount:int = 0;
				var te:Object;
				
				if (standort == null) continue;
				var i:int = 0;
				for ( i = 0; i < standort.length; i++ ) {
					if (standort[i] != null ) subcount ++;
				}
				
				if ( subcount > max ) {
					max = subcount;
				}
			}
			
			return max;
		}
		public function getTermin(tag:String, standort:String):Array {

			if ( !_termine) return null;
			
			if (parseInt(tag) > _maxTage) {
				return MosaikConfig.getAC("termine").getItemAt(monat +1).getTermin( (parseInt(tag)-(_maxTage)).toString(), standort);
			} else if ( _termine[tag] == null ) {
				return null;
			} else if ( _termine[tag][standort] == null ) {
				return null;
			}
			var i:int = 0;
			var ret:Array = [];
			
			for ( i = 0; i < _termine[tag][standort].length; i++) {
				if ( _termine [tag][standort][i] == null ) continue;
				
				if ( _cache[tag+"_"+standort + "_" + i.toString()] == null ) {
					_cache[tag + "_" + standort +"_"+i.toString()] = new Termin(_termine[tag][standort][i]);
				}
				ret.push(_cache[tag + "_" + standort + "_" + i.toString()]);
			}

			return ret;
		}
		
		public function setNotiz (tag:String, standort:String, text:String):void {
			if (!_termine) return;
			
			if ( _notizen[tag] == null ) {
				_notizen[tag] = new Object();
				_notizen[tag][standort] = new Object();
			} else if (_notizen[tag][standort] == null) {
				_notizen[tag][standort] = new Object();
			}
			
			_notizen[tag][standort].notiz = text;
			_notizen[tag][standort].standort_id = standort;
		}
		
		public function getNotiz(tag:String, standort:String):Object {
			if (!_termine) return null;
			
			if ( parseInt(tag) > _maxTage ) {
				return MosaikConfig.getAC("termine").getItemAt(monat + 1).getNotiz ((parseInt(tag) - (_maxTage)).toString(), standort);
			} else if (_notizen[tag] == null ) {
				return null;
			} else if (_notizen[tag][standort] == null) {
				return null;
			}
			
			return _notizen[tag][standort];
		}
		
		public function getFeiertag(tag:Number): String {
			if ( ! _feiertage ) return null;
			
			if ( _feiertage[tag] && _feiertage[tag]) {
				return _feiertage[tag].name;
			}
			
			return null;
		}
		
		private var _terminCount:Object = { };
		
		public function setTermin(tag:String, standort:String, t:Termin):void {
			GMBus.log("Setting Termin... tag: " + tag + " max: " + _maxTage.toString() + "termin: " + t.id);
			_terminOffset = null;
			// termine an den naechsten monat weitergeben wenn sie laenger dauern als
			// einen monat
			if (parseInt(tag) > _maxTage) {
				GMBus.log("nicht in diesem monat");
				MosaikConfig.getAC("termine").getItemAt(monat +1).setTermin( (parseInt(tag) - _maxTage ).toString(), standort, t);
				return;
			} else if ( ! _termine[tag] ) {
				GMBus.log("erstelle tag");
				_termine[tag] = new Object();
			}
			
			// find out the count for the current termin
			/*
			Logger.info("count ermitteln");
			if ( _terminCount[tag] == null) {
				_terminCount[tag] = new Object();
				_terminCount[tag][standort] = 0;
			} else if ( _terminCount[tag][standort] == null ) {
				_terminCount[tag][standort] = 0;
			}
			
			var count:int = _terminCount[tag][standort];
			Logger.info("Count ausgelesen:" + count.toString());
			
			if (_termine[tag][standort] == null ) {
				_termine[tag][standort] = [null,null,null,null,null];
			}
			
			*/
			if (_termine[tag][standort] == null ) {
				_termine[tag][standort] = [];
			}
			
			var count:int = _termine[tag][standort].length;
			
			if (t == null ) {
				_termine[tag][standort].push({});
				_cache[tag + "_" + standort + "_" + count.toString()] = null;
			} else {
				_termine[tag][standort].push(t._details);
				_cache[tag + "_" + standort + "_" + count.toString()] = t;
			}
			change = standort;
			change = "";
			//_terminCount[tag][standort] = count + 1;
			GMBus.log("Termin done..");
		}
		
		public function removeSeminar(id:String, standort:String, recurse:Boolean = true):Array {
			var deleted:Array = new Array();
			var suff:Array = new Array();
			var post:Array = new Array();
			GMBus.log("Remove.. Recurse:" + recurse.toString());
			
			if ( monat != 0 && recurse ) suff = MosaikConfig.getAC("termine").getItemAt(monat - 1).removeSeminar(id, standort, false);
			
			for ( var i:String in _termine) {
				for  ( var j:String in _termine[i][standort] ) {
					if (_termine[i] && _termine[i][standort] && _termine[i][standort][j] && _termine[i][standort][j].id == id) {
						//Logger.info("Remove: " + i );
						deleted.push(_cache[ i + "_" + standort + "_" + j.toString()]);
						_termine[i][standort][j] = null;
						_cache[ i + "_" + standort + "_" + j.toString()] = null;
					}
				}
			}
			
			if ( monat != 11 && recurse ) post = MosaikConfig.getAC("termine").getItemAt(monat + 1).removeSeminar(id, standort, false);
			
			change = standort;
			change = "";
			
			return suff.concat( deleted.concat(post) );
		}
		
		public function updateStatus (terminId:String, standort:String, status:String ):void {
			if ( !_termine ) return;
			
			for ( var i:String in _termine) {
				for (var j:String in _termine[i][standort]) {
					if (_termine[i] && _termine[i][standort] && _termine[i][standort][j] && _termine[i][standort][j].id == terminId) {
						//Logger.info("Status aenderung: " + i );
						_termine[i][standort][j].freigabe_flag = status;
						_termine[i][standort][j].verschoben = i;
					
						// FIXME: muss aus der datenbank ausgelesen werden
						if (status == "F") {
							_termine[i][standort][j].freigabe_veroeffentlichen = "1";
							_termine[i][standort][j].freigabe_farbe = "0x00FF00";
						} else if (status == "B") {
							_termine[i][standort][j].freigabe_veroeffentlichen = "0";
							_termine[i][standort][j].freigabe_farbe = "0xFFFF33";
						} else if (status == "P" ) {
							_termine[i][standort][j].freigabe_veroeffentlichen = "0";
							_termine[i][standort][j].freigabe_farbe = "0xFF0000";
						
						} else if (status == "A") {
							_termine[i][standort][j].freigabe_veroeffentlichen = "1";
							_termine[i][standort][j].freigabe_farbe = "0x0000FF";
						} else if (status == "X") {
							_termine[i][standort][j].freigabe_veroeffentlichen = "1";
							_termine[i][standort][j].freigabe_farbe = "0x000000";
						}
						
					
						_cache[i + "_" + standort + "_" + j] = new Termin(_termine[i][standort][j]);
					}
				}
			}
			change = standort;
			change = "";
		}
	}
}