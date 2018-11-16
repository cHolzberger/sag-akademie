<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>SAG-Akademie Verwaltung - Login</title>
		 <script type="text/javascript" src="gears_init.js">
    </script>
    <script type="text/javascript">
	if ( typeof ($) != "undefined" && $.mosaikRuntime) {
	    $.mosaikRuntime.requireLogin();
	}
    </script>
    </head>
   
    <body text="#000000" bgcolor="#FFFFFF" link="#FF0000" alink="#FF0000" vlink="#FF0000" background="bg.jpg" style="margin:0;">
   <form action="http://sag-akademie.mosaik-software.de/admin/?<?=file_get_contents ("../../version.txt");?>" method="POST" id="loginForm" name="loginForm">

        <div style="width:auto;height:auto;z-index:2;text-align:center;top:0px;left:0px;right:0px;bottom:0px;">
            <br>
            <br>
            <br>
            <div style="background-color:#ffffff;z-index:3;width:350px;font-family:Verdana,Arial;font-size:11px;font-weight:bold;text-align:right;padding-right:40px;margin:auto;">
                <div style="text-align:center;padding-left:20px;padding-top:10px;padding-bottom:20px;">
                    <img src="logo_ci.png" alt="" border="0" width="212" height="75">
                </div>
                    Benutzername:&nbsp;&nbsp; <input name="username" type="text" style="width:200px;" id="username"/>
                    <br/>
                    <br/>
                    Passwort:&nbsp;&nbsp; <input name="password" type="password" style="width:200px;"/>
                    <br/>
                    <br/>
                    Datenbank:&nbsp;&nbsp; 
                    <select name="umgebung" id="umgebung" style="width: 205px;" onChange="setTarget();">
                        <option value="http://sag-akademie.mosaik-software.de/admin/?<?=file_get_contents ("../../version.txt");?>">Produktiv</option>
                        <option value="http://beta.sag-akademie.de/admin/?<?=file_get_contents ("../../version.txt");?>">Test (Neuer Server)</option>
                        <option value="http://localhost:8085/admin/?<?=file_get_contents ("../../version.txt");?>">Lokal</option>
                    </select>
                    <br/>
                    <br/>
			

                    <input type="submit" value="Einloggen"/>
                    <br/>
                    &nbsp;

            </div>
        </div>
		<div style="position: fixed; bottom: 0px; left:0px; right: 0px; width: auto; height: 32px; text-align: right; vertical-align: middle;">
		Version: <?=file_get_contents ("../../version.txt"); ?>
			<!--<input type="button" name="gearsUpdate" value="Offline-Daten aktualisieren" onClick="checkForUpdate();"/>-->
		</div>
	</form>
    </body>
    <script type="text/javascript">
        /* create the local database */
        if (window.google && google.gears) {
        
        	var db = google.gears.factory.create('beta.database');
        	db.open('sag_akademie');
        	db.execute('create table if not exists J_Config (key TEXT, value TEXT, id INT PRIMARY KEY)');
        	
        	var rs = db.execute("SELECT * from J_Config WHERE key='login.username'");
        	
        	var hit = 0;
        	while (rs.isValidRow()) {
        		hit = 1;
        		if (rs.fieldByName("value")) {
        			document.getElementById("username").value = rs.fieldByName("value");
        		}
        		rs.next();
        	}
        	
        	if (hit == 0) {
        		db.execute("insert into J_Config (key,value) VALUES (?,?)", ["login.username", ""])
        	}
        	rs.close();
        	
        	document.getElementById("username").onblur = function() {
        		db.execute("update J_Config  SET value=? WHERE key = 'login.username'", [document.getElementById("username").value]);
        	};
        	
        	
        	/* create a managed store */
        	var STORE_NAME = "sagstore";
        	
        	try {
        		var localServer = google.gears.factory.create('beta.localserver');

        		var store = localServer.openManagedStore(STORE_NAME);
				
        		store = mosaikCheckForUpdate();
        		
        		store.enabled = true;
        	} catch (ex) {
        		alert("Google - Gears Fehler: \n" + ex.message);
        	}
        	
        }
		
		function mosaikCheckForUpdate() {
			console.log("Updating...")
			store = localServer.createManagedStore(STORE_NAME);
        	store.manifestUrl = '/_gearsmanifest.php';
        	store.checkForUpdate();	
			return store;	
		}
        
        function setTarget() {
        	document.getElementById("loginForm").action = document.getElementById("umgebung").value;
        };
    </script>
</html>
