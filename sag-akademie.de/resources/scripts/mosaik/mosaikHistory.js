/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

function MosaikHistory() {
	// history management
	this.history = [];
	this.forward = [];
	this.currentUri="";

	this.popPreviousUrl = function (noForward, count) {
		if ( noForward != true) {
			this.forward.push(this.history.pop()); // add the current to the forward queue
		}
		var backUrl = "";

		if (count != undefined) {
			var ixt=0;
			for (ixt=0; ixt<count; ixt++) {
				backUrl = this.history.pop();
			}
		} else {
			backUrl = this.history.pop();
		}

		return backUrl;
	}

	this.popNextUrl = function () {
		var forwUrl = this.forward.pop();
		return forwUrl;
	};

	this.canGoBackward = function () {
		return this.history.length > 1;
	};

	this.canGoForward = function() {
		return this.forward.length != 0;
	};

	this.setCurrent = function (url) {
		console.log("setting current url:" + url);
		if ( this.currentUri !== "" && this.currentUri != url) {
			console.log("currentUrl:" + this.currentUri);
			console.log("newUrl:" + url);
			this.add(url);
		}
		this.currentUri = url;
	}

	this.add = function (url) {
		this.history.push(url);
	};

	this.replaceCurrent = function (url) {
		this.currentUri = url;
		this.lastHash = "#" + url;
	};

	this.ignoreCurrent = function () {
		this.currentUri="";
	};
};

$.extend({
	mosaikHistory: new MosaikHistory()
});
