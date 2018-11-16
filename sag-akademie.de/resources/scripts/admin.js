;;
var CKEDITOR_BASEPATH = "/resources/scripts/ckeditor/";

function setLoadscreenMessage(msg) { 
	$("#loadscreenMessage").html(msg);
}

function removeDynamicRibbons() {
	$(".ui-ribbon .removeOnReload").remove();
};

function removeDynamicRibbonsRight() {
	$(".ui-ribbon .removeOnReloadRight").remove();
};


/* dialogs  
 * 
 * optimized througth script!
 */
function dialog_init() {
	$(".dialog_confirmation").data("url","");
	
	$(".dialog_confirmation").dialog({
		bgiframe: true,
		resizable: false,
		height:140,
		modal: true,
		autoOpen: false,
		overlay: {
			backgroundColor: '#000000',
			opacity: 0.1
		},
		buttons: {
			"Ok": function() {
				if ($(this).data("url") !== undefined) {
					$.mosaikRuntime.load($(this).data("url"));
				}
				$(this).dialog('close');
			},
			"Abbrechen": function() {
				$(this).dialog('close');
			}
		}
	});

	$(".dialog_closesite").dialog({
		bgiframe: true,
		resizable: false,
		height:140,
		modal: true,
		autoOpen: false,
		overlay: {
			backgroundColor: '#000000',
			opacity: 0.1
		},
		buttons: {
			"Speichern": function() {
				$(this).data("onSave")();
				$(this).dialog('close');
			},
			"Verwerfen": function() {
   				$(this).data("onVerwerfen")();
				$(this).dialog('close');
			},
			"Abbrechen": function() {
				$("#flexApp").css("height","95%");
    				$(this).dialog('close');
				return;
			}
		}
	});

	$(".dialog_notification").dialog({
		bgiframe: true,
		resizable: false,
		height: 140,
		modal: true,
		autoOpen: false,
		overlay: {
			backgroundColor: '#000000',
			opacity: 0.1
		},
		buttons: {
			"Ok": function() {
				$this = $(this);
				if ($this.data("url") !== undefined ) {
					$.mosaikRuntime.load($this.data("url"));
				} else if ( $this.data("okCallback") !== undefined && $this.data("okCallback") !== null) {
					$this.data("okCallback")();
					$this.data("okCallback", null);
				}
				$this.dialog('close');
			}
		}
	});
};


function showDataSaved(cb) {
	$("#saved").data("okCallback",cb);
	$("#saved").dialog("open");
}

function dialog_confirmation (d, url) {
	d.data("url", url);
	d.dialog("open");
};

function init_class_links() {
	$(".wymeditor").wymeditor({
		logoHtml: '',
		  toolsItems: [
    {'name': 'Bold', 'title': 'Strong', 'css': 'wym_tools_strong'},
    {'name': 'Italic', 'title': 'Emphasis', 'css': 'wym_tools_emphasis'},
    {'name': 'InsertOrderedList', 'title': 'Ordered_List', 'css': 'wym_tools_ordered_list'},
    {'name': 'InsertUnorderedList', 'title': 'Unordered_List', 'css': 'wym_tools_unordered_list'},
    {'name': 'Undo', 'title': 'Undo', 'css': 'wym_tools_undo'},
    {'name': 'Redo', 'title': 'Redo', 'css': 'wym_tools_redo'},
    {'name': 'CreateLink', 'title': 'Link', 'css': 'wym_tools_link'},
    {'name': 'Unlink', 'title': 'Unlink', 'css': 'wym_tools_unlink'},
  ],
  containersHtml: '',
  classesHtml: '',
  skin: "compact",
  updateSelector: ".ui-ribbon-button",
  updateEvent: "mouseenter",
  postInit: function (wym) {
	  wym.hovertools();
	  wym.fullscreen();
  }


	});

	/* datepicker */
	$("div.datepicker input").datepicker({
		showOn: 'button',
	 	buttonImage: '/css/theme/icons/calendar.png',
	  	buttonImageOnly: true,
	  	dateFormat: 'dd.mm.yy'
	});
	
	$("input.datepicker").datepicker({
		showOn: 'button',
	 	buttonImage: '/css/theme/icons/calendar.png',
	  	buttonImageOnly: true,
	  	dateFormat: 'dd.mm.yy'
	});
	
	$("input").tooltip({
		track: true,
		delay: 250,
		showURL: false,
		showBody: " - ",
		fade: 250
	});
}

function colsizeUpdate() {
		$(".dbtable").mTableColsizeable({
			dragMove : false, 
			dragProxy : "area"
		});
} 

function validator_init() {
	if ($("form.validate").length > 0) {
		$("form.validate").validate({
			 errorLabelContainer: "#validatorErrorMessages ul",
			 wrapper: "li",
			 errorContainer: "#validatorError",
			 errorPlacement: function () {
			 	//noop
			 }
		});
	}
};

function table_init_colmgr () {
	if ($(".dbContainer").length > 0) {
		$(".dbContainer").mTableColumnManager({
			listTargetID: ".dbColumnManager",
			onClass: "dbColumnOn",
			offClass: "dbColumnOff",
			saveState: true
		});
	}
};

function table_init_colsize() {
	if ($(".dbContainer").length > 0) {
		$(".dbContainer").mTableColsizeable({
			dragMove: false,
			dragProxy: "area"
		});
	}
};

function table_init_scroll() {
	if ($(".dbContainer").length > 0) {
		$(".dbContainer").mTableScroller();
	}
};

function table_init_bindEvents() {
	$(".dbContainer").each(function ( ) {
		$(this).data("mTable").loadState();
	});
};

