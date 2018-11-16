/**
 * @author Christian Holzberger
 * 03.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
var _ = function() { // internal variables
	this.tableHead = document.getElementById("head");
	this.tableFoot = document.getElementById("foot");
	this.tableBody = document.getElementById("body");
	this.sizeProxy = document.getElementById("sizeProxy");
	this.viewport = document.getElementById("viewport");

	
	this.headers = [];
	this.data = [];
	this.footers = [];
	
	this.rowHeight = 20;
	this.rowCount = Math.ceil( this.viewport.clientHeight / this.rowHeight);
	
	this.getRow = function (i) {
		return document.getElementById("row_"+ i);
	};
	
	document.body.style.overflowX="hidden";
	document.body.style.overflowY="hidden";
	return this;
}();

var lastOffset = 0;
_.viewport.onscroll = function () {
	var offset = Math.ceil(this.scrollTop / _.rowHeight);
	
	window.scroll(this.scrollLeft, this.scrollTop);

	if (lastOffset != offset) {
		updateData(offset);
		lastOffset = offset;
	}
};

function addHeader(name) {
	_.headers.push(name); 	
};

function addFooter(name) {
	_.footers.push(name);
};

function initTable() { //need to set headers before
	var cache = [];
	offset = 0;
	for (var i = 0,j; i <= _.data.length; i++) {
		if ( i % 2) cache.push ('<tr id="row_'+ i +'" class="odd">');
		else cache.push ('<tr id="row_'+ i +'" class="even">');
		
		if (_.data[i + offset]) {
			for (j = 0; j < _.data[i + offset].length; j++) {
				//cache.push('<td id="col_' + i + '_' + j + '" name="col_' + j + '">');
				//cache.push('</td>');
			}
		}
		cache.push("</tr>");
	}
	_.tableBody.innerHTML=cache.join("");
	_.sizeProxy.style.height = ((_.data.length) * _.rowHeight ) + "px";
	_.sizeProxy.style.width = ((_.headers.length) * 200 ) + "px";

}



function updateData(offset) {
	var cache = [];
	for (var i = 0,j; i <= Math.ceil( _.viewport.clientHeight / _.rowHeight); i++) {
		//if ( i % 2) cache.push ('<tr id="row_'+ i +'" class="odd">');
		//else cache.push ('<tr id="row_'+ i +'" class="even">');
		
		if (_.data[i + offset]) {
			cache=[];
			for (j = 0; j < _.data[i + offset].length; j++) {
				cache.push('<td id="col_' + (i+offset) + '_' + j + '" name="col_' + j + '">');
				cache.push(_.data[i + offset][j]);
				cache.push('</td>');	 
			}
			document.getElementById("row_" + (offset+ i)).innerHTML = cache.join("");
		} else {
			document.getElementById("row_" +(offset+i)).innerHTML = "<td/>";
			break;
		}
	}
	
	//_.tableBody.innerHTML = cache.join("");
}

function updateRow ( target, rowData) {
	var cache = [];
	
	cache.push ("<tr>");
	for ( i in headers ) {
		cache.push ("<td>");
		cache.push ( headers[i]);
		cache.push ("</td>");
	} 
	cache.push ("</tr>");
	
	target.innerHTML = cache.join("");
}

function setColWidth() {
	var cache = [];
	
	var cg = document.createElement("colgroup");
	
	for ( var i = 0; i< _.headers.length; i++) {
		var col=document.createElement("col");
		col.setAttribute("width", "200");
		cg.appendChild(col);	
	}
	
	
	var tb = document.getElementsByTagName("table");
	
	for ( var i=0; i < tb.length; i++) {
		tb[i].replaceChild(cg.cloneNode(true), tb[i].firstChild);		
	}
	
}


function update() {
	// these are "real tables"
	updateRow ( tableHead, headers);
	updateRow ( tableFoot, footers);
	initTable();
	updateData(0);
	setColWidth();
	// proxy table	
};

_.data=[];
for ( var i=0;i<20; i++ ) {
	addHeader("Header "+i);
}

for ( var i =0;i < 600; i++ ) {
	_.data[i]=[];
	for ( var j=0; j<20; j++) {
		data[i][j]=i+ "adasdasd asd asd" +j+" asd asd asd asd asd " + i + ":" + j;
	}
}

addFooter("bcd");

update();
