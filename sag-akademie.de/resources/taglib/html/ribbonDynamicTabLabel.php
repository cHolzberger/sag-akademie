<?php
$forId = getAttribute("forId",$attributes,"Undefined Id");

$script = <<<END
$("#ribbon").tabs("add", '{$forId}', '$value');
END;

addSiteScript($script);
?>