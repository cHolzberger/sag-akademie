/*
 * Copyright (c) 2008 Greg Weber greg at gregweber.info
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * documentation at http://gregweber.info/projects/uitablefilter
 *
 * changed by Christian Holzberger (ch@mosaik-softare.de) to be used with jquery mtable plugin
 *
 * allows table rows to be filtered (made invisible)
 * <code>
 * t = $('table')
 * $.uiTableFilter( t, phrase )
 * </code>
 * arguments:
 *   jQuery object containing table rows
 *   phrase to search for
 *   optional arguments:
 *     column to limit search too (the column title in the table header)
 *     ifHidden - callback to execute if one or more elements was hidden
 */
jQuery.uiTableFilter = function(jq, phrase, column, ifHidden) {
	var new_hidden = false;
	

	var phrase_length = phrase.length;
	var words = phrase.toLowerCase().split(" ");
	var search_text = null;
	
	// just hide never show!
	var success = function(elem) {
		//elem.show();
		;
	};
	
	var failure = function(elem) {
		elem.hide();
		elem.addClass("filtered");
		new_hidden = true;
	};
	
	if (column) {
		var index = column;
		var iselector = "td:visible:eq("+index+")";
		
		search_text = function() {
			var elem = jQuery(this);
			jQuery.uiTableFilter.has_words(elem.find(iselector).text(), words) ? success(elem) : failure(elem);
		}
		
	} else {
		search_text = function() {
			var elem = jQuery(this);
			jQuery.uiTableFilter.has_words(elem.text(), words) ? elem.show() : elem.hide();
		}
		
	}
	
	if (words.length > 0) {
		jq.find("tbody tr:visible").each(search_text);
	}
	
	if (ifHidden && new_hidden) ifHidden();
	return jq;
};
// not jQuery dependent
// "" [""] -> Boolean
// "" [""] Boolean -> Boolean

// filter methods
jQuery.uiTableFilter.has_words = function(str, words, caseSensitive) {
	if ( str === undefined ) return false;
	if ( words === undefined ) return false;
	var text = caseSensitive ? str : str.toString().toLowerCase();
	for (var i = 0; i < words.length; i++) {
		if (text.indexOf(words[i]) === -1) 			
			return false;
	}
	return true;
};


