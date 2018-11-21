/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */
dojo.provide("appcore.ModelSynchronizer");

dojo.declare ("appcore.ModelSynchronizer",null, {
	localVersion: 0,
	remoteVersion: 1,
	model: null, // the active record table
	prefix: "", // table name
	offset: -1,  // -1 means init
	perRun: 10,  // sync this many datasetz per run
	stepTime: 10, // wait X ms
	data: null, // data to be synced in steps
	deferred: null,

	constructor: function (options) {
		dojo.mixin ( this, options);
	},

	needSync: function () {
		if ( this.remoteVersion != this.localVersion) {
			return true;
		} else {
			return false;
		}
	},

	setData: function (data) {
		this.data = data;
	},

// starts the synchronisation
// returns a dojo defered to react on error or done events
	syncData: function () {
		if ( ! this.deferred) {
			this.deferred = new dojo.Deferred();
		}

		console.log ( "DB => Sync " + this.prefix + " " +this.offset +"/"+ this.data.length );
		var targetOffset = 0;

		if ( this.offset + this.perRun < this.data.length ) {
			targetOffset = this.offset + this.perRun;
		} else {
			targetOffset = this.data.length;
		}
		
		// start to sync
		if ( this.offset == -1 ) {
			console.log ("Sync Offset -1: Start to sync");
			this.offset = 0;
			try {
				this.model.destroyAll();
			} catch ( e ) {
				console.error("can't destroy model ?!");
			}
		} else { // sync data
			for ( var i = this.offset ; i < targetOffset;  i++ ) {
				this.model.create(this.data[i]);
			}

			this.offset = targetOffset;
		}

		if ( this.offset < this.data.length) {
			window.setTimeout ( dojo.hitch ( this, this.syncData), this.stepTime);
		} else {
			console.log ("DB => Sync " + this.prefix + " done");
			this.deferred.callback();
		}

		return this.deferred;
	}
});

