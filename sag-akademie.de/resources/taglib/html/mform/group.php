<?php
$label = getRequiredAttribute("label",$attributes);

?>
<h2 class="mform-group-heading"><?=$label?></h2>
<div class="mform-group-content" <?=getOptional('id', $attributes)?>>
    <?=$value?>
</div>
