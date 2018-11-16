/**
 *   main ajax compoent
 */
var FABridge=null;

function MosaikRuntime ( ) {
	var self = this;
	this.url = "";
	this.path = "";
	this.loading = 0;
	this.replaceReg =  {};
	this.lastHash = window.location.hash;
	this.hashInterval = null;
	this.titleTemplate = document.title;
	this.flexSave = null;

	
	this.checkForm = true;
	// form handling
	this.nextUrl = null;

	this.replace = function ( needle, value) {
		console.log("Adding Variable:" + needle + " Value: " + value);
		this.replaceReg[needle] = value.toString();
	};

	this.replaceVariables = function (url) {
		for ( needle in this.replaceReg ) {
			console.log("replace: " + needle + " width " + this.replaceReg[needle]);
			url = url.replace ( needle,  encodeURIComponent(this.replaceReg[needle]));
		}
		return url;
	}

	this.replaceReset = function () {
		this.replaceReg = {};
	};

	this.reload = function ( ) {
		this.load(self.url, true);
	};

	this.importJS = function(script) {
		$.getScript('/resources/scripts'+script); //scriptpath
	};

	/* set hash and do not save it to history */
	this.tmpHash = function (name) {
		$.mosaikHistory.add( window.location.hash.replace("#","") );

		this.lastHash = "#" + window.location.hash.replace("#","") + "/" +name;
		window.location.hash = window.location.hash.replace("#","") + "/" + name;
	};

	

	// misc load etc.
	this.applyMimeTypeModifyer =function (url, mtm ) {
		if ( url.toString().search (mtm + ";") != -1) return url;
		var queryPos = url.toString().search("\\?");

		if ( queryPos == -1 ) {
			var hashPos = url.toString().search("\\#");
			if (hashPos == -1) {
				url = url + ";" + mtm;
			} else {
				var newurl = url.substr(0, hashPos);
				newurl = newurl + ";" + mtm;
				newurl = newurl + url.substr(hashPos);
				url=newurl;
			}
		} else {
			var newurl = url.substr(0, queryPos);
			newurl = newurl;
			newurl = newurl + ";" + mtm;
			newurl = newurl + url.substr(queryPos);
			url=newurl;
		}

		return url.replace("&amp;","&");
	}

	this.aBeforeSend = function () {
		$("#imain").hide();
		$("#loadscreen").show();
	}

	this.aComplete = function () {
		self.updateHistory();
		q = getLoadCompleteQueue();
		$(window).queue("complete", q);
		$(window).dequeue("complete");
	}

	this.aError = function (request, statusText, error) {
		$("#imain").html("");
		
		var error = "<b>Status:</b> " + statusText + "<br/>";
		error += request.toString();
		self.loading = 0;
		this.flexSave = null;
		$("#imain").html(error.toString());
	}

	this.aSuccess =function (html) {
		$("#imain").html("");
		this.flexSave = null;

		self.loading = 0;
		
		if ( html ) {
			//console.log(html);
			$("#imain").html("<span>" + html.toString() + "</span>");
		}
		//$("#imain").html(html);
		try {
			document.title = self.titleTemplate.replace("-", " - " + $("#imain h1:first").text() + "                                                         ");
			this.hashInterval = setInterval( "$.mosaikRuntime.checkHash();", 750);
		} catch ( e ) {
			console.log("mosaikRuntime.aSuccess:" + e);
		}
		$().trigger("loadComplete");

	}

	this.setChanged = function() {
		window.changed=true;
	}

	this.submitForm = function (url) {
		$(window).trigger("beforeSubmit", {url: url});
		window.changed=false;
		if ( url != undefined ) {
			this.nextUrl = url;
		}

		if ( this.flexSave != null 
			&& FABridge
			&& FABridge[this.flexSave]
			&& $("#flexApp").length > 0 ) {
			FABridge[this.flexSave].root().save();
			// give flex some time
			if ( url != undefined ) {
				window.setTimeout("$.mosaikRuntime.load('"+url+"', true);", 1000);
			}
			
		} else {
			$("#imain").find('form').submit();
		}

		return null;
	};

	this.resetForm = function () {
		window.changed=false;
		$('form').resetForm();

	};

	this.load = function (url, reload, data) {
		//console.log( "Loading: " + url);
		var self = this;
		if ( url.length == 0 ) return false;

		// replace all from replace reg 
		// at the beginning
		
		url = this.replaceVariables(url);
		var originalUrl = url;
	
		if ( $("form").length != 0
			&& url.substr(0,9) != "download:"
			&& this.checkForm
			&& url.substr(0,11) != "javascript:"
			&& reload != true
			&& window.changed ) {
			console.log("Form redirect");
			$("#flexApp").css("height", "1px");
			$(".dialog_closesite").data ("onSave", function () {
				self.submitForm(url);
			});

			$(".dialog_closesite").data ("onVerwerfen", function () {
				window.changed = false;
				self.load (url, true, data);
			});

			$(".dialog_closesite").dialog("open");
			this.replaceReset();
			return false;
		}

		this.nextUrl = null;

		if ((self.url == url && reload !== true) || url.toString[url.toString().length-1] == "#" || url.toString() == "") {
			return;
		}

		/*if ( url != "")  {
			$.mosaikHistory.add(url);
		}*/

		

		if (url.substr(0, 7) == "mailto:") {
			window.location.href = url;
			return false;
		} else if ( url.substr(0,9) == "download:") {
			url = url.replace("download:", "");
			window.location = url;

			$.mosaikHistory.ignoreCurrent(); // do not save downloads in the queue
			return false;
		} else if (url.substr(0, 5) == "http:" && url.substr(7, window.location.hostname.length) != window.location.hostname) {
			window.open(url);
			return false;
		} else if (url.substr(0, 11) == "javascript:") {
			eval ( url.substr(11, url.length) );
			return false;
		}
		$.mosaikHistory.setCurrent(url);
		// fixme: need to make it event based
		$(".dbContainer").each(function() {
			$(this).data("mTable").saveState();
		});

		$("div.removeOnLoad").remove();

		$.xSettings.sendAll();

		var path = url.replace ( "http://" + window.location.hostname, "");
		path = path.replace(/#.*/g,"");
		origUrl = url;
		if ( path != "" ) {
			window.location.hash = path;
		}
		this.lastHash = window.location.hash;
		this.loading = 1;

		url = this.applyMimeTypeModifyer(url, "iframe");


		self.url = origUrl;
		self.path = path;
		$("#imain").html("<div>Lade Seite...</div>");

		$.ajax ({
			url: url,
			async: true,
			cache: false,
			global: false,
			data: data,

			beforeSend:	this.aBeforeSend,
			complete: this.aComplete,
			success: this.aSuccess,
			error: this.aError
		})
		this.replaceReset();
		return false;
	};

	this.checkHash = function () {
		if ( window.location.hash.toString() != this.lastHash.toString() && window.location.hash.toString() != "" ) {
			
			$("#ribbon").find(".ui-state-active").removeClass("ui-state-active");
			clearInterval(this.hashInterval);
			this.load( window.location.hash.replace("#","") );

			console.log("Hash Changed ... reloading");
		}

		//console.log("hash check done");
	}

	this.requireLogin = function () {
		window.location.href="/resources/logon";
	}

	this.keepAlive = function () {
		$.ajax ({
			url: "/admin/_keepalive",
			async: true,
			cache: false,
			global: false,
			success: function ( html ) {
				var today  = new Date();

				$("#lastUpdate").html( today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds() );
			//if (console) console.log(html);
			}
		})
	}

	this.print = function () {
		window.print();
	}

	this.extPrint = function () {
		var printurl = $.mosaikRuntime.path;
		if ( printurl.search(/edit/) != -1) {
			printurl = printurl.replace("?edit", ";iframe?print");
		}else if ( printurl.search(/personen/)!= -1) {
			printurl = printurl.replace("?person", ";iframe?print");
		} else if ( printurl.search(/person/)!= -1) {
			printurl = printurl.replace("?person", ";iframe?print");	
		} else {
			printurl = printurl + ";iframe?print";
		}

		

		//alert(printurl);
		//$("#_iframe").attr("src", printurl);
		window.open(printurl);
	}

	this.updateHistory = function() {
		$.mosaikHistory.canGoBackward() ? $("#button_back").show() : $("#button_back").hide();
		$.mosaikHistory.canGoForward() ? $("#button_forward").show() : $("#button_forward").hide();
	}

	this.goBack = function (noForward, count) {
		this.load($.mosaikHistory.popPreviousUrl(noForward, count));
	}
	this.goForward = function ( ) {
		this.load($.mosaikHistory.popNextUrl());
	}

	// fenster oeffnen mit default optionen
	this.openWindow = function (url, name) {
		if ( name === undefined ) {
			name ="_blank";
		}
		var width = 400;
		var height= 450;
		var _w= window.open (url,name,'status=0,toolbar=0,location=0,menubar=0,directories=0,width='+width.toString()+',height='+height.toString());
		_w.moveTo(window.screenX + window.outerWidth/2 - width/2, window.screenY + window.outerHeight/2 - height/2);
		_w.focus();
		return _w;
	}

	this.hashInterval = setInterval( "$.mosaikRuntime.checkHash();", 500);
	this.keepAliveIntervall = setInterval( "$.mosaikRuntime.keepAlive();",10 * 60 * 1000); // keep alive every 10 minutes
};

$.extend({
	mosaikRuntime: new MosaikRuntime()
});





function MosaikCache () {
	var cache = {};

	this.cache = function (url) {
		
	}

	this.set = function (key, value) {
		cache[key] = value;
	}

	this.get = function (key) {
		if (cache[key])
			return cache[key];
		return null;
	}
}



