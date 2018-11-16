package {
	import flash.ui.ContextMenu;
	import flash.ui.ContextMenuItem;
	import mx.managers.PopUpManager;
	import mx.core.UIComponent;

	/**
	 * ...
	 * @author Christian Holzberger // MOSAIK Software
	 */
	public class Menu 
	{
		public var menu:ContextMenu = new ContextMenu();
		
		public function Menu() {
			var menuItem:ContextMenuItem = new ContextMenuItem("Details");
			menu.hideBuiltInItems();
			menu.customItems.push(menuItem);
		}
		
	}
	
}