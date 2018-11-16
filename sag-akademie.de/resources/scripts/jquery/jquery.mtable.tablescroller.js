(function($) {
	$.extend({
		mTableScroller: new function() {
			var config = null;
			var off = 0;			
			var def = this.defaults = {
				maxHeight: 5000,
				maxWidth: 5000
			};
			
			function addScrollbars(mTable) {//darf erst aufgerufen werden nachdem!!! der wrapper auf der seite ist!!!
					
					mTable.wrapper.scroll(function() {
						if ( mTable.headWrapper[0].scrollLeft == mTable.wrapper[0].scrollLeft ) return;
						mTable.headWrapper[0].scrollLeft = mTable.wrapper[0].scrollLeft;
					});
				
			};
			
			
			
			//event
			function fixHeaders(event) {
				with (event.data.mTable) {
					var visibleTh = head.find("tr > th:visible")
					var visibleTd = body.find("tr:last > td:visible");
					var thWidth = 0;
					colgroup.children("col").remove();
					headColgroup.children("col").remove();
					
					// make up the headers
					var cols = [];
					for (var i = 0; i < visibleTh.length; i++) {
						var $cTh = visibleTh.eq(i);
						
						var mId = (visibleTh.get(i)).id.replace(/dbTh/g,"")
						$cTh.data("mId", mId);
						$cTh.data("visibleIndex",i);
						cols.push("<col class='{id: "+mId+"} dbCol");
						cols.push(mId);
						cols.push("' />");
					}
					
					colgroup.append( cols.join(""));
					headColgroup.append( cols.join(""));
					
					/*$sizeTester = $("#sizeTester");
					
					for (var i = 0; i < visibleTh.length; i++) {
						var $cTh = visibleTh.eq(i);
						var $cTd = visibleTd.eq(i);
									 
						$sizeTester.html( $cTh.text() + "x" );

						var thW = $sizeTester.width() + 60; 
						if (thW === NaN) thW = 0;
						
						$("#sizeTester").html( $cTd.text() + "x" );
						var tdW =  $sizeTester.width() + 9; // fixme: 40?! (padding diff?!)
						if (tdW === NaN) tdW = 0;
						var w = tdW > thW ? tdW : thW;
						
						setVisibleColWidth(i, w);
						
						thWidth = thWidth + $cTh.outerWidth();
					}*/
					cols = colgroup.children("col");

					//head.width(thWidth + 10); // provide some proxy space
					//headRows.width(thWidth + 10);
					//setWidth(thWidth);
					
					headHeight = head.outerHeight();
					//head.height(headHeight);
					//headRows.height(headHeight);
					
					//size calculation finished, set to hardcoded now....
					addPaddingTop = headHeight;
				
					table.css("top", headHeight + "px");
					table.css("table-layout", "fixed");
				}
			};
			
			// event
			function initContextMenu(event) { // fixme: ein menue fuer alle inhalte
				with (event.data.mTable) {
					// fixme: sollte per event aufgerufen werden koennen
					body = table.find("tbody");
					rows = table.find("tr");

					body.children("tr").click(function(ev) {
						if (contextMenu != null) {
							contextMenu.hide();
							contextMenu = null;
						}
						return false;
					});
					
					wrapper.click(function() {
						if (contextMenu != null) {
							contextMenu.hide();
						}
					});
					
					
					$("#dbbuttons").rightClick (function (ev) {
						return false;
					});
					
					body.children("tr").rightClick(function(ev) {
						headerMenu.hide();
						contextMenu = $("#dbbuttons");

						if (contextMenu.css("display") != "none") {
							contextMenu.hide();
							return; 
						}
						
						contextMenu.css("left",ev.pageX);
						contextMenu.css("top",ev.pageY);
						contextMenu.css("position","fixed");
						contextMenu.css("z-index","9999");

						$.mosaikRuntime.replace ("#ID#", $(this).metadata().id);
					
						contextMenu.show();
					});
					
					body.find(".dbContextMenu a").click(function() {
						if (contextMenu != null) contextMenu.hide();
					});
				}
			}
			
			function initHeaderMenu(mTable) {
				with (mTable) {
					body.children("tr").click(function(ev) {
							headerMenu.hide();
					});
						
					wrapper.click(function () { 
						headerMenu.hide();
					});
					
					$("#tableMenu").appendTo(container);
					headerMenu = container.find("#tableMenu");
					columnsMenu = headerMenu.find(".tableSpaltenMenu");
					filterMenu = headerMenu.find(".tableFilterMenu");
					headerMenu.attr("id", "tableMenuObj" + table.attr("id"));
					
					container.find(".tableContextButton").click(function() {
						$this = $(this);
						if (contextMenu) {
							contextMenu.hide();
							contextMenu = null
						}

						headerMenu.css("top", ( $this.height() + 5) + "px");
						headerMenu.css("left", ($this.offset().left - $this.width()) + "px");
						
						var mId = $this.closest("th").data("mId");
						var cellIndex = $this.closest("th")[0].cellIndex;
						headerMenu.data("mId", mId);
						headerMenu.data("cellIndex", cellIndex);
						headerMenu.data("visibleIndex", $this.closest("th").data("visibleIndex"));

						headerMenu.toggle();
						columnsMenu.hide();
						filterMenu.hide();
						return false;
					});	
					
					headerMenu.find(".buttonAufsteigend").click(function() {
						sorter.wk( headerMenu.data("cellIndex") ,1);
						headerMenu.hide();
						table.trigger("updateContextMenu");
						return true;
					});
					
					headerMenu.find(".buttonAbsteigend").click(function() {
						sorter.wk( headerMenu.data("cellIndex") ,0);
						headerMenu.hide();
						table.trigger("updateContextMenu");
						
						return true;
					});
					
					headerMenu.find("#buttonSpaltenMenu").click(function() {
						filterMenu.hide();
						columnsMenu.toggle();
					});
					
					headerMenu.find("#buttonFilterMenu").click(function() {
						var index = headerMenu.data("visibleIndex");
						columnsMenu.hide();
						filterMenu.toggle();
						
						if (headerMenu.data("search") && headerMenu.data("search")[index]) {
							$("#tableSearchText").val( headerMenu.data("search")[index] );
						} else {
							$("#tableSearchText").val("");
						}
					});
					
					headerMenu.find("#tableSearchApply").click( function () {
						// FIXME: zellen ueber die id ansprechen!!! 
						// oder: zum speichern ID nutzen zum suchen nicht
						var index = headerMenu.data("visibleIndex");
						var search = headerMenu.data("search");
						if ( ! search ) search = {};
						var searchText = $("#tableSearchText").val();
						search[index] = searchText;
						headerMenu.data("search", search);
						
						body.children("tr").show();
						body.children().removeClass("filtered");
						
						mTable.foot.empty();
						var searches = [];
						
						for (var i in search) {
							if ( search[i] == "") continue;
							$.uiTableFilter(table, search[i], i);
							searches.push ( i );
							searches.push ( ": ");
							searches.push (search[i]);
						}
						
						mTable.foot.html( searches.join(" ") );
						
						table.trigger("updateScrollbars");
						headerMenu.hide();
					});
					
					table.trigger("rebindEvents");
				}
			};
			
			this.construct = function(settings) {
				return this.each(function() {
					var mTable = $(this).data("mTable");
					config = $.extend(this.config, $.mTableScroller.defaults, settings);
										
					addScrollbars(mTable);
					initHeaderMenu(mTable);
					mTable.table.css("height", "auto");
								
					$(this).bind("updateContextMenu", {
						mTable: mTable
					}, initContextMenu);
					$(this).bind("updateHeaders", {
						mTable: mTable
					}, fixHeaders);
					
					$(this).trigger("updateHeaders");
					$(this).trigger("updateContextMenu");
				});
			};
			
		}
		
	});
	// extend plugin scope
	$.fn.extend({
		mTableScroller: $.mTableScroller.construct
	});
})(jQuery);


