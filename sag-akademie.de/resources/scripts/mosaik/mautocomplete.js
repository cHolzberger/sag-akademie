/**
 * @author Christian Holzberger (ch@mosaik-software.de)
 */
var MAutoComplete = function(targetId, displayTargetId, dataUrl, updatePrefix ) {
	if ( ! dataUrl ) dataUrl = "#";
	if ( ! updatePrefix ) updatePrefix = "input_HotelBuchung_";
	var containerId = "mCompleteContainer";
	var closeId = "mCompleteContainerClose";
	var loadingId = "mCompleteLoading";
	var nohit = "<div style='padding: 5px;'><b>Keine Ergebnisse</b></div>";
	var self = this;
	var locked = false;
	
	this.cache = {};
	
	var $container = self.$container = $("#" + containerId);
	var $close = self.$close = $("#" + closeId);
	var $input = self.$input = $("#" + displayTargetId);
	var $inputId  = self.$inputId = $("#" + targetId);
	var $loading = self.$loading = $("#" + loadingId);
	
	var interval = null;
	var previnput = null;
	var selected = 0;
	var scrollInterval = null;
	// callbacks
	this.onselect = null; 
	this.ondata = null; 
	this.filterData = null;

	function highlight (offset, scrollIntoView) {
		$(".mCompleteElement").removeClass("mCompleteElement-hover");
		$(".mCompleteContainer .mCompleteElement").eq(selected).addClass("mCompleteElement-hover");
		
		if ( scrollIntoView ) {
			$(".mCompleteContainer .mCompleteElement").eq(selected)[0].scrollIntoView();
			if ( $(".mCompleteContainer .mCompleteElement").length != selected ) {
				$(".mCompleteContainer").scrollTop( $(".mCompleteContainer").scrollTop() + scrollIntoView);
			}
		}
	}

	function activate (element) {
		$input.val(element.children(".label").text());
		if ($input.val() == "") element.text();
		$inputId.val(element.metadata().value);
		$(".mCompleteElement").removeClass("mCompleteElement-active");
		preselect();
	}

	function preselect() {
		$(".id_" + $inputId.val() ).addClass("mCompleteElement-active");
	}

	$("div.mCompleteContainer").mousemove(function (ev) {
		if ( $(ev.target).hasClass("mCompleteElement") ) {
			selected = $(".mCompleteContainer .mCompleteElement").index(ev.target);	
			highlight(selected);
		} else if ( $(ev.target).parents().hasClass("mCompleteElement") ) {
			selected = $(".mCompleteContainer .mCompleteElement").index( $(ev.target).parents(".mCompleteElement")[0] );
			highlight (selected);
		}
		
		ev.stopPropagation();
	});
	
	$input.change(function( ) {
		$inputId.val("");
	});
	
	$input.blur(function () {
		setTimeout(function() {
			if ( $inputId.val() == "" ) {
				$input.val("");
			}
			//$container.hide();
			$loading.hide();
		}, 250);
	});
	
	$close.click( function() {
		$container.hide();
			$close.hide();
	});

	$input.click(function() {
		$(".mCompleteElement").removeClass("mCompleteElement-active");
		preselect();

		var pos = $input.offset();

		//self.$loading.show();
		//self.$container.show();
	//	self.$close.show();

		
		self.$loading[0].style.position="fixed";
		self.$loading[0].style.top=  (pos.top+3) + "px";
		self.$loading[0].style.left=  (pos.left + self.$input.width() - 15) + "px";
		
		self.$container[0].style.position="fixed";
		self.$container[0].style.top=  ( pos.top + self.$input.height() +10)  + "px";
		self.$container[0].style.left=  ( pos.left + 2 ) + "px";
		

		self.$close[0].style.position="fixed";
		self.$close[0].style.top=  (pos.top+1) + "px";
		self.$close[0].style.left=  (pos.left + self.$input.width() - 19) + "px";
		self.$close[0].style.height = (self.$input.height() +6) +"px";
		
		self.$loading.hide();
		self.$container.hide();
		self.$close.hide();


		
		if ( $input.val() != "") {
			$close.show();
		    $container.show();
		}
	});
	
	$input.keyup(function (ev)  {
		if (ev.keyCode == 38) {
			window.clearInterval(scrollInterval); // stop timer
		} else if (ev.keyCode == 40) {
			window.clearInterval(scrollInterval); // stop timer
		} else if (ev.keyCode == 13) {
			activate ( $(".mCompleteContainer .mCompleteElement").eq(selected) );
		
			if ( this.onselect ) this.onselect()
			$container.hide();
			$close.hide();

			ev.stopPropagation();

		} else {
			self.filter();
		}

	});
	
	$input.keydown(function(ev) {
		window.clearInterval(scrollInterval);

		if (ev.keyCode == 38) { //up
			scrollInterval = window.setInterval(function() {			
				if (selected != 0) selected = selected - 1;

				highlight(selected, -30);
				
			}, 300);

			if (selected != 0) selected = selected - 1;
			
			highlight(selected, -30);
			
			$container.show();
			$close.show();

		} else if (ev.keyCode == 40) { // down
			scrollInterval = window.setInterval(function() {			

			if (selected != $(".mCompleteContainer .mCompleteElement").length -1) selected = selected + 1;
				highlight(selected, -30);
			}, 300);
			
			if (selected != $(".mCompleteContainer .mCompleteElement").length -1) selected = selected + 1;
			highlight(selected, -30);
			
			$close.show();
			$container.show();	
		}
		ev.stopPropagation();
	});
		
	this.doFilter = function(sData) {
		var searchTerms = $input.val().toLowerCase().split(" ");
		var rhtml = [];
		var foundHit = false;
		$container.html("");

		if (sData && sData.data && sData.data.length) {
			for (row in sData.data) {
				var hit = false;
				if ($input.val() == "")	hit = true;
				else hit = this.filterData(searchTerms, sData.data[row]);

				if ( hit ) $container[0].appendChild(this.ondata(sData.data[row]));
				foundHit = foundHit | hit;
			}
		}

		if ( !foundHit) {
			$container.html(nohit);
		}
		
		$("div.mCompleteContainer div.mCompleteElement").click(function() {
			$container.hide();
			$close.hide();
			activate ( $(this));
			// FIXME: nur auf container anwenden, nur bestimmte prefixe akzeptieren
			
			data = $(this).data("json");
			
			for ( key in data) {
                            if (key == "id" || key=="name") continue;
                            $("#" + updatePrefix + key).val(data[key]);
			}
			//console.log($(this).data("json"));
			
		});
		selected = 0;

		$loading.hide();	
		$container.show();
		$close.show();

		$("div.mCompleteElement").eq(0).addClass("mCompleteElement-hover");
		
	};
	
	this.getData = function(c) {
		clearInterval(interval);
		if (c == "" || c === undefined) c = "_";
		
		if (self.cache[c] === undefined && ! this.locked) {
			
			this.locked=true;

			$.post(dataUrl, {
				q: c,
				flat: 0
			}, function(data) {
			    //console.log(data);
				
					self.cache[ c  ] = data;
					self.locked = false;

					self.doFilter(self.cache[ c  ]);
				
			}, "json");
		} else {
			setTimeout(function() {
				self.doFilter(self.cache[c ]);
			}, 50);
		}
		
	};
	
	/**
	 * filtert die ergebnisliste wird nach jeder eingabe aufgerufen
	 */
	this.filter = function() {
	    // lade nachricht anzeigen
	    // ergebnis liste verstelcken
	    $loading.show();
	    $container.hide();
		$close.hide();
	    $container.html("");

	    // intervall zuruecksetzen
	    clearInterval(interval);

	    // anfangsbuchstaben finden
	    var c = ($input.val().toLowerCase()).substr(0,1);

	    // neuen intervall setzen
	    interval = setInterval(function() {
	    	var sData = self.getData(c);
	    	clearInterval(interval);
	    }, 500);
		
		
		
	};
};
