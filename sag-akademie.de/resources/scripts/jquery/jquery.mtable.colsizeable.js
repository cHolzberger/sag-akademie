(function($) {
	$.extend({
		mTableColsizeable: new function() {
			var mTable = null;
			var self = this;
			var defaults = {
				dragCells: "tr:first th",// cells for allocating column sizing handlers (by default: all cells of first row)
				dragMove: true, // see column moving its width? (true/false)
				dragProxy: "line", // Shape of dragging ghost ("line"/"area")
				dragOpacity: 0.3, // Opacity for dragging ghost (0 - 1)
				minWidth: 8, // width for minimized column (px)
				fixWidth: false, // table with fixed width? (true/false)
				fxHide: "fadeOut", // effect for hiding (fadeOut/hide/slideUp)
				fxSpeed: 200, // speed for hiding (miliseconds)
				namespace: "kiketable-colsizable",
				classHandler: "kiketable-colsizable-handler",
				classDragLine: "kiketable-colsizable-dragLine",
				classDragArea: "kiketable-colsizable-dragArea",
				classMinimized: "kiketable-colsizable-minimized",
				title: 'Expand/Collapse this column',
				renderTime: 0,
				onLoad: function() {
				}
				
			};
			
			// save via xsettings
			this.saveState = function(event) {
				mTable = event.data.mTable;
				var cols = mTable.getCols();
				
				for (var i=0; i < cols.length; i++) {
					var col = cols[i];
					//console.log("saving " + col);
					$.xSettings.set( getPageNS(), "col" + col, mTable.getColWidthById(col) );
				}
			};
			
			// load via xsettings
			this.loadState = function(event) {
				mTable = event.data.mTable;
				
				var cols = mTable.getCols();
				
				for (var i=0; i<cols.length; i++) {
					var col = cols[i];
					var val = $.xSettings.get( getPageNS(), "col" + col, false).value;
					if (val == false || val < 50) {
					    mTable.setColWidthById(col, mTable.calculateWidth(col));
					} else {
						mTable.setColWidthById(col, val);
					} 
				}
				mTable.updateWidth();
			};
			
			this.construct = function(o) {

				$(this).each(function() { // default parameters, properties or settings
					o = $.extend({}, defaults, o);
					o.dragProxy = (o.dragProxy === "line") ? false : true;
					// only for "virgin" html table(s)
					
					$(this).find("table.dbtable:not(." + o.namespace + ")").addClass(o.namespace).each( function(index) {
						var $this = $(this); 

						var mTable = $this.closest(".dbContainer").data("mTable");
						mTable.saveState( self.saveState );
						mTable.loadState( self.loadState );
						o.renderTime = new Date().getTime();
						//
						// global variables
						//
						var oTable = mTable.table[0], $Table = mTable.table;

						mTable.headTable.find("th").each(function() {
							$(this).wrapInner('<div class="kiketable-th-text"></div>');
						});
						
						mTable.headTable.find(o.dragCells).each(function(index) {
							$('<div class="' + o.classHandler + '" title="' + o.title + '"></div>').prependTo(this).bind('dragstart', function(e) {// bind a dragstart event, return the proxy element
								this.index = $(this).closest("th").data("visibleIndex");
								this.$th = $(this).closest("th");
								
								this.cell_width = this.$th.outerWidth();
								this.table_width = $Table.width();
								this.left0 = e.offsetX;
								this.d1 = this.cell_width - this.left0; // precalc for drag event
								this.d2 = o.minWidth - this.d1; // precalc for drag event
								return $(this).clone().appendTo(mTable.wrapper).css("opacity", o.dragOpacity).css("position", "absolute").css((o.dragProxy) ? {
									top: 0,
									left: this.$th.position().left + mTable.wrapper.scrollLeft(),
									width: this.cell_width
								} : {
									top: mTable.body.offset().top,
									left: e.offsetX
								}).removeClass(o.classHandler).addClass((o.dragProxy) ? o.classDragArea : o.classDragLine).height($Table.height());
								
							}).bind('drag', (o.dragMove || o.dragProxy) ? function(e) { // bind a drag event, update proxy position
								var w = e.offsetX + this.d1;
								if (w - this.d2 - this.d1 >= 0) {
									e.dragProxy.style.width = w + "px";
									if (o.dragMove) {
										mTable.setVisibleColWidth(this.index, w);
										
										if (!o.fixWidth) {
											mTable.setWidth(this.table_width - this.cell_width + w);
										}
									}
								}
								
							}
							
 : function(e) {
								var x = e.offsetX;
								if (x - this.d2 >= 0) {
									e.dragProxy.style.left = x + "px";
								}
							}).bind('dragend', function(e) {// bind a dragend event, remove proxy
								//mTable = $(this).closest(".dbContainer").data("mTable");
								this.index = this.$th.data("visibleIndex");
								this.$th = $(this).closest("th");
								if (!o.dragMove) {
									var delta = parseInt(e.dragProxy.style.left) - this.left0;
									var width = (o.dragProxy) ? e.dragProxy.style.width : (this.cell_width + delta);
									mTable.setVisibleColWidth(this.index, width); // cell width
									// change table width (if not fixed) 
									if (!o.fixWidth) {
										mTable.setWidth((o.dragProxy) ? this.table_width - this.cell_width + parseInt(e.dragProxy.style.width) : this.table_width + delta);
									}
									
								}
								$(e.dragProxy)[o.fxHide](o.fxSpeed, function() {
									$(this).remove();
								});
								
								mTable.saveState();
							});
						});
						o.renderTime = (new Date().getTime()) - o.renderTime;
						o.onLoad();
						
					});
				});
			};
			return this;
		}
		
	});
	
	$.fn.extend({
		mTableColsizeable: $.mTableColsizeable.construct
	});
})(jQuery);
;;
