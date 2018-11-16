<div class="preloader2" style="display:none;" id='preloader2'>
<?
function cssMenu($name, $prefix) {
         $ext = "";
        $fn = "img/$prefix"."_"."$name";
        if (file_exists ( $fn . ".jpg" )) {
                $ext = "jpg";
        } else {
                $ext = "png";
        } ?>

 <? if ("nav" == $prefix) { ?>
<img src="/img/<?=$prefix?>_<?=$name?>.<?=$ext?>" >
<? } ?>
       <? } ?>
<?  include ("img/images.php"); ?>

</div>