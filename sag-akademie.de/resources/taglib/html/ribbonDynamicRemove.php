<?php
$script = <<<END
	removeDynamicRibbons();
	removeDynamicRibbonsRight();
END;
addSiteScript($script);
?>