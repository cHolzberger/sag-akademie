package com.mosaiksoftware 
{
	import flash.display.BitmapData;
	import spark.components.Group;
	import mx.controls.Text;
	import flash.geom.Matrix;
	import flash.text.TextFormat;
	import flash.text.TextField;
	import flash.display.Bitmap;
	import mx.controls.Label;
	/**
	 * ...
	 * @author asd
	 */
	public class SpriteGenerator
	{
		
		public function SpriteGenerator() 
		{
			
		}
		
		public function getImage(name:String,color:Object):Bitmap {
			var g:Group = new Group();

			g.width=100;
			g.height=20;
			var lbl:Label = new Label();

			lbl.text = name;
			lbl.opaqueBackground = color;
			g.addElement(lbl);

			var b:BitmapData = new BitmapData(g.width,g.height,false);
			b.draw(g);
			GMBus.log("Bitmap " + g.width + " x " + g.height);
			return new Bitmap(b);
		}
	}
}