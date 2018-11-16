/**
 * @author molle
 */
(function($) {
	var tableCount = 0;
	
	$.extend({
		mTable: new function() {
			var self=this;
			/* caching object */
			this.mTable = {
				table: "x",
				headWrapper: null,
				headTable: null,
				head: null,
				body: null,
				foot: null,
				rows: null,
				headers: null,
				headRows: null,
				colgroup: null,
				cols: null,
				wrapper: null,
				container: null,
				element: null,
				
				currH: null,
				currV: null,
				
				hContainer: null,
				vContainer: null,
				addPaddingTop: null,
				minHeaderWidth: 30,
				
				contextMenu: null,
				headerMenu: null,
				columnMenu: null,
				filterMenu: null,
				cmVisible: null,
				
				sorter: null
			};
			
			function wrapTable(mTable) {
				with (mTable) {
					/*** 
					 * watch for maximum height
					 */
					var wH = table.height();
					var wW = table.width();
					
					if (config.height) {
						wH = config.height;
					} else if (wH > new Number(config.maxHeight)) {
						wH = config.maxHeight;
					}
					
					if (config.width) {
						wW = config.width;
					} else if (wW > new Number(config.maxWidth)) {
						wW = config.maxWidth;
					}
					/*
					wrapper = $('<div class="tsContainer ui-widget ui-widget-content">' +
					'<div class="tsWrapper ui-widget ui-widget-content"></div><div class="tsFooter ui-widget ui-widget-content">--</div></div>');
					
					table.wrap(wrapper);*/
					
					wrapper = table.parent();
					
					container = wrapper.parent();
					container.css("position", "relative");
					foot = container.find(".tsFooter");
					/* css fix */
					/* container.css("width", wW);
					 container.css("height", wH); */
					container.css("width", table.css("width"));
					container.css("height", table.css("height"));
					
					container.css("margin-top", table.css("margin-top"));
					container.css("margin-bottom", table.css("margin-bottom"));
					container.css("position", "relative");
					
					table.css("margin-top", "0px");
					table.css("margin-bottom", "0px");
			
					wrapper.css("position", "absolute");
					wrapper.css("margin-top", "24px");
					wrapper.css("margin-bottom", "26px");
					wrapper.css("top", "0px");
					wrapper.css("bottom", "0px");
					wrapper.css("left", "0px");
					wrapper.css("right", "0px");
					//wrapper.height(container.height() - 24 -26 ); //fixme scrollbar width (17) head height ( 20)
					//wrapper.width(container.width() );
				}
				return mTable;
			};
			
			function initTable($this, mTable) {
				with (mTable) {
					table = $this.find(".dbtable");
					container = $this;
					/* <thead> <tbody> <colgroup> */
					head = $this.find("thead");
					headTable = $this.find(".headTable");
					body = table.find("tbody");
					//foot = table.children("tfoot");
					colgroup = table.find("colgroup");
					cols = colgroup.find("col");
					/* all trs inside the table */
					rows = table.find("tr");
					/* looking for ths only inside the <thead> speeds up things */
					headers = head.find("th");
					headRows = head.children("tr");
					
					// adding classes
					table.addClass("tsTable");
					body.addClass("tsTbody");
					if (body.children().length == 0) {
						body.append('<tr><td colspan="' + headRows.children().length + '">Keine Daten vorhanden</td></tr>');
					}
					head.addClass("tsHead");
					
					//foot.addClass("tsHead");
					headers.addClass("tsTh");
					rows.addClass("tsTr");
					
					headColgroup = headTable.find("colgroup");
					headWrapper = container.find(".headWrapper");
					headTable = headWrapper.find(".headTable");
					head = headTable.find("thead");
					headers = head.find("th");
					headRows = head.find("tr");
				}
				
				return mTable;
			};
			
			function initSorter(mTable) {
				with (mTable) {
					$(".dbtable").attr("id", "dbtable" + tableCount);
					
					sorter = new TINY.table.sorter("sorter");
					sorter.paginate = false;
					sorter.even = "even";
					sorter.odd = "odd";
					sorter.init("dbtable" + tableCount);
					table.find("tr:odd").addClass("odd");
					table.find("tr:even").addClass("even");
					
					tableCount++;
				} 
				return mTable;
			};
			
			this.construct = function(settings) {
				return this.each(function() {
					config = $.extend(this.config, {}, settings);
					var mTable = $.extend({}, {}, {}, self.mTable);
					var $this = $(this); 
					
					initTable($(this), mTable);
					wrapTable(mTable);
					
					initSorter(mTable);

					$this.data("mTable", mTable);
					
					mTable.saveState = function ( fnkt ) {
						if ( fnkt === undefined) {
							mTable.table.trigger("saveState");
						} else {
							mTable.table.bind("saveState", {mTable: mTable}, fnkt);
						}
					}
					
					mTable.loadState = function ( fnkt ) {
						if ( fnkt === undefined) {
							mTable.table.trigger("loadState");
						} else {
							mTable.table.bind("loadState",{mTable: mTable}, fnkt);
						}
					}
					
					mTable.setColWidthById = function (colId, width) { //col id without dbTh or dbCol
						mTable = $this.data("mTable");
						width = parseFloat(width);
						if (width < mTable.minHeaderWidth ) width = mTable.minHeaderWidth;

						mTable.table.children("colgroup").children("col.dbCol" + colId).width(width + "px");
						mTable.table.children("colgroup").children("col.dbCol" + colId).attr("width", width );
						
						mTable.headTable.children("colgroup").children("col.dbCol" + colId).width((width )+ "px");
						mTable.headTable.children("colgroup").children("col.dbCol" + colId).attr("width", (width) ); // -2 is strange!!!
					};
					
					mTable.getColWidthById = function (colId) {
						return parseFloat(mTable.table.children("colgroup").find("col.dbCol" + colId ).css("width"));
					};
					
					mTable.getCols = function () {
						var ret = [];
						var cols = mTable.headTable.find("th");
						for ( var i =0; i< cols.length; i++ ) {
							ret.push ( cols.eq(i).data("mId") );
						}
						return ret;
					};
					
					mTable.calculateWidth = function ( id ) {
						$sizeTester = $("#sizeTester");
						var $cTh = mTable.headTable.find("#dbTh"+id); 
								 
						$sizeTester.html( $cTh.text() + "xxxx" );
		
						var thW = $sizeTester.width() + 60; 
						if (thW === NaN) thW = 0;
							
						return thW;
					};
					
					mTable.setColWidth = function (col, width) {
						//mTable = $this.data("mTable");
						width = parseFloat(width);
						var target = col;
						if (width < mTable.minHeaderWidth ) width = mTable.minHeaderWidth;
						
						var colNum = 0;
						for (var i = 0; i < col; i++) {
							if (mTable.headers.eq(i).css("display") != "none") {
								colNum = colNum + 1;
							} else { 
								col ++;
							}
						}
						mTable.table.children("colgroup").children("col").eq(colNum).attr("width", width);
						mTable.table.children("colgroup").children("col").eq(colNum).width(width + "px");
						
						mTable.headTable.children("colgroup").children("col").eq(colNum).width( (width-1) + "px");
						mTable.headTable.children("colgroup").children("col").eq(colNum).attr("width", (width-1) );
					};
					
					mTable.setVisibleColWidth = function (col, width) {
						//mTable = $this.data("mTable");
						width = parseFloat(width);
						if (width < mTable.minHeaderWidth ) width = mTable.minHeaderWidth;
						
						mTable.table.children("colgroup").children("col").eq(col).width(width + "px");
						mTable.table.children("colgroup").children("col").eq(col).attr("width", width );
						
						mTable.headTable.children("colgroup").children("col").eq(col).width((width )+ "px");
						mTable.headTable.children("colgroup").children("col").eq(col).attr("width", (width) ); // -2 is strange!!!
					};
					
					mTable.updateWidth = function () {
						//mTable = $this.data("mTable");
						var width = 0;
						mTable.headTable.find("col").each(function() {
							width = width + $(this).width();
						});
						mTable.table.width(width);
						mTable.headTable.width(width);
					};
					
					mTable.setWidth = function (width) {
						//mTable = $this.data("mTable");
						width = parseFloat(width);
						
						mTable.table.width(width);
						mTable.headTable.width(width);
						//mTable.table.css("width","auto");
						//mTable.headTable.css("width","auto");
					};
				});
			};
	 }
	});
	// extend plugin scope
	$.fn.extend({
		mTable: $.mTable.construct,
		saveState: $.mTable.saveState,
		loadState: $.mTable.loadState
	});
})(jQuery);