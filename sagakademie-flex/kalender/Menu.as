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
			var custom_menu:ContextMenu = new ContextMenu();
			custom_menu.hideBuiltInItems();
			
			var menuItem:ContextMenuItem = new ContextMenuItem("Details");
			custom_menu.push(menuItem);
			
			_root.menu = custom_menu

		}
		
	}
	
}