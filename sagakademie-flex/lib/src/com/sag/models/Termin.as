package com.sag.models
{
	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	[Bindable]
	public class Termin
	{
		public var _details:Object;
		public var date:Date;

		public function Termin(details:Object) {
			_details = details;
		}
		
		public function set verschoben(value:String):void {
			_details.verschoben = value;
		}
		
		public function get verschoben():String {
			return _details.verschoben;
		}
		
		public function set details (det:Object):void {
			_details = det;
		}
		
		public function get id ():String {
			return _details.id;
		}
		
		public function get details ():Object {
			return _details;
		}
		
		public function get kursnr():String {
			return _details.kursnr;
		}
		
		public function get inhouse_ort():String {
			return _details.inhouse_ort;
		}
		
		public function get inhouse_plz():String {
			return _details.inhouse_plz;
		}
		
		public function get inhouse_firma():String {
			return _details.inhouse_firma;
		}
		
		public function get teilnehmer():String {
			return _details.teilnehmer;
		}
		
		public function get inhouse():String {
			return _details.inhouse;
		}
		
		public function get name():String {
			if ( _details.inhouse == 1 ) {
				return "(I) " + _details.seminar_art_id;
			} else {
				return _details.seminar_art_id;
			}
		}
		
		public function set name(seminar_id:String):void {
			
			_details.seminar_art_id = seminar_id;
		}
		
		public function get dauer():Number {
			return parseInt(_details.dauer);
		}
		
		public function get erster_tag():Number {
			return parseInt(_details.erster_tag);
		}
		
		public function set erster_tag(i:Number):void {
			_details.erster_tag=i;
		}
		
		public function get standort():String {
			return _details.standort_id;
		}
		
		public function set color(color:Number):void {
			_details.farbe = color;
		}
		
		public function get color(): Number {
			if (_details.farbe == "") {
				_details.farbe = "0xffffff";
			}
			return new Number(_details.farbe);
		}
		
		public function set fontColor(color:Number):void {
			
			_details.textfarbe = color;
		}
		
		public function get fontColor(): Number {
			return new Number(_details.textfarbe);
		}
		
		public function get status(): String {
			return _details.freigabe_flag;
		}
		
		public function set status(flag:String): void {
			_details.freigabe_flag = flag;
		}
		
		public function get freigabeFarbe():Number {
			return new Number(_details.freigabe_farbe);
		}
		
		public function set freigabeFarbe(farbe:Number):void {
			_details.freigabe_farbe = farbe;
		}
		
		public function get planungInfo (): String {
			return _details.planung_info;
		}
		
		public function set planungInfo (s:String):void {
			_details.planung_info = s;
		}
		
		public function get freigegeben():Boolean {
			return _details.freigabe_veroeffentlichen == "1";
		}
		
		public function get referenten():String {
			return _details.referenten;
		}
		
		public function get seminar_art_id():String {
			return _details.seminar_art_id;
		}
		
		public function set seminar_art_id(s:String):void {
			_details.seminar_art_id=s;
		}
		
		public function get seminar_art_gesperrt():Boolean {
			return _details.seminar_art_gesperrt=="1";
		}
		
		public function set seminar_art_gesperrt(s:Boolean):void {
			if (s) _details.seminar_art_gesperrt = "1";
			else _details.seminar_art_gesperrt = "0";
		}
		
		public function get standort_gesperrt():Boolean {
				return _details.standort_gesperrt=="1";
		}
		
		public function set standort_gesperrt(s:Boolean):void {
			if (s) _details.standort_gesperrt = "1";
			else _details.standort_gesperrt = "0";
		}
		
		public function get aktualisierung_gesperrt():Boolean {
			return _details.aktualisierung_gesperrt=="1" ;
		}
		
		public function set aktualisierung_gesperrt(s:Boolean):void {
			if (s) _details.aktualisierung_gesperrt = "1";
			else _details.aktualisierung_gesperrt = "0";
		}
	}
}