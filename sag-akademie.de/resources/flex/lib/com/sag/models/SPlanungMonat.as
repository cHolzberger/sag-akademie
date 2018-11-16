package com.sag.models
{
	import com.sag.models.Termin;
	import flash.events.Event;
	import org.osflash.thunderbolt.Logger;
	import mx.collections.ArrayCollection;
	import mx.binding.utils.ChangeWatcher;
	import mx.controls.Alert;
	import com.mosaik.MosaikConfig;
	import mx.collections.ArrayCollection;

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
;
			year = cyear;
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
			return new Date( year, monat, d );
		}
		
		public function hasTermin(tag:int, dauer:int, standort:String):Boolean {
			//Logger.info("Tag: " + tag.toString() + " Dauer: " + dauer.toString());
			
			if ( !_termine) {
				Logger.info ("Termine not set"  );
				return false;
				
			} else if ( tag > _maxTage) {
				Logger.info("Tag zu hoch")
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
			Logger.info("Setting Termin... tag: " + tag + " max: " + _maxTage.toString() + "termin: " + t.id);
			
			// termine an den naechsten monat weitergeben wenn sie laenger dauern als
			// einen monat
			if (parseInt(tag) > _maxTage) {
				Logger.info("nicht in diesem monat");
				MosaikConfig.getAC("termine").getItemAt(monat +1).setTermin( (parseInt(tag) - _maxTage ).toString(), standort, t);
				return;
			} else if ( ! _termine[tag] ) {
				Logger.info("erstelle tag");
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
			Logger.info("Termin done..");
		}
		
		public function removeSeminar(id:String, standort:String, recurse:Boolean = true):Array {
			var deleted:Array = new Array();
			var suff:Array = new Array();
			var post:Array = new Array();
			
			Logger.info("Remove.. Recurse:", recurse);
			
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
						} else {
							_termine[i][standort][j].freigabe_veroeffentlichen = "0";
							_termine[i][standort][j].freigabe_farbe = "0xFF0000";
						
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