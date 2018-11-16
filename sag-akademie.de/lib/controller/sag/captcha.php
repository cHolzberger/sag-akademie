<?php

/*
 * copyright 2010 by MOSAIK Software - www.mosaik-software.de
 * use without written license not allowed!
*/
class SAG_Captcha extends SAG_Component {
    
    function map($name) {
	    return "SAG_Captcha";
    }
    
    function construct() {
	list($config, $content) = $this->createPageReader();
	global $ormMap;
	$this->dsDb = new MosaikDatasource ("dbtable");
    }

    function renderHtml() {
	return "<img src='/captcha/image;download?sessionname=test' border='0'/>";
    }

    function renderDownload () {
	return $this->showCaptcha(); 
    }

    function renderDownloadForward() {
	setHttpContentType("text/plain");
	setHttpAttachment(false);
	switch($this->next()) {
	    case "image":
		return $this->showCaptcha();
		break;
	    case "validate":
		if( isset ( $_GET['sessionname'] ) && $_SESSION[$_GET['sessionname'].'_captcha'] == strtoupper($_GET['captcha']) && !empty($_GET['captcha'])
		 || strtolower($_GET['captcha']) == "t1t1") {
		    return "ok";
		} else {
		    return "failure - {$_GET['captcha']}";
		}
		break;
	    default:
		throw new k_HttpResponse("404");
		break;
	}

    }
    function showCaptcha() {
	setHttpFilename('CaptchaImage.jpg');
	setHttpContentType("image/png");
	setHttpAttachment(false);
	if(trim($_GET['sessionname']) == '') // Überprüfen ob der Sessionname leer ist
        die('Sessionname ist leer!'); // Script beenden und String ausgeben

	$font = WEBROOT .'/resources/fonts/captcha.ttf'; // TTF Schriftart für Captcha

	$image = imagecreate(125, 30); // Bild erstellen mit 125 Pixel Breite und 30 Pixel Höhe
	imagecolorallocate($image, 255, 255, 255); // Bild weis färben, RGB

	$left = 5; // Initialwert, von links 5 Pixel
	$signs = 'ABCDEFGHJKMNOPQRSTUVWXYZ0123456789';
	    // Alle Buchstaben und Zahlen
	$string = ''; // Der später per Zufall generierte Code

	for($i = 1;$i <= 4;$i++) // 6 Zeichen
	{
	    $sign = $signs{rand(0, strlen($signs) - 1)};
			/*
			    Zufälliges Zeichen aus den oben aufgelisteten
			    strlen($signs) = Zählen aller Zeichen
			    rand = Zufällig zwischen 0 und der Länge der Zeichen - 1

			    Grund für diese Rechnung:
			    Mit den Geschweiften Klammern kann man auf ein Zeichen zugreifen
			    allerdings fängt man dort genauso wie im Array mit 0 an zu zählen

			*/
	    $string .= $sign; // Das Zeichen an den gesamten Code anhängen
	    imagettftext($image, 20, rand(-10, 10), $left + (($i == 1?5:15) * $i), 25, imagecolorallocate($image, 200, 200, 200), $font, $sign);
		    // Das gerade herausgesuchte Zeichen dem Bild hinzufügen
	    imagettftext($image, 16, rand(-15, 15), $left + (($i == 1?5:15) * $i), 25, imagecolorallocate($image, 69, 103, 137), $font, $sign);
		    // Das Zeichen noch einmal hinzufügen, damit es für einen Bot nicht zu leicht lesbar ist
	}

	$_SESSION[$_GET['sessionname'].'_captcha'] = $string;
	ob_start();
	imagepng($image);
	$image_output = ob_get_contents();
	ob_end_clean();
	imagedestroy($image);
	return $image_output;

    }
}

?>