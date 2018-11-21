// Summary:
// bootstrap the module
// create a fresh sandbox
// and create and run the module 

dojo.provide("mosaik.core.moduleBootstrap");

console.log("STEP1.0: CREATING SANDBOX");

if ( typeof ( moduleName ) === "undefined") {
	moduleName =  parentSandboxBridge.module_name;
	moduleClass =  parentSandboxBridge.module_class;
}

// create the modules sandbox
sandbox = new mosaik.core.Sandbox({
	app: app,
	ds: ds,
	moduleName: moduleName,
	moduleClass: moduleClass
});
	
moduleInstance = sandbox.loadModule(  );
dojo.parser.parse();

console.log("waiting for the dom");
dojo.connect ( moduleInstance, "onRunFinish" , this, function() {
	// Summary:
	// hides the loading scre	en - the bootstrap has finished
	sandbox.hideLoadingScreen();
});

//	console.log("dom loaded");
sandbox.runModule( window.options );

if ( typeof ( window.onReady ) !=="undefined") {
	window.onReady();
}
app.moduleReady();









