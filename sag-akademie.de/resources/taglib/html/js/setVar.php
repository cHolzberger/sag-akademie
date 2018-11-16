<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

$mpath = (getRequiredAttribute ("mpath", $attributes));
$inputvalue = $dsl->get("dbtable", $mpath);
if (empty($inputvalue)) $inputvalue="#UNDEFINED#";

$varName = (getRequiredAttribute ("name", $attributes));

?>
<script type="text/javascript">
	$.mosaikRuntime.replace("#<?=$varName?>#", "<?=$inputvalue?>" );
</script>