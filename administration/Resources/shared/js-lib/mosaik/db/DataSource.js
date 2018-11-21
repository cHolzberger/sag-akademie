/**
 * @file
 * All sorts of Databindings and Mappings
 * MosaikDataSource used to set get and query values from StoreDrivers
 */
;; 
/**
 * MosaikDataSource class
 * Manage Datasources
 * Maps a RemoteObject or LocalObject to a Form
 * 
 * @ctor
 * Constructor
 * constructs a mosaik Datasource, uses drv to access datasources specify storage namespace by passing namespace
 * per default <b>global<b/> is used
 * @tparam Driver drv
 * @tparam String namespace
 */
dojo.provide ("mosaik.db.DataSource");
dojo.require ("mosaik.db.DataDriven");


//FIXME
dojo.declare("mosaik.db.DataSource", null,{
	constructor: function  (drv, namespace) { 
		this.driver = drv;
			
		if (typeof(namespace) !== "undefined") {
			this.namespace = namespace;
		} else {
			this.namespace = "global";
		}
	},
	
	get: function (key, def) {
		return this.driver.get(this.namespace, key, def);
	},
	
	/**
	 * set value of an dom element! NOT JQUERY ELEMENT
	 *
	 * \tparam DOMElement elem DomElement to set the value to
	 * \tparam Mixed value the value to set
	 */
	setTargetValue: function (elem, value) {
		if (typeof(elem) === "undefined") {
			return;
		}

		switch (elem.type) {
		case "checkbox":
			elem.checked = value;
			break;
		default:
			$(elem).val(value);
			break;
		}
	},
	
	/**
	 * get value of an dom element! NOT JQUERY ELEMENT
	 * 
	 * \tparam DOMElement elem The Dom element to get the value from
	 * \tparam Mixed def The default value to return
	 */
	getTargetValue: function (elem, def) {
		if (typeof(elem) === "undefined") return def;

		switch (elem.type) {
			case "checkbox":
				return elem.checked;
			default:
				return elem.value;
		}
		return def;
	},
	
	/**
	 * binds all known keys to a form
	 * \tparam Array watchIds watch these ids for changes
	 * \tparam Function changeCb call this function on change
	 */
	bindToForm: function (watchIds, changeCb) {
		var keys = this.driver.getKnownKeys(this.namespace);

		// primary bidings this goes to inputs
		if (isArray(keys)) {
			for (var i = 0; i < keys.length; i++) {
				console.log("Setting input (#" + keys[i] + ") to value " + this.namespace + ":" + keys[i]);

				var elem = this.getElementForKey(keys[i]);
				elem.addClass("ns-" + this.namespace);
				this.setTargetValue(elem[0], this.driver.get(this.namespace, keys[i]));
			}
		}

		if (isArray(watchIds)) {
			for (var i = 0; i < watchIds.length; i++) {
				this.watchChange(watchIds[i], this.valueChanged, "change");
				$("#" + this.namespace + "\\:" + watchIds[i]).addClass("ns-" + this.namespace);

				if (isFunction(changeCb)) {
					this.watchChange(watchIds[i], changeCb);
				}

			}
		}
	},
	
	/**
	 * handle changes to data and dispatch the afterChange event to subscribed handlers
	 */
	valueChanged: function (event) {
		var key = event.data.key;
		var namespace = event.data.namespace;
		var ds = event.data.ds; // this is the element in this scope, an dit sucks
		console.log("Input change: " + namespace + ":" + key + " Value: " + ds.getTargetValue(event.target, null));

		event.data.ds.set(key, ds.getTargetValue(event.target, null));

		$(event.target).trigger("afterChange", event);
	},
	
	/**
	 * Only elements with a coresponding input element can be watches
	 * this method checks if this element exists
	 * 
	 */
	canWatch: function (key) {
		return this.getElementForKey(key).length > 0;
	},
	
	/**
	 * watch a key for change
	 */
	watchChange: function (key, cb, evName) {
		if (typeof(evName) === "undefined") evName = "afterChange";

		if (!this.canWatch(key)) return;

		var elem = this.getElementForKey(key);
		console.log("Watching input (#" + this.namespace + ":" + key + ")");

		elem.bind(evName, {
			namespace: this.namespace,
			key: key,
			ds: this
		}, cb);
	},
	
	/**
	 * stops watching a key for change
	 */
	unwatchChange: function (key, cb, evName) {
		if (typeof(evName) === "undefined") evName = "afterChange";

		var elem = this.getElementForKey(key);
		elem.unbind(evName, cb);
	},

	driverUpdateDone: function () {
		this.dispatch("updateDone");
	}
});


