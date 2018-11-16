/*
 * jQuery columnManager plugin
 * Version: 0.2.5
 *
 * Copyright (c) 2007 Roman Weich
 * http://p.sohei.org
 *
 * Dual licensed under the MIT and GPL licenses 
 * (This means that you can choose the license that best suits your project, and use it accordingly):
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Changelog: 
 * v 0.2.5 - 2008-01-17
 *	-change: added options "show" and "hide". with these functions the user can control the way to show or hide the cells
 *	-change: added $.fn.showColumns() and $.fn.hideColumns which allows to explicitely show or hide any given number of columns
 * v 0.2.4 - 2007-12-02
 *	-fix: a problem with the on/off css classes when manually toggling columns which were not in the column header list
 *	-fix: an error in the createColumnHeaderList function incorectly resetting the visibility state of the columns
 *	-change: restructured some of the code
 * v 0.2.3 - 2007-12-02
 *	-change: when a column header has no text but some html markup as content, the markup is used in the column header list instead of "undefined"
 * v 0.2.2 - 2007-11-27
 *	-change: added the ablity to change the on and off CSS classes in the column header list through $().toggleColumns()
 *	-change: to avoid conflicts with other plugins, the table-referencing data in the column header list is now stored as an expando and not in the class name as before
 * v 0.2.1 - 2007-08-14
 *	-fix: handling of colspans didn't work properly for the very first spanning column
 *	-change: altered the cookie handling routines for easier management
 * v 0.2.0 - 2007-04-14
 *	-change: supports tables with colspanned and rowspanned cells now
 * v 0.1.4 - 2007-04-11
 *	-change: added onToggle option to specify a custom callback function for the toggling over the column header list
 * v 0.1.3 - 2007-04-05
 *	-fix: bug when saving the value in a cookie
 *	-change: toggleColumns takes a number or an array of numbers as argument now
 * v 0.1.2 - 2007-04-02
 * 	-change: added jsDoc style documentation and examples
 * 	-change: the column index passed to toggleColumns() starts at 1 now (conforming to the values passed in the hideInList and colsHidden options)
 * v 0.1.1 - 2007-03-30
 * 	-change: changed hideInList and colsHidden options to hold integer values for the column indexes to be affected
 *	-change: made the toggleColumns function accessible through the jquery object, to toggle the state without the need for the column header list
 *	-fix: error when not finding the passed listTargetID in the dom
 * v 0.1.0 - 2007-03-27
 */

