<?php
	include ('../codebase/connector/scheduler_connector.php');

	$res=mysql_connect("localhost","root","");
	mysql_select_db("sampleDB");
	
	$scheduler = new schedulerConnector($res);
	$scheduler->enable_log("log.txt",true);
	$scheduler->render_table("events","event_id","start_date,end_date,event_name,details");
?>