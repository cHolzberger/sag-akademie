<?
include("lib/config.php");
$targettag="beta";
$currentversion = file_get_contents("version.txt");

$br="<br/>";
if (isset($_GET['console'])) {
	$br="\n";
}

switch ($host) {
	case "beta.sag-akademie.de":
	case "bremse.mosaiksoftware.local":
		$targettag="beta";
	break;
	
	case "www.sag-akademie.de":
	case "sag-akademie.de":
		$targettag="stable";
	break;
	
	default:
		die("Unknown Host: $host" );
	break;
}
echo "Host: $host $br";
echo "Tag: $targettag $br";
echo "(aktuelle) Version: $currentVersion";
echo "<b>Pulling update:</b>$br$br";
echo "<pre>";
system("hg -v -y pull 2>&1");
echo "</pre>";
echo "<b>Applying update:</b> $br$br";
echo "<pre>";
system("hg update -r $targettag 2>&1");
echo "</pre>";
$newVersion = file_get_contents("version.txt");

echo "<p>Update von <strong>$currentVersion</strong> auf <strong>$newVersion</strong> erfolgt.</p>";



