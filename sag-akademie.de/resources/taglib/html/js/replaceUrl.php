<?php

$mpath = (getRequiredAttribute ("mpath", $attributes));
$inputvalue = urlencode($dsl->get("dbtable", $mpath));
if (! empty($inputvalue)) {

$base = (getRequiredAttribute ("base", $attributes));
$suffix = (getAttribute ("suffix", $attributes));

?>


<!-- url angaben bei redirects berichtigen -->
<!-- base: <?=$base ?> id: <?=$inputvalue?> -->
<script type="text/javascript">
    $.mosaikHistory.replaceCurrent ( '<?=$base?><?=$inputvalue ?><?=$suffix?>' );
 </script>
<? } ?>