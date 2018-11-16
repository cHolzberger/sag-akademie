<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Export-".date("m.d.Y").".xls");
header("Cache-Control: no-cache, must-revalidate");

$style = 'font-familiy: verdana; font-size: 10px;';
$tdstyle = ""; // style der pro td angewendet wird - wird benutzt um mso-number-format bei bedarf anzugeben.

echo "<html><head></head><body ><table border='1'$style><tr $style>";
$headers = json_decode($_POST['headers'], true);
$data = json_decode($_POST['data'], true);
//print_r($data);
foreach ( $headers as $head) {
    printf ("<td style='%s'><b>%s</b></td>", $style, utf8_decode($head['label']) );
}
echo "</tr>";

foreach ( $data as $da) {
    echo "<tr style='$style'>";

    foreach ($headers as $head ) {
	if ( !array_key_exists($head['field'], $da)) $da[$head['field']] = "";
 
	if ( $da[ $head['field'] ] == "0000-00-00") $da[$head['field']] = "";
	else if ( $da[ $head['field'] ] == "0000-00-00 00:00:00") $da[$head['field']] = "";
	else if ( $head['field'] == "nr") $tdstyle = $style . " mso-number-format: \@;";
	else $tdstyle = $style;
	
	printf("<td style='%s'>%s </td>", $tdstyle, htmlentities( utf8_decode( $da[$head['field']] )));
    }
    
    echo "</tr>";
}
echo "</table></body></html>";
?>