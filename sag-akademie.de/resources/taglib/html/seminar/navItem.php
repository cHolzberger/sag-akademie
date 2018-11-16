<?php
    $target = getRequiredAttribute("target",$attributes);
    $linkTarget  = str_replace ("ü", "_u", $target);
    $linkTarget  = str_replace ("ä", "_a", $linkTarget);
    $linkTarget  = str_replace ("ö", "_o", $linkTarget);
    $linkId  = str_replace (" ", "", $linkTarget);

    $linkTarget  = str_replace (" ", "%20", $linkTarget);

    
?>
<div id="menu_<?=$linkId?>" class="navItem"
     onMouseover="document.body.style.cursor = 'pointer'; $(this).addClass('navhover');"
     onMouseout="document.body.style.cursor = 'default'; $(this).removeClass('navhover');"
     onclick="window.location.href='/seminar/termin/<?=$linkTarget?>';" ><div></div><b><?=$target?></b></div>