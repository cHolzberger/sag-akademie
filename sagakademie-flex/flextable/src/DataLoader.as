package
{
	import com.mosaiksoftware.JsonProxy;
	
	import mx.core.FlexGlobals;

	public class DataLoader
	{
		private var _ds:JsonProxy = null;
		public var onResult:Function;
		public function set onFault(f:Function):void {
			_ds.faultCb = f;
		}
		
		public function DataLoader(url:String)
		{
			_ds = new JsonProxy ( url );	
		}
		
		public function update(parameters:Object):void {
			_ds.request(onResult, parameters);
		}
		
		public function save (save:Object, method:String, saveDone:Function):void {
			_ds.save(save, method, saveDone);
		}
		
		
		
	}
}