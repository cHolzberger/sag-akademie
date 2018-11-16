<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

?>
<textarea style="<?=getAttribute("style", $attributes)?>" class="<?=getAttribute("class", $attributes)?>">
<?= file_get_contents( getRequiredAttribute("file",$attributes) ); ?>
</textarea>