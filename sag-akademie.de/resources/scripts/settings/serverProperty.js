function XSettingsFactoryArray() {
	var self = this;
	this.registry = {};
  
	this.checkVar = function(namespace, name) {
		if (!isSet(this.registry[namespace])) {
			this.registry[namespace] = {};
			this.registry[namespace][name] = {};
			return false;
		}
		
		if (!isSet(this.registry[namespace][name])) {
			this.registry[namespace][name] = {};
			return false;
		}
		
		return true;
	};
	this.set = function(namespace, name, value) {
		var existing = this.checkVar(namespace, name);
	
		if (self.registry[namespace][name].value != value) {
			self.registry[namespace][name].value = value;
			self.registry[namespace][name].name = name;
			self.registry[namespace][name].changed = new Date();
			self.registry[namespace][name].name = name;
			self.registry[namespace][name].namespace = namespace;
		}	
	}
	/*
  get and set properties */
	this.get = function(namespace, name, defValue) {
		if (isSet(self.registry[namespace]) && isSet(self.registry[namespace][name])) {
			return self.registry[namespace][name];
		} else {
			self.checkVar(namespace, name);
			self.registry[namespace][name] = {
				value: defValue,
				name: name,
				namespace: namespace,
				changed: new Date(),
				needSave: true
			};
			if (typeof(defValue) == "object") {
				return defValue;
			} else {
				return self.registry[namespace][name];
			}
		}
	};
	this.sendAll = function() {
  
	}
	this.updateAll = function() {
  
	}
	this.start = function() {
  
	}
	this.load = function() {
	}
	
}

function XSettingsFactoryGears() {
	var self = this;
	var date;
	var table = "X_Config";
	/*window.mStore.execute('DROP TABLE IF EXISTS J_config');*/
	window.mStore.execute('create table if not exists '+table+' (namespace text, name text, value text, changed date, user_id int)');
	this.checkVar = function(namespace, name) {
		var rs = window.mStore.execute('Select Count(*) from '+table+' where namespace = ? AND name = ? AND user_id = ?', [namespace, name, window.mUserID]);

		if(rs.field(0) >= 1) {
			return true;
		} else {
			return false;
		}
	
	}
	this.set = function(namespace, name, value) {
		var date = new Date();
		if(!this.checkVar(namespace, name))
		{
			window.mStore.execute("insert into "+table+" values ('"+namespace+"', '"+name+"', '"+value+"', '"+date+"', '"+window.mUserID+"')");
		}else{
			window.mStore.execute("Update "+table+" SET value = '"+value+"', changed = '"+date+"' where  namespace = '"+namespace+"' AND name = '"+name+"' AND user_id = '"+window.mUserID+"'");
		}
	}
	this.get = function(namespace, name, defValue) {
		var rs = window.mStore.execute("select * from "+table+" where namespace = '"+namespace+"' AND name = '"+name+"' AND user_id = '"+window.mUserID+"' LIMIT 1");
		var output;
		if(rs.isValidRow()) {
			output = {
				value: rs.fieldByName('value'),
				name: rs.fieldByName('name'),
				namespace: rs.fieldByName('namespace'),
				changed: rs.fieldByName('changed')
			};
		
		} else {
			output = {
				value: defValue,
				name: name,
				namespace: namespace
			};
		
	    
		}
		return output;
	}

	this.getAll = function () {
		var rs=window.mStore.execute("select * from " + table + " where user_id="+window.mUserId);

		while (rs.isValidRow()) {
			console.log(rs.fieldByName("namespace") +":"+ rs.fieldByName("name") + " => " +  rs.fieldByName("value") + '@' + rs.fieldByName('changed'));
			rs.next();
		}
	}

	this.sendAll = function() {
  
	}
	this.updateAll = function() {
  
	}
	this.start = function() {
  
	}
	this.load = function() {
	}
	
}
/* provide a Settings factory for the whole page */


function getPageNS() {
	var id = $.mosaikRuntime.path;
	id = id.replace(/\//g, ".");
	id = "page" + id;
	return id;
}

function isSet(s) {
	if (typeof(s) == "undefined") 
		return false;
	return true;
};

function dateFromMysqlDate(sdate) {
	if (typeof(sdate) == "undefined") 
		return new Date();
	data = sdate.split(" ");
	time = data[1].split(":");
	date = data[0].split("-");
	var month = parseInt(date[1]) - 1
	return new Date(date[0], month, date[2], time[0], time[1], time[2]);
};

function dateToMysqlDate(date) {
	var month = (date.getUTCMonth() + 1).toString();
	var ddate = date.getUTCDate().toString();
	
	if (month.length == 1) 
		month = "0" + month;
	if (ddate.length == 1) 
		ddate = "0" + ddate;
	
	return date.getUTCFullYear() + "-" + month + "-" + ddate + " " + date.getUTCHours() + ":" + date.getUTCMinutes() + ":" + date.getUTCSeconds();
};

(function($) {
	if (window.google && google.gears) {
		$.extend({
			xSettings: new XSettingsFactoryGears()
		})
	}else{
		$.extend({
			xSettings: new XSettingsFactoryArray()
		})
	
	}
})(jQuery);

$(window).unload(function() {
	$.xSettings.sendAll();
})

var hashInterval;

$().ready(function() {
	$.xSettings.updateAll();
	$.xSettings.start();
});