(function($) {
	$.extend ( {
		mTableColumnManager: new function () {
			var self = this;
			var settings = {};
			
			var defaults = {
				listTargetID : ".dbColumnManager",
				onClass : 'dbColumnOn',
				offClass : 'dbColumnOff',
				saveState: true
			};
	
			var idCount = 0;
			var mTable = null;
	
			// save via xsettings
			this.saveState = function(event) {
				mTable = event.data.mTable;
				
				if ( mTable.cmVisible ) {
					$.xSettings.set( getPageNS(), "showTable", mTable.cmVisible.join(":"));
				} 
			};
			
			// load via xsettings
			this.loadState = function(event) {
				mTable = event.data.mTable;
				
				var val = $.xSettings.get( getPageNS(), "showTable", false).value;

				if ( val && val != "Array") {
					var ar = val.toString().split(':');
					if (mTable.cmVisible === null) mTable.cmVisible = [];

					for ( var i = 0; i < ar.length; i++ ) {
						if ( ar[i] == "false") mTable.cmVisible[i]=false;
						else ar[i] = mTable.cmVisible[i]=true;
					}

					return mTable.cmVisible;
				}
				return mTable.cmVisible = [];
			};
	
			// sets columns state
			var setColumnState = function ($table, columns, state ){
				mTable = $table.closest(".dbContainer").data("mTable");
				
				if ( columns.constructor == Number ) 
					columns = [columns];
				
				for ( var i = 0; i < columns.length; i++ ) {
					if (state == "invert") {
						mTable.cmVisible[columns[i]] = !mTable.cmVisible[columns[i]];
					} else {
						mTable.cmVisible[columns[i]] = state;
					}
				}
						
				mTable.saveState();
				$table.updateColumns();
			};
			
			var toggleClick = function(mTable) {
				var mTable = $(this).closest(".dbContainer").data("mTable"); 
				var col = $(this).metadata().col;
				if ( col !== undefined) {				
					mTable.table.toggleColumns([col]);
				}
			};
			/**
		 	* Creates the column header list.
		 	* @param {element} table	The table element for which to create the list.
		 	*/
			var createColumnHeaderList = function(mTable)
			{
			
				if ( !settings.listTargetID )
				{
					return;
				}
				var $target = $(settings.listTargetID);
				if ( !$target.length )
				{
					return;
				}
				
				var headRow = mTable.head[0].rows[0];
				var cells = headRow.cells;
				
				if ( !cells.length )
				{
					return; //no header - nothing to do
				}
				//create list in target element
				var $list = [];
		
				$list.push('<ul class="colManagerList">');
			
				var colsVisible = mTable.cmVisible;
				//create list elements from headers
				for ( var i = 0; i < cells.length; i++ )
				{
					colsVisible[i] = ( colsVisible[i] !== undefined ) ? colsVisible[i] : true;
					var text = $(cells[i]).text(), addClass;
					text = trim(text);
					if ( !text.length )	{
						
						text = $(cells[i]).html();
						text= trim(text);
						if ( !text.length ) //still nothing?
						{
							text = 'undefined';
						}
					}
					if ( colsVisible[i] && settings.onClass ) {
						addClass = settings.onClass;
					} else if ( !colsVisible[i] && settings.offClass ) {
						addClass = settings.offClass;
					}
					
					
					if (text != "undefined") {
						$list.push ( '<li class="' + addClass + ' {col: ' + i + '}">' + text + '</li>');
					}
				}
				$list.push('</ul>');
				
				$target.append($list.join(""));
				bindClick();
			};

			function bindClick() {
				//console.log("bind click");
				$(".dbColumnOff").unbind('click',toggleClick);
				$(".dbColumnOn").unbind('click',toggleClick);
			
				$(".dbColumnOff").click(toggleClick)
				$(".dbColumnOn").click(toggleClick)
			};
			
	/**
	 * Manages the column display state for a table.
	 *
	 * Features:
	 * Saves the state and recreates it on the next visit of the site (requires cookie-plugin).
	 * Extracts all headers and builds an unordered(<UL>) list out of them, where clicking an list element will show/hide the matching column.
	 *
	 * @param {map} options		An object for optional settings (options described below).
	 *
	 * @option {string} listTargetID	The ID attribute of the element the column header list will be added to.
	 *						Default value: null
	 * @option {string} onClass		A CSS class that is used on the items in the column header list, for which the column state is visible 
	 *						Works only with listTargetID set!
	 *						Default value: ''
	 * @option {string} offClass		A CSS class that is used on the items in the column header list, for which the column state is hidden.
	 *						Works only with listTargetID set!
	 *						Default value: ''
	 * @option {array} hideInList	An array of numbers. Each column with the matching column index won't be displayed in the column header list.
	 *						Index starting at 1!
	 *						Default value: [] (all columns will be included in the list)
	 * @option {array} colsHidden	An array of numbers. Each column with the matching column index will get hidden by default.
	 *						The value is overwritten when saveState is true and a cookie is set for this table.
	 *						Index starting at 1!
	 *						Default value: []
	 * @option {boolean} saveState	Save a cookie with the sate information of each column.
	 *						Requires jQuery cookie plugin.
	 *						Default value: false
	 * @option {function} onToggle	Callback function which is triggered when the visibility state of a column was toggled through the column header list.
	 *						The passed parameters are: the column index(integer) and the visibility state(boolean).
	 *						Default value: null
	 *
	 * @option {function} show		Function which is called to show a table cell.
	 *						The passed parameters are: the table cell (DOM-element).
	 *						Default value: a functions which simply sets the display-style to block (visible)
	 *
	 * @option {function} hide		Function which is called to hide a table cell.
	 *						The passed parameters are: the table cell (DOM-element).
	 *						Default value: a functions which simply sets the display-style to none (invisible)
	 *
	 * @example $('#table').columnManager([listTargetID: "target", onClass: "on", offClass: "off"]);
	 * @desc Creates the column header list in the element with the ID attribute "target" and sets the CSS classes for the visible("on") and hidden("off") states.
	 *
	 * @example $('#table').columnManager([listTargetID: "target", hideInList: [1, 4]]);
	 * @desc Creates the column header list in the element with the ID attribute "target" but without the first and fourth column.
	 *
	 * @example $('#table').columnManager([listTargetID: "target", colsHidden: [1, 4]]);
	 * @desc Creates the column header list in the element with the ID attribute "target" and hides the first and fourth column by default.
	 *
	 * @example $('#table').columnManager([saveState: true]);
	 * @desc Enables the saving of visibility informations for the columns. Does not create a column header list! Toggle the columns visiblity through $('selector').toggleColumns().
	 *
	 * @type jQuery
	 *
	 * @name columnManager
	 * @cat Plugins/columnManager
	 * @author Roman Weich (http://p.sohei.org)
	 */
	this.construct = function(options)
	{
		settings = $.extend({}, defaults, options);
				
        return this.each(function() {
			$this = $(this);
			mTable = $this.data("mTable");
			mTable.saveState(self.saveState);
			mTable.loadState(self.loadState);
			//get saved state - and overwrite defaults
			self.loadState({data: {mTable: mTable}});
			
			//create column header list
			$this.bind("updateHeaders", bindClick);
			$this.bind("rebindEvents", bindClick);
			$this.updateColumns();
			createColumnHeaderList(mTable);
        }); 
	};

	/**
	 * Shows or hides table columns.
	 *
	 * @param {integer|array} columns		A number or an array of numbers. The display state(visible/hidden) for each column with the matching column index will get toggled.
	 *							Column index starts at 1! (see the example)
	 *
	 * @param {map} options		An object for optional settings to handle the on and off CSS classes in the column header list (options described below).
	 * @option {string} listTargetID	The ID attribute of the element with the column header.
	 * @option {string} onClass		A CSS class that is used on the items in the column header list, for which the column state is visible 
	 * @option {string} offClass		A CSS class that is used on the items in the column header list, for which the column state is hidden.
	 * @option {function} show		Function which is called to show a table cell.
	 * @option {function} hide		Function which is called to hide a table cell.
	 *
	 * @example $('#table').toggleColumns([2, 4], {hide: function(cell) { $(cell).fadeOut("slow"); }});
	 * @before <table id="table">
	 *   			<thead>
	 *   				<th>one</th
	 *   				<th>two</th
	 *   				<th>three</th
	 *   				<th>four</th
	 *   			</thead>
	 * 		   </table>
	 * @desc Toggles the visible state for the columns "two" and "four". Use custom function to fade the cell out when hiding it.
	 *
	 * @example $('#table').toggleColumns(3, {listTargetID: 'theID', onClass: 'vis'});
	 * @before <table id="table">
	 *   			<thead>
	 *   				<th>one</th
	 *   				<th>two</th
	 *   				<th>three</th
	 *   				<th>four</th
	 *   			</thead>
	 * 		   </table>
	 * @desc Toggles the visible state for column "three" and sets or removes the CSS class 'vis' to the appropriate column header according to the visibility of the column.
	 *
	 * @type jQuery
	 *
	 * @name toggleColumns
	 * @cat Plugins/columnManager
	 * @author Roman Weich (http://p.sohei.org)
	 */
	this.updateColumns = function()
	{
        var ret = this.each(function() 
        {
			var colsVisible = mTable.cmVisible;
			if ( !colsVisible )	colsVisible = mTable.cmVisible = [];
			
			var onC = settings.onClass;
			var offC = settings.offClass;
			
			for (var i=0;i< mTable.headers.length; i++) {
				//set toggle direction
				if ( colsVisible[i] === undefined ) {
					colsVisible[i] = true;
				}
				
				if ( colsVisible[i] ) { //show
					mTable.headers.eq(i).show();	
					mTable.rows.find("td:eq("+i+")").show();	
					if ( settings && settings.listTargetID ) {
						$(settings.listTargetID).children("ul").children("li").eq(i).removeClass(offC);
						$(settings.listTargetID).children("ul").children("li").eq(i).addClass(onC);
					} 	
				} else { // hide
					mTable.headers.eq(i).hide();
					mTable.rows.find("td:eq("+i+")").hide();
					if ( settings && settings.listTargetID ) {
						$(settings.listTargetID).children("ul").children("li").eq(i).addClass(offC);
						$(settings.listTargetID).children("ul").children("li").eq(i).removeClass(onC);
					} 	
				}
			}
			
			mTable.table.trigger("updateHeaders");
		});
		
		return ret;
	};

	this.showColumns = function(columns, cmo) {
        return this.each(function() {
			setColumnState($(this), columns, true);		
		});
	};

	/**
	 * Hides table columns.
	 *
	 * @param {integer|array} columns		A number or an array of numbers. Each column with the matching column index will get hidden.
	 *							Column index starts at 0!
	 *
	 *
	 * @example $('#table').hideColumns(3);
	 * @desc Hide column number three.
	 *
	 * @type jQuery
	 *
	 * @name hideColumns
	 * @cat Plugins/columnManager
	 * @author Roman Weich (http://p.sohei.org)
	 */
	this.hideColumns = function(columns) {
        return this.each(function() {
			setColumnState($(this), columns, false);
		});
	};
	
	this.toggleColumns = function ( columns ) {
		return this.each(function() {
			setColumnState($(this), columns, "invert");
		});
	}
	}
	});
	
	$.fn.extend({
		mTableColumnManager: $.mTableColumnManager.construct,
		toggleColumns: $.mTableColumnManager.toggleColumns,
		hideColumns: $.mTableColumnManager.hideColumns,
		showColumns: $.mTableColumnManager.showColumns,
		updateColumns: $.mTableColumnManager.updateColumns,
	});
})(jQuery);
