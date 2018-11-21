dojo.provide("module.shell.Shell");

dojo.require("mosaik.core.Module");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.form.ComboButton");

dojo.declare ("module.listenVerwaltung.ListenVerwaltung", [mosaik.core.Module], {
	
	run: function( options ) {
		this._options = options;
		console.debug("Shell::run >>>");
		console.dir(this.options);
		console.debug ("<<< Shell::run");
        this.flexTable.hide();
	}
});

