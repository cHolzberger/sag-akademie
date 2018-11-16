<div class="preloader" style="display:none;" id='preloader'>
<?

function cssMenu($name, $prefix) {
         $ext = "";
        $fn = "img/$prefix"."_"."$name";
        if (file_exists ( $fn . ".jpg" )) {
                $ext = "jpg";
        } else {
                $ext = "png";
        }
?>
<img src="/img/<?=$prefix?>_a_<?=$name?>.<?=$ext?>" >
<? if ("nav" != $prefix) { ?>
<img src="/img/<?=$prefix?>_<?=$name?>.<?=$ext?>">
<? } ?>

<? } ?>

<?  include ("img/images.php"); ?>
</div>