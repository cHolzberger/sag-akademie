<?php
  $mpath = getRequiredAttribute("mpath", $attributes);
  $val = $dsl->get("dbtable", $mpath);

  if ( !empty ( $val )):
?>
<?=$value?>
<? endif; ?>