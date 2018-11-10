package com.mosaiksoftware.dgcomponents
{
	import mx.core.IFactory;
	
	public class BoolEditorFactory implements IFactory
	{
		public function BoolEditorFactory()
		{
		}
		
		public function newInstance():*
		{
			
			return new BoolEditor();
		}
	}
}