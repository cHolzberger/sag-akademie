<?php
$url = getRequiredAttribute("url", $attributes);
$timeout = getAttribute("timeout",$attributes, 3);

addSiteScript(sprintf ('$.redirect({url: "%s", timeout: "%s"});', $url, $timeout) );
?>
<div class="redirect">
Sie werden weitergeleitet in: <span id="timer"></span> Sekunden.<br/><br/>
Sollten Sie nicht weitergeleitet werden <a href="<?=$url?>">klicken Sie hier</a>

</div>