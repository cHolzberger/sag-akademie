<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
$now = time();

include_once ("services/SystemService.php");
include_once ("Events/KeepAliveEvent.php");

if (@session_name()) {

	session_start();
	if (@isset($_POST['token'])) {
		$_SESSION['token'] = $_POST['token'];
	}

	if (@isset($_GET['token'])) {

		$_SESSION['token'] = $_GET['token'];
	}

	@$oldToken = $_SESSION['token'];
	if (!isset($_SESSION['logon'])) {
		$_SESSION['logon'] = time();
		echo json_encode(array("SessionName" => session_name(), "SessionStatus" => "ok", "Logon" => $_SESSION['logon']));
	} else {
		echo json_encode(array("SessionName" => session_name(), "SessionStatus" => "ok", "Logon" => $_SESSION['logon'], "Id" => session_id()));
	}

} else if (session_start()) {
	if (@isset($_POST['token'])) {
		session_id($_POST['token']);
	}

	if (@isset($_GET['token'])) {
		session_id($_GET['token']);
	}

	echo json_encode(array("SessionStatus" => "new", "SessionName" => session_name()));
} else {
	echo json_encode(array("SessionStatus" => "timed out"));
}

$event = new KeepAliveEvent();
SystemService::dispatchEvent($event);
?>