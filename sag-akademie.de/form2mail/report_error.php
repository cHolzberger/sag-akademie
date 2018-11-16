<?php
$email="info@sag-akademie.de";
$MailToAddress = "ch@mosaik-software.de"; // your email address
$redirectURL = "/kontakt/callbackformular_danke"; // the URL of the thank you page.

# optional settings
$MailSubject = "[Fehler im SAG-ADM-UI]"; // the subject of the message you will receive
$MailToCC = ""; // CC (carbon copy) also send the email to this address (leave empty if you don't use it)
# in the $MailToCC field you can have more then one e-mail address like "d@web4future.com, b@web4future.com, c@web4future.com"

# If you are asking for an email address in your form, you can name that input field "email".
# If you do this, the message will apear to come from that email address and you can simply click the reply button to answer it.
# You can use this scirpt to submit your forms or to receive orders by email.
# You need to send the form as POST!

# If you have a multiple selection box or multiple checkboxes, you MUST name the multiple list box or checkbox as "name[]" instead of just "name"
# you must also add "multiple" at the end of the tag like this: <select name="myselect[]" multiple>
# and the same way with checkboxes

# This script was made by George A. & Calin S. from Web4Future.com
# There are no copyrights in the e-mails sent and we do not ask for anything in return.

# DO NOT EDIT BELOW THIS LINE ============================================================
# ver. 1.2
$Message = "";
    if (!is_array($_POST))
    return;
//reset($_POST);
        while(list($key, $val) = each($_GET)) {
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
                                        else { $Message .= "<tr><td><font face=verdana size=2><b>$key</b></td><td><font face=verdana size=2>".nl2br($val)."</td></tr>"; }
                        }
                }
        } // end while
$Message = '<html><head><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"></head><body><font face=verdana size=2>Die folgenden Eingaben wurden &uuml;ber das Callbackformular gesendet:<br/><br/><table border=1 cellspacing=0 cellpadding=5>'.$Message.'</table></font></body></html>';
mail( $MailToAddress, $MailSubject, $Message, "Content-Type: text/html; charset=UTF-8\r\nFrom: ".$email."\r\nBCc: ".$MailToCC);

header("Location: ".$redirectURL);
?>
