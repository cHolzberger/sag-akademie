<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Gears - Test</title>
    </head>
	<body>
	<script type="text/javascript"  src="gears_init.js"></script>
<script type="text/javascript">


function XSettingsFactory() {
	var self = this;
	var success = false;
	var db;
	var date;
	  if (window.google && google.gears) {
		try {
		  db = google.gears.factory.create('beta.database');
	
		  if (db) {
			db.open('sag-akademie');
			/*db.execute('DROP TABLE IF EXISTS J_config');*/
			db.execute('create table if not exists J_Config' +
					   ' (namespace text, name text, value text, changed date, user_id int)');
			success = true;
		  }
	
		} catch (ex) {
		  alert('Konnte Datenbank nicht erstellen: ' + ex.message);
		}
	  }
  this.getUserid = function () {
  	return 1;
  }
  this.checkVar = function(namespace, name) {
    var rs = db.execute('Select Count(*) from J_Config where namespace = ? AND name = ? AND user_id = ?', [namespace, name, this.getUserid()]);

  	if(rs.field(0) >= 1) {
		return true;
	} else {
		return false;
	}
	
  }
  this.set = function(namespace, name, value) {
  	var date = new Date();
	alert(namespace +'+'+ name +'+'+ value);
	if(!this.checkVar(namespace, name))
	{
		db.execute("insert into J_Config values ('"+namespace+"', '"+name+"', '"+value+"', '"+date+"', '"+this.getUserid()+"')");
		alert("Eintrag Erstellt");
	}else{
		db.execute("Update J_Config SET value = '"+value+"', changed = '"+date+"' where  namespace = '"+namespace+"' AND name = '"+name+"' AND user_id = '"+this.getUserid()+"'");
		alert("Eintrag bereits vorhanden Update durchgefuehrt");
	}
  }
  this.get = function(namespace, name, defValue) {
	var rs = db.execute("select * from J_Config where namespace = '"+namespace+"' AND name = '"+name+"' AND user_id = '"+this.getUserid()+"' LIMIT 1");
	if(rs.isValidRow()) {
		output = {
				value: rs.fieldByName('value'),
				name: rs.fieldByName('name'),
				namespace: rs.fieldByName('namespace'),
				changed: rs.fieldByName('changed')
			};
		return output;
	} else {
		return defValue;
	}

  }
  /*
  this.getAll = function() {
  	  var rs = db.execute('select * from J_Config');
	  var index = 0;
	  var recentPhrases = {};
	  while (rs.isValidRow()) {
		recentPhrases[index] = rs.fieldByName('value');
		++index;
		rs.next();
	  }
	return recentPhrases;
  }
  */
	
}
	var x = new XSettingsFactory();
	var output = x.get('peterpahn', 'test');
	alert('Ergebnis:' + output.value);
	</script>
	<form name="entry_set">
	  Namespace: <input type="text" name="namespace" id="namespace"><br>
	  Name: 	<input  type="text" name="name" id="name"><br>
	  Value: 	<input  type="text" name="value" id="value"><br>
	  <input type="button" onClick="x.set(document.getElementById('namespace').value, document.getElementById('name').value, document.getElementById('value').value); x.getAll()" value="Eintrag speichern">
	  
	</form>
	<br>
	--------------------------------<br>
	Bisherige Einträge:<br>
	--------------------------------<br>
	<div id="output">
	</div>
	</body>
	
</html>