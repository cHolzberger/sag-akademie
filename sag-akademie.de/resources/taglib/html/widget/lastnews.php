<?php
$result = Doctrine::getTable('neuigkeit')->getLastNews()->fetchArray();
?>
<table border="0" cellspacing="0" cellpadding="0" width="230" style="width:230px;"><tr><td style="padding:4px;padding-left:8px;background:url('/img/newheadlinebg.jpg');border-style:solid;border-width:1px;border-color:darkgray;"><span class="newheadline"><b>Aktuelles
		</b>
	    </span>
	</td>

    </tr>
    <tr><td bgcolor="#f8f8f8" style="padding:8px;border-style:solid;border-width:1px;border-color:darkgray;border-top-style:none;">
<?php
if(count($result) >= 1) {
foreach($result as $news) {
    ?>
<b><?=$news['titel']?></b>
	    <br />
	    <br />
	    <?=$news['text']?>
	    <?php
	    if($news['pdf'] != "")
	    {
	    ?>
		<br />
		<a href="<?=$news['pdf']?>" target="_blank"><img src="/img/weiter_lesen.jpg" border="0" alt="weiter lesen" align="right" />
		</a>
	    <?php
	    }
	    ?>
	    <br />

    <?php
}
}else{
?>
    <b>Keine Neuigkeit vorhanden.</b>
<?php
}
?>
	</td>
    </tr>
</table>