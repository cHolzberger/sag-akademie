<?
$info_link = $dsl->get("dbtable", 'info_link');
$info_titel = $dsl->get("dbtable", 'info_titel');
$info_link2 = $dsl->get("dbtable", 'info_link2');
$info_titel2 = $dsl->get("dbtable", 'info_titel2');
$info_link3 = $dsl->get("dbtable", 'info_link3');
$info_titel3 = $dsl->get("dbtable", 'info_titel3');

$basehref=getAttribute("basehref", $attributes);

$output = "";
if(isset($info_link) && $info_link != "")
{
    $uuid = createUuid();
    $uuid2 = $uuid ."2";
    addSiteScript( sprintf ( '$("#%s").click(function () { window.open("%s%s") });', $uuid, $basehref, $info_link));
    addSiteScript( sprintf ( '$("#%s").click(function () { window.open("%s%s") });', $uuid2, $basehref, $info_link));
    if(isset($info_titel) && $info_titel != "")
    {
        $titel = $info_titel;
    }else{
        $titel = "Informationen<br/>zu diesem Seminar";
    }
    $output .= '<tr>
	<td>&nbsp;<a id="'.$uuid.'" name="info_link" target="_blank" class="cursorHand"><img src="/img/pdfklein.png" border="0" alt="Seminar Infos downloaden" />&nbsp;&nbsp;</a></td>
	<td><a id="'.$uuid2.'" name="info_link" target="_blank" class="cursorHand">'.$titel.'</a></td>
    </tr>';
}
if(isset($info_link2) && $info_link2 != "")
{
    $uuid = createUuid();
    $uuid2 =  $uuid . "2";
    addSiteScript( sprintf ( '$("#%s").click(function () { window.open("%s%s") });', $uuid, $basehref, $info_link2));
    addSiteScript( sprintf ( '$("#%s").click(function () { window.open("%s%s") });', $uuid2, $basehref, $info_link2));
    if(isset($info_titel2) && $info_titel2 != "")
    {
        $titel = $info_titel2;
    }else{
        $titel = "Weitere Informationen<br/>zu diesem Seminar";
    }
    $output .= '<tr>
	<td>&nbsp;<a id="'.$uuid.'" name="info_link" target="_blank" class="cursorHand"><img src="/img/pdfklein.png" border="0" alt="Seminar Infos downloaden" />&nbsp;&nbsp;</a></td>
	<td><a id="'.$uuid2.'" name="info_link" target="_blank" class="cursorHand">'.$titel.'</a></td>
    </tr>';
}
if(isset($info_link3) && $info_link3 != "")
{
    $uuid = createUuid();
    $uuid2 =  $uuid . "2";
    addSiteScript( sprintf ( '$("#%s").click(function () { window.open("%s%s") });', $uuid, $basehref, $info_link3));
    addSiteScript( sprintf ( '$("#%s").click(function () { window.open("%s%s") });', $uuid2, $basehref, $info_link3));
    if(isset($info_titel) && $info_titel3 != "")
    {
        $titel = $info_titel3;
    }else{
        $titel = "Weitere Informationen<br/>zu diesem Seminar";
    }
    $output .= '<tr>
	<td>&nbsp;<a id="'.$uuid.'" name="info_link" target="_blank" class="cursorHand"><img src="/img/pdfklein.png" border="0" alt="Seminar Infos downloaden" /></a>&nbsp;&nbsp;</td>
	<td><a id="'.$uuid2.'" name="info_link" target="_blank" class="cursorHand">'.$titel.'</a></td>
    </tr>';
}


?>
<table border="0" cellspacing="0" cellpadding="0">
    <?=$output?>
</table>
