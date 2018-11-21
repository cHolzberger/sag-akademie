dojo.provide("module.terminReferentenEditor.TerminReferentenEditor");
dojo.require("mosaik.core.Module");
dojo.require("mosaik.ui.FlexTerminReferentenEditor");

dojo.declare("module.terminReferentenEditor.TerminReferentenEditor", [mosaik.core.Module], {
	_options: null,
	constructor: function ( sandbox ) {
		console.log ("setting sandbox");
		this.sandbox = sandbox;
        this.referentenEditorContainer=null;
	},
	run: function (options) {
		this._options = options;
		console.dir(options);
		this.container = dijit.byId("borderContainer");
		this.referentenEditorContainer = dijit.byId("referentenEditorContainer");
		// workaround 
		this.referentenEditor = dijit.byId("referentenEditor");
		// flex table init
		this.referentenEditor.queryService(options);
        this.flexTable.hide();
	},

	save: function() {
		this.referentenEditor.save();
	}
});