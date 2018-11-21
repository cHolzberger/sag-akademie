/**
 * @file
 * keeps two stores synchronous (one way) the connector listens to updates of the sourceStore (usually remote)
 * and updates the localStore with the values recieved (the local store is NOT reset while doing so)
 **/
dojo.require("mosaik.core.EventDispatcher");
dojo.provide("mosaik.db.XhrStoreUpdater");

dojo.declare ("mosaik.db.XhrStoreUpdater", [mosaik.core.EventDispatcher], {
	constructor: function (targetDriver) {		
		this.targetDriver = targetDriver;
		this.namespace = "";
	},
	
	// can be moved to .prototype
	_sendRequest: function (service, vars) {
		var query = {
			url: service,
			handleAs: "json",
			preventCache: true,
			content: vars,
			load: dojo.hitch ( this, "onUpdate"),
			error: dojo.hitch (this, "onError")
		}
		
		var options = $.extend ( {
   			format: this.format
		}, vars);
		dojo.xhrPost ( query );
		this.authenticationStateChange ("waiting");

	},
	
	/**
	* one of the sourceStores has update its data
	 */
	onUpdate: function ( store ) {
		console.info("===========> [ObjectStoreConnector] update recivied");
	
		//sync the stores					
		this.targetDriver.updateStart();
			
		for ( i=0; i< keys.length; i++ ) {
			this.targetDriver.set ( this.namespace, keys[i] , this.sourceDriver.get(this.namespace, keys[i]));
		}
			
		this.targetDriver.updateDone();
	},
	
	onError: function () {
		
	}
});
