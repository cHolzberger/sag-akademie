function deleteAllCookies() {
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }
}

window.addEventListener("load", function() {
	window.cookieconsent.initialise({
		"palette": {
			"popup": {
				"background": "#237afc"
			},
			"button": {
				"background": "#fff",
				"text": "#237afc"
			}
		},
		"theme": "classic",
		"type": "opt-out",
		"content": {
			"message": "Wenn Sie diese Webseite weiter nutzen möchten, stimmen Sie bitte der Verwendung der Cookies zu!",
			"dismiss": "Zustimmen",
			"deny": "Ablehnen",
			"link": "weitere Informationen",
			"href": "/cookies"
		},
		onInitialise: function (status) {
			var type = this.options.type;
			var didConsent = this.hasConsented();
			
			if (type == 'opt-out' && !didConsent) {
				deleteAllCookies()
				alert("Um die Webseite der SAG-Akademie zu benutzen müssen Sie der Verwendung von Cookies zustimmen.\nBitte schließen Sie Ihren Browser.");
			}
		},
 
		onStatusChange: function(status, chosenBefore) {
			var type = this.options.type;
			var didConsent = this.hasConsented();
			if (type == 'opt-out' && !didConsent) {
				deleteAllCookies()

				alert("Um die Webseite der SAG-Akademie zu benutzen müssen Sie der Verwendung von Cookies zustimmen.\nBitte schließen Sie Ihren Browser.");
			}
		},
 
		onRevokeChoice: function() {
			var type = this.options.type;
	
			if (type == 'opt-out') {
				deleteAllCookies()

				alert("Um die Webseite der SAG-Akademie zu benutzen müssen Sie der Verwendung von Cookies zustimmen.\nBitte schließen Sie Ihren Browser.");
			}
		}
})});