dojo.provide("mosaik.db.DataDriven");
dojo.require("mosaik.core.EventDispatcher");
/**
 * Object DefaultDriverAdapter (Mixin)
 * Function Collection / used to access a datasourcedriver from within an object
 *
 */
dojo.declare ("mosaik.db.DataDriven", null, {

	setDriver: function (drv) {
		this.driver = drv;
	},
	
	setNamespace: function (ns) {
		this.namespace = ns;
	},
	
	set: function (key, value) {
		this.driver.set(this.namespace, key, value);
		return value;
	},

	get: function (key, def) {
		return this.driver.get(this.namespace, key, def);
	},

	unset: function (key) {
		this.driver.unset(this.namespace, key);
	},

	getElementForKey: function (key) {
		//todo add hidden element to watch events
		return $("#" + this.namespace + "\\:" + key);
	},

	toObject: function () {
		return this.driver.toObject(this.namespace);
	},

	dump: function () {
		$.log.dump(this.namespace, this.driver.toObject(this.namespace));
	}
});
