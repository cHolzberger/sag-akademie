<?php

include_once ("mosaikTaglib/dlog.php");

class SAG_Form extends SAG_Component {
    
    var $entryTable = "seminarArt";
    var $entryClass = "SeminarArt";
    
    function construct() {
        //list ($config, $content) = $this->createPageReader();
    }
		
		function forward($class, $namespace = "") {
			if ( $this->next() == "kontakt") {
				$this->sendMail("Kontaktformular SAG-Akademie.de", "/kontakt/kontaktformular_danke");
			} else if ( $this->next() == "callback" ) {
				$this->sendMail("Callbackformular SAG-Akademie.de", "/kontakt/callbackformular_danke");
			}
		}
  
		
		function sendMail($title, $redirect) {
			/* EMAIL NOTIFICATION */
			$email = new MosaikEmail();
			$email->setContainer("__engine");
			$email->setPage("kontakt");
			$email->addData("text", $this->generateHtml());
			$email->send(SMTP_ADMIN_RECIVER, SMTP_ADMIN_SENDER, $title, SMTP_ADMIN_RECIVER);
			
			instantRedirect($redirect);
		}
    
    function generateHtml() {
        $Message = "";
        if (!is_array($_POST))
            return;
        //reset($_POST);
        while(list($key, $val) = each($_POST)) {
            $GLOBALS[$key] = $val;
            if (is_array($val)) {
                $Message .= "<tr><td><font face=verdana size=2><b>$key</b></td>";
                foreach ($val as $vala) {
                    $vala =stripslashes($vala);
                    $Message .= "</td><td><font face=verdana size=2>$vala</td></tr>";
                }
                $Message .= "";
            }
            else {
                $val = stripslashes($val);
                if (($key == "Submit") || ($key == "submit") || ($key == "Nachricht_senden_x") || ($key == "Nachricht_senden_y")) { }
                else {         if ($val == "") { $Message .= "<tr><td><font face=verdana size=2><b>$key</b></td></td><td><font face=verdana size=2>-</td></tr>"; }
                    else { $Message .= "<tr><td><font face=verdana size=2><b>$key</b></td><td><font face=verdana size=2>$val</td></tr>"; }
                }
            }
        } // end while
        $datum = getdate();
        $Message = '<font face=verdana size=2>Die folgenden Eingaben wurden &uuml;ber das Kontaktformular gesendet:<br/><br/><table border=1 cellspacing=0 cellpadding=5>'.$Message.'<tr><td><font face=verdana size=2><b>Nachricht gesendet</b></td><td><font face=verdana size=2>'.$datum[mday].'.'.$datum[mon].'.'.$datum[year].'</td></tr><td><font face=verdana size=2><b>Bearbeitet am</b></td><td>&nbsp;<br/>&nbsp;</td></tr><td><font face=verdana size=2><b>Bearbeitet von</b></td><td>&nbsp;<br/>&nbsp;</td></tr><td><font face=verdana size=2><b>Bemerkung</b></td><td>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/></td></tr></table></font>';
        return $Message;
    }
    
    function map($name) {
        return "SAG_Form";
    }
}