function table_init() {
	$("#imain").show();

	if ($(".dbContainer").length > 0) {
		$(".dbContainer").mTable();
	}
};

function ribbon_init() {
	$("#ribbon").bind("tabsshow", function () {
		$("#imain").css("top", $("#navigation").height() + "px");

		$(".ui-ribbon").bind("ribbonReady", function () {
			$("#imain").css("top", $("#navigation").height() + "px");
		})
	});
	
	update_ribbon();
};

function updateLink( self ) {
	if ( self.href[self.href.length-1] == "#" ) return;
	if ( self.target == "_blank") return;
		
	if (self.href.search("javascript:") != -1) {
		self.setAttribute("onclick", self.href.replace("javascript:",""));
	} else {
		self.setAttribute("onclick", "$.mosaikRuntime.load('" + self.href + "'); return false;");
	}
	self.href="#";
};

function updateLinks(targetId) {
	$(targetId).find("a").each(function () {
		var self=this;
		setTimeout( function () {
			updateLink (self); 
		}, 5);
	});
};

function update_imain_links() {
	updateLinks("#imain");
};

function markFormsDirty() {
	if ( window.changed ) return;
	document.title = " [*] " + document.title;
	//if (console) console.log("Dirty forms");
	window.changed = true;
}

function update_site() {

     // bind 'myForm' and provide a simple callback function
	 window.noReload = false;

	 var $forms = $("form:not(.ignoreChange)");

	window.changed = false;
	 
	 if ($forms.length > 0) {
		$forms.change (markFormsDirty);
		$forms.keypress (markFormsDirty);
		$("form input").keypress( markFormsDirty );
		$("form textarea").keypress( markFormsDirty );
		$.mosaikRuntime.checkForm = true;
	 }

	 // mark frames dirty
	 var i;
	 for ( i=frames.length; i; i-- ) {
		frames[i-1].onkeydown = markFormsDirty;
	 }


	 $forms = $("form");

	 if ( $forms.length > 0 ) {
	     $forms.each(function() {
	 		this.action = $.mosaikRuntime.applyMimeTypeModifyer(this.action, "iframe");
	 	});

	 	$forms.ajaxForm({
	 		success: function(responseText, statusText) {
				$("#imain").html("");
	 			//$("#ajaxStatus").html(statusText);
	 			$("#savescreen").hide();
				
	 			if ( window.noReload  ) {
					if ( $.mosaikRuntime.nextUrl) {
						$.mosaikRuntime.load($.mosaikRuntime.nextUrl);
					} else {
						if ( window.ignoreChange ) {
							$.mosaikRuntime.tmpHash( "erweiterteSuche" );
							window.ingoreChange = false;
						}
						$.mosaikRuntime.aSuccess(responseText);
						$.mosaikRuntime.aComplete();
					}
	 			} else {
					$.mosaikRuntime.reload();
	 			}
	 		},
	 		
	 		beforeSubmit: function(data, form) {
				$.mosaikRuntime.checkForm = false;
				
				window.noReload = form.hasClass("no-reload") ;
				window.ignoreChange = form.hasClass("ignoreChange") ;


	 			if ( form.valid() ) {
					$("#savescreen").show();
					return true;
				} else {
					return false;
				}
	 		}
	 		
	 	});
	 }
	 
	 $("#breadcrumbs").html( $("#newBreadcrumbs").html() );
	 
	 updateLinks("#breadcrumbs");
	// load flash right click
	//RightClick.init();
};

function update_ribbon() {
	updateLinks("#ribbon .ui-ribbon-tab");
	ribbonUpdate(); // from jquery.ribbon.j 
};

function qAdapter( fn, msg ) {
	return function( ) { 
		window.setTimeout (function () {
			if (msg) setLoadscreenMessage(msg); 
			fn(); 
			$(window).dequeue("complete")
		}, 2); 
	};
}

function getLoadCompleteQueue() {
	q = []; 
	q.push(qAdapter(update_ribbon, "Lade dynamische Buttons..."));
	q.push(qAdapter(dialog_init, "Lade Dialoge..."));
	q.push(qAdapter(update_imain_links, "Aktualisiere Links..."));	
	q.push(qAdapter(init_class_links,"Aktualisiere Links..."));
	/*
	if ($(".dbtable").length > 0) {
		q.push(qAdapter(table_init, "Lade Tabelle..."));
		q.push(qAdapter(table_init_colmgr, "Blende Spalten aus..."));
		q.push(qAdapter(table_init_colsize, "Stelle Spaltenbreiten ein..."));
		q.push(qAdapter(table_init_scroll, "Aktualisiere Scrollbars..."));
		q.push(qAdapter(table_init_bindEvents, "Aktualisiere Tabelle..."));
	}
	*/
  
	if ($("form.validate").length > 0) {
		q.push(qAdapter(validator_init, "Setze ben&ouml;tigte Felder..."));
	}
	
	q.push(qAdapter(update_site,"Aktualisiere die Seite..."));
	
	q.push(function() {
		$("#loadscreen").hide();
		$("#imain").show();
		$(window).dequeue("complete");
	} );
	return q;
}

function getLoadSuccessQueue() {

}

$(function () {
	ribbon_init();
 	table_init();
 	dialog_init();
	init_class_links();
	$("#loadscreen").hide();
	if (window.location.hash) {
		$.mosaikRuntime.load(window.location.hash.replace("#",""));
	} else {
		$.mosaikRuntime.load("/admin");
	}
	$.xSettings.load();
	$("#imain").rightClick(function() {});

});


function errorHandler(message, url, line) {
	console.error(url + ":" + line + "==>" + message )
    return true;
};
//ie on error fix
window.onerror = errorHandler;

